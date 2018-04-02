

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



function verifySettingsForm ()
{
	var f = document.sform;
	if ( f.Name.value.length <= 4 )
	{
		alert ( 'Navnet ditt må være mer enn 4 bokstaver.' );
		f.Name.focus();
		return false;
	}
	if ( f.Password.value.length <= 7 )
	{
		alert ( 'Passordet ditt må være 8 bokstaver eller mer.' );
		f.Password.focus();
		return false;
	}
	f.submit ();
}

function saveArenaSettings ()
{
	
	var jax = new bajax ();
	jax.openUrl ( 'admin.php?module=settings&action=savearenasettings', 'post', true );
	var eles = document.getElementsByTagName ( '*' );
	for ( var a = 0; a < eles.length; a++ )
	{
		if ( eles[a].id.indexOf ( 'arenasetting_' ) == 0 )
		{
			var nm = eles[a].id.replace ( 'arenasetting_', '' );
			switch ( eles[a].type )
			{
				case 'checkbox':
					jax.addVar ( nm, eles[a].checked ? '1' : '0' );
					break;
				default:
					jax.addVar ( nm, eles[a].value );
					break;
			}
		}
	}
	jax.onload = function ()
	{
		if ( this.getResponseText () == 'reload' )
		{
			document.location = 'admin.php?module=settings&rand=' + Math.floor(Math.random()*1000000);
		}
		else
			alert ( 'Innstillingene er lagret.' );
	}
	jax.send ();	
}

function editVariant ( id )
{
	initModalDialogue ( 'variant', 640, 500, 'admin.php?module=settings&action=variant&vid=' + id );
}

function deleteVariant ( id )
{
	if ( confirm ( 'Er du sikker?' ) )
	{
		document.location = 'admin.php?module=settings&action=deletevariant&vid=' + id;
	}
}

function newVariant ()
{
	initModalDialogue ( 'variant', 640, 500, 'admin.php?module=settings&action=variant' );
}

function saveVariant ()
{
	var fields = [ 
		'ID', 'NativeName', 'Name', 'IsDefault', 'AutomaticResources', 'Resources', 
		'UrlActivator', 'BaseUrl'
	];
	var j = new bajax ();
	j.openUrl ( 'admin.php?module=settings&action=savevariant', 'post', true );
	for ( var a = 0; a < fields.length; a++ )
	{
		j.addVar ( fields[a], ge('v'+fields[a]).value );
	}
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			removeModalDialogue ( 'variant' );
			refreshVariants ();
		}
	}
	j.send ();
}

function refreshVariants ()
{
	var j = new bajax ();
	j.openUrl ( 'admin.php?module=settings&action=refreshvariants', 'get', true );
	j.onload = function ()
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			ge ( 'Variants' ).innerHTML = r[1];
		}
	}
	j.send ();
}

