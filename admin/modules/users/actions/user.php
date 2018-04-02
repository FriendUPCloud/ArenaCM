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



// Pres..

$db =& dbObject::globalValue ( 'database' );

// Create user object and attempt to load it

$user = new dbUser ( );

if ( $_REQUEST[ 'ID' ] ) 
	$user->load ( $_REQUEST[ 'ID' ] );

// Don't overwrite password with dummy data
$password = $user->Password;
$user->receiveForm ( $_POST );
if ( $user->Password && $user->Password != '********' ) 
	$user->Password = md5 ( $user->Password );
else $user->Password = $password;

$user->save ( );

// Add image if marked

if ( $_FILES[ 'ImageStream' ][ 'tmp_name' ] )
{
	include_once ( 'lib/classes/dbObjects/dbImage.php' );
	include_once ( 'lib/classes/dbObjects/dbFolder.php' );
	$image = new dbImage ( );
	$image->receiveUpload ( $_FILES[ 'ImageStream' ] );
	$folder = new dbFolder ( );
	$folder->Name = 'Brukerbilder';
	$folder->load ( );
	if ( !$folder->ID )
	{
		$pf = $folder->getRootFolder ( );
		$folder->Parent = $pf->ID;
		$folder->save ( );
	}
	$image->ImageFolder = $folder->ID;
	$image->save ( );
	$user->Image = $image->ID;
}

// Remove past groups and add new links in UsersGroups

$user->InGroups = 0;

if ( $_REQUEST[ 'Groups' ] )
{
	$foundGroup = false;
	$user->setGroupsById ( $_REQUEST[ 'Groups' ] );
	if ( count ( $user->groups ) )
		$user->InGroups = 1;
}

$user->DateModified = date ( 'Y-m-d H:i:s' );
$user->save ( );

// Save extra fields
$user->updateExtraFields ( );

ob_clean ( );
if ( $_REQUEST[ 'close' ] )
	header ( 'location: admin.php' );
else header ( 'location: admin.php?module=users&function=user&uid=' . $user->ID );
die ( );
	
?>
