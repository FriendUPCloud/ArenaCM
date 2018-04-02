

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



/**
 *
 * BlestBox v0.5
 * © 2006-2009 Hogne Titestad <hogne@blest.no>
**/

/**
 * Some vars
**/
var blestBoxData = new Object ( );
blestBoxData.opacity;
blestBoxData.backgroundopacity = 1;
blestBoxData.graphicspath = '';
blestBoxData.targetwidth;
blestBoxData.targetheight;
blestBoxData.image;
blestBoxData.phase;
blestBoxData.background;
blestBoxData.foreground;
blestBoxData.imagerect;
blestBoxData.container;
blestBoxData.type;
blestBoxData.element;
blestBoxData.pageelement;
blestBoxData.closebutton;
blestBoxData.contentloaded;
blestBoxData.busy;
blestBoxData.flashhidden = false;
blestBoxData.pagebackground = '#000';
blestBoxData.shadowimage = '';
blestBoxData.backgroundcolor = '#000';
blestBoxData.foregroundcolor = '#000';
blestBoxData.closebuttonbackground = '#000';
blestBoxData.pagewidth = 640;
blestBoxData.pageheight = 480;
blestBoxData.elementCache = new Array ( );
blestBoxData.zoombackground = true;
blestBoxData.usestyles = true;
blestBoxData.closepagetext = 'Close page';
blestBoxData.closeimagetext = 'Close image';
blestBoxData.animationpath = '';
blestBoxData.cache = new Array ( );

// get the images etc for animating, and preload them
blestBoxData.animbox = false;
blestBoxData.animboxframesurls = new Array ( 
	'animation/loading_01.png', 'animation/loading_02.png', 'animation/loading_03.png', 'animation/loading_04.png',
	'animation/loading_05.png', 'animation/loading_06.png', 'animation/loading_07.png', 'animation/loading_08.png'
);
blestBoxData.animboxframes = new Array ( );

blestBoxData.loadretries = 20;
blestBoxData.loadAnim = function ( )
{
	if ( !this.graphicspath || !this.animationpath )
	{
		var base = document.getElementsByTagName ( 'base' );
		if ( base[ 0 ] ) this.graphicspath = base[ 0 ].href;
		else if ( this.loadretries-- > 0 )
		{
			setTimeout ( 'blestBoxData.loadAnim ( )', 40 );
			return;
		}
	}
	this.animboxframes = new Array ( );
	for ( var a = 0; a < this.animboxframesurls.length; a++ )
	{
		var n = this.animboxframesurls[ a ];
		this.animboxframes[ a ] = new Image ( );
		if ( this.animationpath )
		{
			n = this.animationpath + ( n.split ( '/' )[1] );
		} else n = this.graphicspath + n;
		this.animboxframes[ a ].src = n;
	}
}
//TODO: Reimplement animation!
//blestBoxData.loadAnim ( );

/**
 * Resize the blestbox to new width and height
**/
function BlestBoxResize ( w, h, queuefunc )
{
	// Soon!
	alert ( "Soon implemented! :))" );
}


