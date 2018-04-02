
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

function ActivateModule ( fn, evt )
{
	if ( fn == document.currentActiveModule )
	{
		return;
	}
	if ( confirm ( 'Er du sikker?' ) )
	{
		document.location = 'admin.php?module=extensions&extension=easyeditor&action=activatemodule&file=' + fn + '&pageid='+document.getElementById('pageID').value;
	}
	return false;
}

function NoModule ( )
{
	document.location = 'admin.php?module=extensions&extension=easyeditor&action=nomodule&pageid='+document.getElementById('pageID').value;
}

function ShowAdvanced ()
{
	initModalDialogue ( 'advanced', 480, 480, 'admin.php?module=extensions&extension=easyeditor&action=advanceddialog' );
}

function SaveAdvancedOptions ( close )
{
	var ind = new Array ();
	var inp;
	if ( ge ( 'AdvChBoxen' ) && ( inp = ge ( 'AdvChBoxen' ).getElementsByTagName ( 'input' ) ) )
	{
		for ( var a = 0; a < inp.length; a++ )
		{
			if ( inp[a].checked )
				ind.push ( inp[a].id );
		}
	}
	if ( ind.length )
	{
		var j = new bajax ();
		j.openUrl ( 'admin.php?module=extensions&extension=easyeditor&action=saveadvsettings', 'post', true );
		j.addVar ( 'cids', ind.join ( ',' ) );
		j.send ();
	}
	if ( close ) removeModalDialogue ( 'advanced' );
}

function UploadFile ()
{
	initModalDialogue ( 'uploadfile', 320, 150, 'admin.php?module=extensions&extension=easyeditor&action=uploadfile&pid=' + document.getElementById('pageID').value );
}

function RemovePageAttachment ( oid )
{
	if ( confirm ( 'Er du sikker?' ) )
	{
		document.location = 'admin.php?module=extensions&extension=easyeditor&action=removepageattachment&oid='+oid+'&pid=' + document.getElementById('pageID').value;
	}
}

function SubPage ()
{
	initModalDialogue ( 'newpage', 480, 146, 'admin.php?module=extensions&extension=easyeditor&action=newpage' );
}

function _addPage ()
{
	if ( document.getElementById ( 'npTitle' ).value.length < 1 )
	{
		alert ( i18n ( 'You forgot the page title.' ) );
		document.getElementById ( 'npTitle' ).focus();
		return false;
	}
	var pid = document.getElementById ( 'pageID' ).value;
	var options = '&';
	options += 'title='+escape(document.getElementById ( 'npTitle' ).value)+'&';
	options += 'menutitle='+escape(document.getElementById ( 'npTitle' ).value);
	document.location='admin.php?module=extensions&extension=easyeditor&action=addpage&pid=' + pid + options;
}

function DeletePage ( pid )
{
	if ( confirm ( i18n ( 'Are you sure?' ) ) )
	{
		var pid = document.getElementById ( 'pageID' ).value;
		document.location='admin.php?module=extensions&extension=easyeditor&action=deletepage&pid=' + pid;
	}
}

