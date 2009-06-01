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

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------



function print_feed($feed_arr, $opt, & $feed_reader) {

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$items=$feed_arr["items"];

	$limit=$opt["number"];
	$type=(isset($opt["type"]) ? $opt["type"] : "normal");
	$show_title=(isset($opt["show_title"]) ? (bool)$opt["show_title"] : FALSE);
	$show_desc=(isset($opt["show_desc"]) ? (bool)$opt["show_desc"] : FALSE);
	$show_read_all=(isset($opt["show_read_all"]) ? (bool)$opt["show_read_all"] : FALSE);

	switch($type) {
		case "normal": {
			$link_field="link";
		} break;
		case "podcast": { //experimental :D
			$link_field="guid";
		} break;
	}

// todo[?]: cambiare news_list con feed_list e poi associare nello stile, di default,
// news_list == feed_list poi cosi' se uno se lo vuol personalizzare puo' farlo
	$out->add('<div class="news_list">');

	$i=0;
	while (($i < $limit) && ($i < count($items))) {
		$feed_item=current($items);


		$out->add("<div class=\"news_box\">\n");

		if ($show_title) {
			$out->add("<div class=\"news_title\">\n");
			$out->add(open_ext_link($feed_item[$link_field]));
			$out->add($feed_reader->cleanEntry($feed_item["title"])."</a><br />");
			$out->add("</div>\n"); // news_title
		}

		if (($show_desc) && (isset($feed_item["description"])) && (!empty($feed_item["description"]))) {

			$description = $feed_reader->cleanEntry($feed_item["description"]);
			$description =strip_tags($description);

			$max_len =200;
			if (strlen($description) > $max_len) {
				$description =substr($description, 0 , $max_len);
				$cut_at =strpos($description, "\n\r", $max_len -30);
				if ($cut_at === FALSE) {
					$cut_at =strrpos($description, " ");
				}
				else {
					$cut_at =$cut_at-1;
				}
				$description =rtrim($description, "()[]\/-_{}.=:");
				$description =substr($description, 0 , $cut_at)."...";
			}

			$out->add($description);

			if (!$show_read_all) {
				$out->add("<br />");
			}
		}

		if ($show_read_all) {
			$out->add("\n");
			$out->add("<span class=\"feed_read_all\">[\n");
			$out->add(open_ext_link($feed_item[$link_field]));
			$out->add(def("_READ_MORE", "news")."</a> ]<br />");
			$out->add("</span>\n"); // news_title
		}

		$out->add("</div>\n"); // news_box
		next($items);
		$i++;
	}

	$out->add('</div>'); // news_list
}


?>