/**
 * Initialize the blestbox
**/
blestBoxData.prepare = function ( )
{
	if ( !document.body ) return;
	if ( !document.getElementById ( 'Footer__' ) && typeof ( ArenaMode ) != 'admin' )
	{
		setTimeout ( 'blestBoxData.prepare ( )', 40 );
	}
	else
	{
		var eles = document.body.getElementsByTagName ( 'A' );
		if ( eles.length )
		{
			for ( var a = 0; a < eles.length; a++ )
			{
				if ( eles[ a ].bbinitialized == true ) continue;
				var str = eles[ a ].className.split ( ' ' );
				for ( var b = 0; b < str.length; b++ )
				{
					if ( str[ b ] == 'blestbox' || str[ b ] == 'BoxPopup' )
					{
						for ( var e = 0; e < this.elementCache.length; e++ )
						{
							if ( this.elementCache[ e ].node == eles[ a ] )
							{
								eles[ a ].style.visibility = 'visible';
								continue;
							}
						}
						eles[ a ].className = str.join ( ' ' ).split ( 'BoxPopup' ).join ( 'blestbox' ); 
						var ts = eles[ a ].getAttribute ( 'blestboxurl' );
						var o = new Object ( );
						o.node = eles[ a ];
						o.url = ts ? ts : eles[ a ].href;
						this.elementCache.push ( o );
						eles[ a ].style.visibility = 'visible';
						eles[ a ].href = 'javascript: void ( 0 )';
						break;
					}
				}
			}
		}
	}
}
function BlestBox ( container )
{
	if ( !container )
	{
		if ( !blestBoxData.container && container )
		{
			blestBoxData.container = container;
		}
		else
		{
			blestBoxData.container = document.getElementById ( 'Empty__' );
		}
	}
	else
	{
		blestBoxData.container = container;
	}
	blestBoxData.prepare ( );
	
	var elements = document.body.getElementsByTagName ( 'A' );
	for ( var a = 0; a < elements.length; a++ )
	{
		if ( hasClass ( elements[ a ], 'blestbox' ) && !elements[ a ].bbinitialized )
		{
			elements[ a ].bbinitialized = true;
			/**
			 * Create the rules for this object
			**/
			var i = 0; var bb = false; for ( ; i < blestBoxData.elementCache.length; i++ )
			{
				if ( blestBoxData.elementCache[ i ].node == elements[ a ] )
				{
					bb = blestBoxData.elementCache[ i ];
					break;
				}
			}
			if ( bb )
			{
				if ( bb.url.indexOf ( '.html' ) > 0 || bb.url.indexOf ( '.php' ) > 0 )
					elements[ a ].bbtype = 'page';
				else elements[ a ].bbtype = 'image';
				BlestBoxInitClick ( elements[ a ], bb );
			}
		}
	}
	blestBoxData.elementCache = new Array ( );
}

function BlestBoxSetCursor ( ele, type )
{
	var subs = ele.childNodes;
	if ( subs.length )
	{
		for ( var a = 0; a < subs.length; a++ )
			BlestBoxSetCursor ( subs[ a ], type );
	}
	if ( ele.style )
		ele.style.cursor = type;
}

/**
 * Set the click action on an element
**/
function BlestBoxInitClick ( ele, bb )
{
	ele.urlLocation = bb.url;
	ele.style.visibility = "visible";
	ele.onclick = function ( )
	{
		if ( !blestBoxData.busy ) 
		{
			blestBoxData.opacity = 0;
			blestBoxData.targetwidth = getDocumentWidth ( );
			blestBoxData.targetheight = getDocumentHeight ( );
			blestBoxData.image = new Image ( );
			blestBoxData.image.src = this.urlLocation;
			blestBoxData.phase = 0.0;
			blestBoxData.background = false;
			blestBoxData.foreground = false;
			blestBoxData.imagerect = false;
			blestBoxData.element = this;
			blestBoxData.contentloaded = false;
			
			blestBoxData.type = this.bbtype ? this.bbtype : 'page';
			
			document._prevOverflow = Array ( 
				document.body.style.overflow,
				document.body.style.overflowX,
				document.body.style.overflowY
			);
			
			document.body.style.overflow = 'hidden';
			document.body.style.overflowX = 'hidden';
			document.body.style.overflowY = 'hidden';
			
			BlestBoxZoom ( 'background' );
			
			if ( isIE ) hideFormFields ( 'select' );
			
			if ( this.bbtype == 'image' )
			{
				BlestBoxWaitImage ( );
			}
			else
			{
				BlestBoxLoadPage ( );
			}
		}
	}
	BlestBoxSetCursor ( ele, isIE ? 'hand' : 'pointer' );
}

