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



Class FieldMapChistory extends FieldMap {

	var $lang=NULL;

	/**
	 * class constructor
	 */
	function FieldMapChistory() {

		$this->lang=& DoceboLanguage::createInstance("company", "crm");

		parent::FieldMap();
	}


	function _getMainTable() {

	}


	function getPrefix() {
		return "chistory_";
	}


	function getPredefinedFieldLabel($field_id) {

		$res["description"]=$this->lang->def("_CHISTORY_DESCRIPTION");

		return $res[$field_id];
	}


	function getRawPredefinedFields() {
		return array("description");
	}


	/**
	 * @param array $predefined_data
	 * @param array $custom_data
	 * @param integer $id company id; if 0 a new company will be created
	 * @param boolean $dropdown_id if true will take dropdown values as id;
	 *                             else will search the id starting from the value.
	 */
	function saveFields($predefined_data, $custom_data=FALSE, $id=0, $dropdown_id=TRUE) {
		require_once($GLOBALS["where_crm"]."/modules/contacthistory/lib.contacthistory.php");

		$chdm=new ContactHistoryDataManager();
		$data=array();


		$company_id=(int)$predefined_data["company_id"];

		$data["contact_id"]=(int)$id;
		$data["title"]=$predefined_data["title"];
		$data["description"]=$predefined_data["description"];
		$data["reason"]=0;
		$data["type"]=$predefined_data["type"];

		if (isset($predefined_data["meeting_date"])) {
			$data["meeting_date"]=$predefined_data["meeting_date"];
		}
		else {
			$data["meeting_date"]=date("Y-m-d H:i:s");
		}


		$chistory_id=$chdm->saveContactHistory($company_id, $data);

		return $chistory_id;
	}


}




?>