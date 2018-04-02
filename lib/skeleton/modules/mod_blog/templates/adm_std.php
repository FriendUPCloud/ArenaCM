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
	<div>
		<div>
			<?= $this->blogs ? $this->blogs : 'Du har ingen artikler i arkivet.' ?>
		</div>
		<div class="SpacerSmallColored"></div>
		<?if ( !$this->settings->Sourcepage || $this->settings->Sourcepage == $this->content->MainID ) { ?>
		<button type="button" onclick="mod_blog_new()">
			<img src="admin/gfx/icons/newspaper.png"> <?= i18n ( 'Add article' ) ?>
		</button>
		<button type="button" onclick="mod_blog_authentication()">
			<img src="admin/gfx/icons/page_white.png"> <?= i18n ( 'Authenticate incoming articles' ) ?>
		</button>
		<?}?>
		<button type="button" onclick="mod_blog_settings()">
			<img src="admin/gfx/icons/wrench.png"> <?= i18n ( 'Edit settings' ) ?>
		</button>
		<?if ( $this->otherSourcepage ) { ?>
		<button type="button" onclick="document.location='admin.php?module=extensions&extension=editor&cid=<?= $this->sourcePage ?>';">
			<img src="admin/gfx/icons/page_go.png"> <?= i18n ( 'i18n_Go_to_source' ) ?>
		</button>
		<?}?>
	</div>

