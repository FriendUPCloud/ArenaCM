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
<script language="JavaScript" type="text/javascript" src="admin/modules/library/javascript/main.js"></script>
<script language="JavaScript" type="text/javascript" src="admin/modules/library/javascript/imagefunctions.js"></script>
<script language="JavaScript" type="text/javascript" src="admin/modules/library/javascript/filefunction.js"></script>

<script language="JavaScript" type="text/javascript">
	var currentLibraryLevel = '<?= $GLOBALS[ 'Session' ]->LibraryCurrentLevel; ?>';
	document.lid = currentLibraryLevel;
	var Language = new Object ( );
</script>
<?= enableTextEditor ( ) ?>
<script type="text/javascript" src="lib/plugins/library/javascript/plugin.js"></script>
<link rel="stylesheet" href="admin/modules/library/css/main.css" />
<div class="ModuleContainer">
	<table class="LayoutColumns">
		<tr style="height: 0px">
			<th style="width: 240px; padding-right: <?= MarginSize ?>px" id="ColumnLeftTh"></th>
			<th style="width: 4px"></th>
			<th style="padding-right: <?= MarginSize ?>px; padding-left: <?= MarginSize ?>px" id="ColumnMiddleTh"></th>
		</tr>
		<tr>
			<td style="padding: 0">
				<h1 style="margin: 0 0 4px 0">
					<img src="admin/gfx/icons/folder.png" style="float: left; margin: 0pt 4px 0pt 0pt;"/> <?= i18n ( 'Folders' ) ?>
				</h1>
			</td>
			<td style="padding: 0">&nbsp;</td>
			<td style="padding: 0">
				<h1 style="margin: 0 0 4px 0" id="Header">
					<span class="HeaderBox" id="ContentButtonsSmall">
					</span>
					<div class="HeaderBox">
						<label><?= i18n ( 'Show' ) ?>:</label>
						<select onchange="document.location = 'admin.php?module=library&viewmode=' + this.value">
							<option value="thumbnails"<?= $this->viewmode == 'thumbnails' ? 'selected="selected"' : '' ?>><?= i18n ( 'Thumbnails' ) ?></option>
							<option value="details"<?= $this->viewmode == 'details' ? 'selected="selected"' : '' ?>><?= i18n ( 'Detailed list' ) ?></option>
						</select>
						<label><?= i18n ( 'Listed by' ) ?>:</label> 
						<select onchange="document.location='admin.php?module=library&listmode=' + this.value">
							<option value="date"<?= $this->listmode == 'date' ? ' selected="selected"' : '' ?>><?= i18n ( 'Date' ) ?></option>
							<option value="filename"<?= $this->listmode == 'filename' ? ' selected="selected"' : '' ?>><?= i18n ( 'Filename' ) ?></option>
							<option value="title"<?= $this->listmode == 'title' ? ' selected="selected"' : '' ?>><?= i18n ( 'File title' ) ?></option>
							<option value="filesize"<?= $this->listmode == 'filesize' ? ' selected="selected"' : '' ?>><?= i18n ( 'Filsize' ) ?></option>
						</select>
					</div>
					<img src="admin/gfx/icons/server.png" alt="server" style="vertical-align: bottom"/> 
					<strong id="Innholdsheader"><?= $_REQUEST[ 'tag' ] ? ( i18n ( 'Files and images matching' ) . ' "' . $_REQUEST[ 'tag' ] . '"' ) : ( i18n ( 'The contents of' ) . ' "' . $this->folder->Name . '"' ) ?>:</strong>
				</h1>
			</td>
		</tr>
		<tr>
			<td style="padding: 1px; display: table-cell" class="Container">
				<div id="ChoicesTabs">
					<div style="padding: 0px">
						<div id="levels" style="margin: -1px">
							<div>
								<ul id="LibraryLevelTree" class="Collapsable">
									<?= $this->levels; ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</td>
			<td style="padding: 0">&nbsp;</td>
			<td style="padding: 1px 3px; display: table-cell" id="libMainCol" class="Container">
				<div>
					<div class="SpacerSmall"></div>
					<div id="LibraryMessage"></div>
					<div id="LibraryContentDiv">
						<?= $this->content ?>
					</div>
				</div>
				<iframe name="LibraryUpload" style="position: absolute; width: 1px; height: 1px; visibility: hidden"></iframe>
				
			</td>
		</tr>
		<tr>
			<td style="padding: 2px 0 0 0">
				<div class="SpacerSmallColored"></div>
				<?if ( $this->tags ) { ?>
				<h2 class="BlockHead">
					<img src="admin/gfx/icons/tag_green.png" style="float: left; margin: 0pt 4px 0pt 0pt;"/>
					<?= i18n ( 'Tags' ) ?>
				</h2>
				<div class="BlockContainer" id="TagList">
					<?= $this->tags ?>
				</div>
				<?}?>
				<h2 class="BlockHead">
					<img src="admin/gfx/icons/magnifier.png" style="float: left; margin: 0pt 4px 0pt 0pt;"/> 
					<?= i18n ( 'Search the library' ) ?>
				</h2>
				<div class="BlockContainer">
					<form id="librarySearch" onsubmit="ModuleLibrarySearch ( ); return false">
						<p>
							<input type="text" style="width: 95%" name="libSearchKeywords" id="libSearchKeywords" value="<?= $_REQUEST[ 'libSearchKeywords' ] ? $_REQUEST[ 'libSearchKeywords' ] : i18n ( 'keywords...' ) ?>" onmouseup="this.select()">
						</p>
					</form>
					<div class="Spacer"></div>
					<button type="button" onclick="ModuleLibrarySearch()">
						<img src="admin/gfx/icons/magnifier.png"> <?= i18n ( 'Search' ) ?>
					</button>
					<button type="button" onclick="ModuleResetLibrarySearch()" id="libNullStillSoek" style="position: absolute; visibility: hidden">
						<img src="admin/gfx/icons/cancel.png"> <?= i18n ( 'Reset search' ) ?>
					</button>
				</div>
				<div id="searchResults"></div>
				<div class="SpacerSmall"></div>
			</td>
			<td style="padding: 2px 0 0 0">&nbsp;</td>
			<td style="padding: 2px 0 0 0">
				<div class="SpacerSmallColored"></div>
				<div id="ContentButtons">
				</div>
				<div class="SpacerSmall"></div>
				<div>
					<button type="button" onclick="document.location='admin.php?module=library&action=checkuploadfolder';">
						<img src="admin/gfx/icons/folder_wrench.png"> <?= i18n ( 'Check for missing files' ) ?>
					</button>
					<button type="button" onclick="cleanCache()">
						<img src="admin/gfx/icons/folder_wrench.png"> <?= i18n ( 'Empty temporary files' ) ?>
					</button>
				</div>
			</td>
		</tr>
		
	</table>
	<script language="JavaScript" type="text/javascript">
		setLibraryPos ( <?= ( $_REQUEST[ 'pos' ] >= 0 ? $_REQUEST[ 'pos' ] : '0' ) ?> );
		makeCollapsable ( document.getElementById ( 'LibraryLevelTree' ) );
		setLibraryLevel (); // runs some extra checks
		showContentButtons ( );
		checkLibraryTooltips();
	</script>
	<? 
		// Translations ----------------------------------------------------------
		i18n ( 'Are you sure you want to clear the image cache?' ); 
		i18n ( 'All done!' ); 
		i18n ( 'i18n_search_results_contents' );
		i18n ( 'i18n_search_results' );
		i18n ( 'i18n_contents_of' );
		i18n ( 'i18n_contents_of_main' );
		i18n ( 'i18n_no_files_in_folder' );
		i18n ( 'i18n_edit' );
		i18n ( 'i18n_edit_msg' );
		i18n ( 'i18n_imagefile' );
		i18n ( 'i18n_imagetitle' );
		i18n ( 'i18n_confirm_image_delete' );
		i18n ( 'i18n_no_valid_image_selected' );
		i18n ( 'i18n_delete_image' );
		i18n ( 'i18n_delete' );
		i18n ( 'i18n_delete_msg' );
		i18n ( 'i18n_cancel' );
		i18n ( 'i18n_are_you_sure' );
		i18n ( 'i18n_searchresult_folders' );
		i18n ( 'i18n_no_searchresult_folders' );
		i18n ( 'i18n_no_searchresult_files' );
		i18n ( 'i18n_move' );
		i18n ( 'i18n_move_msg' );
		i18n ( 'i18n_addtoworkbench' );
		i18n ( 'i18n_addtoworkbench_msg' );
		i18n ( 'i18n_move_error_msg1' );
		i18n ( 'i18n_delete_file' );
		i18n ( 'i18n_sure_delete_file_question' );
		i18n ( 'i18n_no_valid_file_selection' );
		i18n ( 'i18n_longer_fileselection_error' );
		i18n ( 'i18n_fullscreen' );
		i18n ( 'i18n_permissions' );
		i18n ( 'i18n_permissions_desc' );
		i18n ( 'i18n_addlevel' );
		i18n ( 'i18n_addlevel_desc' );
	?>
  
</div>