function BlestBoxLaunchElement ( ele )
{
	if ( !blestBoxData.busy && ele ) 
	{
		blestBoxData.opacity = 0;
		blestBoxData.targetwidth = getDocumentWidth ( );
		blestBoxData.targetheight = getDocumentHeight ( );
		blestBoxData.image = new Image ( );
		blestBoxData.image.src = ele.urlLocation;
		blestBoxData.phase = 0.0;
		blestBoxData.background = false;
		blestBoxData.foreground = false;
		blestBoxData.imagerect = false;
		blestBoxData.element = ele;
		blestBoxData.contentloaded = false;
		
		blestBoxData.type = ele.bbtype ? ele.bbtype : 'page';
		
		document._prevOverflow = Array ( 
			document.body.style.overflow,
			document.body.style.overflowX,
			document.body.style.overflowY
		);
		
		document.body.style.overflow = 'hidden';
		document.body.style.overflowX = 'hidden';
		document.body.style.overflowY = 'hidden';
		
		BlestBoxZoom ( 'background' );
		
		if ( ele.bbtype == 'image' )
		{
			BlestBoxWaitImage ( );
		}
		else
		{
			BlestBoxLoadPage ( );
		}
	}
}

/**
 * Set some values
**/
function BlestBoxSet ( key, val )
{
	key = key.toLowerCase ( );
	
	switch ( key )
	{
		case 'graphics-path':
			// must have trailing slash
			if ( val.substr ( val.length - 1, 1 ) != '/' )
				val += '/';
			blestBoxData.graphicspath = val;
			blestBoxData.loadAnim ( );
			break;
		case 'animation-path':
			if ( val.substr ( val.length - 1, 1 ) != '/' )
				val += '/';
			blestBoxData.animationpath = val;
			blestBoxData.loadAnim ( );
			break;
		case 'background-opacity':
			blestBoxData.backgroundopacity = val;
			break;
		case 'background-color':
			blestBoxData.pagebackground = val;
 			break;
 		case 'shadow-image':
 			blestBoxData.shadowimage = val;
 			break;
 		case 'shadowbackground-color':
 			blestBoxData.backgroundcolor = val;
 			break;
 		case 'framebackground-color':
 			blestBoxData.foregroundcolor = val;
 			break;
 		case 'barbackground-color':
 			blestBoxData.closebuttonbackground = val;
 			break;
 		case 'page-width':
 			blestBoxData.pagewidth = val;
 			break;
 		case 'page-height':
 			blestBoxData.pageheight = val;
 			break;
 		case 'background-zoom':
 			blestBoxData.zoombackground = val;
 			break;
 		case 'use-styles':
 			blestBoxData.usestyles = val;
 			break;
 		case 'closepage-text':
 			blestBoxData.closepagetext = val;
			break;
 		case 'closeimage-text':
			blestBoxData.closeimagetext = val;
			break;
		default:
			break;
	}
}

/**
 * Wait for an image to load
**/
function BlestBoxWaitImage ( )
{
	if ( blestBoxData.image.width > 0 )
	{
		blestBoxData.contentloaded = true;
	}
	else 
	{
		setTimeout ( 'BlestBoxWaitImage()', 100 );
	}
}

