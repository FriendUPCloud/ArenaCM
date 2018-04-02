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



class XmlParser 
{	
	var $baseUrl = '';
	
	function __construct ( $baseUrl = '' )
	{
		$this->baseUrl = $baseUrl;
		if ( !$this->baseUrl && defined ( 'BASE_URL' ) )
			$this->baseUrl = BASE_URL;
	}
	
	private function xmlToObjects ( $xml, &$parent )
	{
		$found = 0;
		$objects = array ();
		do
		{
			$obj = new dummy ( );
			list ( $nodeName, ) = explode ( '>', $xml );
			list ( $nodeName, ) = explode ( ' ', $nodeName );
			list ( ,$nodeName ) = explode ( '<', $nodeName );
			if ( !preg_match ( '/\<' . $nodeName . '[^>]*\>[\w\W]*?\<\/' . $nodeName . '\>/i', $xml, $matches ) )
				return;
			$obj->nodeName = $nodeName;
			$xml = trim ( str_replace ( $matches[0], '', $xml ) );
			$line = $matches[0];
			preg_match ( '/\<' . $nodeName . '([^>]*?)\>/i', $line, $attributes );
			if ( $attributes = trim ( $attributes[1] ) )
			{
				if ( preg_match_all ( '/([a-aA-Z0-9]*?\=\"[^"]*?\")/i', $attributes, $attrs ) )
				{
					$attributes = $attrs[1];
					foreach ( $attributes as $a )
					{
						$k = explode ( "=", $a );
						$obj->$k[0] = $k[1];
					}
					$found++;
				}
			}
			if ( preg_match_all ( '/\<' . $nodeName . '[^>]*\>([\w\W]*?)\<\/' . $nodeName . '\>/i', $line, $matches ) )
			{
				$output = array ( );
				foreach ( $matches[1] as $match )
				{
					$output[] = $this->xmlToObjects ( $match, &$obj );
				}
				if ( count ( $output ) <= 1 && trim ( $match ) && !preg_match ( '/<[a-zA-Z]*?[^>]*?>/i', $match ) )
				{
					
					$obj->_ignore = true;
					$parent->{$nodeName} = trim ( $match );
				}
				else
				{
					if ( count ( $output ) > 1 || is_object ( $output[ 0 ] ) || trim ( $output[0] ) )
						$obj->{$nodeName} = count ( $output ) > 1 ? $output : $output[0];
				}
				$found++;
			}				
			if ( !$obj->_ignore )
				$objects[] = $obj;
		}
		while ( strstr ( $xml, '<' ) );
		if ( !$found )
		{
			return $xml;
		}
		return count ( $objects ) > 1 ? $objects : $objects[0];
	}
	
	private function utf8 ( $str )
	{
		return @mb_convert_encoding ( $str, 'utf-8', $this->_encoding );
	}
	
	private function parseXMLNodes ( $xml, $nodenamelist = '' )
	{
		$xml = $this->utf8 ( $xml );
		
		// Find our node groups
		$o = new dummy ( );
		$encoding = preg_match ( '/<\?.*encoding=\"(.*?)\"/i', $xml, $matches );
		$this->_encoding = strtolower ( trim ( $matches[ 0 ] ) );
		$xml = trim ( preg_replace ( '/\<\?[^?]*?\?>/i', '', $xml ) );
		if ( !$nodenamelist )
			$nodenamelist = array ( );
		$nodenamelist = array_reverse ( $nodenamelist );
		if ( $node = array_pop ( $nodenamelist ) )
		{
			$this->_node = $node;
			$nodenamelist = array_reverse ( $nodenamelist );
			$children = false;
			if ( preg_match_all ( '/\<' . $node . '[^>]*\>([\w\W]*?)\<\/' . $node . '\>/i', $xml, $matches ) )
			{
				$str = '';
				foreach ( $matches[1] as $match )
				{
					$str .= $match;
				}
				$children = $this->parseXMLNodes ( $nodenamelist, $str );
			}	
			return $children;
		}
		// Turn xml into objects as we now have found our node group
		else if ( trim ( $xml ) )
		{
			if ( !$this->_node ) $this->_node = 'xml';
			$obj->{$this->_node} = $this->xmlToObjects ( $xml, &$obj );	
		}
		return $obj;
	}
	
	public function getXML ( $str )
	{
		return file_get_contents ( $this->baseUrl . $str );
	} 
	
	public function getXMLNodes ( $str )
	{
		return $this->parseXMLNodes ( $this->getXML ( $str ) );
	}
}

?>
