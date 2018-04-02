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
 * Initialize the plugin
**/
include_once ( "lib/functions/functions.php" );
include_once ( "lib/plugins/library/include/funcs.php" );
include_once ( "lib/classes/dbObjects/dbImage.php" );
include_once ( "lib/classes/dbObjects/dbFile.php" );

$rootFolder = dbFolder::getRootFolder ( );

if ( intval ( $_REQUEST[ 'lid' ] ) > 0 )
	$Session->Set ( 'pluginLibraryLevelID', $_REQUEST[ 'lid' ] );

$currentFolder = new dbFolder ( $Session->pluginLibraryLevelID );

if ( intval ( $currentFolder->ID ) <= 0 )
{
	$Session->Set ( 'pluginLibraryLevelID', $rootFolder->ID );
	$currentFolder = $rootFolder;
}

$tpl = new cPTemplate ( "lib/plugins/library/templates/plugin_gui.php" );
$tpl->FileLevels = generatePluginLevelOptions ( $rootFolder, $currentFolder->ID );
$tpl->FileLevelTree = generatePluginLevelTree ( $rootFolder, $currentFolder->ID );
$tpl->ContentType = $_REQUEST[ "type" ];
$tpl->ContentID = $_REQUEST[ "id" ];
die ( $tpl->render () );	
?>
