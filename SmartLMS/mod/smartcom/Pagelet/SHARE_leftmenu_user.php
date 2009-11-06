<?php require_once($_SERVER['DOCUMENT_ROOT']."/Gconfig.php");
require_once($_SERVER['DOCUMENT_ROOT']."/config.php");




$FILENAME = 'SHARE_leftmenu_user';
$$FILENAME = $tpl->display("$FILENAME.tpl.php");

?>