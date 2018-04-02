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
	<div id="PublishQueueWindow">
		<h1>
			Publiseringskø for: <?= $this->content->MenuTitle ?>
		</h1>
		<div class="Container">
			<p>
				Noen tilknyttede elementer er ikke publiserte. Hvis du ønsker å publisere disse
				nå, merker du dem av i listen under. Når du er ferdig kan du klikke på "Publiser"
				knappen for å publisere siden.
			</p>
			<div id="PublishList">
				<?= $this->list ?>
			</div>
		</div>
		<div class="SpacerSmallColored"></div>
		<button type="button" onclick="removeModalDialogue ( 'publishqueue' )">
			<img src="admin/gfx/icons/cancel.png"> Lukk
		</button>
		<button type="button" onclick="publishPageElements ( )">
			<img src="admin/gfx/icons/page_go.png"> Publiser
		</button>
	</div>
	<script>
		resizeModalDialogue ( 'publishqueue', false, getElementHeight ( document.getElementById ( 'PublishQueueWindow' ) ) + 24 );
	</script>
