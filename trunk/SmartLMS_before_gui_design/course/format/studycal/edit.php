<?php
/**
 * Form that allows users to alter settings for a calendar. 
 * 
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */

require_once('../../../config.php');
require_once('../../lib.php');
require_once('./lib.php');

global $CFG;

$courseid=required_param('course',PARAM_INT);
$course=get_record('course','id',$courseid);
if(!$course) {
    error('Could not find specified course');
}

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

// Get existing calendar settings
$studycal=studycal_get_settings($courseid);

if($_SERVER['REQUEST_METHOD']==='POST') {
    if(!confirm_sesskey()) {
        error('Session key not valid');
    }
    
    $hidenumbers=optional_param('hidenumbers','',PARAM_ALPHA) ? 1 : 0;
    $startdateoffset=
        make_timestamp(required_param('startyear',PARAM_INT),
            required_param('startmonth',PARAM_INT),
            required_param('startday',PARAM_INT))-$course->startdate;
    
    $weekstoview=required_param('weekstoview',PARAM_INT);
    if($weekstoview<1) {
        error('Must show at least 1 week');
    }
    
    if(!empty($studycal->defaultrecord)) {
        if(!execute_sql("
INSERT INTO {$CFG->prefix}studycal(courseid,startdateoffset,hidenumbers,weekstoview) 
VALUES($courseid,$startdateoffset,$hidenumbers,$weekstoview)")) {
global $db;
        error('Unable to create database entry '.$db->ErrorMsg());
        }
    } else {        
        if(!execute_sql("
UPDATE {$CFG->prefix}studycal
SET startdateoffset=$startdateoffset,hidenumbers=$hidenumbers,weekstoview=$weekstoview 
WHERE courseid={$courseid}")) {
        error('Unable to update database');
        }
    }
    
    redirect("../../view.php?id=$courseid");
    exit;
}

$streditcalendarsettings=get_string('editcalendarsettings','format_studycal');
$navigation=array();
$navigation[]=array('name' => $streditcalendarsettings, 'link' => '', 'type' => 'studycal');
print_header($streditcalendarsettings, "$course->fullname",
    build_navigation($navigation));
        
print_heading($streditcalendarsettings);
print_simple_box_start("center");
$strhideweeknumbers=get_string('hideweeknumbers','format_studycal');
$strstartdate=get_string('startdate','format_studycal');
$strweekstoview=get_string('weekstoview','format_studycal');

$hidechecked=$studycal->hidenumbers ? "checked='checked'" : '';
$strsavechanges=get_string('savechanges');
$sesskey=sesskey();

// Expect courses to start on a Saturday
$msgstartdate = '';
if (!empty($CFG->ousite) && date('w', $course->startdate+$studycal->startdateoffset) != 6) {
    $msgstartdate = '<strong>'.trim(userdate($course->startdate+$studycal->startdateoffset, 
                     get_string('strftimedateshort'), 666)).' is not a Saturday</strong>. ' .
                     'OU weeks start on Saturdays and we recommend that all course calendars ' .
                     'follow standard OU weeks. If the course doesn\'t start on a Saturday, ' .
                     'students who use the multiple course calendar view may find it slightly confusing.';
}

// TODO Update this using the new forms library stuff once it's there.
print "
<form method='post' action='edit.php'>
<input type='hidden' name='course' value='$courseid' />
<input type='hidden' name='sesskey' value='$sesskey' />
<p><input name='hidenumbers' id='hidenumbers' type='checkbox' $hidechecked /> 
<label for='hidenumbers'>$strhideweeknumbers</label></p>
$msgstartdate
<p>$strstartdate: ";
print_date_selector('startday','startmonth','startyear',$course->startdate+$studycal->startdateoffset);
print "</p>
<p><label for='weekstoview'>$strweekstoview: </label> <select name='weekstoview' id='weekstoview'>";
for($weeks=1;$weeks<=9;$weeks+=2) {
    $selected=($weeks==$studycal->weekstoview) ? "selected='selected'" : "";
    print "<option $selected>$weeks</option>";
}
print "
</select>
<p>
<input type='submit' value='$strsavechanges' />
</p>
</form> 
";
print_simple_box_end();

// Optionally include upload feature if the folder is present. (i.e.
// this is a way to have the feature in OU but not distribute it outside
// where it's no use and confusing) 
if(file_exists($uploadform=dirname(__FILE__).'/upload/uploadform.php')) {
    require($uploadform);
}
?>