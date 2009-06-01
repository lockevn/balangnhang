<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2007 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');
if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

require_once($GLOBALS["where_ecom"]."/lib/lib.paramset.php");
// TODO: Check permissions!!

function &psaSetup() {
	$res =new ParamSetAdmin();
	$res->urlManagerSetup("modname=paramset&op=main");
	return $res;
}


function paramsetMain() {
	checkPerm("view");

	$res="";

	$psa =& psaSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$title=$psa->lang->def("_PARAMETERS_SET");
	$res.=$psa->titleArea($title);
	$res.=$psa->getHead();

	$vis_item=$GLOBALS["cms"]["visuItem"];
	$res.=$psa->getParamSetTable($vis_item);

	$res.=$psa->getFooter();
	$out->add($res);
}


function addeditSet($id=0) {
	$res="";

	$psa =&psaSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	if ($id > 0) {
		$info=$psa->pSetManager->getSetInfo($id);
		$title_label=$psa->lang->def("_EDIT_PARAMSET").": ".$info["title"];
	}
	else {
		$title_label=$psa->lang->def("_ADD_PARAMSET");
	}

	$um=& UrlManager::getInstance();
	$back_url=$um->getUrl();
	$title[$back_url]=$psa->lang->def("_PARAMETERS_SET");
	$title[]=$title_label;
	$res.=$psa->titleArea($title);
	$res.=$psa->getHead();
	$res.=$psa->backUi();

	$res.=$psa->addeditSet($id);

	$res.=$psa->backUi();
	$res.=$psa->getFooter();
	$out->add($res);
}


function saveParamSet() {
	$psa =&psaSetup();
	$psa->saveSet();
}


function deleteParamSet() {
	$res="";

	if ((isset($_GET["set_id"])) && ($_GET["set_id"] > 0))
		$set_id=(int)$_GET["set_id"];
	else
		return FALSE;


	$psa =&psaSetup();

	$delete_ui_code=$psa->deleteSet($set_id);

	if (!empty($delete_ui_code)) {

		$info=$psa->pSetManager->getSetInfo($set_id);
		$title_label=$psa->lang->def("_DELETE_PARAMSET").": ".$info["title"];

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();
		$title[$back_url]=$psa->lang->def("_PARAMETERS_SET");
		$title[]=$title_label;
		$res.=$psa->titleArea($title);
		$res.=$psa->getHead();
		$res.=$psa->backUi();

		$res.=$delete_ui_code;

		$res.=$psa->getFooter();
		$out->add($res);
	}
}


function showFieldGroups() {
	$res="";

	if ((isset($_GET["set_id"])) && ($_GET["set_id"] > 0))
		$set_id=(int)$_GET["set_id"];
	else
		return FALSE;


	$psa =&psaSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$um=& UrlManager::getInstance();
	$back_url=$um->getUrl();
	$info=$psa->pSetManager->getSetInfo($set_id);
	$title[$back_url]=$psa->lang->def("_PARAMETERS_SET");
	$title[]=$info["title"];
	$res.=$psa->titleArea($title);
	$res.=$psa->getHead();

	$vis_item=20; //$GLOBALS["ecom"]["visuItem"];
	$res.=$psa->showFieldGroups($set_id, $vis_item);

	$res.=$psa->getFooter();
	$out->add($res);
}



function addeditFieldGroup($id=0) {
	$res="";

	if ((isset($_GET["set_id"])) && ($_GET["set_id"] > 0))
		$set_id=(int)$_GET["set_id"];
	else
		return FALSE;


	$psa =&psaSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	if ($id > 0) {
		$info=$psa->pSetManager->getFieldGroupInfo($id);
		$title_label=$psa->lang->def("_EDIT_FIELDGRP").": ".$info["title"];
	}
	else {
		$title_label=$psa->lang->def("_ADD_FIELDGRP");
	}

	$um=& UrlManager::getInstance();
	$home_url=$um->getUrl();
	$back_url=$um->getUrl("op=showset&set_id=".$set_id);
	$title[$home_url]=$psa->lang->def("_PARAMETERS_SET");
	$info=$psa->pSetManager->getSetInfo($set_id);
	$title[$back_url]=$info["title"];
	$title[]=$title_label;
	$res.=$psa->titleArea($title);
	$res.=$psa->getHead();
	$res.=$psa->backUi($back_url);

	$res.=$psa->addeditFieldGroup($set_id, $id);

	$res.=$psa->backUi($back_url);
	$res.=$psa->getFooter();
	$out->add($res);
}


