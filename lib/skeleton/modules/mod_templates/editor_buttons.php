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


global $document;

i18nAddLocalePath ( 'lib/skeleton/modules/mod_templates/locale' );

if ( $Session->AdminUser->checkPermission ( $content, 'Write', 'admin' ) )
{
	$document->addResource ( 'javascript', 'lib/skeleton/modules/mod_templates/javascript/adminmodule.js' );
	$document->addResource ( 'stylesheet', 'lib/skeleton/modules/mod_templates/css/adminmodule.css' );
	$buttonoutput .= '
		<button type="button" onclick="showTemplateShop()" title="' . i18n ( 'i18n_TemplatesetupDesc' ) . '">
			<img src="admin/gfx/icons/page_white.png">
			' . ( $short ? '' : i18n ( 'i18n_Templatesetup' ) ) . '
		</button>
	';
}

?>
