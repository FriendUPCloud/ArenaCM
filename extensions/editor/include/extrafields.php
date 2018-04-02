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

// Add the extrafields script
$document->addResource ( 'javascript', 'lib/plugins/extrafields/javascript/main.js' );

function renderExtraFields ( $obj )
{
	global $extdir;
	$db =& dbObject::globalValue ( 'database' );
	if ( !( $groups = explode ( ',', $obj->ContentGroups ) ) )
		$groups = array ( 'Default' );
	$modstr_ = '';
	if ( $obj && $obj->loadExtraFields ( ) )
	{
		$spacerHidden = false;
		foreach ( array ( 'visible', 'hidden' ) as $mode )
		{
			foreach ( $groups as $group )
			{
				$group = trim ( $group );
				foreach ( $obj as $k=>$v )
				{
					$tpl = new cPTemplate ( );
					if ( substr ( $k, 0, 7 ) == '_extra_' )
					{
						$field =& $obj->{'_field_' . substr ( $k, 7, strlen ( $k ) - 7 )};
						
						if ( $field->ContentID != $obj->ID ) 
							continue;
						if ( $field->ContentGroup != $group ) 
							continue;
						
						$field->_NotOrphan = true; // this one is not orphan
							
						if ( $field->AdminVisibility && $mode == 'visible' )
						{
							switch ( $field->Type )
							{
								case 'contentmodule':
									$tpl->load ( $extdir . '/templates/ext/contentmodule.php' );
									if ( file_exists ( 'lib/skeleton/modules/' . $field->DataString . '/adminmodule.php' ) )
									{
										$content =& $obj;
										$fieldObject = new dbObject ( "ContentData{$field->DataTable}" );
										if ( $fieldObject->load ( $field->ID ) )
										{
											$dataTable = $field->DataTable;
											$field =& $fieldObject;
											$field->DataTable = $dataTable;
										}
										require ( 'lib/skeleton/modules/' . $field->DataString . '/adminmodule.php' );
										$tpl->gui = $module;
										$dataField = 'DataString';
									}
									else $tpl->gui = '<p>Modulen finnes ikke. Vennligst kontakt Blest Interaktiv for mer informasjon.</p>';
									break;
								case 'text':
									$tpl->load ( $extdir . '/templates/ext/text.php' );
									$dataField = 'DataText';
									break;
								case 'script':
									$tpl->load ( $extdir . '/templates/ext/javascript.php' );
									$dataField = 'DataMixed';
									break;
								case 'style':
									$tpl->load ( $extdir . '/templates/ext/stylesheet.php' );
									$dataField = 'DataString';
									break;
								case 'image':
									$tpl->load ( $extdir . '/templates/ext/image.php' );
									$dataField = 'DataInt';
									break;
								case 'leadin':
									$tpl->load ( $extdir . '/templates/ext/leadin.php' );
									$dataField = 'DataText';
									break;
								case 'newscategory':
									$tpl->load ( $extdir . '/templates/ext/newscategory.php' );
									$dataField = 'DataInt';
									break;
								case 'objectconnection':
									$tpl->load ( $extdir . '/templates/ext/objectconnection.php' );
									$dataField = 'DataInt';
									break;
								case 'varchar':
									$tpl->load ( $extdir . '/templates/ext/varchar.php' );
									$dataField = 'DataString';
									break;
								// TODO: Let extensions work
								/*case 'extension':
									$tpl->load ( $extdir . '/templates/ext/extension.php' );
									if ( file_exists ( 'extensions/' . $field->DataString . '/templates/websnippetconfig.php' ) )
									{
										$cnt = new dbContent ( $field->ContentID );
										$tpl->extension = new cPTemplate ( 'extensions/' . $field->DataString . '/templates/websnippetconfig.php' );
										$tpl->extension->page =& $cnt;
										$tpl->extension->content =& $cnt;
										$tpl->extension->data =& $field;
										$tpl->extension = $tpl->extension->render ( );
									}
									else $tpl->extension = '<div class="Container">Denne utvidelsen har ingen innstillinger.</div>';
									break;*/
								case 'pagelisting':
									$tpl = new cPTemplate ( "$extdir/templates/ext/pagelisting.php" );
									$dataField = 'DataInt';
									break;
								case 'extension':
									$tpl = new cPTemplate ( "$extdir/templates/ext/extension.php" );
									$extension = '';
									if ( file_exists ( 'extensions/' . $field->DataString . '/adminmodule.php' ) )
									{
										$fieldObject = new dbObject ( "ContentData{$field->DataTable}" );
										$fieldObject->load ( $field->ID );
										include ( 'extensions/' . $field->DataString . '/adminmodule.php' );
									}
									else if ( file_exists ( 'extensions/' . $field->DataString . '/templates/websnippetconfig.php' ) )
									{
										$extension = new cPTemplate ( 'extensions/' . $field->DataString . '/templates/websnippetconfig.php' );
										$extension->data =& $field;
										$extension->content =& $obj;
										$extension = '<div class="Container" style="padding: 4px">' . $extension->render ( ) .'</div>';
									}
									else $extension .= '<p>Denne utvidelsen har ingen innstillinger.</p>';
									$tpl->extension =& $extension;
									$dataField = 'DataMixed';
									break;
								default:
									$tpl->load ( $extdir . '/templates/ext/unknown.php' );
									$dataField = 'DataString';
									break;
							}
						}
						else if ( !$field->AdminVisibility && $mode == 'hidden' )
						{
							if ( !$spacerHidden )
							{
								$spacerHidden = true;
								$modstr_ .= '<div class="SpacerSmallColored"></div>';
							}
							$tpl->load ( $extdir . '/templates/ext/hidden.php' );
						}
					}
					if ( !$tpl->_template ) continue;
					
					$tabletype = $field->DataTable;
					$tpl->fieldType = 'ContentData' . $field->DataTable;
					$tpl->field =& $field;
					$tpl->content =& $obj;
					$tpl->fieldID = "Extra_{$field->ID}_{$tabletype}_{$dataField}";
					$tpl->fieldGroup = $field->ContentGroup;
					$tpl->top = '
						<h4>
							<div class="Buttons">
								<button title="Endre feltet" type="button" onclick="editEditorField ( \'' . $field->ID . '\', \'' . $tpl->fieldType . '\' )"><img src="admin/gfx/smallbutton_edit.png"></button>
								<button title="Fjern feltet" type="button" onclick="removeField ( \'' . $field->ID . '\', \'' . $tpl->fieldType . '\' )"><img src="admin/gfx/smallbutton_remove.png"></button>
								<button title="Flytt opp" type="button" onclick="reorderField ( \'' . $field->ID . '\', \'' . $tpl->fieldType . '\', -1 )"><img src="admin/gfx/smallbutton_up.png"></button>
								<button title="Flytt ned" type="button" onclick="reorderField ( \'' . $field->ID . '\', \'' . $tpl->fieldType . '\', 1 )"><img src="admin/gfx/smallbutton_down.png"></button>
							</div>
							<a onclick="javascript: scrollTo ( 0, getElementTop ( this ) );">' . str_replace ( '_', ' ', $field->Name ) . ' (' . i18n ( 'in' ) . ' ' . $tpl->fieldGroup . '):</a>
						</h4>
					';
					$modstr_ .= '<div class="ExtraFieldDiv">' . $tpl->render ( ) . '</div>' . 
									'<div class="SpacerSmall"></div>';
				}
			}
		}
	}
	// Show orphan fields that has no valid content group
	if ( $groups )
	{
		$cgroups = '';
		foreach ( $groups as $cg ) $cgroups .= '"' . trim ( $cg ) . '",';
		while ( substr ( $cgroups, -1, 1 ) == ',' )
			$cgroups = substr ( $cgroups, 0, strlen ( $cgroups ) -1 );
	}
	else $cgroups = '';
	if ( $rows = $db->fetchObjectRows ( '
		SELECT * FROM 
		(
			(
				SELECT ID, `Name`, ContentGroup, SortOrder, `Type`, "ContentDataSmall" AS `Table` 
				FROM 
				ContentDataSmall 
				WHERE 
					ContentGroup NOT IN (' . $cgroups . ') AND
					ContentID=\'' . $obj->ID . '\' AND
					ContentTable=\'ContentElement\'
			)
			UNION
			(
				SELECT ID, `Name`, ContentGroup, SortOrder, `Type`, "ContentDataBig" AS `Table` 
				FROM 
				ContentDataBig 
				WHERE 
					ContentGroup NOT IN (' . $cgroups . ') AND
					ContentID=\'' . $obj->ID . '\' AND
					ContentTable=\'ContentElement\'
			)
		) as z
	' ) )
	{
		$modstr_ .= '<div class="SpacerSmallColored"></div>';
		$otpl = new cTemplate ( $extdir . '/templates/ext/orphan.php' );
		foreach ( $rows as $row )
		{
			$fieldType = $row->Table;
			$fieldGroup = $row->ContentGroup;
			$otpl->top = '
						<h4>
							<div class="Buttons">
								<button title="Endre feltet" type="button" onclick="editEditorField ( \'' . $row->ID . '\', \'' . $fieldType . '\' )"><img src="admin/gfx/smallbutton_edit.png"></button>
								<button title="Fjern feltet" type="button" onclick="removeField ( \'' . $field->ID . '\', \'' . $fieldType . '\' )"><img src="admin/gfx/smallbutton_remove.png"></button>
								<button title="Flytt opp" type="button" onclick="reorderField ( \'' . $field->ID . '\', \'' . $fieldType . '\', -1 )"><img src="admin/gfx/smallbutton_up.png"></button>
								<button title="Flytt ned" type="button" onclick="reorderField ( \'' . $field->ID . '\', \'' . $fieldType . '\', 1 )"><img src="admin/gfx/smallbutton_down.png"></button>
							</div>
							<a onclick="javascript: scrollTo ( 0, getElementTop ( this ) );">' . str_replace ( '_', ' ', $row->Name ) . ' (' . i18n ( 'in' ) . ' ' . $fieldGroup . '):</a>
						</h4>
			';
			$modstr_ .= $otpl->render ();
		}
		$modstr_ .= '<div class="SpacerSmall"></div>';
	}
	if ( !$modstr_ )
	{
		return '<p>Intet innhold er finnes på denne siden.</p>';
	}
	return $modstr_;
}
?>
