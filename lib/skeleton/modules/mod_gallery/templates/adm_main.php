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

			<div>
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr>
						<td style="padding-right: 4px">
							<h2 class="BlockHead">
								<table cellspacing="0" cellpadding="0" border="0" width="100%" style="position: relative; top: -1px"><tr><td style="padding: 0"><?= i18n ( 'gal_Preview' ) ?></td><td style="padding: 0 0 0 4px; width: 16px; text-align: right"><img alt="Settings" title="<?= i18n ( 'Settings' ) ?>" style="cursor: hand; cursor: pointer" src="admin/gfx/icons/wrench.png" id="ImageSettingsSwitcher_<?= $this->field->ID ?>"/></td></tr></table>
							</h2>
							<div class="BlockContainer" style="padding: 2px">
								<input type="hidden" value="0" id="galCurrentImageIndex"/>
								<input type="hidden" value="<?= $this->previewInfo[1] ?>" id="galCurrentImageIndexMax"/>
								<div id="gal_preview">
									<div class="Container" style="<?if ( !strstr ( $this->preview, '<img' ) ) { ?>background: url(<?= $this->preview ?>); <?}?>padding: 2px; overflow: hidden">
										<?if ( strstr ( $this->preview, '<img' ) ) { ?>
										<?= $this->preview ?>
										<?}?>
										<br style="clear: both"/>
									</div>
								</div>
							</div>
							<h2 class="BlockHead">
								<div class="HeaderBox" style="margin: -5px 0px 0 0">
									<div class="Button" style="position: relative; overflow: hidden; width: 18px; height: 13px; left: -3px">
										<iframe name="galIframe" style="position: absolute; left: -20000px"></iframe>
										<form action="admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=uploadimage" enctype="multipart/form-data" name="galUploadForm" target="galIframe" method="POST" id="galUploadFormen">
											<input onchange="document.galUploadForm.submit();" name="filestream" type="file" style="position: absolute; top: 0px; left: 0px; z-index: 3;" id="galUploadBtn"/>
										</form>
										<img src="admin/gfx/icons/image_add.png"/>
										<script>setOpacity ( ge('galUploadBtn' ), 0 );</script>
									</div>
									<button type="button" style="position: relative" onclick="galDelPicture()">
										<img src="admin/gfx/icons/bin.png"/>
									</button>
									<button type="button" style="position: relative" onclick="galPrevPicture()">
										<img src="admin/gfx/icons/arrow_left.png"/>
									</button>
									<button type="button" style="position: relative" onclick="galNextPicture()">
										<img src="admin/gfx/icons/arrow_right.png"/>
									</button>
									<button type="button" style="position: relative" onclick="galSavePictureText()">
										<img src="admin/gfx/icons/disk.png"/>
									</button>
									<button type="button" style="position: relative" onclick="galGetImageFolder()">
										<img src="admin/gfx/icons/folder.png"/>
									</button>
								</div>
								Bildetekst: <span id="galPreviewImageTitle"><?= $this->previewInfo[0] ?></span>
							</h2>
							<div class="BlockContainer" id="gal_texts">
								<textarea id="gal_text_box" style="height: 100px" class="mceSelector"><?= $this->previewInfo[2] ?></textarea>
							</div>
							<h2 class="BlockHead">
								Ekstrainformasjon
							</h2>
							<div class="BlockContainer">
								<table class="Gui">
									<tr>
										<td width="50"><label for="tags_<?= $this->field->ID ?>">Tags:</label></td>
										<td><input type="text" id="tags_<?= $this->field->ID ?>" value="<?= $this->previewInfo[3] ?>"/></td>
										<td width="50"><label for="link_<?= $this->field->ID ?>">Lenke:</label></td>
										<td><input size="50" type="text" id="link_<?= $this->field->ID ?>" placeholder="http://" value="<?= $this->previewInfo[4] ?>"/></td>
									</tr>
								</table>
							</div>
						</td>
						<td valign="top" id="ImageSettingsContainer_<?= $this->field->ID ?>" style="width: 360px">
							<div id="ImageSettings_<?= $this->field->ID ?>">
								<div class="tabs" id="gallerytabs" style="padding: 2px">
									<div class="tab" id="tabImageschoice">
										<img src="admin/gfx/icons/images.png"/> Bildevalg
									</div>
									<div class="tab" id="tabGalleryOptions">
										<img src="admin/gfx/icons/wrench.png"/> Innstillinger
									</div>
									<div class="page" id="pageImageschoice">
										<p>
											<strong>Heading:</strong> <input type="text" size="50" style="width: 300px" value="<?= $this->Heading ?>" id="galHeading_<?= $this->field->ID ?>"/>
										</p>
										<div class="Container">
											<select onchange="addSlideshowFolder(this)">
												<option>Legg til slideshow mappe:</option>
												<?
													function listFlds ( $parent = 0, $p = '' )
													{
														$o = new dbObject ( 'Folder' );
														if ( $rows = $o->find ( '
															SELECT * FROM 
																`Folder`
															WHERE 
																`Parent`=' . (int)$parent . '
															ORDER BY 
																`SortOrder` ASC, 
																`Name` ASC
														' ) )
														{
															foreach ( $rows as $row )
															{
																if ( 
																	!$GLOBALS[ 'Session' ]->AdminUser->checkPermission (
																		$row, 'read', 'admin'
																	)
																)
																{
																	continue;
																}
																$name = $row->Name;
																if ( strlen ( $name ) > 30 )
																	$name = substr ( $name, 0, 27 ) . '...';
																$str .= '<option value="' . $row->ID . '">' . $p . $name . '</option>';
																$str .= listFlds ( $row->ID, "$p&nbsp;&nbsp;&nbsp;&nbsp;" );
															}
														}
														return $str;
													}
													return listFlds();
												?>
											</select>
										</div>
										<div class="SpacerSmallColored"></div>
										<div class="Container" id="ImageList_<?= $this->field->ID ?>" style="padding: 4px; overflow: auto">
											<?= $this->folders ?>
										</div>
									</div>
									<div class="page" id="pageGalleryOptions">
										<div class="Container">
										<?
											$modes = array ( 'slideshow'=>'Slideshow', 'gallery'=>'Bildegalleri', 'archive'=>'Bildearkiv' );
											$i = 0;
											foreach ( $modes as $mode=>$name )
											{
												if ( $this->currentMode == $mode )
													$s = ' checked="checked"';
												else $s = '';
												$str .= '<td width="24px"><input type="radio" name="mode" value="' . $mode . '"' . $s . ' onclick="changeGalMode(this)"/></td>';
												$str .= '<td><p style="padding: 3px 0 0 3px">' . $name . '</p></td>';
											}
											return '<table width="100%" style="margin: 0 0 -12px 0"><tr>' . $str . '</tr></table>';
										?>
										</div>
										<div class="SpacerSmallColored"></div>
										<div id="GalControl_slideshow">
											<?if ( $this->currentMode == 'slideshow' ) { ?>
											<div id="GalleryControls_<?= $this->field->ID ?>">
												<div class="Container">
													<table cellspacing="0" cellpadding="0" border="0" width="100%" class="Gui">
														<tr>
															<td><strong>Bredde:</strong></td>
															<td><input type="text" id="galWidth_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->Width ?>"/></td>
														</tr>
														<tr>
															<td><strong>Høyde:</strong></td>
															<td><input type="text" id="galHeight_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->Height ?>"/></td>
														</tr>
														<tr>
															<td><strong>Animert:</strong></td>
															<td><input type="checkbox" id="galAnimated_<?= $this->field->ID ?>"<?= $this->settings->Animated == 1 ? ' checked="checked"' : '' ?>/></td>
														</tr>
														<tr>
															<td><strong>Pause (sek.):</strong></td>
															<td><input type="text" id="galPause_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->Pause >= 1 ? $this->settings->Pause : 2 ?>"/></td>
														</tr>
														<tr>
															<td><strong>Sortering:</strong></td>
															<td>
																<select id="galSortMode_<?= $this->field->ID ?>">
																<?
																	$array = array ( 'listmode_date', 'listmode_sortorder', 'listmode_fromto' );
																	$s = '';
																	foreach ( $array as $m )
																	{
																		$s = $m == $this->settings->SortMode ? ' selected="selected"' : '';
																		$str .= '<option value="' . $m . '"'.$s.'>' . i18n ( $m ) . '</option>';
																	}
																	return $str;
																?>
																</select>
															</td>
														</tr>
														<tr>
															<td>
																<strong>Fremvisning:</strong>
															</td>
															<td>
																<select id="galShowStyle_<?= $this->field->ID ?>">
																<?
																	$array = array ( 'showstyle_fade', 'showstyle_scrollx', 'showstyle_scrolly', 'showstyle_showroom' );
																	$s = '';
																	foreach ( $array as $m )
																	{
																		$s = $m == $this->settings->ShowStyle ? ' selected="selected"' : '';
																		$str .= '<option value="' . $m . '"'.$s.'>' . i18n ( $m ) . '</option>';
																	}
																	return $str;
																?>
																</select>
															</td>
														</tr>
														<tr>
															<td>
																<strong>Tempo:</strong>
															</td>
															<td>
																<select id="galSpeed_<?= $this->field->ID ?>">
																<?
																	for ( $a = 0; $a <= 100; $a++ )
																	{
																		$s = $a == $this->settings->Speed ? ' selected="selected"' : '';
																		$str .= '<option value="' . $a . '"'.$s.'>' . ($a*0.1) . ' sekunder</option>';
																	}
																	return $str;
																?>
																</select>
															</td>
														</tr>
													</table>
												</div>
											</div>
											<?}?>
										</div>
										<?
											if ( !$this->settings->ThumbWidth ) 
												$this->settings->ThumbWidth = 80;
											if ( !$this->settings->ThumbHeight ) 
												$this->settings->ThumbHeight = 60;
											if ( !$this->settings->ThumbColumns ) 
												$this->settings->ThumbColumns = 4;
										?>
										<div id="GalControl_gallery">
											<?if ( $this->currentMode == 'gallery' ) { ?>
											<div class="Container">
												<table cellspacing="0" cellpadding="0" border="0" width="100%" class="Gui">
													<tr>
														<td>
															<strong>Tommebilde bredde:</strong>
														</td>
														<td>
															<input type="text" id="galThumbWidth_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->ThumbWidth ?>"/>
														</td>
														<td>
															<strong>Bakgrunn:</strong>
														</td>
														<td>
															<input type="color" style="width: 30px" id="galPreviewColor_<?= $this->field->ID ?>" maxlength="7" value="<?= $this->settings->PreviewColor ?>"/>
														</td>
													</tr>
													<tr>
														<td>
															<strong>Tommebilde høyde:</strong>
														</td>
														<td colspan="3">
															<input type="text" id="galThumbHeight_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->ThumbHeight ?>"/>
														</td>
													</tr>
													<tr>
														<td>
															<strong>Tommebilde kolonner:</strong>
														</td>
														<td colspan="3">
															<input type="text" id="galThumbColumns_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->ThumbColumns ?>"/>
														</td>
													</tr>
													<tr>
														<td>
															<strong>Detalj bredde:</strong>
														</td>
														<td>
															<input type="text" id="galDetailWidth_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->DetailWidth ?>"/>
														</td>
														<td>
															<strong>Bakgrunn:</strong>
														</td>
														<td>
															<input type="color" style="width: 30px" id="galDetailColor_<?= $this->field->ID ?>" maxlength="7" value="<?= $this->settings->DetailColor ?>"/>
														</td>
													</tr>
													<tr>
														<td>
															<strong>Detalj høyde:</strong>
														</td>
														<td colspan="3">
															<input type="text" id="galDetailHeight_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->DetailHeight ?>"/>
														</td>
													</tr>
													<tr>
														<td><strong>Sortering:</strong></td>
														<td colspan="3">
															<select id="galSortMode_<?= $this->field->ID ?>">
															<?
																$array = array ( 'listmode_date', 'listmode_sortorder', 'listmode_fromto' );
																$s = '';
																foreach ( $array as $m )
																{
																	$s = $m == $this->settings->SortMode ? ' selected="selected"' : '';
																	$str .= '<option value="' . $m . '"'.$s.'>' . i18n ( $m ) . '</option>';
																}
																return $str;
															?>
															</select>
														</td>
													</tr>
													<tr>
														<td><strong>Skalering, preview:</strong></td>
														<td colspan="3">
															<select id="galPreviewScale_<?= $this->field->ID ?>">
															<?
																$array = array ( 'scalemode_proximity', 'scalemode_framed', 'scalemode_centered' );
																$s = '';
																foreach ( $array as $m )
																{
																	$s = $m == $this->settings->PreviewScale ? ' selected="selected"' : '';
																	$str .= '<option value="' . $m . '"'.$s.'>' . i18n ( $m ) . '</option>';
																}
																return $str;
															?>
															</select>
														</td>
													</tr>
													<tr>
														<td><strong>Skalering, detalj:</strong></td>
														<td colspan="3">
															<select id="galDetailScale_<?= $this->field->ID ?>">
															<?
																$array = array ( 'scalemode_proximity', 'scalemode_framed', 'scalemode_centered' );
																$s = '';
																foreach ( $array as $m )
																{
																	$s = $m == $this->settings->DetailScale ? ' selected="selected"' : '';
																	$str .= '<option value="' . $m . '"'.$s.'>' . i18n ( $m ) . '</option>';
																}
																return $str;
															?>
															</select>
														</td>
													</tr>
													<tr>
														<td><strong>Vise titler</strong></td>
														<td colspan="3">
															<input type="checkbox" id="galShowTitles_<?= $this->field->ID ?>"<?= $this->settings->ShowTitles?' checked="checked"':''?>/>
														</td>
													</tr>
													<tr>
														<td><strong>Vise lightbox beskrivelser</strong></td>
														<td colspan="3">
															<input type="checkbox" id="galLightboxDescriptions_<?= $this->field->ID ?>"<?= $this->settings->LightboxDescriptions?' checked="checked"':''?>/>
														</td>
													</tr>
												</table>
											</div>
											<?}?>
										</div>
										<div id="GalControl_archive">
											<?if ( $this->currentMode == 'archive' ) { ?>
											<div class="Container">
												<table cellspacing="0" cellpadding="0" border="0" width="100%" class="Gui">
													<tr>
														<td>
															<strong>Tommebilde bredde:</strong>
														</td>
														<td>
															<input type="text" id="galThumbWidth_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->ThumbWidth ?>"/>
														</td>
													</tr>
													<tr>
														<td>
															<strong>Tommebilde høyde:</strong>
														</td>
														<td>
															<input type="text" id="galThumbHeight_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->ThumbHeight ?>"/>
														</td>
													</tr>
													<tr>
														<td>
															<strong>Tommebilde kolonner:</strong>
														</td>
														<td>
															<input type="text" id="galThumbColumns_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->ThumbColumns ?>"/>
														</td>
													</tr>
													<tr>
														<td>
															<strong>Detalj bredde:</strong>
														</td>
														<td>
															<input type="text" id="galDetailWidth_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->DetailWidth ?>"/>
														</td>
													</tr>
													<tr>
														<td>
															<strong>Detalj høyde:</strong>
														</td>
														<td>
															<input type="text" id="galDetailHeight_<?= $this->field->ID ?>" size="4" value="<?= $this->settings->DetailHeight ?>"/>
														</td>
													</tr>
													<tr>
														<td>
															<strong>Sortering:</strong>
														</td>
														<td>
															<select id="galSortMode_<?= $this->field->ID ?>">
															<?
																$array = array ( 'listmode_date', 'listmode_sortorder' );
																$s = '';
																foreach ( $array as $m )
																{
																	$s = $m == $this->settings->SortMode ? ' selected="selected"' : '';
																	$str .= '<option value="' . $m . '"'.$s.'>' . i18n ( $m ) . '</option>';
																}
																return $str;
															?>
															</select>
														</td>
													</tr>
													<tr>
														<td><strong>Modus:</strong></td>
														<td>
															<select id="galArchiveMode_<?= $this->field->ID ?>">
															<?
																$array = array ( 'archivemode_thumbs', 'archivemode_list' );
																$s = '';
																foreach ( $array as $m )
																{
																	$s = $m == $this->settings->ArchiveMode ? ' selected="selected"' : '';
																	$str .= '<option value="' . $m . '"'.$s.'>' . i18n ( $m ) . '</option>';
																}
																return $str;
															?>
															</select>
														</td>
													</tr>
													<tr>
														<td><strong><?= i18n ( 'i18n_use_subfolders' ) ?>:</strong></td>
														<td>
															<select id="galRecursion_<?= $this->field->ID ?>">
																<option value="0"<?= !$this->settings->Recursion ? ' selected="selected"' : '' ?>><?= i18n ( 'i18n_no' ) ?></option>
																<option value="1"<?= $this->settings->Recursion ? ' selected="selected"' : '' ?>><?= i18n ( 'i18n_yes' ) ?></option>
															</select>
														</td>
													</tr>
												</table>
											</div>
											<?}?>
										</div>
									</div>
								</div>
							</div>
						</td>
					</tr>
				</table>
				<script type="text/javascript">
					initTabSystem ( 'gallerytabs' );
					
					// Initialize hider ---------------------------------------------
					var switcher = ge ( 'ImageSettingsSwitcher_<?= $this->field->ID ?>' );
					var hcontent = ge ( 'ImageSettings_<?= $this->field->ID ?>' );
					hcontent.switcher = switcher;
					hcontent.hide = function ()
					{
						this.td = this.parentNode.parentNode;
						this.pr = this.parentNode;
						this.td.removeChild ( this.pr );
						this.shown = false;
						this.switcher.refresh ();
					}
					hcontent.show = function ()
					{
						this.td.appendChild ( this.pr );
						this.shown = true;
						this.switcher.refresh ();
					}
					switcher.hcontent = hcontent;
					switcher.refresh = function ()
					{
						this.style.float = 'right';
						this.style.display = 'block';
					} 
					switcher.onclick = function ()
					{
						if ( this.hcontent.shown ) this.hcontent.hide ();
						else this.hcontent.show ();
						this.refresh ();
					}
					// Done hider ---------------------------------------------------
					
					// Initialize controls
					var modes = [ 'slideshow', 'gallery', 'archive' ];
					for ( var a = 0; a < modes.length; a++ )
					{
						if ( ( a == 0 && '<?= $this->currentMode ?>' == '' ) || modes[a] == '<?= $this->currentMode ?>' )
							ge( 'GalControl_' + modes[a] ).style.display = '';
						else ge( 'GalControl_' + modes[a] ).style.display = 'none';
					}
					hcontent.hide ();
					switcher.refresh ();
					
					// Change gallery mode
					function changeGalMode ( obj )
					{
						document.location = 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=galmode&mode=' + obj.value;
					}
					
					function galGetImageFolder ()
					{
						var ind = parseInt ( ge ( 'galCurrentImageIndex' ).value );
						if ( isNaN ( ind ) ) ind = 0;
						var j = new bajax ();
						j.openUrl ( 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=getimagefolder&ind=' + ind, 'get', true );
						j.onload = function ()
						{
							document.location = this.getResponseText ();
						}
						j.send ();
					}
					
					function galDelPicture ()
					{
						if ( confirm ( 'Er du sikker?' ) )
						{
							var ind = parseInt ( ge ( 'galCurrentImageIndex' ).value );
							if ( isNaN ( ind ) ) ind = 0;
							var mx_ = parseInt ( ge ( 'galCurrentImageIndexMax' ).value );
							if ( isNaN ( mx_ ) ) mx_ = 0;
							var j = new bajax ();
							j.openUrl ( 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=delimage&ind=' + ind, 'get', true );
							j.onload = function ()
							{
								if ( this.getResponseText () == 'ok' )
								{
									ge ( 'galCurrentImageIndex' ).value = ind - 1 > 0 ? (ind-1) : 0;
									ge ( 'galCurrentImageIndexMax' ).value = mx_ - 1;
									RefreshGalPreview ();
								}
							}
							j.send ();
						}
					}
					
					function galNextPicture ()
					{
						var ind = parseInt ( ge ( 'galCurrentImageIndex' ).value );
						if ( isNaN ( ind ) ) ind = 0;
						var mx_ = parseInt ( ge ( 'galCurrentImageIndexMax' ).value );
						if ( isNaN ( mx_ ) ) mx_ = 0;
						if ( ind + 1 >= mx_ ) ind = mx_-1;
						else ind++;
						ge ( 'galCurrentImageIndex' ).value = ind;
						RefreshGalPreview ();
					}
					
					function galPrevPicture ()
					{
						var ind = parseInt ( ge ( 'galCurrentImageIndex' ).value );
						if ( isNaN ( ind ) ) ind = 0;
						if ( ind - 1 <= 0 ) ind = 0;
						else ind--;
						ge ( 'galCurrentImageIndex' ).value = ind;
						RefreshGalPreview ();
					}
					
					function addSlideshowFolder ( obj )
					{
						var val = obj.value;
						if ( !val ) return;
						if ( confirm ( 'Er du sikker?' ) )
						{
							var j = new bajax ();
							j.openUrl ( 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=addfolder&fid=' + val + '&fieldid=' + <?= $this->field->ID ?>, 'get', true );
							j.onload = function ()
							{
								ge( 'ImageList_<?= $this->field->ID ?>' ).innerHTML = this.getResponseText ();
								for ( var a = 0; a < obj.options.length; a++ )
								{
									if ( a == 0 ) obj.options[a].selected="selected";
									else obj.options[a].selected="";
								}
								RefreshGalPreview ();
							}
							j.send ();
						}
					}
					function RefreshGalPreview ()
					{
						var ind = ge ( 'galCurrentImageIndex' ).value;
						var j = new bajax ( );
						j.openUrl ( 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=preview', 'post', true );
						j.addVar ( 'index', ind );
						j.onload = function ()
						{
							var r = this.getResponseText ().split ( '<!--separate-->' );
							ge( 'gal_preview' ).getElementsByTagName ( 'div' )[0].style.backgroundImage = 'url(' + r[0] + ')';
							ge( 'galPreviewImageTitle' ).innerHTML = r[1];
							texteditor.get('gal_text_box' ).getDocument().body.innerHTML = r[2];
							ge ( 'tags_<?= $this->field->ID ?>' ).value = r[3];
							ge ( 'link_<?= $this->field->ID ?>' ).value = r[4];
						}
						j.send();
					}
					
					function galSavePictureText ()
					{
						var ind = ge ( 'galCurrentImageIndex' ).value;
						var j = new bajax ();
						j.openUrl ( 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=savetext', 'post', true );
						j.addVar ( 'text', ge ( 'gal_text_box' ).value );
						j.addVar ( 'index', ind );
						j.addVar ( 'tags', ge ( 'tags_<?= $this->field->ID ?>' ).value );
						j.addVar ( 'link', ge ( 'link_<?= $this->field->ID ?>' ).value );
						j.onload = function (){}
						j.send ();
					}
					
					AddSaveFunction ( function ( )
					{
						var j = new bajax ( );
						j.openUrl ( 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=savesettings', 'post', true );
						// Add fields that can have duplicates
						var dupFields = [ 
							'ThumbWidth', 'ThumbHeight', 'ThumbColumns', 'DetailWidth', 'DetailHeight', 
							'SortMode', 'ArchiveMode', 'Recursion', 'Animated', 'Pause', 'Width', 'Height', 
							'Heading', 'ShowStyle', 'Speed', 'ShowTitles', 'LightboxDescriptions', 
							'PreviewScale', 'DetailScale', 'PreviewColor', 'DetailColor'
						];
						if ( document.getElementById ( 'GalControl_<?= $this->currentMode ?>' ) )
						{
							for ( var a = 0; a < dupFields.length; a++ )
							{
								var k = 'gal'+dupFields[a]+'_<?= $this->field->ID ?>';
								if ( !ge ( k ) ) continue;
								var val = ge ( k ).value;
								if ( ge ( k ).type == 'checkbox' )
									val = ge ( k ).checked ? '1' : '0';
								j.addVar ( 'key_' + dupFields[a], val );
							}
							j.addVar ( 'fieldid',  <?= $this->field->ID ?> );
							j.onload = function ()
							{
								ge( 'ImageList_<?= $this->field->ID ?>' ).innerHTML = this.getResponseText ();
							}
							j.send();
						}
					}
					);
				</script>
			</div>
			<div class="Spacer"></div>
