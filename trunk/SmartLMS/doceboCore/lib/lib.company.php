<?php

/*************************************************************************/
/* DOCEBO CORE - Framework                                               */
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
 * @package  admin-library
 * @subpackage module
 * @version  $Id: lib.company.php 905 2007-01-12 11:21:18Z fabio $
 */

class CoreCompanyManager {

	var $prefix = NULL;
	var $dbconn = NULL;
	var $acl_manager  = NULL;
	var $company_group = array();
	var $company_info = array();


	function CoreCompanyManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix = ( $prefix !== false ? $prefix : $GLOBALS["prefix_fw"] );
		$this->dbconn = $dbconn;

		$this->acl_manager =& $GLOBALS["current_user"]->getAclManager();
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


	function getCompanyTable() {
		return $this->prefix."_company";
	}

	function getCompanyUsersTable() {
		return $this->prefix."_company_user";
	}


	function getCompanyFieldTable() {
		return $this->prefix."_company_field";
	}


	function getCompanyFieldEntryTable() {
		return $this->prefix."_company_fieldentry";
	}


	function getCompanyTypeFieldTable() {
		return $this->prefix."_ctype_field";
	}


	function getCompanyStatusFieldTable() {
		return $this->prefix."_cstatus_field";
	}


	function getCompanyList($ini=FALSE, $vis_item=FALSE, $in_arr=FALSE, $where=FALSE, $extra_info=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();
		$data_info["company_id_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->getCompanyTable()." ";

		if ($where !== FALSE) {
			$qtxt.="WHERE ".$where;
		}

		if (($in_arr !== FALSE) && (is_array($in_arr)) && (count($in_arr) > 0)) {
			$qtxt.=($where !== FALSE ? "AND" : "WHERE")." ";
			$qtxt.="company_id IN (".implode(",", $in_arr).") ";
		}

		$qtxt.="ORDER BY name ";
		$q=$this->_executeQuery($qtxt);

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$qtxt.="LIMIT ".$ini.",".$vis_item;
			$q=$this->_executeQuery($qtxt);
		}


		if ($extra_info) {
			$ctm=new CompanyTypeManager();
			$csm=new CompanyStatusManager();
			require_once($GLOBALS["where_crm"]."/modules/ticket/lib.ticketmanager.php");
			$tm=new TicketManager();
		}


		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {

				$id=$row["company_id"];
				$data_info["company_id_arr"][]=$id;
				$data_info["data_arr"][$i]=$row;
				$this->company_info[$id]=$row;

				if (!empty($row["code"])) {
					$code=$row["code"];
					$this->company_info["code"][$code]=$row;
				}

				$data_info["data_arr"][$i]["type_label"]="";
				$data_info["data_arr"][$i]["open_tickets"]=0;

				if ($extra_info) {

					if ($row["ctype_id"] > 0) {
						$ct_info=$ctm->getCompanyTypeInfo($row["ctype_id"]);
						$data_info["data_arr"][$i]["type_label"]=(isset($ct_info["label"]) ? $ct_info["label"] : "--");
					}
					else {
						$data_info["data_arr"][$i]["type_label"]="&nbsp;";
					}

					if ($row["cstatus_id"] > 0) {
						$cs_info=$csm->getCompanyStatusInfo($row["cstatus_id"]);
						$data_info["data_arr"][$i]["status_label"]=(isset($cs_info["label"]) ? $cs_info["label"] : "--");
					}
					else {
						$data_info["data_arr"][$i]["status_label"]="&nbsp;";
					}

					$where="t1.company_id='".$id."' AND t1.closed='0'";
					$ticket_list=$tm->getTicketList(FALSE, FALSE, $where);
					$data_info["data_arr"][$i]["open_tickets"]=$ticket_list["data_tot"];
				}

				$i++;
			}
		}

