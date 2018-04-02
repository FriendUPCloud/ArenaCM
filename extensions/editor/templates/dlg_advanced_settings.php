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
	<?if( !isset($this->refresh) ) { ?>
	<div id="AdvancedDialog">
	<?}?>
		<h1 style="overflow: hidden; white-space: nowrap">
			<?= i18n ( 'Advanced settings for' ) ?>: <?= $this->content->MenuTitle ?>
		</h1>
		<div class="Container" style="padding: <?= MarginSize ?>px; overflow: hidden">
			<form id="advanced_form" method="post" action="#">
				<table class="LayoutColumns">
					<tr>
						<td>
							<p>
								<strong><?= i18n ( 'Page showing?' ) ?></strong>
							</p>
						</td>
						<td>
							<table>
								<tr>
									<td>
										<table class="LayoutVCenter">
											<tr>
												<td><?= i18n ( 'Yes' ) ?></td>
												<td><input type="radio" name="IsPublished" value="1"<?= $this->content->IsPublished ? ' checked="checked"' : '' ?>></td>
												<td><?= i18n ( 'No' ) ?></td>
												<td><input type="radio" name="IsPublished" value="0"<?= !$this->content->IsPublished ? ' checked="checked"' : '' ?>></td>
											</tr>
										</table>
									</td>
									<td style="border-left: 1px solid #cccccc">
										<table cellspacing="0" cellspacing="0">
											<tr>
												<td style="padding: 2px">
													&nbsp;<?= i18n ( 'Hide controls?' ) ?>
												</td>
												<td style="padding: 2px"><?= i18n ( 'Yes' ) ?></td>
												<td style="padding: 2px"><input type="radio" name="HideControls" value="1"<?= GetSettingValue ( 'ContentElementHideControls', $this->content->MainID ) ? ' checked="checked"' : '' ?>/></td>
												<td style="padding: 2px"><?= i18n ( 'No' ) ?></td>
												<td style="padding: 2px"><input type="radio" name="HideControls" value="0"<?= !GetSettingValue ( 'ContentElementHideControls', $this->content->MainID ) ? ' checked="checked"' : '' ?>/></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>
							<p>
								<strong><?= i18n ( 'Hidden in the menu' ) ?>:</strong>
							</p>
						</td>
						<td>
						<table>
							<tr>
								<td>
									<table class="LayoutVCenter">
										<tr>
											<td><?= i18n ( 'Yes' ) ?></td>
											<td><input type="radio" name="IsSystem" value="1"<?= $this->content->IsSystem ? ' checked="checked"' : '' ?>></td>
											<td><?= i18n ( 'No' ) ?></td>
											<td><input type="radio" name="IsSystem" value="0"<?= !$this->content->IsSystem ? ' checked="checked"' : '' ?>></td>
										</tr>
									</table>
								</td>
								<td style="border-left: 1px solid #cccccc">
									<table cellspacing="0" cellspacing="0">
										<tr>
											<td style="padding: 2px">
												&nbsp;<?= i18n ( 'Fallback page' ) ?>
											</td>
											<td style="padding: 2px"><?= i18n ( 'Yes' ) ?></td>
											<td style="padding: 2px"><input type="radio" name="IsFallback" value="1"<?= $this->content->IsFallback ? ' checked="checked"' : '' ?>/></td>
											<td style="padding: 2px"><?= i18n ( 'No' ) ?></td>
											<td style="padding: 2px"><input type="radio" name="IsFallback" value="0"<?= !$this->content->IsFallback ? ' checked="checked"' : '' ?>/></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					</tr>
					<tr>
						<td>
							<p><strong><?= i18n ( 'Content type' ) ?>:</strong></p>
						</td>
						<td>
							<p>
								<select name="ContentType">
								<?
									$options = '';
									foreach ( Array ( 
										'extensions'=>i18n( 'Using an extension' ),
										'text'=>i18n ( 'Pure textpage' ),
										'extrafields'=>i18n ( 'Extrafields' ),
										'link'=>i18n ( 'Links to another page' )
									) as $k=>$v )
									{
										$options .= '<option value="' . $k . '"' . ( $k == $this->content->ContentType ? ' selected="selected"' : '' ) . '>' . $v . '</option>';
									}
									return $options;
								?>
								</select>
							</p>
						</td>
					</tr>
					<?
						if ( $this->content->ContentType != 'extensions' )
							return;
						$this->content->loadExtraFields ( );
						$config = explode ( "\\n", $this->content->Intro );
						$ext = '';
						foreach ( $config as $cfg )
						{
							list ( $opt, $val ) = explode ( "\\t", $cfg );
							if ( trim ( $opt ) == 'ExtensionName' )
								$ext = trim ( $val );
							if ( trim ( $opt ) == 'ExtensionContentGroup' )
								$grp = trim ( $val );
						}
						if ( $dir = opendir ( 'extensions' ) )
						{
							$ostr = '<option value="">' . i18n ( 'Select extension' ) . '</option>';
							while ( $file = readdir ( $dir ) )
							{
								if ( $file{0} == '.' ) continue;
								if ( file_exists ( 'extensions/' . $file . '/webmodule.php' ) )
								{
									list ( $name, ) = explode ( '|', file_get_contents ( 'extensions/' . $file . '/info.csv' ) );
									$s = $ext == $file ? ' selected="selected"' : '';
									$ostr .= '<option value="' . $file . '"' . $s . '>' . $name . '</option>';
								}
							}
							closedir ( $dir );
							$str = '<tr><td><p><strong>' . i18n ( 'Choose content group' ) . ':</strong></p></td><td><select name="ModuleName" style="margin-top: -4px">' . $ostr . '</select></td></tr>';
							if ( $groups = explode ( ',', $this->content->ContentGroups ) )
							{
								$ostr = '';
								$groups[] = array ( '__override__', 'Module decides' );
								foreach ( $groups as $group )
								{
									$gkey = false;
									if ( is_array ( $group ) )
									{
										$gkey = $group[0];
										$group = $group[1];
									}
									else $group = trim ( $group );
									$s = ( ( $gkey && $grp == $gkey ) || ( $grp == $group ) ) ? ' selected="selected"' : '';
									$ostr .= '<option value="' . ( $gkey ? $gkey : $group ) . '"' . $s . '>' . ( $gkey ? i18n ( $group ) : $group ) . '</option>';
									foreach ( $this->content as $k=>$v )
									{
										if ( substr ( $k, 0, 7 ) == '_extra_' )
										{
											$fn = substr ( $k, 7, strlen ( $k ) - 7 );
											if ( $this->content->{"_field_$fn"}->ContentGroup == $group )
											{
												$s = $grp == $fn ? ' selected="selected"' : '';
												$ostr .= '<option value="' . $fn . '"' . $s . '>' . $fn . '</option>';
											}
										}
									}
								}
								$str .= '<tr><td><p><strong>Modulplassering:</strong></p></td><td>' . i18n ( 'Put in' ) . ' <select name="ModuleContentGroup" style="margin-top: -4px">' . $ostr . '</select></td></tr>';
							}
							return $str;
						}
						return;
					?>
					<tr>
						<td>
							<p><strong><?= i18n ( 'System name' ) ?>:</strong></p>
						</td>
						<td>
							<p>
								<input type="text" name="SystemName" size="30" value="<?= $this->content->SystemName ?>">
							</p>
						</td>
					</tr>
					<tr>
						<td>
							<p><strong><?= i18n ( 'Page title' ) ?>:</strong></p>
						</td>
						<td>
							<p>
								<input type="text" name="PageTitle" size="30" value="<?= $this->content->Title ?>">
							</p>
						</td>
					</tr>
					<tr>
						<td>
							<p><strong><?= i18n ( 'Content groups' ) ?>:</strong></p>
						</td>
						<td>
							<p>
								<input type="text" name="ContentGroups" size="30" value="<?= $this->content->ContentGroups ?>">
							</p>
						</td>
					</tr>
					<tr>
						<td>
							<p><strong><?= i18n ( 'Subpages use template' ) ?>:</strong></p>
						</td>
						<td>
							<p>
								<select name="ContentTemplateID">
									<?
										$db =& dbObject::globalValue ( 'database' );
										if ( $rows = $db->fetchObjectRows ( 'SELECT * FROM ContentElement WHERE IsTemplate AND !IsDeleted AND MainID = ID ORDER BY SortOrder ASC' ) )
										{
											$str .= '<option value="0">' . i18n ( 'Do not use template' ) . '</option>';
											foreach ( $rows as $row )
											{
												if ( $row->ID == $this->content->ContentTemplateID )
													$s = ' selected="selected"';
												else $s = '';
												$str .= '<option value="' . $row->ID . '"' . $s . '>' . $row->MenuTitle . '</option>';
											}
											return $str;
										}
										return '<option value="0">' . i18n ( 'No template is set up' ) . '</option>';
									?>
								</select>
							</p>
						</td>
					</tr>
					<?if ( file_exists ( BASE_DIR . '/templates' ) && is_dir ( BASE_DIR . '/templates' ) ) { ?>
					<tr>
						<td>
							<p><strong><?= i18n ( 'Programmed template' ) ?>:</strong></p>
						</td>
						<td>
							<p>
								<select name="Template">
									<?
										$str = '<option value="">' . i18n ( 'Do not use programmed template' ) . '.</option>';
										if ( $dir = opendir ( BASE_DIR . '/templates' ) )
										{
											while ( $file = readdir ( $dir ) )
											{
												if ( $file{0} == '.' ) continue;
												if ( strtolower ( substr ( $file, 0, 5 ) ) != 'page_' ) continue;
												$s = ( $file == $this->content->Template ? ' selected="selected"' : '' );
												$fname = str_replace ( array ( 'page_', '.php' ), '', $file );
												$fname = strtoupper ( $fname{0} ) . substr ( $fname, 1, strlen ( $fname ) - 1 );
												$fname = str_replace ( '_', ' ', $fname );
												$str .= '<option value="' . $file . '"' . $s . '>' . $fname . '</option>';
											}
										}
										return $str;
									?>
								</select>
							</p>
						</td>
					</tr>
					<?}?>
					<tr>
						<td>
							<p><strong><?= i18n ( 'Url' ) ?>:</strong></p>
						</td>
						<td>
							<p>
								<input type="text" value="<?= $this->content->getUrl () ?>" style="width: 240px"/>
							</p>
						</td>
					</tr>
				</table>
			</form>
		</div>
		<div class="SpacerSmallColored"></div>
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td>
					<button type="button" onclick="executeAdvanced ( )">
						<img src="admin/gfx/icons/accept.png"> <?= i18n ( 'Save' ) ?>
					</button>
					<button type="button" onclick="removeModalDialogue ( 'advanced' )">
						<img src="admin/gfx/icons/cancel.png"> <?= i18n ( 'Close' ) ?>
					</button>
				</td>
				<td style="text-align: right">
					<button type="button" onclick="createTemplate ( )">
						<img src="admin/gfx/icons/page_white_star.png"> <?= i18n ( 'Save as template' ) ?>
					</button>
				</td>
			</tr>
		</table>
	<?if( !isset($this->refresh) ) { ?>
	</div>
	<?}?>
