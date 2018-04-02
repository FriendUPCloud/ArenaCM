

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



function getElementProperties ( elmnt )
{
	var properties = new Object;
	
	properties.left   = 0;
	properties.top    = 0;
	properties.width  = 0;
	properties.height = 0;
	
	if ( elmnt.offsetWidth )
		properties.width = elmnt.offsetWidth;
	
	if ( elmnt.offsetHeight )
		properties.height = elmnt.offsetHeight;
		
	if ( elmnt.clientLeft )
		properties.top = elmnt.clientLeft
	else
	{
		properties.left = 0;
		pointer = elmnt;
		while ( typeof( pointer ) != "undefined" && typeof( pointer.offsetLeft ) != "undefined" )
		{
			properties.left += pointer.offsetLeft;
			if ( typeof( pointer.parentNode ) != "undefined" ) break; // be nice to konqueror
			pointer = pointer.parentNode;
		}
	}
	
	if ( elmnt.clientTop )
		properties.top = elmnt.clientTop
	else
	{
		properties.top = 0;
		pointer = elmnt;
		while ( typeof( pointer ) != "undefined" && typeof( pointer.offsetTop ) != "undefined" )
		{
			properties.top += pointer.offsetTop;
			if ( typeof( pointer.parentNode ) != "undefined" ) break; // be nice to konqueror
			pointer = pointer.parentNode;
		}
	}
	
	return properties;
}

/////////////////////////////////////////////////////////
//// Dragger Class //////////////////////////////////////
//// 
//// Target events:
////   onDragHover ( element )
////   onDragStop ()
////   onDragDrop ( element )
/////////////////////////////////////////////////////////

