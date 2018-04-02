
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

function setContent ( cid )
{
	document.location = 'admin.php?module=extensions&extension=editor&cid=' + cid;
}

function savePage ( )
{
	if ( ge ( 'MenuTitle' ).value.length <= 0 )
	{
		alert ( i18n ( 'You need to fill in a menu title.' ) );
		ge ( 'MenuTitle' ).focus ( );
		return false;
	}
	if ( ge ( 'Title' ).value.length <= 0 )
	{
		ge ( 'Title' ).value = ge ( 'MenuTitle' ).value;
		return savePage ();
	}
	for ( var a = 0; a < savefuncs.length; a++ )
		savefuncs[a]();
	
	var cid = ge ( 'PageID' ).value;
	var pjax = new bajax ( );
	pjax.openUrl ( 
		'admin.php?module=extensions&extension=editor&action=save', 
		'post', true );
	pjax.addVar ( 'cid', cid );
	if ( ge ( 'IntroText' ) )
		pjax.addVar ( 'Intro', ge ( 'IntroText' ).value );
	if ( ge ( 'BodyText' ) )
		pjax.addVar ( 'Body', ge ( 'BodyText' ).value );
	pjax.addVar ( 'Title', ge ( 'Title' ).value );
	pjax.addVar ( 'MenuTitle', ge ( 'MenuTitle' ).value );
	var notes = ge ( 'PageNotes' ).value;
	if ( typeof ( notes ) != 'undefined' && notes.length )
		pjax.addVar ( 'Notes', ge ( 'PageNotes' ).value );
	if ( ge ( 'LinkText' ) )
		pjax.addVar ( 'Link', ge ( 'LinkText' ).value );
	if ( ge ( 'LinkTarget' ) )
		pjax.addVar ( 'LinkTarget', ge ( 'LinkTarget' ).value );
	
	// Extra fields
	if ( ge ( 'ContentForm' ) )
	{
		var elements = ge ( 'ContentForm' ).getElementsByTagName ( '*' );
		var extrafields = new Array ( );
		if ( elements )
		{
			for ( var a = 0; a < elements.length; a++ )
				if ( elements[ a ].className.indexOf ( 'ExtraFieldData' ) >= 0 )
					extrafields.push ( elements[ a ] );
		}
		for ( var a = 0; a < extrafields.length; a++ )
		{
			var ed;
			if ( ed = editor.get ( extrafields[ a ].id ) )
				extrafields[ a ].value = ed.getContent ( );
			pjax.addVar ( extrafields[ a ].id, extrafields[ a ].value );
		}
	}
	
	pjax.onload = function ( )
	{
		if ( !ge ( 'Structure' ) ) return;
		var structure = this.getResponseText ( ).split ( '<!-- separate -->' );
		var pageUrl = structure[1];
		structure = structure[0];
		if ( structure.indexOf ( '<error>' ) >= 0 )
		{
			structure = structure.split ( '<error>' )[1].split ( '</error>' )[0];
			alert ( structure );
		}
		else
		{
			ge ( 'PageUrl' ).value = pageUrl;
			ge ( 'StructureContainer' ).innerHTML = structure;
			makeCollapsable ( ge ( 'Structure' ) );
			if ( ge ( 'EditHeadlineDiv' ) )
			{
				var as = ge ( 'Structure' ).getElementsByTagName ( 'a' );
				for ( var z = 0; z < as.length; z++ )
				{
					if ( as[z].className == 'current' )
					{
						ge ( 'EditHeadlineDiv' ).innerHTML = as[z].firstChild.innerHTML;
						break;
					}
				}
			}
		}
		updateExtraFields ();
		updateButtons ( );
	}
	pjax.send ( );
}

function updateExtraFields ()
{
	if ( !texteditor )
		return false;
	var eds = texteditor.editors;
	for ( var a = 0; a < eds.length; a++ )
	{
		var j = new bajax ();
		var info = eds[a].area.id.split ( '_' );
		if ( info.length == 4 )
		{
			j.openUrl ( 'admin.php?module=extensions&extension=editor&action=refreshfield', 'post', true );
			j.addVar ( 'fieldid', info[1] );
			j.addVar ( 'fieldtype', info[2] );
			j.addVar ( 'field', info[3] );
			j.field = eds[a];
			j.onload = function ()
			{
				if ( this.getResponseText () == '<!--fail-->' )
				{
					alert ( 'Kunne ikke hente felt informasjon.' );
					return;
				}
				this.field.getDocument ().body.innerHTML = this.getResponseText ();
			}
			j.send ();
		}
	}
}

