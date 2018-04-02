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



class Session 
{
	var $CurrentLanguage;
	var $LanguageCode;
	var $BaseUrl;
	var $WebUsername;
	var $WebPassword;
	var $HasUrlActivator = false;
	var $_prefix;
	
	function __construct ( $id = false )
	{
		global $siteData;
		if ( !$id ) $id = isset( $siteData->ID ) ? $siteData->ID : null;
		$this->_prefix = $id;
		
		$GLOBALS[ 'Session' ] =& $this;
		
		foreach ( $_SESSION as $k=>$v )
		{
			if ( substr ( $k, 0, strlen ( $id ) ) == $id )
			{
				$key = str_replace ( $id, '', $k );
				$this->$key =& $_SESSION[ $k ];
			}
		}
		// Unique id we can use across the session
		if ( isset( $_SESSION[ $this->_prefix .'_uniqueid' ] ) && !$_SESSION[ $this->_prefix .'_uniqueid' ] )
			$_SESSION[ $this->_prefix .'_uniqueid' ] = md5 ( rand ( 0, 9999 ) . rand ( 0, 9999 )  . microtime () );
	}
	
	function GetUniqueID ( )
	{
		return $_SESSION[ $this->_prefix .'_uniqueid' ];
	}
	
	function Clear ( )
	{
		foreach ( $_SESSION as $k=>$v )
		{
			if ( substr ( $k, 0, strlen ( $this->_prefix ) ) == $this->_prefix )
			{
				$key = str_replace ( $this->_prefix, '', $k );
				unset ( $this->$key, $_SESSION[ $k ] );
			}
		}
	}
	
	/**
	 * Yes set is a duplicate of add..
	**/
	function Set ( $name, $value )
	{
		$_SESSION[ $this->_prefix . $name ] = $value;
		$this->$name =& $_SESSION[ $this->_prefix . $name ];
	}
	
	function Add ( $name, $value )
	{
		$this->Set ( $name, $value );
	}
	
	function Del ( $name )
	{
		unset ( $_SESSION[ $this->_prefix . $name ] );
		unset ( $this->$name );
	}
	
	function &Get ( $key )
	{
		$keyset = isset ( $this->$key );
		if ( !isset ( $key ) )
			return $GLOBALS[ 'Session' ];
		else if ( $keyset && !is_string ( $this->$key ))
			return $this->$key;
		else if ( $keyset && ( $s = unserialize ( $this->$key ) ) )
			return $s;
		else if ( $keyset && $this->$key )
			return $this->$key;
		$return = false;
		return $return;
	}
}
?>
