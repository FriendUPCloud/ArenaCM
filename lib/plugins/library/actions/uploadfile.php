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

if ( $_FILES[ "FileStream" ] )
{
	include_once ( "lib/classes/dbObjects/dbFile.php" );
	
	$folder = new dbImageFolder ( );
	if ( !$Session->pluginLibraryLevelID )
	{
		$folder = $folder->getRootFolder ();
		$Session->Set ( 'pluginLibraryLevelID', $folder->ID );
	}
	$write = $Session->AdminUser->checkPermission (
		$folder, 'write', 'admin' );
	
	if ( $write && file_exists ( $_FILES[ "ImageStream" ][ "tmp_name" ] ) )
	{
		$file = new dbFile ( );
		$file->receiveUpload ( $_FILES[ "FileStream" ] );
		$file->Title = $_REQUEST[ "Title" ] ? $_REQUEST[ "Title" ] : $_FILES[ 'FileStream' ][ 'name' ];
		list ( $fallback, ) = explode ( ".", $_FILES[ "FileStream" ][ "name" ] );
		$file->FileFolder = $_REQUEST[ "Level" ] ? $_REQUEST[ "Level" ] : $fallback;
		$file->save ( );
		die ( "<html><head><title>Image upload complete</title></head><body><script>parent.pluginLibraryShowContent ( );</script></body></html>" );
	}
}
die ( "<html><head><title>Image upload complete</title></head><body><script>alert ( 'Opplastingen feilet.' );</script></body></html>" );
?>
