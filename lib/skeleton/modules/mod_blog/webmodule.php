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

global $webuser, $document, $page;

/**
 * Blog engine output
**/
$bid = 0;
$lim = $fieldObject->DataInt ? $fieldObject->DataInt : 10;
$pos = $_REQUEST[ 'blogpos' ] > 0 ? $_REQUEST[ 'blogpos' ] : '0';

// Add language
i18nAddLocalePath ( 'lib/skeleton/modules/mod_blog/locale' );

// Check captcha
if ( $_REQUEST[ 'checkcaptcha' ] )
{
	if ( $_REQUEST[ 'c' ] == $_SESSION[ 'captcha_value' ] )
	{
		ob_clean ( );
		die ( 'ok' );
	}
}

// Some other config vars
$cfg = explode ( "\t", $fieldObject->DataMixed );
$cfgComments = $cfg[0];
$cfgShowAuthor = $cfg[1];
$cfgTagbox = $cfg[2];
$cfgTagboxPlacement = $cfg[3];
$cfgSearchbox = $cfg[4];
$cfgDetailpage = $cfg[5];
$cfgSourcepage = $cfg[6];
$cfgLeadinlength = $cfg[7];
$cfgTitlelength = $cfg[8];
$cfgSizeX = $cfg[9];
$cfgSizeY = $cfg[10];
$cfgHeaderText = $cfg[11];
$cfgHideDetails = $cfg[12];
$cfgFacebookLike = $cfg[13];
list ( $cfgFacebookLikeWidth, $cfgFacebookLikeHeight, ) = explode ( ':', $cfg[14] );
$cfgListMethod = $cfg[15];
$cfgLSizeX = $cfg[16];
$cfgLSizeY = $cfg[17];
$cfgImageAspect = trim ( $cfg[18] ) ? $cfg[18] : 'proximity';
$cfgImagebgColor = trim ( $cfg[19] ) ? str_replace ( '#', '', $cfg[19] ) : '000000';
$cfgGalleryMode = $cfg[20];
$cfgFBComments = $cfg[21];
$cfgTagfilter = $cfg[22];
$cfgImageAspectDtl = trim ( $cfg[23] ) ? $cfg[23] : 'proximity';
$cfgImagebgColorDtl = trim ( $cfg[24] ) ? str_replace ( '#', '', $cfg[24] ) : '000000';
$cfgPagination = $cfg[25]?true:false;

// Check if a user has permission to comment
$canComment = $webuser ? $webuser->checkPermission ( $content, 'Write' ) : dbUser::checkGlobalPermission ( $content, 'Write' );

// Source and detailpages
$sourcepage = new dbContent ( );
if ( $cfgSourcepage ) $sourcepage->load ( $cfgSourcepage );
else $sourcepage->load ( $content->MainID );
$detailpage = false;
if ( $cfgDetailpage > 0 )
{
	$detailpage = new dbContent ( );
	$detailpage->load ( $cfgDetailpage );	
}
else
{
	$detailpage =& $page;
}

