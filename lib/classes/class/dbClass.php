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



/** NOTE:
***
*** This file contains two classes; dbClassDefinition and dbClassInstance. They 
*** are closely linked and not very verbose, the heavy lifting is done by dbObject
*** and cDatabaseTable.
***
*** @author      Inge Jorgensen <inge@elektronaut.no>
*** @copyright   Copyright (c) 2006 Blest AS
**/



/**
*** -----------------------------------------------------------------------------------------------------------------------
*** dbClassDefinition
*** -----------------------------------------------------------------------------------------------------------------------
***
**/


class dbClassDefinition
{
	var $class_name = false;
	var $table_name = false;
	var $table      = false;
	
	function __construct( $class_name, $database=false )
	{
		$this->class_name = $class_name;
		$this->table_name = "class{$this->class_name}";
		if ( $database ) $this->database =& $database;
	}



	/**
	*** Load the database table object and field properties.
	**/
	function loadTable()
	{
		if( !$this->table )
		{
			$database    =& dbObject::globalValue( 'database' );
			$this->table =& $database->getTable( $this->table_name );
		}
		if( !isset( $this->field_properties ) )
		{
			if ( isset( $GLOBALS['__CLASS_DEFINITION__'][$this->class_name]['field_props'] ) )
			{
				$this->field_properties = $GLOBALS['__CLASS_DEFINITION__'][$this->class_name]['field_props'];
			}
			else $this->loadFieldProperties();
		}
	}



	/**
	*** Load information from the ClassDefinition table
	**/
	function loadFieldProperties()
	{
		if ( !$this->class_name ) return false;  // bork out if class name isn't set. just in case.

		// load the properties as dbObjects
		$objs = new dbObject( "ClassDefinitions" );
		$objs->ClassName = $this->class_name;
		$objs = $objs->find();

		// populate the $field_props array
		$field_props = array();
		if ( is_array( $objs ) && count( $objs ) ) foreach( $objs as $obj )
		{
			$field_props[$obj->Field] = $obj;
		}

		// store it in $this->field_properties
		$this->field_properties = $field_props;

		// store it in the global cache array
		$GLOBALS['__CLASS_DEFINITION__'][$this->class_name]['field_props'] = $field_props;
	}



	/**
	*** Get the names of the extra properties which can be set. In other words; the fields in the ClassDefinition table minus ClassName and Field.
	**/
	function extraPropertyNames()
	{
		// This utilizes intimate knowledge of dbObject, but what the hell.
		$obj = new dbObject( "ClassDefinitions" );
		$fields = $obj->_table->getFields();

		$field_names = array();
		if ( is_array( $fields ) && count( $fields ) ) foreach ( $fields as $field )
		{
			$field_name = $field->Field;
			if ( $field_name != "ClassName" && $field_name != "Field" ) $field_names[] = $field_name;
		}
		return $field_names;
	}


	/**
	*** Get (and optionally set) the properties for a field. 
	**/
	function fieldProperties( $field_name, $options=false )
	{
		$this->loadTable(); // Ensure that the table and field properties are loaded.

		// Create a new dbObject if necessary
		if ( !$this->field_properties[$field_name] )
		{
			$obj = new dbObject( "ClassDefinitions" );
			$obj->ClassName = $this->class_name;
			$obj->Field     = $field_name;
			$this->field_properties[ $field_name ] = $obj;
		}

		if( is_array( $options ) && count( $options ) )
		{
			$prop_names = $this->extraPropertyNames();
			foreach( $options as $key => $value ) if ( in_array( $key, $prop_names ) )
			{
				$this->field_properties[ $field_name ]->{$key} = $value;
			}
		}

		return $this->field_properties[$field_name];
	}



	/**
	*** Add a field to the table definition. Examples:
	***
	*** $myClass = new dbClassDefinition( "MyClass" );
	*** $myClass->addField( "ID",          "int",   array( "primaryKey" => true, "autoIncrement" => true ) );   // Create a primary key (strictly not necessary)
	*** $myClass->addField( "Name",        "string" array( "Default" => "No name", Null => "NO" ) );            // Add a Name field with default value, NULL value not allowed
	*** $myClass->addField( "Price",       "float"  );                                                          // Add a Price field with type float
	*** $myClass->addField( "Description", "text"   array( "Label" => "Description of item" ) );                // Add a Description field with a label
	*** $myClass->addField( "Author",      "int",   array( "Position" => "AFTER `Name`" ) );                    // Add an Author field positioned below Name
	*** $myClass->save();
	***
	*** If the field already exists in the database, it will be modified to suit the new datatype etc. In other words, addField() is also used to change existing fields.
	*** 
	*** NOTE: changes to the definition are not written before save() is called.
	**/
	function addField( $fieldName = "", $fieldType="string", $options=array() )
	{
		// i18n it later on...
		if ( $fieldName == "" ) $fildName = "Empty field";
			
		$this->loadTable();

		// Enforce primary key named ID
		if ( !$this->table->getPrimaryKey() && !$options['primaryKey'] )
		{
			$this->table->addField( "ID", "int", array( "primaryKey" => true, "autoIncrement" => true ) );			
		}

		if ( count ( $options ) )
			$this->fieldProperties( $fieldName, $options );

		if ( $this->table->Fields->{$fieldName} )
			$fieldName .= "_dup";

		return $this->table->addField( $fieldName, $fieldType, $options );
	}



