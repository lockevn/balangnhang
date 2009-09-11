<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH.'lib/datalib.php');




class SmartComDataUtil
{
	public static function CheckUserHasTicketOfCourse($username = '') {
		if(empty($username))
		{
			return false;
		}	
		
		$today = date('Ymd');
		return "select count(*) from mdl_smartcom_learning_ticket where allowday = '$today'";
	}
	
	public static function BuyTicketOfCourseForUser($username = '', $courseid = 0) {
		if(empty($username) || $courseid <= 1)
		{
			return -1;
		}    
		
		if(SmartComDataUtil::CheckUserHasTicketOfCourse($username))
		{
			return 0;			
		}
		else
		{
			$today = date('Ymd');
			return "select count(*) from mdl_smartcom_learning_ticket where allowday = '$today'";
		}
	}
	

	//function smartcom_user_candoanything() {
//		$context = get_context_instance(CONTEXT_SYSTEM);

//		return (has_capability('moodle/site:doanything', $context));
//	}
}
	
?>