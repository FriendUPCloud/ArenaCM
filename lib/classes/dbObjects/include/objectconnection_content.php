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



/***********************************************************************************
* Interpreters for objects                                                         *
***********************************************************************************/

function icFolder ( $folder )
{
	$str .= '<element type="Folder" name="' . $folder->Name . '">';
	if ( $files = $folder->getFiles ( ) )
	{
		foreach ( $files as $file )
		{
			$obj = dbObject::create ( $file->Type, $this->_table->database, $file->ID );
			if ( strtolower ( get_class ( $obj ) ) == 'dbimage' ||  strtolower ( get_class ( $obj ) ) == 'dbfile' )
			{
				$str .= '<element type="Image" title="' . $obj->Title . '" path="' . BASE_URL . $obj->getFolderPath ( ) . '/' . $obj->Filename . '" width="' . $obj->Width . '" height="' . $obj->Height . '"/>' . "\n";
			}
		}
	}
	$str .= '</element>';
}

/***********************************************************************************
* Renderers for objects                                                            *
***********************************************************************************/

function rcFile ( $file )
{
	$str .= "\n";
	$str .= '<div class="File ' . texttourl ( $file->Title ) . '">' . "\n";
	
	list ( , $ext ) = explode ( '.', $file->Filename );
	
	switch ( $ext )
	{
		case 'swf':
			if ( !$file->Width )
			{
				list ( $width, $height, ) = getimagesize ( BASE_DIR . '/upload/' . $file->Filename );
			}
			else 
			{
				$width = $file->Width;
				$height = $file->Height;
			}
			if ( $file->Variables )
			{
				$out = explode ( '&', $file->Variables );
				foreach ( $out as $o )
				{
					$o = explode ( '=', $o );
					$vars[] = $o[ 0 ] . '=' . urlencode ( $o[ 1 ] );
				}
				$vars = '?' . implode ( '&', $vars );
			}
			else $vars = '';
			
			$randid = $file->DivID ? $file->DivID : ( 'id' . rand(0,999) . rand(0,999) . rand(0,999) . rand(0,999) );
			$bg = ( $file->Background ? $file->Background : '#ffffff' );
			$id = ' id="' . $randid . '"';
			$furl = BASE_URL . 'upload/' . $file->Filename . $vars;
			
			$extra = '';
			if ( !strstr ( $_SERVER[ 'HTTP_USER_AGENT' ], 'MSIE' ) )
				$extra = ' wmode="transparent"';
				
			$str .= '
				<object' . $id . ' width="' . $width . '" height="' . $height . '" type="application/x-shockwave-flash" data="' . $furl . '"' . $extra . '>
					<param name="movie" value="' . $furl . '"/>
					<param name="wmode" value="transparent"/>
				</object>
			';
			break;
		default:
			$str .= '
				<a href="' . BASE_URL . 'upload/' . $file->Filename . '">' . $file->Title . '</a>
			';
			break;
	}
	
	$str .= '</div>' . "\n";
	return $str;
}

function rcImage ( $image )
{
	$str .= "\n";
	$str .= '<div class="Image ' . texttourl ( $image->Title ) . '">' . "\n";
	$str .= '<img src="' . $image->getFolderPath ( ) . '/' . $image->Filename . '" id="' . $image->ID . '"/>';
	$str .= '</div>' . "\n";
	return $str;
}

function rcContentElement ( $object )
{
	$object = new dbContent ( $object->ID );
	$str .= "\n";
	$str .= '<div class="ContentElement ' . $object->RouteName . '">' . "\n";
	$str .= '<div class="Title">' . $object->Title . '</div>';
	$str .= '<div class="DateModified">' . $object->DateModified . '</div>';
	$str .= '<div class="ContentElementID">' . $object->ID . '</div>';
	$str .= '<div class="Link">' . $object->getUrl ( ) . '</div>';
	$str .= '<div class="RouteName">' . $object->RouteName . '</div>';
	$str .= '<div class="MenuTitle">' . $object->MenuTitle . '</div>';
	$str .= '</div>' . "\n";
	return $str;
}

function rcFolder ( $folder )
{
	$str = "\n";
	$str .= '<div class="Folder ' . $folder->Name . '">' . "\n";
	if ( $files = $folder->getFiles ( ) )
	{
		foreach ( $files as $file )
		{
			$obj = dbObject::create ( $file->Type, $file->_table->database, $file->ID );
			if ( strtolower ( get_class ( $obj ) ) == 'dbimage' )
			{
				$str .= '<img src="' . BASE_URL . $obj->getFolderPath ( ) . '/' . $obj->Filename . '" width="' . $obj->Width . '" height="' . $obj->Height . '" id="image_' . $obj->ID . '" title="' . str_replace ( '"', '`', $obj->Title ) . '" description="' . str_replace ( "\r", '', $obj->Description ) . '"/>' . "\n";
				if ( trim ( $obj->Description ) )
					$str .= '<p>' . $obj->Description . '</p>';
			}
			else if ( strtolower ( get_class ( $obj ) ) == 'dbfile' )
			{
				$str .= '<a href="' . BASE_URL . $obj->getFolderPath ( ) . '/' . $obj->Filename . '">' . $obj->Title . '</a>';
			}
		}
	}
	$str .= '</div>' . "\n";
	return $str;
}

