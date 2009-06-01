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
 * @version  $Id: companystatus.php 173 2006-03-27 09:49:07Z giovanni $
 */
// ----------------------------------------------------------------------------

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');
if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

require_once($GLOBALS["where_framework"]."/lib/lib.company.php");

// -- Url Manager Setup --
require_once($GLOBALS["where_framework"]."/lib/lib.urlmanager.php");
$um=& UrlManager::getInstance();

$um->setStdQuery("modname=company&op=main");
// -----------------------


define("_COMPANY_USER_PREF_FILTER", 'ui.company.filters.current');


function setupCompanyJs() {

	addScriptaculousJs();
	addJs($GLOBALS['where_crm_relative'].'/admin/modules/company/', 'ajax.company.js');

	$GLOBALS['page']->add('<script type="text/javascript">'
		.' setup_company(\''.$GLOBALS['where_crm_relative'].'/admin/modules/company/ajax.company.php\'); '
		.'</script>', 'page_head');
}


function companyMain() {
	$res ="";

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");


	setupCompanyJs();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	$cm=new CompanyManager();
	$fl = new FieldList();
	$fl->setFieldEntryTable($cm->ccManager->getCompanyFieldEntryTable());

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_COMPANY_TAB_CAPTION");
	$table_summary=$lang->def("_COMPANY_TAB_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];

	$acl_manager=$GLOBALS["current_user"]->getAclManager();
	$user_idst=$GLOBALS["current_user"]->getIdST();
	$roles=$acl_manager->getUserRoleFromPath($user_idst, "/crm/company", "view");

	$back_ui_url="index.php";

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY");
	$res.=getTitleArea($title_arr, "company");
	$res.="<div id=\"company_content\" class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$ecom_type=getPLSetting("ecom", "ecom_type", "none");
	$with_buyer=($ecom_type == "with_buyer" ? TRUE : FALSE);

	$filter_form =getCompanyFilterForm();
	$res.=$filter_form["out"];


	// array with the field_id of the fields used as extra columns
	$extra_col =array(1);


	$tab=new typeOne($vis_item, $table_caption, $table_summary);


	$head=array($lang->def("_COMPANY_NAME"));

	$head[]=$lang->def("_COMPANY_CODE");

	$head[]=$lang->def("_COMPANY_TYPE");

		$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$lang->def("_ALT_COMPANYUSERS")."\" ";
		$img.="title=\"".$lang->def("_ALT_COMPANYUSERS")."\" />";
		$head[]=$img;

	if ($with_buyer) {
		$img ="<img src=\"".getPathImage('fw')."standard/buyer.png\" alt=\"".$lang->def("_ALT_ASSIGNBUYERS")."\" ";
		$img.="title=\"".$lang->def("_ALT_ASSIGNBUYERS")."\" />";
		$head[]=$img;
	}

	$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;

	$head_type=array("", "", "", "image", "image", "image");

	if ($with_buyer) {
		$head_type[]="image";
	}


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$tab->initNavBar('ini', 'link');
	$tab->setLink($um->getUrl());

	$ini 	= $tab->getSelectedElement();
	$ccm 	= new CoreCompanyManager();

	$where 		= FALSE;
	$first 		= TRUE;
	$where_in 	= FALSE;
	if ((is_array($filter_form["filters"])) && (!empty($filter_form["filters"]))) {

		foreach($filter_form["filters"] as $key=>$field_info) {

			if (!empty($field_info["value"])) {

				if (isset($field_info["fieldname"])) { // nat_field

					$where .= (!$first ? "AND " : "");
					$where .= $field_info["fieldname"]." LIKE '%".$field_info["value"]."%' ";
					$first = FALSE;
				} else { // custom_field

					$owner_data = $fl->getOwnerData($field_info[FIELD_INFO_ID], $field_info["value"]);
					if ($where_in === FALSE) $where_in = $owner_data;
					else $where_in = array_intersect($owner_data, $where_in);
				}
			} // end if value
		}
		if ($where_in !== FALSE) {
			$where.=(!$first ? "AND " : "");
			if((is_array($where_in)) && (!empty($where_in))) {
				$where.="company_id IN (".implode(",", $where_in).")";
			} else {
				$where.='0';
			}
		}
	}

	$list=$ccm->getCompanyList($ini, $vis_item, FALSE, $where, TRUE);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	if($db_tot == 0) {
		$res .= getResultUi($lang->def('_NO_MATCH'));
	}

	$custom_col_data =$fl->showFieldForUserArr($list["company_id_arr"], $extra_col);

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["company_id"];

		$rowcnt=array();
		$rowcnt[]=$list_arr[$i]["name"];

		$rowcnt[]=$list_arr[$i]["code"];

		$rowcnt[]=$list_arr[$i]["type_label"];

/*
		foreach($extra_col as $field_id) {
			$rowcnt[]=$custom_col_data[$id][$field_id];
		}
*/
		$url="index.php?modname=company&amp;op=company_users&amp;id=".$id;
		$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$lang->def("_ALT_COMPANYUSERS")."\" ";
		$img.="title=\"".$lang->def("_ALT_COMPANYUSERS")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

		if ($with_buyer) {
			$url="index.php?modname=company&amp;op=assignbuyer&amp;id=".$id;
			$img ="<img src=\"".getPathImage('fw')."standard/buyer.png\" alt=\"".$lang->def("_ALT_ASSIGNBUYERS")."\" ";
			$img.="title=\"".$lang->def("_ALT_ASSIGNBUYERS")."\" />";
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
		}


		$url="index.php?modname=company&amp;op=edit&amp;id=".$id;
		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";


		if (!$list_arr[$i]["is_used"]) {
			$url=$um->getUrl("op=del&id=".$id."&conf_del=1");
			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
			$img.="title=\"".$lang->def("_DEL")."\" />";
			$rowcnt[]="<a href=\"".$url."\" title=\"".$lang->def("_DEL")." : ".$list_arr[$i]["name"]."\">".$img."</a>\n";
		}
		else
			$rowcnt[]="&nbsp;";

		$tab->addBody($rowcnt);
	}


	$url=$um->getUrl("op=add");
	$add_box ="<a class=\"new_element_link_float\" href=\"".$url."\">".$lang->def('_ADD')."</a>\n";

	$add_box.="&nbsp;"; // TODO: remove this line and change assign fields image with css background
	$url=$um->getUrl("op=assignfields");
	$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$lang->def("_ALT_ASSIGNFIELDS")."\" ";
	$img.="title=\"".$lang->def("_ALT_ASSIGNFIELDS")."\" />";
	$add_box.="<a href=\"".$url."\">".$img.$lang->def('_ALT_ASSIGNFIELDS')."</a>\n";

	$add_box.="&nbsp;"; // TODO: remove this line and change assign fields image with css background
	$url=$um->getUrl("op=importcompany");
	$img ="<img src=\"".getPathImage('fw')."standard/import.gif\" alt=\"".$lang->def("_ALT_IMPORTCOMPANY")."\" ";
	$img.="title=\"".$lang->def("_ALT_IMPORTCOMPANY")."\" />";
	$add_box.="<a href=\"".$url."\">".$img.$lang->def('_ALT_IMPORTCOMPANY')."</a>\n";

	$tab->addActionAdd($add_box);

	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	//add confirm popup
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=conf_del]');


	// setup the config -------------------------------------------------------
	// $GLOBALS['framework']['company_idref_code']

	if(isset($_POST['save_config'])) {
		$ccm->setIdrefCode($_POST['idref_config_drop']);
	}


	$res .= '<div class="company_idref_code">'
		.$lang->def('_IDREF_CODE_FIELD')
		.'<a id="mod_conf_link" onclick="Effect.toggle(\'mod_config_container\', \'blind\'); return false;" href="javascript:void(0)">';
	$field_assigned = $ccm->getFieldInfoAssignedToCompany();
	switch($ccm->getIdrefCode()) {
		case "code" 		: {
			$res.= ''.$lang->def('_IDREF_DEFAULT_CODE');
		};break;
		case "vat_number" 	: {
			$res.= ''.$lang->def('_IDREF_VAT_NUMBER');
		};break;
		default : {
			$id_field = $ccm->getIdrefCode();
			$res.= ''.str_replace('[field_name]', $field_assigned[$id_field][2], $lang->def('_IDREF_EXTRA_FIELD'));
		}
	}
	$res .= '</a>';

	$field_name = array();
	$field_name['code'] 		= $lang->def('_IDREF_DEFAULT_CODE');
	$field_name['vat_number'] 	= $lang->def('_IDREF_VAT_NUMBER');

	foreach($field_assigned as $k => $value) { $field_name[$k] = $value[2]; }

	$res .= '<div id="mod_config_container">'
			.Form::openForm('form_mod_config', 'javascript:void(0)', false, false, '',
				'onsubmit="save_new_conf(); return false;"')

			.Form::getDropdown($lang->def('_IDREF_DIFFERENT'), 'idref_config_drop', 'idref_config_drop', $field_name, $ccm->getIdrefCode(),
				' '.Form::getButton('save_config', 'save_config', $lang->def('_SAVE'), 'button_nowh'))
			.Form::closeForm()
		.'</div>'
		.'<script type="text/javascript"> $(\'mod_config_container\').style.display = \'none\'; </script>'
		.'</div>';

	// ------------------------------------------------------------------------

	$res.="</div>\n";
	$out->add($res);
}


