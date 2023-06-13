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


 
/**                                                                            
 * PHP Templates
 *
 *  @author Inge Jorgensen <inge@blest.no>
 *  @author Hogne Titlestad <hogne@blest.no>
 *  @package arena-lib
 *  @copyright Copyright (c) 2005-2008 Blest AS                                     
 *                                                                             
**/

require_once( 'lib/functions/functions.php' );

class cDocument extends cPTemplate
{
	var $sHeadData = array ( );
	var $_dups = array ( );
	var $sBottomData = array ( );
	var $_topcontent = array ();
	var $_rendered = false;
	var $_encoding;
	var $autoCSS = true;
	
	function __construct ( $opts = false, $encoding = 'utf-8' )
	{
		parent::__construct ( $opts );
		$GLOBALS[ 'document' ] = &$this;
		if ( !isset ( $GLOBALS[ 'TopMenuExtension' ] ) )
			$GLOBALS[ 'TopMenuExtension' ] = Array ( );
		$this->_encoding = $encoding;
	}
	
	function renderNavigation ( $parent = 0, $levels = 0, $mode = 'FOLLOW', $firstcall = false, $depth = 0 )
	{
		global $TopMenuExtension;
		
		// TODO: Figure out what this is for..
		if( $levels !== 0 && $levels >= 0 )
		{
			if ( $depth >= ($levels+1) || ( $parent == 0 && $depth >= $levels ) )
			{
				if ( $parent == 0 ) return '';
				return array ( 0, '' );
			}
		}
		
		if ( $parent === 0 || $parent > 0 )
		{
			$page = new dbContent ( );
			$page->addClause ( 'WHERE', "Parent='$parent'" );
			$page->addClause ( 'WHERE', "Language='{$page->_language}'" );
			$page->addClause ( 'WHERE', 'ID=MainID' );
			$page->addClause ( 'WHERE', '!IsDeleted' );
			$page->addClause ( 'WHERE', 'IsPublished' );
			$page->addClause ( 'WHERE', '!IsSystem' );
			$page->addClause ( 'ORDER BY', "SortOrder ASC, ID ASC" );
			$pages = $page->find ( );
		}
		
		$oStr = "";
		$rootshown = false;
		
		// Support extending the menu for extensions and modules
		if ( array_key_exists ( $parent, $TopMenuExtension ) )
		{
			if ( !$pages ) $pages = Array ( );
			foreach ( $TopMenuExtension[ $parent ] as $subpage )
			{
				$pages[] = $subpage;
			}
		}
		if ( $pages )
		{
			if( ( $parent == 0 || $firstcall == true ) && !defined ( 'MENUROOTDRAWN' ) )
			{
				$oStr .= "<ul id=\"menuroot\" class=\"{$parent}\">";
				define ( 'MENUROOTDRAWN', 1 );
			}
			else $oStr .= "<ul>";
			
			$openReturn = false;
			$depth++; // Inc depth before loop
			$linkc = 1; // Link number here	
			
			foreach ( $pages as $page )
			{
				$openInLoop = false;
				$counter = ' link_' . $depth . '_' . $linkc;
				switch ( $page->ContentType )
				{
					case "link":
						$linkObject = CreateObjectFromString ( $page->LinkData );
						$link = $page->Link;
						$t = " target=\"{$linkObject->LinkTarget}\"";
						break;
					default:
						$link = $page->getPath ( );
						$t = "";
						break;
				}
				if ( $page->ID == $this->page->ID || ( $_REQUEST[ 'route' ] && $_REQUEST[ 'route' ] == $page->Route ) )
				{
					$class = " class=\"current\"";
					$openReturn = true;
				}
				else 
				{
					$class = "";
				}
				if ( $mode == 'ALL' && $levels > 0 )
				{
					list ( $openInLoop, $s ) = $this->renderNavigation ( $page->ID, $levels - 1, $mode, false, $depth );
					if ( $openInLoop ) 
					{
						$ex = ' menuopen'; 
						$openReturn = true;
					}
					else $ex = '';
					if ( $class )
						$liClass = ' current';
					else $liClass = '';
					$oStr .= "<li class=\"li_{$page->RouteName}{$ex}{$liClass}{$counter}\"><a href=\"" . $link . "\"$class$t><span>" . $page->MenuTitle . "</span></a>";
					if ( trim ( $s ) ) $oStr .= $s;
				}
				else if ( $mode == 'FOLLOW' && $this->isUnderPage ( $page ) )
				{
					list ( $openInLoop, $s ) = $this->renderNavigation ( $page->ID, $levels - 1, $mode, false, $depth );
					if ( $openInLoop ) 
					{
						$ex = ' menuopen'; 
						$openReturn = true;
					}
					else $ex = '';
					if ( $class )
						$liClass = ' current';
					else $liClass = '';
					$oStr .= "<li class=\"li_{$page->RouteName}{$ex}{$liClass}{$counter}\"><a href=\"" . $link . "\"$class$t><span>" . $page->MenuTitle . "</span></a>";
					if ( trim ( $s ) ) $oStr .= $s;
				}
				$oStr .= "</li>";
				$linkc++;
			}
			$oStr .= "</ul>";
			$depth--; // Dec depth after loop
			
			if ( $depth == 0 )
				return $oStr;
			return array ( $openReturn, $oStr );
		}
		else return false;
	}
	
