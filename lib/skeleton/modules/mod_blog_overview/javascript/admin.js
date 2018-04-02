
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

function mod_blogoversikt_new()
{
	initModalDialogue('blogoversikt_new', 512, 512, ACTION_URL + 'mod=mod_blog_overview&modaction=new');
}


function mod_blog_overview_save()
{
	var url = ACTION_URL + 'mod=mod_blog_overview&modaction=executeadd';
	var j = new bajax ( );
	j.openUrl ( url, 'POST', true );
	// Collect values
	var mixed = '';
	var eles = document.getElementById ( 'mod_blogoverview_list' ).getElementsByTagName ( 'tr' );
	var blogOptions = new Array ();
	for ( var a = 0; a < eles.length; a++ )
	{
		var n = eles[a];
		if ( n.getElementsByTagName ( 'input' ).length <= 0 )
			continue;
		mixed += "activated\t" + ( n.getElementsByTagName ( 'input' )[0].checked ? n.getElementsByTagName ( 'input' )[0].id.split('_')[1] : '0' ) + "\n";
		mixed += "quantity\t" + n.getElementsByTagName ( 'input' )[1].value + "\n";
		mixed += "navigation\t" + n.getElementsByTagName ( 'select' )[0].value + "\n";
		mixed += "heading\t" + n.getElementsByTagName ( 'input' )[2].value + "\n";
		mixed += "<!--separate-->";
	}
	// Send request to server	
	j.addVar ( 'mixed', mixed );
	j.addVar ( 'listmode', ge('mod_blog_listmode').value );
	j.addVar ( 'leadinimagewidth', ge('mod_blog_sizex').value );
	j.addVar ( 'leadinimageheight', ge('mod_blog_sizey').value );
	j.onload = function ( )
	{
		ge ( 'mod_blogoverview_content' ).innerHTML = this.getResponseText ( );
		updateStructure();
		removeModalDialogue('blogoversikt_new');
	}
	j.send ( );
}

