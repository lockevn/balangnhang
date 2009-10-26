<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once($CFG->libdir.'/datalib.php');
require_once $CFG->dirroot .'/smartcom/util/courseutil.php';

function printSupportToolMenuBar($courseid) {
	$str = "<div>" ;
	if(!empty($courseid) && $courseid != 1) {
		global $CFG;
		$cmList = getCourseSupportTools($courseid);

		/*resume course link*/
		$str .= "<a href='$CFG->wwwroot/smartcom/resume/view.php?id=$courseid'>" . get_string('resume_course','resume') . "</a>";
		$str .= " ";


		foreach($cmList as $cm) {
			$str .= "<a href='$CFG->wwwroot/mod/$cm->modulename/view.php?id=$cm->id'>$cm->name</a>";
			$str .= " ";
		}
	}
	$str .= "</div>";
	echo "$str";
}

?>