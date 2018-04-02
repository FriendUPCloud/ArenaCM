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
 * 
**/
function generatePluginFolderstructure ( $currentid = 0, $parent = 0, $r = '' )
{
	global $Session;
	if ( !$currentid ) $currentid = $Session->pluginLibraryLevelID;
	$fld = new dbObject ( 'Folder' );
	$fld->Parent = $parent;
	$fld->addClause ( 'ORDER BY', 'Name ASC, ID ASC' );
	if ( $fld = $fld->find ( ) )
	{
		if ( $parent == 0 )
		{
			$str .= '<select id="ContentFolderSelect" style="width: 100%" onchange="pluginSetLibraryLevel ( this.value )">';
			$end = '</select>';
		}
		else
		{
			$r .= '&nbsp;&nbsp;&nbsp;&nbsp;';
			$end = '';
		}
		foreach ( $fld as $f )
		{
			if ( !$Session->AdminUser->checkPermission ( $f, 'read', 'admin' ) ) continue;
			$s = ( ( $f->ID == $currentid ) || ( $r == '' && !$currentid ) )
				? ' selected="selected"' : '';
			$str .= '<option value="' . $f->ID . '"' . $s . '>' . $r . $f->Name . '</option>';
			$str .= generatePluginFolderstructure ( $currentid, $f->ID, $r );
		}
		return $str . $end;
	}
	return '';
}

/**
 * Generate options list for a level select list
**/
function generatePluginLevelOptions ( $content, $currentid, $r = "" )
{
	global $Session;
	
	if ( !is_object ( $content ) ) return "";
	
	$content->_primaryKey = 'ID';
	$content->_tableName = 'Folder';
	$content->_isLoaded = true;
	
	$access = $Session->AdminUser->checkPermission ( $content, 'Read', 'admin' );
	
	if ( $access )
	{
		if ( $currentid == $content->ID )
			$s = " selected=\"selected\"";
		else $s = "";
		$oStr = "<option value=\"{$content->ID}\"$s>$r{$content->Name}</option>";
		if ( !$content->_folders )
			$content->getFolders ( );
		if ( count ( $content->_folders ) < 1 )
			return $oStr;
		if ( $content->_folders )
		{	
			foreach ( $content->_folders as $f )
				$oStr .= generatePluginLevelOptions ( $f, $currentid, $r . "&nbsp;&nbsp;" );
		}
		return $oStr;
	}		
	return '<option>Ingen nivåer tilgjengelige.</option>';
}

/**
 * generate tree of levels with edit / delete
 */
function generatePluginLevelTree ( $content, $currentid, $r = "", $mode = false )
{
	global $Session;
	
	$oStr = '';

	$content->_primaryKey = 'ID';
	$content->_tableName = 'Folder';
	$content->_isLoaded = true;
	
	$access = $Session->AdminUser->checkPermission ( $content, 'Read', 'admin' );
	
	if ( $access )
	{
		if ( $currentid == $content->ID )
		{
			$oStr .= '<li class="current">';
			$oStr .= '<div class="ButtonBox">';

			if ( $content->Parent > 0 )
			{
				$oStr .= '	<div class="ButtonBoxButtons">';
				$oStr .= '		<button id="libleveltoworkbench" type="button" onclick="addToWorkbench ( \'' . $content->ID . '\', \'Folder\' )" style="width: 30px"><img src="admin/gfx/icons/plugin.png" /></button>';
				$oStr .= '	</div>';
			}
			
			$oStr .= '	<b>'. $content->Name .'</b>';
			$oStr .= '	<div style="clear:both;"><em></em></div>';
			
			$oStr .= '</div>';
		}
		else
		{
			$oStr .= '<li>';
			if ( $mode == false )
			{
				$oStr .= '	<a href="javascript:pluginSetLibraryLevel(' . $content->ID . ')">'. $content->Name .'</a>';			
			}
			else
			{
				switch ( $mode )
				{
					case 'librarydialog':
						$oStr .= '	<a href="javascript:setLibraryDialogLevel(' . $content->ID . ')">'. $content->Name .'</a>';			
						break;
					default:
						break;
				}
			}
		}
		if ( !$content->_folders ) $content->getFolders ( );
		
		if ( count ( $content->_folders ) > 0 )
		{
			$len = count ( $content->_folders );
			$iStr = '';
			for ( $a = 0; $a < $len; $a++ )
			{
				if( is_object( $content->_folders[ $a ] ) )
				{
					$iStr.= generatePluginLevelTree( $content->_folders[ $a ], $currentid, $r, $mode );
				}
			}
			if ( strlen ( $iStr ) )
			{
				$oStr .= '<ul>' . $iStr . '</ul>';
				$iStr = '';
			}
		}
		$oStr .= '</li>';
	}
	return $oStr;
} 


?>
