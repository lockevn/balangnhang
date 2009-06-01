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

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @version  $Id: $
 */
// ----------------------------------------------------------------------------
if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

// -- Url Manager Setup --
cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=crm&pi=".getPI()."&modname=todo&op=main");
// -----------------------

require_once($GLOBALS["where_crm"]."/modules/todo/lib.todo.php");


function todo() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("todo", "crm");
	$um=& $GLOBALS["url_manager"];

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_TODO_TABLE_CAPTION");
	$table_summary=$lang->def("_TODO_TABLE_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];

	$todoman=new TodoManager();


	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_TODO");
	$res.=getTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$tab=new typeOne($vis_item, $table_caption, $table_summary);


	$base_url="op=setorder&ord=";

	$head=array();
	$img ="<img src=\"".getPathImage()."todo/todo.gif\" alt=\"".$lang->def("_ALT_TODO")."\" ";
	$img.="title=\"".$lang->def("_ALT_TODO")."\" />";
	$head[]=$img;
	$head[]="<a href=\"".$um->getUrl($base_url."title")."\">".$lang->def("_TODO_ACTION")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."company")."\">".$lang->def("_TODO_COMPANY")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."priority")."\">".$lang->def("_TODO_PRIORITY")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."status")."\">".$lang->def("_STATUS")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."date")."\">".$lang->def("_TODO_END_DATE")."</a>";
	
	// Complete / incomplete
	$img ="<img src=\"".getPathImage()."todo/complete.gif\" alt=\"".$lang->def("_TODO_COMPLETE_STATUS")."\" ";
	$img.="title=\"".$lang->def("_TODO_COMPLETE_STATUS")."\" />";
	$ord_url=$um->getUrl($base_url."closed");
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


	$where=getTodoSearchQuery();

	$acl_manager=$GLOBALS["current_user"]->getAclManager();
	$user_idst=$GLOBALS["current_user"]->getIdST();
	$roles=$acl_manager->getUserRoleFromPath($user_idst, "/crm/todo", "assigned");

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
			$where.=" OR (t1.todo_id IN (".implode(",", $roles["role_info"]).")))";
		}
		else
			$where.=")";

		$where.=" ";
	} echo $where;

	$list=$todoman->tdm->getTodoList($ini, $vis_item, $where);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	$status_arr=$todoman->tdm->getStatusArray();
	$priority_arr=$todoman->tdm->getPriorityArray();

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["todo_id"];
		$company_id=$list_arr[$i]["company_id"];

		$base_url=$um->getUrl("modname=company&op=details&id=".$company_id);

		$rowcnt=array();

		$img ="<img src=\"".getPathImage()."todo/todo.gif\" alt=\"".$lang->def("_ALT_TODO")."\" ";
		$img.="title=\"".$lang->def("_ALT_TODO")."\" />";
		$rowcnt[]=$img;

		$show_details=(isset($_SESSION["show_todo_details"][$id]) ? TRUE : FALSE);

		$url_qry="&op=toggletododetails&company_id=".$company_id."&todo_id=".$id;
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
		

		$rowcnt[]=$priority_arr[$list_arr[$i]["priority"]];
		$rowcnt[]=$status_arr[$list_arr[$i]["status"]];
		$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["end_date"]);


		$locked_msg=$lang->def("_MARK_INCOMPLETE");
		$unlocked_msg=$lang->def("_MARK_COMPLETE");
		
		if ($list_arr[$i]["complete"]) {
			$lock_img ="<img src=\"".getPathImage()."todo/complete.gif\" alt=\"".$locked_msg."\" ";
			$lock_img.="title=\"".$locked_msg."\" />";
		}
		else {
			$lock_img ="<img src=\"".getPathImage()."todo/incomplete.gif\" alt=\"".$unlocked_msg."\" ";
			$lock_img.="title=\"".$unlocked_msg."\" />";
		}		
		
		$url=$um->getUrl("op=switchtodocomplete&todo_id=".$id);
		$rowcnt[]="<a href=\"".$url."\">".$lock_img."</a>";		

		$url=$base_url."&amp;tab_op=edittodo&amp;todo_id=".$id."&backto=todo";
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


function setTodoOrder() {

	$um=& $GLOBALS["url_manager"];
	$todoman=new TodoManager();
	$todoman->tdm->setTodoOrder($_GET["ord"]);

	$back_url=$um->getUrl();
	jumpTo($back_url);
}


