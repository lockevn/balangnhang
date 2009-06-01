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

// -- Url Manager Setup --
if ((isset($_GET["backto"])) && (!empty($_GET["backto"])))
	$extra="&backto=".$_GET["backto"];
else
	$extra="";

cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=crm&pi=".getPI()."&modname=company&op=main".$extra);
// -----------------------

require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");


function company() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.field.php");
	$table_caption=$lang->def("_COMPANY_TAB_CAPTION");
	$table_summary=$lang->def("_COMPANY_TAB_SUMMARY");

	$cm =new CompanyManager();
	$fl =new FieldList();
	$fl->setGroupFieldsTable($cm->ccManager->getCompanyFieldTable());
	$fl->setFieldEntryTable($cm->ccManager->getCompanyFieldEntryTable());

	$vis_item=$GLOBALS["visuItem"];

	$acl_manager=$GLOBALS["current_user"]->getAclManager();
	$user_idst=$GLOBALS["current_user"]->getIdST();
	$roles=$acl_manager->getUserRoleFromPath($user_idst, "/crm/company", "view");

	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY");
	$res.=getCmsTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$res.=printCompanySearchForm();

	addJs($GLOBALS["where_crm_relative"]."/modules/company/", "company.js");

	$um->setTempBaseUrl("popup.php");
	$url=$um->getUrl("op=add");
	$on_click ="javascript:openModWin('".$url."', 1000, 700, refreshParent);";
	$res.="<a class=\"new_element_link\" href=\"".$on_click."\">";
	$res.=$lang->def('_ADD')."</a>\n";

	$tab=new typeOne($vis_item, $table_caption, $table_summary);

	$head=array($lang->def("_COMPANY_NAME"));
	$head[]=$lang->def("_COMPANY_TYPE");
	$head[]=$lang->def("_COMPANY_STATUS");

	$head[]=$lang->def("_INTERESTED_IN");
	$head[]=$lang->def("_ASSIGNED_TO");
	$head[]=$lang->def("_CAME_FROM");

	/*
	$img ="<img src=\"".getPathImage()."ticket/tickets.gif\" alt=\"".$lang->def("_ALT_COMPANYHASTICKETS")."\" ";
	$img.="title=\"".$lang->def("_ALT_COMPANYHASTICKETS")."\" />";
	$head[]=$img;
	*/
	$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$lang->def("_ALT_ASSIGNUSERS")."\" ";
	$img.="title=\"".$lang->def("_ALT_ASSIGNUSERS")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('crm')."standard/new_account.png\" alt=\"".$lang->def("_ALT_NEW_DEMO_USER")."\" ";
	$img.="title=\"".$lang->def("_ALT_NEW_DEMO_USER")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;

	$head_type=array("", "", "", "", "", "image", "image", "image", "image");


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$tab->initNavBar('ini', 'link');
	$tab->setLink($um->getUrl());

	if ((isset($_SESSION["company_ini"])) && (!isset($_GET["ini"]))) {
		$ini =(int)$_SESSION["company_ini"];
	}
	else {
		$ini =$tab->getSelectedElement();
		$_SESSION["company_ini"] =$ini;
	}

	$where=getCompanySearchQuery();

	$level=$GLOBALS["current_user"]->getUserLevelId();
	$is_admin=($level == ADMIN_GROUP_ADMIN ? TRUE : FALSE);
	$is_god_admin=($level == ADMIN_GROUP_GODADMIN ? TRUE : FALSE);

	if ($where !== FALSE) {
		$where.=" AND ";
	}
	$where.="company_id NOT IN("._NO_COMPANY_ID.")";

	// If user is not a god admin then apply permission restrictions
	if ((!$is_god_admin)) {
		if ($where !== FALSE)
			$where.=" AND ";
		else
			$where="";

		$where.="((restricted_access = '0')";
		if (($roles !== FALSE) && (is_array($roles["role_info"]) && (count($roles["role_info"]) > 0))) {
			$where.=" OR (company_id IN (".implode(",", $roles["role_info"]).")))";
		}
		else
			$where.=")";

		$where.=" ";
	}

	$list=$cm->getCompanyList($ini, $vis_item, FALSE, $where, TRUE);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];


	require_once($GLOBALS["where_crm"]."/admin/modules/crmuser/lib.crmuser.php");
	$crmum =new CrmUserManager();
	$crm_users_arr =$crmum->getCrmUsersArray(TRUE);


	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["company_id"];
		$url_details=$um->getUrl("op=details&id=".$id);


		$rowcnt=array();
		$rowcnt[]="<a href=\"".$url_details."\">".$list_arr[$i]["name"]."</a>\n";

		$rowcnt[]=$list_arr[$i]["type_label"];
		$rowcnt[]=$list_arr[$i]["status_label"];

/* - feature disabled
		$open_tickets=$list_arr[$i]["open_tickets"];
		if ($open_tickets > 0) {
			$url=$um->getUrl("op=details&tab_op=ticket&id=".$id."&backto=company");
			$title=$lang->def("_ALT_OPENTICKETS").": ".$open_tickets;
			$img ="<img src=\"".getPathImage()."ticket/tickets.gif\" alt=\"".$title."\" ";
			$img.="title=\"".$title."\" />";
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
		}
		else {
			$img ="<img src=\"".getPathImage()."ticket/no_tickets.gif\" alt=\"".$lang->def("_ALT_NO_OPENTICKETS")."\" ";
			$img.="title=\"".$lang->def("_ALT_NO_OPENTICKETS")."\" />";
			$rowcnt[]=$img;
		}
*/

		$fields_val =$fl->showFieldForUserArr(array($id), array(16, 46));

		if (isset($fields_val[$id][16])) {
			$rowcnt[]=$fields_val[$id][16];
			$user_idst =(int)$list_arr[$i]["assigned_to"];
			$rowcnt[]=(isset($crm_users_arr[$user_idst]) ? $crm_users_arr[$user_idst] : "&nbsp;");
			$rowcnt[]=$fields_val[$id][46];
		}
		else {
			$rowcnt[]="&nbsp;";
			$rowcnt[]="&nbsp;";
			$rowcnt[]="&nbsp;";
		}


		$url=$um->getUrl("op=setpermission&id=".$id);
		$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$lang->def("_ALT_ASSIGNUSERS")."\" ";
		$img.="title=\"".$lang->def("_ALT_ASSIGNUSERS")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

		$img ="<img src=\"".getPathImage('crm')."standard/new_account.png\" alt=\"".$lang->def("_ALT_NEW_DEMO_USER")."\" ";
		$img.="title=\"".$lang->def("_ALT_NEW_DEMO_USER")."\" />";
		$um->setTempBaseUrl("popup.php");
		$url=$um->getUrl("op=manage_demo_user&user_idst=".$list_arr[$i]["demo_user"]."&id=".$id);
		$on_click ="javascript:openModWin('".$url."', 1000, 700, refreshParent);";
		$rowcnt[]="<a href=\"".$on_click."\">".$img."</a>\n";


		$url=$um->getUrl("modname=company&op=details&tab_op=editcompany&id=".$id);
		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";


		if (!$list_arr[$i]["is_used"]) {
			$url=$um->getUrl("op=del&id=".$id);
			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
			$img.="title=\"".$lang->def("_DEL")."\" />";
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
		}
		else
			$rowcnt[]="&nbsp;";

		$tab->addBody($rowcnt);
	}

	$um->setTempBaseUrl("popup.php");
	$url=$um->getUrl("op=add");
	$on_click ="javascript:openModWin('".$url."', 1000, 700, refreshParent);";
	$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$on_click."\">".
	                   $lang->def('_ADD')."</a>\n");

	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	$res.='<div class="print_labels">';
	$url =$GLOBALS["where_crm_relative"]."/modules/company/print_labels.php?pi=".getPI();
	$res.='<a href="'.$url.'" target="_blank">'.$lang->def("_PRINT_LABELS");
	$res.='</a></div>';

	$res.="</div>\n";
	$out->add($res);
}


function addeditCompany($id=0) {
	$res=""; // TODO: check perm

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form=new Form();
	$res="";

	$cm=new CompanyManager();

	if ($id == 0) {
		$stored=array();
		$page_title=$lang->def("_ADD_ITEM");
		$back_ui_url=$um->getUrl();
	}
	else if ($id > 0) {
		$stored=$cm->getCompanyInfo($id);
		$name=$stored["name"];
		$page_title=$lang->def("_EDIT_ITEM").": ".$name;
		$back_ui_url=$um->getUrl("op=details&amp;id=".$id);
	}


	if ($id == 0) {
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_COMPANY");
		$title_arr[]=$page_title;
		$res.=getCmsTitleArea($title_arr, "company");
		$res.="<div class=\"std_block\">\n";
		$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	}

	$cca=new CoreCompanyAdmin();
	$res.=$cca->getAddEditForm($id, $cm, $stored, FALSE, TRUE);

	if ($id > 0) {
		return $res;
	}
	else {
		$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
		$res.="</div>\n";
		$out->add($res);
	}
}


function saveCompany() {
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

	// TODO: check perm

	$um=& UrlManager::getInstance();
	$cm=new CompanyManager();
	$company_id=$cm->saveData($_POST);

	if (isset($_POST["edit"]))
		jumpTo($um->getUrl("op=details&id=".$company_id));
	else
		jumpTo($um->getUrl("op=confcreation&id=".$company_id));
}


