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

// Pre
$db =& dbObject::globalValue ( 'database' );
$ocnt = new dbContent ( );
list ( $ocnt->Language ) = $db->fetchRow ( 'SELECT ID FROM Languages WHERE IsDefault' );
$ocnt->addClause ( 'WHERE', 'Parent=0 AND !IsTemplate AND !IsDeleted AND MainID != ID' );
if ( !( $ocnt = $ocnt->findSingle ( ) ) )
{
	// Create content
	$cnt = new dbContent ( );
	$cnt->Language = $Session->CurrentLanguage;
	$cnt->MenuTitle = 'Uten navn';
	$cnt->Title = 'Uten navn';
	$cnt->Parent = '0';
	$cnt->IsPublished = true;
	$cnt->ContentType = 'extrafields';
	$cnt->ContentGroups = 'Topp, Felt1, Felt2, Bunn';
	$cnt->SystemName = 'root';
	$cnt->save ( );
	$cnt->MainID = $cnt->ID;
	$cnt->save ();
	
	// Make one extrafield for main content
	$mainField = new dbObject ( 'ContentDataBig' );
	$mainField->Name = 'Hovedfelt';
	$mainField->Type = 'text';
	$mainField->ContentID = $cnt->ID;
	$mainField->ContentTable = 'ContentElement';
	$mainField->IsVisible = '1';
	$mainField->ContentGroup = 'Felt1';
	$mainField->save ( );
	
	// Make work copy
	$pub = $cnt->ID;
	$cnt->ID = 0;
	$cnt->copyPermissions ( $ocnt->ID );
	$cnt->MainID = $pub;
	$cnt->save ( );
	$cnt->copyExtraFields ( $cnt->MainID );
	$cnt->copyPermissions ( $cnt->MainID );
}
?>
