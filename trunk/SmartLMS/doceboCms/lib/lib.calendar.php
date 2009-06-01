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


class CmsCalendarAdmin {

	var $lang=NULL;
	var $um=NULL;
	var	$table_style=FALSE;

	var $calendarManager=NULL;


	function CmsCalendarAdmin() {
		$this->lang =& DoceboLanguage::createInstance('admin_calendar', "cms");
		$this->calendarManager=new CmsCalendarManager();
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


	function getCalendarTable($vis_item) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_TABLE_CALENDAR_CAP");
		$table_summary=$this->lang->def("_TABLE_CALENDAR_SUM");

		$um=& UrlManager::getInstance();
		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_TITLE"));


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

		$data_info=$this->calendarManager->getCalendarList($ini, $vis_item);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["calendar_id"];

			$rowcnt=array();
			$rowcnt[]=$data_arr[$i]["title"];

			$img ="<img src=\"".getPathImage('fw')."standard/moduser.gif\" alt=\"".$this->lang->def("_ALT_SETPERM")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_SETPERM")."\" />";
			$url=$um->getUrl("op=setperm&calid=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$url=$um->getUrl("op=editcat&calid=".$id);
			$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

			$img ="<img src=\"".getPathImage('fw')."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
			$img.="title=\"".$this->lang->def("_DEL")."\" />";
			$url=$um->getUrl("op=delcalendar&calid=".$id."&conf_del=1");
			$rowcnt[]="<a href=\"".$url."\" title=\"".$this->lang->def('_DEL')." : ".$data_arr[$i]["title"]."\">".$img."</a>\n";

			$tab->addBody($rowcnt);
		}

		$url=$um->getUrl("op=addcalendar");
		$add_box ="<a class=\"new_element_link_float\" href=\"".$url."\">".$this->lang->def('_ADD')."</a>\n";
		$tab->addActionAdd($add_box);

		$res=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}


