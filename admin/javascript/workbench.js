

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



function initWorkbench ( )
{
	if ( 
 		document.getElementById ( "Workbench" ) && 
		document.getElementById ( "WorkbenchWastebin" ) &&
		typeof ( dragger ) != "undefined"
	)
	{
		var wastebin = document.getElementById ( "WorkbenchWastebin" );
		var workbench = document.getElementById ( "Workbench" );
		var wbarea = document.getElementById ( "WorkbenchArea" );
		var hider = document.getElementById ( "WorkbenchHider" );
		setOpacity ( workbench, 0.95 );
		
		// Add targets anew
		dragger.addTarget ( wastebin );
		dragger.addTarget ( workbench );
		
		wastebin.ondblclick = function ( )
		{
			emptyWorkbench ( );
		}
		wastebin.onDragDrop = function ( element )
		{
			var objectID, objectType;
			if ( element.objectID && element.objectType )
			{
				objectID = element.objectID; 
				objectType = element.objectType;
			}
			else if ( dragger.config.objectID && dragger.config.objectType )
			{
				objectID = dragger.config.objectID; objectType = dragger.config.objectType;
			}
			if ( objectID && objectType )
			{
				removeFromWorkbench ( objectID, objectType );
			}
		}
		
		workbench.onDragDrop = function ( element )
		{
			var objectID, objectType;
			if ( element.objectID && element.objectType )
			{
				objectID = element.objectID; 
				objectType = element.objectType;
			}
			else if ( dragger.config.objectID && dragger.config.objectType )
			{
				objectID = dragger.config.objectID; objectType = dragger.config.objectType;
			}
			if ( objectID && objectType )
			{
				addToWorkbench ( objectID, objectType );
			}
		}
		
		hider.onclick = function ( )
		{
			if ( !getCookie ( 'WorkbenchState' ) )
				setCookie ( 'WorkbenchState', 'hidden' );
				
			var state = getCookie ( 'WorkbenchState' );
			
			if ( state == 'shown' )
				hideWorkbench ( );
			else showWorkbench ( );
		}
		
		var state = getCookie ( 'WorkbenchState' );
		setWorkbenchHider ( state );
		drawWorkbenchElements ( );
		if ( getCookie ( 'WorkbenchVisibility' ) == 'hidden' )
		{
			hideWorkbench ( );
		}
		else 
		{
			showWorkbench ( );
		}
	}
	else
	{
		setTimeout ( "initWorkbench ( )", 20 );
	}
}

function addToWorkbench ( varid, vartype )
{
	var elem = getCookie ( 'WorkbenchElements' ) + "";
	elem = elem.split( ";" );
	var outar = new Array ( );
	for ( var a = 0; a < elem.length; a++ )
	{
		var e = elem[ a ].split ( ':' );
		if ( e[ 0 ] == varid && e[ 1 ] == vartype ) return;
	}
	setCookie ( 'WorkbenchElements', getCookie ( 'WorkbenchElements' ) + ";" + varid + ":" + vartype );
	drawWorkbenchElements ( );
}

function removeFromWorkbench ( varID, varType )
{
	var elem = getCookie ( 'WorkbenchElements' ) + "";
	elem = elem.split( ";" );
	var outar = new Array ( );
	for ( var a = 0; a < elem.length; a++ )
	{
		var e = elem[ a ].split ( ':' );
		if ( e[ 0 ] != varID || e[ 1 ] != varType )
			outar[ outar.length ] = elem[ a ];
	}
	setCookie ( 'WorkbenchElements', outar.join ( ";" ) );
	drawWorkbenchElements ( );
}

function drawWorkbenchElements ( )
{
	var wbJax = new bajax ( );
	wbJax.openUrl ( 
		"admin.php?plugin=workbench&pluginaction=showelements&data=" + getCookie ( 'WorkbenchElements' ),
		"get", true
	);
	wbJax.onload = function ( )
	{
		document.getElementById ( "WorkbenchArea" ).innerHTML = this.getResponseText ( );
	}
	wbJax.send ( );
}

