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

global $Session;

$db =& dbObject::globalValue ( 'database' );

$moutput = '';
$urlfuncs = array ();

$texts = explode ( "\n", $fieldObject->DataMixed );
list ( $search_heading, $search_keywords, $search_webpage, $search_extensions, $search_replacefield, $search_outputpage ) = explode ( "\t", $texts[ 0 ] );

if ( $_REQUEST[ 'keywords' ] && !$GLOBALS[ 'search_lock' ] )
{
	$tpl = new cPTemplate ( 'lib/skeleton/modules/mod_search/templates/web_search.php' );
	
	$wheres = array ( );
	if ( $keys = explode ( ',', $_REQUEST[ 'keywords' ] ) )
	{
		foreach ( $keys as $key )
		{
			if ( $key = trim ( $key ) )
			{
				$key = mysql_real_escape_string($key);
				$wheres[] = '( Title LIKE "%' . $key . '%" OR Tags LIKE "%' . $key . '%" OR Leadin LIKE "%' . $key . '%" )';
			}
		}
	}
	
	$query = '
		SELECT * FROM
		(
	';
	$subquery = array ( );
	if ( $_REQUEST[ 'search_extension' ] )
	{
		if ( file_exists ( 'lib/skeleton/modules/' . $_REQUEST[ 'search_extension' ] . '/websearch.php' ) )
		{
			include ( 'lib/skeleton/modules/' . $_REQUEST[ 'search_extension' ] . '/websearch.php' );
		}
		else if ( file_exists ( 'extensions/' . $_REQUEST[ 'search_extension' ] . '/websearch.php' ) )
		{
			include ( 'extensions/' . $_REQUEST[ 'search_extension' ] . '/websearch.php' );
		}
		else
		{
			$subquery[] = 
				'SELECT ID, Title, MenuTitle AS Tags, ID AS `ContentElementID`, "" AS Leadin, ' .
				'"ContentElement" AS DataTable, ' .
				'"<BASE_URL><CONTENTELEMENT_PATH>/index.html" AS `Url`, ' .  
				'"lib/skeleton/modules/mod_search/templates/web_searched_page.php" AS `Template` ' . 
				'FROM ContentElement WHERE Language="' . $Session->CurrentLanguage . '" AND MainID = ID AND ( ' . str_replace ( array ( 'Tags LIKE', 'Leadin LIKE' ), 'MenuTitle LIKE', implode ( ' OR ', $wheres ) ) . ' )';
		}
	}
	else 
	{
		if ( $sets = explode ( '|', $search_extensions ) )
		{
			foreach ( $sets as $set )
			{
				if ( file_exists ( 'lib/skeleton/modules/' . $set . '/websearch.php' ) )
				{
					include ( 'lib/skeleton/modules/' . $set . '/websearch.php' );
				}
				else if ( file_exists ( 'extensions/' . $set . '/websearch.php' ) )
				{
					include ( 'extensions/' . $set . '/websearch.php' );
				}
			}
		}
		$subquery[] = 
				'SELECT ID, Title, MenuTitle AS Tags, ID AS `ContentElementID`, "" AS Leadin, ' .
				'"ContentElement" AS DataTable, ' .
				'"<BASE_URL><CONTENTELEMENT_PATH>/index.html" AS `Url`, ' .  
				'"lib/skeleton/modules/mod_search/templates/web_searched_page.php" AS `Template` ' . 
				'FROM ContentElement WHERE Language="' . $Session->CurrentLanguage . '" AND MainID = ID AND ( ' . str_replace ( array ( 'Tags LIKE', 'Leadin LIKE' ), 'MenuTitle LIKE', implode ( ' OR ', $wheres ) ) . ' )';
	}
	
	$count = count ( $subquery );
	for ( $a = 0; $a < $count; $a++ )
	{
		$query .= '(' . $subquery[ $a ] . ')';
		if ( $a < $count - 1 ) $query .= ' UNION';
	}
	$query .= '
		) z
		ORDER BY ID DESC
	';
	if ( $rows = $db->fetchObjectRows ( $query ) )
	{
		$str = '';
		$GLOBALS[ 'search_lock' ] = true;
		$loadedContent = array ();
		foreach ( $rows as $row )
		{
			$rowcontent = false;
			if ( (int)$row->ContentElementID > 0 )
			{
				if ( isset ( $loadedContent[$row->ContentElementID] ) )
				{
					$contentelementpath = $loadedContent[$row->ContentElementID];
				}
				else
				{
					$rowcontent = new dbContent ();
					$rowcontent->load ( $row->ContentElementID );
					$contentelementpath = $rowcontent->getRoute ( );
					$loadedContent[$row->ContentElementID] = $contentelementpath;
				}
			}
			else $contentelementpath = '';
			
			if ( isset ( $urlfuncs[$row->DataTable] ) )
			{
				$row->Url = $urlfuncs[$row->DataTable]($row);
			}
			else
			{
				$row->Url = str_replace ( 
					Array ( 
						'<BASE_URL>', '<CONTENT_PATH>', '<CONTENTELEMENT_PATH>', '<ID>', '<TITLE_URLIFIED>' 
					),
					Array (
						BASE_URL, $content->getRoute (), $contentelementpath, $row->ID, texttourl ( $row->Title )
					),
					$row->Url
				);
			}
			$rtpl = new cPTemplate ( $row->Template );
			$rtpl->data =& $row;
			if ( $rowcontent )
			{
				$rtpl->contentelement =& $rowcontent;
				if ( $t = $db->fetchObjectRows ( '
					SELECT ID, Name, Leadin FROM 
					(
						(
							SELECT ID, `Name`, `DataMixed` AS `Leadin` FROM ContentDataSmall s 
							WHERE 
								ContentID = \'' . $rowcontent->ID . '\' 
								AND ContentTable = "ContentElement" AND
								MainID = ID
						)
						UNION
						(
							SELECT ID, `Name`, `DataText` AS `Leadin` FROM ContentDataBig b 
							WHERE 
								ContentID = \'' . $rowcontent->ID . '\' 
								AND ContentTable = "ContentElement" AND
								MainID = ID
						)
					) z
				' ) )
				{
					foreach ( $t as $r )
					{
						if ( strlen ( $r->Leadin ) > 30 )
						{
							$this->data->Leadin = substr ( strip_tags ( $r->Leadin ), 0, 200 ) . '..';
							break;
						}
					}
				}
			}
			else $rtpl->contentelement = new stdclass();
			$str .= $rtpl->render ( );
		}
		$moutput = '
		<div class="Header">
			<h2>' . i18n ( 'Search results' ) . '</h2>
		</div>
		<div class="SearchBox Block">
			' . $str . '
			<div class="Block BackToSearch"><p><a href="' . $content->getUrl ( ) . '">' . i18n ( 'Back to search' ) . '</a></p><br/></div>
		</div>
		';
		if ( $search_replacefield != '' )
		{
			$content->{"_replacement_$search_replacefield"} = $moutput;
			$moutput = '';
		}
		else $module .= $moutput;
	}
	else
	{
		$moutput = '
		<div class="Header">
			<h2>' . i18n ( 'Search results' ) . '</h2>
		</div>
		<div class="SearchBox Block">
			<div class="Block BackToSearch"><p>' . i18n ( 'Your search produced no results.' ) . '</p><p><a href="' . $content->getUrl ( ) . '">' . i18n ( 'Back to search' ) . '</a></p></div>
		</div>
		';
		if ( $search_replacefield != '' )
		{
			$content->{"_replacement_$search_replacefield"} = $moutput;
			$moutput = '';
		}
		else $module .= $moutput;
	}
}

if ( !$moutput )
{
	$tpl = new cPTemplate ( 'lib/skeleton/modules/mod_search/templates/web_main.php' );
	if ( !$fieldObject->DataMixed )
	{
		if ( !$search_heading ) $search_heading = 'Søk';
		if ( !$search_keywords ) $search_keywords = 'Søkeord';
		if ( !$search_webpage ) $search_webpage = 'Søk i nettsiden';
	}
	$tpl->search_heading = $search_heading;
	$tpl->search_keywords = $search_keywords;
	$tpl->search_webpage = $search_webpage;
	$tpl->search_extensions = $search_extensions;
	if ( $search_outputpage > 0 )
	{
		$c = new dbContent ( );
		$c->load ( $search_outputpage );
		$tpl->content =& $c;
	}
	else
	{
		$tpl->content =& $content;
	}
	$module .= $tpl->render ( );
}

?>
