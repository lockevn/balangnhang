<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* & Fabio Pirovano (gishell@tiscali.it)                                 */
/* http://www.docebocms.com                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

if(($GLOBALS['current_user']->isAnonymous()) || (!checkPerm('view', true))) die("You can't access!");


function form() {
	//access control

	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');
	//-TP// funAdminAccess('OP');

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("admin_form", "cms");

	$out->add(getTitleArea($lang->def("_FORM"), "form"));


	$out->add('<div class="std_block">');

	$tableForm = new typeOne(0);

	$out->add($tableForm->OpenTable($lang->def("_FORMLIST")));

	$can_add = checkPerm('add', true);
	$can_mod = checkPerm('mod', true);
	$can_del = checkPerm('del', true);

	$head = array($lang->def("_FORMNAME"),
			$lang->def("_EMAIL"),
			$lang->def("_DESCRIPTION"));
	$headType = array('', '', '');

	if($can_mod) {
		$head[] = '<img src="'.getPathImage().'standard/database.gif" title="'.$lang->def("_MAP_FIELDS").'" alt="'.$lang->def("_MAP_FIELDS").'" />';
		$head[] = '<img src="'.getPathImage('fw').'standard/list.gif" title="'.$lang->def("_MOD_ITEMS").'" alt="'.$lang->def("_MOD_ITEMS").'" />';
		$head[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />';
		$headType[] = 'image';
		$headType[] = 'image';
		$headType[] = 'image';
	}
	if($can_del) {
		$head[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").'" />';
		$headType[] = 'image';
	}

	$out->add($tableForm->WriteHeader(
		$head,
		$headType
		));


	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_form";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {

			$line=array();
			$line[]=$row["title"];
			$line[]=nl2br($row["email"]);
			$line[]=(strlen($row["fdesc"]) > 100 ? substr($row["fdesc"], 0, 100)."..." : $row["fdesc"]);

			if($can_mod) {

				if ($row["storeinfo"] == 1) {
					$img='<img src="'.getPathImage().'standard/database.gif" alt="'.$lang->def("_MAP_FIELDS").'" title="'.$lang->def("_MAP_FIELDS").'" />';
					$line[]="<a href=\"index.php?modname=form&amp;op=mapfields&amp;id=".$row["idForm"]."\">$img</a>\n";
				}
				else {
					$line[]="&nbsp;";
				}

				$img='<img src="'.getPathImage('fw').'standard/list.gif" alt="'.$lang->def("_MOD_ITEMS").'" title="'.$lang->def("_MOD_ITEMS").'" />';
				$line[]="<a href=\"index.php?modname=form&amp;op=moditems&amp;id=".$row["idForm"]."\">$img</a>\n";

				$img='<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def("_MOD").'" title="'.$lang->def("_MOD").'" />';
				$line[]="<a href=\"index.php?modname=form&amp;op=modform&amp;id=".$row["idForm"]."\">$img</a>\n";
			}

			if($can_del) {
				$img='<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def("_DEL").'" title="'.$lang->def("_DEL").' : '.$row['title'].'" />';
				$line[]="<a href=\"index.php?modname=form&amp;op=delform&amp;id=".$row["idForm"]."&amp;conf_del=1\"  title=\"".$lang->def("_DEL").' : '.$row['title']."\">$img</a>\n";
			}

			$out->add($tableForm->WriteRow($line));

		}
	}

	if ($can_add) {
		$out->add($tableForm->WriteAddRow('<a href="index.php?modname=form&amp;op=newform" title="'.$lang->def("_ADD").'">
								<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def("_ADD").'" /> '.$lang->def("_ADD").'</a>'));
	}
	$out->add($tableForm->CloseTable());

	if ($can_del) {
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delform]');
	}

	$out->add('</div>');

	require_once($GLOBALS["where_framework"]."/class/class.fieldmap_user.php");
	$fmu=new FieldMapUser();
}

function newform($err="") {
	//access control
	checkPerm('add');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("admin_form", "cms");
	$form=new Form();

	$back_ui_url="index.php?modname=form&amp;op=form";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_FORM");
	$title_arr[]=$lang->def("_ADD_FORM");
	$out->add(getTitleArea($title_arr, "form"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	if ($err != "")
		$out->add(getErrorUi($err));

	$out->add($form->openForm("form_form", "index.php?modname=form&amp;op=insform"));
	$out->add($form->openElementSpace());


	$out->add($form->getTextField($lang->def("_FORMNAME"), "title", "title", 255, (isset($_POST["title"]) ? $_POST["title"] : "")));
	$out->add($form->getSimpleTextarea($lang->def("_EMAIL")."(".$lang->def("_1FORLINE").")", "email", "email", (isset($_POST["email"]) ? $_POST["email"] : "")));
	$out->add($form->getSimpleTextarea($lang->def("_DESCRIPTION"), "fdesc", "fdesc", (isset($_POST["fdesc"]) ? $_POST["fdesc"] : "")));

	$out->add($form->getHidden("form_type", "form_type", "normal"));

	$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def('_SAVE')));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}

function insform() {
	//REQUIRES: at least one name passed in array nameForm
	//EFFECTS : insert a form
	//MODIFIES: $GLOBALS["prefix_cms"]_form

	//access control
	checkPerm('add');

	$lang=& DoceboLanguage::createInstance("admin_form", "cms");

	$err="";
	if ($_POST["title"] == "") $err=$lang->def("_NOTITLE");
	if ($_POST["email"] == "") $err=$lang->def("_NOEMAIL");
	$form_type=(empty($_POST["form_type"]) ? "normal" : $_POST["form_type"]);

	switch($form_type) {
		case "normal": {
			$storeinfo=0;
		} break;
		case "crm_contact": {
			$storeinfo=1;
		} break;
	}

	if (isset($_POST["storeinfo"])) {
		$storeinfo=$_POST["storeinfo"];
	}

	if ($err == "") {
		$qtxt ="INSERT INTO ".$GLOBALS["prefix_cms"]."_form (title, fdesc, email, storeinfo, form_type) VALUES";
		$qtxt.="('".$_POST["title"]."', '".$_POST["fdesc"]."', '".$_POST["email"]."', ";
		$qtxt.="'".$storeinfo."', '".$form_type."' );";
		$q=mysql_query($qtxt);
		if (!$q) newform(mysql_error());
		else {
			jumpTo("index.php?modname=form&op=form");
		}
	}
	else {
		newform($err);
	}

}

function modform($err="") {
	//access control
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("admin_form", "cms");
	$form=new Form();

	$id=(int)$_GET["id"];
	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_form WHERE idForm='$id';";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
	}


	$back_ui_url="index.php?modname=form&amp;op=form";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_FORM");
	$title_arr[]=$lang->def("_EDIT_FORM").": ".$row["title"];
	$out->add(getTitleArea($title_arr, "form"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));


	if ($err != "")
		$out->add(getErrorUi($err));

	$out->add($form->openForm("form_form", "index.php?modname=form&amp;op=upform"));
	$out->add($form->openElementSpace());


	$out->add($form->getTextField($lang->def("_FORMNAME"), "title", "title", 255, $row["title"]));
	$out->add($form->getSimpleTextarea($lang->def("_EMAIL")."(".$lang->def("_1FORLINE").")", "email", "email", $row["email"]));
	$out->add($form->getSimpleTextarea($lang->def("_DESCRIPTION"), "fdesc", "fdesc", $row["fdesc"]));

	$out->add($form->getHidden("form_type", "form_type", $row["form_type"]));
	$out->add($form->getHidden("id", "id", $id));

	$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def('_SAVE')));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");
}

function upform() {
	//REQUIRES: at least one name passed in array nameForm[]
	//EFFECTS : modify a form
	//MODIFIES: $GLOBALS["prefix_cms"]_form

	//access control
	checkPerm('mod');

	$err="";
	if ($_POST["title"] == "") $err=_NOTITLE;
	if ($_POST["email"] == "") $err=_NOEMAIL;
	$form_type=(empty($_POST["form_type"]) ? "normal" : $_POST["form_type"]);


	switch($form_type) {
		case "normal": {
			$storeinfo=0;
		} break;
		case "crm_contact": {
			$storeinfo=1;
		} break;
	}

	if (isset($_POST["storeinfo"])) {
		$storeinfo=$_POST["storeinfo"];
	}

	$id=$_POST["id"];

	if ($err == "") {
		$qtxt ="UPDATE ".$GLOBALS["prefix_cms"]."_form SET title='".$_POST["title"]."', ";
		$qtxt.="fdesc='".$_POST["fdesc"]."', email='".$_POST["email"]."', ";
		$qtxt.="storeinfo='".$storeinfo."', form_type='".$form_type."' WHERE idForm='$id';";
		$q=mysql_query($qtxt);
		if (!$q) newform(mysql_error());
		else {
			jumpTo("index.php?modname=form&op=form");
		}
	}
	else {
		modform($err);
	}

}



function delform($id) {

	//access control
	checkPerm('del');

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_form', 'cms');

	if (isset($_POST["canc_del"])) {
		jumpTo("index.php?modname=form&op=form");
	}
	else if ( get_req("conf_del", DOTY_INT, false) ) {

		$id=get_req("id", DOTY_INT, false);

		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_form WHERE idForm='$id' LIMIT 1;");
		mysql_query("DELETE FROM ".$GLOBALS["prefix_cms"]."_form_items WHERE idForm='$id';");

		jumpTo("index.php?modname=form&op=form");
	}
	else {

		//load info
		list($title) = mysql_fetch_row(mysql_query("
		SELECT title
		FROM ".$GLOBALS["prefix_cms"]."_form
		WHERE idForm  = '".$id."'"));

		$back_ui_url="index.php?modname=form&amp;op=form";
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_FORM");
		$title_arr[]=$lang->def("_DELETE_FORM").": ".$title;
		$out->add(getTitleArea($title_arr, "form"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$out->add($form->openForm("form", "index.php?modname=form&amp;op=delform&amp;id=$id"));

		$out->add($form->getHidden("id", "id", $id));

		$out->add(getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$title.'<br />',
			false,
			'conf_del',
			'canc_del'));

		$out->add($form->closeForm());
		$out->add("</div>\n");
	}

}


function mod_items() {
	//access control
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("admin_form", "cms");


	$can_add=checkPerm('mod', true);

	$qtxt="SELECT title FROM ".$GLOBALS["prefix_cms"]."_form WHERE idForm='".(int)$_GET["id"]."'";
	list($title)=mysql_fetch_row(mysql_query($qtxt));
	$back_ui_url="index.php?modname=form&amp;op=form";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_FORM");
	$title_arr[]=$lang->def("_FORM_MOD_ITEMS").": ".$title;
	$out->add(getTitleArea($title_arr, "form"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$table = new typeOne(0);

	$out->add($table->OpenTable(""));


	$head = array($lang->def("_FIELDNAME"),
			'<img src="'.getPathImage().'standard/flag.gif" title="'.$lang->def("_COMPULSORY").'" alt="'.$lang->def("_COMPULSORY").'" />',
			'<img src="'.getPathImage().'standard/publish.gif" title="'.$lang->def("_MAKE_COMP").'" alt="'.$lang->def("_MAKE_COMP").'" />',
			'<img src="'.getPathImage().'standard/unpublish.gif" title="'.$lang->def("_REM_COMP").'" alt="'.$lang->def("_REM_COMP").'" />',
			'<img src="'.getPathImage().'standard/down.gif" title="'.$lang->def("_MOVE_DOWN").'" alt="'.$lang->def("_MOVE_DOWN").'" />',
			'<img src="'.getPathImage().'standard/up.gif" title="'.$lang->def("_MOVE_UP").'" alt="'.$lang->def("_MOVE_UP").'" />');
	$headType = array('', 'img', 'img', 'img', 'img', 'img');

	$out->add($table->WriteHeader(
		$head,
		$headType
		));

	$fl=new FieldList();
	$all_fields=$fl->getAllFields();


	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_form_items WHERE idForm='".(int)$_GET["id"]."' ORDER BY ord;";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$tot=mysql_num_rows($q);
		while ($row=mysql_fetch_array($q)) {

			$line=array();
			$line[]=$all_fields[$row["idField"]][FIELD_INFO_TRANSLATION];

			if ($row["comp"]) {
				$img='<img src="'.getPathImage().'standard/flag.gif" title="'.$lang->def("_IS_COMPULSORY").'" alt="'.$lang->def("_IS_COMPULSORY").'" />';
				$line[]=$img;
				$line[]="&nbsp;";

				$img='<img src="'.getPathImage().'standard/unpublish.gif" title="'.$lang->def("_REM_COMP").'" alt="'.$lang->def("_REM_COMP").'" />';
				$line[]="<a href=\"index.php?modname=form&amp;op=switchcomp&amp;id=".$row["idForm"]."&amp;field=".$row["id"]."\">$img</a>\n";
			}
			else {
				$img='<img src="'.getPathImage().'standard/flag_grey.gif" title="'.$lang->def("_IS_NOT_COMPULSORY").'" alt="'.$lang->def("_IS_NOT_COMPULSORY").'" />';
				$line[]=$img;

				$img='<img src="'.getPathImage().'standard/publish.gif" title="'.$lang->def("_MAKE_COMP").'" alt="'.$lang->def("_MAKE_COMP").'" />';
				$act="&amp;act_op=mvup&amp;ord=".$row["ord"];
				$line[]="<a href=\"index.php?modname=form&amp;op=switchcomp&amp;id=".$row["idForm"]."&amp;field=".$row["id"]."\">$img</a>\n";

				$line[]="&nbsp;";
			}


			if ($row["ord"] < $tot-1) {
				$img='<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def("_MOVE_DOWN").'" title="'.$lang->def("_MOVE_DOWN").'" />';
				$act="&amp;act_op=mvdown&amp;ord=".$row["ord"];
				$line[]="<a href=\"index.php?modname=form&amp;op=moditems&amp;id=".$row["idForm"]."$act\">$img</a>\n";
			}
			else
				$line[]="&nbsp;";

			if ($row["ord"] > 0) {
				$img='<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def("_MOVE_UP").'" title="'.$lang->def("_MOVE_UP").'" />';
				$act="&amp;act_op=mvup&amp;ord=".$row["ord"];
				$line[]="<a href=\"index.php?modname=form&amp;op=moditems&amp;id=".$row["idForm"]."$act\">$img</a>\n";
			}
			else
				$line[]="&nbsp;";

			$out->add($table->WriteRow($line));

		}
	}

	if($can_add) {
		$out->add($table->WriteAddRow('<a href="index.php?modname=form&amp;op=selfields&amp;id='.(int)$_GET["id"].'" title="'.$lang->def("_ADDREMITEM").'">
								<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def("_ADDREMITEM").'" /> '.$lang->def("_ADDREMITEM").'</a>'));
	}
	$out->add($table->CloseTable());


	$out->add("</div>\n");
}



function sel_fields() {
	//access control
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("admin_form", "cms");
	$form=new Form();

	$fl=new FieldList();
	$all_fields=$fl->getAllFields();

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_form_items WHERE idForm='".(int)$_GET["id"]."';";
	$q=mysql_query($qtxt);

	$field=array();
	if (($q) && (mysql_num_rows($q) > 0)) { // items that has already been added
		while ($row=mysql_fetch_array($q)) {
			$idField=$row["idField"];

			$field[$idField]=$idField;
		}
	}


	$qtxt="SELECT title FROM ".$GLOBALS["prefix_cms"]."_form WHERE idForm='".(int)$_GET["id"]."'";
	list($title)=mysql_fetch_row(mysql_query($qtxt));
	$home_url="index.php?modname=form&amp;op=form";
	$back_ui_url="index.php?modname=form&amp;op=moditems&amp;id=".(int)$_GET["id"];
	$title_arr=array();
	$title_arr[$home_url]=$lang->def("_FORM");
	$title_arr[$back_ui_url]=$lang->def("_FORM_MOD_ITEMS").": ".$title;
	$title_arr[]=$lang->def("_ADDREMITEM");
	$out->add(getTitleArea($title_arr, "form"));
	$out->add("<div class=\"std_block\">\n");
	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

	$out->add($form->openForm("form_form", "index.php?modname=form&amp;op=moditems&amp;id=".(int)$_GET["id"]));
	$out->add($form->openElementSpace());
//$out->add('debug: '.count($all_fields));
	foreach($all_fields as $key=>$val) {

		$sel=(in_array($key, $field) ? true:false);

		if ($val[FIELD_INFO_TYPE] !== "upload") {
			$out->add($form->getCheckBox($val[FIELD_INFO_TRANSLATION], "field_".$key."_", "field[".$key."]", 1, $sel));
		}

	}

	$out->add($form->getHidden("act_op", "act_op", "savefields"));

	$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def('_SAVE')));
	$out->add($form->getButton('undo', 'undo', $lang->def('_UNDO')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());

	$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	$out->add("</div>\n");


// ----------------------------------------------------
return 0; // Old:


	//category dropdown
	$qtxt = mysql_query("
	SELECT idCommon, nameCategory
	FROM ".$GLOBALS["prefix_cms"]."_groupcategory
	WHERE language = '".get_lang()."'
	ORDER BY nameCategory");

	echo("<form action=\"index.php?modname=form&amp;op=moditems&amp;id=".(int)$_GET["id"]."\" method=\"post\">\n");

	echo '<div class="lineTitle">'._CATEGORYDROPDOWN
		.'</div>';
	$i = 0;
	while(list($idC, $nameC) = mysql_fetch_row($qtxt)) {
		echo '<div class="line'.($i++%2 == 0 ? 'col' : '').'">'
			.'<input type="checkbox" id="field[dropdown]['.$idC.']" name="field[dropdown]['.$idC.']" value="'.$idC.'"';
		if(isset($field["dropdown"][$idC])) echo ' checked="checked"';
		echo ' />'."\n".$nameC."\n"
			.'</div>';
	}

	//freetext field
	$qtxt = mysql_query("
	SELECT idCommon, nameFreetext
	FROM ".$GLOBALS["prefix_cms"]."_groupfreetext
	WHERE language = '".get_lang()."'
	ORDER BY nameFreetext");

	echo '<br />'
		.'<div class="lineTitle">'._FREETEXT.'</div>';
	$i = 0;
	while(list($idC, $nameF) = mysql_fetch_row($qtxt)) {
		echo '<div class="line'.($i++%2 == 0 ? 'col' : '').'">'
			.'<input type="checkbox" id="field[freetext]['.$idC.']" name="field[freetext]['.$idC.']" value="'.$idC.'"';
		if(isset($field["freetext"][$idC])) echo ' checked="checked"';
		echo ' />'."\n".$nameF."\n"
			.'</div>';
	}

	//textarea field
	$qtxt = mysql_query("
	SELECT idCommon, nameTextarea
	FROM ".$GLOBALS["prefix_cms"]."_form_textarea
	WHERE language = '".get_lang()."'
	ORDER BY nameTextarea");

	echo '<br />'
		.'<div class="lineTitle">'._TEXTAREA.'</div>';
	$i = 0;
	while(list($idC, $nameF) = mysql_fetch_row($qtxt)) {
		echo '<div class="line'.($i++%2 == 0 ? 'col' : '').'">'
			.'<input type="checkbox" id="field[textarea]['.$idC.']" name="field[textarea]['.$idC.']" value="'.$idC.'"';
		if(isset($field["textarea"][$idC])) echo ' checked="checked"';
		echo ' />'."\n".$nameF."\n"
			.'</div>';
	}

	echo("<br /><input type=\"hidden\" id=\"act_op\" name=\"act_op\" value=\"savefields\" />\n");
	echo("<br /><input class=\"button\" type=\"submit\" value=\""._SAVE."\" />\n");
	echo("</form>\n");

	echo("</div><br />\n");

	echo("<form action=\"index.php?modname=form&amp;op=moditems&amp;id=".(int)$_GET["id"]."\" method=\"post\">\n");
	echo("<div class=\"std_block\">\n");
	echo("<input class=\"button\" type=\"submit\" value=\""._BACK."\" />\n");
	echo("</div>\n");
	echo("</form>\n");
}


function save_fields($id) {

	if (isset($_POST["field"]))
		$field=(array)$_POST["field"];
	else
		$field=array();

	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_form_items WHERE idForm='$id';";
	$q=mysql_query($qtxt);

	$saved=array();
	$remitems=array();
	if (($q) && (mysql_num_rows($q) > 0)) { // Found items that has to be removed or that has already been added
		$tot=mysql_num_rows($q);
		while ($row=mysql_fetch_array($q)) {

			$type=$row["type"];

			if (in_array($row["idField"], array_keys($field))) {
				$saved[]=$row["idField"];
			}
			else {
				$remitems[]=$row["idField"];
			}
		}
	}

	/* print_r($saved);
	print_r($remitems); die(); */

	$tot=count($saved);

	foreach ($field as $key=>$val) {
		$idField=$key;
		if (!in_array($idField, (array)$saved)) {
			//echo("<br />[ins] $type: $idField\n");
			$qtxt="INSERT INTO ".$GLOBALS["prefix_cms"]."_form_items (idForm, idField, ord) VALUES('$id', '$idField', '$tot');";
			mysql_query($qtxt);
			$tot++;
		}
	}

	foreach ($remitems as $key=>$val) {
		if (!in_array($val, (array)$saved)) {
			//echo("<br />[del] $type: $idField\n");
			$qtxt="DELETE FROM ".$GLOBALS["prefix_cms"]."_form_items WHERE idForm='$id' AND idField='$val' LIMIT 1;";
			mysql_query($qtxt);
		}
	}

	fix_order($id);
}


