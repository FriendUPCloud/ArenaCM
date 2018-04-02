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


  
/** *************************************************************************
*  TABLE CLASS
** ********************************************************************** */

class cDatabaseTable
{
	var $table = NULL;
	var $fields = NULL;
	var $keys = NULL;
	var $exists = false;
	var $_fieldNames = NULL;
	var $type = 'mysql';
		
	function __construct ( $table=false, $database=false )
	{
		if ( !$database ) $this->database = dbObject::globalValue ( "database" );
		$this->table = $table;
	}

	function getDatabaseFields ( )
	{
		$fields = array ();
		if ( $fields_res = mysqli_query( $this->database->resource, "DESCRIBE `{$this->table}`" ) )
		{
			if( mysqli_num_rows( $fields_res ) > 0 ) 
			{
				while( $row = mysqli_fetch_object( $fields_res ) )
				{
					$fields[ $row->Field ] = $row;
				}
			}
		}
		return $fields;
	}

	function load ()
	{
		$fields = $this->getDatabaseFields ();
		if ( count ( $fields ) )
		{
			$this->fields = $fields;
			$this->getFieldNames ();
			$this->exists = true;
			return true;
		}
		return false;
	}


	/**
	*** Save the data definition to the database. The table will NOT be saved if the field set is empty. 
	**/
	function save()
	{
		if ( $this->exists() && count( $this->fields ) )
		{
			// Table already exists. We need to examine the differences between the
			// data definition stored in the database and in memory, and then commit the necessary changes.

			// Grab the fields from database for comparison.
			$dbFields = $this->getDatabaseFields();

			foreach( $this->fields as $fieldName => $field )
			{
				if ( !$dbFields[$fieldName] )
				{
					$this->database->query( "ALTER TABLE `{$this->table}` ADD COLUMN ".$this->fieldSyntax( $field ).";" );
				}
				else
				{
					// Update the definition if it differs from the database.
					// fieldSyntax is a nice way to differentiate field definitions..
					if ( $this->fieldSyntax( $field ) != $this->fieldSyntax( $dbFields[$fieldName] ) )
					{
						$this->database->query( "ALTER TABLE `{$this->table}` CHANGE COLUMN `{$fieldName}` ".$this->fieldSyntax( $field ).";" );
					}
					// Make sure we can alter the field
					// TODO: Add support for primary key alteration
					if ( 
						$field->Null != $dbFields[ $fieldName ]->Null ||
						$field->Type != $dbFields[ $fieldName ]->Type ||
						$field->Default != $dbFields[ $fieldName ]->Default
					)
					{
						if ( is_numeric ( $field->Default ) ) $sign = "'"; else $sign = "\"";
						$null = ( $field->Null == "YES" ) ? "NULL" : "NOT NULL";
						$this->database->query ( "
							ALTER TABLE 
								`{$this->table}` 
							MODIFY 
								`{$fieldName}` {$field->Type} $null
								Default=$sign{$field->Default}$sign
						" );
					}
				}
			}

			// Drop any columns that exist only in the database.
			foreach( $dbFields as $key => $value ) if ( !$this->fields[$key] )
			{
				$this->database->query( "ALTER TABLE `{$this->table}` DROP COLUMN `{$key}`;" );
			}

			
		}
		else
		{
			// Create new table
			return $this->database->query( $this->createTableSyntax() );
		}
	}
	
	function exists()
	{
		if ( !$this->exists ) $this->load();
		return $this->exists;
	}


	/**
	*** Translate the format string to a value MySQL understands.
	**/
	function formatType( $fieldType )
	{
		list( $type, $size ) = preg_split( "/[\s]*[\(\)][\s]*/", $fieldType );

		if( preg_match( "/^(string|varchar)/", $type ) )
		{
			$type = "varchar";
			if ( !$size ) $size = 255;
		}
		else if( preg_match( "/^(bool)/", $type ) )
		{
			$type = "tinyint";
			$size = 4;
		}
		else if ( $fieldType == "int" )
		{
			$type = "int";
			$size = 11;
		}
		else if( preg_match( "/^(int)/", $type ) )
		{
			$type = "bigint";
		}

		if ( $size ) $type = "{$type}({$size})";
		return $type;
	}

	function addField( $fieldName, $fieldType="string", $options = array() )
	{
		if ( $this->fields[$fieldName] )
		{
			$options = array_merge( get_object_vars( $this->fields[$fieldName] ), $options );
		}

		// Jam field name and type into the options array
		$options = array_merge( $options, array( 
			"Field" => $fieldName,
			"Type"  => $this->formatType( $fieldType ),
		) );

		// Create a new anonymous object
		$field = new stdClass();

		// Translate the array to an object
		foreach( $options as $key => $value ) $field->{$key} = $options[$key];

		if ( $field->primaryKey )
		{
			$field->Key = "PRI";
			$this->keys = $fieldName; // TODO: Support for multiple keys here. ( $this->keys becomes an array )
		}
		if ( $field->autoIncrement ) $field->Extra = trim( "auto_increment {$field->Extra}" );

		$this->fields[$fieldName] = $field;
		return true;
	}
	
	function removeField( $fieldName )
	{
		if ( $this->fields[$fieldName] )
		{
			unset( $this->fields[$fieldName] );
		}
	}

	function renameField( $oldName, $newName )
	{
		if( !$this->fields[$oldName] ) return $false;
		$this->fields[$oldName]->renameTo = $newName;
		return true;
	}

	function reorderField( $fieldName, $offset )
	{
		if( !$this->fields[$fieldName] ) return false;

		$index = -1; $a = 0; $fieldNames = array();
		foreach ( $this->fields as $key => $value ) 
		{
			if ( $key == $fieldName ) $index = $a;
			$fieldNames[$a] = $key;
			$a++;
		}

		if ( $index > -1 )
		{
			$afterIndex = $index + $offset - 1;
			if ( $afterIndex > ( count( $fieldNames ) - 1 ) ) $afterIndex = count( $fieldNames ) - 1;
			if ( $afterIndex != $index )
			{
				$this->fields[$fieldName]->Position = ($afterIndex < 0 ) ? "FIRST" : "AFTER `{$fieldNames[$afterIndex]}`";
			}
		}

		return true;	
	}

	function getFields ()
	{
		return $this->fields;
	}
	
	function getFieldNames ()
	{
		if ( is_array ( $this->_fieldNames ) ) return $this->_fieldNames;
		$this->_fieldNames = array ( );
		if ( count ( $this->fields ) )
		{
			foreach ( $this->fields as $field )
				$this->_fieldNames[] = $field->Field;
			return $this->_fieldNames;
		}
		return false;
	}

	function getFieldType ( $field )
	{
		if ( $this->fields [ $field ] )
			return $this->fields [ $field ]->Type;
		else return false;
	}
	
	function getPrimaryKey ( ) 
	{ 
		if ( $this->keys )
		{
			$keys = $this->keys;
		}
		else
		{
			$keys = Array();
			if ( $result = mysqli_query ( $this->database->resource, 'SHOW KEYS FROM `' . $this->table . '`' ) )
			{
				while ( $row = mysqli_fetch_assoc ( $result ) ) 
				{
					if ( $row['Key_name'] == 'PRIMARY' )
						$keys [ $row [ 'Seq_in_index' ] - 1 ] = $row [ 'Column_name' ];
				}
				if ( sizeof ( $keys ) == 1)
					$keys = $keys [ 0 ];
				$this->keys = $keys;
			}
			else
				return false;				
		}
		return $keys;
	}


	function fieldSyntax( $field )
	{
		$fieldName = ($field->renameTo) ? $field->renameTo : $field->Field;
		$output = "`{$fieldName}` {$field->Type}";
		if ( $field->Null == "NO" )
			$output .= " NOT NULL";
		if ( $field->Default )
			$output .= " DEFAULT '{$field->Default}'";
		if ( $field->Extra )
			$output .= " {$field->Extra}";
		if ( $field->Position )
			$output .= " {$field->Position}";
		return $output;
	}
	
	function createTableSyntax()
	{
		$keys = array();
		if ( count ( $this->fields ) )
		{
			foreach ( $this->fields as $key => $value ) $keys[] = $key;
			$prikeys = $this->getPrimaryKey();

			$output =  "CREATE TABLE `{$this->table}` (\n";
			
			for( $a = 0; $a < count( $this->fields ); $a++ )
			{
				$field = $this->fields[$keys[$a]];
				$output .= $this->fieldSyntax( $field );
				if ( $a < ( count( $this->fields ) - 1 ) || $prikeys )
					$output .= ",";
				$output .= "\n";
			}
			if( $prikeys )
			{
				if ( is_array( $prikeys ) ) $prikeys = implode( ", ", $prikeys );
				$output .= "  PRIMARY KEY ({$prikeys})\n";
			}
			$output .= ");";
			return $output;
		}
		return false;
	}

}



/////////////////////////////////////////////////////////
////     DATABASE CLASS                              ////
/////////////////////////////////////////////////////////



class cDatabase
{
	var $resource = NULL;
	var $hostname = NULL;
	var $username = NULL;
	var $password = NULL;
	var $db = NULL;    
	var $tables = NULL;            
	var $user = NULL;
	var $debugInfo = array();

