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



function feedbackBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);


	$out->add(getBlockTitleField($form, $lang, $block_id));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));


	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_form ORDER BY title;";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {
			$ddval[$row["idForm"]]=$row["title"];
		}
	}


	if (isset($opt["form_id"]))
		$sel=$opt["form_id"];
	else
		$sel=0;

	$out->add($form->getDropdown( $lang->def("_FORM_SELECT").":", "form_id", "form_id", $ddval , $sel));

	
	$out->add(getBlindNavDescField($form, $lang, $opt));
	$out->add(getGMonitoringField($form, $lang, $opt));

}



function feedbackBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);
	saveParam($block_id, "form_id", (int)$_POST["form_id"]);


	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function feedbackBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);

}

?>