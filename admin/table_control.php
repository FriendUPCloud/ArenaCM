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



$db =& dbObject::globalValue ( 'database' );

// check for columns which might not exist in older versions
$tableTest = new cDatabaseTable ( 'ContentDataSmall' );
if ( $tableTest->load ( ) )
{
	$isvisible = false;
	foreach ( $tableTest->getFieldNames ( ) as $n )
	{
		if ( $n == 'IsVisible' )
			$isvisible = true;
	}
	if ( !$isvisible )
		$db->query ( 'ALTER TABLE ContentDataSmall ADD ( IsVisible tinyint(4) default 1 )' );
}
$tableTest = new cDatabaseTable ( 'ContentDataBig' );
if ( $tableTest->load ( ) )
{
	$isvisible = false;
	foreach ( $tableTest->getFieldNames ( ) as $n )
	{
		if ( $n == 'IsVisible' )
			$isvisible = true;
	}
	if ( !$isvisible )
		$db->query ( 'ALTER TABLE ContentDataBig ADD ( IsVisible tinyint(4) default 1 )' );
}
?>
