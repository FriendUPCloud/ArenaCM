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

// Add the extrafields script
$document->addResource ( 'javascript', 'lib/plugins/extrafields/javascript/main.js' );

// Render one field
function renderExtraField ( $field, $obj )
{
	$extdir = 'extensions/editor';
	$str = '';
	$tpl = new cPTemplate ();
	switch ( $field->Type )
	{
		case 'contentmodule':
			$tpl->load ( $extdir . '/templates/ext/contentmodule.php' );
			if ( file_exists ( 'lib/skeleton/modules/' . $field->DataString . '/adminmodule.php' ) )
			{
				$content =& $obj;
				$fieldObject = new dbObject ( "ContentData{$field->DataTable}" );
				if ( $fieldObject->load ( $field->ID ) )
				{
					$dataTable = $field->DataTable;
					$field =& $fieldObject;
					$field->DataTable = $dataTable;
				}
				require ( 'lib/skeleton/modules/' . $field->DataString . '/adminmodule.php' );
				$tpl->gui = $module;
				$dataField = 'DataString';
			}
			else $tpl->gui = '<p>Modulen finnes ikke. Vennligst kontakt Blest Interaktiv for mer informasjon.</p>';
			break;
		case 'text':
			$tpl->load ( $extdir . '/templates/ext/text.php' );
			$dataField = 'DataText';
			break;
		case 'script':
			$tpl->load ( $extdir . '/templates/ext/javascript.php' );
			$dataField = 'DataString';
			break;
		case 'style':
			$tpl->load ( $extdir . '/templates/ext/stylesheet.php' );
			$dataField = 'DataString';
			break;
		case 'image':
			$tpl->load ( $extdir . '/templates/ext/image.php' );
			$dataField = 'DataInt';
			break;
		case 'leadin':
			$tpl->load ( $extdir . '/templates/ext/leadin.php' );
			$dataField = 'DataText';
			break;
		case 'newscategory':
			$tpl->load ( $extdir . '/templates/ext/newscategory.php' );
			$dataField = 'DataInt';
			break;
		case 'objectconnection':
			$tpl->load ( $extdir . '/templates/ext/objectconnection.php' );
			$dataField = 'DataInt';
			break;
		case 'varchar':
			$tpl->load ( $extdir . '/templates/ext/varchar.php' );
			$dataField = 'DataString';
			break;
		case 'pagelisting':
			$tpl = new cPTemplate ( "$extdir/templates/ext/pagelisting.php" );
			$dataField = 'DataInt';
			break;
		case 'extension':
			$tpl = new cPTemplate ( "$extdir/templates/ext/extension.php" );
			$extension = '';
			$fieldObject =& $field;
			if ( file_exists ( 'extensions/' . $field->DataString . '/adminmodule.php' ) )
				include ( 'extensions/' . $field->DataString . '/adminmodule.php' );
			else if ( file_exists ( 'extensions/' . $field->DataString . '/templates/websnippetconfig.php' ) )
			{
				$extension = new cPTemplate ( 'extensions/' . $field->DataString . '/templates/websnippetconfig.php' );
				$extension->data =& $field;
				$extension->content =& $obj;
				$extension = '<div class="Container" style="padding: 4px">' . $extension->render ( ) .'</div>';
			}
			else $extension .= '<p>Denne utvidelsen har ingen innstillinger.</p>';
			$tpl->extension =& $extension;
			$dataField = 'DataMixed';
			break;
		default:
			$tpl->load ( $extdir . '/templates/ext/unknown.php' );
			$dataField = 'DataString';
			break;
	}
	
	$tabletype = $field->DataTable;
	$tpl->fieldType = 'ContentData' . $field->DataTable;
	$tpl->field =& $field;
	$tpl->content =& $obj;
	$tpl->fieldID = "Extra_{$field->ID}_{$tabletype}_{$dataField}";
	switch ( $field->ContentGroup )
	{
		case 'Topp':
			$tpl->fieldGroup = 'toppfeltet';
			break;
		case 'Felt1':
			$tpl->fieldGroup = 'hovedfeltet';
			break;
		case 'Felt2':
			$tpl->fieldGroup = 'supplerende felt';
			break;
		case 'Bunn':
			$tpl->fieldGroup = 'bunnfeltet';
			break;
		default:
			$tpl->fieldGroup = $field->ContentGroup;
			break;
	}
	return preg_replace ( '/\<h4\>([\w\W]*?)\<\/h4\>/i', '', $tpl->render ( ) );
}
?>
