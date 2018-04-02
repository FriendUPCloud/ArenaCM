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

include_once ( "lib/classes/dbObjects/dbFolder.php" );
include_once ( "lib/classes/dbObjects/dbImage.php" );
include_once ( "lib/classes/dbObjects/dbFile.php" );
include_once ( "lib/classes/time/ctime.php" );

$time = new cTime ( );

$keys = trim ( $_REQUEST[ 'libSearchKeywords' ] );

if( $keys == '' ) die( '<div class="Info">Du har ikke oppgitt noen søkeord.</div>' );

$keys = explode ( ",", str_replace ( " ", ",", $keys ) );
$out = array ();
$out2 = array();

$contents = '';
$folders = '';

foreach ( $keys as $k )
{
	$out[] = "( Filename LIKE \"%$k%\" OR Title LIKE \"%$k%\" OR Description LIKE \"%$k%\" )";
	$out2[] = "( Name LIKE \"%$k%\" OR Description LIKE \"%$k%\" )";
}


$db =& dbObject::globalValue ( "database" );
$sql = "
	SELECT * FROM 
	(
		(
			SELECT ID, Title, \"Image\" as `Type` FROM Image WHERE ( " . implode ( " OR ", $out ) . " )
		) 
		UNION
		(
			SELECT ID, Title, \"File\" as `Type` FROM File WHERE ( " . implode ( " OR ", $out ) . " )
		)" .
		" UNION" .
		"(" .
		"	SELECT ID, Name as `Title`, \"Folder\" as `Type` FROM Folder WHERE ( " . implode ( " OR ", $out2 ) . " )" .
		")" .
		"
	) AS res
	ORDER BY res.Title
";

if ( $rows = $db->fetchObjectRows ( $sql ) )
{
	
	$itpl = new cPTemplate( 'admin/modules/library/templates/listed_image.php' );
	$ftpl = new cPTemplate( 'admin/modules/library/templates/listed_file.php' );
	
	foreach ( $rows as $row )
	{
		if ( $row->Type == 'Image' )
		{
			$image = new dbImage ( );
			$image->load ( $row->ID );
			$itpl->image = &$image;
			$fld = $db->fetchObjectRow ( 'SELECT * FROM Folder WHERE ID=' . $image->ImageFolder );
			$fld->_primaryKey = 'ID';
			$fld->_tableName = 'Folder';
			$fld->_isLoaded = true;
			$access = $Session->AdminUser->checkPermission ( $fld, 'Read', 'admin' );
			if ( $access )
				$contents .= $itpl->render();
		}
		else if( $row->Type == 'File' )
		{
			$file = new dbFile ( );
			$file->load ( $row->ID );
			$ftpl->tfile = &$file;
			$fld = $db->fetchObjectRow ( 'SELECT * FROM Folder WHERE ID=' . $file->FileFolder );
			$fld->_primaryKey = 'ID';
			$fld->_tableName = 'Folder';
			$fld->_isLoaded = true;
			$access = $Session->AdminUser->checkPermission ( $fld, 'Read', 'admin' );
			if ( $access )
				$contents .= $ftpl->render();
		}
		else if( $row->Type == 'Folder' )
		{
			$row->_primaryKey = 'ID';
			$row->_tableName = 'Folder';
			$row->_isLoaded = true;
			$access = $Session->AdminUser->checkPermission ( $row, 'Read', 'admin' );
			if ( $access )
				$folders.= '<div class="SubContainer"><a href="javascript:setLibraryLevel( ' . $row->ID . ' )">'.$row->Title.'</a></div>';
		}
	}
}

if( $folders ) $folders = '' . $folders . '';

$oStr = $folders .  '<!--SEPERATOR-->' . $contents;

if ( !$oStr ) 
{
	$oStr = '<div class="Info"><small>' . i18n( 'No files or images matched your query.' ) . ' <br /><br /></div>';
}

if ( isset ( $do_not_die ) )
{
	$searchresults =& $oStr;
}
else die ( $oStr );

?>
