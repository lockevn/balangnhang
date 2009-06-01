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

require_once($GLOBALS["where_cms"]."/admin/modules/block_news/util.news_sel.php");

function news_sel_smallBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

	include_once($GLOBALS["where_cms"]."/admin/modules/news/news_class.php");


	// ------- mostro elementi blocco --------------------------------------\

	$out->add(show_items_table($block_id, $sub_id));

	// --------------------------------------------------------------------/


	$out->add(getBlockTitleField($form, $lang, $block_id));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));


	$out->add($form->getTextfield($lang->def("_NEWS_NUMBER"), "number", "number", 3, $opt["number"]));

	if ((isset($opt["show_newsdate"])) && ($opt["show_newsdate"])) $chk=true; else $chk=false;
	$out->add($form->getCheckBox($lang->def("_SHOW_NEWSDATE"), "show_newsdate", "show_newsdate", "1", $chk));

	
	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

}



function news_sel_smallBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveParam($block_id, "show_newsdate", (int)$_POST["show_newsdate"]);
	saveParam($block_id, "number", (int)$_POST["number"]);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function news_sel_smallBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);
	saveParam($block_id, "show_newsdate", 1);
	saveParam($block_id, "number", 10);

}




function news_sel_smallBlockOption(& $out, & $lang, & $form, $block_id, $sub_id, $blk_op) {

	news_selBlockOption($out, $lang, $form, $block_id, $sub_id, $blk_op);

}


?>