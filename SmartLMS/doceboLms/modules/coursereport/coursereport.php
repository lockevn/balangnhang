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

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

function testreport($idTrack, $idTest, $testName, $studentName) {
        checkPerm('view');
        require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
        require_once($GLOBALS['where_lms'].'/lib/lib.test.php');

		$lang =& DoceboLanguage::createInstance('coursereport', 'lms');
        $out =& $GLOBALS['page'];
        $out->setWorkingZone('content');
        $query_testreport = "
        SELECT DATE_FORMAT(date_attempt, '%d/%m/%Y %H:%i'), score
        FROM ".$GLOBALS['prefix_lms']."_testtrack_times 
        WHERE idTrack = '".$idTrack."' AND idTest = '".$idTest."' ORDER BY date_attempt";
        $re_testreport = mysql_query($query_testreport);

		$test_man       = new GroupTestManagement();
		$report_man = new CourseReportManager();
        $org_tests              =& $report_man->getTest();
        $tests_info             =& $test_man->getTestInfo($org_tests);

        $page_title = array(
            'index.php?modname=coursereport&amp;op=coursereport' => $lang->def('_TH_TEST_REPORT'),
            strip_tags($testName)
        );
        $out->add(
                getTitleArea($page_title, 'coursereport', $lang->def('_TH_ALT'))
                .'<div class="std_block">'
                //.Form::openForm('test_vote', 'index.php?modname=coursereport&amp;op=testreview')
                //.Form::getHidden('id_test', 'id_test', $id_test)
                //.Form::getHidden('id_user', 'id_user', $id_user)
        );
        $out->add("<div class=\"back_container\"><a href=\"javascript:history.go(-1)\">".$lang->def('_BACK')."</a></div>");
        $out->add("<strong>".$lang->def('_TH_TEST_NAME')." ".$testName."<br/>Studente: ".$studentName."</strong><br/>");
        $out->add("<table class=\"type-one\"><thead><tr class=\"type-one-header\"><th>N.</th><th>".$lang->def('_TH_DATE_TIME_ATTEMPT')."</th><th>".$lang->def('_TH_POINTS')."</th></tr></thead>");
        $i = 1;
        while(list($date_attempt, $score) = mysql_fetch_row($re_testreport)) {
            $line = $i % 2 == 0 ? 'line' : 'line-col';
            $out->add("<tr class=\"".$line."\"><td align=\"center\">".$i."</td><td align=\"center\">".$date_attempt."</td><td align=\"center\"><b>".$score."</b></td></tr>");
            $i ++;
        }
        $out->add("</table><div class=\"back_container\"><a href=\"javascript:history.go(-1)\">".$lang->def('_BACK')."</a></div>");
}

