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

function loginBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

 	// selettore gruppi


 	$out->add(block_css_list($form, $lang, $opt["css"]));
	$out->add(getBlindNavDescField($form, $lang, $opt));	
 	$out->add(getGMonitoringField($form, $lang, $opt));


}



function loginBlockSave($block_id, $sub_id) {

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveBlindNavDesc($block_id);	
	saveGMonitoring($block_id);

}


function loginBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);

}

?>