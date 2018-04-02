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
	<?if( $this->folder ){?>
		<h1>Slett nivå "<?= $this->folder->Name; ?>"</h1>
		<div class="Container">
			
				
				<h2><?= i18n ( 'i18n_sure_to_delete_folder' ) ?></h2>
				
				<br />
				
				<form id="deleteLevelForm">
						
					<span>Hvis du skal slette nivået, så vil undernivåer, filer og bilder bli anfektet. Hva ønsker du å gjøre med dette innholdet?</span><br />
					<input type="hidden" name="libraryMoveContent" id="libraryMoveContent" value="delete" />
					<br/>
					
					<table cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td style="padding-right: <?= MarginSize ?>px">
								<label for="idldl">1. Slett innhold</label>
							</td>
							<td>
								<input id="idldl" onclick="document.getElementById( 'libraryMoveContent' ).value = 'delete'" type="radio" name="movecontent" value="delete" checked="checked"/>
							</td>
						</tr>
						<tr>
							<td style="padding-right: <?= MarginSize ?>px">
								<label for="iddlm">2. Flytt innhold til valgt mappe under </label>
							</td>
							<td>
								<input id="iddlm" onclick="document.getElementById( 'libraryMoveContent' ).value = 'move'" type="radio" name="movecontent" value="move" />
							</td>
						</tr>
					</table>
					<br/>
						
					<div class="SpacerSmall"></div>
					
					<p>
						<strong>Velg mappe ved valg 2:</strong>
					</p>
					
					<select name="newcontentfolder" size="1" class="w200" onchange="document.getElementById( 'iddlm' ).checked = true">
						<?= $this->otherfolders ?>
					</select>		
					
					<div class="SpacerSmall"></div>
				
				</form>
			
		</div>
		
		<div class="SpacerSmall"></div>
		
		<div class="Container">
				
				
				<button type="button" onclick="doDeleteLibraryLevelEdit( <?= $this->folder->ID; ?> )">
					<img src="admin/gfx/icons/folder_delete.png" /> Slett
				</button>
				<button type="button" onclick="abortLibraryLevelEdit( <?= $this->folder->ID; ?> )">
					<img src="admin/gfx/icons/cancel.png" /> Abort
				</button>
				
		</div>
	<?}?>
	<?if( !$this->folder ){?>
		<i><?= i18n ( 'i18n_folder_not_found' ) ?></i>
	<?}?>
