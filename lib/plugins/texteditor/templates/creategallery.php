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
		Sett opp et bildegalleri
	</h1>
	
	<table style="border-collapse: collapse; border-spacing: 0; width: 100%">
		<tr>
			<td width="65%" style="padding: 0 2px 0 0; vertical-align: top">
				<div class="SubContainer">
					<table>
						<tr>
							<td><strong>Kolonner:</strong></td>
							<td><input type="text" size="6" id="galColumns" value="3"/></td>
							<td><strong>Skaleringsmåte:</strong></td>
							<td>
								<select id="galScalemode">
									<option value="framed" selected="selected">Klippet</option>
									<option value="proximity">Behold aspekt</option>
									<option value="centered">Sentrer</option>
								</select>
							</td>
						</tr>
						<tr>
							<td><strong>Tommebilde bredde:</strong></td>
							<td><input type="text" size="6" id="galThumbWidth" value="120"/></td>
							<td><strong>Tommebilde høyde:</strong></td>
							<td><input type="text" size="6" id="galThumbHeight" value="120"/></td>
						</tr>
						<tr>
							<td><strong>Stort bilde bredde:</strong></td>
							<td><input type="text" size="6" id="galImageWidth" value="512"/></td>
							<td><strong>Stort bilde høyde:</strong></td>
							<td><input type="text" size="6" id="galImageHeight" value="480"/></td>
						</tr>
						<tr>
							<td><strong>Vis titler:</strong></td>
							<td><input type="checkbox" checked="checked" id="galShowTitles"/></td>
							<td><strong>Vis beskrivelse:</strong></td>
							<td><input type="checkbox" checked="checked" id="galShowDesc"/></td>
						</tr>
					</table>
				</div>
				<div class="SpacerSmall"></div>
				<div class="SubContainer" id="GalleryPreview" style="height: 300px; overflow: auto">
					<?= $this->gallery ?>
				</div>
			</td>
			<td width="25%" style="vertical-align: top">
				<div class="SubContainer" style="padding: 2px">
					<p>
						<strong>Velg mappe for galleri:</strong>
					</p>
					<select id="galSelected" style="display: block; width: 100%" size="10" onchange="fetchGalleryPreview ( this.value )">
						<?= $this->options ?>
					</select>
					<p>
						<strong>Oppdater galleriet:</strong>
					</p>
					<p>
						<button type="button" onclick="fetchGalleryPreview ( document.getElementById ( 'galSelected' ).value )">
							<img src="admin/gfx/icons/arrows_refresh.png"/> Frisk opp galleri
						</button>
						<button type="button" onclick="insertGallery ( )">
							<img src="admin/gfx/icons/accept.png"/> Sett inn galleri
						</button>
					</p>
				</div>
			</td>
		</tr>
	</table>
	
	<div class="SpacerSmall"></div>
	<p>
		<button type="button" onclick="removeModalDialogue ( 'gallery' );">
			<img src="admin/gfx/icons/cancel.png"/> Lukk
		</button>
	</p>