		return $data_info;
	}

	function getCompaniesAllinfo($companies) {

		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

		$field_list=array();
		$idst = $this->getCompanyFieldsGroupIdst();

		$fl = new FieldList();
		$fl->setGroupFieldsTable($this->getCompanyFieldTable());
		$fl->setFieldEntryTable($this->getCompanyFieldEntryTable());

		$field_list_arr = $fl->getFieldsArrayFromIdst(array($idst), FIELD_INFO_TRANSLATION);
	 	$companies_data = $fl->showFieldForUserArr($companies, array_keys($field_list_arr));
		foreach($companies as $key => $company_id) {

			$stored = $this->getCompanyInfo($company_id);
	 		$companies_data[$company_id] = $stored + $companies_data[$company_id];
		}
		return $companies_data;
	}

	/**
	 * return the field associated as code of the comapnies
	 * @return string
	 */
	function getIdrefCode() {

		if(!isset($GLOBALS['framework']['company_idref_code']) || $GLOBALS['framework']['company_idref_code'] == '') return "code";
		return $GLOBALS['framework']['company_idref_code'];
	}

	/**
	 * set a field as code of the comapnies
	 * @param string	$new_code	the id of the field that must be used or 'code' or 'vat_number'
	 */
	function setIdrefCode($new_code) {

		$GLOBALS['framework']['company_idref_code'] = $new_code;

		return $this->_executeQuery("
		UPDATE ".$GLOBALS['prefix_fw']."_setting
		SET param_value = '".$new_code."'
		WHERE param_name = 'company_idref_code'");
	}

	/**
	 * return the name of the field associated as code of the companies
	 * @return string 	the field
	 */
	function getCompanyIdrefCodeName() {

		$field_assigned = $this->getFieldInfoAssignedToCompany();
		switch($this->getIdrefCode()) {
			case "code" 		: {
				return def('_IDREF_DEFAULT_CODE',"company", "framework");
			};break;
			case "vat_number" 	: {
				return def('_IDREF_VAT_NUMBER',"company", "framework");
			};break;
			default : {
				$id_field = $ccm->getIdrefCode();
				return str_replace('[field_name]', $field_assigned[$id_field][2], def('_IDREF_EXTRA_FIELD',"company", "framework"));
			}
		}
	}

	/**
	 * find a company using a value of the idref code associated
	 * @param mixed $code_to_search the value to search
	 *
	 * @return array	the company founded
	 */
	function getCompanyFromIdrefCode($code_to_search) {

		if($code_to_search == '') return false;
		switch($this->getIdrefCode()) {
			case "code" 		: {

				return $this->getCompanyInfoFromCode($code_to_search);
			};break;
			case "vat_number" 	: {

				$search_company = ""
					." SELECT * "
					." FROM ".$this->getCompanyTable()." "
					." WHERE vat_number = '".$code_to_search."'";
				$re_company = $this->_executeQuery($search_company);
				if ($re_company && (mysql_num_rows($re_company) > 0)) {
					return mysql_fetch_assoc($re_company);
				}
			};break;
			default : {
				$id_field = $this->getIdrefCode();
				require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

				$fl = new FieldList();
				$fl->setGroupFieldsTable($this->getCompanyFieldTable());

				list($company_id) = $fl->getOwnerData($id_field, $code_to_search);
				return $this->getCompanyInfo($company_id);
			}
		}
		return false;
	}

	/**
	 * return the number of company with a valid idref code
	 * @param mixed $code_to_search the value to search
	 *
	 * @return array	the company founded
	 */
	function numberOfCompanyWithIdrefCode() {

		switch($this->getIdrefCode()) {
			case "code" 		: {

				$search_company = ""
					." SELECT COUNT(*) "
					." FROM ".$this->getCompanyTable()." "
					." WHERE code <> ''";
				$re_company = $this->_executeQuery($search_company);
				list($num) = mysql_fetch_row($re_company);
			};break;
			case "vat_number" 	: {

				$search_company = ""
					." SELECT COUNT(*) "
					." FROM ".$this->getCompanyTable()." "
					." WHERE vat_number <> ''";
				$re_company = $this->_executeQuery($search_company);
				list($num) = mysql_fetch_row($re_company);
			};break;
			default : {
				$id_field = $this->getIdrefCode();
				require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

				$fl = new FieldList();
				$fl->setGroupFieldsTable($this->getCompanyFieldTable());

				$num = $fl->getNumberOfFieldEntryData($id_field, true);
			}
		}
		return $num;
	}

	/**
	 * return all the extra field associated to a company
	 *
	 * @return array	return an array with the ids of all the fields associated
	 */
	function getFieldInfoAssignedToCompany() {
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

		$fl = new FieldList();
		$fl->setGroupFieldsTable($this->getCompanyFieldTable());

		$idst = $this->getCompanyFieldsGroupIdst();
		$arr_all_fields = $fl->getFieldsFromIdst(array($idst));
		return $arr_all_fields;
	}

	/**
	 * load in the internal cache the company information
	 * @param int $id the id of the company
	 *
	 * @return array	the company info founded
	 */
	function loadCompanyInfo($id) {

		$res = array();
		$qtxt = "SELECT * "
			." FROM ".$this->getCompanyTable()." "
			."WHERE company_id='".(int)$id."'";
		if(!$q = $this->_executeQuery($qtxt)) return $res;

		if(mysql_num_rows($q) > 0) {
			$res = mysql_fetch_assoc($q);
		}
		return $res;
	}

	/**
	 * return company information
	 * @param int $id the id of the company
	 *
	 * @return array	the company info founded
	 */
	function getCompanyInfo($id) {

		if(!isset($this->company_info[$id])) {

			$info = $this->loadCompanyInfo($id);
			$this->company_info[$id]=$info;
			if(!empty($info["code"])) {

				$code = $info["code"];
				$this->company_info["code"][$code] = $info;
			}
		}
		return $this->company_info[$id];
	}

	/**
	 * load in the internal cache the company information, search with idref code
	 * @param mixed $code the idref of the company
	 *
	 * @return array	the company info founded
	 */
	function loadCompanyInfoFromCode($code) {

		$res = FALSE;
		if(empty($code)) return $res;

		$qtxt ="SELECT * "
			." FROM ".$this->getCompanyTable()." "
			." WHERE code='".$code."'";
		if(!$q = $this->_executeQuery($qtxt)) return false;

		if(mysql_num_rows($q) > 0) {
			$res = mysql_fetch_assoc($q);
		}
		return $res;
	}

	/**
	 * return the company information, search with idref code
	 * @param mixed $code the idref of the company
	 *
	 * @return array	the company info founded
	 */
	function getCompanyInfoFromCode($code) {

		if (!isset($this->company_info["code"][$code])) {

			$info = $this->loadCompanyInfoFromCode($code);
			$this->company_info["code"][$code] = $info;
			if (($info !== FALSE) && (isset($info["company_id"]))) {

				$id = $info["company_id"];
				$this->company_info[$id] = $info;
			}
		}
		return $this->company_info["code"][$code];
	}

	/**
	 * add a user to a company
	 * @param mixed $code the idref of the company
	 *
	 * @return array	the company info founded
	 */
	function addToCompanyUsers($company_id, $user_id) {

		$qtxt ="INSERT INTO ".$this->getCompanyUsersTable()." (company_id, user_id) ";
		$qtxt.="VALUES ('".(int)$company_id."', '".$user_id."')";
		$q=$this->_executeQuery($qtxt);
		return $q;
	}

	/**
	 * return the id of the companies associated to a user
	 * @param int $user_id the idst of the user
	 *
	 * @return array list of companies that user is a member of.
	 **/
	function getUserCompanies($user_id) {

		$res = array();
		$qtxt = "SELECT company_id "
			." FROM ".$this->getCompanyUsersTable()
			." WHERE user_id='".(int)$user_id."'";
		if(!$q = $this->_executeQuery($qtxt)) return $res;
		while($row = mysql_fetch_array($q)) {

			$res[] = $row["company_id"];
		}
		return $res;
	}

	/**
	 * return the list of the companies associated to the users
	 * @param int $company_id the id of the company
	 *
	 * @return array list array( id_user => array(id_companies), ...., 'companies' => array(id,id,id,..) )
	 **/
	function getUsersCompanies($arr_users) {

		if(!is_array($arr_users) || empty($arr_users)) return array();

		$res = array();
		$qtxt = " SELECT user_id, company_id "
		." FROM ".$this->getCompanyUsersTable()
		." WHERE user_id IN ( ".implode(',', $arr_users)." ) ";
		if(!$q = $this->_executeQuery($qtxt)) return $res;

		while($row = mysql_fetch_array($q)) {

			if(!isset($res[$row["user_id"]])) $res[$row["user_id"]] = array();
			$res[$row["user_id"]][] = $row['company_id'];
			$res['companies'][] = $row['company_id'];
		}
		return $res;
	}

	/**
	 * return the list of the associated users
	 * @param int $company_id the id of the company
	 *
	 * @return array list of user with username
	 **/
	function getCompanyUsers($company_id) {

		$res = array();
		$idst_arr = array();
		$qtxt = "SELECT user_id "
		." FROM ".$this->getCompanyUsersTable()
		." WHERE company_id='".$company_id."'";
		if(!$q = $this->_executeQuery($qtxt)) return $res;

		while($row = mysql_fetch_array($q)) {
			$idst_arr[] = $row["user_id"];
		}
		if(count($idst_arr) > 0) {

			$acl_manager = $GLOBALS["current_user"]->getAclManager();
			$user_info =& $acl_manager->getUsers($idst_arr);
			foreach($idst_arr as $idst) {

				$res[$idst] = $acl_manager->relativeId($user_info[$idst][ACL_INFO_USERID]);
			}
		}
		return $res;
	}

	/**
	 * return the list of the associated users, but with some extra info
	 * @param int $company_id the id of the company
	 *
	 * @return array list of user with users info
	 **/
	function getAllCompanyUsers($ini = FALSE, $vis_item = FALSE, $where = FALSE, $extra_info = FALSE, $order_by=FALSE) {

		$data_info = array();
		$data_info["data_arr"] = array();

		$fields = "t1.*".($extra_info ? ", t2.name as company_name" : "");

		$qtxt = " FROM ".$this->getCompanyUsersTable()." as t1 "
			.($extra_info ? ", ".$this->getCompanyTable()." as t2 " : "")
			."WHERE 1 "
				.($extra_info ? "AND t1.company_id=t2.company_id " : "")
				.($where !== FALSE ? "AND ".$where." " : "");

		if ($order_by !== FALSE) {
			$qtxt.="ORDER BY ".$order_by." ";
		}


		$data_info["data_tot"] = 0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$tot = $this->_executeQuery("SELECT COUNT(*) ".$qtxt);
			if($tot) {
				list($data_info["data_tot"]) = mysql_fetch_row($tot);
			}

			$qtxt .=" LIMIT ".$ini.",".$vis_item;
		}

		$qtxt ="SELECT ".$fields.$qtxt;
		$q = $this->_executeQuery($qtxt);


		$idst_arr = array();

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {

				//$id=$row["company_id"];
				$data_info["data_arr"][$i]=$row;
				//$this->company_info[$id]=$row;

				if (($extra_info) && (!in_array($row["user_id"], $idst_arr)))
					$idst_arr[]=$row["user_id"];

				$i++;
			}
		}


		if ($extra_info) {
			if (count($idst_arr) > 0) {
				$acl_manager=$GLOBALS["current_user"]->getAclManager();
				$user_info=$acl_manager->getUsers($idst_arr);
				foreach ($idst_arr as $idst) {
					$data_info["user"][$idst]=$acl_manager->relativeId($user_info[$idst][ACL_INFO_USERID]);
					$data_info["user_email"][$idst]=$user_info[$idst][ACL_INFO_EMAIL];
				}
			}
			else {
				$data_info["user"]=array();
				$data_info["user_email"]=array();
			}
		}

		return $data_info;
	}

	/**
	 * return a list of copany filtered on a value on a custom field
	 * @param int 		$field_id 		the id of the field in which we must search
	 * @param array 	$field_value 	the value to search
	 *
	 * @return array the companies
	 */
	function getUserCompanyFilteredByFieldVal($field_id, $field_value) {
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

		$fl = new FieldList();

		$fl->setGroupFieldsTable($this->getCompanyFieldTable());
		$res =$fl->getOwnerData($field_id, $field_value);

		return $res;
	}

	/**
	 * list of permission for company
	 */
	function getCompanyPermList() {

		return array("view", "buyer");
	}


	function loadCompanyPerm($company_id) {
		$res=array();
		$pl=$this->getCompanyPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();

		foreach($pl as $key=>$val) {

			$role_id="/crm/company/".$company_id."/".$val;
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

	function addCompanyBuyer($company_id, $user_idst) {
		/*
		if(!isset($this->company_group[$company_id])) {

			$group ='/framework/company/'.$company_id.'/users';
			$this->company_group[$company_id]['group'] = $acl_manager->getGroupST($group);
		}	*/
		if(!isset($this->company_group[$company_id]['buyer'])) {

			$role_id="/crm/company/".$company_id."/buyer";
			$role = $acl_manager->getRole(false, $role_id);
			if (!$role)
					$this->company_group[$company_id]['buyer']=$this->acl_manager->registerRole($role_id, "");
				else
					$this->company_group[$company_id]['buyer']=$role[ACL_INFO_IDST];
		}
		$this->acl_manager->addToRole($this->company_group[$company_id]['buyer'], $user_idst );

	}

	function saveCompanyPerm($company_id, $arr_selection, $arr_deselected) {

		require_once($GLOBALS['where_framework'].'/lib/lib.adminmanager.php');
		$adminManager = new AdminManager();
		$acl_manager =& $GLOBALS["current_user"]->getAclManager();

		$group ='/framework/company/'.$company_id.'/users';
		$group_idst =$acl_manager->getGroupST($group);


		$pl=$this->getCompanyPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();
		foreach($pl as $key=>$val) {
			if ((isset($arr_selection)) && (is_array($arr_selection))) {

				$role_id="/crm/company/".$company_id."/".$val;
				$role=$acl_manager->getRole(false, $role_id);
				if (!$role)
					$idst=$acl_manager->registerRole($role_id, "");
				else
					$idst=$role[ACL_INFO_IDST];

				foreach($arr_selection as $user_idst) {
					$acl_manager->addToRole($idst, $user_idst );

					if (($val == "buyer") && ($group_idst !== FALSE)) {
						$adminManager->addAdminTree($group_idst, $user_idst);
					}
				}

				foreach($arr_deselected as $user_idst) {
					$acl_manager->removeFromRole($idst, $user_idst );

					if (($val == "buyer") && ($group_idst !== FALSE)) {
						$adminManager->removeAdminTree($group_idst, $user_idst);
					}
				}

			}
		}

		// Chek if view access is restricted to a set of users
		$role_id="/crm/company/".$company_id."/view";
		$role=$acl_manager->getRole(false, $role_id);
		$restricted=0;

		if ($role) {
			$idst=$role[ACL_INFO_IDST];
			$members_arr=$acl_manager->getRoleMembers($idst);
			if ((is_array($members_arr)) && (count($members_arr) > 0)) {
				$restricted=1;
			}
		}

		$qtxt ="UPDATE ".$this->getCompanyTable()." SET restricted_access='".$restricted."' ";
		$qtxt.="WHERE company_id='".(int)$company_id."'";
		$q=$this->_executeQuery($qtxt);
	}


	function getCompanyFieldsGroupIdst() {
		$acl_manager=$GLOBALS["current_user"]->getAclManager();

		$group_name="/framework/company/fields";
		$idst=$acl_manager->getGroupST($group_name);
		if ($idst === FALSE) {
			$idst=$acl_manager->registerGroup($group_name, "Used to associate a set of fields", TRUE);
		}

		return $idst;
	}


	/**
	 * Outputs html code with user details
	 */
	function getUserInfo($user_id, & $lang) {
		$res="";

		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

		$acl=& $GLOBALS["current_user"]->getAcl();
		$acl_manager=& $GLOBALS["current_user"]->getAclManager();
		$user=$acl_manager->getUser($user_id, FALSE);

		$res.="<p><b>".$lang->def("_FIRSTNAME").":</b> ".$user[ACL_INFO_FIRSTNAME]."</p>\n";
		$res.="<p><b>".$lang->def("_LASTNAME").":</b> ".$user[ACL_INFO_LASTNAME]."</p>\n";
		$res.="<p><b>".$lang->def("_EMAIL").":</b> ";
		$res.="<a href=\"mailto:".$user[ACL_INFO_EMAIL]."\">".$user[ACL_INFO_EMAIL]."</a></p>\n";

		// --- Extra fields:
		$fl = new FieldList();

		$user_groups=$acl->getUserGroupsST($user_id);
		$field_list=$fl->getFieldsFromIdst($user_groups);

		$field_id_arr=array_keys($field_list);
		$user_field_arr=$fl->showFieldForUserArr(array($user_id), $field_id_arr);

		if (is_array($user_field_arr[$user_id]))
	 		$field_val=$user_field_arr[$user_id];
		else
			$field_val=array();

		foreach ($field_val as $field_id=>$value) {
			$res.="<p><b>".$field_list[$field_id][FIELD_INFO_TRANSLATION].":</b> ".$value."</p>\n";
		}

		return $res;
	}


	function deleteCompany($id) {
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		require_once($GLOBALS['where_crm'].'/modules/todo/lib.todo.php');
		require_once($GLOBALS['where_crm'].'/modules/contacthistory/lib.contacthistory.php');
		require_once($GLOBALS['where_crm'].'/modules/company/lib.company.php');

		if ($id < 1)
			return FALSE;

		// delete company field entries
		$fl=new FieldList();
		$fl->setFieldEntryTable($this->getCompanyFieldEntryTable());
		$fl->removeUserEntry($id);

		// delete user company view, .. permissions
		$acl_manager=& $GLOBALS["current_user"]->getAclManager();
		$acl_manager->deleteRoleFromPath("/crm/company/".$id."/");

		// remove users assigned to the company
		$this->removeCompanyUsers($id);

		// delete company todo
		$tdm=new TodoDataManager();
		$tdm->deleteToDo(FALSE, $id);

		// delete contact history entries
		$chdm=new ContactHistoryDataManager();
		$chdm->deleteContactHistory(FALSE, $id);

		// delete company projects
		$cm=new CompanyManager();
		$cm->deleteCompanyProject(FALSE, $id);

		$qtxt="DELETE FROM ".$this->getCompanyTable()." WHERE company_id='".$id."' LIMIT 1";
		$q=$this->_executeQuery($qtxt);

		return $q;
	}


	function removeCompanyUsers($company_id) {

		if ($company_id < 1)
			return FALSE;

		$qtxt="DELETE FROM ".$this->getCompanyUsersTable()." WHERE company_id='".$company_id."'";
		$q=$this->_executeQuery($qtxt);

		return $q;
	}
	/**
	 * remove a user assignement to all the company
	 *
	 */
	function removeUserFromCompany($id_user) {

		$qtxt="DELETE FROM ".$this->getCompanyUsersTable()." WHERE user_id='".$id_user."'";
		$q=$this->_executeQuery($qtxt);

		return $q;
	}


	/**
	 * This one is needed by the company connector
	 * doceboCore/lib/connectors/connector.docebocompany.php
	 */
	function get_row_by_pk($pk, $connector_name) {

		$rename=array("company"=>"name");

		$qtxt ="SELECT company_id, imported_from_connection ";
		$qtxt.="FROM ".$this->getCompanyTable()." WHERE 1";

		foreach($pk as $fieldname=>$fieldvalue) {
			$field=(isset($rename[$fieldname]) ? $rename[$fieldname] : $fieldname);
			$qtxt.=" AND ".$field." = '".addslashes($fieldvalue)."'";
		}

		$q=mysql_query($qtxt);

		if(!$q)
			return FALSE;

		if(mysql_num_rows($q) == 0)
			return 0;

		list($company_id, $imported_from)=mysql_fetch_row($q);

		if($connector_name != $imported_from)
			return 'jump';

		return $company_id;
	}


	/**
	 * This one is needed by the company connector
	 * doceboCore/lib/connectors/connector.docebocompany.php
	 */
	function find_all_notinserted($connector_name, $arr_id_inserted) {
		$res=array();

		$qtxt ="SELECT company_id FROM ".$this->getCompanyTable()." ";
		$qtxt.="WHERE imported_from_connection = '".$connector_name."'";

		if(!empty($arr_id_inserted)) {
			$qtxt.=" AND idCourse NOT IN (".implode($arr_id_inserted , ',').") ";
		}
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {

				$res[]=$row["company_id"];

			}
		}

		return $res;
	}


	function getUserBuyerCompanyId ($user_id)
	{
		$companies_id = array();
		$companies_id = $this->getUserCompanies($user_id);

		if (count($companies_id))
		{
			$buyer_companies_id = array();
			$counter = 0;

			$acl_man = $GLOBALS['current_user']->getAclManager();

			$query_role = "SELECT r.roleid" .
							" FROM ".$this->getCoreRoleTable()." AS r" .
							" JOIN ".$this->getCoreRoleMembersTable()." AS m ON r.idst = m.idst" .
							" WHERE m.idstMember = '".$user_id."'";

			foreach ($companies_id as $id_companies)
			{
				if ($counter)
					$query_role .= " OR r.roleid = '/crm/company/".$id_companies."/buyer'";
				else
					$query_role .= " AND r.roleid = '/crm/company/".$id_companies."/buyer'";

				$counter++;
			}

			$result_role = mysql_query($query_role);

			while (list($roleid) = mysql_fetch_row($result_role))
				$buyer_companies_id[] = str_replace('/crm/company/', '', str_replace('/buyer', '', $roleid));

			if (count($buyer_companies_id))
				return $buyer_companies_id;
		}
		return false;
	}

}




