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
  `Version` varchar(255) NOT NULL,
  `Information` text NOT NULL,
  `DateUpdated` datetime default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `BlogItem`
--

CREATE TABLE IF NOT EXISTS `BlogItem` (
  `ID` int(11) NOT NULL auto_increment,
  `UserID` bigint(20) NOT NULL,
  `ContentElementID` bigint(20) NOT NULL,
  `AuthorName` varchar(255) NOT NULL,
  `IsPublished` tinyint(4) NOT NULL default '0',
  `Title` varchar(255) NOT NULL,
  `Leadin` text NOT NULL,
  `Body` text NOT NULL,
  `Tags` varchar(255) NOT NULL,
  `DatePublish` datetime NOT NULL,
  `DateCreated` datetime NOT NULL,
  `DateUpdated` datetime NOT NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `BlogTag`
--

CREATE TABLE IF NOT EXISTS `BlogTag` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) NOT NULL,
  `Rating` bigint(20) NOT NULL default '0',
  `DateUpdated` datetime default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `Comment`
--

CREATE TABLE IF NOT EXISTS `Comment` (
  `ID` int(11) NOT NULL auto_increment,
  `DateCreated` datetime default NULL,
  `DateModified` datetime default NULL,
  `ParentID` int(11) default NULL,
  `Nickname` varchar(64) default NULL,
  `UserID` int(11) default NULL,
  `Subject` varchar(128) default NULL,
  `Message` text,
  `IsDeleted` tinyint(4) default NULL,
  `IsSticky` tinyint(4) default NULL,
  `IsLocked` tinyint(4) default NULL,
  `Moderation` bigint(11) default NULL,
  `ElementType` varchar(255) NOT NULL,
  `ElementID` bigint(11) default NULL,
  `SortOrder` bigint(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `ContentDataBig`
--

CREATE TABLE IF NOT EXISTS `ContentDataBig` (
  `ID` bigint(20) NOT NULL auto_increment,
  `ContentID` int(11) default NULL,
  `ContentTable` varchar(128) default NULL,
  `DataText` text,
  `Name` varchar(128) default NULL,
  `SortOrder` int(11) default '0',
  `Type` varchar(128) default NULL,
  `IsVisible` tinyint(4) default '1',
  `AdminVisibility` tinyint(4) NOT NULL default '1',
  `ContentGroup` varchar(255) default 'Default',
  `IsGlobal` tinyint(4) default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=123 ;

-- --------------------------------------------------------

--
-- Table structure for table `ContentDataSmall`
--

CREATE TABLE IF NOT EXISTS `ContentDataSmall` (
  `ID` bigint(20) NOT NULL auto_increment,
  `ContentID` int(11) default NULL,
  `ContentTable` varchar(128) default NULL,
  `DataString` varchar(255) default NULL,
  `DataMixed` text,
  `DataInt` int(11) default NULL,
  `DataDouble` double default NULL,
  `Name` varchar(128) default NULL,
  `SortOrder` int(11) default '0',
  `Type` varchar(128) default NULL,
  `IsVisible` tinyint(4) default '1',
  `AdminVisibility` tinyint(4) NOT NULL default '1',
  `ContentGroup` varchar(255) default 'Default',
  `IsGlobal` tinyint(4) default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=26858 ;

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
  `Title` varchar(128) default NULL,
  `MenuTitle` varchar(128) default NULL,
  `SystemName` varchar(128) default NULL,
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
  `Link` varchar(255) default NULL,
  `LinkData` text default NULL,
  `Template` varchar(255) default NULL,
  `TemplateArchived` varchar(255) default NULL,
  `TemplateID` int(11) default '0',
  `Author` int(11) default NULL,
  `Version` double default NULL,
  `VersionPublished` double default NULL,
  `Language` varchar(56) default '1',
  `ContentType` varchar(128) default NULL,
  `RouteName` varchar(128) default NULL,
  `IsTemplate` tinyint(4) default '0',
  `IsProtected` tinyint(4) default '0',
  `IsDefault` tinyint(4) default '0',
  `SeenTimesUnique` bigint(20) NOT NULL default '0',
  `SeenTimes` bigint(20) NOT NULL default '0',
  `ContentGroups` varchar(1024) default 'Default',
  `ContentTemplateID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `ContentRoute`
--

CREATE TABLE IF NOT EXISTS `ContentRoute` (
  `ID` bigint(11) NOT NULL auto_increment,
  `Route` varchar(255) default NULL,
  `ElementType` varchar(128) default NULL,
  `ElementID` int(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=65 ;

-- --------------------------------------------------------

--
-- Table structure for table `ElementTag`
--

CREATE TABLE IF NOT EXISTS `ElementTag` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Name` varchar(128) default NULL,
  `Popularity` bigint(20) NOT NULL default '0',
  `DateUpdated` datetime default NULL,
  `Type` varchar(255) default NULL,
  `Description` text,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `File`
--

CREATE TABLE IF NOT EXISTS `File` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Title` varchar(128) default NULL,
  `Filename` varchar(255) default NULL,
  `Description` text,
  `Tags` text NOT NULL,
  `FileFolder` int(11) default '0',
  `Filesize` int(11) default NULL,
  `DateCreated` datetime default NULL,
  `DateModified` datetime default NULL,
  `SortOrder` int(11) default NULL,
  `Filetype` varchar(16) default NULL,
  `FilenameOriginal` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- --------------------------------------------------------

--
-- Table structure for table `Folder`
--

CREATE TABLE IF NOT EXISTS `Folder` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(128) default NULL,
  `Parent` int(11) default NULL,
  `Description` varchar(255) default NULL,
  `DateCreated` datetime default NULL,
  `DateModified` datetime default NULL,
  `DiskPath` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

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
  `Nickname` varchar(64) default NULL,
  `UserID` int(11) default NULL,
  `Subject` varchar(128) default NULL,
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Table structure for table `Groups`
--

CREATE TABLE IF NOT EXISTS `Groups` (
  `ID` int(11) NOT NULL auto_increment,
  `GroupID` int(11) NOT NULL default '0',
  `SuperAdmin` tinyint(4) NOT NULL default '0',
  `Name` varchar(56) default NULL,
  `Description` text,
  `SortOrder` int(11) default NULL,
  `TemplateID` int(11) default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `Guestbook`
--

CREATE TABLE IF NOT EXISTS `Guestbook` (
  `ID` int(11) NOT NULL auto_increment,
  `Date` datetime default NULL,
  `Nickname` varchar(64) default NULL,
  `Message` text,
  `IsDeleted` tinyint(4) default NULL,
  `ContentElementID` int(11) default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Image`
--

CREATE TABLE IF NOT EXISTS `Image` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Title` varchar(128) default NULL,
  `Filename` varchar(255) default NULL,
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
  `Filetype` varchar(16) default NULL,
  `FilenameOriginal` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `Languages`
--

CREATE TABLE IF NOT EXISTS `Languages` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(64) default NULL,
  `NativeName` varchar(128) default NULL,
  `IsDefault` tinyint(4) default '0',
  `UrlActivator` varchar(255) default NULL,
  `BaseUrl` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `Log`
--

CREATE TABLE IF NOT EXISTS `Log` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Type` varchar(255) default NULL,
  `Subject` varchar(255) default NULL,
  `Message` text,
  `DateCreated` datetime default NULL,
  `ObjectType` varchar(255) default NULL,
  `ObjectID` bigint(20) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ModulePermissions`
--

CREATE TABLE IF NOT EXISTS `ModulePermissions` (
  `ID` int(11) NOT NULL auto_increment,
  `UserID` int(11) default '0',
  `GroupID` int(11) default '0',
  `Module` varchar(64) default NULL,
  `Name` varchar(64) default NULL,
  `Read` tinyint(4) default '0',
  `Write` tinyint(4) default '0',
  `Data` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ModulesEnabled`
--

CREATE TABLE IF NOT EXISTS `ModulesEnabled` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteID` int(11) default NULL,
  `Module` varchar(128) default NULL,
  `SortOrder` int(11) default '0',
  `ModuleName` varchar(32) default NULL,
  `ModuleIcon` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=628 ;

-- --------------------------------------------------------

--
-- Table structure for table `News`
--

CREATE TABLE IF NOT EXISTS `News` (
  `ID` int(11) NOT NULL auto_increment,
  `Title` varchar(128) default NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `NewsCategory`
--

CREATE TABLE IF NOT EXISTS `NewsCategory` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(128) default NULL,
  `Description` varchar(255) default NULL,
  `DateCreated` datetime default NULL,
  `SortOrder` int(11) default '0',
  `SystemName` varchar(128) default NULL,
  `Parent` int(11) default '0',
  `Language` int(11) default '1',
  `ContentElementID` int(11) default NULL,
  `DateFormat` varchar(128) default 'Y-m-d H:i',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `Notes`
--

CREATE TABLE IF NOT EXISTS `Notes` (
  `ContentTable` varchar(255) NOT NULL,
  `ContentID` bigint(20) NOT NULL,
  `Notes` text,
  PRIMARY KEY  (`ContentTable`,`ContentID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ObjectConnection`
--

CREATE TABLE IF NOT EXISTS `ObjectConnection` (
  `ID` bigint(20) NOT NULL auto_increment,
  `ConnectionGroupID` int(11) default NULL,
  `ObjectID` bigint(20) default NULL,
  `ObjectType` varchar(64) default NULL,
  `ConnectedObjectID` bigint(20) default NULL,
  `ConnectedObjectType` varchar(64) default NULL,
  `ExtensionObjectID` bigint(20) default NULL,
  `ExtensionObjectType` varchar(64) default NULL,
  `Label` varchar(64) default NULL,
  `SortOrder` int(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `ObjectConnectionGroup`
--

CREATE TABLE IF NOT EXISTS `ObjectConnectionGroup` (
  `ID` int(11) default NULL,
  `Name` varchar(64) default NULL,
  `Description` varchar(255) default NULL,
  `ObjectID` bigint(20) default NULL,
  `ObjectType` varchar(128) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `RSSCategory`
--

CREATE TABLE IF NOT EXISTS `RSSCategory` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(255) default NULL,
  `Description` text,
  `UserID` bigint(20) NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `RSSLink`
--

CREATE TABLE IF NOT EXISTS `RSSLink` (
  `ID` bigint(11) NOT NULL auto_increment,
  `CategoryID` int(11) NOT NULL,
  `Name` varchar(255) default NULL,
  `Description` text,
  `Url` varchar(255) default NULL,
  `UserID` bigint(20) NOT NULL default '0',
  `Cache` longtext,
  `CacheDate` datetime default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `Setting`
--

CREATE TABLE IF NOT EXISTS `Setting` (
  `ID` int(11) NOT NULL auto_increment,
  `SettingType` varchar(255) default NULL,
  `Key` varchar(128) default NULL,
  `Value` text,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `UserCollection`
--

CREATE TABLE IF NOT EXISTS `UserCollection` (
  `ID` bigint(20) NOT NULL auto_increment,
  `UserCollectionID` bigint(20) NOT NULL default '0',
  `ImageID` bigint(20) NOT NULL default '0',
  `Name` varchar(255) default NULL,
  `Description` text,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------

--
-- Table structure for table `Sites`
--

CREATE TABLE IF NOT EXISTS `Sites` (
  `ID` int(11) NOT NULL auto_increment,
  `SiteName` varchar(255) default NULL,
  `SqlUser` varchar(128) default NULL,
  `SqlPass` varchar(128) default NULL,
  `SqlHost` varchar(255) default NULL,
  `SqlDatabase` varchar(128) default NULL,
  `BaseUrl` varchar(255) default NULL,
  `BaseDir` varchar(255) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=124 ;

-- --------------------------------------------------------

--
-- Table structure for table `UserLogin`
--

CREATE TABLE IF NOT EXISTS `UserLogin` (
  `ID` int(11) NOT NULL auto_increment,
  `Token` varchar(256) default NULL,
  `UserID` int(11) default NULL,
  `DataSource` varchar(11) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=916 ;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE IF NOT EXISTS `Users` (
  `ID` bigint(20) NOT NULL auto_increment,
  `Username` varchar(32) default NULL,
  `Password` varchar(255) default NULL,
  `Name` varchar(128) default NULL,
  `Address` varchar(128) default NULL,
  `Country` varchar(128) default NULL,
  `City` varchar(128) default NULL,
  `Postcode` varchar(32) default NULL,
  `Telephone` varchar(56) default NULL,
  `Mobile` varchar(56) default NULL,
  `Email` varchar(128) default NULL,
  `Image` int(11) default NULL,
  `DateCreated` datetime default NULL,
  `DateLogin` datetime default NULL,
  `DateModified` datetime default NULL,
  `IsDisabled` tinyint(4) default '0',
  `InGroups` tinyint(4) default '0',
  `IsAdmin` tinyint(4) default '0',
  `IsTemplate` tinyint(4) default NULL,
  PRIMARY KEY  (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=81 ;

-- --------------------------------------------------------

--
-- Table structure for table `UsersGroups`
--

CREATE TABLE IF NOT EXISTS `UsersGroups` (
  `UserID` bigint(20) NOT NULL,
  `GroupID` bigint(20) NOT NULL,
  PRIMARY KEY  (`UserID`,`GroupID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
