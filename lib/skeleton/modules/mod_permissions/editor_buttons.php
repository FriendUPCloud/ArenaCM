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

if ( $Session->AdminUser->isSuperUser ( ) )
{
	$buttonoutput .= '
		<button type="button" onclick="initModalDialogue ( \'permissions\', 650, 583, \'admin.php?module=mod_permissions&action=permissions&cid=' . $content->ID . '&type=ContentElement&mode=admin\' )" title="Se tilgangsinnstillinger for administratorer">
			<img src="admin/gfx/icons/group_key.png">
			' . ( $short ? '' : 'Admin tilgang' ) . '
		</button>
	';
}
if ( $Session->AdminUser->checkPermission ( $content, 'Write', 'admin' ) )
{
	$buttonoutput .= '
		<button type="button" onclick="initModalDialogue ( \'permissions\', 650, 583, \'admin.php?module=mod_permissions&action=permissions&cid=' . $content->ID . '&type=ContentElement&mode=web\' )" title="Se tilgangsinnstillinger for web">
			<img src="admin/gfx/icons/page_key.png">
			' . ( $short ? '' : 'Web tilgang' ) . '
		</button>
	';
}
// Add this one, it is required for Internet Explorer...
$GLOBALS[ 'document' ]->addResource ( 'javascript', 'lib/plugins/permissions/javascript/plugin.php?pmid=' );
?>
