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
 * @version  $Id: $
 */
// ----------------------------------------------------------------------------
if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

// -- Url Manager Setup --
cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=crm&pi=".getPI()."&modname=report&op=main");
// -----------------------

require_once($GLOBALS["where_crm"]."/modules/abook/lib.abook.php");


function report() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("report", "crm");

	$um=& UrlManager::getInstance();

	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_ABOOK");
	$res.=getCmsTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";


	//$res.=reportCompanyPerYear();
	$res.=reportCompanyTypePerYear();
	$res.=reportAssignedPerYear();
	$res.=reportCompanyStatusPerYear();
	$res.=reportCompanyStatusAssignedPerYear();


	$res.="</div>\n";
	$out->add($res);
}


function reportCompanyPerYear() {
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
	$res ="";

	$lang=& DoceboLanguage::createInstance("report", "crm");
	$company_lang=& DoceboLanguage::createInstance("company", "framework");

	$ccm = new CoreCompanyManager();
	$table =$ccm->getCompanyTable();

	$table_caption=$lang->def("_COMPANY_PER_YEAR");
	$table_summary=$lang->def("_TAB_COMPANY_PER_YEAR_SUMMARY");
	$tab =new typeOne(0, $table_caption, $table_summary);

	$head=array();
	$head_type=array();
	for ($i=date("Y"); $i>date("Y")-8; $i--) {
		$head[]=$i;
		$head_type[]="align_center";
	}

	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$qtxt ="SELECT COUNT(from_year) as tot, from_year FROM ".$table." ";
	$qtxt.="WHERE from_year IS NOT NULL GROUP BY from_year ";
	$qtxt.="ORDER BY from_year DESC";
	$q =mysql_query($qtxt);

	$data =array();

	if ($q) {
		$i =0;
		while($row=mysql_fetch_assoc($q)) {

			$year =$row["from_year"];
			$data[$year]=$row["tot"];
		}
	}


	$rowcnt =array();
	for ($i=date("Y"); $i>date("Y")-8; $i--) {
		$rowcnt[]=(isset($data[$i]) ? $data[$i] : "-");
	}
	$tab->addBody($rowcnt);


	$res =$tab->getTable();

	return $res;
}


function reportCompanyTypePerYear() {
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
	$res ="";

	$lang=& DoceboLanguage::createInstance("report", "crm");
	$company_lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	$ccm =new CoreCompanyManager();
	$ctm =new CompanyTypeManager();
	$table =$ccm->getCompanyTable();
	$type_table =$ctm->_getMainTable(); // evil call

	$table_caption=$lang->def("_COMPANY_TYPE_PER_YEAR");
	$table_summary=$lang->def("_TAB_COMPANY_TYPE_PER_YEAR_SUMMARY");
	$tab =new typeOne(0, $table_caption, $table_summary);

	$head =array();
	if ((isset($_GET["tab"])) && ($_GET["tab"] == "typeperyear") && (isset($_GET["ordby"]))) {
		$ordby =$_GET["ordby"];
		$new_ordby =($ordby == "name" ? "pop" : "name");
	}
	else {
		$ordby ="pop";
		$new_ordby ="name";
	}
	$head[]='<a href="'.$um->getUrl("tab=typeperyear&ordby=".$new_ordby).'">'.$lang->def("_COMPANY_TYPE").'</a>';
	$head_type=array("");
	for ($i=date("Y"); $i>date("Y")-8; $i--) {
		$head[]=$i;
		$head_type[]="align_center";
	}

	switch ($ordby) {
		case "name": {
			$order_by ="t2.label";
		} break;
		case "pop":
		default: {
			$order_by ="tot DESC, t2.label";
		} break;
	}


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$fields ="t1.ctype_id, t2.label, COUNT(t1.from_year) as tot, t1.from_year";
	$qtxt ="SELECT ".$fields." FROM ".$table." as t1 ";
	$qtxt.="RIGHT JOIN ".$type_table." as t2 ON (t1.ctype_id=t2.ctype_id) ";
	$qtxt.="GROUP BY t1.from_year, t1.ctype_id ";
	$qtxt.="ORDER BY ".$order_by;
	$q =mysql_query($qtxt);

	$data =array();
	$label_arr =array();
	$tot_year =array();

	if ($q) {
		while($row=mysql_fetch_assoc($q)) {

			$ctype_id =$row["ctype_id"];
			$year =$row["from_year"];
			$tot =$row["tot"];
			$data[$ctype_id][$year]=$tot;
			$label_arr[$ctype_id]=$row["label"];

			if (!isset($tot_year[$year])) {
				$tot_year[$year]=$tot;
			}
			else {
				$tot_year[$year]=$tot_year[$year]+$tot;
			}

		}
	}


	foreach($data as $ctype_id=>$info) {

		$rowcnt =array();
		$rowcnt[]=$label_arr[$ctype_id];

		for ($i=date("Y"); $i>date("Y")-8; $i--) {
			$rowcnt[]=(isset($info[$i]) ? $info[$i] : "-");
		}

		$tab->addBody($rowcnt);
	}

	$rowcnt =array();
	$rowcnt[]=$lang->def("_TOTAL");

	for ($i=date("Y"); $i>date("Y")-8; $i--) {
		$rowcnt[]=(isset($tot_year[$i]) ? $tot_year[$i] : "-");
	}
	$tab->addBody($rowcnt, "report_total");


	$res =$tab->getTable();

	return $res;
}



