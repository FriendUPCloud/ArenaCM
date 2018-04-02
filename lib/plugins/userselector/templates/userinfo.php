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

	<h1 id="userselectorh1">
		<?= $this->user->ID ? ( 'Endre ' . ( $this->user->Name ? $this->user->Name : $this->user->Email ) ) : 'Ny bruker' ?>
	</h1>
	<iframe class="Hidden" name="useriframe"></iframe>
	<form method="post" id="userselectorform" action="admin.php?plugin=userselector&pluginaction=saveuser" target="useriframe">
		<input type="hidden" id="userselectorID" value="<?= $this->user->ID ? $this->user->ID : '0' ?>" name="ID">
		<div class="Container">
			<table class="Form">
				<tr>
					<td><strong>Navn:</strong></td>
					<td><input type="text" name="Name" value="<?= $this->user->Name ?>" size="30"></td>
				</tr>
				<tr>
					<td><strong>E-mail:</strong></td>
					<td><input type="text" name="Email" value="<?= $this->user->Email ?>" size="30"></td>
				</tr>
				<tr>
					<td><strong>Grupper:</strong></td>
					<td><?= $this->groups ?></td>
				</tr>
				<tr>
					<td><strong>Beskrivelse:</strong></td>
					<td><textarea name="Description" cols="32" rows="5"><?= $this->user->Description ?></textarea></td>
				</tr>
			</table>
		</div>
		<div class="SpacerSmallColored"></div>
		<button type="button" onclick="document.getElementById ( 'userselectorform' ).submit ( )">
			<img src="admin/gfx/icons/disk.png"> Lagre
		</button>
		<button type="button" onclick="var str = document.location+''; document.location = str.split('?')[0] + '?' + ( Math.floor ( Math.random () * 100 ) )">
			<img src="admin/gfx/icons/cancel.png"> Lukk
		</button>
	</form>

