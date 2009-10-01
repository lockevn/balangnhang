<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");

require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

define('AJAX_CALL',true);
// header("Content-type: text/javascript;charset=utf-8");
require_login();

$notificationid = required_param('id', PARAM_INT);
$username = $USER->username;

$result = delete_records('smartcom_notification', 'id', $notificationid, 'receiverusername', $username);
if($result)
{
	echo 'ok';
}
else
{
	echo 'fail';
}
?>