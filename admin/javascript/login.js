

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



var login = { animating: false, phase: 0 };

function sendEmail ( )
{
	var sjax = new bajax ( );
	sjax.openUrl ( 'admin.php?action=newpassword', 'post', true );
	sjax.addVar ( 'email', document.getElementById ( 'forgot_password_email' ).value );
	sjax.onload = function ()
	{
		alert ( this.getResponseText ( ) );
		forgotPassword ( );
	}
	sjax.send ( );
}

function showEmailForm ( )
{
	if ( !login.animating )
	{
		document.getElementById ( 'receive_text' ).parentNode.onclick = null;
		login.animating = true;
		login.phase = 1;
		login.mode = 0;
		login.elementHeight = getElementHeight ( document.getElementById ( 'LoginForm' ) );
	}
	if ( login.mode == 0 )
	{
		if ( login.phase > 0 )
		{
			login.phase -= 0.1;
			var factor = Math.pow ( Math.sin ( login.phase * 0.5 * Math.PI ), 6 );
			document.getElementById ( 'LoginForm' ).style.height = Math.round ( login.elementHeight * factor ) + 'px';
			setTimeout ( 'showEmailForm ( )', 40 );
		}
		else 
		{
			login.mode = 1;
			document.getElementById ( 'ForgotEmail' ).style.position = 'relative';
			document.getElementById ( 'ForgotEmail' ).style.height = '0px';
			document.getElementById ( 'ForgotEmail' ).style.overflow = 'hidden';
			login.emailelement = login.elementHeight;
			setTimeout ( 'showEmailForm ( )', 40 );
		}
	}
	else if ( login.mode == 1 )
	{
		if ( login.phase < 1 )
		{
			login.phase += 0.1;
			var factor = Math.pow ( Math.sin ( login.phase * 0.5 * Math.PI ), 6 );
			document.getElementById ( 'ForgotEmail' ).style.height = Math.round ( login.elementHeight * factor ) + 'px';
			setTimeout ( 'showEmailForm ( )', 40 );
		}
		else
		{
			login.mode = 0;
			login.animating = false;
			login.phase = 0;
			document.getElementById ( 'receive_text' ).innerHTML = 'Klikk for å sende passord';
			document.getElementById ( 'login_text' ).innerHTML = 'Tilbake til innlogging';
			document.getElementById ( 'forgot_password_email' ).focus ( );
			document.getElementById ( 'receive_text' ).parentNode.onclick = sendEmail;
			document.getElementById ( 'login_text' ).parentNode.onclick = forgotPassword;
		}
	}
}
function loginFormInit ( )
{
	if ( document.getElementById ( 'receive_text' ) )
	{
		document.getElementById ( 'receive_text' ).parentNode.onclick = showEmailForm;
	}
	else setTimeout ( 'loginFormInit ( )', 50 );
}
loginFormInit ( );

function forgotPassword ( )
{
	if ( !login.animating )
	{
		document.getElementById ( 'login_text' ).parentNode.onclick = null;
		login.animating = true;
		login.phase = 1;
		login.mode = 0;
	}
	if ( login.mode == 0 )
	{
		if ( login.phase > 0 )
		{
			login.phase -= 0.1;
			var factor = Math.pow ( Math.sin ( login.phase * 0.5 * Math.PI ), 6 );
			document.getElementById ( 'ForgotEmail' ).style.height = Math.round ( login.emailelement * factor ) + 'px';
			setTimeout ( 'forgotPassword ( )', 40 );
		}
		else 
		{
			login.mode = 1;
			document.getElementById ( 'LoginForm' ).style.position = 'relative';
			document.getElementById ( 'LoginForm' ).style.height = '0px';
			document.getElementById ( 'LoginForm' ).style.overflow = 'hidden';
			setTimeout ( 'forgotPassword ( )', 40 );
		}
	}
	else if ( login.mode == 1 )
	{
		if ( login.phase < 1 )
		{
			login.phase += 0.1;
			var factor = Math.pow ( Math.sin ( login.phase * 0.5 * Math.PI ), 6 );
			document.getElementById ( 'LoginForm' ).style.height = Math.round ( login.elementHeight * factor ) + 'px';
			setTimeout ( 'forgotPassword ( )', 40 );
		}
		else
		{
			login.mode = 0;
			login.animating = false;
			login.phase = 0;
			document.getElementById ( 'receive_text' ).innerHTML = 'Motta nytt passord';
			document.getElementById ( 'login_text' ).innerHTML = 'Logg deg inn';
			document.getElementById ( 'inputUsername' ).focus ( );
			document.getElementById ( 'receive_text' ).parentNode.onclick = showEmailForm;
			document.getElementById ( 'login_text' ).parentNode.onclick = function ( ){ this.form.submit() };
		}
	}
}
