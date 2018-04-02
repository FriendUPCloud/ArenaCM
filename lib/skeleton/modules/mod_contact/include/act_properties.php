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


	$t = new cPTemplate ( 'lib/skeleton/modules/mod_contact/templates/admin_properties.php' );
	$ar = array ( 'varchar', 'text', 'select' );
	$str = '';
	foreach ( $ar as $a )
	{
		$cl = $a == $settings->{'Type_'.$_REQUEST['field']} ? ' selected="selected"' : '';
		$str .= '<option value="' . $a . '"' . $cl . '>' . i18n ( 'i18n_type_' . $a ) . '</option>';
	}
	$t->types = $str; unset ( $str );
	$t->data = isset($settings->{'Value_'.$_REQUEST['field']})?$settings->{'Value_'.$_REQUEST['field']}:'';
	$t->field = $field;
	$t->required = isset($settings->{'Required_'.$_REQUEST['field']})?$settings->{'Required_'.$_REQUEST['field']}:'';
	die ( $t->render () );
?>
