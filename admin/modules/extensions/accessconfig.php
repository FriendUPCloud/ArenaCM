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
New code is (C) 2011 Idéverket AS, 2015 Friend Studios AS

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
                Rune Nilssen
*******************************************************************************/

?><p><strong>Utvidelser:</strong></p>
<?php
	$db =& dbObject::globalValue ( 'database' );
	if ( $dir = opendir ( 'extensions' ) )
	{
		while ( $file = readdir ( $dir ) )
		{
			if ( $file[0] == '.' ) continue;
			if ( !file_exists ( 'extensions/' . $file . '/info.csv' ) )
				continue;
				
			if ( $access = $db->fetchObjectRow ( '
				SELECT * FROM Setting WHERE SettingType="GroupAccess_' . $this->Group->ID . '" AND `Key` LIKE "extension_Access_' . $file . '" ORDER BY `Key` ASC
			' ) )
			{
				$access = $access->Value;
			}
			else $access = true;
			$checked = $access ? ' checked="checked"' : '';
			$str .= '<li>' . $file . ' <input type="checkbox"' . $checked . ' onchange="document.getElementById ( \\'extension_ax_' . $file . '\\' ).value = this.checked ? 1 : 0"/>';
			$str .= '<input type="hidden" name="extension_Access_' . $file . '" value="' . ( $access ? '1' : '0' ) . '" id="extension_ax_' . $file . '"/>';
			$str .= '</li>';
		}
		closedir ( $dir );
	}
	return '<ul>' . $str . '</ul>';
?>
