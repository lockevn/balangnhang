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

define("_NO_COMPANY_ID", 311);

require_once($GLOBALS["where_framework"]."/lib/lib.company.php");


Class CompanyManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	// CoreCompanyManager
	var $ccManager=NULL;

	var $project_info=array();
	var $task_info=array();
	var $note_info=array();


	function CompanyManager($prefix="crm", $dbconn=NULL) {
		$this->prefix=$prefix;
		$this->dbconn=$dbconn;

		$this->ccManager=new CoreCompanyManager();
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


	function getMainTable() {
		return $this->ccManager->getCompanyTable();
	}


	function _getCompanyUsersTable() {
		return $this->ccManager->getCompanyUsersTable();
	}


	function getProjectTable() {
		return $this->prefix."_project";
	}


	function _getTaskTable() {
		return $this->prefix."_task";
	}


	function _getFileTable() {
		return $this->prefix."_file";
	}


	function _getNoteTable() {
		return $this->prefix."_note";
	}


	function _getCompanyAssignedTable() {
		return $this->prefix."_company_assigned_log";
	}


	function getCompanyList($ini=FALSE, $vis_item=FALSE, $in_arr=FALSE, $where=FALSE, $extra_info=FALSE) {
		return $this->ccManager->getCompanyList($ini, $vis_item, $in_arr, $where, $extra_info);
	}


	function getCompanyInfo($id) {
		return $this->ccManager->getCompanyInfo($id);
	}


	function saveData($data, $save_custom_fields=TRUE) {

		$id=(int)$data["id"];
		$res =$id;

		$name=$data["name"];
		$code=$data["code"];
		$ctype_id=(int)$data["ctype_id"];
		$cstatus_id=(int)$data["cstatus_id"];
		$address=$data["address"];
		$tel=$data["tel"];
		$email=$data["email"];
		$notes=$data["notes"];
		if ((isset($data["recall_on"])) && (!empty($data["recall_on"]))) {
			$recall_on=$GLOBALS["regset"]->regionalToDatabase($data["recall_on"]);
		}
		$assigned_to=(isset($data["assigned_to"]) ? (int)$data["assigned_to"] : FALSE);
		$from_year=(isset($data["from_year"]) ? (int)$data["from_year"] : FALSE);
		$vat_number=$data["vat_number"];
		if ((isset($data["imported_from_connection"])) && (!empty($data["imported_from_connection"]))) {
			$imported_from_connection=$data["imported_from_connection"];
		}
		else {
			$imported_from_connection=FALSE;
		}

		if ($id == 0) {

			if (empty($name)) {
				$lang=& DoceboLanguage::createInstance("company", "framework");
				$name=$lang->def("_NO_NAME");
			}

			$field_list ="name, code, ctype_id, cstatus_id, address, tel, email, vat_number";
			$field_list.=(!empty($notes) ? ", notes" : "");
			$field_list.=(!empty($recall_on) ? ", recall_on" : "");
			$field_list.=($assigned_to !== FALSE ? ", assigned_to" : "");
			$field_list.=($from_year !== FALSE ? ", from_year" : "");
			$field_list.=($imported_from_connection !== FALSE ? ", imported_from_connection" : "");
			$field_val ="'".$name."', ".(empty($code) ? "NULL" : "'".$code."'").", ";
			$field_val.="'".$ctype_id."', '".$cstatus_id."', ";
			$field_val.="'".$address."', '".$tel."', '".$email."', '".$vat_number."'";
			$field_val.=(!empty($notes) ? ", '".$notes."'" : "");
			$field_val.=(!empty($recall_on) ? ", '".$recall_on."'" : "");
			$field_val.=($assigned_to !== FALSE ? ", '".$assigned_to."'" : "");
			$field_val.=($from_year !== FALSE ? ", '".$from_year."'" : "");
			$field_val.=($imported_from_connection !== FALSE ? ", '".$imported_from_connection."'" : "");

			$qtxt="INSERT INTO ".$this->getMainTable()." (".$field_list.") VALUES(".$field_val.")";
			if (!$id=$this->_executeInsert($qtxt)) return false;

			if ($id > 0) {
				$acl_manager =& $GLOBALS["current_user"]->getAclManager();
				$group ='/framework/company/'.$id.'/users';
				$group_idst =$acl_manager->registerGroup($group, 'all the user of a company', true, "company");
				$res =$id;
			}
		}
		else if ($id > 0) {

			$qtxt ="UPDATE ".$this->getMainTable()." SET name='".$name."', ";
			$qtxt.="code=".(empty($code) ? "NULL" : "'".$code."'").", ";
			$qtxt.="ctype_id='".$ctype_id."', cstatus_id='".$cstatus_id."', ";
			$qtxt.="address='".$address."', tel='".$tel."', email='".$email."', ";
			$qtxt.="vat_number='".$vat_number."'";
			$qtxt.=(!empty($notes) ? ", notes='".$notes."' " : " ");
			$qtxt.=(!empty($recall_on) ? ", recall_on='".$recall_on."' " : " ");
			$qtxt.=($assigned_to !== FALSE ? ", assigned_to='".$assigned_to."' " : " ");
			$qtxt.=($from_year !== FALSE ? ", from_year='".$from_year."' " : " ");
			$qtxt.=($imported_from_connection !== FALSE ? ", imported_from_connection='".$imported_from_connection."' " : " ");
			$qtxt.="WHERE company_id='".$id."'";
			if(!$q=$this->_executeQuery($qtxt)) {echo $qtxt; die(); }//return false;
			$res =$id;
		}
		if ($save_custom_fields) {
			require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

			$fl = new FieldList();
			$values=$fl->getFilledSpecVal($data["field_to_store"]);
			$arr_fields=array();
			foreach ($values as $field_id=>$field_info) {
				$arr_fields[$field_id]=$field_info["value"];
			}
			$fl->setFieldEntryTable($this->ccManager->getCompanyFieldEntryTable());
			$fl->storeDirectFieldsForUser($id, $arr_fields, TRUE);
		}
		return $res;
	}


	function saveCompanyDemoUser($company_id, $user_idst) {
		$qtxt ="UPDATE ".$this->getMainTable()." SET demo_user='".$user_idst."' ";
		$qtxt.="WHERE company_id='".(int)$company_id."'";
		$q =$this->_executeQuery($qtxt);
		if (!$q) { var_dump($q); echo $qtxt; echo mysql_error(); die(); }
		return $q;
	}


	/**
	 * @return array list of companies that user is a member of.
	 **/
	function getUserCompanies($user_id) {
		return $this->ccManager->getUserCompanies($user_id);
	}


	function getCompanyUsers($company_id) {
		return $this->ccManager->getCompanyUsers($company_id);
	}


	function updateCompanyUsers($company_id, $assigned_idst, $deselected) {

		$acl_manager =& $GLOBALS["current_user"]->getAclManager();

		$group ='/framework/company/'.$company_id.'/users';
		$group_idst =$acl_manager->getGroupST($group);

		if ($group_idst === FALSE) {
			$group_idst =$acl_manager->registerGroup($group, 'all the user of a company', true, "company");
		}

		if ((is_array($deselected)) && (count($deselected) > 0))
			$deselected_list=implode(",", $deselected);
		else
			$deselected_list="0";

		foreach($deselected as $user_id) {
			$acl_manager->removeFromGroup($group_idst, $user_id);
		}

		$qtxt ="DELETE FROM ".$this->_getCompanyUsersTable()." WHERE company_id='".(int)$company_id."' ";
		$qtxt.="AND user_id IN (".$deselected_list.")";
		$q=$this->_executeQuery($qtxt);

		foreach ($assigned_idst as $idst) {

			$qtxt ="INSERT INTO ".$this->_getCompanyUsersTable()." (company_id, user_id) ";
			$qtxt.="VALUES ('".(int)$company_id."', '".$idst."')";
			$q=$this->_executeQuery($qtxt);

			if ($q) {
				$acl_manager->addToGroup($group_idst, $idst);
			}
		}

	}


	function addToCompanyUsers($company_id, $user_id) {
		$q=$this->ccManager->addToCompanyUsers($company_id, $user_id);
		return $q;
	}


	function getCompanyTypeList($include_any=FALSE) {
		// TODO: maybe this function should be renamed to avoid confusion with
		// the homonymous in doceboCore/lib/lib.company.php
		$res=array();
		$ctm=new CompanyTypeManager();

		$list=$ctm->getCompanyTypeList();
		$res["info"]=$list["data_arr"];

		if ($include_any)
			$res["list"][0]=def("_ANY", "company", "crm");

		foreach ($list["data_arr"] as $info) {

			$id=$info["ctype_id"];
			$res["list"][$id]=$info["label"];

		}

		return $res;
	}


	function getCompanyStatusList($include_any=FALSE) {

		$res=array();
		$csm=new CompanyStatusManager();

		$list=$csm->getCompanyStatusList();
		$res["info"]=$list["data_arr"];

		if ($include_any)
			$res["list"][0]=def("_ANY", "company", "crm");

		foreach ($list["data_arr"] as $info) {

			$id=$info["cstatus_id"];
			$res["list"][$id]=$info["label"];

		}

		return $res;
	}


	function initProjectConstants() {

		if (!defined("PRJ_CONSTANTS_LOADED")) {
			define("PRJ_CONSTANTS_LOADED", TRUE);
		}
		else
			return 0;

		// --- Status
		define("STATUS_OFFERSENT" , 1);
		define("STATUS_WAITINGSIG" , 2);
		define("STATUS_SIGNED" , 3);
		define("STATUS_TOBESTARTED" , 4);
		define("STATUS_STARTED" , 5);
		define("STATUS_DONE" , 6);
		define("STATUS_ACTIVEUNTILRENEW" , 7);
		define("STATUS_SUSPENDED" , 8);
		define("STATUS_CANCELLED" , 9);

		// --- Priority
		define("PRIORITY_VERYLOW" , 1);
		define("PRIORITY_LOW" , 2);
		define("PRIORITY_MEDIUM" , 3);
		define("PRIORITY_HIGH" , 4);
		define("PRIORITY_VERYHIGH" , 5);

	}


	function getStatusArray(& $lang, $include_any=FALSE) {

		$this->initProjectConstants();

		$res=array();

		if ($include_any)
			$res[0]=$lang->def("_ANY");

		$res[STATUS_OFFERSENT]=$lang->def("_STATUS_OFFERSENT"); //
		$res[STATUS_WAITINGSIG]=$lang->def("_STATUS_WAITINGSIG"); //
		$res[STATUS_SIGNED]=$lang->def("_STATUS_SIGNED"); //
		$res[STATUS_TOBESTARTED]=$lang->def("_STATUS_TOBESTARTED");
		$res[STATUS_STARTED]=$lang->def("_STARTED");
		$res[STATUS_DONE]=$lang->def("_STATUS_DONE");
		$res[STATUS_ACTIVEUNTILRENEW]=$lang->def("_STATUS_ACTIVEUNTILRENEW"); // no renew
		$res[STATUS_SUSPENDED]=$lang->def("_SUSPENDED");
		$res[STATUS_CANCELLED]=$lang->def("_STATUS_CANCELLED");

		return $res;
	}


	function getPriorityArray(& $lang, $include_any=FALSE) {

		$this->initProjectConstants();

		$res=array();

		if ($include_any)
			$res[0]=$lang->def("_ANY");

		$res[PRIORITY_VERYLOW]=$lang->def("_PRIORITY_VERYLOW");
		$res[PRIORITY_LOW]=$lang->def("_PRIORITY_LOW");
		$res[PRIORITY_MEDIUM]=$lang->def("_PRIORITY_MEDIUM");
		$res[PRIORITY_HIGH]=$lang->def("_PRIORITY_HIGH");
		$res[PRIORITY_VERYHIGH]=$lang->def("_PRIORITY_VERYHIGH");


		return $res;
	}


	function getProjectList($company_id, $ini=FALSE, $vis_item=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		if ((int)$company_id == 0)
			return 0;

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->getProjectTable()." ";
		$qtxt.="WHERE company_id='".(int)$company_id."' ";
		$qtxt.="ORDER BY priority DESC, name ASC ";
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

				$id=$row["prj_id"];
				$data_info["data_arr"][$i]=$row;
				$this->task_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadProjectInfo($prj_id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->getProjectTable()." ";
		$qtxt.="WHERE prj_id='".(int)$prj_id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getProjectInfo($prj_id) {

		if (!isset($this->project_info[$prj_id]))
			$this->project_info[$prj_id]=$this->loadProjectInfo($prj_id);

		return $this->project_info[$prj_id];
	}


	function saveProject($data) {

		$prj_id=(int)$data["prj_id"];
		$company_id=(int)$data["company_id"];
		$name=$data["name"];
		$cost=$data["cost"];
		$gain=$data["gain"];
		$priority=$data["priority"];
		$status=$data["status"];
		$progress=$data["progress"];
		$sign_date=$GLOBALS["regset"]->regionalToDatabase($data["sign_date"]);
		$expire=$GLOBALS["regset"]->regionalToDatabase($data["expire"]);
		$deadline=$GLOBALS["regset"]->regionalToDatabase($data["deadline"]);
		$ticket=$data["ticket"];

		if ($prj_id == 0) {

			if (empty($name)) {
				$lang=& DoceboLanguage::createInstance("company", "framework");
				$name=$lang->def("_NO_NAME");
			}

			$field_list ="company_id, name, cost, gain, priority, status, ";
			$field_list.="progress, sign_date, expire, deadline, ticket";
			$field_val ="'".$company_id."', '".$name."', '".$cost."', '".$gain."', '".$priority."', '".$status."', ";
			$field_val.="'".$progress."', '".$sign_date."', '".$expire."', '".$deadline."', '".$ticket."'";

			$qtxt="INSERT INTO ".$this->getProjectTable()." (".$field_list.") VALUES(".$field_val.")";
			$prj_id=$this->_executeInsert($qtxt);
		}
		else if ($prj_id > 0) {

			$qtxt ="UPDATE ".$this->getProjectTable()." SET name='".$name."', ";
			$qtxt.="cost='".$cost."', gain='".$gain."', priority='".$priority."', status='".$status."', ";
			$qtxt.="progress='".$progress."', sign_date='".$sign_date."', expire='".$expire."', ";
			$qtxt.="deadline='".$deadline."', ticket='".$ticket."' ";
			$qtxt.="WHERE prj_id='".$prj_id."' AND company_id='".$company_id."'";
			$q=$this->_executeQuery($qtxt);

		}

		return $prj_id;
	}


	/**
	 * if company_id is set will delete all projects of the
	 * specified company
	 */
	function deleteCompanyProject($prj_id, $company_id=FALSE) {
		require_once($GLOBALS['where_crm'].'/modules/ticket/lib.ticketmanager.php');

		$prj_to_del_arr=array();

		if (($prj_id > 0) && ($company_id === FALSE)) {
			$prj_to_del_arr[]=$prj_id;
		}
		else if (($company_id > 0) && ($prj_id === FALSE)) {

			$qtxt ="SELECT prj_id FROM ".$this->getProjectTable()." ";
			$qtxt.="WHERE company_id='".$company_id."'";
			$q=$this->_executeQuery($qtxt);

			if (($q) && (mysql_num_rows($q) > 0)) {
				while($row=mysql_fetch_assoc($q)) {
					$prj_to_del_arr[]=$row["prj_id"];
				}
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}


		foreach($prj_to_del_arr as $prj_id) {

			// delete project notes
			$this->deleteNote(FALSE, "project", $prj_id);

			// delete company tasks
			$this->deleteProjectTask(FALSE, $prj_id);

			// delete company tickets
			$tm=new TicketManager();
			$tm->deleteTicket(FALSE, $prj_id);
		}


		// delete the project(s)
		if ((is_array($prj_to_del_arr)) && (count($prj_to_del_arr) > 0)) {
			$qtxt ="DELETE FROM ".$this->getProjectTable()." ";
			$qtxt.="WHERE prj_id IN (".implode(",", $prj_to_del_arr).")";

			$q=$this->_executeQuery($qtxt);
		}

	}


	function getTaskList($company_id, $prj_id, $ini=FALSE, $vis_item=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$where="t1.company_id='".$company_id."' AND t1.prj_id='".$prj_id."'";
		$data_info=$this->getAllTasksList($ini, $vis_item, $where);

		return $data_info;
	}


	function getAllTasksList($ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="t1.*, t2.name as company_name, t3.name as prj_name";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getTaskTable()." as t1, ";
		$qtxt.=$this->getMainTable()." as t2, ";
		$qtxt.=$this->getProjectTable()." as t3 ";

		$qtxt.="WHERE t1.company_id=t2.company_id AND t1.prj_id=t3.prj_id ";
		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		//$qtxt.="ORDER BY priority DESC, name ASC ";
		$ord=$this->getTaskOrder();
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

				$id=$row["prj_id"];
				$data_info["data_arr"][$i]=$row;
				$this->task_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadTaskInfo($task_id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getTaskTable()." ";
		$qtxt.="WHERE task_id='".(int)$task_id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getTaskInfo($task_id) {

		if (!isset($this->task_info[$task_id]))
			$this->task_info[$task_id]=$this->loadTaskInfo($task_id);

		return $this->task_info[$task_id];
	}



	function getTaskOrder() {

		$field=(isset($_SESSION["task_order"]["field"]) ? $_SESSION["task_order"]["field"] : "t1.priority");
		$type=(isset($_SESSION["task_order"]["type"]) ? $_SESSION["task_order"]["type"] : "DESC");

		$res=array();
		$res["field"]=$field;
		$res["type"]=$type;

		return $res;
	}


	function setTaskOrder($ord) {

		switch ($ord) {
			case "name": {
				$field="t1.name";
				$default_type="ASC";
			} break;
			case "priority": {
				$field="t1.priority";
				$default_type="ASC";
			} break;
			case "status": {
				$field="t1.status";
				$default_type="ASC";
			} break;
			case "date": {
				$field="t1.expire";
				$default_type="DESC";
			} break;
/*			case "closed": {
				$field="t1.closed";
				$default_type="ASC";
			} break; */
			case "company": {
				$field="t2.name";
				$default_type="ASC";
			} break;
			case "project": {
				$field="t3.name";
				$default_type="ASC";
			} break;
		}

		if ((isset($_SESSION["task_order"]["field"])) &&
		    ($field == $_SESSION["task_order"]["field"])) {

			if ($_SESSION["task_order"]["type"] == "ASC")
				$_SESSION["task_order"]["type"]="DESC";
			else
				$_SESSION["task_order"]["type"]="ASC";
		}
		else {
			$_SESSION["task_order"]["field"]=$field;
			$_SESSION["task_order"]["type"]=$default_type;
		}
	}


	function saveTask($data) {

		$task_id=(int)$data["task_id"];
		$prj_id=(int)$data["prj_id"];
		$company_id=(int)$data["company_id"];
		$name=$data["name"];
		$start_date=$GLOBALS["regset"]->regionalToDatabase($data["start_date"]);
		$end_date=$GLOBALS["regset"]->regionalToDatabase($data["end_date"]);
		$expire=$GLOBALS["regset"]->regionalToDatabase($data["expire"]);
		$priority=$data["priority"];
		$status=$data["status"];
		$progress=$data["progress"];


		if ($task_id == 0) {

			if (empty($name)) {
				$lang=& DoceboLanguage::createInstance("company", "framework");
				$name=$lang->def("_NO_NAME");
			}

			$field_list ="prj_id, company_id, name, start_date, end_date, ";
			$field_list.="expire, priority, status, progress";
			$field_val ="'".$prj_id."', '".$company_id."', '".$name."', '".$start_date."', '".$end_date."', ";
			$field_val.="'".$expire."', '".$priority."', '".$status."', '".$progress."'";

			$qtxt="INSERT INTO ".$this->_getTaskTable()." (".$field_list.") VALUES(".$field_val.")";
			$task_id=$this->_executeInsert($qtxt);
		}
		else if ($task_id > 0) {

			$qtxt ="UPDATE ".$this->_getTaskTable()." SET name='".$name."', ";
			$qtxt.="start_date='".$start_date."', end_date='".$end_date."', ";
			$qtxt.="expire='".$expire."', priority='".$priority."', status='".$status."', ";
			$qtxt.="progress='".$progress."' ";
			$qtxt.="WHERE task_id='".$task_id."' AND prj_id='".$prj_id."' AND company_id='".$company_id."'";
			$q=$this->_executeQuery($qtxt);

		}

		return $task_id;
	}


	/**
	 * if prj_id is set will delete all tasks of the
	 * specified project
	 */
	function deleteProjectTask($task_id, $prj_id=FALSE) {

		$task_to_del_arr=array();

		if (($task_id > 0) && ($prj_id === FALSE)) {
			$task_to_del_arr[]=$task_id;
		}
		else if (($prj_id > 0) && ($task_id === FALSE)) {

			$qtxt ="SELECT task_id FROM ".$this->_getTaskTable()." ";
			$qtxt.="WHERE prj_id='".$prj_id."'";
			$q=$this->_executeQuery($qtxt);

			if (($q) && (mysql_num_rows($q) > 0)) {
				while($row=mysql_fetch_assoc($q)) {
					$task_to_del_arr[]=$row["task_id"];
				}
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}


		foreach($task_to_del_arr as $task_id) {

			// delete tasks notes
			$this->deleteNote(FALSE, "task", $task_id);

		}

		if ((is_array($task_to_del_arr)) && (count($task_to_del_arr) > 0)) {
			$qtxt ="DELETE FROM ".$this->_getTaskTable()." ";
			$qtxt.="WHERE task_id IN (".implode(",", $task_to_del_arr).")";

			$q=$this->_executeQuery($qtxt);
		}

	}


	function saveFile($data) {

		require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');

		$file_id=(int)$data["file_id"];
		$parent_id=(int)$data["parent_id"];
		$type=$data["type"];
		$description=$data["filedesc"];


		$path="/doceboCrm/project/";


		if ($file_id == 0) {

			if ((isset($_FILES["file"])) && (!empty($_FILES["file"]["name"]))) {

				$fname=$_FILES["file"]["name"];
				$real_fname=rand(10,99)."_".time()."_".$fname;
				$tmp_fname=$_FILES["file"]["tmp_name"];

				sl_open_fileoperations();
				$f1=sl_upload($tmp_fname, $path.$real_fname);
				sl_close_fileoperations();
			}

			$field_list ="type, parent_id, fname, real_fname, description";
			$field_val="'".$type."', '".$parent_id."', '".$fname."', '".$real_fname."', '".$description."'";

			if ($f1) {
				$qtxt="INSERT INTO ".$this->_getFileTable()." (".$field_list.") VALUES(".$field_val.")";
				$file_id=$this->_executeInsert($qtxt);
			}
		}

		return $file_id;
	}


	function getFileList($type, $parent_id, $ini=FALSE, $vis_item=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getFileTable()." ";
		$qtxt.="WHERE type='".$type."' AND parent_id='".$parent_id."' ";
		$qtxt.="ORDER BY fname ";
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

				$id=$row["file_id"];
				$data_info["data_arr"][$i]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function downloadFile($file_id) {

		require_once($GLOBALS["where_framework"]."/lib/lib.download.php");

		$path="/doceboCrm/project/";

		$qtxt ="SELECT fname, real_fname FROM ".$this->_getFileTable()." ";
		$qtxt.="WHERE file_id='".$file_id."' ";

		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);

			$fn=$row["real_fname"];
			$ext=end(explode(".", $fn));
			$fname=basename($row["fname"], ".".end(explode(".", $row["fname"])));

			sendFile($path, $fn, $ext, $fname);
		}

	}


	function saveNote($data) {

		$note_id=(int)$data["note_id"];
		$type=$data["type"];
		$parent_id=(int)$data["parent_id"];
		$note_txt=$data["note_txt"];
		$user_id=$GLOBALS["current_user"]->getIdSt();


		if ($note_id == 0) {

			$field_list="type, parent_id, note_date, note_author, note_txt";
			$field_val="'".$type."', '".$parent_id."', NOW(), '".$user_id."', '".$note_txt."'";

			$qtxt="INSERT INTO ".$this->_getNoteTable()." (".$field_list.") VALUES(".$field_val.")";
			$note_id=$this->_executeInsert($qtxt);
		}
		else if ($note_id > 0) {

			$qtxt ="UPDATE ".$this->_getNoteTable()." SET note_txt='".$note_txt."' ";
			$qtxt.="WHERE note_id='".$note_id."' AND type='".$type."' AND parent_id='".$parent_id."'";
			$q=$this->_executeQuery($qtxt);

		}

		return $note_id;
	}


	/**
	 *
	 */
	function deleteNote($note_id, $type=FALSE, $parent_id=FALSE) {

		if (($note_id !== FALSE) && ((int)$note_id > 0)) {

			$qtxt ="DELETE FROM ".$this->_getNoteTable()." WHERE ";
			$qtxt.="note_id='".$note_id."'";

		}
		else if (($type !== FALSE) && (!empty($type))) {

			$qtxt ="DELETE FROM ".$this->_getNoteTable()." WHERE ";
			$qtxt.="type='".$type."'";

			if ($parent_id !== FALSE) {
				$qtxt.=" AND parent_id='".$parent_id."'";
			}
		}
		else {
			return FALSE;
		}


		$q=$this->_executeQuery($qtxt);

		return $q;
	}


	function getNoteList($type, $parent_id, $ini=FALSE, $vis_item=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getNoteTable()." ";
		$qtxt.="WHERE type='".$type."' AND parent_id='".$parent_id."' ";
		$qtxt.="ORDER BY note_date DESC ";
		$q=$this->_executeQuery($qtxt);

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$qtxt.="LIMIT ".$ini.",".$vis_item;
			$q=$this->_executeQuery($qtxt);
		}

		$idst_arr=array();

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {

				$id=$row["note_id"];
				$data_info["data_arr"][$i]=$row;
				$this->note_info[$id]=$row;

				if (!in_array($row["note_author"], $idst_arr))
					$idst_arr[]=$row["note_author"];

				$i++;
			}
		}

		if (count($idst_arr) > 0) {
			$acl_manager=$GLOBALS["current_user"]->getAclManager();
			$user_info=$acl_manager->getUsers($idst_arr);
			$data_info["user_info"]=$user_info;
		}
		else {
			$data_info["user_info"]=array();
		}

		return $data_info;
	}


	function loadNoteInfo($note_id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getNoteTable()." ";
		$qtxt.="WHERE note_id='".(int)$note_id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getNoteInfo($note_id) {

		if (!isset($this->note_info[$note_id]))
			$this->note_info[$note_id]=$this->loadNoteInfo($note_id);

		return $this->note_info[$note_id];
	}


	function setProjectTicketTo($prj_id, $val) {

		$qtxt ="UPDATE ".$this->getProjectTable()." SET ticket='".(int)$val."' ";
		$qtxt.="WHERE prj_id='".(int)$prj_id."' LIMIT 1";
		$q=$this->_executeQuery($qtxt);

	}


	function delAssignLog($id) {
		$qtxt ="DELETE FROM ".$this->_getCompanyAssignedTable()." WHERE ";
		$qtxt.="assigned_to='".getLogUserId()."' AND company_id='".(int)$id."' ";
		$qtxt.="LIMIT 1";
		$q =mysql_query($qtxt);

		return $q;
	}


	function addWpTableRow(& $tab, $col1, $col2, $col3=FALSE) {
		$rowcnt =array();
		$rowcnt[]=$col1;
		$rowcnt[]=$col2;
		if ($col3 !== FALSE) {
			$rowcnt[]=$col3;
		}
		$tab->addBody($rowcnt);
	}


	function getRecallOnFilter($type) {
		$res ="0";

		switch ($type) {
			case "expired": {
				$res ="recall_on < DATE_SUB(NOW(), INTERVAL 12 HOUR)";
			} break;
			case "today": {
				$res ="DATE(recall_on) = DATE(NOW())";
			} break;
			case "tomorrow": {
				$res ="DATE(recall_on) = DATE(DATE_ADD(NOW(), INTERVAL 1 DAY))";
			} break;
			case "in_a_week": {
				$res ="recall_on > NOW() AND recall_on < DATE_ADD(NOW(), INTERVAL 1 WEEK)";
			} break;
			case "after_a_week": {
				$res ="recall_on > DATE_ADD(NOW(), INTERVAL 1 WEEK)";
			} break;
		}

		$res.=" AND recall_on > '1000-00-00 00:00:00'";

		return $res;
	}


	function getRecallTotal($type, $with_link=FALSE, $count_all=FALSE) {
		$res ="0";

		$qtxt ="SELECT COUNT(*) as tot FROM ".$this->getMainTable()." WHERE ";
		$qtxt.=$this->getRecallOnFilter($type)." ";
		if (!$count_all) {
			$qtxt.="AND assigned_to='".getLogUserId()."'";
		}

		$q =$this->_executeQuery($qtxt);
		$row =FALSE;
		if ($q) {
			$row =mysql_fetch_assoc($q);
		}

		if ($row !== FALSE) {
			$res =(int)$row["tot"];
		}

		if ($with_link) {
			$base_url ="index.php?mn=crm&amp;pi=40_140&amp;modname=company&amp;op=main";
			$url =$base_url."&amp;set_wp_search=recall&amp;search=".$type;
			if (!$count_all) {
				$url.="&amp;assigned_to_me=1";
			}
			if ($res > 0) {
				$res ='<a href="'.$url.'">'.strval($res).'</a>';
			}
		}

		return strval($res);
	}


	function getCompanyCatTotal($type, $val, $with_link=FALSE, $count_all=FALSE) {
		$res ="0";

		$qtxt ="SELECT COUNT(*) as tot FROM ".$this->getMainTable()." WHERE ";
		switch ($type) {
			case "company_status": {
				$qtxt.="cstatus_id='".(int)$val."' ";
			} break;
			case "company_type": {
				$qtxt.="ctype_id='".(int)$val."' ";
			} break;
			default: {
				$qtxt.="0";
			} break;
		}
		if (!$count_all) {
			$qtxt.="AND assigned_to='".getLogUserId()."'";
		}

		$q =$this->_executeQuery($qtxt);
		$row =FALSE;
		if ($q) {
			$row =mysql_fetch_assoc($q);
		}

		if ($row !== FALSE) {
			$res =(int)$row["tot"];
		}

		if ($with_link) {
			$base_url ="index.php?mn=crm&amp;pi=40_140&amp;modname=company&amp;op=main";
			$url =$base_url."&amp;set_wp_search=".$type."&amp;search=".(int)$val;
			if (!$count_all) {
				$url.="&amp;assigned_to_me=1";
			}
			if ($res > 0) {
				$res ='<a href="'.$url.'">'.strval($res).'</a>';
			}
		}

		return strval($res);
	}


	function getWpRecallTable() {
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
		$res ="";

		$lang =& DoceboLanguage::createInstance("company", "framework");

		$table_caption=$lang->def("_RECALL_ON");
		$table_summary=$lang->def("_TAB_RECALL_ON_SUMMARY");
		$tab =new typeOne(0, $table_caption, $table_summary);

		/* $head =array($lang->def("_PERIOD"));
		$head[]=$lang->def("_NUM");*/
		$head =array("&nbsp;", "&nbsp;", "&nbsp;");

		$head_type=array("", "image", "image");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$expired_all =$this->getRecallTotal("expired", TRUE, TRUE);
		$expired =$this->getRecallTotal("expired", TRUE);
		$today_all =$this->getRecallTotal("today", TRUE, TRUE);
		$today =$this->getRecallTotal("today", TRUE);
		$tomorrow_all =$this->getRecallTotal("tomorrow", TRUE, TRUE);
		$tomorrow =$this->getRecallTotal("tomorrow", TRUE);
		$in_a_week_all =$this->getRecallTotal("in_a_week", TRUE, TRUE);
		$in_a_week =$this->getRecallTotal("in_a_week", TRUE);
		$after_a_week_all =$this->getRecallTotal("after_a_week", TRUE, TRUE);
		$after_a_week =$this->getRecallTotal("after_a_week", TRUE);

		$this->addWpTableRow($tab, $lang->def("_EXPIRED"), $expired, $expired_all);
		$this->addWpTableRow($tab, $lang->def("_TODAY"), $today, $today_all);
		$this->addWpTableRow($tab, $lang->def("_TOMORROW"), $tomorrow, $tomorrow_all);
		$this->addWpTableRow($tab, $lang->def("_IN_A_WEEK"), $in_a_week, $in_a_week_all);
		$this->addWpTableRow($tab, $lang->def("_AFTER_A_WEEK"), $after_a_week, $after_a_week_all);

		$res =$tab->getTable();

		return $res;
	}


	function getWpAssignedTable() {
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
		$res ="";

		$lang =& DoceboLanguage::createInstance("company", "framework");

		$table_caption=$lang->def("_ASSIGN_LOG");
		$table_summary=$lang->def("_TAB_ASSIGN_LOG_SUMMARY");
		$tab =new typeOne(0, $table_caption, $table_summary);

		$head =array($lang->def("_ASSIGNED_ON"));
		$head[]=$lang->def("_COMPANY_NAME");
		$head[]="&nbsp;";

		$head_type=array("", "", "image");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$list=$this->getAssignedLog(getLogUserId());
		$list_arr=$list["data_arr"];
		//$db_tot=$list["data_tot"];

		$base_url ="index.php?mn=crm&amp;pi=40_140&amp;modname=company&amp;op=details&amp;id=";

		$tot=count($list_arr);
		for($i=0; $i<$tot; $i++ ) {

			$company_id=$list_arr[$i]["company_id"];
			$company_name=$list_arr[$i]["company_name"];
			$assigned_on=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["assigned_on"]);

			$company_link ='<a href="'.$base_url.$company_id.'">'.$company_name.'</a>';

			$url="index.php?mn=crm&amp;pi=".getPI()."&amp;op=delcmpasignlog&amp;company_id=".$company_id;
			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
			$img.="title=\"".$lang->def("_DEL")."\" />";
			$del_link ="<a href=\"".$url."\">".$img."</a>\n";

			$this->addWpTableRow($tab, $assigned_on, $company_link, $del_link);

		}

		$res =$tab->getTable();

		return $res;
	}


	function getWpAssigByStatusTable() {
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
		$res ="";

		$lang =& DoceboLanguage::createInstance("company", "framework");

		$table_caption=$lang->def("_ASSIGNED_BY_STATUS");
		$table_summary=$lang->def("_TAB_ASSIGNED_BY_STATUS_SUMMARY");
		$tab =new typeOne(0, $table_caption, $table_summary);

		$head =array("&nbsp;", "&nbsp;", "&nbsp;");

		$head_type=array("", "", "");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$cstatus_info=$this->getCompanyStatusList();
		$cstatus_list=$cstatus_info["list"];

		foreach($cstatus_list as $id=>$val) {
			$tot =$this->getCompanyCatTotal("company_status", $id, $val, TRUE);
			$tot_assigned =$this->getCompanyCatTotal("company_status", $id, $val);
			$this->addWpTableRow($tab, $val, $tot_assigned, $tot);
		}

		$res =$tab->getTable();

		return $res;
	}


	function getWpAssigByTypeTable() {
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
		$res ="";

		$lang =& DoceboLanguage::createInstance("company", "framework");

		$table_caption=$lang->def("_ASSIGNED_BY_TYPE");
		$table_summary=$lang->def("_TAB_ASSIGNED_BY_TYPE_SUMMARY");
		$tab =new typeOne(0, $table_caption, $table_summary);

		$head =array("&nbsp;", "&nbsp;");

		$head_type=array("", "");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$ctype_info=$this->getCompanyTypeList();
		$ctype_list=$ctype_info["list"];

		foreach($ctype_list as $id=>$val) {
			$tot =$this->getCompanyCatTotal("company_type", $id, $val);
			$this->addWpTableRow($tab, $val, $tot);
		}

		$res =$tab->getTable();

		return $res;
	}


	function getWpAssignedTaskTable() {
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
		$res ="";

		$lang =& DoceboLanguage::createInstance("company", "framework");

		$table_caption=$lang->def("_TASK_ASSIGN_LOG");
		$table_summary=$lang->def("_TAB__TASK_ASSIGN_LOG_SUMMARY");
		$tab =new typeOne(0, $table_caption, $table_summary);

		$head =array($lang->def("_ASSIGNED_ON"));
		$head[]=$lang->def("_TASK");
		$head[]="&nbsp;";

		$head_type=array("", "", "image");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$list=$this->getAssignedTaskLog(getLogUserId());
		$list_arr=$list["data_arr"];
		// $db_tot=$list["data_tot"];

		$base_url ="index.php?mn=crm&amp;pi=40_140&amp;modname=taskman&amp;op=main#task_";

		$tot=count($list_arr);
		for($i=0; $i<$tot; $i++ ) {

			$task_id=$list_arr[$i]["task_id"];
			$description =$list_arr[$i]["description"];
			if (empty($description)) {
				$description ="task ".$task_id;
			}
			$assigned_on=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["assigned_on"]);

			$task_link ='<a href="'.$base_url.$task_id.'">'.$description.'</a>';

			$url="index.php?mn=crm&amp;pi=".getPI()."&amp;op=deltasklog&amp;task_id=".$task_id;
			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
			$img.="title=\"".$lang->def("_DEL")."\" />";
			$del_link ="<a href=\"".$url."\">".$img."</a>\n";

			$this->addWpTableRow($tab, $assigned_on, $task_link, $del_link);

		}

		$res =$tab->getTable();

		return $res;
	}


	function getWpTaskSummaryTable($type) {
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
		$res ="";

		$lang =& DoceboLanguage::createInstance("company", "framework");

		$table_caption=$lang->def("_TASK_BY_STATUS");
		$table_summary=$lang->def("_TAB_TASK_BY_STATUS_SUMMARY");
		$tab =new typeOne(0, $table_caption, $table_summary);

		$head =array("&nbsp;", "&nbsp;", "&nbsp;");

		$head_type=array("", "", "");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$cstatus_list=$this->getTaskStatusArray(FALSE, TRUE);

		foreach($cstatus_list as $id=>$val) {
			$tot =$this->getTaskmanTotal($type, "task_status", $id, $val, TRUE);
			$tot_assigned =$this->getTaskmanTotal($type, "task_status", $id, $val);
			$this->addWpTableRow($tab, $val, $tot_assigned, $tot);
		}

		$res =$tab->getTable();

		return $res;
	}


	function saveAssignedLog($company_id, $arr_selection, $arr_deselected) {

		$uid =getLogUserId();

		if ((is_array($arr_selection)) && (!empty($arr_selection))) {

			$ins_arr =array();
			foreach($arr_selection as $user_idst) {
				$ins_arr[]="(".(int)$company_id.", ".(int)$user_idst.", ".$uid.", NOW())";
			}

			if (!empty($ins_arr)) {
				$qtxt ="INSERT INTO ".$this->_getCompanyAssignedTable()." ";
				$qtxt.="(company_id, assigned_to, assigned_by, assigned_on) VALUES ";
				$qtxt.=implode(",", $ins_arr);
				$this->_executeQuery($qtxt);
			}
		}

		if ((is_array($arr_deselected)) && (!empty($arr_deselected))) {

			$qtxt ="DELETE FROM ".$this->_getCompanyAssignedTable()." WHERE ";
			$qtxt.="company_id='".$company_id."' AND assigned_to IN (".implode(",", $arr_deselected).")";
			$this->_executeQuery($qtxt);
		}
	}


	function getAssignedLog($assigned_to) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="t1.*, t2.name as company_name";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getCompanyAssignedTable()." as t1 ";
		$qtxt.="INNER JOIN ".$this->getMainTable()." as t2 ON (t1.company_id=t2.company_id) ";
		$qtxt.="WHERE t1.assigned_to='".(int)$assigned_to."' ";
		$qtxt.="ORDER BY t1.assigned_on DESC ";
		$qtxt.="LIMIT 0, 5";
		$q=$this->_executeQuery($qtxt);


		$idst_arr=array();

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {

				$data_info["data_arr"][]=$row;

				if (!in_array($row["assigned_to"], $idst_arr))
					$idst_arr[]=$row["assigned_to"];
				if (!in_array($row["assigned_by"], $idst_arr))
					$idst_arr[]=$row["assigned_by"];

				$i++;
			}
		}

		if (count($idst_arr) > 0) {
			$acl_manager=$GLOBALS["current_user"]->getAclManager();
			$user_info=$acl_manager->getUsers($idst_arr);
			$data_info["user_info"]=$user_info;
		}
		else {
			$data_info["user_info"]=array();
		}

		return $data_info;
	}



	function getAssignedTaskLog($assigned_to) {
		require_once($GLOBALS["where_crm"]."/modules/taskman/lib.taskman.php");
		$tm =new TaskmanManager();
		return $tm->getAssignedTaskLog($assigned_to);
	}


	function getTaskmanTotal($type, $filter, $val, $with_link=FALSE, $count_all=FALSE) {
		require_once($GLOBALS["where_crm"]."/modules/taskman/lib.taskman.php");
		$tm =new TaskmanManager();
		return $tm->getTaskmanTotal($type, $filter, $val, $with_link, $count_all);
	}


	function getTaskStatusArray($incl_any=FALSE, $excl_closed=FALSE) {
		require_once($GLOBALS["where_crm"]."/modules/taskman/lib.taskman.php");
		$tm =new TaskmanManager();
		return $tm->getStatusArray($incl_any, $excl_closed);
	}


}






