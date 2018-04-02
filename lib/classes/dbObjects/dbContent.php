<?php


/*******************************************************************************
The contents of this file are subject to the Mozilla Public License
Version 1.1 (the "License"); you may not use this file except in
compliance with the License. You may obtain a copy of the License at
http://www.mozilla.org/MPL/

Software distributed under the License is distributed on an "AS IS"
basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
License for the specific language governing rights and limitations
under the License.

The Original Code is (C) 2004-2010 Blest AS.

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
Rune Nilssen
*******************************************************************************/

include_once ( 'lib/functions/functions.php' );
class dbContent extends dbObject
{
	var $parentElement;
	var $subElements;
	var $_tableName = 'ContentElement';
	var $_fieldsLoaded = false;
	var $_language = 1;
	var $_editmode = 0;
	var $_contentGroups = array ( );
	var $_scripts = array ( );
	var $_stylesheets = array ( );
	
	/**
	 * Initialize this content, load table and set up language
	**/
	function __construct ( $id = false )
	{
		global $Session;
		$this->loadTable ();
		
		// Load language
		$this->_language = isset($Session) && isset($Session->CurrentLanguage) ? $Session->CurrentLanguage : 1;
		$this->checkLanguageId ( $this->_language );
		
		// When one wants to preview a page, this setting is used
		if ( 
			( 
				array_key_exists ( 'editmode', $_REQUEST ) && 
				$_REQUEST[ 'editmode' ] && 
				isset ( $Session->arenaCurrentModule ) 
			)
			|| ( defined('ARENAMODE') && ARENAMODE == 'admin' )
		) 
		{
			$this->_editmode = 1;
		}
		else
		{
			if( defined( 'LANGUAGES_ONE_PAGE_STRUCTURE' ) )
			{
				$this->checkLanguageId ( 'default' );
			}
		}
		if ( $id ) $this->load ( $id );
	}
	
	// Check languageid!
	function checkLanguageId ( $id )
	{
		$language = new dbObject( 'Languages' );
		if ( $id != 'default' )
		{
			$language->Load( $id );
		}
		if( !isset( $language->ID ) || !$language->ID )
		{
			$language = new dbObject( 'Languages' );
			$language->IsDefault = 1;
			if( !( $language = $language->findSingle() ) )
				ArenaDie ( 'Language error.' );
			$this->_language = $language->ID;
		}
	}
	
	function load ( $id = false )
	{
		parent::load ( $id );
		if ( isset ( $this->MainID ) && $this->MainID != $this->ID && $this->ID > 0 )
			$this->_editmode = 1;
		
		if( isset ( $this->ContentGroups ) )
		{
			$groups = explode ( ',', $this->ContentGroups );
			if ( is_array ( $groups ) )
			{
				foreach ( $groups as $g )
				{
					$this->_contentGroups[] = trim ( $g );
				}
			}
		}
		if ( $this->ID ) 
		{
			if ( $this->Language != $this->_language )
				$this->_language = $this->Language;
			return true;
		}
		return false;
	}
	
	/**
	 * Load elements that has this element as parent
	**/
	function loadSubElements ( $options = false )
	{
		if ( !$this->ID ) return false;
		
		if ( $options[ 'editmode' ] ) $this->_editmode = 1;
		
		// Load subelements
		$objs = new dbContent ( );
		$objs->addClause ( 'WHERE', "Parent='{$this->MainID}'" );
		$objs->addClause ( 'WHERE', "Language='{$this->_language}'" );
		$objs->addClause ( 'WHERE', '!IsDeleted' );
		$objs->addClause ( 'WHERE', '!IsTemplate' );
		if ( $options[ 'OnlyPublished' ] ) $objs->addClause ( 'WHERE', 'IsPublished' );
		if ( $this->_editmode )
			$objs->addClause ( 'WHERE', 'MainID != ID' );
		else
			$objs->addClause ( 'WHERE', 'MainID = ID' );
		$objs->addClause ( 'WHERE', 'VersionPublished = Version' );
		$objs->addClause ( 'ORDER BY', 'IsSystem ASC, SortOrder ASC, ID ASC' );
		if ( $this->subElements = $objs->find ( ) )
		{
			// Respect permissions
			$o = array ( );
			foreach ( $this->subElements as $sub )
			{	
				// If the mode is web and we are protected
				if ( ARENAMODE == 'web' && $sub->IsProtected )
				{
					if ( $GLOBALS[ 'webuser' ] && $GLOBALS[ 'webuser' ]->checkPermission ( $sub, 'read' ) )
						$o[] = $sub;
				}
				else $o[] = $sub;
			}
			$this->subElements = $o;
			$this->subElementsLoaded = true;
			return true;
		}
		return false;
	}
	
	/**
	 * Get a full url to this content
	**/
	function getUrl ( )
	{
		return BASE_URL . $this->getPath ( );
	}
	
	/**
	 * Only get the route string to this content
	**/
	function getRoute ( )
	{
		return str_replace ( '/index.html', '', $this->getPath ( ) );
	}
	
	/**
	 * Get the path to this content in the content structure
	**/
	function getPath ( )
	{
		global $Session;
		
		if ( !$this->ID ) return '';
		
		$p = new dbObject ( 'ContentElement' );
		$p->load ( $this->ID );
		
		if ( $p->ContentType == 'link' )
			return $p->Link;
		
		$root = '/';
		if ( $p->Parent != 0 )
		{
			$oStr = $this->RouteName ? $this->RouteName : textToUrl ( trim ( strip_tags ( $this->MenuTitle ) ) );
		}
		else 
		{
			$root = '';
			$oStr = '';
		}
		
		if ( $pth = $this->checkCache ( 'getPath', $this->ID ) )	return $pth;
		if ( $p->load ( $p->Parent ) && $p->Parent > 0 )
		do { $oStr = $p->RouteName. '/' . $oStr; }	
		while ( $p->load ( $p->Parent ) && $p->Parent > 0 );
		
		$lang = new dbObject ( 'Languages' );
		if ( $lang->load ( $this->_language ) )
			$code = $lang->Name . '/';
		else  $code = '';

		if ( !isset( $Session->Language->UrlActivator ) || strlen ( $Session->Language->UrlActivator ) <= 0 )
			$o = $code . $oStr . "{$root}index.html";
		else $o = $oStr . "{$root}index.html";
		$this->Cache ( 'getPath', $this->ID, $o );
		return $o;
	}
	
