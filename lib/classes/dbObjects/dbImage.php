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
 * class dbImage
 * @c 2006-2007 Blest AS
 * @author Hogne Titlestad
**/
if ( !function_exists ( 'texttourl' ) )
	include_once ( "lib/functions/functions.php" );
if ( !class_exists ( 'dbFolder' ) )
	include_once ( "lib/classes/dbObjects/dbFolder.php" );

define ( 'CaptchaSnow', 1 );

class dbImageFolder extends dbFolder
{
	var $_images = Array ( );
	var $_tableName = "Folder";
	
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
			$this->DiskPath  = 'upload/images-master';
			$this->BasePath  = 'upload';
			$this->CachePath = 'upload/images-cache';
		}
		
		return $result;
	}
	
	function onSave ( )
	{
		if ( strstr ( $this->Filename, '.php' ) || !trim ( $this->Filename ) )
			return false;
		if ( !$this->DiskPath )
		{
			$this->DiskPath = "upload/images-master";
			$this->BasePath = "upload";
		}
		else
		{
			if ( substr ( $this->DiskPath, strlen ( $this->DiskPath ) - 1, 1 ) == "/" )
				$this->DiskPath = substr ( $this->DiskPath, 0, strlen ( $this->DiskPath ) - 1 );
		}
		if ( !$this->ID ) $this->DateCreated = date ( 'Y-m-d H:i:s' );
		
		// Check if filesize changed
		$fs = filesize ( BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename );
		if ( $fs != $this->Filesize )
		{
			$this->Filesize = $fs;
			$this->cleanCache ( );
		}
		$this->DateModified = date ( 'Y-m-d H:i:s' );
	}
	
	function getImages ( $filter = false )
	{
		if ( !$this->_isLoaded )
			$this->load ( );
		if ( !$this->_isLoaded ) return false;

		$objs = new dbImage ( );
		$objs->addClause ( "ORDER BY", "`DateModified` DESC, `DateCreated` DESC" );
		$objs->addClause ( "WHERE", "`ImageFolder`='{$this->ID}'" );
		$_GLOBALS[ 'debug' ] = 1;
		if ( $objs = $objs->find ( ) )
		{	
			$this->_images = $objs;
			return $this->_images;
		}
		return false;
	}
}

	

class dbImage extends dbObject 
{
	var $_tableName  = 'Image';
	var $_mode       = 'jpg';
	var $_bgcolor    = 0x000000;
	var $BasePath    = 'upload';
	var $DiskPath    = 'upload/images-master';
	var $CachePath   = 'upload/images-cache';
	var $_remotePath = 'uninitialized';
	
	function __construct ( $id = false )
	{
		$this->loadTable ( );
		if ( $id ) 
		{
			$r = $this->load ( $id );
		}
		$this->Folder = new dbImageFolder ( );
	}
	
	function load ( $id = false )
	{
		$this->_remotePath = 'uninitialized';
		
		if ( !isset( $this->Folder ) || !$this->Folder )
			$this->Folder = new dbImageFolder ( );
			
		$result = parent::load ( $id );
		
		// Catch disk paths other than the default
		if ( $this->DiskPath != 'upload/images-master' )
		{
			if ( substr ( $this->DiskPath, -1, 1 ) == '/' )
				$this->DiskPath = substr ( $this->DiskPath, 0, strlen ( $this->DiskPath ) - 1 );
			$this->BasePath = $this->DiskPath;
		}
			
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
		
		if ( $this->ImageFolder )
			$this->Folder->load ( $this->ImageFolder );
			
		if ( !$this->Filesize && $this->ID )
		{
			$fn = BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename;
			if ( file_exists ( $fn ) )
				$this->Filesize = filesize ( $fn );
			else return false;
			$this->save ( );
		}
			
		return $result;
	}
	
	function onSave ( )
	{
		$fn = BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename;
		if ( file_exists ( $fn ) && !is_dir ( $fn ) )
		{
			$this->Filename = safeFilename ( $this->Filename );
			$this->Filesize = filesize ( $fn );
		}
		$this->DateModified = date ( "Y-m-d H:i:s" );
		if ( !$this->ID ) $this->DateCreated = $this->DateModified;
	}
	
	/**
	 * Returns image type
	**/
	function getImageType ( )
	{
		list ( , , $type ) = getimagesize ( BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename );
		switch ( $type )
		{
			case IMAGETYPE_JPEG:
				return 'jpeg';
				break;
			case IMAGETYPE_PNG:
				return 'png';
				break;
			case IMAGETYPE_GIF:
				return 'gif';
				break;
			case IMAGETYPE_BMP:
				return 'bmp';
				break;
		}
		return false;
	}
	
	/**
	 * Sets masterimage size
	**/
	function setMasterImageSize ( $width, $height, $quality = 100 )
	{
		// We require a minimum quality
		if ( $quality < 50 ) $quality = 50;
		
		// We don't cale up
		if ( $width > $this->Width || $height > $this->Height )
			return false;
		
		switch ( strtolower ( $this->Filetype ) )
		{
			case 'jpg':
				$image = imagecreatefromjpeg ( BASE_DIR . '/' . $this->getFolderPath ( ) . "/" . $this->Filename );
				$this->_mode = 'jpg';
				break;
			case 'gif':
				$image = imagecreatefromgif ( BASE_DIR . '/' . $this->getFolderPath ( ) . "/" . $this->Filename );
				$this->_mode = 'gif';
				break;
			case 'png':
				$image = imagecreatefrompng ( BASE_DIR . '/' . $this->getFolderPath ( ) . "/" . $this->Filename );
				$this->_mode = 'png';
				break;
			default: return false;
		}
			
		if ( $image )
		{
			$w = $width;
			$h = $this->Height / $this->Width * $w;
			if ( $h > $height )
			{
				$h = $height;
				$w = $this->Width / $this->Height * $h;
			}
			
			$w = floor ( $w );
			$h = floor ( $h );
			
			$image2 = imagecreatetruecolor ( $w, $h );
			if ( $this->Filetype == 'png' )
			{
				imagealphablending ( $image, false );
				imagesavealpha ( $image, true );
			}
			imagecopyresampled ( $image2, $image, 0, 0, 0, 0, $w, $h, $this->Width, $this->Height );
			
			// Keep backup of old file
			if ( $this->Filename )
			{
				$path = BASE_DIR . '/' . $this->getFolderPath ( );
				if ( trim ( $this->BackupFilename ) && file_exists ( $this->BackupFilename ) )
					unlink ( $path . '/' . $this->BackupFilename );
				rename ( $path . '/' . $this->Filename, $path . '/backup_' . $this->Filename );
				$this->BackupFilename = 'backup_' . $this->Filename;
			}
			
			switch ( $this->Filetype )
			{
				case 'jpg':
					imagejpeg ( $image2, BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename, $quality );
					break;
				case 'png':
					imagepng ( $image2, BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename, $quality );
					break;
				case 'gif':
					imagegif ( $image2, BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename, $quality );
					break;
			}
			$this->Width = $w;
			$this->Height = $h;
			$this->Filesize = filesize ( BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename );
			$this->save ( );
			return true;
		}
		return false;
	}
	