function deleteCompany() {
	if ((isset($_GET["id"])) && ((int)$_GET["id"] > 0)) {
		$company_id=$_GET["id"];
	}
	else {
		return FALSE;
	}

	$um=& UrlManager::getInstance();

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance("company", "framework");

	if (isset($_POST["canc_del"])) {
		jumpTo($um->getUrl());
	}
	else if (isset($_POST["conf_del"])) {

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");

		$ccm=new CoreCompanyManager();
		$ccm->deleteCompany($company_id);

		jumpTo($um->getUrl());
	}
	else {

		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

		$res="";
		$cm=new CompanyManager();

		$stored=$cm->getCompanyInfo($company_id);
		$name=$stored["name"];

		$back_ui_url=$um->getUrl();
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_COMPANY");
		$title_arr[]=$lang->def("_DELETE_COMPANY").": ".$name;
		$out->add(getCmsTitleArea($title_arr, "form"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$res.=$form->openForm("del_form", $um->getUrl("op=del&id=".$company_id));


		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$name.'<br />',
			false,
			'conf_del',
			'canc_del');

		$res.=$form->closeForm();
		$res.="</div>\n";

		$out->add($res);
	}
}


function showCompanyUsers(& $lang) {
	$res=""; // TODO: check perm

	/*$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "crm");*/

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

	$um=& UrlManager::getInstance();

	$company_id=(int)importVar("id", true);


	$table_caption=$lang->def("_COMPANY_USERS_CAPTION");
	$table_summary=$lang->def("_COMPANY_USERS_SUMMARY");

/*	$back_ui_url="index.php?modname=company&amp;op=main";

	$title_arr=array();
	$title_arr[]=$lang->def("_COMPANY");
	$title_arr[]="company name";
	$title_arr[]=$lang->def("_COMPANY_USERS");
	$res.=getTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' )); */



	$tab=new typeOne(0, $table_caption, $table_summary);
	$tab->setTableStyle("company_child_table");

	$img ="<img src=\"".getPathImage('fw')."standard/user.gif\" alt=\"".$lang->def("_USER")."\" ";
	$img.="title=\"".$lang->def("_USER")."\" />";

	$img2 ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img2.="title=\"".$lang->def("_MOD")."\" />";

	$head=array($img, $lang->def("_USER"), $img2);
	$head_type=array("image", "", "image");

	$tab->setColsStyle($head_type);
	$tab->addHead($head);


	$cm=new CompanyManager();
	$user_arr=$cm->getCompanyUsers($company_id);


	foreach($user_arr as $idst=>$userid) {

		$rowcnt=array();

		$img ="<img src=\"".getPathImage('fw')."standard/user.gif\" alt=\"".$lang->def("_USER")." ".$userid."\" ";
		$img.="title=\"".$lang->def("_USER")." ".$userid."\" />";
		$rowcnt[]=$img;

		$show_details=(isset($_SESSION["show_user_details"][$idst]) ? TRUE : FALSE);

		$url_qry ="op=details&id=".$company_id;
		$url_qry.="&tab_op=toggleuserdetails&userid=".$idst;
		$url=$um->getUrl($url_qry);
		if ($show_details) {
			$img ="<img src=\"".getPathImage('fw')."standard/less.gif\" alt=\"".$lang->def("_LESSINFO")." ".$userid."\" ";
			$img.="title=\"".$lang->def("_LESSINFO")." ".$userid."\" />";
		}
		else {
			$img ="<img src=\"".getPathImage('fw')."standard/more.gif\" alt=\"".$lang->def("_MOREINFO")." ".$userid."\" ";
			$img.="title=\"".$lang->def("_MOREINFO")." ".$userid."\" />";
		}
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n"."<a href=\"".$url."\">".$userid."</a>\n";

		//$url=$um->getUrl("op=details&id=".$company_id."&tab_op=edituser&userid=".$idst);
		$url=$um->getUrl("id=".$company_id."&op=edituser&userid=".$idst);
		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")." ".$userid."\" ";
		$img.="title=\"".$lang->def("_MOD")." ".$userid."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

		$tab->addBody($rowcnt);

		if ($show_details) {
			$tab->addBodyExpanded(getUserInfo($idst, $lang), "user_details");
		}
	}


	$add_txt="";
	$url=$um->getUrl("op=createuser&id=".$company_id);
	$img="<img src=\"".getPathImage()."standard/adduser.gif\" alt=\"".$lang->def('_ADD_USER')."\" />";
	$add_txt.="<a href=\"".$url."\">".$img.$lang->def('_ADD_USER')."</a>\n";
	$url=$um->getUrl("op=selectusers&id=".$company_id);
	$img="<img src=\"".getPathImage('fw')."standard/addandremove.gif\" alt=\"".$lang->def('_SELECT_USERS')."\" />";
	$add_txt.="<a href=\"".$url."\">".$img.$lang->def('_SELECT_USERS')."</a>\n";
	$tab->addActionAdd($add_txt);

	$res.=$tab->getTable();

	/* $res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res); */
	return $res;
}


function selCompanyUsers() {

	require_once($GLOBALS['where_framework']."/class.module/class.directory.php");
	$mdir=new Module_Directory();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	$company_id=(int)importVar("id", true);

	$back_url=$um->getUrl("op=details&tab_op=companyusers&id=".$company_id);


	if( isset($_POST['okselector']) ) {

		// TODO: check (edit) perm

		$start_sel=$mdir->getStartSelection();
		$arr_selection=array_diff($mdir->getSelection($_POST), $start_sel);
		$arr_deselected = $mdir->getUnselected();
		$cm=new CompanyManager();
		$cm->updateCompanyUsers($company_id, $arr_selection, $arr_deselected);

		jumpTo($back_url);
	}
	else if( isset($_POST['cancelselector']) ) {
		jumpTo($back_url);
	}
	else {

		$mdir->setNFields(2);
		$mdir->show_group_selector=false;
		$mdir->show_orgchart_selector=false;

		if( !isset($_GET['stayon']) ) {
			$cm=new CompanyManager();
			$mdir->resetSelection(array_keys($cm->getCompanyUsers($company_id)));
		}

		// TODO: check (view) perm

		$regusers_idst=$mdir->aclManager->getGroupRegisteredId();
		$mdir->setUserFilter("group", array($regusers_idst));


		$back_ui_url=$um->getUrl("op=details&tab_op=companyusers&id=".$company_id);
		//$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$url=$um->getUrl("op=selectusers&id=".$company_id."&stayon=1");
		//$um->getUrl("op=assignnewuser&appid=".$app_id."&bugid=".$bug_id."&stayon=1");
		$mdir->loadSelector($url, $lang->def('_BUG_ASSIGNED_USERS'), "", TRUE);

		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	}

}


function showCompanyDetails() {
	$res=""; // TODO: check perm

	require_once($GLOBALS['where_framework'].'/lib/lib.tab.php');

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	$company_id=(int)importVar("id", true);
	$tab_op=importVar("tab_op");

	// These are not always used:
	$prj_id=(isset($_GET["prj_id"]) ? $_GET["prj_id"] : 0);
	$task_id=(isset($_GET["task_id"]) ? $_GET["task_id"] : 0);

	$cm=new CompanyManager();
	$stored=$cm->getCompanyInfo($company_id);

	$use_simplified =(getPLSetting("crm", "use_simplified", "off") == "off" ? FALSE : TRUE);

	$table_caption=$lang->def("_COMPANY_USERS_CAPTION");
	$table_summary=$lang->def("_COMPANY_USERS_SUMMARY");

	if ((isset($_GET["backto"])) && (!empty($_GET["backto"]))) {

		switch($_GET["backto"]) {

			case "company": {
				$back_ui_url=$um->getUrl("modname=company&op=main");
			} break;

			case "abook": {
				$back_ui_url=$um->getUrl("modname=abook&op=main"); echo"aaa";
			} break;

			case "task": {
				$back_ui_url=$um->getUrl("modname=task&op=main");
			} break;

			case "ticket": {
				$back_ui_url=$um->getUrl("modname=ticket&op=main");
			} break;

			case "todo": {
				$back_ui_url=$um->getUrl("modname=todo&op=main");
			} break;

			case "contacthistory": {
				$back_ui_url=$um->getUrl("modname=contacthistory&op=main");
			} break;

		}
	}
	else {
		$back_ui_url=$um->getUrl();
	}

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY");
	$title_arr[]=$stored["name"];
	$title_arr[]=$lang->def("_COMPANY_DETAILS");
	$res.=getCmsTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

	$tab_man = new TabView('course_advice', '');
	$tab1 = new TabElemDefault(	'company_info',
						$lang->def('_COMPANY_INFO'),
						getPathImage().'standard/info.gif');
	$tab2 = new TabElemDefault(	'company_users',
						$lang->def('_COMPANY_USERS'),
						getPathImage().'standard/users.gif');

	if (!$use_simplified) {
		$tab3 = new TabElemDefault(	'company_projects',
							$lang->def('_COMPANY_PROJECTS'),
							getPathImage().'standard/project.gif');
		$tab4 = new TabElemDefault(	'company_tickets',
							$lang->def('_COMPANY_TICKETS'),
							getPathImage().'ticket/tickets.gif');
		$tab5 = new TabElemDefault(	'company_todo',
							$lang->def('_COMPANY_TODO'),
							getPathImage().'todo/todo.gif');
		$tab6 = new TabElemDefault(	'company_contacthistory',
							$lang->def('_COMPANY_CHISTORY'),
							getPathImage().'standard/calendar.gif');
	}

	$tab_man->addTab($tab1);
	/*$tab_man->addTab($tab2);

	if (!$use_simplified) {
		$tab_man->addTab($tab3);
		$tab_man->addTab($tab4);
		$tab_man->addTab($tab5);
		$tab_man->addTab($tab6);
	} */
	$tab_man->parseInput($_POST, $_SESSION);

	$active_tab = $tab_man->getActiveTab();

	$goto_tab=FALSE;
	$to_show=FALSE;
	switch ($tab_op) {
		case "companyusers": {
			// $goto_tab="company_users";
			$goto_tab ="company_info";
		} break;
		case "editcompany": {
			$to_show="edit_company";
			$goto_tab="company_info";
		} break;
		case "toggleuserdetails": {
			// $goto_tab="company_users";
			$goto_tab ="company_info";
			toggleUserDetails($company_id, $_GET["userid"]);
		} break;
		case "showprojects": {
			$goto_tab="company_projects";
		} break;
		case "setprjini": {
			$goto_tab="company_projects";
			$jump_url=$um->getUrl("op=details&id=".$company_id."&tab_op=showprojects");
			setCopmanyIni("project", $company_id, $_GET["ini"], $jump_url);
		} break;
		case "addprj": {
			$to_show="add_project";
			$goto_tab="company_projects";
		} break;
		case "editprj": {
			$to_show="edit_project";
			$goto_tab="company_projects";
		} break;
		case "saveprj": {
			$goto_tab="company_projects";
			if (!isset($_POST["undo"]))
				saveProject($company_id);
		} break;
		case "toggleprjinfo": {
			$goto_tab="company_projects";
			toggleProjectDetails($company_id, $prj_id);
		} break;
		case "selprjvtab": {
			$goto_tab="company_projects";
			$jump_url=$um->getUrl("op=details&id=".$company_id."&tab_op=showprojects");
			setActiveVTab("project", $prj_id, $_GET["tc"], $jump_url);
		} break;
		case "addtask": {
			$to_show="add_task";
			$goto_tab="company_projects";
		} break;
		case "edittask": {
			$to_show="edit_task";
			$goto_tab="company_projects";
		} break;
		case "savetask": {
			$goto_tab="company_projects";
			if (!isset($_POST["undo"]))
				saveTask($company_id);
		} break;
		case "toggletaskinfo": {
			$goto_tab="company_projects";
			toggleTaskDetails($company_id, $task_id);
		} break;
		case "seltaskvtab": {
			$goto_tab="company_projects";
			$jump_url=$um->getUrl("op=details&id=".$company_id."&tab_op=showprojects");
			setActiveVTab("task", $task_id, $_GET["tc"], $jump_url);
		} break;
		case "uploadfile": {
			$goto_tab="company_projects";
			if (!isset($_POST["undo"]))
				saveFile($company_id);
		} break;
		case "download": {
			$goto_tab="company_projects";
			downloadFile();
		} break;
		case "addprjnote": {
			$to_show="add_project_note";
			$goto_tab="company_projects";
		} break;
		case "editprjnote": {
			$to_show="edit_project_note";
			$goto_tab="company_projects";
		} break;
		case "toggleprjnote": {
			$goto_tab="company_projects";
			toggleNoteDetails("project", $company_id, $prj_id, (int)$_GET["note_id"]);
		} break;
		case "addtasknote": {
			$to_show="add_task_note";
			$goto_tab="company_projects";
		} break;
		case "edittasknote": {
			$to_show="edit_task_note";
			$goto_tab="company_projects";
		} break;
		case "toggletasknote": {
			$goto_tab="company_projects";
			toggleNoteDetails("task", $company_id, $task_id, (int)$_GET["note_id"]);
		} break;
		case "savenote": {
			$goto_tab="company_projects";
			if (!isset($_POST["undo"]))
				saveNote($company_id);
		} break;
		case "ticket": {
			$goto_tab="company_tickets";
		} break;
		case "addticket": {
			$to_show="add_ticket";
			$goto_tab="company_tickets";
		} break;
		case "createticket": {
			$to_show="create_ticket";
			$goto_tab="company_tickets";
		} break;
		case "showticket": {
			$to_show="show_ticket";
			$goto_tab="company_tickets";
		} break;
		case "editticket": {
			$to_show="edit_ticket";
			$goto_tab="company_tickets";
		} break;
		case "saveticket": {
			$to_show="save_ticket";
			$goto_tab="company_tickets";
		} break;
		case "addticketreply":
		case "editticketmsg": {
			$to_show="addedit_ticket_msg";
			$goto_tab="company_tickets";
		} break;
		case "saveticketmsg": {
			$to_show="save_ticket_msg";
			$goto_tab="company_tickets";
		} break;
		case "switchticketlock": {
			$to_show="switch_ticket_lock";
			$goto_tab="company_tickets";
		} break;
		case "assignticketusers": {
			$res="";
			assignTicketUsers($company_id);
			return NULL;
		} break;
		case "ticketsetorder": {
			$to_show="set_ticket_order";
			$goto_tab="company_tickets";
		} break;
		case "opentask": {
			$to_show="open_task";
			$goto_tab="company_projects";
		} break;
		case "todo": {
			$goto_tab="company_todo";
		} break;
		case "addtodo": {
			$to_show="add_todo";
			$goto_tab="company_todo";
		} break;
		case "savetodo": {
			$to_show="save_todo";
			$goto_tab="company_todo";
		} break;
		case "edittodo": {
			$to_show="edit_todo";
			$goto_tab="company_todo";
		} break;
		case "switchtodocomplete": {
			$to_show="switch_todo_complete";
			$goto_tab="company_todo";
		} break;
		case "toggletododetails": {
			$to_show="toggle_todo_details";
			$goto_tab="company_todo";
		} break;
		case "todosetorder": {
			$to_show="set_todo_order";
			$goto_tab="company_todo";
		} break;
		case "contacthistory": {
			$goto_tab="company_contacthistory";
		} break;
		case "addcontacthistory": {
			$to_show="add_contacthistory";
			$goto_tab="company_contacthistory";
		} break;
		case "savecontacthistory": {
			$to_show="save_contacthistory";
			$goto_tab="company_contacthistory";
		} break;
		case "editcontacthistory": {
			$to_show="edit_contacthistory";
			$goto_tab="company_contacthistory";
		} break;
		case "switchcontacthistorycomplete": {
			$to_show="switch_contacthistory_complete";
			$goto_tab="company_contacthistory";
		} break;
		case "togglecontacthistorydetails": {
			$to_show="toggle_contacthistory_details";
			$goto_tab="company_contacthistory";
		} break;
		case "contacthistorysetorder": {
			$to_show="set_contacthistory_order";
			$goto_tab="company_contacthistory";
		} break;
	}


	if (empty($active_tab)) {
		if ($goto_tab === FALSE)
			$active_tab="company_info";
		else
			$active_tab=$goto_tab;
		$tab_man->setActiveTab($active_tab);
	}
	else {
		$to_show=FALSE;
	}


	$res.=$tab_man->printTabView_Begin('', true);


	if ($to_show === FALSE) {

		switch ($active_tab) {
			case "company_info": {
				$res.=getCompanyInfo($company_id, $stored["name"], $lang);
			} break;
			case "company_users": {
				$res.=showCompanyUsers($lang);
			} break;
			case "company_projects": {
				$res.=showCompanyProjects($company_id, $lang);
			} break;
			case "company_tickets": {
				$res.=showCompanyTickets($company_id);
			} break;
			case "company_todo": {
				$res.=showCompanyTodo($company_id);
			} break;
			case "company_contacthistory": {
				$res.=showCompanyContactHistory($company_id);
			} break;
		}

	}
	else {

		switch ($to_show) {
			case "edit_company": {
				$res.=addeditCompany($company_id);
			} break;
			case "add_project": {
				$res.=addeditProject($company_id);
			} break;
			case "edit_project": {
				$res.=addeditProject($company_id, $prj_id);
			} break;
			case "add_task": {
				$res.=addeditTask($company_id, $prj_id);
			} break;
			case "edit_task": {
				$res.=addeditTask($company_id, $prj_id, $task_id);
			} break;
			case "add_project_note": {
				$res.=addeditNote("project", $company_id, $prj_id);
			} break;
			case "edit_project_note": {
				$res.=addeditNote("project", $company_id, $prj_id, (int)$_GET["note_id"]);
			} break;
			case "add_task_note": {
				$res.=addeditNote("task", $company_id, $task_id);
			} break;
			case "edit_task_note": {
				$res.=addeditNote("task", $company_id, $task_id, (int)$_GET["note_id"]);
			} break;
			case "show_ticket": {
				$res.=showTicket($company_id);
			} break;
			case "add_ticket": {
				$res.=addTicket($company_id);
			} break;
			case "create_ticket": {
				createTicket($company_id);
			} break;
			case "edit_ticket": {
				$res.=editTicket($company_id);
			} break;
			case "save_ticket": {
				saveTicket($company_id);
			} break;
			case "addedit_ticket_msg": {
				$res.=addeditTicketMsg($company_id);
			} break;
			case "save_ticket_msg": {
				saveTicketMsg($company_id);
			} break;
			case "switch_ticket_lock": {
				$res.=switchTicketLock($company_id);
			} break;
			case "set_ticket_order": {
				setTicketOrder($company_id);
			} break;
			case "open_task": {
				doOpenTask($company_id, $prj_id, $task_id);
			} break;
			case "add_todo": {
				$res.=addTodo($company_id);
			} break;
			case "save_todo": {
				saveTodo($company_id);
			} break;
			case "edit_todo": {
				$res.=editTodo($company_id);
			} break;
			case "switch_todo_complete": {
				switchTodoComplete($company_id);
			} break;
			case "toggle_todo_details": {
				toggleTodoDetails($company_id);
			} break;
			case "set_todo_order": {
				setTodoOrder($company_id);
			} break;
			case "add_contacthistory": {
				$res.=addContactHistory($company_id);
			} break;
			case "save_contacthistory": {
				saveContactHistory($company_id);
			} break;
			case "edit_contacthistory": {
				$res.=editContactHistory($company_id);
			} break;
			case "switch_contacthistory_complete": {
				switchContactHistoryComplete($company_id);
			} break;
			case "toggle_contacthistory_details": {
				toggleContactHistoryDetails($company_id);
			} break;
			case "set_contacthistory_order": {
				setContactHistoryOrder($company_id);
			} break;
		}

	}


	$res.=$tab_man->printTabView_End();

	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}


function getAbookSummaryForCompany($company_id) {
	$res="";

	$um=& UrlManager::getInstance();

	require_once($GLOBALS["where_crm"]."/modules/abook/lib.abook.php");

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("abook", "crm");
	$company_lang=& DoceboLanguage::createInstance("company", "framework");

	require_once($GLOBALS["where_framework"]."/lib/lib.field.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_ABOOK_TABLE_CAPTION");
	$table_summary=$lang->def("_ABOOK_TABLE_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];

	$abm=new AddressBookManager();

	$fl =new FieldList();
	//$fl->setFieldEntryTable($abm->ccm->getCompanyFieldEntryTable());


	$tab=new typeOne($vis_item, $table_caption, $table_summary);
	$tab->setTableStyle("company_child_table");

	$head=array();
	$img ="<img src=\"".getPathImage('fw')."standard/user.gif\" alt=\"".$lang->def("_USER")."\" ";
	$img.="title=\"".$lang->def("_USER")."\" />";
	$head[]=$img;
	$head[]=$lang->def("_USER");
	$head[]=$lang->def("_EMAIL");
	$head[]=$lang->def("_PHONE");
	$head[]=$lang->def("_ROLE_IN_COMPANY");

	$img2 ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img2.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img2;

	$head_type=array("image", "", "", "", "", "image");

	$tab->setColsStyle($head_type);
	$tab->addHead($head);


	$where ="t2.company_id='".(int)$company_id."'";

	$list=$abm->ccm->getAllCompanyUsers(FALSE, FALSE, $where, TRUE);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["user_id"];
		$userid=$list["user"][$id];


		$rowcnt=array();

		$img ="<img src=\"".getPathImage('fw')."standard/user.gif\" alt=\"".$lang->def("_USER")."\" ";
		$img.="title=\"".$lang->def("_USER")."\" />";
		$rowcnt[]=$img;

		$show_details=(isset($_SESSION["show_user_details"][$id]) ? TRUE : FALSE);

		$url_qry ="op=details&id=".$company_id;
		$url_qry.="&tab_op=toggleuserdetails&userid=".$id;
		$url=$um->getUrl($url_qry);
		if ($show_details) {
			$img ="<img src=\"".getPathImage('fw')."standard/less.gif\" alt=\"".$lang->def("_LESSINFO")." ".$userid."\" ";
			$img.="title=\"".$lang->def("_LESSINFO")." ".$userid."\" />";
		}
		else {
			$img ="<img src=\"".getPathImage('fw')."standard/more.gif\" alt=\"".$lang->def("_MOREINFO")." ".$userid."\" ";
			$img.="title=\"".$lang->def("_MOREINFO")." ".$userid."\" />";
		}
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n"."<a href=\"".$url."\">".$userid."</a>\n";

		$toggle_id=$id."_".$company_id;


		$email =$list["user_email"][$id];
		$cnt =(!empty($email) ? '<a href="mailto:'.$email.'">' : '&nbsp;');
		$cnt.=$email.(!empty($email) ? '</a>' : '');
		$rowcnt[]=$cnt;

		$fields_val =$fl->showFieldForUserArr(array($id), array(38, 10));
		if (isset($fields_val[$id][10])) {
			$rowcnt[]=$fields_val[$id][10];
			$rowcnt[]=$fields_val[$id][38];
		}
		else {
			$rowcnt[]="&nbsp;";
			$rowcnt[]="&nbsp;";
		}

		$url=$um->getUrl("id=".$company_id."&op=edituser&userid=".$id);
		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")." ".$userid."\" ";
		$img.="title=\"".$lang->def("_MOD")." ".$userid."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

		$tab->addBody($rowcnt);

		if ($show_details) {
			$tab->addBodyExpanded(getUserInfo($id, $lang), "user_details");
		}
	}

	$add_txt="";
	$url=$um->getUrl("op=createuser&id=".$company_id);
	$img="<img src=\"".getPathImage()."standard/adduser.gif\" alt=\"".$lang->def('_ADD_USER')."\" />";
	$add_txt.="<a href=\"".$url."\">".$img.$lang->def('_ADD_USER')."</a>\n";
	$url=$um->getUrl("op=selectusers&id=".$company_id);
	$img="<img src=\"".getPathImage('fw')."standard/addandremove.gif\" alt=\"".$lang->def('_SELECT_USERS')."\" />";
	$add_txt.="<a href=\"".$url."\">".$img.$lang->def('_SELECT_USERS')."</a>\n";
	$tab->addActionAdd($add_txt);

	$res.=$tab->getTable();

	return $res;
}


function getCompanyInfo($id, $company_name, & $lang) {
	$res="";

	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$um=& UrlManager::getInstance();

	$cm=new CompanyManager();
	$stored=$cm->getCompanyInfo($id);

	$field_list=array();

	$idst=$cm->ccManager->getCompanyFieldsGroupIdst();

	$fl = new FieldList();
	$fl->setGroupFieldsTable($cm->ccManager->getCompanyFieldTable());
	$field_list_arr=$fl->getFieldsArrayFromIdst(array($idst), FIELD_INFO_TRANSLATION);

	$field_list=array_keys($field_list_arr);

	$form_extra="";
	$fl->setFieldEntryTable($cm->ccManager->getCompanyFieldEntryTable());

 	$user_field_arr=$fl->showFieldForUserArr(array($id), $field_list);
	if (is_array($user_field_arr[$id]))
 		$field_val=$user_field_arr[$id];
	else
		$field_val=array();


	$res.=getAbookSummaryForCompany($id);

 	$res.="<table class=\"company_details width_40_left\" cellspacing=\"0\" summary=\"".$lang->def("_TAB_COMPANY_DETAILS_SUMMARY")."\">\n";
	$res.="<caption>".$lang->def("_TAB_COMPANY_DETAILS_CAPTION")."</caption>\n<tbody>\n";
	$i=0;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_COMPANY_NAME").":</th>";
	$res.="<td class=\"".$class."\"><b>".$company_name."</b></td></tr>\n";
	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_ADDRESS").":</th>";
	$res.="<td class=\"".$class."\">".nl2br($stored["address"])."</td></tr>\n";
	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_COMPANY_TEL").":</th>";
	$res.="<td class=\"".$class."\">".$stored["tel"]."</td></tr>\n";

	// ---
	$ctype_info=$cm->getCompanyTypeList();
	$cstatus_info=$cm->getCompanyStatusList();
	$ctype_list=$ctype_info["list"];
	$cstatus_list=$cstatus_info["list"];

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_COMPANY_TYPE").":</th>";
	$res.="<td class=\"".$class."\">".$ctype_list[$stored["ctype_id"]]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_COMPANY_STATUS").":</th>";
	$res.="<td class=\"".$class."\">".$cstatus_list[$stored["cstatus_id"]]."</td></tr>\n";

	// ---

	require_once($GLOBALS["where_crm"]."/admin/modules/crmuser/lib.crmuser.php");
	$crmum =new CrmUserManager();
	$crm_users_arr =$crmum->getCrmUsersArray(TRUE);
	$user_idst =(int)$stored["assigned_to"];
	$assigned_label=(isset($crm_users_arr[$user_idst]) ? $crm_users_arr[$user_idst] : "&nbsp;");

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_ASSIGNED_TO").":</th>";
	$res.="<td class=\"".$class."\">".$assigned_label."</td></tr>\n";
	// ---

	$i=!$i;
	foreach ($field_val as $field_id=>$value) {
		$class="line-".(int)($i);
		$res.="<tr><th scope=\"col\" class=\"".$class."\">".$field_list_arr[$field_id].":</th>";
		$res.="<td class=\"".$class."\">".$value."</td></tr>\n";
		$i=!$i;
	}

	$i=!$i;
	$class="line-".(int)($i);
	$url="mailto:".$stored["email"];
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_EMAIL").":</th>";
	$res.="<td class=\"".$class."\"><a href=\"".$url."\">".$stored["email"]."</a></td></tr>\n";
	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_COMPANY_VAT_NUMBER").":</th>";
	$res.="<td class=\"".$class."\">".$stored["vat_number"]."</td></tr>\n";
	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_COMPANY_CODE").":</th>";
	$res.="<td class=\"".$class."\">".$stored["code"]."</td></tr>\n";

	$url=$um->getUrl("op=details&tab_op=editcompany&id=".$id);
	$img="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def('_EDIT_COMPANY')."\" />";
	$res.="<tr><td colspan=\"2\">"."<a href=\"".$url."\">".$img.$lang->def('_EDIT_COMPANY')."</a>\n";
	$res.="</td></tr>\n";
	$res.="</tbody>\n</table>\n";

	$res.='<div class="width_60_right">'.getCompanyDetailsQuickEdit($id).'</div>';

	$res.='<div class="no_float"></div>';

	return $res;
}


function getCompanyDetailsQuickEdit($company_id) {
	$res ="";

	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$cm =new CompanyManager();
	$form =new Form();
	$um=& UrlManager::getInstance();
	$lang=& DoceboLanguage::createInstance("company", "framework");

	$company_id =(int)$company_id;
	$stored=$cm->getCompanyInfo($company_id);

	$notes=$stored["notes"];
	$recall_on=$GLOBALS["regset"]->databaseToRegional($stored["recall_on"]);


	$url =$um->getUrl("op=quickeditsave&id=".$company_id);
	$form_code=$form->openForm("main_form", $url);
	$res.=$form_code.$form->openElementSpace();

	$res.=$form->getDatefield($lang->def("_RECALL_ON").":", "recall_on", "recall_on", $recall_on, false, true);
	//$res.=$form->getSimpleTextarea($lang->def("_COMPANY_NOTES"), "notes", "notes", $notes);

	$res.='<label for="notes"><b>'.$lang->def("_COMPANY_NOTES").":".'</b></label>';
	$res.='<textarea id="notes" class="textarea" cols="54" rows="15" name="notes">'.$notes.'</textarea>';

	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $lang->def("_SAVE"));
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();


	return $res;
}


