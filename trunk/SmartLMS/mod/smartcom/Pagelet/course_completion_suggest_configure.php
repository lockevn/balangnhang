<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");


$currentConfigOfCourseCompletion = get_record('smartcom_course_completion_suggestion', 'courseid', $courseid);
$arrCurrentNextCourseIdSet = explode(',',$currentConfigOfCourseCompletion->nextcourseidset);


$arrCourseInSystem = get_records_sql(
"select id, fullname as name from mdl_course where id != $courseid and id > 1"
);
$arrCourseInSystem = array_values($arrCourseInSystem);
foreach ($arrCourseInSystem as &$value) {
	if(in_array($value->id, $arrCurrentNextCourseIdSet))
	{
		$value->selected = true;		
	}
	else
	{
		$value->selected = false;        
	}
}
unset($value);
$tpl->assign('arrCourseInSystem', $arrCourseInSystem);


$arrQuiz = get_records( 'quiz', 'course', $courseid, '', 'name,id' );
$arrQuiz = array_values($arrQuiz);
foreach ($arrQuiz as &$value) {
	if($value->id == $currentConfigOfCourseCompletion->finalquizid)
	{
		$value->selected = true;        
	}
	else
	{
		$value->selected = false;        
	}
}
unset($value);
$tpl->assign('arrQuiz', $arrQuiz);


$tpl->assign('courseid', $courseid);

$FILENAME = 'course_completion_suggest_configure';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>