	function setHostname ( $string ) { $this->hostname = $string; }
	function setUsername ( $string ) { $this->username = $string; }
	function setPassword ( $string ) { $this->password = $string; }
	function setDb       ( $string ) { $this->db = $string; }
	
	/**
	*** Open a connection to a MySQL database
	**/
	function open ( $hostname = false, $username = false, $password = false, $dbname = false )
	{         	 
		if ( $hostname ) $this->setHostname ( $hostname );
		if ( $username ) $this->setUsername ( $username );
		if ( $password ) $this->setPassword ( $password );
		if ( gettype ( $dbname ) == 'string' ) $this->setDb ( $dbname );
		
		if ( $this->resource = mysqli_connect( $this->hostname, $this->username, $this->password ) )
		{
			mysqli_set_charset( $this->resource, 'utf8' );
			if ( $this->db )
				$this->useDb ( $this->db );
			return true;
		}
		
		return false;    	
	}
	
	
	
	/**
	*** Select working database
	**/
	function useDb ( $string )
	{
		if ( !isset ( $this->type ) )
			$this->type = 'mysql';
		switch ( $this->type )
		{
			default:
			case "mysql":
				$this->db = $string;
				$this->query ( "USE {$this->db}", $this->resource );
				// TODO: Fix support for strict mode, this is only a temporary hackaround ...
				$this->query ( "SET SESSION sql_mode = ''", $this->resource );
				break;
		}
	}

	
	
