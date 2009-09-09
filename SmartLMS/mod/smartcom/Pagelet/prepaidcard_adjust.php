<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

//$tpl->assign('courseid', $courseid);

$FILENAME = 'prepaidcard_adjust';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>