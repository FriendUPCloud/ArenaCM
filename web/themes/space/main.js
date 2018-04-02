
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
New code is (C) 2011 Idéverket AS, 2015 Friend Studios AS

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
                Rune Nilssen
*******************************************************************************/


/******************************************************************************/
/** ARENA 2 Theme Helper Functions v0.1                                      **/
/** (c) 2009 Blest AS                                                        **/
/******************************************************************************/

/** Some practical functions **************************************************/

function getobjdimensions ( obj )
{
	var top = 0;
	var left = 0; 
	var width = 0; 
	var height = 0;
	
	if ( obj.offsetWidth )
	{
		width = obj.offsetWidth;
		height = obj.offsetHeight;
	}
	else 
	{
		width = obj.clientWidth;
		height = obj.clientHeight;
	}
	do
	{
		top += obj.offsetTop;
		left += obj.offsetLeft;
	}
	while ( obj = obj.offsetParent );
	
	return { "top" : top, "left" : left, "width" : width, "height" : height };
}

/** Some functions that might be already there ********************************/

var mouse_x, mouse_y;
if ( typeof ( addEvent ) == 'undefined' )
{
	document.events = new Object ( );
	addEvent = function ( evtype, func )
	{
		switch ( evtype )
		{
			case 'onmousemove':
				if ( !document.events.onmousemove )
					document.events.onmousemove = new Array ( );
				document.events.onmousemove.push ( func );
				break;
		}
	}
	document.onmousemove = function ( e )
	{
		if ( !e ) e = window.event;
		if ( typeof ( e.pageY ) != 'undefined' )
		{
			mouse_y = e.pageY;
			mouse_x = e.pageX;
		}
		else
		{			
			mouse_y = e.clientY;
			mouse_x = e.clientX;
		}
		var events = document.events.onmousemove;
		if ( !events ) return;
		for ( var a = 0; a < events.length; a++ )
			events[ a ]( e );
	}
}

/** Top menu functionality ****************************************************/
var activetopmenuitem = false;
function inittopmenu ( )
{
	if ( document.getElementById ( 'TopMenu__' ) )
	{
		var eles = document.getElementsByTagName ( 'a' );
		for ( var a = 0; a < eles.length; a++ )
		{
			if ( eles[ a ].parentNode.nodeName.toLowerCase ( ) != 'li' ) 
				continue;
			if ( !eles[ a ].parentNode.getElementsByTagName ( 'ul' ).length )
				continue;
			if ( eles[ a ].parentNode.parentNode.id == 'menuroot' )
				continue;
			if ( eles[ a ].parentNode.parentNode.parentNode.parentNode.id != 'menuroot' )
				continue;
			eles[ a ].onmouseover = function ( )
			{
				if ( activetopmenuitem )
					activetopmenuitem.className = activetopmenuitem.className.split ( ' ' )[0];
				this.parentNode.className += ' hovering';
				activetopmenuitem = this.parentNode;
			}
		}
		addEvent ( 'onmousemove', function ( e )
		{
			if ( !activetopmenuitem ) return;
			var a = activetopmenuitem.getElementsByTagName('a')[0];
			var u = activetopmenuitem.getElementsByTagName('ul')[0];
			if ( !a || !u ) return;
			var aims = getobjdimensions ( a );
			var dims = getobjdimensions ( u );
			var x1 = aims.left; var y1 = aims.top;
			var x2 = dims.left + dims.width; var y2 = dims.top + dims.height;
			if ( mouse_x < x1 || mouse_x >= x2 || mouse_y < y1 || mouse_y >= y2 )
			{
				activetopmenuitem.className = activetopmenuitem.className.split ( ' ' )[0];
				activetopmenuitem = false;
			}
		} );
	}
	else
	{
		setTimeout ( 'inittopmenu()', 100 );
	}
}
inittopmenu ( );



 
