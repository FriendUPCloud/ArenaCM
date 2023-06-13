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


if ( !function_exists ( 'texttourl' ) )
	include_once ( "lib/functions/functions.php" );
if ( !class_exists ( 'dbFolder' ) )
	include_once ( "lib/classes/dbObjects/dbFolder.php" );

class dbFileFolder extends dbFolder
{
	var $_files = Array ( );
	var $_tableName = "Folder";
	var $DiskPath = 'upload';
	
	function __construct ( $id = false )
	{
		$this->loadTable ( );
		if ( $id ) $this->load ( $id );
	}
	
	function load ( $id = false )
	{
		unset ( $this->DiskPath ); // <- this one must not be in the query
		$result = parent::load ( $id );
		
		if ( !trim ( $this->DiskPath ) )
		{
			$this->DiskPath = "upload";
		}
		
		return $result;
	}
	
	function onSave ( )
	{
		if ( strstr ( $this->Filename, '.php' ) || !trim ( $this->Filename ) )
			return false;
		if ( !$this->DiskPath )
			$this->DiskPath = "upload";
		else
			if ( substr ( $this->DiskPath, strlen ( $this->DiskPath ) - 1, 1 ) == "/" )
				$this->DiskPath = substr ( $this->DiskPath, 0, strlen ( $this->DiskPath ) - 1 );
		if ( !$this->ID || !$this->DateCreated ) $this->DateCreated = date ( 'Y-m-d H:i:s' );
		$this->DateModified = date ( 'Y-m-d H:i:s' );
	}
	
	function getFiles ( $filter = false )
	{
		if ( !$this->_isLoaded )
			$this->load ( );
		if ( !$this->_isLoaded ) return false;
		
		$objs = new dbFile ( );
		$objs->addClause ( "ORDER BY", "`DateModified` DESC, `DateCreated` DESC" );
		$objs->addClause ( "WHERE", "`FileFolder`='{$this->ID}'" );
		if ( $objs = $objs->find ( ) )
		{
			$this->_files = $objs;
			return $this->_files;
		}
		return false;
	}
}

	

class dbFile extends dbObject 
{
	var $_tableName = "File";
	
	function __construct ( $id = false )
	{
		$this->loadTable ( );
		if ( $id ) $this->load ( $id );
		$this->Folder = new dbFileFolder ( );
	}
	
	function load ( $id = false )
	{
		if ( !$this->Folder )
		{
			$this->Folder = new dbFileFolder ( );
			if ( $this->DiskPath )
				$this->Folder->DiskPath = $this->DiskPath;
		}
		
		$result = parent::load ( $id );
			
		// Security check
		if ( substr ( $this->Filename, -4, 4 ) == '.php' )
		{
			$newfilename = $this->Filename . rand(0,999).rand(0,999).rand(0,999);
			rename ( 
				BASE_DIR . '/' . $this->getFolderPath () . '/' . $this->Filename, 
				BASE_DIR . '/' . $this->getFolderPath () . '/' . $newfilename
			);
			$this->Filename = $newfilename;
			$this->save ();
		}
		
		if ( $this->FileFolder )
			$this->Folder->load ( $this->FileFolder );
			
		if ( !$this->Filesize && $this->ID )
		{
			$filename = BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename;
			if ( file_exists ( $filename ) && !is_dir ( $filename ) )
			{
				$this->Filesize = filesize ( BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename );
				$this->save ( );
			}
			else
			{
				if ( $this->fixBrokenFilename ( ) )
				{
					$this->save ( );
				}
			}
		}
			
		return $result;
	}
	
	function fixBrokenFilename ( )
	{
		if ( $d = opendir ( $this->DiskPath ) )
		{
			while ( $file = readdir ( $d ) )
			{
				if ( $file{0} == '.' ) continue;
				if ( safeFilename ( $file ) == $this->Filename )
				{
					$this->Filename = $file;
					return true;
				}
			}
			closedir ( $d );
		}
		return false;
	}
	
	function onSave ( )
	{
		if ( !$this->ID ) $this->Filename = safeFilename ( $this->Filename );
		$fn = BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename;
		if( file_exists( $fn ) )
		{
			$this->Filesize = filesize ( $fn );
		}
		else $this->Filesize = 0;
	}
	
	function getUrl ( )
	{
		return BASE_URL . $this->getFolderPath ( ) . '/' . $this->Filename;
	}
	
