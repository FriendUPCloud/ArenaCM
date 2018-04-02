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
<div id="libaryPagination">
	
	<button <?= $this->Prev ? '' : ' style="visibility: hidden; position: absolute;" '; ?> type="button" onclick="showLibraryContent( '<?= $this->Position - $this->Limit ?>' )">
		<img src="admin/gfx/icons/arrow_left.png" /> Forrige side
	</button>

	<button <?= $this->Next ? '' : ' style="visibility: hidden; position: absolute;" '; ?> type="button" onclick="showLibraryContent( ' <?= $this->Position + $this->Limit  ?>')">
		Neste side <img src="admin/gfx/icons/arrow_right.png" />
	</button>
	
	<?if ( $this->PageCount > 1 ) { ?>
	<div class="Container" style="display: inline">
		Viser side <?= $this->CurrentPage ?> av <?= $this->PageCount ?>.
	</div>
	<?}?>&nbsp;&nbsp;
</div>
