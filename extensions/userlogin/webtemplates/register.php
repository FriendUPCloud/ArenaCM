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
		<form method="post" action="<?= $this->content->getUrl ( ) ?>" id="register_form" class="TextBlock">
		
			<input type="hidden" name="ue" value="login" />
			<input type="hidden" name="function" value="verify" />
			<input type="hidden" name="Controlnumber" />
			<input type="hidden" name="Control" value="<?= md5 ( microtime ( ) . rand ( 0, 999 ) ) ?>" />
		
			<h2 class="Register">
				<span><?= i18n ( 'Register new account' ) ?></span>
			</h2>
			
			<table>
				<tr>
					<td>
						<?= i18n ( 'Real name' ) ?>:
					</td>
					<td>
						<input type="text" name="Name" size="30" />
					</td>
				</tr>
				<?if ( !GetSettingValue ( 'Login_Extension', 'emailasusername' ) ) { ?> 
				<tr>
					<td>
						<?= i18n ( 'Username' ) ?>:
					</td>
					<td>
						<input type="text" name="Username" size="20" />
					</td>
				</tr>
				<?}?>
				<?if ( $this->hasNickname && GetSettingValue ( 'Login_Extension', 'usenickname' ) ) { ?> 
				<tr>
					<td>
						<?= i18n ( 'Nickname' ) ?>:
					</td>
					<td>
						<input type="text" name="Nickname" size="20" />
					</td>
				</tr>
				<?}?>
				<?if ( GetSettingValue ( 'Login_Extension', 'emailasusername' ) ) { ?> 
				<tr>
					<td>
						<?= i18n ( 'E-mail address' ) ?> (<?= strtolower ( i18n ( 'Username' ) ) ?>):
					</td>
					<td>
						<input type="text" name="Email" size="40" />
					</td>
				</tr>
				<?}?>
				<tr>
					<td>
						<?= i18n ( 'Password' ) ?>:
					</td>
					<td>
						<input type="password" name="Password" size="20" />
					</td>
				</tr>
				<tr>
					<td>
						<?= i18n ( 'Verify password' ) ?>:
					</td>
					<td>
						<input type="password" name="Passwordverify" size="20" />
					</td>
				</tr>
				<?if ( !GetSettingValue ( 'Login_Extension', 'emailasusername' ) ) { ?> 
				<tr>
					<td>
						<?= i18n ( 'E-mail address' ) ?>:
					</td>
					<td>
						<input type="text" name="Email" size="40" />
					</td>
				</tr>
				<?}?>
				
				<?if ( GetSettingValue ( 'Login_Extension', 'needsaddress' ) ) { ?>
				
				<tr>
					<td>
						<?= i18n ( 'Address' ) ?>:
					</td>
					<td>
						<input type="text" name="Address" size="30"/>
					</td>
				</tr>
				<tr>
					<td>
						<?= i18n ( 'Zipcode' ) ?>/<?= i18n ( 'City' ) ?>:
					</td>
					<td>
						<input type="text" name="Postcode" size="4"/> <input type="text" name="City" size="30"/>
					</td>
				</tr>
				
				<?}?>
				<?if ( GetSettingValue ( 'Login_Extension', 'needsaddress' ) && !GetSettingValue ( 'Login_Extension', 'hidecountry' ) ) { ?> 
				<tr>
					<td>
						<?= i18n ( 'Country' ) ?>:
					</td>
					<td>
						<?
							if ( $countries = GetCountries ( ) )
							{
								foreach ( $countries as $country )
								{
									$ostr .= '<option value="' . $country . '">' . i18n ( $country ) . '</option>';
								}
								return '<select name="Country">' . $ostr . '</select>';
							}
							else
								return 'Critical error!';
						?>
					</td>
				</tr>
				<?}?>
				
			</table>
			<p>
				(<small><?= i18n ( 'All fields need to be filled out.') ?></small>)
			</p>
			<button type="button" onclick="verify_register_form ( )">
				<?= i18n ( 'Click to register' ) ?> &raquo;
			</button>
		</form>
		
		
		<?
			$GLOBALS[ 'document' ]->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
			i18n ( 'You need to fill in your name' ); 
			i18n ( 'You need to fill in a username' );
			i18n ( 'You need to fill in a password' );
			i18n ( 'The password could not be confirmed' );
			i18n ( 'You need to fill in an e-mail address' );
			i18n ( 'You need to fill in your address' );
			i18n ( 'You need to fill in your zip code' ); 
			i18n ( 'You need to fill in your city' );
			i18n ( 'Please choose your country' );
		?>

