<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_login();

$courseid = required_param('courseid', PARAM_INT);   // course
if (! $course = get_record('course', 'id', $courseid)) {
	error('GURUCORE: Course ID is incorrect');
}
$context = get_context_instance(CONTEXT_COURSE, $courseid);
require_capability('mod/smartcom:coursecompletionsuggestconfigure', $context);



$currentConfigOfCourseCompletion = get_record('smartcom_course_completion_suggestion', 'courseid', $courseid);

if($currentConfigOfCourseCompletion)
{
	$arrCurrentNextCourseIdSet = explode(',',$currentConfigOfCourseCompletion->nextcourseidset);
}

$arrCourseInSystem = get_records_sql(
"select id, fullname as name from mdl_course where id != $courseid and id > 1"
);

if(is_array($arrCourseInSystem))
{
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
}
$tpl->assign('arrCourseInSystem', $arrCourseInSystem);

// lookup label lesson of quiz
$arrQuizLesson = SmartComDataUtil::GetQuizAndLessonLabel($courseid);
$arrQuizIdActivity = SmartComDataUtil::GetQuizIdAndActivityLabel($courseid);

$arrQuiz = get_records( 'quiz', 'course', $courseid, 'name', 'id,name' );
$arrQuiz = array_values($arrQuiz);
foreach ($arrQuiz as &$value) {
	if(!empty($currentConfigOfCourseCompletion) && $value->id == $currentConfigOfCourseCompletion->finalquizid)
	{
		$value->selected = true;        
	}
	else
	{
		$value->selected = false;        
	}
	$lessonlabel = $arrQuizLesson[$value->id]->label;
	$activitylabel = $arrQuizIdActivity[$value->id];
	$value->name = "$lessonlabel--------$activitylabel----{$value->name}";
}
unset($value);
$tpl->assign('arrQuiz', $arrQuiz);
$tpl->assign('courseid', $courseid);


$tpl->assign('overallquizzespercent', $currentConfigOfCourseCompletion->overallquizzespercent);
$tpl->assign('finalquizpercent', $currentConfigOfCourseCompletion->finalquizpercent);
$tpl->assign('isenable', $currentConfigOfCourseCompletion->isenable);


$FILENAME = 'course_completion_suggest_configure';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>