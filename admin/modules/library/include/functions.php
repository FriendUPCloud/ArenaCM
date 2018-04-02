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

function saveLibraryTags ( $tags, $type )
{
	if ( $tags = explode ( ',', $tags ) )
	{
		foreach ( $tags as $tag )
		{
			if ( !trim ( $tag ) ) continue;
			$obj = new dbObject ( 'ElementTag' );
			$obj->Name = trim ( $tag );
			$obj->Type = trim ( $type );
			$obj->load ();
			$obj->DateUpdated = date ( 'Y-m-d H:i:s' );
			$obj->save ();
		}
		return true;
	}
	return false;
}

function getTags ()
{
	global $userbase;
	$str = '';
	if ( $rows = $userbase->fetchObjectRow ( 'SELECT COUNT(*) FROM ElementTag WHERE `Type` = "Image" OR `Type` = "File"' ) )
	{
		if ( $tags = $userbase->fetchObjectRows ( 'SELECT * FROM ElementTag WHERE `Type` = "Image" OR `Type` = "File" ORDER BY Popularity DESC, Name ASC LIMIT 16' ) )
		{
			foreach ( $tags as $tag )
			{
				$str .= '<button type="button" class="Tag" onclick="getByTag(\''.$tag->Name.'\')">' . $tag->Name . '</button>';
			}
		}
	}
	return $str;
}

?>
