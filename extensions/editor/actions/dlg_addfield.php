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

$content = new dbContent ( $_REQUEST[ 'cid' ] );
$tpl = new cPTemplate ( $extdir . '/templates/dlg_addfield.php' );
$tpl->content =& $content;
if ( $grps = explode ( ',', $content->ContentGroups ) )
{
	$tr = 0;
	foreach ( $grps as $g )
	{
		$ss = false;
		$ext = trim ( $g );
		if ( $tr == 0 )
				$str .= '<tr>';
		$str .= '<td width="12px"><input type="radio" name="contentgroup" value="' . trim ( $g ) . '"' . ( $ss ? ' checked="checked"' : '' ) . '></td><td>' . $ext . '</td>';
		if ( $tr == 1 )
			$str .= '</tr>';
		$tr = ( $tr + 1 ) % 2;
	}
	if ( $tr != 0 ) $str .= '</tr>';
	$str = '<table width="100%">' . $str . '</table>';
	$tpl->contentgroups = $str;
	$tpl->extensionfields = array ( );
	if ( $dir = opendir ( 'extensions' ) )
	{
		while ( $file = readdir ( $dir ) )
		{
			if ( $file[0] == '.' ) continue;
			if ( $file == 'editor' ) continue;
			if ( !file_exists ( 'extensions/' . $file . '/info.csv' ) ) continue;
			list ( $name, ) = explode ( '|', file_get_contents ( 'extensions/' . $file . '/info.csv' ) );
			$tpl->extensionfields[] = $name . '|' . $file; 
		}
		closedir ( $dir );
	}
	
}
die ( $tpl->render ( ) );
?>
