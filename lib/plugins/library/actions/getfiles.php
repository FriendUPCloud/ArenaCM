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



include_once ( 'lib/classes/dbObjects/dbImage.php' );
include_once ( 'lib/classes/dbObjects/dbFile.php' );
$ifld = new dbImageFolder ( $_REQUEST[ 'folder' ] ); $ifld->getImages ( );
$ffld = new dbFileFolder ( $_REQUEST[ 'folder' ] ); $ffld->getFiles ( );

foreach ( $ifld->_images as $i )
{
	if ( !$istr ) $istr .= '<h2>Bilder:</h2>';
	$istr .= '<img src="' . $i->getImageUrl ( 32, 32 ) . '" style="cursor: hand; cursor: pointer; margin: 4px" onclick="document.getElementById ( \'link__Url\' ).value = \'' . BASE_URL . 'upload/images-master/' . $i->Filename . '\'; document.getElementById ( \'tabNormalUrl\' ).onclick();" />';
}
foreach ( $ffld->_files as $f )
{
	if ( !$fstr ) $fstr .= '<h2>Filer:</h2><ul>';
	$fstr .= '<li><span style="cursor: hand; cursor: pointer;" onclick="document.getElementById ( \'link__Url\' ).value = \'' . $f->getUrl ( ) . '\'; document.getElementById ( \'tabNormalUrl\' ).onclick();">' . 
						$f->Title . 
						'</span></li>';
}
ob_clean ( );
die ( ( $istr ? ( '<div class="SubContainer" style="margin-top: ' . MarginSize . 'px">' . $istr . '</div>' ) : '' ) . ( $fstr ? ( '<div class="SubContainer" style="margin-top: ' . MarginSize . 'px">' . $fstr . '</ul></div>' ) : '' ) );	
?>
