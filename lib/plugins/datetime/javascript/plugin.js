

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



function datetimeSetDate ( e, field, mode, value, urlextra, callbackfunc )
{
	if ( !callbackfunc ) callbackfunc = false;
	if ( !urlextra ) urlextra = '';
	else urlextra = '&' + urlextra;
	var loc = document.location + '';
	if ( loc.indexOf ( '?' ) >= 0 ) 
		loc += '&';
	else loc += '?';
	
	var dt = document.getElementById ( field ).value + '';
	dt = dt.split ( ' ' );
	var nt = dt[ 0 ].split ( '-' );
	
	var d = new Date ( );
	
	if ( !nt[ 0 ] || nt[ 0 ] <= 0 )
		nt[ 0 ] = d.getFullYear ( );
	if ( !nt[ 1 ] || nt[ 1 ] <= 0 )
		nt[ 1 ] = d.getMonth ( );
	if ( !nt[ 2 ] || nt[ 2 ] <= 0 )
		nt[ 2 ] = d.getDay ( );
	
	switch ( mode )
	{
		case 'day':
			dt = nt[ 0 ] + '-' + nt[ 1 ] + '-' + value + ' ' + dt[ 1 ];
			break;
		case 'month':
			dt = nt[ 0 ] + '-' + StrPad ( value, 2, '0' ) + '-' + nt[ 2 ] + ' ' + dt[ 1 ];
			break;
		case 'year':
			dt = value + '-' + nt[ 1 ] + '-' + nt[ 2 ] + ' ' + dt[ 1 ];
			break;
		default: break;
	}
	
	
	// Remove any date variable in urlextra
	urlextra = urlextra.split ( /date=.*?&/ ).join ( '' );
	var tm = dt.split ( ' ' )[1];
	dt = dt.split ( ' ' )[0].split ( '-' );
	dt[2] = StrPad ( dt[2], 2, '0' );
	dt[1] = StrPad ( dt[1], 2, '0' );
	dt = dt[0] + '-' + dt[1] + '-' + dt[2] + ' ' + tm;
	
	// Update with the new date
	document.getElementById ( field ).value = dt;
	
	var jax = new bajax ( );
	jax.openUrl ( loc + 'ajax=true&plugin=datetime&field=' + field + '&date=' + dt + urlextra, 'get', true );
	jax.field = field;
	jax.value = value;
	jax.mode = mode;
	jax.callbackfunc = callbackfunc;
	jax.onload = function ( )
	{
		var cc = document.getElementById ( this.field + 'CalendarContent' );
		if ( cc )
		{
			var n = document.createElement ( 'div' );
			n.id = cc.id;
			n.className = cc.className;
			n.innerHTML = this.getResponseText ( );
			cc.parentNode.replaceChild ( n, cc );
		}
		if ( this.callbackfunc ) this.callbackfunc ( this.getResponseText (), e );
	}
	jax.send ( );
}