function subPage ( )
{
	var cid = ge ( 'PageID' ).value;
	var pjax = new bajax ( );
	pjax.openUrl (
		'admin.php?module=extensions&extension=editor&action=subpage',
		'post', true );
	pjax.addVar ( 'cid', cid );
	pjax.onload = function ( )
	{
		var structure = this.getResponseText ( );
		if ( structure.indexOf ( '<error>' ) >= 0 )
		{
			structure = structure.split ( '<error>' )[1].split ( '</error>' )[0];
			alert ( structure );
		}
		else
		{
			ge ( 'StructureContainer' ).innerHTML = structure;
			makeCollapsable ( ge ( 'Structure' ) );
		}
		updateButtons ( );
	}
	pjax.send ( );
}

var publishQueue = new Array ( );
function checkPublishQueue ( cid, ch )
{
	if ( !ch.checked )
	{
		var o = new Array ( );
		for ( var a = 0; a < publishQueue.length; a++ )
		{
			if ( publishQueue[ a ] != cid )
				o.push ( publishQueue[ a ] );
		}
		publishQueue = o;
	}
	else
	{
		publishQueue.push ( cid );
	}
}
function publishPageElements ( )
{
	removeModalDialogue ( 'publishqueue' );
	var p = new bajax ( );
	p.openUrl ( 'admin.php?module=extensions&extension=editor&action=publishqueue', 'post', true );
	p.addVar ( 'oids', publishQueue.join ( ',' ) );
	p.onload = function ( )
	{
		publishPage ( 1, true );
	}
	p.send ( );
}

function publishPage ( doReload, ignoreUnpublished )
{
	if ( ge ( 'MenuTitle' ).value.length <= 0 )
	{
		alert ( i18n ( 'You need to fill in a menu title.' ) );
		ge ( 'MenuTitle' ).focus ( );
		return false;
	}
	if ( ge ( 'Title' ).value.length <= 0 )
	{
		alert ( i18n ( 'You need to fill in a searchable page title.' ) );
		ge ( 'Title' ).focus ( );
		return false;
	}
	
	var cid = ge ( 'PageID' ).value;
	var testj = new bajax ( );
	testj.openUrl (
		'admin.php?module=extensions&extension=editor&action=testpublishqueue',
		'post', true
	);
	testj.addVar ( 'cid', cid );
	testj.onload = function ( )
	{
		if ( this.getResponseText ( ) == 'ok' || ignoreUnpublished )
		{
			var pjax = new bajax ( );
			pjax.openUrl ( 
				'admin.php?module=extensions&extension=editor&action=publish', 
				'post', true );
			pjax.addVar ( 'cid', cid );
			pjax.onload = function ( )
			{
				var structure = this.getResponseText ( );
				if ( structure.indexOf ( '<error>' ) >= 0 )
				{
					structure = structure.split ( '<error>' )[1].split ( '</error>' )[0];
					alert ( structure );
				}
				else
				{
					ge ( 'StructureContainer' ).innerHTML = structure;
					makeCollapsable ( ge ( 'Structure' ) );
				}
				if ( doReload )
				{
					document.location = 'admin.php?module=extensions&extension=editor';
				}
				else updateButtons ( );
			}
			pjax.send ( );
		}
		else
		{
			showPublishQueue ( cid );
		}
	}
	testj.send ( );
}

function revertPage ( )
{
	if ( confirm ( i18n ( 'Are you sure you want to roll back the\npublished version? The current work copy\nwill be erased.' ) ) )
	{
		var cid = ge ( 'PageID' ).value;
		var pjax = new bajax ( );
		pjax.openUrl ( 
			'admin.php?module=extensions&extension=editor&action=revert', 
			'post', true );
		pjax.addVar ( 'cid', cid );
		pjax.onload = function ( )
		{
			document.location = 'admin.php?module=extensions&extension=editor';
		}
		pjax.send ( );
	}
}

