<?php
/**
 * Script run from course view page to lock or unlock the calendar
 * structure.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */

require_once('../../../config.php');
require_once('../../lib.php');

global $CFG;

$courseid=required_param('course',PARAM_INT);
$lock=required_param('lock',PARAM_INT);

if(!confirm_sesskey()) {
    error('Session key not valid');
}
if($_SERVER['REQUEST_METHOD']!=='POST') {
    error('This script requires POST requests');
}

// Force lock to a valid number
$lock = $lock ? 1 : 0;

// Require login and editing mode (also checks manageactivities)
require_login($courseid);
if(!isediting($courseid)) {
    error('You are not currently editing this course');
}

// Check we have lock access
$context = get_context_instance(CONTEXT_COURSE,$courseid);
require_capability('format/studycal:lock',$context);

// See if there are existing settings for this section
$thiscourse=get_record('studycal','courseid',$courseid);
if(!$thiscourse) {
    if(!$lock) {
        error('Calendar is already unlocked');
    }
    if(!execute_sql("INSERT INTO {$CFG->prefix}studycal(courseid,lock) VALUES ($courseid,$lock)")) {
        error('Error creating database entry');
    }
} else {
    if($lock==$thiscourse->lock) {
        error('Calendar is already '.($lock?'':'un').'locked');
    }
    if(!execute_sql("UPDATE {$CFG->prefix}studycal SET lock=$lock WHERE courseid=$courseid")) {
        error('Error updating database entry');
    }    
}

redirect("../../view.php?id=$courseid");
?>