function addeditCompany($id=0) {
	$res=""; // TODO: check perm

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form=new Form();
	$res="";

	$cm=new CompanyManager();

	if ($id == 0) {
		$stored=array();
		$page_title=$lang->def("_ADD_ITEM");
		$back_ui_url=$um->getUrl();
	}
	else if ($id > 0) {
		$stored=$cm->getCompanyInfo($id);
		$name=$stored["name"];
		$page_title=$lang->def("_EDIT_ITEM").": ".$name;
		$back_ui_url=$um->getUrl();
	}

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY");
	$title_arr[]=$page_title;
	$res.=getTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$cca=new CoreCompanyAdmin();
	$res.=$cca->getAddEditForm($id, $cm, $stored);

	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}


function saveCompany() {
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	// TODO: check perm

	$um=& UrlManager::getInstance();
	$cm=new CompanyManager();
	$cm->saveData($_POST);

	jumpTo($um->getUrl());
}


function deleteCompany() {

	if ((isset($_GET["id"])) && ((int)$_GET["id"] > 0)) {
		$company_id=$_GET["id"];
	}
	else {
		return FALSE;
	}

	//$um=& UrlManager::getInstance();
	$um=& UrlManager::getInstance();

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance("company", "framework");

	if (isset($_POST["canc_del"])) {
		jumpTo($um->getUrl());
	}
	else if (get_req("conf_del", DOTY_INT, false)) {

		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");

		$ccm=new CoreCompanyManager();
		$ccm->deleteCompany($company_id);

		jumpTo($um->getUrl());
	}
	else {

		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

		$res="";
		$cm=new CompanyManager();

		$stored=$cm->getCompanyInfo($company_id);
		$name=$stored["name"];

		$back_ui_url=$um->getUrl();
		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_COMPANY");
		$title_arr[]=$lang->def("_DELETE_COMPANY").": ".$name;
		$out->add(getTitleArea($title_arr, "form"));
		$out->add("<div class=\"std_block\">\n");
		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$form=new Form();

		$res.=$form->openForm("del_form", $um->getUrl("op=del&id=".$company_id));


		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_TITLE').' :</span> '.$name.'<br />',
			false,
			'conf_del',
			'canc_del');

		$res.=$form->closeForm();
		$res.="</div>\n";

		$out->add($res);
	}
}


