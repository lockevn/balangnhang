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

require_once($GLOBALS["where_framework"]."/lib/lib.faq.php");
// TODO: Check permissions!!

function cfaSetup() {
	if (!isset($GLOBALS["core_faq_admin"]))
		$GLOBALS["core_faq_admin"]=new CoreFaqAdmin();

	$cfa=& $GLOBALS["core_faq_admin"];
	$cfa->urlManagerSetup("modname=faq&op=main");
}


function faqMain() {
	checkPerm("view");

	$res="";
	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$title=$cfa->lang->def("_FAQ");
	$res.=$cfa->titleArea($title);
	$res.=$cfa->getHead();

	$vis_item=$GLOBALS["cms"]["visuItem"];
	$res.=$cfa->getFaqCategoryTable($vis_item);

	$res.=$cfa->getFooter();
	$out->add($res);
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delcategory]');
}


function addeditFaqCategory($id=0) {
	$res="";
	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	if ($id > 0) {
		$info=$cfa->faqManager->getCategoryInfo($id);
		$title_label=$cfa->lang->def("_EDIT_CATEGORY").": ".$info["title"];
	}
	else {
		$title_label=$cfa->lang->def("_ADD_CATEGORY");
	}

	$um=& UrlManager::getInstance();
	$back_url=$um->getUrl();
	$title[$back_url]=$cfa->lang->def("_FAQ");
	$title[]=$title_label;
	$res.=$cfa->titleArea($title);
	$res.=$cfa->getHead();
	$res.=$cfa->backUi();

	$res.=$cfa->addeditFaqCategory($id);

	$res.=$cfa->backUi();
	$res.=$cfa->getFooter();
	$out->add($res);
}


function saveFaqCategory() {
	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];
	$cfa->saveCategory();
}


function deleteFaqCategory() {
	$res="";

	if ((isset($_GET["catid"])) && ($_GET["catid"] > 0))
		$cat_id=(int)$_GET["catid"];
	else
		return FALSE;

	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];

	$delete_ui_code=$cfa->deleteCategory($cat_id);


	if (!empty($delete_ui_code)) {

		$info=$cfa->faqManager->getCategoryInfo($cat_id);
		$title_label=$cfa->lang->def("_DEL").": ".$info["title"];

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();
		$title[$back_url]=$cfa->lang->def("_FAQ");
		$title[]=$title_label;
		$res.=$cfa->titleArea($title);
		$res.=$cfa->getHead();
		$res.=$cfa->backUi();

		$res.=$delete_ui_code;

		$res.=$cfa->getFooter();
		$out->add($res);
	}
}


function showCatPerm() {
	$res="";

	if ((isset($_GET["catid"])) && ($_GET["catid"] > 0))
		$cat_id=(int)$_GET["catid"];
	else
		return FALSE;

	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];

	$page_content=$cfa->showCatPerm($cat_id);

	if ($page_content !== FALSE) {

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");

		$info=$cfa->faqManager->getCategoryInfo($cat_id);
		$title_label=$cfa->lang->def("_FAQ_CATEGORY_PERMISSIONS").": ".$info["title"];

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();
		$title[$back_url]=$cfa->lang->def("_FAQ");
		$title[]=$title_label;
		$res.=$cfa->titleArea($title);
		$res.=$cfa->getHead();
		//$res.=$cfa->backUi();

		$res.=$page_content;

		//$res.=$cfa->backUi();
		$res.=$cfa->getFooter();
		$out->add($res);
	}

}


function doneCatPerm() {
	include_once($GLOBALS['where_cms']."/lib/lib.reloadperm.php");
	setCmsReloadPerm();

	cfaSetup();

	$um=& UrlManager::getInstance();
	$url=$um->getUrl();
	jumpTo($url);
}


function exportCategory() {

	if ((isset($_GET["catid"])) && ($_GET["catid"] > 0))
		$cat_id=(int)$_GET["catid"];
	else
		return FALSE;

	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];

	$cfa->exportCategory($cat_id);
}


function importCategory() {
	$res="";

	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];

	$import_ui_code=$cfa->importCategory();

	if (!empty($import_ui_code)) {

		$title_label=$cfa->lang->def("_IMPORT_CATEGORY");

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();
		$title[$back_url]=$cfa->lang->def("_FAQ");
		$title[]=$title_label;
		$res.=$cfa->titleArea($title);
		$res.=$cfa->getHead();
		$res.=$cfa->backUi();

		$res.=$import_ui_code;

		$res.=$cfa->getFooter();
		$out->add($res);
	}
}


function showCatItems() {
	$res="";

	if ((isset($_GET["catid"])) && ($_GET["catid"] > 0))
		$cat_id=(int)$_GET["catid"];
	else
		return FALSE;

	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$um=& UrlManager::getInstance();
	$back_url=$um->getUrl();
	$info=$cfa->faqManager->getCategoryInfo($cat_id);
	$title[$back_url]=$cfa->lang->def("_FAQ");
	$title[]=$info["title"];
	$res.=$cfa->titleArea($title);
	$res.=$cfa->getHead();

	$vis_item=$GLOBALS["cms"]["visuItem"];
	$res.=$cfa->showCatItems($cat_id, $vis_item);

	$res.=$cfa->getFooter();
	$out->add($res);
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=deletefaq]');
}



