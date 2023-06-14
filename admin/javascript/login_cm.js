

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

// Login script for ARENA Enterprise

document.loginform.loginUsername.focus ();

function checkLogin ()
{
	if ( 
		document.loginform.loginUsername.value.length > 2 && 
		document.loginform.loginPassword.value.length > 1 )
	{
		document.loginform.submit ();
	}
	else if ( document.loginform.loginUsername.value.length <= 2 )
	{
		alert ( 'Du må fylle inn brukernavn.' );
		document.loginform.loginUsername.focus ();
	}
	else if ( document.loginform.loginPassword.value.length <= 2 )
	{
		alert ( 'Du må fylle inn passord.' );
		document.loginform.loginPassword.focus ();
	}
}

function checkKeydown ( e )
{
	if ( !e ) e = window.event;
	var w = e.which || e.keyCode;
	if ( w == 13 )
	{
		checkLogin ();
	}
}

function initLogin()
{
	if ( !document.loginform )
		return setTimeout ( 'initLogin()', 10 );
	var f = document.loginform;
	var b = document.getElementById ( 'loginButton' );
	f.loginUsername.onkeydown = function ( e )
	{
		checkKeydown ( e );
	}
	f.loginPassword.onkeydown = function ( e )
	{
		checkKeydown ( e );
	}
	b.onclick = function ()
	{
		checkLogin();
		return false;
	}
	b.onsubmit = function ()
	{
		checkLogin ();
		return false;
	}
	
	document.body.classList.add( 'Loaded' );
}
