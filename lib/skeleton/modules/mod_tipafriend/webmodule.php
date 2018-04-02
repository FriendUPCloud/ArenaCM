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

$GLOBALS[ 'document' ]->addResource ( 'javascript', 'lib/javascript/bajax.js' );
$GLOBALS[ 'document' ]->addResource ( 'javascript', 'lib/javascript/arena-lib.js' );
$GLOBALS[ 'document' ]->addResource ( 'javascript', 'skeleton/modules/mod_tipafriend/javascript/web_main.js' );

if ( $_REQUEST[ 'tips_dialog' ] )
{
	ob_clean ( );
	$mtpl = new cPTemplate ( 'skeleton/modules/mod_tipafriend/templates/web_tipform.php' );
	$mtpl->content =& $content;
	die ( $mtpl->render ( ) );
}
else if ( $_REQUEST[ 'mod_tipafriend' ] ) 
{
	mail_ ( $_REQUEST[ 'email' ], 'Tips fra nettsiden ' . SITE_TITLE, "
Hei!

{$_REQUEST['name']} ønsket å tipse deg om en nettside han syntes kunne være interessant for deg. Dette er hans beskjed:

{$_REQUEST['message']}

For å besøke nettadressen, gå hit:

" . $content->getUrl ( ) . " (" . $content->MenuTitle . ")

mvh,
" . SITE_TITLE . "
", 'Content-type: text/plain; charset=utf-8' );
	die ( 'ok' );
}

$module .= '
	<p class="Info">
		' . $fieldObject->DataMixed . '
	</p>
	<p class="Button">
		<button type="button" onclick="styledDialog ( \'' . $content->getUrl ( ) . '?tips_dialog=true\' )">
			Tips en venn
		</button>
	</p>
';
?>
