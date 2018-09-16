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


$root = '..';

// TODO: Permissions?
// Quick resource gathering
if( file_exists( "$root/config" ) && is_dir( "$root/config" ) && file_exists( "$root/config/config.php" ) )
{
	include_once( "$root/config/config.php" );
}
else
{
	include_once( "$root/config.php" );
}

if( $files = explode( ',', $_REQUEST['files'] ) )
{
	$out = array();
	$clean = array();
	foreach( $files as $f )
	{
		if( !in_array( $f, $clean ) )
			$clean[] = $f;
	}
	foreach( $clean as $f )
	{
		$parts = explode( '.', $f );
		if( in_array( end( $parts ), array( 'js', 'css' ) ) )
		{
			if( $fl = file_get_contents( "$root/$f" ) )
				$out[] = $fl;
		}
	}
	$blob = implode( "\n", $out );
	
	if( defined( 'IMAGE_HOSTS' ) )
	{
		if( $hosts = explode( '|', IMAGE_HOSTS ) )
		{
			// Catch
			$blob = str_replace( BASE_URL . 'subether/upload/', '!!zubether/', $blob );
			$blob = str_replace( BASE_URL . 'upload/',          '!!zpload/', $blob );
			$blob = str_replace( 'subether/upload/',            '!!zubether/', $blob );
			$blob = str_replace( 'upload/',                     '!!zpload/', $blob );
			
			// Finally
			$blob = str_replace( '!!zpload/', $hosts[0] . 'upload/', $blob );
			$blob = str_replace( '!!zubether/', $hosts[0] . 'subether/upload/', $blob );
		}
	}
	
	die( $blob );
}
die( '/* fail */' );

?>
