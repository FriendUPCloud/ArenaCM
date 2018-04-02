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
	$GLOBALS[ 'simpleSetting' ]++;
	if ( $GLOBALS[ 'simpleSetting' ] > 1 )
		return '<div class="SpacerSmall"></div>';
?>

<p>
	<strong><label for="setf<?= $this->Keystring?>"><?= $this->Title ?></label></strong>
</p>
<p>
<?if ( !$this->_FieldType || $this->_FieldType == 'varchar' ) { ?>
	<input 
		id="setf<?= $this->Keystring?>" 
		type="text" 
		name="<?= $this->Keystring ?>" 
		value="" 
		size="40" 
		style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box" 
		class="modulesetting"
	/>
	<div class="Hidden" id="setf<?= $this->Keystring ?>_div"><?= $this->Value ?></div>
	<script type="text/javascript">
		document.getElementById ( 'setf<?= $this->Keystring ?>' ).value = document.getElementById ( 'setf<?= $this->Keystring ?>_div' ).innerHTML;
	</script>
<?}?>
<?if ( $this->_FieldType == 'text' ) { ?>
	<textarea
		id="setf<?= $this->Keystring?>" 
		name="<?= $this->Keystring ?>" 
		style="width: 100%; box-sizing: border-box; -moz-box-sizing: border-box" 
		class="modulesetting"
		rows="10"
	><?= $this->Value ?></textarea>
<?}?>
</p>
<div class="SpacerSmall"></div>

