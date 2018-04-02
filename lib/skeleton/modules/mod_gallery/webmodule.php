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

global $document, $mtpl;
$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
$document->addResource ( 'javascript', 'lib/javascript/bajax.js' );
$document->addResource ( 'stylesheet', 'lib/skeleton/modules/mod_gallery/css/web.css' );
$document->addResource ( 'javascript', 'lib/skeleton/modules/mod_gallery/javascript/web.js' );
$mtpldir = 'lib/skeleton/modules/mod_gallery/templates/';
i18nAddLocalePath ( 'lib/skeleton/modules/mod_gallery/locale');
$settings = CreateObjectFromString ( $field->DataMixed );
if ( !$settings->ThumbWidth ) $settings->ThumbWidth = 80;
if ( !$settings->ThumbHeight ) $settings->ThumbHeight = 60;
if ( !$settings->ThumbColumns ) $settings->ThumbColumns = 4;
if ( $settings->currentMode == 'gallery' )
{
	include ( 'lib/skeleton/modules/mod_gallery/include/web_gallery.php' );
}
else if ( $settings->currentMode == 'archive' )
{
	$mtpl = new cPTemplate ( $mtpldir . 'web_archive.php' );
	include ( 'lib/skeleton/modules/mod_gallery/include/web_archive.php' );
}
else
{
	include ( 'lib/skeleton/modules/mod_gallery/include/web_slideshow.php' );
}
$mtpl->settings =& $settings;
$mtpl->field =& $field;
$module .= $mtpl->render ( );
?>
