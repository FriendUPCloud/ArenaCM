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

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>
			ARENA Enterprise - Logg inn
		</title>
		<meta http-equiv="content-type: text/html; charset=utf-8"/>
		<link rel="stylesheet" href="admin/css/login_enterprise.css"/>
		<link href='http://fonts.googleapis.com/css?family=Ubuntu:300,400,700' rel='stylesheet' type='text/css'>
		<?if(defined('ARENACM_ADMIN_CSS')){?>
		<link rel="stylesheet" href="<?= ARENACM_ADMIN_CSS ?>"/>
		<?}?>
		<!--[if IE 6]>
		<link rel="stylesheet" href="admin/css/login_ie6.css"/>
		<![endif]-->
		<!--[if IE 7]>
		<link rel="stylesheet" href="admin/css/login_ie7.css"/>
		<![endif]-->
		<script type="text/javascript" src="lib/javascript/arena-lib.js"></script>
		<script type="text/javascript" src="lib/javascript/bajax.js"></script>
		<?if(defined('ARENACM_ADMIN_JAVASCRIPT')){?>
		<script src="<?= ARENACM_ADMIN_JAVASCRIPT ?>"></script>
		<?}?>
	</head>
	<body>
		<div id="UnderContent">
			ARENA Enterprise v<?= ARENA_VERSION ?> &copy; 2011-2013 Idéverket AS.
		</div>
		<div id="Logo">
			<img src="admin/gfx/logo_arena_enterprise.png" alt="logo"/>
		</div>
		<div id="CenterBox">
			<div id="Content">
				<div class="Content">
					<form method="post" action="admin.php" name="loginform">
						<h2>
							<?= SITE_ID ?> admin: Logg inn
						</h2>
						<p>
							Logg deg inn med ditt brukernavn og passord.
						</p>
						<hr/>
						<table>
							<tr>
								<td>
									Brukernavn:
								</td>
								<td>
									<input type="text" size="11" value="" name="loginUsername"/>
								</td>
							</tr>
							<tr>
								<td>
									Passord:
								</td>
								<td>
									<input type="password" size="11" value="" name="loginPassword"/>
								</td>
							</tr>
						</table>
						<div style="text-align: center">
							<hr/>
							<button type="button" id="loginButton">
								<img src="admin/gfx/icons/key_go.png"/> Logg inn
							</button>
						</div>
					</form>
					<script type="text/javascript" src="admin/javascript/login_enterprise.js"></script>
				</div>
			</div>
		</div>
	</body>
</html>
