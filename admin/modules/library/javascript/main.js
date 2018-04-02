
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

var modulepath = "admin.php?module=library";
var editButtonHTML = '';			// to be used by editLibraryLevel and abortLibraryLevelEdit

var udFileTitle;
var defaultMainCol = '';
var currentHoverFolder = false;
var globalLibraryPos = 0;

function updateLibraryLevelTree ( html )
{
	var tree = document.createElement ( 'ul' );
	tree.id = 'LibraryLevelTree';
	tree.className = 'Collapsable';
	tree.innerHTML = html;
		
	if ( !isIE ) makeCollapsable ( tree );
	if ( ge ( 'LibraryLevelTree' ) )
	{
		document.getElementById ( 'LibraryLevelTree' ).parentNode.replaceChild ( tree, document.getElementById ( 'LibraryLevelTree' ) );
		if ( isIE ) makeCollapsable ( tree );
		
		var as = tree.getElementsByTagName ( 'a' );
		for ( var a = 0; a < as.length; a++ )
		{
			as[a].onDragOver = function ()
			{
				if ( typeof ( this.oldback ) == 'undefined' ) 
				{
					this.oldback = this.style.background;
					this.oldweight = this.style.fontWeight;
				}
				this.style.background = 'white';
				this.style.fontWeight = 'bold';
				this.over = true;
			}
			as[a].onDragOut = function ()
			{
				this.style.background = this.oldback;
				this.style.fontWeight = this.oldweight;
				this.over = false;
			}
			as[a].onDragDrop = function ()
			{
				var id = this.id.split ( 'levelli' ).join ( '' );
				if ( dragger.config.objectType )
				{
					moveItem ( dragger.config.objectType, dragger.config.objectID , 'Folder', id );
					this.onDragOut ();
				}
			}
			dragger.addTarget ( as[a] );
		}
	}
}

/** 
 * execute search
**/
function ModuleLibrarySearch()
{
	if( document.getElementById( 'libSearchKeywords' ) &&  document.getElementById( 'libSearchKeywords' ).value != '' )
	{

		var j = new bajax ( );
		j.openUrl ( modulepath + '&function=search&libSearchKeywords=' + encodeURIComponent( document.getElementById( 'libSearchKeywords' ).value ), 'get', true );
		j.onload = function ( )
		{
			var response = this.getResponseText ( ).split( '<!--SEPERATOR-->' );
			
			// Folders
			if( response[0] && response[0] != '' )
			{
				document.getElementById ( 'searchResults' ).innerHTML = '<div class="Container" style="margin-top: 2px"><h2>' + i18n ( 'i18n_searchresult_folders' ) + '</h2>' + response[0] + '</div>';
			}
			else
			{
				document.getElementById ( 'searchResults' ).innerHTML = '<div class="Container" style="margin-top: 2px"><p class="Info">' + i18n ( 'i18n_no_searchresult_folders' ) + '</p></div>';
			}
			
			// Files
			if( response[1] && response[1] != '' )
			{
				if( defaultMainCol == '' ) defaultMainCol = document.getElementById ( 'libMainCol' ).innerHTML;
				
				var header = i18n ( 'i18n_search_results_contents' );
				ge ( 'Innholdsheader' ).innerHTML = header;
				ge ( 'libMainCol' ).innerHTML = '<div class="Container">' + response[1] + '<div class="SpacerSmall"></div></div>';
				eval( extractScripts( response[1] ).join("\n") );
			}
			else
			{
				var header = i18n ( 'i18n_search_results' );
				ge ( 'Innholdsheader' ).innerHTML = header;
				ge ( 'libMainCol' ).innerHTML = '<div class="Container"><p>' + i18n ( 'i18n_no_searchresult_files' ) + '</p></div>';
			}
			document.getElementById ( 'libNullStillSoek' ).style.position = 'relative';
			document.getElementById ( 'libNullStillSoek' ).style.visibility = 'visible';	
		}
		j.send ( );

	}
} // end of librarySearch