	// TODO: Check if this is ok
	function CurlUrlExists ( $url, $param = false )
	{
		$ok = false;
		if( !$url ) return false;
		$c = curl_init();
		curl_setopt( $c, CURLOPT_URL, $url );
		curl_setopt( $c, CURLOPT_NOBODY, 1 );
		curl_setopt( $c, CURLOPT_FAILONERROR, 1 );
		curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
		$r = curl_exec( $c );
		$k = curl_getinfo( $c, CURLINFO_HTTP_CODE );
		if( $r !== false && ( $k == 200 || $k == 301 ) )
		{
			$ok = curl_getinfo( $c );
			// Check if parameters match
			if( $param && strstr( $param, '=>' ) )
			{
				$param = explode( '=>', trim( $param ) );
				if( !strstr( $ok[ trim( $param[0] ) ], trim( $param[1] ) ) )
				{
					$ok = false;
				}
			}
		}
		curl_close( $c );
		return $ok;
	}
	
	/**
	 * 
	**/
	function getImageUrl ( $width = false, $height = false, $mode = false, $effects = false, $bgcolor = false )
	{
		list ( $fn, $ex ) = explode ( ".", $this->Filename );
		$processed = false;
	
		// Add effects that are added aposteriori
		if ( is_array ( $this->_effects ) )
		{
			$eff = implode ( ';', $this->_effects );
			if ( $effects )
				$effects .= ';' . $eff;
			else $effects = $eff;
		}
		
		$extra = ( $mode || $effects ) ? ( texttourl ( $mode ) . '_' . urlencode ( $effects ) ) : '';
		
		if ( !$bgcolor ) $bgcolor = $this->_bgcolor;
		$bcol = hex2string ( $bgcolor );		
		// Do not allow .jpeg
		if ( $this->Filetype == 'jpeg' ) { $this->Filetype = 'jpg'; $this->save (); }
		
		$cacheFilename = "{$this->CachePath}/{$width}x{$height}_{$fn}_{$extra}_0x{$bcol}_{$this->ID}.{$this->Filetype}";
		$oldimg = new dbObject ( 'ContentRoute' );
		$oldimg->Route = $cacheFilename;
		if ( !$oldimg->load ( ) )
		{
			$oldimg = false;
			
			// Delete ghosts...
			if ( file_exists ( $cacheFilename ) )
				unlink ( $cacheFilename );
		}
		
		if ( !$this->ID ) return false;
		
		// Get master image if the width, height and filetype is he same
		if ( 
			( 
				( $width == $this->Width && $height == $this->Height ) ||
				( !$width && !$height ) 
			) 
			&& !$effects && !$mode && 
			strstr ( strtolower ( $this->Filename ), ( '.' . $this->_mode ) ) 
		)
		{
			$path = ( $this->hasRemoteFolderPath () ? '' : BASE_URL ) . $this->getFolderPath ( ) . '/' . $this->Filename;
			if ( $info = @getimagesize ( ( $this->hasRemoteFolderPath () ? '' : BASE_DIR . '/' ) . $this->getFolderPath ( ) . '/' . $this->Filename ) )
			{
				$this->cachedWidth = $info[ 0 ];
				$this->cachedHeight = $info[ 1 ];
			}
			else if ( $mode == 'proximity' )
			{
				$this->cachedWidth = $width; 
				$this->cachedHeight = $height; 
			}
			return $path;
		}
		// Get already existing cache-image 
		else if ( $oldimg && file_exists ( BASE_DIR . '/' . $oldimg->Route ) )
		{
			$path = BASE_URL . $oldimg->Route;
			if ( $info = @getimagesize ( BASE_DIR . '/' . $oldimg->Route ) )
			{
				$this->cachedWidth = $info[ 0 ];
				$this->cachedHeight = $info[ 1 ];
			}
			else if ( $mode == 'proximity' )
			{ 
				$this->cachedWidth = $width; 
				$this->cachedHeight = $height; 
			}
			return $path;
		}
		// Generate image
		else
		{
			if ( !$this->Width || !$this->Height ) 
			{
				list ( $w, $h, ) = @getimagesize ( ( $this->hasRemoteFolderPath () ? '' : BASE_DIR . '/' ) . $this->getFolderPath () . '/' . $this->Filename );
				if ( $w && $h )
				{
					$this->Width = $w;
					$this->Height = $h;
					$this->save ( );
				}
				else return "admin/gfx/icons/page_white.png";
			}
			if ( !$width ) $width = $this->Width;
			if ( !$height ) $height = $this->Height;
			
			/**
			 * Carry out scale modes
			**/
			switch ( $mode )
			{
				case 'proximity':
					$w = $width;
					$h = $this->Height / $this->Width * $width;
					if ( $h > $height )
					{
						$h = $height;
						$w = $this->Width / $this->Height * $height;
					}
					$height = $h;
					$width = $w;
					$ox = 0;
					$oy = 0;
					break;
					
				case 'centered':
					
					$w = $width;
					$h = $this->Height / $this->Width * $width;
					if ( $h > $height )
					{
						$h = $height;
						$w = $this->Width / $this->Height * $height;
					}
					// Center image
					$ox = $width / 2 - ( $w / 2 );
					$oy = $height / 2 - ( $h / 2 );
					break;
					
				// Default is to center and cut image
				default:	
					// Scale
					$w = $width;
					$h = $this->Height / $this->Width * $width;
					if ( $h < $height )
					{
						$h = $height;
						$w = $this->Width / $this->Height * $height;
					}
					
					// Variations
					switch ( $mode )
					{
						case 'cutalignright':
							$ox = $width - $w;
							$oy = $height / 2 - ( $h / 2 );
							break;
						case 'cutalignleft':
							$ox = 0;
							$oy = $height / 2 - ( $h / 2 );
							break;
						default:
							// Center image
							$ox = $width / 2 - ( $w / 2 );
							$oy = $height / 2 - ( $h / 2 );
							break;
					}
					break;
			}
			
			// Round
			$h = floor ( $h );
			$w = floor ( $w );
			$found = true;

			if ( $this->hasRemoteFolderPath () )
			{
				if ( !$this->CurlUrlExists ( $this->getFolderPath ( ) . '/' . $this->Filename ) )
				{
					$found = false;
				}
			}
			else if ( !file_exists ( BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename ) )
			{
				$found = false;
			}
			
			//if ( !file_exists ( BASE_DIR . '/' . $this->getFolderPath ( ) . '/' . $this->Filename ) )
			if ( !$found )
			{
				$image = imagecreatetruecolor ( $this->Width, $this->Height );
				imagefilledrectangle ( $image, 0, 0, $this->Width, $this->Height, 0xeeeeee );
				
				$scalew = 110;
				$scaleh = $scalew / $this->Width * $this->Height;
				
				imagettftext ( 
					$image, 
					13 / $scalew * $this->Width, 
					0, 
					10 / $scalew * $this->Width, 
					$this->Height / 2 + ( 6 / $scaleh * $this->Height ), 
					0x666666, 
					BASE_DIR . '/lib/fonts/FreeSansBold.ttf', 
					'Missing image' 
				);
			}
			else
			{
				// Get info about image
				$fn = ( ( $has = $this->hasRemoteFolderPath () ) ? '' : BASE_DIR . '/' ) . $this->getFolderPath ( ) . "/" . $this->Filename;
				list ( , , $type ) = getimagesize ( $fn );
				if ( !$has )
				{
					$res = stat ( $fn );
					$max = defined ( 'MAX_IMAGE_FILESIZE' ) ? MAX_IMAGE_FILESIZE : 11000000;
					if ( $res[7] > $max ) // temporary maximum allowed byes is 2.5 mb for an image
					{
					   return false;
					}
				}
				switch ( $type )
				{
					case IMAGETYPE_JPEG:
						$image = imagecreatefromjpeg ( $fn );
						break;
					case IMAGETYPE_PNG:
						if ( function_exists ( "imagecreatefrompng" ) )
							$image = imagecreatefrompng ( $fn );
						else return false;
						break;
					case IMAGETYPE_GIF:
						if ( function_exists ( "imagecreatefromgif" ) )
							$image = imagecreatefromgif ( $fn );
						else return false;
						break;
					case IMAGETYPE_BMP:
						if ( function_exists ( "imagecreatefrombmp" ) )
							$image = imagecreatefrombmp ( $fn );
						else return false;
						break;
					// Remove unsupported image!
					default:
						$this->delete();
						return false;
						break;
				}
			}
			
			
			if ( $image )
			{
				// We can't have float values!
				$width = floor ( $width );
				$height = floor ( $height );
				
				if ( $width <= 0 ) $width = 1;
				if ( $height <= 0 ) $height = 1;
				
				$image2 = imagecreatetruecolor ( $width, $height );
				
				if ( $this->Filetype == 'png' )
				{
					imagealphablending ( $image2, false );
					imagesavealpha ( $image2, true );
				}
				
				// From effects arguments
				$color = false;
				if ( $bgcolor )	
				{
					$r = ( $bgcolor >> 16 ) & 0xFF;
					$g = ( ( $bgcolor << 8 ) >> 16 ) & 0xFF;
					$b = ( ( $bgcolor << 16 ) >> 16 ) & 0xFF;
					$color = imagecolorallocate ( $image2, $r, $g, $b );
				}
				// Internal background color
				else if ( $this->Filetype != 'png' )
				{
					$r = ( $this->_bgcolor >> 16 ) & 0xFF;
					$g = ( ( $this->_bgcolor << 8 ) >> 16 ) & 0xFF;
					$b = ( ( $this->_bgcolor << 16 ) >> 16 ) & 0xFF;
					$color = imagecolorallocate ( $image2, $r, $g, $b );
				}
				if ( $color ) 
				{
					imagefilledrectangle ( $image2, 0, 0, $width, $height, $color );
				}
				imagecopyresampled ( $image2, $image, $ox, $oy, 0, 0, $w, $h, $this->Width, $this->Height );
				unset ( $image );
				
				$processed = $cacheFilename;
				$this->data =& $image2;
				$this->currentWidth = $w;
				$this->currentHeight = $h;
			}
			
			/**
			 * Apply graphic effects
			**/
			if ( $effects )
			{
				if ( $effects = explode ( ';', $effects ) )
				{
					foreach ( $effects as $effect )
					{
						list ( $filter, $params ) = explode ( ':', $effect );
						$this->filter ( $filter, $params );
					}
				}
			}
			if ( $debug )
			{
				switch ( $this->_mode )
				{
					case 'gif':
						header ( 'content-type: image/gif' );
						die ( imagegif ( $this->data ) );
					case 'png':
						header ( 'content-type: image/png' );
						die ( imagepng ( $this->data ) );
					default:
						header ( 'content-type: image/jpeg' );
						die ( imagejpeg ( $this->data, false, defined ( 'IMAGE_JPEG_QUALITY' ) ? IMAGE_JPEG_QUALITY : '90' ) );
				}
			}
			if ( $this->data )
			{
				switch ( $this->Filetype )
				{
					case 'gif':
						preg_match ( '/(^.*?)(\.[a-zA-Z]*?)$/', $processed, $matches );
						if ( !trim ( $matches[ 1 ] ) )
							return false;
						$processed = $matches[ 1 ] . '.gif';
						imagegif ( $this->data, BASE_DIR . '/' . $processed );
						break;
					case 'png':
						preg_match ( '/(^.*?)(\.[a-zA-Z]*?)$/', $processed, $matches );
						if ( !trim ( $matches[ 1 ] ) )
							return false;
						$processed = $matches[ 1 ] . '.png';
						imagealphablending ( $this->data, true );
						imagesavealpha ( $this->data, true );
						imagepng ( $this->data, BASE_DIR . '/' . $processed );
						break;
					default:
						preg_match ( '/(^.*?)(\.[a-zA-Z]*?)$/', $processed, $matches );
						if ( !trim ( $matches[ 1 ] ) )
							return false;
						$processed = $matches[ 1 ] . '.jpg';
						imagejpeg ( $this->data, BASE_DIR . '/' . $processed, defined ( 'IMAGE_JPEG_QUALITY' ) ? IMAGE_JPEG_QUALITY : '90' );
						break;
				}
				
				$info = getimagesize ( BASE_DIR . '/' . $processed );
				$this->cachedWidth = $info[ 0 ];
				$this->cachedHeight = $info[ 1 ];

				$this->cachedFilename = $processed;
				$obj = new dbObject ( 'ContentRoute' );
				$obj->Route = $processed;
				$obj->ElementType = 'Image';
				$obj->ElementID = $this->ID;
				$obj->save ();
			}
			
			return $processed ? ( BASE_URL . $processed ) : false;
		}
	}
	
