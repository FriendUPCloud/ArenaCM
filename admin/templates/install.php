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

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>
			Arena2 - Installer
		</title>
		<link rel="stylesheet" href="admin/css/admin.css" />
		<style> body, td, th, div, p, input, select { font-size: 12px; } </style>
	</head>
	<body>
		<div style="display: block; padding: 110px 20px 20px 20px">
			<form method="post" action="admin.php?installer=2">
				<h1>
					Installer en ny Site
				</h1>
				<div class="Container">
					<p>
						Fyll inn alle nødvendige felter her for å installere denne siten.
					</p>
					<p>
						<strong>Site ID (navn)</strong>
					</p>
					<p>
						<input type="text" name="SiteName" size="33" value="" />
					</p>
					<p>
						<strong>Database navn:</strong>
					</p>
					<p>
						<input type="text" name="SqlDatabase" value="" />
					</p>
					<p>
						<strong>Database brukernavn:</strong>
					</p>
					<p>
						<input type="text" name="SqlUser" value="" />
					</p>
					<p>
						<strong>Database passord:</strong>
					</p>
					<p>
						<input type="text" name="SqlPass" value="" />
					</p>
					<p>
						<strong>Database host:</strong>
					</p>
					<p>
						<input type="text" name="SqlHost" size="33" value="" />
					</p>
					<p>
						<strong>Base dir (uten / på slutten):</strong>
					</p>
					<p>
						<input type="text" name="BaseDir" size="55" value="" />
					</p>
					<p>
						<strong>Base url (med / på slutten):</strong>
					</p>
					<p>
						<input type="text" name="BaseUrl" size="55" value="" />
					</p>
					<p>
						<button type="submit">
							<img src="admin/gfx/icons/disk.png" /> Installer siten!
						</button>
					</p>
				</div>
			</form>
		</div>
	</body>
</html>
