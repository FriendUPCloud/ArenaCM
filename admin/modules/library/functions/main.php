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
include_once( 'admin/modules/library/include/functions_levels.php' );
include_once ( 'admin/modules/library/include/functions.php' );

$module = new cPTemplate ( "$tplDir/main.php" );
$module->currentLevel = $Session->LibraryCurrentLevel;
$module->viewmode = $Session->LibraryViewMode;
$module->levels = generateLevelTree(  $root, $Session->LibraryCurrentLevel );

// Add this one, it is required for Internet Explorer...
$GLOBALS[ 'document' ]->addResource ( 'javascript', 'lib/plugins/permissions/javascript/plugin.php?pmid=' );

switch ( $Session->LibraryListmode )
{
	case 'title':
		$module->listmode = 'title';
		break;
	case 'filename':
		$module->listmode = 'filename';
		break;
	case 'sortorder':
		$module->listmode = 'sortorder';
		break;
	default:
		$module->listmode = 'date';
		break;
}

// Get the folder
$f = new dbFolder(); 
if ( $Session->LibraryCurrentLevel == 'orphans' )
{
	$f->Name = 'Uorganisert materiale';
	$f->ID = 'orphans';
}
else
{
	$f->load ( $Session->LibraryCurrentLevel );
}

// Contents
if ( isset ( $_REQUEST[ 'libSearchKeywords' ] ) && isset ( $_REQUEST[ 'lid' ] ) )
{
	$f->load ( $_REQUEST[ 'lid' ] );
	$do_not_die = true;
	include ( 'admin/modules/library/functions/search.php' );
	$module->content =& $searchresults;
}
else if ( $Session->LibraryViewMode == 'details' )
{
	$module->content = getLevelContent( $Session->LibraryCurrentLevel );
}
else
{
	$tpl = new cPTemplate( $tplDir . '/listcontents.php' );
	$tpl->contents = getLevelContent( $Session->LibraryCurrentLevel );
	$tpl->folder = $f;
	$module->content = $tpl->render ();
}
$module->folder = $f;

// Tags
$module->tags = getTags ();

?>
