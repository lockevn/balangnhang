<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

// $Id: index.php,v 1.7.2.2 2009/03/31 13:07:21 mudrd8mz Exp $

/**
 * This page lists all the instances of smartcom in a particular course
 *
 * @author  Your Name <your@email.address>
 * @version $Id: index.php,v 1.7.2.2 2009/03/31 13:07:21 mudrd8mz Exp $
 * @package mod/smartcom
 */

 global $CFG;
 /********** MODULE LIB *************/ 
require_once('./lib.php');

$courseid = required_param('courseid', PARAM_INT);   // course
$userid = required_param('userid', PARAM_INT);   // course
$submodule = required_param('submodule', PARAM_TEXT);   // submodule



if (! $course = get_record('course', 'id', $courseid)) {
	error('Course ID is incorrect');
}

require_course_login($course);
// add_to_log($course->id, 'smartcom', 'view all', "index.php?coủid=$course->id", '');

/// LITERAL STRING
$strsmartcoms = get_string('modulenameplural', 'smartcom');

/// Print the header
$navlinks = array();
$navlinks[] = array('name' => $strsmartcoms, 'link' => '', 'type' => 'activity');
$navigation = build_navigation($navlinks);
print_header_simple($strsmartcoms, '', $navigation, '', '', true, '', navmenu($course));
print_heading($strsmartcoms . ' Learning Progress');


switch ($submodule) {
   case 'learning_progress':
   
	require_once(ABSPATH.'lib/ofc-library/open_flash_chart_object.php');
	open_flash_chart_object('100%', 300, 
	"/mod/smartcom/api/student_learning_progress_TongQuanKhoaHoc_ofc_data.php?courseid=$courseid&userid=$userid", 
	false, '/' );

	echo '<br /><br /><br /><br />';

	require_once(ABSPATH.'lib/ofc-library/open_flash_chart_object.php');
	open_flash_chart_object('100%', 300, 
	"/mod/smartcom/api/student_learning_progress_ChiTietBaiHoc_ofc_data.php?courseid=$courseid&userid=$userid", 
	false, '/' );
	break;
	 
   case '':

	 break;
}


/// Finish the page
print_footer($course);

?>
