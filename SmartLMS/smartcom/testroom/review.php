<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

  // $Id: review.php,v 1.59.2.10 2009/01/16 04:47:35 tjhunt Exp $
/**
 * This page prints a review of a particular quiz attempt
 *
 * @author danhut
 * @package quiz
 */
	
//	require_once("locallib.php");
//	require_once($CFG->dirroot.'/mod/quiz/quiz_review_pagelib.php');
//	require_once($CFG->libdir.'/blocklib.php');

require_once("../../config.php");
require_once($CFG->libdir.'/datalib.php');
require_once($CFG->dirroot.'/smartcom/testroom/lib.php');

	//$attemptList = optional_param('attempts', '', PARAM_TEXT);    // list of attemp ids
	global $SESSION, $USER;
	$attemptIdArr = $SESSION->attemptIdArr; //explode(',', $attemptList);
	if(empty($attemptIdArr)) {
		error("You have not finished any parts in the entrance test");
	}
	$attemptArr = array();
	$quizArr = array();
	$cmArr = array();
	
	foreach($attemptIdArr as $attemptId) {
		if (! $attempt = get_record("quiz_attempts", "id", $attemptId, "userid", $USER->id)) {
			error("No such attempt ID completed by such user ID exists");
		}
		if (! $quiz = get_record("quiz", "id", $attempt->quiz)) {
			error("The quiz with id $attempt->quiz belonging to attempt $attempt is missing");
		}
		if (! $course = get_record("course", "id", $quiz->course)) {
			error("The course with id $quiz->course that the quiz with id $quiz->id belongs to is missing");
		}
		if (! $cm = get_coursemodule_from_instance("quiz", $quiz->id, $course->id)) {
			error("The course module for the quiz with id $quiz->id is missing");
		}
	
		$attemptArr[] = $attempt;
		$quizArr[] = $quiz;
		$cmArr[] = $cm;		
	}
	
	require_login($course->id, false);
	add_to_log($course->id, 'testroom', 'review', '/smartcom/testroom/review.php?attempt=' . $attemptIdArr);
	$context = get_context_instance(CONTEXT_COURSE, $course->id);	
	$rows = array();
	
	$totalGrade = 0; /*tổng điểm của bài test user đạt được*/
	$maxGrade = 0; /*điểm tối đa của bài test*/
	/*danhut: duyệt toàn bộ các quiz trong bài test và in kết quả*/
	for($i = 0; $i<sizeof($attemptArr); $i++) {
		
		$attempt = $attemptArr[$i];
		$quiz = $quizArr[$i];
		$cm = $cmArr[$i];												
		
		
		/*danhut: get the activity that contains this quiz, e.g.: Grammar, listening ..*/
		$parentActivityLabel = get_parent_resource_name($cm);
									
		/* danhut: Show total scores of this quiz */		
		if ($quiz->grade and $quiz->sumgrades) {				
			/// Now the scaled grade.
			$a = new stdClass;
			$a->grade = '<b>' . $attempt->sumgrades . '</b>';
			$a->maxgrade = $quiz->sumgrades;
			$a->percent = '<b>' . round(($attempt->sumgrades/$quiz->sumgrades)*100, 0) . '</b>';
			$rows[] = '<tr><td  class="header" scope="row" class="cell"><b>' . $parentActivityLabel . '</b></td><td class="cell">' .
			get_string('outofpercent', 'quiz', $a) . '</td></tr>';
		}
		
		$totalGrade += $attempt->sumgrades;
		$maxGrade += $quiz->sumgrades;
		
		/*danhut: tính điểm thành phần theo từng question category cho quiz hiện tại*/
		$sql = "SELECT cat.name, cat.info, sum(qs.grade) as sumgrade, sum(q.defaultgrade) as sumdefaultgrade
				FROM mdl_question_states qs, mdl_question_categories cat, mdl_question q
				WHERE qs.question = q.id AND q.category=cat.id AND qs.event=6 AND qs.attempt = $attempt->id
				GROUP BY cat.id";
			
		$results = get_records_sql($sql);

		if(!empty($results)) {
			foreach($results as $result) {
				$a = new stdClass;
				$a->grade = '<b>' . $result->sumgrade . '</b>';
				$a->maxgrade = $result->sumdefaultgrade;
				$a->percent = '<b>' . round(($result->sumgrade/$result->sumdefaultgrade)*100, 0) . '</b>';
				
				if(empty($result->info) && empty($result->name)) {
					continue;
				}
				if(!empty($result->info)) {
					$catinfo = $result->info;
				}
				else if(!empty($result->name)) {
					$catinfo = $result->name;
				}
				$rows[] = '<tr><td class="header" scope="row" class="cell">' . $catinfo . '</td><td class="cell">' 
						. get_string('outofpercent', 'quiz', $a) . '</td></tr>';
			}
		}	
		
	}
	
	/*xác định course dựa trên tổng điểm đạt được để recommend*/
	$courseArr = selectCourseByGrade($course->id, $totalGrade/$maxGrade);
	/*nếu có test tiếp theo với kết quả user đã đạt được, redirect user tới test đó*/
	if(!empty($courseArr) && !is_array($courseArr)) {		
		/*xóa session*/
		unset($SESSION->attemptIdArr);
		redirect($CFG->wwwroot . '/course/view.php?id=' . $courseArr);
	}
		
	/// Send emails to those who have the capability set
    
    //quiz_send_notification_emails($course, $quiz, $attempt, $context, $cm);
    
    //email_to_user($USER, get_admin(), $subject, $body);
    
    
	$strreviewtitle = get_string('entrancetestresult', 'smartcom', $attempt->attempt);
	$navigation = build_navigation($strreviewtitle, $cm);
	print_header_simple(format_string($quiz->name), "", $navigation, "", '', true);

	
	echo '<table class="smartlms-table-wrapper" align="center" style="width: 1024px !important;" id="layout-table"><tr>';
	echo '
        <td id="middle-column" >';
	print_container_start();	
	print_heading($strreviewtitle);
	/// Now output the summary table, if there are any rows to be shown.
	if (!empty($rows)) {
		echo '<table class="generaltable generalbox quizreviewsummary smartlms-table-data"><tbody>', "\n";
		/*in overall test result*/
		$a = new stdClass;
		$a->grade = '<b>' . $totalGrade . '</b>';
		$a->maxgrade = $maxGrade;
		$a->percent = '<b>' . round(($totalGrade/$maxGrade)*100, 0) . '</b>';
		echo '<tr><td class="header">' . get_string('overraltestgrade', 'smartcom') . '</td><td >'
			. get_string('outofpercent', 'quiz', $a) . '</td></tr>';
		
		echo implode("\n", $rows);
		echo "\n</tbody></table>\n";
	}

	echo '</tr>';
	

	if(!empty($courseArr['maincourse'])) {
		echo '<tr><td class="header">' . get_string('courselevel', 'smartcom') . $courseArr['maincourse']->categoryname . '</td></tr>';
		echo '<tr><td class="header">' . get_string('maincourse', 'smartcom') . 
			' : <a href="'. $CFG->wwwroot . '/course/enrol.php?id=' . $courseArr['maincourse']->id .'">' . $courseArr['maincourse']->fullname . '</a></td></tr>';
	} 
	if(!empty($courseArr['minorcourse1'])) {
		echo '<tr><td class="header">' . get_string('minorcourse', 'smartcom') . 
			' 1: <a href="'. $CFG->wwwroot . '/course/enrol.php?id=' . $courseArr['minorcourse1']->id .'">' . $courseArr['minorcourse1']->fullname . '</a></td></tr>';
	}
	
	if(!empty($courseArr['minorcourse2'])) {
		echo '<tr class="header"><td>' . get_string('minorcourse', 'smartcom') . 
			' 2: <a href="'. $CFG->wwwroot . '/course/enrol.php?id=' . $courseArr['minorcourse2']->id .'">' . $courseArr['minorcourse2']->fullname . '</a></td></tr>';
	}
	
	echo '</table>';
	
	
	print_footer($course);
	

?>