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
	<h1 style="overflow: hidden; white-space: nowrap">
		<div class="HeaderBox">
			<button type="button" onclick="executeEditField ( )" title="Lagre">
				<img src="admin/gfx/icons/accept.png">
			</button>
			<button type="button" onclick="removeModalDialogue ( 'editfield' )" title="Lukk">
				<img src="admin/gfx/icons/cancel.png">
			</button>
		</div>
		<?= i18n ( 'Edit field in' ) ?>: <?= $this->content->MenuTitle ?>
	</h1>
	<div class="Container" style="padding: <?= MarginSize ?>px">
		<form id="diaform" action="#" method="get">
			<input type="hidden" name="field_id" value="<?= $this->field->ID ?>">
			<input type="hidden" name="field_type" value="<?= $this->fieldTable ?>">
			<table class="LayoutColumns">
				<tr>
					<td>
						<p>
							<?= i18n ( 'Edit field name' ) ?>:
						</p>
					</td>
					<td style="padding-left: 2px">
						<p>
							<?= i18n ( 'Admin visibility' ) ?>:
						</p>
					</td>
					<td style="padding-left: 2px">
						<p>
							<?= i18n ( 'Sort order' ) ?>:
						</p>
					</td>
				</tr>
				<tr>
					<td>
						<div class="SubContainer" style="padding: <?= MarginSize ?>px; height: 23px">
							<input type="text" value="<?= $this->field->Name ?>" name="Name" size="25">
						</div>
					</td>
					<td style="padding-left: 2px; text-align: center">
						<div class="SubContainer" style="padding: <?= MarginSize ?>px; height: 23px">
							<input type="checkbox"<?= $this->field->AdminVisibility ? ' checked="checked"' : '' ?> id="fieldadminvisibility">
						</div>
					</td>
					<td style="padding-left: 2px">
						<div class="SubContainer" style="padding: <?= MarginSize ?>px; height: 23px">
							<input type="text" value="<?= $this->field->SortOrder ?>" name="SortOrder" size="3" style="text-align: center">
						</div>
					</td>
				</tr>
			</table>
			<div class="SpacerSmallColored"></div>
			<div class="Spacer"></div>
			<p>
				<?= i18n ( 'Which content group do you want to place the field in?' ) ?>
			</p>
			<div class="Utvidelse: SubContainer">
				<?= $this->contentgroups ?>
			</div>
			<div class="SpacerSmallColored"></div>
			<div class="Spacer"></div>
			<?if ( in_array ( $this->field->Type, Array ( 'whitespace', 'text', 'varchar', 'leadin', 'extension', 'objectconnection', 'script', 'style' ) ) ) { ?>
			<p>
				<?= i18n ( 'Choose type of field.' ) ?>
			</p>
			<div class="SubContainer" style="padding: <?= MarginSize ?>px; height: 80px; overflow: auto; overflow-x: hidden">
				<table width="100%">
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="text"<?= $this->field->Type == 'text' ? ' checked="checked"' : '' ?>
						</td>
						<td>
							<?= i18n ( 'A full article field' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="varchar"<?= $this->field->Type == 'varchar' ? ' checked="checked"' : '' ?>
						</td>
						<td>
							<?= i18n ( 'A simple text field' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="leadin"<?= $this->field->Type == 'leadin' ? ' checked="checked"' : '' ?>
						</td>
						<td>
							<?= i18n ( 'A small article field' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="whitespace"<?= $this->field->Type == 'whitespace' ? ' checked="checked"' : '' ?>
						</td>
						<td>
							<?= i18n ( 'A simple space for styling' ) ?>
						</td>
					</tr>
					<?
						$checkja = $this->field->Type == 'script' ? ' checked="checked"' : '';
						$checksty = $this->field->Type == 'style' ? ' checked="checked"' : '';
						$checkob = $this->field->Type == 'objectconnection' ? ' checked="checked"' : '';
						return '
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="script"'.$checkja.'>
						</td>
						<td>
							' . i18n ( 'A Javascript field' ) . '
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="style"'.$checksty.'>
						</td>
						<td>
							' . i18n ( 'A stylesheet' ) . '
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="objectconnection"'.$checkob.'>
						</td>
						<td>
							' . i18n ( 'An object connection field' ) . '
						</td>
					</tr>
						';
					?>
					<?	
						$opts = false;
						if ( $dir = opendir ( 'extensions' ) )
						{
							$opts = '';
							while ( $f = readdir ( $dir ) )
							{
								if ( $f{0} == '.' ) continue;
								if ( file_exists ( 'extensions/' . $f . '/websnippet.php' ) )
								{
									if ( $f == $this->field->DataString )
										$s = ' selected="selected"';
									else $s = '';
									$opts .= '<option value="' . $f . '"' . $s . '>' . strtoupper ( $f{0} ) . substr ( $f, 1, strlen ( $f ) - 1 ) . '</option>';
								}
							}
							closedir ( $dir );
							
						}
						if ( $opts )
						{
							return '
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="extension"' . ( $this->field->Type == 'extension' ? ' checked="checked"' : '' ) . '">
						</td>
						<td>
							' . i18n ( 'Extension' ) . ': <select name="fieldextension">
								' . $opts . '
							</select>
						</td>
					</tr>
							';
						}
						
					?>
				</table>
			</div>
			<?}?>
			<?if ( !in_array ( $this->field->Type, Array ( 'text', 'varchar', 'leadin', 'extension' ) ) ) { ?>
			<p>
				Du endrer på et spesialfelt (<? 
					$string = $this->field->DataString;
					return trim ( $string ? $string : i18n ( 'unknown type' ) );
				?>):
			</p>
			<div class="SubContainer" style="padding: <?= MarginSize ?>px">
				<p>
					<?
						if ( $this->field->Type == 'contentmodule' )
						{
							return i18n ( 'You are editing a content module. You can not edit the content type.' );
						}
						else
						{	
							return i18n ( 'This field is of a specified type. You can not edit this field type.' );
						}
					?>
				</p>
			</div>
			<?}?>
			<?if ( $GLOBALS[ 'Session' ]->AdminUser->isSuperUser ( ) ) { ?>
			<div class="Spacer"></div>
			<p>
				<?= i18n ( 'Show field globally?' ) ?>
			</p>
			<div class="SubContainer" style="padding: <?= MarginSize ?>px">
				<table width="100%">
					<tr>
						<td width="12px">
							<input type="radio" name="global" value="0"<?= !$this->field->IsGlobal ? ' checked="checked"' : '' ?>
						</td>
						<td>
							<?= i18n ( 'No, only on this page' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="global" value="1"<?= $this->field->IsGlobal == '1' ? ' checked="checked"' : '' ?>>
						</td>
						<td>
							<?= i18n ( 'Yes, on all pages' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="global" value="2"<?= $this->field->IsGlobal == '2' ? ' checked="checked"' : '' ?>>
						</td>
						<td>
							<?= i18n ( 'Yes, on all subpages' ) ?>
						</td>
					</tr>
				</table>
			</div>
			<?}?>
			<?if ( !$GLOBALS[ 'Session' ]->AdminUser->isSuperUser ( ) ) { ?>
			<div class="Spacer"></div>
			<p>
				<?= i18n ( 'Visibility' ) ?>:
			</p>
			<div class="SubContainer">
				<?= i18n ( 'This field is showing' ) ?> <?= $this->field->IsGlobal ? i18n ( 'use on all pages' ) : i18n ( 'only on this page' ) ?>.
			</div>
			<?}?>
		</form>
	</div>
	<div class="SpacerSmallColored"></div>
	<button type="button" onclick="executeEditField ( )">
		<img src="admin/gfx/icons/accept.png"> <?= i18n ( 'Edit field' ) ?>
	</button>
	<button type="button" onclick="removeModalDialogue ( 'editfield' )">
		<img src="admin/gfx/icons/cancel.png"> <?= i18n ( 'Cancel' ) ?>
	</button>
