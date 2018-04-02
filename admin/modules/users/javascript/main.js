

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



var overEditButtons = 0;

function newUser ( )
{
	document.location = 'admin.php?module=users&function=user';
}

function editUser ( )
{
	if ( document.editUserID )
		document.location = 'admin.php?module=users&function=user&uid=' + document.editUserID;
	else alert ( "Du må velge en bruker å endre." );
}

function deleteUsers ( )
{
	var users = getUniqueListEntries ( 'seluserslist' );
	if ( users.length )
	{
		if ( confirm ( 'Er du sikker?' ) )
		{
			document.location = 'admin.php?module=users&action=deleteusers&users=' + users;
		}
	}
	else 
	{
		alert ( 'Ingen brukere er valgt.' );
	}
}

function editGroup ( gid )
{
	document.location = 'admin.php?module=users&function=editgroup&gid=' + gid;
}

function subGroup ( gid )
{
	document.location = 'admin.php?module=users&function=editgroup&pgid=' + gid;
}

function goGroupUrl ( url )
{
	if ( !overEditButtons )
		document.location = url;
}

function deleteGroup ( gid )
{
	if ( !gid ) return;
	
	if ( confirm ( "Er du sikker på at du ønsker å slette denne gruppen?" ) )
		document.location = 'admin.php?module=users&action=deletegroup&gid=' + gid;
	else return false;
}

function importUser ( )
{
	document.location = 'admin.php?module=users&function=importusers';
}

function editCollection ( collectionid )
{
	if ( !collectionid ) collectionied = '';
	initModalDialogue ( 'collection', 480, 260, 'admin.php?module=users&action=editcollection&collectionid=' + collectionid );
}

function subCollection ( collectionid )
{
	if ( !collectionid ) collectionied = '';
	initModalDialogue ( 'collection', 480, 260, 'admin.php?module=users&action=editcollection&parentid=' + collectionid );
}

function deleteCollection ( collectionid )
{
	if ( confirm ( 'Er du sikker på at du ønsker å fjerne enheten?' ) )
	{
		document.location = 'admin.php?module=users&action=deletecollection&collectionid=' + collectionid;
	}
}

function addToCollection ( )
{
	var users = getUniqueListEntries ( 'seluserslist' );
	if ( users.length )
	{
		initModalDialogue ( 'addtocollection', 480, 313, 'admin.php?module=users&action=addtocollection' );
	}
	else alert ( 'Du har ikke valgt noen brukere.' );
}

function removeFromCollection ( )
{
	if ( confirm ( 'Er du sikker?' ) )
	{
		var users = getUniqueListEntries ( 'seluserslist' );
		if ( users.length )
		{
			document.location = 'admin.php?module=users&action=removefromcollection&ids=' + users.join ( ',' );
		}
	}
}

function addToGroup ( )
{
	initModalDialogue ( 'addtogroup', 400, 313, 'admin.php?module=users&action=addtogroup' );
}

function setUserSortOrder ( id )
{
	var u = ge(id);
	if ( u )
	{
		var j = new bajax ();
		j.openUrl ( 'admin.php?module=users&action=setsortorder', 'post', true );
		j.addVar ( 'sortorder', u.value );
		j.addVar ( 'id', id.split ( '_' )[1] );
		j.onload = function ()
		{
			if ( this.getResponseText () == 'ok' )
			{
				// TODO: Graceful ajax reloading of user table
				document.location.reload ();
			}
		}
		j.send ();
	}
}

function selectAllUsers ()
{
	var inps = ge('UserList').getElementsByTagName ( 'input' );
	for ( var a = 0; a < inps.length; a++ )
	{
		if ( inps[a].type != 'checkbox' ) continue;
		inps[a].click();
	}
}

