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
			<form method="post" action="<?= $this->content->getUrl ( ) ?>" name="searchform">
				<div class="Header">
					<h2><?= $this->header ? $this->header : i18n ( $this->search_heading ) ?></h2>
				</div>
				<div class="SearchBox Block">
					<p class="Searching">
						<label class="Keywords"><?= i18n ( $this->search_keywords ) ?>:</label>
						<input type="text" class="SearchInput" name="keywords" value="<?= htmlentities( $_REQUEST[ 'keywords' ] ) ?>" placeholder="<?= i18n ( $this->search_webpage ) ?>"/>
					</p>
					<p class="Buttons">
						<button type="submit" onclick="">
							<span><?= i18n ( $this->search_webpage ) ?></span>
						</button>
						<?if ( $this->search_extensions ) { ?>
						<select name="search_extension">
							<option value="">Søk i alt</option>
							<?
								if ( $keys = explode ( '|', $this->search_extensions ) )
								{
									$options = '<option value="texts">i tekstsider</option>';
									foreach ( $keys as $key )
									{
										if ( file_exists ( 'lib/skeleton/modules/' . $key . '/info.txt' ) )
											$info = explode ( '|', file_get_contents ( 'lib/skeleton/modules/' . $key . '/info.txt' ) );
										else if ( file_exists ( 'extensions/' . $key . '/info.csv' ) )
											$info = explode ( '|', file_get_contents ( 'extensions/' . $key . '/info.csv' ) );
										else continue;
										$options .= '<option value="' . $key . '">' . i18n ( 'search in ' . $info[0] ) . '</option>';
									}
									return $options;
								}
							?>
						</select>
						<?}?>
					</p>
				</div>
			</form>
