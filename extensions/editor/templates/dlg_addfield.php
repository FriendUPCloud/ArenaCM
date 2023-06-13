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
			<button type="button" onclick="executeAddField ( )" title="<?= i18n ( 'Add the field' ) ?>">
				<img src="admin/gfx/icons/accept.png"> 
			</button>
			<button type="button" onclick="removeModalDialogue ( 'addfield' )" title="<?= i18n ( 'Cancel' ) ?>">
				<img src="admin/gfx/icons/cancel.png"> 
			</button>
		</div>
		<?= i18n ( 'Add a new field in' ) ?>: <?= $this->content->MenuTitle ?>
	</h1>
	<div class="Container" style="padding: <?= MarginSize ?>px">
		<form id="diaform" action="#" method="get">
			<p>
				<?= i18n ( 'Name the field' ) ?>:
			</p>
			<table class="LayoutColumns">
				<tr>
					<td>
						<div class="SubContainer" style="padding: <?= MarginSize ?>px">
							<input type="text" value="Navnløst" name="Name" size="30">
						</div>
					</td>
					<td>
						<div class="SubContainer" style="padding: <?= MarginSize ?>px; margin: 0 0 0 2px">
							<input type="text" value="0" name="SortOrder" size="3" style="text-align: center">
						</div>
					</td>
				</tr>
			</table>
			<div class="SpacerSmallColored"></div>
			<div class="Spacer"></div>
			<p>
				<?= i18n ( 'In which contentgroup do you wish to add the field?' ) ?>
			</p>
			<div class="SubContainer">
				<?= $this->contentgroups ?>
			</div>
			<div class="SpacerSmallColored"></div>
			<div class="Spacer"></div>
			<p>
				<?= i18n ( 'Choose type of field.' ) ?>
			</p>
			<div class="SubContainer" style="padding: <?= MarginSize ?>px; height: 80px; overflow: auto; overflow-x: hidden">
				<table width="100%">
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="text" checked="checked">
						</td>
						<td>
							<?= i18n ( 'A full article field' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="varchar">
						</td>
						<td>
							<?= i18n ( 'A simple text field' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="leadin">
						</td>
						<td>
							<?= i18n ( 'A small article field' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="whitespace">
						</td>
						<td>
							<?= i18n ( 'A simple space for styling' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="script">
						</td>
						<td>
							<?= i18n ( 'A Javascript field' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="style">
						</td>
						<td>
							<?= i18n ( 'A stylesheet' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="objectconnection">
						</td>
						<td>
							<?= i18n ( 'An object connection field' ) ?>
						</td>
					</tr>
					<?	
						$opts = false;
						if ( $dir = opendir ( 'extensions' ) )
						{
							$opts = '';
							while ( $f = readdir ( $dir ) )
							{
								if ( $f[0] == '.' ) continue;
								if ( file_exists ( 'extensions/' . $f . '/websnippet.php' ) )
								{
									$opts .= '<option value="' . $f . '">' . i18n ( strtoupper ( $f[0] ) . substr ( $f, 1, strlen ( $f ) - 1 ) ) . '</option>';
								}
							}
							closedir ( $dir );	
						}
						if ( $opts )
						{
							return '
					<tr>
						<td width="12px">
							<input type="radio" name="type" value="extension">
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
			<?if ( $GLOBALS[ 'Session' ]->AdminUser->isSuperUser ( ) ) { ?>
			<div class="Spacer"></div>
			<p>
				<?= i18n ( 'Show field globally?' ) ?>
			</p>
			<div class="SubContainer" style="padding: <?= MarginSize ?>px">
				<table width="100%">
					<tr>
						<td width="12px">
							<input type="radio" name="global" value="0" checked="checked">
						</td>
						<td>
							<?= i18n ( 'No, only on this page' ) ?>
						</td>
					</tr>
					<tr>
						<td width="12px">
							<input type="radio" name="global" value="1">
						</td>
						<td>
							<?= i18n ( 'Yes, on all pages' ) ?>
						</td>
					</tr>
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
		</form>
	</div>
	<div class="SpacerSmallColored"></div>
	<button type="button" onclick="executeAddField ( )">
		<img src="admin/gfx/icons/accept.png"> <?= i18n ( 'Add the field' ) ?>
	</button>
	<button type="button" onclick="removeModalDialogue ( 'addfield' )">
		<img src="admin/gfx/icons/cancel.png"> <?= i18n ( 'Cancel' ) ?>
	</button>
