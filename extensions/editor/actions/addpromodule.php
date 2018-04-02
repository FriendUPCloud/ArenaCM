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

include_once ( 'extensions/editor/include/funcs.php' );
/**
 * Error checking
**/
if ( !trim ( $_REQUEST[ 'mod' ] ) )
{
	die ( 'fail<!-- separate -->Du må velge en modul som du ønsker å bruke.' );
}

/**
 * Add order to system
**/
$info = file_get_contents ( 'lib/skeleton/modules/' . $_REQUEST[ 'mod' ] . '/info.txt' );
$cols = explode ( '|', $info );
$corebase->query ( "
	INSERT INTO ProductOrder 
		( SiteID, ProductName, OrderText, PriceSum, Status, DateOrdered )
		VALUES
		( '{$siteData->ID}', \"{$_REQUEST['mod']}\", \"\", '{$cols[1]}', 'Pending', NOW() )
" );	
/** 
 * Add the bloody thing - commented out - arenacore will activate it
**/
ob_clean ( );
$mod = 'lib/skeleton/modules/' . $_REQUEST[ 'mod' ];
if ( file_exists ( $mod ) && is_dir ( $mod ) )
{
	$setting = new dbObject ( 'Setting' );
	$setting->SettingType = 'ContentModule';
	$setting->Key = $_REQUEST[ 'mod' ];
	$setting->load ( );
	$setting->save ( );
	list ( $info, ) = explode ( "\n", $info );
	list ( , ,$moduletype, ) = explode ( '|', $info );
	if ( $moduletype == 'adminmodule' )
	{
		die ( 'okreload<!-- separate -->' );
	}
	die ( 'ok<!-- separate -->' . showAddedModules ( $Session->EditorContentID ) );
}
die ( 'fail<!-- separate -->Det oppsto en feil: Modul mappen eksisterer ikke.' );
?>
