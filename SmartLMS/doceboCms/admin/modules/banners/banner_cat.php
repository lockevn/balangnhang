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

function banner_cat_list() {

	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_banners', 'cms');

	$can_add=checkPerm('add', true);
	$can_mod=checkPerm('mod', true);
	$can_del=checkPerm('del', true);

	$out->setWorkingZone('content');

	$out->add(getTitleArea($lang->def("_BANNER_CAT"), "banners"));

	$out->add("<div class=\"std_block\">\n");

	$sel_lang=getLanguage();

	$qtxt="";
	$qtxt.="SELECT * FROM ".$GLOBALS["prefix_cms"]."_banner_cat WHERE language='$sel_lang' ";
	$qtxt.="ORDER BY cat_name;";

	$table = new typeOne(0);
	$out->add($table->OpenTable(""));

	$head = array($lang->def("_TITLE"), $lang->def("_DESCRIPTION"),
		'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />',
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />');
	$head_type = array('', '', 'img', 'img');


	$out->add($table->WriteHeader($head, $head_type));

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_array($q)) {

			$rowcnt=array();
			$rowcnt[]=$row["cat_name"];
			$rowcnt[]=$row["cat_desc"];

			if ($can_mod) {
				$rowcnt[]=
					"<a href=\"index.php?modname=banners&amp;op=editbannercat&amp;id=".$row["cat_id"]."\">".
					"<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" title=\"".$lang->def("_MOD")."\" /></a>";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			if ((!is_used($row["cat_id"])) && ($can_del)) {
				$rowcnt[]=
					//"<a href=\"index.php?modname=banners&amp;op=delbannercat&amp;id=".$row["cat_id"]."\" ".
					"<a href=\"index.php?modname=banners&amp;op=delbannercat&amp;id=".$row["cat_id"]."&amp;conf_del=1\" ".
					" id=\"delbanner_".$row["cat_id"]."\" title=\"".$lang->def("_DEL")." : ".$row["cat_name"]."\">".
					"<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" title=\"".$lang->def("_DEL")."\" /></a>";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			$out->add($table->WriteRow($rowcnt));
		}
	}

	if ($can_add) {
		$add_lbl=$lang->def("_ADD");
		$out->add($table->WriteAddRow('<a href="index.php?modname=banners&amp;op=addbannercat" title="'.$add_lbl.'">
										<img src="'.getPathImage().'standard/add.gif" alt="'.$add_lbl.'" /> '.$add_lbl.'</a>'));
	}

	if ($can_del) {
		//add confirm pop ups
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delbannercat]');
	}

	$out->add($table->CloseTable());

	$out->add("<br />\n");

	$out->add("</div>\n");
}



function is_used($cat_id) {

	return false;

}





function add_edit_bannercat($id=0, $todo="add") {

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$topic_image="";

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_banners', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');

	$back_ui_url="index.php?modname=banners&amp;op=viewcat";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_BANNER_CAT");


	$cat_field_val=array();
	$field_list=getFieldList();
	$form_code="";

	switch($todo) {

		case "edit": {
			checkPerm('mod');

			$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_banner_cat WHERE cat_id='$id';";
			$q=mysql_query($qtxt);

			if (($q) && (mysql_num_rows($q) > 0)) {
				while($row=mysql_fetch_array($q)) {
					foreach ($field_list as $key=>$val) {
						$cat_field_val[$val][$row["language"]]=$row[$val];
					}
				}
			}


			$form_code.=$form->openForm("banner_form", "index.php?modname=banners&amp;op=updbannercat");
			$submit_lbl=$lang->def("_MOD");

			$title_arr[]=$lang->def("_EDIT_BANNER_CAT").": ".$cat_field_val["cat_name"][getLanguage()];

		} break;

		case "add": {
			checkPerm('add');

			$form_code.=$form->openForm("banner_form", "index.php?modname=banners&amp;op=insbannercat");
			$submit_lbl=$lang->def("_INSERT");

			$title_arr[]=$lang->def("_ADD_BANNER_CAT");
		} break;

		default: {
			die();
		} break;

	}

	$out->add(getTitleArea($title_arr, "banners"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form_code.$form->openElementSpace());

	multi_lang_field($form, "cat_name", $lang->def("_TITLE"), $cat_field_val);
	multi_lang_field($form, "cat_desc", $lang->def("_DESCRIPTION"), $cat_field_val);

	if ((int)$id > 0)
		$out->add($form->getHidden("id", "id", $id));

	$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $submit_lbl));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}


