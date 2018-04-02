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

<form method="post" target="hiddenblogiframe" enctype="multipart/form-data" action="admin.php?module=extensions&extension=editor&mod=mod_blog&modaction=save" id="blogform">
	<h3 style="position: relative">
		<div style="float: right">
			<button title="Lagre artikkel" type="button" class="Small" onclick="mod_blog_save()">
				<img src="admin/gfx/icons/disk.png">
			</button>
			<?if ( $this->blog->ID ) { ?>
			<button title="Forhåndsvis" type="button" class="Small" onclick="mod_blog_preview(<?= $this->blog->ID ?>)">
				<img src="admin/gfx/icons/eye.png">
			</button>
			<?}?>
			<button title="Avbryt" type="button" class="Small" onclick="mod_blog_abortedit()">
				<img src="admin/gfx/icons/cancel.png">
			</button>
		</div>
		<span id="BlogItemName">
		<?if ( $this->blog ) { ?>
			<?= i18n ( 'i18n_Edit' ) ?>: <?= $this->blog->Title ?>
		<?}?>
		<?if ( !$this->blog ) { ?>
			<?= i18n ( 'i18n_New_article' ) ?>
		<?}?>
		</div>
	</h3>
	<input type="hidden" id="BlogIdentifier" name="bid" value="<?= $this->blog->ID ?>">
	<div class="SpacerSmallColored"></div>
	<div class="SubContainer" style="padding: <?= MarginSize ?>px">
		
		<table style="width: 100%">
			<tr>
				<td colspan="4">
					<table cellspacing="0" cellpadding="0" border="0" width="100%">
						<tr>
							<td style="width: 110px; vertical-align: top; padding-top: 4px">
								<strong><?= i18n ( 'i18n_Title' ) ?>:</strong>
							</td>
							<td style="vertical-align: top">
								<input type="text" name="Title" id="BlogTitle" size="28" value="<?= $this->blog->Title ?>" style="width: 100%; box-sizing: border-box"/>
							</td>
							<td style="vertical-align: top; padding-left: 8px; padding-top: 4px; width: 110px">
								<strong><?= i18n ( 'i18n_Short_Title' ) ?>:</strong>
							</td>
							<td style="vertical-align: top">
								<input type="text" name="SubTitle" id="BlogSubTitle" size="12" value="<?= $this->blog->SubTitle ?>" style="width: 100%; box-sizing: border-box"/>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?= i18n ( 'i18n_Showing_from' ) ?>:</strong>
				</td>
				<td colspan="2">
					<?= 
						str_replace ( 
							'<td><strong>Dato:</strong> </td>', '', 
							dateToPulldowns ( 
								'DatePublish', 
								( $this->blog->DatePublish ? $this->blog->DatePublish : date ( 'Y-m-d H:i:s' ) ) 
							)
						) 
					?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?= i18n ( 'i18n_Saved_date' ) ?>:</strong>
				</td>
				<td colspan="2">
					<?= 
						str_replace ( 
							'<td><strong>Dato:</strong> </td>', '', 
							dateToPulldowns ( 
								'DateUpdated', 
								( $this->blog->DateUpdated ? $this->blog->DateUpdated : date ( 'Y-m-d H:i:s' ) ) 
							)
						) 
					?>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?= i18n ( 'i18n_tags_categories' ) ?>:</strong>
				</td>
				<td colspan="2">
					<input type="text" name="Tags" id="BlogTags" value="<?= $this->blog->Tags ?>">
				</td>
			</tr>
			<tr>
				<td>
					<strong><?= i18n ( 'i18n_Author' ) ?>:</strong>
				</td>
				<td colspan="2">
					<input type="text" name="AuthorName" id="BlogAuthorName" value="<?= $this->blog ? $this->blog->AuthorName : $GLOBALS[ 'user' ]->Name ?>">
				</td>
			</tr>
			<tr>
				<td>
					<strong><?= i18n ( 'i18n_Published' ) ?>:</strong>
				</td>
				<td colspan="2">
					<input type="hidden" name="IsPublished" id="BlogIsPublished" value="<?= $this->blog->ID <= 0 ? '1' : $this->blog->IsPublished ?>">
					<input style="width: 22px; border: 0" type="checkbox"<?= ( $this->blog->ID <= 0 ? true : $this->blog->IsPublished ) ? ' checked="checked"' : '' ?> onchange="document.getElementById ( 'BlogIsPublished' ).value = this.checked ? '1' : '0'">
				</td>
			</tr>
			<tr>
				<td>
					<strong><?= i18n ( 'i18n_Sticky' ) ?>:</strong>
				</td>
				<td colspan="2">
					<input type="hidden" name="IsSticky" id="BlogIsSticky" value="<?= $this->blog->ID <= 0 ? '1' : $this->blog->IsSticky ?>">
					<input style="width: 22px; border: 0" type="checkbox"<?= ( $this->blog->ID <= 0 ? true : $this->blog->IsSticky ) ? ' checked="checked"' : '' ?> onchange="document.getElementById ( 'BlogIsSticky' ).value = this.checked ? '1' : '0'">
				</td>
			</tr>
			<tr><td colspan="3"><hr/></td></tr>
			<tr>
				<td>
					<strong><?= i18n ( 'i18n_Leadin_image' ) ?>:</strong>
				</td>
				<td style="width: 240px;">
					<table cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td>
								<div id="BlogImagePreview">
									<?
										if ( !$this->blog ) return;
										if ( $imgs = $this->blog->getObjects ( 'ObjectType = Image' ) )
										{
											foreach ( $imgs as $img )
											{
												if ( $img->Title != 'DetailImage' )
												{
													return $img->getImageHTML ( 64, 64, 'centered' ) . '<button type="button" onclick="mod_blog_removeimage(' . $this->blog->ID . ', ' . "'" . 'leadin' . "'" . ')"><img src="admin/gfx/icons/image_delete.png"/></button>';
												}
											}
										}
									?>
								</div>
							</td>
							<td><input type="file" name="Image"></td>
						</tr>
					</table>
				</td>
				<td rowspan="4">
					<table class="LayoutColumns">
						<tr>
							<td style="width: 110px; padding-left: 25px">
								<strong><?= i18n ( 'i18n_Image_folders' ) ?>:</strong>
							</td>
							<td>
								<input type="hidden" name="Folders" id="Folders_hidden" value="<?
									if ( !$this->blog ) return '';
									if ( $flds = $this->blog->getObjects ( 'ObjectType = Folder' ) )
									{
										$s = array ();
										foreach ( $flds as $f )
										{
											$s[] = $f->ID;
										}
										return implode ( ',', $s );
									}
									return '';
								?>"/>
								<select multiple="multiple" size="15" id="Folders" style="min-width: 160px">
								<?
									function recrFld ( $p, $depth, $cs )
									{
										global $database, $Session; 
										$str = '';
										$fld = new dbObject ( 'Folder' );
										if ( $rows = $fld->find ( '
											SELECT * FROM Folder WHERE Parent="' . (string)$p . '"
											ORDER BY `Name` ASC
										' ) )
										{
											foreach ( $rows as $row )
											{
												if ( !$Session->AdminUser->checkPermission (
													$row, 'read', 'admin' ) )
												{
													continue;
												}
												$str .= '<option value="' . $row->ID . '"' . ( in_array ( $row->ID, $cs ) ? ' selected="selected"' : '' ) . '>' . $depth . $row->Name . '</option>';
												$str .= recrFld ( $row->ID, $depth . '&nbsp;&nbsp;&nbsp;&nbsp;', $cs );
											}
											return $str;
										}
									}
									$ids = array ();
									if ( $this->blog )
									{
										foreach ( $this->blog->getObjects ( 'ObjectType = Folder' ) as $f )
										{
											$ids[] = $f->ID;
										}
									}
									return recrFld ( 0, '', $ids );
								?>
								</select>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="width: 110px">
					<strong><?= i18n ( 'i18n_Leadin_image_scalemode' ) ?>:</strong>
				</td>
				<td colspan="2">
					<select name="LeadinScalemode">
						<?
							$str = '';
							foreach ( array ( 'default', 'framed', 'proximity', 'centered', 'original' ) as $mode )
							{
								if ( $mode == $this->blog->LeadinScalemode )
									$s = ' selected="selected"';
								else $s = '';
								$str .= '<option value="' . $mode . '"' . $s . '>' . i18n ( 'i18n_' . $mode ) . '</option>';
							}
							return $str;
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?= i18n ( 'i18n_Detail_image' ) ?>:</strong>
				</td>
				<td style="width: 240px;">
					<table cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td>
								<div id="BlogDetailPreview">
									<? 
										if ( !$this->blog ) return;
										if ( $imgs = $this->blog->getObjects ( 'ObjectType = Image' ) )
										{
											foreach ( $imgs as $img )
											{
												if ( $img->Title == 'DetailImage' )
												{
													return $img->getImageHTML ( 64, 64, 'centered' ) . '<button type="button" onclick="mod_blog_removeimage(' . $this->blog->ID . ',' . "'" . 'detail' . "'" . ')"><img src="admin/gfx/icons/image_delete.png"/></button>';
												}
											}
										}
									?>
								</div>
							</td>
							<td><input type="file" name="DetailImage"></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="width: 110px">
					<strong><?= i18n ( 'i18n_Detail_image_scalemode' ) ?>:</strong>
				</td>
				<td colspan="2">
					<select name="DetailScalemode">
						<?
							$str = '';
							foreach ( array ( 'default', 'framed', 'proximity', 'centered', 'original' ) as $mode )
							{
								if ( $mode == $this->blog->DetailScalemode )
									$s = ' selected="selected"';
								else $s = '';
								$str .= '<option value="' . $mode . '"' . $s . '>' . i18n ( 'i18n_' . $mode ) . '</option>';
							}
							return $str;
						?>
					</select>
				</td>
			</tr>
			<tr><td colspan="3"><hr/></td></tr>
			<tr>
				<td>
					<strong><?= i18n ( 'i18n_Leadin' ) ?>:</strong>
				</td>
				<td colspan="2">
					<textarea class="mceSelector" name="Leadin" id="BlogLeadin" style="height: 120px"><?= $this->blog->Leadin ?></textarea>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?= i18n ( 'i18n_tags_link' ) ?>:</strong>
				</td>
				<td colspan="2">
					<input type="text" name="ExternalLink" id="BlogExternalLink" value="<?= $this->blog->ExternalLink ?>">
				</td>
			</tr>
			<tr>
				<td>
					<strong><?= i18n ( 'i18n_Article' ) ?>:</strong>
				</td>
				<td colspan="2">
					<textarea class="mceSelector" name="Body" id="BlogBody" style="height: 350px"><?= $this->blog->Body ?></textarea>
				</td>
			</tr>
		</table>
	</div>
	<div class="SpacerSmallColored"></div>
	<button type="button" onclick="mod_blog_save()">
		<img src="admin/gfx/icons/disk.png"> <span id="mod_blog_saveblog"><?= i18n ( 'i18n_Save_article' ) ?></span>
	</button>
	<?if ( $this->blog->ID ) { ?>
	<button type="button" onclick="mod_blog_preview(<?= $this->blog->ID ?>)">
		<img src="admin/gfx/icons/eye.png"> <span id="mod_blog_preview"><?= i18n ( 'i18n_Preview' ) ?></span>
	</button>
	<?}?>
	<button type="button" onclick="mod_blog_abortedit()">
		<img src="admin/gfx/icons/cancel.png"> <?= $this->blog ? i18n ( 'i18n_Close' ) : i18n ( 'i18n_Cancel' ) ?>
	</button>
</form>
