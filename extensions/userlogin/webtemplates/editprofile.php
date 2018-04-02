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

	<div class="EditProfile">
	
		<h1 class="YourProfile">
			<span><?= i18n ( 'Your profile' ) ?></span>
		</h1>
		
		<hr/>
		
		<form method="post" action="<?= $this->content->getUrl ( ) ?>?ue=userlogin&function=saveinfo<?= ( $_REQUEST[ 'die' ] ? '&gohome=true' : '' ) ?>" name="infoform">
		
			<table>
				<tr>
					<td>
						<?= i18n ( 'Full name' ) ?>:
					</td>
					<td>
						<input type="text" name="Name" value="<?= $GLOBALS[ 'webuser' ]->Name ?>" size="35"/>
					</td>
				</tr>
				<tr>
					<td>
						<?= i18n ( 'E-mail address' ) ?>:
					</td>
					<td>
						<input type="text" name="Email" value="<?= $GLOBALS[ 'webuser' ]->Email ?>" size="35"/>
					</td>
				</tr>
				<tr>
					<td>
						<?= i18n ( 'Username' ) ?>:
					</td>
					<td>
						<strong><?= $GLOBALS[ 'webuser' ]->Username ?></strong>
					</td>
				</tr>
				<?if ( GetSettingValue ( 'Login_Extension', 'usenickname' ) ) { ?> 
				<tr>
					<td>
						<?= i18n ( 'Nickname' ) ?>:
					</td>
					<td>
						<input type="text" name="Nickname" size="20" value="<?= $GLOBALS[ 'webuser' ]->Nickname ?>" />
					</td>
				</tr>
				<?}?>
				<tr>
					<td>
						<?= i18n ( 'Telephone' ) ?>:
					</td>
					<td>
						<input type="text" name="Telephone" value="<?= $GLOBALS[ 'webuser' ]->Telephone ?>" size="15"/>
					</td>
				</tr>
				<tr>
					<td>
						<?= i18n ( 'Address' ) ?>:
					</td>
					<td>
						<input type="text" name="Address" value="<?= $GLOBALS[ 'webuser' ]->Address ?>" size="35"/>
					</td>
				</tr>
				<tr>
					<td>
						<?= i18n ( 'Postcode' ) ?>/<?= i18n ( 'City' ) ?>:
					</td>
					<td>
						<input type="text" name="Postcode" value="<?= $GLOBALS[ 'webuser' ]->Postcode ?>" size="5"/>
						<input type="text" name="City" value="<?= $GLOBALS[ 'webuser' ]->City ?>" size="24"/>
					</td>
				</tr>
				<?if ( !GetSettingValue ( 'Login_Extension', 'hidecountry' ) ) { ?>
				<tr>
					<td>
						<?= i18n ( 'Country' ) ?>:
					</td>
					<td>
						<input type="text" name="Country" value="<?= $GLOBALS[ 'webuser' ]->Country ?>" size="30"/>
					</td>
				</tr>
				<?}?>
				<tr>
					<td colspan="2">
						<h2><?= i18n ( 'Alter your password' ) ?></h2>
					</td>
				</tr>
				<tr>
					<td>
						<?= i18n ( 'Password' ) ?>:
					</td>
					<td>
						<input type="password" name="Password" value="********" size="20"/>
					</td>
				</tr>
				<tr>
					<td>
						<?= i18n ( 'Confirm password' ) ?>:
					</td>
					<td>
						<input type="password" name="Password_Confirm" value="********" size="20"/>
					</td>
				</tr>
			</table>
			
		</form>
		
		<hr/>
		
		<p>
			<button type="button" onclick="verifyInformationForm ( )">
				<?= i18n ( 'Save information' ) ?>
			</button>
			<button type="button" onclick="document.location='<?= getLocalizedBaseUrl ( ) ?>'">
				<?= i18n ( 'Abort' ) ?>
			</button>
		</p>
		
		
	
	</div>
