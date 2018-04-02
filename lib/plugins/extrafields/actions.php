<?php


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



function renderImageGui ( $obj )
{
	if ( $obj->DataInt )
	{
		$img = new dbImage ( $obj->DataInt );
		return ( '
					<div class="SubContainer" style="float: right; margin: 0 0 0 8px; padding: 2px; text-align: center" id="image' . $obj->Name . '">
						<img style="border: 0" src="' . $img->getImageUrl ( 92, 48 ) . '" /><br />
						<button type="button" style="width: 100%; margin: 2px 0 0 0" onclick="deleteImg' . $obj->Name . '( )">
							<img src="admin/gfx/icons/image_delete.png" /> Fjern bildet
						</button>
					</div>
		' );
	}
	else
	{
		return ( '
					<div style="width: 96px; height: 72px; float: right; margin: 0 0 0 8px; padding: 2px; text-align: center" id="image' . $obj->Name . '" class="Dropzone" id="image' . $obj->Name . '">
						<div style="margin-top: 30px">Slipp bilde her</div>
					</div>
		' );
	}
}

switch ( $_REQUEST[ 'pluginaction' ] )
{
	case 'setsettingvalue':
		include_once ( 'actions/setsettingvalue.php' );
		break;
	case 'setfieldsortorder':
		include_once ( 'actions/setfieldsortorder.php' );
		break;
	case 'adminrender':
		include_once ( 'actions/adminrender.php' );
		break;
	case 'getimagepreviews':
		include_once ( 'actions/getimagepreviews.php' );
		break;
	case 'editfield':
		include_once ( 'actions/editfield.php' );
		break;
	case 'addimagetoimagefield':
		include_once ( 'actions/addimagetoimagefield.php' );
		break;
	case 'showextrafields':
		include_once ( 'actions/showextrafields.php' );
		break;
		
	case 'addextrafield':
		include_once ( 'actions/addextrafield.php' );
		break;
	
	case 'connectobject':
		include_once ( 'actions/connectobject.php' );
		break;
		
	case 'nudgeobject':
		include_once ( 'actions/nudgeobject.php' );
		break;
	
	case 'deleteobject':
		include_once ( 'actions/deleteobject.php' );
		break;
		
	case 'move':
		include_once ( 'actions/move.php' );
		break;
		
	case 'delete':
		include_once ( 'actions/delete.php' );
		break;
	
	case 'setfieldoption':
		include_once ( 'actions/setfieldoption.php' );
		break;
	
	case 'deleteimagedata':
		include_once ( 'actions/deleteimagedata.php' );
		break;
	
	case 'setvisibility':
		include_once ( 'actions/setvisibility.php' );
		break;
	
	case 'setglobal':
		include_once ( 'actions/setglobal.php' );
		break;
	
	case 'setgroup':
		include_once ( 'actions/setgroup.php' );
		break;
	
	case 'removehtml':
		include_once ( 'actions/removehtml.php' );
		break;
}
?>
