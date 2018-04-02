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



$fld = new dbObject ( 'Folder' );
if ( $fld->load ( $GLOBALS[ 'Session' ]->pluginLibraryLevelID ) )
{
	if ( $files = $fld->_table->database->fetchObjectRows ( '
		SELECT * FROM `File` WHERE FileFolder=' . $fld->ID . ' AND `Filetype`=\'swf\' ORDER BY `Filename` ASC
	' ) )
	{
		$str = '';
		foreach ( $files as $file )
		{
			$str .= '<div class="Container" onclick="insertFlashMovie(' . $file->ID . ')" onmouseover="this.style.backgroundColor=\'#cde\'" onmouseout="this.style.backgroundColor=\'#f8f8f8\'" style="cursor: pointer; position: relative; background:url(admin/gfx/icons/page_white_flash.png) no-repeat center center #f8f8f8; width: 80px; height: 80px; margin: 0 2px 2px 0; float: left"><div style="position: absolute; bottom: 4px; left: 4px; width: 72px; text-align: center"><strong>' . $file->Filename . '</strong></div></div>';
		}
		die ( $str . '<br style="clear: both"/>' );
	}
	die ( 'Mappen inneholder ikke flash filer.' );
}
die ( 'Velg en mappe.' );

?>
