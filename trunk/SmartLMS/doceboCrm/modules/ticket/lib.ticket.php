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

class CustomerTicketManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	// CoreCompanyManager
	var $ccManager=NULL;

	// TicketManager
	var $tm=NULL;

	var $lang=NULL;
	var $is_staff=FALSE;
	var $show_backui=TRUE;
	var $show_title_area=TRUE;


	function CustomerTicketManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_crm"]);
		$this->dbconn=$dbconn;

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
		$this->ccManager=new CoreCompanyManager();

		require_once($GLOBALS["where_crm"]."/modules/ticket/lib.ticketmanager.php");
		$this->tm=new TicketManager();

		$this->lang=& DoceboLanguage::createInstance('ticket', "crm");
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


	function _getTicketTable() {
		return $this->prefix."_ticket";
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

		$um=& $GLOBALS["url_manager"];

		$back_ui_url="back";
		//$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		$title_arr=array();
		$title_arr[$um->getUrl()]=$this->lang->def("_TICKETS");
		$title_arr[]=$this->lang->def("_TICKET_SELECT_COMPANY");
		$res.=$this->titleArea($title_arr, "ticket");


		$table_caption=$this->lang->def("_TICKET_SELECT_COMPANY");
		$table_summary=$this->lang->def("_TICKET_SELECT_COMPANY_SUMMARY");

		$tab=new typeOne(0, $table_caption, $table_summary);
		$tab->setTableStyle("ticket_table");

		$head=array($this->lang->def("_COMPANY_NAME"));
		$head_type=array("");

		$tab->addHead($head);
		$tab->setColsStyle($head_type);


		$company_arr=$this->getTicketCompany();
		$available_company=$this->ccManager->getCompanyList(FALSE, FALSE, $company_arr);
		$company_list=$available_company["data_arr"];

		foreach ($company_list as $company) {

			$rowcnt=array();

			$id=$company["company_id"];

			$url=$um->getUrl("op=ticket&company=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$company["name"]."</a>\n";

			$tab->addBody($rowcnt);
		}


		$res.=$tab->getTable();

		// ------------------------------------------------------------------------
		//$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		return $res;
	}


	function setTicketCompany($company_arr) {

		$_SESSION["ticket_company"]=(array)$company_arr;

	}


	function getTicketCompany() {
		if (isset($_SESSION["ticket_company"]))
			return (array)$_SESSION["ticket_company"];
		else
			return array();
	}


	function getCurrentCompanyId() {

		$company_arr=$this->getTicketCompany();
		$tot=count($company_arr);

		if ($tot == 1) {
			return (int)$company_arr[0];
		}
		else if (($tot > 1) && (isset($_GET["company"]))) {
			return (int)$_GET["company"];
		}

	}


	function checkCompanyPerm($company_id, $staff_required=FALSE) {
		$company_arr=$this->getTicketCompany();
		if (!in_array($company_id, $company_arr))
			die("You can't access!");

		if (($staff_required) && (!$this->getIsStaff()))
			die("You can't access!");
	}


	function showCompanyTicket($company_id) {
		$res="";
		$this->checkCompanyPerm($company_id);

		$um=& $GLOBALS["url_manager"];

		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
		$table_caption=$this->lang->def("_TICKET_TABLE_CAPTION");
		$table_summary=$this->lang->def("_TICKET_TABLE_SUMMARY");

		$vis_item=$GLOBALS["visuItem"];

		$tab=new typeOne($vis_item, $table_caption, $table_summary);
		$tab->setTableStyle("ticket_table");


		$is_staff=$this->getIsStaff();

		$head=array();
		$img ="<img src=\"".getPathImage()."ticket/tickets.gif\" alt=\"".$this->lang->def("_ALT_TICKET")."\" ";
		$img.="title=\"".$this->lang->def("_ALT_TICKET")."\" />";
		$head[]=$img;
		$ord_url=$um->getUrl("op=ticketsetorder&company=".$company_id."&ord=title");
		$head[]="<a href=\"".$ord_url."\">".$this->lang->def("_TICKET_MESSAGE")."</a>";
		$ord_url=$um->getUrl("op=ticketsetorder&company=".$company_id."&ord=status");
		$head[]="<a href=\"".$ord_url."\">".$this->lang->def("_STATUS")."</a>";
		$ord_url=$um->getUrl("op=ticketsetorder&company=".$company_id."&ord=date");
		$head[]="<a href=\"".$ord_url."\">".$this->lang->def("_DATE")."</a>";

		$head_type=array("image", "", "", "");

		if ($is_staff) {
			$img ="<img src=\"".getPathImage('fw')."standard/groups.gif\" alt=\"".$this->lang->def("_ALT_ASSIGNUSERS")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_ASSIGNUSERS")."\" />";
			$head[]=$img;
			$head_type[]="image";
		}

		$img ="<img src=\"".getPathImage()."ticket/locked.gif\" alt=\"".$this->lang->def("_ALT_LOCKUNLOCK")."\" ";
		$img.="title=\"".$this->lang->def("_ALT_LOCKUNLOCK")."\" />";
		$ord_url=$um->getUrl("op=ticketsetorder&company=".$company_id."&ord=closed");
		$head[]="<a href=\"".$ord_url."\">".$img."</a>";
		$head_type[]="image";

		if ($is_staff) {
			$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$head[]=$img;
			$head_type[]="image";
		}


		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink("index.php");

		$ini=$tab->getSelectedElement();


		$where="t1.company_id='".$company_id."'";

		$acl_manager=$GLOBALS["current_user"]->getAclManager();
		$user_idst=$GLOBALS["current_user"]->getIdST();
		$roles=$acl_manager->getUserRoleFromPath($user_idst, "/crm/ticket", "assigned");

		$level=$GLOBALS["current_user"]->getUserLevelId();
		$is_admin=($level == ADMIN_GROUP_ADMIN ? TRUE : FALSE);
		$is_god_admin=($level == ADMIN_GROUP_GODADMIN ? TRUE : FALSE);

		// If user is not a admin / god admin then apply permission restrictions
		if ((!$is_god_admin) && (!$is_admin)) {
			$where.=" AND ";

			$where.="((t1.author = '".$GLOBALS["current_user"]->getIdSt()."')";
			if (($roles !== FALSE) && (is_array($roles["role_info"]) && (count($roles["role_info"]) > 0))) {
				$where.=" OR (t1.ticket_id IN (".implode(",", $roles["role_info"]).")))";
			}
			else
				$where.=")";

			$where.=" ";
		}

		$list=$this->tm->getTicketList($ini, $vis_item, $where);
		$list_arr=$list["data_arr"];
		$db_tot=$list["data_tot"];

		$status_list=$this->tm->getTicketStatusList();
		$status_arr=$status_list["list"];

		$tot=count($list_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$list_arr[$i]["ticket_id"];

			$rowcnt=array();
			//$rowcnt[]="<a href=\"".$url_details."\">".$list_arr[$i]["name"]."</a>\n";

			$img ="<img src=\"".getPathImage()."ticket/tickets.gif\" alt=\"".$this->lang->def("_ALT_TICKET")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_TICKET")."\" />";
			$rowcnt[]=$img;

			$url=$um->getUrl("op=showticket&company=".$company_id."&ticket_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$list_arr[$i]["subject"]."</a>\n";
			$rowcnt[]=$status_arr[$list_arr[$i]["status"]];
			$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["post_date"]);


			if ($is_staff) {
				$locked_msg=$this->lang->def("_UNLOCK_TICKET");
				$unlocked_msg=$this->lang->def("_LOCK_TICKET");
			}
			else {
				$locked_msg=$this->lang->def("_TICKET_LOCKED");
				$unlocked_msg=$this->lang->def("_TICKET_UNLOCKED");
			}


			if ($list_arr[$i]["closed"]) {
				$lock_img ="<img src=\"".getPathImage()."ticket/locked.gif\" alt=\"".$locked_msg."\" ";
				$lock_img.="title=\"".$locked_msg."\" />";
			}
			else {
				$lock_img ="<img src=\"".getPathImage()."ticket/unlocked.gif\" alt=\"".$unlocked_msg."\" ";
				$lock_img.="title=\"".$unlocked_msg."\" />";
			}

			if ($is_staff) {

				$url=$um->getUrl("op=assignticketusers&company=".$company_id."&ticket_id=".$id);
				$img ="<img src=\"".getPathImage('fw')."standard/groups.gif\" alt=\"".$this->lang->def("_ALT_ASSIGNUSERS")."\" ";
				$img.="title=\"".$this->lang->def("_ALT_ASSIGNUSERS")."\" />";
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>";

				$url=$um->getUrl("op=switchticketlock&company=".$company_id."&ticket_id=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$lock_img."</a>";

				$url=$um->getUrl("op=editticket&company=".$company_id."&ticket_id=".$id);
				$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
				$img.="title=\"".$this->lang->def("_MOD")."\" />";
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>";

			}
			else {
				$rowcnt[]=$lock_img;
			}

			$tab->addBody($rowcnt);
		}


		// Get projects that have at least one ticket available
		$prj_arr=$this->tm->getProjectArray($company_id, TRUE);

		if (count($prj_arr) > 0) {
			$url=$um->getUrl("op=addticket&company=".$company_id);
			$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".$this->lang->def('_ADD_TICKET')."</a>\n");
		}


		$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}


	function addTicket() {
		$res="";

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$form=new Form();
		$um=& $GLOBALS["url_manager"];


		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id);

		$back_ui_url=$um->getUrl("op=ticket&company=".$company_id);
		$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		$title_arr=array();
		$title_arr[$um->getUrl()]=$this->lang->def("_TICKETS");
		$title_arr[$back_ui_url]="nome azienda";
		$title_arr[]=$this->lang->def("_ADD_TICKET");
		$res.=$this->titleArea($title_arr, "ticket");


		$url=$um->getUrl("op=createticket&company=".$company_id);
		$res.=$form->openForm("main_form", $url);
		$res.=$form->openElementSpace();

		$res.=$form->getTextfield($this->lang->def("_TICKET_SUBJECT").":", "subject", "subject", 255);

		$prj_arr=$this->tm->getProjectArray($company_id);
		$res.=$form->getDropdown($this->lang->def("_TICKET_PROJECT"), "prj_id", "prj_id", $prj_arr);

		if ($this->getIsStaff()) {
			$status_list=$this->tm->getTicketStatusList();
			$status_arr=$status_list["list"];
			$res.=$form->getDropdown($this->lang->def("_STATUS"), "status", "status", $status_arr);
		}

		$res.=$form->getTextarea($this->lang->def("_TEXTOF").":", "text_msg", "text_msg");

		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $this->lang->def('_CREATE'));
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		// ------------------------------------------------------------------------
		$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		return $res;
	}


	function createTicket() {

		$um=& $GLOBALS["url_manager"];
		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id);

		if (!isset($_POST["undo"])) {
			$this->tm->createTicket($company_id, $_POST);
		}

		$url=$um->getUrl("op=ticket&company=".$company_id, FALSE);
		jumpTo($url);
	}


	function editTicket() {
		$res="";

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$form=new Form();
		$um=& $GLOBALS["url_manager"];


		if (!isset($_GET["ticket_id"]))
			return "";
		else
			$ticket_id=(int)$_GET["ticket_id"];


		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id, TRUE);

		$back_ui_url=$um->getUrl("op=ticket&company=".$company_id);
		$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		$title_arr=array();
		$title_arr[$um->getUrl()]=$this->lang->def("_TICKETS");
		$title_arr[$back_ui_url]="nome azienda";
		$title_arr[]=$this->lang->def("_EDIT_TICKET");
		$res.=$this->titleArea($title_arr, "ticket");


		$info=$this->tm->getTicketInfo($ticket_id);
		$subject=$info["subject"];
		$author=$info["author"];
		$prj_id=$info["prj_id"];
		$priority=$info["priority"];
		$status=$info["status"];

		$url=$um->getUrl("op=saveticket&company=".$company_id);
		$res.=$form->openForm("main_form", $url);

		$res.=$form->getTextfield($this->lang->def("_TICKET_SUBJECT").":", "subject", "subject", 255, $subject);

		/* $user_arr=$this->tm->getCompanyUserArray($company_id);
		$res.=$form->getDropdown($this->lang->def("_TICKET_USER"), "author", "author", $user_arr, $author); */

		$prj_arr=$this->tm->getProjectArray($company_id);
		$res.=$form->getDropdown($this->lang->def("_TICKET_PROJECT"), "prj_id", "prj_id", $prj_arr, $prj_id);

		$priority_arr=$this->tm->getPriorityArray($this->lang);
		$res.=$form->getDropdown($this->lang->def("_TICKET_PRIORITY"), "priority", "priority", $priority_arr, $priority);

		$status_list=$this->tm->getTicketStatusList();
		$status_arr=$status_list["list"];
		$res.=$form->getDropdown($this->lang->def("_STATUS"), "status", "status", $status_arr, $status);

		$res.=$form->getHidden("ticket_id", "ticket_id", $ticket_id);


		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $this->lang->def('_SAVE'));
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		// ------------------------------------------------------------------------
		$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		return $res;
	}


	function saveTicket() {

		$um=& $GLOBALS["url_manager"];
		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id, TRUE);

		if (!isset($_POST["undo"])) {
			$this->tm->saveTicket($company_id, $_POST);
		}

		$url=$um->getUrl("op=ticket&company=".$company_id, FALSE);
		jumpTo($url);
	}


	function showTicket() {
		$res="";

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$form=new Form();
		$um=& $GLOBALS["url_manager"];

		if (!isset($_GET["ticket_id"]))
			return "";
		else
			$ticket_id=(int)$_GET["ticket_id"];

		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id);


		$back_ui_url=$um->getUrl("op=ticket&company=".$company_id);
		$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		$info=$this->tm->getTicketInfo($ticket_id);

		$title_arr=array();
		$title_arr[$um->getUrl()]=$this->lang->def("_TICKETS");
		$title_arr[$back_ui_url]="nome azienda";
		$title_arr[]=$info["subject"];
		$res.=$this->titleArea($title_arr, "ticket");


		$res.=$this->getTicketInfoText($info);


		$url=$um->getUrl("op=addticketreply&company=".$company_id."&ticket_id=".$ticket_id);
		$add_link="<a class=\"new_element_link\" href=\"".$url."\">".$this->lang->def("_ADD_REPLY")."</a>";


		$list=$this->tm->getTicketMessageList($ticket_id);
		$list_arr=$list["data_arr"];
		$db_tot=$list["data_tot"];

		$tot=count($list_arr);

		if (($tot == 0) || ($tot > 3)) {
			$res.="<div class=\"ticketmsg_add_box_top\">";
			$res.=$add_link;
			$res.="</div>\n";
		}

		$user_id=$GLOBALS["current_user"]->getIdSt();

		foreach($list_arr as $message) {

			$id=$message["message_id"];

			$actions="";
			$url=$um->getUrl("op=editticketmsg&company=".$company_id."&ticket_id=".$ticket_id."&msg_id=".$id);
			$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$actions.="<a href=\"".$url."\">".$img."</a> ";
			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
			$img.="title=\"".$this->lang->def("_DEL")."\" />";
			$actions.=$img;


			$author_id=$message["author"];
			$author=$list["user"][$author_id];

			$is_owner=($author_id == $user_id ? TRUE : FALSE);

			if ($message["from_staff"])
				$box_type="staff_box";
			else
				$box_type="user_box";

			$res.="<div class=\"ticketmsg_box ".$box_type."\">";

			if (($is_owner) || ($this->getIsStaff()))
				$res.="<div class=\"actions\">".$actions."</div>";

			$res.="<div class=\"post_date\">".$GLOBALS["regset"]->databaseToRegional($message["post_date"])."</div>";
			$res.="<div class=\"author\"><b>".$author."</b></div>";
			$res.="<div class=\"no_float\"></div>";
			$res.="<p>".$message["text_msg"]."</p>";
			$res.="</div>\n";

		}


		if ($tot > 0) {
			$res.="<div class=\"ticketmsg_add_box_bottom\">";
			$res.=$add_link;
			$res.="</div>\n";
		}

		// ------------------------------------------------------------------------
		$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		return $res;
	}


	function getTicketInfoText($info) {
		$res="";
		$um=& $GLOBALS["url_manager"];

		$company_id=$info["company_id"];

		$prj_arr=$this->tm->getProjectArray($company_id, FALSE);
		$project=$prj_arr[$info["prj_id"]];

		$priority_arr=$this->tm->getPriorityArray($this->lang);
		$priority=$priority_arr[$info["priority"]];

		$status_list=$this->tm->getTicketStatusList();
		$status_arr=$status_list["list"];
		$status=$status_arr[$info["status"]];

		$actions="";
		$url=$um->getUrl("op=editticket&company=".$company_id."&ticket_id=".$info["ticket_id"]);
		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
		$img.="title=\"".$this->lang->def("_MOD")."\" />";
		$actions.="<a href=\"".$url."\">".$img."</a> ";

		$res.="<div class=\"ticket_info\">\n";
		$res.="<div class=\"actions\">".$actions."</div>";

		$res.="<p><b>".$this->lang->def("_TICKET_SUBJECT").":</b> ".$info["subject"]."</p>\n";
		$res.="<p><b>".$this->lang->def("_DATE").":</b> ";
		$res.=$GLOBALS["regset"]->databaseToRegional($info["post_date"])."</p>\n";
		$res.="<p><b>".$this->lang->def("_TICKET_PROJECT").":</b> ".$project."</p>\n";
		$res.="<p><b>".$this->lang->def("_TICKET_PRIORITY").":</b> ".$priority."</p>\n";
		$res.="<p><b>".$this->lang->def("_STATUS").":</b> ".$status."</p>\n";

		$res.="</div>\n";

		return $res;
	}


	function addeditMessage() {
		$res="";

		$um=& $GLOBALS["url_manager"];
		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id);

		if (isset($_GET["msg_id"]))
			$message_id=(int)$_GET["msg_id"];
		else
			$message_id=0;

		if (isset($_GET["ticket_id"]))
			$ticket_id=(int)$_GET["ticket_id"];
		else
			return "";

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$form=new Form();


		$ticket_info=$this->tm->getTicketInfo($ticket_id);
		$status=$ticket_info["status"];


		$res.="<div class=\"ticket_flat\">";
		$res.=$this->lang->def("_REPLY_TO").": <b>".$ticket_info["subject"]."</b>";
		$res.="</div>\n";


		$form_code="";
		$url=$um->getUrl("op=saveticketmsg&company=".$company_id);

		$form_code=$form->openForm("note_form", $url);


		if ($message_id > 0) { // Edit

			$submit_lbl=$this->lang->def("_SAVE");
			$info=$this->tm->getTicketMessageInfo($message_id);

			//TODO: check that user is owner or staff member

			$text_msg=$info["text_msg"];
		}
		else { // Add

			$submit_lbl=$this->lang->def("_INSERT");

			$text_msg="";
		}

		$res.=$form_code.$form->openElementSpace();

		if ($this->getIsStaff()) {
			$status_list=$this->tm->getTicketStatusList();
			$status_arr=$status_list["list"];
			$res.=$form->getDropdown($this->lang->def("_STATUS"), "status", "status", $status_arr, $status);
			$res.=$form->getHidden("old_status", "old_status", $status);
		}

		$res.=$form->getTextarea($this->lang->def("_TEXTOF").":", "text_msg", "text_msg", $text_msg);

		$res.=$form->getHidden("message_id", "message_id", $message_id);
		$res.=$form->getHidden("ticket_id", "ticket_id", $ticket_id);


		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $submit_lbl);
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function saveTicketMessage() {
		$um=& $GLOBALS["url_manager"];
		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id);

		if (isset($_POST["ticket_id"]))
			$ticket_id=(int)$_POST["ticket_id"];
		else
			return "";

		if (!isset($_POST["undo"])) {
			$from_staff=($this->getIsStaff() ? 1 : 0);
			$this->tm->saveTicketMessage($ticket_id, $_POST, $from_staff);
		}

		$url=$um->getUrl("op=showticket&company=".$company_id."&ticket_id=".$ticket_id, FALSE);
		jumpTo($url);
	}


	function switchTicketLock() {
		$res="";

		$um=& $GLOBALS["url_manager"];
		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id, TRUE);

		if (isset($_GET["ticket_id"]))
			$ticket_id=(int)$_GET["ticket_id"];
		else
			return "";

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$form=new Form();

		$form_code="";
		$url=$um->getUrl("op=saveticketlock&company=".$company_id);

		$form_code=$form->openForm("note_form", $url);


		$ticket_info=$this->tm->getTicketInfo($ticket_id);
		$closed=$ticket_info["closed"];
		$prj_id=$ticket_info["prj_id"];
		$prj_info=$this->tm->getProjectInfo($prj_id);
		$ticket=$prj_info["ticket"];

		if ($closed) {
			$todo="reopen";
			$ticket_diff=0;
			$label=$this->lang->def("_TICKET_TO_ADD");
			$submit_lbl=$this->lang->def("_UNLOCK_TICKET");
		}
		else {
			$todo="close";
			$ticket_diff=1;
			$label=$this->lang->def("_TICKET_TO_SUB");
			$submit_lbl=$this->lang->def("_LOCK_TICKET");
		}

		$res.=$form_code.$form->openElementSpace();


		$label.=" (".(int)$ticket." ".$this->lang->def("_AVAILABLE").")";
		$res.=$form->getTextfield($label.":", "ticket_diff", "ticket_diff", 255, $ticket_diff);


		$res.=$form->getHidden("ticket_id", "ticket_id", $ticket_id);
		$res.=$form->getHidden("todo", "todo", $todo);

		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $submit_lbl);
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function assignTicketUsers() {
		$res="";
		require_once($GLOBALS['where_framework']."/class.module/class.directory.php");
		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");

		$um=& $GLOBALS["url_manager"];
		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id, TRUE);

		if (isset($_GET["ticket_id"]))
			$ticket_id=(int)$_GET["ticket_id"];
		else
			return "";

		$mdir=new Module_Directory();

		$out=& $GLOBALS["page"];
		$out->setWorkingZone("content");
		$um=& $GLOBALS["url_manager"];
		$ccm=new CoreCompanyManager();


		$back_url=$um->getUrl("op=ticket");


		if( isset($_POST['okselector']) ) {

			$arr_selection=$mdir->getSelection($_POST);
			$arr_deselected=$mdir->getUnselected();

			$this->tm->saveTicketPerm($ticket_id, $arr_selection, $arr_deselected);

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
				$perm=$this->tm->loadTicketPerm($ticket_id);
				$mdir->resetSelection(array_keys($perm["assigned"]));
			}


			$regusers_idst=$mdir->aclManager->getGroupRegisteredId();
			$mdir->setUserFilter("group", array($regusers_idst));


			$back_ui_url=$um->getUrl("op=ticket");

			$url=$um->getUrl("op=assignticketusers&ticket_id=".$ticket_id."&stayon=1");
			$mdir->loadSelector($url, $this->lang->def('_TICKET_ASSIGNED_USERS'), "", TRUE);

			$out->add(getBackUi($back_ui_url, $this->lang->def( '_BACK' )));
		}
	}


	function saveTicketLock() {
		$um=& $GLOBALS["url_manager"];
		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id, TRUE);

		if (!isset($_POST["undo"])) {
			$this->tm->saveTicketLock($_POST);
		}

		$url=$um->getUrl("op=ticket&company=".$company_id, FALSE);
		jumpTo($url);
	}


	function setTicketOrder() {

		$um=& $GLOBALS["url_manager"];
		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id);

		if ((isset($_GET["ord"])) && (!empty($_GET["ord"])))
			$this->tm->setTicketOrder($_GET["ord"]);

		$url=$um->getUrl("op=ticket&company=".$company_id, FALSE);
		jumpTo($url);
	}


}


?>
