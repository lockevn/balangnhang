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
function printSectionActivities($activityArr, $courseid, $selectedLOId, $userid) {	
	if(empty($activityArr)) {
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

function printLectureListOfCurrentActivity($loList) {
	global $CFG;
	if(empty($loList)) {
		return;
	}
	
	$link = "";
	$lectureIndex = 1;	
	for($i = 0; $i < sizeof($loList); $i++) {
		$lo = $loList[$i];
		if($lo->type == "lecture") {
			if($lo->selected == 0) {
				$link .= "<a href='$CFG->wwwroot/mod/resource/view.php?id=$lo->id'>$lectureIndex</a>  ";
			}
			else {
				$link .= "<a href='$CFG->wwwroot/mod/resource/view.php?id=$lo->id'><b>$lectureIndex</b></a>  ";
				/*thử kiểm tra ngay sau current lecture có phải là 1 practice k*/
				if(($i + 1) < sizeof($loList) && $loList[$i+1]->type == "practice") {
					$practice = $loList[$i+1];
				}
			}
			
			$lectureIndex++;
		}
	}
	echo get_string("lecture_list", "smartcom", $link);
	if(empty($practice)) {
		return;
	}	
	$link = "<a href='$CFG->wwwroot/mod/quiz/view.php?id=$practice->id'>". get_string("practice", "smartcom") . "</a>";
	echo $link;
}

?>