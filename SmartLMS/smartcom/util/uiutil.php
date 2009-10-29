<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once($CFG->libdir.'/datalib.php');
require_once $CFG->dirroot .'/smartcom/util/courseutil.php';

/**
 * Display thanh menu ngang màu đỏ chứa các công cụ hỗ trợ học tập
 *
 * @param unknown_type $courseid
 */
function printSupportToolMenuBar($courseid) {
	$str = "<div>" ;
	if(!empty($courseid) && $courseid != 1) {
		global $CFG;
		$cmList = getCourseSupportTools($courseid);

		/*resume course link*/
		$str .= "<a href='$CFG->wwwroot/smartcom/resume/view.php?id=$courseid'>" . get_string('resume_course','resume') . "</a>";
		$str .= " ";

		if(!empty($cmList)) {
			foreach($cmList as $cm) {
				$str .= "<a href='$CFG->wwwroot/mod/$cm->modulename/view.php?id=$cm->id'>$cm->name</a>";
				$str .= " ";
			}
		}
	}
	$str .= "</div>";
	echo "$str";
}

/**
 * Dísplay thanh acitivity bar trong các trang lecture, exercise
 *
 * @param int $courseid
 * @param int $selectedLOId
 * @param int $type (QUIZ / RESOURCE)
 * @param int $userid
 */
function printSectionActivities($courseid, $selectedLOId, $type, $userid) {
	$activityArr = getLessonActivitiesFromLOId($courseid, $selectedLOId, $type) ;
	if($activityArr === false) {
		return;
	}
	$str = "";
	foreach($activityArr as $activity) {
		
		/*tính điểm all quiz*/
		$grade = getAvgGradeOfAllQuizInActivityOfUserFromLOId($courseid, $selectedLOId, $activity->id, $userid );
		$str .= "<a href='$activity->link'>";
		if($activity->selected == 1) {
			$str .= "<b>$activity->name ($grade->status)</b>";	
		} else {
			$str .= $activity->name ."($grade->status)";
		}
		$str .= "</a> ";		
	}
	echo $str;
}

?>