function showPublishQueue ( cid )
{
	initModalDialogue ( 'publishqueue', '480', '200', 'admin.php?module=extensions&extension=editor&action=publishqueue&cid=' + cid );
}

function deletePage ( )
{
	if ( confirm ( i18n ( 'Are you sure you want to delete this page?' ) ) )
	{
		var cid = ge ( 'PageID' ).value;
		var pjax = new bajax ( );
		pjax.openUrl ( 
			'admin.php?module=extensions&extension=editor&action=delete', 
			'post', true );
		pjax.addVar ( 'cid', cid );
		pjax.onload = function ( )
		{
			var cid = this.getResponseText ( );
			if ( cid.indexOf ( '<error>' ) >= 0 )
			{
				cid = cid.split ( '<error>' )[1].split ( '</error>' )[0];
				alert ( cid );
			}
			else
			{
				document.location = 'admin.php?module=extensions&extension=editor&cid=' + cid;
			}
		}
		pjax.send ( );
	}
}

function updateStructure ( )
{
	var cid = ge ( 'PageID' ).value;
	var pjax = new bajax ( );
	pjax.openUrl ( 
		'admin.php?module=extensions&extension=editor&action=structure', 
		'post', true );
	pjax.addVar ( 'cid', cid );
	pjax.onload = function ( )
	{
		var structure = this.getResponseText ( );
		if ( structure.indexOf ( '<error>' ) >= 0 )
		{
			structure = structure.split ( '<error>' )[1].split ( '</error>' )[0];
			alert ( structure );
		}
		else
		{
			ge ( 'StructureContainer' ).innerHTML = structure;
			makeCollapsable ( ge ( 'Structure' ) );
		}
	}
	pjax.send ( );
	checkOrderChanged ( )
}

function updateButtons ( )
{
	var cid = ge ( 'PageID' ).value;
	var pjax = new bajax ( );
	pjax.openUrl ( 
		'admin.php?module=extensions&extension=editor&action=buttons', 
		'post', true );
	pjax.addVar ( 'cid', cid );
	pjax.onload = function ( )
	{
		var data = this.getResponseText ( ).split ( '<!-- separate -->' );
		ge ( 'StructureButtons' ).innerHTML = data[2];
		ge ( 'SmallButtons' ).innerHTML = data[1];
		ge ( 'BottomButtons' ).innerHTML = data[0];
	}
	pjax.send ( );
}

function checkOrderChanged ( )
{
	var cjax = new bajax ( );
	cjax.openUrl ( 'admin.php?module=extensions&extension=editor&action=orderchangedquery', 'get', true );
	cjax.onload = function ( )
	{
		var strd = ge ( 'StructureChangedButton' );
		var r = this.getResponseText ();
		if ( r.split ( ' ' ).join ( '' ).length > 1 )
		{
			strd.innerHTML = r;
			strd.style.height = 'auto';
			strd.style.marginBottom = '0';
		}
		else 
		{
			strd.innerHTML = '';
			strd.style.height = '1px';
			strd.style.marginBottom = '-1px';
		}
	}
	cjax.send ( );
}