/**
 * Zoom background or foreground
**/
blestBoxData.s = 0;
function BlestBoxZoom ( element )
{
	blestBoxData.busy = true;
	
	// hide plugins and elements found over bbox
	if ( !blestBoxData.flashhidden )
	{
		var eles = document.body.getElementsByTagName ( 'embed' );
		var obs = document.body.getElementsByTagName ( 'object' );
		for ( var a = 0; a < eles.length; a++ )
			eles[ a ].style.display = 'none';
		for ( var a = 0; a < obs.length; a++ )
			obs[ a ].style.display = 'none';
		var divs = document.body.getElementsByTagName ( 'div' );
		for ( var a = 0; a < divs.length; a++ )
		{
			if ( divs[ a ].style.zIndex >= 10000 )
			{
				divs[ a ].oldzindex = divs[ a ].style.zIndex;
				divs[ a ].style.zIndex = 9999;
			}
		}
		blestBoxData.flashhidden = true;
	}
	
	if ( blestBoxData.background == false )
	{
		blestBoxData.background = document.createElement ( 'div' );
		blestBoxData.background.style.background = blestBoxData.backgroundcolor;
		blestBoxData.background.style.position = 'absolute';
		blestBoxData.background.style.zIndex = 10000;
		blestBoxData.background.className = 'BlestBoxBackground';
		blestBoxData.container.appendChild ( blestBoxData.background );
	}
	if ( blestBoxData.foreground == false )
	{
		blestBoxData.foreground = document.createElement ( 'div' );
		blestBoxData.foreground.style.background = blestBoxData.foregroundcolor;
		blestBoxData.foreground.style.position = 'absolute';
		blestBoxData.foreground.style.zIndex = 10001;
		blestBoxData.foreground.className = 'BlestBoxForeground';
		blestBoxData.container.appendChild ( blestBoxData.foreground );
	}

	var width = 0, height = 0, top = 0, left = 0;
	var iwidth = 0, iheight = 0, itop = 0, ileft = 0;
	
	if ( blestBoxData.phase < 1 )
	{
		blestBoxData.phase += ( 1 + blestBoxData.phase ) / 25;
		if ( blestBoxData.phase > 1 ) blestBoxData.phase = 1;
		blestBoxData.opacity = blestBoxData.phase * blestBoxData.backgroundopacity;
		
		if ( blestBoxData.zoombackground )
		{
			width = blestBoxData.targetwidth * blestBoxData.phase;
			height = blestBoxData.targetheight * Math.pow ( blestBoxData.phase, 2 );
			left = ( blestBoxData.targetwidth / 2 ) - ( width / 2 );
			top = ( blestBoxData.targetheight / 2 ) - ( height / 2 );
		}
		else
		{
			width = blestBoxData.targetwidth;
			height = blestBoxData.targetheight;
			top = 0;
			left = 0;
		}
		
		iwidth = blestBoxData.image.width * Math.pow ( blestBoxData.phase, 2 );
		iheight = blestBoxData.image.height * blestBoxData.phase;
		ileft = ( blestBoxData.targetwidth / 2 ) - ( iwidth / 2 );
		itop = ( blestBoxData.targetheight / 2 ) - ( iheight / 2 );
		
		setTimeout ( 'BlestBoxZoom("' + element + '")', 40 );
	}
	else
	{
		if ( blestBoxData.contentloaded )
		{
			blestBoxData.phase = 0;
			blestBoxData.opacity = blestBoxData.backgroundopacity;
			width = blestBoxData.targetwidth;
			height = blestBoxData.targetheight;
			left = 0;
			top = 0;
			iwidth = blestBoxData.image.width;
			iheight = blestBoxData.image.height;
			ileft = ( blestBoxData.targetwidth / 2 ) - ( iwidth / 2 );
			itop = ( blestBoxData.targetheight / 2 ) - ( iheight / 2 );
			
			if ( element == 'background' )
			{
				setTimeout ( 'BlestBoxZoom("foreground")', 40 );
			}
			else if ( element == 'foreground' )
			{
				if ( blestBoxData.type == 'image' ) setTimeout ( 'BlestBoxShowImage()', 40 );
				else setTimeout ( 'BlestBoxShowPage()', 40 );
			}
		}
		else
		{
			if ( !blestBoxData.animbox )
			{
				blestBoxData.animbox = document.createElement ( 'div' );
				blestBoxData.background.appendChild ( blestBoxData.animbox );
				blestBoxData.animbox.style.position = 'absolute';
				blestBoxData.animbox.style.left = ( ( blestBoxData.targetwidth / 2 ) - 50 ) + 'px';
				blestBoxData.animbox.style.top = ( ( ( blestBoxData.targetheight / 2 ) - 20 ) ) + 'px';
				blestBoxData.animate ( );
			}
			setTimeout ( 'BlestBoxZoom("' + element + '")', 40 );
		}
	}
	
	if ( element == 'background' )
	{
		if ( typeof ( blestBoxData.background ) == 'object' )
		{
			top += getScrollTop ( );
			blestBoxData.background.style.left = Math.floor ( left ) + 'px';
			blestBoxData.background.style.top = Math.floor ( top ) + 'px';
			blestBoxData.background.style.width = Math.floor ( width ) + 'px';
			blestBoxData.background.style.height = Math.floor ( height ) + 'px';
			setOpacity ( blestBoxData.background, blestBoxData.opacity );
		}
	}	
	
	if ( element == 'foreground' )
	{
		if ( typeof ( blestBoxData.foreground ) == 'object' )
		{
			itop += getScrollTop ( );
			blestBoxData.foreground.style.left = Math.floor ( ileft ) + 'px';
			blestBoxData.foreground.style.top = Math.floor ( itop ) + 'px';
			blestBoxData.foreground.style.width = Math.floor ( iwidth ) + 'px';
			blestBoxData.foreground.style.height = Math.floor ( iheight ) + 'px';
		}
	}
}

