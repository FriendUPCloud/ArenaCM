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
	<table class="List">
		<tr>
			<th colspan="2"<?= $GLOBALS['Session']->LibraryListmode=='sortorder'?' class="Active"' : '' ?> width="48"><a href="admin.php?module=library&listmode=sortorder"><?= i18n ( 'Sortorder' ) ?>:</a></th>
			<th<?= $GLOBALS['Session']->LibraryListmode=='title'?' class="Active"' : '' ?>><a href="admin.php?module=library&listmode=title"><?= i18n ( 'Title' ) ?>:</a></th>
			<th<?= $GLOBALS['Session']->LibraryListmode=='filename'?' class="Active"' : '' ?>><a href="admin.php?module=library&listmode=filename"><?= i18n ( 'Filename' ) ?>:</a></th>
			<th<?= $GLOBALS['Session']->LibraryListmode=='filesize'?' class="Active"' : '' ?> width="70"><a href="admin.php?module=library&listmode=filesize"><?= i18n ( 'Size' ) ?>:</a></th>
			<th<?= $GLOBALS['Session']->LibraryListmode=='date'?' class="Active"' : '' ?> width="120"><a href="admin.php?module=library&listmode=date"><?= i18n ( 'Date modified' ) ?>:</a></th>
			<th width="80">#</th>
		</tr>
		<?= $this->contents ?>
	</table>
	<?= $this->nav ?>

