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
					<?if ( $this->Prev && $this->Next ) { ?>
					<div class="PaginationSeparator"><em></em></div>
					<?}?>

					<?if ( $this->Prev ) { ?>
					<a href="<?= $this->content->getUrl ( ) ?>?newspos=<?= $this->Position - $this->Limit ?>">
						<?= i18n ( 'Previous page' ) ?>
					</a>
					<?}?>
					
					<?if ( $this->Prev && $this->Next ) { ?>
						|
					<?}?>
					
					<?if ( $this->Next ) { ?>
					<a href="<?= $this->content->getUrl ( ) ?>?newspos=<?= $this->Position + $this->Limit ?>">
						<?= i18n ( 'Next page' ) ?>
					</a>
					<?}?>
					
					<?if ( ( $this->Next || $this->Prev ) && $this->PageCount > 1 ) { ?>
						|
					<?}?>
					
					<?if ( $this->PageCount > 1 ) { ?>
					<div style="display: inline">
						<?= i18n ( 'Showing page' ) ?> <?= $this->CurrentPage ?> <?= i18n ( 'of' ) ?> <?= $this->PageCount ?>.
					</div>
					<?}?>