	function fetch2d ( $query, $mode = MYSQLI_ASSOC )
	{
		return $this->fetchRows ( $query, MYSQLI_ASSOC );
	}

	function fetchRows ( $query, $mode = MYSQLI_ASSOC )
	{
		global $queries;
		$array = false; 
		if ( is_array ( $cached = $this->getCached ( $query ) ) )
			return $cached;
		if ( $result = $this->query ( $query, $this->resource ) )
		{
			$array = Array ( );
			do
			{
				$row = mysqli_fetch_array ( $result, MYSQLI_ASSOC );
			}
			while ( $row && $array[] = $row );
			$this->setCached ( $query, $array );
			mysqli_free_result ( $result );
			return $array;      
		}
		return false;
	}
	
	/*
		Fetch the rows and return an object
	*/
	function fetchObjectRows ( $query, $mode = MYSQLI_ASSOC, $debugLabel = false )
	{	
		if ( is_array ( $cached = $this->getCached( $query . ' _OBJECTS_' ) ) )
			return $cached;
	
		$time = false;
		if( defined( 'DEBUG_DATABASE' ) )
		{
			if( !$debugLabel )
				$debugLabel = 'Unlabelled';
			$tarr = explode( ' ', microtime() );
			$time = (float)reset( $tarr );
		}
		
		if ( $qr = mysqli_query ( $this->resource, $query ) )
		{
			// Debug some database info
			if( $debugLabel && defined( 'DEBUG_DATABASE' ) && isset( $time ) )
			{
				$tar = explode( ' ', microtime() );
				$obj = new stdclass();
				$obj->delay = (float)reset( $tar ) - $time;
				$obj->query = $query;
				if( !isset( $this->debugInfo[$debugLabel] ) )
					$this->debugInfo[$debugLabel] = array();
				$this->debugInfo[$debugLabel][] = $obj;
			}
			while ( $rows[] = mysqli_fetch_assoc ( $qr ) ) {}
			array_pop ( $rows );
			if ( ( $count = count ( $rows ) ) )
			{
				$output = Array ( );
				foreach ( $rows as $row )
				{
					$obj = new stdclass ( );
					foreach ( $row as $k=>$v )
					{
						if ( is_string ( $v ) || is_numeric ( $v ) )
							$obj->$k = $v;
					}
					$output[] = $obj;
				}
				$this->setCached( $query . ' _OBJECTS_', $output );
				mysqli_free_result ( $qr );
				return $output;
			}
		}
		return false;
	}
	
