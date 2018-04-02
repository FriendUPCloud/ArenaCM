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
 * Simple content editor for ARENA2
**/
global $document, $Session;

// Hook for when publishing (contains files)
if ( !$Session->EditorPublishHooks )
	$Session->Set ( 'EditorPublishHooks', array () );
$GLOBALS[ 'PublishHooks' ] =& $Session->EditorPublishHooks; 
$PublishHooks = &$GLOBALS[ 'PublishHooks' ];

if ( strstr ( strtolower ( $_SERVER[ 'HTTP_USER_AGENT' ] ), 'msie' ) )
	$document->addResource ( 'stylesheet', 'extensions/editor/css/msie.css' );
include_once ( 'lib/classes/dbObjects/dbContent.php' );
i18nAddLocalePath ( 'extensions/editor/locale' );
include_once ( 'extensions/editor/include/i18njavascript.php' );

if ( strstr ( $_SERVER[ 'HTTP_USER_AGENT' ], 'MSIE 7' ) )
	$document->addResource ( 'stylesheet', 'extensions/editor/css/ie7.css' );
function safeFieldName ( $string )
{
	$allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890_';
	$out = '';
	for ( $a = 0; $a < strlen ( $string ); $a++ )
	{
		$found = false;
		for ( $b = 0; $b < strlen ( $allowed ); $b++ )
		{
			if ( $string{$a} == $allowed{$b} )
			{
				$found = true;
				break;
			}
		}
		if ( $found )
			$out .= $string{$a};
		else $out .= '_';
	}
	return $out;
}

// Trap some bajax events 
if ( $_GET[ 'bajaxrand' ] && $_GET[ 'module' ] != 'extensions' )
{
	if ( file_exists ( 'lib/skeleton/modules/' . $_GET[ 'module' ] . '/module.php' ) )
		include_once ( 'lib/skeleton/modules/' . $_GET[ 'module' ] . '/module.php' );
	die ( );
}

/* Register content id */
if ( $_REQUEST[ 'cid' ] )
	$Session->set ( 'EditorContentID', $_REQUEST[ 'cid' ] );

/* Register language */
if ( $_REQUEST[ 'languageid' ] )
{
	$lang = new dbObject ( 'Languages' );
	if ( $lang->load ( $_REQUEST[ 'languageid' ] ) )
	{
		$Session->Set ( 'CurrentLanguage', $lang->ID );
		$Session->Set ( 'LanguageCode', $lang->Name );
		$Session->Set ( 'Language', $lang );
	}
}
else if ( !$Session->CurrentLanguage )
{
	$lang = new dbObject ( 'Languages' );
	$lang->IsDefault = '1';
	if ( $lang = $lang->findSingle () )
	{
		$Session->Set ( 'CurrentLanguage', $lang->ID );
		$Session->Set ( 'LanguageCode', $lang->Name );
		$Session->Set ( 'Language', $lang );
	}
}

/* Double check that we have content from current language */
if ( $Session->EditorContentID )
{
	$cnt = new dbContent ( $Session->EditorContentID );
	if ( $cnt && $cnt->Language != $Session->CurrentLanguage )
		$Session->EditorContentID = false;
}
if ( !$Session->EditorContentID || $_REQUEST[ 'languageid' ] )
{
	$cnt = new dbContent ( );
	$cnt->addClause ( 
		'WHERE', 
		'MainID != ID AND !IsTemplate AND ' . 
		'!IsDeleted AND `Parent`=0 AND `Language`=' . $Session->CurrentLanguage 
	);
	// If no such content exists, create it
	if ( !( $cnt = $cnt->findSingle ( ) ) )
		include_once ( 'extensions/editor/include/create_languagerootpage.php' );
	$Session->EditorContentID = $cnt->ID;
}

/* Vars */
$extdir = 'extensions/editor/';
$document->addHeadScript ( $extdir . 'javascript/main.js' );
$document->addResource ( 'stylesheet', $extdir . 'css/main.css' );

/* Check query */
$function = 'main';
if ( $_REQUEST[ 'function' ] )
	$function = $_REQUEST[ 'function' ];
$action = false;
if ( $_REQUEST[ 'action' ] )
	$action = $_REQUEST[ 'action' ];
if ( $action && file_exists ( $extdir . 'actions/' . $action . '.php' ) )
	include_once ( $extdir . 'actions/' . $action . '.php' );
if ( $function )
{
	$tpl = new cPTemplate ( $extdir . 'templates/' . $function . '.php' );
	include_once ( $extdir . 'functions/' . $function . '.php' );
}

/* Output and set */
$extension .= $tpl->render ( );
?>