function setWorkbenchHider ( state )
{
	var wastebin = document.getElementById ( "WorkbenchWastebin" );
	var workbench = document.getElementById ( "Workbench" );
	var wbarea = document.getElementById ( "WorkbenchArea" );
	var hider = document.getElementById ( "WorkbenchHider" );
		
	if ( state == 'hidden' )
	{
		if ( workbench.style.display == 'none' )
			return;
		setCookie ( 'WorkbenchState', 'hidden' );
		workbench.style.display = 'none';
		wastebin.style.display = 'none';
		wbarea.style.display = 'none';
		document.getElementById ( 'WorkbenchHiderImage' ).src = 'admin/gfx/icons/resultset_next.png';
	} 
	else
	{
		if ( workbench.style.display == '' )
			return;
		setCookie ( 'WorkbenchState', 'shown' );
		workbench.style.display = '';
		wastebin.style.display = '';
		wbarea.style.display = '';
		document.getElementById ( 'WorkbenchHiderImage' ).src = 'admin/gfx/icons/resultset_previous.png';
	}
}

function emptyWorkbench ( )
{
	document.getElementById ( "WorkbenchArea" ).innerHTML = "";
	setCookie ( 'WorkbenchElements', '' );
}

if ( !getCookie ( 'WorkbenchVisibility' ) )
{
	setCookie ( 'WorkbenchVisibility', 'hidden' );
}

function hideWorkbench ( )
{
	setCookie ( 'WorkbenchVisibility', 'hidden' );
	
	document.getElementById ( 'hideWorkbench' ).style.display = 'none';
	document.getElementById ( 'showWorkbench' ).style.display = '';
	
	document.getElementById ( 'WorkbenchArea' ).oldz = document.getElementById ( 'WorkbenchArea' ).style.zIndex;
	document.getElementById ( 'Workbench' ).oldz = document.getElementById ( 'Workbench' ).style.zIndex;
	document.getElementById ( 'WorkbenchWastebin' ).oldz = document.getElementById ( 'WorkbenchWastebin' ).style.zIndex;
	document.getElementById ( 'WorkbenchHider' ).oldz = document.getElementById ( 'WorkbenchHider' ).style.zIndex;
	document.getElementById ( 'WorkbenchHiderImage' ).oldz = document.getElementById ( 'WorkbenchHiderImage' ).style.zIndex;
	
	document.getElementById ( 'WorkbenchArea' ).style.zIndex = -1;
	document.getElementById ( 'Workbench' ).style.zIndex = -1;
	document.getElementById ( 'WorkbenchWastebin' ).style.zIndex = -1;
	document.getElementById ( 'WorkbenchHider' ).style.zIndex = -1;
	document.getElementById ( 'WorkbenchHiderImage' ).style.zIndex = -1;
	
	document.getElementById ( 'WorkbenchArea' ).style.display = '';
	document.getElementById ( 'Workbench' ).style.display = 'none';
	document.getElementById ( 'WorkbenchWastebin' ).style.display = 'none';
	document.getElementById ( 'WorkbenchHider' ).style.display = 'none';
	document.getElementById ( 'WorkbenchHiderImage' ).style.display = 'none';
	
	setWorkbenchHider ( 'hidden' );
}

function showWorkbench ( )
{
	setCookie ( 'WorkbenchVisibility', 'visible' );
	
	document.getElementById ( 'hideWorkbench' ).style.display = '';
	document.getElementById ( 'showWorkbench' ).style.display = 'none';
	var eles = Array ( 'WorkbenchArea', 'Workbench', 'WorkbenchWastebin', 'WorkbenchHider', 'WorkbenchHiderImage' );
	
	if ( !document.getElementById ( 'WorkbenchArea' ).oldz )
	{
		for ( var a = 0; a < eles.length; a++ )
			document.getElementById ( eles[ a ] ).oldz = 10000;
	}
	
	for ( var a = 0; a < eles.length; a++ )
		document.getElementById ( eles[ a ] ).style.zIndex = document.getElementById ( eles[ a ] ).oldz = 10000;

	document.getElementById ( 'WorkbenchHider' ).style.display = '';
	document.getElementById ( 'WorkbenchHiderImage' ).style.display = '';
	
	setWorkbenchHider ( 'shown' );
}

