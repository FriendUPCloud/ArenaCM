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

include_once ( $extdir . 'include/funcs.php' );
include_once ( $extdir . 'include/extrafields.php' );

// Generate language selector
$langs = new dbObject ( 'Languages' );
$langs->addClause ( 'ORDER BY', 'IsDefault DESC, NativeName DESC' );
$langhtml = '';
if ( count ( $langs = $langs->find ( ) ) > 1 )
{
	$langhtml = '<div style="display: block; float: right; position: relative; top: -4px;">';
	$langhtml .= '<select onchange="changeLanguage(this.value)">';
	foreach ( $langs as $lang )
	{
		if ( $lang->ID == $Session->CurrentLanguage ) $s = ' selected="selected"';
		else $s = '';
		$langhtml .= '<option' . $s . ' value="' . $lang->ID . '">' . $lang->NativeName . ' (' . $lang->Name . ')</option>';
	}
	$langhtml .= '</select>';
	$langhtml .= '</div>';
	$tpl->languages =& $langhtml;
}

// Don't we have the page we're looking for?
if ( !$cnt->ID )
{
	$cnt = new dbContent ( );
	$cnt->Language = $Session->CurrentLanguage;
	$cnt->Parent = '0';
	if ( $new = $cnt->findSingle () )
	{
		$cnt = $new;
	}
	// We are missing the content for the language!
	// Create content
	else
	{
		$cnt->MenuTitle = 'Ny side';
		$cnt->Title = 'Ny side';
		$cnt->save ();
		$cnt->MainID = $cnt->ID;
		$cnt->save ();
		$cnt->ID = 0;
		$cnt->IsPublished = 1;
		$cnt->save ();
	}
}

// Shortcut to save time
define ( 'ACTION_URL', BASE_URL . 'admin.php?module=extensions&extension=editor&' );
$GLOBALS[ 'document' ]->sHeadData[] = "\t\t" . '<script type="text/javascript">' . "\n" .
	"\t\t\t" . 'var ACTION_URL = "' . ACTION_URL . '";' . "\n" .
	"\t\t</script>";
if ( $_REQUEST[ 'mod' ] )
{
	$field = new dbObject ( 'ContentDataSmall' );
	$field->ContentTable = 'ContentElement';
	$field->ContentID = $cnt->ID;
	$field->DataString = $_REQUEST[ 'mod' ];
	if ( $field = $field->findSingle ( ) )
		$fieldObject =& $field;
	else $fieldObject = $field = false; 
	$cnt->loadExtraFields ( );
	$content =& $cnt;
	require ( 'lib/skeleton/modules/' . $_REQUEST[ 'mod' ] . '/adminmodule.php' );
	$_REQUEST[ 'mod' ] = $_REQUEST[ 'modaction' ] = '';
}

$tpl->page =& $cnt;
$tpl->structure = editorStructure ( $cnt );

if ( !$tpl->page->DateCreated )
{
	$tpl->page->_oldTitle = $tpl->page->MenuTitle;
	$tpl->page->Title = '';
	$tpl->page->MenuTitle = '';	
}

$db =& $cnt->getDatabase ( );
if ( $notes = $db->fetchObjectRow ( 'SELECT * FROM Notes WHERE ContentTable="ContentElement" AND ContentID=' . $cnt->ID ) )
	$tpl->Notes =& stripslashes ( $notes->Notes );
else $tpl->Notes = '';

if ( !$Session->EditorDbChecked )
{
	include_once ( $extdir . 'include/dbcheck.php' );
	$Session->set ( 'EditorDbChecked', '1' );
}

if ( $Session->AdminUser->checkPermission ( $cnt, 'Write', 'admin' ) )
{
	$form = new cPTemplate ( );
	switch ( $cnt->ContentType )
	{
		case 'link':
			// Extract link data - here we can add things as we go
			$linkObject = CreateObjectFromString ( $cnt->LinkData );
			if ( !$linkObject->LinkTarget )
				$linkObject->LinkTarget = '_self';
			$form->load ( $extdir . '/templates/contentform_link.php' );
			$form->linkData =& $linkObject;
			$form->extrafields = renderExtraFields ( $cnt );
			break;
		case 'extrafields':
			$form->load ( $extdir . '/templates/contentform_extrafields.php' );
			$form->extrafields = renderExtraFields ( $cnt );
			break;
		case 'text':
			$form->load ( $extdir . '/templates/contentform_main.php' );
			break;	
		default:
			$form->load ( $extdir . '/templates/contentform_extensions.php' );
			$config = explode ( "\n", $cnt->Intro );
			foreach ( $config as $c )
			{
				list ( $e, $v ) = explode ( "\t", $c );
				if ( $e == 'ExtensionName' )
				{
					$cnt->extension = $v;
					break;
				}
			}
			$ext = trim ( $cnt->extension );
			if ( file_exists ( 'extensions/' . $ext . '/templates/pageconfig.php' ) )
			{
				$pl = new cPTemplate ( 'extensions/' . $ext . '/templates/pageconfig.php' );
				$pl->content =& $cnt;
				$pl->page =& $cnt;
				$o = $pl->render ( );
			}
			
			$form->extension = $o;
			$form->extrafields = renderExtraFields ( $cnt );
			break;		
	}
	$form->page =& $cnt;
	$tpl->ContentForm = $form->render ( );
}
else
{
	$tpl->ContentForm = '<iframe width="100%" height="500px" frameborder="no" border="0" resize="no" style="-moz-box-sizing: border-box; box-sizing: border-box; border: 2px solid #a00; -moz-border-radius: 3px;" scrolling="auto" src="' . $cnt->getUrl ( ) . '"></iframe><div class="Spacer"></div>';
}

// Load modules etc in the available / connected modules tabs ------------------
$tpl->pageModulesAvailable = showFreeModules();
$tpl->pageModulesConnected = showAddedModules ( $cnt->ID );

// Table layouts
$tpl->tableLayouts = '';
include_once ( 'extensions/editor/actions/gettablelayouts.php' );

// Look for expansions on the editor -------------------------------------------
if ( $dir = opendir ( 'extensions' ) )
{
	while ( $file = readdir ( $dir ) )
	{
		if ( $file[0] == '.' || $file == 'editor' || $file == 'easyeditor' )
			continue;
		if ( file_exists ( "extensions/$file/editor_expansion.php" ) )
		{
			$content =& $cnt;
			include ( "extensions/$file/editor_expansion.php" );
		}
	}
	closedir ( $dir );
}
if ( !$tpl->toolExpansions )
	$tpl->toolExpansions = '';
if ( !$tpl->moduleTabExpansions )
	$tpl->moduleTabExpansions = '';
if ( !$tpl->moduleExpansions )
	$tpl->moduleExpansions = '';

if ( !$Session->EditorDbCheck )
{
	$table = new cDatabaseTable ( 'Notes' );
	if ( !$table->load ( ) )
	{
		$db->query ( '
			CREATE TABLE `Notes`
			(
				`ContentTable` varchar(255) NOT NULL,
				`ContentID` bigint(20) NOT NULL,
				`Notes` text,
				PRIMARY KEY(ContentTable,ContentID)
			)
		' );
	}
	$Session->set ( 'EditorDbCheck', 1 );
}

?>
