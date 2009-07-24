<?php
/**
 * AJAX-accessed function that stores ticks for a user.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package package_name
 *//** */

// Sends an error back to AJAX handler
function send_ajax_error($message) {
    print $message;
    exit;
}

require_once('../../../config.php');
require_once('lib.php');
global $CFG,$USER;

// Just require them to be logged in. No need for other security,
// for example they can feel free to save ticks for courses they 
// aren't on if they really want. Note that I don't use require_login
// because that sends errors in a way the YUI handler can't understand.
if(!isloggedin()) {
    send_ajax_error('Not logged in');
}

// Get, check, and parse request parameter
$request=required_param('request',PARAM_RAW);
$matches=array();
if(!preg_match('/^([0-9]+)_(cm|e)([0-9]+)_([01])$/',$request,$matches)) {
    send_ajax_error('Unexpected request string'.$request);
}
list($junk,$courseid,$type,$instanceid,$on)=$matches;

try {
    $coursemoduleid=($type=='cm') ? $instanceid : 0;
    $eventid=($type=='e') ? $instanceid : 0;
    studycal_set_ticked($courseid,$USER->id,$coursemoduleid,$eventid,$on);
    print 'OK';
} catch(Exception $e) {
    send_ajax_error($e->getMessage());
}
?>