	function renderBreadCrumbs()
	{
		$db =& dbObject::globalValue ( 'database' );
		
		$pid = $this->page->MainID ? $this->page->MainID : $this->page->ID;
		
		$lang = new dbObject ( 'Languages' );
		if ( $lang->load ( $this->page->Language ) )
			$code = $lang->Name . '/';
		else 
			$code = '';
		
		$route = Array ( );
		$a = 0;
		
		while ( $row = $db->fetchObjectRow ( 'SELECT * FROM ContentElement WHERE MainID = ID AND !IsDeleted AND IsPublished AND ID=' . $pid ) )
		{
			$routes[] = $row->RouteName;
			$pid = $row->Parent;
			if ( $row->ID == $this->page->MainID )
				$ret[] = '<a class="Current" href="<!route!>">' . ( $row->MenuTitle ? $row->MenuTitle : $row->Title ) . '</a>';
			else $ret[] = '<a href="<!route!>">' . ( $row->MenuTitle ? $row->MenuTitle : $row->Title ) . '</a>';
			$a++;
		}
		
		$routes = array_reverse ( $routes );
		$s = '';
		$rl = count ( $routes );
		for ( $a = 0; $a < $rl; $a++ )
		{
			$routes[ $a ] = $s . $routes[ $a ];
			if ( $a > 0 )
				$s = $routes[ $a ] . '/';	
		}
		
		$ret = array_reverse ( $ret );
		$rl = count ( $ret );
		for ( $a = 0; $a < $rl; $a++ )
		{
			if ( $a == 0 )
				$ret[ $a ] = str_replace ( '<!route!>', BASE_URL . $code . 'index.html', $ret[ $a ] );
			else
				$ret[ $a ] = str_replace ( '<!route!>', BASE_URL . $code . $routes[ $a ] . '/', $ret[ $a ] );
		}
		
		return implode ( ' &raquo; ', $ret );
	}
	
	function isUnderPage ( $page )
	{
		global $Session;

		// Is same page
		if ( $page->Parent == 0 )
			return true;
			
		if ( $this->page->MainID == $page->MainID )
		{
			return true;
		}
		// First child
		else if ( $this->page->MainID == $page->Parent )
		{
			return true;
		}
		// Same parent
		else if ( $this->page->Parent == $page->Parent )
		{
			return true;
		}
		return false;
	}
	
	
	/**
	 * Field specific things 
	**/
	
	function getField ( $name, $options = false )
	{
		if ( $this->page )
			return $this->page->getField ( $name, $options );
		else return false;
	}
	