var Dragger = function ()
{
	this.threshold  = 5;
	this.dragElement      = 0;
	this.state      = 0;
	
	this.targets        = new Array();
	this.normalTargets  = new Array();
	this.config    = new Object();

	/////////////////////////////////////////////////////////
	//// Drop targets ///////////////////////////////////////
	/////////////////////////////////////////////////////////
	
	//// Remove drop target /////////////////////////////////
	this.removeTarget = function ( elmnt )
	{
		var outTargets = new Array ( );
		for ( var a = 0; a < this.normalTargets.length; a++ )
		{
			if ( !this.normalTargets[ a ].id || this.normalTargets[ a ].id != elmnt )
				outTargets[ outTargets.length ] = this.normalTargets[ a ];
		}
		this.normalTargets = outTargets;
	}
	
	//// Add a drop target //////////////////////////////////
	
	this.addTarget = function ( elmnt ) 
	{
		this.normalTargets[ this.normalTargets.length ] = elmnt;
	}
	
	//// Reset drop targets /////////////////////////////////
	
	this.resetTargets = function ( elmnt ) 
	{
		this.normalTargets = new Array ();
	}
	
	//// Set drag options/defaults //////////////////////////
	
	this.setOptions = function ( opts )
	{
		this.config = new Object();
		this.config.noDrop = "leave";
		this.config.dropAll = true;
		this.config.constrain = true;
	
		for ( a in opts ) this.config[a] = opts[a];
	
		if ( this.config.pickup == "clone" && !opts.noDrop )
			this.config.noDrop = "destroy";
		
		if ( this.config.pickup == "clone" && !opts.drop )
			this.config.drop = "destroy";			
	}
	
	//// Check if the dragged object is over a target ///////
	
	this.matchTarget = function ( elmnt )
	{
		var properties = getElementPosition( elmnt );
		
		if ( elmnt.id == 'Workbench' || elmnt.id == 'WorkbenchWastebin' )
			properties.top += getScrollTop ( );
	
		if ( 
			( 
				mousex > properties.left && 
				mousex < ( properties.width + properties.left ) 
			) 
			&&	
			( 
				mousey > properties.top && 
				mousey < ( properties.height + properties.top ) 
			) 
		) 
		{
			return true;
		}
		else
			return false;
	}
	
	//// Match all targets, return an array of matches //////
	
	this.matchTargets = function ( )
	{
		// mouse model
		var matched = new Array ();
		for ( var a = 0; a < this.targets.length; a++ ) 
			if ( this.matchTarget ( this.targets[ a ] ) )
				matched[ matched.length ] = this.targets[ a ];
		return ( matched.length < 1 ) ? false: matched;
	}
	
	/////////////////////////////////////////////////////////
	//// Dragging Code //////////////////////////////////////
	/////////////////////////////////////////////////////////
	
	//// Initialize drag ////////////////////////////////////
	
	this.startDrag = function ( dragElement, config ) 
	{
		this.setOptions ( config );
		this.startx        = mousex;
		this.starty        = mousey;
		this.dragElement   = dragElement;
		this.sourceElement = this.dragElement;
		this.state         = 1;
		this.targets       = ( this.config.targets ) ? this.config.targets : this.normalTargets;
	}
	
	//// Check if we're dragging an element /////////////////
	
	this.isDragging = function ( element )
	{
		if ( this.state >= 2 && ( !element || element == this.sourceElement ) )
			return true;
		else
			return false;
	}
	
	//// Get the distance dragged ///////////////////////////
	
	this.getDragDistance = function ()
	{
		var diffx = mousex - this.startx;
		var diffy = mousey - this.starty;
		return Math.sqrt ( ( diffx * diffx ) + ( diffy * diffy ) );
	}
	
	//// Update the drag status on mouse movement ///////////
	
	this.checkDrag = function ()
	{
		if ( this.dragElement == 0 || this.state == 0 ) return false;
		
		// Remove all text selections
		if ( isIE )	document.selection.empty();
		else window.getSelection().removeAllRanges();
		
		var distance = this.getDragDistance();
		
		document.onselectstart = function () { return false; }
		
		// Pick up element if distance > threshold
		if ( this.state == 1 && distance >= this.threshold )
		{
			if ( this.config.pickup == "clone" )
			{
				var pr = getElementPosition ( this.dragElement );
				var temp = this.dragElement.cloneNode ( true );
				temp.style.position = "absolute";
				temp.style.top = (mousey-pr.height/2) + "px";
				temp.style.left = (mousex-pr.width/2) + "px";
				this.dragElement = document.body.appendChild ( temp );
				this.dragElement.style.zIndex = "999999";
				setOpacity ( this.dragElement, 0.8 );
			}
			this.state = 2;
			
			this.dragElement.oldStyle = this.dragElement.style;
			this.dragElement.style.position = "absolute";
			for ( var a = 0; a < this.targets.length; a++ )
			{
				if ( typeof this.targets[a].onDragStart != "undefined" )
					this.targets[a].onDragStart ( this.dragElement );
			}
		}
		
		// Element is picked up, do stuff
		if ( this.state == 2 )
		{
			
			var pr = getElementPosition ( this.dragElement );
			
			var newTop  = ( mousey-pr.height / 2 );
			var newLeft = ( mousex-pr.width / 2 );
			
			if ( this.config.hotspoty ) newTop  -= this.config.hotspoty;
			if ( this.config.hotspotx ) newLeft -= this.config.hotspotx;
			
			if ( this.config.constrain )
			{
				if ( mousex + ( pr.width / 2 ) >= getDocumentWidth() - 16)
					newLeft = getDocumentWidth() - pr.width - 1 - 16;
					
				if ( mousey + ( pr.height / 2 ) >= getDocumentHeight() + getScrollTop() )
					newTop = getDocumentHeight() - pr.height - 1 + getScrollTop();
					
				if ( newTop < 0 ) newTop = 0;						
				if ( newLeft < 0 ) newLeft = 0;						
			}
			
			this.dragElement.style.top = newTop + "px";
			this.dragElement.style.left = newLeft + "px";
			// Added the x and y parameter - 28 apr 2008 - AKG
			this.config.x = newLeft;
			this.config.y = newTop;
			
			for ( var a = 0; a < this.targets.length; a++ )
			{
				if ( this.matchTarget( this.targets[ a ] ) )
				{
					if ( !this.targets[ a ].hovering )
					{
						if ( typeof ( this.targets[ a ].onDragOver ) != "undefined" )
							this.targets[ a ].onDragOver ( this.dragElement );
						this.targets[ a ].hovering = true;
					}
				}
				else
				{
					if ( this.targets[ a ].hovering )
					{
						if ( typeof ( this.targets[ a ].onDragOut ) != "undefined" )
							this.targets[ a ].onDragOut ( this.dragElement );
						this.targets[ a ].hovering = false;
					}
				}
			}
		}
	}
	
	//// Stop dragging //////////////////////////////////////
	
	this.stopDrag = function ()
	{
		if ( this.dragElement == 0 || this.state < 2 )
		{
			this.state = 0;
			return false;
		}
		
		document.onselectstart = function () { return true; }
		
		if ( typeof this.dragElement.onDragStop != "undefined" )
			this.dragElement.onDragStop ();
		
		for ( a in this.targets )
		{
			if ( typeof this.targets[a].onDragStop != "undefined" )
				this.targets[a].onDragStop ( this.dragElement );		
		}
		
		var matched = this.matchTargets ();
		if ( matched )
		{
			var limit = ( this.config.matchAll ) ? matched.length : 1;
			for ( var i = 0; i < limit; i++ )
			{
				if ( typeof matched[i].onDragDrop != "undefined" )
					matched[i].onDragDrop ( this.dragElement );
			}
			if ( this.config.drop == "destroy" )
			{
				this.dragElement.parentNode.removeChild( this.dragElement );
			}
		}
		else
		{
			if ( this.config.noDrop == "return" )
			{
				if ( !this.config.keepPosition )
				{
					this.dragElement.style.position = "relative";
					this.dragElement.style.top = "0";
					this.dragElement.style.left = "0";
				}
			}
			if ( this.config.noDrop == "destroy" )
			{
				this.dragElement.parentNode.removeChild( this.dragElement );
			}
			if ( window.onNoDrop ) window.onNoDrop ();
		}
		this.state = 3;
		setTimeout ( "dragger.clearDrag()", 10 );
	}
	
	this.clearDrag = function ()
	{
		this.dragElement = 0;
		this.state = 0;
	}
}

var dragger = new Dragger ();
document.dragger = dragger;

addAction ( function () { dragger.checkDrag (); } );

document.onmouseup = function ()
{
	dragger.stopDrag ();
}
