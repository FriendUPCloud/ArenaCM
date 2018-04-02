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
		<?= $this->variant->ID ? 'Endre variant' : 'Ny variant' ?>
	</h1>
	<input type="hidden" id="vID" value="<?= $this->variant->ID ?>"/>
	<div class="SubContainer" style="padding: 2px">
		<table class="Gui" role="presentation">
			<tr>
				<td style="vertical-align: top">
					<h4>Nøkkelinformasjon</h4>
					<table class="List" role="presentation" style="border-right: 1px solid #909090">
						<tr class="sw1">
							<th style="text-align: left">
								Navn:
							</th>
							<td>
								<input type="text" id="vNativeName" value="<?= $this->variant->NativeName ?>"/>
							</td>
						</tr>
						<tr class="sw2">
							<th style="text-align: left">
								Kort navn:
							</th>
							<td>
								<input type="text" id="vName" value="<?= $this->variant->Name ?>"/>
							</td>
						</tr>
						<tr class="sw1">
							<th style="text-align: left">
								Url aktivator:
							</th>
							<td>
								<input type="text" id="vUrlActivator" value="<?= $this->variant->UrlActivator ?>"/>
							</td>
						</tr>
						<tr class="sw2">
							<th style="text-align: left">
								Baseurl:
							</th>
							<td>
								<input type="text" id="vBaseUrl" value="<?= $this->variant->BaseUrl ?>"/>
							</td>
						</tr>
						<tr class="sw1">
							<th style="text-align: left">
								Hovedspråk:
							</th>
							<td>
								<input type="hidden" id="vIsDefault" value="<?= $this->variant->IsDefault ?>"/>
								<input type="checkbox"<?= $this->variant->IsDefault ? ' checked="checked"' : '' ?> onclick="ge('vIsDefault').value=this.checked?'1':'0'"/>
							</td>
						</tr>
					</table>
				</td>
				<td style="vertical-align: top">
					<h4>
						Ressurser
					</h4>
					<p>
						<strong>Autotilegning av ressurser?</strong>
					</p>
					<p>
						<input type="hidden" id="vAutomaticResources" value="<?= $this->variant->AutomaticResources ?>"/>
						<input type="checkbox"<?= $this->variant->AutomaticResources ? ' checked="checked"' : '' ?> onclick="ge('vAutomaticResources').value=this.checked?'1':'0'"/>
					</p>
					<p>
						<strong>Spesifisér ressurser (én pr. linje):</strong>
					</p>
					<p>
						<textarea id="vResources" rows="5" cols="40" style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box"><?= $this->variant->Resources ?></textarea>
					</p>
				</td>
			</tr>
		</table>
	</div>
	<div class="SpacerSmall"></div>
	<div class="SubContainer">
		<button type="button" onclick="saveVariant()">
			<img src="admin/gfx/icons/disk.png"/> Lagre
		</button>
		<button type="button" onclick="removeModalDialogue ( 'variant' )">
			<img src="admin/gfx/icons/cancel.png"/> Lukk
		</button>
	</div>

