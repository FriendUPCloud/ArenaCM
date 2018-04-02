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



$contentid = $_REQUEST[ "contentid" ];
$contenttype = $_REQUEST[ "contenttype" ];
$offset = $_REQUEST[ "offset" ];
$id = $_REQUEST[ "vid" ];

$db =& dbObject::globalValue ( "database" );

if ( $objs = $db->fetchObjectRows ( "
	SELECT z.* FROM 
	(
		(
			SELECT b.ID, b.Name, b.Type, b.SortOrder, 'ContentDataBig' AS `DataTable`  
			FROM 
			`ContentDataBig` b 
			WHERE 
				b.ContentID='$contentid' 
			AND
				b.ContentTable=\"$contenttype\"
		)
		UNION
		(
			SELECT c.ID, c.Name, c.Type, c.SortOrder, 'ContentDataSmall' AS `DataTable` 
			FROM 
				`ContentDataSmall` c 
			WHERE 
				c.ContentID='$contentid' 
			AND
				c.ContentTable=\"$contenttype\"
		)
	) AS z
	ORDER BY 
 		z.SortOrder ASC, 
 		z.ID ASC
", MYSQL_ASSOC ) )
{
	/**
	 * Make correct ordering
	**/
	$len = count ( $objs );
		
	for ( $a = 0; $a < $len; $a++ )
	{
		if ( $offset != 1 )
		{
			if ( $objs[ $a ]->ID == $id && $a > 0 )
			{
				$tmp = $objs[ $a - 1 ]->SortOrder;
				$objs[ $a - 1 ]->SortOrder = $objs[ $a ]->SortOrder;
				$objs[ $a ]->SortOrder = $tmp;
				break;
			}
		}
		else
		{
			if ( $objs[ $a ]->ID == $id && $a < $len - 1 )
			{
				$tmp = $objs[ $a + 1 ]->SortOrder;
				$objs[ $a + 1 ]->SortOrder = $objs[ $a ]->SortOrder;
				$objs[ $a ]->SortOrder = $tmp;
				break;
			}
		}
	}
	/**
	 * Save the new order
	**/
	for ( $a = 0; $a < $len; $a++ )
	{
		$db->query ( 
			"UPDATE `" . $objs[ $a ]->DataTable . "` SET SortOrder='" . $objs[ $a ]->SortOrder . "' " . 
			"WHERE ID='" . $objs[ $a ]->ID . "'" 
		);
	}
}
die ();
?>
