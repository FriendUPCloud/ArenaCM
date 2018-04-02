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



ob_clean ( );
if ( !$options[ 'ObjectType' ] || !$options[ 'ObjectID' ] )
	return;
$plugin = new cPTemplate ( $GLOBALS[ "pluginTplDir" ] . "/main.php" );
$plugin->scriptDir = $GLOBALS [ "pluginScriptDir" ];
$plugin->objectConnectionType = $options[ 'ObjectType' ];
$plugin->objectConnectionId = $options[ 'ObjectID' ];
$plugin->heading = isset ( $options[ 'Heading' ] ) ? $options[ 'Heading' ] : 'Objekt tilkobling';
$plugin->heading = isset ( $options[ 'Container' ] ) ? $options[ 'Container' ] : true;
$plugin = $plugin->render ( );
?>
