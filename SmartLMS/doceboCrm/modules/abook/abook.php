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
cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=crm&pi=".getPI()."&modname=abook&op=main");
// -----------------------

require_once($GLOBALS["where_crm"]."/modules/abook/lib.abook.php");


function abook() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("abook", "crm");
	$company_lang=& DoceboLanguage::createInstance("company", "framework");

	$um=& UrlManager::getInstance();

	require_once($GLOBALS["where_framework"]."/lib/lib.field.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_ABOOK_TABLE_CAPTION");
	$table_summary=$lang->def("_ABOOK_TABLE_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];

	$abm=new AddressBookManager();

	$fl =new FieldList();
	//$fl->setFieldEntryTable($abm->ccm->getCompanyFieldEntryTable());


	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_ABOOK");
	$res.=getCmsTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$tab=new typeOne($vis_item, $table_caption, $table_summary);
	$tab->setTableStyle("company_child_table");

	$base_url="op=setorder&ord=";

	$head=array();
	$img ="<img src=\"".getPathImage('fw')."standard/user.gif\" alt=\"".$lang->def("_USER")."\" ";
	$img.="title=\"".$lang->def("_USER")."\" />";
	$head[]=$img;
	$head[]="<a href=\"".$um->getUrl($base_url."name")."\">".$lang->def("_FIRSTNAME")."</a>";
	$head[]="<a href=\"".$um->getUrl($base_url."company")."\">".$lang->def("_COMPANY")."</a>";
	$head[]=$lang->def("_EMAIL");
	$head[]=$lang->def("_PHONE");
	$head[]=$lang->def("_ROLE_IN_COMPANY");
	$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;


	$head_type=array("image", "", "", "", "", "", "image", "image");


	$res.=printSearchForm();


	$res.=printAddExtraForm();


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$tab->initNavBar('ini', 'link');
	$tab->setLink($um->getUrl());

	if ((isset($_SESSION["abook_ini"])) && (!isset($_GET["ini"]))) {
		$ini =(int)$_SESSION["abook_ini"];
	}
	else {
		$ini =$tab->getSelectedElement();
		$_SESSION["abook_ini"] =$ini;
	}


	$where =getABookSearchQuery();
	$ord =getABookOrder();
	$order_by =$ord["field"]." ".$ord["type"]." ";

	$list=$abm->ccm->getAllCompanyUsers($ini, $vis_item, $where, TRUE, $order_by);
	$list_arr=$list["data_arr"];

/*
	$user_arr=array();
	foreach($list["data_arr"] as $data) {

		if (!in_array($data["user_id"], $user_arr))
			$user_arr[]=$data["user_id"];

	}

	$acl_manager=& $GLOBALS["current_user"]->getAclManager();
	$user_info=$acl_manager->getUsers($user_arr);

	$i=0;
	foreach($user_info as $user) {

		$list_arr[$i]["user_id"]=$user[ACL_INFO_IDST];
		$list_arr[$i]["company_id"]="";
		$list_arr[$i]["name"]=$list[$user[ACL_INFO_IDST]];

		$i++;
	}
*/
	//echo $where; print_r($list_arr);
	$db_tot=$list["data_tot"];





	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["user_id"];
		$company_id=$list_arr[$i]["company_id"];

		$base_url=$um->getUrl("modname=company&op=details&id=".$company_id);

		$rowcnt=array();

		$img ="<img src=\"".getPathImage('fw')."standard/user.gif\" alt=\"".$lang->def("_USER")."\" ";
		$img.="title=\"".$lang->def("_USER")."\" />";
		$rowcnt[]=$img;

		$toggle_id=$id."_".$company_id;

		$show_details=(isset($_SESSION["show_abook_user_details"][$toggle_id]) ? TRUE : FALSE);

		$anchor_name ="jump_to_".$toggle_id;
		$url_qry="&op=toggleuserdetails&company_id=".$company_id."&toggle_id=".$toggle_id."#".$anchor_name;
		$short_lbl=$list["user"][$id];
		$url=$um->getUrl($url_qry);
		if ($show_details) {
			$img ="<img src=\"".getPathImage('fw')."standard/less.gif\" alt=\"".$lang->def("_LESSINFO")." ".$short_lbl."\" ";
			$img.="title=\"".$lang->def("_LESSINFO").": ".$short_lbl."\" />";
		}
		else {
			$img ="<img src=\"".getPathImage('fw')."standard/more.gif\" alt=\"".$lang->def("_MOREINFO")." ".$short_lbl."\" ";
			$img.="title=\"".$lang->def("_MOREINFO").": ".$short_lbl."\" />";
		}
		$rowcnt[]="<a name=\"".$anchor_name."\" href=\"".$url."\">".$img.$list["user"][$id]."</a>\n";

		$rowcnt[]=$list_arr[$i]["company_name"];

		$email =$list["user_email"][$id];
		$cnt =(!empty($email) ? '<a href="mailto:'.$email.'">' : '&nbsp;');
		$cnt.=$email.(!empty($email) ? '</a>' : '');
		$rowcnt[]=$cnt;


		$fields_val =$fl->showFieldForUserArr(array($id), array(38, 10));
		$rowcnt[]=$fields_val[$id][10];
		$rowcnt[]=$fields_val[$id][38];

		//$url=$base_url."&tab_op=edituser&userid=".$id."&backto=abook";
		$url=$um->getUrl("modname=company&id=".$company_id."&op=edituser&userid=".$id."&backto=abook");
		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>";

		$url=$um->getUrl("modname=company&id=".$company_id."&op=deluser&userid=".$id."&backto=abook");
		$img ="<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
		$img.="title=\"".$lang->def("_DEL")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>";

		$tab->addBody($rowcnt);
		if ($show_details) {
			$tab->addBodyExpanded($abm->ccm->getUserInfo($id, $company_lang), "user_details");
		}
	}


	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	$res.="</div>\n";
	$out->add($res);
}


