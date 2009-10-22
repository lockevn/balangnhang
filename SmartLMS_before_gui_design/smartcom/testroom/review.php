<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

  // $Id: review.php,v 1.59.2.10 2009/01/16 04:47:35 tjhunt Exp $
/**
 * This page prints a review of a particular quiz attempt
 *
 * @author Martin Dougiamas and many others. This has recently been completely
 *         rewritten by Alex Smith, Julian Sedding and Gustav Delius as part of
 *         the Serving Mathematics project
 *         {@link http://maths.york.ac.uk/serving_maths}
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
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
	
	
	$strreviewtitle = get_string('entrancetestresult', 'smartcom', $attempt->attempt);
	$navigation = build_navigation($strreviewtitle, $cm);
	print_header_simple(format_string($quiz->name), "", $navigation, "", '', true);

	

	echo '<table id="layout-table"><tr>';

	echo '<td id="middle-column">';
	print_container_start();
	/*end of added*/
	

/// Print heading.

		
	print_heading($strreviewtitle);

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
			$rows[] = '<tr><td scope="row" class="cell"><b>' . $parentActivityLabel . '</b></td><td class="cell">' .
			get_string('outofpercent', 'quiz', $a) . '</td></tr>';
		}
		
		$totalGrade += $attempt->sumgrades;
		$maxGrade += $quiz->sumgrades;
		
		/*danhut: tính điểm thành phần theo từng question category cho quiz hiện tại*/
		$sql = "SELECT cat.info, sum(qs.grade) as sumgrade, sum(q.defaultgrade) as sumdefaultgrade
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
				$rows[] = '<tr><td scope="row" class="cell">' . $result->info . '</td><td class="cell">' 
						. get_string('outofpercent', 'quiz', $a) . '</td></tr>';
			}
		}	
		
	}
	
	
	
	
	/// Now output the summary table, if there are any rows to be shown.
	if (!empty($rows)) {
		echo '<table class="generaltable generalbox quizreviewsummary"><tbody>', "\n";
		/*in overall test result*/
		$a = new stdClass;
		$a->grade = '<b>' . $totalGrade . '</b>';
		$a->maxgrade = $maxGrade;
		$a->percent = '<b>' . round(($totalGrade/$maxGrade)*100, 0) . '</b>';
		echo '<tr><td>' . get_string('overraltestgrade', 'smartcom') . '</td><td >'
			. get_string('outofpercent', 'quiz', $a) . '</td></tr>';
		
		echo implode("\n", $rows);
		echo "\n</tbody></table>\n";
	}

	echo '</tr>';
	
	/*xác định course dựa trên tổng điểm đạt được để recommend*/
	$courseArr = selectCourseByGrade($course->id, $totalGrade/$maxGrade);
	if(isset($courseArr['maincourse'])) {
		echo '<tr><td>' . get_string('courselevel', 'smartcom') . $courseArr['maincourse']->categoryname . '</td></tr>';
		echo '<tr><td>' . get_string('maincourse', 'smartcom') . 
			' : <a href="'. $CFG->wwwroot . '/course/enrol.php?id=' . $courseArr['maincourse']->id .'">' . $courseArr['maincourse']->fullname . '</a></td></tr>';
	} 
	if(isset($courseArr['minorcourse1'])) {
		echo '<tr><td>' . get_string('minorcourse', 'smartcom') . 
			' 1: <a href="'. $CFG->wwwroot . '/course/enrol.php?id=' . $courseArr['minorcourse1']->id .'">' . $courseArr['minorcourse1']->fullname . '</a></td></tr>';
	}
	
	if(isset($courseArr['minorcourse2'])) {
		echo '<tr><td>' . get_string('minorcourse', 'smartcom') . 
			' 2: <a href="'. $CFG->wwwroot . '/course/enrol.php?id=' . $courseArr['minorcourse2']->id .'">' . $courseArr['minorcourse2']->fullname . '</a></td></tr>';
	}
	
	echo '</table>';
	
	if (empty($popup)) {
		print_footer($course);
	}

?>