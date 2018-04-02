
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

var sliceZoom = 1;
var sliceOffX = 0;
var sliceOffY = 0;
var sliceClickX = 0;
var sliceClickY = 0;

// select rect coords
var srectx1, srectx2, srecty1, srecty2, srectwd, srecthg, sclickx, sclicky;
var smousex, smousey;

var sliceMouseDown = false;
var sliceShiftDown = false;

function initializeImageSlice ( iid )
{
	initModalDialogue ( 'slice', 800, 512, 
		'admin.php?module=library&function=imageslice&iid='+iid, setupSliceUI );
}

function resizeSliceUI ()
{
	var ui = ge ( 'SliceUI' );
	var im = ge ( 'SliceImage' );
	var tb = ge ( 'SliceToolbar' );
	
	ui.style.width = '800px';
	ui.style.height = '512px';
	
	ui.style.margin = '-8px';
	ui.style.height = ( ui.parentNode.offsetHeight - 2 ) + 'px';
	ui.style.top = '0px';
	ui.style.left = '0px';
	
	tb.style.width = '120px';
	tb.style.height = ui.style.height;
	tb.style.right = '0px';
	tb.style.top = '0px';
	
	im.style.top = '0px';
	im.style.left = '0px';
	
	resizeSlideRect ();
}

function zoomSlice ( m )
{
	if ( m == 'in' ) sliceZoom *= 1.5;
	else sliceZoom /= 1.5;
	
	var im = ge ( 'SliceImage' ).getElementsByTagName ( 'img' )[0];
	if ( !im.owidth ) im.owidth = im.offsetWidth;
	if ( !im.oheight ) im.oheight = im.offsetHeight;
	im.width = im.owidth * sliceZoom;
	im.height = im.oheight * sliceZoom;
	
	resizeSlideRect ();
}

function sliceHideSelection ()
{
	var elements = [ 'TL', 'TM', 'TR', 'ML', 'MM', 'MR', 'BL', 'BM', 'BR' ];
	for ( var a = 0; a < elements.length; a++ )
	{
		ge ( 'Slice' + elements[a] ).style.visibility = 'hidden';
	}
	elements = [
		ge ( 'SliceTop' ),
		ge ( 'SliceLeft' ),
		ge ( 'SliceRight' ),
		ge ( 'SliceBottom' ),
		ge ( 'SliceRect' )
	];
	for ( var a = 0; a < elements.length; a++ )
	{
		elements[a].style.display = 'none';
	}
}
function setupSliceUI ()
{
	var ui = ge ( 'SliceUI' );
	var im = ge ( 'SliceImage' );
	var tb = ge ( 'SliceToolbar' );
	var rk = ge ( 'SliceRect' );
	var st = ge ( 'SliceTop' );
	var sl = ge ( 'SliceLeft' );
	var sr = ge ( 'SliceRight' );
	var sb = ge ( 'SliceBottom' );
	
	// Reset zoom etc
	sliceZoom = 1;
	sliceOffX = 0;
	sliceOffY = 0;
	
	// Main positioning / cfg
	ui.style.position = 'relative';
	ui.style.display = 'block';
	ui.style.overflow = 'hidden';
	ui.style.background = 'url(admin/gfx/checkers.png)';
	tb.style.position = 'absolute';
	tb.style.border = '1px solid #c8c8c8';
	tb.style.background = '#c8c8c8';
	tb.style.zIndex = 40;
	im.style.position = 'absolute';
	im.style.zIndex = 10;
	
	var elements = [ 'TL', 'TM', 'TR', 'ML', 'MM', 'MR', 'BL', 'BM', 'BR' ];
	for ( var a = 0; a < elements.length; a++ )
	{
		var e = ge ( 'Slice' + elements[a] );
		e.style.position = 'absolute';
		e.style.visibility = 'hidden';
		e.style.border = '1px solid #fff';
		e.style.width = '8px';
		e.style.height = '8px';
		e.style.background = 'rgb(128,140,160)';
		e.style.opacity = 0.6;
		e.style.zIndex = 30;
	}
	im.onmousedown = function ()
	{
		sliceHideSelection ();
		sliceMouseDown = true;
		sliceClickX = mousex;
		sliceClickY = mousey;
		return false;
	}
	
	rk.onmousedown = im.onmousedown;
	st.onmousedown = im.onmousedown;
	sl.onmousedown = im.onmousedown;
	sr.onmousedown = im.onmousedown;
	sb.onmousedown = im.onmousedown;
	
	im.onclick = function ( ) { return false; }
	addEvent ( 'onkeydown', function ( e )
	{
		if ( e.which == 16 )
			sliceShiftDown = true;
	});
	addEvent ( 'onkeyup', function ( e )
	{
		sliceShiftDown = false;
	} );
	addEvent ( 'onmouseup', function () 
	{ 
		sliceOffX = parseInt ( im.style.left );
		sliceOffY = parseInt ( im.style.top );
		sliceMouseDown = false; 
	} );
	addEvent ( 'onmousemove', function ( e ) 
	{
		// Scroll image with shift down
		if ( sliceMouseDown && sliceShiftDown )
		{
			var i = im.getElementsByTagName ( 'img')[0];
			var eh = ui.offsetHeight;
			var ew = ui.offsetWidth - tb.offsetWidth;
			var diffx = mousex - sliceClickX + sliceOffX;
			var diffy = mousey - sliceClickY + sliceOffY;
			if ( diffy > 0 ) diffy = 0;
			else if ( i.height < eh )
				diffy = 0;
			else if ( diffy < 0 - ( i.height - eh ) )
				diffy = 0 - ( i.height - eh );
			if ( diffx > 0 ) diffx = 0;
			else if ( i.width < ew )
				diffx = 0;
			else if ( diffx < 0 - ( i.width - ew ) )
				diffx = 0 - ( i.width - ew );
			im.style.left = diffx + 'px';
			im.style.top = diffy + 'px';
			
			// Shade out around rect
			resizeSlideRect ();
		}
		// Selections on image without shift down
		else if ( sliceMouseDown )
		{
			// Coords / vars
			var offx = -getElementLeft ( ui ) * sliceZoom; // offset of ui element on page
			var offy = -getElementTop ( ui ) * sliceZoom;
			var clx = ( sliceClickX * sliceZoom ) + ( offx ); // coords clicked
			var cly = ( sliceClickY * sliceZoom ) + ( offy );
			var mpx = ( mousex * sliceZoom ) + ( offx ); // coords now
			var mpy = ( mousey * sliceZoom ) + ( offy );
			var mnx = Math.min ( clx, mpx );
			var mxx = Math.max ( clx, mpx );
			var mny = Math.min ( cly, mpy );
			var mxy = Math.max ( cly, mpy );
			var mw = mxx-mnx;
			var mh = mxy-mny;
			
			// Store coords
			srectx1 = mnx;
			srectx2 = mxx;
			srecty1 = mny;
			srecty2 = mxy;
			srectwd = mw;
			srecthg = mh;
			sclickx = clx;
			sclicky = cly;
			smousex = mpx;
			smousey = mpy;
			
			// Shade out around rect
			resizeSlideRect ();
		}
	} );
	
	// Resize
	resizeSliceUI ();
}