function addeditCompanyUser($user_idst=FALSE) {
	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');

	$um=& UrlManager::getInstance();

	$md=new Module_Directory();
	$company_id=(int)importVar("id", true);

	$back_url=$um->getUrl("op=details&tab_op=companyusers&id=".$company_id);
	$form_url=$um->getUrl("op=createuser&id=".$company_id);

	$acl_manager=& $GLOBALS["current_user"]->getAclManager();


	if (($user_idst !== FALSE) && ($user_idst > 0)) {
		$user_info=$acl_manager->getUser($user_idst, FALSE);
		$user_id=$acl_manager->relativeId($user_info[ACL_INFO_USERID]);
	}
	else {
		$user_id=FALSE;
	}


	if (isset($_POST["editpersonsave"])) { // Save user
		require_once($GLOBALS['where_framework'].'/modules/org_chart/tree.org_chart.php');

		$repoDb=new TreeDb_OrgDb($GLOBALS['prefix_fw'].'_org_chart_tree');
		$org_view=new TreeView_OrgView($repoDb, 'organization_chart', $GLOBALS['title_organigram_chart']);

		$org_view->aclManager=& $GLOBALS["current_user"]->getAclManager();
		$org_view->extendedParsing($_POST, $_POST, $_POST);


		$userid=$_POST['userid'];
		$user_st=$acl_manager->getUserST($userid);

		if ($user_st !== FALSE) {
			$cm=new CompanyManager();
			$cm->addToCompanyUsers($company_id, $user_st);
		}

		jumpTo($back_url);
	}
	else if (isset($_POST["editpersoncancel"])) { // Cancel user creation
		jumpTo($back_url);
	}
	else { // Show user creation form

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");
		$lang=& DoceboLanguage::createInstance("company", "framework");

		$back_ui_url=$back_url;

		$cm=new CompanyManager();
		$stored=$cm->getCompanyInfo($company_id);

		$title_arr=array();
		$title_arr[]=$lang->def("_COMPANY");
		$title_arr[]=$stored["name"];
		if ($user_id === FALSE)
			$title_arr[]=$lang->def("_ADD_USER");
		else
			$title_arr[]=$lang->def("_EDIT_USER").": ".$user_id;
		$out->add(getCmsTitleArea($title_arr, "company"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$reg_group=$acl_manager->getGroupRegisteredId();
		$md->editPerson($user_id, array($reg_group), $form_url);

		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
		$out->add("</div>\n");
	}

}


function deleteCompanyUser($user_idst) {
	if ((int)$user_idst < 1) {
		return FALSE;
	}
	if ((isset($_GET["id"])) && ((int)$_GET["id"] > 0)) {
		$company_id=$_GET["id"];
	}
	else {
		return FALSE;
	}

	$acl_manager=& $GLOBALS["current_user"]->getAclManager();
	$user_info=$acl_manager->getUser($user_idst, FALSE);
	$user_id=$acl_manager->relativeId($user_info[ACL_INFO_USERID]);

	$um=& UrlManager::getInstance();

	$back_url=$um->getUrl("op=details&tab_op=companyusers&id=".$company_id);

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance("company", "framework");

	if (isset($_POST["canc_del"])) {
		jumpTo($back_url);
	}
	else if (isset($_POST["conf_del"])) {

		// --------------------------------------------------------------------------------
		// Brutally copied from doceboCore/modules/org_chart/tree.org_chart.php

			require_once($GLOBALS['where_framework'] . "/lib/lib.eventmanager.php");

			$u_info = $user_info;
			$userid = $u_info[ACL_INFO_USERID];

			$pl_man =& PlatformManager::createInstance();

			$array_subst = array(	'[url]' => $GLOBALS[$pl_man->getHomePlatform()]['url'],
									'[userid]' => $acl_manager->relativeId($userid) );
			// message to user that is inserted
			$msg_composer = new EventMessageComposer('admin_directory', 'framework');

			$msg_composer->setSubjectLangText('email', '_DELETED_USER_SBJ', false);
			$msg_composer->setBodyLangText('email', '_DELETED_USER_TEXT', $array_subst);

			$msg_composer->setSubjectLangText('sms', '_DELETED_USER_SBJ_SMS', false);
			$msg_composer->setBodyLangText('sms', '_DELETED_USER_TEXT_SMS', $array_subst);
			/*
			createNewAlert(	'UserDel', 'directory', 'edit', '1', 'User '.$userid.' deleted',
						array($idst), $msg_composer );*/

			$event =& DoceboEventManager::newEvent('UserDel', 'directory', 'edit', '1', 'User '.$userid.' deleted');
			$event->setProperty('recipientid', implode(',', array($user_idst)) );
			$event->setProperty('subject', $msg_composer->getSubject('email', getLanguage() ));
			$event->setProperty('body', $msg_composer->getBody('email', getLanguage() ));
			$msg_composer->prepare_serialize(); // __sleep is preferred but i preferr this method
			$event->setProperty('MessageComposer',  addslashes(rawurlencode(serialize($msg_composer))) );
			$event->setProperty('userdeleted', $user_idst);
			DoceboEventManager::dispatch($event);

			$acl_manager->deleteUser( $user_idst );
		// --------------------------------------------------------------------------------

		// TODO: change or remove this!:
		//mysql_query("DELETE FROM core_company_user WHERE company_id='".$company_id."' AND user_id='".$user_idst."' LIMIT 1");

		jumpTo($back_url);
	}
	else {

		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

		$res="";
		$cm=new CompanyManager();

		$stored=$cm->getCompanyInfo($company_id);
		$name=$stored["name"];

		$back_ui_url=$um->getUrl();
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_COMPANY");
		$title_arr[]=$lang->def("_DELETE_COMPANY").": ".$name;
		$out->add(getCmsTitleArea($title_arr, "form"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$res.=$form->openForm("del_form", $um->getUrl("op=deluser&userid=".$user_idst."&id=".$company_id));


		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_USERNAME').' :</span> '.$user_id.'<br />',
			false,
			'conf_del',
			'canc_del');

		$res.=$form->closeForm();
		$res.="</div>\n";

		$out->add($res);
	}
}


function toggleUserDetails($company_id, $user_id) {

	if (isset($_SESSION["show_user_details"][$user_id]))
		unset($_SESSION["show_user_details"][$user_id]);
	else
		$_SESSION["show_user_details"][$user_id]=1;

	$um=& UrlManager::getInstance();

	jumpTo($um->getUrl("op=details&tab_op=companyusers&id=".$company_id));
}


function getUserInfo($user_id, & $lang) {
	$res="";

	$cm=new CompanyManager();
	$res.=$cm->ccManager->getUserInfo($user_id, $lang);

	return $res;
}


function showCompanyProjects($company_id, & $lang) {
	$res="";

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_COMPANY_PROJECT_TAB_CAPTION");
	$table_summary=$lang->def("_COMPANY_PROJECT_TAB_SUMMARY");

	$vis_item=3;//$GLOBALS["visuItem"];

	$um=& UrlManager::getInstance();

	$tab=new typeOne($vis_item, $table_caption, $table_summary);
	$tab->setTableStyle("company_child_table");


	$head=array($lang->def("_PROJECT_NAME"));
	$head[]=$lang->def("_PROGRESS");
	$head[]=$lang->def("_PRIORITY");
	$head[]=$lang->def("_STATUS");
	$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;

	$head_type=array("", "", "", "", "image", "image", "image", "image");


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=setprjini");

	$tab->initNavBar('ini', 'link');
	$tab->setLink($url);

	$ini=(getCopmanyIni("project", $company_id)-1)*$vis_item;


	$cm=new CompanyManager();
	$list=$cm->getProjectList($company_id, $ini, $vis_item);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	$cm->initProjectConstants();
	$status_arr=$cm->getStatusArray($lang);
	$priority_arr=$cm->getPriorityArray($lang);


	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++) {

		$rowcnt=array();

		$id=$list_arr[$i]["prj_id"];
		$name=$list_arr[$i]["name"];

		$show_details=(isset($_SESSION["show_project_details"][$id]) ? TRUE : FALSE);


		$anchor="<a name=\"project".$id."\" id=\"project".$id."\" />\n";

		$url=$um->getUrl("op=details&id=".$company_id."&tab_op=toggleprjinfo&prj_id=".$id);
		if ($show_details) {
			$img ="<img src=\"".getPathImage('fw')."standard/less.gif\" alt=\"".$lang->def("_LESSINFO")." ".$name."\" ";
			$img.="title=\"".$lang->def("_LESSINFO")." ".$name."\" />";
		}
		else {
			$img ="<img src=\"".getPathImage('fw')."standard/more.gif\" alt=\"".$lang->def("_MOREINFO")." ".$name."\" ";
			$img.="title=\"".$lang->def("_MOREINFO")." ".$name."\" />";
		}
		$rowcnt[]=$anchor."<a href=\"".$url."\">".$img."</a><a href=\"".$url."\">".$name."</a>\n";

		$rowcnt[]=drawProgressBar($list_arr[$i]["progress"]);

		$rowcnt[]=$priority_arr[$list_arr[$i]["priority"]];
		$rowcnt[]=$status_arr[$list_arr[$i]["status"]];

		$url=$um->getUrl("op=details&id=".$company_id."&tab_op=editprj&prj_id=".$id);
		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")." ".$name."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";


		$rowcnt[]="&nbsp;";

		$tab->addBody($rowcnt);

		if ($show_details)
			$tab->addBodyExpanded(showProjectDetails($company_id, $id), "project_details");
	}


	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=addprj");
	$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".$lang->def('_ADD')."</a>\n");


	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	return $res;
}


function getCopmanyIni($type, $id) {
	$res=0;

	if (isset($_SESSION["company_ini"][$type][$id]))
		$res=(int)$_SESSION["company_ini"][$type][$id];
	else
		$res=1;

	return $res;
}


function setCopmanyIni($type, $id, $value, $jump_url=FALSE) {

	$_SESSION["company_ini"][$type][$id]=$value;

	if ($jump_url !== FALSE) {
		jumpTo($jump_url);
	}
}


function addeditProject($company_id, $prj_id=0) {
	$res="";

	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form=new Form();
	$cm=new CompanyManager();


	$form_code="";
	$url=$um->getUrl("op=details&id=".$company_id.="&tab_op=saveprj");

	$form_code=$form->openForm("main_form", $url);

	if ($prj_id > 0) { // Edit

		$submit_lbl=$lang->def("_SAVE");

		$info=$cm->getProjectInfo($prj_id);

		$name=$info["name"];
		$cost=$info["cost"];
		$gain=$info["gain"];
		$status=$info["status"];
		$priority=$info["priority"];
		$progress=$info["progress"];
		$sign_date=$info["sign_date"];
		$expire=$info["expire"];
		$deadline=$info["deadline"];
		$ticket=$info["ticket"];

	}
	else { // Add

		$submit_lbl=$lang->def("_INSERT");

		$cm->initProjectConstants();

		$name="";
		$cost="";
		$gain="";
		$status=STATUS_WAITINGSIG;
		$priority=PRIORITY_LOW;
		$progress="0";
		$sign_date="";
		$expire="";
		$deadline="";
		$ticket="0";

	}

	$status_arr=$cm->getStatusArray($lang);
	$priority_arr=$cm->getPriorityArray($lang);


	$res.=$form_code.$form->openElementSpace();

	$date_format=FALSE;

	$res.=$form->getTextfield($lang->def("_PROJECT_NAME").":", "name", "name", 255, $name);
	$res.=$form->getTextfield($lang->def("_PROJECT_COST").":", "cost", "cost", 255, $cost);
	$res.=$form->getTextfield($lang->def("_PROJECT_GAIN").":", "gain", "gain", 255, $gain);
	$res.=$form->getDropdown($lang->def("_PRIORITY").":", "priority", "priority", $priority_arr, $priority);
	$res.=$form->getDropdown($lang->def("_STATUS").":", "status", "status", $status_arr, $status);
	$res.=$form->getTextfield($lang->def("_PROGRESS").":", "progress", "progress", 3, $progress);
	$res.=$form->getDatefield($lang->def("_SIGNED_ON").":", "sign_date", "sign_date", $sign_date, $date_format, true);
	$res.=$form->getDatefield($lang->def("_EXPIRE_ON").":", "expire", "expire", $expire, $date_format, true);
	$res.=$form->getDatefield($lang->def("_DEADLINE").":", "deadline", "deadline", $deadline, $date_format, true);
	$res.=$form->getTextfield($lang->def("_TICKETS").":", "ticket", "ticket", 255, $ticket);

	$res.=$form->getHidden("prj_id", "prj_id", $prj_id);
	$res.=$form->getHidden("company_id", "company_id", $company_id);


	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $submit_lbl);
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	return $res;
}


function saveProject($company_id) {

	$um=& UrlManager::getInstance();
	$cm=new CompanyManager();
	$cm->saveProject($_POST);

	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=showprojects");

	jumpTo($url);
}


function toggleProjectDetails($company_id, $prj_id) {

	if (isset($_SESSION["show_project_details"][$prj_id]))
		unset($_SESSION["show_project_details"][$prj_id]);
	else
		$_SESSION["show_project_details"][$prj_id]=1;

	$anchor="#project".$prj_id;

	$um=& UrlManager::getInstance();

	jumpTo($um->getUrl("op=details&tab_op=showprojects&id=".$company_id), $anchor);
}


function showProjectDetails($company_id, $prj_id) {
	$res="";

	$lang=& DoceboLanguage::createInstance("company", "framework");

	$active=getActiveVTab("project", $prj_id);

	if (empty($active))
		$active="details";

	$tabs=array();
	$tc="details"; // tab code
	$tabs[$tc]["image"]=getPathImage()."standard/details.gif";
	$tabs[$tc]["title"]=$lang->def("_PROJECT_DETAILS");
	$tabs[$tc]["url"]=getProjectVTabUrl($company_id, $prj_id, $tc);
	$tabs[$tc]["style"]="project_details";
	// $tabs[$i]["content_style"]="test";
	$tc="attach";
	$tabs[$tc]["image"]=getPathImage()."standard/attach.gif";
	$tabs[$tc]["title"]=$lang->def("_PROJECT_ATTACHMENTS");
	$tabs[$tc]["url"]=getProjectVTabUrl($company_id, $prj_id, $tc);
	$tabs[$tc]["style"]="attachments";
/*	$tc="comments";
	$tabs[$tc]["image"]=getPathImage()."standard/chat.gif";
	$tabs[$tc]["title"]=$lang->def("_PROJECT_COMMENTS");
	$tabs[$tc]["url"]=getProjectVTabUrl($company_id, $prj_id, $tc);
	$tabs[$tc]["style"]="comments"; */
	$tc="notes";
	$tabs[$tc]["image"]=getPathImage()."standard/notes.gif";
	$tabs[$tc]["title"]=$lang->def("_PROJECT_NOTES");
	$tabs[$tc]["url"]=getProjectVTabUrl($company_id, $prj_id, $tc);
	$tabs[$tc]["style"]="notes";


	$res.=openVerticalTab($tabs, $active); // --------------------------------------------

	switch($active) {

		case "details": {
			$res.=showProjectInfo($prj_id);
		} break;

		case "attach": {
			$res.=showAttachments("project", $company_id, $prj_id);
		} break;

		case "notes": {
			$res.=showNotes("project", $company_id, $prj_id);
		} break;

/*		case "comments": {
			$res.=showComments("project", $company_id, $prj_id);
		} break; */

	}

	$res.=closeVerticalTab(); // ------------------------------------------------

	if (getPLSetting("crm", "use_simplified", "off") == "off") {
		$res.=getProjectTasks($company_id, $prj_id);
	}

	return $res;
}


function getProjectVTabUrl($company_id, $prj_id, $tc) {
	$um=& UrlManager::getInstance();

	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=selprjvtab&prj_id=".$prj_id."&tc=".$tc);
	return $url;
}


function openVerticalTab($tabs, $active=FALSE) {
	$res="";

	$res.="<div class=\"vertical_tab\">";
	$res.="<ul>";

	$content_style="";

	foreach($tabs as $tc=>$tab) {

		$selected=($active === $tc ? TRUE : FALSE);

		$res.="<li".(isset($tab["style"]) ? " class=\"".$tab["style"]."\"" : "").">";

		if (!$selected)
			$res.="<a href=\"".$tab["url"]."\" title=\"".$tab["title"]."\">";

		$res.="<img src=\"".$tab["image"]."\" alt=\"".$tab["title"]."\" />";

		if (!$selected)
			$res.="</a>";

		$res.="</li>\n";

		if ($selected) {
			if (isset($tab["content_style"]))
				$content_style=" ".$tab["content_style"];
			else if (isset($tab["style"]))
				$content_style=" ".$tab["style"];
		}
	}

	$res.="</ul>";
	$res.="<div class=\"tab_content".$content_style."\">";

	return $res;
}


function closeVerticalTab() {
	$res="";

	$res.="</div>"; // tab_content
	$res.="</div>"; // vertical_tab

	return $res;
}


function getActiveVTab($type, $id) {
	$res=0;

	if (isset($_SESSION["vertical_tab"][$type][$id]))
		$res=$_SESSION["vertical_tab"][$type][$id];

	return $res;
}


function setActiveVTab($type, $id, $value, $jump_url=FALSE) {

	$_SESSION["vertical_tab"][$type][$id]=$value;

	$anchor=FALSE;

	switch($type) {
		case "project": {
			$anchor="#project".$id;
		} break;
		case "task": {
			$anchor="#task".$id;
		} break;
	}

	if ($jump_url !== FALSE) {
		jumpTo($jump_url, $anchor);
	}
}


function showProjectInfo($prj_id) {
	$res="";

	$lang=& DoceboLanguage::createInstance("company", "framework");
	$cm=new CompanyManager(); // TODO: optimize this; cause is a double instance

	$info=$cm->getProjectInfo($prj_id);
	$cm->initProjectConstants();

	$status_arr=$cm->getStatusArray($lang);
	$priority_arr=$cm->getPriorityArray($lang);

 	$res.="<table class=\"project_info\" cellspacing=\"0\" summary=\"".$lang->def("_TAB_PROJECT_INFO_SUMMARY")."\">\n";
	$res.="<caption>".$lang->def("_TAB_PROJECT_INFO_CAPTION")."</caption>\n<tbody>\n";

	$i=0;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_PROJECT_NAME").":</th>";
	$res.="<td class=\"".$class."\">".$info["name"]."</td></tr>\n";

	if (getPLSetting("crm", "use_simplified", "off") == "off") {
		$i=!$i;
		$class="line-".(int)($i);
		$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_PROJECT_COST").":</th>";
		$res.="<td class=\"".$class."\">".$info["cost"]."</td></tr>\n";

		$i=!$i;
		$class="line-".(int)($i);
		$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_PROJECT_GAIN").":</th>";
		$res.="<td class=\"".$class."\">".$info["gain"]."</td></tr>\n";

		$i=!$i;
		$class="line-".(int)($i);
		$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_PRIORITY").":</th>";
		$res.="<td class=\"".$class."\">".$priority_arr[$info["priority"]]."</td></tr>\n";
	}

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_STATUS").":</th>";
	$res.="<td class=\"".$class."\">".$status_arr[$info["status"]]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_PROGRESS").":</th>";
	$res.="<td class=\"".$class."\">".$info["progress"]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_SIGNED_ON").":</th>";
	$res.="<td class=\"".$class."\">".$info["sign_date"]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_EXPIRE_ON").":</th>";
	$res.="<td class=\"".$class."\">".$info["expire"]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_DEADLINE").":</th>";
	$res.="<td class=\"".$class."\">".$info["deadline"]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_TICKETS").":</th>";
	$res.="<td class=\"".$class."\">".$info["ticket"]."</td></tr>\n";


	$res.="</tbody>\n</table>\n";

	return $res;
}


function getProjectTasks($company_id, $prj_id) {
	$res="";

	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_COMPANY_TASK_TAB_CAPTION");
	$table_summary=$lang->def("_COMPANY_TASK_TAB_SUMMARY");


	$tab=new typeOne(0, $table_caption, $table_summary);
	$tab->setTableStyle("company_child_table");


	$head=array($lang->def("_TASK_NAME"));
	$head[]=$lang->def("_PROGRESS");
	$head[]=$lang->def("_PRIORITY");
	$head[]=$lang->def("_STATUS");
	$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;

	$head_type=array("", "", "", "", "image", "image", "image", "image");


	$tab->setColsStyle($head_type);
	$tab->addHead($head);



	$cm=new CompanyManager();
	$list=$cm->getTaskList($company_id, $prj_id);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	$cm->initProjectConstants();
	$status_arr=$cm->getStatusArray($lang);
	$priority_arr=$cm->getPriorityArray($lang);

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++) {

		$rowcnt=array();

		$id=$list_arr[$i]["task_id"];
		$prj_id=$list_arr[$i]["prj_id"];
		$name=$list_arr[$i]["name"];

		$show_details=(isset($_SESSION["show_task_details"][$id]) ? TRUE : FALSE);


		$anchor="<a name=\"task".$id."\" id=\"task".$id."\" />\n";
		$url=$um->getUrl("op=details&id=".$company_id."&tab_op=toggletaskinfo&task_id=".$id."&prj_id=".$prj_id);
		if ($show_details) {
			$img ="<img src=\"".getPathImage('fw')."standard/less.gif\" alt=\"".$lang->def("_LESSINFO")." ".$name."\" ";
			$img.="title=\"".$lang->def("_LESSINFO")." ".$name."\" />";
		}
		else {
			$img ="<img src=\"".getPathImage('fw')."standard/more.gif\" alt=\"".$lang->def("_MOREINFO")." ".$name."\" ";
			$img.="title=\"".$lang->def("_MOREINFO")." ".$name."\" />";
		}
		$rowcnt[]=$anchor."<a href=\"".$url."\">".$img."</a><a href=\"".$url."\">".$name."</a>\n";

		$rowcnt[]=drawProgressBar($list_arr[$i]["progress"]);

		$rowcnt[]=$priority_arr[$list_arr[$i]["priority"]];
		$rowcnt[]=$status_arr[$list_arr[$i]["status"]];

		$url=$um->getUrl("op=details&id=".$company_id."&tab_op=edittask&task_id=".$id."&prj_id=".$prj_id);
		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")." ".$name."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

		$rowcnt[]="&nbsp;";

		$tab->addBody($rowcnt);

		if ($show_details)
			$tab->addBodyExpanded(showTaskDetails($company_id, $prj_id, $id), "task_details");
	}


	$url=$um->getUrl("op=details&id=".$company_id."&prj_id=".$prj_id."&tab_op=addtask");
	$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".$lang->def('_ADD')."</a>\n");


	$res.=$tab->getTable();

	return $res;
}


