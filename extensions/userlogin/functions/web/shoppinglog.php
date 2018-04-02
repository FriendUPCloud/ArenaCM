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

$tpl = new cPTemplate ( 'extensions/userlogin/webtemplates/shoppinglog.php' );
$obj = new dbObject ( 'classProductOrder' );
$obj->AddClause ( 'WHERE', 'UserID=' . $GLOBALS[ 'webuser' ]->ID );
$obj->AddClause ( 'ORDER BY', 'ID DESC' );
if ( $objs = $obj->find ( ) )
{
	foreach ( $objs as $obj )
	{
		switch ( $obj->Status )
		{
			case 0:
				$status = i18n ( 'Pending' );
				break;
			case 1:
				$status = i18n ( 'Processed' );
				break;
			case 2:
				$status = i18n ( 'Dispatched' );
				break;
			default:
				$status = i18n ( 'Deleted' );
				break;
		}
		$ostr .= '<tr>';
		$ostr .= '<td>#' . str_pad ( $obj->ID, 7, '0', STR_PAD_LEFT ) . '</td>';
		$ostr .= '<td>' . $obj->DateUpdated . '</td>';
		$ostr .= '<td>' . $status . '</td>';
		$ostr .= '<td><button type="button" onclick="seeOrder(' . $obj->ID . ')">' . i18n ( 'See order' ) . '</button></td>';
		$ostr .= '</tr>';
		$ostr .= '<tr>';
		$ostr .= '<td colspan="4" id="OrderDetails' . $obj->ID . '">';
		$ostr .= '</td>';
		$ostr .= '</tr>';
	}
	$tpl->OrderHistory = $ostr;
}
else $tpl->OrderHistory = '<tr><td colspan="4"><p>' . i18n ( 'You have not placed any orders.' ) . '</p></td></tr>';

if ( $_REQUEST[ 'die' ] )
{
	ob_clean ( );
	$tpl->content =& $GLOBALS[ 'page' ];
	die ( $tpl->render ( ) );
}
?>
