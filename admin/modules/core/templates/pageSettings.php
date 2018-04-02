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
	<?
		global $document;
		$document->addHeadScript ( 'admin/modules/settings/javascript/settings.js' );
	?>
	<table class="LayoutColumns">
		<tr>
			<td style="width: 33%; padding-right: <?= MarginSize ?>px">
				<h1>
					Språk:
				</h1>
				<div class="Container">
					<select id="languageid" size="4" style="width: 100%">
						<?= $this->psLanguages ?>
					</select>
					<div class="SpacerSmall"><em></em></div>
					<div class="SubContainer">
						<button onclick="cfgAddLanguage ( )">
							<img src="admin/gfx/icons/page_white_add.png" /> 
						</button>
						<button onclick="cfgEditLanguage ( )">
							<img src="admin/gfx/icons/page_white_edit.png" />
						</button>
						<button onclick="cfgDeleteLanguage ( )">
							<img src="admin/gfx/icons/page_white_delete.png" />
						</button>
					</div>
				</div>
				<div class="SpacerSmall"></div>
				<h1>
					Hjelpesystem url:
				</h1>
				<div class="Container">
					<form method="post" action="admin.php?module=core&amp;action=savehelpurl">
						<input type="text" size="33" value="<?
							$obj = new dbObject ( 'Setting' );
							$obj->SettingType = 'settings';
							$obj->Key = 'helpsystempath';
							$obj->load ( );
							return $obj->Value;
						?>" name="Value" />
						<div class="SpacerSmall"></div>
						<button type="submit">
							<img src="admin/gfx/icons/disk.png" /> Lagre
						</button>
					</form>
				</div>
			</td>
			<td style="width: 43%; padding-right: <?= MarginSize ?>px">
				<h1>
					Innstillinger for "<?= $this->currentModule->ModuleName ? $this->currentModule->ModuleName : $this->currentModule->Module ?>":
				</h1>
				<div class="SubContainer">
					<strong>Velg en annen modul:</strong>
					<select onchange="document.location='admin.php?module=core&settingsmodule=' + this.value">
						<?
							foreach ( $GLOBALS[ 'modules' ] as $k=>$v )
							{
								if ( $v->Module == $this->currentModule->Module )
									$s = ' selected="selected"';
								else $s = '';
								if ( !trim ( $v->ModuleName ) ) $v->ModuleName = $v->Module;
								$ostr .= '<option value="' . $v->Module . '"' . $s . '>' . $v->ModuleName . '</option>';
							}
							return $ostr;
						?>
					</select>
				</div>
				<div class="SpacerSmall"></div>
				<?= $this->psGeneralSettings ?>
			</td>
			<td style="width: 23%">
				<h1>
					Brukte moduler
				</h1>
				<form method="post" action="admin.php?module=core&amp;action=modulelist" name="moduleform">
				<div class="Container">
					<select style="width: 100%; box-sizing: border-box; -moz-border-sizing: border-box;" size="10" multiple="multiple" name="selectedmodules[]">
						<?
							foreach ( $GLOBALS[ 'modules' ] as $k=>$v )
								$modules[] = $v->Module;
							
							if ( $moduleDir = opendir ( 'admin/modules' ) )
							{
								while ( $file = readdir ( $moduleDir ) )
								{
									if ( $file{0} == '.' ) continue;
									
									$name = false;
									foreach ( $GLOBALS[ 'modules' ] as $k => $v )
									{
										if ( $v->Module == $file )
										{
											$name = $v->ModuleName ? $v->ModuleName : $v->Module;
											$s = ' selected="selected"';
											break;
										}
									}
									if ( !trim ( $name ) )
									{
										$s = '';
										$name = $file;
									}
									$ostr .= '<option' . $s . ' value="' . strtolower ( $file ) . '">' . $name . '</option>';
								}
								closedir ( $moduleDir );
								return $ostr;
							}
						?>
					</select>
					<div class="SpacerSmall"></div>
					<button type="button" onclick="if ( confirm ( 'Er du sikker på at du ønsker å lagre modul listen?' ) ) { document.moduleform.submit(); }">
						<img src="admin/gfx/icons/disk.png" /> Lagre modul listen
					</button>
					</div>
				</form>
			</td>
		</tr>
	</table>
	
	<script>
		cfgRefreshLanguages ( );
	</script>
	
