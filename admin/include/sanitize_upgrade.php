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



// This happens on the upgrade to version 1.99.1
$db =& dbObject::globalValue ( 'database' );
$info = new cDatabaseTable ( 'ArenaInfo' );
if ( !$info->load ( ) )
{
	$db->query ( '
		CREATE TABLE `ArenaInfo`
		( 
			`ID` int(11) NOT NULL auto_increment, 
			`Version` varchar(255) NOT NULL, 
			`Information` text NOT NULL, 
			`DateUpdated` datetime,
			PRIMARY KEY(ID)
		)
	' );
}
// Oppgrader (liste over versioner)
$versions = Array (
	'1.99.1', '1.99.4', '1.99.5', '1.99.6', '1.99.7', '1.99.8', '1.99.9', 
	'2.0.10', '2.0.12', '2.0.13', '2.0.14', '2.0.15', '2.0.16', '2.0.17', 
	'2.0.18'
);
foreach ( $versions as $version )
{
	$test = new dbObject ( 'ArenaInfo' );
	$test->Version = $version;
	if ( !( $test = $test->findSingle ( ) ) )
	{
		$obj = new dbObject ( 'ArenaInfo' );
		$obj->Version = $version;
		$obj->Information = "Starter oppgradering:\n\n";
		switch ( $version )
		{
			case '1.99.1':
				$db->query ( 'ALTER TABLE `Groups` ADD `SuperAdmin` tinyint(4) NOT NULL DEFAULT 0 AFTER ID' );
				$obj->Information .= " * La til SuperAdmin felt på Groups\n";
				break;
			case '1.99.4':
				$db->query ( 'ALTER TABLE `ContentDataSmall` ADD `AdminVisibility` tinyint(4) NOT NULL default 1 AFTER `IsVisible`' );
				$db->query ( 'ALTER TABLE `ContentDataBig` ADD `AdminVisibility` tinyint(4) NOT NULL default 1 AFTER `IsVisible`' );
				$obj->Information .= " * La til admin visibilitet for ContentDataSmall og Big\n";
				break;
			case '1.99.5':
				$db->query ( 'ALTER TABLE `ContentElement` ADD `SeenTimes` bigint(20) NOT NULL default 0 AFTER `IsDefault`' );
				$db->query ( 'ALTER TABLE `ContentElement` ADD `SeenTimesUnique` bigint(20) NOT NULL default 0 AFTER `IsDefault`' );
				$obj->Information .= " * La til statistikk rad på contentelement";
				break;
			case '1.99.6':
				$db->query ( '
				CREATE TABLE `PublishQueue`
				(
					`ID` bigint(20) NOT NULL auto_increment,
					`ContentElementID` bigint(20) NOT NULL,
					`ContentTable` varchar(255) default "",
					`ContentID` bigint(20) NOT NULL,
					`ActionScript` varchar(255) default "",
					`FieldName` varchar(255) default "",
					`LiteralName` varchar(255) default "",
					`Title` varchar(255) default "",
					PRIMARY KEY(ID)
				)
				' );
				$obj->Information .= " * La til publiserings kø tabell";
				break;
			case '1.99.7':
				$db->query ( '
					ALTER TABLE `ContentElement` ADD `ContentTemplateID` int(11) NOT NULL default 0
				' );
				break;
			case '1.99.8':
				$db->query ( '
				CREATE TABLE `UserCollection`
				(
					`ID` bigint(20) NOT NULL auto_increment,
					`UserCollectionID` bigint(20) NOT NULL default 0,
					`ImageID` bigint(20) NOT NULL default 0,
					`Name` varchar(255),
					`Description` text,
					PRIMARY KEY(ID)
				)
				' );
				$db->query ( '
				ALTER TABLE `Groups` ADD `GroupID` int(11) NOT NULL DEFAULT 0 AFTER `ID`
				' );
				break;
			case '1.99.9':
				$db->query ( '
				CREATE TABLE `Log`
				(
					`ID` bigint(20) NOT NULL auto_increment,
					`Type` varchar(255),
					`Subject` varchar(255),
					`Message` text,
					`DateCreated` datetime,
					`ObjectType` varchar(255),
					`ObjectID` bigint(20),
					PRIMARY KEY(ID)
				)
				' );
				break;	
			case '2.0.10':
				$db->query ( '
					ALTER TABLE `Image` ADD `Tags` text NOT NULL AFTER `Description`
				' );
				$db->query ( '
					ALTER TABLE `Image` ADD `ColorSpace` varchar(255) NOT NULL default "rgb" AFTER `Tags`
				' );
				$db->query ( '
					ALTER TABLE `File` ADD `Tags` text NOT NULL AFTER `Description`
				' );
				$db->query ( '
					CREATE TABLE `ElementTag` (
					  `ID` bigint(20) NOT NULL auto_increment,
					  `Name` varchar(128) default NULL,
					  `Popularity` bigint(20) NOT NULL default \'0\',
					  `DateUpdated` datetime,
					  `Type` varchar(255) default NULL,
					  `Description` text,
					  PRIMARY KEY  (`ID`)
					)
				' );
				break;
			case '2.0.12':
				$db->query ( '
					ALTER TABLE `ContentElement` CHANGE `LinkText` `LinkData` text
				' );
				break;
			case '2.0.13':
				$db->query ( '
					ALTER TABLE `Image` ADD `BackupFilename` varchar(255) NOT NULL default ""
				' );
				$db->query ( '
					ALTER TABLE `File` ADD `BackupFilename` varchar(255) NOT NULL default ""
				' );
				break;
			case '2.0.14':
				$db->query ( '
					ALTER TABLE `Image` ADD `DateFrom` datetime NOT NULL
				' );
				$db->query ( '
					ALTER TABLE `File` ADD `DateFrom` datetime NOT NULL 
				' );
				$db->query ( '
					ALTER TABLE `Image` ADD `DateTo` datetime NOT NULL
				' );
				$db->query ( '
					ALTER TABLE `File` ADD `DateTo` datetime NOT NULL 
				' );
				break;
			case '2.0.15':
				$t = new cDatabaseTable ( 'ObjectConnectionGroup' );
				$t->load ();
				$found = false;
				foreach ( $t->fields as $f )
				{
					if ( $f->Key == 'PRI' )
						$found = true;
				}
				if ( !$found )
				{
					$db->query ( '
						ALTER TABLE `ObjectConnectionGroup`
						DROP `ID`
					' );
					$db->query ( '
						ALTER TABLE `ObjectConnectionGroup`
						ADD `ID` bigint(20) PRIMARY KEY auto_increment
					' );
				}
				// Double check
				$t = new cDatabaseTable ( 'ObjectConnectionGroup' );
				$t->load ();
				$found = false;
				foreach ( $t->fields as $f )
				{
					if ( $f->Key == 'PRI' )
						$found = true;
				}
				if ( !$found )
				{
					ArenaDie ( 'Still no primary key on ObjectConnectionGroup!' );
				}
				break;
			case '2.0.16':
				$t = new cDatabaseTable ( 'WebCache' );
				if ( !$t->load () )
				{
					$db->query ( '
						CREATE TABLE `WebCache` (
						 `ID` bigint(20) NOT NULL AUTO_INCREMENT,
						 `UniqueName` varchar(255) DEFAULT NULL,
						 `Date` datetime DEFAULT NULL,
						 `Content` longtext,
						 PRIMARY KEY (`ID`)
						) 
					' );
				}
				break;
			case '2.0.17':
				$t = new cDatabaseTable ( 'ObjectPermission' );
				if ( $t->load () )
				{
					$hasDelete = false;
					foreach ( $t->getFieldNames () as $name )
					{
						if ( $name == 'Delete' ) $hasDelete = true;
					}
					if ( !$hasDelete )
					{
						$db->query ( '
							ALTER TABLE ObjectPermission
							ADD `Delete` tinyint(4) NOT NULL default \'0\' 
							AFTER `Structure`
						' );
					}
				}
				break;
			case '2.0.18':
				$t = new cDatabaseTable ( 'Folder' );
				if ( $t->load () )
				{
					$hasSO = false;
					$hasNO = false;
					foreach ( $t->getFieldNames () as $name )
					{
						if ( $name == 'SortOrder' ) $hasSO = true;
						else if ( $name == 'Notes' ) $hasNO = true;
					}
					if ( !$hasSO )
					{
						$db->query ( '
							ALTER TABLE `Folder`
							ADD `SortOrder` int(11) NOT NULL default \'0\' 
							AFTER `Diskpath`
						' );
					}
					if ( !$hasNO )
					{
						$db->query ( '
							ALTER TABLE `Folder`
							ADD `Notes` text NOT NULL default ""
							AFTER `SortOrder`
						' );
					}
				}
				break;
		}
		$obj->DateUpdated = date ( 'Y-m-d H:i:s' );
		$obj->save ( );
	}
}
?>