	function interpretExtraField ( $fieldObject )
	{
		if ( $this->page )
			return $this->page->interpretExtraField ( $fieldObject );
		return false;
	}
	
	function renderExtraField ( $fieldObject )
	{
		if ( $this->page ) 
			return $this->page->renderExtraField ( $fieldObject );
		return false;
	}
	
	function renderExtraFields ( $options = false )
	{
		if ( $this->page )
			return $this->page->renderExtraFields ( $options );
		return false;
	}
	
	function loadExtraFields ( $options = false )
	{
		if ( $this->page )
			return $this->page->renderExtraFields ( $options );
		return false;
	}
	
	/**
	 * Universal built-in search function
	**/
	function generateSearchString ( $string, $fields )
	{
		$oStr = Array ( );
		$string = explode ( " ", str_replace ( "  ", " ", str_replace ( ",", " ", $string ) ) );
		foreach ( $string as $str )
		{
			foreach ( $fields as $k )
			{
				$oStr[] = "( {$k} LIKE \"%{$str}%\" )";
			}
		}
		$oStr = implode ( " OR ", $oStr );
		return $oStr;
	}
	
	function searchContent ( $content, $limit = false )
	{
		global $Session;
		// TODO: Implement pagination
		/**
		 * Generate query
		**/
		if ( !is_array ( $content ) )
			$content = Array ( $content );
		$query = "";
		$a = 0;
		$letters = "abcdefghijklmnopqrstuvwxyz";
		
		foreach ( $content as $cont )
		{
			$b = $letters[$a];
			switch ( $cont )
			{
				case "News":
					if ( $Session->CurrentLanguage ) 
						$language = " c.Language='{$Session->CurrentLanguage}' AND";
					else $language = "";
					$query[] = "
					(
						SELECT n.ID, \"News\" AS `Table` FROM News n, NewsCategory c WHERE c.ID = n.CategoryID AND$language
						(
							" . $this->generateSearchString ( $_REQUEST[ "searchKeywords" ], Array ( "Title", "Intro", "Article" ) ) . "
						)
					)\n";
					break;
				case "ContentElement":
					if ( $Session->CurrentLanguage )
						$language = " AND Language='{$Session->CurrentLanguage}'";
					else $language = "";
					$query[] = "
					(
						SELECT ID, \"ContentElement\" AS `Table` FROM ContentElement WHERE !IsDeleted AND !IsTemplate AND IsPublished AND !IsSystem AND MainID = ID$language AND
						(
							" . $this->generateSearchString ( $_REQUEST[ "searchKeywords" ], Array ( "Title", "Intro", "Body" ) ) . "
						)
					)\n";
					break;
				default: break;
			}
			$a++;
		}
		$query = implode ( " UNION ", $query );
		$db =& dbObject::globalValue ( "database" );
		
		if ( $rows = $db->fetchObjectRows ( "SELECT * FROM ( " . $query . " ) AS tquery", MYSQLI_ASSOC ) )
		{
			$tpl = new cPTemplate ( $this->findTemplate( "search_result.php", array ( "templates/", "web/templates/" ) ) );
			
			$oStr = "";
			$a = 1;
			foreach ( $rows as $row )
			{
				$path = $tpl->path = "";
				$obj = dbObject::get ( $row->ID, $row->Table );
				if ( method_exists ( $obj, "getPath" ) ) $tpl->path = $obj->getPath ( );
				else 
				{
					switch ( $row->Table )
					{
						case "News":
							$cat = new dbObject ( "NewsCategory" );
							$cat->load ( $obj->CategoryID );
							if ( $cat->ContentElementID )
							{
								if ( $content = dbObject::get ( $cat->ContentElementID, "ContentElement" ) )
								{
									$path = $content->getPath ( ) . "?nid={$obj->ID}";
									$tpl->path = $path;
								}
							}
							break;
						default:  break;
					}
				}
				$tpl->data = $obj;
				$tpl->orderNumber = $a;
				$oStr .= $tpl->render ( );
				$a++;
			}
			$this->page->Body = $oStr;
		}
	}
	