function addeditTask($company_id, $prj_id, $task_id=0) {
	$res="";

	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form=new Form();
	$cm=new CompanyManager();


	$form_code="";
	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=savetask");

	$form_code=$form->openForm("main_form", $url);

	if ($task_id > 0) { // Edit

		$submit_lbl=$lang->def("_SAVE");

		$info=$cm->getTaskInfo($task_id);

		$name=$info["name"];
		$start_date=$info["start_date"];
		$end_date=$info["end_date"];
		$expire=$info["expire"];
		$status=$info["status"];
		$priority=$info["priority"];
		$progress=$info["progress"];

	}
	else { // Add

		$submit_lbl=$lang->def("_INSERT");

		$cm->initProjectConstants();

		$name="";
		$start_date="";
		$end_date="";
		$expire="";
		$status=STATUS_WAITINGSIG;
		$priority=PRIORITY_LOW;
		$progress="0";

	}

	$status_arr=$cm->getStatusArray($lang);
	$priority_arr=$cm->getPriorityArray($lang);

	$date_format=FALSE;

	$res.=$form_code.$form->openElementSpace();

	$res.=$form->getTextfield($lang->def("_PROJECT_NAME").":", "name", "name", 255, $name);
	$res.=$form->getDatefield($lang->def("_START_DATE").":", "start_date", "start_date", $start_date, $date_format, true);
	$res.=$form->getDatefield($lang->def("_END_DATE").":", "end_date", "end_date", $end_date, $date_format, true);
	$res.=$form->getDatefield($lang->def("_EXPIRE_ON").":", "expire", "expire", $expire, $date_format, true);
	$res.=$form->getDropdown($lang->def("_PRIORITY").":", "priority", "priority", $priority_arr, $priority);
	$res.=$form->getDropdown($lang->def("_STATUS").":", "status", "status", $status_arr, $status);
	$res.=$form->getTextfield($lang->def("_PROGRESS").":", "progress", "progress", 3, $progress);


	$res.=$form->getHidden("task_id", "task_id", $task_id);
	$res.=$form->getHidden("prj_id", "prj_id", $prj_id);
	$res.=$form->getHidden("company_id", "company_id", $company_id);


	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $submit_lbl);
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	return $res;
}


