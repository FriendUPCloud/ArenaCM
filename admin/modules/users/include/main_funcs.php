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

if ( !$_SESSION[ 'users_dbcheck' ] )
{
	$t = new cDatabaseTable ( 'Users' );
	if ( $t->load () )
	{
		$sort = false;
		$image = false;
		foreach ( $t->getFieldNames () as $field )
		{
			if ( $field == 'SortOrder' )
				$sort = true;
			if ( $field == 'ImageID' )
				$image = true;
		}
		if ( !$sort )
		{
			$database->query ( 'ALTER TABLE `Users` ADD SortOrder int(11) NOT NULL default \'0\' AFTER ID' );
		}
		if ( !$image )
		{
			$database->query ( 'ALTER TABLE `Users` ADD ImageID bigint(20) NOT NULL default \'0\' AFTER ID' );
		}
	}
	$_SESSION[ 'users_dbcheck' ] = true;
}



function renderGroupList ( $parent = 0 )
{
	global $Session;
	global $database;
	
	if ( $rows = $database->fetchObjectRows ( 'SELECT * FROM `Groups` WHERE GroupID=\'' . $parent . '\' ORDER BY `Name` ASC' ) )
	{
		$str .= '<ul' . ( $parent ? '' : ' class="Collapsable"' ) . '>';
		if ( !$parent )
		{
			$n = new Dummy ( );
			$n->ID = 'orphans';
			$n->Name = 'Brukere uten gruppe';
			$rows[] = $n;
			$n = new Dummy ( );
			$n->ID = 'inactive';
			$n->Name = 'Inaktive brukere';
			$rows[] = $n;
			$n = new Dummy ( );
			$n->ID = 'all';
			$n->Name = 'Alle brukere';
			$rows[] = $n;
		}
		foreach ( $rows as $row )
		{
			if ( $row->ID === $Session->UsersCurrentGroup )
			{
				 $str .= '<li class="current" id="currentlevel">
				 <div class="ButtonBox" id="levelButtons' . $row->ID . '">
				 	 <span>
				 		<a href="admin.php?module=users&gid=' . $row->ID . '">' . $row->Name . '</a>
					 </span>
				 ';
				 
				 if ( $row->ID > 0 )
				 {
				 	$str .= '
				 	<div class="ButtonBoxButtons">
					 	<button onclick="editGroup(' . $row->ID . ')">
					 		<img src="admin/gfx/icons/group_edit.png">
					 	</button> 
					 	<button onclick="subGroup(' . $row->ID . ')">
					 		<img src="admin/gfx/icons/group_add.png">
					 	</button> 
					 	<button onclick="deleteGroup(' . $row->ID . ')">
					 		<img src="admin/gfx/icons/group_delete.png">
					 	</button>
					 	<button onclick="addToWorkbench ( \'' . $row->ID . '\', \'Groups\' )">
					 		<img src="admin/gfx/icons/plugin.png">
					 	</button>
					 </div>
					 ';
				}
				$str .= '
					 <div style="clear: both"></div>
				 </div>';
			}
			else $str .= '<li><span><a href="admin.php?module=users&gid=' . $row->ID . '">' . $row->Name . '</a></span>';
			if ( $row->ID > 0 )
				$str .= renderGroupList ( $row->ID );
			if ( $row->ID === $Session->UsersCurrentGroup )
				$str .= '<div style="clear: both"><em></em></div>';
			$str .= '</li>';
		}
		$str .= '</ul>';
	}
	return $str;
}

function renderUnitList ( $parent = 0 )
{
	global $Session;
	global $database;
	
	if ( $rows = $database->fetchObjectRows ( 'SELECT * FROM UserCollection WHERE UserCollectionID=' . $parent . ' ORDER BY `Name` ASC' ) )
	{
		$str .= '<ul' . ( $parent ? '' : ' class="Collapsable"' ) . '>';
		foreach ( $rows as $row )
		{
			if ( $row->ID === $Session->UsersCollectionID )
			{
				 $str .= '<li class="current" id="currentlevel">
				 <div class="ButtonBox" id="levelButtons' . $row->ID . '">
				 	<a href="admin.php?module=users&collectionid=' . $row->ID . '">' . (trim($row->Name)?$row->Name:i18n('Unnamed')) . '</a>
				 ';
				 if ( $row->ID > 0 )
				 {
				 	$str .= '
					<div class="ButtonBoxButtons">
						<button onclick="editCollection(' . $row->ID . ')">
							<img src="admin/gfx/icons/building_edit.png">
						</button> 
						<button onclick="subCollection(' . $row->ID . ')">
							<img src="admin/gfx/icons/building_add.png">
						</button> 
						<button onclick="deleteCollection(' . $row->ID . ')">
							<img src="admin/gfx/icons/building_delete.png">
						</button>
					</div>
					 ';
				}
				$str .= '
					 <div style="clear: both"></div>
				 </div>';
			}
			else $str .= '<li><span><a href="admin.php?module=users&collectionid=' . $row->ID . '">' . (trim($row->Name)?$row->Name:i18n('Unnamed')) . '</a></span>';
			if ( $row->ID > 0 )
				$str .= renderUnitList ( $row->ID );
			if ( $row->ID === $Session->UsersCollectionID )
				$str .= '<div style="clear: both"><em></em></div>';
			$str .= '</li>';
		}
		$str .= '</ul>';
	}
	else if ( !$parent )
	{
		$str = '<p>Ingen enheter er satt opp.</p>';
	}
	return $str;
}



