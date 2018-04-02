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
New code is (C) 2011 Idéverket AS, 2015 Friend Studios AS

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
                Rune Nilssen
*******************************************************************************/


?>
	<?if ( trim ( $this->settings->Heading ) ) { ?>
	<h1 class="Heading"><?= trim ( $this->settings->Heading ) ?></h1>
	<?}?>
	<?
		if ( $folders = explode ( ':', $this->settings->Folders ) )
		{
			$str = '';
			foreach ( $folders as $fid )
			{
				$img = new dbImage ();
				if ( $this->settings->SortMode == 'listmode_sortorder' )
				{
					$img->addClause ( 'ORDER BY', 'SortOrder ASC' );
				}
				else if ( $this->settings->SortMode == 'listmode_fromto' )
				{
					$img->addClause ( 'WHERE', 'DateFrom <= NOW() AND DateTo >= NOW()' );
					$img->addClause ( 'ORDER BY', 'SortOrder ASC' );
				}
				else
				{
					$img->addClause ( 'ORDER BY', 'DateModified DESC' );
				}
				$img->addClause ( 'WHERE', 'ImageFolder=' . $fid );
				if ( $images = $img->find ( ) )
				{
					foreach ( $images as $i )
					{
						$i->Description = stripslashes( str_replace ( array ( '#--quote--#', '"' ), array ( '', '#--quote--#' ), decodeArenaHTML ( $i->Description ) ) );
						$str .= '<img src="';
						$str .= $i->getImageUrl ( $this->settings->Width, $this->settings->Height, 'framed' );
						$str .= '" alt="' . str_replace ( array ( "\\n", "\\r" ), array ( "<br/>", "" ), $i->Description ) . '" title="' . $i->Title . '" tags="' . $i->Tags . '"/>';
					}
				}
			}
			return $str;
		}
		return '';
	?>
	<script type="text/javascript">
		var gal = new arenaGallery ();
		gal.galleryWidth = <?= $this->settings->Width > 0 ? $this->settings->Width : 'false' ?>;
		gal.galleryHeight = <?= $this->settings->Height > 0 ? $this->settings->Height : 'false' ?>;
		gal.galleryAnimated = <?= $this->settings->Animated == 1 ? '1' : '0' ?>;
		gal.galleryShowStyle = '<?= $this->settings->ShowStyle ?>';
		gal.galleryPause = <?= $this->settings->Pause >= 1 ? $this->settings->Pause : '2' ?>;
		gal.gallerySpeed = <?= $this->settings->Speed >= 1 ? ($this->settings->Speed*100) : '200' ?>;
		gal.init ( '<?= $this->field->Name ?>' );
	</script>

