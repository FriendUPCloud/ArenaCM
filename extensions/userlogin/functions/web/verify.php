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

/** 
 * Verify and register a new user
**/
if ( $_POST[ "Controlnumber" ] != "verified_and_done" )
	die ( "SPAM" );
	
$tpl = new cPTemplate ( 'extensions/userlogin/webtemplates/verify.php' );	
$db =& dbObject::globalValue ( 'database' );

if ( !$_REQUEST[ 'Username' ] ) 
	$_REQUEST[ 'Username' ] = $_REQUEST[ 'Email' ];

if ( !( $row = $db->fetchObjectRow ( "
	SELECT * FROM Users WHERE Email=\"{$_REQUEST["Email"]}\" OR Username=\"{$_REQUEST["Username"]}\"
" ) ) && $_SESSION[ "RegisterControl" ] != $_POST[ "Control" ] )
{
	/**
	 * Mark as all ok and prevent double posts
	**/
	$_SESSION[ "RegisterControl" ] = $_POST[ "Control" ];
	$tpl->verified = true;
	
	/**
	 * Save user
	**/
	$user = new dbUser ( );
	$user->receiveForm ( $_POST );
	$user->Password = $user->hash ( $_REQUEST[ "Password" ] );
	if ( $_REQUEST[ 'Nickname' ] )
		$user->Nickname = $_REQUEST[ 'Nickname' ];
	$user->save ( );
	
	$user->Password_unhash = $_REQUEST[ "Password" ];
	$user->sendWelcomeMail ( );
	
	/**
	 * Attach user to groups
	**/
	$GroupDefaults = new dbObject ( "Setting" );
	$GroupDefaults->SettingType = 'Login_Extension';
	$GroupDefaults->Key = 'DefaultGroups';
	$GroupDefaults->load ( );
	$GroupDefaults = explode ( "\t", $GroupDefaults->Value );
	if ( is_array ( $GroupDefaults ) )
	{
		foreach ( $GroupDefaults as $g )
		{
			$oc = new dbObject ( "UsersGroups" );
			$oc->UserID = $user->ID;
			$oc->GroupID = $g;
			$oc->save ( );
		}
	}
}	
else
{
	/**
	 * Failed to register somehow
	**/
	$tpl->verified = false;
	if ( $_SESSION[ "RegisterControl" ] == $_POST[ "Control" ] )
		$tpl->double = true;
	else $tpl->double = false;
}
?>
