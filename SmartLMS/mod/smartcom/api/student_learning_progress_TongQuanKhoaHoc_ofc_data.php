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
"
select instance as quizid, cm.section, cs.`label`
from `mdl_course_modules` as cm
join mdl_course_sections as cs
on cm.section = cs.id
and cm.course = $courseid
and cs.course = $courseid
and cm.module=12
"
);
foreach (((array)$arrCourseModule) as $key => $value) {	
	$mapLessonName_QuizID[] = array(
		'resourcename' => $value->section, 
		'quizid' => $value->quizid,
		'label' => $value->label
		);
}

foreach (((array)$mapLessonName_QuizID) as $value) {
	$dataXLabel[] = $value['label'];        
}
$dataXLabel = array_unique($dataXLabel);





/* get quiz of course */
$recsMaxSumGrades = get_records_sql(
"select id as quiz, name, sumgrades from mdl_quiz where course=$courseid and sumgrades>0;"
);

/*get grade of user of course */
$recsUserSumGrades = get_records_sql(
"select quiz, grade as sumgrades 
from `mdl_quiz_grades` 
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


$bar = new bar(70, '#FFB900');
$bar->key( 'Unit score', 10);

foreach (((array)$assocResourceData) as $key => $value) {
	$dataEntry = 100*$value['usergrade']/$value['maxgrade'];
	$lessonname = '';
	foreach (((array)$mapLessonName_QuizID) as $value) {
		if($value['resourcename'] == $key)
		{
			$lessonname = $value['label'];
			break;
		}    
	}
	$bar->add_link($dataEntry, "javascript:showDetailChartOfLesson($key,'$lessonname');" );
}
$g->data_sets[] = $bar;


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