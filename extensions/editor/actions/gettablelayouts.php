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


global $database;

if ( $othergroups = $database->fetchObjectRows ( 'SELECT DISTINCT(ContentGroups) AS `CG` FROM ContentElement' ) )
{
	$groups = array ();
	foreach ( $othergroups as $og )
	{
		if ( $og = explode ( ',', trim ( $og->CG ) ) )
		{
			foreach ( $og as $o )
			{
				if ( !trim ( $o ) ) continue;
				if ( !in_array ( trim ( $o ), $groups ) )
				{
					$groups[] = trim ( $o );
				}
			}
		}
	}
	if ( count ( $groups ) )
	{
		if ( $rows = $database->fetchObjectRows ( '
			SELECT * FROM `Setting` WHERE `SettingType` = "Layout" AND `Key` = "Table"
		' ) )
		{
			$s = '<table class="List"><tr><th>' . i18n ( 'Group_From' ) . ':</th><th>' . i18n ( 'Group_To' ) . ':</th><th>#</th></tr>';
			$sw = 2;
			foreach ( $rows as $row )
			{
				$now = explode ( "\t" , $row->Value );
		
				$str = '';
				foreach ( $groups as $g )
				{
					$sel = '';
					if ( $g == $now[0] )
						$sel = ' selected="selected"';
					$str .= '<option value="' . $g . '"' . $sel . '>' . $g . '</option>';
				}
				$sel1 = '<select id="tlfrom_' . $row->ID . '"><option value="">-</option>' . $str . '</select>';
		
				$str = '';
				foreach ( $groups as $g )
				{
					$sel = '';
					if ( $g == $now[1] )
						$sel = ' selected="selected"';
					$str .= '<option value="' . $g . '"' . $sel . '>' . $g . '</option>';
				}
				$sel2 = '<select id="tlto_' . $row->ID . '"><option value="">-</option>' . $str . '</select>';
		
				$sw = ( $sw == 1 ? 2 : 1 );
				$del = '<button type="button" onclick="delTableLayout(' . $row->ID . ')"><img src="admin/gfx/icons/page_white_delete.png"/></button>';
				$del .= '<button type="button" onclick="saveTableLayout(' . $row->ID . ')"><img src="admin/gfx/icons/page_save.png"/></button>';
		
				$s .= '<tr class="sw' . $sw . '"><td>' . $sel1 . '</td><td>' . $sel2 . '</td><td>' . $del . '</td></tr>';
			}
			$s .= '</table>';
			if ( isset ( $tpl ) )
				$tpl->tableLayouts = $s;
			else die ( 'ok<!--separate-->' . $s );
		}
		else
		{
			if ( isset ( $tpl ) )
				$tpl->tableLayouts = i18n ( 'No table layouts set up.' );
			else die ( 'ok<!--separate-->' . i18n ( 'No table layouts set up.' ) );
		}
	}
}
if ( isset ( $tpl ) )
	$tpl->tableLayouts = i18n ( 'No contentgroups defined.' );
else  ( 'ok<!--separate-->' . i18n ( 'No contentgroups defined.' ) );

?>
