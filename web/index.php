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
 * Include site config
**/
if( file_exists( 'config' ) && is_dir( 'config' ) && file_exists( 'config/config.php' ) )
{
	include_once( 'config/config.php' );
}
else
{
	include_once( 'config.php"' );
}

$GLOBALS[ 'LoadTime' ] = microtime(true);


/**
 * Include api if defined in config
**/
if ( defined( 'API' ) && ( isset( $_REQUEST['route'] ) && preg_match ( '/api-([a-z0-9]*?)\//i', $_REQUEST['route'], $matches ) || isset( $_SERVER['REQUEST_URI'] ) && preg_match ( '/\/sitemap.xml/i', $_SERVER['REQUEST_URI'], $matches ) ) )
{
	require ( API ); die();
}
// TODO: Remove this later when old API requests are updates to the new path /api-*?/
else if( defined( 'API' ) && isset( $_REQUEST['route'] ) && preg_match ( '/(plugins|documentation|information|connect|authenticate|secure-files|parse|components)\//i', $_REQUEST['route'], $matches ) )
{
	require ( API ); die();
}

/**
 * Defaults
**/
define ( 'NL', "\n" );
define ( 'TAB', "\t" );
define ( 'ARENAMODE', 'web' );

/**
 * Include site config
**/
session_start ( );
include_once ( 'config.php' );
header ( 'Cache-Control: public' );

/**
 * Prerequisites
**/
include_once ( 'lib/functions/functions.php' );
include_once ( 'lib/classes/database/cdatabase.php' );
include_once ( 'lib/classes/dbObjects/dbObject.php' );
include_once ( 'lib/classes/dbObjects/dbContent.php' );
include_once ( 'lib/classes/dbObjects/dbUser.php' );
include_once ( 'lib/classes/template/cPTemplate.php' );
include_once ( 'lib/classes/template/cDocument.php' );
include_once ( 'lib/classes/session/session.php' );

/**
 * Setup the core database
**/
$corebase = new cDatabase ( );
include_once ( 'lib/lib.php' );
if( file_exists( 'config' ) && is_dir( 'config' ) && file_exists( 'config/core_config.php' ) )
{
	include_once( 'config/core_config.php' );
}
else
{
	include_once ( 'lib/core_config.php' );
}
$corebase->Open ( );
dbObject::globalValueSet ( 'corebase', $corebase );

/**
 * Setup the site database
**/
if ( !( $siteData = $corebase->fetchObjectRow ( 'SELECT * FROM `Sites` WHERE `SiteName`="' . SITE_ID . '"' ) ) )
{
	if ( file_exists ( 'install.php' ) )
		include ( 'install.php' );
	ArenaDie( 'Failed to initialize site: ' . SITE_ID );
}

// Set basedir and baseurl dynamic
if( $siteData->BaseUrl && !strstr( $siteData->BaseUrl, 'http' ) )
{
	$siteData->BaseUrl = ( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://' ) . $siteData->BaseUrl;
}
else if( !$siteData->BaseUrl )
{
	$siteData->BaseUrl = ( !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https://' : 'http://' ) . str_replace ( 
		array ( 'index.php', 'admin.php' ), 
		'', 
		$_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] 
	);
}
if( !$siteData->BaseDir )
{
	$siteData->BaseDir = getcwd ();
}

define ( 'BASE_DIR', $siteData->BaseDir );

$database = new cDatabase ( );
$database->setUsername ( $siteData->SqlUser );
$database->setPassword ( $siteData->SqlPass );
$database->setHostname ( $siteData->SqlHost );
$database->setDb ( $siteData->SqlDatabase );
$database->Open ( );
$userbase =& $database; // <- Compatability with ARENA1, userbase is used

dbObject::globalValueSet ( 'sitedata', $siteData );
dbObject::globalValueSet ( 'database', $database );

/**
 * Session variables
**/
$Session = new Session ( $siteData->ID . 'web' );
$GLOBALS[ 'Session' ] =& $Session;

/**
 * Language
 * A site must always have one default language installed to
 * work!
**/
$langtest = new dbObject ( 'Languages' );
$langtest->addClause ( 'WHERE', '( UrlActivator IS NOT NULL && UrlActivator != "" )' );
if ( $langtest->findCount ( ) )
	$Session->HasUrlActivator = true;

