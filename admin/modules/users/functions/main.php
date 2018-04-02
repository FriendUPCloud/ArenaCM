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



global $Session;
$module = new cPTemplate ( "$tplDir/main.php" );

$db =& dbObject::globalValue ( 'database' );

include_once ( ClassDir . 'time/ctime.php' );
include_once ( 'admin/modules/users/include/main_funcs.php' );

/** 
 * Get some variables
**/
if ( trim ( $_REQUEST[ 'collectionid' ] ) )
{
	if ( $_REQUEST[ 'collectionid' ] == 'none' )
		$Session->del ( 'UsersCollectionID' );
	else $Session->set ( 'UsersCollectionID', $_REQUEST[ 'collectionid' ] );
}
if ( trim ( $_REQUEST[ 'gid' ] ) )
{
	switch ( $_REQUEST[ 'gid' ] )
	{
		case 'all':
			$Session->Set ( 'UsersCurrentGroup', 'all' );
			break;
		case 'orphans':
			if ( $Session->AdminUser->_dataSource == 'core' )
			{
				$Session->Set ( 'UsersCurrentGroup', 'orphans' );
			}
			break;
		case 'inactive':
			$Session->Set ( 'UsersCurrentGroup', 'inactive' );
			break;
		default:
			$group = new dbObject ( 'Groups' );
			if ( $group->load ( $_REQUEST[ 'gid' ] ) )
			{
				if ( $Session->AdminUser->checkPermission ( $group, 'Read', 'admin' ) )
				{
					$Session->Set ( 'UsersPosition', 0 );
					$Session->Set ( 'UsersCurrentGroup', $_REQUEST[ 'gid' ] );
					if ( !$Session->UsersCurrentGroup )
						$Session->Set ( 'UsersCurrentGroup', 'all' );
				}
			}
			else $Session->Set ( 'UsersCurrentGroup', 'all' );
			break;		
	}
}
if ( isset ( $_REQUEST[ 'pos' ] ) || $_REQUEST[ 'pos' ] === 0 )
	$Session->Set ( 'UsersPosition', $_REQUEST[ 'pos' ] );
if ( $Session->UsersPosition <= 0 )
	$Session->Set ( 'UsersPosition', 0 );
if ( $_REQUEST[ 'limit' ] )
	$Session->Set ( 'UsersLimit', $_REQUEST[ 'limit' ] );
if ( !$Session->UsersLimit ) $Session->Set ( 'UsersLimit', 20 );
$limit = $Session->UsersLimit;
$module->limit = $limit;
$pos = $Session->UsersPosition;
if ( $pos <= 0 ) $pos = '0';
$count = 0;
if ( $_REQUEST[ 'export' ] )
{
	$limit = 999999999; $pos = 0;
}
if ( $_REQUEST[ 'sortfield' ] )
{
	if ( $Session->UsersSortField == $_REQUEST[ 'sortfield' ] )
		$Session->Set ( 'UsersSortField', $_REQUEST[ 'sortfield' ] . 'Inv' );
	else $Session->Set ( 'UsersSortField', $_REQUEST[ 'sortfield' ] );
	ob_clean ( );
	header ( 'Location: admin.php?module=users' );
	die ( );
}
if ( !$Session->UsersSortField )
	$Session->Set ( 'UsersSortField', 'Username' );

/**
 * Get users by listmode
 * Orphans: groupless users
 * Inactive: users which have been deactivated
 * All: :-)
 * Or by Group ID
**/

$where = Array ( );
$select = '';

