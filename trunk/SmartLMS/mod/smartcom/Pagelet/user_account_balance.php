<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");


require_login();
$userid = $USER->id;
$username = $USER->username;

$sql = "select * from mdl_smartcom_account where username = '$username'";
$accountBalance = get_record_sql($sql);
$tpl->assign('accountBalance', $accountBalance);
$tpl->assign('CFG', $CFG);		
$FILENAME = 'user_account_balance';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>