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

include_once ( $extdir . '/include/funcs.php' );
ob_clean ( );
$target = new dbObject ( 'ContentElement' );
if ( $target->load ( $_REQUEST[ 'target' ] ) )
{
	while ( ( $newtarget = $target->findSingle ( 'SELECT * FROM ContentElement WHERE ID != MainID AND MainID = ' . $target->Parent ) ) )
	{
		$target = $newtarget;
		if ( $target->MainID == $_REQUEST[ 'cid' ] )
			die ( 'fail<!-- separate -->Du kan ikke flytte en side til en underside av seg selv.' );
	}
	$target->load ( $_REQUEST[ 'target' ] );
	$org = new dbObject ( 'ContentElement' );
	$org->load ( $_REQUEST[ 'cid' ] );
	$org->Parent = $target->MainID;
	$org->save ( );
	$option = new dbObject ( 'Setting' );
	$option->SettingType = 'ArenaContentExtension';
	$option->Key = 'SortOrderChanged';
	$option->load ( );
	$option->Value = 1;
	$option->save ( );
	die ( 'ok<!-- separate -->' . renderHierarchyOptions ( $org->ID ) );
}
else
{
	die ( 'fail<!-- separate -->Kan ikke laste inn siden du ønsker å flytte til.' );
}
?>
