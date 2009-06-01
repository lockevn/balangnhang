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



class ProjectTaskManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $lang=NULL;

	// Company manager
	var $cm=NULL;

	function ProjectTaskManager($prefix="crm", $dbconn=NULL) {
		$this->prefix=$prefix;
		$this->dbconn=$dbconn;

		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
		$this->cm=new CompanyManager();

		$this->lang=& DoceboLanguage::createInstance('task', "crm");
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


	function getCompanyArray($include_any=FALSE, $in_arr=FALSE) {
		$res=array();

		$available_company=$this->cm->getCompanyList(FALSE, FALSE, $in_arr);
		$company_list=$available_company["data_arr"];

		if ($include_any)
			$res[0]=def("_ANY", "ticket", "crm");

		foreach ($company_list as $company) {
			$id=$company["company_id"];
			$res[$id]=$company["name"];
		}

		return $res;
	}


	/**
	 * @param int  $company_id
	 * @param bool $include_any
	 */
	function getProjectArray($company_id, $include_any=FALSE) {
		$res=array();

		$list=$this->cm->getProjectList($company_id);
		$prj_list=$list["data_arr"];

		if ($include_any)
			$res[0]=def("_ANY", "ticket", "crm");

		foreach ($prj_list as $key=>$val) {
			$res[$val["prj_id"]]=$val["name"];
		}

		return $res;
	}

}


?>
