
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

var GalleryPopupList = new Array ();

// Extend DomObject
var GalleryPopup = function ( domobj, url )
{
	this.Url = url;
	if ( typeof ( domobj ) != 'object' )
		this.DomObjectId = domobj;
	else this.DomObjectId = domobj.id;
	this.initialize ( );
	var css = document.createElement ( 'link' );
	css.rel = 'stylesheet'; css.href = 'lib/javascript/css/gallerypopup.css';
	document.getElementsByTagName ( 'head' )[0].appendChild ( css );
	GalleryPopupList.push ( this );
}

GalleryPopup.prototype = GuiObject.prototype;

// Setup the image gallery popup system
GalleryPopup.prototype.initialize = function ()
{
	var dom = ge ( this.DomObjectId );
	var links = dom.getElementsByTagName ( 'a' );
	var ar = new Array ();
	this.images = ar;
	// Gather all a tags and collect / replace images 
	for ( var a = 0; a < links.length; a++ )
	{
		// Nod is the a tag
		var nod = links[a];
		if ( nod.link ) continue;
		var img;
		if ( !( img = nod.getElementsByTagName ( 'img' ) ) )
			continue;
		ar.push ( nod );
		img = img[0];
		nod.className = 'GalleryPopupImage';
		nod.onmouseover = function ()
		{ this.className = 'GalleryPopupImageOver'; }
		nod.onmouseout = function ()
		{ this.className = 'GalleryPopupImage'; }
		nod.link = ( nod.href + '' );
		nod.obj = this;
		nod.images = ar;
		nod.index = ar.length-1;
		// Make onclick function on the a tag
		nod.onclick = function ()
		{
			// Set index on object
			this.obj.index = this.index;
			
			// Copy some properties onto a new image
			var img = new Image ();
			img.obj = this.obj;
			img.images = this.obj.images;
			img.link = ( this.link + '' );
			// Load func
			img.onload = function ()
			{
				var cont = document.body;
				var bg = document.createElement ( 'div' );
				bg.className = 'GalleryPopupBackground';
				cont.appendChild ( bg );
				setOpacity ( bg, 0.0 );
				
				var box = document.createElement ( 'div' );
				box.className = 'GalleryPopupBox';
				
				var image = document.createElement ( 'div' );
				this.obj.image = image;
				
				// Set properties on image div to show image
				image.obj = this.obj;
				image.style.position = 'absolute';
				image.style.left = '0px';
				image.style.top = '0px';
				image.style.width = '0px';
				image.style.height = '0px';
				image.innerHTML = '<img src="' + this.link + '" style="width: 0px; height: 0px"/>';
				image.id = 'GalleryPopupImage';
				image.phase = 0;
				image.tw = this.width;
				image.th = this.height;
				image.ow = this.width;
				image.oh = this.height;
				image.style.boxShadow = '0px 2px 50px black';
				image.style.borderRadius = '4px';
				
				box.appendChild ( image );
				
				var white = document.createElement ( 'div' );
				white.id = 'GalleryImageWhitener';
				image.whitener = white;
				box.appendChild ( white );
				cont.appendChild ( box );
				image.bg = bg;
				bg.currentImage = image;
				this.obj.Constrain ();
				this.obj.Open();
				image.keyfunc = addEvent ( 'onkeydown', function ( e )
				{
					if ( !e ) e = window.event;
					var gpi = ge('GalleryPopupImage');
					if ( !gpi || gpi.keydown ) return;
					gpi.keydown = true;
					if ( gpi && gpi.bg ) 
					{
						var ind = gpi.obj.index;
						var w = e.which ? e.which : e.keyCode;
						switch ( w )
						{
							case 27: 
								gpi.obj.Close(); 
								break;
							case 39:
								gpi.obj.Resize ( ind+1, false );
								break;
							case 37:
								gpi.obj.Resize ( ind-1, false );
								break;
						}
					}
				}
				);
				image.keyfunc = addEvent ( 'onkeyup', function ( e )
				{
					var gpi = ge('GalleryPopupImage');
					gpi.keydown = false;
				}
				);
			}
			img.src = this.link;
		}
		nod.href = 'javascript:void(0)';
	}
}

