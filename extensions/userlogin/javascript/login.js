
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

function verify_register_form ( )
{
	var theform = document.getElementById ( 'register_form' );

	if ( theform.Name.value.length <= 0 )
	{
		alert ( i18n ( 'You need to fill in your name' ) + '.' );
		theform.Name.focus ( );
		return false;
	}
	if ( theform.Username )
	{
		if ( theform.Username.value.length <= 0 )
		{
			alert ( i18n ( 'You need to fill in a username' ) + '.' );
			theform.Username.focus ( );
			return false;
		}
	}
	if ( theform.Password.value.length <= 0 )
	{
		alert ( i18n ( 'You need to fill in a password' ) + '.' );
		theform.Password.focus ( );
		return false;
	}
	if ( theform.Password.value != theform.Passwordverify.value )
	{
		alert ( i18n ( 'The password could not be confirmed' ) + '.' );
		theform.Passwordverify.focus ( );
		return false;
	}
	if ( theform.Email.value.length <= 0 )
	{
		alert ( i18n ( 'You need to fill in an e-mail address' ) + '.' );
		theform.Email.focus ( );
		return false;
	}
	
	if ( theform.Address )
	{
		if ( theform.Address.value.length <= 0 )
		{
			alert ( i18n ( 'You need to fill in your address' ) + '.' )
			theform.Address.focus ( );
			return false;
		}
		if ( theform.Postcode.value.length <= 0 )
		{
			alert ( i18n ( 'You need to fill in your zip code' ) + '.' )
			theform.Postcode.focus ( );
			return false;
		}
		if ( theform.City.value.length <= 0 )
		{
			alert ( i18n ( 'You need to fill in your city' ) + '.' )
			theform.City.focus ( );
			return false;
		}
		if ( theform.Country )
		{
			if ( theform.Country.value.length <= 0 )
			{
				alert ( i18n ( 'Please choose your country' ) + '.' )
				theform.Country.focus ( );
				return false;
			}
		}
	}
	
	theform.Controlnumber.value = 'verified_and_done';
	theform.submit ( );
}

/**
* Set opacity on an element (compatible with most, cross browser (except konqueror))
**/
function loginSetOpacity ( ele, op )
{
	if ( !ele ) return;
	if ( ele.opacity && ele.opacity == op ) return;
	
	if ( op == false ) op = 0
	
	ele.opacity = op;
	
	if ( isIE )
	{
		ele.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(Opacity=' + ( op * 100 ) + ')';
	}
	else
	{
		ele.style.opacity = op;
	}
}

function forgotPassword ( )
{
	var loc = document.location + '';
	var langs = Array ( 'en', 'no', 'se', 'dk', 'fi', 'is', 'de', 'sp', 'it' );
	var ex = '';
	for ( var a = 0; a < langs.length; a++ )
	{
		if ( loc.indexOf ( langs[ a ] + '/' ) > 0 )
		{
			ex = langs[ a ] + '/';
			break;	
		}
	}
	styledDialog ( ex + '?ue=userlogin&function=forgotpassword', 'forgotPasswordPopup' );
}

function seeOrder ( oid )
{
	var base = document.getElementsByTagName ( 'base' );
	document.ojax = new bajax ( );
	document.ojax.openUrl ( base[ 0 ].href + '?ue=userlogin&function=showorder&oid=' + oid, 'get', true );
	document.ojax.onload = function ( )
	{
		document.getElementById ( 'OrderDetails' + oid ).innerHTML = this.getResponseText ( );
		var nodes = document.body.getElementsByTagName ( 'td' );
		for ( var a = 0; a < nodes.length; a++ )
		{
			var n = nodes[ a ];
			if ( n.id && n.id.indexOf ( 'rderDetails' ) > 0 && n.id != ( 'OrderDetails' + oid ) )
			{
				n.innerHTML = '';
			}
		}
		document.ojax = 0;
	}
	document.ojax.send ( );
}

function verifyInformationForm ( )
{
	var tform = document.infoform;
	
	if ( tform.Name.value.length < 1 )
	{
		alert ( i18n ( 'You have to enter your name.' ) );
		tform.Name.focus ( );
		return false;
	}
	if ( tform.Password.value.length < 1 )
	{
		alert ( i18n ( 'You need to write in a password.' ) );
		tform.Password.focus ( );
		return false;
	}
	if ( tform.Password_Confirm.value != tform.Password.value )
	{
		alert ( i81n ( 'Your password does not match the one in the confirm field.' ) );
		tform.Password_Confirm.focus ( );
		return false;
	}
	tform.submit ( );
}

function receivePassword ( )
{
	var base = document.getElementsByTagName ( 'base' );
	document.passjax = new bajax ( );
	document.passjax.openUrl ( base[ 0 ].href + '?ue=userlogin&function=createpassword&email=' + document.getElementById ( 'findEmailAddy' ).value, 'get', true );
	document.passjax.onload = function ( )
	{
		if ( this.getResponseText ( ) == 'OK' )
		{
			alert ( i18n ( 'A new password has been sent to your e-mail address.' ) );
		}
		else
		{
			alert ( i18n ( 'No user with such an e-mail address exists.' ) + ' (' + document.getElementById ( 'findEmailAddy' ).value + ')' );
		}
		closeForgetPopup ( );
		document.passjax = 0;
	}
	document.passjax.send ( );
}

function closeForgetPopup ( )
{
	document.getElementById ( 'Empty__' ).removeChild ( document.getElementById ( 'forgotPasswordPopup' ) );
}


