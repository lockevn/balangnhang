<?php
/*************************************************************************/
/* DOCEBO CRM - Customer Relationship Management                         */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2007 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.com                                                 */
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
if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

// -- Url Manager Setup --
cmsUrlManagerSetup(FALSE, FALSE, FALSE, "mn=crm&pi=".getPI()."&modname=taskman&op=main");
// -----------------------

require_once($GLOBALS["where_crm"]."/modules/taskman/lib.taskman.php");


function taskman() {
	$res="";

	addScriptaculousJs();
	addJs($GLOBALS["where_crm_relative"]."/modules/taskman/", "taskman.js");

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("taskman", "crm");
	$um=& UrlManager::getInstance();

/*	$acl_manager=$GLOBALS["current_user"]->getAclManager();
	$user_idst=$GLOBALS["current_user"]->getIdST();
	$roles=$acl_manager->getUserRoleFromPath($user_idst, "/crm/company", "view"); */

	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_TASKMAN");
	$res.=getCmsTitleArea($title_arr, "taskman");
	$res.="<div class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$res.=printTaskmanSearchForm();

	$res.=getTaskmanTable("internal");
	$res.=getTaskmanTable("customer");

	$res.="</div>\n";
	$out->add($res);
}


function getTaskmanTable($type) {
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

	$res ="";
	$lang=& DoceboLanguage::createInstance("taskman", "crm");
	$um=& UrlManager::getInstance();

	$tm =new TaskmanManager();


	$table_caption=$lang->def("_".strtoupper($type)."_TASK_TAB_CAPTION");
	$table_summary=$lang->def("_".strtoupper($type)."_TASK_TAB_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];

	$um->addToStdQuery("type=".$type);
	$tab=new typeOne($vis_item, $table_caption, $table_summary);

	$base_url="op=setorder&ord=";

	$head=array("");
	$head_type=array("image");
	if ($type == "internal") {
		$head[]=$lang->def("_PROJECT");
	}
	else if ($type == "customer") {
		$head[]=$lang->def("_CUSTOMER");
	}
	$head_type[]="";
	$head[]="<a href=\"".$um->getUrl($base_url."description")."\">".$lang->def("_DESCRIPTION")."</a>";
	$head_type[]="";
	$head[]="<a href=\"".$um->getUrl($base_url."assignedto")."\">".$lang->def("_ASSIGNED_TO")."</a>";
	$head_type[]="";
	$head[]="<a href=\"".$um->getUrl($base_url."schedend")."\">".$lang->def("_SCHEDULED_END_DATE")."</a>";
	$head_type[]="";
	$head[]="<a href=\"".$um->getUrl($base_url."timediff")."\">".$lang->def("_TIMEDIFF")."</a>";
	$head_type[]="align_center";
	$head[]="<a href=\"".$um->getUrl($base_url."priority")."\">".$lang->def("_PRIORITY")."</a>";
	$head_type[]="image";
	$head[]="<a href=\"".$um->getUrl($base_url."status")."\">".$lang->def("_STATUS")."</a>";
	$head_type[]="";

	if ($type == "customer") {
		$img ="<img src=\"".getPathImage()."standard/wait_alarm.png\" alt=\"".$lang->def("_ALT_WAITING_ANSWER")."\" ";
		$img.="title=\"".$lang->def("_WAITING_ANSWER")."\" />";
		$head[]=$img;
		$head_type[]="image";
	}


	$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$lang->def("_ALT_ASSIGNUSERS")."\" ";
	$img.="title=\"".$lang->def("_ALT_ASSIGNUSERS")."\" />";
	$head[]=$img;
	$head_type[]="image";
	$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;
	$head_type[]="image";
	$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;
	$head_type[]="image";


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$tab->initNavBar('ini', 'link');
	$tab->setLink($um->getUrl());

	if ((isset($_SESSION["taskman_ini"])) && (!isset($_GET["ini"]))) {
		$ini =(int)$_SESSION["taskman_ini"];
	}
	else {
		$ini =$tab->getSelectedElement();
		$_SESSION["taskman_ini"] =$ini;
	}

	$where ="t1.task_type='".$type."'";
	$search_q =getTaskmanSearchQuery();
	$where.=(!empty($search_q) ? "AND ".$search_q : "");

	$level=$GLOBALS["current_user"]->getUserLevelId();
	$is_admin=($level == ADMIN_GROUP_ADMIN ? TRUE : FALSE);
	$is_god_admin=($level == ADMIN_GROUP_GODADMIN ? TRUE : FALSE);

	$ord =getTaskmanOrder();
	if ($type == "internal") {
		$order_by ="t3.prj_label ASC";
	}
	else if ($type == "customer") {
		$order_by ="t2.name ASC";
	}
	$order_by.=", ".$ord["field"]." ".$ord["type"]." ";

	$list=$tm->getTaskList($ini, $vis_item, $where, $order_by);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];


	require_once($GLOBALS["where_crm"]."/admin/modules/crmuser/lib.crmuser.php");
	$crmum =new CrmUserManager();
	$crm_users_arr =$crmum->getCrmUsersArray(TRUE);

	$priority_arr =$tm->getPriorityArray();
	$status_arr =$tm->getStatusArray();
	$status_color_arr =$tm->getStatusColorArr();

	$img_path =getPathImage('franework')."standard/";

	$prev_grp_index =FALSE;

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["task_id"];
		$project_id =$list_arr[$i]["project_id"];
		$customer_id =$list_arr[$i]["customer_id"];
		$url_details=$um->getUrl("op=details&id=".$id);

		$ins_sub_header =FALSE;
		if (($type == "internal") && ($project_id != $prev_grp_index)) {
			$rowcnt=array_fill(0, 11, "&nbsp;");
			$rowcnt[1]=$list_arr[$i]["prj_label"];

			$url=$um->getUrl("op=editprj&id=".$project_id);
			$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
			$img.="title=\"".$lang->def("_MOD")."\" />";
			$rowcnt[9]="<a href=\"".$url."\">".$img."</a>\n";
/*
			$url=$um->getUrl("op=delprj&id=".$project_id);
			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
			$img.="title=\"".$lang->def("_DEL")."\" />";
			$rowcnt[10]="<a href=\"".$url."\">".$img."</a>\n";
*/

			$tab->addBody($rowcnt, "task_sub_header");
		}
		else if (($type == "customer") && ($customer_id != $prev_grp_index)) {
			$rowcnt=array_fill(0, 12, "&nbsp;");
			$rowcnt[1]=$list_arr[$i]["company_name"];

			if (isCrmUser()) {
				$url=$um->getUrl("modname=company&op=details&tab_op=editcompany&id=".$customer_id);
				$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
				$img.="title=\"".$lang->def("_MOD")."\" />";
				$rowcnt[9]="<a href=\"".$url."\">".$img."</a>\n";
			}

			$tab->addBody($rowcnt, "task_sub_header");
		}

		if ($type == "internal") {
			$prev_grp_index =$project_id;
		}
		else if ($type == "customer") {
			$prev_grp_index =$customer_id;
		}

		$rowcnt=array();

		$jsid =$type.$i;
		$more ='<span id="expander_'.$jsid.'"><a href="javascript:expandList(\''.$jsid.'\', \''.$img_path.'\');">';
    $more.='<img src="'.$img_path.'more.gif" alt="more" /></a></span>';
		$rowcnt[]=$more;

		$rowcnt[]="&nbsp;";

		$rowcnt[]='<a id="task_'.$id.'"></a>'.$list_arr[$i]["description"];

		$assigned_to_txt ="";
		foreach($list_arr[$i]["assigned_arr"] as $user_idst) {
			$assigned_to_txt.=$list["user_info"][$user_idst].", ";
		}
		$rowcnt[]=trim($assigned_to_txt, ", ");

		$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["scheduled_end_date"], "date");
		$status_id =$list_arr[$i]["status_id"];
		if ($status_id != _STATUS_FINISHED) {
			$scheduled_diff =$list_arr[$i]["scheduled_diff"];
			if ($scheduled_diff < 0) {
				$scheduled_diff_txt ='<span class="task_expired">'.$scheduled_diff.'</span>';
			}
			else {
				$scheduled_diff_txt =$scheduled_diff;
			}
			$rowcnt[]=$scheduled_diff_txt;
		}
		else {
			$rowcnt[]="&nbsp;";
		}


		$priority_id =$list_arr[$i]["priority_id"];
		$priority_img ='<img src="'.getPathImage('crm').'taskman/priority_'.$priority_id.'.png" ';
		$priority_img.='alt="'.$priority_arr[$priority_id].'" title="'.$priority_arr[$priority_id].'" />';
		$rowcnt[]=$priority_img;
		$rowcnt[]=$status_arr[$status_id];

		if ($type == "customer") {
			if ($list_arr[$i]["waiting_answer"] == 1) {
				$img ="<img src=\"".getPathImage()."standard/wait_alarm.png\" alt=\"".$lang->def("_ALT_WAITING_ANSWER")."_".$id."\" ";
				$img.="title=\"".$lang->def("_WAITING_ANSWER")."\" />";
				$rowcnt[]=$img;
			}
			else {
				$rowcnt[]="&nbsp;";
			}
		}

		$url=$um->getUrl("op=assignuser&id=".$id);
		$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$lang->def("_ALT_ASSIGNUSERS")."\" ";
		$img.="title=\"".$lang->def("_ALT_ASSIGNUSERS")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

		$url=$um->getUrl("op=edit&id=".$id);
		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";


		$url=$um->getUrl("op=del&id=".$id);
		$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
		$img.="title=\"".$lang->def("_DEL")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";


		$details ='<div class="task_details">';
		$details.='<div class="details_line">'."<p>".$lang->def("_START_DATE").":</p> ";
		$details.=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["start_date"], "date")."</div>";
		$details.='<div class="details_line">'."<p>".$lang->def("_ACTUAL_END_DATE").":</p> ";
		$details.=$GLOBALS["regset"]->databaseToRegional($list_arr[$i]["actual_end_date"], "date")."</div>";
		$details.='<div class="details_line">'."<p>".$lang->def("_WAITING_ANSWER").":</p> ";
		if ($list_arr[$i]["waiting_answer"] == 1) {
			$details.=$lang->def("_YES").": ".nl2br($list_arr[$i]["waiting_answer_notes"])."</div>";
		}
		else {
			$details.=$lang->def("_NO")."</div>";
		}
		$details.='<div class="details_line">'."<p>".$lang->def("_NOTES").":</p> ";
		$details.=$list_arr[$i]["notes"]."</div>";
		$details.="&nbsp;</div>\n"; // task_details

		$status_id =$list_arr[$i]["status_id"];
		$style =$status_color_arr[$status_id];

		$tab->addBody($rowcnt, $style);
		$tab->setJoinNextRow();
		$tab->addBodyExpanded($details, "task_details", 'id="details_'.$jsid.'" style="visibility: collapse;"');
	}

	// TODO:
	// elenco di progetti che non han assegnato nessun task con bottone modifica / elimina
	// (in fondo a lista progetti / attività)
	// (+implementare funzione elimina)

	$url=$um->getUrl("op=add");
	$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".
	                   $lang->def('_ADD')."</a>\n");

	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	return $res;
}


