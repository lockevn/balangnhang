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

function simpleprjBlockEdit(& $out, & $lang, & $form, $block_id, $sub_id) {

	require_once($GLOBALS['where_cms']."/lib/lib.simpleprj.php");

	$opt=loadBlockOption($block_id);
	$textof=loadTextof($block_id);

	$out->add(getBlockTitleField($form, $lang, $block_id));

	$db_prj =loadBlockSimplePrj($block_id);

	$res ="";
	$res.=$form->getOpenFieldset($lang->def("_BLOCK_SIMPLEPRJ_LIST"));

	$spm =new SimplePrjManager();
	$data_info =$spm->getSimplePrjList();

	$data_arr =$data_info["data_arr"];
	$db_tot =$data_info["data_tot"];

	$tot=count($data_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id =$data_arr[$i]["project_id"];
		$title =$data_arr[$i]["title"];

		$checked =(in_array($id, $db_prj) ? TRUE : FALSE);
		$field_id ="sel_simpleprj_".$id;
		$field_name ="sel_simpleprj[".$id."]";
		$res.=$form->getCheckbox($title, $field_id, $field_name, $id, $checked);
	}

	$res.=$form->getCloseFieldset();
	$out->add($res);

 	$out->add(block_css_list($form, $lang, $opt["css"]));

	$pubdate=(isset($opt["pubdate"]) ? $opt["pubdate"] : "");
	$expdate=(isset($opt["expdate"]) ? $opt["expdate"] : "");
 	$out->add(show_pubexp_table($form, $lang, $pubdate, $expdate));

	$out->add(getBlindNavDescField($form, $lang, $opt));
 	$out->add(getGMonitoringField($form, $lang, $opt));

}



function simpleprjBlockSave($block_id, $sub_id) {

	saveBlockTitle($block_id);

	saveParam($block_id, "css", (int)$_POST["css"]);

	saveBlockSimplePrj($block_id);

	save_pubexp_info($block_id);

	saveBlindNavDesc($block_id);
	saveGMonitoring($block_id);

}


function simpleprjBlockAdd($block_id, $sub_id) {

	saveParam($block_id, "css", 1);

}


function simpleprjBlockDel($block_id, $sub_id) {
	$qtxt="DELETE FROM ".$GLOBALS['prefix_cms']."_area_block_simpleprj WHERE block_id='".(int)$block_id."'";
	$q=mysql_query($qtxt);
}


function loadBlockSimplePrj($block_id) {

	$qtxt="SELECT * FROM ".$GLOBALS['prefix_cms']."_area_block_simpleprj WHERE block_id='".(int)$block_id."'";
	$q=mysql_query($qtxt);

	$db_prj=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {
			$db_prj[]=$row["project_id"];
		}
	}

	return $db_prj;
}


function saveBlockSimplePrj($block_id) {

	$sel_simpleprj =$_POST["sel_simpleprj"];


	$db_prj =loadBlockSimplePrj($block_id);

	if((!is_array($sel_simpleprj)) || (count($sel_simpleprj) == 0)) {
		$qtxt="DELETE FROM ".$GLOBALS['prefix_cms']."_area_block_simpleprj WHERE block_id='".(int)$block_id."'";
		mysql_query($qtxt);
		return;
	}
	foreach ($sel_simpleprj as $key=>$val) {

		if (!in_array($val, $db_prj)) {
			$qtxt="INSERT INTO ".$GLOBALS['prefix_cms']."_area_block_simpleprj (block_id, project_id) VALUES ('$block_id', '$val');";
			mysql_query($qtxt);
		}
	}

	foreach ($db_prj as $key=>$val) {
		if (!in_array($val, $sel_simpleprj)) {
			$qtxt="DELETE FROM ".$GLOBALS['prefix_cms']."_area_block_simpleprj WHERE block_id='".(int)$block_id."' AND project_id='$val';";
			mysql_query($qtxt);
		}
	}
}


?>
