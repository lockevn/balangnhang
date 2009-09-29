<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");


require_login();
$userid = $USER->id;
$username = $USER->username;


$onlineUsers = get_records_sql(
"select * from mdl_smartcom_account
where username = '$username';"
);

$tpl->assign('onlineUsers', $onlineUsers);
		
$FILENAME = 'user_account_balance';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>