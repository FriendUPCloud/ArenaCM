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



/**
 * Things for getting/setting current plugin
**/

function getCurrentModule ( )
{
	global $Session;
	
	if ( 
		array_key_exists ( 'module', $_REQUEST ) && 
		file_exists ( 'admin/modules/' . $_REQUEST[ 'module' ] . '/module.php' ) 
	)
	{
		$Session->Set ( 'arenaCurrentModule', $_REQUEST[ 'module' ] );
		return $Session->arenaCurrentModule;
	}
	else if ( 
		array_key_exists ( 'module', $_REQUEST ) && 
		file_exists ( 'extensions/' . $_REQUEST[ 'module' ] . '/extension.php' ) 
	)
	{
		$Session->Set ( 'arenaCurrentModule', 'extensions' );
		$_REQUEST[ 'extension' ] = $_REQUEST[ 'module' ];
	}
	if ( !$Session->arenaCurrentModule )
		return false;
	return $Session->arenaCurrentModule;
}

function setCurrentModule ( $module )
{
	global $Session;
	if ( !is_object ( $module ) ) return false;
	$Session->Set ( 'arenaCurrentModule', $module->Module );
	return true;
}

/**
 * Include deps and run a module plugin
**/

function renderPlugin ( $name, $options = false )
{
	global $Session;
	if ( file_exists ( "lib/plugins/$name/plugin.php" ) )
	{
		$GLOBALS[ 'pluginTplDir' ] = BASE_DIR . "/lib/plugins/$name/templates";
		$GLOBALS[ 'pluginScriptDir' ] = BASE_URL . "lib/plugins/$name/javascript";
		$plugin = '';
		include ( "lib/plugins/$name/plugin.php" );
		return $plugin;
	}
}

/**
 * Execute a plugin function 
**/
function getPluginFunction ( $name, $functionname, $options = false )
{
	global $Session;
	if ( file_exists ( BASE_DIR . "/lib/plugins/$name/functions/$functionname.php" ) )
	{
		$GLOBALS[ 'pluginTplDir' ] = BASE_DIR . "/lib/plugins/$name/templates";
		$GLOBALS[ 'pluginScriptDir' ] = BASE_URL . "lib/plugins/$name/javascript";
		$plugin = '';
		include_once ( BASE_DIR . "/lib/plugins/$name/functions/$functionname.php" );
		return $plugin;
	}
}

/**
 * Check for plugin actions
**/

function checkPluginAction ( )
{
	global $Session;
	$name = isset ( $_REQUEST[ 'plugin' ] ) ? $_REQUEST[ 'plugin' ] : '';
	if ( !$name ) return;
	$GLOBALS[ 'pluginTplDir' ] = BASE_DIR . "/lib/plugins/$name/templates";
	$GLOBALS[ 'pluginScriptDir' ] = BASE_URL . "lib/plugins/$name/javascript";
	if ( file_exists ( BASE_DIR . "/lib/plugins/$name/actions.php" ) )
		include_once ( BASE_DIR . "/lib/plugins/$name/actions.php" );
}

/**
 * Retrieve module configuration screen for a page
**/
function getPageModuleConfig ( $name, $content )
{
	if ( file_exists ( BASE_DIR . "/admin/modules/$name/pageconfig.php" ) )
	{
		include_once ( BASE_DIR . "/admin/modules/$name/pageconfig.php" );
		return $config;
	}
	return 'Error loading module configuration panel.';
}

?>
