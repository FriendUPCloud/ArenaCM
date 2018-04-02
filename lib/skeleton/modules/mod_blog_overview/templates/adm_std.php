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
	<?
	    $db_blog =& dbObject::globalValue('database');
	    i18nAddLocalePath ( 'lib/skeleton/modules/mod_blog_overview/locale');
		$pageids = array();
	    $noOfArticles = array();
		$this->blogoversikt = '';
		// Old way
		if ( !strstr ( $this->datamixed, '--Version 2.0--' ) )
		{
			if ($this->datamixed)
			{
				if ( list ( $blogs, $amounts, $nav ) = explode ( '#', $this->datamixed ) )
				{
					$blogs = explode ( '_', $blogs );
					$amounts = explode ( '_', $amounts );
					$nav = explode ( '_', $nav );
				
					$switch = 0;
					$this->blogoversikt .= '<div class="blogheader"><div class="blogheaderitem1">Blognavn</div><div class="blogheaderitem2">Antall artikler vist</div><div class="blogheaderitem3">Sidenavigasjon</div></div>';
					for ( $k = 0; $k < count ( $blogs ); $k++ )
					{
						$switch = 1 - $switch;
						$q = 'SELECT Title FROM ContentElement WHERE MainID = ' . $blogs[$k] . ' AND ID = MainID';
						$result = $db_blog->fetchObjectRow($q);
						if ($result) $this->blogoversikt .= '<div class="blogentry' . $switch . '"><div class="blogname">' . $result->Title . '</div><div class="blogamount">' . $amounts[$k] . '</div><div class="blognav">' . i18n($nav[$k]) . '</div></div>';
					}
				}
			}
		}
		// New way
		else
		{
			list ( , $mixed ) = explode ( '<!--Version 2.0-->', $this->datamixed );
			$mixed = explode ( '<!--separate-->', $mixed );
			$conf = CreateObjectFromString ( $mixed[0] );
			$n = 0;
			$this->blogoversikt .= '<div class="blogheader"><div class="blogheaderitem1">Blognavn</div><div class="blogheaderitem2">Antall artikler vist</div><div class="blogheaderitem3">Sidenavigasjon</div></div>';
			foreach ( $mixed as $mix )
			{
				if ( $n++ == 0 ) continue;
				$opts = CreateObjectFromString ( $mix );
				$switch = 1 - $switch;
				if ( !$opts->activated ) continue;
				$q = 'SELECT Title FROM ContentElement WHERE MainID = ' . $opts->activated . ' AND ID = MainID';
				$result = $db_blog->fetchObjectRow( $q );
				if ( $result ) 
				{
					$this->blogoversikt .= '<div class="blogentry' . $switch . '"><div class="blogname">' . $result->Title . '</div><div class="blogamount">' . $opts->quantity . '</div><div class="blognav">' . i18n($opts->navigation) . '</div></div>';
				}
			}
		}
		// We dont need everything as we are returning a bajax blish po!
		if ( $_REQUEST[ 'modaction' ] == 'executeadd' )
			die ( $this->blogoversikt );
	?>
	<div>
		<div class="SubContainer" style="padding: 2px" id="mod_blogoverview_content">
			<?= $this->blogoversikt ? $this->blogoversikt : 'Du har ingen blogoversikter i arkivet.' ?>
		</div>
		<div class="SpacerSmallColored"></div>
		<button type="button" onclick="mod_blogoversikt_new()">
			<img src="admin/gfx/icons/newspaper.png"> Endre blogoversikt
		</button>
	</div>

