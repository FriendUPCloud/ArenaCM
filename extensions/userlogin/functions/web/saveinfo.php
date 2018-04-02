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

$GLOBALS[ 'webuser' ]->Email = $_REQUEST[ 'Email' ];
$GLOBALS[ 'webuser' ]->loadExtraFields ( );
if ( $_REQUEST[ 'Password' ] != '********' )
	$GLOBALS[ 'webuser' ]->Password = $GLOBALS[ 'webuser' ]->hash ( $_REQUEST[ 'Password' ] );
$GLOBALS[ 'webuser' ]->Name = $_REQUEST[ 'Name' ];
$GLOBALS[ 'webuser' ]->Address = $_REQUEST[ 'Address' ];
$GLOBALS[ 'webuser' ]->Postcode = $_REQUEST[ 'Postcode' ];
$GLOBALS[ 'webuser' ]->City = $_REQUEST[ 'City' ];
$GLOBALS[ 'webuser' ]->Country = $_REQUEST[ 'Country' ];
$GLOBALS[ 'webuser' ]->Telephone = $_REQUEST[ 'Telephone' ];
$GLOBALS[ 'webuser' ]->Nickname = $_REQUEST[ 'Nickname' ];
$GLOBALS[ 'webuser' ]->save ( );
$GLOBALS[ 'webuser' ]->saveExtraFields ( );
if ( $_REQUEST[ 'Password' ] != '********' )
	$GLOBALS[ 'webuser' ]->reauthenticate ( $GLOBALS[ 'webuser' ]->Username, $_REQUEST[ 'Password' ] );
$GLOBALS[ 'webuser' ]->sendUpdateMail ( );
if ( $_REQUEST[ 'gohome' ] )
	header ( 'Location:' . $GLOBALS[ 'page' ]->getUrl ( ) );
else header ( 'Location: ' . $GLOBALS[ 'page' ]->getUrl ( ) . '?ue=userlogin&function=editprofile' );
die ( );
?>
