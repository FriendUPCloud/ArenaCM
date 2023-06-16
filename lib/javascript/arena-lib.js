

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



/***********************************************************\
|                                                           |
|          - ARENA v2 Content Management System -           |
|             Copyright (c) 2004-2009 Blest AS              |
|                  E-mail: blest@blest.no                   |
|                 Tel: (+0047) 99 37 77 88                  |
|                 Web: http://www.blest.no                  |
|                                                           |
\***********************************************************/

var mousex = 0, mousey = 0;
var isOpera = ( navigator.userAgent.indexOf ( 'Opera' ) != -1 );
var isIE = ( !isOpera && navigator.userAgent.indexOf ( 'MSIE' ) != -1 );
var isIE6 = ( !isOpera && navigator.userAgent.indexOf ( 'MSIE 6' ) != -1 );
var isIE7 = ( !isOpera && navigator.userAgent.indexOf ( 'MSIE 7' ) != -1 );
var isIE8 = ( !isOpera && navigator.userAgent.indexOf ( 'MSIE 8' ) != -1 );
var isIE9 = ( !isOpera && navigator.userAgent.indexOf ( 'MSIE 9' ) != -1 );
var isMozilla = ( navigator.userAgent.indexOf ( 'Gecko' ) > 0 );
var isSafari = ( navigator.userAgent.indexOf ( 'Safari' ) > 0 );
var isKonqueror = ( navigator.userAgent.indexOf ( 'Konqueror' ) > 0 );

// Expose HTMLElement for prototyping in Safari and Konqueror
if ( isSafari || isKonqueror ) 
{
	function HTMLElement ( ) { };
	function HTMLDocument ( ) { };
	function HTMLCollection ( ) { };
	function HTMLOptionsCollection ( ) { };
	function Text ( ){ }; function Node ( ){ };
	
	HTMLElement = new Object ( );
	HTMLDocument = new Object ( );
	HTMLCollection = new Object ( );
	HTMLOptionsCollection = new Object ( );
	Text = document.createTextNode ( '' ).constructor;
	Node = Text;
	
	HTMLElement.prototype = document.createElement ( 'p' ).__proto__.__proto__;
	HTMLDocument = document.constructor;
	HTMLOptionsCollection = document.createElement ( 'select' ).options.constructor;
}

// IE fixes
if( typeof Array.prototype.push == 'undefined' )
	Array.prototype.push = function( element ) { this[this.length] = element; }
if( typeof Array.prototype.pop == 'undefined' )
	Array.prototype.pop = function () { var last = this[ this.length - 1 ]; this.length--; return last; }
if( typeof Array.prototype.forEach == 'undefined' )
	Array.prototype.forEach = function( f ) { for( var a = 0; a < this.length; a++ ) if ( this[a] ) f ( this[a] ); }

if( typeof window.addEventListener == "undefined" )
{
	window.addEventListener = function( type, listener, useCapture )
	{ window.attachEvent ( "on"+type, listener ); }
}

/* ************************************************************************ *\
*  MISC FUNCTIONS
\* ************************************************************************ */

function escapeFlash( string )
{
	// Only escape &s, =s, and whitespace characters
	string = string.replace( /([?=&\s])/g, function( match ) { return escape( match ) } );
	// Also escape the + char
	string = string.replace( /\+/g,  "%2B" );
	return string;
}

function getValueByElementId ( elementId )
{
	if ( document.getElementById( elementId ) )
	{
		elmnt = document.getElementById( elementId );
		if ( elmnt.type == "checkbox" )
			return elmnt.checked;
		return elmnt.value;
	}
	else
		return "";
}

var _includeBuf = new Array ( );
var _includePreDocBuf = new Array ( );
var _includeTimeout = false;
function include ( src )
{
	if ( !document.body && src )
	{
		_includePreDocBuf.push ( src );
		include_delayed ( );
	}
	else if ( document.body && src )
	{
		if ( _includeBuf[ src ] ) return false;
		_includeBuf[ src ] = src;
		var head = document.getElementsByTagName ( 'head' )[ 0 ];
		var script = document.createElement ( 'script' );
		script.src = src;
		script.type = 'text/javascript';
		head.appendChild ( script );
		return true;
	}
}
function include_delayed ( )
{
	clearTimeout ( _includeTimeout );
	
	if ( !_includePreDocBuf.length )
		return;
		
	if ( document.body )
	{
		if ( _includeTimeout ) clearTimeout ( _includeTimeout );
		var ar = _includePreDocBuf;
		_includePreDocBuf = false;
		for ( var a = 0; a < ar.length; a++ )
			include ( ar[ a ] );
	}
	else 
	{
		_includeTimeout = setTimeout ( 'include_delayed ( )', 50 );
	}
}

function getDocumentWidth ( )
{
  var theWidth;
  if ( window.innerWidth )
    theWidth = window.innerWidth;
  else if ( document.documentElement && document.documentElement.clientWidth )
    theWidth = document.documentElement.clientWidth;
  else if ( document.body )
    theWidth = document.body.clientWidth;
  return theWidth;
}

function hideFormFields ( varAr )
{
	if ( !isIE ) return;
		
	if ( typeof ( varAr ) != "array" )
		varAr = Array ( varAr );
	for ( var a = 0; a < varAr.length; a++ )
	{
		var tagname = varAr[ a ] + "";
		var eles = document.getElementsByTagName ( tagname.toUpperCase ( ) );
		for ( var b = 0; b < eles.length; b++ )
		{
			if ( !eles[ b ].hidden )
			{
				eles[ b ].hidden = true;
				eles[ b ].style.visibility = "hidden";
			}
		}
	}
}

function computedStyle ( element )
{
	if ( isIE )
	{
		return element.currentStyle;
	}
	return getComputedStyle ( element, null );
}


function showFormFields ( varAr )
{
	if ( !isIE ) return;
		
	if ( typeof ( varAr ) != "array" )
		varAr = Array ( varAr );
	for ( var a = 0; a < varAr.length; a++ )
	{
		var tagname = varAr[ a ] + "";
		var eles = document.getElementsByTagName ( tagname.toUpperCase ( ) );
		for ( var b = 0; b < eles.length; b++ )
		{
			if ( eles[ b ].hidden )
			{
				eles[ b ].hidden = false;
				eles[ b ].style.visibility = "visible";
			}
		}
	}
}

function getScrollTop ( )
{
	if ( isIE )
	{
		if ( document.body && document.body.scrollTop ) 
			return document.body.scrollTop;
		return document.documentElement.scrollTop;		
	} 
	else if ( document.scrollTop )
		return document.scrollTop;
	else return window.pageYOffset; 
}

function getScrollLeft ( )
{
    if ( isIE )
		return document.documentElement.scrollLeft;
    else if ( document.body.scrollLeft )
		return document.body.scrollLeft;
	else
		return window.pageXOffset;
}

function getDocumentHeight ( )
{
  var theHeight;
  if ( window.innerHeight )
    theHeight = window.innerHeight;
  else if ( document.documentElement && document.documentElement.clientHeight )
    theHeight = document.documentElement.clientHeight;
  else if ( document.body )
    theHeight = document.body.clientHeight;
  return theHeight;
}

function getElementWidth ( element )
{
	if ( element == window )
		return getDocumentWidth ( );
	return element.offsetWidth ? element.offsetWidth : element.clientWidth;
}

function getElementHeight ( element )
{
	if ( element == window )
		return getDocumentHeight ( );
	return element.offsetHeight ? element.offsetHeight : element.clientHeight;
}

function getVerticalScrollbarWidth ( )
{
	return getDocumentWidth ( ) - getElementWidth ( document.body );
}

function positionElement ( element, halign, valign )
{
	var marginx = 8;
	var marginy = 8;
	var maxwidth = 0;
	var maxheight = 0;
	var windowx = 0;
	var windowy = 0;
	var scrolloffsetx = 0;
	var scrolloffsety = 0;
	var posx = 0;
	var posy = 0;
	var elementWidth = getElementWidth ( element );
	var elementHeight = getElementHeight ( element );
	element.style.top = "0px";
	element.style.left = "0px";
	
	if (self.innerHeight) // all except Explorer
	{
		windowx = self.innerWidth;
		windowy = self.innerHeight;
	}
	else if (document.documentElement && document.documentElement.clientHeight)
	// Explorer 6 Strict Mode
	{
		windowx = document.documentElement.clientWidth;
		windowy = document.documentElement.clientHeight;
	}
	else if (document.body) // other Explorers
	{
		windowx = document.body.clientWidth;
		windowy = document.body.clientHeight;
	}
	if ( document.body.clientWidth )
	{
		maxwidth  = document.body.clientWidth;
		maxheight = document.body.clientHeight;
	}
	else if ( document.documentElement.clientWidth )
	{
		maxwidth  = document.documentElement.clientWidth;
		maxheight = document.documentElement.clientHeight;
	}
	scrolloffsety = getScrollTop ( );		
	scrolloffsetx = getScrollLeft ( );	
		
	// Vertical alignment
	if ( valign == "mouseTop" )
	{
		posy = scrolloffsety + mousey - 8;
		element.style.top = posy + "px";
	}
	if ( valign == "middle" )
	{
		posy = windowy / 2 ;
		posy = posy - ( elementHeight / 2 );
		if ( scrolloffsety )
		{
			posy = posy + scrolloffsety;
			if ( posy < ( scrolloffsety + marginy ) )
				posy = scrolloffsety + marginy;
		}
		if ( posy < marginy )
			posy = marginy;
		posy = Math.ceil ( posy );
		element.style.top = posy + "px";
	}
	if ( halign == "mouseRight" )
	{
		posx = scrolloffsetx + mousex - 8;
		element.style.left = posx + "px";
	}
	if ( halign == "mouseCenter" )
	{
		posx = scrolloffsetx + mousex;
		posx = posx - ( elementWidth / 2 );
		element.style.left = posx + "px";
	}
	if ( halign == "center" )
	{
		posx = windowx / 2 ;
		if ( scrolloffsetx )
			posx = posx + scrolloffsetx;
		posx = posx - ( elementWidth / 2 );
		if ( posx < marginx )
			posx = marginx;
		posx = Math.ceil ( posx );
		element.style.left = posx + "px";
	}
}

/* ************************************************************************ *\
*  DOM EXTENSIONS
*  Rewritten 2006-06-06 - inge
\* ************************************************************************ */

// The getElementsByClassName and getElementsByTagAndClassName functions
document.getElementsByTagAndClassName = function ( tagName, className, parentElement )
{
	var elements = new Array ();
	var parent = ( parentElement ) ? parentElement : document.body;
	var children = parent.getElementsByTagName( tagName );
	for ( var a = 0; a < children.length; a++ )
		if ( hasClass ( children[ a ], className ) ) elements.push ( children[a] );
	return elements;
}

// ..and the wrappers
document.getElementsByClassName                    = function ( className, parentElement ) { return document.getElementsByTagAndClassName ( "*", className, parentElement ); }
HTMLElement.prototype.getElementsByTagAndClassName = function ( tagName, className )       { return document.getElementsByTagAndClassName ( tagName, className, this ); }
HTMLElement.prototype.getElementsByClassName       = function ( className )                { return document.getElementsByClassName ( className, this ); }

