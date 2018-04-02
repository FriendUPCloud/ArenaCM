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

?>
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


include_once ( 'lib/classes/dbObjects/dbContent.php' );

$etpl = new cTemplate ( 'extensions/easyeditor/templates/advanceddialog.php' );
$etpl->page =& $page;
$old = GetSettingValue ( 'EasyEditor', 'FieldNames' . $page->MainID );
$namedata = explode ( ',', $old );
$str = '';
$sw = 2;
$page->loadExtraFields ();
foreach ( $page as $k=>$v )
{
	if ( substr ( $k, 0, 6 ) != '_extra' ) 
		continue;
	$sw = ( $sw == 1 ? 2 : 1 );
	$name = substr ( $k, 7, strlen ( $k ) - 7 );
	$field = $page->{'_field_' . $name };
	if ( $field->ContentID != $page->ID )
		continue;
	if ( in_array ( $name, $namedata ) )
		$sel = ' checked="checked"';
	else $sel = '';
	$id = $field->ID;
	$str .= '<tr class="sw' . $sw . '"><td>' . strtoupper( $k{7} ) . substr ( $k, 8, strlen ( $k ) - 8 ) . ':</td><td><input type="checkbox" id="ach_' . $id . '_' . $field->DataTable . '" ' . $sel . '/></td></tr>';
}
$etpl->efields = $str; unset ( $str );

die ( $etpl->render () );

?>
