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
 * @version  $Id:  $
 */
// ----------------------------------------------------------------------------


Class TicketManager {

	var $prefix=NULL;
	var $dbconn=NULL;


	var $ticket_info=array();
	var $ticket_message_info=array();


	function TicketManager($prefix="crm", $dbconn=NULL) {
		$this->prefix=$prefix;
		$this->dbconn=$dbconn;
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


	function _getMessageTable() {
		return $this->prefix."_ticket_msg";
	}


	function _getStatusTable() {
		return $this->prefix."_ticket_status";
	}


	function createTicket($company_id, $data) {

		$subject=$data["subject"];
		$author=$GLOBALS["current_user"]->getIdSt();
		$text_msg=$data["text_msg"];
		$prj_id=(int)$data["prj_id"];
		$status=(int)$data["status"];

		$field_list ="company_id, prj_id, ";
		$field_list.="post_date, author, subject, status";
		$field_val ="'".$company_id."', '".$prj_id."', ";
		$field_val.="NOW(), '".$author."', '".$subject."', '".$status."'";

		$ticket_id=0;

		if ($prj_id > 0) {
			$qtxt="INSERT INTO ".$this->_getTicketTable()." (".$field_list.") VALUES(".$field_val.")";
			$ticket_id=$this->_executeInsert($qtxt);
		}

		if ((int)$ticket_id > 0) {

			$field_list="ticket_id, post_date, author, text_msg";
			$field_val="'".$ticket_id."', NOW(), '".$author."', '".$text_msg."'";

			$qtxt="INSERT INTO ".$this->_getMessageTable()." (".$field_list.") VALUES(".$field_val.")";
			$ticket_id=$this->_executeInsert($qtxt);

		}

		return $ticket_id;
	}


	function saveTicket($company_id, $data) {

		$ticket_id=(int)$data["ticket_id"];
		$subject=$data["subject"];
		$author=(int)$data["author"];
		$prj_id=(int)$data["prj_id"];
		$priority=(int)$data["priority"];
		$status=(int)$data["status"];


		if ((int)$ticket_id > 0) {

			$qtxt ="UPDATE ".$this->_getTicketTable()." SET prj_id='".$prj_id."', subject='".$subject."', ";
			$qtxt.="author='".$author."', priority='".$priority."', status='".$status."' ";
			$qtxt.="WHERE ticket_id='".$ticket_id."' AND company_id='".(int)$company_id."' LIMIT 1";
			$q=$this->_executeQuery($qtxt);

		}

		return $ticket_id;
	}


	function initTicketConstants() {

		if (!defined("TICKET_CONSTANTS_LOADED")) {
			define("TICKET_CONSTANTS_LOADED", TRUE);
		}
		else
			return 0;

		// --- Priority
		define("PRIORITY_VERYLOW" , 1);
		define("PRIORITY_LOW" , 2);
		define("PRIORITY_MEDIUM" , 3);
		define("PRIORITY_HIGH" , 4);
		define("PRIORITY_VERYHIGH" , 5);
	}


	function getPriorityArray(& $lang) {

		$this->initTicketConstants();

		$res=array();
		$res[PRIORITY_VERYLOW]=$lang->def("_PRIORITY_VERYLOW");
		$res[PRIORITY_LOW]=$lang->def("_PRIORITY_LOW");
		$res[PRIORITY_MEDIUM]=$lang->def("_PRIORITY_MEDIUM");
		$res[PRIORITY_HIGH]=$lang->def("_PRIORITY_HIGH");
		$res[PRIORITY_VERYHIGH]=$lang->def("_PRIORITY_VERYHIGH");


		return $res;
	}


	/**
	 * @param int  $company_id
	 * @param bool $with_ticket if TRUE the project must have at least 1 ticket available
	 *  and the number of availables ticket is shown near the project name.
	 */
	function getProjectArray($company_id, $with_ticket=TRUE, $include_any=FALSE) {
		$res=array();

		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
		$cm=new CompanyManager();

		$list=$cm->getProjectList($company_id);
		$prj_list=$list["data_arr"];

		if ($include_any)
			$res[0]=def("_ANY", "ticket", "crm");

		foreach ($prj_list as $key=>$val) {
			if (!$with_ticket)
				$res[$val["prj_id"]]=$val["name"];
			else if (($with_ticket) && ($val["ticket"] > 0))
				$res[$val["prj_id"]]=$val["name"]." (".$val["ticket"].")";
		}

		return $res;
	}


	function getProjectInfo($prj_id) {

		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
		$cm=new CompanyManager();

		$res=$cm->getProjectInfo($prj_id);

		return $res;
	}


	function getCompanyUserArray($company_id) {
		$res=array();

		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
		$cm=new CompanyManager();

		$res=$cm->getCompanyUsers($company_id);

		return $res;
	}


	function getCompanyArray(& $ccManager, $include_any=FALSE, $in_arr=FALSE) {
		$res=array();

		$available_company=$ccManager->getCompanyList(FALSE, FALSE, $in_arr);
		$company_list=$available_company["data_arr"];

		if ($include_any)
			$res[0]=def("_ANY", "ticket", "crm");

		foreach ($company_list as $company) {
			$id=$company["company_id"];
			$res[$id]=$company["name"];
		}

		return $res;
	}


	function getTicketStatusList($include_any=FALSE) {
		require_once($GLOBALS["where_crm"]."/admin/modules/ticketstatus/lib.ticketstatus.php");

		$res=array();
		$tsm=new TicketStatusManager();

		$list=$tsm->getTicketStatusList();
		$res["info"]=$list["data_arr"];

		if ($include_any)
			$res["list"][0]=def("_ANY", "ticket", "crm");

		foreach ($list["data_arr"] as $info) {

			$id=$info["status_id"];
			$res["list"][$id]=$info["label"];

		}

		return $res;
	}


	function getTicketOrder() {

		$field=(isset($_SESSION["ticket_order"]["field"]) ? $_SESSION["ticket_order"]["field"] : "t1.post_date");
		$type=(isset($_SESSION["ticket_order"]["type"]) ? $_SESSION["ticket_order"]["type"] : "DESC");

		$res=array();
		$res["field"]=$field;
		$res["type"]=$type;

		return $res;
	}


	function getTicketList($ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
		$cm=new CompanyManager();

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="t1.*, t2.name as company_name, t3.name as prj_name";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getTicketTable()." as t1, ";
		$qtxt.=$cm->getMainTable()." as t2, ";
		$qtxt.=$cm->getProjectTable()." as t3 ";

		$qtxt.="WHERE t1.company_id=t2.company_id AND t1.prj_id=t3.prj_id ";
		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}


		$ord=$this->getTicketOrder();
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

				$id=$row["ticket_id"];
				$data_info["data_arr"][$i]=$row;
				$this->ticket_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadTicketInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getTicketTable()." ";
		$qtxt.="WHERE ticket_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getTicketInfo($id) {

		if (!isset($this->ticket_info[$id]))
			$this->ticket_info[$id]=$this->loadTicketInfo($id);

		return $this->ticket_info[$id];
	}


	function getTicketMessageList($ticket_id, $ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getMessageTable()." ";
		$qtxt.="WHERE ticket_id='".(int)$ticket_id."' ";

		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		$qtxt.="ORDER BY post_date DESC ";
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

				$id=$row["message_id"];
				$data_info["data_arr"][$i]=$row;
				$this->ticket_message_info[$id]=$row;

				if (!in_array($row["author"], $idst_arr))
					$idst_arr[]=$row["author"];

				$i++;
			}
		}

		if (count($idst_arr) > 0) {
			$acl_manager=$GLOBALS["current_user"]->getAclManager();
			$user_info=$acl_manager->getUsers($idst_arr);
			foreach ($idst_arr as $idst) {
				$data_info["user"][$idst]=$acl_manager->relativeId($user_info[$idst][ACL_INFO_USERID]);
			}
		}
		else {
			$data_info["user"]=array();
		}

		return $data_info;
	}


	function loadTicketMessageInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getMessageTable()." ";
		$qtxt.="WHERE message_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getTicketMessageInfo($id) {

		if (!isset($this->ticket_message_info[$id]))
			$this->ticket_message_info[$id]=$this->loadTicketMessageInfo($id);

		return $this->ticket_message_info[$id];
	}


	function saveTicketMessage($ticket_id, $data, $from_staff) {

		$message_id=(int)$data["message_id"];
		$text_msg=$data["text_msg"];
		$author=$GLOBALS["current_user"]->getIdSt();

		if (isset($data["status"])) {
			$status=(int)$data["status"];
			$old_status=(int)$data["old_status"];
		}
		else {
			$status=0;
		}


		if ($message_id == 0) {

			$field_list="ticket_id, post_date, author, text_msg, from_staff";
			$field_val="'".$ticket_id."', NOW(), '".$author."', '".$text_msg."', '".$from_staff."'";

			$qtxt="INSERT INTO ".$this->_getMessageTable()." (".$field_list.") VALUES(".$field_val.")";
			$message_id=$this->_executeInsert($qtxt);
		}
		else if ($message_id > 0) {

			$qtxt ="UPDATE ".$this->_getMessageTable()." SET text_msg='".$text_msg."' ";
			$qtxt.="WHERE message_id='".$message_id."' AND ticket_id='".$ticket_id."'";
			$q=$this->_executeQuery($qtxt);

		}

		if (($status > 0) && ($status != $old_status)) {
			$qtxt="UPDATE ".$this->_getTicketTable()." SET status='".$status."' ";
			$qtxt.="WHERE ticket_id='".$ticket_id."' LIMIT 1";
			$q=$this->_executeQuery($qtxt);
		}

		return $message_id;
	}


	function saveTicketLock($data) {

		$ticket_id=(int)$data["ticket_id"];
		$ticket_diff=(int)$data["ticket_diff"];

		$ticket_info=$this->getTicketInfo($ticket_id);
		$prj_id=$ticket_info["prj_id"];
		$prj_info=$this->getProjectInfo($prj_id);
		$ticket=$prj_info["ticket"];

		if ($data["todo"] == "reopen") {
			$new=0;
			$ticket_new=$ticket+$ticket_diff;
		}
		else {
			$new=1;
			$ticket_new=$ticket-$ticket_diff;
		}

		if ($ticket_new < 0)
			$ticket_new=0;

		if ($ticket_diff > 0) {
			$this->setProjectTicketTo($prj_id, $ticket_new);
		}

		$qtxt ="UPDATE ".$this->_getTicketTable()." SET closed='".(int)$new."' ";
		$qtxt.="WHERE ticket_id='".$ticket_id."' LIMIT 1";
		$q=$this->_executeQuery($qtxt);
	}


	function setProjectTicketTo($prj_id, $val) {
		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
		$cm=new CompanyManager();
		$cm->setProjectTicketTo($prj_id, $val);
	}


	function setTicketOrder($ord) {

		switch ($ord) {
			case "title": {
				$field="t1.subject";
				$default_type="ASC";
			} break;
			case "status": {
				$field="t1.status";
				$default_type="ASC";
			} break;
			case "date": {
				$field="t1.post_date";
				$default_type="DESC";
			} break;
			case "closed": {
				$field="t1.closed";
				$default_type="ASC";
			} break;
			case "company": {
				$field="t2.name";
				$default_type="ASC";
			} break;
			case "project": {
				$field="t3.name";
				$default_type="ASC";
			} break;
		}

		if ((isset($_SESSION["ticket_order"]["field"])) &&
		    ($field == $_SESSION["ticket_order"]["field"])) {

			if ($_SESSION["ticket_order"]["type"] == "ASC")
				$_SESSION["ticket_order"]["type"]="DESC";
			else
				$_SESSION["ticket_order"]["type"]="ASC";
		}
		else {
			$_SESSION["ticket_order"]["field"]=$field;
			$_SESSION["ticket_order"]["type"]=$default_type;
		}


	}


	function getTicketPermList() {
		return array("assigned");
	}


	function loadTicketPerm($ticket_id) {
		$res=array();
		$pl=$this->getTicketPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();

		foreach($pl as $key=>$val) {

			$role_id="/crm/ticket/".$ticket_id."/".$val;
			$role=$acl_manager->getRole(false, $role_id);

			if (!$role) {
				$res[$val]=array();
			}
			else {
				$idst=$role[ACL_INFO_IDST];
				$res[$val]=array_flip($acl_manager->getRoleMembers($idst));
			}
		}

		return $res;
	}


	function saveTicketPerm($ticket_id, $arr_selection, $arr_deselected) {

		$pl=$this->getTicketPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();
		foreach($pl as $key=>$val) {
			if ((isset($arr_selection)) && (is_array($arr_selection))) {

				$role_id="/crm/ticket/".$ticket_id."/".$val;
				$role=$acl_manager->getRole(false, $role_id);
				if (!$role)
					$idst=$acl_manager->registerRole($role_id, "");
				else
					$idst=$role[ACL_INFO_IDST];

				foreach($arr_selection as $user_idst) {
					$acl_manager->addToRole($idst, $user_idst );
				}

				foreach($arr_deselected as $user_idst) {
					$acl_manager->removeFromRole($idst, $user_idst );
				}

			}
		}
	}
	
	
	/**
	 * if prj_id is set will delete all tickets of the
	 * specified project
	 */			
	function deleteTicket($ticket_id, $prj_id=FALSE) {
		
		$acl_manager=& $GLOBALS["current_user"]->getAclManager();
		
		$ticket_to_del_arr=array();
		
		if (($ticket_id > 0) && ($prj_id === FALSE)) {
			$ticket_to_del_arr[]=$ticket_id;
		}
		else if (($prj_id > 0) && ($ticket_id === FALSE)) {
			
			$qtxt ="SELECT ticket_id FROM ".$this->_getTicketTable()." ";
			$qtxt.="WHERE prj_id='".$prj_id."'";
			$q=$this->_executeQuery($qtxt);
			
			if (($q) && (mysql_num_rows($q) > 0)) {
				while($row=mysql_fetch_assoc($q)) {
					$ticket_to_del_arr[]=$row["ticket_id"];
				}
			}
			else {
				return FALSE;
			}
		}
		else {
			return FALSE;
		}
		
		
		foreach($ticket_to_del_arr as $ticket_id) {
			
			// Delete ticket permissions
			$role_id="/crm/ticket/".$ticket_id."/";
			$acl_manager->deleteRoleFromPath($role_id);		
			
		}
		
		// Delete the ticket(s)
		if ((is_array($ticket_to_del_arr)) && (count($ticket_to_del_arr) > 0)) {
			$qtxt ="DELETE FROM ".$this->_getTicketTable()." ";
			$qtxt.="WHERE ticket_id IN (".implode(",", $ticket_to_del_arr).")";
			
			$q=$this->_executeQuery($qtxt);
		}		
		
	}


}


?>
