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
 * @version  $Id: $
 */
// ----------------------------------------------------------------------------
if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

// -- Url Manager Setup --
cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=crm&pi=".getPI()."&modname=task&op=main");
// -----------------------

require_once($GLOBALS["where_crm"]."/modules/task/lib.task.php");


function task() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("task", "crm");
	$company_lang=& DoceboLanguage::createInstance("company", "framework");

	$um=& $GLOBALS["url_manager"];

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_TASK_TABLE_CAPTION");
	$table_summary=$lang->def("_TASK_TABLE_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];

	$ptm=new ProjectTaskManager();


	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_TASKS");
	$res.=getTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$tab=new typeOne($vis_item, $table_caption, $table_summary);


	$base_url="op=setorder&ord=";

	$head=array();
	$img ="<img src=\"".getPathImage()."standard/project.gif\" alt=\"".$lang->def("_ALT_TASK")."\" ";
	$img.="title=\"".$lang->def("_ALT_TASK")."\" />";
	$head[]=$img;
	$head[]="<a href=\"".$um->getUrl($base_url."name")."\">".$lang->def("_TASK_NAME")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."company")."\">".$lang->def("_TASK_COMPANY")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."project")."\">".$lang->def("_TASK_PROJECT")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."priority")."\">".$lang->def("_TASK_PRIORITY")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."status")."\">".$lang->def("_STATUS")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."date")."\">".$lang->def("_TASK_EXPIRYDATE")."</a>";
	$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;


	$head_type=array("image", "", "", "", "", "", "image", "image");


	$res.=printSearchForm();


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$tab->initNavBar('ini', 'link');
	$tab->setLink($um->getUrl());

	$ini=$tab->getSelectedElement();


	$where=getTaskSearchQuery();

	$list=$ptm->cm->getAllTasksList($ini, $vis_item, $where);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	$status_arr=$ptm->cm->getStatusArray($company_lang);
	$priority_arr=$ptm->cm->getPriorityArray($company_lang);

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["task_id"];
		$company_id=$list_arr[$i]["company_id"];
		$prj_id=$list_arr[$i]["prj_id"];

		$base_url=$um->getUrl("modname=company&op=details&id=".$company_id);

		$rowcnt=array();

		$img ="<img src=\"".getPathImage()."standard/project.gif\" alt=\"".$lang->def("_ALT_TASK")."\" ";
		$img.="title=\"".$lang->def("_ALT_TASK")."\" />";
		$rowcnt[]=$img;

		$url=$base_url."&tab_op=opentask&prj_id=".$prj_id."&task_id=".$id."&backto=task";

		$rowcnt[]="<a href=\"".$url."\">".$list_arr[$i]["name"]."</a>\n";


		$rowcnt[]=$list_arr[$i]["company_name"];
		$rowcnt[]=$list_arr[$i]["prj_name"];

		$rowcnt[]=$priority_arr[$list_arr[$i]["priority"]];
		$rowcnt[]=$status_arr[$list_arr[$i]["status"]];
		$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["expire"], "date");


		$url=$base_url."&tab_op=edittask&task_id=".$id."&prj_id=".$prj_id."&backto=task";
		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>";

		$tab->addBody($rowcnt);
	}


	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	$res.="</div>\n";
	$out->add($res);
}


function setTaskOrder() {

	$um=& $GLOBALS["url_manager"];
	$ptm=new ProjectTaskManager();

	if ((isset($_GET["ord"])) && (!empty($_GET["ord"])))
		$ptm->cm->setTaskOrder($_GET["ord"]);

	$url=$um->getUrl();
	jumpTo($url);
}