function getABookOrder() {

	$field=(isset($_SESSION["abook_order"]["field"]) ? $_SESSION["abook_order"]["field"] : "t2.name");
	$type=(isset($_SESSION["abook_order"]["type"]) ? $_SESSION["abook_order"]["type"] : "ASC");

	$res=array();
	$res["field"]=$field;
	$res["type"]=$type;

	return $res;
}


function setABookOrder() {

	$um=& UrlManager::getInstance();
	$back_url =$um->getUrl();

	if ((isset($_GET["ord"])) && (!empty($_GET["ord"]))) {
		$ord =$_GET["ord"];
	}
	else {
		jumpTo($back_url);
		return TRUE;
	}

	switch ($ord) {
		case "name": {
			$field="t1.user_id";
			$default_type="ASC";
		} break;
		case "company": {
			$field="t2.name";
			$default_type="ASC";
		} break;
	}

	if ((isset($_SESSION["abook_order"]["field"])) &&
			($field == $_SESSION["abook_order"]["field"])) {

		if ($_SESSION["abook_order"]["type"] == "ASC")
			$_SESSION["abook_order"]["type"]="DESC";
		else
			$_SESSION["abook_order"]["type"]="ASC";
	}
	else {
		$_SESSION["abook_order"]["field"]=$field;
		$_SESSION["abook_order"]["type"]=$default_type;
	}

	jumpTo($back_url);
}


function printSearchForm() {
	$res="";

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
	$form=new Form();

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("abook");

	$abm=new AddressBookManager();
	$lang=& DoceboLanguage::createInstance("task", "crm");
	$company_lang=& DoceboLanguage::createInstance("company", "framework");

	$um=& UrlManager::getInstance();

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
//	$priority=$search->getSearchItem("priority", "bool");
//	$status=$search->getSearchItem("status", "bool");
	//$openclose=$search->getSearchItem("openclose", "bool");
	$company=$search->getSearchItem("company", "bool");
//	$project=$search->getSearchItem("project", "bool");

	$res.=$form->getTextfield($search->lang->def("_SEARCH_KEY"), "search_key", "search_key", 255, $search_key);

/*	$priority_arr=$abm->cm->getPriorityArray($company_lang, TRUE);
	$res.=$form->getDropdown($lang->def("_TASK_PRIORITY"), "priority", "priority", $priority_arr, $priority);

	$status_arr=$abm->cm->getStatusArray($company_lang, TRUE);
	$res.=$form->getDropdown($lang->def("_STATUS"), "status", "status", $status_arr, $status);  */

/*	$openclose_arr=array();
	$openclose_arr[0]=$lang->def("_ANY");
	$openclose_arr[1]=$lang->def("_ONLY_OPEN");
	$openclose_arr[2]=$lang->def("_ONLY_CLOSED");
	$res.=$form->getDropdown($lang->def("_TASK_OPENCLOSE"), "openclose", "openclose", $openclose_arr, $openclose); */

	$company_arr=$abm->getCompanyArray(TRUE);
	$res.=$form->getDropdown($lang->def("_COMPANY"), "company", "company", $company_arr, $company);

/*	if ($company > 0) {
		$project_arr=$ptm->getProjectArray($company, TRUE);
		$res.=$form->getDropdown($lang->def("_TASK_PROJECT"), "project", "project", $project_arr, $project);
	}
	else {
		$res.=$form->getLineBox($lang->def("_TASK_PROJECT"), $lang->def("_SELECT_A_COMPANY"));
	}
*/
	// --------------------------------------------------------------------------
	$res.=$search->closeSearchForm($form);

	return $res;
}


