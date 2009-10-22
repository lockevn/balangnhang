<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once($_SERVER['DOCUMENT_ROOT']."/mod/smartcom/locallib.php");


define('AJAX_CALL',true);
require_login();
// header("Content-type: text/javascript;charset=utf-8");


$courseid = required_param('courseid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);
$childquizid = required_param('childquizid', PARAM_SEQUENCE);

$recs = SmartComDataUtil::GetQuizArrayPercentOfUser($userid, $courseid, $childquizid);
if($recs > 0)
{
	echo $recs;
}


?>