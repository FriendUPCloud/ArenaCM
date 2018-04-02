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

/**
 * module for Arena 2 Library Administration
**/

$root = dbFolder::getRootFolder ( );
$db = &dbObject::globalValue ( 'database' );	 
i18nAddLocalePath ( 'admin/modules/library/locale' );
$document->addResource ( 'javascript', 'admin/modules/library/javascript/imageslices.js' );

// Translations
i18n ( 'i18n_long_image_upload_error' );

if( !defined( 'LIBRARY_ITEMSPERPAGE' ) ) define( 'LIBRARY_ITEMSPERPAGE', 15 );

if ( !$Session->LibraryListmodeOrder )
	$Session->Set ( 'LibraryListmodeOrder', 'ASC' );

if ( $_REQUEST[ 'listmode' ] )
{
	if ( $_REQUEST[ 'listmode' ] == $Session->LibraryListmode )
		$Session->Set ( 'LibraryListmodeOrder', $Session->LibraryListmodeOrder == 'ASC' ? 'DESC' : 'ASC' );
	$Session->Set ( 'LibraryListmode', $_REQUEST[ 'listmode' ] );
}
if ( !$Session->LibraryListmode ) $Session->Set ( 'LibraryListmode', 'sortorder' );

if( ( isset( $_REQUEST[ 'lid' ] ) && intval( $_REQUEST[ 'lid' ] ) > 0 ) || $_REQUEST[ 'lid' ] == 'orphans' ) 
	$Session->set ( 'LibraryCurrentLevel', $_REQUEST[ 'lid' ] );
else if ( !$Session->LibraryCurrentLevel )
	$Session->set ( 'LibraryCurrentLevel', $root->ID );
if ( $_REQUEST[ 'viewmode' ] )
	$Session->Set ( 'LibraryViewMode', $_REQUEST[ 'viewmode' ] );
if ( !$Session->LibraryViewMode ) $Session->Set ( 'LibraryViewMode', 'details' );

include_once ( 'lib/classes/pagination/cpagination.php' );
include_once ( 'lib/functions/functions.php' );
include_once ( 'admin/modules/library/include/functions.php' );
	
/**
 * Main, default stuff 
**/
if ( $action ) include ( $action );
if ( $function ) include ( $function ); 
?>
