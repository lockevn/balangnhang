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


define("_CATALOG_FPATH_INTERNAL", "/doceboEcom/catalog/");
define("_CATALOG_FPATH", $GLOBALS["where_files_relative"]._CATALOG_FPATH_INTERNAL);
define("_CATALOG_PPATH_INTERNAL", "/doceboEcom/catalog/preview/");
define("_CATALOG_PPATH", $GLOBALS["where_files_relative"]._CATALOG_PPATH_INTERNAL);
define("_CATALOG_CPATH_INTERNAL", "/doceboEcom/catalog/category/");
define("_CATALOG_CPATH", $GLOBALS["where_files_relative"]._CATALOG_CPATH_INTERNAL);



class CatalogAdmin {

	var $lang =NULL;
	var $um =NULL;
	var	$table_style =FALSE;

	var $catalogManager =NULL;


	function CatalogAdmin() {
		$this->lang =& DoceboLanguage::createInstance('admin_catalog', "ecom");
		$this->catalogManager =new CatalogManager();
	}


	function getTableStyle() {
		return $this->table_style;
	}


	function setTableStyle($style) {
		$this->table_style =$style;
	}


	function titleArea($text, $image = '', $alt_image = '') {
		$res="";

		$res =getTitleArea($text, $image = '', $alt_image = '');

		return $res;
	}


	function getHead() {
		$res ="";
		$res.='<div class="std_block">'."\n" ;
		return $res;
	}


	function getFooter() {
		$res ="";
		$res.="</div>\n";
		return $res;
	}


	function backUi($url=FALSE) {
		$res ="";
		$um =& UrlManager::getInstance();

		if ($url === FALSE)
			$url =$um->getUrl();

		$res.=getBackUi($url, $this->lang->def( '_BACK' ));
		return $res;
	}


	function urlManagerSetup($std_query) {
		require_once($GLOBALS['where_framework']."/lib/lib.urlmanager.php");

		$um =& UrlManager::getInstance();

		$um->setStdQuery($std_query);
	}


	function getCatalogMain($vis_item) {
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
		$res ="";

		$um =& UrlManager::getInstance();
		$url =$um->getUrl();

		$form =new Form();
		$language =getLanguage();

		$catalog_page_db=new TreeDb_CatalogDb($this->catalogManager->getCategoryTable(), $this->catalogManager->getCategoryInfoTable(), $language);
		$treeView=new TreeView_CatalogView($catalog_page_db, 'catalog_tree');

		if(isset($_SESSION['ecom_catalog']['tree_status'])) {
			$arr_state = @unserialize(stripslashes($_SESSION['ecom_catalog']['tree_status']));
			if(is_array($arr_state))
				$treeView->setState($arr_state);
		}
		$treeView->parsePositionData($_POST, $_POST, $_POST);
		$_SESSION['ecom_catalog']['tree_status'] = addslashes(serialize($treeView->getState()));

		$folder_id=$treeView->getSelectedFolderId();
		$folder_name=$treeView->getFolderPrintName($catalog_page_db->getFolderById($folder_id));

		switch ($treeView->getOp()) {
			case "newfolder":
			case "renamefolder": {
				$res.=$form->openForm("main_form", $url, "", "", "multipart/form-data");
			} break;
			default: {
				$res.=$form->openForm("main_form", $url);
			} break;
		}

		$res.=$treeView->autoLoad();

		$res.=$form->closeForm();

		if ($treeView->getOp() == "display") {
			$res.=$this->getProductsTable($folder_id, $vis_item);
		}

		return $res;
	}


