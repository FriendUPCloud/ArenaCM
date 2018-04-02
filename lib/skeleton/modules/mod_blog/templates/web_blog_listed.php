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
		<div class="Block Newsitem<?= $this->extraClass ?>">
			<h2>
				<?if ( $this->ReadMore ) { ?><a href="<?= ( $this->detailpage ? $this->detailpage->getRoute ( ) : $this->content->getRoute ( ) ) . '/blogitem/' . $this->blog->ID . '_' . texttourl ( $this->blog->Title ) ?>"><?}?><?= ( $this->titleLength > 0 && strlen ( strip_tags ( $this->blog->Title ) ) > $this->titleLength ) ? ( substr ( strip_tags ( $this->blog->Title ), 0, $this->titleLength - 3 ) . '...' ) : $this->blog->Title ?><?if ( $this->ReadMore ) { ?></a><?}?>
			</h2>
			<?if ( $this->image ) { ?>
			<div class="Image" style="<? if ( $this->ReadMore ) { return 'cursor: hand; cursor: pointer; '; } ?>background-image: url(<?= $this->image->getImageUrl ( $this->sizeX, $this->sizeY, $this->ImageAspect ) ?>)"<?
				if ( $this->ReadMore || $this->cfgComments )
				{
					$q = '"';
					return " onclick={$q}document.location='" . 
						( $this->detailpage ? $this->detailpage->getRoute ( ) : $this->content->getRoute ( ) ) . 
						'/blogitem/' . $this->blog->ID . '_' . texttourl ( $this->blog->Title ) . ".html';$q";
				}
			?>></div>
			<?}?>
			<?if ( $this->imagetag ) { ?>
			<?= $this->imagetag ?>
			<?}?>
			<?if ( $this->cfgShowAuthor ) { ?>
			<p class="Bold WrittenBy"><?= i18n ( 'written by' ) . ' <span>' . $this->blog->AuthorName ?></span></p>
			<?}?>
			<p class="Small Date"><span class="Published"><?= i18n ( 'posted date' ) . '</span> <span class="Date">' . ArenaDate ( DATE_FORMAT, $this->blog->DateUpdated ) ?></span></p>
			<?if ( $this->blogTags ) { ?>
			<p class="Small Tags">
				<?= $this->blogTags ?>
			</p>
			<?}?>
			<div class="Block Leadin">
				<?= ( $this->leadinLength > 0 && strlen ( strip_tags ( $this->blog->Leadin ) ) > $this->leadinLength ) ? ( substr ( strip_tags ( $this->blog->Leadin ), 0, $this->leadinLength - 3 ) . '...' ) : $this->blog->Leadin ?>
			</div>
			<?if ( trim ( $this->blog->ExternalLink ) ) { ?>
			<p class="Block ReadMore">
				<a class="FloatLeft" href="<?= $this->blog->ExternalLink ?>" target="_blank">
					<span><?= i18n ( 'Read more' ) ?></span>
				</a>
			</p>
			<?}?>
			<?if ( $this->ReadMore || $this->cfgComments ) { ?>
			<p class="Block ReadMore">
				<?
					if ( $this->ReadMore )
					{
						return '
				<a class="FloatLeft Small" href="'. ( $this->detailpage ? $this->detailpage->getRoute ( ) : $this->content->getRoute ( ) ) . '/blogitem/' . $this->blog->ID . '_' . texttourl ( $this->blog->Title ) .'.html">
					<span>'. i18n ( 'Read more' ) .'</span>
				</a>
						';
					}
				?>
				<?
					if ( $this->cfgComments ) 
					{
						if ( $this->commentcount )
							$commentString = $this->commentcount . ' ' . ( $this->commentcount == 1 ? i18n ( 'comment' ) : i18n ( 'comments' ) );
						else $commentString = i18n ( 'no comments' );
						$link = ( $this->detailpage ? $this->detailpage->getRoute ( ) : $this->content->getRoute ( ) ) . '/blogitem/' . $this->blog->ID . '_' . texttourl ( $this->blog->Title );
						
						$ztr = '';
						if ( $this->ReadMore )
						{
							$ztr .= '
				<div class="FloatLeft">
					&nbsp;|&nbsp;
				</div>
							';
						}
						if ( $this->canComment )
						{
							$ztr .= '
				<a class="FloatLeft Small" href="'. $link .'.html#comment">
				<span>'. 
					i18n ( 'Add comment' ) .' ('. $commentString .')
				</span></a>
							';
						}
						else
						{
							$ztr .= '<div class="FloatLeft Small"><span>' . $commentString . '</span></div>';
						}
						return $ztr;
					}
				?>
				<span class="ClearBoth"></span>
			</p>
			<?}?>
			<?if ( $this->facebookLike ) { ?>
			<p class="FacebookLike">
				<iframe src="http://www.facebook.com/plugins/like.php?href=<?= $this->facebookLikeUrl ?>&amp;layout=standard&amp;show_faces=false&amp;width=<?= $this->facebookLikeWidth ?>&amp;action=like&amp;font=tahoma&amp;colorscheme=light&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:<?= $this->facebookLikeWidth ?>px; height:<?= $this->facebookLikeHeight ?>px;" allowTransparency="true"></iframe>
			</p>
			<?}?>
			<div class="ClearBoth"></div>
		</div>
