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

// ----------------------------------------------------------------------------


class SimplePrjAdmin {

	var $lang=NULL;
	var $um=NULL;
	var	$table_style=FALSE;

	var $simpleprjManager=NULL;


	function SimplePrjAdmin() {
		$this->lang =& DoceboLanguage::createInstance('admin_simpleprj', "cms");
		$this->simpleprjManager=new SimplePrjManager();
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


	function getSimplePrjTable($vis_item) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_TABLE_SIMPLEPRJ_PROJECT_CAP");
		$table_summary=$this->lang->def("_TABLE_SIMPLEPRJ_PROJECT_SUM");

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
		$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$this->lang->def("_ALT_SETPERM")."\" ";
		$img.="title=\"".$this->lang->def("_ALT_SETPERM")."\" />";
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

		$data_info=$this->simpleprjManager->getSimplePrjList($ini, $vis_item);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["project_id"];

			$rowcnt=array();
			$rowcnt[]=$data_arr[$i]["title"];


			if ($ini+$i < $db_tot-1) {
				$img ="<img src=\"".getPathImage('fw')."standard/down.gif\" alt=\"".$this->lang->def("_MOVE_DOWN")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_DOWN")."\" />";
				$url=$um->getUrl("op=movedown&prjid=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			if ($ini+$i > 0) {
				$img ="<img src=\"".getPathImage('fw')."standard/up.gif\" alt=\"".$this->lang->def("_MOVE_UP")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_UP")."\" />";
				$url=$um->getUrl("op=moveup&prjid=".$id);
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";
			}
			else {
				$rowcnt[]="&nbsp;";
			}

			$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$this->lang->def("_ALT_SETPERM")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_SETPERM")."\" />";
			$url=$um->getUrl("op=setperm&prjid=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$url=$um->getUrl("op=editprj&prjid=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
			$img.="title=\"".$this->lang->def("_DEL")."\" />";
			$url=$um->getUrl("op=delproject&prjid=".$id."&conf_del=1");
			$rowcnt[]="<a href=\"".$url."\" title=\"".$this->lang->def("_DEL")." : ".$data_arr[$i]["title"]."\">".$img."</a>\n";

			$tab->addBody($rowcnt);
		}

		$url=$um->getUrl("op=addproject");
		$add_box ="<a class=\"new_element_link_float\" href=\"".$url."\">".$this->lang->def('_ADD')."</a>\n";
		$tab->addActionAdd($add_box);

		$res=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}


	function addeditSimplePrj($id=0) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$form=new Form();
		$form_code="";

		$um=& UrlManager::getInstance();
		$url=$um->getUrl("op=saveproject");

		if ($id == 0) {
			$form_code=$form->openForm("main_form", $url);
			$submit_lbl=$this->lang->def("_INSERT");

			$title="";
			$description="";
		}
		else if ($id > 0) {
			$form_code=$form->openForm("main_form", $url);
			$submit_lbl=$this->lang->def("_SAVE");

			$info=$this->simpleprjManager->getSimplePrjInfo($id);

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


	function saveSimplePrj() {
		$um=& UrlManager::getInstance();

		$project_id=$this->simpleprjManager->saveSimplePrj($_POST);

		$url=$um->getUrl();
		jumpTo($url);
	}


	function deletePrj($project_id) {
		include_once($GLOBALS['where_framework']."/lib/lib.form.php");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();


		if (isset($_POST["undo"])) {
			jumpTo($back_url);
		}
		else if (get_req("conf_del", DOTY_INT, false)) {

			$this->simpleprjManager->deleteSimplePrj($project_id);

			jumpTo($back_url);
		}
		else {

			$res="";
			$info=$this->simpleprjManager->getSimplePrjInfo($project_id);
			$title=$info["title"];

			$form=new Form();

			$url=$um->getUrl("op=delproject&prjid=".$project_id);
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


	function showPrjPerm($project_id) {
		$res=FALSE;
		require_once($GLOBALS['where_cms']."/lib/lib.simplesel.php");

		$um=& UrlManager::getInstance();
		$ssel=new SimpleSelector(TRUE, $this->lang);

		$perm=array();

		$perm["view"]["img"]=getPathImage('fw')."standard/view.gif";
		$perm["view"]["alt"]=$this->lang->def("_VIEW_PERM");
		$perm["upload"]["img"]=getPathImage('fw')."standard/attach.gif";
		$perm["upload"]["alt"]=$this->lang->def("_ATTACH_PERM");
		$perm["comment"]["img"]=getPathImage('cms')."simpleprj/comment.gif";
		$perm["comment"]["alt"]=$this->lang->def("_COMMENT_PERM");

		$ssel->setPermList($perm);

		$url=$um->getUrl("op=setperm&prjid=".$project_id);
		$back_url=$um->getUrl("op=doneperm");
		$ssel->setLinks($url, $back_url);

		$op=$ssel->getOp();

		if (($op == "main") || ($op == "manual_init") )
			$saved_data=$this->simpleprjManager->loadPrjPerm($project_id);


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
				$this->simpleprjManager->savePrjPerm($project_id, $save_info["selected"], $save_info["database"]);

				$ssel->setSavedData($saved_data);
				$ssel->loadManualSelector($this->lang->def("_SIMPLEPRJ_PROJECT_PERMISSIONS"));
			} break;
			case "manual": {
				$ssel->loadManualSelector($this->lang->def("_SIMPLEPRJ_PROJECT_PERMISSIONS"));
			} break;

			case "save_manual": {

				// Saving permissions of manual selector
				$save_info=$ssel->getSaveInfo();
				$this->simpleprjManager->savePrjPerm($project_id, $save_info["selected"], $save_info["database"]);

				jumpTo(str_replace("&amp;", "&", $url));
			} break;

			case "save": {

				// Saving permissions of simple selector
				$save_info=$ssel->getSaveInfo();
				$this->simpleprjManager->savePrjPerm($project_id, $save_info["selected"], $save_info["database"]);

				jumpTo(str_replace("&amp;", "&", $back_url));
			} break;

		}

		return $res;
	}


	function moveSimplePrj($project_id, $direction) {
		$um=& UrlManager::getInstance();

		$this->simpleprjManager->moveSimplePrj($project_id, $direction);

		$url=$um->getUrl();
		jumpTo($url);
	}


}






Class SimplePrjManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $project_info=NULL;
	var $simpleprj_info=NULL;

	function SimplePrjManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_cms"]);
		$this->dbconn=$dbconn;
	}


