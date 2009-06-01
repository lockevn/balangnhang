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

function bannersBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);
	$textof=loadTextof($block_id);

	$out->add(getBlockTitleField($form, $lang, $block_id));

	$out->add(banner_cat_list($form, $lang, $opt["cat_id"]));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add(getBlindNavDescField($form, $lang, $opt));	
}



function bannersBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveParam($block_id, "cat_id", (int)$_POST["cat_id"]);

	save_pubexp_info($block_id);
	
	saveBlindNavDesc($block_id);

}


function bannersBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);
	saveParam($block_id, "cat_id", 0);

}


function banner_cat_list(& $form, & $lang, $cur) {

	$res="";

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_banner_cat WHERE language='".getLanguage()."' ORDER BY cat_name";
	$q=mysql_query($qtxt);

 	$cat_arr=array();

 	if (($q) && (mysql_num_rows($q) > 0)) {
 		while($row=mysql_fetch_array($q)) {
			$cat_arr[$row["cat_id"]]=$row["cat_name"];
			$i++;
		}
	}

	$res.=$form->getDropdown($lang->def("_BANNER_CAT").":", "cat_id", "cat_id", $cat_arr, $cur);

	return $res;
}

?>