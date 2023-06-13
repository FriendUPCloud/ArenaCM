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



/**  ------------------------------------------------------------------------
***  dbObject SQL Abstraction Engine                                          
***  ------------------------------------------------------------------------
***
***  dbObject provides a layer between the cDatabase classes and the OOP
***  features of Arena.
***
***  Gotchas:
***    Private variables are prefixed with an underscore, please avoid starting   
***    column names with underscores.
***
***  Dependencies:
***    dbObject has one single dependency; the cDatabase class
***
***
***
***  -- Using dbObject ------------------------------------------------------
***
***  Configuring dbObject:
***
***    dbObject must be configured with a connected cDatabase object before 
***    it can be used.
***
***    Example:
***    <code>
***      $database = new cDatabase ();
***      $database->hostname = "localhost";
***      $database->username = "username";
***      $database->password = "password";
***      $database->db       = "myDatabase";
***      $database->open();
***
***      dbObject::globalValueSet ( "database", $database );
***    </code>
***
***    This is the only configuration step necessary, dbObject will pull
***    necessary information from the database.
***                                                                            
***                                                                            
***  Loading a dbObject:
***
***    <code>
***      $artist = new dbObject ( "Artist" );      // "Artist" is the name of the table
***      $artist->load( 2 );                       // 2 is the primary key ID
***    </code>
***
***    We can now access the columns as properties on the object:
***    <code>
***      print( $artist->Name );                 // Name is the column name
***    </code>
***
***    There are other ways of loading dbObjects, we'll get back to that later.
***
***
***  Saving a dbObject:
***
***    Saving dbObjects is equally simple. The save() method will check for an
***    existing record, and perform INSERT or UPDATE queries as necessary.
***
***    Example:
***    <code>
***      $artist->Name = "Radiohead";
***      $artist->save();
***    </code>
***
***	   For simplicity, dbObjects use ID as primary key, auto incremented.
***
***
***  -- Loading several dbObjects -------------------------------------------
***
***  The find() method:
***
***    Often, you'll want to load a collection of dbObjects based on certain
***    criteria. To achieve this, create a query object (in other words,
***    a dbObject instance), set the properties and call the find() method.
***
***    The following example creates an array of dbObjects:
***    <code>
***      $albums = new dbObject ( "Album" );
***      $albums->ArtistID = 2;
***      $albums = $albums->find();
***    </code>
***
***    This example finds all rock albums released in 2006:
***    <code>
***      $albums = new dbObject ( "Album" );
***      $albums->ReleaseYear = 2006;
***      $albums->Genre       = "rock";
***      $albums = $albums->find();
***    </code>
***
***
***  Sorting and limiting results:
***
***    Use the addClause method on the query object to narrow down
***    result sets. The following example will load the 50 first rock albums
***    sorted by artist ID and album name:
***
***    <code>
***      $albums = new dbObject ( "Album" );
***      $albums->Genre = "rock";
***      $albums->addClause( "ORDER BY", "ArtistID" );
***      $albums->addClause( "ORDER BY", "Name" );
***      $albums->addClause( "LIMIT",    50 );
***      $albums = $albums->find ();
***    </code>
***    
***
***  Custom SQL:
***
***    find() also accepts a custom SQL query parameter, which overrides
***    the automated query building:
***
***    <code>
***      $albums = new dbObject ( "Album" );
***      $albums = $albums->find( "SELECT * FROM Album WHERE ArtistID = 5" );
***    </code>
***
***
***  Counting matches:
***
***    <code>
***      $numAlbums = new dbObject ( "Album" );
***      $numAlbums->ArtistID = 2;
***      $numAlbums = $albumCount->findCount ();
***    </code>
***
***
***
***  -- Extending dbObject --------------------------------------------------
***
***    dbObject can be extended.  
***
***    <code>
***      class dbArtist extends dbObject
***      {
***        var $_tableName = "Artist";  // name of the table
***        // Insert your functions here
***      }
***    </code>
*** 
***                                                                            
*** #### EXTENDING dbObject: ################################################  
***                                                                            
***   The following hook methods are triggered on the extended child class     
***   (if they are defined), and can be used for ie. formatting data or        
***   loading/saving related objects.                                          
***                                                                            
***	  onLoad ()    - Performed before object is loaded                         
***	  onLoaded ()  - Performed after object is loaded                          
***	  onSave ()    - Performed before object is saved                          
***	  onSaved ()   - Performed after object is saved                           
***   onDelete ()  - Performed before object is deleted                        
***   onDeleted () - Performed after object is deleted                         
***                                                                            
***                                                                            
***   EXAMPLE:                                                                 
***   An extended class that does parent-child relations                       
***                                                                            
***   <code>   
***   class dbPage extends dbObject                                            
***   {                                                                        
***     var $rendered; // rendered html-output                                 
***     var $subpages;                                                         
***     var $_tableName = "Page";
***                                                                            
***     function onLoaded ()                                                   
***     {                                                                      
***        $this->rendered = Template::render ( $this, "template.tpl" );       
***        $this->loadSubpages ();                                             
***     }
***
***     function onSave ()
***     {
***        if ( !$this->DateAdded ) $this->DateAdded = "NOW()";
***     }                                                                     
***                                                                            
***     function onDelete ()                                                   
***     {                                                                      
***        // Delete subpages                                                  
***        foreach ( $this->subpages as $page )                                
***        {                                                                   
***          $page->delete ();                                                 
***        }                                                                   
***     }                                                                      
***                                                                            
***     function loadChildren ()                                               
***     {                                                                      
***       $children = new dbPage ();                                           
***       $children->Parent = $this->ID;                                       
***       $children->addClause ( "ORDER BY", "DatePosted DESC" );              
***       $this->subpages = $children->find ();                                
***     }                                                                      
***  }                                                                         
***  </code>                                                                          
***                                                                            
***                                                                            
*** @author Inge Jørgensen <inge@blest.no>                                     
*** @author	Hogne Titlestad <inge@blest.no>
*** @author Forhud Fornebu <forhud@blest.no>
*** @package arena-lib                                                         
*** @copyright Copyright (c) 2005-2008 Blest AS                                     
***                                                                            
**/

define ( 'DBO_INCLUDE_THIS', true );

// Empty dummy class
class Dummy {}

class dbObject
{
	var $_table = NULL;     
	var $_tableName = NULL; 
	var $_primaryKey = NULL;
	var $_isLoaded = false;
	var $_loadState = array();
	var $_doHooks  = true;
	var $_clauses = NULL;
	var $_autoLoad = NULL;
	var $_dbOverride = NULL;
	var $loadState = NULL;
	var $ID = NULL;
	var $customQuery = NULL;
	var $_contentGroups = false;
	var $_loadingExtrafields = false;
	var $_loadedObject = false;
	var $_cache;

	/**
	*** Create a new database object
	***
	*** @param string $table optional Name of table
	*** @param string $database optional Reference to override cDatabase object
	**/
	function __construct ( $table=false, &$database=false )
	{
		// Check cache
		if ( !isset ( $GLOBALS[ 'dbObjectCache' ] ) )
			$GLOBALS[ 'dbObjectCache' ] = array ();
		$this->_cache =& $GLOBALS[ 'dbObjectCache' ];
		// Set tablename if passed
		if ( $table ) $this->_tableName  =  $table;
		// Set database override
		if ( $database ) $this->_dbOverride =& $database;
		// Load table structure
		$this->loadTable();
	}
	
	function create( $tableName=false, $database=false, $id=false )
	{
		$className = "db{$tableName}";
		
		if ( class_exists( $className ) )
		{
			$object = new $className( $tablename, $database );
		}
		else
		{
			$object = new dbObject( $tableName, $database );
		}
		
		if ( $id ) $object->load ( $id );
		
		return $object;
	}
	
	/**
	*** Get this identifier (name,title etc)
	**/
	function getIdentifier ( )
	{
		if ( $this->Name )
			return $this->Name;
		else if ( $this->Title )
			return $this->Title;
		else if ( $this->Username )
			return $this->Username;
		else if ( $this->Identifier )
			return $this->Identifier;
		else return $this->{$this->_primaryKey};
	}
	
	
	/**
	*** Compare with another dbObject, return true if they're equal.
	***
	*** @param  mixed   $object
	*** @return boolean true if equal
	**/
	function compare ( &$object )
	{
		if ( is_object( $object ) && $object->_tableName && $object->_primaryKey )
		{
			if ( 
				$object->{$object->_primaryKey} == $this->{$this->_primaryKey} &&
				$object->_tableName == $this->_tableName 
			)
			{
				return true;
			}
		}
		return false;
	}


	
	/////////////////////////////////////////////////////////
	////     TABLE STRUCTURE                             ////
	/////////////////////////////////////////////////////////

	
	
	/**
	*** Load table info (including primary key) from the cDatabase classes.      
	***
	*** @return void
	**/
	function loadTable ()
	{
		if ( !is_object ( $this->_table ) )
		{
			$database =& $this->getDatabase ( );
			if ( $this->_table =& $database->getTable ( $this->_tableName ) )
			{
				if ( !$this->_primaryKey ) 
					$this->_primaryKey =  $this->_table->getPrimaryKey ();
				return true;
			}
			else
			{
				ArenaDie( 'Could not get table of name: ' . $this->_tableName );
			}
			return false;
		}
		return true;
	}
	
	function loadState ( $key, $value = false )
	{
		if ( $value ) $this->_loadState[$key] = $value;
		return ( isset( $this->_loadState[$key] ) ) ? $this->loadState[$key] : false;
	}
	
	
	
	/**
	 * Return a reference to this object's cDatabaseTable.
	 *
	 * @return void
	 */
	function &getTable ()
	{
		return $this->_table;
	}
	
	
	
	/**
	*** Get class name (for static methods)
	***
	*** @return string name of class
	**/
	function getClassName ()
	{
		$className = debug_backtrace();
		$className = $className[ 1 ][ 'class' ];
		return $className;
	}



	/////////////////////////////////////////////////////////
	////     FORMATTING                                  ////
	/////////////////////////////////////////////////////////



	/**                                                                          
	*** Format a field for database insertion.                                   
	***
	*** @param  string $field Name of field                                      
	*** @param  mixed  $value Value to format                                    
	*** @return string                                                           
	*/
	function formatField ( $field, $value )
	{
		$database =& $this->getDatabase ( );
		
		$fieldType = $this->_table->getFieldType ( $field );
		
		// Serialize object if necessary
		if ( is_object ( $value ) || is_array ( $value ) )                       
		{
			$value = serialize ( $value );                                       
		}
		
		// Blobs...
		if ( strstr ( $fieldType, 'lob' ) )
			return '"' . mysqli_real_escape_string( $database->resource, $value ) . '"';
			
		// NOW() statement
		if ( preg_match ( "/^(date|time)/", $fieldType ) && $value == 'NOW()' )  
			return $value;         
		
		// Faked boolean using tinyint
		if ( preg_match ( "/^tinyint/", $fieldType ) && !$value )                
			return '\'0\'';          
		
		// Numeric data
		if ( is_numeric ( $value ) )                                             
			return "'$value'";     
		
		// Blank value
		if ( !$value )                                                           
			return '\'\'';           
		
		// Handle wierd special cases
		if ( !is_string ( $value ) )
		{
			if ( is_object ( $value ) && get_class ( $value ) == '_PHP_Incomplete_Class_' );
			{
				$value = ( dbObject::analyze ( $value ) );
			}
		}
			
		return '"' . mysqli_real_escape_string ( $database->resource, $value ) . '"';
	}
	
	function analyze ( $obj )
	{
		if ( !is_array ( $obj ) && !is_object ( $obj ) )
			return '';
		foreach ( $obj as $k=>$v )
		{
			if ( !is_string ( $v ) )
				continue;
			$str .= $k . "\t=>\t" . $v . "\n";
		}
		return $str;
	}

