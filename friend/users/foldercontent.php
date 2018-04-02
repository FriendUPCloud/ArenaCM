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


$pus = new dbObject ( 'Users' );
$array = array ();

if ( $pus = $pus->find ( 'SELECT * FROM Users WHERE !IsDisabled AND `Name` != ""' ) )
{
	$dupTest = [];
	foreach ( $pus as $pu )
	{
		$fname = $pu->Name . '.json';
		
		if( in_array( $fname, $dupTest ) )
			continue;
			
		$f = new stdclass ();
		$f->Type = 'File';
		$f->MetaType = 'File';
		$f->Filename = $fname;
		$f->Path = $_REQUEST['path'];
		$array[] = $f;
		$dupTest[] = $fname;
	}
}
die ( 'ok<!--separate-->' . json_encode ( $array ) );

?>