function reorder ( dir )
{
	var sel = ge ( 'ReorderSelect' );
	var opts = sel.options;
	var index = sel.selectedIndex;
	
	// give ids
	for ( var a = 0; a < opts.length; a++ ) { opts[ a ].id = a + 1; }
	
	// Backtrack to where level starts
	for ( var a = index; a > 0 && checkOptionDepth ( opts[ a ] ) >= checkOptionDepth ( opts[ index ] ); a-- )
	{ }; 
	var start = ++a;
	
	// The other element to move against
	var target = index + dir;
	if ( target < 0 || target >= opts.length )
		return false;
	// Find the root of the target block if it is a block and we're moving up
	while ( dir < 0 && checkOptionDepth ( opts[ target ] ) > checkOptionDepth ( opts[ index ] ) )
	{ target--; }
	// Find the root of the target block if it is a block and we're moving down
	while ( dir > 0 && checkOptionDepth ( opts[ target ] ) > checkOptionDepth ( opts[ index ] ) )
	{ target++; }
	// We might have gone astray, so double check
	if ( target < 0 || target >= opts.length )
		return false;
	
	// Set objects involved
	var targetObj = opts[ target ];
	var sourceObj = opts[ index ];
	var targetID = targetObj.id;
	var sourceID = sourceObj.id;
	
	if ( checkOptionDepth ( targetObj ) < checkOptionDepth ( sourceObj ) )
		return false;
	
	// Save contents of eventual blocks
	var targetBlock = new Array ( );
	var sourceBlock = new Array ( );
	// We are moving the source block, remove children for the time being
	if ( checkOptionDepth ( opts[ index + 1 ] ) > checkOptionDepth ( opts[ index ] ) )
	{
		for ( var a = index + 1; a < opts.length && checkOptionDepth ( opts[ a ] ) > checkOptionDepth ( opts[ index ] ); a++ )
			sourceBlock.push ( opts[ a ] );
		for ( var a = 0; a < sourceBlock.length; a++ )
			sel.removeChild ( sourceBlock[ a ] );
	}
	
	// Find our indices again
	for ( var a = 0; a < opts.length; a++ )
	{
		if ( opts[ a ].id == targetID ) target = a;
		if ( opts[ a ].id == sourceID ) index = a;
	}
	
	// We are moving the target block, remove children for the time being
	if ( checkOptionDepth ( opts[ target + 1 ] ) > checkOptionDepth ( opts[ target ] ) )
	{
		for ( var a = target + 1; a < opts.length && checkOptionDepth ( opts[ a ] ) > checkOptionDepth ( opts[ target ] ); a++ )
			targetBlock.push ( opts[ a ] );
		for ( var a = 0; a < targetBlock.length; a++ ) 
			sel.removeChild ( targetBlock[ a ] );
	}
	
	// Find our indices again
	for ( var a = 0; a < opts.length; a++ )
	{
		if ( opts[ a ].id == targetID ) target = a;
		if ( opts[ a ].id == sourceID ) index = a;
	}
	
	// Make sure we only move on our own level
	if ( checkOptionDepth ( opts[ index + dir ] ) == checkOptionDepth( opts[ index ] ) )
	{
		// Move only a single option
		var t = opts[ index ];
		sel.removeChild ( opts[ index ] );
		sel.insertBefore ( t, opts[ index + dir ] );
	}
	
	// Find our indices again
	for ( var a = 0; a < opts.length; a++ )
	{
		if ( opts[ a ].id == targetID ) target = a;
		if ( opts[ a ].id == sourceID ) index = a;
	}
	
	// Add children on source if it was a block
	if ( sourceBlock.length )
	{
		if ( index + 1 >= opts.length )
		{
			for ( var a = 0; a < sourceBlock.length; a++ )
				sel.appendChild ( sourceBlock[ a ] );
		}
		else
		{
			var inext = opts[ index + 1 ];
			for ( var a = 0; a < sourceBlock.length; a++ )
				sel.insertBefore ( sourceBlock[ a ], inext );
		}
		delete sourceBlock;
	}
	
	// Find our indices again
	for ( var a = 0; a < opts.length; a++ )
	{
		if ( opts[ a ].id == targetID ) target = a;
		if ( opts[ a ].id == sourceID ) index = a;
	}
	
	// Add targetblocks
	if ( targetBlock.length )
	{
		//alert ( opts[ target ].text );
		if ( target + 1 >= opts.length )
		{
			for ( var a = 0; a < targetBlock.length; a++ )
				sel.appendChild ( targetBlock[ a ] );
		}
		else
		{
			var tnext = opts[ target + 1 ];
			for ( var a = 0; a < targetBlock.length; a++ )
				sel.insertBefore ( targetBlock[ a ], tnext );
		}
		delete targetBlock;
	}
	
	return;
}
function checkOptionDepth ( opt )
{
	depth = 0;
	if ( !opt )
		return false;
	for ( var a = 0; a < opt.innerHTML.length; a += 6 )
	{
		if ( opt.innerHTML.substr ( a, 6 ) == '&nbsp;' )
		{
			depth++;
		}
		else return depth;
	}
	return depth;
}

function saveorder ( )
{
	var sel = ge ( 'ReorderSelect' );
	var str = '';
	for ( var a = 0; a < sel.options.length; a++ )
	{
		str += a + ':' + sel.options[ a ].value + ';';
	}
	str = str.substr ( 0, str.length - 1 );
	var orderj = new bajax ( );
	orderj.openUrl ( 'admin.php?module=extensions&extension=editor&action=saveorder', 'post', true );
	orderj.addVar ( 'ids', str );
	orderj.onload = function ( )
	{
		updateStructure ( );
	}
	orderj.send ( );
}


