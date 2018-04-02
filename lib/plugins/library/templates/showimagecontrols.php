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
<input type="hidden" id="LibraryImageDialogImageID" value="<?= $this->imageid ?>" />
<h1>
	Behandle bilde "<a id="ImageTitleH1"><?= $this->imagetitle ?></a>"
</h1>
<table cellspacing="0" cellpadding="0" width="100%" id="LibraryImageContainerTable">
	<tr>
		<td width="100%" style="vertical-align: top" colspan="3">
			<h2 class="BlockHead">
				Forh&aring;ndsvisning
			</h2>
			<div class="BlockContainer" style="text-align: center; overflow: auto; height: 185px" id="libraryImage">
			</div>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<div id="ImagePreview">
				<table cellspacing="0" cellpadding="0" style="width: 100%">
					<tr>
						<td style="vertical-align: top; width: 50%; padding-right: 2px">
						
							<input id="imagetitle" type="hidden" value="<?= $this->imagetitle ?>" />
							<input type="hidden" id="VisibleImageUrl" />
						
							<h2 class="BlockHead">
								Bildest&oslash;rrelse
							</h2>
							<div class="BlockContainer">
								<table class="Gui">
									<tr>
										<td>
											<strong>Aspekt:</strong>
										</td>
										<td>
											<select onchange="setPreviewImageScalemode ( '&scalemode=' + this.value )" id="ImageScaleMode">
												<option value="proximity">Behold aspekt</option>
												<option value="framed">Kutt til st&oslash;rrelse</option>
												<option value="cutalignright">Kutt og juster til h&oslash;yre</option>
												<option value="cutalignleft">Kutt og juster til venstre</option>
												<option value="centered">Sentrer i st&oslash;rrelse</option>
											</select>
										</td>
										<td rowspan="3" valign="top" style="vertical-align: top">
											<table class="Gui">
												<tr>
													<td>
														<strong>Bakgrunnsfarge:</strong>
													</td>
													<td style="width: 120px">
														<input type="text" style="width: 100px" value="#000000" id="ImageBackground"/>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td style="padding-right: <?= MarginSize ?>px">
											<strong>Bredde/h&oslash;yde:</strong>
										</td>
										<td>
											<input type="text" value="240" size="2" id="libraryImageWidth" style="text-align: center" />
									
											x
										
											<input type="text" value="180" size="2" id="libraryImageHeight" style="text-align: center" /> 
											
											(org: <a style="cursor: hand; cursor: pointer" id="imagedimensions" onclick="var i = this.innerHTML + ''; i = i.split ( 'x' ); document.getElementById ( 'libraryImageWidth' ).value = i[ 0 ]; document.getElementById ( 'libraryImageHeight' ).value = i[ 1 ];"><?= $this->imagedimensions ?></a>)
											
										</td>
									</tr>
									<tr>
										<td style="padding-right: <?= MarginSize ?>px">
											<strong>Størrelseforslag:</strong>
										</td>
										<td>
											<select onchange="setSelectImageSize ( this.value )">
												<option value="original">Orginal størrelse</option>
												<option value="original">-----------------------</option>
												<option value="50%">50%</option>
												<option value="25%">25%</option>
												<option value="10%">10%</option>
												<option value="original">-----------------------</option>
												<option value="640x480">640 x 480 (VGA HQ)</option>
												<option value="320x240">320 x 240 (VGA LQ)</option>
												<option value="160x120">160 x 120 (Tommelbilde)</option>
												<option value="80x80">80 x 80 (Passbilde)</option>
												<?
													if ( defined ( 'LIBRARY_IMAGESIZES' ) )
													{
														if ( $sizes = explode ( ',', LIBRARY_IMAGESIZES ) )
														{
															$s = '<option value="original">-----------------------</option>';
															$s .= '<option value="original">Egendefinerte størrelser:</option>';
															$s .= '<option value="original">-----------------------</option>';
															foreach ( $sizes as $size )
															{
																list ( $w, $h, $text, ) = explode ( 'x', $size );
																$sizeLtrl = $w . ' x ' . $h;
																if ( $text )
																	$sizeLtrl .= ' (' . $text . ')';
																$s .= '<option value="' . $size . '">' . $sizeLtrl . '</option>';
															}
															return $s;
														}
													}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<td colspan="3">
											<button type="button" onclick="showModalPreviewImage ( '<?= $this->imageid ?>', document.getElementById ( 'libraryImageWidth' ).value, document.getElementById ( 'libraryImageHeight' ).value )">
												<img src="admin/gfx/icons/image_edit.png" /> Endre st&oslash;rrelse
											</button>	
											<button type="button" onclick="setCookie ( 'arenaClipBoardImage', document.getElementById ( 'libraryImage' ).innerHTML ); pasteImage ( )">
												<img src="admin/gfx/icons/image_link.png" /> Sett inn vist
											</button>
											<button type="button" onclick="setCookie ( 'arenaClipBoardImage', '<img src=\'<?= BASE_URL ?>upload/images-master/<?= $this->imagefilename ?>\' />' ); pasteImage ( true )">
												<img src="admin/gfx/icons/image_link.png" /> Sett inn orginal
											</button>		
										</td>
									</tr>
								</table>
								<input type="hidden" id="UseDescriptionAsSubtext" />
								<input type="hidden" id="UseImagetitleAsHeader" />
							</div>
						</td>
						<!--<td style="vertical-align: top; width: 50%">
							<h2 class="BlockHead">
								Bilde innlimingsvalg
							</h2>
							<div class="BlockContainer" style="height: 147px">
								<p>
									<input type="checkbox" id="UseImagetitleAsHeader" /> Bruk bildetittel som overskrift
								</p>
								
								<textarea id="imagedescription" style="width: 300px; -moz-box-sizing: border-box; box-sizing: border-box"><?= $this->imagedescription ?></textarea>
								<p>
									<input type="checkbox" id="UseDescriptionAsSubtext" /> Bruk bildetekst som undertekst
								</p>
							
								<button type="button" onclick="setCookie ( 'arenaClipBoardImage', document.getElementById ( 'libraryImage' ).innerHTML ); pasteImage ( )">
									<img src="admin/gfx/icons/image_link.png" /> Sett inn vist
								</button>
								<button type="button" onclick="setCookie ( 'arenaClipBoardImage', '<img src=\'<?= BASE_URL ?>upload/images-master/<?= $this->imagefilename ?>\' />' ); pasteImage ( true )">
									<img src="admin/gfx/icons/image_link.png" /> Sett inn orginal
								</button>
							</div>
						</td>-->
					</tr>
					<tr>
						<td>
							<div class="SpacerSmallColored"></div>
							<?if ( !$this->Mode && $this->edit ) { ?>
							<button type="button" onclick="pluginLibrarySaveImage ( '<?= $this->imageid ?>' <? if( $_REQUEST['module' ] ) return ", '".$_REQUEST['module' ]."'"; ?> )">
								<img src="admin/gfx/icons/disk.png" /> Lagre
							</button>
							<button type="button" onclick="pluginLibraryDeleteImage ( '<?= $this->imageid ?>' <? if( $_REQUEST['module' ] ) return ", '".$_REQUEST['module' ]."'"; ?> ); removeModalDialogue ( 'imageDia' )">
								<img src="admin/gfx/icons/bin.png" /> Slett bildet
							</button>
							<?}?>
							<?if ( !$this->Mode ) { ?>
							<button type="button" onclick="removeModalDialogue ( 'imageDia' )">
								<img src="admin/gfx/icons/cancel.png" /> Lukk
							</button>
							<?}?>
							<?if ( $this->Mode == 'librarydialog' ) { ?>
							<button type="button" onclick="replaceModalDialogue ( 'library', 600, 540, 'admin.php?plugin=library&pluginaction=renderfordialog', setupLibraryDialog )">
								<img src="admin/gfx/icons/arrow_left.png" /> Tilbake til biblioteket
							</button>
							<button type="button" onclick="removeModalDialogue ( 'library' )">
								<img src="admin/gfx/icons/cancel.png" /> Lukk vinduet
							</button>
							<?}?>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
</table>

