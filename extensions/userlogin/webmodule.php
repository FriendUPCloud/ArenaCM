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

i18nAddLocalePath ( 'extensions/userlogin/locale' );
$extdir = 'extensions/userlogin';

switch ( $_REQUEST[ 'function' ] )
{
	case 'register':
		$tpl = new cPTemplate ( $extdir.'/webtemplates/register.php' );
		$db =& dbObject::globalValue ( 'database' );
		if ( $row = $db->fetchObjectRow ( 'SELECT ID FROM ContentDataSmall WHERE Name="Nickname" AND ContentTable="Users" LIMIT 1' ) )
			$tpl->hasNickname = true;
		break;
	case 'verify':
		include_once ( $extdir.'/functions/web/verify.php' );
		break;
	case 'forgotpassword':
		include_once ( $extdir.'/functions/web/forgotpassword.php' );
		break;
	case 'createpassword':
		include_once ( $extdir.'/functions/web/createpassword.php' );
		break;
	case 'editprofile':
		include_once ( $extdir.'/functions/web/editprofile.php' );
		break;
	case 'saveinfo':
		include_once ( $extdir.'/functions/web/saveinfo.php' );
		break;
	case 'shoppinglog':
		include_once ( $extdir.'/functions/web/shoppinglog.php' );
		break;
	case 'showorder':
		include_once ( $extdir.'/functions/web/showorder.php' );
		break;
	default:
		$tpl = new cPTemplate ( $extdir.'/webtemplates/default.php' );
		break;
}
$tpl->content =& $content;
$module .= $tpl->render ( );

if ( ( $field = GetSettingValue ( 'Login_Extension', 'modulefield' ) ) )
{
	$content->{"_replacement_{$field}"} = $module;
	$module = '';
}
?>
