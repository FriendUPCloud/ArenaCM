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

ob_clean ( );

// check for folder 
if ( $_REQUEST[ 'fileFolder' ] < 1 && !$Session->LibraryCurrentLevel ) 
{
	die( '
		<script language="JavaScript" type="text/javascript">
			parent.showUploadError( "Mangler mapppen. Lukk dialogen og pr&oslash;v igjen." );
		</script>
	' );	
}

// load check that folder existis 
$f = new dbFileFolder();
$f->ID = $_REQUEST[ 'fileFolder' ] ? $_REQUEST[ 'fileFolder' ] : $Session->LibraryCurrentLevel;
if ( !$f->load() )
{
	die( '
		<script language="JavaScript" type="text/javascript">
			parent.showUploadError("Kunne ikke laste inn mapppen. Lukk dialogen og pr&oslash;v igjen.");
		</script>
	' );
}

if ( $_FILES[ 'uploadFile' ][ 'error' ] == 1 )
{
	die ( '<script> alert ( \'Filen er for stor!\' ); </script>' );
}

// check that we either have existing image or uploaded file (minimum)
if ( ( !isset( $_REQUEST[ 'fileID' ] ) || $_REQUEST[ 'fileID' ] ) == 0 && !isset( $_FILES[ 'uploadFile' ] ) ) 
{
	die( '<script language="JavaScript" type="text/javascript">parent.showUploadError("Husk &aring; ha med filen i opplastingen.");</script>' );
}

// create new er resave
$file = new dbFile();
if ( isset ( $_REQUEST['fileID'] ) )
{
	$file->load ( $_REQUEST[ 'fileID' ] );
}
$title = trim ( $_REQUEST[ 'fileTitle' ] );
$file->receiveForm ( $_POST );
$file->Tags = trim ( $_REQUEST[ 'fileTags' ] );
saveLibraryTags ( $file->Tags, 'File' );
$file->Description = $_REQUEST[ 'fileDesc' ];
$file->DateModified = date ( "Y-m-d H:i:s" );
// get date from request
if ( !$file->FileFolder )
	$file->FileFolder = $f->ID;
if( isset( $_FILES[ 'uploadFile' ] ) && $_FILES[ 'uploadFile' ][ 'tmp_name' ] ) 
{
	$file->receiveUpload ( $_FILES[ 'uploadFile' ] );	
	$file->Filesize = filesize ( 'upload/' . $file->Filename );
	$file->Title = $title;
	$file->save ( );
	
	if ( trim ( $_REQUEST[ 'FileFilename' ] ) && $_REQUEST[ 'FileFilename' ] != $file->Filename )
	{
		$fn = safeFilename ( $_REQUEST[ 'FileFilename' ] );
		if ( rename ( BASE_DIR . '/' . $f->DiskPath . '/' . $file->Filename, BASE_DIR . '/' . $f->DiskPath . '/' . $fn ) )
		{
			$file->Filename = $fn;
			$file->save ( );
		}
	}
	die( '<script language="JavaScript" type="text/javascript">parent.showUploadSuccess();</script>' );
}
else if ( trim ( $_REQUEST[ 'FileFilename' ] ) && $_REQUEST[ 'FileFilename' ] != $file->Filename )
{
	$fn = safeFilename ( $_REQUEST[ 'FileFilename' ] );
	if ( rename ( BASE_DIR . '/' . $f->DiskPath . '/' . $file->Filename, BASE_DIR . '/' . $f->DiskPath . '/' . $fn ) )
	{
		$file->Filename = $fn;
	}
	$file->Title = $title;
	$file->save ( );
	die( '<script language="JavaScript" type="text/javascript">parent.showUploadSuccess();</script>' );
}
else if ( file_exists ( 'upload/' . $file->Filename ) )
{
	$file->Title = $title;
	$file->save ( );
	die( '<script language="JavaScript" type="text/javascript">parent.showUploadSuccess();</script>' );
}
else die ( '<script type="text/javascript">alert ( "Kunne ikke laste opp filen. Filen var sikkert for stor." );</script>' );

?>