// Gets a div by id inside an element
function getDivById ( id, element )
{
	if ( !element ) element = document.body;
	var divs = element.getElementsByTagName ( "DIV" );
	for ( var a = 0; a < divs.length; a++ )
		if ( divs[ a ].id == id ) return divs[ a ];
	return false;
}
document.getDivById 																= function ( id, element ){ return getDivById ( id, element ); }
HTMLElement.prototype.getDivById										= function ( id ){ return getDivById ( id, this ); }

// Get the computed css property
function getStyle( element, cssrule )
{
	var str = '';
	if ( document.defaultView && document.defaultView.getComputedStyle )
	{
		try 
		{
			str = document.defaultView.getComputedStyle ( element, '' ).getPropertyValue( cssrule );
		}
		catch ( e ){};
	}
	else if ( element.currentStyle )
	{
		cssrule = cssrule.replace(
			/\-(\w)/g, 
			function ( strMatch, p1 )
			{
				return p1.toUpperCase ( );
			}
		);
		str = element.currentStyle[ cssrule ];
	}
	return str;

}

// Set a style property
function setStyle( element, cssRule, value )
{
	var original = getStyle( element, cssRule );
	if ( !element.styleHistory )            element.styleHistory = new Array();
	if ( !element.styleHistory[ cssRule ] ) element.styleHistory[ cssRule ] = new Array();
	element.styleHistory[ cssRule ].push( original );
	element.style[ cssRule ] = value;
	return value;
}

// Undo a style property change
function undoSetStyle( element, cssRule )
{
	if( this.styleHistory && this.styleHistory[ cssRule ] && this.styleHistory[ cssRule ].length )
	{
		var oldValue = this.styleHistory[ cssRule ].pop();
		this.style[ cssRule ] = oldValue;
	}
}

// Revert one or more style properties
function revertStyle( element, cssRules )
{
	var stylesChanged = 0;
	if( element.styleHistory )
	{
		if ( cssRules == '*' ) 
		{ 
			cssRules = new Array(); for( var a in element.styleHistory ) cssRules.push( a ); 
		}
		else if ( typeof cssRules != "Array" ) cssRules = Array( cssRules );

		for( var a = 0; a < cssRules.length; a++ )
		{
			var cssRule = cssRules[a];
			if ( element.styleHistory[ cssRules[a] ] && element.styleHistory[ cssRules[a] ][0] )
			{
				element.style[ cssRules[a] ] = element.styleHistory[ cssRules[a] ][0];
				element.styleHistory[ cssRules[a] ] = new Array();
				stylesChanged++;
			}
		}
	}
	return stylesChanged;
}

// Toggle display: none on an element
function toggleHidden( element )
{	
	if ( element )
	{
		if ( element.isHidden || element.style.display == "none" || element.style.visibility == "hidden" ) 
		{
			element.style.display = "";
			element.style.visibility = "visible";
			element.isHidden = false;
		}
		else
		{
			element.style.display = "none";
			element.style.visibility = "hidden";
			element.isHidden = true;
		}
	}
}

// Check if an element has a specific class name
function hasClass( element, className )
{
	return element.classList.contains( className );
}

// Add a classname to an element
function addClass( element, className )
{
	return element.classList.add( className );
}

// Remove a classname from an element
function removeClass( element, className )
{
	return element.classList.remove( className );
}


function getRadioValue ( radioobj )
{
	if ( !radioobj ) return;
	if ( !radioobj.length && radioobj.checked )
		return radioobj.value;
	else if ( radioobj.length )
	{
		for ( var a = 0; a < radioobj.length; a++ )
		{
			if ( radioobj[ a ].checked )
				return radioobj[ a ].value;
		}
	}
	return false;
}

function elementHasClass( element, className ) { return hasClass( element, className ); }

/* ************************************************************************ *\
*  GET MOUSE POSITION
\* ************************************************************************ */

var arenaInputEvents = new Array ();
arenaInputEvents[ "onmouseup" ] = new Array ( );
arenaInputEvents[ "onmousedown" ] = new Array ( );
arenaInputEvents[ "onmouseout" ] = new Array ( );
arenaInputEvents[ "onscroll" ] = new Array ( );
arenaInputEvents[ "onresize" ] = new Array ( );
arenaInputEvents[ "onmousemove" ] = new Array ( );
arenaInputEvents[ "onkeydown" ] = new Array ( );
arenaInputEvents[ "onkeyup" ] = new Array ( );
arenaInputEvents[ "onmousewheel" ] = new Array ( );

function removeEvent ( varEvent, varfunc )
{
	if ( !arenaInputEvents [ varEvent ] )
		return false;
	var output = new Array ( );
	for ( var a = 0; a < arenaInputEvents [ varEvent ].length; a++ )
	{
		if ( arenaInputEvents [ varEvent ][ a ] != varfunc )
			output.push ( arenaInputEvents [ varEvent ][ a ] );
	}
	arenaInputEvents [ varEvent ] = output;
	return true;
}

function addEvent ( varEvent, varfunc )
{
	switch ( varEvent )
	{
		case "onmouseup":
			var events = arenaInputEvents[ "onmouseup" ];
			events.push ( varfunc );
			return varfunc;
		case "onmousedown":
			var events = arenaInputEvents[ "onmousedown" ];
			events.push ( varfunc );
			return varfunc;
		case "onmouseout":
			var events = arenaInputEvents[ "onmouseout" ];
			events.push ( varfunc );
			return varfunc;
		case "onscroll":
			var events = arenaInputEvents[ "onscroll" ];
			events.push ( varfunc );
			return varfunc;
		case "onresize":
			var events = arenaInputEvents[ "onresize" ];
			events.push ( varfunc );
			return varfunc;
		case "onmousemove":
			var events = arenaInputEvents[ "onmousemove" ];
			events.push ( varfunc );
			return varfunc;
		case "onkeydown":
			var events = arenaInputEvents[ "onkeydown" ];
			events.push ( varfunc );
		case "onkeyup":
			var events = arenaInputEvents[ "onkeyup" ];
			events.push ( varfunc );
		case "onmousewheel":
			var events = arenaInputEvents[ "onmousewheel" ];
			events.push ( varfunc );
		default: 
			return false;
	}
}

function addAction ( func )
{
	return addEvent ( 'onmousemove', func );
}

// Add event listener for all browsers
function addEventTo ( obj, strEvent, func )
{
	if ( isIE )
	{
		var ev = strEvent;
		if ( ev.substr ( 0, 2 ) != 'on' )
			ev = 'on' + ev;
		obj.attachEvent ( ev, func );
	}
	else
	{
		obj.addEventListener ( strEvent, func, true );
	}
}

var winele__ = isIE ? document : window; // sweet eh?

winele__.onscroll = function ( e )
{
	if ( typeof ( arenaInputEvents[ "onscroll" ] ) != "undefined" )
	{
		for ( var a = 0; a < arenaInputEvents[ "onscroll" ].length; a++ )
			arenaInputEvents[ "onscroll" ][ a ] ( e );
	}
}

winele__.onresize = function ( e )
{
	if ( typeof ( arenaInputEvents[ "onresize" ] ) != "undefined" )
	{
		for ( var a = 0; a < arenaInputEvents[ "onresize" ].length; a++ )
			arenaInputEvents[ "onresize" ][ a ] ( e );
	}
}

winele__.onmousemove = function ( e )
{
	var posx = 0;
	var posy = 0;
	var ev = !e ? window.event : e;
	
	if ( ev.pageX || ev.pageY )
	{
		mousex = ev.pageX;
		mousey = ev.pageY;
	}
	else if ( ev.clientX || ev.clientY )
	{
		mousex = ev.clientX;
		mousey = ev.clientY;

		if ( isIE )
		{
			mousex += getScrollLeft();
			mousey += getScrollTop();
		}
	}

	if ( typeof ( arenaInputEvents[ "onmousemove" ] ) != "undefined" )
	{
		for ( var a = 0; a < arenaInputEvents[ "onmousemove" ].length; a++ )
			arenaInputEvents[ "onmousemove" ][ a ] ( e );
	}
	if ( document.leftDocument )
	{
		var t = ev.target ? ev.target : ev.srcElement;
		if ( t && t.nodeName == '#document' )
		{
			if ( typeof ( arenaInputEvents[ "onmouseout" ] ) != "undefined" )
			{
				for ( var a = 0; a < arenaInputEvents[ "onmouseout" ].length; a++ )
					arenaInputEvents[ "onmouseout" ][ a ] ( e );
			}
		}
		else document.leftDocument = false;
	}
}

winele__.onmousedown = function ( e )
{
	if ( typeof ( arenaInputEvents[ "onmousedown" ] ) != "undefined" )
	{
		for ( var a = 0; a < arenaInputEvents[ "onmousedown" ].length; a++ )
			arenaInputEvents[ "onmousedown" ][ a ] ( e );
	}
}

winele__.onmouseup = function ( e )
{
	if ( typeof ( arenaInputEvents[ "onmouseup" ] ) != "undefined" )
	{
		for ( var a = 0; a < arenaInputEvents[ "onmouseup" ].length; a++ )
			arenaInputEvents[ "onmouseup" ][ a ] ( e );
	}
}

winele__.onkeyup = function ( e )
{
	if ( typeof ( arenaInputEvents[ "onkeyup" ] ) != "undefined" )
	{
		for ( var a = 0; a < arenaInputEvents[ "onkeyup" ].length; a++ )
			arenaInputEvents[ "onkeyup" ][ a ] ( e );
	}
}

winele__.onkeydown = function ( e )
{
	if ( typeof ( arenaInputEvents[ "onkeydown" ] ) != "undefined" )
	{
		for ( var a = 0; a < arenaInputEvents[ "onkeydown" ].length; a++ )
			arenaInputEvents[ "onkeydown" ][ a ] ( e );
	}
}

winele__.onmouseout = function ( e )
{
	e = e ? e : window.event;
	var t = e.target ? e.target : e.srcElement;
	
	if ( t.parentNode && t.parentNode.nodeName == '#document' )
	{
		document.leftDocument = true;
	}
	else document.leftDocument = false;
}

// mouse wheel stuff
_arena_wheel_func = function ( e )
{
	if ( typeof ( arenaInputEvents[ "onmousewheel" ] ) != "undefined" )
	{
		for ( var a = 0; a < arenaInputEvents[ "onmousewheel" ].length; a++ )
			arenaInputEvents[ "onmousewheel" ][ a ] ( e );
	}
}
if ( winele__.addEventListener )
	winele__.addEventListener('DOMMouseScroll', _arena_wheel_func, false);
winele__.onmousewheel = document.onmousewheel = _arena_wheel_func;

/* ************************************************************************ *\
*  ONLOAD
\* ************************************************************************ */

var onloadFunctions = Array ();

function addOnload ( func )
{
  var temparray = onloadFunctions;
  temparray [ temparray.length ] = func;
  onloadFunctions = temparray;
}

window.onload = function ( )
{
  var func;
  for ( var i = 0; i < onloadFunctions.length; ++i ) 
    onloadFunctions[i] ();
  window.loaded = true;
} 

var collapsedLists = false;
function collapseLists ( )
{
	if ( !collapsedLists )
	{
		// Apply collapsable behaviour on lists
		var lists = document.getElementsByTagName ( "ul" );
		for ( i = 0; i < lists.length; i++ )
		{
			if ( elementHasClass ( lists[ i ], "collapsable" ) )
				makeCollapsable ( lists[ i ] );	
		}
		collapsedLists = true;
	}
}