function setLibraryPos ( pos )
{
	if ( pos >= 0 ) globalLibraryPos = pos;
	else globalLibraryPos = 0;
}

function ModuleResetLibrarySearch ( )
{
	document.getElementById( 'libSearchKeywords' ).value = '';
	document.getElementById( 'libNullStillSoek' ).style.visibility = 'hidden';
	document.getElementById( 'libNullStillSoek' ).style.position = 'absolute';
	if ( document.getElementById ( 'searchResults' ) )
		document.getElementById ( 'searchResults' ).innerHTML = '';
	showLibraryContent();
}

/** 
 * move item to another folder
**/
function moveItem( sourceType, sourceID, targetType, targetID )
{
	
	var targetItem = targetType+":"+targetID;
	var sourceItem = sourceType+":"+sourceID;
	var turl = modulepath + "&action=moveitems&target="+targetItem+"&items="+sourceItem;
			
	var movejax = new bajax ( );
	movejax.openUrl ( turl, 'GET', true );
	movejax.onload = function ( )
	{
		showLibraryContent();
		checkLibraryTooltips();
		if ( sourceType == 'Folder' )
		{
			setLibraryLevel ( false );
		}
	}
	movejax.send ( );
	
	

} // end of moveItem

/** 
 * successfully edit / addded image
**/	
function showUploadSuccess()
{
	removeModalDialogue ( 'EditLevel' );
	if ( ge ( 'libSearchKeywords' ).value.length && ge ( 'libNullStillSoek' ).style.visibility == 'visible' )
	{
		ModuleLibrarySearch ();
	}
	else
	{
		showLibraryContent();
	}
	checkLibraryTooltips();
	reloadTags ();
}

/** 
 * error on imagesave
**/
function showUploadError( emsg )
{
	if( document.getElementById( "uploadInfoBox" ) ) document.getElementById( "uploadInfoBox" ).innerHTML = '<div class="SpacerSmall"></div><p class="error">'+ emsg +'</p>';
	else alert( emsg );
}

function reloadTags ()
{
	var t = new bajax ();
	t.openUrl ( 'admin.php?module=library&action=gettags', 'get', true );
	t.onload = function ()
	{
		if ( document.getElementById ( 'TagList' ) )
			document.getElementById ( 'TagList' ).innerHTML = this.getResponseText ();
	}
	t.send ();
}

function getByTag ( t )
{
	document.location = 'admin.php?module=library&tag='+t;
}


/**
 * edit library level => save data
**/
function saveLibraryLevelEdit( lid )
{
	if( !document.getElementById( 'editLevelForm' ) ) { alert ( i18n ( 'i18n_page_still_loading' ) ); return; }
	
	var savejax = new bajax ( );
	savejax.openUrl ( modulepath + '&action=savelevel&lid=' + lid, 'POST', true );
	savejax.onload = function ( )
	{		
		removeModalDialogue ( 'EditLevel' );
		var r = this.getResponseText ( );
		updateLibraryLevelTree ( r );
		eval( extractScripts( r ).join("\n") );
		document.lid = lid;
		showLibraryContent();
		checkLibraryTooltips();
	}
	savejax.addVarsFromForm( 'editLevelForm' );
	savejax.send ( );	
} // end of saveLibraryLevelEdit

/**
 * delete library level => get form to delete it and choose what going to happen with the files.
**/
function deleteLibraryLevel( lid )
{
	document.ModalSelection = false;
	initModalDialogue ( 'EditLevel', 320, 365, modulepath + '&action=deletelevel&step=1&lid=' + lid );	
}

function doDeleteLibraryLevelEdit( lid )
{

	if( !document.getElementById( "deleteLevelForm" ) ) 
	{ 
		removeModalDialogue ( 'EditLevel' ); return;
	}

	var deljax = new bajax ( );
	deljax.openUrl ( modulepath + '&action=deletelevel&step=2&lid=' + lid, 'POST', true );
	deljax.onload = function ( )
	{
		removeModalDialogue ( 'EditLevel' );
		var data = this.getResponseText ( ).split ( '<!--SEPARATE-->' );
		updateLibraryLevelTree ( data[0] );
		document.lid = data[ 1 ];
	
		// redo gui ....		
		showLibraryContent();
		checkLibraryTooltips();
	}
	deljax.addVarsFromForm( 'deleteLevelForm' );
	deljax.send ( );
}

