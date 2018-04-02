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



include_once ( "lib/classes/template/cPTemplate.php" );

class cPagination
{
	var $Count = 0;
	var $Position = 0;
	var $Limit = 0;
	var $Template = "lib/classes/pagination/navigation.php";
	var $UsePages = 0;
	var $ExtraUrlData = '';
	var $Target = false;
	var $PositionVariable = 'pos';
	var $ShowCount = false;
	
	function render ( )
	{
		if ( !$this->Target )
			$this->Target = ( BASE_URL . 'admin.php?module=' . $_REQUEST[ 'module' ] . '&' );
		if ( ( $this->Count > $this->Limit && $this->Limit > 0 ) || $this->Position > 0 || $this->ShowCount )
		{
			$tplPagination = new cPTemplate ( $this->Template );
			$tplPagination->Target =& $this->Target;
			$tplPagination->obj =& $this;
			$tplPagination->Count = $this->Count;
			$tplPagination->Position = $this->Position;
			$tplPagination->Limit = $this->Limit;
			$tplPagination->ExtraUrlData = $this->ExtraUrlData;
			$tplPagination->ShowCount = $this->ShowCount;
			
			// Select options
			if ( ( $this->Count > $this->Limit && $this->Limit > 0 ) || $this->Position > 0 )
			{
				$pages = $this->Count / $this->Limit;
				if ( round ( $pages ) < $pages )
					$pages = round ( $pages ) + 1;
				else if ( round ( $pages ) > $pages )
					$pages = round ( $pages );
				else $pages = round ( $pages );
				$tplPagination->Select = '';
				for ( $a = 0; $a < $pages; $a++ )
				{
					$s = $a * $this->Limit == $this->Position ? ' selected="selected"' : '';
					$tplPagination->Select .= '<option value="' . ( $a * $this->Limit ) . '"' . $s . '>' . i18n ( 'Page' ) . ' ' . ( $a + 1 ) . '</option>';
				}
			}
			
			
			// Next / prev
			if ( $this->Position + $this->Limit < $this->Count )
				$tplPagination->Next = true;
			else $tplPagination->Next = false;
			
			if ( $this->Position > 0 )
				$tplPagination->Prev = true;
			else $tplPagination->Prev = false;
			
			// Total pages
			$tplPagination->PageCount = ceil ( $this->Count / $this->Limit );
			
			// Current page
			$curr = $this->Position + $this->Limit;
			if ( $curr > $this->Count ) $curr = $this->Count;
			$tplPagination->CurrentPage = ceil ( $curr / $this->Limit );
			
			if ( $this->Content ) $tplPagination->content =& $this->Content;
			
			return ( $oStr = $tplPagination->render () ) ? "<div class=\"SubContainer\">$oStr</div>" : "";
		}
		return '';
	}
}
?>
