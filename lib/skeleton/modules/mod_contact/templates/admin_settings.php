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
	
	<div id="Contact_Form">
		<div class="tab" id="tabContactMain">
			<img src="admin/gfx/icons/page_white.png"/> Skjemainformasjon
		</div>
		<div class="tab" id="tabContactFields">
			<img src="admin/gfx/icons/wrench.png"/> Avansert
		</div>
		<br style="clear: both"/>
		<div class="page" id="pageContactMain" style="padding: 4px">
			<p>
				Ingress/instruks før skjemaet:
			</p>
			<p>
				<textarea class="mceSelector" rows="8" cols="50" id="contact_LeadinMessage" style="height: 150px"><?= $this->LeadinMessage ?></textarea>
			</p>
			<br/>
			<p>
				Beskjed vist til bruker ved forsendelse:
			</p>
			<p>
				<textarea class="mceSelector" rows="8" cols="50" id="contact_SendMessage" style="height: 150px"><?= $this->SendMessage ?></textarea>
			</p>
			<br/>
			<p>
				E-post adresse(r) som skal motta skjemaet:
			</p>
			<p>
				<input type="text" id="contact_Receivers" value="<?= $this->Receivers ?>" size="50"/>
			</p>
		</div>
		<div class="page" id="pageContactFields" style="padding: 4px">
			<table cellspacing="0" cellpadding="4" border="0" width="100%">
				<tr>
					<td valign="top" width="*">
						<p>
							Kryss av hvilke felter som skal vises, og fjern med det røde ikonet:
						</p>
						<table cellspacing="0" cellpadding="4" border="0" width="100%">
							<?
								if ( isset ( $this->SortOrder ) && count ( $this->SortOrder ) && trim ( $this->SortOrder[0] ) )
								{
									$this->fields = $this->SortOrder;
								}
								else
									$this->fields = array ( 'Name', 'Telephone', 'Fax', 'Email', 'Address', 'Zipcode', 'City', 'Country', 'Message' );
							
								foreach ( 
									$this->fields as $field 
								)
								{
									if ( $i == 0 )
										$str .= '<tr>';
									$s = key_exists ( $field, $this ) && $this->$field == 1 ? ' checked="checked"' : '';
									$str .= '
								<td style="border-bottom: 1px solid #ccc; white-space: nowrap" class="FormButtons">
									<div title="' . i18n ( 'Remove' ) . '" class="Container" style="cursor: hand; cursor: pointer; margin: 3px 5px -5px 5px; padding: 0; float: left" onclick="removeContactField(\\'' . $field . '\\')">
										<img src="admin/gfx/icons/bullet_delete.png" valign="baseline"/ >
									</div>
									<div title="' . i18n ( 'Properties' ) . '" class="Container" style="cursor: hand; cursor: pointer; margin: 3px 5px -5px 0px; padding: 0; float: left" onclick="propertiesContactField(\\'' . $field . '\\')">
										<img src="admin/gfx/icons/bullet_wrench.png" valign="baseline"/ >
									</div>
									<div style="margin: 5px 0 -5px 0;">
									' . i18n ( $field ) . '
									</div>
								</td>
								<td style="border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; width: 30px">
									<input type="checkbox" id="contact_' . $field . '"' . $s . ' style="position: relative; top: 2px;"/>
								</td>
									';
									if ( $i == 1 )
										$str .= '</tr>';
									$i = ( $i + 1 ) % 2;
								}
								if ( substr ( $str, -5, 5 ) != '</tr>' )
									$str .= '</tr>';
								return $str;
							?>
						</table>
						<div class="Spacer"></div>
						<div class="SpacerSmallColored"></div>
						<div class="Spacer"></div>
						<p>
							Legg til et felt:
						</p>
						<p>
							<input type="text" id="newfield" onkeydown="this.setAttribute ( 'used', 'yes' );" value="Nytt felt"/> <button type="button" onclick="document.addContactField()"><img src="admin/gfx/icons/table_row_insert.png"/> Legg til</button>
						</p>
						<br/>
					</td>
					<td width="10">&nbsp;</td>
					<td valign="top" width="250px">
						<p>
							Sorter feltene:
						</p>
						<select id="contact_sortorder" style="width: 200px;" size="10">
							<?
								foreach ( $this->fields as $field )
								{
									$str .= '<option value="' . $field . '">' . i18n ( $field ) . '</option>';
								}
								return $str;
							?>
						</select>
						<div class="SpacerSmall"></div>
						<p>
							Klikk i listen og hold inne muspekeren mens du<br/>
							flytter musen. Da vil den valgte raden i listen<br/>
							flytte seg.
						</p>
						<p>
							<button type="button" onclick="saveContactSortOrder()">
								<img src="admin/gfx/icons/arrow_refresh.png"/> Lagre sorteringen
							</button>
							<?if ( $this->SortOrder ) { ?>
							<button type="button" onclick="document.location='admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=reset'">
								<img src="admin/gfx/icons/cancel.png"/> Nullstill listen
							</button>
							<?}?>
						</p>
					</td>
				</tr>
			</table>
		
			
		</div>
	</div>
	
	<script type="text/javascript">
		initTabSystem ( 'Contact_Form' );
		AddSaveFunction ( function ()
		{
			var j = new bajax ();
			j.openUrl ( 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=savesettings', 'post', true );
			var eles = document.getElementById ( 'Contact_Form' ).getElementsByTagName ( 'input' );
			for ( var a = 0; a < eles.length; a++ )
			{
				if ( eles[a].type == 'checkbox' )
				{
					j.addVar ( 'checkbox_' + a, eles[a].id.split('_')[1] + '<!--sep-->' + ( eles[a].checked ? '1' : '0' ) );
				}
				else j.addVar ( eles[a].id.split('_')[1], eles[a].value );
			}
			var eles = document.getElementById ( 'Contact_Form' ).getElementsByTagName ( 'textarea' );
			for ( var a = 0; a < eles.length; a++ )
			{
				j.addVar ( eles[a].id.split('_')[1], eles[a].value );
			}
			j.onload = function (){}
			j.send ();
		}
		);
		document.addContactField = function ()
		{
			if ( document.getElementById ( 'newfield' ).getAttribute ( 'used' ) )
			{
				var items = document.getElementById ( 'contact_sortorder' ).options;
				var order = '';
				for ( var a = 0; a < items.length; a++ )
				{
					order += items[a].value + ":";
				}
				order += document.getElementById ( 'newfield' ).value;
				document.location = 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=sortorder&fields=' + escape ( order );
			}
			else { alert ( 'Du må fylle inn feltet.' ); document.getElementById ( 'newfield' ).focus (); }
		}
		var cso = document.getElementById ( 'contact_sortorder' );
		for ( var a = 0; a < cso.options.length; a++ )
		{
			cso.options[a].onmouseover = function ()
			{
				if ( this.parentNode.mousedown )
				{
					for ( var b = 0; b < this.parentNode.options.length; b++ )
						if ( this.parentNode.options[b] != this )
							this.parentNode.options[b].selected = '';
					this.selected = 'selected';
				}
			}
		}
		cso.onmousedown = function ( e )
		{
			this.mousedown = true;
		}
		cso.onmouseup = function ( e )
		{
			this.sob = false;
			this.mousedown = false;
			this.index = false; 
			for ( var a = 0; a < this.options.length; a++ ) 
			{
				this.options[a].selected = ''; 
				this.blur();
			}
		}
		cso.onmousemove = function ( e )
		{
			if ( this.mousedown )
			{
				// Init
				if ( this.selectedIndex < 0 ) return;
				if ( !this.sob) 
					this.sob = this.options[ this.selectedIndex ];
			
				this.pindex = this.index;
				this.index = this.selectedIndex; 
			
				if ( this.pindex === false || this.index === false )
					return;
				if ( this.pindex >= 0 && this.index >= 0 ) 
				{ 
					var src = this.pindex;
					var dst = this.selectedIndex;
					var tmp = [ this.options[dst].value, this.options[dst].text ]; 
					this.options[dst].value = this.options[src].value;
					this.options[dst].text = this.options[src].text;  
					this.options[src].value = tmp[0]; 
					this.options[src].text = tmp[1];
				}
			}
		}
		function saveContactSortOrder ()
		{
			var items = document.getElementById ( 'contact_sortorder' ).options;
			var order = '';
			for ( var a = 0; a < items.length; a++ )
			{
				order += items[a].value + ":";
			}
			order = order.substr ( 0, order.length - 1 );
			document.location = 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=sortorder&fields=' + escape ( order );
		}
		function propertiesContactField ( field )
		{
			initModalDialogue ( 'properties', 320, 320, 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=properties&field=' + field );
		}
		function saveContactFieldValue ( field, fid )
		{
			var j = new bajax ();
			j.openUrl ( 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=savecfield&field=' + field + '&fid=' + fid, 'post', true );
			j.addVar ( 'data', ge ( 'fFieldData' ).value );
			j.addVar ( 'type', ge ( 'fFieldType' ).value );
			j.addVar ( 'required', ge ( 'fFieldRequired' ).value );
			j.onload = function ()
			{
				removeModalDialogue ( 'properties' );
			}
			j.send ();
		}
		function removeContactField ( field )
		{
			if ( confirm ( 'Er du sikker?' ) )
			{
				document.location = 'admin.php?module=extensions&extension=<?= $_REQUEST[ 'extension' ] ?>&modaction=removefield&field=' + escape ( field );
			}
		}
		addEvent ( 'onmouseup', function ( e )
		{
			if ( cso )
			{
				var top = getElementTop ( cso );
				var lef = getElementLeft ( cso );
				var wid = getElementWidth ( cso );
				var hei = getElementHeight ( cso );
				var xm = mousex - lef;
				var ym = mousey - top;
				if ( ym < 0 || xm < 0 || ym >= hei || xm >= wid )
					cso.onmouseup( e );
			}
		}
		);
	</script>

