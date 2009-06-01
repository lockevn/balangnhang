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



function wikiBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	require_once($GLOBALS['where_framework']."/lib/lib.wiki.php");

	$opt=loadBlockOption($block_id);
	$textof=loadTextof($block_id);

	$out->add(getBlockTitleField($form, $lang, $block_id));

	$cwm=new CoreWikiManager();
	$wiki_arr=$cwm->getWikiList();
	$data_arr=(isset($wiki_arr["data_arr"]) ? $wiki_arr["data_arr"] : array());
	$wiki_list=array();
	foreach ($data_arr as $key=>$val) {
		$wiki_list[$val["wiki_id"]]=$val["title"];
	}
	$sel_wiki=(isset($opt["wiki_id"]) ? $opt["wiki_id"] : "");

	$out->add($form->getDropdown($lang->def("_WIKI"), "wiki_id", "wiki_id", $wiki_list , $sel_wiki));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add(getBlindNavDescField($form, $lang, $opt));
 	$out->add(getGMonitoringField($form, $lang, $opt));

}



function wikiBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "wiki_id", (int)$_POST["wiki_id"]);
	saveParam($block_id, "css", (int)$_POST["css"]);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function wikiBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);

}


?>
