
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

function mod_blog_tip_send ( )
{
	var jax = new bajax ( );
	jax.openUrl ( document.location + '', 'post', true );
	jax.addVar ( 'modaction', 'sendtip' );
	jax.addVar ( 'heading', document.getElementById ( 'blog_title' ).value );
	jax.addVar ( 'author_name', document.getElementById ( 'blog_author_name' ).value );
	jax.addVar ( 'leadin', document.getElementById ( 'blog_leadin' ).value );
	jax.addVar ( 'article', document.getElementById ( 'blog_article' ).value );
	jax.onload = function ( )
	{	
		alert ( this.getResponseText ( ) );
	}
	jax.send ( );
}

