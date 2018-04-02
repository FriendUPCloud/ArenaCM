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

global $user;
$dir = $_REQUEST[ 'dir' ];

$cnt = new dbContent ( );
if ( $cnt->load ( $_REQUEST[ 'cid' ] ) )
{
	// Check that we have the correct permissions
	if ( $cnt->checkPermission ( $user, 'write', 'admin' ) )
	{
		// Load the field
		$f = new dbObject ( $_REQUEST[ 'ft' ] );
		if ( $f->load ( $_REQUEST[ 'fid' ] ) )
		{
			if ( $db =& $f->getDatabase ( ) )
			{
				if ( $fields = $db->fetchObjectRows ( '
					SELECT * FROM  
					( 
						SELECT
							ID, `Name`, "ContentDataSmall" as `Table`, SortOrder 
						FROM 
							ContentDataSmall 
						WHERE 
							ContentTable="ContentElement" AND ContentID=' . $cnt->ID . '
							
						UNION 
						
						SELECT 
							ID, `Name`, "ContentDataBig" as `Table`, SortOrder 
						FROM 
							ContentDataBig 
						WHERE ContentTable="ContentElement" AND ContentID=' . $cnt->ID . '
					) as z 
					ORDER BY SortOrder ASC, ID ASC
				' ) )
				{
					$cnt = count ( $fields );
					$so = 1;
					// Make incremental order
					for ( $a = 0; $a < $cnt; $a++ )
					{
						$curr =& $fields[$a];
						$curr->SortOrder = $so++;
						
						$o = new dbObject ( $curr->Table ); 
						$o->Load( $curr->ID );
						$o->SortOrder = $curr->SortOrder;
						$o->save();
					}
					// Do the swap
					for( $a = 0; $a < $cnt; $a++ )
					{
						$curr =& $fields[$a];
						if( $curr->ID == $_REQUEST['fid'] )
						{
							// Check bounds
							if( $dir < 0 && $a-1 < 0 ) break;
							if( $dir > 0 && $a+1 >= $cnt ) break;
							
							// Preform sortorder swap							
							$target = $curr;
							$swap   = $dir > 0 ? $fields[$a+1] : $fields[$a-1];

							$t = new dbObject( $curr->Table ); $t->load( $target->ID );
							$s = new dbObject( $swap->Table ); $s->load( $swap->ID   );
							$t->SortOrder = $swap->SortOrder; 
							$s->SortOrder = $target->SortOrder; 
							
							// Save fields
							$t->save ();
							$s->save ();
							break;
						}
					}
				}
			}
		}
	}
}

header ( 'location: admin.php?module=extensions&extension=editor&cid=' . $_REQUEST[ 'cid' ] );
die ();
?>
