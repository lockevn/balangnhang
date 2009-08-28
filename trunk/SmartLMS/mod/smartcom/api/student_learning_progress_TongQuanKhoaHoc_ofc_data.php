<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH.'lib/ofc-library/open-flash-chart.php');

$courseid = required_param('courseid', PARAM_INT);   // course
$userid = required_param('userid', PARAM_INT);   // course


$g = new OFCgraph();
$g->title('Tổng quan khoá học', '{font-size: 26px;}');

$data1 = array();
$dataXLabel = array();


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

if($recsUserSumGrades != false)
{
	foreach (((array)$recsMaxSumGrades) as $key => $value) {		
		$userGrades = $recsUserSumGrades[$key]->sumgrades;
		$data1[] = 100*$userGrades/$value->sumgrades;
		$dataXLabel[] = $value->name;
	}
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