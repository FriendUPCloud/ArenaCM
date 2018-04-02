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

function activateModule ( $pid, $mod )
{
	$p = new dbContent ( );
	$db =& dbObject::globalValue ( 'database' );
	if ( $p->load ( $pid ) )
	{
		// Get contentgroups
		if ( defined ( 'MAIN_CONTENTGROUP' ) )
		{
			$targetGroup = MAIN_CONTENTGROUP;
		}
		else
		{
			$groups = explode ( ',', $p->ContentGroups );
			foreach ( $groups as $k=>$v ) $groups[$k] = trim ( $v );
			if ( !in_array ( 'Felt1', $groups ) )
				$targetGroup = $groups[0];
			else $targetGroup = 'Felt1';
		}
		
		// Deactivate other modules
		if ( $dir = opendir ( 'lib/skeleton/modules' ) )
		{
			while ( $file = readdir ( $dir ) )
			{
				if ( $file{0} == '.' ) continue;
				if ( $file == $mod ) continue;
				if ( substr ( $file, 0, 4 ) == 'mod_' )
				{
					$info = file_get_contents ( 'lib/skeleton/modules/' . $file . '/info.txt' );
					$info = explode ( '|', $info );
					
					// Ahh simple is a module that can be used by easyeditor
					// now we can try to deactivate this one
					if ( trim( $info[3] ) == 'simple' )
					{
						// Is it active
						if ( GetSettingValue ( $file, $p->ID ) == 1 )
						{
							// Ok, remove the field
							$db->query ( '
								DELETE FROM `ContentDataSmall` 
								WHERE 
									`ContentTable`="ContentElement" AND 
									`ContentID`=\'' . $p->ID . '\' AND 
									`AdminVisibility`=\'1\' AND `IsVisible`=\'1\' AND 
									`ContentGroup`=\'' . $targetGroup . '\' AND `DataString`=\'' . $file . '\'
							' );
							
							// Set the setting that the module is inactive
							SetSetting ( $file, $p->ID, '0' );
							
							// Reactivate replaced field (which is in db)
							$obj = new dbObject ( 'Setting' );
							if ( $obj = $obj->findSingle ( 'SELECT * FROM `Setting` WHERE SettingType="' . $file . '_replaced" AND `Key`=\'' . $p->ID . '\'' ) )
							{
								$data = explode ( '_', $obj->Value );
								
								// Load field
								$fld = new dbObject ( $data[1] );
								$fld->load ( $data[0] );
								$fld->AdminVisibility = '1';
								$fld->IsVisible = '1';
								$fld->save ();
							}
						}
					}
				}
			}
			closedir ( $dir );
		}
		$db->query ( 'DELETE FROM `Setting` WHERE `Key`=' . $p->ID . ' AND SettingType LIKE "%_replaced"' );
	
	
		$info = 'lib/skeleton/modules/' . $mod . '/info.txt';
		if ( file_exists ( $info ) )
		{
			$info = file_get_contents ( $info );
			$info = explode ( '|', $info );
			
			$db =& dbObject::globalValue ( 'database' );
	
			// Create a new content field
			$field = new dbObject ( 'ContentDataSmall' );
			$field->ContentID = $p->ID;
			$field->ContentTable = 'ContentElement';
			$field->Name = $info[0];
			$field->ContentGroup = $targetGroup;
			$field->AdminVisibility = '1';
			$field->IsVisible = '1';
			$field->Type = 'contentmodule';
			$field->DataString = $mod;
			if ( $old = $field->findSingle () )
				$old->delete ();

			$rid = $rtype = '';
			// Eliminate old field (big)
			if ( $row = $db->fetchObjectRow ( '
				SELECT * FROM ContentDataBig
				WHERE 
					ContentID=\'' . $p->ID . '\' AND 
					ContentTable="ContentElement" AND 
					AdminVisibility >= 1
				ORDER BY SortOrder ASC LIMIT 1
			' ) )
			{
				$db->query ( 'UPDATE ContentDataBig SET AdminVisibility=0,IsVisible=0 WHERE ID=' . $row->ID );
				$rid = $row->ID;
				$rtype = 'ContentDataBig';
			}
			// Else eliminate old field (small)
			else if ( $row = $db->fetchObjectRow ( '
				SELECT * FROM ContentDataSmall
				WHERE 
					ContentID=\'' . $p->ID . '\' AND 
					ContentTable="ContentElement" AND 
					AdminVisibility >= 1
				ORDER BY SortOrder ASC LIMIT 1
			' ) )
			{
				$db->query ( 'UPDATE ContentDataSmall SET AdminVisibility=0,IsVisible=0 WHERE ID=' . $row->ID );
				$rid = $row->ID;
				$rtype = 'ContentDataSmall';
			}
			else return false;
			
			// Save new field
			$field->save ();
			SetSetting ( $mod, $p->ID, 1 );
			
			// Store field that was deactivated
			SetSetting ( $mod . '_replaced', $p->ID, $rid . '_' . $rtype );
			return true;
		}
	}
	return false;
}

function listLevels ( $p = '0', $lang = false )
{
	global $Session;
	$db =& dbObject::globalValue ( 'database' );
	$str = '';

	$contents = new dbObject ( 'ContentElement' );
	$contents->addClause ( 'WHERE', 'MainID != ID AND Parent = ' . $p . ' AND !IsTemplate AND !IsDeleted' );
	if ( $lang ) $contents->addClause ( 'WHERE', 'Language=' . $lang );
	$contents->addClause ( 'ORDER BY', 'IsSystem ASC, SortOrder ASC, ID ASC' );

	if ( $rows = $contents->find () )
	{
		$str .= '<ul>';
		foreach ( $rows as $row )
		{
			$row->_tableName = 'ContentElement';
			$row->_isLoaded = true;
			if ( $Session->AdminUser->checkPermission ( $row, 'Read', 'admin' ) )
			{
				$c  = ($Session->EditorContentID == $row->ID) ? ' class="current"' : '';
				$cc = ($Session->EditorContentID == $row->ID) ? ' current' : '';
				$str .= '<li class="' . texttourl ( $row->MenuTitle ) . '' . $cc . '">';
				$str .= '<a' . $c . ' href="admin.php?module=extensions&extension=easyeditor&cid=' . $row->ID . '">';
				if ( !trim ( $row->MenuTitle ) ) $row->MenuTitle = i18n ( 'Unnamed' );
				$str .= $row->MenuTitle . '</a>';
				$str .= listLevels ( $row->MainID, $row->Language );
				$str .= '</li>';
			}
		}
		$str .= '</ul>';
	}
	return $str;
}

?>
