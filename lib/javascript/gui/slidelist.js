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
 * Makes a sliding list with clickable headers
 * Requires HTML to conform
 *
 * Example:
 *
 * <div id="MySlidelist">
 *   <div class="ClickGroup">
 *     <div class="ClickHeader"><p>Click me 1</p></div>
 *     <div class="ClickContent"><p>Info for first group</div>
 *   </div>
 *   <div class="ClickGroup">
 *     <div class="ClickHeader"><p>Click me 2</p></div>
 *     <div class="ClickContent"><p>Info for second group</div>
 *   </div>
 * </div>
 * <script type="text/javascript"> 
 *   var s = new GuiSlidelist ( document.getElementById ( 'MySlideList' ) );
 * </script>
**/

GuiSlidelist = function ( domobject, pobj )
{
	this.DomObject = domobject;
	this.parentObject = pobj ? pobj : this.DomObject.parentNode;
	this.Refresh ();	
	
}
GuiSlidelist.prototype = GuiObject.prototype;
GuiSlidelist.prototype.Refresh = function ()
{
	// Check that we are visible
	var par = this.DomObject.parentNode;
	do
	{
		if ( par.offsetHeight <= 0 )
			return;
	}
	while ( par = par.parentNode );
	
	// Prepare a list for clickgroups
	this.groups = new Array ();
	
	// Collect all clickgroups
	var divs = this.DomObject.getElementsByTagName ( 'div' );
	for ( var a = 0; a < divs.length; a++ )
	{
		if ( divs[a].className.indexOf ( 'ClickGroup' ) == 0 )
		{
			var group = new Object ();
			var subs = divs[a].getElementsByTagName ( 'div' );
			for ( var b = 0; b < subs.length; b++ )
			{
				if ( subs[b].className.indexOf ( 'Header' ) > 0 )
					group.Header = subs[b];
				else if ( subs[b].className.indexOf ( 'Content' ) > 0 )
					group.Content = subs[b];
			}
			if ( !group.Header || !group.Content ) continue;
			group.Container = divs[a];
			group.ContentHeight = group.Container.offsetHeight - group.Header.offsetHeight + 2;
			
			group.Groups = this.groups;
			group.Container.object = group;
			group.Header.object = group;
			group.Content.object = group;
			group.hide = function ()
			{
				this.Content.style.position = 'relative';
				this.Content.style.overflow = 'hidden';
				this.Content.style.height = '1px';
				this.Content.className = 'ClickContent';
				this.Header.className = 'ClickHeader';
			}
			group.show = function ()
			{
				this.Content.style.webkitTransition = "all 0.2s";
				this.Content.style.height = this.ContentHeight + 'px';
				this.Content.className = 'ClickContentActive';
				this.Header.className = 'ClickHeaderActive';
			}
			group.hide();
			this.groups.push ( group );
		}
	}
	
	// Set clickgroups
	for ( var a = 0; a < this.groups.length; a++ )
	{
		this.groups[a].Header.onclick = function ()
		{
			for ( var c = 0; c < this.object.Groups.length; c++ )
			{
				if ( this.object.Groups[c] == this.object ) continue;
				this.object.Groups[c].hide();
			}
			this.object.show ();
		}
	}
}

