

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



// we can be called from library module (and potentially other ones too ;)
var callModule = false;

var pluginLibraryOptions = { scalemode : '&scalemode=proximity', background : '0x000000' };

/**
 * Add a level to the library under the current one
**/
function pluginAddLevel ( )
{
	if ( document.getElementById ( 'libraryNewLevel' ).value.length <= 0 )
		return false;
	document.levjax = new bajax ( );
	document.levjax.openUrl ( 'admin.php?plugin=library&pluginaction=newlevel&name=' + document.getElementById ( 'libraryNewLevel' ).value, 'get', true );
	document.levjax.onload = function ( )
	{
		document.getElementById ( 'libraryNewLevel' ).value = '';
		pluginLibraryShowContent ( );
		pluginLibraryUpdateLevels ( );
		pluginSetLibraryLevel ( this.getResponseText ( ) );
		document.levjax = 0;
	}
	document.levjax.send ( );
}

/**
 * Show the content of the current level in the library
**/
function pluginLibraryShowContent ( )
{
	if ( document.getElementById ( 'LibraryContent' ) )
	{
		if ( document.getElementById ( 'DiaLibraryImages' ) )
			_librarydialogImages ( );
		
		var mode = '';
		// Check if we're having an on-top modaldialogue instance
		if ( document.getElementById ( 'UploadTabsmodaldialogue' ) ) mode = '&mode=modaldialogue';

		var libjax = new bajax ( );
		libjax.openUrl ( 'admin.php?plugin=library&pluginaction=showcontents' + mode, 'get', true );
		libjax.onload = function ( )
		{
			// We need oldcon!!
			var oldcon;
			if ( !( oldcon = document.getElementById ( 'LibraryContent' ) ) )
				return;
			if ( !oldcon.nodeName && !oldcon.nodeName != 'div' ) 
				return;
				
			var con = document.createElement ( 'div' );
			con.className = 'SubContainer';
			con.innerHTML = this.getResponseText ( );
			con.id = 'LibraryContent';
	
			oldcon.parentNode.replaceChild ( con, oldcon );
			checkLibraryToolTips();
			_libraryShowNewLevelGUI ( );
		}
		libjax.send ( );
	}
	else if ( document.getElementById ( 'DiaLibraryImages' ) )
	{
		_librarydialogImages ( );
	}
	else
	{
		setTimeout ( 'pluginLibraryShowContent ( )', 100 );
	}
}

/**
 * Update all level select lists with options
**/
function pluginLibraryUpdateLevels ( )
{
	document.updjax = new bajax ( );
	document.updjax.openUrl ( 'admin.php?plugin=library&pluginaction=getlevels', 'get', true );
	document.updjax.onload = function ( )
	{	
		pluginLibrarySetupSelects ( this.getResponseText ( ) );		
		document.updjax = 0;
	}
	document.updjax.send ( );
}

function pluginLibrarySetupSelects ( vartex )
{
	if ( !vartex ) return false;
	else vartex = vartex + '';
	
	var fileLevels = document.getElementById ( 'FileLevels' );
	var imageLevels = document.getElementById ( 'ImageLevels' );
	
	if ( fileLevels && fileLevels.parentNode )
	{
		var tdata = vartex.split ( "!ID!" ).join ( "FileLevels" );	
		var span = document.createElement ( 'span' );
		span.innerHTML = tdata;
		fileLevels.parentNode.parentNode.replaceChild ( span, fileLevels.parentNode );
		fileLevels = document.getElementById ( 'FileLevels' );
		fileLevels.onchange = function ( ){ pluginSetLibraryLevel ( this.value ); };
		fileLevels.name = "Level";
	}
	if ( imageLevels && imageLevels.parentNode )
	{
		var tdata = vartex.split ( "!ID!" ).join ( "ImageLevels" );	
		var span = document.createElement ( 'span' );
		span.innerHTML = tdata;
		imageLevels.parentNode.parentNode.replaceChild ( span, imageLevels.parentNode );
		imageLevels = document.getElementById ( 'ImageLevels' );
		imageLevels.onchange = function ( ){ pluginSetLibraryLevel ( this.value ); };
		imageLevels.name = "Level";
	}
}

