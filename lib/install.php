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


error_reporting( E_ALL & ~E_NOTICE & ~E_DEPRECATED );
ini_set( 'display_errors', true );

$root = '.';

include_once( "$root/lib/classes/template/cPTemplate.php" );
include_once( "$root/lib/classes/database/cdatabase.php" );
include_once( "$root/lib/classes/dbObjects/dbObject.php" );
include_once( "$root/lib/functions/functions.php" );

// Set basedir and baseurl
$basedir = getcwd ();
$baseurl = str_replace ( 
	array ( 'index.php', 'admin.php' ), 
	'', 
	$_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] 
);

// Check for errors ----------------------------------------------------------->

$errors = array ();

// If config doesn't exist create it
if ( !file_exists( "$root/config.php" ) )
{
	$fp = fopen ( "$root/config.php", 'w' );
	if ( !$fp && file_exists( "$root/config.php.example" ) )
	{
		// Rename example file to an empty config file
		if ( $fc = fopen ( "$root/config.php.example", 'w+' ) )
		{
			fwrite ( $fc, '' );
			fclose ( $fc );
			
			@rename( "$root/config.php.example", "$root/config.php" );
		}
	}
	fclose ( $fp );
}
// Check that the config is writable
if ( !filesize( "$root/config.php" ) )
{
	if ( !( $fp = fopen ( "$root/config.php", 'w' ) ) )
	{
		$errors[] = 'Can not open config.php or config.php.example for writing! Please change the permissions on it to "777".';
	}
	else fclose ( $fp );
}

// If core config doesn't exist create it
if ( !file_exists( "$root/lib/core_config.php" ) )
{
	$fp = fopen ( "$root/lib/core_config.php", 'w' );
	if ( !$fp && file_exists( "$root/lib/core_config.php.example" ) )
	{
		// Rename example file to an empty config file
		if ( $fc = fopen ( "$root/lib/core_config.php.example", 'w+' ) )
		{
			fwrite ( $fc, '' );
			fclose ( $fc );
			
			@rename( "$root/lib/core_config.php.example", "$root/lib/core_config.php" );
		}
	}
	fclose ( $fp );
}
// Check that the core config is writable
if ( !filesize( "$root/lib/core_config.php" ) )
{
	if ( !( $fp = fopen ( "$root/lib/core_config.php", 'w' ) ) )
	{
		$errors[] = 'Can not open lib/core_config.php or lib/core_config.php.example for writing! Please change the permissions on it to "777".';
	}
	else fclose ( $fp );
}

// Create admin.php file if it's missing
if ( !file_exists( "$root/admin.php" ) )
{
	// Set config file
	if( !( $fp = fopen ( "$root/admin.php", 'w+' ) ) )
	{
		$errors[] = 'No "admin.php" file exists! and we can\'t create it. Copy it from "admin/admin.php".';
	}
	else
	{
		$str = '<?php require ( \'admin/admin.php\' ); ?>';
		fwrite ( $fp, $str );
		fclose ( $fp );
	}
}

// Create symlink to .htaccess file
if ( !file_exists( $basedir . '/.htaccess' ) && !is_link( $basedir . '/.htaccess' ) )
{
	if( !symlink( $basedir . '/lib/htaccess', $basedir . '/.htaccess' ) )
	{
		$errors[] = 'No ".htaccess" file exists! and we can\'t link to it. Copy it from "lib/htaccess" and rename it ".htaccess".';
	}
}

