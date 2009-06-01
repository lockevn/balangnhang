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



function pollBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	require_once($GLOBALS['where_cms']."/admin/modules/poll/functions.php");
	
	$opt=loadBlockOption($block_id);
	$textof=loadTextof($block_id);

	$out->add(getBlockTitleField($form, $lang, $block_id));

	$poll=new PollManager();
	$poll_arr=$poll->getAllPoll();
	foreach ($poll_arr as $key=>$val) {
		$poll_list[$val["poll_id"]]=$val["question"];
	}
	$sel_poll=(isset($opt["poll_id"]) ? $opt["poll_id"] : "");
	
	$out->add($form->getDropdown($lang->def("_POLL"), "poll_id", "poll_id", $poll_list , $sel_poll));	
	
 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add(getBlindNavDescField($form, $lang, $opt));
 	$out->add(getGMonitoringField($form, $lang, $opt));

}



function pollBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "poll_id", (int)$_POST["poll_id"]);
	saveParam($block_id, "css", (int)$_POST["css"]);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function pollBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);

}


function pollBlockDel($block_id, $sub_id) {
}


?>
