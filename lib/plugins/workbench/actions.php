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



ob_clean ();
switch ( $_REQUEST[ 'pluginaction' ] )
{
	case 'showelements':
		$ostr = '';
		if ( $eles = explode ( ';', $_REQUEST[ 'data' ] ) )
		{
			foreach ( $eles as $e )
			{
				list ( $id, $type ) = explode ( ':', $e );
				if ( !$id || $id == 'false' ) continue;
				$obj = new dbObject ( $type );
				if ( $obj->load ( $id ) )
				{
					$extra = '';
					switch ( $type )
					{
						case 'ContentElement':
							if ( file_exists ( 'extensions/editor/info.csv' ) )
							{
								$image = 'admin/gfx/icons/page_white.png';
								$link = 'admin.php?module=extensions&extension=editor&cid=' . $obj->ID;
							}
							else
							{
								$image = 'admin/gfx/icons/page.png';
								$link = 'admin.php?module=contents&cid=' . $obj->ID;
							}
							$label = $obj->MenuTitle;
							break;
						case 'Users':
							$image = 'admin/gfx/icons/user.png';
							$link = 'admin.php?module=users&function=user&uid=' . $obj->ID;
							$label = $obj->Username;
							break;
							
						case 'Groups':
							$image = 'admin/gfx/icons/group.png';
							$link = 'admin.php?module=users&function=group&gif=' . $obj->ID;
							$label = $obj->Name;
							break;
							
						case 'NewsCategory':
							$image = 'admin/gfx/icons/folder_page_white.png';
							$link = 'admin.php?module=news&function=category&cid=' . $obj->ID;
							$label = $obj->Name;
							break;
						case 'News':
							$image = 'admin/gfx/icons/newspaper.png';
							$link = 'admin.php?module=news';
							$label = $obj->Title;
							break;
						case 'Image':
							include_once ( 'lib/classes/dbObjects/dbImage.php' );
							$img = new dbImage ( );
							$img->load ( $obj->ID );
							$image = $img->getImageUrl ( 54, 14 );
							$extra = ' style="border-top: 1px solid #aaa; border-left: 1px solid #ccc; border-right: 1px solid #ccc; border-bottom: 1px solid #fff; -moz-border-radius: 2px"';
							$link = 'admin.php?module=library&lid=' . $img->ImageFolder . '&libSearchKeywords=' . $img->Filename;
							$label = $obj->Title ? $obj->Title : $obj->Filename;
							break;

						case 'File':
							include_once ( 'lib/classes/dbObjects/dbFile.php' );
							$file = new dbFile ( );
							$file->load ( $obj->ID );
							$image = $file->getIconPath();
							$link = 'admin.php?module=library&lid=' . $file->FileFolder . '&libSearchKeywords=' . $file->Filename;
							$label = $obj->Title ? $obj->Title : $obj->Filename;
							break;

						case 'Folder':
							$image = 'admin/gfx/icons/application_view_gallery.png';
							$link = 'admin.php?module=library&lid=' . $obj->ID;
							$label = $obj->Name;
							break;

						default:
							$image = 'admin/gfx/icons/page_white.png';
							$link = 'javascript: alert(\'Ingen lenke for dette objektet.\');';
							$label = $obj->getIdentifier ( ) . ' ';
							$label .= '('.str_replace ( 'class', '', $obj->_tableName ).')';
							break;
					}
					
					// Make sure words fit
					$words = explode ( ' ', $label );
					$fulllabel =  $label;
					$label = ''; $a = 0;
					foreach ( $words as $word )
					{
						if ( strlen ( $word ) > 10 )
						{
							$word = substr ( $word, 0, 8 ) . '..';
						}
						if ( $a++ >= 2 ) break;
					}
					$label = implode ( ' ', $words );
					
					$ostr .= "
						<div class=\"WorkbenchObject\" onselectstart=\"return false\" onmousedown=\"dragger.startDrag ( this, { pickup: 'clone', objectType: '$type', objectID: '$id' } )\">
							<div style=\"background: url($image) no-repeat center top; width: 100%; height: 24px\" onmousedown=\"return false\"$extra></div>
							<div class=\"WorkbenchItemContents\">
								<span onmousedown=\"return false\"><a href=\"$link\" title=\"{$fulllabel}\">$label</a></span>
							</div>
							<div class=\"WorkbenchItemOverlay\"></div>
						</div>
					";
				}
			}
			die ( $ostr );
		}
		
		break;
}
die ( "FAIL" );
?>
