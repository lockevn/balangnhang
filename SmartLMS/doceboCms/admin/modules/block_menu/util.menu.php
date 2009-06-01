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


function menuBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

	$out->add(getBlockTitleField($form, $lang, $block_id));
 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$chk1=""; $chk2="";

	$complete=($opt["type"] == "complete" ? TRUE : FALSE);
	$under=($opt["type"] == "under" ? TRUE : FALSE);
	$macroarea=($opt["type"] == "macroarea" ? TRUE : FALSE);
	$onlyparent=($opt["type"] == "onlyparent" ? TRUE : FALSE);
	$onlycurrent=($opt["type"] == "onlycurrent" ? TRUE : FALSE);
	$onlychild=($opt["type"] == "onlychild" ? TRUE : FALSE);


	$out->add($form->getOpenFieldset($lang->def("_MENU_TYPE")));
	$out->add($form->getRadio($lang->def("_MENU_EXPLODE"), "mt_complete", "menu_type", "complete", $complete));
	$out->add($form->getRadio($lang->def("_MENU_VOICE"), "mt_under", "menu_type", "under", $under));
	$out->add($form->getRadio($lang->def("_MENU_MACROAREA"), "mt_macroarea", "menu_type", "macroarea", $macroarea));
	$out->add($form->getRadio($lang->def("_MENU_ONLYPARENT"), "mt_onlyparent", "menu_type", "onlyparent", $onlyparent));
	$out->add($form->getRadio($lang->def("_MENU_ONLYCURRENT"), "mt_onlycurrent", "menu_type", "onlycurrent", $onlycurrent));
	$out->add($form->getRadio($lang->def("_MENU_ONLYCHILD"), "mt_onlychild", "menu_type", "onlychild", $onlychild));
	$out->add($form->getCloseFieldset());

	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

}



function menuBlockSave($block_id, $sub_id) {

	$valid=array("complete", "under", "macroarea", "onlyparent", "onlycurrent", "onlychild");
	if ((isset($_POST['menu_type'])) && (in_array($_POST['menu_type'], $valid))) {
		saveParam($block_id, "type", $_POST['menu_type']);
	}

	saveParam($block_id, "css", (int)$_POST["css"]);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);
	saveBlockTitle($block_id);

}


function menuBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "type", "under");
	saveParam($block_id, "css", 1);
	saveBlockTitle($block_id);

}


?>