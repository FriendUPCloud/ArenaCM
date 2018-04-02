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
					<?if ( $this->Prev ) { ?>
					<button type="button" onclick="document.location='<?= $this->Target ?><?= $this->obj->PositionVariable ?>=<?= $this->Position - $this->Limit ?><?= $this->ExtraUrlData ?>'">
						<img src="admin/gfx/icons/arrow_left.png" /> <?= i18n ( 'Previous page' ) ?>
					</button>
					<?}?>
					
					<?if ( $this->Select ) { ?>
					<select onchange="document.location = '<?= $this->Target ?><?= $this->obj->PositionVariable ?>=' + this.value<?= $this->ExtraUrlData ? ( " + '" . $this->ExtraUrlData . "'" ) : '' ?>">
						<?= $this->Select ?>
					</select>
					<?}?>
					
					<?if ( $this->Next ) { ?>
					<button type="button" onclick="document.location='<?= $this->Target ?><?= $this->obj->PositionVariable ?>=<?= $this->Position + $this->Limit ?><?= $this->ExtraUrlData ?>'">
						<?= i18n ( 'Next page' ) ?> <img src="admin/gfx/icons/arrow_right.png" />
					</button>
					<?}?>
					
					<?if ( $this->PageCount > 1 ) { ?>
					<div class="SubContainer" style="display: inline">
						<?= i18n ( 'Showing page' ) ?> <?= $this->CurrentPage ?> <?= i18n ( 'of' ) ?> <?= $this->PageCount ?>
					<?}?>
					
						<?if ( $this->ShowCount ) { ?>
						<div style="display: inline">
							<?= ( $this->PageCount <= 1 ? ( i18n ( 'Showing' ) . ' ' . $this->Count . ' ' . ( i18n ( 'elements' ) . '.' ) ) :  
								( i18n ( 'with' ) . ' ' . ( $this->Count ? ( $this->Count . ' ' ) : '' ) . i18n ( 'elements total' ) . '.' ) ) ?>
						</div>
						<?}?>
						
					<?if ( $this->PageCount > 1 ) { ?>
					</div>
					<?}?>
