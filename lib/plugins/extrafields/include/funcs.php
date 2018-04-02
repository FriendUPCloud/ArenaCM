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



// Check on some fields in the db
function checkPluginExtrafieldsDb ( )
{
	$db =& dbObject::globalValue ( 'database' );
	$mixedfound = false;
	$element = new dbObject ( 'ContentDataSmall' );
	foreach ( $element->_table->getFieldNames () as $field )
	{
		if ( $f == 'DataMixed' )
		{
			$mixedfound = true;
			break;
		}
	}
	if ( !$mixedfound )
	{ 
		$db->query ( 'ALTER TABLE `ContentDataSmall` ADD `DataMixed` text default "" AFTER `DataString`' );
	}
}
function parseCats ( $parent, $language, $current, $r = '&nbsp;&nbsp;&nbsp;&nbsp;' )
{
	$cats = $GLOBALS[ 'cats' ];
	$oStr = '';
	if ( !$cats ) return '';
	foreach ( $cats as $cat )
	{
		if ( ( $cat->Language == $language || $parent != 0 || !$language ) && $cat->Parent == $parent )
		{
			if ( $current == $cat->ID ) $s = ' selected="selected"'; else $s = '';
			$oStr .= "<option value=\"{$cat->ID}\"$s>$r{$cat->Name}</option>";
			$oStr .= parseCats ( $cat->ID, $language, $current, $r . '&nbsp;&nbsp;&nbsp;&nbsp;' );
		}
	}
	return $oStr;
}

function parseContent ( $parent, $language, $current, $r = '&nbsp;&nbsp;&nbsp;&nbsp;' )
{
	$content = $GLOBALS[ 'content' ];
	$oStr = '';
	if ( !$content ) return '';
	foreach ( $content as $cnt )
	{
		if ( ( $cnt->Language == $language || $parent != 0 || !$language ) && $cnt->Parent == $parent )
		{
			if ( $current == $cnt->ID ) $s = ' selected="selected"'; else $s = '';
			if ( $cnt->ID )
			{
				if ( !$cnt->Title ) $cnt->Title = $cnt->MenuTitle;
				if ( !$cnt->Title ) $cnt->Title = $cnt->SystemName;
				if ( !$cnt->Title ) $cnt->Title = $cnt->ID;
				$oStr .= "<option value=\"{$cnt->ID}\"$s>$r{$cnt->Title}</option>";
				$oStr .= parseContent ( $cnt->ID, $language, $current, $r . '&nbsp;&nbsp;&nbsp;&nbsp;' );
			}
		}
	}
	return $oStr;
}