function coursereport() {
	checkPerm('view');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	// XXX: Initializaing 
	$lang 			=& DoceboLanguage::createInstance('coursereport', 'lms');
	$out 			=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$included_test 	= array();
	$mod_perm = checkPerm('mod', true);
	
	// XXX: Instance management
	$acl_man 	= $GLOBALS['current_user']->getAclManager();
	$test_man 	= new GroupTestManagement();
	$report_man = new CourseReportManager();
	
	// XXX: Find test from organization
	$org_tests 		=& $report_man->getTest();
	$tests_info		= $test_man->getTestInfo($org_tests);
	
	// XXX: Find students
	$id_students	=& $report_man->getStudentId();
	$students_info 	=& $acl_man->getUsers($id_students);
	
	// XXX: Info for updates
	$query_tot_report = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_lms']."_coursereport 
	WHERE id_course = '".$_SESSION['idCourse']."'";
	list($tot_report) = mysql_fetch_row(mysql_query($query_tot_report));
	
	$query_tests = "
	SELECT id_report, id_source 
	FROM ".$GLOBALS['prefix_lms']."_coursereport 
	WHERE id_course = '".$_SESSION['idCourse']."' AND source_of = 'test'";
	$re_tests = mysql_query($query_tests);
	while(list($id_r, $id_t) = mysql_fetch_row($re_tests)) {
		
		$included_test[$id_t] = $id_t;
		$included_test_report_id[$id_r] = $id_r;
	}
	
	// XXX: Update if needed
	if($tot_report == 0) {
		
		$report_man->initializeCourseReport($org_tests);
	} else {
		if(is_array($included_test)) $test_to_add = array_diff($org_tests, $included_test);
		else $test_to_add = $org_tests;
		if(is_array($included_test)) $test_to_del = array_diff($included_test, $org_tests);
		else $test_to_del = $org_tests;
		if(!empty($test_to_add) || !empty($test_to_del)) {
			
			$report_man->addTestToReport($test_to_add, 1);
			$report_man->delTestToReport($test_to_del);
			
			$included_test = $org_tests;
		}
	}
	$report_man->updateTestReport($org_tests);
	
	// XXX: Retrive all colums (test and so), and set it
	$img_mod = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" />';
	
	$type_h = array('line_users');
	$cont_h = array(	$lang->def('_DETAILS')); 

	$a_line_1 = array('');
	$a_line_2 = array('');
	$colums['max_score']		= array($lang->def('_MAX_SCORE'));
	$colums['required_score']	= array($lang->def('_REQUIRED_SCORE'));
	$colums['weight']	 		= array($lang->def('_WEIGHT'));
	$colums['show_to_user'] 	= array($lang->def('_SHOW_TO_USER'));
	$colums['use_for_final'] 	= array($lang->def('_USE_FOR_FINAL'));
	
	$query_report = "
	SELECT id_report, title, max_score, required_score, weight, show_to_user, use_for_final, source_of, id_source 
	FROM ".$GLOBALS['prefix_lms']."_coursereport 
	WHERE id_course = '".$_SESSION['idCourse']."' 
	ORDER BY sequence ";
	$re_report = mysql_query($query_report);
	$total_weight = 0;
	$i = 0;
	while($info_report = mysql_fetch_assoc($re_report)) {
		
		$id 									= $info_report['id_source'];
		$reports[$info_report['id_report']]		= $info_report;
		$reports_id[] 							= $info_report['id_report'];
		
		// XXX: set action colums
		
		$type_h[] = 'align_center';
		
		switch($info_report['source_of']) {
			case "test" : {
				
				$title = strip_tags($tests_info[$info_report['id_source']]['title']);
				
				if(!$mod_perm) {
						$my_action = $title;
						$a_line_2[] = '';
				} else {
					
					$my_action = '<li><a href="index.php?modname=coursereport&amp;op=testvote&amp;id_test='.$id.'" title="'.$lang->def('_CHANGE_TEST_VOTE').'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" /> '
						.$title.'</a></li>'
						.'<br/><li><a href="index.php?modname=coursereport&amp;op=testQuestion&amp;id_test='.$id.'" title="'.$lang->def('_TQ_LINK').'">'.str_replace('[test]', $title, $lang->def('_TQ_LINK'));
						
					$a_line_2[] = '<a href="index.php?modname=coursereport&amp;op=roundtest&amp;id_test='.$id.'" '
								.'title="'.$lang->def('_ROUND_TEST_VOTE').'">'.$lang->def('_ROUND_VOTE').'</a>';
				}
			};break;
			case "activity" 	: {
				
				$title = strip_tags($info_report['title']);
				
				if(!$mod_perm) {
						$my_action = $title;
						$a_line_2[] = '';
				} else {
					
					$my_action = '<li><a href="index.php?modname=coursereport&amp;op=modactivityscore&amp;id_report='.$info_report['id_report'].'" '
						.'title="'.$lang->def('_CHANGE_ACTIVITY_VOTE').'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" /> '
						.$title.'</a></li>'
						
						.'<li><a href="index.php?modname=coursereport&amp;op=delactivity&amp;id_report='.$info_report['id_report'].'" '
						.'title="'.$lang->def('_DELETE_ACTIVITY_VOTE').' : '.$title.'">'
						.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$title.'" />'
						.'</a></li>';
				
					$a_line_2[] = '<a href="index.php?modname=coursereport&amp;op=roundreport&amp;id_report='.$info_report['id_report'].'" '
							.'title="'.$lang->def('_ROUND_ACTIVITY_VOTE_TITLE').'">'.$lang->def('_ROUND_VOTE').'</a>';
				}
			};break;
			case "final_vote" 	: {
				
				$title = strip_tags($lang->def('_FINAL_VOTE'));
				$info_report['weight'] = $total_weight;
				
				if(!$mod_perm) {
						$my_action = $title;
						$a_line_2[] = '';
				} else {
					
					$my_action = '<li><a href="index.php?modname=coursereport&amp;op=finalvote&amp;id_report='.$info_report['id_report'].'" '
						.'title="'.$lang->def('_CHANGE_TEST_VOTE').' : '.$title.'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" /> '
						.$title.'</a></li>';
						
					$a_line_2[] = '<ul class="adjac_vert_link">'
							.'<li><a href="index.php?modname=coursereport&amp;op=redofinal&amp;id_report='.$info_report['id_report'].'" '
								.'title="'.$lang->def('_REDO_FINAL_VOTE_TITLE').'">'.$lang->def('_REDO_FINAL_VOTE').'</a></li>'
							.'<li><a href="index.php?modname=coursereport&amp;op=roundreport&amp;id_report='.$info_report['id_report'].'" '
								.'title="'.$lang->def('_ROUND_FINAL_VOTE_TITLE').'">'.$lang->def('_ROUND_VOTE').'</a></li>'
							.'</ul>';
				}
			};break;
		}
		
		$top = '<ul class="adjac_link">';
		if($mod_perm) {
			if($i != 0 && $info_report['source_of'] != 'final_vote') {
				$top .= '<li>'
					.'<a href="index.php?modname=coursereport&amp;op=moveleft&amp;id_report='.$info_report['id_report'].'" '
						.'title="'.$lang->def('_MOVE_LEFT').' : '.$title .'">'
					.'<img src="'.getPathImage().'standard/left.gif" alt="'.$lang->def('_LEFT').'" /></a></li>';
			}
		}
		$top .= $my_action;
		if($mod_perm) {
			if(($i < ($tot_report - 1)) && ($tot_report > 2) ) {
				$top .= '<li>'
						.'<a href="index.php?modname=coursereport&amp;op=moveright&amp;id_report='.$info_report['id_report'].'" '
							.'title="'.$lang->def('_MOVE_RIGHT').' : '.$title .'">'
						.'<img src="'.getPathImage().'standard/right.gif" alt="'.$lang->def('_RIGHT').'" /></a></li>';
			}
		}
		$top .= '</ul>';
		$cont_h[] = $top;
		$i++;
		
		//set info colums
		$colums['max_score'][] 		= $info_report['max_score'];
		$colums['required_score'][]	= $info_report['required_score'];
		$colums['weight'][] 			= $info_report['weight'];
		$colums['show_to_user'][] 		= ( $info_report['show_to_user'] == 'true' ? $lang->def('_YES') : $lang->def('_NO') );
		$colums['use_for_final'][] 	= ( $info_report['use_for_final'] == 'true' ? $lang->def('_YES') : $lang->def('_NO') );
		
		if($info_report['use_for_final'] == 'true') $total_weight += $info_report['weight'];
	}
	
	// XXX: Set table intestation
	$tb_report = new TypeOne(0, $lang->def('_COURSE_REPORT_CAPTION'), $lang->def('_COURSE_REPORT_SUMMARY'));
	
	$tb_report->setColsStyle($type_h);
	$tb_report->addHead($cont_h);
	
	$tb_report->addBody($a_line_2);
	
	$tb_report->addBody($colums['max_score']);
	$tb_report->addBody($colums['required_score']);
	$tb_report->addBody($colums['weight']);
	$tb_report->addBody($colums['show_to_user']);
	$tb_report->addBody($colums['use_for_final']);
	
	//$tb->addBodyExpanded('<span class="text_bold title_big">'.$lang->def('_STUDENTS_VOTE').'</span>', 'align_center');
	$tb_score = new TypeOne(0, $lang->def('_STUDENTS_VOTE'), $lang->def('_COURSE_REPORT_SUMMARY'));
	$tb_score->setColsStyle($type_h);
	$cont_h[0] = $lang->def('_STUDENTS');
	$tb_score->addHead($cont_h);
	
	// XXX: Retrive Test info and scores
	$tests_score 	=& $test_man->getTestsScores($included_test, $id_students);
	
	// XXX: Calculate statistic
	$test_details 	= array();
	if(is_array($included_test)) {
		
		while(list($id_test, $users_result) = each($tests_score)) {
			
			while(list($id_user, $single_test) = each($users_result)) {
				
				if($single_test['score_status'] == 'valid') {
					
					// max
					if(!isset($test_details[$id_test]['max_score'])) {
						$test_details[$id_test]['max_score'] = $single_test['score'];
					} elseif($single_test['score'] > $test_details[$id_test]['max_score']) {
						$test_details[$id_test]['max_score'] = $single_test['score'];
					}
					
					// min
					if(!isset($test_details[$id_test]['min_score'])) {
						$test_details[$id_test]['min_score'] = $single_test['score'];
					} elseif($single_test['score'] < $test_details[$id_test]['min_score']) {
						$test_details[$id_test]['min_score'] = $single_test['score'];
					}
					
					//number of valid score
					if(!isset($test_details[$id_test]['num_result'])) $test_details[$id_test]['num_result'] = 1;
					else $test_details[$id_test]['num_result']++;
					
					// averange
					if(!isset($test_details[$id_test]['averange'])) $test_details[$id_test]['averange'] = $single_test['score'];
					else $test_details[$id_test]['averange'] += $single_test['score'];
					
				}
			}
		}
		while(list($id_test, $single_detail) = each($test_details)) {
			
			if(isset($single_detail['num_result'])) {
				$test_details[$id_test]['averange'] /= $test_details[$id_test]['num_result'];
			}
		}
		reset($test_details);
	}
	// XXX: Retrive other source scores
	$reports_score 	=& $report_man->getReportsScores(
		( isset($included_test_report_id) && is_array($included_test_report_id) ? array_diff($reports_id, $included_test_report_id) : $reports_id ));
	
	// XXX: Calculate statistic
	$report_details = array();
	while(list($id_report, $users_result) = each($reports_score)) {
		
		while(list($id_user, $single_report) = each($users_result)) {
			
			if($single_report['score_status'] == 'valid') {
				
				// max
				if(!isset($report_details[$id_report]['max_score'])) {
					$report_details[$id_report]['max_score'] = $single_report['score'];
				} elseif($single_report['score'] > $report_details[$id_report]['max_score']) {
					$report_details[$id_report]['max_score'] = $single_report['score'];
				}
				
				// min
				if(!isset($report_details[$id_report]['min_score'])) {
					$report_details[$id_report]['min_score'] = $single_report['score'];
				} elseif($single_report['score'] < $report_details[$id_report]['min_score']) {
					$report_details[$id_report]['min_score'] = $single_report['score'];
				}
				
				//number of valid score
				if(!isset($report_details[$id_report]['num_result'])) $report_details[$id_report]['num_result'] = 1;
				else $report_details[$id_report]['num_result']++;
				
				// averange
				if(!isset($report_details[$id_report]['averange'])) $report_details[$id_report]['averange'] = $single_report['score'];
				else $report_details[$id_report]['averange'] += $single_report['score'];
				
			}
		}
	}
	while(list($id_report, $single_detail) = each($report_details)) {
		
		if(isset($single_detail['num_result'])) {
			$report_details[$id_report]['averange'] /= $report_details[$id_report]['num_result'];
		}
	}
	reset($report_details);
	
	// XXX: Display user scores
	if(!empty($students_info))
	while(list($idst_user, $user_info) = each($students_info)) {
		
		$user_name = ( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
						? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
						: $acl_man->relativeId($user_info[ACL_INFO_USERID]) );
		$cont = array($user_name);//, 
					//'<a href="index.php?modname=coursereport&amp;op=userscore&amp;id_user='.$idst_user.'" '
					//	.'title="'.$lang->def('_MOD_USER_SCORE').' : '.strip_tags($user_name).'">'
					//	.'<img src="'.getPathImage().'standard/mod.gif" '
					//		.'alt="'.$lang->def('_MOD').' : '.strip_tags($user_name).'" /></a>'
		// for every colum
		foreach($reports as $id_report => $info_report) {
			
			switch($info_report['source_of']) {
				case "test" : {
					
					$id_test = $info_report['id_source'];
					if(isset($tests_score[$id_test][$idst_user])) {
						
						switch($tests_score[$id_test][$idst_user]['score_status']) {
							case "not_complete" : $cont[] = '-';break;
							case "not_checked" 	: {
								$cont[] = '<span class="cr_not_check">'.$lang->def('_NOT_CHECKED').'</span>';
								
								// Count not checked
								if(!isset($test_details[$id_test]['not_checked'])) $test_details[$id_test]['not_checked'] = 1;
								else $test_details[$id_test]['not_checked']++;
							};break;
							case "passed" 		: {
								//$cont[] = '<span class="cr_passed">'.$lang->def('_PASSED').'</span>';
								$cont[]='<img src="'.getPathImage('fw').'emoticons/thumbs_up.gif" alt="'.$lang->def('_PASSED').'" />';
								// Count passed
								if(!isset($test_details[$id_test]['passed'])) $test_details[$id_test]['passed'] = 1;
								else $test_details[$id_test]['passed']++;
							};break;
							case "not_passed" 	: {
								//$cont[] = '<span class="cr_not_passed">'.$lang->def('_NOT_PASSED').'</span>';
								$cont[]='<img src="'.getPathImage('fw').'emoticons/thumbs_down.gif" alt="'.$lang->def('_NOT_PASSED').'" />';
								// Count not passed
								if(!isset($test_details[$id_test]['not_passed'])) $test_details[$id_test]['not_passed'] = 1;
								else $test_details[$id_test]['not_passed']++;
							};break;
							case "valid" 		: {
								
								$score = $tests_score[$id_test][$idst_user]['score'];
								
								if ($tests_score[$id_test][$idst_user]['times'] > 1)
                                    $tests_score[$id_test][$idst_user]['times'] = "<a href=\"index.php?modname=coursereport&op=testreport&idTest=".$tests_score[$id_test][$idst_user]['idTest']."&idTrack=".$tests_score[$id_test][$idst_user]['idTrack']."&testName=".$tests_info[$info_report['id_source']]['title']."&studentName=".$acl_man->relativeId($user_info[ACL_INFO_USERID])."\">".$tests_score[$id_test][$idst_user]['times']."</a>";
                                    $tt = "(".$tests_score[$id_test][$idst_user]['times'].")";
								
								if($score >= $info_report['required_score']) {
									if($score == $test_details[$id_test]['max_score']) $cont[] = '<span class="cr_max_score">'.$score." ".$tt.'</span>';
									else $cont[] = $score." ".$tt;
									
									// Count passed
									if(!isset($test_details[$id_test]['passed'])) $test_details[$id_test]['passed'] = 1;
									else $test_details[$id_test]['passed']++;
								} else {
									if($score == $test_details[$id_test]['max_score']) $cont[] = '<span class="cr_max_score cr_not_passed">'.$score." ".$tt.'</span>';
									else$cont[] = '<span class="cr_not_passed">'.$score." ".$tt.'</span>';
									
									// Count not passed
									if(!isset($test_details[$id_test]['not_passed'])) $test_details[$id_test]['not_passed'] = 1;
									else $test_details[$id_test]['not_passed']++;
								}
								if(isset($test_details[$id_test]['varianza']) && isset($test_details[$id_test]['averange'])) {
									$test_details[$id_test]['varianza'] += pow(($tests_score[$id_test][$idst_user]['score'] - $test_details[$id_test]['averange']), 2);
								} else {
									$test_details[$id_test]['varianza'] = pow(($tests_score[$id_test][$idst_user]['score'] - $test_details[$id_test]['averange']), 2);
								}
							};break;
							default : {
								
								$cont[] = '-';
							}
						}
					} else {
						
						$cont[] = '-';
					}
				};break;
				case "activity" : 
				case "final_vote" : {
					
					$id_report = $info_report['id_report'];
					if(isset($reports_score[$id_report][$idst_user])) {
						
						switch($reports_score[$id_report][$idst_user]['score_status']) {
							case "not_complete" : $cont[] = '-';break;
							case "valid" 		: {
								if($reports_score[$id_report][$idst_user]['score'] >= $info_report['required_score']) {
									if($reports_score[$id_report][$idst_user]['score'] == $info_report['max_score']) {
										$cont[] = '<span class="cr_max_score">'.$reports_score[$id_report][$idst_user]['score'].'</span>';
									} else $cont[] = $reports_score[$id_report][$idst_user]['score'];
									
									// Count passed
									if(!isset($report_details[$id_report]['passed'])) $report_details[$id_report]['passed'] = 1;
									else $report_details[$id_report]['passed']++;
								} else {
									$cont[] = '<span class="cr_not_passed">'.$reports_score[$id_report][$idst_user]['score'].'</span>';
									
									// Count not passed
									if(!isset($report_details[$id_report]['not_passed'])) $report_details[$id_report]['not_passed'] = 1;
									else $report_details[$id_report]['not_passed']++;
								}
								if(isset($report_details[$id_report]['varianza']) && isset($report_details[$id_report]['averange'])) {
									$report_details[$id_report]['varianza'] += round(pow(($reports_score[$id_report][$idst_user]['score'] - $report_details[$id_report]['averange']), 2), 2);
								} else {
									$report_details[$id_report]['varianza'] = round(pow(($reports_score[$id_report][$idst_user]['score'] - $report_details[$id_report]['averange']), 2), 2);
								}
							};break;
						}
					} else {
						
						$cont[] = '-';
					}
				};break;
			}
		}
		$tb_score->addBody($cont);
	}
	// XXX: Display statistics
	$stats['passed'] 		= array($lang->def('_PASSED'));//, ''
	$stats['not_passed'] 	= array($lang->def('_NOT_PASSED'));//, ''
	$stats['not_checked'] 	= array($lang->def('_NOT_CHECKED'));//, ''
	$stats['averange'] 		= array($lang->def('_AVERANGE'));//, ''
	$stats['varianza'] 		= array($lang->def('_STANDARD_DEVIATION'));//, ''
	$stats['max_score'] 	= array($lang->def('_MAX_SCORE'));//, ''
	$stats['min_score'] 	= array($lang->def('_MIN_SCORE'));//, ''
	foreach($reports as $id_report => $info_report) {
		
		switch($info_report['source_of']) {
			case "test" : {
				
				$id_test = $info_report['id_source'];
				
				if(isset($test_details[$id_test]['passed']) || isset($test_details[$id_test]['not_passed'])) {
					
					if(!isset($test_details[$id_test]['passed'])) $test_details[$id_test]['passed'] = 0;
					if(!isset($test_details[$id_test]['not_passed'])) $test_details[$id_test]['not_passed'] = 0;
					
					$test_details[$id_test]['varianza'] /= ($test_details[$id_test]['passed'] + $test_details[$id_test]['not_passed']);
					$test_details[$id_test]['varianza'] = sqrt($test_details[$id_test]['varianza']);
				}
				$stats['passed'][] 		= ( isset($test_details[$id_test]['passed']) ? round($test_details[$id_test]['passed'], 2) : '-' );
				$stats['not_passed'][] = ( isset($test_details[$id_test]['not_passed']) ? round($test_details[$id_test]['not_passed'], 2) : '-' );
				$stats['not_checked'][] = ( isset($test_details[$id_test]['not_checked']) ? round($test_details[$id_test]['not_checked'], 2) : '-' );
				$stats['averange'][] 	= ( isset($test_details[$id_test]['averange']) ? round($test_details[$id_test]['averange'], 2) : '-' );
				$stats['varianza'][]	= ( isset($test_details[$id_test]['varianza']) ? round($test_details[$id_test]['varianza'], 2) : '-' );
				$stats['max_score'][] 	= ( isset($test_details[$id_test]['max_score']) ? round($test_details[$id_test]['max_score'], 2) : '-' );
				$stats['min_score'][] 	= ( isset($test_details[$id_test]['min_score']) ? round($test_details[$id_test]['min_score'], 2) : '-' );
			};break;
			case "activity" :
			case "final_vote" : {
				
				if(isset($report_details[$id_report]['passed']) || isset($report_details[$id_report]['not_passed'])) { 
					
					if(!isset($report_details[$id_report]['passed'])) $report_details[$id_report]['passed'] = 0;
					if(!isset($report_details[$id_report]['not_passed'])) $report_details[$id_report]['not_passed'] = 0;
					
					$report_details[$id_report]['varianza'] /= ($report_details[$id_report]['passed'] + $report_details[$id_report]['not_passed']);
					$report_details[$id_report]['varianza'] = sqrt($report_details[$id_report]['varianza']);
				}
				$stats['passed'][] 		= ( isset($report_details[$id_report]['passed']) ? round($report_details[$id_report]['passed'], 2) : '-' );
				$stats['not_passed'][] = ( isset($report_details[$id_report]['not_passed']) ? round($report_details[$id_report]['not_passed'], 2) : '-' );
				$stats['not_checked'][] = ( isset($report_details[$id_report]['not_checked']) ? round($report_details[$id_report]['not_checked'], 2) : '-' );
				$stats['averange'][] 	= ( isset($report_details[$id_report]['averange']) ? round($report_details[$id_report]['averange'], 2) : '-' );
				$stats['varianza'][]	= ( isset($report_details[$id_report]['varianza']) ? round(sqrt($report_details[$id_report]['varianza']), 2) : '-' );
				$stats['max_score'][] 	= ( isset($report_details[$id_report]['max_score']) ? round($report_details[$id_report]['max_score'], 2) : '-' );
				$stats['min_score'][] 	= ( isset($report_details[$id_report]['min_score']) ? round($report_details[$id_report]['min_score'], 2) : '-' );
			};break;
		}
	}
	$tb_stat = new TypeOne(0, $lang->def('_SUMMARY_VOTE'), $lang->def('_COURSE_REPORT_SUMMARY'));
	$tb_stat->setColsStyle($type_h);
	$cont_h[0] = $lang->def('_STATS');
	$tb_stat->addHead($cont_h);
	
	//$tb->addBodyExpanded('<span class="text_bold title_big">'.$lang->def('_SUMMARY_VOTE').'</span>', 'align_center');
	$tb_stat->addBody($stats['passed']);
	$tb_stat->addBody($stats['not_passed']);
	$tb_stat->addBody($stats['not_checked']);
	$tb_stat->addBody($stats['averange']);
	$tb_stat->addBody($stats['varianza']);
	$tb_stat->addBody($stats['max_score']);
	$tb_stat->addBody($stats['min_score']);
	
	// Write in output
	$out->add( getTitleArea($lang->def('_COURSE_REPORT'), 'coursereport')
		.'<div class="std_block">' );
		
	if(checkPerm('mod', true)) {
		$out->add(
			'<div class="add_container_top">'
			.'<a href="index.php?modname=coursereport&amp;op=addactivity" title="'.$lang->def('_ADD_ACTIVITY_TITLE').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" /> '
			.$lang->def('_ADD_ACTIVITY').'</a><br/>'
			.'<a href="index.php?modname=coursereport&amp;op=export" title="'.$lang->def('_EXPORT_STATS_TITLE').'" onclick="window.open(this.href); return false;">'
			.'<img src="'.getPathImage().'report/export_cvs.gif" alt="'.$lang->def('_ADD').'" /> '
      .$lang->def('_EXPORT_STATS').'</a><br/>'
			.'</div>'
		);
	}
	$out->add(
		$tb_report->getTable().'<br /><br />'
		.$tb_score->getTable().'<br /><br />'
		.$tb_stat->getTable().'<br /><br />' );
	
	if(checkPerm('mod', true)) {
		$out->add(
			'<div class="add_container">'
			.'<a href="index.php?modname=coursereport&amp;op=addactivity" title="'.$lang->def('_ADD_ACTIVITY_TITLE').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" /> '
			.$lang->def('_ADD_ACTIVITY').'</a><br/>'
			.'<a href="index.php?modname=coursereport&amp;op=export" title="'.$lang->def('_EXPORT_STATS_TITLE').'" onclick="window.open(this.href); return false;">'
			.'<img src="'.getPathImage().'report/export_cvs.gif" alt="'.$lang->def('_ADD').'" /> '
      .$lang->def('_EXPORT_STATS').'</a><br/>'
			.'</div>'
		);
	}
	$out->add( '</div>');
}

