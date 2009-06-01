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



function chatBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);


	$out->add(getBlockTitleField($form, $lang, $block_id));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

 	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));


	$chat_type=array();
	$chat_type["htmlframechat"]=$lang->def("_CHAT_HTMLFRAMECHAT");
	$chat_type["htmlwachat"]=$lang->def("_CHAT_HTMLWACHAT");

	if (isset($opt["chat_type"]))
		$sel=$opt["chat_type"];
	else
		$sel=0;

	$out->add($form->getDropdown( $lang->def("_CHAT_TYPE").":", "chat_type", "chat_type", $chat_type , $sel));

	require_once($GLOBALS["where_scs"]."/lib/lib.room.php");
	$sel =(isset($opt["default_room"]) ? (int)$opt["default_room"] : FALSE);
	$room_list =getRoomList(FALSE, array("course"));
	$out->add($form->getDropdown( $lang->def("_DEFAULT_ROOM").":", "default_room", "default_room", $room_list , $sel));

	// $chk =((isset($opt["use_room"])) && ($opt["use_room"] == 0) ? FALSE : TRUE);
	// $out->add($form->getCheckBox($lang->def("_USE_ROOMS"), "use_room", "use_room", "1", $chk));

	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

}



function chatBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveParam($block_id, "chat_type", $_POST["chat_type"]);
	// saveParam($block_id, "use_room", (int)$_POST["use_room"]);
	saveParam($block_id, "default_room", (int)$_POST["default_room"]);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function chatBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);
	saveParam($block_id, "chat_type", "htmlframechat");

}

?>