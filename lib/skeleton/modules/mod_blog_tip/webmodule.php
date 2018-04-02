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

global $page, $document;

$config = new Dummy ( );
$config->Heading = i18n ( 'Send us a tip' );
$config->Info = i18n ( 'Do you have a blog for us? Send it in for evaluation!' );
$config->ButtonText = i18n ( 'Get started' );

if ( $cfg = explode ( '<!-- separator_rows --/>', $field->DataMixed ) )
{
	foreach ( $cfg as $c )
	{
		if ( $c = explode ( '<!-- separator_cols --/>', $c ) )
		{
			if ( $c[0] = trim ( $c[0] ) )
			{
				$config->{$c[0]} = $c[1];
			}
		}
	}
}

if ( $config->ContentElementID )
{
	$target = new dbContent ( $config->ContentElementID );
	$target = $target->getUrl ( );
	
	if ( $page->MainID == $config->ContentElementID )
	{
		$page->loadExtraFields ( );
		$fld = new Dummy ( );
		$fld->ContentGroup = $config->ContentGroup;
		$fld->IsVisible = true;
		$ekey = texttourl ( "_extra_{$field->Name}_form" );
		$fkey = texttourl ( "_field_{$field->Name}_form" );
		$nkey = texttourl ( $field->Name . '_form' );
					
		switch ( $_REQUEST[ 'modaction' ] )
		{
			case 'sendtip':
				foreach ( array ( 'heading', 'author_name', 'leadin', 'article' ) as $n )
				{
					if ( !trim ( $_REQUEST[ $n ] ) )
						die ( i18n ( 'You forgot to fill in all the fields' ) . '.' );
				}
				$blog_item = new dbObject ( 'BlogItem' );
				$blog_item->Title = $_REQUEST[ 'heading' ];
				$blog_item->AuthorName = $_REQUEST[ 'author_name' ];
				$blog_item->Leadin = $_REQUEST[ 'leadin' ];
				$blog_item->Body = $_REQUEST[ 'article' ];
				$blog_item->IsPublished = false;
				$blog_item->DatePublish = date ( 'Y-m-d H:i:s' );
				$blog_item->DateCreated = date ( 'Y-m-d H:i:s' );
				$blog_item->DateUpdated = date ( 'Y-m-d H:i:s' );
				$blog_item->ContentElementID = 0;
				$blog_item->save ( );
				die ( i18n ( 'Thank you for your submission' ) . '!' . i18n ( 'We will evaluate it as soon as we can' ) . '.' );
			default:
				$tpl = new cPTemplate ( 'skeleton/modules/mod_blog_tip/templates/web_form.php' );
				$tpl->texteditor = enableTextEditor ( );
				$document->addHeadScript ( 'lib/javascript/arena-lib.js' );
				$document->addHeadScript ( 'lib/javascript/texteditor.js' );
				$document->addHeadScript ( 'skeleton/modules/mod_blog_tip/javascript/web.js' );
				$page->$ekey = '<div id="' . $nkey . '">' . $tpl->render ( ) . '</div>';
				
				break;
		}
		$page->$fkey =& $fld;
		$page->$nkey =& $page->$ekey;
	}
}
else $target = BASE_URL;

$mtpl = new cPTemplate ( 'skeleton/modules/mod_blog_tip/templates/web_main.php' );
$mtpl->target = $target;
$mtpl->config =& $config;
$module .= $mtpl->render ( );
?>
