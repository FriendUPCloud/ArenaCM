

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
* Switcher object                                                              *
* Creates a GuiSwitcher on 'MyDiv' and switches the first two dom nodes in it. *
*                                                                              *
* Usage:                                                                       *
* var slide1 = new GuiSwitcher ( document.getElementById ( 'MyDiv' ) );        *
*                                                                              *
*******************************************************************************/

GuiSwitcher = function ( domobject, pobj )
{
	if ( !pobj ) pobj = false;
	this.Init ( 'GuiSwitcher', domobject, pobj );
	
	domobject.engine = this;
	
	this.ForegroundNode = null;
	this.BackgroundNode = null;
	
	// Find our nodes!
	if ( this.DomObject.childNodes.length )
	{
		var nodenum = 0;
		for ( var a = 0; a < this.DomObject.childNodes.length; a++ )
		{
			if ( typeof ( this.DomObject.childNodes[ a ].id ) != 'undefined' )
			{
				if ( nodenum == 0 ) this.BackgroundNode = this.DomObject.childNodes[ a ];
				else if ( nodenum == 1 ) this.ForegroundNode = this.DomObject.childNodes[ a ];
				nodenum++;
			}
		}
	}
	if ( !this.ForegroundNode || !this.BackgroundNode )
	{
		return false;
	}
	
	// Default vars
	this.ForegroundNode.engine = this;
	this.BackgroundNode.engine = this;
	setOpacity ( this.BackgroundNode, 1 );
	this.BackgroundNode.op = 100;
	setOpacity ( this.ForegroundNode, 0 );
	this.ForegroundNode.op = 0;
	this.slidesover = false;
	this.slidesout = false;
	this.ForegroundNode.style.cursor = isIE ? 'hand' : 'pointer';
	this.BackgroundNode.style.cursor = isIE ? 'hand' : 'pointer';
	
	this.DomObject.onmouseover = function ( )
	{
		if ( this.engine.mode == 'slidein' ) return;
		this.engine.mode = 'slidein';
		setTimeout ( 'document.getElementById ( \'' + this.id + '\' ).engine.brain ( )', 50 );
	}
	this.DomObject.onmouseout = function ( )
	{
		if ( this.engine.mode == 'slideout' ) return;
		this.engine.mode = 'slideout';
		setTimeout ( 'document.getElementById ( \'' + this.id + '\' ).engine.brain ( )', 50 );
	}
	this.brain = function ( )
	{
		if ( this.mode == 'slidein' )
		{
			if ( this.ForegroundNode.op < 100 )
			{
				var d = ( this.ForegroundNode.op - 100 ) / 4;
				if ( -d < 0.3 )
				{
					this.ForegroundNode.op = 100;
				}
				else
				{
					this.ForegroundNode.op -= d;
					setTimeout ( 'document.getElementById ( \'' + this.DomObject.id + '\' ).engine.brain ( )', 50 );
				}
				setOpacity ( this.ForegroundNode, this.ForegroundNode.op / 100 );
				setOpacity ( this.BackgroundNode, ( 100 - this.ForegroundNode.op ) / 100 );
			}
		}
		else if ( this.mode == 'slideout' )
		{
			if ( this.ForegroundNode.op > 0 )
			{
				var d = this.ForegroundNode.op / 4;
				if ( d < 0.3 )
				{
					this.ForegroundNode.op = 0;
				}
				else
				{
					this.ForegroundNode.op -= d;
					setTimeout ( 'document.getElementById ( \'' + this.DomObject.id + '\' ).engine.brain ( )', 50 );
				}
				setOpacity ( this.ForegroundNode, this.ForegroundNode.op / 100 );
				setOpacity ( this.BackgroundNode, ( 100 - this.ForegroundNode.op ) / 100 );
			}
		}
	}
}
GuiSwitcher.prototype = GuiObject.prototype;
