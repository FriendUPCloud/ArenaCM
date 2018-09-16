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

	<div id="editLevelContainer">
		<?if ( $this->writePermission ) { ?>
		<iframe name="hiddenlevel" class="Hidden"></iframe>
		<form id="editLevelForm" target="hiddenlevel" method="post" action="admin.php?module=library&function=editlevel&action=savelevel">
		<?}?>
		<h1>
			<div class="HeaderBox">
				<?if ( $this->writePermission ) { ?>
				<button type="submit">
					<img src="admin/gfx/icons/disk.png" /> 
				</button>
				<button type="button" onclick="ge('editLevelForm').close.value='1'; ge('editLevelForm').submit();" title="Lagre og lukk">
					<img src="admin/gfx/icons/accept.png" />
				</button>
				<?}?>
				<button type="button" onclick="editor.removeControl ( 'folderDescription' ); removeModalDialogue ( 'EditLevel' ); getLibraryLevelTree ( );">
					<img src="admin/gfx/icons/cancel.png" />
				</button>
			</div>
			<?= $this->folder->ID ? ( 'Rediger mappe "' .  $this->folder->Name . '"' ) : 
				( 'Ny mappe' . ( $this->pfolder->Name ? " i '{$this->pfolder->Name}'" : '' ) ) ?>
		</h1>		
		<div class="Container">
	
				<input type="hidden" value="<?= $this->folder->ID ?>" name="ID">
			
				<table class="Gui">
					<tr>
						<td>
							<label for="folderName">Navn:</label>
							<input id="folderName" class="w300" type="text" name="folderName" value="<?= $this->folder->Name; ?>" maxlength="200"/>
							<script type="text/javascript">
								ge('folderName').focus ();
							</script>
						</td>
						<td>
							<label for="folderPosition">Plassering:</label>
							<select id="folderPosition" name="Parent">
								<?
									function fhierarchy ( $current, $parent = '0', $r = '' )
									{
										$db =& dbObject::globalValue ( 'database' );
										if ( $rows = $db->fetchObjectRows ( 'SELECT * FROM Folder WHERE Parent=\\'' . (string)$parent . '\\'' ) )
										{
											foreach ( $rows as $row )
											{
												$c = $row->ID == $current ? ' selected="selected"' : '';
												if ( !isset($_REQUEST['lid']) || $row->ID != $_REQUEST['lid'] ) 
												{
													$str .= '<option value="' . $row->ID . '"' . $c . '>' . $r . stripslashes ( $row->Name ) . '</option>';
													$str .= fhierarchy ( $current, $row->ID, $r . '&nbsp;&nbsp;&nbsp;&nbsp;' );
												}
											}
											return $str;
										}
									}
									return fhierarchy ( $this->folder->Parent ? $this->folder->Parent : $this->pfolder->ID );
								?>
							</select>
						</td>
						<td>
							<label for="folderSortOrder">Sorteringsorden:</label>
							<input type="text" size="3" name="folderSortOrder" style="text-align: center; width: 50px" value="<?= $this->folder->SortOrder ?>"/>
						</td>
					</tr>
				</table>
		
				<div class="SpacerSmall"></div>
		
		
				<label for="folderDescription">Beskrivelse</label><br />
				<textarea id="folderDescription" class="mceSelector" name="folderDescription" rows="10" style="height: 300px" cols="30"><?= $this->folder->Description; ?></textarea>
	

				<div class="Spacer"></div>

				<?if ( $this->writePermission ) { ?>
				<button type="submit">
					<img src="admin/gfx/icons/disk.png" /> Lagre
				</button>
				<button type="button" onclick="ge('editLevelForm').close.value='1'; ge('editLevelForm').submit();">
					<img src="admin/gfx/icons/accept.png" /> Lagre og lukk
				</button>
				<?}?>
				<button type="button" onclick="editor.removeControl ( 'folderDescription' ); removeModalDialogue ( 'EditLevel' ); getLibraryLevelTree ( );">
					<img src="admin/gfx/icons/cancel.png" /> Lukk
				</button>
		
		</div>
		<div style="clear:both"><em></em></div>
		<input type="hidden" name="close" value="0"/>
		<?if ( $this->writePermission ) { ?>
		</form>
		<?}?>

	</div>
	
