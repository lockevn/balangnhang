<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.com                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

if(($GLOBALS['current_user']->isAnonymous()) || (!checkPerm('view', true))) die("You can't access!");


include_once($GLOBALS['where_cms']."/admin/modules/banners/functions.php");
include_once($GLOBALS['where_cms']."/admin/modules/banners/banner_cat.php");
define("_BANNER_FPATH_INTERNAL", "/doceboCms/banners/");
define("_BANNER_FPATH", $GLOBALS['where_files_relative']._BANNER_FPATH_INTERNAL);

function banners() {
	//access control

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_banners', 'cms');

	$out->setWorkingZone('content');

	//-TP// funAdminAccess('OP');
	$out->add(getTitleArea($lang->def("_BANNER"), "banners"));

	$out->add("<div class=\"std_block\">\n");
	show_filter_dialog();
	show_banner_list(1);
	$out->add("</div>\n");
}


function new_banner($err="") {
	//access control
	checkPerm('add');

	//include("admin/modules/group/groupUtils.php");
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_banners', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');

	$back_ui_url="index.php?modname=banners&amp;op=banners";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_BANNER");
	$title_arr[]=$lang->def("_ADD_BANNER");
	$out->add(getTitleArea($title_arr, "banners"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));


	if ($err != "")
		$out->add(getErrorUi($err));

	$out->add($form->openForm("banner_form", "index.php?modname=banners&amp;op=insnew"));
	$out->add($form->openElementSpace());

	$out->add($form->getOpenFieldset($lang->def("_TYPE")));
	$out->add($form->getRadio($lang->def("_IMAGE"), "kind_image", "kind", "image", true));
	$out->add($form->getRadio($lang->def("_CODE"), "kind_code", "kind", "code", false));
	$out->add($form->getRadio($lang->def("_FLASH"), "kind_flash", "kind", "flash", false));
	$out->add($form->getCloseFieldset());


	$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def('_INSERT')));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}


function ins_new() {

	checkPerm('add');

	$lang=& DoceboLanguage::createInstance('admin_banners', 'cms');

	if ($_POST["kind"] != "") {
		//$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_banners (title, idGroup, type, kind) VALUES('"._UNTITLED."', '".$_POST["idGroup"]."', 'large', '".$_POST["kind"]."');";
		$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_banner (title, idGroup, kind) VALUES('".$lang->def("_UNTITLED")."', '0', '".$_POST["kind"]."');";
		$q=mysql_query($qtxt);
		if (!$q) new_banner(mysql_error());
		else {

			include_once($GLOBALS['where_cms']."/lib/lib.reloadperm.php");
			setCmsReloadPerm();

			list($id)=mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID() FROM ".$GLOBALS["prefix_cms"]."_banner;"));
			jumpTo("index.php?modname=banners&op=modbanner&id=$id");
		}
	}

}


