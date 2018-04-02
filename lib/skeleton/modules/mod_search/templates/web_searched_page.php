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
	<div class="Block">
		<p class="Title">
			<a href="<?= $this->data->Url ?>"><span><?= $this->data->Title ?></span></a>
		</p>
		<p class="Leadin">
			<?= $this->data->Leadin ?>
		</p>
		<p class="ReadMore">
			<a href="<?= $this->data->Url ?>"><span><?= i18n ( 'Read more' ) ?></span></a>
		</p>
	</div>