function loadAssignFields() {
	$res="";

	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$form=new Form();

	$um=& UrlManager::getInstance();
	$cm=new CompanyManager();

	$fl = new FieldList();
	$fl->setGroupFieldsTable($cm->ccManager->getCompanyFieldTable());

	$idst=$cm->ccManager->getCompanyFieldsGroupIdst();

	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY");
	$title_arr[]=$lang->def("_ASSIGN_FIELDS");
	$res.=getTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$url=$um->getUrl("op=saveassigned");
	$res.=$form->openForm("main_form", $url);
	$res.=$form->openElementSpace();

	$arr_all_fields=$fl->getAllFields(array("crm"));
	$sel_fields=$fl->getFieldsFromIdst(array($idst));
	$sel_fields_list=array_keys($sel_fields);

	// print_r($arr_all_fields);
	// print_r($sel_fields);


	$tab=new TypeOne(0, $lang->def('_FIELD_LIST_CAPTION'), $lang->def('_FIELD_LIST_SUMMARY'));
	$tab->setTableStyle('tree_org_table_field');
	$tab->addHeadCustom('<tr class="first_intest">'
		.'<th scope="col">'
		.$lang->def('_FIELD_NAME').'</th>'
		.'<th scope="col">'
		.$lang->def('_FIELD_MANDATORY').'</th>'
		.'<th scope="col">'
		.$lang->def('_FIELD_USERACCESS').'</th>'
		.'</tr>');


	foreach ($arr_all_fields as $field_id=>$field_info) {

		$checked=in_array($field_id, $sel_fields_list);
		$field_lbl="<b>".$field_info[FIELD_INFO_TRANSLATION]."</b>";
		$field_check=$form->getCheckbox($field_lbl, "fields_".$field_id, "fields[".$field_id."]", $field_id, $checked);

		$checked=FALSE;
		if ((isset($sel_fields[$field_id][FIELD_INFO_MANDATORY])) &&
		    ($sel_fields[$field_id][FIELD_INFO_MANDATORY] == 'true'))
			$checked=TRUE;
		$field_lbl=$lang->def('_FIELD_MANDATORY');
		$manadatory_check=
			$form->getCheckbox($field_lbl, "mandatory_".$field_id, "mandatory[".$field_id."]", $field_id, $checked);

		$checked=FALSE;
		if ((!isset($sel_fields[$field_id][FIELD_INFO_USERACCESS]))) // Checked by default
			$checked=TRUE;
		else if ((isset($sel_fields[$field_id][FIELD_INFO_USERACCESS])) &&
		    ($sel_fields[$field_id][FIELD_INFO_USERACCESS] == 'readwrite'))
			$checked=TRUE;
		$field_lbl=$lang->def('_FIELD_USERACCESS');
		$useraccess_check=
			$form->getCheckbox($field_lbl, "useraccess_".$field_id, "useraccess[".$field_id."]", $field_id, $checked);

		$tab->addBodyCustom('<tr>'
			.'<th scope="row">'.$field_check.'</th>'
			.'<td>'.$manadatory_check.'</td>'
			.'<td>'.$useraccess_check.'</td>'
			.'</tr>');

	}

	$res.=$tab->getTable();


	foreach($sel_fields_list as $field_id) {
		$res.=$form->getHidden("field_in_db_".$field_id, "field_in_db[".$field_id."]", $field_id);
	}


	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $lang->def("_SAVE"));
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}