/* ************************************************************************ *\
*  GLOBAL ARRAY
\* ************************************************************************ */

var globalArray = new Array ( );
function globalAdd ( func )
{
	var len = globalArray.length;
	globalArray[ len ] = func;
	return len;
}
function globalRemove ( pos )
{
	var Arr = new Array ( );
	for ( var a = 0; a < globalArray.length; a++ )
	{
		if ( a != pos )
			Arr[ Arr.length ] = globalArray[ a ];
	}
	globalArray = Arr;
}

/* ************************************************************************ *\
*  COLLAPSABLE LISTS
\* ************************************************************************ */

function makeCollapsable ( item ) 
{
	var listitems = item.getElementsByTagName ( 'li' );
	var uls = item.getElementsByTagName ( 'ul' );
	
	// Update the cache of spacer divs
	document.collapseElementsBreak = getElementsByClassName ( 'Break', item );
  
	var expandElements = new Array ();
  
	for ( var i = 0; i < listitems.length; i++ ) 
 	{
 		var span = document.createElement( "SPAN" );
 		
 		span.className = "collapse";
 		{
 			listitems[ i ].topElement = item;
    		listitems[ i ].insertBefore ( span, listitems[ i ].firstChild );
    	}
		listitems[ i ].ullist = uls;
		
		if ( elementHasClass ( listitems[ i ], "current" ) )
			expandElements[ expandElements.length ] = listitems[ i ];
    
		/* *****************************************************
		*  COLLAPSE ITEM
		\* ***************************************************** */
		listitems[ i ].collapse = function ( ) 
		{
			this.collapsed = true;
			var len = this.subLists ? this.subLists.length : 0;
			for ( var x = 0; x < len; x++ )
			{
				if ( this.subLists[ x ].parentNode == this )
					this.subLists[ x ].style.display = 'none';
				
				// Remove spacer divs when collapsing
				var breaks = getElementsByClassName ( 'Break', this );
				for ( var z = 0; z < breaks.length; z++ )
					breaks[ z ].parentNode.removeChild ( breaks[ z ] );
			}
			this.icons ();
		}
    
		/* *****************************************************
		*  EXPAND ITEM
		\* ***************************************************** */
		listitems[ i ].expand = function () 
		{
			this.collapsed = false;
			var len = this.subLists ? this.subLists.length : 0;
			for ( var x = 0; x < len; x++ ) 
			{
				if ( this.subLists[ x ].parentNode == this )
					this.subLists[ x ].style.display = '';
			}	
			this.icons ();
		}
    
    /* *****************************************************
    *  EXPAND TREE
    \* ***************************************************** */
    listitems[ i ].expandTree = function ()
    {
      this.expand ();
      if ( 
      	this.parentNode.parentNode && 
      	typeof ( this.parentNode.parentNode.expandTree ) != "undefined" 
      )
      {
        this.parentNode.parentNode.expandTree ();
      }
    }
    
    /* *****************************************************
    *  OPEN/COLLAPSE ITEM
    \* ***************************************************** */
    listitems[ i ].toggle = function () 
    {
    	// Update the cache of spacer divs
      if ( this.collapsed == false )
        this.collapse ();
      else
        this.expand ();
      // Update the cache of spacer divs
      document.collapseElementsBreak = getElementsByClassName ( 'Break', this.topElement );
    }

    /* *****************************************************
    *  RECALCULATE ICONS
    \* ***************************************************** */
    listitems[ i ].icons = function ()
    {
    	var img, icon;
			if ( !( img = this.firstChild.firstChild ) )
			{
				img = document.createElement ( "IMG" );
				img.className = "Icon";
				this.firstChild.appendChild( img );
				if ( elementHasClass ( this.firstChild.parentNode, "current" ) )
					this.firstChild.className = "Iconcurrent";
			}
    	if ( this.subLists.length > 0 )
    	{
    		if ( this.collapsed )
    		{
	    		icon = "lib/icons/bullet_toggle_plus.png";
    		}
    		else
    		{
    			icon = "lib/icons/bullet_toggle_minus.png";
    			
    			// Insert spacer divs..
    			var found = false;
    			for ( var ea = 0; ea < document.collapseElementsBreak.length; ea++ ) 
    			{
    				if ( document.collapseElementsBreak[ ea ].parentNode == this )
    				{
    					found = true; 
    					break;
    				}
    			}
    			if ( !found )
    			{
					var Break = document.createElement ( "DIV" );
					Break.className = "Break";
					if ( this.insertBefore && this.childNodes.length >= 3 )
						this.insertBefore ( Break, this.childNodes[ 2 ] );
				}
    		}
    	}
    	else
    	{
    		icon = "lib/icons/bullet_black.png";
    	}
    	if ( img.src != icon ) img.src = icon;
    }

    clickables = listitems[ i ].getElementsByTagName ( 'span' );
    for ( var x = 0; x < clickables.length; x++ ) 
    {
      if ( clickables[ x ].className == 'collapse' ) 
      {        
        clickables[ x ].onclick = function ( ) 
        {
          this.parentNode.toggle ( );
        }
        if ( isIE )
        	clickables[ x ].style.cursor = "hand";
        else
        	clickables[ x ].style.cursor = "pointer";
      }
    }
    listitems[i].subLists = listitems[i].getElementsByTagName ( 'ul' );
    listitems[i].collapsed = true;
    listitems[i].collapse ();
    listitems[i].icons ();
    
  }
  for (var i = 0; i < expandElements.length; ++i) 
  	expandElements[ i ].expandTree ();
}

/* ************************************************************************ *\
*  FIX PNGS IN IE
\* ************************************************************************ */

var fixPNGsExceptions = new Array ( );
function isFixPNGsException ( varClass )
{
	for ( var a = 0; a < fixPNGsExceptions.length; a++ )
		if ( fixPNGsExceptions[ a ] == varClass )
			return true;
	return false;
}
function addFixPNGsException ( varClass )
{
	fixPNGsExceptions[ fixPNGsExceptions.length ] = varClass;
}
function fixPNGs ( arg, mode )
{	
	if ( !mode || mode == 1 )
	{
		if ( !arg )
			arg = false;
		var img = true;
		var div = true
		var td = true;
		if ( arg == "img" )
		{
			div = false;
			td = false;
		}
		if ( isIE )
		{							
			if ( div )
			{
				// div
				var elementlist = document.body.getElementsByTagName ( "DIV" );						
				for ( var a = 0; a < elementlist.length; a++ )
				{
					if ( 
						!isFixPNGsException ( elementlist[ a ].className ) &&
						elementlist[ a ].style.background.indexOf ( ".png" ) > 0 
					)
					{
						var sourceString = elementlist[ a ].style.background.split ( "'" );
						sourceString = sourceString.join ( "" );
						
						var image = sourceString.split ( "url(" );					
						
						if ( typeof ( image[ 1 ] ) != "undefined" )
						{
							var img = image[ 1 ].split ( ")" );						
							elementlist[ a ].style.background = "none";
							elementlist[ a ].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+img[0]+"', sizingMethod='normal')";			
						}					
					}
				}
			}						
			// td
			if ( td )
			{
				var elementlist2 = document.body.getElementsByTagName ( "td" );		
				for ( var a = 0; a < elementlist2.length; a++ )
				{
					if ( 
						!isFixPNGsException ( elementlist2[ a ].className ) &&
						elementlist2[ a ].style.background.indexOf ( ".png" ) > 0 
					)
					{
						var sourceString = elementlist2[ a ].style.background.split ( "'" );
						sourceString = sourceString.join ( "" );
						
						var image = sourceString.split ( "url(" );					
						
						if ( typeof ( image[ 1 ] ) != "undefined" )
						{						
							var img = image[ 1 ].split ( ")" );						
							elementlist2[ a ].style.background = "none";
							elementlist2[ a ].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+img[0]+"', sizingMethod='normal')";
						}					
					}
				}
			}			
			//img
			if ( img )
			{
				var elementlist3 = document.body.getElementsByTagName ( "img" );		
				for ( var a = 0; a < elementlist3.length; a++ )
				{				
					if ( 
						!isFixPNGsException ( elementlist3[ a ].className ) &&
						elementlist3[ a ].src.indexOf ( ".png" ) > 0 
					)
					{					
						var image = elementlist3[ a ].src;					
						if ( image.indexOf ( ".png" ) > 0 )
						{		
							if ( typeof ( image ) != "undefined" )
							{							
								var tmpImg = new Image ( );							
								tmpImg.src = elementlist3[ a ].src;
								elementlist3[ a ].src = "admin/gfx/nil.gif";
								elementlist3[ a ].style.width = tmpImg.width;
								elementlist3[ a ].style.height = tmpImg.height;
								elementlist3[ a ].style.background = "none";
								elementlist3[ a ].style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='"+tmpImg.src+"', sizingMethod='normal')";							
							}					
						}
					}
				}
			}
		}
	}
}

// Get an array from document.location
function getUrlVar ( varU )
{
	varStr = document.location + "";
	varStr = varStr.split ( varU + "=" );		
	if ( typeof ( varStr[ 1 ] ) == "string" )
	{		
		varStr = varStr[ 1 ].split ( "&" );		
		return varStr[ 0 ];
	}	
	return false;
}

var cProgressLoader = function ( siteDiv, progressbarCanvas, progressbarDiv, fixMode )
{
	if ( fixMode )
		this.fixPNGImages = fixMode;
	else
		this.fixPNGImages = false;	
	this.progressbarDiv = progressbarDiv;
	this.progressbarCanvas = progressbarCanvas;
	this.progressBarDom = document.createElement ( "DIV" );
	this.progressBarDom.className = "ProgressBar";
	this.progressTextDom = document.createElement ( "DIV" );
	this.progressTextDom.className = "ProgressText";
	this.siteDiv = siteDiv;
	this.imagesLoaded = 0;
	this.totalImages = 0;
	this.imagesArray = new Array ( );	
	this.showSite = function ( )
	{						
		this.progressbarCanvas.innerHTML = "";
		this.progressbarCanvas.style.display = "none";
		this.siteDiv.style.visibility = "visible";		
		if ( this.fixPNGImages )
			fixPNGs ( this.fixPNGImages );
	}			
	this.init = function ( )
	{	
		// Add nodes		
		this.progressBarNode = this.progressbarDiv.appendChild ( this.progressBarDom );				
		this.progressTextNode = this.progressbarDiv.appendChild ( this.progressTextDom );
		
		var imagesEle = document.getElementsByTagName ( "img" );		
		if ( imagesEle.length > 0 && navigator.userAgent.indexOf ( "Safari" ) <= 0 )
		{			
			for ( var a = 0; a < imagesEle.length; a++ )
			{
				this.imagesLoaded++;
				this.totalImages++;
				var len = this.imagesArray.length;
				this.imagesArray[ len ] = new Image ( );
				this.imagesArray[ len ].master = this;
				this.imagesArray[ len ].onload = function ( )
				{
					var progress = Math.round ( 100 - ( this.master.imagesLoaded / this.master.totalImages * 100 ) );
					this.master.imagesLoaded--;
					this.master.progressTextNode.innerHTML = "Laster inn - " + progress + "% ferdig";
					this.master.progressBarNode.style.width = progress + "%";										
					
					if ( this.master.imagesLoaded == 0 )
						this.master.showSite ( );						
				}
				this.imagesArray[ len ].src = imagesEle[ a ].src;
			}		 	
		}
		else
			this.showSite ( );
	}	
		
	this.init ( );	
}

