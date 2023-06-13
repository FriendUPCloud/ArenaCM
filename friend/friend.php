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


// Bootstrap -------------------------------------------------------------------

session_start ();

include ( 'lib/functions/functions.php' );
include ( 'lib/classes/database/cdatabase.php' );
include ( 'lib/classes/dbObjects/dbObject.php' );
include ( 'lib/classes/dbObjects/dbFolder.php' );
include ( 'lib/classes/dbObjects/dbFile.php' );
include ( 'lib/classes/dbObjects/dbImage.php' );
require ( 'lib/classes/dbObjects/dbUser.php' );
include ( 'lib/classes/session/session.php' );
include ( 'lib/classes/template/cPTemplate.php' );
include ( 'friend/include/common.php' );
include ( 'config.php' );

if ( !defined ( 'LOCALE' ) )
	define ( 'LOCALE', 'en' );
$Session = new Session ( SITE_ID . 'admin' );
$Session->Set ( 'LanguageCode', LOCALE );
$Session->Set ( 'AdminLanguageCode', LOCALE );
$GLOBALS[ 'Session' ] =& $Session;
i18nAddLocalePath ( 'admin/locale' );

/**
 * Setup the core database
 */

$corebase = new cDatabase ( );
include_once( 'lib/lib.php' );
include_once( 'lib/core_config.php' );
$corebase->Open();
dbObject::globalValueSet( 'corebase', $corebase );

/**
 * Setup the site database
 */
if ( !( $siteData = $corebase->fetchObjectRow ( 'SELECT * FROM `Sites` WHERE `SiteName`="' . SITE_ID . '"' ) ) )
{
	if ( $_REQUEST[ 'installer' ] )
	{
		include_once ( 'install.php' );
	}
	else
	{
		ArenaDie ( 'Failed to initialize site: ' . SITE_ID );
	}
}

/**
 * Set up the database and controls
**/

$database = new cDatabase( );
$database->setUsername( $siteData->SqlUser );
$database->setPassword( $siteData->SqlPass );
$database->setHostname( $siteData->SqlHost );
$database->setDb( $siteData->SqlDatabase );
$database->Open();
$userbase =& $database;
dbObject::globalValueSet( 'sitedata', $siteData );
define ( 'BASE_URL', $siteData->BaseUrl );
define ( 'BASE_DIR', $siteData->BaseDir );
dbObject::globalValueSet ( 'database', $database );

/**
 * Make sure we clean up after ourselves
**/

function shutdown_arena2( $ar )
{
	$ar[ 'corebase' ]->Close ( );
	$ar[ 'database' ]->Close ( );
}
register_shutdown_function ( shutdown_arena2, array ( 'corebase'=>&$corebase, 'database'=>&$database ) );

