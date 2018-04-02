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

?><html>
	<head>
		<title>
			<?= defined ( 'SITE_TITLE' ) ? SITE_TITLE : SITE_ID ?> har mottatt din bestilling
		</title>
		<?if ( file_exists ( BASE_DIR . '/css/email.css' ) ) { ?>
		<style type="text/css">
			<?= file_get_contents ( BASE_DIR . '/css/email.css' ) ?>
		</style>
		<?}?>
		<?if ( !file_exists ( BASE_DIR . '/css/email.css' ) ) { ?>
		<style type="text/css">
			body
			{
				background: #fff;
				color: #000;
				font-family: arial, helvetica;
				font-size: 11px;
			}
		</style>
		<?}?>
	</head>
	<body>
		<div id="Content">
			<div id="Header">
				<?
					if ( file_exists ( BASE_DIR . '/templates/mail_header.php' ) )
					{
						$tpl = new cPTemplate ( BASE_DIR . '/templates/mail_header.php' );
						return $tpl->render ( );
					}
				?>
			</div>
			<div id="Body">
				<?= $this->data ?>
			</div>
			<div id="Footer">
				<?
					if ( file_exists ( BASE_DIR . '/templates/mail_footer.php' ) )
					{
						$tpl = new cPTemplate ( BASE_DIR . '/templates/mail_footer.php' );
						return $tpl->render ( );
					}
				?>
			</div>
		</div>
	</body>
</html>

