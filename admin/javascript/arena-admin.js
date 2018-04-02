

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



var ArenaMode = 'admin';

var makePassword = function ( )
{
		var keylist="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ123456789";
		var password='';
		var len = Math.floor( Math.random() * 4 ) + 5;
		for ( i=0; i < len; i++ )
			password += keylist.charAt( Math.floor( Math.random() * keylist.length ) );
		return password;
}

//// POPUP ////

var arenaPopup = function () 
{
	this.halign = "center";
	this.valign = "middle";
	
	if ( !( this.container = document.getElementById ( "popupcontainer" ) ) )
	{
		this.container = document.createElement ( "DIV" );
		this.container.id = "popupcontainer";
		document.body.appendChild ( this.container );
	}

	// METHODS //
	
	this.set = function ( str )
	{
		this.container.innerHTML = str;
		
		// Apply collapsable behaviour
		var lists = this.container.getElementsByTagName ( "ul" );
		for ( i = 0; i < lists.length; i++ )
		{
			if ( elementHasClass ( lists[ i ], "collapsable" ) )
				makeCollapsable ( lists[ i ] );				
		}
	}
	
	this.show = function ()
	{
		positionElement ( this.container, this.halign, this.valign );
		this.container.style.visibility = "visible";
	}
	
	this.hide = function ()
	{
		this.container.style.visibility = "hidden";
	}
	
	this.destroy = function ()
	{
		this.hide ();
		this.container.innerHTML = "";
	}
	
	this.showFeedback = function ( str )
	{
		this.container.innerHTML = '<div class="feedback">' + str + '</div>';
		this.show ();
	}

	this.showLoading = function ( str )
	{
		this.showFeedback ( '<img src="admin/gfx/icons/disk.png" /> ' + str );
	}
	
	this.getByAjax = function ( url )
	{
		var ajax = new bajax ();
		ajax.popup = this;
		ajax.onload = function ()
		{
			this.popup.destroy ();
			this.popup.set ( this.getResponseText () );
			this.popup.show ();
			if ( this.popup.onload )
				this.popup.onload();
		}
		ajax.openUrl ( url, "get", true );
		ajax.send ();
	}
}

// Make fake select options (divs in a container div)
function activateDivSelectors ( )
{
	var list1 = getElementsByClassName ( "divselector" );
	var list2 = getElementsByClassName ( "divselector_selected" );
	for ( var a = 0; a < list1.length; a++ )
	{
		if ( 
			typeof ( list1[ a ].innerHTML ) == "string" 
		) 
		{
			if ( !list1[ a ].isSelector )
			{
				var varName = list1[ a ].title + "";
				if ( varName.substr( 0, 8 ) == "groupid_" )
				{
					var varid = varName.split ( "groupid_" );
					varid = varid[ 1 ];
					list1[ a ].value = varid;
				}
				else
				{
					list1[ a ].value = list1[ a ].title;
				}
				list1[ a ].title = "";
				list1[ a ].text = list1[ a ].innerHTML;
				list1[ a ].selected = false;
				list1[ a ].isSelector = true;
				list1[ a ].onclick = function ( )
				{
					if ( this.selected == false )
					{
						this.selected = true;
						this.className = "divselector_selected";
					}
					else
					{
						this.selected = false;
						this.className = "divselector";
					}
				}
			}
		}
	}
	for ( var a = 0; a < list2.length; a++ )
	{
		if ( 
			typeof ( list2[ a ].innerHTML ) == "string" 
		) 
		{
			if ( !list2[ a ].isSelector )
			{
				var varName = list2[ a ].title + "";
				if ( varName.substr( 0, 8 ) == "groupid_" )
				{
					var varid = varName.split ( "groupid_" );
					varid = varid[ 1 ];
					list2[ a ].value = varid;
				}
				else
				{
					list2[ a ].value = list2[ a ].title;
				}
				list2[ a ].title = "";
				list2[ a ].text = list2[ a ].innerHTML;
				list2[ a ].selected = true;
				list2[ a ].isSelector = true;
				list2[ a ].onclick = function ( )
				{
					if ( this.selected == false )
					{
						this.selected = true;
						this.className = "divselector_selected";
					}
					else
					{
						this.selected = false;
						this.className = "divselector";
					}
				}
			}
		}
		else if ( typeof ( list2[ a ].value ) != "undefined" ) 
			alert ( list2[ a ].value );
		else
			alert ( list2[ a ].value );
	}
}

