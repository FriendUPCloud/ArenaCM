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
	<?
		$this->name = "Extra_{$this->data->ID}_Small_DataString";
	?>
	<div class="SpacerSmall"></div>
	<strong><?= $this->data->Name ?>:</strong>
	<div class="SpacerSmall"></div>
	<?
		if ( file_exists ( 'extensions/' . $this->data->DataString . '/templates/websnippetconfig.php' ) )
		{
			$tpl = new cPTemplate ( 'extensions/' . $this->data->DataString . '/templates/websnippetconfig.php' );
			$tpl->data =& $this->data;
			$tpl->content =& $this->content;
			$tpl->page =& $this->page;
			return $tpl->render ( );
		}
		else return 'Ekstensjonen har ingen instillinger.';
	?>
	<div class="SpacerSmall"></div>



