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



function faqBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	require_once($GLOBALS['where_framework']."/lib/lib.faq.php");

	$opt=loadBlockOption($block_id);
	$textof=loadTextof($block_id);

	$out->add(getBlockTitleField($form, $lang, $block_id));

	$cfm=new CoreFaqManager();
	$cat_arr=$cfm->getCategoryList();
	$data_arr=(isset($cat_arr["data_arr"]) ? $cat_arr["data_arr"] : array());
	$cat_list=array();
	foreach ($data_arr as $key=>$val) {
		$cat_list[$val["category_id"]]=$val["title"];
	}
	$sel_cat=(isset($opt["category_id"]) ? $opt["category_id"] : "");

	$out->add($form->getDropdown($lang->def("_FAQ_CATEGORY"), "category_id", "category_id", $cat_list , $sel_cat));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add(getBlindNavDescField($form, $lang, $opt));
 	$out->add(getGMonitoringField($form, $lang, $opt));

}



function faqBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "category_id", (int)$_POST["category_id"]);
	saveParam($block_id, "css", (int)$_POST["css"]);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function faqBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);

}


?>