// SAFE VAR - REMEMBER TO SYNC WITH PHP EQUIVALENT!

// Read a string that has been made safe for transport
function readVarSafe ( va )
{
	// Tab
	va = str_replace ( "_[taB]_", 	"\t", 							va );
	va = str_replace ( "_[taB2]_", 	"_[taB]_", 					va ); // <- unlikely but needed
	// Line ending		
	va = str_replace ( "_[newlinE]_", 	"\n", 					va );
	va = str_replace ( "_[newlinE2]_", 	"_[newlinE]_", 	va ); // <- unlikely but needed
	return va;
}

// Write a string in a way that it's safe to transport
function writeVarSafe ( va )
{
	// Tab
	va = str_replace ( "_[taB]_", "_[taB2]_", 				va ); // <- unlikely but needed (precaution)
	va = str_replace ( "\t", "_[taB]_", 					va );
	// Line ending
	va = str_replace ( "_[newlinE]_", "_[newlinE2]_",		va ); // <- unlikely but needed (precaution)
	va = str_replace ( "\n", "_[newlinE]_", 				va );
	return va;
}

function getElementsByClassName ( classname, node )
{
	if ( !node ) node = document.body;
	var Elements = node.getElementsByTagName ( "*" );
	if ( typeof ( Elements ) != "undefined" )
	{
		var array = new Array ( );
		var len = Elements.length;
		for ( var a = 0; a < len; a++ )
		{
			if ( Elements[ a ].className.length )
			{
				if ( Elements[ a ].className.match ( 
					new RegExp( "(^|\\s)" + classname + "(\\s|$)" ) 
				) )
				{
					array.push ( Elements[ a ] );
				}
			}
		}
	}
	return array;
}

function getElementLeft ( varElement )
{
	if ( !varElement ) return;
	var element = varElement;
	var val = 0;
	try 
	{
		if ( element.offsetParent )
		{
			do
			{
				val += element.offsetLeft;
				if ( element.id == "workbench" && !isIE6 )
					val += getScrollLeft ( );
			}
			while ( ( element = element.offsetParent ) != null );
		}
		else if ( element.offsetLeft )
			val = element.offsetLeft;
	}
	catch ( e ){};
	return val;
}

function getElementTop ( varElement )
{
	if ( !varElement ) return;
	var element = varElement;
	var val = 0;
	try 
	{
		if ( element.offsetParent )
		{		
			do
			{
				val += element.offsetTop;
				if ( element.id == "workbench" && !isIE6 )
					val += getScrollTop ( );
			}
			while ( ( element = element.offsetParent ) != null );
		}
		else if ( element.offsetTop )
			val = element.offsetTop;
	}
	catch ( e ){};
	return val;
}

function getElementPosition ( element )
{
	if ( !element ) return;
	var obj = new Object ();
	obj.top = getElementTop ( element );
	obj.left = getElementLeft ( element );
	obj.width = getElementWidth ( element );
	obj.height = getElementHeight ( element );
	return obj;
}

function getCookie ( cookieName )
{
	if ( document.cookie.length > 0 )
	{
		var c = document.cookie + '';
		c = c.split ( cookieName + '=' );
		if ( c.length > 1 )
		{
			if ( c[ 1 ].indexOf ( ';' ) >= 0 )
			{
				c = c[ 1 ].split ( ';' );
				return unescape ( c[ 0 ] );
			}
			return unescape ( c[ 1 ] );
		}
		return false;
	}
	return false;
}

function setCookie ( cookieName, cookieVar, expiration, path )
{

    if ( !expiration ) expiration = 365;
    if ( !path )
    {
        path = document.location + '';
        var prot = document.location.href.match( /https/i ) ? 'https' : 'http';
        path = path.replace ( prot + '://', '' );
        path = '.' + path.split('/')[0];
        path = path.split ( ':' )[0];
        while ( path.substr ( 0, 1 ) == '.' )
            path = path.substr ( 1, path.length - 1 );
        if ( path == 'localhost' || path == '127.0.0.1' )
            path = '';
    }

    var expireDate = new Date();

    expireDate.setDate ( expireDate.getDate ( ) + expiration );

    var Cookie = cookieName + "=" + escape( cookieVar );
    if ( expiration ) Cookie += "; expires=" + expireDate.toGMTString ( );
    if ( path ) Cookie += "; domain=" + path + "; path=/";
    document.cookie = Cookie;
} 

// Copy an object
function copyObject ( obj )
{
	if ( obj )
	{
		var copy;
		if ( obj.tagName )
			copy = document.createElement ( obj.tagName );
		else copy = new Object ( );
		for ( var p in obj )
		{
			try
			{
				copy[ p ] = obj[ p ];
			}
			catch ( e ){ }
		}
		return copy;
	}
	return false;
}

function preventBrowserDrag ( element )
{
	element.ondrag = function () { return false; }
	if ( typeof element.addEventListener != "undefined" )
	{
		element.addEventListener( "mousedown", function ( e ) 
		{ 
			if ( e.preventDefault )
				e.preventDefault();
		}, true );
	}
}

function makeNumeric ( varNUM )
{
	varNUM = varNUM + "";
	varNUM = varNUM.split ( "px" );
	varNUM = varNUM.join ( "" );
	varNUM = varNUM.split ( "," );
	varNUM = varNUM.join ( "." );
	varNUM++; varNUM--;
	return varNUM;
}

// Generic tab page support
function changeTab ( varTab, arr, cookiename )
{
	// make sure we have the tab!
	var found = 0;
	for ( var a = 0; a < arr.length; a++ )
	{
		if ( arr[ a ] == varTab )
		{
			found = 1; break;
		}
	}
	if ( found != 1 ) varTab = arr[ 0 ];
	
	if ( !cookiename )
		cookiename = 'tabmode=';
	else cookiename = cookiename + '=';
	
	for ( var a = 0; a < arr.length; a++ )
	{
		if ( arr[ a ] == varTab )
		{
			document.getElementById ( "tab" + varTab ).tabIsActive = true;
			document.getElementById ( "tab" + varTab ).className = "tab tabCurrent";
			document.getElementById ( "page" + varTab ).style.display = "";
			document.getElementById ( "page" + varTab ).pageIsActive = true;
			document.cookie = cookiename + varTab;
		}
		else
		{
			document.getElementById ( "tab" + arr[ a ] ).tabIsActive = false;
			document.getElementById ( "tab" + arr[ a ] ).className = "tab";
			document.getElementById ( "page" + arr[ a ] ).style.display = "none";
			document.getElementById ( "page" + arr[ a ] ).pageIsActive = false;
		}
		if ( navigator.userAgent.indexOf ( "MSIE" ) > 0 )
			document.getElementById ( "tab" + arr[ a ] ).style.cursor = "hand";
		else document.getElementById ( "tab" + arr[ a ] ).style.cursor = "pointer";
	}
}

/**
 * New tab support
 * Usage: initTabSystem ( 'myTabs', 'normal', 'myTabsTabs' ); @last two args are optional
 * Or   : initTabSystem ( 'myTabs', 'subtabs' );
 * Or   : initTabSystem ( 'myTabs' );
 */
function initTabSystem ( element, vartype, key )
{
	var ele, varID;
	if ( !element ) element = false;
	if ( typeof ( element ) == 'object' ) ele = element;
	else ele = document.getElementById ( element );
	
	if ( !ele ) return;
	else if ( ele.initialized ) return;
	else ele.initialized = true;
	
	varID = ele.id;
	if ( !vartype ) vartype = 'normal';
	if ( !key ) key = varID;
	var Match = 'tab';
	if ( vartype == 'subtabs' ) Match = 'subTab';
	
	var tabs = ele.getElementsByTagName ( 'div' );
	var activetab = false;
	var lasttab = false;

	for ( var n = 0; n < tabs.length; n++ )
	{	
		if ( hasClass ( tabs[ n ], Match ) )
		{
			if ( tabs[ n ].id == getCookie ( key + 'activeTab' ) || !getCookie ( key + 'activeTab' ) )
			{
				tabs[ n ].className = Match + 'Active';
				activatePage ( tabs[ n ].id.replace ( Match, 'page' ), tabs[ n ].parentNode );
				setCookie ( key + 'activeTab', tabs[ n ].id );
				activetab = tabs[n];
			}			
			tabs[ n ].onclick = function ( )
			{
				var pid = this.id.replace ( Match, 'page' );
				activateTab ( this, this.parentNode, vartype );
				setCookie ( key + 'activeTab', this.id );
			}
			lasttab = tabs[ n ];
		}
	}
	if ( !activetab && lasttab )
	{
		lasttab.className = Match + 'Active';
		activatePage ( lasttab.id.replace ( Match, 'page' ), lasttab.parentNode );
		setCookie ( key + 'activeTab', lasttab.id );
	}
}
/**
 * Activate a tab - will also activate the corresponding page
**/
function activateTab ( element, container, vartype )
{
	var ele;
	var Match = "tab";
	if ( vartype == "subtabs" )
		Match = "subTab";

	if ( typeof ( element ) == "object" ) ele = element;
	else ele = document.getElementById ( element );
		
	if ( typeof ( container ) != "object" )
	{
		if ( element && typeof ( element ) == "object" )
			container = element.parentNode;
		else if ( ele && typeof ( ele ) == "object" )
			container = ele.parentNode;
		else
			container = document.getElementById ( container );
	}	
	
	if ( !ele ) return;
	
	var tabs = container.getElementsByTagName ( "DIV" );
	ele.className = Match + "Active";
	for ( var n = 0; n < tabs.length; n++ )
	{
		if ( tabs[ n ].parentNode != container ) continue;
		if ( tabs[ n ].id != ele.id && tabs[ n ].className.substr ( 0, Match.length ) == Match )
			tabs[ n ].className = Match;
	}
	activatePage ( getDivById ( ele.id.replace ( Match, 'page' ), container ), container );
}
/** 
 * This activates a page, used bu activateTab()
**/
function activatePage ( element, container )
{
	var ele; 
	
	if ( typeof ( container ) != "object" )
	{
		if ( typeof ( element ) == "object" )
			container = element.parentNode;
		else
			container = document.getElementById ( container );
	}
	
	if ( !container ) return;
	
	if ( typeof ( element ) == "object" ) ele = element;
	else ele = getDivById ( element, container );
	
	if ( !ele ) return false;
	
	ele.className = "pageActive";
	ele.style.visibility = 'visible';
	
	if ( isIE )
	{
		ele.style.position = 'static';
	}
	else 
	{
		ele.style.position = 'relative';
		ele.style.display = '';
	}
	
	var pages = container.getElementsByTagName ( "div" )
	for ( var n = 0; n < pages.length; n++ )
	{
		if ( pages[ n ].parentNode != container ) continue;
		if ( pages[ n ].id != ele.id && pages[ n ].className.substr ( 0, 4 ) == "page" )
		{
			pages[ n ].className = "page";
			if ( isIE )
			{
				pages[ n ].style.visibility = 'hidden';
				pages[ n ].style.position = 'absolute';
			}
			else pages[ n ].style.display = 'none';
		}
	}
	
}
// Done new tab support

