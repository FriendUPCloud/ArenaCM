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

/**
 * Plugin Datetime v0.1
 * For Arena 2
 *
 * @author Hogne Titlestad
 * @c 2008-2009 Blest AS
**/

$plugindir = 'lib/plugins/datetime';
$ostr = '';
include_once ( "$plugindir/include/funcs.php" );

ob_clean ( );

// Get date divider
if ( defined ( 'DATE_FORMAT' ) )
{
	$divider = preg_replace ( '/[0-9a-z]*/i', '', DATE_FORMAT );
	$divider = $divider{0};
}
else $divider = '-';

// Get current date from options or request
list( $cdate, ) = explode ( ' ', $_REQUEST[ 'date' ] ? $_REQUEST[ 'date' ] : $options[ 'Date' ] );
list ( $cyear, $cmonth, $cday, ) = explode ( '-', $cdate );	
$readonly = $options[ 'ReadOnly' ];
$ptpl = new cPTemplate ( "$plugindir/templates/calendar.php" );
$ptpl->divider = $divider;
$ptpl->field = $options[ 'Field' ];
$ptpl->urlextra = $options[ 'UrlExtra' ] ? $options[ 'UrlExtra' ] : '';
$callback = ( $options[ 'CallbackFunc' ] ? ( ', ' . $options[ 'CallbackFunc' ] ) : '' );

// Get some date variables
list ( $month, $year, $daynum, $monthname ) = 
	explode ( '-', date ( 'm-Y-N-F', 
		strtotime ( date ( $cyear . '-' . 
			str_pad ( $cmonth, 2, '0', STR_PAD_LEFT ) . '-01 00:00:00' ) 
		) ) 
	);
		
// Create calendar table	
for ( $a = $year - 10; $a < $year + 20; $a++ )
{
	$n = $a == $cyear ? ' selected="selected"' : '';
	$years .= '<option value="' . $a . '"' . $n . '>' . $a . '</option>';
}
$monthnames = Array ( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
for ( $a = 1; $a <= 12; $a++ )
{
	$n = $a == (int)$cmonth ? ' selected="selected"' : '';
	$months .= '<option value="' . $a . '"' . $n . '>' . i18n ( $monthnames[ $a - 1 ] ) . '</option>';
}
$daynum--; // we go from 0-6 not 1-7 (daynum is 1-7)

if ( $readonly )
{
	
	$ostr .= '<div class="Overview"><h3>' . $cyear . ' ' . i18n ( $monthnames[ $cmonth - 1 ] ) . '</h3></div>';
}
else
{
	$ostr .= '<table class="Dates"><tr>';
	$ostr .= '<td><select onchange="datetimeSetDate ( event, \'' . $options[ 'Field' ] . '\', \'month\', this.value, \'' . $ptpl->urlextra . '\'' . $callback . ' )">' . $months . '</select>, </td><td><select onchange="datetimeSetDate ( event, \'' . $options[ 'Field' ] . '\', \'year\', this.value, \'' . $ptpl->urlextra . '\'' . $callback . ' )">' . $years . '</select></td>';
	$ostr .= '</tr></table>';
}

$ostr .= '<table class="Calendar"><tr class="Row1">';
$ostr .= '<tr class="Row2">';
$days = Array ( 'Mon', 'Tue', 'Wed', 'Thu', 'Fre', 'Sat', 'Sun' );
for ( $a = 0; $a < 7; $a++ )
{
	$ostr .= '<th>' . i18n ( $days[ $a ] ) . '</th>';
}
$ostr .= '</tr><tr class="Row3">' . "\n";
$rownum = 3;

// fill blanks till first day
for ( $a = 0; $a < $daynum; $a++ )
	$ostr .= '<td>&nbsp;</td>';
for ( $a = 1; $a < 28 || checkdate ( $month, $a, $year ); $a++, $daynum++ )
{
	$classes = $days[ $daynum % 7 ];
	if ( $a == (int)$cday ) 
		$classes .= ' Current';
	$ex = ' class="' . $classes . '"';
	$ostr .= '<td' . $ex . '>';
	if ( !$readonly )
	{
		$ostr .= '<a href="javascript: void(0)" onclick="datetimeSetDate ( event, \'' . $options[ 'Field' ] . '\', \'day\', \'' . $a . '\', \'' . $ptpl->urlextra . '\'' . $callback . ' )">';
	}
	$ostr .= $a;
	if ( !$readonly ) $ostr .= '</a>';
	$ostr .= '</td>';
	if ( $daynum % 7 == 6 )
	{
		$ostr .= '</tr>';
		$rownum++;
	}
	if ( $daynum % 7 == 6 && $a > 1 )
		$ostr .= '<tr class="Row' . $rownum . '">';
}

// Finish row with blanks
for ( $a = $daynum % 7; $a <= 6; $a++ )
	$ostr .= '<td>&nbsp;</td>';
if ( $daynum % 7 != 6 )
{
	$ostr .= '</tr>';
	$rownum++;
}
$ostr .= '</table>';
$ptpl->content = $ostr;

// Render template
if ( $_REQUEST[ 'ajax' ] )
{
	ob_clean ( );
	die ( $ostr );
}
$plugin = $ptpl->render ( );
$document->addResource ( 'javascript', BASE_URL . $plugindir . '/javascript/plugin.js' );
?>