function fix_order($id) {


	$qtxt="SELECT * FROM ".$GLOBALS["prefix_cms"]."_form_items WHERE idForm='$id' ORDER BY ord;";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$i=0;
		while ($row=mysql_fetch_array($q)) {
			$id=$row["id"];
			$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_form_items SET ord='$i' WHERE id='$id' LIMIT 1;";
			mysql_query($qtxt);
			$i++;
		}
	}

}


function move_field($dir, $idForm, $ord) {


	$table=$GLOBALS["prefix_cms"]."_form_items";

	if ($dir == "down") {
		$qtxt ="UPDATE $table as t1, $table as t2 SET t1.ord='".($ord+1)."', t2.ord=($ord) ";
		$qtxt.="WHERE t1.idForm='$idForm' AND t2.idForm='$idForm' AND t1.ord='$ord' AND t2.ord=(".($ord+1).");";
	}
	else if ($dir == "up") {
		$qtxt ="UPDATE $table as t1, $table as t2 SET t1.ord='".($ord-1)."', t2.ord=($ord) ";
		$qtxt.="WHERE t1.idForm='$idForm' AND t2.idForm='$idForm' AND t1.ord='$ord' AND t2.ord=(".($ord-1).");";
	}
	$q=mysql_query($qtxt);
}