function publishSortOrder ( )
{
	var cjax = new bajax ( );
	cjax.openUrl ( 'admin.php?module=extensions&extension=editor&action=publishsortorder', 'get', true );
	cjax.onload = function ( )
	{
		checkOrderChanged ( );
	}
	cjax.send ( );
}

function movePage ( )
{
	var cid = ge ( 'PageID' ).value;
	initModalDialogue ( 
		'move',
		480, 375,
		'admin.php?module=extensions&extension=editor&action=movepage&cid=' + cid
	);
}

function executeMove ( )
{
	if ( !ge ( 'StructureMove' ) ) return;
	var target = ge ( 'StructureMove' ).getElementsByTagName ( 'select' )[0].value;
	var cid = ge ( 'PageID' ).value;
	if ( cid == target )
	{
		alert ( i18n ( 'You can not move a page onto itself.' ) );
		return false;
	}
	var mjax = new bajax ( );
	mjax.openUrl ( 
		'admin.php?module=extensions&extension=editor&action=executemovepage' +
		'&cid=' + cid + '&target=' + target,
		'get', true );
	mjax.onload = function ( )
	{
		var res = this.getResponseText ( ).split ( '<!-- separate -->' );
		if ( res[ 0 ] == 'ok' )
		{
			ge ( 'StructureMove' ).innerHTML = res[ 1 ];
			updateStructure ( );
		}
		else
		{
			alert ( res[ 1 ] );
		}
	}
	mjax.send ( );
}

function reorderPage ( )
{
	var cid = ge ( 'PageID' ).value;
	initModalDialogue ( 
		'reorder', 
		480, 375,
		'admin.php?module=extensions&extension=editor&action=reorder&cid=' + cid
	);
}

function previewPage ( )
{
	var cid = ge ( 'PageID' ).value;
	window.open ( ge ( 'PageUrl' ).value + '?editmode=1', '', 'width=920,height=600,topbar=no,scrollbars=yes,resize=yes,status=no' );
}

/** Showing module blocks *****************************************************/

function showConnectedModules ( )
{
	var jax = new bajax ( );
	jax.openUrl ( 'admin.php?module=extensions&extension=editor&action=showmodules', 'post', true );
	jax.addVar ( 'type', 'connected' );
	jax.addVar ( 'pid', ge ( 'PageID' ).value );
	jax.onload = function ( )
	{
		ge ( 'pageModulesConnected' ).innerHTML = this.getResponseText ( );
	}
	jax.send ();
}

function showFreeModules ( )
{
	var jax = new bajax ( );
	jax.openUrl ( 'admin.php?module=extensions&extension=editor&action=showmodules', 'post', true );
	jax.addVar ( 'type', 'free' );
	jax.addVar ( 'pid', ge ( 'PageID' ).value );
	jax.onload = function ( )
	{
		ge ( 'pageModulesAvailable' ).innerHTML = this.getResponseText ( );
	}
	jax.send ();
}

/** Start Free modules **/

function addModule ( modname )
{
	var ajax = new bajax ( );
	ajax.openUrl ( 
		'admin.php?module=extensions&extension=editor&action=addmodule',
		'post', true );
	ajax.addVar ( 'mod', modname );
	ajax.onload = function ( )
	{
		var res = this.getResponseText ( ).split ( '<!-- separate -->' );
		if ( res[0] == 'ok' )
		{
			showConnectedModules ( );
			showFreeModules ( );
			ge ( 'tabModulesConnected' ).onclick ();
		}
		else if ( res[0] == 'okreload' )
		{
			ge ( 'tabModulesConnected' ).onclick ();
			document.location = 'admin.php?module=extensions&extension=editor';
		}
		else alert ( res[1] );
	}
	ajax.send ( );
}

function delModule ( modname )
{
	var ajax = new bajax ( );
	ajax.openUrl (
		'admin.php?module=extensions&extension=editor&action=delmodule',
		'post', true );
	ajax.addVar ( 'mod', modname );
	ajax.onload = function ( )
	{
		if ( this.getResponseText ( ) == 'okreload' )
		{
			document.location = 'admin.php?module=extensions&extension=editor';
		}
		else
		{
			showConnectedModules ( );
			showFreeModules ( );
		}
	}
	ajax.send ( );
}

