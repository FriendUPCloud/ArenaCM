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



include_once ( 'lib/classes/dbObjects/dbFile.php' );

if ( $_REQUEST[ 'Filename' ] )
{
	list ( $filename, ) = explode ( '.', $_REQUEST[ 'Filename' ] );
	$ofilename = $filename;
	$filename = texttourl ( $filename );
	$file = new dbFile ( );
	$file->FileFolder = $Session->LibraryCurrentLevel;
	while ( file_exists ( 'upload/' . $filename . '.' . $_REQUEST[ 'Filetype' ] ) )
	{
		$filename = $filename . rand ( 0, 99999 ) . '.' . $_REQUEST[ 'Filetype' ];
	}
	if ( !strstr ( $filename, '.' . $_REQUEST[ 'Filetype' ] ) )
		$filename .= '.' . $_REQUEST[ 'Filetype' ];
		
	$fp = fopen ( 'upload/' . $filename, 'w+' );
	fwrite ( $fp, $_REQUEST[ 'Content' ] ? $_REQUEST[ 'Content' ] : "\n" );
	fclose ( $fp ); 
	$file->Filename = $filename;
	$file->Title = $ofilename;
	$file->DateCreated = date ( 'Y-m-d H:i:s' );
	$file->DateModified = $file->DateCreated;
	$file->Filetype = $_REQUEST[ 'Filetype' ];
	$file->FilenameOriginal = $_REQUEST[ 'Filename' ];
	$file->save ( );
	die ( 'ok' );
}
else
{
	$tpl = new cPTemplate ( 'admin/modules/library/templates/create_library_file.php' );
	ob_clean ( );
	die ( $tpl->render ( ) );
}
die ( 'fail' );
?>
