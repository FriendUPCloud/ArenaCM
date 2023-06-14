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
	<table class="LayoutColumns">
		<tr>
			<td style="width: 368px; padding-right: 8px" class="Column">
				<h1>
					<img src="admin/gfx/icons/book_open.png" style="float: left; margin: 0 4px 0 0" /> <?= $this->languages ?>
					<?= i18n ( 'Website overview' ) ?>
				</h1>
				<div id="StructureChangedButton">
				</div>
				<div id="StructureButtonsTop" style="padding: 2px 2px 0 2px">
					<div><?= structureButtons ( $this->page, 'simple' ) ?></div>
				</div>
				<div class="Container" id="StructureContainer">
					<?= $this->structure ?>
					<script>
						makeCollapsable ( document.getElementById ( 'Structure' ) );
					</script>
				</div>
				<div class="SpacerSmall"></div>
				<div id="StructureButtons" class="Container">
					<?= structureButtons ( $this->page ) ?>
				</div>
				<div class="SpacerSmallColored"></div>
				<h2 class="BlockHead"><?= i18n ( 'Your notes on this page' ) ?>:</h2>
				<div class="BlockContainer">
					<textarea id="PageNotes" style="<?= trim ( $this->Notes ) ? 'height: 100px;' : 'height: 35px;' ?>"><?= $this->Notes ?></textarea>
				</div>
				<?= $this->toolExpansions ?>
				<div class="SpacerSmallColored"></div>
				<div id="EditorAdvancedPages" class="TabPages">
					<div id="tabYourInfo" class="tab">
						<img src="admin/gfx/icons/user.png"> <?= i18n ( 'Your info' ) ?>
					</div>
					<div id="tabModuleShop" class="tab">
						<img src="admin/gfx/icons/brick.png"> <?= i18n ( 'Modules' ) ?>
					</div>
					<div id="tabLayout" class="tab">
						<img src="admin/gfx/icons/application_view_icons.png"> <?= i18n ( 'Layout' ) ?>
					</div>	
					<div id="tabTrash" class="tab">
						<img src="admin/gfx/icons/bin.png"> <?= i18n ( 'Trash' ) ?>
					</div>
					<div id="pageYourInfo" class="page">
						<div style="float: right; margin: 0 0 10px 10px">
							<?
								if( $GLOBALS[ 'Session' ]->AdminUser->Image > 0 )
								{
									$i = new dbImage( $GLOBALS[ 'Session' ]->AdminUser->Image );
									return $i->getImageHTML( 128, 128, 'proximity' );
								}
								else
								{
									return '<img src="admin/gfx/arenaicons/user_johndoe_128.png"/>';
								}
							?>
						</div>
						<p><strong><?= $GLOBALS[ 'Session' ]->AdminUser->Name ?></strong></p>
						<p>
							<?
								$u = $GLOBALS[ 'Session' ]->AdminUser;
								if( $u->_dataSource == 'core' )
									return i18n( 'i18n_super_user' ) . '.';
								$now = time();
								$log = strtotime( $u->DateLogin );
								$tim = $now - $log;
								$uhl = i18n( 'You have been logged in for' );
								if( $tim > 3600 )
								{
									return $uhl . ' ' . floor ( $tim / 3600 ) . ' ' . ( floor ( $tim / 3600 ) == 1 ? i18n( 'hour' ) : i18n( 'hours' ) ) . '.';
								}
								else if ( $tim > 60 )
								{
									return $uhl . ' ' . floor ( $tim / 60 ) . ' ' . ( floor ( $tim / 60 ) == 1 ? i18n( 'minute' ) : i18n( 'minutes' ) ) . '.';
								}
								return $uhl . ' ' . ( $now - $log ) . ' ' . i18n( 'seconds' ) .'.';
							?>
						</p>
						<p>
							<button type="button" onclick="document.location='admin.php?logout=1'">
								<img src="admin/gfx/icons/lock_break.png"/> <?= i18n( 'Logout' ) ?>
							</button>
						</p>
						<br style="clear: both"/>
					</div>
					<div id="pageModuleShop" class="page" style="padding: 4px">
						<div id="EditorModuleTabs">
							<div class="tab" id="tabModulesConnected">
								<img src="admin/gfx/icons/folder_brick.png"> <?= i18n ( 'Your modules' ) ?>
							</div>
							<?if ( $GLOBALS[ 'Session' ]->AdminUser->isSuperUser ( ) ) { ?>
							<div class="tab" id="tabModulesAvailable">
								<img src="admin/gfx/icons/bricks.png"> <?= i18n ( 'Get modules' ) ?>
							</div>
							<?= $this->moduleTabExpansions ?>
							<?}?>
							<div class="page" id="pageModulesConnected">
							<?= $this->pageModulesConnected ?>
							</div>
							<?if ( $GLOBALS[ 'Session' ]->AdminUser->isSuperUser ( ) ) { ?>
							<div class="page" id="pageModulesAvailable">
							<?= $this->pageModulesAvailable ?>
							</div>
							<?= $this->moduleExpansions ?>
							<?}?>
						</div>
					</div>
					<div id="pageLayout" class="page" style="padding: 8px">
						<h2>
							<?= i18n ( 'Templates' ) ?>
						</h2>
						<div id="pageTemplates">
							<?= showPageTemplates ( ) ?>
						</div>
						
						<h2>
							<?= i18n ( 'Table layout' ) ?>
						</h2>
						<div class="Container" id="TableLayout" style="padding: 2px">
						</div>
						<div class="SpacerSmall"></div>
						<button type="button" onclick="newTableLayout()">
							<img src="admin/gfx/icons/page_white_add.png"/> <?= i18n ( 'New table layout' ) ?>
						</button>
					</div>
					<div id="pageTrash" class="page">
						<?= showTrashcan () ?>
					</div>
				</div>
				<script type="text/javascript">
					initTabSystem ( 'EditorAdvancedPages' );
					initTabSystem ( 'EditorModuleTabs' );
				</script>
			</td>
			<td class="Column">
				<h1 id="EditHeadline">
					<table cellspacing="0" cellpadding="0" width="100%">
						<tr>
							<td width="18" valign="middle">
								<img src="admin/gfx/icons/page_edit.png" style="margin: 0 4px 0 0" /> 
							</td>
							<td width="1%">
								<?= i18n ( 'Edit' ) ?>: 
							</td>
							<td valign="middle" style="width: 8px">
								<em></em>
							</td>
							<td valign="middle">
								<?if ( !$GLOBALS[ 'Session' ]->AdminUser->checkPermission ( $this->page, 'Write', 'admin' ) ) { ?>
								<input type="hidden" value="<?= $this->page->MenuTitle ?>" size="40" id="MenuTitle">
								"<strong id="EditHeadlineDiv"><?= $this->page->MenuTitle ? $this->page->MenuTitle : $this->page->_oldTitle ?></strong>"
								<?}?>
								<?if ( $GLOBALS[ 'Session' ]->AdminUser->checkPermission ( $this->page, 'Write', 'admin' ) ) { ?>
								<input type="text" 
									style="box-sizing: border-box; -moz-box-sizing: border-box; width: 100%; margin: -6px 0 0 0;"
									value="<?= $this->page->MenuTitle ? $this->page->MenuTitle : $this->page->_oldTitle ?>" id="MenuTitle"/>
								<?}?>
							</td>
							<td valign="middle" style="width: 8px">
								<em></em>
							</td>
							<td width="1%" valign="middle">
								<div id="SmallButtons" class="HeaderBox">
									<?= contentButtons ( $this->page, 1 ) ?>
								</div>
							</td>
						</tr>
					</table>
				</h1>
				<input type="hidden" id="PageID" value="<?= $this->page->ID ?>">
				<input type="hidden" id="PageUrl" value="<?= $this->page->getUrl ( ) ?>">
				<input type="hidden" id="Title" value="<?= trim ( $this->page->Title ) ? $this->page->Title : $this->page->MenuTitle ?>"/>
				<div class="Container" id="ContentForm">
					<div id="ContentFields">
						<?= $this->ContentForm ?>
					</div>
					<div class="SpacerSmallColored Bottom"></div>
					<div id="BottomButtonContainer">
						<div id="BottomButtons">
							<?= contentButtons ( $this->page ) ?>
						</div>
					</div>
				</div>
			</td>
		</tr>
	</table>
	
	<?= enableTextEditor ( ) ?>