function switch_comp($idForm, $id) {


	$qtxt="SELECT comp FROM ".$GLOBALS["prefix_cms"]."_form_items WHERE id='$id';";

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_array($q);
		$comp=$row["comp"];

		if ($comp)
			$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_form_items SET comp='0' WHERE id='$id';";
		else
			$qtxt="UPDATE ".$GLOBALS["prefix_cms"]."_form_items SET comp='1' WHERE id='$id';";

		mysql_query($qtxt);
		header("location: index.php?modname=form&op=moditems&id=$idForm");
	}

}


function mapFields() {
	$res="";

	if ((isset($_GET["id"])) && ($_GET["id"] > 0))
		$form_id=$_GET["id"];
	else
		return FALSE;

	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("admin_form", "cms");
	$form=new Form();

	$qtxt="SELECT title FROM ".$GLOBALS["prefix_cms"]."_form WHERE idForm='".$form_id."'";
	list($title)=mysql_fetch_row(mysql_query($qtxt));
	$back_ui_url="index.php?modname=form&amp;op=form";
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_FORM");
	$title_arr[]=$lang->def("_MAP_FIELDS").": ".$title;
	$res.=getTitleArea($title_arr, "form");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));



	$res.=$form->openForm("form_form", "index.php?modname=form&amp;op=savemap");
	$res.=$form->openElementSpace();


	$fl=new FieldList();
	$all_fields=$fl->getAllFields();

	$fields="t1.idField, t2.field_map_resource, t2.field_type, t2.field_map_to";
	$qtxt ="SELECT ".$fields." FROM ".$GLOBALS["prefix_cms"]."_form_items as t1 ";
	$qtxt.="LEFT JOIN ".$GLOBALS["prefix_cms"]."_form_map as t2 ";
	$qtxt.="ON (t2.form_id='".$form_id."' AND t1.idField = t2.field_id) ";
	$qtxt.="WHERE t1.idForm='".$form_id."' ORDER BY t1.ord";
	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		while ($row=mysql_fetch_array($q)) {

			$field_id=$row["idField"];
			$field_name=$all_fields[$field_id][FIELD_INFO_TRANSLATION];

			$drop_id="field_map_".$field_id;
			$drop_name="field_map[".$field_id."]";

			$field_map=$row["field_map_resource"]."_".$row["field_type"]."_".$row["field_map_to"];

			$res.=getFieldMapDropdown($form, $field_name, $drop_id, $drop_name, $field_map);
		}
	}


	$res.=$form->getHidden("id", "id", $form_id);

	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $lang->def('_SAVE'));
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";

	$out->add($res);
}


