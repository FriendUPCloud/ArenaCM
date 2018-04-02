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

if ( $_FILES[ 'uploadfile' ] )
{
	include_once ( 'lib/classes/dbObjects/dbFolder.php' );
	include_once ( 'lib/classes/dbObjects/dbFile.php' );
	$fld = dbFolder::getRootFolder ();
	$ar = getimagesize ( $_FILES[ 'uploadfile' ][ 'tmp_name' ] );
	$type = 'File';
	switch ( strtolower ( $ar[2] ) )
	{
		case 1:
		case 2:
		case 3:
			$file = new dbImage ();
			$file->ImageFolder = $fld->ID;
			$file->receiveUpload (  $_FILES[ 'uploadfile' ] );
			$file->save ();
			$type = 'Image';
			break;
		default:
			$file = new dbFile ( );
			$file->FileFolder = $fld->ID;
			$file->receiveUpload ( $_FILES[ 'uploadfile' ] );
			$file->save ( );
			break;
	}
	
	$db =& dbObject::globalValue ( 'database' );
	$db->query ( '
		INSERT INTO ObjectConnection
		( ObjectType, ObjectID, ConnectedObjectType, ConnectedObjectID, Label )
		VALUES
		(
			\'ContentElement\', \'' . $_REQUEST[ 'pid' ] . '\', 
			\'' . $type . '\', \'' . $file->ID . '\', \'PageAttachment\'
		)
	' );
	
	ob_clean ( );
	die ( '<script>parent.document.location.reload()</script>' );
}
else if ( !isset ( $_REQUEST[ 'bajaxrand' ] ) )
{
	// do nothing
}
else
{
	$tp = new cPTemplate ( 'lib/plugins/objectconnector/templates/uploadfile.php' );
	die ( $tp->render ( ) );
}

?>
