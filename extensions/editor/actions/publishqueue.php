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

if ( $_REQUEST[ 'oids' ] )
{
	if ( $oids = explode ( ',', $_REQUEST[ 'oids' ] ) )
	{
		foreach ( $oids as $oid )
		{
			$o = new dbObject ( 'PublishQueue' );
			if ( $o->load ( $oid ) )
			{
				$object = new dbObject ( $o->ContentTable );
				if ( $object->load ( $o->ContentID ) )
				{
					if ( $o->ActionScript && file_exists ( $o->ActionScript ) )
					{
						include_once ( $o->ActionScript );
					}
					else if ( $o->FieldName )
					{
						$object->{$o->FieldName} = 1;
						$object->save ();
					}
					$o->delete ( );
				}
			}
		}
	}
	die ( 'ok' );
}
else
{
	$tpl = new cPTemplate ( 'extensions/editor/templates/dlg_publish_queue.php' );
	$cnt = new dbContent ( );
	$cnt->load ( $_REQUEST[ 'cid' ] );
	$tpl->content =& $cnt;
	
	$db =& dbObject::globalValue ( 'database' );
	
	// Try to clean up bad queue elements (queue elements with missing real content)
	do
	{
		$deleted = false;
		if ( $rows = $db->fetchObjectRows ( 'SELECT * FROM PublishQueue WHERE ContentElementID = ' . $cnt->MainID . ' ORDER BY ID DESC' ) )
		{
			foreach ( $rows as $row )
			{
				$o = new dbObject ( $row->ContentTable );
				$o->load ( $row->ContentID );
				if ( !$o->ID )
				{
					$db->query ( 'DELETE FROM PublishQueue WHERE ID=' . $row->ID );
					$deleted = true;
					break;
				}
			}
		}
	}
	while ( $deleted );
	
	if ( count ( $rows ) )
	{
		// List them out
		$str = '<table cellspacing="0" cellpadding="2" width="100%" border="0">';
		$str .= '<tr><th align="left">Type:</th><th align="left">Tittel/Navn:</th><th></th></tr>';
		foreach ( $rows as $row )
		{
			$sw = $sw == 2 ? 1 : 2;
			$str .= '<tr class="sw' . $sw . '">';
			$str .= '<td>' . $row->LiteralName . '</td>';
			$str .= '<td>' . $row->Title . '</td>';
			$str .= '<td width="12px"><input type="checkbox" onchange="checkPublishQueue ( \'' . $row->ID . '\', this )"></td>';
			$str .= '</tr>';
		}
		$str .= '</table>';
		$tpl->list =& $str;
	}
	
	die ( $tpl->render ( ) );
}
?>
