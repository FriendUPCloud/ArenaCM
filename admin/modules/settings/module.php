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

i18nAddLocalePath ( 'admin/modules/settings/locale' );

$Settings = getModuleSettings ( 'settings' );

// Get variants
function getVariants ()
{
	global $database;
	$t = new cPTemplate ( 'admin/modules/settings/templates/variants.php' );
	$str = '<tr class="sw1"><td colspan="2">Ingen varianter er lagret.</td></tr>';
	if ( $rows = $database->fetchObjectRows ( '
		SELECT * FROM Languages ORDER BY NativeName
	' ) )
	{
		$str = '';
		foreach ( $rows as $row )
		{
			if ( $row->IsDefault ) { $bs = '<strong>'; $be = '</strong>'; }
			else $bs = $be = '';
			$str .= '<tr class="sw' . ( $sw = ( $sw == 1 ? 2 : 1 ) ) . '">';
			$str .= '<td>' . $bs . $row->NativeName . ' (' . $row->Name . ')' . $be . '</td>';
			$str .= '<td style="width: 130px">';
			$str .= '<button type="button" onclick="editVariant(' . $row->ID . ')"><img src="admin/gfx/icons/page_edit.png"/>Endre</button>';
			$str .= '<button type="button" onclick="deleteVariant(' . $row->ID . ')"><img src="admin/gfx/icons/page_delete.png"/>Slett</button>';
			$str .= '</td>';
			$str .= '</tr>';
		}
	}
	$t->variants = $str;
	return $t->render ();
}

require_once ( 'admin/modules/settings/dbcheck.php' );
require_once ( 'lib/classes/time/ctime.php' );
require_once ( 'lib/functions/functions.php' );

if ( $action ) include ( str_replace ( array ( '..' ), '', $action ) );
if ( $function ) include ( str_replace ( array ( '..' ), '', $function ) );

?>