	function importFile ( $file )
	{
		if ( file_exists ( $file ) )
		{
			$folder = $this->getFolderPath ( );
			$filename = safeFilename ( $file );
			$this->FilenameOriginal = $file;
			while ( file_exists ( BASE_DIR . "/$folder/$filename" ) )
				$filename = uniqueFilename ( $filename );
			if ( !( copy ( $file, BASE_DIR . "/$folder/$filename" ) ) )
				return false;
			$this->Filename = $filename;
			list ( $this->Title, $this->Filetype ) = explode ( ".", $filename );
			$this->Filesize = filesize ( BASE_DIR . "/$folder/$filename" );
			return true;
		}
		return false;
	}
	
	function receiveUpload ( $file )
	{
		if ( strstr ( $file[ "name" ], '.php' ) )
		{
			$this->Filename = '';
			$this->Title = '';
			return false;
		}
		if ( $file[ "tmp_name" ] )
		{
			$folder = $this->getFolderPath ( );

			// Keep backup of old file
			if ( $this->Filename )
			{
				$path = BASE_DIR . '/' . $this->getFolderPath ( );
				if ( trim ( $this->BackupFilename ) && file_exists ( $this->BackupFilename ) )
					unlink ( $path . '/' . $this->BackupFilename );
				rename ( $path . '/' . $this->Filename, $path . '/backup_' . $this->Filename );
				$this->BackupFilename = 'backup_' . $this->Filename;
			}
			
			// save new one ----------------------------------------------------------------------------------------
			$filename = safeFilename( $file[ "name" ] );
			
			$this->FilenameOriginal = $file[ "name" ];
			
			while ( file_exists ( BASE_DIR . "/$folder/$filename" ) )
				$filename = uniqueFilename ( $filename );
			if ( !( copy ( $file[ "tmp_name" ], BASE_DIR . "/$folder/$filename" ) ) )
				return false;
				
			unlink ( $file[ "tmp_name" ] );
			
			$this->Filename = $filename;
			$this->Filetype = end ( explode ( '.', $filename ) );
			$this->Title = str_replace ( '.' . $this->Filetype, '', str_replace ( ' ', '_', $filename ) );
			$this->Filesize = filesize ( BASE_DIR . "/$folder/$filename" );
			
			return true;
		}
		return false;
	}
	
	function getFolderPath ( )
	{
		if ( ( !$this->Folder || !$this->Folder->ID ) && isset ( $this->FileFolder ) )
			$this->Folder = new dbFileFolder ( $this->FileFolder );
		if ( substr ( $this->Folder->DiskPath, -1, 1 ) == '/' )
			$this->Folder->DiskPath = substr ( $this->Folder->DiskPath, 0, strlen ( $this->Folder->DiskPath ) - 1 );
		if ( $this->Folder && isset( $this->Folder->DiskPath ) && trim ( $this->Folder->DiskPath ) )
			return $this->Folder->DiskPath;
		else if ( isset ( $this->DiskPath ) )
			return $this->DiskPath;
		return 'upload';
	}
	
	function delete ( )
	{
		if ( !$this->ID ) return false;
		$folder = $this->getFolderPath ( );
		foreach ( array ( $this->Filename, $this->BackupFilename ) as $fnm )
		{
			$fn = BASE_DIR . "/$folder/{$fnm}";
			if ( file_exists ( $fn ) && !is_dir ( $fn ) )
				unlink ( $fn );
		}
		parent::delete ( );
	}
	
	function getIcon( $xtra)
	{
		return '<img src="'. $this->getIconPath() .'" '. $xtra .'/>';
	}
	
	function getIconPath( )
	{
	
		$suffix = strtolower( substr( $this->Filename, strrpos( $this->Filename, '.' ) + 1 ) );
	
	
		switch( $suffix )
		{
			case 'txt':
			case 'log':
				return 'admin/gfx/icons/page_white_text.png';
			
			case 'rtf':
			case 'doc':
			case 'doc':
				return 'admin/gfx/icons/page_white_word.png';
				
			case 'pdf':
				return 'admin/gfx/icons/page_white_acrobat.png';
				
			case 'csv':
			case 'ods':
			case 'xls':
				return 'admin/gfx/icons/page_white_excel.png';
			
			case 'swf':
			case 'fla':
			case 'flv':
			case 'as':
				return 'admin/gfx/icons/page_white_flash.png';
				
			case 'ppt':
				return 'admin/gfx/icons/page_white_powerpointflash.png';
			
			case 'zip':
			case 'rar':
			case 'sit':
			case 'ace':
			case 'gz':
				return 'admin/gfx/icons/page_white_zip.png';
			
			case 'sql':
			case 'mysql':
			case 'mssql':
			case 'psql':
				return 'admin/gfx/icons/database.png';
			#case '':
			
			default:
				return 'admin/gfx/icons/page_white.png';
		}
		
		return '';
		
	}
}

?>
