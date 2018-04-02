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

require_once( 'lib/classes/dbObjects/dbFile.php' );

$tpl = new cPTemplate( 'admin/modules/library/templates/editfile.php' );

if( intval( $_REQUEST[ 'fileID' ] ) > 0)
{
	$file = new dbFile();
	$file->load( $_REQUEST[ 'fileID' ] );
	$tpl->file = &$file;
	$tpl->folderID = $file->FileFolder;
}
else if( intval( $_REQUEST[ 'folderID' ] ) > 0 )
{
	$tpl->folderID = intval( $_REQUEST[ 'folderID' ] );	
}

$db =& dbObject::globalValue ( 'database' );
$content = $db->fetchObjectRow ( 'SELECT * FROM Folder WHERE ID=' . $tpl->folderID, MYSQL_ASSOC );
$content->_primaryKey = 'ID';
$content->_tableName = 'Folder';
$content->_isLoaded = true;
$tpl->edit = $Session->AdminUser->checkPermission ( $content, 'Write', 'admin' );

if ( $file->ID )
{
	list ( , $ext ) = explode ( '.', $file->Filename );
	switch ( strtolower ( $ext ) )
	{
		case 'swf':
			$hasWidth = false;
			$hasHeight = false;
			$hasDivID = false;
			$hasBackground = false;
			$hasVariables = false;
			foreach ( $file->_table->getFieldNames ( ) as $field )
			{
				if ( $field == 'Width' ) $hasWidth = true;
				if ( $field == 'Height' ) $hasHeight = true;
				if ( $field == 'DivID' ) $hasDivID = true;
				if ( $field == 'Background' ) $hasBackground = true;
				if ( $field == 'Variables' ) $hasVariables = true;
			}
			if ( !$hasWidth ) $db->query ( 'ALTER TABLE `File` ADD `Width` int(11) default \'0\'' );
			if ( !$hasHeight ) $db->query ( 'ALTER TABLE `File` ADD `Height` int(11) default \'0\'' );
			if ( !$hasDivID ) $db->query ( 'ALTER TABLE `File` ADD `DivID` varchar(255) default ""' );
			if ( !$hasBackground ) $db->query ( 'ALTER TABLE `File` ADD `Background` varchar(255) default ""' );
			if ( !$hasVariables ) $db->query ( 'ALTER TABLE `File` ADD `Variables` varchar(255) default ""' );
			break;
		default:
			break;
	}
}

ob_clean();
die( $tpl->render() );

?>
