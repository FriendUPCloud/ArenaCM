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

// Hook for when publishing (contains files)
if ( !$Session->EditorPublishHooks )
	$Session->Set ( 'EditorPublishHooks', array () );
$GLOBALS[ 'PublishHooks' ] =& $Session->EditorPublishHooks; 
$PublishHooks = &$GLOBALS[ 'PublishHooks' ];

i18nAddLocalePath ( 'extensions/easyeditor/locale' );
include_once ( 'extensions/easyeditor/include/i18n_javascript.php' );

// Setup default content -------------------------------------------------------
include_once ( 'lib/classes/dbObjects/dbContent.php' );
if ( $_REQUEST[ 'cid' ] )
	$Session->Set ( 'EditorContentID', $_REQUEST[ 'cid' ] );
$page = new dbContent ( );
$content =& $page;
if ( !( $page->load ( $Session->EditorContentID ) ) )
{
	$Session->Del ( 'EditorContentID' );
	$page = new dbObject ( 'ContentElement' );
	$page->addClause ( 'WHERE', 'Parent=0' );
	$page->addClause ( 'WHERE', '!IsDeleted AND !IsTemplate' );
	$page->addClause ( 'WHERE', 'ID != MainID' );
	$page = $page->findSingle ();
	$Session->Set ( 'EditorContentID', $page->ID );
}

// Do functions and actions ----------------------------------------------------
$function = $_REQUEST[ 'function' ] ? 
	"functions/{$_REQUEST['function']}.php" : 'functions/main.php';
$action = $_REQUEST[ 'action' ] ? 
	"actions/{$_REQUEST['action']}.php" : false;

if ( $action && file_exists ( "extensions/easyeditor/$action" ) )
	include_once ( "extensions/easyeditor/$action" );
if ( file_exists ( "extensions/easyeditor/$function" ) ) 
	include_once ( "extensions/easyeditor/$function" );
?>
