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


if( strstr( $pathHere = $_POST['path'], ':Content' ) )
{
	if( strstr( $pathHere, '/' ) )
	{
		$pathHere = explode( '/', $pathHere );
		$field = array_pop( $pathHere );
		$pathHere = implode( '/', $pathHere );
	}
	else
	{
		$pathHere = explode( ':', $_pathHere );
		$field = $pathHere[1];
		$pathHere = $pathHere[0] . ':';
	}
	$content = GetContentByPath( $pathHere );
	
	// TODO: Handle different modules!
	if( $row = $database->fetchObjectRow( '
		SELECT * FROM (
			SELECT DataMixed TEXT, ID, "ContentDataSmall" AS `Field` FROM ContentDataSmall WHERE `Name` = "' . $field . '" AND ContentID=\'' . $content->ID . '\'
			UNION
			SELECT DataText TEXT, ID, "ContentDataBig" AS `Field` FROM ContentDataBig WHERE `Name` = "' . $field . '" AND ContentID=\'' . $content->ID . '\'
		) z
		LIMIT 1
	' ) )
	{
		$f = new dbObject( $row->Field );
		if( $f->Load( $row->ID ) )
		{
			if( $f->Field == 'ContentDataSmall' )
			{
				$f->DataMixed = $_POST['content'];
			}
			else $f->DataText = $_POST['content'];
			$f->Save();
			die( 'ok' );
		}
	}
}

die( 'fail<!--separate-->' );

?>
