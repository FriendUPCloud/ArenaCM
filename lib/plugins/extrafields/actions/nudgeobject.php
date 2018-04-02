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



include_once ( "lib/plugins/extrafields/include/funcs.php" );

$conn = new dbObject ( 'ObjectConnection' );
if ( $conn->load ( $_REQUEST[ 'obj' ] ) )
{
	$objs = new dbObject ( 'ObjectConnection' );
	$objs->addClause ( 'WHERE', 'ObjectID=' . $conn->ObjectID );
	$objs->addClause ( 'WHERE', 'ObjectType="' . $conn->ObjectType . '"' );
	$objs->addClause ( 'ORDER BY', 'SortOrder ASC' );
	if ( $objs = $objs->find ( ) )
	{
		$len = count ( $objs );
			
		// Down
		if ( $_REQUEST[ 'dir' ] > 0 )
		{
			for ( $a = 0; $a < $len; $a++ )
			{
				if ( $objs[ $a ]->ID == $conn->ID && $a < $len - 1 )		// Only move object down in list if it isn't on the bottom
				{
					$tmp = $objs[ $a ];
					$objs[ $a ] = $objs[ $a + 1 ];
					$objs[ $a + 1 ] = $tmp;
					$a++;
				}
			}
		}
		// Up
		else
		{
			for ( $a = 0; $a < $len; $a++ )
			{
				if ( $objs[ $a ]->ID == $conn->ID && $a > 0 )				// Only move object up if it isn't on the top
				{
					$tmp = $objs[ $a - 1 ];
					$objs[ $a - 1 ] = $objs[ $a ];
					$objs[ $a ] = $tmp;
				}
			}
		}
		// Reorder all objects
		for ( $a = 0; $a < $len; $a++ )
		{
			$objs[ $a ]->SortOrder = $a;
			$objs[ $a ]->save ( );
		}
	}
	$out = showConnectedObjects ( $conn->ObjectID, $conn->ObjectType );
}
ob_clean ( );
die ( $out );
?>