// Check upload folder, that it exists
if ( !file_exists( "$root/upload" ) )
{
	@mkdir( "$root/upload", 0777, true );
	@chmod( "$root/upload", 0777 );
}
if ( !file_exists ( "$root/upload" ) )
{
	$errors[] = 'No "upload" directory exists!';
}
else
{
	// Check upload folder
	if ( !( $fp = fopen ( "$root/upload/test", 'w' ) ) )
	{
		$errors[] = 'The "upload" folder is not writable. Please change the permissions on it to "777".';
	}
	if ( file_exists( "$root/upload/test" ) )
	{
		@unlink( "$root/upload/test" );
	}
	if ( $fp ) fclose ( $fp );
	
	// Check images-master
	if ( !file_exists ( "$root/upload/images-master" ) )
	{
		@mkdir( "$root/upload/images-master", 0777, true );
		@chmod( "$root/upload/images-master", 0777 );
	}
	if ( !file_exists ( "$root/upload/images-master" ) )
	{
		$errors[] = 'No "upload/images-master" directory exists!';	
	}
	else if ( !( $fp = fopen ( "$root/upload/images-master/test", 'w' ) ) )
	{
		$errors[] = 'The "upload/images-master" folder is not writable. Please change the permissions on it to "777".';
	}
	if ( file_exists( "$root/upload/images-master/test" ) )
	{
		@unlink( "$root/upload/images-master/test" );
	}
	if ( $fp ) fclose ( $fp );
	
	// Check images-cache
	if ( !file_exists ( "$root/upload/images-cache" ) )
	{
		@mkdir( "$root/upload/images-cache", 0777, true );
		@chmod( "$root/upload/images-cache", 0777 );
	}
	if ( !file_exists ( "$root/upload/images-cache" ) )
	{
		$errors[] = 'No "upload/images-cache" directory exists!';	
	}
	else if ( !( $fp = fopen ( "$root/upload/images-cache/test", 'w' ) ) )
	{
		$errors[] = 'The "upload/images-cache" folder is not writable. Please change the permissions on it to "777".';
	}
	if ( file_exists( "$root/upload/images-cache/test" ) )
	{
		@unlink( "$root/upload/images-cache/test" );
	}
	if ( $fp ) fclose ( $fp );
}
// Check extensions folder
if ( !file_exists ( "$root/extensions" ) )
{
	$errors[] = 'No "extensions" folder exist!';
}
else if ( !file_exists ( "$root/extensions/editor" ) )
{
	$errors[] = 'You need to add the "editor" extension to the extensions folder. Copy the "editor" extension folder into "extensions/" to adress this.';
}

// Check if username is a valid one for the Treeroot extension
if ( isset( $_REQUEST['step'] ) && isset( $_POST['subether'] ) && $_POST['subether'] && $_POST['loginUsername'] )
{
	if( ( !strstr( $_POST['loginUsername'], '@' ) && !strstr( $_POST['loginUsername'], '.' ) ) || ( strlen( $_POST['loginUsername'] ) < 4 && !is_numeric( $_POST['loginUsername'] ) ) )
	{
		$errors[] = 'Type in a valid unique email as username for the Treeroot extension.';
	}
}

// Done checking for errors ---------------------------------------------------<