function printAddExtraForm() {
	$res="";

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

	$lang=& DoceboLanguage::createInstance("company", "framework");

	$um=& UrlManager::getInstance();
	$abm=new AddressBookManager();


	addJs($GLOBALS["where_crm_relative"]."/modules/company/", "company.js");

	$um->setBaseUrl("popup.php");
	$url =$um->getUrl("op=add_extra");
	$um->setBaseUrl("index.php");
	$other =' target="my_mod_win" onSubmit="openFormPopup()"';


	$res.='<div class="add_extra_box">';
	$res.=Form::openForm("add_extra", $url, FALSE, FALSE, "", $other);


	$company_arr=$abm->getCompanyArray(FALSE, FALSE, TRUE);
	//$res.=Form::getDropdown($lang->def("_COMPANY"), "company", "company", $company_arr);

	$res.='<select id="company" name="company" class="abook_company_dropdown">';
	foreach($company_arr as $id=>$val) {
		$res.='<option value="'.$id.'">'.$val.'</option>';
	}
	$res.='</select>';

	$res.=Form::getButton('new_user', 'new_user', $lang->def("_ADD_USER"))."<br />\n";
	$res.=Form::getButton('new_company', 'new_company', $lang->def("_ADD_NEW_COMPANY"));

	$res.=Form::closeForm();
	$res.='</div>'; // add_extra_box

	return $res;
}


function addExtraJump() {
	$um=& UrlManager::getInstance();

	if (isset($_POST["new_company"])) {
		$url =$um->getUrl("modname=company&op=add");
	}
	else if ((isset($_POST["new_user"])) && ($_POST["company"] > 0)) {
		$url =$um->getUrl("modname=company&op=createuser&id=".(int)$_POST["company"]);
	}
	else {
		$url =$um->getUrl();
	}

	jumpTo($url);
}


function getABookSearchQuery() {
	$res=FALSE;
	$first=TRUE;

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("abook");

	$search_key=$search->getSearchItem("search_key", "string");
	$company=$search->getSearchItem("company", "bool");

	if (!empty($search_key)) {
		$res.=($first ? "" : " AND ");
		$acl_manager=& $GLOBALS["current_user"]->getAclManager();
		$internal_fields=array();
		$internal_fields[ACL_INFO_USERID]["filter"]=$search_key;
		$internal_fields[ACL_INFO_USERID]["like"]="both";
		$internal_fields[ACL_INFO_USERID]["nextop"]="OR";
		$found_users=$acl_manager->searchUsers($internal_fields);
		$res.="t1.user_id IN (".implode(",", $found_users).")";
		$first=FALSE;
	}

	if ($company > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.company_id='".$company."'";
		$first=FALSE;
	}
	return $res;
}


function showhideTaskSearchForm() {

	$um=& UrlManager::getInstance();
	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("abook");

	$search->showHideSearchForm();

	$url=$um->getUrl();
	jumpTo($url);
}


function toggleUserDetails() {
	$ok=TRUE;
	$um=& UrlManager::getInstance();

	if ((isset($_GET["toggle_id"])) && (!empty($_GET["toggle_id"])))
			$toggle_id=$_GET["toggle_id"];
	else
		$ok=FALSE;

	if ($ok) {
		if (isset($_SESSION["show_abook_user_details"][$toggle_id]))
			unset($_SESSION["show_abook_user_details"][$toggle_id]);
		else
			$_SESSION["show_abook_user_details"][$toggle_id]=1;
	}

	jumpTo($um->getUrl());
}


// ----------------------------------------------------------------------------

$op=(isset($_GET["op"]) ? $_GET["op"] : "main");

switch ($op) {

	case "main": {
		abook();
	} break;

	case "setorder": {
		setABookOrder();
	} break;

	case "showhidesearchform": {
		showhideABookSearchForm();
	} break;

	case "toggleuserdetails": {
		toggleUserDetails();
	} break;


	case "add_extra": {
		addExtraJump();
	} break;

}

?>