/**
 * Modal dialogues --------------------------------------------------
**/

var dialogCount = 0;
var ModalDialogue = function ( )
{
	// Internal variables
	this._fademode = '';
	this._speed = 500;
	this._phase = 0;
	
	// Setup background
	this.Background = document.createElement ( "DIV" );
	this.DiaBox = document.createElement ( "DIV" );
	
	this.Background.style.position = 'fixed';
	this.Background.style.top = '0px';
	this.Background.style.left = '0px';
	this.Background.style.width = '100%';
	this.Background.style.height = isIE6 ? ( getDocumentHeight ( ) + 'px' ) : '100%';
	this.Background.style.background = '#043355';
	this.Background.style.zIndex = 2001 + ( dialogCount * 2 );
	this.Background.onclick = function () { return false; };
	this.Background.onmousedown = function () { return false; };
	this.Background.style.cursor = 'busy';
	
	this.DiaBox.style.position = 'fixed';
	this.DiaBox.style.background = '#eeeeee';
	this.DiaBox.style.zIndex = 2001 + ( dialogCount * 2 );
	this.DiaBox.style.mozBorderRadius = '2px';
	this.DiaBox.style.webkitBorderRadius = '2px';
	
	// Setup foreground
	this.Foreground = document.createElement ( "DIV" );
	this.Foreground.style.position = 'fixed';

	this.Foreground.style.zIndex = 2002 + ( dialogCount * 2 );
	this.Foreground.className = 'Container ModalContents';
	this.Foreground.style.padding = '2px';

	this.setWidth = function ( varwidth )
	{
		if ( varwidth )
		{
			this.Foreground.style.width = Math.round ( varwidth ) + "px";
			this.Foreground.style.left = Math.round ( ( getDocumentWidth ( ) / 2 ) - ( Math.round ( varwidth ) / 2 ) ) + "px";
			this.Width = Math.round ( varwidth );
		}
	}
	
	this.setHeight = function ( varheight )
	{
		if ( varheight )
		{
			if ( isIE6 )
			{
				var t = this;
				addEvent ( 'onscroll', function ( )
				{
					t.Foreground.style.height = Math.round ( varheight ) + "px";
					var top = Math.round ( ( getDocumentHeight ( ) / 2 ) - ( Math.round ( varheight ) / 2 ) );
					var bgTop = 0;
					if( getScrollTop() > 0 )
					{
						top += getScrollTop();
						bgTop = getScrollTop();
					}
					t.Foreground.style.top = top + 'px'; 
					t.Background.style.top = bgTop + 'px'; 
				} );
				document.onscroll();
			}
			else
			{
				var top = Math.round ( ( getDocumentHeight ( ) / 2 ) - ( Math.round ( varheight ) / 2 ) );
				this.Foreground.style.top = top + 'px';
			}
			this.Height = Math.round ( varheight );
		}
	}

	this.init = function ( )
	{
		this._container = document.createElement ( 'div' );
		document.body.insertBefore ( this._container, document.body.firstChild  );
		
		if ( !this.contenturl ) removeModalDialogue ( this.Name );
		this.jax = new bajax ( );
		this.jax.modal = this;
		this.jax.openUrl ( this.contenturl, 'get', true );
		this.jax.onload = function ( )
		{
			this.modal._content = this.getResponseText ( );
			
			// Lost session? To main page!
			if ( this.modal._content.indexOf ( 'loginUsername' ) >= 0 )
			{
				var bref = document.getElementsByTagName ( 'base' )[0].href;
				if ( document.getElementById ( 'MetaButtons' ) )
					bref += 'admin.php';
				removeModalDialogue ( this.Name );
				document.location = bref;
				return false;
			}
			
			// Extract scripts
			this.modal._scripts = extractScripts ( this.modal._content );
			
			this.modal._renderBackground ( );
			this.modal._tm = ( new Date () ).getTime ();
			if ( this.modal.queuefunc )
			{	
				this.modal._fadein ( 
					new Array ( 
						this.modal.InstanceName + '._renderForeground ( )', 
						this.modal.InstanceName + '.queuefunc ( )' 
					) 
				);
			}
			else
			{
				this.modal._fadein ( this.modal.InstanceName + '._renderForeground ( )' );
			}
			dialogCount++;
		}
		this.jax.send ( );
	}
	
	this._removeBackground = function ( )
	{
		if ( this._container && this.Background )
			this._container.removeChild ( this.Background ); 
	}
	
	this._removeForeground = function ( )
	{
		if ( ge ( 'ModalForeground' ).intr )
		{
			clearInterval ( ge ( 'ModalForeground' ).intr );
		}
		this._container.removeChild ( this.Foreground );
	}
	
	this._renderBackground = function ( )
	{
		this._container.appendChild ( this.Background );
		setOpacity ( this.Background, 0 );
		this._container.appendChild ( this.DiaBox );
	}
	this._renderForeground = function ( )
	{
		this.Foreground.obj = this;
		this.Foreground.onmousedown = function ()
		{
			document._tempForeground = this.obj;
			setTimeout ( 'try { document._tempForeground.refresh(); document._tempForeground = false; } catch(e){};', 250 );
		}
		this.Foreground.innerHTML = '<div id="ModalForeground" class="SubContainer" style="display: block; overflow: auto;">' + this._content + '</div>';
		this._container.appendChild ( this.Foreground );
		
		// If we have scripts queued to be executed, execute!!
		if ( this._scripts )
		{
			for ( var a = 0; a < this._scripts.length; a++ )
			{
				if ( this._scripts[ a ] )
				{
					try
					{
						eval.call ( window, this._scripts[ a ] );
					}
					catch ( e ){ window.execScript ( this._scripts[ a ] ); };
				}
			}
		}
		this.refresh ();
		document.getElementById ( 'ModalForeground' ).modal = this;
		document.getElementById ( 'ModalForeground' ).intr = 
			setInterval ( "try { document.getElementById ( 'ModalForeground' ).modal.refresh(); } catch (e){};", 250 );
	}
	
	this._fadein = function ( varfunc )
	{
		if ( !varfunc ) varfunc = '';
		if ( this._fademode != '' && this._fademode != 'in' )
			return;
		this._fademode = 'in';
		
		var diff = ( ( new Date () ).getTime () - this._tm ) / this._speed;
		this._phase = diff > 1 ? 1 : Math.sin ( diff * 0.5 * Math.PI );
		var timeout = '';
		
		if ( this._phase < 1 )
		{
			this.Background.style.cursor = 'busy';
			this.DiaBox.style.cursor = 'busy';
			timeout = this.InstanceName + "._fadein ( '" + varfunc + "' )";
		}
		else 
		{
			this.Background.style.cursor = 'default';
			this.DiaBox.style.cursor = 'default';
			this._fademode = '';
			if ( varfunc )
			{
				if ( typeof ( varfunc ) == "array" )
				{
					for ( var z = 0; z < varfunc.length; z++ )
						timeout = varfunc[ z ];
				}
				else timeout = varfunc;
			}
		}
		if ( this._phase == 1 )
		{
			setOpacity ( this.Background, 0.6 );
			this.DiaBox.className = 'DialogueBox';
		}
		else setOpacity ( this.Background, this._phase * 0.6 );
		this.refresh ();
		if ( timeout.length )
			setTimeout ( timeout, timeout.indexOf ( 'fadein' ) >= 0 ? 5 : 5 );
	}
	
	this.refresh = function ( )
	{
		if ( this.Foreground.innerHTML.length > 0 )
			this.Height = getElementHeight ( this.Foreground );
		this.setHeight ( this.Height );
		
		var BgWidth = getElementHeight ( this.Background );
		var BgHeight = getElementWidth ( this.Background );
		var BorderWidth = 12 * this._phase;
		var BorderHeight = 6 * this._phase;
		var dOffX = 3 - ( getVerticalScrollbarWidth ( ) * 0.5 );
		var dOffY = 3;
		var dWidth = Math.round ( this.Width * this._phase );
		var dHeight = Math.round ( this.Height * this._phase );
		
		this.DiaBox.style.top = ( Math.round ( BgWidth * 0.5 - ( dHeight * 0.5 ) ) - dOffY + ( isIE6 ? getScrollTop ( ) : 0 ) ) + 'px';
		this.DiaBox.style.left = ( Math.round ( BgHeight * 0.5 - ( dWidth * 0.5 ) ) - dOffX ) + 'px';
		this.DiaBox.style.width = ( dWidth+BorderWidth ) + 'px';
		this.DiaBox.style.height = ( dHeight+BorderHeight ) + 'px';
	}
	
	this._fadeout = function ( varfunc )
	{
		if ( !varfunc ) varfunc = '';
		if ( this._fademode != '' && this._fademode != 'out' )
			return;
		this._fademode = 'out';
		var diff = ( ( new Date () ).getTime () - this._tm ) / this._speed;
		this._phase = 1 - ( diff > 1 ? 1 : Math.pow ( diff, 3 ) );
		var timeout = '';
		
		if ( this._phase > 0 )	
		{
			this.refresh ();
			this.Background.style.cursor = 'busy';
			timeout = this.InstanceName + "._fadeout ( \"" + varfunc + "\" )";
		}
		else 
		{
			this.DiaBox.style.display = 'none';
			this._fademode = '';
			if ( varfunc ) timeout = varfunc;
		}
		
		if ( timeout.length )
			setTimeout ( timeout, timeout.indexOf ( 'fadeout' ) >= 0 ? 5 : 5 );
	}
}

function removeModalDialogue ( varname )
{
	if ( !document.modaldialogues )
		return false;
	for ( var a = 0; a < document.modaldialogues.length; a++ )
	{
		if ( document.modaldialogues[ a ].Name == varname )
		{
			document.modaldialogues[ a ]._removeForeground ( );
			document.modaldialogues[ a ]._tm = ( new Date () ).getTime ();
			document.modaldialogues[ a ]._fadeout ( "_removeModalDialogue ( '" + varname + "' )" );
			setOpacity ( document.modaldialogues[ a ].Background, 0 );
			dialogCount--;
			return true;
		}
	}
	return false;
}

function _removeModalDialogue ( varname )
{
	var outar = new Array ( );
	for ( var a = 0; a < document.modaldialogues.length; a++ )
	{
		if ( document.modaldialogues[ a ].Name != varname )
		{
			var dex = outar.length;
			outar[ dex ] = document.modaldialogues[ a ];
			outar[ dex ].InstanceName = 'document.modaldialogues[ ' + dex + ' ]';
			outar[ dex ].Index = dex;
		}
		else
		{
			document.modaldialogues[ a ]._removeBackground ( );
		}
	}
	document.body.style.overflow = 'auto';
	document.modaldialogues = outar;
}

function replaceModalDialogue ( varname, width, height, contenturl, queuefunc )
{
	if ( !queuefunc ) queuefunc = 0;
	document.repljax = new bajax ( );
	document.repljax.openUrl ( contenturl, 'get', true );
	document.repljax.queuefunc = queuefunc;
	document.repljax.onload = function ( )
	{
		for ( var a = 0; a < document.modaldialogues.length; a++ )
		{
			if ( document.modaldialogues[ a ].Name == varname )
			{
				document.modaldialogues[ a ]._removeForeground ( );
				document.modaldialogues[ a ].setWidth ( width );
				document.modaldialogues[ a ].setHeight ( height );
				document.modaldialogues[ a ]._content = this.getResponseText ( );
				document.modaldialogues[ a ]._renderForeground ( );
				document.modaldialogues[ a ].refresh ();
			}
		}	
		if ( this.queuefunc )
			this.queuefunc ( );
	}
	document.repljax.send ( );
}

