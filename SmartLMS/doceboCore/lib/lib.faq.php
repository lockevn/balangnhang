<?php
/*************************************************************************/
/* DOCEBO CORE - Framework                                               */
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
 * @package admin-library
 * @subpackage module
 * @version  $Id:$
 */

class CoreFaqAdmin {

	var $lang=NULL;
	var $um=NULL;
	var	$table_style=FALSE;

	var $faqManager=NULL;


	function CoreFaqAdmin() {
		$this->lang =& DoceboLanguage::createInstance('admin_faq', "framework");
		$this->faqManager=new CoreFaqManager();
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


	function getFaqCategoryTable($vis_item) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_TABLE_FAQ_CATEGORY_CAP");
		$table_summary=$this->lang->def("_TABLE_FAQ_CATEGORY_SUM");

		$um=& UrlManager::getInstance();
		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_TITLE"));


		$img ="<img src=\"".getPathImage('fw')."standard/export.gif\" alt=\"".$this->lang->def("_EXPORT")."\" ";
		$img.="title=\"".$this->lang->def("_EXPORT")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$this->lang->def("_ALT_SETPERM")."\" ";
		$img.="title=\"".$this->lang->def("_ALT_SETPERM")."\" />";
		$head[]=$img;
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

		$data_info=$this->faqManager->getCategoryList($ini, $vis_item);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["category_id"];

			$rowcnt=array();
			$rowcnt[]=$data_arr[$i]["title"];


			$img ="<img src=\"".getPathImage('fw')."standard/export.gif\" alt=\"".$this->lang->def("_EXPORT")."\" ";
			$img.="title=\"".$this->lang->def("_EXPORT")."\" />";
			$url=$um->getUrl("op=exportcat&catid=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$this->lang->def("_ALT_SETPERM")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_SETPERM")."\" />";
			$url=$um->getUrl("op=setperm&catid=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/modelem.gif\" alt=\"".$this->lang->def("_ALT_MODITEMS")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_MODITEMS")."\" />";
			$url=$um->getUrl("op=showcat&catid=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$url=$um->getUrl("op=editcat&catid=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
			$img.="title=\"".$this->lang->def("_DEL")."\" />";
			$url=$um->getUrl("op=delcategory&catid=".$id."&conf_del=1");
			$rowcnt[]="<a href=\"".$url."\" title=\"".$this->lang->def('_DEL')." : ".$data_arr[$i]['title']."\">".$img."</a>\n";

			$tab->addBody($rowcnt);
		}

		$url=$um->getUrl("op=addcategory");
		$add_box ="<a class=\"new_element_link_float\" href=\"".$url."\">".$this->lang->def('_ADD')."</a>\n";
		$add_box.="&nbsp;"; // TODO: remove this line and change import image with css background
		$url=$um->getUrl("op=importcat");
		$img ="<img src=\"".getPathImage('fw')."standard/import.gif\" alt=\"".$this->lang->def("_IMPORT")."\" ";
		$img.="title=\"".$this->lang->def("_IMPORT")."\" />";
		$add_box.="<a href=\"".$url."\">".$img.$this->lang->def('_IMPORT')."</a>\n";
		$tab->addActionAdd($add_box);

		$res=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}


	function addeditFaqCategory($id=0) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$form=new Form();
		$form_code="";

		$um=& UrlManager::getInstance();
		$url=$um->getUrl("op=savecategory");

		if ($id == 0) {
			$form_code=$form->openForm("main_form", $url);
			$submit_lbl=$this->lang->def("_INSERT");

			$title="";
			$description="";
		}
		else if ($id > 0) {
			$form_code=$form->openForm("main_form", $url);
			$submit_lbl=$this->lang->def("_SAVE");

			$info=$this->faqManager->getCategoryInfo($id);

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


	function saveCategory() {
		$um=& UrlManager::getInstance();

		$cat_id=$this->faqManager->saveCategory($_POST);

		$url=$um->getUrl();
		jumpTo($url);
	}


	function deleteCategory($cat_id) {
		include_once($GLOBALS['where_framework']."/lib/lib.form.php");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();


		if (isset($_POST["undo"])) {
			jumpTo($back_url);
		}
		else if ( get_req("conf_del", DOTY_INT, false) ) {

			$this->faqManager->deleteCategory($cat_id);

			jumpTo($back_url);
		}
		else {

			$res="";
			$info=$this->faqManager->getCategoryInfo($cat_id);
			$title=$info["title"];

			$form=new Form();

			$url=$um->getUrl("op=delcategory&catid=".$cat_id);
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


	function showCatPerm($cat_id) {
		$res=FALSE;
		require_once($GLOBALS['where_cms']."/lib/lib.simplesel.php");

		$um=& UrlManager::getInstance();
		$ssel=new SimpleSelector(TRUE, $this->lang);

		$perm=array();

		$perm["add"]["img"]=getPathImage('fw')."standard/add.gif";
		$perm["add"]["alt"]=$this->lang->def("_ADD");
		$perm["edit"]["img"]=getPathImage('fw')."standard/mod.gif";
		$perm["edit"]["alt"]=$this->lang->def("_MOD");

		$ssel->setPermList($perm);

		$url=$um->getUrl("op=setperm&catid=".$cat_id);
		$back_url=$um->getUrl("op=doneperm");
		$ssel->setLinks($url, $back_url);

		$op=$ssel->getOp();

		if (($op == "main") || ($op == "manual_init") )
			$saved_data=$this->faqManager->loadCategoryPerm($cat_id);


		$page_body="";
		$full_page="";

		switch($op) {

			case "main": {
				$ssel->setSavedData($saved_data);
				$res=$ssel->loadSimpleSelector();
			} break;

			case "manual_init":{

				// Saving permissions of simple selector
				$save_info=$ssel->getSaveInfo();
				$this->faqManager->saveCategoryPerm($cat_id, $save_info["selected"], $save_info["database"]);

				$ssel->setSavedData($saved_data);
				$ssel->loadManualSelector($this->lang->def("_FAQ_CATEGORY_PERMISSIONS"));
			} break;
			case "manual": {
				$ssel->loadManualSelector($this->lang->def("_FAQ_CATEGORY_PERMISSIONS"));
			} break;

			case "save_manual": {

				// Saving permissions of manual selector
				$save_info=$ssel->getSaveInfo();
				$this->faqManager->saveCategoryPerm($cat_id, $save_info["selected"], $save_info["database"]);

				jumpTo(str_replace("&amp;", "&", $url));
			} break;

			case "save": {

				// Saving permissions of simple selector
				$save_info=$ssel->getSaveInfo();
				$this->faqManager->saveCategoryPerm($cat_id, $save_info["selected"], $save_info["database"]);

				jumpTo(str_replace("&amp;", "&", $back_url));
			} break;

		}

		return $res;
	}


	function exportCategory($cat_id) {
		$cat_exported=array();


		$info=$this->faqManager->getCategoryInfo($cat_id);
		$cat_title=$info["title"];

		$doc = new DoceboDOMDocument("1.0");
		$root=$doc->createElement("FAQCATEGORY");
		$doc->appendChild($root);

		$elem=$doc->createElement("DATE");
		$elemText=$doc->createTextNode(date("Y-m-d H:i:s"));
		$elem->appendChild($elemText);
		$root->appendChild($elem);

		$elem=$doc->createElement("TITLE");
		$elemText=$doc->createTextNode(urlencode($info["title"]));
		$elem->appendChild($elemText);
		$root->appendChild($elem);

		$elem=$doc->createElement("DESCRIPTION");
		$elemText=$doc->createTextNode(urlencode($info["description"]));
		$elem->appendChild($elemText);
		$root->appendChild($elem);

/*		$elem=$doc->createElement("AUTHOR");
		$elemText=$doc->createTextNode($info["author"]);
		$elem->appendChild($elemText);
		$root->appendChild($elem); */

		$items=$doc->createElement("CATEGORYITEMS");
		$root->appendChild($items);

		$data_info=$this->faqManager->getCategoryItems($cat_id);
		$data_arr=$data_info["data_arr"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

/*			$id=$data_arr[$i]["faq_id"];

			$elem=$doc->createElement("faq_id");
			$elemText=$doc->createTextNode($id);
			$elem->appendChild($elemText);
			$elem->setAttribute("id", $id);
			$items->appendChild($elem);
*/

			$id=$data_arr[$i]["faq_id"];

			$faq=$doc->createElement("faq");
			$faq->setAttribute("id", $id);
			$items->appendChild($faq);

			$elem=$doc->createElement("title");
			$elemText=$doc->createTextNode(urlencode($data_arr[$i]["title"]));
			$elem->appendChild($elemText);
			$faq->appendChild($elem);

			$elem=$doc->createElement("question");
			$elemText=$doc->createTextNode(urlencode($data_arr[$i]["question"]));
			$elem->appendChild($elemText);
			$faq->appendChild($elem);

			$elem=$doc->createElement("keyword");
			$elemText=$doc->createTextNode(urlencode($data_arr[$i]["keyword"]));
			$elem->appendChild($elemText);
			$faq->appendChild($elem);

			$elem=$doc->createElement("answer");
			$elemText=$doc->createTextNode(urlencode($data_arr[$i]["answer"]));
			$elem->appendChild($elemText);
			$faq->appendChild($elem);

		}

		$out=$doc->saveXML();

		$title=rawurlencode(str_replace(" ", "", $cat_title));
		$date=date("Ymd");
		$domain=preg_replace("/www/i", "", $_SERVER["SERVER_NAME"]);
		$domain=str_replace(".", "", $domain);
		$filename="faq_".$date."_".$title."_".$domain;
		$filename=substr($filename, 0, 200);

		//-- Debug: --//
		// echo $filename."<br /><br /><textarea rows=\"20\" cols=\"80\">".$out."</textarea>"; die();

		ob_end_clean();
		//Download file
		//send file length info
		header('Content-Length:'. strlen($out));
		//content type forcing dowlad
		header("Content-type: application/download\n");
		//cache control
		header("Cache-control: private");
		//sending creation time
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		//content type
		header('Content-Disposition: attachment; filename="'.$filename.'.xml"');
		//sending file
		echo $out;
		//and now exit
		exit();
	}


	function importCategory() {
		include_once($GLOBALS['where_framework']."/lib/lib.form.php");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();


		if (isset($_POST["undo"])) { // -------------------------- Cancel ------- |
			jumpTo($back_url);
		}
		else if (isset($_POST["conf_import"])) { // -------------- Import ------- |

			$tmp_fname=$_FILES["file"]["tmp_name"];
			$import_arr=$this->faqManager->getImportArrFromXml($tmp_fname);

			$this->faqManager->importNewCategory($import_arr);

			// TODO: add import into category if cat_id > 0

			jumpTo($back_url);
		}
		else { // ------------------------------------------------ Import Form -- |

			$res="";
			$form=new Form();

			$url="";
			$res.=$form->openForm("import_form", $url, FALSE, FALSE, "multipart/form-data");
			$res.=$form->openElementSpace();

			$res.=$form->getFilefield($this->lang->def("_FILE"), "file", "file");

			$res.=$form->closeElementSpace();
			$res.=$form->openButtonSpace();
			$res.=$form->getButton('conf_import', 'conf_import', $this->lang->def('_IMPORT'));
			$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
			$res.=$form->closeButtonSpace();
			$res.=$form->closeForm();

			return $res;
		}
	}


	function showCatItems($cat_id, $vis_item) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_TABLE_FAQ_CAP");
		$table_summary=$this->lang->def("_TABLE_FAQ_SUM");

		$um=& UrlManager::getInstance();
		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_TITLE"));


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

		$head_type=array("", "image", "image", "image", "image");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);
		
		$tab->initNavBar('ini', 'link');
		$tab->setLink($um->getUrl('op=showcat&catid='.$cat_id));

		$ini=$tab->getSelectedElement();


		$data_info=$this->faqManager->getCategoryItems($cat_id, $ini, $vis_item);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["faq_id"];

			$rowcnt=array();
			$rowcnt[]=$data_arr[$i]["title"];


			if ($ini+$i < $db_tot-1) {
				$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$this->lang->def("_MOVE_DOWN")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_DOWN")."\" />";
				$url=$um->getUrl("op=movefaqdown&catid=".$cat_id."&faqid=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			if ($ini+$i > 0) {
				$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$this->lang->def("_MOVE_UP")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_UP")."\" />";
				$url=$um->getUrl("op=movefaqup&catid=".$cat_id."&faqid=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$url=$um->getUrl("op=editfaq&catid=".$cat_id."&faqid=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
			$img.="title=\"".$this->lang->def("_DEL")."\" />";
			$url=$um->getUrl("op=deletefaq&catid=".$cat_id."&faqid=".$id."&conf_del=1");
			$rowcnt[]="<a href=\"".$url."\" title=\"".$this->lang->def('_DEL')." : ".$data_arr[$i]["title"]."\">".$img."</a>\n";

			$tab->addBody($rowcnt);
		}

		$url=$um->getUrl("op=addfaq&catid=".$cat_id);
		$tab->addActionAdd("<a class=\"new_element_link\" href=\"".$url."\">".$this->lang->def('_ADD')."</a>\n");

		$res=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}


	function addeditFaq($cat_id, $id=0) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$form=new Form();
		$form_code="";

		$um=& UrlManager::getInstance();
		$url=$um->getUrl("op=savefaq&catid=".$cat_id);

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

			$info=$this->faqManager->getFaqInfo($id);

			$title=$info["title"];
			$question=$info["question"];
			$keyword=$info["keyword"];
			$answer=$info["answer"];
		}


		$res.=$form_code;
		$res.=$form->openElementSpace();

		$res.=$form->getTextfield($this->lang->def("_TITLE"), "title", "title", 255, $title);
		$res.=$form->getTextfield($this->lang->def("_QUESTION"), "question", "question", 255, $question);
		$res.=$form->getSimpleTextarea($this->lang->def("_KEYWORDS"), "keyword", "keyword", $keyword);
		$res.=$form->getTextarea($this->lang->def("_ANSWER"), "answer", "answer", $answer);

		$res.=$form->getHidden("cat_id", "cat_id", $cat_id);
		$res.=$form->getHidden("id", "id", $id);


		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $submit_lbl);
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function saveFaq($cat_id) {
		$um=& UrlManager::getInstance();

		$faq_id=$this->faqManager->saveFaq($cat_id, $_POST);

		$url=$um->getUrl("op=showcat&catid=".$cat_id);
		jumpTo($url);
	}


	function deleteFaq($cat_id, $faq_id) {
		include_once($GLOBALS['where_framework']."/lib/lib.form.php");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl("op=showcat&catid=".$cat_id);


		if (isset($_POST["undo"])) {
			jumpTo($back_url);
		}
		else if ( get_Req("conf_del", DOTY_INT, false)>0 ) {

			$this->faqManager->deleteFaq($faq_id);

			jumpTo($back_url);
		}
		else {

			$res="";
			$info=$this->faqManager->getFaqInfo($faq_id);
			$title=$info["title"];

			$form=new Form();

			$url="";
			$res.=$form->openForm("delete_form", $url);

			$res.=$form->getHidden("faq_id", "faq_id", $faq_id);
			$res.=$form->getHidden("cat_id", "cat_id", $cat_id);

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


	function moveFaq($cat_id, $faq_id, $direction) {
		$um=& UrlManager::getInstance();

		$this->faqManager->moveFaq($direction, $faq_id);

		$url=$um->getUrl("op=showcat&catid=".$cat_id);
		jumpTo($url);
	}


}






Class CoreFaqPublic {

	var $lang=NULL;
	var $faqManager=NULL;


	function CoreFaqPublic() {
		$this->lang =& DoceboLanguage::createInstance("faq", "framework");
		$this->faqManager=new CoreFaqManager();
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
		$mode=((isset($_GET["mode"])) && (!empty($_GET["mode"])) ? $_GET["mode"] : "faq");
		return $mode;
	}


	function displayModeMenu($mode) {
		$res="";

		$um=& UrlManager::getInstance();

		$res.="<div class=\"faq_align_center\">";
		if ($mode == "help") {
			$res.="[ <a href=\"".$um->getUrl("mode=faq")."\">".$this->lang->def('_SWITCH_TO_FAQ')."</a>";
			$res.=" | ".$this->lang->def("_SWITCH_TO_HELP")."</a> ]\n";
		}
		else {
			$res.="[ ".$this->lang->def("_SWITCH_TO_FAQ")." | ";
			$res.="<a href=\"".$um->getUrl("mode=help")."\">".$this->lang->def("_SWITCH_TO_HELP")."</a> ]\n";
		}

		$res.="</div>";

		return $res;
	}


	function getSearchForm($cat_id, $mode) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$form=new Form();
		$um=& UrlManager::getInstance();

		$search_info=$this->getSearchInfo();
		$search=$search_info["search_txt"];
		$letter=$search_info["letter"];

		$res.=$form->openForm("glossary_play", $um->getUrl("op=search&mode=".$mode));

		$res.=$form->getOpenFieldset($this->lang->def('_FILTER'));
		$res.=$form->getHidden('idCategory', 'idCategory', $cat_id);
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


	function showCategoryItems($cat_id, $data_arr, $db_tot=FALSE, $read_only=FALSE) {
		$res="";

		$um=& UrlManager::getInstance();
		$mode=$this->readModeFromUrl();

		$search_info=$this->getSearchInfo();
		$search_txt=$search_info["search_txt"];

		$can_add=$this->checkCategoryPerm($cat_id, "add", TRUE);
		$can_edit=$this->checkCategoryPerm($cat_id, "edit", TRUE);

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$title=$data_arr[$i]["title"];
			$question=$data_arr[$i]["question"];
			$answer=$data_arr[$i]["answer"];

			if (!empty($search_txt)) {
				$new_text="<span class=\"faq_evidence\">".$search_txt."</span>";
				$question=preg_replace("/".$search_txt."/i", $new_text, $question);
				$answer=preg_replace("/".$search_txt."/i", $new_text, $answer);
			}


			if ($can_edit) {
				$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
				$img.="title=\"".$this->lang->def("_MOD")."\" />";
				$url=$um->getUrl("op=editfaq&faqid=".$data_arr[$i]["faq_id"]);

				$edit_code ="<div class=\"faq_edit_box\">";
				$edit_code.="<a href=\"".$url."\">".$img."</a>\n";
				$edit_code.="</div>\n";
			}
			else
				$edit_code="";

			$res.="<div class=\"faq_boxinfo_title\">";
			if ($mode == "faq")
				$res.=$question;
			else if ($mode == "help")
				$res.=$title;
			$res.="</div>\n";

			$res.="<div class=\"faq_boxinfo_container\">";
			$res.=$edit_code;
			$res.="<p>".$answer."</p>";
			$res.="</div>\n";

		}

		if ($can_add) {
			$url=$um->getUrl("op=addfaq");
			$res.="<div class=\"faq_add_box\">";
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

		$res.="<div class=\"faq_boxinfo_title\">";
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


	function showFaqList($cat_id, $read_only=FALSE) {
		$res="";

		$um=& UrlManager::GetInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$cat_info=$this->faqManager->getCategoryInfo($cat_id);

		$mode=$this->readModeFromUrl();
		$title=$cat_info["title"];
		$um->setModRewriteTitle(format_mod_rewrite_title($title));

		$res.=$this->displayModeMenu($mode);
		$res.=$this->getSearchForm($cat_id, $mode);

		$res.="<div class=\"faq_cat_title\">".$this->lang->def('_TITLE').": ".$title."</div>\n";


		if ($mode == "help") { // Show keywords..
			// we have to get all data, without search filter..
			$data_info=$this->faqManager->getCategoryItems($cat_id);
			$data_arr=$data_info["data_arr"];

			$keys=$this->extractKeys($data_arr);
			$res.="<div class=\"faq_colum_25\">";
			$res.=$this->showKeysMenu($keys);
			$res.="</div>\n"; // colum_25
			$res.="<div class=\"faq_colum_75\">";
		}


		$where=$this->getSearchWhere();

		$data_info=$this->faqManager->getCategoryItems($cat_id, FALSE, FALSE, $where);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$res.=$this->showCategoryItems($cat_id, $data_arr, $db_tot, $read_only);

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
				$_SESSION["faq_search"]["search_txt"]=$_POST["search"];
			}

			if ((isset($_GET["letter"])) && ((int)$_GET["letter"] > 0)) {
				$_SESSION["faq_search"]["letter"]=$_GET["letter"];
			}

			if ((isset($_GET["keyword"])) && (!empty($_GET["keyword"]))) {
				$_SESSION["faq_search"]["keyword"]=base64_decode($_GET["keyword"]);
			}
		}
		else {
			$_SESSION["faq_search"]["search_txt"]="";
			$_SESSION["faq_search"]["letter"]="";
			$_SESSION["faq_search"]["keyword"]="";
		}

		$mode=$this->readModeFromUrl();
		$url=$um->getUrl("mode=".$mode);
		jumpTo($url);
	}


	function getSearchInfo() {
		$res=array();

		$res["search_txt"]=(isset($_SESSION["faq_search"]["search_txt"]) ? $_SESSION["faq_search"]["search_txt"] : "");
		$res["letter"]=(isset($_SESSION["faq_search"]["letter"]) ? $_SESSION["faq_search"]["letter"] : "");
		$res["keyword"]=(isset($_SESSION["faq_search"]["keyword"]) ? $_SESSION["faq_search"]["keyword"] : "");

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


	function checkCategoryPerm($cat_id, $perm, $return_res=FALSE) {
		$res=FALSE;

		$user=& $GLOBALS['current_user'];
		$acl=new DoceboACL();
		$role_id="/framework/faq_category/".$cat_id."/".$perm;

		if (($role_id != "") && ($acl->getRoleST($role_id) != FALSE))
			$res=$user->matchUserRole($role_id);

		if ($return_res) {
			return $res;
		}
		else if (!$res)
			die("You can't access!");
	}


	function addeditFaq($cat_id, $faq_id, $todo) {
		$res="";

		if ($cat_id < 1)
			return FALSE;

		// Check permissions
		$this->checkCategoryPerm($cat_id, $todo);

		$cfa=new CoreFaqAdmin();
		$res.=$cfa->addeditFaq($cat_id, $faq_id);

		return $res;
	}


	function saveFaq($cat_id) {

		$um=& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		if ($cat_id < 1)
			return FALSE;

		if ((int)$_POST["id"] > 0)
			$todo="edit";
		else
			$todo="add";

		// Check permissions
		$this->checkCategoryPerm($cat_id, $todo);

		$this->faqManager->saveFaq($cat_id, $_POST);

		$back_url=$um->getUrl();
		jumpTo($back_url);
	}


}






Class CoreFaqManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $category_info=NULL;
	var $faq_info=NULL;

	function CoreFaqManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_fw"]);
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


	function _getFaqTable() {
		return $this->prefix."_faq";
	}


	function _getCategoryTable() {
		return $this->prefix."_faq_cat";
	}


	function getLastOrd($table) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");
		return utilGetLastOrd($table, "ord");
	}


	function moveFaq($direction, $id_val) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

		$table=$this->_getFaqTable();

		utilMoveItem($direction, $table, "faq_id", $id_val, "ord");
	}


	function getCategoryList($ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getCategoryTable()." ";

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

				$id=$row["category_id"];
				$data_info["data_arr"][$i]=$row;
				$this->category_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function saveCategory($data) {

		$cat_id=(int)$data["id"];
		$title=$data["title"];
		$description=$data["description"];

		if ($cat_id < 1) {

			$field_list ="title, description";
			$field_val="'".$title."', '".$description."'";

			$qtxt="INSERT INTO ".$this->_getCategoryTable()." (".$field_list.") VALUES(".$field_val.")";
			$res=$this->_executeInsert($qtxt);
		}
		else {

			$qtxt ="UPDATE ".$this->_getCategoryTable()." SET title='".$title."', ";
			$qtxt.="description='".$description."' ";
			$qtxt.="WHERE category_id='".$cat_id."'";
			$q=$this->_executeQuery($qtxt);

			$res=$cat_id;
		}

		return $res;
	}


	function loadCategoryInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getCategoryTable()." ";
		$qtxt.="WHERE category_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getCategoryInfo($id) {

		if (!isset($this->category_info[$id])) {
			$info=$this->loadCategoryInfo($id);
			$this->category_info[$id]=$info;
		}

		return $this->category_info[$id];
	}


	function deleteCategory($cat_id) {

		// Delete category
		$qtxt ="DELETE FROM ".$this->_getCategoryTable()." ";
		$qtxt.="WHERE category_id='".(int)$cat_id."' LIMIT 1";
		$q=$this->_executeQuery($qtxt);

		// Delete category items
		$qtxt ="DELETE FROM ".$this->_getFaqTable()." ";
		$qtxt.="WHERE category_id='".(int)$cat_id."'";
		$q=$this->_executeQuery($qtxt);

		// Delete category roles
		$role_id="/framework/faq_category/".(int)$cat_id."/";
		$acl_manager=$GLOBALS["current_user"]->getAclManager();
		$acl_manager->deleteRoleFromPath($role_id);
	}


	function getCategoryPermList() {
		return array("add", "edit");
	}


	function loadCategoryPerm($cat_id) {
		$res=array();
		$pl=$this->getCategoryPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();

		foreach($pl as $key=>$val) {

			$role_id="/framework/faq_category/".$cat_id."/".$val;
			$role=$acl_manager->getRole(false, $role_id);

			if (!$role) {
				$res[$val]=array();
			}
			else {
				$idst=$role[ACL_INFO_IDST];
				$res[$val]=array_flip($acl_manager->getRoleMembers($idst));
			}
		}

		return $res;
	}


	function saveCategoryPerm($cat_id, $selected_items, $database_items) {

		$pl=$this->getCategoryPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();
		foreach($pl as $key=>$val) {
			if ((isset($selected_items[$val])) && (is_array($selected_items[$val]))) {

				$role_id="/framework/faq_category/".$cat_id."/".$val;
				$role=$acl_manager->getRole(false, $role_id);
				if (!$role)
					$idst=$acl_manager->registerRole($role_id, "");
				else
					$idst=$role[ACL_INFO_IDST];

				foreach($selected_items[$val] as $pk=>$pv) {
					if ((!isset($database_items[$val])) || (!is_array($database_items[$val])) ||
						(!in_array($pv, array_keys($database_items[$val])))) {
							$acl_manager->addToRole($idst, $pv);
					}
				}

				if ((isset($database_items[$val])) && (is_array($database_items[$val])))
					$to_rem=array_diff(array_keys($database_items[$val]), $selected_items[$val]);
				else
					$to_rem=array();
				foreach($to_rem  as $pk=>$pv) {
					$acl_manager->removeFromRole($idst, $pv);
				}

			}
		}

	}




	function getImportArrFromXml($filename) {
		$res=array();

		require_once($GLOBALS["where_framework"].'/lib/lib.domxml.php');
		$xml_doc=new DoceboDOMDocument();

		if (!$xml_doc)
			return FALSE;

		if ($xml_doc->load($filename)) {

			$xpath=new DoceboDOMXPath($xml_doc);

			$cat_info=array();
			$category_node=$xpath->query('/FAQCATEGORY');

			for($i = 0; $i < $category_node->length; $i++) {

				$item=$category_node->item($i);
				$elem=$xpath->query('TITLE/text()', $item);
				$elemNode=$elem->item(0);
				$cat_info["title"]=urldecode($elemNode->textContent);

				$item=$category_node->item($i);
				$elem=$xpath->query('DESCRIPTION/text()', $item);
				$elemNode=$elem->item(0);
				$cat_info["description"]=urldecode($elemNode->textContent);


				$cat_items=$xpath->query('CATEGORYITEMS/faq', $item);

				$faq_list=array();
				$arr_id=0;
				for($iFaq = 0; $iFaq < $cat_items->length; $iFaq++) {

					$faq=$cat_items->item($iFaq);
					$elem=$xpath->query('title/text()', $faq);
					$elemNode=$elem->item(0);
					$faq_list[$arr_id]["title"]=urldecode($elemNode->textContent);

					$faq=$cat_items->item($iFaq);
					$elem=$xpath->query('question/text()', $faq);
					$elemNode=$elem->item(0);
					$faq_list[$arr_id]["question"]=urldecode($elemNode->textContent);

					$faq=$cat_items->item($iFaq);
					$elem=$xpath->query('keyword/text()', $faq);
					$elemNode=$elem->item(0);
					$faq_list[$arr_id]["keyword"]=urldecode($elemNode->textContent);

					$faq=$cat_items->item($iFaq);
					$elem=$xpath->query('answer/text()', $faq);
					$elemNode=$elem->item(0);
					$faq_list[$arr_id]["answer"]=urldecode($elemNode->textContent);

					$arr_id++;
				}
			}
		}
		else
			return FALSE;

		$res["cat_info"]=$cat_info;
		$res["faq_list"]=$faq_list;

		return $res;
	}


	function importNewCategory($import_arr) {

		$cat_data=array();
		$cat_data["id"]=0;
		$cat_data["title"]=addslashes($import_arr["cat_info"]["title"]);
		$cat_data["description"]=addslashes($import_arr["cat_info"]["description"]);

		$cat_id=$this->saveCategory($cat_data);

		$this->importCategoryItems($cat_id, $import_arr["faq_list"]);
	}


	function importCategoryItems($cat_id, $faq_list) {

		foreach ($faq_list as $faq) {

			$fat_data=array();

			$faq_data["id"]=0;
			$faq_data["title"]=addslashes($faq["title"]);
			$faq_data["question"]=addslashes($faq["question"]);
			$faq_data["keyword"]=addslashes($faq["keyword"]);
			$faq_data["answer"]=addslashes($faq["answer"]);

			$this->saveFaq($cat_id, $faq_data);
		}
	}


	function getCategoryItems($cat_id, $ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getFaqTable()." ";

		$qtxt.="WHERE category_id='".(int)$cat_id."' ";
		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		$qtxt.="ORDER BY ord ";
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

				$id=$row["faq_id"];
				$data_info["data_arr"][$i]=$row;
				$this->faq_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function loadFaqInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getFaqTable()." ";
		$qtxt.="WHERE faq_id='".(int)$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getFaqInfo($id) {

		if (!isset($this->faq_info[$id])) {
			$info=$this->loadFaqInfo($id);
			$this->faq_info[$id]=$info;
		}

		return $this->faq_info[$id];
	}


	function saveFaq($cat_id, $data) {

		$faq_id=(int)$data["id"];
		$title=$data["title"];
		$question=$data["question"];
		$keyword=$data["keyword"];
		$answer=$data["answer"];

		if ($faq_id < 1) { // Add

			$ord=$this->getLastOrd($this->_getFaqTable())+1;

			$field_list ="title, category_id, question, keyword, answer, ord";
			$field_val ="'".$title."', '".(int)$cat_id."', '".$question."', ";
			$field_val.="'".$keyword."', '".$answer."', '".$ord."'";

			$qtxt="INSERT INTO ".$this->_getFaqTable()." (".$field_list.") VALUES (".$field_val.")";
			$res=$this->_executeInsert($qtxt);
		}
		else { // Update

			$qtxt ="UPDATE ".$this->_getFaqTable()." SET title='".$title."', ";
			$qtxt.="question='".$question."', keyword='".$keyword."', answer='".$answer."' ";
			$qtxt.="WHERE faq_id='".$faq_id."' AND category_id='".(int)$cat_id."'";
			$q=$this->_executeQuery($qtxt);

			$res=$faq_id;
		}

		return $res;
	}


	function deleteFaq($faq_id) {

		// Delete faq
		$qtxt ="DELETE FROM ".$this->_getFaqTable()." ";
		$qtxt.="WHERE faq_id='".(int)$faq_id."' LIMIT 1";
		$q=$this->_executeQuery($qtxt);
	}


}



?>
