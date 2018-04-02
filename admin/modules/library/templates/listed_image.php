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
		<div class="Container Imagecontainer"
			onclick="toggleSelectedImage ( this )"
			ondblclick="editLibraryImage( '<?= $this->image->ID?>' )" 
			id="imagecontainer<?= $this->image->ID?>"
			onmousedown="dragger.startDrag ( this.getElementsByTagName ( 'img' )[0], { pickup: 'clone', objectType: 'Image', objectID: '<?= $this->image->ID?>' } ); return false"
		>
			
				<div style="position: relative; width: 120px; height: 100px; overflow: hidden">
					<img src="<? $this->image->setBackgroundColor ( 0xffffff ); return $this->image->getImageUrl( 120, 100, 'centered' ); ?>">
					<span style="position: absolute; top: 0px; left: 0px; width: 120px; height: 100px; z-index: 2; background: #fff">
					</span>
				</div>
				<h4><?= strlen( $this->image->Title ) < 12 ? $this->image->Title : trimtext( $this->image->Title, 12 ) ?></h4>
			
			
		</div>
		<script>
			addToolTip( '<?= $this->image->Title ?>','<?= trim( $this->image->Description ) != '' ? ( ( '<b>' . i18n ( 'i18n_description' ) . ':</b><br />' ) . str_replace ( array ( getLn ( 'windows' ), getLn ( ), getLn ( 'r' ) ), '', addslashes ( nl2br( $this->image->Description ) ) ) . '<hr />' ) : ''?><b><?= i18n ( 'i18n_filename' ) ?>:</b> <?= nl2br ( trim ( $this->image->Filename ) ) ?><br /><b><?= i18n ( 'i18n_filesize' ) ?>:</b> <?= filesizeToHuman( $this->image->Filesize ); ?>', 'imagecontainer<?= $this->image->ID?>' );
			setOpacity ( document.getElementById ( 'imagecontainer<?= $this->image->ID?>' ).getElementsByTagName ( 'span' )[0], 0 );
		</script>	
