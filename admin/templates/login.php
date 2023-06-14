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
			ARENA CM - Login
		</title>
		<meta http-equiv="content-type: text/html; charset=utf-8"/>
		<link rel="stylesheet" href="admin/css/login_cm.css"/>
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
	<body style="" onload="initLogin()">
		<div id="UnderContent">
			ARENA CM v<?= ARENA_VERSION ?> &copy; 2011-2023 Idéverket AS.
		</div>
		<div id="Logo">
			<img src="admin/gfx/logo-neg.svg" alt="logo"/>
		</div>
		<div id="CenterBox">
			<div id="Content">
				<div class="Content">
					<form method="post" action="admin.php" name="loginform">
						<h2>
							<?= SITE_ID ?> admin: Login
						</h2>
						<p>
							Please log in with your username and password.
						</p>
						<hr/>
						<div class="Flexed">
							<div>
								<label for="loginUsername">Username:</label>
							</div>
							<div>
								<input type="text" size="11" value="" placeholder="your@name.com" name="loginUsername"/>
							</div>
						</div>
						<div class="Flexed">
							<div>
								<label for="loginPassword">Password:</label>
							</div>
							<div>
								<input type="password" size="11" value="" name="loginPassword"/>
							</div>
						</div>
						<div style="text-align: center">
							<hr/>
							<button type="button" id="loginButton">
								Log in
							</button>
						</div>
					</form>
					<script type="text/javascript" src="admin/javascript/login_cm.js"></script>
				</div>
			</div>
		</div>
	</body>
</html>
