<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/External/Savant3.php");
require_once(ABSPATH."Lib/Text.php");
require_once(ABSPATH."Business/Friend.php");


$following = Friend::CheckFollowing($tpl->AUpid, $tpl->AUpu, $tpl->pid, $tpl->pu);
$tpl->assign('following', $following); 
$blocking = Friend::CheckBlocking($tpl->pid, $tpl->pu);
$tpl->assign('blocking', $blocking); 


$FILENAME = 'action_with_profile';
$$FILENAME = $tpl->fetch("$FILENAME.tpl.php");

?>