	function addRel ( $type, $params )
	{
		$string = "\t\t<link rel=\"stylesheet\" href=\"{$params}\" />";
		if ( count ( $this->sHeadData ) && is_array ( $this->sHeadData ) )
		{
			foreach ( $this->sHeadData as $test )
			{
				if ( $test == $string)
					return false;
				
			}
		}
		switch ( $type )
		{
			case 'stylesheet':
				$this->sHeadData[] = $string;
				break;
			default:
				break;
		}
	}
	
	function addResource ( $type, $params )
	{
		if ( !in_array ( $params, $this->_dups ) )
		{
			$this->_dups[] = $params;
			switch ( $type )
			{
				case 'stylesheet':
					return $this->addRel ( $type, $params );
				case 'javascript':
					return $this->addHeadScript ( $params );
				default: break;
			}
		}	
	}
	
	function addBottomData ( $data )
	{
		$this->sBottomData[] = $data;
	}
	
	function addHeadScript ( $params )
	{
		$string = "\t\t<script type=\"text/javascript\" src=\"{$params}\"></script>";
		if ( count ( $this->sHeadData ) && is_array ( $this->sHeadData ) )
		{
			foreach ( $this->sHeadData as $test )
			{
				if ( $test == $string )
					return false;
			}
		}
		$this->sHeadData[] = $string;
	}
	
	function addBodyScript ( $params )
	{
		$this->addBottomData ( "\t" . '<script type="text/javascript" src="' . $params . '"></script>' . "\n" );
	}
	