function activateModule ( modname )
{
	var ajax = new bajax ( );
	ajax.openUrl ( 
		'admin.php?module=extensions&extension=editor&action=activatemodule',
		'post', true );
	ajax.addVar ( 'mod', modname );
	ajax.addVar ( 'cid', ge ( 'PageID' ).value );
	ajax.onload = function ( )
	{
		if ( this.getResponseText ( ) == 'ok' )
		{
			document.location = 'admin.php?module=extensions&extension=editor&cid=' + ge ( 'PageID' ).value;
		}
		else alert ( i18n ( 'Unexpected error.' ) );
	}
	ajax.send ( );
}

function deactivateModule ( modname )
{
	var ajax = new bajax ( );
	ajax.openUrl ( 
		'admin.php?module=extensions&extension=editor&action=deactivatemodule',
		'post', true );
	ajax.addVar ( 'mod', modname );
	ajax.addVar ( 'cid', ge ( 'PageID' ).value );
	ajax.onload = function ( )
	{
		if ( this.getResponseText ( ) == 'ok' )
		{
			document.location = 'admin.php?module=extensions&extension=editor&cid=' + ge ( 'PageID' ).value;
		}
		else alert ( i18n ( 'Unexpected error.' ) );
	}
	ajax.send ( );
}

/** End free modules **/

function changeLanguage( id )
{
	document.location = 'admin.php?module=extensions&extension=editor&languageid=' + id;
}

function addField ( )
{
	initModalDialogue ( 'addfield', 440, 530, 'admin.php?module=extensions&extension=editor&action=dlg_addfield&cid=' + ge ( 'PageID' ).value, function ( ){ ge ( 'diaform' ).Name.focus ( ); } );
}

function executeAddField ( )
{
	var frm = ge ( 'diaform' );
	var type = getRadioValue ( frm.type );
	var global = getRadioValue ( frm.global );
	var contentgroup = getRadioValue ( frm.contentgroup );
	if ( !contentgroup )
	{
		alert ( i18n ( 'You need to choose a content group.' ) );
		return false;
	}
	var aja = new bajax ( );
	aja.openUrl ( 'admin.php?module=extensions&extension=editor&action=addfield', 'post', true );
	aja.addVar ( 'Name', frm.Name.value );
	aja.addVar ( 'ContentGroup', contentgroup );
	aja.addVar ( 'Type', type );
	aja.addVar ( 'IsGlobal', global );
	aja.addVar ( 'SortOrder', frm.SortOrder.value );
	aja.addVar ( 'cid', ge ( 'PageID' ).value );
	if ( type == 'extension' && frm.fieldextension )
		aja.addVar ( 'fieldextension', frm.fieldextension.value );
	aja.onload = function ( )
	{
		var r = this.getResponseText ( ).split ( '<!-- separate -->' );
		switch ( r[0] )
		{
			case 'ok':
				document.location = 'admin.php?module=extensions&extension=editor&cid=' + ge ( 'PageID' ).value;
				break;
			default:
				if ( r[ 1 ] ) alert ( r[ 1 ] );
				break;
		}
	}
	aja.send ( );
}

function reorderField ( fid, ft, dir )
{
	if ( confirm ( i18n ( 'Are you sure you want to move the field?' ) ) )
	{
		document.location = 'admin.php?module=extensions&extension=editor&action=reorderfield&dir=' + dir + '&fid=' + fid + '&cid=' + ge ( 'PageID' ).value + '&ft=' + ft;
	}
}

function removeField ( fid, ft, dir )
{
	if ( confirm ( i18n ( 'Are you sure you want to remove the field?' ) ) )
	{
		document.location = 'admin.php?module=extensions&extension=editor&action=delfield&dir=' + dir + '&fid=' + fid + '&cid=' + ge ( 'PageID' ).value + '&ft=' + ft;
	}
}

function editEditorField ( fid, ft )
{
	initModalDialogue ( 'editfield', 440, 530, 'admin.php?module=extensions&extension=editor&action=dlg_editfield&cid=' + ge ( 'PageID' ).value + '&ft=' + ft + '&fid=' + fid, function ( ){ ge ( 'diaform' ).Name.focus ( ); } );
}

