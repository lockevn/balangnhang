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
	
	/**
	* @desc 
	* @return bool false if error, return array of quiz and grade (in percent form 00-100)
	*/
	public static function GetQuizResultPercentOfUser($userid=0, $courseid = 0) {
		if($userid < 1)
		{
			return false;
		}
		
		$whereClauseForCourse = '';
		if($courseid > 1)
		{
			$whereClauseForCourse = " course=$courseid and ";
		}
		
		
		$recs = get_records_sql(
"select MaxGrade.quiz as quizid, 100*SumGrades/MaxSumGrades as grade from
(
select id as quiz, sumgrades as MaxSumGrades from mdl_quiz where $whereClauseForCourse sumgrades>0
) as MaxGrade
join
(
select quiz, max(sumgrades) as sumgrades
from `mdl_quiz_attempts`
where userid=5 and quiz in (select id from mdl_quiz where $whereClauseForCourse sumgrades>0) 
group by quiz
) as UserGrade
on MaxGrade.quiz = UserGrade.quiz"
);
		if(is_array($recs))
		{			
			return $recs;			
		}
		return false;
	}

	
	
	public static function GetOverallQuizResultPercentOfUserInCourse($userid=0, $courseid = 0) {
		if($userid < 1 || $courseid <= 1)
		{
			return false;
		}
		
		$recs = get_field_sql(
"
select 100*UserSumgrades/SumSumGradesOfCourse as grade
from
(
	 select sum(sumgrades) as SumSumGradesOfCourse from mdl_quiz where course=$courseid and sumgrades>0
) as MaxGrade
join
(
	 select sum(sumgrades) as UserSumgrades
	from `mdl_quiz_attempts`
	where userid=$userid and
	quiz in (select id from mdl_quiz where course=$courseid and sumgrades>0)
) as UserGrade
"
		);
		
		if($recs)
		{            
			return $recs;
		}
		return false;
	}
	
	//function smartcom_user_candoanything() {
//		$context = get_context_instance(CONTEXT_SYSTEM);

//		return (has_capability('moodle/site:doanything', $context));
//	}
}
	
?>