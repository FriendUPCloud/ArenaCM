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

$tpl = new cPTemplate( 'admin/modules/library/templates/editimage.php' );

if ( intval( $_REQUEST[ 'imageID' ] ) > 0 )
{
	if ( $image = new dbImage ( $_REQUEST[ 'imageID' ] ) )
	{
		$tpl->imageHTML = $image->getImageHTML( 180,180, 'proximity' );
		$tpl->image =& $image;
		$tpl->folderID = $image->ImageFolder;
	}
}
else if ( intval( $_REQUEST[ 'folderID' ] ) > 0 )
{
	$tpl->folderID = intval( $_REQUEST[ 'folderID' ] );	
}

$db =& dbObject::globalValue ( 'database' );
$content = $db->fetchObjectRow ( 'SELECT * FROM Folder WHERE ID=' . $tpl->folderID, MYSQL_ASSOC );
$content->_primaryKey = 'ID';
$content->_tableName = 'Folder';
$content->_isLoaded = true;
$tpl->edit = $Session->AdminUser->checkPermission ( $content, 'Write', 'admin' );

ob_clean();
die( $tpl->render() );

?>
