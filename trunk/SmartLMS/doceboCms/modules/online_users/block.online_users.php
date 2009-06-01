<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

function online_users_showMain($idBlock, $title, $block_op) {

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	if ((isset($title)) && ($title != ''))
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");


	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang =DoceboLanguage::createInstance("message");


	require_once($GLOBALS["where_framework"]."/lib/lib.myfriends.php");
	$friends = new MyFriends(getLogUserId());
	$arr_friends =$friends->getFriendsList();
	$arr_id_friends =array_keys($arr_friends);
	$stats_required =array("all", "register_today", "register_yesterday", "register_7d", "now_online");

	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	$user_dir = new Module_Directory();
	$user_stats = $user_dir->getUsersStats($stats_required, $arr_id_friends);

	$opt =loadBlockOption($idBlock);
	$show_user_stat =(isset($opt["show_user_stat"]) ? (bool)$opt["show_user_stat"] : FALSE);

	if ($show_user_stat) {

		$out->add($lang->def('_TOTAL_USER').': <b>'.($user_stats['all'] - 1).'</b><br />');

		$out->add($lang->def('_REG_TODAY').': <b>'.$user_stats['register_today'].'</b><br />');
		$out->add($lang->def('_REG_YESTERDAY').': <b>'.$user_stats['register_yesterday'].'</b><br />');
		$out->add($lang->def('_REG_LASTSEVENDAYS').': <b>'.$user_stats['register_7d'].'</b><br />');
	}

	// --------------------------------------------------------------------------

	addCss('windows', "framework");
	addCss('instmsg', "framework");
	addAjaxJs();
	addScriptaculousJs();
	addJs($GLOBALS['where_framework_relative'].'/modules/instmsg/','instmsg.js');
	$acl_man  = $GLOBALS['current_user']->getAclManager();
	$idUser = $GLOBALS['current_user']->getUserid();
	$userName = $GLOBALS['current_user']->getUserName();
	$GLOBALS['page']->add('<script type="text/javascript">'.'setup_instmsg(\''.$GLOBALS['where_framework_relative'].'/modules/instmsg/ajax.instmsg.php\',\''.$idUser.'\',\''.$userName.'\',\''.getPathImage('fw').'\', \'cms\')'     .'</script>', 'page_head');
	//$GLOBALS['page']->add('<div class="box_whois_online">'    .'<span>'.$lang->def('_WHOIS_ONLINE').' : </span><div id="user_online_n"><a href="javascript:;"onclick="openUsersList()">'.TrackUser::getWhoIsOnline($_SESSION['idCourse']).'</a></div><br />', 'content');
	$out->add($lang->def('_WHOIS_ONLINE').': <b>'.$user_stats['now_online'].'</b> ');
	$out->add('<div class="box_whois_online">'    .'<span>'.$lang->def('_NUM_FRIENDS_ONLINE').' : </span><div id="user_online_n"><a href="javascript:;"onclick="openUsersList()"><b>'.$user_stats['now_online_filtered'].'</b></a></div></div>');

}


?>
