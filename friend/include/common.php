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
New code is (C) 2011 Idéverket AS, 2015 Friend Studios AS

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
                Rune Nilssen
*******************************************************************************/


/* Helper function to get a file by path */
function GetFileByPath ( $path, $parent = 0 )
{
	if( is_numeric ( $parent ) && $parent == 0 )
	{
		if( strstr ( $path, ':Library/' ) && list ( , $path ) = explode ( ':', $path ) )
		{
			$filename = end( explode( '/', $path ) );
			
			// Remove filename
			if( substr( $path, -1, 1 ) != '/' )
			{
				$path = explode( '/', $path );
				array_pop( $path );
				$path = implode( '/', $path );
			}
			$path = explode ( '/', $path );
			if( count( $path ) == 1 )
			{
				$p = dbFolder::getRootFolder();
			}
			else $p = GetFolderByPath( $path );
			
			$f = new dbObject( 'Image' );
			$f->ImageFolder = $p->ID;
			$f->Filename = $filename;
			if( !$f->Load() )
			{
				$f = new dbObject( 'File' );
				$f->FileFolder = $p->ID;
				$f->Filename = $filename;
				if( !$f->Load() )
					return false;
			}
			return $f;
		}
		else if( strstr( $path, ':Users/' ) && list( , $path ) = explode( ':Users/', $path ) )
		{
			die( 'fail' );
		}
		else if( strstr( $path, ':Content/' ) && list( , $path ) = explode( ':Content/', $path ) )
		{
			i18nAddLocalePath ( 'friend/content/locale/' );
			$apath = explode ( '/', $path );
			
			if( $c = GetContentByPath( $path ) )
			{
				die( 'ok<!--separate-->..' . print_r( $c, 1 ) );
			}
			die( 'fail' );
			
			$o = new dbObject ( $_POST['objecttype'] );
			if( $o->Load ( $_POST['objectid'] ) )
			{
				$WindowWidth = 500;
				$WindowHeight = 400;
				$o->Type = str_replace ( array ( '/', '..' ), '', $o->Type );
				$output = '';
				if( file_exists ( 'friend/content/view/' . $o->Type . '.php' ) )
				{
					if( file_exists ( 'friend/content/templates/' . $o->Type . '.php' ) )
					{
						$tpl = new cPTemplate ( 'friend/content/templates/' . $o->Type . '.php' );
						$tpl->field = $o;
						$tpl->fieldType = $_POST['objecttype'];
						$tpl->path = $_POST['path'];
					}
					include_once ( 'friend/content/view/' . $o->Type . '.php' );
				}
				// Generate output
				$return = new stdclass ();
				if( strlen ( $output ) && !isset ( $tpl ) )
					$return->HTML = $output;
				else if( isset ( $tpl ) )
					$return->HTML = $tpl->render();
				$return->WindowWidth = $WindowWidth;
				$return->WindowHeight = $WindowHeight;
				die ( 'ok<!--separate-->' . json_encode ( $return ) );
			}
			die( 'fail<!--separate-->' );
		}
		else if( strstr ( $path, ':Settings/' ) && list ( , $path ) = explode ( ':Settings/', $path ) )
		{
			i18nAddLocalePath ( 'friend/content/locale/' );
			$return = array ();
			$default = new stdclass ();
			$default->ID = $path . 'Settings';
			$default->MetaType = 'Executable';
			$default->Title = 'Settings';
			$default->Name = 'Settings';
			$default->Volume = reset ( explode ( ':', $path ) ) . ':';
			$default->Path = $path;
			$default->Type = 'arena';
			$return[] = $default;
			die( 'ok<!--separate-->' . json_encode ( $return ) );
		}
		else
		{
			die( 'ok<!--separate-->' . json_encode ( array ( 'failed!' ) ) );
		}
	}
	if ( is_array ( $path ) )
	{
		$currPath = $path[0];
		$path = array_reverse ( $path ); array_pop ( $path );
		$path = array_reverse ( $path );

		// Try to load as folder
		if ( count ( $path ) && isset ( $path[0] ) && trim ( $path[0] ) )
		{
			$folder = new dbFolder ();
			$folder->Name = $currPath;
			$folder->Parent = $parent->ID;
			if ( $folder->Load () )
			{
				return GetFileByPath ( $path, $folder );
			}
		}
		else
		{
			// FIXME:
			// Always assuming file here on file extension!
			// Give more data in the request!
			$ext = strtolower ( end ( explode ( '.', $currPath ) ) );
			switch ( $ext )
			{
				case 'jpg':
				case 'png':
				case 'bmp':
				case 'jpeg':
				case 'gif':
					$file = new dbImage ();
					$file->ImageFolder = $parent->ID;
					$file->Name = $currPath;
					$file->Load ();
					break;
				default:
					$file = new dbFile ();
					$file->FileFolder = $parent->ID;
					$file->Name = $currPath;
					$file->Load ();
					break;
			}
			die ( 'ok<!--separate-->' . BASE_URL . 'friend.php?action=getfile&fid=' . $file->ID . '&type=' . ( strtolower ( get_class ( $file ) ) == 'dbimage' ? 'image' : 'file' ) );
		}
	}
	else
	{
	}
	die ( 'fail' );
}

function GetFolderByPath( $path, $par = false, $lev = 1 )
{
	if( !$par )
		$par = dbFolder::getRootFolder();
	$totalDepth = count( $path ); // Includes superfulous library/ first
	$lev++;
	
	if( $totalDepth == 1 && $path[0] == 'Library' )
		return $par;
	
	if( $flds = $par->getFolders() )
	{
		foreach( $flds as $fld )
		{
			if( $fld->Name == $path[$lev-1] )
			{
				if( $lev == $totalDepth )
				{
					return $fld;
				}
				else
				{
					return getFolderByPath( $path, $fld, $lev );
				}
			}
		}
	}
	return false;
}

// Gets a content element by path
function GetContentByPath( $path, $par = false, $lev = 1 )
{
	include_once( 'lib/classes/dbObjects/dbContent.php' );
	
	$volume = '';
	if( is_string( $path ) )
	{
		list( $volume, $path ) = explode( ':', $path );
		if( strstr( $path, '/' ) ) $path = explode( '/', $path );
		else $path = array( $path );
	}
	
	if( !$par )
	{
		$cnt = new dbContent();
		$par = $cnt->getRootContent();
	}

	$totalDepth = count( $path ); // Includes superfulous library/ first
	$lev++;
	
	if( $totalDepth == 1 && $path[0] == 'Content' )
		return $par;
	
	$par->loadSubElements();
	
	if( $flds = $par->subElements )
	{
		foreach( $flds as $fld )
		{
			if( $fld->MenuTitle == $path[$lev-1] )
			{
				if( $lev == $totalDepth )
				{
					return $fld;
				}
				else
				{
					return getContentByPath( $path, $fld, $lev );
				}
			}
		}
	}
	return false;
}

?>