// Open the image popup
GalleryPopup.prototype.Open = function ()
{
	var gpi = ge ( 'GalleryPopupImage' );
	if ( gpi.phase < 1 )
	{
		gpi.phase += 0.08;
		setTimeout ( 'var g = ge(\'GalleryPopupImage\'); if ( g && g.obj ) g.obj.Open()', 30 );
	}
	else 
	{ 
		this.ShowControls(); 
	}
	if ( gpi.phase > 1 ) gpi.phase = 1;
	var ph = Math.pow ( Math.sin ( gpi.phase * 0.5 * Math.PI ), 3 );
	var i = gpi.getElementsByTagName ( 'img' )[0];
	i.style.width = Math.floor ( gpi.tw * ph ) + 'px';
	i.style.height = Math.floor ( gpi.th * ph ) + 'px';
	gpi.style.width = i.style.width;
	gpi.style.height = i.style.height;
	gpi.style.top = -Math.floor ( gpi.th * ph * 0.5 ) - 22 + 'px';
	gpi.style.left = -Math.floor ( gpi.tw * ph * 0.5 ) + 'px';
	setOpacity ( gpi.bg, gpi.phase * 0.85 );
}

GalleryPopup.prototype.ShowControls = function ()
{
	// Shortcut to GalleryPopup object in case of function movement outside class
	var o = this.obj ? this.obj : this;
	//
	var next = document.createElement ( 'div' ); next.className = 'GalleryPopupNext';
	next.innerHTML = '<a href="javascript:void(0)">' + i18n ( 'Next' ) + ' <span>»</span></a>';
	var prev = document.createElement ( 'div' ); prev.className = 'GalleryPopupPrev';
	prev.innerHTML = '<a href="javascript:void(0)"><span>«</span> ' + i18n ( 'Previous' ) + '</a>';
	var close = document.createElement ( 'div' ); close.className = 'GalleryPopupClose';
	close.innerHTML = '<a href="javascript:void(0)"><span>X</span> ' + i18n ( 'Close' ) + '</a>';
	
	// Add them
	var gpi = ge('GalleryPopupImage');
	gpi.appendChild ( next );
	gpi.appendChild ( prev );
	gpi.appendChild ( close );
	
	// Actions
	close.img = gpi; next.img = gpi; prev.img = gpi;
	close.onclick = function () 
	{ 
		this.img.obj.Close(); 
	}
	next.onclick = function () 
	{ 
		this.img.obj.Resize ( this.img.obj.index + 1, false ); 
	}
	prev.onclick = function () 
	{ 
		this.img.obj.Resize ( this.img.obj.index - 1, false ); 
	}
	
	// Record
	this.bnext = next; this.bprev = prev; this.bclose = close;
	
	// Position
	o.PositionControls ();

	// Resize	
	this.Resize ( this.index, true );
}

// constrain proportions
GalleryPopup.prototype.Constrain = function ()
{
	var image = ge ( 'GalleryPopupImage' );
	var ww = window.innerWidth || document.documentElement.clientWidth;
	ww -= 60;
	var wh = window.innerHeight || document.documentElement.clientHeight;
	wh -= 90;
	image.tw = image.ow;
	image.th = image.oh;
	if ( image.tw > ww )
	{
		var mul = image.tw;
		image.tw = ww - 20;
		image.th = ( image.th / mul ) * image.tw;
	}
	if ( image.th > wh )
	{
		var mul = image.th;
		image.th = wh - 20;
		image.tw = ( image.tw / mul ) * image.th;
	}
}

