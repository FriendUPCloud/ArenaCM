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

	<div class="Container" style="padding: <?= MarginSize ?>px">
		<table class="LayoutColumns">
			<tr>
				<td>
					<label>Tips heading:</label>
				</td>
				<td>
					<input type="text" id="mod_blogtip_heading" value="<?= $this->data->Heading ?>">
				</td>
			</tr>
			<tr><td><div class="SpacerSmall"></div></td></tr>
			<tr>
				<td>
					<label>Tips info:</label>
				</td>
				<td>
					<input type="text" id="mod_blogtip_info" value="<?= $this->data->Info ?>">
				</td>
			</tr>
			<tr><td><div class="SpacerSmall"></div></td></tr>
			<tr>
				<td>
					<label>Button text:</label>
				</td>
				<td>
					<input type="text" id="mod_blogtip_buttontext" value="<?= $this->data->ButtonText ?>">
				</td>
			</tr>
			<tr>
				<td>
					<label>Side for skjema:</label>
				</td>
				<td>
					<select id="mod_blogtip_contentelementid">
						<?= getSiteStructureOptions ( $this->data->ContentElementID ) ?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label>Innholdsgruppe:</label>
				</td>
				<td>
					<select id="mod_blogtip_contentgroup">
						<?
							$db =& dbObject::globalValue ( 'database' );
							if ( $rows = $db->fetchObjectRows ( '
								SELECT DISTINCT(ContentGroups) AS Uni FROM ContentElement WHERE MainID=ID
							' ) )
							{
								$groups = array ( );
								foreach ( $rows as $row )
								{
									if ( $gr = explode ( ',', $row->Uni ) )
									{
										foreach ( $gr as $g )
										{
											if ( !in_array ( trim ( $g ), $groups ) )
												$groups[] = trim ( $g );
										}
									}
								}
								foreach ( $groups as $g )
								{
									if ( $g == $this->data->ContentGroup )
										$s = ' selected="selected"';
									else $s = '';
									$str .= '<option value="' . $g . '"' . $s . '>' . $g . '</option>';
								}
								return $str;
							}
						?>
					</select>
				</td>
			</tr>
		</table>
	</div>
	<div class="SpacerSmall"></div>
	
	<script type="text/javascript">
		AddSaveFunction ( function ( ) {
			var j = new bajax ( );
			j.openUrl ( ACTION_URL + 'mod=mod_blog_tip&modaction=savecfg', 'post', true );
			j.addVar ( 'configHeading', document.getElementById ( 'mod_blogtip_heading' ).value );
			j.addVar ( 'configInfo', document.getElementById ( 'mod_blogtip_info' ).value );
			j.addVar ( 'configButtonText', document.getElementById ( 'mod_blogtip_buttontext' ).value );
			j.addVar ( 'configContentElementID', document.getElementById ( 'mod_blogtip_contentelementid' ).value );
			j.addVar ( 'configContentGroup', document.getElementById ( 'mod_blogtip_contentgroup' ).value );
			j.onload = function ( ) { };
			j.send ();
		} );
	</script>
	
