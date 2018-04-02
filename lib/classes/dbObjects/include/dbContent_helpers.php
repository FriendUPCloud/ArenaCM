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



/**
 * Helper functions to dbContent, loaded on demand 
**/
function _mailFormTo ( $emails, $subject, $prefix, $content )
{
	$sent = false;
	if ( $emails = explode ( ',', $emails ) )
	{
		foreach ( $_POST as $k => $v )
		{
			if ( !trim ( $k ) ) continue;
			if ( !$prefix || strpos( $k, $prefix ) == 0 )
			{
				$key = str_replace ( $prefix, '', $k );
				$ostr .= "$key: $v\n";
			}
		}
		if ( $ostr )
		{
			foreach ( $emails as $email )
			{
				if ( trim ( $email ) )
				{
					if ( mail_ ( $email, utf8_decode ( $subject ), utf8_decode ( $ostr ), 'Content-type: text/plain; charset=ISO-8859-1' . "\n" . 'From: ' . WEBMASTER_EMAIL, false ) )
					{
						$sent = true;
					}
				}
			}
		}
	} 
	header ( 'Location: ' . $content->getUrl ( ) . '?formsent' . $prefix . '=true' );
	die ( );
}
?>
