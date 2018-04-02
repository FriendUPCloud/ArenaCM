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

/** =========================================================================================================
 * get contents of chosen level
 */
function getLevelContent( $lid )
{
	global $Session, $database;
	$db =& dbObject::globalValue ( 'database' );
	
	include_once ( "lib/classes/dbObjects/dbFolder.php" );
	include_once ( "lib/classes/dbObjects/dbImage.php" );
	include_once ( "lib/classes/dbObjects/dbFile.php" );
	include_once ( "lib/classes/time/ctime.php" );
	
	$fld = new dbFolder ( );
	$fld->load ( $lid );
	$time = new cTime ( );
	$limit = 15;
	$pos = $_REQUEST[ 'pos' ] ? $_REQUEST[ 'pos' ] : '0';
	$images = $files = false;
	
	// If we want the detail view
	if ( $Session->LibraryViewMode == 'details' )
	{	
		$lm = '';
		switch ( $Session->LibraryListmode )
		{
			case 'date': $lm = 'DateModified'; break;
			case 'filename': $lm = 'Filename'; break;
			case 'filesize': $lm = 'Filesize'; break;
			case 'sortorder': $lm = 'SortOrder'; break;
			case 'title':
			default: $lm = 'Title'; break;
		}
		
		// If we list by tag
		if ( $t = $_REQUEST[ 'tag' ] )
		{
			$q = '
				SELECT * FROM
				(
					( 
						SELECT 
							Image.ID, Image.Title, Image.Filename, Image.Filesize, Image.Width, 
							Image.Height, Image.DateModified, Image.SortOrder, \'Image\' AS `Type`,
							Image.Tags
						FROM `Image` 
						WHERE Image.Tags LIKE "%'.$t.'%"
					)
					UNION
					( 
						SELECT 
							File.ID, File.Title, File.Filename, File.Filesize, 0 AS `Width`, 
							0 AS `Height`, File.DateModified, File.SortOrder, \'File\' AS `Type`,
							File.Tags
						FROM `File` 
						WHERE File.Tags LIKE "%'.$t.'%"
					)
				) as z
				ORDER BY `' . $lm . '` ' . $Session->LibraryListmodeOrder . '
				LIMIT ' . $pos . ',' . $limit . '
			';
		}
		// Else just list out current folder
		else
		{
			$q = '
				SELECT * FROM (
				( SELECT 
					Image.ID, Image.Title, Image.Filename, Image.Filesize, Image.Width, 
					Image.Height, Image.DateModified, Image.SortOrder, \'Image\' AS `Type`
				FROM `Image` WHERE `ImageFolder` = \'' . ( $lid == 'orphans' ? '0' : $lid ) . '\' )
				UNION
				( SELECT 
					File.ID, File.Title, File.Filename, File.Filesize, 0 AS `Width`, 
					0 AS `Height`, File.DateModified, File.SortOrder, \'File\' AS `Type`
				FROM `File` WHERE `FileFolder` = \'' . ( $lid == 'orphans' ? '0' : $lid ) . '\' )
				) as z
				ORDER BY `' . $lm . '` ' . $Session->LibraryListmodeOrder . '
				LIMIT ' . $pos . ',' . $limit . '
			';
		}
		
		// Get count of all rows
		$countq = explode ( 'ORDER BY', $q ); $countq = $countq[0];
		$countq = explode ( 'SELECT * FROM', $countq );
		$countq = "SELECT COUNT(*) CNT FROM {$countq[1]}";
		$total = $fld->_table->database->fetchObjectRow ( $countq );
		$total = $total->CNT;
		
		$tpl = new cPTemplate ( 'admin/modules/library/templates/listcontents_mode_details.php' );
		if ( $rows = $fld->_table->database->fetchObjectRows ( $q ) )
		{
			$im = 0;
			foreach ( $rows as $row )
			{
				$icon = false;
				if ( $row->Type == 'Image' )
				{
					$i = new dbImage ( $row->ID );
					$icon = $i->getImageUrl ( 12, 12, 'framed' );
					$act = 'editLibraryImage( \'' . $row->ID . '\' ); return false;';
					$drag = 'dragger.startDrag ( this.parentNode.getElementsByTagName ( \'td\' )[1].firstChild, { pickup: \'clone\', objectType: \'Image\', objectID: \'' . $row->ID . '\' } ); return false';
				}
				else 
				{
					$act = 'editLibraryFile( \'' . $row->ID . '\' ); return false;';
					$drag = 'dragger.startDrag ( this.parentNode.getElementsByTagName ( \'td\' )[1].firstChild, { pickup: \'clone\', objectType: \'File\', objectID: \'' . $row->ID . '\' } ); return false';
				}
				
				$ext = explode ( '.', $row->Filename ); $ext = $ext[ count ( $ext ) - 1 ];
				if ( !$icon )
				{
					switch ( strtolower ( $ext ) )
					{
						case 'jpg':
						case 'gif':
						case 'png':
						case 'bmp':
						case 'jpeg':
							$icon = 'admin/gfx/icons/page_white_picture.png';
							break;
						case 'pdf':
							$icon = 'admin/gfx/icons/page_white_acrobat.png';
							break;
						case 'ppt':
							$icon = 'admin/gfx/icons/page_white_powerpoint.png';
							break;
						case 'htm':
						case 'html':
						case 'js':
						case 'css':
							$icon = 'admin/gfx/icons/page_white_code.png';
							break;
						case 'xls':
						case 'xlsx':
							$icon = 'admin/gfx/icons/page_white_excel.png';
							break;
						case 'swf':
						case 'flv':
						case 'fla':
							$icon = 'admin/gfx/icons/page_white_flash.png';
							break;
						case 'doc':
						case 'docx':
							$icon = 'admin/gfx/icons/page_white_word.png';
							break;
						default:
							$icon = 'admin/gfx/icons/page_white.png';
							break;
					}
				}
				
				// Correct dates if need be
				$modified = false;
				if ( !$row->DateCreated ) 
				{
					$row->DateCreated = date ( 'Y-m-d H:i:s' );
					$modified = 1;
				}
				if ( !$row->DateModified ) 
				{
					$row->DateModified = $row->DateCreated;
					$modified = 1;
				}
				if ( $modified )
				{
					$f = new dbObject ( 'Folder' );
					if ( $f->load ( $row->ID ) )
					{
						foreach ( $f->_table->getFieldNames ( ) as $fi )
						{
							$f->$fi = $row->$fi;
						}
						$f->save ( );
					}
				}
				// Done correcting dates
				$fn = $row->Type == 'Image' ? "upload/images-master/{$row->Filename}" : "upload/{$row->Filename}";
				$rlFilesize = file_exists ( $fn ) ? filesize ( $fn ) : 0;
				if ( $rlFilesize != $row->Filesize )
				{
					if ( $row->Type == 'Image' )
						$db->query ( 'UPDATE `Image` SET `Filesize`=\''.$rlFilesize.'\' WHERE ID=\''.$row->ID.'\'' );
					else $db->query ( 'UPDATE `File` SET `Filesize`=\''.$rlFilesize.'\' WHERE ID=\''.$row->ID.'\'' );
					$row->Filesize = (int)$rlFilesize;
				}
				
				$onc = 'setSortOrder(\''.$row->ID.'\',\''.$row->Type.'\',this.value)';
				$click = 'toggleSelectedImage ( this )';
				$drag = ' onclick="return false;" onmousedown="' . $drag . '" onstartdrag="return false;"';
				
				$str .= '
					<tr id="' . ( $row->Type == 'Image' ? 'imagecontainer' : 'tfilecontainer' ) . $row->ID . '" onclick="' . $click . '" ondblclick="' . $act . '" class="sw' . ( $sw = ( $sw == 2 ? 1 : 2 ) ) . ' Listedcontainer">
						<td style="text-align: right; width: 24px"><input type="text" size="2" onchange="'.$onc.'" class="SmallNum" value="' . ($row->SortOrder?$row->SortOrder:'0') . '"/></td>
						<td class="Icon" style="width: 24px"><div class="Container" style="padding: 3px"><img src="' . $icon . '" ></div></td>
						<td' . $drag . '>' . stripslashes ( $row->Title ) . '</td>
						<td' . $drag . '>' . $row->Filename . '</td>
						<td style="text-align: right"' . $drag . '>' . filesizetohuman ( $row->Filesize ) . '</td>
						<td style="text-align: center; width: 150px"' . $drag . '>' . ArenaDate ( $row->DateModified, DATEFORMAT ) . '</td>
						<td style="text-align: center; width: 80px"><button onclick="editLibrary' . ( $row->Type == 'Image' ? 'Image' : 'File' ) . '(' . $row->ID . ')" type="button" style="display: block"><img src="admin/gfx/icons/' . ( $row->Type == 'Image' ? 'image' : 'page' ) . '_edit.png"/> ' . i18n ( 'i18n_edit' ) . '</button></td>
					</tr>
				';
				$im++;
			}
			$tpl->contents = $str;
			
			if ( $total > $limit )
			{
				$pagin = new cPagination ();
				$pagin->Count = $total;
				$pagin->Limit = $limit;
				$pagin->Position = $pos;
				$pagin->UsePages = true;
				$pagin->ShowCount = true;
				$tpl->nav = '<div class="SpacerSmallColored"></div>' . $pagin->render ();
			}
		}
		else
		{
			$tpl->contents = '<tr class="sw1"><td colspan="7">' . i18n ( 'i18n_no_files_in_folder' ) . '</td></tr>';
			$tpl->nav = '';
		}
		return $tpl->render ( );
	}
	// If we want the normal thumbnail view
	else
	{
		if ( $Session->AdminUser->checkPermission ( $fld, 'Read', 'admin' ) )
		{
			$iW = array (); $fW = array ();
			if ( $_REQUEST[ 'tag' ] )
			{
				$iW[] = '( i.Tags LIKE "%' . trim ( $_REQUEST[ 'tag' ] ) . '%" )';
				$fW[] = '( f.Tags LIKE "%' . trim ( $_REQUEST[ 'tag' ] ) . '%" )';
			}
			
			if ( count ( $iW ) ) $iW = 'AND ( ' . implode ( ' OR ', $iW ) . ' )';
			else $iW = '';
			if ( count ( $fW ) )	$fW = 'AND ( ' . implode ( ' OR ', $fW ) . ' )';
			else $fW = '';
			
			$listMode = '';
			
			switch ( $Session->LibraryListmode )
			{
				case 'title':
					$listMode = 'Title ' . $Session->LibraryListmodeOrder;
					break;
				case 'filesize':
					$listMode = 'Filesize ' . $Session->LibraryListmodeOrder;
					break;
				case 'filename':
					$listMode = 'Filename ' . $Session->LibraryListmodeOrder;
					break;
				default:
					$listMode = 'DateModified ' . $Session->LibraryListmodeOrder;
					break;
			}
			
			if ( $rows = $database->fetchObjectRows ( '
				SELECT * FROM 
				(
					(
						SELECT i.ID, i.Filename, i.Filesize, i.Title, i.DateModified, "Image" AS `Type` FROM `Image` i
						WHERE 
							i.ImageFolder = \'' . $fld->ID . '\'
						' . $iW . '
					)
					UNION
					(
						SELECT f.ID, f.Filename, f.Filesize, f.Title, f.DateModified, "File" AS `Type` FROM `File` f
						WHERE 
							f.FileFolder = \'' . $fld->ID . '\'
						' . $fW . '
					)
				)
				z
				ORDER BY ' . $listMode . '
			' ) )
			{
				return array ( 'all' => $rows );
			}
			return array ( 'images' => array (), 'files' => array () );
		}
	}
	return false;
	
} // end of getLevelContent

/** =========================================================================================================
 * generate tree of levels with edit / delete
 */
function generateLevelTree ( $content, $currentid, $r = "" )
{
	global $Session;
	
	$oStr = '';
	
	$content->_tableName = 'Folder';
	$content->_primaryKey = 'ID';
	$content->_isLoaded = true;
	
	$access = $Session->AdminUser->checkPermission ( $content, 'Read', 'admin' );
	
	if ( $access )
	{
		// Make sure we at least have one selected
		if ( !$currentid ) 
		{
			$currentid = new dbFolder ( );
			$currentid = $currentid->getRootFolder ( );
			$currentid = $currentid->ID;
		}

		if ( $currentid == $content->ID )
		{
			$oStr.='<li class="current" id="currentlevel">';
			$oStr.='<div class="ButtonBox" id="levelButtons' . $content->ID . '">';
			$oStr .= '	<b id="levelli' . $content->ID . '" onmousedown="dragger.startDrag ( this, { pickup: \'clone\', objectType: \'Folder\', objectID: \'' . $content->ID . '\' } ); return false">'. trim ( $content->Name ) .'</b>';
			
			$oStr.='	<div class="ButtonBoxButtons">';
			$delete = $Session->AdminUser->checkPermission ( $content, 'Delete', 'admin' );
			$structure = $Session->AdminUser->checkPermission ( $content, 'Structure', 'admin' );
			if ( $structure && $content->ID != 'orphans' )
			{
				$oStr .= 		'<button type="button" id="editliblevel" onclick="if ( this.onmouseout ){ this.onmouseout(); }; editLibraryLevel( ' . $content->ID . ' );"><img src="admin/gfx/icons/folder_edit.png"></button>';
				$oStr .=		'<button type="button" id="addlevel" onclick="addLibraryLevel ('.$content->ID.')"><img src="admin/gfx/icons/folder_add.png"/></button>';
				if ( $Session->AdminUser->isSuperUser () )
				{
					$oStr .= 		'<button type="button" id="editpermissions" onclick="editLevelPermissions( ' . $content->ID . ' );"><img src="admin/gfx/icons/group_edit.png"></button>';
				}
			}
			if ( $currentid != 1 && $content->ID != 'orphans' && $content->Parent > 0 )
			{
				if ( $delete )
				{
					$oStr .= 	'<button type="button" id="deleteliblevel" title="' . i18n( 'delete' ) . '" onclick="if ( this.onmouseout ) this.onmouseout();deleteLibraryLevel( ' . 
									$content->ID . ' );">' .
									'      <img src="admin/gfx/icons/folder_delete.png" title="' . i18n ( 'i18n_delete' ) . '" /></button>';
				}
				$oStr .= 		'<button id="libleveltoworkbench" type="button" onclick="addToWorkbench ( \'' . $content->ID . '\', \'Folder\' )" style="width: 30px"><img src="admin/gfx/icons/plugin.png" /></button>';
			}
			$oStr .= '	</div>';
			
			$oStr .= '	<div style="clear:both;"><em></em></div>';
			$oStr .= '</div>';
		}
		else
		{
			$oStr .= '<li>';
			$oStr .= '	<a id="levelli' . $content->ID . '" href="javascript:setLibraryLevel(\'' . $content->ID . '\')" onmousedown="dragger.startDrag ( this, { pickup: \'clone\', objectType: \'Folder\', objectID: \'' . $content->ID . '\' } ); return false">'. ( $content->Name != '' ? trim ( $content->Name ) : ( '<i>' . i18n ( 'i18n_no_title' ) . '</i>' ) )  .'</a>';			

		}
		if ( !$content->_folders ) 
		{
			$content->getFolders ( );
		}
		
		$len = count ( $content->_folders );
		
		if ( $len > 0 && is_array ( $content->_folders ) )
		{
			$oStr.='<ul>';
			for ( $a = 0; $a < $len; $a++ )
			{
				if( is_object( $content->_folders[ $a ] ) )
				{
					$oStr.= generateLevelTree( $content->_folders[ $a ], $currentid );
				}
			}
			$oStr.='</ul>';			
		}
		// Orphans entry (trash)
		if ( $content->Parent == 0 )
		{
			$oStr .= '<li'.($currentid=='orphans'?' id="currentlevel" class="current"><div class="ButtonBox"><b':'><a').' id="levellitrash" href="javascript:setLibraryLevel(\'orphans\')">'. i18n ( 'Unorganized material' ) .($currentid=='orphans'?'</b><div style="clear:both"></div></div>':'</a>').'</li>';
		}
		
		// check if description to level is given.... initialize toolto then..
		if( trim( $content->Description ) != '' )
		{
			$desc = str_replace ( array ( "\r", "\n" ), "", $content->Description );
			$oStr.= '<script>addToolTip( "' . $content->Name . '","' . addslashes ( $desc ) . '","levelli'.$content->ID.'" )</script>';
		}
		
		$oStr.='</li>';
	}
	else if ( $content->Parent == 0 && $content->ID > 0 )
	{
		$oStr.='<li class="current" id="currentlevel" style="padding: 4px">' . i18n ( 'i18n_no_folders_pr_permission' ) . '</li>';	
	}
	return $oStr;
	
} // end of generateLevelTree

function generateLevelOptions( $content, $currentid = false, $excludeid = false, $level = 0 )
{
	$oStr = '';

	// check for exclude ======================================================
	if ( $excludeid == $content->ID	) return '';

	// check for selected =====================================================
	if ( $currentid == $content->ID	)
		$xtra = ' selected="selected"';
	else
		$xtra = '';
	
	
	$oStr .= '<option value="' . $content->ID . '" '.$xtra.'>';
	
	for ( $i = $level; $i > 0; $i-- ) $oStr .= '&nbsp; &nbsp;';
	
	$oStr .= trim ( $content->Name ) . '</option>';

	if ( !$content->_folders ) $content->getFolders ( );
	
	if ( count ( $content->_folders ) > 0 )
	{
		
		$len = count ( $content->_folders );
		for ( $a = 0; $a < $len; $a++ )
		{
			if ( is_object( $content->_folders[ $a ] ) ) $oStr.= generateLevelOptions( $content->_folders[ $a ], $currentid, $excludeid, $level+1 );
		}
	}
	return $oStr;
}
?>