class CoreCompanyAdmin {

	var $lang=NULL;

	function CoreCompanyAdmin() {
		$this->lang=& DoceboLanguage::createInstance("company", "framework");
	}


	function getAddEditForm($id, & $cm, $stored, $op_save=FALSE, $in_crm=FALSE) {
		$res="";

		if (empty($op_save)) {
			$op_save ="save";
		}

		$um =& UrlManager::getInstance();

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$form=new Form();


		$form_code="";
		$form_extra="";
		$url=$um->getUrl("op=".$op_save);

		if ($id == 0) {
			$form_code=$form->openForm("main_form", $url);
			$submit_lbl=$this->lang->def("_INSERT");

			$name="";
			$code="";
			$address="";
			$tel="";
			$email="";
			$vat_number="";
			$ctype_id =FALSE;
			$cstatus_id =FALSE;
			$notes="";
			$recall_on="";
			$assigned_to=FALSE;
			$from_year=date("Y");
		}
		else if ($id > 0) {
			$form_code=$form->openForm("main_form", $url);


			$submit_lbl=$this->lang->def("_MOD");

			$form_extra.=$form->getHidden("edit", "edit", 1);
			$name=$stored["name"];
			$code=$stored["code"];
			$address=$stored["address"];
			$tel=$stored["tel"];
			$email=$stored["email"];
			$vat_number=$stored["vat_number"];
			$ctype_id =(int)$stored["ctype_id"];
			$cstatus_id =(int)$stored["cstatus_id"];
			$notes=$stored["notes"];
			$recall_on=$GLOBALS["regset"]->databaseToRegional($stored["recall_on"]);
			$assigned_to=(isset($stored["assigned_to"]) ? (int)$stored["assigned_to"] : FALSE);
			$from_year=(isset($stored["from_year"]) ? (int)$stored["from_year"] : 0);
		}


		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

		$field_list=array();

		$idst=$cm->ccManager->getCompanyFieldsGroupIdst();

		$fl = new FieldList();
		$fl->setGroupFieldsTable($cm->ccManager->getCompanyFieldTable());
		$field_info_arr=$fl->getFieldsFromIdst(array($idst));
		$field_list=$fl->getFieldsArrayFromIdst(array($idst), FIELD_INFO_ID);


		$fl->setFieldEntryTable($cm->ccManager->getCompanyFieldEntryTable());
		foreach ($field_list as $field_id) {

			$user_access=$field_info_arr[$field_id][FIELD_INFO_USERACCESS];
			$mandatory=($field_info_arr[$field_id][FIELD_INFO_MANDATORY] == "true" ? TRUE : FALSE);

			switch ($user_access) {
				case "noaccess": {
					$show=FALSE;
					$freeze=TRUE;
				} break;
				case "readonly": {
					$show=TRUE;
					$freeze=TRUE;
				} break;
				case "readwrite": {
					$show=TRUE;
					$freeze=FALSE;
				} break;
			}

			if ($show) {
				$form_extra.=$fl->playFieldForUser($id, $field_id, $freeze, $mandatory);
				$form_extra.=$form->getHidden("field_to_store_".$field_id, "field_to_store[".$field_id."]", $field_id);
			}
		}


		$res.=$form_code.$form->openElementSpace();

		$res.=$form->getHidden("id", "id", $id);

		$res.=$form->getTextfield($this->lang->def("_COMPANY_NAME"), "name", "name", 255, $name);

		$res.=$form->getTextfield($this->lang->def("_COMPANY_CODE"), "code", "code", 255, $code);


		$ctype_info=$cm->getCompanyTypeList();
		$cstatus_info=$cm->getCompanyStatusList();

		if(isset($ctype_info["list"]) && !empty($ctype_info["list"])) {
			$res.=$form->getDropdown($this->lang->def("_COMPANY_TYPE"), "ctype_id", "ctype_id", $ctype_info["list"], $ctype_id);
		} else $res.=$form->getHidden("ctype_id", "ctype_id", $ctype_id);

		if(isset($cstatus_info["list"]) && !empty($cstatus_info["list"])) {
			$res.=$form->getDropdown($this->lang->def("_COMPANY_STATUS"), "cstatus_id", "cstatus_id", $cstatus_info["list"], $cstatus_id);
		} else $res.=$form->getHidden("cstatus_id", "cstatus_id", $cstatus_id);

		$res.=$form->getSimpleTextarea($this->lang->def("_ADDRESS"), "address", "address", $address);
		$res.=$form->getTextfield($this->lang->def("_COMPANY_TEL"), "tel", "tel", 255, $tel);
		$res.=$form->getTextfield($this->lang->def("_EMAIL"), "email", "email", 255, $email);
		$res.=$form->getTextfield($this->lang->def("_COMPANY_VAT_NUMBER"), "vat_number", "vat_number", 255, $vat_number);

		//$res.=$form->getSimpleTextarea($this->lang->def("_COMPANY_NOTES"), "notes", "notes", $notes);
		//$res.=$form->getDatefield($this->lang->def("_RECALL_ON").":", "recall_on", "recall_on", $recall_on, false, true);

		if ($in_crm) {
			require_once($GLOBALS["where_crm"]."/admin/modules/crmuser/lib.crmuser.php");
			$crmum =new CrmUserManager();
			$crm_users_arr =$crmum->getCrmUsersArray();
			$res.=$form->getDropdown($this->lang->def("_ASSIGNED_TO"), "assigned_to", "assigned_to", $crm_users_arr, $assigned_to);

			$from_year_arr =array();
			$from_year_arr[0]=$this->lang->def("_NO_VAL");
			for ($i=date("Y")+1; $i>2004; $i--) {
				$from_year_arr[$i]=$i;
			}
			$res.=$form->getDropdown($this->lang->def("_FROM_YEAR"), "from_year", "from_year", $from_year_arr, $from_year);
		}

		$res.=$form_extra;


		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $submit_lbl);
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}

}





