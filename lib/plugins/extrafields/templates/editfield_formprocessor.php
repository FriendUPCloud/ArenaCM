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
	<div class="ToggleBox">

		<h2 class="BlockHead">
			<img src="admin/gfx/icons/table.png" /> Skjema prosessering - <?= $this->data->Name ?>:
		</h2>
		<div class="BlockContainer">
			<div style="float: right">
				<button type="button" class="WebsnippetConfig">
					<img src="admin/gfx/icons/page.png" /> Innstillinger
				</button>
			</div>
			<br style="clear: both"/>
		</div>
		<div class="BlockContainer">
			<?
				$this->name = "Extra_{$this->data->ID}_{$this->data->DataTable}_DataText";
				list ( 
					$this->data_1, $this->data_2, $this->data_3, $this->data_4, 
					$this->data_5, $this->data_6, $this->data_7 
				) = explode ( "\\t", $this->data->DataText );
			?>
			<p>
				<strong>Mail mottakere (komma separert): </strong>
			</p>
			<p>
				<input type="text" style="-moz-box-sizing: border-box; box-sizing: border-box; width: 100%;" id="<?= $this->name ?>_1" size="45" value="<?= $this->data_1 ?>" />
			</p>
			<p>
				<strong>Mail tittel/emne: </strong> <input type="text" id="<?= $this->name ?>_2" value="<?= $this->data_2 ?>" />
				<strong>Skjema felt prefix: </strong> <input type="text" id="<?= $this->name ?>_3" value="<?= $this->data_3 ?>" />
			</p>
			<p>
				<strong>Tekst ved forsendelse:</strong>
			</p>
			<textarea class="mceSelector" id="<?= $this->name ?>_4"></textarea>
			<div class="Hidden" id="<?= $this->name ?>_hidden4"><?= $this->data_4 ?></div>
			<div class="SpacerSmall"></div>
			<p>
				<strong>Felt som skal vise responstekst: </strong>
			</p>
			<p>
				<input type="text" style="-moz-box-sizing: border-box; box-sizing: border-box; width: 100%;" id="<?= $this->name ?>_7" size="30" value="<?= $this->data_7 ?>" />
			</p>
			<div class="SpacerSmall"></div>
			<p>
				<strong>Autosvar tittel: </strong> <input type="text" id="<?= $this->name ?>_5" value="<?= $this->data_5 ?>" />
			</p>
			<p>
				<strong>Autosvar til avsender:</strong>
			</p>
			<textarea class="mceSelector" id="<?= $this->name ?>_6"></textarea>
			<div class="Hidden" id="<?= $this->name ?>_hidden6"><?= $this->data_6 ?></div>
			<script>
				document.getElementById ( '<?= $this->name ?>_4' ).value = document.getElementById ( '<?= $this->name ?>_hidden4' ).innerHTML;
				document.getElementById ( '<?= $this->name ?>_6' ).value = document.getElementById ( '<?= $this->name ?>_hidden6' ).innerHTML;
			</script>
			
			<div class="SpacerSmall"></div>
			
			<button type="button" onclick="swapToggleVisibility ( this.parentNode, this.parentNode.sibling )">
				<img src="admin/gfx/icons/cancel.png" /> Lukk
			</button>
			
		</div>
	</div>
	<script>
		AddSaveFunction ( function ( )
		{
			var str = '';
			str += document.getElementById ( '<?= $this->name ?>_1' ).value + "\t";
			str += document.getElementById ( '<?= $this->name ?>_2' ).value + "\t";
			str += document.getElementById ( '<?= $this->name ?>_3' ).value + "\t";
			str += editor.getContent ( '<?= $this->name ?>_4' ) + "\t";
			str += document.getElementById ( '<?= $this->name ?>_5' ).value + "\t";
			str += editor.getContent ( '<?= $this->name ?>_6' ) + "\t";
			str += document.getElementById ( '<?= $this->name ?>_7' ).value;
			actionExtraField ( 'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Big&id=<?= $this->data->ID ?>&field=DataText&value=' + encodeURIComponent ( str ) );
		}
		);
	</script>
	
