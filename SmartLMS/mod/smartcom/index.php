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
/********** MODULE LIB *************/ 
require_once('./lib.php');
require_once('./locallib.php');


$submodule = required_param('submodule', PARAM_TEXT);   // submodule
$courseid = optional_param('courseid', 0, PARAM_INT);


///////////////////////////////// LITERAL STRING
$strsmartcoms = get_string('modulenameplural', 'smartcom');




///////////////////////////////// Print the header
$navlinks = array();
$course = null;
if($courseid > 1)
{
	$course = get_record('course', 'id', $courseid);
	$navlinks[] = array('name' => $course->shortname, 'link' => "{$CFG->wwwroot}/course/view.php?id=$courseid", 'type' => 'title');
}
$navlinks[] = array('name' => $strsmartcoms, 'link' => '', 'type' => 'activity');
$navlinks[] = array('name' => $submodule, 'link' => '', 'type' => 'title');
$navigation = build_navigation($navlinks);
print_header_simple($strsmartcoms, '', $navigation, '', '', true, '', '');
//print_header($strsmartcoms, '', $navigation, "", "", true, "&nbsp;", navmenu($course));	



///////////////////////////////// 
 // init share Template Savant engine here
$tpl->setPath('template', 'Pagelet');
		
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
'course_completion_suggest_configure',
'ticket_buy'
);

echo '<table width="100%" cellspacing="0" cellpadding="0">
        <tr><td width="20"></td>';
if ($submodule == 'prepaidcard_enduser_deposit' || $submodule == 'prepaidcard_enduser_deposit_history' || $submodule == 'user_account_balance') 
{
    echo '<td width="220" valign="top" style="padding-top: 20px;" >';
    ///////////////////////////////// RENDER content template
    require_once("Pagelet/SHARE_leftmenu_user.php");        
    //echo $SHARE_leftmenu_user;
    echo '</td><td width="20"></td>';
}
else {
    
}


echo '<td>';
///////////////////////////////// RENDER content template
if(in_array($submodule, $allowSubModule, true))
{
	require_once("Pagelet/$submodule.php");        
	echo $$submodule;
}
else
{
	echo 'Hacking activities are logged';
}


echo '</td><td width="20"></td></tr></table>';
///////////////////////////////// Finish the page
print_footer($course);
?>