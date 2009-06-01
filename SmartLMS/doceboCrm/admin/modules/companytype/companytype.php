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
 * @version  $Id: companytype.php 827 2006-11-27 18:50:02Z giovanni $
 */
// ----------------------------------------------------------------------------

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');
if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

require_once($GLOBALS["where_framework"]."/lib/lib.company.php");


function companyTypeMain() {
	$res="";

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("companytype", "crm");

	require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
	$table_caption=$lang->def("_COMPANY_TYPE_TAB_CAPTION");
	$table_summary=$lang->def("_COMPANY_TYPE_TAB_SUMMARY");

	$vis_item=$GLOBALS["visuItem"];


	$back_ui_url="index.php?modname=companytype&amp;op=main";

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY_TYPE");
	$res.=getTitleArea($title_arr, "companytype");
	$res.="<div class=\"std_block\">\n";
	//$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$tab=new typeOne($vis_item, $table_caption, $table_summary);


	$head=array($lang->def("_DESCRIPTION"));
	$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$lang->def("_MOVE_DOWN")."\" ";
	$img.="title=\"".$lang->def("_MOVE_DOWN")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$lang->def("_MOVE_UP")."\" ";
	$img.="title=\"".$lang->def("_MOVE_UP")."\" />";
	$head[]=$img;
/*	$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$lang->def("_ALT_ASSIGNFIELDS")."\" ";
	$img.="title=\"".$lang->def("_ALT_ASSIGNFIELDS")."\" />";
	$head[]=$img; */
	$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
	$img.="title=\"".$lang->def("_MOD")."\" />";
	$head[]=$img;
	$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
	$img.="title=\"".$lang->def("_DEL")."\" />";
	$head[]=$img;

	$head_type=array("", "image", "image", "image", "image", "image");


	$tab->setColsStyle($head_type);
	$tab->addHead($head);

	$tab->initNavBar('ini', 'link');
	$tab->setLink("index.php");

	$ini=$tab->getSelectedElement();

	$ctm=new CompanyTypeManager();
	$list=$ctm->getCompanyTypeList($ini, $vis_item);
	$list_arr=$list["data_arr"];
	$db_tot=$list["data_tot"];

	$tot=count($list_arr);
	for($i=0; $i<$tot; $i++ ) {

		$id=$list_arr[$i]["ctype_id"];

		$rowcnt=array();
		$rowcnt[]=$list_arr[$i]["label"];

		if ($i+$ini+1 < $db_tot) {
			$url="index.php?modname=companytype&amp;op=movedown&amp;id=".$id;
			$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$lang->def("_MOVE_DOWN")."\" ";
			$img.="title=\"".$lang->def("_MOVE_DOWN")."\" />";
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
		}
		else
			$rowcnt[]="&nbsp;";

		if ($i+$ini > 0) {
			$url="index.php?modname=companytype&amp;op=moveup&amp;id=".$id;
			$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$lang->def("_MOVE_UP")."\" ";
			$img.="title=\"".$lang->def("_MOVE_UP")."\" />";
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
		}
		else
			$rowcnt[]="&nbsp;";


		/* $url="index.php?modname=companytype&amp;op=assignfields&amp;id=".$id;
		$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$lang->def("_ALT_ASSIGNFIELDS")."\" ";
		$img.="title=\"".$lang->def("_ALT_ASSIGNFIELDS")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n"; */

		$url="index.php?modname=companytype&amp;op=edit&amp;id=".$id;
		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$lang->def("_MOD")."\" ";
		$img.="title=\"".$lang->def("_MOD")."\" />";
		$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";


		if (!$list_arr[$i]["is_used"]) {
			$url="index.php?modname=companytype&amp;op=del&amp;id=".$id;
			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$lang->def("_DEL")."\" ";
			$img.="title=\"".$lang->def("_DEL")."\" />";
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
		}
		else
			$rowcnt[]="&nbsp;";

		$tab->addBody($rowcnt);
	}


	$url="index.php?modname=companytype&amp;op=add";
	$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".$lang->def('_ADD')."</a>\n");


	$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);

	$res.="</div>\n";
	$out->add($res);
}


function addeditCompanyType($id=0) {
	$res=""; // TODO: check perm

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("companytype", "crm");

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$form=new Form();
	$res="";

	$form_code="";
	$url="index.php?modname=companytype";

	if ($id == 0) {
		$form_code=$form->openForm("main_form", $url."&amp;op=save");
		$submit_lbl=$lang->def("_INSERT");
		$page_title=$lang->def("_ADD_ITEM");

		$label="";
	}
	else if ($id > 0) {
		$form_code=$form->openForm("main_form", $url."&amp;op=save");

		$ctm=new CompanyTypeManager();
		$stored=$ctm->getCompanyTypeInfo($id);

		$label=$stored["label"];
		$submit_lbl=$lang->def("_MOD");
		$page_title=$lang->def("_MOD").": ".$label;
	}


	$back_ui_url="index.php?modname=companytype&amp;op=main";

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY_TYPE");
	$title_arr[]=$page_title;
	$res.=getTitleArea($title_arr, "companytype");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

	$res.=$form_code.$form->openElementSpace();

	$res.=$form->getTextfield($lang->def("_DESCRIPTION"), "label", "label", 255, $label);

	$res.=$form->getHidden("id", "id", $id);


	$res.=$form->closeElementSpace();
	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $submit_lbl);
	$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();


	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));
	$res.="</div>\n";
	$out->add($res);
}


function saveCompanyType() {

	$ctm=new CompanyTypeManager();

	// TODO: check perm

	$ctm->saveData($_POST);
	jumpTo("index.php?modname=companytype&op=main");
}


