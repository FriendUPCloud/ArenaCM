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
	<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden !important;">
		<div style="position: absolute; top: 0; left: 0; right: 0; bottom: 40px">
			<textarea spellcheck="false" id="FieldContent_<?= $this->field->ID ?>"><?= $this->field->DataText ?></textarea>
		</div>
		<div style="position: absolute; top: auto; bottom: 0px; left: 0; right: 0; height: 40px; background: white">
			<div style="padding: 4px">
				<button type="button" onclick="ElementWindow ( ge ( 'FieldContent_<?= $this->field->ID ?>' ) ).save()">
					<img src="ac2/icsmall/disk.png"/> <?= i18n ( 'i18n_Save' ) ?>
				</button>
				<button type="button" onclick="ElementWindow ( ge ( 'FieldContent_<?= $this->field->ID ?>' ) ).save(1)">
					<img src="ac2/icsmall/accept.png"/> <?= i18n ( 'i18n_SaveAndClose' ) ?>
				</button>
				<button type="button" onclick="CloseWindow(ElementWindow(ge('FieldContent_<?= $this->field->ID ?>')))">
					<img src="ac2/icsmall/cancel.png"/> <?= i18n ( 'i18n_Close' ) ?>
				</button>
			</div>
		</div>
	</div>

	<script>
		var mw = ElementWindow ( ge ( 'FieldContent_<?= $this->field->ID ?>' ) );
		var ed = SimpleFriendEditor ( 'FieldContent_<?= $this->field->ID ?>' );
		if ( mw )
		{
			SetWindowFlag ( mw, 'width', 570 );
			SetWindowFlag ( mw, 'height', 280 );
			SetWindowFlag ( mw, 'min-width', 570 );
			SetWindowFlag ( mw, 'min-height', 280 );
			mw.style.overflow = 'hidden';
		}
		mw.save = function ( cl )
		{
			var obj = {
				'Type':       'arena',
				'ObjectType': '<?= $this->fieldType ?>',
				'ObjectID':   '<?= $this->field->ID ?>',
				'Data':       ed.getContent (),
				'Path':       '<?= $this->path ?>'
			}
			ExecuteDirective( 'savefile', obj );
			if ( cl ) CloseWindow ( mw );
			
		}
	</script>

