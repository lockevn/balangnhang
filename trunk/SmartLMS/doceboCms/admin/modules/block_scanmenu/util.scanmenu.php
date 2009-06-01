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


function scanmenuBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

 	$out->add(block_css_list($form, $lang, $opt["css"]));
 	$description=($opt["type"] == "description" ? TRUE : FALSE);
 	$explode=($opt["type"] == "explode" ? TRUE : FALSE);
 	$simple=($opt["type"] == "simple" ? TRUE : FALSE);
 	$out->add($form->getOpenFieldset($lang->def("_SCANMENU_TYPE")));
 	$out->add($form->getRadio($lang->def("_SCANMENU_EXPLODE"), "mt_explode", "menu_type", "explode", $explode));
 	$out->add($form->getRadio($lang->def("_SCANMENU_DESCRIPTION"), "mt_description", "menu_type", "description", $description));
 	$out->add($form->getRadio($lang->def("_SCANMENU_SIMPLE"), "mt_simple", "menu_type", "simple", $simple));
 	$out->add($form->getCloseFieldset());
 	
 	$from_current=($opt["base"] == "current" ? TRUE : FALSE);
 	$from_macroarea=($opt["base"] == "macroarea" ? TRUE : FALSE);
 	$out->add($form->getOpenFieldset($lang->def("_SCANMENU_BASE")));
 	$out->add($form->getRadio($lang->def("_SCANMENU_CURRENT_PAGE"), "mt_current", "menu_base", "current", $from_current));
 	$out->add($form->getRadio($lang->def("_SCANMENU_MACROAREA"), "mt_macroarea", "menu_base", "macroarea", $from_macroarea));
 	$out->add($form->getCloseFieldset());
 	
	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

}



function scanmenuBlockSave($block_id, $sub_id) {


	$valid=array("explode", "description", "simple",'current','macroarea');

	if ((isset($_POST['menu_type'])) && (in_array($_POST['menu_type'], $valid))) {
		saveParam($block_id, "type", $_POST['menu_type']);
	}
	
	$valid=array('current','macroarea');

	if ((isset($_POST['menu_base'])) && (in_array($_POST['menu_base'], $valid))) {
		saveParam($block_id, "base", $_POST['menu_base']);
	}
	
	saveParam($block_id, "css", (int)$_POST["css"]);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function scanmenuBlockAdd($block_id, $sub_id) {

//	saveParam($block_id, "type", "under");
	saveParam($block_id, "css", 2);

}


?>