	function _query( $query ) {
		doDebug("\n<!-- debug $query -->");
		if( $this->dbconn === NULL )
			$rs = mysql_query( $query );
		else
			$rs = mysql_query( $query, $this->dbconn );
		return $rs;
	}


	function _insQuery( $query ) {
		doDebug("\n<!-- debug $query -->");
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


	function _getSimplePrjTable() {
		return $this->prefix."_simpleprj";
	}


	function _getSimplePrjFileTable() {
		return $this->prefix."_simpleprj_file";
	}


	function _getSimplePrjTaskTable() {
		return $this->prefix."_simpleprj_task";
	}


	function _getSimplePrjBlockTable() {
		return $this->prefix."_area_block_simpleprj";
	}


	function getLastOrd($table) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");
		return utilGetLastOrd($table, "ord");
	}


	function moveSimplePrj($id_val, $direction) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

		$table=$this->_getSimplePrjTable();

		utilMoveItem($direction, $table, "project_id", $id_val, "ord");
	}


	function getSimplePrjList($ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getSimplePrjTable()." ";

		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="WHERE ".$where." ";
		}

		$qtxt.="ORDER BY ord, title ";
		$q=$this->_query($qtxt);

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

				$id=$row["project_id"];
				$data_info["data_arr"][$i]=$row;
				$this->project_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function saveSimplePrj($data) {

		$project_id=(int)$data["id"];
		$title=$data["title"];
		$description=$data["description"];

		if ($project_id < 1) {

			$ord=$this->getLastOrd($this->_getSimplePrjTable())+1;

			$field_list ="title, description, ord";
			$field_val="'".$title."', '".$description."', '".$ord."'";

			$qtxt="INSERT INTO ".$this->_getSimplePrjTable()." (".$field_list.") VALUES(".$field_val.")";
			$res=$this->_insQuery($qtxt);
		}
		else {

			$qtxt ="UPDATE ".$this->_getSimplePrjTable()." SET title='".$title."', ";
			$qtxt.="description='".$description."' ";
			$qtxt.="WHERE project_id='".$project_id."'";
			$q=$this->_query($qtxt);

			$res=$project_id;
		}

		return $res;
	}


	function loadSimplePrjInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getSimplePrjTable()." ";
		$qtxt.="WHERE project_id='".(int)$id."'";
		$q=$this->_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getSimplePrjInfo($id) {

		if (!isset($this->project_info[$id])) {
			$info=$this->loadSimplePrjInfo($id);
			$this->project_info[$id]=$info;
		}

		return $this->project_info[$id];
	}


	function deleteSimplePrj($project_id) {

		// Delete project files and comments
		$data_info=$this->getSimplePrjDocList($project_id);
		$data_arr=$data_info["data_arr"];
		foreach($data_arr as $document) {
			$file_id =$document["file_id"];
			$this->deleteDocument($file_id, $project_id);
		}

		// Delete project
		$qtxt ="DELETE FROM ".$this->_getSimplePrjTable()." ";
		$qtxt.="WHERE project_id='".(int)$project_id."' LIMIT 1";
		$q=$this->_query($qtxt);

		// Delete project from cms blocks
		$qtxt ="DELETE FROM ".$this->_getSimplePrjBlockTable()." ";
		$qtxt.="WHERE project_id='".(int)$project_id."'";
		$q=$this->_query($qtxt);

		// Delete project roles
		$role_id="/cms/modules/simpleprj/".(int)$project_id."/";
		$acl_manager=$GLOBALS["current_user"]->getAclManager();
		$acl_manager->deleteRoleFromPath($role_id);
	}


	function getPrjPermList() {
		return array("view", "upload", "comment");
	}


	function loadPrjPerm($project_id) {
		$res=array();
		$pl=$this->getPrjPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();

		foreach($pl as $key=>$val) {

			$role_id="/cms/modules/simpleprj/".$project_id."/".$val;
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


	function savePrjPerm($project_id, $selected_items, $database_items) {

		$pl=$this->getPrjPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();
		foreach($pl as $key=>$val) {
			if ((isset($selected_items[$val])) && (is_array($selected_items[$val]))) {

				$role_id="/cms/modules/simpleprj/".$project_id."/".$val;
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


	function getSimplePrjDocInfo($file_id, $project_id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getSimplePrjFileTable()." ";
		$qtxt.="WHERE file_id='".(int)$file_id."' AND project_id='".(int)$project_id."'";
		$q=$this->_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function saveDocument($project_id, $data) {
		include_once($GLOBALS['where_framework']."/lib/lib.upload.php");

		$project_id =(int)$project_id;
		$doc_id =(int)$data["doc_id"];
		$title =$data["title"];
		$description =$data["description"];
		$author =$GLOBALS["current_user"]->getIdSt();

		if ($doc_id < 1) {

			$name =$_FILES["document"]["name"];
			$fname =$project_id."_".time()."_".rand(10,99)."_".$name;
			$tmp_fname =$_FILES["document"]["tmp_name"];

			sl_open_fileoperations();
			$f1=sl_upload($tmp_fname, _SP_FPATH_INTERNAL.$fname);
			sl_close_fileoperations();

			if (!$f1) {
				$fname ="";
			}

			$field_list ="project_id, fname, title, description, author";
			$field_val="'".$project_id."', '".addslashes($fname)."', '".$title."', '".$description."', '".$author."'";

			$qtxt="INSERT INTO ".$this->_getSimplePrjFileTable()." (".$field_list.") VALUES(".$field_val.")";
			$res=$this->_insQuery($qtxt);
		}
		else {

			$update_fname =FALSE;
			if ((isset($_FILES["document"]["name"])) && (!empty($_FILES["document"]["name"]))) {

				$name =$_FILES["document"]["name"];
				$fname =$project_id."_".time()."_".rand(10,99)."_".$name;
				$tmp_fname =$_FILES["document"]["tmp_name"];

				sl_open_fileoperations();
				$f1=sl_upload($tmp_fname, _SP_FPATH_INTERNAL.$fname);
				if (!empty($_POST["old_document"])) {
					sl_unlink(_SP_FPATH_INTERNAL.$_POST["old_document"]);
				}
				sl_close_fileoperations();

				if ($f1) {
					$update_fname =TRUE;
				}
			}

			$qtxt ="UPDATE ".$this->_getSimplePrjFileTable()." SET title='".$title."', ";
			$qtxt.="description='".$description."'";
			$qtxt.=($update_fname ? ", fname='".addslashes($fname)."'" : " ");
			$qtxt.="WHERE file_id='".$doc_id."' AND project_id='".$project_id."'";
			$q=$this->_query($qtxt);

			$res=$project_id;
		}

		return $res;
	}


	function getSimplePrjDocList($project_id, $ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getSimplePrjFileTable()." ";
		$qtxt.="WHERE project_id='".(int)$project_id."' ";

		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		$qtxt.="ORDER BY title ";
		$q=$this->_query($qtxt);

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

				$id=$row["file_id"];
				$data_info["data_arr"][$i]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function deleteDocument($file_id, $project_id) {
		require_once($GLOBALS['where_framework']."/lib/lib.upload.php");
		require_once($GLOBALS["where_framework"]."/lib/lib.ajax_comment.php");

		$doc_info =$this->getSimplePrjDocInfo($file_id, $project_id);

		if (count($doc_info) < 1) { // Security check
			return FALSE;
		}

		$fname =$doc_info["fname"];

		sl_open_fileoperations();
		if (!empty($fname)) {
			sl_unlink(_SP_FPATH_INTERNAL.$fname);
		}
		sl_close_fileoperations();

		// Delete project document
		$qtxt ="DELETE FROM ".$this->_getSimplePrjFileTable()." ";
		$qtxt.="WHERE file_id='".(int)$file_id."' AND project_id='".(int)$project_id."' LIMIT 1";
		$q=$this->_query($qtxt);

		// Delete document comments
		$ax_comm = new AjaxComment('simpleprj_doc', 'cms');
		$ax_comm->deleteCommentByResourceKey($file_id);

		return $q;
	}


	function getSimplePrjTaskList($project_id, $ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getSimplePrjTaskTable()." ";
		$qtxt.="WHERE project_id='".(int)$project_id."' ";

		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="AND ".$where." ";
		}

		$qtxt.="ORDER BY complete, description ";
		$q=$this->_query($qtxt);

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

				$id=$row["task_id"];
				$data_info["data_arr"][$i]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function saveTask($task_id, $description=FALSE, $complete=FALSE) {

		$qtxt ="UPDATE ".$this->_getSimplePrjTaskTable()." SET ";
		$first =TRUE;
		if ($description !== FALSE) {
			$qtxt.="description='".$description."' ";
			$first =FALSE;
		}
		if ($complete !== FALSE) {
			$qtxt.=(!$first ? "," : "");
			$qtxt.="complete='".(int)$complete."' ";
			$first =FALSE;
		}
		$qtxt.="WHERE task_id='".$task_id."'";

		$res =$this->_query($qtxt);

		return $res;
	}


	function deleteTask($task_id, $project_id) {

		$qtxt ="DELETE FROM ".$this->_getSimplePrjTaskTable()." ";
		$qtxt.="WHERE task_id='".(int)$task_id."' AND project_id='".(int)$project_id."'";

		$res =($this->_query($qtxt) ? TRUE : FALSE);

		return $res;
	}


	function addTask($project_id, $description) {

		$qtxt ="INSERT INTO ".$this->_getSimplePrjTaskTable()." (project_id, description) ";
		$qtxt.="VALUES ('".(int)$project_id."', '".$description."')";

		$task_id =$this->_insQuery($qtxt);

		return $task_id;
	}


}



?>
