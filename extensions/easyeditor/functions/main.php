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

global $Session, $document;
include_once ( 'lib/classes/dbObjects/dbContent.php' );
include_once ( 'extensions/easyeditor/include/extrafields.php' );
include_once ( 'extensions/easyeditor/include/modulefuncs.php' );
$document->addResource ( 'javascript', 'extensions/easyeditor/javascript/main.js' );

$fieldnames = GetSettingValue ( 'EasyEditor', 'FieldNames' . $page->MainID );

$document->addResource ( 'stylesheet', 'extensions/easyeditor/css/admin.css' );
$etpl = new cPTemplate ( 'extensions/easyeditor/templates/main.php' );
$etpl->page =& $page;

if ( $groups = explode ( ',', $page->ContentGroups ) )
{
	$out = array ();
	foreach ( $groups as $group )
	{
		if ( !trim ( $group ) ) continue;
		$out[] = trim ( $group );
	}
	$groups = $out;
}
else $groups = '';

// Find content field
$db =& dbObject::globalValue ( 'database' );
if ( $groups )
{
	$etpl->editableField = '';
	$out = array ();
	// Show selected content fields ---------------------------------------------
	foreach ( $groups as $g )
	{
		if ( $fields = $db->fetchObjectRows ( $q = '
			SELECT * FROM 
			(
				(
					SELECT ID, `Name`, SortOrder, "Big" AS `DataTable` FROM ContentDataBig 
					WHERE 
						ContentID = \'' . $page->ID . '\' AND 
						ContentTable = "ContentElement" AND 
						AdminVisibility >= 1
						' . ( $groups ? ( ' AND ContentGroup=\'' . $g . '\'' ) : '' ) . '
				)
				UNION
				(
					SELECT ID, `Name`, SortOrder, "Small" AS `DataTable` FROM ContentDataSmall
					WHERE 
						ContentID = \'' . $page->ID . '\' AND 
						ContentTable = "ContentElement" AND 
						AdminVisibility >= 1
						' . ( $groups ? ( ' AND ContentGroup=\'' . $g . '\'' ) : '' ) . '
				)
			) z
			WHERE `Name` IN ( "' . str_replace ( ',', '","', $fieldnames ) . '" )
			ORDER BY SortOrder ASC
		' ) )
		{
			foreach ( $fields as $f )
			{
				$obj = $db->fetchObjectRow ( '
					SELECT *, "' . $f->DataTable . '" AS `DataTable` 
					FROM 
					ContentData' . $f->DataTable . ' 
					WHERE ID=' . $f->ID . ' LIMIT 1
				' );
				$out[] = '
					<p>
						<strong>' . $obj->Name . ':</strong>
					</p>' . renderExtraField ( $obj, $page );
			}
		}
	}
	if ( count ( $out ) )
	{
		foreach ( $out as $o )
		{
			$etpl->editableField .= $o;
		}
	}
	// Show first visible content field -----------------------------------------
	else if ( $f = $db->fetchObjectRow ( $q = '
		SELECT * FROM 
		(
			(
				SELECT ID, `Name`, SortOrder, "Big" AS `DataTable` FROM ContentDataBig 
				WHERE 
					ContentID = \'' . $page->ID . '\' AND 
					ContentTable = "ContentElement" AND 
					AdminVisibility >= 1 AND
					ContentGroup IN ( "' . str_replace ( ' ', '', str_replace ( ',', '","', $page->ContentGroups ) ) . '" )
			)
			UNION
			(
				SELECT ID, `Name`, SortOrder, "Small" AS `DataTable` FROM ContentDataSmall
				WHERE 
					ContentID = \'' . $page->ID . '\' AND 
					ContentTable = "ContentElement" AND 
					AdminVisibility >= 1 AND
					ContentGroup IN ( "' . str_replace ( ' ', '', str_replace ( ',', '","', $page->ContentGroups ) ) . '" )
			)
		) z
		ORDER BY SortOrder ASC
		LIMIT 1
	' ) )
	{
		$obj = $db->fetchObjectRow ( '
			SELECT *, "' . $f->DataTable . '" AS `DataTable` 
			FROM 
			ContentData' . $f->DataTable . ' 
			WHERE ID=' . $f->ID . ' LIMIT 1
		' );
		$etpl->editableField .= '<p>
				<strong>' . $obj->Name . ':</strong>
			</p>' . renderExtraField ( $obj, $page );
	}
}

// Notes
if ( $note = $db->fetchObjectRow ( 'SELECT * FROM Notes WHERE ContentTable="ContentElement" AND ContentID=' . $page->ID ) )
{
	$etpl->note = str_replace ( array ( "\n", "\r" ), array ( "<br/>", "" ), stripslashes ( $note->Notes ) );
}
else $etpl->note = '';

$etpl->levels = listLevels ();

$extension .= $etpl->render ();

?>
