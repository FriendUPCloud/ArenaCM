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
	<div class="SubContainer">
		<table>
			<tr>
				<td>
					<strong>Antall artikler pr. side:</strong>
				</td>
				<td>
					<input type="text" id="mod_blog_limit" size="10" value="<?= $this->settings->Limit ?>">
				</td>
			</tr>
			<tr>
				<td>
					<strong>Bruker kommentarer:</strong>
				</td>
				<td>
					<input type="checkbox" id="mod_blog_comments"<?= $this->settings->Comments ? ' checked="checked"' : '' ?>>
				</td>
			</tr>
			<tr>
				<td>
					<strong>Viser forfatter:</strong>
				</td>
				<td>
					<input type="checkbox" id="mod_blog_showauthor"<?= $this->settings->ShowAuthor ? ' checked="checked"' : '' ?>>
				</td>
			</tr>
		</table>
	</div>
	<div class="SpacerSmallColored"></div>
	<button type="button" onclick="mod_blog_savesettings ( )">
		<img src="admin/gfx/icons/disk.png"> <span id="mod_blog_savetext">Lagre innstillingene</span>
	</button>
	<button type="button" onclick="mod_blog_abortedit ( )">
		<img src="admin/gfx/icons/newspaper.png"> Vis blog arkivet
	</button>

