<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/External/Savant3.php");
require_once(ABSPATH."Business/Message.php");




$arrayData = Message::GetMessageDataFromAPI($mod);

if($mod == 'user' && Common::GetCurrentPageNumber() <= 1)
{
    $arrLastMsg[] = $arrayData[0];
    $arrLastMsg = Common::convertTime($arrLastMsg);
    $tpl->assign('arrLastMsg', $arrLastMsg[0]);
}

$arrayData = Common::convertTime($arrayData);
$tpl->assign('arrayData', $arrayData);

$paging = common::Paging($arrayData);
$tpl->assign('paging', $paging);

$FILENAME = 'msglist';
$$FILENAME = $tpl->fetch("COMMON.msglist.tpl.php");

?>