	function getProductsTable($folder_id, $vis_item) {
		$res ="";


		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_TABLE_PRODUCTS_CAP");
		$table_summary=$this->lang->def("_TABLE_PRODUCTS_SUM");

		$um=& UrlManager::getInstance();
		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_PRD_CODE"));
		$head[]=$this->lang->def("_TITLE");
		$head[]=$this->lang->def("_PRICE");


		$img ="<img src=\"".getPathImage()."catalog/photo.png\" alt=\"".$this->lang->def("_PRD_PICS")."\" ";
		$img.="title=\"".$this->lang->def("_PRD_PICS")."\" />";
		$head[]=$img;

		$img ="<img src=\"".getPathImage()."catalog/folders.png\" alt=\"".$this->lang->def("_PRD_PICS")."\" ";
		$img.="title=\"".$this->lang->def("_PRD_PICS")."\" />";
		$head[]=$img;


		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
		$img.="title=\"".$this->lang->def("_MOD")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
		$img.="title=\"".$this->lang->def("_DEL")."\" />";
		$head[]=$img;

		$head_type=array("image", "", "", "image", "image", "image", "image");


		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink($um->getUrl());

		$ini=$tab->getSelectedElement();

		$language =getLanguage();
		$data_info =$this->catalogManager->getProductList($folder_id, $language, $ini, $vis_item);
		$data_arr =$data_info["data_arr"];
		$db_tot =$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["prd_id"];

			$rowcnt=array();
			$rowcnt[]=$data_arr[$i]["prd_code"];
			$rowcnt[]=$data_arr[$i]["title"];
			$rowcnt[]=$data_arr[$i]["price"];



			$img ="<img src=\"".getPathImage()."catalog/photo.png\" alt=\"".$this->lang->def("_PRD_PICS")."\" ";
			$img.="title=\"".$this->lang->def("_PRD_PICS")."\" />";
			$url=$um->getUrl("op=manpics&prd_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage()."catalog/folders.png\" alt=\"".$this->lang->def("_PRD_FOLDERS")."\" ";
			$img.="title=\"".$this->lang->def("_PRD_FOLDERS")."\" />";
			$url=$um->getUrl("op=selfolders&prd_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";


			$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$url=$um->getUrl("op=editproduct&prd_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
			$img.="title=\"".$this->lang->def("_DEL")."\" />";
			$url=$um->getUrl("op=delproduct&prd_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$tab->addBody($rowcnt);
		}

		$url =$um->getUrl("op=addproduct&folder_id=".$folder_id);
		$add_box ="<a class=\"new_element_link_float\" href=\"".$url."\">".$this->lang->def('_ADD')."</a>\n";
		$tab->addActionAdd($add_box);

		$res =$tab->getTable();
		if ($tot > 0) {
			$res.=$tab->getNavBar($ini, $db_tot);
		}

		return $res;
	}


	function addeditProduct($id) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
		require_once($GLOBALS["where_ecom"]."/lib/lib.paramset.php");
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

		$form=new Form();
		$form_code="";

		$fl = new FieldList();
		$um=& UrlManager::getInstance();
		$url=$um->getUrl("op=saveproduct");


		$fl->setFieldTable($GLOBALS["prefix_ecom"]."_product_field");
		$fl->setFieldEntryTable($GLOBALS["prefix_ecom"]."_product_field_entry");
		$fl->setUseMultiLang(TRUE);

		if ($id < 1) {
			$form_code=$form->openForm("main_form", $url, "", "", "multipart/form-data");
			$submit_lbl=$this->lang->def("_INSERT");

			$prd_code ="";
			$price ="";
			$image ="";
			$sel_ps =FALSE;
			$can_add_to_cart =TRUE;
		}
		else {
			$form_code=$form->openForm("main_form", $url, "", "", "multipart/form-data");
			$submit_lbl=$this->lang->def("_SAVE");

			$info =$this->catalogManager->getProductInfo($id);

			$prd_code =$info["prd_code"];
			$price =$info["price"];
			$image =$info["image"];
			$sel_ps =$info["param_set_id"];
			$can_add_to_cart =($info["can_add_to_cart"] == 1 ? TRUE : FALSE);
		}


		$res.=$form_code;
		$res.=$form->openElementSpace();

		$res.=$form->getTextfield($this->lang->def("_PRD_CODE"), "prd_code", "prd_code", 255, $prd_code);

		$larr=$GLOBALS['globLangManager']->getAllLangCode();
		foreach ($larr as $key=>$val) {
			$field_id ="title_".$val;
			$field_name ="title[".$val."]";
			$title =(isset($info["title"][$val]) ? $info["title"][$val] : "");
			$res.=$form->getTextfield($this->lang->def("_TITLE").' ('.$val.')', $field_id, $field_name, 255, $title);
		}

		$res.=$form->getTextfield($this->lang->def("_PRICE"), "price", "price", 20, $price);
		$res.=$form->getExtendedFilefield($this->lang->def('_IMAGE'), "image", "image", $image);

		$psm =new ParamSetManager();
		$ps_array =$psm->getParamSetArray();
		$res.=$form->getDropdown("param_set_id", "param_set_id", "param_set_id", $ps_array, $sel_ps);

		$res.=$form->getCheckBox($this->lang->def("_CAN_ADD_TO_CART"), "can_add_to_cart", "can_add_to_cart", 1, $can_add_to_cart);

		$larr=$GLOBALS['globLangManager']->getAllLangCode();
		foreach ($larr as $key=>$val) {
			$field_id ="description".$val;
			$field_name ="description[".$val."]";
			$description =(isset($info["description"][$val]) ? $info["description"][$val] : "");
			$res.=$form->getTextarea($this->lang->def("_DESCRIPTION").' ('.$val.')', $field_id, $field_name, $description);
		}

		if ($id < 1) {
			$res.=$form->getHidden("cat_id", "cat_id", (int)$_GET["folder_id"]);
		}
		$res.=$form->getHidden("id", "id", $id);

		$res.=$form->getOpenFieldset($this->lang->def("_CUSTOM_FIELDS"));
		$grp_items =$psm->getGroupItemFieldList(FALSE, $sel_ps, TRUE);
		$res.=$fl->playSpecFields($grp_items, FALSE, ($id > 0 ? $id : FALSE));
		$res.=$form->getCloseFieldset();

		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $submit_lbl);
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function saveProduct() {
		$um =& UrlManager::getInstance();

		$prd_id =$this->catalogManager->saveProduct($_POST);

		$url =$um->getUrl();
		jumpTo($url);
	}


	function deleteProduct($prd_id) {
		$res ="";
		include_once($GLOBALS['where_framework']."/lib/lib.form.php");

		$um=& UrlManager::getInstance();
		$back_url =$um->getUrl();


		if (isset($_POST["undo"])) {
			jumpTo($back_url);
		}
		else if (isset($_POST["conf_del"])) {

			$this->catalogManager->deleteProduct($prd_id);

			jumpTo($back_url);
		}
		else {

			$info =$this->catalogManager->getProductInfo($prd_id);
			$title=$info["title"];

			$form=new Form();

			$url=$um->getUrl("op=delproduct&prd_id=".$prd_id);
			$res.=$form->openForm("delete_form", $url);

			$res.=getDeleteUi(
			$this->lang->def('_AREYOUSURE'),
				'<span class="text_bold">'.$this->lang->def('_TITLE').' :</span> '.$title.'<br />',
				false,
				'conf_del',
				'undo');

			$res.=$form->closeForm();
		}
		return $res;
	}


	function selProductFolders($prd_id) {

		$um =& UrlManager::getInstance();

		if (isset($_POST["undo"])) {
			$url =$um->getUrl();
			jumpTo($url);
		}
		else if (isset($_POST["save"])) {

			$checked_items_name =$_POST["checked_items_name"];
			$checked_items =(isset($_POST[$checked_items_name]) ? $_POST[$checked_items_name] : array());

			if ((is_array($checked_items)) && (count($checked_items) > 0)) {
				$this->catalogManager->saveItemCategories($prd_id, $checked_items);

				$url =$um->getUrl();
				jumpTo($url);
			}
			else {
				$res =getErrorUi($this->lang->def("_ERR_SELECT_ATLEAST_ONE_CAT"));
				$res.=$this->selProductFoldersMain($prd_id);
				return $res;
			}
		}
		else {
			return $this->selProductFoldersMain($prd_id);
		}
	}


	function selProductFoldersMain($prd_id) {
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$prd_id =(int)$prd_id;

		$res ="";
		$um =& UrlManager::getInstance();
		$url =$um->getUrl("op=selfolders&prd_id=".$prd_id);

		$form =new Form();
		$res.=$form->openForm("main_form", $url);


		$language =getLanguage();
		$catalog_page_db=new TreeDb_CatalogDb($this->catalogManager->getCategoryTable(), $this->catalogManager->getCategoryInfoTable(), $language);
		$treeView=new TreeView_CatalogView($catalog_page_db, 'catalog_tree');


		if (isset($_POST["from_post"])) {
			$checked_items =(isset($_POST[$treeView->getCheckedItemsName()]) ? $_POST[$treeView->getCheckedItemsName()] : array());
		}
		else {
			$checked_items =$this->catalogManager->getItemCategories($prd_id);
		}
		$treeView->setCheckedItems($checked_items);


		$treeView->setShowCheckbox(TRUE);
		$treeView->hideInlineAction();
		$treeView->hideAction();
		$treeView->parsePositionData($_POST, $_POST, $_POST);
		$folder_id=$treeView->getSelectedFolderId();
		$folder_name=$treeView->getFolderPrintName($catalog_page_db->getFolderById($folder_id));
		$res.=$treeView->autoLoad();

		$res.=$form->getHidden("from_post", "from_post", "1");
		$res.=$form->getHidden("checked_items_name", "checked_items_name", $treeView->getCheckedItemsName());

		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $this->lang->def('_SAVE'));
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}



	function getProductImagesTable($prd_id, $vis_item) {
		$res ="";

		$prd_id =(int)$prd_id;

		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_TABLE_PRODUCT_IMAGES_CAP");
		$table_summary=$this->lang->def("_TABLE_PRODUCT_IMAGES_SUM");

		$um=& UrlManager::getInstance();
		$um->addToStdQuery("prd_id=".$prd_id);
		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_IMG_PREVIEW"));
		$head[]=$this->lang->def("_TITLE");
		$head[]=$this->lang->def("_DESCRIPTION");


		$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$this->lang->def("_MOVE_DOWN")."\" ";
		$img.="title=\"".$this->lang->def("_MOVE_DOWN")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$this->lang->def("_MOVE_UP")."\" ";
		$img.="title=\"".$this->lang->def("_MOVE_UP")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('ecom')."standard/active.gif\" alt=\"".$this->lang->def("_PUBLISH_STATUS")."\" ";
		$img.="title=\"".$this->lang->def("_PUBLISH_STATUS")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
		$img.="title=\"".$this->lang->def("_MOD")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
		$img.="title=\"".$this->lang->def("_DEL")."\" />";
		$head[]=$img;

		$head_type=array("image", "", "", "image", "image", "image", "image", "image");


		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink($um->getUrl());

		$ini=$tab->getSelectedElement();

		$data_info =$this->catalogManager->getProductImagesList($prd_id, $ini, $vis_item);
		$data_arr =$data_info["data_arr"];
		$db_tot =$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["img_id"];

			$rowcnt=array();
			if (!empty($data_arr[$i]["image"])) {
				$img_name =implode('_', array_slice(explode('_', $data_arr[$i]["image"]), 3));
				$img ="<img src=\""._CATALOG_PPATH.$data_arr[$i]["image"]	."\" ";
				$img.="alt=\"".$img_name."\" title=\"".$img_name."\" />";
				$rowcnt[]=$img;
			}
			else {
				$rowcnt[]="&nbsp;";
			}
			$rowcnt[]=$data_arr[$i]["title"];

			$description =strip_tags($data_arr[$i]["description"]);
			if (strlen($description) > 100) {
				$description =substr($description, 0, 100)."...";
			}
			$rowcnt[]=$description;


			if ($ini+$i < $db_tot-1) {
				$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$this->lang->def("_MOVE_DOWN")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_DOWN")."\" />";
				$url=$um->getUrl("op=moveimgdown&img_id=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			if ($ini+$i > 0) {
				$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$this->lang->def("_MOVE_UP")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_UP")."\" />";
				$url=$um->getUrl("op=moveimgup&img_id=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}


			if ($data_arr[$i]["published"] > 0) {
				$img_title =$this->lang->def("_UNPUBLISH_IMAGE");
				$img_fname ="active";
			}
			else {
				$img_title =$this->lang->def("_PUBLISH_IMAGE");
				$img_fname ="deactive";
			}
			$img ="<img src=\"".getPathImage('ecom')."standard/".$img_fname.".gif\" alt=\"".$img_title."\" ";
			$img.="title=\"".$img_title."\" />";
			$url=$um->getUrl("op=switchimgpublish&img_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$url=$um->getUrl("op=editimage&img_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
			$img.="title=\"".$this->lang->def("_DEL")."\" />";
			$url=$um->getUrl("op=delimage&img_id=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$tab->addBody($rowcnt);
		}

		$url =$um->getUrl("op=addimage&prd_id=".$prd_id);
		$add_box ="<a class=\"new_element_link_float\" href=\"".$url."\">".$this->lang->def('_ADD')."</a>\n";
		$tab->addActionAdd($add_box);

		$res =$tab->getTable();
		if ($tot > 0) {
			$res.=$tab->getNavBar($ini, $db_tot);
		}

		return $res;
	}


	function addeditImage($id, $prd_id) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$form=new Form();
		$form_code="";

		$um=& UrlManager::getInstance();
		$url=$um->getUrl("op=saveimage&prd_id=".$prd_id);

		$form_code=$form->openForm("main_form", $url, "", "", "multipart/form-data");

		if ($id < 1) {
			$submit_lbl=$this->lang->def("_INSERT");

			$image ="";
			$title ="";
			$description ="";
		}
		else {
			$submit_lbl=$this->lang->def("_SAVE");

			$info =$this->catalogManager->getProductImageInfo($id);

			$image =$info["image"];
			$title =$info["title"];
			$description =$info["description"];
		}


		$res.=$form_code;
		$res.=$form->openElementSpace();
		$res.=$form->getExtendedFilefield($this->lang->def('_IMAGE'), "image", "image", $image);
		$res.=$form->getTextfield($this->lang->def("_TITLE"), "title", "title", 255, $title);
		$res.=$form->getTextarea($this->lang->def("_DESCRIPTION"), "description", "description", $description);


		$res.=$form->getHidden("img_id", "img_id", $id);


		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $submit_lbl);
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function saveProductImage($prd_id) {
		$um =& UrlManager::getInstance();

		$img_id =$this->catalogManager->saveProductImage($_POST, $prd_id);

		$url =$um->getUrl("op=manpics&prd_id=".$prd_id);
		jumpTo($url);
	}


	function moveImage($prd_id, $img_id, $direction) {
		$um=& UrlManager::getInstance();

		$this->catalogManager->moveImage($direction, $img_id);

		$url=$um->getUrl("op=manpics&prd_id=".$prd_id);
		jumpTo($url);
	}


	function switchImgPublish($prd_id, $img_id) {
		$um=& UrlManager::getInstance();

		$this->catalogManager->switchImgPublish($img_id);

		$url=$um->getUrl("op=manpics&prd_id=".$prd_id);
		jumpTo($url);
	}


	function deleteProductImage($prd_id, $img_id) {
		$res ="";
		include_once($GLOBALS['where_framework']."/lib/lib.form.php");

		$um=& UrlManager::getInstance();
		$back_url =$um->getUrl("op=manpics&prd_id=".$prd_id);


		if (isset($_POST["undo"])) {
			jumpTo($back_url);
		}
		else if (isset($_POST["conf_del"])) {

			$this->catalogManager->deleteProductImage($img_id);

			jumpTo($back_url);
		}
		else {

			$info =$this->catalogManager->getProductImageInfo($img_id);
			$title=$info["title"];

			$form=new Form();

			$url=$um->getUrl("op=delimage&prd_id=".$prd_id."&img_id=".$img_id);
			$res.=$form->openForm("delete_form", $url);

			$res.=getDeleteUi(
			$this->lang->def('_AREYOUSURE'),
				'<span class="text_bold">'.$this->lang->def('_TITLE').' :</span> '.$title.'<br />',
				false,
				'conf_del',
				'undo');

			$res.=$form->closeForm();
		}
		return $res;
	}


}





class CatalogPublic {

	var $lang =NULL;
	var $um =NULL;
	var $catalogManager =NULL;
	var	$table_style =FALSE;


	function CatalogPublic() {
		$this->lang =& DoceboLanguage::createInstance('catalog', "ecom");
		$this->catalogManager =new CatalogManager();
	}


	function getTableStyle() {
		return $this->table_style;
	}


	function setTableStyle($style) {
		$this->table_style =$style;
	}


	function urlManagerSetup($std_query) {
		require_once($GLOBALS['where_framework']."/lib/lib.urlmanager.php");

		$um =& UrlManager::getInstance();

		$um->setStdQuery($std_query);
	}


	function getCategoriesMenuCode($from_path, $lev=FALSE, $cat_id=FALSE) {

		$um=& UrlManager::getInstance();

		if ($lev === FALSE) {
			$cat_info =$this->catalogManager->getCategoryInfo(FALSE, $from_path);
			$lev =$cat_info["lev"];
		}

		$language =getLanguage();
		$categories =$this->catalogManager->getCategoryMenuList($from_path, $lev, $language);

		$res ='<ul class="catalog_menu_list">';
		foreach($categories as $cat) {

			if (($lev > 0) && ($cat["lev"] > $lev)) {
				$menu_item ="child_menu_item";
			}
			else if ($cat["lev"] < $lev) {
				$menu_item ="parent_menu_item";
			}
			else {
				$menu_item ="menu_item";
			}

			$res.='<li class="'.($cat_id == $cat["cat_id"] ? "menu_sel_item" : "").' '.$menu_item.'">';
			$url =$um->getUrl("cat_id=".$cat["cat_id"]);
			// temp --------:
			$tmp_link_arr =array(4, 5);
			if (in_array($cat["cat_id"], $tmp_link_arr)) {
				$url ="http://81.74.194.121/ipmexe/ipm.exe?TXN=0&USR=0&FUN=7&TIM=1188892738&MEN=1";
			}
			// -----------------------
			$res.='<a href="'.$url.'">';
			$res.=$cat["name"];
			$res.='</a></li>';

		}
		$res.="</ul>\n";

		return $res;
	}


	function showCategoryItems($cat_id, $show_subcat=FALSE) {
		$res ="";
		//require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		//$table_caption=$this->lang->def("_TABLE_PRODUCTS_CAP");
		//$table_summary=$this->lang->def("_TABLE_PRODUCTS_SUM");

		$um=& UrlManager::getInstance();
		$um->addToStdQuery("cat_id=".(int)$cat_id);


		if ($show_subcat) {
			$res.=$this->showSubCategories($cat_id);
		}


		$language =getLanguage();
		$data_info =$this->catalogManager->getProductList($cat_id, $language);
		$data_arr =$data_info["data_arr"];
		$db_tot =$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["prd_id"];

			$res.='<div class="prd_line_'.($i % 2 == 0 ? "1" : "0").'">';

			if (!empty($data_arr[$i]["image"])) {
				$img =_CATALOG_PPATH.$data_arr[$i]["image"];
				$res.='<div class="prd_img"><img src="'.$img.'" /></div>';
			}

			$res.='<div class="prd_info">';
			$url =$um->getUrl("op=product&prd_id=".$id);
			$res.='<span class="prd_title">';
			$res.='<a href="'.$url.'">'.$data_arr[$i]["title"]."</a>";
			$res.='</span>';

			$res.='<p class="prd_description">';
			$description =$data_arr[$i]["description"];
			$max_len =500;
			if (strlen($description) > $max_len) {
				$description =substr($description, 0 , $max_len);
				$cut_at =strpos($description, "\n\r", $max_len -200);
				if ($cut_at === FALSE) {
					$cut_at =strrpos($description, " ");
				}
				else {
					$cut_at =$cut_at-1;
				}
				$description =substr($description, 0 , $cut_at)."...";
			}
			$res.=$description;
			$res.='</p>';

			$extra_info ="";
			if ((!$GLOBALS["current_user"]->isAnonymous()) && ((int)str_replace(",", ".", $data_arr[$i]["price"]) > 0)) {
				$extra_info.=$this->lang->def("_PRICE").": ".$data_arr[$i]["price"];
			}

			if (!empty($extra_info)) {
				$res.='<div class="prd_extra_info">';
				$res.=$extra_info;
				$res.='</div>';
			}


			$res.="</div>"; // prd_info

			$res.='<div class="no_float"></div>';
			$res.='<div class="prd_buttons">';
			$res.='<a href="'.$um->getUrl("op=product&prd_id=".$id).'">';
			$res.=$this->lang->def("_ITEM_DETAILS").'&raquo;</a></div>';

			$res.="</div>\n"; // prd_line
		}

		/*
		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_PRD_CODE"));
		$head[]=$this->lang->def("_TITLE");
		$head[]=$this->lang->def("_PRICE");


		$head_type=array("image", "", "");


		$tab->setColsStyle($head_type);
		//$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink($um->getUrl());

		$ini=$tab->getSelectedElement();

		//$data_info =$this->catalogManager->getProductList($cat_id, $ini, $vis_item);
		$data_info =$this->catalogManager->getProductList($cat_id);
		$data_arr =$data_info["data_arr"];
		$db_tot =$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["prd_id"];

			$rowcnt=array();
			$rowcnt[]=$data_arr[$i]["prd_code"];

			$url =$um->getUrl("op=product&prd_id=".$id);
			$rowcnt[]='<a href="'.$url.'">'.$data_arr[$i]["title"]."</a>";

			$rowcnt[]=$data_arr[$i]["price"];



			$tab->addBody($rowcnt);
		}


		$res =$tab->getTable();
		if ($tot > 0) {
			$res.=$tab->getNavBar($ini, $db_tot);
		} */

		return $res;
	}


	function showSubCategories($parent_id) {
		$res ="";

		$um=& UrlManager::getInstance();

		$res.='<div class="categories_list_box">';
		$res.='<ul class="categories_list">';

		$cat_list =$this->catalogManager->getSubCategoriesList($parent_id);

		if (empty($cat_list)) {
			return "";
		}

		$i =0;
		foreach($cat_list as $category) {

			$res.='<li class="cat_line_'.($i % 2 == 0 ? "1" : "0").'">';
			$name =$category["name"];
			$res.='<a href="'.$um->getUrl("cat_id=".$category["cat_id"]).'">';
			$res.='<div class="cat_img_container">';
			if (!empty($category["image"])) {
				$res.='<img src="'._CATALOG_CPATH.$category["image"].'" alt="'.$name.'" title="'.$name.'" />';
			}
			else {
				$res.='<img src="'.getPathImage()."catalog/no_image.gif".'" alt="'.$name.'" title="'.$name.'" />';
			}
			$res.='</div>';
			$res.='<span class="category_name">'.$name.'</span></a>';
			$res.='</li>';
			$i++;
		}

		$res.="</ul>";
		$res.='<div class="no_float"></div>';
		$res.="</div>\n";
		return $res;
	}


	function showProductDetails($prd_id, $cat_id) {
		$res ="";
		require_once($GLOBALS["where_ecom"]."/lib/lib.paramset.php");

		$prd_id =(int)$prd_id;
		$cat_id =(int)$cat_id;

		$um=& UrlManager::getInstance();
		$psm =new ParamSetManager();
		$info =$this->catalogManager->getProductInfo($prd_id);
		$language =getLanguage();


		$res.='<div class="detail_prd_title">';
		$title =(isset($info["title"][$language]) ? $info["title"][$language] : "");
		$res.=$title;
		$res.='</div>';

		$res.="<ul class=\"catalog_toolbar\">\n";

		$tab_op ="main";
		if ((isset($_GET["tab_op"])) && (!empty($_GET["tab_op"]))) {
			$tab_op =substr($_GET["tab_op"], 0 , 20);
		}


		$um->addToStdQuery("cat_id=".$cat_id."&prd_id=".$prd_id);


		$data_info=$psm->getFieldGroups($info["param_set_id"]);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id =$data_arr[$i]["fieldgrp_id"];
			$label =$data_arr[$i]["title"][getLanguage()];

			$cur_op=($data_arr[$i]["is_main"] == 1 ? "main" : "tab_".$id);
			$img ="";
			if ($cur_op != $tab_op) {
				$url=$um->getUrl("op=product&tab_op=".$cur_op);
				$res.="<li>";
				$res.="<a href=\"".$url."\"><span>";
				$res.=$label."</span></a>";
			}
			else {
				$res.="<li class=\"selected\"><div><span>";
				$res.=$label."</span></div>";
			}
			$res.="</li>\n";
		}


		$res.="</ul>\n";
		$res.='<div class="detail_tab_box">';

		$show_price =FALSE;
		if ((!$GLOBALS["current_user"]->isAnonymous()) && ((int)str_replace(",", ".", $info["price"]) > 0)) {
			$show_price =TRUE;
		}

		$top_links ="";
		if (($show_price) && ($info["can_add_to_cart"] == 1)) {
			$top_links.='<a href="'.$um->getUrl("op=addtocart&prd_id=".$prd_id).'">';
			$top_links.=$this->lang->def("_ADD_TO_CART");
			$top_links.='</a>';
		}

		if (!empty($top_links)) {
			$res.='<div class="detail_top_link">';
			$res.=$top_links;
			$res.='</div>';
		}

		if ($tab_op == "main") {
			$res.=$this->showProductMainTab($info, $language);
		}
		else {
			$fieldgrp_id =(int)substr($tab_op, 4);
			$res.=$this->showProductTab($prd_id, $fieldgrp_id);
		}

		$res.='</div>'; //detail_tab_box
		return $res;
	}


	function showProductMainTab($info, $language) {
		$res ="";

		if (!empty($info["image"])) {
			$res.='<div class="detail_prd_image">';
			$name =$info["title"][$language];
			$res.='<img src="'._CATALOG_FPATH.$info["image"].'" alt="'.$name.'" title="'.$name.'" />';
			$res.='</div>';
		}

		$res.='<div class="detail_prd_description">';
		$description =(isset($info["description"][$language]) ? $info["description"][$language] : "");
		$res.=$description;
		$res.='</div>'; // detail_prd_description

		$res.='<div class="detail_prd_otherimg">';
		$res.=$this->getOtherImages($info["prd_id"], 0, 3);
		$res.='</div>'; // detail_prd_otherimg

		$bottom_details ="";
		if ((!$GLOBALS["current_user"]->isAnonymous()) && ((int)str_replace(",", ".", $info["price"]) > 0)) {
			$bottom_details.=$this->lang->def("_PRICE").": ".number_format($info["price"], 2, ',', '')." ";
			$bottom_details.=$this->lang->def("_PRICE_CURRENCY");
		}

		$res.='<div class="no_float"></div>';
		if (!empty($bottom_details)) {
			$res.='<div class="detail_bottom_link">';
			$res.=$bottom_details;
			$res.='</div>';
		}

		return $res;
	}


	function getOtherImages($prd_id, $ini, $vis_item) {
		$res ="";

		$data_info =$this->catalogManager->getProductImagesList($prd_id, $ini, $vis_item);
		$data_arr =$data_info["data_arr"];
		$db_tot =$data_info["data_tot"];

		$res.='<div class="prd_other_images">';

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id =$data_arr[$i]["img_id"];
			$title =$data_arr[$i]["title"];
			$image =$data_arr[$i]["image"];

			$res.='<img src="'._CATALOG_PPATH.$image.'" alt="'.$title.'" title="'.$title.'" />';

		}


		$res.="</div>\n";
		return $res;
	}


	function showProductTab($prd_id, $fieldgrp_id) {
		$res ="";

		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		require_once($GLOBALS["where_ecom"]."/lib/lib.paramset.php");

		$acl=& $GLOBALS["current_user"]->getAcl();
		$acl_manager=& $GLOBALS["current_user"]->getAclManager();

		$fl = new FieldList();
		$psm =new ParamSetManager();

		$fl->setFieldTable($GLOBALS["prefix_ecom"]."_product_field");
		$fl->setFieldEntryTable($GLOBALS["prefix_ecom"]."_product_field_entry");
		$fl->setUseMultiLang(TRUE);

		$all_fields=$fl->getAllFields(array("ecom")); //print_r($all_fields);

		//$field_list =$fl->getFieldsFromIdst(array($prd_id), FALSE);
		$grp_items =$psm->getGroupItemFieldList($fieldgrp_id);
		$field_list =$fl->getFieldsFromArray($grp_items);

		$user_field_arr=$fl->showFieldForUserArr(array($prd_id), $grp_items);

		if (is_array($user_field_arr[$prd_id]))
	 		$field_val=$user_field_arr[$prd_id];
		else
			$field_val=array();

		foreach ($field_val as $field_id=>$value) {
			$res.="<p><b>".$field_list[$field_id][FIELD_INFO_TRANSLATION].":</b> ".$value."</p>\n";
		}

		return $res;
	}


	function showMiniCartSummary() {
		$res ="";
		require_once($GLOBALS['where_ecom'].'/lib/lib.cart.php');

		$um=& UrlManager::getInstance();
		$cart =& Cart::createInstance();

		$res.='<div class="mini_cart_box">';

		$cart->setUseQuantities(TRUE);
		$cart->loadCart();

		if ($cart->isEmpty()) {
			$res.=getInfoUi($this->lang->def("_CART_IS_EMPTY"));
		}
		else {
			$items =$cart->getCartItems();

			$res.='<ul class="cart_summary">';
			foreach($items as $cart_item) {
				$res.='<li>';
				$res.=$cart_item["descriptor"];
				$res.='</li>';
			}
			$res.="</ul>\n";

			$res.='<div class="mini_cart_checkout">';
			$res.='<a href="'.$um->getUrl("op=showcart").'">';
			$res.=$this->lang->def("_CART_CHECKOUT");
			$res.='</a>';
			$res.="</div>\n"; // mini_cart_checkout
		}
		$res.="</div>\n"; // mini_cart_box

		return $res;
	}


	function addToCart($prd_id, $cat_id) {
		$um=& UrlManager::getInstance();

		$this->catalogManager->addToCart($prd_id);

		$url=$um->getUrl("op=product&cat_id=".(int)$cat_id."&prd_id=".(int)$prd_id);
		jumpTo($url);
	}


	function showCartSummary() {
		$res ="";
		require_once($GLOBALS['where_ecom'].'/lib/lib.cart.php');

		$um=& UrlManager::getInstance();
		$cart =& Cart::createInstance();

		$cart->setUseQuantities(TRUE);

		$stop =TRUE;
		if(isset($_POST['update_cart']))
			$this->updateCart();
		else if(isset($_POST['payment_method_select']))
			$res =$this->paymentMethodSelect();
		else if(isset($_POST['company_billing_info']))
			$res =$this->companyBillingInfo();
		else if(isset($_POST['invoice_info']))
			$res =$this->invoiceInfo();
		else if(isset($_POST['back_to_list']))
			jumpTo($um->getUrl());
		else if(isset($_POST['del_item']))
			$this->updateCart();
		else if(isset($_POST['confirm_buy']))
			$res =$this->confirmBuy();
		else if(isset($_POST['save_order']))
			$res =$this->saveOrder();
		else {
			$stop =FALSE;
		}

		if ($stop) {
			return $res;
		}


		$tax_zone_arr=$cart->getTaxZoneDropdownArr();

		$tax_zone=FALSE;
		$sel_tax_zone=FALSE;
		if (isset($_GET["updated"])) {
			$tax_zone =$_SESSION["cart_tax_zone"];
		}
		else if (isset($_POST["tax_zone"])) {
			$tax_zone=$_POST["tax_zone"];
			$_SESSION["cart_tax_zone"]=$tax_zone;
		}
		else if (isset($_SESSION["cart_tax_zone"])) {
			$sel_tax_zone =$_SESSION["cart_tax_zone"];
		}

		if (($tax_zone !== FALSE) || count($tax_zone_arr) == 1) {

			if ($tax_zone === FALSE)
				list($tax_zone)=$tax_zone_arr;

			require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
			$res.=Form::openForm('cart',$um->getUrl("op=showcart"));

			$res.=$cart->getCart(FALSE, $tax_zone);

			$res.=Form::getHidden('tax_zone', 'tax_zone', $tax_zone);

			$res.=Form::openButtonSpace();
			$res.=Form::getButton('update_cart', 'update_cart', $this->lang->def('_UPDATE_QUANTITIES'));
			if (!$cart->isEmpty()) {
				$res.=Form::getButton('invoice_info', 'invoice_info', $this->lang->def('_NEXT_STEP'));
			}
			$res.=Form::closeButtonSpace();
			$res.=Form::closeForm();
		}
		else {
			$res.=$this->selectTaxZone($tax_zone_arr, $sel_tax_zone);
		}

		return $res;
	}


	function selectTaxZone($tax_zone_arr, $sel_tax_zone=FALSE) {
		$res ="";
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

		$um=& UrlManager::getInstance();

		$res.=Form::openForm('cart',$um->getUrl("op=showcart"));

		$res.=Form::getDropdown($this->lang->def('_TAX_ZONE'), 'tax_zone', 'tax_zone', $tax_zone_arr, $sel_tax_zone);

		$res.=Form::openButtonSpace();
		$res.=Form::getButton('go_cart', 'go_cart', $this->lang->def('_CONTINUE'));
		$res.=Form::closeButtonSpace();
		$res.=Form::closeForm();

		return $res;
	}


	function updateCart() {
		require_once($GLOBALS['where_ecom'].'/lib/lib.cart.php');
		$um=& UrlManager::getInstance();
		$cart =& Cart::createInstance();

		$go_to_cart =FALSE;

		if (!empty($_POST['del_item']))
		{
			foreach ($_POST['del_item'] as $id_item => $quantity)
			{
				$cart->updateQuantityItem($id_item, 0);
			}
			$cart->saveCart();
			$go_to_cart=TRUE;
		}

		if (!empty($_POST['item']))
		{
			foreach ($_POST['item'] as $id_item => $quantity)
			{
				$cart->updateQuantityItem($id_item,$quantity);
			}
			$cart->saveCart();
		}
		if(($go_to_cart) || (isset($_POST['update_cart']))){
			jumpTo($um->getUrl("op=showcart&updated=1"));
		}
		else {
			jumpTo($um->getUrl());
		}
	}


	function paymentMethodSelect() {
		$res ="";
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
		require_once($GLOBALS['where_ecom'].'/modules/payment/class.payment.php');

		$um=& UrlManager::getInstance();
		$payment= new Payment();

		//$out->add(getTitleArea($lang->def('_TITLE_CART'), 'cart_summary', $this->lang->def('_ALT_CART'))	.'<div class="std_block">');
		$res.=getBackUi($um->getUrl("op=showcart"), $this->lang->def('_BACK'));
		$res.="<h3>".$this->lang->def("_SEL_PAY_METHOD").":</h3>";
		$res.=Form::openForm('payment',$um->getUrl("op=showcart"));
		$res.=$payment->getFormSelection();
		$res.=Form::openButtonSpace();
		$res.=Form::getButton('invoice_info', 'invoice_info', $this->lang->def('_BACK'));
		$res.=Form::getButton('confirm_buy', 'confirm_buy', $this->lang->def('_SUMMARY'));
		$res.=Form::closeButtonSpace();
		//.Form::closeForm().'</div>');

		return $res;
	}


	function invoiceInfo() {
		$res ="";
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.company.php');

		$um=& UrlManager::getInstance();
		$company= new CoreCompanyManager();

		$form = new Form();
		//$out->add(getTitleArea($lang->def('_TITLE_INVOICE_INFO'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');
		$res.=$form->openForm('invoice_info',$um->getUrl("op=showcart"));
		$company_for_user=$company->getUserCompanies(getLogUserID());
		if ((is_array($company_for_user)) && (count($company_for_user) > 0)){
		$res.=$form->getRadio($this->lang->def('_USE_DATE_ASSOCIATED_COMPANY'), 'invoice_associated_company', 'invoice_mode', 'company', true );
			if ((count($company_for_user))>1){

				foreach ($company_for_user as $key => $value) {
					$company_info=$company->getCompanyInfo($value);
					$id_company=$company_info['company_id'];
					$companies[$id_company]=$company_info['name'];

				}
				$res.=$form->getDropdown($this->lang->def('_COMPANY_NAME'),'company_to_associate','company_to_associate',$companies);
			} else {
				$company_for_user=$company->getCompanyInfo($company_for_user[0]);
				$res.='<div class="form_line_l">'.$this->lang->def('_COMPANY_NAME').$form->getHidden('company_to_associate','company_to_associate',$company_for_user['company_id']);
				$res.=$company_for_user['name'].'</div>';
			}
		}



		$res.=$form->getRadio($this->lang->def('_USE_CODE_TO_ASSOCIATE'), 'invoice_use_code_to_associate', 'invoice_mode', 'company_code');
		$res.=$form->getTextfield($company->getCompanyIdrefCodeName(),'company_code','company_code','255','','','','');
		$res.=$form->getRadio($this->lang->def('_INSERT_NEW_COMPANY'), 'new_company', 'invoice_mode', 'new_company');
		$res.=$form->getHidden('tax_zone', 'tax_zone', $_SESSION["cart_tax_zone"]);
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('go_cart', 'go_cart', $this->lang->def('_BACK'));
		$res.=$form->getButton('company_billing_info', 'company_billing_info', $this->lang->def('_NEXT_STEP'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();
		//.'</div>');

		return $res;
	}


	function companyBillingInfo() {
		$res ="";
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.company.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');

		$can_go_on =TRUE;

		$um=& UrlManager::getInstance();
		$company= new CoreCompanyManager();
		$form = new Form();
		$tb	= new TypeOne('', $this->lang->def('_INVOICE_INFO_CAPTION'), $this->lang->def('_INVOICE_INFO_SUMMARY'));
		//$res.=getTitleArea($this->lang->def('_TITLE_INVOICE_INFO'), 'cart_summary', $this->lang->def('_ALT_CART'))	.'<div class="std_block">';
		$res.=getBackUi($um->getUrl("op=showcart"), $this->lang->def('_BACK'));
		$res.=$form->openForm('invoice_info',$um->getUrl("op=showcart"));

		$billing_valid_info=array('name','address','tel','email','vat_number');
		if (isset($_POST['invoice_mode']))
			$invoice_mode=$_POST['invoice_mode'];
		else if (isset($_GET['invoice_mode']))
			$invoice_mode=$_GET['invoice_mode'];
		else
			return FALSE;
		switch($invoice_mode){
			// retrieve data of company in db
			case 'company' : {

				if (isset($_POST['company_to_associate']))
					$company_id=$_POST['company_to_associate'];
				else if (isset($_GET['company_id']))
					$company_id=$_GET['company_id'];
				else
					return FALSE;

				$billing_info=$company->getCompanyInfo($company_id);
			}break;
			case 'company_code' : {

				if (isset($_POST['company_code']))
					$company_code=$_POST['company_code'];
				else if (isset($_GET['company_code']))
					$company_code=$_GET['company_code'];
				else
					return FALSE;

				$billing_info=$company->getCompanyFromIdrefCode($company_code);
			}break;
			case 'new_company' : {
				jumpTo($um->getUrl("op=new_company"));
				die();
			}break;
		}
		if ((isset($billing_info)) && ($billing_info !== FALSE)) {
			$type_h = array('image','image');
			$cont_h	= array('','');
			$tb->setColsStyle($type_h);
			$tb->addHead($cont_h);
			foreach ($billing_info as $key => $value) {

				if(in_array($key,$billing_valid_info)==TRUE ){
					$tb_billing_info=array();
					$tb_billing_info[]=$this->lang->def("_COMPANY_INFO_".strtoupper($key));
					$tb_billing_info[]=$value;
					$res.=$tb->addBody($tb_billing_info);
				}
			}
			$_SESSION["cart_billing_info"]=$billing_info;
		}
		else {
			$can_go_on =FALSE;
			$res.=getErrorUi($this->lang->def('_NO_INVOICE_INFO'));
		}


		$res.=$tb->getTable();
		$form = new Form();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('invoice_info', 'invoice_info', $this->lang->def('_BACK'));
		if ($can_go_on) {
			$res.=$form->getButton('payment_method_select', 'payment_method_select', $this->lang->def('_NEXT_STEP'));
		}
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();
		//.'</div>');

		return $res;
	}


	function confirmBuy() {
		$res ="";
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS["where_ecom"]."/lib/lib.cart.php");

		$um=& UrlManager::getInstance();
		$cart=& Cart::createInstance();

		// $out->add(getTitleArea($lang->def('_TITLE_CART'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');
		$res.=getBackUi($um->getUrl("op=showcart"), $this->lang->def('_BACK'));
		$res.="<h3>".$this->lang->def("_CONFIRM_BUY").":</h3>";

		$code=$cart->getCart(FALSE, $_SESSION["cart_tax_zone"]);
		$res.=$code;

		$res.=Form::openForm('payment', $um->getUrl("op=showcart"));
		$res.=Form::getHidden('paymod', 'paymod', $_POST["paymod"]);
		$res.=Form::openButtonSpace();
		$res.=Form::getButton('undo', 'undo', $this->lang->def('_CANCEL_BUY'));
		$res.=Form::getButton('save_order', 'save_order', $this->lang->def('_CONFIRM_BUY'));
		$res.=Form::closeButtonSpace();
		$res.=Form::closeForm();
		// '</div>'

		return $res;
	}


	function saveOrder() {
		$res ="";
		require_once($GLOBALS['where_ecom'].'/modules/payment/class.payment.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_ecom'].'/lib/lib.cart.php');
		$um=& UrlManager::getInstance();
		$cart =& Cart::createInstance();

		if (isset($_POST["undo"])) {
			jumpTo($um->getUrl("op=showcart"));
		}

		$payment= new Payment();
		$valid_paymod=$payment->getActivePayment();
		if (in_array($_POST['paymod'], $valid_paymod))
		require_once($GLOBALS['where_ecom'].'/modules/payment/'.$_POST['paymod'].'.php');


		//$out->add(getTitleArea($lang->def('_TITLE_CART'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');

		$default_payment_status=$payment->getDefaultStatus("payment");
		$default_order_status=$payment->getDefaultStatus("order");

		switch($_POST['paymod']){

			case "wire_transfer" : {
				$payment_info = getWireTransferInfo();
				$payment_status=$default_payment_status;
				$order_status=$default_order_status;
			};break;

			case "mark" : {
				$payment_info = getMarkInfo();
				$payment_status=$default_payment_status;
				$order_status=$default_order_status;
			};break;
			case "check" : {
				$payment_info = getCheckInfo();
				$payment_status=$default_payment_status;
				$order_status=$default_order_status;
			};break;

			case "money_order" : {
				$payment_info = getMoneyOrderInfo();
				$payment_status=$default_payment_status;
				$order_status=$default_order_status;
			};break;
			case "paypal" : {

			};break;
		}
		$company_id=$_SESSION["cart_billing_info"]["company_id"];
		$payment->saveTransaction($company_id, $cart->getTotalAmount(),$order_status,$payment_status,$_POST['paymod'],$cart->array_item);
		foreach($payment_info as $key => $value ) {
			$res.="<b>".$this->lang->def($key)."</b>:&nbsp;".$value."<br />";
		}
		$total_amount=$cart->getTotalAmount();
		$cart->emptyCart();
		//$out->add('</div>');

		return $res;
	}


	function createNewCompany() {
		$res ="";
		require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

		//$out->add(getTitleArea($lang->def('_TITLE_INVOICE_INFO'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');

		$cm =new CompanyManager();
		$cca =new CoreCompanyAdmin();

		require_once($GLOBALS["where_framework"]."/lib/lib.urlmanager.php");
		$um =& UrlManager::getInstance();

		$res.=$cca->getAddEditForm(0, $cm, array(), "savecompany");

		return $res;
	}


	function saveCompany() {
		require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
		require_once($GLOBALS['where_framework'].'/lib/lib.company.php');

		$um =& UrlManager::getInstance();

		if (!isset($_POST["undo"])) {
			$cm =new CompanyManager();
			$ccm =new CoreCompanyManager();

			$company_id =$cm->saveData($_POST);
			$ccm->addToCompanyUsers($company_id, $GLOBALS["current_user"]->getIdSt());

			$url =$um->getUrl("op=company_billing_info&invoice_mode=company&company_id=".$company_id);
		}
		else {
			$url =$um->getUrl("op=showcart");
		}

		jumpTo($url);
	}


}




Class CatalogManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $product_info=NULL;


