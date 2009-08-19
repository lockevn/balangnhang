<?php require_once($_SERVER['DOCUMENT_ROOT']."/config.php");

require_once(ABSPATH."Lib/External/Savant3.php");
require_once(ABSPATH."Business/Common.php");



$userinfo = Common::GetCurrentProfileInfo();

$tpl->assign('id', $userinfo['id']);
$tpl->assign('u', $userinfo['u']);
$tpl->assign('phone', $userinfo['phone']);
$tpl->assign('img', $userinfo['img']);
$tpl->assign('loc', $userinfo['loc']);
$tpl->assign('e', $userinfo['e']);
$tpl->assign('www', $userinfo['www']);
$tpl->assign('lang', $userinfo['lang']);
$tpl->assign('bio', $userinfo['bio']);

$FILENAME = 'profile_namecard';
$$FILENAME = $tpl->fetch("$FILENAME.tpl.php");

?>