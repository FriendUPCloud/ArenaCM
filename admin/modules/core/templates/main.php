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
			<div class="ModuleContainer">
			
				<div class="tabs" id="SettingsTabs">				
			
					<div class="tab" id="tabSettings">
						<img src="admin/gfx/icons/user.png" /> Admin Core Innstillinger
					</div>
				
					<div class="tab" id="tabSites">
						<img src="admin/gfx/icons/sitemap.png" /> ARENA2 Nettsider
					</div>
					
					<div class="tab" id="tabTools">
						<img src="admin/gfx/icons/wrench.png"/> <?= i18n ( 'Tools' ) ?>
					</div>
				
					<div class="page" id="pageSettings">
						<?= $this->pageSettings ?>
					</div>
				
					<div class="page" id="pageSites">
						<h1>
							ARENA2 nettsider i databasen
						</h1>
						<div class="Container">
							<div class="SubContainer" style="padding: 0">
								<table style="border-collapse: collapse; border-spacing: 0px; width: 100%">
									<?= $this->Sites ?>
								</table>
							</div>
						</div>
						<div class="SpacerSmall"></div>
						<button type="button"  onclick="initModalDialogue ( 'site', 500, 500, 'admin.php?module=core&function=site' )">
							<img src="admin/gfx/icons/world_go.png" /> Legg til en ny ARENA2 nettside
						</button>
					</div>
					
					<div class="page" id="pageTools">
						<?
							include_once ( 'lib/classes/template/ctemplate.php' );
							$t = new cTemplate ( 'admin/modules/core/templates/page_tools.php' );
							return $t->render ();
						?>
					</div>
				
				</div>
				
			</div>
			
			<script type="text/javascript" src="admin/modules/core/javascript/core.js"></script>
			
			<script type="text/javascript">
				initTabSystem ( 'SettingsTabs' );
			</script>
