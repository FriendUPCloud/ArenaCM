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

include_once ( 'lib/classes/dbObjects/dbContent.php' );
$parent = new dbContent ( );
if ( $parent->load ( $_REQUEST[ 'pid' ] ) )
{
	if ( $GLOBALS[ 'Session' ]->AdminUser->checkPermission ( $parent, 'Write', 'admin' ) )
	{
		if ( $db =& $parent->getDatabase ( ) )
		{
			list ( $max, ) = $db->fetchRow ( 'SELECT MAX(SortOrder) FROM ContentElement WHERE Parent=' . $parent->MainID );
			
			$ctpl = false;
			if ( $parent->ContentTemplateID )
			{
				$ctpl = new dbContent ( );
				if ( !$ctpl->load ( $parent->ContentTemplateID ) )
					$ctpl = false;
			}
			
			// Published version	
			$newPage = new dbContent ( );
			$newPage->Title = utf8_encode ( $_REQUEST[ 'title' ] );
			$newPage->ContentType = 'extrafields';
			$newPage->MenuTitle = utf8_encode ( $_REQUEST[ 'menutitle' ] );
			$newPage->Parent = $parent->MainID;
			$newPage->IsPublished = '1';
			$newPage->SortOrder = $max + 1;
			$newPage->save ( );
			$newPage->MainID = $newPage->ID;
			$newPage->Author = $Session->AdminUser->ID;
			$newPage->ContentGroups = $ctpl ? $ctpl->ContentGroups : ( $parent->ContentGroups ? $parent->ContentGroups : 'Topp, Felt1, Felt2, Bunn' );
			$newPage->ContentTemplateID = $parent->ContentTemplateID;
			$newPage->IsSystem = '0';
			$newPage->save ( );
			
			// Work copy
			$newPage->ID = 0;
			$newPage->DateModified = date ( 'Y-m-d H:i:s' );
			$newPage->save ( );
			
			// Copy permissions from parent
			$newPage->copyPermissions ( $ctpl ? $ctpl->ID : $parent->ID, 'admin' );
			$newPage->copyPermissions ( $ctpl ? $ctpl->ID : $parent->ID, 'web' );
			
			// Copy advanced settings from parent (special for easy editor)
			$fieldnames = GetSettingValue ( 'EasyEditor', 'FieldNames' . $parent->MainID );
			SetSetting ( 'EasyEditor', 'FieldNames' . $newPage->MainID, $fieldnames );
			
			// If we're using a template, then copy all extrafields from it
			if ( $ctpl )
			{
				$newPage->copyExtraFields ( $ctpl->ID );
			}
			// Initial extrafield on work copy
			else
			{
				$ef = new dbObject ( 'ContentDataBig' );
				$ef->Type = 'text';
				$ef->ContentTable = 'ContentElement';
				$ef->ContentID = $newPage->ID;
				if ( defined ( 'MAIN_CONTENTGROUP' ) )
					$ef->ContentGroup = MAIN_CONTENTGROUP;
				else if ( strstr ( $parent->ContentGroups, 'Felt1' ) )
					$ef->ContentGroup = 'Felt1';
				else
				{
					$groups = explode ( ',', $newPage->ContentGroups );
					if ( count ( $groups ) > 1 ) $ef->ContentGroup = trim ( $groups[1] );
					else $ef->ContentGroup = trim ( $groups[0] );
				}
				$ef->SortOrder = 1;
				$ef->Name = 'Hovedfelt';
				$ef->IsGlobal = 0;
				$ef->IsVisible = 1;
				$ef->save ( );
			}
			ob_clean ();
			header ( 'Location: admin.php?module=extensions&extension=easyeditor&cid=' . $newPage->ID );
			die ( );
		}
	}
}
header ( 'Location: admin.php?module=extensions&extension=easyeditor' );
die ( );
?>
