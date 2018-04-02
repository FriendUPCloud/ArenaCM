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

$db_blog =& dbObject::globalValue('database');
$mtpldir = 'skeleton/modules/mod_blog_overview/templates/';
i18nAddLocalePath ( 'skeleton/modules/mod_blog_overview/locale');

/**
 * Blog engine output
**/
//$lim = $fieldObject->DataInt ? $fieldObject->DataInt : 10;
//	$pos = $_REQUEST[ 'bpos' ] > 0 ? $_REQUEST[ 'bpos' ] : '0';
//	$bid = $_REQUEST[ 'bid' ];
$pos = 0;

if ($field->DataMixed)
{
    if ( list ( $pages, $amounts, $nav ) = explode ( '#', $field->DataMixed ) )
    {
        $pages = explode ( '_', $pages );
        $amounts = explode ( '_', $amounts );
		$nav = explode('_', $nav);
        $blogcontents = array ( );

        for ( $k = 0; $k < count ( $pages ); $k++ )
        {
			$bid = $_REQUEST[ 'bid' ];
			if ($bid == $pages[$k]) $pos = $_REQUEST[ 'bpos' ];
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
			//$lim = $amount;
            
            
            $blogs->addClause('ORDER BY', 'DateUpdated DESC, ID DESC');
			if($cnt > 1) $blogs->addClause ( 'LIMIT', $pos . ',' . $amounts[$k] );


            if ($blogs = $blogs->find())
            {
                $page = new dbObject('ContentElement');
                $page->load($pages[$k]);

                $str .= '<div class="Bold BlogListTitle">' . i18n ( 'Blogarticles from the page' ) . " " . $page->Title . ':</div>';
                foreach($blogs as $blog)
                {
                    if ( !$blogcontents[$blog->ContentElementID] )
                        $blogcontents[$blog->ContentElementID] = new dbContent ( $blog->ContentElementID );
                    $botpl = new cpTemplate($mtpldir . 'web_blogoversiktlist.php');
                    $botpl->cnt =& $blogcontents[$blog->ContentElementID];
                    $botpl->blog =& $blog;
                    $str .= $botpl->render();
                }
				if ($nav[$k] == 'on')
				{
					$keys = $_REQUEST[ 'keywords' ] ? ( '&keywords=' . $_REQUEST[ 'keywords' ] ) : '';
					$next = $prev = $sep = '';
					if ( $pos > 0 && $cnt > 1 )
						$prev = '<a href="' . $content->getUrl ( ) . '?bpos=' . ( !$bid || $pages[$k] == $bid ? $pos - $amounts[$k] : $pos ) . $keys . '&bid=' . $pages[$k] . '" class="Prev"><span>' . i18n ( 'Newer blogs' ) . '</span></a>';
					if ( $pos + $amounts[$k] < $cnt )
						$next = '<a href="' . $content->getUrl ( ) . '?bpos=' . ( !$bid || $pages[$k] == $bid ? $pos + $amounts[$k] : $pos ) . $keys . '&bid=' . $pages[$k] . '" class="Next"><span>' . i18n ( 'Older blogs' ) . '</span></a>';
					if ( $prev && $next ) 
						$sep = ' <span class="Separator">&nbsp;</span> ';
					else $sep = '';
					if ( $prev || $next )
						$str .= '<div id="mod_blog_navigation"><hr/><p>' . $prev . $sep . $next . '</p></div>';
				}
            }
        }
    }
}

if ($botpl)
	$module = $str;

// Navigation

?>
