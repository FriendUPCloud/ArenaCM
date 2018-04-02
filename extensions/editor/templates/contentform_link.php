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
					
					<div class="SpacerSmall"></div>
					<table class="LayoutColumns">
						<tr>
							<td width="110px">
								<h4><?= i18n ( 'Link url' ) ?>:</h4>
							</td>
							<td>
								<input id="LinkText" type="text" value="<?= str_replace ( '"', '&quot;', stripslashes ( $this->page->Link ) ) ?>" style="-moz-box-sizing: border-box; box-sizing: border-box; width: 100%">
							</td>
						</tr>
						<tr>
							<td><h4><?= i18n ( 'Link target' ) ?>:</h4></td>
							<td>
								<select id="LinkTarget">
									<?
										$str = '';
										foreach ( array ( '_self'=>'Samme vindu', '_blank'=>'Nytt vindu' ) as $m=>$l )
										{
											$s = ( $this->linkData->LinkTarget == $m ? ' selected="selected"' : '' );
											$str .= '<option value="' . $m . '"'. $s .'>' . $l . '</option>';
										}
										return $str;
									?>
								</select>
							</td>
						</tr>
					</table>
					<div class="SpacerSmallColored"></div>
					<?= $this->extrafields ?>