	// renders the document in XML for data extraction (flash mode)
	function renderFlashXML ( )
	{
		$out = Array ( );
		
		$module = false;
		
		if ( $this->page->ContentType == 'extensions' )
		{
			$config = explode ( "\n", $this->page->Intro );
			foreach ( $config as $c )
			{
				list ( $e, $v ) = explode ( "\t", $c );
				if ( $e == 'ExtensionName' )
				{
					$ext = $v;
					break;
				}
			}
			if ( $ext && file_exists ( 'extensions/' . $ext . '/webmoduleflashxml.php' ) )
			{
				$module = Array ( );
				include_once ( 'extensions/' . $ext . '/webmoduleflashxml.php' );
			}
		}
		else if ( $this->page->ContentType == 'news' )
		{
			include_once ( 'web/modules/news/module.php' );
			$this->page->Body = $module;
			$module = false;
		}
		
		foreach ( $this->page as $k=>$v )
		{
			if ( $k[0] == '_' ) continue;
			if ( $module && ( $k == 'Intro' || $k == 'Body' ) )
			{
				if ( $k == 'Intro' )
				{
					if ( $module )
					{
						foreach ( $module as $mod )
						{
							$out[] = $mod;
						}
					}
				}
				continue;
			}
			$key = str_replace ( ' ', '_', $k );
		
			// Content is converted to flash able
			if ( is_array ( $v ) ) die ( print_r ( $v, 1 ) );
			if ( preg_match ( "/[\n|\t|\r|\<|\>]/", $v, $ar ) )
			{
				if ( strstr ( $v, '&' ) )
				{
					$v = preg_replace_callback ( 
						"/(&[a-zA-Z0-9]*;)/", 
						function( $matches ){ return utf8_encode ( html_entity_decode ( $matches[0] ) ); },
						// DEPRECATED create_function ( '$matches', 'return utf8_encode ( html_entity_decode ( $matches[0] ) );' ), 
						$v 
					);
				}
				
				$v = preg_replace ( "/(.*)\n([a-zA-Z0-9\&])/", "$1 $2", $v );
				$v = str_replace ( "\r", ' ', $v );
				
				// Convert new line tags to new lines
				$v = preg_replace ( "/\<p([^>]*?)\>(.*?)\<\/p[^>]*?\>/i", '<p$1><text><![CDATA[$2]]></text></p>', $v );
				$value = preg_replace ( Array ( "/\<p[\w\W]*?\>[\n]{0,1}/", "/<\/p\>[\n]{0,1}/", "/\<br[\/\ ]*\>[\n]{0,1}/" ), Array ( "", "\n\n", "\n" ), $v );
				
				// Remove comments
				$value = preg_replace ( "/\<\!\-[\w\W]*?\-\>/", "", $value );
				
				// Watch font tags
				$value = preg_replace ( "/(\<.*?\=)([0-9]*)\>/i", "$1\"$2\">", $value );
				
				// Watch singletags like br
				$value = preg_replace ( "/\<br\>/i", "<br/>", $value );
				$value = preg_replace ( "/\<hr\>/i", "<hr/>", $value );
								
				// Remove white space
				$value = preg_replace ( "/(\n)([\ |\t]*)([\w\W]{1})/", '$1$3', $value );
				$value = preg_replace ( "/([\w\W]*?)([\ ]*)(\n)/", '$1$3', $value );
				$value = preg_replace ( "/[\ ]{2,99}/", " ", $value );
				$value = str_replace ( "\n\n\n", "\n\n", $value );
				$value = preg_replace ( "/(\n[\t]*)(\w\W)*/", "\n$2", $value );
				$value = str_replace ( "\t", '', $value );
				$value = preg_replace ( "/(\>)([\r|\n]*)([\<])/", "$1$3", $value );
				
				// Fix "single" tags
				$value = preg_replace ( "/\<([a-zA-Z\ ]*?)\/\>/i", "<$1></$1>", $value );
				// ..images
				$value = preg_replace ( "/\<[i|I][m|M][g|G] ([\w\W]*?)\/\>/", "<img $1>", $value );
				$value = preg_replace ( "/\<[i|I][m|M][g|G] ([\w\W]*?)\>/", "<img $1></img>", $value );
				// ..areas
				$value = preg_replace ( "/\<[a|A][r|R][e|E][a|A] ([\w\W]*?)\/\>/", "<area $1>", $value );
				$value = preg_replace ( "/\<[a|A][r|R][e|E][a|A] ([\w\W]*?)\>/", "<area $1></area>", $value );
				
				// Convert strong tags to bold tags
				$value = str_replace ( Array ( '<strong>', '</strong>' ), Array ( '<b>', '</b>' ), $value );
			
				// Remove white space
				$value = preg_replace ( '/\>[\s]*\</', '><', $value );
				
				// Remove obstructive content
				$value = preg_replace ( '/\<form[\w\W]*?\<\/form\>/', '', $value );
				$value = preg_replace ( '/\<button[\w\W]*?\<\/button\>/', '', $value );
				
				// Encapsulate all text data inside 
				$value = $this->EncapsulateInCDATA ( $value );
			
				// Some common bad asses
				$value = str_replace ( 
					Array ( '&nbsp;', '&laquo;', '&raquo;', '&ldquo;', '&rdquo;', '&' ), 
					Array ( ' ', '«', '»', "\"", "\"", '&amp;' ), 
					$value 
				);
				
				// Remove empty ones
				$value = str_replace ( '<![CDATA[]]>', '', $value );
				$value = preg_replace ( "/\<\!\[CDATA\[([\t|\r|\n]*)\]\]\>/", "", $value );
				
				$out[] = '<element name="' . $key . '">' . stripslashes ( $value ) . '</element>';
			}
			else
			{
				$v = str_replace ( '"', '&quot;', $v );
				$value = ' value="' . $v . '">';
				$out[] = '<element name="' . $key . '"' . $value . '</element>';
			}
		}
		$out = preg_replace ( "/([\>])([\ |\r|\n]*)([\<])/", "$1$3", $out );
	
		header ( 'content-type: text/xml; charset=' . $this->_encoding );
		return '<?xml version="1.0" encoding="' . $this->_encoding . '"?>' . getLn ( ) .  
			'<xml>' . mb_convert_encoding ( implode ( "\n", $out ), $this->_encoding, 'utf-8' ) .
			'</xml>'
		;
	}
	