function saveTestUpdate($id_test, &$test_man) {

		// Save report modification
		if(isset($_POST['user_score'])) {
			
			$query_upd_report = "
			UPDATE ".$GLOBALS['prefix_lms']."_coursereport
			SET weight = '".$_POST['weight']."', 
				show_to_user = '".$_POST['show_to_user']."', 
				use_for_final = '".$_POST['use_for_final']."'"
			.(isset($_POST['max_score']) && $_POST['max_score'] > 0 ? ", max_score = '".(float)$_POST['max_score']."'" : "")
			." WHERE  id_course = '".$_SESSION['idCourse']."' AND id_source = '".$id_test."' AND source_of = 'test'";
			$re = mysql_query($query_upd_report);
			
			// save user score modification
			$re &= $test_man->saveTestUsersScores($id_test, $_POST['user_score'], $_POST['date_attempt'], $_POST['comment']);
		} else {
			$query_upd_report = "
			UPDATE ".$GLOBALS['prefix_lms']."_coursereport
			SET weight = '".$_POST['weight']."', 
				show_to_user = '".$_POST['show_to_user']."', 
				use_for_final = '".$_POST['use_for_final']."'"
			.(isset($_POST['max_score']) && $_POST['max_score'] > 0 ? ", max_score = '".(float)$_POST['max_score']."'" : "")
			." WHERE  id_course = '".$_SESSION['idCourse']."' AND id_source = '".$id_test."' AND source_of = 'test'";
			$re = mysql_query($query_upd_report);
			
		}
		return $re;
}

