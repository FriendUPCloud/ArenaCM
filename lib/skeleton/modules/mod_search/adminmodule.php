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

$moddir = 'lib/skeleton/modules/mod_search';

switch ( $_REQUEST[ 'modaction' ] )
{
	case 'save':
		$fieldObject->DataMixed = "{$_REQUEST['heading']}\t{$_REQUEST['keywords']}\t{$_REQUEST['webpage']}\t{$_REQUEST['extensions']}\t{$_REQUEST['replacefield']}\t{$_REQUEST['outputpage']}\n";
		$fieldObject->save ( );
		die ( 'ok' );
		break;
	default:
		$mtpl = new cPTemplate ( "$moddir/templates/adm_main.php" );
		
		$texts = explode ( "\n", $fieldObject->DataMixed );
		
		list ( 
			$search_heading, $search_keywords, $search_webpage, $search_extensions, 
			$search_replacefield, $search_outputpage
		) = explode ( "\t", $texts[ 0 ] );
		if ( !$search_heading ) $search_heading = 'Søk';
		if ( !$search_keywords ) $search_keywords = 'Søkeord';
		if ( !$search_webpage ) $search_webpage = 'Søk i nettsiden';
		$mtpl->search_heading = $search_heading;
		$mtpl->search_keywords = $search_keywords;
		$mtpl->search_webpage = $search_webpage;
		$mtpl->search_extensions = $search_extensions;

		// Fields
		$db =& dbObject::globalValue ( 'database' );
		$options = '<option value="">Standard</option>';
		if ( $flds = $db->fetchObjectRows ( '
			SELECT DISTINCT(Name) FROM ContentDataBig 
			UNION
			SELECT DISTINCT(Name) FROM ContentDataSmall 
		' ) )
		{
			foreach ( $flds as $fld )
			{
				$s = $fld->Name == $search_replacefield ? ' selected="selected"' : '';
				$options .= '<option value="' . $fld->Name . '"' . $s . '>' . $fld->Name . '</option>';
			}
		}
		$mtpl->search_replacefield = $options;
		$mtpl->search_outputpage = getSiteStructureOptions ( $search_outputpage, 0 );
		
		$module = $mtpl->render ( );
		break;
}
?>
