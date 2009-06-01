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


class StoredFormManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $lang=NULL;
	var $ccm =NULL;

	var $storedform_info=FALSE;


	function StoredFormManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_cms"]);
		$this->dbconn=$dbconn;

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
		$this->ccm=new CoreCompanyManager();

		$this->lang=& DoceboLanguage::createInstance('storedform', "crm");
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


	function _getInfoTable() {
		return $this->prefix."_form_sendinfo";
	}


	function _getStorageTable() {
		return $this->prefix."_form_storage";
	}


	function _getFormMapTable() {
		return $this->prefix."_form_map";
	}


	function getStoredFormList($ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getInfoTable()." as t1 ";


		$qtxt.="WHERE t1.form_type='crm_contact' ";


		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		$qtxt.="ORDER BY send_date DESC ";
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

				$id=$row["send_id"];
				$data_info["data_arr"][$i]=$row;
				$this->storedform_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadStoredFormInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getInfoTable()." ";
		$qtxt.="WHERE send_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getStoredFormInfo($id) {

		if (!isset($this->storedform_info[$id]))
			$this->storedform_info[$id]=$this->loadStoredFormInfo($id);

		return $this->storedform_info[$id];
	}


	function getStoredFormFields($send_id, $form_id) {
		require_once($GLOBALS["where_cms"]."/modules/feedback/functions.php");

		$acl=& $GLOBALS["current_user"]->getAcl();
		$acl_manager=& $GLOBALS["current_user"]->getAclManager();

		// --- Extra fields:
		$fl = new FieldList();
		$fl->setFieldEntryTable($this->_getStorageTable());

		$field_info=getFormFieldInfo($form_id);
		$field_list=$fl->getFieldsFromArray($field_info["fields"]);

		$field_id_arr=$field_info["fields"];
		$user_field_arr=$fl->showFieldForUserArr(array($send_id), $field_id_arr);

		if (is_array($user_field_arr[$send_id]))
	 		$field_val=$user_field_arr[$send_id];
		else
			$field_val=array();

		$res["field_values"]=$field_val;
		$res["field_list"]=$field_list;

		return $res;
	}


	function getStoredFormDetails($email, $data) {
		$res="";

		$lang=& DoceboLanguage::createInstance("storedform", "crm");


		$res.="<p><b>".$lang->def("_EMAIL").":</b> ";
		$res.="<a href=\"mailto:".$email."\">".$email."</a></p>\n";


		foreach ($data as $resource=>$resource_arr) {
			foreach ($resource_arr as $type=>$type_arr) {
				foreach($type_arr as $field_id=>$field_info) {
					$res.="<p><b>".$field_info["description"].":</b> ".$field_info["value"]."</p>\n";
				}
			}
		}

		return $res;
	}


	function getMappedData($form_id, $send_id) {

		require_once($GLOBALS["where_cms"]."/modules/feedback/functions.php");
		require_once($GLOBALS['where_framework'].'/lib/lib.fieldmap.php');

		$fmm=new FieldMapManager();
		$fmm->setMapTable($GLOBALS["prefix_cms"]."_form_map");
		$fmm->setMapFromTable($this->_getStorageTable());
		$fmm->setMapExtraFilter("form_id='".$form_id."'");
		$fmm->setResourceList("user", "company", "chistory");

		$field_info=getFormFieldInfo($form_id);
		$field_list=$field_info["fields"];
		$res=$fmm->getMappedFields($field_list, $send_id);

		return $res;
	}


	function saveStoredForm($company_id, $data=FALSE) {

		if ($data === FALSE)
			$data=$_POST;

		$storedform_id=(int)$data["storedform_id"];
		$title=$data["title"];
		$description=$data["description"];
		$priority=(int)$data["priority"];
		$status=(int)$data["status"];
		$end_date=$GLOBALS["regset"]->regionalToDatabase($data["end_date"]);


		if ((int)$storedform_id > 0) {

			$qtxt ="UPDATE ".$this->_getInfoTable()." SET title='".$title."', description='".$description."', ";
			$qtxt.="priority='".$priority."', status='".$status."', end_date='".$end_date."' ";
			$qtxt.="WHERE storedform_id='".$storedform_id."' LIMIT 1";
			//$q=$this->_executeQuery($qtxt);

		}
		else {

			$field_list ="company_id, title, description, ";
			$field_list.="status, priority, start_date, end_date";
			$field_val ="'".(int)$company_id."', '".$title."', '".$description."', ";
			$field_val.="'".$status."', '".$priority."', NOW(), '".$end_date."'";

			$qtxt="INSERT INTO ".$this->_getInfoTable()." (".$field_list.") VALUES(".$field_val.")";
			//$storedform_id=$this->_executeInsert($qtxt);
		}

		return $storedform_id;
	}


	function deleteStoredForm($send_id, $form_id=FALSE) {
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		require_once($GLOBALS["where_cms"]."/modules/feedback/functions.php");

		if ($form_id === FALSE) {
			$form_info=$this->getStoredFormInfo($send_id);
			$form_id=$form_info["form_id"];
		}

		// --- Extra fields:
		$fl = new FieldList();
		$fl->setFieldEntryTable($this->_getStorageTable());

		$field_info=getFormFieldInfo($form_id);
		$fl->removeUserEntry($send_id, FALSE, $field_info["fields"]);

		$qtxt ="DELETE FROM ".$this->_getInfoTable()." ";
		$qtxt.="WHERE send_id='".(int)$send_id."'";
		$q=$this->_executeQuery($qtxt);
	}


	function getCompanyArray($include_any=FALSE, $in_arr=FALSE) {
		$res=array();

		$available_company=$this->ccm->getCompanyList(FALSE, FALSE, $in_arr);
		$company_list=$available_company["data_arr"];

		if ($include_any) {
			$res[0]=def("_ANY", "ticket", "crm");
		}

		foreach ($company_list as $company) {
			$id=$company["company_id"];
			$res[$id]=$company["name"];
		}

		return $res;
	}


}


?>