function saveFieldGroup() {

	if ((isset($_GET["set_id"])) && ($_GET["set_id"] > 0))
		$set_id=(int)$_GET["set_id"];
	else
		return FALSE;


	$psa =&psaSetup();
	$psa->saveFieldGroup($set_id);
}


function deleteFieldGroup() {
	$res="";

	if ((isset($_GET["set_id"])) && ($_GET["set_id"] > 0))
		$set_id=(int)$_GET["set_id"];
	else
		return FALSE;

	if ((isset($_GET["fieldgrp_id"])) && ($_GET["fieldgrp_id"] > 0))
		$fieldgrp_id=(int)$_GET["fieldgrp_id"];
	else
		return FALSE;


	$psa =&psaSetup();

	$delete_ui_code=$psa->deleteFieldGroup($set_id, $fieldgrp_id);

	if (!empty($delete_ui_code)) {

		$info=$psa->pSetManager->getFieldGroupInfo($fieldgrp_id);
		$title_label=$psa->lang->def("_DELETE_FIELDGRP").": ".$info["title"];

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");

		$um=& UrlManager::getInstance();
		$home_url=$um->getUrl();
		$back_url=$um->getUrl("op=showset&set_id=".$set_id);
		$title[$home_url]=$psa->lang->def("_PARAMETERS_SET");
		$info=$psa->pSetManager->getSetInfo($set_id);
		$title[$back_url]=$info["title"];
		$title[]=$title_label;
		$res.=$psa->titleArea($title);
		$res.=$psa->getHead();
		$res.=$psa->backUi($back_url);

		$res.=$delete_ui_code;

		$res.=$psa->getFooter();
		$out->add($res);
	}
}


function moveFieldGroup($direction) {

	if ((isset($_GET["fieldgrp_id"])) && ($_GET["fieldgrp_id"] > 0))
		$fieldgrp_id=(int)$_GET["fieldgrp_id"];
	else
		return FALSE;

	if ((isset($_GET["set_id"])) && ($_GET["set_id"] > 0))
		$set_id=(int)$_GET["set_id"];
	else
		return FALSE;

	$psa =&psaSetup();
	$psa->moveFieldGroup($fieldgrp_id, $set_id, $direction);
}


function showGroupItems() {
	$res="";

	if ((isset($_GET["fieldgrp_id"])) && ($_GET["fieldgrp_id"] > 0))
		$fieldgrp_id=(int)$_GET["fieldgrp_id"];
	else
		return FALSE;

	if ((isset($_GET["set_id"])) && ($_GET["set_id"] > 0))
		$set_id=(int)$_GET["set_id"];
	else
		return FALSE;


	$psa =&psaSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$um=& UrlManager::getInstance();
	$back_url=$um->getUrl();
	$title[$back_url]=$psa->lang->def("_PARAMETERS_SET");
	$info=$psa->pSetManager->getSetInfo($set_id);
	$grp_url =$um->getUrl("op=showset&set_id=".$set_id);
	$title[$grp_url]=$info["title"];
	$info=$psa->pSetManager->getFieldGroupInfo($fieldgrp_id);
	$title[]=$info["title"];
	$res.=$psa->titleArea($title);
	$res.=$psa->getHead();

	$vis_item=$GLOBALS["cms"]["visuItem"];
	$res.=$psa->showGroupItems($fieldgrp_id, $set_id, $vis_item);

	$res.=$psa->getFooter();
	$out->add($res);
}


function groupAddRemFields() {
	$res="";

	if ((isset($_GET["fieldgrp_id"])) && ($_GET["fieldgrp_id"] > 0))
		$fieldgrp_id=(int)$_GET["fieldgrp_id"];
	else
		return FALSE;

	if ((isset($_GET["set_id"])) && ($_GET["set_id"] > 0))
		$set_id=(int)$_GET["set_id"];
	else
		return FALSE;


	$psa =&psaSetup();
	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$um=& UrlManager::getInstance();
	$back_url=$um->getUrl();
	$title[$back_url]=$psa->lang->def("_PARAMETERS_SET");
	$info=$psa->pSetManager->getSetInfo($set_id);
	$grp_url =$um->getUrl("op=showset&set_id=".$set_id);
	$title[$grp_url]=$info["title"];
	$info=$psa->pSetManager->getFieldGroupInfo($fieldgrp_id);
	$fields_url =$um->getUrl("op=showgrpitems&set_id=".$set_id."&fieldgrp_id=".$fieldgrp_id);
	$title[$fields_url]=$info["title"];
	$title[]=$psa->lang->def("_ADDREM_FIELDS");
	$res.=$psa->titleArea($title);
	$res.=$psa->getHead();

	$res.=$psa->groupAddRemFields($fieldgrp_id, $set_id);

	$res.=$psa->getFooter();
	$out->add($res);
}