function saveTask($company_id) {

	$um=& UrlManager::getInstance();
	$cm=new CompanyManager();
	$cm->saveTask($_POST);

	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=showprojects");

	jumpTo($url);
}


function toggleTaskDetails($company_id, $task_id) {

	if (isset($_SESSION["show_task_details"][$task_id]))
		unset($_SESSION["show_task_details"][$task_id]);
	else
		$_SESSION["show_task_details"][$task_id]=1;

	$anchor="#task".$task_id;

	$um=& UrlManager::getInstance();

	jumpTo($um->getUrl("op=details&tab_op=showprojects&id=".$company_id), $anchor);
}


function showTaskDetails($company_id, $prj_id, $task_id) {
	$res="";

	$lang=& DoceboLanguage::createInstance("company", "framework");

	$active=getActiveVTab("task", $task_id);

	if (empty($active))
		$active="details";

	$tabs=array();
	$tc="details"; // tab code
	$tabs[$tc]["image"]=getPathImage()."standard/details.gif";
	$tabs[$tc]["title"]=$lang->def("_TASK_DETAILS");
	$tabs[$tc]["url"]=getTaskVTabUrl($company_id, $task_id, $tc);
	$tabs[$tc]["style"]="task_details";
	// $tabs[$i]["content_style"]="test";
	$tc="attach";
	$tabs[$tc]["image"]=getPathImage()."standard/attach.gif";
	$tabs[$tc]["title"]=$lang->def("_TASK_ATTACHMENTS");
	$tabs[$tc]["url"]=getTaskVTabUrl($company_id, $task_id, $tc);
	$tabs[$tc]["style"]="attachments";
	$tc="comments";
	$tabs[$tc]["image"]=getPathImage()."standard/chat.gif";
	$tabs[$tc]["title"]=$lang->def("_TASK_COMMENTS");
	$tabs[$tc]["url"]=getTaskVTabUrl($company_id, $task_id, $tc);
	$tabs[$tc]["style"]="comments";
	$tc="notes";
	$tabs[$tc]["image"]=getPathImage()."standard/notes.gif";
	$tabs[$tc]["title"]=$lang->def("_TASK_NOTES");
	$tabs[$tc]["url"]=getTaskVTabUrl($company_id, $task_id, $tc);
	$tabs[$tc]["style"]="notes";


	//$res.="<a name=\"task".$task_id."\" />\n";

	$res.=openVerticalTab($tabs, $active); // --------------------------------------------

	switch($active) {

		case "details": {
			$res.=showTaskInfo($task_id);
		} break;

		case "attach": {
			$res.=showAttachments("task", $company_id, $task_id);
		} break;

		case "notes": {
			$res.=showNotes("task", $company_id, $task_id);
		} break;

		case "comments": {
			$res.=showComments("task", $company_id, $task_id);
		} break;

	}

	$res.=closeVerticalTab(); // ------------------------------------------------

	return $res;
}