/**
 * edit library level => get form to change edit it
**/
function editLibraryLevel( lid )
{
	initModalDialogue ( 'EditLevel', 730, 590, 'admin.php?module=library&function=editlevel&lid=' + lid, initEditLevel );
} 
function initEditLevel ( )
{
	editor.addControl ( document.getElementById ( 'folderDescription' ) );
}
// end of editLibraryLevel


// Get the library levels
function getLibraryLevelTree ( )
{
	var g = new bajax ( );
	g.openUrl ( 'admin.php?module=library&action=setlevel', 'get', true );
	g.onload = function ( )
	{
		updateLibraryLevelTree ( this.getResponseText ( ) );
	}
	g.send ( );
}

/**
 * close that dialogue
**/
function abortLibraryLevelEdit( lid )
{
	removeModalDialogue ( 'EditLevel' );
} // end of abortLibraryLevelEdit



/** 
 * add new library level under currently chosen one
 * update tree and content
**/
function addLibraryLevel(cid)
{
	initModalDialogue ( 'EditLevel', 730, 590, 'admin.php?module=library&function=editlevel&lid=0&plid='+cid, initEditLevel );
}

/**
 * set library level
 * update tree and content
**/
function setLibraryLevel ( varlev )
{
	var pos = globalLibraryPos;
	if ( !varlev ) { varlev = document.lid; } else pos = 0;
	document.lid = varlev;
	
	var updjax = new bajax ( );
	updjax.openUrl ( modulepath + '&action=setlevel&lid=' + varlev, 'get', true );
	updjax.onload = function ( )
	{		
		var r = this.getResponseText ( );
		eval( extractScripts( this.getResponseText ( ) ).join("\n") );
		updateLibraryLevelTree ( r );
		showLibraryContent( pos );
		checkLibraryTooltips();
		if ( document.getElementById ( 'libraryParent' ) )
			document.getElementById ( 'libraryParent' ).value = varlev;
	}
	currentLibraryLevel = varlev;
	if( document.getElementById( 'newlibrarylevelparentlevel' ) ) 
		document.getElementById( 'newlibrarylevelparentlevel' ).value = varlev;
	updjax.send ( );
} // end of setLibraryLevel

/**
 * Show the content of the current level in the library
**/
function showLibraryContent ( pos )
{
	if( pos < 0 || typeof ( pos ) == 'undefined' ) 
	{
		if ( globalLibraryPos ) pos = globalLibraryPos;
		else pos = 0;
	}
	else 
	{
		globalLibraryPos = pos;
	}
	showContentButtons ( );
	var libjax = new bajax ( );
	libjax.openUrl ( modulepath + '&function=listcontents&pos=' + pos + '&lid=' + document.lid, 'get', true );
	libjax.onload = function ( )
	{
		if ( !ge ( 'LibraryContentDiv' ) )
		{
			var d = ge('libMainCol');
			d.innerHTML = '<div>' +
					'<div class="SpacerSmall"></div>' +
					'<div id="LibraryMessage"></div>' +
					'<div id="LibraryContentDiv">' +
					'</div>';
		}
		var cn = document.createElement ( 'div' );
		cn.id = 'LibraryContentDiv';
		cn.innerHTML = this.getResponseText ( );
		document.getElementById ( 'LibraryContentDiv' ).parentNode.replaceChild ( cn, document.getElementById ( 'LibraryContentDiv' ) );
		eval( extractScripts( this.getResponseText ( ) ).join("\n") );
		initContentDropTarget ( );
		checkLibraryTooltips ( );
		var b = {};
		if ( ge ( 'currentlevel' ) )
		{
			b =  document.getElementById ( 'currentlevel' ).getElementsByTagName ( 'B' );
		}
		if ( b.length )
			document.getElementById ( 'Innholdsheader' ).innerHTML = i18n ( 'i18n_contents_of' ) + ' "' + b[ 0 ].innerHTML + '":';
		else document.getElementById ( 'Innholdsheader' ).innerHTML = i18n ( 'i18n_contents_of_main' ) + ':';
	}
	if( !document.getElementById ( 'LibraryContentDiv' ) )
		document.getElementById ( 'libMainCol' ).innerHTML = defaultMainCol;
	
	libjax.send ( );
	
} // end of showLibraryContent

