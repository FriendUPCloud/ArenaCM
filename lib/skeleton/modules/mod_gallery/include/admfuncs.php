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

function galGetImageFolder ( $settings, $field, $index = 0 )
{
	global $database;
	$folders = explode ( ':', trim ( $settings->Folders ) );
	$n = 0;
	if ( count ( $folders ) )
	{
		foreach ( $folders as $fld )
		{
			if ( $rows = $database->fetchObjectRows ( '
				SELECT * FROM Image WHERE ImageFolder=' . $fld . ' ORDER BY ' . 
					( $settings->SortMode == 'listmode_sortorder' ? ' SortOrder ASC' : ' DateModified DESC' ) . 
				'
			' ) )
			{
				foreach ( $rows as $row )
				{
					if ( $n == $index )
					{
						include_once ( 'lib/classes/dbObjects/dbImage.php' );
						$i = new dbImage ();
						if ( $i->load ( $row->ID ) )
						{
							return 'admin.php?module=library&lid=' . $i->ImageFolder;
						}
					}
					$n++;
				}
			}
		}
	}
	return 'admin.php';
}

function delImage ( $settings, $field, $index = 0 )
{
	global $database;
	$folders = explode ( ':', trim ( $settings->Folders ) );
	$n = 0;
	if ( count ( $folders ) )
	{
		foreach ( $folders as $fld )
		{
			if ( $rows = $database->fetchObjectRows ( '
				SELECT * FROM Image WHERE ImageFolder=' . $fld . ' ORDER BY ' . 
					( $settings->SortMode == 'listmode_sortorder' ? ' SortOrder ASC' : ' DateModified DESC' ) . 
				'
			' ) )
			{
				foreach ( $rows as $row )
				{
					if ( $n == $index )
					{
						include_once ( 'lib/classes/dbObjects/dbImage.php' );
						$i = new dbImage ();
						if ( $i->load ( $row->ID ) )
							$i->delete ();
						return 'ok';
					}
					$n++;
				}
			}
		}
	}
	return 'fail';
}

function saveImageText ( $settings, $field, $index = 0, $text = '' )
{
	global $database;
	$folders = explode ( ':', trim ( $settings->Folders ) );
	$n = 0;
	
	$index = $_POST[ 'index' ];
	$text = $_POST[ 'text' ];
	$link = trim ( $_POST[ 'link' ] );
	$tags = trim ( $_POST[ 'tags' ] );
	
	if ( count ( $folders ) )
	{
		foreach ( $folders as $fld )
		{
			if ( $rows = $database->fetchObjectRows ( '
				SELECT * FROM Image WHERE ImageFolder=' . $fld . ' ORDER BY ' . 
					( $settings->SortMode == 'listmode_sortorder' ? ' SortOrder ASC' : ' DateModified DESC' ) . 
				'
			' ) )
			{
				foreach ( $rows as $row )
				{
					if ( $n == $index )
					{
						include_once ( 'lib/classes/dbObjects/dbImage.php' );
						$i = new dbImage ();
						$i->load ( $row->ID );
						$dm = $i->DateModified;
						$i->Description = $text;
						$i->Tags = $tags;
						SetSetting ( 'Image', 'Link_' . $i->ID, $link );
						$i->save ();
						// keep modified date
						$database->query ( 'UPDATE Image SET DateModified = "' . $dm . '" WHERE ID=' . $i->ID );
						return 'ok';
					}
					$n++;
				}
			}
		}
	}
	return 'fail';
}

function getPreviewInfo ( $settings, $field, $index = 0 )
{
	global $database;
	$str = '';
	
	$folders = explode ( ':', trim ( $settings->Folders ) );
	$count = 0;
	
	if ( count ( $folders ) && isset ( $folders[0] ) && trim ( $folders[0] ) )
	{
		for ( $b = 0; $b <= 1; $b++ )
		{
			$n = 0;
			foreach ( $folders as $fld )
			{
				if ( $b == 0 )
				{
					$countq = 'SELECT COUNT(*) AS CNT FROM Image WHERE ImageFolder=' . $fld;
					$counto = $database->fetchObjectRow ( $countq );
					$count += $counto->CNT;
				}
				else
				{
					if ( $rows = $database->fetchObjectRows ( '
						SELECT * FROM Image WHERE ImageFolder=' . $fld . ' ORDER BY ' . 
							( $settings->SortMode == 'listmode_sortorder' ? ' SortOrder ASC' : ' DateModified DESC' ) . 
						'
					' ) )
					{
						foreach ( $rows as $row )
						{
							if ( $n == $index )
							{
								return array ( 
									'"' . $row->Title . '" (bilde ' . ($n+1) . '/' . $count . ')', 
									$count,
									$row->Description,
									$row->Tags,
									GetSettingValue ( 'Image', 'Link_' . $row->ID )
								);
							}
							$n++;
						}
					}
				}
			}
		}
	}
}
function getPreview ( $settings, $field, $index = 0 )
{
	$str = '';

	if ( !$settings->ThumbWidth ) 
		$settings->ThumbWidth = 80;
	if ( !$settings->ThumbHeight ) 
		$settings->ThumbHeight = 60;
	if ( !$settings->ThumbColumns ) 
		$settings->ThumbColumns = 4;

	$folders = explode ( ':', trim ( $settings->Folders ) );
	if ( $settings->currentMode == 'gallery' )
	{
		if ( count ( $folders ) )
		{
			foreach ( $folders as $fld )
			{
				$imgs = new dbImage ();
				$imgs->addClause ( 'WHERE', 'ImageFolder=\'' . $fld . '\'' );
				if ( $settings->SortMode == 'listmode_sortorder' )
				{
					$imgs->addClause ( 'ORDER BY', 'SortOrder ASC' );
				}
				else
				{
					$imgs->addClause ( 'ORDER BY', 'DateModified DESC' );
				}
				
				if ( $images = $imgs->find ( ) )
				{
					$i = 0;
					foreach ( $images as $image )
					{
						$str .= $image->getImageHTML ( $settings->ThumbWidth, $settings->ThumbHeight, 'framed' );
						if ( ++$i >= $settings->ThumbColumns )
						{
							$str .= '<br/>';
							$i = 0;
						}
					}
				}
			}
		}
		if ( !$str ) $str = '<p>Ingen bilder er lagt til.</p>'; 
	}
	// else slideshow
	else
	{
		if ( count ( $folders ) && is_array ( $folders ) && $folders[0] > 0 )
		{
			$i = new dbImage ( );
			$i->addClause ( 'WHERE', 'ImageFolder=\'' . $folders[0] . '\'' );
			$i->addClause ( 'ORDER BY', $settings->SortMode == 'listmode_sortorder' ?
				'SortOrder ASC' : 'DateModified DESC' );
			if ( $i = $i->find () )
			{
				$num = 0;
				foreach ( $i as $image )
				{
					if ( $num++ == $index )
					{
						$str = $image->getImageUrl ( 400, 200, 'proximity' );
					}
				}
			}
		}
		if ( !$str ) $str = 'admin/gfx/icons/error.png'; 
	}
	return $str;
}

function listFolders ( $str, $field )
{
	if ( $ids = explode ( ':', trim ( $str ) ) )
	{
		$str = '<table cellspacing="0" cellpadding="0" border="0" width="100%" class="Gui">';
		foreach ( $ids as $id )
		{
			$fld = new dbObject ( 'Folder' );
			if ( $fld->load ( $id ) )
			{
				$str .= '<tr class="sw' . ( $sw = ( $sw == 1 ? 2 : 1 ) ) . '">';
				$str .= '<td width="100%"><a href="admin.php?module=library&lid=' . $fld->ID . '">' . $fld->Name . '</a></td>';
				$str .= '<td><a onclick="agalRemoveBrick(' . $fld->ID . ', ' . $field->ID . ')" href="javascript:void(0)"><img src="admin/gfx/icons/brick_delete.png" border="0"/></a></td>';
				$str .= '</tr>';
			}
		}
		return $str . '</table>';
	}
	return '<p>Ingen mapper er lagt til.</p>';
}

function saveGalSettings ( $settings, $field )
{
	$f = new dbObject ( 'ContentDataSmall' );
	$f->load ( $field->ID );
	$f->DataMixed = CreateStringFromObject ( $settings );
	$f->save ( );
}

?>