GalleryPopup.prototype.PositionControls = function ()
{
	var gpi = ge('GalleryPopupImage');
	
	// Position
	var prev = this.bprev;
	var close = this.bclose;
	var next = this.bnext;
	
	if ( prev )
	{
		prev.style.top = Math.floor ( gpi.th ) + 'px';
		prev.style.left = '0px';
	}
	if ( next )
	{
		next.style.right = '0px';
		next.style.top = Math.floor ( gpi.th ) + 'px';
	}
	if ( close )
	{
		close.style.left = Math.floor ( getElementWidth ( gpi ) * 0.5 ) - 
			Math.floor ( getElementWidth ( close ) * 0.5 ) + 'px';
		close.style.top = Math.floor ( gpi.th ) + 'px';
	}
}

// Close the popup
GalleryPopup.prototype.Close = function ()
{
	if ( !this.closing )
	{
		setTimeout ( 'var g = ge(\'GalleryPopupImage\'); if ( g && g.obj ) g.obj.Close()', 30 );
		this.closeTime = ( new Date () ).getTime ();
		this.closing = true;
		return;
	}
	var gpi = ge('GalleryPopupImage');	
	if ( !gpi ) return;
	
	// removes the key checker
	removeEvent ( 'onkeydown', this.keyfunc );
	
	// remove controls
	if ( this.bnext ) 
	{
		this.bnext.parentNode.removeChild ( this.bnext );
		this.bprev.parentNode.removeChild ( this.bprev );
		this.bclose.parentNode.removeChild ( this.bclose );
		this.bnext = false;
	}
	
	var phase = ( ( ( new Date () ).getTime () ) - this.closeTime ) / 400;
	if ( phase > 1 ) phase = 1;
	phase = 1 - phase;
	if ( phase == 0 )
	{
		gpi.bg.parentNode.removeChild ( gpi.bg );
		gpi.parentNode.parentNode.removeChild ( gpi.parentNode );
		this.closing = false;
		return;
	}
	var ph = Math.pow ( Math.sin ( phase * 0.5 * Math.PI ), 3 );
	var i = gpi.getElementsByTagName ( 'img' )[0];
	i.style.width = Math.floor ( gpi.tw * ph ) + 'px';
	i.style.height = Math.floor ( gpi.th * ph ) + 'px';
	gpi.style.width = i.style.width;
	gpi.style.height = i.style.height;
	gpi.style.top = -Math.floor ( gpi.th * ph * 0.5 ) - 22 + 'px';
	gpi.style.left = -Math.floor ( gpi.tw * ph * 0.5 ) + 'px';
	setOpacity ( gpi.bg, phase * 0.85 );
	setTimeout ( 'var g = ge(\'GalleryPopupImage\'); if ( g && g.obj ) g.obj.Close()', 30 );
}

// Resize the popup to new image dimensions
GalleryPopup.prototype.Resize = function ( arg, click )
{
	if ( typeof ( arg ) != 'undefined' )
	{
		if ( arg < 0 ) arg = 0;
		if ( arg >= this.images.length ) 
			arg = this.images.length - 1;
		var i = new Image ();
		i.obj = this;
		i.p = function ()
		{
			var gpi = ge ( 'GalleryPopupImage' );
			this.obj.index = arg;
			if ( arg > 0 ) this.obj.bprev.style.display = '';
			else this.obj.bprev.style.display = 'none';
			if ( arg < this.obj.images.length - 1 ) this.obj.bnext.style.display = '';
			else this.obj.bnext.style.display = 'none';
			_gpTweeno.image = this;
			this.width = Math.floor ( this.width );
			this.height = Math.floor ( this.height );
			gpi.ow = this.width;
			gpi.oh = this.height;
			this.obj.Constrain ();
			_gpTweeno.action = 'changeimage';
			_gpTweenResize ( gpi.tw, gpi.th, gpi, click ? true : false );
		}
		i.onload = function () { this.p(); }
		i.src = this.images[arg].link;
		
		if ( i.width && i.height ) i.p();
	}
	else if ( ge ( 'GalleryPopupImage' ) )
	{
		// Constrain to proportions
		var image = ge ( 'GalleryPopupImage' );
		image.obj.Constrain ();
		_gpTweeno.action = 'resize';
		_gpTweenResize ( image.tw, image.th, image, click ? true : false );
	}
}

