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
		<?if ( $this->Heading ) { ?>
		<h1>
			<?= $this->Heading ?>
		</h1>
		<?}?>
		
		<script type="text/javascript">
			var ObjectConnectionType = '<?= $this->objectConnectionType ?>';
			var ObjectConnectionId = '<?= $this->objectConnectionId ?>';
		</script>
		
		<?if ( $this->Container ) { ?>
		<div class="Container">
		<?}?>
			<div id="ObjectDropArea" class="Dropzone">
			
				Slipp objekter her
			
			</div>
			<div class="SpacerSmall"><em></em></div>
			<div id="Objects" class="Container">
			</div>
			
			<div class="SpacerSmall"><em></em></div>
			<div class="Upload">
				<button type="button" onclick="poc_doUploadObject()">
					<img src="admin/gfx/icons/attach.png"/> Last opp tilkobling
				</button>
				<button type="button" onclick="poc_emptyConnectedObjects()">
					<img src="admin/gfx/icons/cancel.png"/> Fjern tilkoblinger
				</button>
			</div>
			
		<?if ( $this->Container ) { ?>
		</div>
		<?}?>
		
		<script src="<?= $this->scriptDir ?>/main.js"></script>
