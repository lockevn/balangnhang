<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS['where_framework'].'/class/class.dashboard.php');

class Dashboard_Lms extends Dashboard {
	
	function Dashboard_Lms() {
	
	}
	
	function getBoxContent() {
		
		$html = '';
		
		if(!checkPerm('view', true, 'course', 'lms')) return $html;
		
		require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');
		
		$course_man = new AdminCourseManagment();
		$course_stats = $course_man->getCoursesStats();
		
		$lang =& DoceboLanguage::createInstance('dashboard', 'framework');
		$html = array();
		$html[] = '<h2 class="course_main_title">'.$lang->def('_COURSES_PANEL').'</h2>'
			.'<p class="course_main">'
				.$lang->def('_TOTAL_COURSE').': <b>'.$course_stats['total'].'</b>;<br />'
				.$lang->def('_ACTIVE_COURSE').': <b>'.$course_stats['active'].'</b>;'
			.'</p><p>'
				.$lang->def('_ACTIVE_SEVEN_COURSE').': <b>'.$course_stats['active_seven'].'</b>;<br />'
				.$lang->def('_DEACTIVE_SEVEN_COURSE').': <b>'.$course_stats['deactive_seven'].'</b>;'
			.'</p><p>'
				.$lang->def('_TOTAL_SUBSCRIPTION').': <b>'.$course_stats['user_subscription'].'</b>;<br />'
				.( checkPerm('moderate', true, 'course', 'lms')
					? $lang->def('_WAITING_SUBSCRIPTION').': <b>'.$course_stats['user_waiting'].'</b>;'
					: '' )
			.'</p>';
		
		return $html;
	}
	
}

?>