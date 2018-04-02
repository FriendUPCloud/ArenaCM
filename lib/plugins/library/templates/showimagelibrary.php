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
<h1>
	<div class="HeaderBox">
		<button type="button" onclick="removeModalDialogue ( 'library' )" title="Lukk vinduet">
			<img src="admin/gfx/icons/cancel.png" />
		</button>
	</div>
	Velg mappe
</h1>
<div id="juba" class="SubContainer">
	<h2 class="PluginLibrary">Mapper:</h2>
	<div class="SpacerSmall"></div>
	<?= generatePluginFolderstructure ( $GLOBALS[ 'Session' ]->pluginLibraryLevelID ); ?>
</div>
<div class="SpacerSmall"></div>
<h1>
	Velg bilde
</h1>
<div class="SubContainer" id="DiaLibraryImages" style="height: 287px; overflow: auto">
	Laster inn...
</div>
<div class="SpacerSmallColored"></div>
<div class="SubContainer" style="padding: <?= MarginSize ?>px">
	<iframe style="visibility: hidden; position: absolute; top: -1000px; left: -1000px" name="upfr"></iframe>
	<form name="upfrm" action="admin.php?plugin=library&pluginaction=uploadimage" method="post" enctype="multipart/form-data" target="upfr">
	<strong>Last opp bilde:</strong>&nbsp;<input type="file" name="ImageStream"> <button type="submit"><img src="admin/gfx/icons/attach.png"> Last opp</button>
	</form>
</div>
<div class="SpacerSmall"></div>
<button type="button" onclick="removeModalDialogue ( 'library' )">
	<img src="admin/gfx/icons/cancel.png" /> Lukk vinduet
</button>
