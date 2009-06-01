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

function topic_list() {

	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_mantopic', 'cms');

	$sel_lang=getLanguage();

	$can_mod=checkPerm('mod', true);
	$can_del=checkPerm('del', true);

	$qtxt="";
	$qtxt.="SELECT * FROM ".$GLOBALS["prefix_cms"]."_topic WHERE language='$sel_lang';";
	$q=mysql_query($qtxt);
	if ($q) $tot=mysql_num_rows($q);

	$qtxt="";
	$qtxt.="SELECT * FROM ".$GLOBALS["prefix_cms"]."_topic WHERE language='$sel_lang' ";
	$qtxt.="ORDER BY label;";

	$table = new typeOne(0);
	$out->add($table->OpenTable(""));

	$head = array($lang->def("_TITLE"), $lang->def("_TOPIC_IMAGE"),
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />',
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
	$head_type = array('', 'img', 'img', 'img');


	$out->add($table->WriteHeader($head, $head_type));

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {

			$rowcnt=array($row["label"]);

			if ($row["image"] != "") {
				$rowcnt[]="<img src=\""._TOPIC_FPATH.$row["image"]."\" alt=\"".$row["label"]."\" title=\"".$row["label"]."\" />\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			if ($can_mod) {
				$rowcnt[]=
					"<a href=\"index.php?modname=mantopic&amp;op=modtopic&amp;id=".$row["topic_id"]."\">".
					"<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" title=\"".$lang->def("_MOD")."\" /></a>";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			if ((!is_used($row["topic_id"])) && ($can_del)) {
				$rowcnt[]=
					"<a href=\"index.php?modname=mantopic&amp;op=deltopic&amp;id=".$row["topic_id"]."&amp;conf_del=1\" title=\"".$lang->def("_DEL")." : ".$row["label"]."\">".
					"<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" title=\"".$lang->def("_DEL")."\" /></a>";
			}
			else {
				$rowcnt[]="&nbsp;";
			}


			$out->add($table->WriteRow($rowcnt));
		}
	}

	if (checkPerm('add', true)) {
		$add_lbl=$lang->def("_ADD");
		$out->add($table->WriteAddRow('<a href="index.php?modname=mantopic&amp;op=addtopic" title="'.$add_lbl.'">
										<img src="'.getPathImage().'standard/add.gif" alt="'.$add_lbl.'" /> '.$add_lbl.'</a>'));
	}

	if ($can_del) {
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=deltopic]');
	}

	$out->add($table->CloseTable());

	$out->add("<br />\n");
}



function info_lang_table($id) {
	include_once('core/class/class.typeone.php');

	$table = new typeOne(0);
	$table->OpenTable("");

	$head = array(_LANG,
		'<img src="'.getPathImage().'standard/mod.gif" alt="'._MOD.'" title="'._MOD.'" />');
	$head_type = array('', 'img');


	$table->WriteHeader($head, $head_type);

	$larr=$GLOBALS['globLangManager']->getAllLangCode();
	foreach ($larr as $key=>$val) {

		$img="<img src=\"".getPathImage()."standard/mod.gif\" alt=\""._MOD."\" title=\""._MOD.": $val\" />";
		$url="index.php?modname=mantopic&amp;op=modtopic&amp;lang=$val&amp;id=$id";
		$link="<a href=\"$url\" alt=\"\" />$img</a>";

		$line=array($val, $link);
		$table->WriteRow($line);
	}

	$table->CloseTable();
}



function multi_lang_field(& $form, $field_name, $field_lbl, $field_val=NULL) {

	if ($field_val == NULL) $field_val=array();

	$larr=$GLOBALS['globLangManager']->getAllLangCode();
	foreach ($larr as $key=>$val) {

		$field=$field_name."[".$val."]";
		$field_id=$field_name."_".$val."_";

		if (isset($field_val[$field_name][$val]))
			$value=$field_val[$field_name][$val];
		else
			$value=$field_lbl;

		$GLOBALS['page']->add($form->getTextfield($field_lbl." (".$val.")", $field_id, $field, 255, $value));
	}

}



function is_used($topic_id) {
	$res=0;

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_news_topic WHERE topic_id='$topic_id'";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0))
		$res=1;

	return $res;
}



?>