function testvote() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	// XXX: Initializaing
	$id_test 		= importVar('id_test', true, 0);
	$lang 			=& DoceboLanguage::createInstance('coursereport', 'lms');
	$out 			=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	// XXX: Instance management
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	$test_man 		= new GroupTestManagement();
	$report_man 	= new CourseReportManager();
	
	// XXX: Find students
	$id_students	=& $report_man->getStudentId();
	$students_info 	=& $acl_man->getUsers($id_students);
	
	// XXX: Find test
	$test_info		=& $test_man->getTestInfo(array($id_test));
	
	
	// XXX: Write in output
	$page_title = array(
		'index.php?modname=coursereport&amp;op=coursereport' => $lang->def('_COURSE_REPORT'),
		strip_tags($test_info[$id_test]['title'])
	);
	$GLOBALS['page']->add(
			getTitleArea($page_title, 'coursereport')
			.'<div class="std_block">', 'content');
	//==========================================================================================
	// XXX: Reset track of user
	if(isset($_POST['reset_track'])) {
		
		$re = saveTestUpdate($id_test, $test_man);
		list($id_user, ) = each($_POST['reset_track']);
		
		$user_info = $acl_man->getUser($id_user, false);
		
		$GLOBALS['page']->add(
			Form::openForm('test_vote', 'index.php?modname=coursereport&amp;op=testvote')
			.Form::getHidden('id_test', 'id_test', $id_test)
			.Form::getHidden('id_user', 'id_user', $id_user)
			.getDeleteUi(	$lang->def('_AREYOUSURE_RESET_TRACK'),
							
							'<span>'.$lang->def('_DELETE_TRACK_OF_TEST').' : </span>'.strip_tags($test_info[$id_test]['title']).'<br />'
							.'<span>'.$lang->def('_OF_USER').' : </span>'.( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
									? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
									: $acl_man->relativeId($user_info[ACL_INFO_USERID]) ),
										false, 
							'confirm_reset',
							'undo_reset'	)
			.Form::closeForm()
			.'</div>', 'content');
		return;
	}
	if(isset($_POST['confirm_reset'])) {
		
		$id_user = importVar('id_user', true, 0);
		if($test_man->deleteTestTrack($id_test, $id_user)) {
			
			$GLOBALS['page']->add(getResultUi($lang->def('_DEL_RESULT_OK')), 'content');//($lang->def('_RESET_TRACK_SUCCESS')), 'content');
		} else {
			
			$GLOBALS['page']->add(getErrorUi($lang->def('_RESET_TRACK_FAIL')), 'content');
		}
	}
	
	//==========================================================================================
	
	if(isset($_POST['save'])) {
		
		$re = saveTestUpdate($id_test, $test_man);
		jumpTo('index.php?modname=coursereport&amp;op=coursereport&resul='.( $re ? 'ok' : 'err' ));
	}
		
	// retirive activity info
	$query_report = "
	SELECT id_report, title, max_score, required_score, weight, show_to_user, use_for_final, source_of, id_source 
	FROM ".$GLOBALS['prefix_lms']."_coursereport 
	WHERE id_course = '".$_SESSION['idCourse']."' 
	AND source_of = 'test' AND id_source = '".$id_test."'";
	
	$info_report = mysql_fetch_assoc(mysql_query($query_report));
	
	$query =	"SELECT question_random_number"
				." FROM ".$GLOBALS['prefix_lms']."_test"
				." WHERE idTest = '".$id_test."'";
	
	list($question_random_number) = mysql_fetch_row(mysql_query($query));
	
	/* XXX: scores */
	$tb = new TypeOne(0, $lang->def('_STUDENTS_VOTE'), $lang->def('_STUDENTS_VOTE_SUMMARY'));
	$type_h = array('', 'align_center' , 'align_center', 'align_center', '', 'image');
	$cont_h = array( 	$lang->def('_STUDENTS'), 
						$lang->def('_SCORE'),
						$lang->def('_SHOW_ANSWER'), 
						$lang->def('_DATE'), 
						$lang->def('_COMMENT'),
						'<img src="'.getPathImage('lms').'test/reset_track.gif" alt="'.$lang->def('_RESET_TRACK').'" title="'.$lang->def('_RESET_TRACK_TITLE').'" />' );
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	
	$out->add(
		Form::openForm('test_vote', 'index.php?modname=coursereport&amp;op=testvote')
		.Form::getHidden('id_test', 'id_test', $id_test)
	);
	
	$out->add(
		// main form
		Form::openElementSpace()
		.Form::getOpenFieldSet($lang->def('_TEST_INFO'))
		
		.Form::getLinebox(	$lang->def('_TITLE_ACT'),
							strip_tags($test_info[$id_test]['title']) )
		.($question_random_number ? Form::getTextfield($lang->def('_MAX_SCORE'), 'max_score', 'max_score', '11', $info_report['max_score']) : Form::getLinebox($lang->def('_MAX_SCORE'), $info_report['max_score']))
		.Form::getLinebox(	$lang->def('_REQUIRED_SCORE'),
							$info_report['required_score'] )
		
		.Form::getTextfield(	$lang->def('_WEIGHT'),
								'weight', 
								'weight', 
								'11', 
								$info_report['weight'] )
		.Form::getDropdown(		$lang->def('_SHOW_TO_USER'),
								'show_to_user', 
								'show_to_user', 
								array('true' => $lang->def('_YES'), 'false' => $lang->def('_NO')),
								$info_report['show_to_user'] )
		.Form::getDropdown(		$lang->def('_USE_FOR_FINAL'),
								'use_for_final', 
								'use_for_final', 
								array('true' => $lang->def('_YES'), 'false' => $lang->def('_NO')),
								$info_report['use_for_final'] )
		.Form::getCloseFieldSet()
		.Form::closeElementSpace()
	);
	
	// XXX: retrive scores
	$tests_score 	=& $test_man->getTestsScores(array($id_test), $id_students);
	
	// XXX: Display user scores
	$i = 0;
	while(list($idst_user, $user_info) = each($students_info)) {
		
		$user_name = ( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
						? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
						: $acl_man->relativeId($user_info[ACL_INFO_USERID]) );
		
		$cont = array(Form::getLabel('user_score_'.$idst_user, $user_name));
		
		$id_test = $info_report['id_source'];
		if(isset($tests_score[$id_test][$idst_user])) {
			
			switch($tests_score[$id_test][$idst_user]['score_status']) {
				case "not_complete" : {
					$cont[] = '-';
				};break;
				case "not_checked" 	: {
					$cont[] = '<span class="cr_not_check">'.$lang->def('_NOT_CHECKED').'</span><br />'
								.Form::getInputTextfield(	'textfield_nowh', 
															'user_score_'.$idst_user, 
															'user_score['.$idst_user.']', 
															$tests_score[$id_test][$idst_user]['score'], 
															strip_tags($lang->def('_SCORE')), 
															'8',
															' tabindex="'.$i++.'" ' );
				};break;
				case "not_passed" 	:
				case "passed" 		: {
				/*
					$cont[] = Form::getInputDropdown(	'dropdown', 
															'user_score', 
															'user_score', 
															array('passed' => $lang->def('_PASSED'), 'not_passed' => $lang->def('_NOT_PASSED')),
															$tests_score[$id_test][$idst_user]['score_status'],
															'');
															*/
					$cont[] = Form::getInputTextfield(	'textfield_nowh', 
														'user_score_'.$idst_user, 
														'user_score['.$idst_user.']', 
														$tests_score[$id_test][$idst_user]['score'], 
														strip_tags($lang->def('_SCORE')), 
														'8',
														' tabindex="'.$i++.'" ' );
														
				};break;
				case "valid" 		: {
					$cont[] = Form::getInputTextfield(	'textfield_nowh', 
														'user_score_'.$idst_user, 
														'user_score['.$idst_user.']', 
														$tests_score[$id_test][$idst_user]['score'], 
														strip_tags($lang->def('_SCORE')), 
														'8',
														' tabindex="'.$i++.'" ' );
				};break;
				default : {
					
					$cont[] = '-';
				}
			}
			if($tests_score[$id_test][$idst_user]['score_status'] != 'not_comlete') {
				
				$cont[] = Form::getButton('view_anser_'.$idst_user, 'view_answer['.$idst_user.']', $lang->def('_SHOW_ANSWER'), 'button_nowh');
				$cont[] = Form::getInputDatefield(	'textfield_nowh', 
													'date_attempt_'.$idst_user, 
													'date_attempt['.$idst_user.']', 
													$GLOBALS['regset']->databaseToRegional($tests_score[$id_test][$idst_user]['date_attempt']) );
													
				$cont[] = Form::getInputTextarea(	'comment_'.$idst_user, 
													'comment['.$idst_user.']', 
													$tests_score[$id_test][$idst_user]['comment'], 
													'textarea_wh_full', 
													2);
				
				$cont[] = '<input 	class="reset_track" 
									type="image" 
									src="'.getPathImage('lms').'test/reset_track.gif" 
									alt="'.$lang->def('_RESET_TRACK').'" 
									id="reset_track_'.$idst_user.'" 
									name="reset_track['.$idst_user.']" 
									title="'.$lang->def('_RESET_TRACK_TITLE').'" />';
			}
		} else {
			
			$cont[] = '-';
			$cont[] = '-';
			$cont[] = '-';
			$cont[] = '-';
			$cont[] = '-';
		}
		$tb->addBody($cont);
	}
	
	$out->add(
		Form::openButtonSpace()
		.Form::getButton('save_top', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo_top', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		
		.$tb->getTable()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>');
	
}

function testDetail()
{
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php');
	
	$lang =& DoceboLanguage::createInstance('coursereport', 'lms');
	
	$out =& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$id_test = importVar('id_test', true, 0);
	
	$test_man = new GroupTestManagement();
	$acl_man = $GLOBALS['current_user']->getAclManager();
	
	$quests = array();
	$answers = array();
	$tracks = array();
	
	$test_info =& $test_man->getTestInfo(array($id_test));

    $page_title = array(	'index.php?modname=coursereport&amp;op=coursereport' => $lang->def('_COURSE_REPORT'),
							'index.php?modname=coursereport&amp;op=testdetail&amp;id_test='.$id_test => $test_info[$id_test]['title']
    );

    $out->add(	getTitleArea($page_title, 'coursereport')
				.'<div class="std_block">'
    );
    
    $query_test =	"SELECT title"
					." FROM ".$GLOBALS['prefix_lms']."_test"
					." WHERE idTest = '".$id_test."'";
    
    list($titolo_test) = mysql_fetch_row(mysql_query($query_test));
    
    $query_quest =	"SELECT idQuest, type_quest, title_quest"
					." FROM ".$GLOBALS['prefix_lms']."_testquest"
					." WHERE idTest = '".$id_test."'"
					." ORDER BY sequence";
	
	$result_quest = mysql_query($query_quest);
	
	while (list($id_quest, $type_quest, $title_quest) = mysql_fetch_row($result_quest))
	{
		$quests[$id_quest]['idQuest'] = $id_quest;
		$quests[$id_quest]['type_quest'] = $type_quest;
		$quests[$id_quest]['title_quest'] = $title_quest;
		
		$query_answer =	"SELECT idAnswer, is_correct, answer"
						." FROM ".$GLOBALS['prefix_lms']."_testquestanswer"
						." WHERE idQuest = '".$id_quest."'"
						." ORDER BY sequence";
		
		$result_answer = mysql_query($query_answer);
		
		while (list($id_answer, $is_correct, $answer) = mysql_fetch_row($result_answer))
		{
			$answers[$id_quest][$id_answer]['idAnswer'] = $id_answer;
			$answers[$id_quest][$id_answer]['is_correct'] = $is_correct;
			$answers[$id_quest][$id_answer]['answer'] = $answer;
		}
	}
	
	
	
	$query_track =	"SELECT idTrack"
					." FROM ".$GLOBALS['prefix_lms']."_testtrack"
					." WHERE idTest = '".$id_test."'";
	
	$result_track = mysql_query($query_track);
	
	while(list($id_track) = mysql_fetch_row($result_track))
	{
		$query_track_answer =	"SELECT idQuest, idAnswer"
								." FROM ".$GLOBALS['prefix_lms']."_testtrack_answer"
								." WHERE idTrack = '".$id_track."'";
		
		$result_track_answer = mysql_query($query_track_answer);
		
		while(list($id_quest, $id_answer) = mysql_fetch_row($result_track_answer))
			$tracks[$id_track][$id_quest] = $id_answer;
	}
}

function testreview() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	// XXX: Initializaing
	$id_test 		= importVar('id_test', true, 0);
	$lang 			=& DoceboLanguage::createInstance('coursereport', 'lms');
	$out 			=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	// XXX: Instance management
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	$test_man 		= new GroupTestManagement();
	$report_man 	= new CourseReportManager();
	
	// XXX: Save input if needed
	if(isset($_POST['view_answer'])) {
		
		$re = saveTestUpdate($id_test, $test_man);
		list($id_user, ) = each($_POST['view_answer']);
	} else {
		$id_user = importVar('id_user', true, 0);
	}
	
	if(isset($_POST['save_new_scores'])) {
		
		$re = $test_man->saveReview($id_test, $id_user);
		jumpTo('index.php?modname=coursereport&amp;op=testvote&amp;id_test='.$id_test.'&result='.( $re ? 'ok' : 'err' ));
	}
	
	$user_name = $acl_man->getUserName($id_user);
	
	// XXX: Find test
	$test_info =& $test_man->getTestInfo(array($id_test));
	
	// XXX: Write in output
	$page_title = array(
		'index.php?modname=coursereport&amp;op=coursereport' => $lang->def('_COURSE_REPORT'),
		'index.php?modname=coursereport&amp;op=testvote&amp;id_test='.$id_test =>$test_info[$id_test]['title'],
		$user_name 
	);
	$out->add(
		getTitleArea($page_title, 'coursereport')
		.'<div class="std_block">'
		.Form::openForm('test_vote', 'index.php?modname=coursereport&amp;op=testreview')
		.Form::getHidden('id_test', 'id_test', $id_test)
		.Form::getHidden('id_user', 'id_user', $id_user)
	);
	$test_man->editReview($id_test, $id_user);
	$out->add(
		Form::openButtonSpace()
		.Form::getButton('save_new_scores', 'save_new_scores', $lang->def('_SAVE'))
		.Form::getButton('undo_testreview', 'undo_testreview', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>'
	);
}

function finalvote() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	// XXX: Initializaing
	$id_report 		= importVar('id_report', true, 0);
	$lang 			=& DoceboLanguage::createInstance('coursereport', 'lms');
	$out 			=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	// XXX: Instance management
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	$report_man 	= new CourseReportManager();
	
	// XXX: Find students
	$id_students	=& $report_man->getStudentId();
	$students_info 	=& $acl_man->getUsers($id_students);
	
	// XXX: Write in output
	$page_title = array(
		'index.php?modname=coursereport&amp;op=coursereport' => $lang->def('_COURSE_REPORT'),
		strip_tags($lang->def('_FINAL_VOTE'))
	);
	$out->add(
		getTitleArea($page_title, 'coursereport')
		.'<div class="std_block">'
		.Form::openForm('finalvote', 'index.php?modname=coursereport&amp;op=finalvote')
		.Form::getHidden('id_report', 'id_report', $id_report)
	);
	
	// XXX: Save input if needed
	if(isset($_POST['save'])) {
		
		// Save report modification
		$query_upd_report = "
		UPDATE ".$GLOBALS['prefix_lms']."_coursereport
		SET max_score = '".$_POST['max_score']."',
			required_score = '".$_POST['required_score']."', 
			show_to_user = '".$_POST['show_to_user']."' 
		WHERE  id_course = '".$_SESSION['idCourse']."' AND id_report = '".$id_report."' 
			AND source_of = 'final_vote' AND id_source = '0'";
		mysql_query($query_upd_report);
		// save user score modification
		
		$re = $report_man->saveReportScore($id_report, $_POST['user_score'], $_POST['date_attempt'], $_POST['comment']);
		
		jumpTo('index.php?modname=coursereport&amp;op=coursereport&result='.( $re ? 'ok' : 'err' ));
	}
	
	if(isset($_POST['save'])) { 
		
		// retirive activity info
		$info_report = array(
			'max_score' => importVar('max_score', true), 
			'required_score' => importVar('required_score', true), 
			'weight' => importVar('weight', true), 
			'show_to_user' => importVar('show_to_user', false, 'true'), 
			'id_source' => 0,
			'source_of' => 'final_vote' 
		);
	} else {
		
		// retirive activity info
		$query_report = "
		SELECT id_report, title, max_score, required_score, weight, show_to_user, use_for_final, source_of, id_source 
		FROM ".$GLOBALS['prefix_lms']."_coursereport 
		WHERE id_course = '".$_SESSION['idCourse']."' 
				AND source_of = 'final_vote' AND id_source = '0'";
		$info_report = mysql_fetch_assoc(mysql_query($query_report));
	}
	
	$out->add(
		// main form
		Form::openElementSpace()
		.Form::getOpenFieldSet($lang->def('_TEST_INFO'))
		
		.Form::getLinebox(	$lang->def('_TITLE_ACT'),
							$lang->def('_FINAL_VOTE') )
		.Form::getTextfield(	$lang->def('_MAX_SCORE'),
								'max_score', 
								'max_score', 
								'11', 
								$info_report['max_score'] )
		.Form::getTextfield(	$lang->def('_REQUIRED_SCORE'),
								'required_score', 
								'required_score', 
								'11', 
								$info_report['required_score'] )
		.Form::getDropdown(		$lang->def('_SHOW_TO_USER'),
								'show_to_user', 
								'show_to_user', 
								array('true' => $lang->def('_YES'), 'false' => $lang->def('_NO')),
								$info_report['show_to_user'] )
		.Form::getCloseFieldSet()
		.Form::closeElementSpace()
	);
	
	/* XXX: scores */
	$tb = new TypeOne(0, $lang->def('_STUDENTS_VOTE'), $lang->def('_STUDENTS_VOTE_SUMMARY'));
	$type_h = array('', 'align_center' , 'align_center', 'align_center', '');
	$cont_h = array( 	$lang->def('_STUDENTS'), 
						$lang->def('_SCORE'),
						$lang->def('_DATE'), 
						$lang->def('_COMMENT') );
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	
	// XXX: retrive scores
	$report_score 	=& $report_man->getReportsScores(array($id_report));
	
	// XXX: Display user scores
	$i = 0;
	while(list($idst_user, $user_info) = each($students_info)) {
		
		$user_name = ( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
						? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
						: $acl_man->relativeId($user_info[ACL_INFO_USERID]) );
		$cont = array(Form::getLabel('user_score_'.$idst_user, $user_name));
		
		$cont[] = Form::getInputTextfield(	'textfield_nowh', 
													'user_score_'.$idst_user, 
													'user_score['.$idst_user.']', 
													( isset($report_score[$id_report][$idst_user]['score']) 
														? $report_score[$id_report][$idst_user]['score'] : '' ), 
													strip_tags($lang->def('_SCORE')), 
													'8',
													' tabindex="'.$i++.'" ' );
		$cont[] = Form::getInputDatefield(	'textfield_nowh', 
													'date_attempt_'.$idst_user, 
													'date_attempt['.$idst_user.']', 
													$GLOBALS['regset']->databaseToRegional(
														( isset($report_score[$id_report][$idst_user]['date_attempt']) 
															? $report_score[$id_report][$idst_user]['date_attempt'] : '' ), 'date'));
		$cont[] = Form::getInputTextarea(	'comment_'.$idst_user, 
											'comment['.$idst_user.']', 
											( isset($report_score[$id_report][$idst_user]['comment']) 
															? $report_score[$id_report][$idst_user]['comment'] : '' ), 
											'textarea_wh_full', 
											2);
	
		$tb->addBody($cont);
	}
	
	$out->add(
		Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		
		.$tb->getTable()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>');
	
}
/*
function userscore() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	// XXX: Initializaing
	$id_test 		= importVar('id_test', true, 0);
	$lang 			=& DoceboLanguage::createInstance('coursereport', 'lms');
	$out 			=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	// XXX: Instance management
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	$test_man 		= new GroupTestManagement();
	$report_man 	= new CourseReportManager();
	
	// XXX: Find test from organization
	$org_tests 		=& $report_man->getTest();
	$tests_info		=& $test_man->getTestInfo($org_tests);
	
	// XXX: Find students
	$id_student		= importVar('id_user', true, 0);
	$students_info 	=& $acl_man->getUsers(array($id_student));
	$user_info = $students_info[$id_student];
	$user_name = ( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
						? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
						: $acl_man->relativeId($user_info[ACL_INFO_USERID]) );
	
	// XXX: Retrive all colums (test and so), and set it
	$type_h = array('line_users');
	$cont_h = array($lang->def('_DETAILS'));
	
	$query_report = "
	SELECT id_report, max_score, required_score, weight, show_to_user, use_for_final, source_of, id_source 
	FROM ".$GLOBALS['prefix_lms']."_coursereport 
	WHERE id_course = '".$_SESSION['idCourse']."' 
	ORDER BY sequence ";
	$re_report = mysql_query($query_report);
	while($info_report = mysql_fetch_assoc($re_report)) {
		
		$id 									= $info_report['id_source'];
		$reports[$info_report['id_report']]	= $info_report;
		$reports_id[] 							= $info_report['id_report'];
		
		// set action colums
		$type_h[] = 'align_center';
		switch($info_report['source_of']) {
			case "test" : {
				
				$cont_h[] = strip_tags($tests_info[$info_report['id_source']]['title']);
			};break;
			case "final_vote" 	: {
				
				$cont_h[] = strip_tags($lang->def('_FINAL_VOTE'));
			};break;
		}
		
	}
	
	// XXX: Set table intestation
	$tb_report = new TypeOne(0, $lang->def('_COURSE_REPORT_CAPTION'), $lang->def('_COURSE_REPORT_SUMMARY'));
	
	$tb_report->setColsStyle($type_h);
	$tb_report->addHead($cont_h);
	
	//$tb->addBodyExpanded('<span class="text_bold title_big">'.$lang->def('_STUDENTS_VOTE').'</span>', 'align_center');
	$tb_score = new TypeOne(0, $lang->def('_STUDENTS_VOTE'), $lang->def('_COURSE_REPORT_SUMMARY'));
	$tb_score->setColsStyle($type_h);
	$cont_h[0] = $lang->def('_STUDENTS');
	$tb_score->addHead($cont_h);
	
	// XXX: Retrive Test info and scores
	
	// XXX: Write in output
	$page_title = array(
		'index.php?modname=coursereport&amp;op=coursereport' => $lang->def('_COURSE_REPORT'),
		$user_name
	);
	$out->add(
		getTitleArea($page_title, 'coursereport')
		.'<div class="std_block">'
		.Form::openForm('user_vote', 'index.php?modname=coursereport&amp;op=userscore')
		.Form::getHidden('id_test', 'id_test', $id_test)
	);
	
	
	$out->add(
		$tb_score->getTable()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>'
	);
}
*/
// NOTE: round and math

function roundtest() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	// XXX: Initializaing
	$id_test 		= importVar('id_test', true, 0);
	$lang 			=& DoceboLanguage::createInstance('coursereport', 'lms');
	$out 			=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	// XXX: Instance management
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	$test_man 		= new GroupTestManagement();
	$report_man 	= new CourseReportManager();
	
	// XXX: Find test from organization
	$re = $test_man->roundTestScore($id_test);
	
	jumpTo('index.php?modname=coursereport&amp;op=coursereport&amp;result='.( $re ? 'ok' : 'err' ));
}

