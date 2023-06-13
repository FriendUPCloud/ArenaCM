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



/**                                                                            
 * PHP Templates
 *
 *  @author Inge Jorgensen <inge@blest.no>
 *  @author Hogne Titlestad <hogne@blest.no>
 *  @package arena-lib
 *  @copyright Copyright (c) 2005-2009 Blest AS                                     
 *                                                                             
**/

class cPTemplate
{
	var $_template;
	var $_replaceStart;
	var $_replaceEnd;
	var $_includedFiles = array ();
	var $_isLoaded      = false;
	var $_templateFilename = '';
	var $_isDocument = false;
	var $_isAdmin = false;
	var $_bodyClasses = array ();
	
	/**
	* @param string $file Template file
	*/
	function __construct ( $file = false )
	{
		if ( $file ) $this->load ( $file );
		$this->_replaceStart = '!';
		$this->_replaceEnd   = '!';
	}
	
	function createSelect( $values, $labels=false, $defaultvalue=false )
	{
		$labels = ( $labels ) ? $labels : $values;
		
		$values = preg_split( '/[\s]*,[\s]*/', $values );
		$labels = preg_split( '/[\s]*,[\s]*/', $labels );
		
		$vcount = count ( $values );
		for ( $i = 0; $i < $vcount; $i++ )
		{
			$value = $values[$i];
			$label = ($labels[$i]) ? $labels[$i] : $value;
			$selected = ( $value == $defaultvalue ) ? ' selected="selected"' : '';
			$output .= '<option value="' . $value . '"' . $selected . '>' . $label . '</option>' . NL;
		}
		return $output;
	}
	
	function getTemplateFile ( $filename )
	{
		global $templateDirs;
		
		if ( !$filename ) return false;
		
		if ( is_array ( $templateDirs ) )
		{
			foreach ( $templateDirs as $dir )
			{
				$path = BASE_DIR . '/' . $dir;
				if ( file_exists ( $path . '/' . $filename ) )
					return $path . '/' . $filename;
				if ( file_exists ( $path ) && is_dir ( $path ) )
					if ( $result = cPTemplate::findFile ( $filename, $path ) )
						return $result;
			}
		}
		
		// Extract filename from filestring with path!
		$name = $filename;
		$filename = explode ( '/', $filename );
		if ( count ( $filename ) > 1 )
		{
			$filename = $filename[ count ( $filename ) - 1 ];
		}
		else $filename = $name;

		// Get to work!
		// Try usual dirs
		$path = BASE_DIR . '/templates';
		
		if ( $dir = opendir ( $path ) )
		{
			while ( $file = readdir ( $dir ) )
			{
				if ( is_dir ( $path . '/' . $file ) && $file[0] != '.' ) 
				{
					if ( $result = cPTemplate::findFile ( $filename, $path . '/' . $file ) )
					{
						closedir ( $dir );
						return $result;
					}
				}
				else if ( $file == $filename )
				{
					closedir ( $dir );
					return $path . '/' . $file;
				}
			}
			closedir ( $dir );
		}
		return false;
	}
	
	/* Recursively try to find a file in a folder hierarchy */
	function findFile ( $filename, $path )
	{
		if ( $dir = opendir ( $path ) )
		{
			while ( $file = readdir ( $dir ) )
			{
				if ( is_dir ( $path . '/' . $file ) && $file[0] != '.' )
				{
					if ( $result = cPTemplate::findFile ( $filename, $path . '/' . $file ) )
					{
						closedir ( $dir );
						return $result;
					}
				}
				else if ( $file == $filename )
				{
					closedir ( $dir );
					return $path . '/' . $file;	
				}
			}
			closedir ( $dir );
		}
		return false;
	}
	
	function getIncludeFile ( $filename )
	{
		global $includeDirs;
		if ( !$filename ) return false;
		
		if ( is_array ( $includeDirs ) )
		{
			foreach ( $includeDirs as $dir )
			{
				$file = BASE_DIR . '/' . $dir . '/' . $filename;
				if ( file_exists ( $file ) ) return $file;
			}
		}
		return false;
	}
	
