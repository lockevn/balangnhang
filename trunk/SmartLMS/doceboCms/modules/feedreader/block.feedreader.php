
<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS['where_framework']."/lib/lib.rss.php");
require_once($GLOBALS['where_cms']."/modules/feedreader/functions.php");

function feedreader_showMain($idBlock, $title, $block_op) {

	$opt=loadBlockOption($idBlock);

	if ((isset($title)) && ($title != ''))
		$GLOBALS["page"]->add('<div class="titleBlock">'.$title.'</div>', "content");

	getPageId();
	setPageId($GLOBALS["area_id"], $idBlock);


	$GLOBALS["page"]->add("<div class=\"body_block\">\n", "content");

	$feed_id=(isset($opt["feed_id"]) ? $opt["feed_id"] : 0);

	$feed_reader=new FeedReader();
	$feed_info=$feed_reader->frManager->getFeedInfo($feed_id);
	$last_update_ts=$GLOBALS["regset"]->databaseToTimestamp($feed_info["last_update"]);

	$reload=FALSE;
	if (time()-$opt["refresh_time"]*60 > $last_update_ts)
		$reload=TRUE;

	$feed_arr=$feed_reader->readFeed($feed_id, $reload, TRUE);

	print_feed($feed_arr, $opt, $feed_reader);

	$GLOBALS["page"]->add("</div>\n", "content"); // body_block
}


?>
