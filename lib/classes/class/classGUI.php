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



class cClassGUI
{
	function renderJavascript ( )
	{
		$tpl = new cPTemplate ( "admin/modules/classes/templates/class_javascript.php" );
		return $tpl->render ( );
	}

	function renderCalendar ( $field, $value, $useTime = false )
	{
		$value = $value ? $value : date ( "Y-m-d H:i:s" );
		$tpl = new cPTemplate ( "admin/modules/classes/templates/field_calendar.php" );
		$tpl->useTime = $useTime;
		$tpl->value = $value;
		$tpl->field = $field->Field;
		return $tpl->render ( );
	}

	function getObjectField ( $field, $object )
	{
		if ( $field->Field != "ID" )
		{
			$props = $object->fieldProperties ( $field->Field ); 
			$label = $props->Label;
			$value = $object->{$field->Field};
			
			$oStr = "<p><strong>" . ( $label ? $label : $field->Field ) . "</strong></p>";
			list ( $type ) = explode ( "(", $field->Type );
			
			if ( $props->Relation )
			{
				if ( $relations = $object->getRelations ( $field->Field ) )
				{
					$opt = "";
					foreach ( $relations as $rel )
					{
						if ( $rel->ID == $value )
							$sel = " selected"; else $sel = "";
						$opt .= "<option value=\"{$rel->ID}\"$sel>" . $rel->getIdentifier ( ) . "</option>";
					}
					return $oStr . "<p><select name=\"{$field->Field}\">$opt</select></p>";
				}
			}
			
			switch ( $type )
			{
				case "date":
					return "$oStr" . renderCalendar ( $field, $value );
				case "datetime":
					return "$oStr" . renderCalendar ( $field, $value, true );
				case "int":
				case "bigint":
				case "double":
					return "$oStr<p><input id=\"field_{$field->Field}\" name=\"{$field->Field}\" type=\"text\" class=\"input_text\" size=\"15\" value=\"$value\" /></p>";
				case "text":
				case "longtext":
					return "$oStr<p><textarea class=\"input_text\" id=\"field_{$field->Field}\" name=\"{$field->Field}\" rows=\"10\" cols=\"46\">$value</textarea></p>";
				default:
					return "$oStr<p><input id=\"field_{$field->Field}\" name=\"{$field->Field}\" type=\"text\" value=\"$value\" class=\"input_text\" size=\"45\" /></p>";
			}
		}
		else
		{
			return "<input type=\"hidden\" name=\"ID\" value=\"" . $object->{$field->Field} . "\" />";
		}
	}
}
?>
