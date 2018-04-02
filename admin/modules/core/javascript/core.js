
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

function getNull ( )
{
	try
	{
		return NULL;
	}
	catch ( e ){};
	try
	{
		return null;
	}
	catch ( e ){};
}

function cfgAddLanguage ( )
{
	initModalDialogue ( 'language', 320, 330, 'admin.php?module=core&action=language' );
}

function cfgEditLanguage ( )
{
	if ( document.getElementById ( 'languageid' ).value )
	{
		initModalDialogue ( 'language', 320, 330, 'admin.php?module=core&action=language&lid=' + document.getElementById ( 'languageid' ).value );
	}
	else alert ( 'Vennligst velg et språk å endre på.' );
}

function cfgSaveLanguage ( )
{
	var jax = new bajax ( );
	jax.openUrl ( 'admin.php?module=core&action=savelanguage', 'post', true );
	jax.addVarsFromForm ( 'languageform' );
	jax.onload = function ( )
	{
		removeModalDialogue ( 'language' );
		cfgRefreshLanguages ( );
	}
	jax.send ( );
}

function cfgRefreshLanguages ( )
{
	var rjax = new bajax ( );
	rjax.openUrl ( 'admin.php?module=core&action=getlanguages', 'get', true );
	rjax.onload = function ( )
	{
		var sel = document.getElementById ( 'languageid' );
		var div = document.createElement ( 'div' );
		div.innerHTML = this.getResponseText ( );
		var n = div.getElementsByTagName ( 'SELECT' );
		sel.parentNode.replaceChild ( n[ 0 ], sel );
	}
	rjax.send ( );
}

function cfgDeleteLanguage ( )
{
	var ele = document.getElementById ( 'languageid' );
	for ( var a = 0; a < ele.options.length; a++ )
	{
		if ( ele.options[ a ].selected )
		{
			if ( confirm ( 'Er du sikker? Dette vil deaktivere alle sidene for språket.' ) )
			{
				var djax = new bajax ( );
				djax.openUrl ( 'admin.php?module=core&action=dellanguage&lid=' + ele.options[ a ].value, 'get', true );
				djax.onload = function ( )
				{
					cfgRefreshLanguages ( );
				}
				djax.send ( );
				return;
			}
			return;
		}
	}
	alert ( 'Vennligst velg et språk å slette.' );
}

function saveSimpleSetting( setting, value)
{

	if( document.getElementById( 'msg' + setting ) )
	{
		document.setjax = new bajax ( document.getElementById( 'msg' + setting ) );
		document.setjax.openUrl ( 'admin.php?module=core&action=setsimplesetting&setting=' + setting, 'POST', true );
		document.setjax.onload = function ( )
		{
			var response = this.getResponseText ( );
			this.parentObject.innerHTML = response;
			document.setjax = 0;
		}
		document.setjax.addVar( 'setvalue', value );
		document.setjax.addVar( 'type', setting );
		document.getElementById( 'msg' + setting ).innerHTML = 'Lagrer setting...';
		document.setjax.send ( );
	}
	else
	{
		alert( 'Feil i Arena 2. Kan ikke finne brukergrensesnittsobject for å vise resultat av aksjonen.' )
	}
}

function saveSettings ( module )
{
	// Savedqueue
	document._settingssave = 0;

	// Save options
	if ( !document.setjax )
		document.setjax = new Array ( );
	var eles = getElementsByClassName ( 'modulesetting', document.body );
	
	document._settingssave += eles.length + 2; // <- settings and modulename + moduleicon
	
	if ( typeof ( tinyMCE ) != 'undefined' )
		tinyMCE.triggerSave();
	
	for ( var a = 0; a < eles.length; a++ )
	{
		// Make sure we have data
		var c = document.setjax.length;
		document.setjax[ c ] = new bajax ( );
		document.setjax[ c ].index = c;
		document.setjax[ c ].openUrl ( 'admin.php?module=core&action=setsimplesetting&type=' + module + '&setting=' + eles[ a ].name , 'POST', false );
		document.setjax[ c ].onload = function ( )
		{
			// Clean up
			var outar = new Array ( );
			for ( var a = 0; a < document.setjax.length; a++ )
			{
				if ( a != this.index )
					outar[ outar.length ] = document.setjax[ a ];
			}
			checkSettingsSave ( );
			document.setjax = outar;
		}
		document.setjax[ c ].addVar( 'setvalue', eles[ a ].value );
		document.setjax[ c ].send ( );
	}
	
	// Save module name
	document.modname = new bajax ( );
	document.modname.openUrl ( 
		'admin.php?module=core&action=setmodulename&modulename=' + module + '&value=' + document.getElementById ( 'SettingModuleName' ).value,
		'get', true
	);
	document.modname.onload = function ( )
	{
		checkSettingsSave ( );
		document.modname = 0;
	}
	document.modname.send ( );
	
	// Save module icon
	document.modicon = new bajax ( );
	document.modicon.openUrl ( 
		'admin.php?module=core&action=setmoduleicon&modulename=' + module + '&value=' + document.getElementById ( 'SettingModuleIcon' ).value,
		'get', true
	);
	document.modicon.onload = function ( )
	{
		checkSettingsSave ( );
		document.modicon = 0;
	}
	document.modicon.send ( );
}
function checkSettingsSave ( )
{
	document._settingssave--;
	if ( document._settingssave <= 0 )
	{
		document.location = 'admin.php?module=core';
	}
}


cfgRefreshLanguages ( );
