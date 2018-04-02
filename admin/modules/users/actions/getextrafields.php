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



ob_clean ( );

$db =& dbObject::globalValue ( 'database' );
if ( !$_REQUEST[ 'uid' ] ) die ( 'Systemet feiled i å laste inn ekstrafeltene.' );

if ( $rows = $db->fetchObjectRows ( '
	SELECT * FROM (
		(
			SELECT 
				ID, Name, Type, DataString as TheValue, SortOrder 
			FROM 
				ContentDataSmall 
			WHERE 
				Type="varchar" AND ContentTable = "Users" AND ContentID = \'' . $_REQUEST[ 'uid' ] . '\'
		)
		UNION
		(
			SELECT 
				ID, Name, Type, DataText as TheValue, SortOrder 
			FROM 
				ContentDataBig
			WHERE 
				Type="text" AND ContentTable = "Users" AND ContentID = \'' . $_REQUEST[ 'uid' ] . '\'
		)
	) as k ORDER BY SortOrder ASC, ID DESC;
' ) )
{
	$ostr = '<table style="width: border-collapse: collapse; border-spacing: 0; display: block">';
	foreach ( $rows as $row )
	{
		// Buttons for core user
		if ( $GLOBALS[ 'Session' ]->AdminUser->_dataSource == 'core' )
		{
			$buttons = 	'<a href="javascript:;" onclick="NudgeEx ( -1, \'' . $row->Type . '\', ' . $row->ID . ' )"><img src="admin/gfx/icons/arrow_up.png" border="0"/></a>' .
									'<a href="javascript:;" onclick="NudgeEx ( 1, \'' . $row->Type . '\', ' . $row->ID . ' )"><img src="admin/gfx/icons/arrow_down.png" border="0"/></a>' . 
									'<a href="javascript:;" onclick="DelEx ( \'' . $row->Type . '\', ' . $row->ID . ' )"><img src="admin/gfx/icons/bin.png" border="0"/></a>';
		}
		else $buttons = '';
		
		switch ( $row->Type )
		{
			case 'varchar':
				$ostr .= '
				<tr>
					<td style="vertical-align: top; font-weight: bold; width: 96px">' . $row->Name . ':</td>
					<td style="vertical-align: top; width: 100%">
						<input name="extra_' . $row->Type . '_' . $row->ID . '" type="text" value="' . $row->TheValue . '" size="32" style="width: 100%; -moz-box-sizing: border-box; box-sizing: border-box">
					</td>
					<td style="white-space: nowrap; vertical-align: top; width: 128px;">' . $buttons . '</td>
				</tr>
				';
				break;
			case 'text':
				$ostr .= '
				<tr>
					<td style="vertical-align: top; font-weight: bold;; width: 96px">' . $row->Name . ':</td>
					<td style="vertical-align: top; width: 100%">
						<textarea name="extra_' . $row->Type . '_' . $row->ID . '" rows="10" cols="30" style="width: 100%; -moz-box-sizing: border-box; box-sizing: border-box">' . $row->TheValue . '</textarea>
					</td>
					<td style="white-space: nowrap; vertical-align: top; width: 128px;">' . $buttons . '</td>
				</tr>
				';
				break;
			default: break;
		}
	}
	$ostr .= '</table>';
	die ( $ostr );
}
else
{
	die ( 'Ingen ekstrafelter er definert.' );
}
?>