function pluginSetLibraryLevel ( varlev )
{
	var updjax = new bajax ( );
	updjax.openUrl ( 'admin.php?plugin=library&pluginaction=setlevel&lid=' + varlev, 'get', true );
	updjax.onload = function ( )
	{	
		if ( document.getElementById ( 'ContentLevelTree' ) )
		{
			var r = this.getResponseText ( ) + '';
			var parts = r.split('<!--SEP-->');
			pluginLibrarySetupSelects ( parts[0] );
			var ul = document.createElement ( 'ul' );
			ul.className = 'Collapsable';
			ul.id = 'ContentLevelTree';
			ul.innerHTML = parts[ 1 ];
			makeCollapsable ( ul );
			var oldUl = document.getElementById ( 'ContentLevelTree' );
			oldUl.parentNode.replaceChild ( ul, oldUl );
			pluginLibraryShowContent ( );
			checkLibraryToolTips();
		}
		if ( document.getElementById ( 'FlashFiles' ) )
			setupLibraryFlashDialog ( );
		if ( document.getElementById ( 'ContentFolderSelect' ) )
		{
			pluginLibraryShowContent ( );
			checkLibraryToolTips ( );
		}
	}
	updjax.send ( );
}

function showModalPreviewImage ( iid, iwidth, iheight )
{
	// Cache for showModalPreviewImageAdjusted
	pluginLibraryOptions.background = ge('ImageBackground').value;
	pluginLibraryOptions.height = iheight;
	pluginLibraryOptions.width = iwidth;
	pluginLibraryOptions.id = iid;

	var scalemode, effects;
	if ( pluginLibraryOptions.scalemode )
		scalemode = pluginLibraryOptions.scalemode;
	if ( pluginLibraryOptions.effects )
		effects = pluginLibraryOptions.effects;
	if ( !pluginLibraryOptions.background )
		pluginLibraryOptions.background = '#000000';
	var bg = pluginLibraryOptions.background.split ( '#' ).join ( '' );
	
	if ( !scalemode ) scalemode = '';
	else
	{
		var sm = scalemode.split ( '=' ); sm = sm[ 1 ];
		var opts = document.getElementById ( 'ImageScaleMode' ).options;
		for ( var a = 0; a < opts.length; a++ )
			if ( opts[ a ].value == sm ){ opts.selectedIndex = a; break; }
	}
	if ( !effects ) effects = '';
	else effects = '&effects=' + effects;

	var imgjax = new bajax ( );
	imgjax.openUrl (
		'admin.php?plugin=library&pluginaction=getimagepreview' +
		'&width=' + iwidth +
		'&height=' + iheight +
		'&background=' + bg +
		scalemode + effects +
		'&iid=' + iid, 'get', true
	);
	imgjax.onload = function ( )
	{
		if ( !document.getElementById ( 'libraryImage' ) )
			return false;
			
		var html = this.getResponseText ( ) + '';
		document.getElementById ( 'libraryImage' ).innerHTML = html;
		var str = html.split ( "\"" );
		document.getElementById ( 'VisibleImageUrl' ).value = str[ 1 ];
	}
	imgjax.send ( );
}
function setPreviewImageEffect ( )
{
	var effects = new Array ();

	if ( document.getElementById ( 'ColorBrightness' ).val > 0 )
	{
		if ( document.getElementById ( 'ColorBrightness' ).val != 1 )
			effects.push ( 'brightness:' + parseDouble ( ( document.getElementById ( 'ColorBrightness' ).val / 100 ) * 2 ) );
	}
	if ( document.getElementById ( 'ColorGamma' ).val > 0 )
	{
		effects.push ( 'gamma:0.5,' + parseDouble( document.getElementById ( 'ColorGamma' ).val / 100 ) );
	}
	if ( document.getElementById ( 'ColorInvert' ).val > 0 )
	{
		effects.push ( 'invert:' + parseDouble ( document.getElementById ( 'ColorInvert' ).val / 100 ) );
	}
	if ( document.getElementById ( 'ColorUnsharp' ).val > 0 )
    {
        var val = ( document.getElementById ( 'ColorUnsharp' ).val / 100 );
        var amp = parseDouble ( val * 10 );
        var eff = parseDouble ( val * 200 );
		effects.push ( 'unsharp:' + eff + ',' + amp + ',0' );
	}
	if ( document.getElementById ( 'ColorGaussianBlur' ).val > 0 )
	{
		effects.push ( 'gaussianblur:' + parseDouble ( ( document.getElementById ( 'ColorGaussianBlur' ).val / 100 ) * 20 ) );
	}
	if ( document.getElementById ( 'ColorGrayscale' ).val > 0 )
	{
		effects.push ( 'grayscale:' + parseDouble ( document.getElementById ( 'ColorGrayscale' ).val / 100 ) );
	}
	if ( document.getElementById ( 'ColorPower' ).val > 0 )
	{
		var r = Math.floor ( document.getElementById ( 'ColorRed' ).val / 100 * 255 );
		var g = Math.floor ( document.getElementById ( 'ColorGreen' ).val / 100 * 255 );
		var b = Math.floor ( document.getElementById ( 'ColorBlue' ).val / 100 * 255 );
		var p = Math.floor ( document.getElementById ( 'ColorPower' ).val / 100 * 10 ) / 10;
		effects.push ( 'tocolor:' + r + ',' + g + ',' + b + ',' + p );
	}
	pluginLibraryOptions.effects = effects ? effects.join ( ';' ) : '';
	showModalPreviewImage ( pluginLibraryOptions.id, pluginLibraryOptions.width, pluginLibraryOptions.height );
}
function setPreviewImageScalemode ( scalemode )
{
	pluginLibraryOptions.scalemode = scalemode;
	showModalPreviewImage ( pluginLibraryOptions.id, pluginLibraryOptions.width, pluginLibraryOptions.height );
}