/** 
 * Check what tooltips are there to be diplayed
**/
function checkLibraryTooltips()
{
		if( ge( 'editliblevel' ) )	
			addToolTip ( i18n ( 'i18n_edit' ), i18n ( 'i18n_edit_msg' ), 'editliblevel' );
		if( ge( 'deleteliblevel' ) )	
			addToolTip ( i18n ( 'i18n_delete' ), i18n ( 'i18n_delete_msg' ), 'deleteliblevel' );	
		if( ge( 'moveliblevel' ) )	
			addToolTip ( i18n ( 'i18n_move' ), i18n ( 'i18n_move_msg' ), 'moveliblevel' );	
		if( ge( 'libleveltoworkbench' ) )	
			addToolTip ( i18n ( 'i18n_addtoworkbench' ), i18n ( 'i18n_addtoworkbench_msg' ), 'libleveltoworkbench' );
		if( ge( 'editliblevel' ) )
			addToolTip ( i18n ( 'i18n_addlevel' ), i18n ( 'i18n_addlevel_desc' ), 'addlevel' );
		if( ge( 'editpermissions' ) )
			addToolTip ( i18n ( 'i18n_permissions' ), i18n ( 'i18n_permissions_desc' ), 'editpermissions' );
} // end of checkLibraryTooltips


/**
 * Show buttons for adding images etc
**/
function showContentButtons ( )
{
	document.lgjax = new bajax ( );
	document.lgjax.openUrl ( 'admin.php?module=library&function=showcontentbuttons&lid=' + document.lid, 'get', true );
	document.lgjax.onload = function ( )
	{
		var r = this.getResponseText ( ) + '';
		r = r.split ( '!!!' );
	
		if ( r[1] )
		{
			var dv1 = document.createElement ( 'span' );
			dv1.id = 'ContentButtonsSmall';
			dv1.innerHTML = r[ 1 ];
			if ( ge ( 'ContentButtonsSmall' ) )
			{
				document.getElementById ( 'ContentButtonsSmall' ).parentNode.replaceChild ( dv1, document.getElementById ( 'ContentButtonsSmall' ) );
			}
		}
		if ( r[0] )
		{
			var dv2 = document.createElement ( 'div' );
			dv2.id = 'ContentButtons';
			dv2.innerHTML = r[ 0 ];
			if ( ge ( 'ContentButtons' ) )
			{
				document.getElementById ( 'ContentButtons' ).parentNode.replaceChild ( dv2, document.getElementById ( 'ContentButtons' ) );
			}
		}
		else
		{
			if ( ge ( 'ContentButtons' ) && ge ( 'ContentButtonsSmall' ) )
			{
				document.getElementById ( 'ContentButtons' ).innerHTML = '';
				document.getElementById ( 'ContentButtonsSmall' ).innerHTML = '';
			}
		}
		
		document.lgjax = 0;
	}
	document.lgjax.send ( );
	initContentDropTarget ();
}

