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

$tpl = new cPTemplate ( $mtpldir . 'adm_settings.php' );
$settings = new Dummy ( );
$settings->Limit = $fieldObject->DataInt ? $fieldObject->DataInt : 10;
$tpl->settings =& $settings;
$test = explode ( "\t", $fieldObject->DataMixed );
$tpl->settings->Comments = 		$test[0];
$tpl->settings->ShowAuthor = 		$test[1];
$tpl->settings->TagBoxEnabled = 	$test[2];
$tpl->settings->TagBoxPosition = $test[3];
$tpl->settings->SearchBox = 		$test[4];
$tpl->settings->Detailpage = 		$test[5];
$tpl->settings->Sourcepage = 		$test[6];
$tpl->settings->LeadinLength = 	$test[7];
$tpl->settings->TitleLength = 	$test[8];
$tpl->settings->SizeX = 			$test[9];
$tpl->settings->SizeY = 			$test[10];
$tpl->settings->HeaderText = 		$test[11];
$tpl->settings->HideDetails = 	$test[12];
$tpl->settings->FacebookLike = 	$test[13];
list ( $tpl->settings->FacebookLikeWidth, $tpl->settings->FacebookLikeHeight ) = explode ( ":", $test[14] );
$tpl->settings->ListMethod = 		$test[15];
$tpl->settings->LSizeX = 			$test[16];
$tpl->settings->LSizeY = 			$test[17];
$tpl->settings->Imageaspect = 	$test[18];
$tpl->settings->Imgcolor =    	$test[19];
$tpl->settings->GalleryMode = 	$test[20];
$tpl->settings->FBComments = 		$test[21];
$tpl->settings->Tagfilter = 		$test[22];
$tpl->settings->Imageaspectdtl = $test[23];
$tpl->settings->Imgcolordtl =    $test[24];
$tpl->settings->Pagination =    $test[25];
$tpl->content =& $content;
$tpl->field =& $fieldObject;
die ( $tpl->render ( ) );

?>
