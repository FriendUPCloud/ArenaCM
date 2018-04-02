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


if ( isset ( $_REQUEST[ 'imagefunc' ] ) )
{
	if ( $_REQUEST[ 'imagefunc' ] == 'getImageDescription' )
	{
		$i = new dbImage ( $_REQUEST[ 'id' ] );
		$i->Description = str_replace ( "\n", '<br>', $i->Description );
		die ( '<div class="ImageDescription"><span>' . $i->Description . '</span></div>' );
	}
}
$mtpl = new cPTemplate ( $mtpldir . 'web_gallery.php' );
$str = '';
if ( trim ( $settings->Heading ) )
	$str .= '<h1 class="Heading">' . trim ( $settings->Heading ) . '</h1>';
$folders = explode ( ':', trim ( $settings->Folders ) );
if ( count ( $folders ) )
{
	foreach ( $folders as $fld )
	{
		$imgs = new dbImage ();
		$imgs->addClause ( 'WHERE', 'ImageFolder=\'' . $fld . '\'' );
		if ( $settings->SortMode == 'listmode_sortorder' )
			$imgs->addClause ( 'ORDER BY', 'SortOrder ASC' );
		else if ( $settings->SortMode == 'listmode_fromto' )
		{
			$imgs->addClause ( 'WHERE', 'DateFrom <= NOW() AND DateTo >= NOW()' );
			$imgs->addClause ( 'ORDER BY', 'SortOrder ASC' );
		}
		else $imgs->addClause ( 'ORDER BY', 'DateModified DESC' );
		
		if ( $images = $imgs->find ( ) )
		{
			$i = 0;
			$DetailScale = end ( explode ( '_', $settings->DetailScale ) );
			$PreviewScale = end ( explode ( '_', $settings->PreviewScale ) );
			$ColorDetail = hexdec ( $settings->DetailColor );
			$ColorPreview = hexdec ( $settings->PreviewColor );
			foreach ( $images as $image )
			{
				$detail = new dbImage ( $image->ID );
				if ( !$settings->DetailWidth || !$settings->DetailHeight )
					$fn = 'upload/images-master/' . $image->Filename;
				else
					$fn = $detail->getImageUrl ( $settings->DetailWidth, $settings->DetailHeight, $DetailScale, false, $ColorDetail );
				
				$url = $image->getImageUrl ( $settings->ThumbWidth, $settings->ThumbHeight, $PreviewScale, false, $ColorPreview );
				
				if ( $settings->LightboxDescriptions )
				{
					$str .= '<a href="javascript:void(0)" onclick="showImage(this.getAttribute(\'link\'),this.getAttribute(\'imageid\'))" link="' . $fn . '" imageid="' . $image->ID . '">';
				}
				else
				{
					$str .= '<a href="javascript:void(0)" onclick="showImage(this.getAttribute(\'link\'))" link="' . $fn . '">';
				}
				if ( $settings->ShowTitles ) $str .= '<p>' . $image->Title . '</p>';
				$str .= '<img src="' . $url . '" width="' . $image->cachedWidth . '" height="' . $image->cachedHeight . '"/>';
				$str .= '</a>';
				
				if ( ++$i >= $settings->ThumbColumns )
				{
					$str .= '<br/>';
					$i = 0;
				}
			}
		}
		if ( substr ( $str, -5, 5 ) != '<br/>' )
			$str .= '<br/>';
	}
}
if ( !$str ) $str = '<p>Ingen bilder er lagt til.</p>'; 
$mtpl->output = $str;
?>
