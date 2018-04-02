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


require_once( 'lib/classes/dbObjects/dbImage.php' );

// Overwrite??
if( $f = GetFileByPath( $_POST['path'] ) )
{
	// 1. Find folder
	$fl = new dbFolder( isset( $f->ImageFolder ) ? $f->ImageFolder : $f->FileFolder );
	$dp = $fl->DiskPath ? $fl->DiskPath : ( isset( $f->ImageFolder ) ? 'upload/images-master/' : 'upload/' );
	
	// 2. save file with new content
	if( $fp = fopen( BASE_DIR . '/' . $dp . $f->Filename, 'w' ) )
	{
		fwrite( $fp, $_POST[ 'content' ] );
		fclose( $fp );
	}
	
	// 3. die ok!
	die( 'ok' );
}
// Ok, new file
else
{
	// 1. remove the filename
	$path = end( explode( ':', $_POST['path'] ) );
	$path = explode( '/', $path );
	$filename = end( $path );
	array_pop( $path );
	
	// 2. find the folder
	$fl = GetFolderByPath( $path );

	$path = implode( '/', $path );
	
	// 3. make new file and save it
	if( $fl->ID > 0 )
	{
		// Image or file?
		$ext = end( explode( '.', $filename ) );
		switch( strtolower( $ext ) )
		{
			case 'jpg':
			case 'jpeg':
			case 'gif':
			case 'bmp':
			case 'png':
				$f = new dbImage();
				$f->ImageFolder = $fl->ID;
				break;
			default:
				$f = new dbFile();
				$f->FileFolder = $fl->ID;
				break;
		}
		$dp = $fl->DiskPath ? $fl->DiskPath : ( isset( $f->ImageFolder ) ? 'upload/images-master/' : 'upload/' );
		
		// Make unique name
		$fbase = strstr( $filename, '.' . $ext ) ? substr( $filename, 0, strlen( $filename ) - strlen( '.' . $ext ) ) : $filename;
		$base  = $dp . $fbase;
		$fpart = $fbase;
		$ofilename = $fbase;
		while( file_exists( $base . '.' . $ext ) )
		{
			$fpart = $fbase . rand( 0, 9999 );
			$base = $dp . $fpart;
		}
		
		$filename = $fpart . '.' . $ext;
		
		$fz = 0;
		if( $fi = fopen( BASE_DIR . '/' . $dp . $filename, 'w+' ) )
		{
			fwrite( $fi, $_POST['content'] );
			fclose( $fi );

			$fz = filesize( $dp . $filename );
			
			$f->Filename = $ofilename . '.' . $ext;
			$f->FilenameOriginal = $filename;
			$f->Filesize = $fz;
		
			$f->save();
			die( 'ok' );
		}
	}
	
	// We didn't make it
	die( 'fail' );
	
}

die( 'fail<!--separate-->' );

?>
