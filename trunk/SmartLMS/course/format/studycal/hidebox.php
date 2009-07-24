<?php
/**
 * Turns on or off hiding of a tickbox.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */
 
require_once('../../../config.php');
require_once('../../lib.php');
require_once('lib.php');

$courseid=required_param('course',PARAM_INT);
$sectionindex=required_param('section',PARAM_INT);
$hide=required_param('hide',PARAM_INT);
$coursemoduleid=optional_param('coursemodule',0,PARAM_INT);
$eventid=optional_param('event',0,PARAM_INT);

if(!confirm_sesskey()) {
    error('Session key not valid');
}
if($_SERVER['REQUEST_METHOD']!=='POST') {
    error('This script requires POST requests');
}

// Require login and editing mode (also checks manageactivities)
require_login($courseid);
if(!isediting($courseid)) {
    error('You are not currently editing this course');
}

try {
    studycal_set_hide_box($courseid,$coursemoduleid,$eventid,$hide?true:false);    
    redirect("../../view.php?id=$courseid#section-$sectionindex");
} catch(Exception $e) {
    error($e->getMessage());
}

?>