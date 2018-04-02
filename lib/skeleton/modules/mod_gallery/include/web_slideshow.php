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


	global $database;
	
	// Consider template -----
	switch ( $settings->ShowStyle )
	{
		case 'showstyle_showroom':
			$mtpl = new cPTemplate ( $mtpldir . 'web_showroom.php' );
			$mtpl->settings = $settings;
			$document->addResource ( 
				'stylesheet', 
				'lib/skeleton/modules/mod_gallery/css/slideshow.css'
			);
			break;
		default: 
			$mtpl = new cPTemplate ( $mtpldir . 'web_main.php' );
			break;
	}
	
	$mtpl->images = '';
	if ( $folders = explode ( ':', $settings->Folders ) )
	{
		$str = '';
		$flds = array ();
		$queries = array ();
		foreach ( $folders as $fid )
		{
			$queries[] =
					'SELECT ID FROM Folder WHERE ID=' . $fid . ' OR Parent=' . $fid . '
					UNION
					SELECT s.ID FROM Folder p, Folder s 
					WHERE p.Parent = ' . $fid . ' AND s.Parent = p.ID
					UNION
					SELECT ss.ID FROM Folder p, Folder s, Folder ss 
					WHERE p.Parent = ' . $fid . ' AND s.Parent = p.ID AND ss.Parent = s.ID
					UNION
					SELECT sss.ID FROM Folder p, Folder s, Folder ss, Folder sss 
					WHERE p.Parent = ' . $fid . ' AND s.Parent = p.ID AND ss.Parent = s.ID AND
					sss.Parent = ss.ID'
			;
		}
		$query = '
		SELECT DISTINCT(ID) FROM Folder WHERE ID IN
		(
			' . implode ( '
					UNION
					', $queries ) . '
		)
		';
		$img = new dbImage ();
		if ( $settings->SortMode == 'listmode_sortorder' )
		{
			$img->addClause ( 'ORDER BY', 'SortOrder ASC' );
		}
		else if ( $settings->SortMode == 'listmode_fromto' )
		{
			$img->addClause ( 'WHERE', 'DateFrom <= NOW() AND DateTo >= NOW()' );
			$img->addClause ( 'ORDER BY', 'SortOrder ASC' );
		}
		else
		{
			$img->addClause ( 'ORDER BY', 'DateModified DESC' );
		}

		$img->addClause ( 'WHERE', 'ImageFolder IN ( ' . $query . ' )' );
		
		if ( $images = $img->find ( ) )
		{
			foreach ( $images as $i )
			{
				if ( $settings->ShowStyle == 'showstyle_showroom' )
				{
					$str .= '<span title="' . $i->getImageUrl ( $settings->Width, $settings->Height, 'framed' ) . '" tags="' . $i->Tags . '" description="' . trim ( str_replace ( array ( '"', "\n" ), array ( '&quot;', "<br/>" ), $i->Title ) ) . '" extended="' . trim ( str_replace ( array ( '"', "\n" ), array ( '&quot;', "<br/>" ), $i->Description ) ) . '"></span>';
					if ( $l = GetSettingValue ( 'Image', 'Link_' . $i->ID ) )
					{
						$l = str_replace ( array ( "\n", "'" ), '', $l );
						$str = str_replace ( '<span', '<span onclick="document.location=\'' . $l . '\'"', $str );
					}
				}
				else
				{
					$i->Description = str_replace ( array ( '#--quote--#', '"' ), array ( '', '#--quote--#' ), $i->Description );
					$str .= '<img src="';
					$str .= $i->getImageUrl ( $settings->Width, $settings->Height, 'framed' );
					$str .= '" alt="' . str_replace ( array ( "\\n", "\\r" ), array ( "<br/>", "" ), $i->Description ) . '" title="' . $i->Title . '"/>';
					if ( $l = GetSettingValue ( 'Image', 'Link_' . $i->ID ) )
					{
						$l = str_replace ( array ( "\n", "'" ), '', $l );
						$str = str_replace ( '<img', '<img onclick="document.location=\'' . $l . '\'"', $str );
					}
				}
			}
		}
		$mtpl->images = $str;
	}
?>
