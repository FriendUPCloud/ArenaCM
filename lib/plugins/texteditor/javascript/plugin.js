
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


// Setup content changer
var tco = ge ( 'TxContentOptions' );
tco.onchange = function () 
{
	var j = new bajax ();
	j.openUrl ( 'admin.php?plugin=texteditor&pluginaction=getcontentfields', 'post', true );
	j.addVar ( 'cid', this.value );
	j.onload = function () 
	{
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			ge ( 'TxContentFields' ).innerHTML = r[1];
		}
	}
	j.send ();
}
tco.onchange ();

// Insert this content
window.txInsertContentField = function ()
{
	var cid = ge ( 'TxContentOptions' ).value;
	var fnm = ge ( 'TxContentFields' ).getElementsByTagName ( 'select' )[0].value;
	removeModalDialogue ( 'fieldobject' );
	var ed = texteditor.get ( texteditor.activeEditorId );
	ed.insertHTML ( '<span arenatype="fieldobject" style="display: block; width: 400px; height: 100px; background: #c0c0c0 url(admin/gfx/icons/layout.png) no-repeat center center; border: 1px dotted #808080" id="FieldObject__' + cid + '__' + fnm + '">&nbsp;</span>' );
}
