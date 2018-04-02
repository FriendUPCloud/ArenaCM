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
		<?= renderPlugin ( 'permissions', Array ( 'ContentID' => $_REQUEST[ 'cid' ], 'ContentTable' => $_REQUEST[ 'type' ], 'PermissionType' => $_REQUEST[ 'mode' ] ) ) ?>
		<div class="SpacerSmallColored"></div>
		<button type="button" onclick="if ( confirm ( 'Er du sikker på at du ønsker å bruke disse\nrettighetene på alle undersidene?' ) ) { document.copyPermissionsToSubs ( ); }">
			<img src="admin/gfx/icons/wand.png"> Bruk på alle undersider
		</button>
		<button type="button" onclick="updateStructure ( ); removeModalDialogue ( 'permissions' )">
			<img src="admin/gfx/icons/cancel.png"> Lukk
		</button>
		
		<script>
			document.copyPermissionsToSubs = function ( )
			{
				var pjax = new bajax ( );
				pjax.openUrl ( 'admin.php?module=mod_permissions', 'post', true );
				pjax.addVar ( 'action', 'copypermissionstosubs' );
				pjax.addVar ( 'type', '<?= $_REQUEST[ 'mode' ] ?>' );
				pjax.addVar ( 'cid', document.getElementById ( 'PageID' ).value );
				pjax.onload = function ( )
				{
					if ( this.getResponseText ( ) == 'ok' )
					{
						document.location = 'admin.php?module=extensions&extension=editor';
					}
					else
					{
						alert ( this.getResponseText ( ) );
					}
				}
				pjax.send ( );
			}
		</script>
