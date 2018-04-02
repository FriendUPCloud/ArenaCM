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
		
		<?if ( $this->popupdialogs = GetSettingValue ( 'Login_Extension', 'popupdialogs' ) ) { ?>
		<?}?>
		
		<?if ( $GLOBALS[ 'webuser' ]->ID && GetSettingValue ( 'Login_Extension', 'hidewelcometext' ) <= 0 ) { ?>
			<p class="Welcome">
				<strong><?= i18n ( "Welcome" ) ?>, <?= $GLOBALS[ 'webuser' ]->Name ?>!</strong>
			</p>
		<?}?>
		<?if ( $GLOBALS[ 'webuser' ]->ID && GetSettingValue ( 'Login_Extension', 'hidewelcometext' ) > 0 ) { ?>
			<p class="LoggedInAs">
				<?= i18n ( 'You are logged in as' ) ?> <strong><?= $GLOBALS[ 'webuser' ]->Name ?></strong>
			</p>
		<?}?>
		<?if ( $GLOBALS[ 'webuser' ]->ID && GetSettingValue ( 'Login_Extension', 'hidelogintime' ) <= 0 ) { ?>
			<p>
				<?= i18n ( "You have been logged in since" ) ?> <?
					if ( date ( "Y-m-d" ) == date ( "Y-m-d", strtotime ( $GLOBALS[ "webuser" ]->DateLogin ) ) )
						return i18nDate ( date ( "H:i", strtotime ( $GLOBALS[ "webuser" ]->DateLogin ) ) );
					else 
						return i18nDate ( date ( "Y-m-d H:i", strtotime ( $GLOBALS[ "webuser" ]->DateLogin ) ) );
				?>
			</p>
		<?}?>
		<?if ( $GLOBALS[ 'webuser' ]->ID ) { ?>
			<p class="LoginEdit">
				<button id="LoginEdit" class="Edit" type="button" onclick="document.location='<?= BASE_URL . $this->content->getRoute ( ) ?>?ue=userlogin&function=editprofile'">
					<span><?= i18n ( 'Edit your profile' ) ?></span>
				</button>
			</p>
			<?
				global $Session;
				if ( trim ( GetSettingValue ( 'webshop', 'productpage' . $Session->LanguageCode ) ) )
				{
					return '
			<p>
				<button id="LoginProfile" class="ShoppingLog" type="button" onclick="document.location=\\\\\'' . BASE_URL . $this->content->getRoute ( ) . '?ue=userlogin&function=shoppinglog\\\\\'">
					<span>' . i18n ( 'Show shopping log' ) . '</span>
				</button>
			</p>
					';
				}
			?>
			<p class="LogoutParagraph">
				<button class="Logout" type="button" onclick="document.location='<?= $this->content->getUrl ( ) ?>?logout=true'">
					<span><?= i18n ( 'Logout' ) ?></span>
				</button>
			</p>
		<?}?>
		<?if ( $GLOBALS[ 'webuser' ]->ID && $this->popupdialogs ) { ?>
			<script>
				var LoginEdit = document.getElementById ( 'LoginEdit' );
				if ( LoginEdit )
				{
					LoginEdit.onclick = function ( )
					{
					
						this.urlLocation = '<?= getLocalizedBaseUrl ( ) ?>?ue=userlogin&function=editprofile&die=true';
						BlestBoxLaunchElement ( this );
					}
				}
				var LoginLog = document.getElementById ( 'LoginProfile' );
				if ( LoginLog )
				{
					LoginLog.onclick = function ( )
					{
					
						this.urlLocation = '<?= getLocalizedBaseUrl ( ) ?>?ue=userlogin&function=shoppinglog&die=true';
						BlestBoxLaunchElement ( this );
					}
				}
			</script>
		<?}?>
		
		<?if ( $GLOBALS[ 'webuser' ]->IsAdmin ) { ?>
		<p class="ArenaParagraph">
			<a href="admin.php" target="_blank"><?= i18n ( 'Login to Blest ARENA' ) ?> &raquo;</a>
		</p>
		<?}?>
		
		<?if ( !$GLOBALS[ 'webuser' ]->ID ) { ?>
			<form method="post" class="LoginForm" name="LoginForm" action="<?= $this->content->getUrl ( ) ?>">
				<p class="pUser">
					<span class="sUser" id="labelUsername"><?= i18n ( 'Username' ) ?>:</span> <input type="text" name="webUsername" />
				</p>
				<p class="pPass">
					<span class="sPass" id="labelPassword"><?= i18n ( 'Password' ) ?>:</span> <input type="password" name="webPassword" />
				</p>
				<p class="pButton">
					<button class="Login" type="submit">
						<span class="sButton"><?= i18n ( 'Login' ) ?></span>
					</button>
				</p>
			</form>
			<div class="pRegisterForm">
				<?= $this->registerForm ?>
			</div>
		<?}?>
		
		<?
			$GLOBALS[ 'document' ]->addHeadScript ( BASE_URL . 'extensions/userlogin/javascript/login.js' );
		?>
		
		<? i18n ( 'Are you sure?' ); ?>
		<? i18n ( 'A new password has been sent to your e-mail address.' ); ?>
		<? i18n ( 'No user with such an e-mail address exists.' ); ?>
		