function executeEditField ( )
{
	var frm = ge ( 'diaform' );
	var type = '';
	if ( frm.type ) type = getRadioValue ( frm.type );
	var global = getRadioValue ( frm.global );
	var contentgroup = getRadioValue ( frm.contentgroup );
	if ( !contentgroup )
	{
		alert ( i18n ( 'You need to choose a content group.' ) );
		return false;
	}
	var aja = new bajax ( );
	aja.openUrl ( 'admin.php?module=extensions&extension=editor&action=editfield', 'post', true );
	aja.addVar ( 'fid', frm.field_id.value );
	aja.addVar ( 'ft', frm.field_type.value );
	aja.addVar ( 'Name', frm.Name.value );
	aja.addVar ( 'SortOrder', frm.SortOrder.value );
	if ( type == 'extension' && frm.fieldextension )
		aja.addVar ( 'fieldextension', frm.fieldextension.value );
	aja.addVar ( 'ContentGroup', contentgroup );
	if ( type.length )
		aja.addVar ( 'Type', type );
	aja.addVar ( 'IsGlobal', global );
	aja.addVar ( 'adminvisibility', ge ( 'fieldadminvisibility' ).checked ? '1' : '0' );
	aja.addVar ( 'cid', ge ( 'PageID' ).value );
	aja.onload = function ( )
	{
		var r = this.getResponseText ( ).split ( '<!-- separate -->' );
		switch ( r[0] )
		{
			case 'ok':
				document.location = 'admin.php?module=extensions&extension=editor&cid=' + ge ( 'PageID' ).value;
				break;
			default:
				if ( r[ 1 ] ) alert ( r[ 1 ] );
				break;
		}
	}
	aja.send ( );
}

function advancedSettings ( )
{
	initModalDialogue ( 'advanced', 480, 340, 
		'admin.php?module=extensions&extension=editor&action=dlg_advanced_settings&cid=' + ge ( 'PageID' ).value 
	);
}

// Refresh the content of the advanced dialog
function refreshAdvancedDialog()
{
	var j = new bajax();
	j.openUrl( 'admin.php?module=extensions&extension=editor&action=dlg_advanced_settings', 'post', true );
	j.addVar( 'cid', ge( 'PageID' ).value );
	j.addVar( 'refresh', 'true' );
	j.onload = function()
	{
		ge( 'AdvancedDialog' ).innerHTML = this.getResponseText();
	}
	j.send();
}

function executeAdvanced ( )
{
	var frm = ge ( 'advanced_form' );
	var jax = new bajax ( );
	jax.openUrl (
		'admin.php?module=extensions&extension=editor&action=dlg_advanced_settings',
		'post', true 
	);
	jax.addVar ( 'cid', ge ( 'PageID' ).value );
	jax.addVar ( 'system', getRadioValue ( frm.IsSystem ) );
	jax.addVar ( 'published', getRadioValue ( frm.IsPublished ) );
	jax.addVar ( 'fallback', getRadioValue ( frm.IsFallback ) );
	jax.addVar ( 'contenttype', frm.ContentType.value );
	jax.addVar ( 'systemname', frm.SystemName.value );
	jax.addVar ( 'pagetitle', frm.PageTitle.value );
	jax.addVar ( 'contentgroups', frm.ContentGroups.value );
	jax.addVar ( 'contenttemplateid', frm.ContentTemplateID.value );
	jax.addVar ( 'hidecontrols', getRadioValue ( frm.HideControls ) );
	if ( frm.Template )
		jax.addVar ( 'template', frm.Template.value );
	if ( frm.ModuleName )
	{
		jax.addVar ( 'modulename', frm.ModuleName.value );
		jax.addVar ( 'modulecontentgroup', frm.ModuleContentGroup.value );
	}
	jax.onload = function ()
	{
		if ( this.getResponseText ( ) == 'ok' )
		{
			refreshAdvancedDialog();
			savePage();
		}
		else
		{
			alert ( i18n ( 'Failed.' ) );
		}
	}
	jax.send ( );
}

