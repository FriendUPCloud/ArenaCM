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


ob_clean();

require_once( 'lib/classes/dbObjects/dbImage.php' );

function deleteWholeFolder( $fld )
{
	global $database;
	// Delete sub-folders
	if( $rows = $database->fetchObjectRows( 'SELECT * FROM `Folder` WHERE `Parent`=\'' . $fld->ID . '\'' ) )
	{
		foreach( $rows as $row )
		{
			deleteWholeFolder( $row );
		}
	}
	// Delete files
	$f = new dbFile();
	$f->FileFolder = $fld->ID;
	if( $fs = $f->find() )
	{
		foreach( $fs as $fi ) $fi->delete();
	}
	// Delete images
	$f = new dbImage();
	$f->ImageFolder = $fld->ID;
	if( $fs = $f->find() )
	{
		foreach( $fs as $fi ) $fi->delete();
	}
	$database->query( 'DELETE FROM `Folder` WHERE ID=\'' . $fld->ID . '\'' );
	return true;
}

// It's a single file..
$f = GetFileByPath( $_POST['path'] );
if( $f->ID > 0 )
{
	$f->delete();
	die( 'ok' );
}
// This could be a recursive delete too!
else
{
	$p = end( explode( ':', $_POST['path'] ) );
	$fld = GetFolderByPath( explode( '/', $p ) );
	if( $fld->ID )
	{
		if( deleteWholeFolder( $fld ) )
			die( 'ok' );
	}
}

die( 'fail' );


?>
