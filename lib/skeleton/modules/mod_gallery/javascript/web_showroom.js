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


var Showroom = new Object();
Showroom.init = function ( ele, w, h )
{
	this.container = document.getElementById ( ele );
	this.container.object = this;
	this.w = w; this.h = h;
	this.index = 0;
	var imgs = this.container.getElementsByTagName ( 'span' );
	var imgA = new Array ();
	this.pages = new Array ();
	var p = document.createElement ( 'div' );
	p.className = 'Pages';
	this.direction = false;
	this.start = false;
	var l = imgs.length;
	for ( var a = 0; a < l; a++ )
	{
		var d = document.createElement ( 'div' );
		d.innerHTML = '<span>' + (a+1) + '</span>';
		d.object = this;
		d.index = a;
		d.onclick = function () { clearInterval ( this.object.interval ); this.object.changePage(this.index); }
		d.className = 'Page';
		p.appendChild ( d );
		this.pages.push ( d );
		imgA.push ( imgs[a] );
		if ( a == 0 ) d.className = 'PageActive';
	}
	var ar = document.createElement ( 'div' );
	ar.className = 'Arrows';
	var an = document.createElement ( 'div' ); an.className = 'ArrowNext';
	var ap = document.createElement ( 'div' ); ap.className = 'ArrowPrev';
	an.object = this; ap.object = this;
	an.onclick = function () { clearInterval ( this.object.interval ); this.object.changePage ( this.object.index+1 ); }
	ap.onclick = function () { clearInterval ( this.object.interval ); this.object.changePage ( this.object.index-1 ); }
	an.innerHTML = '<span>»</span>';
	ap.innerHTML = '<span>«</span>';
	ar.appendChild ( ap ); ar.appendChild ( an );
	this.container.appendChild ( p );
	this.container.appendChild ( ar );
	this.arrows = [ ap, an ];
	var i = document.createElement ( 'div' );
	i.className = 'ImageContainer';
	var k = document.createElement ( 'div' );
	k.className = 'ImageDescriptions';
	this.images = new Array ();
	this.descriptions = new Array ();
	for ( var a = 0; a < l; a++ )
	{
		var d = document.createElement ( 'div' );
		if ( a == 0 ) d.style.right = '0%';
		if ( a == 0 ) d.className = 'ImageCurrent'; else d.className = 'Image';
		if ( a == 0 ) d.style.zIndex = '11';
		else d.style.zIndex = '10';
		var ds = document.createElement ( 'div' );
		ds.className = a == 0 ? 'DescriptionCurrent' : 'Description';
		ds.setAttribute ( 'tags', imgs[a].getAttribute ( 'tags' ) );
		var oncl = imgs[a].getAttribute ( 'onclick' );
		ds.className += ' ' + imgs[a].getAttribute ( 'tags' );
		if ( a == 0 ) 
		{
			var im = document.createElement ( 'img' );
			im.src = imgs[a].getAttribute ( 'title' );
			im.description = imgs[a].getAttribute ( 'description' );
			im.onload = function ()
			{
				this.style.width = this.width + 'px';
				this.style.height = this.height + 'px';
			}
			d.appendChild ( im );
			d.notLoaded = false;
		}
		else 
		{
			var o = new Object ();
			o.src = imgs[a].getAttribute ( 'title' );
			im.description = imgs[a].getAttribute ( 'description' );
			d.notLoaded = o;
		}
		ds.innerHTML = '<h2>' + im.description + '</h2>';
		if ( imgs[a].getAttribute ( 'extended' ).length )
			ds.innerHTML += imgs[a].getAttribute ( 'extended' );
		i.appendChild ( d );
		d.object = this;
		d.oncl = oncl;
		if ( oncl ) d.setAttribute ( 'hasLink', 1 );
		d.onclick = function ()
		{
			if ( oncl )
			{
				if ( window.execScript )
					window.execScript ( '(' + oncl + ')' );
				else eval ( oncl );
			}
			clearInterval ( this.object.interval );
			if ( this.object.direction == 'next' )
				this.object.changePage ( this.object.index - 1 );
			else if ( this.object.direction == 'prev' )
				this.object.changePage ( this.object.index + 1 );
		}
		this.images.push ( d );
		if ( this.images.length > 1 ) 
			setOpacity ( d, 0 );
		this.descriptions.push ( ds );
		k.appendChild ( ds );
		setTimeout ( 'ShowroomDelayedLoading()', 100 );
	}
	for ( var a in imgA )
	{
		if ( imgA[a].parentNode == this.container )
			this.container.removeChild ( imgA[a] );		
	}
	this.container.appendChild ( i );
	this.container.appendChild ( k );
	this.container.style.width = w + 'px';
	this.container.style.height = h + 'px';
	this.container.className = 'Showroom';
	this.changePage = function ( ind )
	{
		if ( ind == 'next' ) ind = this.index + 1;
		if ( this.start > 0 ) return;
		this.pindex = this.index;
		if ( ind >= this.images.length ) ind = 0;
		else if ( ind < 0 ) ind = this.images.length - 1;
		if ( ind == this.index ) return;
		// Only change page if image is loaded, else delay operation
		if ( this.images[ind].firstChild.width > 0 )
		{
			this.index = ind;
			this.tweener ();
		}
		else setTimeout ( 'document.getElementById(\''+this.container.id+'\').object.changePage(\''+ind+'\')', 10 );
	}
	this.tweener = function ( running )
	{
		if ( !running )
		{
			this.start = ( new Date () ).getTime ();
			setOpacity ( this.images[this.index], 0 );
			this.images[this.index].style.right = '0%';
			for ( var a = 0; a < this.images.length; a++ )
			{
				if ( a != this.index && a != this.pindex )
					this.images[a].style.zIndex = '10';
			}
			this.images[this.index].style.zIndex = '12';
			this.images[this.pindex].style.zIndex = '11';
			return setTimeout ( 'document.getElementById(\''+this.container.id+'\').object.tweener(1)', 5 );
		}
		else
		{
			var p = ( ( new Date () ).getTime () - this.start ) / 1000;
			if ( p >= 1 ) p = 1;
			var pp = Math.pow ( Math.sin ( p * 0.5 * Math.PI ), 3 );
			setOpacity ( this.images[this.index], pp );
			if ( p < 1 )
			{
				this.tm = setTimeout ( 'document.getElementById(\''+this.container.id+'\').object.tweener(1)', 5 );
			}
			else 
			{
				this.images[this.pindex].style.right = '100%';
				this.start = false;
				clearTimeout ( this.tm ); this.tm = 0;
				for ( var a = 0; a < this.pages.length; a++ )
				{
					if ( a == this.index )
					{
						this.pages[a].className = 'PageActive';
						this.images[a].className = 'ImageCurrent';
						this.descriptions[a].className = 'DescriptionCurrent';
						this.descriptions[a].className += ' ' + this.descriptions[a].getAttribute ( 'tags' );
					}
					else 
					{
						this.pages[a].className = 'Page';
						this.images[a].className = 'Image';
						this.descriptions[a].className = 'Description';
						this.descriptions[a].className += ' ' + this.descriptions[a].getAttribute ( 'tags' );
					}
					
				}
			}
		}
	}
	/*/this.container.onmousemove = function ( e )
	{
		var coordx = e.clientX ? e.clientX : e.pageXOffset;
		var coordy = e.clientY ? e.clientY : e.pageYOffset;
		var l = 0; var p = this;
		var t = 0;
		while ( p != document.body && p )
		{
			l += p.offsetLeft;
			t += p.offsetTop;
			p = p.offsetParent;
		}
		coordx -= l;
		coordy -= t;
		var pr = this.object.arrows[0];
		var nr = this.object.arrows[1];
		if ( coordy >= 0 && coordy < this.offsetHeight )
		{
			if ( coordx < ( this.offsetWidth * 0.5 ) )
			{
				pr.className = 'ArrowPrevActive';
				nr.className = 'ArrowNext';
				this.object.direction = 'next';
			}
			else 
			{
				pr.className = 'ArrowPrev';
				nr.className = 'ArrowNextActive';
				this.object.direction = 'prev';
			}
		}
	}/*/
	this.container.onmouseout = function ()
	{
		var pr = this.object.arrows[0];
		var nr = this.object.arrows[1];
		pr.className = 'ArrowPrev';
		nr.className = 'ArrowNext';
		this.object.direction = false;
	}
	this.interval = setInterval ( 'document.getElementById(\''+this.container.id+'\').object.changePage(\'next\');', 6000 );
}
function ShowroomDelayedLoading()
{
	for ( var a = 0; a < Showroom.images.length; a++ )
	{
		if ( Showroom.images[a].notLoaded )
		{
			var img = Showroom.images[a].notLoaded;
			var im = document.createElement ( 'img' );
			im.src = img.src;
			im.description = img.description;
			im.onload = function ()
			{
				this.style.width = this.width + 'px';
				this.style.height = this.height + 'px';
			}
			Showroom.images[a].appendChild ( im );
			Showroom.images[a].notLoaded = false;
		}
	}
}
