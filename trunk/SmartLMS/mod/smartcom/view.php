<?php  // $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $
    
    /**
     * This page prints a particular instance of smartcom
     *
     * @author  Your Name <your@email.address>
     * @version $Id: view.php,v 1.6.2.3 2009/04/17 22:06:25 skodak Exp $
     * @package mod/smartcom
     */
    
    /// (Replace smartcom with the name of your module and remove this line)
    
    require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
    require_once(dirname(__FILE__).'/lib.php');
    
    $id = optional_param('id', 0, PARAM_INT); // course_module ID, or
    $a  = optional_param('a', 0, PARAM_INT);  // smartcom instance ID
    
    if ($id) {
        if (! $cm = get_coursemodule_from_id('smartcom', $id)) {
            error('Course Module ID was incorrect');
        }
    
        if (! $course = get_record('course', 'id', $cm->course)) {
            error('Course is misconfigured');
        }
    
        if (! $smartcom = get_record('smartcom', 'id', $cm->instance)) {
            error('Course module is incorrect');
        }
    
    } else if ($a) {
        if (! $smartcom = get_record('smartcom', 'id', $a)) {
            error('Course module is incorrect');
        }
        if (! $course = get_record('course', 'id', $smartcom->course)) {
            error('Course is misconfigured');
        }
        if (! $cm = get_coursemodule_from_instance('smartcom', $smartcom->id, $course->id)) {
            error('Course Module ID was incorrect');
        }
    
    } else {
        error('You must specify a course_module ID or an instance ID');
    }
    
    require_login($course, true, $cm);
    
    add_to_log($course->id, "smartcom", "view", "view.php?id=$cm->id", "$smartcom->id");
    
    /// Print the page header
    $strsmartcoms = get_string('modulenameplural', 'smartcom');
    $strsmartcom  = get_string('modulename', 'smartcom');
    
    $navlinks = array();
    $navlinks[] = array('name' => $strsmartcoms, 'link' => "index.php?id=$course->id", 'type' => 'activity');
    $navlinks[] = array('name' => format_string($smartcom->name), 'link' => '', 'type' => 'activityinstance');
    
    $navigation = build_navigation($navlinks);
    
    print_header_simple(format_string($smartcom->name), '', $navigation, '', '', true,
                  update_module_button($cm->id, $course->id, $strsmartcom), navmenu($course, $cm));
    
    /// Print the main part of the page
    
	include_once('./getparams.php');
	include_once('./tabs.php');
	include_once('./moveto.php');
    
    
    /// Finish the page
    print_footer($course);
    
?>