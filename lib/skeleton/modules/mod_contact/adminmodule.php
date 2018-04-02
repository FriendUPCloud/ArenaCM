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
	
global $document;
i18nAddLocalePath ( 'lib/skeleton/modules/mod_contact/locale/' );
$document->addResource ( 'stylesheet', 'lib/skeleton/modules/mod_contact/css/admin.css' );

$module = '';

$settings = CreateObjectFromString ( $field->DataMixed );

if ( $_REQUEST[ 'modaction' ] == 'properties' )
{
	include_once ( 'lib/skeleton/modules/mod_contact/include/act_properties.php' );
}
else if ( $_REQUEST[ 'modaction' ] == 'savecfield' )
{
	$f = new dbObject ( 'ContentDataSmall' );
	if ( $f->load ( $_REQUEST[ 'fid' ] ) )
	{
		$set = CreateObjectFromString ( $f->DataMixed );
		$set->{'Value_' . $_REQUEST[ 'field' ]} = $_POST[ 'data' ];
		$set->{'Type_' . $_REQUEST[ 'field' ]} = $_POST[ 'type' ];
		$set->{'Required_' . $_REQUEST[ 'field' ]} = $_POST[ 'required' ];
		$f->DataMixed = CreateStringFromObject ( $set );
		$f->save ();
		die ( 'ok' );
	}
	die ( 'fail' );
}
else if ( $_REQUEST[ 'modaction' ] == 'removefield' )
{
	$dummy = new Dummy();
	$fld = utf8_encode ( $_REQUEST[ 'field' ] );
	$so = explode ( ':', $settings->SortOrder );
	$nso = array ();
	foreach ( $so as $s )
	{
		if ( $s != $fld )
			$nso[] = $s;
	}
	$settings->SortOrder = implode ( ':', $nso );
	foreach ( $settings as $k=>$v )
	{
		if ( $k == $fld )
		{
			continue;
		}
		else $dummy->$k = $v;
	}
	$field->DataMixed = CreateStringFromObject ( $dummy );
	$field->save ();
	ob_clean ( );
	header ( 'Location: admin.php?module=extensions&extension=' . $_REQUEST[ 'extension' ] );
	die ( );
}
else if ( $_REQUEST[ 'modaction' ] == 'savesettings' )
{
	foreach ( $_POST as $k=>$v )
	{
		if ( substr ( $k, 0, 9 ) == 'checkbox_' )
		{
			list ( $key, $value ) = explode ( '<!--sep-->', $v );
			$settings->$key = str_replace ( array ( "\n", "\t" ), "", $value );
		}
		else
		{
			$settings->$k = str_replace ( array ( "\n", "\t" ), "", $v );
		}
	}
	$field->DataMixed = CreateStringFromObject ( $settings );
	$field->save ();
}
else if ( $_REQUEST[ 'modaction' ] == 'reset' )
{
	$settings->SortOrder = '';
	$field->DataMixed = CreateStringFromObject ( $settings );
	$field->save ();
	ob_clean ( );
	header ( 'Location: admin.php?module=extensions&extension=' . $_REQUEST[ 'extension' ] );
	die ( );
}
else if ( $_REQUEST[ 'modaction' ] == 'sortorder' )
{
	$settings->SortOrder = utf8_encode ( $_REQUEST[ 'fields' ] );
	$field->DataMixed = CreateStringFromObject ( $settings );
	$field->save ();
	ob_clean ( );
	header ( 'Location: admin.php?module=extensions&extension=' . $_REQUEST[ 'extension' ] );
	die ( );
}
$mtpl = new cPTemplate ( 'lib/skeleton/modules/mod_contact/templates/admin_settings.php' );
foreach ( $settings as $k=>$v )
	$mtpl->$k = $v;
if ( isset ( $settings->SortOrder ) )
	$mtpl->SortOrder = explode ( ":", $settings->SortOrder );
$module .= $mtpl->render ();

?>