function printSearchForm() {
	$res="";

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
	$form=new Form();

	require_once($GLOBALS["where_framework"]."/lib/lib.search.php");
	$search=new SearchUI("todo");

	$um=& $GLOBALS["url_manager"];
	$todoman=new TodoManager();
	$lang=& DoceboLanguage::createInstance("todo", "crm");

	if (isset($_POST["do_search"])) {
		$search->setSearchItem("search_key");
		$search->setSearchItem("status");
		$search->setSearchItem("priority");		
		$search->setSearchItem("complete");
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
	$status=$search->getSearchItem("status", "bool");
	$priority=$search->getSearchItem("priority", "bool");
	$openclose=$search->getSearchItem("complete", "bool");
	$company=$search->getSearchItem("company", "bool");

	$res.=$form->getTextfield($search->lang->def("_SEARCH_KEY"), "search_key", "search_key", 255, $search_key);

	$status_arr=$todoman->tdm->getStatusArray(TRUE);
	$res.=$form->getDropdown($lang->def("_STATUS"), "status", "status", $status_arr, $status);

	$priority_arr=$todoman->tdm->getPriorityArray(TRUE);
	$res.=$form->getDropdown($lang->def("_TODO_PRIORITY"), "priority", "priority", $priority_arr, $priority);

	$openclose_arr=array();
	$openclose_arr[0]=$lang->def("_ANY", "search", "framework");
	$openclose_arr[1]=$lang->def("_ONLY_INCOMPLETE");
	$openclose_arr[2]=$lang->def("_ONLY_COMPLETE");
	$res.=$form->getDropdown($lang->def("_TODO_COMPLETE_STATUS"), "complete", "complete", $openclose_arr, $openclose);

	$company_arr=array(); //$todoman->tdm->getCompanyArray($todoman->ccManager, TRUE);
	$res.=$form->getDropdown($lang->def("_TODO_COMPANY"), "company", "company", $company_arr, $company);


	// --------------------------------------------------------------------------
	$res.=$search->closeSearchForm($form);

	return $res;
}


function getTodoSearchQuery() {
	$res=FALSE;
	$first=TRUE;

	require_once($GLOBALS["where_framework"]."/lib/lib.search.php");
	$search=new SearchUI("todo");

	$search_key=$search->getSearchItem("search_key", "string");
	$status=$search->getSearchItem("status", "bool");
	$priority=$search->getSearchItem("priority", "bool");
	$complete=$search->getSearchItem("complete", "bool");
	$company=$search->getSearchItem("company", "bool");

	if (!empty($search_key)) {
		$res.=($first ? "" : " AND ");
		$res.="t1.title LIKE '%".$search_key."%'";
		$first=FALSE;
	}

	if ($status > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.status='".$status."'";
		$first=FALSE;
	}
	
	if ($priority > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.priority='".$priority."'";
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


function showhideTodoSearchForm() {

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$um=& $GLOBALS["url_manager"];
	$search=new SearchUI("todo");

	$search->showHideSearchForm();

	$url=$um->getUrl();
	jumpTo($url);
}


function toggleTodoDetails() {
	$ok=TRUE;
	$um=& UrlManager::getInstance();
	
	if ((isset($_GET["company_id"])) && ($_GET["company_id"] > 0))
		$company_id=$_GET["company_id"];
	else
		$ok=FALSE;
	
	if ((isset($_GET["todo_id"])) && ($_GET["todo_id"] > 0))		
			$todo_id=$_GET["todo_id"];
	else
		$ok=FALSE;
	
	if ($ok) {
		if (isset($_SESSION["show_todo_details"][$todo_id]))
			unset($_SESSION["show_todo_details"][$todo_id]);
		else
			$_SESSION["show_todo_details"][$todo_id]=1;	
	}
	
	jumpTo($um->getUrl());
}


function switchTodoComplete() {
			
			
		$um=& UrlManager::getInstance();	
		$back_url=$um->getUrl("", FALSE);
		
		if ((isset($_GET["todo_id"])) && ($_GET["todo_id"] > 0)) {
			$todoman=new TodoManager();
			
			$todoman->tdm->switchTodoComplete($_GET["todo_id"]);
		}		

		jumpTo($back_url);
}



// ----------------------------------------------------------------------------

$op=(isset($_GET["op"]) ? $_GET["op"] : "main");

switch ($op) {

	case "main": {
		todo();
	} break;

	case "setorder": {
		setTodoOrder();
	} break;

	case "showhidesearchform": {
		showhideTodoSearchForm();
	} break;
	
	case "toggletododetails": {
		toggleTodoDetails();
	} break;
	
	case "switchtodocomplete": {
		switchTodoComplete();		
	} break;

}

?>