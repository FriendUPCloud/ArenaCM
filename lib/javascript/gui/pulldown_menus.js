

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
* Animated pulldown menu                                                       *
*                                                                              *
*******************************************************************************/

var _navnodes = new Array ( );
var _navnodeMMNodes = new Array ( ); // for onmousemove events

// Used for subnodes
var _navnode = function ( )
{
	this.mode = '';
	this.object = null;
	this.calculatedHeight = 0;
	this.extraHeight = new Array ( );
	this.currentHeight = 0;
	this.counter = 0;
	this.mindepth = 1;
	this.etimer = 0;
	this.timer = 0;
	this.hovering = false;
	this.queuedCollapse = false;
	this.visible = false;
	
	// Get the parent navnode object
	this.getParentNavNode = function ( )
	{
		if ( this.ul.parentNode.nodeName.toLowerCase ( ) != 'div' )
		{
			if ( typeof ( this.ul.parentNode.parentNode.parentNode.navnode ) != 'undefined' )
			{
				var nod = this.ul.parentNode.parentNode.parentNode.navnode;
				return nod;
			}
		}
		return null;
	}
	// Expand a submenu
	this.expand = function ( init )
	{
		if ( !init && !this.mode ) return;
		
		// if we're trying to init an already expanding anim, abort
		if ( init && this.mode == 'expanding' )
			return;
		// normal init
		else if ( init && this.mode != 'expanding' ) 
		{
			this.mode = 'expanding';
			this.timer = 4;
			this.etimer = 7;
		}
		// abort if we're not supposed to be expanding!
		else if ( !init && this.mode != 'expanding' ) return;
		
		if ( this.etimer > 0 ) 
		{
			this.etimer--;
		}
		else
		{
			this.calculatedHeight = this.calculateHeight ( );
			var mh = this.calculatedHeight;
			this.currentHeight -= ( this.currentHeight - mh ) / 3;
			var d = ( this.counter - 100 ) / 3;
			if ( Math.abs ( d ) < 0.3 ) this.counter = 100;
			else this.counter -= d;
			var c100 = this.counter / 100;
			var h = Math.round ( c100 * this.currentHeight );
			this.ul.style.height = ( isNaN ( h ) ? 0 : h ) + 'px';
			setOpacity ( this.ul, c100 );
		}
		var hovering = this.checkHover ( );
		if ( this.counter < 100 && this.mode == 'expanding' )
		{
			// Aborted as we've not begun expanding
			if ( !hovering && this.etimer > 0 )
			{
				var nm = this.object.firstChild.firstChild.innerHTML;
				this.mode = '';
				this.etimer = 0;
				return;
			}
			else if ( this.etimer == 0 )
			{
				if ( isIE )
					hideFormFields ( 'select' );
				this.setULVisibility ( true );
				if ( this.getParentNavNode ( ) )
					this.adjustParent ( );
			}
			setTimeout ( '_navnodes[' + this.index + '].expand ( )', 50 );	
		}
	}
	// Adjust the height of a menu
	this.adjust = function ( init )
	{
		if ( !init ) init = false;
		this.calculatedHeight = this.calculateHeight ( );
		var mh = this.calculatedHeight;
		var d = ( this.currentHeight - mh ) / 2;
		if ( Math.abs ( d ) < 0.3 ) this.currentHeight = mh;
		else this.currentHeight -= d;
		if ( mh != this.currentHeight )
			setTimeout ( '_navnodes[' + this.index + '].adjust ( )', 50 );
		if ( this.getDepth () >= this.mindepth )
			this.ul.style.height = Math.round ( this.currentHeight ) + 'px';
	}
	// Adjust the height of the parent menu
	this.adjustParent = function ( )
	{
		var parent = this.getParentNavNode ( );
		if ( parent ) 
		{
			parent.extraHeight [ this.index ] = this.calculateHeight ( );
			parent.adjust ( 1 );
			parent.adjustParent ( );
		}
	}
	// Collapse submenu and all subs
	this.collapseAll = function ( depth )
	{
		// do not interrupt an already occuring collapse
		if ( this.mode == 'collapsing' ) return;
		// reset anim mode so we can collapse at will
		this.mode = '';
		if ( !depth ) depth = 0;
		if ( depth == 0 && this.hovering ) return;
		if ( this.object.childNodes[ 1 ].firstChild )
		{
			var nods = this.object.childNodes[ 1 ].childNodes;
			for ( var a = 0; a < nods.length; a++ )
			{
				if ( !nods[ a ].nodeName || nods[ a ].nodeName.toLowerCase ( ) != 'li' )
					continue;
				if ( nods[ a ].navnode ) 
					nods[ a ].navnode.collapseAll ( depth + 1 );
			}
		}
		// remove extraheight from this
		this.extraHeight = new Array ( );
		// init collapse
		this.collapse ( 1 );
	}
	// Collapse a submenu
	this.collapse = function ( init )
	{
		// We can't collapse something which is not visible!
		if ( !this.visible ) return;
		// Get parent
		var parent = this.getParentNavNode ( );
		// if we're initing while collapsing, abort
		if ( init && this.mode == 'collapsing' )
			return;
		else if ( !init && !this.mode ) return;
		// init normally
		else if ( init && this.mode != 'collapsing' )
		{
			this.mode = 'collapsing';
			this.calculatedHeight = this.calculateHeight ( );
			if ( parent ) 
			{
				parent.extraHeight[ this.index ] = 0;
				this.adjustParent ( );
			}
		}
		// abort if we're not supposed to be collapsing
		else if ( !init && this.mode != 'collapsing' )
			return;
		
		if ( this.timer > 0 ) this.timer--;
		else
		{
			var mh = this.calculatedHeight;
			this.currentHeight -= ( this.currentHeight - mh ) / 2;
			var d = this.counter / 2;
			if ( Math.abs ( d ) < 0.3 ) this.counter = 0;
			else this.counter -= d;
			var c100 = this.counter / 100;
			setOpacity ( this.ul, c100 );
			var h = Math.round ( c100 * this.currentHeight );
			this.ul.style.height = ( isNaN ( h ) ? 0 : h ) + 'px';
		}
		if ( this.counter > 0 && this.mode == 'collapsing' )
			setTimeout ( '_navnodes[' + this.index + '].collapse ( )', 50 );
		else if ( this.counter == 0 && this.depth > this.mindepth - 1 )
		{
			this.setULVisibility ( false );
			if ( isIE ) showFormFields ( 'select' );
		}
	}
	// get basic height
	this.getBaseHeight = function ( )
	{
		if ( !this.baseHeight )
		{
			var mh = 0;
			for ( var a = 0; a < this.ul.childNodes.length; a++ )
			{
				mh += getElementHeight ( this.ul.childNodes[ a ] );;
				mh += parseInt ( getStyle ( this.ul.childNodes[ a ], 'margin-top' ) );
				mh += parseInt ( getStyle ( this.ul.childNodes[ a ], 'margin-bottom' ) );
				mh += parseInt ( getStyle ( this.ul.childNodes[ a ], 'padding-top' ) );
				mh += parseInt ( getStyle ( this.ul.childNodes[ a ], 'padding-bottom' ) );
			}
			this.baseHeight = mh;
		}
		return this.baseHeight;
	}
	// Calculate the height of a submenu
	this.calculateHeight = function ( )
	{
		if ( !this.visible ) return 0;
		var mh = this.getBaseHeight ( );
		for ( var a in this.extraHeight )
		{
			if ( !isNaN ( Number ( this.extraHeight[ a ] ) ) )
				mh += this.extraHeight[ a ];
		}
		return mh;
	}
	this.getName = function ( )
	{
		return this.object.firstChild.firstChild.innerHTML;
	}
	this.checkHover = function ( )
	{
		// over li
		var pnode = this.getParentNavNode ( );
		if ( pnode && !pnode.visible ) return false;
		
		var overUl, overOb;
		
		var ly1 = getElementTop ( this.object );
		var lx1 = getElementLeft ( this.object );
		var lx2 = lx1 + getElementWidth ( this.object );
		var ly2 = ly1 + getElementHeight ( this.object );
		overOb = ( mousex >= lx1 && mousey >= ly1 && mousex < lx2 && mousey < ly2 );
		
		// over sub ul (the obj must be visible and the ul must not be hidden!
		if ( this.visible && this.ul.style.display != 'none' )
		{
			var uy1 = getElementTop ( this.ul );
			var ux1 = getElementLeft ( this.ul );
			var ux2 = ux1 + getElementWidth ( this.ul );
			var uy2 = uy1 + getElementHeight ( this.ul );
			// check
			overUl = ( mousex >= ux1 && mousey >= uy1 && mousex < ux2 && mousey < uy2 );
		}
		
		if ( overOb || overUl )
		{
			return true;
		}
		this.ul.style.border = 'none';
		return false;
	}
	this.getDepth = function ( )
	{
		if ( this.depth ) return this.depth;
		// Finds how deep we are to the containing div
		var depth = 0;
		var el = this.object;
		while ( el.parentNode.nodeName.toLowerCase ( ) != 'div' ) 
		{ el = el.parentNode; depth++; }
		this.depth = depth;
		return depth;
	}
	this.setULVisibility = function ( val )
	{
		if ( val )
		{
			this.ul.style.visibility = 'visible';
			this.ul.style.display = '';
			this.visible = true;
			// ie must remove heights
			if ( isIE6 )
			{
				var under = this.ul.getElementsByTagName ( '*' );
				for ( var a = 0; a < under.length; a++ )
					under[ a ].style.height = '0px';
			}
		}
		else
		{
			this.ul.style.visibility = 'hidden';
			this.ul.style.display = 'none';
			this.visible = false;
			// ie must remember heights
			if ( isIE6 )
			{
				var under = this.ul.getElementsByTagName ( '*' );
				for ( var a = 0; a < under.length; a++ )
					under[ a ].style.height = 'auto';
			}	
		}
		//document.title = 'Setting ' + this.getName ( ) + ' to ' + ( this.visible ? 'visible' : 'invisible' );
	}	
	this.index = _navnodes.length;
	_navnodes.push ( this );
}