function reportAssignedPerYear() {
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
	require_once($GLOBALS["where_crm"]."/admin/modules/crmuser/lib.crmuser.php");
	$res ="";

	$lang=& DoceboLanguage::createInstance("report", "crm");
	$company_lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	$ccm =new CoreCompanyManager();
	$table =$ccm->getCompanyTable();

	$crmum =new CrmUserManager();
	$crm_users_arr =$crmum->getCrmUsersArray();

	$table_caption=$lang->def("_ASSIGNED_TO_ON_YEAR");
	$table_summary=$lang->def("_TAB_ASSIGNED_TO_ON_YEAR_SUMMARY");
	$tab =new typeOne(0, $table_caption, $table_summary);

	$head =array();
	if ((isset($_GET["tab"])) && ($_GET["tab"] == "typeperyear") && (isset($_GET["ordby"]))) {
		$ordby=($_GET["ordby"] == "name" ? "pop" : "name");
	}
	else {
		$ordby="pop";
	}
	$head[]=$lang->def("_ASSIGNED_TO");
	$head_type=array("");
	for ($i=date("Y"); $i>date("Y")-8; $i--) {
		$head[]=$i;
		$head_type[]="align_center";
	}

	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$fields ="COUNT(from_year) as tot, assigned_to, from_year";
	$qtxt ="SELECT ".$fields." FROM ".$table." ";
	$qtxt.="GROUP BY from_year, assigned_to ";
	$qtxt.="ORDER BY from_year DESC";
	$q =mysql_query($qtxt);

	$data =array();
	$tot_year =array();

	if ($q) {
		while($row=mysql_fetch_assoc($q)) {

			$assigned_to =$row["assigned_to"];
			$year =$row["from_year"];
			$tot =$row["tot"];
			$data[$assigned_to][$year]=$tot;

			if (!isset($tot_year[$year])) {
				$tot_year[$year]=$tot;
			}
			else {
				$tot_year[$year]=$tot_year[$year]+$tot;
			}

		}
	}


	foreach($crm_users_arr as $user_idst=>$userid) {

		$rowcnt =array();
		$rowcnt[]=$userid;

		for ($i=date("Y"); $i>date("Y")-8; $i--) {
			$rowcnt[]=(isset($data[$user_idst][$i]) ? $data[$user_idst][$i] : "-");
		}

		$tab->addBody($rowcnt);
	}

	$rowcnt =array();
	$rowcnt[]=$lang->def("_TOTAL");

	for ($i=date("Y"); $i>date("Y")-8; $i--) {
		$rowcnt[]=(isset($tot_year[$i]) ? $tot_year[$i] : "-");
	}
	$tab->addBody($rowcnt, "report_total");


	$res =$tab->getTable();

	return $res;
}



function reportCompanyStatusPerYear() {
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
	$res ="";

	$lang=& DoceboLanguage::createInstance("report", "crm");
	$company_lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	$ccm =new CoreCompanyManager();
	$csm =new CompanyStatusManager();
	$table =$ccm->getCompanyTable();
	$status_table =$csm->_getMainTable(); // evil call

	$table_caption=$lang->def("_COMPANY_STATUS_PER_YEAR");
	$table_summary=$lang->def("_TAB_COMPANY_STATUS_PER_YEAR_SUMMARY");
	$tab =new typeOne(0, $table_caption, $table_summary);

	$head =array();
	if ((isset($_GET["tab"])) && ($_GET["tab"] == "statusperyear") && (isset($_GET["ordby"]))) {
		$ordby =$_GET["ordby"];
		$new_ordby =($ordby == "name" ? "pop" : "name");
	}
	else {
		$ordby ="pop";
		$new_ordby ="name";
	}
	$head[]='<a href="'.$um->getUrl("tab=statusperyear&ordby=".$new_ordby).'">'.$lang->def("_COMPANY_STATUS").'</a>';
	$head_type=array("");
	for ($i=date("Y"); $i>date("Y")-8; $i--) {
		$head[]=$i;
		$head_type[]="align_center";
	}

	switch ($ordby) {
		case "name": {
			$order_by ="t2.label";
		} break;
		case "pop":
		default: {
			$order_by ="tot DESC, t2.label";
		} break;
	}


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$fields ="t1.cstatus_id, t2.label, COUNT(t1.from_year) as tot, t1.from_year";
	$qtxt ="SELECT ".$fields." FROM ".$table." as t1 ";
	$qtxt.="RIGHT JOIN ".$status_table." as t2 ON (t1.cstatus_id=t2.cstatus_id) ";
	$qtxt.="GROUP BY t1.from_year, t1.cstatus_id ";
	$qtxt.="ORDER BY ".$order_by;
	$q =mysql_query($qtxt);

	$data =array();
	$label_arr =array();
	$tot_year =array();

	if ($q) {
		while($row=mysql_fetch_assoc($q)) {

			$cstatus_id =$row["cstatus_id"];
			$year =$row["from_year"];
			$tot =$row["tot"];
			$data[$cstatus_id][$year]=$tot;
			$label_arr[$cstatus_id]=$row["label"];

			if (!isset($tot_year[$year])) {
				$tot_year[$year]=$tot;
			}
			else {
				$tot_year[$year]=$tot_year[$year]+$tot;
			}

		}
	}


	foreach($data as $cstatus_id=>$info) {

		$rowcnt =array();
		$rowcnt[]=$label_arr[$cstatus_id];

		for ($i=date("Y"); $i>date("Y")-8; $i--) {
			$rowcnt[]=(isset($info[$i]) ? $info[$i] : "-");
		}

		$tab->addBody($rowcnt);
	}

	$rowcnt =array();
	$rowcnt[]=$lang->def("_TOTAL");

	for ($i=date("Y"); $i>date("Y")-8; $i--) {
		$rowcnt[]=(isset($tot_year[$i]) ? $tot_year[$i] : "-");
	}
	$tab->addBody($rowcnt, "report_total");


	$res =$tab->getTable();

	return $res;
}


