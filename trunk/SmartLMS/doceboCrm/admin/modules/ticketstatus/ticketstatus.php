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

require_once($GLOBALS["where_crm"]."/admin/modules/ticketstatus/lib.ticketstatus.php");


function ticketStatusMain() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("ticketstatus", "crm");

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_TICKET_STATUS_TAB_CAPTION");
	$table_summary=$lang->def("_TICKET_STATUS_TAB_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];


	$back_ui_url="index.php?modname=ticketstatus&amp;op=main";

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_STATUS");
	$res.=getTitleArea($title_arr, "ticketstatus");
	$res.="<div class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$tab=new typeOne($vis_item, $table_caption, $table_summary);


	$head=array($lang->def("_DESCRIPTION"));
	$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$lang->def("_MOVE_DOWN")."\" ";
	$img.="title=\"".$lang->def("_MOVE_DOWN")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$lang->def("_MOVE_UP")."\" ";
	$img.="title=\"".$lang->def("_MOVE_UP")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;

	$head_type=array("", "image", "image", "image", "image");


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$tab->initNavBar('ini', 'link');
	$tab->setLink("index.php");

	$ini=$tab->getSelectedElement();

	$ctm=new TicketStatusManager();
	$list=$ctm->getTicketStatusList($ini, $vis_item);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["status_id"];

		$rowcnt=array();
		$rowcnt[]=$list_arr[$i]["label"];

		if ($i+$ini+1 < $db_tot) {
			$url="index.php?modname=ticketstatus&amp;op=movedown&amp;id=".$id;
			$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$lang->def("_MOVE_DOWN")."\" ";
			$img.="title=\"".$lang->def("_MOVE_DOWN")."\" />";
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
		}
		else
			$rowcnt[]="&nbsp;";

		if ($i+$ini > 0) {
			$url="index.php?modname=ticketstatus&amp;op=moveup&amp;id=".$id;
			$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$lang->def("_MOVE_UP")."\" ";
			$img.="title=\"".$lang->def("_MOVE_UP")."\" />";
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
		}
		else
			$rowcnt[]="&nbsp;";


		$url="index.php?modname=ticketstatus&amp;op=edit&amp;id=".$id;
		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";


		if (!$list_arr[$i]["is_used"]) {
			$url="index.php?modname=ticketstatus&amp;op=del&amp;id=".$id;
			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
			$img.="title=\"".$lang->def("_DEL")."\" />";
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
		}
		else
			$rowcnt[]="&nbsp;";

		$tab->addBody($rowcnt);
	}


	$url="index.php?modname=ticketstatus&amp;op=add";
	$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".$lang->def('_ADD')."</a>\n");


	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	$res.="</div>\n";
	$out->add($res);
}


function addeditTicketStatus($id=0) {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("ticketstatus", "crm");

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form=new Form();
	$res="";

	$form_code="";
	$url="index.php?modname=ticketstatus";

	if ($id == 0) {
		$form_code=$form->openForm("main_form", $url."&amp;op=save");
		$submit_lbl=$lang->def("_INSERT");
		$page_title=$lang->def("_ADD_ITEM");

		$label="";
	}
	else if ($id > 0) {
		$form_code=$form->openForm("main_form", $url."&amp;op=save");

		require_once($GLOBALS["where_crm"]."/admin/modules/ticketstatus/lib.ticketstatus.php");

		$ctm=new TicketStatusManager();
		$stored=$ctm->getTicketStatusInfo($id);

		$label=$stored["label"];
		$submit_lbl=$lang->def("_MOD");
		$page_title=$lang->def("_MOD").": ".$label;
	}


	$back_ui_url="index.php?modname=ticketstatus&amp;op=main";

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_STATUS");
	$title_arr[]=$page_title;
	$res.=getTitleArea($title_arr, "ticketstatus");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

	$res.=$form_code.$form->openElementSpace();

	$res.=$form->getTextfield($lang->def("_DESCRIPTION"), "label", "label", 255, $label);

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


function saveTicketStatus() {
	require_once($GLOBALS["where_crm"]."/admin/modules/ticketstatus/lib.ticketstatus.php");

	$ctm=new TicketStatusManager();

	$ctm->saveData($_POST);
	jumpTo("index.php?modname=ticketstatus&op=main");
}


function moveItem($direction, $id_val) {
	require_once($GLOBALS["where_crm"]."/admin/modules/ticketstatus/lib.ticketstatus.php");

	$ctm=new TicketStatusManager();
	$ctm->moveItem($direction, $id_val);

	jumpTo("index.php?modname=ticketstatus&op=main");
}


function deleteTicketStatus() {
	$res="";

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");
	require_once($GLOBALS["where_crm"]."/admin/modules/ticketstatus/lib.ticketstatus.php");

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("ticketstatus", "crm");
	$ctm=new TicketStatusManager();

	$back_url="index.php?modname=ticketstatus&op=main";


	if (isset($_POST["undo"])) {
		jumpTo($back_url);
	}
	else if (isset($_POST["conf_del"])) {

		$ctm->deleteTicketStatus($_POST["id"]);

		jumpTo($back_url);
	}
	else {

		$id=(int)importVar("id");
		$stored=$ctm->getTicketStatusInfo($id);
		$label=$stored["label"];

		$back_ui_url="index.php?modname=ticketstatus&amp;op=main";

		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_STATUS");
		$title_arr[]=$lang->def("_DEL").": ".$label;
		$res.=getTitleArea($title_arr, "ticketstatus");
		$res.="<div class=\"std_block\">\n";
		$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

		$form=new Form();

		$url="index.php?modname=ticketstatus&amp;op=del";

		$res.=$form->openForm("main_form", $url);

		$res.=$form->getHidden("id", "id", $id);


		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_DESCRIPTION').' :</span> '.$label.'<br />',
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
			ticketStatusMain();
	} break;

	case "add": {
		addeditTicketStatus();
	} break;

	case "save": {
		if (!isset($_POST["undo"]))
			saveTicketStatus();
		else
			ticketStatusMain();
	} break;

	case "edit": {
		addeditTicketStatus((int)$_GET["id"]);
	} break;

	case "movedown": {
		moveItem("down", (int)$_GET["id"]);
	} break;

	case "moveup": {
		moveItem("up", (int)$_GET["id"]);
	} break;

	case "del": {
		deleteTicketStatus();
	} break;

}

?>