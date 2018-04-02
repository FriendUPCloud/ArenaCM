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
		<?= $this->top ?>
		<div class="SpacerSmall"></div>
		<table border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td valign="middle">
					<iframe name="<?= $this->fieldID . 'iframe' ?>" class="Hidden"></iframe>
					<form name="<?= $this->fieldID . 'form' ?>" method="post" action="admin.php?module=extensions&extension=editor&action=imagefield&field=<?= $this->field->ID ?>" enctype="multipart/form-data" target="<?= $this->fieldID . 'iframe' ?>">
						<input type="file" class="ExtraFieldData" name="filestream" id="imagefield_<?= $this->fieldID ?>">
					</form>
				</td>
				<td>&nbsp;</td>
				<td width="32" valign="middle">
					<div class="Box" style="width: 32px; height: 32px; border: 1px solid #ccc; background: #eee;" id="imagepreview_<?= $this->field->ID ?>">
						<?
							if ( $this->field->DataInt )
							{
								$i = new dbImage ( $this->field->DataInt );
								return $i->getImageHTML ( 32, 32, 'framed' );
							}
						?>
					</div>
				</td>
				<?if ( $this->field->DataInt ) { ?>
				<td>&nbsp;</td>
				<td valign="middle">
					<button class="Small" type="button" onclick="removeEFImage ( <?= $this->field->DataInt ?> )"><img src="admin/gfx/icons/image_delete.png"> <?= i18n ( 'Remove image' ) ?></button>
				</td>
				<?}?>
			</tr>
		</table>
		<script>
			AddSaveFunction ( function ( )
			{
				if ( document.getElementById ( 'imagefield_<?= $this->fieldID ?>' ).value )
				{
					document.getElementById ( 'imagefield_<?= $this->fieldID ?>' ).parentNode.submit ( );
				}
			}
			);
		</script>
