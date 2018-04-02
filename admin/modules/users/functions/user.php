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

global $database;

include_once ( 'admin/modules/users/include/main_funcs.php' );
$db =& dbObject::globalValue ( 'database' );
$module = new cPTemplate ( "$tplDir/user.php" );

$user = new dbUser ( );

// Try to load the user by uid
if ( $_REQUEST[ 'uid' ] ) $user->load ( $_REQUEST[ 'uid' ] );

// Update the state of the user in terms of group participation
$ingroups = $db->fetchObjectRow ( 'SELECT COUNT(*) FROM UsersGroups WHERE UserID = ' . $user->ID ) ? 1 : 0;
if ( $ingroups != $user->InGroups )
{
	$user->InGroups = $ingroups;
	$user->Save ( );
}

// Test if there is a user template which is global (i.e. there are no group based templates)
if( intval( $user->ID ) > 0 )
{
	if ( !( $rows = $db->fetchObjectRows ( '
		SELECT * FROM Groups WHERE TemplateID > 0
	' ) ) )
	{
		if ( $row = $db->fetchObjectRow ( 'SELECT * FROM Users WHERE IsTemplate = 1' ) )
		{
			$saves = array();
			$i = 0;
			if( isset( $row->ID ) && $user->ID != $row->ID )
			{
				// Small/big content fields
				foreach ( array( 'Small', 'Big' ) as $k  )
				{
					// Check all content data on template user...
					if( $urows = $db->fetchObjectRows( '
						SELECT * FROM `ContentData' . $k . '`
						WHERE
							    ContentID = \'' . $row->ID . '\'
							AND ContentTable = "Users"
						ORDER BY
							SortOrder ASC, ID ASC
					' ) )
					{
						foreach( $urows as $u )
						{
							if( !isset( $u->Name ) ) continue;
							// If the particular element does not exist for user, create it
							if( !$db->fetchObjectRow ( '
							SELECT * FROM `ContentData' . $k . '`
							WHERE 
								    ContentID=\'' . $user->ID . '\' 
								AND ContentTable="Users"
								AND `Name`="' . $u->Name . '"
							' ) )
							{
								$o = new dbObject( 'ContentData' . $k );
								foreach( $u as $kk=>$v )
									$o->$kk = $v;
								$o->ID = 0;
								$o->ContentID = $user->ID;
								$o->Save();
								$saves[] = $u->Name . ' (' . $o->ID . ')';
								$i++;
							}
						}
					}
				}
			}
			//if( $i > 0 )
			//	die( $i .'..' . print_r( $saves, 1 ) );
		}
	}
	// We have a group based template - which is TODO
	else
	{
		// TODO:
	}
}

// Setup pass photo

if ( $user->Image )
{
	include_once ( 'lib/classes/dbObjects/dbImage.php' );
	$image = new dbImage ( $user->Image );
	$module->Passphoto = $image->getImageUrl ( 128, 128, 'framed' );
}
else
{
	$module->Passphoto = 'admin/gfx/arenaicons/user_johndoe_128.png';
}
$module->PassphotoWidth = 128;
$module->PassphotoHeight = 128;

list ( $module->canRead, $module->canWrite, ) = checkUserPermissions ( $user );

// Groups which the user is member of

function listGroups ( &$user, $pid = 0, $r = '' )
{
	global $database, $Session;
	if ( $rows = $database->fetchObjectRows ( 'SELECT * FROM Groups g WHERE g.GroupID=' . $pid ) )
	{
		foreach ( $rows as $row )
		{
			$row->_isLoaded = true;
			$row->_tableName = 'Groups';
			$row->_primaryKey = 'ID';
			if ( !$Session->AdminUser->checkPermission ( $row, 'Read', 'admin' ) )
				continue;
			if ( !$Session->AdminUser->checkPermission ( $row, 'Write', 'admin' ) )
				continue;
			$s = '';
			if ( is_array ( $user->groupids ) )
			{
				if ( in_array ( $row->ID, $user->groupids ) )
					$s = ' selected="selected"';
			}
			$str .= '<option value="' . $row->ID . '"' . $s . '>' . $r . $row->Name . '</option>';
			$str .= listGroups ( $user, $row->ID, $r . '&nbsp;&nbsp;&nbsp;&nbsp;' );
		}
	}
	return $str;
}
$user->loadGroups ( );
$user->groupids = Array ( );
if ( $user->groups )
{
	foreach ( $user->groups as $g )
	{
		$user->groupids[] = $g->ID;
	}
}
$module->groups = listGroups ( $user );


// Add to template
$module->user =& $user;

?>
