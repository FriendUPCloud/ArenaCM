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
		<?
			$GLOBALS[ 'document' ]->addRel ( 'stylesheet', 'admin/modules/users/css/user.css' );
		?>
		<div class="ModuleContainer">
			
			<table class="Layout">
				<tr>
					<td width="100%" style="vertical-align: top">
						<h1>
							<div class="HeaderBox">
								<button type="button" onclick="checkForm()">
									<img src="admin/gfx/icons/<?= !$this->user->ID ? "user_add" : "disk" ?>.png" />
								</button>
								<button type="button" onclick="checkForm('close')">
									<img src="admin/gfx/icons/accept.png" />
								</button>
								<?if ( $GLOBALS[ 'Session' ]->AdminUser->_dataSource == 'core' && $this->user->IsTemplate == '1' ) { ?>
								<button type="button" onclick="document.location='admin.php?module=users&action=resignastemplate&uid=<?= $this->user->ID ?>'">
									<img src="admin/gfx/icons/page_white_delete.png" />
								</button>
								<?}?>
								<button type="button" onclick="document.location='admin.php?module=users'">
									<img src="admin/gfx/icons/cancel.png" />
								</button>
							</div>
							<?= $this->user->ID ? "Endre kontoen til ".$this->user->Name."" : "Ny bruker" ?>
						</h1>
						<div class="Container">
							<form name="userform" method="post" enctype="multipart/form-data" action="admin.php?module=users&amp;action=user&amp;function=user">
								<input type="hidden" name="ID" id="HiddenID" value="<?= $this->user->ID ?>" />
								<input type="hidden" name="IsTemplate" name="IsTemplate" value="<?= $this->user->IsTemplate ?>" />
								<table width="100%" cellspacing="0" cellpadding="0" border="0">
									<tr>
										<td class="Container">
											<table width="100%" cellspacing="0" cellpadding="0" border="0" id="MainCont">
												<tr>
													<td>
														<table<?if ( $this->user->ID ) { ?> width="100%"<?}?> cellspacing="0" cellpadding="0" border="0" class="Gui">
															<tr>
																<td width="120">
																	<strong>Fullt navn:</strong>
																</td>
																<td>
																	<input type="text" size="30" name="Name" value="<?= $this->user->Name ?>" />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>E-post adresse:</strong>
																</td>
																<td>
																	<input type="text" size="30" name="Email" value="<?= $this->user->Email ?>" />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>Brukernavn:</strong>
																</td>
																<td>
																	<input type="text" size="30" name="Username" value="<?= $this->user->Username ?>" />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>Passord:</strong>
																</td>
																<td>
																	<input type="password" size="11" id="Password" name="Password" value="<?= $this->user->ID ? "********" : "" ?>" />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>Verifiser passord:</strong>
																</td>
																<td>
																	<input type="password" size="11" id="PasswordVerify" value="<?= $this->user->ID ? "********" : "" ?>" />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>Passbilde:</strong>
																</td>
																<td colspan="2">
																	<input type="file" name="ImageStream" />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>Er admin:</strong>
																</td>
																<td>
																	<input type="hidden" name="IsAdmin" id="isadmin" value="<?= $this->user->IsAdmin ?>" />
																	<input type="checkbox" onchange="document.getElementById ( 'isadmin' ).value = this.checked ? '1' : '0'"<?= $this->user->IsAdmin ? " checked='checked'" : "" ?> />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>Hustelefon:</strong>
																</td>
																<td>
																	<input type="text" size="15" name="Telephone" value="<?= $this->user->Telephone ?>" />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>Mobiltelefon:</strong>
																</td>
																<td>
																	<input type="text" size="15" name="Mobile" value="<?= $this->user->Mobile ?>" />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>Adresse:</strong>
																</td>
																<td>
																	<input type="text" size="30" name="Address" value="<?= $this->user->Address ?>" />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>Postnr./Poststed:</strong>
																</td>
																<td>
																	<input type="text" size="4" name="Postcode" value="<?= $this->user->Postcode ?>" /> <input type="text" size="23" name="City" value="<?= $this->user->City ?>" />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>Land:</strong>
																</td>
																<td>
																	<input type="text" size="30" name="Country" value="<?= $this->user->Country ?>" />
																</td>
															</tr>
															<tr>
																<td>
																	<strong>Bruker er deaktivert:</strong>
																</td>
																<td style="text-align: left">
																	<input type="hidden" name="IsDisabled" value="<?= $this->user->IsDisabled ?>" id="userIsDisabled">
																	<input type="checkbox"<?= $this->user->IsDisabled ? ' checked="checked"' : '' ?> onchange="document.getElementById ( 'userIsDisabled' ).value = this.checked ? '1' : '0'">
																</td>
															</tr>
															<tr>
																<td style="width: 120px; vertical-align: top">
																	<strong>Gruppe(r):</strong>
																</td>
																<td style="text-align: left; padding-right: 8px">
																	<select name="Groups[]" size="10" style="width: 230px" multiple="multiple" id="groupsel">
																		<?if ( $GLOBALS[ 'Session' ]->AdminUser->_dataSource == 'core' ) { ?>
																		<option value="0"<?= ( !$this->user->InGroups && !$GLOBALS[ 'Session' ]->UsersCurrentGroup ) ? " selected='selected'" : "" ?>>Uten gruppe</option>
																		<option value="0">----------------------------------------------------------------------------</option>
																		<?}?>
																		<?= $this->groups ?>
																	</select>
																</td>
															</tr>
														</table>
													</td>
													<?if ( $this->user->ID ) { ?>
													<td width="200" style="text-align: center; vertical-align: top; padding-right: 8px">
														<img id="Passphoto" src="<?= $this->Passphoto ?>" /><br />
														<button type="button" onclick="deletePassphoto()">
															<img src="admin/gfx/icons/image_delete.png"> Slett bilde
														</button>
													</td>
													<?}?>
												</tr>
												<tr>
													<td colspan="2" style="padding: 8px">
														<?if ( !$this->user->ID || $this->canWrite ) { ?>
														<button type="button" onclick="checkForm()">
															<img src="admin/gfx/icons/<?= !$this->user->ID ? "user_add" : "disk" ?>.png" /> <?= $this->user->ID ? "Lagre" : "Opprett" ?>
														</button>
														<button type="button" onclick="checkForm('close')">
															<img src="admin/gfx/icons/accept.png" /> <?= $this->user->ID ? "Lagre og lukk" : "Opprett og lukk" ?>
														</button>
														<?}?>
														<?if ( $this->user->ID && ( $GLOBALS[ 'Session' ]->AdminUser->isSuperUser ( ) || $GLOBALS[ 'Session' ]->AdminUser->_dataSource == 'core' ) && $this->user->IsTemplate == '1' ) { ?>
														<button type="button" onclick="document.location='admin.php?module=users&action=resignastemplate&uid=<?= $this->user->ID ?>'">
															<img src="admin/gfx/icons/page_white_delete.png" /> Fjern som mal
														</button>
														<?}?>
														<?if ( $this->user->ID && ( $GLOBALS[ 'Session' ]->AdminUser->isSuperUser ( ) || $GLOBALS[ 'Session' ]->AdminUser->_dataSource == 'core' ) && !( $this->user->IsTemplate == '1' ) ) { ?>
														<button type="button" onclick="document.location='admin.php?module=users&action=setastemplate&uid=<?= $this->user->ID ?>'">
															<img src="admin/gfx/icons/page_white_go.png" /> Sett som brukermal 
														</button>
														<?}?>
														<button type="button" onclick="document.location='admin.php?module=users'">
															<img src="admin/gfx/icons/cancel.png" /> Lukk
														</button>
													</td>
												</tr>
											</table>
										</td>
										<?if ( $this->user->ID ) { ?>
										<td style="vertical-align: top; padding: 0; padding-left: 16px; width: 65%">
											
											<div class="pages" id="UserExtrafields">
												<div class="tab" id="tabUserExtraFields">
													<img src="admin/gfx/icons/table_row_insert.png"> Utvidet informasjon
												</div>
												<div class="tab" id="tabUserExtrafieldSetup">
													<img src="admin/gfx/icons/wrench.png"> Legg til felter
												</div>
												<div class="page" id="pageUserExtraFields">
													<?= getPluginFunction ( 'extrafields', 'adminrender', Array ( 'ContentID'=>$this->user->ID, 'ContentType'=>'Users' ) ) ?>
													<?= renderPlugin ( 'objectconnector', Array ( 'ObjectType'=>'Users', 'ObjectID'=>$this->user->ID ) ) ?>
												</div>
												<div class="page" id="pageUserExtrafieldSetup">
													<?= renderPlugin ( 'extrafields', Array ( 'ContentID'=>$this->user->ID, 'ContentType'=>'Users' ) ) ?>
												</div>
											</div>
											<script type="text/javascript">
												initTabSystem ( 'UserExtrafields' );
											</script>
										
										</td>
										<?}?>
									</tr>
								</table>
							</form>
						</div>
					</td>
				</tr>
			</table>					
		</div>
		
		<script src="admin/modules/users/javascript/user.js"></script>
		<script>
			var opts = document.getElementById ( 'groupsel' ).options;
			var sel = false;
			for ( var a = 0; a < opts.length; a++ )
			{
				var opt = opts[ a ];
				if ( opt.selected )
					sel = true;
			}
			if ( !sel && opts.length )
			{
				opts[ 0 ].selected = "selected";
			}
			<?if ( $this->user->ID ) { ?>
			initToggleBoxes ( document.getElementById ( 'pageUserExtraFields' ) );
			addExtraFieldUpdateFunction ( function ( )
			{
				var jax = new bajax ( );
				jax.openUrl ( 'admin.php?plugin=extrafields&pluginaction=adminrender&contentid=' + getUrlVar ( 'uid' ) + '&contenttype=Users', 'get', true );
				jax.onload = function ( )
				{
					document.getElementById ( 'pageUserExtraFields' ).innerHTML = this.getResponseText ( );
					initToggleBoxes ( document.getElementById ( 'pageUserExtraFields' ) );
				}
				jax.send ( );
			} );
			<?}?>
			<?if ( $this->user->ID ) { ?>
			var use = Array ( document.getElementById ( 'pageUserExtrafieldSetup' ), document.getElementById ( 'pageUserExtraFields' ) );
			var mai = document.getElementById ( 'MainCont' );
			for ( var a = 0; a < use.length; a++ )
			{
				if ( getElementHeight ( use[a] ) < getElementHeight ( mai ) )
				{
					use[a].style.height = ( getElementHeight ( mai ) - 52 ) + 'px';
				}
			}
			<?}?>
		</script>
		
		<?= enableTextEditor ( ); ?>
		

