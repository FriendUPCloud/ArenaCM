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

global $user;
include_once ( $extdir . '/include/funcs.php' );
function __deleteContent ( $cnt, $selid )
{
	global $user, $Session;
	// Delete content
	if ( $user->checkPermission ( $cnt, 'Write', 'admin' ) )
	{
		// Make sure we do not delete the root object
		if ( $cnt->Parent == 0 )
			return;
			
		// Delete subcontent first
		$subContent = new dbObject ( 'ContentElement' );
		$subContent->addClause ( 'WHERE', 'Parent=' . $cnt->MainID );
		$subContent->addClause ( 'WHERE', 'ID != MainID' );
		if ( $subs = $subContent->find ( ) )
		{
			foreach ( $subs as $sub )
			{
				$sc = new dbContent ( );
				$sc->load ( $sub->ID );
				__deleteContent ( $sc, $selid );
			}
		}
	
		// Delete selected content
		$pub = new dbContent ( );
		if ( $pub->load ( $cnt->MainID ) )
			$pub->delete ( );
		$db =& $cnt->getDatabase ( );
		$cnt->delete ( );
		
		// Delete notes
		$db->query ( 'DELETE FROM Notes WHERE ContentID=' . $cnt->ID . ' AND ContentTable="ContentElement"' );
		
		// Find parent and set new editor content id
		if ( $selid == $cnt->ID )
		{
			$parent = new dbObject ( 'ContentElement' );
			$parent->addClause ( 'WHERE', 'ID != MainID AND MainID = ' . $cnt->Parent );
			$parent = $parent->findSingle ( );
			$Session->Set ( 'EditorContentID', $parent->ID );
			return $parent->ID;
		}
	}
	//die ( '<error>Du har ikke skrive rettigheter.</error>' );
}

if ( $list = explode ( ',', $_REQUEST[ 'ids' ] ) )
{
	$parent = 0;
	foreach ( $list as $id )
	{
		$cnt = new dbContent ( );
		if ( $cnt->load ( $id ) )
		{
			$parent = __deleteContent ( $cnt, $id );
		}
	}
}
ob_clean ();
header ( 'Location: admin.php?module=extensions&extension=editor' );

?>