	function CatalogManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_ecom"]);
		$this->dbconn=$dbconn;
	}


	function _query( $query ) {
		if( $GLOBALS['do_debug'] == 'on' && isset($GLOBALS['page']) )  $GLOBALS['page']->add( "\n<!-- debug $query -->", 'debug' );
		else echo "\n<!-- debug $query -->";
		if( $this->dbconn === NULL )
			$rs = mysql_query( $query );
		else
			$rs = mysql_query( $query, $this->dbconn );
		return $rs;
	}


	function _insQuery( $query ) {
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


	function _getProductTable() {
		return $this->prefix."_product";
	}

	function getProductInfoTable() {
		return $this->prefix."_product_info";
	}

	function getCategoryTable() {
		return $this->prefix."_product_cat";
	}


	function getCategoryInfoTable() {
		return $this->prefix."_product_cat_info";
	}


	function getCategoryItemsTable() {
		return $this->prefix."_product_cat_item";
	}


	function getProductImagesTable() {
		return $this->prefix."_product_img";
	}


	function getProductList($cat_id, $language, $ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info =array();
		$data_info["data_arr"]=array();

		$fields="t1.prd_id, t2.cat_id, t1.prd_code, info.title, info.description, t1.price, t1.image";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getProductTable()." as t1 ";
		$qtxt.="LEFT JOIN ".$this->getProductInfoTable()." as info ";
		$qtxt.="ON (t1.prd_id=info.prd_id AND info.language='".$language."'), ";
		$qtxt.=$this->getCategoryItemsTable()." as t2 ";
		$qtxt.="WHERE t1.prd_id=t2.prd_id AND t2.cat_id='".(int)$cat_id."' ";

		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		//$qtxt.="ORDER BY info.title ";
		$qtxt.="ORDER BY t1.ord, t1.prd_id ";
		$q =$this->_query($qtxt);

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$qtxt.="LIMIT ".$ini.",".$vis_item;
			$q=$this->_query($qtxt);
		}

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_assoc($q)) {

				$id =$row["prd_id"];
				$data_info["data_arr"][$i]=$row;
				$this->product_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadProductInfo($id) {
		$res=array();

		$fields="t1.*, info.language, info.title, info.description";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getProductTable()." as t1 ";
		$qtxt.="LEFT JOIN ".$this->getProductInfoTable()." as info ";
		$qtxt.="ON (t1.prd_id=info.prd_id) ";
		$qtxt.="WHERE t1.prd_id='".(int)$id."'";
		$q=$this->_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {
				if (empty($res)) {
					$res =$row;
				}
				$lang =$row["language"];
				if (!empty($lang)) {
					if (!is_array($res["title"])) {
						$res["title"]=array();
					}
					if (!is_array($res["description"])) {
						$res["description"]=array();
					}
					$res["title"][$lang]=$row["title"];
					$res["description"][$lang]=$row["description"];
				}
			}
		}

		return $res;
	}


	function getProductInfo($id) {

		if (!isset($this->product_info[$id])) {
			$info=$this->loadProductInfo($id);
			$this->product_info[$id]=$info;
		}

		return $this->product_info[$id];
	}


	function getItemCategories($prd_id) {
		$res =array();

		$qtxt ="SELECT * FROM ".$this->getCategoryItemsTable()." WHERE ";
		$qtxt.="prd_id='".(int)$prd_id."'";

		$q =$this->_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_assoc($q)) {
				$res[]=$row["cat_id"];
			}
		}

		return $res;
	}


	function saveProduct($data) {

		require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.multimedia.php');

		$prd_id =(int)$data["id"];
		$prd_code =$data["prd_code"];
		$title =(array)$data["title"];
		$description =(array)$data["description"];
		$price =$data["price"];
		$param_set_id =(int)$data["param_set_id"];
		$can_add_to_cart =(int)$data["can_add_to_cart"];


		sl_open_fileoperations();

		$deleted =FALSE;
		if ((isset($data["file_to_del"])) && (is_array($data["file_to_del"]))) {
			foreach($data["file_to_del"] as $name=>$file_name) {
				if ($name == "image") {
					$deleted =TRUE;
				}
				sl_unlink(_CATALOG_FPATH_INTERNAL.$file_name);
				sl_unlink(_CATALOG_PPATH_INTERNAL.$file_name);
			}
		}


		if ((is_array($_FILES)) && (!empty($_FILES))) {
			$fname=$_FILES["image"]["name"];

			if (!empty($fname)) {
				$time =time();
				$real_fname =substr($time, 0, 5)."_".substr($time, 5)."_".rand(10,99)."_".$fname;
				$tmp_fname =$_FILES["image"]["tmp_name"];

				$f1 =sl_upload($tmp_fname, _CATALOG_FPATH_INTERNAL.$real_fname);

				$max_size =100;
				$fpreview =$real_fname;
				$preview =createPreview(_CATALOG_FPATH, _CATALOG_PPATH, $fpreview, $max_size, $max_size);

				if ((isset($data["old_image"])) && (!empty($data["old_image"]))) {
					sl_unlink(_CATALOG_FPATH_INTERNAL.$data["old_image"]);
					sl_unlink(_CATALOG_PPATH_INTERNAL.$data["old_image"]);
				}
			}
		}

		sl_close_fileoperations();

		if (isset($real_fname)) {
			$image =$real_fname;
		}
		else {
			$image ="";
		}


		if ($prd_id < 1) {

			$cat_id =(int)$data["cat_id"];

			$field_list ="prd_code, price, param_set_id, can_add_to_cart";
			$field_val ="'".$prd_code."', '".$price."', '".$param_set_id."', '".$can_add_to_cart."'";

			if (!empty($image)) {
				$field_list.=", image";
				$field_val.=", '".$image."'";
			}

			$qtxt="INSERT INTO ".$this->_getProductTable()." (".$field_list.") VALUES(".$field_val.")";
			$res =$this->_insQuery($qtxt);

			// Add it to the category items table
			$field_list ="cat_id, prd_id";
			$field_val="'".$cat_id."', '".$res."'";
			$qtxt="INSERT INTO ".$this->getCategoryItemsTable()." (".$field_list.") VALUES(".$field_val.")";
			$q =$this->_query($qtxt);
		}
		else {

			$qtxt ="UPDATE ".$this->_getProductTable()." SET prd_code='".$prd_code."', ";
			$qtxt.="price='".$price."', param_set_id='".$param_set_id."', ";
			$qtxt.="can_add_to_cart='".$can_add_to_cart."'";

			if ((!empty($image)) || ($deleted)) {
				$qtxt.=", image='".$image."' ";
			}
			else {
				$qtxt.=" ";
			}

			$qtxt.="WHERE prd_id='".$prd_id."'";
			$q=$this->_query($qtxt);

			$res=$prd_id;
		}

		if ($res > 0) {
			$qtxt ="DELETE FROM ".$this->getProductInfoTable()." WHERE prd_id='".$res."'";
			$q =$this->_query($qtxt);

			$ins_arr =array();
			$larr=$GLOBALS['globLangManager']->getAllLangCode();
			foreach ($larr as $key=>$val) {
				$ins_arr[]="('".$res."', '".$val."', '".$title[$val]."', '".$description[$val]."')";
			}

			if (count($ins_arr) > 0) {
				$qtxt ="INSERT INTO ".$this->getProductInfoTable()." (prd_id, language, title, description) ";
				$qtxt.="VALUES ".implode(",", $ins_arr);
				$q =$this->_query($qtxt);
			}
		}


		// Saving custom fields
		require_once($GLOBALS["where_ecom"]."/lib/lib.paramset.php");
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		$psm =new ParamSetManager();
		$grp_items =$psm->getGroupItemFieldList(FALSE, $param_set_id);

		$fl = new FieldList();
		$fl->setFieldTable($GLOBALS["prefix_ecom"]."_product_field");
		$fl->setFieldEntryTable($GLOBALS["prefix_ecom"]."_product_field_entry");
		$fl->setUseMultiLang(TRUE);
		$filled_val =$fl->getFilledSpecVal($grp_items);
		$arr_fields=array();
		foreach($filled_val as $field_id=>$val) {
			$arr_fields[$field_id]=$val["value"];
		}
		$fl->storeDirectFieldsForUser($res, $arr_fields);


		return $res;
	}


	function deleteProduct($prd_id) {
		$prd_id =(int)$prd_id;
			/*
			ecom_product_info
			*/

		// Deleting product images
		$data_info =$this->getProductImagesList($prd_id);
		$data_arr =$data_info["data_arr"];
		$db_tot =$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {
			$img_id=$data_arr[$i]["img_id"];
			$this->deleteProductImage($img_id);
		}

		// Delete product from category items "index" (table)
		$qtxt ="DELETE FROM ".$this->getCategoryItemsTable()." WHERE prd_id='".$prd_id."'";
		$q =mysql_query($qtxt);

		// Delete product data field entries table
		$qtxt ="DELETE FROM ".$GLOBALS["prefix_ecom"]."_product_field_entry WHERE id_user='".$prd_id."'";
		$q =mysql_query($qtxt);

		// Delete product information
		$qtxt ="DELETE FROM ".$this->getProductInfoTable()." WHERE prd_id='".$prd_id."'";
		$q =mysql_query($qtxt);

		// And finally the product itself..
		$qtxt ="DELETE FROM ".$this->_getProductTable()." WHERE prd_id='".$prd_id."' LIMIT 1";
		$q =mysql_query($qtxt);
	}


	function saveItemCategories($prd_id, $checked_items) {
		$ins_q =TRUE;
		$del_q =TRUE;

		$items_in_db =$this->getItemCategories($prd_id);

		$to_add =array_diff($checked_items, $items_in_db);
		$to_rem =array_diff($items_in_db, $checked_items);


		if (count($to_add) > 0) {
			$qtxt ="INSERT INTO ".$this->getCategoryItemsTable()." (cat_id, prd_id) ";
			$to_add_arr ="";
			foreach ($to_add as $cat_id) {
				$to_add_arr[]="('".(int)$cat_id."', '".(int)$prd_id."')";
			}
			$qtxt.="VALUES ".implode(",", $to_add_arr);

			$ins_q =$this->_query($qtxt);
		}

		if (count($to_rem) > 0) {
			$qtxt ="DELETE FROM ".$this->getCategoryItemsTable()." WHERE ";
			$qtxt.="prd_id='".(int)$prd_id."' AND cat_id IN (".implode(",", $to_rem).")";

			$del_q =$this->_query($qtxt);
		}

		$res =(($ins_q && $del_q) ? TRUE : FALSE);
		return $res;
	}


	function getProductImagesList($prd_id, $ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info =array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->getProductImagesTable()." ";
		$qtxt.="WHERE prd_id='".(int)$prd_id."' ";

		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		$qtxt.="ORDER BY ord ";
		$q =$this->_query($qtxt);

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		if (($ini !== FALSE) && ($vis_item !== FALSE)) {
			$qtxt.="LIMIT ".$ini.",".$vis_item;
			$q=$this->_query($qtxt);
		}

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_assoc($q)) {

				$id =$row["prd_id"];
				$data_info["data_arr"][$i]=$row;
				$this->image_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadProductImageInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->getProductImagesTable()." ";
		$qtxt.="WHERE img_id='".(int)$id."'";
		$q=$this->_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_assoc($q);
		}

		return $res;
	}


	function getProductImageInfo($id) {

		if (!isset($this->image_info[$id])) {
			$info=$this->loadProductImageInfo($id);
			$this->image_info[$id]=$info;
		}

		return $this->image_info[$id];
	}


	function getLastOrd($table) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");
		return utilGetLastOrd($table, "ord");
	}


	function moveImage($direction, $id_val) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

		$table =$this->getProductImagesTable();

		utilMoveItem($direction, $table, "img_id", $id_val, "ord");
	}


	function saveProductImage($data, $prd_id) {

		require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.multimedia.php');

		$prd_id =(int)$prd_id;
		$img_id =(int)$data["img_id"];
		$title =$data["title"];
		$description =$data["description"];
		$deleted =FALSE;

		sl_open_fileoperations();

		if ((isset($data["file_to_del"])) && (is_array($data["file_to_del"]))) {
			foreach($data["file_to_del"] as $name=>$file_name) {
				if ($name == "image") {
					$deleted =TRUE;
				}
				sl_unlink(_CATALOG_FPATH_INTERNAL.$file_name);
				sl_unlink(_CATALOG_PPATH_INTERNAL.$file_name);
			}
		}

//print_r($_POST); print_r($_FILES); die();
		if ((is_array($_FILES)) && (!empty($_FILES))) {
			$fname=$_FILES["image"]["name"];

			if (!empty($fname)) {
				$time =time();
				$real_fname =substr($time, 0, 5)."_".substr($time, 5)."_".rand(10,99)."_".$fname;
				$tmp_fname =$_FILES["image"]["tmp_name"];

				$f1 =sl_upload($tmp_fname, _CATALOG_FPATH_INTERNAL.$real_fname);

				$max_size =100;
				$fpreview =$real_fname;
				$preview =createPreview(_CATALOG_FPATH, _CATALOG_PPATH, $fpreview, $max_size, $max_size);

				if ((isset($data["old_image"])) && (!empty($data["old_image"]))) {
					sl_unlink(_CATALOG_FPATH_INTERNAL.$data["old_image"]);
					sl_unlink(_CATALOG_PPATH_INTERNAL.$data["old_image"]);
				}
			}
		}
//die();

		if (isset($real_fname)) {
			$image =$real_fname;
		}
		else {
			$image ="";
		}

		if ($img_id < 1) {

			$ord =$this->getLastOrd($this->getProductImagesTable())+1;

			$field_list ="prd_id, image, title, description, ord";
			$field_val="'".$prd_id."', '".$image."', '".$title."', '".$description."', '".$ord."'";

			$qtxt="INSERT INTO ".$this->getProductImagesTable()." (".$field_list.") VALUES(".$field_val.")";
			$res =$this->_insQuery($qtxt);
		}
		else {

			$qtxt ="UPDATE ".$this->getProductImagesTable()." SET ";
			if ((!empty($image)) || ($deleted)) {
				$qtxt.="image='".$image."', ";
			}
			if (empty($image)) {
				$qtxt.="published='0', ";
			}
			$qtxt.="title='".$title."', description='".$description."' ";
			$qtxt.="WHERE img_id='".$img_id."'";
			$q=$this->_query($qtxt);

			$res=$img_id;
		}

		sl_close_fileoperations();

		return $res;
	}


	function switchImgPublish($img_id) {
		$img_id =(int)$img_id;

		$info =$this->getProductImageInfo($img_id);
		$new_status =($info["published"] > 0 ? 0 : 1);

		$qtxt ="UPDATE ".$this->getProductImagesTable()." SET ";
		$qtxt.="published='".$new_status."' ";
		$qtxt.="WHERE img_id='".$img_id."'";
		$res =$this->_query($qtxt);

		return $res;
	}


	function deleteProductImage($img_id) {
		require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
		$img_id =(int)$img_id;

		$info =$this->getProductImageInfo($img_id);
		$image =$info["image"];

		if (!empty($image)) {
			sl_unlink(_CATALOG_FPATH_INTERNAL.$image);
			sl_unlink(_CATALOG_PPATH_INTERNAL.$image);
		}

		$qtxt ="DELETE FROM ".$this->getProductImagesTable()." ";
		$qtxt.="WHERE img_id='".$img_id."' LIMIT 1";
		$res =$this->_query($qtxt);

		return $res;
	}


	function getCategoryInfo($cat_id, $path=FALSE) {
		$res =array();

		$fields ="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->getCategoryTable()." WHERE ";

		if (($path !== FALSE) && (!empty($path))) {
			$qtxt.="path='".$path."' ";
		}
		else {
			$qtxt.="cat_id='".(int)$cat_id."' ";
		}
		$qtxt.="ORDER path";

		$q =$this->_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_assoc($q);
		}
		else {
			$res["lev"]="0";
			$res["path"]="/root";
			$res["cat_id"]="0";
			$res["image"]="";
		}

		return $res;
	}



	function getCategoryMenuList($path, $lev, $language) {
		$res =array();

		$lev =(int)$lev;

		$fields ="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->getCategoryTable()." as t1,";
		$qtxt.=$this->getCategoryInfoTable()." as t2 WHERE ";
		$qtxt.="t1.cat_id=t2.cat_id AND t2.language='".$language."' AND ";

		if ($lev > 1) {
			$from_path =implode('/', array_slice(explode('/', $path), 0 ,-1));
			$qtxt.="(path='".$from_path."' AND lev='".($lev-1)."') OR ";
			$qtxt.="(path LIKE '".$from_path."/%' AND lev='".$lev."') OR ";
			$qtxt.="(path LIKE '".$path."/%' AND lev='".($lev+1)."') ";
		}
		else if ($lev > 0) {
			$from_path =implode('/', array_slice(explode('/', $path), 0 ,-1));
			$qtxt.="(path LIKE '".$from_path."/%' AND lev='".$lev."') OR ";
			$qtxt.="(path LIKE '".$path."/%' AND lev='".($lev+1)."') ";
		}
		else {
			$qtxt.="path LIKE '".$path."/%' AND lev='1' ";
		}
		$qtxt.="ORDER BY path";

		$q =$this->_query($qtxt);

		if ($q) {
			while($row=mysql_fetch_assoc($q)) {
				$row["name"]=$row["title"];
				$res[]=$row;
			}
		}

		return $res;
	}


	function getSubCategoriesList($parent_id) {
		$res =array();


		$fields ="*, REPLACE(path, ' ', '') as no_space_path";
		$qtxt ="SELECT ".$fields." FROM ".$this->getCategoryTable()." WHERE ";
		$qtxt.="parent_id='".(int)$parent_id."' ";
		$qtxt.="ORDER BY no_space_path";

		$q =$this->_query($qtxt);

		if ($q) {
			while($row=mysql_fetch_assoc($q)) {
				$row["name"]=end(explode('/', $row["path"]));
				$res[]=$row;
			}
		}

		return $res;
	}


	function addToCart($prd_id) {
		require_once($GLOBALS['where_ecom'].'/lib/lib.cart.php');

		$cart =& Cart::createInstance();

		$prd_info =$this->getProductInfo($prd_id);

		$id_item =(int)$prd_id;
		$item_code ="catalog_product";
		$language =getLanguage();
		$description =$prd_info["title"][$language];
		$price =$prd_info["price"];
		$quantity ="1";
		$type ="catalog_product";

		$cart->addItemToCart($item_code, $id_item, $description, $price,$quantity, $type);
		$cart->saveCart();
	}


}