if ( $Session->UsersCollectionID )
{
	$select = 'SELECT u.* FROM Users u, ObjectConnection o, UserCollection c';
	$where[] = '(c.ID = o.ObjectID AND o.ObjectType="UserCollection" AND u.ID = o.ConnectedObjectID AND o.ConnectedObjectType="Users" AND c.ID=' . $Session->UsersCollectionID . ')';
	$g = new dbObject ( 'UserCollection' );
	$g->load ( $Session->UsersCollectionID );
	$module->groupChoice = ' i enheten "' . $g->Name . '"';
}
else
{
	switch ( $Session->UsersCurrentGroup )
	{
		case 'orphans':
			if ( $Session->AdminUser->_dataSource == 'core' )
			{
				$Session->Set ( 'UsersCurrentGroup', 'orphans' );
				$select = 'SELECT u.*, ug.GroupID FROM Users u LEFT JOIN UsersGroups ug ON ( ug.UserID = u.ID )';
				$where[] = 'GroupID IS NULL';
			}
			$module->groupChoice = 'uten gruppe';
			break;
		
		case 'inactive':
			// Root users se all
			if ( $Session->AdminUser->_dataSource == 'core' )
			{
				$select = 'SELECT u.* FROM Users u ';
				$where[] = 'u.IsDisabled';
			}
			// Other users sees inactive amoung the ones they have access too
			else
			{
				if ( $authorizedGroups = getAuthorizedGroups ( $Session->AdminUser ) )
				{
					$groups = Array ( );
					foreach ( $authorizedGroups as $ag ) 
						$groups[] = '( u.ID = ug.UserID AND ug.GroupID = \'' . $ag->GroupID . '\' )';
					$select = 'SELECT u.* FROM Users u, UsersGroups ug';
					$where[] = '(' . implode ( ' OR ', $groups ) . ')';
					$where[] = 'u.IsDisabled';
				}
			}
			$module->groupChoice = 'som er inaktive';
			break;
		
		default:
			// We want users from a specific group 
			if ( is_numeric ( $Session->UsersCurrentGroup ) )
			{
				$group = new dbObject( 'Groups' );
				//if ( $group = dbObject::get ( $Session->UsersCurrentGroup, 'Groups' ) )
				if ( $group->get( $Session->UsersCurrentGroup ) )
				{
					$module->groupChoice = ' i gruppen "' . $group->Name . '"';
					if ( $Session->AdminUser->checkPermission ( $group, 'Read', 'admin' ) )
					{
						function fetchSubGroups ( $pid = 0 )
						{
							$db =& dbObject::globalValue ( 'database' );
							$gq = Array ( );
							if ( $rows = $db->fetchObjectRows ( 'SELECT * FROM Groups WHERE GroupID=' . $pid ) )
							{
								foreach ( $rows as $row )
								{
									$gq[] = "(ug.GroupID = '" . $row->ID . "')";
									if ( $qt = fetchSubGroups ( $row->ID ) )
										array_merge ( $gq, $qt );
								}
								return $gq;
							}	
						}
						$select = 'SELECT u.* FROM Users u, UsersGroups ug';
						$where[] = 'u.ID = ug.UserID';
						$gq = Array ( "(ug.GroupID = '" . $Session->UsersCurrentGroup . "')" );
						if ( $subgroups = fetchSubGroups ( $Session->UsersCurrentGroup ) )
						{
							$gq = array_merge ( $gq, $subgroups );
						}
						$where[] = '(' . implode ( ' OR ', $gq ) . ')';
					}
				}
				$module->groupChoice = 'fra "' . $group->Name . '"';
			}
			// We want all users and we're a core user
			else if ( $Session->AdminUser->_dataSource == 'core' )
			{
				$select = 'SELECT u.* FROM Users u';
				$Session->Set ( 'UsersCurrentGroup', 'all' );
				$module->groupChoice = 'fra hele databasen';
			}
			// We want all users and we aren't a core user
			else
			{
				if ( $authorizedGroups = getAuthorizedGroups ( $Session->AdminUser ) )
				{
					$groups = Array ( );
					foreach ( $authorizedGroups as $ag ) 
						$groups[] = '( u.ID = ug.UserID AND ug.GroupID = \'' . $ag->GroupID . '\' )';
					$select = 'SELECT u.* FROM Users u, UsersGroups ug';
					$where[] = '(' . implode ( ' OR ', $groups ) . ')';
				}
				$Session->Set ( 'UsersCurrentGroup', 'all' );
				$module->groupChoice = 'fra hele databasen';
			}
			break;
	}
}

if ( $Session->AdminUser->_dataSource != 'core' )
{
	$where[] = '( u.IsTemplate IS NULL OR u.IsTemplate = 0 )';
}

/**
 * Add search to query
**/
if ( $_REQUEST[ 'keywords' ] )
{
	$keys = explode ( ',', $_REQUEST[ 'keywords' ] );
	for ( $a = 0; $a < count ( $keys ); $a++ )
	{
		$k = trim ( $keys[ $a ] );
		$search[] = "( Username LIKE \"%{$k}%\" OR Name LIKE \"%{$k}%\" OR Email LIKE \"%{$k}%\" )";
	}
	$where[] = ' ( ' . implode ( ' OR ', $search ) . ' ) ';
}

