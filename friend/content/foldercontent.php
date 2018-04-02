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


include_once ( 'lib/classes/dbObjects/dbFolder.php' );
include_once ( 'lib/classes/dbObjects/dbContent.php' );
include_once ( 'lib/classes/dbObjects/dbFile.php' );

function listContentFolders ( $parent, $path )
{
	global $database;
	
	if( isset( $path ) )
	{
		list( $volume, $path ) = explode( ':', $path );
		$volume .= ':';
	}
	else if( isset( $_REQUEST['path'] ) )
	{
		list( $volume, $path ) = explode( ':', $_REQUEST['path'] );
		$volume .= ':';
	}
	
	$out = array ();
	if ( $rows = $database->fetchObjectRows ( '
		SELECT e.* FROM ContentElement e
		WHERE
			e.Parent = \'' . $parent . '\' AND e.MainID = e.ID AND
			!e.IsDeleted
		ORDER BY 
			SortOrder ASC, ID ASC
	' ) )
	{
		foreach ( $rows as $row )
		{
			$cl = new stdclass ();
			$cl->Type = 'Directory';
			$cl->MetaType = 'Directory';
			$cl->Filename = $row->MenuTitle;
			$cl->ObjectType = 'ContentElement';
			$cl->ObjectID = $row->MainID;
			$cl->Path = $volume . ( $path ? ( $path . '/' ) : '' ) . $cl->Filename . '/';
			$out[] = $cl;
		}
	}
	return $out;
}

function listContentFiles ( $parent, $path )
{
	global $database;
	
	if( isset( $path ) )
	{
		list( $volume, $path ) = explode( ':', $path );
		$volume .= ':';
	}
	else if( isset( $_REQUEST['path'] ) )
	{
		list( $volume, $path ) = explode( ':', $_REQUEST['path'] );
		$volume .= ':';
	}
	
	$out = array ();
	if ( $rows = $database->fetchObjectRows ( '
		SELECT * FROM
		(
			(
				SELECT ID, `Name`, "Small" AS `FType` FROM ContentDataSmall
				WHERE
					ContentID = \'' . $parent . '\' AND 
					ContentTable = "ContentElement"
			)
			UNION
			(
				SELECT ID, `Name`, "Big" AS `FType` FROM ContentDataBig
				WHERE
					ContentID = \'' . $parent . '\' AND 
					ContentTable = "ContentElement"
			)
		) z
	' ) )
	{
		foreach ( $rows as $row )
		{
			$cl = new stdclass ();
			$cl->Type = 'File';
			$cl->MetaType = 'File';
			$cl->Filename = $row->Name;
			$cl->Title = $row->Name;
			$cl->ObjectType = 'ContentData' . $row->FType;
			$cl->ObjectID = $row->ID;
			$cl->Path = $volume . ( $path ? ( $path . '/' ) : '' ) . $cl->Filename;
			$out[] = $cl;
		}
	}
	return $out;
}

// Get content
$fld = getContentByPath( $_REQUEST['path'] );
if( !$fld ) die( 'fail' );

$array = array ();

$subfolders = listContentFolders ( $fld->MainID, $_REQUEST['path'] );
$files = listContentFiles ( $fld->MainID, $_REQUEST['path'] );
$array = array_merge ( $subfolders, $files );

die ( 'ok<!--separate-->' . json_encode ( $array ) );

?>
