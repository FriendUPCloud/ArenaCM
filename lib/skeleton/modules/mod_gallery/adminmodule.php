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

global $Session, $document;
i18nAddLocalePath ( 'lib/skeleton/modules/mod_gallery/locale' );
include_once ( 'lib/skeleton/modules/mod_gallery/include/admfuncs.php' );
$mtpldir = 'lib/skeleton/modules/mod_gallery/templates/';
$document->addResource('stylesheet', $mtpldir . '../css/admin.css');
$document->addResource('javascript', $mtpldir . '../javascript/admin.js');
$settings = CreateObjectFromString ( $field->DataMixed );

switch( $_REQUEST['modaction'] )
{
	case 'getimagefolder':
		die ( galGetImageFolder ( $settings, $field, $_REQUEST[ 'ind' ] ) );
		break;
	case 'delimage':
		die ( delImage ( $settings, $field, $_REQUEST[ 'ind' ] ) );
		break;
	case 'uploadimage':
		if ( $flds = explode ( ':', $settings->Folders ) )
		{
			include_once ( 'lib/classes/dbObjects/dbImage.php' );
			$im = new dbImage ();
			$im->receiveUpload ( $_FILES[ 'filestream' ] );
			$im->ImageFolder = $flds[0];
			$im->save ();
		}
		die ( '<script>
			var mx_ = parseInt ( parent.ge ( \'galCurrentImageIndexMax\' ).value );
			if ( isNaN ( mx_ ) ) mx_ = 0;
			parent.ge ( \'galCurrentImageIndexMax\' ).value = mx_ + 1;
			parent.ge ( \'galUploadFormen\' ).reset ();
			parent.RefreshGalPreview ();
		</script>' );
		break;
	case 'galmode':
		$settings->currentMode = $_REQUEST[ 'mode' ];
		saveGalSettings ( $settings, $field );
		ob_clean ( );
		header ( 'location: admin.php?module=extensions&extension=' . $_REQUEST[ 'extension' ] );
		die ( );
		break;
	case 'addfolder':
		if ( $_REQUEST[ 'fieldid' ] == $field->ID )
		{
			$out = array ();
			if ( $settings->Folders )
				$Folders = explode ( ":", $settings->Folders );
			else $Folders = array ();
			if ( !in_array ( $_REQUEST[ 'fid' ], $Folders ) )
				$Folders[] = $_REQUEST[ 'fid' ];
			$settings->Folders = implode ( ':', $Folders );
			saveGalSettings ( $settings, $field );
			die ( listFolders ( $settings->Folders, $field ) );
		}
		break;
	case 'removebrick':
		if ( $_REQUEST[ 'fieldid' ] == $field->ID )
		{
			if ( $folders = explode ( ':', $settings->Folders ) )
			{
				$out = array ();
				foreach ( $folders as $f )
				{
					if ( $f != $_REQUEST[ 'folderid' ] )
					{
						$out[] = $f;
					}
				}
				$settings->Folders = implode ( ':', $out );
				saveGalSettings ( $settings, $field );
				die ( listFolders ( $settings->Folders, $field ) );
			}
		}
		break;
	case 'savesettings':
		if ( $_REQUEST[ 'fieldid' ] == $field->ID )
		{
			foreach ( $_POST as $k=>$v )
			{
				if ( !trim ( $v ) && !( $v === '0' ) ) continue;
				if ( substr ( $k, 0, 4 ) == 'key_' )
				{
					$z = substr ( $k, 4, strlen ( $k ) - 4 );
					$settings->$z = $v;
				}
			}
			saveGalSettings ( $settings, $field );
			die ( listFolders ( $settings->Folders, $field ) );
		}
		break;
	case 'savetext':
		die ( saveImageText ( $settings, $field ) );
	case 'preview':
		$inf = getPreviewInfo ( $settings, $field, $_POST[ 'index' ] );
		die( 	
			getPreview ( $settings, $field, $_POST[ 'index' ] ) . '<!--separate-->' . 
			$inf[0] . '<!--separate-->' . 
			$inf[2] . '<!--separate-->' . 
			$inf[3] . '<!--separate-->' . 
			$inf[4] 
		);
	default:
		$mtpl = new cPTemplate ( $mtpldir . 'adm_main.php' );
		$mtpl->field =& $field;
		$mtpl->settings =& $settings;
		$mtpl->preview = getPreview ( $settings, $field );
		$mtpl->previewInfo = getPreviewInfo ( $settings, $field );
		$mtpl->folders = listFolders ( $settings->Folders, $field );
		if ( !$settings->currentMode )
			$settings->currentMode = 'slideshow';
		$mtpl->currentMode = $settings->currentMode;
		$mtpl->Heading = $settings->Heading;
		break;
}

if ($mtpl) $module = $mtpl->render();
?>