	// Check if the folder path is remote
	function hasRemoteFolderPath ()
	{
		if ( $this->_remotePath == 'uninitialized' )
		{
			$fp = $this->getFolderPath ();
			$this->_remotePath = substr ( $fp, 0, 7 ) == 'http://' || substr ( $fp, 0, 8 ) == 'https://';
		}
		return $this->_remotePath;
	}
	
	function getImageHTML ( $width = false, $height = false, $mode = false, $effects = false, $bgcolor = '' )
	{
		if ( !$width ) $width = $this->Width;
		if ( !$height ) $height = $this->Height;
		if ( $bgcolor ) $this->_bgcolor = $bgcolor;
		$image = $this->getImageUrl ( $width, $height, $mode, $effects, $this->_bgcolor );
		if ( !$mode ) $mode = '0';
		if ( !$effects ) $effects = '0';
		
		// We must get correct virtual filename
		if ( !$this->cachedFilename )
		{
			list ( $this->cachedFilename, ) = explode ( '.', $this->Filename );
			$this->cachedFilename .= '.' . $this->_mode;
		}
		$source = BASE_URL . 'arena-images/' . $width . 'x' . $height . '_' . $mode . '_' . urlencode ( $effects ) . '_0x' . hex2string ( $this->_bgcolor ) . '/' . $this->ID . '/' . $this->cachedFilename;
		
		$w = $this->cachedWidth;
		$h = $this->cachedHeight;
		return "<img src=\"$source\" alt=\"{$this->Title}\" style=\"width: {$w}px; height: {$h}px\" />";
	}
	
