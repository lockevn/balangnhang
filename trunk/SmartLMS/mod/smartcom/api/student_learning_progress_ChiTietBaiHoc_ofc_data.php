<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH.'lib/ofc-library/open-flash-chart.php');
$g = new OFCgraph();

$g->title('Chi tiết bài học', '{font-size: 26px;}');

$data1 = array();
$data2 = array();
$recs = get_records_sql('select id as id, id*2 as idx2 from mdl_user');
foreach (((array)$recs) as $key => $value) {        
	$data1[] = $key;
	$data2[] = $value->idx2;
}

$g->set_data($data1);
$g->bar( 20, '#FFB900', '', 10 );

// label each point with its value
$g->set_x_labels( array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec' ) );
$g->set_x_label_style( 12, '#000000', 2);

// set the Y max
$g->set_y_max(60);
// label every 20 (0,20,40,60)
$g->y_label_steps(6);


// display the data
echo $g->render();
?>