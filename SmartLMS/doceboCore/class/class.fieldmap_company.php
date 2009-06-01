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



Class FieldMapCompany extends FieldMap {

	var $lang=NULL;

	/**
	 * class constructor
	 */
	function FieldMapCompany() {

		$this->lang=& DoceboLanguage::createInstance("company", "crm");

		parent::FieldMap();
	}


	function _getMainTable() {

	}


	function getPrefix() {
		return "company_";
	}


	function getPredefinedFieldLabel($field_id) {

		$res["company"]=$this->lang->def("_COMPANY_NAME");
		$res["code"]=$this->lang->def("_COMPANY_CODE");
		$res["ctype"]=$this->lang->def("_COMPANY_TYPE");
		$res["cstatus"]=$this->lang->def("_COMPANY_STATUS");
		$res["address"]=$this->lang->def("_ADDRESS");
		$res["tel"]=$this->lang->def("_COMPANY_TEL");
		$res["email"]=$this->lang->def("_EMAIL");
		$res["vat_number"]=$this->lang->def("_COMPANY_VAT_NUMBER");

		return $res[$field_id];
	}


	function getRawPredefinedFields() {
		return array("company", "code", "ctype", "cstatus", "address", "tel", "email", "vat_number");
	}


	function getCustomFields($with_prefix=TRUE) {
		require_once($GLOBALS["where_framework"]."/lib/lib.field.php");
		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");

		$res=array();
		$fl=new FieldList();
		$ccm=new CoreCompanyManager();

		$idst=$ccm->getCompanyFieldsGroupIdst();

		$fl->setGroupFieldsTable($ccm->getCompanyFieldTable());
		$field_list=$fl->getFieldsArrayFromIdst(array($idst), FIELD_INFO_TRANSLATION);

		$pfx=($with_prefix ? $this->getPrefix()."custom_" : "");
		foreach($field_list as $field_id=>$val) {
			$res[$pfx.$field_id]=$val;
		}

		return $res;
	}

	// TODO ? : getCustomFieldsExtra info with mandatory fields, default values
	// field types and values types and replace code in saveFields and iotask connector*
	// *the part of code that (should) check for mandatory fields


	/**
	 * @param array $predefined_data
	 * @param array $custom_data
	 * @param integer $id company id; if 0 a new company will be created
	 * @param boolean $dropdown_id if true will take dropdown values as id;
	 *                             else will search the id starting from the value.
	 * @param boolean $dropdown_id_for_predefined the same of $dropdown_id but only for predefined fields
	 */
	function saveFields($predefined_data, $custom_data, $id=0, $dropdown_id=TRUE, $dropdown_id_for_predefined=TRUE) {
		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");


		$cm=new CompanyManager();
		$data=array();


		if ($dropdown_id_for_predefined) {

			$ctype_id=(int)$predefined_data["ctype_id"];
			$cstatus_id=(int)$predefined_data["cstatus_id"];

		}
		else { // Finding dropdown id from its values

			$ctype_list=$cm->getCompanyTypeList(FALSE);
			$ctype_arr=array_flip($ctype_list["list"]);

			if (isset($ctype_arr[$predefined_data["ctype_id"]])) {
				$ctype_id=(int)$ctype_arr[$predefined_data["ctype_id"]];
			}
			else {
				$ctype_id=0;
			}


			$cstatus_list=$cm->getCompanyStatusList();
			$cstatus_arr=array_flip($cstatus_list["list"]);

			if (isset($cstatus_arr[$predefined_data["cstatus_id"]])) {
				$cstatus_id=(int)$cstatus_arr[$predefined_data["cstatus_id"]];
			}
			else {
				$cstatus_id=0;
			}

		}

		$data["id"]=(int)$id;
		$data["name"]=$predefined_data["name"];
		$data["code"]=$predefined_data["code"];
		$data["ctype_id"]=$ctype_id;
		$data["cstatus_id"]=$cstatus_id;
		$data["address"]=$predefined_data["address"];
		$data["tel"]=$predefined_data["tel"];
		$data["email"]=$predefined_data["email"];
		$data["vat_number"]=$predefined_data["vat_number"];
		if (isset($predefined_data["imported_from_connection"]))
			$data["imported_from_connection"]=$predefined_data["imported_from_connection"];

		$company_id=$cm->saveData($data, FALSE);


		//print_r($data);

		// -- Custom fields ----------------------------------------------------

		require_once($GLOBALS["where_framework"]."/lib/lib.field.php");
		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");

		$res=array();
		$fl=new FieldList();
		$ccm=new CoreCompanyManager();

		$custom_fields=array_keys($this->getCustomFields(FALSE));
		$company_entry_table=$ccm->getCompanyFieldEntryTable();
		unset($ccm);
		$field_info_arr=$fl->getFieldsFromIdst($custom_fields);

		foreach($custom_fields as $field_id) {

			// store direct
			if (isset($custom_data[$field_id])) {
				$field_obj=& $fl->getFieldInstance($field_id);
				$field_obj->setFieldEntryTable($company_entry_table);
				$field_obj->storeDirect($company_id, $custom_data[$field_id], $dropdown_id, FALSE, TRUE );
			}

		}

		return $company_id;
	}


}




?>