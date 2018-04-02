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
$sitedata =& dbObject::globalValue ( 'sitedata' );
$a = 0;
// Insert new
foreach ( $_REQUEST[ 'selectedmodules' ] as $m )
{
	if ( !$names[ $a ] ) $names[ $a ] = $m;
	if ( !( $row = $db->fetchRow ( 'SELECT * FROM ModulesEnabled WHERE SiteID=\'' . $sitedata->ID . '\' AND Module=\'' . $m . '\' LIMIT 1' ) ) )
	{
		$db->query ( 'INSERT INTO ModulesEnabled ( SiteID, Module, ModuleName ) VALUES ( \'' . $sitedata->ID . '\', "' . $m . '", "' . $names[ $a ] . '" )' );
	}
	$a++;
}
// Delete unselected
if ( $rows = $db->fetchRows ( 'SELECT * FROM ModulesEnabled WHERE SiteID=\'' . $sitedata->ID . '\'' ) )
{
	foreach ( $rows as $row )
	{
		$found = false;
		foreach ( $_REQUEST[ 'selectedmodules' ] as $m )
		{
			if ( $m == $row[ 'Module' ] )
			{
				$found = true; break;
			}
		}
		if ( $found ) continue;
		$db->query ( 'DELETE FROM ModulesEnabled WHERE SiteID=\'' . $sitedata->ID . '\' AND Module="' . $row[ 'Module' ] . '" LIMIT 1' );
	}
}
ob_clean ( );
header ( 'Location: admin.php?module=core' );
die ( );
?>
