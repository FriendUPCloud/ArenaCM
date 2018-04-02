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
	<div class="ForgotPassword">
		<h2 class="ForgotPassword">
			<span><?= i18n ( 'Forgot your password' ) ?>?</span>
		</h2>
	
		<p>
			<?= i18n ( 'Fill in your e-mail address and click submit' ) ?>
			<?= i18n ( 'and we will send you a new password.' ) ?>
		</p>
	
		<p>
			<strong><?= i18n ( 'Your e-mail address' ) ?>:</strong> <input type="text" id="findEmailAddy" value="" size="20"/>
		</p>
	
		<p>
			<button onclick="receivePassword ( )">
				<?= i18n ( 'Click to receive a new password' ) ?>
			</button>
			<button onclick="closeStyledDialog ( )">
				<?= i18n ( 'Cancel' ) ?>
			</button>
		</p>
	</div>


