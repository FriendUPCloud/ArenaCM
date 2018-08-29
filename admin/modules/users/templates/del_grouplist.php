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
					<?
						global $Session;
					
						if ( !$this->groups ) return "";
						foreach ( $this->groups as $g )
						{
							if ( !$Session->AdminUser->checkPermission ( $g, 'Read', 'admin' ) ) 
								continue;
							
							if ( $g->ID == $Session->UsersCurrentGroup )
								$act = "Active";
							else $act = "";
						
							$oStr .= "
								<span class='Group$act' onclick='goGroupUrl ( 'admin.php?module=users&gid={$g->ID}' )'>
									<span onmouseover='overEditButtons = 1' onmouseout='overEditButtons = 0'>
							";
						
							$oStr .= "	<img src='admin/gfx/icons/group_edit.png' onclick='editGroup('{$g->ID}')' />";
							if ( $Session->AdminUser->checkPermission ( $g, 'Write', 'admin' ) )
								$oStr .= "	<img src='admin/gfx/icons/group_delete.png' onclick='deleteGroup('{$g->ID}')' />";
						
							$oStr .= "
										<img src='admin/gfx/icons/plugin.png' onclick='addToWorkbench ( '{$g->ID}', 'Groups' )' />
									</span>
									<img src='admin/gfx/icons/group.png' /> <strong>{$g->Name}</strong>
								</span>";
						}
						return $oStr;
					?>
					<span class="Group<?= $GLOBALS[ 'Session' ]->UsersCurrentGroup == 'all' ? 'Active' : '' ?>" onclick="document.location='admin.php?module=users&gid=all'">
						<img src="admin/gfx/icons/eye.png" /> <strong>Alle brukere</strong>
					</span>
					<?if ( $Session->AdminUser->_dataSource == 'core' ) { ?>
					<span class="Group<?= $GLOBALS[ 'Session' ]->UsersCurrentGroup == 'orphans' ? 'Active' : '' ?>" onclick="document.location='admin.php?module=users&gid=orphans'">
						<img src="admin/gfx/icons/user_gray.png" /> <strong>Brukere uten gruppe</strong>
					</span>
					<?}?>
					<span style="margin: 0;" class="Group<?= $GLOBALS[ 'Session' ]->UsersCurrentGroup == 'inactive' ? 'Active' : '' ?>" onclick="document.location='admin.php?module=users&gid=inactive'">
						<img src="admin/gfx/icons/user_green.png" /> <strong>Deaktiverte brukere</strong>
					</span>