if ( !$errors )
{
	switch ( isset( $_REQUEST['step'] ) ? $_REQUEST['step'] : '' )
	{
		case '3':
			$tpl = new cPTemplate ( "$root/lib/templates/installer/step3.php" );
			break;
		case '2':
			
			switch ( $_POST['siteType'] )
			{
				// SingleSite Setup
				
				case '1':
					
					// Define objects
					$sdb = new cDatabase ( );
					$sdb->setUsername ( $_POST['siteUsername'] );
					$sdb->setPassword ( $_POST['sitePassword'] );
					$sdb->setHostname ( $_POST['siteHostname'] );
					
					// Connect to core database
					if ( !$sdb->open () )
						die ( 'Failed to connect to database!' );
					$sdb->setDb ( $_POST['siteDatabase'] );
					
					// Check for site database, create if it doesn't exist
					$result = $sdb->fetchRow ( 'DESCRIBE Sites' );
					if ( $result['Field'] != 'ID' )
					{
						// Create
						$sdb->query ( 'CREATE DATABASE `' . $_POST['siteDatabase'] . '`' );
						$sdb->query ( 'USE `' . $_POST['siteDatabase'] . '`' );
						
						if ( !$sdb->fetchObjectRows( 'SELECT ID FROM Sites ORDER BY ID ASC' ) )
						{
							// Import structure
							$sql = file_get_contents ( $basedir . '/lib/skeleton/arenasingle.sql' ); 
							$sql = explode ( ';', $sql );
							foreach ( $sql as $s )
							{
								if ( $s{0} == '-' ) continue;
								if ( !trim ( $s ) ) continue;
								$sdb->query ( trim ( $s ) );
							}
						}
						
						if ( !$sdb->fetchObjectRows( 'SELECT ID FROM Users ORDER BY ID ASC' ) )
						{
							// Add root user
							$sdb->query ( '
								INSERT INTO Users ( `Username`, `Password`, `Name`, `Email`, `IsAdmin` ) 
								VALUES ( "'.($_POST['loginUsername']?$_POST['loginUsername']:'arenauser').'", md5("'.($_POST['loginPassword']?$_POST['loginPassword']:'arenapassword').'"), "'.($_POST['loginName']?$_POST['loginName']:'ArenaCM Admin').'", "'.($_POST['loginEmail']?$_POST['loginEmail']:'admin@'.$_SERVER['SERVER_NAME']).'", "1" )
							' );
						}
						
						$result = $sdb->fetchRow ( 'DESCRIBE Sites' );
					}
					if ( $result['Field'] != 'ID' )
					{
						die ( 'Failed to find and/or create database!' );
					}
					
					$sdb->query ( 'USE `' . $_POST['siteDatabase'] . '`' );
					
					if ( !$sdb->fetchObjectRows( $q = 'SELECT ID FROM Sites BaseUrl = \'' . $baseurl . '\' AND BaseDir = \'' . $basedir . '\' ORDER BY ID ASC' ) )
					{
						// Insert site into db
						$site = new dbObject ( 'Sites', $sdb );
						$site->SiteName = $_POST['siteID'];
						$site->Load ();
						$site->SqlUser = $_POST['siteUsername'];
						$site->SqlPass = $_POST['sitePassword'];
						$site->SqlHost = $_POST['siteHostname'];
						$site->SqlDatabase = $_POST['siteDatabase'];
						$site->BaseUrl = $baseurl;
						$site->BaseDir = $basedir;
						$site->Save ();
						
						// Add user to superadmin group
						if ( $user = $sdb->fetchObjectRow( 'SELECT ID FROM Users ORDER BY ID ASC' ) )
						{
							if ( isset( $user->ID ) && $user->ID > 0 )
							{
								$group = new dbObject ( 'Groups', $sdb );
								$group->SuperAdmin = 1;
								$group->Name = 'Administrator';
								$group->Load ();
								$group->Save ();
							}
							
							if ( isset( $group->ID ) && $group->ID > 0 )
							{
								$usgro = new dbObject ( 'UsersGroups', $sdb );
								$usgro->UserID = $user->ID;
								$usgro->GroupID = $group->ID;
								$usgro->Load ();
								$usgro->Save ();
							}
						}
						
						// Insert standard modules
						$a = 0; $mods = array ( 'extensions', 'library', 'users', 'settings' );
						foreach ( $mods as $m )
						{
							$mod = new dbObject ( 'ModulesEnabled', $sdb );
							$mod->SiteID = $site->ID;
							$mod->Module = $m;
							$mod->Load ();
							$mod->SortOrder = $a++;
							$mod->Save ();
						}
					}
					
					// If subether is available use that installer
					if ( isset( $_POST['subether'] ) && $_POST['subether'] && file_exists( "$root/subether/install.php" ) )
					{
						include_once( "$root/subether/install.php" );
					}
					// Run default arena setup
					else
					{
						if ( $sdb && !$sdb->fetchObjectRows( 'SELECT ID FROM ContentElement ORDER BY ID ASC' ) )
						{							
							$language = new dbObject ( 'Languages', $sdb );
							$language->Name = 'no';
							$language->NativeName = 'Norsk';
							$language->IsDefault = '1';
							$language->Load ();
							$language->Save (); 
							
							// Add first content
							$firstContent = new dbObject ( 'ContentElement', $sdb );
							$firstContent->Title = 'Root';
							$firstContent->MenuTitle = i18n( 'Welcome to ARENACM' );
							$firstContent->Parent = '0';
							$firstContent->SystemName = 'root';
							$firstContent->IsPublished = '1';
							$firstContent->Language = $language->ID;
							$firstContent->ContentType = 'extrafields';
							$firstContent->RouteName = 'root';
							$firstContent->ContentGroups = 'Topp, Felt1, Felt2, Bunn';
							$firstContent->Save ();
							$firstContent->MainID = $firstContent->ID;
							$firstContent->Save (); // published copy
							$pbid = $firstContent->ID;
							$firstContent->ID = false; 
							$firstContent->Save (); // work copy
							$cpid = $firstContent->ID;
							
							$contentData = new dbObject ( 'ContentDataBig', $sdb );
							$contentData->ContentID = $pbid;
							$contentData->ContentTable = 'ContentElement';
							$contentData->DataText = i18n( 'Welcome to ARENACM' );
							$contentData->Name = 'Hovedfelt';
							$contentData->Type = 'text';
							$contentData->IsVisible = '1';
							$contentData->AdminVisibility = '1';
							$contentData->ContentGroup = 'Felt1';
							$contentData->Save (); // published copy
							$contentData->ID = false;
							$contentData->ContentID = $cpid;
							$contentData->Save (); // work copy
						}
					}
					
					break;
				
				// Multisite Setup
				
				default:
					
					// Define objects
					$cdb = new cDatabase ( );
					$cdb->setUsername ( $_POST['coreUsername'] );
					$cdb->setPassword ( $_POST['corePassword'] );
					$cdb->setHostname ( $_POST['coreHostname'] );
					$sdb = new cDatabase ( );
					$sdb->setUsername ( $_POST['siteUsername'] );
					$sdb->setPassword ( $_POST['sitePassword'] );
					$sdb->setHostname ( $_POST['siteHostname'] );
					
					// Connect to core database
					if ( !$cdb->open () )
						die ( 'Failed to connect to core database!' );
					$cdb->setDb ( $_POST['coreDatabase'] );
					
					// Check for core database, create if it doesn't exist
					$result = $cdb->fetchRow ( 'DESCRIBE Sites' );
					if ( $result['Field'] != 'ID' )
					{
						// Create
						$cdb->query ( 'CREATE DATABASE `' . $_POST['coreDatabase'] . '`' );
						$cdb->query ( 'USE `' . $_POST['coreDatabase'] . '`' );
						
						if ( !$cdb->fetchObjectRows( 'SELECT ID FROM Sites ORDER BY ID ASC' ) )
						{
							// Import structure
							$sql = file_get_contents ( $basedir . '/lib/skeleton/arenacore.sql' ); 
							$sql = explode ( ';', $sql );
							foreach ( $sql as $s )
							{
								if ( $s{0} == '-' ) continue;
								if ( !trim ( $s ) ) continue;
								$cdb->query ( trim ( $s ) );
							}
						}
						
						if ( !$cdb->fetchObjectRows( 'SELECT ID FROM Users ORDER BY ID ASC' ) )
						{
							// Add root user
							$cdb->query ( '
								INSERT INTO Users ( `Username`, `Password`, `Name`, `Email`, `IsAdmin` ) 
								VALUES ( "'.($_POST['loginUsername']?$_POST['loginUsername']:'arenauser').'", md5("'.($_POST['loginPassword']?$_POST['loginPassword']:'arenapassword').'"), "'.($_POST['loginName']?$_POST['loginName']:'ArenaCM Admin').'", "'.($_POST['loginEmail']?$_POST['loginEmail']:'admin@'.$_SERVER['SERVER_NAME']).'", "1" )
							' );
						}
						
						$result = $cdb->fetchRow ( 'DESCRIBE Sites' );
					}
					if ( $result['Field'] != 'ID' )
					{
						die ( 'Failed to find and/or create core database!' );
					}
					
					// Create site
					$cdb->query ( 'CREATE DATABASE `' . $_POST['siteDatabase'] . '`' );
					$cdb->query ( 'USE `' . $_POST['siteDatabase'] . '`' );
					if ( $sdb->open () )
					{
						if ( !$cdb->fetchObjectRows( 'SELECT ID FROM Sites ORDER BY ID ASC' ) )
						{
							$sql = file_get_contents ( $basedir . '/lib/skeleton/arenadb.sql' ); 
							$sql = explode ( ';', $sql );
							foreach ( $sql as $s )
							{
								if ( $s{0} == '-' ) continue;
								if ( !trim ( $s ) ) continue;
								$cdb->query ( trim ( $s ) );
							}
						}
					}
					else die ( 'Failed to create database `' . $_POST['siteDatabase'] . '`.' );
					
					$cdb->query ( 'USE `' . $_POST['coreDatabase'] . '`' );
					
					if ( !$cdb->fetchObjectRows( 'SELECT ID FROM Sites WHERE BaseUrl = \'' . $baseurl . '\' AND BaseDir = \'' . $basedir . '\' ORDER BY ID ASC' ) )
					{
						// Insert site into core db
						$site = new dbObject ( 'Sites', $cdb );
						$site->SiteName = $_POST['siteID'];
						$site->Load ();
						$site->SqlUser = $_POST['siteUsername'];
						$site->SqlPass = $_POST['sitePassword'];
						$site->SqlHost = $_POST['siteHostname'];
						$site->SqlDatabase = $_POST['siteDatabase'];
						$site->BaseUrl = $baseurl;
						$site->BaseDir = $basedir;
						$site->Save ();
						
						// Add user to superadmin group
						if ( $user = $cdb->fetchObjectRow( 'SELECT ID FROM Users ORDER BY ID ASC' ) )
						{
							if ( isset( $user->ID ) && $user->ID > 0 )
							{
								$group = new dbObject ( 'Groups', $cdb );
								$group->SuperAdmin = 1;
								$group->Name = 'Administrator';
								$group->Load ();
								$group->Save ();
							}
							
							if ( isset( $group->ID ) && $group->ID > 0 )
							{
								$usgro = new dbObject ( 'UsersGroups', $cdb );
								$usgro->UserID = $user->ID;
								$usgro->GroupID = $group->ID;
								$usgro->Load ();
								$usgro->Save ();
							}
						}
						
						$language = new dbObject ( 'Languages', $sdb );
						$language->Name = 'no';
						$language->NativeName = 'Norsk';
						$language->IsDefault = '1';
						$language->Load ();
						$language->Save (); 
						
						// Add first content
						$firstContent = new dbObject ( 'ContentElement', $sdb );
						$firstContent->Title = 'Root';
						$firstContent->MenuTitle = 'Root';
						$firstContent->Parent = '0';
						$firstContent->SystemName = 'root';
						$firstContent->IsPublished = '1';
						$firstContent->Language = $language->ID;
						$firstContent->ContentType = 'extrafields';
						$firstContent->RouteName = 'root';
						$firstContent->ContentGroups = 'Topp, Felt1, Felt2, Bunn';
						$firstContent->Save ();
						$firstContent->MainID = $firstContent->ID;
						$firstContent->Save (); // published copy
						$pbid = $firstContent->ID;
						$firstContent->ID = false; 
						$firstContent->Save (); // work copy
						$cpid = $firstContent->ID;
						
						$contentData = new dbObject ( 'ContentDataBig', $sdb );
						$contentData->ContentID = $pbid;
						$contentData->ContentTable = 'ContentElement';
						$contentData->DataText = i18n( 'Welcome to ARENACM' );
						$contentData->Name = 'Hovedfelt';
						$contentData->Type = 'text';
						$contentData->IsVisible = '1';
						$contentData->AdminVisibility = '1';
						$contentData->ContentGroup = 'Felt1';
						$contentData->Save (); // published copy
						$contentData->ID = false;
						$contentData->ContentID = $cpid;
						$contentData->Save (); // work copy
						
						// Insert standard modules
						$a = 0; $mods = array ( 'extensions', 'library', 'users', 'settings' );
						foreach ( $mods as $m )
						{
							$mod = new dbObject ( 'ModulesEnabled', $cdb );
							$mod->SiteID = $site->ID;
							$mod->Module = $m;
							$mod->Load ();
							$mod->SortOrder = $a++;
							$mod->Save ();
						}
					}
					
					break;
			}
			
			if ( !$errors )
			{
				if ( !filesize( "$root/config.php" ) )
				{
					// Set config file
					$fp = fopen ( "$root/config.php", 'w+' );
					$str = '<?php
	' . ( isset( $config ) ? $config : '' ) . 
	'define( \'SITE_ID\', \'' . $_POST['siteID'] . '\' );
	define( \'NEWEDITOR\', \'true\' );
	// Mail setup --------------------------------------------------------------
	define( \'MAIL_TRANSPORT\', \'phpmailer\' );
	define( \'MAIL_FROMNAME\', \'' . $_POST['mailFromName'] . '\' );
	define( \'MAIL_REPLYTO\', \'' . $_POST['mailReplyTo'] . '\' );
	define( \'MAIL_SMTP_HOST\', \'' . $_POST['mailHost'] . '\' );
	define( \'MAIL_USERNAME\', \'' . $_POST['mailUsername'] . '\' );
	define( \'MAIL_PASSWORD\', \'' . $_POST['mailPassword'] . '\' );
?>';
					fwrite ( $fp, $str );
					fclose ( $fp );
				}
				
				if ( !filesize( "$root/lib/core_config.php" ) )
				{
					// Set core config file
					$fp = fopen ( "$root/lib/core_config.php", 'w+' );
					// Check if it's single og multi site
					if ( isset( $_POST['siteType'] ) && $_POST['siteType'] == 1 )
					{
						$str = '<?php
	$corebase->setUsername ( "' . $_POST['siteUsername'] . '" );
	$corebase->setPassword ( "' . $_POST['sitePassword'] . '" );
	$corebase->setHostname ( "' . $_POST['siteHostname'] . '" );
	$corebase->setDb ( "' . $_POST['siteDatabase'] . '" ); 
?>';
					}
					else
					{
						$str = '<?php
	$corebase->setUsername ( "' . $_POST['coreUsername'] . '" );
	$corebase->setPassword ( "' . $_POST['corePassword'] . '" );
	$corebase->setHostname ( "' . $_POST['coreHostname'] . '" );
	$corebase->setDb ( "' . $_POST['coreDatabase'] . '" ); 
?>';
					}
					fwrite ( $fp, $str );
					fclose ( $fp );
				}
				
				// Redirect to step 3
				header ( 'Location: admin.php' );
				die ( );
			}
			
			break;
		case '1':
		default:
			$tpl = new cPTemplate ( "$root/lib/templates/installer/step1.php" );
			break;
	}
}
else
{
	$tpl = new cPTemplate ( "$root/lib/templates/installer/error.php" );
	foreach ( $errors as $er )
	{
		$tpl->error .= '<p>' . $er . '</p>';
	}
}
die ( $tpl->render () );

?>