function roundreport() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	// XXX: Initializaing
	$id_report 		= importVar('id_report', true, 0);
	
	// XXX: Instance management
	$report_man		= new CourseReportManager();
	
	// XXX: Find test from organization
	$re = $report_man->roundReportScore($id_report);
	
	jumpTo('index.php?modname=coursereport&amp;op=coursereport&amp;result='.( $re ? 'ok' : 'err' ));
}

/**
 *	final_score = 
 *
 *	sum( (score[n] * weight[n]) / total_weigth )
 *	----------------------------------------------------  * final_max_score 
 *	sum( (max_score[n] * weight[n]) / total_weigth )
 *
 * equal to :
 *	sum( score[n] * weight[n] )
 *	--------------------------------  * final_max_score 
 *	sum( max_score[n] * weight[n] )
 */

function redofinal() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	// XXX: Initializaing 
	$lang 			=& DoceboLanguage::createInstance('coursereport', 'lms');
	
	// XXX: Instance management
	$acl_man 	= $GLOBALS['current_user']->getAclManager();
	$test_man 	= new GroupTestManagement();
	$report_man = new CourseReportManager();
	
	// XXX: Find students
	$id_students	=& $report_man->getStudentId();
	
	// XXX: retrive info about the final score
	 $query_final = "
	SELECT id_report, max_score 
	FROM ".$GLOBALS['prefix_lms']."_coursereport 
	WHERE id_course = '".$_SESSION['idCourse']."' AND source_of = 'final_vote'";
	$info_final = mysql_fetch_assoc(mysql_query($query_final));
	
	// XXX: Retrive all reports (test and so), and set it
	
	$query_report = "
	SELECT id_report, max_score, weight, source_of, id_source 
	FROM ".$GLOBALS['prefix_lms']."_coursereport 
	WHERE id_course = '".$_SESSION['idCourse']."' AND use_for_final = 'true' AND source_of <> 'final_vote' 
	ORDER BY sequence ";
	
	$re_report = mysql_query($query_report);
	if(!mysql_num_rows($re_report)) {
		jumpTo('index.php?modname=coursereport&amp;op=coursereport&amp;result=ok');
	}
	
	$sum_max_score = 0;
	$included_test 	= array();
	$other_source = array();
	while($info_report = mysql_fetch_assoc($re_report)) {
		
		$sum_max_score 	+= $info_report['max_score'] * $info_report['weight'];
		
		$reports_info[$info_report['id_report']] = $info_report;
		
		switch($info_report['source_of']) {
			case "activity" : $other_source[$info_report['id_report']] = $info_report['id_report'];break;
			case "test" : $included_test[$info_report['id_source']] = $info_report['id_source'];break;
		}
	}
	
	// XXX: Retrive Test score
	if(!empty($included_test))
		$tests_score =& $test_man->getTestsScores($included_test, $id_students);
	
	// XXX: Retrive other score
	if(!empty($other_source))
		$other_score =& $report_man->getReportsScores($other_source);
	
	$final_score = array();
	while(list(, $id_user) = each($id_students)) {
		
		$user_score = 0;
		while(list($id_report, $rep_info) = each($reports_info)) {
			
			$id_source = $rep_info['id_source'];
			switch($rep_info['source_of']) {
				
				case "activity" : {
					
					if(isset($other_score[$id_report][$id_user]) && ($other_score[$id_report][$id_user]['score_status'] == 'valid')) {
						$user_score += ($other_score[$id_report][$id_user]['score'] * $rep_info['weight']);
					} else {
						$user_score += 0;
					}
				};break;
				case "test" : {
					
					if(isset($tests_score[$id_source][$id_user]) && ($tests_score[$id_source][$id_user]['score_status'] == 'valid')) {
						$user_score += ($tests_score[$id_source][$id_user]['score'] * $rep_info['weight']);
					} else {
						$user_score += 0;
					}
				};break;
			}
		}
		
		reset($reports_info);
		// user final score
		if($sum_max_score != 0) {
			
			$final_score[$id_user] = round(($user_score / $sum_max_score) * $info_final['max_score'], 2);
		} else {
			
			$final_score[$id_user] = 0;
		}
	}
	// Save final scores
	$exists_final = array();
	$query_final_score = "
	SELECT id_user 
	FROM ".$GLOBALS['prefix_lms']."_coursereport_score 
	WHERE id_report = '".$info_final['id_report']."'";
	$re_final = mysql_query($query_final_score);
	while(list($id_user) = mysql_fetch_row($re_final)) {
		
		$exists_final[$id_user] = $id_user;
	}
	$re = true;
	while(list($user, $score) = each($final_score)) {
		
		if(isset($exists_final[$user])) {
			
			$query_scores = "
			UPDATE ".$GLOBALS['prefix_lms']."_coursereport_score
			SET score = '".$score."', 
				date_attempt = '".date("Y-m-d H:i:s")."'
			WHERE id_report = '".$info_final['id_report']."' AND id_user = '".$user."'";
			$re &= mysql_query($query_scores);
		} else {
			
			$query_scores = "
			INSERT INTO  ".$GLOBALS['prefix_lms']."_coursereport_score
			( id_report, id_user, score, date_attempt ) VALUES (
				'".$info_final['id_report']."', 
				'".$user."', 
				'".$score."',
				'".date("Y-m-d H:i:s")."' )";
			$re &= mysql_query($query_scores);
		}
	}
	jumpTo('index.php?modname=coursereport&amp;op=coursereport&amp;result='.( $re ? 'ok' : 'err' ));
}

