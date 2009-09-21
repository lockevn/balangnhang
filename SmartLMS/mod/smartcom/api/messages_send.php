<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once(ABSPATH.'lib/datalib.php');


define('AJAX_CALL',true);
require_login();


$message = required_param('message', PARAM_TEXT);
$from = required_param('from', PARAM_TEXT);
$to = required_param('to', PARAM_TEXT);
$link = optional_param('link', 'http://gurucore.com', PARAM_TEXT);


/// Save the new message in the database
$savemessage = new object();
$savemessage->senderusername = $from;
$savemessage->receiverusername = $to;
$savemessage->message = $message;
$savemessage->link = $link;


if (!$savemessage->id = insert_record('smartcom_notification', $savemessage)) 
{
	echo 'send message fail';
}
else
{
	echo "send message ok from $from, to $to, message: $message";
}	

?>