	/**
	*** Find a template file
	*** @param $filename string
	*** @param $dir optional
	*** @return filename with path
	**/
	function findTemplate ( $filename, $dir = '' )
	{
		$dirs = Array ( 'templates/', 'web/templates/' );
		
		if ( $dir )
		{
			if ( is_array ( $dir ) )
			{
				foreach ( $dir as $d )
				{
					if ( is_dir ( BASE_DIR . '/' . $d ) && file_exists ( BASE_DIR . '/' . $d ) )
						$dirs = array_merge ( $dirs, Array ( $d ) );
				}
			}
			else
			{
				if ( substr ( $dir, 0, strlen ( $dir ) - 1 ) != '/' )
					$dir .= '/';
				if ( is_dir ( BASE_DIR . '/' . $dir ) && file_exists ( BASE_DIR . '/' . $dir ) )
					$dirs = array_merge ( $dirs, Array ( $dir ) );
			}
		}
		else $dirs = Array ( 'templates/', 'web/templates/' );
		
		for ( $a = 0; $a < count ( $dirs ); $a++ )
		{
			for ( $b = 0; $b < 2; $b++ )
			{
				if ( file_exists ( $dirs[ $a ] ) && $dir = opendir ( $dirs[ $a ] ) )
				{
					while ( $file = readdir ( $dir ) )
					{
						if ( $b == 0 && $file[0] != '.' && !is_dir ( $file ) )
						{
							if ( $file == $filename )
								return $dirs[ $a ] . $file;
						}
						else if ( $b == 1 && $file[0] != '.' && is_dir ( $file ) )
						{
							if ( $result = $this->findTemplate ( $filename, $dirs[ 0 ] . $file ) )
								return $result;
						}
					}
				}
			}
		}
		return false;
	}
	
	/** 
	 * Add body class on template
	**/
	
	function addBodyClass ( $className )
	{
		if ( !in_array ( $className, $this->_bodyClasses ) )
			$this->_bodyClasses[] = $className;
	}
	
