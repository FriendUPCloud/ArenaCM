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
 * Setup main template
**/
$extemplate = new cPTemplate ( "admin/modules/extensions/templates/pageconfig.php" );

/** 
 * Setup config
**/

if ( substr ( $content->Intro, 0, 16 ) != "Extensionconfig\n" )
{
	$content->Intro = "Extensionconfig\n";
	$content->save ( );
}
$extemplate->content = &$content;

$extension = parseTabbedConfig ( "ExtensionName", $content->Intro );
$config = unfoldTabbedConfig ( $content->Intro );

if ( $extension && file_exists ( "extensions/{$extension}/templates/pageconfig.php" ) )
{
	$extemplate->Extension = new cPTemplate ( "extensions/{$extension}/templates/pageconfig.php" );
	$extemplate->Extension->content =& $content;
	$extemplate->Extension->config =& $config;
	if ( file_exists ( "extensions/{$extension}/functions/pageconfig.php" ) )
	{
		$extensiontemplate =& $extemplate->Extension;
		include_once ( "extensions/{$extension}/functions/pageconfig.php" );
	}
	$extemplate->Extension = $extemplate->Extension->render ( );
}
else
{
	if ( !file_exists ( "extensions/{$extension}/templates/pageconfig.php" ) )
		$extemplate->Extension = "<p><strong>Modulen har ingen innstillinger(pageconfig).</strong></p>";
	else $extemplate->Extension = "<p><strong>Ingen ekstensjon er valgt eller er tilgjengelig.</strong></p>";
}

if ( file_exists ( 'extensions' ) && is_dir ( 'extensions' ) )
{
	if ( $dir = opendir ( "extensions" ) )
	{
		$oStr = "<option value=\"\"" . ( $extension == '' ? ' selected="selected"' : '' ) . ">Velg ekstensjon:</option>";
		while ( $file = readdir ( $dir ) )
		{
			if ( $file[0] == "." ) continue;
			if ( $file == $extension ) $s = " selected=\"selected\"";
			else $s = "";
			$oStr .= "<option value=\"{$file}\"$s>" . ( strtoupper ( $file[0] ) . substr ( $file, 1, strlen ( $file ) - 1 ) ) . "</option>";
		}
		if ( !$oStr ) $oStr = "<option value=\"\">Ingen er installert.</option>";
		$extemplate->Extensions = $oStr;
	}
}
else $extemplate->Extensions = "<option value=\"\">Ingen utvidelser finnes i extensions mappen.</option>";

$config = $extemplate->render ( );
?>
