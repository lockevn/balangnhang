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
cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=crm&pi=".getPI()."&modname=ticket&op=main");
// -----------------------

require_once($GLOBALS["where_crm"]."/modules/ticket/lib.ticket.php");


function ticket() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("ticket", "crm");
	$um=& $GLOBALS["url_manager"];

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_TICKET_TABLE_CAPTION");
	$table_summary=$lang->def("_TICKET_TABLE_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];

	$ctm=new CustomerTicketManager();


	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_TICKETS");
	$res.=getTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$tab=new typeOne($vis_item, $table_caption, $table_summary);


	$base_url="op=setorder&ord=";

	$head=array();
	$img ="<img src=\"".getPathImage()."ticket/tickets.gif\" alt=\"".$lang->def("_ALT_TICKET")."\" ";
	$img.="title=\"".$lang->def("_ALT_TICKET")."\" />";
	$head[]=$img;
	$head[]="<a href=\"".$um->getUrl($base_url."title")."\">".$lang->def("_TICKET_MESSAGE")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."company")."\">".$lang->def("_TICKET_COMPANY")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."project")."\">".$lang->def("_TICKET_PROJECT")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."status")."\">".$lang->def("_STATUS")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."date")."\">".$lang->def("_DATE")."</a>";
	
	$img ="<img src=\"".getPathImage()."ticket/locked.gif\" alt=\"".$lang->def("_ALT_LOCKUNLOCK")."\" ";
	$img.="title=\"".$lang->def("_ALT_LOCKUNLOCK")."\" />";
	$ord_url=$um->getUrl($base_url."&ord=closed");
	$head[]="<a href=\"".$ord_url."\">".$img."</a>";
	$head_type[]="image";	
	
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


	$where=getTicketSearchQuery();

	$acl_manager=$GLOBALS["current_user"]->getAclManager();
	$user_idst=$GLOBALS["current_user"]->getIdST();
	$roles=$acl_manager->getUserRoleFromPath($user_idst, "/crm/ticket", "assigned");

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
			$where.=" OR (t1.ticket_id IN (".implode(",", $roles["role_info"]).")))";
		}
		else
			$where.=")";

		$where.=" ";
	}

	$list=$ctm->tm->getTicketList($ini, $vis_item, $where);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	$status_list=$ctm->tm->getTicketStatusList();
	$status_arr=$status_list["list"];

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["ticket_id"];
		$company_id=$list_arr[$i]["company_id"];

		$base_url=$um->getUrl("modname=company&op=details&id=".$company_id);

		$rowcnt=array();

		$img ="<img src=\"".getPathImage()."ticket/tickets.gif\" alt=\"".$lang->def("_ALT_TICKET")."\" ";
		$img.="title=\"".$lang->def("_ALT_TICKET")."\" />";
		$rowcnt[]=$img;

		$url=$base_url."&amp;tab_op=showticket&amp;ticket_id=".$id."&backto=ticket";

		$rowcnt[]="<a href=\"".$url."\">".$list_arr[$i]["subject"]."</a>\n";


		$rowcnt[]=$list_arr[$i]["company_name"];
		$rowcnt[]=$list_arr[$i]["prj_name"];

		$rowcnt[]=$status_arr[$list_arr[$i]["status"]];
		$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["post_date"]);


		$locked_msg=$lang->def("_UNLOCK_TICKET");
		$unlocked_msg=$lang->def("_LOCK_TICKET");


		if ($list_arr[$i]["closed"]) {
			$lock_img ="<img src=\"".getPathImage()."ticket/locked.gif\" alt=\"".$locked_msg."\" ";
			$lock_img.="title=\"".$locked_msg."\" />";
		}
		else {
			$lock_img ="<img src=\"".getPathImage()."ticket/unlocked.gif\" alt=\"".$unlocked_msg."\" ";
			$lock_img.="title=\"".$unlocked_msg."\" />";
		}

			
		$url=$um->getUrl("modname=company&op=details&tab_op=switchticketlock&id=".$company_id."&ticket_id=".$id."&backto=ticket");
		$rowcnt[]="<a href=\"".$url."\">".$lock_img."</a>";

		$url=$base_url."&amp;tab_op=editticket&amp;ticket_id=".$id."&amp;backto=ticket";
		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>";


		$tab->addBody($rowcnt);
	}


	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	$res.="</div>\n";
	$out->add($res);
}


