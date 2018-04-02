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

include_once ( "lib/classes/dbObjects/dbFolder.php" );

/**
 * save library level
 * 
 * data comes from request ;)
 * 
 * lid via GET // rest via POST
 */

// load folder ===================================================
$new = false;
$f = new dbFolder();
if ( $_REQUEST[ 'ID' ] )
	$f->load ( $_REQUEST[ 'ID' ] );
else $new = true;
	
// save data =====================================================
if ( strlen ( trim ( $_REQUEST[ 'folderName' ] ) ) )
{
	$f->Name = $_REQUEST[ 'folderName' ];
	$f->Description = $_REQUEST[ 'folderDescription' ];
	if ( $_REQUEST[ 'Parent' ] != $_REQUEST[ 'ID' ] )
	{
		$f->Parent = $_REQUEST[ 'Parent' ];
	}
	$f->SortOrder = $_REQUEST[ 'folderSortOrder' ];
	$f->Save();
	$Session->Set ( 'LibraryCurrentLevel', $f->ID );
}
if ( $new ) $f->copyPermissions ( $f->Parent );

ob_clean ( );

$script = '
	<script> 
		var f = parent.ge(\'editLevelForm\');
		f.folderName.value="' . addslashes ( $f->Name ) . '";
		f.ID.value = \'' . $f->ID . '\';
		parent.setLibraryLevel ( \'' . $f->ID . '\' );
		parent.getLibraryLevelTree ();
		!!CLOSE!!
	</script>
';
// Add closure code if we need to remove modaldialogue
if ( $_REQUEST[ 'close' ] )
{
	$script = str_replace ( "!!CLOSE!!", 
		'parent.editor.removeControl ( \'folderDescription\' ); 
		parent.removeModalDialogue ( \'EditLevel\' ); 
		', 
		$script
	);
}
else $script = str_replace ( "!!CLOSE!!", "", $script );

die ( $script );

?>