/**
 * Animate something while we are waiting for content to load
**/
blestBoxData.animate = function ( )
{
	
	if ( typeof ( blestBoxData.animbox ) == 'object' )
	{
		if ( blestBoxData.contentloaded == true ) 
		{
			blestBoxData.background.removeChild ( blestBoxData.animbox );
			blestBoxData.animbox = false;
		}
		else
		{
			if ( isNaN ( blestBoxData.animbox.index ) ) blestBoxData.animbox.index = 0;
			else blestBoxData.animbox.index++;
			if ( blestBoxData.animbox.index >= blestBoxData.animboxframes.length )
				blestBoxData.animbox.index = 0;
			if ( blestBoxData.animbox.childNodes[ 0 ] )
				blestBoxData.animbox.childNodes[ 0 ].src = blestBoxData.animboxframes[ blestBoxData.animbox.index ].src;
			else
				blestBoxData.animbox.innerHTML = "<img src=\"" + blestBoxData.animboxframes[ blestBoxData.animbox.index ].src + "\" />";
			setTimeout ( 'blestBoxData.animate()', 150 );
		}
	}
}

/**
 * Zoom out and clean up!
**/
function BlestBoxZoomOut ( )
{
	blestBoxData.phaseinv;
	
	var width, height, top, left;
	var iwidth, iheight, itop, ileft;
	
	if ( blestBoxData.phase < 1 )
	{
		blestBoxData.phase += ( 1 + blestBoxData.phase ) / 10;
		if ( blestBoxData.phase > 1 ) blestBoxData.phase = 1;
		
		blestBoxData.phaseinv = 1 - blestBoxData.phase;
		
		blestBoxData.opacity = blestBoxData.phaseinv * 0.7;
		
		if ( blestBoxData.zoombackground )
		{
			width = blestBoxData.targetwidth * blestBoxData.phaseinv;
			height = blestBoxData.targetheight * blestBoxData.phaseinv;
			left = blestBoxData.targetwidth / 2 - ( width / 2 );
			top = blestBoxData.targetheight / 2 - ( height / 2 );
		}
		else
		{
			width = blestBoxData.targetwidth;
			height = blestBoxData.targetheight;
			top = 0;
			left = 0;
		}
		
		iwidth = ( blestBoxData.image.width ) * blestBoxData.phaseinv;
		iheight = ( blestBoxData.image.height ) * Math.pow ( blestBoxData.phaseinv, 2 );
		ileft = blestBoxData.targetwidth / 2 - ( iwidth / 2 );
		itop = blestBoxData.targetheight / 2 - ( iheight / 2 );
		
		top += getScrollTop ( );
		itop += getScrollTop ( );
		
		blestBoxData.background.style.left = Math.floor ( left ) + 'px';
		blestBoxData.background.style.top = Math.floor ( top ) + 'px';
		blestBoxData.background.style.width = Math.floor ( width ) + 'px';
		blestBoxData.background.style.height = Math.floor ( height ) + 'px';
		setOpacity ( blestBoxData.background, blestBoxData.opacity );
		
		blestBoxData.foreground.style.left = Math.floor ( ileft ) + 'px';
		blestBoxData.foreground.style.top = Math.floor ( itop ) + 'px';
		blestBoxData.foreground.style.width = Math.floor ( iwidth ) + 'px';
		blestBoxData.foreground.style.height = Math.floor ( iheight ) + 'px';
		
		setTimeout ( 'BlestBoxZoomOut()', 40 );
	}
	else
	{
		if ( isIE ) showFormFields ( 'select' );
		
		blestBoxData.phase = 0;
		
		// Remove all children in blestBoxData.container
		var eles = blestBoxData.container.childNodes;
		if ( eles.length )
		{
			for ( var a = 0; a < eles.length; a++ )
			{
				if ( eles[ a ].id && ( eles[ a ].id.indexOf ( 'BlestBox' ) >= 0 || eles[ a ].className.indexOf ( 'BlestBox' ) >= 0 ) )
				{
					blestBoxData.container.removeChild ( eles[ a ] );
				}
			}
		}
		if ( blestBoxData.background )
			blestBoxData.background.parentNode.removeChild ( blestBoxData.background );
		
		blestBoxData.background = false;
		blestBoxData.foreground = false;
		blestBoxData.contentloaded = false;
		
		document.body.style.overflow = document._prevOverflow[ 0 ];
		document.body.style.overflowX = document._prevOverflow[ 1 ];
		document.body.style.overflowY = document._prevOverflow[ 2 ];
		
		blestBoxData.busy = false;
		
		// show plugins and divs
		if ( blestBoxData.flashhidden )
		{
			var eles = document.body.getElementsByTagName ( 'embed' );
			var obs = document.body.getElementsByTagName ( 'object' );
			for ( var a = 0; a < eles.length; a++ )
				eles[ a ].style.display = '';
			for ( var a = 0; a < obs.length; a++ )
				obs[ a ].style.display = '';
			blestBoxData.flashhidden = false;
			var divs = document.body.getElementsByTagName ( 'div' );
			for ( var a = 0; a < divs.length; a++ )
			{
				if ( divs[ a ].oldzindex )
				{
					divs[ a ].style.zIndex = divs[ a ].oldzindex
				}
			}
		}
	}
}

