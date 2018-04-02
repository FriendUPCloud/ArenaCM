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

if ( $_REQUEST[ 'modaction' ] )
{
	if ( $_REQUEST[ 'modaction' ] == 'savecfg' )
	{
		$d = array ( );
		foreach ( $_POST as $k=>$v )
		{
			if ( substr ( $k, 0, 6 ) == 'config' )
			{
				$d[] = trim ( str_replace ( 'config', '', $k ) ) . '<!-- separator_cols --/>' . trim ( $v );
			}
		}
		$field->DataMixed = implode ( '<!-- separator_rows --/>', $d );
		$field->save ( );
		die ( 'ok' );
	}
}

$mtpl = new cPTemplate ( 'skeleton/modules/mod_blog_tip/templates/adm_main.php' );

$cfg = new Dummy ( );
$cfg->Heading = 'Send us a tip';
$cfg->Info = 'Do you have a blog for us? Send it in for evaluation!';
$cfg->ButtonText = 'Get started';
$cfg->ContentGroup = '';
$cfg->ContentElementID = 0;

// Get options
if ( $mdat = explode ( '<!-- separator_rows --/>', $field->DataMixed ) )
{
	foreach ( $mdat as $mc )
	{
		$mc = explode ( '<!-- separator_cols --/>', $mc );
		if ( $mc[0] = trim($mc[0]) )
		{
			$cfg->{$mc[0]} = trim ( $mc[1] );
		}
	}
}

$mtpl->data =& $cfg;

$module .= $mtpl->render ( );
?>
