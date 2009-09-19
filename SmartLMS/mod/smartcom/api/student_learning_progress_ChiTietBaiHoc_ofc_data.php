<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH.'lib/ofc-library/open-flash-chart.php');
require_once(ABSPATH.'lib/datalib.php');
require_once(ABSPATH.'mod/smartcom/locallib.php');



$courseid = required_param('courseid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$sectionid = optional_param('sectionid',0 , PARAM_INT);
$lessonname = optional_param('lessonname', '' , PARAM_TEXT);

$g = new OFCgraph();
$g->title("Chi tiết bài học [$lessonname]", '{font-size: 18px;}');


/////////// QUIZ IN LESSON /////////////
$mapQuiz_Section = get_records_sql("
select instance as quiz, section from `mdl_course_modules`
where section = $sectionid
and course=$courseid
and module=12
");


////////////////// QUIZ and PARENT NAME (ACTIVITY) ////////////////
$mapResourceName_QuizIDlist = array();
$arrCourseModule = get_coursemodules_in_course('quiz', $courseid);
foreach (((array)$arrCourseModule) as $key => $value) {
	if(array_key_exists($value->instance, $mapQuiz_Section))
	{
		$resourceName = trim(get_parent_resource_name($value));
		if(empty($resourceName) == false)
		{			
			$mapResourceName_QuizIDlist[$resourceName] .= $value->instance . ',';	
		}
	}
}
$dataXLabel = $dataXLabel = array_unique(array_keys($mapResourceName_QuizIDlist));




if($sectionid > 0)
{
	$data1 = array();
	foreach (((array)$mapResourceName_QuizIDlist) as $activityname => $quizidlist) {	
		$data1[] = SmartComDataUtil::GetQuizArrayPercentOfUser($userid, $courseid, $quizidlist);
	}
	$g->set_data($data1);
}

$g->bar(70, '#FFB900', 'Unit score', 10);
// label each point with its value
$g->set_x_labels( $dataXLabel );
$g->set_x_label_style( 12, '#000000', 2);
$g->set_x_legend('Unit', 10, '#736AFF');
$g->set_y_max(100);
$g->y_label_steps(10);
$g->set_y_legend('Scores', 10, '#736AFF');

// display the data
echo $g->render();

?>