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
					<div>
						<button onclick="addLibraryImage ()"><img src="admin/gfx/icons/image_add.png"> <?= i18n ( 'Upload image' ) ?></button>
						<button onclick="addLibraryFile ()"><img src="admin/gfx/icons/page_add.png"> <?= i18n ( 'Upload file' ) ?></button>
						<button onclick="createLibraryFile ()"><img src="admin/gfx/icons/star.png"> <?= i18n ( 'Create a file' ) ?></button>
						<button onclick="initModalDialogue ( 'optimizesize', 400, 400, 'admin.php?module=library&function=optimizesize&lid=' + document.lid )"><img src="admin/gfx/icons/folder_image.png"> <?= i18n ( 'Optimize images' ) ?></button>
						<button onclick="deleteSelected ()"><img src="admin/gfx/icons/image_delete.png"> <?= i18n ( 'Delete selected' ) ?></button>
						<button onclick="duplicateSelected ()"><img src="admin/gfx/icons/page_white_copy.png"> <?= i18n ( 'Duplicate selected' ) ?></button>
					
					</div>
					
					!!!
					
					<div class="HeaderBox">
						<button onclick="addLibraryImage ()" title="Last opp nytt bilde"><img src="admin/gfx/icons/image_add.png"></button>
						<button onclick="addLibraryFile ()" title="Last opp ny fil"><img src="admin/gfx/icons/page_add.png"></button>
						<button onclick="createLibraryFile ()" title="Lag en ny fil"><img src="admin/gfx/icons/star.png"></button>
					</div>
				
