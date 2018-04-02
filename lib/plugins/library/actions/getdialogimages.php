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



$images = new dbImage ();
if ( !$Session->pluginLibraryLevelID )
{
	$fld = new dbImageFolder ( );
	$fld = $fld->getRootFolder ( );
	$Session->set ( 'pluginLibraryLevelID', $fld->ID );
}
$images->ImageFolder = $Session->pluginLibraryLevelID;
if ( $images = $images->find ( ) )
{
	foreach ( $images as $image )
	{
		$imageurl = $image->getImageUrl ( 110, 95, 'centered', false, 0xffffff );
		$ostr .= '<div onclick="replaceModalDialogue ( \'library\',  760, 520, \'admin.php?plugin=library&pluginaction=showimagecontrols&iid=' . $image->ID . '&mode=librarydialog\', setupImageControls )" style="width: 112px; height: 110px; text-align: center; -moz-border-radius: 3px; background: #fff url(\'' . $imageurl . '\') no-repeat center 4px; padding: 4px; border: 1px solid #ccc; display: block; float: left; cursor: hand; cursor: pointer; margin: 4px; overflow: hidden"><p style="margin-top: 98px"><small>' . $image->Title . '</small></p></div>';
	}
	ob_clean ( );
	die ( $ostr . '<br style="clear: both" />' );
}
ob_clean ( );
die ( 'Ingen bilder finnes i denne mappen.' );
?>
