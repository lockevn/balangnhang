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

if(($GLOBALS['current_user']->isAnonymous()) || (!checkPerm('view', true))) die("You can't access!");


require_once($GLOBALS['where_cms']."/admin/modules/topic/functions.php");
define("_TOPIC_FPATH_INTERNAL", "/doceboCms/topic/");
define("_TOPIC_FPATH", $GLOBALS['where_files_relative']._TOPIC_FPATH_INTERNAL);


function man_topic() {
	//access control

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_mantopic', 'cms');

	$out->setWorkingZone('content');

	$out->add(getTitleArea($lang->def("_MANTOPIC"), "topic"));

	$out->add("<div class=\"std_block\">\n");

	topic_list();

	$out->add("</div>\n");
}

function ins_topic() {

	checkPerm('add');
	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");
	include_once($GLOBALS['where_framework']."/lib/lib.multimedia.php");

	$topic_id=0;

	$fname=$_FILES["file"]["name"]; // File Upload
	if ($fname != "") {

		$real_fname=time().rand(10,99)."_".$fname;
		$tmp_fname=$_FILES["file"]["tmp_name"];

		sl_open_fileoperations();
		$f1=sl_upload($tmp_fname, _TOPIC_FPATH_INTERNAL.$real_fname);
		sl_close_fileoperations();

		if ($f1) {
			$topic_image=$real_fname;
			createPreview(_TOPIC_FPATH, _TOPIC_FPATH, $real_fname, 80, 80, false);
		}
		else
			$topic_image="";

	}

	foreach ($_POST["label"] as $key=>$val) {

		if ($topic_id == 0) { // Insert the first row

			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_topic (language, label, image) VALUES ('$key', '$val', '".addslashes($topic_image)."')";
			$q=mysql_query($qtxt);
			list($topic_id)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID() FROM ".$GLOBALS["prefix_cms"]."_topic"));
			$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_topic SET topic_id='$topic_id' WHERE id='$topic_id'";
			$q=mysql_query($qtxt);

		}
		else { // Insert the remaining rows
			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_topic (topic_id, language, label, image) VALUES ('$topic_id', '$key', '$val', '".addslashes($topic_image)."')";
			$q=mysql_query($qtxt);
		}

	}


	jumpTo("index.php?modname=mantopic&op=mantopic");
}


function add_edit_topic($id, $todo="add") {

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$topic_image="";

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_mantopic', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');


	$back_ui_url="index.php?modname=mantopic&amp;op=mantopic";

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_MANTOPIC");
	switch($todo) {
		case "edit": {
			$title_arr[]=$lang->def("_EDIT_TOPIC");
		} break;
		case "add": {
			$title_arr[]=$lang->def("_ADD_TOPIC");
		} break;
	}
	$out->add(getTitleArea($title_arr, "topic"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));


	switch($todo) {

		case "edit": {
			checkPerm('mod');

			$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_topic WHERE topic_id='$id';";
			$q=mysql_query($qtxt);

			if (($q) && (mysql_num_rows($q) > 0)) {
				while($row=mysql_fetch_array($q)) {
					$label_val["label"][$row["language"]]=$row["label"];
					if ($topic_image == "")
						$topic_image=$row["image"];
				}
			}

			$out->add($form->openForm("topic_form", "index.php?modname=mantopic&amp;op=updatetopic", "", "", "multipart/form-data"));
			$submit_lbl=$lang->def("_MOD");

		} break;

		case "add": {
			checkPerm('add');

			$out->add($form->openForm("topic_form", "index.php?modname=mantopic&amp;op=insnew", "", "", "multipart/form-data"));
			$submit_lbl=$lang->def('_ADD');
			$label_val=false;

		} break;

		default: {
			die();
		} break;

	}
	$out->add($form->openElementSpace());

	multi_lang_field($form, "label", $lang->def("_TITLE"), $label_val);

	if ($topic_image != "") {
		$sel_img=" [<i>".$topic_image."</i>]";
		$imgtag="<img src=\""._TOPIC_FPATH.$topic_image."\" alt=\"".$topic_image."\" title=\"".$topic_image."\" />\n";
	}
	else {
		$sel_img="";
		$imgtag="";
	}
	$out->add($imgtag);
	$out->add($form->getFilefield($lang->def("_TOPIC_IMAGE"), "file", "file", $topic_image));

	if ((int)$id > 0)
		$out->add($form->getHidden("id", "id", $id));
	if ($topic_image != "")
		$out->add($form->getHidden("old_file", "old_file", $topic_image));
	$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $submit_lbl));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}

