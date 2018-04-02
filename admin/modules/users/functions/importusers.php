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



$module = new cPTemplate ( 'admin/modules/users/templates/importusers.php' );

$user = new dbUser ( );
$user->IsTemplate = 1;
$user->load ( );
$user->loadExtraFields ( );
$module->User =& $user;

$matches = Array ( 
	'Username'=>'Brukernavn',
	'Password'=>'Passord (md5)',
	'Password_Plain'=>'Passord (tekst)',
	'Name'=>'Navn',		
	'Address'=>'Adresse',
	'City'=>'Poststed',
	'Postcode'=>'Postnummer',
	'Country'=>'Land',
	'Telephone'=>'Hustelefon',
	'Mobile'=>'Mobil telefon',
	'Email'=>'Epost',
	'DateCreated'=>'Dato opprettet'
);

for ( $a = 0; $a < 30; $a++ )
	$options .= '<option value="' . $a . '">' . $a . '</option>';
	
foreach ( $matches as $k=>$v )
{
	$ostr .= '
		<tr>
			<td><strong>' . $v . '</strong></td>
			<td><select name="index_' . $k . '">' . $options . '</select></td>
			<td><input type="hidden" name="field_' . $k . '" id="field_' . $k . '"/><input type="checkbox" onchange="document.getElementById ( \'field_' . $k . '\' ).value = this.checked ? \'1\' : \'0\'"/></td>
		</tr>
	';
}

foreach ( $user as $k=>$v )
{
	if ( strstr ( $k, 'extra_' ) )
	{
		$v = str_replace ( '_extra_', '', $k );
		$ostr .= '
			<tr>
				<td><strong>' . $v . '</strong></td>
				<td><select name="index_' . $k . '">' . $options . '</select></td>
				<td><input type="hidden" name="field_' . $k . '" id="field_' . $k . '"/><input type="checkbox" onchange="document.getElementById ( \'field_' . $k . '\' ).value = this.checked ? \'1\' : \'0\'"/></td>
			</tr>
		';
	}
}
$module->Fields = $ostr;

?>
