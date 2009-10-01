<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

define('AJAX_CALL',true);
header("Content-type: text/javascript;charset=utf-8");
require_login();

$context = get_context_instance(CONTEXT_SYSTEM);
if(!has_capability('mod/smartcom:poolingnotification', $context, $USER->id, false))
{
	die('{"stat":"NOT_ALLOW"}');
}

$username = $USER->username;
$sql = "select * from mdl_smartcom_notification where receiverusername = '$username'";
$result = get_records_sql($sql);
if($result && is_array($result))
{	
	$result = array_values($result);
	echo json_encode($result); 
}

?>