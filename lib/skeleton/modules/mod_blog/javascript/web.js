
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

function mod_blog_reloadCaptcha ( )
{
	var j = new bajax ( );
	var l = document.location + '';
	j.openUrl ( l.split ( '#' )[0] + '?captcha=true', 'get', true );
	j.onload = function ( )
	{
		var img = document.getElementById ( 'mod_blog_captcha_image' ).getElementsByTagName ( 'img' )[0];
		img.src = this.getResponseText ( );
	} 
	j.send ( );
}

function mod_blog_submitComment ( )
{
	var frm = document.getElementById ( 'mod_blog_commentform' );
	if ( frm.Name.value.length < 2 )
	{
		alert ( i18n ( 'You have to enter your name.' ) );
		frm.Name.focus ( );
		return false;
	}
	if ( frm.Message.value.length < 2 )
	{
		alert ( i18n ( 'You have to write your comment.' ) );
		frm.Message.focus ( );
		return false;
	}
	var cjax = new bajax ( );
	var l = document.location + '';
	cjax.openUrl ( 
		l.split ( '#' )[0] + '?checkcaptcha=true&c=' + frm.Captcha.value, 
		'get', true 
	);
	cjax.onload = function ( )
	{
		if ( this.getResponseText ( ) == 'ok' )
		{
			frm.submit ( );
		}
		else
		{ 
			alert ( i18n ( 'Wrong spam control text, please try again.' ) );
			mod_blog_reloadCaptcha ( );
			frm.Captcha.focus ( );
			return false;
		}
	}
	cjax.send ( );
}
