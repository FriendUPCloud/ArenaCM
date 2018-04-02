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
	<h2 class="BlockHead">
		<?if ( $this->object->_tableName == 'ContentElement' && $this->PermissionType != 'admin' ) { ?>
		<div style="float: right">
			<table>
				<tr>
					<td>
						Bruk beskyttelse:
					</td>
					<td>
						<input type="checkbox"<?= $this->object->IsProtected ? ' checked="checked"' : '' ?> onchange="pmSetProtectedFlag<?= $this->PluginID ?> ( '<?= $this->object->ID ?>', '<?= $this->object->_tableName ?>', this.checked ? '1' : '0', this )" style="margin-top: -1px">
					</td>
				</tr>
			</table>
		</div>
		<?}?>
		<?= strtoupper ( $this->PermissionType{0} ) . substr ( $this->PermissionType, 1, strlen ( $this->PermissionType ) - 1 ) ?>rettigheter for <?= $this->object->getIdentifier ( ) ?> (<?= $this->ContentType ?>):
	</h2>
	<input type="hidden" id="pmContentType<?= $this->PluginID ?>" value="<?= $this->object->_tableName ?>">
	<input type="hidden" id="pmContentID<?= $this->PluginID ?>" value="<?= $this->object->ID ?>">
	<input type="hidden" id="pmPermissionType<?= $this->PluginID ?>" value="<?= $this->PermissionType ?>">
	
	<div class="BlockContainer" id="pmTable<?= $this->PluginID ?>">
		<table width="100%" cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td width="55%" style="padding-right: <?= MarginSize ?>px">
					<table width="100%" cellspacing="0" cellpadding="2" border="0" style="margin-bottom: 2px">
						<tr>
							<th width="*">
								<h2>Grupperettigheter:</h2>
							</th>
							<th style="width: 23px">
								<img src="admin/gfx/icons/eye.png" title="Lese">
							</th>
							<th style="width: 23px">
								<img src="admin/gfx/icons/page_edit.png" title="Skrive">
							</th>
							<th style="width: 23px">
								<img src="admin/gfx/icons/page_go.png" title="Publisere">
							</th>
							<th style="width: 23px">
								<img src="admin/gfx/icons/chart_organisation.png" title="Strukturere">
							</th>
							<th style="width: 23px">
								<img src="admin/gfx/icons/bin.png" title="Slette">
							</th>
						</tr>
					</table>
					<div id="pmGroupRights<?= $this->PluginID ?>">
						<?= pmGroupPermissions ( $this->object, $this->PermissionType, $this->PluginID ) ?>
					</div>
				</td>
				<td width="24px" style="vertical-align: middle">
					<p>
						<button type="button" onclick="pmAddGroups<?= $this->PluginID ?> ( )">
							<img src="admin/gfx/icons/arrow_left.png">
						</button>
					</p>
					<p>
						<button type="button" onclick="pmDelGroups<?= $this->PluginID ?> ( )">
							<img src="admin/gfx/icons/arrow_right.png">
						</button>
					</p>
				</td>
				<td width="45%" style="padding-left: <?= MarginSize ?>px">
					<table><tr><th><h2>&nbsp;</h2></th></tr></table>
					<div id="pmGroups<?= $this->PluginID ?>">
						<?= pmGetGroups ( $this->PluginID ) ?>
					</div>
				</td>
			</tr>
			<tr>
				<td style="padding-right: <?= MarginSize ?>px">
					<table width="100%" cellspacing="0" cellpadding="2" border="0" style="margin-bottom: 2px">
						<tr>
							<th width="*">
								<h2>Brukerrettigheter:</h2>
							</th>
							<th style="width: 23px">
								<img src="admin/gfx/icons/eye.png" title="Lese">
							</th>
							<th style="width: 23px">
								<img src="admin/gfx/icons/page_edit.png" title="Skrive">
							</th>
							<th style="width: 23px">
								<img src="admin/gfx/icons/page_go.png" title="Publisere">
							</th>
							<th style="width: 23px">
								<img src="admin/gfx/icons/chart_organisation.png" title="Strukturere">
							</th>
							<th style="width: 23px">
								<img src="admin/gfx/icons/bin.png" title="Slette">
							</th>
						</tr>
					</table>
					<div id="pmUserRights<?= $this->PluginID ?>">
						<?= pmUserPermissions ( $this->object, $this->PermissionType, $this->PluginID ) ?>
					</div>
				</td>
				<td style="vertical-align: middle">
					<p>
						<button type="button" onclick="pmAddUsers<?= $this->PluginID ?> ( )">
							<img src="admin/gfx/icons/arrow_left.png">
						</button>
					</p>
					<p>
						<button type="button" onclick="pmDelUsers<?= $this->PluginID ?> ( )">
							<img src="admin/gfx/icons/arrow_right.png">
						</button>
					</p>
				</td>
				<td style="padding-left: <?= MarginSize ?>px">
					<table><tr><th><h2>&nbsp;</h2></th></tr></table>
					<div id="pmUsers<?= $this->PluginID ?>">
						<?= pmGetUsers ( $this->PluginID ) ?>
					</div>
				</td>
			</tr>
		</table>
	</div>
	
	<style type="text/css">
		<?= str_replace ( "!ID!", $this->PluginID, $this->css ) ?>
	</style>
	<script type="text/javascript">
		<?= str_replace ( "!ID!", $this->PluginID, file_get_contents ( $this->javascript ) ) ?>
	</script>