function onNoDrop ()
{
	document.poofdom = document.createElement ( "IMG" );
	document.poofdom.timer = 5;
	document.poofdom.poof = function ( )
	{
		if ( this.timer > 0 )
		{
			if ( this.timer == 5 )
			{
				this.src = "admin/gfx/poof.gif";
				this.style.position = "absolute";
				this.style.height = "48px";
				this.style.width = "48px";
				this.style.top = ( mousey - 24 ) + "px";
				this.style.left = ( mousex - 24 ) + "px";
				document.poofnode = document.body.appendChild ( this );
			}
			this.timer--;
		}
		else
		{
			document.body.removeChild ( document.poofnode );
			this.timer = 0;
			clearInterval ( this.interval );
			document.poofdom = false;
		}
	}
	document.poofdom.interval = setInterval ( "document.poofdom.poof ( )", 40 );
}

// Toggles
function checkToggleElement ( varName, obj )
{
	var eles = document.getElementsByTagName ( "*" );
	for ( var a = 0; a < eles.length; a++ )
	{
		if ( eles[ a ].name == varName )
		{
			if ( navigator.userAgent.indexOf ( "MSIE" ) > 0 )
				obj.style.cursor = "hand";
			else
				obj.style.cursor = "pointer";
			
			if ( eles[ a ].value.length > 0 || eles[ a ].notInit )
			{
				eles[ a ].style.display = "";
				obj.isClosed = false;
				obj.src = "admin/gfx/folder_open.gif";
			}
			else
			{
				eles[ a ].style.display = "none";
				obj.isClosed = true;
				obj.src = "admin/gfx/folder_closed.gif";
			}
			
			obj.element = eles[ a ];
			
			obj.onclick = function ( )
			{
				if ( this.isClosed )
				{
					this.element.style.display = "";
					this.isClosed = false;
					this.src = "admin/gfx/folder_open.gif";
					if ( navigator.userAgent.indexOf ( "MSIE" ) > 0 )
						this.style.cursor = "hand";
					else
						this.style.cursor = "pointer";
				}
				else
				{
					this.element.style.display = "none";
					this.isClosed = true;
					this.src = "admin/gfx/folder_closed.gif";
					if ( navigator.userAgent.indexOf ( "MSIE" ) > 0 )
						this.style.cursor = "hand";
					else
						this.style.cursor = "pointer";
				}
			}
		}

	}
}

function initToggles ( )
{
	var imgs = document.getElementsByTagName ( "IMG" );
	for ( var a = 0; a < imgs.length; a++ )
	{
		if ( imgs[ a ].id && imgs[ a ].id.substr ( 0,7 ) == "toggle_" )
		{
			var id = imgs[ a ].id.split ( "_" );
			id = id[ 1 ];
			checkToggleElement ( id, imgs[ a ] );
		}
	}
}
// Done Toggles


function getHelp ( sect )
{
	if ( !sect ) sect = '';
	else sect = '&route=' + encodeURIComponent ( sect );
	initModalDialogue ( 'help', 640, 492, 'admin.php?plugin=help&pluginaction=main' + sect )
}

addOnload ( function ( )
{
	if ( document.getElementById ( 'BajaxProgress' ) && document.getElementById ( 'BajaxProgressContainer' ) )
	{
		globalBajaxProgressElement = document.getElementById ( 'BajaxProgress' );
		globalBajaxProgressElementContainer = document.getElementById ( 'BajaxProgressContainer' );
		bajaxProgressMeter ( );
	}
} );

