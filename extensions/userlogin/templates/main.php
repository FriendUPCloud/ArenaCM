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

	<?= enableTextEditor ( ) ?>

	<h1>Logins og nye brukere</h1>
	
	<div class="Container">
		
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td valign="top" width="50%">
					<form method="post">
		
						<input type="hidden" name="action" value="save" />
		
						<table class="Gui">
							<tr>
								<td>
									<p>
										<strong>Hvilke grupper skal nye brukere settes inn i?</strong>
									</p>
			
									<p>
										<select size="10" name="groups[]" style="width: 240px" multiple="multiple">
				
											<?= $this->groups ?>
				
										</select>
									</p>
				
									<p>
										<strong>Krever registreringsskjemaet at brukeren skriver inn adresse?</strong>
									</p>
			
									<p>
										<input type="hidden" name="needsaddress" value="<?= GetSettingValue ( 'Login_Extension', 'needsaddress' ) ?>" id="needsaddy"/>
										<input type="checkbox"<?= GetSettingValue ( 'Login_Extension', 'needsaddress' ) ? ' checked="checked"' : '' ?> onchange="document.getElementById ( 'needsaddy' ).value = this.checked ? '1' : '0'"/>
									</p>
			
									<p>
										<strong>Bruker email adresse som brukernavn?</strong>
									</p>
			
									<p>
										<input type="hidden" name="emailasusername" value="<?= GetSettingValue ( 'Login_Extension', 'emailasusername' ) ?>" id="emailasusername"/>
										<input type="checkbox"<?= GetSettingValue ( 'Login_Extension', 'emailasusername' ) ? ' checked="checked"' : '' ?> onchange="document.getElementById ( 'emailasusername' ).value = this.checked ? '1' : '0'"/>
									</p>
			
									<p>
										<strong>Gjemme nasjonalitetsvalg ved registrering?</strong>
									</p>
			
									<p>
										<input type="hidden" name="hidecountry" value="<?= GetSettingValue ( 'Login_Extension', 'hidecountry' ) ?>" id="hidecountry"/>
										<input type="checkbox"<?= GetSettingValue ( 'Login_Extension', 'hidecountry' ) ? ' checked="checked"' : '' ?> onchange="document.getElementById ( 'hidecountry' ).value = this.checked ? '1' : '0'"/>
									</p>
								</td>
								<td>
			
									<p>
										<strong>Gjemme hvor lenge en har vært logget inn?</strong>
									</p>
			
									<p>
										<input type="hidden" name="hidelogintime" value="<?= GetSettingValue ( 'Login_Extension', 'hidelogintime' ) ?>" id="hidelogintime"/>
										<input type="checkbox"<?= GetSettingValue ( 'Login_Extension', 'hidelogintime' ) ? ' checked="checked"' : '' ?> onchange="document.getElementById ( 'hidelogintime' ).value = this.checked ? '1' : '0'"/>
									</p>
			
									<p>
										<strong>Gjemme velkomsttekst?</strong>
									</p>
			
									<p>
										<input type="hidden" name="hidewelcometext" value="<?= GetSettingValue ( 'Login_Extension', 'hidewelcometext' ) ?>" id="hidewelcometext"/>
										<input type="checkbox"<?= GetSettingValue ( 'Login_Extension', 'hidewelcometext' ) ? ' checked="checked"' : '' ?> onchange="document.getElementById ( 'hidewelcometext' ).value = this.checked ? '1' : '0'"/>
									</p>
			
									<p>
										<strong>Åpne dialoger i nytt vindu?</strong>
									</p>
			
									<p>
										<input type="hidden" name="popupdialogs" value="<?= GetSettingValue ( 'Login_Extension', 'popupdialogs' ) ?>" id="popupdialogs"/>
										<input type="checkbox"<?= GetSettingValue ( 'Login_Extension', 'popupdialogs' ) ? ' checked="checked"' : '' ?> onchange="document.getElementById ( 'popupdialogs' ).value = this.checked ? '1' : '0'"/>
									</p>
			
									<p>
										<strong>Side å foreta registrering på:</strong>
									</p>
									<p>
										<select name="register_contentid">
											<option value="0">Ikke spesifiser side</option>
										<?
											include_once ( 'lib/classes/dbObjects/dbContent.php' );
					
											function getContentStructureOptions ( $parent, $language, $current, $r = '' )
											{
												$content = $GLOBALS[ "content" ];
												$oStr = '';
												if ( !$content ) return '';
												foreach ( $content as $cnt )
												{
													if ( ( $cnt->Language == $language || $parent != 0 || !$language ) && $cnt->Parent == $parent )
													{
														if ( $cnt->ID )
														{
															if ( !$cnt->Title ) $cnt->Title = $cnt->MenuTitle;
															if ( !$cnt->Title ) $cnt->Title = $cnt->SystemName;
															if ( !$cnt->Title ) $cnt->Title = $cnt->ID;
															if ( $current == $cnt->ID )
															{
																$w = ' selected="selected"';
																$GLOBALS[ 'logincurrentcontent' ] = $cnt;
															}
															else $w = '';
															$oStr .= "<option value=".$cnt->ID."$w>$r{$cnt->Title}</option>";
															$oStr .= getContentStructureOptions ( $cnt->ID, $language, $current, $r . "&nbsp;&nbsp;&nbsp;&nbsp;" );
														}
													}
												}
												return $oStr;
											}
					
					
											$GLOBALS[ 'content' ] = new dbObject ( 'ContentElement' );
											$GLOBALS[ 'content' ]->addClause ( 'WHERE', 'Language = ' . $GLOBALS[ 'Session' ]->CurrentLanguage );
											$GLOBALS[ 'content' ]->addClause ( 'WHERE', 'MainID = ID' );
											$GLOBALS[ 'content' ]->addClause ( 'WHERE', '!IsDeleted' );
											$GLOBALS[ 'content' ]->addClause ( 'WHERE', '!IsTemplate' );
											$GLOBALS[ 'content' ] = $GLOBALS[ 'content' ]->find ( );
											$db = dbObject::globalValue ( 'database' );
											return getContentStructureOptions ( 0, $GLOBALS[ 'Session' ]->CurrentLanguage, GetSettingValue ( 'Login_Extension', 'register_contentid' ) );
										?>
										</select>
									</p>
			
									<p>
										<strong>Sidefelt:</strong>
									</p>
									<p>
										<?
											include_once ( 'lib/classes/dbObjects/dbContent.php' );
											$db = dbObject::globalValue ( 'database' );
					
											if ( !( $page = $GLOBALS[ 'logincurrentcontent' ] ) )
											{
												$proto = new dbContent ( ); $proto = $proto->getRootContent ( );
												if ( $proto = $proto->findSingle ( ) )
												{ $page = $proto; }
											}
											if ( $page )
											{
												$oldv = GetSettingValue ( 'Login_Extension', 'modulefield' );
												$page->loadExtraFields ( );
												$ostr = '<option value="0">' . i18n ( 'Standard' ) . '</option>';
												foreach ( $page as $k=>$v )
												{
													if ( substr ( $k, 0, 6 ) == '_extra' )
													{
														list ( ,,$key ) = explode ( '_', $k );
														if ( $key == $oldv ) $s = ' selected="selected"'; else $s = '';
														$ostr .= '<option value="' . $key . '"' . $s . '>' . $key . '</option>';
													}
												}
											}
											else
											{
												$ostr = '<option value="0">' . i18n ( 'Standard' ) . '</option>';
											}
											return '<select name="modulefield">' . $ostr . '</select>';
										?>
									</p>
									
									<p>
										<strong>Bruker kallenavn?</strong>
									</p>
			
									<p>
										<input type="hidden" name="usenickname" value="<?= GetSettingValue ( 'Login_Extension', 'usenickname' ) ?>" id="usenickname"/>
										<input type="checkbox"<?= GetSettingValue ( 'Login_Extension', 'usenickname' ) ? ' checked="checked"' : '' ?> onchange="document.getElementById ( 'usenickname' ).value = this.checked ? '1' : '0'"/>
									</p>
									
									
									
									
								</td>
							</tr>
						</table>
						<div class="SpacerSmallColored"></div>
						<button type="submit">
							<img src="admin/gfx/icons/disk.png" /> Lagre
						</button>
		
					</form>
				</td>
				<td valign="top" width="50%">
					
					<h2>
						Alternativ registreringstekst
					</h2>
					<p>
						Nøkkelordene som blir endret av ARENA er: %register% and %forgotpassword%
					</p>
					<p>
						Disse nøkkelordene leder da brukeren til registrerings eller passord siden. Husk å ha dem inne i en lenke.
					</p>
					
					<textarea id="RegisterText" class="mceSelector" style="height: 140px"><?= GetSettingValue ( 'Login', 'RegisterText' ) ?></textarea>
					<div class="SpacerSmallColored"></div>
					<p>
						<button type="button" onclick="saveRegisterText ( )">
							<img src="admin/gfx/icons/page_save.png"/> Lagre tekst
						</button>
					</p>
					
				</td>
			</tr>
		</table>
		
	</div>
	
	<script type="text/javascript">
		function saveRegisterText ( )
		{
			var jax = new bajax ( );
			jax.openUrl ( 'admin.php?module=extensions&extension=userlogin&action=savetext', 'post', true );
			jax.addVar ( 'text', document.getElementById ( 'RegisterText' ).value );
			jax.onload = function ( )
			{
				alert ( this.getResponseText ( ) );
			}
			jax.send ( );
		}
	</script>
	