function saveAssignedFields() {

	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

	$ccm=new CoreCompanyManager();

	$fl = new FieldList();
	$fl->setGroupFieldsTable($ccm->getCompanyFieldTable());

	$um=& UrlManager::getInstance();

	$idst=$ccm->getCompanyFieldsGroupIdst();

	$fields=$_POST["fields"];
	$mandatory_arr=(isset($_POST["mandatory"]) ? $_POST["mandatory"] : array());
	$useraccess_arr=(isset($_POST["useraccess"]) ? $_POST["useraccess"] : array());
	$field_in_db=(isset($_POST["field_in_db"]) ? $_POST["field_in_db"] : array());

	foreach($field_in_db as $field_id) {
		$fl->removeFieldFromGroup($field_id, $idst);
	}

	foreach($fields as $field_id) {

		$mandatory=(in_array($field_id, $mandatory_arr) ? "true" : "false");
		$useraccess=(in_array($field_id, $useraccess_arr) ? "readwrite" : "readonly");

		$fl->addFieldToGroup($field_id, $idst, $mandatory, $useraccess);
	}

	jumpTo($um->getUrl());
}

function companyAssignBuyer() {
	require_once($GLOBALS['where_framework']."/class.module/class.directory.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
	$mdir=new Module_Directory();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();
	$ccm=new CoreCompanyManager();

	$company_id=(int)importVar("id", true);

	$back_url=$um->getUrl("op=main");

	if( isset($_POST['okselector']) ) {

		// TODO: check (edit) perm

		$start_sel=$mdir->getStartSelection();
		$arr_selection=array_diff($mdir->getSelection($_POST), $start_sel);
		$arr_deselected = $mdir->getUnselected();

		$ccm->saveCompanyPerm($company_id, $arr_selection, $arr_deselected);

		jumpTo($back_url);
	}
	else if( isset($_POST['cancelselector']) ) {
		jumpTo($back_url);
	}
	else {

		$mdir->setNFields(1);
		$mdir->show_group_selector=true;
		$mdir->show_orgchart_selector=false;

		if( !isset($_GET['stayon']) ) {
			$perm=$ccm->loadCompanyPerm($company_id);
			if (isset($perm["buyer"])) {
				$mdir->resetSelection(array_keys($perm["buyer"]));
			}
		}


		$regusers_idst=$mdir->aclManager->getGroupRegisteredId();
		$mdir->setUserFilter("group", array($regusers_idst));


		$back_ui_url=$um->getUrl("op=main");

		$url=$um->getUrl("op=assignbuyer&id=".$company_id."&stayon=1");
		$mdir->loadSelector($url, $lang->def('_COMPANY_ASSIGN_BUYER'), "", TRUE);

		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	}

}


function importCompany() {
	$res="";

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS["where_framework"]."/lib/lib.company.php");


	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$form=new Form();

	$um=& UrlManager::getInstance();
	$ccm=new CoreCompanyManager();



	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY");
	$title_arr[]=$lang->def("_IMPORT_COMPANY");
	$res.=getTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

	$url=$um->getUrl("op=loadcsv");
	$res.=$form->openForm("main_form", $url, "", "", "multipart/form-data");
	$res.=$form->openElementSpace();

	$res.=$form->getFileField($lang->def("_CSV_FILE"), "csv_file", "csv_file");
	$res.=$form->getTextField($lang->def("_CSV_SEPARATOR"), "csv_separator", "csv_separator", 1, ",");
	$res.=$form->getCheckBox($lang->def("_CSV_IMPORT_HEADER"), "csv_import_header", "csv_import_header", 1, TRUE);
	$res.=$form->getTextField($lang->def("_CSV_CHARSET"), "csv_charset", "csv_charset", 255, "ISO-8859-1");

	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $lang->def("_SAVE"));
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}


