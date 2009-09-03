<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

global $USER, $CFG;
global $tpl;

if (!$CFG->messaging) {
	return '';
}

$userid = required_param('userid', PARAM_INT);   // course
$courseid = required_param('courseid', PARAM_INT);   // course

$users = get_records_sql("SELECT m.useridfrom as id, COUNT(m.useridfrom) as count,
										 u.firstname, u.lastname, u.picture, u.imagealt, u.lastaccess
									   FROM {$CFG->prefix}user u, 
											{$CFG->prefix}message m 
									   WHERE m.useridto = $userid 
										 AND u.id = m.useridfrom
									GROUP BY m.useridfrom, u.firstname,u.lastname,u.picture,u.lastaccess,u.imagealt");
$tpl->assign('arrayData', $users);

$tpl->assign('userid', $userid);
$tpl->assign('courseid', $courseid);
echo $tpl->fetch("~/blocks/messages/message_list.tpl.php");

?>