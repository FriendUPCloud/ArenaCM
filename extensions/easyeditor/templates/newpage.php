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
		<?= i18n ( 'New subpage' ) ?> 
	</h1>
	<div class="SubContainer">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td><strong><?= i18n ( 'Page title' ) ?>:</strong></td>
				<td><input type="text" value="" id="npTitle" size="40"/></td>
			</tr>
		</table>
	</div>
	<div class="SpacerSmallColored"></div>
	<p>
		<button type="button" onclick="_addPage()">
			<img src="admin/gfx/icons/disk.png"/> <?= i18n ( 'Save page' ) ?>
		</button>
		<button type="button" onclick="removeModalDialogue('newpage')">
			<img src="admin/gfx/icons/cancel.png"/> <?= i18n ( 'Cancel' ) ?>
		</button>
	</p>
