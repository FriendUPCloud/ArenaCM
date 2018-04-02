

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
* Slideshow object                                                             *
* Creates a GuiSlideshow on 'MyDiv', making each childnode a slide. Will be    *
* controlled by a slider, if preferred.                                        *
*                                                                              *
* Usage:                                                                       *
*   var slide1 = new GuiSwitcher ( document.getElementById ( 'MyDiv' ) );      *
*                                                                              *
* To add events when the slides are changing:                                  *
*   slide1.addSlideEvent ( myfunction );                                       *
*                                                                              *
* Variables:                                                                   *
*  slide1.Tween = the speed of the crossfade of slide a to b                   *
*  slide2.Speed = the update freq, like frames per second                      *
*  slide3.Pause = how long to wait between fades                               *
*  slide3.FadeBackground = wether to fade both background / foreground         *
*                                                                              *
*******************************************************************************/

GuiSlideshow = function ( domobject, pobj )
{
	if ( !pobj ) pobj = false;
	this.Init ( 'GuiSlideshow', domobject, pobj );
	this.campaigns = new Array ( );

	for ( var a = 0; a < this.DomObject.childNodes.length; a++ )
	{
		var no = this.DomObject.childNodes[ a ];
		if ( no.innerHTML )
		{
			var no2 = document.createElement ( no.tagName );
			no2.className = no.className;
			no2.id = no.id;
			no2.innerHTML = ( no.innerHTML + '' );
			this.campaigns.push ( no2 );
		} 
	} 
	this.DomObject.innerHTML = '';

	this.index = -1;
	this.front = document.createElement ( 'div' );
	this.back = document.createElement ( 'div' );
	this.slidergroove = document.createElement ( 'div' );
	this.slider = document.createElement ( 'div' );

	this.front.className = 'Kampanjefront';
	this.back.className = 'Kampanjeback';
	this.slidergroove.className = 'Kampanjegroove';
	this.slider.className = 'Kampanjeslider';

	this.DomObject.appendChild ( this.back );
	this.DomObject.appendChild ( this.front );
	this.slidergroove.appendChild ( this.slider );
	this.DomObject.appendChild ( this.slidergroove );
	this.slider.object = this;

	this.alpha = 0;
	this.mode = 0;
	this.running = true;
	this.started = false;
	this.slideFuncs = false;
	this.executedFuncs = false;

	this.Speed = 50;															// How fast to fade
	this.Tween = 10;															// How hard to tween the fade
	this.Pause = 4000;															// How long to wait between frames
	this.FadeBackground = false;												// Fade background in the process of fading the foreground?

	this.slider.onmousedown = function ( )
	{
		document.slidermoveobject = this;
		var relleft = getElementLeft ( this );
		document.slidermoveoffsetx = mousex - relleft;
		document.oldonselectstart = document.onselectstart;
		document.onselectstart = function ( ){ return false; }
		this.object.stop ( );
		return false;
	}
	this.terminate = function ( )
	{
		if ( document.slidermoveobject )
		{
			document.slidermoveobject.object.running = true;
			document.slidermoveobject.object.run ( );
			document.slidermoveobject = false;
			document.onselectstart = document.oldonselectstart;
		}
	}
	addEvent ( 'onmouseup', this.terminate );
	addEvent ( 'onmouseout', this.terminate );
	addEvent ( 'onmousemove', function ( )
	{
		if ( document.slidermoveobject )
		{
			var o = document.slidermoveobject;
			var destx = ( mousex - document.slidermoveoffsetx );
			destx -= getElementLeft ( o.parentNode );
			if ( destx < 0 ) destx = 0;
			if ( destx > getElementWidth ( o.parentNode ) - getElementWidth ( o ) )
				destx = getElementWidth ( o.parentNode ) - getElementWidth ( o );
			o.style.left = destx + 'px';
		
			o.object.index = Math.floor ( destx / getElementWidth ( o.parentNode ) * o.object.campaigns.length ) - 1;
			o.object.nextSlide ( );
		}	
	} );
	this.addSlideEvent = function ( varfunc )
	{
		if ( !this.slideFuncs )
		{
			this.slideFuncs = new Array ( );
		}
		this.slideFuncs.push ( varfunc );
	}
	this.nextSlide = function ( )
	{
		this.index = ( this.index + 1 ) % this.campaigns.length;
		
		// don't copy the innerHTML object (problems msie)
		// and.. here we swap front and back =)
		this.back.innerHTML = ( this.front.innerHTML + '' );
		
		this.alpha = 0;
		
		if ( this.started )
		{
			setOpacity ( this.back, 100 );
			setOpacity ( this.front, 0 );
		}
		else this.started = true;

		var tpl = this.campaigns[ this.index ];
		var el = document.createElement ( tpl.tagName );
		el.className = tpl.className;
		el.id = tpl.id;
		el.innerHTML = ( tpl.innerHTML + '' ); // not copying the object (msie)
		
		this.front.innerHTML = '';		
		this.front.appendChild ( el );
	}
	this.run = function ( )
	{
		if ( this.running )
		{
			if ( this.alpha == 0 )
			{
				var sw = getElementWidth ( this.slider );
				var gw = getElementWidth ( this.slidergroove ) - sw;
				var left = ( ( this.index / this.campaigns.length - 1 * gw ) );
				if ( isNaN ( left ) )
					left = 0;
				this.slider.style.left = left + 'px';
			}
		
			var speed = this.Speed;
		
			if ( this.alpha < 100 )
			{
				if ( !this.executedFuncs && this.slideFuncs )
				{
					this.executedFuncs = true;
					for ( var a = 0; a < this.slideFuncs.length; a++ )
					{
						this.slideFuncs[ a ] ( );
					}
				}
				var diff = ( this.alpha - 100 ) / this.Tween;
				if ( -diff < 0.05 ) 
				{	
					this.alpha = 100;
				}
				else 
				{
					this.alpha -= diff;
				}
				setOpacity ( this.front, this.alpha / 100 );
				if ( this.FadeBackground ) 
					setOpacity ( this.back, ( 100 - this.alpha ) / 100 );
			}
			else
			{
				this.executedFuncs = false;
				speed = this.Pause;												// Pause
				this.nextSlide ( );												// Show next slide
			}
			setTimeout ( 'document.getElementById ( \'' + this.DomObject.id + '\' ).Object.run ( )', speed );
		}
	}
	this.stop = function ( )
	{
		this.running = false;
		this.alpha = 100;
		setOpacity ( this.front, 1 );
		setOpacity ( this.back, 0 );
	}
	this.nextSlide ( );
	this.run ( );
	return this;
}
GuiSlideshow.prototype = GuiObject.prototype;