/**
 * Drop target for moving files and images
**/
function initContentDropTarget ( )
{
	if ( ge ( 'LibraryContentDiv' ) )
	{
		var lcd = ge ( 'LibraryContentDiv' ).parentNode;
		dragger.removeTarget ( lcd );
		lcd.onDragDrop = function ( element )
		{
			if ( 
				typeof ( dragger.config.objectType ) != "undefined" && 
				typeof ( dragger.config.objectID ) != "undefined"
			)
			{
				if ( 
					( dragger.config.objectType == "Image"	) || ( dragger.config.objectType == "File" ) || ( dragger.config.objectType == "Folder" )
				)
				{
					moveItem( 
						dragger.config.objectType, dragger.config.objectID, 
						'Folder', currentLibraryLevel // currentLibraryLevel is global variable
					);
				}
				else
				{
					document.getElementById( 'LibraryMessage' ).innerHTML = "<p class='error'>" + i18n ( 'move_error_msg1' ) + "</p>";
				}
			}
			this.style.border = this.oldstyle;
		}
		lcd.onDragOver = function ()
		{
			this.oldstyle = this.style.border;
			this.style.border = '1px solid #00aa00';
		}
		lcd.onDragOut = function ()
		{
			this.style.border = this.oldstyle;
		}
		dragger.addTarget( lcd );
	}
}

addEvent ( 'onkeydown', function ( e )
{
	e = e ? e : ( window.Event ? window.Event : window.event );
	document.libraryKey = e.keyCode ? e.keyCode : e.which;
} );
addEvent ( 'onkeyup', function ( e )
{
	document.libraryKey = '';
} );

function toggleSelectedImage ( ele )
{
	// With ctrl key down or shift key
	if ( document.libraryKey == 17 || document.libraryKey == 16 )
	{
		if ( hasClass ( ele, 'Selected' ) )
		{ 
			var classes = ele.className.split ( ' ' );
			var out = new Array ( );
			for ( var a = 0; a < classes.length; a++ )
			{
				if ( classes[ a ] != 'Selected' )
					out.push ( classes[ a ] );
			}
			ele.className = out.join ( ' ' );
		}
		else
		{
			ele.className += ' Selected';
		}
	}
	// Without keys down
	else
	{
		// Remove selected from others
		if ( !hasClass ( ele, 'Selected' ) )
		{	
			ele.className += ' Selected';
		}
		var elesr = Array ( getElementsByClassName ( 'Imagecontainer' ), getElementsByClassName ( 'Listedcontainer' ) );
		for ( var zz = 0; zz < elesr.length; zz++ )
		{
			var eles = elesr[ zz ];
			for ( var a = 0; a < eles.length; a++ )
			{
				if ( eles[ a ] != ele )
				{
					if ( hasClass ( eles[ a ], 'Selected' ) )
					{
						var classes = eles[ a ].className.split ( ' ' );
						var out = new Array ( );
						for ( var b = 0; b < classes.length; b++ )
						{
							if ( classes[ b ] != 'Selected' )
								out.push ( classes[ b ] );
						}
						eles[ a ].className = out.join ( ' ' );
					} 
				}
			}
		}
	}
}

function createLibraryFile ( complete )
{
	if ( !complete ) 
	{
		complete = false;
		initModalDialogue ( 'newfile', 540, 400, 'admin.php?module=library&action=create_library_file' );
	}
	// Create it
	else
	{
		if ( document.getElementById ( 'nfFilename' ).value.length < 1 )
		{
			alert ( 'Du må skrive inn et filnavn!' );
			document.getElementById ( 'nfFilename' ).focus ( );
			return false;
		}
		var jax = new bajax ( );
		jax.openUrl ( 'admin.php?module=library&action=create_library_file', 'post', true );
		jax.addVar ( 'Filename', document.getElementById ( 'nfFilename' ).value );
		jax.addVar ( 'Filetype', document.getElementById ( 'nfFiletype' ).value );
		jax.addVar ( 'Content', document.getElementById ( 'nfContent' ).value );
		jax.onload = function ( )
		{
			if ( this.getResponseText ( ) == 'ok' )
			{
				removeModalDialogue ( 'newfile' );
				showLibraryContent ( );
			}
			else alert ( 'unknown response! ' + this.getResponseText ( )  );
		}
		jax.send ( );
	}
}

