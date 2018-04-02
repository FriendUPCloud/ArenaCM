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

global $document;
$url = $content->getUrl ();

// Get sorted list of folders
$folderList = explode ( ':', $settings->Folders );
$folderList = implode ( ', ', $folderList );
$db =& dbObject::globalValue ( 'database' );
$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
$document->addResource ( 'javascript', 'lib/javascript/gui.js' );
$document->addResource ( 'javascript', 'lib/javascript/gui/gallerypopup.js' );

$mstr = '';

if ( !function_exists ( 'listImages' ) )
{
	function listImages ( $pfolder, $where, $settings, $fieldid )
	{
		// Fetch images sorted by sortorder and date
		$cstr = '';
		$cols = $settings->ThumbColumns;
		if ( $settings->SortMode == 'listmode_sortorder' )
			$where = ' ORDER BY SortOrder ASC';
		else $where = ' ORDER BY DateModified DESC';
		$imgs = new dbImage ();
		
		$q = 'SELECT * FROM `Image` WHERE ImageFolder=\'' . $pfolder->ID . '\'' . $where;
		if ( $imgs = $imgs->find ( $q ) )
		{
			// Thumbs
			if ( $settings->ArchiveMode == 'archivemode_thumbs' )
			{
				$cstr .= '<table>';
				$col = 1;
				foreach ( $imgs as $im )
				{
					if ( $col == 1 ) $cstr .= '<tr>';
					$cstr .= '<td>';
					$cstr .= '<div class="FolderImage">';
					$cstr .= '<a href="' . $im->getImageUrl ( $settings->DetailWidth, $settings->DetailHeight, 'framed' ) . '">';
					$cstr .= $im->getImageHTML ( $settings->ThumbWidth, $settings->ThumbHeight, 'framed' );
					$cstr .= '</a>';
					$cstr .= '<div class="Description">' . $im->Description . '</div>';
					$cstr .= '</div>';
					$cstr .= '</td>';
					if ( $col++ >= $cols )
					{
						$col = 1; $cstr .= '</tr>';
					}
				}
				if ( $col != 1 ) $cstr .= '</tr>';
				$cstr .= '</table>';
				$cstr .= '<script type="text/javascript"> var g = new GalleryPopup ( \'' . $fieldid . '\' ); </script>';
			}
			// List
			else
			{
				$cstr .= '<table width="100%" class="List">';
				$cstr .= '<tr>';
				$cstr .= '<th class="Image">#</th>';
				$cstr .= '<th class="Title">' . i18n ( 'Title' ) . ':</th>';
				$cstr .= '<th class="Filename">' . i18n ( 'Filename' ) . ':</th>';
				$cstr .= '<th class="Filesize">' . i18n ( 'Filesize' ) . ':</th>';
				$cstr .= '<th class="Date">' . i18n ( 'Date' ) . ':</th>';
				$cstr .= '</tr>';
				
				foreach ( $imgs as $im )
				{
					$cstr .= '<tr class="sw' . ( $sw = ( $sw == 1 ? 2 : 1 ) ) . '">';
					$cstr .= '<td class="Image">';
					$cstr .= '<a href="' . $im->getImageUrl ( $settings->DetailWidth, $settings->DetailHeight, 'framed' ) . '">';
					$cstr .= $im->getImageHTML ( $settings->ThumbWidth, $settings->ThumbHeight, 'framed' );
					$cstr .= '</a>';
					$cstr .= '<div class="Description">' . $im->Description . '</div>';
					$cstr .= '</td>'; 
					$cstr .= '<td class="Title">' . $im->Title . '</td>';
					$cstr .= '<td class="Filename">' . $im->Filename . '</td>';
					$cstr .= '<td class="Filesize">' . filesizetohuman ( $im->Filesize ) . '</td>';
					$cstr .= '<td class="Date">' . ArenaDate ( DATE_FORMAT, $im->DateUpdated ) . '</td>';
					$cstr .= '</tr>';
				}
				$cstr .= '</table>';
				$cstr .= '<script type="text/javascript"> var g = new GalleryPopup ( \'' . $fieldid . '\' ); </script>';
			}
		}
		return $cstr;
	}
}

