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

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');


class ContactHistoryManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	// ContactHistoryDataManager
	var $chdm=NULL;

	var $lang=NULL;
	var $is_staff=FALSE;
	var $show_backui=TRUE;
	var $show_title_area=TRUE;


	function ContactHistoryManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_crm"]);
		$this->dbconn=$dbconn;

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
		$this->chdm=new ContactHistoryDataManager();

		$this->lang=& DoceboLanguage::createInstance('contacthistory', "crm");
	}


	function setIsStaff($val) {
		$this->is_staff=(bool)$val;
	}


	function getIsStaff() {
		return $this->is_staff;
	}

	function titleArea($text, $image = '', $alt_image = '') {
		$res="";

		if (!$this->showTitleArea())
			return "";

		if ($GLOBALS["platform"] == "cms") {
			$res=getCmsTitleArea($text, $image = '', $alt_image = '');
		}
		else {
			$res=getTitleArea($text, $image = '', $alt_image = '');
		}

		return $res;
	}


	function getBackUi($url, $label) {

		if ($this->showBackUi())
			$res=getBackUi($url, $label);
		else
			$res="";

		return $res;
	}


	function setShowTitleArea($val) {
		$this->show_title_area=(bool)$val;
	}


	function showTitleArea() {
		return $this->show_title_area;
	}


	function setShowBackUi($val) {
		$this->show_backui=(bool)$val;
	}


	function showBackUi() {
		return $this->show_backui;
	}


	function showCompanySelect() {
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$res="";

		$um=& UrlManager::getInstance();

		$back_ui_url="back";
		//$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		$title_arr=array();
		$title_arr[$um->getUrl()]=$this->lang->def("_CHISTORYS");
		$title_arr[]=$this->lang->def("_CHISTORY_SELECT_COMPANY");
		$res.=$this->titleArea($title_arr, "contacthistory");


		$table_caption=$this->lang->def("_CHISTORY_SELECT_COMPANY");
		$table_summary=$this->lang->def("_CHISTORY_SELECT_COMPANY_SUMMARY");

		$tab=new typeOne(0, $table_caption, $table_summary);
		$tab->setTableStyle("contacthistory_table");

		$head=array($this->lang->def("_COMPANY_NAME"));
		$head_type=array("");

		$tab->addHead($head);
		$tab->setColsStyle($head_type);


		$company_arr=$this->getContactHistoryCompany();
		$available_company=$this->ccManager->getCompanyList(FALSE, FALSE, $company_arr);
		$company_list=$available_company["data_arr"];

		foreach ($company_list as $company) {

			$rowcnt=array();

			$id=$company["company_id"];

			$url=$um->getUrl("tab_op=contacthistory&company=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$company["name"]."</a>\n";

			$tab->addBody($rowcnt);
		}


		$res.=$tab->getTable();

		// ------------------------------------------------------------------------
		//$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		return $res;
	}


	function setContactHistoryCompany($company_id) {

		$_SESSION["contacthistory_company"]=(int)$company_id;

	}


	function getContactHistoryCompany() {
		if (isset($_SESSION["contacthistory_company"]))
			return (int)$_SESSION["contacthistory_company"];
		else
			return 0;
	}


	function getCurrentCompanyId() {

		$company_id=$this->getContactHistoryCompany();

		if ($company_id > 0) {
			return $company_id;
		}
		else if (isset($_GET["company"])) {
			return (int)$_GET["company"];
		}
		else {
			return 0;
		}

	}


	function checkCompanyPerm($company_id, $staff_required=FALSE) {
		/*
	}
		$company_arr=$this->getContactHistoryCompany();
		if (!in_array($company_id, $company_arr))
			die("You can't access!");

		if (($staff_required) && (!$this->getIsStaff()))
			die("You can't access!"); */

		return TRUE;
	}


	function showCompanyContactHistory($company_id) {
		$res="";
		$this->checkCompanyPerm($company_id);

		$um=& UrlManager::getInstance();

		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
		$table_caption=$this->lang->def("_CHISTORY_TABLE_CAPTION");
		$table_summary=$this->lang->def("_CHISTORY_TABLE_SUMMARY");

		$vis_item=$GLOBALS["visuItem"];

		$tab=new typeOne($vis_item, $table_caption, $table_summary);


		$is_staff=$this->getIsStaff();

		$head=array();
		$img ="<img src=\"".getPathImage()."standard/calendar.gif\" alt=\"".$this->lang->def("_ALT_CHISTORY")."\" ";
		$img.="title=\"".$this->lang->def("_ALT_CHISTORY")."\" />";
		$head[]=$img;
		$ord_url=$um->getUrl("tab_op=contacthistorysetorder&company=".$company_id."&ord=title");
		$head[]="<a href=\"".$ord_url."\">".$this->lang->def("_TITLE")."</a>";
		$ord_url=$um->getUrl("tab_op=contacthistorysetorder&company=".$company_id."&ord=type");
		$head[]="<a href=\"".$ord_url."\">".$this->lang->def("_CHISTORY_TYPE")."</a>";
		$ord_url=$um->getUrl("tab_op=contacthistorysetorder&company=".$company_id."&ord=date");
		$head[]="<a href=\"".$ord_url."\">".$this->lang->def("_DATE")."</a>";

		$head_type=array("image", "", "", "");


		// Complete / incomplete
		$img ="<img src=\"".getPathImage()."contacthistory/complete.gif\" alt=\"".$this->lang->def("_CHISTORY_CONTACT_REASON")."\" ";
		$img.="title=\"".$this->lang->def("_CHISTORY_CONTACT_REASON")."\" />";
		$ord_url=$um->getUrl("tab_op=contacthistorysetorder&company=".$company_id."&ord=closed");
		$head[]="<a href=\"".$ord_url."\">".$img."</a>";
		$head_type[]="image";

		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
		$img.="title=\"".$this->lang->def("_MOD")."\" />";
		$head[]=$img;
		$head_type[]="image";



		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink("index.php");

		$ini=$tab->getSelectedElement();


		$where="t1.company_id='".$company_id."'";

		$acl_manager=$GLOBALS["current_user"]->getAclManager();
		$user_idst=$GLOBALS["current_user"]->getIdST();
		$roles=$acl_manager->getUserRoleFromPath($user_idst, "/crm/contacthistory", "assigned");

		$level=$GLOBALS["current_user"]->getUserLevelId();
		$is_admin=($level == ADMIN_GROUP_ADMIN ? TRUE : FALSE);
		$is_god_admin=($level == ADMIN_GROUP_GODADMIN ? TRUE : FALSE);

		// If user is not a admin / god admin then apply permission restrictions
		if ((!$is_god_admin) && (!$is_admin)) {
			$where.=" AND ";

			$where.="((t1.author = '".$GLOBALS["current_user"]->getIdSt()."')";
			if (($roles !== FALSE) && (is_array($roles["role_info"]) && (count($roles["role_info"]) > 0))) {
				$where.=" OR (t1.contact_id IN (".implode(",", $roles["role_info"]).")))";
			}
			else
				$where.=")";

			$where.=" ";
		}

		$list=$this->chdm->getContactHistoryList($ini, $vis_item, $where);
		$list_arr=$list["data_arr"];
		$db_tot=$list["data_tot"];

		$type_arr=$this->chdm->getTypeArray();
		$reason_arr=$this->chdm->getReasonArray();

		$tot=count($list_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$list_arr[$i]["contact_id"];

			$rowcnt=array();

			$img ="<img src=\"".getPathImage()."standard/calendar.gif\" alt=\"".$this->lang->def("_ALT_CHISTORY")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_CHISTORY")."\" />";
			$rowcnt[]=$img;


			$show_details=(isset($_SESSION["show_contacthistory_details"][$id]) ? TRUE : FALSE);

			$url_qry ="op=details&id=".$company_id;
			$url_qry.="&tab_op=togglecontacthistorydetails&contact_id=".$id;
			$short_lbl=substr($list_arr[$i]["title"], 0 , 20)."...";
			$url=$um->getUrl($url_qry);
			if ($show_details) {
				$img ="<img src=\"".getPathImage('fw')."standard/less.gif\" alt=\"".$this->lang->def("_LESSINFO")." ".$short_lbl."\" ";
				$img.="title=\"".$this->lang->def("_LESSINFO")." ".$short_lbl."\" />";
			}
			else {
				$img ="<img src=\"".getPathImage('fw')."standard/more.gif\" alt=\"".$this->lang->def("_MOREINFO")." ".$short_lbl."\" ";
				$img.="title=\"".$this->lang->def("_MOREINFO")." ".$short_lbl."\" />";
			}
			$rowcnt[]="<a href=\"".$url."\">".$img.$list_arr[$i]["title"]."</a>\n";

			$rowcnt[]=$type_arr[$list_arr[$i]["type"]];
			$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["meeting_date"]);


			if ($is_staff) {
				$locked_msg=$this->lang->def("_MARK_INCOMPLETE");
				$unlocked_msg=$this->lang->def("_MARK_COMPLETE");
			}
			else {
				$locked_msg=$this->lang->def("_COMPLETE");
				$unlocked_msg=$this->lang->def("_INCOMPLETE");
			}


			$rowcnt[]=$reason_arr[$list_arr[$i]["reason_id"]];


			$url=$um->getUrl("tab_op=editcontacthistory&company=".$company_id."&contact_id=".$id);
			$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>";

			$tab->addBody($rowcnt);

			if ($show_details) {
				$tab->addBodyExpanded($list_arr[$i]["description"], "line_details");
			}
		}


		$url=$um->getUrl("tab_op=addcontacthistory&company=".$company_id);
		$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".$this->lang->def('_ADD')."</a>\n");


		$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}



	function addeditContactHistory($contact_id=0) {
		$res="";

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$form=new Form();
		$um=& UrlManager::getInstance();


		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id);

		$back_ui_url=$um->getUrl("tab_op=contacthistory&company=".$company_id);
		$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		$title_arr=array();
		$title_arr[$um->getUrl()]=$this->lang->def("_CHISTORYS");
		$title_arr[$back_ui_url]="nome azienda";

		if ($contact_id > 0) {
			$title_arr[]=$this->lang->def("_EDIT_CHISTORY");
			$submit_lbl=$this->lang->def('_SAVE');

			$info=$this->chdm->getContactHistoryInfo($contact_id);

			$title=$info["title"];
			$reason=$info["reason_id"];
			$type=$info["type"];
			$description=$info["description"];
			$meeting_date=$GLOBALS["regset"]->databaseToRegional($info["meeting_date"]);

		}
		else {
			$title_arr[]=$this->lang->def("_ADD_CHISTORY");
			$submit_lbl=$this->lang->def('_CREATE');

			$title="";
			$reason=FALSE;
			$type=FALSE;
			$description="";
			$meeting_date="";
		}

		$res.=$this->titleArea($title_arr, "contacthistory");


		$url=$um->getUrl("tab_op=savecontacthistory&company=".$company_id);
		$res.=$form->openForm("main_form", $url);
		$res.=$form->openElementSpace();

		$res.=$form->getTextfield($this->lang->def("_TITLE").":", "title", "title", 255, $title);

		$type_arr=$this->chdm->getTypeArray();
		$res.=$form->getDropdown($this->lang->def("_CHISTORY_TYPE"), "type", "type", $type_arr, $type);

		$reason_arr=$this->chdm->getReasonArray();
		$res.=$form->getDropdown($this->lang->def("_CHISTORY_CONTACT_REASON"), "reason", "reason", $reason_arr, $reason);

		$res.=$form->getDatefield($this->lang->def("_DATE"), "meeting_date", "meeting_date", $meeting_date);

		$res.=$form->getTextarea($this->lang->def("_CHISTORY_DESCRIPTION").":", "description", "description", $description);

		$res.=$form->getHidden("contact_id", "contact_id", $contact_id);

		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $submit_lbl);
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		// ------------------------------------------------------------------------
		$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		return $res;
	}


	function saveContactHistory() {

		$company_id=$this->getCurrentCompanyId();

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl("tab_op=contacthistory&company=".$company_id, FALSE);

		if (isset($_POST["undo"])) {
			jumpTo($back_url);
			die();
		}

		$this->chdm->saveContactHistory($company_id);

		jumpTo($back_url);
	}


	function switchContactHistoryComplete() {
		$company_id=$this->getCurrentCompanyId();

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl("tab_op=contacthistory&company=".$company_id, FALSE);

		if ((isset($_GET["contact_id"])) && ($_GET["contact_id"] > 0)) {
			$this->chdm->switchContactHistoryComplete($_GET["contact_id"]);
		}

		jumpTo($back_url);
	}


	function toggleContactHistoryDetails() {

		$company_id=$this->getCurrentCompanyId();

		if ((isset($_GET["contact_id"])) && ($_GET["contact_id"] > 0)) {

			$contact_id=$_GET["contact_id"];

			if (isset($_SESSION["show_contacthistory_details"][$contact_id]))
				unset($_SESSION["show_contacthistory_details"][$contact_id]);
			else
				$_SESSION["show_contacthistory_details"][$contact_id]=1;

		}

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl("tab_op=contacthistory&company=".$company_id, FALSE);
		jumpTo($back_url);
	}


	function setContactHistoryOrder() {

		$um=& UrlManager::getInstance();
		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id);

		if ((isset($_GET["ord"])) && (!empty($_GET["ord"])))
			$this->chdm->setContactHistoryOrder($_GET["ord"]);

		$url=$um->getUrl("tab_op=contacthistory&company=".$company_id, FALSE);
		jumpTo($url);
	}


}










class ContactHistoryDataManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	// CoreCompanyManager
	var $ccManager=NULL;

	var $lang=NULL;
	var $is_staff=FALSE;
	var $show_backui=TRUE;
	var $show_title_area=TRUE;

	var $contacthistory_info=array();


	function ContactHistoryDataManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_crm"]);
		$this->dbconn=$dbconn;

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
		$this->ccManager=new CoreCompanyManager();

		$this->lang=& DoceboLanguage::createInstance('contacthistory', "crm");
	}


	function _executeQuery( $query ) {
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- debug $query -->", 'debug' );
		else echo "\n<!-- debug $query -->";
		if( $this->dbconn === NULL )
			$rs = mysql_query( $query );
		else
			$rs = mysql_query( $query, $this->dbconn );
		return $rs;
	}


	function _executeInsert( $query ) {
		if( $GLOBALS['do_debug'] == 'on' ) $GLOBALS['page']->add( "\n<!-- debug $query -->" , 'debug' );
		if( $this->dbconn === NULL ) {
			if( !mysql_query( $query ) )
				return FALSE;
		} else {
			if( !mysql_query( $query, $this->dbconn ) )
				return FALSE;
		}
		if( $this->dbconn === NULL )
			return mysql_insert_id();
		else
			return mysql_insert_id($this->dbconn);
	}


	function _getContactHistoryTable() {
		return $this->prefix."_contact_history";
	}


	function getTypeArray($include_any=FALSE) {

		$res=array();

		if ($include_any)
			$res[0]=def("_ANY", "search", "framework");

		$res["form"]=$this->lang->def("_TYPE_FORM");
		$res["email"]=$this->lang->def("_EMAIL");
		$res["phone"]=$this->lang->def("_TYPE_PHONE");
		$res["meeting"]=$this->lang->def("_TYPE_MEETING");

		return $res;
	}


	function getReasonArray($include_any=FALSE) {
		$res=array();
		require_once($GLOBALS["where_crm"]."/admin/modules/contactreason/lib.contactreason.php");

		$crm=new ContactReasonManager();

		$reason_list=$crm->getContactReasonList();
		$reason_arr=$reason_list["data_arr"];

		if ($include_any)
			$res[0]=def("_ANY", "search", "framework");

		foreach($reason_arr as $reason) {

			$res[$reason["reason_id"]]=$reason["label"];

		}

		return $res;
	}


	function getContactHistoryOrder() {

		$field=(isset($_SESSION["contacthistory_order"]["field"]) ? $_SESSION["contacthistory_order"]["field"] : "t1.meeting_date");
		$type=(isset($_SESSION["contacthistory_order"]["type"]) ? $_SESSION["contacthistory_order"]["type"] : "DESC");

		$res=array();
		$res["field"]=$field;
		$res["type"]=$type;

		return $res;
	}


	function getContactHistoryList($ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
		$cm=new CompanyManager();

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="t1.*, t2.name as company_name";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getContactHistoryTable()." as t1, ";
		$qtxt.=$cm->getMainTable()." as t2 ";

		$qtxt.="WHERE t1.company_id=t2.company_id ";
		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}


		$ord=$this->getContactHistoryOrder();
		$qtxt.="ORDER BY ".$ord["field"]." ".$ord["type"]." ";
		$q=$this->_executeQuery($qtxt);

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$qtxt.="LIMIT ".$ini.",".$vis_item;
			$q=$this->_executeQuery($qtxt);
		}

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {

				$id=$row["contact_id"];
				$data_info["data_arr"][$i]=$row;
				$this->contacthistory_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadContactHistoryInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getContactHistoryTable()." ";
		$qtxt.="WHERE contact_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getContactHistoryInfo($id) {

		if (!isset($this->contacthistory_info[$id]))
			$this->contacthistory_info[$id]=$this->loadContactHistoryInfo($id);

		return $this->contacthistory_info[$id];
	}


	function saveContactHistory($company_id, $data=FALSE) {

		if ($data === FALSE)
			$data=$_POST;

		$contact_id=(int)$data["contact_id"];
		$title=$data["title"];
		$description=$data["description"];
		$reason=(int)$data["reason"];
		$type=substr($data["type"], 0, 20);
		$meeting_date=$GLOBALS["regset"]->regionalToDatabase($data["meeting_date"]);


		if ((int)$contact_id > 0) {

			$qtxt ="UPDATE ".$this->_getContactHistoryTable()." SET title='".$title."', type='".$type."', ";
			$qtxt.="reason_id='".$reason."', meeting_date='".$meeting_date."', description='".$description."' ";
			$qtxt.="WHERE contact_id='".$contact_id."' LIMIT 1";
			$q=$this->_executeQuery($qtxt);

		}
		else {

			$field_list ="company_id, title, type, ";
			$field_list.="reason_id, meeting_date, description";
			$field_val ="'".(int)$company_id."', '".$title."', '".$type."', ";
			$field_val.="'".$reason."', '".$meeting_date."', '".$description."'";

			$qtxt="INSERT INTO ".$this->_getContactHistoryTable()." (".$field_list.") VALUES(".$field_val.")";
			$contact_id=$this->_executeInsert($qtxt);
		}

		return $contact_id;
	}


	function setContactHistoryOrder($ord) {

		switch ($ord) {
			case "title": {
				$field="t1.title";
				$default_type="ASC";
			} break;
			case "type": {
				$field="t1.type";
				$default_type="ASC";
			} break;
			case "reason": {
				$field="t1.reason_id";
				$default_type="ASC";
			} break;
			case "date": {
				$field="t1.meeting_date";
				$default_type="DESC";
			} break;
			case "company": {
				$field="t2.name";
				$default_type="ASC";
			} break;
		}

		if ((isset($_SESSION["contacthistory_order"]["field"])) &&
		    ($field == $_SESSION["contacthistory_order"]["field"])) {

			if ($_SESSION["contacthistory_order"]["type"] == "ASC")
				$_SESSION["contacthistory_order"]["type"]="DESC";
			else
				$_SESSION["contacthistory_order"]["type"]="ASC";
		}
		else {
			$_SESSION["contacthistory_order"]["field"]=$field;
			$_SESSION["contacthistory_order"]["type"]=$default_type;
		}


	}


	/**
	 * if company_id is set will delete the whole contact history
	 * of the specified company
	 */
	function deleteContactHistory($contact_id, $company_id=FALSE) {

		$qtxt ="DELETE FROM ".$this->_getContactHistoryTable()." WHERE ";


		if (($contact_id > 0) && ($company_id === FALSE)) {
			$qtxt.="contact_id='".$contact_id."' LIMIT 1";
		}
		else if (($company_id > 0) && ($contact_id === FALSE)) {
			$qtxt.="company_id='".$company_id."'";
		}
		else {
			return FALSE;
		}

		$q=$this->_executeQuery($qtxt);

		return $q;
	}


}


?>
