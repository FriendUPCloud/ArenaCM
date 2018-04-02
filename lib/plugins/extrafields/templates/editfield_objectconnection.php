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
	<div class="SpacerSmall"></div>
	<strong><?= $this->data->Name ?>:</strong>
	<div class="SpacerSmall"></div>
	<?
		$this->name = "Extra_{$this->data->ID}_{$this->data->DataTable}_DataString";
	?>
	<div class="Dropzone" id="Drop<?= $this->name ?>">
		Slipp objekter her
	</div>
	<div class="SpacerSmall"></div>		
	<div class="SubContainer" id="Preview<?= $this->name ?>">
		<?= showConnectedObjects ( $this->data->ContentID, $this->data->ContentTable ); ?>
	</div>
	<script>
		
		function Init<?= $this->name ?>( )
		{
			var ele = document.getElementById ( 'Drop<?= $this->name ?>' );
			dragger.addTarget ( ele );
			ele.onDragDrop = function ( )
			{
				var bjax = new bajax ( );
				bjax.openUrl ( 'admin.php', 'post', true );
				bjax.addVar ( 'plugin', 'extrafields' );
				bjax.addVar ( 'pluginaction', 'connectobject' );
				bjax.addVar ( 'coid', dragger.config.objectID );
				bjax.addVar ( 'cotype', dragger.config.objectType );
				bjax.addVar ( 'oid', '<?= $this->data->ContentID ?>' );
				bjax.addVar ( 'otype', '<?= $this->data->ContentTable ?>' );
				bjax.onload = function ( )
				{
					document.getElementById ( 'Preview<?= $this->name ?>' ).innerHTML = this.getResponseText ( );
				}
				bjax.send ( );
			}
		}
		Init<?= $this->name ?>( );
		
	</script>
	<div class="SpacerSmall"></div>
	
