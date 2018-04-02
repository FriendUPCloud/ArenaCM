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

global $document, $database;
$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
$document->addResource ( 'javascript', 'lib/javascript/bajax.js' );
$document->addResource ( 'javascript', 'lib/javascript/blestbox.js' );
$document->addResource ( 'javascript', 'lib/skeleton/modules/mod_blog_overview/javascript/web.js' );
$db_blog =& dbObject::globalValue('database');
$mtpldir = 'lib/skeleton/modules/mod_blog_overview/templates/';
i18nAddLocalePath ( 'lib/skeleton/modules/mod_blog_overview/locale');

// set up arrays to hold next and prev pos for navigation
$posAr = $pageAr = Array();
if ($_REQUEST['bpos'])
	$posAr= explode('_', $_REQUEST['bpos']);
$str = '';
if ( $pos <= 0 ) $pos = '0';

// New way ---------------------------------------------------------------------
if ( strstr ( $field->DataMixed, '--Version 2.0--' ) )
{
	list ( , $mixed ) = explode ( '<!--Version 2.0-->', $field->DataMixed );
	$globalConf = false;
	if ( $mixed = explode ( '<!--separate-->', $mixed ) )
	{
		$i = 0;
		foreach ( $mixed as $mix )
		{
			if ( $i++ == 0 )
			{
				$globalConf = CreateObjectFromString ( $mix );
			}
			else
			{
				$conf = CreateObjectFromString ( $mix );
				if ( $conf->activated )
				{
					if ( $globalConf->listmode == 'full' )
					{
						$bpage = new dbContent ();
						if ( !$bpage->load ( $conf->activated ) )
							continue;
							
						if ( trim ( $conf->heading ) )
						{
							$module .= '<h2 class="BlogOverviewHeading">' . $conf->heading . '</h2>';
						}
							
						if ( $blogs = $database->fetchObjectRows ( '
							SELECT ID FROM BlogItem WHERE ContentElementID=\'' . $conf->activated . '\' AND IsPublished
							ORDER BY DateUpdated DESC, ID DESC
							LIMIT ' . $conf->quantity . '
						' ) )
						{
							$btpl = new cPTemplate ( $mtpldir . 'web_blog_w_ingress.php' );
							$ii = 1;
							foreach ( $blogs as $blog )
							{
								$bo = new dbObject ( 'BlogItem' ); 
								$bo->load ( $blog->ID );
								$btpl->link = $bpage->getRoute () . '/blogitem/' . $bo->ID . '_' . texttourl ( $bo->Title ) . '.html';
								
								if ( 
									$globalConf->leadinimagewidth > 0 && $globalConf->leadinimageheight > 0 && 
									list ( $image, ) = $bo->getObjects ( 'ObjectType = Image' ) 
								)
								{
									$i = new dbImage ( $image->ID );
									$btpl->image = '<div class="OverviewImage"><a href="' . $btpl->link . '">' . $i->getImageHTML ( 
										$globalConf->leadinimagewidth, $globalConf->leadinimageheight, 'framed' 
									) . '</a></div>';
								}
								else $btpl->image = '';
								if ( trim ( $bo->SubTitle ) )
							    	$bo->Title = '<a href="' . $btpl->link . '">' . $bo->SubTitle . '</a>';
								$btpl->blog = $bo;
								$btpl->item = $ii++;
								$module .= $btpl->render ();
							}
						}
					}
					// List only titles and date
					else if ( $globalConf->listmode == 'titles' || $globalConf->listmode == 'titles_images' )
					{
						$blogcontents = array();
						
						$blogs = new dbObject ('BlogItem');
						$blogs->addClause( 'SELECT', 'ContentElementID, Title' );
					
						// Add search
						if ( $_REQUEST[ 'keywords' ] )
						{
							$keys = explode ( ',', $_REQUEST[ 'keywords' ] );
							foreach ( $keys as $key )
							{
								if ( !trim ( $key ) ) continue;
								$wheres[] = "(Title LIKE \"%$key%\" OR Leadin LIKE \"%$key%\" OR Body LIKE \"%$key%\" OR Tags LIKE \"%$key%\")";
							}
							if ( count ( $wheres ) )
								$blogs->addClause ( 'WHERE', '( ' . implode ( ' OR ', $wheres ) . ' )' );
						}
						
						$blogs->addClause( 'WHERE', 'IsPublished AND DatePublish <= NOW() AND ContentElementID=' . $conf->activated );
						$cnt = $blogs->findCount();
						$amount = $conf->quantity;
						$lim = $conf->quantity;
						$blogs->addClause( 'ORDER BY', 'DateUpdated DESC, ID DESC' );
						$blogs->addClause ( 'LIMIT', $pos . ',' . $lim );
						$bpage = new dbObject( 'ContentElement' );
						$bpage->load( $conf->activated );
						
						$num = 0;
						if ( $blogs = $blogs->find() )
						{
							$str .= '<div class="Bold BlogListTitle">' . 
								i18n ( $conf->heading ? $conf->heading : ( 'Blogarticles from the page' ) . $bpage->Title . ':' ) . '</div>';
							
							$botpl = new cPTemplate($mtpldir . 'web_blogoversiktlist.php');
							foreach( $blogs as $blog )
							{
							    if ( !$blogcontents[$blog->ContentElementID] )
							        $blogcontents[$blog->ContentElementID] = new dbContent ( $blog->ContentElementID );
							    if ( trim ( $blog->SubTitle ) )
							    	$blog->Title = $blog->SubTitle;
							    $botpl->num = ' Row' . ++$num;
							    $botpl->cnt =& $blogcontents[$blog->ContentElementID];
							    $botpl->blog =& $blog;
							    $botpl->date = ArenaDate ( DATE_FORMAT, $blog->DatePublish );
								if ( 
									$globalConf->leadinimagewidth > 0 && $globalConf->leadinimageheight > 0 && 
									list ( $image, ) = $blog->getObjects ( 'ObjectType = Image' ) 
								)
								{
									$i = new dbImage ( $image->ID );
									$botpl->image = '<div class="OverviewImage">' . $i->getImageHTML ( 
										$globalConf->leadinimagewidth, $globalConf->leadinimageheight, 'framed' 
									) . '</div>';
								}
								else $botpl->image = '';
								$str .= $botpl->render();
							}
			
							// navigation
							$nextPos = $prevPos = $posAr;
							if ($pos > 0) $prevPos[$k] = $pos - $lim;
							$prevPos = join('_', $prevPos);
							if ($pos + $lim < $cnt) $nextPos[$k] = $pos + $lim;
							$nextPos = join('_', $nextPos);
			
							$keys = $_REQUEST[ 'keywords' ] ? ( '&keywords=' . $_REQUEST[ 'keywords' ] ) : '';
							$next = $prev = $sep = '';

							if ($pos > 0)
								$prev = '<a href="javascript:blog_navigate(\'' . $prevPos . $keys . '\')" class="Prev"><span>' . i18n ('Newer blogs') . '</span></a>';
							if ($pos + $lim < $cnt)
								$next = '<a href="javascript:blog_navigate(\'' . $nextPos . $keys . '\')" class="Next"><span>' . i18n ('Older blogs') . '</span></a>';
							if ($prev && $next) 
								$sep = ' <span class="Separator">&nbsp;</span> ';
							else $sep = '';
							if ($prev || $next)
								$str .= '<div id="mod_blog_navigation"><hr/><p>' . $prev . $sep . $next . '</p></div>';
					
						}
					}
				}
			}
		}
	}
}
// Old sad way -----------------------------------------------------------------
else if ( $field->DataMixed )
{
    
    if (list($pages, $amounts, $navigations, $headings, $listmode, $imagesizex, $imagesizey ) = explode('#', $field->DataMixed))
    {
    	if ( !$listmode ) $listmode == 'titles';
    	$pages = explode('_', $pages);
	    $amounts = explode('_', $amounts);
	    if ( $headings = explode ( "\t\t", $headings ) )
	        foreach ( $headings as $k=>$v ) $headings[$k] = str_replace ( "%hash%", "#", $v );
	            
    	// Full listmode
		if ( $listmode == 'full' )
		{
			$len = count ( $pages );
			for ( $a = 0; $a < $len; $a++ )
			{
				$bpage = new dbContent ();
				if ( !$bpage->load ( $pages[$a] ) )
					continue;
				
				if ( $blogs = $database->fetchObjectRows ( '
					SELECT ID FROM BlogItem WHERE ContentElementID=\'' . $pages[$a] . '\' 
					ORDER BY DateUpdated DESC, ID DESC
					LIMIT ' . $amounts[$a] . '
				' ) )
				{
					$btpl = new cPTemplate ( $mtpldir . 'web_blog_w_ingress.php' );
					$ii = 1;
					foreach ( $blogs as $blog )
					{
						$bo = new dbObject ( 'BlogItem' ); $bo->load ( $blog->ID );
						if ( $imagesizey > 0 && $imagesizex > 0 && list ( $image, ) = $bo->getObjects ( 'ObjectType = Image' ) )
						{
							$i = new dbImage ( $image->ID );
							$btpl->image = '<div class="OverviewImage">' . $i->getImageHTML ( $imagesizex, $imagesizey, 'framed' ) . '</div>';
						}
						else $btpl->image = '';
						$btpl->blog = $bo;
						$btpl->item = $ii++;
						$btpl->link = $bpage->getRoute () . '/blogitem/' . $bo->ID . '_' . texttourl ( $bo->Title ) . '.html';
						$module .= $btpl->render ();
					}
				}
			}
		}
    	// List only titles and date
    	else 
    	{
		    $blogcontents = array();
		    $hc = count ( $headings ) && $headings[0];

			if (!$posAr)
			{
				for ($k = 0; $k < count($pages); $k++ )
				{
					$posAr[$k] = 0;
				}
			}
		    for ($k = 0; $k < count($pages); $k++)
		    {
				$pos = $posAr[$k];
		        $blogs = new dbObject ('BlogItem');
		        $blogs->addClause('SELECT', 'ContentElementID, Title');
		        
		        // Add search
		        if ( $_REQUEST[ 'keywords' ] )
		        {
		        	$keys = explode ( ',', $_REQUEST[ 'keywords' ] );
		        	foreach ( $keys as $key )
		        	{
		        		if ( !trim ( $key ) ) continue;
		        		$wheres[] = "(Title LIKE \"%$key%\" OR Leadin LIKE \"%$key%\" OR Body LIKE \"%$key%\" OR Tags LIKE \"%$key%\")";
		        	}
		        	if ( count ( $wheres ) )
		        		$blogs->addClause ( 'WHERE', '( ' . implode ( ' OR ', $wheres ) . ' )' );
		        }
		        
		        $blogs->addClause('WHERE', 'IsPublished AND DatePublish <= NOW() AND ContentElementID=' . $pages[$k]);
		        $cnt = $blogs->findCount();
				$amount = $amounts[$k];
				$lim = $amount;
		      $blogs->addClause('ORDER BY', 'DateUpdated DESC, ID DESC');
				$blogs->addClause ( 'LIMIT', $pos . ',' . $lim );

				$num = 0;
		        if ($blogs = $blogs->find())
		        {
		            $bpage = new dbObject('ContentElement');
		            $bpage->load($pages[$k]);

				
		            $str .= '<div class="Bold BlogListTitle Row' . ++$num . '">' . i18n ( $hc ? $headings[$k] : 'Blogarticles from the page' ) . ( $hc ? '' : $bpage->Title ) . ':</div>';
		            foreach($blogs as $blog)
		            {
		                if ( !$blogcontents[$blog->ContentElementID] )
		                    $blogcontents[$blog->ContentElementID] = new dbContent ( $blog->ContentElementID );
		                $botpl = new cPTemplate($mtpldir . 'web_blogoversiktlist.php');
		                $botpl->cnt =& $blogcontents[$blog->ContentElementID];
		                $botpl->blog =& $blog;
		                $botpl->date = ArenaDate ( DATE_FORMAT, $blog->DatePublish );
		                $str .= $botpl->render();
		            }
				
					// navigation
					$nextPos = $prevPos = $posAr;
					if ($pos > 0) $prevPos[$k] = $pos - $lim;
					$prevPos = join('_', $prevPos);
					if ($pos + $lim < $cnt) $nextPos[$k] = $pos + $lim;
					$nextPos = join('_', $nextPos);
				
					$keys = $_REQUEST[ 'keywords' ] ? ( '&keywords=' . $_REQUEST[ 'keywords' ] ) : '';
					$next = $prev = $sep = '';

					if ($pos > 0)
						$prev = '<a href="javascript:blog_navigate(\'' . $prevPos . $keys . '\')" class="Prev"><span>' . i18n ('Newer blogs') . '</span></a>';
					if ($pos + $lim < $cnt)
						$next = '<a href="javascript:blog_navigate(\'' . $nextPos . $keys . '\')" class="Next"><span>' . i18n ('Older blogs') . '</span></a>';
					if ($prev && $next) 
						$sep = ' <span class="Separator">&nbsp;</span> ';
					else $sep = '';
					if ($prev || $next)
						$str .= '<div id="mod_blog_navigation"><hr/><p>' . $prev . $sep . $next . '</p></div>';
		        }
		    }
		}
    }
}
if ($_REQUEST['bpos'])
	die($str);
else if ($botpl)
	$module = $str;
?>
