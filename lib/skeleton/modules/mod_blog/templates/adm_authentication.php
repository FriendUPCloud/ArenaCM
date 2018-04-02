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
	<div class="SubContainer" style="padding: 2px">
	<?
		if ( $this->blogs )
		{
			$sw = 0;
			foreach ( $this->blogs as $blog )
			{
				$sw = ( $sw + 1 ) % 2;
				$buttons = '';
				$buttons .= '<a href="javascript: void(0)" title="Endre og godkjenn" onclick="mod_blog_edit ( ' . $blog->ID . ' )"><img src="admin/gfx/icons/page_edit.png"></a>';
				$buttons .= '<a href="javascript: void(0)"  class="Small" title="Slett" onclick="mod_blog_delete ( ' . $blog->ID . ' )"><img src="admin/gfx/icons/page_delete.png"></a>';
				$buttons .= '<a href="javascript: void(0)"  class="Small" title="Forhåndsvis" onclick="mod_blog_preview ( ' . $blog->ID . ' )"><img src="admin/gfx/icons/eye.png"></a>';
				$str .= '<tr class="sw' . ( $sw + 1 ) . '"><td>' . $blog->Title . '</td><td>' . $blog->AuthorName . '</td><td>' . ArenaDate ( DATE_FORMAT, strtotime ( $blog->DateUpdated ) ) . '</td><td style="text-align: right">' . $buttons . '</td></tr>';
			}
			return '<table class="Overview LayoutColumns"><tr><th>Tittel:</th><th>Forfatter:</th><th>Dato:</th><th></th></tr>' . $str . '</table>';
		}
		return 'Ingen artikler trenger godkjenning.';
	?>
	</div>
	<div class="SpacerSmallColored"></div>
	<button type="button" onclick="mod_blog_abortedit ( )">
		<img src="admin/gfx/icons/newspaper.png"> Vis blog arkivet
	</button>
