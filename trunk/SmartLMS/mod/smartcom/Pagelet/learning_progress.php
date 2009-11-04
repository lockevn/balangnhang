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

/**
* Tabs include
* 
* @var mixed
*/
$userid = optional_param('id', $USER->id, PARAM_INT);    // user id
$course = optional_param('course', SITEID, PARAM_INT);   // course id (defaults to Site)
if (!$course = get_record('course', 'id', $course)) {
    error('Course ID was incorrect');
}
// The user profile we are editing
if (!$user = get_record('user', 'id', $userid)) {
    error('User ID was incorrect');
}
$tpl->assign('user', $user);
$tpl->assign('USER', $USER);
$tpl->assign('course', $course);
$tpl->assign('CFG', $CFG);

        
$FILENAME = 'learning_progress';
$$FILENAME = $tpl->fetch("$FILENAME.tpl.php");

?>