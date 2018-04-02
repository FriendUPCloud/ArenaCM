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

require_once ( 'lib/classes/dbObjects/dbContent.php' );
require_once ( 'extensions/easyeditor/include/modulefuncs.php' );
$db =& dbObject::globalValue ( 'database' );
$p = new dbContent ( );
if ( $p->load ( $_REQUEST[ 'pageid' ] ) )
{
	if ( $dir = opendir ( 'lib/skeleton/modules' ) )
	{
		while ( $file = readdir ( $dir ) )
		{
			if ( $file{0} == '.' ) continue;
			if ( substr ( $file, 0, 4 ) == 'mod_' )
			{
				$info = file_get_contents ( 'lib/skeleton/modules/' . $file . '/info.txt' );
				$info = explode ( '|', $info );
				
				// Ahh simple is a module that can be used by easyeditor
				// now we can try to deactivate this one
				if ( trim( $info[3] ) == 'simple' )
				{
					// Is it active
					if ( GetSettingValue ( $file, $p->ID ) == 1 )
					{
						// Ok, remove the field
						$db->query ( '
							DELETE FROM `ContentDataSmall` 
							WHERE 
								`ContentTable`="ContentElement" AND 
								`ContentID`=\'' . $p->ID . '\' AND 
								`AdminVisibility`=\'1\' AND `IsVisible`=\'1\' AND 
								`ContentGroup`=\'Felt1\' AND `DataString`=\'' . $file . '\'
						' );
						
						// Set the setting that the module is inactive
						SetSetting ( $file, $p->ID, '0' );
						
						// Reactivate replaced field (which is in db)
						$obj = new dbObject ( 'Setting' );
						if ( $obj = $obj->findSingle ( 'SELECT * FROM `Setting` WHERE SettingType="' . $file . '_replaced" AND `Key`=\'' . $p->ID . '\'' ) )
						{
							$data = explode ( '_', $obj->Value );
							
							// Load field
							$fld = new dbObject ( $data[1] );
							$fld->load ( $data[0] );
							$fld->AdminVisibility = '1';
							$fld->IsVisible = '1';
							$fld->save ();
						}
					}
				}
			}
		}
		closedir ( $dir );
	}
	$db->query ( 'DELETE FROM `Setting` WHERE `Key`=' . $p->ID . ' AND SettingType LIKE "%_replaced"' );
}
ob_clean ( );
header ( 'Location: admin.php?module=extensions&extension=easyeditor' );
die ();
?>
