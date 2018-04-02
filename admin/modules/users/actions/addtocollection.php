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
	$collections = Array ( );
	if ( count ( $_POST[ 'collections' ] ) )
	{
		foreach ( $_POST[ 'collections' ] as $cid )
		{
			$c = new dbObject ( 'UserCollection' );
			if ( $c->load ( $cid ) )
			{
				$collections[ $cid ] = $c;
			}
		}
	}
	if ( count ( $collections ) )
	{
		if ( $ids = explode ( ',', $_REQUEST[ 'ids' ] ) )
		{
			foreach ( $ids as $id )
			{
				$u = new dbObject ( 'Users' );
				$u->load ( $id );
				foreach ( $collections as $col )
				{
					$col->addObject ( $u );
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
	function showcols ( $parent = 0, $r = '' )
	{
		$db =& dbObject::globalValue ( 'database' );
		if ( $rows = $db->fetchObjectRows ( '
			SELECT * FROM UserCollection WHERE UserCollectionID=' . $parent . '
		' ) )
		{
			foreach ( $rows as $row )
			{
				$str .= '<option value="' . $row->ID . '">' . $r . $row->Name . '</option>';
				$str .= showcols ( $row->ID, $r . '&nbsp;&nbsp;&nbsp;&nbsp;' );
			}
			return $str;
		}
	}
	$tpl = new cPTemplate ( 'admin/modules/users/templates/addtocollection.php' );
	$tpl->cols = showcols ( );
	die ( $tpl->render ( ) );
}
?>
