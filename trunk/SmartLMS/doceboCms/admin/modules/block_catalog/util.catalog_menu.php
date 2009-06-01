<?php


function catalog_menuBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	$opt=loadBlockOption($block_id);

	$out->add(getBlockTitleField($form, $lang, $block_id));

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add(getBlindNavDescField($form, $lang, $opt));
 	$out->add(getGMonitoringField($form, $lang, $opt));

}



function catalog_menuBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function catalog_menuBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);

}


?>