// ----------------------------------------------------------------------------




function getCompanySearchQuery() {
	$res=FALSE;
	$first=TRUE;

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("company");

	$search_key=$search->getSearchItem("search_key", "string");
	$company_type=$search->getSearchItem("company_type", "int");
	$company_status=$search->getSearchItem("company_status", "int");
	$recall=$search->getSearchItem("recall", "string");
	$assigned_to_me=$search->getSearchItem("assigned_to_me", "bool");


	if (!empty($search_key)) {
		$res.=($first ? "" : " AND ");
		$res.="name LIKE '%".$search_key."%'";
		$first=FALSE;
	}

	if ($company_type > 0) {
		$res.=($first ? "" : " AND ");
		$res.="ctype_id='".$company_type."'";
		$first=FALSE;
	}

	if ($company_status > 0) {
		$res.=($first ? "" : " AND ");
		$res.="cstatus_id='".$company_status."'";
		$first=FALSE;
	}

	if ((!empty($recall)) && ($recall != "-1")) {
		$cm=new CompanyManager();
		$res.=($first ? "" : " AND ");
		$res.=$cm->getRecallOnFilter($recall);
		$first=FALSE;
	}

	if ($assigned_to_me > 0) {
		$res.=($first ? "" : " AND ");
		$res.="assigned_to='".getLogUserId()."'";
		$first=FALSE;
	}

	return $res;
}


?>
