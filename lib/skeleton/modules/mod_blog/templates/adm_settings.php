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
	<h1>
		<?= i18n ( 'Settings for blog field' ) ?>
	</h1>
	<div class="SubContainer" style="padding: <?= MarginSize ?>px">
		<table class="LayoutColumns">
			<tr>
				<td>
					<table class="Gui">
						<tr>
							<td>
								<strong><?= i18n ( 'Articlecount pr. page' ) ?>:</strong>
							</td>
							<td>
								<input type="text" id="mod_blog_limit" size="10" value="<?= $this->settings->Limit ?>">
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Use comments' ) ?>:</strong>
							</td>
							<td>
								<input type="checkbox" id="mod_blog_comments"<?= $this->settings->Comments ? ' checked="checked"' : '' ?>>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Facebook comments' ) ?>:</strong>
							</td>
							<td>
								<input type="checkbox" id="mod_blog_FBComments"<?= $this->settings->FBComments ? ' checked="checked"' : '' ?>>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Show author' ) ?>:</strong>
							</td>
							<td>
								<input type="checkbox" id="mod_blog_showauthor"<?= $this->settings->ShowAuthor ? ' checked="checked"' : '' ?>>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Show tagbox' ) ?>:</strong>
							</td>
							<td>
								<input type="checkbox" id="mod_blog_tagbox"<?= $this->settings->TagBoxEnabled ? ' checked="checked"' : '' ?>>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Filter on tags' ) ?>:</strong>
							</td>
							<td>
								<input type="text" id="mod_blog_tagfilter" size="10" value="<?= $this->settings->Tagfilter ?>">
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Tagbox placement' ) ?>:</strong>
							</td>
							<td>
								<select id="mod_tagbox_placement">
									<?
										$db =& dbObject::globalValue ( 'database' );
										if ( $rows = $db->fetchObjectRows ( '
											SELECT DISTINCT(ContentGroups) AS Uni FROM ContentElement WHERE MainID=ID
										' ) )
										{
											$groups = array ( );
											foreach ( $rows as $row )
											{
												if ( $gr = explode ( ',', $row->Uni ) )
												{
													foreach ( $gr as $g )
													{
														if ( !in_array ( trim ( $g ), $groups ) )
															$groups[] = trim ( $g );
													}
												}
											}
											foreach ( $groups as $g )
											{
												if ( $g == $this->settings->TagBoxPosition )
													$s = ' selected="selected"';
												else $s = '';
												$str .= '<option value="' . $g . '"' . $s . '>' . $g . '</option>';
											}
											return $str;
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Show searchbox' ) ?>:</strong>
							</td>
							<td>
								<input type="checkbox" id="mod_blog_searchbox"<?= $this->settings->SearchBox ? ' checked="checked"' : '' ?>>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Hide details' ) ?>:</strong>
							</td>
							<td>
								<input type="checkbox" id="mod_blog_hide_details"<?= $this->settings->HideDetails ? ' checked="checked"' : '' ?>>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Listmethod' ) ?>:</strong>
							</td>
							<td>
								<select id="mod_blog_listmethod">
									<?
										$str = '';
										foreach ( array ( 'normal', 'random' ) as $mode )
										{
											$s = '';
											if ( $this->settings->ListMethod == $mode )
												$s = ' selected="selected"';
											$str .= '<option value="' . $mode . '"' . $s . '>' . 
												i18n ( 'bloglistmode_' . $mode ) . '</option>';
										}
										return $str;
									?>
								</select>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'i18n_gallery_mode' ) ?>:</strong>
							</td>
							<td>
								<select id="mod_blog_gallerymode">
									<?
										$str = '';
										foreach ( array ( 'default' ,'slideshow', 'gallery' ) as $mode )
										{
											$s = '';
											if ( $this->settings->GalleryMode == $mode )
												$s = ' selected="selected"';
											$str .= '<option value="' . $mode . '"' . $s . '>' . 
												i18n ( 'bloggallerymode_' . $mode ) . '</option>';
										}
										return $str;
									?>
								</select>
							</td>
						</tr>
					</table>
				</td>
				<td>
					<table class="Gui">
						<tr>
							<td>
								<strong><?= i18n ( 'Use this page for "Read more"' ) ?>:</strong>
							</td>
							<td id="mod_blog_detailpage">
								<?
									$str = dbContent::RenderSelect ( 'mod_blog_detailpage', false, $this->settings->Detailpage, false, false, false, false, '*' );
									return $str;
								?>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Fetch blogs from this page' ) ?>:</strong>
							</td>
							<td id="mod_blog_sourcepage">
								<?
									$str = dbContent::RenderSelect ( 'mod_blog_sourcepage', false, $this->settings->Sourcepage, false, false, false, false, '*' );
									return $str;
								?>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Max amount of letters in leadin' ) ?>:</strong>
							</td>
							<td>
								<input type="text" id="mod_blog_leadinlength" size="4" value="<?= $this->settings->LeadinLength ?>">
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Max amount of letters in title' ) ?>:</strong>
							</td>
							<td>
								<input type="text" id="mod_blog_titlelength" size="4" value="<?= $this->settings->TitleLength ?>">
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Leadin imagesize' ) ?>:</strong>
							</td>
							<td>
								<table cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td>
											<input type="text" id="mod_blog_sizex" style="width: 40px" size="4" value="<?= $this->settings->SizeX ?>">
										</td>
										<td> x&nbsp;</td>
										<td>
											<input type="text" id="mod_blog_sizey" style="width: 40px" size="4" value="<?= $this->settings->SizeY ?>">
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Detail imagesize' ) ?>:</strong>
							</td>
							<td>
								<table cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td>
											<input type="text" id="mod_blog_lsizex" style="width: 40px" size="4" value="<?= $this->settings->LSizeX ?>">
										</td>
										<td> x&nbsp;</td>
										<td>
											<input type="text" id="mod_blog_lsizey" style="width: 40px" size="4" value="<?= $this->settings->LSizeY ?>">
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Imageaspect, leadin' ) ?>:</strong>
							</td>
							<td>
								<select id="mod_blog_imageaspect">
									<?
										$str = '';
										foreach ( array ( 'imgtag', 'proximity', 'framed', 'centered' ) as $mode )
										{
											$s = '';
											if ( $this->settings->Imageaspect == $mode )
												$s = ' selected="selected"';
											$str .= '<option value="' . $mode . '"' . $s . '>' . 
												i18n ( 'blogscalemode_' . $mode ) . '</option>';
										}
										return $str;
									?>
								</select>
								<input type="color" value="<?= ( $this->settings->Imgcolor ? $this->settings->Imgcolor : '#000000' ) ?>" maxlength="7" id="mod_blog_imgcolor" style="width: 30px">
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Imageaspect, article' ) ?>:</strong>
							</td>
							<td>
								<select id="mod_blog_imageaspectdtl">
									<?
										$str = '';
										foreach ( array ( 'imgtag', 'proximity', 'framed', 'centered' ) as $mode )
										{
											$s = '';
											if ( $this->settings->Imageaspectdtl == $mode )
												$s = ' selected="selected"';
											$str .= '<option value="' . $mode . '"' . $s . '>' . 
												i18n ( 'blogscalemode_' . $mode ) . '</option>';
										}
										return $str;
									?>
								</select>
								<input type="color" value="<?= ( $this->settings->Imgcolordtl ? $this->settings->Imgcolordtl : '#000000' ) ?>" maxlength="7" id="mod_blog_imgcolordtl" style="width: 30px">
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Headertext' ) ?>:</strong>
							</td>
							<td>
								<table cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td>
											<input type="text" id="mod_blog_headertext" size="30" value="<?= $this->settings->HeaderText ?>" style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box">
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<strong><?= i18n ( 'Pagination pr. page' ) ?>:</strong>
							</td>
							<td>
								<table cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td>
											<input type="checkbox" id="mod_blog_pagination" size="3"<?= $this->settings->Pagination ? ' checked="checked"' : '' ?> style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box"/>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<div class="SpacerSmallColored"></div>
		<table>
			<tr>
				<td>
					<strong><?= i18n ( 'Use facebook "Like"' ) ?>:</strong>
				</td>
				<td>
					<input type="checkbox"<?= $this->settings->FacebookLike ? ' checked="checked"' : '' ?> id="mod_blog_facebooklike"/>
				</td>
			</tr>
			<tr>
				<td>
					<strong><?= i18n ( 'Facebook "Like" dimensions' ) ?>:</strong>
				</td>
				<td>
					<?= i18n ( 'Width' ) ?>: <input type="text" value="<?= $this->settings->FacebookLikeWidth ?>" size="5" id="mod_blog_facebooklikewidth"/>
					<?= i18n ( 'Height' ) ?>: <input type="text" value="<?= $this->settings->FacebookLikeHeight ?>" size="5" id="mod_blog_facebooklikeheight"/>
				</td>
			</tr>
		</table>
	</div>
	<div class="SpacerSmallColored"></div>
	<button type="button" onclick="mod_blog_savesettings ( )">
		<img src="admin/gfx/icons/disk.png"> <span id="mod_blog_savetext"><?= i18n ( 'Save' ) ?></span>
	</button>
	<button type="button" onclick="mod_blog_savesettings ( ); removeModalDialogue ( 'blogsettings' );">
		<img src="admin/gfx/icons/accept.png"> <span id="mod_blog_savetext"><?= i18n ( 'Save and close' ) ?></span>
	</button>
	<button type="button" onclick="removeModalDialogue ( 'blogsettings' )">
		<img src="admin/gfx/icons/cancel.png"> <?= i18n ( 'Close' ) ?>
	</button>

