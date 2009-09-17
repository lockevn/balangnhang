<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

/**
 * This page lists all the instances of smartcom in a particular course
 *
 * @author  GURUCORE <info@gurucore.com>
 * @version $Id: index.php,v 1.7.2.2 2009/03/31 13:07:21 mudrd8mz Exp $
 * @package mod/smartcom
 */

global $CFG;

 // init share Template Savant engine here
$tpl->setPath('template', 'Pagelet');



/********** MODULE LIB *************/ 
require_once('./lib.php');
require_once('./locallib.php');

$courseid = required_param('courseid', PARAM_INT);   // course
$submodule = required_param('submodule', PARAM_TEXT);   // submodule
$userid = optional_param('userid', PARAM_INT);


if (! $course = get_record('course', 'id', $courseid)) {
	error('GURUCORE: Course ID is incorrect');
}

// TODO: uncomment out
// require_course_login($course);
// add_to_log($course->id, 'smartcom', 'view all', "index.php?coủid=$course->id", '');

/// LITERAL STRING
$strsmartcoms = get_string('modulenameplural', 'smartcom');

/// Print the header
$navlinks = array();
$navlinks[] = array('name' => $strsmartcoms, 'link' => '', 'type' => 'activity');
$navigation = build_navigation($navlinks);
print_header_simple($strsmartcoms, '', $navigation, '', '', true, '', navmenu($course));

$allowSubModule = array(
'learning_progress',
'realtime_performance_check',
'prepaidcard_usage_report',
'prepaidcard_manager',
'prepaidcard_enduser_deposit',
'prepaidcard_generator',
'prepaidcard_adjust',
'prepaidcard_enduser_deposit_history',
'user_account_balance',
'course_completion_suggest_configure'
);

if(in_array($submodule, $allowSubModule, true))
{
	require_once("Pagelet/$submodule.php");        
	echo $$submodule;
}
else
{
	echo 'Hacking activities are logged';
}

/// Finish the page
print_footer($course);

?>