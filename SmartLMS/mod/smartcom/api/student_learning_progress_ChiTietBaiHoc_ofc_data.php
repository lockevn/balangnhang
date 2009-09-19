<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH.'lib/ofc-library/open-flash-chart.php');
require_once(ABSPATH.'lib/datalib.php');


$courseid = required_param('courseid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$sectionid = optional_param('sectionid',0 , PARAM_INT);
$lessonname = optional_param('lessonname', '' , PARAM_TEXT);

$g = new OFCgraph();
$g->title("Chi tiết bài học $lessonname", '{font-size: 20px;}');

$data1 = array();
$dataXLabel = array();


$mapResourceName_QuizID = array();

$arrCourseModule = get_coursemodules_in_course('quiz', $courseid);
foreach (((array)$arrCourseModule) as $key => $value) {
	$resourceName = get_parent_resource_name($value);	
	if(empty($resourceName) == false)
	{
		$mapResourceName_QuizID[] = array('resourcename' => $resourceName, 'quizid' => $value->instance);	
	}
}

foreach (((array)$mapResourceName_QuizID) as $value) {
	$dataXLabel[] = $value['resourcename'];		
}
$dataXLabel = array_unique($dataXLabel);




if($sectionid > 0)
{
	/* get quiz of course */
	$recsMaxSumGrades = get_records_sql(
	"select id as quiz, name, sumgrades from mdl_quiz where course=$courseid and sumgrades>0;"
	);

	/*get grade of user of course */
	$recsUserSumGrades = get_records_sql(
	"select quiz, max(sumgrades) as sumgrades 
	from `mdl_quiz_attempts` 
	where userid=$userid and quiz in (select id from mdl_quiz where course=$courseid) 
	group by quiz;"
	);

	$assocResourceData = array();
	if($mapResourceName_QuizID != false)
	{
		foreach (((array)$mapResourceName_QuizID) as $mapEntry) {	
			
			$nMaxGrade = $recsMaxSumGrades[$mapEntry['quizid']]->sumgrades;		
			$assocResourceData[$mapEntry['resourcename']]['maxgrade'] += $nMaxGrade;
			
			$nUserGrade = $recsUserSumGrades[$mapEntry['quizid']]->sumgrades;        
			$assocResourceData[$mapEntry['resourcename']]['usergrade'] += $nUserGrade;
		}
	}

	foreach (((array)$dataXLabel) as $resourceName) {
		$data1[] = 100*$assocResourceData[$resourceName]['usergrade']/$assocResourceData[$resourceName]['maxgrade'];
	}

	$g->set_data($data1);
	//$g->set_data($assocResourceData);
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