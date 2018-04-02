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
	<iframe name="LibraryUpload" style="position: absolute; width: 1px; height: 1px; visibility: hidden"></iframe>
	<div id="UploadTabs" style="width: 100%">
		<div class="tab" id="tabLibrary">
			<img src="admin/gfx/icons/help.png" title="Hjelp">
		</div>
		<div class="tab" id="tabUploadImage">
			<img src="admin/gfx/icons/image_add.png" title="Last opp bilde"> Bilde
		</div>
		<div class="tab" id="tabUploadFile">
			<img src="admin/gfx/icons/page_add.png" title="Last opp fil"> Fil
		</div>
		<div class="tab" id="tabLibraryContent">
			<img src="admin/gfx/icons/folder_find.png" title="Se i arkivet"> Arkiv
		</div>
		<div class="page" id="pageLibrary">
			<h2>Biblioteket:</h2>
			<p>
				Vedlikehold og bruk biblioteket med dette verktøyet. Knappene på bunnen av dette panelet fører deg til 
				seksjonene som vist under:
			</p>
			<p>
				<img src="admin/gfx/icons/image_add.png" style="cursor: pointer; cursor: hand; vertical-align: bottom" onclick="activateTab ( 'tabUploadImage', 'UploadTabs', 'tabs' );" /> Last opp bilde
			</p>
			<p>
				<img src="admin/gfx/icons/page_add.png" style="cursor: pointer; cursor: hand; vertical-align: bottom" onclick="activateTab ( 'tabUploadFile', 'UploadTabs', 'tabs' );" /> Last opp en fil
			</p>
			<p>
				<img src="admin/gfx/icons/folder_find.png" style="cursor: pointer; cursor: hand; vertical-align: bottom" onclick="activateTab ( 'tabLibraryContent', 'UploadTabs', 'tabs' );" /> Ordne mapper og søk
			</p>
		</div>
		<div class="page" id="pageUploadImage">
			<p>
				<strong>Last opp bilde:</strong>
			</p>
			<p>
				Velg et bilde fra disk og last opp i arkivet. Velg en mappe via rullegardinen for å laste opp
				bildet i den mappen.
			</p>
			<form method="post" enctype="multipart/form-data" target="LibraryUpload" action="admin.php?plugin=library&amp;pluginaction=uploadimage">
				<div class="SubContainer">
					<strong>
						Bildetittel:
					</strong>
					<div class="Spacer"></div>
					<input type="text" size="20" name="Title" value="" />
					<div class="Spacer"></div>
					<strong>
						Mappe:
					</strong>
					<div class="Spacer"></div>
					<span>
						<select name="Level" id="ImageLevels" onchange="setLibraryLevel ( this.value )">
							<?= $this->FileLevels ?>
						</select>
					</span>
					<div class="Spacer"></div>
					<input type="file" name="ImageStream" />
					<div class="Spacer"></div>
					<button type="submit">
						<img src="admin/gfx/icons/monitor_go.png" /> Last opp
					</button>
				</div>
			</form>
		</div>
		<div class="page" id="pageUploadFile">
			<p>
				<strong>Last opp en fil:</strong>
			</p>
			<p>
				Last opp filer fra disk og inn i arkivet. Velg et mappe via rullegardinen for å laste opp
				bildet i den mappen.
			</p>
			<form method="post" enctype="multipart/form-data" target="LibraryUpload" action="admin.php?plugin=library&amp;pluginaction=uploadfile">
				<div class="SubContainer">
					<strong>
						Filtittel:
					</strong>
					<div class="Spacer"></div>
					<input type="text" size="20" name="Title" value="" />
					<div class="Spacer"></div>
					<strong>
						Mappe:
					</strong>
					<div class="Spacer"></div>
					<span>
						<select name="Level" id="FileLevels" onchange="setLibraryLevel ( this.value )">
							<?= $this->FileLevels ?>
						</select>
					</span>
					<div class="Spacer"></div>
					<input type="file" name="FileStream" />
					<div class="Spacer"></div>
					<button type="submit">
						<img src="admin/gfx/icons/page_go.png" /> Last opp
					</button>
				</div>
			</form>
		</div>
		<div class="page" id="pageLibraryContent" style="padding: 2px">
			
			<div class="SubContainer">
				<form method="post" action="" onsubmit="librarySearch ( ); return false">
					<strong>Søk:</strong>&nbsp;
					<input type="text" size="20" value="" id="LibraryKeywords" style="width: 50%; background: #fff url('admin/gfx/icons/page_white.png') 3px 3px no-repeat; padding-left: 22px;" />
					<button onclick="librarySearch ( )" class="Small" title="Søk i biblioteket" type="button">
						<img src="admin/gfx/icons/magnifier.png" />
					</button>
					<button onclick="pluginLibraryShowContent ( ); document.getElementById ( 'LibraryKeywords' ).value = ''" class="Small" title="Nullstill søk" type="button">
						<img src="admin/gfx/icons/cancel.png" />
					</button>
				</form>
			</div>
			<div class="SpacerSmall"></div>
			<div class="SubContainer" style="padding: 0">
				<h2 class="PluginLibrary">Mapper:</h2>
				<div class="SpacerSmall"></div>
				<ul id="ContentLevelTree" class="Collapsable">
					<?= $this->FileLevelTree ?>
				</ul>
				<script language="JavaScript" type="text/javascript">
					makeCollapsable ( document.getElementById ( 'ContentLevelTree' ) );
				</script>
			</div>
			<div id="libraryNewLevelBox">
			</div>
			<div class="SpacerSmall"></div>
			<div class="SubContainer" style="padding: 2px">
				<div class="SubContainer" id="LibraryContent">
					<div class="Info">
						<small>Intet innhold er tilgjengelig.</small>
					</div>
				</div>
			</div>
			<div class="SpacerSmall"></div>
		</div>
	</div>
	