function mod_banner() {
	//access control
	checkPerm('mod');

	//include_once("admin/modules/group/groupUtils.php");
	//include_once("core/manDateTime.php");

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_banners', 'cms');
	$form=new Form();

	$out->setWorkingZone('content');


	$id=$_GET["id"];
	$err=importVar("err");

	if ((isset($err)) && ($err != ""))
		$out->add(getErrorUi($err));


	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_banner WHERE banner_id='$id';";
	$q=mysql_query($qtxt);

	$row=FALSE;
	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
	}

	$back_ui_url="index.php?modname=banners&amp;op=banners";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_BANNER");
	$edit_banner=$lang->def("_EDIT_BANNER");
	if ($row !== FALSE) {
		$edit_banner.=": ".$row["title"];
	}
	$title_arr[]=$edit_banner;
	$out->add(getTitleArea($title_arr, "banners"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("banner_form", "index.php?modname=banners&amp;op=banners&amp;act_op=savebanner&amp;id=$id", "", "", "multipart/form-data"));

	$out->add($form->openElementSpace());


	if ($row !== FALSE) {

		$out->add($form->getTextfield($lang->def("_TITLE").":", "title", "title", 255, $row["title"]));
		$out->add($form->getTextfield($lang->def("_DESCRIPTION").":", "bdesc", "bdesc", 255, $row["bdesc"]));


		$out->add(getCategoryDropdown($form, $lang, $row["cat_id"]));

		/*echo("<br /><div class=\"title\">"._BANNER_GROUP.":\n");
		group_select("idGroup", $row["idGroup"]);
		echo("</div>\n");

		echo("<br /><div class=\"title\">"._type.":</div>\n");
		echo("<input type=\"radio\" id=\"type\" name=\"type\"".($row["type"] == "block" ? " checked=\"checked\"" : "")." value=\"block\" />"._BLOCK);
		echo("&nbsp;&nbsp;&nbsp;\n");
		echo("<input type=\"radio\" id=\"type\" name=\"type\"".($row["type"] == "large" ? " checked=\"checked\"" : "")." value=\"large\" />"._LARGE);
		echo("&nbsp;&nbsp;&nbsp;\n");
		echo("<input type=\"radio\" id=\"type\" name=\"type\"".($row["type"] == "main" ? " checked=\"checked\"" : "")." value=\"main\" />"._MAINBAN);
		echo("&nbsp;&nbsp;&nbsp;\n");
		echo("<input type=\"radio\" id=\"type\" name=\"type\"".($row["type"] == "context" ? " checked=\"checked\"" : "")." value=\"context\" />"._CONTBAN);
		echo("<br /><br />\n"); */

		/*init_calendar();
		echo("<div class=\"title\"><label for=\"kind\">"._EXPIRE.":</label></div>\n");
		echo("<input type=\"checkbox\" id=\"use_expdate\" name=\"use_expdate\"".($row["expdate"] > 0 ? " checked=\"checked\"" : "")." value=\"1\" />\n");
		echo("<b>"._EXP_DATE.":</b>\n");
		if ($row["expdate"] > 0) {
			set_from_timestamp($row["expdate"], $expdate, $exptime);
		}
		else
			$expdate="";
		make_cal($expdate, "_exp");
		echo("<br /><br />\n");
		echo("<input type=\"checkbox\" id=\"use_expimp\" name=\"use_expimp\"".($row["expimp"] > 0 ? " checked=\"checked\"" : "")." value=\"1\" />\n");
		echo("<b>"._EXP_IMP.":</b>\n");
		echo("<input type=\"text\" id=\"expimp\" name=\"expimp\" size=\"5\" value=\"".$row["expimp"]."\" /><br />\n"); */

		if (($row["expdate"] == 0) || ($row["expdate"] == "")) {
			$expdate=$GLOBALS["regset"]->internalToRegional(time());
			$use_expdate=false;
		}
		else {
			$expdate=$GLOBALS["regset"]->databaseToRegional($row["expdate"]);
			$use_expdate=true;
		}


		$out->add($form->getOpenFieldset($lang->def("_EXPIRATION")));

		$out->add($form->getCheckbox($lang->def("_USE_EXPDATE").":", "use_expdate", "use_expdate", "1", $use_expdate));
		$out->add($form->getDatefield($lang->def("_EXPDATE"), "expdate","expdate", $expdate, false, true));

		$sel=($row["expimp"] ? true : false);
		$out->add($form->getCheckbox($lang->def("_USE_EXP_IMP").":", "use_expimp", "use_expimp", "1", $sel));
		$out->add($form->getTextfield($lang->def("_EXP_IMP").":", "expimp", "expimp", 255, $row["expimp"]));

		$out->add($form->getCloseFieldset());


		switch ($row["kind"]) {
			case "image": {

				$out->add($form->getFilefield($lang->def("_TOPIC_IMAGE")." [<i>".$row["banfile"]."</i>]:", "image", "image", $row["banfile"]));

				$out->add($form->getTextfield($lang->def("_BANNER_URL").":", "banurl", "banurl", 255, $row["banurl"]));
			} break;
			case "code": {
				$out->add($form->getSimpleTextarea($lang->def("_BANNER_CODE").":", "bancode", "bancode", $row["bancode"]));
			} break;
			case "flash": {
				
				$out->add($form->getFilefield($lang->def("_FILE")." [<i>".$row["banfile"]."</i>]:", "swf", "swf", $row["banfile"]));

				if (!empty($row["banfile"])) {
					$out->add($form->getTextfield($lang->def("_WIDTH").":", "ban_w", "ban_w", 5, $row["ban_w"]));
					$out->add($form->getTextfield($lang->def("_HEIGHT").":", "ban_h", "ban_h", 5, $row["ban_h"]));
				}
				else {
					$out->add($form->getHidden("ban_w", "ban_w", 0));
					$out->add($form->getHidden("ban_h", "ban_h", 0));
				}

				$out->add($form->getTextfield($lang->def("_BANNER_BGCOL").":", "ban_bg", "ban_bg", 9, $row["ban_bg"]));
			} break;
		}

		$out->add($form->getOpenFieldset($lang->def("_VIS_LANGUAGE")));
		$out->add(sel_vis($form, $lang, $id, "language"));
		$out->add($form->getCloseFieldset());
		$out->add($form->getOpenFieldset($lang->def("_VIS_MACROAREA")));
		$out->add(sel_vis($form, $lang, $id, "macroarea"));
		$out->add($form->getCloseFieldset());
		/*echo("<br /><div class=\"title\">"._VIS_GROUPS.":\n");
		sel_vis($id, "group"); echo("</div>\n"); */

		$out->add($form->getHidden("kind", "kind", $row["kind"]));
		$out->add($form->getHidden("id", "id", $id));

		$out->add($form->closeElementSpace());
		$out->add($form->openButtonSpace());
		$out->add($form->getButton('save', 'save', $lang->def('_SAVE')));
		$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
		$out->add($form->closeButtonSpace());

	}
	else {
		$out->add($form->closeElementSpace());
		$out->add($form->openButtonSpace());
		$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
		$out->add($form->closeButtonSpace());
	}


	$out->add($form->closeForm());
	$out->add("</div>\n");
}



