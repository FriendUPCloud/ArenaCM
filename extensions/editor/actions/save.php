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

include_once ( $extdir . '/include/funcs.php' );
$content = new dbContent ( );
if ( $content->load ( $_POST[ 'cid' ] ) )
{
	if ( $GLOBALS[ 'Session' ]->AdminUser->checkPermission ( $content, 'Write', 'admin' ) )
	{
		if ( $content->ContentType == 'text' )
		{
			$content->Intro = arenasafeHTML ( $_POST[ 'Intro' ] );
			$content->Body = arenasafeHTML ( $_POST[ 'Body' ] );
		}
		$content->Title = $_POST[ 'Title' ];
		$content->MenuTitle = $_POST[ 'MenuTitle' ];
		$content->Link = $_POST[ 'Link' ];
		$content->LinkData = "LinkTarget\t" . $_POST[ 'LinkTarget' ];
		$content->DateModified = date ( 'Y-m-d H:i:s' );
		if ( strtotime ( $content->DateCreated ) <= 0 )
			$content->DateCreated = $content->DateModified;
		$content->Author = $Session->AdminUser->ID;
		$content->save ();
		
		// Save extra fields
		$content->updateExtraFields ( );
		
		// Update notes
		$db =& $content->getDatabase ( );
		if ( $row = $db->fetchObjectRow ( 'SELECT * FROM Notes WHERE ContentTable="ContentElement" AND ContentID=' . $content->ID ) )
		{
			$db->query ( 'UPDATE `Notes` SET `Notes`="' . trim ( addslashes ( $_POST[ 'Notes' ] ) ) . '" WHERE ContentTable="ContentElement" AND ContentID=' . $content->ID );
		}
		else
		{
			$db->query ( 'INSERT INTO `Notes` ( ContentTable, ContentID, Notes ) VALUES ( "ContentElement", ' . $content->ID . ', "' . trim ( str_replace ( "\"", "", $_POST[ 'Notes' ] ) ) . '" )' );
		}
		die ( editorStructure ( $content ) . '<!-- separate -->' . $content->getUrl () );
	}
	else die ( '<error>Du har ikke rettigheter til å lagre!</error>' );
}
die ( 'Error!' );
?>