function setTicketOrder() {

	$um=& $GLOBALS["url_manager"];
	$ctm=new CustomerTicketManager();
	$ctm->tm->setTicketOrder($_GET["ord"]);

	$back_url=$um->getUrl();
	jumpTo($back_url);
}


function printSearchForm() {
	$res="";

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
	$form=new Form();

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("ticket");

	$um=& $GLOBALS["url_manager"];
	$ctm=new CustomerTicketManager();
	$lang=& DoceboLanguage::createInstance("ticket", "crm");

	if (isset($_POST["do_search"])) {
		$search->setSearchItem("search_key");
		$search->setSearchItem("status");
		$search->setSearchItem("openclose");
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
	$status=$search->getSearchItem("status", "bool");
	$openclose=$search->getSearchItem("openclose", "bool");
	$company=$search->getSearchItem("company", "bool");
	$project=$search->getSearchItem("project", "bool");

	$res.=$form->getTextfield($search->lang->def("_SEARCH_KEY"), "search_key", "search_key", 255, $search_key);

	$status_list=$ctm->tm->getTicketStatusList(TRUE);
	$status_arr=$status_list["list"];
	$res.=$form->getDropdown($lang->def("_STATUS"), "status", "status", $status_arr, $status);

	$openclose_arr=array();
	$openclose_arr[0]=$lang->def("_ANY");
	$openclose_arr[1]=$lang->def("_ONLY_OPEN");
	$openclose_arr[2]=$lang->def("_ONLY_CLOSED");
	$res.=$form->getDropdown($lang->def("_TICKET_OPENCLOSE"), "openclose", "openclose", $openclose_arr, $openclose);

	$company_arr=$ctm->tm->getCompanyArray($ctm->ccManager, TRUE);
	$res.=$form->getDropdown($lang->def("_TICKET_COMPANY"), "company", "company", $company_arr, $company);


	if ($company > 0) {
		$project_arr=$ctm->tm->getProjectArray($company, FALSE, TRUE);
		$res.=$form->getDropdown($lang->def("_TICKET_PROJECT"), "project", "project", $project_arr, $project);
	}
	else {
		$res.=$form->getLineBox($lang->def("_TICKET_PROJECT"), $lang->def("_SELECT_A_COMPANY"));
	}


	// --------------------------------------------------------------------------
	$res.=$search->closeSearchForm($form);

	return $res;
}


function getTicketSearchQuery() {
	$res=FALSE;
	$first=TRUE;

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("ticket");

	$search_key=$search->getSearchItem("search_key", "string");
	$status=$search->getSearchItem("status", "bool");
	$openclose=$search->getSearchItem("openclose", "bool");
	$company=$search->getSearchItem("company", "bool");
	$project=$search->getSearchItem("project", "bool");

	if (!empty($search_key)) {
		$res.=($first ? "" : " AND ");
		$res.="t1.subject LIKE '%".$search_key."%'";
		$first=FALSE;
	}

	if ($status > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.status='".$status."'";
		$first=FALSE;
	}

	if ($openclose > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.closed='".($openclose-1)."'";
		$first=FALSE;
	}

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


function showhideTicketSearchForm() {

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$um=& $GLOBALS["url_manager"];
	$search=new SearchUI("ticket");

	$search->showHideSearchForm();

	$url=$um->getUrl();
	jumpTo($url);
}


// ----------------------------------------------------------------------------

$op=(isset($_GET["op"]) ? $_GET["op"] : "main");

switch ($op) {

	case "main": {
		ticket();
	} break;

	case "setorder": {
		setTicketOrder();
	} break;

	case "showhidesearchform": {
		showhideTicketSearchForm();
	} break;

}

?>