<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once(ABSPATH.'lib/ofc-library/open-flash-chart.php');
$g = new OFCgraph();

$g->title( ''. date('D, j-M-Y  G:i:s'), '{font-size: 26px;}' );

$data = array();
$data[] = 7;
$data[] = 8;


$g->set_data($data);

// label each point with its value
$g->set_x_labels( array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec' ) );

// set the Y max
$g->set_y_max(60);
// label every 20 (0,20,40,60)
$g->y_label_steps(6);

// display the data
echo $g->render();
?>