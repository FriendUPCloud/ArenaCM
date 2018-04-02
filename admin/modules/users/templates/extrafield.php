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
	<?= i18n ( 'Add extra field' ) ?>
</h1>
<div class="Container">
	<p>
		<strong><?= i18n ( 'Fieldtype' ) ?>:</strong>
	</p>
	<p>
		<select id="extraFieldType">
			<option value="text"><?= i18n ( 'Small textfield' ) ?></option>
			<option value="longtext"><?= i18n ( 'Big textfield' ) ?></option>
		</select>
	</p>
	<p>
		<strong><?= i18n ( 'Fieldname' ) ?>:</strong>
	</p>
	<p>
		<input type="text" value="" id="extraFieldName" size="30"/>
	</p>
</div>
<div class="SpacerSmall"></div>
<button type="button" onclick="executeExtraField ( )">
	<img src="admin/gfx/icons/accept.png" /> <?= i18n ( 'Ok' ) ?>
</button>
<button type="button" onclick="removeModalDialogue ( 'extrafield' )">
	<img src="admin/gfx/icons/cancel.png" /> <?= i18n ( 'Cancel' ) ?>
</button>
