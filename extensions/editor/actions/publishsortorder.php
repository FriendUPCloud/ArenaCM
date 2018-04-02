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

$objs = new dbObject ( 'ContentElement' );
$objs->addClause ( 'WHERE', '!IsTemplate AND !IsDeleted AND MainID != ID' );
if ( $objs = $objs->find ( ) )
{
	$db =& $objs[ 0 ]->getDatabase ( );
	foreach ( $objs as $obj )
	{
		// Update sort order
		$db->query ( 'UPDATE ContentElement SET SortOrder=' . $obj->SortOrder . ' WHERE ID=' . $obj->MainID );
		
		// Also update moves (parent/child relations)
		$par = new dbObject ( 'ContentElement' );
		$par->load ( $obj->Parent );
		$db->query ( 'UPDATE ContentElement SET Parent=' . $par->MainID . ' WHERE ID=' . $obj->MainID );
	}
	$option = new dbObject ( 'Setting' );
	$option->SettingType = 'ArenaContentExtension';
	$option->Key = 'SortOrderChanged';
	$option->load ( );
	$option->Value = 0;
	$option->save ( );
	die ( 'ok' );
}
die ( 'fail' );
?>
