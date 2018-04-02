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

if ( $_POST[ "groups" ] )
{
	$Setting = new dbObject ( "Setting" );
	$Setting->SettingType = 'Login_Extension';
	$Setting->Key = 'DefaultGroups';
	$Setting->load ( );
	$Setting->Value = implode ( "\t", $_POST[ "groups" ] );
	$Setting->save ( );
}

if ( !( $Setting = GetSetting ( 'Login_Extension', 'needsaddress' ) ) )
	SetSetting ( 'Login_Extension', 'needsaddress', 0 );
SetSetting ( 'Login_Extension', 'needsaddress', $_POST[ 'needsaddress' ] );
if ( !( $Setting = GetSetting ( 'Login_Extension', 'hidecountry' ) ) )
	SetSetting ( 'Login_Extension', 'hidecountry', 0 );
SetSetting ( 'Login_Extension', 'hidecountry', $_POST[ 'hidecountry' ] );
if ( !( $Setting = GetSetting ( 'Login_Extension', 'emailasusername' ) ) )
	SetSetting ( 'Login_Extension', 'emailasusername', 0 );
SetSetting ( 'Login_Extension', 'emailasusername', $_POST[ 'emailasusername' ] );
if ( !( $Setting = GetSetting ( 'Login_Extension', 'modulefield' ) ) )
	SetSetting ( 'Login_Extension', 'modulefield', 0 );
SetSetting ( 'Login_Extension', 'modulefield', $_POST[ 'modulefield' ] );
if ( !( $Setting = GetSetting ( 'Login_Extension', 'hidelogintime' ) ) )
	SetSetting ( 'Login_Extension', 'hidelogintime', 0 );
SetSetting ( 'Login_Extension', 'hidelogintime', $_POST[ 'hidelogintime' ] );
if ( !( $Setting = GetSetting ( 'Login_Extension', 'hidewelcometext' ) ) )
	SetSetting ( 'Login_Extension', 'hidewelcometext', 0 );
SetSetting ( 'Login_Extension', 'hidewelcometext', $_POST[ 'hidewelcometext' ] );
if ( !( $Setting = GetSetting ( 'Login_Extension', 'popupdialogs' ) ) )
	SetSetting ( 'Login_Extension', 'popupdialogs', 0 );
SetSetting ( 'Login_Extension', 'popupdialogs', $_POST[ 'popupdialogs' ] );
if ( !( $Setting = GetSetting ( 'Login_Extension', 'register_contentid' ) ) )
	SetSetting ( 'Login_Extension', 'register_contentid', 0 );
SetSetting ( 'Login_Extension', 'register_contentid', $_POST[ 'register_contentid' ] );
if ( !( $Setting = GetSetting ( 'Login_Extension', 'usenickname' ) ) )
	SetSetting ( 'Login_Extension', 'usenickname', 0 );
SetSetting ( 'Login_Extension', 'usenickname', $_POST[ 'usenickname' ] );


ob_clean ( );
header ( "Location: admin.php?module=extensions&extension=userlogin" );
die ( );
?>
