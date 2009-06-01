<?php

/************************************************************************/
/* DOCEBO - Learning Managment System                               	*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2007                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once(dirname(__FILE__).'/certificate.base.php');

class CertificateSubs_UserStat extends CertificateSubstitution {

	function getSubstitutionTags() {
		
		$lang =& DoceboLanguage::createInstance('admin_certificate', 'lms');
		
		$subs = array();
		
		$id_meta = get_req('idmeta', DOTY_INT, 0);
		
		if($_GET['modname'] == 'meta_certificate' || $id_meta)
		{
			$subs['[table_course]'] = $lang->def('_TABLE_COURSE');
		}
		else
		{
			$subs['[date_enroll]'] 			= $lang->def('_DATE_ENROLL');
			$subs['[date_first_access]'] 	= $lang->def('_DATE_FIRST_ACCESS');
			$subs['[date_complete]'] 		= $lang->def('_DATE_COMPLETE');
			$subs['[total_time]'] 			= $lang->def('_TOTAL_TIME');
			$subs['[total_time_hour]'] 		= $lang->def('_TOTAL_TIME_HOUR');
			$subs['[total_time_minute]'] 	= $lang->def('_TOTAL_TIME_MINUTE');
			$subs['[total_time_second]'] 	= $lang->def('_TOTAL_TIME_SECOND');
			$subs['[test_score_start]'] 	= $lang->def('_TEST_SCORE_START');
			$subs['[test_score_start_max]'] = $lang->def('_TEST_SCORE_START_MAX');
			$subs['[test_score_final]'] 	= $lang->def('_TEST_SCORE_FINAL');
			$subs['[test_score_final_max]'] = $lang->def('_TEST_SCORE_FINAL_MAX');
			$subs['[course_score_final]'] 	= $lang->def('_COURSE_SCORE_FINAL');
			$subs['[course_score_final_max]'] = $lang->def('_COURSE_SCORE_FINAL_MAX');
		}
		
		return $subs;
	}
	
	function getSubstitution() {
		
		$subs = array();
		
		$lang =& DoceboLanguage::createInstance('course', 'lms');
		$lang =& DoceboLanguage::createInstance('admin_certificate', 'lms');
		
		$id_meta = get_req('idmeta', DOTY_INT, 0);
		
		if($_GET['modname'] == 'meta_certificate' || $id_meta)
		{
			require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
			require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
			
			$courses = array();
			
			$query =	"SELECT DISTINCT idCourse"
						." FROM ".$GLOBALS['prefix_lms']."_certificate_meta_course"
						." WHERE idMetaCertificate = '".$id_meta."'"
						." AND idUser = '".$this->id_user."'";
			
			$result = mysql_query($query);
			
			$table =	'<p align="center">'.$lang->def('_TABLE_COURSE_CAPTION').'</p>'
						.'<table width="80%" cellspacing="1" cellpadding="1" border="1" summary="Corsi frequentati" align="center">'
						.'<tr>'
						.'<td>'.$lang->def('_COURSE_CODE').'</td>'
						.'<td>'.$lang->def('_COURSE_NAME', 'course').'</td>'
						.'<td>'.$lang->def('_COURSE_RESULT').'</td>'
						.'</tr>';
			
			while(list($id_course) = mysql_fetch_row($result))
			{
				//$courses[$id_course] = $id_course;
				
				$man_course = new Man_Course();
				
				$course_info = $man_course->getCourseInfo($id_course);
				
				$rep_man = new CourseReportManager();
				
				$score_course = $rep_man->getUserFinalScore(array($this->id_user), array($this->id_course));
				
				$table .=	'<tr>'
							.'<td>'.$course_info['code'].'</td>'
							.'<td>'.$course_info['name'].'</td>'
							.'<td>'.(isset($score_course[$this->id_course][$this->id_user]) ? $score_course[$this->id_course][$this->id_user]['score'].' / '.$score_course[$this->id_course][$this->id_user]['max_score'] : '&nbsp;').'</td>'
							.'</tr>';
			}			
			
			$table .= '</table>';
			
			$subs['[table_course]'] = $table;
		}
		else
		{
			require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
			
			$courseuser = new Man_CourseUser();
			$course_stat =& $courseuser->getUserCourses($this->id_user, false, false, false, array($this->id_course));
			
			if(isset($course_stat[$this->id_course])) {
					
				$subs['[date_enroll]'] = $GLOBALS['regset']->databaseToRegional($course_stat[$this->id_course]['date_inscr'], 'date');
				$subs['[date_first_access]'] = $GLOBALS['regset']->databaseToRegional($course_stat[$this->id_course]['date_first_access'], 'date');
				$subs['[date_complete]'] = $GLOBALS['regset']->databaseToRegional($course_stat[$this->id_course]['date_complete'], 'date');
			} else {
				
				$subs['[date_enroll]'] = '';
				$subs['[date_first_access]'] = '';
				$subs['[date_complete]'] = '';
			}
			
			require_once($GLOBALS['where_lms'].'/lib/lib.orgchart.php');
			$org_man = new OrganizationManagement($this->id_course);
			
			$score_start = $org_man->getStartObjectScore(array($this->id_user), array($this->id_course));
			$score_final = $org_man->getFinalObjectScore(array($this->id_user), array($this->id_course));
			
			
			require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
			$rep_man = new CourseReportManager();
			
			$score_course = $rep_man->getUserFinalScore(array($this->id_user), array($this->id_course));
			
			
			$subs['[test_score_start]'] = ( isset($score_start[$this->id_course][$this->id_user]) ? $score_start[$this->id_course][$this->id_user]['score'] : '' );
			$subs['[test_score_start_max]'] = ( isset($score_start[$this->id_course][$this->id_user]) ? $score_start[$this->id_course][$this->id_user]['max_score'] : '' );
			$subs['[test_score_final]'] = ( isset($score_final[$this->id_course][$this->id_user]) ? $score_final[$this->id_course][$this->id_user]['score'] : '' );
			$subs['[test_score_final_max]'] = ( isset($score_final[$this->id_course][$this->id_user]) ? $score_final[$this->id_course][$this->id_user]['max_score'] : '' );
			
			$subs['[course_score_final]'] 	= ( isset($score_course[$this->id_course][$this->id_user]) ? $score_course[$this->id_course][$this->id_user]['score'] : '' );
			$subs['[course_score_final_max]'] = ( isset($score_course[$this->id_course][$this->id_user]) ? $score_course[$this->id_course][$this->id_user]['max_score'] : '' );
			
			require_once($GLOBALS['where_lms'].'/lib/lib.track_user.php');
			$time_in = TrackUser::getUserTotalCourseTime($this->id_user, $this->id_course);
			
			$hours = (int)($time_in/3600);
			$minutes = (int)(($time_in%3600)/60);
			$seconds = (int)($time_in%60);
			if($minutes < 10) $minutes = '0'.$minutes;
			if($seconds < 10) $seconds = '0'.$seconds;
			
			$subs['[total_time]'] 		= $hours.'h '.$minutes.'m '.$seconds.'s';
			$subs['[total_time_hour]'] 	= $hours;
			$subs['[total_time_minute]'] 	= $minutes;
			$subs['[total_time_second]'] 	= $seconds;
		}
		
		return $subs;
	}
	
}

?>