<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH.'lib/ofc-library/open_flash_chart_object.php');

$chart1 = open_flash_chart_object_str('90%', 300, 
"/mod/smartcom/api/student_learning_progress_TongQuanKhoaHoc_ofc_data.php?courseid=$courseid&userid=$userid", 
false, '/' );

$chart2 = open_flash_chart_object_str('90%', 300, 
"/mod/smartcom/api/student_learning_progress_ChiTietBaiHoc_ofc_data.php?courseid=$courseid&userid=$userid", 
false, '/' );

	
$tpl->assign('chart1', $chart1);
$tpl->assign('chart2', $chart2);
		
$FILENAME = 'learning_progress';
$$FILENAME = $tpl->fetch("$FILENAME.tpl.php");

?>