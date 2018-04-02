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



global $Session;

include_once( 'lib/classes/dbObjects/dbFolder.php' );
include_once( 'admin/modules/library/include/functions_levels.php' );

switch( $_REQUEST[ 'step' ] )
{
	case '2':
		// do finally delete... =============================================================================================
		
		$root = dbFolder::getRootFolder ( );
		
		// load folder...... ==================================================================
		$f = new dbFolder();
		$f->ID = $_REQUEST[ 'lid' ];
		if( $f->load() )
		{
			
			$Session->Set ( 'LibraryCurrentLevel', $f->Parent );
			
			// delete or move content =========================================================
			if( $_REQUEST[ 'libraryMoveContent' ] == 'move' )
			{
				$db = &$GLOBALS[ 'database' ];
				
				$sql = 'UPDATE Folder SET Parent = ' . ( intval( $_REQUEST[ 'newcontentfolder' ] ) > 0 ? intval( $_REQUEST[ 'newcontentfolder' ] ) : $root->ID ) . ' WHERE Parent = ' . $f->ID;		
				$db->query( $sql );

				$sql = 'UPDATE File SET FileFolder = ' . ( intval( $_REQUEST[ 'newcontentfolder' ] ) > 0 ? intval( $_REQUEST[ 'newcontentfolder' ] ) : $root->ID ) . ' WHERE FileFolder = ' . $f->ID;		
				$db->query( $sql );

				$sql = 'UPDATE Image SET ImageFolder = ' . ( intval( $_REQUEST[ 'newcontentfolder' ] ) > 0 ? intval( $_REQUEST[ 'newcontentfolder' ] ) : $root->ID ) . ' WHERE ImageFolder = ' . $f->ID;		
				$db->query( $sql );
			
			}
			
			// delete our contents ============================================================
			$f->delete();
			

			// reget listing =================================================
			$levels = generateLevelTree( $root, $Session->LibraryCurrentLevel );
			
			ob_clean();
			die( $levels );
			
						
		}
		
		// reget listing =================================================
		$root = dbFolder::getRootFolder ( );
		$levels = generateLevelTree( $root, $Session->LibraryCurrentLevel );
			
		ob_clean();
		die( $levels . '<!--SEPARATE-->' . $Session->LibraryCurrentLevel );
		
		
		break;
	
	case '1':
	default:
		// show form to confirm deletion ====================================================================================
		
		ob_clean();
		
		$tpl = new cPTemplate( 'admin/modules/library/templates/deletelevel.php' );
		
		$f = new dbFolder();
		$f->ID = intval( $_REQUEST[ 'lid' ] );
		
		$root = dbFolder::getRootFolder ( );
		$tpl->otherfolders = generateLevelOptions( $root, false, $f->ID );
		
		if( $f->load() )
			$tpl->folder = &$f;
		else
			$tpl->folder = false;
		
		die( $tpl->render() );
		
} // end master delete switch

?>