function resizeSlideRect ()
{
	var soffx = ge ( 'SliceImage' ).offsetLeft;
	var soffy = ge ( 'SliceImage' ).offsetTop;
	
	// Store coords
	var mnx = srectx1 + soffx;
	var mxx = srectx2 + soffx;
	var mny = srecty1 + soffy;
	var mxy = srecty2 + soffy;
	var mw 	= srectwd;
	var mh 	= srecthg;
	var clx = sclickx + soffx;
	var cly = sclicky + soffy;
	var mpx = smousex + soffx;
	var mpy = smousey + soffy;
	
	var ui = ge ( 'SliceUI' );
	var im = ge ( 'SliceImage' );
	var tb = ge ( 'SliceToolbar' );
	var rk = ge ( 'SliceRect' );
	if ( !rk ) return;
	var srk = ge ( 'SliceRect' );
	var st = ge ( 'SliceTop' );
	var sl = ge ( 'SliceLeft' );
	var sr = ge ( 'SliceRight' );
	var sb = ge ( 'SliceBottom' );
	
	// Shade
	st.style.display = ''; sb.style.display = '';
	sl.style.display = ''; sr.style.display = '';
	st.style.visibility = 'visible'; st.style.top = '0px'; st.style.left = '0px';
	st.style.width = ui.offsetWidth + 'px'; st.style.height = Math.floor ( mny ) + 'px';
	sb.style.visibility = 'visible'; sb.style.top = Math.floor ( mxy ) + 'px'; st.style.left = '0px';
	sb.style.width = ui.offsetWidth + 'px'; sb.style.height = Math.floor ( (ui.offsetHeight-mxy) ) + 'px';
	sl.style.visibility = 'visible'; sl.style.top = Math.floor ( mny ) + 'px'; sl.style.left = '0px';
	sl.style.width = Math.floor ( mnx ) + 'px'; sl.style.height = Math.floor ( mh ) + 'px';
	sr.style.visibility = 'visible'; sr.style.top = Math.floor ( mny ) + 'px'; sr.style.left = Math.floor ( mxx ) + 'px';
	sr.style.width = Math.floor ( (ui.offsetWidth - mxx) ) + 'px'; sr.style.height = Math.floor ( mh ) + 'px';
	srk.style.display = '';
	srk.style.top = Math.floor (mny) + 'px';
	srk.style.left = Math.floor (mnx) + 'px';
	srk.style.width = Math.floor (mw-2) + 'px';
	srk.style.height = Math.floor (mh-2) + 'px';
	
	// Position corners of rect
	var elements = [ 'TL', 'TM', 'TR', 'ML', 'MM', 'MR', 'BL', 'BM', 'BR' ];
	for ( var a = 0; a < elements.length; a++ )
	{
		var el = ge ( 'Slice' + elements[a] );
		el.style.visibility = 'visible';
		switch ( elements[a].substr ( 0, 1 ) )
		{
			case 'T':
				el.style.top = Math.floor (cly-4) + 'px';
				break;
			case 'M':
				el.style.top = Math.floor (mny+Math.round(mh*0.5)-4)+'px';
				break;
			case 'B':
				el.style.top = Math.floor (mpy-4) + 'px';
				break;
		}
		switch ( elements[a].substr ( 1, 1 ) )
		{
			case 'L':
				el.style.left = Math.floor (clx-4) + 'px';
				break;
			case 'M':
				el.style.left = Math.floor (mnx+Math.round(mw*0.5)-4)+'px';
				break;
			case 'R':
				el.style.left = Math.floor (mpx-4) + 'px';
				break;
		}
	}
}
