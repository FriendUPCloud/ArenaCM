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
include_once ( "lib/classes/dbObjects/dbFolder.php" );
include_once ( "lib/classes/dbObjects/dbImage.php" );
include_once ( "lib/classes/dbObjects/dbFile.php" );
include_once ( "lib/classes/time/ctime.php" );

$fld = new dbFolder ( );
$time = new cTime ( );

if ( $fld->load ( $Session->pluginLibraryLevelID ) )
{
	$oStr = "";
	
	$imagefolder = new dbImageFolder ( );
	$imagefolder->load ( $fld->ID );
	if ( $imagefolder->getImages () )
	{
		$oStr .= "<h2>Bilder:</h2>";
		foreach ( $imagefolder->_images as $image )
		{
			$oStr .= "<div class=\"LibraryImage\">";
			if ( $image->Description ) $image->Description = "<p>{$image->Description}</p>";
			$image->Description .= "<p><small>Endret: " . $time->fancyNoTime ( $image->DateModified ) . "</small></p>";
			$image->Description .= "<p><small>Lastet opp: " . $time->fancyNoTime ( $image->DateCreated ) . "</small></p>";
			$oStr .= "<span class=\"ImageDescription\">";
			$oStr .= "<span ondblclick=\"initModalDialogue ( 'imageDia', 740, 570, 'admin.php?plugin=library&pluginaction=showimagecontrols&iid={$image->ID}', setupImageControls )\" onmousedown=\"dragger.startDrag ( this, { pickup: 'clone', objectType: 'Image', objectID: '{$image->ID}' } ); return false\" class=\"LibraryImg\">";
			$oStr .= $image->getImageHTML ( 48, 48 );
			$oStr .= '<img class="plugin" src="admin/gfx/icons/plugin.png" border="0"/>';
			$oStr .= "</span>";
			$oStr .= "<p><strong>{$image->Title}</strong></p>";
			$oStr .= '<div class="Description">' . $image->Description . '</div>';
			$oStr .= "</span>";
			$oStr .= "</div>";
		}
	}
	
	$filefolder = new dbFileFolder ( );
	$filefolder->load ( $fld->ID );
	if ( $filefolder->getFiles ( ) )
	{
		$oStr .= "<h2>Filer:</h2>";
		foreach ( $filefolder->_files as $file )
		{
			$oStr .= "<div class=\"LibraryFile\">";
			if ( $file->Description ) $file->Description = "<p>{$file->Description}</p>";
			$file->Description .= "<p><small>Endret: " . $time->fancyNoTime ( $file->DateModified ) . "</small></p>";
			$file->Description .= "<p><small>Lastet opp: " . $time->fancyNoTime ( $file->DateCreated ) . "</small></p>";
			$oStr .= "<span class=\"FileDescription\">";
			$oStr .= "<span onmousedown=\"dragger.startDrag ( this, { pickup: 'clone', objectType: 'File', objectID: '{$file->ID}' } ); return false\" class=\"LibraryFl\"><img src=\"admin/gfx/icons/package.png\" /></span>";
			$oStr .= "<p><strong><a href=\"" . $filefolder->DiskPath . "/" . "{$file->Filename}\">{$file->Title}</a></strong></p>";
			$oStr .= $file->Description;
			$oStr .= "</span>";
			$oStr .= "</div>";
		}		
	}
}

if ( !$oStr ) 
{
	$oStr = "<div class=\"Info\">
					<small>Intet innhold er tilgjengelig.</small>
				</div>";
}

die ( $oStr );

?>