	function fetchSingle ( $query, $mode = MYSQLI_ASSOC )
	{
		return $this->fetchRow ( $query, MYSQLI_ASSOC );
	}
	
	// We're phasing out fetchSingle and fetch2d
	function fetchRow ( $query, $mode = MYSQLI_ASSOC, $debugLabel = false )
	{
		global $queries;
		if( $cached = $this->getCached( $query ) )
			return $cached;
		
		if ( $result = $this->query( $query, $this->resource ) )
		{
			$array = mysqli_fetch_array ( $result, MYSQLI_ASSOC );
			$this->setCached ( $query, $array );
			mysqli_free_result ( $result );
			return $array;
		}
		return false;
	}
	
	// Fetch a row and return a object
	function fetchObjectRow ( $query, $mode = MYSQLI_ASSOC, $debugLabel = false )
	{		
		if ( is_object( $cached = $this->getCached( $query . ' _OBJECT_' ) ) )
			return $cached;
		
		$time = false;
		if( defined( 'DEBUG_DATABASE' ) )
		{
			if( !$debugLabel )
				$debugLabel = 'Unlabelled';
			$tarr = explode( ' ', microtime() );
			$time = (float)reset( $tarr );
		}
			
		if ( $rs = mysqli_query( $this->resource, $query ) )
		{
			// Debug some database info
			if( $debugLabel && defined( 'DEBUG_DATABASE' ) && isset( $time ) )
			{
				$tar = explode( ' ', microtime() );
				$obj = new stdclass();
				$obj->delay = (float)reset( $tar ) - $time;
				$obj->query = $query;
				if( !isset( $this->debugInfo[$debugLabel] ) )
					$this->debugInfo[$debugLabel] = array();
				$this->debugInfo[$debugLabel][] = $obj;
			}
			if ( $row = mysqli_fetch_assoc ( $rs ) )
			{
				$obj = new stdclass ( );
				foreach ( $row as $k=>$v )
					$obj->$k = $v;
				$this->setCached ( $query . ' _OBJECT_', $obj );
				mysqli_free_result ( $rs );
				return $obj;
			}
		}
		return false;
	}

	// Do a raw query
	function query( $query, $resource = false, $debugLabel = false )
	{
		global $queries;
		$this->_lastQuery = $query;
		
		$time = false;
		if( defined( 'DEBUG_DATABASE' ) )
		{
			if( !$debugLabel )
				$debugLabel = 'Unlabelled';
			$tarr = explode( ' ', microtime() );
			$time = (float)reset( $tarr );
		}
		
		/**
		 * Try to get around sql injections where possible by dropping
		 * multiqueries..
		**/
		
		if ( strstr ( $query, ';' ) )
		{
			$found = false;
			foreach ( array ( '; DROP', '; SELECT', '; ALTER', '; EMPTY', '; DELETE' ) as $test )
				if ( strstr ( $query, $test ) ) { $found = true; break; }
			if ( $found ) { ArenaDie ( 'SQL injection intercepted!' ); return false; }
		}
		
		/**
		 * Go ahead
		**/
		
		$queries[] = $query;
		
		if ( !$resource ) $resource = $this->resource;
		if ( $resource && ( $result = mysqli_query ( $resource, $query ) ) )
		{
			// Debug some database info
			if( $debugLabel && defined( 'DEBUG_DATABASE' ) && isset( $time ) )
			{
				$tar = explode( ' ', microtime() );
				$obj = new stdclass();
				$obj->delay = (float)reset( $tar ) - $time;
				$obj->query = $query;
				if( !isset( $this->debugInfo[$debugLabel] ) )
					$this->debugInfo[$debugLabel] = array();
				$this->debugInfo[$debugLabel][] = $obj;
			}
			if ( preg_match ( "/(INSERT|UPDATE)/i", $query ) )
				$GLOBALS["__GLOBALS___{$this->db}"] = array ();
			return $result;
		}
		return false;
	}
	
