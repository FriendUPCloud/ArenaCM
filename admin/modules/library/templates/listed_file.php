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
		<div class="Container Imagecontainer"
			onclick="toggleSelectedImage ( this )" 
			ondblclick="editLibraryFile( '<?= $this->tfile->ID?>' )" 
			id="tfilecontainer<?= $this->tfile->ID?>"
			onmousedown="dragger.startDrag ( this, { pickup: 'clone', objectType: 'File', objectID: '<?= $this->tfile->ID?>' } ); return false"
		>
			<?= $this->tfile->getIcon( ' class="filelisticon"' ); ?>
			<h4 <?= trim( $this->tfile->Description ) == '' ? ' title="'.$this->tfile->Title.'"' : '';?>><?= strlen( $this->tfile->Title ) < 50 ? $this->tfile->Title : trimtext( $this->tfile->Title, 50 ); ?></h4>
			
			<?if( trim( $this->tfile->Description ) == '' ){?>
			<h5>Filenavn:</h5><span title="<?= $this->tfile->Filename?>"><?= strlen( $this->tfile->Filename ) > 15 ? trimtext( $this->tfile->Filename, 15 ) : $this->tfile->Filename; ?></span><br />
			<h5>Størrelse:</h5><?= filesizeToHuman( $this->tfile->Filesize ); ?>
			<?}?>
			<h5>Sist oppdatert:</h5><?
				$now = date ( 'Ymd' );
				$filed = date ( 'Ymd', strtotime ( $this->tfile->DateModified ) );
				if ( $filed == $now )
				{
					$nh = date ( 'H' );
					$fh = date ( 'H', strtotime ( $this->tfile->DateModified ) );
					if ( $nh == $fh )
					{
						$nm = date ( 'i' );
						$fm = date ( 'i', strtotime ( $this->tfile->DateModified ) );
						if ( $nm == $fm )
						{
							$ns = date ( 's' );
							$fs = date ( 's', strtotime ( $this->tfile->DateModified ) );
							$span = $ns - $fs;
							if ( $span == 0 )
							{
								return 'nå';
							}
							return (  $span . ( $span == 1 ? ' sekund' : ' sekunder' ) . ' siden' );
						}
						$span = $nm - $fm;
						return ( $span . ( $span == 1 ? ' minutt' : ' minutter' ) . ' siden' );
					}
					$span = $nh - $fh;
					return ( $span . ( $span == 1 ? ' time' : ' timer' ) . ' siden' );
				}
				else
				{
					return date ( 'd/m/Y', strtotime ( $this->tfile->DateModified ) );
				}
			?>
		</div>
		<?if( trim( $this->tfile->Description ) != '' ){?>
		<script>
			addToolTip( '<?= $this->tfile->Title?>','<b>Beskrivelse:</b><br /><?= str_replace ( array ( getLn ( 'windows' ), getLn ( ), getLn ( 'r' ) ), '', addslashes ( nl2br( $this->tfile->Description ) ) ); ?><hr /><b>Filenavn:</b><br /><?= strlen( $this->tfile->Filename ) > 25 ? substr( $this->tfile->Filename,0,25 ) . '<br />' . substr( $this->tfile->Filename,25,50 ) : $this->tfile->Filename;  ?><br /><b>Størrelse</b>: <?= filesizeToHuman( $this->tfile->Filesize ); ?>', 'tfilecontainer<?= $this->tfile->ID?>' );
		</script>	
		<?}?>
