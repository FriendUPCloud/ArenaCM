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
	<div id="FileEditContainer">
		<?if( $this->file->ID ){?>
		<h1><div class="HeaderBox"><button onclick="removeModalDialogue ( 'EditLevel' );"><img src="admin/gfx/icons/cancel.png" /></button></div><?= i18n ( 'i18n_edit_file_head' ) ?></h1>
		<?}?>
		<?if( !$this->file->ID ){?>
		<h1><div style class="HeaderBox"><button onclick="removeModalDialogue ( 'EditLevel' );"><img src="admin/gfx/icons/cancel.png" /></button></div><?= i18n ( 'i18n_add_file_head' ) ?></h1>
		<?}?>
		<div id="uploadFileTabs">
			<iframe style="visibility: hidden; width: 1px; height: 1px; position: absolute" name="FileUpload"></iframe>

			<div class="tab" id="tabRediger">
				<img src="admin/gfx/icons/page_white.png"/>
				<?if( $this->file->ID ){?>
				<?= i18n ( 'i18n_edit_file' ) ?>
				<?}?>
				<?if( !$this->file->ID ){?>
				<?= i18n ( 'i18n_new_file' ) ?>
				<?}?>
			</div>
			<?if ( $this->file->ID ) { ?>
			<div class="tab" id="tabProperties">
				<img src="admin/gfx/icons/page_gear.png"/> <?= i18n ( 'i18n_advanced' ) ?>
			</div>
			<?}?>
			<?if ( !$this->file->ID ) { ?>
			<div class="tab" id="tabMultiple">
				<img src="admin/gfx/icons/page_go.png"/> <?= i18n ( 'i18n_batch_upload' ) ?>
			</div>
			<?}?>
			<div class="page" id="pageRediger">
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td>
							<form method="post" enctype="multipart/form-data" id="uploadForm" name="uploadForm" action="admin.php?module=library&action=savefile" target="FileUpload">
								<div class="SubContainer">
									<input type="hidden" name="fileok" id="fileok" value="0" />
									<?if( $this->file->ID ){?>
										<input type="hidden" name="fileID" id="fileID" value="<?= $this->file->ID; ?>" />
										<input type="hidden" name="fileFolder" id="fileFolder" value="<?= $this->file->FileFolder; ?>" />
									<?}?>
	
									<?if( !$this->file->ID ){?>
										<input type="hidden" name="fileFolder" id="fileFolder" value="<?= $this->folderID; ?>" />
									<?}?>			
							
									<label for="fileTitle"><?= i18n ( 'i18n_name' ) ?>:</label>
									<input id="fileTitle" type="text" name="fileTitle" class="w300" value="<?= $this->file->Title; ?>"/>
							
									<div class="SpacerSmall"></div>
									<label for="uploadFile"><?= i18n ( 'i18n_file' ) ?>:</label>
									<input id="uploadFile" type="file" name="uploadFile" onchange="checkFileUpload()" />
					
									<div id="uploadInfoBox"></div>
									<div class="SpacerSmall"></div>
									<label for="fileDesc"><?= i18n ( 'i18n_description' ) ?>:</label>
									<textarea id="fileDesc" name="fileDesc" class="w300" rows="5" style="width: 300px; -moz-box-sizing: border-box; box-sizing: border-box;"><?= $this->file->Description; ?></textarea>
								</div>
								<?if( $this->file->ID ){?>
								<div class="SpacerSmall"></div>
								<div class="SubContainer">
									<table class="Layout">
										<tr>
											<td><label><?= i18n ( 'i18n_filename' ) ?>:</label></td>
											<td><input type="text" value="<?= $this->file->Filename?>" size="44" name="FileFilename"/><br </></td>
										</tr>
										<tr>
											<td><label><?= i18n ( 'i18n_filesize' ) ?>:</label></td>
											<td><?= filesizeToHuman( $this->file->Filesize ); ?></td>
										</tr>
										<tr>
											<td><label><?= i18n ( 'i18n_download' ) ?>:</label></td>
											<td><a href="<?= BASE_URL . $this->file->Folder->DiskPath .'/' .$this->file->Filename?>" target="_blank"><?= $this->file->Filename?></a></td>
										</tr>
										<?
											$fn = BASE_DIR . '/' . $this->file->getFolderPath () . '/' . $this->file->BackupFilename;
											$lfn = BASE_URL . $this->file->getFolderPath () . '/' . $this->file->BackupFilename;
											if ( file_exists ( $fn ) && trim ( $this->file->BackupFilename ) )
											{
												$info = stat ( $fn );
												$date = date ( 'd/m/Y H:i', $info[ 'mtime' ] );
												return '
										<tr>
											<th>' . i18n ( 'i18n_backup' ) . ':</th>
											<td>
												<a href="' . $lfn . '">' . $this->file->BackupFilename . '</a> (' . $date . ')</a>
											</td>
										</tr>';
											}
										?>
									</table>
								</div>
								<?}?>
							</form>
						</td>
					</tr>
				</table>
				<div class="SpacerSmall"></div>
				<?if ( $this->edit ) { ?>
				<button onclick="submitFileUpload()"><img src="admin/gfx/icons/page_go.png" /> <?= i18n ( 'i18n_save' ) ?></button>
				<?}?>
				<?if( $this->file->ID && $this->edit ){?>
				<button onclick="deleteLibraryFile('<?= $this->file->ID; ?>');"><img src="admin/gfx/icons/page_delete.png" /> <?= i18n ( 'i18n_delete' ) ?></button>
				<?}?>
				<button onclick="removeModalDialogue ( 'EditLevel' );"><img src="admin/gfx/icons/cancel.png" /> <?= i18n ( 'i18n_close' ) ?></button>
			</div>
			<?if ( $this->file->ID ) { ?>
			<div class="page" id="pageProperties">
				<?
					list ( , $ext ) = explode ( '.', $this->file->Filename );
					switch ( strtolower ( $ext ) )
					{
						case 'swf':
							$properties = new cPTemplate ( 'admin/modules/library/templates/file_properties_swf.php' );
							$properties->file =& $this->file;
							return $properties->render ( );
						case 'css':
						case 'txt':
						case 'locale':
						case 'js':
						case 'html':
							if ( file_exists ( 'upload/' . $this->file->Filename ) )
							{
								$properties = new cPTemplate ( 'admin/modules/library/templates/file_properties_text.php' );
								$properties->file =& $this->file;
								$properties->contents = stripslashes ( file_get_contents ( 'upload/' . $this->file->Filename ) );
								return $properties->render ( );
							}
							return 'Filen finnes ikke på disk.';
						default:
							$properties = new cPTemplate ( 'admin/modules/library/templates/file_properties_generic.php' );
							$properties->file =& $this->file;
							return $properties->render ( );
					}
				?>
			</div>
			<?}?>
			<?if ( !$this->file->ID ) { ?>
			<div class="page" id="pageMultiple">
				<form method="post" enctype="multipart/form-data" action="admin.php?module=library&action=savefiles" target="FileUpload">
					<input type="hidden" name="fileFolder" id="fileFolder" value="<?= $this->folderID; ?>" />
					<div id="multipleFiles">
					</div>
					<p>
						<button type="button" onclick="mulIncreaseFiles ( )">
							<img src="admin/gfx/icons/add.png"/> <?= i18n ( 'i18n_more_files' ) ?>
						</button>
						<button type="button" onclick="mulDecreaseFiles ( )">
							<img src="admin/gfx/icons/delete.png"/> <?= i18n ( 'i18n_fewer_files' ) ?>
						</button>
					</p>
					<p>
						<button type="submit">
							<img src="admin/gfx/icons/monitor_go.png" /> <?= i18n ( 'i18n_save' ) ?>
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
		initTabSystem ( 'uploadFileTabs' );
		<?if ( !$this->file->ID ) { ?>
		function mulFiles ( num )
		{
			var ostr = '';
			for ( var a = 0; a < num; a++ )
			{
				ostr += '<tr class="sw'+(a%2+1)+'"><td>Filtittel ' + ( a + 1 ) + ': ';
				ostr += '<input type="text" size="20" name="filename_' + a + '"/>';
				ostr += '</td><td>';
				ostr += 'Fil ' + ( a + 1 ) + ': ';
				ostr += '<input type="file" name="file_' + a + '"/>';
				ostr += '</td></tr>';
			}
			document.getElementById ( 'multipleFiles' ).innerHTML = '<table id="MultipleFilesTable">' + ostr + '</table>';
		}
		mulFiles ( 2 );
		<?}?>
		if ( document.getElementById ( 'tabRediger' ) )
			document.getElementById ( 'tabRediger' ).onclick ( );
	</script>
	
	
	

