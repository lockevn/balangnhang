<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

$username = required_param('username', PARAM_TEXT);

$onlineUsers = get_records_sql(
"select * from mdl_smartcom_account
where username = '$username';"
);


$tpl->assign('courseid', $courseid);
$tpl->assign('onlineUsers', $onlineUsers);
		
$FILENAME = 'user_account_balance';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>