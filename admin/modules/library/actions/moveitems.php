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
 * move item inside lib to another folder
 */

include_once ( "lib/classes/dbObjects/dbFile.php" );
include_once ( "lib/classes/dbObjects/dbImage.php" );

ob_clean();

$_REQUEST['target'] = explode( ":", $_REQUEST['target'] );

switch( $_REQUEST['target'][0] )
{
	case 'Folder':
		$target = new dbFolder();
		break;
	default:
		die( 'Feil! Fikk ikke mappe som mål for innholdsflyttning.' );		
}

if ( !$target->load( $_REQUEST['target'][1] ) )
{
	die( 'Feil. Kunne ikke laste inn målmappen.' );
}

foreach( explode( ";", $_REQUEST['items'] ) as $item )
{
	$item = explode( ":", $item );
	
	switch( $item[0] )
	{
		case 'Image':
			$object = new dbImage();
			if ( $object->load( $item[ 1 ] ) ) 
			{
				$object->ImageFolder = $target->ID;
				$object->save ( );	
			}				
			break;
			
		case 'Folder':
			$folder = new dbObject ( 'Folder' );
			if ( $folder->load ( $item[ 1 ] ) )
			{
				moveFolderIntoFolder ( $folder, $target );
			}
			break;
		
		case 'File':
			$object = new dbFile ( );
			if( $object->load ( $item[ 1 ] ) ) 
			{
				$object->FileFolder = $target->ID;
				$object->save ();	
			}				
			break;
			
		default:
			die( 'printr ...' . print_r( $item ,1) );
			// do nothing
	}

}

	
die( 'Flyttet biblioteksinnhold.' );

function moveFolderIntoFolder ( $folder, $target )
{
	if ( $folder->ID == $target->ID ) return false;
	$test = $target->clone ( );
	while ( $test->Parent != 0 )
	{
		$test->load ( $test->Parent );
		// ouch, we're moving into a child of self!
		if ( $test->ID == $folder->ID ) return false;
	}
	$folder->Parent = $target->ID;
	$folder->save ( );
	return true;
}

?>
