
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

document.arenaGalleries = new Array ( );
var arenaGallery = function ()
{
	this.aBoxWidth = 30;
	this.galleryAnimated = false;
	this.index = document.arenaGalleries.length;
	this.galleryWidth = 320;
	this.galleryHeight = 240;
	this.galleryPause = 2;
	this.gallerySpeed = 200;
	this.initialized = false;
	this.stop = false;
	document.arenaGalleries[this.index] = this;
}
arenaGallery.prototype.getTime = function ()
{
	return ( new Date () ).getTime ();
}
arenaGallery.prototype.init = function ( pelement )
{
	// Get container div
	pelement = document.getElementById ( pelement );
	pelement.setAttribute ( 'GalleryWidth', this.galleryWidth );
	this.pelement = pelement;
	
	// Prepare images
	var images = pelement.getElementsByTagName ( 'img' );
	var galleryImages = new Array ( );
	for ( var a = 0; a < images.length; a++ )
	{
		var i = new Image ( );
		i.src = images[a].src;
		i.description = images[a].getAttribute ( 'alt' );
		i.rel = images[a].getAttribute ( 'rel' );
		i.tags = images[a].getAttribute ( 'tags' );
		galleryImages.push ( i );
	}
	pelement.innerHTML = '';
	pelement.className = 'ArenaGallery';
	
	// Setup container
	var gc = document.createElement ( 'div' );
	gc.className = 'GalleryContainer';
	gc.img1 = document.createElement ( 'div' ); gc.img1.className = 'Image1';
	gc.img2 = document.createElement ( 'div' ); gc.img2.className = 'Image2';
	gc.setAttribute ( 'CurrentElement', '1' );
	gc.imgs = galleryImages;
	gc.currentImage = 0;
	pelement.style.width = this.galleryWidth + 'px';
	pelement.style.height = ( this.galleryHeight + 27 ) + 'px';
	gc.style.width = pelement.style.width;
	gc.style.height = pelement.style.height;
	
	// Setup pages
	var pages = document.createElement ( 'div' );
	pages.className = 'Pages';
	for ( var a = 0; a < galleryImages.length; a++ )
	{
		var at = document.createElement ( 'a' );
		at.o = this;
		at.innerHTML = a+1;
		at.pel = pelement.id;
		at.i = a;
		at.onmouseover = function ( ){ clearInterval ( this.o.galInterval ); this.o.galInterval = false; }
		at.onclick = function ( ){ this.o.SetGalleryImage ( this.pel, this.i ); }
		at.href = "javascript: void(0)";
		at.style.left = (this.aBoxWidth+1)+(a*(this.aBoxWidth+1)) + "px";
		pages.appendChild ( at );
	}
	// Arrow back / forth
	var arF = document.createElement ( 'div' ); arF.className = "ArrowNext";
	var arB = document.createElement ( 'div' ); arB.className = "ArrowPrev";
	arF.o = this; arB.o = this;
	arF.el = pelement.id; arB.el = pelement.id;
	arF.onclick = function ( ){ this.o.NextGalleryImage ( this.el ); }
	arB.onclick = function ( ){ this.o.PrevGalleryImage ( this.el ); }
	gc.appendChild ( arF ); gc.appendChild ( arB );
	
	// Append children
	gc.appendChild ( gc.img1 ); gc.appendChild ( gc.img2 );
	gc.appendChild ( pages );
	pelement.gc = gc;
	gc.pages = pages;
	pelement.appendChild ( gc );
	this.SetGalleryImage ( pelement.id, 0 );
	
	// galInterval adaptation to soya
	if ( this.galleryAnimated )
	{
		clearInterval ( this.galInterval );
		this.galInterval = setInterval ( 
			'document.arenaGalleries[' + this.index + '].GalNextButton ( )', 
			this.galleryPause * 1000 
		);
	}
}
arenaGallery.prototype.setOp = function ( el, val )
{
	if ( navigator.userAgent.indexOf ( 'MSIE' ) >= 0 ) 
	{
		if ( val == 1 )
			el.style.filter = null;
		else el.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(Opacity=' + Math.round( val * 100 ) + ')';
	}
	else el.style.opacity = val;
}
arenaGallery.prototype.NextGalleryImage = function ( pelement )
{
	if ( !pelement ) pelement = this.pelement;
	else pelement = document.getElementById ( pelement );
	var gc = pelement.gc;
	this.SetGalleryImage ( pelement.id, ++gc.currentImage );
}
arenaGallery.prototype.PrevGalleryImage = function ( pelement )
{
	if ( !pelement ) pelement = this.pelement;
	else pelement = document.getElementById ( pelement );
	var gc = pelement.gc;
	this.SetGalleryImage ( pelement.id, --gc.currentImage );
}
arenaGallery.prototype.SetGalleryImage = function ( pelement, num )
{
	// This time 
	this._currentTime = this.getTime();
	
	// Get container div
	if ( !pelement ) pelement = this.pelement;
	else pelement = document.getElementById ( pelement );
	var gc = pelement.gc;
	if ( gc.intv ) { clearInterval ( gc.intv ); gc.intv = false; }
	
	// Check current image
	if ( !num && !(num === 0) ) num = gc.currentImage;
	else gc.currentImage = num;
	if ( gc.currentImage >= gc.imgs.length )
		gc.currentImage = gc.imgs.length - 1;
	else if ( gc.currentImage < 0 ) gc.currentImage = 0;
	num = gc.currentImage;
	
	// Activate page
	var as = gc.pages.getElementsByTagName ( 'a' );
	var aGalWidth = parseInt ( pelement.getAttribute ( 'GalleryWidth' ) );
	var boxesInHalfWidth = Math.round ( ( aGalWidth / 2 ) / this.aBoxWidth );
	if ( !gc.oWidth )
	{
		gc.oWidth = parseInt ( as[as.length-1].style.left ) + this.aBoxWidth;
	}
	for ( var a = 0; a < as.length; a++ )
	{
		if ( a == num )
		{
			as[a].className = 'Active';
			if ( gc.oWidth >= aGalWidth - this.aBoxWidth )
			{
				// Offset looks if a (clicked item) has offset >= (aGalWidth / 2) before
				// moving the whole page row
				var offset = (this.aBoxWidth+1)*(a>=(boxesInHalfWidth-1)?boxesInHalfWidth:(a+1));
				var skew = a*(this.aBoxWidth+1);
				for ( var b = 0; b < as.length; b++ )
				{
					var basepos = b*(this.aBoxWidth+1);
					as[b].style.left = (offset-skew+basepos)+"px";
				}
			}
		}
		else as[a].className = '';
	}
	// Rolling over image makes animation stop!
	gc.img2.gallery = this;
	gc.img2.onmouseover = function ()
	{
		this.gallery.stop = true;
	}
	gc.img2.onmouseout = function ()
	{
		this.gallery.stop = false;
	}
	// Start fade routine
	if ( gc.img2.innerHTML.length )
	{
		var dom = document.createElement ( 'div' );
		dom.className = "Image"; 
		dom.style.background 	= gc.img2.firstChild.style.background; 
		dom.style.width 		= gc.img2.firstChild.style.width;
		dom.style.height 		= gc.img2.firstChild.style.height;
		if ( gc.img1.firstChild ) gc.img1.replaceChild ( dom, gc.img1.firstChild );
		else gc.img1.appendChild ( dom );
		gc.img1.style.top = '0px'; gc.img1.style.left = '0px';
	}
	this.setOp ( gc.img2, 0 );
	gc.img2.op = 0;
	gc.img2.innerHTML = '<div class="Image" ' +
						'style="background:url(\'' + gc.imgs[num].src + '\') no-repeat top left; width:' + 
						this.galleryWidth + 'px; height: ' + ( this.galleryHeight ) + 'px"></div>';
	gc.parentNode.setAttribute ( 'tags', gc.imgs[num].tags );
	if ( gc.imgs[num].description && gc.imgs[num].description.length )
	{
		var desc = gc.imgs[num].description.split ( '#--quote--#' ).join ( '"' );
		if (  desc.indexOf ( 'http://' ) >= 0 && desc.indexOf( '<param' ) < 0 )
		{
			// also show the text with a link
			var test = gc.imgs[num].description.split ( 'http://' );
			if ( test[0].length > 0 && desc && desc.toLowercase && desc.toLowercase().indexOf ( '<a href' ) < 0 )
			{
				gc.img2.innerHTML += '<div class="Description">' + test[0] + '</div>';
				var cl = document.createElement ( 'div' ); cl.className = 'Close'; cl.innerHTML = 'X';
				cl.onclick = function (){ this.parentNode.style.display = 'none'; }
				gc.img2.getElementsByTagName ( 'div' )[1].appendChild ( cl );
			}
			// click
			var div = gc.img2.getElementsByTagName ( 'div' )[0];
			if( navigator.userAgent.indexOf ( 'MSIE' ) >= 0 )
				div.style.cursor = 'hand';
			else div.style.cursor = 'pointer';
			var bref = document.getElementsByTagName ( 'base' )[0].href;
			if ( ( 'http://' + test[1] ).indexOf ( bref ) >= 0 )
				div.onclick = function (){ document.location = 'http://'+test[1]; }
			else div.onclick = function (){ window.open( 'http://'+test[1], '', '' ); }
			if ( gc.img2.getElementsByTagName ( 'div' ).length > 1 )
			{
				gc.img2.getElementsByTagName ( 'div' )[1].onclick = div.onclick;
				gc.img2.getElementsByTagName ( 'div' )[1].style.cursor = div.style.cursor;
			}
		}
		else
		{
			gc.img2.innerHTML += '<div class="Description">' + desc + '</div>';
			var cl = document.createElement ( 'div' ); cl.className = 'Close'; cl.innerHTML = 'X';
			cl.onclick = function (){ this.parentNode.style.display = 'none'; }
			gc.img2.getElementsByTagName ( 'div' )[1].appendChild ( cl );
		}
	}
	var pad = navigator.userAgent.indexOf ( 'iPad' ) >= 0 ? true : false;
	var funcToRun = 'document.arenaGalleries[' + this.index + '].FadeGalleryImageIn(\''+pelement.id+'\')';
	var funcTimeC = pad ? 100 : 25;
	// Make sure image has loaded!
	if ( gc.imgs[num].width > 0 && gc.imgs[num].height > 0 )
		gc.intv = setInterval ( funcToRun, funcTimeC );
	else
	{
		gc.imgs[num].onload = function () 
		{
			gc.intv = setInterval ( funcToRun, funcTimeC );
		}
	}
}
arenaGallery.prototype.FadeGalleryImageIn = function ( el )
{
	el = document.getElementById ( el );
	var gc = el.gc;
	var alpha = ( this.getTime ()-this._currentTime ) / this.gallerySpeed;
	if ( alpha > 1 ) alpha = 1;
	var power = Math.sin ( Math.pow ( alpha, 2 ) * 0.5 * Math.PI );
	gc.img2.op = alpha;
	// First show, immediately show image
	if ( !this.initialized )
	{
		this.initialized = true;
		alpha = 1;
		gc.img2.op = 1;
		power = 1;
		this.setOp ( gc.img2, alpha );
		clearInterval ( gc.intv );
		return;
	}
	var gwP = Math.floor ( this.galleryWidth * power );
	switch ( this.galleryShowStyle )
	{
		case 'showstyle_scrollx':
			this.setOp ( gc.img2, alpha );
			gc.img1.style.left = ( 0 - gwP ) + 'px';
			gc.img2.style.left = ( this.galleryWidth - gwP ) + 'px';
			break;
		case 'showstyle_scrolly':
			this.setOp ( gc.img2, alpha );
			gc.img1.style.top = ( 0 - gwP ) + 'px';
			gc.img2.style.top = ( this.galleryHeight - gwP ) + 'px';
			break;
		default:
			this.setOp ( gc.img2, alpha );
			break;
	}
	if ( gc.img2.op > 1 )
	{
		clearInterval ( gc.intv );
		gc.intv = false;
		this.isActive = 0;
	}
	else
	{
		this.isActive = 1;
	}
}
arenaGallery.prototype.GalNextButton = function ( )
{
	// If stop is flagged, don't use next button
	if ( this.stop )
	{
		return false;
	}
	var buts = this.pelement.getElementsByTagName ( 'a' );
	var active = false;
	for ( var a = 0; a < buts.length; a++ )
	{
		if ( buts[a].className == 'Active' )
			active = buts[a];
	}
	if ( !active ) active = buts[0];
	// Do next one
	for ( var a = 0; a < buts.length; a++ )
	{
		if ( buts[a] == active && buts[a+1] )
		{
			return this.SetGalleryImage ( this.pelement.id, a+1 );
		}
	}
	this.SetGalleryImage ( this.pelement.id, 0 );
}


