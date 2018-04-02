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


function listSubpageLevels ( $pp, $currlev, $maxlevels, $fieldObject, $content, $options )
{
	global $page;
	$subpages = new dbContent ();
	$subpages->Parent = $pp->MainID;
	$subpages->addClause ( 'WHERE', 'MainID = ID AND !IsDeleted AND !IsSystem' );
	$subpages->addClause ( 'ORDER BY', 'SortOrder ASC, ID ASC' );
	$str = '';
	$oneOpen = false;
	if ( $subpages = $subpages->find ( ) )
	{
		if ( $options->Mode == 'mode_brief' )
		{
			$str .= '<div class="SubListing"><ul>';
		}
		foreach ( $subpages as $p )
		{
			$open = false;
			$p->{"_locked_".$fieldObject->Name} = 'true';
			if ( $options->Mode == 'mode_brief' )
			{
				if ( $page->MainID == $p->MainID )
					$c = ' current';
				else $c = '';
				$tl = $currlev + 1;
				$istr = '';
				if ( $tl < $maxlevels )
				{
					$a = listSubpageLevels ( $p, $tl, $maxlevels, $fieldObject, $content, $options );
					if ( $a[0] )
						$istr = $a[0];
					if ( $a[1] )
						$open = $a[1];
				}
				$str .= '<li class="' . $p->RouteName . $c . '' . ( $open ? ' open' : '' ) . '"><a href="' . $p->getUrl () . '">' . $p->MenuTitle . '</a>';
				$str .= $istr;
				$str .= '</li>';
				if ( $c ) $open = 1; // subpage is current
			}
			else if ( $options->Mode == 'mode_field' )
			{
				$p->loadExtraFields ();
				$str .= '<div class="SubListing"><div class="Title"><a href="' . $p->getUrl () . '">' . $p->MenuTitle . '</a></div><div class="' . $options->Field . '">' . $p->{$options->Field} . '</div></div>';
				continue;
			}
			else
			{
				$str .= '<div class="Block '.$p->RouteName . '">';
				$str .= preg_replace ( '/id\=\"([^"]*?)\"/i', 'class="$1"', $p->renderExtraFields () );
				if ( $tl < $maxlevels )
				{
					$a = listSubpageLevels ( $p, $tl, $maxlevels, $fieldObject, $content, $options );
					if ( $a[0] )
						$str .= $a[0];
					if ( $a[1] )
						$open = $a[1];
				}
				$str .= '</div>';
			}
			if ( $open )
				$oneOpen = true;
		}
		if ( $options->Mode == 'mode_brief' )
		{
			$str .= '</ul></div>';
		}
	}
	return array ( $str, $oneOpen );
}

?>
