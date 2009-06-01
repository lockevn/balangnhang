<?php
/*************************************************************************/
/* DOCEBO CORE - Framework                                               */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2007 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

// ----------------------------------------------------------------------------

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class ParamSetAdmin {

	var $lang=NULL;
	var $um=NULL;
	var	$table_style=FALSE;

	var $pSetManager=NULL;


	function ParamSetAdmin() {
		$this->lang =& DoceboLanguage::createInstance('admin_paramset', "ecom");
		$this->pSetManager=new ParamSetManager();
	}


	function getTableStyle() {
		return $this->table_style;
	}


	function setTableStyle($style) {
		$this->table_style=$style;
	}


	function titleArea($text, $image = '', $alt_image = '') {
		$res="";

		$res=getTitleArea($text, $image = '', $alt_image = '');

		return $res;
	}


	function getHead() {
		$res="";
		$res.="<div class=\"std_block\">\n";
		return $res;
	}


	function getFooter() {
		$res="";
		$res.="</div>\n";
		return $res;
	}


	function backUi($url=FALSE) {
		$res="";
		$um=& UrlManager::getInstance();

		if ($url === FALSE)
			$url=$um->getUrl();

		$res.=getBackUi($url, $this->lang->def( '_BACK' ));
		return $res;
	}


	function urlManagerSetup($std_query) {
		require_once($GLOBALS['where_framework']."/lib/lib.urlmanager.php");

		$um=& UrlManager::getInstance();

		$um->setStdQuery($std_query);
	}


	function getParamSetTable($vis_item) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_TABLE_PARAMSET_CAP");
		$table_summary=$this->lang->def("_TABLE_PARAMSET_SUM");

		$um=& UrlManager::getInstance();
		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_TITLE"));


		$img ="<img src=\"".getPathImage('fw')."standard/modelem.gif\" alt=\"".$this->lang->def("_ALT_MODITEMS")."\" ";
		$img.="title=\"".$this->lang->def("_ALT_MODITEMS")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
		$img.="title=\"".$this->lang->def("_MOD")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
		$img.="title=\"".$this->lang->def("_DEL")."\" />";
		$head[]=$img;

		$head_type=array("", "image", "image", "image", "image", "image");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink($um->getUrl());

		$ini=$tab->getSelectedElement();

		$data_info=$this->pSetManager->getParamSetList($ini, $vis_item);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["set_id"];

			$rowcnt=array();
			$rowcnt[]=$data_arr[$i]["title"];


			$img ="<img src=\"".getPathImage('fw')."standard/modelem.gif\" alt=\"".$this->lang->def("_ALT_MODITEMS")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_MODITEMS")."\" />";
			$url=$um->getUrl("op=showset&set_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$url=$um->getUrl("op=editset&set_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";


			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
			$img.="title=\"".$this->lang->def("_DEL")."\" />";
			$url=$um->getUrl("op=delset&set_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$tab->addBody($rowcnt);
		}

		$url=$um->getUrl("op=addset");
		$add_box ="<a class=\"new_element_link_float\" href=\"".$url."\">".$this->lang->def('_ADD')."</a>\n";
		$tab->addActionAdd($add_box);

		$res=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}


	function addeditSet($id=0) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$form=new Form();
		$form_code="";

		$um=& UrlManager::getInstance();
		$url=$um->getUrl("op=saveset");

		if ($id == 0) {
			$form_code=$form->openForm("main_form", $url);
			$submit_lbl=$this->lang->def("_INSERT");

			$title="";
			$description="";
		}
		else if ($id > 0) {
			$form_code=$form->openForm("main_form", $url);
			$submit_lbl=$this->lang->def("_SAVE");

			$info=$this->pSetManager->getSetInfo($id);

			$title=$info["title"];
			$description=$info["description"];
		}


		$res.=$form_code;
		$res.=$form->openElementSpace();

		$res.=$form->getTextfield($this->lang->def("_TITLE"), "title", "title", 255, $title);
		$res.=$form->getSimpleTextarea($this->lang->def("_DESCRIPTION"), "description", "description", $description);

		$res.=$form->getHidden("id", "id", $id);


		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $submit_lbl);
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function saveSet() {
		$um=& UrlManager::getInstance();

		$set_id=$this->pSetManager->saveSet($_POST);

		$url=$um->getUrl();
		jumpTo($url);
	}


	function deleteSet($set_id) {
		include_once($GLOBALS['where_framework']."/lib/lib.form.php");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();


		if (isset($_POST["undo"])) {
			jumpTo($back_url);
		}
		else if (isset($_POST["conf_del"])) {

			$this->pSetManager->deleteSet($set_id);

			jumpTo($back_url);
		}
		else {

			$res="";
			$info=$this->pSetManager->getSetInfo($set_id);
			$title=$info["title"];

			$form=new Form();

			$url=$um->getUrl("op=delset&set_id=".$set_id);
			$res.=$form->openForm("delete_form", $url);

			$res.=getDeleteUi(
			$this->lang->def('_AREYOUSURE'),
				'<span class="text_bold">'.$this->lang->def('_TITLE').' :</span> '.$title.'<br />',
				false,
				'conf_del',
				'undo');

			$res.=$form->closeForm();
			return $res;
		}
	}


	function showFieldGroups($set_id, $vis_item) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_TABLE_FIELDGRP_CAP");
		$table_summary=$this->lang->def("_TABLE_FIELDGRP_SUM");

		$um=& UrlManager::getInstance();
		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_TITLE"));


		$img ="<img src=\"".getPathImage('fw')."standard/list.gif\" alt=\"".$this->lang->def("_MANAGE_FIELDS")."\" ";
		$img.="title=\"".$this->lang->def("_MANAGE_FIELDS")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$this->lang->def("_MOVE_DOWN")."\" ";
		$img.="title=\"".$this->lang->def("_MOVE_DOWN")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$this->lang->def("_MOVE_UP")."\" ";
		$img.="title=\"".$this->lang->def("_MOVE_UP")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
		$img.="title=\"".$this->lang->def("_MOD")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
		$img.="title=\"".$this->lang->def("_DEL")."\" />";
		$head[]=$img;

		$head_type=array("", "image", "image", "image", "image", "image");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink($um->getUrl());

		$ini=$tab->getSelectedElement();


		$data_info=$this->pSetManager->getFieldGroups($set_id, $ini, $vis_item);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["fieldgrp_id"];

			$rowcnt=array();
			$rowcnt[]=$data_arr[$i]["title"][getLanguage()];


			$img ="<img src=\"".getPathImage('fw')."standard/list.gif\" alt=\"".$this->lang->def("_MANAGE_FIELDS")."\" ";
			$img.="title=\"".$this->lang->def("_MANAGE_FIELDS")."\" />";
			$url=$um->getUrl("op=showgrpitems&set_id=".$set_id."&fieldgrp_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			if (($i > 0) && ($ini+$i < $db_tot-1)) {
				$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$this->lang->def("_MOVE_DOWN")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_DOWN")."\" />";
				$url=$um->getUrl("op=movefieldgrpdown&set_id=".$set_id."&fieldgrp_id=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			if ($ini+$i > 1) {
				$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$this->lang->def("_MOVE_UP")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_UP")."\" />";
				$url=$um->getUrl("op=movefieldgrpup&set_id=".$set_id."&fieldgrp_id=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$url=$um->getUrl("op=editfieldgrp&set_id=".$set_id."&fieldgrp_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			if (!$data_arr[$i]["is_main"]) {
				$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
				$img.="title=\"".$this->lang->def("_DEL")."\" />";
				$url=$um->getUrl("op=deletefieldgrp&set_id=".$set_id."&fieldgrp_id=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			$tab->addBody($rowcnt);
		}

		$url=$um->getUrl("op=addfieldgrp&set_id=".$set_id);
		$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".$this->lang->def('_ADD')."</a>\n");

		$res=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}


	function addeditFieldGroup($set_id, $id=0) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$form=new Form();
		$form_code="";

		$um=& UrlManager::getInstance();
		$url=$um->getUrl("op=savefieldgrp&set_id=".$set_id);

		if ($id == 0) {
			$form_code=$form->openForm("main_form", $url);
			$submit_lbl=$this->lang->def("_INSERT");

			$title="";
			$question="";
			$keyword="";
			$answer="";
		}
		else if ($id > 0) {
			$form_code=$form->openForm("main_form", $url);
			$submit_lbl=$this->lang->def("_SAVE");

			$info=$this->pSetManager->getFieldGroupInfo($id);

			$title=$info["title"];
			$description=$info["description"];
		}


		$res.=$form_code;
		$res.=$form->openElementSpace();

		$larr=$GLOBALS['globLangManager']->getAllLangCode();
		foreach($larr as $lang) {
			$field_id ="title_".$lang;
			$field_name ="title[".$lang."]";
			$res.=$form->getTextfield($this->lang->def("_TITLE"). " (".$lang.")", $field_id, $field_name, 255, $title[$lang]);
		}
		$res.=$form->getTextarea($this->lang->def("_DESCRIPTION"), "description", "description", $description);

		$res.=$form->getHidden("set_id", "set_id", $set_id);
		$res.=$form->getHidden("id", "id", $id);


		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $submit_lbl);
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function saveFieldGroup($set_id) {
		$um=& UrlManager::getInstance();

		$fieldgrp_id =$this->pSetManager->saveFieldGroup($set_id, $_POST);

		$url=$um->getUrl("op=showset&set_id=".$set_id);
		jumpTo($url);
	}


	function deleteFieldGroup($set_id, $fieldgrp_id) {
		include_once($GLOBALS['where_framework']."/lib/lib.form.php");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl("op=showset&set_id=".$set_id);


		if (isset($_POST["undo"])) {
			jumpTo($back_url);
		}
		else if (isset($_POST["conf_del"])) {

			$this->pSetManager->deleteFieldGroup($fieldgrp_id);

			jumpTo($back_url);
		}
		else {

			$res="";
			$info=$this->pSetManager->getFieldGroupInfo($fieldgrp_id);
			$title=$info["title"][getLanguage()];

			$form=new Form();

			$url=$um->getUrl("op=deletefieldgrp&set_id=".$set_id."&fieldgrp_id=".$fieldgrp_id);
			$res.=$form->openForm("delete_form", $url);

			$res.=$form->getHidden("fieldgrp_id", "fieldgrp_id", $fieldgrp_id);
			$res.=$form->getHidden("set_id", "set_id", $set_id);

			$res.=getDeleteUi(
			$this->lang->def('_AREYOUSURE'),
				'<span class="text_bold">'.$this->lang->def('_TITLE').' :</span> '.$title.'<br />',
				false,
				'conf_del',
				'undo');

			$res.=$form->closeForm();
			return $res;
		}
	}


	function moveFieldGroup($fieldgrp_id, $set_id, $direction) {
		$um=& UrlManager::getInstance();

		$this->pSetManager->moveFieldGroup($direction, $fieldgrp_id, $set_id);

		$url=$um->getUrl("op=showset&set_id=".$set_id);
		jumpTo($url);
	}


	function showGroupItems($fieldgrp_id, $set_id, $vis_item) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

		$table_caption=$this->lang->def("_TABLE_FIELDGRP_CAP");
		$table_summary=$this->lang->def("_TABLE_FIELDGRP_SUM");

		$um=& UrlManager::getInstance();
		$um->addToStdQuery("fieldgrp_id=".(int)$fieldgrp_id."&set_id=".(int)$set_id);
		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_TITLE"));


		/*$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
		$img.="title=\"".$this->lang->def("_MOD")."\" />"; */
		$head[]=$this->lang->def("_COMPULSORY");//$img;
		$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$this->lang->def("_MOVE_DOWN")."\" ";
		$img.="title=\"".$this->lang->def("_MOVE_DOWN")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$this->lang->def("_MOVE_UP")."\" ";
		$img.="title=\"".$this->lang->def("_MOVE_UP")."\" />";
		$head[]=$img;

		$head_type=array("", "image", "image", "image");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink($um->getUrl());

		$ini=$tab->getSelectedElement();


		$fl=new FieldList();
		$fl->setFieldTable($GLOBALS["prefix_ecom"]."_product_field");
		$all_fields=$fl->getAllFields();

		$data_info=$this->pSetManager->getGroupItems($fieldgrp_id, $set_id, $ini, $vis_item);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["item_id"];
			$idField =$data_arr[$i]["idField"];

			$rowcnt=array();
			$rowcnt[]=$all_fields[$idField][FIELD_INFO_TRANSLATION];



			if ($data_arr[$i]["compulsory"] == 1) {
				$img ="<img src=\"".getPathImage('fw')."standard/active_on.gif\" alt=\"".$this->lang->def("_COMPULSORY_FIELD")."\" ";
				$img.="title=\"".$this->lang->def("_COMPULSORY_FIELD")."\" />";
			}
			else {
				$img ="<img src=\"".getPathImage('fw')."standard/active_off.gif\" alt=\"".$this->lang->def("_NOT_COMPULSORY_FIELD")."\" ";
				$img.="title=\"".$this->lang->def("_NOT_COMPULSORY_FIELD")."\" />";
			}
			$url=$um->getUrl("op=switchitemcompstatus&cur=".$data_arr[$i]["compulsory"]."&item_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			if ($ini+$i < $db_tot-1) {
				$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$this->lang->def("_MOVE_DOWN")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_DOWN")."\" />";
				$url=$um->getUrl("op=movegrpitemdown&item_id=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			if ($ini+$i > 0) {
				$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$this->lang->def("_MOVE_UP")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_UP")."\" />";
				$url=$um->getUrl("op=movegrpitemup&item_id=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			$tab->addBody($rowcnt);
		}

		$url=$um->getUrl("op=addremfields");
		$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".$this->lang->def('_ADDREM_FIELDS')."</a>\n");

		$res=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}


	function groupAddRemFields($fieldgrp_id, $set_id) {
		$res ="";
		require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');


		$form =new Form();
		$fl=new FieldList();
		$fl->setFieldTable($GLOBALS["prefix_ecom"]."_product_field");
		$all_fields=$fl->getAllFields();

		$um=& UrlManager::getInstance();

		$res.=$form->openForm("main_form", $um->getUrl("op=savegrpitems&set_id=".$set_id."&fieldgrp_id=".$fieldgrp_id));
		$res.=$form->openElementSpace();

		$data_info =$this->pSetManager->getGroupItems($fieldgrp_id, $set_id, FALSE, FALSE, FALSE, TRUE);
		$sel_items =$data_info["idField_list"]; // print_r($sel_items); print_r($all_fields);

		foreach($all_fields as $key=>$val) {

			$sel =(in_array($key, $sel_items) ? TRUE : FALSE);

			if ($val[FIELD_INFO_TYPE] !== "upload") {
				$res.=$form->getCheckBox($val[FIELD_INFO_TRANSLATION], "field_".$key."_", "field[".$key."]", 1, $sel);
			}
		}

		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $this->lang->def('_SAVE'));
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function saveGroupItems($fieldgrp_id, $set_id) {
		$um=& UrlManager::getInstance();

		$this->pSetManager->saveGroupItems($fieldgrp_id, $set_id, $_POST);

		$url=$um->getUrl("op=showgrpitems&set_id=".$set_id."&fieldgrp_id=".$fieldgrp_id);
		jumpTo($url);
	}


	function moveGroupItem($fieldgrp_id, $set_id, $item_id, $direction) {
		$um=& UrlManager::getInstance();

		$this->pSetManager->moveGroupItem($direction, $fieldgrp_id, $set_id, $item_id);

		$url=$um->getUrl("op=showgrpitems&set_id=".$set_id."&fieldgrp_id=".$fieldgrp_id);
		jumpTo($url);
	}


	function switchItemCompulsoryStatus($fieldgrp_id, $set_id, $item_id, $current) {
		$um=& UrlManager::getInstance();

		$this->pSetManager->switchItemCompulsoryStatus($fieldgrp_id, $set_id, $item_id, $current);

		$url=$um->getUrl("op=showgrpitems&set_id=".$set_id."&fieldgrp_id=".$fieldgrp_id);
		jumpTo($url);
	}


}






Class ParamSetPublic {

	var $lang=NULL;
	var $pSetManager=NULL;


	function ParamSetPublic() {
		$this->lang =& DoceboLanguage::createInstance("paramset", "ecom");
		$this->pSetManager=new ParamSetManager();
	}


	function getTableStyle() {
		return $this->table_style;
	}


	function setTableStyle($style) {
		$this->table_style=$style;
	}


	function titleArea($text, $image = '', $alt_image = '') {
		$res="";

		if ($GLOBALS["platform"] == "cms") {
			$res=getCmsTitleArea($text, $image = '', $alt_image = '');
		}
		else {
			$res=getTitleArea($text, $image = '', $alt_image = '');
		}

		return $res;
	}


	function getHead() {
		$res="";
		$res.="<div class=\"std_block\">\n";
		return $res;
	}


	function getFooter() {
		$res="";
		$res.="</div>\n";
		return $res;
	}


	function backUi($url=FALSE) {
		$res="";
		$um=& UrlManager::getInstance();

		if ($url === FALSE)
			$url=$um->getUrl();

		$res.=getBackUi($url, $this->lang->def( '_BACK' ));
		return $res;
	}


	function readModeFromUrl() {
		$mode=((isset($_GET["mode"])) && (!empty($_GET["mode"])) ? $_GET["mode"] : "paramset");
		return $mode;
	}



	function getSearchForm($set_id, $mode) {
		$res="";
		require_once($GLOBALS["where_ecom"]."/lib/lib.form.php");

		$form=new Form();
		$um=& UrlManager::getInstance();

		$search_info=$this->getSearchInfo();
		$search=$search_info["search_txt"];
		$letter=$search_info["letter"];

		$res.=$form->openForm("glossary_play", $um->getUrl("op=search&mode=".$mode));

		$res.=$form->getOpenFieldset($this->lang->def('_FILTER'));
		$res.=$form->getHidden('idCategory', 'idCategory', $set_id);
		//$res.=$form->getHidden('back_url', 'back_url', $back_coded);

		$search_txt=(($search != '') && (!isset($_POST["empty"])) ? $search : "");
		$res.=$form->getTextfield($this->lang->def('_SEARCH'), 'search', 'search', 255, $search_txt);

		$base_url="op=search&mode=".$mode."&";

		if ($mode == "help") {

			$res.="[ ";

			//letter selection
			for($i = 97; $i < 123; $i++) {
				if($letter == $i)
					$res.='<span class="text_bold">(';
				$res.='<a href="'.$um->getUrl($base_url."letter=".$i).'">'.chr($i).'</a>';

				if($letter == $i)
					$res.=')</span>';
				if($i < 122)
					$res.='-';
			}

			$res.='&nbsp;]&nbsp;[&nbsp;';
			// Numbers
			for($i = 48; $i < 58; $i++) {
				if ($letter == $i)
					$res.='<span class="text_bold">(';
				$res.='<a href="'.$um->getUrl($base_url."letter=".$i).'">'.chr($i).'</a>';

				if ($letter == $i)
					$res.=')</span>';
				if ($i < 57)
					$res.='-';
			}
			$res.=' ] ';

		}

		$res.=$form->getBreakRow();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('do_search', 'do_search', $this->lang->def('_SEARCH'));
		$res.=$form->getButton('clear_search', 'clear_search', $this->lang->def('_CLEAR_SEARCH'));
		$res.=$form->closeButtonSpace();
		$res.=$form->getCloseFieldset();
		$res.=$form->closeForm();

		return $res;
	}


	function showCategoryItems($set_id, $data_arr, $db_tot=FALSE, $read_only=FALSE) {
		$res="";

		$um=& UrlManager::getInstance();
		$mode=$this->readModeFromUrl();

		$search_info=$this->getSearchInfo();
		$search_txt=$search_info["search_txt"];

		$can_add=$this->checkCategoryPerm($set_id, "add", TRUE);
		$can_edit=$this->checkCategoryPerm($set_id, "edit", TRUE);

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$title=$data_arr[$i]["title"];
			$question=$data_arr[$i]["question"];
			$answer=$data_arr[$i]["answer"];

			if (!empty($search_txt)) {
				$new_text="<span class=\"paramset_evidence\">".$search_txt."</span>";
				$question=preg_replace("/".$search_txt."/i", $new_text, $question);
				$answer=preg_replace("/".$search_txt."/i", $new_text, $answer);
			}


			if ($can_edit) {
				$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
				$img.="title=\"".$this->lang->def("_MOD")."\" />";
				$url=$um->getUrl("op=editfieldgrp&set_id=".$data_arr[$i]["paramset_id"]);

				$edit_code ="<div class=\"paramset_edit_box\">";
				$edit_code.="<a href=\"".$url."\">".$img."</a>\n";
				$edit_code.="</div>\n";
			}
			else
				$edit_code="";

			$res.="<div class=\"paramset_boxinfo_title\">";
			if ($mode == "paramset")
				$res.=$question;
			else if ($mode == "help")
				$res.=$title;
			$res.="</div>\n";

			$res.="<div class=\"paramset_boxinfo_container\">";
			$res.=$edit_code;
			$res.="<p>".$answer."</p>";
			$res.="</div>\n";

		}

		if ($can_add) {
			$url=$um->getUrl("op=addfieldgrp");
			$res.="<div class=\"paramset_add_box\">";
			$res.="<a href=\"".$url."\">".$this->lang->def("_ADD")."</a>\n";
			$res.="</div>\n";
		}

		return $res;
	}


	function extractKeys($data_arr, $filter=TRUE) {
		$res=array();

		$search_info=$this->getSearchInfo();
		$letter=$search_info["letter"];

		foreach ($data_arr as $data) {

			$key_arr=explode(",", $data["keyword"]);

			if ((is_array($key_arr)) && (count($key_arr) > 0)) {

				foreach ($key_arr as $key) {
					$key=trim($key);
					if ((!$filter) || (empty($letter)) || (preg_match("/^".chr($letter)."/i", $key))) {
						$res[$key]=(isset($res[$key]) ? $res[$key]+=1 : 1);
					}
				}
			}
		}

		ksort($res);

		return $res;
	}


	function showKeysMenu($keys) {
		$res="";

		$um=& UrlManager::getInstance();
		$mode=$this->readModeFromUrl();

		$base_url="op=search&mode=".$mode."&";

		$res.="<div class=\"paramset_boxinfo_title\">";
		$res.=$this->lang->def('_KEYWORDS');
		$res.="</div>\n";

		$res.="<ul class=\"unformatted_list\">";

		foreach ($keys as $key=>$count) {

			$um->setModRewriteTitle(format_mod_rewrite_title($key));
			$url=$um->getUrl($base_url."keyword=".base64_encode($key));

			$res.="<li>";
			$res.="<a href=\"".$url."\">".$key."</a> (".$count.")";
			$res.="</li>\n";

		}

		$res.="</ul>";

		return $res;
	}


	function showParamSetList($set_id, $read_only=FALSE) {
		$res="";

		$um=& UrlManager::GetInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$cat_info=$this->pSetManager->getSetInfo($set_id);

		$mode=$this->readModeFromUrl();
		$title=$cat_info["title"];
		$um->setModRewriteTitle(format_mod_rewrite_title($title));

		$res.=$this->displayModeMenu($mode);
		$res.=$this->getSearchForm($set_id, $mode);

		$res.="<div class=\"paramset_cat_title\">".$this->lang->def('_TITLE').": ".$title."</div>\n";


		if ($mode == "help") { // Show keywords..
			// we have to get all data, without search filter..
			$data_info=$this->pSetManager->getCategoryItems($set_id);
			$data_arr=$data_info["data_arr"];

			$keys=$this->extractKeys($data_arr);
			$res.="<div class=\"paramset_colum_25\">";
			$res.=$this->showKeysMenu($keys);
			$res.="</div>\n"; // colum_25
			$res.="<div class=\"paramset_colum_75\">";
		}


		$where=$this->getSearchWhere();

		$data_info=$this->pSetManager->getCategoryItems($set_id, FALSE, FALSE, $where);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$res.=$this->showCategoryItems($set_id, $data_arr, $db_tot, $read_only);

		if ($mode == "help") {
			$res.="</div>\n"; // colum_75
		}

		return $res;
	}


	function setSearch() {

		$um=& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		if (!isset($_POST["clear_search"])) {

			if (isset($_POST["search"])) {
				$_SESSION["paramset_search"]["search_txt"]=$_POST["search"];
			}

			if ((isset($_GET["letter"])) && ((int)$_GET["letter"] > 0)) {
				$_SESSION["paramset_search"]["letter"]=$_GET["letter"];
			}

			if ((isset($_GET["keyword"])) && (!empty($_GET["keyword"]))) {
				$_SESSION["paramset_search"]["keyword"]=base64_decode($_GET["keyword"]);
			}
		}
		else {
			$_SESSION["paramset_search"]["search_txt"]="";
			$_SESSION["paramset_search"]["letter"]="";
			$_SESSION["paramset_search"]["keyword"]="";
		}

		$mode=$this->readModeFromUrl();
		$url=$um->getUrl("mode=".$mode);
		jumpTo($url);
	}


	function getSearchInfo() {
		$res=array();

		$res["search_txt"]=(isset($_SESSION["paramset_search"]["search_txt"]) ? $_SESSION["paramset_search"]["search_txt"] : "");
		$res["letter"]=(isset($_SESSION["paramset_search"]["letter"]) ? $_SESSION["paramset_search"]["letter"] : "");
		$res["keyword"]=(isset($_SESSION["paramset_search"]["keyword"]) ? $_SESSION["paramset_search"]["keyword"] : "");

		return $res;
	}


	function getSearchWhere() {
		$res="";
		$first=TRUE;

		$mode=$this->readModeFromUrl();

		$search_info=$this->getSearchInfo();
		$search=$search_info["search_txt"];
		$letter=$search_info["letter"];
		$keyword=$search_info["keyword"];


		if (($mode == "help") && (!empty($letter))) {
			/*
			$res.=(!$first ? " AND " : "");
			$res.="question LIKE '".chr($letter)."%'";
			$first=FALSE;
			*/
		}


		if (!empty($search)) {

			$res.=(!$first ? " AND " : "");
			$res.="( question LIKE '%".$search."%' OR answer LIKE '%".$search."%' )";
			$first=FALSE;

		}


		if (($mode == "help") && (!empty($keyword))) {

			$res.=(!$first ? " AND " : "");
			$res.="keyword LIKE '%".$keyword."%'";
			$first=FALSE;

		}


		if (empty($res))
			$res=FALSE;

		return $res;
	}


	function addeditFieldGroup($set_id, $paramset_id, $todo) {
		$res="";

		if ($set_id < 1)
			return FALSE;

		// Check permissions
		$this->checkCategoryPerm($set_id, $todo);

		$cfa=new ParamSetAdmin();
		$res.=$cfa->addeditFieldGroup($set_id, $paramset_id);

		return $res;
	}


	function saveFieldGroup($set_id) {

		$um=& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		if ($set_id < 1)
			return FALSE;

		if ((int)$_POST["id"] > 0)
			$todo="edit";
		else
			$todo="add";

		// Check permissions
		$this->checkCategoryPerm($set_id, $todo);

		$this->pSetManager->saveFieldGroup($set_id, $_POST);

		$back_url=$um->getUrl();
		jumpTo($back_url);
	}


}






Class ParamSetManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $paramset_info=NULL;
	var $fieldgrp_info=NULL;
	var $grpitem_info=NULL;

	function ParamSetManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_ecom"]);
		$this->dbconn=$dbconn;
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


	function _getParamSetTable() {
		return $this->prefix."_paramset";
	}


	function _getFieldGroupTable() {
		return $this->prefix."_paramset_fieldgrp";
	}


	function _getGroupItemTable() {
		return $this->prefix."_paramset_grpitem";
	}


	function getLastOrd($table, $where=FALSE) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");
		return utilGetLastOrd($table, "ord", $where=FALSE);
	}


	function moveFieldGroup($direction, $id_val, $set_id) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

		$table=$this->_getFieldGroupTable();

		$where ="set_id='".(int)$set_id."'";
		utilMoveItem($direction, $table, "fieldgrp_id", $id_val, "ord", $where);
	}


	function getParamSetList($ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getParamSetTable()." ";

		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="WHERE ".$where." ";
		}

		$qtxt.="ORDER BY title ";
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
			while($row=mysql_fetch_assoc($q)) {

				$id=$row["set_id"];
				$data_info["data_arr"][$i]=$row;
				$this->paramset_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function saveSet($data) {

		$set_id=(int)$data["id"];
		$title=$data["title"];
		$description=$data["description"];

		if ($set_id < 1) {

			$field_list ="title, description";
			$field_val="'".$title."', '".$description."'";

			$qtxt="INSERT INTO ".$this->_getParamSetTable()." (".$field_list.") VALUES(".$field_val.")";
			$res=$this->_executeInsert($qtxt);

			// Add set main Field Group
			$field_list ="set_id, title, is_main, ord";
			$field_val="'".$res."', '".def("_MAIN_GROUP", "admin_paramset", "ecom")."', '1', '1'";
			$qtxt="INSERT INTO ".$this->_getFieldGroupTable()." (".$field_list.") VALUES(".$field_val.")";
			$q =$this->_executeQuery($qtxt);
		}
		else {

			$qtxt ="UPDATE ".$this->_getParamSetTable()." SET title='".$title."', ";
			$qtxt.="description='".$description."' ";
			$qtxt.="WHERE set_id='".$set_id."'";
			$q=$this->_executeQuery($qtxt);

			$res=$set_id;
		}

		return $res;
	}


	function loadSetInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getParamSetTable()." ";
		$qtxt.="WHERE set_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getSetInfo($id) {

		if (!isset($this->paramset_info[$id])) {
			$info=$this->loadSetInfo($id);
			$this->paramset_info[$id]=$info;
		}

		return $this->paramset_info[$id];
	}


	function deleteSet($set_id) {

		// Delete set
		$qtxt ="DELETE FROM ".$this->_getParamSetTable()." ";
		$qtxt.="WHERE set_id='".(int)$set_id."' LIMIT 1";
		$q=$this->_executeQuery($qtxt);

		// Delete set groups
		$qtxt ="DELETE FROM ".$this->_getFieldGroupTable()." ";
		$qtxt.="WHERE set_id='".(int)$set_id."'";
		$q=$this->_executeQuery($qtxt);

		// Delete set group items
		$qtxt ="DELETE FROM ".$this->_getGroupItemTable()." ";
		$qtxt.="WHERE set_id='".(int)$set_id."'";
		$q=$this->_executeQuery($qtxt);
	}


	function getParamSetArray($include_any=FALSE) {
		$res=array();

		$available_param_set =$this->getParamSetList();
		$param_set_list =$available_param_set["data_arr"];

		if ($include_any)
			$res[0]=def("_ANY", "ticket", "crm");

		foreach ($param_set_list as $param_set) {
			$id =$param_set["set_id"];
			$res[$id] =$param_set["title"];
		}

		return $res;
	}


	function getFieldGroups($set_id, $ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getFieldGroupTable()." ";

		$qtxt.="WHERE set_id='".(int)$set_id."' ";
		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		$qtxt.="ORDER BY is_main DESC, ord ASC ";
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

				$title =unserialize($row["title"]);

				if (!is_array($title)) {
					$title =array();
					$title[getLanguage()] =$row["title"];
				}

				$row["title"] =$title;

				$id=$row["fieldgrp_id"];
				$data_info["data_arr"][$i]=$row;
				$this->fieldgrp_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadFieldGroupInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getFieldGroupTable()." ";
		$qtxt.="WHERE fieldgrp_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);

			$title =unserialize($res["title"]);

			if (!is_array($title)) {
				$title =array();
				$title[getLanguage()] =$res["title"];
			}

			$res["title"] =$title;
		}

		return $res;
	}


	function getFieldGroupInfo($id) {

		if (!isset($this->fieldgrp_info[$id])) {
			$info=$this->loadFieldGroupInfo($id);
			$this->fieldgrp_info[$id]=$info;
		}

		return $this->fieldgrp_info[$id];
	}


	function saveFieldGroup($set_id, $data) {

		$fieldgrp_id=(int)$data["id"];
		$set_id=(int)$set_id;
		$title =serialize($data["title"]);
		$description=$data["description"];


		if ($fieldgrp_id < 1) { // Add

			$ord=$this->getLastOrd($this->_getFieldGroupTable(), "set_id='".$set_id."'")+1;

			$field_list ="set_id, title, description, ord";
			$field_val ="'".(int)$set_id."', '".$title."', '".$description."', ";
			$field_val.="'".$ord."'";

			$qtxt="INSERT INTO ".$this->_getFieldGroupTable()." (".$field_list.") VALUES (".$field_val.")";
			$res=$this->_executeInsert($qtxt);
		}
		else { // Update

			$qtxt ="UPDATE ".$this->_getFieldGroupTable()." SET title='".$title."', ";
			$qtxt.="description='".$description."' ";
			$qtxt.="WHERE fieldgrp_id='".$fieldgrp_id."' AND set_id='".(int)$set_id."'";
			$q=$this->_executeQuery($qtxt);

			$res=$set_id;
		}

		return $res;
	}


	function deleteFieldGroup($fieldgrp_id) {

		// Delete paramset
		$qtxt ="DELETE FROM ".$this->_getFieldGroupTable()." ";
		$qtxt.="WHERE fieldgrp_id='".(int)$fieldgrp_id."' LIMIT 1";
		$q=$this->_executeQuery($qtxt);

		// Delete set group items
		$qtxt ="DELETE FROM ".$this->_getGroupItemTable()." ";
		$qtxt.="WHERE fieldgrp_id='".(int)$fieldgrp_id."'";
		$q=$this->_executeQuery($qtxt);
	}


	function getGroupItems($fieldgrp_id, $set_id, $ini=FALSE, $vis_item=FALSE, $where=FALSE, $extra_info=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();
		if ($extra_info) {
			$data_info["item_id_list"] =array();
			$data_info["idField_list"] =array();
		}

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getGroupItemTable()." ";

		$qtxt.="WHERE fieldgrp_id='".(int)$fieldgrp_id."' AND set_id='".(int)$set_id."' ";
		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		$qtxt.="ORDER BY ord ASC ";
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

				$id=$row["item_id"];
				$data_info["data_arr"][$i]=$row;
				$this->fieldgrp_info[$id]=$row;

				if ($extra_info) {
					$data_info["item_id_list"][]=$id;
					$data_info["idField_list"][]=$row["idField"];
				}

				$i++;
			}
		}

		return $data_info;
	}


	function getGroupItemFieldList($fieldgrp_id, $set_id=FALSE, $unique=FALSE) {
		$res =array();

		$group_by ="";

		$fields="idField";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getGroupItemTable()." ";

		if (($fieldgrp_id > 0) && ($set_id === FALSE)) {
			$qtxt.="WHERE fieldgrp_id='".(int)$fieldgrp_id."' ";
		}
		else if (($fieldgrp_id > 0) && ($set_id !==FALSE)) {
			$qtxt.="WHERE fieldgrp_id='".(int)$fieldgrp_id."' ";
			$qtxt.="AND set_id='".(int)$set_id."' ";
		}
		else if (($fieldgrp_id === FALSE ) && ($set_id > 0)) {
			$qtxt.="WHERE set_id='".(int)$set_id."' ";
			$group_by ="GROUP BY idField ";
		}

		$qtxt.=$group_by."ORDER BY ord ASC";
		$q=$this->_executeQuery($qtxt);


		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_array($q)) {
				$res[]=$row["idField"];
			}
		}

		if ($unique) {
			$res =array_unique($res);
		}

		return $res;
	}


	function saveGroupItems($fieldgrp_id, $set_id, $data) {

		$fieldgrp_id =(int)$fieldgrp_id;
		$set_id =(int)$set_id;
		$field_list =(isset($data["field"]) ? array_keys($data["field"]) : array());


		$data_info =$this->getGroupItems($fieldgrp_id, $set_id, FALSE, FALSE, FALSE, TRUE);
		$saved =$data_info["idField_list"];
		$ord =$this->getLastOrd($this->_getGroupItemTable(), "fieldgrp_id='".$fieldgrp_id."' AND set_id='".$set_id."'")+1;

		$to_add =array_diff($field_list, $saved);
		$to_rem =array_diff($saved, $field_list);

		foreach ($to_add as $idField) {
			$qtxt ="INSERT INTO ".$this->_getGroupItemTable()." (fieldgrp_id, set_id, idField, ord) ";
			$qtxt.="VALUES('".(int)$fieldgrp_id."', '".(int)$set_id."', '".$idField."', '".$ord."')";
			$this->_executeQuery($qtxt);
			$ord++;
		}

		if (count($to_rem) > 0) {
			$qtxt ="DELETE FROM ".$this->_getGroupItemTable()." WHERE ";
			$qtxt.="fieldgrp_id='".(int)$fieldgrp_id."' AND set_id='".(int)$set_id."' ";
			$qtxt.="AND idField IN (".implode(",", $to_rem).")";
			$this->_executeQuery($qtxt);
		}
	}


	function moveGroupItem($direction, $fieldgrp_id, $set_id, $item_id) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

		$table=$this->_getGroupItemTable();

		$where ="fieldgrp_id='".(int)$fieldgrp_id."' AND set_id='".(int)$set_id."'";
		utilMoveItem($direction, $table, "item_id", $item_id, "ord", $where);
	}


	function switchItemCompulsoryStatus($fieldgrp_id, $set_id, $item_id, $current) {

		$new =($current == 1 ? 0 : 1);

		$qtxt ="UPDATE ".$this->_getGroupItemTable()." SET compulsory='".$new."' ";
		$qtxt.="WHERE fieldgrp_id='".(int)$fieldgrp_id."' AND set_id='".(int)$set_id."' ";
		$qtxt.="AND item_id='".(int)$item_id."'";

		$res =$this->_executeQuery($qtxt);

		return $res;
	}


}



?>
