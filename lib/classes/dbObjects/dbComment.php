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
 * Prereq
**/

$table = new cDatabaseTable ( 'Comment' );
$db =& dbObject::globalValue ( "database" );
if ( !$table->load ( ) )
{
	$table->addField ( 'ID', 'int', array ( 'primaryKey'=>true, 'autoIncrement'=>true ) );
	$table->addField ( 'DateCreated', 'datetime' );
	$table->addField ( 'DateModified', 'datetime' );
	$table->addField ( 'ParentID', 'int', array ( 'default'=>'0' ) );
	$table->addField ( 'Nickname', 'varchar(64)' );
	$table->addField ( 'UserID', 'int' );
	$table->addField ( 'Subject', 'varchar(128)' );
	$table->addField ( 'Message', 'text' );
	$table->addField ( 'IsDeleted', 'tinyint(4)', array ( 'default'=>'0' ) );
	$table->addField ( 'IsSticky', 'tinyint(4)', array ( 'default'=>'0' ) );
	$table->addField ( 'IsLocked', 'tinyint(4)', array ( 'default'=>'0' ) );
	$table->addField ( 'Moderation', 'int(11)', array ( 'default'=>'0' ) );
	$table->addField ( 'ElementType', 'int(11)' );
	$table->addField ( 'ElementID', 'int(11)' );
	$table->addField ( 'SortOrder', 'int(11)' );
	$db->query ( $table->createTableSyntax ( ) );
	if ( !$table->load ( ) )
		arenadie ( 'Can\'t create table!' );
}

class dbComment extends dbObject
{
	function __construct ( $id = false )
	{
		$this->_tableName = "Comment";
		parent::loadTable ( );
		if ( $id ) parent::load ( $id );
	}

	function GetComments ( $object )
	{
		$comments = new dbComment ( );
		$comments->addClause ( "WHERE", "ElementType='{$object->_tableName}' AND ElementID='{$object->ID}'" );
		$comments->addClause ( "ORDER BY", "ParentID ASC" );
		$comments->addClause ( "ORDER BY", "DateCreated ASC" );
		return $comments->find ( );
	}
	
	function ProcessMessage ( $string )
	{ 
		$string = "<p>" . str_replace ( "\n", "</p>\n<p>", $string ) . "</p>";
		return $string;
	}
	
	function CountComments ( $object )
	{
		$comments = new dbComment ( );
		$comments->addClause ( "WHERE", "ElementType='{$object->_tableName}' AND ElementID='{$object->ID}'" );
		$comments->addClause ( "ORDER BY", "ParentID ASC" );
		$comments->addClause ( "ORDER BY", "DateCreated ASC" );
		return $comments->findCount ( );
	}
	
	/** 
   * Add a comment in a thread
  **/
	function AddReply ( $commentObject )
	{
		$commentObject->ParentID = $this->ID;
		$commentObject->ElementType = $this->ElementType;
		$commentObject->ElementID = $this->ElementID;
		$commentObject->save ( );
	}
	
	/**
	 * Save this object in relation to another object
	**/
	function SaveOnObject ( $object )
	{
		$this->ParentID = '0';
		$this->ElementType = $object->_tableName;
		$this->ElementID = $object->ID;
		$this->save ( );
	}
}

?>
