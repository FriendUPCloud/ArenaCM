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

i18nAddLocalePath ( 'lib/skeleton/modules/mod_subpages/locale' );

$options = CreateObjectFromString ( $fieldObject->DataMixed );

$mode_Options = '';
foreach ( 
	array ( 
		'mode_full'=>'i18n_mode_full', 
		'mode_brief'=>'i18n_mode_brief',
		'mode_field'=>'i18n_mode_field'
	) as $mode_=>$val
)
{
	$s = $options->Mode == $mode_ ? ' selected="selected"' : '';
	$mode_Options .= '<option value="' . $mode_ . '"' . $s . '>' . i18n ( $val ) . '</option>';
}

$level_Options = '';
for ( $a = 0; $a < 10; $a++ )
{
	$s = $options->Levels == $a ? ' selected="selected"' : '';
	$level_Options .= '<option value="' . $a . '"' . $s . '>' . $a . '</option>';
}

$t = '<table>
			<tr>
				<td valign="middle"><label>' . i18n ( 'subpages_Subpages of page' ) . ':</label></td>
				<td valign="middle">
					' . $content->RenderSelect ( 'subp' . $fieldObject->ID, 'subpages_' . $fieldObject->ID, $fieldObject->DataInt ) . '
				</td>
				<td valign="middle">
					<select id="subpagesmode_' . $fieldObject->ID . '">
					' . $mode_Options . '
					</select>
				</td>
				<td valign="middle">
					<select id="subpageslevels_' . $fieldObject->ID . '">
					' . $level_Options . '
					</select>
				</td>
			</tr>
		</table>';
		
if ( $options->Mode == 'mode_field' )
{
	$p = new dbObject ( 'ContentElement' );
	$p->Parent = $fieldObject->DataInt;
	$p = $p->findSingle ();
	$p->loadExtraFields ();
	$o = '<select id="subpagefields_' . $fieldObject->ID . '">';
	foreach ( $p as $f=>$v )
	{
		if ( !isset ( $p->{'_extra_'.$f} ) )
			continue;
		if ( $f == $options->Field ) $s = ' selected="selected"';
		else $s = '';
		$o .= '<option value="' . $f . '"' . $s . '>' . $f . '</option>';
	}
	$o .= '</select>';
	$t .= '<table><tr><td><strong>' . i18n ( 'i18n_choose_field' ) . ':</strong></td><td>' . $o . '</td></tr></table>';
}

if ( $_REQUEST[ 'action' ] && $_REQUEST[ 'action' ] == 'subpages_saveoption' )
{
	$fieldObject->DataInt = $_REQUEST[ 'sid' ];
	$fieldObject->DataMixed = $_REQUEST[ 'mixed' ];
	$fieldObject->save ();
	die ('ok<!--separate-->' . $t );
}

$module = '
	<div id="Gui_' . $fieldObject->ID . '">
		' . $t . '
	</div>
	<script type="text/javascript">
		AddSaveFunction ( function ()
		{ 
			var j = new bajax ();
			j.openUrl ( \'admin.php?module='.$_REQUEST['module'].'&extension='.$_REQUEST['extension'].'&action=subpages_saveoption\', \'post\', true );
			j.addVar ( \'sid\', document.getElementById ( \'subpages_'.$fieldObject->ID.'\' ).value );
			var flds = ge(\'subpagefields_' . $fieldObject->ID . '\');
			var s = "Mode\t"+ge(\'subpagesmode_' . $fieldObject->ID . '\').value;
			s += "\nLevels\t" +ge(\'subpageslevels_' . $fieldObject->ID . '\').value;
			s += "\nField\t" +(flds?flds.value:\'\');
			j.addVar ( \'mixed\', s );
			j.onload = function ()
			{
				var r = this.getResponseText().split(\'<!--separate-->\');
				if ( r[0] == \'ok\' )
				{
					ge("Gui_' . $fieldObject->ID . '").innerHTML = r[1];
				}
				else alert ( r[0] );
			}
			j.send ();
		}
		);
	</script>
';

?>