function rcGeneric ( $generic )
{
	if ( file_exists ( 'templates/rc_' . $generic->_tableName . '.php' ) )
	{
		$str = new cPTemplate ( 'templates/rc_' . $generic->_tableName . '.php' );
		$str->object =& $generic;
		$str = $str->render ( );
	}
	else
	{
		$str = "\n";
		
		$str .= '<div class="Generic ' . $generic->getIdentifier ( ) . '">' . "\n";
		$str .= "\t";
		foreach ( $generic->_table->getFieldNames () as $field )
		{
			$str .= '<div type="' . $field . '">' . $generic->$field . '</div>';
		}
		$str .= '</div>' . "\n";
		
		$str .= "\n";
	}
	return $str;
}

function rcGroups ( $group )
{
	$str = "\n";
	$str .= '<div class="Group ' . $group->Name . '">' . "\n";
	$obj = new dbObject ( 'Users' );
	if ( $objs = $obj->find ( '
		SELECT * FROM Users u, UsersGroups ug WHERE ug.UserID = u.ID AND ug.GroupID = ' . $group->ID . ' ORDER BY u.Name ASC
	' ) )
	{
		foreach ( $objs as $obj )
		{
			$str .= rcUser ( $obj );
		}
	}
	$str .= '</div>' . "\n";
	return $str;
}

function rcUser ( $user )
{
	$str .= "\n";
	$str .= '<div class="User ' . texttourl ( $user->Name ) . '">' . "\n";
	$str .= "\t" . '<div class="Name">' . $user->Name . '</div>' . "\n";
	$str .= "\t" . '<div class="Email">' . "\n\t\t" . '<a href="mailto:' . $user->Email . '">' . $user->Email . '</a>' . "\n" . '</div>' . "\n";
	$str .= "\t" . '<div class="Telephone">' . $user->Telephone . '</div>' . "\n";
	$str .= "\t" . '<div class="Mobile">' . $user->Mobile . '</div>' . "\n";
	$str .= "\t" . '<div class="Address">' . $user->Address . '</div>' . "\n";
	$str .= "\t" . '<div class="Postcode">' . $user->Postcode . '</div>' . "\n";
	$str .= "\t" . '<div class="City">' . $user->City . '</div>' . "\n";
	$str .= "\t" . '<div class="Country">' . $user->Country . '</div>' . "\n";
	
	$photo = '';
	if ( $user->Image )
	{
		if ( $photo = new dbImage ( $user->Image ) )
		{
			$photo = "\n\t\t" . '<img src="' . BASE_URL . $photo->getFolderPath ( ) . '/' . $photo->Filename . '" id="image_' . $photo->ID . '"/>' . "\n";
		}
	}
	$str .= "\t" . '<div class="Photo">' . $photo . '</div>' . "\n";
	
	$user->loadExtraFields ( );
	foreach ( $user as $k=>$v )
	{
		if ( substr ( $k, 0, 7 ) == '_extra_' )
		{
			$key = str_replace ( '_extra_', '', $k );
			$str .= "\t" . '<div class="' . $key . '">' . $v . '</div>' . "\n";
		}
	}
	
	$str .= '</div>' . "\n";
	
	return $str;
}


/***********************************************************************************
* Render objects by classname or table name                                        *
***********************************************************************************/
function renderObject ( $object )
{
	switch ( strtolower ( get_class ( $object ) ) )
	{
		case 'dbimage':
			return rcImage ( $object );
		case 'dbfile':
			return rcFile ( $object );
		case 'dbfolder':
			return rcFolder ( $object );
		case 'dbobject':
		default:
			switch ( $object->_tableName )
			{
				case 'ContentElement':
					return rcContentElement ( $object );
				case 'Groups':
					return rcGroups ( $object );
				case 'File':
					return rcFile ( $object );
				default:
					return rcGeneric ( $object );
					break;
			}
			break;
	}
}

/***********************************************************************************
* Interpret objects by classname or table name                                     *
***********************************************************************************/
function interpretObject ( $object )
{
	switch ( strtolower ( get_class ( $object ) ) )
	{
		case 'dbfolder':
			return icFolder ( $object );
		case 'dbobject':
		default:
			switch ( $object->_tableName )
			{
				default:	
					break;
			}
			break;
	}
}
?>
