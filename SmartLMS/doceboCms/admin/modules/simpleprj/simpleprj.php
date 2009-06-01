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

require_once($GLOBALS["where_cms"]."/lib/lib.simpleprj.php");
require_once($GLOBALS["where_cms"]."/modules/simpleprj/define.simpleprj.php");

// TODO: Check permissions!!

function csaSetup() {
	if (!isset($GLOBALS["core_simpleprj_admin"]))
		$GLOBALS["core_simpleprj_admin"]=new SimplePrjAdmin();

	$csa=& $GLOBALS["core_simpleprj_admin"];
	$csa->urlManagerSetup("modname=simpleprj&op=main");
}


function simpleprjMain() {
	checkPerm("view");

	$res="";
	csaSetup();
	$csa=& $GLOBALS["core_simpleprj_admin"];
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$title=$csa->lang->def("_SIMPLEPRJ");
	$res.=$csa->titleArea($title);
	$res.=$csa->getHead();

	$vis_item=$GLOBALS["cms"]["visuItem"];
	$res.=$csa->getSimplePrjTable($vis_item);

	$res.=$csa->getFooter();
	$out->add($res);
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delproject]');
}


function addeditSimplePrj($id=0) {
	$res="";
	csaSetup();
	$csa=& $GLOBALS["core_simpleprj_admin"];
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	if ($id > 0) {
		$info=$csa->simpleprjManager->getSimplePrjInfo($id);
		$title_label=$csa->lang->def("_MOD").": ".$info["title"];
	}
	else {
		$title_label=$csa->lang->def("_ADD");
	}

	$um=& UrlManager::getInstance();
	$back_url=$um->getUrl();
	$title[$back_url]=$csa->lang->def("_SIMPLEPRJ");
	$title[]=$title_label;
	$res.=$csa->titleArea($title);
	$res.=$csa->getHead();
	$res.=$csa->backUi();

	$res.=$csa->addeditSimplePrj($id);

	$res.=$csa->backUi();
	$res.=$csa->getFooter();
	$out->add($res);
}


function saveSimplePrj() {
	csaSetup();
	$csa=& $GLOBALS["core_simpleprj_admin"];
	$csa->saveSimplePrj();
}


function deleteSimplePrj() {
	$res="";

	if ((isset($_GET["prjid"])) && ($_GET["prjid"] > 0))
		$cat_id=(int)$_GET["prjid"];
	else
		return FALSE;


	csaSetup();
	$csa=& $GLOBALS["core_simpleprj_admin"];

	$delete_ui_code=$csa->deletePrj($cat_id);

	if (!empty($delete_ui_code)) {

		$info=$csa->simpleprjManager->getSimplePrjInfo($cat_id);
		$title_label=$csa->lang->def("_DEL").": ".$info["title"];

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();
		$title[$back_url]=$csa->lang->def("_SIMPLEPRJ");
		$title[]=$title_label;
		$res.=$csa->titleArea($title);
		$res.=$csa->getHead();
		$res.=$csa->backUi();

		$res.=$delete_ui_code;

		$res.=$csa->getFooter();
		$out->add($res);
	}
}


function showPrjPerm() {
	$res="";

	if ((isset($_GET["prjid"])) && ($_GET["prjid"] > 0))
		$cat_id=(int)$_GET["prjid"];
	else
		return FALSE;

	csaSetup();
	$csa=& $GLOBALS["core_simpleprj_admin"];

	$page_content=$csa->showPrjPerm($cat_id);

	if ($page_content !== FALSE) {

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");

		$info=$csa->simpleprjManager->getSimplePrjInfo($cat_id);
		$title_label=$csa->lang->def("_SIMPLEPRJ_PROJECT_PERMISSIONS").": ".$info["title"];

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();
		$title[$back_url]=$csa->lang->def("_SIMPLEPRJ");
		$title[]=$title_label;
		$res.=$csa->titleArea($title);
		$res.=$csa->getHead();

		$res.=$page_content;

		$res.=$csa->getFooter();
		$out->add($res);
	}

}


function donePrjPerm() {
	include_once($GLOBALS['where_cms']."/lib/lib.reloadperm.php");
	setCmsReloadPerm();

	csaSetup();

	$um=& UrlManager::getInstance();
	$url=$um->getUrl();
	jumpTo($url);
}


function moveSimplePrj($direction) {

	if ((isset($_GET["prjid"])) && ($_GET["prjid"] > 0))
		$project_id=(int)$_GET["prjid"];
	else
		return FALSE;

	csaSetup();
	$csa=& $GLOBALS["core_simpleprj_admin"];

	$csa->moveSimplePrj($project_id, $direction);
}


//---------------------------------------------------------------------------//

function simpleprjDispatch($op) {
	switch($op) {
		case "main" : {
			simpleprjMain();
		} break;
		case "addproject" : {
			addeditSimplePrj();
		} break;
		case "editprj" : {
			addeditSimplePrj((int)$_GET["prjid"]);
		} break;
		case "saveproject" : {
			if (!isset($_POST["undo"]))
				saveSimplePrj();
			else
				simpleprjMain();
		} break;
		case "delproject" : {
			deleteSimplePrj();
		} break;
		case "setperm" : {
			showPrjPerm();
		} break;
		case "doneperm" : {
			donePrjPerm();
		} break;
		case "movedown": {
			moveSimplePrj("down");
		} break;
		case "moveup": {
			moveSimplePrj("up");
		} break;
	}
}

?>
