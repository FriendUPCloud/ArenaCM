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

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
Rune Nilssen
*******************************************************************************/

$module->user =& $GLOBALS[ 'user' ]; // Add user to template
$module->Settings =& $Settings;

if ( $_REQUEST[ 'settingsmodule' ] )
{
	foreach ( $GLOBALS[ 'modules' ] as $m )
	{
		if ( $m->Module == $_REQUEST[ 'settingsmodule' ] )
		{
			$GLOBALS[ 'Session' ]->Set ( 'SettingsModule', $m );
			break;
		}
	}
}

if ( $GLOBALS[ "user" ]->_dataSource == 'core' )
{
	$module->pageSettings = new cPTemplate ( "$tplDir/pageSettings.php" );
	
	/**
	 * loop throught available modules and look for setting whcih shall be set up for them here
	**/
	
	$module->pageSettings->psGeneralSettings = '';
	
	if ( is_array ( $GLOBALS[ 'modules' ] ) )
	{
		foreach( $GLOBALS[ 'modules' ] as $availableModule )
		{
			if ( !$GLOBALS[ 'Session' ]->SettingsModule )
				$GLOBALS[ 'Session' ]->SettingsModule = &$availableModule;
			
			if ( $GLOBALS[ 'Session' ]->SettingsModule->Module != $availableModule->Module )
				continue;
			
			$str = '<div class="SubContainer">';
			$pstr = '';
			
			
			$str .= '<p><strong>Vist modulnavn</strong></p>' .
							'<p><input type="text" id="SettingModuleName" style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box" value="' . $availableModule->ModuleName . '" /></p>' .
							'<p><strong>Vist modulikon</strong></p>';
					
			$str .= '<p><select id="SettingModuleIcon">';
						
			if ( $dir = opendir ( 'admin/gfx/icons' ) )
			{
				while ( $file = readdir ( $dir ) )
				{
					if ( $file[0] == '.' ) continue;
					$icons[] = $file;
				}
				closedir ( $dir );
				arsort ( $icons );
				$icons = array_reverse ( $icons );
				foreach ( $icons as $file )
				{
					if  ( $availableModule->ModuleIcon == $file )
						$s = ' selected="selected"';
					else $s = '';
					$str .= '<option value="' . $file . '"' . $s . '>' . $file . '</option>';
				}
			}
							
			$str .= '</select></p>';
			
			$str .= '</div>';
			
			$str .= '<div class="SpacerSmall"></div>';
			
			if ( is_file( BASE_DIR . '/admin/modules/' . $availableModule->Module . '/settings.php' ) ) 
			{
				include_once( BASE_DIR . '/admin/modules/' . $availableModule->Module . '/settings.php' );
				
				if ( is_array ( $moduleSettings ) )
				{
					if ( $subTpl = new cPTemplate ( "$tplDir/snippetSimplesetting.php" ) )
					{
						foreach ( $moduleSettings as $skey => $sval)
						{
							$subTpl->_FieldType = $moduleSettingTypes[ $skey ];
							$subTpl->Title = $sval;
							$subTpl->Keystring = $skey;
							
							// check for existing content
							$oc = new dbObject( 'Setting' );
							$oc->SettingType = $availableModule->Module;
							$oc->Key = $subTpl->Keystring;
							if ( $oc->load() ) $subTpl->Value = $oc->Value;
							
							$pstr .= $subTpl->render(); 
						}
						unset( $subTpl ); unset( $oc );
					}
				}
				else
				{
					$pstr = '<p>Denne modulen har ingen innstillinger.</p>';
				}
				
				$str .= 	'<div class="SubContainer">' . $pstr . '</div>';
								
			}
			else
			{
				$str .= '<div class="SubContainer"><p>Denne modulen har ingen innstillinger. Vennligst velg en annen modul fra listen over.</div>';
			}
			
			
			$str .= '<div class="SpacerSmall"></div><div class="SubContainer">' .
							'<p><button type="button" onclick="saveSettings(\'' . $availableModule->Module.'\')"><img src="admin/gfx/icons/disk.png" /> Lagre innstillingene</button>' .
							'<button type="button" onclick="document.location=\'admin.php?module=core&action=nudge&offset=up&m=' . $availableModule->Module . '\'">' .
								'<img src="admin/gfx/icons/arrow_left.png" />&nbsp;' .
							'</button>' .
							'<button type="button" onclick="document.location=\'admin.php?module=core&action=nudge&offset=down&m=' . $availableModule->Module . '\'">' .
								'<img src="admin/gfx/icons/arrow_right.png" />&nbsp;' .
							'</button>' .
							'</p>' .
							'</div>';
			
			$module->pageSettings->psGeneralSettings .= $str;
			break;
		}
	}
	
	$module->pageSettings->currentModule = $GLOBALS[ 'Session' ]->SettingsModule;
	$module->pageSettings = $module->pageSettings->render ( );

	// Sites list

	$base =& dbObject::globalValue ( 'corebase' );
	if ( $sites = $base->fetchObjectRows ( 'SELECT * FROM Sites ORDER BY SiteName ASC' ) )
	{
		foreach ( $sites as $site )
		{
			$ostr .= '<tr class="sw' . ( $GLOBALS[ 'sw' ] = ( $GLOBALS[ 'sw' ] == '1' ? '2' : '1' ) ) . '">';
			$ostr .= '<td style="padding: 4px; font-weight: bold">' . $site->SiteName . '</td>';
			$ostr .= '<td style="padding: 4px; text-align: right"><button type="button" onclick="initModalDialogue ( \'modules\', 500, 300, \'admin.php?module=core&function=modules&sid=' . $site->ID . '\' )"><img src="admin/gfx/icons/images.png"/> Sett opp moduler</button><button type="button" onclick="initModalDialogue ( \'site\', 500, 500, \'admin.php?module=core&function=site&sid=' . $site->ID . '\' )"><img src="admin/gfx/icons/page_white_edit.png"/> Endre nettsted</button></td>';
			$ostr .= '</tr>';
		}
		$module->Sites = $ostr;
	}
}


?>
