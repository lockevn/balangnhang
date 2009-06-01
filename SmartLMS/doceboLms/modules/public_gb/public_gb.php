<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

function publicGb() {

	checkPerm('view');

	require_once($GLOBALS['where_lms'].'/lib/lib.test.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	
	$lang 	=& DoceboLanguage::createInstance('gradebook', 'lms');
	$out 	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$test_man 		= new GroupTestManagement();
	$report_man 	= new CourseReportManager();
	
	// Select course for the user
	
	$query_course = "SELECT u.idCourse, u.edition_id, c.name"
					." FROM ".$GLOBALS['prefix_lms']."_courseuser AS u"
					." JOIN ".$GLOBALS['prefix_lms']."_course AS c ON c.idCourse = u.idCourse"
					." WHERE u.idUser = '".getLogUserId()."'"
					." AND u.status IN ('"._CUS_SUBSCRIBED."', '"._CUS_BEGIN."', '"._CUS_END."', '"._CUS_SUSPEND."')"
					." ORDER BY c.name";
	
	$result = mysql_query($query_course);
	
	$out->add(getTitleArea($lang->def('_PUBLIC_GB'), 'gradebook')
			.'<div class="std_block">');
	
	while (list($id_course, $id_edition, $name) = mysql_fetch_row($result))
	{
		$_SESSION['idCourse'] = $id_course;
		
		// XXX: update if needed
		$org_tests 		=& $report_man->getTest();
		$tests_info		=& $test_man->getTestInfo($org_tests);
		
		$i_test = array();
		$i_test_report_id = array();
		
		// XXX: Info for updates
		$query_tot_report = "
		SELECT COUNT(*)
		FROM ".$GLOBALS['prefix_lms']."_coursereport 
		WHERE id_course = '".$id_course."'";
		list($tot_report) = mysql_fetch_row(mysql_query($query_tot_report));
		
		$query_tests = "
		SELECT id_report, id_source 
		FROM ".$GLOBALS['prefix_lms']."_coursereport 
		WHERE id_course = '".$id_course."' AND source_of = 'test'";
		$re_tests = mysql_query($query_tests);
		while(list($id_r, $id_t) = mysql_fetch_row($re_tests)) {
			
			$i_test[$id_t] = $id_t;
			$i_test_report_id[$id_r] = $id_r;
		}
		
		// XXX: Update if needed
		if($tot_report == 0) {
			
			$report_man->initializeCourseReport($org_tests);
		} else {
			if(is_array($i_test)) $test_to_add = array_diff($org_tests, $i_test);
			else $test_to_add = $org_tests;
			if(is_array($i_test)) $test_to_del = array_diff($i_test, $org_tests);
			else $test_to_del = $org_tests;
			if(!empty($test_to_add) || !empty($test_to_del)) {
				
				
				$report_man->addTestToReport($test_to_add, 1);
				$report_man->delTestToReport($test_to_del);
				
				$included_test = $org_tests;
			}
		}
		$report_man->updateTestReport($org_tests);
		
		$reports 	= array();
		$id_test 	= array();
		$id_report 	= array();
		
		// XXX: retrive all report info
		$query_report = "
		SELECT id_report, title, max_score, required_score, weight, show_to_user, use_for_final, source_of, id_source 
		FROM ".$GLOBALS['prefix_lms']."_coursereport 
		WHERE id_course = '".$id_course."' AND show_to_user = 'true' 
		ORDER BY sequence ";
		$re_report = mysql_query($query_report);
		
		while($info_report = mysql_fetch_assoc($re_report)) {
			
			$reports[$info_report['id_report']]	= $info_report;
			
			switch($info_report['source_of']) {
				case "test" : {
					$id_test[] = $info_report['id_source'];
				};break;
				case "activity" :
				case "final_vote" : {
					$id_report[] = $info_report['id_report'];
				};break;
			}
		}
		
		// XXX: retrive report and test score
		$report_score 	=& $report_man->getReportsScores($id_report, getLogUserId());
		$tests_score 	=& $test_man->getTestsScores($id_test, array(getLogUserId()));
		
		// XXX: create table
		$caption = str_replace('[course]', $name, $lang->def('_PUBLIC_GB_CAPTION'));
		$table = new TypeOne(0, $caption, $caption);
		
		$type_h = array('', 'align_center', 'align_center', '');
		$cont_h = array(
			$lang->def('_TITLE'),
			$lang->def('_REPORT_SCORE'),
			$lang->def('_REPORT_SCORE_REQUIRED'),
			$lang->def('_DATE'),
			$lang->def('_REPORT_COMMENT')
		);
		
		$table->setColsStyle($type_h);
		$table->addHead($cont_h);
		
		$id_user = getLogUserId();
		
		// XXX: construct table data
		if(!empty($reports)) 
		while(list($id_report, $report_info) = each($reports)) {
			
			$id_source = $report_info['id_source'];
			$title = strip_tags($report_info['title']);
			$score = '';
			$required = $report_info['required_score'];
			$maxscore = $report_info['max_score'];
			$date = '';
			$comment = '';
			
			switch($report_info['source_of']) {
				case "test" : {
					
					$title = $tests_info[$id_source]['title'];
					if(isset($tests_score[$id_source][$id_user])) {
						
						switch($tests_score[$id_source][$id_user]['score_status']) {
							case "not_checked" 	: {
								
								$score = '<span class="cr_not_check">'.$lang->def('_NOT_CHECKED').'</span>';
							};break;
							case "passed" 		: {
								
								//$score = '<span class="cr_passed">'.$lang->def('_PASSED').'</span>';
								$score = '<img src="'.getPathImage('fw').'emoticons/thumbs_up.gif" alt="'.$lang->def('_PASSED').'" />&nbsp;'.$tests_score[$id_source][$id_user]['score'];
								$date = $GLOBALS['regset']->databaseToRegional($tests_score[$id_source][$id_user]['date_attempt_mod']);
								$comment = $tests_score[$id_source][$id_user]['comment'];
							};break;
							case "not_passed" 	: {
								
								//$score = '<span class="cr_not_passed">'.$lang->def('_NOT_PASSED').'</span>';
								$score = '<img src="'.getPathImage('fw').'emoticons/thumbs_down.gif" alt="'.$lang->def('_NOT_PASSED').'" />&nbsp;'.$tests_score[$id_source][$id_user]['score'];
								$date = $GLOBALS['regset']->databaseToRegional($tests_score[$id_source][$id_user]['date_attempt_mod']);
								$comment = $tests_score[$id_source][$id_user]['comment'];
							};break;
							case "valid" 		: {
								
								$score = $tests_score[$id_source][$id_user]['score'];
								if($score == $report_info['max_score']) $score = '<span class="cr_max_score">'.$score.'</span>';
								elseif($score < $report_info['required_score']) $score = '<span class="cr_not_passed">'.$score.'</span>';
								$date = $GLOBALS['regset']->databaseToRegional($tests_score[$id_source][$id_user]['date_attempt_mod']);
								$comment = $tests_score[$id_source][$id_user]['comment'];
							};break;
						}
					}
				};break;
				case "activity" : {
					
					if(isset($report_score[$id_report][$id_user]) && $report_score[$id_report][$id_user]['score_status'] == 'valid') {
						
						$score = $report_score[$id_report][$id_user]['score'];
						if($score == $report_info['max_score']) $score = '<span class="cr_max_score">'.$score.'</span>';
						elseif($score < $report_info['required_score']) $score = '<span class="cr_not_passed">'.$score.'</span>';
						
						$date = $GLOBALS['regset']->databaseToRegional($report_score[$id_report][$id_user]['date_attempt']);
						$comment = $report_score[$id_report][$id_user]['comment'];
					}
				};break;
				case "final_vote" : {
					
					$title = strip_tags($lang->def('_FINAL_VOTE'));
					if(isset($report_score[$id_report][$id_user]) && $report_score[$id_report][$id_user]['score_status'] == 'valid') {
						
						$score = $report_score[$id_report][$id_user]['score'];
						if($score == $report_info['max_score']) $score = '<span class="cr_max_score">'.$score.'</span>';
						elseif($score < $report_info['required_score']) $score = '<span class="cr_not_passed">'.$score.'</span>';
						
						$date = $GLOBALS['regset']->databaseToRegional($report_score[$id_report][$id_user]['date_attempt']);
						$comment = $report_score[$id_report][$id_user]['comment'];
					} 
				};break;
			}
			$table->addBody(array(
				$title, 
				( $score == '' ? $lang->def('_NO_SCORE') : $score.' '.$lang->def('_MAX_DIVISOR').' '.$maxscore ), 
				$required, 
				$date, 
				$comment));
		}
		$out->add($table->getTable());
	}
	
	$out->add('</div>');
	
	unset($_SESSION['idCourse']);
}
	
function publicGbDispatch($op) {
	
	switch ($op) {
		default:
		case 'public_gb':
			publicGb();
		break;
	}
}

?>