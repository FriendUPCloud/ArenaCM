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
		<div class="Container" style="padding: 2px">
			<div class="SubContainer" id="mod_search_content">
				<table class="LayoutColumns">
					<tr>
						<td>
							<table class="LayoutColumns">
								<tr>
									<th style="font-weight: bold; width: 90px; height: 24px">Ord:</th><th style="font-weight: bold;">Vist tekst:</th>
								</tr>
								<tr>
									<td style="padding-top: 4px">Søkeheading:</td><td><input type="text" id="mod_search_heading" value="<?= $this->search_heading ?>"><div class="SpacerSmall"></div></td>
								</tr>
								<tr>
									<td style="padding-top: 4px">Søkeord tekst:</td><td><input type="text" id="mod_search_keywords" value="<?= $this->search_keywords ?>"><div class="SpacerSmall"></div></td>
								</tr>
								<tr>
									<td style="padding-top: 4px">Søk i nettsiden:</td><td><input type="text" id="mod_search_webpage" value="<?= $this->search_webpage ?>"><div class="SpacerSmall"></div></td>
								</tr>
							</table>
						</td>
						<td style="padding-left: <?= MarginSize ?>px">
							<p>
								<strong>Søkevalg for ekstensjoner:</strong>
							</p>
							<?
								
								$settings = new dbObject ( 'Setting' );
								$settings->SettingType = 'ContentModule';
								$settings->addClause ( 'ORDER BY', '`Key` ASC' );
								$exts = explode ( '|', $this->search_extensions );
								
								$str = '<option style="padding: 3px" value="default"' . ( in_array ( 'default', $exts ) ? ' selected="selected"' : '' ) . '>Standard</option>';
								
								if ( $settings = $settings->find ( ) )
								{
									foreach ( $settings as $setting )
									{
										if ( !file_exists ( 'lib/skeleton/modules/' . $setting->Key . '/info.txt' ) )
											continue;
										if ( !file_exists ( 'lib/skeleton/modules/' . $setting->Key . '/websearch.php' ) )
											continue;
										$data = explode ( '|', file_get_contents ( 'lib/skeleton/modules/' . $setting->Key . '/info.txt' ) );
										if ( in_array ( $setting->Key, $exts ) )
											$s = ' selected="selected"';
										else $s = '';
										$str .= '<option style="padding: 3px" value="' . $setting->Key . '"' . $s . '>' . $data[0] . '</option>';
									}
								}
								if ( $dir = opendir ( 'extensions' ) )
								{
									while ( $file = readdir ( $dir ) )
									{
										if ( $file{0} == '.' ) continue;
										if ( !file_exists ( 'extensions/' . $file . '/websearch.php' ) ) continue;
										if ( file_exists ( 'extensions/' . $file . '/info.csv' ) )									
											$data = explode ( '|', file_get_contents ( 'extensions/' . $file . '/info.csv' ) );
										else $data = array ( $file, $file );
										if ( in_array ( $file, $exts ) )
											$s = ' selected="selected"';
										else $s = '';
										$str .= '<option style="padding: 3px" value="' . $file . '"' . $s . '>' . $data[0] . '</option>';
									}
								}
								if ( strlen ( $str ) > 0 )
									return '<select style="padding: 0; -moz-box-sizing: border-box; box-sizing: border-box; width: 100%;" id="mod_search_extensions" size="5" multiple="multiple">' . $str . '</select>';
								return '<p>Ingen av ekstensjonene støtter søk.</p>';
							?>
							<table width="100%" cellspacing="0" cellpadding="0">
								<tr>
									<td valign="top">
										<p>
											<strong>List output i felt:</strong>
										</p>
										<p>
											<select id="mod_search_replacefield"><?= $this->search_replacefield ?></select>
										</p>
									</td>
									<td>&nbsp;&nbsp;</td>
									<td valign="top">
										<p>
											<strong>Målside for søkeresultater:</strong>
										</p>
										<p>
											<select id="mod_search_outputpage"><option value="0">Standard</option><option value="0">===================</option><?= $this->search_outputpage ?></select>
										</p>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="SpacerSmall"></div>
		
		<script type="text/javascript">
			AddSaveFunction ( function ( )
			{
				var out = new Array ( );
				if ( document.getElementById ( 'mod_search_extensions' ) )
				{
					if ( document.getElementById ( 'mod_search_extensions' ).options )
					{
						var sels = document.getElementById ( 'mod_search_extensions' ).options;
						if ( sels && sels.length )
						{
							for ( var a = 0; a < sels.length; a++ )
							{
								if ( sels[ a ].selected )
									out.push ( sels[ a ].value );
							}
						}
					}
				}
			
				var sjax = new bajax ( );
				sjax.openUrl ( ACTION_URL + 'mod=mod_search&modaction=save', 'post', true );
				sjax.addVar ( 'heading', document.getElementById ( 'mod_search_heading' ).value );
				sjax.addVar ( 'keywords', document.getElementById ( 'mod_search_keywords' ).value );
				sjax.addVar ( 'webpage', document.getElementById ( 'mod_search_webpage' ).value );
				sjax.addVar ( 'extensions', out.join ( "|" ) );
				sjax.addVar ( 'replacefield', document.getElementById ( 'mod_search_replacefield' ).value );
				sjax.addVar ( 'outputpage', document.getElementById ( 'mod_search_outputpage' ).value );
				sjax.onload = function ( )
				{
					document.getElementById ( 'mod_search_content' ).style.background = 'none';
				}
				document.getElementById ( 'mod_search_content' ).style.background = '#468';
				sjax.send ( );
			} );
		</script>

