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



// Set some options
$columns = $_REQUEST[ 'columns' ] ? $_REQUEST[ 'columns' ] : 3;
$showtitles = $_REQUEST[ 'showtitles' ] ? $_REQUEST[ 'showtitles' ] : false;
$showdesc = $_REQUEST[ 'showdesc' ] ? $_REQUEST[ 'showdesc' ] : false;
$thumbwidth = $_REQUEST[ 'thumbwidth' ] ? $_REQUEST[ 'thumbwidth' ] : 120;
$thumbheight = $_REQUEST[ 'thumbheight' ] ? $_REQUEST[ 'thumbheight' ] : 120;
$scalemode = $_REQUEST[ 'scalemode' ] ? $_REQUEST[ 'scalemode' ] : 'framed';
$bigwidth = $_REQUEST[ 'bigwidth' ] ? $_REQUEST[ 'bigwidth' ] : 512;
$bigheight = $_REQUEST[ 'bigheight' ] ? $_REQUEST[ 'bigheight' ] : 480;
$gallery = $_REQUEST[ 'gid' ] ? $_REQUEST[ 'gid' ] : 0;
if ( $gallery == 0 ){ $gallery = dbFolder::getRootFolder ( ); $gallery = $gallery->ID; }

// Load and generate gallery
$folder = new dbFolder ( );
if ( $folder->load ( $gallery ) )
{
	$images = new dbImage ( );
	$images->ImageFolder = $folder->ID;
	$ostr = 'Ingen bilder i mappe "' . $folder->Name . '"';
	if ( $images = $images->find ( ) )
	{
		$ostr = '<table class="ArenaImageGallery">';
		$row = Array ( );
		$sw = 2;
		foreach ( $images as $image )
		{
			$link = $image->getImageUrl ( $bigwidth, $bigheight, $scalemode );
			$str = '';
			if ( $showtitles )
				$str .= '<p style="Title"><a class="blestbox" href="' . $link . '">' . $image->Title . '</a></p>';
			$str .= '<div class="Image"><a class="blestbox" href="' . $link . '"><img src="' . 
				$image->getImageUrl ( $thumbwidth, $thumbheight, $scalemode ) . '"/></a></div>';
			if ( $showdesc ) 
				$str .= '<div style="Desc">' . $image->Description . '</p>';
			$row[] = $str;
			if ( count ( $row ) >= $columns )
			{
				$ostr .= '<tr class="sw' . ( $sw = ( $sw == 1 ? 2 : 1 ) ) . '"><td>' . implode ( '</td><td>', $row ) . '</td></tr>';
				$row = Array ( );
			}
		}
		// Fill in missing columns
		if ( count ( $row ) > 0 ) 
		{
			$colspan = $columns - count ( $row );
			for ( $a = 0; $a < $colspan; $a++ )
				$row[] = ' ';
			$ostr .= '<tr><td>' . implode ( '</td><td>', $row ) . '</td></tr>';
		}
		$ostr .= '</table>';
	}
}
else $ostr = 'Kunne ikke laste inn bildemappen: ' . $gallery;

$galleryoutput = $ostr; unset ( $ostr );
if ( $_REQUEST[ 'ajax' ] ) die ( $galleryoutput );
?>
