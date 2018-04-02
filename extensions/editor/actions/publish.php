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
		$published = new dbContent ( );
		if ( $published->load ( $workcopy->MainID ) )
		{
			// Keep original sort order
			$oldSort = $published->SortOrder;
			// Update published version from working copy
			foreach ( $published->_table->getFieldNames() as $field )
				if ( $field != 'ID' ) $published->$field = $workcopy->$field;
			// the original, sort order is published somewhere else you see
			$published->SortOrder = $oldSort; 
			// Save original
			$published->save ( );

			// Copy from working copy to published version
			$published->copyExtraFields ( $workcopy->ID );
			$published->copyObjects ( $workcopy->ID );
			$published->copyPermissions ( $workcopy->ID, 'web' );
			$published->copyPermissions ( $workcopy->ID, 'admin' );
			
			// Sync dates etc
			$published->DateModified = date ( 'Y-m-d H:i:s' );
			$workcopy->DateModified = $published->DateModified;
			$workcopy->DatePublish = $published->DateModified;
			$workcopy->save ( );
			$published->save ( );
			
			// Go through publishhooks
			if ( count ( $Session->EditorPublishHooks ) > 0 )
			{
				foreach ( $Session->EditorPublishHooks as $ph )
				{
					if ( !trim ( $ph ) ) continue;
					if ( file_exists ( $ph ) )
						include_once ( $ph );
				}
			}
			$Session->Del ( 'EditorPublishHooks' );
			
			die ( editorStructure ( $workcopy ) );
		}
	}
	die ( '<error>Ingen rettigheter!</error>' );
}	
die ( '<error>Kan ikke laste inn innhold!</error>' );
?>