	/**
	 * Get root content
	**/
	function getRootContent ( $options = false )
	{
		if ( $options[ 'editmode' ] == 1 || $this->_editmode )
		{
			$p = new dbContent ( );
			$p->addClause ( 'WHERE', 'Parent=\'0\' AND !IsDeleted AND !IsTemplate AND ID!=MainID AND Version=VersionPublished AND Language=\'' . $this->_language . '\'' );
			
			if ( $o = $p->findSingle ( ) )
			{
				$o->_editmode = 1;
				return $o;
			}
			// Anamoly, but try again without versions
			else
			{
				$p = new dbContent ( );
				$p->addClause ( 'WHERE', 'Parent=\'0\' AND !IsDeleted AND !IsTemplate AND ID!=MainID AND Language=\'' . $this->_language . '\'' );
				if ( $p = $p->findSingle ( ) )
				{
					$p->_editmode = 1;
					return $p;
				}
				// Create root page if we can't find none
				else
				{
					$p = new dbContent ( );
					$p->Title = 'Root';
					$p->MenuTitle = 'Root';
					$p->Parent = '0';
					$p->Version = 0;
					$p->IsTemplate = 0;
					$p->IsDeleted = 0;
					$p->Language = $this->_language;
					$p->VersionPublished = 0;
					$p->Version = 0;
					$p->SystemName = 'root';
					$p->save ( );
					$p->MainID = $p->ID;
					$p->save ( );
					$p->_editmode = 1;
					return $p;
				}
			}
		}
		else
		{
			$p = new dbContent ( );
			$p->addClause ( 'WHERE', 'Parent=\'0\' AND !IsDeleted AND !IsTemplate AND ID=MainID AND Version=VersionPublished AND Language=\'' . $this->_language . '\'' );
				
			if ( $o = $p->findSingle ( ) )
			{
				return $o;
			}
			// Create root page if we can't find none
			else
			{
				$p->Title = 'Root';
				$p->MenuTitle = 'Root';
				$p->Parent = '0';
				$p->Version = 0;
				$p->IsTemplate = 0;
				$p->IsDeleted = 0;
				$p->Language = $this->_language;
				$p->VersionPublished = 0;
				$p->Version = 0;
				$p->SystemName = 'root';
				$p->save ( );
				$p->MainID = $p->ID;
				$p->save ( );
				return $p;
			}
		}
	}
	 