function printSearchForm() {
	$res="";

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
	$form=new Form();

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("task");

	$ptm=new ProjectTaskManager();
	$lang=& DoceboLanguage::createInstance("task", "crm");
	$company_lang=& DoceboLanguage::createInstance("company", "framework");

	$um=& $GLOBALS["url_manager"];

	if (isset($_POST["do_search"])) {
		$search->setSearchItem("search_key");
		$search->setSearchItem("priority");
		$search->setSearchItem("status");
		//$search->setSearchItem("openclose");
		$search->setSearchItem("company");
		$search->setSearchItem("project");
	}


	if ($search->getShowSearchForm()) {
		$hide_form=FALSE;
		$label=$search->lang->def("_HIDE_SEARCH_FORM");
		$class="hide_form";
	}
	else {
		$hide_form=TRUE;
		$label=$search->lang->def("_SHOW_SEARCH_FORM");
		$class="show_form";
	}


	$url=$um->getUrl("op=showhidesearchform");
	$res.="<div class=\"search_form ".$class."\">";
	$res.="<a href=\"".$url."\">".$label."</a></div>\n";


	if ($hide_form) {
		return $res;
	}

	$res.=$search->openSearchForm($form, $um->getUrl());
	// --------------------------------------------------------------------------





	$search_key=$search->getSearchItem("search_key", "string");
	$priority=$search->getSearchItem("priority", "bool");
	$status=$search->getSearchItem("status", "bool");
	//$openclose=$search->getSearchItem("openclose", "bool");
	$company=$search->getSearchItem("company", "bool");
	$project=$search->getSearchItem("project", "bool");

	$res.=$form->getTextfield($search->lang->def("_SEARCH_KEY"), "search_key", "search_key", 255, $search_key);

	$priority_arr=$ptm->cm->getPriorityArray($company_lang, TRUE);
	$res.=$form->getDropdown($lang->def("_TASK_PRIORITY"), "priority", "priority", $priority_arr, $priority);

	$status_arr=$ptm->cm->getStatusArray($company_lang, TRUE);
	$res.=$form->getDropdown($lang->def("_STATUS"), "status", "status", $status_arr, $status);

/*	$openclose_arr=array();
	$openclose_arr[0]=$lang->def("_ANY");
	$openclose_arr[1]=$lang->def("_ONLY_OPEN");
	$openclose_arr[2]=$lang->def("_ONLY_CLOSED");
	$res.=$form->getDropdown($lang->def("_TASK_OPENCLOSE"), "openclose", "openclose", $openclose_arr, $openclose); */

	$company_arr=$ptm->getCompanyArray(TRUE);
	$res.=$form->getDropdown($lang->def("_TASK_COMPANY"), "company", "company", $company_arr, $company);

	if ($company > 0) {
		$project_arr=$ptm->getProjectArray($company, TRUE);
		$res.=$form->getDropdown($lang->def("_TASK_PROJECT"), "project", "project", $project_arr, $project);
	}
	else {
		$res.=$form->getLineBox($lang->def("_TASK_PROJECT"), $lang->def("_SELECT_A_COMPANY"));
	}

	// --------------------------------------------------------------------------
	$res.=$search->closeSearchForm($form);

	return $res;
}


function getTaskSearchQuery() {
	$res=FALSE;
	$first=TRUE;

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("task");

	$search_key=$search->getSearchItem("search_key", "string");
	$priority=$search->getSearchItem("priority", "bool");
	$status=$search->getSearchItem("status", "bool");
//	$openclose=$search->getSearchItem("openclose", "bool");
	$company=$search->getSearchItem("company", "bool");
	$project=$search->getSearchItem("project", "bool");

	if (!empty($search_key)) {
		$res.=($first ? "" : " AND ");
		$res.="t1.name LIKE '%".$search_key."%'";
		$first=FALSE;
	}

	if ($priority > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.priority='".$priority."'";
		$first=FALSE;
	}

	if ($status > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.status='".$status."'";
		$first=FALSE;
	}

/*	if ($openclose > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.closed='".($openclose-1)."'";
		$first=FALSE;
	} */

	if ($company > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.company_id='".$company."'";
		$first=FALSE;
	}

	if ($project > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.prj_id='".$project."'";
		$first=FALSE;
	}

	return $res;
}


function showhideTaskSearchForm() {

	$um=& $GLOBALS["url_manager"];
	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("task");

	$search->showHideSearchForm();

	$url=$um->getUrl();
	jumpTo($url);
}



// ----------------------------------------------------------------------------

$op=(isset($_GET["op"]) ? $_GET["op"] : "main");

switch ($op) {

	case "main": {
		task();
	} break;

	case "setorder": {
		setTaskOrder();
	} break;

	case "showhidesearchform": {
		showhideTaskSearchForm();
	} break;

}

?>