<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH.'lib/datalib.php');




class SmartComDataUtil
{
	public static function CheckUserHasTicketOfCourse($username='', $courseid=1) {
		if(empty($username))
		{
			return false;
		}	
		
		$today = date('Ymd');
		$recs = get_records_sql( 
		"
		select id from mdl_smartcom_learning_ticket 
		where allowday='$today' 
		and username='$username' and courseid=$courseid
		");
		
		if($recs)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public static function BuyTicketOfCourseForUser($username = '', $courseid = 0, $coursePrice) {
		if(empty($username) || $courseid <= 1)
		{
			return -1;
		}
				
		if(SmartComDataUtil::CheckUserHasTicketOfCourse($username, $courseid))
		{
			return 1;
		}
		
		global $CFG;		
		$mysqli = new mysqli($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname);
		if (mysqli_connect_errno()) {
			printf("Connect failed: %s\n", mysqli_connect_error());
			exit();
		}
		$mysqli->autocommit(false);


		// subtract money
		$dbret = $mysqli->query(			
		"update mdl_smartcom_account set coinvalue = coinvalue - $coursePrice
		where username = '$username' 
		and coinvalue >= $coursePrice
		and expiredate >= curdate()"
		);
					
		if($dbret)
		{     
			$today = date('Ymd');
			$dbret = $mysqli->query("
			replace into mdl_smartcom_learning_ticket (username, allowday, courseid)
			values ('$username','$today',$courseid)            
			");
		}
		
		if($dbret === true)
		{
			$mysqli->commit();
			return 1;
		}
		else
		{
			// lack of money, expired
			$mysqli->rollback();
			return -2;
		}		
	}
	

	//function smartcom_user_candoanything() {
//		$context = get_context_instance(CONTEXT_SYSTEM);

//		return (has_capability('moodle/site:doanything', $context));
//	}
}
	
?>