function getTaskVTabUrl($company_id, $task_id, $tc) {
	$um=& UrlManager::getInstance();

	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=seltaskvtab&task_id=".$task_id."&tc=".$tc);
	return $url;
}


function showTaskInfo($task_id) {
	$res="";

	$lang=& DoceboLanguage::createInstance("company", "framework");
	$cm=new CompanyManager(); // TODO: optimize this; cause is a double instance

	$info=$cm->getTaskInfo($task_id);
	$cm->initProjectConstants();

	$status_arr=$cm->getStatusArray($lang);
	$priority_arr=$cm->getPriorityArray($lang);

 	$res.="<table class=\"task_info\" cellspacing=\"0\" summary=\"".$lang->def("_TAB_TASK_INFO_SUMMARY")."\">\n";
	$res.="<caption>".$lang->def("_TAB_TASK_INFO_CAPTION")."</caption>\n<tbody>\n";

	$i=0;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_PROJECT_NAME").":</th>";
	$res.="<td class=\"".$class."\">".$info["name"]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_START_DATE").":</th>";
	$res.="<td class=\"".$class."\">".$info["start_date"]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_END_DATE").":</th>";
	$res.="<td class=\"".$class."\">".$info["end_date"]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_EXPIRE_ON").":</th>";
	$res.="<td class=\"".$class."\">".$info["expire"]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_PRIORITY").":</th>";
	$res.="<td class=\"".$class."\">".$priority_arr[$info["priority"]]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_STATUS").":</th>";
	$res.="<td class=\"".$class."\">".$status_arr[$info["status"]]."</td></tr>\n";

	$i=!$i;
	$class="line-".(int)($i);
	$res.="<tr><th scope=\"col\" class=\"".$class."\">".$lang->def("_PROGRESS").":</th>";
	$res.="<td class=\"".$class."\">".$info["progress"]."</td></tr>\n";


	$res.="</tbody>\n</table>\n";

	return $res;
}


function showAttachments($type, $company_id, $parent_id) {
	$res="";

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");

	$um=& UrlManager::getInstance();
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$form=new Form();

	$cm=new CompanyManager();
	$list=$cm->getFileList($type, $parent_id);
	$file_list=$list["data_arr"];
	$db_tot=$list["data_tot"];

	foreach($file_list as $attach) {

		$fname=$attach["fname"];

		$url=$um->getUrl("op=details&id=".$company_id."&tab_op=download&type=".$type."&file_id=".$attach["file_id"]);
		$img="<img src=\"".getPathImage('fw').mimeDetect($fname)."\" alt=\"".$fname."\" title=\"".$fname."\" />\n";
		$link ="<a href=\"".$url."\">".$img."</a> <a href=\"".$url."\">".$fname."</a>";

		$res.="<div class=\"attach_line\">";
		$res.="<b>".$link."</b>\n";
		$res.="<p class=\"attach_description\">".$attach["description"]."</p>\n";
		$res.="</div>\n";

	}

	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=uploadfile");
	$res.=$form->openForm("upload_form", $url, "", "", "multipart/form-data");

	$res.=$form->openElementSpace();

	$res.=$form->getFilefield($lang->def("_FILE"), "file", "file", 255);
	$res.=$form->getTextfield($lang->def("_DESCRIPTION"), "filedesc", "filedesc", 255);

	$res.=$form->getHidden("file_id", "file_id", 0);
	$res.=$form->getHidden("type", "type", $type);
	$res.=$form->getHidden("parent_id", "parent_id", $parent_id);

	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $lang->def("_UPLOAD"));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	return $res;
}


function saveFile($company_id) {

	$um=& UrlManager::getInstance();
	$cm=new CompanyManager();
	$cm->saveFile($_POST);

	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=showprojects");

	jumpTo($url);
}


function downloadFile() {
	// TODO: check perm [?]

	$type=(isset($_GET["type"]) ? $_GET["type"] : "");
	$file_id=(isset($_GET["file_id"]) ? (int)$_GET["file_id"] : 0);

	if (($file_id < 1) || (empty($type)))
		return "";

	$cm=new CompanyManager();
	$cm->downloadFile($file_id);
}


