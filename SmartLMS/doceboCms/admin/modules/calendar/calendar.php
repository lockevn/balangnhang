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
if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

require_once($GLOBALS["where_cms"]."/lib/lib.calendar.php");


function &ccaSetup() {
	$res =new CmsCalendarAdmin();
	$res->urlManagerSetup("modname=calendar&op=main");
	return $res;
}


function calendarMain() {
	checkPerm("view");

	$res="";
	$cca =& ccaSetup();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$title=$cca->lang->def("_CALENDAR");
	$res.=$cca->titleArea($title);
	$res.=$cca->getHead();

	$vis_item=$GLOBALS["cms"]["visuItem"];
	$res.=$cca->getCalendarTable($vis_item);

	$res.=$cca->getFooter();
	$out->add($res);
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delcalendar]');
}


function addeditCalendar($id=0) {
	$res="";
	$cca =& ccaSetup();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	if ($id > 0) {
		checkPerm("mod");
		$info=$cca->calendarManager->getCalendarInfo($id);
		$title_label=$cca->lang->def("_EDIT_CALENDAR").": ".$info["title"];
	}
	else {
		checkPerm("add");
		$title_label=$cca->lang->def("_ADD_CALENDAR");
	}

	$um=& UrlManager::getInstance();
	$back_url=$um->getUrl();
	$title[$back_url]=$cca->lang->def("_CALENDAR");
	$title[]=$title_label;
	$res.=$cca->titleArea($title);
	$res.=$cca->getHead();
	$res.=$cca->backUi();

	$res.=$cca->addeditCalendar($id);

	$res.=$cca->backUi();
	$res.=$cca->getFooter();
	$out->add($res);
}


function saveCalendar() {
	checkPerm("mod");
	$cca =& ccaSetup();

	$cca->saveCalendar();
}


function deleteCalendar() {
	checkPerm("del");
	$res="";

	if ((isset($_GET["calid"])) && ($_GET["calid"] > 0))
		$cat_id=(int)$_GET["calid"];
	else
		return FALSE;

	$cca =& ccaSetup();


	$delete_ui_code=$cca->deleteCalendar($cat_id);

	if (!empty($delete_ui_code)) {

		$info=$cca->calendarManager->getCalendarInfo($cat_id);
		$title_label=$cca->lang->def("_DELETE_CALENDAR").": ".$info["title"];

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();
		$title[$back_url]=$cca->lang->def("_CALENDAR");
		$title[]=$title_label;
		$res.=$cca->titleArea($title);
		$res.=$cca->getHead();
		$res.=$cca->backUi();

		$res.=$delete_ui_code;

		$res.=$cca->getFooter();
		$out->add($res);
	}
}


function showCalPerm() {
	checkPerm("mod");
	$res="";

	if ((isset($_GET["calid"])) && ($_GET["calid"] > 0))
		$cat_id=(int)$_GET["calid"];
	else
		return FALSE;

	$cca =& ccaSetup();


	$page_content=$cca->showCalendarPerm($cat_id);

	if ($page_content !== FALSE) {

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");

		$info=$cca->calendarManager->getCalendarInfo($cat_id);
		$title_label=$cca->lang->def("_CALENDAR_PERMISSIONS").": ".$info["title"];

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();
		$title[$back_url]=$cca->lang->def("_CALENDAR");
		$title[]=$title_label;
		$res.=$cca->titleArea($title);
		$res.=$cca->getHead();

		$res.=$page_content;

		$res.=$cca->getFooter();
		$out->add($res);
	}

}


function doneCalPerm() {
	checkPerm("mod");
	include_once($GLOBALS['where_cms']."/lib/lib.reloadperm.php");
	setCmsReloadPerm();

	$cca =& ccaSetup();

	$um=& UrlManager::getInstance();
	$url=$um->getUrl();
	jumpTo($url);
}



//---------------------------------------------------------------------------//

function calendarDispatch($op) {
	switch($op) {
		case "main" : {
			calendarMain();
		} break;
		case "addcalendar" : {
			addeditCalendar();
		} break;
		case "editcat" : {
			addeditCalendar((int)$_GET["calid"]);
		} break;
		case "savecalendar" : {
			if (!isset($_POST["undo"]))
				saveCalendar();
			else
				calendarMain();
		} break;
		case "delcalendar" : {
			deleteCalendar();
		} break;
		case "setperm" : {
			showCalPerm();
		} break;
		case "doneperm" : {
			doneCalPerm();
		} break;
	}
}

?>