require_once($GLOBALS['where_framework'].'/lib/lib.treedb.php');
require_once($GLOBALS['where_framework'].'/lib/lib.treeview.php');

class TreeDb_CatalogDb extends TreeDb {

	var $info_table="";
	var $language="";


	// Constructor of TreeDb_CatalogDb class
	function TreeDb_CatalogDb($table_name, $info_table, $language) {

		$this->info_table = $info_table;
		$this->language = $language;

		$this->table = $table_name;
		$this->fields = array(
			'id' => 'cat_id',
			'idParent' => 'parent_id',
			'path' => 'path',
			'lev' => 'lev'
		);
	}


	function _getOtherTables() { return " LEFT JOIN ".$this->info_table;	}

	function _getJoinFilter($tname = FALSE) {
		$tname.=(!empty($tname) ? "." : "");

		$res =$tname."cat_id = ".$this->info_table.".cat_id AND ";
		$res.=$this->info_table.".language='".$this->language."'";

		return $res;
	}


	function _getOtherFields($tname = FALSE) {
		$res =", ";
		$tname.=(!empty($tname) ? "." : "");

		$res.=$tname."param_set_id	, ";
		$res.=$tname."image ";

		return $res;
	}


	function _getOtherSelectFields($tname = FALSE) {
		$res =", ";
		$tname.=(!empty($tname) ? "." : "");

		$res.=$this->info_table.".title ";

		return $res;
	}

/*
	function getFoldersCollection( &$arrayId ) {
		$query = "SELECT ".$this->_getDISTINCT(). $this->_getBaseFields('t1') .", count(t2.".$this->fields['id']."), "
				. "REPLACE(t1.path, ' ', '') as no_space_path "
				. $this->_getOtherFields('t1')
				." FROM ". $this->table ." AS t1 LEFT JOIN ". $this->table ." AS t2"
				."		ON (t1.".$this->fields['id']." = t2.".$this->fields['idParent']
				.$this->_getParentJoinFilter( 't1','t2' ).")"
				.$this->_getOtherTables( 't1' )
				.$this->_outJoinFilter( 't1' );
		if( $arrayId === NULL )
			$query .=" WHERE ((1) "
					.$this->_getFilter('t1');
		else
			$query .=" WHERE ((t1.". $this->fields['id'] ." IN (". implode( ',', $arrayId) ."))"
					//."   AND ((t1.".$this->fields['id']." = t2.".$this->fields['idParent'] .")"
					//."    OR  (t1.".$this->fields['id']." = 0 ))"
					.$this->_getFilter('t1');
		$query .=") GROUP BY ". $this->_getBaseFields('t1')
				. $this->_getOtherFields('t1')
				." ORDER BY no_space_path";

		$rs = $this->_executeQuery( $query )
				or $this->_printSQLError( 'getFoldersCollection: '.$query );
		$coll = new FoldersCollection( $this, $rs, TRUE );
		return $coll;
	} */


