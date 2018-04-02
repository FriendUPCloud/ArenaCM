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



$db =& dbObject::globalValue ( 'corebase' );
$siteData =& dbObject::globalValue ( 'sitedata' );	

if ( $rows = $db->fetchRows ( 'SELECT * FROM ModulesEnabled WHERE SiteID=\'' . $siteData->ID . '\' ORDER BY SortOrder ASC, Module DESC' ) )
{
	$len = count ( $rows );
	for ( $a = 0; $a < $len; $a++ )
	{
		// Reorder items
		$rows[ $a ][ 'SortOrder' ] = $a;
		// Order items
		if ( $rows[ $a ][ 'Module' ] == $_REQUEST[ 'm' ] && $_REQUEST[ 'offset' ] == 'up' && $a > 0 )
		{
			$tmp = $rows[ $a ][ 'SortOrder' ];
			$rows[ $a ][ 'SortOrder' ] = $rows[ $a - 1 ][ 'SortOrder' ];
			$rows[ $a - 1 ][ 'SortOrder' ] = $tmp;
			// Save this and prev row
			$db->query ( 'UPDATE ModulesEnabled SET SortOrder=\'' . $rows[ $a - 1 ][ 'SortOrder' ] . '\' WHERE ID=\'' . $rows[ $a - 1 ][ 'ID' ] . '\' LIMIT 1' );
			$db->query ( 'UPDATE ModulesEnabled SET SortOrder=\'' . $rows[ $a ][ 'SortOrder' ] . '\' WHERE ID=\'' . $rows[ $a ][ 'ID' ] . '\' LIMIT 1' );
		}
		else if ( $rows[ $a ][ 'Module' ] == $_REQUEST[ 'm' ] && $_REQUEST[ 'offset' ] == 'down' && $a < count ( $rows ) - 1 )
		{
			$tmp = $rows[ $a ][ 'SortOrder' ];
			$rows[ $a ][ 'SortOrder' ] = $a + 1;
			$rows[ $a + 1 ][ 'SortOrder' ] = $tmp;
			// Save this and next row
			$db->query ( 'UPDATE ModulesEnabled SET SortOrder=\'' . $rows[ $a + 1 ][ 'SortOrder' ] . '\' WHERE ID=\'' . $rows[ $a + 1 ][ 'ID' ] . '\' LIMIT 1' );
			$db->query ( 'UPDATE ModulesEnabled SET SortOrder=\'' . $rows[ $a ][ 'SortOrder' ] . '\' WHERE ID=\'' . $rows[ $a ][ 'ID' ] . '\' LIMIT 1' );
			// Skip next row
			$a++;
		}
		else
		{
			// Save this row
			$db->query ( 'UPDATE ModulesEnabled SET SortOrder=\'' . $rows[ $a ][ 'SortOrder' ] . '\' WHERE ID=\'' . $rows[ $a ][ 'ID' ] . '\' LIMIT 1' );
		}
	}
}
ob_clean ( );
header ( 'Location: admin.php?module=core&settingsmodule=' . $_REQUEST[ 'm' ] );
die ( );
?>
