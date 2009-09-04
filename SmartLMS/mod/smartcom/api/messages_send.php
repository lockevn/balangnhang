<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");
require_once(ABSPATH.'lib/datalib.php');


$message = required_param('message', PARAM_TEXT);
$fromuserid = required_param('fromuserid', PARAM_INT);
$touserid = required_param('touserid', PARAM_INT);

/// Save the new message in the database
$savemessage = new object();
$savemessage->useridfrom    = $fromuserid;
$savemessage->useridto      = $touserid;
$savemessage->message       = $message;
// $savemessage->format        = $format;
$savemessage->timecreated   = time();
$savemessage->messagetype   = 'direct';


if (!$savemessage->id = insert_record('message', $savemessage)) 
{
	echo 'send message fail';
}
else
{
	echo "send message ok from $fromuserid, to $touserid, message: $message";
}	

?>