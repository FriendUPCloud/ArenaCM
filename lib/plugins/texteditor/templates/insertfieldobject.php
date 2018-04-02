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
		<?= i18n ( 'Select content field for insertion' ) ?>
	</h1>
	<table>
		<tr>
			<td>
				<strong><?= i18n ( 'Content' ) ?>:</strong>
			</td>
			<td>
				<select type="text/javascript" id="TxContentOptions">
					<?= $this->contentOptions ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?= i18n ( 'Content field' ) ?>:</strong>
			</td>
			<td id="TxContentFields">
				
			</td>
		</tr>
	</table>
	<div class="SpacerSmallColored"></div>
	<script>
		<?= file_get_contents ( 'lib/plugins/texteditor/javascript/plugin.js' ) ?>
	</script>
	<button type="button" onclick="window.txInsertContentField ()">
		<img src="admin/gfx/icons/table_row_insert.png"/> <?= i18n ( 'Insert field' ) ?>
	</button>
	<button type="button" onclick="removeModalDialogue ( 'fieldobject' )">
		<img src="admin/gfx/icons/cancel.png"/> <?= i18n ( 'Abort' ) ?>
	</button>