// Resize the popup to new image dims animated (called by Resize())
_gpTweeno = new Object ();
_gpTweenResize = function ( width, height, obj, firstTime )
{
	if ( width )
	{
		if ( _gpTweeno.timeout ) clearTimeout ( _gpTweeno.timeout );
		_gpTweeno.timeout = false;
		_gpTweeno.twidth = width;
		_gpTweeno.firstTime = firstTime ? true : false;
		_gpTweeno.theight = height;
		_gpTweeno.owidth = parseInt ( obj.style.width );;
		_gpTweeno.oheight = parseInt ( obj.style.height );
		_gpTweeno.spanw = width - parseInt ( obj.style.width );
		_gpTweeno.spanh = height - parseInt ( obj.style.height );
		_gpTweeno.obj = obj;
		_gpTweeno.timer = ( new Date () ).getTime ();
		_gpTweenResize ();
	}
	else
	{
		var o = _gpTweeno;
		var gpi = _gpTweeno.obj;
		
		var p = ( ( new Date () ).getTime () - _gpTweeno.timer ) / 500;
		if ( p >= 1 || o.firstTime )
		{
			p = 1;
			o.firstTime = false;
			o.obj.whitener.style.display = 'none';
		}
		if ( p >= 0.5 ) 
		{
			var i = gpi.getElementsByTagName ( 'img' )[0];
			i.src =  gpi.obj.images[gpi.obj.index].link;
			//gpi.style.backgroundImage = 'url(' + gpi.obj.images[gpi.obj.index].link+')';
		}
		if ( p != 1 )
		{
			// White fade effect
			if ( _gpTweeno.action == 'changeimage' )
			{
				var wi = gpi.whitener;
				wi.style.display = '';
				var wiopacity = Math.sin ( p * Math.PI );
				setOpacity ( wi, wiopacity );
			}
			
			// Image dimension animation
			var pp = Math.pow ( Math.sin ( p * 0.5 * Math.PI ), 3 );
			var nw = o.owidth + ( o.spanw * pp );
			var nh = o.oheight + ( o.spanh * pp );
			var i = gpi.getElementsByTagName ( 'img' )[0];
			i.style.width = Math.floor ( nw ) + 'px';
			i.style.height = Math.floor ( nh ) + 'px';
			gpi.style.width = i.style.width;
			gpi.style.height = i.style.height;
			gpi.style.left = Math.floor ( 0 - ( 0.5 * parseInt ( gpi.style.width ) ) ) + 'px';
			gpi.style.top = Math.floor ( 0 - ( 0.5 * parseInt ( gpi.style.height ) ) ) - 22 + 'px';
			gpi.tw = Math.floor ( nw );
			gpi.th = Math.floor ( nh );
			gpi.obj.PositionControls ();
			if ( _gpTweeno.action == 'changeimage' )
			{
				var wi = gpi.whitener;
				wi.style.position = 'absolute';
				wi.style.top = gpi.style.top;
				wi.style.left = gpi.style.left;
				wi.style.width = gpi.style.width;
				wi.style.height = gpi.style.height;
				wi.style.background = 'white';
			}
			setTimeout ( '_gpTweenResize()', 10 );
		}
	}
}

function __GalleryPopupResize ()
{
	for ( var a = 0; a < GalleryPopupList.length; a++ )
	{
		GalleryPopupList[a].Resize ();
	}
}

if ( navigator.userAgent.indexOf ( 'MSIE' ) >= 0 )
	window.attachEvent ( 'onresize', __GalleryPopupResize );
else window.addEventListener ( 'resize', __GalleryPopupResize );

