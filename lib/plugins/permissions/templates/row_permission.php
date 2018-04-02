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
		<div class="pmPermissionRow" id="pm_<?= $this->permission->AuthType ?>Permission_<?= $this->permission->ID ?>" onmousedown="pmCheckItem<?= $this->PluginID ?> ( this )">
			<div class="<?= $this->switch ?>">
				<?
					$q = '"';
					$this->readCheck = " onchange={$q}pmSetPermission{$this->PluginID} ( 'Read', this.checked ? '1' : '0', '{$this->permission->ID}' ){$q}";
					$this->writCheck = " onchange={$q}pmSetPermission{$this->PluginID} ( 'Write', this.checked ? '1' : '0', '{$this->permission->ID}' ){$q}";
					$this->publCheck = " onchange={$q}pmSetPermission{$this->PluginID} ( 'Publish', this.checked ? '1' : '0', '{$this->permission->ID}' ){$q}";
					$this->struCheck = " onchange={$q}pmSetPermission{$this->PluginID} ( 'Structure', this.checked ? '1' : '0', '{$this->permission->ID}' ){$q}";
					$this->deleCheck = " onchange={$q}pmSetPermission{$this->PluginID} ( 'Delete', this.checked ? '1' : '0', '{$this->permission->ID}' ){$q}";
				?>
				<div class="pmPermissionTypes">
					<table cellspacing="0" cellpadding="2" border="0">
						<tr>
							<td style="width: 23px">
								<input type="checkbox"<?= $this->permission->Read ? ' checked="checked"' : '' ?><?= $this->readCheck ?>>	
							</td>
							<td style="width: 23px">
								<input type="checkbox"<?= $this->permission->Write ? ' checked="checked"' : '' ?><?= $this->writCheck ?>>	
							</td>
							<td style="width: 23px">
								<input type="checkbox"<?= $this->permission->Publish ? ' checked="checked"' : '' ?><?= $this->publCheck ?>>	
							</td>
							<td>
								<input type="checkbox"<?= $this->permission->Structure ? ' checked="checked"' : '' ?><?= $this->struCheck ?>>
							</td>
							<td style="width: 23px">
								<input type="checkbox"<?= $this->permission->Delete ? ' checked="checked"' : '' ?><?= $this->deleCheck ?>>
							</td>
						</tr>
					</table>
				</div>
				<div class="pmPermissionName">
					<?= $this->Name ?> <?= $this->Info ? ( '(' . $this->Info . ')' ) : '' ?>
				</div>
				<div class="pmBreak">
				</div>
			</div>
		</div>
