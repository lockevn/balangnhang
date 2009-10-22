<?php
/**
 * Script run from course view page to turn on/off the 'show all weeks'
 * setting.
 *
 * @copyright &copy; 2006 The Open University
 * @author s.marshall@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package studycal
 *//** */

require_once('../../../config.php');

// No need for security checks as this only controls a session variable 
$courseid=required_param('course',PARAM_INT);
$all=required_param('all',PARAM_INT);

if($all) {
    $USER->allweeks[$courseid]=true;
} else {
    unset($USER->allweeks[$courseid]);
}

redirect("../../view.php?id=$courseid");
?>