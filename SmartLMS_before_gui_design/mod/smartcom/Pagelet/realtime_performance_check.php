<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_login();
$userid = $USER->id;

$courseid = required_param('courseid', PARAM_INT);   // course
if (! $course = get_record('course', 'id', $courseid)) {
	error('GURUCORE: Course ID is incorrect');
}


$context = get_context_instance(CONTEXT_COURSE, $courseid);
require_capability('mod/smartcom:realtimeperformancecheck', $context);


$tpl->assign('courseid', $courseid);
$tpl->assign('userid', $userid);
$tpl->assign('username', $USER->username);

		
$FILENAME = 'realtime_performance_check';
$$FILENAME = $tpl->fetch("$FILENAME.tpl.php");

?>