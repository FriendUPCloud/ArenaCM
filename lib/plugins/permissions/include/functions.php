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
 * Get groups which can be used for permissions
**/
function pmGetGroups ( $pluginid = '' )
{
	$groups = new dbObject ( 'Groups' );
	if ( $groups = $groups->find ( ) )
	{
		$ostr = '';
		$tpl = new cPTemplate ( 'lib/plugins/permissions/templates/row_group.php' );
		
		$d = new Dummy ( ); $d->ID = '0'; $d->Name = i18n ( 'All users' );
		$tpl->group = $d;
		$tpl->switch = $tpl->switch == 'sw1' ? 'sw2' : 'sw1';
		$tpl->active = true;
		$tpl->PluginID = $pluginid;
		$ostr .= $tpl->render ( );
		
		foreach ( $groups as $group )
		{
			$tpl->group =& $group;
			$tpl->active = false;
			$tpl->PluginID = $pluginid;
			$tpl->switch = $tpl->switch == 'sw1' ? 'sw2' : 'sw1';
			$ostr .= $tpl->render ( );
		}
		
		return $ostr;
	}
	return '';
}

/**
 * Get users which can be used for permissions
**/
function pmGetUsers ( $gid = false, $page = 0, $pluginid = '' )
{
	global $Session;
	$db =& dbObject::globalValue ( 'database' );
	if ( $gid == '0' ) $gid = false;
	$limit = 7;
	
	if ( !$Session->pmPos ) $Session->Set ( 'pmPos', 0 );
	if ( $_REQUEST[ 'pmPos' ] ) $Session->Set ( 'pmPos', $_REQUEST[ 'pmPos' ] );
	if ( $page >= 0 )
	{
		$Session->Set ( 'pmPos', $page * $limit );
	}
	else $Session->Set ( 'pmPos', 0 );
	
	if ( $gid == 'PREVIOUS' )
	{
		$query = explode ( ' LIMIT ', $Session->pmPrevUserListmode );
		$query = $query[ 0 ] . ' LIMIT ' . $Session->pmPos . ',' . $limit;
		$users = $db->fetchObjectRows ( $query );
		$Session->Set ( 'pmPrevUserListmode', $query );
	}
	else if ( $gid )
	{
		$query = '
			SELECT u.* FROM UsersGroups ug, Users u WHERE 
			ug.UserID = u.ID AND ug.GroupID = \'' . $gid . '\'
			ORDER BY u.Username ASC LIMIT ' . (string)$Session->pmPos . ',' . $limit . '
		';
		$users = $db->fetchObjectRows ( $query );
		$Session->Set ( 'pmPrevUserListmode', $query );
	}
	else
	{
		$query = 'SELECT * FROM Users ORDER BY Username ASC LIMIT ' . (string)$Session->pmPos . ',' . $limit;
		$users = $db->fetchObjectRows ( $query );
		$Session->Set ( 'pmPrevUserListmode', $query );
	}

	$ostr = '';

	$count = 0;

	$tquery = explode ( ' LIMIT ', $query );
	$tquery = explode ( ' FROM ', $tquery[ 0 ] );

	list ( $total, ) = $db->fetchRow ( 'SELECT COUNT(*) FROM ' . $tquery[ 1 ] );

	if ( $users )
	{
		$tpl = new cPTemplate ( 'lib/plugins/permissions/templates/row_user.php' );
		foreach ( $users as $user )
		{
			$tpl->user =& $user;
			$tpl->PluginID = $pluginid;
			$tpl->switch = $tpl->switch == 'sw1' ? 'sw2' : 'sw1';
			$ostr .= $tpl->render ( );
			$count++;
		}
	}
	
	$page = round ( $Session->pmPos / $limit );
	
	$nnav = $pnav = '';
	if ( $Session->pmPos > 0 )
		$pnav = '<button type="button" class="Small" onclick="pmShowUsersPage' . $pluginid . ' ( ' . ( $page - 1 ) . ' )"><img src="admin/gfx/icons/arrow_left.png" border="0"></button>';
	if ( $Session->pmPos + $limit < $total )
		$nnav = '<button type="button" class="Small" onclick="pmShowUsersPage' . $pluginid . ' ( ' . ( $page + 1 ) . ' )"><img src="admin/gfx/icons/arrow_right.png" border="0"></button>';
	
	
	/*
		Søkehtml som skal brukes i v1.99.0:
		
				<td style="vertical-align: middle; padding: 2px">
					<input type="text" size="9" id="pmKeywords' . $pluginid . '" value="søk" onclick="if ( this.value == \'søk\' ) this.value = \'\'" style="position: relative; top: -2px; height: 19px; padding: 2px 1px 0px 1px">
					<button class="Small" type="button" style="margin-top: 1px"><img src="admin/gfx/icons/magnifier.png"></button>
				</td>
	*/
	
	$ostr .= '
	<div style="display: block; position: absolute; bottom: 0px; left: 0; width: 100%; height: 28px; overflow: hidden">
		<table width="100%" cellspacing="0" cellpadding="0" border="0" style="position: relative; border-top: 1px solid #aaaaaa">
			<tr>
				<td style="border-left: 1px solid #aaaaaa; padding: 2px; padding-left: 8px; vertical-align: middle; white-space: nowrap">
					' . $pnav . '
					Viser ' . ( $Session->pmPos + 1 ) . ' - ' . ( $Session->pmPos + $count ) . ' av ' . $total . '
					' . $nnav . '
				</td>
			</tr>
		</table>
	</div>
	';
	
	return $ostr;
}