function update_topic() {
	//access control

	checkPerm('mod');
	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");
	include_once($GLOBALS['where_framework']."/lib/lib.multimedia.php");


	$old_file=$_POST["old_file"];

	$fname=$_FILES["file"]["name"];  // File Upload
	if ($fname != "") {

		if ($old_file != "") {
			sl_unlink(_TOPIC_FPATH_INTERNAL.$old_file);
		}

		$real_fname=time().rand(10,99)."_".$fname;
		$tmp_fname=$_FILES["file"]["tmp_name"];

		sl_open_fileoperations();
		$f1=sl_upload($tmp_fname, _TOPIC_FPATH_INTERNAL.$real_fname);
		sl_close_fileoperations();

		if ($f1) {
			$set_topic_image=", image='$real_fname'";
			createPreview(_TOPIC_FPATH, _TOPIC_FPATH, $real_fname, 80, 80, false);
		}
		else
			$set_topic_image="";

	}

	$id=(int)$_POST["id"];

	foreach ($_POST["label"] as $key=>$val) {

		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_topic WHERE topic_id='$id' AND language='$key'";
		$q=mysql_query($qtxt);

		if (($id > 0) && ($q) && (mysql_num_rows($q) == 0)) {
			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_topic (topic_id, language) VALUES ('$id', '$key')";
			$q=mysql_query($qtxt);
		}

		$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_topic SET label='$val'".$set_topic_image." WHERE topic_id='$id' AND language='$key';";
		$q=mysql_query($qtxt);

	}

	header("location: index.php?modname=mantopic&op=mantopic");
}



function del_topic($id) {
	//access control
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");
	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_mantopic', 'cms');

	if (isset($_POST["canc_del"])) {
		header("location: index.php?modname=mantopic&op=mantopic");
	}
	else if ( get_req("conf_del", DOTY_INT, false) ) {
		$id= get_req('id', DOTY_INT, false);

		if (is_used($id)) die("This item can't be deleted");

		//fix_order();


		$qtxt="SELECT image FROM ".$GLOBALS["prefix_cms"]."_topic WHERE topic_id='$id'";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$topic_label=$row["label"];
			$topic_image=$row["image"];

			sl_unlink(_TOPIC_FPATH_INTERNAL.$topic_image);
		}

		$qtxt="DELETE FROM ".$GLOBALS["prefix_cms"]."_topic WHERE topic_id='$id';";
		$q=mysql_query($qtxt);

		header("location: index.php?modname=mantopic&op=mantopic");
	}
	else {
		$sel_lang=getLanguage();

		$qtxt="SELECT label FROM ".$GLOBALS["prefix_cms"]."_topic WHERE topic_id='$id' AND language='$sel_lang'";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$topic_label=$row["label"];
		}


		$back_ui_url="index.php?modname=mantopic&amp;op=mantopic";

		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_MANTOPIC");
		$title_arr[]=$lang->def("_DELETE_TOPIC").": ".$topic_label;
		$out->add(getTitleArea($title_arr, "topic"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$out->add($form->openForm("topic_form", "index.php?modname=mantopic&amp;op=deltopic&amp;id=$id"));

		$out->add($form->getHidden("id", "id", $id));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$topic_label.'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}


}








$act_op="";
if ((isset($_GET["act_op"])) && ($_GET["act_op"] != "")) $act_op=$_GET["act_op"];
if ((isset($_POST["act_op"])) && ($_POST["act_op"] != "")) $act_op=$_POST["act_op"];
switch($act_op) {
}


if ((isset($_GET["op"])) && ($_GET["op"] != "")) $op=$_GET["op"];
if ((isset($_POST["op"])) && ($_POST["op"] != "")) $op=$_POST["op"];
switch($op) {
	default; case "mantopic" : {
		man_topic();
	} break;

	case "addtopic" : {
		add_edit_topic(NULL, "add");
	} break;

	case "insnew" : {
		if (isset($_POST["undo"]))
			man_topic();
		else
			ins_topic();
	} break;

	case "modtopic" : {
		add_edit_topic((int)$_GET["id"], "edit");
	} break;

	case "updatetopic" : {
		if (isset($_POST["undo"]))
			man_topic();
		else
			update_topic();
	} break;

	case "deltopic" : {
		del_topic((int)$_GET["id"]);
	};break;

}




?>