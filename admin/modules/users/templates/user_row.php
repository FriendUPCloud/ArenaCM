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
		<tr class="sw<?= $this->sw ?>">
			<td>
				<?
					$img = new dbImage ( $this->data->Image );
					if ( !$img->ID )
					{
						$ml = '<img src="admin/gfx/arenaicons/user_johndoe_32.png" width="32px" height="32px">';
					}
					else $ml = $img->getImageHTML ( 32, 32, 'framed' );
					return $ml;
				?>
			</td>
			<td style="text-align: center">
				<input type="text" value="<?= $this->data->SortOrder ?>" id="sortorder_<?= $this->data->ID ?>" size="2" style="width: 24px; text-align: right" onchange="setUserSortOrder(this.id)"/>
			</td>
			<td<?= $this->data->IsTemplate ? ' style="color: #a00"' : '' ?>>
				<?= $this->data->IsDisabled ? '(<span style="color: #aaa">' : '' ?>
				<?if ( $this->data->Name ) { ?>
					<strong><?= $this->data->Name ?></strong>,<br />
				<?}?>
				<?= $this->data->Username ?>
				<?= $this->data->IsDisabled ? '</span>)' : '' ?>
			</td>
			<td>
				<?= $this->InGroups ?>
			</td>
			<td>
				<?
					if ( strstr ( $this->DateModified, '1970' ) ) 
						return $this->DateCreated; 
					return $this->DateModified;
				?>
			</td>
			<td>
				<?
					if ( strstr ( $this->DateLogin, '1970' ) ) 
						return i18n ( 'Never logged in.' ); 
					return $this->DateLogin;
				?>
			</td>
			<td style="text-align: center">
				<?if ( $this->canWrite ) { ?>
					<input type="checkbox" onchange="if ( this.checked ) addToUniqueList ( 'seluserslist', '<?= $this->data->ID ?>' ); else remFromUniqueList ( 'seluserslist', '<?= $this->data->ID ?>' )" />
				<?}?>
			</td>
			<td style="text-align: center">
				<?if ( $this->canRead ) { ?>
				<button type="button" onclick="document.location='admin.php?module=users&function=user&uid=<?= $this->data->ID ?>';" class="Small">
					<img src="admin/gfx/icons/user_edit.png" />
				</button>
				<?}?>
				<button type="button" onclick="addToWorkbench ( '<?= $this->data->ID ?>', 'Users' )" class="Small">
					<img src="admin/gfx/icons/plugin.png" />
				</button>
			</td>
		</tr>