	// Checks file on disk and recalculates size
	function recalculateSize ( )
	{
		list ( $width, $height, $type, ) = getimagesize ( BASE_DIR . '/' . $this->getFolderPath () . '/' . $this->Filename );
		$this->Width = $width;
		$this->Height = $height;
		if ( $this->ID )
			$this->save ( );
		return array ( $width, $height );
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
			
			$this->cleanCache();				
			
			$filename = safeFilename( $file[ "name" ] );
			
			$this->FilenameOriginal = $file[ "name" ];
			list ( $this->Width, $this->Height, $type ) = getimagesize ( $file[ "tmp_name" ] );
			
			while ( file_exists ( BASE_DIR . "/$folder/$filename" ) )
				$filename = uniqueFilename ( $filename );
			
			$this->Filename = $filename;
			
			switch ( $type )
			{
				case IMAGETYPE_JPEG:
					$this->Filetype = 'jpg';
					$this->Title = str_replace ( '.jpg', '', $this->Filename );
					break;
				case IMAGETYPE_PNG:
					$this->Filetype = 'png';
					$this->Title = str_replace ( '.png', '', $this->Filename );
					break;
				case IMAGETYPE_GIF:
					$this->Filetype = 'gif';
					$this->Title = str_replace ( '.gif', '', $this->Filename );
					break;
				default:	
					return false;
			}
			
			$path = BASE_DIR . "/$folder/$filename";
			
			if ( !( move_uploaded_file ( $file[ "tmp_name" ], $path ) ) )
				return false;
			
			// Check image rotation
			if( $type == IMAGETYPE_JPEG && function_exists( 'exif_read_data' ) )
			{
				$exif = exif_read_data( $path );
				
				if( !empty( $exif['Orientation'] ) )
				{
					$image = imagecreatefromstring( file_get_contents( $path ) );
					switch( $exif['Orientation'] )
					{
						case 8:
							$image = imagerotate( $image, 90, 0 );
							break;
						case 3:
							$image = imagerotate( $image, 180, 0 );
							break;
						case 6:
							$image = imagerotate( $image, -90, 0 );
							break;
					}
				}
				// Write rotated image
				imagejpeg( $image, $path, 90 );
			}
			
			$this->Filesize = filesize ( BASE_DIR . "/$folder/$filename" );
			return true;
		}
		return false;
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
		$this->cleanCache ( );
		parent::delete ( );
	}
	
	function cleanCache ( )
	{
		$db =& $this->getDatabase ( );
		if ( $rows = $db->fetchRows ( 'SELECT * FROM ContentRoute WHERE ElementType="Image" AND ElementID=\'' . $this->ID . '\'' ) )
		{
			foreach ( $rows as $row )
			{
				$candidate = BASE_DIR . '/' . $obj[ 'Route' ];
				if ( trim ( $row[ 'Route' ] ) && file_exists ( $candidate ) && !is_dir ( $candidate ) )
					unlink ( $candidate );
				$db->query ( 'DELETE FROM ContentRoute WHERE ID=' . $row[ 'ID' ] );
			}
		}
	}
	
	function getMasterImage ( )
	{
		$folder = $this->getFolderPath ( );
		if ( file_exists ( $folder . '/' . $this->Filename ) && !is_dir ( $folder . '/' . $this->Filename ) )
		{
			list ( $this->_masterWidth, $this->_masterHeight, ) = getimagesize ( $folder . '/' . $this->Filename );
			return $folder . '/' . $this->Filename;
		}
		return '';
	}
	
	function getMasterWidth ( )
	{
		$this->getMasterImage ( );
		return $this->_masterWidth;
	}
	
	function getMasterHeight ( )
	{
		$this->getMasterImage ( );
		return $this->_masterHeight;
	}
	
	function getFolderPath ( )
	{
		if ( ( !$this->Folder || !$this->Folder->ID ) && isset ( $this->ImageFolder ) )
			$this->Folder = new dbImageFolder ( $this->ImageFolder );
		if ( substr ( $this->Folder->DiskPath, -1, 1 ) == '/' )
			$this->Folder->DiskPath = substr ( $this->Folder->DiskPath, 0, strlen ( $this->Folder->DiskPath ) - 1 );
		if ( $this->Folder && isset( $this->Folder->DiskPath ) && trim ( $this->Folder->DiskPath ) )
			return $this->Folder->DiskPath;
		else if ( isset ( $this->DiskPath ) )
			return $this->DiskPath;
		return 'upload/images-master';
	}
	
	/**
	* Apply filter to image. Allowed filters:
	*
	* FILTER NAME       PARAMS (params in brackets are optional)               EFFECT
	* ----------------- ------------------------------------------------------ ------------------------------------------------------
	* gaussianblur      [radius(0.25-50)]                                      Apply gaussian blur
	* grayscale           [amount(0-1)]                                          Convert image to grayscale
	* invert                                                                   Invert image
	* unsharp             [amount(0-500), radius(0.25-50), threshold(0-255)]     Apply unsharpen mask (values emulate Photoshop values)
	*
	* @param  string $filter Filter name
	* @param  mixed  $params Parameters
	* @return bool   true    on success
	*/
	function filter ( $filter, $params = false )
	{
		$width = $this->currentWidth ? $this->currentWidth : $this->Width;
		$height = $this->currentHeight ? $this->currentHeight : $this->Height;
		
		/*
		if ( !imageistruecolor ( $this->data ) );
			return false;
		*/
		
		switch ( $filter )
		{
		
			case "gaussianblur":
				if ( !$params )
				{
					$params = 2;
				}
				$this->data = $this->filterGaussianBlur ( $this->data, $params );
				return true;
				break;
			
			// Blur image
			case "blur":
			
				$amount = 2;
				
				$buffer = $this->data;
				
				for ( $y = 0; $y < $height; $y ++ )
				{
					for ( $x = 0; $x < $width; $x ++ )
					{
						// Add colours to array
						$amnt = $amount - 1;
						$amnt = $amnt / 2;
						$colors = Array ( );
						for ( $sy = $y - $amnt; $sy <= $y + $amnt; $sy++ )
						{
							for ( $sx = $x - $amnt; $sx <= $x + $amnt; $sx++ )
							{
								if ( $sx < $width && $sx > 0 && $sy > 0 && $sy < $height )
								{
									$colors[] = $this->getPixel ( $sx, $sy, $buffer );
								}
							}
						}
						foreach ( $colors as $col )
						{
							$color[ 'r' ] += $col[ 'r' ];
							$color[ 'g' ] += $col[ 'g' ];
							$color[ 'b' ] += $col[ 'b' ];
						}
						$colorCount = count ( $colors );
						if ( $colorCount > 0 )
						{
							$color[ 'r' ] = round ( $color[ 'r'] / $colorCount ); if ( $color[ 'r' ] > 255 ) $color[ 'r' ] = 255;
							$color[ 'g' ] = round ( $color[ 'g'] / $colorCount ); if ( $color[ 'g' ] > 255 ) $color[ 'g' ] = 255;
							$color[ 'b' ] = round ( $color[ 'b'] / $colorCount ); if ( $color[ 'b' ] > 255 ) $color[ 'b' ] = 255;
						}
						//$color = round ( ( $color [ 'r' ] + $color [ 'g' ] + $color [ 'b' ] ) / 3 );
						$this->setPixel ( $x, $y, array ( $color['r'], $color['g'], $color['b'] ) );
					}
				}
				return true;
				break; 			
				
			case "brightness":
								
				if ( $params ) $amount = $params; else $amount = 0.5; 			
					
				$buffer = $this->data;
				
				for ( $y = 0; $y < $height; $y++ )
				{
					for ( $x = 0; $x < $width; $x++ )
					{
						$color = $this->getPixel ( $x, $y, $buffer );
						$color[ 'r' ] *= $amount; if ( $color[ 'r' ] > 255 ) $color[ 'r' ] = 255;
						$color[ 'g' ] *= $amount; if ( $color[ 'g' ] > 255 ) $color[ 'g' ] = 255;
						$color[ 'b' ] *= $amount; if ( $color[ 'b' ] > 255 ) $color[ 'b' ] = 255;
						$this->setPixel ( $x, $y, array ( $color[ 'r' ], $color[ 'g' ], $color[ 'b' ] ) );
					}
				}
				return true;	
				
				break;
				
			case "invert":
								
				if ( $params ) $amount = $params; else $amount = 0.5; 			  								
				
				for ( $y = 0; $y < $height; $y++ )
				{
					for ( $x = 0; $x < $width; $x++ )
					{
						$color = $this->getPixel ( $x, $y );
						$color[ 'r' ] -= ( $color[ 'r' ] - ( 255 - $color[ 'r' ] ) ) * $amount;
						$color[ 'g' ] -= ( $color[ 'g' ] - ( 255 - $color[ 'g' ] ) ) * $amount;
						$color[ 'b' ] -= ( $color[ 'b' ] - ( 255 - $color[ 'b' ] ) ) * $amount;
						$this->setPixel ( $x, $y, array ( $color[ 'r' ], $color[ 'g' ], $color[ 'b' ] ) );
					}
				}
				return true;	
				
				break;
				
			case "gamma":
								
				if ( $params )
				{
					list ( $amplitude, $amount ) = $params = preg_split ( "/,[\s]*/", $params ); 
				}
				else
				{
					$amount = 1;
					$amplitude = 1;
				}  			  			
				
				for ( $y = 0; $y < $height; $y++ )
				{
					for ( $x = 0; $x < $width; $x++ )
					{
						$color = $this->getPixel ( $x, $y );											
						$color[ 'r' ] += ( $color[ 'r' ] * ( $amplitude + ( $color[ 'r' ] / 255 ) ) ) * $amount;
						$color[ 'g' ] += ( $color[ 'g' ] * ( $amplitude + ( $color[ 'g' ] / 255 ) ) ) * $amount;
						$color[ 'b' ] += ( $color[ 'b' ] * ( $amplitude + ( $color[ 'b' ] / 255 ) ) ) * $amount;
						
						if ( $color[ 'r' ] > 255 ) $color[ 'r' ] = 255;
						if ( $color[ 'g' ] > 255 ) $color[ 'g' ] = 255;
						if ( $color[ 'b' ] > 255 ) $color[ 'b' ] = 255;
						
						$this->setPixel ( $x, $y, array ( $color[ 'r' ], $color[ 'g' ], $color[ 'b' ] ) );
					}
				}
				return true;	
				
				break;
				
			case "colorize":
			case "tocolor":
				$params = preg_split ( "/,[\s]*/", $params ); 
				$r = 255; $g = 0; $b = 0; 
				$amount = 0.5;
					
				if ( count ( $params ) > 3 )
				{
					list ( $r, $g, $b, $amount ) = $params;
				}
				else if ( count ( $params ) > 1 )
				{
					list ( $r, $g, $b ) = $params;
				}
					
				$buffer = $this->data;
				
				for ( $y = 0; $y < $height; $y++ )
				{
					for ( $x = 0; $x < $width; $x++ )
					{
						$color = $this->getPixel ( $x, $y, $buffer );
						
						$light = ( $color[ 'r' ] + $color[ 'g' ] + $color[ 'b' ] ) / 3;
						
						$ir = $r / 255 * $light;
						$ig = $g / 255 * $light;
						$ib = $b / 255 * $light;
						
						$color[ 'r' ] -= ( $color[ 'r' ] - $ir ) * $amount;
						$color[ 'g' ] -= ( $color[ 'g' ] - $ig ) * $amount;
						$color[ 'b' ] -= ( $color[ 'b' ] - $ib ) * $amount;
						$this->setPixel ( $x, $y, array ( $color[ 'r' ], $color[ 'g' ], $color[ 'b' ] ) );
					}
				}
				return true;	
				break;
			
			// Convert image to grayscale
			case "grayscale":
				
				
				$amount = 0;
				
				if ( $params )
				{
					$amount = $params;
				}
				if ( !$amount ) return false;

				if ( $amount < 1 )
				{
					for ( $y = 0; $y < $height; $y ++ )
					{
						for ( $x = 0; $x < $width; $x ++ )
						{
							$color = $this->getPixel ( $x, $y );
							$newcolor = floor ( ( $color [ 'r' ] + $color [ 'g' ] + $color [ 'b' ] ) / 3 );
							$newcolor = array ( $newcolor, $newcolor, $newcolor );						
							$newcolor[ 0 ] = round ( $color[ 'r' ] - ( ( $color[ 'r' ] - $newcolor[ 0 ] ) * $amount ) );
							$newcolor[ 1 ] = round ( $color[ 'g' ] - ( ( $color[ 'g' ] - $newcolor[ 1 ] ) * $amount ) );
							$newcolor[ 2 ] = round ( $color[ 'b' ] - ( ( $color[ 'b' ] - $newcolor[ 2 ] ) * $amount ) );
							$this->setPixel ( $x, $y, $newcolor );
						}
					}
				}
				else
				{
					for ( $y = 0; $y < $height; $y ++ )
					{
						for ( $x = 0; $x < $width; $x ++ )
						{
							$color = $this->getPixel ( $x, $y );
							$newcolor = floor ( ( $color [ 'r' ] + $color [ 'g' ] + $color [ 'b' ] ) / 3 );
							$newcolor = array ( $newcolor, $newcolor, $newcolor );						
							$this->setPixel ( $x, $y, $newcolor );
						}
					}
				}
				return true;
				break; 
				
			case 'transback':
				
				
				// Find transcolor
				// TODO: Add feather to the effect...
				// TODO: implement alphablending
				// TODO: Fix effect which is forgotten!
				
				$max = 0;
				$in = 0;
				$colorhits = Array ( );
				list ( $feather, $threshold, $key ) = explode ( ',', $params );
				$key = string2hex ( $key );
				
				for ( $y = 0; $y < $this->Height; $y++ )
				{
					for ( $x = 0; $x < $this->Width; $x++ )
					{
						$i = $this->colorDifference ( ( $ca = imagecolorat ( $this->data, $x, $y ) ), $key );
						if ( $i <= $threshold )
						{
							$r2 = ( $ca >> 16 ) & 0xff;
							$g2 = ( ( $ca << 8 ) >> 16 ) & 0xff;
							$b2 = ( ( $ca << 16 ) >> 16 ) & 0xff;
							
							$r1 = ( $this->_bgcolor >> 16 );
							$g1 = ( ( $this->_bgcolor << 8 ) >> 16 ) & 0xff;
							$b1 = ( ( $this->_bgcolor << 16 ) >> 16 ) & 0xff;
							
							$u = 100 - $threshold;
							$i = $i * 100 / $u;
							$i = $i / $u;
							
							$r1 -= ( $r1 - $r2 ) * $i;
							$g1 -= ( $g1 - $g2 ) * $i;
							$b1 -= ( $b1 - $b2 ) * $i;
							

							$col = ( 0 << 24 ) | ( ( int )$r1 << 16 ) | ( ( int )$g1 << 8 ) | ( ( int )$b1 );
							
							//print ( $i . '<br/>' );
							
							imagesetpixel ( $this->data, $x, $y, $col );
						}							
					}
				}
				return true;
				
			case "unsharp":
				if ( $params )
				{
					$params = preg_split ( "/,[\s]*/", $params );
				}
				else
				{
					$params = array ( 100, 0.5, 3 );
				}
				$this->data = $this->filterUnsharpMask ( $this->data, $params[0], $params[1], $params[2] );
				return true;
				
			default:
				return false;
		}
	}
	
	function filterByString ( $filters )
	{
		$filters = explode ( ";", $filters );
		foreach ( $filters as $filter )
		{
			$filter = explode ( ":", trim( $filter ) );
			if ( $filter[1] )
				$this->filter ( $filter[0], $filter[1] );
			else
				$this->filter ( $filter[0] );
		}
	}
	
	function filterUnsharpMask ( $img, $amount, $radius, $threshold )    
	{
		// Limit amount to max 500 and convert it from Photoshop format
		$amount = ( $amount > 500 ) ? 500 : $amount;
		$amount = $amount * 0.016;

		// Limit radius to max 50 and convert it from Photoshop format
		$radius = ( $radius > 50 ) ? 50 : $radius;
		$radius = abs ( round ( $radius * 2 ) );

		// Limit threshold to max 255
		$threshold = $threshold & 0xff; //( $threshold > 255 ) ? 255 : $threshold;
		
		// Fail if radius is 0    
		if ( $radius == 0 ) return $img;
		
		
		$w = imagesx($img); $h = imagesy($img);
		$imgCanvas = imagecreatetruecolor($w, $h);
		$imgCanvas2 = imagecreatetruecolor($w, $h);
		$imgBlur = imagecreatetruecolor($w, $h);
		$imgBlur2 = imagecreatetruecolor($w, $h);
		imagecopy ($imgCanvas, $img, 0, 0, 0, 0, $w, $h);
		imagecopy ($imgCanvas2, $img, 0, 0, 0, 0, $w, $h);
		

		// Gaussian blur matrix:
		//                        
		//    1    2    1        
		//    2    4    2        
		//    1    2    1        
		//                        
		//////////////////////////////////////////////////

		// Move copies of the image around one pixel at the time and merge them with weight
		// according to the matrix. The same matrix is simply repeated for higher radii.
		for ($i = 0; $i < $radius; $i++)    
		{
			imagecopy ( $imgBlur, $imgCanvas, 0, 0, 1, 1, $w - 1, $h - 1 ); // up left
			imagecopymerge ( $imgBlur, $imgCanvas, 1, 1, 0, 0, $w, $h, 50 ); // down right
			imagecopymerge ( $imgBlur, $imgCanvas, 0, 1, 1, 0, $w - 1, $h, 33.33333 ); // down left
			imagecopymerge ( $imgBlur, $imgCanvas, 1, 0, 0, 1, $w, $h - 1, 25 ); // up right
			imagecopymerge ( $imgBlur, $imgCanvas, 0, 0, 1, 0, $w - 1, $h, 33.33333 ); // left
			imagecopymerge ( $imgBlur, $imgCanvas, 1, 0, 0, 0, $w, $h, 25 ); // right
			imagecopymerge ( $imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 20 ); // up
			imagecopymerge ( $imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 16.666667 ); // down
			imagecopymerge ( $imgBlur, $imgCanvas, 0, 0, 0, 0, $w, $h, 50 ); // center
			imagecopy ( $imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h );

			// During the loop above the blurred copy darkens, possibly due to a roundoff
			// error. Therefore the sharp picture has to go through the same loop to
			// produce a similar image for comparison. This is not a good thing, as processing
			// time increases heavily.
			imagecopy ( $imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h );
			imagecopymerge ( $imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50 );
			imagecopymerge ( $imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333 );
			imagecopymerge ( $imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25 );
			imagecopymerge ( $imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 33.33333 );
			imagecopymerge ( $imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 25 );
			imagecopymerge ( $imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 20 );
			imagecopymerge ( $imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 16.666667 );
			imagecopymerge ( $imgBlur2, $imgCanvas2, 0, 0, 0, 0, $w, $h, 50 );
			imagecopy ( $imgCanvas2, $imgBlur2, 0, 0, 0, 0, $w, $h );
				
		}

		// Calculate the difference between the blurred pixels and the original
		// and set the pixels
		for ($x = 0; $x < $w; $x++)    
		{ // each row
				for ($y = 0; $y < $h; $y++)    
				{ // each pixel
								
						$rgbOrig = ImageColorAt ( $imgCanvas2, $x, $y );
						$rOrig = ( ( $rgbOrig >> 16 ) & 0xFF );
						$gOrig = ( ( $rgbOrig >> 8 ) & 0xFF );
						$bOrig = ( $rgbOrig & 0xFF );
						
						$rgbBlur = ImageColorAt($imgCanvas, $x, $y);
						
						$rBlur = (($rgbBlur >> 16) & 0xFF);
						$gBlur = (($rgbBlur >> 8) & 0xFF);
						$bBlur = ($rgbBlur & 0xFF);
						
						// When the masked pixels differ less from the original
						// than the threshold specifies, they are set to their original value.
						$rNew = ( abs ( $rOrig - $rBlur ) >= $threshold )
								? max ( 0, min ( 255, ( $amount * ( $rOrig - $rBlur ) ) + $rOrig ) )
								: $rOrig;
						$gNew = (abs($gOrig - $gBlur) >= $threshold)
								? max ( 0, min ( 255, ( $amount * ( $gOrig - $gBlur ) ) + $gOrig ) )
								: $gOrig;
						$bNew = (abs($bOrig - $bBlur) >= $threshold)
								? max ( 0, min ( 255, ( $amount * ( $bOrig - $bBlur ) ) + $bOrig ) )
								: $bOrig;
						
						
												
						if ( ( $rOrig != $rNew ) || ( $gOrig != $gNew ) || ( $bOrig != $bNew ) ) 
						{
							$pixCol = ImageColorAllocate ( $img, $rNew, $gNew, $bNew );
							ImageSetPixel ( $img, $x, $y, $pixCol );
						}
				}
		}

		imagedestroy ( $imgCanvas );
		imagedestroy ( $imgCanvas2 );
		imagedestroy ( $imgBlur );
		imagedestroy ( $imgBlur2 );
		
		return $img;
	}

	function filterGaussianBlur ( $img, $radius )    
	{
		// Limit radius to max 50 and convert it from Photoshop format
		$radius = ( $radius > 50 ) ? 50 : $radius;
		$radius = abs ( round ( $radius * 2 ) );
		
		// Fail if radius is 0    
		if ( $radius == 0 ) return $img;
		
		$w = imagesx($img); $h = imagesy($img);
		$imgCanvas = imagecreatetruecolor($w, $h);
		$imgBlur = imagecreatetruecolor($w, $h);
		imagecopy ($imgCanvas, $img, 0, 0, 0, 0, $w, $h);
		
		for ($i = 0; $i < $radius; $i++)    
		{
			imagecopy ($imgBlur, $imgCanvas, 0, 0, 1, 1, $w - 1, $h - 1); // up left
			imagecopymerge ($imgBlur, $imgCanvas, 1, 1, 0, 0, $w, $h, 50); // down right
			imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 1, 0, $w - 1, $h, 33.33333); // down left
			imagecopymerge ($imgBlur, $imgCanvas, 1, 0, 0, 1, $w, $h - 1, 25); // up right
			imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 1, 0, $w - 1, $h, 33.33333); // left
			imagecopymerge ($imgBlur, $imgCanvas, 1, 0, 0, 0, $w, $h, 25); // right
			imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 1, $w, $h - 1, 20 ); // up
			imagecopymerge ($imgBlur, $imgCanvas, 0, 1, 0, 0, $w, $h, 16.666667); // down
			imagecopymerge ($imgBlur, $imgCanvas, 0, 0, 0, 0, $w, $h, 50); // center
			imagecopy ($imgCanvas, $imgBlur, 0, 0, 0, 0, $w, $h);
		}
		imagecopy ($img, $imgCanvas, 0, 0, 0, 0, $w, $h);			
		imagedestroy($imgCanvas);
		imagedestroy($imgBlur);
		
		return $img;

	}
	
	function getPixel ( $x, $y, $data = false )
	{
		if ( !$data )
		{
			$color = imagecolorat ( $this->data, $x, $y );
		}
		else
		{
			$color = imagecolorat ( $data, $x, $y );
		}
		return array ( 'r' =>  ( ($color >> 16) & 0xFF ), 'g' =>  ( ($color >> 8) & 0xFF ), 'b' => ( $color & 0xFF ) );
	}
	
	
	function setPixel ( $x, $y, $color, $data = false )
	{	
		$color = ( $color[0] << 16 ) | ( $color[1] << 8 ) | $color[2];
		if ( !$data )
		{	
			imagesetpixel ( $this->data, $x, $y, $color );
		}
		else
		{
			imagesetpixel ( $data, $x, $y, $color );
		}
	}
	
	// Clean up the captcha images older than 3 minutes
	function cleanUpCaptchaFolder ( )
	{
		if ( $dir = opendir ( $this->CachePath ) )
		{
			$time = time();
			while ( $file = readdir ( $dir ) )
			{
				if ( $file[0] == '.' ) continue;
				if ( !strstr ( $file, 'captcha' ) ) continue;
				$stat = stat ( $this->CachePath . '/' . $file );
				if ( ( ( $time - $stat[9] ) / 60 ) > 3 )
				{
					unlink ( $this->CachePath . '/' . $file );
				}
			}
			closedir ( $dir );
		}
	}
	
	/**
	 * Renders a captcha to disk and returns the url and the string which is contained in it
	 * Parameters are optional
	 * @param $width int
	 * @param $height int
	 * @param $background int
	 * @param $foreground int
	 * @param $backimg mixed (int flags/object dbImage)
	 * @param $text string
	 *
	 * @return Array ( $htmlImageTag, $Textcode )
	 * @return false
	**/
	function renderCaptcha ( $width = false, $height = false, $background = 0xffffff, $foreground = 0x000000, $backimg = false, $text = false )
	{
		if ( $this->ID ) return false;
		$this->cleanUpCaptchaFolder ( );
		if ( !$width ) $width = 200;
		if ( !$height ) $height = 90;
		if ( !$text )
		{
			$letters = 'abcdefghijklmnopqrstuvwxyz0123456789';
			$out = '';
			for ( $a = 0; $a < 6; $a++ )
			{
				$o = rand ( 0, 10 ) > 5 ? '1' : '0';
				if ( $o == '1' )
					$out .= $letters[ rand ( 0, strlen ( $letters ) - 1 ) ];
				else $out .= strtoupper ( $letters[ rand ( 0, strlen ( $letters ) - 1 ) ] );
			}
		}
		else if ( strlen ( $text ) > 6 ) $text = substr ( $text, 0, 5 );
		
		$img = imagecreatetruecolor ( $width, $height );
		
		$img2 = imagecreatetruecolor ( 200, 90 );
		if ( !$backimg || ( is_object ( $backimg ) && !$backimg->ID ) )
		{
			imagefilledrectangle ( $img2, 0, 0, 200, 90, $background );
			imagefilledrectangle ( $img, 0, 0, 200, 90, $background );
		}
		else if ( $backimg == CaptchaSnow )
		{
			for ( $y = 0; $y < 90; $y++ )
			{
				for ( $x = 0; $x < 200; $x++ )
				{
					$bf = 0xff & ( $foreground << 8 >> 24 );
					$gf = 0xff & ( ( $foreground << 16 ) >> 24 );
					$rf = 0xff & ( ( $foreground << 24 ) >> 24 );					
					$bb = 0xff & ( $background << 8 >> 24 );
					$gb = 0xff & ( ( $background << 16 ) >> 24 );
					$rb = 0xff & ( ( $background << 24 ) >> 24 );					
					
					$rf -= ( $rf - $rb ) * ( rand ( 20, 100 ) / 100.0 );
					$gf -= ( $gf - $gb ) * ( rand ( 20, 100 ) / 100.0 );
					$bf -= ( $bf - $bb ) * ( rand ( 20, 100 ) / 100.0 );
					
					imagesetpixel ( $img2, $x, $y, ( $rf | $gf << 8 | $bf << 16 ) );
					imagesetpixel ( $img, $x, $y, ( $rf | $gf << 8 | $bf << 16 ) );
				}
			}
		}
		// Image
		else if ( is_object ( $backimg ) )
		{
			$url = $backimg->getImageUrl ( 200, 90, 'proximity' );
			imagecopyresampled ( $img2, $backimg->data, 0, 0, 0, 0, 200, 90, $backimg->currentWidth, $backimg->currentHeight );
			imagecopyresampled ( $img, $backimg->data, 0, 0, 0, 0, 200, 90, $backimg->currentWidth, $backimg->currentHeight );
			unlink ( str_replace ( BASE_URL, '', $url ) );
		}
		else return false;
		
		$scalew = 200;
		$scaleh = $scalew / $width * $height;
		
		$r = imagettftext ( 
			$img2, 
			32, 
			0, 
			3, 
			33, 
			$foreground, 
			BASE_DIR . '/lib/fonts/FreeSerifItalic.ttf', 
			$out
		);
		
		// Delete old images stored yesterday
		$upath = BASE_DIR . '/' . $this->DiskPath;
		if ( $udir = opendir ( $upath ) )
		{
			list ( , $now ) = explode ( ' ', microtime ( ) );
			$yesterday = $now - ( 60 * 60 * 24 );
			while ( $file = readdir ( $udir ) )
			{
				if ( $file[0] == '.' ) continue;
				if ( !strstr ( $file, 'captcha' ) ) continue;
				list ( , $time, ) = explode ( '_', $file );
				if ( $time < $yesterday )
				{
					unlink ( $upath . '/' . $file );
				}
			}
			closedir ( $udir );
		}
		
		$fn = BASE_DIR . '/' . $this->DiskPath . '/' . texttourl ( microtime ( ) ) . '_captcha.jpg';
		
		$x1 = $r[ 6 ];
		$y1 = $r[ 7 ];
		$x2 = $r[ 2 ];
		$y2 = $r[ 3 ];
		
		$w = abs ( $x2 - $x1 );
		$h = abs ( $y2 - $y1 );
		
		$dx = $width / 2 - ( $w / 2 );
		$dy = $height / 2 - ( $h / 2 );
		
		imagecopyresampled ( $img, $img2, $dx, $dy, $x1, $y1, $w, $h, $w, $h );
		$img2 = imagecreatetruecolor ( $width, $height );
		imagecopyresampled ( $img2, $img, 0, 0, 0, 0, $width, $height, $width, $height );
		
		$ypow = rand ( 6, 14 );
		$xpow = rand ( 6, 14 );
		$xamp = 20 + rand ( 0, 20 );
		$yamp = 20 + rand ( 0, 20 );
		
		for ( $y = 0; $y < $height; $y++ )
		{
			for ( $x = 0; $x < $width; $x++ )
			{
				$p = imagecolorat ( $img2, $x, $y );
				$siny = cos ( $x / $xamp ) * $ypow;
				$sinx = sin ( $y / $yamp ) * $xpow;
				
				$sx = $sinx - ( $xpow / 2 ) + $x - 3;
				$sy = $siny + $y;
				
				if ( $sx < 0 || $sx >= $width || $sy < 0 || $sy >= $height ) continue;
				imagesetpixel ( $img, $sx, $sy, $p );
			}	
		}
		
		unset ( $img2 );
		imagejpeg ( $img, $fn, defined ( 'IMAGE_JPEG_QUALITY' ) ? IMAGE_JPEG_QUALITY : '90' );
		
		return array ( '<img src="' . str_replace ( BASE_DIR . '/', BASE_URL, $fn ) . '" width="' . $width . '" height="' . $height . '" alt="captcha" class="Captcha"/>', $out );
	}
	
	function setBackgroundColor ( $color )
	{
		// Set the 32-bit value
		$this->_bgcolor = $color;
	}
	
	function setOutputMode ( $mode )
	{
		switch ( strtolower ( $mode ) )
		{
			case 'jpg':
			case 'gif':
			case 'png':
				$this->_mode = strtolower ( $mode );
				break;
			default:
				$this->_mode = 'jpg';
				break;
		}
	}
	
	/**
	 * Find the background color and make it transparent
	**/
	function setBackgroundTransparent ( $featherWidth, $threshold, $keycolor )
	{
		// Automatically sets image mode to png
		$this->setOutputMode ( 'PNG' );
		$this->_effects[] = 'transback:' . ( string )$featherWidth . ',' . ( string )$threshold . ',' . ( string )hex2string ( $keycolor );
	}
	
	/**
	 * Get the difference in color in percent
	**/
	function colorDifference ( $color1, $color2 )
	{
		// Return color difference in percent
		$r1 = ( $color1 >> 16 ) & 0xFF;
		$g1 = ( ( $color1 << 8 ) >> 16 ) & 0xFF;
		$b1 = ( ( $color1 << 16 ) >> 16 ) & 0xFF;
		$r2 = ( $color2 >> 16 ) & 0xFF;
		$g2 = ( ( $color2 << 8 ) >> 16 ) & 0xFF;
		$b2 = ( ( $color2 << 16 ) >> 16 ) & 0xFF;
		
		$p1 = abs ( $r1 - $r2 );
		$p2 = abs ( $g1 - $g2 );
		$p3 = abs ( $b1 - $b2 );
		
		return ( $p1 + $p2 + $p3 ) / 765 * 100;
	}
}
?>