function getTaskType() {
	$task_type ="";
	$valid_task_type =array("internal", "customer");
	if (isset($_GET["type"])) {
		$task_type =$_GET["type"];
	}
	if (!in_array($task_type, $valid_task_type)) {
		$task_type =FALSE;
	}

	return $task_type;
}


function getAddEditForm($id=0) {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$um =& UrlManager::getInstance();
	$tm =new TaskmanManager();
	$lang=& DoceboLanguage::createInstance("taskman", "crm");

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form=new Form();

	addScriptaculousJs();
	addJs($GLOBALS["where_crm_relative"]."/modules/taskman/", "taskman.js");

	$task_type =getTaskType();
	if ($task_type === FALSE) die();


	$form_code="";
	$form_extra="";
	$url=$um->getUrl("op=save");

	if ($id == 0) {
		$form_code=$form->openForm("main_form", $url);
		$submit_lbl=$lang->def("_INSERT");
		$from_action ="add";

		$customer_id =0;
		$customerinstall_id =0;
		$project_id =0;
		$description="";
		$start_date=$GLOBALS["regset"]->databaseToRegional(date("Y-m-d H:i:s"));
		$scheduled_end_date="";
		$actual_end_date="";
		$priority_id =FALSE;
		$status_id =FALSE;
		$waiting_answer =FALSE;
		$waiting_answer_notes ="";
		$notes="";
	}
	else if ($id > 0) {
		$stored =$tm->getTaskInfo($id);
		$form_code=$form->openForm("main_form", $url);


		$submit_lbl=$lang->def("_EDIT");
		$from_action ="edit";

		$form_extra.=$form->getHidden("edit", "edit", 1);
		$customer_id =$stored["customer_id"];
		$customerinstall_id =$stored["customerinstall_id"];
		$project_id =$stored["project_id"];
		$description =$stored["description"];
		$start_date =$GLOBALS["regset"]->databaseToRegional($stored["start_date"]);
		$scheduled_end_date =$GLOBALS["regset"]->databaseToRegional($stored["scheduled_end_date"]);
		$actual_end_date =$GLOBALS["regset"]->databaseToRegional($stored["actual_end_date"]);
		$priority_id =$stored["priority_id"];
		$status_id =$stored["status_id"];
		$waiting_answer =$stored["waiting_answer"];
		$waiting_answer_notes =$stored["waiting_answer_notes"];
		$notes =$stored["notes"];
	}


	$res.=$form_code.$form->openElementSpace();

	$res.=$form->getHidden("id", "id", $id);
	$res.=$form->getHidden("task_type", "task_type", $task_type);
	$res.=$form->getHidden("from_action", "from_action", $from_action);

	if ($task_type == "internal") {
		$project_arr =$tm->getProjectArray();
		$disabled =($project_id > 0 ? 'disabled="disabled"' : '');
		$js ='onchange="javascript: prjDropdownChange(this.value);"';
		$res.=$form->getDropdown($lang->def("_PROJECT"), "project_id", "project_id", $project_arr, $project_id, '', '', $js);
		$res.=$form->getTextfield($lang->def("_NEW_PROJECT"), "project", "project", 255, '', $disabled);
	}
	else if ($task_type == "customer") {

		addAjaxJS();
		$GLOBALS['page']->add('<script type="text/javascript">'
			.' setup_taskman(\''.$GLOBALS['where_crm_relative'].'/modules/taskman/ajax.taskman.php\'); '
			.'</script>', 'page_head');

		require_once($GLOBALS["where_crm"]."/modules/abook/lib.abook.php");
		$abm =new AddressBookManager();
		$company_arr=$abm->getCompanyArray(FALSE);
		$js ='onchange="javascript: refresh_dropdown(this.value);"';
		$res.=$form->getDropdown($lang->def("_COMPANY"), "customer_id", "customer_id", $company_arr, $customer_id, '', '', $js);

		$customerinstall_arr =array();
		if ($customer_id > 0) {
			$customerinstall_arr =$tm->getCustomerInstallArr($customer_id);
		}
		$res.=$form->getDropdown($lang->def("_CUSTOMER_INSTALL"), "customerinstall_id", "customerinstall_id", $customerinstall_arr, $customerinstall_id);
	}
	$res.=$form->getTextfield($lang->def("_DESCRIPTION"), "description", "description", 255, $description);

	$res.=$form->getDatefield($lang->def("_START_DATE").":", "start_date", "start_date", $start_date, false, true);
	$res.=$form->getDatefield($lang->def("_SCHEDULED_END_DATE").":", "scheduled_end_date", "scheduled_end_date", $scheduled_end_date, false, true);
	$res.=$form->getDatefield($lang->def("_ACTUAL_END_DATE").":", "actual_end_date", "actual_end_date", $actual_end_date, false, true);

	$priority_arr =$tm->getPriorityArray();
	$res.=$form->getDropdown($lang->def("_PRIORITY"), "priority_id", "priority_id", $priority_arr, $priority_id);
	$status_arr =$tm->getStatusArray();
	$res.=$form->getDropdown($lang->def("_STATUS"), "status_id", "status_id", $status_arr, $status_id);

	$res.=$form->getCheckbox($lang->def("_WAITING_ANSWER"), "waiting_answer", "waiting_answer", 1, $waiting_answer);
	$res.=$form->getSimpleTextarea($lang->def("_WAITING_ANSWER_NOTES"), "waiting_answer_notes", "waiting_answer_notes", $waiting_answer_notes);

	$res.=$form->getTextarea($lang->def("_NOTES"), "notes", "notes", $notes);


	$res.=$form_extra;


	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $submit_lbl);
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	$res.="</div>\n";
	$out->add($res);
}