	function &getRootFolder() {
		$folder = new Folder( $this, array( 0, 0, "/root", 0) );
		return $folder;
	}


	function getFolderSaveValues($update=FALSE) {
		$res ="";
		require_once($GLOBALS["where_framework"]."/lib/lib.multimedia.php");
		require_once($GLOBALS["where_framework"]."/lib/lib.upload.php");


		// ------------------------- Image ---------------------------
		$deleted =FALSE;
		if ((isset($_POST["file_to_del"])) && (is_array($_POST["file_to_del"]))) {
			foreach($_POST["file_to_del"] as $name=>$file_name) {
				if ($name == "image") {
					$deleted =TRUE;
				}
				sl_unlink(_CATALOG_CPATH_INTERNAL.$file_name);
			}
		}

		if ((is_array($_FILES)) && (!empty($_FILES))) {
			$fname=$_FILES["image"]["name"];

			if (!empty($fname)) {
				$time =time();
				$real_fname =substr($time, 0, 5)."_".substr($time, 5)."_".rand(10,99)."_".$fname;
				$tmp_fname =$_FILES["image"]["tmp_name"];
				$new_file_path =_CATALOG_CPATH_INTERNAL.$real_fname;

				$max_size =100;
				$upload =createImageFromTmp($tmp_fname, $new_file_path, $real_fname, $max_size, $max_size);

				if ((isset($_POST["old_image"])) && (!empty($_POST["old_image"]))) {
					sl_unlink(_CATALOG_CPATH_INTERNAL.$_POST["old_image"]);
				}
			}
		}

		// ------------------------- Param. set id ---------------------------
		$param_set_id =(int)$_POST["param_set_id"];

		if ((isset($upload)) && ($upload == 0)) {
			$image =$real_fname;
		}
		else {
			$image ="";
		}

		$res =($update ? "" : ", ");
		$res.=($update ? "param_set_id=" : "")."'".$param_set_id."'";
		if ((!empty($image)) || ($deleted)) {
				$res.=", ".($update ? "image=" : "")."'".$image."'";
		}
		else if (!$update) {
			$res.=", ''";
		}

		$res.=" ";
		return $res;
	}