// Blog details
if ( preg_match ( '/.*?\/blogitem\/([0-9]*?)\_.*?/', $_REQUEST[ 'route' ], $matches ) && $cfgHideDetails != 1 )
{
	$GLOBALS[ 'document' ]->addHeadScript ( 'lib/javascript/arena-lib.js' );
	$GLOBALS[ 'document' ]->addHeadScript ( 'lib/javascript/bajax.js' );
	$GLOBALS[ 'document' ]->addHeadScript ( 'lib/skeleton/modules/mod_blog/javascript/web.js' );
	$bid = $matches[ 1 ];
	$blog = new dbObject ( 'BlogItem' );
	$blog->load ( $bid );
	
	// If we're using a different page
	if ( $detailpage && $content->MainID != $detailpage->MainID )
	{
		ob_clean ( );
		header ( 'Location: ' . BASE_URL . $detailpage->getRoute ( ) . '/blogitem/' . $matches[1] . '_' . texttourl ( $blog->Title ) . '.html' );
		die ( );
	}
	
	// Receive post
	if ( $_POST[ 'Message' ] && $_REQUEST[ 'Captcha' ] == $_SESSION[ 'captcha_value' ] )
	{
		$comment = new dbObject ( 'Comment' );
		$comment->Message = $_POST[ 'Message' ];
		$comment->Nickname = $_POST[ 'Name' ];
		$comment->ElementTable = 'BlogItem';
		$comment->ElementID = $blog->ID;
		$comment->UserID = $GLOBALS[ 'webuser' ]->ID ? $GLOBALS[ 'webuser' ]->ID : 0;
		$comment->DateCreated = date ( 'Y-m-d H:i:s' );
		$comment->DateModified = date ( 'Y-m-d H:i:s' );
		$comment->Subject = $_POST[ 'Subject' ];
		$comment->save ( );
		
		ob_clean ();
		$_SESSION[ 'captcha_value' ] = '';
		header ( 'Location: ' . $content->getRoute ( ) . '/blogitem/' . $bid . '_' . texttourl ( $blog->Title ) . '.html' );
		die ( );
	}
	$btpl = new cPTemplate ( 'lib/skeleton/modules/mod_blog/templates/web_blog.php' );
	
	// For facebook
	$document->sHeadData[] = '
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
	  var js, fjs = d.getElementsByTagName(s)[0];
	  if (d.getElementById(id)) return;
	  js = d.createElement(s); js.id = id;
	  js.src = "//connect.facebook.net/nb_NO/all.js#xfbml=1";
	  fjs.parentNode.insertBefore(js, fjs);
	}(document, \'script\', \'facebook-jssdk\'));</script>
	';
	
	$btpl->image = false;
	$btpl->fbcomments = $cfgFBComments;
	$btpl->ImageAspectDtl = $blog->DetailScalemode == 'default' ? $cfgImageAspectDtl : $blog->DetailScalemode;
	$btpl->ImagebgColorDtl = '0x' . $cfgImagebgColorDtl;
	if ( $imgs = $blog->getObjects ( 'ObjectType = Image' ) )
	{
		$detailfound = false;
		foreach ( $imgs as $img )
		{
			if ( $img->Title == 'DetailImage' )
			{
				$detailfound = true;
				$btpl->image = $img;
			}
			else if ( !$detailfound )
			{
				$btpl->image = $img;
			}
		}
	}
	
	$btpl->blog =& $blog;
	$btpl->cfgComments = $cfgComments;
	$btpl->canComment = $canComment;
	$btpl->cfgShowAuthor = $cfgShowAuthor;
	$btpl->content =& $content;
	$btpl->sizeX = $cfgLSizeX > 0 ? $cfgLSizeX : $cfgSizeX;
	$btpl->sizeY = $cfgLSizeY > 0 ? $cfgLSizeY : $cfgSizeY;
	$btpl->sourcepage =& $sourcepage;
	$btpl->comments = '';
	
	// Set page title
	$document->sTitle = SITE_TITLE . ' - ' . $blog->Title;
	
	// Gallery support using gallery module
	if ( $flds = $blog->getObjects ( 'ObjectType = Folder' ) )
	{
		$document->addResource ( 'javascript', 'lib/skeleton/modules/mod_gallery/javascript/web.js' );
		$ids = array ();
		foreach ( $flds as $fld )
		{
			$ids[] = $fld->ID;
		}
		$img = new dbImage ();
		$img->addClause ( 'WHERE', 'ImageFolder IN ( ' . implode ( ',', $ids ) . ' )' );
		$img->addClause ( 'ORDER BY', 'SortOrder ASC, ID DESC' );
		if ( $img = $img->find () )
		{
			$settings = new stdclass ();
			$settings->Folders = implode ( ':', $ids );
			$settings->Header = '';
			$settings->ThumbWidth = floor ( $cfgLSizeX / 4 );
			$settings->ThumbHeight = floor ( $cfgLSizeY / 4 );
			$settings->DetailWidth = $settings->Width = $cfgLSizeX;
			$settings->DetailHeight = $settings->Height = $cfgLSizeY;
			$settings->LightboxDescriptions = true;
			$settings->ThumbColumns = 4;
			if ( $cfgGalleryMode == 'slideshow' )
			{
				$settings->currentMode = 'slideshow';
				$settings->ShowStyle = 'showstyle_showroom';
				$mtpldir = 'lib/skeleton/modules/mod_gallery/templates/';
				include ( 'lib/skeleton/modules/mod_gallery/include/web_slideshow.php' );
				$btpl->gallery = '<div class="Gallery">' . $mtpl->render () . '</div>';
			}
			else if ( $cfgGalleryMode == 'gallery' )
			{
				$settings->currentMode = 'gallery';
				$settings->ShowStyle = 'showstyle_gallery';
				$mtpldir = 'lib/skeleton/modules/mod_gallery/templates/';
				include ( 'lib/skeleton/modules/mod_gallery/include/web_gallery.php' );
				$btpl->gallery = '<div class="Gallery">' . $mtpl->render () . '</div>';
			}
		}
	}
	
	// Facebook
	$btpl->facebookLike = $cfgFacebookLike;
	$btpl->facebookLikeWidth = $cfgFacebookLikeWidth;
	$btpl->facebookLikeHeight = $cfgFacebookLikeHeight;
	$btpl->facebookLikeUrl = BASE_URL . $content->getRoute ( ) . '/blogitem/' . $bid . '_' . texttourl ( $blog->Title );
	
	if ( $cfgComments )
	{
		$comment = new dbObject ( 'Comment' );
		$comment->ElementTable = 'BlogItem';
		$comment->ElementID = $blog->ID;
		$comment->addClause ( 'ORDER BY', 'DateUpdated ASC, ID DESC' );
	
		if ( $comments = $comment->find ( ) )
		{
			$ctpl = new cPTemplate ( 'lib/skeleton/modules/mod_blog/templates/web_blog_comment.php' );
			$str = '';
			foreach ( $comments as $comment )
			{
				$ctpl->comment =& $comment;
				$str .= $ctpl->render ( );
			}
			$btpl->comments = $str;
		}
	}

	// Open graph tags
	if( $btpl->image )
		$document->sHeadData[] = "\t\t<meta property=\"og:image\" content=\"".$btpl->image->getImageUrl()."\"/>";
	$document->sHeadData[] = "\t\t<meta property=\"og:title\" content=\"".$blog->Title."\"/>";
	$document->sHeadData[] = "\t\t<meta property=\"og:url\" content=\"".BASE_URL . $detailpage->getRoute ( ) . '/blogitem/' . $matches[1] . '_' . texttourl ( $blog->Title ) . '.html'."\"/>";
	$document->sHeadData[] = "\t\t<meta property=\"og:type\" content=\"blog\"/>";
	
	$module = $btpl->render ( );

	// Increment view count on a per-session basis
	if( isset( $blog->Views ) && !isset( $_SESSION['mod_blog_viewed'][$blog->ID] ) )
	{
		$blog->Views++;
		$blog->save();
		$_SESSION['mod_blog_viewed'][$blog->ID] = true;
	}
	
}
// List all blogs
else
{
	// List out tagbox
	if ( $cfgTagbox )
	{
		$tags = '';
		if ( $tagO = $GLOBALS[ 'database' ]->fetchObjectRows ( '
			SELECT DISTINCT(b.Tags) c FROM BlogItem b WHERE b.ContentElementID=' . $sourcepage->MainID . '
		' ) )
		{
			$options = array ();
			foreach ( $tagO as $t )
			{
				$t = explode ( ',', str_replace ( ' ', ',', trim ( $t->c ) ) );
				foreach ( $t as $st ) $options[$st] = $st;
			}
			if ( count ( $options ) )
			{
				$tags .= '<label>' . i18n ( 'Tagbox_Tags' ) . '<span>:</span></label>';
				foreach ( $options as $t )
				{
					if ( !trim ( $t ) ) continue;
					if ( isset ( $_REQUEST['tag'] ) && trim ( $t ) == $_REQUEST[ 'tag' ] )
						$w = ' class="current"';
					else $w = '';
					$tags .= '<a href="' . $sourcepage->getUrl () . '?tag=' . $t . '"' . $w . '><span>' . $t . '</span></a>, ';
				}
				$tags = substr ( $tags, 0, strlen ( $tags ) - 2 );
			}
		}
		$content->addExtraField ( 'Tagbox', 'text', $cfgTagboxPlacement, $tags );
	}
	
	$blogs = new dbObject ( 'BlogItem' );
	$blogs->addClause ( 'WHERE', 'ContentElementID=' . $sourcepage->MainID );
	if ( $_REQUEST[ 'month' ] )
	{
		$month = date ( 'Y-m', $_REQUEST[ 'month' ] );
		$nmonth = date ( 'Y-m', strtotime ( $month . '-01' ) + 2764800 );
		$blogs->addClause ( 'WHERE', 'IsPublished AND DatePublish <= NOW() AND DatePublish >= \'' . $month . '-01\' AND DatePublish < \'' . $nmonth . '-01\'' ); 
	}
	else
	{
		$blogs->addClause ( 'WHERE', 'IsPublished AND DatePublish <= NOW()' );
	}
	// Be mindful of the tags!
	if ( $cfgTagbox && isset ( $_REQUEST[ 'tag' ] ) )
	{
		$t = strtolower ( str_replace ( ',', '', $_REQUEST[ 'tag' ] ) );
		$blogs->addClause ( 'WHERE', 'Tags LIKE "%' . mysql_real_escape_string( $t ) . '%"' );
	}
	else if ( $cfgTagbox && isset ( $_REQUEST[ 'tags' ] ) )
	{
		if ( $tags = explode ( ',', $_REQUEST[ 'tags' ] ) )
		{
			$wh = array ();
			foreach ( $tags as $t )
			{
				if ( !trim ( $t ) ) continue;
				$wh[] = '( Tags LIKE "%' . trim ( $t ) . '%" )';
			}
			if ( count ( $wh ) )
				$blogs->addClause ( 'WHERE', '(' . implode ( ' OR ', $wh ) . ')' );
		}
	}
	// Filter on tagfilter!
	if ( trim ( $cfgTagfilter ) )
	{
		if ( $tags = explode ( ',', $cfgTagfilter ) )
		{
			$wh = array ();
			foreach ( $tags as $t )
			{
				if ( !trim ( $t ) ) continue;
				$wh[] = '( Tags LIKE "%' . trim ( $t ) . '%" )';
			}
			if ( count ( $wh ) )
				$blogs->addClause ( 'WHERE', '(' . implode ( ' OR ', $wh ) . ')' );
		}
	}
	$cnt = $blogs->findCount ( );
	if ( $cfgListMethod == 'random' )
		$blogs->addClause ( 'ORDER BY', 'RAND()' );
	else $blogs->addClause ( 'ORDER BY', 'SortOrder ASC, DateUpdated DESC, ID DESC' );
	$blogs->addClause ( 'LIMIT', $pos . ',' . $lim );
	
	if ( $blogs = $blogs->find () )
	{
		$blogtpl = new cPTemplate ( 'lib/skeleton/modules/mod_blog/templates/web_blog_listed.php' );
		$str = '';
		if ( trim ( $cfgHeaderText ) )
			$str .= '<h2 class="BlogListHeader">' . $cfgHeaderText . '</h2>';
		// Add numbers so one can change the first ten items with extraclass
		$num = -1;
		$numbers = Array ( 'first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'tenth' );
		foreach ( $blogs as $blog )
		{
			$btpl = clone ( $blogtpl );
			$img = false;
			$btpl->ReadMore = true;
			$link = ( $detailpage ? $detailpage->getRoute ( ) : $content->getRoute ( ) ) . '/blogitem/' . $blog->ID . '_' . texttourl ( $blog->Title );
			if ( $blog->ExternalLink ) $link = $blog->ExternalLink;
			$blog->ReadMoreLink = $link;
			
			if ( !trim ( strip_tags ( $blog->Body ) ) && !$blog->ExternalLink )
				$btpl->ReadMore = false;
			
			if ( trim ( $blog->SubTitle ) )
				$blog->Title = $blog->SubTitle;
			
			$btpl->image = false;
			$btpl->imgtag = false;
			$imgtag = false;
			if ( $cfgImageAspect == 'imgtag' )
				$imgtag = true;
			if ( $imgs = $blog->getObjects ( 'ObjectType = Image' ) )
			{
				foreach ( $imgs as $img )
				{
					if ( $img->Title != 'DetailImage' )
					{
						if ( $imgtag ) $btpl->imagetag = '<div><a href="' . $link . '"' . ( $blog->ExternalLink ? ' target="_blank"' : '' ) . '>' . $img->getImageHTML ( $cfgSizeX, $cfgSizeY, 'proximity' ) . '</a></div>';
						else $btpl->image = $img;
					}
				}
			}
			if ( !$btpl->image )
			{
				if ( $flds = $blog->getObjects ( 'ObjectType = Folder' ) )
				{
					$ids = array ();
					foreach ( $flds as $fld )
					{
						$ids[] = $fld->ID;
					}
					$img = new dbImage ();
					$img->addClause ( 'WHERE', 'ImageFolder IN ( ' . implode ( ',', $ids ) . ' )' );
					$img->addClause ( 'ORDER BY', 'SortOrder ASC, ID DESC' );
					if ( $img = $img->findSingle () )
					{
						if ( $imgtag ) $btpl->imagetag = $img->getImageHTML ( $cfgSizeX, $cfgSizeY, 'proximity' );
						else $btpl->image = $img;
					}
				}
			}
			$btpl->blog =& $blog;
			$btpl->ImageAspect = $blog->LeadinScalemode == 'default' ? $cfgImageAspect : $blog->LeadinScalemode;
			$btpl->ImagebgColor = '0x' . $cfgImagebgColor;
			$btpl->extraClass = ++$num <= 10 ? ( ' ' . $numbers[ $num ] ) : '';
			$btpl->content =& $content;
			if ( $cfgComments )
			{
				$comment = new dbObject ( 'Comment' );
				$comment->ElementTable = 'BlogItem';
				$comment->ElementID = $blog->ID;
				$btpl->commentcount = $comment->findCount ( );
			}
			$btpl->facebookLike = $cfgFacebookLike;
			if ( $btpl->facebookLike == 1 )
			{
				$btpl->facebookLikeUrl = BASE_URL . $sourcepage->getRoute ( ) . '/blogitem/' . $blog->ID . '_' . texttourl ( $blog->Title ) . '.html';
				$btpl->facebookLikeWidth = $cfgFacebookLikeWidth;
				$btpl->facebookLikeHeight = $cfgFacebookLikeHeight;
			}
			$btpl->cfgComments = $cfgComments;
			$btpl->canComment = $canComment;
			$btpl->cfgShowAuthor = $cfgShowAuthor;
			$btpl->detailpage = $detailpage;
			$btpl->leadinLength = $cfgLeadinlength;
			$btpl->titleLength = $cfgTitlelength;
			$btpl->sizeX = $cfgSizeX;
			$btpl->sizeY = $cfgSizeY;
			$btpl->hideDetails = $cfgHideDetails;
			
			// Show tags
			$btpl->blogTags = '';
			if ( $cfgTagboxPlacement )
			{
				$url = explode ( '?', $_REQUEST[ 'route' ] );
				$url = $url[0];
				$btpl->blogTags = utf8_encode ( preg_replace ( '/([^\s]*)/i', '<a href="' . htmlentities( $url ) . '?tag=$1">$1</a>', utf8_decode ( $blog->Tags ) ) );
			}
			
			$str .= $btpl->render ( );
		}
		$module = $str;
		unset ( $str );
		
		// Navigation
		$curl = $content->getUrl ();
		if ( $cfgTagbox )
			$tagOp = isset ( $_REQUEST['tag'] ) ? ( '&tag=' . htmlentities( $_REQUEST['tag'] ) ) : '';
		else $tagOp = '';
		$next = $prev = $sep = '';
		if ( $pos > 0 )
			$prev = '<a href="' . $curl . '?blogpos=' . ( $pos - $lim ) . $tagOp . '" class="Prev"><span>' . i18n ( 'Newer blogs' ) . '</span></a>';
		if ( $pos + $lim < $cnt )
			$next = '<a href="' . $curl . '?blogpos=' . ( $pos + $lim ) . $tagOp . '" class="Next"><span>' . i18n ( 'Older blogs' ) . '</span></a>';
		if ( $prev && $next ) 
			$sep = ' <span class="Separator">&nbsp;</span> ';
		else $sep = '';
		$pages = '';
		if ( $cfgPagination )
		{
			$cp = floor ( (int)$pos / $lim ) + 1;
			$i = 1;
			for ( $a = 0; $a < $cnt; $a += $lim, $i++ )
			{
				if ( $cp == $i )
				{
					$pages .= '<a href="' . $curl . '?blogpos=' . $a . '" class="Page Current"><span>' . $i . '</span></a>';
				}
				else
				{
					$pages .= '<a href="' . $curl . '?blogpos=' . $a . '" class="Page"><span>' . $i . '</span></a>';
				}
			}
			if ( $pages ) $pages = $sep . $pages;
		}
		if ( $prev || $next )
			$module .= '<div id="mod_blog_navigation"><hr/><p>' . $prev . $sep . $next . $pages . '</p></div>';
	}
	else $module = '<p>' . i18n ( 'i18n_No_blogs' ) . '</p>';
}
?>
