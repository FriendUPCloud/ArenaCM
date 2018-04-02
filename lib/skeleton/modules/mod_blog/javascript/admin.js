
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

function mod_blog_pos ( pos )
{
	var jax = new bajax ( );
	jax.openUrl ( ACTION_URL + 'mod=mod_blog&modaction=standard&bpos=' + pos, 'get', true );
	jax.onload = function ( )
	{
		ge ( 'mod_blog_content' ).innerHTML = this.getResponseText ( );
	}
	jax.send ( );
}
function mod_blog_new ( )
{
	var jax = new bajax ( );
	jax.openUrl ( ACTION_URL + 'mod=mod_blog&modaction=new', 'get', true );
	jax.onload = function ( )
	{
		var dom = document.createElement ( 'div' );
		dom.innerHTML = this.getResponseText ( );
		var old = ge ( 'mod_blog_content' ).getElementsByTagName ( 'div' )[0];
		var pnode = old.parentNode;
		pnode.replaceChild ( dom, old );
		mod_blog_setform ( );
		blogHideEditorButtons ();
	}
	jax.send ( );
}
function mod_blog_setform ( id )
{
	var elements = ge ( 'mod_blog_content' ).getElementsByTagName ( 'input' );
	for ( var a = 0; a < elements.length; a++ )
	{
		if ( elements[ a ].type == 'hidden' )
		{
			elements[ a ].style.position = 'absolute';
			elements[ a ].style.left = '-1000px';
			elements[ a ].style.visibility = 'hidden';
		}
	}
	editor.addControl ( ge ( 'BlogLeadin' ) );
	editor.addControl ( ge ( 'BlogBody' ) );
	if ( id )
		ge ( 'mod_blog_content' ).style.borderColor = '#3E719C';
	else ge ( 'mod_blog_content' ).style.borderColor = '#a00';
	ge ( 'mod_blog_content' ).style.borderWidth = '2px';
}
function mod_blog_edit ( id )
{
	var jax = new bajax ( );
	jax.openUrl ( ACTION_URL + 'mod=mod_blog&modaction=edit&bid=' + id, 'get', true );
	jax.id = id;
	jax.onload = function ( )
	{
		var dom = document.createElement ( 'div' );
		dom.innerHTML = this.getResponseText ( );
		var old = ge ( 'mod_blog_content' ).getElementsByTagName ( 'div' )[0];
		var pnode = old.parentNode;
		pnode.replaceChild ( dom, old );
		mod_blog_setform ( this.id );
		
		// Hufflepuff
		dragger.addTarget ( ge ( 'BlogImagePreview' ) );
		ge ( 'BlogImagePreview' ).onDragDrop = function ()
		{
			var b = new bajax ();
			b.openUrl ( ACTION_URL + 'mod=mod_blog&modaction=editimage&bid=' + id + '&imageid=' + dragger.config.objectID, 'get', true );
			b.onload = function ()
			{
				if ( this.getResponseText ().indexOf ( 'img src=' ) > 0 )
				{
					ge ( 'BlogImagePreview' ).innerHTML = this.getResponseText ();
				}
			}
			b.send ();
		}
		ge ( 'BlogDetailPreview' ).onDragDrop = function ()
		{
			var b = new bajax ();
			b.openUrl ( ACTION_URL + 'mod=mod_blog&modaction=editdetailimage&bid=' + id + '&imageid=' + dragger.config.objectID, 'get', true );
			b.onload = function ()
			{
				if ( this.getResponseText ().indexOf ( 'img src=' ) > 0 )
				{
					ge ( 'BlogImagePreview' ).innerHTML = this.getResponseText ();
				}
			}
			b.send ();
		}
		blogHideEditorButtons();
	}
	jax.send ( );
}
function mod_blog_delete ( id )
{
	if ( confirm ( 'Er du sikker på at du ønsker å slette denne artikkelen?' ) )
	{
		var jax = new bajax ( );
		jax.openUrl ( ACTION_URL + 'mod=mod_blog&modaction=delete&bid=' + id, 'get', true );
		jax.onload = function ( )
		{
			ge ( 'mod_blog_content' ).innerHTML = this.getResponseText ( );
		}
		jax.send ( );
	}
}
function mod_blog_save ( )
{
	ge ( 'mod_blog_saveblog' ).innerHTML = 'Lagrer...';
	ge ( 'BlogLeadin' ).value = editor.get ( 'BlogLeadin' ).getContent ( );
	ge ( 'BlogBody' ).value = editor.get ( 'BlogBody' ).getContent ( );
	var fds = new Array ();
	var opts = ge('Folders').getElementsByTagName ( 'option' );
	for ( var a = 0; a < opts.length; a++ )
		if ( opts[a].selected ) fds.push ( opts[a].value );
	ge ( 'Folders_hidden' ).value = fds.join ( ',' );
	ge('blogform').submit ();
}
function mod_blog_removeimage ( bid, type )
{
	var i = new bajax ( );
	i.openUrl ( ACTION_URL + 'mod=mod_blog&modaction=removeimage&type=' + type + '&bid=' + bid, 'get', true );
	i.onload = function ( )
	{
		if ( ge ( 'BlogLeadin' ) )
		{
			editor.removeControl ( 'BlogLeadin' );
			editor.removeControl ( 'BlogBody' );
		}
		mod_blog_edit ( bid );
	}
	i.send ( );
}
function mod_blog_settings()
{
	initModalDialogue ( 'blogsettings', 780, 480, ACTION_URL + 'mod=mod_blog&modaction=settings', null );
}
function mod_blog_savesettings ( ) 
{
	ge ( 'mod_blog_savetext' ).innerHTML = 'Lagrer innstillingene';
	var sj = new bajax ( );
	sj.openUrl ( ACTION_URL + 'mod=mod_blog&modaction=savesettings', 'post', true );
	sj.addVar ( 'limit', ge ( 'mod_blog_limit' ).value );
	sj.addVar ( 'comments', ge ( 'mod_blog_comments' ).checked ? '1' : '0' );
	sj.addVar ( 'showauthor', ge ( 'mod_blog_showauthor' ).checked ? '1' : '0' );
	sj.addVar ( 'tagbox', ge ( 'mod_blog_tagbox' ).checked ? '1' : '0' );
	sj.addVar ( 'tagbox_placement', ge ( 'mod_tagbox_placement' ).value );
	sj.addVar ( 'searchbox', ge ( 'mod_blog_searchbox' ).checked ? '1' : '0' );
	sj.addVar ( 'detailpage', ge ( 'mod_blog_detailpage' ).getElementsByTagName ( 'select' )[0].value );
	sj.addVar ( 'sourcepage', ge ( 'mod_blog_sourcepage' ).getElementsByTagName ( 'select' )[0].value );
	sj.addVar ( 'leadinlength', ge ( 'mod_blog_leadinlength' ).value );
	sj.addVar ( 'titlelength', ge ( 'mod_blog_titlelength' ).value );
	sj.addVar ( 'sizex', ge ( 'mod_blog_sizex' ).value );
	sj.addVar ( 'sizey', ge ( 'mod_blog_sizey' ).value );
	sj.addVar ( 'lsizex', ge ( 'mod_blog_lsizex' ).value );
	sj.addVar ( 'lsizey', ge ( 'mod_blog_lsizey' ).value );
	sj.addVar ( 'imageaspect', ge ( 'mod_blog_imageaspect' ).value );
	sj.addVar ( 'imgcolor', ge ( 'mod_blog_imgcolor' ).value );
	sj.addVar ( 'imageaspectdtl', ge ( 'mod_blog_imageaspectdtl' ).value );
	sj.addVar ( 'imgcolordtl', ge ( 'mod_blog_imgcolordtl' ).value );
	sj.addVar ( 'headertext', ge ( 'mod_blog_headertext' ).value );
	sj.addVar ( 'hidedetails', ge ( 'mod_blog_hide_details' ).checked ? '1' : '0' );
	sj.addVar ( 'listmethod', ge ( 'mod_blog_listmethod' ).value );
	sj.addVar ( 'gallerymode', ge ( 'mod_blog_gallerymode' ).value );	
	sj.addVar ( 'facebooklike', ge ( 'mod_blog_facebooklike' ).checked ? 1 : '0' );
	sj.addVar ( 'fbcomments', ge ( 'mod_blog_FBComments' ).checked ? 1 : '0' );
	sj.addVar ( 'facebooklikedimensions', ge ( 'mod_blog_facebooklikewidth' ).value + ':' + ge ( 'mod_blog_facebooklikeheight' ).value );
	sj.addVar ( 'tagfilter', ge ( 'mod_blog_tagfilter' ).value );
	sj.addVar ( 'pagination', ge ( 'mod_blog_pagination' ).checked ? 1 : '0' );
	sj.onload = function ( )
	{
		if ( ge ( 'mod_blog_savetext' ) )
		{
			ge ( 'mod_blog_savetext' ).innerHTML = i18n ( 'saved' );
			setTimeout ( "ge ( 'mod_blog_savetext' ).innerHTML = '"+i18n('Save')+"';", 200 );
		}
	}
	sj.send ( );
}