	function afterAddUpdate($cat_id) {

		// ------------------------- Title ---------------------------
		$larr=$GLOBALS['globLangManager']->getAllLangCode();
		$arr =array();
		foreach ($larr as $key=>$val) {
			$field_id ="folder_title_".$val;
			if (isset($_POST[$field_id])) {
				$arr[$val]=$_POST[$field_id];
			}
		}
		if (count($arr) > 0) {
			$this->saveCategoryTitle($cat_id, $arr);
		}

	}


	function getCategoryTitles($cat_id) {
		$res =array();

		$qtxt ="SELECT language, title FROM ".$this->info_table." WHERE cat_id='".$cat_id."'";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while ($row=mysql_fetch_array($q)) {
				$res[$row["language"]]=$row["title"];
			}
		}

		return $res;
	}


	function saveCategoryTitle($cat_id, $values) {

		$cat_id =(int)$cat_id;

		$db_arr =array();
		$qtxt ="SELECT cat_id, language FROM ".$this->info_table." WHERE cat_id='".$cat_id."'";
		$q=mysql_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while ($row=mysql_fetch_array($q)) {
				$db_arr[]=$row["language"];
			}
		}

		foreach ($values as $key=>$val) {
			if (in_array($key, $db_arr)) { // Aggiorno
				$qtxt ="UPDATE ".$this->info_table." SET title='$val' WHERE cat_id='".$cat_id."' AND language='".$key."'";
			}
			else { // Inserisco
				$qtxt ="INSERT INTO ".$this->info_table." (cat_id, language, title) ";
				$qtxt.="VALUES('".$cat_id."','".$key."','".$val."');";
			}
			$q =mysql_query($qtxt);
			if (!$q) { echo mysql_error(); die(); }
		}

		return $q;
	}


	function _addFolder($idParent, $path, $level) {

		$cat_id =parent::_addFolder($idParent, $path, $level);
		$this->afterAddUpdate($cat_id);

	}


	// Called by TreeDb::_addFolder
	function _getOtherValues() {

		$res =$this->getFolderSaveValues();

		return $res;
	}


	// Called by TreeDb::changeOtherData
	function _getOtherUpdates() {

		$res =$this->getFolderSaveValues(TRUE);

		return $res;
	}


	function renameFolder( &$folder, $newName ) {

		parent::renameFolder( $folder, $newName );
		$this->changeOtherData( $folder );

		$cat_id =(int)$folder->id;
		$this->afterAddUpdate($cat_id);
	}


	function getMaxChildPos( $idFolder ) {
		$query = "SELECT MAX(SUBSTRING_INDEX(path, '/', -1))"
				." FROM ". $this->table
				." WHERE (". $this->fields['idParent'] ." = '". (int)$idFolder ."')"
				.$this->_getFilter();
		$rs = mysql_query( $query )
				or die( "Error [$query] <br />". mysql_error() );
		if( mysql_num_rows( $rs ) == 1 ) {
			list( $result ) = mysql_fetch_row( $rs );
			return $result;
		} else {
			return '00000001';
		}
	}

	function getNewPos( $idFolder ) {
		return substr('00000000' .($this->getMaxChildPos( $idFolder )+1), -8);
	}


}