	function addeditCalendar($id=0) {
		$res="";
		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$form=new Form();
		$form_code="";

		$um=& UrlManager::getInstance();
		$url=$um->getUrl("op=savecalendar");

		if ($id == 0) {
			$form_code=$form->openForm("main_form", $url);
			$submit_lbl=$this->lang->def("_INSERT");

			$title="";
			$description="";
		}
		else if ($id > 0) {
			$form_code=$form->openForm("main_form", $url);
			$submit_lbl=$this->lang->def("_SAVE");

			$info=$this->calendarManager->getCalendarInfo($id);

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


	function saveCalendar() {
		$um=& UrlManager::getInstance();

		$cat_id=$this->calendarManager->saveCalendar($_POST);

		$url=$um->getUrl();
		jumpTo($url);
	}


	function deleteCalendar($cat_id) {
		include_once($GLOBALS['where_framework']."/lib/lib.form.php");

		$um=& UrlManager::getInstance();
		$back_url=$um->getUrl();


		if (isset($_POST["undo"])) {
			jumpTo($back_url);
		}
		else if (get_req("conf_del", DOTY_INT, false)) {

			$this->calendarManager->deleteCalendar($cat_id);

			jumpTo($back_url);
		}
		else {

			$res="";
			$info=$this->calendarManager->getCalendarInfo($cat_id);
			$title=$info["title"];

			$form=new Form();

			$url=$um->getUrl("op=delcalendar&calid=".$cat_id);
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


	function showCalendarPerm($cat_id) {
		$res=FALSE;
		require_once($GLOBALS['where_cms']."/lib/lib.simplesel.php");

		$um=& UrlManager::getInstance();
		$ssel=new SimpleSelector(TRUE, $this->lang);

		$perm=array();

		$perm["view"]["img"]=getPathImage('fw')."standard/view.gif";
		$perm["view"]["alt"]=$this->lang->def("_ALT_VIEW");
		$perm["edit"]["img"]=getPathImage('fw')."standard/mod.gif";
		$perm["edit"]["alt"]=$this->lang->def("_MOD");
		$perm["admin"]["img"]=getPathImage('fw')."standard/modadmin.gif";
		$perm["admin"]["alt"]=$this->lang->def("_ALT_ADMIN");

		$ssel->setPermList($perm);

		$url=$um->getUrl("op=setperm&calid=".$cat_id);
		$back_url=$um->getUrl("op=doneperm");
		$ssel->setLinks($url, $back_url);

		$op=$ssel->getOp();

		if (($op == "main") || ($op == "manual_init") )
			$saved_data=$this->calendarManager->loadCalendarPerm($cat_id);


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
				$this->calendarManager->saveCalendarPerm($cat_id, $save_info["selected"], $save_info["database"]);

				$ssel->setSavedData($saved_data);
				$ssel->loadManualSelector($this->lang->def("_CALENDAR_PERMISSIONS"));
			} break;
			case "manual": {
				$ssel->loadManualSelector($this->lang->def("_CALENDAR_PERMISSIONS"));
			} break;

			case "save_manual": {

				// Saving permissions of manual selector
				$save_info=$ssel->getSaveInfo();
				$this->calendarManager->saveCalendarPerm($cat_id, $save_info["selected"], $save_info["database"]);

				jumpTo(str_replace("&amp;", "&", $url));
			} break;

			case "save": {

				// Saving permissions of simple selector
				$save_info=$ssel->getSaveInfo();
				$this->calendarManager->saveCalendarPerm($cat_id, $save_info["selected"], $save_info["database"]);

				jumpTo(str_replace("&amp;", "&", $back_url));
			} break;

		}

		return $res;
	}


}

Class CmsCalendarManager {

	var $prefix=NULL;
	var $dbconn=NULL;

	var $calendar_info=NULL;

	function CmsCalendarManager($prefix=FALSE, $dbconn=NULL) {
		$this->prefix=($prefix !== false ? $prefix : $GLOBALS["prefix_cms"]);
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


	function _getCalendarTable() {
		return $this->prefix."_calendar";
	}


	function getLastOrd($table) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");
		return utilGetLastOrd($table, "ord");
	}


	function getCalendarList($ini=FALSE, $vis_item=FALSE, $where=FALSE) {

		$data_info=array();
		$data_info["data_arr"]=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getCalendarTable()." ";

		if (($where !== FALSE) && (!empty($where))) {
			$qtxt.="WHERE ".$where." ";
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

				$id=$row["calendar_id"];
				$data_info["data_arr"][$i]=$row;
				$this->calendar_info[$id]=$row;

				$i++;
			}
		}

		return $data_info;
	}


	function getCalendarArray($include_any=FALSE) {

		$res=array();

		if ($include_any) {
			$res[0]=def("_ANY", "admin_calendar");
		}

		$cal_arr =$this->getCalendarList();
		$data_arr =(isset($cal_arr["data_arr"]) ? $cal_arr["data_arr"] : array());
		foreach ($data_arr as $key=>$val) {
			$res[$val["calendar_id"]]=$val["title"];
		}

		return $res;
	}


	function saveCalendar($data) {

		$cat_id=(int)$data["id"];
		$title=$data["title"];
		$description=$data["description"];

		if ($cat_id < 1) {

			$field_list ="title, description";
			$field_val="'".$title."', '".$description."'";

			$qtxt="INSERT INTO ".$this->_getCalendarTable()." (".$field_list.") VALUES(".$field_val.")";
			$res=$this->_insQuery($qtxt);
		}
		else {

			$qtxt ="UPDATE ".$this->_getCalendarTable()." SET title='".$title."', ";
			$qtxt.="description='".$description."' ";
			$qtxt.="WHERE calendar_id='".$cat_id."'";
			$q=$this->_query($qtxt);

			$res=$cat_id;
		}

		return $res;
	}


	function loadCalendarInfo($id) {
		$res=array();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getCalendarTable()." ";
		$qtxt.="WHERE calendar_id='".(int)$id."'";
		$q=$this->_query($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$res=mysql_fetch_array($q);
		}

		return $res;
	}


	function getCalendarInfo($id) {

		if (!isset($this->calendar_info[$id])) {
			$info=$this->loadCalendarInfo($id);
			$this->calendar_info[$id]=$info;
		}

		return $this->calendar_info[$id];
	}


	function deleteCalendar($cat_id) {

		// Delete calendar
		$qtxt ="DELETE FROM ".$this->_getCalendarTable()." ";
		$qtxt.="WHERE calendar_id='".(int)$cat_id."' LIMIT 1";
		$q=$this->_query($qtxt);

		// Delete calendar items..
	}


	function getCalendarPermList() {
		return array("view", "edit", "admin");
	}


	function loadCalendarPerm($cal_id) {
		$res=array();
		$pl=$this->getCalendarPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();

		foreach($pl as $key=>$val) {

			$role_id="/cms/calendar/".$cal_id."/".$val;
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


	function saveCalendarPerm($cat_id, $selected_items, $database_items) {

		$pl=$this->getCalendarPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();
		foreach($pl as $key=>$val) {
			if ((isset($selected_items[$val])) && (is_array($selected_items[$val]))) {

				$role_id="/cms/calendar/".$cat_id."/".$val;
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
}

?>