/**
 * Fade in the image
**/
function BlestBoxShowImage ( )
{
	if ( !blestBoxData.imagerect )
	{
		blestBoxData.imagerect = document.createElement ( 'div' );
		blestBoxData.imagerect.style.position = 'relative';
		blestBoxData.imagerect.style.zIndex = 10002;
		blestBoxData.imagerect.opacity = 0;
		blestBoxData.imagerect.style.background = 'url(' + blestBoxData.image.src + ') 0px 0px no-repeat blue';
		blestBoxData.imagerect.style.top = '0px';
		blestBoxData.imagerect.style.left = '0px';
		blestBoxData.imagerect.style.width = parseInt ( blestBoxData.image.width ) + 'px';
		blestBoxData.imagerect.style.height = parseInt ( blestBoxData.image.height ) + 'px';
		blestBoxData.foreground.appendChild ( blestBoxData.imagerect );
	}
	if ( blestBoxData.imagerect.opacity < 1 )
	{
		blestBoxData.imagerect.opacity += ( blestBoxData.imagerect.opacity + 1 ) / 8;
		setTimeout ( 'BlestBoxShowImage ( )', 40 );
	}
	else
	{
		blestBoxData.ShowCloseButton ( );
		blestBoxData.imagerect.opacity = 1;
		blestBoxData.imagerect.onclick = function ( )
		{
			blestBoxData.fadeOutClosebutton ( 'BlestBoxCloseImage ( )' );
		}
	}
	blestBoxData.imagerect.style.opacity = blestBoxData.imagerect.opacity;
}

