<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$tpl->assign('courseid', $courseid);
$tpl->assign('userid', $userid);
		
$FILENAME = 'realtime_performance_check';
$$FILENAME = $tpl->fetch("$FILENAME.tpl.php");

?>