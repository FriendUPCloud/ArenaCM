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

?><?= $this->docinfo . $this->doctype ?><html<?= $this->xmlns ?>>
	<head>
		<title>
			<?= ( ( isset ( $this->sTitle ) ) ? $this->sTitle : ( defined ( 'SITE_TITLE' ) ? SITE_TITLE : SITE_ID ) ) . ' - ' . $this->page->Title . $this->page->ExtraTitle ?>
		</title>
		<?
			if ( strstr ( $this->userAgent, 'ipad' ) ) { 
				echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
			}
		?>
	</head><?
		$agent = strtolower ( $_SERVER[ 'HTTP_USER_AGENT' ] );
		if ( strstr ( $agent, 'webkit' ) )
			$this->userAgent = 'webkit';
		else if ( strstr ( $agent, 'gecko' ) )
			$this->userAgent = 'gecko';
		else $this->userAgent = 'msie';
		if ( strstr ( $agent, 'windows' ) )
			$this->userAgent .= ' windows';
		else if ( strstr ( $agent, 'linux' ) )
			$this->userAgent .= ' linux';
		else if ( strstr ( $agent, 'mac' ) )
			$this->userAgent .= ' mac';
		else $this->userAgent .= ' otheros';
		if ( strstr ( $agent, 'ipad' ) )
			$this->userAgent .= ' ipad';
		else if ( strstr ( $agent, 'android' ) )
			$this->userAgent .= ' android';
		if ( strstr ( $agent, 'msie 7' ) ) $this->userAgent .= ' msie7';
		else if ( strstr ( $agent, 'msie 8' ) ) $this->userAgent .= ' msie8';
		else if ( strstr ( $agent, 'msie 9' ) ) $this->userAgent .= ' msie9';
		if ( $GLOBALS[ 'bodyclass' ] )
			$this->userAgent .= ' ' . $GLOBALS[ 'bodyclass' ];
	?>
	<body class="<?= $this->userAgent; ?> <?= $this->LanguageCode . ' ' . $this->page->RouteName ?>">
		<? $this->__Content =  executeWebModule ( $this->page, $_REQUEST[ 'ue' ] ? 'extensions' : false ); ?>
		<?= $this->__TopContent ?>
		<div id="Empty__"></div>
		<div id="CenterBox__" class="<?= is_numeric ( $this->page->RouteName{0} ) ? ( 'a' . $this->page->RouteName ) : $this->page->RouteName ?>">
			<div id="Center__" style="">
				<div id="Content__">
					<?if ( !defined ( 'TOPMENU_CONTENTGROUP' ) || !TOPMENU_CONTENTGROUP ) { ?>
					<div id="TopMenu__" class="<?= texttourl( trim ( strip_tags ( $this->page->MenuTitle ) ) ) ?>"><?= $this->renderNavigation ( defined( 'NAVIGATION_ROOTPARENT' ) ? NAVIGATION_ROOTPARENT :  0, defined( 'NAVIGATION_LEVELS' ) ? NAVIGATION_LEVELS : 1 , defined( 'NAVIGATION_MODE' ) ? NAVIGATION_MODE : 'FOLLOW', true ); ?></div>
					<?}?>
					<div id="InnerContainer__">
						<div id="InnerContent__">
							<?= $this->__Content ?>
							<?= ( defined( 'NAVIGATION_SHOWBREADCRUMBS' ) && NAVIGATION_SHOWBREADCRUMBS ) ? '<div id="BreadCrumbs__">' . $this->renderBreadCrumbs() . '</div>' : ''; ?>
						</div>
					</div>
				</div>
				<div id="Footer__"><div id="InnerFooter__"><?= $this->page->footerline != '' ? $this->page->footerline : ''; ?></div></div>
			</div>
		</div>
	</body>
</html>