	function encapsulateInCDATA ( $data )
	{
		$depth = 0;
		$mode = 0;
		$len = strlen ( $data );
		$ostr = '';
		$intag = 0;
		$lasttag = 'starttag';
		$encapsulateTimes = 0;
		$data = preg_replace ( '/([\w\W]*?)([\n|\ ]*$)/', "$1", $data );
		$data = preg_replace ( '/([\w\W]*?)((?!\ )[\n|\ ]*\<)/', "$1<", $data );
		
		for ( $a = 0; $a < $len; $a++ )
		{
			if ( $mode == 0 && !$intag && $depth == 0 )
			{
				if ( !preg_match ( '/[\<|\n| ]/', substr ( $data, $a, 1 ) ) )
				{
					$modechange = true;
					if ( substr ( $data, $a, 1 ) == '<' )
						$depth++;
					if ( $lasttag == 'starttag' )
					{
						$ostr .= '<![CDATA[';
						$mode = 1;
						$encapsulateTimes++;
					}
				}
			}
			else if ( $mode == 1 )
			{
				if ( substr ( $data, $a, 2 ) == '</' && $depth == 0 )
				{
					$ostr .= ']]>';
					$mode = 0;
					$lasttag = 'endtag';
					$encapsulateTimes--;
				}
				if ( substr ( $data, $a, 1 ) == '<' && substr ( $data, $a, 2 ) != '</' )
				{
					$lasttag = 'starttag';
					$depth++;
				}
				else if ( ( substr ( $data, $a, 2 ) == '</' || substr ( $data, $a, 2 ) == '/>' ) && $mode == 1 )
				{
					$lasttag = 'endtag';
					$depth--;
				}
			}
			if ( substr ( $data, $a, 1 ) == '<' )
				$intag = true;
			else if ( substr ( $data, $a, 1 ) == '>' )
				$intag = false;
			$ostr .= substr ( $data, $a, 1 );
		}
		if ( $encapsulateTimes && !strstr ( $data, ']]>' ) ) $ostr .= ']]>';
		return $ostr;
	}
	
	function parse_entities ( $ents )
	{
		return utf8_encode ( html_entity_decode ( $ents ) );
	}
	
	function addTopContent ( $cnt )
	{
		$this->_topcontent[] = $cnt;
	}
	