function showExtraFields ( $contentid, $contenttype )
{
	$db = &dbObject::globalValue ( 'database' );
	
	$oStr = false;
	
	if ( $rows = $db->fetchObjectRows ( "
		SELECT z.* FROM 
		(
			(
				SELECT b.ID, b.IsVisible, b.Name, b.Type, b.SortOrder, 'Big' AS `DataTable`, b.ContentGroup, b.IsGlobal  
				FROM 
				`ContentDataBig` b 
				WHERE 
				b.ContentID='$contentid' 
				AND
				b.ContentTable=\"$contenttype\"
			)
			UNION
			(
				SELECT c.ID, c.IsVisible, c.Name, c.Type, c.SortOrder, 'Small' AS `DataTable`, c.ContentGroup, c.IsGlobal
				FROM 
				`ContentDataSmall` c 
				WHERE 
				c.ContentID='$contentid' 
				AND
				c.ContentTable=\"$contenttype\"
			)
		) AS z
		ORDER BY z.SortOrder ASC, z.ID ASC
	" ) ) 
	{
	
		$oStr = "\n
						<table class=\"List\">
							<tr>
								<th style=\"text-align: right; width: 5%\">#</th>
								<th style=\"width: 19%\"><strong>Felt ID:</strong></th>
								<th style=\"width: 6%\"><strong>Viser:</strong></th>
								<th style=\"width: 6%\"><strong>Global:</strong></th>
								<th style=\"width: 14%\"><strong>Felttype:</strong></th>
								" . ( $contenttype == 'ContentElement' ? "<th style=\"width: 15%\"><strong>Innholdsgruppe:</strong></th>" : '' ) . "
								<th style=\"width: 40%\">&nbsp;</th>
							</tr>\n";
							
		$len = count ( $rows );
		
		// Separate content groups into an array
		$content = new dbObject ( 'ContentElement' );
		if ( $content->load ( $contentid ) )
		{
			$groups = explode ( ',', $content->ContentGroups );
			foreach ( $groups as $k=>$v ) 
			{
				if ( !$v ) continue;
				$outgroups[] = trim ( $v );
			}
			$groups = $outgroups;
		}
		else $groups = false;
		
		// List out fields
		for ( $a = 0; $a < $len; $a++ )
		{
			switch ( $rows[ $a ]->Type )
			{
				case 'pagelisting':
					$name = 'Sideutlisting';
					$options = '';
					break;
				case 'newscategory':
					$name = 'Nyhetskategori';
					$options = '';
					break;
				
				case 'image':
					$name = 'Bilde';
					$options = '';
					break;
				
				case 'extension':
				
					$obj = new dbObject ( 'ContentData' . $rows[ $a ]->DataTable );
					$obj->load ( $rows[ $a ]->ID );
					
					$name = 'Utvidelse';
					$options = '<select onchange="actionExtraField ( \'admin.php?plugin=extrafields&pluginaction=setfieldoption&type=Small&id=' . $obj->ID . '&field=DataString&value=\' + this.value )">';
					if ( is_dir ( 'extensions' ) && $dir = opendir ( 'extensions' ) )
					{
						$options .= '<option value=""' . ( $obj->DataString == '' ? ' selected="selected"' : '' ) . '>Velg en utvidelse:</option>';
						while ( $file = readdir ( $dir ) )
						{
							if ( $file{0} != '.' )
							{
								if ( $file == $obj->DataString ) $s = ' selected="selected"'; else $s = '';
								$options .= "<option value=\"{$file}\"$s>" . 
									strtoupper ( $file{0} ) . substr ( $file, 1, strlen ( $file ) - 1 ) . 
									'</option>';
							}
						}
						closedir ( $dir );
					}
					$options .= '</select>';
					break;
					
				case 'file':
					$name = 'Fil';
					$options = '';
					break;
				
				case 'objectconnection':
					$name = 'Objekt tilkoblingsfelt';
					$options = '';
					break;
					
				case 'script':
					$name = 'Script';
					$options = '';
					break;
					
				case 'style':
					$name = 'Stilark';
					$options = '';
					break;
					
				case 'formprocessor':
					$name = 'Skjema prosessering';
					$options = '';
					break;
					
				case 'varchar':
					$name = 'Setning';
					$options = '';
					break;
					
				case 'leadin':
					$name = 'Kort tekst';
					$options = '';
					break;
					
				case 'text':
					$name = 'Artikkel';
					$options = '';
					break;
				default:
					$name = 'Tekstfelt';
					$options = '';
					break;
			}
		
			$sw = $sw == 1 ? 0 : 1;
			
			if ( count ( $groups ) && is_array ( $groups ) )
			{
				$igroups = '';
				foreach ( $groups as $group )
				{
					if ( !$group ) continue;
					$s = ( $group == $rows[ $a ]->ContentGroup ) ? ' selected="selected"' : '';
					$igroups .= '<option value="' . $group . '"' . $s . '>' . $group . '</option>';
				}
			}
			else
			{
				$igroups = '<option value="Default" selected="selected">Default</option>';
			}
			
			$oStr .= "\n
							<tr class=\"swSub$sw\">
								<td style=\"text-align: right\">
									<input type=\"text\" size=\"3\" onchange=\"setFieldSortOrder( '" . $rows[ $a ]->ID . "', '" . $rows[ $a ]->DataTable . "', this.value )\" style=\"width: 16px; text-align: right\" value=\"" . $rows[ $a ]->SortOrder . "\">
								</td>
								<td>
									<strong>" . $rows[ $a ]->Name . "</strong>
								</td>
								<td>
									<input onchange=\"setFieldVisibility ( '" . $rows[ $a ]->ID . "', '" . $rows[ $a ]->DataTable . "', this.checked ? '1' : '0' )\" type=\"checkbox\"" . ( $rows[ $a ]->IsVisible ? ' checked="checked"' : '' ) . "/>
								</td>
								<td>
									<input onchange=\"setFieldGlobal ( '" . $rows[ $a ]->ID . "', '" . $rows[ $a ]->DataTable . "', this.checked ? '1' : '0' )\" type=\"checkbox\"" . ( $rows[ $a ]->IsGlobal ? ' checked="checked"' : '' ) . "/>
								</td>
								<td>
									$name
								</td>
								
								" . ( $contenttype == 'ContentElement' ? ( "
								<td>
									<select onchange=\"setFieldGroup ( '" . $rows[ $a ]->ID . "', '" . $rows[ $a ]->DataTable . "', this.value )\">
										<option value=\"\">Velg gruppe:</option>
										$igroups
									</select>
								</td>" ) : "" ) . "
								
								<td style=\"text-align: right;\">
									<button type=\"button\" onclick=\"moveExtraField ( '-1', '" . $rows[ $a ]->ID . "' )\">
										<img src=\"admin/gfx/icons/arrow_up.png\" /> 
									</button>
									<button type=\"button\" onclick=\"moveExtraField ( '1', '" . $rows[ $a ]->ID . "' )\">
										<img src=\"admin/gfx/icons/arrow_down.png\" /> 
									</button>
									<button type=\"button\" onclick=\"var n; if ( n = prompt ( 'Endre felt navn?', '" . $rows[ $a ]->Name . "' ) ){ editField ( '" . $rows[ $a ]->ID . "', '" . $rows[ $a ]->DataTable . "', n ); }\">
										<img src=\"admin/gfx/icons/table_edit.png\" /> 
									</button>
									<button type=\"button\" onclick=\"if ( confirm ( 'Er du sikker?' ) ){ deleteExtraField ( '" . $rows[ $a ]->ID . "', '" . $rows[ $a ]->DataTable . "' ); }\">
										<img src=\"admin/gfx/icons/bin.png\" />
									</button>
								</td>
							</tr>\n";
							
			if ( !$options ) continue;
			
			$sw = $sw == 1 ? 0 : 1;
			
			$oStr .= "\n
							<tr class=\"swSub$sw\">
								<td></td>
								<td colspan=\"4\">
									$options
								</td>
							</tr>\n";
		}
		$oStr .= "\n
						</table>\n";
	}
	return $oStr;
}

function showConnectedObjects ( $objectid, $objecttype )
{
	$str = 'Ingen objekter er tilkoblet.';
	$obj = new dbObject ( $objecttype );
	if ( $obj->load ( $objectid ) )
	{
		$objs = new dbObject ( 'ObjectConnection' );
		$objs->ObjectType = $objecttype;
		$objs->ObjectID = $objectid;
		$objs->addClause ( 'ORDER BY' ,'SortOrder ASC' );
		if ( $objs = $objs->find ( ) )
		{
			$end = Array ( );
			foreach ( $objs as $ob )
			{
				switch ( $ob->ConnectedObjectType )
				{
					case 'Folder':
						$org = new dbObject ( $ob->ConnectedObjectType );
						$org->load ( $ob->ConnectedObjectID );
						$str = '';
						$str .= '<div class="ConnectedObject">';
						$str .= '<img class="Identifier" src="admin/gfx/icons/folder.png">';
						$str .= '<div class="Buttons">';
						$str .= '<button class="Small" type="button" onclick="nudgecobj ( -1,' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/arrow_up.png"></button>';
						$str .= '<button class="Small" type="button" onclick="nudgecobj ( 1,' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/arrow_down.png"></button>';
						$str .= '<button class="Small" type="button" onclick="delcobj ( ' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/bin.png"></button>';
						$str .= '</div>';
						$str .= '<div class="Title">' . $org->Name . ' (mappe)</div>';
						$str .= '<br style="clear: both">';
						$str .= '</div>';
						$end[] = $str;
						break;
					case 'Groups':
						$org = new dbObject ( $ob->ConnectedObjectType );
						$org->load ( $ob->ConnectedObjectID );
						$str = '';
						$str .= '<div class="ConnectedObject">';
						$str .= '<img class="Identifier" src="admin/gfx/icons/group.png">';
						$str .= '<div class="Buttons">';
						$str .= '<button class="Small" type="button" onclick="nudgecobj ( -1,' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/arrow_up.png"></button>';
						$str .= '<button class="Small" type="button" onclick="nudgecobj ( 1,' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/arrow_down.png"></button>';
						$str .= '<button class="Small" type="button" onclick="delcobj ( ' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/bin.png"></button>';
						$str .= '</div>';
						$str .= '<div class="Title">' . $org->Name . ' (brukergruppe)</div>';
						$str .= '<br style="clear: both">';
						$str .= '</div>';
						$end[] = $str;
						break;
					case 'Image':
						include_once ( 'lib/classes/dbObjects/dbImage.php' );
						$obj = new dbImage ( $ob->ConnectedObjectID );
						if ( !$obj->ID ) return '';
						$str = '';
						$str .= '<div class="ConnectedObject">';
						$str .= '<img class="Identifier" src="' . $obj->getImageUrl ( 20, 16 ) . '">';
						$str .= '<div class="Buttons">';
						$str .= '<button class="Small" type="button" onclick="nudgecobj ( -1,' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/arrow_up.png"></button>';
						$str .= '<button class="Small" type="button" onclick="nudgecobj ( 1,' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/arrow_down.png"></button>';
						$str .= '<button class="Small" type="button" onclick="delcobj ( ' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/bin.png"></button>';
						$str .= '</div>';
						$str .= '<div class="Title">' . $obj->Title . ' (bilde)</div>';
						$str .= '<br style="clear: both">';
						$str .= '</div>';
						$end[] = $str;
						break;
					case 'File':
						include_once ( 'lib/classes/dbObjects/dbFile.php' );
						$obj = new dbFile ( $ob->ConnectedObjectID );
						if ( !$obj->ID ) return '';
						$str = '';
						$str .= '<div class="ConnectedObject">';
						$str .= '<img class="Identifier" src="admin/gfx/icons/package.png">';
						$str .= '<div class="Buttons">';
						$str .= '<button class="Small" type="button" onclick="nudgecobj ( -1,' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/arrow_up.png"></button>';
						$str .= '<button class="Small" type="button" onclick="nudgecobj ( 1,' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/arrow_down.png"></button>';
						$str .= '<button class="Small" type="button" onclick="delcobj ( ' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/bin.png"></button>';
						$str .= '</div>';
						$str .= '<div class="Title">' . $obj->Title . ' (fil)</div>';
						$str .= '<br style="clear: both">';
						$str .= '</div>';
						$end[] = $str;
						break;
					case 'ContentElement':
						include_once ( 'lib/classes/dbObjects/dbContent.php' );
						$obj = new dbContent ( $ob->ConnectedObjectID );
						if ( !$obj->ID ) return '';
						$str = '';
						$str .= '<div class="ConnectedObject">';
						$str .= '<img class="Identifier" src="admin/gfx/icons/page.png">';
						$str .= '<div class="Buttons">';
						$str .= '<button class="Small" type="button" onclick="nudgecobj ( -1,' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/arrow_up.png"></button>';
						$str .= '<button class="Small" type="button" onclick="nudgecobj ( 1,' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/arrow_down.png"></button>';
						$str .= '<button class="Small" type="button" onclick="delcobj ( ' . $ob->ID . ', this )">';
						$str .=  '<img src="admin/gfx/icons/bin.png"></button>';
						$str .= '</div>';
						$str .= '<div class="Title">' . $obj->MenuTitle . ' (side)</div>';
						$str .= '<br style="clear: both">';
						$str .= '</div>';
						$end[] = $str;
						break;
					default:
						$obj = new dbObject ( $ob->ConnectedObjectType );
						if ( $obj->load ( $ob->ConnectedObjectID ) )
						{
							$str = '';
							$str .= '<div class="ConnectedObject">';
							$str .= '<img class="Identifier" src="admin/gfx/icons/page.png">';
							$str .= '<div class="Buttons">';
							$str .= '<button class="Small" type="button" onclick="nudgecobj ( -1,' . $ob->ID . ', this )">';
							$str .=  '<img src="admin/gfx/icons/arrow_up.png"></button>';
							$str .= '<button class="Small" type="button" onclick="nudgecobj ( 1,' . $ob->ID . ', this )">';
							$str .=  '<img src="admin/gfx/icons/arrow_down.png"></button>';
							$str .= '<button class="Small" type="button" onclick="delcobj ( ' . $ob->ID . ', this )">';
							$str .=  '<img src="admin/gfx/icons/bin.png"></button>';
							$str .= '</div>';
							$str .= '<div class="Title">' . $obj->getIdentifier ( ) . ' (' . str_replace ( 'class', '', $obj->_tableName ) . ')</div>';
							$str .= '<br style="clear: both">';
							$str .= '</div>';
							$end[] = $str;
						}
						break;
				}
			}
			$str = implode ( '<div class="SpacerSmall"></div>', $end );
		}
	}
	return $str;
}
	
?>
