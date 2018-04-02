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

	<?= $this->texteditor ?>
	<h3>
		<?= $this->heading ?>
	</h3>
	<div class="Block">
		<table>
			<tr>
				<td><label><?= i18n ( 'Blog title' ) ?>:</label></td>
				<td><input type="text" value="" id="blog_title" size="35"/></td>
			</tr>
			<tr>
				<td><label><?= i18n ( 'Your name' ) ?>:</label></td>
				<td><input type="text" value="" id="blog_author_name" size="35"/></td>
			</tr>
			<tr>
				<td><label><?= i18n ( 'Leadin' ) ?>:</label></td>
				<td><div class="Editor"><textarea id="blog_leadin" class="mceSelector" style="height: 100px" rows="5" cols="35"></textarea></div></td>
			</tr>
			<tr>
				<td><label><?= i18n ( 'Full article' ) ?>:</label></td>
				<td><div class="Editor"><textarea id="blog_article" class="mceSelector" style="height: 250px" rows="20" cols="35"></textarea></div></td>
			</tr>
			<tr>
				<td></td>
				<td><p>
					<button type="button" onclick="mod_blog_tip_send()">
						<?= i18n ( 'Send us your tip' ) ?>
					</button>
				</p></td>
			</tr>
		</table>
	</div>
	
