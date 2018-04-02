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
	<div id="ImageEditContainer">
		<?if( $this->image->ID ){?>
			<h1>
				<div class="HeaderBox MsieFix">
					<button onclick="removeModalDialogue ( 'EditLevel' );" style="float: right">
						<img src="admin/gfx/icons/cancel.png" alt="cancel"/>
					</button>
				</div> 
				<?= i18n ( 'i18n_edit_image' ) ?>
			</h1>
		<?}?>
		<?if( !$this->image->ID ){?>
			<h1>
				<div class="HeaderBox">
					<button onclick="removeModalDialogue ( 'EditLevel' );">
						<img src="admin/gfx/icons/cancel.png" alt="cancel"/>
					</button>
				</div>
				<?= i18n ( 'i18n_add_image' ) ?>
			</h1>
		<?}?>
		<div id="uploadTabs">
			<?if( !$this->image->ID ){?>
			<div class="tab" id="tabRediger">
				<img src="admin/gfx/icons/image.png"/>
				<?= $this->image->ID ? i18n ( 'i18n_edit_image' ) : i18n ( 'i18n_add_image' ) ?>
			</div>
			<div class="tab" id="tabMultiple">
				<img src="admin/gfx/icons/images.png"/> <?= i18n ( 'i18n_batch_images' ) ?>
			</div>
			<br style="clear: both">
			<div class="page" id="pageRediger">
			<?}?>
				<iframe style="visibility: hidden; width: 1px; height: 1px; position: absolute" name="ImageUpload"></iframe>
				<form method="post" enctype="multipart/form-data" id="uploadForm" name="uploadForm" action="admin.php?module=library&action=saveimage" target="ImageUpload">
					<table style="display: block; border-spacing: 0; border-collapse: collapse;">
						<tr>
							<td style="width: <?= $this->image->ID ? '50' : '100' ?>%; padding: 0">

								<div class="SubContainer">		
								
									<input type="hidden" name="fileok" id="fileok" value="0" />
									<?if( $this->image->ID ){?>
										<input type="hidden" name="imageID" id="imageID" value="<?= $this->image->ID; ?>" />
										<input type="hidden" name="fileFolder" id="fileFolder" value="<?= $this->image->ImageFolder; ?>" />
									<?}?>
					
									<?if( !$this->image->ID ){?>
									<input type="hidden" name="fileFolder" id="fileFolder" value="<?= $this->folderID; ?>" />
									<?}?>
								
									<label for="fileTitle"><?= i18n ( 'i18n_name' ) ?>:</label>
									<input id="fileTitle" type="text" name="fileTitle" class="w300" value="<?= $this->image->Title; ?>"/>
									<label for="fileTags"><?= i18n ( 'i18n_tags' ) ?>:</label>
									<input id="fileTags" type="text" name="fileTags" class="w300" value="<?= $this->image->Tags; ?>"/>
								
									<div class="SpacerSmall"></div>
									<label for="uploadFile"><?= i18n ( 'i18n_imagefile' ) ?>:</label>
									<input id="uploadFile" type="file" name="uploadFile" onchange="checkImageUpload()" />
						
									<div id="uploadInfoBox"></div>
									<div class="SpacerSmall"></div>
									<label for="fileDesc"><?= i18n ( 'i18n_description' ) ?>:</label>
									<textarea id="fileDesc" name="fileDesc" class="w300" rows="5" style="width:300px; -moz-box-sizing: border-box; box-sizing: border-box;"><?= $this->image->Description; ?></textarea>
								</div>
							
								<?if( $this->image->ID ){?>
									<div class="SpacerSmall"></div>
									<div class="SubContainer">
										<table class="Layout">
											<tr>
												<td><label><?= i18n ( 'i18n_filename' ) ?>:</label></td>
												<td><input type="text" value="<?= $this->image->Filename?>" size="44" name="ImageFilename"/><br </></td>
											</tr>
											<tr>
												<td><label><?= i18n ( 'i18n_filesize' ) ?>:</label></td>
												<td><?= filesizeToHuman( $this->image->Filesize ); ?><br /></td>
											</tr>
											<tr>
												<td><label><?= i18n ( 'i18n_dimensions' ) ?>:</label></td>
												<td><?= $this->image->Width; ?>x<?= $this->image->Height; ?> px</td>
											</tr>
											<tr>
												<td><label><?= i18n ( 'i18n_show_image' ) ?>:</label></td>
												<td><a href="<?= $this->image->getMasterImage(); ?>" target="_blank"><?= $this->image->Filename?></a></td>
											</tr>
											<?
												$fn = BASE_DIR . '/' . $this->image->getFolderPath () . '/' . $this->image->BackupFilename;
												$lfn = BASE_URL . $this->image->getFolderPath () . '/' . $this->image->BackupFilename;
												if ( file_exists ( $fn ) && trim ( $this->image->BackupFilename ) )
												{
													$info = stat ( $fn );
													$date = date ( 'd/m/Y H:i', $info[ 'mtime' ] );
													return '
											<tr>
												<th>' . i18n ( 'i18n_backup' ) . ':</th>
												<td>
													<a href="' . $lfn . '">' . $this->image->BackupFilename . '</a> (' . $date . ')</a>
												</td>
											</tr>';
												}
											?>
										</table>
									</div>
								<?}?>							
							</td>
							<td style="width: 50%; padding: 0; padding-left: 2px;">
								<?if( $this->image->ID ){?>
								<h2 class="BlockHead" style="margin-top: 0"><?= i18n ( 'i18n_preview_image' ) ?></h2>
								<div class="BlockContainer" style="height: 207px; text-align: center; vertical-align: center; overflow: hidden">
									<div style="display: block;">
										<?= $this->imageHTML ?>
									</div>
								</div>
								<div class="SpacerSmallColored"></div>
								<div class="Container">
									<table class="Layout">
										<tr>
											<td style="vertical-align: middle">
												<strong><?= i18n ( 'i18n_showfrom_setting' ) ?>:</strong>
											</td>
											<td style="vertical-align: middle">
												<?= dateToPulldowns ( 'DateFrom', $this->image->DateFrom ) ?>
											</td>
										</tr>
										<tr>
											<td style="vertical-align: middle">
												<strong><?= i18n ( 'i18n_showto_setting' ) ?>:</strong>
											</td>
											<td style="vertical-align: middle">
												<?= dateToPulldowns ( 'DateTo', $this->image->DateTo ) ?>
											</td>
										</tr>
									</table>
								</div>
								<!--
								<button type="button" onclick="initializeImageSlice(<?= $this->image->ID ?>)">
									<img src="admin/gfx/icons/application_view_gallery.png"/> Lag bildeutsnitt
								</button>
								-->
								<?}?>
							</td>
						</tr>
					</table>
				</form>
				<div class="SpacerSmallColored"></div>
				<?if ( $this->edit ) { ?>
				<button onclick="submitImageUpload()">
					<img src="admin/gfx/icons/disk.png" /> <?= i18n ( 'i18n_save' ) ?>
				</button>
				<button onclick="submitImageUpload('close')">
					<img src="admin/gfx/icons/accept.png" /> <?= i18n ( 'i18n_save_and_close' ) ?>
				</button>
				<?}?>
				<?if( $this->image->ID && $this->edit ){?>
				<button onclick="deleteLibraryImage('<?= $this->image->ID; ?>');">
					<img src="admin/gfx/icons/image_delete.png" /> <?= i18n ( 'i18n_delete' ) ?>
				</button>
				<?}?>
				<button onclick="removeModalDialogue ( 'EditLevel' );"><img src="admin/gfx/icons/cancel.png" /> <?= i18n ( 'i18n_close' ) ?></button>
			<?if( !$this->image->ID ){?>
			</div>
			<div class="page" id="pageMultiple">
				<form method="post" enctype="multipart/form-data" action="admin.php?module=library&action=saveimages" target="LibraryUpload">
					<input type="hidden" name="fileFolder" id="fileFolder" value="<?= $this->folderID; ?>" />
					<div id="multipleImages">
					</div>
					<p>
						<button type="button" onclick="mulIncreaseImages ( )">
							<img src="admin/gfx/icons/add.png"/> <?= i18n ( 'i18n_more_images' ) ?>
						</button>
						<button type="button" onclick="mulDecreaseImages ( )">
							<img src="admin/gfx/icons/delete.png"/> <?= i18n ( 'i18n_fewer_images' ) ?>
						</button>
					</p>
					<p>
						<button type="button" onclick="submitImageUpload()">
							<img src="admin/gfx/icons/disk.png" /> <?= i18n ( 'i18n_save' ) ?>
						</button>
						<button type="button" onclick="submitImageUpload('close')">
							<img src="admin/gfx/icons/accept.png" /> <?= i18n ( 'i18n_save_and_close' ) ?>
						</button>
						<button type="button" onclick="removeModalDialogue ( 'EditLevel' );">
							<img src="admin/gfx/icons/cancel.png" /> <?= i18n ( 'i18n_close' ) ?>
						</button>
					</p>
				</form>
			</div>
			<?}?>
		</div>
	</div>
	<script>
		<?if( !$this->image->ID ){?>
		initTabSystem ( 'uploadTabs' );
		<?}?>
		function mulImages ( num )
		{
			var ostr = '';
			for ( var a = 0; a < num; a++ )
			{
				ostr += '<tr class="sw'+(a%2+1)+'"><td><?= i18n ( 'i18n_imagetitle' ) ?> ' + ( a + 1 ) + ': ';
				ostr += '<input type="text" size="20" name="filename_' + a + '"/>';
				ostr += '</td><td>';
				ostr += '<?= i18n ( 'i18n_imagefile' ) ?> ' + ( a + 1 ) + ': ';
				ostr += '<input type="file" name="image_' + a + '"/>';
				ostr += '</td></tr>';
			}
			if ( document.getElementById ( 'multipleImages' ) )
				document.getElementById ( 'multipleImages' ).innerHTML = '<table id="MultipleFilesTable" class="List">' + ostr + '</table>';
		}
		<?if ( !$this->file->ID ) { ?>
		mulImages ( 2 );
		<?}?>
		if ( document.getElementById ( 'tabRediger' ) )
			document.getElementById ( 'tabRediger' ).onclick ( );
	</script>