function loadCompanyCsv() {
	$res="";

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
	require_once($GLOBALS['where_framework'].'/class/class.fieldmap_company.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');

	sl_open_fileoperations();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$form=new Form();

	$um=& UrlManager::getInstance();
	$ccm=new CoreCompanyManager();
	$fmc=new FieldMapCompany();


	$back_ui_url=$um->getUrl();

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY");
	$title_arr[]=$lang->def("_IMPORT_COMPANY");
	$res.=getTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

	$url=$um->getUrl("op=docsvimport");
	$res.=$form->openForm("main_form", $url);
	$res.=$form->openElementSpace();


	$tab_caption=$lang->def("_COMPANY_IMPORT_TAB_CAP");
	$tab_summary=$lang->def("_COMPANY_IMPORT_TAB_SUM");

	$tab=new TypeOne(0, $tab_caption, $tab_summary);

	$file=$_FILES["csv_file"]["name"];
	$file_path="/common/";
	sl_upload($_FILES["csv_file"]["tmp_name"], $file_path.$_FILES["csv_file"]["name"]);

	$handle=fopen($GLOBALS["where_files_relative"].$file_path.$file,"r");

	$csv_separator=$_POST["csv_separator"];
	$data=fgetcsv($handle, 1000, $csv_separator);
	$col_count=count($data);

	$head_type=array_fill(0, $col_count, "");
	$tab->setColsStyle($head_type);


	$field_list=$fmc->getPredefinedFields();
	$field_list=$field_list+$fmc->getCustomFields();

	$head=array();
	for($i=0; $i<$col_count; $i++) {
		$d_id="field_map_".$i;
		$d_name="field_map[".$i."]";
		$current=each($field_list);
		$selected=(isset($field_list[$current["key"]]) ? $current["key"] : FALSE);
		$head[]=$form->getInputDropdown("dropdown_nowh", $d_id, $d_name, $field_list, $selected, "");
	}
	reset($field_list);
	$tab->addHead($head);

	if (isset($_POST["csv_import_header"])) {
		$head=array();
		for($i=0; $i<$col_count; $i++) {
			$head[]=$data[$i];
		}
		$tab->addHead($head);
	}
	else {
		// Reset file pointer
		fseek($handle, 0);
	}

	$sample_line = 0;
	while(( $data=fgetcsv($handle, 1000, $csv_separator)) !== FALSE && ($sample_line < 3)) {

		$row_cont=array();
		for($i=0; $i<$col_count; $i++) {
			$row_cont[]=$data[$i];
		}
		$tab->addBody($row_cont);
		$sample_line++;
	}


	$res.=$tab->getTable();

	$res.=$form->getHidden("csv_file", "csv_file", $file);
	$res.=$form->getHidden("csv_separator", "csv_separator", $csv_separator);
	$csv_import_header=(isset($_POST["csv_import_header"]) ? 0 : 1);
	$res.=$form->getHidden("csv_import_header", "csv_import_header", (int)$csv_import_header);
	$res.=$form->getHidden("csv_charset", "csv_charset", $_POST["csv_charset"]);

	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $lang->def("_NEXT"));
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	fclose($handle);

	sl_close_fileoperations();
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}


function doCsvImport() {

	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	require_once($GLOBALS['where_framework'].'/class/class.fieldmap_company.php');

	$lang=& DoceboLanguage::createInstance("company", "framework");

	$fmc=new FieldMapCompany();

	sl_open_fileoperations(); 	// print_r($_POST);

	$file=$_POST["csv_file"];
	$file_path="/common/";


	$handle=fopen($GLOBALS["where_files_relative"].$file_path.$file,"r");

	$csv_separator=$_POST["csv_separator"];
	$data=fgetcsv($handle, 1000, $csv_separator);
	$col_count=count($data);

	$csv_import_header=((isset($_POST["csv_import_header"])) && ($_POST["csv_import_header"] >0) ? TRUE : FALSE);

	if ($csv_import_header) {
		// Reset file pointer
		fseek($handle, 0);
	}

	$field_map=array_flip($_POST['field_map']);  // print_r($field_map);

	$custom_fields_arr=$fmc->getCustomFields(FALSE);
	$custom_data=array();
	while(($data=fgetcsv($handle, 5000, $csv_separator)) !== FALSE) {

		$value=(isset($field_map["company_predefined_company"]) ? $data[$field_map["company_predefined_company"]] : $lang->def("_NO_NAME"));
		$predefined_data["name"]=$value;
		$value=(isset($field_map["company_predefined_code"]) ? $data[$field_map["company_predefined_code"]] : "");
		$predefined_data["code"]=$value;
		$value=(isset($field_map["company_predefined_ctype"]) ? $data[$field_map["company_predefined_ctype"]] : "");
		$predefined_data["ctype_id"]=$value;
		$value=(isset($field_map["company_predefined_cstatus"]) ? $data[$field_map["company_predefined_cstatus"]] : "");
		$predefined_data["cstatus_id"]=$value;
		$value=(isset($field_map["company_predefined_address"]) ? $data[$field_map["company_predefined_address"]] : "");
		$predefined_data["address"]=$value;
		$value=(isset($field_map["company_predefined_tel"]) ? $data[$field_map["company_predefined_tel"]] : "");
		$predefined_data["tel"]=$value;
		$value=(isset($field_map["company_predefined_email"]) ? $data[$field_map["company_predefined_email"]] : "");
		$predefined_data["email"]=$value;
		$value=(isset($field_map["company_predefined_vat_number"]) ? $data[$field_map["company_predefined_vat_number"]] : "");
		$predefined_data["vat_number"]=$value;

		foreach($custom_fields_arr as $field_id=>$label) {
			if (isset($field_map["company_custom_".$field_id])) {
				$custom_data[$field_id]=$data[$field_map["company_custom_".$field_id]];
			}
		}

		$fmc->saveFields($predefined_data, $custom_data, 0, FALSE, FALSE);
	}
	fclose($handle);

	sl_unlink($file_path.$file);
	sl_close_fileoperations();

	$um=& UrlManager::getInstance();
	jumpTo($um->getUrl());
}