function moveItem($direction, $id_val) {

	// TODO: check perm

	$ctm=new CompanyTypeManager();
	$ctm->moveItem($direction, $id_val);

	jumpTo("index.php?modname=companytype&op=main");
}


function deleteCompanyType() {
	$res="";

	include_once($GLOBALS['where_framework']."/lib/lib.form.php");

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("companytype", "crm");
	$ctm=new CompanyTypeManager();

	$back_url="index.php?modname=companytype&op=main";


	if (isset($_POST["undo"])) {
		jumpTo($back_url);
	}
	else if (isset($_POST["conf_del"])) {

		// TODO: check perm
		// TODO: check if is used
		$ctm->deleteCompanyType($_POST["id"]);

		jumpTo($back_url);
	}
	else {

		$id=(int)importVar("id");
		$stored=$ctm->getCompanyTypeInfo($id);
		$label=$stored["label"];

		$back_ui_url="index.php?modname=companytype&amp;op=main";

		$title_arr=array();
		$title_arr[$back_ui_url]=$lang->def("_COMPANY_TYPE");
		$title_arr[]=$lang->def("_DEL").": ".$label;
		$res.=getTitleArea($title_arr, "companytype");
		$res.="<div class=\"std_block\">\n";
		$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));

		$form=new Form();

		$url="index.php?modname=companytype&amp;op=del";

		$res.=$form->openForm("main_form", $url);

		$res.=$form->getHidden("id", "id", $id);


		$res.=getDeleteUi(
		$lang->def('_AREYOUSURE'),
			'<span class="text_bold">'.$lang->def('_DESCRIPTION').' :</span> '.$label.'<br />',
			false,
			'conf_del',
			'undo');

		$res.=$form->closeForm();
		$res.="</div>\n";
		$out->add($res);
	}
}


function loadAssignFields($id) {
	$res=""; // TODO: check perm

	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");
	$lang=& DoceboLanguage::createInstance("companytype", "crm");
	$form=new Form();

	$fl = new FieldList();
	$fl->setGroupFieldsTable($GLOBALS['prefix_crm']."_ctype_field");

	$ctm=new CompanyTypeManager();
	$stored=$ctm->getCompanyTypeInfo($id);

	$back_ui_url="index.php?modname=companytype&amp;op=main";

	$title_arr=array();
	$title_arr[$back_ui_url]=$lang->def("_COMPANY_TYPE");
	$title_arr[]=$lang->def("_ASSIGN_FIELDS_TO").": ".$stored["label"];
	$res.=getTitleArea($title_arr, "companytype");
	$res.="<div class=\"std_block\">\n";
	$res.=getBackUi($back_ui_url, $lang->def( '_BACK' ));


	$url="index.php?modname=companytype&amp;op=saveassigned";
	$res.=$form->openForm("main_form", $url);
	$res.=$form->openElementSpace();

	$arr_all_fields=$fl->getAllFields(array("crm"));
	$sel_fields=$fl->getFieldsFromIdst(array($id));
	$sel_fields_list=array_keys($sel_fields);

	// print_r($arr_all_fields);
	// print_r($sel_fields);


	$tab=new TypeOne(0, $lang->def('_FIELD_LIST_CAPTION'), $lang->def('_FIELD_LIST_SUMMARY'));
	$tab->setTableStyle('tree_org_table_field');
	$tab->addHeadCustom('<tr class="first_intest">'
		.'<th scope="col" abbr="'.$lang->def('_FIELD_NAME_ABBR').'">'
		.$lang->def('_FIELD_NAME').'</th>'
		.'<th scope="col" abbr="'.$lang->def('_FIELD_MANDATORY_ABBR').'">'
		.$lang->def('_FIELD_MANDATORY').'</th>'
		.'<th scope="col" abbr="'.$lang->def('_FIELD_USERACCESS_ABBR').'">'
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

	$res.=$form->getHidden("id", "id", $id);

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

	// TODO: check perm

	$fl = new FieldList();
	$fl->setGroupFieldsTable($GLOBALS['prefix_crm']."_ctype_field");

	$id=(int)$_POST["id"];
	$fields=$_POST["fields"];
	$mandatory_arr=$_POST["mandatory"];
	$useraccess_arr=$_POST["useraccess"];
	$field_in_db=$_POST["field_in_db"];

	foreach($field_in_db as $field_id) {
		$fl->removeFieldFromGroup($field_id, $id);
	}

	foreach($fields as $field_id) {

		$mandatory=(in_array($field_id, $mandatory_arr) ? "true" : "false");
		$useraccess=(in_array($field_id, $useraccess_arr) ? "readwrite" : "readonly");

		$fl->addFieldToGroup($field_id, $id, $mandatory, $useraccess);
	}

	jumpTo("index.php?modname=companytype&op=main");
}


// ----------------------------------------------------------------------------

$op=(isset($_GET["op"]) ? $_GET["op"] : "main");

switch ($op) {

	case "main": {
			companyTypeMain();
	} break;

	case "add": {
		addeditCompanyType();
	} break;

	case "save": {
		if (!isset($_POST["undo"]))
			saveCompanyType();
		else
			companyTypeMain();
	} break;

	case "edit": {
		addeditCompanyType((int)$_GET["id"]);
	} break;

	case "movedown": {
		moveItem("down", (int)$_GET["id"]);
	} break;

	case "moveup": {
		moveItem("up", (int)$_GET["id"]);
	} break;

	case "del": {
		deleteCompanyType();
	} break;

	case "assignfields": {
		// loadAssignFields((int)$_GET["id"]);
	} break;

	case "saveassigned": {
		if (!isset($_POST["undo"]))
			saveAssignedFields();
		else
			companyTypeMain();
	} break;

}

?>