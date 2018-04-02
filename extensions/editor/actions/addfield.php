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

$content = new dbContent ( );
if ( $content->load ( $_REQUEST[ 'cid' ] ) )
{
	if ( $GLOBALS[ 'Session' ]->AdminUser->checkPermission ( $content, 'write', 'admin' ) )
	{
		if ( $_REQUEST[ 'IsGlobal' ] && !$GLOBALS[ 'Session' ]->AdminUser->checkPermission ( $content, 'structure', 'admin' ) )
		{
			die ( 'FAIL<!-- separate -->Du har ikke rettigheter til å endre strukturen.' );
		}
		switch ( $_REQUEST[ 'Type' ] )
		{
			case 'varchar':
				$ext = new dbObject ( 'ContentDataSmall' );
				break;
			case 'leadin':
			case 'text':
				$ext = new dbObject ( 'ContentDataBig' );
				break;
			case 'extension':
				$ext = new dbObject ( 'ContentDataSmall' );
				break;
			default:
				$ext = new dbObject ( 'ContentDataSmall' );
				break;
		}
		$db =& dbObject::globalValue ( 'database' );
		if ( $row = $db->fetchObjectRow ( 'SELECT * FROM ContentDataSmall WHERE `Name`="' . $_REQUEST[ 'Name' ] . '" AND ContentTable="ContentElement" AND ContentID=' . $content->ID ) )
		{
			die ( 'FAIL<!-- separate -->Feltet finnes. Du må finne et unikt navn for feltet.' );
		}
		if ( $row = $db->fetchObjectRow ( 'SELECT * FROM ContentDataBig WHERE `Name`="' . $_REQUEST[ 'Name' ] . '" AND ContentTable="ContentElement" AND ContentID=' . $content->ID ) )
		{
			die ( 'FAIL<!-- separate -->Feltet finnes. Du må finne et unikt navn for feltet.' );
		}
		$max1 = end( $database->fetchRow( 
			'SELECT MAX(SortOrder) FROM ContentDataSmall WHERE ContentID=\'' . 
			$content->ID . '\' AND ContentTable=\'ContentElement\'' ) ); 
		$max2 = end( $database->fetchRow( 
			'SELECT MAX(SortOrder) FROM ContentDataBig WHERE ContentID=\'' . 
			$content->ID . '\' AND ContentTable=\'ContentElement\'' ) ); 
		$max = max( $max1, $max2 );
		$ext->ContentID = $content->ID;
		$ext->ContentTable = 'ContentElement';
		$ext->IsGlobal = $_REQUEST[ 'IsGlobal' ];
		$ext->Name = safeFieldName ( $_REQUEST[ 'Name' ] );
		$ext->ContentGroup = $_REQUEST[ 'ContentGroup' ];
		$ext->SortOrder = ( isset( $_REQUEST[ 'SortOrder' ] ) && $_REQUEST['SortOrder'] > 0 ) ? $_REQUEST[ 'SortOrder' ] : $max;
		$ext->IsVisible = '1';
		$ext->Type = trim ( $_REQUEST[ 'Type' ] );
		if ( strstr ( $ext->Type, '|' ) )
		{
			list ( $type, $extension ) = explode ( '|', $ext->Type );
			$ext->Type = $type;
			$ext->DataString = $extension;
		}
		if ( $_REQUEST[ 'fieldextension' ] )
			$ext->DataString = $_REQUEST[ 'fieldextension' ];
		$ext->save ( );
		$content->DateModified = date ( 'Y-m-d H:i:s' );
		$content->save ( );
		if ( $ext->ID )
		{
			die ( 'ok<!-- separate -->Lagret.' );
		}
	}
	die ( 'FAIL<!-- separate -->Du har ikke rettigheter til å skrive til dette elementet.' );
}
die ( 'FAIL<!-- separate -->Kunne ikke laste inn innhold.' );
?>