var imageview;
var imagebg;
var cachedImages = new Array ();
function showImage ( fn, imageid )
{
	if ( !imageview )
	{
		imagebg = document.createElement ( 'div' );
		imagebg.className = 'Imagebackground';
		imagebg.style.position = 'fixed';
		imagebg.style.opacity = '0';
		imagebg.style.top = '0px';
		imagebg.style.left = '0px';
		imagebg.style.width = '100%';
		imagebg.style.height = '100%';
		
		imageview = document.createElement ( 'div' );
		imageview.className = 'Imagecenter';
		imageview.style.position = 'fixed';
		imageview.style.top = '50%';
		imageview.style.left = '50%';
		
		var img = document.createElement ( 'div' );
		imagebg.img = img;
		img.className = 'Imagecontainer';
		img.style.position = 'absolute';
		img.style.top = '0px';
		img.style.left = '0px';
		img.style.width = '0px';
		img.style.height = '0px';
		img.style.opacity = 0;
		img.style.overflow = 'hidden';
		img.style.cursor = 'pointer';
		img.style.border = '0px solid white';
		img.onclick = function ()
		{
			img.style.opacity = 0;
			img.style.width = '0px';
			img.style.height = '0px';
			img.style.top = '0px';
			img.style.left = '0px';
			imagebg.style.opacity = 0;
			img.style.border = '0px solid white';
			setTimeout ( 'imagebg.style.display = "none"; imagebg.img.style.display = "none"', 500 );
		}
		
		imageview.appendChild ( img );
		document.getElementById ( 'Empty__' ).appendChild ( imagebg );
		document.getElementById ( 'Empty__' ).appendChild ( imageview );
	}
	
	if ( !cachedImages[fn] )
	{
		var theImage = new Image ();
		theImage.src = fn;
		theImage.onload = function ()
		{
			var box = imageview.getElementsByTagName ( 'div' )[0];
			box.innerHTML = '<img src="' + this.src + '" width="' + this.width + '" height="' + this.height + '"/>';
			box.style.top = 0-(20+Math.floor ( (this.height*0.5) )) + 'px';
			box.style.left = 0-(20+Math.floor ( (this.width*0.5) )) + 'px';
			box.style.width = this.width + 'px';
			box.style.height = this.height + 'px';
			box.style.opacity = 1;
			imagebg.style.opacity = 0.8;
			imagebg.style.display = '';
			imagebg.img.style.display = '';
			box.style.border = '20px solid white';
			if ( imageid )
			{
				box.j = new bajax (); box.j.b = box; box.j.image = this;
				box.j.openUrl ( 
					document.location.href.split ( '?' )[0]+'?imagefunc=getImageDescription&id='+imageid, 
					'get', true 
				);
				box.j.onload = function ()
				{
					if ( !this.b ) return;
					if ( this.getResponseText().indexOf ( 'ImageDescription' ) > 0 )
					{
						this.b.innerHTML += this.getResponseText ();
						this.b.style.height = this.image.height + 
							this.b.getElementsByTagName ( 'div' )[0].offsetHeight + 'px';
					}
				}
				box.j.send ();
			}
		}
		theImage.f = function (){ this.onload(); }
		cachedImages[fn] = theImage;
	}
	else
	{
		cachedImages[fn].f();
	}		
}

