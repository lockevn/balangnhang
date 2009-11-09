<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once($CFG->libdir.'/datalib.php');
require_once $CFG->dirroot .'/smartcom/util/courseutil.php';

/**
 * Display thanh menu ngang màu đỏ chứa các công cụ hỗ trợ học tập
 *
 * @param unknown_type $courseid
 */
function printSupportToolMenuBar($courseid) {
	
	global $CFG;
	$str = '<div class="user-sub-menu">' ;
	if(!empty($courseid) && $courseid != 1) {
		
		$cmList = getCourseSupportTools($courseid);

		/*resume course link*/		
		$str .= "<div class='menu2'><a href='$CFG->wwwroot/smartcom/resume/view.php?id=$courseid'><img src='$CFG->themewww/".current_theme() ."/template/images/MN2_1.jpg' /></a></div>";
		$str .= "<div class='menu2split'></div>";			
		//$str .= "<a href='$CFG->wwwroot/smartcom/resume/view.php?id=$courseid'>" . get_string('resume_course','resume') . "</a>";
		/*course forum link*/
		$link = "";
		if(!empty($cmList['forum'])) {
			$cm = $cmList['forum'];
			$link = "$CFG->wwwroot/mod/$cm->modulename/view.php?id=$cm->id";
		}
		$str .= "<div class='menu2'><a href='$link'><img src='$CFG->themewww/".current_theme() ."/template/images/MN2_2.jpg' /></a></div>";
		$str .= "<div class='menu2split'></div>";

		/*course gallery link*/
		if(!empty($cmList['lightboxgallery'])) {
			$cm = $cmList['lightboxgallery'];
			$link = "$CFG->wwwroot/mod/$cm->modulename/view.php?id=$cm->id";
		}
		else {
			$link = "#";
		}		
		$str .= "<div class='menu2'><a href='$link'><img src='$CFG->themewww/".current_theme() ."/template/images/MN2_3.jpg' /></a></div>";
		$str .= "<div class='menu2split'></div>";
		
		/*course chatroom link*/
		if(!empty($cmList['chat'])) {
			$cm = $cmList['chat'];
			$link = "$CFG->wwwroot/mod/$cm->modulename/view.php?id=$cm->id";
		}
		else {
			$link = "#";
		}
		$str .= "<div class='menu2'><a href='$link'><img src='$CFG->themewww/".current_theme() ."/template/images/MN2_4.jpg' /></a></div>";
		$str .= "<div class='menu2split'></div>";
		
		/*course glossary link*/
		if(!empty($cmList['glossary'])) {
			$cm = $cmList['glossary'];
			$link = "$CFG->wwwroot/mod/$cm->modulename/view.php?id=$cm->id";
		}
		else {
			$link = "#";
		}
		$str .= "<div class='menu2'><a href='$link'><img src='$CFG->themewww/".current_theme() ."/template/images/MN2_5.jpg' /></a></div>";
		$str .= "<div class='menu2split'></div>";
		
		/*voice recording link*/
		if(!empty($cmList['nanogong'])) {
			$cm = $cmList['nanogong'];
			$link = "$CFG->wwwroot/mod/$cm->modulename/view.php?id=$cm->id";
		}
		else {
			$link = "#";
		}
		$str .= "<div class='menu2'><a href='$link'><img src='$CFG->themewww/".current_theme() ."/template/images/MN2_6.jpg' /></a></div>";
		$str .= "<div class='menu2split'></div>";
		
		/*nạp thẻ*/
		$str .= "<div class='menu2'><a href='$CFG->wwwroot/mod/smartcom/index.php?courseid=$courseid&submodule=prepaidcard_enduser_deposit'><img src='$CFG->themewww/".current_theme() ."/template/images/MN2_7.jpg' /></a></div>";
		$str .= "<div class='menu2split'></div>";	
	}
	$str .= "</div>";
	echo $str;

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
				$link .= "<a style=\"margin: 0 5px; color: #FFF; padding: 0 5px;background: transparent url($CFG->themewww/".current_theme() ."/template/images/CircleBR.gif) no-repeat scroll center center; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;\" href='$CFG->wwwroot/mod/resource/view.php?id=$lo->id'>$lectureIndex</a>";
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
			$str .= "<a style=\"color: #FFF; background: url(".$CFG->wwwroot."/theme/menu_horizontal/template/images/CircleBR.gif) no-repeat; margin-right: 5px; padding: 0 5px;\" href='$CFG->wwwroot/mod/resource/view.php?id=$lecture->id'>$i</a> ";
			$i++;
		}
		$str .= "<br>";
	}
	echo $str;
}

?>