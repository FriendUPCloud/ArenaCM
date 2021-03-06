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

$cnt = new dbContent ( );
if ( $cnt->load ( $_REQUEST[ 'cid' ] ) )
{
	if ( $GLOBALS[ 'Session' ]->AdminUser->checkPermission ( $cnt, 'write', 'admin' ) )
	{
		$f = new dbObject ( $_REQUEST[ 'ft' ] );
		if ( $f->load ( $_REQUEST[ 'fid' ] ) )
		{
			$db =& dbObject::globalValue ( 'database' );
			$db->query ( $q = '
			DELETE FROM 
				`' . $f->_tableName . '`      
			WHERE
				`Name`="' . $f->Name . '" AND
				`Type`="' . $f->Type . '" AND
				`ContentTable`="' . $f->ContentTable . '" AND
				`ContentID`=\'' . $f->ContentID . '\' AND
				`ContentGroup`="' . $f->ContentGroup . '"
			' );
			$cnt->DateModified = date ( 'Y-m-d H:i:s' );
			$cnt->save ( );
		}
	}
}
ob_clean ( );
header ( 'location: admin.php?module=extensions&extension=editor&cid=' . $_REQUEST[ 'cid' ] );
die ( );
?>