	/**
	* Load a template file
	* @param string $file filename
	*/
	function load ( $file )
	{
		if ( file_exists ( $file ) )
		{
			$this->_templateFilename = $file;
			$this->_template = file_get_contents ( $file );
			$this->_isLoaded = true;
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	* Parse PHP code
	* @private
	* @param string $code code to parse
	* @return string parsed code
	*/
	function parse_php_code ( $code )
	{  
		$code = stripslashes ( $code );
		if ( $code && trim( $code ) )
		{
			return eval ( $code );
		}
		else return '';
	}

	function parse_condition_block ( $condition, $block )
	{
		$condition = stripslashes ( $condition );
		if ( $condition && eval ( 'return ( ' . $condition . ' );' ) )
		{
			return stripslashes ( $block );
		}
		else return '';
	}

	function addData ( $data )
	{
		if ( is_object ( $data ) )
		{
			$className = strtolower ( get_class ( $data ) );
			if ( $className == 'cptemplate' || $className = 'ctemplate' )
				$data = $data->render ();
		}
		$this->_template .= $data;
	}

	/**
	* Render template
	* @return string rendered template
	*/
	function render ( )
	{
		global $document;
		
		$output = $this->_template;
		
		// Template preprocessor
		$preprocessor_pattern = 
			"/^[\s]*".           // Attach to beginning of file, disregard whitespace
			"@([\w-_]+)[ \t]+".  // Directive is a word, prefixed with @
			"([\W\w]*)".         // Accept any kind of character as params
			"[\n]+/U";           // Terminate by line break
		
		while ( preg_match ( $preprocessor_pattern, $output, $matches ) )
		{
			// Remove the line from the output
			$output	= trim ( preg_replace ( 
				$preprocessor_pattern, '', $output 
			) );
			
			$directive = $matches [1];
			if ( $matches[2] )
			{
				$params    = eval ( "return ( {$matches[2]} );" );
			}
			switch ( $directive )
			{
				case 'template':
					if ( $params && $file = $this->getTemplateFile ( $params ) )
					{
						$wraps[] = file_get_contents ( $file );
					}
					break;
				
				case 'include':
					if ( $params && $file = $this->getIncludeFile ( $params )  )
					{
						if ( !in_array ( $file, $this->_includedFiles ) )
						{
							include_once ( $file );
							$this->_includedFiles[] = $file;
						}
					}
					break;
				
				case 'title':
					if ( !$titleSet && $document )
					{
						$document->setTitle ( $params );
						$titleSet = true;
					}
					break;
				
				case 'stylesheet':
					if ( $document )
						$document->addRel ( 'stylesheet', $params );
					break;

				case 'script':
					if ( $document )
						$document->addHeadScript ( $params );
					break;
				
				case 'no-cache':
					if ( $document )
						$document->cache = false;
					break;
			
			}
			if ( is_array ( $wraps ) ) foreach ( $wraps as $wrap )
			{
				if ( strstr ( $wrap, '@data' ) )
					$output = str_replace ( '@data', $output, $wrap );
				else
					$output = $wrap . $output;
			}
			unset ( $wraps, $wrap );			
		}
	
		// Condition blocks
		$preg = "/<\?if([\W\w]*)[\s]*{[\s]*\?>([\W\w]*)<\?[\s]*}[\s]*\?>/U";
		while ( preg_match ( $preg, $output ) )
		{
			// TODO: Remove this, Deprecated in PHP7
			/*$output = preg_replace ( "{$preg}e", "\$this->parse_condition_block ('\\1', '\\2')", $output );*/
			$output = preg_replace_callback ( "{$preg}", function( $matches ){ return $this->parse_condition_block ( $matches[1], $matches[2] ); }, $output );
		}
		
		// <?php to <?
		$output = str_replace ( '<?php', '<?', $output );
		
		// = $var to return $var
		$output = preg_replace ( "/<\?=([\w\W]*?)\?>/", "<? return\\1; ?>" ,$output );
		
		// Parse PHP code
		// TODO: Remove this, Deprecated in PHP7
		/*$output = preg_replace ( "/<\?([\w\W]*?)\?>/e", "\$this->parse_php_code('\\1')" ,$output );*/
		$output = preg_replace_callback ( "/<\?([\w\W]*?)\?>/", function( $matches ){ return $this->parse_php_code( $matches[1] ); }, $output );
		
		// Locale support
		$ar = explode ( '/', $this->_templateFilename );
		if ( $ar[ 0 ] == 'admin' )
		{
			$nm = Array ();
			$arcount = count ( $ar );
			for ( $a = 1; $a < $arcount; $a++ )
				$nm[] = $ar[  $a ];
			$ar = $nm;
		}
		
		$nm = implode ( '/', $ar ); $nm = BASE_DIR . '/extensions/locales/' . LOCALE . '/' . $nm;
		if ( file_exists ( $nm ) && !is_dir ( $nm ) )
			$output = i18n::translate_with_file ( $output, $nm );
		
		// Match body
		if ( preg_match ( '/\<body.*?class\=\"([^"]*?)\"/i', $output, $m ) )
		{
			$classes = explode ( ' ', $m[1] );
			if ( isset( $this->_bodyClasses ) && $this->_bodyClasses )
			{
				$bdclass = ( is_array( $this->_bodyClasses ) ? $this->_bodyClasses : explode( ' ', $this->_bodyClasses ) );
				$classes = array_merge ( $classes, $bdclass );
			}
			$classes = implode ( ' ', $classes );
			$output = str_replace ( '!CLASSES!', $classes, preg_replace ( '/(\<body.*?class\=\")[^"]*?(\")/i', '$1!CLASSES!$2', $output ) );
		}
		
		return $output;
	}
	
	/**
	* @param dbObject $object object
	* @return string rendered template
	*/
	function renderObject ( $obj )
	{
		if ( $obj->Template )
		{
			if ( $path = $this->getTemplateFile ( $obj->Template ) )
			{
				$tpl = new cPTemplate ( $path );
				$tpl->object = $obj;
				return $tpl->render ( );
			}
		}
		return false;
	}
}
