

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



if ( !document.pmInitialized!ID! )
{
	function pmCheckItem!ID! ( ele )
	{
		var info = ele.id.split ( '_' );
		switch ( info[ 1 ] )
		{
			case 'Groups':
				var eles = ele.parentNode.getElementsByTagName ( 'div' );
				for ( var a = 0; a < eles.length; a++ )
				{
					if ( eles[ a ].parentNode != ele.parentNode ) continue;
					if ( ele == eles[ a ] )
					{
						var id = ele.id.split ( '_' );
						pmShowUsers!ID! ( id[ 2 ] );
						pmSetCssClass!ID! ( ele, 'pmGroupRowActive', 0 );
					}
					else pmSetCssClass!ID! ( eles[ a ], 'pmGroupRow', 0 );
				}
				break;
			case 'Users':
				if ( pmGetCssClass!ID! ( ele, 0 ) == 'pmUserRow' )
				{
					pmSetCssClass!ID! ( ele, 'pmUserRowActive', 0 );
				}
				else pmSetCssClass!ID! ( ele, 'pmUserRow', 0 );
				break;
			case 'GlobalPermissionPermission':
			case 'GroupsPermission':
				if ( pmGetCssClass!ID! ( ele, 0 ) == 'pmPermissionRow' )
				{
					pmSetCssClass!ID! ( ele, 'pmPermissionRowActive', 0 );
				}
				else pmSetCssClass!ID! ( ele, 'pmPermissionRow', 0 );
				break;
			case 'UsersPermission':
				if ( pmGetCssClass!ID! ( ele, 0 ) == 'pmPermissionRow' )
				{
					pmSetCssClass!ID! ( ele, 'pmPermissionRowActive', 0 );
				}
				else pmSetCssClass!ID! ( ele, 'pmPermissionRow', 0 );
				break;
			default:
				break;
		}
	}
	
	function pmGetCssClass!ID! ( ele, ind )
	{
		var classes = ele.className.split ( ' ' );
		for ( var a = 0; a < classes.length; a++ )
		{
			if ( a == ind )
			{
				return classes[ a ];
			}
		}
	}
	
	function pmSetCssClass!ID! ( ele, cn, ind )
	{
		var classes = ele.className.split ( ' ' );
		for ( var a = 0; a < classes.length; a++ )
		{
			if ( a == ind )
			{
				classes[ a ] = cn;
			}
		}
		ele.className = classes.join ( ' ' );
	}
	
	function pmAddGroups!ID! ( )
	{
		var groups = getElementsByClassName ( 'pmGroupRowActive' );
		var ids = new Array ( );
		for ( var a = 0; a < groups.length; a++ )
		{
			if ( groups[ a ].parentNode.id != 'pmGroups!ID!' ) continue;
			var id = groups[ a ].id.split ( '_' );
			ids.push ( id[ 2 ] );
		}
		var gjax = new bajax ( );
		var ctype = document.getElementById ( 'pmContentType!ID!' ).value;
		var cid = document.getElementById ( 'pmContentID!ID!' ).value;
		var ptype = document.getElementById ( 'pmPermissionType!ID!' ).value;
		gjax.openUrl ( 
			'admin.php?plugin=permissions&pluginaction=addgroups&objid=' + 
			cid + '&objtype=' + ctype + '&gids=' + ids.join ( ',' ) + 
			'&permissiontype=' + ptype, 
			'get', true 
		);
		gjax.onload = function ( )
		{
			pmShowGroupPermissions!ID! ( );
		}
		gjax.send ( );
	}
	
	function pmAddUsers!ID! ( )
	{
		var users = getElementsByClassName ( 'pmUserRowActive' );
		var ids = new Array ( );
		for ( var a = 0; a < users.length; a++ )
		{
			if ( users[ a ].parentNode.id != 'pmUsers!ID!' ) continue;
			var id = users[ a ].id.split ( '_' );
			ids.push ( id[ 2 ] );
		}
		var ujax = new bajax ( );
		var ctype = document.getElementById ( 'pmContentType!ID!' ).value;
		var cid = document.getElementById ( 'pmContentID!ID!' ).value;
		var ptype = document.getElementById ( 'pmPermissionType!ID!' ).value;
		ujax.openUrl ( 
			'admin.php?plugin=permissions&pluginaction=addusers&objid=' + 
			cid + '&objtype=' + ctype + '&uids=' + ids.join ( ',' ) + 
			'&permissiontype=' + ptype, 
			'get', true 
		);
		ujax.onload = function ( )
		{
			pmShowUserPermissions!ID! ( );
		}
		ujax.send ( );
	}
	
	function pmDelGroups!ID! ( )
	{
		var groups = getElementsByClassName ( 'pmPermissionRowActive' );
		var ids = new Array ( );
		for ( var a = 0; a < groups.length; a++ )
		{
			if ( groups[ a ].parentNode.id != 'pmGroupRights!ID!' ) continue;
			var id = groups[ a ].id.split ( '_' );
			if ( id[ 1 ] == 'GroupsPermission' || id[ 1 ] == 'GlobalPermissionPermission' )
				ids.push ( id[ 2 ] );
		}
		if ( ids.length )
		{
			var ptype = document.getElementById ( 'pmPermissionType!ID!' ).value;
			var gjax = new bajax ( );
			gjax.openUrl ( 
				'admin.php?plugin=permissions&pluginaction=delgroups&gids=' + ids.join ( ',' ) + 
				'&permissiontype=' + ptype, 
				'get', true 
			);
			gjax.onload = function ( )
			{
				pmShowGroupPermissions!ID! ( );
			}
			gjax.send ( );
		}
	}
	
	function pmDelUsers!ID! ( )
	{
		var users = getElementsByClassName ( 'pmPermissionRowActive' );
		var ids = new Array ( );
		for ( var a = 0; a < users.length; a++ )
		{
			if ( users[ a ].parentNode.id != 'pmUserRights!ID!' ) continue;
			var id = users[ a ].id.split ( '_' );
			if ( id[ 1 ] == 'UsersPermission' )
				ids.push ( id[ 2 ] );
		}
		if ( ids.length )
		{
			var ujax = new bajax ( );
			var ptype = document.getElementById ( 'pmPermissionType!ID!' ).value;
			ujax.openUrl ( 
				'admin.php?plugin=permissions&pluginaction=delusers&uids=' + ids.join ( ',' ) + 
				'&permissiontype=' + ptype, 
				'get', true 
			);
			ujax.onload = function ( )
			{
				pmShowUserPermissions!ID! ( );
			}
			ujax.send ( );
		}
	}
	
	function pmShowUserPermissions!ID! ( )
	{
		var ujax = new bajax ( );
		var ctype = document.getElementById ( 'pmContentType!ID!' ).value;
		var cid = document.getElementById ( 'pmContentID!ID!' ).value;
		var ptype = document.getElementById ( 'pmPermissionType!ID!' ).value;
		
		ujax.openUrl ( 'admin.php?plugin=permissions&pluginaction=showuserpermissions&objid=' + 
			cid + '&objtype=' + ctype + '&permissiontype=' + ptype + '&pluginid=!ID!', 'get', true );
		ujax.onload = function ( )
		{
			document.getElementById ( 'pmUserRights!ID!' ).innerHTML = this.getResponseText ( );
		}
		ujax.send ( );
	}
	
	function pmShowGroupPermissions!ID! ( )
	{
		var gjax = new bajax ( );
		var ctype = document.getElementById ( 'pmContentType!ID!' ).value;
		var ptype = document.getElementById ( 'pmPermissionType!ID!' ).value;
		var cid = document.getElementById ( 'pmContentID!ID!' ).value;
		
		gjax.openUrl ( 'admin.php?plugin=permissions&pluginaction=showgrouppermissions&objid=' + 
			cid + '&objtype=' + ctype + '&permissiontype=' + ptype + '&pluginid=!ID!', 'get', true );
		gjax.onload = function ( )
		{
			document.getElementById ( 'pmGroupRights!ID!' ).innerHTML = this.getResponseText ( );
		}
		gjax.send ( );
	}
	
	function pmShowUsers!ID! ( gid )
	{
		if ( !gid ) gid = '*';
		var gjax = new bajax ( );
		gjax.openUrl ( 'admin.php?plugin=permissions&pluginaction=showusers&gid=' + gid + '&pluginid=!ID!', 'get', true );
		gjax.onload = function ( )
		{
			document.getElementById ( 'pmUsers!ID!' ).innerHTML = this.getResponseText ( );
		}
		gjax.send ( );
	}
	
	function pmShowUsersPage!ID! ( page )
	{
		var gjax = new bajax ( );
		gjax.openUrl ( 'admin.php?plugin=permissions&pluginaction=showuserspage&page=' + page + '&pluginid=!ID!', 'get', true );
		gjax.onload = function ( )
		{
			document.getElementById ( 'pmUsers!ID!' ).innerHTML = this.getResponseText ( );
		}
		gjax.send ( );
	}
	
	function pmSetPermission!ID! ( perm, val, pid )
	{
		var pjax = new bajax ( );
		pjax.openUrl ( 'admin.php?plugin=permissions&pluginaction=setpermission&pid= ' + pid + '&perm=' + perm + '&val=' + val, 'get', true );
		pjax.onload = function ( )
		{
			if ( this.getResponseText ( ) == 'Groups' )
				pmShowGroupPermissions!ID! ( );
			else pmShowUserPermissions!ID! ( );
		}
		pjax.send ( );
	}	
	
	function pmSetProtectedFlag!ID! ( oid, otype, flag, obj )
	{
		var fjax = new bajax ( );
		fjax.openUrl ( 'admin.php?plugin=permissions&pluginaction=setprotectedflag&oid=' + oid + '&otype=' + otype + '&flag=' + flag, 'get', true );
		fjax.obj = obj;
		fjax.onload = function ( )
		{
			obj.checked = this.getResponseText ( );
		}
		fjax.send ( );
	}

	document.pmInitialized!ID! = true;
}

