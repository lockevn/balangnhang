<?php
/************************************************************************/
/* DOCEBO CORE - Framework                                              */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2006                                                   */
/* http://www.docebo.org                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

/**
 * @package admin-core
 * @subpackage field
 */
 
if(!defined('IN_DOCEBO')) die('You cannot access this file directly!');

require_once($GLOBALS["where_framework"]."/class/class.fieldmap.php");

Class FieldMapUser extends FieldMap {

	var $lang=NULL;

	/**
	 * class constructor
	 */
	function FieldMapUser() {

		$this->lang=& DoceboLanguage::createInstance("admin_directory", "framework");

		parent::FieldMap();
	}


	function getPrefix() {
		return "user_";
	}


	function getPredefinedFieldLabel($field_id) {

		$res["name"]=$this->lang->def("_DIRECTORY_FIRSTNAME");
		$res["lastname"]=$this->lang->def("_LASTNAME");
		$res["userid"]=$this->lang->def("_USERNAME");

		return $res[$field_id];
	}


	function getRawPredefinedFields() {
		return array("name", "lastname", "userid");
	}


	function getCustomFields($with_prefix=TRUE) {
		require_once($GLOBALS["where_framework"]."/lib/lib.field.php");

		$res=array();
		$fl=new FieldList();

		$acl_manager=$GLOBALS["current_user"]->getAclManager();
		$user_groups=array($acl_manager->getGroupRegisteredId());

		$pfx=($with_prefix ? $this->getPrefix()."custom_" : "");
		$field_list=$fl->getFieldsFromIdst($user_groups);

		foreach($field_list as $field_id=>$val) {
			$res[$pfx.$field_id]=$val[FIELD_INFO_TRANSLATION];
		}

		return $res;
	}


	/**
	 * @param array $predefined_data
	 * @param array $custom_data
	 * @param integer $id user id; if 0 a new user will be created
	 * @param boolean $dropdown_id if true will take dropdown values as id;
	 *                             else will search the id starting from the value.
	 */
	function saveFields($predefined_data, $custom_data, $id=0, $dropdown_id=TRUE) {
		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

		// TODO: at this moment the function works only for user creation;
		// does not update the user if it already exists

		$acl =& $GLOBALS['current_user']->getACL();
		$acl_manager =& $GLOBALS['current_user']->getAclManager();

		$data=array();

		$userid=$predefined_data["userid"];
		$firstname=$predefined_data["firstname"];
		$lastname=$predefined_data["lastname"];
		$pass=$predefined_data["pass"];
		$email=$predefined_data["email"];

		if (!empty($userid)) {
			$idst = $acl_manager->registerUser($userid, $firstname, $lastname,
															$pass, $email, '', '','');
		}
		else {
			$idst=FALSE;
		}

		if($idst !== false) {

			//  -- Add user to registered users group if not importing into root ---

			$idst_oc 			= $acl_manager->getGroup(false, '/oc_0');
			$idst_oc 			= $idst_oc[ACL_INFO_IDST];

			$idst_ocd 			= $acl_manager->getGroup(false, '/ocd_0');
			$idst_ocd 			= $idst_ocd[ACL_INFO_IDST];

			$acl_manager->addToGroup($idst_oc, $idst);
			$acl_manager->addToGroup($idst_ocd, $idst);

			//  -------------------------------------------------------------------|

			// add to group level
			$userlevel = $acl_manager->getGroupST(ADMIN_GROUP_USER);
			$acl_manager->addToGroup($userlevel,$idst );



			// -- Custom fields ----------------------------------------------------

			require_once($GLOBALS["where_framework"]."/lib/lib.field.php");

			$res=array();
			$fl=new FieldList();

			$custom_fields=array_keys($this->getCustomFields(FALSE));
			$field_info_arr=$fl->getFieldsFromIdst($custom_fields);

			foreach($custom_fields as $field_id) {

				// store direct
				if (isset($custom_data[$field_id])) {
					$field_obj=& $fl->getFieldInstance($field_id);
//					$field_obj->setFieldEntryTable($company_entry_table);
					$field_obj->storeDirect($idst, $custom_data[$field_id], $dropdown_id, FALSE, TRUE );
				}

			}
		}

		return $idst;
	}


}


?>