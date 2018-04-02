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



if ( $_REQUEST[ 'apply' ] )
{
	$db =& dbObject::globalValue ( 'database' );
	$groups = Array ( );
	if ( count ( $_POST[ 'groups' ] ) )
	{
		foreach ( $_POST[ 'groups' ] as $cid )
		{
			$c = new dbObject ( 'Groups' );
			if ( $c->load ( $cid ) )
			{
				$groups[ $cid ] = $c;
			}
		}
	}
	if ( count ( $groups ) )
	{
		if ( $ids = explode ( ',', $_REQUEST[ 'ids' ] ) )
		{
			foreach ( $ids as $id )
			{
				$u = new dbObject ( 'Users' );
				$u->load ( $id );
				foreach ( $groups as $col )
				{
					$db->query ( 'DELETE FROM UsersGroups WHERE UserID=' . $id . ' AND GroupID=' . $col->ID );
					$db->query ( 'INSERT INTO UsersGroups ( UserID, GroupID ) VALUES ( ' . $id . ',' . $col->ID . ' )' );
				}
			}
			ob_clean ( );
			header ( 'Location: admin.php?module=users' );
			die ( );
		}
	}
	die ( 'FAIL' );
}
else
{
	function showgroups ( $parent = 0, $r = '' )
	{
		$db =& dbObject::globalValue ( 'database' );
		if ( $rows = $db->fetchObjectRows ( '
			SELECT * FROM Groups WHERE GroupID=' . $parent . '
		' ) )
		{
			foreach ( $rows as $row )
			{
				$str .= '<option value="' . $row->ID . '">' . $r . $row->Name . '</option>';
				$str .= showgroups ( $row->ID, $r . '&nbsp;&nbsp;&nbsp;&nbsp;' );
			}
			return $str;
		}
	}
	$tpl = new cPTemplate ( 'admin/modules/users/templates/addtogroup.php' );
	$tpl->groups = showgroups ( );
	die ( $tpl->render ( ) );
}
?>
