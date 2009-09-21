<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

define('AJAX_CALL',true);
require_login();
header("Content-type: text/javascript;charset=utf-8");


$username = $USER->username;

$sql = "select * from mdl_smartcom_notification where receiverusername = '$username'";
$result = get_records_sql($sql);
if($result && is_array($result))
{	
	$result = array_values($result);
	echo json_encode($result); 
}

?>