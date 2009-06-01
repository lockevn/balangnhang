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



function feedreaderBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	require_once($GLOBALS['where_framework']."/lib/lib.rss.php");

	$feedmanager_lang =& DoceboLanguage::createInstance('feedmanager', "framework");

	$opt=loadBlockOption($block_id);
	$textof=loadTextof($block_id);

	$out->add(getBlockTitleField($form, $lang, $block_id));

	$fr_manager=new FeedReaderManager();
	$feed_list=$fr_manager->getFeedListArray("cms");

	$sel_feed=(isset($opt["feed_id"]) ? $opt["feed_id"] : "");
	$refresh_time=(isset($opt["refresh_time"]) ? $opt["refresh_time"] : 240);
	$show_title=(isset($opt["show_title"]) ? (bool)$opt["show_title"] : FALSE);
	$show_desc=(isset($opt["show_desc"]) ? (bool)$opt["show_desc"] : FALSE);
	$show_read_all=(isset($opt["show_read_all"]) ? (bool)$opt["show_read_all"] : FALSE);

	$out->add($form->getDropdown($lang->def("_FEED"), "feed_id", "feed_id", $feed_list , $sel_feed));

	$frAdmin=new FeedReaderAdmin();
	$refresh_time_arr=$frAdmin->getRefreshTimeArr($feedmanager_lang);

	$out->add($form->getDropdown($lang->def("_REFRESH_TIME"), "refresh_time", "refresh_time", $refresh_time_arr , $refresh_time));

	$out->add($form->getCheckbox($lang->def("_SHOW_TITLE"), "show_title", "show_title", 1, $show_title));
	$out->add($form->getCheckbox($lang->def("_SHOW_DESC"), "show_desc", "show_desc", 1, $show_desc));
	$out->add($form->getCheckbox($lang->def("_SHOW_READ_ALL"), "show_read_all", "show_read_all", 1, $show_read_all));

	$out->add($form->getTextfield($lang->def("_FEED_NUMBER"), "number", "number", 3, $opt["number"]));


 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add(getBlindNavDescField($form, $lang, $opt));
 	$out->add(getGMonitoringField($form, $lang, $opt));

}



function feedreaderBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "feed_id", (int)$_POST["feed_id"]);
	saveParam($block_id, "refresh_time", (int)$_POST["refresh_time"]);
	saveParam($block_id, "number", (int)$_POST["number"]);
	saveParam($block_id, "show_title", (int)$_POST["show_title"]);
	saveParam($block_id, "show_desc", (int)$_POST["show_desc"]);
	saveParam($block_id, "show_read_all", (int)$_POST["show_read_all"]);
	saveParam($block_id, "css", (int)$_POST["css"]);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function feedreaderBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);
	saveParam($block_id, "number", 10);
	saveParam($block_id, "show_title", 1);

}


function feedreaderBlockDel($block_id, $sub_id) {
}


?>
