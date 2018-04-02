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



include_once ( 'lib/plugins/extrafields/include/funcs.php' );
include_once ( 'lib/classes/dbObjects/dbContent.php' );

$db = &dbObject::globalValue ( 'database' );
	
$plugin = '';

$contentid = $options[ 'ContentID' ];
$contenttype = $options[ 'ContentType' ];

if ( $rows = $db->fetchObjectRows ( "
	SELECT z.* FROM 
	(
		(
			SELECT b.ID, b.IsVisible, b.Name, b.Type, b.SortOrder, 'Big' AS `DataTable`  
			FROM 
			`ContentDataBig` b 
			WHERE 
			b.ContentID='$contentid' 
			AND
			b.ContentTable=\"$contenttype\"
		)
		UNION
		(
			SELECT c.ID, c.IsVisible, c.Name, c.Type, c.SortOrder, 'Small' AS `DataTable` 
			FROM 
			`ContentDataSmall` c 
			WHERE 
			c.ContentID='$contentid' 
			AND
			c.ContentTable=\"$contenttype\"
		)
	) AS z
	ORDER BY z.SortOrder ASC, z.ID ASC
", MYSQL_ASSOC ) ) 
{
	$content = new dbContent ( );
	$content->load ( $contentid );
	$content->loadSubElements ( );
	foreach ( $rows as $row )
	{
		if ( $GLOBALS[ 'user' ]->_dataSource != 'core' && $row->IsVisible != '1' ) continue;
		switch ( $row->Type )
		{
			case 'varchar':
				$obj = new dbObject ( "ContentData{$row->DataTable}" );
				$obj->load ( $row->ID );
				$tpl = new cPTemplate ( 'lib/plugins/extrafields/templates/editfield_varchar.php' );
				$tpl->data =& $obj;
				$obj->DataString = str_replace ( array ( "\"", "\n", "\r" ), Array ( '&quot;', '', '' ), $obj->DataString );
				break;
			case 'extension':
				$obj = new dbObject ( "ContentData{$row->DataTable}" );
				$obj->load ( $row->ID );
				$tpl = new cPTemplate ( 'lib/plugins/extrafields/templates/editfield_extension.php' );
				$page = new dbContent ( );
				$page->load ( $obj->DataInt ? $obj->DataInt : $contentid );
				$tpl->page =& $page;
				$tpl->data =& $obj;
				break;
			case 'pagelisting':
				$obj = new dbObject ( "ContentData{$row->DataTable}" );
				$obj->load ( $row->ID );
				$page = new dbContent ( );
				$page->load ( $obj->DataInt ? $obj->DataInt : $contentid );
				$page->loadSubElements ( );
				$tpl = new cPTemplate ( 'lib/plugins/extrafields/templates/editfield_pagelisting.php' );
				$tpl->subs =& $page->subElements;
				$tpl->data =& $obj;
				$tpl->page =& $page;
				$tpl->name = $row->Name;
				break;
			case "script":
				$tpl = new cPTemplate ( 'lib/plugins/extrafields/templates/editfield_script.php' );
				$obj = new dbObject ( "ContentData" . $row->DataTable );
				$obj->load ( $row->ID );
				$tpl->data = $obj;
				$tpl->data->DataTable = $row->DataTable;
				break;
			case 'style':
				$tpl = new cPTemplate ( 'lib/plugins/extrafields/templates/editfield_style.php' );
				$obj = new dbObject ( "ContentData" . $row->DataTable );
				$obj->load ( $row->ID );
				$tpl->data = $obj;
				$tpl->data->DataTable = $row->DataTable;
				break;
			case 'image':
				$tpl = new cPTemplate ( 'lib/plugins/extrafields/templates/editfield_image.php' );
				$obj = new dbObject ( "ContentData" . $row->DataTable );
				$obj->load ( $row->ID );
				$tpl->data = $obj;
				$tpl->data->DataTable = $row->DataTable;
				break;
			case "file":
				$tpl = new cPTemplate ( "lib/plugins/extrafields/templates/editfield_file.php" );
				$obj = new dbObject ( "ContentData" . $row->DataTable );
				$obj->load ( $row->ID );
				$tpl->data = $obj;
				$tpl->data->DataTable = $row->DataTable;
				break;
			case 'newscategory':
				$o = new dbObject ( "ContentData{$row->DataTable}" );
				$o->load ( $row->ID );
				list ( $aff, $rev, ) = explode ( '|', $o->DataMixed );
				$obj = new dbObject ( 'News' );
				$obj->addClause ( 'WHERE', "CategoryID='{$o->DataInt}'" );
				if ( $rev ) $obj->addClause ( 'ORDER BY', 'DateActual ASC, ID ASC' );
				else $obj->addClause ( 'ORDER BY', 'DateActual DESC, ID DESC' );
				$objs = $obj->find ( );
				$tpl = new cPTemplate ( 'lib/plugins/extrafields/templates/editfield_newscategory.php' );
				$tpl->news =& $objs;
				$tpl->data =& $o;
				$tpl->name = $row->Name;
				break;
			case 'formprocessor':
				$tpl = new cPTemplate ( 'lib/plugins/extrafields/templates/editfield_formprocessor.php' );
				$obj = new dbObject ( "ContentData" . $row->DataTable );
				$obj->load ( $row->ID );
				$tpl->data = $obj;
				$tpl->data->DataTable = $row->DataTable;
				break;
			case 'objectconnection':
				$tpl = new cPTemplate ( 'lib/plugins/extrafields/templates/editfield_objectconnection.php' );
				$obj = new dbObject ( 'ContentData' . $row->DataTable );
				$obj->load ( $row->ID );
				$tpl->data = $obj;
				$tpl->data->DataTable = $row->DataTable;
				break;
			case 'leadin':
				$tpl = new cPTemplate ( 'lib/plugins/extrafields/templates/editfield_leadin.php' );
				$obj = new dbObject ( 'ContentData' . $row->DataTable );
				$obj->load ( $row->ID );
				$tpl->data = $obj;
				$tpl->data->DataTable = $row->DataTable;
				break;
			default:
				$tpl = new cPTemplate ( 'lib/plugins/extrafields/templates/editfield_text.php' );
				$obj = new dbObject ( 'ContentData' . $row->DataTable );
				$obj->load ( $row->ID );
				$tpl->data = $obj;
				$tpl->data->DataTable = $row->DataTable;
				break;
		}
		// All extrafields must be able to access the $this->page or $this->content
		$tpl->page =& $content;
		$tpl->content =& $content;
		$plugin .= $tpl->render ( );
	}
}
?>
