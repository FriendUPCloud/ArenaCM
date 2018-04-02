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



global $Session;

include_once ( "lib/classes/dbObjects/dbImage.php" );

$image = new dbImage ( );
$image->load ( $_REQUEST[ "iid" ] );

$tpl = new cPTemplate ( "lib/plugins/library/templates/showimagecontrols.php" );

$folder = new dbObject ( 'Folder' );
$folder->load ( $image->ImageFolder );
if ( $Session->AdminUser->checkPermission ( $folder, 'Write', 'admin' ) )
	$tpl->edit = true;
else $tpl->edit = false;

$tpl->Mode = $_REQUEST[ 'mode' ];
$tpl->image = $image->ID;
$tpl->imageid = $image->ID;
$tpl->imagetitle = $image->Title;
$tpl->imagedescription = $image->Description;
$tpl->imagefilename = $image->Filename;
$tpl->imagedimensions = $image->Width . "x" . $image->Height;
die ( $tpl->render ( ) );
?>
