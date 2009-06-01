<?php
/*************************************************************************/
/* DOCEBO CRM - Customer Relationship Management                         */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @version  $Id:  $
 */
// ----------------------------------------------------------------------------
if(!defined('IN_DOCEBO')) die('You cannot access this file directly');
if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

require_once($GLOBALS["where_lms"]."/lib/lib.classlocation.php");


function classLocationMain() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("classlocation", "lms");

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_CLASS_LOCATION_TAB_CAPTION");
	$table_summary=$lang->def("_CLASS_LOCATION_TAB_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];


	$back_ui_url="index.php?modname=classlocation&amp;op=main";

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_CLASS_LOCATION");
	$res.=getTitleArea($title_arr, "classlocation");
	$res.="<div class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$tab=new typeOne($vis_item, $table_caption, $table_summary);


	$head=array($lang->def("_LOCATION"));
	$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;

	$head_type=array("", "image", "image");


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$tab->initNavBar('ini', 'link');
	$tab->setLink("index.php?modname=classlocation&amp;op=main");

	$ini=$tab->getSelectedElement();

	$clm=new ClassLocationManager();
	$list=$clm->getClassLocationList($ini, $vis_item);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["location_id"];

		$rowcnt=array();
		$rowcnt[]=$list_arr[$i]["location"];


		$url="index.php?modname=classlocation&amp;op=edit&amp;id=".$id;
		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";


		$url="index.php?modname=classlocation&amp;op=del&amp;id=".$id;
		$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
		$img.="title=\"".$lang->def("_DEL")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

		$tab->addBody($rowcnt);
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=del]');
	
	$url="index.php?modname=classlocation&amp;op=add";
	$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".$lang->def('_ADD')."</a>\n");


	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	$res.="</div>\n";
	$out->add($res);
}


function addeditClassLocation($id=0) {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("classlocation", "lms");

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form=new Form();
	$res="";

	$form_code="";
	$url="index.php?modname=classlocation";

	if ($id == 0) {
		$form_code=$form->openForm("main_form", $url."&amp;op=save");
		$submit_lbl=$lang->def("_INSERT");
		$page_title=$lang->def("_ADD_ITEM");

		$location="";
	}
	else if ($id > 0) {
		$form_code=$form->openForm("main_form", $url."&amp;op=save");

		$clm=new ClassLocationManager();
		$stored=$clm->getClassLocationInfo($id);

		$location=$stored["location"];
		$submit_lbl=$lang->def("_SAVE");
		$page_title=$lang->def("_EDIT_ITEM").": ".$location;
	}


	$back_ui_url="index.php?modname=classlocation&amp;op=main";

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_CLASS_LOCATION");
	$title_arr[]=$page_title;
	$res.=getTitleArea($title_arr, "classlocation");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

	$res.=$form_code.$form->openElementSpace();

	$res.=$form->getTextfield($lang->def("_LOCATION"), "location", "location", 255, $location);

	$res.=$form->getHidden("id", "id", $id);


	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $submit_lbl);
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();


	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}


function saveClassLocation() {

	$clm=new ClassLocationManager();

	$clm->saveData($_POST);
	jumpTo("index.php?modname=classlocation&op=main");
}


function deleteClassLocation() {
	$res="";

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("classlocation", "lms");
	$clm=new ClassLocationManager();

	$back_url="index.php?modname=classlocation&op=main";


	if (isset($_POST["undo"])) {
		jumpTo($back_url);
	}
	else if (get_req('conf_del', DOTY_INT, 0) == 1) {

		$clm->deleteClassLocation($_POST["id"]);

		jumpTo($back_url);
	}
	else {

		$id=(int)importVar("id");
		$stored=$clm->getClassLocationInfo($id);
		$location=$stored["location"];

		$back_ui_url="index.php?modname=classlocation&amp;op=main";

		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_CLASS_LOCATION");
		$title_arr[]=$lang->def("_DEL").": ".$location;
		$res.=getTitleArea($title_arr, "classlocation");
		$res.="<div class=\"std_block\">\n";
		$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

		$form=new Form();

		$url="index.php?modname=classlocation&amp;op=del";

		$res.=$form->openForm("main_form", $url);

		$res.=$form->getHidden("id", "id", $id);


		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_LOCATION').' :</span> '.$location.'<br />',
			false,
			'conf_del',
			'undo');

		$res.=$form->closeForm();
		$res.="</div>\n";
		$out->add($res);
	}
}


// ----------------------------------------------------------------------------

$op=(isset($_GET["op"]) ? $_GET["op"] : "main");

switch ($op) {

	case "main": {
			classLocationMain();
	} break;

	case "add": {
		addeditClassLocation();
	} break;

	case "save": {
		if (!isset($_POST["undo"]))
			saveClassLocation();
		else
			classLocationMain();
	} break;

	case "edit": {
		addeditClassLocation((int)$_GET["id"]);
	} break;

	case "del": {
		deleteClassLocation();
	} break;

}

?>