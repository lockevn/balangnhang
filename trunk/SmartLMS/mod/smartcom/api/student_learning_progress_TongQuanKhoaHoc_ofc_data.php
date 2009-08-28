<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH.'lib/ofc-library/open-flash-chart.php');

$courseid = required_param('courseid', PARAM_INT);   // course
$userid = required_param('userid', PARAM_INT);   // course


$g = new OFCgraph();
$g->title('Tổng quan khoá học', '{font-size: 26px;}');

$data1 = array();
$dataXLabel = array();



$mapLessonName_QuizID = array();

$arrCourseModule = get_records_sql(
"select instance as quizid, section from `mdl_course_modules` where section in
(select id from mdl_course_sections where course=$courseid)
and module=12"
);
foreach (((array)$arrCourseModule) as $key => $value) {	
	$mapLessonName_QuizID[] = array('resourcename' => $value->section, 'quizid' => $value->quizid);    	
}

foreach (((array)$mapLessonName_QuizID) as $value) {
	$dataXLabel[] = $value['resourcename'];        
}
$dataXLabel = array_unique($dataXLabel);





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
if($mapLessonName_QuizID != false)
{
	foreach (((array)$mapLessonName_QuizID) as $mapEntry) {    
		
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