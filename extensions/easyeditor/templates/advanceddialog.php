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
	<div class="HeaderBox">
		<button type="button" onclick="SaveAdvancedOptions ()">
			<img src="admin/gfx/icons/disk.png"/>
		</button>
		<button type="button" onclick="SaveAdvancedOptions ( 1 )">
			<img src="admin/gfx/icons/accept.png"/>
		</button>
		<button type="button" onclick="removeModalDialogue ( 'advanced' )">
			<img src="admin/gfx/icons/cancel.png"/>
		</button>
	</div>
	<?= i18n ( 'Advanced options' ) ?>
</h1>
<div class="SubContainer" style="padding="2px">
	<p>
		<?= i18n ( 'i18n_EasyEdAdvancedInfo' ) ?>
	</p>
	<div class="Container" style="padding: 0">
		<table class="List" id="AdvChBoxen">
			<tr>
				<th><?= i18n ( 'i18n_FieldName' ) ?>:</th>
				<th width="12">#</th>
			</tr>
			<?= $this->efields ?>
		</table>
	</div>
</div>
<div class="SpacerSmallColored"></div>
<div>
	<button type="button" onclick="SaveAdvancedOptions ()">
		<img src="admin/gfx/icons/disk.png"/> <?= i18n ( 'Save' ) ?>
	</button>
	<button type="button" onclick="SaveAdvancedOptions ( 1 )">
		<img src="admin/gfx/icons/accept.png"/> <?= i18n ( 'Save and close' ) ?>
	</button>
	<button type="button" onclick="removeModalDialogue ( 'advanced' )">
		<img src="admin/gfx/icons/cancel.png"/> <?= i18n ( 'Close' ) ?>
	</button>
</div>
