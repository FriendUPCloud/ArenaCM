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
		<?= i18n ( 'Add users to collections' ) ?>
	</h1>
	<form method="post" action="admin.php?module=users&action=addtocollection&apply=true">
		<div class="Container" style="padding: 2px">
			<input type="hidden" class="hidden" id="hiddenusers" value="" name="ids">
			<select name="collections[]" style="-moz-box-sizing: border-box; width: 100%;" size="15">
				<?= $this->cols ?>
			</select>
		</div>
	
		<div class="SpacerSmallColored"></div>
		<button type="submit">
			<img src="admin/gfx/icons/building_go.png"> <?= i18n ( 'Save' ) ?>
		</button>
		<button type="button" onclick="removeModalDialogue ( 'addtocollection' )">
			<img src="admin/gfx/icons/cancel.png"> <?= i18n ( 'Close' ) ?>
		</button>
	</form>
	
	<script type="text/javascript">
		var users = getUniqueListEntries ( 'seluserslist' );
		document.getElementById ( 'hiddenusers' ).value = users;
	</script>

