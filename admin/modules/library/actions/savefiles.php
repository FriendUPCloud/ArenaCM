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



include_once ( "lib/classes/dbObjects/dbFile.php" );

ob_clean ( );

$folder = new dbObject ( 'Folder' );
if ( $folder->load ( $_REQUEST[ 'fileFolder' ] ) )
{
	$a = 0;
	while ( $_FILES[ 'file_' . ( string )$a ][ 'tmp_name' ] )
	{
		$file = new dbFile ( );
		$file->FileFolder = $folder->ID;
		if ( $file->receiveUpload ( $_FILES[ 'file_' . ( string )$a ] ) )
		{
			if ( $_REQUEST[ 'filename_' . ( string )$a ] )
			{
				$file->Title = $_REQUEST[ 'filename_' . ( string )$a ];
			}
			else
			{
				$file->Title = $_FILES[ 'file_' . ( string )$a ][ 'name' ];
			}
			$file->Filesize = filesize ( 'upload/' . $file->Filename );
			$file->save ( );
		}
		$a++;
	}	
	$success = 'Lagret ' . $a . ' filer.';
}
else $success = 'Feilet..';

die( '<script language="JavaScript" type="text/javascript">alert ( "' . $success . '" ); parent.showUploadSuccess();</script>' );
?>