function pluginLibraryDeleteImage ( varid )
{
	if ( confirm ( 'Er du sikker p&aring; at du &oslash;nsker &aring; slette bildet?' ) )
	{
		document.deljax = new bajax ( );
		document.deljax.openUrl ( 
			'admin.php?plugin=library&pluginaction=deleteimage&iid=' + varid, 'get', true
		);
		document.deljax.onload = function ( )
		{
			pluginLibraryShowContent ( );
			document.deljax = 0;
		}
		document.deljax.send ( );
		return true;
	}
	return false;
}


function pluginLibrarySaveImage ( varid, module )
{
	if( module ) callModule = module; else callModule = false;
	
	document.sjax = new bajax ( );
	document.sjax.addVar ( 'ID', varid );
	document.sjax.addVar ( 'Title', document.getElementById ( 'imagetitle' ).value );
	document.sjax.addVar ( 'Description', document.getElementById ( 'imagedescription' ).value );
	document.sjax.openUrl ( 'admin.php?plugin=library&pluginaction=saveimage', 'post', true );
	document.sjax.onload = function ( )
	{
		document.getElementById ( 'ImageTitleH1' ).innerHTML = document.getElementById ( 'imagetitle' ).value;
		pluginLibraryShowContent ( );
		document.sjax = 0;
	}
	document.sjax.send ( );
}

// Set the image controls to their normal walues
function resetImageControls ( )
{
	// Disabled
	return;
	var grooveWidth = getElementWidth ( document.getElementById ( 'ColorBrightness' ).parentNode );
	var midVal = Math.round ( grooveWidth / 2 );

	var buttons = document.getElementById ( 'SliderTable' ).getElementsByTagName ( 'button' );
	for ( var a = 0; a < buttons.length; a++ )
	{
		if ( buttons[ a ].id == 'ColorBrightness' )
		{
			buttons[ a ].style.left = ( midVal - Math.round ( getElementWidth ( buttons[ a ] ) / 2 ) ) + 'px';
			buttons[ a ].val = parseDouble ( ( midVal - Math.round ( getElementWidth ( buttons[ a ] ) / 2 ) ) / ( grooveWidth - getElementWidth ( buttons[ a ] ) ) * 100 );
		}
		else
		{
			buttons[ a ].style.left = '0px';
			buttons[ a ].val = 0;
		}
		switch ( buttons[ a ].id )
		{
			case 'GaussianBlur':
				buttons[ a ].multiplier = 20;
				buttons[ a ].round = true;
				break;
			case 'ColorUnsharp':
				buttons[ a ].multiplier = 20;
				buttons[ a ].round = true;
				break;
			case 'ColorGamma':
			case 'ColorInvert':
			case 'ColorGrayscale':
				buttons[ a ].multiplier = 1;
				buttons[ a ].round = false;
				break;
			case 'ColorBrightness':
				buttons[ a ].multiplier = 2;
				buttons[ a ].round = false;
				break;
			case 'ColorPower':
				buttons[ a ].multiplier = 10;
				buttons[ a ].round = true;
				break;
			default:
				buttons[ a ].multiplier = 255;
				buttons[ a ].round = true;
				break;
		}
		var val = buttons[ a ].val / 100 * buttons[ a ].multiplier;
		if ( buttons[ a ].round ) document.getElementById ( buttons[ a ].id + 'Val' ).value = Math.round ( val );
		else document.getElementById ( buttons[ a ].id + 'Val' ).value = parseDouble ( val );
	}
}

