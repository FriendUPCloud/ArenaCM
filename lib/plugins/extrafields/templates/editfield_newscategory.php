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
	<div class="ToggleBox">
		<h2 class="BlockHead">
			<img src="admin/gfx/icons/newspaper.png" /> <?= $this->name ?>:
		</h2>

		<div class="BlockContainer">
			<div class="SubContainer">
				<table class="LayoutColumns">
					<tr>
						<td>
							<?
								if ( is_array ( $this->news ) )
								{
									include_once ( "lib/classes/time/ctime.php" );
									$time = new cTime ( );
									foreach ( $this->news as $p )
									{
										$sw = $sw == "#f8f8f8" ? "#fff" : "#f8f8f8";
										$oStr .= "<div style='display: block; padding: 4px; background: $sw'>";
										$oStr .= "<strong>{$p->Title}</strong> ";
										$oStr .= "<small>";
										$oStr .= $time->fancy ( $p->DateActual );
										$oStr .= "</small>";
										$oStr .= "</div>";
									}
								}
								else return "<p>Ingen nyheter finnes i kategorien.</p>";
								return $oStr;
							?>
						</td>
						<td style="text-align: right; width: 180px; padding-left: 16px">
							<button type="button" onclick="document.location='admin.php?module=news'" class="WebsnippetConfig">
								<img src="admin/gfx/icons/newspaper.png" /> Administrer nyheter
							</button>
							<button type="button" class="WebsnippetConfig">
								<img src="admin/gfx/icons/wrench.png" /> Feltinnstillinger
							</button>
						</td>
					</tr>
				</table>
			</div>
		</div>
	
		<div class="BlockContainer">
			<div class="SubContainer">
				<p>
					Sett opp nyhetskategori innstillingene:
				</p>
				<?
					$name = 'Nyhets kategori';
					$opts = '';
		
					$obj =& $this->data;
					
					// Some extended options
					list ( $skipDetailMode, $reverseOrder, ) = explode ( '|', $obj->DataMixed );
					
					// --
					$db =& dbObject::globalValue ( 'database' );
					$langs = new dbObject ( 'Languages' );
					$langs->addClause ( 'ORDER BY', 'IsDefault DESC, Name ASC' );
					$langs = $langs->find ( );
		
					$cats = new dbObject ( 'NewsCategory' );
					$cats->addClause ( 'ORDER BY', 'Name ASC' );
					$GLOBALS[ 'cats' ] = $cats->find ( );
					unset ( $cats );
		
					if ( $langs )
					{
						foreach ( $langs as $lang )
						{
							$opts .= "<option value='0'>{$lang->NativeName} ({$lang->Name})</option>";
							$opts .= parseCats ( 0, $lang->ID, $obj->DataInt );
						}
					}
					else
					{
						$opts .= parseCats ( 0, false, $obj->DataInt, '' );
					}
		
					if ( $obj->DataInt <= 0 ) $s = ' selected="selected"'; else $s = '';
		
					// Affected by nid?
					$opchaffhidden = 'newsaffected' . $obj->ID;
					// DataMixed: how to get value
					$opmixedvalue = "document.getElementById ( '$opchaffhidden' ).value";
					
					// Uses reverse listing order?
					$opchrevhidden = 'reverseorder' . $obj->ID;
					// Get Reverse order
					$oprevvalue = "document.getElementById ( '$opchrevhidden' ).value";
		
					// The other options =)
					$opnchlim = "actionExtraField ( 'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Small&id=" . $obj->ID . "&field=DataDouble&value=' + this.value )";
					$onchsele = "actionExtraField ( 'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Small&id=" . $obj->ID . "&field=DataInt&value=' + this.value )";
					$opnchnav = "actionExtraField ( 'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Small&id=" . $obj->ID . "&field=DataString&value=' + ( this.value == 0 ? '0' : '1' ) )";
					// Onchange on affected by nid option
					$opchaff = "document.getElementById ( '$opchaffhidden' ).value = this.checked ? '1' : '0'; ";
					$opchaff .= "actionExtraField ( 'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Small&id=" . $obj->ID . 
								"&field=DataMixed&value='+" . $opmixedvalue . "+'|'+" . $oprevvalue . " );";
					// On change on reverse order option
					$opchrev = "document.getElementById ( '$opchrevhidden' ).value = this.checked ? '1' : '0'; ";
					$opchrev .= "actionExtraField ( 'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Small&id=" . $obj->ID . 
								"&field=DataMixed&value='+" . $opmixedvalue . "+'|'+" . $oprevvalue . " );";
		
					if ( $obj->DataString == '0' ) $n1 = ' selected="selected"'; else $n1 = '';
					if ( $obj->DataString == '1' ) $n2 = ' selected="selected"'; else $n2 = '';
					$navs = "<option value='0'$n1>Nei</option><option value='1'$n2>Ja</option>"; 
		
					$options  =  'Kategori: &nbsp;';
					$options .= "<select onchange='$onchsele' style='font-size: 9px'>";
					$options .= "<option value='0'$s>Alle nyhetskategorier</option>$opts";
					$options .= "</select>";
					$options .= "&nbsp; Nyheter pr. side: <input type='text' size='3' onchange='$opnchlim' value='{$obj->DataDouble}' style='font-size: 9px; text-align: right' />";
					$options .= "&nbsp; Generer navigasjon: <select onchange='$opnchnav' style='font-size: 9px; text-align: right'>$navs</select>";
					$options .= "<br/>";
					$options .= "Gå i detaljmodus: <input onchange='$opchaff' type='checkbox'" . ( $skipDetailMode ? ' checked="checked"' : '' ) . "/>";
					$options .= 		"<input type='hidden' id='$opchaffhidden' value='" . ( $skipDetailMode ? '1' : '0' ) . "'/>";
					$options .= "&nbsp; List ut i reversert orden: <input onchange='$opchrev' type='checkbox'" . ( $reverseOrder ? ' checked="checked"' : '' ) . "/>";
					$options .= 		"<input type='hidden' id='$opchrevhidden' value='" . ( $reverseOrder ? '1' : '0' ) . "'/>";
					
					return $options;
				?>
			</div>
			<div class="SpacerSmall"></div>
			<button type="button" onclick="swapToggleVisibility ( this.parentNode, this.parentNode.sibling )"><img src="admin/gfx/icons/arrow_turn_left.png" /> Gjem innstillingene</button>
		</div>
	</div>
	
		
