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
			die ( 'FAIL<!-- separate -->Du har ikke rettigheter til å endre strukturen.' );
	
		$fld = new dbObject ( $_REQUEST[ 'ft' ] );
		if ( $fld->load ( $_REQUEST[ 'fid' ] ) )
		{
			$db =& $fld->getDatabase ( );				
			if ( $row = $db->fetchObjectRow ( 'SELECT * FROM ContentDataSmall WHERE `Name`="' . $_REQUEST[ 'Name' ] . '" AND ID != ' . $fld->ID . ' AND ContentTable="ContentElement" AND ContentID=' . $content->ID ) )
				die ( 'FAIL<!-- separate -->Feltet finnes. Du må finne et unikt navn for feltet.' );
			if ( $row = $db->fetchObjectRow ( 'SELECT * FROM ContentDataBig WHERE `Name`="' . $_REQUEST[ 'Name' ] . '" AND ID != ' . $fld->ID . ' AND ContentTable="ContentElement" AND ContentID=' . $content->ID ) )
				die ( 'FAIL<!-- separate -->Feltet finnes. Du må finne et unikt navn for feltet.' );
				
			// Check if we need to make a new field
			if ( $_REQUEST[ 'Type' ] )
			{
				$destTable = '';
				switch ( $_REQUEST[ 'Type' ] )
				{
					case 'script':
					case 'objectconnection':
					case 'style':
					case 'whitespace':
					case 'varchar':
						$destTable = 'ContentDataSmall';
						break;
					case 'leadin':
					case 'text':
						$destTable = 'ContentDataBig';
						break;
					case 'extension':
						$destTable = 'ContentDataSmall';
						break;
					default:
						die ( 'FAIL<!-- separate -->Ujent type!' );
				}
				// Need a new field				
				if ( $destTable != $_REQUEST[ 'ft' ] )
				{
					$newField = new dbObject ( $destTable );
					// Try to keep data
					if ( $_REQUEST[ 'ft' ] == 'ContentDataSmall' )
						$newField->DataText = $fld->DataString;
					else $newField->DataString = str_replace ( Array ( "\n", "\r" ), '', $fld->DataText );
					$newField->ContentID = $fld->ContentID;
					$newField->ContentTable = 'ContentElement';
					$newField->SortOrder = $fld->SortOrder;
					$newField->Name = safeFieldName ( $_REQUEST[ 'Name' ] );
					$newField->Type = $_REQUEST[ 'Type' ];
					$newField->IsGlobal = $_REQUEST[ 'IsGlobal' ];
					$newField->IsVisible = '1';
					$newField->ContentGroup = $_REQUEST[ 'ContentGroup' ];
					$newField->AdminVisibility = $_REQUEST[ 'adminvisibility' ];
					if ( $_REQUEST[ 'fieldextension' ] )
						$newField->DataString = $_REQUEST[ 'fieldextension' ];
					$newField->SortOrder = $fld->SortOrder;
					if ( $newField->save ( ) )
					{
						$fld->delete ();
						die ( 'ok<!-- separate -->Feltet er endret.' );
					}
					else die ( 'fail<!-- separate -->Feltet ble ikke lagret!' );
				}
			}
			// Just update the field
			$fld->Name = safeFieldName ( $_REQUEST[ 'Name' ] );
			$fld->IsGlobal = $_REQUEST[ 'IsGlobal' ];
			$fld->SortOrder = $_REQUEST[ 'SortOrder' ];
			$fld->ContentGroup = $_REQUEST[ 'ContentGroup' ];
			$fld->IsVisible = '1';
			$fld->AdminVisibility = $_REQUEST[ 'adminvisibility' ];
			// Convert safe types
			if ( $_REQUEST[ 'Type' ] )
				$fld->Type = $_REQUEST[ 'Type' ];
			if ( $_REQUEST[ 'fieldextension' ] )
				$fld->DataString = $_REQUEST[ 'fieldextension' ];
			$fld->save ( );
			$content->DateModified = date ( 'Y-m-d H:i:s' );
			$content->save ( );
			die ( 'ok<!-- separate -->Lagret.' );
		}
		die ( 'FAIL<!-- separate -->Kunne ikke laste inn feltet.' );
	}
	die ( 'FAIL<!-- separate -->Du har ikke rettigheter til å skrive til dette elementet.' );
}
die ( 'FAIL<!-- separate -->Kunne ikke laste inn innhold.' );
?>
