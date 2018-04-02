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


function checkContactField ( field )
{
	var fields = new Array ();
	var inps = 
	[ 
		field.getElementsByTagName ( 'input' ),
		field.getElementsByTagName ( 'select' ),
		field.getElementsByTagName ( 'textarea' )
	];
	for ( var a = 0; a < inps.length; a++ )
	{
		for ( var b = 0; b < inps[a].length; b++ )
		{
			fields.push ( inps[a][b] );
		}
	}
	for ( var a = 0; a < fields.length; a++ )
	{
		var f = fields[a];
		var p = fields[a].parentNode.parentNode.getElementsByTagName ( 'td' )[0];
		if ( p.innerHTML.indexOf ( '*' ) > 0 )
		{
			if ( f.value.length <= 0 )
			{
				alert ( i18n ( 'i18n_forgot_to_fill_in_field' ) + ': ' + p.innerText.split ( '*' ).join ( '' ).split ( ':' ).join ( '' ).toLowerCase () );
				f.focus ();
				return false;
			}
		}
	}
	field.submit ();
}

