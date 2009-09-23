<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");


require_login();
$courseid = required_param('courseid', PARAM_INT);
$course = get_record('course', 'id', $courseid);
if(empty($course))
{
	print_error("COURSE_NOT_EXISTED");
	die();
}

global $SESSION;
$alreadyHasTicket = SmartComDataUtil::CheckUserHasTicketOfCourse($USER->username, $courseid);
if($alreadyHasTicket)
{
	SmartComDataUtil::ChangeRoleOfExpiredStudentToStudentInCourse($courseid, $USER->id);
	redirect($SESSION->wantsurl, "You've already have ticket to this course");
	die();
}
 
if(empty($course->cost))
{
	SmartComDataUtil::ChangeRoleOfExpiredStudentToStudentInCourse($courseid, $USER->id);
	redirect($SESSION->wantsurl, "This course is free");
	die();
}


// GO HERE: no ticket, not free, change role to expireStudent
SmartComDataUtil::ChangeRoleOfStudentToExpiredStudentInCourse($courseid, $USER->id);

$accountinfo = get_record('smartcom_account', 'username', $USER->username);
$tpl->assign('courseid', $courseid);
$tpl->assign('course', $course);
$tpl->assign('accountinfo', $accountinfo);
$tpl->assign('alreadyHasTicket', $alreadyHasTicket);


$FILENAME = 'ticket_buy';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>