function createTemplate ( )
{
	var i = prompt ( i18n ( 'Templatename' ) + ':', '' );
	if ( i.length )
	{
		var j = new bajax ( );
		j.openUrl ( 'admin.php?module=extensions&extension=editor&action=createtemplate&cid=' + ge ( 'PageID' ).value, 'post', true );
		j.addVar ( 'name', i );
		j.onload = function ( )
		{
			alert ( this.getResponseText ( ) );
		}
		j.send ( );
		
	}
}

function deleteTemplates ()
{
	var inps = document.getElementsByTagName ( 'input' );
	var checks = new Array ();
	for ( var a = 0; a < inps.length; a++ )
	{
		if ( inps[a].type.toLowerCase() != 'checkbox' ) continue;
		var id = inps[a].id ? inps[a].id.split('_') : false;
		if ( id && inps[a].checked )
		{
			checks.push ( id[1] );
		}
	}
	if ( checks.length )
	{
		if ( confirm ( i18n ( 'Are you sure?' ) ) )
		{
			var tj = new bajax ();
			tj.openUrl ( 'admin.php?module=extensions&extension=editor&action=deletetemplates&ids=' + checks.join ( ',' ), 'get', true );
			tj.onload = function () 
			{
				ge ( 'pageTemplates' ).innerHTML = this.getResponseText ();
			}
			tj.send ();
		}
	}
}

function eraseSelected ()
{
	
	var ids = new Array ();
	var eles = document.getElementsByTagName ( 'input' );
	for ( var a = 0; a < eles.length; a++ )
	{
		if ( eles[a].id.substr ( 0, 6 ) == 'trash_' && eles[a].checked )
		{
			ids.push ( eles[a].id.split ( '_' )[1] );
		}
	}
	if ( ids.length )
	{
		if ( confirm ( i18n ( 'Do you really want to erase the selected items?' ) ) )
		{
			document.location = 'admin.php?module=extensions&extension=editor&action=erase&ids=' + ids.join ( ',' );
		}
	}
	else alert ( i18n ( 'You need to select some items.' ) );
}

function restoreSelected ()
{
	var ids = new Array ();
	var eles = document.getElementsByTagName ( 'input' );
	for ( var a = 0; a < eles.length; a++ )
	{
		if ( eles[a].id.substr ( 0, 6 ) == 'trash_' && eles[a].checked )
		{
			ids.push ( eles[a].id.split ( '_' )[1] );
		}
	}
	if ( ids.length )
	{
		document.location = 'admin.php?module=extensions&extension=editor&action=restoredeleted&ids=' + ids.join ( ',' );
	}
	else alert ( i18n ( 'You need to select some items.' ) );
}

var savefuncs = new Array ( );
function AddSaveFunction ( func )
{
	savefuncs.push ( func );
}

/* Table layouts ------------------------------------------------------------ */

function refreshTableLayout ()
{
	var b = new bajax ();
	b.openUrl ( 'admin.php?module=extensions&extension=editor&action=gettablelayouts', 'get', true );
	b.onload = function ()
	{
		var tlc = ge('TableLayout');
		var r = this.getResponseText ().split ( '<!--separate-->' );
		if ( r[0] == 'ok' )
		{
			tlc.innerHTML = r[1];
		}
		else tlc.innerHTML = 'error: ' + this.getResponseText ();
	}
	b.send ();
}

function newTableLayout ()
{
	var b = new bajax ();
	b.openUrl ( 'admin.php?module=extensions&extension=editor&action=tablelayout', 'post', true );
	b.onload = function ()
	{
		refreshTableLayout ();
	}
	b.send ();
}

function delTableLayout ( lid )
{
	var b = new bajax ();
	b.openUrl ( 'admin.php?module=extensions&extension=editor&action=deltablelayout&sid='+lid, 'post', true );
	b.onload = function ()
	{
		refreshTableLayout ();
	}
	b.send ();
}

function saveTableLayout ( lid )
{
	var b = new bajax ();
	b.openUrl ( 'admin.php?module=extensions&extension=editor&action=tablelayout', 'post', true );
	b.addVar ( 'sid', lid );
	b.addVar ( 'data', ge( 'tlfrom_' + lid ).value + "\t" + ge( 'tlto_' + lid ).value );
	b.onload = function ()
	{
		refreshTableLayout ();
	}
	b.send ();
}


addOnload ( checkOrderChanged );
addOnload ( refreshTableLayout );