// ---------------------------- | lib.companytype.php | -----------------------


Class CompanyTypeManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $ctype_info=array();


	function CompanyTypeManager($prefix="core", $dbconn=NULL) {
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


	function _getMainTable() {
		return $this->prefix."_companytype";
	}


	function getLastOrd($table) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");
		return utilGetLastOrd($table, "ord");
	}


	function moveItem($direction, $id_val) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

		$table=$this->_getMainTable();

		utilMoveItem($direction, $table, "ctype_id", $id_val, "ord");
	}


	function getCompanyTypeList($ini=FALSE, $vis_item=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getMainTable()." ";
		$qtxt.="ORDER BY ord ";
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

				$id=$row["ctype_id"];
				$data_info["data_arr"][$i]=$row;
				$this->ctype_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadCompanyTypeInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getMainTable()." ";
		$qtxt.="WHERE ctype_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getCompanyTypeInfo($id) {

		if (!isset($this->ctype_info[$id]))
			$this->ctype_info[$id]=$this->loadCompanyTypeInfo($id);

		return $this->ctype_info[$id];
	}



	function saveData($data) {

		$id=(int)$data["id"];
		$label=$data["label"];

		if ($id == 0) {
			$ord=$this->getLastOrd($this->_getMainTable())+1;

			if (empty($label)) {
				$lang=& DoceboLanguage::createInstance("companytype", "crm");
				$label=$lang->def("_NO_LABEL");
			}

			$field_list="label, ord";
			$field_val="'".$label."', '".$ord."'";

			$qtxt="INSERT INTO ".$this->_getMainTable()." (".$field_list.") VALUES(".$field_val.")";
			$id=$this->_executeInsert($qtxt);
		}
		else if ($id > 0) {

			$qtxt="UPDATE ".$this->_getMainTable()." SET label='".$label."' WHERE ctype_id='".$id."'";
			$q=$this->_executeQuery($qtxt);

		}

		return $id;
	}


	function deleteCompanyType($id) {
		$qtxt="DELETE FROM ".$this->_getMainTable()." WHERE ctype_id='".$id."' AND is_used='0' LIMIT 1";
		$q=$this->_executeQuery($qtxt);

		return $q;
	}

}