var ModalData = new Object ( );
function initModalDialogue ( varname, width, height, contenturl, queuefunc )
{
	if ( !queuefunc ) queuefunc = false;
	removeModalDialogue ( varname );
	if ( !document.modaldialogues )
		document.modaldialogues = new Array ( );
	
	var dex = document.modaldialogues.length;
	document.modaldialogues[ dex ] = new ModalDialogue ( );
	document.modaldialogues[ dex ].setWidth ( width );
	document.modaldialogues[ dex ].setHeight ( height );
	document.modaldialogues[ dex ].contenturl = contenturl;
	document.modaldialogues[ dex ].Index = dex;
	document.modaldialogues[ dex ].Name = varname;
	document.modaldialogues[ dex ].InstanceName = "document.modaldialogues[ " + dex + " ]";
	document.modaldialogues[ dex ].queuefunc = queuefunc;
	document.modaldialogues[ dex ].init ();
	
	return document.modaldialogues[ dex ];
}

function resizeModalDialogue ( name, width, height )
{
	if ( name && ( width || height ) )
	{
		for ( var a = 0; a < document.modaldialogues.length; a++ )
		{
			var m = document.modaldialogues[ a ];
			if ( m.Name == name )
			{
				if ( width )
					m.setWidth ( width );
				if ( height )
					m.setHeight ( height );
				m.refresh ();
				break;
			}
		}
	}
}

/* Lists of unique values */
var arenaUniqueList = function ( varname )
{
	this.Name = varname;
	this.Entries = new Array ( );
	this.addEntry = function ( val )
	{
		for ( var a = 0; a < this.Entries.length; a++ )
			if ( this.Entries[ a ] == val ) return false;
		this.Entries[ this.Entries.length ] = val;
		return true;
	}
	this.remEntry = function ( val )
	{
		var outAr = new Array ( );
		for ( var a = 0; a < this.Entries.length; a++ )
			if ( this.Entries[ a ] != val )
				outAr[ outAr.length ] = this.Entries[ a ];
		this.Entries = outAr;
		return true;
	}
}
function addToUniqueList ( vname, val )
{
	if ( !document.arenalists )
		document.arenalists = new Array ( );
	var lst = false;
	for ( var a = 0; a < document.arenalists.length; a++ )
		if ( document.arenalists[ a ].Name == vname )
			lst = document.arenalists[ a ];
	if ( !lst )
	{
		document.arenalists[ document.arenalists.length ] = new arenaUniqueList ( vname );
		lst = document.arenalists[ document.arenalists.length - 1 ];
	}	
	return lst.addEntry ( val );
}
function remFromUniqueList ( vname, val )
{
	if ( !document.arenalists )
		document.arenalists = new Array ( );
	var lst = false;
	for ( var a = 0; a < document.arenalists.length; a++ )
		if ( document.arenalists[ a ].Name == vname )
			lst = document.arenalists[ a ];
	if ( !lst )
		return false;
	return lst.remEntry ( val );
}
function getUniqueListEntries ( vname )
{
	if ( !document.arenalists )
		document.arenalists = new Array ( );
	var lst = false;
	for ( var a = 0; a < document.arenalists.length; a++ )
		if ( document.arenalists[ a ].Name == vname )
			lst = document.arenalists[ a ];
	if ( !lst )
		return false;
	if ( lst.Entries.length )
		return lst.Entries;
	return false;
}

/**
 * Toggle an elements visibility
**/
function ToggleContents ( varelements, key )
{
	if ( !varelements ) return;
	var eles;
	if ( varelements.toLowerCase ( ) == "left" )
		eles = Array ( "ColumnLeftTh", "ColumnLeftTd" );
	if ( varelements.toLowerCase ( ) == "right" )
		eles = Array ( "ColumnRightTh", "ColumnRightTd" );
	var mode = '';
	
	for ( var a = 0; a < eles.length; a++ )
	{
		var varelement;
		if ( !( varelement = document.getElementById ( eles[ a ] ) ) ) continue;
		
		if ( !varelement.originalSize ) 
		{
			var elwidth = varelement.style.width + "";
			elwidth = elwidth.replace ( "%", "" );
			elwidth++; elwidth--;
			varelement.originalSize = elwidth;
		}
		
		var midEle = varelement.id.indexOf ( 'Th' ) > 0 ? document.getElementById ( 'ColumnMiddleTh' ) : document.getElementById ( 'ColumnMiddleTd' );
		if ( !midEle.originalSize ) 
		{
			var curWidth = midEle.style.width + "";
			curWidth = curWidth.replace ( "%", "" );
			curWidth++; curWidth--;
			midEle.originalSize = curWidth;
			midEle.shownSize = curWidth;
		}
		
		
		if ( !varelement.hidden )
		{
			varelement.hidden = true;
			varelement.display = varelement.style.display;
			varelement.style.display = 'none';
			midEle.shownSize += varelement.originalSize;
			mode = 'hidden';
		}
		else
		{
			varelement.hidden = false;
			varelement.style.display = varelement.display;
			midEle.shownSize -= varelement.originalSize;
			mode = 'shown';
		}
		midEle.style.width = midEle.shownSize + "%";
	}
	setCookie ( key + varelements.toLowerCase ( ), mode );
}

/* MouseOver lib  */
var toolTip = function ( )
{
	this.Visible = false;
	this.Rects = new Array ( );
	this.Counter = 0;
	this.Node = document.createElement ( 'DIV' );
	this.Node.id = "ToolTipRect";
	if ( isIE )
		this.Node.style.position = 'absolute';
	else
		this.Node.style.position = 'fixed';
	this.Node.style.visibility = 'hidden';
	this.Node.style.top = '0px';
	this.Node.style.left = '0px';
	this.Node.style.height = '4px';
	this.Node.style.zIndex = '10000';
	if ( isIE )
		this.Node.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity=90)';
	else
		this.Node.style.opacity = 0.9;
	var tc = ge ( 'TopLevelContainer' ) ? ge ( 'TopLevelContainer' ) : ge ( 'Empty__' );
	tc.appendChild ( this.Node );
	
	this.brain = function ( )
	{
		/* 
			Check that we're not over an element. If we are, then continue to show
			tooltip. If we aren't then give a little timeout and hide. Onmouseout doesn't
			always get triggered, therefor this code is needed.
		*/
		this.Counter --;
		if ( this.Counter <= 0 )
		{
			this.Counter = 0;
			for ( var a = 0; a < this.Rects.length; a++ )
			{
				if ( 
					mousex >= this.Rects[ a ].left && mousex < this.Rects[ a ].left + this.Rects[ a ].width &&
					mousey >= this.Rects[ a ].top && mousey < this.Rects[ a ].top + this.Rects[ a ].height
				)
				{
					this.Counter += 1;
					break;
				}
			}
			if ( this.Counter <= 0 )
				this.hide ();
		}
		setTimeout ( 'document.toolTip.brain ( )', 500 );
	}
	
	this.show = function ( )
	{
		this.Node.style.width = '180px'; 
		this.Node.style.height = 'auto';
		this.Node.innerHTML = '<div><p><strong>' + this.Header + '</strong></p>' + ( this.Desc ? ( '<p>' + this.Desc + '</p></div>' ) : '' );
		this.Visible = true; 
		this.move ( );
		this.Node.style.visibility = 'visible';
		this.Node.style.height = 'auto';
		this.Counter = 2;
	}
	this.hide = function ( )
	{
		this.Node.style.visibility = 'hidden';
		this.Node.style.width = '1px'; this.Node.style.height = '4px';
		this.Visible = false;
	}
	this.move = function ( )
	{
		if ( this.Visible )
		{
			/* Make rect stick to bounds */
			var Width = this.Node.style.width + "";

			Width = Width.replace ( "px", "" ); 
			Width++; Width--;

			var MaxLeft = getElementWidth ( document.body );
			var Left = mousex - 90; 
			if ( Left < 2 ) Left = 2;
			if ( Left + Width >= MaxLeft )
				Left = MaxLeft - Width - 24;

			var Top = mousey + ( isIE ? 16 : ( 16 - getScrollTop ( ) ) );

			if ( isIE )
			{
				if ( ( Top + getElementHeight ( this.Node ) ) - getScrollTop ( ) > getElementHeight ( document.body ) )
					Top = mousey - getElementHeight ( this.Node ) - 10;
			}
			if ( !isIE )
			{
				if ( ( Top + getElementHeight ( this.Node ) ) > ( ( getScrollTop ( ) + window.innerHeight ) ) )
					Top = mousey - getElementHeight ( this.Node ) - 10 - getScrollTop ( );
			}

			if ( isNaN ( Top ) )
			{
				// Do nothing
			}
			else
			{		
				// Position
				this.Node.style.top = Top + 'px';
				this.Node.style.left = Left + 'px';
			}
		}
	}
	
	//	Adds to rectlist so that we can keep track of our tip elements' positions
	this.addRect = function ( ele )
	{
		var Rect = new Object ();
		Rect.width = getElementWidth ( ele );
		Rect.height = getElementHeight ( ele );
		Rect.top = getElementTop ( ele );
		Rect.left = getElementLeft ( ele );
		this.Rects[ this.Rects.length ] = Rect;
	}
	
	this.brain ( );
}

addOnload ( function ( )
{
	var t = ge ( 'TopLevelContainer' ) ? ge ( 'TopLevelContainer' ) : ge ( 'Empty__' );
	if ( t )
	{
		document.toolTip = new toolTip ( );
		addEvent ( 'onmousemove', function ( ) { document.toolTip.move ( ) } );
	}
} );

function addToolTip ( varTitle, varDesc, varEle )
{
	if ( !varEle ) return;
	if ( typeof ( varEle ) != "object" )
		varEle = document.getElementById ( varEle );
	if ( !varEle ) return;
	
	varEle.onmouseover = function ( )
	{
		if ( document.toolTip )
		{
			document.toolTip.Header = varTitle;
			document.toolTip.Desc = varDesc;
			document.toolTip.show ( );
			document.toolTip.addRect ( this );
		}
	}
	varEle.onmouseout = function ( )
	{
		if ( document.toolTip )
		{
			document.toolTip.hide ( );
		}
	}
}