/* Do the tango! ------------------------------------------------------------ */
switch( $_REQUEST['action'] )
{
	// Auth to get a user token!
	case 'auth':
		$us = new dbUser ();
		if ( $us->authenticate ( ) )
		{
			$token = new dbObject ( 'UserLogin' );
			$token->UserID = $us->ID;
			$token->DataSource = 'core';
			if ( $token->Load () )
			{
				$token = $token->Token;
			}
			else $token = '';
			die ( 'ok<!--separate-->' . $token );
		}
		session_destroy ();
		die ( 'fail' );
	case 'volumeinfo':
		$fsum = $database->fetchObjectRow( '
			SELECT SUM(Filesize) fz FROM `File`
		' );
		$isum = $database->fetchObjectRow( '
			SELECT SUM(Filesize) fz FROM `Image`
		' );
		
		die( 'ok<!--separate-->{"Filesize": "5000000", "Used":"' . ( $isum->fz + $fsum->fz ) . '"}' );
		
		break;
	case 'user':
		$token = new dbObject ( 'UserLogin' );
		$token->Token = $_REQUEST['token'];
		if ( trim ( $token->Token ) && $token->Load () )
		{
			$user = new dbObject ( 'Users' );
			list ( , $name ) = explode ( '/', $_REQUEST['path'] );
			$user->Name = $name;
			if ( $user->Load () )
			{
				$class = new stdClass ();
				$class->Name = $user->Name;
				$class->Username = $user->Username;
				die ( 'ok<!--separate-->' . json_encode ( $class ) );
			}
		}
		break;
	case 'makedir':
		$fold = end( explode( ':', $_REQUEST['path'] ) );
		if( $fold && trim( $fold ) )
		{
			if( $fold = explode( '/', $fold ) )
			{
				if( $fld = GetFolderByPath( $fold ) )
				{
					$newFold = new dbFolder();
					$newFold->Parent = $fld->ID;
					$newFold->Name = $_REQUEST['foldername' ];
					$newFold->Save();
					die( 'ok' );
				}
			}
			die( 'fail' );
		}
		// It's root...
		else
		{
			$fld = dbFolder::getRootFolder();
			$newFold = new dbFolder();
			$newFold->Parent = $fld->ID;
			$newFold->Name = $_REQUEST['foldername' ];
			$newFold->Save();
			die( 'ok' );
		}
		break;
	case 'getfile':
		$token = new dbObject ( 'UserLogin' );
		$token->Token = $_REQUEST['token'];
		ob_clean ();
		if ( trim ( $token->Token ) && $token->Load () )
		{
			require( 'friend/library/getfile.php' );
		}
		die ( 'No token' );
		break;
	case 'savefile':
		// TODO: PERMISSIONS PERMISSSONSSSS!!!
		$token = new dbObject ( 'UserLogin' );
		$token->Token = $_POST['token'];
		if ( trim ( $token->Token ) && $token->Load () )
		{	
			$part = reset( explode( '/', end( explode( ':', $_POST['path'] ) ) ) );
			switch( $part )
			{
				case 'Library':
					include( 'friend/library/writefile.php' );
					break;
				case 'Content':
					include( 'friend/content/writefile.php' );
					break;
				default:
					break;
			}
		}
		die ( 'fail' );
		break;
	case 'deletefile':
		$token = new dbObject ( 'UserLogin' );
		$token->Token = $_POST['token'];
		if ( trim ( $token->Token ) && $token->Load () )
		{
			$part = reset( explode( '/', end( explode( ':', $_POST['path'] ) ) ) );
			switch( $part )
			{
				case 'Library':
					include( 'friend/library/deletefile.php' );
					break;
				default:
					break;
			}
		}
		break;
	// Must be logged in!
	case 'filecontent':
		$token = new dbObject ( 'UserLogin' );
		$token->Token = $_POST['token'];
		if( trim( $token->Token ) && $token->Load() )
		{
			if( strstr( $pathHere = $_POST['path'], ':Content' ) )
			{
				if( strstr( $pathHere, '/' ) )
				{
					$pathHere = explode( '/', $pathHere );
					$field = array_pop( $pathHere );
					$pathHere = implode( '/', $pathHere );
				}
				else
				{
					$pathHere = explode( ':', $_pathHere );
					$field = $pathHere[1];
					$pathHere = $pathHere[0] . ':';
				}
				$content = GetContentByPath( $pathHere );
				
				// TODO: Handle different modules!
				if( $row = $database->fetchObjectRow( '
					SELECT * FROM (
						SELECT DataMixed TEXT FROM ContentDataSmall WHERE `Name` = "' . $field . '" AND ContentID=\'' . $content->ID . '\'
						UNION
						SELECT DataText TEXT FROM ContentDataBig WHERE `Name` = "' . $field . '" AND ContentID=\'' . $content->ID . '\'
					) z
					LIMIT 1
				' ) )
				{
					if( $_POST['mode'] == 'rb' )
						die( $row->TEXT );
					die( 'ok<!--separate-->' . $row->TEXT );
				}
				die( mysql_error() ) ;
				
				die( 'fail' );
			}
			else
			{
				include( 'friend/library/getfile.php' );
			}
			//else die( 'fail<!--separate-->' );
		}
		die( 'fail' );
		break;
	// Display folder content! (must be logged in!)
	case 'foldercontent':
		$token = new dbObject ( 'UserLogin' );
		$token->Token = $_POST['token'];
		if ( trim ( $token->Token ) && $token->Load () )
		{
			if ( isset ( $_POST[ 'foldertype' ] ) )
			{
				switch ( $_POST['foldertype'] )
				{
					// List out all the entities in the selected folder
					case 'folder':
						if ( !trim ( $_POST['path'] ) )
							die ( 'fail' );
						// What are we asking for? Check first component
						list ( , $virtualFolder ) = explode ( ':', $_POST['path'] );
						$folders = explode ( '/', $virtualFolder );
						list ( $virtualFolder ) = explode ( '/', $virtualFolder );

						// Ah it's the users folder - then list out all the users!
						if ( $virtualFolder == 'Users' )
						{
							include ( 'friend/users/foldercontent.php' );
						}
						// We want the library!
						else if ( $virtualFolder == 'Library' )
						{
							include ( 'friend/library/foldercontent.php' );
						}
						else if ( $virtualFolder == 'Content' )
						{
							include ( 'friend/content/foldercontent.php' );
						}
						break;
					// List out all the modules and extensions on the site
					case 'root':
						$array = array ();
						if ( $dir = opendir ( 'admin/modules' ) )
						{
							while ( $file = readdir ( $dir ) )
							{
								if ( $file[0] == '.' ) continue;
								$mod = false;
								switch ( $file )
								{
									case 'library':
									case 'users':
									case 'settings':
										$mod = new stdClass ();
										$mod->Type = 'Directory';
										$mod->MetaType = 'Directory';
										$mod->Filename = ucfirst( $file );
										$mod->Path = $_REQUEST['volume'] . ':' . ucfirst( $file );
										break;
								}
								if ( $mod ) $array[] = $mod;
							}
							closedir ( $dir );
						}
						if ( $dir = opendir ( 'extensions' ) )
						{
							while ( $file = readdir ( $dir ) )
							{
								if ( $file[0] == '.' ) continue;
								$mod = false;
								if ( file_exists ( 'extensions/' . $file . '/info.csv' ) )
								{
									$mod = new stdClass ();
									$mod->Type = 'Directory';
									$mod->MetaType = 'Directory';
									if ( $file == 'editor' )
										$mod->Filename = 'Content';
									else $mod->Filename = ucfirst( $file );
									$mod->Path = $_REQUEST['volume'] . ':' . $mod->Filename;
								}
								if ( $mod ) $array[] = $mod;
							}
							closedir ( $dir );
						}
						die ( 'ok<!--separate-->' . json_encode ( $array ) );
						break;
					default:
						break;
				}
			}
		}
		die ( 'fail' );
	case 'status':
		// Must be logged in
		$token = new dbObject( 'UserLogin' );
		$token->Token = $_POST['token'];
		if( !trim( $token->Token ) || !$token->Load() )
			die( 'fail' );

		// Check for module
		if( isset( $_POST['module'] ) && $module = trim( $_POST['module'] ) )
		{
			if( file_exists( 'extensions/' . $module . '/status.json' ) )
			{
				die( 'ok<!--separate-->'.file_get_contents( 'extensions/' . $module . '/status.json' ) );
			}
			else
			{
				die( 'ok<!--separate-->'.json_encode( array( 'error' => 'no status' ) ) );
			}
		}
		else
		{
			die( 'ok<!--separate-->'.json_encode( array( 'error' => 'not implemented' ) ) );
		}
		break;
}
die ( 'totalfail' );

?>
