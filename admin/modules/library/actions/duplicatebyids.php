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

if ( is_array ( $ids = explode ( ',', $_REQUEST[ 'ids' ] ) ) )
{
	foreach ( $ids as $id )
	{
		list ( $type, $id ) = explode ( '_', $id );
		if ( $type == 'image' )
		{
			$f = new dbImage();
			$c = new dbImage ();
			$p = 'upload/images-master/';
		}
		else
		{
			$f = new dbFile ( );
			$c = new dbFile ( );
			$p = 'upload/';
		}
		if ( $f->load( intval ( $id ) ) )
		{
			$c->load ( $id );
			$c->ID = 0;
			$c->_isLoaded = false;
			$num = 0;
			$fn = $f->Filename;
			while ( file_exists ( $p . $fn ) )
			{
				$fn = explode ( '.', $f->Filename );
				$ex = $fn[count($fn)-1];
				array_pop ( $fn );
				$fn = implode ( '.', $fn ) . '_copy_' . ++$num . '.' . $ex;
			}
			copy ( $p . $f->Filename, $p . $fn );
			$c->Filename = $fn;
			$c->Title = $f->Title . ' copy';
			$c->save ();
		}
	}
	die( 'OK' );
}
die ( 'FAIL' );
?>