define("FIELD_TITLE", 0);
define("FIELD_PARAM_SET_ID", 0);
define("FIELD_IMAGE", 1);

class TreeView_CatalogView extends TreeView {

	var $can_add = false;
	var $can_mod = false;
	var $can_del = false;
	var $lang = false;
	var $show_action = true;

	var $hide_inline_action = false;
	var $show_checkbox =FALSE;
	var $checked_items_arr =array();
	var $checked_items_name ="checked_items";

	function  TreeView_CatalogView($tdb, $id, $rootname = 'root') {

		parent::TreeView($tdb, $id, $rootname);
		$this->can_add = true;
		$this->can_mod = true;
		$this->can_del = true;
	}


	function hideInlineAction() {

		$this->hide_inline_action = true;
	}


	function showInlineAction() {

		$this->hide_inline_action = false;
	}


	function showAction() {
		$this->show_action = true;
	}


	function hideAction() {
		$this->show_action = false;
	}


	function setShowCheckbox($val) {
		$this->show_checkbox =(bool)$val;
	}


	function getShowCheckbox() {
		return (bool)$this->show_checkbox;
	}


	function setCheckedItemsName($val) {
		$this->checked_items_name =$val;
	}


	function getCheckedItemsName() {
		return $this->checked_items_name;
	}


