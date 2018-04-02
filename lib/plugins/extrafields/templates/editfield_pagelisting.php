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
			<img src="admin/gfx/icons/text_list_bullets.png" /> Sideutlisting: <?= $this->name ?>
		</h2>
		<div class="BlockContainer">
			<button type="button" style="float: right">
				<img src="admin/gfx/icons/page_edit.png"> Rediger utlistingen
			</button>
			<ul>
			<?
				$page = new dbContent ( );
				$page->load ( $this->data->DataInt );
				$page->loadSubElements ( );
				if ( ( $c = count ( $page->subElements ) ) && is_array ( $page->subElements ) )
				{
					$i = 0;
					foreach ( $page->subElements as $sub )
					{
						if ( $i < $c )
						{
							$ostr .= '<li>' . $sub->Title . ' (' . ArenaDate ( $sub->DateModified, DATE_FORMAT ) . ')</li>';
						}
						$i++;
					}
					return $ostr;
				}
				return '<li><em>Ingen sider finnes.</em></li>';
				
		
			?>
			</ul>
		</div>
		<div class="BlockContainer">
			<div class="SubContainer" style="padding: <?= MarginSize ?>px">
			<?
				$content = new dbContent ( );
				$content->addClause ( 'WHERE', '!IsDeleted' );
				$content->addClause ( 'WHERE', '!IsTemplate' );
				$content->addClause ( 'WHERE', 'IsPublished' );
				$content->addClause ( 'WHERE', 'ID = MainID' );
				$content->addClause ( 'ORDER BY', 'Language ASC, Parent ASC, MenuTitle ASC' );
				if ( $obj =& $this->data )
				{
					$GLOBALS[ 'content' ] = $content->find ( );
		
					$options = '';
					$langs = new dbObject ( 'Languages' );
					$langs->addClause ( 'ORDER BY', 'IsDefault DESC, Name ASC' );
					if ( $langs = $langs->find ( ) )
					{
						$options .= '<option value="0"> </option>';
						foreach ( $langs as $lang )
						{
							$options .= '<option value="0">' . $lang->NativeName . ' (' . $lang->Name . '):</option>';
							$options .= parseContent ( 0, $lang->ID, $obj->DataInt );
							$options .= '<option value="0"> </option>';
						}
					}
					else
					{
						$options .= parseContent ( 0, false, $obj->DataInt, '' );
					}
		
					if ( $obj->DataInt == 0 ) $s = ' selected="selected"'; else $s = '';
					if ( $obj->DataInt < 0 ) $s2 = ' selected="selected"'; else $s2 = '';
				
					$output = '<select style="font-size: 9px" onchange="if ( this.value == 0 ) this.selectedIndex = 0; actionExtraField ( \\\'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Small&id=' . $obj->ID . '&field=DataInt&value=\\\' + this.value )">';
					$output .= '<option value="0"' . $s . '>Arv overliggende sides innstilling</option>';
					$output .= '<option value="-1"' . $s2 . '>Undersider av denne siden</option>';
					$output .= $options . '</select>';
		
					$alternatives = Array ( 'titles'=>'Titler', 'intro'=>'Ingress', 'titlesandintro'=>'Titler og ingress', 'body'=>'Body', 'titlesandbody'=>'Titler og body', 'everything'=>'Alt' );
		
					$output .= ' &nbsp; <strong>List ut:</strong> <select onchange="actionExtraField ( \\\'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Small&id=' . $obj->ID . '&field=DataString&value=\\\' + this.value )">';
					foreach ( $alternatives as $k=>$alternative )
					{
						if ( $k == $obj->DataString )
							$s = ' selected="selected"';
						else $s = '';
						$output .= '<option value="' . $k . '"' . $s . '>' . $alternative . '</option>';
					}
					$output .= '</select>';
				
					$output .= ' &nbsp; <strong>Max antall sider:</strong> <input size="5" style="text-align: right" type="text" value="' . round ( $obj->DataDouble ) . '" onchange="actionExtraField ( \\\'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Small&id=' . $obj->ID . '&field=DataDouble&value=\\\' + this.value )">';
				
				
					$usingParentHeading = false;
					if ( $obj->DataMixed )
					{
						$data = explode ( "\\n", $obj->DataMixed );
						
						foreach ( $data as $row )
						{
							list ( $key, $value ) = explode ( "\\t", $row );
							switch ( $key )
							{
								case 'usingParentHeading':
									$usingParentHeading = $value;
									break;
							}
						}
					}
					$output .= '
					<table>
						<tr>
							<td><strong>Bruker overliggende side som heading:</strong></td>
							<td>
								<input type="checkbox"' . ( $usingParentHeading ? ' checked="checked"' : '' ) . ' onchange="document.getElementById ( \\'usingParentHeading_' . $obj->ID . '\\' ).value = this.checked ? \\'1\\' : \\'0\\'">
								<input type="hidden" id="usingParentHeading_' . $obj->ID . '" value="' . ( $usingParentHeading ? '1' : '0' ) . '">
							</td>
						</tr>
					</table>';
				
					return $output;
				}
				return '';
			?>
			</div>
			<div class="SpacerSmallColored"></div>
			<button type="button" onclick="swapToggleVisibility ( this.parentNode, this.parentNode.sibling )">
				<img src="admin/gfx/icons/cancel.png" /> Lukk
			</button>
		</div>
	</div>
	<script type="text/javascript">
		AddSaveFunction ( function ( )
		{
			var val = new Array ( );
			val.push ( "usingParentHeading\t" + document.getElementById ( 'usingParentHeading_<?= $this->data->ID ?>' ).value );
			var j = new bajax ( );
			j.openUrl ( 'admin.php?plugin=extrafields&pluginaction=setfieldoption&field=DataMixed&type=Small&id=<?= $this->data->ID ?>', 'post', true );
			j.addVar ( 'value', val.join ( "\n" ) );
			j.onload = function ( ){};
			j.send ( );
		}
		);
	</script>
	
