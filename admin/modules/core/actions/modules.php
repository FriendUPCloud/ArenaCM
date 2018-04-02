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



if ( $_REQUEST[ 'SiteID' ] )
{
	$base =& dbObject::globalValue ( 'corebase' );
	
	$ModuleSettings = Array (
		Array ( 'settings', '0', 'Innstillinger', 'wrench.png' ),
		Array ( 'contents', '1', 'Innhold', 'sitemap.png' ),
		Array ( 'news', '2', 'Nyheter', 'newspaper.png' ),
		Array ( 'library', '3', 'Biblotek', 'images.png' ), 
		Array ( 'users', '4', 'Brukere', 'group.png' ), 
		Array ( 'extensions', '5', 'Utvidelser', 'plugin.png' )
	);
	
	foreach ( $ModuleSettings as $s )
	{
		if ( $_REQUEST[ $s[ 0 ] ] != '0' )
		{
			if ( !( $settings = $base->fetchObjectRow ( 'SELECT ID FROM ModulesEnabled WHERE ID=' . $_REQUEST[ $s[ 0 ] ] ) ) )
			{
				$base->query ( '
				INSERT INTO ModulesEnabled 
					( SiteID, Module, SortOrder, ModuleName, ModuleIcon ) 
				VALUES 
					( \'' . $_REQUEST[ 'SiteID' ] . '\', "' . $s[ 0 ] . '", ' . $s[ 1 ] . ', "' . $s[ 2 ] . '", "' . $s[ 3 ] . '" )
				' );
			}
		}
		else
		{
			$base->query ( 'DELETE FROM ModulesEnabled WHERE Module="' . $s[ 0 ] . '" AND SiteID=\'' . $_REQUEST[ 'SiteID' ] . '\'' );
		}
	}
}
ob_clean ( );
header ( 'Location: admin.php?module=core' );
die ( );
?>
