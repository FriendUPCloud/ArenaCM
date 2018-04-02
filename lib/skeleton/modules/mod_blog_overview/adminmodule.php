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


global $Session;
i18nAddLocalePath ( 'lib/skeleton/modules/mod_blog_overview/locale/' );
$mtpldir = 'lib/skeleton/modules/mod_blog_overview/templates/';
$GLOBALS['document']->addResource('stylesheet', $mtpldir . '../css/admin.css');

if (!$Session->mod_blogoversikt_initialized)
{
	$Session->set('mod_blogoversikt_initialized', 1);
}

switch($_REQUEST['modaction'])
{
	case 'new':
		if ( strstr ( $fieldObject->DataMixed, '--Version 2.0--' ) )
			$mtpl = new cPTemplate ( $mtpldir . 'adm_blogoversikt_new.php' );
		else $mtpl = new cPTemplate($mtpldir . 'adm_blogoversikt.php');
		$mtpl->amounts =& $amounts;
		$mtpl->datamixed = $field->DataMixed;
		die ($mtpl->render());
		break;
				
	case 'executeadd':
		$db =& dbObject::globalValue('database');
		if ( isset ( $_POST['mixed'] ) )
		{
			$field->DataMixed = '<!--Version 2.0-->'.
				"listmode\t{$_POST['listmode']}\n" .
				"leadinimagewidth\t{$_POST['leadinimagewidth']}\n" .
				"leadinimageheight\t{$_POST['leadinimageheight']}" .
				'<!--separate-->' .
				$_POST[ 'mixed' ];
		}
		$field->save ();

	case 'standard':
	default:
		$mtpl = new cPTemplate($mtpldir . 'adm_main.php');
		$std = new cPTemplate($mtpldir . 'adm_std.php');
		$std->datamixed =& $field->DataMixed;
		$mtpl->standard = $std->render ( );
		if ($_REQUEST['modaction'] == 'standard' || $_REQUEST['modaction'] == 'executeadd')
			die ($std->render());
		break;
}

if ($mtpl)
	$module = $mtpl->render();
?>
