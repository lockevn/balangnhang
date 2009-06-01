<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Emanuele Sandri, Fabio Pirovano, Giovanni Derks */
/*                      http://www.docebocms.com                         */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS["where_cms"]."/modules/login/functions.php");

function subscription_showMain($idBlock, $title, $block_op) {

	$opt=loadBlockOption($idBlock);

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	if (!empty($title))
		$out->add("<div class=\"titleBlock\">$title</div>", "content");

	$out->add("<div class=\"body_block\">\n");
	$out->add("<div class=\"profile_form_box\">\n");

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	load_register();

	$out->add("</div>\n"); // profile_form_box
	$out->add("</div>\n"); // body_block

}


?>