function taskmanSave() {

	$um =& UrlManager::getInstance();
	$tm =new TaskmanManager();

	$id =$tm->saveData($_POST);

	if ($_POST["from_action"] == "add")
		$url =$um->getUrl("op=assignuser&type=".$_POST["task_type"]."&id=".$id);
	else
		$url =$um->getUrl();
	jumpTo($url);
}




function getAddEditProjectForm($id=0) {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$um =& UrlManager::getInstance();
	$tm =new TaskmanManager();
	$lang=& DoceboLanguage::createInstance("taskman", "crm");

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form=new Form();

	$form_code="";
	$url=$um->getUrl("op=saveprj");

	if ($id == 0) {
		$form_code=$form->openForm("main_form", $url);
		$submit_lbl=$lang->def("_INSERT");
		$from_action ="add";

		$customer_id =0;
		$project ="";
	}
	else if ($id > 0) {
		$stored =$tm->getTaskProjectInfo($id);
		$form_code=$form->openForm("main_form", $url);

		$submit_lbl=$lang->def("_EDIT");
		$from_action ="edit";

		$project =$stored["prj_label"];
	}


	$res.=$form_code.$form->openElementSpace();

	$res.=$form->getHidden("id", "id", $id);

	$res.=$form->getTextfield($lang->def("_PROJECT"), "project", "project", 255, $project);

	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $submit_lbl);
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	$res.="</div>\n";
	$out->add($res);
}