function getFieldMapToArray() {
	$res=array();

	$lang=& DoceboLanguage::createInstance("admin_form", "cms");

	$map_to_arr=array("user", "company", "chistory");
	// TODO: unify this with the code in lib.fieldmap.php and put it in
	// class.fieldmap.php or leave it i lib.fieldmap.php ..
	foreach($map_to_arr as $key=>$val) {

		$code=(is_array($val) ? $key : $val);

		if ((is_array($val)) && (isset($val["class_path"])))
			$class_path=$val["class_path"];
		else
			$class_path=$GLOBALS["where_framework"]."/class/";

		if ((is_array($val)) && (isset($val["class_file"])))
			$class_file=$val["class_file"];
		else
			$class_file="class.fieldmap_".strtolower($code).".php";

		if ((is_array($val)) && (isset($val["class_name"])))
			$class_name=$val["class_name"];
		else
			$class_name="FieldMap".ucfirst($code);


		require_once($class_path.$class_file);
		$fmap=new $class_name();

		if (!isset($res[$val]))
			$res[$val]=array();

		$res[$val]=$res[$val]+$fmap->getPredefinedFields();
		$res[$val]=$res[$val]+$fmap->getCustomFields();
	}

	return $res;
}


function getFieldMapDropdown(&$form, $field_name, $drop_id, $drop_name, $field_map ) {
	$res="";

	$field_arr=getFieldMapToArray(); //print_r($field_arr);

	$res.=$form->openFormLine();
	$res.=$form->getLabel($drop_id, $field_name);

	$res.='<select class="dropdown" id="'.$drop_id.'" name="'.$drop_name.'"  >'."\n";


	$selected=($field_map == "none" ? ' selected="selected"' : '');
	$res.='<option value="none"'.$selected.'>'.def("_DO_NOT_USE", "admin_form", "cms").'</option>'."\n";

	$res.='<optgroup label="'.def("_USER_FIELDS", "admin_form", "cms").'">'."\n";
	foreach($field_arr["user"] as $key=>$val) {

		$selected=($key == $field_map ? ' selected="selected"' : '');

		$res.='<option value="'.$key.'"'.$selected.'>'.$val.'</option>'."\n";

	}
	$res.='</optgroup>'."\n";

	$res.='<optgroup label="'.def("_COMPANY_FIELDS", "admin_form", "cms").'">'."\n";
	foreach($field_arr["company"] as $key=>$val) {

		$selected=($key == $field_map ? ' selected="selected"' : '');

		$res.='<option value="'.$key.'"'.$selected.'>'.$val.'</option>'."\n";

	}
	$res.='</optgroup>'."\n";

	$res.='<optgroup label="'.def("_CHISTORY_FIELDS", "admin_form", "cms").'">'."\n";
	foreach($field_arr["chistory"] as $key=>$val) {

		$selected=($key == $field_map ? ' selected="selected"' : '');

		$res.='<option value="'.$key.'"'.$selected.'>'.$val.'</option>'."\n";

	}
	$res.='</optgroup>'."\n";

	$res.="</select>\n";

	$res.=$form->closeFormLine();
	return $res;
}


