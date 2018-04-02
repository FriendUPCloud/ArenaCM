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
New code is (C) 2011 Idéverket AS, 2015 Friend Studios AS

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
                Rune Nilssen
*******************************************************************************/


function mod_tipafriend_send ( )
{
	var email = document.getElementById ( 'mod_tipafriend_email' );
	var message = document.getElementById ( 'mod_tipafriend_message' );
	var name = document.getElementById ( 'mod_tipafriend_name' );
	if ( name.value.length < 3 )
	{
		alert ( 'Du må skrive inn navnet ditt.' );
		name.focus ( );
		return false;
	}
	if ( 
		email.value.length < 2 || email.value.indexOf ( '@' ) < 0 || 
		email.value.indexOf ( '.' ) < 0
	)
	{
		alert ( 'Du skrive inn mottakers e-post adresse.' );
		email.focus ( );
		return false;
	}
	if ( message.value.length < 3 )
	{
		alert ( 'Du må skrive inn en beskjed.' );
		message.focus ( );
		return false;
	}
	var j = new bajax ( );
	j.openUrl ( document.location + '', 'post', true );
	j.addVar ( 'mod_tipafriend', '1' );
	j.addVar ( 'message', message.value );
	j.addVar ( 'email', email.value );
	j.addVar ( 'name', name.value );
	j.onload = function ( )
	{
		if ( this.getResponseText ( ) == 'ok' )
		{
			alert ( 'Tipset er sendt!' );
			closeStyledDialog ( );
		}
		else alert ( 'Det skjedde en feil ved forsendelsen. Beklager.' + this.getResponseText ( ) );
	}
	j.send ( );
}