function mod_blog_abortedit ( )
{
	if ( document.getElementById ( 'BlogLeadin' ) )
	{
		editor.removeControl ( 'BlogLeadin' );
		editor.removeControl ( 'BlogBody' );
	}
	var jax = new bajax ( );
	jax.openUrl ( ACTION_URL + 'mod=mod_blog&modaction=standard', 'get', true );
	jax.onload = function ( )
	{
		document.getElementById ( 'mod_blog_content' ).innerHTML = this.getResponseText ( );
		document.getElementById ( 'mod_blog_content' ).style.borderWidth = '';
		document.getElementById ( 'mod_blog_content' ).style.borderColor = '';
		blogShowEditorButtons();
	}
	jax.send ( );
}

function mod_blog_preview ( bid )
{
	window.open ( 'admin.php?module=extensions&extension=editor&mod=mod_blog&modaction=preview&bid=' + bid, '', 'width=640,height=480,status=no,resize=yes,scrollbars=1,topbar=no' );
}

function mod_blog_authentication ( )
{
	var j = new bajax ( );
	j.openUrl ( 'admin.php?module=extensions&extension=editor&mod=mod_blog&modaction=authentication', 'get', true );
	j.onload = function ( )
	{
		ge ( 'mod_blog_content' ).innerHTML = this.getResponseText ( );
	}
	j.send ( );
}

function mod_blog_sortorder ( id, order )
{
	if ( id && order )
	{
		var jax = new bajax ( );
		jax.openUrl ( ACTION_URL + 'mod=mod_blog&modaction=sortorder&bid=' + id + '&order=' + order, 'get', true );
		jax.onload = function ( )
		{
			ge ( 'mod_blog_content' ).innerHTML = this.getResponseText ( );
		}
		jax.send ( );
	}
}
	
function blogHideEditorButtons ()
{
	if ( ge('BottomButtonContainer') )
	{
		getElementsByClassName ( 'Bottom' )[0].style.display = 'none';
		ge('BottomButtonContainer').style.display = 'none';
	}
	else if ( ge ( 'BottomButtonsEasy' ) )
	{
		ge('BottomButtonsEasy').style.display = 'none';
	}
}
function blogShowEditorButtons ()
{
	if ( ge('BottomButtonContainer') )
	{
		getElementsByClassName ( 'Bottom' )[0].style.display = '';
		ge('BottomButtonContainer').style.display = '';
	}
	else if ( ge ( 'BottomButtonsEasy' ) )
	{
		ge('BottomButtonsEasy').style.display = '';
	}
}
