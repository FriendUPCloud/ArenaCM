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
	<div class="Newsitem_Detail">
		
		<?
			global $Session;
			$this->lang = $Session->LanguageCode ? $Session->LanguageCode : $this->data->LanguageCode;
			$cat = new dbObject ( 'NewsCategory' );
			$cat->load ( $this->data->CategoryID );
			if ( $cat->BackPageID > 0 )
				$bid = $cat->BackPageID;
			else $bid = $cat->ContentElementID;
			$p = new dbContent ( ); 
			if ( $p->load ( $bid ) )
			{
				$this->backurl = $p->getUrl ( );
				return;
			}
			$this->backurl = $this->path;
		?>
		
		<h3>
			<?= $this->data->Title ?>
		</h3>
		<p class="Date">
 			<?= ArenaDate ( DATE_FORMAT, $this->data->DateActual ) ?>
		</p>
		
		<?if ( strstr ( $this->data->Article, '<p' ) || strstr ( $this->data->Article, '<table' ) ) { ?>
		<div class="Article">
			<?= $this->data->Article ?>
		</div>
		<?}?>
		<?if ( !strstr ( $this->data->Article, '<p' ) && !strstr ( $this->data->Article, '<table' ) ) { ?>
		<p class="Article">
			<?= $this->data->Article ?>
		</p>
		<?}?>
		
		<?
			// Extra fields!
			$str = '';
			$this->data->loadExtraFields ( );
			foreach ( $this->data as $k=>$v )
			{
				if ( substr ( $k, 0, 6 ) == '_extra' )
				{
					list ( , , $class ) = explode ( '_', $k );
					$str .= '<p class="' . $class . '">' . stripslashes ( $v ) . '</p>';
				}
			}
			return $str;
		?>
		
		<div class="Spacer"></div>
		
		<a name="comments"><em></em></a>
		
		<p class="Back">
			<a href="<?= $this->backurl ?>"><?= i18n ( "Back", $this->lang ) ?></a>
			<?if ( ( $GLOBALS[ "webuser" ]->ID && $this->config->comments == 1 ) || $this->config->comments == 2 ) { ?>
				<?= ( $this->config->comments > 0 ? '| ' : '' ) ?> <a href="<?= $this->path ?>?nid=<?= $this->data->ID ?>#comment"><?= i18n ( 'Add comment', $this->lang ) ?></a>
			<?}?>
		</p>
		<?if ( $this->commentcount > 0 ) { ?>
		<h4><?= $this->commentcount ?> <?= i18n ( ( $this->commentcount == 1 ? 'comment' : 'comments' ), $this->data->LanguageCode ) ?></h4>
		<?= $this->comments ?>
		<?}?>
		
		<?if ( ( ( $GLOBALS[ "webuser" ]->ID && $this->config->comments == 1 ) || $this->config->comments == 2 ) && !( $_REQUEST[ "action" ] == 'post' && $GLOBALS[ 'postsuccess' ] ) ) { ?>
			
		<a name="comment"><em></em></a>
			
		<script type="text/javascript" src="web/modules/news/javascript/comment.js"></script>
		
		<form method="post" action="<?= $this->path ? $this->path : $_SERVER[ 'REQUEST_URI' ] ?>#comment" id="comment_form">
		
		<input type="hidden" name="action" value="post" />
		<input type="hidden" name="nid" value="<?= $this->data->ID ?>" />
		<input type="hidden" name="Controlnumber" value="post" />
		<input type="hidden" name="Control" value="<?= md5 ( microtime ( ) . rand ( 0, 999 ) ) ?>" />
		
		<h4>
			<?= i18n ( "Your comment", $this->lang ) ?>
		</h4>
		
		<table class="CommentForm">
			<tr>
				<td class="First">
					<p>
						<?= i18n ( "Title", $this->lang ) ?>
					</p>
					<p>
						<input type="text" name="Subject" value="<?= $_REQUEST[ 'Subject' ] ?>" />
					</p>
				</td>
		
		<?
			if ( $this->config->comments == 2 )
				return '<td class="Second"><p>' . i18n ( 'Nickname', $this->lang ) . '</p><p><input type="text" name="Nickname" value="' . $_REQUEST[ 'Nickname' ] . '" /></p></td>';
		?>
		
			</tr>
		</table>
		
		<p>
			<?= i18n ( "Message", $this->lang ) ?>
		</p>
		<p>
			<textarea cols="50" rows="10" name="Message"><?= $_REQUEST[ 'Message' ] ?></textarea>
		</p>
		<p>
			<?= i18n ( 'What does it say in the image below?' ) ?>
		</p>
		<p>
			<input type="text" value="" size="10" name="captcha"/>
		</p>
		<?
			if ( $GLOBALS[ 'newsposterror' ] == 'captcha' )
			{
				return '
		<p><em class="alert">
			' . i18n ( 'The text you wrote did not match the image contents', $this->lang ) . '.
		</em></p>
				';
			}
		?>
		<p>
		<?
			if ( !defined ( 'CAPTCHA_BACKGROUND' ) )
				define ( 'CAPTCHA_BACKGROUND', 0x7E2F34 );
			if ( !defined ( 'CAPTCHA_FOREGROUND' ) )
				define ( 'CAPTCHA_FOREGROUND', 0xFFFFFF );
			list ( $image, $_SESSION[ 'captcha' ] ) = dbImage::renderCaptcha ( 200, 70, CAPTCHA_BACKGROUND, CAPTCHA_FOREGROUND, CaptchaSnow );
			return $image;
		?>
		</p>
		<p>
			<button type="button" onclick="check_comment_form ( )">
				<?= i18n ( "Submit", $this->lang ) ?> 
			</button>
		</p>
		
		</form>
			
		<?}?>
		<?if ( $_REQUEST[ "action" ] == 'post' && $GLOBALS[ 'postsuccess' ] ) { ?>
		<a name="comment"><em></em></a>
		<h2>
			<?= i18n ( 'Your comment has been submitted!' ) ?>
		</h2>
		<p class="Back">
			<button type="button" onclick="document.location='<?
				list ( $l, ) = explode ( '#', $_SERVER[ 'REQUEST_URI' ] );
				return $l;
			?>';"><?= i18n ( 'Back' ) ?></button>
		</p>
		<?}?>
		
	</div>

	
