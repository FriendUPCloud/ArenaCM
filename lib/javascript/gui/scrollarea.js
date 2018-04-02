

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
* Scrollarea object                                                            *
* Creates a GuiScrollArea on 'MyDiv', creating a scrollable iframe. Will be    *
* controlled by two stylable arrows.                                           *
*                                                                              *
* Usage:                                                                       *
*   var area = new GuiScrollArea ( document.getElementById ( 'MyDiv' ) );      *
*                                                                              *
* Variables:                                                                   *
*  area.Speed = How many pixels to jump on a scroll                            *
*                                                                              *
*******************************************************************************/

var _scrollElements = new Array ( );

GuiScrollArea = function ( domobject, pobj )
{
	if ( !pobj ) pobj = false;
	this.Init ( 'GuiSlideshow', domobject, pobj );
	
	this.Speed = 15;
	
	// Add self
	this.Index = _scrollElements.length;
	_scrollElements[ this.Index ] = this;
	
	if ( domobject.style.visibility != 'visible' )
		domobject.style.visibility = 'visible';
	if ( !document._newsbrains )
		document._newsbrains = new Array ( );
	this.Index = document._newsbrains.length;
	this.ElementID = domobject.id;
	document._newsbrains.push ( this );
	this.DomObject.brain = this;
	this.TargetY = 0;
	this.CurrentY = 0;
	this.Scrolling = false;
	
	this.getIframeDocument = function ( )
	{
		if ( this.scrf.contentWindow.document )
			return this.scrf.contentWindow.document;
		return this.scrf.contentDocument;
	}
	
	// Fix dates
	stripEmptyTextNodes ( this.DomObject );
	var dates = this.DomObject.getElementsByTagName ( 'small' );
	for ( var a = 0; a < dates.length; a++ )
	{
		var dat = dates[ a ];
		var pnode = dat.parentNode;
		pnode.removeChild ( dat );
		pnode.insertBefore ( dat, pnode.childNodes[ 0 ] );
	}
	
	// Container for scrollitems
	this.container = document.createElement ( 'div' );
	this.container.id = this.DomObject.id + 'Scroller';
	
	// Create a scroll content
	var html = this.DomObject.innerHTML;
	this.content = document.createElement ( 'div' );
	this.container.appendChild ( this.content );
	
	// Insert iframe before the first child of centerbox
	var cb = document.getElementById ( 'CenterBox__' );
	cb.insertBefore ( this.container, cb.firstChild );
	this.DomObject.parentNode.removeChild ( this.DomObject );
	
	// Set the content
	
	// Set content
	this.content.innerHTML = '<div class="Content">' + html + '</div>';
	this.DomObject = this.container;
	this.DomObject.style.position = 'absolute';
	
	this.apply = function ( )
	{
		if ( this.ArrowUp ) return false;
			
		var h = getElementHeight ( this.container );
		var ch = getElementHeight ( this.content );
	
		if ( ch > h )
		{
			this.content.style.position = 'absolute';
			this.ArrowUp = document.createElement ( 'div' );
			this.ArrowDown = document.createElement ( 'div' );
			this.ArrowUp.className =  this.ElementID + 'ArrowUp';
			this.ArrowDown.className =  this.ElementID + 'ArrowDown';
			this.container.parentNode.appendChild ( this.ArrowUp );
			this.container.parentNode.appendChild ( this.ArrowDown );
			this.ArrowDown.Object = this;
			this.ArrowUp.Object = this;
			this.OffsetY = 0;
			this.MaxScroll = -( ch - h );
			this.MinScroll = 0;
			this.ArrowDown.onmousedown = function ( )
			{
				document.scrolling = true;
				this.Object.ScrollDown ( );
				return false;
			}
			this.ArrowUp.onmousedown = function ( )
			{
				document.scrolling = true;
				this.Object.ScrollUp ( );
				return false;
			}
			this.ScrollDown = function ( )
			{
				if ( ( this.OffsetY - this.Speed ) > this.MaxScroll )
					this.OffsetY -= this.Speed;
				else this.OffsetY = this.MaxScroll;
				this.content.style.top = this.OffsetY + 'px';
				if ( document.scrolling )
					setTimeout ( '_scrollElements[ ' + this.Index + ' ].ScrollDown ( )', 100 );
			}
			this.ScrollUp = function ( )
			{
				if ( this.OffsetY + this.Speed < this.MinScroll )
					this.OffsetY += this.Speed;
				else this.OffsetY = this.MinScroll;
				this.content.style.top = this.OffsetY + 'px';
				if ( document.scrolling )
					setTimeout ( '_scrollElements[ ' + this.Index + ' ].ScrollUp ( )', 100 );
			}
			addEvent ( 'onmouseup', function ( )
			{
				document.scrolling = false;
			} );
		}
	}
	this.apply ( );
	
	return this;
}
GuiScrollArea.prototype = GuiObject.prototype;