function saveFieldMap() {
	$debug=FALSE;

	$undo=(isset($_POST["undo"]) ? TRUE : FALSE);
	$has_data=((isset($_POST["field_map"])) && (is_array($_POST["field_map"])) ? TRUE : FALSE);

	if ((!$undo) && ($has_data)) {

		$form_id=(int)$_POST["id"];

		$qtxt ="DELETE FROM ".$GLOBALS["prefix_cms"]."_form_map ";
		$qtxt.="WHERE form_id='".$form_id."'";
		$q=mysql_query($qtxt);

		$ins_arr=array();
		$qtxt ="INSERT INTO ".$GLOBALS["prefix_cms"]."_form_map ";
		$qtxt.="(form_id, field_id, field_map_resource, field_type, field_map_to) VALUES ";

		if ($debug) {
			print_r($_POST["field_map"]);
			echo "<br /><br />";
		}

		foreach($_POST["field_map"] as $field_id=>$field_code) {

			if ($debug) {
				print_r($field_code);
				echo "<br />\n";
			}

			$field_info=explode("_", $field_code);
			$resource=$field_info[0];
			$field_type=$field_info[1];
			unset($field_info[0]);
			unset($field_info[1]);
			$field_map_id=implode("_", $field_info);
			$field_map_to=($field_type == "predefined" ? $field_map_id : (int)$field_map_id);

			if ($debug) {
				echo "resource: ".$resource."<br />\n";
				echo "field type: ".$field_type."<br />\n";
				echo "field map id: ".$field_map_id."<br />\n";
				echo "field map to: ".$field_map_to."<br />\n";
				echo "<br /><br />";
			}

			$ins_txt ="('".$form_id."', '".$field_id."', '".$resource."', ";
			$ins_txt.="'".$field_type."', '".$field_map_to."')";
			$ins_arr[]=$ins_txt;
		}

		if ($debug) {
			print_r($ins_arr); die();
		}

		if (count($ins_arr) > 0) {
			$qtxt.=implode(",\n", $ins_arr);
			$q=mysql_query($qtxt);
		}
	}

	jumpTo("index.php?modname=form&op=form");
}