function taskmanSaveProject() {

	$um =& UrlManager::getInstance();
	$tm =new TaskmanManager();

	$id =$tm->saveProject($_POST);

	$url =$um->getUrl();
	jumpTo($url);
}


function taskmanAssignUsers() {
	require_once($GLOBALS["where_crm"]."/admin/modules/crmtaskuser/lib.crmtaskuser.php");
	require_once($GLOBALS['where_framework']."/class.module/class.directory.php");

	if ((isset($_GET["id"])) && ($_GET["id"] > 0)) {
		$id =$_GET["id"];
	}
	else {
		return FALSE;
	}
	$task_type =getTaskType();
	if ($task_type === FALSE) return FALSE;


	$mdir=new Module_Directory();
	$tm =new TaskmanManager();
	$um =& UrlManager::getInstance();

	$back_url =$um->getUrl();


	$stored =$tm->getTaskInfo($id);
	$assigned_to =$tm->getAssignedToIdstArr($stored["assigned_to"]);


	if( isset($_POST['okselector']) ) {

		$arr_selection=$mdir->getSelection($_POST);
		$arr_unselected=$mdir->getUnselected();

		$new_assigned_arr =array_diff($assigned_to, $arr_unselected);
		$new_assigned_arr =$assigned_to+$arr_selection;
		$new_assigned_arr =$arr_selection;

		$tm->updateAssignedTo($id, $new_assigned_arr, $assigned_to);

		jumpTo($back_url);
	} elseif( isset($_POST['cancelselector']) ) {
		jumpTo($back_url);
	} else {

		$lang=& DoceboLanguage::createInstance("crmuser", "crm");

		$url=$um->getUrl("op=assignuser&type=".$task_type."&id=".$id);
		$mdir->show_user_selector = TRUE;
		$mdir->show_group_selector = FALSE;
		$mdir->show_orgchart_selector = FALSE;

		if( !isset($_GET['stayon']) ) {
			$mdir->resetSelection($assigned_to);
		}

		// Exclude anonymous user!
		$acl_man =$GLOBALS["current_user"]->getAclManager();
		//$mdir->setUserFilter('exclude', array($acl_man->getAnonymousId()));
		$ctum=new CrmTaskUserManager();
		$taskman_users =$ctum->getUsersIdstArr();
		$mdir->setUserFilter('user', $taskman_users);

		$mdir->loadSelector($url,
			$lang->def( '_TASK_USERS' ), "", TRUE);

	}

}



