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

ob_clean ( );

// Some funcs to get CLEAN structures.. :)
function GetCleanDataBig()
{
	$obj = new dbObject('ContentDataBig');
	$obj->ID = 0;
	$obj->DataText = '';
	$obj->SortOrder = 0;
	$obj->IsVisible = 0;  
	$obj->AdminVisibility = 0;
	$obj->ContentGroup = '';
	$obj->IsGlobal = 0;
	// 
	$obj->ContentID = $_REQUEST['contentid'];
	$obj->Name = $_REQUEST['name'];
	$obj->Type = $_REQUEST['type'];
	$obj->ContentTable = $_REQUEST['table'];
	return $obj;
}
function GetCleanDataSmall()
{
	$obj = new dbObject('ContentDataSmall');
	$obj->ID = 0;
	$obj->DataString = '';
	$obj->DataMixed = '';
	$obj->DataInt = 0;
	$obj->DataDouble = 0.0; 
	$obj->DataText = '';
	$obj->SortOrder = 0;
	$obj->IsVisible = 0;  
	$obj->AdminVisibility = 0;
	$obj->ContentGroup = '';
	$obj->IsGlobal = 0;
	// 
	$obj->ContentID = $_REQUEST['contentid'];
	$obj->Name = $_REQUEST['name'];
	$obj->Type = $_REQUEST['type'];
	$obj->ContentTable = $_REQUEST['table'];
	return $obj;
}

// Some required fields
$requires = array( 'contentid', 'table', 'type', 'name' );
foreach( $requires as $req ) if( !isset( $_REQUEST[$req] ) ) die( '500' );
	
// Get db
$db =& dbObject::globalValue('database');

list ( $maxs, ) = $db->fetchRow ( '
	SELECT MAX(SortOrder) 
	FROM ContentDataSmall 
	WHERE 
		ContentID=\'' . $_REQUEST['contentid'] . '\' AND 
		ContentTable="' . $_REQUEST['table'] . '"
' );

list ( $maxb, ) = $db->fetchRow ( '
	SELECT MAX(SortOrder) 
	FROM ContentDataBig 
	WHERE 
		ContentID=\'' . $_REQUEST['contentid'] . '\' AND 
		ContentTable="' . $_REQUEST['table'] . '"
' );

// Get a higher sort order..
$max = max( array( $maxs, $maxb ) ) + 1; 

// Check types
switch ( $_REQUEST['type'] )
{
	case 'text':
		$obj = GetCleanDataBig();
		$obj->SortOrder = $max;
		$obj->save ( );
		break;
	case 'leadin':
		$obj = GetCleanDataBig();
		$obj->SortOrder = $max;
		$obj->save ( );
		break;
	// Will use datastring for short text
	case 'formprocessor':
		$obj = GetCleanDataBig();
		$obj->SortOrder = $max;
		$obj->save ( );
		break;
	// Will use datastring for short text
	case 'varchar':
		$obj = GetCleanDataSmall();
		$obj->SortOrder = $max;
		$obj->save ( );
		break;
	// Will use datastring for short text
	case 'extension':
		$obj = GetCleanDataSmall();
		$obj->SortOrder = $max;
		$obj->save ( );
		break;
	// Will use dataint for resulting image
	case 'image':
	// Will use dataint for resulting file
	case 'file':
		$obj = GetCleanDataSmall();
		$obj->SortOrder = $max;
		$obj->save ( );
		break;
	// Will use object connections to this one
	case 'objectconnection':
		$obj = GetCleanDataSmall();
		$obj->DataInt = $_REQUEST['contentid'];
		$obj->SortOrder = $max;
		$obj->save ( );
		break;
	// Will use DataInt for the page root of the listing
	case 'pagelisting':
		$obj = GetCleanDataSmall();
		$obj->SortOrder = $max;
		$obj->save ( );
		break;
	case 'newscategory':
		$obj = GetCleanDataSmall();
		$obj->SortOrder = $max;
		$obj->save ( );
		break;
	// Styles and scripts
	case 'style':		
	case 'script':
		$obj = GetCleanDataSmall();
		$obj->SortOrder = $max;
		$obj->save ( );
		break;
	default:
		break;
}
die( 'ok' );
?>
