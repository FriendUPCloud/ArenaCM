-- phpMyAdmin SQL Dump
-- version 2.11.3deb1ubuntu1.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 09, 2010 at 11:39 AM
-- Server version: 5.0.51
-- PHP Version: 5.2.4-2ubuntu5.12

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `ArenaCore2`
--

-- --------------------------------------------------------

--
-- Table structure for table `AvailableExtension`
--

CREATE TABLE IF NOT EXISTS `AvailableExtension` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` text default NULL,
  `Intro` text,
  `Body` text,
  `ImageID` int(11) default '0',
  `Pris` double default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Case`
--

CREATE TABLE IF NOT EXISTS `Case` (
  `ID` bigint(20) NOT NULL auto_increment,
  `CaseID` bigint(20) NOT NULL default '0',
  `ProjectID` int(11) NOT NULL,
  `Title` text NOT NULL,
  `Description` text,
  `AuthorID` int(11) NOT NULL,
  `AssignedToID` int(11) NOT NULL,
  `DateCreated` datetime default NULL,
  `DateUpdated` datetime default NULL,
  `Status` varchar(255) default 'new',
  `Hours` double default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=174 ;

-- --------------------------------------------------------

--
-- Table structure for table `Client`
--

CREATE TABLE IF NOT EXISTS `Client` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Name` text default NULL,
  `Description` text,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=9 ;

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
  `ElementType` bigint(11) default NULL,
  `ElementID` bigint(11) default NULL,
  `SortOrder` bigint(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=50 ;

-- --------------------------------------------------------

--
-- Table structure for table `File`
--

CREATE TABLE IF NOT EXISTS `File` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Title` text default NULL,
  `Filename` text default NULL,
  `Description` text,
  `FileFolder` int(11) default '0',
  `Filesize` int(11) default NULL,
  `DateCreated` datetime default NULL,
  `DateModified` datetime default NULL,
  `SortOrder` int(11) default NULL,
  `Filetype` text default NULL,
  `FilenameOriginal` text default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=8 ;

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
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=33 ;

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
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Groups`
--

CREATE TABLE IF NOT EXISTS `Groups` (
  `ID` int(11) NOT NULL auto_increment,
  `GroupID` int(11) NOT NULL default '0',
  `Name` text default NULL,
  `Description` text,
  `SortOrder` int(11) default NULL,
  `TemplateID` int(11) default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

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
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=158 ;

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
-- Table structure for table `ModuleSubscribers`
--

CREATE TABLE IF NOT EXISTS `ModuleSubscribers` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` int(11) NOT NULL,
  `ModuleName` text default NULL,
  `Active` tinyint(4) default '1',
  `Price` double default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

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
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=23 ;

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
-- Table structure for table `ProductOrder`
--

CREATE TABLE IF NOT EXISTS `ProductOrder` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` int(11) NOT NULL,
  `ProductName` text default NULL,
  `OrderText` text,
  `PriceSum` double default NULL,
  `Status` text default NULL,
  `DateOrdered` datetime default NULL,
  `DateFinished` datetime default NULL,
  PRIMARY KEY  (`ID`,`SiteID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `Project`
--

CREATE TABLE IF NOT EXISTS `Project` (
  `ID` int(11) NOT NULL auto_increment,
  `ProjectID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Description` text NOT NULL,
  `Status` text default NULL,
  `DateCreated` datetime NOT NULL,
  `DateModified` datetime NOT NULL,
  `IsActive` tinyint(4) NOT NULL,
  `SiteID` int(11) NOT NULL,
  `ClientID` bigint(20) default NULL,
  `Hours` double default NULL,
  `HourPrice` double default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=38 ;

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
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `ID` int(11) NOT NULL auto_increment,
  `Username` text default NULL,
  `Password` text default NULL,
  `Name` text default NULL,
  `Email` text default NULL,
  `DateCreated` datetime default NULL,
  `DateModified` datetime default NULL,
  `InGroups` tinyint(4) default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `UsersGroups`
--

CREATE TABLE IF NOT EXISTS `UsersGroups` (
  `UserID` bigint(20) NOT NULL,
  `GroupID` bigint(20) NOT NULL,
  PRIMARY KEY  (`UserID`,`GroupID`)
) ENGINE=INNODB CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

