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



global $pluginTplDir;
ob_clean ();
i18nAddLocalePath ( 'lib/plugins/texteditor/locale' );
switch ( $_REQUEST[ "pluginaction" ] )
{
	case 'showhtml':
		$tpl = new cPTemplate ( $pluginTplDir . '/showhtml.php' );
		die ( $tpl->render ( ) );
		break;
	case 'generategallery':
		include ( 'actions/generategallery.php' );
		break;
	case 'insertlink':
		$tpl = new cPTemplate ( $pluginTplDir . '/insertlink.php' );
		die ( $tpl->render ( ) );
		break;
	case 'creategallery':
		include ( 'lib/plugins/texteditor/actions/creategallery.php' );
		break;
	case 'inserttable':
		include ( 'lib/plugins/texteditor/actions/inserttable.php' );
		break;
	case 'getyoutube':
		include ( 'lib/plugins/texteditor/actions/getyoutube.php' );
		break;
	case 'insertfieldobject':
		include ( 'lib/plugins/texteditor/actions/insertfieldobject.php' );
		break;
	case 'getcontentfields':
		include ( 'lib/plugins/texteditor/actions/getcontentfields.php' );
		break;
	case 'properties':
		$tpl = new cPTemplate ( $pluginTplDir . '/properties.php' );
		$tpl->nodeid = $_REQUEST[ 'nodeid' ];
		die ( $tpl->render ( ) );
		break;
	default: die ( '' );
}
?>
