<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH.'lib/datalib.php');



define('EXPIRED_STUDENT_ROLE_ID', 10);
define('STUDENT_ROLE_ID', 5);

class SmartComDataUtil
{
	/**
	* @desc 
	* @return bool true if OK, false if not
	*/
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
	
	/**
	* @desc 
	* @return int -1 if fail, 1 if already has ticket or buy OK
	*/
	public static function BuyTicketOfCourseForUser($username = '', $courseid = 0, $coursePrice = 0){
		if(empty($username) || $courseid <= 1)
		{
			return -1;
		}
		
		if(SmartComDataUtil::CheckUserHasTicketOfCourse($username, $courseid))
		{
			return 1;
		}
		
		$coursePrice = empty($coursePrice) ? 0 : (int)$coursePrice;
		if($coursePrice === 0)
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


	/**
	* @desc lấy % điểm trên tập các quizid. 
	* @return bool false if error, return array of quiz and grade (in percent form 00-100)
	*/
	public static function GetQuizArrayPercentOfUser($userid=0, $courseid = 0, $childquizid = '') {
		if($userid < 1 || $courseid < 2 || empty($childquizid))
		{
			return false;
		}
		
		$childquizid = trim($childquizid, ',');		
		$recs = get_field_sql(
		"
		select ROUND(100 * sum(SumGrades) / sum(MaxSumGrades)) as grade
		from
		(
			select id as quiz, sumgrades as MaxSumGrades from mdl_quiz where id in ($childquizid) and sumgrades>0
		) as MaxGrade
		join
		(
			select quiz, grade as SumGrades
			from `mdl_quiz_grades`
			where userid=$userid and quiz in ($childquizid)
			group by quiz
		) as UserGrade
		on MaxGrade.quiz = UserGrade.quiz
		"
		);

		if($recs)
		{
			return $recs;
		}
		else
		{
			return 0;
		}
	}
	
	
	public static function GetQuizAndLessonLabel($courseid)
	{
		if($courseid < 2)
		{
			return false;
		}
		
		$recs = get_records_sql(
		"
select q.id, q.name, cs.label
from mdl_quiz as q
join
(
select instance as quiz, section from `mdl_course_modules` where module=12
) as Lesson_Quiz
on q.id = Lesson_Quiz.quiz
join
mdl_course_sections 
as cs
on Lesson_Quiz.section = cs.id
and cs.course=$courseid
		"
		);

		if($recs)
		{
			return $recs;
		}
		else
		{
			return null;
		}		
	}
	
	public static function GetQuizIdAndActivityLabel($courseid)
	{
		if($courseid < 2)
		{
			return false;
		}
		
		$mapResourceName_QuizIDlist = array();
		$arrCourseModule = get_coursemodules_in_course('quiz', $courseid);
		foreach (((array)$arrCourseModule) as $key => $value) {
			$mapResourceName_QuizIDlist[$value->instance] = trim(get_parent_resource_name($value));
		}
		return $mapResourceName_QuizIDlist;
	}
		
	/**
	 * @desc only work with student role, hasCapa(buyticket)
	 *	 
	 * @param mixed $courseorid id of the course or course object	 
	 * @param bool $setwantsurltome Define if we want to set $SESSION->wantsurl, defaults to
	 *             true. Used to avoid (=false) some scripts (file.php...) to set that variable,
	 *             in order to keep redirects working properly. MDL-14495
	 */
	public static function require_smartcom_ticket($courseid=0, $setwantsurltome=true) 
	{
		global $CFG, $SESSION, $USER, $COURSE, $FULLME;
		
		$course = get_record('course', 'id', $courseid);
		if(!$course)
		{
			// course does not existed, so ????            
			// allow to continue
			return;
		}
		
		if($course && empty($course->cost))
		{
			// free for all course
			// allow to continue
			return;
		}		
	
		
		// PAID COURSE, now checking condition role right
		$context = get_context_instance(CONTEXT_COURSE, $courseid);
		
		//*********************
		// cách này chặt chẽ hơn, vì dựa trên role name, tuy nhiên role (về mặt instance mà nói) là không tồn tại (theo recommend của moodle)
		// cách này cũng tốn thêm một câu query, tạm thời không dùng, vì cách check capa là đã đủ
				
		// is this STUDENT?
		//$bFoundStudentRoleOfCurrentUser = false;
//		$roles = get_user_roles($context, $USER->id, false, 'r.shortname DESC', true);
//		foreach (((array)$roles) as $role) {
//			if($role->shortname === 'student')
//			{
//				$bFoundStudentRoleOfCurrentUser = true;
//			}
//		}
		//*********************/
		
		// check course context, does this user has capa of buyticket?
		// ignore the adminRole, because admin always has every roles		
		$bIsCurrentUserHasAdminRole = has_capability('moodle/site:doanything', $context);
		if(!$bIsCurrentUserHasAdminRole && has_capability('mod/smartcom:buyticket', $context))
		{
			// if do not HAVE ticket
			if (SmartComDataUtil::CheckUserHasTicketOfCourse($USER->username, $courseid) == false) 
			{
				$urltoredirect = '';

				if ($setwantsurltome) {
					$SESSION->wantsurl = $FULLME;
				}
				if (!empty($_SERVER['HTTP_REFERER'])) {
					$SESSION->fromurl = $_SERVER['HTTP_REFERER'];
				}
				
				if (empty($CFG->loginhttps)) 
				{ 
					//do not require https
					$wwwroot = $CFG->wwwroot;
				} 
				else 
				{
					$wwwroot = str_replace('http:','https:', $CFG->wwwroot);
				}
				$urltoredirect = $wwwroot ."/mod/smartcom/index.php?courseid=$courseid&submodule=ticket_buy";
				
				
				// if ajax call, echo code and exit
				if(defined('AJAX_CALL'))
				{            
					die("NOT_HAVE_TICKET,{$USER->username},$courseid,$urltoredirect");
				}            
				else
				{
					redirect($urltoredirect);
					exit;
				}
			}
			else
			{			
				/////////////***********************/////////////////
				// GO HERE mean ticket is valid, continue their continue
				/// Make sure current IP matches the one for this session (if required)
				if (!empty($CFG->tracksessionip)) {
					if ($USER->sessionIP != md5(getremoteaddr())) {
						print_error('sessionipnomatch', 'error');
					}
				}
				return;   // User is allowed to see this course
			}			
		}
		else
		{			
			// không phải student, đành kệ nó thôi
			return;
		}
	}


	public static function ChangeRoleOfStudentToExpiredStudentInCourse($courseid, $userid)
	{		
		$context = get_context_instance(CONTEXT_COURSE, $courseid);
		role_unassign(STUDENT_ROLE_ID, $userid, null, $context->id);        
		role_assign(EXPIRED_STUDENT_ROLE_ID, $userid, null, $context->id);
	}
	
	public static function ChangeRoleOfExpiredStudentToStudentInCourse($courseid, $userid)
	{        
		$context = get_context_instance(CONTEXT_COURSE, $courseid);
		role_unassign(EXPIRED_STUDENT_ROLE_ID, $userid, null, $context->id);        
		role_assign(STUDENT_ROLE_ID, $userid, null, $context->id);
	}
	
}	
?>