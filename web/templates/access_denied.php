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

?><?= $this->docinfo . $this->doctype ?><html<?= $this->xmlns ?>>
	<head>
		<title>
			<?= i18n ( 'Please log in' ) ?>
		</title>
	</head>
	<body class="Login">
		<div class="Login">
			<?if ( $_POST[ "webUsername" ] ) { ?>
			<h1 class="Login">
				<?= i18n ( 'The username or password was wrong' ) ?>
			</h1>
			<p class="Please">
				<?= i18n ( 'Please try to log in anew.' ) ?>
			</p>
			<?}?>
			<?if ( !$_POST[ "webUsername" ] ) { ?>
			<h1 class="Login">
				<?= i18n ( 'Please log in' ) ?>
			</h1>
			<p class="Authentication">
				<?= i18n ( 'The page you are trying to reach requires user authentication.' ) ?>
			</p>
			<?}?>
			<form method="post">
				<p class="Username">
					<strong><?= i18n ( 'Username' ) ?>:</strong>
					<input type="text" name="webUsername" />
				</p>
				<p class="Password">
					<strong><?= i18n ( 'Password' ) ?>:</strong>
					<input type="password" name="webPassword" />
				</p>
				<p class="Submit">
					<button type="submit">
						<span><?= i18n ( 'Login' ) ?></span>
					</button>
				</p>
			</form>
		
			<?if ( $_REQUEST[ "logout" ] ) { ?>
			<p class="Back">
				<a href="<?= BASE_URL ?>">&laquo; <?= i18n ( 'Go back' ) ?></a>
			</p>
			<?}?>
			<?if ( !$_REQUEST[ "logout" ] ) { ?>
			<?= $this->page->Parent > 0 ? ( '<p class="Parent"><a href="' . $this->parentPage->getUrl ( ) . '">' . i18n ( 'Go to parent page', $GLOBALS[ 'Session' ]->LanguageCode ) . '</a></p>' ) : '' ?>
			<?}?>
		</div>
	</body>
</html>
