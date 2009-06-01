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

require_once($GLOBALS["where_cms"]."/admin/modules/block_profile_search/functions.php");


function profile_search_teacherBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

	$out->add(getBlockTitleField($form, $lang, $block_id));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$stored_filter=loadBlockFilter($block_id, "profile_search");
	$sel_group=(isset($stored_filter["group"]) ? $stored_filter["group"] : array());
	$sel_custom_fields =(isset($opt["custom_field"]) ? $opt["custom_field"] : FALSE);
	$avatar_size =(isset($opt["avatar_size"]) ? $opt["avatar_size"] : "small");

	$out->add(getCustomFieldsList("block_form", $sel_custom_fields));

	$out->add(getGroupsSelector("block_form", "x", "", "filter_group", $sel_group, FALSE));

	$size_arr =getAvatarSizeDropdownArr($lang);
	$out->add($form->getDropdown($lang->def("_AVATAR_SIZE"), "avatar_size", "avatar_size", $size_arr, $avatar_size));


	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add(getBlindNavDescField($form, $lang, $opt));
 	$out->add(getGMonitoringField($form, $lang, $opt));
}



function profile_search_teacherBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);

	$filter_group=(isset($_POST["filter_group"]) ? array_keys($_POST["filter_group"]) : array());

	if (in_array("all", $filter_group)) {
		$filter_group=array();
	}

	saveBlockFilter($block_id, "profile_search", "group", $filter_group);

	if ((isset($_POST["custom_field"])) && (is_array($_POST["custom_field"]))) {
		saveParam($block_id, "custom_field", implode(",", $_POST["custom_field"]));
	}
	else {
		saveParam($block_id, "custom_field", "");
	}

	saveParam($block_id, "avatar_size", $_POST["avatar_size"]);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);
}


function profile_search_teacherBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);

}


?>
