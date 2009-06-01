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

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------

addCss("style_crm");

require_once($GLOBALS["where_crm"]."/lib/lib.permission.php");

function showCrmMenu($mode="vertical") {
	$res="";

	$lang=DoceboLanguage::createInstance('publicmenu', 'crm');
	$sel_modname =(isset($_GET["modname"]) ? $_GET["modname"] : FALSE);

	if (!isset($_SESSION['current_main_menu']))
		$_SESSION['current_main_menu']=1;

	//find information about the current main area
	if(!isset($GLOBALS['current_main_info'])) {
		$query_main = "
		SELECT name, image
		FROM ".$GLOBALS['prefix_crm']."_public_menu
		WHERE ".	"idMenu = '".$_SESSION['current_main_menu']."'";
		$q=mysql_query($query_main);
		if (($q) && (mysql_num_rows($q) > 0)) {
			list($menu_name, $GLOBALS['current_main_info']['image']) = mysql_fetch_row($q);
			$GLOBALS['current_main_info']['name'] = $lang->def($menu_name);
		}
		else {
			return "";
		}
	}

	$GLOBALS['page']->add('<li><a href="#menu_lat">'.$lang->def('_BLIND_MENU_CRM').'</a></li>', 'blind_navigation');

	$res.=
		//menu intestation
		'<div id="menu_lat" class="menu_box'.($mode == "vertical" ? "" : "_horizontal").'">'."\n";

	if ($mode == "vertical") {
		$res.='<div class="menu_intest">'
			.( ($GLOBALS['current_main_info']['image'] != '') ?
				'<img src="'.getPathImage("crm").'menu/'.$GLOBALS['current_main_info']['image'].'" alt="'
					.$GLOBALS['current_main_info']['name'].'" />' :
				'' )
			.$GLOBALS['current_main_info']['name']
			.'</div>';
	}



	//find information about the element of the menu
	$query_menu = "
	SELECT t1.idUnder, t1.module_name, t1.default_op, t1.default_name, t1.associated_token
	FROM ".$GLOBALS['prefix_crm']."_publicmenu_under AS t1
	WHERE ".( isset($_SESSION['current_main_menu']) ? " t1.idMenu = '".$_SESSION['current_main_menu']."' " : '1' )."
	ORDER BY t1.sequence";
	$re_menu_voice = mysql_query($query_menu);

	$use_simplified=(getPLSetting("crm", "use_simplified", "off") == "off" ? FALSE : TRUE);
	$not_in_simplified=array("task");

	$res.='<ul class="menu_box_list">';

	if (isCrmUser() || isCrmTaskUser()) {
		$url ="http://www.docebo.com/doceboCms/page/40/Area_riservata.html";
		$res.='<li'.(empty($sel_modname) ? ' class="selected"' : '').'>'
			.'<a class="voice" href="'.$url.'">'
			.def("_CRM_HOME", "company", "framework")
			.'</a>'
			.'</li>';
	}

	$extra_perm =array();
	if (isCrmUser()) {
		$extra_perm[]="company";
		$extra_perm[]="abook";
		$extra_perm[]="storedform";
		$extra_perm[]="report";
	}

	if (isCrmTaskUser()) {
		$extra_perm[]="taskman";
		$extra_perm[]="serverlist";
		$extra_perm[]="customerinstall";
	}

	if (isCrmMarketingUser()) {
		$extra_perm[]="marketing";
		$extra_perm[]="activities";
	}

	while(list($id_module, $module_name, $default_op, $default_name, $token) = mysql_fetch_row($re_menu_voice)) {

		if (($GLOBALS["current_user"]->matchUserRole('/crm/module/'.$module_name.'/'.$token)) &&
			  (in_array($module_name, $extra_perm))) {

			if ((!in_array($module_name, $not_in_simplified)) || (!$use_simplified)) {

				$GLOBALS['module_assigned_name'][$module_name] = $lang->def($default_name);
				// TODO // if(checkModPerm($token, $module_name, true)) {

					//if(isset($_SESSION['sel_module_id']) && $_SESSION['sel_module_id'] == $id_module) {

				$res.='<li'.($module_name == $sel_modname ? ' class="selected"' : '').'>'
					.'<a class="voice" href="index.php?mn=crm&amp;pi='.getPI().'&amp;modname='.$module_name.'&amp;op='.$default_op.'">'
					.$GLOBALS['module_assigned_name'][$module_name]
					.'</a>'
					.'</li>';

				// TODO // }
			}
		}
	}


	$res.= '</ul>'."\n".'</div>';

	if ($mode == "horizontal") {
		$res.='<div class="no_float"></div><br />'."\n";
	}

	$GLOBALS["page"]->add($res, "content");
}


function showCrmWelcomePage() {
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	$res ="";

	if ($GLOBALS["current_user"]->isAnonymous()) {
		return $res;
	}

	$out =& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang =& DoceboLanguage::createInstance("company", "framework");
	$cm =new CompanyManager();

	isCrmUser();

	$res.="<div class=\"crm_box\">\n";
	$res.='<div class="width_50_left">';
	$res.=$cm->getWpAssignedTable();
	$res.=$cm->getWpRecallTable();
	$res.=$cm->getWpAssigByStatusTable();
	$res.="</div>\n";

	if (isCrmTaskUser()) {
		$res.='<div class="width_50_right">';
		$res.=$cm->getWpAssignedTaskTable();
		$res.=$cm->getWpTaskSummaryTable("internal");
		$res.=$cm->getWpTaskSummaryTable("customer");
		$res.="</div>\n";
	}


	$res.='<div class="no_float"></div>'."\n";
	$res.="</div>\n"; // crm_box

	$out->add($res);
}


function deleteCrmLog($type, $id) {

	if ((int)$id < 1) {
		return FALSE;
	}

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance("company", "framework");

	$base_url ="index.php?mn=crm&amp;pi=".getPI();
	$back_url ="index.php?special=changearea&amp;newArea=".$GLOBALS["area_id"];

	$url =$base_url;
	if ($type == "company") {
		$url.="&amp;op=delcmpasignlog&amp;company_id=".$id;
	}
	else if ($type == "task") {
		$url.="&amp;op=deltasklog&amp;task_id=".$id;
	}

	if (isset($_POST["canc_del"])) {
		jumpTo($back_url);
	}
	else if (isset($_POST["conf_del"])) {

		if ($type == "company") {
			require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
			$cm =new CompanyManager();
			$cm->delAssignLog($id);
		}
		else if ($type == "task") {
			require_once($GLOBALS["where_crm"]."/modules/taskman/lib.taskman.php");
			$tm =new TaskmanManager();
			$tm->delAssignLog($id);
		}

		jumpTo($back_url);
	}
	else {

		$res="";

		$back_ui_url=$back_url;
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_CRM");
		$title_arr[]=$lang->def("_DELETE_LOG");
		$out->add(getCmsTitleArea($title_arr, "form"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$res.=$form->openForm("del_form", $url);


		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_SURE_DELETE_SEL_LOG').'</span><br />',
			false,
			'conf_del',
			'canc_del');

		$res.=$form->closeForm();
		$res.="</div>\n";

		$out->add($res);
	}
}


?>