function reportCompanyStatusAssignedPerYear() {
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
	$res ="";

	$lang=& DoceboLanguage::createInstance("report", "crm");
	$company_lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	$ccm =new CoreCompanyManager();
	$csm =new CompanyStatusManager();
	$table =$ccm->getCompanyTable();
	$status_table =$csm->_getMainTable(); // evil call
	$ccm =new CoreCompanyManager();

	$crmum =new CrmUserManager();
	$crm_users_arr =$crmum->getCrmUsersArray();

	$table_caption=$lang->def("_COMPANY_STATUS_ASSIGNED");
	$table_summary=$lang->def("_COMPANY_STATUS_ASSIGNED_SUMMARY");
	$tab =new typeOne(0, $table_caption, $table_summary);

	$head =array();
	if ((isset($_GET["tab"])) && ($_GET["tab"] == "statyrassigned") && (isset($_GET["ordby"]))) {
		$ordby =$_GET["ordby"];
		$new_ordby =($ordby == "name" ? "pop" : "name");
	}
	else {
		$ordby ="pop";
		$new_ordby ="name";
	}
	$head[]='<a href="'.$um->getUrl("tab=statyrassigned&ordby=".$new_ordby).'">'.$lang->def("_COMPANY_STATUS").'</a>';
	$head_type=array("");
	foreach($crm_users_arr as $user_idst=>$userid) {
		$head[]=$userid;
		$head_type[]="align_center";
	}

	switch ($ordby) {
		case "name": {
			$order_by ="t2.label";
		} break;
		case "pop":
		default: {
			$order_by ="tot DESC, t2.label";
		} break;
	}


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$fields ="t1.cstatus_id, t1.assigned_to, t2.label, COUNT(t1.assigned_to) as tot";
	$qtxt ="SELECT ".$fields." FROM ".$table." as t1 ";
	$qtxt.="RIGHT JOIN ".$status_table." as t2 ON (t1.cstatus_id=t2.cstatus_id) ";
	$qtxt.="GROUP BY t1.assigned_to, t1.cstatus_id ";
	$qtxt.="ORDER BY ".$order_by;
	$q =mysql_query($qtxt); // echo $qtxt;

	$data =array();
	$label_arr =array();
	$tot_year =array();

	if ($q) {
		while($row=mysql_fetch_assoc($q)) {

			$cstatus_id =$row["cstatus_id"];
			$assigned_to =$row["assigned_to"];
			$tot =$row["tot"];
			$data[$cstatus_id][$assigned_to]=$tot;
			$label_arr[$cstatus_id]=$row["label"];

			if (!isset($tot_year[$assigned_to])) {
				$tot_year[$assigned_to]=$tot;
			}
			else {
				$tot_year[$assigned_to]=$tot_year[$assigned_to]+$tot;
			}

		}
	}


	foreach($data as $cstatus_id=>$info) {

		$rowcnt =array();
		$rowcnt[]=$label_arr[$cstatus_id];

		foreach($crm_users_arr as $user_idst=>$userid) {
			$rowcnt[]=(isset($info[$user_idst]) ? $info[$user_idst] : "-");
		}

		$tab->addBody($rowcnt);
	}

	$rowcnt =array();
	$rowcnt[]=$lang->def("_TOTAL");

	foreach($crm_users_arr as $user_idst=>$userid) {
		$rowcnt[]=(isset($tot_year[$user_idst]) ? $tot_year[$user_idst] : "-");
	}
	$tab->addBody($rowcnt, "report_total");


	$res =$tab->getTable();

	return $res;
}



// ----------------------------------------------------------------------------

$op=(isset($_GET["op"]) ? $_GET["op"] : "main");

switch ($op) {

	case "main": {
		report();
	} break;

}

?>