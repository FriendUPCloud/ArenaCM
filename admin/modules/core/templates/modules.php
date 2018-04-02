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
			Sett opp moduler på "<?= $this->site->SiteName ?>":
		</h1>
		<form name="modulelist" method="post">
		
			<input type="hidden" name="action" value="modules"/>
			<input type="hidden" name="SiteID" value="<?= $this->site->ID ?>"/>
			
			<input type="hidden" name="settings" value="<?= $this->modules[ 'settings' ]->ID ? $this->modules[ 'settings' ]->ID : '0' ?>"/>
			<input type="hidden" name="users" value="<?= $this->modules[ 'users' ]->ID ? $this->modules[ 'users' ]->ID : '0' ?>"/>
			<input type="hidden" name="contents" value="<?= $this->modules[ 'contents' ]->ID ? $this->modules[ 'contents' ]->ID : '0' ?>"/>
			<input type="hidden" name="news" value="<?= $this->modules[ 'news' ]->ID ? $this->modules[ 'news' ]->ID : '0' ?>"/>
			<input type="hidden" name="library" value="<?= $this->modules[ 'library' ]->ID ? $this->modules[ 'library' ]->ID : '0' ?>"/>
			<input type="hidden" name="extensions" value="<?= $this->modules[ 'extensions' ]->ID ? $this->modules[ 'extensions' ]->ID : '0' ?>"/>
			
			<div class="SubContainer">
				<table>
					<tr>
						<td><strong>Innstillinger:</strong></td>
						<td><input onchange="seth( 'settings', this )" type="checkbox"<?= $this->modules[ 'settings' ]->ID ? ' checked="checked"' : '' ?>/></td>
					</tr>
					<tr>
						<td><strong>Brukere:</strong></td>
						<td><input onchange="seth( 'users', this )" type="checkbox"<?= $this->modules[ 'users' ]->ID ? ' checked="checked"' : '' ?>/></td>
					</tr>
					<tr>
						<td><strong>Innhold:</strong></td>
						<td><input onchange="seth( 'contents', this )" type="checkbox"<?= $this->modules[ 'contents' ]->ID ? ' checked="checked"' : '' ?>/></td>
					</tr>
					<tr>
						<td><strong>Nyheter:</strong></td>
						<td><input onchange="seth( 'news', this )" type="checkbox"<?= $this->modules[ 'news' ]->ID ? ' checked="checked"' : '' ?>/></td>
					</tr>
					<tr>
						<td><strong>Bibliotek:</strong></td>
						<td><input onchange="seth( 'library', this )" type="checkbox"<?= $this->modules[ 'library' ]->ID ? ' checked="checked"' : '' ?>/></td>
					</tr>
					<tr>
						<td><strong>Utvidelser:</strong></td>
						<td><input onchange="seth( 'extensions', this )" type="checkbox"<?= $this->modules[ 'extensions' ]->ID ? ' checked="checked"' : '' ?>/></td>
					</tr>
				</table>
			</div>
			<div class="SpacerSmall"></div>
			<button type="button" onclick="submit ( )">
				<img src="admin/gfx/icons/disk.png"/> Lagre listen
			</button>
			<button type="button" onclick="removeModalDialogue ( 'modules' )">
				<img src="admin/gfx/icons/cancel.png"/> Lukk
			</button>
		
		</form>
		
		<script>
			function seth ( key, obj )
			{
				document.modulelist[ key ].value = obj.checked ? 'saveme' : '0';
			}
		</script>
