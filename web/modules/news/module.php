<?


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



/**
 * Prereq
**/

include_once ( 'lib/classes/time/ctime.php' );
include_once ( 'lib/classes/dbObjects/dbComment.php' );
include_once ( 'lib/classes/pagination/cpagination.php' );

$time = new cTime ( );

/**
 * Parse config 
**/
$config = explode ( NL, $content->Intro );
$confObj = new Dummy ( );
foreach ( $config as $ni )
{
	list ( $k, $v ) = explode ( "\t", $ni );
	if ( $k && $v )
	{
		$confObj->$k = $v;
		switch ( $k )
		{
			case 'newscategories': $conf[ $k ] = explode ( ',', $v ); break;
			default: $conf[ $k ] = $v; break;
		}
	}
}

/**
 * Actions
**/

$error = false;

switch ( $_REQUEST[ "action" ] )
{
	case "post":
		
		if ( $_POST[ 'captcha' ] != $_SESSION[ 'captcha' ] )
			$error = 'captcha';
		
		if ( $_POST[ 'Controlnumber' ] != 'verified_and_comment' )
			$error = 'controlnumber';
		
		if ( $_POST[ 'Control' ] == $GLOBALS[ 'Session' ]->news_comment_post )
			$error = 'control';
		
		if ( $error == false )
		{
			$news = new dbObject ( 'News' );
			if ( $news->load ( $_POST[ 'nid' ] ) )
			{
				$comment = new dbComment ( );
				$comment->UserID = $GLOBALS[ 'webuser' ]->ID;
				$comment->DateCreated = date ( 'Y-m-d H:i:s' );
				$comment->DateModified = date ( 'Y-m-d H:i:s' );
				$comment->receiveForm ( $_POST );
				$comment->SaveOnObject ( $news );
				$GLOBALS[ 'Session' ]->Set ( 'news_comment_post', $_POST[ 'Control' ] );
				$GLOBALS[ 'postsuccess' ] = true;
				$_REQUEST[ 'Message' ] = '';
				$_REQUEST[ 'Nickname' ] = '';
				$_REQUEST[ 'Subject' ] = '';
			}
		}
		
		$GLOBALS[ 'newsposterror' ] = $error;
		
		break;
}

/**
 * Parse some request variables used for page position etc 
**/
$npos = $_REQUEST[ 'newspos' ];
if ( $npos <= 0 ) $npos = '0';

