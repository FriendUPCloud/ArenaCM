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



$u = new dbObject ( 'Users' );

if ( $_REQUEST[ 'ID' ] )
{
	$u->load ( $_REQUEST[ 'ID' ] );
}
else
{
	$db =& dbObject::globalValue ( 'database' );
	if ( $row = $db->fetchObjectRow ( 'SELECT ID FROM Users WHERE Username="' . $_REQUEST[ 'Username' ] . '"' ) )
		die ( '<script> alert ( "Brukeren finnes fra før (brukernavn)." ); </script>' );
	else if ( $row = $db->fetchObjectRow ( 'SELECT ID FROM Users WHERE Email="' . $_REQUEST[ 'Email' ] . '"' ) ) 
		die ( '<script> alert ( "Brukeren finnes fra før (e-post)." ); </script>' );
}

$u->receiveForm ( $_POST );
$u->DateModified = date ( 'Y-m-d H:i:s' );
$u->save ( );
if ( $_REQUEST[ 'groups' ] )
{
	$db =& dbObject::globalValue ( 'database' );
	$db->query ( 'DELETE FROM `UsersGroups` WHERE UserID=\'' . $u->ID . '\'' );
	foreach ( $_REQUEST[ 'groups' ] as $group )
	{
		$db->query ( 'INSERT INTO `UsersGroups` ( `UserID`, `GroupID` ) VALUES ( \'' . $u->ID . '\', \'' . $group . '\' )' );
	}
}
ob_clean ( );
die ( '
	<script>
		if ( parent.document.getElementById ( \'userselectorID\' ) )
		{
			parent.document.getElementById ( \'userselectorID\' ).value = "' . $u->ID . '";
		}
		if ( parent.document.getElementById ( \'userselectorh1\' ) )
		{
			parent.document.getElementById ( \'userselectorh1\' ).innerHTML = "Endre ' . trim ($u->Name) . ' (lagret)";
		}
	</script>
' );
?>
