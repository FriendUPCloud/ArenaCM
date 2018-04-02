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
New code is (C) 2011 Idéverket AS, 2015 Friend Studios AS

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
                Rune Nilssen
*******************************************************************************/


include_once ( 'lib/classes/dbObjects/dbFolder.php' );
include_once ( 'lib/classes/dbObjects/dbFile.php' );

// Get parent folder
list( $vol, $p ) = explode( ':', trim( $_POST['path'] ) ); 
if( substr( $p, -1, 1 ) == '/' ) $p = substr( $p, 0, strlen( $p ) - 1 );
$p = explode( '/', $p );
$fld = GetFolderByPath( $p );
if( !$fld ) die( 'fail' );

$array = array ();
list ( , $path ) = explode ( ':', $_POST['path'] );

// Get subfolders
if ( $folders = $fld->getFolders () )
{
	foreach ( $folders as $fold )
	{
		$f = new stdclass ();
		$f->Type = 'Directory';
		$f->MetaType = 'Directory';
		$f->Filename = $fold->Name;
		$f->Path = $vol . ':' . $path . '/' . $fold->Name . '/';
		$array[] = $f;
	}
}
// Get files in folder
if ( $files = $fld->getFiles () )
{
	foreach ( $files as $file )
	{
		$f = new stdclass ();
		$f->Type = 'File';
		$f->MetaType = 'File';
		$f->Filename = $file->Filename;
		$f->Path = $vol . ':' . $path . '/' . $file->Filename;
		$array[] = $f;
	}
}
die ( 'ok<!--separate-->' . json_encode ( $array ) );

?>
