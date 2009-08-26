<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH.'lib/ofc-library/open-flash-chart.php');
$g = new OFCgraph();

$g->title('Tổng quan khoá học', '{font-size: 26px;}');

$data1 = array();
$data2 = array();
$recs = get_records_sql('select id as id, id*2 as idx2 from mdl_user');
foreach (((array)$recs) as $key => $value) {		
	$data1[] = $key;	
}
$g->set_data($data1);

$g->bar(20, '#FFB900', 'Unit score', 10);

// label each point with its value
$g->set_x_labels( array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec' ) );
$g->set_x_label_style( 12, '#000000', 2);

$g->set_y_max(100);
$g->y_label_steps(10);

// display the data
echo $g->render();
?>