function checkUserPermissions ( $user )
{
	global $Session;
	$db =& dbObject::globalValue ( 'database' );
	
	$canWrite = $canRead = false;
	
	if ( $Session->AdminUser->_dataSource == 'core' )
	{
		return Array ( true, true );
	}
	
	
	if ( $groups = $db->fetchObjectRows ( "
		SELECT g.ID, g.Name FROM UsersGroups ug, `Groups` g WHERE ug.GroupID = g.ID AND ug.UserID='" . $user->ID . "' ORDER BY g.Name
	" ) )
	{
		$canRead = true;
		$canWrite = true;
		foreach ( $groups as $g )
		{
			$g->_tableName = 'Groups';
			$g->_primaryKey = 'ID';
			$g->_isLoaded = true;
			if ( !$Session->AdminUser->checkPermission ( $g, 'Read', 'admin' ) )
				$canRead = false;
			if ( !$Session->AdminUser->checkPermission ( $g, 'Write', 'admin' ) )
				$canWrite = false;
		}
	}
	return Array ( $canRead, $canWrite );
}


/* TODO: Fix these! */

function action_nudgegroupobject ( $options )
{
	if ( !$options[ 'otype' ] || !$options[ 'oid' ] ) return false;
	
	$group = new dbObjectConnectable ( "Groups" );
	if ( !$group->load ( $options[ 'gid' ] ) ) return false;
	
	$object = new dbObject ( $options['otype' ] );
	$object->load ( $options[ 'oid' ] );
	
	$offset = ( $options['offset'] ) ? $options['offset'] : 0;
	if ( $options['dir'] == "up" )   $offset = -1;
	if ( $options['dir'] == "down" ) $offset = +1;
	$group->reorderObject( $object, $offset );
	
	die ( );
}

function action_deletegroupobject ( $options )
{
	if ( !$options[ 'otype' ] || !$options[ 'oid' ] ) return false;
	$group = new dbObjectConnectable ( "Groups" );
	if ( !$group->load ( $options[ 'gid' ] ) ) return false;
	$obj = new dbObject ( $options[ 'otype' ] );
	if ( !$obj->load ( $options[ 'oid' ] ) ) return false;
	$group->removeObject ( $obj );
	die ( );
}

/////////////////////////////////////////////////////////
////     FUNCTIONS                                   ////
/////////////////////////////////////////////////////////


function getAuthorizedGroups ( $user, $permission = '' )
{
	$db =& dbObject::globalValue ( 'database' );
	if ( $user->_dataSource == 'core' || $user->isSuperUser ( ) )
	{
		return $db->fetchObjectRows ( '
			SELECT ID as `GroupID` FROM `Groups`
		' );
	}
	if ( $permission )
	{
		$permission = '
		op.' . $permission . ' > 0 AND'; 
	}
	$query = '
		SELECT op.ObjectID as `GroupID` FROM ObjectPermission op, UsersGroups ug 
		WHERE 
		op.PermissionType = "admin" AND 
		op.Read > 0 AND 
		op.ObjectType = "Groups" AND
		op.AuthType = "Groups" AND
		op.AuthID = ug.GroupID AND' . $permission . '
		ug.UserID = \'' . $user->ID . '\'
		GROUP BY ObjectID;
	';
	
	return $db->fetchObjectRows ( $query );
		
}

function function_main ( $options )
{				
	$self =& $this;
	require_once ( "functions/main.php" );
}

function function_list_users ( $options )
{
	$self =& $this;
	require_once ( "functions/list_users.php" );
	ob_clean ( );
	die ( $this->template->render ( ) );
}

function function_edit_user ( $options )
{
	global $Session;
	$usr = new dbUser ( );
	if ( $usr->load ( $options[ 'uid' ] ) )
	{
		// Get objects
		$this->user = $usr;
		$this->template->hasUsername = $this->config->get ( "hasUsername" );
		$this->template->objects = $this->get_objects_template();
		$this->template->user = $usr;
		$Session->Set ( UsersCurrentUser,  $usr->ID );
	}
	else
	{
		$usr = new dbUser ( );
		$this->user = $usr;
		$this->template->hasUsername = $this->config->get ( "hasUsername" );
		$this->template->objects = $this->get_objects_template();
		$this->template->user = $usr;
		$Session->Set ( 'UsersCurrentUser', $usr->ID );
	}
}

/**
*** Export CSV (can be overridden in extensions/arena/users/function_export.php)
***
***/

function function_export ( $options )
{
	if ( file_exists ( "extensions/arena/users/function_export.php" ) )
	{
		include_once ( "extensions/arena/users/function_export.php" );
	}
	else
	{
		$obj = new dbObject ( "Users" );
		$obj->addClause ( "ORDER BY Name" );
		if ( $objs = $obj->find ( ) )
		{
			$output = i18n( "Name" ) . ":\t" . i18n ( "Username" ) . ":\t" . 
								i18n( "Email" ) . ":\t" . i18n ( "Date Created" ) . ":\t" . 
								i18n( "Last login" ) . ":\t" . i18n( "Address" ) . ":\t" . 
								i18n( "Postcode" ) . ":\t" . i18n( "City" ) . ":\t" . 
								i18n( "Telephone" ) . ":\t" . i18n( "Mobile" ) . ":\n\n";
			foreach ( $objs as $obj )
			{
				$output .= 	"{$obj->Name}\t{$obj->Username}\t{$obj->Email}\t" . 
										"{$obj->DateCreated}\t{$obj->DateLastLogin}\t" .
										"{$obj->Address}\t{$obj->Postcode}\t{$obj->City}\t" .
										"{$obj->Telephone}\t{$obj->Mobile}\n";
			}
		}
	}
	ob_clean ( );
	header ( "Content-type: application/octet-stream" );
	header ( "Content-disposition: inline; filename=\"export.csv\"" );
	die ( $output );
}


/**
***	AJAX EDIT ***
***						**/

// Remove an avatar
function function_ajax_removeavatar ( $options )
{
	$user = new dbUser ( );
	$user->ID = $options[ 'uid' ];
	$user->load ( );
	if ( $user->dbImage )
		$user->dbImage->delete ( );
	$user->Image = 0;
	$user->save();
}

function function_ajax_showgroupobjects ( $options )
{
	$this->template->group = new dbObjectConnectable ( "Groups" );
	if ( $this->template->group->load ( $options[ 'gid' ] ) )
	{
		$this->template->objects = $this->template->group->getObjects ( );
		if ( !count ( $this->template->objects ) ) $this->template->objects = false;
	}
	else $this->template->objects = false;
}

function function_ajax_editgroup ( $options )
{
	$this->template->group = new dbObject ( "Groups" );
	$this->template->group->load ( $options [ 'id' ] );			
	$this->template->config =& $GLOBALS [ 'conf' ];			
	$this->template->modules =& $GLOBALS [ 'moduleNames' ];			
}

function function_ajax_newuser ( $options )
{
	$this->loadCurrentGroup ( $options );
	$this->template->currentGroup =& $this->currentGroup;
}

function function_ajax_edituser ( $options )
{
	if ( !$options ['id'] )	  return false;
	
	$this->template->user = new dbUser ();			
	$this->template->user->load ( $options[ 'id' ] );
	$this->template->hasUsername = $this->config->get ( "hasUsername" );
	
}

function function_ajax_user ( $options )
{
	if ( !$options['id'] )    return false;
	
	$this->template = new cPTemplate ( "{$this->dir}/templates/user.php" );
	$this->template->user = new dbUser ();
	$this->template->user->load ( $options['id'] );
}

function function_ajax_objects ( $options )
{
	if ( !$this->user )	$this->loadUser ();
	$this->template = $this->get_objects_template();
}	

/////////////////////////////////////////////////////////
////     INTERFACES                                  ////
/////////////////////////////////////////////////////////


/**	
*** Interface for textpages module
**/
function interface_textpages ()
{
}

function interface_core ( )
{
}


/**
***	Funcs
**/

function get_objects_template (  )
{
	$template = new cPTemplate ( "{$this->dir}/templates/objects.php" );
	if ( $objects = $this->user->getObjects ( ) ) foreach ( $objects as $object )
	{
		$objTpl = new cPTemplate ( "{$this->dir}/templates/user_object.php" );
		$objTpl->object = $object;
		$template->objects .= $objTpl->render();
	}
	return $template;
}

?>
