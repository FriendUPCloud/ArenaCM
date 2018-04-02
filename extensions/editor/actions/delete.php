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
	$cnt->IsDeleted = '1';
	$cnt->IsPublished = '0';
	if ( $cnt->ID != $cnt->MainID )
	{
		$main = new dbContent ( );
		if ( $main->load ( $cnt->MainID ) )
		{
			$main->IsDeleted = '1';
			$main->IsPublished = '0';
			$main->save ();
		}
	}
	$cnt->save ();
	// Find parent and set new editor content id
	if ( $cnt->ID )
	{
		$parent = new dbObject ( 'ContentElement' );
		$parent->addClause ( 'WHERE', 'ID != MainID AND MainID = ' . $cnt->Parent );
		$parent = $parent->findSingle ( );
		$Session->Set ( 'EditorContentID', $parent->ID );
		die ( $parent->ID );
	}
}
die ( '<error>Feil</error>' );

?>
