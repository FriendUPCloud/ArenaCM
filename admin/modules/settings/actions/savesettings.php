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



if ( $_POST[ 'Footertext' ] )
{
	SetSetting ( 'settings', 'FooterText', $_POST[ 'Footertext' ] );
}
if ( $_POST[ 'SiteTitle' ] )
{
	$c = file_get_contents ( 'config.php' );
	
	$c = explode ( "\n", $c );
	$out = array ();
	
	// Expect no settings to be set
	$mail_fromname = false;
	$mail_replyto = false;
	$mail_username = false;
	$mail_password = false;
	$mail_hostname = false;
	$mail_transport = false;
	$date_format = false;
	$topmenu_contentgroup = false;
	$topmenu_levels = false;
	$topmenu_mode = false;
	$main_contentgroup = false;
	$site_title = false;
	$admin_language = false;
	$admin_allowinlinestyle = false;
	$admin_jpegquality = false;
	
	// Set some options
	foreach ( $c as $line )
	{
		if ( strstr ( $line, 'SITE_TITLE' ) )
		{
			$out[] = "\tdefine ( 'SITE_TITLE', '{$_POST['SiteTitle']}' );";
			$site_title = true;
		}
		else if ( strstr ( $line, 'MAIL_SMTP_HOST' ) )
		{
			$out[] = "\tdefine ( 'MAIL_SMTP_HOST', '{$_POST['Email_SMTP']}' );";
			$mail_hostname = true;
		}
		else if ( strstr ( $line, 'MAIL_USERNAME' ) )
		{
			$out[] = "\tdefine ( 'MAIL_USERNAME', '{$_POST['Email_Username']}' );";
			$mail_username = true;
		}
		else if ( strstr ( $line, 'MAIL_PASSWORD' ) )
		{
			$out[] = "\tdefine ( 'MAIL_PASSWORD', '{$_POST['Email_Password']}' );";
			$mail_password = true;
		}
		else if ( strstr ( $line, 'MAIL_REPLYTO' ) )
		{
			$out[] = "\tdefine ( 'MAIL_REPLYTO', '{$_POST['Email_ReplyTo']}' );";
			$mail_replyto = true;
		}
		else if ( strstr ( $line, 'MAIL_FROMNAME' ) )
		{
			$out[] = "\tdefine ( 'MAIL_FROMNAME', '{$_POST['Email_FromName']}' );";
			$mail_fromname = true;
		}
		else if ( strstr ( $line, 'MAIL_TRANSPORT' ) )
		{
			$out[] = "\tdefine ( 'MAIL_TRANSPORT', '{$_POST['Email_Transport']}' );";
			$mail_transport = true;
		}
		else if ( strstr ( $line, 'TOPMENU_CONTENTGROUP' ) )
		{
			$out[] = "\tdefine ( 'TOPMENU_CONTENTGROUP', '{$_POST['MenuContentGroup']}' );";
			$topmenu_contentgroup = true;
		}
		else if ( strstr ( $line, 'NAVIGATION_LEVELS' ) )
		{
			$out[] = "\tdefine ( 'NAVIGATION_LEVELS', '{$_POST['MenuLevels']}' );";
			$topmenu_levels = true;
		}
		else if ( strstr ( $line, 'NAVIGATION_MODE' ) )
		{
			$out [] = "\tdefine ( 'NAVIGATION_MODE', '{$_POST['MenuMode']}' );";
			$topmenu_mode = true;
		}
		else if ( strstr ( $line, 'MAIN_CONTENTGROUP' ) )
		{
			$out[] = "\tdefine ( 'MAIN_CONTENTGROUP', '{$_POST['MainContentGroup']}' );";
			$main_contentgroup = true;
		}
		else if ( strstr ( $line, 'DATE_FORMAT' ) )
		{
			$out[] = "\tdefine ( 'DATE_FORMAT', '{$_POST['Date_Format']}' );";
			$date_format = true;
		}
		else if ( strstr ( $line, 'ADMIN_LANGUAGE' ) )
		{
			$out[] = "\tdefine ( 'ADMIN_LANGUAGE', '{$_POST['Admin_Language']}' );";
			$admin_language = true;
		}
		else if ( strstr ( $line, 'ADMIN_ALLOWINLINESTYLE' ) )
		{
			$out[] = "\tdefine ( 'ADMIN_ALLOWINLINESTYLE', '{$_POST['Admin_AllowInlineStyle']}' );";
			$admin_allowinlinestyle = true;
		}
		else if ( strstr ( $line, 'IMAGE_JPEG_QUALITY' ) )
		{
			$out[] = "\tdefine ( 'IMAGE_JPEG_QUALITY', '{$_POST['JpegQuality']}' );";
			$admin_jpegquality = true;
		}
		else if ( trim ( $line ) )
		{
			$out[] = $line;
		}
	}
	
	// Write some settings if not set
	if ( !$site_title )
		$out[] = "\tdefine ( 'SITE_TITLE', '{$_POST['SiteTitle']}' );";
	if ( !$mail_fromname )
		$out[] = "\tdefine ( 'MAIL_FROMNAME', '{$_POST['Email_FromName']}' );";
	if ( !$mail_replyto )
		$out[] = "\tdefine ( 'MAIL_REPLYTO', '{$_POST['Email_ReplyTo']}' );";
	if ( !$mail_username )
		$out[] = "\tdefine ( 'MAIL_USERNAME', '{$_POST['Email_Username']}' );";
	if ( !$mail_password )
		$out[] = "\tdefine ( 'MAIL_PASSWORD', '{$_POST['Email_Password']}' );";
	if ( !$mail_hostname )
		$out[] = "\tdefine ( 'MAIL_SMTP_HOST', '{$_POST['Email_SMTP']}' );";
	if ( !$mail_transport )
		$out[] = "\tdefine ( 'MAIL_TRANSPORT', '{$_POST['Email_Transport']}' );";
	if ( !$topmenu_contentgroup )
		$out[] = "\tdefine ( 'TOPMENU_CONTENTGROUP', '{$_POST['MenuContentGroup']}' );";
	if ( !$topmenu_levels )
		$out[] = "\tdefine ( 'NAVIGATION_LEVELS', '{$_POST['MenuLevels']}' );";
	if ( !$topmenu_mode )
		$out[] = "\tdefine ( 'NAVIGATION_MODE', '{$_POST['MenuMode']}' );";
	if ( !$main_contentgroup )
		$out[] = "\tdefine ( 'MAIN_CONTENTGROUP', '{$_POST['MainContentGroup']}' );";
	if ( !$date_format )
		$out[] = "\tdefine ( 'DATE_FORMAT', '{$_POST['Date_Format']}' );";
	if ( !$admin_language )
		$out[] = "\tdefine ( 'ADMIN_LANGUAGE', '{$_POST['Admin_Language']}' );";
	if ( !$admin_allowinlinestyle )
		$out[] = "\tdefine ( 'ADMIN_ALLOWINLINESTYLE', '{$_POST['Admin_AllowInlineStyle']}' );";
	if ( !$admin_jpegquality )
		$out[] = "\tdefine ( 'IMAGE_JPEG_QUALITY', '{$_POST['JpegQuality']}' );";
	
	
	$str = implode ( "\n", $out );
	$str = str_replace ( array ( '<?php', '?>', '<?' ), '', $str );
	$str = str_replace ( "\n\n", "\n", $str );
	$str = "<?php\n" . $str . "\n?>";
	
	if ( $f = fopen ( 'config.php', 'w+' ) )
	{
		fwrite ( $f, $str );
		fclose ( $f );
	}
}
if ( $_POST[ 'Username' ] )
{
	$user = $Session->AdminUser;
	$user->Name = $_POST[ 'Name' ];
	if ( $_POST[ 'Password' ] != '********' && $_POST[ 'Password' ] == $_POST[ 'Password_Confirm' ] )
		$user->Password = md5 ( $_POST[ 'Password' ] );
	$user->Email = $_POST[ 'Email' ];
	$user->save ( );
	$user->reauthenticate ( $_POST[ 'Username' ], $_POST[ 'Password' ] );
}

header ( 'Location: admin.php?module=settings' );
die ();

?>