function modactivity() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	// XXX: Initializaing
	$id_report 		= importVar('id_report', true, 0);
	$lang 			=& DoceboLanguage::createInstance('coursereport', 'lms');
	$out 			=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	// XXX: undo 
	if(isset($_POST['undo'])) {
		jumpTo('index.php?modname=coursereport&amp;op=coursereport');
	}
	
	// XXX: Retrive all colums (test and so), and set it
	if($id_report == 0) {
		
		$info_report = array(
			'id_report' => importVar('id_report', true, 0), 
			'title' => importVar('title'),
			'max_score' => importVar('max_score', true), 
			'required_score' => importVar('required_score', true), 
			'weight' => importVar('weight', true), 
			'show_to_user' => importVar('show_to_user', false, 'true'), 
			'use_for_final' => importVar('use_for_final', false, 'true') 
		);
	} elseif(!isset($_POST['save'])) {
		
		$query_report = "
		SELECT id_report, title, max_score, required_score, weight, show_to_user, use_for_final 
		FROM ".$GLOBALS['prefix_lms']."_coursereport 
		WHERE id_course = '".$_SESSION['idCourse']."' AND id_report = '".$id_report."' 
				AND source_of = 'activity' AND id_source = '0'";
		$info_report = mysql_fetch_assoc(mysql_query($query_report));
	}
	
	$page_title = array(
		'index.php?modname=coursereport&amp;op=coursereport' => $lang->def('_COURSE_REPORT'),
		strip_tags($lang->def('_ADD_ACTIVITY'))
	);
	$out->add(
		getTitleArea($page_title, 'coursereport')
		.'<div class="std_block">'
		
		.getBackUi('index.php?modname=coursereport&amp;op=coursereport', $lang->def('_BACK'))
	);
	// XXX: Save input if needed
	if(isset($_POST['save'])) {
		
		$report_man = new CourseReportManager();
		// check input
		if($_POST['title'] == '') $_POST['title'] = $lang->def('_NOTITLE');
		
		$re_check = $report_man->checkActivityData($_POST);
		if(!$re_check['error']) {
			
			if($id_report == 0) $re = $report_man->addActivity($_SESSION['idCourse'], $_POST);
			else $re = $report_man->updateActivity($id_report, $_SESSION['idCourse'], $_POST);
			jumpTo('index.php?modname=coursereport&amp;op=coursereport&result='.( $re ? 'ok' : 'err' ));
		} else {
			
			$out->add(getErrorUi($re_check['message']));
		}
	}
	
	// XXX: Write in output
	$page_title = array(
		'index.php?modname=coursereport&amp;op=coursereport' => $lang->def('_COURSE_REPORT'),
		strip_tags($lang->def('_ADD_ACTIVITY'))
	);
	$out->add(
		Form::openForm('addactivity', 'index.php?modname=coursereport&amp;op=addactivity')
		.Form::openElementSpace()
		.Form::getHidden('id_report', 'id_report', $id_report)
		.Form::getTextfield(	$lang->def('_TITLE_ACT'),
								'title', 
								'title', 
								'255', 
								$info_report['title'] )
		.Form::getTextfield(	$lang->def('_MAX_SCORE'),
								'max_score', 
								'max_score', 
								'11', 
								$info_report['max_score'] )
		.Form::getTextfield(	$lang->def('_REQUIRED_SCORE'),
								'required_score', 
								'required_score', 
								'11', 
								$info_report['required_score'] )
		.Form::getTextfield(	$lang->def('_WEIGHT'),
								'weight', 
								'weight', 
								'11', 
								$info_report['weight'] )
		.Form::getDropdown(		$lang->def('_SHOW_TO_USER'),
								'show_to_user', 
								'show_to_user', 
								array('true' => $lang->def('_YES'), 'false' => $lang->def('_NO')),
								$info_report['show_to_user'] )
		.Form::getDropdown(		$lang->def('_USE_FOR_FINAL'),
								'use_for_final', 
								'use_for_final', 
								array('true' => $lang->def('_YES'), 'false' => $lang->def('_NO')),
								$info_report['use_for_final'] )
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>');
}


function modactivityscore() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	// XXX: Initializaing
	$id_report 		= importVar('id_report', true, 0);
	$lang 			=& DoceboLanguage::createInstance('coursereport', 'lms');
	$out 			=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	// XXX: Instance management
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	$report_man 	= new CourseReportManager();
	
	// XXX: Find students
	$id_students	=& $report_man->getStudentId();
	$students_info 	=& $acl_man->getUsers($id_students);
	
	if(isset($_POST['save'])) { 
		
		// retirive activity info
		$info_report = array(
			'id_report' => importVar('id_report', true, 0), 
			'title' => importVar('title'),
			'max_score' => importVar('max_score', true), 
			'required_score' => importVar('required_score', true), 
			'weight' => importVar('weight', true), 
			'show_to_user' => importVar('show_to_user', false, 'true'), 
			'use_for_final' => importVar('use_for_final', false, 'true') 
		);
		// XXX: retrive scores
	} else {
		
		// retirive activity info
		$query_report = "
		SELECT id_report, title, max_score, required_score, weight, show_to_user, use_for_final 
		FROM ".$GLOBALS['prefix_lms']."_coursereport 
		WHERE id_course = '".$_SESSION['idCourse']."' AND id_report = '".$id_report."' 
				AND source_of = 'activity' AND id_source = '0'";
		$info_report = mysql_fetch_assoc(mysql_query($query_report));
		
		// XXX: retrive scores
		$report_score 	=& $report_man->getReportsScores(array($id_report));
	}
	
	// XXX: Write in output
	$page_title = array(
		'index.php?modname=coursereport&amp;op=coursereport' => $lang->def('_COURSE_REPORT'),
		strip_tags($info_report['title'])
	);
	$out->add(
		getTitleArea($page_title, 'coursereport')
		.'<div class="std_block">'
		.Form::openForm('activity', 'index.php?modname=coursereport&amp;op=modactivityscore')
	);
	
	// XXX: Save input if needed
	if(isset($_POST['save'])) {
		
		if($_POST['title'] == '') $_POST['title'] = $lang->def('_NOTITLE');
		
		$re_check = $report_man->checkActivityData($_POST);
		if(!$re_check['error']) {
			if(!$report_man->updateActivity($id_report, $_SESSION['idCourse'], $_POST)) {
				
				$out->add(getErrorUi($lang->def('_ERROR_SAVING_ACTIVITY_INFO')));
			} else {
				
				// save user score modification
				$re = $report_man->saveReportScore($id_report, $_POST['user_score'], $_POST['date_attempt'], $_POST['comment']);
				jumpTo('index.php?modname=coursereport&amp;op=coursereport&result='.( $re ? 'ok' : 'err' ));
			}
		} else {
			$out->add(getErrorUi($re_check['message']));
		}
	}
	
	$out->add(
		// main form
		Form::openElementSpace()
		.Form::getOpenFieldSet($lang->def('_ACTIVITY_INFO'))
		.Form::getHidden('id_report', 'id_report', $id_report)
		.Form::getTextfield(	$lang->def('_TITLE_ACT'),
								'title', 
								'title', 
								'255', 
								$info_report['title'] )
		.Form::getTextfield(	$lang->def('_MAX_SCORE'),
								'max_score', 
								'max_score', 
								'11', 
								$info_report['max_score'] )
		.Form::getTextfield(	$lang->def('_REQUIRED_SCORE'),
								'required_score', 
								'required_score', 
								'11', 
								$info_report['required_score'] )
		.Form::getTextfield(	$lang->def('_WEIGHT'),
								'weight', 
								'weight', 
								'11', 
								$info_report['weight'] )
		.Form::getDropdown(		$lang->def('_SHOW_TO_USER'),
								'show_to_user', 
								'show_to_user', 
								array('true' => $lang->def('_YES'), 'false' => $lang->def('_NO')),
								$info_report['show_to_user'] )
		.Form::getDropdown(		$lang->def('_USE_FOR_FINAL'),
								'use_for_final', 
								'use_for_final', 
								array('true' => $lang->def('_YES'), 'false' => $lang->def('_NO')),
								$info_report['use_for_final'] )
		.Form::getCloseFieldSet()
		.Form::closeElementSpace()
	);
	
	/* XXX: scores */
	$tb = new TypeOne(0, $lang->def('_STUDENTS_VOTE'), $lang->def('_STUDENTS_VOTE_SUMMARY'));
	$type_h = array('', 'align_center', 'align_center', '');
	$tb->setColsStyle($type_h);
	$cont_h = array( 	$lang->def('_STUDENTS'), 
						$lang->def('_SCORE'),
						$lang->def('_DATE'), 
						$lang->def('_COMMENT') );
	$tb->addHead($cont_h);
	
	// XXX: Display user scores
	$i = 0;
	while(list($idst_user, $user_info) = each($students_info)) {
		
		$user_name = ( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
						? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
						: $acl_man->relativeId($user_info[ACL_INFO_USERID]) );
		$cont = array(Form::getLabel('user_score_'.$idst_user, $user_name));
		
		$cont[] = Form::getInputTextfield(	'textfield_nowh', 
													'user_score_'.$idst_user, 
													'user_score['.$idst_user.']', 
													( isset($report_score[$id_report][$idst_user]['score']) 
														? $report_score[$id_report][$idst_user]['score'] 
														: (isset($_POST['user_score'][$idst_user]) ? $_POST['user_score'][$idst_user] : '') ), 
													strip_tags($lang->def('_SCORE')), 
													'8',
													' tabindex="'.$i++.'" ' );
		$cont[] = Form::getInputDatefield(	'textfield_nowh', 
													'date_attempt_'.$idst_user, 
													'date_attempt['.$idst_user.']', 
													$GLOBALS['regset']->databaseToRegional(
														( isset($report_score[$id_report][$idst_user]['date_attempt']) 
															? $report_score[$id_report][$idst_user]['date_attempt'] 
															: (isset($_POST['date_attempt'][$idst_user]) ? $_POST['date_attempt'][$idst_user] : '') ), 'date'));
		$cont[] = Form::getInputTextarea(	'comment_'.$idst_user, 
											'comment['.$idst_user.']', 
											( isset($report_score[$id_report][$idst_user]['comment']) 
															? $report_score[$id_report][$idst_user]['comment'] 
															: (isset($_POST['comment'][$idst_user]) ? stripslashes($_POST['comment'][$idst_user]) : '') ), 
											'textarea_wh_full', 
											2);
	
		$tb->addBody($cont);
	}
	
	$out->add(
		Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		
		.$tb->getTable()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>');
	
}

function delactivity() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	// XXX: Initializaing
	$id_report 		= importVar('id_report', true, 0);
	$lang 			=& DoceboLanguage::createInstance('coursereport', 'lms');
	$out 			=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	// XXX: Instance management
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	$report_man 	= new CourseReportManager();
	
	if(isset($_POST['confirm'])) {
		
		if(!$report_man->deleteReportScore($id_report)) {
			jumpTo('index.php?modname=coursereport&amp;op=coursereport&amp;result=err');
		}
		
		$re = $report_man->deleteReport($id_report);
		
		jumpTo('index.php?modname=coursereport&amp;op=coursereport&amp;result='.( $re ? 'ok' : 'err' ));
	}
	
	// retirive activity info
	$query_report = "
	SELECT id_report, title, max_score, required_score, weight, show_to_user, use_for_final 
	FROM ".$GLOBALS['prefix_lms']."_coursereport 
	WHERE id_course = '".$_SESSION['idCourse']."' AND id_report = '".$id_report."' 
			AND source_of = 'activity' AND id_source = '0'";
	$info_report = mysql_fetch_assoc(mysql_query($query_report));
	
	// XXX: Write in output
	$page_title = array(
		'index.php?modname=coursereport&amp;op=coursereport' => $lang->def('_COURSE_REPORT'),
		$lang->def('_DELETE_ACTIVITY_VOTE').' : '.strip_tags($info_report['title'])
	);
	$out->add(
		getTitleArea($page_title, 'coursereport')
		.'<div class="std_block">'
		.Form::openForm('delactivity', 'index.php?modname=coursereport&amp;op=delactivity')
		.Form::getHidden('id_report', 'id_report', $id_report)
		.getDeleteUi(	$lang->def('_ARE_YOU_SURE_ACT'),
				$lang->def('_TITLE_ACT').' : '.$info_report['title'],
				false,
				'confirm',
				'undo' )
		.Form::closeForm()
		.'</div>');
}

