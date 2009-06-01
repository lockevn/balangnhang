<?php
ob_start();

session_name("docebo_upgrader");
session_start();

require_once(dirname(__FILE__).'/config.php');
require_once($GLOBALS['where_upgrade'].'/'.$GLOBALS['path_to_config'].'config.php');

require_once($GLOBALS['where_upgrade'].'/lib/lib.function.php');
require_once($GLOBALS['where_upgrade'].'/lib/lib.step.php');
require_once($GLOBALS['where_upgrade'].'/lib/lib.form.php');
require_once($GLOBALS['where_upgrade'].'/lib/lib.docebosql.php');


DoceboUpgradeGui::includeLang();

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php DoceboUpgradeGui::getBrowserLangCode(); ?>">
	<head>
		<title><?php echo _UPGRADER_TITLE; ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="Copyright" content="Docebo srl" />
		<link rel="Copyright" href="http://www.docebo.com" title="Copyright Notice" />
		<link href="templates/images/favicon.ico" rel="shortcut icon" />
		<link href="templates/style/style.css" rel="stylesheet" type="text/css" />
		
		<!-- Dependency -->
		<script type="text/javascript" src="./lib/yui/yahoo/yahoo-min.js"></script>

		<!-- Used for Custom Events and event listener bindings -->
		<script type="text/javascript" src="./lib/yui/event/event-min.js"></script>

		<!-- Source file -->
		<script type="text/javascript" src="./lib/yui/connection/connection-min.js"></script>
		<script type="text/javascript" src="./lib/yui/json/json-beta-min.js"></script>
		

		<script type="text/javascript" src="./lib/loader.js"></script>
		
	</head>
	<body>
		<ul id="blind_navigation" class="blind_navigation">
			<li>
				<a href="#main_area_title"><?php echo _JUMP_TO_CONTENT; ?></a>
			</li>
		</ul>
		<?php
			require_once('templates/header.php');
		?>
		<div class="block_left">
			<div class="spacer"></div>
			<h1 class="title_menu">Upgrade</h1>
			<?php
				require_once($GLOBALS['where_upgrade'].'/menu/menu_lat.php'); 
			?>
		</div>
		<div class="block_right">
			<div class="spacer"></div>
			<div class="central_area">
				<?php
					dispatchStep(( isset($_REQUEST['step']) ? $_REQUEST['step'] : '' ));
				?>
			</div>
		</div>
	</body>
</html>
<?php ob_end_flush(); ?>
