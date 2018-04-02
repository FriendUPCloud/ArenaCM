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

The Initial Developer of the Original Code is Blest AS.
Portions created by Blest AS are Copyright (C) 2004-2010
Blest AS. All Rights Reserved.

Contributor(s): Hogne Titlestad, Thomas Wollburg, Inge Jørgensen, Ola Jensen, 
Rune Nilssen
*******************************************************************************/

$i = new dbImage ( $_REQUEST[ 'iid' ] );
die ( '
		<div id="SliceUI">
			<div id="SliceToolbar">
				<table>
					<tr>
						<td>
							<button type="button" onclick="setTool(\'select\')">
								<img src="admin/gfx/icons/cursor.png"/>
							</button>
						</td>
						<td>
							<button type="button" onclick="zoomSlice(\'in\')">
								<img src="admin/gfx/icons/zoom_in.png"/>
							</button>
						</td>
						<td>
							<button type="button" onclick="zoomSlice(\'out\')">
								<img src="admin/gfx/icons/zoom_out.png"/>
							</button>							
						</td>
					</tr>
					<tr>
					</tr>
				</table>
				<p>
					<button type="button">
								<img src="admin/gfx/icons/disk.png"/> Lagre utsnitt
					</button>
					<button type="button">
						<img src="admin/gfx/icons/arrow_refresh.png"/> Nullstill
					</button>
					<button type="button" onclick="removeModalDialogue(\'slice\')">
						<img src="admin/gfx/icons/cancel.png"/> Avbryt
					</button>
				</p>
			</div>
			<div id="SliceImage">
				<img src="upload/images-master/' . $i->Filename . '"/>
			</div>
			<div id="SliceTL"></div>
			<div id="SliceTM"></div>
			<div id="SliceTR"></div>
			<div id="SliceML"></div>
			<div id="SliceMM"></div>
			<div id="SliceMR"></div>
			<div id="SliceBL"></div>
			<div id="SliceBM"></div>
			<div id="SliceBR"></div>
			<div id="SliceTop"></div>
			<div id="SliceLeft"></div>
			<div id="SliceRight"></div>
			<div id="SliceBottom"></div>
			<div id="SliceRect"></div>
		</div>
' );

?>
