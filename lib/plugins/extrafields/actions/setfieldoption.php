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



if ( $element = new dbObject ( "ContentData{$_REQUEST["type"]}" ) )
{
	//$debug = Array ( );
	$element->load ( $_REQUEST[ 'id' ] );
	if ( $_REQUEST[ 'field' ] )
	{
		$element->$_REQUEST[ 'field' ] = $_REQUEST[ 'value' ];
	}
	else
	{
		$cands = Array ( 'DataInt', 'DataMixed', 'DataDouble', 'DataBig', 'DataString' );
		foreach ( $element->_table->getFieldNames ( ) as $f )
		{
			if ( in_array ( $f, $cands ) && isset ( $_REQUEST[ $f ] ) )
			{
				$element->$f = $_REQUEST[ $f ];
				//$debug[] = 'Setup set ' . $f . ' with ' . $_REQUEST[ $f ];
			}
		}
	}
	$element->save ( );
	die ( 'OK' ); // . implode ( "\n", $debug ) . print_r ( $_REQUEST, 1 ) . '|' . print_r ( $element->_table->getFields ( ), 1 ) );
}
die ( "FAIL" );
?>
