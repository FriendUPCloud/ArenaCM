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
*** module extensions
***
*** @author Hogne Titlestad <hogne@blest.no>                                   
*** @package arena-admin                                                       
*** @copyright Copyright (c) 2005,2006,2007 Blest AS                                     
***                                                                            
**/

if ( $_GET[ 'extension' ] )
	$Session->Set ( 'currentExtension', $_GET[ 'extension' ] );
else if ( !$Session->currentExtension && !$_GET[ 'extension' ])
{
	if ( $d = opendir ( 'extensions' ) )
	{
		while ( $f = readdir ( $d ) )
		{
			if ( $f[0] == '.' )
				continue;
			$Session->Set ( 'currentExtension', $f );
			break;
		}
		closedir ( $d );
		header ( 'Location: admin.php?module=extensions&extension=' . $Session->currentExtension );
		die ( );
	}
}	
			
$module = new cPTemplate ( "$tplDir/main.php" );

$extlist = 'Ingen ekstensjoner funnet.';
			
if ( file_exists ( 'extensions' ) && is_dir ( 'extensions' ) )
{
	/**
	* List the extensions
	**/
	if ( $dp = opendir ( 'extensions' ) )
	{
		$extlist = '';
		$out = array ( );
		while ( $file = readdir ( $dp ) )
		{
			if ( 
				$file[0] != '.' &&
				file_exists ( "extensions/$file/info.csv" )	
			)
			{
				$info = file_get_contents ( "extensions/$file/info.csv" );
				if ( trim ( $name ) )
				{
					list ( $name, $priority ) = explode ( '|', $info );
					if ( !(int)$priority ) $priority = 20;
					$out[] = str_pad ( trim ( $priority ), 4, '0', STR_PAD_LEFT ) . '___' . trim ( $file );
				}
			}
		}
		if ( count ( $out ) )
		{
			arsort ( $out );
			foreach ( $out as $k=>$v )
			{
				if ( trim ( $v ) )
				{
					list ( , $out[ $k ] ) = explode ( '___', $v );
				}
			}
			$out = array_reverse ( $out );
		}
		
		if ( count ( $out ) >= 5 )
		{
			sort ( $out );
			$count = 0;
			foreach ( $out as $o )
			{
				$sw = $sw == 1 ? 2 : 1;
				if ( file_exists ( "extensions/$o/info.csv" ) && filesize ( "extensions/$o/info.csv" ) > 0 )
				{
					if ( $fp = fopen ( "extensions/$o/info.csv", 'r' ) )
					{
						list ( $Realname, ) = explode ( ',', fread ( $fp, filesize ( "extensions/$o/info.csv" ) ) );
						fclose ( $fp );
					}
				}
				$count++;
				if ( file_exists ( 'extensions/' . $o . '/extension.png' ) )
					$img = '<img src="extensions/' . $o . '/extension.png" alt="extension_' . $o . '"> ';
				else 
					$img = '';
				$extlist .= "<div class=\"tab" . $o . "\" onclick=\"document.location='admin.php?extension=$o'\">$img$Realname</div>";
			}
			$module->extensionList = $extlist;
		}
		closedir ( $dp );
	}
	
	/** 
	 * Get the desired extension
	**/
	$ext = $Session->currentExtension ? $Session->currentExtension : $out[ 0 ];
	
	/** 
	 * Only allow to use extensions that are made to be hidden if we have a function or action 
	**/
	if ( !$_REQUEST[ 'function' ] && !$_REQUEST[ 'action' ] && !file_exists ( 'extensions/' . $ext . '/info.csv' ) ) 
		$ext = $out[ 0 ];
	
	/**
	 * Include and execute
	**/
	$fn = "extensions/$ext/extension.php";
	if ( file_exists ( $fn ) && !is_dir ( $fn )	)
	{
		$extension = '';
		include ( $fn );
		if ( is_object ( $module ) )
			$module->content = $extension;
		else
		{
			$tmp = $module;
			$module = new cPTemplate ( );
			$module->_template =& $tmp;
		}
	}
	
	/**
	 * Set active menu
	**/
	if ( count ( $out ) )
	{
		foreach ( $out as $o )
		{
			if ( $o == $ext )
			{
				$module->extensionList = str_replace ( '"tab' . $o . '"', '"tabActive"', $module->extensionList );
			}
			else
			{
				$module->extensionList = str_replace ( '"tab' . $o . '"', '"tab"', $module->extensionList );
			}
		}
	}
}
?>
