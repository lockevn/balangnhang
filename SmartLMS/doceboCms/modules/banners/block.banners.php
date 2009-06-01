<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.com                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS["where_cms"]."/modules/banners/functions.php");

function banners_showMain($idBlock, $title, $block_op) {

	$opt=loadBlockOption($idBlock);

	$GLOBALS["page"]->add("<div style=\"text-align: center;\">\n", "content");
	$GLOBALS["page"]->add(show_banner($opt["cat_id"]), "content");
	$GLOBALS["page"]->add("</div>\n", "content");

}


?>