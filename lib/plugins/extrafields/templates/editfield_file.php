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
	<?
		$this->name = "Extra_{$this->data->ID}_{$this->data->DataTable}_DataInt";
	?>
	
	<h2 class="BlockHead">
		<img src="admin/gfx/icons/file.png" /> <?= $this->data->Name ?>:
	</h2>
	<div class="BlockContainer">
		<table class="Gui" style="width: 100%">
			<tr>
				<td>
					<div class="SubContainer">
						<h2>
							Last opp en ny fil her
						</h2>
						<input type="file" id="<?= $this->name ?>" name="<?= $this->name ?>" />
					</div>
				</td>
				<td>
					<div class="Dropzone" id="Drop<?= $this->name ?>">
						Slipp en fil her
					</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="SubContainer" id="prev<?= $this->name ?>">
						<?
							if ( $this->data->DataInt )
							{
								$file = new dbObject ( 'File' );
								$file->load ( $this->data->DataInt );
								return $file->Title . ' (' . $file->Filename . ')';
							}
							return 'Ingen fil bruker dette feltet';
						?>
					</div>
				</td>
			</tr>
		</table>
	</div>
	
	<script>
		document.getElementById ( 'Drop<?= $this->name ?>' ).onDragDrop = function ( )
		{
			var jax = new bajax ( );
			jax.openUrl ( 'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Small&field=DataInt&id=<?= $this->data->ID ?>&value=' + dragger.config.objectID, 'get', true );
			jax.onload = function ( )
			{
				document.getElementById ( 'prev<?= $this->name ?>' ).innerHTML = 'La til fil med id: ' + dragger.config.objectID;
			}
			jax.send ( );
		}
		dragger.addTarget ( document.getElementById ( 'Drop<?= $this->name ?>' ) );
	</script>
	
