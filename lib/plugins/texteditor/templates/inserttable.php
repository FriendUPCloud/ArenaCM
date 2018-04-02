<?php
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

?>
	<h1>
		Sett inn en tabell
	</h1>
	<div class="Container" style="position: relative">
		<div id="TableContainer" class="SubContainer" style="height: 200px; overflow: scroll">
			<div id="TableContent" style="position: relative; width: 600px; height: 500px;"></Div>
		</div>
		<div id="TablePreview" style="position: absolute; border: 1px solid #c0c0c0; background: #b0b0b0; top: 20px; left: 20px; width: 245px; height: 180px; border-radius: 3px">
		</div>
	</div>
	<table>
		<tr>
			<td>Kolonner:</td><td><input type="text" id="tblCols" size="3" value="1"/></td>
			<td>Rader:</td><td><input type="text" id="tblRows" size="3" value="1"/></td>
		</tr>
	</table>
	<div class="SpacerSmallColored"></div>
	<button type="button" onclick="texteditor.get ( texteditor.activeEditorId ).insertTable (); removeModalDialogue('inserttable');"><img src="admin/gfx/icons/table.png"> Sett inn tabellen</button>
	<button type="button" onclick="removeModalDialogue('inserttable')"><img src="admin/gfx/icons/cancel.png"> Lukk</button>
	
	<script>
		var div = ge('TableContainer');
		function tableScroller ()
		{
			ge('tblCols').value = Math.floor ( div.scrollLeft / 345 * 20 );
			ge('tblRows').value = Math.floor ( div.scrollTop / 315 * 20 );
			if ( ge('tblCols').value < 1 ) ge('tblCols').value = 1;
			if ( ge('tblRows').value < 1 ) ge('tblRows').value = 1;
			refreshTableScroller ();
		}
		function refreshTableScroller ()
		{
			var cols = parseInt(ge('tblCols').value);
			var rows = parseInt(ge('tblRows').value);
			var str = '<table style="width: 100%; height: 100%" cellspacing="1" cellpadding="0" style="background: #404040">';
			for ( var y = 0; y < rows; y++ )
			{
				str += '<tr>';
				for ( var x = 0; x < cols; x++ )
				{
					str += '<td style="background:white"><em></em></td>';
				}
				str += '</tr>';
			}
			str += '</table>';
			ge('TablePreview').innerHTML = str;
		}
		div.onscroll = tableScroller;
		tableScroller ();
		ge('tblCols').onchange = function () { refreshTableScroller (); }
		ge('tblRows').onchange = function () { refreshTableScroller (); }
	</script>
	

