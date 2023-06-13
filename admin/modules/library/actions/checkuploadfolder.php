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
include_once ( 'lib/classes/dbObjects/dbImage.php' );

foreach ( array ( 'dbFile', 'dbImage' ) as $row )
{
	$dirname = $row == 'dbFile' ? 'upload' : 'upload/images-master';
	if ( $dir = opendir ( $dirname ) )
	{
		while ( $file = readdir ( $dir ) )
		{
			if ( $file[0] == '.' ) continue;
			if ( is_dir ( $dirname . '/' . $file ) ) continue;
			$ext = end ( explode ( '.', $file ) );
			switch ( strtolower ( $ext[count($ext)-1] ) )
			{
				default:
					$f = new $row ();
					$f->Filename = $file;
					if ( $f->load ( ) ) continue;
					$f->Title = $file;
					$f->Filesize = filesize ( $dirname . '/' . $file );
					$f->Filetype = strtolower ( $ext[count($ext)-1] );
					$f->Filenameoriginal = $file;
					$f->DateModified = date ( 'Y-m-d H:i:s' );
					$f->DateCreated = $f->DateModified;
					$f->save ( );
					break;
			}
		}
		closedir ( $dir );
	}
}

ob_clean ( );
header ( 'Location: admin.php?module=library' );
die ( );

?>