/**
 * Makes a two state box
**/
function swapToggleVisibility ( ele1, ele2 )
{
	if ( ele1 && ele2 )
	{
		ele1.style.visibility = 'hidden';
		ele1.style.position = 'absolute';
		ele1.style.top = '-10000px';
		ele1.style.left = '-10000px';
		
		ele2.style.position = 'relative';
		ele2.style.visibility = 'visible';
		ele1.style.top = 'auto';
		ele1.style.left = 'auto';
		
		/*
			Yes internet explorer is this wierd!!!!
		*/
		if ( isIE )
		{
			var w = getElementWidth ( ele2 );
			if ( typeof ( w ) != 'undefined' )
			{
				if ( w == 0 ) ele2.style.width = '100%';
				else ele2.style.width = w + 'px';
			}
			ele1.style.top = '-2000px';
			ele1.style.left = '-2000px';
			ele2.style.top = 'auto';
			ele2.style.left = 'auto';
			
			// Internet explorer needs to be fed more info..
			var textareas = ele2.getElementsByTagName ( 'textarea' );
			if ( textareas.length )
			{
				for ( var a = 0; a < textareas.length; a++ )
				{
					if ( textareas[ a ].className == 'mceSelector' ) 
					{
						var el = document.getElementById ( textareas[ a ].id + '_hidden' );
						if ( el && el.innerHTML )
						{
							var ed = editor.get ( textareas[ a ].id );
							if ( ed ) ed.setContent ( el.innerHTML );
						}
					}
				}
			}
		}
		else
		{
			if ( typeof ( editor ) == 'undefined' ) return;
			// Add control on elements that we are switching to shown state
			var textareas = ele2.getElementsByTagName ( 'textarea' );
			for ( var a = 0; a < textareas.length; a++ )
			{
				if ( textareas[ a ].className == 'mceSelector' )
				{
					editor.addControl ( textareas[ a ].id );
				}
			}
		
			// Remove control on elements that we are switching to hidden state
			textareas = ele1.getElementsByTagName ( 'textarea' );
			for ( var a = 0; a < textareas.length; a++ )
			{
				if ( textareas[ a ].className == 'mceSelector' )
				{
					if ( editor.get ( textareas[ a ].id ) )
					{
						editor.removeControl ( textareas[ a ].id );
					}
				}
			}
		}
	}
}
function initToggleBoxes ( element )
{
	var boxes = element.getElementsByTagName ( 'DIV' );
	for ( var a = 0; a < boxes.length; a++ )
	{
		if ( boxes[ a ].className == 'ToggleBox' )
		{
			var view, edit; var c = 0;
			for ( var b = 0; b < boxes.length; b++ )
			{
				if ( boxes[ b ].parentNode != boxes[ a ] ) continue;
				if ( c == 0 ) view = boxes[ b ];
				else { edit = boxes[ b ]; break; }
				c++;
			}
			if ( isIE ) view.style.cursor = 'hand'; else view.style.cursor = 'pointer';
			if ( edit )
			{
				swapToggleVisibility ( edit, view);
				view.sibling = edit; edit.sibling = view;
				view.onclick = function ( ) { swapToggleVisibility ( this, this.sibling ); }
				boxes[ a ].style.position = 'relative';
				boxes[ a ].style.visibility = 'visible';
			}
			else alert ( element.id + ' feilet i å laste inn sin edit boks!' );
		}
	}
}


/**
* Set opacity on an element (compatible with most, cross browser (except konqueror))
**/
function setOpacity ( ele, op )
{
	if ( !ele ) return;
	if ( ele.opacity && ele.opacity == op ) return;
	if ( op == false ) op = 0
	
	ele.opacity = op;
	
	if ( isIE ) 
	{
		if ( op == 1 )
			ele.style.filter = null; // gives back antialiasing properly..
		else ele.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(Opacity=' + Math.round( op * 100 ) + ')';
	}
	else ele.style.opacity = op;
}
function getOpacity ( ele )
{
	if ( !ele ) return;
	if ( isIE )
	{
		if ( ele.style.filter )
		{
			var o = ele.style.filter.split ( "Opacity=" );
			return parseInt ( o[1].split ( ')' )[0] ) * 0.01;
		}
		return false;
	}
	else
	{
		return parseInt ( ele.style.opacity );
	}
	return false;
}

/**
 * Return an array of the script code
**/
function extractScripts ( html )
{
	var scripts;
	var endscripts = new Array ( );
	while ( scripts = html.match ( /\<script[^>]*?\>([\w\W]*?)\<\/script\>/i ) )
	{
		endscripts.push ( scripts[1] );
		html = html.split ( scripts[0] ).join ( '' );
	}
	return endscripts;
}

/**
 * Remove scripts
**/
function removeScripts ( html )
{
	var tc = html.split ( '<script' );
	for ( var a = 0; a < tc.length; a++ )
		html = html.replace ( /<script\b[^>]*>(.*?)<\/script>/i, '' );
	return html;
}

/**
 * Give back a string that is safe for showing without obstructing form elements
**/
function ArenaTextfieldSafe ( string )
{
	string = replaceFromTo ( '<input', '>', '<hr style="width: 200px; height: 20px; background: #eee; border: 1px solid #aaa" />', string );
	string = replaceFromTo ( '<textarea', '</textarea>', '<hr style="width: 200px; height: 20px; background: #eee; border: 1px solid #aaa" />', string );
	string = replaceFromTo ( '<select', '</select>', '<hr style="width: 200px; height: 20px; background: #eee; border: 1px solid #aaa" />', string );
	string = replaceFromTo ( '<form', '>', '', string );
	string = str_replace ( '</form>', '', string );
	return string;
}

// Replace all occurrances of a string
function str_replace ( varSource, varDestination, varValue )
{		
	varValue += "";
	varValue = varValue.split ( varSource );
	varValue = varValue.join ( varDestination );
	return varValue;		
}

// Replace all occurances of a string with regular expressions
function replaceFromTo ( varSource, varDestination, replacement, varValue )
{
	var ostr = '';
	var disabled = false;
	var vlen = varSource.length;
	var dlen = varDestination.length;
	for ( var a = 0; a < varValue.length; a++ )
	{
		if ( varValue.substr ( a, vlen ) == varSource )
		{
			disabled = true;
			a += varSource.length - 1;
			ostr += replacement;
		}
		else if ( disabled && varValue.substr ( a, dlen ) == varDestination )
		{
			disabled = false;
			a += varDestination.length - 1;
		}
		else if ( !disabled ) 
		{
			ostr += varValue.substr ( a, 1 );
		}
	}
	return ostr;
}

/** 
 * Get current translation of this one
**/
function trans ( key )
{
	if ( typeof ( document.translations ) == 'object' )
	{
		if ( document.translations[ key ] )
			return document.translations[ key ];
	}
	return key;
}


/** 
 * Get current translation of this one
**/
function i18n ( key )
{
	if ( typeof ( document.translations ) == 'object' )
	{
		if ( document.translations[ key ] )
			return document.translations[ key ];
	}
	return key;
}

/**
 * Show a simple styled dialog
**/
var _oldBodyOverflow = '';
function styledDialog ( url, id )
{
	if ( !id ) id = 'dialog' + Math.round ( ( Math.random ( ) * 100 ) );
	var blocker = document.createElement ( 'div' );
	blocker.style.position = 'absolute';
	document.currentStyledDialog = blocker;
	var top = getScrollTop ();
	var width = getDocumentWidth ( );
	var height = getDocumentHeight ( );
	blocker.style.top = top + 'px';
	blocker.style.left = '0px';
	blocker.style.width = width + 'px';
	blocker.style.height = height + 'px';
	blocker.style.zIndex = '20001';
	blocker.id = id;
	
	_oldBodyOverflow = document.body.style.overflow ? document.body.style.overflow : 'auto';
	document.body.style.overflow = 'hidden';
	
	var blockerbg = document.createElement ( 'div' );
	blockerbg.className = 'StyledDialogBackground';
	blockerbg.style.width = width + 'px';
	blockerbg.style.height = height + 'px';
	setOpacity ( blockerbg, 0.5 );
	blocker.appendChild ( blockerbg );
	
	var element = document.createElement ( 'div' );
	element.className = 'StyledDialogPosition';
	blocker.appendChild ( element );
	
	var textcont = document.createElement ( 'div' );
	textcont.className = 'StyledDialogContainer';
	element.appendChild ( textcont );

	document.getElementById ( 'Empty__' ).appendChild ( blocker );	
	
	var base = document.getElementsByTagName ( 'base' );
	document.fjax = new bajax ( );
	document.fjax.openUrl ( base[ 0 ].href + url, 'get', true );
	document.fjax.onload = function ( )
	{
		textcont.innerHTML = this.getResponseText ( );
		document.fjax = 0;
	}
	document.fjax.send ( );
}
function closeStyledDialog ( id )
{
	if ( !id ) id = false;
	document.body.style.overflow = _oldBodyOverflow;
	document.currentStyledDialog.parentNode.removeChild ( document.currentStyledDialog );
}

function parseDouble ( vari )
{
	vari++; vari--;
	return Math.floor ( vari * 10 ) / 10;
}

// Checks if a plugin is available on name
function hasPlugin ( pluginName )
{
	// TODO: Fix ie stuff
	if ( isIE )
		return false;
	else
	{
		for ( var a = 0; a < navigator.plugins.length; a++ )
		{
			if ( pluginName.toLowerCase ( ) == navigator.plugins[ a ].name.toLowerCase ( ) )
				return true;
		}
		return false;
	}
}

// Inserts a tag in html at start (offset) to end (offset) with tag strs(start) and tag strr(end)
// Returns the resulting modified html. The offsets are in the clean readable text, not including
// entities and html code
function insertTagInHTML ( html, start, end, strs, stre )
{
	var mode = 0;
	var counted = 0; // chars counted by tinymce
	var modes = new Array ( 0 );
	var spos = 0; // start of replacement
	var epos = 0; // end of replacement
	var ostr = ''; // for debug output
	var tagChars = 0; // counts how many chars has been in the tag that is being read progressively
	
	/*
		Modes: 
		1: Inside a tag
		2: Inside an entity
	*/
	
	for ( var a = 0; a < html.length; a++ )
	{
		switch ( html.substr ( a, 1 ) )
		{
			case "\r":
				counted--;
				break;
			case '<':
				tagChars = 0;
				
				var singletag = false;
				for ( var b = a; b < html.length; b++ )
				{
					if ( html.substr ( b, 2 ) == '/>' )
					{
						singletag = true;
						break;
					}
					if ( html.substr ( b, 1 ) == '>' )
						break;
				}
					
				modes.push ( 1 );
				
				break;
			case '&':
				// don't register "&" if it's not an "&xxxx;", but an "& bla"
				if ( html.substr ( a + 1, 1 ) != ' ' )
				{
					modes.push ( 2 );
					counted++;
				}
				break;
			case ';':
				if ( mode == 2 )
				{
					modes.pop ();
					
					// If we cound the end offset just after a mode2 exit, register epos
					if ( counted == end && !epos ) epos = a;
					continue;
				}
				break;
			case '>':
				if ( mode == 1 )
				{
					modes.pop ();
					
					// If we cound the end offset just after a mode1 exit, register epos
					if ( counted == end && !epos ) epos = a - tagChars;
					continue;
				}
				break;
			default: break;
		}
		mode = modes.length ? modes[ modes.length - 1 ] : 0;
		
		if ( counted == start )
			spos = a;
		
		// If we're in free flow and find end offset, register epos
		if ( counted == end && !epos )
			epos = a - tagChars;
			
		if ( mode == 0 )
		{
			counted++;
			tagChars = 0;
		}
		else if ( mode == 1 )
			tagChars++;
	}
	
	var element = html.substr ( spos, epos - spos );
	return html.substr ( 0, spos ) + strs + element + stre + html.substr ( epos, ( html.length - 1 ) - epos );
}

