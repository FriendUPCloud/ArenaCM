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

global $Session;
i18nAddLocalePath ( 'lib/skeleton/modules/mod_blog/locale/' );
include_once ( 'lib/skeleton/modules/mod_blog/translations.php' );

$mtpldir = 'lib/skeleton/modules/mod_blog/templates/';
$GLOBALS[ 'document' ]->addResource ( 'stylesheet', $mtpldir . '../css/admin.css' );

if ( !$Session->mod_blog_initialized )
{
	$db =& dbObject::globalValue ( 'database' );
	$tb = new cDatabaseTable ( 'BlogItem' );
	if ( !$tb->load ( ) )
	{
		
		$db->query ( '
			CREATE TABLE `BlogItem`
			(
				`ID` int(11) auto_increment NOT NULL,
				`SortOrder` bigint(20) NOT NULL DEFAULT 0,
				`UserID` bigint(20) NOT NULL,
				`ContentElementID` bigint(20) NOT NULL,
				`AuthorName` varchar(255) NOT NULL,
				`IsPublished` tinyint(4) NOT NULL DEFAULT 0,
				`IsSticky` tinyint(4) NOT NULL DEFAULT 0,
				`Title` varchar(255) NOT NULL,
				`Leadin` text NOT NULL,
				`Body` text NOT NULL,
				`Tags` varchar(255) NOT NULL,
				`DatePublish` datetime NOT NULL,
				`DateCreated` datetime NOT NULL,
				`DateUpdated` datetime NOT NULL,
				PRIMARY KEY(ID)
			)
		' );
	}
	else
	{
		$subtitle = false;
		$leadinscalemode = false;
		$detailscalemode = false;
		$isSticky = false;
		$views = false;
		$rlink = false;
		$sortorder = false;
		foreach ( $tb->getFields () as $f )
		{
			if ( $f->Field == 'SubTitle' ) $subtitle = true;
			if ( $f->Field == 'LeadinScalemode' ) $leadinscalemode = true;
			if ( $f->Field == 'DetailScalemode' ) $detailscalemode = true;
			if ( $f->Field == 'IsSticky' ) $isSticky = true;
			if ( $f->Field == 'Views' ) $views = true;
			if ( $f->Field == 'ExternalLink' ) $rlink = true;
			if ( $f->Field == 'SortOrder' ) $sortorder = true;
		}
		if ( !$subtitle )
			$db->query ( 'ALTER TABLE `BlogItem` ADD SubTitle varchar(255) default "" AFTER Title' );
		if ( !$leadinscalemode )
			$db->query ( 'ALTER TABLE `BlogItem` ADD LeadinScalemode varchar(255) default "" AFTER SubTitle' );
		if ( !$detailscalemode )
			$db->query ( 'ALTER TABLE `BlogItem` ADD DetailScalemode varchar(255) default "" AFTER SubTitle' );
		if ( !$isSticky )
			$db->query ( 'ALTER TABLE `BlogItem` ADD `IsSticky` tinyint(4) NOT NULL DEFAULT 0 AFTER IsPublished' );
		if ( !$views )
			$db->query ( 'ALTER TABLE `BlogItem` ADD `Views` int(11) NOT NULL DEFAULT 0 AFTER IsSticky' );
		if ( !$rlink )
			$db->query ( 'ALTER TABLE `BlogItem` ADD `ExternalLink` varchar(255) NOT NULL DEFAULT "" AFTER `Title`' );
		if ( !$sortorder )
			$db->query ( 'ALTER TABLE `BlogItem` ADD `SortOrder` bigint(20) NOT NULL DEFAULT 0 AFTER ID' );
	}
	$tb = new cDatabaseTable ( 'BlogTag' );
	if ( !$tb->load ( ) )
	{
		$db->query ( '
			CREATE TABLE `BlogTag`
			(
				`ID` int(11) auto_increment NOT NULL,
				`Name` varchar(255) NOT NULL,
				`Rating` bigint(20) NOT NULL default 0,
				`DateUpdated` datetime,
				PRIMARY KEY(ID)
			)
		' );
	}
	$Session->Set ( 'mod_blog_initialized', '1' );
}

if ( !function_exists ( 'listBlogs' ) )
{
	function saveImage ( $blog, $title = 'Image', $die = false )
	{
		// Remove old images
		$msg = '';
		if ( $images = $blog->getObjects ( 'ObjectType = Image' ) )
		{
			foreach ( $images as $image )
			{
				// Don't delete the wrong image!
				if ( $title == 'DetailImage' && $image->Title != $title )
					continue;
				else if ( $title != 'DetailImage' && $image->Title == 'DetailImage' )
					continue;
				// Delete the right image!
				$blog->removeObject ( $image );
				$image->delete ( );
				$msg .= ' image deleted';
			}
		}
		$img = new dbImage ( );
		if ( $img->receiveUpload ( $_FILES[ $title ] ) )
		{
			$img->Title = $title; // Set correct image title
			if ( $img->save ( ) )
			{
				$type = 'leadin';
				$did = 'BlogImagePreview';
				if ( $title == 'DetailImage' )
				{
					$type = 'detail';
					$did = 'BlogDetailPreview';
				}
				$blog->addObject ( $img );
				if ( $die )
				{
					die ( '
						<script> 
							parent.document.getElementById ( \'' . $did . '\' ).innerHTML = "' . 
								addslashes ( $img->getImageHTML ( 64, 64, 'centered' ) ) . '<button type="button" onclick="mod_blog_removeimage(' . $blog->ID . ',\'' . $type . '\')"><img src="admin/gfx/icons/image_delete.png"/></button>"; 
						</script>
					' );
				}
				else
				{
					return $img->getImageHTML ( 64, 64, 'centered' ) . '<button type="button" onclick="mod_blog_removeimage(' . $blog->ID . ',\'' . $type . '\')"><img src="admin/gfx/icons/image_delete.png"/></button>';
				}
			}
			return 'Saving error.';
		}
		return 'Image error.';
	}
	function listBlogs ( $contentid, $start = 0, $lim = 20 )
	{
		global $Session, $content;
		if ( !$contentid ) $contentid = $GLOBALS[ 'content' ]->MainID;
		if ( !$start ) $start = 0;
		$editorcontent = new dbContent ( $Session->EditorContentID );
		$content = new dbContent ( $contentid );
		$blogs = new dbObject ( 'BlogItem' );
		$blogs->addClause ( 'WHERE', 'ContentElementID=' . $content->MainID );
		$cnt = $blogs->findCount ( );
		$blogs->addClause ( 'LIMIT', (string)$start . ', ' . (string)$lim );
		$blogs->addClause ( 'ORDER BY', 'SortOrder ASC, DateUpdated DESC, ID DESC' );
		if ( $blogs = $blogs->find ( ) )
		{
			$str = '<table class="List">
			<tr>
				<th colspan="2">
					#
				</th>
				<th>
					Tittel:
				</th>
				<th>
					Forfatter:
				</th>
				<th>
					Dato:
				</th>
				<th>
					Publisert:
				</th>
				<th>
					Viser fra:
				</th>
				' . ( $content->MainID == $editorcontent->MainID ? '<th colspan="2">Rediger:</th>' : '' ) . '
			</tr>';
			foreach ( $blogs as $blog )
			{
				if ( $images = $blog->getObjects ( 'ObjectType = Image' ) )
				{
					$image = '<div style="float: right">' . $images[0]->getImageHTML ( 32, 32, 'framed' ) . '</div>';
					foreach ( $images as $im )
					{
						if ( $im->Title != 'DetailImage' )
							$image = '<div style="float: right">' . $im->getImageHTML ( 32, 32, 'framed' ) . '</div>';
					}
				}
				else $image = '';
				$valign = ' style="vertical-align: middle"';
				$valigc = ' style="vertical-align: middle; text-align: center"';
				if ( $content->MainID == $editorcontent->MainID )
				{
					$buttons = '
					<button type="button" onclick="mod_blog_edit(' . $blog->ID . ')"><img src="admin/gfx/icons/page_white_edit.png" title="Endre"/></button></td><td' . $valigc . '>
					<button type="button" onclick="mod_blog_delete(' . $blog->ID . ')"><img src="admin/gfx/icons/page_white_delete.png" title="Slett"/></button>
					';
				}
				else $buttons = '';
				$str .= '
			<tr class="' . ( $sw = ( $sw == 'sw2' ? 'sw1' : 'sw2' ) ) . '">
				<td style="text-align: right; width: 24px"><input type="text" value="' . $blog->SortOrder . '" class="SmallNum" onchange="mod_blog_sortorder(\'' . $blog->ID . '\',this.value)" size="2"></td>
				<td' . $valigc . ' width="32px">' . $image . '</td>
				<td' . $valign . '><strong>' . $blog->Title . '</strong></td>
				<td' . $valign . '>av ' . $blog->AuthorName . '</td>
				<td' . $valign . '>' . ArenaDate ( DATE_FORMAT, $blog->DateUpdated ) . '</td>
				<td' . $valigc . '>' . ( $blog->IsPublished ? '<img src="admin/gfx/icons/lightbulb.png"/>' : '<img src="admin/gfx/icons/lightbulb_off.png"/>' ) . '</td>
				<td' . $valign . '>' . ArenaDate ( DATE_FORMAT, $blog->DatePublish ) . '</td>
				' . ( $buttons ? ( '<td' . $valigc . '>' . $buttons . '</td>' ) : '' ) . '

			</tr>
				';
			}
			$str .= '</table>';
			
			$navigation = '';
			if ( $start > 0 )
			{
				$np = $start - $lim; if ( $np < 0 ) $np = "0";
				$navigation .= '<button type="button" onclick="mod_blog_pos(\'' . $np . '\')"><img src="admin/gfx/icons/arrow_left.png"> Forrige side</button>';
			}
			if ( $start + $lim < $cnt )
			{
				$np = $start + $lim; 
				$navigation .= '<button type="button" onclick="mod_blog_pos(\'' . $np . '\')">Neste side <img src="admin/gfx/icons/arrow_right.png"></button>';
			}
			
			return $str . ( $navigation ? "<hr>$navigation" : '' );
		}
		return '<p>Ingen artikler finnes.</p>';
	}
}	
switch ( $_REQUEST[ 'modaction' ] )
{
	case 'preview':
		i18nAddLocalePath ( BASE_DIR . '/locale' );
		// Some other config vars
		$cfg = explode ( "\t", $fieldObject->DataMixed );
		$cfgComments = $cfg[0];
		$cfgShowAuthor = $cfg[1];
		$mtpl = new cPTemplate ( "{$mtpldir}adm_preview.php" );
		$blog = new dbObject ( 'BlogItem' );
		$blog->load ( $_REQUEST[ 'bid' ] );
		$mtpl->blog =& $blog;
		$mtpl->bloghtml = new cPTemplate ( "{$mtpldir}web_blog.php" );
		$mtpl->bloghtml->blog =& $blog;
		$mtpl->bloghtml->content =& $content;
		$mtpl->bloghtml->cfgShowAuthor = $cfgShowAuthor;
		$mtpl->bloghtml->cfgComments = $cfgComments;
		$mtpl->bloghtml = $mtpl->bloghtml->render ( );
		die ( $mtpl->render ( ) );
	
	case 'editimage':
		$blog = new dbObject ( 'BlogItem' );
		if ( $blog->load ( $_REQUEST[ 'bid' ] ) )
		{
			$db =& dbObject::globalValue ( 'database' );
			$db->query ( 'DELETE FROM ObjectConnection o WHERE o.ConnectedObjectType="Image" AND o.ObjectType="BlogItem" AND o.ObjectID=' . $blog->ID . ' AND i.ID = o.ConnectedObjectID AND i.Title != "DetailImage"' );
			$img = new dbImage ( );
			if ( $img->load ( $_REQUEST[ 'imageid' ] ) )
				$blog->addObject ( $img );
		}
		die ( $img->getImageHTML ( 64, 64, 'framed' ) );
		
	case 'editdetailimage':
		$blog = new dbObject ( 'BlogItem' );
		if ( $blog->load ( $_REQUEST[ 'bid' ] ) )
		{
			$db =& dbObject::globalValue ( 'database' );
			$db->query ( 'DELETE FROM ObjectConnection o WHERE o.ConnectedObjectType="Image" AND o.ObjectType="BlogItem" AND o.ObjectID=' . $blog->ID . ' AND i.ID = o.ConnectedObjectID AND i.Title="DetailImage"' );
			$img = new dbImage ( );
			if ( $img->load ( $_REQUEST[ 'imageid' ] ) )
			{
				$img->Title = 'DetailImage';
				$img->save ();
				$blog->addObject ( $img );
			}
		}
		die ( $img->getImageHTML ( 64, 64, 'framed' ) );
		
	case 'savesettings':
		$fld = new dbObject ( 'ContentDataSmall' );
		$fld->load ( $fieldObject->ID );
		$fld->DataInt = $_POST[ 'limit' ];
		$fld->DataMixed = 	
							$_POST[ 'comments' ] . "\t" . 
							$_POST[ 'showauthor' ] . "\t" . 
							$_POST[ 'tagbox' ] . "\t" . 
							$_POST[ 'tagbox_placement' ] . "\t" . 
							$_POST[ 'searchbox' ] . "\t" . 
							$_POST[ 'detailpage' ] . "\t" .
							$_POST[ 'sourcepage' ] . "\t" .
							$_POST[ 'leadinlength' ] . "\t" .
							$_POST[ 'titlelength' ] . "\t" .
							$_POST[ 'sizex' ] . "\t" .
							$_POST[ 'sizey' ] . "\t" . 
							$_POST[ 'headertext' ] . "\t" . 
							$_POST[ 'hidedetails' ] . "\t" .
							$_POST[ 'facebooklike' ] . "\t" .
							$_POST[ 'facebooklikedimensions' ] . "\t" . 
							$_POST[ 'listmethod' ] . "\t" .
							$_POST[ 'lsizex' ] . "\t" . 
							$_POST[ 'lsizey' ] . "\t" . 
							$_POST[ 'imageaspect' ] . "\t" .
							$_POST[ 'imgcolor' ] . "\t" .
							$_POST[ 'gallerymode' ] . "\t" .
							$_POST[ 'fbcomments' ] . "\t" . 
							$_POST[ 'tagfilter' ] . "\t" . 
							$_POST[ 'imageaspectdtl' ] . "\t" .
							$_POST[ 'imgcolordtl' ] . "\t" .
							$_POST[ 'pagination' ];
		$fld->save ( );
		die ( 'ok' );
		
	case 'delete':
		$blog = new dbObject ( 'BlogItem' );
		if ( $blog->load ( $_REQUEST[ 'bid' ] ) )
			$blog->delete ( );
		$std = new cPTemplate ( $mtpldir . 'adm_std.php' );
		$std->blogs = listBlogs ( $fieldObject->ContentID, $_REQUEST[ 'bpos' ], 20 );
		die ( $std->render ( ) );
	
	case 'sortorder':
		$blog = new dbObject ( 'BlogItem' );
		if ( $blog->load ( $_REQUEST[ 'bid' ] ) )
		{
			$blog->SortOrder = $_REQUEST[ 'order' ];
			$blog->save ();
		}
		$std = new cPTemplate ( $mtpldir . 'adm_std.php' );
		$std->blogs = listBlogs ( $fieldObject->ContentID, $_REQUEST[ 'bpos' ], 20 );
		die ( $std->render ( ) );
	
	case 'saveimage':
		
		$blog = new dbObject ( 'BlogItem' );
		if ( $blog->load ( $_REQUEST[ 'bid' ] ) )
		{
			saveImage ( $blog, '', true );
		}		
		die ( '<script> parent.ge ( \'BlogImagePreview\' ).innerHTML = "Kunne ikke laste opp bildet."; </script>' );
	
	case 'savedetailimage':	
		$blog = new dbObject ( 'BlogItem' );
		if ( $blog->load ( $_REQUEST[ 'bid' ] ) )
		{
			saveImage ( $blog, 'DetailImage', true );
		}		
		die ( '<script> parent.ge ( \'BlogDetailPreview\' ).innerHTML = "Kunne ikke laste opp bildet."; </script>' );
		
	case 'removeimage':
		$blog = new dbObject ( 'BlogItem' );
		if ( $blog->load ( $_REQUEST[ 'bid' ] ) )
		{
			// Remove old images
			if ( $images = $blog->getObjects ( 'ObjectType = Image' ) )
			{
				if ( $_REQUEST[ 'type' ] == 'leadin' )
				{
					foreach ( $images as $image )
					{ 
						if ( $image->Title == 'DetailImage' ) continue;
						$blog->removeObject ( $image );
						$image->delete ( );
					}
				}
				else
				{
					foreach ( $images as $image )
					{
						if ( $image->Title != 'DetailImage' ) continue;
						$blog->removeObject ( $image );
						$image->delete ( );
					}
				}
			}
		}
		break;
		
	case 'save':
		$db =& dbObject::globalValue ( 'database' );
		// Clean some messy stuff
		$db->query ( 'DELETE FROM BlogItem WHERE ContentElementID <= 0' );
		$blog = new dbObject ( 'BlogItem' );
		$tags = explode ( ',', $_REQUEST[ 'Tags' ] );
		foreach ( $tags as $tag )
		{
			$t = new dbObject ( 'BlogTag' );
			$t->Name = trim ( $tag );
			$t->load ( );
			$t->DateUpdated = date ( 'Y-m-d H:i:s' );
			$t->save ( );
		}
		$cnt = new dbContent ( $GLOBALS[ 'Session' ]->EditorContentID );
		$blog->ContentElementID = $cnt->MainID;
		if ( $_REQUEST[ 'bid' ] ) $blog->load ( $_REQUEST[ 'bid' ] );
		$blog->receiveForm ( $_POST );
		if ( !$blog->UserID && $GLOBALS[ 'user' ]->_dataSource != 'core' ) 
			$blog->UserID = $GLOBALS[ 'user' ]->ID;
		$blog->Leadin = decodeArenaHTML ( $blog->Leadin );
		$blog->Body = decodeArenaHTML ( $blog->Body );
		$blog->Title = str_replace ( '"', '&quot;', $blog->Title );
		if ( !$_REQUEST[ 'bid' ] )
		{
			$blog->DateCreated = date ( 'Y-m-d H:i:s' );
			$blog->DateUpdated = $blog->DateCreated;
		}
		$blog->save ( );
		
		if ( $_FILES[ 'Image' ][ 'tmp_name' ] )
			$imagehtml = saveImage ( $blog );
		if ( $_FILES[ 'DetailImage' ][ 'tmp_name' ] )
			$imagedetailhtml = saveImage ( $blog, 'DetailImage' );
		
		if ( $_POST[ 'Folders' ] )
		{
			$existing = $blog->getObjects ( 'ObjectType = Folder' );
			// Add new objects
			$folders = explode ( ',', $_POST[ 'Folders' ] );
			$found = array ();
			foreach ( $folders as $fld )
			{
				$f = new dbObject ( 'Folder' );
				if ( $f->load ( $fld ) )
				{
					$blog->addObject ( $f );
					foreach ( $existing as $k=>$v ) 
					{
						if ( $v->ID == $f->ID ) 
						{
							$found[] = $f->ID;
						}
					}
				}
			}
			// Clean up deleted connections
			foreach ( $existing as $ext )
			{
				if ( !in_array ( $ext->ID, $found ) )
				{
					$database->query ( '
						DELETE FROM ObjectConnection WHERE 
						ConnectedObjectType = "Folder" AND ConnectedObjectID = \'' . $ext->ID . '\'
						AND
						ObjectType = "BlogItem" AND ObjectID="' . $blog->ID . '"
					' );
				}
			}
		}
		// Remove folders..
		else
		{
			$database->query ( '
				DELETE FROM ObjectConnection WHERE 
				ConnectedObjectType = "Folder"
				AND
				ObjectType = "BlogItem" AND ObjectID="' . $blog->ID . '"
			' );
		}
		
		// Maintain publish queue
		$queue = new dbObject ( 'PublishQueue' );
		$queue->ContentElementID = $content->MainID;
		$queue->ContentID = $blog->ID;
		$queue->ContentTable = "BlogItem";
		$queue->FieldName = 'IsPublished';
		$queue->load ( );
		if ( (int)$blog->IsPublished <= 0 ) 
		{
			$queue->LiteralName = 'Blog';
			$queue->Title = $blog->Title;
			$queue->save ( );
		}
		else if ( (int)$queue->ID > 0 )
			$queue->delete ( );

		// Die with the ID nr of the blog item
		die ( '
		<script>
			parent.ge(\'BlogIdentifier\').value = \'' . $blog->ID . '\';
			' 
				. ( 
					$imagehtml ? 
					'parent.ge(\'blogform\').Image.value = \'\'; parent.ge(\'BlogImagePreview\').innerHTML = \'' . addslashes ( $imagehtml ) . '\';' : 
					'' 
				) . 
			'
			' 
				. ( 
					$imagedetailhtml ? 
					'parent.ge(\'blogform\').DetailImage.value = \'\'; parent.ge(\'BlogDetailPreview\').innerHTML = \'' . addslashes ( $imagedetailhtml ) . '\';' : 
					'' 
				) . 
			'
			parent.ge(\'BlogItemName\').innerHTML = "Endre: ' . addslashes ( $blog->Title ) . '";
			parent.ge(\'mod_blog_saveblog\').innerHTML = \'Lagre artikkel\';
		</script>
		' );
		
	case 'new':
		$mtpl = new cPTemplate ( $mtpldir . 'adm_blog.php' );
		die ( $mtpl->render ( ) );
	
	case 'authentication':
		$mtpl = new cPTemplate ( $mtpldir . 'adm_authentication.php' );
		$blogs = new dbObject ( 'BlogItem' );
		$blogs = $blogs->find ( 'SELECT * FROM BlogItem WHERE !IsPublished AND ContentElementID = 0 ORDER BY DateUpdated DESC' );
		$mtpl->blogs =& $blogs;
		die ( $mtpl->render ( ) );
		
	case 'edit':
		$mtpl = new cPTemplate ( $mtpldir . 'adm_blog.php' );
		$blog = new dbObject ( 'BlogItem' );
		$blog->load ( $_REQUEST[ 'bid' ] );
		$blog->Leadin = encodeArenaHTML ( $blog->Leadin );
		$blog->Body = encodeArenaHTML ( $blog->Body );
		$mtpl->blog =& $blog;
		die ( $mtpl->render ( ) );
		
	case 'standard':
	default:	
		$act = 'lib/skeleton/modules/mod_blog/actions/' . $_REQUEST[ 'modaction' ] . '.php';
		if ( file_exists ( $act ) )
		{
			include_once ( $act );
		}
		else
		{
			$settings = new Dummy ( );
			$test = explode ( "\t", $fieldObject->DataMixed );
			$settings->Comments = $test[0];
			$settings->ShowAuthor = $test[1];
			$settings->TagBoxEnabled = $test[2];
			$settings->TagBoxPosition = $test[3];
			$settings->SearchBox = $test[4];
			$settings->Detailpage = $test[5];
			$settings->Sourcepage = $test[6];
			$settings->FBComments = $test[21];
			$settings->Tagfilter = $test[22];
	
			$cnt = new dbContent ( );
			if ( $settings->Sourcepage )
				$cnt->load ( $settings->Sourcepage );
			else $cnt->load ( $Session->EditorContentID );
	
			$sourceCopy = new dbContent ();
			if ( $settings->Sourcepage )
			{
				$sourceCopy->addClause ( 'WHERE', 'MainID != ID' );
				$sourceCopy->addClause ( 'WHERE', 'MainID = \'' . $settings->Sourcepage . '\'' );
				$sourceCopy = $sourceCopy->findSingle ();
			}
	
			$mtpl = new cPTemplate ( $mtpldir . 'adm_main.php' );
			$std = new cPTemplate ( $mtpldir . 'adm_std.php' );
			$std->settings = $settings;
			$std->content = $content;
			$std->blogs = listBlogs ( $cnt->ID, $_REQUEST[ 'bpos' ], 20 );
			$std->otherSourcepage = $content->MainID != $settings->Sourcepage && $settings->Sourcepage;
			$std->sourcePage = $sourceCopy->ID;
			$mtpl->standard = $std->render ( );
			if ( $_REQUEST[ 'modaction' ] == 'standard' )
				die ( $std->render ( ) );
			break;
		}
}
if ( $mtpl )
	$module = $mtpl->render ( );
$module .= '<iframe class="Hidden" name="hiddenblogiframe"></iframe>';
?>