function save_banner($id) {
	checkPerm('mod');

	require_once($GLOBALS["where_framework"]."/lib/lib.upload.php");
	// include_once("core/manDateTime.php");

	$qtxt="SELECT banfile FROM ".$GLOBALS["prefix_cms"]."_banner WHERE banner_id='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$old_file=$row["banfile"];
	}
	else
		$old_file="";

	$qtxt ="UPDATE ".$GLOBALS["prefix_cms"]."_banner SET ";
	if ($_POST["title"] != "") $qtxt.="title='".$_POST["title"]."', ";
	$qtxt.="bdesc='".$_POST["bdesc"]."', ";
	$qtxt.="cat_id='".$_POST["cat_id"]."', ";
//	$qtxt.="idGroup='".$_POST["idGroup"]."', ";

	if (($_POST["kind"] == "code") && ($_POST["bancode"] != "")) $qtxt.="bancode='".$_POST["bancode"]."', ";

	if (($_POST["kind"] == "image") && ($_FILES["image"]["name"] != "")) {
		$fname=time().rand(10,99)."_".$_FILES["image"]["name"];
		$tmp_fname=$_FILES["image"]["tmp_name"];
		sl_open_fileoperations();
		$f1=sl_upload($tmp_fname, _BANNER_FPATH_INTERNAL.$fname);
		sl_close_fileoperations();
		if ($f1) {
			$qtxt.="banfile='".$fname."', ";
			if ($old_file != "") sl_unlink(_BANNER_FPATH_INTERNAL.$old_file);
		}
	}

	if ((isset($_POST["banurl"])) && ($_POST["banurl"] != "")) {
		$qtxt.="banurl='".$_POST["banurl"]."', ";
	}

	$size_autodetected=FALSE;
	if (($_POST["kind"] == "flash") && ($_FILES["swf"]["name"] != "")) {
		$fname=time().rand(10,99)."_".$_FILES["swf"]["name"];
		$tmp_fname=$_FILES["swf"]["tmp_name"];
		sl_open_fileoperations();
		$f1=sl_upload($tmp_fname, _BANNER_FPATH_INTERNAL.$fname);
		sl_close_fileoperations();
		if ($f1) {
			$qtxt.="banfile='".addslashes($fname)."', ";
		}

		// Try to auto-detect banner size:
		require_once($GLOBALS["where_framework"]."/lib/lib.multimedia.php");
		$swf_info=getSwfInfoArray(_BANNER_FPATH.$fname);

		if (!$swf_info["error"]) {
			$size_autodetected=TRUE;
			$qtxt.="ban_w='".$swf_info["width"]."', ";
			$qtxt.="ban_h='".$swf_info["height"]."', ";
		}
	}

	if (!$size_autodetected) {
		$qtxt.="ban_w='".$_POST["ban_w"]."', ";
		$qtxt.="ban_h='".$_POST["ban_h"]."', ";
	}

	$qtxt.="ban_bg='".$_POST["ban_bg"]."', ";


	if ((isset($_POST["use_expdate"])) && ($_POST["use_expdate"])) {
		$expdate=$GLOBALS["regset"]->regionalToDatabase($_POST["expdate"]);
		$qtxt.="expdate='".$expdate."', ";
	}
	else
		$qtxt.="expdate='0', ";

	if ((isset($_POST["use_expimp"])) && ($_POST["use_expimp"]))
		$qtxt.="expimp='".$_POST["expimp"]."' ";
	else
		$qtxt.="expimp='0' ";


	$qtxt.="WHERE banner_id='$id' LIMIT 1;";

	$save_q=mysql_query($qtxt);

	if ($save_q) {
		include_once($GLOBALS['where_cms']."/lib/lib.reloadperm.php");
		setCmsReloadPerm();
	}

	mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_banner_rules WHERE banner_id='$id';");

	if (isset($_POST["vis_language"])) {
		foreach ((array)$_POST["vis_language"] as $key=>$val) {
			mysql_query("INSERT INTO ".$GLOBALS["prefix_cms"]."_banner_rules (banner_id, item_val, item_type) VALUES('$id', '$val', 'language');");
		}
	}

	if (isset($_POST["vis_macroarea"])) {
		foreach ((array)$_POST["vis_macroarea"] as $key=>$val) {
			mysql_query("INSERT INTO ".$GLOBALS["prefix_cms"]."_banner_rules (banner_id, item_id, item_type) VALUES('$id', '$val', 'macroarea');");
		}
	}

	if (isset($_POST["vis_group"])) {
		foreach ((array)$_POST["vis_group"] as $key=>$val) {
			mysql_query("INSERT INTO ".$GLOBALS["prefix_cms"]."_banner_rules (banner_id, item_id, item_type) VALUES('$id', '$val', 'group');");
		}
	}

	return $save_q;
}


