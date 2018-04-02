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
<div class="ModuleContainer">
	<table class="LayoutColumns">
		<tr>
			<td width="25%" style="padding-right: <?= MarginSize ?>px">
				
				<form method="post" enctype="multipart/form-data" name="importform" action="admin.php?module=users&amp;action=importusers">
					
					<h1>
						<div style="float: right">
							<button type="submit" title="Start import">
								<img src="admin/gfx/icons/script_go.png"/>
							</button>
							<button type="button" onclick="document.location='admin.php?module=users'" title="Avbryt">
								<img src="admin/gfx/icons/cancel.png"/>
							</button>
						</div>
						<?= i18n ( 'Import users' ) ?>
					</h1>
				
					<div class="Container">
						
						<h2><?= i18n ( 'Fields to import, including sort index' ) ?>:</h2>
						<p>
							<table>
								<tr>
									<th><?= i18n ( 'Name' ) ?>:</th>
									<th><?= i18n ( 'Index' ) ?>:</th>
									<th>&#x2713;</th>
								</tr>
							<?= $this->Fields ?>
							</table>
						</p>
						<h2>
							<?= i18n ( 'File to import from' ) ?>:
						</h2>
						<p>
							<input type="file" name="filestream"/>
						</p>
						<h2>
							<?= i18n ( 'Add users to this group' ) ?>:
						</h2>
						<p>
							<select name="groupid[]" multiple="multiple" size="4">
								<option value="0"><?= i18n ( 'No group' ) ?></option>
								<?
									$obj = new dbObject ( 'Groups' );
									$obj->addClause ( 'ORDER BY', 'Name ASC' );
									if ( $objs = $obj->find ( ) ) 
									{
										foreach ( $objs as $obj )
										{
											$ostr .= '<option value="' . $obj->ID . '">' . $obj->Name . '</option>';
										}
										return $ostr;
									}
								?>
							</select>
						</p>
						<h2>
							<?= i18n ( 'Generate passwords automatically?' ) ?>
						</h2>
						<p>
							<input type="hidden" name="GeneratePassword" value="0"/>
							<input type="checkbox" onchange="document.importform.GeneratePassword.value = this.checked ? '1' : '0'"/>
						</p>
						<h2>
							<?= i18n ( 'Generate a logfile?' ) ?>
						</h2>
						<p>
							<input type="hidden" name="UseLogfile" value="0"/>
							<input type="checkbox" onchange="document.importform.UseLogfile.value = this.checked ? '1' : '0'"/>
						</p>
						<h2>
							<?= i18n ( 'E-mail address to send logfile' ) ?>:
						</h2>
						<p>
							<input type="text" value="" name="Email"/>
						</p>
						<h2>
							<?= i18n ( 'Column separation symbol' ) ?>:
						</h2>
						<p>
							<input type="text" value="," name="Separator"/>
						</p>
						<p>
							<button type="submit">
								<img src="admin/gfx/icons/script_go.png"/> <?= i18n ( 'Start import' ) ?>
							</button>
							<button type="button" onclick="document.location='admin.php?module=users'">
								<img src="admin/gfx/icons/cancel.png"/> <?= i18n ( 'Cancel' ) ?>
							</button>
						</p>
					</div>
				
				</form>
				
			</td>
		</tr>
	</table>
</div>