	function setCheckedItems($arr) {
		$this->checked_items_arr =(array)$arr;
	}


	function _getAddImage() 						{ return getPathImage('fw').'standard/add.gif'; }
	function _getAddLabel() 						{ return def('_ADD', 'standard'); }
	function _getAddAlt() 							{ return def('_ADD', 'standard'); }
	function canAdd() 								{ return $this->can_add; }

	function _getRenameImage()						{ return getPathImage('fw').'standard/mod.gif'; }
	function _getRenameLabel() 						{ return def('_MOD', 'standard'); }
	function canRename() 							{ return $this->isFolderSelected() && $this->can_mod; }
	function canInlineRename() 						{ return $this->can_mod && !$this->hide_inline_action; }
	function canInlineRenameItem( &$stack, $level ) { return (($level != 0) && $this->can_mod); }

	function _getMoveLabel() 						{ return def('_ALT_MOVE', 'standard'); }
	function canMove()								{ return $this->isFolderSelected() && $this->can_mod; }
	function canInlineMove() 						{ return $this->can_mod && !$this->hide_inline_action; }
	function canInlineMoveItem( &$stack, $level ) 	{ return (($level != 0) && $this->can_mod); }

	function _getDeleteLabel() 						{ return def('_DEL', 'standard'); }
	function canDelete() {
		$info = $this->getSelectedFolderData();
		return ($info['isLeaf'] == 1) && $this->isFolderSelected() && $this->can_del;
	}
	function canInlineDelete() 						{ return $this->can_del && !$this->hide_inline_action; }
	function canInlineDeleteItem( &$stack, $level ) {
		return ( ($stack[$level]['isLeaf'] == 1) && ($level != 0) && $this->can_del );
	}

	function _getMoveTargetLabel()					{ return def('_MOVE_CATEGORY', 'standard').' : '; }
	function _getCancelLabel()						{ return def('_UNDO', 'standard'); }

	function _getOtherActions() {

		if( $this->isFolderSelected() ) {

			return array();
		}
		return array();
	}

	function getFolderPrintName( &$folder ) {
		if( $folder->id == 0 )
			return $this->rootname;
			else {
				$title=$folder->otherValues[FIELD_TITLE];

				if (!empty($title)) {
					$res =$title;
				}
				else {
					$res ="[ ".def("_UNAMED", "admin_catalog", "ecom")." ]";
				}

				return $res;
			}
	}

	function extendedParsing( $arrayState, $arrayExpand, $arrayCompress ) {

		if(!isset($arrayState[$this->id])) return;

		parent::extendedParsing( $arrayState, $arrayExpand, $arrayCompress );
	}


	/* function getImage( &$stack, $currLev, $maxLev ) {
		$res=FALSE;

		if( $currLev == $maxLev ) {
			if (($currLev > 0) && ($stack[$maxLev]['isExpanded'])) {
				if (!$stack[$maxLev]['isLeaf'])
					$res=array("wiki_page", "wiki/page_open.gif", "_PAGE");
			}
			else if (($currLev > 0) && (!$stack[$currLev]['isExpanded'])) {
				$res=array("wiki_page", "wiki/page.gif", "_PAGE");
			}
		}

		if ($res === FALSE)
			return parent::getImage( $stack, $currLev, $maxLev );
		else
			return $res;
	} */


	function getOp() {
		$op =(!empty($this->op) ? $this->op : "display");
		return $op;
	}


	function autoLoad() {
		$res="";
		$op=(!empty($this->op) ? $this->op : "display");
		switch($op) {
			case 'newfolder': {
				$res=$this->loadNewFolder();
			} break;
			case "renamefolder": {
				$res=$this->loadRenameFolder();
			} break;
			case "movefolder": {
				$res=$this->loadMoveFolder();
			} break;
			case "deletefolder": {
				$res=$this->loadDeleteFolder();
			} break;

			default:
			case 'display': {
				$res =$this->load();
				if ($this->show_action) {
					$res.=$this->loadActions();
				}
			} break;
		}
		return $res;
	}

	function printElement(&$stack, $level) {
		$folder_id =$stack[$level]['folder']->id;
		$tree = '<div class="TreeViewRowBase">';
		$id = ($stack[$level]['isExpanded'])?($this->_getCompressActionId()):($this->_getExpandActionId());
		$id .= $folder_id;

		if ($this->getShowCheckbox()) {
			$chk_id =$this->getCheckedItemsName()."_".$folder_id;
			$chk_name =$this->getCheckedItemsName()."[".$folder_id."]";
			$checked =(in_array($folder_id, $this->checked_items_arr) ? ' checked="checked"' : "");

			$tree.='<input type="checkbox" name="'.$chk_name.'" id="'.$chk_id.'" ';
			$tree.='value="'.$folder_id.'"'.$checked.' />';
		}

		for( $i = 0; $i <= $level; $i++ ) {
			list( $classImg, $imgFileName, $imgAlt ) = $this->getImage($stack,$i,$level);
			if( $i != ($level-1) || $stack[$level]['isLeaf'] ) {
				$tree .= '<img src="'.getPathImage('fw').$imgFileName.'" '
						.'class="'.$classImg.'" alt="'.$imgAlt.'" '
						.'title="'.$imgAlt.'" />';
			} else {
				$tree .= '<input type="submit" class="'.$classImg.'" value="'
					.'" name="'. $id .'" id="seq_'. $stack[$level]['idSeq'] .'img" />';
			}
		}
		if( $folder_id == $this->selectedFolder ) {
			$this->selectedFolderData = $stack[$level];
			$classStyle = 'TreeItemSelected';
		} else {
			$classStyle = 'TreeItem';
		}
		$tree .= $this->getPreFolderName($stack[$level]['folder']);
		$tree .= '<input type="submit" class="'.$classStyle.'" value="'
			.$this->getFolderPrintName( $stack[$level]['folder'])
			.'" name="'
			. $this->_getSelectedId().$folder_id
			.'" id="seq_'. $stack[$level]['idSeq'] .'" '
			.$this->getFolderPrintOther($stack[$level]['folder'])
			.' />';
		$tree .= '</div>';
		$tree .= $this->printActions( $stack, $level );
		return $tree."\n";
	}


	function loadNewFolder() {
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
		require_once($GLOBALS["where_ecom"]."/lib/lib.paramset.php");
		$form=new Form();
		$psm =new ParamSetManager();

		$tree = $this->printState();

		$image ="";
		$sel_ps =FALSE;
		$ps_array =$psm->getParamSetArray();

		$tree.=$form->openElementSpace();

		$folder_name =$this->tdb->getNewPos($this->selectedFolder);
		//$tree.=$form->getTextfield($this->_getFolderNameLabel(), $this->_getFolderNameId(), $this->_getFolderNameId(), 255, "");
		$tree.=$form->getHidden($this->_getFolderNameId(), $this->_getFolderNameId(), $folder_name);
		$larr =$GLOBALS['globLangManager']->getAllLangCode();
		foreach ($larr as $key=>$val) {
			$field_id ="folder_title_".$val;
			$tree.=$form->getTextfield($this->_getFolderNameLabel().' ('.$val.')', $field_id, $field_id, 255, "");
		}

		$tree.=$form->getDropdown("param_set_id", "param_set_id", "param_set_id", $ps_array, $sel_ps);
		$tree.=$form->getExtendedFilefield($this->lang->def('_IMAGE'), "image", "image", $image);

		$tree.=$form->closeElementSpace();
		$tree.=$form->openButtonSpace();

		$tree .= ' <img src="'.$this->_getCreateImage().'" alt="'.$this->_getCreateAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getCreateLabel().'"'
			.' name="'.$this->_getCreateFolderId().'" id="'.$this->_getCreateFolderId().'" />';
		$tree .= ' <img src="'.$this->_getCancelImage().'" alt="'.$this->_getCancelAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getCancelLabel().'"'
			.' name="'.$this->_getCancelId().'" id="'.$this->_getCancelId().'" />';

		$tree.=$form->closeButtonSpace();
		return $tree;
	}


	function loadRenameFolder() {
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
		require_once($GLOBALS["where_ecom"]."/lib/lib.paramset.php");
		$form=new Form();
		$psm =new ParamSetManager();

		$tree = $this->printState();
		$tdb = $this->tdb;
		$folder_id =(int)$this->getSelectedFolderId();
		$folder = $tdb->getFolderById( $folder_id );

		$image =$folder->otherValues[FIELD_IMAGE];
		$sel_ps =$folder->otherValues[FIELD_PARAM_SET_ID]; print_r($folder->otherValues);
		$ps_array =$psm->getParamSetArray();

		$tree.=$form->openElementSpace();

		//$tree.=$form->getTextfield($this->_getFolderNameLabel(), $this->_getFolderNameId(), $this->_getFolderNameId(), 255, $this->getFolderPrintName($folder));
		$path_name =end(explode('/', $folder->path));
		$tree.=$form->getHidden($this->_getFolderNameId(), $this->_getFolderNameId(), $path_name);
		$larr =$GLOBALS['globLangManager']->getAllLangCode();
		$lang_values =$this->tdb->getCategoryTitles($folder_id);
		foreach ($larr as $key=>$val) {
			$field_id ="folder_title_".$val;
			$title =(isset($lang_values[$val]) ? $lang_values[$val] : "");
			$tree.=$form->getTextfield($this->_getFolderNameLabel().' ('.$val.')', $field_id, $field_id, 255, $title);
		}

		$tree.=$form->getDropdown("param_set_id", "param_set_id", "param_set_id", $ps_array, $sel_ps);
		$tree.=$form->getExtendedFilefield($this->lang->def('_IMAGE'), "image", "image", $image);

		$tree.=$form->closeElementSpace();
		$tree.=$form->openButtonSpace();

		$tree .= ' <img src="'.$this->_getRenameImage().'" alt="'.$this->_getRenameAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getRenameLabel().'"'
			.' name="'.$this->_getRenameFolderId().'" id="'.$this->_getRenameFolderId().'" />';
		$tree .= ' <img src="'.$this->_getCancelImage().'" alt="'.$this->_getCancelAlt().'" /> '
			.'<input type="submit" class="TreeViewAction" value="'.$this->_getCancelLabel().'"'
			.' name="'.$this->_getCancelId().'" id="'.$this->_getCancelId().'" />';

		$tree.=$form->closeButtonSpace();
		return $tree;
	}


	function beforeDeleteItem( &$folder ) {
		$res =TRUE;
		require_once($GLOBALS["where_framework"]."/lib/lib.upload.php");

		// TODO: check that the folder has no items/products
		// in it; if it has some then please return FALSE.


		$image =$folder->otherValues[FIELD_IMAGE];
		if (!empty($image)) {
			sl_unlink(_CATALOG_CPATH_INTERNAL.$image);
		}

		$folder_id =(int)$folder->id;
		$qtxt ="DELETE FROM ".$this->tdb->info_table." WHERE cat_id='".$folder_id."'";
		$q =$this->tdb->_executeQuery($qtxt);

		return $res;
	}


}


?>