function showCompanyUsers() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

	$cm=new CompanyManager();


	$page_title=$lang->def("_COMPANY_USERS");
	$back_ui_url=$um->getUrl();


	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY");
	$title_arr[]=$page_title;
	$res.=getTitleArea($title_arr, "company");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$company_id=(int)importVar("id", true);


	$table_caption=$lang->def("_COMPANY_USERS_CAPTION");
	$table_summary=$lang->def("_COMPANY_USERS_SUMMARY");


	$tab=new typeOne(0, $table_caption, $table_summary);

	$img ="<img src=\"".getPathImage('fw')."standard/user.gif\" alt=\"".$lang->def("_USER")."\" ";
	$img.="title=\"".$lang->def("_USER")."\" />";


	$head=array($img, $lang->def("_USER"));

	$img ="<img src=\"".getPathImage('fw')."standard/buyer.png\" alt=\"".$lang->def("_ALT_IS_BUYER")."\" ";
	$img.="title=\"".$lang->def("_ALT_IS_BUYER")."\" />";
	$head[]=$img;

	$head_type=array("image", "", "image");

	$tab->setColsStyle($head_type);
	$tab->addHead($head);


	$cm=new CompanyManager();
	$user_arr=$cm->getCompanyUsers($company_id);

	$acl_manager =$GLOBALS["current_user"]->getAclManager();
	$role_id="/crm/company/".$company_id."/buyer";
	$role=$acl_manager->getRole(false, $role_id);
	$buyers =$acl_manager->getRoleMembers($role[ACL_INFO_IDST]);

	foreach($user_arr as $idst=>$userid) {

		$rowcnt=array();

		$img ="<img src=\"".getPathImage('fw')."standard/user.gif\" alt=\"".$lang->def("_USER")." ".$userid."\" ";
		$img.="title=\"".$lang->def("_USER")." ".$userid."\" />";
		$rowcnt[]=$img;

		$show_details=(isset($_SESSION["show_user_details"][$idst]) ? TRUE : FALSE);

		$url_qry ="op=details&id=".$company_id;
		$url_qry.="&op=toggleuserdetails&userid=".$idst;
		$url=$um->getUrl($url_qry);
		if ($show_details) {
			$img ="<img src=\"".getPathImage('fw')."standard/less.gif\" alt=\"".$lang->def("_LESSINFO")." ".$userid."\" ";
			$img.="title=\"".$lang->def("_LESSINFO")." ".$userid."\" />";
		}
		else {
			$img ="<img src=\"".getPathImage('fw')."standard/more.gif\" alt=\"".$lang->def("_MOREINFO")." ".$userid."\" ";
			$img.="title=\"".$lang->def("_MOREINFO")." ".$userid."\" />";
		}
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n"."<a href=\"".$url."\">".$userid."</a>\n";

		$img ="<img src=\"".getPathImage('fw')."standard/buyer.png\" alt=\"".$lang->def("_ALT_IS_BUYER")."\" ";
		$img.="title=\"".$lang->def("_ALT_IS_BUYER")."\" />";
		$rowcnt[]=(in_array($idst, $buyers) ? $img : "&nbsp;");

		$tab->addBody($rowcnt);

		if ($show_details) {
			require_once($GLOBALS["where_framework"].'/lib/lib.user_profile.php');

			$lang =& DoceboLanguage::createInstance('profile', 'lms');

			$profile = new UserProfile($idst);
			$profile->init('profile', 'lms', 'modname=company&op=company_users&id='.$company_id, 'ap');

			$user_info =$profile->getUserInfo( getLogUserId() )
			           .$profile->getUserLmsStat( getLogUserId() );

			$tab->addBodyExpanded($user_info, 'user_more_info');
		}
	}


	$add_txt="";
	$url=$um->getUrl("op=assign_company_users&id=".$company_id);
	$img="<img src=\"".getPathImage('fw')."standard/addandremove.gif\" alt=\"".$lang->def('_SELECT_USERS')."\" />";
	$add_txt.="<a href=\"".$url."\">".$img.$lang->def('_SELECT_USERS')."</a>\n";
	$tab->addActionAdd($add_txt);

	$res.=$tab->getTable();

	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}


function toggleUserDetails() {

	$company_id=(int)importVar("id", true);
	$user_id=(int)importVar("userid", true);

	if (($company_id > 0) && ($user_id > 0)) {
		$close=(isset($_SESSION["show_user_details"][$user_id]) ? TRUE : FALSE);
		if (isset($_SESSION["show_user_details"]))
			unset($_SESSION["show_user_details"]);
		if (!$close) {
			$_SESSION["show_user_details"][$user_id]=TRUE;
		}
	}

	$um=& UrlManager::getInstance();
	jumpTo($um->getUrl("op=company_users&id=".$company_id));
}


