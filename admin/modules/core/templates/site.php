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

	<form method="post">
		<input type="hidden" name="action" value="site"/>
		<input type="hidden" name="ID" value="<?= $this->site->ID ?>"/>
		<h1>
			<?= $this->site->ID ? ( 'Endre nettsiden: "' . $this->site->SiteName . '":' ) : 'Ny site' ?>
		</h1>
		<div class="SubContainer">
			<p>
				<strong>Site navn:</strong>
			</p>
			<p>
				<input type="text" value="<?= $this->site->SiteName ?>" name="SiteName" size="50"/>
			</p>
			<p>
				<strong>SQL Brukernavn:</strong>
			</p>
			<p>
				<input type="text" value="<?= $this->site->SqlUser ?>" name="SqlUser" size="15"/>
			</p>
			<p>
				<strong>SQL Passord:</strong>
			</p>
			<p>
				<input type="text" value="<?= $this->site->SqlPass ?>" name="SqlPass" size="15"/>
			</p>
			<p>
				<strong>SQL Server Adresse:</strong>
			</p>
			<p>
				<input type="text" value="<?= $this->site->SqlHost ?>" name="SqlHost" size="25"/>
			</p>
			<p>
				<strong>SQL Server Database:</strong>
			</p>
			<p>
				<input type="text" value="<?= $this->site->SqlDatabase ?>" name="SqlDatabase" size="50"/>
			</p>
			<p>
				<strong>BASE_URL (med trailing slash):</strong>
			</p>
			<p>
				<input type="text" value="<?= $this->site->BaseUrl ?>" name="BaseUrl" size="50"/>
			</p>
			<p>
				<strong>BASE_DIR (uten trailing slash):</strong>
			</p>
			<p>
				<input type="text" value="<?= $this->site->BaseDir ?>" name="BaseDir" size="50"/>
			</p>
		</div>
		<div class="SpacerSmall"></div>
		<button type="button" onclick="submit ( )">
			<img src="admin/gfx/icons/disk.png"/> Lagre
		</button>
		<?if ( $this->site->ID ) { ?>
		<button type="button" onclick="if ( confirm ( 'Er du helt sikker på at du ønsker å slette denne?' ) ) { document.location='admin.php?module=core&action=deletesite&cid=<?= $this->site->ID ?>'; }">
			<img src="admin/gfx/icons/bin.png"/> Slett
		</button>
		<?}?>
		<button type="button" onclick="removeModalDialogue ( 'site' )">
			<img src="admin/gfx/icons/cancel.png"/> Lukk
		</button>
	</form>	