	/**
	 * Get content by path string
	**/
	function getByPath ( $route )
	{
		global $Session;
		
		if ( isset( $_REQUEST[ 'editmode' ] ) && $_REQUEST[ 'editmode' ] ) $this->_editmode = 1;
					
		// Remove the language code from the route if we have an url activator
		if ( $Session->HasUrlActivator || ( substr ( $route, 0, 3 ) == $Session->LanguageCode . '/' ) )
		{
			$s = $Session->LanguageCode . '/';
			$slen = strlen ( $s );
			if ( substr ( $route, 0, $slen ) == $s )
				$route = substr ( $route, $slen, strlen ( $route ) - $slen );
		}

		// Remove language part of path to page.... 
		if ( !$Session->HasUrlActivator && $p = strpos ( $route, '/' ) )
		{
			if ( $p < strlen ( $route ) - 1 ) 
				$route = substr ( $route, 0, strlen ( $route ) - 1 );
		}
		
		$route = str_replace ( 'index.html', '', str_replace ( '/index.html', '', $route ) );
		
		$route = 'root/' . $route;

		$db =& $this->globalValue ( 'database' );
		
		$path = $route;

		if ( !is_array ( $_SESSION[ "routes{$this->_language}" ] ) )
			$_SESSION[ "routes{$this->_language}" ] = Array ( );

		$page = new dbContent ( );
		// Don't try to fetch previews (in editmode) from session cache
		if ( !$this->_editmode )
		{
			if ( $key = array_search ( $path, $_SESSION[ "routes{$this->_language}" ] ) )	
			{
				if ( $page->load ( $key ) )
				{
					// Check if the session is blown! That the db is more up to date
					// than the cache
					$testpath = explode ( '/', $path );
					if ( strstr ( $testpath[ count ( $testpath ) - 1 ], 'index.htm' ) ) 
					{ array_pop ( $testpath ); }
					if ( $testpath[ count ( $testpath ) - 1 ] != $page->RouteName )
					{
						// Clean this busted cache!
						$key = false;
						$_SESSION[ "routes{$this->_language}" ] = Array ( );
					}
					// Check if page is deleted
					else if ( !$page->IsDeleted ) return $page;
					else $key = false;
				} else $key = false;
			} 
			else $key = false;
		}
		
		if ( !$key )
		{
			$paths = explode ( '/', $path );
			$parent = '0';

			while ( count ( $paths ) )
			{
				$path = $paths[ 0 ];
				if ( !$path ) 
					break;
				if ( substr ( trim ( $path ), 0, 9 ) != 'index.htm' ) 
				{
					// Use MainID != ID for unpublished version
					$query = "
						SELECT 
							MenuTitle, Title, ID, MainID, Parent 
						FROM 
							ContentElement 
						WHERE 
							Parent='$parent' AND " . ( $this->_editmode ? 'MainID != ID' : 'MainID = ID' ) . " AND ( RouteName=\"$path\" OR SystemName LIKE \"$path\" ) AND !IsDeleted AND !IsTemplate AND Language='{$this->_language}'
						LIMIT 1
					";
					
					if ( $tmp = $db->fetchObjectRow ( $query, MYSQLI_ASSOC ) )
					{
						$page = $tmp;
						$parent = $page->MainID;
					}
					else
					{
						$query = "
							SELECT 
								MenuTitle, Title, ID, MainID, Parent 
							FROM 
								ContentElement 
							WHERE 
								Parent='$parent' AND " . ( $this->_editmode ? 'MainID != ID' : 'MainID = ID' ) . " AND IsFallback AND !IsDeleted AND !IsTemplate AND Language='{$this->_language}' 
							ORDER BY 
								ID DESC 
							LIMIT 1 
						";
						
						if ( $tmp = $db->fetchObjectRow ( $query, MYSQLI_ASSOC ) )
						{
							$page = $tmp;
							$page->MenuTitle = ucfirst( $path );
							$page->Title = ucfirst( $path );
							$parent = $page->MainID;
						}
					}
				}
				$paths = array_reverse ( $paths );
				array_pop ( $paths );
				$paths = array_reverse ( $paths );
			}
			
			if ( $page->ID )
			{
				// Don't cache previews...
				if ( !$this->_editmode )
					$_SESSION[ "routes{$this->_language}" ][ $page->ID ] = $route;
				$p = new dbContent ( );
				if( $p->load ( $page->ID ) )
				{
					$p->MenuTitle = $page->MenuTitle;
					$p->Title = $page->Title;
					return $p;
				}
			}
			return false;
		}
	}
	
	/**
	 * Get a field by name
	**/
	function getField ( $name, $options = false )
	{
		if ( $name == 'Body' ) return $this->Body;
		else if ( $name == 'Intro' ) return $this->Intro;
		else if ( $name == 'Title' ) return $this->Title;
		else if ( $name == 'MenuTitle' ) return $this->MenuTitle;
		
		for ( $a = 0; $a < 2; $a++ )
		{
			if ( $this->{$name} )
			{	
				if ( !$options )
					return $this->{$name};
				else 
					return $this->renderExtraField ( $this->{'_field_' . $name}, $options );
			}
			$this->loadExtraFields ( );
		}
	}
	
	/**
	 * Get editing copy (ID!=MainID)
	**/
	function getEditCopy ( )
	{
		$cobj = new dbContent ( );
		if ( $cobj = $cobj->findSingle ( '
			SELECT * FROM ContentElement WHERE MainID=\'' . $this->ID . '\' AND ID != MainID LIMIT 1
		' ) )
			return $cobj;
		return false;
	}
	
	/**
	 * Add a new extrafield
	**/
	function addExtraField ( $name, $type, $group, $content )
	{
		$o = new stdclass ();
		$o->Name = $name;
		$o->ContentGroup = $group;
		$o->Type = $type;
		
		$c = new stdclass ();
		$c->Name = $name;
		$c->ContentGroup = $group;
		$c->Type = $type;
		
		$this->$name = $content;
		$this->{"_field_$name"} = $o;
		$this->{"_extra_$name"} = $c;
		
		return true;
	}
	
	/**
	 * Get the extra field of the parent page and upwards
	**/
	function interpretParentExtraField ( $fieldObject, $options )
	{
		if ( $this->Parent == 0 ) return false;
		$obj = new dbContent ( );
		if ( $obj->load ( $this->Parent ) )
		{
			if ( $this->_editmode )
			{
				if ( $i = $obj->getEditCopy ( ) )
					$obj = $i;
				else return false;
			}
			
			if ( $fieldObject->DataTable == 'Small' )
				$fobj = new dbObject ( 'ContentDataSmall' );
			else 
				$fobj = new dbObject ( 'ContentDataBig' );
			
			$fobj->Type = $fieldObject->Type;
			$fobj->Name = $fieldObject->Name;
			$fobj->ContentTable = 'ContentElement';
			$fobj->ContentID = $obj->ID;
			
			if ( $fobj->load ( ) )
			{
				$fobj->DataTable = $fieldObject->DataTable;
				return $obj->interpretExtraField ( $fobj, $options );
			}
			else
			{
				$fobj->DataTable = $fieldObject->DataTable;
				return $obj->interpretParentExtraField ( $fieldObject, $options );
			}
		}
		return false;
	}
	
	/**
	 * Render parent extra field contents
	**/
	function renderParentExtraField ( $fieldObject, $options )
	{
		if ( $this->Parent == 0 ) 
			return false;
		
		$obj = new dbContent ( );
		if ( $obj->load ( $this->Parent ) )
		{
			if ( $this->_editmode )
			{
				if ( $i = $obj->getEditCopy ( ) )
					$obj = $i;
				else return false;
			}
				
			if ( $fieldObject->DataTable == 'Small' )
				$fobj = new dbObject ( 'ContentDataSmall' );
			else $fobj = new dbObject ( 'ContentDataBig' );
			
			$fobj->Type = $fieldObject->Type;
			$fobj->Name = $fieldObject->Name;
			$fobj->ContentTable = 'ContentElement';
			$fobj->ContentID = $obj->ID;
			if ( $fobj->load ( ) )
			{
				$fobj->DataTable = $fieldObject->DataTable;
				return $obj->renderExtraField ( $fobj, $options );
			}
			else
			{
				$fobj->DataTable = $fieldObject->DataTable;
				return $obj->renderParentExtraField ( $fieldObject, $options );
			}
		}
		return false;
	}
	
	/**
	 * Get an interpreted extra field 
	**/
	function interpretExtraField ( $fieldObject, $options = false )
	{
		switch ( $fieldObject->Type )
		{
			case 'formprocessor':
				break;
				
			case 'pagelisting':
				$obj = new dbContent ( );
				if ( $fieldObject->DataInt > 0 )
					$obj->load ( $fieldObject->DataInt );
				else if ( $fieldObject->DataInt < 0 )
				{	
					$obj->addClause ( 'WHERE', "ID='{$this->ID}'" );
					$obj = $obj->findSingle ( );
				}
				else 
				{
					if ( $result = $this->interpretParentExtraField ( $fieldObject, $options ) )
						return $result;
					$obj->addClause ( 'WHERE', "ID='{$this->ID}'" );
					$obj = $obj->findSingle ( );
				}
				if ( $obj ) 
				{
					$obj->loadSubElements ( $options );
				}
				if ( ( $c = count ( $obj->subElements ) ) )
				{
					if ( $fieldObject->DataDouble > 0 && $fieldObject->DataDouble < $c )
					{
						$subs = Array ( );
						$i = 0;
						$c = $fieldObject->DataDouble;
						
						foreach ( $obj->subElements as $sub )
						{
							if ( $i < $c )
							{
								$subs[] = $sub;
								$i++;
							}
							else break;
						}
						return $subs;
					}
					return $obj->subElements;
				}
				return false;
			
			case 'contentmodule':
				
				if ( !$this->_editmode || ARENAMODE == 'web' )
				{
					return $fieldObject->DataString;
				}
				return '';
			
			case 'extension':
				
				if ( !$this->_editmode || ARENAMODE == 'web' )
				{
					if ( !$fieldObject->DataString )
					{
						return $this->interpretParentExtraField ( $fieldObject, $options );
					}
					$target = 'extensions/' . $fieldObject->DataString . '/websnippet.php';
					if ( file_exists ( $target ) )
					{
						$content =& $this;
						include ( $target );
						return ( $extension );
					}
				}
				return '';
				
			case 'file':
				include_once ( 'lib/classes/dbObjects/dbFile.php' );
				$obj = new dbFile ( );
				if ( $obj->load ( $fieldObject->DataInt ) )
					return $obj;
				return $this->interpretParentExtraField ( $fieldObject, $options );
				
			case 'image':
				include_once ( 'lib/classes/dbObjects/dbImage.php' );
				$obj = new dbImage ( );
				if ( $obj->load ( $fieldObject->DataInt ) )
					return $obj;
				return $this->interpretParentExtraField ( $fieldObject, $options );
			
			case 'newscategory':	
				$objs = new dbObject ( 'News' );
				if ( $fieldObject->DataInt > 0 )
					$objs->addClause ( 'WHERE', "CategoryID='{$fieldObject->DataInt}'" );
				list ( $aff, $rev, ) = explode ( '|', $fieldObject->DataMixed );
				if ( $rev ) $objs->addClause ( 'ORDER BY', 'DateActual ASC, ID ASC' );
				else $objs->addClause ( 'ORDER BY', 'DateActual DESC, ID DESC' );
				if ( $objs = $objs->find ( ) )
					return $objs;
				return false;
			
			case 'objectconnection':
				return "<element name=\"{$fieldObject->Name}\"></element>";
			case 'script':
				if ( trim ( $fieldObject->DataString ) )
					return $fieldObject->DataString;
				return $fieldObject->DataMixed;
				break;
			case 'whitespace': return '<span></span>';
			case 'varchar':
			case 'script':
			case 'style':
				if ( trim ( $fieldObject->DataString ) )
					return $fieldObject->DataString;
				else return $this->interpretParentExtraField ( $fieldObject, $options );

			default:
				if ( $fieldObject->DataText )						
					return $this->ProcessText ( $fieldObject->DataText );
				else return $this->interpretParentExtraField ( $fieldObject, $options );
		}
		return '';
	}
	
	/**
	 * Get the HTML of the extra field, as displayed on the web
	**/
	function renderExtraField ( $fieldObject, $options = false )
	{
		global $Session;
		
		switch ( $fieldObject->Type )
		{
			case 'file':
				include_once ( 'lib/classes/dbObjects/include/objectconnection_content.php' );
				include_once ( 'lib/classes/dbObjects/dbFile.php' );
				$file = new dbFile ( );
				if ( $file->load ( $fieldObject->DataInt ) )
				{
					return "\n\n<div id=\"{$fieldObject->Name}\">" . rcFile ( $file ) . "</div>\n\n";	
				}
				break;
				
			case 'formprocessor':
				include_once ( 'lib/classes/dbObjects/include/dbContent_helpers.php' );
				list ( $receivers, $subject, $prefix, $sent, $mailtitle, $mailmessage, $responsefield ) = explode ( "\t", $fieldObject->DataText );
				$foundcount = 0;
				foreach ( $_POST as $k=>$v )
				{
					if ( substr ( $k, 0, strlen ( $prefix ) ) == $prefix )
						$foundcount++;
				}
				if ( $foundcount )
				{
					// Autoreply:
					if ( trim ( $mailmessage ) && trim ( $mailtitle ) )
					{
						$mail = '';
						if ( $_REQUEST[ $prefix . 'email' ] ) $mail = $_REQUEST[ $prefix . 'email' ];
						else if ( $_REQUEST[ $prefix . 'Email' ] ) $mail = $_REQUEST[ $prefix . 'Email' ];
						else if ( $_REQUEST[ $prefix . 'E-Mail' ] ) $mail = $_REQUEST[ $prefix . 'E-Mail' ];
						else if ( $_REQUEST[ $prefix . 'epost' ] ) $mail = $_REQUEST[ $prefix . 'epost' ];
						else if ( $_REQUEST[ $prefix . 'E-Post' ] ) $mail = $_REQUEST[ $prefix . 'E-Post' ];
						
						if ( $mail )
						{
							mail_ ( $mail, $mailtitle, $mailmessage, 'Content-type: text/html; charset=UTF-8' . getLn ( ) . 'From: noreply@offshoreliving.no', true );
						}
					}
					
					// Send to receivers:
					_mailFormTo ( $receivers, $subject, $prefix, $this );
				}
				else if ( $_REQUEST[ 'formsent' . $prefix ] )
				{
					if ( !trim ( $responsefield ) )
					{
						$this->Body = $sent;
					}
					else $this->$responsefield = '<div id="' .  $responsefield . '">' . $sent . '</div>';
				}
				break;
			case 'pagelisting':
				/**
				 * Cache the parent pages for later use
				**/
				if ( !$GLOBALS[ 'pagelisting_parents' ] ) $GLOBALS[ 'pagelisting_parents' ] = Array ( );
				if ( !$GLOBALS[ 'pagelisting_parents' ][ $_REQUEST[ 'route' ] ] )
				{
					$GLOBALS[ 'pagelisting_parents' ][ $_REQUEST[ 'route' ] ] = Array ( );
					$page = $this->getByPath ( $_REQUEST[ 'route' ] );
					$lim = 20;
					while ( $page->Parent != 0 && $lim > 0 )
					{
						$GLOBALS[ 'pagelisting_parents' ][ $_REQUEST[ 'route' ] ][] = $page->Parent;
						$page->load ( $page->Parent );
						$lim--;
					}
				}
				$obj = new dbContent ( );
				if ( $fieldObject->DataInt > 0 )
				{
					$obj->load ( $fieldObject->DataInt );
				}
				else
				{
					// Try to get parent
					if ( $fieldObject->DataInt == 0 )
					{
						if ( !$options[ 'pageid' ] )
							$options[ 'pageid' ] = $this->ID;
						if ( !$GLOBALS[ 'pagelisting_currentid' ] ) 
							$GLOBALS[ 'pagelisting_currentid' ] = $this->ID;
						if ( $result = $this->renderParentExtraField ( $fieldObject, $options ) )
						{
							unset ( $GLOBALS[ 'pagelisting_currentid' ] );
							return $result;
						}
						unset ( $GLOBALS[ 'pagelisting_currentid' ] );
					}
					// or same page
					if ( $options[ 'pageid' ] )
					{
						$id = $options[ 'pageid' ];
					}
					else 
					{
						$id = $this->ID;
					}
					$obj = $obj->findSingle ( 'SELECT * FROM ContentElement WHERE ID=' . $id );
				}

				if ( !$obj )
				{
					return '';
				}

				// Check some options
				$usingParentHeading = false;
				$usingRecursion = false;
				if ( $fieldObject->DataMixed )
				{
					$data = explode ( "\n", $fieldObject->DataMixed );
					
					foreach ( $data as $row )
					{
						list ( $key, $value ) = explode ( "\t", $row );
						switch ( $key )
						{
							case 'usingParentHeading':
								$usingParentHeading = $value;
								break;
							case 'usingRecursion':
								$usingRecursion = $value;
								break;
						}
					}
				}
				
				// Some inherited options (that *can* be passed on down)
				if ( $options[ 'usingRecursion' ] )
					$usingRecursion = $options[ 'usingRecursion' ];
				
				$obj->loadSubElements ( );
				
				if ( ( $c = count ( $obj->subElements ) ) )
				{
					if ( $fieldObject->DataDouble > 0 && $fieldObject->DataDouble < $c )
					{
						$subs = Array ( );
						$i = 0;
						$c = $fieldObject->DataDouble;
						foreach ( $obj->subElements as $sub )
						{
							if ( $i < $c )
							{
								$subs[] = $sub;
								$i++;
							}
							else break;
						}
					}
					else $subs = $obj->subElements;

					if ( $usingParentHeading )
					{
						$oStr .= '<h2 class="PagelistingHeading">' . $obj->MenuTitle . '</h2>';
					}

				
					if ( $fieldObject->DataString == 'intro' )
					{
						$oStr .= "\n" . '<div class="PagelistingDiv">';
					}
					$oStr .= "\n" . '<ul>'; // <- fallback is title listing
						
					if ( is_array ( $subs ) )
					{
						foreach ( $subs as $ele )
						{
							if ( !$ele->IsPublished || $ele->IsSystem )
							{
								continue;
							}
							if ( 
								( $GLOBALS[ 'pagelisting_currentid' ] && $ele->ID == $GLOBALS[ 'pagelisting_currentid' ] ) ||
								( !$GLOBALS[ 'pagelisting_currentid' ] && $ele->ID == $this->ID )
							)
							{
								$c = ' class="current"';
							}
							else $c = '';
						
							$extra = '';
							if ( $ele->ID == $this->ID || in_array ( $ele->ID, $GLOBALS[ 'pagelisting_parents' ][ $_REQUEST[ 'route' ] ] ) )
							{
								$extra = new Dummy ( );
								$extra->Type = 'pagelisting';
								$extra->DataInt = $ele->ID;
								$options[ 'usingRecursion' ] = $usingRecursion;
								$extra = $this->renderExtraField ( $extra, $options );
							}
						
							// Intro
							if ( $fieldObject->DataString == 'intro' )
							{
								$oStr .= '<li' . $c . '>' . $ele->Intro . $extra . '</li>';
							}
							else if ( $fieldObject->DataString == 'body' )
							{
								$oStr .= '<li' . $c . '>' . $ele->Body . $extra . '</li>';
							}
							// Titles and intro
							else if ( $fieldObject->DataString == 'titlesandintro' )
							{
								$oStr .= "\n<li$c><span class=\"Title\"><a href=\"" . $ele->getPath ( ) . "\">{$ele->MenuTitle}</a></span><span class=\"Intro\"><a href=\"" . $ele->getPath ( ) . "\">{$ele->Intro}</a></span><span class=\"Clear\"></span></li>$extra";
							}
							else if ( $fieldObject->DataString == 'titlesandbody' )
							{
								$oStr .= "\n<li$c><span class=\"Title\"><a href=\"" . $ele->getPath ( ) . "\">{$ele->MenuTitle}</a></span><span class=\"Intro\"><a href=\"" . $ele->getPath ( ) . "\">{$ele->Body}</a></span><span class=\"Clear\"></span></li>$extra";
							}
							// Everything
							else if ( $fieldObject->DataString == 'everything' )
							{
								$oStr .= '<li class="' . texttourl ( $ele->MenuTitle ) . '">';
								$oStr .= '<div class="Url">' . $ele->getUrl ( ) . '</div>';
								if ( $ele->Title )
									$oStr .= '<div class="Title">' . $ele->Title . '</div>';
								if ( $ele->MenuTitle )
									$oStr .= '<div class="MenuTitle">' . $ele->MenuTitle . '</div>';
								if ( $ele->Intro )
									$oStr .= '<div class="Intro">' . $ele->Intro . '</div>';
								if ( $ele->Body )
									$oStr .= '<div class="Body">' . $ele->Intro . '</div>';
								$ele->loadExtraFields ( );
								$ele->renderExtraFields ( );
								foreach ( $ele as $k=>$v )
								{
									$f = '_extra_' . $k;
									if ( $ele->$f )
										$oStr .= '<div class="' . $k . '">' . $ele->$f . '</div>';
								}
								$oStr .= '</li>';
							}
							// Titles only
							else
							{
								// If we are using recursion, then show them here
								$ul = '';
								if ( $usingRecursion && $c )
								{
									$ele->loadSubElements ( );
									if ( $ele->subElements )
									{
										$ul .= '<ul>';
										foreach ( $ele->subElements as $s )
										{
											$ul .= "\n<li><a href=\"" . $s->getPath ( ) . "\">{$s->MenuTitle}</a></li>";
										}
										$ul .= '</ul>';
									}
								}
								// With or without recursion, show the title now
								$oStr .= "\n<li$c><a href=\"" . $ele->getPath ( ) . "\">{$ele->MenuTitle}</a>$ul</li>$extra";
							}
						}
					}

					$oStr .= "\n" . '</ul>'; // <- fallback is title listing

					if ( $fieldObject->DataString == 'intro' )
					{
						$oStr .= "\n" . '</div>';
					}						
					return "\n\n<div id=\"{$fieldObject->Name}\">$oStr</div>\n\n";
				}
				
				return '';
				
			case 'image':
			
				// Try to get parent
				if ( !$fieldObject->DataInt )
				{
					if ( $result = $this->renderParentExtraField ( $fieldObject, $options = false ) )
						return $result;
				}
				include_once ( 'lib/classes/dbObjects/dbImage.php' );
				if ( $obj = new dbImage ( $fieldObject->DataInt ) )
				{
					list ( $w, $h, $class, $scalemode ) = explode ( "\t", $fieldObject->DataMixed );
					if ( $w && $h )
					{
						$img = $obj->getImageUrl ( $w, $h, $scalemode ? $scalemode : 'framed' );
					}
					else
					{
						$w = $obj->getMasterWidth ( );
						$h = $obj->getMasterHeight ( );
						$img = $obj->getMasterImage ( );
					}
					if ( $fieldObject->DataString )
					{
						if ( $fieldObject->DataString == '~' )
						{
							$fieldObject->DataString = BASE_URL;
							list ( $lcnt, ) = $obj->_table->database->fetchRow ( 'SELECT COUNT(*) FROM Languages' );
							if ( $lcnt > 1 ) $fieldObject->DataString .= $Session->LanguageCode . '/';
						}
						$op = Array ( '', ' target="_blank"' );
						$exs = '<a href="' . $fieldObject->DataString . '"' . $op[ floor ( $fieldObject->DataDouble ) ] . '>';
						$exe = '</a>';
					} else $exs = $exe = '';
					return "\n\n<div id=\"{$fieldObject->Name}\"" . ( $class ? ( ' class="' . $class . '"' ) : '' ) . ">{$exs}<img alt=\"{$fieldObject->Name}\" src=\"" . $img . "\" style=\"width: " . $w . "px; height: " . $h . "px\"/>$exe</div>";
				}
				return '';
			
			case 'contentmodule':
				if ( !$this->_editmode || ARENAMODE == 'web' )
				{
					$target = 'lib/skeleton/modules/' . $fieldObject->DataString . '/webmodule.php';
					if ( file_exists ( $target ) )
					{
						$content =& $this;
						$field =& $fieldObject;
						$module = '';
						include ( $target );
						return '<div id="' . $fieldObject->Name . '">' . $module . '</div>';
					}
				}
				return '';
				
			case 'extension':
			
				if ( !$this->_editmode || ARENAMODE == 'web' )
				{
					if ( !$fieldObject->DataString )
						return $this->renderParentExtraField ( $fieldObject, $options );
					$target = 'extensions/' . $fieldObject->DataString . '/websnippet.php';
					if ( file_exists ( $target ) )
					{
						$content =& $this;
						include ( $target );
						$class = '';
						if( isset( $fieldObject->Class ) )
							$class = ' class="' . $fieldObject->Class . '"';
						return '<div id="' . $fieldObject->Name . '"' . $class . '>' . $extension . '</div>';
					}
				}
				return '';
				
			case 'newscategory':
				include ( 'lib/classes/dbObjects/include/render_newscategory.php' );
				return false;
			
			case 'objectconnection':
				$ob =& $this;
				if ( $fieldObject->ContentID != $this->ID )
				{
					$o = new dbObject ( 'ContentElement' );
					if ( $o->load ( $fieldObject->ContentID ) )
						$ob =& $o;
				}
				if ( $objects = $ob->getObjects ( ) )
				{
					include_once ( 'lib/classes/dbObjects/include/objectconnection_content.php' );
					$str = "<div id=\"{$fieldObject->Name}\">";
					foreach ( $objects as $object )
					{
						$str .= renderObject ( $object );
					}
					$str .= "</div>";
					return $str;
				}
				break;
			
			case 'whitespace':
				return "<div id=\"{$fieldObject->Name}\" class=\"Whitespace\"></div>";	
			
			case 'varchar':
				return "<div id=\"{$fieldObject->Name}\">{$fieldObject->DataString}</div>";
			
			case 'script':
				// New method - just throw the script onto the page
				if ( trim ( $fieldObject->DataMixed ) )
					return $fieldObject->DataMixed;
				// Old obsolete one - deprecated
				else if ( trim ( $fieldObject->DataString ) )
					$this->_scripts[ $fieldObject->ContentGroup ][] = BASE_URL . $fieldObject->DataString;
				else return $this->renderParentExtraField ( $fieldObject, $options = false );
				return '';
				
			case 'style':
				if ( $fieldObject->DataString )
					$this->_stylesheets[ $fieldObject->ContentGroup ][] = BASE_URL . $fieldObject->DataString;
				else return $this->renderParentExtraField ( $fieldObject, $options = false );
				return '';
			
			default:
				if ( $fieldObject->DataText )
					return "<div id=\"{$fieldObject->Name}\">" . $this->ProcessText ( $fieldObject->DataText ) . "</div>";
				else return $this->renderParentExtraField ( $fieldObject, $options = false );
				return '';
		}
		return '';
	}
	
	/**
	 * Render all HTML from extrafields
	 * arranged in content groups
	**/
	function renderExtraFields ( $options = false )
	{
		global $document;
		$this->loadExtraFields ( $options );
		$groups = Array ( );
		$emptygroups = $this->_contentGroups;
		
		// Topmenu contentgroup
		if ( defined ( 'TOPMENU_CONTENTGROUP' ) )
		{
			if ( ( in_array ( TOPMENU_CONTENTGROUP, $this->_contentGroups ) ) )
			{
				$groups[ TOPMENU_CONTENTGROUP ] .= '<div id="TopMenu__">' . $GLOBALS[ 'document' ]->renderNavigation ( defined( 'NAVIGATION_ROOTPARENT' ) ? NAVIGATION_ROOTPARENT :  0, defined( 'NAVIGATION_LEVELS' ) ? NAVIGATION_LEVELS : 1 , defined( 'NAVIGATION_MODE' ) ? NAVIGATION_MODE : 'FOLLOW', true ) . '</div>';
			}
		}
		
		foreach ( $this as $k=>$v )
		{
			// Locked fields are ignored
			if ( key_exists ( "_locked_$k", $this ) && $this->{'_locked_' . $k} ) continue;
			// Place top menu
			if ( key_exists ( "_extra_$k", $this ) && $this->{'_extra_' . $k} && $this->{'_field_' . $k}->IsVisible )
			{
				$fkey = $this->{ '_field_' . $k }->ContentGroup;
				
				// Mindful of replacement fields
				if ( key_exists ( "_replacement_$k", $this ) && $this->{ '_replacement_' . $k } )
				{
					$v = '<div id="' . $k . '">' . getLn ( ) . $this->{ '_replacement_' . $k } . getLn ( ) . '</div>' . getLn ( );
				}
				$groups[ $fkey ] .= $v;		
			}
		}
		
		$ostr = '';
		$tableStart = false;
		$tableEnd = false;
		$tdNum = 0;
		$tlSet = isset ( $GLOBALS[ 'TableLayout' ] );
		foreach ( $this->_contentGroups as $g )
		{
			if ( $tlSet )
			{
				$tl = $GLOBALS[ 'TableLayout' ];
				if ( !$tableStart && array_key_exists ( $g, $tl ) )
				{
					$tableStart = $g; 					
					$tableEnd = $tl[$g];
					$tdNum = 1;
				}
			}
			// Empty groups with no members ---------------------------------------
			
			// Groups with field data ---------------------------------------------

		
			if ( $tableStart == $g )
			{
				$ostr .= getLn ();
				$ostr .= "\t\t\t\t\t\t\t" . '<table class="Table_' . $g . '"><tr><td class="Col' . $tdNum++ . '">';
			}
			else if ( $tableStart != false )
			{
				$ostr .= "\t\t\t\t\t\t\t" . '</td><td class="Space' . ($tdNum-1) . '"></td><td class="Col' . $tdNum++ . '">';
			}
			if ( !array_key_exists ( $g, $groups ) )
			{
				$ostr .= getLn ( );
				$ostr .= "\t\t\t\t\t\t\t" . '<div id="' . $g . '">' . getLn ( );
				$ostr .= "\t\t\t\t\t\t\t\t" . getLn ( );
				$ostr .= "\t\t\t\t\t\t\t" . '</div>' . getLn ( );
			}
			else
			{
				$ostr .= getLn ( );
				$ostr .= "\t\t\t\t\t\t\t" . '<div id="' . $g . '">' . getLn ( );
				$ostr .= "\t\t\t\t\t\t\t\t" . $groups[ $g ] . getLn ( );
				$ostr .= "\t\t\t\t\t\t\t" . '</div>' . getLn ( );
			}
			if ( $tableEnd == $g )
			{
				$ostr .= "\t\t\t\t\t\t\t" . '</td></tr></table>';
				$ostr .= getLn ();
				$tableEnd = $tableStart = false;
			}
		}
		// Load in resources (css/js)
		foreach ( $this->_contentGroups as $g )
		{
			if ( $this->_scripts[ $g ] )
				foreach ( $this->_scripts[ $g ] as $s )
					$document->addHeadScript ( $s );
			if ( $this->_stylesheets[ $g ] )
				foreach ( $this->_stylesheets[ $g ] as $s )
					$document->addResource ( 'stylesheet', $s );
		}
		return $ostr;
	}
	
	/**
	 * Process text for output
	**/
	function ProcessText ( $string )
	{
		global $database;
		$string = stripslashes ( $string );
		$string = preg_replace ( "/(<!--arenaform[^>]*-->)/i", "<form\$1>", $string );
		$string = preg_replace ( "/(<!--\/arenaform[^>]*-->)/i", "</form>", $string );
		$string = str_replace ( '<!--arenaform', '', $string );
		$string = str_replace ( '-->>', '>', $string );
		if ( strstr ( strtolower ( stripslashes ( $string ) ), 'class="arenafieldobject"' ) )
        {
            if ( preg_match_all ( '/\<span\ class\=\"ArenaFieldObject\"\ id\=\"([^"]*?)\"\>[^<]*?\<\/span\>/i', $string, $matches ) )
            {
            	foreach ( $matches[1] as $k=>$match )
            	{
				    list ( , $cid, $name ) = explode ( '__', $match );
			    	if ( $row = $database->fetchObjectRow ( '
			    		SELECT * FROM 
			    		(
			    			(
			    				SELECT ID, "Small" as `Type` 
			    				FROM ContentDataSmall WHERE `Name`="' . $name . '" AND ContentID=\'' . $cid . '\' AND ContentTable=\'ContentElement\'
			    			)
			    			UNION
			    			(
			    				SELECT ID, "Big" as `Type` 
			    				FROM ContentDataBig WHERE `Name`="' . $name . '" AND ContentID=\'' . $cid . '\' AND ContentTable=\'ContentElement\'
			    			)
			    		) z
			    	' ) )
			    	{
			    		$r = new dbObject ( 'ContentData' . $row->Type );
			    		$r->load ( $row->ID );	
				        $string = str_replace ( $matches[0][$k], $this->renderExtraField ( $r ), $string );
				    }
				}
			}
        }
		return $string;
	}
	
	/**
	 * Save this content back to the database
	**/
	function save ( )
	{
		/** Get a routename **/
		$this->RouteName = textToUrl ( trim ( strip_tags ( $this->MenuTitle ) ) );
		
		/** Make sure we have a language **/
		if ( !$this->Language && $this->Parent > 0 )
		{
			$obj = new dbContent ( );
			$obj->load ( $this->ID );
			while ( $obj->Parent > 0 )
			{
				$obj->load ( $obj->Parent );
			}
			$this->Language = $obj->Language;
		}
		
		/** 
		 * Save 
		**/
		return parent::save ( );	
	}
	
	// Renders a select list with the content tree
	function RenderSelect ( $name, $id = '', $current = 0, $parent = 0, $r = '', $rgbc = Array ( 0, 0, 0 ), $rgbb = Array ( 230, 230, 230 ), $langid = false )
	{
		global $Session;
		if ( $parent <= 0 ) $parent = 0;
		$lang = $langid ? $langid : $Session->CurrentLanguage;
		
		if ( !$rgbc ) $rgbc = Array ( 0, 0, 0 );
		if ( !$rgbb ) $rgbb = Array ( 230, 230, 230 );
		foreach ( $rgbc as $k=>$v ) if ( $v < 0 ) $rgbc[$k] = 0; else if ( $v > 255 ) $rgbc[$k] = 255;
		foreach ( $rgbb as $k=>$v ) if ( $v < 0 ) $rgbb[$k] = 0; else if ( $v > 255 ) $rgbb[$k] = 255;
		
		$db =& parent::getDatabase ( );
		if ( $rows = $db->fetchObjectRows ( '
			SELECT * FROM ContentElement WHERE 
			Parent=\'' . (string)$parent . '\' AND 
			!IsTemplate AND !IsDeleted AND ID=MainID' . ( $lang > 0 ? ( ' AND `Language`=\'' . $lang . '\'' ) : ( $lang == '*' ? ' AND `Language` >= 0' : ' AND Language=\'0\'' ) ) . '
			ORDER BY ' . ( $lang == '*' ? ' `Language` ASC, ' : '' ) . 'SortOrder ASC, `Title` ASC' ) )
		{
			$ostr = "\n";
			if ( $r == '' )
			{
				$ostr .= '<select';
				$ostr .= ' name="' . $name . '"';
				if ( $id ) $ostr .= ' id="' . $id . '"';
				$ostr .= '>' . "\n";
				$ostr .= "\t" . '<option value="0" style="background: #fff; color: #000">Velg side:</option>' . "\n";
			}
			foreach ( $rows as $row )
			{
				if ( $parent <= 0 && $lang == '*' )
				{
					$l = new dbObject ( 'Languages' );
					$l->load ( $row->Language );
					$ostr .= "\t" . '<option value="0">' . ($l->NativeName?($l->NativeName.' ('.$l->Name.')'):'Unnamed') . '</option>';
				}
				if ( $row->ID == $current ) $s = ' selected="selected"';
				else $s = '';
				$ostr .= "\t" . '<option value="' . $row->ID . '"' . $s;
				$ostr .= ' style="background: rgb(' . $rgbb[ 0 ] . ',' . $rgbb[ 1 ] . ',' . $rgbb[ 2 ] . ');';
				$ostr .= ' color: rgb(' . $rgbc[ 0 ] . ',' . $rgbc[ 1 ] . ',' . $rgbc[ 2 ] . ')">' . $r . '&nbsp;&nbsp;&nbsp;&nbsp;';
				$ostr .= $row->MenuTitle ? $row->MenuTitle : ( $row->Title ? $row->Title : ( 'Unknown #' . $row->ID ) ) . '</option>' . "\n";
				$ostr .= dbContent::RenderSelect ( 
					$name, $id, $current, $row->ID, $r . '&nbsp;&nbsp;&nbsp;&nbsp;', 
					Array ( $rgbc[ 0 ] + 20, $rgbc[ 1 ] + 20, $rgbc[ 2 ] + 20 ),
					Array ( $rgbb[ 0 ] - 20, $rgbb[ 1 ] - 20, $rgbb[ 2 ] - 20 ), 
					$langid
				);
			}
			if ( $r == '' )
			{
				$ostr .= "\n" . '</select>' . "\n";
			}
			return $ostr;
		}
		return '';
	}
}	
?>
