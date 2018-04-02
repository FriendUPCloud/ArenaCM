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



$group = new dbObject ( "Groups" );
if ( $_REQUEST[ "gid" ] ) $group->load ( $_REQUEST[ "gid" ] );
if ( !$_REQUEST[ 'gid' ] || $GLOBALS[ 'Session' ]->AdminUser->checkPermission ( $group, 'Write', 'admin' ) ) 
{ 
	$group->receiveForm ( $_POST );
	$group->save ( );
	
	if ( $_POST[ 'LanguageSetting' ] > 0 )
	{
		SetSetting ( 'GroupLanguageSetting', $group->ID, $_POST[ 'LanguageSetting' ] );
	}
	
	if ( $_FILES[ 'Image' ] && $_FILES[ 'Image' ][ 'tmp_name' ] )
	{
		$i = new dbImage ();
		$i->receiveUpload ( $_FILES[ 'Image' ] );
		$i->save ();
		if ( $i->ID > 0 )
		{
			$group->ImageID = $i->ID;
			$group->save ();
		}
	}

	$group->grantPermission ( $GLOBALS[ 'Session' ]->AdminUser, 'Read', 1, 'admin' );
	$group->grantPermission ( $GLOBALS[ 'Session' ]->AdminUser, 'Write', 1, 'admin' );

	$path = 'admin/modules';
	$ostr = '';
	if ( $dir = opendir ( $path ) )
	{
		while ( $file = readdir ( $dir ) )
		{
			if ( $file{0} != '.' )
			{
				// Enable / Disable
				$setting = new dbObject ( 'Setting' );
				$setting->SettingType = 'GroupAccess_' . $group->ID;
				$setting->Key = $file . '_Access';
				$setting->load ( );
				$setting->Value = $_POST[ $file . '_Access' ];
				$setting->save ( );
			
				// Set options for module
				foreach ( $_POST as $k=>$v )
				{
					if ( substr ( $k, 0, strlen ( $file ) + 1 ) == ( $file . '_' ) )
					{
						$setting = new dbObject ( 'Setting' );
						$setting->SettingType = 'GroupAccess_' . $group->ID;
						$setting->Key = $k;
						$setting->load ( );
						$setting->Value = $v;
						$setting->save ( );
					}
				}
			
				// Set special options
				$search = $path . '/' . $file . '/actions/accessconfig.php';
				if ( file_exists ( $search ) && is_file ( $search ) )
				{
					include_once ( $search );
				}
			}
		}
	}
	// Extension settings
	foreach ( $_POST as $k=>$v )
	{
		if ( substr ( $k, 0, 17 ) == "extension_Access_" )
		{
			list ( , , $ext ) = explode ( '_', $k );
			$setting = new dbObject ( 'Setting' );
			$setting->SettingType = 'GroupAccess_' . $group->ID;
			$setting->Key = 'extension_Access_' . $ext;
			$setting->load ( );
			$setting->Value = $v;
			$setting->save ( );
		}
	}
}
ob_clean ( );
header ( 'Location: admin.php?module=users&function=editgroup&gid=' . $group->ID );
die ( );
?>
