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
	<h3>
		<?= i18n ( 'Search and replace' ) ?>
	</h3>
	<div class="Container">
		<?if ( !$this->sreplace ) { ?>
		<p>
			<?= i18n ( 'Search_replace_desc' ) ?>
		</p>
		<form method="post" action="admin.php?module=core&action=searchreplace" name="srform">
			<table>
				<tr>
					<td>
						<strong><?= i18n ( 'Search for' ) ?>:</strong>
					</td>
					<td>
						<input type="text" name="searchfor" size="25" value=""/>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?= i18n ( 'Replace with' ) ?>:</strong>
					</td>
					<td>
						<input type="text" name="replacewith" size="25" value=""/>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<button type="submit">
							<img src="admin/gfx/icons/magnifier.png"/> <?= i18n ( 'Start' ) ?>
						</button>
					</td>
				</tr>
			</table>
		</form>
		<?}?>
		<?= $this->sreplace ?>
	</div>

