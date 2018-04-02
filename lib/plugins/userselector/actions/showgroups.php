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



$pos = $_REQUEST[ 'pos' ];
if ( $pos <= 0 ) $pos = '0';
$limit = 5;
$db =& dbObject::globalValue ( 'database' );

if ( $keywords = explode ( ' ', str_replace ( ',', ' ', $_REQUEST[ 'keywords' ] ) ) )
{
	foreach ( $keywords as $k=>$v )
	{
		if ( trim ( $v ) == 'undefined' ) continue;
		if ( !trim ( $v ) ) continue;
		$keywords[ $k ] = '( Name LIKE "%' . trim ( $v ) . '%" OR Description LIKE "%' . trim ( $v ) . '%" )';
	}
	$wheres = implode ( ' OR ', $keywords );
	if ( trim ( $wheres ) == 'undefined' ) $wheres = '';
	else $wheres = ' WHERE ' . $wheres;
}
else $wheres = '';
if ( strtolower ( trim ( $wheres ) ) == 'where' ) $wheres = '';

list ( $count ) = $db->fetchRow ( 'SELECT COUNT(*) AS CNT FROM Groups' . $wheres );

$query = 'SELECT * FROM Groups ' . $wheres . ' ORDER BY Name LIMIT ' . $pos . ', ' . $limit;

if ( $rows = $db->fetchObjectRows ( $query ) )
{
	$i = 0;
	$p = $pos;
	foreach ( $rows as $row )
	{
		if ( $i > 0 ) $ostr .= '<div class="SpacerSmall"></div>';
		$ostr .= '<div class="Container GroupRow" onselectstart="return false" onmousedown="dragger.startDrag ( this, { pickup: \'clone\', objectType: \'Groups\', objectID: \'' . $row->ID . '\' } ); return false" style="cursor: hand; cursor: pointer; overflow: hidden; padding: 4px; white-space: nowrap">';
		$ostr .= $row->Name;
		$ostr .= '</div>';
		$i++;
		$p++;
	}
	
	if ( $p <= ( $count - 1 ) || $pos > 0 )
	{
		$ostr .= '<div class="SpacerSmall"></div>';
		if ( $p <= ( $count - 1 ) )
		{
			$ostr .= '<button type="button" class="Small" onclick="loadGroupList ( document.getElementById ( \'userskeywords\' ).value, \'' . ( $pos + $limit ) . '\' )">Neste side <img src="admin/gfx/icons/arrow_right.png"/></button>';
		}
		if ( $pos > 0 )
		{
			$ostr .= '<button type="button" class="Small" onclick="loadGroupList ( document.getElementById ( \'userskeywords\' ).value, \'' . ( $pos - $limit ) . '\' )"><img src="admin/gfx/icons/arrow_left.png"/> Forrige side</button>';
		}
	}
	ob_clean ( );
	die ( $ostr );
}
die ( '<p>Ingen grupper å liste ut.</p>' );
?>
