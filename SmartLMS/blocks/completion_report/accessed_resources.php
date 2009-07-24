<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Accessed Resources</title>
<style>
body
{
    font-size: 100%;
    font-family: Arial;
}
</style>
</head>
<body>
<?php
 /*
 * accessed_resources.php - part of block_completion_report
 *            - displays the accessed resources of a specified course, by a specified user
 *              according to the configured rubrics using the configure page, and the log table in the database
 * created by Andrew Chow, of Lambda Solutions Inc., Vancouver, BC, Canada
 * http://www.lambdasolutions.net/ - andrew@lambdasolutions.net
 * based on block tutorial by Jon Papaioannou (pj@uom.gr)
 * with all the French translation files in /lang/fr_utf8/ created by Valery Fremaux at http://www.ethnoinformatique.fr/
 */ 
 
require_once('../../config.php');
require_once($CFG->dirroot .'/lib/datalib.php');
require_once($CFG->dirroot .'/course/lib.php');
require_once($CFG->dirroot .'/lib/blocklib.php');
require_once('lib.php');

// required course id parameter passed by URL
$courseid = optional_param('course', 0, PARAM_RAW); // 

// course name displayed on the page
$coursename = get_field( 'course', 'fullname', 'id', $courseid);

// user id displayed on the page
$userid = optional_param('user', 0, PARAM_RAW); // 


$context = get_context_instance(CONTEXT_COURSE, $courseid);
$str_role = get_user_roles_in_context($USER->id, $context->id);
$context_admin = get_context_instance(CONTEXT_SYSTEM);
$str_admin_role = get_user_roles_in_context($USER->id, $context_admin->id);
if (
                ( strpos($str_role, 'Teacher') > 0 || 
                strpos( $str_admin_role, 'Administrator') > 0 ) || 
                has_capability('moodle/legacy:admin') || 
                has_capability('moodle/legacy:teacher') || 
                has_capability('moodle/legacy:editingteacher')
   )
{
    display_accessed_resources( $courseid, $userid );
    display_required_resources( $courseid, false );
}

?>
</body>
</html>
