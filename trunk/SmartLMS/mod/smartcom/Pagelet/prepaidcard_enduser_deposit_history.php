<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$username = required_param('username', PARAM_TEXT);

$onlineUsers = get_records_sql(
"
select * from mdl_smartcom_card_used where depositforusername = '$username'
");

$tpl->assign('courseid', $courseid);
$tpl->assign('onlineUsers', $onlineUsers);
		
$FILENAME = 'prepaidcard_enduser_deposit_history';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>