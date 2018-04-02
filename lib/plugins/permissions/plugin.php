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



/*
	Plugin that lets a user manager user permissions
*/
include_once ( 'lib/plugins/permissions/include/functions.php' );

global $document;

if ( $object = dbObject::get ( $options[ 'ContentID' ], $options[ 'ContentTable' ] ) )
{
	$pmtype = $options[ 'PermissionType' ] ? $options[ 'PermissionType' ] : 'web';
	$pmid = $options[ 'PluginID' ] ? $options[ 'PluginID' ] : '';
	
	$tpl = new cPTemplate ( 'lib/plugins/permissions/templates/plugin.php' );
	$tpl->object = $object;
	$tpl->css = file_get_contents ( BASE_DIR . '/lib/plugins/permissions/css/plugin.css' );
	$tpl->javascript = BASE_DIR . '/lib/plugins/permissions/javascript/plugin.js';
	$tpl->PermissionType = $pmtype;
	$tpl->PluginID = $pmid;
	
	switch ( $object->_tableName )
	{
		case 'ContentElement':
			$tpl->ContentType = '"' . ( $object->Title ? $object->Title : $object->MenuTitle ) . '"';
			break;
		default:
			$tpl->ContentType = $object->_tableName;
			break;
	}
	$plugin .= $tpl->render ( );
	unset ( $object, $tpl );
}
else $plugin .= '<div class="SubContainer">Error in initializing plugin: Bad object id / type</div>';
?>
