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


include_once ( "lib/classes/dbObjects/dbImage.php" );

ob_clean ( );

// check that we either have existing image or uploaded file (minimum)
if ( 
	( !isset( $_REQUEST[ 'imageID' ] ) || intval( $_REQUEST[ 'imageID' ] ) == 0 ) && 
	!isset( $_FILES[ 'uploadFile' ] ) 
) 
{
	die( '<script language="javascript" type="text/javascript">parent.showUploadError( "Vennligst last opp et bilde." );</script>' );
}

// create new er resave ===========================================================================================
$image = new dbImage();
$image->load( intval( $_REQUEST[ 'imageID' ] ) );

// get date from request ==========================================================================================
if( intval( $_REQUEST[ 'fileFolder' ] ) > 0 ) 
	$image->ImageFolder = $_REQUEST[ 'fileFolder' ];
	
if( isset( $_FILES[ 'uploadFile' ] ) ) 
	$image->receiveUpload ( $_FILES[ 'uploadFile' ] );

$image->Description = $_REQUEST[ 'fileDesc' ];
$image->DateTo = $_REQUEST[ 'DateTo' ];
$image->DateFrom = $_REQUEST[ 'DateFrom' ];
$image->DateModified = date ( "Y-m-d H:i:s" );
$image->Title = $_REQUEST[ 'fileTitle' ];
$image->Tags = trim ( $_REQUEST[ 'fileTags' ] );
$image->save ( );
saveLibraryTags ( $image->Tags, 'Image' );

// load folder
$f = new dbImageFolder();
$f->ID = $_REQUEST[ 'fileFolder' ];
if( !$f->load() ) $f = false;

if ( trim ( $_REQUEST[ 'ImageFilename' ] ) && $_REQUEST[ 'ImageFilename' ] != $image->Filename )
{
	$fn = safeFilename ( $_REQUEST[ 'ImageFilename' ] );
	$path = $f ? ( trim ( $f->DiskPath ) ? $f->DiskPath : $image->getFolderPath ( ) ) : $image->getFolderPath ( );
	if ( rename ( BASE_DIR . '/' . $path . '/' . $image->Filename, BASE_DIR . '/' . $path . '/' . $fn ) )
	{
		$image->Filename = $fn;
		$image->save ( );
	}
}

unset( $f );

die( '<script language="javascript" type="text/javascript">parent.showUploadSuccess();</script>' );

?>


  