/**
 * List group permissions for content
**/
function pmGroupPermissions ( $object, $ptype = 'web', $pluginid = '' )
{
	if ( !$object ) return '';  
	$db =& dbObject::globalValue ( 'database' );
	$str = '';
	if ( $permissions = $object->getPermissionRules ( 'Groups', $ptype ) )
	{
		foreach ( $permissions as $permission )
		{
			$groups[] = 'ID = \'' . $permission->AuthID . '\'';
		}
		if ( $groups = $db->fetchObjectRows ( '
			SELECT * FROM Groups WHERE ' . implode ( ' OR ', $groups ) . ' ORDER BY `Name` ASC
		' ) )
		{
			$tpl = new cPTemplate ( 'lib/plugins/permissions/templates/row_permission.php' );
			foreach ( $permissions as $permission )
			{
				foreach ( $groups as $group )
				{
					if ( $group->ID == $permission->AuthID )
					{
						$tpl->obj = $group;
						$tpl->permission = $permission;
						$tpl->Name = $group->Name;
						$tpl->Info = false;
						$tpl->PluginID = $pluginid;
						$tpl->switch = $tpl->switch == 'sw1' ? 'sw2' : 'sw1';
						$str .= $tpl->render ( );
					}
				}
			}
		}
	}
	if ( $permissions = $object->getPermissionRules ( 'GlobalPermission', $ptype ) )
	{
		$tpl = new cPTemplate ( 'lib/plugins/permissions/templates/row_permission.php' );
		$tpl->obj = 'global';
		$tpl->permission = $permissions[0];
		$tpl->Name = i18n ( 'All users' );
		$tpl->Info = false;
		$tpl->PluginID = $pluginid;
		$tpl->switch = $tpl->switch == 'sw1' ? 'sw2' : 'sw1';
		$str .= $tpl->render ( );
	}
	if ( $str )
		return $str;
	return 'Ingen grupperettigheter er satt opp.';
}

/**
 * List user permissions for content
**/
function pmUserPermissions ( $object, $ptype = 'web', $pluginid = '' )
{
	if ( !$object ) return '';  
	$db =& dbObject::globalValue ( 'database' );
	if ( $permissions = $object->getPermissionRules ( 'Users', $ptype ) )
	{
		foreach ( $permissions as $permission )
		{
			$users[] = 'ID = \'' . $permission->AuthID . '\'';
		}
		if ( $users = $db->fetchObjectRows ( '
			SELECT * FROM Users WHERE ' . implode ( ' OR ', $users ) . ' ORDER BY `Name` ASC
		' ) )
		{
			$tpl = new cPTemplate ( 'lib/plugins/permissions/templates/row_permission.php' );
			$str = '';
			foreach ( $permissions as $permission )
			{
				foreach ( $users as $user )
				{
					if ( $user->ID == $permission->AuthID )
					{
						$tpl->obj = $user;
						$tpl->permission = $permission;
						$tpl->Name = $user->Name;
						$tpl->Info = $user->Username;
						$tpl->PluginID = $pluginid;
						$tpl->switch = $tpl->switch == 'sw1' ? 'sw2' : 'sw1';
						$str .= $tpl->render ( );
					}
				}
			}
			return $str;
		}
	}
	return 'Ingen brukerrettigheter er satt opp.';
}

?>
