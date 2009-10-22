<?php
/**
 * Form that allows users to alter settings (all two of them) 
 * for an individual calendar week.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */

require_once('../../../config.php');
require_once('../../lib.php');

require_once('editweek_form.php');

global $CFG;

$courseid=required_param('course',PARAM_INT);
$course=get_record('course','id',$courseid);
if(!$course) {
    error('Could not find specified course');
}
$sectionindex=required_param('section',PARAM_INT);

// Require login and editing mode (also checks manageactivities)
require_login($courseid);
if(!isediting($courseid)) {
    error('You are not currently editing this course');
}

// Check calendar isn't locked
$lock=get_field('studycal','lock','courseid',$course->id);
if($lock) {
    error('The calendar structure is locked and cannot be edited');
}

// Check they have the capability
$context = get_context_instance(CONTEXT_COURSE, $course->id);
if(!has_capability('format/studycal:manage',$context)) {
    error('You do not have permission to manage this calendar');
}

// Get all sections and check section index is valid
$sections=get_all_sections($courseid);
if($sectionindex > count($sections) || $sectionindex < 1) {
    error('Invalid section index'); 
}
$thissection=$sections[$sectionindex];

$mform = new format_studycal_editweek_form();

// Get week settings for this section, or initialise.
if(!($thisweek=get_record('studycal_weeks','sectionid',$thissection->id))) {
    $thisweek = new StdClass;
    $thisweek->hidenumber=0;
    $thisweek->hidedate=0;
    $thisweek->title=null;
    $thisweek->resetnumber=null;
    $thisweek->needscreating=true;
}
$thisweek->resetnumberon = !is_null($thisweek->resetnumber);
$mform->set_data($thisweek);

//default 'action' for form is strip_querystring(qualified_me())
if ($mform->is_cancelled()){
    //you need this section if you have a cancel button on your form
    //here you tell php what to do if your user presses cancel
    //probably a redirect is called for!
} else if ($fromform = $mform->get_data()){
//this branch is where you process validated data.

    if(!confirm_sesskey()) {
        error('Session key not valid');
    }

    $hidenumber = isset($fromform->hidenumber) ? 1 : 0;
    $hidedate = isset($fromform->hidedate) ? 1 : 0;
    $resetnumber = isset($fromform->resetnumberon) ? $fromform->resetnumber : 'NULL';
    $title = (''===$fromform->title) ? 'NULL' : "'".addslashes($fromform->title)."'";


    if(!empty($thisweek->needscreating)) {
        if(!execute_sql("
INSERT INTO {$CFG->prefix}studycal_weeks(sectionid,hidenumber,resetnumber,title,hidedate) 
VALUES({$thissection->id},$hidenumber,$resetnumber,$title,$hidedate)")) {
        error('Unable to create database entry');
        }
    } else {        
        if(!execute_sql("
UPDATE {$CFG->prefix}studycal_weeks 
SET hidenumber=$hidenumber,resetnumber=$resetnumber,title=$title,hidedate=$hidedate 
WHERE sectionid={$thissection->id}")) {
        error('Unable to update database');
        }
    }

    redirect("../../view.php?id=$courseid#section-$sectionindex");
    exit;

} else {
// this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
// or on the first display of the form.
    //setup strings for heading
    $streditweeksettings=get_string('editweeksettings','format_studycal');
    $navigation=array();
    $navigation[]=array('name' => $streditweeksettings, 'link' => '', 'type' => 'studycal');
    print_header($streditweeksettings, "$course->fullname",
    build_navigation($navigation));

    //notice use of $mform->focus() above which puts the cursor 
    //in the first form field or the first field with an error.

    print_heading($streditweeksettings);

    //call to print_heading_with_help or print_heading? then :

    //put data you want to fill out in the form into array $toform here then :
    $toform = array();

    $mform->set_data(array(
        'course'    => $courseid,
        'section'   => $sectionindex, /*
        'hidenumber'=> $thisweek->hidenumber,
        'hidedate'  => $thisweek->hidedate,
        'resetnumberon'=>!is_null($thisweek->resetnumber),
        'resetnumber'=>$thisweek->resetnumber,
        'title'     => $thisweek->title*/ ));

    $mform->display();

    print_footer();
}
       
?>