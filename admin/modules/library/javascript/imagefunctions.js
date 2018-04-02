
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

/** ===========================================================================================================
	rediger et bilde
*/
function editLibraryImage( imageID )
{
	document.ModalSelection = false;
	initModalDialogue ( 'EditLevel', 790, 520, modulepath + '&function=editimage&imageID=' + imageID );	
} // end of applyContentActions


/** ===========================================================================================================
	delete image => 2 functions => first confirm then delete
*/
var iecHTML = '';
function deleteLibraryImage( imageID )
{
	if( document.getElementById( "ImageEditContainer" ) )
	{
		
		var confirmtxt = "";
		confirmtxt+= "<h1>" + i18n ( 'i18n_delete_image' ) + " \""+ document.getElementById( "fileTitle" ).value +"\"</h1>";
		confirmtxt+= "<div class='Container'>";
		confirmtxt+= "	<h2 class='question'>" + i18n ( 'i18n_confirm_image_delete' ) + "</h2>";
		confirmtxt+= ' <div class="SpacerSmall"></div><div class="SpacerSmall"></div>';
		confirmtxt+= '	<button onclick="doDeleteLibraryImage(' + imageID  + ');"><img src="admin/gfx/icons/image_delete.png" /> ' + i18n ( 'i18n_delete' ) + '</button>';
		confirmtxt+= '	<button onclick="abortDeleteLibraryImage();"><img src="admin/gfx/icons/cancel.png" /> ' + i18n ( 'i18n_cancel' ) + '</button>';
		confirmtxt+= "</div>";
		
		iecHTML = document.getElementById( "ImageEditContainer" ).innerHTML;
		document.getElementById( "ImageEditContainer" ).innerHTML = confirmtxt;
		
	}
	else if ( confirm ( i18n ( 'i18n_confirm_image_delete' ) ) )
	{
		doDeleteLibraryImage( imageID );
	} 
	

} // end of deleteLibraryImage

function doDeleteLibraryImage( imageID )
{
	document.deljax = new bajax ( );
	document.deljax.openUrl ( modulepath + '&action=deleteimage&imageID=' + imageID, 'GET', true );
	document.deljax.onload = function ( )
	{
		removeModalDialogue ( 'EditLevel' );

		showLibraryContent();
		checkLibraryTooltips();
	
	}
	document.deljax.send ( );
} // end of doDeleteLibraryImage

function abortDeleteLibraryImage()
{
	if( iecHTML != '' && document.getElementById( "ImageEditContainer" ) ) document.getElementById( "ImageEditContainer" ).innerHTML =  iecHTML;
	else removeModalDialogue ( 'EditLevel' );
}


/** ===========================================================================================================
	submit image upload
*/
function submitImageUpload()
{
	// Update dates
	if ( ge('DateFrom_id') )
	{
		ge('DateFrom_id').value = 
			( ge( 'DateFrom_year' ).value + '-' + 
			ge( 'DateFrom_month' ).value + '-' + 
			ge( 'DateFrom_day' ).value );
		ge('DateTo_id').value = 
			( ge( 'DateTo_year' ).value + '-' + 
			ge( 'DateTo_month' ).value + '-' + 
			ge( 'DateTo_day' ).value );
	}
		
	// We want to upload multiple images.
	if ( ge( 'tabMultiple' ) && ge( 'tabMultiple' ).className == 'tabActive' )
	{
		var frm = ge( 'pageMultiple' ).getElementsByTagName ( 'form' )[0];
		frm.submit ();
	}
	// We want to upload one image
	else
	{
		if( ge( 'fileok' ).value == 1 )
		{
			document.uploadForm.submit();
		}
		else if( ge( 'imageID' ) )
		{
			document.uploadForm.submit();
		}
		else
		{
				var emsg = i18n ( 'i18n_no_valid_image_selected' );
				if( ge( 'uploadInfoBox' ) ) ge( 'uploadInfoBox' ).innerHTML = '<div class="SpacerSmall"></div><p class="error">'+ emsg +'</p>';
				else alert( emsg );
		}
	}
} // end of submitImageUpload

/** ===========================================================================================================
	check image upload
*/	
function checkImageUpload()
{
	if ( document.uploadForm.uploadFile && document.uploadForm.uploadFile.value )
	{
		var fileName = document.uploadForm.uploadFile.value.split( /(\/|\\)/ );
		fileName = fileName[fileName.length -1 ];
		
		
		var fileTitle = fileName.replace( /(\.tar)?\.([\w\d]*)$/, "" );
		fileTitle = fileTitle.replace( /(\.|-|_)/g, " " );
		
		if ( document.uploadForm.fileTitle && ( !document.uploadForm.fileTitle.value || document.uploadForm.fileTitle.value == udFileTitle ) )
			udFileTitle = document.uploadForm.fileTitle.value = fileTitle;
		
		uploadInfo = document.getElementById( "uploadInfoBox" );
		
		if ( fileName.toLowerCase().match( /\.(jpg|jpeg|png|gif)$/ )  )
		{
			if( document.getElementById( "uploadInfoBox" ) ) document.getElementById( "uploadInfoBox" ).innerHTML = "";
			document.getElementById( "fileok" ).value = 1;
			// submit here....
		}
		else
		{
			var emsg = i18n ( 'i18n_long_image_upload_error' );
			if( document.getElementById( "uploadInfoBox" ) ) document.getElementById( "uploadInfoBox" ).innerHTML = '<div class="SpacerSmall"></div><p class="error">'+ emsg +'</p>';
			else alert( emsg );
			document.getElementById( "fileok" ).value = 0;
		}
		
	}
	else
	{
		alert("Velg en fil f&oring;rst");
		return false;
	}
} // end of checkImageUpload

/* ===========================================================================================================
	add image to current libary folder
	
	currentLibraryLevel is global variable here
*/
function addLibraryImage()
{
	document.ModalSelection = false;
	initModalDialogue ( 'EditLevel', 540, 390, modulepath + '&function=editimage&folderID=' + currentLibraryLevel );	
} // end of addLibraryImage

