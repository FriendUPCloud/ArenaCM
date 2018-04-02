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
 * Plugin Extrafields v0.2
 * For Arena 2
 *
 * @author Hogne Titlestad
 * @c 2007 Blest AS
**/
global $Session;

include_once ( "lib/plugins/extrafields/include/funcs.php" );

// Do this only one time
if ( !$Session->extrafields_checkedExtraFields )
{
	checkPluginExtrafieldsDb ( );
	$Session->set ( 'extrafields_checkedExtraFields', '1' );
}
ob_clean ( );

$s = showExtraFields ( $options[ "ContentID" ], $options[ "ContentType" ] );
if ( !$s ) $s = "Ingen ekstrafelter er definert";

$ptpl = new cPTemplate ( "lib/plugins/extrafields/templates/editor.php" );
$ptpl->ContentType = $options[ "ContentType" ];
$ptpl->ContentID = $options[ "ContentID" ];
$ptpl->extraFields = $s;
$ptpl->Ajax = $options[ "Ajax" ];
$plugin = $ptpl->render ( );
	
?>
