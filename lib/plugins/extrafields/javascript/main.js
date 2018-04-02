

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
 * Field editor
 */

var ExtraFields = new Array ( );
var ExtraFieldUpdateFunctions = new Array ( );

function addExtraFieldUpdateFunction ( func )
{
	ExtraFieldUpdateFunctions.push ( func );
}

function addExtraField ( )
{
	if( !document.getElementById ( 'FieldType' ).value ||
		!document.getElementById ( 'FieldName' ).value ||
		!ContentType || !ContentID
	)
	{
		alert( 'Det oppsto en feil ved å legge til feltet.' );
		return false;
	}
	var j = new bajax ( );
	j.addVar ( 'type', document.getElementById ( 'FieldType' ).value );
	j.addVar ( 'name', document.getElementById ( 'FieldName' ).value );
	j.addVar ( 'table', ContentType );
	j.addVar ( 'contentid', ContentID );
	j.openUrl (
		'admin.php?plugin=extrafields&pluginaction=addextrafield', 
		'post', true
	);
	j.onload = function ( )
	{
		showExtraFields ( );
	}
	j.send ( );
}

function initFieldEditor ( )
{
	ExtraFields = document.getElementById ( 'FieldData' ).split ( "\n" );
}

function editField ( fid, type, tx )
{
	var fjax = new bajax ( );
	fjax.openUrl ( 
		'admin.php?plugin=extrafields&pluginaction=editfield&fid=' + fid + '&txt=' + tx + '&type=' + type, 
		'get', true 
	);
	fjax.onload = function ( )
	{
		showExtraFields ( );
	}
	fjax.send ( );
}


function showExtraFields ( )
{
	if( !ContentType || !ContentID ) return false;
	var j = new bajax ( );
	j.openUrl ( 
		'admin.php?plugin=extrafields&pluginaction=showextrafields&contenttype=' + 
		ContentType + '&contentid=' + ContentID, 'get', true
	);
	j.onload = function ( )
	{
		document.getElementById ( 'ExtraFieldListing' ).innerHTML = this.getResponseText ( );
		for ( var a = 0; a < ExtraFieldUpdateFunctions.length; a++ )
		{
			ExtraFieldUpdateFunctions[ a ] ( );
		}
	}
	j.send ( );
}

function moveExtraField ( offset, vid )
{
	if( !ContentType || !ContentID ) return false;
	document.exjax = new bajax ( );
	document.exjax.openUrl ( 
		'admin.php?plugin=extrafields&pluginaction=move&contenttype=' + 
		ContentType + '&contentid=' + ContentID + '&offset=' + offset + '&vid=' + vid, 'get', true
	);
	document.exjax.onload = function ( )
	{
		showExtraFields ( );
		document.exjax = 0;
	}
	document.exjax.send ( );
}

function actionExtraField ( varaction, varfunction )
{
	if ( !varfunction ) varfunction = false;
	var j = new bajax();
	j.openUrl( varaction, 'get', true );
	j.onload = function ( )
	{
		if ( varfunction )
			varfunction ( );
	}
	j.send();
}

function deleteExtraField ( fieldid, contenttable )
{
	var j = new bajax ( );
	j.openUrl ( 'admin.php?plugin=extrafields&pluginaction=delete&contenttable=ContentData' + 
		contenttable + '&contentid=' + fieldid, 'get', true );
	j.onload = function ( )
	{
		showExtraFields ( );
	}
	j.send ( );
}

/**
 * Set content group on field
**/
function setFieldGroup ( contentid, contenttable, setting )
{
	document.fjax = new bajax ( );
	document.fjax.openUrl ( 
		'admin.php?plugin=extrafields&pluginaction=setgroup&contenttable=ContentData' + contenttable + '&contentid=' + contentid + '&group=' + setting, 
		'get', true
	);
	document.fjax.onload = function ( )
	{
		showExtraFields ( );
		document.fjax = 0;
	}
	document.fjax.send ( );
}

/**
 * Set sortorder on field
**/
function setFieldSortOrder ( contentid, contenttable, sovalue )
{
	var sojax = new bajax ( );
	sojax.openUrl ( 
		'admin.php?plugin=extrafields&pluginaction=setfieldsortorder&' +
		'contenttable=ContentData' + contenttable + '&contentid=' + contentid + '&value=' + sovalue, 
		'get', true 
	);
	sojax.onload = function ( )
	{
		showExtraFields ( );
	}
	sojax.send ( );
}

/**
 * Set visibility on a field (to others than the core users)
**/
function setFieldVisibility ( contentid, contenttable, setting )
{
	document.fjax = new bajax ( );
	document.fjax.openUrl ( 
		'admin.php?plugin=extrafields&pluginaction=setvisibility&contenttable=ContentData' + contenttable + '&contentid=' + contentid + '&visibility=' + setting, 
		'get', true
	);
	document.fjax.onload = function ( )
	{
		showExtraFields ( );
		document.fjax = 0;
	}
	document.fjax.send ( );
}

/**
 * Set globality on a field (to others than the core users)
**/
function setFieldGlobal ( contentid, contenttable, setting )
{
	document.fjax = new bajax ( );
	document.fjax.openUrl ( 
		'admin.php?plugin=extrafields&pluginaction=setglobal&contenttable=ContentData' + contenttable + '&contentid=' + contentid + '&global=' + setting, 
		'get', true
	);
	document.fjax.onload = function ( )
	{
		showExtraFields ( );
		document.fjax = 0;
	}
	document.fjax.send ( );
}

function removeHTML ( varid )
{
	document.rjax = new bajax ( );
	document.rjax.openUrl ( 'admin.php?plugin=extrafields&pluginaction=removehtml', 'post', true );
	document.rjax.addVar ( 'content', encodeURIComponent ( editor.get ( varid ).getContent ( ) ) );
	document.rjax.onload = function ( )
	{
		editor.get ( varid ).setContent ( this.getResponseText ( ) );
		document.rjax = 0;
	}
	document.rjax.send ( );
}


function nudgecobj ( dir, id, obj )
{
	var bjax = new bajax ( );
	bjax.openUrl ( 'admin.php?plugin=extrafields&pluginaction=nudgeobject&dir=' + dir + '&obj=' + id, 'get', true );
	bjax.obj = obj;
	bjax.onload = function ( )
	{
		obj.parentNode.parentNode.parentNode.innerHTML = this.getResponseText ( );
	}
	bjax.send ( );
}

function delcobj ( id, obj )
{
	if ( confirm ( 'Er du sikker?' ) )
	{
		var bjax = new bajax ( );
		bjax.openUrl ( 'admin.php?plugin=extrafields&pluginaction=deleteobject&obj=' + id, 'get', true );
		bjax.obj = obj;
		bjax.onload = function ( )
		{
			obj.parentNode.parentNode.parentNode.innerHTML = this.getResponseText ( );
		}
		bjax.send ( );
	}
}