function stripEmptyTextNodes ( obj )
{
	if ( typeof ( obj.getElementsByTagName ) == 'undefined' )
		return;
	var toRemove = new Array ( );
	var children = obj.getElementsByTagName ( '*' );
	for ( var a = 0; a < obj.childNodes.length; a++ )
	{
		if ( !obj.childNodes[ a ] ) continue;
		if ( obj.childNodes[ a ].nodeName == '#text' )
			toRemove.push ( obj.childNodes[ a ] );
		else stripEmptyTextNodes ( obj.childNodes[ a ] );
	}
	var reg = /[\s]*/;
	for ( var a = 0; a < toRemove.length; a++ )
	{
		if ( !toRemove[ a ].nodeValue.replace ( reg, '' ) )
			obj.removeChild ( toRemove[ a ] );
	}
}

/**
 * This is an editor abstraction that uses either tinymce or blests own editor
**/
var EditorAbstraction = function ( mode, options )
{
	this.mode = mode;
	this.options = { 'mode' : 'normal' };
	if ( !options )
		options = false;
	else
	{
		options = options.split ( ';' );
		for ( var a = 0; a < options.length; a++ )
		{
			var pairs = options[a].split ( ':' );
			if ( pairs.length > 1 )
			{
				this.options[pairs[0]] = pairs[1];
			}
		}
	}
	
	this.getContent = function ( edid )
	{
		if ( this.mode == 'tinymce' )
		{
			return tinyMCE.get ( edid ).getContent ( );
		}
		else if ( this.mode == 'blest' )
		{
			return texteditor.get ( edid ).getContent ( );
		}
	}
	this.addControl = function ( edid )
	{
		if ( this.mode == 'tinymce' )
		{
			return tinyMCE.execCommand ( 'mceAddControl', false, edid );
		}
		else if ( this.mode == 'blest' )
			return texteditor.addControl ( edid );
	}
	
	this.insertImage = function ( image, edid )
	{
		if ( !edid ) edid = false;
		
		if ( this.mode == 'tinymce' )
		{
			tinyMCE.execCommand ( 'arenaImageInsert' );
		}
		else if ( this.mode == 'blest' )
		{
			if ( edid == false )
				edid = texteditor.activeEditorId;
			var ed = texteditor.get ( edid );
			if ( ed )
			{
				if ( ed._lastClickedItem && ed._lastClickedItem.nodeName.toLowerCase () == 'img' )
				{
					var i = document.createElement ( 'img' );
					var s;
					var newWidth = ed._lastClickedItem.style.width;
					var newHeight = ed._lastClickedItem.style.height;
					var attrs = [], styles = [];
					image = image.split ( "'" ).join ( '"' );
					while ( s = image.match (/([a-z]*?)\=\"([^"]*?)\"/i) )
					{
						image = image.split ( s[0] ).join ( '' );
						attrs.push ( [ s[1], s[2] ] );
					}
					// Get new width and height
					for ( var z = 0; z < attrs.length; z++ )
					{
						// Retrieve style
						if ( attrs[z][0].toLowerCase() == 'style' )
						{
							var sattrs = attrs[z][1].split ( ';' );
							for ( var b = 0; b < sattrs.length; b++ )
							{
								var pair = sattrs[b].split ( ':' );
								var k = ( pair[0].split(' ').join('').toLowerCase() );
								if ( k == 'width' ) newWidth = pair[1].split ( ' ' ).join ( '' );
								else if ( k == 'height' ) newHeight = pair[1].split ( ' ' ).join ( '' );
							}
						}
						i.setAttribute ( attrs[z][0], attrs[z][1] );
					}
					// Set old style -----
					var oldStyleS = ed._lastClickedItem.getAttribute ( 'style' );
					if ( isIE && oldStyleS.cssText ) oldStyleS = oldStyleS.cssText.toLowerCase();
					var oldStyle = [];
					// retrieve style
					if ( oldStyleS )
					{
						oldStyleS = oldStyleS.split ( ';' );
					
						for ( var b = 0; b < oldStyleS.length; b++ )
						{
							var pair = oldStyleS[b].split ( ':' );
							if ( pair[0].split ( ' ' ).join ( '' ) == '' ) continue;
							var k = ( pair[0].split(' ').join('').toLowerCase() );
							var v = pair[1].split ( ' ' ).join ( '' ); 
							if ( k == 'width' || k == 'height' ) continue;
							if ( isIE ) k = k.toUpperCase ();
							if ( isIE ) v = v.toUpperCase ();
							i.style[ k ] = v;
						}
					}
					// Set width and height
					if ( newWidth > 0 )
					{
						i.style.width = newWidth;
						i.style.height = newHeight;
					}
					// Replace old image (clicked) with new
					ed._lastClickedItem.parentNode.replaceChild ( i, ed._lastClickedItem );
					// Make image display correctly (anti-universe of MSIE)
					if ( isIE )
					{
						ed.toggleSource ();
						ed.toggleSource ();
					}
					ed._lastClickedItem = false;
					return;
				}
				return ed.insertHTML ( image );
			}
			alert ( 'Du har ikke aktivert redigeringsfeltet\nsom bildet skal inn i.' );
		}
	}
	
	this.removeContent = function ( edid )
	{
		if ( this.mode == 'tinymce' )
			return tinyMCE.execCommand ( 'mceRemoveControl', false, edid );
		else if ( this.mode == 'blest' )
			return texteditor.removeControl ( edid );
	}
	
	this.get = function ( edid )
	{
		if ( this.mode == 'tinymce' )
			return tinyMCE.get ( edid );
		else if ( this.mode == 'blest' )
			return texteditor.get ( edid );
	}
	
	this.removeControl = function ( edid )
	{
		if ( this.mode == 'tinymce' )
		{
			tinyMCE.execCommand ( 'mceRemoveControl', false, edid );
		}
		else if ( this.mode == 'blest' )
		{
			texteditor.removeControl ( edid );
		}
	}
}

function toHex ( dec ) 
{
	var hexCharacters = "0123456789ABCDEF"
	if ( dec < 0 ) return "00";
	if ( dec > 255 ) return "FF";
	var i = Math.floor( dec / 16 );
	var j = dec % 16;
	return hexCharacters.charAt ( i ) + hexCharacters.charAt ( j );
}

var codepressiframe;
function CodePressArena_catchkey ( e )
{
	var cp = codepressiframe.contentWindow.CodePress;
	switch ( e.keyCode )
	{
		case 9:
			e.preventDefault ( );
			codepressiframe.contentWindow.CodePress.insertCode ( "\t" );
			var allEles = codepressiframe.parentNode.parentNode.getElementsByTagName ( '*' );
			for ( var a = 0; a < allEles.length; a++ )
				allEles[ a ].onfocus = function ( ) { codepressiframe.contentWindow.focus ( ); }
		 	codepressiframe.contentWindow.focus ( );
		 	break;
		case 13:
			e.preventDefault();
			var body = codepressiframe.contentWindow.document.body;
			var doc = codepressiframe.contentWindow.document;
			var win = codepressiframe.contentWindow;
			var before = body.innerHTML;
			cp.insertCode ( "\n" );
			var after = body.innerHTML;
			var b = 0; var c = 0;
			var str = '';
			
			// Find where HTML differs now
			for ( var a = 0; a < after.length; a++ )
			{
				if ( before.substr ( a, 1 ) != after.substr ( a, 1 ) )
				{
					// Found place
					b = a;
					c = b;
					break;
				}
			}
			
			// Go to line before
			for ( ; b > 0 && after.substr ( b - 4, 4 ) != "<br>"; b-- ) { }
			
			// Count tabs
			for ( ; b < after.length && after.substr ( b, 4 ) != "<br>"; b++ )
			{
				if ( after.substr ( b, 1 ) == "\t" )
				{
					str += "\t";
				}
			}
			cp.insertCode ( str );
			break;
	}	
}
function CodePressArena_registerKeyUp ( e )
{
	var cp = codepressiframe.contentWindow.CodePress;
	this.area.value = cp.getCode ( );
	if ( this.editorArea )
	{
		this.editorArea.value = this.area.value;
		texteditor.get ( this.editorArea.id ).getDocument ( ).body.innerHTML = this.area.value;
	}
}
function CodePressArenaFunctions ( ifr )
{
	if ( !ifr ) return false;
	var par = ifr.parentNode;
	while ( par.tagName.toLowerCase ( ) != 'div' || par == document.body )
		par = par.parentNode;
	var w = getElementWidth ( par );
	if ( par.style.paddingLeft )
		w -= parseInt ( par.style.paddingLeft );
	if ( par.style.paddingRight )
		w -= parseInt ( par.style.paddingRight );
	if ( w >= 200 ) ifr.style.width = w + 'px';
	else ifr.style.width = '100%';
	
	// Get the original area
	var areaid = ifr.parentNode.getElementsByTagName ( 'textarea' )[0].id.replace ( 'temporary_codepress_', '' );
	areaid = areaid.substr ( 0, areaid.length - 3 );
	var oarea = document.getElementById ( areaid );
	
	if ( ifr.contentWindow.attachEvent )
	{
		ifr.contentWindow.attachEvent ( 'onkeydown', CodePressArena_catchkey );
		ifr.contentWindow.attachEvent ( 'onkeyup', CodePressArena_registerKeyUp );
	}
	else 
	{
		ifr.contentWindow.addEventListener ( 'keydown', CodePressArena_catchkey, true );
		ifr.contentWindow.addEventListener ( 'keyup', CodePressArena_registerKeyUp, true );
	}
	if ( typeof ( CodePress ) != 'undefined' )
	{
		ifr.contentWindow.area = ifr.parentNode.getElementsByTagName ( 'textarea' )[0];
		ifr.contentWindow.editorArea = oarea;
	}
	if ( ifr.attachEvent )
		ifr.attachEvent ( 'onmouseover', function ( ){ codepressiframe = this; } );
	else ifr.addEventListener ( 'mouseover', function ( ){ codepressiframe = this; }, true );
}


// Convert between formats
function dateFormatted ( date, srcformat, destformat )
{
	date = date.split ( ' ' )[0].split ( '-' );
	
	switch ( srcformat )
	{
		case 'no':
			date = Array ( date[ 2 ], date[ 1 ], date[ 0 ] );
			break;
	}
	
	for ( var a = 1; a < date.length; a++ )
	{
		if ( date[ a ].length == 1 )
			date[ a ] = '0' + date[ a ];
	}
	
	var result;
	switch ( destformat )
	{
		case 'no':
			result = date[ 2 ] + '-' + date[ 1 ] + '-' + date[ 0 ];
			break;
		case 'us':
			result = date.join ( '-' );
			break;
	}
	return result;
}

// Pad a dings
function StrPad ( str, pad, sign )
{
	str = str + '';
	while ( str.length < pad )
	{
		str = sign + str;
	}
	return str;
}

// Quick replacement of getElementByID and getElementsByClassName
function ge ( elemnt, type )
{
	if ( !type ) type = '*';
	// Try classname
	if ( elemnt.substr(0,1) == '.' )
	{
		var eles = document.getElementsByTagName ( type );
		var out = new Array ();
		var cl = elemnt.substr ( 1, elemnt.length - 1 );
		for ( var a = 0; a < eles.length; a++ )
		{
			var nams = eles[a].className.split ( ' ' );
			for ( var c = 0; c < nams.length; c++ )
				if ( nams[c] == cl ) out.push ( eles[a] );
		}
		return out;
	}
	return document.getElementById ( elemnt );
}

