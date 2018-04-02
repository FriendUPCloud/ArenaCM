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

$scalemode = false;
if ( $_REQUEST[ 'scalemode' ] ) $scalemode = $_REQUEST[ 'scalemode' ];
$effects = false;
if ( $_REQUEST[ 'effects' ] ) $effects = $_REQUEST[ 'effects' ];

if ( 
	$_REQUEST[ "width" ] > 1024 || $_REQUEST[ "height" ] > 1024 ||
	$_REQUEST[ "width" ] <= 0 || $_REQUEST[ "height" ] <= 0
)
{
	$width = 240;
	$height = 180;
}
else
{
	$width = $_REQUEST[ 'width' ];
	$height = $_REQUEST[ 'height' ];
}

$image = new dbImage ( );
$image->load ( $_REQUEST[ "iid" ] );
ob_clean ( );
die ( $image->getImageHTML ( $width, $height, $scalemode, $effects, string2hex ( $_REQUEST[ 'background' ] ) ) );
?>
