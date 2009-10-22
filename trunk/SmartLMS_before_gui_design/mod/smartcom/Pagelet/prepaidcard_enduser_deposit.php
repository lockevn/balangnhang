<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_login();

$tpl->assign('state', '');

$FILENAME = 'prepaidcard_enduser_deposit';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>