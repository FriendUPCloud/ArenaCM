<?php
/* @ExportLicense MPL ------------------------------------------------------- */
?><!DOCTYPE html>
<html>
	<head>
		<title>
			<?= 
				$this->Title ? 
				preg_replace ( '/(ARENACM v[0-9]*)/i', 'ARENA Content Management v' . ARENA_VERSION, $this->Title ) : 
				'ARENA Content Management ' . ARENA_VERSION 
			?>
		</title>
		<meta http-equiv="imagetoolbar" content="no" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
		<meta http-equiv="expires" content="<?= date ( 'D, d M Y H:i:s', time ( ) - 72000 ) ?> GMT" />
		<base href="<?= BASE_URL ?>" />
		<?if ( GetSettingValue ( 'ARENA_Usersettings_' . $GLOBALS[ 'Session' ]->AdminUser->ID, 'ResourceFriendlyCSS' ) == '1' ) { ?>
		<link rel="stylesheet" href="admin/css_light/admin.css" />
		<?}?>
		<?if ( GetSettingValue ( 'ARENA_Usersettings_' . $GLOBALS[ 'Session' ]->AdminUser->ID, 'ResourceFriendlyCSS' ) != '1' ) { ?>
		<link rel="stylesheet" href="admin/css/admin.css" />
		<?}?>
		<?if ( file_exists ( 'upload/adminstyles.css' ) ) { ?>
		<link rel="stylesheet" href="upload/adminstyles.css"/>
		<?}?>
		<!--[if IE 7]><link rel="stylesheet" href="admin/css/workarounds_ie7.css" /><![endif]-->
		<script type="text/javascript">
			var CookieDomain = "<?= BASE_URL ?>";
		</script>
		<script type="text/javascript" src="lib/javascript/bajax.js"></script>
		<script type="text/javascript" src="lib/javascript/arena-lib.js"></script>
		<script type="text/javascript" src="lib/javascript/arena-api.js"></script>
		<script type="text/javascript" src="lib/javascript/draglib.js"></script>
		<script type="text/javascript" src="lib/javascript/workbench.js"></script>
		<script type="text/javascript" src="lib/javascript/arena-admin.js"></script>
<?= implode ( "\\n", $this->sHeadData ) ?>
	</head>
	<body class="<?= getCurrentModule ( ) ?>">
		<div id="TopLogo"><a href="<?= BASE_URL ?>admin.php"><img src="admin/gfx/arenalogo2.<?= strstr ( $_SERVER[ 'HTTP_USER_AGENT' ], 'MSIE 6' ) ? 'gif' : 'png' ?>" border="0"/></a></div>
		<div id="TopLevelContainer">
		</div>	
		<div id="MetaButtons">
			<div class="logout" onclick="document.location='admin.php?module=settings&logout=1'">
				<img src="admin/gfx/icons/lock.png" alt="logg_ut"> <?= i18n ( 'metabuttons_Logout' ) ?>
			</div>
			<div class="preview" id="showWebsite">
				<a href="<?= BASE_URL ?>" target="_blank" style="color: #edf5ff"><img src="admin/gfx/icons/house.png" alt="vis_nettsiden" border="0"> <?= i18n ( 'metabuttons_Goto website' ) ?></a>
			</div>
			<div class="help" >
				<a href="javascript:void(0)" onclick="window.open('admin.php?module=extensions&extension=arenahelp&action=help','','width=720,height=480,topbar=no,status=no')" style="color: #EDF5FF" target="_blank"><img src="admin/gfx/icons/help.png" alt="hjelp" border="0"> <?= i18n ( 'metabuttons_Help' ) ?></a>
			</div>
			<?if ( $GLOBALS['Session']->AdminUser->modulePermission ( 'Access', 'settings' ) ) { ?>
			<div class="settings"<? if ( getCurrentModule ( ) == 'settings' ) return ' style="background: url(admin/gfx/background_header.jpg); opacity: 0.9; -moz-border-radius: 4px; -webkit-border-radius: 4px; border-radius: 4px;"'; ?>>
				<a href="admin.php?module=settings" style="color: <? if ( getCurrentModule ( ) == 'settings' ) return '#4B83B3'; return '#EDF5FF'; ?>">
					<img src="admin/gfx/icons/page_white_edit.png" alt="innstillinger" border="0"> <?= i18n ( 'metabuttons_Settings' ) ?>
				</a>
			</div>
			<?}?>
			<?if ( $GLOBALS[ 'Session' ]->AdminUser->_dataSource == 'core' ) { ?>
			<div class="core" id="showCore"<? if ( getCurrentModule ( ) == 'core' ) return ' style="background: url(admin/gfx/background_header.jpg); opacity: 0.9; -moz-border-radius: 4px; -webkit-border-radius: 4px; border-radius: 4px;"'; ?>>
				<a href="<?= BASE_URL ?>admin.php?module=core" style="color:  <? if ( getCurrentModule ( ) == 'core' ) return '#4B83B3'; return '#EDF5FF'; ?>"><img src="admin/gfx/icons/computer_edit.png" alt="superbruker" border="0"> <?= i18n ( 'metabuttons_Superuser' ) ?></a>
			</div>
			<?}?>
			<div class="workbench_show" onclick="hideWorkbench()" id="hideWorkbench">
				<img src="admin/gfx/icons/plugin_delete.png" alt="gjem_arbeidsbenk"> <?= i18n ( 'Hide workbench' ) ?>
			</div>
			<div class="workbench_hide" onclick="showWorkbench()" id="showWorkbench">
				<img src="admin/gfx/icons/plugin_add.png" alt="vis_arbeidsbenk"> <?= i18n ( 'Show workbench' ) ?>
			</div>
		</div>
		<div id="BajaxProgressContainer">
			<div class="Inner">
				<div id="BajaxProgress">
					<div id="BajaxProgressCounter">
					</div>
				</div>
			</div>
		</div>
		
		<div id="Workbench">
			<div id="WorkbenchHider"><img src="admin/gfx/icons/resultset_previous.png" id="WorkbenchHiderImage" /></div>
			<div id="WorkbenchArea"></div>
			<div id="WorkbenchWastebin"></div>
		</div>
		<script type="text/javascript">initWorkbench ( );</script>
		<div id="ModuleMoreArrowLeft"></div>
		<div id="ModuleMoreArrowRight"></div>
		<div id="ModuleList">
			<div id="ModuleListInner">
				<?= $this->moduleList ?>
			</div>
			<script type="text/javascript"> initTopTabs ( ); </script>
		</div>
		<div id="Content">
			<?= $this->moduleOutput ?>
			<br style="clear: both">
			<div id="Footer">
				Basert på ARENA CM v<?= ARENA_VERSION ?> | <?= i18n ( 'ARENA CM is available under the' ) ?> <a href="http://www.mozilla.org/MPL/MPL-1.1.html" target="_blank"><?= i18n ( 'MPL License' ) ?></a> | <a href="admin.php?module=settings&function=about">Om ARENA Enterprise</a>
			</div>
		</div>
		<script> initToggleBoxes (document.body); </script>
	</body>
</html>
