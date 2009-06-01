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
if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

// -- Url Manager Setup --
cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=crm&pi=".getPI()."&modname=contacthistory&op=main");
// -----------------------

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS["where_crm"]."/modules/contacthistory/lib.contacthistory.php");


function contacthistory() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("contacthistory", "crm");
	$um=& UrlManager::getInstance();

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_CHISTORY_TABLE_CAPTION");
	$table_summary=$lang->def("_CHISTORY_TABLE_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];

	$contacthistoryman=new ContactHistoryManager();


	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_CHISTORY");
	$res.=getTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$tab=new typeOne($vis_item, $table_caption, $table_summary);


	$base_url="op=setorder&ord=";

	$head=array();
	$img ="<img src=\"".getPathImage()."standard/calendar.gif\" alt=\"".$lang->def("_ALT_CHISTORY")."\" ";
	$img.="title=\"".$lang->def("_ALT_CHISTORY")."\" />";
	$head[]=$img;
	$head[]="<a href=\"".$um->getUrl($base_url."title")."\">".$lang->def("_TITLE")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."company")."\">".$lang->def("_CHISTORY_COMPANY")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."type")."\">".$lang->def("_CHISTORY_TYPE")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."date")."\">".$lang->def("_DATE")."</a>";
	$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;

	/*$img ="<img src=\"".getPathImage()."standard/details.gif\" alt=\"".$lang->def("_ALT_COMPANYDETAILS")."\" ";
	$img.="title=\"".$lang->def("_ALT_COMPANYDETAILS")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;*/

	$head_type=array("image", "", "", "", "", "", "image", "image");


	$res.=printSearchForm();


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$tab->initNavBar('ini', 'link');
	$tab->setLink("index.php");

	$ini=$tab->getSelectedElement();


	$where=getContactHistorySearchQuery();

	$acl_manager=$GLOBALS["current_user"]->getAclManager();
	$user_idst=$GLOBALS["current_user"]->getIdST();
	$roles=$acl_manager->getUserRoleFromPath($user_idst, "/crm/contacthistory", "assigned");

	$level=$GLOBALS["current_user"]->getUserLevelId();
	$is_admin=($level == ADMIN_GROUP_ADMIN ? TRUE : FALSE);
	$is_god_admin=($level == ADMIN_GROUP_GODADMIN ? TRUE : FALSE);

	// If user is not a admin / god admin then apply permission restrictions
	if ((!$is_god_admin) && (!$is_admin)) {
		if ($where !== FALSE)
			$where.=" AND ";
		else
			$where="";

		$where.="((t1.author = '".$GLOBALS["current_user"]->getIdSt()."')";
		if (($roles !== FALSE) && (is_array($roles["role_info"]) && (count($roles["role_info"]) > 0))) {
			$where.=" OR (t1.contact_id IN (".implode(",", $roles["role_info"]).")))";
		}
		else
			$where.=")";

		$where.=" ";
	}

	$list=$contacthistoryman->chdm->getContactHistoryList($ini, $vis_item, $where);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	$type_arr=$contacthistoryman->chdm->getTypeArray();

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["contact_id"];
		$company_id=$list_arr[$i]["company_id"];

		$base_url=$um->getUrl("modname=company&op=details&id=".$company_id);

		$rowcnt=array();

		$img ="<img src=\"".getPathImage()."standard/calendar.gif\" alt=\"".$lang->def("_ALT_CHISTORY")."\" ";
		$img.="title=\"".$lang->def("_ALT_CHISTORY")."\" />";
		$rowcnt[]=$img;

		$show_details=(isset($_SESSION["show_contacthistory_details"][$id]) ? TRUE : FALSE);

		$url_qry="&op=togglecontacthistorydetails&company_id=".$company_id."&contact_id=".$id;
		$short_lbl=substr($list_arr[$i]["title"], 0 , 20)."...";
		$url=$um->getUrl($url_qry);
		if ($show_details) {
			$img ="<img src=\"".getPathImage('fw')."standard/less.gif\" alt=\"".$lang->def("_LESSINFO")." ".$short_lbl."\" ";
			$img.="title=\"".$lang->def("_LESSINFO")." ".$short_lbl."\" />";
		}
		else {
			$img ="<img src=\"".getPathImage('fw')."standard/more.gif\" alt=\"".$lang->def("_MOREINFO")." ".$short_lbl."\" ";
			$img.="title=\"".$lang->def("_MOREINFO")." ".$short_lbl."\" />";
		}
		$rowcnt[]="<a href=\"".$url."\">".$img.$list_arr[$i]["title"]."</a>\n";


		$rowcnt[]=$list_arr[$i]["company_name"];


		$rowcnt[]=$type_arr[$list_arr[$i]["type"]];
		$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["meeting_date"]);


		$url=$base_url."&amp;tab_op=editcontacthistory&amp;contact_id=".$id."&backto=contacthistory";
		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>";

		$tab->addBody($rowcnt);

		if ($show_details) {
			$tab->addBodyExpanded($list_arr[$i]["description"], "line_details");
		}
	}


	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	$res.="</div>\n";
	$out->add($res);
}