function details($id) {
	//access control
	//-TP// funAdminAccess('OP');

	require_once($GLOBALS['where_cms'].'/lib/manDateTime.php');

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance('admin_banners', 'cms');


	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_banner WHERE banner_id='$id';";
	$q=mysql_query($qtxt);

	$row=FALSE;
	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
	}

	$back_ui_url="index.php?modname=banners&amp;op=banners";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_BANNER");
	$edit_banner=$lang->def("_BANNER_DETAILS");
	if ($row !== FALSE) {
		$edit_banner.=": ".$row["title"];
	}
	$title_arr[]=$edit_banner;
	$out->add(getTitleArea($title_arr, "banners"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));


	if ($row !== FALSE) {

		$bdesc=$row["bdesc"];
		switch ($row["kind"]) {
			case "image": {
				$out->add("<img src=\""._BANNER_FPATH.$row["banfile"]."\" alt=\"$bdesc\" title=\"$bdesc\" />\n");
			} break;
			case "code": {
				$out->add($row["bancode"]);
			} break;
			case "flash": {
				$bg=$row["ban_bg"];
				$w=$row["ban_w"];
				$h=$row["ban_h"];
				$out->add("<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\" width=\"$w\" height=\"$h\" />\n");
				$out->add("<param name=\"movie\" value=\"./"._BANNER_FPATH.$row["banfile"]."\" />\n");
				$out->add("<param name=\"menu\" value=\"false\" />\n");
				$out->add("<param name=\"quality\" value=\"high\" />\n");
				$out->add("<param name=\"bgcolor\" value=\"$bg\" />\n");
				$out->add("<embed src=\"./"._BANNER_FPATH.$row["banfile"]."\" menu=\"false\" quality=\"high\" bgcolor=\"$bg\" width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_P rod_Version=ShockwaveFlash\">\n");
				$out->add("</embed>\n");
				$out->add("</object>\n");
			} break;
		}

		$out->add("<br /><br /><b>".$lang->def("_TITLE").":</b>\n");
		$out->add($row["title"]."<br /><br />\n");

		$out->add("<b>".$lang->def("_DESCRIPTION").":</b>\n");
		$out->add($row["bdesc"]."<br /><br />\n");

		/* $out->add("<b>"._BANNER_GROUP.":</b>\n");
		$groups=listGroup();
		$out->add($groups[$row["idGroup"]]);
		$out->add("<br /><br />\n"); */

		/* $out->add("<b>".$lang->def("_TYPE").":</b>\n");
		if ($row["type"] == "block") $out->add(_BLOCK);
		if ($row["type"] == "large") $out->add(_LARGE);
		$out->add("<br /><br />\n"); */

		$out->add("<b>".$lang->def("_EXP_DATE").":</b>\n");
		if ($row["expdate"] > 0) {
			//set_from_timestamp($row["expdate"], $expdate, $exptime);
			$expdate = $GLOBALS["regset"]->databaseToRegional($row["expdate"]);
			$exptime = '';
		}
		else
			$expdate="-";
		$out->add($expdate."<br /><br />\n");
		$out->add("<b>".$lang->def("_EXP_IMP").":</b>\n");
		$expimp=($row["expimp"] > 0 ? $row["expimp"] : "-");
		$out->add($expimp."<br /><br />\n");

		$out->add("<b>".$lang->def("_IMPRESSION").":</b>\n");
		$out->add($row["impression"]."<br /><br />\n");

		$out->add("<b>".$lang->def("_CLICK").":</b>\n");
		$out->add($row["click"]."<br /><br />\n");

		$out->add("<b>".$lang->def("_CTR").":</b>\n");
		$ctr=($row["impression"] > 0 ? number_format(100*$row["click"]/$row["impression"], 2, '.', '') : "0")."%";
		$out->add($ctr."<br /><br />\n");

	}


	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}



