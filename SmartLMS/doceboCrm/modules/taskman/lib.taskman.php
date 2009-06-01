<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2007 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.org                                                 */
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


define("_PRIORITY_LOW", 10);
define("_PRIORITY_MEDIUM", 20);
define("_PRIORITY_HIGH", 30);
define("_PRIORITY_VERYHIGH", 40);

define("_STATUS_NOTSTARTED", 10);
define("_STATUS_INPROGRESS", 20);
define("_STATUS_TOBETESTED", 30);
define("_STATUS_TOBEINSTALLED", 40);
define("_STATUS_FINISHED", 50);
define("_STATUS_PLANNED", 60);
define("_STATUS_SUSPENDED", 70);


class TaskmanManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $lang=NULL;

	// Core Company manager
	var $ccm=NULL;

	function TaskmanManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_crm"]);
		$this->dbconn=$dbconn;

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
		$this->ccm=new CoreCompanyManager();

		$this->lang=& DoceboLanguage::createInstance('taskman', "crm");
	}


	function _query( $query ) {
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- debug $query -->", 'debug' );
		else echo "\n<!-- debug $query -->";
		if( $this->dbconn === NULL )
			$rs = mysql_query( $query );
		else
			$rs = mysql_query( $query, $this->dbconn );
		return $rs;
	}


	function _insQuery( $query ) {
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
		return $this->prefix."_taskman";
	}


	function _getTaskmanLogTable() {
		return $this->prefix."_taskman_assigned_log";
	}


	function _getTaskmanPrjTable() {
		return $this->prefix."_taskman_prj";
	}


	function getPriorityArray($incl_any=FALSE) {
		$res =array();
		if ($incl_any) {
			$res[0] =$this->lang->def("_ANY");
		}
		$res[_PRIORITY_LOW]=$this->lang->def("_PRIORITY_LOW");
		$res[_PRIORITY_MEDIUM]=$this->lang->def("_PRIORITY_MEDIUM");
		$res[_PRIORITY_HIGH]=$this->lang->def("_PRIORITY_HIGH");
		$res[_PRIORITY_VERYHIGH]=$this->lang->def("_PRIORITY_VERYHIGH");

		return $res;
	}


	function getStatusArray($incl_any=FALSE, $excl_closed=FALSE) {
		$res =array();
		if ($incl_any) {
			$res[0] =$this->lang->def("_ANY");
		}
		$res[_STATUS_NOTSTARTED]=$this->lang->def("_STATUS_NOTSTARTED");
		$res[_STATUS_INPROGRESS]=$this->lang->def("_STATUS_INPROGRESS");
		$res[_STATUS_TOBETESTED]=$this->lang->def("_STATUS_TOBETESTED");
		$res[_STATUS_TOBEINSTALLED]=$this->lang->def("_STATUS_TOBEINSTALLED");
		if (!$excl_closed) {
			$res[_STATUS_FINISHED]=$this->lang->def("_STATUS_FINISHED");
		}
		$res[_STATUS_PLANNED]=$this->lang->def("_STATUS_PLANNED");
		$res[_STATUS_SUSPENDED]=$this->lang->def("_SUSPENDED");

		return $res;
	}


	function getProjectArray($incl_add_new=TRUE, $incl_any=FALSE) {
		$res =array();

		if ($incl_any) {
			$res[0] =$this->lang->def("_ANY");
		}

		if ($incl_add_new) {
			$res["add"] =$this->lang->def("_ADD_NEW_OR_SEL_PROJECT");
		}

		$qtxt ="SELECT * FROM ".$this->_getTaskmanPrjTable()." ORDER BY prj_label";
		$q =mysql_query($qtxt);

		if ($q) {
			while($row=mysql_fetch_assoc($q)) {
				$prj_id =$row["prj_id"];
				$res[$prj_id]=$row["prj_label"];
			}
		}

		return $res;
	}


	function getTaskList($ini=FALSE, $vis_item=FALSE, $where=FALSE, $order_by=FALSE) {

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
		$ccm =new CoreCompanyManager();

		$data_info=array();
		$data_info["data_arr"]=array();
		$data_info["user_info"]=array();
		$idst_arr =array();

		$fields ="t1.*, DATEDIFF(t1.scheduled_end_date, NOW()) as scheduled_diff, ";
		$fields.="t2.name as company_name, t3.prj_label";
		$qtxt ="SELECT ".$fields." FROM ".$this->getMainTable()." as t1 ";
		$qtxt.="LEFT JOIN ".$ccm->getCompanyTable()." as t2 ON (t2.company_id = t1.customer_id) ";
		$qtxt.="LEFT JOIN ".$this->_getTaskmanPrjTable()." as t3 ON (t3.prj_id = t1.project_id) ";

		if ($where !== FALSE) {
			$qtxt.="WHERE ".$where." ";
		}

		if ($order_by !== FALSE) {
			$qtxt.="ORDER BY ".$order_by." ";
		}
		else {
			$qtxt.="ORDER BY t1.scheduled_end_date ";
		}
		$q=$this->_query($qtxt);

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$qtxt.="LIMIT ".$ini.",".$vis_item;
			$q=$this->_query($qtxt);
		}


		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_assoc($q)) {

				$id=$row["task_id"];
				$data_info["data_arr"][$i]=$row;
				$assigned_arr =$this->getAssignedToIdstArr($row["assigned_to"]);
				$data_info["data_arr"][$i]["assigned_arr"]=$assigned_arr;
				foreach($assigned_arr as $idst) {
					if ((!empty($idst)) && (!in_array($idst, $idst_arr))) {
						$idst_arr[]=$idst;
					}
				}

				$i++;
			}
		}

		$idst_arr =array_unique($idst_arr);
		if(count($idst_arr) > 0) {

			$acl_manager = $GLOBALS["current_user"]->getAclManager();
			$user_info =& $acl_manager->getUsers($idst_arr);
			foreach($idst_arr as $idst) {
				$username =$user_info[$idst][ACL_INFO_FIRSTNAME]." ".$user_info[$idst][ACL_INFO_LASTNAME];
				$data_info["user_info"][$idst] =(!empty($username) ? $username : $acl_manager->relativeId($user_info[$idst][ACL_INFO_USERID]));
			}
		}

		return $data_info;
	}


	function getTaskInfo($id) {

		$res = array();
		$qtxt = "SELECT * "
			." FROM ".$this->getMainTable()." "
			."WHERE task_id='".(int)$id."'";
		if(!$q = $this->_query($qtxt)) return $res;

		if(mysql_num_rows($q) > 0) {
			$res = mysql_fetch_assoc($q);
		}
		return $res;
	}


	function getTaskProjectInfo($id) {

		$res = array();
		$qtxt = "SELECT * "
			." FROM ".$this->_getTaskmanPrjTable()." "
			."WHERE prj_id='".(int)$id."'";
		if(!$q = $this->_query($qtxt)) return $res;

		if(mysql_num_rows($q) > 0) {
			$res = mysql_fetch_assoc($q);
		}
		return $res;
	}


	function saveData($data) {

		$id=(int)$data["id"];
		$res =$id;

		$description =$data["description"];
		$task_type =$data["task_type"];
		$start_date =$GLOBALS["regset"]->regionalToDatabase($data["start_date"]);
		$scheduled_end_date =$GLOBALS["regset"]->regionalToDatabase($data["scheduled_end_date"]);
		$actual_end_date =$GLOBALS["regset"]->regionalToDatabase($data["actual_end_date"]);
		$priority_id =(int)$data["priority_id"];
		$status_id =(int)$data["status_id"];
		$waiting_answer =(int)$data["waiting_answer"];
		$waiting_answer_notes =$data["waiting_answer_notes"];
		$notes =$data["notes"];

		if ($task_type == "internal") {
			if ($data["project_id"] == "add") {
				$prj_label =(!empty($data["project"]) ? $data["project"] : def("_UNAMED"));
				$qtxt ="INSERT INTO ".$this->_getTaskmanPrjTable()." (prj_label) ";
				$qtxt.="VALUES('".$prj_label."')";
				$project_id =$this->_insQuery($qtxt);
			}
			else {
				$project_id =(int)$data["project_id"];
			}
		}

		if ($task_type == "customer") {
			$customer_id =(int)$data["customer_id"];
			$customerinstall_id =(int)$data["customerinstall_id"];
		}

		if ($id == 0) {

			$field_list =(isset($project_id) ? "project_id, " : "");
			$field_list.=(isset($customer_id) ? "customer_id, " : "");
			$field_list.=(isset($customerinstall_id) ? "customerinstall_id, " : "");
			$field_list.="description, task_type, start_date, scheduled_end_date, ";
			$field_list.="actual_end_date, priority_id, status_id, waiting_answer, ";
			$field_list.="waiting_answer_notes, notes";
			$field_val =(isset($project_id) ? "'".$project_id."', " : "");
			$field_val.=(isset($customer_id) ? "'".$customer_id."', " : "");
			$field_val.=(isset($customerinstall_id) ? "'".$customerinstall_id."', " : "");
			$field_val.="'".$description."', '".$task_type."', '".$start_date."', ";
			$field_val.="'".$scheduled_end_date."', '".$actual_end_date."', '".$priority_id."', ";
			$field_val.="'".$status_id."', '".$waiting_answer."', '".$waiting_answer_notes."', '".$notes."'";

			$qtxt ="INSERT INTO ".$this->getMainTable()." (".$field_list.") VALUES(".$field_val.")";
			$id =$this->_insQuery($qtxt);

			if ($id > 0) {
				$res =$id;
			}
			else {
				echo $qtxt."<br />".mysql_error();
				die();
			}
		}
		else if ($id > 0) {

			$qtxt ="UPDATE ".$this->getMainTable()." SET ";
			$qtxt.=(isset($project_id) ? "project_id='".$project_id."', " : "");
			$qtxt.=(isset($customer_id) ? "customer_id='".$customer_id."', " : "");
			$qtxt.=(isset($customerinstall_id) ? "customerinstall_id='".$customerinstall_id."', " : "");
			$qtxt.="description='".$description."', ";
			$qtxt.="start_date='".$start_date."', scheduled_end_date='".$scheduled_end_date."', ";
			$qtxt.="actual_end_date='".$actual_end_date."', priority_id='".$priority_id."', ";
			$qtxt.="status_id='".$status_id."', waiting_answer='".$waiting_answer."', ";
			$qtxt.="waiting_answer_notes='".$waiting_answer_notes."', notes='".$notes."' ";
			$qtxt.="WHERE task_id='".$id."'";
			$q =$this->_query($qtxt);
			$res =$id;
		}

		return $res;
	}


	function saveProject($data) {

		$id=(int)$data["id"];
		$res =$id;

		$project =$data["project"];

		if ($id == 0) {

			$field_list ="prj_label";
			$field_val ="'".$project."'";

			$qtxt ="INSERT INTO ".$this->_getTaskmanPrjTable()." (".$field_list.") VALUES(".$field_val.")";
			$id =$this->_insQuery($qtxt);

			if ($id > 0) {
				$res =$id;
			}
			else {
				echo $qtxt."<br />".mysql_error();
				die();
			}
		}
		else if ($id > 0) {

			$qtxt ="UPDATE ".$this->_getTaskmanPrjTable()." SET ";
			$qtxt.="prj_label='".$project."' ";
			$qtxt.="WHERE prj_id='".$id."'";
			$q =$this->_query($qtxt);
			$res =$id;
		}

		return $res;
	}


	function deleteTask($task_id) {

		$qtxt ="SELECT task_type, project_id FROM ".$this->getMainTable()." WHERE task_id='".(int)$task_id."' LIMIT 1";
		$q =$this->_query($qtxt);

		$check_empty_prj =FALSE;
		if ($q) {
			$row =mysql_fetch_assoc($q);
			$task_type =$row["task_type"];
			$project_id =$row["project_id"];

			if	 (($task_type == "internal") && ($project_id > 0)) {
				$check_empty_prj =TRUE;
			}
		}

		$qtxt ="DELETE FROM ".$this->getMainTable()." WHERE task_id='".(int)$task_id."' LIMIT 1";
		$q =$this->_query($qtxt);

		if ($check_empty_prj) {
			$qtxt ="SELECT COUNT(*) as tot FROM ".$this->getMainTable()." WHERE project_id='".(int)$project_id."'";
			$q =$this->_query($qtxt);

			if ($q) {
				$row =mysql_fetch_assoc($q);
				$tot =$row["tot"];

				if ($tot == 0) {
					$qtxt ="DELETE FROM ".$this->_getTaskmanPrjTable()." WHERE prj_id='".(int)$project_id."' LIMIT 1";
					$q =$this->_query($qtxt);
				}
			}
		}

		return $q;
	}


	function getAssignedToIdstArr($val) {
		return explode(",", trim($val, ","));
	}


	function getAssignedToIdstStr($val) {
		return ",".implode(",", (array)$val).",";
	}


	function updateAssignedTo($task_id, $assigned_to_arr, $old_assigned_to_arr=FALSE) {
		if ($old_assigned_to_arr === FALSE) {
			$old_assigned_to_arr =array();
		}
		$assigned_to =$this->getAssignedToIdstStr($assigned_to_arr);

		$qtxt ="UPDATE ".$this->getMainTable()." SET ";
		$qtxt.="assigned_to='".$assigned_to."' ";
		$qtxt.="WHERE task_id='".(int)$task_id."'";
		$q =$this->_query($qtxt);

		$uid =getLogUserId();
		$ins_arr =array();
		foreach($assigned_to_arr as $user_idst) {
			if (!in_array($user_idst, $old_assigned_to_arr)) {
				$ins_arr[]="(".(int)$task_id.", ".(int)$user_idst.", ".$uid.", NOW())";
			}
		}

		if (!empty($ins_arr)) {
			$qtxt ="INSERT INTO ".$this->_getTaskmanLogTable()." ";
			$qtxt.="(task_id, assigned_to, assigned_by, assigned_on) VALUES ";
			$qtxt.=implode(",", $ins_arr);
			$this->_query($qtxt);
		}

		return $q;
	}


	function getStatusColorArr() {
		$res =array();

		$res[_STATUS_NOTSTARTED]="back_color_white";
		$res[_STATUS_INPROGRESS]="back_color_yellow";
		$res[_STATUS_TOBETESTED]="back_color_lightorange";
		$res[_STATUS_TOBEINSTALLED]="back_color_darkorange";
		$res[_STATUS_FINISHED]="back_color_green";
		$res[_STATUS_PLANNED]="back_color_cyan";
		$res[_STATUS_SUSPENDED]="back_color_grey";

		return $res;
	}


	function getAssignedTaskLog($assigned_to) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="t1.*, t2.description";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getTaskmanLogTable()." as t1 ";
		$qtxt.="INNER JOIN ".$this->getMainTable()." as t2 ON (t1.task_id=t2.task_id) ";
		$qtxt.="WHERE t1.assigned_to='".(int)$assigned_to."' ";
		$qtxt.="ORDER BY t1.assigned_on DESC ";
		$qtxt.="LIMIT 0, 5";
		$q=$this->_query($qtxt);


		$idst_arr=array();

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_assoc($q)) {

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


	function getTaskmanTotal($type, $filter, $val, $with_link=FALSE, $count_all=FALSE) {
		$res ="0";

		$search ="";
		$qtxt ="SELECT COUNT(*) as tot FROM ".$this->getMainTable()." WHERE ";
		switch ($filter) {
			case "task_status": {
				$qtxt.="status_id='".(int)$val."' ";
				$search ="status_id";
			} break;
			default: {
				$qtxt.="0";
			} break;
		}
		if (!$count_all) {
			$qtxt.="AND assigned_to LIKE '%,".getLogUserId().",%' ";
		}

		$qtxt.="AND task_type='".$type."' ";

		$q =$this->_query($qtxt);
		$row =FALSE;
		if ($q) {
			$row =mysql_fetch_assoc($q);
		}

		if ($row !== FALSE) {
			$res =(int)$row["tot"];
		}

		if ($with_link) {
			$base_url ="index.php?mn=crm&amp;pi=40_140&amp;modname=taskman&amp;op=main";
			$url =$base_url."&amp;set_wp_search=".$search."&amp;search=".(int)$val;
			if (!$count_all) {
				$url.="&amp;assigned_to_me=1";
			}
			if ($res > 0) {
				$res ='<a href="'.$url.'">'.strval($res).'</a>';
			}
		}

		return strval($res);
	}


	function delAssignLog($id) {
		$qtxt ="DELETE FROM ".$this->_getTaskmanLogTable()." WHERE ";
		$qtxt.="assigned_to='".getLogUserId()."' AND task_id='".(int)$id."' ";
		$qtxt.="LIMIT 1";
		$q =mysql_query($qtxt);

		return $q;
	}


	function getCustomerInstallArr($company_id) {
		require_once($GLOBALS["where_crm"]."/modules/customerinstall/lib.customerinstall.php");
		$res =array();

		$cim =new CustomerInstallManager();
		$arr =$cim->getCustomerInstall_FilterByCompany($company_id);

		foreach($arr as $val) {
			$id =$val["customerinstall_id"];
			$res[$id]=$val["domain"];
		}

		return $res;
	}


}


?>