	// Get formatted info about time delay
	function getDebugInfo()
	{
		if( !$this->debugInfo ) return '<p>No debug info.</p>';
		$str = '';
		$total = 0.0;
		$count = 0;
		foreach( $this->debugInfo as $k=>$v )
		{
			$str .= '<h2>' . $k . '</h2>';
			$sub = 0.0;
			$subcount = 0;
			if( !count( $v ) )
			{
				$str .= '<p><strong>Empty label ' . $k . '.</strong></p>';
			}
			else
			{
				foreach( $v as $row )
				{
					$str .= '<p><strong>Query:</strong> (delay, ' . (string)round( $row->delay, 4 ) . ' seconds)</p>';
					$str .= '<pre>' . $row->query . '</pre>';
					$sub += $row->delay;
					$count++;
					$subcount++;
				}
				$str .= '<p><strong>Total ' . $k . ': ' . (string)round( $sub, 4 ) . ' seconds (' . $subcount . ' queries here)</strong></p>';
			}
			$total += $sub;
		}
		$str .= '<hr/>';
		$str .= '<p><strong>Final delay: ' . (string)round( $total, 4 ) . ' seconds</strong></p>';
		$str .= '<p><strong>Totally, ' . $count . ' queries.</strong></p>';
		return $str;
	}
	
	function getId ( )
	{
		if ( $res = mysqli_query( $this->resource, "SELECT LAST_INSERT_ID() AS ID" ) )
		{
			$res = mysqli_fetch_array ( $res, MYSQLI_ASSOC );
			return $res[ "ID" ] ? $res[ "ID" ] : false;
		}
		return false;
	}
	
	function close ( )
	{
		// TODO: Find out!!! Is this not working???? 
		
		if( $this->resource ) mysqli_close ( $this->resource );
	}
	
	
	function getCached ( $query )
	{
		return isset( $GLOBALS["__GLOBALS___{$this->db}"][$query] ) ?
			$GLOBALS["__GLOBALS___{$this->db}"][$query] :
			false;
	}
	
	function setCached ( $query, $result )
	{
		$GLOBALS["__GLOBALS___{$this->db}"][$query] = $result;
	}

/* -------------------------------------------------------------------------
TABLE FIELD FUNCTIONS
Get username and password from cookie and validate against database.
------------------------------------------------------------------------- */
	
	function &getTable ( $table ) 
	{
		if ( is_array ( $this->tables ) && array_key_exists ( $table, $this->tables ) )
			return $this->tables[ $table ];
		else return $this->loadTable ( $table );
	}

	function &loadTable ( $table )
	{
		$cTable = new cDatabaseTable ( $table );
		$cTable->database = $this;
		if ( $cTable->load() )
		{
			$this->tables[ $table ] = $cTable;
		}
		else $cTable = false;
		return $cTable;
	}
	
	function loadTables ()
	{
		if ( $tables = $this->fetchRows ( 'SHOW TABLES', MYSQLI_ASSOC ) ) 
		{
			foreach ( $tables as $table ) 
				$this->loadTable ( $table );
			return true;
		}
	}       
	
/* -------------------------------------------------------------------------
User functions   
------------------------------------------------------------------------- */    
	
	function setUsertable ( $table )
	{
		$this->usertable = trim ( $table );    
	}
	
	function setUserfield ( $field )
	{
		$this->userfield = trim ( $field );
	}
	
	function setPassfield ( $field )
	{
		$this->passfield = trim ( $field );
	}
	
	function useEncryption ( $tf )
	{
		$this->useEncryption = $tf;
	}
		
	function authenticate ()
	{
		if ( $this->usertable && $this->userfield && $this->passfield && !isset ( $_GET[ "logout" ] ) )
		{    		    		
			if ( $_GET[ "username" ] )
			{    			
				$user = $_GET[ "username" ];
				$pass = $_GET[ "password" ];
				if ( $this->useEncryption )  
					$pass = dbUser::hash ( $pass );
			}
			else if ( $_POST[ "username" ] )
			{    			
				$user = $_POST[ "username" ];
				$pass = $_POST[ "password" ];
				if ( $this->useEncryption )      			
					$pass = dbUser::hash ( $pass );  		    			
			}
			else
			{    			
				$user = $_SESSION[ "user_name" ];
				$pass = $_SESSION[ "user_pass" ];
			}    		
			$dbO = new dbObject ( $this->usertable );
			$dbO->{$this->userfield} = $user;
			$dbO->{$this->passfield} = $pass;    		    		
			
			if ( $user && $pass && $dbO = $dbO->findSingle ( ) )
			{    			    		    			
				session_regenerate_id ( true );
				$_SESSION[ "user_name" ] = $dbO->{$this->userfield};
				$_SESSION[ "user_pass" ] = $dbO->{$this->passfield};
				$this->user = $dbO;
				return true; 
			}
		}    	    	    	
		return false;
	}
	
}

?>
