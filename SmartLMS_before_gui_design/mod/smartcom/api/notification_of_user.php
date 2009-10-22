<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

define('AJAX_CALL',true);
header("Content-type: text/javascript;charset=utf-8");
require_login();

$courseid = required_param('courseid', PARAM_INT);
$context = get_context_instance(CONTEXT_COURSE, $courseid);
if(!has_capability('mod/smartcom:poolingnotification', $context, $USER->id, false))
{
	die("{'stat':'NOT_ALLOW', 'courseid':'$courseid', 'userid':'{$USER->id}'}");
}

$username = $USER->username;
$sql = "select * from mdl_smartcom_notification where receiverusername = '$username' order by id";
$result = get_records_sql($sql);
if($result && is_array($result))
{	
	$result = array_values($result);
	echo json_encode($result); 
}

?>