	/**
	*** Returns boolean true if the definition exists in the database.
	**/
	function exists()
	{
		$this->loadTable();
		return $this->table->exists();
	}



	/**
	*** Save the table definition to database. See cDatabaseTable::save() for more information.
	**/
	function save()
	{
		$this->loadTable();

		// Save the field properties
		if ( is_array( $this->field_properties ) ) 
		{
			foreach ( $this->field_properties as $key => $value )
			{
				$this->field_properties[ $key ]->save();
			}
		}

		return $this->table->save();
	}


	/**
	*** Reload the table definition from the database. This will destroy all modifications since the last call to save()
	**/
	function reload()
	{
		$this->loadTable();
		return $this->table->reload();
	}


	/**
	*** Remove a field from the table definition.
	**/
	function removeField( $fieldName )
	{
		$this->loadTable();
		return $this->table->removeField( $fieldName );
	}

	function renameField( $oldName, $newName )
	{
		$this->loadTable();
		return $this->table->renameField( $oldName, $newName );
	}

	function reorderField( $fieldName, $offset=0 )
	{
		$this->loadTable();
		return $this->table->reorderField( $fieldName, $offset );
	}
	
	/**
	*** List all classes
	**/
	
	function showClasses ( )
	{
		$database =& dbObject::globalValue ( "database" );
		if ( $result = $database->query ( "
			SHOW TABLES
		" ) )
		{
			$outar = Array ();
			while ( $row = mysql_fetch_row ( $result ) )
			{
				if ( substr ( $row[ 0 ], 0, 5 ) == "class" )
				{
					$outar[] = $row[ 0 ];
				}
			}
		}
		return $outar;
	}
	
	/**
	*** Get all classes
	**/
	
	function getClasses ( )
	{
		$database =& dbObject::globalValue ( "database" );
		if ( $result = $database->query ( "
			SHOW TABLES
		" ) )
		{
			$outar = Array ();
			while ( $row = mysql_fetch_row ( $result ) )
			{
				if ( substr ( $row[ 0 ], 0, 5 ) == "class" )
				{
					$outar[] = new dbClassDefinition ( 
						substr ( $row[ 0 ], 5, strlen ( $row[ 0 ] ) - 5 ) 
					);
				}
			}
		}
		return $outar;
	}
	
	/**
	*** Delete class and all instances 
	*** Can cause extreme dataloss (obviously!)
	**/
	function delete ( )
	{	
		$database =& dbObject::globalValue ( "database" );
		$database->query ( "DELETE FROM `class{$this->class_name}`" );
		$database->query ( "DELETE FROM `ClassDefinitions` WHERE `ClassName`=\"{$this->class_name}\"" );
		$database->query ( "DROP TABLE `class{$this->class_name}`" );
	}
}



/**
*** -----------------------------------------------------------------------------------------------------------------------
*** dbClassInstance
*** -----------------------------------------------------------------------------------------------------------------------
***
**/

class dbClassInstance extends dbObject
{
	var $_class_definition = false;
	var $_tableNameMatch   = "/^class(.*)/";

	/**
	*** NOTE: the constructor will not always receive parameters (in particular when using find() and friends).
	*** Do not put mission-critical code in the constructor.
	**/
	function __construct ( $class_name=false, $database=false )
	{
		if ( $class_name )
		{
			$def = $this->getClassDefinition( $class_name, &$database );
			parent::dbObject( $def->table_name, &$database );
		}
	}

	function getClassDefinition( $class_name=false, $database=false )
	{
		if ( !$this->_class_definition && $class_name )
		{
			$this->_class_definition = new dbClassDefinition( $class_name, &$database );
			if ( !$this->_class_definition->exists() )
			{
				return false;
				trigger_error( "Cannot get class definition for <em>{$class_name}</em>. The table is most likely missing.", E_USER_ERROR );
			}
		}
		return ($this->_class_definition) ? $this->_class_definition : false;
	}


	// Get the properties for a field.
	function fieldProperties( $field_name, $options=false )
	{
		return $this->_class_definition->fieldProperties( $field_name, $options );
	}

	function getRelations( $field_name )
	{
		$props = $this->fieldProperties( $field_name );
		if ( !( $criteria = $props->Relation ) ) return false;
		
		// get the order by clause
		if ( strstr( $criteria, "|" ) ) list( $type, $criteria, $order_by ) = explode( "|", $criteria );
		else $type = $criteria;

		// We'll assume the criteria is the primary key unless it contains : or ;
		if ( !preg_match( "/[;:]/", $criteria ) )
		{
			$objs = dbObject::create ( $type );
			if ( $order_by )
				$objs->addClause ( "ORDER BY", $order_by );
			return ( $objs->find ( ) );
		}
		else
		{
			$searcher = dbObject::create( $type );

			foreach( explode( ";", $criteria ) as $prop )
			{
				preg_match( "/(.*):(.*)/", $prop, $matches );
				$searcher->{$matches[1]} = $matches[2];
			}

			if ( $order_by )
				$searcher->addClause( "ORDER BY", $order_by );

			return ( $searcher->find() );
		}
	}
}


?>