if ( isset( $_REQUEST[ 'setlang' ] ) && is_numeric ( $_REQUEST[ 'setlang' ] ) )
{
	$lang = new dbObject ( 'Languages' );
	if ( $lang->load ( $_REQUEST[ 'setlang' ] ) )
	{
		$Session->CurrentLanguage = $lang->ID;
		$Session->LanguageCode = $lang->Name;
	}
	else $Session->CurrentLanguage = 0;
}
else if ( !$Session->HasUrlActivator && !defined( 'LANGUAGES_ONE_PAGE_STRUCTURE' ) && isset( $_GET[ 'route' ] ) )
{
	list ( $langcode, ) = explode ( '/', $_GET[ 'route' ] );
	if ( $langcode )
	{
		$lang = new dbObject ( 'Languages' );
		$lang->addClause ( 'WHERE', 'Name="' . $langcode . '"' );
		if ( $lang = $lang->findSingle ( ) ) 
		{
			$Session->CurrentLanguage = $lang->ID;
			$Session->LanguageCode = $lang->Name;
		}
	}
}
else if ( $Session->HasUrlActivator )
{
	$search = $_SERVER[ 'SERVER_NAME' ];
	$lang = new dbObject ( 'Languages' );
	$lang->addClause ( 'ORDER BY', 'IsDefault DESC' );
	$lang->addClause ( 'WHERE', 'UrlActivator LIKE "%' . $search. '"' );
	if ( $lang = $lang->findSingle ( ) )
	{
		$Session->CurrentLanguage = $lang->ID;
		$Session->LanguageCode = $lang->Name;
		if ( $lang->BaseUrl )
			$Session->BaseUrl = $lang->BaseUrl;
	}
}
if ( !$Session->CurrentLanguage || !$Session->LanguageCode )
{
	if ( !isset( $lang ) || !$lang )
	{
		$lang = new dbObject ( 'Languages' );
		$lang->addClause ( 'WHERE', 'IsDefault=\'1\'' );
		$lang = $lang->findSingle ( );
		if ( !$lang )
		{
			$lang = new dbObject ( 'Languages' );
			$lang->Name = 'no';
			$lang->NativeName = 'Norsk';
			$lang->IsDefault = '1';
			$lang->save ( );
		}
	}
	if ( $lang->UrlActivator ) $Session->HasUrlActivator = true;
		
	if ( $lang->BaseUrl )
	{
		$Session->CurrentLanguage = $lang->ID;
		$Session->BaseUrl = $lang->BaseUrl;
	}
	$Session->CurrentLanguage = $lang->ID;
	$Session->LanguageCode = $lang->Name;
}

/**
 * Base url
**/
if ( !$Session->BaseUrl ) $Session->BaseUrl = $siteData->BaseUrl;
define ( 'BASE_URL', $Session->BaseUrl );

/**
 * Make sure we're on the correct domain
**/
if ( isset( $_SERVER[ 'HTTPS' ] ) && $_SERVER[ 'HTTPS' ] )
	$http = 'https://';
else $http = 'http://';
list ( $burl, ) = explode ( '/', str_replace ( array ( 'https://', 'http://' ), '', BASE_URL ) );
if ( $_SERVER[ 'HTTP_HOST' ] != $burl )
{
    header ( 'Location: ' . $http . str_replace ( array ( 'https://', 'http://' ), '', BASE_URL ) );
	die ( );
}

/**
 * Web User
**/
$webuser = new dbUser ( );
$webuser->authenticate ( 'web', isset( $Session->SessionPrefix ) ? $Session->SessionPrefix : null );
if ( isset( $_REQUEST[ 'logout' ] ) && $_REQUEST[ 'logout' ] && is_object ( $webuser ) ) // This should be POST request only, and check HOST header #VULN0002 - Low
{
	$webuser->logout ( );
	ob_clean ( );
	list ( $url, ) = explode ( '?', $_SERVER[ 'REQUEST_URI' ] );
	header ( 'Location: ' . $url );
	die ( );
}
if ( !$webuser->is_authenticated ) $webuser = false;
dbObject::globalValueSet ( 'webuser', $webuser );

/** 
 * Check mode
**/

if( strstr( $_SERVER['REQUEST_URI'], '/template-css/' ) )
{
	$_REQUEST['mode'] = 'templatecss';
	$_REQUEST['filename'] = str_replace( '/template-css/', '', $_SERVER['REQUEST_URI'] );
}