/**
 * Go into listing mode 
**/
if ( !$_REQUEST[ 'nid' ] )
{
	/**
	* Make sql query
	**/
	$search = Array ( );
	foreach ( $conf[ 'newscategories' ] as $n )
	{
		$search[] = "CategoryID='{$n}'";
	}
	
	$query = "
	SELECT * FROM 
		News 
	WHERE 
		( " . implode ( " OR ", $search ) . " ) 
	AND 
		( 
			( IsEvent AND DateFrom >= NOW() AND DateTo < NOW() ) 
			OR 
			!IsEvent 
		) 
	AND !IsDeleted AND IsPublished
	ORDER BY DateActual DESC, ID DESC
	" . ( $conf[ "prpage" ] > 0 ? "LIMIT $npos, {$conf["prpage"]}" : '' ) . "
	";
	
	/**
	 * Find how many news items there are total
	**/
	list ( $countq, ) = explode ( 'ORDER BY', $query );
	$countq = str_replace ( '*', 'COUNT(*)', $countq );
	$db =& dbObject::globalValue ( 'database' );
	list ( $totalcount, ) = $db->fetchRow ( $countq );
	
	$news = new dbObject ( 'News' );
	$oStr = '';
	if ( $news = $news->find ( $query ) )
	{
		$ntpl = new cPTemplate ( );
		$ntpl = new cPTemplate ( $ntpl->findTemplate ( 'news_listing.php', array ( 'templates/', 'web/templates/modules/news/' ) ) );
		$ntpl->time = $time;
		
		foreach ( $news as $n )
		{
			$ntpl->data = $n;
			if ( !$cats[ $n->CategoryID ] )
			{
				$cats[ $n->CategoryID ] = new dbObject ( 'NewsCategory' );
				$cats[ $n->CategoryID ]->load ( $n->CategoryID );
			}
			$ntpl->data->Category = $cats[ $n->CategoryID ];
			if ( $ntpl->data->Category->ContentElementID )
			{
				if ( !$paths[ $ntpl->data->Category->ContentElementID ] )
				{
					$paths[ $ntpl->data->Category->ContentElementID ] = new dbContent ( );
					$paths[ $ntpl->data->Category->ContentElementID ]->load ( $ntpl->data->Category->ContentElementID );
					$paths[ $ntpl->data->Category->ContentElementID ] = $paths[ $ntpl->data->Category->ContentElementID ]->getPath ( );
				}
			}
			if ( !$languages[ $cats[ $n->CategoryID ]->Language ] )
			{
				$languages[ $cats[ $n->CategoryID ]->Language ] = new dbObject ( 'Languages' );
				$languages[ $cats[ $n->CategoryID ]->Language ]->load ( $cats[ $n->CategoryID ]->Language );
			}
			$ntpl->data->LanguageCode = $languages[ $cats[ $n->CategoryID ]->Language ]->Name;
			$ntpl->data->FormattedDate = $time->interpretFormat ( 
				$ntpl->data->DateActual, 
				$ntpl->data->Category->DateFormat, 
				$ntpl->data->LanguageCode
			);
			$ntpl->comments = $conf[ "comments" ];
			$count = dbComment::CountComments ( $n );
			$ntpl->replycount = ( $count > 1 || $count <= 0 ) ? ( $count . ' ' . i18n ( 'comments' ) ) : ( '1 ' . i18n ( 'comment' ) );
			$ntpl->path = $paths[ $ntpl->data->Category->ContentElementID ] ? BASE_URL . $paths[ $ntpl->data->Category->ContentElementID ] : BASE_URL;
			$ntpl->config =& $confObj;
			
			/* Translations */
			if ( $translations = $db->fetchObjectRows ( '
				SELECT n2.*, l.NativeName FROM News n, News n2, ObjectConnection oc, Languages l, NewsCategory c
				WHERE 
					oc.ObjectID = n.ID AND oc.ObjectType = "News" AND 
					oc.ConnectedObjectID = n2.ID AND oc.ConnectedObjectType = "News" AND
					oc.Label = "Translation" AND n.ID = \'' . $n->ID . '\' AND
					c.ID = n2.CategoryID AND
					l.ID = c.Language
				ORDER BY 
					n2.DateActual DESC, n2.ID DESC
			' ) )
			{
				$ostr = Array ( );
				foreach ( $translations as $t )
				{
					$ostr[] = '<a href="' . $ntpl->path . '?nid=' . $t->ID . '">' . $t->NativeName . '</a>';
				}
				$ntpl->translations = ' | ' . i18n ( 'Read this in' ) . ' ' . implode ( ' | ', $ostr );
			} else $ntpl->translations = false;
			
			$oStr .= $ntpl->render ( );
		}
	
		$module = $oStr;
		
		/**
		* Pagination
		**/
		$pagination = new cPagination ( );
		$pagination->Limit = $conf[ 'prpage' ];
		$pagination->Position = $npos;
		$pagination->Count = $totalcount;
		$pagination->Template = $ntpl->findTemplate ( 'navigation.php', array ( 'templates/', 'web/templates/modules/news/' ) );
		$pagination->Content =& $content;
		$module .= $pagination->render ( );
	}
	else
	{
		$module = i18n('No news..');
	}
}
/**
 * Go into detail mode
**/
else
{
	$ntpl = new cPTemplate ( );
	$ntpl = new cPTemplate ( $ntpl->findTemplate ( 'news_detail.php', array ( 'templates/', 'web/templates/modules/news/' ) ) );
	$news = new dbObject ( 'News' );
	$news->load ( $_REQUEST[ 'nid' ] );
	$ntpl->data =& $news;
	$ntpl->time = $time;
	
	$cat = new dbObject ( 'NewsCategory' );
	$cat->load ( $news->CategoryID );
	$ntpl->data->Category = $cat;
	
	if ( $cat->ContentElementID )
	{
		$cnt = new dbContent ( );
		$cnt->load ( $cat->ContentElementID );
		$ntpl->path = $cnt->getPath ( );
	}
	
	$language = new dbObject ( 'Languages' );
	$language->load ( $cat->Language );
	$ntpl->data->LanguageCode = $language->Name;
	
	$ntpl->data->FormattedDate = $time->interpretFormat ( 
		$ntpl->data->DateActual, 
		$ntpl->data->Category->DateFormat, 
		$ntpl->data->LanguageCode
	);
	
	$ntpl->config =& $confObj;
	
	if ( ( $count = dbComment::CountComments ( $news ) ) > 0 )
	{
		$ntpl->commentcount = $count;
		$ctpl = new cPTemplate ( $ntpl->findTemplate ( 'news_comment.php', array ( 'templates/', 'web/templates/modules/news/' ) ) );
		$ntpl->comments = "";
		$cached_users = Array ( );
		foreach ( dbComment::GetComments ( $news ) as $comment )
		{
			$ctpl->data =& $comment;
			$ctpl->config =& $confObj;
			if ( !$cached_users[ $comment->UserID ] ) 
			{
				$cached_users[ $comment->UserID ] = new dbUser ( );
				$cached_users[ $comment->UserID ]->load ( $comment->UserID );
			}
			$ctpl->poster =& $cached_users[ $comment->UserID ];
			$ntpl->comments .= $ctpl->render ( );
		}
	}
	$module = $ntpl->render ( );
}
?>
