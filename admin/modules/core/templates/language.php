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
		<?= $this->language->ID ? 'Endre ' . $language->Name : 'Nytt språk' ?>
	</h1>

	<div class="SubContainer">
		
		<form name="languageform" id="languageform">
		
			<input type="hidden" value="<?= $this->language->ID ?>" name="ID">
			
			<h2>
				Kort navn:
			</h2>
			<p>
				<input type="text" name="Name" value="<?= $this->language->Name ?>" size="35">
			</p>
		
			<table class="LayoutColumns">
				<tr>
					<td>
						<h2>
							Navn (orginalspråk):
						</h2>
						<p>
							<input name="NativeName" type="text" value="<?= $this->language->NativeName ?>" size="7">
						</p>
					</td>
					<td>
						<h2>
							Er hovedspråk?
						</h2>
						<p>
							<input name="IsDefault" id="isdefaultlang" type="hidden" value="<?= $this->language->IsDefault ?>">
							<input type="checkbox"<?= $this->language->IsDefault ? ' checked="checked"' : '' ?> onchange="document.getElementById ( 'isdefaultlang' ).value = this.checked ? '1' : '0'">
						</p>
					</td>
				</tr>
			</table>
		
			<h2>
				Url aktivator:
			</h2>
			<p>
				<input type="text" name="UrlActivator" value="<?= $this->language->UrlActivator ?>" size="35">
			</p>
		
			<h2>
				BaseUrl:
			</h2>
			<p>
				<input type="text" name="BaseUrl" value="<?= $this->language->BaseUrl ?>" size="35">
			</p>
		
		</form>
		
	</div>

	<div class="SpacerSmall"></div>

	<div class="SubContainer">
		<button onclick="cfgSaveLanguage ( )">
			<img src="admin/gfx/icons/disk.png"> Lagre
		</button>
		<button onclick="removeModalDialogue ( 'language' )">
			<img src="admin/gfx/icons/cancel.png"> Lukk
		</button>
	</div>