function saveFileContents ( fid )
{
	var jax = new bajax ( );
	jax.openUrl ( 'admin.php?module=library&action=savefilecontents&fid=' + fid, 'post', true );
	if ( document.getElementById ( 'advfileContents_cp' ) )
	{
		var html = document.getElementById ( 'pageProperties' ).getElementsByTagName ( 'iframe' )[0].getCode ( );
		jax.addVar ( 'contents', html );
	}
	else jax.addVar ( 'contents', document.getElementById ( 'advfileContents' ).value );
	jax.onload = function ( )
	{
		if ( this.getResponseText ( ) == 'ok' )
		{
			if ( ge ( 'advfileContents' ) )
			{
				
				var afc = ge ( 'advfileContents' );
				afc.oldStyle = afc.style.background;
				afc.style.background = '#00aa02';
				setTimeout ( "ge ( 'advfileContents' ).style.background = ge ( 'advfileContents' ).oldStyle", 250 );
			}
		}
		// Failed to save! show red!
		else 
		{
			var afc = ge ( 'advfileContents' );
			afc.oldStyle = afc.style.background;
			afc.style.background = '#ff0020';
			setTimeout ( "ge ( 'advfileContents' ).style.background = ge ( 'advfileContents' ).oldStyle", 250 );
		}
	}
	jax.send ( );
}

function deleteSelected ( )
{
	var elesr = Array ( getElementsByClassName ( 'Imagecontainer' ), getElementsByClassName ( 'Listedcontainer' ) );
	var ids = new Array ( );
	for ( var zz = 0; zz < elesr.length; zz++ )
	{
		var eles = elesr[ zz ];
		for ( var a = 0; a < eles.length; a++ )
		{
			if ( hasClass ( eles[ a ], 'Selected' ) )
			{
				if ( eles[ a ].id.substr ( 0, 5 ) == 'image' )
				{
					ids.push ( 'image_' + eles[ a ].id.split ( 'imagecontainer' ).join ( '' ) );
				}
				else
				{
					ids.push ( 'file_' + eles[ a ].id.split ( 'tfilecontainer' ).join ( '' ) );
				}
			}
		}
	}
	if ( ids.length > 0 )
	{
		if ( confirm ( i18n ( 'i18n_are_you_sure' ) ) )
		{
			var jax = new bajax ( );
			jax.openUrl ( 'admin.php?module=library&action=deletebyids&ids=' + ids.join ( ',' ), 'get', true );
			jax.onload = function ( )
			{
				showLibraryContent ( );
			}
			jax.send ( );
		}
	}
}

function duplicateSelected ( )
{
	var elesr = Array ( getElementsByClassName ( 'Imagecontainer' ), getElementsByClassName ( 'Listedcontainer' ) );
	var ids = new Array ( );
	for ( var zz = 0; zz < elesr.length; zz++ )
	{
		var eles = elesr[ zz ];
		for ( var a = 0; a < eles.length; a++ )
		{
			if ( hasClass ( eles[ a ], 'Selected' ) )
			{
				if ( eles[ a ].id.substr ( 0, 5 ) == 'image' )
				{
					ids.push ( 'image_' + eles[ a ].id.split ( 'imagecontainer' ).join ( '' ) );
				}
				else
				{
					ids.push ( 'file_' + eles[ a ].id.split ( 'tfilecontainer' ).join ( '' ) );
				}
			}
		}
	}
	if ( ids.length > 0 )
	{
		if ( confirm ( i18n ( 'i18n_are_you_sure' ) ) )
		{
			var jax = new bajax ( );
			jax.openUrl ( 'admin.php?module=library&action=duplicatebyids&ids=' + ids.join ( ',' ), 'get', true );
			jax.onload = function ( )
			{
				showLibraryContent ( );
			}
			jax.send ( );
		}
	}
}
	
function editLevelPermissions ( cid )
{
	initModalDialogue ( 'permissions', 640, 570, 'admin.php?module=library&function=levelpermissions&cid=' + cid );
}

