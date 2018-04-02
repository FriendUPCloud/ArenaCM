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
New code is (C) 2011 Idéverket AS, 2015 Friend Studios AS

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
                Rune Nilssen
*******************************************************************************/


	// Try to render parent extra field
if ( !$fieldObject->DataInt )
	if ( $result = $this->renderParentExtraField ( $fieldObject, $options = false ) )
		return $result;		
include_once ( 'lib/classes/time/ctime.php' );
include_once ( 'lib/classes/pagination/cpagination.php' );
$time = new cTime ( );
$path = $this->getPath ( );

if ( is_array ( $options ) && $options[ 'Link' ] )
	$link = $options[ 'Link' ];
else $link = false;

if ( !$link )
{
	$cat = new dbObject ( 'NewsCategory' );
	if ( $cat->load ( $fieldObject->DataInt ) )
	{
		if ( $cat->ContentElementID )
		{
			$page = new dbContent ( );
			if ( $page->load ( $cat->ContentElementID ) )
			{
				$link = $page->getPath ( );
			}
		}
	}
}

list ( $DetailMode, $Reversed, ) = explode ( '|', $fieldObject->DataMixed );

if ( $_REQUEST[ 'nid' ] && $DetailMode )
{
	$obj = new dbObject ( 'News' );
	$obj->load ( $_REQUEST[ 'nid' ] );
	
	$tpl = new cPTemplate ( );
	$tpl = new cPTemplate ( $tpl->findTemplate ( 'news_detail.php', array ( 'templates/', 'templates/modules/news/', 'web/templates/modules/news/' ) ) );
							
	$tpl->data = $obj;
	
	$cat = new dbObject ( 'NewsCategory' );
	$cat->load ( $obj->CategoryID );
	$tpl->data->Category = $cat;
	
	if ( $cat->ContentElementID )
	{
		$cnt = new dbContent ( );
		$cnt->load ( $cat->ContentElementID );
		$tpl->path = $cnt->getPath ( );
	}
	
	$language = new dbObject ( 'Languages' );
	$language->load ( $cat->Language );
	$tpl->data->LanguageCode = $language->Name;
	
	$tpl->data->FormattedDate = $time->interpretFormat ( 
		$tpl->data->DateActual, 
		$tpl->data->Category->DateFormat, 
		$tpl->data->LanguageCode
	);
	
	$tpl->path = $path;
	$tpl->time = &$time;
	$tpl->Link = $link;
	
	// 
	return '<div id="' . $fieldObject->Name . '">' . $tpl->render ( ) . '</div>';
}

$pos = $_REQUEST[ 'newspos' ];
if ( $pos <= 0 ) $pos = '0';

$objs = new dbObject ( 'News' );
if ( $fieldObject->DataInt > 0 )
	$objs->addClause ( 'WHERE', "CategoryID='{$fieldObject->DataInt}'" );
$objs->addClause ( 'WHERE', '( ( NOW() >= DateFrom AND NOW() < DateTo AND IsEvent ) OR !IsEvent )' );
if ( $Reversed ) $objs->addClause ( 'ORDER BY', 'DateActual ASC, ID ASC' );
else $objs->addClause ( 'ORDER BY', 'DateActual DESC, ID DESC' );
$objs->addClause ( 'WHERE', 'IsPublished' );

// Total, for pagination
if ( $fieldObject->DataString == 1 )
	$total = $objs->findCount ( );
	
// Limit?
if ( $fieldObject->DataDouble > 0 )
	$objs->addClause ( 'LIMIT', "$pos," . round ( $fieldObject->DataDouble ) );

if ( $objs = $objs->find ( ) )
{
	$tpl = new cPTemplate ( );
	$tpl = new cPTemplate ( $tpl->findTemplate ( 'news_listing.php', array ( 'templates/', 'templates/modules/news/', 'web/templates/modules/news/' ) ) );
	if ( !$tpl ) return '';
	$tpl->time = $time;
	$len = count ( $objs );
	$oStr = '';
	for ( $a = 0; $a < $len; $a++ )
	{
		$tpl->data = $objs[ $a ];
		
		// Add category
		if ( !$cats[ $objs[ $a ]->CategoryID ] )
		{
			$cats[ $objs[ $a ]->CategoryID ] = new dbObject ( 'NewsCategory' );
			$cats[ $objs[ $a ]->CategoryID ]->load ( $objs[ $a ]->CategoryID );
		}
		$tpl->data->Category = $cats[ $objs[ $a ]->CategoryID ];
		
		// Add content element 
		if ( $tpl->data->Category->ContentElementID )
		{
			if ( !$paths[ $tpl->data->Category->ContentElementID ] )
			{
				$paths[ $tpl->data->Category->ContentElementID ] = new dbContent ( );
				$paths[ $tpl->data->Category->ContentElementID ]->load ( $tpl->data->Category->ContentElementID );
				$paths[ $tpl->data->Category->ContentElementID ] = $paths[ $tpl->data->Category->ContentElementID ]->getPath ( );
			}
		}
		
		// Add language 
		if ( !$languages[ $cats[ $objs[ $a ]->CategoryID ]->Language ] )
		{
			$languages[ $cats[ $objs[ $a ]->CategoryID ]->Language ] = new dbObject ( 'Languages' );
			$languages[ $cats[ $objs[ $a ]->CategoryID ]->Language ]->load ( $cats[ $objs[ $a ]->CategoryID ]->Language );
		}
		$tpl->data->LanguageCode = $languages[ $cats[ $objs[ $a ]->CategoryID ]->Language ]->Name;
		
		// Add formatted date 
		$tpl->data->FormattedDate = $time->interpretFormat ( 
			$tpl->data->DateActual, 
			$tpl->data->Category->DateFormat, 
			$tpl->data->LanguageCode
		);
		
		$tpl->path = $path;
		$tpl->Link = $link;
		
		if ( $a < $len - 1 ) $tpl->Spacer = true;
		else $tpl->Spacer = false;
		$oStr .= $tpl->render ( );
	}
	// Enable pagination if needed
	if ( $total && count ( $objs ) < $total && $fieldObject->DataString == 1 )
	{
		$p = new cPagination ( );
		$p->Count = $total;
		$p->Position = $pos;
		$p->Limit = round ( $fieldObject->DataDouble );
		$p->Target = $this->getUrl ( ) . '?';
		$p->PositionVariable = 'newspos';
		$ex = '<div class="Pagination">' . $p->render ( ) . '</div>';
	} else $ex = '';
	return "<div id=\"{$fieldObject->Name}\">$oStr$ex</div>";
}
?>