if ( isset ( $_REQUEST[ 'fid' ] ) )
{
	$pfolder = new dbObject ( 'Folder' );
	$pfolder->load ( $_REQUEST[ 'fid' ] );
	$mstr .= '<div class="Block SelectedFolder">';
	$mstr .= '<h2>' . $pfolder->Name . '</h2>';
	$mstr .= '<hr class="SelectedFolder">';
	$mstr .= listImages ( $pfolder, $whgere, $settings, $field->Name );
	$mstr .= '</div>';
	$folders = $db->fetchObjectRows ( 'SELECT * FROM `Folder` WHERE Parent=\'' . $_REQUEST[ 'fid' ] . '\' ORDER BY SortOrder ASC, Name ASC' );
}
else $folders = $db->fetchObjectRows ( 'SELECT * FROM `Folder` WHERE ID IN (' . $folderList . ') ORDER BY SortOrder ASC, Name ASC' );
if ( $folders )
{
	foreach ( $folders as $f )
	{
		$cstr = '';
		// Start FolderContainer
		$cstr .= '<div class="Block FolderContainer">';
		
		$cstr .= '<h3 class="FolderName ' . texttourl ( $f->Name ) . '"><span>' . $f->Name . '</span></h3>';
		// Get first image in folder
		$i = new dbImage ();
		$i->ImageFolder = $f->ID;
		if ( $i = $i->findSingle () )
		{
			$cstr .= '<div class="FolderPreview"><a href="' . $url . '?fid=' . $f->ID . '">' . $i->getImageHTML ( $settings->ThumbWidth, $settings->ThumbHeight, 'framed' ) . '</a></div>';
		}
		else if ( preg_match ( '/img src=\".*?\/([0-9]{1,})\/[^"]*?\"/i', $f->Description, $m ) )
		{
			$i = new dbImage ( $m[1] );
			$cstr .= '<div class="FolderPreview"><a href="' . $url . '?fid=' . $f->ID . '">' . $i->getImageHTML ( $settings->ThumbWidth, $settings->ThumbHeight, 'framed' ) . '</a></div>';
		}
		else
		{
			$cstr .= '<div class="FolderPreview"><a href="' . $url . '?fid=' . $f->ID . '"><img src="' . i18n ( 'i18n_folder_image' ) . '" width="' . $settings->ThumbHeight . '" height="' . $settings->ThumbHeight . '"/></a></div>';
		}

		if ( trim ( strip_tags ( $f->Description ) ) )
		{
			$cstr .= '<div class="FolderDescription">';
			$cstr .= $f->Description;
			$cstr .= '</div>';
		}

		// Start folder output
		$cstr .= '<div class="Block Folder">';
		
		// List out selected folder if using recursions
		// Else just list out images by folder
		if ( $settings->Recursion != '1' )
			$fid = $f->ID;
		else $fid = $_REQUEST[ 'fid' ];
		
		if ( !isset ( $_REQUEST[ 'fid' ] ) && $settings->Recursion != '1' )
			$istr = listImages ( $f, $where, $settings, $field->Name );
		
		$cstr .= $istr;
		
		// Finish folder output
		$cstr .= '</div>';
		
		// Finish folder container
		$cstr .= '</div>';
		
		$cstr .= '<hr class="FolderDivider"/>';
		
		// Don't list empty folders in nonrecursive mode
		if ( !trim ( $istr ) && $settings->Recursion != '1' )
			$cstr = '';	
		$mstr .= $cstr;
	}
}

// Parent link
if ( $_REQUEST[ 'fid' ] && $settings->Recursion >= 1 )
{
	// Directive to load parent folder or initial state
	$pfolder = new dbObject ( 'Folder' );
	if ( $pfolder->load ( $_REQUEST[ 'fid' ] ) )
	{
		if ( in_array ( $_REQUEST[ 'fid' ], explode ( ',', str_replace ( ' ', '', $folderList ) ) ) )
		{
			$fid = '';
		}
		else $fid = '?fid=' . $pfolder->Parent;
	}
	$mstr .= '<hr class="ParentFolder"/>';
	$mstr .= '<div class="Block ParentFolder">';
	$mstr .= '<a href="' . $url . $fid . '"><span>' . i18n ( 'Parent folder' ) . '</span></a>';
	$mstr .= '</div>';
}


$mtpl->folders = $mstr;

?>