	/**
	*** Remove slashes from entries.
	**/
	function unslash()
	{
		foreach ( $this->_table->getFieldNames() as $field )
		{
			if ( isset( $this->{$field} ) )
				$this->{$field} = stripslashes ( $this->{$field} );
		}
	}
	
	function isSerialized( $value, &$result = null )
	{
		// Bit of a give away this one
		if ( !is_string( $value ) )
		{
			return false;
		}
		// Serialized false, return true. unserialize() returns false on an
		// invalid string or it could return false if the string is serialized
		// false, eliminate that possibility.
		if ( $value === 'b:0;' )
		{
			$result = false;
			return true;
		}
		$length	= strlen( $value );
		$end	= '';
		switch ( $value[0] )
		{
			case 's':
				if ( $value[$length - 2] !== '"' )
				{
					return false;
				}
			case 'b':
			case 'i':
			case 'd':
				// This looks odd but it is quicker than isset()ing
				$end .= ';';
			case 'a':
			case 'O':
				$end .= '}';
				if ( $value[1] !== ':' )
				{
					return false;
				}
				switch ( $value[2] )
				{
					case 0:
					case 1:
					case 2:
					case 3:
					case 4:
					case 5:
					case 6:
					case 7:
					case 8:
					case 9:
					break;
					default:
						return false;
				}
			case 'N':
				$end .= ';';
				if ( $value[$length - 1] !== $end[0] )
				{
					return false;
				}
				break;
			default:
				return false;
		}
		if ( ( $result = @unserialize( $value ) ) === false )
		{
			$result = null;
			return false;
		}
		return true;
	}
	
	/////////////////////////////////////////////////////////
	////     INPUT/OUTPUT                                ////
	/////////////////////////////////////////////////////////
	
	
	
	/**                                                                          
	*** Load entry from an array                                                 
	*** Triggers the following hooks: onLoaded()
	***                                                                           
	*** @param  array $row                                                       
	*** @return bool  true                                                       
	**/
	function setFromArray ( $row )
	{
		if ( !is_array ( $row ) || count ( $row ) < 1 ) return false;  // Sanity check
		
		$this->_loadedObject = new stdClass();
		
		foreach ( $row as $key => $value )
		{
			if ( $value && $this->isSerialized( $value ) )
			{
				$value = @unserialize ( $value );
				$this->$key = $value;
			}
			else if ( $key && !is_numeric ( $key ) )
			{
				$this->$key = stripslashes ( $value );
			}
			
			if ( $key )
			{
				$this->_loadedObject->$key = $this->$key;
			}
		}
		
		if ( 
			!$this->_isLoaded && $this->_doHooks && 
			method_exists ( $this, 'onLoaded' ) && !$this->loadState ( 'onLoaded' ) 
		)
		{
			$this->onLoaded ();
			$this->loadState ( 'onLoaded', true );
		}
		$this->_isLoaded = true;
		
		return true;
	}



	/**
	*** Load entry from a dbObject
	*** Triggers the following hooks: onLoaded()
	***
	*** @param  dbObject $object
	*** @return bool     true on success
	**/
	function setFromObject( $object )
	{
		// Sanity check
		if ( !is_object( $object ) || !is_object( $object->_table ) ) return false;

		//cDebug::analyze ( $object->children );
		
		$this->_loadedObject = new stdClass();
		
		foreach ( $object->_table->getFieldNames() as $field )
		{
			$this->{$field} = $object->{$field};
			
			if ( $field )
			{
				$this->_loadedObject->{$field} = $object->{$field};
			}
		}
		
		if ( $object->_dbOverride ) 
		{
			$this->_dbOverride =& $object->_dbOverride;
		}

		if ( !$this->_isLoaded && $this->_doHooks && method_exists ( $this, 'onLoaded' ) && !$this->loadState ( 'onLoaded' ) )
		{
			$this->onLoaded ();
			$this->loadState ( 'onLoaded', true );
		}
		$this->_isLoaded = true;

		return true;
	}

	
	
	/**
	*** Get a dbObject by reference
	***
	*** @param mixed $key optional key to get for
	**/
	public function get ( $key=false, $tableName=false )
	{
		if ( !$key )
		{	
			//trigger_error ( "dbObject: cannot get object, no key specified", E_USER_WARNING );
			return false;
		}
		
		if ( !$tableName ) $tableName = $this->_tableName;
		
		switch ( $tableName )
		{
			case 'Users': 
				$className = 'dbUser'; 
				break;
				
			case 'ContentElement':
				$className = 'dbContent';
				break;
				
			case 'ImageFolder':
				include_once ( 'lib/classes/dbObjects/dbImage.php' );
				$className = 'dbImageFolder';
				break;
				
			case 'FileFolder':
				include_once ( 'lib/classes/dbObjects/dbFile.php' );
				$className = 'dbFileFolder';
				break;
				
			default: 
				if ( substr ( $tableName, 0, 5 ) == 'class' )
				{
					$className = 'dbClassInstance';
					$type = substr ( $tableName, 5, strlen ( $tableName ) - 5 );
				}
				else if ( class_exists ( "db$tableName" ) )
				{
					$className = "db$tableName";
				}
				else
				{
					if ( !$tableName )
						return false;
					else
						$className = 'dbObject';
				}
				break;
		}
		if ( $className == 'dbClassInstance' )
		{
			$object = new dbClassInstance ( $type );
		}
		else
		{
			if ( $className == 'dbObject' && $tableName )
			{
				$object = new $className ( $tableName );
			}
			else
			{
				if ( strtolower ( $className ) == 'dbcontent' )
					include_once ( 'lib/classes/dbObjects/dbContent.php' );
				$object = new $className ();
			}
		}
		if ( !$object->_tableName )
		{	
			trigger_error ( 'dbObject: cannot get object, no table name specified', E_USER_WARNING );
			return false;
		}
		if ( $object->load ( $key ) )
			return $object;	
		return false;
	}

	function &getDatabase ( )
	{
		if ( $this->_dbOverride ) return $this->_dbOverride;
		else if ( $this->_table && $this->_table->database )
			return $this->_table->database;
		return dbObject::globalValue ( 'database' );
	}
	
	// Just override the current database with a new one
	function setDatabase ( &$dbobj )
	{
		$this->_dbOverride =& $dbobj;
	}
	
