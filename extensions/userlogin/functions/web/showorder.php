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

include_once ( 'extensions/webshop/include/functions.php' );

$Object = new dbObject ( 'classProductOrder' );
if ( $Object->load ( $_REQUEST[ 'oid' ] ) )
{
	$ostr = '';
	$lstr = '';
	$orderdata = explode ( "\n", $Object->Order );
	$vat = getOrderPart ( 'VAT', $Object->Order );
	if ( !$vat ) $vat = GetSettingValue ( 'webshop', 'MVA' );
	$ostr = '';
	$sum = 0.0;
	foreach ( $orderdata as $d )
	{
		$d = explode ( "\t", $d );
		$du = new Dummy ( );
		foreach ( $d as $dd )
		{
			$dd = explode ( ':', $dd );
			$du->{$dd[0]} = $dd[ 1 ];
		}
		if ( !$du->Name ) continue;
		$ostr .= '<tr class="sw' . ( $sw = ( $sw == 1 ? ( $sw = 2 ) : ( $sw = 1 ) ) ) . '">';
		$ostr .= '<td><strong>' . $du->Name . '</strong></td><td>' . ( $du->ProductNumber ? $du->ProductNumber : '-' ) . '</td>';
		$ostr .= '<td>kr. ' . number_format ( ( ( double )$du->PriceNoVAT * $vat ), 2, ',', '.' ) . '</td>';
		$ostr .= '<td>x ' . $du->Quantity . '</td></tr>';
		$sum += ( double )$du->PriceNoVAT * ( double )$du->Quantity;
	}

	/**
	 * Delivery user
	**/
	$deliveryuser = new dummy ( );
	$data = explode ( "\n", $Object->DeliveryAddress );
	foreach ( $data as $d )
	{
		$d = explode ( ':', $d );
		if ( trim ( $d[ 0 ] ) )
		{
			$deliveryuser->{$d[0]} = $d[ 1 ];
		}
	}

	$frakt = getOrderPart ( 'Shipping', $Object->Order ) / $vat;
	$sum += $frakt;

	$ostr .= '<tr class="sw' . ( $sw = ( $sw == 1 ? ( $sw = 2 ) : ( $sw = 1 ) ) ) . '"><td colspan="2"><strong><em>Frakt</em></strong>:</td><td>kr. ' . number_format ( $frakt * $vat, 2, ',', '.' ) . '</td><td></td></tr>';
	$ostr .= '<tr class="sw' . ( $sw = ( $sw == 1 ? ( $sw = 2 ) : ( $sw = 1 ) ) ) . '"><td colspan="2"><strong><em>Sum</em></strong>:</td><td>kr. ' . number_format ( $sum * $vat, 2, ',', '.' ) . '</td><td></td></tr>';
	$ostr .= '<tr class="sw' . ( $sw = ( $sw == 1 ? ( $sw = 2 ) : ( $sw = 1 ) ) ) . '"><td colspan="2"><strong>Herav mva</strong>:</td><td>kr. ' . number_format ( ( $sum * $vat ) - $sum, 2, ',', '.' ) . '</td><td></td></tr>';
	
	$lstr .= '
	<br/>
	<h2 class="DeliveryAddress">Leveringsadresse:</h2>
	<table class="DeliveryAddress">
		<tr class="sw1"><th><strong>Mottaker:</strong></th><td>' . $deliveryuser->Name . '</td></tr>
		<tr class="sw2"><th><strong>Adresse:</strong></th><td>' . $deliveryuser->Address . '</td></tr>
		<tr class="sw1"><th><strong>Postnr./sted:</strong></th><td>' . $deliveryuser->Postcode . ', ' . $deliveryuser->City . '</td></tr>
		' . ( $deliveryuser->Country ? '<tr class="sw2"><th><strong>Land:</strong></th><td>' . $deliveryuser->Country . '</td></tr>' : '' ) . '
	</table>
	';
	
	die ( '<table style="width: auto">' . $ostr . '</table>' . $lstr . '<br/><button type="button" onclick="document.getElementById ( \'OrderDetails' . $Object->ID . '\' ).innerHTML = \'\';">' . i18n ( 'Hide information' ) . '</button><hr/>' );
}
die ( 'Order not found!' );
?>
