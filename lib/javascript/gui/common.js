
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


// Simple colorbox implementation
function GuiColorBox ( valuefield )
{
	if ( !valuefield ) return;
	if ( valuefield.inited ) return;
	valuefield.inited = true;
	var d = document.createElement ( 'div' );
	d.className = 'ColorBoxForm';
	d.style.position = 'relative';
	d.style.height = getElementHeight ( valuefield ) + 'px';
	var p = document.createElement ( 'div' );
	p.className = 'ColorBoxPreview';
	p.style.border = '1px solid #cccccc';
	p.style.borderRadius = '3px';
	p.style.width = '20%';
	p.style.position = 'absolute';
	p.style.right = '0px';
	p.style.top = '0px';
	p.style.height = getElementHeight ( valuefield ) - 2 + 'px';
	valuefield.style.width = '75%';
	valuefield.style.position = 'absolute';
	valuefield.style.left = '0px';
	valuefield.style.top = '0px';
	if ( valuefield.parentNode.replaceChild )
		valuefield.parentNode.replaceChild ( d, valuefield );
	else
	{
		var p = valuefield.parentNode;
		p.insertBefore ( d, valuefield );
		p.removeChild ( valuefield );
	}
	d.appendChild ( valuefield );
	d.appendChild ( p );
	valuefield.preview = p;
	valuefield.onchange = function ()
	{
		try
		{
			this.preview.style.backgroundColor = this.value;
		}
		catch ( e ) { }
	}
	valuefield.onblur = function ()
	{ this.onchange (); }
	valuefield.onkeyup = function ()
	{ this.onchange (); }
	valuefield.onchange();
}
	
// Simple size widget 
function GuiSizeWidget ( valuefield )
{
	if ( !valuefield ) return;
	if ( valuefield.inited ) return;
	valuefield.inited = true;
	var d = document.createElement ( 'div' );
	d.className = 'SizeBoxForm';
	d.style.position = 'relative';
	d.style.height = getElementHeight ( valuefield ) + 'px';
	var up = document.createElement ( 'div' );
	var down = document.createElement ( 'div' );
	var ar = [ up, down ];
	for ( var a = 0; a < 2; a++ )
	{
		var u = ar[a];
		u.className = 'ColorBoxPreview';
		u.style.border = '1px solid #cccccc';
		u.style.background = '#c0c0c0';
		u.style.borderRadius = '3px';
		u.style.width = '16px'; u.style.height = Math.floor ( ( getElementHeight ( valuefield ) - 2 ) * 0.5 ) + 'px';
		u.style.position = 'absolute';
		u.style.left = getElementWidth ( valuefield ) - 18 + 'px';
		u.style.top = a == 0 ? '0px' : u.style.height;
		u.innerHTML = '<img src="admin/gfx/icons/bullet_arrow_' + ( a == 0 ? "up" : "down" ) + '.png">';
	}
	valuefield.style.width = getElementWidth ( valuefield ) - 20 + 'px';
	if ( valuefield.parentNode.replaceChild )
		valuefield.parentNode.replaceChild ( d, valuefield );
	else
	{
		var p = valuefield.parentNode;
		p.insertBefore ( d, valuefield );
		p.removeChild ( valuefield );
	}
	d.appendChild ( valuefield );
	d.appendChild ( up ); d.appendChild ( down );
	up.vf = valuefield;
	down.vf = valuefield;
	up.onclick = function ()
	{
		var sign = 'px';
		if ( isNaN ( parseInt ( this.vf.value ) ) )
			this.vf.value = '0';
		if ( this.vf.value.indexOf ( '%' ) > 0 )
			sign = '%';
		this.vf.value = ( parseInt ( this.vf.value ) + 1 ) + sign;
	}
	down.onclick = function ()
	{
		var sign = 'px';
		if ( isNaN ( parseInt ( this.vf.value ) ) )
			this.vf.value = '0';
		if ( this.vf.value.indexOf ( '%' ) > 0 )
			sign = '%';
		this.vf.value = ( parseInt ( this.vf.value ) - 1 ) + sign;
	}
	up.style.cursor = isIE ? 'hand' : 'pointer';
	down.style.cursor = isIE ? 'hand' : 'pointer';
}

