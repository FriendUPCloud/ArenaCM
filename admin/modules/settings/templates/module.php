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
<?
	// Get current date for use by stats
	if ( $_GET[ "date" ] )
		$_SESSION[ "settingsStatsCurDate" ] = $_GET[ "date" ];
	if ( !$_SESSION[ "settingsStatsCurDate" ] )
		$_SESSION[ "settingsStatsCurDate" ] = date ( "Y-m-d" );	
?>
		
		<script type="text/javascript" src="admin/modules/settings/javascript/main.js"></script>
		
		<div class="ModuleContainer">
			
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td width="*" valign="top">
					
						<h1>
							<?= i18n ( 'Your settings' ) ?><?= $this->user->_dataSource == 'core' ? (' ('.i18n('you are logged in as superuser').')') : '' ?>
						</h1>
					
						<form method="post" action="admin.php?module=settings&action=savesettings" name="sform">
							<div id="arenasettingtabs">
								<div class="tab" id="tabARENASettingsPlain">
									<img src="admin/gfx/icons/user_green.png"/> <?= i18n ( 'Personal settings' ) ?>
								</div>
								<div class="tab" id="tabARENASettingsAdvanced">
									<img src="admin/gfx/icons/wrench.png"/> <?= i18n ( 'Advanced' ) ?>
								</div>
								<div class="page" id="pageARENASettingsPlain">
									<div class="SubContainer">
										<h2>
											<?= i18n ( 'Website name' ) ?>
										</h2>
										<p>
											<input type="text" size="100" name="SiteTitle" value="<?= 
												SITE_TITLE
											?>"/>
										</p>
										<h2>
											<?= i18n ( 'Your user account' ) ?>:
										</h2>
										<table width="100%" cellspacing="0" cellpadding="0" border="0">
											<tr>
												<td>
													<strong><?= i18n ( 'Your name' ) ?>:</strong>
												</td>
												<td>
													<input type="text" size="30" name="Name" value="<?= $this->user->Name ?>"/>
												</td>
											</tr>
											<tr>
												<td>
													<strong><?= i18n ( 'E-mail' ) ?>:</strong>
												</td>
												<td>
													<input type="text" size="30" name="Email" value="<?= $this->user->Email ?>"/>
												</td>
											</tr>
											<tr>
												<td>
													<strong><?= i18n ( 'Change password' ) ?>:</strong>
												</td>
												<td>
													<input type="password" size="30" name="Password" value="********"/>
												</td>
											</tr>
											<tr>
												<td>
													<strong><?= i18n ( 'Confirm password' ) ?>:</strong>
												</td>
												<td>
													<input type="password" size="30" name="Password_Confirm" value="********"/>
												</td>
											</tr>
										</table>
										<div class="Spacer"></div>
										<?if ( file_exists ( 'extensions/footer' ) ) { ?>
										<h2>
											<?= i18n ( 'Footer on the website' ) ?>
										</h2>
										<p>
											<input type="text" size="100" name="Footertext" value="<?= 
												GetSettingValue ( 'settings', 'FooterText' ) 
											?>"/>
										</p>
										<div class="SpacerSmall"></div>
										<?}?>
									</div>
								</div>
								<div class="page" id="pageARENASettingsAdvanced">
									<table cellspacing="0" cellpadding="0">
										<tr>
											<td valign="top" width="50%" style="padding-right: 8px">
												<h1><?= i18n ( 'Mail settings' ) ?></h1>
												<div class="SubContainer">									
													<table class="Layout">
														<tr>
															<td width="160px"><strong><?= i18n ( 'Mail server (SMTP)' ) ?>:</strong></td>
															<td><input type="text" size="30" name="Email_SMTP" value="<?= 
																defined ( 'MAIL_SMTP_HOST' ) ? MAIL_SMTP_HOST : '' ?>"/></td>
														</tr>
														<tr>
															<td><strong><?= i18n ( 'Mailserver username' ) ?>:</strong></td>
															<td><input type="text" size="30" name="Email_Username" value="<?= 
																defined ( 'MAIL_USERNAME' ) ? MAIL_USERNAME : '' ?>"/></td>
														</tr>
														<tr>
															<td><strong><?= i18n ( 'Mailserver password' ) ?>:</strong></td>
															<td><input type="text" size="30" name="Email_Password" value="<?= 
																defined ( 'MAIL_PASSWORD' ) ? MAIL_PASSWORD : '' ?>"/></td>
														</tr>
														<tr>
															<td><strong><?= i18n ( 'E-mail reply-to' ) ?>:</strong></td>
															<td><input type="text" size="30" name="Email_ReplyTo" value="<?= 
																defined ( 'MAIL_REPLYTO' ) ? MAIL_REPLYTO : '' ?>"/></td>
														</tr>
														<tr>
															<td><strong><?= i18n ( 'E-mail reply fullname' ) ?>:</strong></td>
															<td><input type="text" size="30" name="Email_FromName" value="<?= 
																defined ( 'MAIL_FROMNAME' ) ? MAIL_FROMNAME : '' ?>"/></td>
														</tr>
														<tr>
															<td><strong><?= i18n ( 'E-mail transport' ) ?>:</strong></td>
															<td>
																<select name="Email_Transport">
																<?
																	$transports = array ( 'PhpMailer', 'PHP Native', 'ARENA Enterprise' );
																	foreach ( $transports as $t )
																	{
																		if ( $t == MAIL_TRANSPORT )
																			$o = ' selected="selected"';
																		else $o = '';
																		$str .= '<option value="' . $t . '"'.$o.'>' . $t . '</option>';
																	}
																	return $str;
																?>
																</strong>
															</td>
														</tr>
													</table>
												</div>
												<div class="SpacerSmallColored"></div>
												<h1><?= i18n ( 'Contentsettings' ) ?></h1>
												<div class="SubContainer">
													<table class="Layout">
														<tr>
															<td width="160px"><strong><?= i18n ( 'Contentgroup for menu' ) ?>:</strong></td>
															<td><select name="MenuContentGroup"><?
																$db =& dbObject::globalValue ( 'database' );
																if ( $groups = $db->fetchObjectRows ( 'SELECT ContentGroups AS Gs FROM ContentElement WHERE ID!=MainID' ) )
																{
																	$uniqueGroups = array ();
																	foreach ( $groups as $g )
																	{
																		$cgroups = explode ( ',', $g->Gs );
																		foreach ( $cgroups as $cg )
																		{
																			if ( !trim ( $cg ) ) continue;
																			if ( !in_array ( trim ( $cg ), $uniqueGroups ) )
																				$uniqueGroups[] = trim ( $cg );
																		}
																	}
																	$str = '<option value="">' . i18n ( 'Use standard setting' ) . '</option>';
																	foreach ( $uniqueGroups as $g )
																	{
																		if ( defined ( 'TOPMENU_CONTENTGROUP' ) && $g == TOPMENU_CONTENTGROUP )
																			$s = ' selected="selected"';
																		else $s = '';
																		$str .= '<option value="' . $g . '"' . $s . '>' . $g . '</option>';
																	}
																	return $str;
																}
															?></select></td>
														</tr>
														<tr>
															<td width="160px"><strong><?= i18n ( 'Sublevels to list on menus' ) ?>:</strong></td>
															<td><select name="MenuLevels"><?
																$options = array ( '0','1','2','3','4','5','10','20','40','99999' );
																$str = '';
																foreach ( $options as $opt )
																{
																	if ( $opt == 'ALL' ) $key = 'Standard';
																	else $key = $opt;
																	$s = $opt == NAVIGATION_LEVELS ? ' selected="selected"' : '';
																	$str .= '<option value="' . $opt . '"' . $s . '>' . $key . '</option>';
																}
																return $str;
															?></select></td>
														</tr>
														<tr>
															<td width="160px"><strong><?= i18n ( 'Listmode for menu' ) ?>:</strong></td>
															<td><select name="MenuMode"><?
																$options = array ( 'FOLLOW'=>i18n ( 'One sublevel' ), 'ALL'=>i18n( 'Show all' ) );
																$str = '';
																foreach ( $options as $value=>$key )
																{
																	$s = $value == NAVIGATION_MODE ? ' selected="selected"' : '';
																	$str .= '<option value="' . $value . '"' . $s . '>' . $key . '</option>';
																}
																return $str;
															?></select></td>
														</tr>
														<tr>
															<td width="160px"><strong><?= i18n ( 'Main contentgroup' ) ?>:</strong></td>
															<td><select name="MainContentGroup"><?
																$db =& dbObject::globalValue ( 'database' );
																if ( $groups = $db->fetchObjectRows ( 'SELECT ContentGroups AS Gs FROM ContentElement WHERE ID!=MainID' ) )
																{
																	$uniqueGroups = array ();
																	foreach ( $groups as $g )
																	{
																		$cgroups = explode ( ',', $g->Gs );
																		foreach ( $cgroups as $cg )
																		{
																			if ( !trim ( $cg ) ) continue;
																			if ( !in_array ( trim ( $cg ), $uniqueGroups ) )
																				$uniqueGroups[] = trim ( $cg );
																		}
																	}
																	$str = '<option value="">' . i18n ( 'Use standard setting' ) . '</option>';
																	foreach ( $uniqueGroups as $g )
																	{
																		if ( defined ( 'MAIN_CONTENTGROUP' ) && $g == MAIN_CONTENTGROUP )
																			$s = ' selected="selected"';
																		else $s = '';
																		$str .= '<option value="' . $g . '"' . $s . '>' . $g . '</option>';
																	}
																	return $str;
																}
															?></select></td>
														</tr>
													</table>
												</div>
												<!--<div class="SpacerSmallColored"></div>
												<h1>
													ARENA Admin Innstilinger
												</h1>
												<div class="SubContainer">
													<table>
														<tr>
															<td>
																Bruke ressursvennlig ARENA tema?
															</td>
															<td>
																<input id="arenasetting_ResourceFriendlyCSS" type="checkbox"<?= 
																	GetSettingValue ( 'ARENA_Usersettings_' . $GLOBALS[ 'Session' ]->AdminUser->ID, 'ResourceFriendlyCSS' ) ? ' checked="checked"': '' ?>/>
															</td>
														</tr>
													</table>
													<div class="Spacer"></div>
													<p>
														<button type="button" onclick="saveArenaSettings()">
															<img src="admin/gfx/icons/database_save.png"/> Lagre ARENA ADMIN innstillingene
														</button>
													</p>
												</div>-->
											<td>
											<td valign="top">
												<h1>
													<?= i18n ( 'Fallback settings' ) ?>
												</h1>
												<div class="SubContainer" style="padding: 0; margin: 0 0 4px 0">
													<table class="List">
														<tr class="sw2">
															<td>
																<?= i18n ( 'Default date format' ) ?>:
															</td>
															<td>
																<input type="text" name="Date_Format" value="<?= defined ( 'DATE_FORMAT' ) ? DATE_FORMAT : 'Y-m-d H:i:s' ?>"/> (ex: Y-m-d H:i:s)
															</td>
														</tr>
														<tr class="sw1">
															<td>
																<?= i18n ( 'i18n_jpeg_compression' ) ?>:
															</td>
															<td>
																<input type="range" name="JpegQuality" onchange="ge('JPEG_quality').value=this.value;" min="1" max="100" default="90" value="<?= defined ( 'IMAGE_JPEG_QUALITY' ) ? IMAGE_JPEG_QUALITY : '90' ?>"/><input type="text" size="4" readonly="readonly" id="JPEG_quality" style="width: 32px; text-align: center;" value="<?= defined ( 'IMAGE_JPEG_QUALITY' ) ? IMAGE_JPEG_QUALITY : '90' ?>"/>
															</td>
														</tr>
													</table>
												</div>
												<h1>
													<?= i18n ( 'Language settings' ) ?>
												</h1>
												<div class="SubContainer">
													<select name="Admin_Language">
														<?
															$langs = array ( 'no'=>'Norsk', 'en'=>'English' );
															foreach ( $langs as $lang=>$name )
															{
																$s = $lang == ADMIN_LANGUAGE ? ' selected="selected"' : '';
																$str .= '<option value="' . $lang . '"'.$s.'>' . $name . '</option>';
															}
															return $str;
														?>
													</select>
												</div>
												<div class="SpacerSmall"></div>
												<h1>
													<?= i18n ( 'i18n_Admin_Variants' ) ?>
												</h1>
												<div class="SubContainer" id="Variants">
													<?= $this->variants ?>
													<div class="SpacerSmallColored"></div>
													<p>
														<button onclick="newVariant ()">
															<img src="admin/gfx/icons/page_add.png"/> Legg til variant
														</button>
													</p>
												</div>
											</td>
										</tr>
									</table>
								</div>
							</div>
							<p>
								<button type="button" onclick="verifySettingsForm()">
									<img src="admin/gfx/icons/disk.png"/> <?= i18n ( 'Save settings' ) ?>
								</button>
							</p>
						</form>
						<?if ( file_exists ( BASE_DIR . '/' . $this->Settings->webalizer_path . '/daily_usage_' . date ( 'Ym' ) . '.png' ) ) { ?>
						<div class="SpacerSmallColored"></div>
						<h1>
							Daglig aktivitet denne måneden (Webalizer):
						</h1>
						<div class="Container">
							<?
								$file = BASE_DIR . '/' . $this->Settings->webalizer_path . '/daily_usage_' . date ( 'Ym' ) . '.png';
								if ( file_exists ( $file ) )
								{
									list ( $w, $h, ) = getimagesize ( $file );
									copy ( $file, BASE_DIR . '/upload/daily_usage_' . date ( 'Ym' ) . '.png' );
									return '<img src="' . BASE_URL . 'upload/daily_usage_' . date ( 'Ym' ) . '.png' . '" width="' . $w . '" height="' . $h . '" border="0" />';
								}
							?>
						</div>
						<?}?>
					</td>
				</tr>
			</table>
		</div>
		
		<br style="clear: both" />

		<script type="text/javascript">
			initTabSystem ( 'arenasettingtabs' );
		</script>
	
