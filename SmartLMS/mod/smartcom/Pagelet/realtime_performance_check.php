<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");


require_login();
$tpl->assign('courseid', $courseid);
$tpl->assign('userid', $USER->id);
$tpl->assign('username', $USER->username);

		
$FILENAME = 'realtime_performance_check';
$$FILENAME = $tpl->fetch("$FILENAME.tpl.php");

?>