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

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
Rune Nilssen
*******************************************************************************/

global $page, $document;

i18nAddLocalePath ( 'lib/skeleton/modules/mod_contact/locale/' );
i18n ( 'i18n_forgot_to_fill_in_field' );
$settings = CreateObjectFromString ( $field->DataMixed );

if ( !function_exists ( 'parseContactFormType' ) )
{
	function parseContactFormType ( $k, $settings )
	{
		if ( substr ( $k, 0, 6 ) == 'Value_' || substr ( $k, 0, 5 ) == 'Type_' )
			return '';
		if ( !trim ( $settings->$k ) )
			return '';
		if ( $settings->{'Required_'.$k} )
			$re = '<span class="Required">*</span>';
		else $re = '';
		$str = '';
		if ( isset ( $settings->{'Type_'.$k} ) )
		{
			switch ( $settings->{'Type_'.$k} )
			{
				case 'text':
					$str .= '<tr><td class="' . texttourl ( $k ) . '">' . i18n ( $k ) . ':' . $re . '</td>';
					$str .= '<td class="' . texttourl ( $k ) . '"><textarea name="' . texttourl(i18n($k)) . '" cols="50" rows="10">' . $settings->{'Value_'.$k} . '</textarea></td></tr>';
					break;
				case 'select':
					$options = '';
					if ( $opts = explode ( ',', $settings->{'Value_'.$k} ) )
					{
						foreach ( $opts as $opt )
						{
							$options .= '<option value="' . trim ( $opt ) . '">' . trim ( $opt ) . '</option>';
						}
					}
					else $options = '<option value="0">...</option>';
					$str .= '<tr><td class="' . texttourl ( $k ) . '">' . i18n ( $k ) . ':' . $re . '</td>';
					$str .= '<td class="' . texttourl ( $k ) . '"><select name="' . texttourl(i18n($k)) . '">' . $options . '</select></td></tr>';
					break;
				case 'varchar':
				default:
					$str .= '<tr><td class="' . texttourl ( $k ) . '">' . i18n ( $k ) . ':' . $re . '</td>';
					$str .= '<td class="' . texttourl ( $k ) . '"><input type="text" name="' . texttourl(i18n($k)) . '" value="' . trim ( $settings->{'Value_'.$k} ) . '" size="50"/></td></tr>';
					break;
			}
		}
		else
		{
			switch ( $k )
			{
				case 'LeadinMessage':
				case 'SendMessage':
				case 'Receivers':
				case 'undefined':
					return '';
				case 'Message':
					$str .= '<tr><td class="' . texttourl ( $k ) . '">' . i18n ( $k ) . ':' . $re . '</td>';
					$str .= '<td class="' . texttourl ( $k ) . '"><textarea name="' . texttourl(i18n($k)) . '" cols="50" rows="10"></textarea></td></tr>';
					break;
				default:
					$str .= '<tr><td class="' . texttourl ( $k ) . '">' . i18n ( $k ) . ':' . $re . '</td>';
					$str .= '<td class="' . texttourl ( $k ) . '"><input type="text" name="' . texttourl(i18n($k)) . '" value="" size="50"/></td></tr>';
					break;
			}
		}
		return $str;
	}
}

if ( $_POST[ 'action' ] && $_SESSION[ 'last_contact_mailkey' ] != $_POST[ 'action' ] )
{
	if ( trim ( $_POST[ 'spamcontrol' ] ) != $_SESSION[ 'spam_answer' ] )
	{
		$module .= '<h1>' . i18n('Spam') . '</h1><p>'.i18n('Your message was identified as spam').'.</p><p><a href="' . $page->getUrl () . '">' . i18n ( 'Back' ) . '</a></p>';
	} 
	else if ( $settings->Receivers )
	{
		$str = '';
		foreach ( $_POST as $k=>$v )
		{
			if ( $k == 'action' )continue;
			$str .= "$k: $v\n";
		}
		$str .= "IP Adresse/Host: " . $_SERVER[ 'REMOTE_ADDR' ] . '/' . $_SERVER[ 'REMOTE_HOST' ];
		if ( !@mail_ ( $settings->Receivers, utf8_decode ( i18n ( 'Contact form' ) ), utf8_decode ( $str ), 'Content-type: text/plain; charset=iso-8859-1' ) )
		{
			mail ( $settings->Receivers, utf8_decode ( i18n ( 'Contact form' ) ), utf8_decode ( $str ), 'Content-type: text/plain; charset=iso-8859-1' );
		}
		$_SESSION[ 'last_contact_mailkey' ] = $_POST[ 'action' ];
		$module .= $settings->SendMessage;
	}
	else
	{
		$module .= '<p>System error. No e-mail receiver set up. Please contact support.</p>';
	}
}
else if ( $_POST[ 'action' ] )
{
	$module .= '<h2>' . i18n ( 'Already posted' ) . '</h2>';
	$module .= '<p>' . i18n ( 'Your message was already posted' ) . '</p>';
}
else
{
	if ( trim ( $settings->SortOrder ) )
	{
		$order = explode ( ':', $settings->SortOrder );
		foreach ( $order as $k )
		{
			if ( substr ( $k, 0, 6 ) == 'Value_' || substr ( $k, 0, 5 ) == 'Type_' || substr ( $k, 0, 9 ) == 'Required_' )
				continue;
			if ( !$settings->$k )
				continue;	
			$str .= parseContactFormType ( $k, $settings );
		}
	}
	else
	{
		foreach ( $settings as $k=>$v )
		{
			if ( substr ( $k, 0, 6 ) == 'Value_' || substr ( $k, 0, 5 ) == 'Type_' || substr ( $k, 0, 9 ) == 'Required_' )
				continue;
			$str .= parseContactFormType ( $k, $settings );
		}
	}
	$str .= '</table>';
	
	include_once ( 'lib/skeleton/modules/mod_contact/include/include.php' );
	list ( $q, $a ) = generateSpamControl ();
	$_SESSION[ 'spam_answer' ] = $a;
	
	$str .= '<table><tr><td class="spam_control">' .i18n ( 'Spam control' ) . ' - ' . $q . '</td>';
	$str .= '<td class="spam_control"><input type="text" name="spamcontrol" value=""/></td></tr>';
	
	$module .= $settings->LeadinMessage;
	$module .= '<form method="post" id="' . $field->Name . '_id" name="' . $field->Name . '" action="' . $page->getUrl () . '">';
	$module .= '<input type="hidden" name="action" value="mail' . ( microtime() . rand(0,99999) ) . '"/>';
	$module .= '<table>' . $str . '</table>';
	$module .= '<p class="submit"><button type="button" onclick="checkContactField(ge(\'' . $field->Name . '_id\'))">' . i18n ( 'Send form' ) . '</button></p>';
	$module .= '</form>';
	
	$document->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
	$document->addResource ( 'javascript', 'lib/javascript/bajax.js' );
	$document->addResource ( 'javascript', 'lib/skeleton/modules/mod_contact/javascript/web.js' );
}

?>
