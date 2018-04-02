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



ob_clean ( );
$tpl = new cPTemplate ( 'lib/plugins/userselector/templates/userinfo.php' );
$u = new dbUser ( );
$u->load ( $_REQUEST[ 'uid' ] );
$tpl->user =& $u;

function USListGroups ( $parent = 0, $r = '', $user )
{
	$db =& dbObject::globalValue ( 'database' );
	if ( $rows = $db->fetchObjectRows ( 'SELECT * FROM Groups WHERE GroupID=' . $parent . ' ORDER BY Name ASC' ) )
	{
		foreach ( $rows as $row )
		{
			$row->_tableName = 'Groups';
			$row->_primaryKey = 'ID';
			$row->_isLoaded = true;
			$s = $user->inGroup ( $row ) ? ' selected="selected"' : '';
			$str .= '<option value="' . $row->ID . '"' . $s . '>' . $r . $row->Name . '</option>';
			$str .= USListGroups ( $row->ID, $r . '&nbsp;&nbsp;&nbsp;&nbsp;', &$user );
		}
		return $str;
	}
}

$tpl->groups = '<select multiple="multiple" name="groups[]" size="7" style="width: 270px">' . USListGroups ( 0, '', &$u ) . '</select>';

die ( $tpl->render ( ) );

?>