/** 
 * Combine vars to a query and list out results to template
**/
if ( $select )
{
	$query = "$select " . ( count ( $where ) ? 'WHERE ' : '' ) . 
				 implode ( ' AND ', $where ) . ' GROUP BY u.ID';
	
	// Find out about sort column
	switch ( $Session->UsersSortField )
	{
		case 'DateLogin':
			$sort = 'ORDER BY DateLogin ASC';
			break;
		case 'DateLoginInv':
			$sort = 'ORDER BY DateLogin DESC';
			break;
		case 'DateCreated':
			$sort = 'ORDER BY DateModified ASC';
			break;
		case 'DateCreatedInv':
			$sort = 'ORDER BY DateModified DESC';
			break;
		case 'SortOrder':
			$sort = 'ORDER BY SortOrder ASC';
			break;
		case 'SortOrderInv':
			$sort = 'ORDER BY SortOrder DESC';
			break;
		case 'UsernameInv':
			$sort = 'ORDER BY u.Username DESC, u.Email DESC, u.Name DESC';
			break;
		case 'Username':
		default:
			$sort = 'ORDER BY u.Username ASC, u.Email ASC, u.Name ASC';
			break;
	}
	$query .= ' ' . $sort;
}
else $query = '';
if ( $query )
{
	if ( $_REQUEST[ 'export' ] )
	{
		$ExportFields = array ( 'Navn'=>'Name', 'E-post'=>'Email', 'Adresse'=>'Address', 'Postnummer'=>'Postcode', 'Poststed'=>'City' );
		$export = array ( "Navn:\tE-post:\tAdresse:\tPostnummer:\tPoststed:" );
	}
		
	list ( $count, ) = $db->fetchRow ( 'SELECT COUNT(*) FROM ( ' . $query . ' ) AS z' );
	if ( $users = $db->fetchObjectRows ( "$query LIMIT $pos, $limit" ) )
	{
		$lim = count ( $users );
		$utpl = new cPTemplate ( "$tplDir/user_row.php" );
		$time = new cTime ( );
		for ( $a = 0; $a < $lim; $a++ )
		{
			$utpl->sw = $utpl->sw == 1 ? 2 : 1;
		
			/**
			 * Group membership
			**/
			
			$utpl->InGroups = '';
			if ( $groups = $db->fetchObjectRows ( "
				SELECT g.ID, g.Name FROM UsersGroups ug, Groups g WHERE ug.GroupID = g.ID AND ug.UserID='" . $users[ $a ]->ID . "' ORDER BY g.Name
			" ) )
			{
				$canRead = true;
				$canWrite = true;
				foreach ( $groups as $g )
				{
					$utpl->InGroups .= $g->Name . ', ';
					$g->_tableName = 'Groups';
					$g->_primaryKey = 'ID';
					$g->_isLoaded = true;
					if ( !$Session->AdminUser->checkPermission ( $g, 'Read', 'admin' ) )
						$canRead = false;
					if ( !$Session->AdminUser->checkPermission ( $g, 'Write', 'admin' ) )
						$canWrite = false;
				}
				$utpl->InGroups = substr ( $utpl->InGroups, 0, strlen ( $utpl->InGroups ) - 2 );
				if ( strlen ( $utpl->InGroups ) > 23 ) $utpl->InGroups = substr ( $utpl->InGroups, 0, 21 ) . '..';
				if ( !$utpl->InGroups ) $utpl->InGroups = 'Ingen';
			}
		
			list ( $utpl->canRead, $utpl->canWrite, ) = checkUserPermissions ( $users[ $a ] );
		
			/**
			 * Time
			**/
			$utpl->DateLogin = ( strstr (  $users[ $a ]->DateLogin, '1970' ) || !$users[ $a ]->DateLogin ) ? 
				i18n ( 'Never logged in.' ) : ArenaDate ( DATE_FORMAT, $users[ $a ]->DateLogin );
			$utpl->DateCreated = ArenaDate ( DATE_FORMAT, $users[ $a ]->DateCreated );
			if ( !$users[ $a ]->DateModified )
			{
				$date = date ( 'Y-m-d H:i:s' );
				$db->query ( 'UPDATE Users SET DateModified = NOW() WHERE ID=' . $users[ $a ]->ID );
				$users[ $a ]->DateModified = $date;
			}
			$utpl->DateModified = ArenaDate ( DATE_FORMAT, $users[ $a ]->DateModified );
			
			/**
			 * Add user data object to user row template
			**/
			$utpl->data = $users[ $a ];
		
			if ( $ExportFields )
			{
				$exrow = array ( );
				foreach ( $ExportFields as $va=>$ke )
				{
					$exrow[] = $users[ $a ]->$ke;
				}
				$export[] = implode ( "\t", $exrow );
			}
		
			/**
			 * Render the user row template
			**/
			$oStr .= $utpl->render ( );
		}
		
		/**
		 * Output export data
		**/
		if ( $_REQUEST[ 'export' ] )
		{
			header ( 'Content-type: application/octet-stream' );
			header ( 'Content-Disposition: attachment; filename="Brukereksport_' . date ( 'Y-m-d' ) . '.csv"' );
			die ( implode ( "\n", $export ) );
		}
		$module->userlist = $oStr; 
		unset ( $oStr );
	}
	else $query = '';
}
if ( !$query )
{
	$module->userlist = "<tr class=\"sw1\"><td colspan=\"8\">Ingen brukere finnes her.</td></tr>";
}

/**
 * Connect groups data to module, and respect user permissions
**/
$groups = new dbObject ( "Groups" );
$groups->addClause ( "ORDER BY", "SortOrder ASC, Name ASC" );
$groups = $groups->find ();
$module->groups = $groups;
$module->authGroups = is_array ( getAuthorizedGroups ( $Session->AdminUser , 'Write' ) ) ? true : false;

/**
 * Navigation
**/
include_once ( "lib/classes/pagination/cpagination.php" );
$nav = new cPagination ( );
$nav->Count = $count;
$nav->Position = $pos;
$nav->Limit = $limit;
$module->Navigation = $nav->render ( );

?>