function setContactHistoryOrder() {

	$um=& UrlManager::getInstance();
	$contacthistoryman=new ContactHistoryManager();
	$contacthistoryman->chdm->setContactHistoryOrder($_GET["ord"]);

	$back_url=$um->getUrl();
	jumpTo($back_url);
}


function printSearchForm() {
	$res="";

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
	$form=new Form();

	require_once($GLOBALS["where_framework"]."/lib/lib.search.php");
	$search=new SearchUI("contacthistory");

	$um=& UrlManager::getInstance();
	$contacthistoryman=new ContactHistoryManager();
	$lang=& DoceboLanguage::createInstance("contacthistory", "crm");

	if (isset($_POST["do_search"])) {
		$search->setSearchItem("search_key");
		$search->setSearchItem("type");
		$search->setSearchItem("reason");
		$search->setSearchItem("company");
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
	$type=$search->getSearchItem("type", "bool");
	$reason=$search->getSearchItem("reason", "bool");
	$company=$search->getSearchItem("company", "bool");

	$res.=$form->getTextfield($search->lang->def("_SEARCH_KEY"), "search_key", "search_key", 255, $search_key);

	$type_arr=$contacthistoryman->chdm->getTypeArray(TRUE);
	$res.=$form->getDropdown($lang->def("_CHISTORY_TYPE"), "type", "type", $type_arr, $type);

	$reason_arr=$contacthistoryman->chdm->getReasonArray(TRUE);
	$res.=$form->getDropdown($lang->def("_CHISTORY_CONTACT_REASON"), "reason", "reason", $reason_arr, $reason);

	$company_arr=array(); //$contacthistoryman->chdm->getCompanyArray($contacthistoryman->ccManager, TRUE);
	$res.=$form->getDropdown($lang->def("_CHISTORY_COMPANY"), "company", "company", $company_arr, $company);


	// --------------------------------------------------------------------------
	$res.=$search->closeSearchForm($form);

	return $res;
}


function getContactHistorySearchQuery() {
	$res=FALSE;
	$first=TRUE;

	require_once($GLOBALS["where_framework"]."/lib/lib.search.php");
	$search=new SearchUI("contacthistory");

	$search_key=$search->getSearchItem("search_key", "string");
	$type=$search->getSearchItem("type", "bool");
	$complete=$search->getSearchItem("complete", "bool");
	$company=$search->getSearchItem("company", "bool");

	if (!empty($search_key)) {
		$res.=($first ? "" : " AND ");
		$res.="t1.title LIKE '%".$search_key."%'";
		$first=FALSE;
	}

	if ($type > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.type='".$type."'";
		$first=FALSE;
	}

	if ($complete > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.complete='".($complete-1)."'";
		$first=FALSE;
	}

	if ($company > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.company_id='".$company."'";
		$first=FALSE;
	}

	return $res;
}


function showhideContactHistorySearchForm() {

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$um=& UrlManager::getInstance();
	$search=new SearchUI("contacthistory");

	$search->showHideSearchForm();

	$url=$um->getUrl();
	jumpTo($url);
}


function toggleContactHistoryDetails() {
	$ok=TRUE;
	$um=& UrlManager::getInstance();

	if ((isset($_GET["company_id"])) && ($_GET["company_id"] > 0))
		$company_id=$_GET["company_id"];
	else
		$ok=FALSE;

	if ((isset($_GET["contact_id"])) && ($_GET["contact_id"] > 0))
			$contact_id=$_GET["contact_id"];
	else
		$ok=FALSE;

	if ($ok) {
		if (isset($_SESSION["show_contacthistory_details"][$contact_id]))
			unset($_SESSION["show_contacthistory_details"][$contact_id]);
		else
			$_SESSION["show_contacthistory_details"][$contact_id]=1;
	}

	jumpTo($um->getUrl());
}


// ----------------------------------------------------------------------------

$op=(isset($_GET["op"]) ? $_GET["op"] : "main");

switch ($op) {

	case "main": {
		contacthistory();
	} break;

	case "setorder": {
		setContactHistoryOrder();
	} break;

	case "showhidesearchform": {
		showhideContactHistorySearchForm();
	} break;

	case "togglecontacthistorydetails": {
		toggleContactHistoryDetails();
	} break;

}

?>