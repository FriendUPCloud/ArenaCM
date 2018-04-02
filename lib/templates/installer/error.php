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

?><!DOCTYPE html><? header ( 'Content-type: text/html; charset=utf-8;' ); ?>
<html>
	<head>
		<title><?= i18n ( 'Install ARENACM Site - Error' ) ?></title>
		<meta http-equiv="content-type" content="text/html; charset=utf8;"/>
		<link rel="stylesheet" href="admin/css/admin.css"/>
	</head>
	<body style="background: #e8e8e8">
		<div class="ModuleContainer">
			<div class="Container">
				<h1>
					<?= i18n ( 'Install ARENACM Site - Error' ) ?>
				</h1>
				<div class="Container">
					<div style="border: 2px solid #a00; background: #ffc; padding: 15px"><?= $this->error ?></div>
					<div class="SpacerSmallColored"></div>
					<!--<button type="button" onclick="document.location.reload()">-->
					<button type="button" onclick="location.href='?step=1'">
						<img src="admin/gfx/icons/arrow_refresh.png"/> Reload
					</button>
				</div>
			</div>
		</div>
	</body>
</html>
