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
 * Script imports a CSV file to the users database
**/

$rowOrder = Array ( );
	
// Setup order
$fieldOrder = Array ( );
foreach ( $_POST as $k=>$v )
{
	if ( !$v ) continue;
	if ( substr ( $k, 0, 6 ) == 'field_' )
	{
		list ( ,$name ) = explode ( '_', $k );
		if ( !$name ) continue;
		foreach ( $_POST as $k2=>$v2 )
		{
			if ( !$k2 ) continue;
			if ( substr ( $k2, 0, 6 ) == 'index_' && strstr ( $k2, $name ) )
			{
				$fieldOrder[ ] = str_pad ( $v2, 4, '0', STR_PAD_LEFT ) . '_' . $name;
			}
		}
	}
}
sort ( $fieldOrder );
foreach ( $fieldOrder as $k=>$v )
{
	list ( ,$fieldOrder[ $k ] ) = explode ( '_', $v );
}

$logFile = Array ( );

$g = false;
if ( !is_array ( $_REQUEST[ 'groupid' ] ) )
{
	$g = new dbObject ( 'Groups' );
	$g->load ( $_REQUEST[ 'groupid' ] );
}
else
{
	$g = Array ( );
	foreach ( $_REQUEST[ 'groupid' ] as $gid )
	{
		$gr = new dbObject ( 'Groups' );
		$gr->load ( $gid );
		$g[] = $gr;
	}
}

if ( $fptr = fopen ( $_FILES[ 'filestream' ][ 'tmp_name' ], 'r' ) )
{
	$data = fread ( $fptr, filesize ( $_FILES[ 'filestream' ][ 'tmp_name' ] ) );
	fclose ( $fptr );
	$data = str_replace ( "\r", '', $data );
	$data = explode ( "\n", $data );
	$len = count ( $data );
	// Number 1 is our header
	for ( $a = 1; $a < $len; $a++ )
	{
		$u = new dbUser ( );
		if ( $_REQUEST[ 'Separator' ] )
			$line = explode ( $_REQUEST[ 'Separator' ], $data[ $a ] );
		else
		{
			if ( strstr ( $line, ';' ) )
				$line = explode ( ';', $data[ $a ] );
			if ( strstr ( $line, "\t" ) )
				$line = explode ( "\t", $data[ $a ] );
			else $line = explode ( ',', $data[ $a ] );
		}
		$clen = count ( $line );
		for ( $b = 0; $b < $clen; $b++ )
		{
			if ( !trim ( $line[ $b ] ) ) continue;
			if ( strstr ( $line[ $b ], '"' ) ) $line[ $b ] = trim ( str_replace ( '"', '', $line[ $b ] ) );
			if ( $fieldOrder[$b] )
				$u->{$fieldOrder[$b]} = $line[ $b ];
		}
		if ( $u->Password_Plain )
			$u->Passowrd = md5 ( $u->Password_Plain );
		if ( $_POST[ 'GeneratePassword' ] )
			$u->makePassword ( );
		if ( $_POST[ 'UseLogfile' ] )
			$logFile[] = 'Generated user ' . $u->Username . ' with password ' . $u->Password_unhash . ' (realname: ' . $u->Name . ')';
		$u->Save ( );
		
		// Insert user in multiple groups
		if ( is_array ( $g ) )
		{
			foreach ( $g as $gr )
			{
				if ( $gr->ID > 0 )
				{
					$gobj = new dbObject ( 'UsersGroups' );
					$gobj->UserID = $u->ID;
					$gobj->GroupID = $gr->ID;
					$gobj->save ( );
				}
			}
		}
		// Insert user in one group
		else if ( $g->ID > 0 )
		{
			$gobj = new dbObject ( 'UsersGroups' );
			$gobj->UserID = $u->ID;
			$gobj->GroupID = $g->ID;
		}
		
		unset ( $u );
	}
}
unset ( $data );

if ( $_POST[ 'Email' ] && count ( $logFile ) )
{
	mail ( $_POST[ 'Email' ], 'Loggfil', implode ( "\n", $logFile ), 'Content-type: text/plain; charset=UTF-8' . "\n" . 'From: no-reply@blest.no' );
}
header ( 'Location: admin.php?module=users' );
?>
