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
*** User model
***
*** The primary function of dbUser is to manage authentication and access privileges.
*** dbUser provides several authentication methods, see the authenticate() method
*** for more details.
*** 
*** @see dbUser::authenticate()
*** @author Inge Jørgensen <inge@blest.no>
*** @author Hogne Titlestad <hogne@blest.no>
*** @package arena-lib
*** @copyright Copyright (c) 2005 Blest AS                                     
***                                                                             
**/

/**
*** Needs this
**/
include_once ( 'dbImage.php' );

class dbUser extends dbObject
{	
	// options
	var $use_cookies            = true;      // use cookie based authentication
	var $block_session_fixation = true;      // block session fixation by checking the token cookie. 
	var $lock_session_to_ip     = false;     // block session fixation by checking the user's ip address.
	
	// attributes
	var $_tableName             = 'Users';
	var $is_authenticated       = 0;         // authentication state
	var $rights                 = false;     // storage container for rights
	var $permissions            = array ();  // storage container for permissions
	var $_dataSource            = false;     // reflects if it's from site or core database
	var $_token 				= '';
	var $_encryptionMethod;
	
	var $Image = NULL;
	var $groups = NULL;
	var $IsDisabled = NULL;
	var $DateCreated = NULL;
	var $cookie_prefix = NULL;


	/**
	*** Constructor
	*** @param string $domain Domain name for cookies, defaults to $_SERVER ['SERVER_NAME'];
	*** @param string $path   Path for cookies, defaults to "/";
	**/
	function __construct ( $cookie_domain = false, $cookie_path = false )
	{
		global $application;
	
		// Ready cache
		if ( isset( $GLOBALS[ 'Cache' ] ) && !$GLOBALS[ 'Cache' ][ 'SuperAdmin' ] )
			$GLOBALS[ 'Cache' ][ 'SuperAdmin' ] = Array ( );
			
		$this->ip = $_SERVER['REMOTE_ADDR'];
 		$this->loadTable ();
		
		// set cookie settings
		$this->cookie_domain = ( $cookie_domain )               ? $cookie_domain : $_SERVER['SERVER_NAME'];
		$this->cookie_path   = ( $cookie_path   )               ? $cookie_path   : '/';
		$this->cookie_prefix = ( ARENAMODE == 'admin' ) 		? 'arena_'       : 'arenaweb_';
	}
	
	/**
	*** Change database of user
	**/
	function changeDatabase ( $database )
	{
		$this->_dbOverride =& $database;
	}
	
	
	/**
	*** Check the state of the database, insert dummy admin group and user if they don't exist.
	**/
	function checkState ( )
	{
		$database =& $this->getDatabase ( );
	
		$groupCount = $database->fetchRow ( 'SELECT COUNT(ID) as Count FROM Groups' );
		$groupCount = $groupCount [ 'Count' ];
		if ( $groupCount < 1 )
		{
			$group = new dbObject ( 'Groups' );
			$group->Name = 'Administratorer';
			$group->Level = 'unlimited';
			$group->save ();
		}
	
		$userCount = $database->fetchRow ( 'SELECT COUNT(ID) as Count FROM Users' );
		$userCount = $userCount [ 'Count' ];
		
		// Insert default admin user if no users exists
		if ( $userCount < 1 )
		{
			$group = new dbObject ( 'Groups' );
			$group->addClause ( 'ORDER BY', 'ID ASC' );
			$group = $group->findSingle ();
			$user = new dbUser ();
			$user->Username = 'admin';
			$user->Name = 'Administrator';
			$user->Email = '';
			$user->Password = $this->hash( 'admin' );
			$user->isAdminUser = true;
			$user->setGroupsById ( array ( $group->ID) );
			$user->save ();
		}
	}	
	
	
	
	function &get ( $key = false, $tableName = false )
	{
		return parent::get ( $key, 'Users' );
	}
	
	
	/////////////////////////////////////////////////////////
	////     AUTHENTICATION / PASSWORD CODE              ////
	/////////////////////////////////////////////////////////
	
