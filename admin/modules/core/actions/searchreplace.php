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


$search = trim ( $_REQUEST[ 'searchfor' ] );
$replace = trim ( $_REQUEST[ 'replacewith' ] );

if ( $search )
{
	// Search and replace all ContentElement ------------------------------------- /
	$obj = new dbObject ( 'ContentElement' );
	if ( $objs = $obj->find () )
	{
		foreach ( $objs as $obj )
		{
			$obj->Intro = str_replace ( $search, $replace, $obj->Intro );
			$obj->Body = str_replace ( $search, $replace, $obj->Body );
			$obj->save ();
		}
	}

	// Search and replace all ContentDataSmall ----------------------------------- /
	$obj = new dbObject ( 'ContentDataSmall' );
	if ( $objs = $obj->find () )
	{
		foreach ( $objs as $obj )
		{
			$obj->DataMixed = str_replace ( $search, $replace, $obj->DataMixed );
			$obj->save ();
		}
	}

	// Search and replace all ContentDataBig ------------------------------------- /
	$obj = new dbObject ( 'ContentDataBig' );
	if ( $objs = $obj->find () )
	{
		foreach ( $objs as $obj )
		{
			$obj->DataText = str_replace ( $search, $replace, $obj->DataText );
			$obj->save ();
		}
	}
}

header ( 'Location: admin.php?module=core' );
die ( );

?>
