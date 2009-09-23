<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once(ABSPATH.'mod/smartcom/locallib.php');


define('AJAX_CALL',true);
require_login();

$username = required_param('username', PARAM_TEXT);
$courseid = required_param('courseid', PARAM_INT);

if($username === $USER->username)
{	
	$course = get_record('course', 'id', $courseid);
	if($course)
	{		
		$buyresult = SmartComDataUtil::BuyTicketOfCourseForUser($USER->username, $course->id, empty($course->cost) ? 0 : $course->cost );
		if($buyresult === 1)
		{
			SmartComDataUtil::ChangeRoleOfExpiredStudentToStudentInCourse($course->id, $USER->id);
			echo 'ok';
		}
		else
		{
			echo "buy fail, result=$buyresult";
		}
	}
	else
	{
		echo 'course is not valid';
	}
}
else
{
	echo 'user is not valid';
}

?>