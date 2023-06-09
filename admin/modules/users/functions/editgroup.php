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



$module = new cPTemplate ( "$tplDir/editgroup.php" );
$group = new dbObject ( "Groups" );
$group->load ( $_REQUEST[ "gid" ] );
$module->group =& $group;

/**
 * Read access config from all available modules
**/
$path = 'admin/modules';
$ostr = '';
if ( $dir = opendir ( $path ) )
{
	while ( $file = readdir ( $dir ) )
	{
		if ( $file{0} != '.' )
		{
			if ( !$Session->AdminUser->modulePermission ( 'Access', $file ) ) continue;
			
			$search = $path . '/' . $file . '/accessconfig.php';
			if ( file_exists ( $search ) && is_file ( $search ) )
			{
				$settings = new Dummy ( );
				$st = new dbObject ( 'Setting' );
				if ( $st = $st->find ( '
					SELECT * FROM `Setting` WHERE SettingType="GroupAccess_' . $group->ID . '" AND `Key` LIKE "' . $file . '_%" ORDER BY `Key` ASC
				' ) )
				{
					foreach ( $st as $s )
					{
						$key = explode ( '_', $s->Key );
						$key = array_reverse ( $key );
						array_pop ( $key );
						$key = array_reverse ( $key );
						$key = implode ( '_', $key );
						if ( count ( $key ) > 1 ) die ( print_r ( $key, true ) );
						$settings->$key = $s->Value;
					}
				}
				
				$name = strtoupper ( $file{0} ) . substr ( $file, 1, strlen ( $file ) - 1 );
				$ostr .= '<h2 class="BlockHead">"';
				$ostr .= '<div style="float: right;">Tilgang: ';
				$ostr .= '<input type="hidden" name="' . $file . '_Access" value="' . $settings->Access . '" id="' . $file . '_Access"/>';
				$ostr .= '<input type="checkbox"' . ( $settings->Access ? ' checked="checked"' : '' ) . ' onchange="document.getElementById ( \'' . $file . '_Access\' ).value = this.checked ? \'1\' : \'0\'"/>';
				$ostr .= '</div>';
				$ostr .= $name . '" innstillinger:</h2>';
				$ostr .= '<div class="BlockContainer">';
				$tpl = new cPTemplate ( $search );
				$tpl->Settings = $settings;
				$tpl->Group =& $group;
				$ostr .= $tpl->render ( );
				$ostr .= '</div>';
			}
		}
	}
	closedir ( $dir );
}
$module->AdminGui = $ostr;
?>