function del_banner($id) {
	//access control
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");
	include_once($GLOBALS['where_framework']."/lib/lib.upload.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_banners', 'cms');

	if (isset($_POST["canc_del"])) {
		jumpTo("index.php?modname=banners&op=banners");
	}
	else if ( get_req("conf_del", DOTY_INT, false) ) {

		$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_banner WHERE banner_id='$id';";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$file=$row["banfile"];

			// rimozione ruoli..

			$acl_manager=& $GLOBALS["current_user"]->getAclManager();
			$acl_manager->deleteRoleFromPath("/cms/banner/".$id."/");

			mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_banner WHERE banner_id='$id' LIMIT 1;");
			mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_banner_rules WHERE banner_id='$id';");
			if ($file != "") sl_unlink(_BANNER_FPATH_INTERNAL.$file);
		}

		jumpTo("index.php?modname=banners&op=banners");
	}
	else {

		$qtxt="SELECT title FROM ".$GLOBALS["prefix_cms"]."_banner WHERE banner_id='$id'";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$cat_name=$row["title"];
		}


		$back_ui_url="index.php?modname=banners&amp;op=banners";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_BANNER");
		$edit_banner=$lang->def("_DELETE_BANNER").": ".$cat_name;
		$title_arr[]=$edit_banner;
		$out->add(getTitleArea($title_arr, "banners"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$out->add($form->openForm("banner_form", "index.php?modname=banners&amp;op=delbanner&amp;id=$id"));

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


function show_selector($banner_id, $role) {

	require_once($GLOBALS['where_cms']."/lib/lib.simplesel.php");

	$out =& $GLOBALS['page'];
	$out->setWorkingZone("content");
	$lang =& DoceboLanguage::createInstance('admin_banners', 'cms');


	switch($role) {
		case "view": {
			$title=$lang->def('_SEL_BANNER_VISIBILITY');
			$op="visperm";
		} break;
		case "customer": {
			$title=$lang->def('_SEL_BANNER_CUSTOMER');
			$op="selcustomer";
		} break;
	}



	$acl=$GLOBALS["current_user"]->getAcl();
	$role_id="/cms/banner/".$banner_id."/".$role;
	$st_id=$acl->getRoleST($role_id);
	if (($st_id === FALSE) && ($banner_id > 0)) {
		$acl_manager=$acl->getACLManager();
		$st_id=$acl_manager->registerRole($role_id, "");
	}


	$ssel=new SimpleSelector(false, $lang);


	$url="index.php?modname=banners&amp;op=".$op."&amp;id=".$banner_id;
	$back_url="index.php?modname=banners&amp;op=banners";
	$ssel->setLinks($url, $back_url);

	$op=$ssel->getOp();

	if (($op == "main") || ($op == "manual_init") ) {
		$acl_manager=$GLOBALS["current_user"]->getACLManager();
		$saved_data=$acl_manager->getRoleGMembers($st_id);
	}

	$page_body="";
	$full_page="";

	switch($op) {

		case "main": {
			$ssel->setSavedData($saved_data);
			$page_body=$ssel->loadSimpleSelector();
		} break;

		case "manual_init":{

			// Saving permissions of simple selector
			$save_info=$ssel->getSaveInfo();
			//saveForumPerm($idForum, $save_info["selected"], $save_info["database"]);

			$ssel->setSavedData($saved_data);
			$full_page=$ssel->loadManualSelector($title);
		} break;
		case "manual": {
			$full_page=$ssel->loadManualSelector($title);
		} break;

		case "save_manual": {

			// Saving permissions of manual selector
			$save_info=$ssel->getSaveInfo();
			saveBannerPerm($st_id, $save_info["selected"], $save_info["database"]);

			jumpTo(str_replace("&amp;", "&", $url));
		} break;

		case "save": {

			// Saving permissions of simple selector
			$save_info=$ssel->getSaveInfo();
			saveBannerPerm($st_id, $save_info["selected"], $save_info["database"]);

			jumpTo(str_replace("&amp;", "&", $back_url));
		} break;

	}

	if (!empty($full_page))
		$out->add($full_page);

	if (!empty($page_body)) {
		// If we have only the page body, then better to add the area title.

		$back_ui_url="index.php?modname=banners&amp;op=banners";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_BANNER");
		$title_arr[]=$title;
		$out->add(getTitleArea($title_arr, "banners"));
		$out->add("<div class=\"std_block\">\n");
		//$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
		$out->add($page_body);
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
		$out->add("</div>\n");
	}

}


function saveBannerPerm($st_id, $selected, $database) {

	if (!($st_id > 0)) return 0;

	$arr_selection=$selected["view"];
	$db_tmp=array_flip($database["view"]);
	$arr_unselected=array_diff($db_tmp, $arr_selection);
	unset($db_tmp);

	//print_r($arr_selection); echo "------"; print_r($arr_unselected); die();

	$acl_manager=$GLOBALS["current_user"]->getACLManager();

	foreach($arr_unselected as $idstMember) {
		$acl_manager->removeFromRole($st_id, $idstMember );
	}

	foreach($arr_selection as $idstMember) {
		$acl_manager->addToRole($st_id, $idstMember );
	}

}


function show_selector_ok($banner_id, $role) {
	require_once($GLOBALS['where_framework']."/class.module/class.directory.php");
	require_once($GLOBALS['where_framework']."/lib/lib.acl.php");

	$mdir=new Module_Directory();

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_banners', 'cms');

	$out->setWorkingZone('content');

	$acl=new DoceboACL();

	$role_id="/cms/banner/".$banner_id."/".$role;
	$st_id=$acl->getRoleST($role_id);
	if (($st_id === FALSE) && ($banner_id > 0)) {
		$acl_manager=$acl->getACLManager();
		$st_id=$acl_manager->registerRole($role_id, "");
	}

	$back_url="index.php?modname=banners&amp;op=banners";

	if( isset($_POST['okselector']) ) {
		$arr_selection=$mdir->getSelection($_POST);
		$arr_unselected=$mdir->getUnselected();

		foreach($arr_unselected as $idstMember) {
			$mdir->aclManager->removeFromRole($st_id, $idstMember );
		}

		foreach($arr_selection as $idstMember) {
			$mdir->aclManager->addToRole($st_id, $idstMember );
		}

		include_once($GLOBALS['where_cms']."/lib/lib.reloadperm.php");
		setCmsReloadPerm();

		jumpTo(str_replace("&amp;", "&", $back_url));
	}
	else if( isset($_POST['cancelselector']) ) {
		jumpTo(str_replace("&amp;", "&", $back_url));
	}
	else {

		if( !isset($_GET['stayon']) ) {
			$mdir->resetSelection($mdir->aclManager->getRoleGMembers($st_id));
		}

		switch($role) {
			case "view": {
				$title=$lang->def('_SEL_BANNER_VISIBILITY');
			} break;
			case "customer": {
				$title=$lang->def('_SEL_BANNER_CUSTOMER');
			} break;
		}

		$mdir->show_user_selector = TRUE;
		$mdir->show_group_selector = TRUE;
		$mdir->show_orgchart_selector = FALSE;

		$url="index.php?modname=banners&amp;op=".$_GET["op"]."&amp;id=".$banner_id."&amp;stayon=1";
		$mdir->loadSelector($url,
			$title, "", TRUE);
	}

}



// --------------------------------------------------------------------------------------------

$act_op="";
if ((isset($_GET["act_op"])) && ($_GET["act_op"] != "")) $act_op=$_GET["act_op"];
if ((isset($_POST["act_op"])) && ($_POST["act_op"] != "")) $act_op=$_POST["act_op"];

switch($act_op) {
	case "savebanner" : {
		if (!isset($_POST["undo"]))
			save_banner($_POST["id"]);
	} break;

	case "activate" : {
		//-TP// funAdminAccess('MOD');
		mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_banner SET status='1' WHERE banner_id='".$_GET["id"]."';");
	} break;

	case "deactivate" : {
		//-TP// funAdminAccess('MOD');
		mysql_query("UPDATE ".$GLOBALS["prefix_cms"]."_banner SET status='0' WHERE banner_id='".$_GET["id"]."';");
	} break;

}


$op="";
if ((isset($_GET["op"])) && ($_GET["op"] != "")) $op=$_GET["op"];
if ((isset($_POST["op"])) && ($_POST["op"] != "")) $op=$_POST["op"];


switch($op) {
	case "banners": {
		banners();
	};break;

	case "newbanner": {
		new_banner();
	} break;

	case "insnew": {
		if (isset($_POST["undo"]))
			banners();
		else
			ins_new();
	} break;

	case "modbanner" : {
		mod_banner();
	} break;

	case "delbanner" : {
		del_banner((int)$_GET["id"]);
	} break;

	case "details" : {
		details((int)$_GET["id"]);
	} break;


	case "selcustomer": {
		if (isset($_POST["undo"]))
			banners();
		else
			show_selector((int)$_GET["id"], "customer");
	} break;

	case "visperm": {
		if (isset($_POST["undo"]))
			banners();
		else
			show_selector((int)$_GET["id"], "view");
	} break;


	case "viewcat" : {
		banner_cat_list();
	} break;

	case "addbannercat" : {
		add_edit_bannercat();
	} break;

	case "insbannercat" : {
		if (isset($_POST["undo"]))
			banner_cat_list();
		else
			ins_bannercat();
	} break;

	case "editbannercat": {
		add_edit_bannercat($_GET["id"], "edit");
	} break;

	case "updbannercat" : {
		if (isset($_POST["undo"]))
			banner_cat_list();
		else
			upd_bannercat($_POST["id"]);
	} break;


	case "delbannercat" : {
			del_bannercat($_GET["id"]);
	} break;

}



?>
