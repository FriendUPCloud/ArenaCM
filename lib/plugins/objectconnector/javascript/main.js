

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



function initObjectConnector ( )
{
	if ( document.getElementById ( 'ObjectDropArea' ) )
	{
		ge ( 'ObjectDropArea' ).onDragOver = function ()
		{
			this.oldborder = this.style.border;
			this.style.border = '2px solid #00aa00';
			this.style.borderTop = '2px solid #008800';
		}
		ge ( 'ObjectDropArea' ).onDragOut = function ()
		{
			this.style.border = this.oldborder;
		}
		ge ( 'ObjectDropArea' ).onDragDrop = function ( )
		{
			var drjax = new bajax ( );
			drjax.openUrl ( 
				"admin.php?plugin=objectconnector&pluginaction=addobject&" +
				"objectid=" + ObjectConnectionId + "&objecttype=" + ObjectConnectionType + "&" +
				"connectedobjecttype=" + dragger.config.objectType + "&connectedobjectid=" + 
				dragger.config.objectID, "get", true
			);
			drjax.onload = function ()
			{
				if ( this.getResponseText ( ) != 'OK' )
				{
					alert ( this.getResponseText ( ) );
				}
				else showObjectConnections ( );
			}
			drjax.send ( );
			this.style.border = this.oldborder;
		}
		dragger.addTarget ( document.getElementById ( 'ObjectDropArea' ) );
		showObjectConnections ( );
	}
	else setTimeout ( 'initObjectConnector ( )', 50 );
}

function showObjectConnections ( o, t )
{
	var scriptjax = new bajax ( );
	scriptjax.openUrl (
		"admin.php?plugin=objectconnector&pluginaction=objects&" +
		"objecttype=" + ObjectConnectionType + "&objectid=" + ObjectConnectionId,
		"get", true
	);
	scriptjax.onload = function ( )
	{
		document.getElementById ( 'Objects' ).innerHTML = this.getResponseText ();
	}
	scriptjax.send ( );
}

function poc_Nudge ( offset, type, id )
{
	var poc = new bajax ( );
	poc.openUrl ( 
		'admin.php?plugin=objectconnector&pluginaction=nudge&' +
		'connectedobjectid= ' + id + '&connectedobjecttype=' + type + '&offset=' + offset +
		'&objectid=' + ObjectConnectionId + '&objecttype=' + ObjectConnectionType, 
		'get', true
	);
	poc.onload = function ( )
	{
		showObjectConnections ( );
	}
	poc.send ( );
}

function poc_Delete ( type, id )
{
	if ( confirm ( 'Er du sikker?' ) )
	{
		var poc = new bajax ( );
		poc.openUrl ( 
			'admin.php?plugin=objectconnector&pluginaction=delete&' +
			'connectedobjectid=' + id + '&connectedobjecttype=' + type + '&' + 
			'objectid=' + ObjectConnectionId + '&objecttype=' + ObjectConnectionType, 
			'get', true
		);
		poc.onload = function ( )
		{
			showObjectConnections ( );
		}
		poc.send ( );
	}
}

function poc_emptyConnectedObjects ()
{
	if ( confirm ( 'Er du sikker?' ) )
	{
		var poc = new bajax ( );
		poc.openUrl ( 
			'admin.php?plugin=objectconnector&pluginaction=removeall&' +
			'objectid=' + ObjectConnectionId + '&objecttype=' + ObjectConnectionType, 
			'get', true
		);
		poc.onload = function ( )
		{
			if ( this.getResponseText () != 'ok' )
				alert ( 'Problem' );
			showObjectConnections ( );
		}
		poc.send ( );
	}
}

function poc_doUploadObject ()
{
	initModalDialogue ( 'upload', 320, 180, 'admin.php?plugin=objectconnector&pluginaction=uploadfile&objectid=' + ObjectConnectionId + '&objecttype=' + ObjectConnectionType, false );
}

initObjectConnector ( );
