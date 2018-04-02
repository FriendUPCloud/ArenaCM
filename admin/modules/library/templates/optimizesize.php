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

	<form name="OptimizeSizeForm" method="post">
		
		<input type="hidden" name="FolderID" value="<?= $this->folder->ID ?>"/>
		<input type="hidden" name="action" value="optimizesize"/>
		<input type="hidden" name="module" value="library"/>
		
		<h1>
			Optimaliser bildene i <?= $this->folder->Name ?>
		</h1>
		<div class="SubContainer">
			<p>
				Dette verktøyet lar deg spare plass på systemet, og gjøre reskaleringer
				av store bilder litt raskere. Dette verktøyet er spesiellt anvendbart for 
				deg som laster opp store bilder fra digitalkamera.
			</p>
		</div>
		<div class="SpacerSmall"></div>
		<div class="SubContainer">
			<p>
				Velg en oppløsning under som du ønsker å reskalere orginalbildene til. Bilder som
				er mindre enn ønsket størrelse blir hoppet over. <strong>Bildene vil alltid beholde aspekt</strong>.
			</p>
			<p>
				<input type="radio" name="OptimizeSize" value="1920x1080"/> 1920x1080
			</p>
			<p>
				<input type="radio" name="OptimizeSize" value="1600x1200"/> 1600x1200
			</p>
			<p>
				<input type="radio" checked="checked" name="OptimizeSize" value="1280x720"/> 1280x720
			</p>
			<p>
				<input type="radio" name="OptimizeSize" value="1024x768"/> 1024x768
			</p>
			<p>
				<input type="radio" name="OptimizeSize" value="800x600"/> 800x600
			</p>
			<p>
				<input type="radio" name="OptimizeSize" value="640x480"/> 640x480
			</p>
		</div>
		<div class="SpacerSmall"></div>
		<div class="SubContainer">
			<p>
				Velg en kvalitetsprosent fra 50%-100% (har å gjøre med hvor mye filstørrelsen
				skal forminskes på bekostning av bildekvalitet).
			</p>
			<p>
				<select name="OptimizeQuality">
					<?
						for ( $a = 100; $a >= 50; $a -= 5 )
						{
							$ostr .= '<option value="' . $a . '">' . $a . '</option>';
						}
						return $ostr;
					?>
				</select>
			</p>
		</div>
		<div class="SpacerSmall"></div>
		<button type="button" onclick="if ( confirm ( 'Er du sikker?' ) ){ document.OptimizeSizeForm.submit ( ); }">
			<img src="admin/gfx/icons/image.png"/> Start optimaliseringen
		</button>
		<button type="button" onclick="removeModalDialogue ( 'optimizesize' )">
			<img src="admin/gfx/icons/cancel.png"/> Lukk vinduet
		</button>
	</form>