function saveGroupItems() {

	if ((isset($_GET["fieldgrp_id"])) && ($_GET["fieldgrp_id"] > 0))
		$fieldgrp_id=(int)$_GET["fieldgrp_id"];
	else
		return FALSE;

	if ((isset($_GET["set_id"])) && ($_GET["set_id"] > 0))
		$set_id=(int)$_GET["set_id"];
	else
		return FALSE;

	$psa =&psaSetup();
	$psa->saveGroupItems($fieldgrp_id, $set_id);
}


function moveGroupItem($direction) {

	if ((isset($_GET["fieldgrp_id"])) && ($_GET["fieldgrp_id"] > 0))
		$fieldgrp_id=(int)$_GET["fieldgrp_id"];
	else
		return FALSE;

	if ((isset($_GET["set_id"])) && ($_GET["set_id"] > 0))
		$set_id=(int)$_GET["set_id"];
	else
		return FALSE;

	if ((isset($_GET["item_id"])) && ($_GET["item_id"] > 0))
		$item_id =(int)$_GET["item_id"];
	else
		return FALSE;

	$psa =&psaSetup();
	$psa->moveGroupItem($fieldgrp_id, $set_id, $item_id, $direction);
}


function switchItemCompulsoryStatus() {

	if ((isset($_GET["fieldgrp_id"])) && ($_GET["fieldgrp_id"] > 0))
		$fieldgrp_id=(int)$_GET["fieldgrp_id"];
	else
		return FALSE;

	if ((isset($_GET["set_id"])) && ($_GET["set_id"] > 0))
		$set_id=(int)$_GET["set_id"];
	else
		return FALSE;

	if ((isset($_GET["item_id"])) && ($_GET["item_id"] > 0))
		$item_id =(int)$_GET["item_id"];
	else
		return FALSE;

	if ((isset($_GET["cur"])) && ($_GET["cur"] > 0))
		$current =(int)$_GET["cur"];
	else
		$current =0;

	$psa =&psaSetup();
	$psa->switchItemCompulsoryStatus($fieldgrp_id, $set_id, $item_id, $current);
}


//---------------------------------------------------------------------------//

function paramsetDispatch($op) {
	switch($op) {
		case "main" : {
			paramsetMain();
		} break;
		case "addset" : {
			addeditSet();
		} break;
		case "editset" : {
			addeditSet((int)$_GET["set_id"]);
		} break;
		case "saveset" : {
			if (!isset($_POST["undo"]))
				saveParamSet();
			else
				paramsetMain();
		} break;
		case "delset" : {
			deleteParamSet();
		} break;

		case "showset": {
			showFieldGroups();
		} break;
		case "addfieldgrp" : {
			addeditFieldGroup();
		} break;
		case "editfieldgrp" : {
			addeditFieldGroup((int)$_GET["fieldgrp_id"]);
		} break;
		case "savefieldgrp" : {
			if (!isset($_POST["undo"]))
				saveFieldGroup();
			else
				showCatItems();
		} break;
		case "deletefieldgrp" : {
			deleteFieldGroup();
		} break;
		case "movefieldgrpdown": {
			moveFieldGroup("down");
		} break;
		case "movefieldgrpup": {
			moveFieldGroup("up");
		} break;

		case "showgrpitems": {
			showGroupItems();
		} break;
		case "addremfields": {
			groupAddRemFields();
		} break;
		case "savegrpitems" : {
			if (!isset($_POST["undo"]))
				saveGroupItems();
			else
				showGroupItems();
		} break;
		case "movegrpitemdown": {
			moveGroupItem("down");
		} break;
		case "movegrpitemup": {
			moveGroupItem("up");
		} break;
		case "switchitemcompstatus": {
			switchItemCompulsoryStatus();
		} break;
	}
}

?>
