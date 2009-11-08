<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_login();
$userid = $USER->id;
$username = $USER->username;

$sql = "select * from mdl_smartcom_card_used where depositforusername = '$username'";
$histories = get_records_sql($sql);
$tpl->assign('histories', $histories);
$tpl->assign('CFG', $CFG);
		
$FILENAME = 'prepaidcard_enduser_deposit_history';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>