/**
 * Fade out the image
 * and then close the whole thing
**/
function BlestBoxCloseImage ( )
{
	if ( blestBoxData.imagerect.opacity > 0 )
	{
		var op = blestBoxData.imagerect.opacity / 10;
		if ( op < 0.05 )
			op = 0.05;
		blestBoxData.imagerect.opacity -= op;
		setTimeout ( 'BlestBoxCloseImage()', 40 );
	}
	else 
	{
		blestBoxData.imagerect.opacity = 0;
		try
		{
			blestBoxData.foreground.removeChild ( blestBoxData.imagerect );
		} catch ( e ){};
		blestBoxData.imagerect = false;
		setTimeout ( 'BlestBoxZoomOut()', 40 );
	}
	if ( blestBoxData.imagerect )
		blestBoxData.imagerect.style.opacity = blestBoxData.imagerect.opacity;
}

function BlestBoxLoadPage ( )
{
	blestBoxData.image = new Object ( );
	blestBoxData.image.width = blestBoxData.pagewidth;
	blestBoxData.image.height = blestBoxData.pageheight;
	blestBoxData.scripts = new Array ( );
	var pagejax = new bajax ( );
	pagejax.openUrl ( blestBoxData.element.urlLocation, 'get', true );
	pagejax.onload = function ( )
	{
		blestBoxData.pageelement = document.createElement ( 'div' );
		blestBoxData.pageelement.style.width = blestBoxData.pagewidth + 'px';
		blestBoxData.pageelement.style.height = blestBoxData.pageheight + 'px';
		blestBoxData.pageelement.style.overflowX = 'hidden';
		blestBoxData.pageelement.style.overflowY = 'auto';
		blestBoxData.pageelement.style.position = 'relative';
		blestBoxData.pageelement.style.backgroundColor = blestBoxData.pagebackground;
		
		var html = this.getResponseText ( );
		if ( html.indexOf ( '<body' ) > 0 )
		{
			var e = new RegExp ( /\<body.*?\>/ );
			html = html.split ( e );
			html = html[ 1 ].split ( '</body>' );
		}
		else html = Array ( html );
		
		var chtml = html[ 0 ] + '';
		
		scripts = extractScripts ( chtml );
		chtml = removeScripts ( chtml );
		
		blestBoxData.pageelement.innerHTML = '<div id="BlestBoxContent">' + chtml + '</div>';
		
		// queue page scripts
		for ( var a = 0; a < scripts.length; a++ )
			blestBoxData.scripts[ blestBoxData.scripts.length ] = scripts[ a ];
		
		// Tell everyone that we're loaded!
		blestBoxData.contentloaded = true;
	}
	pagejax.send ( );
}

/**
 * Show a close button under a page or image view
**/
blestBoxData.ShowCloseButton = function ( )
{
	if ( !this.closebutton )
	{
		this.closebutton = document.createElement ( 'div' );
		this.closebutton.child = document.createElement ( 'div' );
		this.closebutton.style.position = 'absolute';
		this.closebutton.style.left = Math.round ( ( ( this.targetwidth / 2 ) - ( this.image.width / 2 ) ) ) + 'px';
		this.closebutton.style.width = getElementWidth ( this.foreground ) + 'px';
		this.closebutton.style.top = Math.round ( ( ( this.targetheight / 2 ) - ( this.image.height / 2 ) ) - 25 + getScrollTop ( ) ) + 'px';
		this.closebutton.style.zIndex = 10000;
		if ( this.usestyles )
		{
			this.closebutton.style.height = '25px';
			this.closebutton.style.background = this.closebuttonbackground;
			this.closebutton.child.style.position = 'absolute';
			this.closebutton.child.style.top = '0px';
			this.closebutton.child.style.right = '0px';
			this.closebutton.child.style.width = '128px';
			this.closebutton.child.style.cursor = isIE ? 'hand' : 'pointer';
		}
		if ( this.type == 'image' )
		{
			this.closebutton.child.innerHTML = '<a href="javascript: blestBoxData.fadeOutClosebutton ( \'BlestBoxCloseImage ( )\' )">' + trans ( this.closeimagetext ) + '</a>';
			this.closebutton.child.onclick = function ( ){ blestBoxData.fadeOutClosebutton ( 'BlestBoxCloseImage ( )' ); };
			this.closebutton.child.className = 'bbclosebutton_image';
		}
		else
		{
			this.closebutton.child.innerHTML = '<a href="javascript: blestBoxData.fadeOutClosebutton ( \'BlestBoxHidePage ( )\' )">' + trans ( this.closepagetext ) + '</a>';
			this.closebutton.child.onclick = function ( ){ blestBoxData.fadeOutClosebutton ( 'BlestBoxHidePage ( )' ); };
			this.closebutton.child.className = 'bbclosebutton_page';
		}
		this.closebutton.appendChild ( this.closebutton.child );
		this.container.appendChild ( this.closebutton );
		// Show background
		this.background.style.backgroundImage = 'url(' + this.shadowimage + ')';
		this.background.style.backgroundPosition = 'center center';
		this.background.style.backgroundRepeat = 'no-repeat';
	}
	if ( !isIE )
		setOpacity ( this.closebutton, 0 );
	this.closebutton.bbop = 0;
	this.closebutton.top = getElementTop ( this.closebutton );
	this.fadeInClosebutton ( );
}

