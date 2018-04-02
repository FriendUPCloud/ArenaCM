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
							<script type="text/javascript">
								var ContentType = '<?= $this->ContentType ?>';
								var ContentID = '<?= $this->ContentID ?>';
							</script>
							
							<h2>
								Ekstrafelter:
							</h2>
							
							<div id="ExtraFieldListing"><?= $this->extraFields ?></div>

							<div class="Spacer"><em></em></div>
							<div class="SubContainer" id="ExtraFields">
								<table class="Layout">
									<tr>
										<th style="padding-right: <?= MarginSize ?>px">
											<strong>Felt ID:</strong>
										</th>
										<th style="padding-right: <?= MarginSize ?>px">
											<input type="text" id="FieldName" value="" size="16" />
										</th>
										<th style="padding-right: <?= MarginSize ?>px">
											<strong>Felttype:</strong>
										</th>
										<th style="padding-right: <?= MarginSize ?>px">
											<select id="FieldType">
												<option value="varchar">Setning</option>
												<option value="text">Artikkel</option>
												<option value="leadin">Kort tekst</option>
												<option value="file">Fil</option>
												<option value="image">Bilde</option>
												<option value="objectconnection">Objekttilkoblingsfelt</option>
												<option value="pagelisting">Sideutlisting</option>
												<option value="newscategory">Nyhetskategori</option>
												<option value="script">Javascript</option>
												<option value="style">Stilark</option>
												<option value="formprocessor">Skjema prosessering</option>
												<option value="extension">Utvidelse</option>
											</select>
										</th>
										<th style="padding-right: <?= MarginSize ?>px">
											<button type="button" onclick="addExtraField ( )">
												<img src="admin/gfx/icons/table_row_insert.png" /> Legg til feltet
											</button>
										</th>
									</tr>
								</table>
							</div>
							
							<?if ( !$this->Ajax ) { ?>
							<script type="text/javascript" src="lib/plugins/extrafields/javascript/main.js"></script>
							<?}?>
