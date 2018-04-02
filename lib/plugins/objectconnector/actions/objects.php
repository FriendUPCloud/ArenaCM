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

i18nAddLocalePath ( 'lib/plugins/objectconnector/locale' );

$o = new dbObject ( $_REQUEST[ 'objecttype' ] );
if ( $o->load ( $_REQUEST[ 'objectid' ] ) )
{
	$a = 0;
	if ( $objs = $o->getObjects ( ) ) 
	{
		foreach ( $objs as $obj )
		{
			switch ( $obj->_tableName )
			{
				case 'Image':
					$extra = $obj->getImageHTML ( 24, 24, 'framed' );
					$identifier = $obj->getIdentifier ( );
					$type = 'Bilde';
					break;
				default:
					if ( substr ( $obj->_tableName, 0, 5 ) == 'class' )
						$extra = '<img src="admin/gfx/icons/plugin.png">';
					else $extra = '';
					$identifier = $obj->getIdentifier ( );
					$type = $obj->_tableName;
					break;
			}
			$info = "'{$obj->_tableName}', '{$obj->ID}'";
			$ostr .= '
		<div class="SubContainer" style="' . ( $a ? 'margin-top: 2px; ' : '' ) . 'height: 24px; overflow: hidden; padding: 4px; background: ' . ( $switch = $switch == '#f8f8f8' ? '#ffffff' : '#f8f8f8' ) . '">
			<table style="width: 100%; border-collapse: collapse; border-spacing: 0">
				<tr>
					<td' . ( $extra ? ( ' style="vertical-align: middle; width: 32px">' . $extra . '</td><td style="vertical-align: middle">' ) : ' colspan="2">' ) . $identifier . '</td>
					<td style="vertical-align: middle" width="150px">' . $type . '</td>
					<td style="vertical-align: middle; width: 60px; text-align: right">
						<div class="SubContainer" style="padding: 2px; float: right">
							<a href="javascript: void ( 0 );" onclick="poc_Nudge( \'up\', ' . $info . ' )"><img src="admin/gfx/icons/arrow_up.png" border="0"></a>
							<a href="javascript: void ( 0 );" onclick="poc_Nudge( \'down\', ' . $info . ' )"><img src="admin/gfx/icons/arrow_down.png" border="0"></a>
							<a href="javascript: void ( 0 );" onclick="poc_Delete( ' . $info . ' )"><img src="admin/gfx/icons/bin.png" border="0"></a>
						</div>
					</td>
				</tr>
			</table>
		</div>
			';
			$a++;
		}
		die ( $ostr );
	}
}
die ( i18n ( 'No objects connected.' ) );
?>
