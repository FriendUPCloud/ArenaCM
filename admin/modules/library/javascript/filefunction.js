
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
	delete image => 2 functions => first confirm then delete
*/
var iecHTML = '';
function deleteLibraryFile( fileID )
{
	if( document.getElementById( "FileEditContainer" ) )
	{
		
		var confirmtxt = "";
		confirmtxt+= "<h1>" + i18n ( 'i18n_delete_file' ) + " \""+ document.getElementById( "fileTitle" ).value +"\"</h1>";
		confirmtxt+= "<div class='Container'>";
		confirmtxt+= "	<h2 class='question'>" + i18n ( 'i18n_sure_delete_file_question' ) + "</h2>";
		confirmtxt+= ' <div class="SpacerSmall"></div><div class="SpacerSmall"></div>';
		confirmtxt+= '	<button onclick="doDeleteLibraryFile(' + fileID  + ');"><img src="admin/gfx/icons/page_delete.png" /> ' + i18n ( 'i18n_delete' ) + '</button>';
		confirmtxt+= '	<button onclick="abortDeleteLibraryImage();"><img src="admin/gfx/icons/cancel.png" /> ' + i18n ( 'i18n_cancel' ) + '</button>';
		confirmtxt+= "</div>";
		
		iecHTML = document.getElementById( "FileEditContainer" ).innerHTML;
		document.getElementById( "FileEditContainer" ).innerHTML = confirmtxt;
		
	}
	else if ( confirm ( i18n ( 'i18n_sure_delete_file_question' ) ) )
	{
		doDeleteLibraryImage( fileID );
	} 
	

} // end of deleteLibraryImage

function doDeleteLibraryFile( fileID )
{
	var d = new bajax ( );
	d.openUrl ( modulepath + '&action=deletefile&fileID=' + fileID, 'GET', true );
	d.onload = function ( )
	{
		removeModalDialogue ( 'EditLevel' );

		showLibraryContent();
		checkLibraryTooltips();
	
	}
	d.send ( );
} // end of doDeleteLibraryImage

/** ===========================================================================================================
	rediger et bilde
*/
var txdoctitle = document.title;
function editLibraryFile( fileID )
{
	document.ModalSelection = false;
	initModalDialogue ( 'EditLevel', 790, 520, modulepath + '&function=editfile&fileID=' + fileID );	
} // end of applyContentActions


/** ===========================================================================================================
	add image
*/
function addLibraryFile()
{
	document.ModalSelection = false;
	initModalDialogue ( 'EditLevel', 540, 390, modulepath + '&function=editfile&folderID=' + currentLibraryLevel );	
} // end of addLibraryFile

/** ===========================================================================================================
	submit image upload
*/
function submitFileUpload()
{
	if( document.getElementById( "fileok" ).value == 1 )
	{
		document.uploadForm.submit();
	}
	else if( document.getElementById( "fileID" ) )
	{
		document.uploadForm.submit();
	}
	else
	{
		var emsg = i18n ( 'i18n_no_valid_file_selection' );
		if( document.getElementById( "uploadInfoBox" ) ) document.getElementById( "uploadInfoBox" ).innerHTML = '<div class="SpacerSmall"></div><p class="error">'+ emsg +'</p>';
		else alert( emsg );
	}
} // end of submitFileUpload

/** ===========================================================================================================
	check file upload
*/	
function checkFileUpload ()
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
			if( document.getElementById( "uploadInfoBox" ) ) document.getElementById( "uploadInfoBox" ).innerHTML = '';
			document.getElementById( "fileok" ).value = 1;
			// submit here....

			var emsg = i18n ( 'i18n_longer_fileselection_error' );
			if( document.getElementById( "uploadInfoBox" ) ) document.getElementById( "uploadInfoBox" ).innerHTML = '<div class="SpacerSmall"></div><p><b>'+ emsg +'</b></p>';
			else alert( emsg );

		}
		else
		{
			document.getElementById( "fileok" ).value = 1;
			document.getElementById( "uploadInfoBox" ).innerHTML = '';
		}
		
	}
	else
	{
		alert ( i18n ( 'i18n_choose_file' ) );
		return false;
	}
} // end of checkFileUpload

var txdoctitle;
function texteditFullscreen ( )
{
	txdoctitle = document.title;
	document.title = document.getElementById ( 'file_filename' ).innerHTML;
	document.getElementById ( 'tekstFullscreen' ).innerHTML = '<img src="admin/gfx/icons/arrow_in.png"/> Forminsk vindu';
	document.getElementById ( 'tekstFullscreen' ).onclick = function ( ){ texteditNormal ( ); };
	document.body.style.overflow = 'hidden';
	var ifr = document.getElementById ( 'pageProperties' ).getElementsByTagName ( 'iframe' )[0];
	if ( !ifr ) ifr = document.getElementById ( 'pageProperties' ).getElementsByTagName ( 'textarea' )[0];
	ifr.style.position = 'fixed';
	ifr.style.top = '36px';
	ifr.style.left = '0px';
	ifr.style.width = '100%';
	ifr.style.height = ( getDocumentHeight ( ) - 36 ) + 'px';
	ifr.style.zIndex = '10000';
	var tbr = document.getElementById ( 'TexteditToolbar' );
	tbr.style.position = 'fixed';
	tbr.style.top = '0px';
	tbr.style.left = '0px';
	tbr.style.height = '34px';
	tbr.style.background = '#ddd';
	tbr.style.width = '100%';
	tbr.style.padding = '4px 0 0 4px';
	tbr.style.borderTop = '1px solid #fff';
	tbr.style.borderLeft = '1px solid #fff';
	tbr.style.borderRight = '1px solid #ccc';
	tbr.style.borderBottom = '1px solid #ccc';
}
function texteditNormal ( )
{
	document.title = txdoctitle;
	document.getElementById ( 'tekstFullscreen' ).innerHTML = '<img src="admin/gfx/icons/arrow_out.png"/> ' + i18n ( 'i18n_fullscreen' );
	document.getElementById ( 'tekstFullscreen' ).onclick = function ( ){ texteditFullscreen ( ); };
	document.body.style.overflow = 'auto';
	var ifr = document.getElementById ( 'pageProperties' ).getElementsByTagName ( 'iframe' )[0];
	if ( !ifr ) ifr = document.getElementById ( 'pageProperties' ).getElementsByTagName ( 'textarea' )[0];
	ifr.style.position = 'static';
	ifr.style.top = 'auto';
	ifr.style.left = 'auto';
	ifr.style.width = '100%';
	ifr.style.height = '340px';
	ifr.style.zIndex = 'auto';
	var tbr = document.getElementById ( 'TexteditToolbar' );
	tbr.style.position = 'static';
	tbr.style.top = 'auto';
	tbr.style.left = 'auto';
	tbr.style.height = 'auto';
	tbr.style.background = 'none';
	tbr.style.width = 'auto';
	tbr.style.padding = '0 0 0 0';
}

