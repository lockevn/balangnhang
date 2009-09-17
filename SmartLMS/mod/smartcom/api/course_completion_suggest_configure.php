<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once(ABSPATH."lib/db/DBHelper.php");

define('AJAX_CALL',true);
require_login();

$finalexamquizpercent = required_param('finalexamquizpercent', PARAM_TEXT);
$averageoverallquizzespercent = required_param('averageoverallquizzespercent', PARAM_INT);
$finalexamid = required_param('finalexamid', PARAM_INT);
$nextcourseidset = required_param('nextcourseidset', PARAM_SEQUENCE);
$courseid = required_param('courseid', PARAM_INT);

$enabletoapply = required_param('enabletoapply', PARAM_TEXT);
if($enabletoapply == 'true')
{
	$enabletoapply = 1;
}

$sql = "
replace into `mdl_smartcom_course_completion_suggestion` 
(courseid, overallquizzespercent, finalquizid, finalquizpercent, nextcourseidset,isenable)
values ($courseid, $averageoverallquizzespercent, $finalexamid, $finalexamquizpercent, '$nextcourseidset', $enabletoapply)
";

$ret = execute_sql($sql, false);
if($ret)
{
	echo 'ok';
}
else
{
	echo 'fail';
}

?>