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
	<form method="post" action="admin.php?module=users&action=savecollection">
		<h1>
			<?= $this->collection->ID ? ( i18n ( 'Endre' ) . ': ' . $this->collection->Name ) : i18n ( 'New collection' ) ?>
		</h1>
		<input type="hidden" value="<?= ( $_REQUEST[ 'parentid' ] ? $_REQUEST[ 'parentid' ] : $this->collection->UserCollectionID ) ?>" class="hidden" name="UserCollectionID">
		<input type="hidden" value="<?= $this->collection->ID ?>" class="hidden" name="ID">
		<div class="Container">
			<table>
				<tr>
					<td>
						<strong><?= i18n ( 'Name' ) ?>:</strong>
					</td>
					<td>
						<input type="text" name="Name" value="<?= $this->collection->Name ?>">
					</td>
				</tr>
				<tr>
					<td>
						<strong><?= i18n ( 'Description' ) ?>:</strong>
					</td>
					<td>
						<textarea name="Description" cols="40" rows="5"><?= $this->collection->Description ?></textarea>
					</td>
				</tr>
			</table>
		</div>
		<div class="SpacerSmallColored"></div>
		<button>
			<img src="admin/gfx/icons/disk.png"> <?= i18n ( 'Save' ) ?>
		</button>
		<button onclick="removeModalDialogue ( 'collection' )">
			<img src="admin/gfx/icons/cancel.png"> <?= i18n ( 'Close' ) ?>
		</button>
	</form>
