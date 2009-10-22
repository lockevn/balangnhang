<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."lib/Text.php");

$courseid = 15;
$mapResourceName_QuizIDlist = array();
$arrCourseModule = get_coursemodules_in_course('quiz', $courseid);
foreach (((array)$arrCourseModule) as $key => $value) {	
	$resourceName = trim(get_parent_resource_name($value));	   
	$mapResourceName_QuizIDlist[$value->instance] = $resourceName;
}
$v = array_keys($mapResourceName_QuizIDlist);

print_r($v);   



require_login();
$userid = $USER->id;

$courseid = required_param('courseid', PARAM_INT);   // course
if (! $course = get_record('course', 'id', $courseid)) {
	error('GURUCORE: Course ID is incorrect');
}


$context = get_context_instance(CONTEXT_SYSTEM);
require_capability('mod/smartcom:prepaidcardusagereport', $context);


require_once($CFG->dirroot.'/mod/smartcom/locallib.php');
	SmartComDataUtil::require_smartcom_ticket($id);

	 
?>