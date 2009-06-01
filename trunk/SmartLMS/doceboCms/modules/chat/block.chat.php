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


function chat_showMain($idBlock, $title, $block_op) {

	$opt=loadBlockOption($idBlock);

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('chat', 'cms');


	if (!empty($title))
		$out->add("<div class=\"titleBlock\">$title</div>", "content");

	$out->add("<div class=\"body_block\">\n");

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	if ($GLOBALS["where_scs"] === FALSE) {

		$out->add($lang->def("_SCS_REQUIRED"));

	}
	else {

		require_once($GLOBALS["where_scs"]."/lib/lib.chat.php");
		$chatman=new ChatManager();

		/* the getOpenChatCommand should use backurl like here:
		$url=urlencode($_SERVER["REQUEST_URI"]);
		$link =$GLOBALS["where_scs_relative"]."/modules/".$opt["chat_type"]."/index.php?sn=".$GLOBALS["platform"];
		$link.="&amp;backurl=".htmlentities(urlencode($url));
		*/

		switch($opt["chat_type"]) {
			case "htmlframechat": {
				$chat_type="default";
			} break;
			case "htmlwachat": {
				$chat_type="accessible";
			} break;
		}

		$default_room =(isset($opt["default_room"]) ? (int)$opt["default_room"] : 0);
		//$use_room =((isset($opt["use_room"])) && ($opt["use_room"] == 0) ? FALSE : TRUE);
		$use_room =FALSE;

		$chatman->setRoomTypeFilter(array("'public'"));
		$basepath=$GLOBALS["cms"]["url"];
		$out->add($chatman->getOpenChatCommand($lang->def("_OPEN_CHAT"), $lang->def("_OPEN_CHAT"), $GLOBALS["platform"], $default_room, $basepath, $chat_type, $use_room));

		//$out->add(open_ext_link($link));
		//$out->add($lang->def("_OPEN_CHAT")."</a>");

	}

	$out->add("</div>\n"); // body_block

}


?>