function showNotes($type, $company_id, $parent_id) {
	$res="";

	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	switch($type) {

		case "project": {
			$type_pfx="prj";
		} break;

		case "task": {
			$type_pfx="task";
		} break;

	}

	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=add".$type_pfx."note&".$type_pfx."_id=".$parent_id);
	$add_link="<a class=\"new_element_link\" href=\"".$url."\">".$lang->def("_ADD_NOTE")."</a>";


	$cm=new CompanyManager();

	$list=$cm->getNoteList($type, $parent_id);
	$note_list=$list["data_arr"];
	$user_info=$list["user_info"];
	$db_tot=$list["data_tot"];


	$tot=count($note_list);


	if (($tot == 0) || ($tot > 3)) {
		$res.="<div class=\"note_add_box_top\">";
		$res.=$add_link;
		$res.="</div>\n";
	}


	$acl_manager=$GLOBALS["current_user"]->getAclManager();

	foreach($note_list as $note) {

		$id=$note["note_id"];

		$actions="";
		$url_qry ="op=details&id=".$company_id."&tab_op=edit".$type_pfx."note";
		$url_qry.="&".$type_pfx."_id=".$parent_id."&note_id=".$id;
		$url=$um->getUrl($url_qry);
		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$actions.="<a href=\"".$url."\">".$img."</a> ";
		$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
		$img.="title=\"".$lang->def("_DEL")."\" />";
		$actions.=$img;


		$url=$um->getUrl("op=details&id=".$company_id."&tab_op=toggle".$type_pfx."note&note_id=".$id);

		$show_details=(isset($_SESSION["show_note_details"][$id]) ? TRUE : FALSE);

		if ($show_details) {
			$img ="<img src=\"".getPathImage('fw')."standard/less.gif\" alt=\"".$lang->def("_LESSINFO")."\" ";
			$img.="title=\"".$lang->def("_LESSINFO")."\" />";
			$note_class="note_box_opened";
		}
		else {
			$img ="<img src=\"".getPathImage('fw')."standard/more.gif\" alt=\"".$lang->def("_MOREINFO")."\" ";
			$img.="title=\"".$lang->def("_MOREINFO")."\" />";
			$note_class="note_box_closed";
		}
		$toggle="<a href=\"".$url."\">".$img."</a>\n";

		$author=$acl_manager->relativeId($user_info[$note["note_author"]][ACL_INFO_USERID]);

		$res.="<div class=\"".$note_class."\">";
		$res.="<div class=\"toggle_box\">".$toggle."</div>";
		$res.="<div class=\"actions\">".$actions."</div>";
		$res.="<div class=\"note_date\">".$GLOBALS["regset"]->databaseToRegional($note["note_date"])."</div>";
		$res.="<div class=\"author\"><b>".$author."</b></div>";
		$res.="<div class=\"no_float\"></div>";
		$res.="<p>".$note["note_txt"]."</p>";
		$res.="</div>\n";

	}


	if ($tot > 0) {
		$res.="<div class=\"note_add_box_bottom\">";
		$res.=$add_link;
		$res.="</div>\n";
	}

	return $res;
}


function showComments($type, $company_id, $parent_id) {
	$res="";

	$um=& UrlManager::getInstance();

	switch($type) {

		case "project": {
			$key="project_comment";
			$anchor=	"#project".$parent_id;
			$active="P".$parent_id;
		} break;

		case "task": {
			$key="task_comment";
			$anchor=	"#task".$parent_id;
			$active="T".$parent_id;
		} break;

	}


	require_once($GLOBALS["where_framework"]."/lib/lib.sysforum.php");

	$sf=new sys_forum("crm", $key, $parent_id);
	$sf->setPrefix("crm");
	$sf->can_write=TRUE; // TODO : set the right permissions according to the $type val.
	$sf->can_moderate=TRUE;
	$sf->can_upload=TRUE;


	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=showprojects");

	$sf->setAnchor($anchor);
	$sf->setActive($active);

	$sf->url=$url;
	$res.=$sf->show(FALSE);

	return $res;
}


function addeditNote($type, $company_id, $parent_id, $note_id=0) {
	$res="";

	$um=& UrlManager::getInstance();
	$lang=& DoceboLanguage::createInstance("company", "framework");

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form=new Form();
	$cm=new CompanyManager();


	$form_code="";
	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=savenote");

	$form_code=$form->openForm("note_form", $url);

	if ($note_id > 0) { // Edit

		$submit_lbl=$lang->def("_SAVE");
		$info=$cm->getNoteInfo($note_id);

		$note_txt=$info["note_txt"];
	}
	else { // Add

		$submit_lbl=$lang->def("_INSERT");

		$note_txt="";
	}

	$res.=$form_code.$form->openElementSpace();

	$res.=$form->getTextarea($lang->def("_NOTE_TEXT").":", "note_txt", "note_txt", $note_txt);


	$res.=$form->getHidden("note_id", "note_id", $note_id);
	$res.=$form->getHidden("parent_id", "parent_id", $parent_id);
	$res.=$form->getHidden("company_id", "company_id", $company_id);
	$res.=$form->getHidden("type", "type", $type);


	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $submit_lbl);
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	return $res;
}


function saveNote($company_id) {

	$um=& UrlManager::getInstance();

	$cm=new CompanyManager();
	$cm->saveNote($_POST);

	$url=$um->getUrl("op=details&id=".$company_id."&tab_op=showprojects");

	jumpTo($url);
}


function toggleNoteDetails($type, $company_id, $parent_id, $note_id) {

	$um=& UrlManager::getInstance();

	if (isset($_SESSION["show_note_details"][$note_id]))
		unset($_SESSION["show_note_details"][$note_id]);
	else
		$_SESSION["show_note_details"][$note_id]=1;

	switch ($type) {

		case "prject": {
			$anchor="#project".$parent_id;
		} break;

		case "task": {
			$anchor="#task".$parent_id;
		} break;

	}

	jumpTo($um->getUrl("op=details&tab_op=showprojects&id=".$company_id), $anchor);
}


function ticketUrlManagerSetup($company_id=FALSE) {
	require_once($GLOBALS["where_framework"]."/lib/lib.urlmanager.php");

	if (!isset($GLOBALS["url_manager"]))
		$GLOBALS["url_manager"]=new UrlManager();

	$um=& UrlManager::getInstance();

	$std_query="modname=company&op=details";
	if ($company_id !== FALSE)
		$std_query.="&id=".(int)$company_id;

	$query_map=array("op"=>"tab_op");

	$um->setUseModRewrite(FALSE);
	$um->setIgnoreItems(array("company"));
	$um->addToStdQuery($std_query);
	$um->setQueryMap($query_map);

}


function ctmSetup($company_id=FALSE) {
	require_once($GLOBALS["where_crm"]."/modules/ticket/lib.ticket.php");

	ticketUrlManagerSetup($company_id);
	$ctm=new CustomerTicketManager();

	$ctm->setIsStaff(TRUE);
	$ctm->setTicketCompany(array($company_id));
	$ctm->setShowTitleArea(FALSE);
	$ctm->setShowBackUi(FALSE);

	return $ctm;
}


function showCompanyTickets($company_id) {
	$res="";

	$ctm=ctmSetup($company_id);
	$res.=$ctm->showCompanyTicket($company_id);

	return $res;
}


function showTicket($company_id) {
	$res="";

	$ctm=ctmSetup($company_id);
	$res.=$ctm->showTicket();

	return $res;
}


function addTicket($company_id) {
	$res="";

	$ctm=ctmSetup($company_id);
	$res.=$ctm->addTicket();

	return $res;
}


function createTicket($company_id) {

	$ctm=ctmSetup($company_id);
	$ctm->createTicket();

}


function editTicket($company_id) {
	$res="";

	$ctm=ctmSetup($company_id);
	$res.=$ctm->editTicket();

	return $res;
}


function saveTicket($company_id) {

	$ctm=ctmSetup($company_id);
	$ctm->saveTicket();

}


function addeditTicketMsg($company_id) {
	$res="";

	$ctm=ctmSetup($company_id);
	$res.=$ctm->addeditMessage();

	return $res;
}


function saveTicketMsg($company_id){

	$ctm=ctmSetup($company_id);
	$ctm->saveTicketMessage();

}


function switchTicketLock($company_id){

	$ctm=ctmSetup($company_id);
	$res=$ctm->switchTicketLock();

	return $res;
}


function assignTicketUsers($company_id) {

	// This function will send information directly to output
	// take a look to case "assignticketusers" in function
	// showCompanyDetails()

	$ctm=ctmSetup($company_id);
	$ctm->assignTicketUsers();

}


function setTicketOrder($company_id) {

	$ctm=ctmSetup($company_id);
	$ctm->setTicketOrder();

}


function doOpenTask($company_id, $prj_id, $task_id) {

	$um=& UrlManager::getInstance();

	unset($_SESSION["show_project_details"]);
	$_SESSION["show_project_details"][$prj_id]=1;

	$_SESSION["show_task_details"][$task_id]=1;

	$anchor="#task".$task_id;

	jumpTo($um->getUrl("op=details&tab_op=showprojects&id=".$company_id), $anchor);
}


function todomanSetup($company_id=FALSE) {
	require_once($GLOBALS["where_crm"]."/modules/todo/lib.todo.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.urlmanager.php");


	$um=& UrlManager::getInstance();

	$todoman=new TodoManager();
	$std_query ="modname=company&op=details";
	if ($company_id !== FALSE)
		$std_query.="&id=".(int)$company_id;

	$um->addToStdQuery($std_query);
	$um->setIgnoreItems(array("company"));

	$todoman->setTodoCompany($company_id);
	$todoman->setShowTitleArea(FALSE);
	$todoman->setShowBackUi(FALSE);

/*
	$ctm->setIsStaff(TRUE);
*/
	return $todoman;
}


function showCompanyTodo($company_id) {
	$res="";

	$todoman=todomanSetup($company_id);
	$res.=$todoman->showCompanyTodo($company_id);

	return $res;
}


function addTodo($company_id) {
	$res="";

	$todoman=todomanSetup($company_id);
	$res.=$todoman->addeditTodo();

	return $res;
}


function saveTodo($company_id) {

	$todoman=todomanSetup($company_id);
	$todoman->saveTodo();

}


function editTodo($company_id) {
	$res="";

	if ((isset($_GET["todo_id"])) && ($_GET["todo_id"] > 0))
		$todo_id=$_GET["todo_id"];
	else
		return "";

	$todoman=todomanSetup($company_id);
	$res.=$todoman->addeditTodo($todo_id);

	return $res;
}


function switchTodoComplete($company_id){

	$todoman=todomanSetup($company_id);
	$todoman->switchTodoComplete();

}


function toggleTodoDetails($company_id){

	$todoman=todomanSetup($company_id);
	$todoman->toggleTodoDetails();

}


function setTodoOrder($company_id) {

	$todoman=todomanSetup($company_id);
	$todoman->setTodoOrder();

}


function chmSetup($company_id=FALSE) {
	require_once($GLOBALS["where_crm"]."/modules/contacthistory/lib.contacthistory.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.urlmanager.php");


	$um=& UrlManager::getInstance();

	$chm=new ContactHistoryManager();
	$std_query ="modname=company&op=details";
	if ($company_id !== FALSE)
		$std_query.="&id=".(int)$company_id;

	$um->addToStdQuery($std_query);
	$um->setIgnoreItems(array("company"));

	$chm->setContactHistoryCompany($company_id);
	$chm->setShowTitleArea(FALSE);
	$chm->setShowBackUi(FALSE);

/*
	$ctm->setIsStaff(TRUE);
*/
	return $chm;
}


function showCompanyContactHistory($company_id) {
	$res="";

	$chm=chmSetup($company_id);
	$res.=$chm->showCompanyContactHistory($company_id);

	return $res;
}


function addContactHistory($company_id) {
	$res="";

	$chm=chmSetup($company_id);
	$res.=$chm->addeditContactHistory();

	return $res;
}


function saveContactHistory($company_id) {

	$chm=chmSetup($company_id);
	$chm->saveContactHistory();

}


function editContactHistory($company_id) {
	$res="";

	if ((isset($_GET["contact_id"])) && ($_GET["contact_id"] > 0))
		$contact_id=$_GET["contact_id"];
	else
		return "";

	$chm=chmSetup($company_id);
	$res.=$chm->addeditContactHistory($contact_id);

	return $res;
}


function switchContactHistoryComplete($company_id){

	$chm=chmSetup($company_id);
	$chm->switchContactHistoryComplete();

}