function assignCompanyUsers() {

	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	require_once($GLOBALS['where_framework']."/class.module/class.directory.php");
	$mdir=new Module_Directory();

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("company", "framework");
	$um=& UrlManager::getInstance();

	$company_id=(int)importVar("id", true);

	$back_url=$um->getUrl("op=company_users&id=".$company_id);


	if( isset($_POST['okselector']) ) {

		// TODO: check (edit) perm

		$start_sel=$mdir->getStartSelection();
		$arr_selection=array_diff($mdir->getSelection($_POST), $start_sel);
		$arr_deselected = $mdir->getUnselected();
		$cm=new CompanyManager();
		$cm->updateCompanyUsers($company_id, $arr_selection, $arr_deselected);

		jumpTo($back_url);
	}
	else if( isset($_POST['cancelselector']) ) {
		jumpTo($back_url);
	}
	else {

		$mdir->setNFields(2);
		$mdir->show_group_selector=false;
		$mdir->show_orgchart_selector=false;

		if( !isset($_GET['stayon']) ) {
			$cm=new CompanyManager();
			$mdir->resetSelection(array_keys($cm->getCompanyUsers($company_id)));
		}

		// TODO: check (view) perm

		$regusers_idst=$mdir->aclManager->getGroupRegisteredId();
		$mdir->setUserFilter("group", array($regusers_idst));


		$back_ui_url=$um->getUrl("op=company_users&id=".$company_id);
		//$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));

		$url=$um->getUrl("op=assign_company_users&id=".$company_id."&stayon=1");
		//$um->getUrl("op=assignnewuser&appid=".$app_id."&bugid=".$bug_id."&stayon=1");
		$mdir->loadSelector($url, $lang->def('_BUG_ASSIGNED_USERS'), "", TRUE);

		$out->add(getBackUi($back_ui_url, $lang->def( '_BACK' )));
	}
}


function getNatFields() {
	$add_nat_fields = array(
		'company_name' => array(
							'fieldname' => 'name',
							'filter_field' => TRUE,
							'filter_base' => TRUE,
							'column_field' => TRUE,
							'field_type' => 'textfield',
							),
		'email' => array(
							'fieldname' => 'email',
							'filter_field' => TRUE,
							'filter_base' => FALSE,
							'column_field' => FALSE,
							'field_type' => 'textfield'
							)
		);

	return $add_nat_fields;
}


