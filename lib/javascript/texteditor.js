

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



/*******************************************************************************
*                                                                              *
* ARENA2 Texteditor v1.0                                                       *
* Written by Hogne Titlestad                                                   *
* (c) 2005 - 2010 Blest AS                                                     *
* (c) 2011 - 2014 Idéverket AS                                                 *
*                                                                              *
*******************************************************************************/

var ieMode = navigator.userAgent.indexOf ( 'MSIE 6' ) >= 0 || navigator.userAgent.indexOf ( 'MSIE 7' ) >= 0;

function cimatch ( str1, str2 )
{
	if ( str1.toLowerCase ( ) == str2.toLowerCase ( ) )
		return true;
	return false;
}

function initTexteditorAddons ( )
{
	if ( document.body )
	{
		if ( typeof ( CodePress ) == 'undefined' )
			include ( 'lib/3rdparty/codepress/codepress.js' );
		// Only do it when not in library
		if ( document.body.className != 'library' )
		{
			if ( typeof ( setupLibraryDialog ) == 'undefined' )
				include ( "lib/plugins/library/javascript/plugin.js" );
		}
	}
	else setTimeout ( "initTexteditorAddons ( );", 50 );
}
initTexteditorAddons ( );

var texteditor = new Object ( );
texteditor.editors = new Array ( );
texteditor.storedSelection = false;