function getTaskmanOrder() {

	$field=(isset($_SESSION["taskman_order"]["field"]) ? $_SESSION["taskman_order"]["field"] : "scheduled_end_date");
	$type=(isset($_SESSION["taskman_order"]["type"]) ? $_SESSION["taskman_order"]["type"] : "ASC");

	$res=array();
	$res["field"]=$field;
	$res["type"]=$type;

	return $res;
}


function setTaskmanOrder() {

	$um=& UrlManager::getInstance();
	$back_url =$um->getUrl();

	if ((isset($_GET["ord"])) && (!empty($_GET["ord"]))) {
		$ord =$_GET["ord"];
	}
	else {
		jumpTo($back_url);
		return TRUE;
	}

	switch ($ord) {
		case "project": {
			$field="t1.project";
			$default_type="ASC";
		} break;
		case "customer": {
			$field="company_name";
			$default_type="ASC";
		} break;
		case "description": {
			$field="t1.description";
			$default_type="ASC";
		} break;
		case "assignedto": {
			$field="t1.assigned_to";
			$default_type="ASC";
		} break;
		case "schedend": {
			$field="t1.scheduled_end_date";
			$default_type="ASC";
		} break;
		case "timediff": {
			$field="t1.scheduled_diff";
			$default_type="ASC";
		} break;
		case "priority": {
			$field="t1.priority_id";
			$default_type="ASC";
		} break;
		case "status_id": {
			$field="t1.status_id";
			$default_type="ASC";
		} break;
	}

	if ((isset($_SESSION["taskman_order"]["field"])) &&
			($field == $_SESSION["taskman_order"]["field"])) {

		if ($_SESSION["taskman_order"]["type"] == "ASC")
			$_SESSION["taskman_order"]["type"]="DESC";
		else
			$_SESSION["taskman_order"]["type"]="ASC";
	}
	else {
		$_SESSION["taskman_order"]["field"]=$field;
		$_SESSION["taskman_order"]["type"]=$default_type;
	}

	jumpTo($back_url);
}