function addeditFaq($id=0) {
	$res="";

	if ((isset($_GET["catid"])) && ($_GET["catid"] > 0))
		$cat_id=(int)$_GET["catid"];
	else
		return FALSE;

	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	if ($id > 0) {
		$info=$cfa->faqManager->getFaqInfo($id);
		$title_label=$cfa->lang->def("_EDIT_FAQ").": ".$info["title"];
	}
	else {
		$title_label=$cfa->lang->def("_ADD_FAQ");
	}

	$um=& UrlManager::getInstance();
	$home_url=$um->getUrl();
	$back_url=$um->getUrl("op=showcat&catid=".$cat_id);
	$title[$home_url]=$cfa->lang->def("_FAQ");
	$info=$cfa->faqManager->getCategoryInfo($cat_id);
	$title[$back_url]=$info["title"];
	$title[]=$title_label;
	$res.=$cfa->titleArea($title);
	$res.=$cfa->getHead();
	$res.=$cfa->backUi($back_url);

	$res.=$cfa->addeditFaq($cat_id, $id);

	$res.=$cfa->backUi($back_url);
	$res.=$cfa->getFooter();
	$out->add($res);
}


function saveFaq() {

	if ((isset($_GET["catid"])) && ($_GET["catid"] > 0))
		$cat_id=(int)$_GET["catid"];
	else
		return FALSE;

	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];
	$cfa->saveFaq($cat_id);
}


function deleteFaq() {
	$res="";

	if ((isset($_GET["catid"])) && ($_GET["catid"] > 0))
		$cat_id=(int)$_GET["catid"];
	else
		return FALSE;

	if ((isset($_GET["faqid"])) && ($_GET["faqid"] > 0))
		$faq_id=(int)$_GET["faqid"];
	else
		return FALSE;

	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];

	$delete_ui_code=$cfa->deleteFaq($cat_id, $faq_id);

	if (!empty($delete_ui_code)) {

		$info=$cfa->faqManager->getFaqInfo($faq_id);
		$title_label=$cfa->lang->def("_DELETE_FAQ").": ".$info["title"];

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");

		$um=& UrlManager::getInstance();
		$home_url=$um->getUrl();
		$back_url=$um->getUrl("op=showcat&catid=".$cat_id);
		$title[$home_url]=$cfa->lang->def("_FAQ");
		$info=$cfa->faqManager->getCategoryInfo($cat_id);
		$title[$back_url]=$info["title"];
		$title[]=$title_label;
		$res.=$cfa->titleArea($title);
		$res.=$cfa->getHead();
		$res.=$cfa->backUi($back_url);

		$res.=$delete_ui_code;

		$res.=$cfa->getFooter();
		$out->add($res);
	}
}


function moveFaq($direction) {

	if ((isset($_GET["catid"])) && ($_GET["catid"] > 0))
		$cat_id=(int)$_GET["catid"];
	else
		return FALSE;

	if ((isset($_GET["faqid"])) && ($_GET["faqid"] > 0))
		$faq_id=(int)$_GET["faqid"];
	else
		return FALSE;

	cfaSetup();
	$cfa=& $GLOBALS["core_faq_admin"];

	$cfa->moveFaq($cat_id, $faq_id, $direction);
}


//---------------------------------------------------------------------------//

function faqDispatch($op) {
	switch($op) {
		case "main" : {
			faqMain();
		} break;
		case "addcategory" : {
			addeditFaqCategory();
		} break;
		case "editcat" : {
			addeditFaqCategory((int)$_GET["catid"]);
		} break;
		case "savecategory" : {
			if (!isset($_POST["undo"]))
				saveFaqCategory();
			else
				faqMain();
		} break;
		case "delcategory" : {
			deleteFaqCategory();
		} break;
		case "setperm" : {
			showCatPerm();
		} break;
		case "doneperm" : {
			doneCatPerm();
		} break;
		case "exportcat" : {
			exportCategory();
		} break;
		case "importcat" : {
			importCategory();
		} break;

		case "showcat": {
			showCatItems();
		} break;
		case "addfaq" : {
			addeditFaq();
		} break;
		case "editfaq" : {
			addeditFaq((int)$_GET["faqid"]);
		} break;
		case "savefaq" : {
			if (!isset($_POST["undo"]))
				saveFaq();
			else
				showCatItems();
		} break;
		case "deletefaq" : {
			deleteFaq();
		} break;
		case "movefaqdown": {
			moveFaq("down");
		} break;
		case "movefaqup": {
			moveFaq("up");
		} break;
	}
}

?>