switch ( isset( $_REQUEST[ 'mode' ] ) ? $_REQUEST[ 'mode' ] : null )
{
	case 'redirect':
		if ( isset( $_REQUEST['url'] ) )
		{
			header( 'HTTP/1.1 301 Moved Permanently' );
			header( 'Location: ' . ( BASE_URL . $_REQUEST['url'] ) );
			exit();
		}
		else if ( isset( $_REQUEST['geturl'] ) )
		{
			die( trim( BASE_URL . $_REQUEST['geturl'] ) );
		}
		die( '404' );
	case 'templatecss':
		if ( !strstr( $_REQUEST['filename'], '..' ) )
		{
			include_once( 'lib/functions/functions.php' );
			header ( 'Content-type: text/css' );
			if ( file_exists ( 'upload/template/css/' . $_REQUEST['filename'] ) )
			{
				die( ParseCssFile( file_get_contents( 'upload/template/css/' . $_REQUEST['filename'] ) ) );
			}
			else if( file_exists( $_REQUEST['filename'] ) && substr( $_REQUEST['filename'], -4, 4 ) == '.css' )
			{
				die( ParseCssFile( file_get_contents( $_REQUEST['filename'] ), $_REQUEST['filename'] ) );
			}
			else if( strstr( $_REQUEST['filename'], '.css' ) && strstr( $_REQUEST['filename'], ';' ) )
			{
				die( ParseCssFile( false, $_REQUEST['filename'] ) );
			}
		}
		die ( '404' );
	case 'securefile':
		session_destroy ();
		session_name ( 'arenaadmin' );
		session_start ();
		$adminUser =& $_SESSION[ SITE_ID . 'adminAdminUser' ];
		if ( $adminUser->Username && ( $adminUser->IsAdmin || $adminUser->_dataSource == 'core' ) )
		{
			$path = 'secure/' . $_REQUEST['filename']; // Arbitrary file download and directory traversal!! #VULN0001 - High
			if ( file_exists( $path ) )
			{
				header ( 'Content-type: application/octet-stream;' );
				die ( file_get_contents ( $path ) );
			}
		}
		header ( 'HTTP/1.0 404 Not Found' );
		die ();
		break;
	case 'image':
		include_once ( 'lib/classes/dbObjects/dbImage.php' );
		$img = new dbImage ( );
		$img->load ( $_REQUEST[ 'iid' ] );
		
		if ( strstr ( strtolower ( $_REQUEST[ 'filename' ] ), '.png' ) )
			$img->setOutputMode ( 'PNG' );
		else if ( strstr ( strtolower ( $_REQUEST[ 'filename' ] ), '.gif' ) )
			$img->setOutputMode ( 'GIF' );
			
		$width = $_REQUEST[ 'width' ];
		$height = $_REQUEST[ 'height' ];
		$scalemode =  ( $_REQUEST[ 'scalemode' ] != '0' && isset ( $_REQUEST[ 'scalemode' ] ) ) ? $_REQUEST[ 'scalemode' ] : false;
		$effects =  ( $_REQUEST[ 'effects' ] != '0' && isset ( $_REQUEST[ 'effects' ] ) ) ? urldecode ( $_REQUEST[ 'effects' ] ) : false;
		$bgcolor =  ( $_REQUEST[ 'bgcolor' ] != '0' && isset ( $_REQUEST[ 'bgcolor' ] ) ) ? $_REQUEST[ 'bgcolor' ] : false;
		
		// Try to do it the hard way if regexp can't handle it....
		if ( !$effects )
		{
			list ( ,,$effects ) = explode ( '/', $_REQUEST[ 'filename' ] );
			list ( ,,,$effects, ) = explode ( '_', $effects );
		}
		$img->setBackgroundColor ( string2hex ( $bgcolor ) );
		
		ob_clean ( );
		$url = BASE_DIR . '/' . str_replace ( BASE_URL, '', $img->getImageUrl ( $width, $height, $scalemode, $effects ) );
		
		if ( $fp = fopen ( $url, 'r' ) )
		{
			header ( 'Pragma: max-age=86400' );
			$data = fread ( $fp, filesize ( $url ) );
			fclose ( $fp );
			switch ( $img->_mode )
			{
				case 'gif':
					header ( 'Content-type: image/gif' );
					break;
				case 'png':
					header ( 'Content-type: image/png' );
					break;
				default:
					header ( 'Content-type: image/jpeg' );
					break;
			}
			echo ( $data );
		}
		die ();
	
	default:
		// We will not be delivering images in this mode
		if ( !strstr ( $_SERVER[ 'REQUEST_URI' ], 'secure-files' ) &&
			!strstr ( $_SERVER[ 'REQUEST_URI' ], '?' ) && ( isset( $_REQUEST[ 'route' ] ) && (
			stristr ( $_REQUEST[ 'route' ], '.png' ) ||
			stristr ( $_REQUEST[ 'route' ], '.gif' ) ||
			stristr ( $_REQUEST[ 'route' ], '.jpg' )
		) ) )
		{
			ob_clean ( );
			header ( 'HTTP/1.0 404 Not Found' );
			die ( '404. Unimplemented response.' );
		}
		header ( 'Content-type: text/html; charset=utf-8' );
		
		// Setup content group rules
		if ( $rows = $database->fetchRows ( '
			SELECT * FROM `Setting` WHERE `SettingType` = "Layout" AND `Key` = "Table"
		' ) )
		{
			$TableLayout = array ();
			foreach ( $rows as $row )
			{
				if ( $here = explode ( "\t", $row['Value'] ) )
				{
					if ( trim ( $here[0] ) && trim ( $here[1] ) && $here[0] != '-' && $here[1] != '.' )
					{
						$TableLayout[$here[0]]=$here[1];
					}
				}
			}
		}
		
		$page = new dbContent ();
		$document = new cDocument ();
		if ( !$page->ID )
		{
			// Test for javascript request (no 404 for missing .js)
			if ( isset( $_REQUEST[ 'route' ] ) && substr ( $_REQUEST[ 'route' ], -3, 3 ) == '.js' ) 
				die ( '// Javascript not found' );

			// If we fail to grab page by path
			if ( !( $page = $page->getByPath ( isset( $_REQUEST[ 'route' ] ) ? $_REQUEST[ 'route' ] : null ) ) )
			{
				// Try to find an older page location and redirect
				$obj = new dbObject ( 'ContentRoute' );
				$obj->Route = $_REQUEST[ 'route' ];
				$obj->ElementType = 'ContentElement';
				if ( $obj->load ( ) )
				{
					$page = new dbContent ( );
					$page->load ( $obj->ElementID );
					ob_clean ( );
					header ( 'Location: ' . BASE_URL . $page->getPath ( ) );
					$page = false;
				}
				else
				{
					// Try to fetch a parentpage if it is an extension
					$page = new dbContent ( );
					list ( $route, ) = explode ( '?', $_REQUEST[ 'route' ] );
					$route = explode ( '/', str_replace ( '/index.html', '', $route ) );
					$route = implode ( '/', $route ) . '/index.html';
					
					if ( ( $page = $page->getByPath ( $route ) ) && $page->ContentType == 'extensions' )
					{
						// Dupe #1
						$config = explode ( "\n", $page->Intro );
						foreach ( $config as $c )
						{
							list ( $e, $v ) = explode ( "\t", $c );
							if ( $e == 'ExtensionName' )
								$page->extension = $v;
						}
						if ( file_exists ( 'extensions/' . $page->extension . '/webmodule_preparse.php' ) )
							include_once ( 'extensions/' . $page->extension . '/webmodule_preparse.php' );
					}
					else
					{
						// Just end up with 404 - page is totally deleted and/or non existant
						$document->load ( $document->findTemplate ( '404.php', array ( 'templates/', 'web/templates/' ) ) );
						echo $document->render ( );
						$page = false;
					}
				}
			}
			else
			{
				if ( $page->ContentType == 'extensions' )
				{
					// Dupe #2
					$config = explode ( "\n", $page->Intro );
					foreach ( $config as $c )
					{
						list ( $e, $v ) = explode ( "\t", $c );
						if ( $e == 'ExtensionName' )
						{
							$page->extension = $v;
							break;
						}
					}
					if ( file_exists ( 'extensions/' . $page->extension . '/webmodule_preparse.php' ) )
						include_once ( 'extensions/' . $page->extension . '/webmodule_preparse.php' );
				}
			}
		}
		if ( $page )
		{
			if ( !$page->IsPublished )
			{
				$document->load ( $document->findTemplate ( 'page_not_published.php', array ( 'templates/', 'web/templates/' ) ) );
				$document->page =& $page;
				$parentPage = new dbContent ( );
				$parentPage->load ( $page->Parent );
				$document->parentPage =& $parentPage;
			}
			else
			{
				// If the content type is a link, redirect and stop output
				if ( $page->ContentType == 'link' ) 
				{
					if ( substr ( $page->Link, 0, 7 ) != 'http://' )
					{
						$page->Link = BASE_URL . $page->Link;
					}
					header ( 'Location: ' . $page->Link );
					die ();
				}
				
				$page->loadExtraFields ( array ( 'OnlyPublished'=>true ) );
				$access = true;
				if ( $page->IsProtected )
				{
					// Check global permissions if no user is logged in
					if ( !( $webuser = dbObject::globalValue ( 'webuser' ) ) )
						$access = dbUser::checkGlobalPermission ( $page, 'Read' );
					else if ( !$webuser->checkPermission ( $page, 'Read' ) )
						$access = false;
				}
				if ( !$access )
				{
					// Check for override to access_denied.php thingie
					$setting = new dbObject ( 'Setting' );
					$setting->SettingType = 'ContentsProtectedSymlink';
					$setting->Key = $page->ID;
					if ( $setting->load ( ) )
					{
						$page = new dbContent ( );
						$page->load ( $setting->Value );
						if ( $page->Template )
						{
							$document->load ( 'templates/' . $page->Template );
						}
						else 
						{
							$document->_templateFilename = false;
						}
						if ( !$document->_templateFilename )
						{
							$document->load ( $document->findTemplate ( 'page.php', array ( 'templates/', 'web/templates/' ) ) );
						}
						$document->page =& $page;
					}
					else
					{
						$document->load ( $document->findTemplate ( 'access_denied.php', array ( 'templates/', 'web/templates/' ) ) );
						$document->page =& $page;
					}
					$parentPage = new dbContent ( $page->Parent );
					$document->parentPage =& $parentPage;
				}
				else
				{
					if( trim( $page->Template ) && file_exists( $page->Template ) )
						$document->load( $page->Template );
					else $document->load ( ( $page->Template ? ( 'templates/' . $page->Template ) : 'web/templates/page.php' ) );
					$document->page =& $page;
				}
				if ( defined ( 'DOCTYPE' ) )
				{
					$document->xmlns = defined ( 'DOCTYPE_XMLNS' ) ? DOCTYPE_XMLNS : '';
					$document->doctype = '<!DOCTYPE ' . DOCTYPE . ">\n";
					$document->docinfo = defined ( 'DOCTYPE_INFO' ) ? ( DOCTYPE_INFO . "\n" ) : '';
				}
				else
				{
					$document->xmlns = ' xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"';
					$document->doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
					$document->docinfo = '<' . '?xml version="1.0"?' . '>'."\n";
				}
				if ( defined ( 'DOCTYPE_OVERRIDE' ) )
				{
					$document->xmlns = '';
					$document->doctype = DOCTYPE_OVERRIDE;
					$document->docinfo = '';
				}
			}
			switch ( $_REQUEST[ 'arenamode' ] )
			{
				// Flashmode only outputs content in XML
				case 'flash':
				case 'xml':
					if ( $_REQUEST[ 'encoding' ] )
						$document->_encoding = $_REQUEST[ 'encoding' ];
					echo $document->renderFlashXML ();
					break;
				case 'objectinfo':
					if ( $_REQUEST[ 'encoding' ] )
						$document->_encoding = $_REQUEST[ 'encoding' ];
					if ( $img = dbObject::get ( $_REQUEST[ 'id' ], $_REQUEST[ 'type' ] ) )
					{
						header ( 'Content-type: text/xml' );
						die ( '<' . '?xml version="1.0" charset="utf-8"?' . '>
<xml>
<element type="' . $_REQUEST[ 'type' ] . '" src="' . BASE_URL . 'upload/images-master/' . $img->Filename . '" name="' . $img->Title . '" id="' . $img->ID . '">
	<![CDATA[' . $img->Description . ']]>
</element>
</xml>' );
					}
					break;
				default:
					$db =& $page->getDatabase ( ); 
					$db->query ( 'UPDATE ContentElement SET SeenTimes = \'' . ( ( int )$page->SeenTimes + 1 ) . '\' WHERE ID=\'' . $page->MainID . '\' OR MainID=\'' . $page->MainID . '\'' );
					if ( !$Session->VersionCheck )
					{
						$Session->Set ( 'VersionCheck', 1 );
						include_once ( 'admin/include/sanitize_upgrade.php' );
						if ( !file_exists ( 'upload/index.html' ) )
						{
							if ( $f = fopen ( 'upload/index.html', 'w+' ) )
							{
								fwrite ( $f, ' ' );
								fclose ( $f );
							}
						}
					}
					if ( !( $cs =& $Session->Get ( 'ContentStatistics' ) ) )
						$cs = array ( );
					if ( !$cs[ $page->MainID ] )
					{
						$cs[ $page->MainID ] = 1;
						$db->query ( 'UPDATE ContentElement SET SeenTimesUnique = \'' . ( ( int )$page->SeenTimesUnique + 1 ) . '\' WHERE ID=\'' . $page->MainID . '\' OR MainID=\'' . $page->MainID . '\'' );
					}
					$Session->Set ( 'ContentStatistics', $cs );
					$document->LanguageCode = $Session->LanguageCode;
					//die( print_r( $document,1 ) . ' --' );
					echo $document->render ( );
					break;
			}
		}
		break;
}


/**
 * Close database
 */
$database->Close ( );
$corebase->Close ( );

?>
