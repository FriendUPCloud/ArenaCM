

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
* Autocomplete box                                                             *
*                                                                              *
*******************************************************************************/

var _dropdowns = new Array ( );

var AutoComplete = function ( domobj, url )
{
	this.Url = url;
	if ( typeof ( domobj ) != 'object' )
		this.DomObjectId = domobj;
	else this.DomObjectId = domobj.id;
	this.Index = _dropdowns.length; _dropdowns.push ( this );
	this.InitTries = 20;
	this.QueuedSuggestion = '';
	this.Suggesting = false;
	this.PrevValue = '';
	this.Pos = -1;
	this.Initialize ( );
}

AutoComplete.prototype = GuiObject.prototype;

AutoComplete.prototype.Initialize = function ( )
{
	var domo = document.getElementById ( this.DomObjectId );
	if ( typeof ( domo ) == 'object' )
	{
		this.Init ( 'AutoComplete', domo, false );
		this.DivPopup = document.createElement ( 'div' );
		this.DivPopup.className = 'AutoCompletionBox';
		this.DivPopup.style.visibility = 'hidden';
		this.DivPopup.style.position = 'absolute';
		this.DivPopup.style.top = ( getElementTop ( this.DomObject ) + getElementHeight ( this.DomObject ) ) + 'px';
		this.DivPopup.style.left = getElementLeft ( this.DomObject ) + 'px';
		this.DomObject.compl = this;
		this.Visible = false;
		this.DomObject.onkeyup = function ( e )
		{
			if ( !e ) e = window.event;
			this.compl.SuggestCompletion ( this.value, e.keyCode );
		}
		var cn = document.getElementById ( 'Content__' );
		cn.insertBefore ( this.DivPopup, cn.firstChild );
	}
	else
	{
		this.InitTries--;
		// bail out!
		if ( this.InitTries < 1 )
			return;
		// retry
		setTimeout ( '_dropdowns[' + this.Index + '].Init()', 50 );
	}
}

AutoComplete.prototype.SuggestCompletion = function ( val, code )
{
	if ( !val ) 
	{
		this.HidePopup ( );
		return;
	}
	
	// Don't try several times at the same time
	if ( this.Suggesting ) 
	{
		// Queue the last suggestion
		this.QueuedSuggestion = val;
		return;
	}
	// Positioning
	if ( this.PrevValue != val )
		this.Pos = -1;
	
	if ( code == 40 )
		this.Pos++;
	else if ( code == 38 )
		this.Pos--;
	if ( this.Pos < -1 ) this.Pos = -1;
	this.Suggesting = true;
	
	// Don't reload everything if we aren't getting new stuff
	if ( this.PrevValue == val && this.Options )
	{
		for ( var i = 0; i < this.Options.length; i++ )
		{
			if ( i == this.Pos ) this.Options[ i ].selected = 'yes';
			else this.Options[ i ].selected = 'no';
		}
		if ( code == 13 )
		{
			for ( var i = 0; i < this.Options.length; i++ )
			{
				if ( i == this.Pos )
					return this.ActivateOption ( i );
			}
		}
		this.ShowPopup ( );
		this.Suggesting = false;
		return;
	}
	
	this.PrevValue = val;
	var bref = document.getElementsByTagName ( 'base' )[0].href;
	var url = bref + this.Url;
	var compljax = new bajax ( );
	compljax.addVar ( 'value', val );
	compljax.addVar ( 'position', this.Pos );
	compljax.openUrl ( url, 'post', true );
	compljax.object = this;
	compljax.code = code;
	compljax.onload = function ( )
	{
		this.object.SetOptionsByXML ( this.getResponseXML ( ), this.code );
		this.object.ShowPopup ( );
		this.object.Suggesting = false;
		// If we have a queued suggestion, perform it
		var s;
		if ( s = this.object.QueuedSuggestion )
		{
			this.object.QueuedSuggestion = '';
			this.object.SuggestCompletion ( s );
		}
	}
	compljax.send ( );
}

AutoComplete.prototype.SetOptionsByXML = function ( xml, code )
{
	if ( !code ) code = false;
	if ( !xml ) return;
	stripEmptyTextNodes ( xml );
	this.Options = new Array ( );
	for ( var a = 0; a < xml.firstChild.childNodes.length; a++ )
	{
		var nA = xml.firstChild.childNodes[ a ];
		if ( nA.nodeName != 'element' ) continue;
		if ( !nA.attributes.length ) continue;
		if ( nA.getAttribute ( 'type' ) == 'option' )
		{
			var opt = new Object ( );
			for ( var z = 0; z < nA.attributes.length; z++ )
				if ( nA.attributes[ z ].specified )
					opt[ nA.attributes[ z ].nodeName ] = nA.attributes[ z ].nodeValue;
			if ( opt.selected == 'yes' && code == 13 )
			{
				this.DomObject.value = opt.value;
				this.ActiveOption = opt;
				this.Options = false;
				this.HidePopup ();
				if ( this._completionFunc )
					this._completionFunc ( );
				return;
			}
			this.Options.push ( opt );
		}
	}
	if ( !this.Options.length ) { this.Options = false; this.HidePopup (); }
	if ( this.Pos >= this.Options.length ) this.Pos = this.Options.length - 1;
}

AutoComplete.prototype.ActivateOption = function ( index )
{
	if ( this.Options[ index ] )
	{
		this.DomObject.value = this.Options[ index ].value;
		this.ActiveOption = this.Options[ index ];
		this.Options = false;
		this.HidePopup ();
		if ( this._completionFunc )
			this._completionFunc ( );
		this.Suggesting = false;
	}
}

AutoComplete.prototype.ShowPopup = function ( )
{
	if ( this.Options )
	{
		var html = '<div class="Content">';
		for ( var a = 0; a < this.Options.length; a++ )
		{
			html += '<div class="Option ' + ( ( this.Options[ a ].selected == 'yes' ) ? 'selected' : '' ) + '" onclick="_dropdowns[' + this.Index + '].ActivateOption ( ' + a + ' )">' + this.Options[ a ].value + '</div>';
		}
		html += '</div>';
		this.DivPopup.innerHTML = html;
	}
	if ( !this.Visible && this.Options )
	{
		this.DivPopup.style.visibility = 'visible';
		this.Visible = true;
	}
}

AutoComplete.prototype.HidePopup = function ( )
{
	if ( this.Visible )
	{
		this.Visible = false;
		this.DivPopup.style.visibility = 'hidden';
	}
}

AutoComplete.prototype.SetCompletionFunction = function ( func )
{
	if ( func ) this._completionFunc = func;
}


