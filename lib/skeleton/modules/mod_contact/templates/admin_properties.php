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
			<?= i18n ( 'i18n_properties_heading' ) ?> "<?= $_REQUEST[ 'field' ] ?>"
		</h1>
		<div class="Container">
			<table class="LayoutColumns">
				<tr>
					<td>
						<strong><?= i18n ( 'i18n_input_type' ) ?>:</strong>
					</td>
					<td>
						<select id="fFieldType">
							<?= $this->types ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?= i18n ( 'i18n_input_data' ) ?>:</strong>
					</td>
					<td>
						<textarea rows="5" cols="20" id="fFieldData"><?= $this->data ?></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?= i18n ( 'i18n_input_required' ) ?>:</strong>
					</td>
					<td>
						<input type="hidden" id="fFieldRequired" value="<?= $this->required ? '1' : '0' ?>"/>
						<input type="checkbox"<?= $this->required ? ' checked="checked"' : '' ?> onchange="ge('fFieldRequired').value=this.checked?'1':'0'"/>
					</td>
				</tr>
			</table>
		</div>
		<div class="SpacerSmallColored"></div>
		<div class="Container">
			<button type="button" onclick="saveContactFieldValue('<?= $_REQUEST[ 'field' ] ?>', '<?= $this->field->ID ?>')">
				<img src="admin/gfx/icons/accept.png"/> <?= i18n ( 'i18n_save_close_props' ) ?>
			</button>
			<button type="button" onclick="removeModalDialogue ( 'properties' )">
				<img src="admin/gfx/icons/cancel.png"/> <?= i18n ( 'i18n_close_properties' ) ?>
			</button>
		</div>

