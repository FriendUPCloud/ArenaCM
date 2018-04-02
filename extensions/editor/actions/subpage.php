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

include_once ( $extdir . '/include/funcs.php' );
$parent = new dbContent ( );
if ( $parent->load ( $_POST[ 'cid' ] ) )
{
	if ( $GLOBALS[ 'Session' ]->AdminUser->checkPermission ( $parent, 'Write', 'admin' ) )
	{
		if ( $db =& $parent->getDatabase ( ) )
		{
			$otitle = $title = 'Ny side'; $a = 1;
			while ( $row = $db->fetchObjectRow ( '
				SELECT * FROM ContentElement WHERE MenuTitle = \'' . $title . '\' AND Parent=\'' . $parent->MainID . '\'
			' ) )
			{
				$a++;
				$title = $otitle . ' ' . $a;
			}			
			
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
			$newPage->Title = $title;
			$newPage->ContentType = isset( $parent->ContentType ) ? $parent->ContentType : 'extrafields';
			if ( $newPage->ContentType == 'extensions' )
				$newPage->Intro = $parent->Intro;
			$newPage->MenuTitle = $title;
			$newPage->Parent = $parent->MainID;
			$newPage->IsPublished = '1';
			$newPage->SortOrder = $max + 1;
			$newPage->save ( );
			$newPage->MainID = $newPage->ID;
			$newPage->Author = $Session->AdminUser->ID;
			$newPage->ContentGroups = $ctpl ? $ctpl->ContentGroups : ( $parent->ContentGroups ? $parent->ContentGroups : 'Topp, Felt1, Felt2, Bunn' );
			$newPage->ContentTemplateID = $parent->ContentTemplateID;
			$newPage->Template = $parent->Template;
			$newPage->IsSystem = '0';
			
			// Check if we're using contenttemplate etc
			if ( $parent->ContentTemplateID )
			{
				$t = new dbObject ( 'ContentElement' );
				if ( $t->load ( $parent->ContentTemplateID ) )
				{
					$newPage->Template = $t->Template;
				}
			}
			
			// Save the new page
			$newPage->save ( );
			
			// Work copy
			$newPage->ID = 0;
			$newPage->DateModified = date ( 'Y-m-d H:i:s' );
			$newPage->save ( );
			
			// Copy permissions from parent
			$newPage->copyPermissions ( $ctpl ? $ctpl->ID : $parent->ID, 'admin' );
			$newPage->copyPermissions ( $ctpl ? $ctpl->ID : $parent->ID, 'web' );
			
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
				if ( strstr ( $parent->ContentGroups, 'Felt1' ) )
				{
					$ef->ContentGroup = 'Felt1';
				}
				else
				{
					$groups = explode ( ',', $newPage->ContentGroups );
					if ( $groups[1] ) $ef->ContentGroup = trim ( $groups[1] );
					else $ef->ContentGroup = trim ( $groups[0] );
				}
				$ef->SortOrder = 1;
				$ef->Name = 'Hovedfelt';
				$ef->IsGlobal = 0;
				$ef->IsVisible = 1;
				$ef->save ( );
			}
			
			// Set permissions
			if ( $permissions )
			{
				foreach ( $permissions as $p )
				{
					$p->ID = 0;
					$p->ObjectID = $newPage->ID;
					$p->save ( );
				}
			}
			
			// Check initializers for each contentfield
			$newPage->LoadExtraFields ();
			foreach ( $newPage as $k=>$v )
			{
				if ( substr ( $k, 0, 7 ) == '_field_' )
				{
					if ( isset ( $newPage->$k ) )
					{
						if ( $v->Type == 'extension' )
						{
							if ( file_exists ( $f = ( 'extensions/' . $v->Name . '/field_init.php' ) ) )
							{
								$initField = $v;
								$initParent =& $newPage;
								include ( $f );
							}
						}
					}
				}
			}
			
			die ( editorStructure ( $parent ) );
		}
	}
}
die ( '<error>Det oppsto en feil.</error>' );
?>