// ---------------------------- | lib.companystatus.php | -----------------------



Class CompanyStatusManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $cstatus_info=array();


	function CompanyStatusManager($prefix="core", $dbconn=NULL) {
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


	function _getMainTable() {
		return $this->prefix."_companystatus";
	}


	function getLastOrd($table) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");
		return utilGetLastOrd($table, "ord");
	}


	function moveItem($direction, $id_val) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

		$table=$this->_getMainTable();

		utilMoveItem($direction, $table, "cstatus_id", $id_val, "ord");
	}


	function getCompanyStatusList($ini=FALSE, $vis_item=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getMainTable()." ";
		$qtxt.="ORDER BY ord ";
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

				$id=$row["cstatus_id"];
				$data_info["data_arr"][$i]=$row;
				$this->cstatus_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadCompanyStatusInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getMainTable()." ";
		$qtxt.="WHERE cstatus_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getCompanyStatusInfo($id) {

		if (!isset($this->cstatus_info[$id]))
			$this->cstatus_info[$id]=$this->loadCompanyStatusInfo($id);

		return $this->cstatus_info[$id];
	}



	function saveData($data) {

		$id=(int)$data["id"];
		$label=$data["label"];

		if ($id == 0) {
			$ord=$this->getLastOrd($this->_getMainTable())+1;

			if (empty($label)) {
				$lang=& DoceboLanguage::createInstance("companystatus", "crm");
				$label=$lang->def("_NO_LABEL");
			}

			$field_list="label, ord";
			$field_val="'".$label."', '".$ord."'";

			$qtxt="INSERT INTO ".$this->_getMainTable()." (".$field_list.") VALUES(".$field_val.")";
			$id=$this->_executeInsert($qtxt);
		}
		else if ($id > 0) {

			$qtxt="UPDATE ".$this->_getMainTable()." SET label='".$label."' WHERE cstatus_id='".$id."'";
			$q=$this->_executeQuery($qtxt);

		}

		return $id;
	}


	function deleteCompanyStatus($id) {
		$qtxt="DELETE FROM ".$this->_getMainTable()." WHERE cstatus_id='".$id."' AND is_used='0' LIMIT 1";
		$q=$this->_executeQuery($qtxt);

		return $q;
	}

}





?>
