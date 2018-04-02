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
	<div class="TextBlock">
		<?if ( $this->verified ) { ?>
			<h2 class="Success"><span><?= i18n ( "Success" ) ?>!</span></h2>
			<div class="Notice">
				<p>
					<?= i18n ( "You are now registered! Please" ) ?>
					<?= i18n ( "log in with your username" ) ?>
					<?= i18n ( "and password in the login form" ) ?>.
				</p>
				<button type="button" onclick="document.location='<?= $this->content->getUrl ( ) ?>'">
					<?= i18n ( "Will do" ) ?>!
				</button>
			</div>
		<?}?>
		<?if ( !$this->verified && !$this->double ) { ?>
			<h2 class="AlreadyExists"><span><?= i18n ( "User already exists" ) ?></span></h2>
			<div class="Notice">
				<p>
					<?= i18n ( "The user you are trying to register" ) ?>
					<?= i18n ( "has already been registered by" ) ?>
					<?= i18n ( "somebody else" ) ?>.
				</p>
				<form method="post">
					<input type="hidden" name="function" value="register" />
					<input type="hidden" name="ue" value="login" />
					<button type="submit">
						<?= i18n ( "Try again with another username" ) ?>
					</button>
				</form>
			</div>
		<?}?>
		<?if ( !$this->verified && $this->double ) { ?>
			<h2 class="DoublePost"><span><?= i18n ( "Double post" ) ?></span></h2>
			<div class="Notice">
				<p>
					<?= i18n ( "You have already been registered" ) ?>
					<?= i18n ( "and have resubmitted a completed" ) ?>
					<?= i18n ( "registration form." ) ?>.
				</p>
				<button type="button" onclick="document.location='<?= $this->content->getUrl ( ) ?>'">
					<?= i18n ( "Ok, I understand" ) ?>
				</button>
			</div>
		<?}?>
	</div>
