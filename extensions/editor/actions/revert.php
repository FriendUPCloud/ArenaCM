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
include_once ( $extdir . '/include/funcs.php' );
$workcopy = new dbContent ( );
if ( $workcopy->load ( $_REQUEST[ 'cid' ] ) )
{
	if ( $GLOBALS[ 'Session' ]->AdminUser->checkPermission ( $workcopy, 'Publish', 'admin' ) )
	{
		// Load published version and copy everything from it
		$published = new dbContent ( );
		if ( $published->load ( $workcopy->MainID ) )
		{
			// Update workcopy version from published version
			foreach ( $workcopy->_table->getFieldNames() as $field )
				if ( $field != 'ID' ) $workcopy->$field = $published->$field;
			// restore sortorder
			$workcopy->SortOrder = $published->SortOrder; 

			// Copy from published version copy to workcopy version
			$workcopy->copyExtraFields ( $published->ID );
			$workcopy->copyObjects ( $published->ID );
			$workcopy->copyPermissions ( $published->ID, 'web' );
			$workcopy->copyPermissions ( $published->ID, 'admin' );
			
			// Sync dates etc
			$workcopy->DateModified = $published->DateModified;
			$workcopy->DatePublish = $published->DateModified;
			$workcopy->save ( );
			die ( 'ok' );
		}
	}
	die ( '<error>Ingen rettigheter!</error>' );
}	
die ( '<error>Kan ikke laste inn innhold!</error>' );
?>
