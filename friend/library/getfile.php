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


$f = GetFileByPath( $_POST['path'] );

$fl = new dbFolder( isset( $f->ImageFolder ) ? $f->ImageFolder : $f->FileFolder );

$dp = $fl->DiskPath ? $fl->DiskPath : ( isset( $f->ImageFolder ) ? 'upload/images-master/' : 'upload/' );

ob_clean();

$mime_type = finfo_file( $db . $f->Filename );

$fi = new finfo( FILEINFO_MIME, '/usr/share/file/magic' );
$mime_type = $fi->buffer( $result = file_get_contents($dp . $f->Filename) );

ob_clean();
header( 'Content-type: ' . $mime_type );
die( $result );

?>
