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

// Activating a NORMAL/FREE module
$cnt = new dbContent ( );
if ( $cnt->load ( $_REQUEST[ 'cid' ] ) )
{
	// Get info about this module and do some actions
	// based on its type
	$info = 'lib/skeleton/modules/' . $_REQUEST[ 'mod' ] . '/info.txt';
	if ( file_exists ( $info ) )
	{
		if ( $info = file_get_contents ( $info ) )
		{
			list ( $info, $desc ) = explode ( "\n", $info );
			list ( $name, $price, $moduletype ) = explode ( '|', $info );
		}
	}
	$groups = explode ( ',', $cnt->ContentGroups );
	foreach ( $groups as $k=>$v )
		$groups[ $k ] = trim ( $v );
		
	$extra = new dbObject ( 'ContentDataSmall' );
	$extra->ContentID = $cnt->ID;
	$extra->ContentTable = 'ContentElement';
	$extra->Type = 'contentmodule';
	$extra->Name = str_replace ( ' ', '_', trim ( $name ? $name : $_REQUEST[ 'mod' ] ) );
	$extra->DataString = $_REQUEST[ 'mod' ];
	
	if ( !$extra->load ( ) )
	{
		$db =& $cnt->getDatabase ( );
		list ( $max, ) = $db->fetchRow ( 'SELECT MAX(SortOrder) FROM ContentDataSmall WHERE ContentTable="ContentElement" AND ContentID=' . $cnt->ID );
		$max++;
		if ( !in_array ( 'Felt1', $groups ) )
			$extra->ContentGroup = $groups[ 1 ] ? $groups[ 1 ] : $groups[ 0 ];
		else $extra->ContentGroup = 'Felt1';
		$extra->IsVisible = '1';
		$extra->SortOrder = $max;
		$extra->save ( );
		die ( 'ok' );
	}
	die ( 'already' );
}	
die ( 'fail' );
?>
