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
<script type="text/javascript" src="admin/modules/users/javascript/main.js"></script>
<link rel="stylesheet" href="admin/modules/users/css/main.css" />
<div class="ModuleContainer">
	<table class="LayoutColumns">
		<tr>
			<td width="290px" style="padding-right: <?= MarginSize ?>px">
				<h1>
					<img src="admin/gfx/icons/group_gear.png" style="float: left; margin: 0pt 4px 0pt 0pt;"/> <?= i18n ( 'Group overview' ) ?>
				</h1>
				<div id="GroupList" class="Container">
					<?= renderGroupList ( ) ?>
					<script type="text/javascript">
						if ( document.getElementById ( 'GroupList' ).getElementsByTagName ( 'ul' )[0] )
							makeCollapsable ( document.getElementById ( 'GroupList' ).getElementsByTagName ( 'ul' )[0] );
					</script>
				</div>
				
				<div class="SpacerSmall"><em></em></div>
				
				<div class="Container" style="padding: <?= MarginSize ?>px">
					<button onclick="editGroup()">
						<img src="admin/gfx/icons/group_add.png" /> <?= i18n ( 'Add a group' ) ?>
					</button>
				</div>
				
				<div class="SpacerSmallColored"><em></em></div>
				
				<h1>
					<img src="admin/gfx/icons/vcard.png" style="float: left; margin: 0pt 4px 0pt 0pt;"/> <?= i18n ( 'Usercollections' ) ?>
				</h1>
				<div id="UnitList" class="Container">
					<?= renderUnitList ( ) ?>
					<script type="text/javascript">
						if ( document.getElementById ( 'UnitList' ).getElementsByTagName ( 'ul' )[0] )
							makeCollapsable ( document.getElementById ( 'UnitList' ).getElementsByTagName ( 'ul' )[0] );
					</script>
				</div>
				
				<div class="SpacerSmall"><em></em></div>
				
				<div class="Container" style="padding: <?= MarginSize ?>px">
				
					<button onclick="editCollection()">
						<img src="admin/gfx/icons/building_add.png" /> <?= i18n ( 'Add a usercollection' ) ?>
					</button>
					<?if ( $GLOBALS[ 'Session' ]->UsersCollectionID ) { ?>
					<button onclick="document.location='admin.php?module=users&collectionid=none'" class="Small">
						<img src="admin/gfx/icons/building_error.png"> <?= i18n ( 'Hide usercollections' ) ?>
					</button>
					<?}?>
					
				</div>
				
				<div class="SpacerSmallColored"><em></em></div>
				
				<h1>
					<img src="admin/gfx/icons/magnifier.png" style="float: left; margin: 0pt 4px 0pt 0pt;"/> <?= i18n ( 'Search users' ) ?>
				</h1>
				
				<div class="Container">
					
					<form method="post">
						
						<input type="hidden" name="pos" value="0"/>
						<p>
							<strong><?= i18n ( 'Keywords' ) ?>: </strong> &nbsp; <input type="text" name="keywords" value="<?= $_REQUEST[ "keywords" ] ?>" size="26" />					
						</p>
						
						<button type="submit">
							<img src="admin/gfx/icons/magnifier.png" /> <?= i18n ( 'Search' ) ?>
						</button>
						<?if ( $_REQUEST[ "keywords" ] ) { ?>
						<button type="button" onclick="document.location='admin.php?module=users'">
							<img src="admin/gfx/icons/cancel.png" /> <?= i18n ( 'Reset search' ) ?>
						</button>
						<?}?>
						
					</form>
					
				</div>
				
			</td>
			<td>
				<h1>
					<img src="admin/gfx/icons/user_suit.png" style="float: left; margin: 0pt 4px 0pt 0pt;"/> 
					<div class="HeaderBox">
						<label><?= i18n ( 'Users pr. page' ) ?>:</label>
						<select onchange="document.location='admin.php?module=users&limit=' + this.value">
							<?
								foreach ( Array ( 20, 50, 100, 500, 1000, 10000 ) as $a )
								{
									$s = $a == $this->limit ? ' selected="selected"' : '';
									$str .= '<option value="' . $a . '"' . $s . '>' . $a . '</option>';
								}
								return $str;
							?>
						</select>
					</div>
					<?= i18n ( 'Users' ) ?> <?= $this->groupChoice ?>:
				</h1>
				<div class="Container" style="padding: <?= MarginSize ?>px" id="UserList">
					<table class="List">
						<tr>
							<?
								$str = '';
								$widths = array ( '32px', '32px', '', '', '128px', '128px', '70px', '70px' );
								$w = 0;
								foreach ( array (
										'Bilde'=>i18n('user_Image'),
										'SortOrder'=>i18n('user_SortOrder'),
										'Username'=>i18n('user_Username'),
										'I gruppe(r)'=>i18n('user_ingroup'),
										'DateCreated'=>i18n('user_datecreated'),
										'DateLogin'=>i18n('user_datelogin')
									) as $k=>$v )
								{
									if ( $v )
									{
										if ( $k == $GLOBALS[ 'Session' ]->UsersSortField )
										{
											$ss = '<em>'; $se = '</em>';
										}
										else if ( $k . 'Inv' == $GLOBALS[ 'Session' ]->UsersSortField )
										{
											$ss = '<u>'; $se = '</u>';
										}
										else $ss = $se = '';
										
										$str .= '<th style="width: ' . $widths[$w] . '">'.$ss.'<a href="admin.php?module=users&sortfield='.$k.'">'.$v.':</a>'.$se.'</th>';
									}
									else $str .= '<th style="width: ' . $widths[$w] . '">' . $k . ':</th>';
									$w++;
								}
								return $str;
							?>
							<th style="text-align: center; white-space: nowrap">
								<button onclick="addToGroup()" class="Small" title="<?= i18n ( 'Add to group' ) ?>">
									<img src="admin/gfx/icons/group_link.png" />
								</button>
								<button type="button" onclick="deleteUsers()" class="Small" title="<?= i18n ( 'Delete user' ) ?>">
									<img src="admin/gfx/icons/user_delete.png">
								</button>
								<button type="button" onclick="addToCollection()" class="Small" title="<?= i18n ( 'Add to collection' ) ?>">
									<img src="admin/gfx/icons/building_link.png">
								</button>
								<?if ( $GLOBALS[ 'Session' ]->UsersCollectionID ) { ?>
								<button type="button" onclick="removeFromCollection()" class="Small" title="<?= i18n ( 'Remove from collection' ) ?>">
									<img src="admin/gfx/icons/building_error.png">
								</button>
								<?}?>
							</th>
							<th style="text-align: center">
								<?= i18n ( 'Tools' ) ?>:
							</th>
						</tr>
					<?= $this->userlist ?>
						<tr>
							<td colspan="6">
							</td>
							<td style="text-align: center; white-space: nowrap; padding: 4px">
								<button onclick="addToGroup()" class="Small" title="<?= i18n ( 'Add to group' ) ?>">
									<img src="admin/gfx/icons/group_link.png" />
								</button>
								<button type="button" onclick="deleteUsers()" class="Small" title="<?= i18n ( 'Delete user' ) ?>">
									<img src="admin/gfx/icons/user_delete.png" />
								</button>
								<button type="button" onclick="addToCollection()" class="Small" title="<?= i18n ( 'Add to collection' ) ?>">
									<img src="admin/gfx/icons/building_link.png">
								</button>
								<?if ( $GLOBALS[ 'Session' ]->UsersCollectionID ) { ?>
								<button type="button" onclick="removeFromCollection()" class="Small" title="<?= i18n ( 'Remove from collection' ) ?>">
									<img src="admin/gfx/icons/building_error.png">
								</button>
								<?}?>
							</td>
							<td>
								
							</td>
						</tr>
					</table>
					<?if ( $this->authGroups > 0 ) { ?>
					<div class="Spacer"><em></em></div>
					<div class="SpacerSmallColored"><em></em></div>
					<button onclick="newUser ( )">
						<img src="admin/gfx/icons/user_add.png" /> <?= i18n ( 'Add user' ) ?>
					</button>
					<button onclick="importUser ( )">
						<img src="admin/gfx/icons/page_white_excel.png" /> <?= i18n ( 'Import users' ) ?>
					</button>
					<button onclick="document.location='admin.php?module=users&export=1';">
						<img src="admin/gfx/icons/table_go.png" /> <?= i18n ( 'Export users' ) ?>
					</button>
					<button onclick="selectAllUsers()">
						<img src="admin/gfx/icons/cog.png" /> <?= i18n ( 'Toggle selection on listed users' ) ?>
					</button>
					<?}?>
					<div class="Spacer"><em></em></div>
					<?= $this->Navigation ?>
				</div>
			</td>
		</tr>
	</table>
</div>
