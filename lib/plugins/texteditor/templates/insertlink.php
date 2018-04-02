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
<h1>
	<?= ( $_REQUEST[ 'linktype' ] == 'new' ) ? 'Lag en ny lenke' : 'Endre lenken' ?>
</h1>
	
<div id="LinkTabs">
	<div class="tab" id="tabNormalUrl">
		<img src="admin/gfx/icons/link.png" /> Ekstern url
	</div>
	<div class="tab" id="tabEmailUrl">
		<img src="admin/gfx/icons/email.png" /> E-post
	</div>
	<div class="tab" id="tabArenaUrl">
		<img src="admin/gfx/icons/star.png" /> ARENAside
	</div>
	<div class="tab" id="tabImageUrl">
		<img src="admin/gfx/icons/image.png" /> Fil/Bilde
	</div>
	<br style="clear: both" />
	<div class="page" id="pageNormalUrl">
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td width="30%" style="padding-right: 16px">
					<strong>Adresse:</strong>*
				</td>
				<td>
					<input type="text" value="" id="link__Url" style="width: 230px; box-sizing: border-box; -moz-box-sizing: border-box;" />
				</td>
			</tr>
		</table>
	</div>
	<div class="page" id="pageEmailUrl">
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td width="30%" style="padding-right: 16px">
					<strong>E-post adresse:</strong>*
				</td>
				<td>
					<input type="text" value="" id="link__Email" style="width: 230px; box-sizing: border-box; -moz-box-sizing: border-box;" />
				</td>
			</tr>
		</table>
	</div>
	<div class="page" id="pageArenaUrl">
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td width="30%" style="padding-right: 16px">
					<strong>Velg underside:</strong>
				</td>
				<td>
					<select id="link__Arena" onchange="document.getElementById ( 'link__Url' ).value = '';">
						<?
							include_once ( 'lib/classes/dbObjects/dbContent.php' );
							
							$page = new dbContent ( );
							$page = $page->getRootContent ( );
							$options .= '<option value="">Klikk for å velge side:</option>';
							$options .= '<option value="">---------------------------</option>';
							
							$options .= '<option value="' . $page->getPath ( ) . '">' . $page->MenuTitle . '</option>';
							function subPages ( $page, $r = '&nbsp;&nbsp;&nbsp;&nbsp;' )
							{
								if ( !$page->subElementsLoaded )
									$page->loadSubElements ( );
								if ( $page->subElements )
								{
									foreach ( $page->subElements as $e )
									{
										$options .= '<option value="' . $e->getPath ( ) . '">' . $r . $e->MenuTitle . '</option>';
										$options .= subPages ( $e, $r . '&nbsp;&nbsp;&nbsp;&nbsp;' );
									}
									return $options;
								}
								return '';
							}
							$options .= subPages ( $page );
							return $options;
						?>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div class="page" id="pageImageUrl">
		<table cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td width="30%" style="padding-right: 16px">
					<strong>Velg mappe:</strong>
				</td>
				<td>
					<select id="link__Folders" onchange="loadArenaFiles ( this.value )">
						<?
							include_once ( 'lib/classes/dbObjects/dbContent.php' );
							
							$fld = new dbFolder ( );
							$fld = $fld->getRootFolder ( );
							$options .= '<option value="">Klikk for å velge mappe:</option>';
							$options .= '<option value="">---------------------------</option>';
							$options .= '<option value="' . $fld->ID . '">' . $fld->Name . '</option>';
							
							function subFolders ( $fld, $r = '&nbsp;&nbsp;&nbsp;&nbsp;' )
							{
								global $Session;
								if ( !$fld->_folders )
									$fld->getFolders ( );
								if ( $fld->_folders )
								{
									foreach ( $fld->_folders as $e )
									{
										if ( !$Session->AdminUser->checkPermission ( $e, 'read', 'admin' ) ) continue;
										$options .= '<option value="' . $e->ID . '">' . $r . $e->Name . '</option>';
										$options .= subFolders ( $e, $r . '&nbsp;&nbsp;&nbsp;&nbsp;' );
									}
									return $options;
								}
								return '';
							}
							
							$options .= subFolders ( $fld );
							return $options;
						?>
					</select>
				</td>
			</tr>
		</table>
		<div id="Arena__Images"></div>
	</div>
</div>
<div class="SubContainer">
	
	<table cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td width="30%" style="padding-right: 16px">
				<strong>Lenkemål:</strong>
			</td>
			<td>
				<select id="link__Target">
					<option value="">Åpne lenken normalt</option>
					<option value="_blank">Åpne i nytt vindu</option>
				</select>
			</td>
		</tr>
		<tr>
			<td width="30%" style="padding-right: 16px">
				<strong>Tittel:</strong>
			</td>
			<td>
				<input type="text" value="" id="link__Title" style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box;">
			</td>
		</tr>
		<tr>
			<td width="30%" style="padding-right: 16px">
				<strong>Navn:</strong>
			</td>
			<td>
				<input type="text" value="" id="link__Name" style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box;">
			</td>
		</tr>
		<tr>
			<td width="30%" style="padding-right: 16px">
				<strong>Onclick hendelse:</strong>
			</td>
			<td>
				<input type="text" value="" id="link__Onclick" style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box;">
			</td>
		</tr>
		<tr>
			<td width="30%" style="padding-right: 16px">
				<strong>CSS klasse:</strong>
			</td>
			<td>
				<input type="text" value="" id="link__Class" style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box;" />
			</td>
		</tr>
	</table>
	
</div>
<div class="SpacerSmall"></div>
<button type="button" id="LinkEditDone">
	<img src="admin/gfx/icons/accept.png" /> Ok
</button>
<button type="button" onclick="removeModalDialogue('link_dialogue')">
	<img src="admin/gfx/icons/cancel.png" /> Avbryt
</button>
