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

require_once($GLOBALS["where_cms"]."/modules/calendar/functions.php");

function calendar_showMain($idBlock, $title, $block_op) {

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);

	if ((isset($title)) && ($title != ''))
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");

	$opt =loadBlockOption($idBlock);
	$cal_id =(isset($opt["calendar_id"]) ? $opt["calendar_id"] : 0);
	calendarDispatch("calendar", $cal_id);
}


?>