// Top tab management
function runTopTabArrows ( )
{
	if ( toptabsarrows ) return;
	toptabsarrows = true;
	var al = document.getElementById ( 'ModuleMoreArrowLeft' );
	var ar = document.getElementById ( 'ModuleMoreArrowRight' )
	if ( al.cop < al.op )
		al.cop += 5;
	else if ( al.cop > al.op )
		al.cop -= 5;
	if ( ar.cop < ar.op )
		ar.cop += 5;
	else if ( ar.cop > ar.op )
		ar.cop -= 5;
	if ( ar.cop != ar.op || al.cop != al.op )
		setTimeout ( 'runTopTabArrows()', 60 );
	setOpacity ( al, al.cop / 100 );
	setOpacity ( ar, ar.cop / 100 );
	toptabsarrows = false;
}
var tabxoffset = 0;
var toptabsarrows = false;
function initTopTabs ( )
{
	if ( document.getElementById ( 'ModuleListInner' ) )
	{
		var inner = document.getElementById ( 'ModuleListInner' )
		var divs = inner.getElementsByTagName ( 'div' );
		var modtabs = new Array ( );
		for ( var a = 0; a < divs.length; a++ )
		{
			if ( hasClass ( divs[ a ], 'ModuleTab' ) || hasClass ( divs[ a ], 'ModuleTabActive' ) )
				modtabs.push ( divs[ a ] );
		}
		var x = 0;
		var innerw = 0;
		var docw = getDocumentWidth ( );
		for ( var a = 0; a < modtabs.length; a++ )
		{
			modtabs[ a ].style.left = x + 'px';
			x += getElementWidth ( modtabs[ a ] ) + 2;
		}
		innerw = x;
		inner.innerw = x;
			
		var innerx = getCookie ( 'toptabsleft' ) ? parseInt ( getCookie ( 'toptabsleft' ) ) : 0;
		inner.style.left = innerx + 'px';
		setOpacity ( document.getElementById ( 'ModuleMoreArrowRight' ), 0 );
		setOpacity ( document.getElementById ( 'ModuleMoreArrowLeft' ), 0 );
		
		// Exit if tab width is no issue (and make sure position is correct)
		if ( innerw <= getDocumentWidth () - 10 )
		{
			document.getElementById ( 'ModuleListInner' ).style.left = '0px';
			return;
		}
			
		document.getElementById ( 'ModuleMoreArrowLeft' ).cop = 0;
		document.getElementById ( 'ModuleMoreArrowRight' ).cop = 0;
		
		// Fade in and out arrows and check module tab positions
		function t( )
		{
			var mli = document.getElementById ( 'ModuleListInner' );
			var innerw = mli.innerw;
			var docw = getDocumentWidth ( );
			
			if ( mousey >= 44 && mousey < 85 )
			{
				var val = tabxoffset;
				if ( mousex >= tabxoffset )
					val = tabxoffset - Math.floor ( mousex / docw * ( innerw - ( docw - tabxoffset ) ) );
				mli.style.left = val + 'px';
				setCookie ( 'toptabsleft', val );
			}
			var al = document.getElementById ( 'ModuleMoreArrowLeft' );
			var ar = document.getElementById ( 'ModuleMoreArrowRight' )
			var l = getElementLeft ( mli );
			al.op = l < 0 ? tabxoffset : 0;
			ar.op = ( l + innerw > docw ) ? tabxoffset : 0;
			if ( ar.cop != ar.op || al.cop != al.op )
				runTopTabArrows ( );
		}
		addEvent ( 'onmousemove', t );
		t();
	}
	else setTimeout ( 'initTopTabs ()', 50 );
}
// Readjust tabs after load
if ( window.addEventListener )
	window.addEventListener ( 'load', initTopTabs, true );
else window.attachEvent ( 'onload', initTopTabs, true );
