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
	<div class="ToggleBox">
	
		<h2 class="BlockHead">
			<img src="admin/gfx/icons/image.png" /> Bilde: <?= $this->data->Name ?>
		</h2>
		<div class="BlockContainer">
			<div class="SubContainer">
				<table class="LayoutColumns">
					<tr>
						<td id="preview_image_<?= $this->data->Name ?>">
							<?if ( $this->data->DataInt ) { ?>
							<img style="border: 0" src="<?
								include_once ( "lib/classes/dbObjects/dbImage.php" );
								$img = new dbImage ( );
								$img->load ( $this->data->DataInt );
								return ( $img->getImageUrl ( 92, 48 ) );
							?>" />
							<?}?>
						</td>
						<td style="text-align: right">
							<button type="button" class="WebsnippetConfig">
								<img src="admin/gfx/icons/image.png" /> Endre bildet
							</button>
						</td>
					</tr>
				</table>
			</div>
		</div>
	
		<div class="BlockContainer">
		
			<?
				$this->name = "Extra_{$this->data->ID}_{$this->data->DataTable}_DataInt";
				$this->namelink = "Extra_{$this->data->ID}_{$this->data->DataTable}_DataString";
				$this->namelinktype = "Extra_{$this->data->ID}_{$this->data->DataTable}_DataDouble";
				$this->namewidth = "custom_{$this->data->ID}_{$this->data->DataTable}_width";
				$this->nameheight = "custom_{$this->data->ID}_{$this->data->DataTable}_height";
				$this->nameclass = "custom_{$this->data->ID}_{$this->data->DataTable}_class";
				$this->namescalemode = "custom_{$this->data->ID}_{$this->data->DataTable}_scalemode";
				list ( $this->width, $this->height, $this->class, ) = explode ( "\\t", $this->data->DataMixed );
			?>
			
			<script>
				function initImg<?= $this->data->Name ?> ( )
				{
					document.getElementById ( 'image<?= $this->data->Name ?>' ).onDragDrop = function ( )
					{
						if ( dragger.config.objectType != 'Image' )
						{
							alert ( 'Du kan kun slippe bilder her.' );
						}
						else
						{
							var bjax = new bajax ( );
							bjax.openUrl ( 'admin.php?plugin=extrafields&pluginaction=addimagetoimagefield&imageid=' + dragger.config.objectID + '&fieldid=<?= $this->data->ID ?>', 'get', true );
							bjax.onload = function ( )
							{
								dragger.removeTarget ( 'image<?= $this->data->Name ?>' );
								document.getElementById ( 'ImageContainer<?= $this->data->Name ?>' ).innerHTML = this.getResponseText ( );
								dragger.addTarget ( document.getElementById ( 'image<?= $this->data->Name ?>' ) );
							}
							bjax.send ( );
						}
					}
					dragger.addTarget ( document.getElementById ( 'image<?= $this->data->Name ?>' ) );
				}
				initImg<?= $this->data->Name ?>( );
				
				function updateImagePreviews<?= $this->data->Name ?>( )
				{
					var fjax = new bajax ( );
					fjax.openUrl ( 'admin.php?plugin=extrafields&pluginaction=getimagepreviews&fid=' + <?= $this->data->ID ?>, 'get', true );
					fjax.onload = function ( )
					{
						var results = this.getResponseText ( ).split ( '<!--separate-->' );
						
						// Editmode image
						dragger.removeTarget ( 'image<?= $this->data->Name ?>' );
						document.getElementById ( 'ImageContainer<?= $this->data->Name ?>' ).innerHTML = results[ 0 ];
						dragger.addTarget ( document.getElementById ( 'image<?= $this->data->Name ?>' ) );
						
						// Preview image
						document.getElementById ( 'preview_image_<?= $this->data->Name ?>' ).innerHTML = results[ 1 ];
					}
					fjax.send ( );
				}
				
				function deleteImg<?= $this->data->Name ?>( )
				{
					document.bjax = new bajax ( );
					document.bjax.openUrl ( 'admin.php?plugin=extrafields&pluginaction=deleteimagedata&oid=<?= $this->data->ID ?>', 'get', true );
					document.bjax.onload = function ( )
					{
						document.getElementById ( 'ImageContainer<?= $this->data->Name ?>' ).innerHTML = this.getResponseText ( );
						document.getElementById ( 'preview_image_<?= $this->data->Name ?>' ).innerHTML = '';
						initImg<?= $this->data->Name ?>( );
						document.bjax = 0;
					}
					document.bjax.send ( );
				}
				AddSaveFunction ( function ( )
				{
					var str = '';
					str += document.getElementById ( '<?= $this->namewidth ?>' ).value + "\t";
					str += document.getElementById ( '<?= $this->nameheight ?>' ).value + "\t";
					str += document.getElementById ( '<?= $this->nameclass ?>' ).value + "\t";
					str += document.getElementById ( '<?= $this->namescalemode ?>' ).value + "\t";
					actionExtraField ( 
						'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Small&id=<?= $this->data->ID ?>&field=DataMixed&value=' +
						encodeURIComponent ( str ) 
					);
				} );
			</script>
			
			<div class="SubContainer">
				<table class="Gui">
					<tr>
						<td style="vertical-align: top">
							<table class="Gui">
								<tr>
									<th>
										Velg fil på disk:
									</th>
									<td>
										<input type="file" id="<?= $this->name ?>" name="<?= $this->name ?>" />
									</td>
								</tr>
								<tr>
									<th>
										Valgfri lenke adresse: 
									</th>
									<td>
										<input type="text" size="30" id="<?= $this->namelink ?>" name="<?= $this->namelink ?>" value="<?= $this->data->DataString ?>" />
									</td>
								</tr>
								<tr>
									<th>
										Åpne lenken:
									</th>
									<td>
										<select name="<?= $this->namelinktype ?>">
											<?
												$options = Array ( '_self'=>'Vanlig', '_blank'=>'I nytt vindu' );
												$i = 0;
												foreach ( $options as $k=>$v )
												{
													if ( $i == floor ( $this->data->DataDouble ) ) $s = ' selected="selected"';
													else $s = '';
													$ostr .= '<option value="' . $i . '"' . $s . '>' . $v . '</option>';
													$i++;
												}
												return $ostr;
											?>
										</select>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<div id="ImageContainer<?= $this->data->Name ?>">
										<?if ( $this->data->DataInt ) { ?>
											<div class="SubContainer" style="float: left; padding: 2px; text-align: center" id="image<?= $this->data->Name ?>">
												<img style="border: 0" src="<?
													include_once ( "lib/classes/dbObjects/dbImage.php" );
													$img = new dbImage ( );
													$img->load ( $this->data->DataInt );
													return ( $img->getImageUrl ( 92, 48 ) );
												?>" /><br />
												<button type="button" style="width: 100%; margin: 2px 0 0 0" onclick="deleteImg<?= $this->data->Name ?>( )">
													<img src="admin/gfx/icons/image_delete.png" /> Fjern bildet
												</button>
											</div>
										<?}?>
										<?if ( $this->data->DataInt <= 0 ) { ?>
											<div style="width: 96px; height: 72px; float: right; margin: 0 0 0 8px; padding: 2px; text-align: center" id="image<?= $this->data->Name ?>" class="Dropzone" id="image<?= $this->data->Name ?>">
												<div style="margin-top: 30px">Slipp bilde her</div>
											</div>
										<?}?>
									</div>
									</td>
								</tr>
							</table>
						</td>
						<td style="vertical-align: top; border-left: 1px solid #ddd">
							<table class="Gui">
								<tr>
									<th>
										Bildebredde:
									</th>
									<td>
										<input type="text" size="10" id="<?= $this->namewidth ?>" name="<?= $this->namewidth ?>" value="<?= $this->width ?>"/>
									</td>
								</tr>
								<tr>
									<th>
										Bildehøyde:
									</th>
									<td>
										<input type="text" size="10" id="<?= $this->nameheight ?>" name="<?= $this->nameheight ?>" value="<?= $this->height ?>"/>
									</td>
								</tr>
								<tr>
									<th>
										Bildeskalering:
									</th>
									<td>
										<select id="<?= $this->namescalemode ?>" name="<?= $this->namescalemode ?>">
											<option value="framed"<?= $this->scalemode == 'framed' ? ' selected="selected"' : '' ?>>Kuttet</option>
											<option value="centered"<?= $this->scalemode == 'centered' ? ' selected="selected"' : '' ?>>Sentrert</option>
											<option value="proximity"<?= $this->scalemode == 'proximity' ? ' selected="selected"' : '' ?>>Aspekt</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>
										Klassenavn:
									</th>
									<td>
										<input type="text" size="10" id="<?= $this->nameclass ?>" name="<?= $this->nameclass ?>" value="<?= $this->class ?>"/>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			
				<?if ( $this->data->DataInt ) { ?>
					<br style="clear: both" />
				<?}?>
		
				<button type="button" onclick="swapToggleVisibility ( this.parentNode, this.parentNode.sibling )">
					<img src="admin/gfx/icons/cancel.png" /> Lukk
				</button>
			</div>
		
		</div>
		
	</div>
	
	
