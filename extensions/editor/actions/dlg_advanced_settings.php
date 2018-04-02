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

if ( $_REQUEST[ 'bajaxrand' ] && $_REQUEST[ 'contenttype' ] )
{
	$cnt = new dbContent ( );
	if ( $cnt->load ( $_REQUEST[ 'cid' ] ) )
	{
		$cnt->IsSystem = $_REQUEST[ 'system' ] ? '1' : '0';
		$cnt->IsPublished = $_REQUEST[ 'published' ] ? '1' : '0';
		$cnt->IsFallback = $_REQUEST[ 'fallback' ] ? '1' : '0';
		$cnt->ContentType = $_REQUEST[ 'contenttype' ];
		if ( $cnt->ContentType != 'link' )
			$cnt->Link = '';
		if ( trim ( $_REQUEST[ 'modulename' ] ) )
			$cnt->Intro = 'ExtensionName' . "\t" . $_REQUEST[ 'modulename' ] . "\n";
		if ( trim ( $_REQUEST[ 'modulecontentgroup' ] ) )
			$cnt->Intro .= 'ExtensionContentGroup' . "\t" . $_REQUEST[ 'modulecontentgroup' ] . "\n";
		$cnt->SystemName = stripslashes ( trim ( $_REQUEST[ 'systemname' ] ) );
		$cnt->Title = stripslashes ( trim ( $_REQUEST[ 'pagetitle' ] ) );
		$cnt->ContentGroups = stripslashes ( trim ( $_REQUEST[ 'contentgroups' ] ) );
		$cnt->DateModified = date ( 'Y-m-d H:i:s' );
		$cnt->ContentTemplateID = $_REQUEST[ 'contenttemplateid' ];
		$cnt->Template = $_REQUEST[ 'template' ] ? $_REQUEST[ 'template' ] : '';
		$cnt->save ( );
		SetSetting ( 'ContentElementHideControls', $cnt->MainID, $_REQUEST[ 'hidecontrols' ] == '1' ? '1' : '0' );
		die ( 'ok' );
	}
	die ( 'fail' );
}
else
{
	$tpl = new cPTemplate ( $extdir . '/templates/dlg_advanced_settings.php' );
	$cnt = new dbContent ( $_REQUEST[ 'cid' ] );
	$tpl->content =& $cnt;
	if ( isset ( $_REQUEST['refresh'] ) && $_REQUEST['refresh'] == true )
	{
		$tpl->refresh = true;
		die ( $tpl->render() );
	}
	die ( $tpl->render ( ) );
}
?>
