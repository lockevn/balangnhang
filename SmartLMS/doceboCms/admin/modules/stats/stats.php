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

if(($GLOBALS['current_user']->isAnonymous()) || (!checkPerm('view', true))) die("You can't access!");

$lang=& DoceboLanguage::createInstance('admin_stats', 'cms');
$GLOBALS["page"]->add(getTitleArea($lang->def("_STATS"), "stats"));


$GLOBALS["page"]->add("<div class=\"std_block\">\n", "content");
define("_BBCLONE_DIR", $GLOBALS["where_cms"]."/addons/bbclone/");


$op=importVar("op");

switch($op) {
	case "stats" : {
		require_once(_BBCLONE_DIR."cms_show_global.php");
	};break;
	case "statsdetails" : {
		require_once(_BBCLONE_DIR."cms_show_detailed.php");
	};break;
	case "statstemporal" : {
		require_once(_BBCLONE_DIR."cms_show_time.php");
	};break;
	case "showviews" : {
		require_once(_BBCLONE_DIR."cms_show_views.php");
	};break;

}

$GLOBALS["page"]->add("</div>\n", "content");

require_once(_BBCLONE_DIR."cms_stats_head.php");

?>