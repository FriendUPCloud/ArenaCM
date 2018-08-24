-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 09, 2010 at 11:40 AM
-- Server version: 5.0.51
-- PHP Version: 5.2.4-2ubuntu5.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `retronaughts`
--

-- --------------------------------------------------------

--
-- Table structure for table `ArenaInfo`
--

CREATE TABLE IF NOT EXISTS `ArenaInfo` (
  `ID` int(11) NOT NULL auto_increment,
  `Version` text NOT NULL,
  `Information` text NOT NULL,
  `DateUpdated` datetime default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `BlogItem`
--

CREATE TABLE IF NOT EXISTS `BlogItem` (
  `ID` int(11) NOT NULL auto_increment,
  `UserID` bigint(20) NOT NULL,
  `ContentElementID` bigint(20) NOT NULL,
  `AuthorName` text NOT NULL,
  `IsPublished` tinyint(4) NOT NULL default '0',
  `Title` text NOT NULL,
  `Leadin` text NOT NULL,
  `Body` text NOT NULL,
  `Tags` text NOT NULL,
  `DatePublish` datetime NOT NULL,
  `DateCreated` datetime NOT NULL,
  `DateUpdated` datetime NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `BlogTag`
--

CREATE TABLE IF NOT EXISTS `BlogTag` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` text NOT NULL,
  `Rating` bigint(20) NOT NULL default '0',
  `DateUpdated` datetime default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `Comment`
--

CREATE TABLE IF NOT EXISTS `Comment` (
  `ID` int(11) NOT NULL auto_increment,
  `DateCreated` datetime default NULL,
  `DateModified` datetime default NULL,
  `ParentID` int(11) default NULL,
  `Nickname` text default NULL,
  `UserID` int(11) default NULL,
  `Subject` text default NULL,
  `Message` text,
  `IsDeleted` tinyint(4) default NULL,
  `IsSticky` tinyint(4) default NULL,
  `IsLocked` tinyint(4) default NULL,
  `Moderation` bigint(11) default NULL,
  `ElementType` text NOT NULL,
  `ElementID` bigint(11) default NULL,
  `SortOrder` bigint(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `ContentDataBig`
--

CREATE TABLE IF NOT EXISTS `ContentDataBig` (
  `ID` bigint(20) NOT NULL auto_increment,
  `ContentID` int(11) default NULL,
  `ContentTable` text default NULL,
  `DataText` text,
  `Name` text default NULL,
  `SortOrder` int(11) default '0',
  `Type` text default NULL,
  `IsVisible` tinyint(4) default '1',
  `AdminVisibility` tinyint(4) NOT NULL default '1',
  `ContentGroup` varchar(255) default 'Default',
  `IsGlobal` tinyint(4) default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=123 ;

-- --------------------------------------------------------

--
-- Table structure for table `ContentDataSmall`
--

CREATE TABLE IF NOT EXISTS `ContentDataSmall` (
  `ID` bigint(20) NOT NULL auto_increment,
  `ContentID` int(11) default NULL,
  `ContentTable` text default NULL,
  `DataString` text default NULL,
  `DataMixed` text,
  `DataInt` int(11) default NULL,
  `DataDouble` double default NULL,
  `Name` text default NULL,
  `SortOrder` int(11) default '0',
  `Type` text default NULL,
  `IsVisible` tinyint(4) default '1',
  `AdminVisibility` tinyint(4) NOT NULL default '1',
  `ContentGroup` varchar(255) default 'Default',
  `IsGlobal` tinyint(4) default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=26858 ;

-- --------------------------------------------------------

--
-- Table structure for table `ContentElement`
--

CREATE TABLE IF NOT EXISTS `ContentElement` (
  `ID` int(11) NOT NULL auto_increment,
  `MainID` int(11) default NULL,
  `Parent` int(11) default NULL,
  `SymLink` int(11) default '0',
  `SortOrder` int(11) default NULL,
  `Title` text default NULL,
  `MenuTitle` text default NULL,
  `SystemName` text default NULL,
  `Intro` text,
  `Body` text,
  `IsPublished` tinyint(4) default NULL,
  `IsFallback` tinyint(4) default NULL,
  `IsSystem` tinyint(4) default NULL,
  `IsMain` tinyint(4) default NULL,
  `IsDeleted` tinyint(4) default '0',
  `DateCreated` datetime default NULL,
  `DateModified` datetime default NULL,
  `DatePublish` datetime default NULL,
  `DateArchive` datetime default NULL,
  `Link` text default NULL,
  `LinkData` text default NULL,
  `Template` text default NULL,
  `TemplateArchived` text default NULL,
  `TemplateID` int(11) default '0',
  `Author` int(11) default NULL,
  `Version` double default NULL,
  `VersionPublished` double default NULL,
  `Language` varchar(255) default '1',
  `ContentType` text default NULL,
  `RouteName` text default NULL,
  `IsTemplate` tinyint(4) default '0',
  `IsProtected` tinyint(4) default '0',
  `IsDefault` tinyint(4) default '0',
  `SeenTimesUnique` bigint(20) NOT NULL default '0',
  `SeenTimes` bigint(20) NOT NULL default '0',
  `ContentGroups` varchar(255) default 'Default',
  `ContentTemplateID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `ContentRoute`
--

CREATE TABLE IF NOT EXISTS `ContentRoute` (
  `ID` bigint(11) NOT NULL auto_increment,
  `Route` text default NULL,
  `ElementType` text default NULL,
  `ElementID` int(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=65 ;

-- --------------------------------------------------------

--
-- Table structure for table `ElementTag`
--

CREATE TABLE IF NOT EXISTS `ElementTag` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Name` text default NULL,
  `Popularity` bigint(20) NOT NULL default '0',
  `DateUpdated` datetime default NULL,
  `Type` text default NULL,
  `Description` text,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `File`
--

CREATE TABLE IF NOT EXISTS `File` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Title` text default NULL,
  `Filename` text default NULL,
  `Description` text,
  `Tags` text NOT NULL,
  `FileFolder` int(11) default '0',
  `Filesize` int(11) default NULL,
  `DateCreated` datetime default NULL,
  `DateModified` datetime default NULL,
  `SortOrder` int(11) default NULL,
  `Filetype` text default NULL,
  `FilenameOriginal` text default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `Folder`
--

CREATE TABLE IF NOT EXISTS `Folder` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` text default NULL,
  `Parent` int(11) default NULL,
  `Description` text default NULL,
  `DateCreated` datetime default NULL,
  `DateModified` datetime default NULL,
  `DiskPath` text default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `ForumMessage`
--

CREATE TABLE IF NOT EXISTS `ForumMessage` (
  `ID` int(11) NOT NULL auto_increment,
  `DateCreated` datetime default NULL,
  `DateModified` datetime default NULL,
  `ParentID` int(11) default NULL,
  `ForumID` int(11) default NULL,
  `ThreadID` int(11) default NULL,
  `Nickname` text default NULL,
  `UserID` int(11) default NULL,
  `Subject` text default NULL,
  `Message` text,
  `IsForum` tinyint(4) default '0',
  `IsTopic` tinyint(4) default '0',
  `IsDeleted` tinyint(4) default '0',
  `IsSticky` tinyint(4) default '0',
  `IsLocked` tinyint(4) default '0',
  `Moderation` int(11) default '0',
  `ContentElementID` bigint(11) default NULL,
  `SortOrder` int(11) default '0',
  `SeenTimes` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Table structure for table `Groups`
--

CREATE TABLE IF NOT EXISTS `Groups` (
  `ID` int(11) NOT NULL auto_increment,
  `GroupID` int(11) NOT NULL default '0',
  `SuperAdmin` tinyint(4) NOT NULL default '0',
  `Name` text default NULL,
  `Description` text,
  `SortOrder` int(11) default NULL,
  `TemplateID` int(11) default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `Guestbook`
--

CREATE TABLE IF NOT EXISTS `Guestbook` (
  `ID` int(11) NOT NULL auto_increment,
  `Date` datetime default NULL,
  `Nickname` text default NULL,
  `Message` text,
  `IsDeleted` tinyint(4) default NULL,
  `ContentElementID` int(11) default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Image`
--

CREATE TABLE IF NOT EXISTS `Image` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Title` text default NULL,
  `Filename` text default NULL,
  `Description` text,
  `Tags` text NOT NULL,
  `ColorSpace` varchar(255) NOT NULL default 'rgb',
  `ImageFolder` int(11) default '0',
  `Filesize` int(11) default NULL,
  `Width` int(11) default NULL,
  `Height` int(11) default NULL,
  `DateCreated` datetime default NULL,
  `DateModified` datetime default NULL,
  `SortOrder` int(11) default '0',
  `Filetype` text default NULL,
  `FilenameOriginal` text default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `Languages`
--

CREATE TABLE IF NOT EXISTS `Languages` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` text default NULL,
  `NativeName` text default NULL,
  `IsDefault` tinyint(4) default '0',
  `UrlActivator` text default NULL,
  `BaseUrl` text default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `Log`
--

CREATE TABLE IF NOT EXISTS `Log` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Type` text default NULL,
  `Subject` text default NULL,
  `Message` text,
  `DateCreated` datetime default NULL,
  `ObjectType` text default NULL,
  `ObjectID` bigint(20) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ModulePermissions`
--

CREATE TABLE IF NOT EXISTS `ModulePermissions` (
  `ID` int(11) NOT NULL auto_increment,
  `UserID` int(11) default '0',
  `GroupID` int(11) default '0',
  `Module` text default NULL,
  `Name` text default NULL,
  `Read` tinyint(4) default '0',
  `Write` tinyint(4) default '0',
  `Data` text default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ModulesEnabled`
--

CREATE TABLE IF NOT EXISTS `ModulesEnabled` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` int(11) default NULL,
  `Module` text default NULL,
  `SortOrder` int(11) default '0',
  `ModuleName` text default NULL,
  `ModuleIcon` text default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=628 ;

-- --------------------------------------------------------

--
-- Table structure for table `News`
--

CREATE TABLE IF NOT EXISTS `News` (
  `ID` int(11) NOT NULL auto_increment,
  `Title` text default NULL,
  `Intro` text,
  `Article` text,
  `CategoryID` int(11) default NULL,
  `DateCreated` datetime default NULL,
  `DateFrom` datetime default NULL,
  `DateTo` datetime default NULL,
  `DateModified` datetime default NULL,
  `DateActual` datetime default NULL,
  `AuthorID` int(11) default NULL,
  `IsPublished` tinyint(4) default NULL,
  `IsDeleted` tinyint(4) default NULL,
  `IsEvent` tinyint(4) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `NewsCategory`
--

CREATE TABLE IF NOT EXISTS `NewsCategory` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` text default NULL,
  `Description` text default NULL,
  `DateCreated` datetime default NULL,
  `SortOrder` int(11) default '0',
  `SystemName` text default NULL,
  `Parent` int(11) default '0',
  `Language` int(11) default '1',
  `ContentElementID` int(11) default NULL,
  `DateFormat` varchar(255) default 'Y-m-d H:i',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Notes`
--

CREATE TABLE IF NOT EXISTS `Notes` (
  `ContentTable` text NOT NULL,
  `ContentID` bigint(20) NOT NULL,
  `Notes` text,
  PRIMARY KEY  (`ContentTable`,`ContentID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ObjectConnection`
--

CREATE TABLE IF NOT EXISTS `ObjectConnection` (
  `ID` bigint(20) NOT NULL auto_increment,
  `ConnectionGroupID` int(11) default NULL,
  `ObjectID` bigint(20) default NULL,
  `ObjectType` text default NULL,
  `ConnectedObjectID` bigint(20) default NULL,
  `ConnectedObjectType` text default NULL,
  `ExtensionObjectID` bigint(20) default NULL,
  `ExtensionObjectType` text default NULL,
  `Label` text default NULL,
  `SortOrder` int(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `ObjectConnectionGroup`
--

CREATE TABLE IF NOT EXISTS `ObjectConnectionGroup` (
  `ID` int(11) default NULL,
  `Name` text default NULL,
  `Description` text default NULL,
  `ObjectID` bigint(20) default NULL,
  `ObjectType` text default NULL
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ObjectPermission`
--

CREATE TABLE IF NOT EXISTS `ObjectPermission` (
  `ID` int(10) unsigned NOT NULL auto_increment,
  `AuthType` varchar(255) NOT NULL default '',
  `AuthID` int(11) NOT NULL default '0',
  `ObjectType` varchar(255) NOT NULL default '',
  `ObjectID` int(11) NOT NULL default '0',
  `Read` tinyint(4) NOT NULL default '0',
  `Write` tinyint(4) NOT NULL default '0',
  `Publish` tinyint(4) NOT NULL default '0',
  `Structure` tinyint(4) NOT NULL default '0',
  `PermissionType` varchar(255) default 'web',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `PublishQueue`
--

CREATE TABLE IF NOT EXISTS `PublishQueue` (
  `ID` bigint(20) NOT NULL auto_increment,
  `ContentElementID` bigint(20) NOT NULL,
  `ContentTable` varchar(255) default '',
  `ContentID` bigint(20) NOT NULL,
  `ActionScript` varchar(255) default '',
  `FieldName` varchar(255) default '',
  `LiteralName` varchar(255) default '',
  `Title` varchar(255) default '',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `RSSCategory`
--

CREATE TABLE IF NOT EXISTS `RSSCategory` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` text default NULL,
  `Description` text,
  `UserID` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `RSSLink`
--

CREATE TABLE IF NOT EXISTS `RSSLink` (
  `ID` bigint(11) NOT NULL auto_increment,
  `CategoryID` int(11) NOT NULL,
  `Name` text default NULL,
  `Description` text,
  `Url` text default NULL,
  `UserID` bigint(20) NOT NULL default '0',
  `Cache` longtext,
  `CacheDate` datetime default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `Setting`
--

CREATE TABLE IF NOT EXISTS `Setting` (
  `ID` int(11) NOT NULL auto_increment,
  `SettingType` text default NULL,
  `Key` text default NULL,
  `Value` text,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `UserCollection`
--

CREATE TABLE IF NOT EXISTS `UserCollection` (
  `ID` bigint(20) NOT NULL auto_increment,
  `UserCollectionID` bigint(20) NOT NULL default '0',
  `ImageID` bigint(20) NOT NULL default '0',
  `Name` text default NULL,
  `Description` text,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;
-- --------------------------------------------------------

--
-- Table structure for table `Sites`
--

CREATE TABLE IF NOT EXISTS `Sites` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteName` text default NULL,
  `SqlUser` text default NULL,
  `SqlPass` text default NULL,
  `SqlHost` text default NULL,
  `SqlDatabase` text default NULL,
  `BaseUrl` text default NULL,
  `BaseDir` text default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=124 ;

-- --------------------------------------------------------

--
-- Table structure for table `UserLogin`
--

CREATE TABLE IF NOT EXISTS `UserLogin` (
  `ID` int(11) NOT NULL auto_increment,
  `Token` text default NULL,
  `UserID` int(11) default NULL,
  `DataSource` text default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=916 ;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Username` text default NULL,
  `Password` text default NULL,
  `Name` text default NULL,
  `Address` text default NULL,
  `Country` text default NULL,
  `City` text default NULL,
  `Postcode` text default NULL,
  `Telephone` text default NULL,
  `Mobile` text default NULL,
  `Email` text default NULL,
  `Image` int(11) default NULL,
  `DateCreated` datetime default NULL,
  `DateLogin` datetime default NULL,
  `DateModified` datetime default NULL,
  `IsDisabled` tinyint(4) default '0',
  `InGroups` tinyint(4) default '0',
  `IsAdmin` tinyint(4) default '0',
  `IsTemplate` tinyint(4) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=81 ;

-- --------------------------------------------------------

--
-- Table structure for table `UsersGroups`
--

CREATE TABLE IF NOT EXISTS `UsersGroups` (
  `UserID` bigint(20) NOT NULL,
  `GroupID` bigint(20) NOT NULL,
  PRIMARY KEY  (`UserID`,`GroupID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
