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

?><<? ?>?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>
			<?= $this->page->Title . $this->page->ExtraTitle ?>
		</title>
	</head>
	<body>
		<div id="Empty__"></div>
			<div id="CenterBox__">
			<div id="Center__">
				<div id="Content__">
					<div id="TopMenu__"><?= $this->renderNavigation ( defined( 'NAVIGATION_ROOTPARENT' ) ? NAVIGATION_ROOTPARENT :  0, defined( 'NAVIGATION_LEVELS' ) ? NAVIGATION_LEVELS : 1 , defined( 'NAVIGATION_MODE' ) ? NAVIGATION_MODE : 'FOLLOW', true ); ?></div>
					<div id="InnerContent__">
						<div id="NotPublished">
							<h1>
								<?= i18n ( 'Not published', $GLOBALS[ 'Session' ]->LanguageCode ) ?>!
							</h1>
							<p>
								<?= i18n ( 'This page is marked as "not published"', $GLOBALS[ 'Session' ]->LanguageCode ) ?>.
							</p>
							<?= $this->page->Parent > 0 ? ( '<p><a href="' . $this->parentPage->getUrl ( ) . '">' . i18n ( 'Go to parent page', $GLOBALS[ 'Session' ]->LanguageCode ) . '</a></p>' ) : '' ?>
						</div>
					</div>
				</div>
				<div id="Footer__"><?= $this->page->footerline != '' ? '<div id="InnerFooter__">'.$this->page->footerline.'</div>' : ''; ?></div>
			</div>
		</div>
	</body>
</html>
