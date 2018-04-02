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
	<h2>
		Innstillinger for Adobe Flash fil:
	</h2>
	<div class="SpacerSmall"></div>

	<div class="SubContainer" id="SwfProperties">
	
		<table class="Gui" style="width: 100%">
			<tr>
				<td>
					<strong>Bredde:</strong>
				</td>
				<td>
					<input type="text" size="8" name="Width" value="<?= $this->file->Width ?>"/>
				</td>
				<td>
					<strong>Høyde:</strong>
				</td>
				<td>
					<input type="text" size="8" name="Height" value="<?= $this->file->Height ?>"/>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Bakgrunn:</strong>
				</td>
				<td>
					<input type="text" size="10" name="Background" value="<?= $this->file->Background ?>"/>
				</td>
				<td>
					<strong>ID:</strong>
				</td>
				<td>
					<input type="text" size="15" name="DivID" value="<?= $this->file->DivID ?>"/>
				</td>
			</tr>
			<tr>
				<td	colspan="4">
					<h2>
						Skriv inn variablene slik: 
					</h2>
					<div class="SubContainer">
						<code>minvariabel1=verdi&minvariabel2=verdi</code>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Variabler:</strong>
				</td>
				<td colspan="3">
					<input type="text" size="40" name="Variables" value="<?= $this->file->Variables ?>"/>
				</td>
			</tr>
			<tr>
				<td colspan="4">
					<p><strong>Hent HTML:</strong></p>
					<div class="SubContainer" style="padding: 2px">
						<div style="width: 692px; overflow: auto">
						<pre>&lt;object<?if ( $this->file->DivID ) { ?> id="<?= $this->file->DivID ?>"<?}?> data="upload/<?= $this->file->Filename . ( $this->file->Variables ? ( '?' . $this->file->Variables ) : '' ) ?>" width="<?= $this->file->Width ?>" height="<?= $this->file->Height ?>" wmode="transparent" type="application/x-shockwave-flash"&gt;
	&lt;param name="width" value="<?= $this->file->Width ?>"/&gt;
	&lt;param name="height" value="<?= $this->file->Height ?>"/&gt;
	&lt;param name="movie" value="upload/<?= $this->file->Filename . ( $this->file->Variables ? ( '?' . $this->file->Variables ) : '' ) ?>"/&gt;
	&lt;param name="wmode" value="transparent"/&gt;
&lt;/object&gt;</pre>
						</div>
					</div>
				</td>
			</tr>
		</table>
		
	</div>
	
	<div class="SpacerSmall"></div>
	
	<div>
		<button type="button" onclick="submitSwf()"><img src="admin/gfx/icons/page_go.png" /> Lagre</button>
		<button type="button" onclick="removeModalDialogue ( 'EditLevel' )"><img src="admin/gfx/icons/cancel.png" /> Lukk</button>
	</div>
	
	
	
	
	
