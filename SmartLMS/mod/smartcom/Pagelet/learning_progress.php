<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH.'lib/ofc-library/open_flash_chart_object.php');


require_login();
$userid = $USER->id;

$courseid = required_param('courseid', PARAM_INT);   // course


$chartTongQuanKhoaHoc = open_flash_chart_object_str('45%', 300, 
"/mod/smartcom/api/student_learning_progress_TongQuanKhoaHoc_ofc_data.php?courseid=$courseid&userid=$userid", 
false, '/' );

$chartTongQuanKhoaHocTheoKyNang = open_flash_chart_object_str('45%', 300, 
"/mod/smartcom/api/student_learning_progress_TongQuanKhoaHocTheoKyNang_ofc_data.php?courseid=$courseid&userid=$userid", 
false, '/' );




$chartChiTietBaiHoc = open_flash_chart_object_str('90%', 300, 
"/mod/smartcom/api/student_learning_progress_ChiTietBaiHoc_ofc_data.php?courseid=$courseid&userid=$userid", 
false, '/');

	
$tpl->assign('chartTongQuanKhoaHoc', $chartTongQuanKhoaHoc);
$tpl->assign('chartTongQuanKhoaHocTheoKyNang', $chartTongQuanKhoaHocTheoKyNang);

$tpl->assign('chartChiTietBaiHoc', $chartChiTietBaiHoc);
$tpl->assign('chitietbaihocurl', "/mod/smartcom/api/student_learning_progress_ChiTietBaiHoc_ofc_data.php?courseid=$courseid&userid=$userid");

		
$FILENAME = 'learning_progress';
$$FILENAME = $tpl->fetch("$FILENAME.tpl.php");

?>