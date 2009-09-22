<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");


require_login();
$courseid = required_param('courseid', PARAM_INT);
$course = get_record('course', 'id', $courseid);

if(empty($course->cost))
{
	global $SESSION;
	redirect($SESSION->wantsurl, "You've already have ticket to this course");
}

$accountinfo = get_record('smartcom_account', 'username', $USER->username);

$tpl->assign('courseid', $courseid);
$tpl->assign('course', $course);
$tpl->assign('accountinfo', $accountinfo);


$FILENAME = 'ticket_buy';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>