function toggleContactHistoryDetails($company_id){

	$chm=chmSetup($company_id);
	$chm->toggleContactHistoryDetails();

}


function setContactHistoryOrder($company_id) {

	$chm=chmSetup($company_id);
	$chm->setContactHistoryOrder();

}


function printCompanySearchForm() {
	$res="";

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
	$form=new Form();

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("company");

	$um=& UrlManager::getInstance();
	$cm=new CompanyManager();
	$lang=& DoceboLanguage::createInstance("company", "framework");

	if (isset($_POST["do_search"])) {
		$search->setSearchItem("search_key");
		$search->setSearchItem("company_type");
		$search->setSearchItem("company_status");
		$search->setSearchItem("recall");
		$search->setSearchItem("assigned_to_me");
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
	$company_type=$search->getSearchItem("company_type", "int");
	$company_status=$search->getSearchItem("company_status", "int");
	$recall=$search->getSearchItem("recall", "string");
	$assigned_to_me=$search->getSearchItem("assigned_to_me", "bool");

	$res.=$form->getTextfield($search->lang->def("_SEARCH_KEY"), "search_key", "search_key", 255, $search_key);

	$type_list=$cm->getCompanyTypeList(TRUE);
	$type_arr=$type_list["list"];
	$res.=$form->getDropdown($lang->def("_COMPANY_TYPE"), "company_type", "company_type", $type_arr, $company_type);

	$status_list=$cm->getCompanyStatusList(TRUE);
	$status_arr=$status_list["list"];
	$res.=$form->getDropdown($lang->def("_COMPANY_STATUS"), "company_status", "company_status", $status_arr, $company_status);

	$recall_type_arr =array();
	$recall_type_arr["-1"]=$lang->def("_ANY");
	$recall_type_arr["expired"]=$lang->def("_EXPIRED");
	$recall_type_arr["today"]=$lang->def("_TODAY");
	$recall_type_arr["tomorrow"]=$lang->def("_TOMORROW");
	$recall_type_arr["in_a_week"]=$lang->def("_IN_A_WEEK");
	$recall_type_arr["after_a_week"]=$lang->def("_AFTER_A_WEEK");
	$res.=$form->getDropdown($lang->def("_RECALL_ON_FILTER"), "recall", "recall", $recall_type_arr, $recall);

	$res.=$form->getCheckbox($lang->def("_ASSIGNED_TO_ME"), "assigned_to_me", "assigned_to_me", 1, $assigned_to_me);

	// --------------------------------------------------------------------------
	$res.=$search->closeSearchForm($form);

	return $res;
}


function showhideCompanySearchForm() {

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$um=& UrlManager::getInstance();
	$search=new SearchUI("company");

	$search->showHideSearchForm();

	$url=$um->getUrl();
	jumpTo($url);
}


function setCompanyPermission() {
	require_once($GLOBALS['where_framework']."/class.module/class.directory.php");
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

	$cm =new CompanyManager();
	$mdir=new Module_Directory();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();
	$ccm=& $cm->ccManager; //new CoreCompanyManager();

	$company_id=(int)importVar("id", true);

	$back_url=$um->getUrl("op=main");


	if( isset($_POST['okselector']) ) {

		// TODO: check (edit) perm

		$start_sel=$mdir->getStartSelection();
		$arr_selection=array_diff($mdir->getSelection($_POST), $start_sel);
		$arr_deselected = $mdir->getUnselected();

		$ccm->saveCompanyPerm($company_id, $arr_selection, $arr_deselected);
		$cm->saveAssignedLog($company_id, $arr_selection, $arr_deselected);

		jumpTo($back_url);
	}
	else if( isset($_POST['cancelselector']) ) {
		jumpTo($back_url);
	}
	else {

		$mdir->setNFields(1);
		$mdir->show_group_selector=true;
		$mdir->show_orgchart_selector=false;

		if( !isset($_GET['stayon']) ) {
			$perm=$ccm->loadCompanyPerm($company_id);
			$mdir->resetSelection(array_keys($perm["view"]));
		}


		$regusers_idst=$mdir->aclManager->getGroupRegisteredId();
		$mdir->setUserFilter("group", array($regusers_idst));


		$back_ui_url=$um->getUrl("op=main");

		$url=$um->getUrl("op=setpermission&id=".$company_id."&stayon=1");
		$mdir->loadSelector($url, $lang->def('_BUG_ASSIGNED_USERS'), "", TRUE);

		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	}

}


function confirmCompanyCreation() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	if ((isset($_GET["id"])) && ($_GET["id"] > 0))
		$id=$_GET["id"];
	else {
		jumpTo($um->getUrl());
		die();
	}

	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	$res="";

	$cm=new CompanyManager();


	$stored=$cm->getCompanyInfo($id);
	$name=$stored["name"];
	$page_title=$lang->def("_COMPANY_CREATED").": ".$name;
	$back_ui_url=$um->getUrl("op=details&id=".$id);

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY");
	$title_arr[]=$page_title;

	$res.=getCmsTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

	$res.=getInfoUi($lang->def("_NO_USER_PERM_SET"));
	$url=$um->getUrl("op=setpermission&id=".$id);
	$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$lang->def("_ALT_ASSIGNPERM")."\" ";
	$img.="title=\"".$lang->def("_ALT_ASSIGNPERM")."\" />";
	$res.="<div class=\"company_confirm_actions\">";
	$res.="<a href=\"".$url."\">".$img.$lang->def("_ALT_ASSIGNPERM")."</a>\n";
	$res.="</div>\n";

	$res.=getInfoUi($lang->def("_NO_COMPANY_USER_SEL"));

	$add_txt="";
	$url=$um->getUrl("op=createuser&id=".$id);
	$img="<img src=\"".getPathImage()."standard/adduser.gif\" alt=\"".$lang->def('_ADD_USER')."\" />";
	$add_txt.="<a href=\"".$url."\">".$img.$lang->def('_ADD_USER')."</a>\n";
	$url=$um->getUrl("op=selectusers&id=".$id);
	$img="<img src=\"".getPathImage('fw')."standard/addandremove.gif\" alt=\"".$lang->def('_SELECT_USERS')."\" />";
	$add_txt.="<a href=\"".$url."\">".$img.$lang->def('_SELECT_USERS')."</a>\n";
	$res.="<div class=\"company_confirm_actions\">".$add_txt."</div>\n";

	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}


function manageDemoUser() {
	$res="";
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_crm'].'/modules/company/demo_user.php');

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$form =new Form();
	$um=& UrlManager::getInstance();
	$um->setBaseUrl("popup.php");


	if ((isset($_GET["id"])) && ($_GET["id"] > 0)) {
		$id =$_GET["id"];
	}
	else {
		jumpTo($um->getUrl("close_popup=1", FALSE));
	}

	if ((isset($_GET["user_idst"])) && ($_GET["user_idst"] > 0)) {
		$user_idst =$_GET["user_idst"];
	}
	else {
		$user_idst =0;
	}

	$form_code ="";
	$url =$um->getUrl("op=save_demo_user");
	$form_code.=$form->openForm("main_form", $url);
	$form_code.=$form->openElementSpace();

	if ($user_idst < 1) {
		$page_title =$lang->def("_ADD_DEMO_USER");
		$submit_lbl =$lang->def("_CREATE");

		$user_id ="staff.";
		$pass ="cambiami";
	}
	else {
		$page_title =$lang->def("_EDIT_DEMO_USER");
		$submit_lbl =$lang->def("_SAVE");

		$user_info =get_demo_user_info($user_idst);

		$user_id =$user_info[ACL_INFO_USERID];
		$pass =$user_info[ACL_INFO_PASS];
	}

	$form_code.=$form->getTextfield($lang->def("_USERNAME"), "user_id", "user_id", 255, $user_id);
	$form_code.=$form->getTextfield($lang->def("_PASSWORD"), "user_pass", "user_pass", 255, $pass);

	$form_code.=$form->getHidden("id", "id", (int)$id);
	$form_code.=$form->getHidden("user_idst", "user_idst", (int)$user_idst);

	$form_code.=$form->closeElementSpace();
	$form_code.=$form->openButtonSpace();
	$form_code.=$form->getButton('save', 'save', $submit_lbl);
	$form_code.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$form_code.=$form->closeButtonSpace();
	$form_code.=$form->closeForm();

	$back_ui_url =$um->getUrl("close_popup=1");
	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY");
	$title_arr[]=$page_title;

	$res.=getCmsTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

	$res.=$form_code;

	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}


function saveDemoUser() {
	require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');
	require_once($GLOBALS['where_crm'].'/modules/company/demo_user.php');
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");


	$user_info[ACL_INFO_USERID] ="/".$_POST["user_id"];
	$user_info[ACL_INFO_PASS] =md5($_POST["user_pass"]);

	$user_idst =remote_transfer($user_info);

	$cm=new CompanyManager();
	$cm->saveCompanyDemoUser((int)$_POST["id"], $user_idst);

	$um=& UrlManager::getInstance();
	$um->setBaseUrl("popup.php");
	jumpTo($um->getUrl("close_popup=1", FALSE));
}


function companyQuickEditSave($company_id) {
		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
		$ccm=new CoreCompanyManager();
		$table =$ccm->getCompanyTable();

		$qtxt ="UPDATE ".$table." SET notes='".$_POST["notes"]."', ";
		$qtxt.="recall_on='".$GLOBALS["regset"]->regionalToDatabase($_POST["recall_on"])."' ";
		$qtxt.="WHERE company_id='".(int)$company_id."'";
		$q =mysql_query($qtxt);

		return $q;
}


// ----------------------------------------------------------------------------

$op=(isset($_GET["op"]) ? $_GET["op"] : "main");


if (isset($_GET["set_wp_search"])) {
	require_once($GLOBALS["where_framework"]."/lib/lib.search.php");
	$search=new SearchUI("company");
	$search->clearSearchFilter();
	if ((isset($_GET["assigned_to_me"])) && ($_GET["assigned_to_me"] == 1)) {
		$search->setSearchItem("assigned_to_me", 1);
	}
	else {
		$search->setSearchItem("assigned_to_me", 0);
	}
	if (!empty($_GET["set_wp_search"])) {
		$search->setSearchItem($_GET["set_wp_search"], $_GET["search"]);
	}
}


switch ($op) {

	case "main": {
		company();
	} break;

	case "add": {
		addeditCompany();
	} break;

	case "save": {
		if (!isset($_POST["undo"]))
			saveCompany();
		else {
			if (isset($_POST["edit"])) {
				$um=& UrlManager::getInstance();
				jumpTo($um->getUrl("op=details&id=".$_POST["id"]));
			}
			else if (!defined("POPUP_MODE")) {
				company();
			}
			else {
				$um=& UrlManager::getInstance();
				jumpTo($um->getUrl("close_popup=1"));
			}
		}
	} break;

	case "del": {
		deleteCompany();
	} break;

	case "selectusers": {
		selCompanyUsers();
	} break;

	case "createuser": {
		addeditCompanyUser();
	} break;

	case "edituser": {
		addeditCompanyUser($_GET["userid"]);
	} break;

	case "deluser": {
		deleteCompanyUser($_GET["userid"]);
	} break;

	case "details": {
		showCompanyDetails();
	} break;

	case "showhidesearchform": {
		showhideCompanySearchForm();
	} break;

	case "setpermission": {
		setCompanyPermission();
	} break;

	case "confcreation": {
		confirmCompanyCreation();
	} break;

	case "manage_demo_user": {
		manageDemoUser();
	} break;

	case "save_demo_user": {
		if (isset($_POST["undo"])) {
			$um =& UrlManager::getInstance();
			jumpTo($um->getUrl("close_popup=1", FALSE));
		}
		else {
			saveDemoUser();
		}
	} break;


	case "quickeditsave": {
		$company_id =(int)$_GET["id"];
		if (!isset($_POST["undo"])) {
			companyQuickEditSave($company_id);
		}
		$um=& UrlManager::getInstance();
		jumpTo($um->getUrl("op=details&id=".$company_id));
	} break;


}

?>