function movereport($direction) {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	
	// XXX: Initializaing
	$id_report 		= importVar('id_report', true, 0);
	$lang 			=& DoceboLanguage::createInstance('coursereport', 'lms');
	
	// XXX: Instance management
	$report_man 	= new CourseReportManager();
	
	list($seq) = mysql_fetch_row(mysql_query("
	SELECT sequence 
	FROM ".$GLOBALS['prefix_lms']."_coursereport 
	WHERE id_course = '".$_SESSION['idCourse']."' AND id_report = '".$id_report."'"));
	
	if($direction == 'left') {
		$re = mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_coursereport 
		SET sequence = '".$seq."' 
		WHERE id_course = '".$_SESSION['idCourse']."' AND sequence = '".($seq - 1)."'");
		$re &= mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_coursereport  
		SET sequence = sequence - 1 
		WHERE id_course = '".$_SESSION['idCourse']."' AND id_report = '".$id_report."'");
		
	}
	if($direction == 'right') {
		$re = mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_coursereport 
		SET sequence = '$seq' 
		WHERE id_course = '".$_SESSION['idCourse']."' AND sequence = '".($seq + 1)."'");
		$re &= mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_coursereport 
		SET sequence = sequence + 1 
		WHERE id_course = '".$_SESSION['idCourse']."' AND id_report = '".$id_report."'");
	}
	
	jumpTo('index.php?modname=coursereport&amp;op=coursereport&amp;result='.( $re ? 'ok' : 'err' ));
}

function export()
{
	require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursereport.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php');
	
	$lang =& DoceboLanguage::createInstance('coursereport', 'lms');
	
	$field_man = new FieldList();
	$acl_man =& $GLOBALS['current_user']->getAclManager();
	$test_man 	= new GroupTestManagement();
	$report_man = new CourseReportManager();
	
	// XXX: Find test from organization
	$org_tests 		=& $report_man->getTest();
	$tests_info		=& $test_man->getTestInfo($org_tests);
	
	// XXX: Find students
	$id_students	=& $report_man->getStudentId();
	$students_info 	=& $acl_man->getUsers($id_students);
	
	$excel = '';
	
	$query_report = "
	SELECT id_report, title, max_score, required_score, weight, show_to_user, use_for_final, source_of, id_source 
	FROM ".$GLOBALS['prefix_lms']."_coursereport 
	WHERE id_course = '".$_SESSION['idCourse']."' 
	ORDER BY sequence ";
	$re_report = mysql_query($query_report);
	$total_weight = 0;
	$i = 0;
	while($info_report = mysql_fetch_assoc($re_report))
		$reports[$info_report['id_report']] = $info_report;
	
	$category_detail = array();
	$category_info = array();
	$test_detail = array();
	$id_test_array = array();
	$user_detail = array();
	$username = array();
	
	$total_quest = array();
	$total_quest_answered = array();
	
	while(list($id_user, $user_info) = each($students_info))
	{
		$username[$id_user] = ( $user_info[ACL_INFO_LASTNAME].$user_info[ACL_INFO_FIRSTNAME]
						? $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]
						: $acl_man->relativeId($user_info[ACL_INFO_USERID]) );
		
		$query_category = "SELECT idCategory, name"
						." FROM ".$GLOBALS['prefix_lms']."_quest_category"
						." ORDER BY name";
		
		$result_category = mysql_query($query_category);
		while (list($id_category, $category_name) = mysql_fetch_row($result_category))
		{
			$category_detail[$id_user][$id_category]['name'] = $category_name;
			$category_detail[$id_user][$id_category]['number'] = 0;
			$category_detail[$id_user][$id_category]['max_point'] = 0;
			$category_detail[$id_user][$id_category]['point'] = 0;
			
			$category_info[$id_category]['name'] = $category_name;
			$category_info[$id_category]['number'] = 0;
		}
		
		$category_detail[$id_user][0]['name'] = $lang->def('_NO_CATEGORY');
		$category_detail[$id_user][0]['number'] = 0;
		$category_detail[$id_user][0]['max_point'] = 0;
		$category_detail[$id_user][0]['point'] = 0;
		
		$category_info[0]['name'] = $lang->def('_NO_CATEGORY');
		$category_info[0]['number'] = 0;
		
		$reports = array();
		$id_test = array();
		$id_report = array();
		$tests = array();
		
		$query_report = "
		SELECT id_report, title, max_score, required_score, weight, show_to_user, use_for_final, source_of, id_source 
		FROM ".$GLOBALS['prefix_lms']."_coursereport 
		WHERE id_course = '".$_SESSION['idCourse']."' AND show_to_user = 'true' 
		ORDER BY sequence ";
		$re_report = mysql_query($query_report);
		
		while($info_report = mysql_fetch_assoc($re_report))
		{
			switch($info_report['source_of'])
			{
				case "test" :
					$id_test[] = $info_report['id_source'];
				break;
				case "final_vote":
					$id_final_vote = $info_report['id_source'];
				break;
			}
		}
		
		if (count($id_test))
		{
			$course_title = $GLOBALS['course_descriptor']->getValue('name');
			
			$nquest=0;
			$course_score=0;
			$course_score_max=0;
			$j=0;
			for ($i=0;$i<count($id_test);$i++)
			{
				$query_number_of_quest = "SELECT COUNT(*)"
										." FROM ".$GLOBALS['prefix_lms']."_testquest"
										." WHERE idTest = '".$id_test[$i]."'";
				
				list($number_of_quest) = mysql_fetch_row(mysql_query($query_number_of_quest));
				
				if (isset($total_quest[$id_user]))
					$total_quest[$id_user] += $number_of_quest;
				else
					$total_quest[$id_user] = $number_of_quest;
				
				$test_title = $tests_info[$id_test[$i]]['title'];
				$query_track = "SELECT idTrack FROM ".$GLOBALS['prefix_lms']."_testtrack "
								."WHERE idTest =".$id_test[$i]." AND idUser=".$id_user;
				$re_track = mysql_query($query_track);
				$track = mysql_fetch_assoc($re_track);
				
				list( $title, $mod_doanswer, $point_type, $point_required, $question_random_number, 
					$show_score, $show_score_cat, $show_doanswer, 
					$show_solution) = mysql_fetch_row( mysql_query("
				SELECT  title, mod_doanswer, point_type, point_required, question_random_number, 
						show_score, show_score_cat, show_doanswer, 
						show_solution 
				FROM ".$GLOBALS['prefix_lms']."_test 
				WHERE idTest = '".(int)$id_test[$i]."'"));
				
				list($score, $bonus_score, $date_attempt, $date_attempt_mod) = mysql_fetch_row( mysql_query("
				SELECT score, bonus_score, date_attempt, date_attempt_mod 
				FROM ".$GLOBALS['prefix_lms']."_testtrack 
				WHERE idTrack = '".(int)$track['idTrack']."'"));
				
				$point_do = $bonus_score;
				$max_score = 0;
				$num_manual = 0;
				$manual_score = 0;
				$quest_sequence_number = 1;
				$report_test = '';
				$point_do_cat = array();
				
				if ($track['idTrack'])
				{
					if($question_random_number != 0)
					{
						$re_visu_quest = mysql_query("SELECT idQuest 
						FROM ".$GLOBALS['prefix_lms']."_testtrack_quest 
						WHERE idTrack = '".(int)$track['idTrack']."' ");
						
						while(list($id_q) = mysql_fetch_row($re_visu_quest)) $quest_see[] = $id_q;
						
						$query_question = "
						SELECT q.idQuest, q.title_quest, q.type_quest, t.type_file, t.type_class, q.idCategory 
						FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
						WHERE q.idTest = '".$id_test[$i]."' AND q.type_quest = t.type_quest AND  q.idQuest IN (".implode($quest_see, ',').") 
						ORDER BY q.sequence";
					}
					else
					{
						$query_question = "
						SELECT q.idQuest, q.title_quest, q.type_quest, q.idCategory, t.type_file, t.type_class, q.idCategory 
						FROM ".$GLOBALS['prefix_lms']."_testquest AS q JOIN ".$GLOBALS['prefix_lms']."_quest_type AS t 
						WHERE q.idTest = '".$id_test[$i]."' AND q.type_quest = t.type_quest 
						ORDER BY q.sequence";
					}
					$reQuest = mysql_query($query_question);
					
					while(list($id_quest, $title_quest, $type_quest, $id_category, $type_file, $type_class, $id_cat) = mysql_fetch_row($reQuest))
					{
						require_once($GLOBALS['where_lms'].'/modules/question/'.$type_file);
						
						$quest_point_do = 0;
						
						$quest_obj = eval("return new $type_class( $id_quest );");
						$quest_point_do = $quest_obj->userScore($track['idTrack']);
						$quest_max_score = $quest_obj->getMaxScore();
						if(($type_quest != 'title') && ($type_quest != 'break_page')) {
							$review = $quest_obj->displayUserResult( 	$track['idTrack'], 
																		( $type_quest != 'title' ? $quest_sequence_number++ : $quest_sequence_number ), 
																		$show_solution );
							
							$category_detail[$id_user][$id_category]['number']++;
							$category_info[$id_category]['number']++;
							
							$query_max_point = "SELECT SUM(score_correct)"
												." FROM ".$GLOBALS['prefix_lms']."_testquestanswer"
												." WHERE idQuest = '".$id_quest."'"
												." AND is_correct > '0'";
							
							list($max_point) = mysql_fetch_row(mysql_query($query_max_point));
							
							$category_detail[$id_user][$id_category]['max_point'] += $max_point;
							
							$category_detail[$id_user][$id_category]['point'] += $review['score'];
							
							$query_is_answered = "SELECT COUNT(*)"
												." FROM ".$GLOBALS['prefix_lms']."_testtrack_answer"
												." WHERE idTrack = '".$track['idTrack']."'"
												." AND idQuest = '".$id_quest."'";
							
							list($is_answered) = mysql_fetch_row(mysql_query($query_is_answered));
							if ($is_answered)
							{
								if (isset($total_quest_answered[$id_user]))
									$total_quest_answered[$id_user]++;
								else
									$total_quest_answered[$id_user] = 1;
							}
							$nquest++;
							$report_test.="<tr><td>".$nquest."</td><td>".strip_tags($title_quest)."</td><td align=\"right\">".$review['score']."</td></tr>";
							
						}
						
						if($quest_obj->getScoreSetType() == 'manual') {
							++$num_manual;
							$manual_score = round($manual_score + $quest_max_score, 2);
						}
						
						$point_do = round($point_do + $quest_point_do, 2);
						$max_score = round($max_score + $quest_max_score, 2);
						
						$course_score+=$point_do;
						$course_score_max+=$max_score;
						
						if(isset($point_do_cat[$id_cat])) {
							$point_do_cat[$id_cat] = round($point_do + $point_do_cat[$id_cat], 2);
						}
						else {
							$point_do_cat[$id_cat] = $point_do;
						}
					
						$perc_score = round(round($point_do / $max_score, 2) * 100, 2);
					}
				}
				
				if ($track['idTrack'])
				{
					$GLOBALS['page']->add($report_test, 'content');
					$GLOBALS['page']->add('<tr><td colspan="3" align="right"><strong>'.$test_title.' - '.$lang->def('_TEST_TOTAL').':&nbsp;'.$point_do.' su '.$max_score.' ('.$perc_score.'%)</strong> </td></tr>', 'content');
					
					$test_detail[$id_user][$id_test[$i]] = $perc_score;
					$id_test_array[$id_test[$i]] = $id_test[$i];
				}
				else
				{
					$GLOBALS['page']->add('<tr><td colspan="3" align="right"><strong>'.$lang->def('_TEST_NOT_PLAYED').'</strong></td></tr>', 'content');
					
					$query_id_category = "SELECT idQuest, idCategory"
										." FROM ".$GLOBALS['prefix_lms']."_testquest"
										." WHERE idTest = '".$id_test[$i]."'";
					
					$result_id_category = mysql_query($query_id_category);
					
					$test_detail[$id_user][$id_test[$i]] = '0';
					$id_test_array[$id_test[$i]] = $id_test[$i];
					
					while (list($id_quest, $id_category) = mysql_fetch_row($result_id_category))
					{
						
						$category_detail[$id_user][$id_category]['number']++;
						$category_info[$id_category]['number']++;
						
						
						$query_max_point = "SELECT SUM(score_correct)"
											." FROM ".$GLOBALS['prefix_lms']."_testquestanswer"
											." WHERE idQuest = '".$id_quest."'"
											." AND is_correct > '0'";
						
						list($max_point) = mysql_fetch_row(mysql_query($query_max_point));
						
						$course_score_max += $max_point;
						
						$category_detail[$id_user][$id_category]['max_point'] += $max_point;
						
						if (!isset($total_quest_answered[$id_user]))
							$total_quest_answered[$id_user] = '0';
					}
				}
			}
		}
		
		$perc_course_score[$id_user] = round(round($course_score / $course_score_max, 2) * 100, 2);
	}
	
	ob_end_clean();
	
	$course	= $GLOBALS['course_descriptor']->getAllInfo();
	
	$filename = str_replace(' ', '_', date('Y-m-d').' '.str_replace('[course]', $course['name'], $lang->def('_FILENAME'))).'.xls';
	
	$test_detail_for_name = array();
	
	header ("Content-Type: application/vnd.ms-excel");
	header ("Content-Disposition: inline; filename=$filename");
	
	echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">'
		.'<html lang=it><head>'
		.'<title>'.$filename.'</title></head>'
		.'<body>'
		.'<table border="1">'
		.'<tr>'
		.'<td>'.$lang->def('_FULLNAME').'</td>'
		.'<td>'.$lang->def('_FINAL_VOTE').'</td>'
		.'<td>'.$lang->def('_QUESTION_ANSWERED').'</td>'
		.'<td>'.$lang->def('_TOT_QUESTION').'</td>';
	
	foreach ($id_test_array as $id_test)
	{
		$query_test_title = "SELECT title"
							." FROM ".$GLOBALS['prefix_lms']."_test"
							." WHERE idTest = '".$id_test."'";
		
		list ($test_title) = mysql_fetch_row(mysql_query($query_test_title));
		
		echo '<td>'.str_replace('[test]', $test_title, $lang->def('_VOTE_IN_TEST')).'</td>';
	}
	
	foreach ($category_info as $id_category_temp => $info_category)
	{
		if ($info_category['number'] != 0 && $id_category_temp != 0)
			echo '<td>'.str_replace('[category]', $info_category['name'], $lang->def('_VOTE_IN_CATEGORY')).'</td>';
	}
	
	echo '</tr>';
	
	foreach ($username as $id_user => $name_user)
	{
		echo '<tr>';
		
		echo '<td>'.$name_user.'</td>'
			.'<td align="right">'.(isset($perc_course_score[$id_user]) ? $perc_course_score[$id_user] : '').' %</td>'
			.'<td>'.(isset($total_quest_answered[$id_user]) ? $total_quest_answered[$id_user] : '').'</td>'
			.'<td>'.(isset($total_quest[$id_user]) ? $total_quest[$id_user] : '').'</td>';
		
		foreach ($test_detail[$id_user] as $point)
		{
			$query_test_title = "SELECT title"
								." FROM ".$GLOBALS['prefix_lms']."_test"
								." WHERE idTest = '".$id_test."'";
			
			list ($test_title) = mysql_fetch_row(mysql_query($query_test_title));
			
			echo '<td align="right">'.$point.' %</td>';
		}
		
		foreach ($category_detail[$id_user] as $id_category_temp => $detail_category)
		{
			if ($info_category['number'] != 0 && $id_category_temp != 0)
			{
				$point = round(($detail_category['point'] / $detail_category['max_point']) * 100, 2);
				echo '<td align="right">'.$point.' %</td>';
			}
		}
		
		echo '</tr>';
	}
	
	echo '</table>'
		.'</body></html>';
	exit(0);
}

function testQuestion()
{
	checkPerm('mod');
	
	addYahooJs(array('animation' => 'my_animation.js'));
	addJs($GLOBALS['where_lms_relative'].'/modules/coursereport/', 'ajax.coursereport.js');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.test.php');
	
	$lang =& DoceboLanguage::createInstance('coursereport', 'lms');
	
	$out =& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$out->add('<script type="text/javascript">'
			.' setup_coursereport(\''.$GLOBALS['where_lms_relative'].'/modules/coursereport/ajax.coursereport.php\'); '
			.'</script>', 'page_head');
	
	$id_test = importVar('id_test', true, 0);
	
	$test_man = new GroupTestManagement();
	$acl_man = $GLOBALS['current_user']->getAclManager();
	
	$quests = array();
	$answers = array();
	$tracks = array();
	
	$test_info = $test_man->getTestInfo(array($id_test));

    $page_title = array(	'index.php?modname=coursereport&amp;op=coursereport' => $lang->def('_COURSE_REPORT'),
							'index.php?modname=coursereport&amp;op=testdetail&amp;id_test='.$id_test => $test_info[$id_test]['title']
    );

    $out->add(	getTitleArea($page_title, 'coursereport')
				.'<div class="std_block">'
    );
    
    $query_test =	"SELECT title"
					." FROM ".$GLOBALS['prefix_lms']."_test"
					." WHERE idTest = '".$id_test."'";
    
    list($titolo_test) = mysql_fetch_row(mysql_query($query_test));
    
    $query_quest =	"SELECT idQuest, type_quest, title_quest"
					." FROM ".$GLOBALS['prefix_lms']."_testquest"
					." WHERE idTest = '".$id_test."'"
					." ORDER BY sequence";
	
	$result_quest = mysql_query($query_quest);
	
	while (list($id_quest, $type_quest, $title_quest) = mysql_fetch_row($result_quest))
	{
		$quests[$id_quest]['idQuest'] = $id_quest;
		$quests[$id_quest]['type_quest'] = $type_quest;
		$quests[$id_quest]['title_quest'] = $title_quest;
		
		$query_answer =	"SELECT idAnswer, is_correct, answer"
						." FROM ".$GLOBALS['prefix_lms']."_testquestanswer"
						." WHERE idQuest = '".$id_quest."'"
						." ORDER BY sequence";
		
		$result_answer = mysql_query($query_answer);
		
		
		while (list($id_answer, $is_correct, $answer) = mysql_fetch_row($result_answer))
		{
			$answers[$id_quest][$id_answer]['idAnswer'] = $id_answer;
			$answers[$id_quest][$id_answer]['is_correct'] = $is_correct;
			$answers[$id_quest][$id_answer]['answer'] = $answer;
		}
		if ($type_quest == 'choice' || $type_quest == 'inline_choice')
		{
			$answers[$id_quest][0]['idAnswer'] = 0;
			$answers[$id_quest][0]['is_correct'] = 0;
			$answers[$id_quest][0]['answer'] = $lang->def('_QUEST_IDONTWANTTO');
		}
	}
	
	
	
	$query_track =	"SELECT idTrack"
					." FROM ".$GLOBALS['prefix_lms']."_testtrack"
					." WHERE idTest = '".$id_test."'";
	
	$result_track = mysql_query($query_track);
	
	while(list($id_track) = mysql_fetch_row($result_track))
	{
		$query_track_answer =	"SELECT idQuest, idAnswer, more_info"
								." FROM ".$GLOBALS['prefix_lms']."_testtrack_answer"
								." WHERE idTrack = '".$id_track."'";
		
		$result_track_answer = mysql_query($query_track_answer);
		
		while(list($id_quest, $id_answer, $more_info) = mysql_fetch_row($result_track_answer))
			$tracks[$id_track][$id_quest][$id_answer]['more_info'] = $more_info;
	}
	
	$query_total_play =	"SELECT COUNT(*)"
						." FROM ".$GLOBALS['prefix_lms']."_testtrack"
						." WHERE idTest = '".$id_test."'";
	
	list($total_play) = mysql_fetch_row(mysql_query($query_total_play));
	
	foreach($quests as $quest)
	{
		switch ($quest['type_quest'])
		{
			case "inline_choice":
			case "hot_text":
			case "choice_multiple":
			case "choice":
				$cont_h = array
					(
						$lang->def('_ANSWER'),
						$lang->def('_PERCENTAGE')
					);
				$type_h = array( 
					'', 'image nowrap');
				
				$tb = new TypeOne(0, str_replace('[title]', $quest['title_quest'], $lang->def('_TABLE_QUEST')));
				$tb->setColsStyle($type_h);
				$tb->addHead($cont_h);
				
				foreach($answers[$quest['idQuest']] as $answer)
				{
					$cont = array();
					
					if($answer['is_correct'])
						$txt = '<img src="'.getPathImage('lms').'test/correct.gif" alt="'.$lang->def('_ANSWER_CORRECT').'" title="'.$lang->def('_ANSWER_CORRECT').'" align="left" />';
					else
						$txt = '';
					
					$cont[] = '<p>'.$txt.' '.$answer['answer'].'</p>';
					
					$answer_given = 0;
					
					foreach($tracks as $track)
					{
						if(isset($track[$quest['idQuest']][$answer['idAnswer']]))
							$answer_given++;
						elseif(!isset($track[$quest['idQuest']]) && $answer['idAnswer'] == 0)
							$answer_given++;
					}
					
					if($total_play > 0)
						$percentage = ($answer_given / $total_play) * 100;
					else
						$percentage = 0;
					
					
					$percentage = number_format($percentage, 2);
					
					$cont[] = drawProgressBar($percentage, true, false, false, false, false);
					
					$tb->addBody($cont);
				}
				
				$out->add($tb->getTable().'<br/>');
			break;
			
			case "upload":
			case "extended_text":
				$out->add('<div>');
				$out->add('<p><a href="#" onclick="getQuestDetail('.$quest['idQuest'].', '.$id_test.', \''.$quest['type_quest'].'\'); return false;" id="more_quest_'.$quest['idQuest'].'"><img src="'.getPathImage('fw').'standard/more.gif" alt="'.$lang->def('_MORE_INFO').'" />'.str_replace('[title]', $quest['title_quest'], $lang->def('_TABLE_QUEST_LIST')).'</a></p>');
				$out->add('<p><a href="#" onclick="closeQuestDetail('.$quest['idQuest'].'); return false;" id="less_quest_'.$quest['idQuest'].'" style="display:none"><img src="'.getPathImage('fw').'standard/less.gif" alt="'.$lang->def('_LESS_INFO').'" />'.str_replace('[title]', $quest['title_quest'], $lang->def('_TABLE_QUEST_LIST')).'</a></p>');
				$out->add('</div>');
				$out->add('<div id="quest_'.$quest['idQuest'].'">');
				$out->add('</div>');
			break;
			
			case "text_entry":
				$cont_h = array
					(
						$lang->def('_PERCENTAGE_CORRECT')
					);
				$type_h = array('align_center');
				
				$tb = new TypeOne(0, str_replace('[title]', $quest['title_quest'], $lang->def('_TABLE_QUEST_CORRECT_TXT')));
				$tb->setColsStyle($type_h);
				$tb->addHead($cont_h);
				
				foreach($answers[$quest['idQuest']] as $answer)
				{
					$cont = array();
					
					$answer_correct = 0;
					
					foreach($tracks as $track)
					{
						if($track[$quest['idQuest']][$answer['idAnswer']]['more_info'] === $answer['answer'])
							$answer_correct++;
					}
					
					$percentage = ($answer_correct / $total_play) * 100;
					
					$percentage = number_format($percentage, 2);
					
					$cont[] = drawProgressBar($percentage, true, false, false, false, false);
					
					$tb->addBody($cont);
				}
				
				$out->add($tb->getTable().'<br/>');
			break;
			
			case "associate":
				$cont_h = array
					(
						$lang->def('_ANSWER'),
						$lang->def('_PERCENTAGE_CORRECT')
					);
				$type_h = array('', 'align_center');
				
				$tb = new TypeOne(0, str_replace('[title]', $quest['title_quest'], $lang->def('_TABLE_QUEST_CORRECT_ASS')));
				$tb->setColsStyle($type_h);
				$tb->addHead($cont_h);
				
				foreach($answers[$quest['idQuest']] as $answer)
				{
					$cont = array();
					
					$cont[] = $answer['answer'];
					
					$answer_correct = 0;
					
					foreach($tracks as $track)
					{
						if($track[$quest['idQuest']][$answer['idAnswer']]['more_info'] === $answer['is_correct'])
							$answer_correct++;
					}
					
					$percentage = ($answer_correct / $total_play) * 100;
					
					$percentage = number_format($percentage, 2);
					
					$cont[] = drawProgressBar($percentage, true, false, false, false, false);
					
					$tb->addBody($cont);
				}
				
				$out->add($tb->getTable().'<br/>');
			break;
		}
		
		reset($answers);
		reset($tracks);
	}
	
	$out->add('</div>');
}

function coursereportDispatch($op) {
	
	if(isset($_POST['undo'])) $op = 'coursereport';
	if(isset($_POST['undo_testreview'])) $op = 'testvote';
	if(isset($_POST['undo_reset'])) $op = 'testvote';
	if(isset($_POST['view_answer'])) $op = 'testreview';
	
	switch($op) {
		
		case "export":
			export();
		break;
		case "coursereport" : {
			coursereport();
		};break;
		case "testvote" : {
			testvote();
		};break;
		case "testreview" : {
			testreview();
		};break;
		case "testQuestion" :
			testQuestion();
		break;
		case "finalvote" : {
			finalvote();
		};break;
		
		case "roundtest" : {
			roundtest();
		};break;
		
		case "roundreport" : {
			roundreport();
		};break;
		case "redofinal" : {
			redofinal();
		};break;
		
		case "addactivity" :{
			modactivity();
		};break;
		case "modactivityscore" : {
			modactivityscore();
		};break;
		
		case "delactivity" : {
			delactivity();
		};break;
		
		case "moveright" : {
			movereport('right');
		};break;
		case "moveleft" : {
			movereport('left');
		};break;
		case "testreport" : {
            testreport($_GET['idTrack'],$_GET['idTest'],$_GET['testName'],$_GET['studentName']);
        };break;
	}
	
}

?>