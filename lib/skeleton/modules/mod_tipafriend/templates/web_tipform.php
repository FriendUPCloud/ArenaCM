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

	<div class="Block TipAFriend">
		<h2>
			Tips en venn!
		</h2> 
		
		<p>
			Tips en venn om siden:<br/>
			&nbsp;&nbsp;"<?= $this->content->MenuTitle ?>"
		</p>
	
		<p>
			Ditt navn:
		</p>
		<p>
			<input type="text" value="" size="45" id="mod_tipafriend_name"/>
		</p>
		<p>
			Skriv inn e-post adresse til mottaker:
		</p>
		<p>
			<input type="text" value="" size="45" id="mod_tipafriend_email"/>
		</p>
		<p>
			Skriv en beskjed:
		</p>
		<p>
			<textarea rows="5" cols="46" id="mod_tipafriend_message"></textarea>
		</p>
		<p>
			<button type="button" onclick="mod_tipafriend_send ( )">
				Send tipset
			</button>
			<button type="button" onclick="closeStyledDialog ( )">Lukk vinduet</button>
		</p>
	</div>


