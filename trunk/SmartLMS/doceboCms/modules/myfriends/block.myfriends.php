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

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");


require_once($GLOBALS["where_cms"]."/modules/myfriends/functions.php");

function myfriends_showMain($idBlock, $title, $block_op) {

	$opt=loadBlockOption($idBlock);

	if ((isset($title)) && ($title != ''))
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);


	$GLOBALS["page"]->add("<div class=\"body_block\">\n", "content");

	if(!$GLOBALS['current_user']->isAnonymous()) {
		$op="myfriends";
		myfriendsDispatch($op);
	}
	else {
		$GLOBALS["page"]->add(def("_LOGIN_REQUIRED", "standard", "cms"), "content");
	}

	$GLOBALS["page"]->add("</div>\n", "content"); // body_block
}


?>
