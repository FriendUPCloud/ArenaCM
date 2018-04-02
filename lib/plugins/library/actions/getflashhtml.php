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



$file = new dbObject ( 'File' );
if ( $file->load ( $_REQUEST[ 'fid' ] ) )
{	
	die ( "
	<span arenatype=\"movie\" style=\"width: {$file->Width}px; height: {$file->Height}px; display: block; border: 2px dotted #aaa; background: #ccc url(admin/gfx/arenaicons/page_flash_64.png) no-repeat center center\" id=\"{$file->DivID}\" data=\"upload/" . ( $file->Filename . ( $file->Variables ? ( '?' . $file->Variables ) : '' ) ) . "\" width=\"{$file->Width}\" height=\"{$file->Height}\" wmode=\"transparent\" type=\"application/x-shockwave-flash\">
		<param name=\"width\" value=\"{$file->Width}\"></param>
		<param name=\"height\" value=\"{$file->Height}\"></param>
		<param name=\"movie\" value=\"upload/" . ( $file->Filename . ( $file->Variables ? ( '?' . $file->Variables ) : '' ) ) . "\"></param>
		<param name=\"wmode\" value=\"transparent\"></param>
	</span>
	" );
}
die ( 'Fant ikke flash filen.' );

?>