	function get_vars ( $prefix = 'login', $postfix = '' )
	{
		// Get user data from POST vars (when logging in)
		if ( isset ( $_POST[ "{$prefix}Username" ] ) && isset ( $_POST[ "{$prefix}Password" ] ) )
		{
			$this->Username = $_POST[ "{$prefix}Username" ];
			$this->Password_unhash = $_POST[ "{$prefix}Password" ];
			// Protect against sql injection
			$this->Username = MakeSafePost ( $this->Username );
			$this->Password_unhash = MakeSafePost ( $this->Password_unhash );
			return true;
		}
		// Get user data from session (when we have already logged in)
		else if ( $_SESSION[ "{$this->cookie_prefix}Username" ] && $_SESSION[ "{$this->cookie_prefix}Password" ] )
		{
			$this->Username = $_SESSION[ "{$this->cookie_prefix}Username" ];
			$this->Password = $_SESSION[ "{$this->cookie_prefix}Password" ];
			return true;
		}
		// Get userdata from cookie (usually when session timed out)
		else if ( $_COOKIE[ "{$this->cookie_prefix}UserToken" ] )
		{
			$obj = new dbObject ( 'UserLogin' );
			$obj->Token = $_COOKIE[ "{$this->cookie_prefix}UserToken" ];
			
			if ( $obj->load ( ) )
			{
				if ( !$obj->UserID ) 
				{
					$obj->delete ( );
					return false;
				}
				
				$tokenuser = new dbUser ( );
				if ( $obj->DataSource == 'core' )
					$tokenuser->setDatabase ( $GLOBALS[ 'corebase' ] );
				else $tokenuser->setDatabase ( $GLOBALS[ 'database' ] );
				if ( $tokenuser->load ( $obj->UserID ) )
				{
					$this->Username = $tokenuser->Username;
					$this->Password = $tokenuser->Password;
				}
				// Something is wrong!
				else 
				{
					$obj->delete ( );
					return false;
				}	
				session_regenerate_id ( );
				return true;
			}
		}
		return false;
	}

	
	function escape_values ()
	{
		foreach ( array( 'Username', 'Password', 'token' ) as $var )
			$this->{$var} = $this->escape( $this->{$var} );
	}
	
	/**
	 * Check if the user is a super user
	**/
	function isSuperUser ( )
	{
		if ( $this->_dataSource == 'core' )
		{
			return true;
		}
		if ( isset( $GLOBALS[ 'Cache']['SuperAdmin'][$this->ID] ) && $GLOBALS[ 'Cache']['SuperAdmin'][$this->ID] )
		{
			return $GLOBALS[ 'Cache']['SuperAdmin'][$this->ID];
		}
		if ( !$this->ID )
			return false;
		if ( !$this->groups )
			$this->loadGroups ( );
		if ( $this->groups ) 
		{
			foreach ( $this->groups as $group )
			{
				if ( $group->SuperAdmin )
				{
					$GLOBALS[ 'Cache']['SuperAdmin'][$this->ID] = true;
					return true;
				}
			}
		}
		return false;
	}
	
	function GetToken ( )
	{
		if ( $this->is_authenticated )
		{
			return ( isset( $_COOKIE[ "{$this->cookie_prefix}UserToken" ] ) ? $_COOKIE[ "{$this->cookie_prefix}UserToken" ] : $this->_token );
		}
		else
		{
			return '';
		}
	}
	
	/**
	 * Reauthenticate user with given username and password
	**/
	function reauthenticate ( $username, $password )
	{
		if ( !$username || !$password ) return false;
		$this->Username = $username;
		$this->Password_unhash = $password;
		unset ( $this->Password );
		$this->authenticate ( $this->_prefix, $this->_postfix, true );
	}
	
