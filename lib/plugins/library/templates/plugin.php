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
				<?if ( $this->ShowHeader ) { ?>
				<h1>
					<div class="HeaderBox">
						<a href="javascript:;" onclick="getHelp ( 'no/funksjonsbokser/biblotek/index.html' )"><img src="admin/gfx/icons/help.png" style="border: 0" /></a>
					</div>
					Bibliotek - innhold og media 
				</h1>
				<?}?>
				<div id="PluginLibrary"><div class="Container">Laster inn...</div></div>
				<link rel="stylesheet" href="<?= BASE_URL ?>lib/plugins/library/css/plugin.css" />
				<script type="text/javascript" src="<?= BASE_URL ?>lib/plugins/library/javascript/plugin.js"></script>
				<script type="text/javascript"> initPluginLibrary ( '<?= $this->ContentType ?>', '<?= $this->ContentID ?>', '<?= $this->Mode ?>' ); </script>
