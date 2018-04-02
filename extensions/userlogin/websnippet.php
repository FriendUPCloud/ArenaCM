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

$db = dbObject::globalValue ( 'database' );
i18nAddLocalePath ( 'extensions/userlogin/locale' );
$contentid = GetSettingValue ( 'Login_Extension', 'register_contentid' );

if ( $contentid )
{
	$cnt = new dbContent ( );
	$cnt->load ( $contentid );
}
else $cnt = $content;

if ( !( $val = getSettingValue ( 'Login_Extension', 'register_contentid' ) ) )
{
	
	list ( $lcnt, ) = $db->fetchRow ( 'SELECT COUNT(*) FROM Languages' );
	$url = BASE_URL . ( $lcnt > 1 ? ( $Session->LanguageCode . '/' ) : '' );
}
else
{
	$url = $cnt->getUrl ( );
}

// Snippet template
$snippet = new cPTemplate ( "extensions/userlogin/templates/websnippet.php" );
$snippet->url = $url;
$snippet->content = $cnt;

// Gen translations
i18n ( 'No user with such an e-mail address exists.' );

$regText = GetSettingValue ( 'Login', 'RegisterText' );
$test = trim ( preg_replace ( array ( '/<br[^>]*?>/', "/\n/", "/\t/", "/\r/" ), '', $regText ) );
if ( strlen ( $test ) )
{
	$regText = i18n ( $regText );
	$regText = str_replace ( '%forgotpassword%', 'javascript: forgotPassword ( )', $regText );
	$regText = str_replace ( '%register%', $url . '?ue=userlogin&function=register', $regText );
	$snippet->registerForm = $regText;
}
else
{
	$registerform = new cPTemplate ( 'extensions/userlogin/webtemplates/registerform.php' );
	$registerform->content = $cnt;
	$registerform->url = $url;
	$snippet->registerForm = $registerform->render ( );
}

$extension .= $snippet->render ( );
?>
