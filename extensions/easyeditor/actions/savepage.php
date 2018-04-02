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

global $Session;
$db =& dbObject::globalValue ( 'database' );
include_once ( 'lib/classes/dbObjects/dbContent.php' );

// Get content
$workcopy = new dbContent ();
if ( $workcopy->load ( $_POST[ 'ID' ] ) )
{
	if ( $workcopy->ID == $workcopy->MainID )
	{
		$id = $db->fetchObjectRow ( 'SELECT ID FROM ContentElement WHERE MainID != ID AND MainID=' . $workcopy->ID );
		$workcopy->load ( $id->ID );
	}
	$GLOBALS[ 'testing' ] = '1';
	if ( $Session->AdminUser->checkPermission ( $workcopy, 'Write', 'admin' ) )
	{
		$workcopy->MenuTitle = stripslashes ( $_POST[ 'pageTitle' ] );
		$workcopy->Title = stripslashes ( $_POST[ 'pageTitle' ] );
		$workcopy->DateModified = date ( 'Y-m-d H:i:s' );
	
		// Find content field and update it
		if ( isset ( $_POST[ 'bodyField' ] ) )
		{
			list ( , $fieldId, $fieldType, ) = explode ( '_', $_POST[ 'bodyField' ] );
			if ( $field = $db->fetchObjectRow ( '
				SELECT * FROM ContentData' . $fieldType . '
				WHERE 
					ID=\'' . $fieldId . '\' AND 
					ContentID=\'' . $workcopy->ID . '\' AND 
					ContentTable="ContentElement" AND 
					AdminVisibility >= 1
				ORDER BY SortOrder ASC LIMIT 1
			' ) )
			{
				$of = new dbObject ( 'ContentData' . $fieldType );
				$of->load ( $field->ID );
				switch ( $field->Type )
				{
					case 'script':
						$of->DataString = stripslashes ( $_POST[ 'fieldData' ] );
						break;
					case 'image':
						$of->DataInt = stripslashes ( $_POST[ 'fieldData' ] );
						break;
					case 'style':
						$of->DataString = stripslashes ( $_POST[ 'fieldData' ] );
						break;
					case 'varchar':
						$of->DataString = stripslashes ( $_POST[ 'fieldData' ] );
						break;
					case 'text':
						$of->DataText = arenasafeHTML ( $_POST[ 'fieldData' ] );
						break;
					case 'contentmodule':
						break;
					case 'leadin':
						$of->DataText = arenasafeHTML ( $_POST[ 'fieldData' ] );
						break;
					case 'newscategory':
						$of->DataInt = stripslashes ( $_POST[ 'fieldData' ] );
						break;
					case 'objectconnection':
						$of->DataInt = stripslashes ( $_POST[ 'fieldData' ] );
						break;
					case 'pagelisting':
						$of->DataInt = stripslashes ( $_POST[ 'fieldData' ] );
						break;
					case 'extension':
						$of->DataMixed = $_POST[ 'fieldData' ];
						break;
					default:
						$of->DataString = stripslashes ( $_POST[ 'fieldData' ] );
						break;
				}
				$of->save ( );
			}
			else
			{
				$workcopy->Body = arenasafeHTML ( $_POST[ 'fieldData' ] );
			}
		}

		// Save content
		$workcopy->IsPublished = '1';
		$workcopy->save ();
	
		// Publish page
		if ( $Session->AdminUser->checkPermission ( $workcopy, 'Publish', 'admin' ) )
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
				$modDate = $workcopy->DateModified;
				$published->DateModified 	= $modDate;
				$published->DatePublish 	= $modDate;
				$published->save ();
				$workcopy->DateModified 	= $modDate;
				$workcopy->DatePublish 		= $modDate;
				$workcopy->save ();
				
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
				
				die ( 'ok' );
			}
		}

		die ( 'fail' );
	}
	die ( 'fail' ); // No permissions?
}
die ( 'fail' );

?>