var GuiAnimatedMenu = function ( domobject, pobj )
{
	if ( !pobj ) pobj = false;
	stripEmptyTextNodes ( domobject );
	this.Init ( 'GuiAnimatedMenu', domobject, pobj );
	this.campaigns = new Array ( );
	this.expanded = new Array ( );
	
	// Inits one navnode and all subnodes (this navnode is an LI)
	this.initNavNode = function ( ele, startdepth )
	{
		if ( !ele.childNodes[ 1 ] )
			return;
		
		var el = ele;
		
		ele.navnode = new _navnode ( );
		ele.navnode.ul = ele.childNodes[ 1 ];
		ele.navnode.object = ele;
		ele.navnode.menu = this;
	
		var depth = ele.navnode.getDepth ( );
		ele.navnode.currentHeight = 0;
		ele.navnode.counter = 0;
		ele.navnode.mindepth = startdepth;
		
		// If we're deeper in the node tree than the initialized LI
		if ( depth > startdepth )
		{
			// Hide the node initially
			ele.navnode.ul.style.height = '0px';
			ele.navnode.setULVisibility ( false );
			ele.navnode.mouseover = function ( )
			{ 
				this.queuedCollapse = false;
				if ( !this.hovering ) this.expand ( 1 );
			}
		}
		else 
		{
			ele.navnode.setULVisibility ( true );
		}
		
		_navnodeMMNodes.push ( ele );
		ele.navnode.ul.style.zIndex = 1000 + depth;
		ele.navnode.object.node = ele.navnode;
	}
	
	// Remove empty text nodes from the main dom node
	stripEmptyTextNodes ( this.DomObject );
	
	// Init our menu
	var nodes = this.DomObject.getElementsByTagName ( 'li' );
	for ( var a = 0; a < nodes.length; a++ )
	{ this.initNavNode ( nodes[ a ], 2 ); }
	
	// Only do this once!!
	if ( !document.hasNavNodeMouseMoveEvent )
	{
		addEvent ( 'onmousemove', function ( )
		{
			for ( var z = 0; z < _navnodeMMNodes.length; z++ )
			{
				var mele = _navnodeMMNodes[ z ].navnode;
				var hovering = mele.checkHover ( );
				
				// If the mouse is hovering over this menu, then 
				// it won't be possible to collapse it, as it must
				// remain open
				if ( mele.object.parentNode.style.visibility == 'hidden' ) 
					continue;
				if ( !mele.mouseover ) 
					continue;
				if ( hovering )
				{
					//document.title = ( mele.visible ? 'visible' : 'not visible' ) + mele.getName ( ) + ' is hovered ' + getElementHeight ( mele.ul );
					mele.mouseover ( );	
					mele.hovering = true;
				}
				// We can collapse menus that is not hovered over
				else
				{
					if ( mele.hovering )
					{
						if ( mele.getDepth ( ) == 3 )
							mele.queuedCollapse = true;
					}
					mele.hovering = false;
					if ( mele.queuedCollapse )
					{
						mele.visible = true;
						mele.collapseAll ( );
					}
				}	
			}
		} );
		document.hasNavNodeMouseMoveEvent = 1;
	}
	// Set to visible
	domobject.style.visibility = 'visible';
}
GuiAnimatedMenu.prototype = GuiObject.prototype;