function ins_bannercat() {
	checkPerm('add');

	$cat_id=0;
	$field_list=getFieldList();
	$lang_list=$GLOBALS['globLangManager']->getAllLangCode();

	foreach ($lang_list as $lang_key=>$lang) {

		$qstr_arr=array();
		foreach ($field_list as $key=>$val) {
			$qstr_arr[]="'".$_POST[$val][$lang]."'";
		}


		// --- Extra fields:
		$db_field_list=$field_list;
		$db_field_list[]="language";
		$qstr_arr[]="'".$lang."'";
		// ---


		if ($cat_id == 0) { // Insert the first row

			$fields=implode(", ", $db_field_list);
			$qstr=implode(", ", $qstr_arr);

			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_banner_cat (".$fields.") VALUES (".$qstr.")";
			$q=mysql_query($qtxt);
			list($cat_id)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID() FROM ".$GLOBALS["prefix_cms"]."_banner_cat"));
			$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_banner_cat SET cat_id='$cat_id' WHERE id='$cat_id'";
			$q=mysql_query($qtxt);

		}
		else { // Insert the remaining rows

			// --- Extra fields:
			$db_field_list[]="cat_id";
			$qstr_arr[]="'".$cat_id."'";
			// ---

			$fields=implode(", ", $db_field_list);
			$qstr=implode(", ", $qstr_arr);

			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_banner_cat (".$fields.") VALUES (".$qstr.")";
			$q=mysql_query($qtxt);
		}

	}

	jumpTo("index.php?modname=banners&op=viewcat");
}



function upd_bannercat($id) {
	checkPerm('mod');

	$field_list=getFieldList();
	$lang_list=$GLOBALS['globLangManager']->getAllLangCode();

	foreach ($lang_list as $lang_key=>$lang) {

		$qstr_arr=array();
		foreach ($field_list as $key=>$val) {
			$qstr_arr[]=$val."='".$_POST[$val][$lang]."'";
		}
		$qstr=implode(", ", $qstr_arr);

		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_banner_cat WHERE cat_id='$id' AND language='$lang'";
		$q=mysql_query($qtxt);

		if (($id > 0) && ($q) && (mysql_num_rows($q) == 0)) {
			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_banner_cat (cat_id, language) VALUES ('$id', '$lang')";
			$q=mysql_query($qtxt);
		}

		$qtxt ="UPDATE ".$GLOBALS["prefix_cms"]."_banner_cat SET ".$qstr." ";
		$qtxt.="WHERE cat_id='".(int)$id."' AND language='$lang'";
		$q=mysql_query($qtxt);

	}

	jumpTo("index.php?modname=banners&op=viewcat");
}





function del_bannercat($id) {
	//access control
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_banners', 'cms');

	if (isset($_POST["canc_del"])) {
		jumpTo("index.php?modname=banners&op=viewcat");
	}
	else if (get_req("conf_del", DOTY_INT, false)) {

		if (is_used($id)) die("This item can't be deleted");

		$qtxt="DELETE FROM ".$GLOBALS["prefix_cms"]."_banner_cat WHERE cat_id='".(int)$id."'";
		$q=mysql_query($qtxt);

		jumpTo("index.php?modname=banners&op=viewcat");
	}
	else {

		$sel_lang=getLanguage();

		$qtxt="SELECT cat_name FROM ".$GLOBALS["prefix_cms"]."_banner_cat WHERE cat_id='$id' AND language='$sel_lang'";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$cat_name=$row["cat_name"];
		}


		$back_ui_url="index.php?modname=banners&amp;op=viewcat";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_BANNER_CAT");
		$title_arr[]=$lang->def("_DEL").": ".$cat_name;
		$out->add(getTitleArea($title_arr, "banners"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$out->add($form->openForm("banner_form", "index.php?modname=banners&amp;op=delbannercat&amp;id=$id"));

		$out->add($form->getHidden("id", "id", $id));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$cat_name.'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}
}




function multi_lang_field(& $form, $field_name, $field_lbl, $field_val=NULL) {

	if ($field_val == NULL) $field_val=array();

	$larr=$GLOBALS['globLangManager']->getAllLangCode();
	foreach ($larr as $key=>$val) {

		$field=$field_name."[".$val."]";
		$field_id=$field_name."_".$val."_";

		if (isset($field_val[$field_name][$val])) {
			$value=$field_val[$field_name][$val];
		}
		else {
			$value="";
		}

		$GLOBALS['page']->add($form->getTextfield($field_lbl." (".$val.")", $field_id, $field, 255, $value));

	}

}



function getFieldList() {

	$res=array();

	$res[]="cat_name";
	$res[]="cat_desc";

	return $res;
}


?>