/**
 * Sets up different tools for use with the image modal dialogue
**/
function setupImageControls ( )
{
	var container = document.getElementById ( 'libraryImage' );
	if ( !container )
		return false;
	showModalPreviewImage ( document.getElementById ( 'LibraryImageDialogImageID' ).value, 260, 180 );
	GuiColorBox ( ge ( 'ImageBackground' ) );
}

function initPluginLibrary ( vartype, varid )
{
	// Load 
	document.plugjax = new bajax ( );
	document.plugjax.openUrl ( 'admin.php?plugin=library&pluginaction=init&type=' + vartype + '&id=' + varid, 'get', true );
	document.plugjax.onload = function ( )
	{
		var div = document.createElement ( "DIV" );
		div.innerHTML = this.getResponseText ( );
		div.style.padding = '2px';
		
		if ( isIE )
			document.getElementById ( "PluginLibrary" ).replaceNode ( div );
		else
			document.getElementById ( "PluginLibrary" ).parentNode.replaceChild ( div, document.getElementById ( "PluginLibrary" ) );
		
		if ( 
			div.parentNode.className != "Container" &&
			div.parentNode.className != "SubContainer" &&
			div.parentNode.className != "page" &&
			div.parentNode.className != "pageActive"
		)
		{
			div.className = "Container";
		}

		// Check if we're having an on-top modaldialogue instance
		if ( document.getElementById ( 'UploadTabsmodaldialogue' ) )
		{
			initTabSystem ( 'UploadTabsmodaldialogue' );
			activateTab ( 'tabLibraryContentmodaldialogue' );
		}
		else 
		{
			initTabSystem ( 'UploadTabs' );
		}
		pluginLibraryShowContent ( );
		document.plugjax = 0;
	}
	document.plugjax.send ( );
}

function librarySearch ( )
{
	var keys = document.getElementById ( 'LibraryKeywords' ).value;
	if ( !keys || keys.length < 1 ) return false;
	
	document.searchjax = new bajax ( );
	document.searchjax.openUrl ( 'admin.php?plugin=library&pluginaction=search&keys=' + keys, 'get', true );
	document.searchjax.onload = function ( )
	{
		document.getElementById ( 'LibraryContent' ).innerHTML = this.getResponseText ( );
		document.searchjax = 0;
	}
	document.searchjax.send ( );
}

function checkLibraryToolTips()
{
	if( document.getElementById( "editliblevel" ) )
	{
		addToolTip ( 'Rediger', 'Klikk for å redigere biblioteksniv&aring;et.', 'editliblevel' );
	}
	if( document.getElementById( "libleveltoworkbench" ) )
	{
		addToolTip ( 'Til arbeidsbenken', 'Legg ned en lenke p&aring; arbeidsbenken som representerer dette niv&aring;. Lenken kan du koble til p&aring; andre elementer.', 'libleveltoworkbench' );
	}
}

function copyOriginalImageHTML ( varid )
{
	document.imgjax = new bajax ( );
	document.imgjax.openUrl ( 'admin.php?plugin=library&pluginaction=originalimagehtml&iid=' + varid, 'get', true );
	document.imgjax.onload = function ( )
	{
		setCookie ( 'arenaClipBoardImage', this.getResponseText ( ) );
		document.imgjax = 0;
	}
	document.imgjax.send ( );
}

function pasteImage ( original )
{
	if ( !original ) original = false;
	
	// In case we want title / description on image
	if ( document.getElementById ( 'UseImagetitleAsHeader' ).checked || document.getElementById ( 'UseDescriptionAsSubtext' ).checked )
	{
		var html = getCookie ( 'arenaClipBoardImage' );
		
		var imagewidth;
		
		if ( original )
		{
			imagewidth = document.getElementById ( 'imagedimensions' ).innerHTML + '';
			imagewidth = imagewidth.split ( 'x' );
			imagewidth = parseInt ( imagewidth[ 0 ] );
		}
		else
		{
			imagewidth = parseInt ( document.getElementById ( 'libraryImageWidth' ).value );
		}
		
		var out = "<table class=\"ImageWithText\">";
		if ( document.getElementById ( 'UseImagetitleAsHeader' ).checked )
			out += "<tr><td class=\"ImageHeader\">" + document.getElementById ( 'imagetitle' ).value + "</td></tr>";
		out += "<tr><td class=\"Image\">" + html + "</td></tr>";
		if ( document.getElementById ( 'UseDescriptionAsSubtext' ).checked )
			out += '<tr><td class=\"ImageText\">' + str_replace ( '\n', '<br />', document.getElementById ( 'imagedescription' ).value ) + "</td></tr>";
		out += "</table>";
		setCookie ( 'arenaClipBoardImage', out );
	}
	
	// Remove modal dialog
	if ( document.modaldialogues )
	{
		if ( document.modaldialogues.length )
		{
			removeModalDialogue ( document.modaldialogues[ document.modaldialogues.length-1 ].Name );
		}
	}
	// Paste image
	document.editor.insertImage ( getCookie ( 'arenaClipBoardImage' ) );
}

