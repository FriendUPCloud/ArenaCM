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
		<?= i18n ( 'File upload' ) ?>
	</h1>
	<form method="post" enctype="multipart/form-data" action="admin.php?module=extensions&extension=easyeditor&action=uploadfile&pid=<?= $_REQUEST[ 'pid' ] ?>">
		<div class="Container">
			<p>
				<input type="file" name="uploadfile"/>
			</p>
		</div>
		<div class="SpacerSmall"></div>
		<p>
			<button type="submit"><img src="admin/gfx/icons/disk.png"/> <?= i18n ( 'Start uploading' ) ?></button>
			<button type="button" onclick="removeModalDialogue('uploadfile')">
				<img src="admin/gfx/icons/cancel.png"/> <?= i18n ( 'Close' ) ?>
			</button>
		</p>
	</form>