	/**
	 * Authenticate a user (this is usually called by the system)
	**/
	function authenticate ( $prefix = 'login', $postfix = '', $skipgetvars = false )
	{	
		if ( !$skipgetvars )
			$this->get_vars ( $prefix, $postfix );
		
		for ( $a = 0; $a < 2; $a++ )
		{
			switch ( $a )
			{
				// Try against site database
				case 0:
					$database =& $GLOBALS[ 'database' ];
					$this->_dataSource = 'site';
					break;
				// Try against core database
				case 1:
					$database =& $GLOBALS[ 'corebase' ];
					if ( !$database ) return false;
					$this->_dataSource = 'core';
					break;
				default: break;
			}
			
			if ( isset ( $this->Username ) )
				$username = $this->Username;
			if ( isset ( $this->Password ) )
				$password = $this->Password;
			else if ( $this->Password_unhash )
				$password = $this->setPassword ( $this->Password_unhash );
			
			if ( !isset ( $username ) || !isset ( $password ) )
			{
				return false;
			}
			
			$query = "SELECT * FROM Users WHERE Username='{$username}' AND Password='$password' LIMIT 1";
			
			if ( $row = $database->fetchObjectRow ( $query ) )
			{
				$_SESSION[ "{$this->cookie_prefix}Username" ] = $username;
				$_SESSION[ "{$this->cookie_prefix}Password" ] = $password;

				// Register when we logged in
				$this->_dbOverride =& $database;
				$this->load ( $row->ID );
				
				// If we're disabled, return false
				if ( $this->IsDisabled )
				{
					foreach ( $this as $k=>$v )
						unset ( $this->$k );
					$this->is_authenticated = false;
					return false;
				}
				$this->save ( );
				
				$token = ( isset( $_SESSION[ "{$this->cookie_prefix}UserToken" ] ) ? $_SESSION[ "{$this->cookie_prefix}UserToken" ] : $_COOKIE[ "{$this->cookie_prefix}UserToken" ] );
				
				$this->is_authenticated = true;
				$obj = new dbObject ( 'UserLogin' );
				$obj->Token = $token;
				
				if ( !$obj->load ( ) )
				{
					// Update date login
					$this->DateLogin = date ( 'Y-m-d H:i:s' );
					$this->save ( );
					
					// Set token
					$database->query ( "DELETE FROM UserLogin WHERE UserID='{$this->ID}' AND ( DataSource='{$this->_dataSource}' OR DataSource IS NULL )" );
					$obj = new dbObject ( 'UserLogin' );
					$obj->Token = $this->make_token ( );
					$obj->UserID = $this->ID;
					$obj->DateCreated = date( 'Y-m-d H:i:s' );
					
					setcookie ( "{$this->cookie_prefix}UserToken", $obj->Token, time() + 2592000, '/' );
					
					$_SESSION[ "{$this->cookie_prefix}UserToken" ] = $obj->Token;
				}
				$obj->DataSource = $this->_dataSource;
				$obj->DateExpired = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date('m'), date('d')+3, date('Y') ) );
				$obj->save ( );
				return $this->is_authenticated;
			}
			else 
			{
				$this->is_authenticated = false;
			}
		}
		return $this->is_authenticated;
	}	

	function make_token ()
	{
		return $this->_token = md5( microtime() . date( 'r' ) . rand( 10000, 32000 ) );
	}

	function hash ( $var )
	{
		if ( defined ( 'DBUSER_HASH_INCLUDE_USERNAME' ) && DBUSER_HASH_INCLUDE_USERNAME === true && $this->_dataSource != 'core' ) 
			$var = "{$this->Username}\n{$var}"; // moronic hashing alogritms makes baby jesus cry: "eZ suxxors!"
		return md5( $var );
	}
		
	function makePassword ()
	{
		$template = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$length = rand ( 5, 7 );
		for ($a = 0; $a <= $length; $a++) 
		{
			$b = rand(0, strlen($template) - 1);
			$rndstring .= $template[$b];
		}
		$this->Password        = $this->hash ( $rndstring );
		$this->Password_unhash = $rndstring;
		return true;
	}

	function escape ( $var )
	{
		global $database;
		
		$var = ( get_magic_quotes_gpc() ) ? stripslashes ( $var ) : $var;
		return mysqli_escape_string ( $database->resource, $var );
	}
	
	function logout ( )
	{
		global $database;
		$this->is_authenticated = false;
		foreach ( $_COOKIE as $k=>$v )
		{			
			if ( substr ( $k, 0, strlen ( $this->cookie_prefix ) ) == $this->cookie_prefix )
			{
				unset ( $_COOKIE[ $k ] );
				setcookie( $k, '', mktime ( 12,0,0,1, 1, 1970 ), '/' );
			}
		}
		$database->query ( 'DELETE FROM UserLogin WHERE UserID=\'' . $this->ID . '\' AND ( DataSource=\'' . $this->_dataSource . '\' OR DataSource IS NULL )' );
		if ( session_id() ) session_destroy();
	}
	
	function onLoadedExtraFields ( $options = false, $r = 0 )
	{
		// Try to cache template user
		if ( !isset( $GLOBALS[ 'templateuser' ] ) || !$GLOBALS[ 'templateuser' ] )
		{
			$templateUser = new dbUser ( );
			$templateUser->IsTemplate = 1;
			if ( $templateUser = $templateUser->findSingle ( ) )
			{
				if ( $templateUser->ID == $this->ID ) return;
				$templateUser->loadExtraFields ( );
				$GLOBALS[ 'templateuser' ] =& $templateUser;
			}
		}
		// Make sure we have the correct fields from template user
		if ( $u =& $GLOBALS[ 'templateuser' ] )
		{
			$saved = 0;
			foreach ( $u as $k=>$v )
			{
				if ( substr ( $k, 0, 7 ) == '_field_' )
				{
					if ( !key_exists ( $v->Name, $this ) )
					{
						$o = new dbObject ( key_exists ( 'DataInt', $v ) ? 'ContentDataSmall' : 'ContentDataBig' );
						$o->load ( $v->ID );
						unset ( $o->ID );
						$o->ContentID = $this->ID;
						$o->save ( );
						$saved++;
					}
				}
			}
			if ( $saved > 0 ) $this->reloadExtraFields ( );
		}
	}
	
	/////////////////////////////////////////////////////////
	////     DBOBJECT HOOKS                              ////
	/////////////////////////////////////////////////////////
	
	function onSave ()
	{
		global $user;
		if ( !$this->exists () )
		{
			if ( $user->ID ) $this->CreatedBy = $user->ID;
		}
		
		/**
		 * Clean up and verify usersgroups connection 
		**/
		$db =& $this->getDatabase ( );
		if ( $row = $db->fetchObjectRow ( "SELECT * FROM UsersGroups WHERE UserID='{$this->ID}'" ) )
			$this->InGroups = '1';
		else $this->InGroups = '0';
		if ( !$this->DateCreated ) $this->DateCreated = 'NOW()';
		if ( !$this->Username && $this->Email ) $this->Username = $this->Email;
	}
	
	function onSaved ()
	{
		// Ensure login
		global $user;
		if ( $user && $user->ID && ( $this->ID == $user->ID ) && @!$_REQUEST[ 'logout' ] )
		{
			if ( isset( $_SESSION["{$this->cookie_prefix}password"] ) ) $_SESSION["{$this->cookie_prefix}password"] = $this->Password;
			setcookie ( "{$this->cookie_prefix}username", $this->Username, time() + 2592000, '/' );
		}
		$this->saveGroups ();
		$this->saveExtraFields ();
	}
	
	function onLoaded ()
	{
		if ( $this->Image )
		{
			$this->dbImage = new dbImage ( $this->Image );
		}
		$this->loadGroups ();
	}
	
	function onDeleted ()
	{
		$database =& $this->getDatabase ( );
		if ( is_array ( $this->groups ) && $this->ID )
		{
			$database->query ( 'DELETE FROM UsersGroups WHERE UserID = ' . $this->ID );
		}
		$this->deleteVerificationString ( );
		$this->deleteExtraFields ( );
	}
	
	function deleteVerificationString ( )
	{
		$database =& $this->getDatabase ( );
		$database->query ( 'DELETE FROM Setting WHERE SettingType="useraccount_verification" AND `Key`=\'' . $this->ID . '\'' );
	}
	
	function deleteExtraFields ( )
	{
		$database =& $this->getDatabase ( );
		$database->query ( 'DELETE FROM ContentDataBig WHERE ContentID=\'' . $this->ID . '\' AND ContentTable="Users"' );
		$database->query ( 'DELETE FROM ContentDataSmall WHERE ContentID=\'' . $this->ID . '\' AND ContentTable="Users"' );
	}
	
	/////////////////////////////////////////////////////////
	////     GROUP RELATIONS                             ////
	/////////////////////////////////////////////////////////
	
	
	
	/**
	*** Set groups which this user belong to
	*** @param $groups array Group IDs
	*** @return bool false on failure
	**/
	function setGroupsById ( $groups )
	{
		if ( is_array ( $groups ) )
		{
			$this->groups = array ();
			foreach ( $groups as $groupID )
			{
				$group = new dbObject ( 'Groups' );
				if ( $group->load ( $groupID ) )
					$this->groups[] = $group;
			}
			return true;
		}
		return false;
	}
	
	/**
	*** Load group associations
	**/
	function loadGroups ()
	{
		if ( !$this->groups ) 
		{
			$groups = new dbObject ( 'Groups' );
			$groups = $groups->find ( 'SELECT g.* FROM `Groups` g, `UsersGroups` ug WHERE ug.GroupID = g.ID AND ug.UserID = ' . $this->ID );
			if ( is_array ( $groups ) )
			{
				$this->groups = $groups;
				foreach ( $groups as $group )
				{
					$rights = isset( $group->Modules ) ? $group->Modules : false;
					if ( isset( $group->Level ) && $group->Level == 'unlimited' )
					{
						$this->rights = 'all';
					}
					else if ( $this->rights != 'all' && $rights )
					{
						$this->rights = array_merge ( $this->rights, $rights );						
					}	
				}
				return true;
			}
		}
		return false;
	}
	
	
	
	/**
	*** Save group associations
	**/
	function saveGroups ()
	{
		$database = &$this->getDatabase ( );
		
		if ( is_array ( $this->groups ) && $this->ID )
		{
			$database->query ( 'DELETE FROM UsersGroups WHERE UserID = ' . $this->ID );
			foreach ( $this->groups as $group )
			{
				$database->query ( "INSERT INTO UsersGroups (UserID, GroupID) VALUES ( {$this->ID}, {$group->ID} )" );
			}
			$this->GroupID = 0;
			return true;
		}
		else
			return false;
	}
	
	
	
	/**
	*** Check if user is a member of group
	*** @param mixed $groupID Either a numeric ID or a Group dbObject
	*** @return boolean true if user is a member of specified group.
	**/
	function inGroup ( $groupID )
	{
		if ( !is_numeric ( $groupID ) && !is_object ( $groupID ) )
		{
			$group = new dbObject ( 'Groups' );
			$group->Name = $groupID;
			if ( !$group->load ( ) )
				return false;
			$groupID = $group->ID;
		}
		if ( !$this->groups ) $this->loadGroups ();
		if ( is_array ( $this->groups ) )
		{
			foreach ( $this->groups as $group )
			{
				if ( is_numeric ( $groupID ) && $group->ID == $groupID )
					return true;
				if ( is_object ( $groupID ) && $group->ID == $groupID->ID )
					return true;
			}
		}
		return false;
	}
	
	/////////////////////////////////////////////////////////
	////     PERMISSIONS                                 ////
	/////////////////////////////////////////////////////////
	
	/**
	*** Check if user has write permissions on $key
	*** @param string $key
	*** @return bool true if user can write
	**/
	function canWrite ( $key )
	{
		if ( $this->rights == 'all' || $this->_dataSource == 'core' )
			return true;
			
		return $this->rights [ $key ] [ 'write' ];
	}

	/**
	*** Check if user has read permissions on $key
	*** @param string $key
	*** @return bool true if user can write
	**/
	function canRead ( $key )
	{
		if ( $this->rights == 'all' || $this->_dataSource == 'core' )
			return true;
			
		return ($this->canWrite($key)) ? $this->canWrite($key): $this->rights [ $key ] [ 'read' ];
	}


	function loadPermissions ()
	{
		if ( !is_array ( $this->groups ) )   return false;
		
		foreach ( $this->groups as $group )
		{
			$permissions           = new dbObject ( 'GroupPermissions' );
			$permissions->ID_Group = $group->ID;
			$permissions = $permissions->find ();
			
			if ( is_array ( $permissions ) ) foreach ( $permissions as $permission )
			{
				$permission->Value = ( unserialize( $permission->Value ) ) ? unserialize( $permission->Value ) : $permission->Value;  // unserialize value if necessary
				if ( !isset ( $this->permissions[$permission->Name] ) || $this->permissions[$permission->Name] < $permission->Value )
				{
					$this->permissions[$permission->Name] = $permission->Value;
				}
			}
		}
	}
		
	function permission ( $key )
	{
		if ( count( $this->permissions ) < 1 ) $this->loadPermissions ();
		$permission = false;
		if ( isset( $this->permissions[$key] ) )    $permission = $this->permissions[$key];
		return $permission;
	}
	
	/**
	 * Get the state or a tree of a permission, optionally in a specific module
	 * If $object is specified, then it will check for permissions on the specific object
	**/
	function modulePermission ( $permission = false, $module = false, $object = false )
	{
		global $Session, $modulename;
		
		// Core user has all rights!
		if ( $Session->AdminUser->_dataSource == 'core' && $permission )
		{
			// Negative permissions has Restrict in the name
			// and returns false on success (not restricted)
			if ( strstr ( $permission, 'Restrict' ) )
				return false;
			// Positive permissions return true on success
			return true;
		}
		
		if ( !$module )
		{
			$module = $modulename;
		}
		
		// Special case on the ContentElement (also check parent elements if returned false)
		// Commented out, because it is illogical!! -Hogne
		/*if ( $object && ( $object->_tableName == 'ContentElement' || $object->_tableName == 'Folder' ) )
			$checkParents = true;
		else $checkParents = false;*/
		
		/**
		 * Set up member group permissions here
		**/
		
		// Get member groups if not gotten
		if ( !$Session->AdminUser->Groups )
		{
			$groups = new dbObject ( 'Groups' );
			if ( $groups = $groups->find ( '
				SELECT g.* FROM `Groups` g, `UsersGroups` ug WHERE ug.UserID=' . $Session->AdminUser->ID . ' AND g.ID = ug.GroupID
			' ) )
			{
				$Session->AdminUser->Groups = $groups;
			}
		}
		$groups =& $Session->AdminUser->Groups;
		
		// Get all permissions
		if ( is_array ( $groups ) )
		{
			foreach ( $groups as $k=>$v )
			{
				if ( !$groups[ $k ]->Permissions )
				{
					$groups[ $k ]->Permissions = new Dummy ( );
					$settings = new dbObject ( 'Setting' );
					$settings->SettingType = 'GroupAccess_' . $groups[ $k ]->ID;
					if ( $settings = $settings->find ( ) )
					{
						foreach ( $settings as $s )
						{
							$groups[ $k ]->Permissions->{$s->Key} = $s->Value;
						}
					}
				}
			}
		}
		
		$result = false;
		
		// Get a object with all permissions on this module
		if ( !$permission )
		{
			$result = new Dummy ( );
			foreach ( $groups as $group )
			{
				foreach ( $group->Permissions as $k=>$v )
				{
					list ( $mod, $key, $table, $rowid ) = explode ( '_', $k );
					if ( ( ( !$object && !$table ) || ( $object && $object->_tableName == $table && $object->{$object->_primaryKey} == $rowid ) ) )
					{
						if ( $mod == $module ) 
						{
							// Booleans
							if ( is_numeric ( $v ) )
							{
								if ( $v > 0 ) $result->$key = $v;
							}
							// Comma lists
							else if ( is_string ( $v ) && $v != '' && $v != '0' )
							{
								$result->$key .= $v . ',';
							}
						}
					}
				}
			}
			return $result;
		}
		// Only the permission I asked for!
		else
		{
			foreach ( $groups as $group )
			{
				foreach ( $group->Permissions as $k=>$v )
				{
					list ( $mod, $key, $table, $rowid ) = explode ( '_', $k );
					
					if ( ( ( !$object && !$table ) || ( $object && $object->_tableName == $table && $object->{$object->_primaryKey} == $rowid ) ) )
					{
						if ( $mod == $module && $key == $permission )
						{
							// Booleans
							if ( is_numeric ( $v ) && $v > 0 )
							{
								return true;
							}
							// Comma lists
							else if ( is_string ( $v ) && $v != '' && $v != '0' )
							{
								$result .= $v . ',';
							}
						}
					}
				}
			}
		}
		
		// If we're checking parents on an object
		if ( $checkParents && !$result )
		{
			if ( $object )
			{
				
				$pobj = new dbObject ( $object->_tableName );
				switch ( $object->_tableName )
				{
					case 'ContentElement':
						$pobj = $pobj->findSingle ( 'SELECT e.* FROM ContentElement e, ContentElement ee WHERE ee.ID = ' . $object->Parent . ' AND e.MainID != e.ID AND e.MainID = ee.ID' );
						break;
					case 'Folder':
						$pobj = $pobj->findSingle ( 'SELECT * FROM Folder WHERE ID=' . $object->Parent );
						break;
				}
				
				if ( $pobj->{$pobj->_primaryKey} )
					return $this->getPermission ( $permission, $module, $pobj );
				
			}
		}
		return $result;
	}
	
	
	function extensionPermission ( $permission, $extension )
	{
		$db =& $this->getDatabase ();
		$permission = strtoupper($permission[0]).substr($permission,1,strlen($permission)-1);
		
		// Admin user always has access
		if ( $this->_dataSource == 'core' )
			return true;
			
		// Cache group dummy objects
		if ( !$this->_groups )
		{	
			if ( $rows = $db->fetchObjectRows ( '
				SELECT * FROM `Groups` g, `UsersGroups` ug WHERE ug.UserID=' . $this->ID . ' AND g.ID = ug.GroupID
			' ) )
			{
				$this->_groups = $rows;
			}
		}
		// Make a list of groups
		$g = array ();
		foreach ( $this->_groups as $group )
		{
			$g[] = $group->ID;
		}
		// Find permissions on all groups of this user
		$found = false;
		foreach ( $g as $gid )
		{
			if ( $row = $db->fetchObjectRow ( '
				SELECT * FROM `Setting` WHERE `SettingType`="GroupAccess_' . $gid . '" AND `Key`="extension_' . $permission . '_' . $extension . '"
			' ) )
			{
				$found = true;
				if ( $row->Value == 1 )
				{
					return true;
				}
			}
		}
		// If we find no permission settings on this, just give access (meaning, nothing is saved here)
		if ( !$found ) return true;
		// Deny access if we have settings on this sort
		return false;
	}
	
	function setEncryptionMethod( $m )
	{
		switch( $m )
		{
			case 'plain':
			case 'md5':
				$this->_encryptionMethod = $m;
				break;
		}
	}
	
	function setPassword ( $pass )
	{
		switch( $this->_encryptionMethod )
		{
			case 'plain':
				$this->Password = $pass;
				break;
			default:
				$this->Password = $this->hash ( $pass );
				break;
		}
		return $this->Password;
	}
}

?>