/**
	For, and only for the library dialog associated with text fields
**/
function setupLibraryDialog ( )
{
	/*_librarydialogFolders ( );*/
	_librarydialogImages ( );
}

var __pl_lib_bor, __pl_lib_bg;
function _librarydialogImages ( )
{
	var __libdiawaiter = new bajax ( );
	__libdiawaiter.openUrl ( 'admin.php?plugin=library&pluginaction=getdialogimages', 'get', true );
	__libdiawaiter.onload = function ( )
	{		
		document.getElementById ( 'DiaLibraryImages' ).innerHTML = this.getResponseText ( );
		var imgs = document.getElementById ( 'DiaLibraryImages' ).getElementsByTagName ( 'div' );
		for ( var a = 0; a < imgs.length; a++ )
		{
			imgs[ a ].onmouseover = function ( )
			{
				__pl_lib_bg = this.style.backgroundColor;
				__pl_lib_bor = this.style.border;
				this.style.border = '1px solid #5A7A93';
				this.style.color = '#fff';
				this.style.backgroundColor = '#5E8EB4';
			}
			imgs[ a ].onmouseout = function ( )
			{
				this.style.border = __pl_lib_bor;
				this.style.color = '#000000';
				this.style.backgroundColor = __pl_lib_bg;
			}
		}
	}
	__libdiawaiter.send ( );
}
/*
	Done with the library dialog for text fields
*/

// Setup flash dialog
function setupLibraryFlashDialog ( )
{
	var dj = new bajax ( );
	dj.openUrl ( 'admin.php?plugin=library&pluginaction=getflashfiles', 'get', true );
	dj.onload = function ( )
	{
		document.getElementById ( 'FlashFiles' ).innerHTML = this.getResponseText ( );
	}
	dj.send ( );
}

function insertFlashMovie ( id )
{
	var moj = new bajax ( );
	moj.openUrl ( 'admin.php?plugin=library&pluginaction=getflashhtml&fid=' + id, 'get', true );
	moj.onload = function ( )
	{
		removeModalDialogue ( 'library' );
		var ed = texteditor.get ( texteditor.activeEditorId );
		ed.iframe.focus ( );
		ed.insertHTML ( this.getResponseText ( ) );
	}
	moj.send ( );
}


/**
 * Show gui for making new sublevels under current level
**/
function _libraryShowNewLevelGUI ( )
{
	var ijax = new bajax ( );
	ijax.openUrl ( 'admin.php?plugin=library&pluginaction=getnewlevelgui', 'get', true );
	ijax.onload = function ( )
	{
		if ( document.getElementById ( 'libraryNewLevelBox' ) )
			document.getElementById ( 'libraryNewLevelBox' ).innerHTML = this.getResponseText ( );
	}
	ijax.send ( );
}

function setSelectImageSize ( size )
{
	var w = 0, h = 0;
	if ( size.indexOf ( '%' ) >= 0 )
	{
		var p = size.replace ( '%', '' );
		p = parseInt ( p );
		p = p / 100.0;
		var dim = document.getElementById ( 'imagedimensions' ).innerHTML.split ( 'x' );
		w = Math.floor ( parseInt ( dim[0] ) * p );
		h = Math.floor ( parseInt ( dim[1] ) * p );
	}
	else if ( size == 'original' )
	{
		var dim = document.getElementById ( 'imagedimensions' ).innerHTML.split ( 'x' );
		w = dim[0];
		h = dim[1];
	}
	else 
	{
		var p = size.split ( ' ' ).join ( ' ' );
		p = size.split ( 'x' );			
		w = parseInt ( p[0] );
		h = parseInt ( p[1] );
	}
	document.getElementById ( 'libraryImageWidth' ).value = w; 
	document.getElementById ( 'libraryImageHeight' ).value = h;
	
	// Refresh preview image
	pluginLibraryOptions.width = w;
	pluginLibraryOptions.height = h;
	showModalPreviewImage ( pluginLibraryOptions.id, pluginLibraryOptions.width, pluginLibraryOptions.height );
}