function cleanCache ( )
{
	if ( confirm ( i18n( 'Are you sure you want to clear the image cache?' ) ) )
	{
		var jax = new bajax ( );
		jax.openUrl ( 'admin.php?module=library&action=clearcache', 'get', true );
		jax.onload = function ( )
		{
			if ( this.getResponseText ( ) == 'ok' )
			{
				alert ( i18n ( 'All done!' ) );
				document.location = 'admin.php?module=library';
			}
		}
		jax.send ( );
	}
}

/** Multiple file upload etc **************************************************/

function mulDecreaseImages ( )
{
	var trs = document.getElementById ( 'MultipleFilesTable' ).getElementsByTagName ( 'tr' );
	if ( trs.length > 1 )
		trs[ trs.length - 1 ].parentNode.removeChild ( trs[ trs.length - 1 ] );
}
function mulIncreaseImages ( )
{
	var tr = document.createElement ( 'tr' );
	var index = document.getElementById ( 'MultipleFilesTable' ).getElementsByTagName ( 'tr' ).length;
	while ( document.uploadForm[ 'file_' + index ] )
		index++;
	var td = document.createElement ( 'td' );
	td.innerHTML = '' +
		i18n ( 'i18n_imagetitle' ) + ' ' + ( index + 1 ) + ': ' +
		'<input type="text" size="20" name="filename_' + index + '"/>';
	var td2 = document.createElement ( 'td' );
	td2.innerHTML = '' +
		i18n ( 'i18n_imagefile' ) + ' ' + ( index + 1 ) + ':' +
		'<input type="file" name="image_' + index + '"/>';
	tr.className = 'sw'+(index % 2 + 1);
	tr.appendChild ( td ); tr.appendChild ( td2 );
	var par = document.getElementById ( 'MultipleFilesTable' );
	if ( par.firstChild.nodeName.toLowerCase ( ) == 'tbody' )
		par.firstChild.appendChild ( tr );
	else par.appendChild ( tr );
}
function mulDecreaseFiles ( )
{
	var trs = document.getElementById ( 'MultipleFilesTable' ).getElementsByTagName ( 'tr' );
	if ( trs.length > 1 )
		trs[ trs.length - 1 ].parentNode.removeChild ( trs[ trs.length - 1 ] );
}
function mulIncreaseFiles ( )
{
	var tr = document.createElement ( 'tr' );
	var index = document.getElementById ( 'MultipleFilesTable' ).getElementsByTagName ( 'tr' ).length;
	while ( document.uploadForm[ 'file_' + index ] )
		index++;
	var td = document.createElement ( 'td' );
	td.innerHTML = '' +
		'Filtittel ' + ( index + 1 ) + ': ' +
		'<input type="text" size="20" name="filename_' + index + '"/>';
	var td2 = document.createElement ( 'td' );
	td2.innerHTML = '' +
		'Fil ' + ( index + 1 ) + ': ' +
		'<input type="file" name="file_' + index + '"/>';
	tr.className = 'sw'+(index % 2 + 1);
	tr.appendChild ( td ); tr.appendChild ( td2 );
	var par = document.getElementById ( 'MultipleFilesTable' );
	if ( par.firstChild.nodeName.toLowerCase ( ) == 'tbody' )
		par.firstChild.appendChild ( tr );
	else par.appendChild ( tr );
}

function submitSwf ( )
{
	var form = document.getElementById ( 'uploadForm' );
	var eles = document.getElementById ( 'SwfProperties' ).getElementsByTagName ( 'input' );
	if ( eles )
	{
		for ( var a = 0; a < eles.length; a++ )
		{
			var clone = document.createElement ( 'input' );
			clone.name = eles[ a ].name;
			clone.value = eles[ a ].value;
			clone.type = 'hidden';
			form.appendChild ( clone );
		}
	}
	submitFileUpload ( );
}

function setSortOrder ( fid, typ, order )
{
	document.location = 'admin.php?module=library&action=setsortorder&t='+typ+'&i='+fid+'&o='+order;
}

