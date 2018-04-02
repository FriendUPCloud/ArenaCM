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
<?if( is_array( $this->contents ) ){?>
	<?
		$o = '';
		
		$countListed = 0;
		$startat = intval( $_REQUEST[ 'pos' ] > 0 ) ? $_REQUEST[ 'pos' ] : 0;
		$stopat = $startat + LIBRARY_ITEMSPERPAGE;
		
		if ( is_array ( $this->contents[ 'all' ] ) )
		{
			$tpli = new cPTemplate( 'admin/modules/library/templates/listed_image.php' );
			$tplf = new cPTemplate( 'admin/modules/library/templates/listed_file.php' );
			foreach( $this->contents['all'] as $mixed )
			{
				if( $countListed < $startat ) { $countListed++; continue; }
				if( $countListed >= $stopat ) break;
			
				if ( $mixed->Type == 'Image' )
				{
					$i = new dbImage ( $mixed->ID );
					$tpli->image = $i;
					$o .= $tpli->render();
				}
				else 
				{
					$f = new dbFile ( $mixed->ID );
					$tplf->tfile = $f;
					$o .= $tplf->render ();
				}
				$countListed++;
			}
		}
		else
		{
			if( is_array( $this->contents['images'] ) && count( $this->contents['images'] ) > 0 )
			{
				$tpl = new cPTemplate( 'admin/modules/library/templates/listed_image.php' );
				foreach( $this->contents['images'] as $image )
				{
					if( $countListed < $startat ) { $countListed++; continue; }
					if( $countListed >= $stopat ) break;
				
					$tpl->image = $image;
					$o .= $tpl->render();
					$countListed++;
				}
			}	
			else if( !is_array( $this->contents['images'] ) ) $this->contents['images'] = array();
		
			if( is_array( $this->contents['files'] ) && count( $this->contents['files'] ) > 0 && $countListed < $stopat )
			{
				$tpl = new cPTemplate( 'admin/modules/library/templates/listed_file.php' );
				foreach( $this->contents['files'] as $file )
				{
					if( $countListed < $startat ) { $countListed++; continue; }
					if( $countListed >= $stopat ) break;
				
					$tpl->tfile = $file;
					$o.= $tpl->render();
					$countListed++;
				}
			}
			else if(  !is_array( $this->contents['files'] ) ) $this->contents['files'] = array();
		}
		
		$btn = '';
		
		// pageination
		$totalCount = $this->contents[ 'all' ] ? count ( $this->contents[ 'all' ] ) : 
			( count ( $this->contents['images'] ) + count ( $this->contents['files'] ) );
		if( $totalCount > 10 ) 
		{
			$cp = new cPagination();
			$cp->Count = $totalCount;
			$cp->Position = $startat;
			$cp->Limit = LIBRARY_ITEMSPERPAGE;
			$cp->Template = 'admin/modules/library/templates/pagination.php';
			$btn .= $cp->render ( );
		}
		
		$this->pagination = $btn;
		
		return $o;
	?>
	<div class="SpacerSmall"></div>
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<?= $this->pagination ? ( '<td style="white-space: nowrap; padding-right: 2px">' . $this->pagination . '</td>' ) : '' ?>
			<td>
				<div class="SubContainer" style="height: 24px; padding-top: 15px">
					<?= i18n ( 'Folder' ) ?> "<?= $this->folder->Name ?>" <?= i18n ( 'contains' ) ?> <?= count( $this->contents['images'] )?> bilde(r) og <?= count( $this->contents['files'] )?> fil(er).
				</div>
			</td>
		</tr>
	</table>
<?}?>
<?if( !is_array( $this->contents ) ){?>
	<h1>Teknisk feil. Kontakt din leverandør!</h1>
<?}?>
