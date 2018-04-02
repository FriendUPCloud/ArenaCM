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
			<div class="HeaderBox">
				<button type="button" onclick="createLibraryFile ( 1 )" title="<?= i18n ( 'i18n_save' ) ?>">
					<img src="admin/gfx/icons/disk.png"/>
				</button>
				<button type="button" onclick="removeModalDialogue ( 'newfile' )" title="<?= i18n ( 'i18n_close' ) ?>">
					<img src="admin/gfx/icons/cancel.png"/>
				</button>
			</div>
			<?= i18n ( 'i18n_create_new_file' ) ?>
		</h1>
		
		<div class="SubContainer">
			<table class="LayoutColumns">
				<tr>
					<td>
						<strong><?= i18n ( 'i18n_filetype' ) ?>:</strong>
					</td>
					<td>
						<select id="nfFiletype">
							<option value="css"><?= i18n ( 'i18n_a_stylesheet' ) ?></option>
							<option value="txt"><?= i18n ( 'i18n_a_text_file' ) ?></option>
							<option value="js"><?= i18n ( 'i18n_a_javascript' ) ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<strong><?= i18n ( 'i18n_filename' ) ?>:</strong>
					</td>
					<td>
						<input type="text" id="nfFilename" size="43">
					</td>
				</tr>
				<tr>
					<td>
						<strong><?= i18n ( 'i18n_file_contents' ) ?>:</strong>
					</td>
					<td>
						<textarea id="nfContent" rows="10" cols="45"></textarea>
					</td>
				</tr>
			</table>
		</div>
		<div class="SpacerSmall"></div>
		<div class="Container">
			<button type="button" onclick="createLibraryFile ( 1 )">
				<img src="admin/gfx/icons/disk.png"/> <?= i18n ( 'i18n_save' ) ?>
			</button>
			<button type="button" onclick="removeModalDialogue ( 'newfile' )">
				<img src="admin/gfx/icons/cancel.png"/> <?= i18n ( 'i18n_close' ) ?>
			</button>
		</div>
		
