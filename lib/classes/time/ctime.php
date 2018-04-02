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



define ( "MonthsDefault", 0 );
define ( "MonthsNamedShort", 1 );
define ( "MonthsNamedFull", 2 );
define ( "DaysDefault", 0 );
define ( "DaysNamedShort", 1 );
define ( "DaysNamedFull", 1 );

class cTime
{
	var $language;
	
	function __construct ( )
	{
		if ( !$this ) $self =& $GLOBALS[ "Time" ];
		else $self =& $this;
		
		$self->language = "norsk";
	}
	
	function setLang ( $language )
	{
		if ( !$this ) $self =& $GLOBALS[ "Time" ];
		else $self =& $this;
		
		$language = strtolower ( $language );
		$self->language = strtolower ( $language );
	}
	
	function interpretFormat ( $date, $format, $languagecode )
	{
		$out = date ( $format, strtotime ( $date ) );
		$outs = Array ( );
		$signs = Array ( );
		$b = 0;
		for ( $a = 0; $a < strlen ( $out ); $a++ )
		{
			if ( $out{$a} == "," || $out{$a} == "." || $out{$a} == " " || $out{$a} == "-" || $out{$a} == ":" || $out{$a} == ";" )
			{
				$signs[] = $out{$a};
				$b++;
			}
			else
				$outs[ $b ] .= $out{$a};
		}
		$out = "";
		
		foreach ( $outs as $k=>$o )
		{
			if ( strstr ( $format, "F" ) != false )
				$outs[ $k ] = i18n ( $o, $languagecode );
			else if ( strstr ( $format, "f" ) != "false" )
				$outs[ $k ] = i18n ( $o, $languagecode );
			else $outs[ $k ] = $o;
		}		
		for ( $a = 0; $a <= count ( $outs ); $a++ )
			$out .= $outs[ $a ] . ( $signs[ $a ] ? $signs[ $a ] : "" );
		$str = $out;
		return $str;
	}
	
	function fancy ( $time )
	{
		if ( !$this ) $self =& $GLOBALS[ "Time" ];
		else $self =& $this;
		
		if ( is_string ( $time ) || ( int )$time <= 1 )
			$time = strtotime ( $time );
		if ( !$self->language )
			$self->setLang ( "norsk" );
		switch ( $self->language )
		{
			default:
				return date ( "d.", $time ) . " " . $self->monthName ( date ( "m", $time ) ) . ", " . date ( "Y", $time ) . " - kl. " . date ( "H:i:s", $time );
		}
	}
	
	function fancyNoTime ( $time )
	{
		if ( !$this ) $self =& $GLOBALS[ "Time" ];
		else $self =& $this;
		
		list ( $day, $monthname, $year, ) = explode ( " ", $self->fancy ( $time ) );
		return $day . " " . $monthname . " " . $year;
	}
	
	function monthName ( $monthnum )
	{
		if ( !$this ) $self =& $GLOBALS[ "Time" ];
		else $self =& $this;
		
		if ( $monthnum <= 0 ) $monthnum = 1;
		if ( $monthnum >= 13 ) $monthnum = 12;
		
		switch ( $self->language )
		{
			case 'english':
				switch ( ( int )$monthnum )
				{
					case 1:		return "January";
					case 2:		return "February";
					case 3:		return "March";
					case 4:		return "April";
					case 5:		return "May";
					case 6:		return "June";
					case 7:		return "July";
					case 8:		return "August";
					case 9:		return "September";
					case 10:	return "October";
					case 11:	return "November";
					case 12:	return "December";
				}
				break;
			default:
				switch ( ( int )$monthnum )
				{
					case 1:		return "Januar";
					case 2:		return "Februar";
					case 3:		return "Mars";
					case 4:		return "April";
					case 5:		return "Mai";
					case 6:		return "Juni";
					case 7:		return "Juli";
					case 8:		return "August";
					case 9:		return "September";
					case 10:	return "Oktober";
					case 11:	return "November";
					case 12:	return "Desember";
				}
				break;
		}
	} 
}
$GLOBALS[ "Time" ] = new cTime ( );
?>