function taskmanDel() {
	if ((isset($_GET["id"])) && ((int)$_GET["id"] > 0)) {
		$task_id=$_GET["id"];
	}
	else {
		return FALSE;
	}

	$um=& UrlManager::getInstance();

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang=& DoceboLanguage::createInstance("company", "framework");

	if (isset($_POST["canc_del"])) {
		jumpTo($um->getUrl());
	}
	else if (isset($_POST["conf_del"])) {

		$tm =new TaskmanManager();
		$tm->deleteTask($task_id);

		jumpTo($um->getUrl());
	}
	else {

		$res="";
		$tm =new TaskmanManager();

		$stored=$tm->getTaskInfo($task_id);
		$name=$stored["description"];

		$back_ui_url=$um->getUrl();
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_TASKMAN");
		$title_arr[]=$lang->def("_DELETE_TASK").": ".$name;
		$out->add(getCmsTitleArea($title_arr, "form"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$res.=$form->openForm("del_form", $um->getUrl("op=del&id=".$task_id));


		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_DESCRIPTION').' :</span> '.$name.'<br />',
			false,
			'conf_del',
			'canc_del');

		$res.=$form->closeForm();
		$res.="</div>\n";

		$out->add($res);
	}
}



function printTaskmanSearchForm() {
	$res="";

	require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
	$form=new Form();

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("taskman");

	$um =& UrlManager::getInstance();
	$tm =new TaskmanManager();
	$lang =& DoceboLanguage::createInstance("taskman", "crm");

	$hide_finished ="on";
	if (isset($_POST["do_search"])) {
		$search->setSearchItem("assigned_to_me");
		$search->setSearchItem("priority_id");
		$search->setSearchItem("status_id");
		$search->setSearchItem("scheduled_end_date");
		$hide_finished =(isset($_POST["hide_finished"]) ? "on" : "off");
		$search->setSearchItem("hide_finished", $hide_finished);
	}

/*
	if ($search->getShowSearchForm()) {
		$hide_form=FALSE;
		$label=$search->lang->def("_HIDE_SEARCH_FORM");
		$class="hide_form";
	}
	else {
		$hide_form=TRUE;
		$label=$search->lang->def("_SHOW_SEARCH_FORM");
		$class="show_form";
	}
*/

	$class="";
	$label =$search->lang->def("_SHOW_HIDE_SEARCH_FORM");

	$url ="#"; //$um->getUrl("op=showhidesearchform");
	$js ='onclick="javascript: $(\'search_form_box\').toggle(); return false;"';
	$res.="<div class=\"search_form ".$class."\">";
	$res.="<a href=\"".$url."\" ".$js.">".$label."</a></div>\n";

/*
	if ($hide_form) {
		return $res;
	} */


	$res.="<div id=\"search_form_box\">";
	$res.=$search->openSearchForm($form, $um->getUrl());
	// --------------------------------------------------------------------------


	$assigned_to_me =$search->getSearchItem("assigned_to_me", "bool");
	$priority_id =$search->getSearchItem("priority_id", "int");
	$status_id =$search->getSearchItem("status_id", "int");
	$scheduled_end_date =$search->getSearchItem("scheduled_end_date", "string");
	$hide_finished =$search->getSearchItem("hide_finished", "string", $hide_finished);

	// $res.=$form->getTextfield($search->lang->def("_SEARCH_KEY"), "search_key", "search_key", 255, $search_key);

	$priority_arr =$tm->getPriorityArray(TRUE);
	$res.=$form->getDropdown($lang->def("_PRIORITY"), "priority_id", "priority_id", $priority_arr, $priority_id);

	$status_arr =$tm->getStatusArray(TRUE);
	$res.=$form->getDropdown($lang->def("_STATUS"), "status_id", "status_id", $status_arr, $status_id);

	$res.=$form->getDatefield($lang->def("_SCHEDULED_END_DATE").":", "scheduled_end_date", "scheduled_end_date", $scheduled_end_date, false, true);

	$res.=$form->getCheckbox($lang->def("_ASSIGNED_TO_ME"), "assigned_to_me", "assigned_to_me", 1, $assigned_to_me);

	$chk_hide_finished =($hide_finished == "on" ? TRUE : FALSE);
	$res.=$form->getCheckbox($lang->def("_HIDE_FINISHED"), "hide_finished", "hide_finished", "on", $chk_hide_finished);

	// --------------------------------------------------------------------------
	$res.=$search->closeSearchForm($form);
	$res.="</div>\n";

	return $res;
}


function getTaskmanSearchQuery() {
	$res=FALSE;
	$first=TRUE;

	require_once($GLOBALS["where_crm"]."/lib/lib.search.php");
	$search=new SearchUI("taskman");

	$assigned_to_me =$search->getSearchItem("assigned_to_me", "bool");
	$priority_id =$search->getSearchItem("priority_id", "int");
	$status_id =$search->getSearchItem("status_id", "int");
	$scheduled_end_date =$search->getSearchItem("scheduled_end_date", "string");
	$hide_finished =$search->getSearchItem("hide_finished", "string", "on");

	if ($assigned_to_me > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.assigned_to LIKE '%,".getLogUserId().",%'";
		$first=FALSE;
	}

	if ($priority_id > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.priority_id='".$priority_id."'";
		$first=FALSE;
	}


	if ($status_id > 0) {
		$res.=($first ? "" : " AND ");
		$res.="t1.status_id='".$status_id."'";
		$first=FALSE;
	}


	if (!empty($scheduled_end_date)) {
		$scheduled_end_date =$GLOBALS["regset"]->regionalToDatabase($scheduled_end_date, "date");
		$res.=($first ? "" : " AND ");
		$res.="DATE(t1.scheduled_end_date)=DATE('".$scheduled_end_date."')";
		$first=FALSE;
	}


	if ($hide_finished == "on") {
		$res.=($first ? "" : " AND ");
		$res.="t1.status_id != '"._STATUS_FINISHED."'";
		$first=FALSE;
	}


	return $res;
}



// ----------------------------------------------------------------------------

$op=(isset($_GET["op"]) ? $_GET["op"] : "main");

if (isset($_GET["set_wp_search"])) {
	require_once($GLOBALS["where_framework"]."/lib/lib.search.php");
	$search=new SearchUI("taskman");
	$search->clearSearchFilter();
	if ((isset($_GET["assigned_to_me"])) && ($_GET["assigned_to_me"] == 1)) {
		$search->setSearchItem("assigned_to_me", 1);
	}
	else {
		$search->setSearchItem("assigned_to_me", 0);
	}
	if (!empty($_GET["set_wp_search"])) {
		$search->setSearchItem($_GET["set_wp_search"], $_GET["search"]);
	}
}


switch ($op) {

	case "main": {
		taskman();
	} break;

	case "add": {
		getAddEditForm();
	} break;

	case "edit": {
		getAddEditForm((int)$_GET["id"]);
	} break;

	case "save": {
		if (isset($_POST["undo"])) {
			taskman();
		}
		else {
			taskmanSave();
		}
	} break;

	case "assignuser": {
		taskmanAssignUsers();
	} break;

	case "setorder": {
		setTaskmanOrder();
	} break;

	case "del": {
		taskmanDel();
	} break;

	case "editprj": {
		getAddEditProjectForm((int)$_GET["id"]);
	} break;

	case "saveprj": {
		if (isset($_POST["undo"])) {
			taskman();
		}
		else {
			taskmanSaveProject();
		}
	} break;


}


?>
