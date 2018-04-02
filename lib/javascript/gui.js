

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

/* For making simple guis --------------------------------------------------- */

function guiBox ( id, pelement, type )
{
	if ( ge ( id ) ) guiRemoveBox ( id );
	var d = document.createElement ( 'div' );
	d.events = new Array ();
	d.id = id;
	d.type = type;
	
	switch ( type )
	{
		default:
			d.style.position = 'absolute';
			d.style.background = '#f0f0f0';
			d.style.borderRadius = '4px';
			d.style.boxShadow = '0px 2px 28px rgba(0,0,0,0.7)';
			d.style.padding = '8px';
			d.style.overflow = 'hidden';
			d.resize = function ()
			{
			}
			d.style.zIndex = idseed++;
			break;
	}
	
	if ( !pelement )
	{
		pelement = ge ( 'Gui' );
	}
	
	pelement.appendChild ( d );
	
	return d;
}

function guiRemoveBox ( id )
{
	var n = ge ( id );
	if ( n ) return n.parentNode.removeChild ( n );
}

/* Done making simple guis -------------------------------------------------- */

/*
	Class prototype
*/
var GuiObject = function ()
{
	this.Elements = new Array ( );
	this.DomObject = window;
	this.Type = 'GuiObject';
};
GuiObject.prototype.Init = function ( type, domobject, pobj )
{
	if ( typeof ( domobject ) == 'string' )
	{
		domobject = document.getElementById ( domobject );
	}
	
	if ( !pobj ) pobj = window.Gui ? window.Gui : false;
	
	this.DomObject = domobject;
	this.DomObject.Object = this;
	this.Elements = new Array ( );
	
	if ( !type ) this.Type = 'GuiObject';
	else this.Type = type;
	
	if ( pobj ) 
	{
		if ( pobj != this ) 
		{
			pobj.AddObject ( this.Type, this.DomObject, this );
		}
	}
}
GuiObject.prototype.AddObject = function ( type, domobject, guiobject )
{
	if ( !guiobject )
	{
		guiobject = new GuiObject ( domobject );
		guiobject.Type = type;
	}
	this.Elements[ this.Elements.length ] = guiobject;
}
GuiObject.prototype.GetWidth = function ( )
{
	getElementWidth ( this.DomObject );
}
GuiObject.prototype.GetHeight = function ( )
{
	getElementHeight ( this.DomObject );
}
GuiObject.prototype.GetX = function ( )
{
	getElementLeft ( this.DomObject );
}
GuiObject.prototype.GetY = function ( )
{
	getElementTop ( this.DomObject );
}
GuiObject.prototype.Hide = function ( )
{
	for ( var a = 0; a < this.Elements.length; a++ )
	{
		this.Elements[ a ].Hide ( );
	}
	this._oldx = this.GetX ( );
	this._oldy = this.GetY ( );
	this._oldpos = this.DomObject.style.position;
	this.DomObject.style.visibility = 'hidden';
	this.DomObject.style.position = 'absolute';
	this.DomObject.style.top = '-1000px';
	this.DomObject.style.left = '-1000px';
}
GuiObject.prototype.Show = function ( )
{
	for ( var a = 0; a < this.Elements.length; a++ )
	{
		this.Elements[ a ].Show ( );
	}
	this.DomObject.style.position = this._oldpos;
	this.DomObject.style.top = this._oldy;
	this.DomObject.style.left = this._oldx;
	this.DomObject.style.visibility = 'visible';
}

/*
	Main structure
*/
window.Gui = new GuiObject ( );

/*
	Register all classes-------------------------------------------------------------------------------------------------------------------
*/

GuiContainer = function ( domobject, pobj )										// Legger til et container element til et parent gui element
{
	if ( !pobj ) pobj = false;
	this.Init ( 'GuiContainer', domobject, pobj );
	this.DomObject.className = 'ArenaGuiContainer';
	return this;
}
GuiContainer.prototype = GuiObject.prototype;

/**
	Include our gui classes
**/
include ( 'lib/javascript/gui/slideshow.js' );
include ( 'lib/javascript/gui/switcher.js' );
include ( 'lib/javascript/gui/pulldown_menus.js' );
include ( 'lib/javascript/gui/scrollarea.js' );
include ( 'lib/javascript/gui/autocomplete.js' );
include ( 'lib/javascript/gui/slidelist.js' );


