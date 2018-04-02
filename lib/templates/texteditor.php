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

?><!doctype html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>
		</title>
		<link rel="stylesheet" href="lib/templates/texteditor.css"/>
		<?= stripslashes ( $_REQUEST['extra'] ) ?>
		<meta http-equiv="content-type" content="text/xhtml; charset=UTF-8"/>
	</head>
	<body onload="if ( navigator.userAgent.indexOf ( 'MSIE' ) >= 0 ) document.body.contentEditable='true';" onmouseup="if ( navigator.userAgent.indexOf ( 'MSIE' ) >= 0 ) return false" onselectstart="if ( navigator.userAgent.indexOf ( 'MSIE' ) >= 0 ) return false"  onclick="if ( navigator.userAgent.indexOf ( 'MSIE' ) >= 0 ) return false" oncontextmenu="return false" class="ArenaEditor">
		Laster inn...
	</body>
</html>