texteditor.init = function ( obj )
{
	if ( obj && obj.nodeName && obj.nodeName.toLowerCase ( ) == 'textarea' )
	{
		return this.addControl ( obj );
	}
	// Defaults
	this._lastClickedItem = false; // The last clicked dom node
	this._lastCursorItem = false; // The dom node the cursor is in
	this.config = new Object ( );
	this.config.height = '400px';
	this.clickX = 0;
	this.clickY = 0;	
	if ( !this.customCssClasses )
		this.customCssClasses = new Array ( );

	// Copy parameters to config
	for ( var a in obj )
		this.config[ a ] = obj[ a ];
	
	// Copy config from document.editor
	if ( document.editor && document.editor.options )
	{
		for ( var a in document.editor.options )
		{
			this.config[ a ] = document.editor.options[ a ];
		}
	}
	
	// Take only by classnames
	if ( this.config.classNames )
	{
		var ids = this.config.classNames.split ( ',' );
		for ( var a = 0; a < ids.length; a++ )
		{
			var id = ids[ a ].split ( ' ' ).join ( '' );
			var textareas = document.body.getElementsByTagName ( 'textarea' );
			for ( var b = 0; b < textareas.length; b++ )
				if ( hasClass ( textareas[ b ], id ) )
					this.addControl ( textareas[ b ] );
		}
	}
	// Take all
	else
	{
		var textareas = document.body.getElementsByTagName ( 'textarea' );
		for ( var b = 0; b < textareas.length; b++ )
			this.addControl ( textareas[ b ] );
	}
	return false;
}
texteditor.setStylesheet = function ( cssfilename )
{
	this.stylesheetSrc = cssfilename;
}
texteditor.setCustomStyles = function ( classes )
{
	if ( classes.length )
		this.customCssClasses = classes;
}
texteditor.addControl = function ( area )
{
	if ( typeof ( area ) == 'string' )
		area = document.getElementById ( area );
	if ( !area ) return false;
	
	// Clean HTML for dangerous entries
	if ( !ieMode )
	{
		var cleanex = /[n|N][a|A][m|M][e|E][\s]*?=[\s]*?[\"]{0,1}[\_]*?action[\"]{0,1}/;
		area.value = area.value.split ( cleanex ).join ( 'name="_illegal_field_name_"' );
		cleanex = /[n|N][a|A][m|M][e|E][\s]*?=[\s]*?[\"]{0,1}[\_]*?module[\"]{0,1}/;
		area.value = area.value.split ( cleanex ).join ( 'name="_illegal_field_name_"' );
	}
	
	// Don't do it double
	for ( var a = 0; a < this.editors.length; a++ )
	{
		if ( this.editors[ a ].area.id == area.id )
			return false;
	}

	if ( typeof ( area ) == 'string' )
		area = document.getElementById ( area );

	// Create our object
	var obj = new Object ( );
	obj.undoBuffer = new Array ( );
	obj.area = area;
	obj.stylesheetSrc = this.stylesheetSrc;
	obj.index = this.editors.length;
	obj.texteditor = this;
	this.editors[ obj.index ] = obj;
	obj.editors = this.editors;
	obj.localRangeObject = false;
	
	// Create our iframe container
	obj.container = document.createElement ( 'div' );
	obj.container.className = 'ArenaEditorContainer';

	// Create our iframe
	obj.iframe = document.createElement ( 'iframe' );
	if ( isIE ) 
	{
		obj.iframe.setAttribute ( 'frameborder', 'no' );
		obj.iframe.setAttribute ( 'framespacing', '0' );
		obj.iframe.style.marginBottom = '1px';
	}
	obj.iframe.setAttribute ( 'scrolling', 'no' );
	obj.iframe.style.height = this.config.height;
	obj.iframe.className = 'ArenaEditor';
	obj.iframeHeight = 0;	
	
	if ( !document.scrollEvent )
	{
		fadeMetabuttons = function ( dir )
		{
			if ( !ge ( 'MetaButtons' ) )
				return;
			if ( !document.metabobj )
				document.metabobj = { span: 0, y: 0, dir: false, intr: 0, tm: 0 };
			var metabobj = document.metabobj;
			if ( dir )
			{
				if ( dir != metabobj.dir )
				{
					metabobj.dir = dir;
					metabobj.tm = ( new Date () ).getTime();
					metabobj.intr = setInterval ( 'fadeMetabuttons()', 5 );
					metabobj.cy = metabobj.y;
					if ( dir == 'up' )
						metabobj.span = metabobj.y == 0 ? -40 : ( -40 + (-metabobj.y) );
					else metabobj.span = metabobj.y <= 0 ? ( -metabobj.y ) : ( metabobj.y );
				}
				return;
			}
			else
			{
				var phase = ( ( new Date () ).getTime() - metabobj.tm ) / 300;
				if ( phase >= 1 )
				{
					clearInterval ( metabobj.intr );
					metabobj.dir = '';
					phase = 1;
				}
				var ph = Math.pow ( phase, 2 );
				metabobj.y = metabobj.cy + ( ( metabobj.span ) * ph );
				ge ( 'MetaButtons' ).style.top = Math.floor ( metabobj.y ) + 'px';
			}
		}
		scrollFunc = function ()
		{
			var metab = ge ( 'MetaButtons' );
			for ( var e = 0; e < texteditor.editors.length; e++ )
			{
				var eobj = texteditor.editors[e];
				var st = getScrollTop ();
				if ( getElementHeight ( eobj.iframe ) != eobj.iframeHeight )
				{
					eobj.toolbar.originalTop = getElementTop ( eobj.toolbar ) - ( eobj.offsety ? eobj.offsety : 0 );
					eobj.iframeHeight = getElementHeight ( eobj.iframe );
				}
				var tt = eobj.toolbar.originalTop + 2;
				if ( st > tt )
				{
					var off = st - tt;
					var th = getElementHeight ( eobj.toolbar );
				
					var testT = off + tt + th;
					var testI = ( getElementTop ( eobj.iframe ) + getElementHeight ( eobj.iframe ) );
					if ( testT >= testI )
						off = testI - ( tt + th );
					eobj.toolbar.style.position = 'relative';
					eobj.toolbar.style.top = off + 'px';
					eobj.offsety = off;
					if ( eobj.index == 0 )
						fadeMetabuttons ( 'up' );
				}
				else 
				{
					eobj.toolbar.style.top = '0px';
					if ( eobj.index == 0 )
						fadeMetabuttons ( 'down' );
				}
			}
		}
		/*
		FIXME: Gets too crouded!
		if ( isIE )
		{
			window.onscroll = scrollFunc;
		}
		else document.scrollEvent = addEvent ( 'onscroll', scrollFunc );*/
	}
	
	if ( area.style.height ) obj.iframe.style.height = area.style.height;
	if ( area.style.width ) obj.iframe.style.width = area.style.width;
	
	// Create our toolbar
	obj.toolbar = document.createElement ( 'div' );
	obj.toolbar.className = 'ArenaEditorToolbar';
	obj.GenerateToolbar = function ( menu )
	{
		this.toolbarButtons = new Array ( );
		var a = 0, html = '<table><tr>';
		for ( ; a < menu.length; a++ )
		{
			// Only some buttons is used for the undo buffer
			var rem = 'texteditor.get ( \'' + this.area.id + '\' ).rememberContent ( ); ';
			switch ( menu[ a ].action )
			{
				case 'undo ( )':
				case 'redo ( )':
					rem = '';				
					break;
			}
			switch ( menu[ a ].type )
			{
				case 'button':
					html += '<td class="' + menu[ a ].classname + '">';
					html += '<a href="javascript: void(0)" onclick="' + rem + 'texteditor.get ( ' + ( this.area.id ? ( '\'' + this.area.id + '\'' ) : false ) + ' ).' + menu[ a ].action + ';">';
					html += '<img title="' + menu[ a ].title + '" src="admin/gfx/icons/' + menu[ a ].icon + '"/></a></td>';
					break;
				case 'spacer':
					html += '<th>&nbsp;<span style="border-left: 1px solid #cccccc"><em></em></span>&nbsp;</th>';
					break;
				case 'newline':
					html += '</tr></table><hr/><table><tr>';
					break;
				case 'select':
					html += '<td class="' + menu[ a ].classname + '">';
					html += '<select onchange="' + rem + 'texteditor.editors[' + this.index + '].' + menu[ a ].action + ';">';
					var list = menu[ a ].list.split ( ';' );
					for ( var b = 0; b < list.length; b++ )
					{
						if ( !list[ b ].length ) continue;
						var e = list[ b ].split ( ':' );
						html += '<option value="' + e[ 0 ] + '">' + e[ 1 ] + '</option>';
					}
					html += '</select></td>';
					break;
				default:
					break;
			}
		}
		this.toolbar.innerHTML = html + '</tr></table>';
		
		// Add buttons to toolbarButtons
		var realbuttons = this.toolbar.getElementsByTagName ( 'td' );
		for ( var a = 0; a < realbuttons.length; a++ )
		{
			if ( realbuttons[ a ].className )
				this.toolbarButtons[ realbuttons[ a ].className ] = realbuttons[ a ];
		}
	}
	
	// Custom css styles
	var cclasses = '';
	if ( this.customCssClasses.length )
	{
		for ( var a = 0; a < this.customCssClasses.length; a++ )
			cclasses += this.customCssClasses[ a ] + ':' + this.customCssClasses[ a ] + ';';
		cclasses = '-:Velg klasse;' + cclasses.substr ( 0, cclasses.length - 1 );
	}
	else
	{
		cclasses = '-:Velg klasse;Farget:Colored';
	}
	
	var adv = this.mode == 'admin' && this.config['mode'] == 'normal';
	// Generate the toolbar
	obj.GenerateToolbar ( Array ( 
		adv ? { 'type' : 'select', 'classname' : 'bFormatting', 'action' : 'setFormatting ( this.value )', 
			'list' : '-:Velg formatering;none:Standard;p:Paragraf;h1:Overskrift 1;h2:Overskrift 2;h3:Overskrift 3;h4:Overskrift 4;pre:Preformatert kode;' }: {},
		adv ? { 'type' : 'select', 'classname' : 'bFontFamily', 'action' : 'setFontFamily ( this.value )',
			'list' : '-:Velg skrifttype;normal:Normal;Verdana:Verdana;Arial:Arial;Times New Roman:Times New Roman;Monospace:Monospace;Calibri:Calibri;Courier:Courier' } : {},
		adv ? { 'type' : 'select', 'classname' : 'bClass', 'action' : 'setClass ( this.value );',
			'list' : cclasses } : {},
		adv ? { 'type' : 'spacer' } : {},
		adv ? { 'type' : 'button', 'classname' : 'bFlash', 'action' : 'insertFlash ( )', 'title' : 'Sett inn flash film', 'icon' : 'page_white_flash.png' } : {},
		{ 'type' : 'button', 'classname' : 'bYouTube', 'action' : 'insertYoutube ( )', 'title' : 'Sett inn Youtube link', 'icon' : 'youtube_add.png' },
		{ 'type' : 'button', 'classname' : 'bVimeo', 'action' : 'insertVimeo ( )', 'title' : 'Sett inn Vimeo link', 'icon' : 'vimeo_add.png' },
		adv ? { 'type' : 'button', 'classname' : 'bFieldObject', 'action' : 'insertFieldObject ( )', 'title' : 'Sett inn ARENA felt', 'icon' : 'layout_add.png' } : {},
		this.mode == 'admin' ? { 'type' : 'button', 'classname' : 'bImage', 'action' : 'insertImage ( )', 'title' : 'Sett inn bilde', 'icon' : 'image_add.png' } : {},
		adv ? { 'type' : 'button', 'classname' : 'bTable', 'action' : 'insertTable ( )', 'title' : 'Sett inn tabell', 'icon' : 'table_add.png' } : {},
		/*this.mode == 'admin' ? { 'type' : 'button', 'classname' : 'bGallery', 'action' : 'insertGallery ( )', 'title' : 'Sett inn galleri', 'icon' : 'images.png' } : {},*/
		adv ? { 'type' : 'button', 'classname' : 'bHR', 'action' : 'insertHR ( )', 'title' : 'Sett inn horisontal linje', 'icon' : 'text_horizontalrule.png' } : {},
		adv ? { 'type' : 'spacer' } : {},
		adv ? { 'type' : 'button', 'classname' : 'bScript', 'action' : 'toggleSource ( );", 100 ); }', 'title' : 'Se kildekode', 'icon' : 'html.png' } : {},
		adv ? { 'type' : 'newline' } : {},
		{ 'type' : 'button', 'classname' : 'bBold', 'action' : 'toggleBold ( )', 'title' : 'Fet', 'icon' : 'text_bold.png' },
		{ 'type' : 'button', 'classname' : 'bItalic', 'action' : 'toggleItalic ( )', 'title' : 'Kursiv', 'icon' : 'text_italic.png' },
		{ 'type' : 'button', 'classname' : 'bUnderline', 'action' : 'toggleUnderline ( )', 'title' : 'Understreket', 'icon' : 'text_underline.png' },
		{ 'type' : 'button', 'classname' : 'bSuper', 'action' : 'toggleSuperscript()', 'title' : 'Superscript', 'icon' :  'text_superscript.png' },
		{ 'type' : 'button', 'classname' : 'bSub', 'action' : 'toggleSubscript()', 'title' : 'Subscript', 'icon' :  'text_subscript.png' },
		{ 'type' : 'spacer' },
		{ 'type' : 'button', 'classname' : 'bUL', 'action' : 'toggleUL ( )', 'title' : 'Punkt liste', 'icon' : 'text_list_bullets.png' },
		{ 'type' : 'button', 'classname' : 'bOL', 'action' : 'toggleOL ( )', 'title' : 'Nummerert liste', 'icon' : 'text_list_numbers.png' },		
		
		{ 'type' : 'button', 'classname' : 'bAlignLeft', 'action' : 'alignLeft ( )', 'title' : 'Venstrejustert', 'icon' : 'text_align_left.png' },
		{ 'type' : 'button', 'classname' : 'bAlignRight', 'action' : 'alignRight ( )', 'title' : 'Høyrejustert', 'icon' : 'text_align_right.png' },
		{ 'type' : 'button', 'classname' : 'bAlignCenter', 'action' : 'alignCenter ( )', 'title' : 'Sentrert', 'icon' : 'text_align_center.png' },
		adv ? { 'type' : 'button', 'classname' : 'bAlignJustify', 'action' : 'alignJustify ( )', 'title' : 'Blokkjustert', 'icon' : 'text_align_justify.png' } : {},
		adv ? { 'type' : 'spacer' } : {},
		adv ? { 'type' : 'button', 'classname' : 'bIndent', 'action' : 'indent ( )', 'title' : 'Innrykk', 'icon' : 'text_indent.png' } : {},
		adv ? { 'type' : 'button', 'classname' : 'bOutdent', 'action' : 'outdent ( )', 'title' : 'Fjern innrykk', 'icon' : 'text_indent_remove.png' } : {},
		this.mode == 'admin' ? { 'type' : 'spacer' } : {},
		this.mode == 'admin' ? { 'type' : 'button', 'classname' : 'bLink', 'action' : 'createLink ( )', 'title' : 'Lag lenke', 'icon' : 'link.png' } : {},
		this.mode == 'admin' ? { 'type' : 'button', 'classname' : 'bUnlink', 'action' : 'destroyLink ( )', 'title' : 'Fjern lenke', 'icon' : 'link_break.png' } : {},
		adv ? { 'type' : 'spacer' } : {},
		{ 'type' : 'button', 'classname' : 'bCleanHTML', 'action' : 'cleanHTML ( )', 'title' : 'Rensk utvalg', 'icon' : 'page_white_swoosh.png' },
		adv ? { 'type' : 'button', 'classname' : 'bCleanAllHTML', 'action' : 'cleanAllHTML ( )', 'title' : 'Rensk hele feltet', 'icon' : 'page_white_wrench.png' } : {},
		this.mode == 'admin' ? { 'type' : 'button', 'classname' : 'bRemoveAllHTML', 'action' : 'removeAllHTML ()', 'title' : 'Ta vekk formatering', 'icon' : 'page_white_code_red.png' } : {},
		adv ? { 'type' : 'spacer' } : {},
		adv ? { 'type' : 'spacer' } : {},
		{ 'type' : 'button', 'classname' : 'bUndo', 'action' : 'undo ( )', 'title' : 'Angre', 'icon' : 'arrow_undo.png' },
		{ 'type' : 'button', 'classname' : 'bRedo', 'action' : 'redo ( )', 'title' : 'Gjør om igjen', 'icon' : 'arrow_redo.png' },
		adv ? {} : { 'type' : 'newline' },
		adv ? {} : { 'type' : 'select', 'classname' : 'bFormatting', 'action' : 'setFormatting ( this.value )', 
			'list' : '-:Velg formatering;none:Standard;p:Paragraf;h1:Overskrift 1;h2:Overskrift 2;h3:Overskrift 3;h4:Overskrift 4;pre:Preformatert kode;' },
		adv ? {} : { 'type' : 'select', 'classname' : 'bClass', 'action' : 'setClass ( this.value );',
			'list' : cclasses }
	) );
	
	// Assign the original text area
	obj.area = area;
	area.obj = obj;
	
	// Browser specific things
	obj.iframe.obj = obj;
	if ( navigator.userAgent.indexOf ( 'MSIE' ) >= 0 && !isIE9 )
	{
		addEventTo ( obj.iframe, 'onload', function ( ){ 
			obj.activateEvents ( obj ); 
			if ( obj.stylesheetSrc )
			{
				var doc = obj.getDocument ();
				var path = document.getElementsByTagName ( 'base' )[0].href;
				var link = doc.createElement ( 'link' );
				link.rel = 'stylesheet';
				link.href = path + obj.stylesheetSrc;
				var def = doc.createElement ( 'link' );
				def.rel = 'stylesheet';
				def.href = path + 'lib/templates/texteditor.css';
				var head = doc.getElementsByTagName ( 'head' )[ 0 ];
				head.appendChild ( def ); head.appendChild ( link );
				doc.documentElement.style.borderStyle = 'none';
			}
		} );
	}
	else obj.iframe.onload = function ( )
	{ 
		if ( this.obj.activateEvents ) this.obj.activateEvents ( this ); 
		this.contentDocument.designMode = 'on';
	}
	
	// Insert our texteditor
	obj.container.appendChild ( obj.iframe );
	area.parentNode.insertBefore ( obj.container, area );
	area.parentNode.insertBefore ( obj.toolbar, obj.container );
	area.parentNode.removeChild ( area );
	obj.container.appendChild ( area );
	
	// Hide our textarea
	area.style.visibility = 'hidden';
	area.style.position = 'absolute';
	area.style.top = '-1000px';
	area.style.left = '-1000px';
	
	// Add some good functions on our object -----------------------------------
	
	obj.getContent = function ( )
	{
		// If we are in view source mode and uses no cparea code editor (some browsers)
		if ( this._sourceview && !this.cpArea )
		{
			this.getDocument().body.innerHTML = this.area.value;
		}
		return this.getDocument ( ).body.innerHTML;
	}
	// Clean HTML
	obj.correctHTML = function ( string )
	{
		// Trim and make sure we don't have tables at the start
		// or end of the document (blocking editation)
		if ( !string.length )
			return string;
			
		var len = string.length - 1;
		while ( 
			len > 0 &&
			( string.substr ( len, 1 ) == ' ' || 
			  string.substr ( len, 1 ) == "\t" ||
			  string.substr ( len, 1 ) == "\n"
			)
		) { len--; }
		string = string.substr ( 0, len + 1 );
		if ( string.substr ( string.length - 8, 8 ) == '</table>' )
			string += '<p><br/></p>';
		var a = 0; 
		while ( 
			a < string.length &&
			( string.substr ( a, 1 ) == ' ' || 
			 string.substr ( a, 1 ) == "\t" ||
			 string.substr ( a, 1 ) == "\n" 
			)
		) { a++; }
		string = string.substr ( a, string.length - a );
		if ( string.substr ( 0, 6 ) == '<table' )
			string = '&nbsp;' + string;
		// Change empty p's with content
		string = string.split ( /\<p\>[\s]+\<\/p\>/i ).join ( '<p>&nbsp;</p>' );
		// Strip and remember embeds
		var protEmbs = new Array ();
		while ( matches = string.match ( /(\<span.*type\=\"movie\"[^>]*?\>)([\w\W]*?)(\<\/span\>)/i ) )
		{
			string = string.split ( matches[0] ).join ( '<!--prot' + protEmbs.length + '-->' );
			protEmbs.push ( matches[0] );
		}
		while ( matches = string.match ( /(\<span.*type\=\"fieldobject\"[^>]*?\>)([\w\W]*?)(\<\/span\>)/i ) )
		{
			string = string.split ( matches[0] ).join ( '<!--prot' + protEmbs.length + '-->' );
			protEmbs.push ( matches[0] );
		}
			
		// Add embeds again
		for ( var a = 0; a < protEmbs.length; a++ )
		{
			string = string.split ( '<!--prot' + a + '-->' ).join ( protEmbs[a] );
		}
		
		// If last element is a br, convert to p with br . . . . . . . . . . . .
		if ( string.substr ( string.length - 5, 5 ).toLowerCase () == '<br/>' )
			string = string.substr ( 0, string.length - 5 ) + '<p><br/></p>';
		else if ( string.substr ( string.length - 4, 4 ).toLowerCase () == '<br>' )
			string = string.substr ( 0, string.length - 4 ) + '<p><br/></p>';
			
		// ARENA cleaning function --------------------------------------------
		// Strip out end paragraphs with line breaks
		string = string.split ( /\<font\ [a-z]*[^>]*?\>/i ).join ( '' );
		string = string.split ( /\<\/font\>/i ).join ( '' );
		// Remove empty styles
		string = string.split ( /style\=\"\"/i ).join ( '' );
		string = string.split ( /style\=\"[ ;]*\"/i ).join ( '' );
		// Remove empty classes
		string = string.split ( /class\=\"\-\"/i ).join ( '' );
		// Remove meta tags
		string = string.split ( /\<meta[^>]*?\>/i ).join ( '' );
		// Strip comments from inside paragraphs with OFFICE CRAP!
		string = string.split ( /\<p\>\<!\-[\w\W]*?MsoNormal[\w\W]*?\<\/p\>/i ).join ( '<p></p>' );
		// Remove blockquotes styles
		string = string.split ( /\<blockquote\ style[^>]*?>/i ).join ( '<blockquote>' );
		return string;
	}
	// Set contents
	obj.setContent = function ( string )
	{
		// Remove empty paragraphs
		string = typeof ( string ) == 'string' ? string.split ( /\<p[^>]*?\>\<\/p\>/i ).join ( '' ) : string;
		if ( string )
		{
			this.bufcontentString = string;
			this.setcontentretries = 20;
		}
		var doc = this.getDocument ( );
		if ( isIE && !isIE9 )
		{
			if ( doc && doc.body )
			{
				var s = this.bufcontentString.split ( /\<body.*\n/i )[1];
				if ( typeof ( s ) == 'undefined' )
					return false;
				s = s.split ( /\<\/body\>/i )[0];
				s = this.correctHTML ( s );
				doc.body.innerHTML = s;
			}
			else if ( this.setcontentretries-- )
			{
				setTimeout ( 'texteditor.editors[' + this.index + '].setContent();', 70 );
				return;
			}
			else return;
		}
		else if ( doc )
		{
			doc.open ( );
			doc.write ( this.bufcontentString );
			doc.close ( );
			doc.body.innerHTML = this.correctHTML ( doc.body.innerHTML );
			this.bufcontentString = false;
		}
		else if ( this.setcontentretries-- )
		{
			setTimeout ( 'texteditor.editors[' + this.index + '].setContent();', 70 );
		}
		this.resizeToFit ( );
	}
	// Toggle bold
	obj.toggleBold = function ( )
	{
		if ( this.toolbarButtons.bBold.on )
		{
			this.buttonOff ( this.toolbarButtons.bBold );
			var sel = this.getSelectedNode ( );
			if ( sel )
			{
				if ( 
					sel && sel.nodeName == '#text' && sel.parentNode && 
					getStyle ( sel.parentNode, 'font-weight' ).indexOf ( 'bold' ) >= 0 
				)
				{
					sel.parentNode.style.fontWeight = 'normal';
				}
				else
				{
					sel.style.fontWeight = 'normal';
				}
			}
			else this.getDocument ( ).execCommand ( 'bold', false, false );
		}
		else
		{
			this.getDocument ( ).execCommand ( 'bold', false, false );
			this.buttonOn ( this.toolbarButtons.bBold );
		}
		this.area.value = this.getContent ( );
	}
	// Toggle italic
	obj.toggleItalic = function ( )
	{
		if ( this.toolbarButtons.bItalic.on )
		{
			this.buttonOff ( this.toolbarButtons.bItalic );
			var sel = this.getSelectedNode ( );
			if ( sel )
			{
				if ( sel.nodeName == '#text' && sel.parentNode && getStyle ( sel.parentNode, 'font-style' ).indexOf ( 'opaque' ) >= 0 )
					sel.parentNode.style.fontStyle = 'normal';
				else if ( sel && sel.nodeName == '#text' && sel.parentNode && getStyle ( sel.parentNode, 'font-style' ).indexOf ( 'italic' ) >= 0 )
				sel.parentNode.style.fontStyle = 'normal';
				else sel.style.fontStyle = 'normal'; 
			}
			else
			{
				this.getDocument().execCommand ( 'italic', false, false );
			}
		}
		else
		{
			this.getDocument().execCommand ( 'italic', false, false );
			this.buttonOn ( this.toolbarButtons.bItalic );
		}
		this.area.value = this.getContent ( );
	}
	// Toggle underline
	obj.toggleUnderline = function ( )
	{
		if ( this.toolbarButtons.bUnderline.on )
		{
			this.buttonOff ( this.toolbarButtons.bUnderline );
			var sel = this.getSelectedNode ( );
			if ( sel )
			{
				if ( sel.nodeName == '#text' && sel.parentNode && getStyle ( sel.parentNode, 'text-decoration' ).indexOf ( 'underline' ) >= 0 )
					sel.parentNode.style.textDecoration = 'none';
				else sel.style.textDecoration = 'none';
			} 
			else
			{
				this.getDocument().execCommand ( 'underline', false, false );
			}
		}
		else
		{
			this.getDocument().execCommand ( 'underline', false, false );
			this.buttonOn ( this.toolbarButtons.bUnderline );
		}
		this.area.value = this.getContent ( );
	}
	obj.setClass = function ( val )
	{
		var nod = this.getSelectedNode ();
		var sel = ( this.getRange () + "" ).length;
		if ( sel <= 0 && nod && nod != this.getDocument().body && nod.nodeName.toLowerCase () != '#text' )
		{
			nod.className = val;
		}
		else
		{
			var as = this.createTempNode ( );
			var cnt = document.createElement ( 'span' );
			cnt.innerHTML = as.innerHTML;
			cnt.className = val;
			as.parentNode.replaceChild ( cnt, as );
		}
		this.area.value = this.getContent ( );
	}
	obj.viewClass = function ( frm )
	{
		if ( !this.toolbarButtons.bClass )
			return false;
		var select = this.toolbarButtons.bClass.getElementsByTagName ( 'select' );
		select = select[ 0 ];
		select.options[ 0 ].selected = "selected";
		for ( var a = 1; a < select.options.length; a++ )
		{
			if ( select.options[ a ].value == frm )
			{
				select.options[ a ].selected = "selected";
				select.options[ 0 ].selected = false;
				return true;
			}
		}
		return false;
	}
	obj.indent = function ( )
	{
		this.getDocument ( ).execCommand ( 'indent', false, false );
	}
	obj.outdent = function ( )
	{
		this.getDocument ( ).execCommand ( 'outdent', false, false );
	}
	// Toggle ordered lists
	obj.toggleOL = function ( )
	{
		this.getDocument ( ).execCommand ( 'insertorderedlist', false, false );
	}
	// Toggle unordered lists
	obj.toggleUL = function ( )
	{
		this.getDocument().execCommand ( 'insertunorderedlist', false, false );
	}
	obj.reParentIfSingleChild = function ( ele )
	{
		if ( ele && ele.parentNode && ele.parentNode.childNodes.length == 1 && ele.parentNode.nodeName.toLowerCase ( ) != 'body' )
		{
			if ( ele.parentNode.parentNode )
				ele.parentNode.parentNode.replaceChild ( ele, ele.parentNode );
			this.checkSelection ( );
			return true;
		}
		this.checkSelection ( );
		return false;
	}
	obj.alignLeft = function ( )
	{
		var l = this._lastClickedItem;
		if ( l && l.nodeName.toLowerCase ( ) == 'img' )
		{
			if ( this.toolbarButtons.bAlignLeft.on )
			{
				// Special case when the clicked item is in a left aligned block element and isn't floating
				if ( l.parentNode && getStyle ( l, 'float' ) != 'left' && l.align != 'left' )
				{
					l.style.float = 'left';
					l.align = 'left';
					this.checkSelection ( );
					return;
				}
				l.align = '';
				l.style.float = 'none';
				this.reParentIfSingleChild ( l );
			}
			else
			{
				l.style.float = 'left';
				l.align = 'left';
			}
		}	
		else this.getDocument().execCommand ( 'justifyleft', false, false );
		this.checkSelection ( );
	}
	obj.alignRight = function ( )
	{
		var l = this._lastClickedItem;
		if ( l && this._lastClickedItem.nodeName.toLowerCase ( ) == 'img' )
		{
			if ( this.toolbarButtons.bAlignRight.on )
			{
				// Special case when the clicked item is in a left aligned block element and isn't floating
				if ( l.parentNode && getStyle ( l, 'float' ) != 'right' && l.align != 'right' )
				{
					l.style.float = 'right';
					l.align = 'right';
					this.checkSelection ( );
					return;
				}
				l.align = '';
				l.style.float = 'none';
				this.reParentIfSingleChild ( l );
			}
			else
			{
				l.style.float = 'right';
				l.align = 'right';
			}
		}	
		else this.getDocument().execCommand ( 'justifyright', false, false );
		this.checkSelection ( );
	}
	obj.alignCenter = function ( )
	{
		var l = this._lastClickedItem;
		if ( this.toolbarButtons.bAlignCenter.on )
		{
			this.buttonOff ( this.toolbarButtons.bAlignCenter );
			var ele = this.getSelectedNode ( );
			if ( this.reParentIfSingleChild ( ele ) ) return;
			this.getDocument ( ).execCommand ( 'justifyleft', false, false );
		}
		else
		{
			if ( l && l.nodeName.toLowerCase ( ) == 'img' )
			{
				l.style.float = 'none';
				l.align = '';
			}
			this.getDocument().execCommand ( 'justifycenter', false, false );
		}
		this.checkSelection ( );
	}
	obj.alignJustify = function ( )
	{
		var l = this._lastClickedItem;
		if ( this.toolbarButtons.bAlignJustify.on && l.nodeName.toLowerCase ( ) == 'img' )
		{
			l.style.float = 'none';
			l.style.align = '';
			var n;
			if ( ( n = this.getSelectedNode ( ) ) != l && n )
				n.style.textAlign = '';
			else l.style.textAlign = '';	
		}
		else this.getDocument().execCommand ( 'justifyfull', false, false );
		this.checkSelection ( );
	}
	// Create a link
	obj.createLink = function ( )
	{
		// Memorize the active editor id
		texteditor.activeEditorId = this.area.id;

		// Get old link
		var node = this.getSelectedNode ( );
		
		// Get selected text
		var rangetext = this.getRange ();
		if ( isIE ) rangetext = rangetext.text;
		else rangetext = rangetext+"";
		var nodetext = node ? ( node.innerHTML ? node.innerHTML : node.nodeValue ) : '';
		if ( nodetext )
		{
			nodetext = nodetext.split ( /\<[^>]*?\>/i ).join ( '' );
		}
		this.storeSelection ();
		
		if ( node.parentNode && node.parentNode.nodeName.toLowerCase ( ) == 'a' )
			node = node.parentNode;
		// Try to get text again
		nodetext = node ? ( node.innerHTML ? node.innerHTML : node.nodeValue ) : '';
		
		if ( !node && this._lastClickedItem )
		{
			if ( this._lastClickedItem.nodeName.toLowerCase ( ) == 'body' )
				node = false; 
			else node = this._lastClickedItem;
		}
		// check out that we're not a nested node inside an a tag
		var nested = false;
		if ( node )
		{
			var testNode = node;
			while ( testNode != this.getDocument().body )
			{
				if ( testNode.nodeName.toLowerCase () == 'a' )
				{
					nested = testNode;
					break;
				}
				if ( testNode.parentNode )
					testNode = testNode.parentNode;
				else break;
			}
		} 
		if ( node && nodetext && nodetext.length != rangetext.length )
		{
			this.linkMode = 'bookmark';
		}
		else if ( node && node.nodeName.toLowerCase ( ) != 'img' && node.nodeName.toLowerCase ( ) != 'a' )
		{
			node = false;
		}
		// Check out the parentnode if that is the one which is a href
		if ( nested && nested.href )
		{
			node = nested;
		}
		
		// Edit link?
		if ( node && node.href )
		{
			this.linkNode = node;
			this.linkMode = false;
			this.bookmark = false;
			initModalDialogue ( 
				'link_dialogue', 480, 330, 
				'admin.php?plugin=texteditor&pluginaction=insertlink', 
				insertLink
			);
		}
		// New link?
		else
		{
			this.linkNode = false;
			if ( !node )
			{
				var r = this.getRange ( );
				if ( r && r.getBookmark ) 
				{
					this.bookmark = this.getRange ( ).getBookmark ( );
					this.linkMode = 'bookmark';
				}
				if ( node = this.getSelectedNode( ) )
				{
					if ( (node.nodeName.toLowerCase() == 'td' || node.nodeName.toLowerCase() == 'th') && node.childNodes.length == 1 )
						node = node.getElementsByTagName ( '*' )[0];
					this.linkNode = node;
				}
				else if ( r.length )
				{
					var nod = this.createTempNode ();
					var textnode = document.createTextNode ( nod.innerHTML );
					if ( nod.parentNode )
					{
						nod.parentNode.replaceChild ( textnode, nod );
						this.linkNode = textnode;
					}
					else return false;
				}
				else return false;
			}
			else 
			{
				this.linkNode = node;
			}
			initModalDialogue ( 
				'link_dialogue', 480, 330, 
				'admin.php?plugin=texteditor&pluginaction=insertlink&linktype=new', 
				function () { ge ( 'link__Url' ).value = 'http://'; insertLink(); }
			);
		}
	}
	// Remove a link
	obj.destroyLink = function ( )
	{
		var nod = this.getSelectedNode ( 'a' );
		if ( nod && nod.nodeName.toLowerCase() == 'a' )
		{
			var t = document.createTextNode ( nod.innerHTML );
			nod.parentNode.replaceChild ( t, nod );
		}
		else
		{
			this.getDocument ( ).execCommand ( 'unlink', false, false );
		}
		this.area.value = this.getContent ( );
	}
	
	obj.storeSelection = function ( ) 
	{
		this.storedSelection = false;
		var doc = this.getDocument();
		if ( doc.getSelection )
		{
			var selection = doc.getSelection ( );
			if ( selection.rangeCount > 0 ) 
			{
				var selectedRange = selection.getRangeAt( 0 );
				this.storedSelection = selectedRange.cloneRange ( );
			}
		}
		else if ( this.getDocument().selection )
		{
			var selection = this.getDocument().selection;
			if ( selection.type.toLowerCase() == 'text' ) 
				this.storedSelection = selection.createRange ( ).getBookmark ( );
		}
	}

	obj.restoreSelection = function ( ) 
	{
		if ( this.storedSelection ) 
		{
			var doc = this.getDocument ();
			if ( doc.getSelection ) 
			{
				var selection = doc.getSelection ( );
				selection.removeAllRanges ( );
				selection.addRange ( this.storedSelection );
			}
			else if ( this.getDocument().selection && this.getDocument().body.createTextRange ) 
			{
				var range = this.getDocument().body.createTextRange ( );
				range.moveToBookmark( this.storedSelection );
				range.select ( );
			}
		}
	}

	
	// Insert a web gallery
	obj.insertGallery = function ( )
	{
		initModalDialogue ( 'gallery', 720, 540, 'admin.php?plugin=texteditor&pluginaction=creategallery', setupGalleryForm );
	}
	obj.insertHR = function ( )
	{
		this.insertHTML ( '<hr/>' );
	}
	// Toggle superscript
	obj.toggleSuperscript = function ( )
	{
		if ( this.toolbarButtons.bSuper.on )
			this.buttonOff ( this.toolbarButtons.bSuper );
		else
			this.buttonOn ( this.toolbarButtons.bSuper );
		this.getDocument ( ).execCommand ( 'superscript', false, false );
		this.area.value = this.getContent ( );
	}
	// Toggle subscript
	obj.toggleSubscript = function ( )
	{
		if ( this.toolbarButtons.bSub.on )
			this.buttonOff ( this.toolbarButtons.bSub );
		else
			this.buttonOn ( this.toolbarButtons.bSub );
		this.getDocument ( ).execCommand ( 'subscript', false, false );
		this.area.value = this.getContent ( );
	}
	obj.insertYoutube = function ()
	{
		var lnk = prompt ( 'Sett inn YouTube embed url', '' );
		if ( lnk && lnk.length )
		{
			var testlink = lnk.indexOf ( 'watch?' ) > 0 || lnk.indexOf ( 'youtu.be' ) >= 0;
			if ( testlink )
			{
				if ( lnk.indexOf ( 'watch?' ) > 0 )
					lnk = lnk.split ( 'watch?v=' ).join ( 'v/' ) + '?fs=1&amp;hl=en_US';
				else lnk = 'http://www.youtube.com/v/' + lnk.split ( 'youtu.be/' )[1] + '?version=3&amp;hl=en_US';
				
				var d = new Date ();
				var str = '' +
				'<span arenatype="movie" style="width: 480px; height: 270px; display: block; border: 2px dotted #aaa; background: #ccc url(admin/gfx/arenaicons/page_flash_64.png) no-repeat center center" id="flash_' + d.getTime() + '" data="' + lnk + '" width="480" height="270" wmode="transparent" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash">' +
				'	<param name="width" value="480"></param>' +
				'	<param name="height" value="270"></param>' +
				'	<param name="movie" value="' + lnk + '"></param>' +
				'	<param name="allowscriptaccess" value="always"></param>' +
				'	<param name="allowfullscreen" value="true"></param>' +
				'	<param name="wmode" value="transparent"></param>' +
				'</span>&nbsp;'
				this.insertHTML ( str );
			}
			else alert ( 'Feil Youtube lenke adresse.' );
		}
	}
	obj.insertVimeo = function ()
	{
		var lnk = prompt ( 'Sett inn Vimeo embed url', '' );
		if ( lnk && lnk.length )
		{
			if ( lnk.indexOf ( 'vimeo.com/' ) > 0 )
			{
				lnk = lnk.split ( 'vimeo.com/' )[1];
				lnk = 'http://vimeo.com/moogaloop.swf?clip_id=' +
						lnk + '&amp;server=vimeo.com&amp;show_title=0&amp;show_byline=0' +
						'&amp;show_portrait=0&amp;color=00adef&amp;fullscreen=1&amp;autoplay=0&amp;loop=0';
				var d = new Date ();
				var str = '' +
				'<span arenatype="movie" style="width: 480px; height: 270px; display: block; border: 2px dotted #aaa;' +
				' background: #ccc url(admin/gfx/arenaicons/page_flash_64.png) no-repeat center center" id="flash_' + 
				d.getTime() + '" data="' + lnk + '" width="480" height="270" wmode="transparent" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash">' +
				'	<param name="width" value="480"></param>' +
				'	<param name="height" value="270"></param>' +
				'	<param name="movie" value="' + lnk + '"></param>' +
				'	<param name="allowfullscreen" value="true"></param>' +
				'	<param name="allowscriptaccess" value="always"></param>' +
				'	<param name="wmode" value="transparent"></param>' +
				'</span>&nbsp;';
				this.insertHTML ( str );
			}
			else alert ( 'Feil Vimeo lenke adresse.' );
		}
	}
	obj.insertFieldObject = function ()
	{
		initModalDialogue ( 'fieldobject', 400, 200, 'admin.php?plugin=texteditor&pluginaction=insertfieldobject' );
	}
	// Strip bad html
	obj.cleanBadHTML = function ( )
	{
		var b = this.getDocument().body;
		var cnt = b.innerHTML; var a;
		cnt = cnt.split ( "\t" ).join ( "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" );
		// Remove apple spans
		while ( ( a = cnt.match ( /(\<span.*?apple\-[^"]*\"[^>]*?\>)(.*?)(\<\/span\>)/i ) ) )
		{ cnt = cnt.split ( a[0] ).join ( a[2] ); }
		// Remove empty divs
		while ( ( a = cnt.match ( /(\<div\>)(.*?)(\<\/div\>)/i ) ) )
		{ cnt = cnt.split ( a[0] ).join ( a[2] ); }
		// Other bad things
		cnt = cnt.split ( /\<v[^>]*?\>/i ).join ( '' );
		cnt = cnt.split ( /\<\/v[^>]*?\>/i ).join ( '' );
		cnt = cnt.split ( /\<w[^>]*?\>/i ).join ( '' );
		cnt = cnt.split ( /\<\/w[^>]*?\>/i ).join ( '' );
		cnt = cnt.split ( /\<\!\-\-[\w\W]*?\-\-\>/i ).join ( '' );
		// Insert changed html
		if ( cnt != b.innerHTML )
			b.innerHTML = cnt;
	}
	obj.selectElement = function ( el, caret )
	{
		var doc = this.getDocument ();
		var s = this.getSelection ();
		var b = doc.body;
		if ( document.createRange )
		{
			var r = doc.createRange();
			r.selectNodeContents( el );
			r.collapse(false);
			s.removeAllRanges();
			s.addRange(r);
		}
		else
		{
			 var r = b.createTextRange();
		    r.moveToElementText( el );
		    r.select();
		    if ( caret ) r.pasteHTML ( el.innerHTML );
		}
	}
	// Undo an operation
	obj.undo = function ( )
	{
		if ( navigator.userAgent.indexOf ( 'Firefox' ) >= 0 )
		{
			this.getDocument ().execCommand( 'undo', false, null );
			return;
		}
		if ( !this.undoBuffer.length )
			return;
		// Go to previous history
		if ( this.undoPos > 0 ) 
		{
			this.undoPos--;
			// If the buffer has content, use it
			if ( typeof ( this.undoBuffer[ this.undoPos ] ) != 'undefined' )
			{
				//alert ( this.undoPos + ' ' + this.undoBuffer[ this.undoPos ] );
				this.getDocument().body.innerHTML = this.undoBuffer[ this.undoPos ];
				this.lastBuffer = this.undoBuffer[ this.undoPos ]
				return true;
			}
		}
		return false;
	}
	// Undo the undo operation
	obj.redo = function ( )
	{
		if ( navigator.userAgent.indexOf ( 'Firefox' ) >= 0 )
		{
			this.getDocument ().execCommand( 'Redo', false, null );
			return;
		}
		if ( this.undoBuffer.length <= 0 )
			return;
		// Go to history in the future
		if ( this.undoPos + 1 <= this.undoBuffer.length - 1 )
		{
			this.undoPos++;
			// If it has content, use it
			if ( typeof ( this.undoBuffer[this.undoPos] ) != 'undefined' )
			{
				this.getDocument ( ).body.innerHTML = this.undoBuffer[ this.undoPos ];
				this.lastBuffer = this.undoBuffer[ this.undoPos ]
				return true;
			}
		}
		return false;
	}
	// Our undo buffer
	obj.rememberContent = function ( )
	{
		this.resizeToFit ();
		
		// Only remember if content has changed
		var newContent = this.getDocument ().body.innerHTML;
		
		if ( newContent != this.lastBuffer )
		{
			// Add content to undo buffer and move pos to next history slot
			this.undoBuffer[ ++this.undoPos ] = newContent;
			this.lastBuffer = newContent;

			// If we have more than maximum number of undo buffers, delete the first one
			if ( this.undoPos > 50 )
			{
				var o = new Array ( );
				for ( var a = 1; a < this.undoBuffer.length; a++ )
				{
					o[ a-1 ] = this.undoBuffer[ a ];
				}
				this.undoBuffer = o;
				this.undoPos--;
			}
		}
	}
	// Null out undo buffer
	obj.resetUndoBuffer = function ()
	{
		if ( !this.getDocument ().body )
		{
			setTimeout ( 'texteditor.editors[' + obj.index + '].resetUndoBuffer()', 100 );
		}
		else
		{
			this.undoBuffer = new Array ();
			this.undoBuffer[0] = this.getDocument ( ).body.innerHTML;
			this.lastBuffer = this.undoBuffer[0];
			this.undoPos = 0;
		}
	}
	obj.insertHTML = function ( html )
	{
		if ( isIE )
		{	
			// Fix html
			html = this.correctHTML ( html );
			
			// First try this way - experimental!
			if ( this.localRangeObject )
			{
				this.localRangeObject.pasteHTML ( html );
				this.area.value = this.getContent ( );
				return true;
			}
			return false;
		}
		else
		{
			this.getDocument ( ).execCommand ( 'inserthtml', false, html );
		}
		this.area.value = this.getContent ( );
	}
	obj.insertFlash = function ( )
	{
		texteditor.activeEditorId = this.area.id;
		initModalDialogue ( 'library', 600, 540, 'admin.php?plugin=library&pluginaction=flashdialog', setupLibraryFlashDialog );
	}
	obj.insertImage = function ( )
	{
		// Memorize the active editor id
		this.storeSelection ();
		texteditor.activeEditorId = this.area.id;
		initModalDialogue ( 'library', 600, 545, 'admin.php?plugin=library&pluginaction=renderfordialog', setupLibraryDialog );
	}
	obj.encapsulateElement = function ( tagName )
	{
		var node = this._lastClickedItem ? this._lastClickedItem : this.getSelectedNode ( );
		if ( node )
		{
			switch ( tagName )
			{
				case 'p':
					var newNode = this.getDocument ( ).createElement ( 'p' );
					node.parentNode.replaceChild ( newNode, node );
					newNode.appendChild ( node );
					break;
				default: 
					break;
			}
		}
	}
	obj.cleanHTML = function ( )
	{
		// Store selection in memory
		this.storeSelection ();
		
		// Remove format
		this.getDocument().execCommand ( 'removeformat', false, null );
		
		// Get content now
		var fhtml = this.getDocument().body.innerHTML;

		// Alter document
		this.insertHTML ( '<!--altered document-->' );

		// Create new nodes on selection
		var nhtml = this.getDocument().body.innerHTML;
		
		// New documents first HTML part
		var fpart = '';
		
		// Find the first part which remains unchanged
		for ( var a = 0; a < fhtml.length; a++ )
		{
			var fchar = fhtml.substr ( a, 1 );
			if ( fchar != nhtml.substr ( a, 1 ) )
				break;
			fpart += fchar;
		}
		var endfp = fpart.length;
		
		// Find the last part that remains unchanged
		var fixpart = '';
		var diff = nhtml.length - fhtml.length;
		var lpart = '';
		var mode = 0;
		for ( var q = 0, z = fhtml.length; z > a-1; z--, q++ )
		{
			var fchar = fhtml.substr ( z, 1 );
			if ( fchar != nhtml.substr ( z+diff, 1 ) && mode == 0 )
			{
				mode = 1;
				fixpart = fchar;
			}
			else if ( mode == 1 && q != endfp )
			{
				fixpart = fchar + fixpart;
			}
			else
			{
				lpart = fchar + lpart;
			}
		}
		
		// Clean selected part
		fixpart = fixpart.split ( "\n" ).join ( "\r" ).split ( /\<br[^>]*?\>/i ).join ( "\n" );
		fixpart = fixpart.split ( /\<\/p[^>]*?\>/i ).join ( "\n" );
		fixpart = fixpart.split ( /\<[^>]*?>/i ).join ( '' );
		fixpart = fixpart.split ( "\n" ).join ( "<br/>" );
		
		// Fix document
		this.getDocument().body.innerHTML = fpart + fixpart + lpart;
		
		// Try to restore selection
		this.restoreSelection ();
	}
	obj.removeAllHTML = function ( )
	{
		var content = this.getDocument ( ).body.innerHTML;
		content = content.split ( /\<[b|B][r|R][^>]*?\>/ ).join ( "\n" );
		content = content.split ( /\<[^>]*?\>/ ).join ( '' );
		this.getDocument ( ).body.innerHTML = content.split ( "\n" ).join ( "<br>" );
	}
	obj.cleanAllHTML = function ( )
	{
		this.deepCleanHTML ( 1 );
	}
	obj.getSelectedHTML = function ( )
	{
		if (document.selection && document.selection.createRange) 
		{
			return ( document.selection.createRange ( ) ).htmlText;
		}
		else
		{
			return this.getSelection ();
		}
		return '';
	}
	// Funtion does not work properly
	obj.deepCleanHTML = function ( mode )
	{	
		var cnt;	
		if ( mode )
		 cnt = this.getDocument ( ).body.innerHTML;
		else cnt = this.getSelectedHTML ( ) + '';
		
		// Remove empty tags
		cnt = cnt.split ( /\<span[^>]*?\><\/span[^>]*?\>/ ).join ( '' );
		// Remove office tags
		cnt = cnt.split ( /\<p\:o[^>]*?\><\/p\:o[^>]*?\>/ ).join ( '' );
		// Remove title tags
		cnt = cnt.split ( /\<title[^>]*?\>[^<]*?<\/title[^>]*?\>/ ).join ( '' );
		// Remove meta tags
		cnt = cnt.split ( /\<meta[^>]*?\>[^<]*?<\/meta[^>]*?\>/ ).join ( '' );
		cnt = cnt.split ( /\<meta[^>]*?\>/ ).join ( '' );
		// Remove style tags
		cnt = cnt.split ( /\<style[^>]*?\>[\w\W]*?<\/style[^>]*?\>/ ).join ( '' );
		// Remove font tags
		cnt = cnt.split ( /\<[/]*font[^>]*?\>/ ).join ( '' );
		// Remove strong, italic, underline
		cnt = cnt.split ( /\<[/]*strong[^>]*?\>/ ).join ( '' );
		cnt = cnt.split ( /\<[/]*u\>/i ).join ( '' );
		cnt = cnt.split ( /\<[/]*i\>/i ).join ( '' );
		cnt = cnt.split ( /\<[/]*em[^>]*?\>/i ).join ( '' );
		cnt = cnt.split ( /\<[/]*b\>/i ).join ( '' );
		cnt = cnt.split ( /\<[/]*h[^>]*?\>/i ).join ( '' );
		
		// Remove styles
		var mat;
		while ( mat = cnt.match ( /(style=\"[^"]*?\")/ ) )
			cnt = cnt.split ( mat[ 0 ] ).join ( '' ); 
		// Remove classes
		while ( mat = cnt.match ( /(class=\"[^"]*?\")/ ) )
			cnt = cnt.split ( mat[ 0 ] ).join ( '' );
		// Remove width
		while ( mat = cnt.match ( /(width=\"[^"]*?\")/ ) )
			cnt = cnt.split ( mat[ 0 ] ).join ( '' ); 
		// Remove height
		while ( mat = cnt.match ( /(height=\"[^"]*?\")/ ) )
			cnt = cnt.split ( mat[ 0 ] ).join ( '' ); 
		// Remove border
		while ( mat = cnt.match ( /(border=\"[^"]*?\")/ ) )
			cnt = cnt.split ( mat[ 0 ] ).join ( '' ); 
			
		// Remove trailing whitespace in tags
		cnt = cnt.split ( /\s\>/ ).join ( '>' );
		
		// Update document
		if ( mode ) this.getDocument ( ).body.innerHTML = cnt;
		else this.insertHTML ( cnt );
		this.area.value = this.getContent ( );
	}
	obj.insertTable = function ( )
	{
		// Memorize the active editor id
		texteditor.activeEditorId = this.area.id;
		
		if ( !document.getElementById ( 'tblCols' ) )
		{
			initModalDialogue ( 'inserttable', 320, 200, 'admin.php?plugin=texteditor&pluginaction=inserttable' );
			return;
		}
		
		var cols = parseInt ( document.getElementById ( 'tblCols' ).value );
		var rows = parseInt ( document.getElementById ( 'tblRows' ).value );
		if ( cols <= 0 || rows <= 0 )
			return;
			
		var html = '<table><tbody>';
		
		cols = cols > 0 ? cols : 1;
		rows = rows > 0 ? rows : 1;
		
		for ( var r = 0; r < rows; r++ )
		{
			html += '<tr>';
			for ( var c = 0; c < cols; c++ )
			{
				html += '<td>&nbsp;</td>';
			}
			html += '</tr>';
		}
		
		html += '</tbody></table>';
		this.insertHTML ( html );
	}
	obj._remWaste = function ( ftype, string )
	{
		if ( ftype.substr ( 0, 1 ) == 'H' )
		{
			string = string.split ( /\<[/]{0,}h[^>]*?>/ ).join ( '' );
			string = string.split ( /\<[/]{0,}p[^>]*?>/ ).join ( '' );
			string = string.split ( /\<[/]{0,}div[^>]*?>/ ).join ( '' );
			string = string.split ( /\<[/]{0,}span[^>]*?>/ ).join ( '' );
		}
		return string;
	}
	obj.setFormatting = function ( ftype )
	{
			var body = this.getDocument().body;
			var n = this.getSelectedNode ( );
			if ( n == body ) n = false;
			else if ( n == body.parentNode ) n = false;
			var sel;
			
			if ( isIE )
			{
				sel = this.getRange ( );
				if ( sel.text ) 
					sel = sel.text;
				else sel = false;
			}
			else sel = this.getSelection () + '';
			
			ftype = ftype.toUpperCase ( );
			var specialClass = '';
			if ( ftype == 'NONE' )
			{
				ftype = 'P';
				specialClass = 'Standard';
			}
			switch ( ftype )
			{
				case 'H1':
				case 'H2':
				case 'H3':
				case 'H4':
				case 'H5':
				case 'H6':
				case 'PRE':
				case 'P':
					// If the n is a tagname then replace the node with a new node
					if ( n.tagName && ( n.innerHTML && n.innerHTML.length ) && !sel && n.parentNode )
					{
						var nod = n.parentNode;
						var ele = document.createElement ( ftype );
						ele.innerHTML = n.innerHTML;
						nod.replaceChild ( ele, n );
						if ( specialClass )	ele.className = specialClass;
						ele.innerHTML = this._remWaste ( ftype, ele.innerHTML );
					}
					else
					{
						// If we have a selection
						var as1 = this.createTempNode ();
						if ( sel.length > 0 && as1.parentNode )
						{	
							var asr = this.getDocument().body.getElementsByTagName ( 'a' );
							var replacers = new Array ();
							
							// Go through all links
							for ( var a = 0; a < asr.length; a++ )
							{
								if ( asr[a].href != as1.href ) 
								{
									// don't style links that are not tempnodes!
									continue;
								}
								var as = asr[a];
								
								// Create temp string with everything in the parentnode except the selected
								var pnodeWithoutTmpNode = as.parentNode.innerHTML + '';
								pnodeWithoutTmpNode = pnodeWithoutTmpNode.split ( as.getAttribute ( 'href' ) ).join ( 'arenacmdelete' );
								pnodeWithoutTmpNode = pnodeWithoutTmpNode.split ( /\<a href\=\"arenacmdelete\"[^>]*?\>.*?\<\/a>/i ).join ( '' );
							
								// Create new element on whole parentnode
								if ( 
									!pnodeWithoutTmpNode.length &&
									as.parentNode != body && 
									as.parentNode != body.parentNode && 
									as.parentNode.nodeName.toLowerCase ().substr(0,1) != 't'
								)
								{
									var nod = document.createElement ( ftype );
									nod.innerHTML = as.innerHTML;
									nod.innerHTML = this._remWaste ( ftype, nod.innerHTML );
									if ( specialClass )	nod.className = specialClass;
									replacers.push ( [ as.parentNode.parentNode, nod, as.parentNode ] );
								}
								// The parent is body or mixed node. Replace temp node safely
								else
								{
									var nod = document.createElement ( ftype );
									nod.innerHTML = as.innerHTML;
									nod.innerHTML = this._remWaste ( ftype, nod.innerHTML );
									if ( specialClass )	nod.className = specialClass;
									replacers.push ( [ as.parentNode, nod, as ] );
								}	
							}
							// Replace all to be replaced in reverse order
							for ( var a = replacers.length - 1; a >= 0; a-- )
							{
								var reps = replacers[a];
								reps[0].replaceChild ( reps[1], reps[2] );
							}
						}
						// No selection (we just clicked somewhere) (don't catch this one)
						else
						{
							if ( as1 && as1.parentNode )
							{
								// We clicked an image or a hr etc
								if ( n.nodeName )
								{
									if ( as1.innerHTML.match ( /special_arenacm_link.*?done/i ) )
									{
										as1.innerHTML = as1.innerHTML.split ( /special_arenacm_link.*?done/i ).join ( '' );
									}
									as1.appendChild ( n );
									var p = document.createElement ( ftype );
									p.innerHTML = as1.innerHTML;
									p.innerHTML = this._remWaste ( ftype, p.innerHTML );
									if ( specialClass )	p.className = specialClass;
									as1.parentNode.replaceChild ( p, as1 );
								}
								// We selected an image or something like it
								else
								{
									var p = document.createElement ( ftype );
									p.innerHTML = as1.innerHTML;
									p.innerHTML = this._remWaste ( ftype, p.innerHTML );
									if ( specialClass )	p.className = specialClass;
									as1.parentNode.replaceChild ( p, as1 );
								}
							}
							else
							{
								// Never catched this one
							}
							return false;
						}
					}
					this.area.value = this.getDocument ( ).body.innerHTML;
					return true;
				default:
					break;
			}
		
	}
	obj.createTempNode = function ( )
	{
		var linkc = 'special_arenacm_link' + ( Math.random ( ) * 9999 ) + ( Math.random ( ) * 9999 ) + '_done';
		this.getDocument().execCommand ( 'CreateLink', '', linkc );
		var as = this.getDocument ( ).getElementsByTagName ( 'a' );
		for ( var a = 0; a < as.length; a++ )
		{
			if ( as[a].href.indexOf ( linkc ) >= 0 )
			{
				return as[a];
			}
		}
		return false;
	}
	obj.viewFormatting = function ( frm, node )
	{
		var select = this.toolbarButtons.bFormatting.getElementsByTagName ( 'select' )[0];
		select.options[ 0 ].selected = "selected";
		if ( frm == 0 )
			return;
		else
		{
			for ( var a = 0; a < select.options.length; a++ )
			{
				if ( node && frm == 'p' && select.options[ a ].value == 'none' )
				{
					select.options[ a ].selected = "selected";
					select.options[ 0 ].selected = false;
					return true;
				}
				else if ( !node && select.options[ a ].value == frm )
				{
					select.options[ a ].selected = "selected";
					select.options[ 0 ].selected = false;
					return true;
				}
			}
		}
		return false;
	}
	obj.setFontFamily = function ( fam )
	{
		this.getDocument().execCommand ( 'fontname', false, fam );
	}
	obj.viewFontFamily = function ( frm )
	{
		if ( !this.toolbarButtons.bFontFamily )
			return false;
			
		var select = this.toolbarButtons.bFontFamily.getElementsByTagName ( 'select' );
		select = select[ 0 ];
		select.options[ 0 ].selected = "selected";
		for ( var a = 1; a < select.options.length; a++ )
		{
			if ( select.options[ a ].value == frm )
			{
				select.options[ a ].selected = "selected";
				select.options[ 0 ].selected = false;
				return true;
			}
		}
		return false;
	}
	obj.setFontSize = function ( size )
	{
		this.getDocument().execCommand ( 'fontsize', false, size );
	}
	obj._sourceview = false;
	obj.toggleSource = function ( mode )
	{
		if ( mode )
		{
			mode = mode == 'hidden' ? false : true;
			if ( mode == this._sourceview )
				return;
			this._sourceview = mode;
		}
		else this._sourceview = ( !this._sourceview ? true : false );
		
		// Show textarea / codepress (if installed)
		if ( this._sourceview == true )
		{
			this.area.value = this.getDocument ( ).body.innerHTML;
			this.buttonOn ( this.toolbarButtons.bScript );
			this.iframe.contentEditable = false;
			this.iframe.style.display = 'none';
			this.area.style.display = '';
			this.area.style.position = 'relative';
			this.area.style.top = 'auto';
			this.area.style.left = 'auto';
			this.area.style.visibility = 'visible';

			// If chrome, safari etc (codepress doesnt support)
			if ( navigator.userAgent.indexOf ( 'afari' ) > 0 )
			{
				return;
			}
			
			if ( this.area.id.indexOf ( '_cp' ) && typeof ( CodePress ) != 'undefined' )
			{
				if ( !this.cpArea )
				{
					this.cpArea = document.createElement ( 'textarea' );
					this.cpArea.className = 'codepress html';
					this.cpArea.id = 'temporary_codepress_' + this.area.id;
					this.cpArea.style.position = 'absolute';
					this.cpArea.style.visibility = 'hidden';
					this.cpArea.style.left = '-1000px';
					this.cpArea.style.height = this.iframe.style.height;
					this.cpArea.value = this.area.value;
					this.area.parentNode.insertBefore ( this.cpArea, this.area );
					this.area.style.display = 'none';
					
					// Make sure form saves
					var frm = this.area;
					while ( frm != document.body && frm.nodeName.toLowerCase () != 'form' )
						frm = frm.parentNode;
					if ( frm.nodeName.toLowerCase ( ) == 'form' )
					{
						frm.obj = this;
						this.area.frm = frm;
						if ( isIE )
						{
							frm.oldsubmit = frm.submit;
							frm.submit = function ( )
							{
								this.obj.toggleSource ( );
								this.oldsubmit();
							}
						}
						else
						{
							frm.submit = function ( )
							{
								this.obj.toggleSource ( );
								delete this.submit;
								this.submit ( );
							}
						}
					}
					
				}
				if ( typeof ( CodePress ) != 'undefined' )
				{
					CodePress.run ();
					CodePressArenaFunctions ( this.area.parentNode.getElementsByTagName ( 'iframe' )[1] );
				}
			}
			// Else if we have only got the blest editor
			else
			{
				this.area.onkeyup = function ( )
				{
					this.obj.getDocument ( ).body.innerHTML = this.value;
				}
			}
		}
		// Show rich edit iframe
		else
		{
			if ( typeof ( CodePress ) != 'undefined' && navigator.userAgent.indexOf ( 'afari' ) < 0 )
			{
				// Iframe
				var ifr = this.area.parentNode.getElementsByTagName ( 'iframe' )[1];
				
				// Copy over from cparea and remove it
				this.area.style.display = '';
				if ( isIE )
					this.cpArea.value = ifr.contentWindow.CodePress.getCode ( );
				this.area.value = this.cpArea.value;
				this.area.parentNode.removeChild ( this.cpArea );
				this.cpArea = false;
				
				// Clean up submit func
				if ( this.area.frm )
				{
					if ( isIE )
					{
						this.area.frm.submit = function ( ){ this.oldsubmit(); };
					}
					else delete this.area.frm.submit;
				}
				
				// Get CodePress iframe
				if ( ifr.contentWindow.CodePress )
				{
					try
					{
						delete ifr.contentWindow.CodePress;
					}
					catch ( e ) { ifr.contentWindow.CodePress = null; }
				}
				ifr.parentNode.removeChild ( ifr );
			}
			this.getDocument ( ).body.innerHTML = this.area.value;
			this.buttonOff ( this.toolbarButtons.bScript );
			this.iframe.contentEditable = true;
			this.iframe.style.display = '';
			this.area.style.position = 'absolute';
			this.area.style.top = '-1000px';
			this.area.style.left = '-1000px';
			this.area.style.visibility = 'hidden';
			// Webkit makes the whole field blue, so skip
			if ( navigator.userAgent.indexOf ( 'afari' ) < 0 )
				this.iframe.focus ( );
		}
	}
	obj.removeControl = function ( options )
	{
		// Remove DOM nodes etc
		if ( this.iframe ) { this.iframe.contentEditable = false; this.container.removeChild ( this.iframe ); }
		if ( this.area ) this.container.removeChild ( this.area );
		if ( this.container )
		{
			this.container.parentNode.insertBefore ( this.area, this.container );
			this.container.parentNode.removeChild ( this.container );
		}
		if ( this.toolbar ) this.toolbar.parentNode.removeChild ( this.toolbar );
	}

	obj.getDocument = function ( )
	{
		if ( this.iframe )
		{
			if ( this.iframe.contentDocument )
				return this.iframe.contentDocument;
			if ( this.iframe.contentWindow )
				return this.iframe.contentWindow.document;
		}
		return false;
	}
	
	obj.getSelection = function ( )
	{
		var doc = this.getDocument ( );
		if ( doc.selection )
			return doc.selection;
		else if ( this.iframe.contentWindow.getSelection ( ) )
			return this.iframe.contentWindow.getSelection ( );
		else if ( doc.getSelection )
			return doc.getSelection ( );
		return doc.createRange ( ).text;
	}
	
	obj.getRange = function ( )
	{
		if ( this.iframe )
		{
			var sel = this.getSelection ( );
			if ( !sel ) return false;
			try
			{
				if ( sel.getRangeAt ) return sel.getRangeAt ( 0 );
				else if ( sel.createRange )
				{
					var r = sel.createRange ( );
					return r;
				}
				else 
				{
					var r = this.getDocument ( ).createRange ( );
					if ( typeof ( sel ) == 'object' )
					{
						r.setStart ( sel.anchorNode, sel.anchorOffset );
						r.setEnd ( sel.focusNode, sel.focusOffset );
					}
					return r;
				}
			}
			catch ( e ){}
		}
		return false;
	}
	
	obj.findDomNodeByRange = function ( range )
	{
		var html = range.htmlText;
		
		// Find the containing tag
		for ( var a = 0; a < html.length; a++ )
			if ( html.substr ( a, 1 ) == '<' )
				break;
		if ( html.substr ( a, 1 ) != '<' ) return false;
		a++;
		var tag = '';
		for ( ; a < html.length; a++ )
		{
			if ( html.substr ( a, 1 ) == ' ' || html.substr ( a, 1 ) == '>' )
				break;
			tag += html.substr ( a, 1 );
		}
		if ( html.substr ( a, 1 ) != ' ' && html.substr ( a, 1 ) != '>' )
			return false;
		// Get contents of tag
		var mhtml = ''; var mode = 0;
		for ( var a = 0; a < html.length; a++ )
		{
			if ( html.substr ( a, 1 ) == '>' && mode == 0 )
			{
				mode = 1;
				continue;
			}
			if ( mode == 1 )
				mhtml += html.substr ( a, 1 );
		}
		for ( var a = mhtml.length - 1; a > 0; a-- )
		{
			if ( mhtml.substr ( a, 1 ) == '<' && !cimatch ( mhtml.substr ( a, 3 ), '<br' ) && !cimatch ( mhtml.substr ( a, 4 ), '<img' ) )
				break;
		}
		mhtml = mhtml.substr ( 0, a );
		
		// Get matching tag
		var nodes = this.getDocument ( ).getElementsByTagName ( tag );
		for ( var a = 0; a < nodes.length; a++ )
		{
			if ( nodes[ a ].offsetLeft == range.offsetLeft && nodes[ a ].offsetTop == range.offsetTop )
			{
				return nodes[ a ];
			}
		}
		return false;
	}
	
	// Get the selected node
	obj.getSelectedNode = function ( tagname )
	{
		// Get the node
		var node;
		if ( this._lastClickedItem && this._lastClickedItem.nodeName.toLowerCase ( ) != 'body' )
		{
			node = this._lastClickedItem;
		}
		else
		{
			var sel = this.getSelection ( );
			if ( sel.focusNode && sel.focusNode.nodeName.toLowerCase ( ) != 'body' )
			{
				node = sel.focusNode;
			}
			else
			{
				var range = this.getRange ( );
				if ( range.text && range.text.length )
				{
					node = this.findDomNodeByRange ( range  );
				}
			}
		}
		// Return false on no node
		if ( !node ) 
			return false;
		
		// Try to find matching tagname node
		if ( tagname && node.childNodes.length && node.nodeName.toLowerCase ( ) != tagname )
		{
			var nodes;
			if ( nodes = node.getElementsByTagName ( tagname ) )
				return nodes[ 0 ];
		}
		// Just return node
		return node;
	}
	obj.delTableRow = function ( )
	{
		var tdnode = this.getSelectedNode ( );
		if ( tdnode && ( tdnode.nodeName.toLowerCase ( ) == 'th' || tdnode.nodeName.toLowerCase ( ) == 'td' ) )
		{
			tdnode.parentNode.parentNode.removeChild ( tdnode.parentNode );
			this.removePopup ( );
		}
	}
	obj.addTableColumn = function ( varpos )
	{
		if ( !varpos ) varpos = 'before';
		var tdnode = this.getSelectedNode ( );
		var td = this.getDocument ( ).createElement ( 'td' );
		if ( varpos == 'before' )
		{
			tdnode.parentNode.insertBefore ( td, tdnode );
		}
		else
		{
			var tds = tdnode.parentNode.getElementsByTagName ( 'td' );
			for ( var a = 0; a < tds.length; a++ )
			{
				if ( tds[ a ] == tdnode && a == tds.length - 1 )
				{
					tdnode.parentNode.appendChild ( td );
					return;
				}
			}
			tdnode.parentNode.insertBefore ( td, tds[ tds.length - 1 ] );
		}
	}
	obj.addTableRow = function ( varpos )
	{
		if ( !varpos ) varpos = 'before';
		var tdnode = this.getSelectedNode ( );
		var tr = this.getDocument ( ).createElement ( 'tr' );
		var rows = tdnode.parentNode.childNodes.length;
		var tds = 0;
		for ( var a = 0; a < rows; a++ )
			if ( tdnode.parentNode.childNodes[ a ].nodeName == tdnode.nodeName )
				tds++;
		if ( !tds ) return;
		for ( var a = 0; a < tds; a++ )
		{
			var td = this.getDocument ( ).createElement ( tdnode.nodeName );
			td.innerHTML = '&nbsp;';
			tr.appendChild ( td );
		}
		if ( varpos == 'before' )
			tdnode.parentNode.parentNode.insertBefore ( tr, tdnode.parentNode );
		else
		{
			if ( tdnode.parentNode.nextSibling )
				tdnode.parentNode.parentNode.insertBefore ( tr, tdnode.parentNode.nextSibling );
			else tdnode.parentNode.parentNode.appendChild ( tr );
		}
	}
		
	// Check what we have been selecting!	
	obj.checkSelection = function ( )
	{
		texteditor.activeEditorId = this.area.id;
		
		var sel = this.getSelection ( );
		if ( !sel ) 
			return false;
		var rng = this.getRange ( );
		if ( !rng ) return false;
		
		var childNodes; // <- selected nodes
		
		// Get selected node if any
		var node = this.getSelectedNode ( );
		var specialObject = node ? ( node.nodeName.toLowerCase () == 'img' || node.nodeName.toLowerCase () == 'div' ) : false;
		
		// If we haven't selected any range (end/start offset is same)
		if ( rng.startOffset == rng.endOffset )
		{
			if ( node )
				childNodes = Array ( node );
			else if ( this._lastClickedItem )
				childNodes = Array ( this._lastClickedItem );
			else return false;
		}
		// else if we have a "single tag" element (then we have range)
		else if ( node && specialObject )
		{
			childNodes = Array ( node );
		}
		// We have selected a range
		else
		{
			var cnt = rng.cloneContents ( );
			if ( !cnt ) return false;
			childNodes = new Array ( );
			for ( var b = 0; b < cnt.childNodes.length; b++ )
				childNodes.push ( cnt.childNodes[ b ] );
		}
		if ( !childNodes ) return;
		
		// Sometimes we have a link inside a h1 or similar, then keep
		// the parent relevant
		if ( 
			node.parentNode && node.parentNode.nodeName.toLowerCase ( ) != 'body' &&  
			(
				node.nodeName.toLowerCase ( ) == 'a' ||
				node.nodeName.toLowerCase ( ) == 'span'
			)
		)
		{
			childNodes.push ( node.parentNode );
		}
		// Sometimes, we have a span, with an i, inside an A, then we need that
		// one too
		if ( 
			node.parentNode && node.parentNode.parentNode &&
			node.parentNode.parentNode.nodeName.toLowerCase () == 'a' 
		)
		{
			childNodes.push ( node.parentNode.parentNode );
		}
		
		// Reset all
		this.buttonOff ( this.toolbarButtons.bBold );
		this.buttonOff ( this.toolbarButtons.bUnderline );
		this.buttonOff ( this.toolbarButtons.bItalic );
		this.buttonOff ( this.toolbarButtons.bAlignLeft );
		this.buttonOff ( this.toolbarButtons.bAlignRight );
		this.buttonOff ( this.toolbarButtons.bAlignCenter );
		this.buttonOff ( this.toolbarButtons.bAlignJustify );
		this.buttonOff ( this.toolbarButtons.bLink );
		this.buttonOff ( this.toolbarButtons.bSub );
		this.buttonOff ( this.toolbarButtons.bSuper );
		this.viewFormatting ( 0 );
		this.viewFontFamily ( 0 );
		this.viewClass ( 0 );

		// Se if there are more than one node type
		var nodeTypeCount = 1;
		var nodeType = childNodes[0].nodeName;
		for ( var a = 0; a < childNodes.length; a++ )
			if ( nodeType != childNodes[a].nodeName )
				nodeTypeCount = 2;
				
		// Activate buttons
		for ( var a = 0; a < childNodes.length; a++ )
		{
			var cn = childNodes[ a ];
			var nn = cn.nodeName.toLowerCase ( );
			if ( nn == '#text' && cn.parentNode != this.getDocument().body )
			{
				cn = cn.parentNode;
				nn = cn.nodeName.toLowerCase ();
			}
			if ( a == 0 ) this._lastCursorItem = cn;
			
			this.activateFormattingButtonOn ( cn, childNodes.length );
			
			// Special case for a tags
			if ( node && node.parentNode && node.parentNode.nodeName.toLowerCase () == 'a' )
				this.buttonOn ( this.toolbarButtons.bLink );
			
			if ( !cn )
				return;
				
			// Alignment and float options
			switch ( getStyle ( cn, 'float' ) )
			{
				case 'none':
					break;
				case 'left':
					this.buttonOn ( this.toolbarButtons.bAlignLeft );
					break;
				case 'right':
					this.buttonOn ( this.toolbarButtons.bAlignRight );
					break;	
				default:
					if ( cn.align && cn.align == 'left' )
						this.buttonOn ( this.toolbarButtons.bAlignLeft );
					else if ( cn.align && cn.align == 'right' )
						this.buttonOn ( this.toolbarButtons.bAlignLeft );
					break;
			}
			if ( nn == 'img' )
			{
				// If we have document fragment "bug"
				if ( cn.parentNode && cn.parentNode.nodeName.toLowerCase ( ) == '#document-fragment' )
				{
					var l = this._lastClickedItem;
					if ( l && l.parentNode && l.parentNode.nodeName.toLowerCase () != '#document-fragment' )		
					{
						cn = l.parentNode;
						nn = cn.nodeName.toLowerCase ( );
					}
				}
				if ( this.toolbarButtons.bAlignRight.on || this.toolbarButtons.bAlignLeft.on )
					return;
			}
			
			if ( nn )
			{
				switch ( getStyle ( cn, 'text-align' ) )
				{
					case 'none':
						break;
					case 'start':
					case '-moz-left':
					case 'left':
						this.buttonOn ( this.toolbarButtons.bAlignLeft );
						break;
					case '-moz-right':
					case 'right':
						this.buttonOn ( this.toolbarButtons.bAlignRight );
						break;	
					case '-moz-justify':
					case 'justify':
						this.buttonOn ( this.toolbarButtons.bAlignJustify );
						break;	
					case 'center':
						this.buttonOn ( this.toolbarButtons.bAlignCenter );
						break;
					default:
						break;
				}
			}
		
			if ( cn.style && cn.style.fontFamily )
			{
				this.viewFontFamily ( cn.style.fontFamily );
			}
			if ( cn.className )
			{
				this.viewClass ( cn.className );
			}
		}
	}
	obj.activateFormattingButtonOn = function ( node, nodeTypeCount )
	{
		if ( !node ) return false;
		var nn = node.nodeName.toLowerCase ();
		switch ( nn )
		{
			case '#text':
				node = false;
				break;
			case 'span':
				var fw = getStyle ( node, 'font-weight' );
				if ( typeof ( fw ) == 'number' && fw >= 700 )
					this.buttonOn ( this.toolbarButtons.bBold );
				else if ( typeof ( fw ) == 'string' && fw.indexOf ( 'bold' ) >= 0 )
					this.buttonOn ( this.toolbarButtons.bBold );
				if ( getStyle ( node, 'text-decoration' ).indexOf ( 'underline' ) >= 0 )
					this.buttonOn ( this.toolbarButtons.bUnderline );
				if ( getStyle ( node, 'font-style' ).indexOf ( 'italic' ) >= 0 )
					this.buttonOn ( this.toolbarButtons.bItalic );
				if ( getStyle ( node, 'font-style' ).indexOf ( 'opaque' ) >= 0 )
					this.buttonOn ( this.toolbarButtons.bItalic );
				break;
			case 'strong':
			case 'b':
				this.buttonOn ( this.toolbarButtons.bBold );
				break;
			case 'u':
				this.buttonOn ( this.toolbarButtons.bUnderline );
				break;
			case 'i':
				this.buttonOn ( this.toolbarButtons.bItalic ); 
				break;
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'p':
			case 'pre':
				if ( nodeTypeCount > 1 )
					this.viewFormatting ( 0 );
				else this.viewFormatting ( nn, node.className == 'Standard' ? node : false );
				break;
			case 'a':
				this.buttonOn ( this.toolbarButtons.bLink );
				break;
			case 'sup':
				this.buttonOn ( this.toolbarButtons.bSuper );
				break;
			case 'sub':
				this.buttonOn ( this.toolbarButtons.bSub );
				break;
			default: 
				break;
		}
		// Check container tag
		if ( node && node.parentNode != document.body && node.parentNode )
		{
			this.activateFormattingButtonOn ( node.parentNode );
		}
	}
	obj.showProperties = function ( parentnode )
	{
		this.removePopup ( );
		var node = parentnode ? this._propertiesParentNode : this._propertiesNode;
		this._propertiesNodeType = parentnode ? 'parentnode' : 'node';
		var nt = this.getLiteralNodename ( node.nodeName );
		initModalDialogue ( 'elementproperties', 720, 380, 'admin.php?pluginaction=properties&plugin=texteditor' );
	}
	obj.removeArenaForm = function ( )
	{
		alert ( 'Kommer snart!' );
	}
	obj.editArenaForm = function ( )
	{
		alert ( 'Kommer snart!' );
	}
	obj.getLiteralNodename = function ( nodeName )
	{
		var nn = '';
		switch ( nodeName.toLowerCase ( ) )
		{
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6':		nn = 'overskrift'; break;
			case 'table':   nn = 'tabell'; break;
			case 'th':		nn = 'tabell header felt'; break;
			case 'td':		nn = 'tabell felt'; break;
			case 'a':		nn = 'lenke'; break;
			case 'img':		nn = 'bilde'; break;
			case 'li':		nn = 'liste element'; break;
			case 'ul':		nn = 'unummerert liste'; break;
			case 'ol':		nn = 'nummerert liste'; break;
			case 'hr':		nn = 'horisontal linje'; break;
			case 'p':		nn = 'paragraf'; break;
			default:		nn = 'element';	break;
		}
		return nn;
	}
	obj.resizeToFit = function ()
	{
		var height;
		
		// brTest to find absolute height
		var brTest = document.createElement ( 'div' );
		brTest.style.clear = 'both';
		
		this.getDocument ().body.appendChild ( brTest );
		
		if ( !this.minHeight )
		{
			this.minHeight = parseInt ( this.iframe.style.height );
		} 
		
		var eles = this.getDocument().body.getElementsByTagName ( '*' );
		var max = 0; 
		var b = 0;
		for ( var a = eles.length - 1; a >= 0; a--, b++ )
		{
			var t = eles[a].offsetHeight + eles[a].offsetTop;
			if ( t > max ) max = t;
			if ( b > 10 ) break;
		}
		height = max;
		if ( height > this.minHeight )
		{
			this.iframe.style.height = ( height + 30 ) + 'px';
			if ( this.iframe.contentWindow )
				this.iframe.contentWindow.scrollTo ( 0, 0 );
			else this.iframe.scrollTo ( 0, 0 );
		}
		
		// Special case, cleanup
		brTest.parentNode.removeChild ( brTest );
	}
	
	obj.getIframeScrollXY = function ( )
	{
		if ( this.iframe.contentWindow && this.iframe.contentWindow.scrollY )
		{
			return Array ( this.iframe.contentWindow.scrollX, this.iframe.contentWindow.scrollY );
		}
		return Array ( this.iframe.contentWindow.document.documentElement.scrollLeft, this.iframe.contentWindow.document.documentElement.scrollTop );
	}
	obj.showPopupMenu = function ( )
	{
		if ( !document.getElementById ( 'TopLevelContainer' ) ) return;
		if ( this.popupMenu )
		{
			if ( this.popupMenu.parentNode )
			{
				this.popupMenu.parentNode.removeChild ( this.popupMenu );
				this.popupMenu = false;
			}
		}
		this.popupMenu = this.getDocument ( ).createElement ( 'div' );
		var pm = this.popupMenu;
		pm.className = 'ArenaEditorPopupMenu';
		pm.style.position = 'absolute';
		pm.style.zIndex = 9999;
		
		pm.id = 'ArenaEditorPopupMenuDiv';
		
		var anode = this.getSelectedNode ( 'a' );
		var inode = this.getSelectedNode ( 'img' );
		var tnode = this.getSelectedNode ( 'table' );
		var node = this.getSelectedNode ( );
		
		// Generate menu html--
		var menuhtml = '';
		var firstlink = '';
		
		// Menu header
		menuhtml += '<li><strong>Tekstfelt alternativer:</strong></li>';
		if ( node )
		{
			this._propertiesNode = node;
			var nn = this.getLiteralNodename ( node.nodeName );
			if ( node.nodeName.toLowerCase ( ) == 'img' )
			{
				menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].insertImage(); texteditor.editors[' + this.index + '].removePopup();"><img src="admin/gfx/icons/image_edit.png"/>Endre bilde</a></li>';
				if ( !node.parentNode || node.parentNode.nodeName.toLowerCase ( ) != 'p' )
				{
					menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].encapsulateElement( \'p\' ); texteditor.editors[' + this.index + '].removePopup();"><img src="admin/gfx/icons/application_get.png"/>Lag paragraf for bildet</a></li>';
				}
				if ( node.parentNode && node.parentNode.nodeName.toLowerCase ( ) == 'a' )
				{
					menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].createLink(); texteditor.editors[' + this.index + '].removePopup();"><img src="admin/gfx/icons/link_edit.png"/>Endre lenke</a></li>';
				}
			}
			else if ( node.nodeName.toLowerCase ( ) == 'a' )
			{
				menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].createLink(); texteditor.editors[' + this.index + '].removePopup();"><img src="admin/gfx/icons/link_edit.png"/>Endre lenke</a></li>';
			}
			// Properties
			menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].showProperties ( );"><img src="admin/gfx/icons/wrench.png"/>Egenskaper for ' + nn + '</a></li>';
		}
		else return false;
		
		// Can only make an image when the selected is not an image
		if ( node.nodeName.toLowerCase ( ) != 'img' )
			menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].insertImage(); texteditor.editors[' + this.index + '].removePopup();"><img src="admin/gfx/icons/image_add.png"/>Sett inn bilde</a></li>';
		// Can only make link when the selected is not a link
		if ( node.nodeName.toLowerCase ( ) != 'a' && ( node.parentNode && node.parentNode.nodeName.toLowerCase () != 'a' ) )
			menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].createLink(); texteditor.editors[' + this.index + '].removePopup();"><img src="admin/gfx/icons/link_add.png"/>Lag lenke</a></li>';
		
		// Create a table link
		menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].insertTable(); texteditor.editors[' + this.index + '].removePopup();"><img src="admin/gfx/icons/table_add.png"/>Sett inn tabell</a></li>';
		
		// Check if there is an arena form
		if ( this.getContent ( ).indexOf ( 'arenaform' ) >= 0 )
		{
			menuhtml += '<li><strong>Skjema funksjoner:</strong></li>';
			menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].removeArenaForm ( );"><img src="admin/gfx/icons/page_white_lightning.png"/>Fjern skjema</a></li>';
			menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].editArenaForm ( );"><img src="admin/gfx/icons/page_white_text.png"/>Endre skjema</a></li>';
		}
		
		// exception blocks
		if ( node )
		{
			 this._propertiesParentNode = false;
			if ( node.nodeName.toLowerCase ( ) == 'td' || node.nodeName.toLowerCase ( ) == 'th' )
			{
				menuhtml += '<li><strong>Tabell funksjoner:</strong></li>';
				menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].showProperties ( \'parentnode\' );"><img src="admin/gfx/icons/wrench.png"/>Egenskaper for tabell</a></li>';
				menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].addTableRow ( \'before\' );"><img src="admin/gfx/icons/table_row_insert.png"/>Sett inn rad før</a></li>';
				menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].addTableRow ( \'after\' );"><img src="admin/gfx/icons/table_row_insert.png"/>Sett inn rad etter</a></li>'
				menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].addTableColumn ( \'before\' );"><img src="admin/gfx/icons/table_row_insert.png"/>Sett inn kolonne før</a></li>';
				menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].addTableColumn ( \'after\' );"><img src="admin/gfx/icons/table_row_insert.png"/>Sett inn kolonne etter</a></li>'
				menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].delTableRow ( );"><img src="admin/gfx/icons/table_row_delete.png"/>Fjern rad</a></li>';;
				this._propertiesParentNode = node;
				while ( this._propertiesParentNode.nodeName.toLowerCase ( ) != 'table' )
					this._propertiesParentNode = this._propertiesParentNode.parentNode;
			}
			else
			{
				
				if ( node.parentNode != this.getDocument ().body )
				{
					menuhtml += '<li><strong>Overliggende element</strong></li>';
					var n = node.parentNode.nodeName.toLowerCase ( );
					menuhtml += '<li><a href="javascript: texteditor.editors[' + this.index + '].showProperties ( \'parentnode\' );"><img src="admin/gfx/icons/wrench.png"/>Egenskaper for ' + ( this.getLiteralNodename ( node.parentNode.nodeName ) ) + '</a></li>';
					this._propertiesParentNode = node.parentNode;
				}
			}
		}
		
		pm.innerHTML = '<ul>' + menuhtml + '</ul>';
		// Done with menu html--
		
		// Add scroll to coords
		var py = ( mousey - 30 ) - this.getIframeScrollXY ( )[1];
		var px = ( mousex - 10 ) - this.getIframeScrollXY ( )[0];
		
		// Needs this on ie 7...
		if ( isIE )
		{
			px += getElementLeft ( this.iframe );
			py += getElementTop ( this.iframe );
		}
		
		pm.style.top = py + 'px';
		pm.style.left = px + 'px';
		pm.iframe = this.iframe;
		pm.ed = this;
		// Make sure the popup is not outside the viewing area
		pm.checkPosition = function ()
		{
			var y1 = getElementTop ( this );
			var x1 = getElementLeft ( this );
			var x2 = x1 + getElementWidth ( this );
			var y2 = y1 + getElementHeight ( this );
			var scroll = getScrollTop();
			var iframeOffset = getElementTop ( this.iframe );
			var menuTop = y1 - scroll;
			var menuHeight = getElementHeight ( this );
			var menuBottom = menuHeight + menuTop;
			var pWindowHeight = getElementHeight ( parent.window );
			if ( menuBottom > pWindowHeight )
			{
				var curry = parseInt ( this.style.top );
				curry += pWindowHeight - menuBottom;
				this.style.top = curry + 'px';
			}
		}
			
		if ( !document._editorpopupmenumovefunc )
		{
			addEventTo ( this.getDocument ( ), 'mousemove', function ( )
			{
				var pm = document.getElementById ( 'ArenaEditorPopupMenuDiv' );
				if ( pm )
				{
					var y1 = getElementTop ( pm );
					var x1 = getElementLeft ( pm );
					var x2 = x1 + getElementWidth ( pm );
					var y2 = y1 + getElementHeight ( pm );
					pm.checkPosition();
					if ( mousex < x1 || mousex >= x2 || mousey < y1 || mousey >= y2 )
					{
						pm.parentNode.removeChild ( pm );
						obj.popupMenu = false;
					}
				}
			} );
		}
		if ( isIE )
			pm.oncontextmenu = function ( ) { return false; }
		document.getElementById ( 'TopLevelContainer' ).appendChild ( pm );
		pm.checkPosition();
	}
	obj.removePopup = function ( )
	{
		this.popupMenu.parentNode.removeChild ( this.popupMenu );
		this.popupMenu = false;
	}
	obj.buttonOn = function ( btn )
	{
		if ( !btn ) return;
		btn.style.background = '#74ACE5';
		btn.on = true;
	}
	obj.buttonOff = function ( btn )
	{
		if ( !btn ) return;
		btn.style.background = '';
		btn.on = false;
	}
	// Store a range object on the iframe for later use
	obj.storeLocalRangeObject = function ()
	{
		if ( isIE )
			this.localRangeObject = this.getDocument ().selection.createRange ();
	}
	// Keyboard shortcuts
	obj.checkKeyboardShortcut = function ( event )
	{
		var key = event.which ? event.which : event.keyCode;
		if ( event.ctrlKey )
		{
			switch ( key )
			{
				// 'z'
				/*case 90:
					if ( navigator.userAgent.indexOf ( 'Firefox' ) >= 0 ) 
						return false;
					return this.undo ();
				// 'y'
				case 89:
					if ( navigator.userAgent.indexOf ( 'Firefox' ) >= 0 )
						return false;
					return this.redo ();*/
			}
		}
		else
		{
			switch ( key )
			{
				// tab
				case 9:
					if ( event.shiftKey )
						this.outdent ();
					else this.indent ();
					break;
			}
		}
		return false;
	}
	// Setup events on the iframe so we can add good functionality
	obj.activateEvents = function ( fr )
	{
		var doc = this.getDocument ( );
		addEventTo ( doc, 'keydown', function ( event )
			{
				// Remember changes
				obj.rememberContent ();
				
				// Let the system take care of what to do
				if ( obj.checkKeyboardShortcut ( event ) )
				{
					event.cancelBubble = true;
					if ( event.stopPropagation ) event.stopPropagation ( );
					return false;
				}
			}
		);
		addEventTo ( doc, 'mouseover', function ( event )
			{
				obj.rememberContent ();
			}
		);
		addEventTo ( doc, 'keydown', function ( event )
			{
				var b = obj.getDocument().body;
				// Remove if the only thing that is in the html field is a line break!
				if ( b.innerHTML.split ( ' ' ).join ( '' ).toLowerCase () == '<br/>' )
					b.innerHTML = '';
				if ( b.innerHTML.split ( ' ' ).join ( '' ).toLowerCase () == '<br>' )
					b.innerHTML = '';
			}
		);
		addEventTo ( doc, 'keyup', function ( event )
			{
				obj.storeLocalRangeObject ( );
				
				// Check for pasting
				var key = event.which ? event.which : event.keyCode;
				switch ( key )
				{
					case 86: 
						if ( event.ctrlKey && event.shiftKey ) 
							obj.cleanBadHTML ();
						break;
					default:
						break;
				}
				// Remember changes
				obj.rememberContent ();
				obj._lastClickedItem = false;
				obj._lastCursorItem = false;
				if ( !obj._sourceview )
					obj.area.value = obj.getContent ( );
				obj.checkSelection ( );
			}
		);
		addEventTo ( doc, 'focus', function ( event )
			{
				obj.texteditor.activeEditorId = obj.area.id;
			}
		);
		addEventTo ( doc, 'contextmenu', function ( e )
			{
				if ( !e ) e = window.event;
				e.cancelBubble = true;
				if ( e.stopPropagation ) e.stopPropagation ( );
				if ( e.preventDefault ) e.preventDefault ( );
				return false
			}
		);
		addEventTo ( doc, 'mousedown', function ( e )
			{
				obj.storeLocalRangeObject ( );
				if ( !e ) e = window.event;
				if ( e.button == 2 )
				{
					window.oncontextmenu = function () { return false; }
				}
				obj.resizeToFit ();
			}
		);
		addEventTo ( doc, 'mouseup', function ( e )
			{
				obj.storeLocalRangeObject ( );
				if ( !e ) e = window.event;
				// Register clicked item
				if ( e.target )
					obj._lastClickedItem = e.target;
				else if ( e.srcElement )
					obj._lastClickedItem = e.srcElement;
				obj.checkSelection ( );
				
				obj.clickX = isIE ? mousex : ( mousex - getElementLeft ( obj.iframe ) );
				obj.clickY = isIE ? mousey : ( mousey - getElementTop ( obj.iframe ) );
				
				// Right mouse key or left with ctrl (for mac)
				if ( e.button == 2 || ( e.button == 0 && e.ctrlKey ) )
				{
					obj.showPopupMenu ( );
				}
			}
		);
		addEventTo ( doc, 'mousemove', function ( e )
			{				
				if ( !e ) e = window.event;
				var posx = 0;
				var posy = 0;
				if ( !e ) 
				{
					if ( fr.document )
						e = fr.document.event;
					else e = fr.contentWindow.event;
				}

				if ( e.pageX || e.pageY )
				{
					mousex = e.pageX;
					mousey = e.pageY;
				}
				else if ( e.clientX || e.clientY )
				{
					mousex = e.clientX;
					mousey = e.clientY;
				}
				if ( typeof ( getElementTop ) != 'undefined' )
				{
					mousex += getElementLeft ( fr );
					mousey += getElementTop ( fr );
				}
			}
		);
		addEventTo ( doc, 'selectstart', function ( even ) 
			{
				obj.checkSelection ( );
			}
		);
		addEventTo ( doc, 'selectstop', function ( e )
			{
				if ( !e ) e = window.event;
				obj._lastClickedItem = false;
				obj.checkSelection ( );
			}
		);
		addEventTo ( doc, 'blur', function ( event )
			{
				if ( !obj._sourceview )
				{
					obj.correctHTML ( obj.getContent () );
					obj.area.value = obj.getContent ( );
				}
			}
		);
		if ( ieMode && this.stylesheetSrc )
		{	
			var link = this.getDocument ( ).createElement ( 'link' );
			link.rel = 'stylesheet';
			link.href = this.stylesheetSrc;
			this.getDocument ( ).getElementsByTagName ( 'head' )[ 0 ].appendChild ( link );
		}
	}
	
	// Set content first time
	var jax = new bajax ( );
	jax.openUrl ( 'lib/templates/texteditor.php', 'post', true );
	if ( obj.stylesheetSrc )
	{
		var t = new Date ();
		jax.addVar ( 'extra', '<link rel="stylesheet" href="' + obj.stylesheetSrc + '?rand=' + (t.getTime()+(Math.random()*10000000)+(Math.random()*10000000)) + '"/>' );
	}
	jax.obj = obj;
	jax.onload = function ( )
	{
		this.obj.getDocument ( ).designMode = 'off';
		var cnt = this.getResponseText ( );
		if ( cnt.indexOf ( 'Laster inn...' ) )
			cnt = this.getResponseText ( ).split ( 'Laster inn...' ).join ( this.obj.area.value );
		else cnt = this.getResponseText ( );
		this.obj.setContent ( cnt );
		this.obj.getDocument ( ).designMode = 'on';
		this.obj.getDocument ( ).oncontextmenu = new Function ( "return false;" );
		if ( !obj.getDocument ().body )
		{
			setTimeout ( 'texteditor.editors[' + obj.index + '].resetUndoBuffer()', 100 );
		}
		else
		{
			this.obj.resetUndoBuffer ();
		}
		
		// Force clean HTML!
		try 
		{
			this.obj.getDocument().execCommand( 'styleWithCSS', 0, false);
		} 
		catch ( e )
		{
			try
			{
				Editor.execCommand ( 'useCSS', 0, true );
			} 
			catch ( e ) 
			{
				try 
				{
					Editor.execCommand( 'styleWithCSS', false, false );
				}
				catch (e) {}
			}
		}
	}
	jax.send ( );
	
	return obj ? obj : false;
}

// Get an editor instance
texteditor.get = function ( texteditorid )
{
	for ( var a = 0; a < this.editors.length; a++ )
	{
		if ( this.editors[ a ].area.id == texteditorid )
			return this.editors[ a ];
	}
	if ( typeof ( CodePress ) != 'undefined' )
	{
		texteditorid += '_cp';
		for ( var a = 0; a < this.editors.length; a++ )
		{
			if ( this.editors[ a ].area.id == texteditorid )
				return this.editors[ a ];
		}
	}
	return null;
}

texteditor.removeControl = function ( texteditorid )
{
	for ( var a = 0; a < this.editors.length; a++ )
	{
		if ( this.editors[ a ].area.id == texteditorid )
		{
			this.editors[ a ].removeControl ( );
			var out = new Array ( );
			for ( var b = 0; b < this.editors.length; b++ )
			{
				if ( b != a )
				{
					this.editors[ b ].index = b;
					out.push ( this.editors[ b ] );
				}
			}
			this.editors = out;
			return true;
		}
	}
	return false;
}

/* Helper functions ***********************************************************/

function loadArenaFiles ( )
{
	document.loadjax = new bajax ( );
	document.loadjax.openUrl ( 'admin.php?plugin=library&pluginaction=getfiles&folder=' + document.getElementById ( 'link__Folders' ).value, 'get', true );
	document.loadjax.onload = function ( )
	{
		document.getElementById ( 'Arena__Images' ).innerHTML = this.getResponseText ( );
		document.loadjax = '';
	}
	document.loadjax.send ( );
}

var insertLink__prevSelected = false;
function insertLink ( )
{
	initTabSystem ( 'LinkTabs' );
	// Find texteditor
	var ed = texteditor.get ( texteditor.activeEditorId );
	if ( !ed ) 
	{
		alert ( 'Kunne ikke finne tekst behandleren!' );
		return false;
	}
	// Get link node
	if ( ed.linkMode == 'bookmark' )
	{
		// do nothing
	}
	else
	{
		var node = ed.linkNode;
		if ( 
			node && node.nodeName.toLowerCase ( ) != 'a' && 
			node.parentNode && node.parentNode.nodeName.toLowerCase ( ) == 'a' 
		)
		{
			node = node.parentNode;
			ed.linkNode = node;
		}
		if ( node && node.nodeName.toLowerCase ( ) == 'a' )
		{
			// Link target
			var sel = document.getElementById ( 'link__Target' ); 
			for ( var a = 0; a < sel.options.length; a++ ) 
			{
				if ( sel.options[ a ].value == node.target ) 
					sel.options[ a ].selected = 'selected';
				else sel.options[ a ].selected = '';
			}
		
			// Others
			var aname = node.getAttribute ( 'name' );
			var ahref = node.getAttribute ( 'href' );
			var aonclick = node.getAttribute ( 'onclick' );
			var aclass = node.getAttribute ( 'class' );
			var atitle = node.getAttribute ( 'title' );
			var atarget = node.getAttribute ( 'target' );
			document.getElementById ( 'link__Name' ).value = aname ? aname : '';
			if ( ahref.indexOf ( 'mailto:' ) >= 0 )
				document.getElementById ( 'link__Email' ).value = ahref ? ahref.split('mailto:').join ('') : '';
			else document.getElementById ( 'link__Url' ).value = ahref ? ahref : '';
			document.getElementById ( 'link__Onclick' ).value = aonclick ? aonclick : '';
			document.getElementById ( 'link__Class' ).value = aclass ? aclass : '';
			document.getElementById ( 'link__Title' ).value = atitle ? atitle : '';
			document.getElementById ( 'link__Target' ).value = atarget ? atarget : '';
			
			// It's an email addy
			if ( ahref.indexOf ( 'mailto:' ) >= 0 )
			{
				activateTab ( 'tabEmailUrl' );
			}
			// Test for arena webpage link from pulldown
			else
			{
				var eles = document.getElementById ( 'link__Arena' ).options;
				for ( var a = 0; a < eles.length; a++ )
				{
					if ( eles[ a ].value == ahref )
					{
						eles[ a ].selected = 'selected';
						document.getElementById ( 'link__Url' ).value = '';
						activateTab ( 'tabArenaUrl' );
					}
					else 
					{
						eles[ a ].selected = '';
					}
				}
			}
		}
	}
	document.getElementById ( 'LinkEditDone' ).onclick = function ( )
	{
		// Find texteditor
		var ed = texteditor.get ( texteditor.activeEditorId );

		// Check if we've selected a link
		if ( 
			document.getElementById ( 'link__Url' ).value.length < 1 && 
			document.getElementById ( 'link__Arena' ).value.length < 1 && 
			document.getElementById ( 'link__Email' ).value.length < 1 
		)
		{
			alert ( 'Du må fylle ut lenke adresse.' );
			document.getElementById ( 'link__Url' ).focus ( );
		}
		// if we have, then do it
		else
		{	
			var link;
			if ( document.getElementById ( 'tabArenaUrl' ).className == 'tabActive' )
			{
				link = document.getElementById ( 'link__Arena' ).value
			}
			else if ( document.getElementById ( 'tabNormalUrl' ).className == 'tabActive' ) 
			{
				link = document.getElementById ( 'link__Url' ).value;
			}
			else if ( document.getElementById ( 'tabEmailUrl' ).className == 'tabActive' ) 
			{
				link = document.getElementById ( 'link__Email' ).value;
				if ( link.indexOf ( 'mailto:' ) < 0 )
					link = 'mailto:' + link;
			}
			
			// Bookmark mode - we selected some text abitrarily	
			if ( ed.linkMode == 'bookmark' )
			{
				ed.linkMode = false;
				ed.restoreSelection ();
				ed.iframe.focus ();
				if ( isIE )
				{
					return ge ( 'LinkEditDone' ).onclick();
				}
				// Create link the safe way
				var tmp = ed.createTempNode ();
				var an  = document.createElement ( 'a' );
				an.innerHTML = tmp.innerHTML;
				an.href = link;
				tmp.parentNode.replaceChild ( an, tmp );
				if( tmp.className ) an.className = tmp.className;
				ed.linkNode = an;
				node = ed.linkNode;
			}
			else node = ed.linkNode;
			if ( ed.linkNode && ed.linkNode.nodeName.toLowerCase() == '#text' )
			{
				var nod = ed.getDocument().createElement ( 'a' );
				nod.innerHTML = ed.linkNode.nodeValue;
				nod.href = link;
				ed.linkNode.parentNode.replaceChild ( nod, ed.linkNode );
				node = nod;
			}
			else if ( ieMode && node.nodeName && node.nodeName.toLowerCase ( ) == 'img' ) 
			{	
				var a = ed.getDocument ( ).createElement ( 'a' );
				a.href = link;
				node.parentNode.insertBefore ( a, node );
				node.parentNode.removeChild ( node );
				a.appendChild ( node );
				node = a;
			}
			else if ( node && node.nodeName.toLowerCase ( ) == 'img' )
			{
				var a = ed.getDocument ( ).createElement ( 'a' );
				var img = node;
				node.parentNode.replaceChild ( a, img );
				a.appendChild ( img );
				node = a;
				a.href = link;
			}
			else if ( node && node.nodeName.toLowerCase () == 'a' )
			{
				node.href = link;
			}
			else if ( !node )
			{
				ed.getDocument( ).execCommand ( 'createlink', false, link );
				node = ed.getSelectedNode ( 'a' );
			}
		
			if ( !node ) 
			{
				ed.insertHTML ( '<a href="' + link + '">' + link + '</a>' );
				removeModalDialogue( 'link_dialogue' );
				return false;
			}
				
			if ( node.nodeName == 'IMG' )
				node = node.parentNode;
			if ( node.nodeName != 'A' )
			{
				if( node.nodeName == 'SPAN' )
				{
					var a = document.createElement( 'a' );
					node.parentNode.replaceChild( a, node );
					a.innerHTML = node.innerHTML;
					if( node.className ) a.className = node.className;
					node = a;
				}
				else node = new Object ( );
			}
		
			// Update link
			if ( node.href )
			{
				node.setAttribute ( 'href', link );
				node.setAttribute ( 'name', document.getElementById ( 'link__Name' ).value );
				node.setAttribute ( 'onclick', document.getElementById ( 'link__Onclick' ).value );
				node.setAttribute ( 'class', document.getElementById ( 'link__Class' ).value );
				node.setAttribute ( 'title', document.getElementById ( 'link__Title' ).value );
				node.setAttribute ( 'target', document.getElementById ( 'link__Target' ).value );
			}
		
			// Update real textarea
			removeModalDialogue ( 'link_dialogue' );
			ed.linkNode = false;
			ed.linkMode = false;
			ed.area.value = ed.getContent ( );
		}
	}
	if ( document.getElementById ( 'link__Email' ).value.length )
	{
		activateTab ( 'tabEmailUrl' );
	}
	else if ( !document.getElementById ( 'link__Url' ).value && document.getElementById ( 'link__Arena' ).value.length )
	{
		activateTab ( 'tabArenaUrl' );	
	}
	else
	{
		activateTab ( 'tabNormalUrl' );	
		document.getElementById ( 'link__Url' ).focus ( );					
	}
}
function setupGalleryForm ( )
{
	
}
function insertGallery ( )
{
	texteditor.get ( texteditor.activeEditorId ).insertHTML ( document.getElementById ( 'GalleryPreview' ).innerHTML );
	removeModalDialogue ( 'gallery' );
}
function fetchGalleryPreview ( gid )
{
	var gjax = new bajax ( );
	gjax.openUrl ( 'admin.php?plugin=texteditor&pluginaction=generategallery&ajax=true&gid=' + gid, 'post', true );
	gjax.addVar ( 'columns', document.getElementById ( 'galColumns' ).value );
	gjax.addVar ( 'scalemode', document.getElementById ( 'galScalemode' ).value );
	gjax.addVar ( 'thumbwidth', document.getElementById ( 'galThumbWidth' ).value );
	gjax.addVar ( 'thumbheight', document.getElementById ( 'galThumbHeight' ).value );
	gjax.addVar ( 'bigwidth', document.getElementById ( 'galImageWidth' ).value );
	gjax.addVar ( 'bigheight', document.getElementById ( 'galImageHeight' ).value );
	gjax.addVar ( 'showtitles', document.getElementById ( 'galShowTitles' ).checked ? 1 : 0 );
	gjax.addVar ( 'showdesc', document.getElementById ( 'galShowDesc' ).checked ? 1 : 0 );
	gjax.onload = function ( )
	{
		document.getElementById ( 'GalleryPreview' ).innerHTML = this.getResponseText ();
	}
	gjax.send ( );
}