	/**                                                                           
	*** Check if object already exists in the database.                           
	***
	*** @param  mixed   $key optional Key to search for, defaults to $this->(_primaryKey)            
	*** @return boolean true if exists                                            
	**/
	function exists ( $key=false )
	{
		$database =& $this->getDatabase ( );
		
		$exists   =  false;                                                // default to false
		
		$keys = $this->_primaryKey;                                        // get keys from $this->_primaryKey
		if ( !is_array( $keys) ) $keys = array ( $keys );                  // and ensure that it's an array
		
		$whereClause = array ();                                           // create the whereClause array
		foreach ( $keys as $key ) 
		{
			if ( isset ( $this->{$key} ) )           // and populate it
			{
				$whereClause[] = "`{$key}` = " . $this->formatField ( $key, $this->{$key} );
			}
		}
		
		if ( count ( $keys ) == count ( $whereClause ) )                   // only do the query if we have enough values
		{
			$key = ($key) ? $key : $this->{$this->_primaryKey};
			$whereClause = 'WHERE ' . implode ( ' AND ' , $whereClause );
		
			$row = $database->fetchSingle ( "
				SELECT COUNT(*) as Count 
				FROM `{$this->_tableName}` 
				{$whereClause}
			" );
			$exists = ( $row [ 'Count' ] > 0 ) ? true : false;
		}
		return $exists;
	}



	/**                                                                          
	*** Load entry from database
	*** Triggers the following hooks: onLoad(), onLoaded() (by extension)
	***                                                                           
	*** @param mixed $key optional Primary key                                   
	*** @return bool true if successful                                          
	**/
	function load ( $key=false )
	{
		$database =& $this->getDatabase ( );
		if ( !$this->loadTable () ) 
			return false;
			
		// Call the onLoad hook
		if ( method_exists ( $this, 'onLoad' ) ) $this->onLoad();    
		
		// Count fields set
		$fieldsSet = 0; $limit = 500;
		foreach ( $this->_table->_fieldNames as $field )
		{
			if ( isset ( $this->{$field} ) ) $fieldsSet++;
			if ( $fieldsSet > $limit ) break;
		}
		// Load by primary key
		if ( 
			!empty ( $this->_primaryKey ) && 
			!is_array ( $this->_primaryKey ) && 
			( $key || $this->{$this->_primaryKey} ) 
		)
		{
			$key = ( $key ) ? $key : $this->{$this->_primaryKey};
			$query = "SELECT * FROM `{$this->_tableName}` WHERE `{$this->_primaryKey}` = " . $this->formatField ($this->_primaryKey,$key);
			$array = $database->fetchRow ( $query );
			$this->_lastQuery = $query;
			if ( is_array ( $array ) )
			{
				$this->{$this->_primaryKey} = $key;
				$this->setFromArray ( $array );
			}
			else 
			{
				return false;
			}
		}
		// Fallback to findSingle()
		else if ( $fieldsSet > 0 )
		{
			if ( $newobj = $this->findSingle() )
				$this->setFromObject( $newobj );
		}
		else
		{
			return false;
			//trigger_error ( "dbObject: Loading failed, no values set.", E_USER_WARNING );
		}
		if ( $this->_autoLoad )
		{
			$this->loadRelations ( -1 );
		}
		// Call the onloaded
		if ( method_exists ( $this, 'onLoaded' ) ) $this->onLoaded();    

		// Return the proper boolean bit conditionally
		if ( $this->{$this->_primaryKey} > 0 )
		{
			return $this->_isLoaded;			
		}
		$this->_isLoaded = false;
		return false;
	}



	/**                                                                          
	*** Save entry to database.                                                  
	***                                                                          
	*** If $this->($this->_primaryKey) is unset or non-existant, a new row will  
	*** be inserted, and the primary key will be changed to reflect the new row. 
	***                                                                          
	*** If not, the existing row will be updated.                                
	***                                                                          
	*** Triggers the following hooks: onSave(), onSaved()                        
	***                                                                          
	*** @return bool true if successful                                          
	**/
	function save ()
	{
		$database =& $this->getDatabase ( );
		$this->loadTable ();
		if ( !$this->_table ) return false;
	
		if ( method_exists ( $this, 'onSave' ) )  $this->onSave (); // Call the onSave hook
	
		$fieldsTouched = 0;

		$keys = $this->_primaryKey; if ( !is_array( $keys ) ) $keys = array( $keys );
		
		// Update existing row
		if ( count( $keys ) && ( count( array_intersect( array_keys( get_object_vars( $this ) ), $keys ) ) == count( $keys ) ) && $this->exists() )
		{
			$queryValues = array ();
			foreach ( $this->_table->getFieldNames () as $field )
			{
				if ( $this->_loadedObject && is_object( $this->_loadedObject ) )
				{
					if ( isset ( $this->$field ) && $field != $this->_primaryKey && $this->$field != $this->_loadedObject->$field )
					{
						$queryValues[] = "`{$field}` = ".$this->formatField ( $field, $this->$field );
					}
				}
				else
				{
					if ( isset ( $this->$field ) && $field != $this->_primaryKey )
					{
						$queryValues[] = "`{$field}` = ".$this->formatField ( $field, $this->$field );
					}
				}
				$fieldsTouched++;
			}
			$queryValues = implode( ', ', $queryValues );
			$whereClause = array();
			foreach ( $keys as $key )
			{
				$whereClause[] = "`{$key}` = ".$this->formatField( $key, $this->{$key} );
			}
			$whereClause = implode( ' AND ', $whereClause );
			
			if( !$queryValues || !trim( $queryValues ) )
				return false;
			
			$query = "UPDATE `{$this->_tableName}` 
					  SET {$queryValues} 
					  WHERE {$whereClause}"; 
		}
		// Insert a new row
		else
		{
			$queryFields = array ();
			$queryValues = array ();
			if ( $fieldNames = $this->_table->getFieldNames () )
			{
				foreach ( $fieldNames as $field )
				{
					if ( isset ( $this->$field ) )
					{
						$queryFields[] = "`{$field}`";
						$queryValues[] = $this->formatField ( $field, $this->$field );
						$fieldsTouched++;
					}
				}
				// A little hack to save empty objects..
				if ( $fieldsTouched == 0 )
				{
					$queryFields[] = '`' . $this->_primaryKey . '`';
					$queryValues[] = '0';
					$fieldsTouched++;
				}
				$queryFields = implode( ', ', $queryFields );
				$queryValues = implode( ', ', $queryValues );
				$query = "INSERT INTO `{$this->_tableName}` ( {$queryFields} ) VALUES ( {$queryValues} )";
			}
		}
		
		// Do the query
		if ( $fieldsTouched > 0 )
		{
			try
			{
				$database->query ( $query );
			}
			catch( Exception $e )
			{
				die( 'Error executing query: ' . $e->getMessage() );
			}
			$this->_lastQuery = $query;
			$this->{$this->_primaryKey} = ( $this->{$this->_primaryKey} ) ? $this->{$this->_primaryKey} : $database->getId ();
			$this->unslash();
			if ( method_exists ( $this, 'onSaved' ) ) $this->onSaved ();
			$this->_isLoaded = true;
		}
		return $fieldsTouched;
	}



	/**                                                                          
	*** Delete entry from database.                                              
	***                                                                          
	*** Triggers the following hooks: onDelete(), onDeleted()                    
	***                                                                          
	*** @return bool true if successful                                          
	**/
	function delete ()
	{
		$database =& $this->getDatabase ( );
		
		// Ensure that connections are deleted aswell
		if ( $this->_tableName && $this->{$this->_primaryKey} )
		{
			/* todo: SUPPORT MULTIPLE PRIMARY KEYS! */
			$database->query ( "
				DELETE FROM `ObjectConnection` WHERE 
					`ConnectedObjectType`=\"{$this->_tableName}\" AND
					`ConnectedObjectID`='" . $this->{$this->_primaryKey} . "'"  
			);
			$database->query ( "
				DELETE FROM `ObjectConnection` WHERE 
					`ObjectType`=\"{$this->_tableName}\" AND
					`ObjectID`='" . $this->{$this->_primaryKey}  . "'" 
			);
			
			// Delete permissions
			$database->query ( 'DELETE FROM `ObjectPermission` WHERE `ObjectID`=\'' . $this->ID . '\' AND `ObjectType`="' . $this->_tableName . '"' );
			
			// Clean up extra fields
			$database->query ( 'DELETE FROM ContentDataSmall WHERE ContentID=\'' . $this->ID . '\' AND ContentTable="' . $this->_tableName . '\'' );
			$database->query ( 'DELETE FROM ContentDataBig WHERE ContentID=\'' . $this->ID . '\' AND ContentTable="' . $this->_tableName . '\'' );
		}
		
		$this->loadTable ();
	
		if ( method_exists ( $this, 'onDelete' ) )
			$this->onDelete ();  // Call the onDelete hook
		
		$query = "DELETE FROM `{$this->_tableName}` WHERE ";
		
		$keys = $this->_primaryKey;
		if ( !is_array( $keys ) ) $keys = array ( $keys );
		
		for ( $i = 0; $i < sizeof( $this->_primaryKey ); $i++ )
		{
			if ( $i > 0 ) $query .= 'AND ';
			$query .= "`{$keys[$i]}` = " .$this->formatField ( $keys[$i], $this->{$keys[$i]} ) . " ";
		}
		
		$this->_lastQuery = $query;
		
		if ( $database->query ( $query ) )
		{
			if ( method_exists ( $this, 'onDeleted' ) )  $this->onDeleted (); // Call the onDeleted hook
			$this->removeFromParents();
			
			// Clean up dangling permissions / objects and extra fields
			$pk = $this->{$this->_primaryKey};
			$database->query ('DELETE FROM ContentDataBig WHERE ContentTable="'.$this->_tableName.'" AND ContentID NOT IN ( SELECT `'.$pk.'` FROM `'.$this->_tableName.'` )');
			$database->query ('DELETE FROM ContentDataSmall WHERE ContentTable="'.$this->_tableName.'" AND ContentID NOT IN ( SELECT `'.$pk.'` FROM `'.$this->_tableName.'` )');
			$database->query ('DELETE FROM ObjectPermission WHERE ObjectType ="'.$this->_tableName.'" AND ObjectID NOT IN ( SELECT `'.$pk.'` FROM `'.$this->_tableName.'` )');
			$database->query ('DELETE FROM ObjectConnection WHERE ObjectType="'.$this->_tableName.'" AND ObjectID NOT IN ( SELECT `'.$pk.'` FROM `'.$this->_tableName.'` )');
			$database->query ('DELETE FROM ObjectConnection WHERE ConnectedObjectType="'.$this->_tableName.'" AND ConnectedObjectID NOT IN ( SELECT `'.$pk.'` FROM `'.$this->_tableName.'` )');
			return true;    
		}
		else
		{
			return false;
		}
	}

	/**
	*** Get total count of connected objects
	**/
	function countObjects ( )
	{
		$count = new dbObject ( 'ObjectConnection' );
		$count->ObjectType = $this->_tableName;
		$count->ObjectID   = $this->ID;
		return $count->findCount ();
	}

	
	
	/**                                                                          
	*** Set values from a submitted form.                                        
	***                                                                           
	*** @param  array  $array  Array to set from (usually $_REQUEST)             
	*** @param  string $prefix Field prefix                                      
	*** @return int            Number of rows affected                           
	**/   
	function receiveForm ( $array, $prefix = '' )
	{
		$this->loadTable ();
		$fields = $this->_table->getFieldNames ();
		$affected = 0;
		
		foreach ( $array as $k=>$v )
		{
			if ( $prefix )
			{
				$klen = strlen ( $prefix );
				$k = substr ( $k, $klen, strlen ( $k ) - $klen );
			}
			foreach ( $fields as $field )
			{
				// Check for field type
				list ( $fieldType ) = explode ( '(', $this->_table->getFieldType ( $field ) );
			
				if ( $field == $k && ( $field != $this->_primaryKey || !$this->_isLoaded ) )
				{    		     	
					$value = $array[ $prefix . $field ];
					// Check if field is checkbox value or not (different type of bool in some browsers )
					switch ( $fieldType )
					{
						case 'tinyint':
							if ( $value == 'on' || $value == 'off' || empty ( $value ) )
								$value = ( $value == 'on' )?( 1 ):( 0 );
							break;
						default:
							break;
					}
					$this->{$field} = $value;        
					$affected++;
				}
			}
		}        
		return $affected;
	}



	
	/////////////////////////////////////////////////////////
	////     SEARCHING                                   ////
	/////////////////////////////////////////////////////////



	/**                                                                          
	*** Add a clause to the SQL query. Valid values for $clause are:             
	*** - "ORDER BY"   ex: addClause ( "ORDER BY", "Name DESC" )                 
	*** - "WHERE"      ex: addClause ( "WHERE", "DateStart > NOW()" );           
	*** - "LIMIT"      ex: addClause ( "LIMIT", "10" );                          
	***                                                                          
	*** If chosen clause already has a value, the new value will be appended.    
	***                                                                           
	*** @param  string $clause Clause to add                                     
	*** @param  string $value  Value to add                                      
	*** @return void                                                             
	**/
	function addClause( $clause, $value )
	{
		if ( !$value || !$clause ) return;
		if ( !is_array ( $this->_clauses ) ) $this->_clauses = Array ( );
		switch ( strtolower ( $clause ) )
		{
			case 'where':
				if ( !$this->hasClause ( 'WHERE' ) ) $this->_clauses[ 'WHERE' ] = '';
				$this->_clauses['WHERE']    .= strlen( $this->_clauses['WHERE'] ) ? ( ' AND ' ) : ( '' );
				$this->_clauses['WHERE']    .= $value;
				break;
			case 'order by':
				if ( !$this->hasClause ( 'ORDER BY' ) ) $this->_clauses[ 'ORDER BY' ] = '';
				$this->_clauses['ORDER BY'] .= strlen ( $this->_clauses['ORDER BY'] ) ? ( ', ' ) : ( '' );
				$this->_clauses['ORDER BY'] .= $value;
				break;
			case 'limit':
				$this->_clauses [ 'LIMIT' ]  = $value;
				break;
			case 'group by':
				if ( !$this->hasClause ( 'GROUP BY' ) ) $this->_clauses[ 'GROUP BY' ] = '';
				$this->_clauses[ 'GROUP BY' ]    .= strlen( $this->_clauses[ 'GROUP BY' ] ) ? ( ' AND ' ) : ( '' );
				$this->_clauses[ 'GROUP BY' ]    .= $value;
				break;
		}
	}

	function hasClause ( $clause )
	{
		if ( is_array ( $this->_clauses ) )
		{
			if ( array_key_exists ( $clause, $this->_clauses ) )
				return true;
		}
		return false;
	}

	/**                                                                          
	***  Perform a query based on the parameters given, and return an array       
	***  of objects from the resultset.                                           
	***                                                                           
	***  Example 1 - find all pages with parent = 15:                             
	***                                                                           
	***    $pages = new dbObject ( "Page" );                                      
	***    $pages->Parent = 15;                                                   
	***    $pages = $pages->find();                                               
	***                                                                           
	***  Example 3 - Use $this->addClause() to limit and sort the results:        
	***                                                                           
	***    $pages = new dbObject ( "Page" );                                      
	***    $pages->addClause ( "LIMIT", 100 );                                    
	***    $pages->addClause ( "ORDER BY", "DatePosted DESC" );                   
	***    $pages = $pages->find();                                               
	***                                                                           
	***  @param  string $customQuery Specific query to override with              
	***  @return array               of dbObjects                                 
	**/
	function find ( $customQuery = false )
	{
		global $debug;
		$database =& $this->getDatabase ( );
		$ids = $matches = array ();

		if ( method_exists ( $this, 'onFind' ) ) $this->onFind ();
		
		$query = $customQuery ? $customQuery : false;
		
		if ( !$query )
		{
			// Build query
			$query = "SELECT ID FROM `{$this->_tableName}`";
			
			if ( $this->_table->_fieldNames )
			{
				foreach ( $this->_table->_fieldNames as $field )
				{
					if ( isset ( $this->{$field} ) )      
					{
						$queryWhere[] = "`{$field}` = ".$this->formatField ( $field, $this->{$field} );
					}
				}
			}
			
			if ( $this->hasClause ( 'WHERE' ) )
				$queryWhere[] = $this->_clauses['WHERE'];
			
			if ( count( $queryWhere ) > 0 )
				$query .= ' WHERE ' . implode( ' AND ', $queryWhere ) . ' ';
			
			$query .= $this->hasClause ( 'GROUP BY' ) ? 
				( ' GROUP BY ' . $this->_clauses[ 'GROUP BY' ] ) : '';
			$query .= $this->hasClause ( 'ORDER BY' ) ? 
				( ' ORDER BY ' . $this->_clauses[ 'ORDER BY' ] ) : '';
			$query .= $this->hasClause ( 'LIMIT' ) ? 
				( ' LIMIT ' . $this->_clauses[ 'LIMIT' ] ) : '';	
		}
		
		// Fetch the results
		$rows = $database->fetchRows ( $query, MYSQLI_ASSOC );
		$this->_lastQuery = $query;
		
		$matches = false;
		
		if ( $rows ) 
		{
			$matches = array ();
			$className = get_class ( $this );
			foreach ( $rows as $row )
			{	
				if ( $className != 'dbObject' ) 
				{
					$obj = new $className(); $obj->_dbOverride =& $this->_dbOverride;
				}
				else $obj = new dbObject ( $this->_tableName, $this->_dbOverride );
				if ( $obj->load ( $row[ 'ID' ] ) ) $matches[] = $obj;
			}
		}
		
		// Remove clauses to avoid problems with a new query assumed with blank rules
		$this->_clauses = array ();
		
		// Return results or false
		return $matches;
	}

	
	
	/**
	***  Find a single object.
	***
	***  @param  string $customQuery Specific query to override with              
	***  @return mixed               dbObject of correct type
	**/
	function findSingle ( $customQuery = '' )
	{
		$this->addClause ( 'LIMIT', 1 );
		if ( $results = $this->find ( $customQuery ) )
		{
			return $results[ 0 ];
		}
		else
		{
			return false;
		}
	}
	
	function findCount ( $customQuery = false )
	{
		$database =& $this->getDatabase ( );
		$ids = $matches = array ();
	
		$query = ($customQuery) ? $customQuery : false;
		if ( !$query )
		{
			// Build query
			$query = "SELECT COUNT(*) as Count FROM `{$this->_tableName}`";
			
			if ( $this->_table->getFieldNames() )
			{
				foreach ( $this->_table->getFieldNames() as $field )
				{
					if ( isset ( $this->{$field} ) )
						$queryWhere[] = "`{$field}` = " . 
							$this->formatField ( $field, $this->{$field} );
				}
			}
			
			if ( $this->_clauses['WHERE'] )
				$queryWhere[] = $this->_clauses['WHERE'];
			
			if ( count( $queryWhere ) > 0 )
				$query .= ' WHERE ' . implode( ' AND ', $queryWhere ). '';
			
			$query .= isset( $this->_clauses['GROUP BY'] ) ? 
				( ' GROUP BY ' . $this->_clauses[ 'GROUP BY' ] ) : '';
			$query .= isset( $this->_clauses['ORDER BY'] ) ? 
				( ' ORDER BY ' . $this->_clauses[ 'ORDER BY' ] ) : '';
			$query .= isset( $this->_clauses['LIMIT'] ) ? 
				( ' LIMIT ' . $this->_clauses[ 'LIMIT' ] ) : '';
		}	
		
		// Fetch the results
		$row = $database->fetchRow ( $query );
		$this->_lastQuery = $query;
	
		return ( $row['Count'] );
	}
	
	function searchTerms ( $searchTerms )
	{
		if ( !is_array( $searchTerms ) )
		{
			$searchTerms = ' ' . $searchTerms;                                                                               // prepend with a space
			$searchTerms = str_replace ( ' -', ' %2D', $searchTerms );                                                     // replace minus with urlencode equivalent
			$searchTerms = preg_replace ( "/(\"|')([\W\w]+)(\"|')/Ue", 'str_replace( \' \', \'%20\', \'$2\' )', $searchTerms );  // replace spaces in quoted terms with urlencoded space
			$searchTerms = trim ( $searchTerms );                                                                          // trim away whitespace chars
			$searchTerms = preg_split ( "/[\s]+/", $searchTerms );                                                         // split the terms into an array
		}
		return $searchTerms;
	}
	
	function searchFields ( )
	{
		$fields = false;
		foreach ( $this->_table->getFields () as $field )
		{
			if ( preg_match ( "/(char|text|enum)/", $field->Type ) )
			{
				$fields[] = $field->Field;
			}
		}
		return $fields;
	}
	
	function searchScore ( $searchTerms = false )
	{
		$searchTerms = ( $searchTerms ) ? $searchTerms : $this->_searchTerms;
		$searchTerms = dbObject::searchTerms( $searchTerms );
		$score = 0;
		$fields = $this->searchFields();
		for ( $i = 0; $i < count ( $fields ); $i++ )
		{
			$field = $fields[$i];
			foreach ( $searchTerms as $term )
			{
				$fieldAdjust = 2 - $i / count ( $fields );
				$score += ( substr_count ( strtolower( $this->{$field}), strtolower( $term ) ) ) * $fieldAdjust;
			}
		}
		return $score;
	}
	
	/**
	*** Compare to objects by $object->searchScore ()
	**/
	function searchScoreSort ( &$a, &$b )
	{
		$a_score = $a->searchScore ();
		$b_score = $b->searchScore ();
		if ( $a_score == $b_score ) return 0;
		return ( $a_score < $b_score ) ? 1 : -1;
	}
	
	function search ( $searchTerms )
	{
		$searchTerms = dbObject::searchTerms( $searchTerms ); // massage the search terms a bit, and make sure they're in an array.
		
		$searcher = dbObject::getClassName(); // get the class of this object
		$searcher = new $searcher (); // and create a new object
		if ( !$searcher->_tableName && $this->_tableName )
		{
			$searcher->_tableName = $this->_tableName;
			$searcher->loadTable ();
		}
		
		$query = "SELECT * FROM `{$searcher->_tableName}`";
		
		for ( $i = 0; $i < count( $searchTerms ); $i++ )
		{
			$searchTerms[$i] = urldecode ( $searchTerms[$i] ); // urldecode each term
			$temp = array ();
				foreach ( $searcher->_table->getFields () as $field )
				{
					if ( preg_match ( "/(char|text|enum)/", $field->Type ) )
					{
						$temp[] = 
							( substr( $searchTerms[$i], 0, 1 ) != '-' ) ? 
							"`{$field->Field}` LIKE \"%{$searchTerms[$i]}%\"" : 
							"`{$field->Field}` NOT LIKE \"%".substr($searchTerms[$i],1)."%\"";
					}
				}
				if ( count ( $temp ) > 0 ) 
				{
					$queryWhere[] = 
						( substr( $searchTerms[$i], 0, 1 ) != '-' ) ? 
						( '( ' . implode ( ' OR ', $temp ) . ' )' ) : 
						( '( ' . implode ( ' AND ', $temp ). ' )' );
				}
			}
		
			if ( $this->_clauses['WHERE'] )           $queryWhere[] = $this->_clauses['WHERE'];
			
			if ( count( $queryWhere ) > 0 )
			{
				$query .= ' WHERE ' . implode( ' AND ', $queryWhere ). ' ';
			}
			
			$query .= ( $this->_clauses ['GROUP BY'] ) ? ( ' GROUP BY ' . $this->_clauses[ 'GROUP BY' ] ) : '';
			$query .= ( $this->_clauses ['ORDER BY'] ) ? ( ' ORDER BY ' . $this->_clauses[ 'ORDER BY' ] ) : '';
			$query .= ( $this->_clauses ['LIMIT']    ) ? ( ' LIMIT ' . $this->_clauses[ 'LIMIT' ] ) : '';
			
			if ( $results = $searcher->find ( $query ) )
			{
				for ( $i = 0; $i < count ( $results ); $i++ )
				{
					$results[$i]->_searchTerms = $searchTerms;
				}
				usort( $results, array ( 'dbObject', 'searchScoreSort' ) );
			}

			if ( !$results ) $results = array();
			
			return $results;
	}

	/////////////////////////////////////////////////////////
	////     DATABASE RELATIONS                          ////
	/////////////////////////////////////////////////////////

	function loadParent ( )
	{
		// meep
		$this->loadParents ( 0 );
	}
	
	function loadRelations ( $levels = 0 )
	{
		$this->loadChildren( $levels );
	}
	
	function hasParent ( &$object )
	{
		if ( 
			$object && $object->_tableName && 
			is_array( $this->_relations['parents'] ) && 
			count( $this->_relations['parents'] ) > 0 
		)
		{
			foreach ( $this->_relations['parents'] as $parent )
				if ( $object->compare( $parent ) ) return true;
		}
		return false;
	}
	
	function addParent ( &$object )
	{
		if ( $object && $object->_tableName && !$this->hasParent( $object ) )
		{
			$this->_relations['parents'][] =& $object;
			return true;
		}
		return false;
	}
	
	function removeFromParents ()
	{
		if ( is_array( $this->_relations['parents'] ) && count( $this->_relations['parents'] ) > 0 )
		{
			for ( $i = 0; $i < count ( $this->_relations['parents'] ); $i++ )
				$this->_relations['parents'][$i]->removeChild( $this );
		}
	}
	
	function removeChild ( &$object )
	{
		if ( is_object( $object ) && $object->_tableName && $this->_relations['hasMany'][$object->_tableName] )
		{
			$children    =& $this->_relations['hasMany'][$object->_tableName];
			$newChildren = array ();
			for ( $i = 0; $i < count ( $children ); $i++ )
			{
				if ( !$object->compare( $children[$i] ) )
					$newChildren[] =& $children[$i];
			}
			$this->_relations['hasMany'][$object->_tableName] = $newChildren;
		}
	}
	
	/**
	*** Count children
	***
	*** @return numeric Number of childrens loaded
	**/
	function countChildren ()
	{
		$childCount = 0;
		foreach ( $this->_relations['hasMany'] as $children ) 
			$childCount += count ( $children );
		return $childCount;
	}
	
	/**
	*** Load this object's children (as specified by $this->_hasMany)
	*** (this is hardcore)
	***
	*** @param numeric $levels Number of recursions, -1 means load all.
	**/
	function loadChildren ( $levels = 0, $recursive=false )
	{
		$database =& $this->getDatabase ( );
		$ruleFilter = "/([\w\d_-]+)\(([\w\d\s>_+-]+)\)([\w\d\s_-]*)/";     // Regular expression to match rules against
		
		// Load children
		if ( isset ( $this->_hasMany ) )
		{
		 	// Split the rules by comma
			$hasMany = explode ( ',', $this->_hasMany );
			for ( $i = 0; $i < count ( $hasMany ); $i++ )
			{
				preg_match ( $ruleFilter, $hasMany[$i], $matches );
				$hasMany [ $i ] = array ();
				// Table name / object type
				$hasMany [ $i ][ 'table' ]   = trim ( $matches[1] );
				// Name of foreign key or link table
				$hasMany [ $i ][ 'foreign' ] = trim ( $matches[2] );
				// Optional name of property for the array (ie, $this->targets)
				$hasMany [ $i ][ 'target' ]  = trim ( $matches[3] );
			}
			
			// Skip loading children if the hasMany flag has been set
			if ( !$this->loadState( 'hasMany' ) )
			{
				//  each table this object is related to
				for ( $a = 0; $a < count ( $hasMany ); $a++ )
				{
					// Shorthand: $tableName
					$tableName  = $hasMany[ $a ]['table'];
					// Shorthand: $foreignKey
					$foreignKey = $hasMany[ $a ]['foreign'];
					// Shorthand: $target 
					$target     = $hasMany[ $a ]['target'];
					
					// Get a dbObject for reference
					$table = dbObject::create ( $tableName );
					
				
					// If the foreign key matches this pattern, we should get relations 
					// from a relations table. Most likely, this is a many-many relation.
					if ( preg_match ( 
						"/([\w\d_-]+)>([\w\d_-]+)\+([\w\d_-]+)/", $foreignKey, $matches ) 
					)
					{
						$query = "SELECT a.{$matches[2]} as `Primary`, a.{$matches[3]} as `Foreign` 
								FROM `{$matches[1]}` a, `{$table->_tableName}` b
								WHERE b.{$table->_primaryKey} = a.{$matches[3]}";
					}
					
					// Plain one-to-many relation. Get the relation from the target object's foreign key
					else
					{
						$query = "SELECT a.{$this->_primaryKey} as `Primary`, b.{$table->_primaryKey} AS `Foreign`
								FROM `{$this->_tableName}` a, `{$table->_tableName}` b
								WHERE a.{$this->_primaryKey} = b.{$foreignKey}";
					}
					
					// Sort the relations by the target object's _orderBy property (if set)
					if ( isset( $table->_orderBy ) )
					{
						$orderBy = explode( ',', $table->_orderBy );
						for ( $i = 0; $i < count( $orderBy ); $i++ ) $orderBy[$i] = 'b.' . trim( $orderBy[$i] );
						$query .= "\nORDER BY " . implode(', ', $orderBy );
					}
						
					// Perform the query 
					$relations = $database->fetchRows ( $query );
					
					// Step through all relations, add the ones relevant to $this to $related
					$related  = array (); 
					if ( is_array ( $relations ) && count ( $relations ) > 0 )
					{
						foreach ( $relations as $relation )
						{
							if ( $relation['Primary'] == $this->{$this->_primaryKey} )
							{
								$related[] = $relation;
							}
						}
					}
					
					// Initialize the relations array
					$this->_relations['hasMany'][$tableName] = array ();
					
					// Step through related
					if ( count ( $related ) > 0 ) 
					{
						foreach ( $related as $relation )
						{
							// ..and add the ones where the primary key matches the primary key on $this
							if ( $relation['Primary'] == $this->{$this->_primaryKey} )
							{
								// Try to get the object. Only existing objects will be added
								if ( $object =& dbObject::get( $relation['Foreign'], $tableName ) )
								{
									$object->addParent ( $this );              // Make sure the object knows that
																				// $this is one of it's parents.
									$this->_relations['hasMany'][$tableName][] =& $object;
								}
							}
						}
					}
					if ( $target && count ( $this->_relations['hasMany'][$tableName] ) > 0 )
						$this->$target =& $this->_relations['hasMany'][$tableName];
				}
				$this->loadState( 'hasMany', true );                       // Set the loadState flag,
			}
			
			
			// Loading should be recursive if $levels n <> 0. If n > 0, n recursions will be done. If n < 0, 
			// all relations will be loaded. Additionally, this step will be skipped if either: they've already 
			// been loaded at an equal or higher level of recursion
			$doChildren = false;
			if ( $levels != 0 )
			{
				if ( $levels > 0 && ( !$this->loadState( 'hasManyChildren' ) ||  $this->loadState( 'hasManyChildren' ) < $levels ) )
					$doChildren = true; 
				if ( $levels < 0 && ( !$this->loadState( 'hasManyChildren' ) || !$this->loadState( 'hasManyChildren' ) > 0 ) )
					$doChildren = true;
			}
			if ( $doChildren )
			{
				$childlevel = ( $levels > 0 ) ? $levels -1 : $levels;      // $childLevel will be passed to the recursion,
																		// decrement it if $levels is positive.
				foreach ( $hasMany as $a )
				{
					$tableName  = $a['table'];
					if ( is_array ( $this->_relations['hasMany'][$tableName] ) && count ( $this->_relations['hasMany'][$tableName] ) > 0 )
					{
						for ( $i = 0; $i < count ( $this->_relations['hasMany'][$tableName] ); $i++ )
						{
							$this->_relations['hasMany'][$tableName][$i]->loadChildren( $childlevel, true );
						}
					}
				}
				$this->loadState( 'hasManyChildren', $levels );            // Set the loadState flag
			}
		}
	}
	
	
	// these should be replaced..	
	function getRelationObject ( $table )
	{
		$className = "db{$table}";
		if ( class_exists ( $className ) )  $table = new $className ();
		else                                $table = dbObject::create ( $table );
		return $table;
	}
	
	function loadParents ( $levels = 0 )
	{
		$database =& $this->getDatabase ( );
		$ruleFilter = "/([\w\d_-]+)\(([\w\d_-]+)\)([\w\d\s_-]*)/";     // Regular expression to match
		
		//////////// BELONGS-TO RELATION (ie, the parent) ////////////////
		if ( isset ( $this->_belongsTo ) )
		{
			$belongsTo = explode ( ',', $this->_belongsTo );
			for ( $i = 0; $i < count ( $belongsTo ); $i++ )
			{
				preg_match ( $ruleFilter, $belongsTo[$i], $matches );
				$belongsTo [ $i ] = array ();
				$belongsTo [ $i ][ 'table' ]   = trim ( $matches[1] );         // Table name
				$belongsTo [ $i ][ 'foreign' ] = trim ( $matches[2] );         // Name of foreign key
				$belongsTo [ $i ][ 'target' ]  = trim ( $matches[3] );         // Optional name of property (ie, $this->targets)
			}
		
			if ( !$this->loadState( 'belongsTo' ) )
			{
				foreach ( $belongsTo as $a )
				{
					$table = $tableName  = $a [ 'table' ];
					$foreignKey = $a [ 'foreign' ];
					$target     = $a [ 'target' ];
					
					// old code
					$parent     = $this->getRelationObject ( $table );
					if ( $this->{$foreignKey} && $parent =& $parent->get ( $this->{$foreignKey}, $parent->_tableName ) )
					{
						$this->_relations['belongsTo'][ $parent->_tableName ] =& $parent;
						if ( $target )
						{
							$this->{$target} =& $this->_relations['belongsTo'][ $parent->_tableName ];
						}
					}
				}
				$this->loadState( 'belongsTo', true );
			}
		}
	}
	
	/**
	*** Serialize object to XML (w/o header)
	**/
	function toXML ( $indent_level = 1 )
	{
		$indent_char = "\t";
		$indent = str_pad( '', $indent_level * strlen( $indent_char ), $indent_char );
		
		$primaryKey = $this->_primaryKey;
		if ( is_array ( $primaryKey ) ) $primaryKey = implode ( ',', $primaryKey );
		$output  = "{$indent}<dbObject type=\"{$this->_tableName}\" primarykey=\"{$primaryKey}\">\n";
		
		// fields
		foreach ( $this->_table->getFieldNames () as $field )
		{
			$output .= "{$indent}{$indent_char}<{$field}>".$this->formatForXML( $this->{$field} )."</{$field}>\n";
		}
		
		// relations
		$ruleFilter = "/([\w\d_-]+)\(([\w\d_-]+)\)([\w\d\s_-]*)/";     // Regular expression to match
		$hasMany = explode ( ",", $this->_hasMany );                   // Split the rules by comma
		for ( $i = 0; $i < count ( $hasMany ); $i++ )
		{
			preg_match ( $ruleFilter, $hasMany[$i], $matches );        // ..and match them against the rule filter
			$hasMany [ $i ] = array ();
			$hasMany [ $i ][ 'table' ]   = trim ( $matches[1] );       // Table name / object type
			$hasMany [ $i ][ 'foreign' ] = trim ( $matches[2] );       // Name of foreign key or link table
			$hasMany [ $i ][ 'target' ]  = trim ( $matches[3] );       // Optional name of property for the array (ie, $this->targets)
		}
		
		foreach ( $hasMany as $child ) if ( $child ['target'] && count ( $this->{$child ['target']} ) )
		{
			$output .= "{$indent}{$indent_char}<hasMany type=\"{$child['table']}\" name=\"{$child['target']}\">\n";
			foreach ( $this->{$child ['target']} as $sub )
				$output .= $sub->toXML ( $indent_level + 2 );
			$output .= "{$indent}{$indent_char}</hasMany>\n";
		}
		
		$output .= "{$indent}</dbObject>\n";
		return $output;
	}
	
	function formatForXML ( $value )
	{
		$value = preg_replace( "/&/", '&amp;', $value );
		return $value;
	}

	/////////////////////////////////////////////////////////
	////     STATIC FUNCTIONS                            ////
	/////////////////////////////////////////////////////////

	/**
	*** Returns boolean true if the $class_name is a subclass of dbObject, and has a $_tableNameMatch var.
	**/
	function tableNameMatchFilter( $class_name )
	{
		// Check if the class is inherited from dbObject.
		// This is PHP4, so we'll have to use a while loop and match the classname as a string.. ugh.
		$parent_class = get_parent_class( $class_name );
		while( $parent_class )
		{
			if ( strtolower( $parent_class ) == 'dbobject' ) $isdbobj = true;
			$parent_class = get_parent_class( $parent_class );
		}

		if ( $isdbobj && array_key_exists( '_tableNameMatch', get_class_vars( $class_name ) ) )
		{
			return true;
		}
		return false;
	}
	
	/**                                                                          
	*  Set a global value                                                       
	*  (hacky-patchy workaround for PHP4)
	**/
	public static function globalValueSet ( $key, &$new )
	{
		$GLOBALS[ '__GLOBALS__' ][ 'dbObject' ][ $key ] =& $new;
	}
	
	public static function &globalValueUnset ( $key )
	{
		if ( isset ( $GLOBALS[ '__GLOBALS__' ][ 'dbObject' ][ $key ] ) )
		{
			unset ( $GLOBALS[ '__GLOBALS__' ][ 'dbObject' ][ $key ] );
		}
		return true;
	}
	
	/**                                                                          
	*  Retrieve a global value                                                  
	*  (hacky-patchy workaround for PHP4)
	**/
	public static function &globalValue ( $key ) 
	{
		// Commented out because doesn't work in static mode ...
		/*if ( $this && $key == 'database' && isset ( $this->_dbOverride ) )
		{
			return $this->_dbOverride;
		}*/
		if ( !isset ( $GLOBALS[ '__GLOBALS__' ][ 'dbObject' ][ $key ] ) )
		{
			$rv = false;
			return $rv;
		}
		return $GLOBALS[ '__GLOBALS__' ][ 'dbObject' ][ $key ];
	}
	
	/**
	 * Grants authobj permissions on this object
	 * 
	 * $authObj = object which has the permission
	 * $permission = which permission is set
	 * $value = the value of the permission (0|1)
	 * $permissiontype = which permission type to apply to
	**/
	function grantPermission ( $authObj, $permission, $value = '1', $permissiontype = 'web' )
	{
		global $Session;
		
		// We don't need to grant the core users anything, they have all access rights
		if ( $authObj->_dataSource == 'core' ) return;
		
		// Set global permission (all users and groups and objects)
		if ( $authObj == 'global' )
		{
			$obj = new dbObject ( 'ObjectPermission' );
			$obj->AuthType = 'GlobalPermission';
			$obj->AuthID = '0';
			$obj->ObjectType = $this->_tableName;
			$obj->ObjectID = $this->ID;
			$obj->PermissionType = $permissiontype;
			$obj->load();
		}
		// Set permission by object
		else
		{
			$obj = new dbObject ( 'ObjectPermission' );
			$obj->AuthType = $authObj->_tableName;
			$obj->AuthID = $authObj->ID;
			$obj->ObjectType = $this->_tableName;
			$obj->ObjectID = $this->ID;
			$obj->PermissionType = $permissiontype;
			$obj->load();
		}
		
		if ( is_string ( $permission ) )
		{
			switch ( strtolower ( $permission ) )
			{
				case 'read':
					$obj->Read = $value;
					break;
				case 'write':
					$obj->Write = $value;
					break;
				case 'structure':
					$obj->Structure = $value;
					break;
				case 'publish':
					$obj->Publish = $value;
					break;
				case '*':
					$obj->Read = $values;
					$obj->Write = $values;
					$obj->Structure = $values;
					$obj->Publish = $values;
					break;
				default: break;
			}
		}
		else if ( is_array ( $permission ) )
		{
			foreach ( $permission as $k => $v ) 
			{
				$obj->$k = $v;
			}
		}
		
		$obj->save();
	}
	
	/**
	 * Removes permission rules associated with object, of a permission type
	**/
	function removePermissionRule ( $authObj, $permissiontype = 'web' )
	{
		$db =& $this->getDatabase ( );
		$db->query ( '
			DELETE FROM ObjectPermission WHERE
				AuthType = "' . $authObj->_tableName . '" AND
				AuthID = \'' . $authObj->ID . '\' AND
				ObjectType = "' . $this->_tableName . '" AND
				ObjectID = \'' . $this->ID . '\' AND
				PermissionType = "' . $permissiontype . '"
		' );
	}
	
	/**
	 * Removes all permission rules on this object
	**/
	function removePermissionRules ( )
	{
		$db =& $this->getDatabase ( );
		$db->query ( '
			DELETE FROM ObjectPermission WHERE
				ObjectType = "' . $this->_tableName . '" AND
				ObjectID = \'' . $this->ID . '\'
		' );
	}
	
	/**
	 * Get permission rules for this object
	 * authobj and permissiontype is optional
	**/
	function getPermissionRules ( $authObj = false, $permissiontype = false )
	{
		global $Session;
		// Check if we have all our fields!
		if ( !$Session->pmDbCheck )
		{
			$read = false;
			$permission = false;
			$obj = new dbObject ( 'ObjectPermission' );
			foreach ( $obj->_table->getFieldNames ( ) as $field )
			{
				if ( $field == 'Read' )
					$read = true;
				if ( $field == 'Permission' )
					$permission = true;
			}
			if ( $permission )
			{
				$this->_table->database->query ( 'ALTER TABLE ObjectPermission DROP `Permission`' );
			}
			if ( !$read )
			{
				$this->_table->database->query ( 'ALTER TABLE ObjectPermission ADD `Read` tinyint(4) NOT NULL default 0' );
				$this->_table->database->query ( 'ALTER TABLE ObjectPermission ADD `Write` tinyint(4) NOT NULL default 0 AFTER `Read`' );
				$this->_table->database->query ( 'ALTER TABLE ObjectPermission ADD `Publish` tinyint(4) NOT NULL default 0 AFTER `Write`' );
				$this->_table->database->query ( 'ALTER TABLE ObjectPermission ADD `Structure` tinyint(4) NOT NULL default 0 AFTER `Publish`' );
				$this->_table->database->query ( 'ALTER TABLE ObjectPermission ADD `PermissionType` varchar(255) NOT NULL default "web" AFTER `Structure`' );
				$Session->Set ( 'pmDbCheck', true );
			}
		}
		
		$obj = new dbObject ( 'ObjectPermission' );
		$obj->ObjectType = $this->_tableName;
		$obj->ObjectID = $this->ID;
		if ( $permissiontype ) 
			$obj->PermissionType = $permissiontype;
		if ( is_object ( $authObj ) )
		{
			$obj->AuthType = $authObj->_tableName;
			$obj->AuthID = $authObj->ID;
		}
		else if ( is_string ( $authObj ) )
		{
			$obj->AuthType = $authObj;
		}
		if ( $objs = $obj->find ( ) )
			return $objs;
		else return false;
	}
	
	function checkGlobalPermission ( $targetObj, $permission = 'Read', $permissiontype = 'web' )
	{
		$p = new dbObject ( 'ObjectPermission' );
		$p->AuthType = 'GlobalPermission';
		$p->AuthID = '0';
		$p->ObjectType = $targetObj->_tableName;
		$p->ObjectID = $targetObj->ID;
		if ( $p->load () )
		{
			return $p->$permission;
		}
		return false;
	}
	
	/**
	 * Checks this objects permission on target object
	**/
	function checkPermission ( $targetObj, $permission = 'Read', $permissiontype = 'web' )
	{
		// Core user gets access to all!
		if ( $this->_dataSource == 'core' ) 
			return true;
		
		// Make permission 1st char uppercase 
		if ( $permission != 'Read' ) 
			$permission = strtoupper ( $permission[0] ) . substr ( $permission, 1, strlen ( $permission ) - 1 );
			
		// Safety margin
		if ( !$targetObj || !is_object ( $targetObj ) || !$targetObj->_isLoaded || !$targetObj->ID )
			return false;
			
		switch ( $this->_tableName )
		{
			case 'Users':
				// Check if the user has direct permissions
				$obj = new dbObject ( 'ObjectPermission' );
				$obj->AuthType = $this->_tableName;
				$obj->AuthID = $this->ID;
				$obj->ObjectType = $targetObj->_tableName;
				$obj->ObjectID = $targetObj->ID;
				$obj->PermissionType = $permissiontype;
				
				if ( $obj->load ( ) )
				{
					if ( $permission == '*' )
						$result = $obj;
					$result = $obj->$permission;
				}
				
				// Else check if the groups which the users is part of has permissions
				if ( !$result )
				{
					$db =& $this->getDatabase ( );
					$gproto = new dbObject ( 'Groups' );
					if ( $groups = $gproto->find ( '
						SELECT g.* FROM `UsersGroups` ug, `Groups` g WHERE ug.UserID = \'' . $this->ID . '\' AND ug.GroupID = g.ID
					' ) )
					{	
						foreach ( $groups as $group )
						{
							// Superadmins get all rights
							if ( $group->SuperAdmin == 1 )
							{
								return true;
							}
							if ( ( $per = $group->checkPermission ( $targetObj, $permission, $permissiontype ) ) > 0 )
							{
								$result = $per;
								break;
							}
						}
					}
				}
				return $result ? $result : dbObject::checkGlobalPermission ( $targetObj, $permission, $permissiontype );
				break;
			
			case 'ContentElement':
				if ( !$this->IsProtected ) return true;
			default:
				// This is a fallback: checks if the object is connected to the page
				$obj = new dbObject ( 'ObjectPermission' );
				$obj->AuthType = $this->_tableName;
				$obj->AuthID = $this->ID;
				$obj->ObjectType = $targetObj->_tableName;
				$obj->ObjectID = $targetObj->ID;
				$obj->PermissionType = $permissiontype;
				$perm = false;
				
				if ( $obj->load ( ) )
				{
					if ( $permission == '*' )
						return $obj;
					$perm = $obj->$permission;
				}
				return $perm ? $perm : dbObject::checkGlobalPermission ( $targetObj, $permission, $permissiontype );
				
				break;
		}
		
		return false;
	}
	
	function duplicate ( )
	{
		$class = get_class ( $this );
		$thing = new $class ( strtolower ( $class ) == 'dbobject' ? $this->_tableName : '' );
		$thing->setFromObject( $this );
	}
	
	/**
	 * Object things
	 */
	
	/**
	*** Check if object is connected to another object
	***
	*** @param  dbObject  $object
	*** @return boolean   true if connected
	**/
	function hasObject( $object )
	{
		$connection = $this->getConnectionObject( $object );
		return ( $connection->load() ) ? true : false;
	}
	
	/**
	*** Add connection to a new object
	***
	*** @param  dbObject  $object
	*** @return boolean   true if a new connection is established
	**/
	function addObject ( $object )
	{
		if ( is_array ( $object ) )
		{
			foreach ( $object as $o ) $this->addObject ( $o );
		}
		else
		{
			$connection = $this->getConnectionObject ( $object );
			if ( !$connection->load() )
			{
				$count = $this->countObjects ();
				$connection->SortOrder = $count + 1;
				$connection->save ();
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	/**
	*** Remove a connected object
	***
	*** @param  dbObject  $object
	*** @return boolean   true if connected
	**/
	function removeObject( $object )
	{
		$connection = $this->getConnectionObject ( $object );
		if ( $connection->load() )
		{
			$connection->delete();
			return true;
		}
		else
			return false;
	}
	
	/**
	*** Load and retrieve connected objects, optionally with certain criteria
	***
	*** @param  string  $criteria  criteria string, optional
	*** @param  mixed   $id        primary key, optional (for static methods)
	*** @return array   an array of dbObject compatible objects, or false
	**/
	function getObjects ( $criterias='', $id=false )
	{
		if ( !$criteria && !$id && !$this->ID ) return false;
		if ( !$id ) $id = $this->{$this->_primaryKey};
		
		list ( $type, $criteria ) = explode ( '=', $criterias );
		if ( trim ( $criteria ) && $criteria = explode ( ',', $criteria ) )
		{
			$type = trim ( $type );
			$querAr = Array ( );
			for ( $a = 0; $a < count ( $criteria ); $a++ )
			{
				$querAr[] = "( Connected$type='" . trim ( $criteria[ $a ] ) . '\' )';
			}
		}
		
		$rows = $this->_table->database->fetchRows ( "
			SELECT * FROM ObjectConnection WHERE 
				ObjectType = '{$this->_tableName}' AND ObjectID = '$id'
			" . ( 
				( count ( $querAr ) > 0 ) ? ( " AND " . join ( " OR ", $querAr ) ) : "" 
			) . '
			ORDER BY SortOrder ASC, ID ASC
		' );
			
		if ( is_array ( $rows ) ) 
		{
			$out = Array ( );
			foreach ( $rows as $row )
			{
				if ( class_exists ( 'db' . $row[ 'ConnectedObjectType' ] ) )
				{
					$classname = 'db' . $row[ 'ConnectedObjectType' ];
					$obj = new $classname ( );
					$obj->load ( $row[ 'ConnectedObjectID' ] );
				} 
				else
				{
					if ( substr ( $row[ 'ConnectedObjectType' ], 0, 5 ) == 'class' )
					{
						if ( class_exists ( 'dbClassInstance' ) )
						{
							$obj = new dbClassInstance (
								substr ( $row[ 'ConnectedObjectType' ], 5, strlen (  $row[ 'ConnectedObjectType' ] ) - 5 )
							);
						}
						else
						{
							$obj = new dbObject ( $row[ 'ConnectedObjectType' ] );
						}
					}
					else
						$obj = new dbObject ( $row[ 'ConnectedObjectType' ] );
					
					$obj->load ( $row[ 'ConnectedObjectID' ] );
				}
				if ( $obj )
					$out[] = $obj;
			}
			return $out;
		}
		return false;
	}
	
	/**
	*** Change the sorting order of products, move the specified object by $offset
	***
	*** @param  mixed     $object   dbObject compatible object
	*** @param  numeric   $offset   positive or negative offset, "up" or "down"
	*** @return boolean true on success
	**/
	function reorderObject( $object, $offset )
	{
		$proto = new dbObject ( 'ObjectConnection' );
		$proto->ObjectType = $this->_tableName;
		$proto->ObjectID = $this->ID ? $this->ID : $this->{$this->_primaryKey};
		$proto->addClause ( 'ORDER BY', 'SortOrder ASC, ID DESC' );
		if ( $connObjs = $proto->find ( ) )
		{			
			$len = count ( $connObjs );
			if ( $offset == 'up' ) $offset = -1;
			else if ( $offset == 'down' ) $offset = 1;
			else $offset = intval ( $offset );
			$result = '';
			for ( $i = 0; $i < $len; $i++ )
			{
				if ( 
					$connObjs[ $i ]->ConnectedObjectID == $object->ID && 
					$connObjs[ $i ]->ConnectedObjectType == $object->_tableName 
				)
				{
					// Move object down
					if ( $offset == 1 && $i < $len - 1 )
					{
						$connObjs[ $i ]->SortOrder = $i + 1;
						$connObjs[ $i ]->save ();
						$connObjs[ $i + 1 ]->SortOrder = $i;
						$connObjs[ $i + 1 ]->save ( );
						$i++;
						continue;
					}
					// Move object up
					else if ( $offset == -1 && $i > 0 )
					{
						$connObjs[ $i ]->SortOrder = $i - 1;
						$connObjs[ $i ]->save ( );
						$connObjs[ $i - 1 ]->SortOrder = $i;
						$connObjs[ $i - 1 ]->save ( );
					}
				}
				// Save linear sort order
				else
				{
					$connObjs[ $i ]->SortOrder = $i;
					$connObjs[ $i ]->save ( );
				}
			}
			return true;
		}
	}

	/**
	*** @private
	*** Get an (unloaded) connection meta-object for the specified object
	***
	*** @param  mixed     $object   dbObject compatible object
	*** @return dbObject
	**/
	function getConnectionObject ( $object )
	{
		$connection = new dbObject ( 'ObjectConnection' );
		$connection->ObjectType = $this->_tableName;
		$connection->ObjectID   = $this->ID;
		$connection->ConnectedObjectType = $object->_tableName;
		$connection->ConnectedObjectID   = $object->{$object->_primaryKey};
		return $connection;
	}
	
	/**
	*** @private
	*** Get an array of connected meta-objects based on criteria
	***
	*** @param  string  $criteria  criteria string, optional
	*** @param  mixed   $id        primary key, optional (for static methods)
	*** @return array   an array of dbObjects
	**/
	function getConnectionObjects ( $criteria='', $id=false )
	{
		// Get the search object
		$objects = new dbObject ( 'ObjectConnection' );
		$objects->ObjectType = $this->_tableName;
		$objects->ObjectID   = ( $id ) ? $id : $this->{$this->_primaryKey};
		
		if ( $criteria ) foreach( preg_split ( "/,[\s]*/", $criteria ) as $field )
		{
			if ( preg_match ( "/^([\w\d-_]+)[\s]*=[\s]*(.*)/", trim($field), $field ) )
			{
				if ( $field[1] == 'ObjectType' ) $field[1] = 'ConnectedObjectType';
				if ( $field[1] == 'ObjectID' )   $field[1] = 'ConnectedObjectID';
				$objects->{$field[1]} = $field[2];
			}
		}
		
		$objects->addClause ( 'ORDER BY', 'SortOrder ASC, ID ASC' );
		
		$test = $objects->find ( );
		
		return $objects->find();
	}
	
	/**
 	 * Receive extra fields from form update
 	 * Returns update javascript that can be used
	**/
	function updateExtraFields ( )
	{
		$efScripts = Array ( );
		foreach ( $_POST as $k=>$v )
		{
			if ( substr ( $k, 0, 6 ) == 'Extra_' )
			{
				list ( , $id, $type, $field ) = explode ( '_', $k );
				$obj = new dbObject ( "ContentData$type" );
				if ( $obj->load ( $id ) )
				{
					switch ( $obj->Type )
					{
						case 'script':
							$obj->DataString = '';
							$obj->$field = trim ( $v );
							break;
						case 'leadin':
						case 'text':
							// Here we make sure that the encode and decode functions match
							// explains multiple passes
							$obj->$field = arenasafeHTML ( $v );
							break;
						default:
							$obj->$field = trim ( $v );
							break;
					}
					$obj->save ( );
				}
			}
		}
		
		foreach ( $_FILES as $k=>$v )
		{
			if ( substr ( $k, 0, 6 ) == 'Extra_' )
			{
				list ( , $id, $type, $field ) = explode ( '_', $k );
				$obj = new dbObject ( "ContentData$type" );
				if ( $obj->load ( $id ) )
				{
					// Receive upload
					switch ( $obj->Type )
					{
						case 'image':
							$image = new dbImage ( );
							$image->ImageFolder = 0;
							if ( $image->receiveUpload ( $_FILES[ $k ] ) )
							{
								$image->save ( );
								$obj->$field = $image->ID;
								$imgurl = $image->getImageUrl ( 92, 48 );
								$efScripts[] = "parent.updateImagePreviews{$obj->Name}();";
								$obj->save ( );
							}
							break;
						case 'file':
							break;
						default:
							break;
					}
				}
			}
		}
		
		return $efScripts;
	}
	
	function saveExtraFields ( )
	{
		if ( $this->_dataSource == 'core' ) return;
		// Check if we have extra fields
		$class = strtolower ( get_class ( $this ) );
		$testUser = $class == 'dbobject' ? new dbObject ( $this->_tableName ) : new $class ( );
		$testUser->load ( $this->ID );
		$testUser->loadExtraFields ( );
		foreach ( $testUser as $k=>$v )
		{
			if ( substr ( $k, 0, 7 ) == '_field_' )
			{
				list ( ,,$name ) = explode ( '_', $k );
				$o = new dbObject ( key_exists ( 'DataInt', $testUser->{"_field_$name"} ) ? 'ContentDataSmall' : 'ContentDataBig' );
				$o->load ( $testUser->{"_field_$name"}->ID );
				switch ( $o->Type )
				{
					case 'script':
						$o->DataMixed = $this->$name;
						$o->DataString = '';
						break;
					case 'extension':
					case 'style':
					case 'varchar':
						$o->DataString = $this->$name;
						break;
					case 'file':
					case 'image':
					case 'newscategory':
					case 'pagelisting':
						$o->DataInt = $this->$name;
						break;
					case 'leadin':
					case 'text':
					case 'formprocessor':
						$o->DataText = $this->$name;
						break;
				}
				$o->save ( );
			}
		}
	}
	
	/** 
	 * Load in extra fields anew
	**/
	function reloadExtraFields ( )
	{
		$this->_fieldsLoaded = false;
		$this->_loadingExtrafields = false;
		$this->loadExtraFields ( );	
	}
	
	function getObjectIdentifier ()
	{
		return $this->_tableName . '_' . $this->{$this->_primaryKey};
	}
	
	function extraFieldCache ( $row )
	{
		if ( isset ( $GLOBALS[ '__GLOBALS__' ][ 'ExtraFields' ][ $row->Name ][ $row->ID ] ) )
			return $GLOBALS[ '__GLOBALS__' ][ 'ExtraFields' ][ $row->Name ][ $row->ID ];
		return false;
	}
	
	function extraFieldCacheSet ( $row )
	{
		$GLOBALS[ '__GLOBALS__' ][ 'ExtraFields' ][ $row->Name ][ $row->ID ] = $row;
	}
	
	/**
	 * Load in extra fields
	**/
	function loadExtraFields ( $options = false, $r = 0 )
	{
		if ( method_exists ( $this, 'onLoadExtraFields' ) )
			$this->onLoadExtraFields ( );
		if ( $this->_dataSource == 'core' )
			return false;
		if ( $r == 0 && $this->_loadingExtrafields )
			return false;
		if ( $r == 0 ) $this->_loadingExtrafields = true;
		if ( $r > 1 ) 
			die ( 'Error with creating duplicate fields for editmode!!' );
		if ( isset( $this->_fieldsLoaded ) && $this->_fieldsLoaded ) 
			return true;
		$db =& $this->getDatabase ( );
		
		// Register this object to receive updates for recursions
		$GLOBALS[ '__GLOBALS__' ][ 'ExtraFieldParents' ][ $this->getObjectIdentifier () ] =& $this;
		
		// ContentElement objects have working copies and published copies
		$contentoptions = $contentoptions1a = $contentoptions1b = $contentoptions2a = $contentoptions2b = '';
		if ( $this->_tableName == 'ContentElement' )
		{
			$contentoptions1a = ', `ContentElement` el1';
			$contentoptions1b = ', `ContentElement` el2';
			// Get working copy
			if ( $this->MainID != $this->ID )
			{
				$contentoptions2a = ' AND el1.MainID != el1.ID AND el1.ID = b.ContentID';
				$contentoptions2b = ' AND el2.MainID != el2.ID AND el2.ID = c.ContentID';
				if ( $this->isTemplate <= 0 )
				{
					$contentoptions2a .= ' AND el1.isTemplate = 0';
					$contentoptions2b .= ' AND el2.isTemplate = 0';
				}
			}
			// Get published version
			else
			{
				$contentoptions2a = ' AND el1.MainID = el1.ID AND el1.ID = b.ContentID';
				$contentoptions2b = ' AND el2.MainID = el2.ID AND el2.ID = c.ContentID';
				if ( $this->isTemplate <= 0 )
				{
					$contentoptions2a .= ' AND el1.isTemplate = 0';
					$contentoptions2b .= ' AND el2.isTemplate = 0';
				}
			}
		}
	
		$exb = $exc = '';
		// Only load from these exclusive content groups
		if ( $this->_contentGroups )
		{
			$exb = Array ( );
			foreach ( $this->_contentGroups as $group )
				$exb[] = 'b.ContentGroup = "' . $group . '"';
			$exb = 'AND (' . implode ( ' OR ', $exb ) . ')';
			$exc = str_replace ( 'b.ContentGroup', 'c.ContentGroup', $exb );
		}
		
		$extra1a = ''; $extra1b = ''; $extra2a = ''; $extra2b = '';
		
		// Some queer exceptions
		if ( $this->_tableName == 'ContentElement' )
		{
			$extra1a = ', `ContentElement` ce1';
			$extra1b = 'AND ce1.ID = b.ContentID AND ce1.Language=\''. $this->Language.'\'';
			$extra2a = ', `ContentElement` ce2';
			$extra2b = 'AND ce2.ID = c.ContentID AND ce2.Language=\''. $this->Language.'\'';
		}
		// Get the rows
		$pex = ( isset ( $this->Parent ) ? ",{$this->Parent}" : '' );
		$query = "
			SELECT z.* FROM 
			(
				(
					SELECT 
						b.ContentID, b.Type, b.DataText, '' AS `DataInt`, \"\" AS `DataString`, '' AS `DataDouble`, 'Big' AS `DataTable`, 
						\"\" AS `DataMixed`, b.SortOrder, b.ID, b.Name, b.ContentGroup, b.IsVisible, b.AdminVisibility, b.IsGlobal
					FROM 
					`ContentDataBig` b{$contentoptions1a} $extra1a
					WHERE 
					( b.ContentID='{$this->ID}' || ( (b.IsGlobal='1'||(b.IsGlobal='2' AND b.ContentID IN ({$this->ID}{$pex}))){$contentoptions2a} ) )
					AND
					b.ContentTable=\"{$this->_tableName}\" $exb 
					$extra1b
				)
				UNION
				(
					SELECT 
						c.ContentID, c.Type, \"\" AS `DataText`, c.DataInt, c.DataString, c.DataDouble, 'Small' AS `DataTable`, 
						c.DataMixed, c.SortOrder, c.ID, c.Name, c.ContentGroup, c.IsVisible, c.AdminVisibility, c.IsGlobal
					FROM 
					`ContentDataSmall` c{$contentoptions1b} $extra2a
					WHERE 
					( c.ContentID='{$this->ID}' || ( (c.IsGlobal='1'||(c.IsGlobal='2' AND c.ContentID IN ({$this->ID}{$pex}))){$contentoptions2b} ) )
					AND
					c.ContentTable=\"{$this->_tableName}\" $exc
					$extra2b
				)
			) AS z
			ORDER BY z.SortOrder ASC, z.ID ASC
		";
		
		if ( $rows = $db->fetchObjectRows ( $query, MYSQLI_ASSOC ) )
		{
			/**
			 * If the object has special facililites
			**/
			if ( method_exists ( $this, 'renderExtraField' ) && method_exists ( $this, 'interpretExtraField' ) )
			{
				foreach ( $rows as $row )
				{
					if ( $this->{'_locked_' . $row->Name} ) 
						continue;
					if ( !trim ( $row->Name ) ) continue;
					
					// Globals for subpages
					if ( $row->IsGlobal == '2' && ( $row->ContentID != $this->Parent && $row->ContentID != $this->ID ) )
						continue;
						
					$key = '_extra_' . $row->Name;
					$fieldkey = '_field_' . $row->Name;
					$this->$fieldkey = $row;
					if ( !$this->extraFieldCache ( $row ) )
					{
						$o = new stdclass ();
						$o->Name = $row->Name;
						$o->ID = $row->ID;
						$this->extraFieldCacheSet ( $o );
						if ( $row->IsGlobal )
						{
							if ( isset ( $GLOBALS[ '__GLOBALS__' ][ 'ExtraFieldParents' ] ) )
							{
								foreach ( $GLOBALS[ '__GLOBALS__' ][ 'ExtraFieldParents' ] as $kz=>$ps )
								{
									$p =& $GLOBALS[ '__GLOBALS__' ][ 'ExtraFieldParents' ][$kz];
									if ( strstr ( $p->ContentGroups, $row->ContentGroup ) && !$p->$key )
									{
										$p->{$row->Name} = $p->renderExtraField ( $row, $options );
										if ( $row->Type == 'extension' )
											$p->$key = $p->{$row->Name};
										else $p->$key = $p->interpretExtraField ( $row, $options );
										$this->{$row->Name} = $p->{$row->Name};
										$this->$key = $p->$key;
									}
								}
							}
						}
						else
						{
							$this->{$row->Name} = $this->renderExtraField ( $row, $options );
							if ( $row->Type == 'extension' )
								$this->$key = $this->{$row->Name};
							else $this->$key = $this->interpretExtraField ( $row, $options );
						}
					}
				}
			}
			/**
			 * Fallback for all other objects
			**/
			else
			{
				foreach ( $rows as $row )
				{
					if ( $this->{'_locked_' . $row->Name} ) 
						continue;
					$n = $row->Name;
					$m = '_extra_' . $n;
					if ( trim ( $row->DataText ) ) $this->$n = $this->$m = $row->DataText;
					else if ( trim ( $row->DataString ) ) $this->$n = $this->$m = $row->DataString;
					else if ( trim ( $row->DataMixed ) ) $this->$n = $this->$m = $row->DataMixed;
					else if ( $row->DataInt ) $this->$n = $this->$m = $row->DataInt;
					else if ( $row->DataDouble ) $this->$n = $this->$m = $row->DataDouble;
					else $this->$n = $this->$m = NULL;
					$fieldkey = '_field_' . $row->Name;
					$this->$fieldkey = $row;
				}
			}
			$this->_fieldsLoaded = true;
			$this->_loadingExtrafields = false;
			// If there's a function after trying to load extrafields
			if ( method_exists ( $this, 'onLoadedExtraFields' ) )
				$this->onLoadedExtraFields ( );
			unset ( $GLOBALS[ '__GLOBALS__' ][ 'ExtraFieldParents' ][ $this->getObjectIdentifier ] );
			
			// Resort extrafields
			$sortable = array ();
			$fields = array ();
			foreach ( $this as $k=>$v )
			{
				if ( substr ( $k, 0, 7 ) != '_field_' )
					continue;
				$key = str_pad ( $v->SortOrder, 3, '0', STR_PAD_LEFT ) .'_'. $v->Name;
				$sortable[ $key ] = $v;
				$fields[ $key ] = array ( $this->{'_extra_'.$v->Name}, $this->{$v->Name} );
				unset ( $this->{'_field_'.$v->Name}, $this->{'_extra_'.$v->Name}, $this->{$v->Name} );
			}
			foreach ( $sortable as $k=>$v )
			{
				$this->{'_field_'.$v->Name} = $v;
				$this->{'_extra_'.$v->Name} = $fields[$k][0];
				$this->{$v->Name} = $fields[$k][1];
			}
			return true;
		}

		// If there's a function after trying to load extrafields
		if ( method_exists ( $this, 'onLoadedExtraFields' ) )
			$this->onLoadedExtraFields ( );
		
		if( isset( $this->getObjectIdentifier ) && isset( $GLOBALS[ '__GLOBALS__' ][ 'ExtraFieldParents' ][ $this->getObjectIdentifier ] ) )
		{
			unset ( $GLOBALS[ '__GLOBALS__' ][ 'ExtraFieldParents' ][ $this->getObjectIdentifier ] );
		}
		return false;
	}
	
	/**
	 * Load only the names of the extra fields and return an array
	**/
	function loadExtraFieldNames ( $force = false )
	{
		if ( !$force && $this->_fieldsLoaded ) return true;
		$db =& $this->getDatabase ( );
	
		// ContentElement objects have working copies and published copies
		$contentoptions = '';
		if ( $this->_tableName == 'ContentElement' )
		{
			$contentoptions1a = ', `ContentElement` el1';
			$contentoptions1b = ', `ContentElement` el2';
			// Get working copy
			if ( $this->MainID != $this->ID )
			{
				$contentoptions2a = ' AND el1.MainID != el1.ID AND el1.ID = b.ContentID';
				$contentoptions2b = ' AND el2.MainID != el2.ID AND el2.ID = c.ContentID';
			}
			// Get published version
			else
			{
				$contentoptions2a = ' AND el1.MainID = el1.ID AND el1.ID = b.ContentID';
				$contentoptions2b = ' AND el2.MainID = el2.ID AND el2.ID = c.ContentID';
			}
		}
	
		// Some queer exceptions
		if ( $this->_tableName == 'ContentElement' )
		{
			$extra1a = ', `ContentElement` ce1';
			$extra1b = 'AND ce1.ID = b.ContentID AND ce1.Language=\''. $this->Language.'\'';
			$extra2a = ', `ContentElement` ce2';
			$extra2b = 'AND ce2.ID = c.ContentID AND ce2.Language=\''. $this->Language.'\'';
		}
		
		// Get the rows
		$pex = ( isset ( $this->Parent ) ? ",{$this->Parent}" : '' );
		$query = "
			SELECT z.Name FROM 
			(
				(
					SELECT b.ContentID, b.Type, b.DataText, '0' AS `DataInt`, \"\" AS `DataString`, '0.0' AS `DataDouble`, 'Big' AS `DataTable`, \"\" AS `DataMixed`, b.SortOrder, b.ID, b.Name, b.ContentGroup
					FROM 
					`ContentDataBig` b{$contentoptions1a}
					WHERE 
					( b.ContentID='{$this->ID}' || ( (b.IsGlobal='1'||(b.IsGlobal='2' AND b.ContentID IN ({$this->ID},{$pex}))){$contentoptions2a}) )
					AND
					b.ContentTable=\"{$this->_tableName}\" {$this->loadExtraFieldsQueryRules}
				)
				UNION
				(
					SELECT c.ContentID, c.Type, \"\" AS `DataText`, c.DataInt, c.DataString, c.DataDouble, 'Small' AS `DataTable`, c.DataMixed, c.SortOrder, c.ID, c.Name, c.ContentGroup  
					FROM 
					`ContentDataSmall` c{$contentoptions1b} $extra2a
					WHERE 
					( c.ContentID='{$this->ID}' || ( (c.IsGlobal='1'||(c.IsGlobal='2' AND c.ContentID IN ({$this->ID},{$pex}))){$contentoptions2b}) )
					AND
					c.ContentTable=\"{$this->_tableName}\" {$this->loadExtraFieldsQueryRules}
					$extra2b
				)
			) AS z
			ORDER BY z.SortOrder ASC, z.ID ASC
		";
		
		if ( $rows = $db->fetchObjectRows ( $query, MYSQLI_ASSOC ) )
		{
			$names = Array ( );
			foreach ( $rows as $row )
			{	
				// Globals for subpages
				if ( $row->IsGlobal == '2' && ( $row->ContentID != $this->Parent && $row->ContentID != $this->ID ) )
					continue;
				$names[] = $row->Name;
			}
			return $names;
		}
		return false;
	}
	
	/**
	 * Create extra fields from source onto this content
	**/
	function copyExtraFields ( $source )
	{
		$db =& $this->getDatabase ( );
		
		// Delete old fields
		$deleteFields = 'DELETE FROM ContentDataSmall WHERE ContentTable="' . $this->_tableName . '" AND ContentID=\'' . $this->ID . '\'';
			
		// Add fields from source to self
		$proto = new dbObject ( 'ContentDataSmall' );
		if ( $smalls = $proto->find ( 'SELECT * FROM ContentDataSmall WHERE ContentTable="' . $this->_tableName . '" AND ContentID=\'' . $source . '\'' ) )
		{
			$db->query ( $deleteFields );
			foreach ( $smalls as $small )
			{
				$small->ID = 0;
				$small->_isLoaded = 0;
				$small->ContentID = $this->ID;
				$small->save ( );
			}
		}
		else $db->query ( $deleteFields );
		
		// Delete old fields
		$deleteFields = 'DELETE FROM ContentDataBig WHERE ContentTable="' . $this->_tableName . '" AND ContentID=\'' . $this->ID . '\'';
		
		// Add fields from source to self
		$bigs = new dbObject ( 'ContentDataBig' );
		if ( $bigs = $bigs->find ( 'SELECT * FROM ContentDataBig WHERE ContentTable="' . $this->_tableName . '" AND ContentID=\'' . $source . '\'' ) )
		{
			$db->query ( $deleteFields );
			foreach ( $bigs as $big )
			{
				$big->ID = 0;
				$big->_isLoaded = 0;
				$big->ContentID = $this->ID;
				$big->save ( );
			}
		}
		else $db->query ( $deleteFields );
	}
	
	/**
	 * Copy objects from source onto this content
	**/
	function copyObjects ( $source )
	{
		$db =& $this->getDatabase ( );
		
		// Delete old objects
		$deleteObjs = 'DELETE FROM ObjectConnection WHERE ObjectType="' . $this->_tableName . '" AND ObjectID=' . $this->ID;
		
		// Add objects from source
		$objs = new dbObject ( 'ObjectConnection' );
		$objs->addClause ( 'WHERE', 'ObjectType="' . $this->_tableName . '" AND ObjectID=' . $source );
		if ( $objs = $objs->find ( ) )
		{
			$db->query ( $deleteObjs );
			foreach ( $objs as $obj )
			{
				$obj->ID = 0;
				$obj->_isLoaded = 0;
				$obj->ObjectID = $this->ID;
				$obj->save ( );
			}
		}
		else $db->query ( $deleteObjs );
	}
	
	/**
	 * Create permissions from source onto this content
	**/
	function copyPermissions ( $source, $type = false )
	{
		$db =& $this->getDatabase ( );
		
		// Special case for content element
		if ( $type == 'web' && $this->_tableName == 'ContentElement' )
		{
			$p = new dbObject ( 'ContentElement' );
			if ( $p->load ( $source ) )
			{
				$this->IsProtected = $p->IsProtected;
				$this->DateModified = date ( 'Y-m-d H:i:s' );
				$this->save ( );
			}
		}
		
		// Add permissions from source
		$objs = new dbObject ( 'ObjectPermission' );
		$objs->addClause ( 
			'WHERE', 
			'ObjectType="' . $this->_tableName . '" AND ObjectID=\'' . $source . '\'' . 
				( $type ? ' AND PermissionType=\'' . $type . '\'' : '' ) );
		$deletePerms = '
				DELETE FROM 
					ObjectPermission 
				WHERE 
					ObjectType="' . $this->_tableName . '" AND 
					ObjectID=\'' . $this->ID . '\' '
					. ( $type ? '
					AND PermissionType=\'' . $type . '\'
					'  : '' ) . '
		';
		if ( $objs = $objs->find ( ) )
		{
			// Delete old permissions
			$db->query ( $deletePerms );
			foreach ( $objs as $obj )
			{
				// Set new
				$obj->ID = 0;
				$obj->_isLoaded = 0;
				$obj->ObjectID = $this->ID;
				$obj->save ( );
			}
		}
		else $db->query ( $deletePerms );
	}
	
	function setPermissionsRecursively ( $parent = false, $type = false )
	{
		if ( !$parent ) $parent = $this->ID;
		if ( !is_object ( $parent ) )
		{
			$pid = $parent;
			$parent = new dbObject ( $this->_tableName );
			$parent->load ( $pid );
		}
	
		// Get child folders under original
		$pobj = new dbObject ( $parent->_tableName );
		switch ( $parent->_tableName )
		{
			case 'ContentElement':
				$pobj->Parent = $parent->MainID;
				if ( $parent->ID != $parent->MainID )
					$pobj->addClause ( 'WHERE', 'MainID != ID' );
				else $pobj->addClause ( 'WHERE', 'ID = MainID' );
				break;
			case 'Folder':
			default:
				$pobj->Parent = $parent->ID;
				break;
		}
	
		// Find and cycle through child folders
	
		if ( $pobjs = $pobj->find ( ) )
		{
			foreach ( $pobjs as $pobj )
			{
				// Copy permissions
				$pobj->copyPermissions ( $parent->ID, $type );
				
				// Go through the original permissions
				$this->setPermissionsRecursively ( $pobj, $type );
			}
		}
		return true;
	}
	
	
	function LockExtraField ( $field )
	{
		$this->{'_field_' . $field} = true;
	}
	
	function UnlockExtraField ( $field )
	{
		$this->{'_field_' . $field} = false;
	}
	
	// Check cache for data
	function CheckCache ( $type, $key )
	{
		switch ( $type )
		{
			case 'getPath':
				if ( isset ( $this->_cache[ 'getPath' ] ) )
				{
					if ( array_key_exists ( $key, $this->_cache[ 'getPath' ] ) )
						return $this->_cache[ 'getPath' ][ $key ];
				}
				break;
		}
		return false;
	}
	// Cache data
	function Cache ( $type, $key, $value )
	{
		if ( !isset ( $this->_cache[ $type ] ) )
			$this->_cache[ $type ] = array ();
		$this->_cache[ $type ][ $key ] = $value;
		return true;
	}
}

?>