function getFormTypeArray() {
	$lang=& DoceboLanguage::createInstance("admin_form", "cms");

	$res=array();
	$res["normal"]=$lang->def("_NORMAL");
	$res["crm_contact"]=$lang->def("_CRM_CONTACT");

	return $res;
}



// ------------------------------------------------------------------------------------------------
$act_op="";
if ((isset($_POST["act_op"])) && ($act_op == "")) $act_op=$_POST["act_op"];
if ((isset($_GET["act_op"])) && ($act_op == "")) $act_op=$_GET["act_op"];

switch ($act_op) {
	case "savefields": {
		if (!isset($_POST["undo"]))
			save_fields((int)$_GET["id"]);
	} break;
	case "mvdown": {
		move_field("down", (int)$_GET["id"], (int)$_GET["ord"]);
	} break;
	case "mvup": {
		move_field("up", (int)$_GET["id"], (int)$_GET["ord"]);
	} break;
}


$op=importVar("op");

switch($op) {
	case "form" : {
		form();
	};break;

	case "newform" : {
		newform();
	};break;
	case "insform" : {
		if (isset($_POST["undo"]))
			form();
		else
			insform();
	};break;

	case "modform" : {
		modform();
	};break;
	case "upform" : {
		if (isset($_POST["undo"]))
			form();
		else
			upform();
	};break;

	case "delform" : {
		delform((int)$_GET["id"]);
	};break;

	case "moditems" : {
		mod_items();
	};break;

	case "selfields" : {
		sel_fields();
	};break;

	case "switchcomp": {
		switch_comp((int)$_GET["id"], (int)$_GET["field"]);
	}	break;

	case "mapfields": {
		mapFields();
	} break;

	case "savemap": {
		saveFieldMap();
	} break;

}


?>