function getCompanyFilterForm() {
	$out ="";

	$lang=& DoceboLanguage::createInstance("company", "framework");
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

	$um=& UrlManager::getInstance();
	$filter = new Form();
	$cm=new CompanyManager();

	$id ="c_filter";
	$url =$um->getUrl();
	$out.=$filter->openForm("filter_form", $url);
	$out .= $filter->getOpenFieldset($lang->def('_COMPANY_FILTER'));

	$fl = new FieldList();
	$fl->setFieldEntryTable($cm->ccManager->getCompanyFieldEntryTable());
	$arr_fields_filter 	= array();
	$arr_nat_fields 	= array();
	$add_nat_fields 	= getNatFields();

	// set filter for base fields
	require_once($GLOBALS['where_framework'].'/modules/field/class.field.php');
	$field = new Field(0);
	$arr_fields = Field::getArrFieldValue_Filter( $_POST, $add_nat_fields, $id, '_' );
	foreach( $arr_fields as $fname => $fvalue )
		if( isset( $fvalue['value'] ) /*&& $fvalue['value'] != ''*/ )
			$arr_fields_filter[$fname] = $fvalue;

	// set filter for custom fields
	$arr_all_fields = $fl->getAllFields();
	$arr_fields = Field::getArrFieldValue_Filter( $_POST, $arr_all_fields, $id, '_' );
	foreach( $arr_fields as $fname => $fvalue )
		if( isset( $fvalue['value'] ) )
			$arr_fields_filter[$fname] = $fvalue;

	foreach( getNatFields() as $nat_id => $nat_info ) {
		$arr_nat_fields[$nat_id] = $lang->def('_COMPANY_FILTER_'.$nat_id);
	}

	//$out .= $filter->getHidden('ord', 'ord', $ord);
	//$out .= $filter->getHidden('flip', 'flip', $flip);


	if (isset($_POST["add_filter"])) {
		$id_field = $_POST['new_filter'];
		if( is_numeric( $id_field ) ) {
			$arr_fields_filter['ff'.count($arr_fields_filter).'_'.$id_field] = $arr_all_fields[$id_field];
		} else {
			$arr_fields_filter['ff'.count($arr_fields_filter).'_'.$id_field] = $add_nat_fields[$id_field];
		}
	}
	else if (isset($_POST["del_filter"])) {
		$val =$_POST["del_filter"];
		if(is_array($val)) {
			unset($arr_fields_filter[key($val)]);
		}
	}
	else if (isset($_POST["reset_filter"])) {
		$arr_fields_filter =array();
	}
	else if (!isset($_POST["search"])) {
		$arr_fields_filter = unserialize(urldecode($GLOBALS['current_user']->preference->getPreference(_COMPANY_USER_PREF_FILTER )));
	}


	// show complex filter ===========================================================

	$idst=$cm->ccManager->getCompanyFieldsGroupIdst();

	$fl->setGroupFieldsTable($cm->ccManager->getCompanyFieldTable());
	$field_list_arr=$fl->getFieldsArrayFromIdst(array($idst), FIELD_INFO_TRANSLATION);
	$field_list=array_keys($field_list_arr);

	$filter_to_show = $arr_nat_fields+$field_list_arr;

	$out .= $filter->openFormLine();

	$out .= $filter->getInputDropdown( 'new_filter',
										'new_filter',
										'new_filter',
										$filter_to_show,
										'',
										'');
	$out .= ' '.$filter->getButton( 'add_filter',
								'add_filter',
								$lang->def('_ADD'),
								'button_nowh');
	if(!empty($arr_fields_filter)) {

		$out .= ' '.$filter->getButton('reset_filter',
									'reset_filter',
									$lang->def('_RESET'),
									'button_nowh');
	}
	// display selected filter -------------------------------------------------------
	if(!empty($arr_fields_filter))
	foreach( $arr_fields_filter as $field_id => $field_prop ) {

		if( !isset( $field_prop['fieldname'] ) ) {
			// custom field
			$arr_field_info = $fl->getBaseFieldInfo( $field_prop[FIELD_INFO_TYPE] );
			require_once($GLOBALS['where_framework'].'/modules/field/'.$arr_field_info[FIELD_BASEINFO_FILE]);
			$field_obj =  new $arr_field_info[FIELD_BASEINFO_CLASS]( $field_id );

			$del_spot = '<input type="image" class="cancel_filter" '
						.' src="'.getPathImage('framework').'standard/cancel16.gif"'
						.' id="del_filter_'.$field_id.'"'
						.' name="del_filter['.$field_id.']"'
						.' title="'.$lang->def('_DIRECTORY_FILTER_DEL').'"'
						.' alt="'.$lang->def('_DIRECTORY_FILTER_DEL').'" />';


			$out .= $field_obj->play_filter($field_id,
											( isset($field_prop['value']) ? $field_prop['value'] : false ),
											$field_prop[FIELD_INFO_TRANSLATION],
											$id,
											$del_spot,
											'',
											$field_prop[FIELD_INFO_ID]);
			//play_filter( $id_field, $value = FALSE, $label = FALSE, $field_prefix = FALSE, $other_after = '', $other_before = '', $field_special = FALSE )
		} else {
			// base field
			$arr_field_info = $fl->getBaseFieldInfo( $field_prop['field_type'] );
			require_once($GLOBALS['where_framework'].'/modules/field/'.$arr_field_info[FIELD_BASEINFO_FILE]);

			$field_obj =  new $arr_field_info[FIELD_BASEINFO_CLASS]( 0 );

			$del_spot = '<input type="image" class="cancel_filter" '
						.' src="'.getPathImage('framework').'standard/cancel16.gif"'
						.' id="del_filter_'.$field_id.'"'
						.' name="del_filter['.$field_id.']"'
						.' title="'.$lang->def('_FILTER_DEL').'"'
						.' alt="'.$lang->def('_FILTER_DEL').'" />';

			$out .= $field_obj->play_filter($field_id,
											( isset($field_prop['value']) ? $field_prop['value'] : false ),
											$lang->def('_FILTER_'.$field_prop['fieldname']),
											$id,
											$del_spot,
											'',
											'');

		}

	} // end else for filter



	$out .= $filter->openButtonSpace();

	$out .= $filter->getButton('search', 'search', $lang->def('_SEARCH'));
	$out .= $filter->closeButtonSpace();

	$out .= $filter->getCloseFieldset();

	// save state of filter
	$GLOBALS['current_user']->preference->setPreference(
					_COMPANY_USER_PREF_FILTER,
					urlencode(serialize($arr_fields_filter)));

	$res =array();
	$res["out"]=$out;
	$res["filters"]=$arr_fields_filter;
	return $res;
}



// ----------------------------------------------------------------------------


function companyDispatch($op) {

	if (empty($op))
		$op="main";

	if(isset($_POST["undo"])) $op="main";

	switch ($op) {

		case "main": {
			companyMain();
		} break;

		case "add": {
			addeditCompany();
		} break;

		case "save": {
			saveCompany();
		} break;

		case "edit": {
			addeditCompany((int)$_GET["id"]);
		} break;

		case "del": {
			deleteCompany();
		} break;

		case "assignfields": {
			loadAssignFields();
		} break;

		case "saveassigned": {
			saveAssignedFields();
		} break;

		case "assignbuyer": {
			companyAssignBuyer();
		} break;

		case "company_users": {
			showCompanyUsers();
		} break;

		case "assign_company_users": {
			assignCompanyUsers();
		} break;

		case "importcompany": {
			importCompany();
		} break;

		case "loadcsv": {
			loadCompanyCsv();
		} break;

		case "docsvimport": {
			doCsvImport();
		} break;

		case "toggleuserdetails": {
			toggleUserDetails();
		} break;

	}
}

?>