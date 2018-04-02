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

?><!DOCTYPE html><?= header ( 'Content-type: text/html; charset=utf-8;' ); ?>
<html>
	<head>
		<title><?= i18n ( 'Install ARENACM Site' ) ?></title>
		<meta http-equiv="content-type" content="text/html; charset=utf8;"/>
		<link rel="stylesheet" href="admin/css/admin.css"/>
	</head>
	<body style="background: #e8e8e8">
		<div class="ModuleContainer">
			<div class="Container">
				<h1>
					<?= i18n ( 'Install ARENACM Site' ) ?>
				</h1>
				<form method="post" action="index.php?step=2" name="installform">
					<div class="Container">
						<div id="Block1">
							<table cellspacing="0" cellpadding="4" width="600px">
								<tr>
									<td width="130px"><strong>Arena Site Type:</strong></td>
									<td>
										<input type="radio" name="siteType" value="0" onclick="document.getElementById('Block3').style='';document.getElementById('Block2').style.display='none'"/> <span>MultiSite</span>
										<input type="radio" name="siteType" value="1" checked="checked" onclick="document.getElementById('Block3').style.display='none';document.getElementById('Block2').style=''"/> <span>SingleSite</span>
									</td>
								</tr>
							</table>
							<div class="SpacerSmallColored"></div>
							<div class="Spacer"></div>
						</div>
						<div id="Block2">
							<?if(file_exists('subether')){?>
							<h2>
								Include extended modules
							</h2>
							<p>
								Check of to install extended modules.
							</p>
							<table cellspacing="0" cellpadding="4" width="600px">
								<tr>
									<td width="100px"><strong>Treeroot:</strong></td>
									<td><input type="checkbox" value="1" name="subether" checked="checked"/></td>
								</tr>
							</table>
							<div class="SpacerSmallColored"></div>
							<div class="Spacer"></div>
							<?}?>
						</div>
						<div id="Block3" style="display:none;">
							<h2>
								Login information to ARENACM core database:
							</h2>
							<div class="Spacer"></div>
							<p>
								The first step in installing this ARENACM site is to 
								enter in the ARENACM core database information, so that
								we can register the site with the core. All important 
								settings are stored there.
							</p>
							<p>
								NB: Make sure that the ARENACM core database user has 
								the privileges to create a new database for the site. 
								If you do not have these privileges, then you will have 
								to set up the site database manually.
							</p>
							<table cellspacing="0" cellpadding="4" width="600px">
								<tr>
									<td><strong>MySQL hostname:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['coreHostname'] ) ? $_POST['coreHostname'] : 'localhost' ?>" name="coreHostname"/></td>
								</tr>
								<tr>
									<td><strong>MySQL database:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['coreDatabase'] ) ? $_POST['coreDatabase'] : '' ?>" name="coreDatabase"/></td>
								</tr>
								<tr>
									<td width="175px"><strong>MySQL username:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['coreUsername'] ) ? $_POST['coreUsername'] : '' ?>" name="coreUsername"/></td>
								</tr>
								<tr>
									<td><strong>MySQL password:</strong></td>
									<td><input type="password" value="<?= isset( $_POST['corePassword'] ) ? $_POST['corePassword'] : '' ?>" name="corePassword"/></td>
								</tr>
							</table>
							<div class="SpacerSmallColored"></div>
							<div class="Spacer"></div>
						</div>
						<div id="Block4">
							<h2>
								Setup your site info
							</h2>
							<p>
								Now enter the site information that will be used to initialize the site.
								As stated above, you need to create the site database manually if the ARENACM core
								user has no "CREATE DATABASE" privileges.
							</p>
							<table cellspacing="0" cellpadding="4" width="600px">
								<tr>
									<td width="175px"><strong>Site ID (literal):</strong></td>
									<td><input type="text" value="<?= isset( $_POST['SiteID'] ) ? $_POST['SiteID'] : 'my_site' ?>" name="siteID"/></td>
								</tr>
								<tr>
									<td><strong>Site MySQL hostname:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['siteHostname'] ) ? $_POST['siteHostname'] : 'localhost' ?>" name="siteHostname"/></td>
								</tr>
								<tr>
									<td><strong>Site MySQL database:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['siteDatabase'] ) ? $_POST['siteDatabase'] : '' ?>" name="siteDatabase"/></td>
								</tr>
								<tr>
									<td><strong>Site MySQL username:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['siteUsername'] ) ? $_POST['siteUsername'] : '' ?>" name="siteUsername"/></td>
								</tr>
								<tr>
									<td><strong>Site MySQL password:</strong></td>
									<td><input type="password" value="<?= isset( $_POST['sitePassword'] ) ? $_POST['sitePassword'] : '' ?>" name="sitePassword"/></td>
								</tr>
							</table>
							<div class="SpacerSmallColored"></div>
							<div class="Spacer"></div>
						</div>
						<div id="Block5">
							<h2>
								Setup your account info
							</h2>
							<p>
								Now enter your superuser information that will be used to login to the admin site.
							</p>
							<table cellspacing="0" cellpadding="4" width="600px">
								<tr>
									<td><strong>Account name:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['loginName'] ) ? $_POST['loginName'] : 'ArenaCM Admin' ?>" name="loginName"/></td>
								</tr>
								<tr>
									<td><strong>Account email:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['loginEmail'] ) ? $_POST['loginEmail'] : 'admin@'.$_SERVER['SERVER_NAME'] ?>" name="loginEmail"/></td>
								</tr>
								<tr>
									<td width="175px"><strong>Login username:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['loginUsername'] ) ? $_POST['loginUsername'] : 'arenauser' ?>" name="loginUsername"/></td>
								</tr>
								<tr>
									<td><strong>Login password:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['loginPassword'] ) ? $_POST['loginPassword'] : 'arenapassword' ?>" name="loginPassword"/></td>
								</tr>
							</table>
							<div class="SpacerSmallColored"></div>
							<div class="Spacer"></div>
						</div>
						<div id="Block6">
							<h2>
								Setup site mail info (optional)
							</h2>
							<p>
								Now enter your mailserver (SMTP) information that will be used to send out emails (ex new account info) from the system.
							</p>
							<table cellspacing="0" cellpadding="4" width="600px">
								<tr>
									<td><strong>Mailserver (SMTP):</strong></td>
									<td><input type="text" value="<?= isset( $_POST['mailHost'] ) ? $_POST['mailHost'] : '' ?>" name="mailHost"/></td>
								</tr>
								<tr>
									<td><strong>Mailserver username:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['mailUsername'] ) ? $_POST['mailUsername'] : '' ?>" name="mailUsername"/></td>
								</tr>
								<tr>
									<td width="175px"><strong>Mailserver password:</strong></td>
									<td><input type="password" value="<?= isset( $_POST['mailPassword'] ) ? $_POST['mailPassword'] : '' ?>" name="mailPassword"/></td>
								</tr>
								<tr>
									<td><strong>E-mail reply-to:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['mailReplyTo'] ) ? $_POST['mailReplyTo'] : '' ?>" name="mailReplyTo"/></td>
								</tr>
								<tr>
									<td><strong>E-mail reply fullname:</strong></td>
									<td><input type="text" value="<?= isset( $_POST['mailFromName'] ) ? $_POST['mailFromName'] : '' ?>" name="mailFromName"/></td>
								</tr>
							</table>
							<div class="SpacerSmallColored"></div>
							<div class="Spacer"></div>
						</div>
						<button type="button" onclick="checkform()">
							Finish <img src="admin/gfx/icons/arrow_right.png"/>
						</button>
					</div>
				</form>
			</div>
		</div>
		<script type="text/javascript">
			function checkform ()
			{
				var exclude = [ 'coreHostname', 'coreDatabase', 'coreUsername', 'corePassword', 'mailHost', 'mailUsername', 'mailPassword', 'mailReplyTo', 'mailFromName' ];
				var form = document.installform;
				
				var eles = document.getElementsByTagName ( 'input' );
				for ( var a = 0; a < eles.length; a++ )
				{
					if ( form['siteType'].value == 1 && exclude.indexOf(eles[a].name) > 0 ) continue;
					
					if ( eles[a].type != 'radio' && eles[a].type != 'checkbox' && eles[a].value <= 1 )
					{
						alert ( 'Du må fylle inn ' + eles[a].name.toLowerCase () + '.' );
						eles[a].focus();
						return false;
					}
				}
				document.installform.submit();
			}
		</script>
	</body>
</html>

