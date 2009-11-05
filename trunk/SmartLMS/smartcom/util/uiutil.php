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
    global $CFG;
	if(empty($activityArr)) {
		return;
	}
	$str = '<table cellpadding="0" cellspacing="0"><tr>';
	foreach($activityArr as $activity) {
		
		/*tính điểm all quiz*/
		$grade = getAvgGradeOfAllQuizInActivityOfUserFromLOId($courseid, $selectedLOId, $activity->id, $userid );
        $str .= '<td>
            <table cellpadding="0" cellspacing="5px">
                <tr>
                    <td class="courseBB">
                        &nbsp;&nbsp;<a href='.$activity->link.'>';
                        
                            if($activity->selected == 1) {
                                $str .= "<b>$activity->name </b>";     //($grade->status)
                            } else {
                               $str .= $activity->name ;
                            }                  
        $str .= '       </a>
                    </td>                                            
                    <td>
                        <img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/coursemenu_x.JPG" /> 
                    </td>                                            
                    <td>
                        <img src="'. $CFG->wwwroot.'/theme/menu_horizontal/template/images/MN1_SP.jpg" /> 
                    </td>
                </tr>
             </table>
             </td>                           
            ';
	}
	echo $str. '</tr></table>';
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
				$link .= "<a style=\"margin: 0 5px; color: #FFF; padding: 0 5px;background: transparent url(http://smartlms/theme/menu_horizontal/template/images/CircleBR.gif) no-repeat scroll center center; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;\" href='$CFG->wwwroot/mod/resource/view.php?id=$lo->id'>$lectureIndex</a>";
			}
			else {
				$link .= "<a style=\"margin: 0 5px; color: #FFF; padding: 0 5px; background: gray; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;\" href='$CFG->wwwroot/mod/resource/view.php?id=$lo->id'><b>$lectureIndex</b></a>";
				/*thử kiểm tra ngay sau current lecture có phải là 1 practice k*/
				if(($i + 1) < sizeof($loList) && $loList[$i+1]->type == "practice") {
					$practice = $loList[$i+1];
				}
			}
			
			$lectureIndex++;
		}
	}
    
	echo '- '. get_string("lecture_list", "smartcom", $link);
	if(empty($practice)) {
		return;
	}	
	$link = "<a href='$CFG->wwwroot/mod/quiz/view.php?id=$practice->id'>". get_string("practice", "smartcom") . "</a>";
	echo $link;
}

function printLectureListOfCurrentQuiz($lectureList) {
	global $CFG;
	if(empty($lectureList)) {
		return;
	}
	$str = "";
	foreach($lectureList as $key => $lectureArr) {
		if(empty($lectureArr)) {
			continue;
		}
		$str .= get_string("lecture_review", "smartcom", $key);
		$i = 1;
		foreach($lectureArr as $lecture) {
			$str .= "<a href='$CFG->wwwroot/mod/resource/view.php?id=$lecture->id'>$i</a> | ";
			$i++;
		}
		$str .= "<br>";
	}
	echo $str;
}

?>