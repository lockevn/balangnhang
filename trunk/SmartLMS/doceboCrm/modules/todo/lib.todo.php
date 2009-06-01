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


class TodoManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	// TodoDataManager
	var $tdm=NULL;

	var $lang=NULL;
	var $is_staff=FALSE;
	var $show_backui=TRUE;
	var $show_title_area=TRUE;


	function TodoManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_crm"]);
		$this->dbconn=$dbconn;

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
		$this->tdm=new TodoDataManager();

		$this->lang=& DoceboLanguage::createInstance('todo', "crm");
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
		$title_arr[$um->getUrl()]=$this->lang->def("_TODOS");
		$title_arr[]=$this->lang->def("_TODO_SELECT_COMPANY");
		$res.=$this->titleArea($title_arr, "todo");


		$table_caption=$this->lang->def("_TODO_SELECT_COMPANY");
		$table_summary=$this->lang->def("_TODO_SELECT_COMPANY_SUMMARY");

		$tab=new typeOne(0, $table_caption, $table_summary);
		$tab->setTableStyle("todo_table");

		$head=array($this->lang->def("_COMPANY_NAME"));
		$head_type=array("");

		$tab->addHead($head);
		$tab->setColsStyle($head_type);


		$company_arr=$this->getTodoCompany();
		$available_company=$this->ccManager->getCompanyList(FALSE, FALSE, $company_arr);
		$company_list=$available_company["data_arr"];

		foreach ($company_list as $company) {

			$rowcnt=array();

			$id=$company["company_id"];

			$url=$um->getUrl("tab_op=todo&company=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$company["name"]."</a>\n";

			$tab->addBody($rowcnt);
		}


		$res.=$tab->getTable();

		// ------------------------------------------------------------------------
		//$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		return $res;
	}


	function setTodoCompany($company_id) {

		$_SESSION["todo_company"]=(int)$company_id;

	}


	function getTodoCompany() {
		if (isset($_SESSION["todo_company"]))
			return (int)$_SESSION["todo_company"];
		else
			return 0;
	}


	function getCurrentCompanyId() {

		$company_id=$this->getTodoCompany();

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
		$company_arr=$this->getTodoCompany();
		if (!in_array($company_id, $company_arr))
			die("You can't access!");

		if (($staff_required) && (!$this->getIsStaff()))
			die("You can't access!"); */
			
		return TRUE;
	}


	function showCompanyTodo($company_id) {
		$res="";
		$this->checkCompanyPerm($company_id);

		$um=& $GLOBALS["url_manager"];

		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
		$table_caption=$this->lang->def("_TODO_TABLE_CAPTION");
		$table_summary=$this->lang->def("_TODO_TABLE_SUMMARY");

		$vis_item=$GLOBALS["visuItem"];

		$tab=new typeOne($vis_item, $table_caption, $table_summary);


		$is_staff=$this->getIsStaff();

		$head=array();
		$img ="<img src=\"".getPathImage()."todo/todo.gif\" alt=\"".$this->lang->def("_ALT_TODO")."\" ";
		$img.="title=\"".$this->lang->def("_ALT_TODO")."\" />";
		$head[]=$img;
		$ord_url=$um->getUrl("tab_op=todosetorder&company=".$company_id."&ord=title");
		$head[]="<a href=\"".$ord_url."\">".$this->lang->def("_TODO_ACTION")."</a>";
		$ord_url=$um->getUrl("tab_op=todosetorder&company=".$company_id."&ord=status");
		$head[]="<a href=\"".$ord_url."\">".$this->lang->def("_STATUS")."</a>";
		$ord_url=$um->getUrl("tab_op=todosetorder&company=".$company_id."&ord=date");
		$head[]="<a href=\"".$ord_url."\">".$this->lang->def("_TODO_END_DATE")."</a>";

		$head_type=array("image", "", "", "");


		// Complete / incomplete
		$img ="<img src=\"".getPathImage()."todo/complete.gif\" alt=\"".$this->lang->def("_TODO_COMPLETE_STATUS")."\" ";
		$img.="title=\"".$this->lang->def("_TODO_COMPLETE_STATUS")."\" />";
		$ord_url=$um->getUrl("tab_op=todosetorder&company=".$company_id."&ord=closed");
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
		$roles=$acl_manager->getUserRoleFromPath($user_idst, "/crm/todo", "assigned");

		$level=$GLOBALS["current_user"]->getUserLevelId();
		$is_admin=($level == ADMIN_GROUP_ADMIN ? TRUE : FALSE);
		$is_god_admin=($level == ADMIN_GROUP_GODADMIN ? TRUE : FALSE);

		// If user is not a admin / god admin then apply permission restrictions
		if ((!$is_god_admin) && (!$is_admin)) {
			$where.=" AND ";

			$where.="((t1.author = '".$GLOBALS["current_user"]->getIdSt()."')";
			if (($roles !== FALSE) && (is_array($roles["role_info"]) && (count($roles["role_info"]) > 0))) {
				$where.=" OR (t1.todo_id IN (".implode(",", $roles["role_info"]).")))";
			}
			else
				$where.=")";

			$where.=" ";
		}

		$list=$this->tdm->getTodoList($ini, $vis_item, $where);
		$list_arr=$list["data_arr"];
		$db_tot=$list["data_tot"];

		$status_arr=$this->tdm->getStatusArray();

		$tot=count($list_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$list_arr[$i]["todo_id"];

			$rowcnt=array();

			$img ="<img src=\"".getPathImage()."todo/todo.gif\" alt=\"".$this->lang->def("_ALT_TODO")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_TODO")."\" />";
			$rowcnt[]=$img;


			$show_details=(isset($_SESSION["show_todo_details"][$id]) ? TRUE : FALSE);

			$url_qry ="op=details&id=".$company_id;
			$url_qry.="&tab_op=toggletododetails&todo_id=".$id;
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
			
			$rowcnt[]=$status_arr[$list_arr[$i]["status"]];
			$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["end_date"]);


			if ($is_staff) {
				$locked_msg=$this->lang->def("_MARK_INCOMPLETE");
				$unlocked_msg=$this->lang->def("_MARK_COMPLETE");
			}
			else {
				$locked_msg=$this->lang->def("_COMPLETE");
				$unlocked_msg=$this->lang->def("_INCOMPLETE");
			}


			if ($list_arr[$i]["complete"]) {
				$lock_img ="<img src=\"".getPathImage()."todo/complete.gif\" alt=\"".$locked_msg."\" ";
				$lock_img.="title=\"".$locked_msg."\" />";
			}
			else {
				$lock_img ="<img src=\"".getPathImage()."todo/incomplete.gif\" alt=\"".$unlocked_msg."\" ";
				$lock_img.="title=\"".$unlocked_msg."\" />";
			}

			$url=$um->getUrl("tab_op=switchtodocomplete&company=".$company_id."&todo_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$lock_img."</a>";

			$url=$um->getUrl("tab_op=edittodo&company=".$company_id."&todo_id=".$id);
			$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>";

			$tab->addBody($rowcnt);
			
			if ($show_details) {
				$tab->addBodyExpanded($list_arr[$i]["description"], "line_details"); 
			}			
		}


		$url=$um->getUrl("tab_op=addtodo&company=".$company_id);
		$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".$this->lang->def('_ADD')."</a>\n");


		$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}
	


	function addeditTodo($todo_id=0) {
		$res="";

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$form=new Form();
		$um=& $GLOBALS["url_manager"];


		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id);

		$back_ui_url=$um->getUrl("tab_op=todo&company=".$company_id);
		$res.=$this->getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		$title_arr=array();
		$title_arr[$um->getUrl()]=$this->lang->def("_TODOS");
		$title_arr[$back_ui_url]="nome azienda";
		
		if ($todo_id > 0) {
			$title_arr[]=$this->lang->def("_EDIT_TODO");
			$submit_lbl=$this->lang->def('_SAVE');
			
			$info=$this->tdm->getTodoInfo($todo_id);
			
			$title=$info["title"];
			$priority=$info["priority"];
			$status=$info["status"];
			$description=$info["description"];
			$end_date=$GLOBALS["regset"]->databaseToRegional($info["end_date"]);
			
		}
		else {
			$title_arr[]=$this->lang->def("_ADD_TODO");
			$submit_lbl=$this->lang->def('_CREATE');
			
			$title="";
			$priority=FALSE;
			$status=FALSE;
			$description="";
			$end_date="";
		}
		
		$res.=$this->titleArea($title_arr, "todo");


		$url=$um->getUrl("tab_op=savetodo&company=".$company_id);
		$res.=$form->openForm("main_form", $url);
		$res.=$form->openElementSpace();

		$res.=$form->getTextfield($this->lang->def("_TODO_ACTION").":", "title", "title", 255, $title);

		$priority_arr=$this->tdm->getPriorityArray();
		$res.=$form->getDropdown($this->lang->def("_TODO_PRIORITY"), "priority", "priority", $priority_arr, $priority);

		$status_arr=$this->tdm->getStatusArray();
		$res.=$form->getDropdown($this->lang->def("_STATUS"), "status", "status", $status_arr, $status);
		
		$res.=$form->getDatefield($this->lang->def("_TODO_END_DATE"), "end_date", "end_date", $end_date);

		$res.=$form->getTextarea($this->lang->def("_DESCRIPTION").":", "description", "description", $description);
		
		$res.=$form->getHidden("todo_id", "todo_id", $todo_id);

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


	function saveTodo() {
		
		$company_id=$this->getCurrentCompanyId();

		$um=& UrlManager::getInstance();	
		$back_url=$um->getUrl("tab_op=todo&company=".$company_id, FALSE);
		
		if (isset($_POST["undo"])) {
			jumpTo($back_url);
			die();
		}		

		$this->tdm->saveTodo($company_id);

		jumpTo($back_url);
	}	
	

	function switchTodoComplete() {
		$company_id=$this->getCurrentCompanyId();

		$um=& UrlManager::getInstance();	
		$back_url=$um->getUrl("tab_op=todo&company=".$company_id, FALSE);
		
		if ((isset($_GET["todo_id"])) && ($_GET["todo_id"] > 0)) {
			$this->tdm->switchTodoComplete($_GET["todo_id"]);
		}		

		jumpTo($back_url);
	}
	
	
	function toggleTodoDetails() {
	
		$company_id=$this->getCurrentCompanyId();
		
		if ((isset($_GET["todo_id"])) && ($_GET["todo_id"] > 0)) {
			
			$todo_id=$_GET["todo_id"];
		
			if (isset($_SESSION["show_todo_details"][$todo_id]))
				unset($_SESSION["show_todo_details"][$todo_id]);
			else
				$_SESSION["show_todo_details"][$todo_id]=1;
				
		}
		
		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl("tab_op=todo&company=".$company_id, FALSE);
		jumpTo($back_url);
	}		


	function setTodoOrder() {

		$um=& $GLOBALS["url_manager"];
		$company_id=$this->getCurrentCompanyId();
		$this->checkCompanyPerm($company_id);

		if ((isset($_GET["ord"])) && (!empty($_GET["ord"])))
			$this->tdm->setTodoOrder($_GET["ord"]);

		$url=$um->getUrl("tab_op=todo&company=".$company_id, FALSE);
		jumpTo($url);
	}


}










class TodoDataManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	// CoreCompanyManager
	var $ccManager=NULL;

	var $lang=NULL;
	var $is_staff=FALSE;
	var $show_backui=TRUE;
	var $show_title_area=TRUE;
	
	var $todo_info=array();


	function TodoDataManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_crm"]);
		$this->dbconn=$dbconn;

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
		$this->ccManager=new CoreCompanyManager();

		$this->lang=& DoceboLanguage::createInstance('todo', "crm");
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


	function _getTodoTable() {
		return $this->prefix."_todo";
	}
	
	
	function initTodoConstants() {

		if (!defined("TODO_CONSTANTS_LOADED")) {
			define("TODO_CONSTANTS_LOADED", TRUE);
		}
		else
			return 0;

		// --- Priority
		define("PRIORITY_VERYLOW" , 1);
		define("PRIORITY_LOW" , 2);
		define("PRIORITY_MEDIUM" , 3);
		define("PRIORITY_HIGH" , 4);
		define("PRIORITY_VERYHIGH" , 5);
		
		// --- Status
		define("STATUS_INCOMPLETE" , 1);
		define("STATUS_COMPLETE" , 2);		
	}


	function getPriorityArray($include_any=FALSE) {

		$this->initTodoConstants();

		$res=array();
		
		if ($include_any)
			$res[0]=def("_ANY", "search", "framework");		
		
		$res[PRIORITY_VERYLOW]=$this->lang->def("_PRIORITY_VERYLOW");
		$res[PRIORITY_LOW]=$this->lang->def("_PRIORITY_LOW");
		$res[PRIORITY_MEDIUM]=$this->lang->def("_PRIORITY_MEDIUM");
		$res[PRIORITY_HIGH]=$this->lang->def("_PRIORITY_HIGH");
		$res[PRIORITY_VERYHIGH]=$this->lang->def("_PRIORITY_VERYHIGH");

		return $res;
	}	
	
	
	function getStatusArray($include_any=FALSE) {

		$this->initTodoConstants();

		$res=array();
		
		if ($include_any)
			$res[0]=def("_ANY", "search", "framework");				
		
		$res[STATUS_INCOMPLETE]=$this->lang->def("_INCOMPLETE");
		$res[STATUS_COMPLETE]=$this->lang->def("_COMPLETE");

		return $res;		
	}
	

	function getTodoOrder() {

		$field=(isset($_SESSION["todo_order"]["field"]) ? $_SESSION["todo_order"]["field"] : "t1.start_date");
		$type=(isset($_SESSION["todo_order"]["type"]) ? $_SESSION["todo_order"]["type"] : "DESC");

		$res=array();
		$res["field"]=$field;
		$res["type"]=$type;

		return $res;
	}	


	function getTodoList($ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
		$cm=new CompanyManager();

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="t1.*, t2.name as company_name";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getTodoTable()." as t1, ";
		$qtxt.=$cm->getMainTable()." as t2 ";

		$qtxt.="WHERE t1.company_id=t2.company_id ";
		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}


		$ord=$this->getTodoOrder();
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

				$id=$row["todo_id"];
				$data_info["data_arr"][$i]=$row;
				$this->todo_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}	
	

	function loadTodoInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getTodoTable()." ";
		$qtxt.="WHERE todo_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getTodoInfo($id) {

		if (!isset($this->todo_info[$id]))
			$this->todo_info[$id]=$this->loadTodoInfo($id);

		return $this->todo_info[$id];
	}	


	function saveTodo($company_id, $data=FALSE) {
			
		if ($data === FALSE)
			$data=$_POST;
	
		$todo_id=(int)$data["todo_id"];
		$title=$data["title"];
		$description=$data["description"];
		$priority=(int)$data["priority"];
		$status=(int)$data["status"];
		$end_date=$GLOBALS["regset"]->regionalToDatabase($data["end_date"]);


		if ((int)$todo_id > 0) {

			$qtxt ="UPDATE ".$this->_getTodoTable()." SET title='".$title."', description='".$description."', ";
			$qtxt.="priority='".$priority."', status='".$status."', end_date='".$end_date."' ";
			$qtxt.="WHERE todo_id='".$todo_id."' LIMIT 1";
			$q=$this->_executeQuery($qtxt);

		}
		else {
			
			$field_list ="company_id, title, description, ";
			$field_list.="status, priority, start_date, end_date";
			$field_val ="'".(int)$company_id."', '".$title."', '".$description."', ";
			$field_val.="'".$status."', '".$priority."', NOW(), '".$end_date."'";

			$qtxt="INSERT INTO ".$this->_getTodoTable()." (".$field_list.") VALUES(".$field_val.")";
			$todo_id=$this->_executeInsert($qtxt);
		}

		return $todo_id;
	}		
	
	
	function switchTodoComplete($todo_id) {
		
		$info=$this->getTodoInfo($todo_id);
		
		$new_val=($info["complete"] > 0 ? 0 : 1);
		
		$qtxt ="UPDATE ".$this->_getTodoTable()." SET complete='".$new_val."' ";
		$qtxt.="WHERE todo_id='".$todo_id."' LIMIT 1";
		$q=$this->_executeQuery($qtxt);		
		
		return $q;
	}
	
	
	function setTodoOrder($ord) {

		switch ($ord) {
			case "title": {
				$field="t1.title";
				$default_type="ASC";
			} break;
			case "status": {
				$field="t1.status";
				$default_type="ASC";
			} break;
			case "priority": {
				$field="t1.priority";
				$default_type="ASC";
			} break;			
			case "date": {
				$field="t1.end_date";
				$default_type="DESC";
			} break;
			case "closed": {
				$field="t1.complete";
				$default_type="ASC";
			} break;
			case "company": {
				$field="t2.name";
				$default_type="ASC";
			} break;
		}

		if ((isset($_SESSION["todo_order"]["field"])) &&
		    ($field == $_SESSION["todo_order"]["field"])) {

			if ($_SESSION["todo_order"]["type"] == "ASC")
				$_SESSION["todo_order"]["type"]="DESC";
			else
				$_SESSION["todo_order"]["type"]="ASC";
		}
		else {
			$_SESSION["todo_order"]["field"]=$field;
			$_SESSION["todo_order"]["type"]=$default_type;
		}


	}
	
	
	/**
	 * if company_id is set will delete all todo of the
	 * specified company
	 */	
	function deleteToDo($todo_id, $company_id=FALSE) {
		
		$qtxt ="DELETE FROM ".$this->_getTodoTable()." WHERE ";	
	
	
		if (($todo_id > 0) && ($company_id === FALSE)) {
			$qtxt.="todo_id='".$todo_id."' LIMIT 1";
		}
		else if (($company_id > 0) && ($todo_id === FALSE)) {
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