	function render ( )
	{
		global $Session;
		
		if ( !$this->_isAdmin )
		{
			if ( isset ( $_REQUEST[ "searchKeywords" ] ) )
			{
				$this->searchContent ( Array ( "ContentElement", "News" ) );
			}
		}

		/* Add support for content on the top of body */
		if ( count ( $this->_topcontent ) )
		{
			$this->__TopContent = implode ( "\n", $this->_topcontent );
		}
		else $this->__TopContent = '';

		$output = parent::render ( );
	
		/**
		 * Pile up all translations for javascript
		**/
		if ( isset ( $GLOBALS[ 'translations' ] ) )
		{
			$script .= "\n\t\t" . '<script type="text/javascript">' . "\n";
			$script .= "\t\t\t" . 'document.translations = new Array ( );' . "\n";
			foreach ( $GLOBALS[ 'translations' ] as $k => $v )
			{
				$script .= "\t\t\t" . 'document.translations[ "' . addslashes ( $k ) . '" ] = "' . addslashes ( $v ) . '";' . "\n";
			}
			$script .= "\t\t" . '</script>';
			$output = preg_replace ( "/\<body(.*?)\>/", "<body$1>\n" . $script . "\n", $output );
		}
		
		/**
			* Special cases where this template is a document template
		**/

		if ( $this->autoCSS )
		{
			if ( !$this->_isAdmin )
			{
				$this->sHeadData = array_merge ( Array ( "\t\t<base href=\"" . BASE_URL . "\" />" ), $this->sHeadData );
			
				$resources = false;
				$resdata = array ();
				if ( isset ( $Session->LanguageCode ) )
				{
					$l = new dbObject ( 'Languages' );
					$l->Name = $Session->LanguageCode;
					if ( $l->load () )
					{
						if ( isset ( $l->AutomaticResources ) && !$l->AutomaticResources )
						{
							$resources = true;
							if ( trim ( $l->Resources ) )
							{
								$resdata = explode ( "\n", $l->Resources );
							}
						}
					}
				}
				
				if ( !$resources )
				{
					// Include default css + js
					if ( file_exists ( BASE_DIR . "/css/main.css" ) )
						$this->sHeadData[] = "\t\t<link rel=\"stylesheet\" href=\"css/main.css\" />";
					if ( file_exists ( BASE_DIR . "/upload/main.css" ) )
						$this->sHeadData[] = "\t\t<link rel=\"stylesheet\" href=\"upload/main.css\" />";
					if ( file_exists ( BASE_DIR . "/upload/index.css" ) )
						$this->sHeadData[] = "\t\t<link rel=\"stylesheet\" href=\"upload/index.css\" />";
					if ( file_exists ( BASE_DIR . "/css/ie6.css" ) )
						$this->sHeadData[] = "\t\t<!--[if IE 6]>\n<link rel=\"stylesheet\" href=\"css/ie6.css\" />\n<![endif]-->";
					if ( file_exists ( BASE_DIR . "/upload/ie6.css" ) )
						$this->sHeadData[] = "\t\t<!--[if IE 6]><link rel=\"stylesheet\" href=\"upload/ie6.css\" /><![endif]-->\n";				
					if ( file_exists ( BASE_DIR . "/css/ie7.css" ) )
						$this->sHeadData[] = "\t\t<!--[if IE 7]><link rel=\"stylesheet\" href=\"css/ie7.css\" /><![endif]-->";
					if ( file_exists ( BASE_DIR . "/upload/ie7.css" ) )
						$this->sHeadData[] = "\t\t<!--[if IE 7]><link rel=\"stylesheet\" href=\"upload/ie7.css\" /><![endif]-->";
					if ( file_exists ( BASE_DIR . "/css/ie8.css" ) )
						$this->sHeadData[] = "\t\t<!--[if IE 8]><link rel=\"stylesheet\" href=\"css/ie8.css\" /><![endif]-->";
					if ( file_exists ( BASE_DIR . "/upload/ie8.css" ) )
						$this->sHeadData[] = "\t\t<!--[if IE 8]><link rel=\"stylesheet\" href=\"upload/ie8.css\" /><![endif]-->";
					if ( file_exists ( BASE_DIR . "/javascript/main.js" ) )
						$this->sHeadData[] = "\t\t<script type=\"text/javascript\" src=\"" . BASE_URL . "javascript/main.js\"></script>";
					if ( file_exists ( BASE_DIR . "/upload/main.js" ) )
						$this->sHeadData[] = "\t\t<script type=\"text/javascript\" src=\"" . BASE_URL . "upload/main.js\"></script>";
				}
				else if ( count ( $resdata ) )
				{
					foreach ( $resdata as $d )
					{
						if ( !trim ( $d ) ) continue;
						$ext = end ( explode ( '.', trim ( $d ) ) );
						switch ( $ext )
						{
							case 'css':
								$this->sHeadData[] = "\t\t" . '<link rel="stylesheet" href="' . trim ( $d ) . '"/>';
								break;
							case 'js':
								$this->sHeadData[] = "\t\t" . '<script src="' . trim ( $d ) . '"></script>';
								break;
						}
					}
				}
			
				// Others
				$this->sHeadData[] = "\t\t<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\"/>";

				// Favicon support
				if ( file_exists ( BASE_DIR . '/favicon.ico' ) )
					$this->sHeadData[] = "\t\t<link rel=\"shortcut icon\" href=\"/favicon.ico\"/>";
		            
				// On sHeadData, first list css, then javascript
				$outBase = array ();
				$outCss = array ();
				$outOther = array ();
				foreach ( $this->sHeadData as $d )
				{
					if ( strstr ( $d, 'rel="stylesheet' ) )
						$outCss[] = $d;
					else if ( strstr ( $d, '<base' ) )
						$outBase[] = $d;
					else $outOther[] = $d;
				}
				$this->sHeadData = array_merge ( $outBase, $outCss, $outOther );
			
				$top .= implode ( "\n", $this->sHeadData );
				$bottom = implode ( "\n", $this->sBottomData );
			
				// If the template author has created his/her own doctype
				if ( $this->ControlHeaders )
					return $output;
				// Else business as usual
				$output = str_replace ( '</head>', "\n{$top}\n\t</head>", $output );
				$output = str_replace ( "</title>\n\t\n", "</title>\n", $output );
			}
			else
			{
				$metas = Array (
					"\t\t<meta http-equiv=\"cache-control\" content=\"no-store\"/>\n",
					"\t\t<meta http-equiv=\"pragma\" content=\"no-cache\"/>\n"
				);
				$output = str_replace ( "</title>", "</title>\n" . implode ( $metas ), $output );
				$test = explode ( '</title>', $output );
				$test = explode ( '</head>', trim ( $test[1] ) );
				$test = explode ( "\n", trim ( $test[0] ) );
				$outBase = array ();
				$outCss = array ();
				$outScript = array ();
				$outOther = array ();
				foreach ( $test as $t )
				{
					if ( !( $t = trim ( $t ) ) ) continue;
					if ( strstr ( $t, '<script' ) && strstr ( $t, '</script>' ) ) $outScript[] = $t;
					else if ( strstr ( $t, 'rel="stylesheet' ) ) $outCss[] = $t;
					else if ( strstr ( $t, '<base' ) ) $outBase[] = $t;
					else $outOther[] = $t;
				}
				$GLOBALS[ 'tmpheaders' ] = implode ( "\n\t\t", array_merge ( $outBase, $outCss, $outOther, $outScript ) );
				$output = preg_replace_callback ( 
					'/\<\/title\>([\w|\W]*?)\<\/head\>/i', 
					function( $matches )
					{
						return "</title>\n\t\t{$GLOBALS[\'tmpheaders\']}\n\t</head>";
					},
					/*DEPRECTATED: create_function (
						'$matches',
						'return "</title>\n\t\t{$GLOBALS[\'tmpheaders\']}\n\t</head>";'
					),*/
					$output
				);
				unset ( $GLOBALS[ 'tmpheaders' ] );
			}
		}
		else
		{
			// On sHeadData, first list css, then javascript
			$outBase = array ();
			$outCss = array ();
			$outOther = array ();
			foreach ( $this->sHeadData as $d )
			{
				if ( strstr ( $d, 'rel="stylesheet' ) )
					$outCss[] = $d;
				else if ( strstr ( $d, '<base' ) )
					$outBase[] = $d;
				else $outOther[] = $d;
			}
			$this->sHeadData = array_merge ( $outBase, $outCss, $outOther );
		
			$top .= implode ( "\n", $this->sHeadData );
			$bottom = implode ( "\n", $this->sBottomData );
		
			// If the template author has created his/her own doctype
			if ( $this->ControlHeaders )
				return $output;
			// Else business as usual
			$output = str_replace ( '</head>', "\n{$top}\n\t</head>", $output );
			$output = str_replace ( "</title>\n\t\n", "</title>\n", $output );
		}
		
		$bdata = implode ( "\n", $this->sBottomData );
		
		// Last minute replacements
		$output = str_replace ( 
			array ( '</body>', ' texteditormark="1"' ), 
			array ( $bdata . "\n\t</body>", '' ), 
			$output 
		);
		$this->_rendered = true;
		return $output;
	}
}

?>