blestBoxData.fadeInClosebutton = function ( )
{
	if ( this.closingbutton ) return;
	var d = ( this.closebutton.bbop - 100 ) / 5;
	if ( Math.abs ( d ) < 0.25 )
	{
		this.closebutton.bbop = 100;
	}
	else
	{
		this.closebutton.bbop -= d;
		setTimeout ( 'blestBoxData.fadeInClosebutton ( )', 40 );
	}
	if ( this.closebutton )
	{
		this.closebutton.style.top = Math.round ( this.closebutton.top + ( 5 * ( 1 - ( this.closebutton.bbop / 100 ) ) ) ) + 'px';
		if ( !isIE ) setOpacity ( this.closebutton, this.closebutton.bbop / 100 );
	}
}

blestBoxData.fadeOutClosebutton = function ( endfunc )
{
	if ( !this.closebutton ) return;
	if ( !isIE )
	{
		this.closingbutton = true;
		var d = this.closebutton.bbop / 5;
		if ( d < 0.36 )
		{
			this.closingbutton = false;
			setTimeout ( endfunc, 5 );
			this.RemoveCloseButton ( );
		}
		else
		{
			if ( d < 0.3 ) d = 0.3;
			this.closebutton.bbop -= d;
			this.closebutton.style.top = Math.round ( this.closebutton.top + ( 5 * ( 1 - ( this.closebutton.bbop / 100 ) ) ) ) + 'px';
			if ( !isIE ) setOpacity ( this.closebutton, this.closebutton.bbop / 100 );
			setTimeout ( 'blestBoxData.fadeOutClosebutton ( \'' + endfunc + '\' )', 50 );
		}
	}
	else
	{
		setTimeout ( endfunc, 5 );
		this.RemoveCloseButton ( );
	}
}

/**
 * Remove the close button
**/
blestBoxData.RemoveCloseButton = function ( )
{
	this.container.removeChild ( this.closebutton );
	this.closebutton = false;
}

/**
 * Show a page view
**/
function BlestBoxShowPage ( )
{
	blestBoxData.foreground.style.overflow = 'auto';
	blestBoxData.foreground.appendChild ( blestBoxData.pageelement );
	for ( var a = 0; a < blestBoxData.scripts.length; a++ )
		eval ( blestBoxData.scripts[ a ] );
	blestBoxData.ShowCloseButton ( );
}

/** 
 * Hide a page
**/
function BlestBoxHidePage ( )
{
	blestBoxData.foreground.style.overflow = 'hidden';
	blestBoxData.foreground.removeChild ( blestBoxData.pageelement );
	blestBoxData.pageelement = 0;
	BlestBoxZoomOut ( );
}

/**
 * Init the blestbox dingsen
**/
blestBoxData.prepare ();
blestBoxData.init = function ( )
{
	if ( typeof ( addOnload ) == 'undefined' )
	{
		setTimeout ( 'blestBoxData.init()', 5 );
	}
	else
	{
		addOnload ( function ( ){ BlestBox ( document.getElementById ( 'Empty__' ) ); } );
	}
}
blestBoxData.init();
