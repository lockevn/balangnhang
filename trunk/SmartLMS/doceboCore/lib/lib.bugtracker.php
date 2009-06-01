<?php
/*************************************************************************/
/* DOCEBO CORE - Framework                                               */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @package  admin-library
 * @subpackage module
 * @version  $Id: lib.bugtracker.php 941 2007-01-25 11:39:03Z giovanni $
 */

class BugTracker {
	/** BugTracker manager object */
	var $btManager=NULL;

	var $lang=NULL;
	var $table_style=FALSE;

	var $feed_platform=NULL;

	/**
	 * BugTracker constructor
	 * @param string $pfm_prefix the prefix for the tables names
	 *			if not given global $prefix variable is used
	 * @param resource $dbconn the connection to the database
	 * 			if not given last connection will be used.
	 */
	function BugTracker($feed_platform="cms", $pfm_prefix=FALSE, $dbconn=NULL) {

		$this->btManager=new BugTrackerManager($pfm_prefix, $dbconn);
		$this->lang =& DoceboLanguage::createInstance('bugtracker', "framework");

		$this->setFeedPlatform($feed_platform);

	}


	function getTableStyle() {
		return $this->table_style;
	}


	function setTableStyle($style) {
		$this->table_style=$style;
	}


	function getFeedPlatform() {
		return $this->feed_platform;
	}


	function setFeedPlatform($platform) {
		$this->feed_platform=$platform;
	}


	function titleArea($text, $image = '', $alt_image = '') {
		$res="";

		if ($GLOBALS["platform"] == "cms") {
			$res=getCmsTitleArea($text, $image = '', $alt_image = '');
		}
		else {
			$res=getTitleArea($text, $image = '', $alt_image = '');
		}

		$this->includeFeed();

		return $res;
	}


	function getAppLabel($app_id) {
		$lang_arr=$this->btManager->getItemLangText($app_id);
		return $lang_arr[getLanguage()];
	}


	function getOriginalFileName($fs_filename) {

		$break_apart=explode('_', $fs_filename);
		unset($break_apart[0], $break_apart[1], $break_apart[2]);
		$fname=implode('', $break_apart);

		return $fname;
	}


	function listBugTrackerApp($vis_item) {
		$res="";

		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_APP_LIST");
		$table_summary=$table_caption;
		$vis_item=20;
		$um =& UrlManager::getInstance();
		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$head=array($this->lang->def("_TITLE"));

		$img ="<img src=\"".getPathImage('fw')."bugtracker/details.gif\" alt=\"".$this->lang->def("_ALT_SHOW")."\" ";
		$img.="title=\"".$this->lang->def("_ALT_SHOW")."\" />";
		$head[]=$img;

		$head_type=array("", "image");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink($um->getUrl());

		$ini=$tab->getSelectedElement();

		$data_info=$this->btManager->getAllData("app", 0, $ini, $vis_item);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["data_id"];


			//if ($this->btManager->checkPerm("app", $id, "view")) {
			if ($id != 17) {

				$url=$um->getUrl("op=show&appid=".$id);

				$rowcnt=array();
				$rowcnt[]="<a href=\"".$url."\">".$data_arr[$i]["data_txt"]."</a>\n";

				$img ="<img src=\"".getPathImage('fw')."bugtracker/details.gif\" alt=\"".$this->lang->def("_ALT_SHOW")."\" ";
				$img.="title=\"".$this->lang->def("_ALT_SHOW")."\" />";
				$rowcnt[]="<a href=\"".$url."\">".$img."</a>\n";

				$tab->addBody($rowcnt);
			}
			else {
				$db_tot=$db_tot-1;
			}
		}

		$res=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}


	function showAppBugs($vis_item) {
		$res="";

		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_BUGS_LIST");
		$table_summary=$table_caption;

		$um =& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);

		$can_write=$this->btManager->checkPerm("app", $app_id, "write");
		$can_moderate=$this->btManager->checkPerm("app", $app_id, "moderate");

		$role_id="/framework/bugtracker/app/".$app_id."/view";
		if (!$GLOBALS["current_user"]->matchUserRole($role_id))
				die("You can't access");


		$back_ui_url=$um->getUrl();
		$res.=getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		$title_arr=array();
		$title_arr[$um->getUrl()]=$this->lang->def("_BUG_TRACKER");
		$title_arr[]=$this->getAppLabel($app_id);
		$res.=$this->titleArea($title_arr, "bugtracker");

		$url=$um->getUrl("op=showhidesearchform&appid=".$app_id);
		if ((!isset($_SESSION["hide_bug_search_form"])) || (!$_SESSION["hide_bug_search_form"])) {
			$res.="<div class=\"search_form hide_form\">";
			$res.="<a href=\"".$url."\">".$this->lang->def("_HIDE_SEARCH_FORM")."</a></div>\n";
			$res.=$this->printSearchForm($app_id);
		}
		else {
			$res.="<div class=\"search_form show_form\">";
			$res.="<a href=\"".$url."\">".$this->lang->def("_SHOW_SEARCH_FORM")."</a></div>\n";
		}

		$tab=new typeOne($vis_item, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());


		$base_url="op=setorder&appid=".$app_id."&ord=";
		$head=array();
		$head[]="<a href=\"".$um->getUrl($base_url."title")."\">".$this->lang->def("_TITLE")."</a>\n";
		$head[]="<a href=\"".$um->getUrl($base_url."area")."\">".$this->lang->def("_APP_AREA")."</a>\n";
		//$head[]=$this->lang->def("_AUTHOR");
		$head[]="<a href=\"".$um->getUrl($base_url."date")."\">".$this->lang->def("_DATE")."</a>\n";
		$head[]="<a href=\"".$um->getUrl($base_url."status")."\">".$this->lang->def("_STATUS")."</a>\n";


		$head_type=array("", "", "", "", "image");

		if ($can_moderate) {

			$img ="<img src=\"".getPathImage('fw')."bugtracker/locked.gif\" alt=\"".$this->lang->def("_ALT_LOCKUNLOCK")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_LOCKUNLOCK")."\" />";
			$head[]="<a href=\"".$um->getUrl($base_url."closed")."\">".$img."</a>\n";
			$img ="<img src=\"".getPathImage('fw')."standard/groups.gif\" alt=\"".$this->lang->def("_ALT_ASSIGN")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_ASSIGN")."\" />";
			$head[]=$img;
			$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$head[]=$img;

			array_push($head_type, "image", "image", "image");
		}

		$img ="<img src=\"".getPathImage('fw')."bugtracker/details.gif\" alt=\"".$this->lang->def("_ALT_SHOW")."\" ";
		$img.="title=\"".$this->lang->def("_ALT_SHOW")."\" />";
		$head[]=$img;

		array_push($head_type, "image");


		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink($um->getUrl("op=show&appid=".$app_id));

		$ini=$tab->getSelectedElement();

		$bug_info=$this->btManager->getBugList($app_id, $ini, $vis_item, TRUE);
		$status_color=$this->btManager->getStatusColorArr();
		$bug_arr=$bug_info["info"];
		$user_arr=$bug_info["user"];
		$bug_label_arr=$bug_info["label"];
		$db_tot=$bug_info["tot"];


		$tot=count($bug_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$bug_arr[$i]["bug_id"];
			$details_url=$um->getUrl("op=bugdetails&appid=".$app_id."&bugid=".$id);

			$rowcnt=array();
			$txt=strip_tags(substr($bug_arr[$i]["txt"], 0, 150))." ...";
			$rowcnt[]="<a href=\"".$details_url."\" title=\"".$txt."\">".$bug_arr[$i]["title"]."</a>\n";

			$rowcnt[]=$bug_label_arr[$i]["prob_id"];
			//$rowcnt[]=$user_arr[$bug_arr[$i]["user_id"]];
			$rowcnt[]=$GLOBALS["regset"]->databaseToRegional($bug_arr[$i]["post_time"], "date");
			$rowcnt[]=$bug_label_arr[$i]["stat_id"];


			if ($can_moderate) {

				$closed=$bug_arr[$i]["closed"];

				if ($closed) {
					$img ="<img src=\"".getPathImage('fw')."bugtracker/locked.gif\" alt=\"".$this->lang->def("_UNLOCK_BUG")."\" ";
					$img.="title=\"".$this->lang->def("_UNLOCK_BUG")."\" />";
				}
				else {
					$img ="<img src=\"".getPathImage('fw')."bugtracker/unlocked.gif\" alt=\"".$this->lang->def("_LOCK_BUG")."\" ";
					$img.="title=\"".$this->lang->def("_LOCK_BUG")."\" />";
				}
				$rowcnt[]="<a href=\"".$um->getUrl("op=switchlock&appid=".$app_id."&bugid=".$id)."\">".$img."</a>\n";

				if (!$closed) {
					$img ="<img src=\"".getPathImage('fw')."standard/groups.gif\" alt=\"".$this->lang->def("_ALT_ASSIGN")."\" ";
					$img.="title=\"".$this->lang->def("_ALT_ASSIGN")."\" />";
					$rowcnt[]="<a href=\"".$um->getUrl("op=assign&appid=".$app_id."&bugid=".$id)."\">".$img."</a>\n";

					$img ="<img src=\"".getPathImage('fw')."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
					$img.="title=\"".$this->lang->def("_MOD")."\" />";
					$rowcnt[]="<a href=\"".$um->getUrl("op=editbug&appid=".$app_id."&bugid=".$id)."\">".$img."</a>\n";
				}
				else {
					$rowcnt[]="&nbsp;";
					$rowcnt[]="&nbsp;";
				}

			}


			$img ="<img src=\"".getPathImage('fw')."bugtracker/details.gif\" alt=\"".$this->lang->def("_ALT_SHOW")."\" ";
			$img.="title=\"".$this->lang->def("_ALT_SHOW")."\" />";
			$rowcnt[]="<a href=\"".$details_url."\">".$img."</a>\n";

			$status_id=$bug_arr[$i]["stat_id"];
			if (isset($status_color[$status_id]))
				$style=$status_color[$status_id];
			else
				$style=FALSE;

			$tab->addBody($rowcnt, $style);
		}


		if ($can_write) {
			$url=$um->getUrl("op=addbug&appid=".$app_id);
			$img ="<img src=\"".getPathImage('fw')."bugtracker/addbug.gif\" alt=\"".$this->lang->def('_ADD')."\" />";
			$tab->addActionAdd("<a href=\"".$url."\">".$img.$this->lang->def('_ADD')."</a>\n");
		}


		$res.=$tab->getTable().$tab->getNavBar($ini, $db_tot);
		$res.=getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		return $res;

	}


	function showHideBugSearchForm() {

		$um =& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);

		if ((!isset($_SESSION["hide_bug_search_form"])) || (!$_SESSION["hide_bug_search_form"])) {
			$_SESSION["hide_bug_search_form"]=1;
			//$this->clearBugFilter($app_id);
		}
		else {
			unset($_SESSION["hide_bug_search_form"]);
		}

		$url=$um->getUrl("op=show&appid=".$app_id, FALSE);
		jumpTo($url);
	}


	function printSearchForm($app_id) {
		$res="";

		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$um =& UrlManager::getInstance();
		$form=new Form();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);


		if (isset($_POST["clear_search"])) {
			$this->clearBugFilter($app_id);
		}
		else if (isset($_POST["do_search"])) {
			$this->setSearchItem("search_key", $app_id);
			$this->setSearchItem("app_area", $app_id);
			$this->setSearchItem("severity", $app_id);
			$this->setSearchItem("status", $app_id);
		}


		$url=$um->getUrl("op=show&appid=".$app_id);
		$res.=$form->openForm("bugtracker", $url);
		$res.=$form->openElementSpace();

		$res.=$form->getHidden("do_search", "do_search", 1);

		$search_key=$this->getSearchItem("search_key", "string", $app_id);
		$app_area=$this->getSearchItem("app_area", "bool", $app_id);
		$severity=$this->getSearchItem("severity", "bool", $app_id);
		$status=$this->getSearchItem("status", "bool", $app_id);


		$res.=$form->getTextfield($this->lang->def("_SEARCH_KEY"), "search_key", "search_key", 255, $search_key);

		$area_list=$this->btManager->getDataArr(TRUE, "app_area", $app_id, TRUE);
		$res.=$form->getDropdown($this->lang->def("_APP_AREA"), "app_area", "app_area",	$area_list, $app_area);

		$severity_list=$this->btManager->getDataArr(TRUE, "severity", 0, TRUE);
		$res.=$form->getDropdown($this->lang->def("_SEVERITY"), "severity", "severity",	$severity_list, $severity);

		$status_list=$this->btManager->getDataArr(TRUE, "status", 0, TRUE);
		$res.=$form->getDropdown($this->lang->def("_STATUS"), "status", "status",	$status_list, $status);


		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('search', 'search', $this->lang->def('_SEARCH'));
		$res.=$form->getButton('clear_search', 'clear_search', $this->lang->def('_CLEAR_SEARCH'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function clearBugFilter($app_id) {
		unset($_SESSION["bug_filter"][$app_id]);
	}


	function getSearchItem($key, $type, $app_id) {
		return $this->btManager->getSearchItem($key, $type, $app_id);
	}


	function setSearchItem($key, $app_id, $value=FALSE) {

		$set_val=FALSE;

		if ($value !== FALSE)
			$set_val=$value;
		else if (isset($_POST[$key]))
			$set_val=$_POST[$key];

		if (($set_val === FALSE) || (empty($set_val))) {
			if (isset($_SESSION["bug_filter"][$app_id][$key]))
				unset($_SESSION["bug_filter"][$app_id][$key]);
		}
		else {
			$_SESSION["bug_filter"][$app_id][$key]=$set_val;
		}
	}


	function getAddEditBugForm() {
		$res="";

		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");

		$um =& UrlManager::getInstance();
		$form=new Form();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);
		$bug_id=(int)importVar("bugid", true);

		$can_write=$this->btManager->checkPerm("app", $app_id, "write");
		$can_moderate=$this->btManager->checkPerm("app", $app_id, "moderate");
		$can_upload=$this->btManager->checkPerm("app", $app_id, "upload");


		if ($bug_id > 0) { // Edit bug
			if (!$can_moderate)
				die("You can't access");

			$bug_info=$this->btManager->getBugInfo($bug_id);

			$new=0;
			$title=$bug_info["title"];
			$text=$bug_info["txt"];
			$app_area=$bug_info["prob_id"];
			$severity=$bug_info["sev_id"];
			$status=$bug_info["stat_id"];
			$sslink=$bug_info["sslink"];
			$patch_fname=$bug_info["patch_fname"];
			$patch_desc=$bug_info["patch_desc"];
			$fwd_id=($bug_info["fwd_id"] > 0 ? $bug_info["fwd_id"] : "");
			$action_label=$this->lang->def("_EDIT_BUG").": ".$title;

		}
		else { // New bug
			if ((!$can_write) && (!$can_moderate))
				die("You can't access");

			$new=1;
			$title="";
			$text="";
			$app_area=FALSE;
			$severity=FALSE;
			$status=$this->btManager->getDefaultStatus();
			$sslink="http://";
			$action_label=$this->lang->def("_NEW_BUG");

		}


		$back_ui_url=$um->getUrl("op=show&appid=".$app_id);
		$res.=getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		$title_arr=array();
		$title_arr[$um->getUrl()]=$this->lang->def("_BUG_TRACKER");
		$title_arr[$um->getUrl("op=show&appid=".$app_id)]=$this->getAppLabel($app_id);
		$title_arr[]=$action_label;
		$res.=$this->titleArea($title_arr, "bugtracker");

		$url=$um->getUrl("op=savebug&appid=".$app_id);
		$res.=$form->openForm("bugtracker", $url, "", "", "multipart/form-data");
		$res.=$form->openElementSpace();


		$res.=$form->getHidden("bug_id", "bug_id", $bug_id);
		$res.=$form->getHidden("app_id", "app_id", $app_id);
		$res.=$form->getHidden("new", "new", $new);

		$res.=$form->getTextfield($this->lang->def("_TITLE"), "title", "title", 255, $title);
		$res.=$form->getTextarea($this->lang->def("_DESCRIPTION").":", "text", "text", $text);


		$area_list=$this->btManager->getDataArr(TRUE, "app_area", $app_id);
		$res.=$form->getDropdown($this->lang->def("_APP_AREA"), "app_area", "app_area",	$area_list, $app_area);

		$severity_list=$this->btManager->getDataArr(TRUE, "severity");
		$res.=$form->getDropdown($this->lang->def("_SEVERITY"), "severity", "severity",	$severity_list, $severity);

		if ($can_moderate) {
			$status_list=$this->btManager->getDataArr(TRUE, "status");
			$res.=$form->getDropdown($this->lang->def("_STATUS"), "status", "status",	$status_list, $status);
		}
		else {
			$res.=$form->getHidden("status", "status", $status);
		}

		$res.=$form->getTextfield($this->lang->def("_SSHOT_LINK"), "sslink", "sslink", 255, $sslink);


		if (($bug_id > 0) && ($can_upload)) {
			$res.=$form->getFilefield($this->lang->def("_BUG_PATCH"), "patch", "patch");
			$res.=$form->getSimpleTextarea($this->lang->def("_BUG_PATCH_DESC"), "patch_desc", "patch_desc",
					$patch_desc, false, false, false, 5, 36);
			$res.=$form->getHidden("old_patch", "old_patch", $patch_fname);
		}

		if (($bug_id > 0) && ($can_moderate)) {
			$res.=$form->getTextfield($this->lang->def("_MARK_BUG_DUPLICATE_OF"), "fwd_id", "fwd_id", 255, $fwd_id);
		}


		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $this->lang->def('_SAVE'));
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();


		$res.=getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		return $res;
	}


	function saveBug() {

		$um =& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);

		$bug_id=$this->btManager->saveBug($_POST);


		if (($_POST["new"]) && ($this->btManager->checkPerm("app", $app_id, "moderate")))
			$url=$um->getUrl("op=assign&appid=".$app_id."&bugid=".$bug_id, FALSE);
		else
			$url=$um->getUrl("op=show&appid=".$app_id, FALSE);


		jumpTo($url);
	}


	function switchLock() {

		$um =& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);
		$bug_id=(int)importVar("bugid", true);

		if ($this->btManager->checkPerm("app", $app_id, "moderate"))
			$this->btManager->switchLock($bug_id);

		$this->updateFeed();

		$url=$um->getUrl("op=show&appid=".$app_id, FALSE);
		jumpTo($url);
	}


	function showAssigned() {
		$res="";

		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		$table_caption=$this->lang->def("_BUG_ASSIGNED_USERS");
		$table_summary=$table_caption;

		$um =& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);
		$bug_id=(int)importVar("bugid", true);

		$back_ui_url=$um->getUrl("op=show&appid=".$app_id);
		$res.=getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		$title_arr=array();
		$title_arr[$um->getUrl()]=$this->lang->def("_BUG_TRACKER");
		$title_arr[$um->getUrl("op=show&appid=".$app_id)]=$this->getAppLabel($app_id);
		$title_arr[]=$this->lang->def("_BUG_ASSIGNED_USERS");
		$res.=$this->titleArea($title_arr, "bugtracker");


		$role_id="/framework/bugtracker/app/".$app_id."/moderate";
		if (!$GLOBALS["current_user"]->matchUserRole($role_id))
				die("You can't access");


		$tab=new typeOne(0, $table_caption, $table_summary);

		if ($this->getTableStyle() !== FALSE)
			$tab->setTableStyle($this->getTableStyle());

		$img ="<img src=\"".getPathImage('fw')."standard/user.gif\" alt=\"".$this->lang->def("_USER")."\" ";
		$img.="title=\"".$this->lang->def("_USER")."\" />";

		$head=array($img, $this->lang->def("_USER"));
		$head_type=array("image", "");

		$tab->setColsStyle($head_type);
		$tab->addHead($head);


		$user_arr=$this->btManager->getAssignedToBug($bug_id);

		foreach($user_arr as $idst=>$userid) {

			$rowcnt=array();

			$img ="<img src=\"".getPathImage('fw')."standard/user.gif\" alt=\"".$this->lang->def("_USER")." ".$userid."\" ";
			$img.="title=\"".$this->lang->def("_USER")." ".$userid."\" />";
			$rowcnt[]=$img;

			$rowcnt[]=$userid;

			$tab->addBody($rowcnt);
		}


		$url=$um->getUrl("op=assignnewuser&appid=".$app_id."&bugid=".$bug_id);
		$img ="<img src=\"".getPathImage('fw')."standard/addandremove.gif\" alt=\"".$this->lang->def('_ADDREM_USER')."\" />";
		$tab->addActionAdd("<a href=\"".$url."\">".$img.$this->lang->def('_ADDREM_USER')."</a>\n");

		$res.=$tab->getTable();

		$res.=getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		return $res;
	}


	function selAssignedUsers() {

		require_once($GLOBALS['where_framework']."/class.module/class.directory.php");
		$mdir=new Module_Directory();

		$um =& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);
		$bug_id=(int)importVar("bugid", true);

		$back_url=$um->getUrl("op=assign&appid=".$app_id."&bugid=".$bug_id, FALSE);


		if( isset($_POST['okselector']) ) {

			$arr_selection=$mdir->getSelection($_POST);
			$this->btManager->updateBugAssigendUsers($app_id, $bug_id, $arr_selection);

			// Send Bug Change notify
			$this->btManager->btNotifier->sendBugChangeNotify($app_id, $bug_id, "Bug ".$bug_id." changed");

			// Send a message to users assigned to the bug
			$this->btManager->btNotifier->sendAssignedNotify($app_id, $bug_id, $arr_selection, $this->lang);

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
				$mdir->resetSelection(array_keys($this->btManager->getAssignedToBug($bug_id)));
			}

			$regusers_idst=$mdir->aclManager->getGroupRegisteredId();
			$mdir->setUserFilter("group", array($regusers_idst));


			$back_ui_url=$um->getUrl("op=assign&appid=".$app_id."&bugid=".$bug_id);
			$GLOBALS["page"]->add(getBackUi($back_ui_url, $this->lang->def( '_BACK' )), "content");

			$url=$um->getUrl("op=assignnewuser&appid=".$app_id."&bugid=".$bug_id."&stayon=1");
			$mdir->loadSelector($url, $this->lang->def('_BUG_ASSIGNED_USERS'), "", TRUE);

			$GLOBALS["page"]->add(getBackUi($back_ui_url, $this->lang->def( '_BACK' )), "content");
		}

	}


	function showBugDetails() {
		$res="";

		$um =& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);
		$bug_id=(int)importVar("bugid", true);

		$can_view=$this->btManager->checkPerm("app", $app_id, "view");
		$can_write=$this->btManager->checkPerm("app", $app_id, "write");
		$can_moderate=$this->btManager->checkPerm("app", $app_id, "moderate");
		$can_upload=$this->btManager->checkPerm("app", $app_id, "upload");

		if (!$can_view)
			die("You can't access!");

		$bug_details=$this->btManager->getBugInfo($bug_id);


		$back_ui_url=$um->getUrl("op=show&appid=".$app_id);
		$res.=getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		$title_arr=array();
		$title_arr[$um->getUrl()]=$this->lang->def("_BUG_TRACKER");
		$title_arr[$um->getUrl("op=show&appid=".$app_id)]=$this->getAppLabel($app_id);
		$title_arr[]=$this->lang->def("_BUG_DETAILS");
		$res.=$this->titleArea($title_arr, "bugtracker");


		$res.="<table class=\"bug_details\" cellspacing=\"0\" summary=\"".$this->lang->def("_BUG_DETAILS_SUMMARY")."\">\n";
		$res.="<caption>".$this->lang->def("_BUG_DETAILS")."</caption>\n";
		$res.="<tbody>\n";

		$scope=" scope=\"col\"";
		$class="line-";
		$switch=1;

		$switch=(int)(!$switch);
		$res.="<tr><th".$scope." class=\"".$class.$switch."\">".$this->lang->def("_BUG_ID").":</th>";
		$res.="<td class=\"".$class.$switch."\">".$bug_details["bug_id"]."</td></tr>";

		$switch=(int)(!$switch);
		$res.="<tr><th".$scope." class=\"".$class.$switch."\">".$this->lang->def("_TITLE").":</th>";
		$res.="<td class=\"".$class.$switch."\">".$bug_details["title"]."</td></tr>";

		$switch=(int)(!$switch);
		$res.="<tr><th".$scope." class=\"".$class.$switch."\">".$this->lang->def("_AUTHOR").":</th>";
		$res.="<td class=\"".$class.$switch."\">".$bug_details["userid"]."</td></tr>";

		$switch=(int)(!$switch);
		$res.="<tr><th".$scope." class=\"".$class.$switch."\">".$this->lang->def("_BUG_DATE").":</th>";
		$res.="<td class=\"".$class.$switch."\">".$GLOBALS["regset"]->databaseToRegional($bug_details["post_time"])."</td></tr>";

		$switch=(int)(!$switch);
		$res.="<tr><th".$scope." class=\"".$class.$switch."\">".$this->lang->def("_BUG_LAST_MODIFIED").":</th>";
		$res.="<td class=\"".$class.$switch."\">".$GLOBALS["regset"]->databaseToRegional($bug_details["upd_time"])."</td></tr>";

		$switch=(int)(!$switch);
		$res.="<tr><th".$scope." class=\"".$class.$switch."\">".$this->lang->def("_APPLICATION").":</th>";
		$res.="<td class=\"".$class.$switch."\">".$bug_details["app_label"]."</td></tr>";

		$switch=(int)(!$switch);
		$res.="<tr><th".$scope." class=\"".$class.$switch."\">".$this->lang->def("_APP_AREA").":</th>";
		$res.="<td class=\"".$class.$switch."\">".$bug_details["prob_label"]."</td></tr>";

		$switch=(int)(!$switch);
		$res.="<tr><th".$scope." class=\"".$class.$switch."\">".$this->lang->def("_SEVERITY").":</th>";
		$res.="<td class=\"".$class.$switch."\">".$bug_details["sev_label"]."</td></tr>";

		$switch=(int)(!$switch);
		$res.="<tr><th".$scope." class=\"".$class.$switch."\">".$this->lang->def("_STATUS").":</th>";
		$res.="<td class=\"".$class.$switch."\">".$bug_details["stat_label"]."</td></tr>";

		$switch=(int)(!$switch);
		$res.="<tr><th".$scope." class=\"".$class.$switch."\">".$this->lang->def("_DESCRIPTION").":</th>";
		$res.="<td class=\"".$class.$switch."\">".$bug_details["txt"]."</td></tr>";

		if (!empty($bug_details["sslink"])) {
			$switch=(int)(!$switch);
			$res.="<tr><th".$scope." class=\"".$class.$switch."\">".$this->lang->def("_SSHOT_LINK").":</th>";
			$res.="<td class=\"".$class.$switch."\">";
			$link_txt=(strlen($bug_details["sslink"]) > 45 ? substr($bug_details["sslink"], 0, 45)." ..." : $bug_details["sslink"]);
			$res.="<a href=\"".$bug_details["sslink"]."\">".$link_txt."</a>\n";
			$res.="</td></tr>";
		}

		$res.="</tbody>\n";
		$res.="</table>\n";

		$fwd_id=(int)$bug_details["fwd_id"];
		$closed=(bool)$bug_details["closed"];
		$user_anonymous=$GLOBALS["current_user"]->isAnonymous();

		if ($fwd_id > 0) {
			$class="bug_duplicated";
			$url=$um->getUrl("op=bugdetails&appid=".$app_id."&bugid=".$fwd_id);
			$msg =$this->lang->def("_BUG_IS_A_DUPLICATE_OF")." ";
			$msg.="<b><a href=\"".$url."\">";
			$msg.=$this->lang->def("_BUG_NUMBER")." ".$fwd_id."</a></b>\n";
		}
		else if (!empty($bug_details["patch_fname"])) {
			$class="bug_patch_ready";
			$url=$um->getUrl("op=getpatch&appid=".$app_id."&bugid=".$bug_id);
			$fname=$this->getOriginalFileName($bug_details["patch_fname"]);
			$msg =$this->lang->def("_BUG_PATCH_READY");
			if (!empty($bug_details["patch_desc"]))
				$msg.="<p class=\"patch_desc\">".$bug_details["patch_desc"]."</p>\n";
			$msg.="<div class=\"download_link\">".$this->lang->def("_DOWNLOAD").": ";
			$msg.="<a href=\"".$url."\">".$fname."</a></div>\n";

			if ($can_moderate) {
				$url=$um->getUrl("op=delpatch&appid=".$app_id."&bugid=".$bug_id);
				$msg.="<div class=\"remove_patch\">";
				$msg.="<a href=\"".$url."\">".$this->lang->def("_BUG_DEL_PATCH")."</a></div>\n";
			}
		}
		else if ($closed) {
			$class="bug_closed";
			$msg=$this->lang->def("_BUG_IS_CLOSED");
		}
		else {
			$class="bug_patch_waiting";
			$msg=$this->lang->def("_BUG_PATCH_WAITING");
		}


		// Patch / Info box:
		// --------------------------------------------------------------------------
		$res.="<div class=\"".$class."\"><p>".$msg."</p></div>\n";


		// User monitoring box:
		// --------------------------------------------------------------------------
		$user_arr=$this->btManager->getAssignedToBug($bug_id);

		if ((!$closed) && (is_array($user_arr)) && (count($user_arr) > 0)) {
			$msg=$this->lang->def("_USERS_WORKING_ON_BUG").":";
			$msg.="<p class=\"user_assigned_list\">".implode(", ", $user_arr).".</p>";
			$res.="<div class=\"bug_user_assigned\"><p>".$msg."</p></div>\n";
		}


		// Notifications box:
		// --------------------------------------------------------------------------
		$msg="";

		if (!$user_anonymous) {

			$user_id=$GLOBALS["current_user"]->getIdSt();
			$nofity_active=($this->btManager->btNotifier->issetNotify("bug", $bug_id, $user_id) ? true : false);

			if (!$nofity_active) {
				$url=$um->getUrl("op=setnotify&appid=".$app_id."&bugid=".$bug_id."&status=on");
				$msg.="<a href=\"".$url."\">".$this->lang->def("_BUG_TURN_ON_NOTIFY")."</a>\n";
			}
			else {
				$url=$um->getUrl("op=setnotify&appid=".$app_id."&bugid=".$bug_id."&status=off");
				$msg.="<a href=\"".$url."\">".$this->lang->def("_BUG_TURN_OFF_NOTIFY")."</a>\n";
			}

			$res.="<div class=\"bug_user_notifications\"><p>".$msg."</p></div>\n";
		}

		// Comments:
		// --------------------------------------------------------------------------

		if (($fwd_id > 0) || ($bug_details["closed"]))
			$can_write=FALSE;

		require_once($GLOBALS['where_framework'].'/lib/lib.sysforum.php');

		$sf=new sys_forum("framework", "bug_comment", $bug_id);
		$sf->setPrefix("core");
		$sf->can_write=$can_write;
		$sf->can_moderate=$can_moderate;
		$sf->can_upload=$can_upload;

		$url=$um->getUrl("op=bugdetails&appid=".$app_id."&bugid=".$bug_id);
		$found=strpos($url, "?");
		if ($found === FALSE)
			$url.="?comment=1";
		$sf->url=$url;
		$res.=$sf->show(FALSE);



		// ------------------------------------------------------------------------
		$res.=getBackUi($back_ui_url, $this->lang->def( '_BACK' ));

		return $res;
	}


	function deletePatch() {
		$res="";
		include_once($GLOBALS['where_framework']."/lib/lib.form.php");

		$um =& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);
		$bug_id=(int)importVar("bugid", true);

		$back_url=$um->getUrl("op=bugdetails&appid=".$app_id."&bugid=".$bug_id);


		if (isset($_POST["undo"])) {
			jumpTo($back_url);
		}
		else if (isset($_POST["conf_del"])) {

			$this->btManager->deletePatch((int)$_POST["app_id"], $_POST["bug_id"]);

			jumpTo($back_url);
		}
		else {

			$bug_details=$this->btManager->getBugInfo($bug_id);
			$fname=$this->getOriginalFileName($bug_details["patch_fname"]);

			$res.=getBackUi($back_url, $this->lang->def( '_BACK' ));

			$title_arr=array();
			$title_arr[$um->getUrl()]=$this->lang->def("_BUG_TRACKER");
			$title_arr[$um->getUrl("op=show&appid=".$app_id)]=$this->getAppLabel($app_id);
			$title_arr[$um->getUrl("op=bugdetails&appid=".$app_id."&bugid=".$bug_id)]=$bug_details["title"];
			$title_arr[]=$this->lang->def("_BUG_DEL_PATCH");
			$res.=$this->titleArea($title_arr, "bugtracker");


			$form=new Form();

			$url=$um->getUrl("op=delpatch&appid=".$app_id."&bugid=".$bug_id);

			$res.=$form->openForm("bugtracker_form", $url);

			$res.=$form->getHidden("app_id", "app_id", $app_id);
			$res.=$form->getHidden("bug_id", "bug_id", $bug_id);

			$res.=getDeleteUi(
			$this->lang->def('_AREYOUSURE'),
				'<span class="text_bold">'.$this->lang->def('_TITLE').' :</span> '.$fname.'<br />',
				false,
				'conf_del',
				'undo');

			$res.=$form->closeForm();
			$res.=getBackUi($back_url, $this->lang->def( '_BACK' ));
		}

		return $res;
	}


	function getPatch() {

		$um =& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);
		$bug_id=(int)importVar("bugid", true);

		$this->btManager->getPatch($app_id, $bug_id);

	}


	function setNotify() {

		$um =& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);
		$bug_id=(int)importVar("bugid", true);
		$status=importVar("status");

		if (!$GLOBALS["current_user"]->isAnonymous()) {

			$user_id=$GLOBALS["current_user"]->getIdSt();

			switch ($status) {
				case "on": {
					$this->btManager->btNotifier->setNotify("bug", $bug_id, $user_id);
				} break;
				case "off": {
						$this->btManager->btNotifier->unsetNotify("bug", $bug_id, $user_id);
				} break;
			}
		}

		$url=$um->getUrl("op=bugdetails&appid=".$app_id."&bugid=".$bug_id, FALSE);
		jumpTo($url);
	}


	function includeFeed() {
		require_once($GLOBALS["where_framework"]."/lib/lib.rss.php");

		$fg=new FeedGenerator("fixed_bugs", NULL, "framework");
		$info=$fg->getGeneratedFeedInfo();

		if ($info !== FALSE) {
			$feed_id=$info["feed_id"];
			$alias=$info["alias"];

			$url=$GLOBALS[$this->getFeedPlatform()]["url"];

			$fg->addFeedToMeta("title", $feed_id, $alias, $url);
		}
	}


	function updateFeed() {
		require_once($GLOBALS["where_framework"]."/lib/lib.rss.php");
		$fg=new FeedGenerator("fixed_bugs", NULL, "framework");

		$max_items=$fg->getMaxFeedItems();
		$url=$GLOBALS[$this->getFeedPlatform()]["url"];

		$title=$this->lang->def("_LAST_FIXED_BUGS");
		$feed_arr=$this->btManager->getFixedBugsFeedArr($max_items, $url);


		$res=$fg->generateFeed($title, $feed_arr, FALSE, TRUE, $url);
		return $res;
	}


	function setBugsOrder() {
		$um =& UrlManager::getInstance();

		if (isset($_GET["mr_str"]))
			$um->loadOtherModRewriteParamFromVar($_GET["mr_str"]);

		$app_id=(int)importVar("appid", true);
		$ord=importVar("ord");

		$this->btManager->setBugsOrder($ord);


		$url=$um->getUrl("op=show&appid=".$app_id, FALSE);
		jumpTo($url);
	}


}




class BugTrackerManager {
	/** db connection */
	var $dbconn;
	/** prefix for the database */
	var $prefix;

	/** Notification manager object */
	var $btNotifier=NULL;

	var $localized_strings=array();
	var $status_color_array=NULL;

	/**
	 * BugTrackerManager constructor
	 * @param string $param_prefix the prefix for the tables names
	 *			if not given global $prefix variable is used
	 * @param resource $dbconn the connection to the database
	 * 			if not given last connection will be used.
	 */
	function BugTrackerManager( $param_prefix = FALSE, $dbconn = NULL ) {
		if( $param_prefix === FALSE ) {
			$this->prefix=$GLOBALS["prefix_fw"];
		} else {
			$this->prefix=$param_prefix;
		}
		$this->dbConn=$dbconn;

		$tab_monitoring=$this->_getMonitoringTable();
		$tab_assigned=$this->_getAssignedTable();
		$this->btNotifier=new BugTrackerNotifier($tab_monitoring, $tab_assigned, $param_prefix, $dbconn);
	}


	/**
	 **/
	function _getDataTable() {
		return $this->prefix."_bt_data";
	}

	/**
	 * @return string table name with the localized values
	 **/
	function _getLangTable() {
		return $this->prefix."_bt_data_lang";
	}


	/**
	 * @return string table name with the field options
	 **/
	function _getFieldOptTable() {
		return $this->prefix."_bt_fieldopt";
	}


	/**
	 * @return string table name with the saved bugs
	 **/
	function _getBugsTable() {
		return $this->prefix."_bugtracker";
	}


	/**
	 * @return string table name with the list of a bug's assigned users
	 **/
	function _getAssignedTable() {
		return $this->prefix."_bt_assigned";
	}


	/**
	 * @return string table name with the list of a bug's assigned users
	 **/
	function _getMonitoringTable() {
		return $this->prefix."_bt_monitoring";
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



	/**
	 */
	function getAllData($type, $parent=0, $ini, $vis_item) {

		$data_info=array();
		$data_info["data_arr"]=array();
		$sel_lang=getLanguage();

		$fields="t1.data_id, t1.parent_id, t1.type, t1.ord, t2.data_txt";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getDataTable()." as t1 ";
		$qtxt.="LEFT JOIN ".$this->_getLangTable()." as t2 ";
		$qtxt.="ON (t2.data_id=t1.data_id AND t2.lang='".$sel_lang."') ";
		$qtxt.="WHERE type ='".$type."' ";
		if ($parent > 0)
			$qtxt.="AND t1.parent_id='".$parent."' ";
		$qtxt.="ORDER BY ord ";
		$q=$this->_executeQuery($qtxt);

		if ($q)
			$data_info["data_tot"]=mysql_num_rows($q);
		else
			$data_info["data_tot"]=0;

		$qtxt.="LIMIT ".$ini.",".$vis_item;
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {
				$data_info["data_arr"][$i]=$row;

				$i++;
			}
		}

		return $data_info;
	}



	function getDataArr($check_perm=TRUE, $type, $parent=0, $include_any=FALSE) {

		$sel_lang=getLanguage();

		$fields="t1.data_id, t1.parent_id, t2.data_txt";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getDataTable()." as t1 ";
		$qtxt.="LEFT JOIN ".$this->_getLangTable()." as t2 ";
		$qtxt.="ON (t2.data_id=t1.data_id AND t2.lang='".$sel_lang."') ";
		$qtxt.="WHERE type ='".$type."' ";
		if ($parent > 0)
			$qtxt.="AND t1.parent_id='".$parent."' ";
		$qtxt.="ORDER BY t1.ord ";
		$q=$this->_executeQuery($qtxt);

		$res=array();

		if ($include_any)
			$res[0]=def("_ANY", "bugtracker", "framework");

		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_array($q)) {

				$can_see=TRUE;
				switch ($type) {
					case "app": {
						if ($check_perm)
							$can_see=$this->checkPerm("app", $row["data_id"], "view");
					} break;
				}

				if ($can_see)
					$res[$row["data_id"]]=$row["data_txt"];
			}
		}

		return $res;
	}


	function checkPerm($type, $id, $action) {
			$role_id="/framework/bugtracker/".$type."/".$id."/".$action;
			return $GLOBALS["current_user"]->matchUserRole($role_id);
	}


	/**
	 * @return array
	 */
	function getFlowInfo($flow_id) {

		$res=array();

		$qtxt="SELECT * FROM ".$this->_getListTable()." WHERE flow_id='$flow_id' ORDER BY ord";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row;
		}

		return $res;
	}


	/**
	 */
	function getLocalizedStrings($data_id) {

		if ((!isset($this->localized_strings[$data_id])) || (!is_array($this->localized_strings[$data_id]))
			 || (count($this->localized_strings[$data_id]) < 1)) {

			$this->localized_strings=$this->loadLocalizedStrings($data_id);
		}

		return $this->localized_strings;
	}


	/**
	 */
	function loadLocalizedStrings($data_id) {

		$res=array();

		$qtxt="SELECT data_txt, lang FROM ".$this->_getLangTable()." WHERE data_id='".$data_id."' ORDER BY lang";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_array($q)) {
				$res[$data_id][$row["lang"]]=$row["data_txt"];
			}
		}

		return $res;
	}


	/**
	 */
	function getItemLangText($data_id) {

		$locstr=$this->getLocalizedStrings($data_id);

		if (isset($locstr[$data_id]))
			return $locstr[$data_id];
		else
			return false;
	}


	/**
	 */
	function saveData($data) {

		$id=(int)$data["data_id"];
		$type=$data["type"];

		if (isset($data["parent_id"]))
			$parent=$data["parent_id"];
		else
			$parent=0;

		if ($id == 0) {

			$ord=$this->getLastOrd($this->_getDataTable(), $type)+1;

			if ($parent > 0) {
				$field_list="type, parent_id, ord";
				$field_val="'".$type."', '".$parent."', '".$ord."'";
			}
			else {
				$field_list="type, ord";
				$field_val="'".$type."', '".$ord."'";
			}

			$qtxt="INSERT INTO ".$this->_getDataTable()." (".$field_list.") VALUES(".$field_val.")";
			$id=$this->_executeInsert($qtxt);
		}


		$larr=$GLOBALS['globLangManager']->getAllLangCode();
		foreach ($larr as $key=>$val) {

			$this->addEditItemLangText($id, $parent, $val, $data["data_txt"][$val]);

		}

		$this->saveExtraOpt($id, $type);
	}


	/**
	 */
	function addEditItemLangText($data_id, $parent_id, $lang, $value) {
		$data_id=(int)$data_id;

		$where="WHERE data_id='".$data_id."' AND lang='".$lang."'";

		$qtxt="SELECT * FROM ".$this->_getLangTable()." ".$where;
		$q=$this->_executeQuery($qtxt);

		if ($q) {
			if (mysql_num_rows($q) > 0) {
				$qtxt ="UPDATE ".$this->_getLangTable()." SET ";
				if ($parent_id > 0)
					$qtxt.="parent_id='".$parent_id."', ";
				$qtxt.="data_txt='".$value."' ".$where;
			}
			else {
				if ($parent_id > 0) {
					$field_list="data_id, lang, parent_id, data_txt";
					$field_val="'".$data_id."', '".$lang."', '".$parent_id."', '".$value."'";
				}
				else {
					$field_list="data_id, lang, data_txt";
					$field_val="'".$data_id."', '".$lang."', '".$value."'";
				}

				$qtxt ="INSERT INTO ".$this->_getLangTable()." (".$field_list.") ";
				$qtxt.="VALUES(".$field_val.")";
			}
		}

		$q=$this->_executeQuery($qtxt);
	}


	/**
	 */
	function GetLastOrd($table, $type) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");
		return utilGetLastOrd($table, "ord", "type='".$type."'");
	}


	function moveItem($direction, $id_val, $type, $parent) {
		require_once($GLOBALS["where_framework"]."/lib/lib.utils.php");

		$table=$this->_getDataTable();

		$where ="type='".$type."' ";
		if ($parent > 0)
			$where.="AND parent_id='".$parent."'";

		utilMoveItem($direction, $table, "data_id", $id_val, "ord", $where);
	}


	function deleteData($data_id, $type) {

		$this->extraDelete($data_id, $type);

		$qtxt ="DELETE FROM ".$this->_getDataTable()." WHERE data_id='".(int)$data_id."'";
		$q=$this->_executeQuery($qtxt);
		$qtxt ="DELETE FROM ".$this->_getLangTable()." WHERE data_id='".(int)$data_id."'";
		$q=$this->_executeQuery($qtxt);
	}


	function extraDelete($data_id, $type) {
		$qtxt="";
		switch($type) {
			case "app": {
				$qtxt ="DELETE FROM ".$this->_getDataTable()." WHERE parent_id='".$data_id."'";
				$q=$this->_executeQuery($qtxt);
				$qtxt ="DELETE FROM ".$this->_getLangTable()." WHERE parent_id='".$data_id."'";
				$q=$this->_executeQuery($qtxt);
			} break;
		}
	}



	function saveExtraOpt($id, $type) {

		switch($type) {
			case "severity": {
			} break;
			case "status": {
				$opt["statcol"]=$_POST["statcol"];
				$this->saveFieldOpt($id, $opt);
			} break;
		}

	}


	function loadFieldOpt($id) {
		$opt=array();

		$qtxt="SELECT * FROM ".$this->_getFieldOptTable()." WHERE field_id='".$id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_array($q)) {
				$opt[$row["my_key"]]=$row["my_val"];
			}
		}

		return $opt;
	}


	function saveFieldOpt($id, $opt) {

		$qtxt="DELETE FROM ".$this->_getFieldOptTable()." WHERE field_id='".$id."';";
		$q=$this->_executeQuery($qtxt);

		foreach ($opt as $key=>$val) {
			if ($val) {
				$qtxt ="INSERT INTO ".$this->_getFieldOptTable()." ";
				$qtxt.="(field_id, my_key, my_val) VALUES ('".$id."', '".$key."', '".$val."')";
				$q=$this->_executeQuery($qtxt);
			}
		}
	}



	function getBtAppPermList() {
		return array("view", "write", "moderate", "upload");
	}


	function loadBtAppSavedPerm($idApp) {
		$res=array();
		$pl=$this->getBtAppPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();

		foreach($pl as $key=>$val) {

			$role_id="/framework/bugtracker/app/".$idApp."/".$val;
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


	function saveBtAppPerm($idApp, $selected_items, $database_items) {

		$pl=$this->getBtAppPermList();
		$acl_manager=& $GLOBALS['current_user']->getACLManager();
		foreach($pl as $key=>$val) {
			if ((isset($selected_items[$val])) && (is_array($selected_items[$val]))) {

				$role_id="/framework/bugtracker/app/".$idApp."/".$val;
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


	function getDefaultStatus() {
		$res=0;

		$qtxt="SELECT data_id FROM ".$this->_getDataTable()." WHERE type='status' ORDER BY ord LIMIT 0,1";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$res=$row["data_id"];
		}

		return $res;
	}



	function saveBug($data) {
		$res=false;

		$title=(!empty($data["title"]) ? $data["title"] : $this->lang->def("_NOTITLE"));
		$txt=$data["text"];
		$app_id=(int)$data["app_id"];
		$prob_id=(int)$data["app_area"];
		$sev_id=(int)$data["severity"];
		$stat_id=(int)$data["status"];
		$user_id=(int)$GLOBALS["current_user"]->getIdSt();
		$sslink=($data["sslink"] != "http://" ? $data["sslink"] : "");

		$bug_id=(int)$data["bug_id"];

		if ($bug_id < 1) { // New Bug
			// TODO: check perm
			$qtxt ="INSERT INTO ".$this->_getBugsTable()." ";
			$qtxt.="(app_id, prob_id, sev_id, stat_id, user_id, title, txt, sslink, post_time, upd_time) ";
			$qtxt.="VALUES ('".$app_id."', '".$prob_id."', '".$sev_id."', '".$stat_id."', '".$user_id."', ";
			$qtxt.="'".$title."', '".$txt."', '".$sslink."', NOW(), NOW())";

			$res=$this->_executeInsert($qtxt);
		}
		else { // Edit bug
			// TODO: check perm

			$patch=$this->savePatch();

			$qtxt ="UPDATE ".$this->_getBugsTable()." SET prob_id='".$prob_id."', sev_id='".$sev_id."', ";
			$qtxt.="stat_id='".$stat_id."', title='".$title."', ";
			$qtxt.="txt='".$txt."', sslink='".$sslink."', upd_time=NOW() ";
			if (($patch !== FALSE) && (!empty($patch))) {
				$qtxt.=", patch_fname='".$patch."' ";
			}
			if (isset($data["patch_desc"]))
				$qtxt.=", patch_desc='".$data["patch_desc"]."' ";
			if (isset($data["fwd_id"]))
				$qtxt.=", fwd_id='".(int)$data["fwd_id"]."' ";
			$qtxt.="WHERE app_id='".$app_id."' AND bug_id='".$bug_id."' LIMIT 1";

			$q=$this->_executeQuery($qtxt);
			$res=$bug_id;

			if ($q) { // Send Bug Change notify
				$this->btNotifier->sendBugChangeNotify($app_id, $bug_id, "Bug ".$bug_id." changed");
			}
		}

		return $res;
	}


	function savePatch() {
		$res=FALSE;
		require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');

		$bt_files_path="/doceboCore/bugtracker/";

		if ((isset($_FILES["patch"])) && (!empty($_FILES["patch"]["name"]))) {

			$fname=$_FILES["patch"]["name"];
			$patch_fname=$GLOBALS["current_user"]->getIdSt()."_".rand(10,99)."_".time()."_".$fname;

			$tmp_fname=$_FILES["patch"]["tmp_name"];

			sl_open_fileoperations();
			$f1=sl_upload($tmp_fname, $bt_files_path.$patch_fname);
			sl_close_fileoperations();

			if ($f1)
				$res=$patch_fname; else die("Upload failed");
		}

		return $res;
	}


	function getSearchItem($key, $type, $app_id) {

		if (isset($_SESSION["bug_filter"][$app_id][$key])) {
			return $_SESSION["bug_filter"][$app_id][$key];
		}
		else {

			switch ($type) {
				case "string": {
					return "";
				} break;
				case "bool": {
					return FALSE;
				} break;
			}
		}
	}


	function getBugList($app_id, $ini, $vis_item, $use_search=FALSE) {

		$bug_info=array();
		$bug_info["info"]=array();
		$bug_info["label"]=array();
		$bug_info["user"]=array();
		$sel_lang=getLanguage();

		$fields="*";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getBugsTable()." as t1 ";
		$qtxt.="WHERE t1.app_id ='".$app_id."' ";

		if ($use_search) {

			$search_key=$this->getSearchItem("search_key", "string", $app_id);
			$app_area=$this->getSearchItem("app_area", "bool", $app_id);
			$severity=$this->getSearchItem("severity", "bool", $app_id);
			$status=$this->getSearchItem("status", "bool", $app_id);

			if ((int)$search_key > 0)
				$qtxt.="AND t1.bug_id='".$search_key."' ";
			else if (!empty($search_key))
				$qtxt.="AND (t1.title LIKE '%".$search_key."%' OR t1.txt LIKE '%".$search_key."%') ";

			if ($app_area !== FALSE)
				$qtxt.="AND t1.prob_id='".$app_area."' ";

			if ($severity !== FALSE)
				$qtxt.="AND t1.sev_id='".$severity."' ";

			if ($status !== FALSE)
				$qtxt.="AND t1.stat_id='".$status."' ";
		}

		$ord=$this->getBugsOrder();
		$qtxt.="ORDER BY ".$ord["field"]." ".$ord["type"]." ";
		$q=$this->_executeQuery($qtxt);

		if ($q)
			$bug_info["tot"]=mysql_num_rows($q);
		else
			$bug_info["tot"]=0;

		$idst_arr=array();

		$qtxt.="LIMIT ".$ini.",".$vis_item;
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$i=0;
			while($row=mysql_fetch_array($q)) {
				$bug_id=$row["bug_id"];
				$bug_info["info"][$i]=$row;
				$lang_arr=$this->getItemLangText($row["app_id"]);
				$bug_info["label"][$i]["app_id"]=$lang_arr[$sel_lang];
				$lang_arr=$this->getItemLangText($row["prob_id"]);
				$bug_info["label"][$i]["prob_id"]=$lang_arr[$sel_lang];
				$lang_arr=$this->getItemLangText($row["sev_id"]);
				$bug_info["label"][$i]["sev_id"]=$lang_arr[$sel_lang];
				$lang_arr=$this->getItemLangText($row["stat_id"]);
				$bug_info["label"][$i]["stat_id"]=$lang_arr[$sel_lang];

				if (!in_array($row["user_id"], $idst_arr))
					$idst_arr[]=$row["user_id"];

				$i++;
			}
		}

		if (count($idst_arr) > 0) {
			$acl_manager=$GLOBALS["current_user"]->getAclManager();
			$user_info=$acl_manager->getUsers($idst_arr);
			foreach ($idst_arr as $idst) {
				$bug_info["user"][$idst]=$acl_manager->relativeId($user_info[$idst][ACL_INFO_USERID]);
			}
		}

		return $bug_info;
	}


	function loadStatusColorArr() {
		$res=array();

		$fields="t1.data_id, t2.my_val as color";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getDataTable()." as t1, ";
		$qtxt.=$this->_getFieldOptTable()." as t2 ";
		$qtxt.="WHERE t1.type ='status' AND t2.my_key='statcol' ";
		$qtxt.="AND t1.data_id=t2.field_id ";
		$qtxt.="ORDER BY ord ";
		$q=$this->_executeQuery($qtxt);


		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_array($q)) {
				$res[$row["data_id"]]=$row["color"];
			}
		}

		return $res;
	}


	function getStatusColorArr() {

		if (!is_array($this->status_color_array))
			$this->status_color_array=$this->loadStatusColorArr();

		return $this->status_color_array;
	}


	function getBugInfo($bug_id) {
		$res=array();

		$sel_lang=getLanguage();

		$qtxt ="SELECT * FROM ".$this->_getBugsTable()." as t1 ";
		$qtxt.="WHERE bug_id ='".$bug_id."' ";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);

			$res=$row;

			$acl_manager=$GLOBALS["current_user"]->getAclManager();
			$user_info=$acl_manager->getUsers(array($row["user_id"]));
			$res["userid"]=$acl_manager->relativeId($user_info[$row["user_id"]][ACL_INFO_USERID]);

			$lang_arr=$this->getItemLangText($row["app_id"]);
			$res["app_label"]=$lang_arr[$sel_lang];
			$lang_arr=$this->getItemLangText($row["prob_id"]);
			$res["prob_label"]=$lang_arr[$sel_lang];
			$lang_arr=$this->getItemLangText($row["sev_id"]);
			$res["sev_label"]=$lang_arr[$sel_lang];
			$lang_arr=$this->getItemLangText($row["stat_id"]);
			$res["stat_label"]=$lang_arr[$sel_lang];
		}

		return $res;
	}


	function switchLock($bug_id) {

		$qtxt="SELECT closed FROM ".$this->_getBugsTable()." WHERE bug_id='".$bug_id."'";
		$q=$this->_executeQuery($qtxt);

		if (($q) && (mysql_num_rows($q) > 0)) {
			$row=mysql_fetch_array($q);
			$closed=$row["closed"];
		}
		else
			return FALSE;

		if ($closed == 1)
			$closed=0;
		else
			$closed=1;

		$qtxt="UPDATE ".$this->_getBugsTable()." SET closed='".$closed."' WHERE bug_id='".$bug_id."'";
		$q=$this->_executeQuery($qtxt);

		return $q;
	}


	function getAssignedToBug($bug_id) {
		$res=array();

		$qtxt="SELECT user_id FROM ".$this->_getAssignedTable()." WHERE bug_id='".$bug_id."'";
		$q=$this->_executeQuery($qtxt);

		$idst_arr=array();

		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_array($q)) {
				$idst_arr[]=$row["user_id"];
			}
		}

		if (count($idst_arr) > 0) {
			$acl_manager=$GLOBALS["current_user"]->getAclManager();
			$user_info=$acl_manager->getUsers($idst_arr);
			foreach ($idst_arr as $idst) {
				$res[$idst]=$acl_manager->relativeId($user_info[$idst][ACL_INFO_USERID]);
			}
		}

		return $res;
	}


	function updateBugAssigendUsers($app_id, $bug_id, $assigned_idst) {

		if (!$this->checkPerm("app", $app_id, "moderate"))
			die("You can't access!");

		$qtxt="DELETE FROM ".$this->_getAssignedTable()." WHERE bug_id='".(int)$bug_id."'";
		$q=$this->_executeQuery($qtxt);

		foreach ($assigned_idst as $idst) {
			$qtxt ="INSERT INTO ".$this->_getAssignedTable()." (bug_id, user_id) ";
			$qtxt.="VALUES ('".(int)$bug_id."', '".$idst."')";
			$q=$this->_executeQuery($qtxt);
		}

	}


	function deletePatch($app_id, $bug_id) {
		require_once($GLOBALS['where_framework']."/lib/lib.upload.php");

		if (!$this->checkPerm("app", $app_id, "moderate"))
			die("You can't access!");

		$bt_files_path="/doceboCore/bugtracker/";

		$bug_details=$this->getBugInfo($bug_id);
		$fname=$bug_details["patch_fname"];

		if (!empty($fname)) {
			sl_unlink($bt_files_path.$fname);
		}

		$qtxt="UPDATE ".$this->_getBugsTable()." SET patch_fname='', patch_desc=NULL WHERE bug_id='".$bug_id."'";
		$q=$this->_executeQuery($qtxt);
	}


	function getPatch($app_id, $bug_id) {
		require_once($GLOBALS['where_framework']."/lib/lib.download.php");

		if (!$this->checkPerm("app", $app_id, "view"))
			die("You can't access!");

		$bt_files_path="/doceboCore/bugtracker/";

		$bug_details=$this->getBugInfo($bug_id);
		$fname=$bug_details["patch_fname"];
		$ext=end(explode(".", $fname));

		sendFile($bt_files_path, $fname, $ext);
	}


	function getFixedBugsFeedArr($max_items, $base_url="") {
		$res=array();

		$um =& UrlManager::getInstance();

		$fields="bug_id, app_id, title, txt, upd_time";
		$qtxt ="SELECT ".$fields." FROM ".$this->_getBugsTable()." WHERE closed='1' ";
		$qtxt.="ORDER BY upd_time DESC LIMIT 0,".(int)$max_items;

		$q=$this->_executeQuery($qtxt);


		$i=0;
		if (($q) && (mysql_num_rows($q) > 0)) {
			while($row=mysql_fetch_array($q)) {
				// TODO:
				// add the bug only if is visible to anonymous users
				$timestamp=$GLOBALS["regset"]->databaseToTimestamp($row["upd_time"]);
				$url=$um->getUrl("op=bugdetails&appid=".$row["app_id"]."&bugid=".$row["bug_id"]);

				$res[$i]["title"]=$row["title"];
				$res[$i]["description"]=$row["txt"];
				$res[$i]["url"]=$base_url.$url;
				$res[$i]["date"]=date("r", $timestamp);
				$i++;
			}
		}

		return $res;
	}


	function setBugsOrder($ord) {

		switch ($ord) {
			case "title": {
				$field="t1.title";
				$default_type="ASC";
			} break;
			case "area": {
				$field="t1.prob_id";
				$default_type="ASC";
			} break;
			case "date": {
				$field="t1.upd_time";
				$default_type="DESC";
			} break;
			case "status": {
				$field="t1.stat_id";
				$default_type="ASC";
			} break;
			case "closed": {
				$field="t1.closed";
				$default_type="DESC";
			} break;
		}

		if ((isset($_SESSION["bugs_order"]["field"])) &&
		    ($field == $_SESSION["bugs_order"]["field"])) {

			if ($_SESSION["bugs_order"]["type"] == "ASC")
				$_SESSION["bugs_order"]["type"]="DESC";
			else
				$_SESSION["bugs_order"]["type"]="ASC";
		}
		else {
			$_SESSION["bugs_order"]["field"]=$field;
			$_SESSION["bugs_order"]["type"]=$default_type;
		}

	}


	function getBugsOrder() {

		$field=(isset($_SESSION["bugs_order"]["field"]) ? $_SESSION["bugs_order"]["field"] : "t1.upd_time");
		$type=(isset($_SESSION["bugs_order"]["type"]) ? $_SESSION["bugs_order"]["type"] : "DESC");

		$res=array();
		$res["field"]=$field;
		$res["type"]=$type;

		return $res;
	}


}



class BugTrackerAdmin {
	/** BugTracker manager object */
	var $btManager=NULL;

	var $lang=NULL;

	/**
	 * BugTrackerAdmin constructor
	 * @param string $pfm_prefix the prefix for the tables names
	 *			if not given global $prefix variable is used
	 * @param resource $dbconn the connection to the database
	 * 			if not given last connection will be used.
	 */
	function BugTrackerAdmin($pfm_prefix=FALSE, $dbconn=NULL) {

		$this->btManager=new BugTrackerManager($pfm_prefix, $dbconn);
		$this->lang =& DoceboLanguage::createInstance('admin_bugtracker', "framework");

	}


	function getHead() {
		$res="";

		$op=(isset($_GET["op"]) ? $_GET["op"] : "");

		$title=$this->lang->def("_BUGTRACKER");
		$back_url="index.php?modname=bugtracker&amp;op=bugtracker";

			if ((isset($_GET["parent"])) && ($_GET["parent"] > 0)) {
				$voice_title_arr=$this->btManager->getItemLangText($_GET["parent"]);
				$voice_title=$voice_title_arr[getLanguage()];
			}
			else if ((isset($_GET["id"])) && ($_GET["id"] > 0)) {
				$voice_title_arr=$this->btManager->getItemLangText($_GET["id"]);
				$voice_title=$voice_title_arr[getLanguage()];
			}
			else
				$voice_title="";

		switch($op) {
			case "editapp": {
				$title=array($back_url=>$title);
				$title[]=$this->lang->def("_SUBITEMS_OF").": ".$voice_title;
			} break;
			case "edit": {
				$title=array($back_url=>$title);
				$title[]=$this->lang->def("_EDIT_ITEM").": ".$voice_title;
			} break;
		}

		$res.=getTitleArea($title, "bugtracker");
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

		if ($url === FALSE)
			$url="index.php?modname=bugtracker&amp;op=bugtracker";

		$res.=getBackUi($url, $this->lang->def( '_BACK' ));
		return $res;
	}


	function drawDataTable($type, $parent=0, $extra_col=FALSE, $extra_col_type=FALSE) {
		$res="";

		$vis_item=$GLOBALS["framework"]["visuItem"];

		require_once($GLOBALS["where_framework"]."/lib/lib.newtypeone.php");

		switch($type) {
			case "app": {
				$table_caption=$this->lang->def("_APP_LIST");
				$table_summary=$table_caption;
			} break;
			case "app_area": {
				$table_caption=$this->lang->def("_APP_AREA_LIST");
				$table_summary=$table_caption;
			} break;
			case "severity": {
				$table_caption=$this->lang->def("_SEVERITY_LIST");
				$table_summary=$table_caption;
			} break;
			case "status": {
				$table_caption=$this->lang->def("_STATUS_LIST");
				$table_summary=$table_caption;
			} break;
		}

		$tab=new typeOne($vis_item, $table_caption, $table_summary);
		$tab->setTableStyle("type-one distance");

		$url="index.php?modname=bugtracker&amp;";
		if ($parent > 0)
			$url.="parent=".$parent."&amp;";

		$head=array($this->lang->def("_TITLE"));
		$img ="<img src=\"".getPathImage()."standard/down.gif\" alt=\"".$this->lang->def("_MOVE_DOWN")."\" ";
		$img.="title=\"".$this->lang->def("_MOVE_DOWN")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage()."standard/up.gif\" alt=\"".$this->lang->def("_MOVE_UP")."\" ";
		$img.="title=\"".$this->lang->def("_MOVE_UP")."\" />";
		$head[]=$img;

		if (($extra_col !== FALSE) && (is_array($extra_col))) {
			foreach($extra_col as $col) {
				$head[]=$col["img"];
			}
		}

		$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
		$img.="title=\"".$this->lang->def("_MOD")."\" />";
		$head[]=$img;
		$img ="<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
		$img.="title=\"".$this->lang->def("_DEL")."\" />";
		$head[]=$img;

		$head_type=array('', 'image', 'image');
		if (($extra_col_type !== FALSE) && (is_array($extra_col_type)))
			foreach($extra_col_type as $val)
				$head_type[]=$val;
		array_push($head_type, "image", "image");


		$tab->setColsStyle($head_type);
		$tab->addHead($head);

		$tab->initNavBar('ini', 'link');
		$tab->setLink("index.php?modname=bugtracker&amp;op=bugtracker&amp;type=".$type);

		if ((isset($_GET["type"])) && ($_GET["type"] == $type))
			$ini=$tab->getSelectedElement();
		else
			$ini=0;

		$data_info=$this->btManager->getAllData($type, $parent, $ini, $vis_item);
		$data_arr=$data_info["data_arr"];
		$db_tot=$data_info["data_tot"];

		$tot=count($data_arr);
		for($i=0; $i<$tot; $i++ ) {

			$id=$data_arr[$i]["data_id"];

			$rowcnt=array();
			$rowcnt[]=$data_arr[$i]["data_txt"];

			if ($ini+$i < $db_tot-1) {
				$img ="<img src=\"".getPathImage()."standard/down.gif\" alt=\"".$this->lang->def("_MOVE_DOWN")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_DOWN")."\" />";
				$rowcnt[]="<a href=\"".$url."op=movedown&amp;type=".$type."&amp;id=".$id."\">".$img."</a>";
			}
			else
				$rowcnt[]="&nbsp;";

			if ($ini+$i > 0) {
				$img ="<img src=\"".getPathImage()."standard/up.gif\" alt=\"".$this->lang->def("_MOVE_UP")."\" ";
				$img.="title=\"".$this->lang->def("_MOVE_UP")."\" />";
				$rowcnt[]="<a href=\"".$url."op=moveup&amp;type=".$type."&amp;id=".$id."\">".$img."</a>";
			}
			else
				$rowcnt[]="&nbsp;";

			if (($extra_col !== FALSE) && (is_array($extra_col))) {
				foreach($extra_col as $col) {

					$extra="";
					if ((isset($col["extra"])) && (is_array($col["extra"]))) {
						foreach ($col["extra"] as $extra_id=>$extra_name) {
							if (isset($data_arr[$i][$extra_id]))
								$extra.="&amp;".$extra_name."=".$data_arr[$i][$extra_id];
						}
					}

					$rowcnt[]="<a href=\"".$url."op=".$col["op"]."&amp;type=".$type.$extra."\">".$col["img"]."</a>";
				}
			}

			$img ="<img src=\"".getPathImage()."standard/mod.gif\" alt=\"".$this->lang->def("_MOD")."\" ";
			$img.="title=\"".$this->lang->def("_MOD")."\" />";
			$rowcnt[]="<a href=\"".$url."op=edit&amp;type=".$type."&amp;id=".$id."\">".$img."</a>";
			$img ="<img src=\"".getPathImage()."standard/rem.gif\" alt=\"".$this->lang->def("_DEL")."\" ";
			$img.="title=\"".$this->lang->def("_DEL")."\" />";
			$rowcnt[]="<a href=\"".$url."op=del&amp;type=".$type."&amp;id=".$id."\">".$img."</a>";

			$tab->addBody($rowcnt);

		}

		$url="index.php?modname=bugtracker&amp;op=add&amp;type=".$type;
		if ($parent > 0)
			$url.="&amp;parent=".$parent;
		$img ="<img src=\"".getPathImage()."standard/add.gif\" alt=\"".$this->lang->def('_ADD')."\" />";
		$tab->addActionAdd("<a href=\"".$url."\">".$img.$this->lang->def('_ADD')."</a>\n");

		$res=$tab->getTable().$tab->getNavBar($ini, $db_tot);

		return $res;
	}


	function drawAddEditDataForm($type, $data_id=0, $parent_id=0) {
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$form=new Form();
		$res="";

		$url="index.php?modname=bugtracker";
		if ($parent_id > 0)
			$url.="&amp;parent=".$parent_id;

		if ($data_id == 0) {
			$res.=$form->openForm("bugtracker_form", $url."&amp;op=insnew");
			$submit_lbl=$this->lang->def("_INSERT");

			$stored_val=NULL;
			$extra_opt=array();
		}
		else if ($data_id > 0) {
			$res.=$form->openForm("bugtracker_form", $url."&amp;op=save");
			$submit_lbl=$this->lang->def("_MOD");

			$stored_val["data_txt"]=$this->btManager->getItemLangText($data_id);
			$extra_opt=$this->btManager->loadFieldOpt($data_id);
		}

		$res.=$form->openElementSpace();

		$res.=$this->multi_lang_field($form, "data_txt", $this->lang->def("_TITLE"), $stored_val);

		$res.=$this->getExtraFormItems($form, $type, $extra_opt);

		$res.=$form->getHidden("data_id", "data_id", $data_id);
		$res.=$form->getHidden("parent_id", "parent_id", $parent_id);
		$res.=$form->getHidden("type", "type", $type);

		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $submit_lbl);
		$res.=$form->getButton('undo', 'undo', $this->lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();

		return $res;
	}


	function multi_lang_field(& $form, $field_name, $field_lbl, $field_val=NULL, $type="text") {
		$res="";

		if ($field_val == NULL)
			$field_val=array();

		$larr=$GLOBALS['globLangManager']->getAllLangCode();
		foreach ($larr as $key=>$val) {

			$field=$field_name."[".$val."]";
			$field_id=preg_replace("/(\\[|\\])/", "_", $field);

			if (isset($field_val[$field_name][$val]))
				$stored_val=$field_val[$field_name][$val];
			else
				$stored_val=$field_lbl;

			if ($type == "text")
				$res.=$form->getTextfield($field_lbl." (".$val.")", $field_id, $field, 255, $stored_val);
			else if ($type == "textarea")
				$res.=$form->getSimpleTextarea($field_lbl." (".$val.")", $field_id, $field, $stored_val);
		}

		return $res;
	}


	function getExtraFormItems(& $form, $type, $extra_opt) {
		$res="";

		switch($type) {
			case "severity": {
			} break;
			case "status": {
				$statcol=(isset($extra_opt["statcol"]) ? $extra_opt["statcol"] : NULL);
				$col_arr=$this->getColorsArr();
				$res.=$form->getDropdown($this->lang->def("_COLOR"), "statcol", "statcol", $col_arr , $statcol);
			} break;
		}

		return $res;
	}


	function getColorsArr() {
		$col=array();

		$col["white"]=$this->lang->def("_WHITE");
		$col["lightRed"]=$this->lang->def("_LIGHT_RED");
		$col["lightOrange"]=$this->lang->def("_LIGHT_ORANGE");
		$col["lightYellow"]=$this->lang->def("_LIGHT_YELLOW");
		$col["lightGreen"]=$this->lang->def("_LIGHT_GREEN");
		$col["lightCyan"]=$this->lang->def("_LIGHT_CYAN");
		$col["lightBlue"]=$this->lang->def("_LIGHT_BLUE");
		$col["lightViolet"]=$this->lang->def("_LIGHT_VIOLET");
		$col["darkRed"]=$this->lang->def("_DARK_RED");
		$col["darkOrange"]=$this->lang->def("_DARK_ORANGE");
		$col["darkYellow"]=$this->lang->def("_DARK_YELLOW");
		$col["darkGreen"]=$this->lang->def("_DARK_GREEN");
		$col["darkCyan"]=$this->lang->def("_DARK_CYAN");
		$col["darkBlue"]=$this->lang->def("_DARK_BLUE");
		$col["darkViolet"]=$this->lang->def("_DARK_VIOLET");
		$col["lightGrey"]=$this->lang->def("_LIGHT_GREY");
		$col["darkGrey"]=$this->lang->def("_DARK_GREY");
		$col["black"]=$this->lang->def("_BLACK");

		return $col;
	}


	function deleteDataForm($type, $data_id=0, $parent_id) {

		include_once($GLOBALS['where_framework']."/lib/lib.form.php");

		$out=& $GLOBALS['page'];
		$out->setWorkingZone("content");

		$back_url="index.php?modname=bugtracker&op=bugtracker";
		if ($parent_id > 0)
			$back_url.="&parent=".$parent_id;

		if (isset($_POST["undo"])) {
			jumpTo($back_url);
		}
		else if (isset($_POST["conf_del"])) {

			$this->btManager->deleteData((int)$_POST["data_id"], $_POST["type"]);

			jumpTo($back_url);
		}
		else {

			$id=(int)importVar("id");
			$stored_val["data_txt"]=$this->btManager->getItemLangText($id);
			$data_txt=$stored_val["data_txt"][getLanguage()];

			$out->add(getTitleArea($this->lang->def("_BUGTRACKER"), "bugtracker"));

			$out->add("<div class=\"std_block\">\n");

			$form=new Form();

			$url="index.php?modname=bugtracker&amp;op=del&amp;type=".$type."&amp;id=".$id;
			if ($parent_id > 0)
				$url.="&amp;parent=".$parent_id;
			$out->add($form->openForm("bugtracker_form", $url));

			$out->add($form->getHidden("data_id", "data_id", $data_id));
			$out->add($form->getHidden("parent_id", "parent_id", $parent_id));
			$out->add($form->getHidden("type", "type", $type));

			$out->add(getDeleteUi(
			$this->lang->def('_AREYOUSURE'),
				'<span class="text_bold">'.$this->lang->def('_TITLE').' :</span> '.$data_txt.'<br />',
				false,
				'conf_del',
				'undo'));

			$out->add($form->closeForm());
			$out->add("</div>\n");
		}
	}


	function edtiAppPerm(& $out, $id) {
		require_once($GLOBALS['where_framework']."/lib/lib.simplesel.php");

		$res="";


		$ssel=new SimpleSelector(true, $this->lang);

		$perm=array();
		$perm["view"]["img"]=getPathImage()."standard/view.gif";
		$perm["view"]["alt"]=$this->lang->def("_ALT_VIEW");
		$perm["write"]["img"]=getPathImage()."standard/add.gif";
		$perm["write"]["alt"]=$this->lang->def("_ADD");
		$perm["moderate"]["img"]=getPathImage()."directory/group_moderate.gif";
		$perm["moderate"]["alt"]=$this->lang->def("_ALT_MODERATE");
		$perm["upload"]["img"]=getPathImage()."standard/attach.gif";
		$perm["upload"]["alt"]=$this->lang->def("_ALT_UPLOAD");

		$ssel->setPermList($perm);

		$url="index.php?modname=bugtracker&amp;op=editperm&amp;type=".$_GET["type"]."&amp;id=".$id;
		$back_url="index.php?modname=bugtracker&amp;op=bugtracker";
		$ssel->setLinks($url, $back_url);

		$op=$ssel->getOp();

		if (($op == "main") || ($op == "manual_init") )
			$saved_data=$this->btManager->loadBtAppSavedPerm($id);

		$page_body="";
		$full_page="";

		switch($op) {

			case "main": {
				$ssel->setSavedData($saved_data);
				$page_body=$ssel->loadSimpleSelector();
			} break;

			case "manual_init":{

				// Saving permissions of simple selector
				$save_info=$ssel->getSaveInfo();
				$this->btManager->saveBtAppPerm($id, $save_info["selected"], $save_info["database"]);

				$ssel->setSavedData($saved_data);
				$full_page=$ssel->loadManualSelector($this->lang->def( '_BUGTRACKER_PERM' ));
			} break;
			case "manual": {
				$full_page=$ssel->loadManualSelector($this->lang->def( '_BUGTRACKER_PERM' ));
			} break;

			case "save_manual": {

				// Saving permissions of manual selector
				$save_info=$ssel->getSaveInfo();
				$this->btManager->saveBtAppPerm($id, $save_info["selected"], $save_info["database"]);

				jumpTo(str_replace("&amp;", "&", $url));
			} break;

			case "save": {

				// Saving permissions of simple selector
				$save_info=$ssel->getSaveInfo();
				$this->btManager->saveBtAppPerm($id, $save_info["selected"], $save_info["database"]);

				jumpTo(str_replace("&amp;", "&", $back_url));
			} break;

		}

		if (!empty($full_page))
			$out->add($full_page);

		if (!empty($page_body)) {
			// If we have only the page body, then better to add the area title.
			$ta_array=array();
			$ta_array["index.php?modname=bugtracker&amp;op=bugtracker"]=$this->lang->def("_BUGTRACKER");
			$ta_array[]=$this->lang->def( '_BUGTRACKER_PERM' );

			$out->add($this->getHead());
			$out->add($page_body);
			$out->add($this->getFooter());
		}
	}

}


class BugTrackerNotifier {
	/** db connection */
	var $dbconn;
	/** prefix for the database */
	var $prefix;



	/**
	 * BugTrackerNotifier constructor
	 * @param string $param_prefix the prefix for the tables names
	 *			if not given global $prefix variable is used
	 * @param resource $dbconn the connection to the database
	 * 			if not given last connection will be used.
	 */
	function BugTrackerNotifier( $tab_monitoring, $tab_assigned, $param_prefix = FALSE, $dbconn = NULL ) {
		if( $param_prefix === FALSE ) {
			$this->prefix=$GLOBALS["prefix_fw"];
		} else {
			$this->prefix=$param_prefix;
		}
		$this->dbconn=$dbconn;

		$this->tab_monitoring=$tab_monitoring;
		$this->tab_assigned=$tab_assigned;
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


	/**
	 **/
	function _getMonitoringTable() {
		return $this->tab_monitoring;
	}


	/**
	 **/
	function _getAssignedTable() {
		return $this->tab_assigned;
	}



	/**
	 * Register a new notify
	 * @param string	$type 	specify the type of the notify
	 * @param int		$item_id  		specify the id of the resource
	 * @param int		$user_idst 		the user idst
	 *
	 * @return	bool	true if success false otherwise
	 */
	function setNotify($type, $item_id, $user_idst) {
		$query_notify = "
		INSERT INTO ".$this->_getMonitoringTable()."
		( item_id, user_idst, item_type ) VALUES (
			'".$item_id."',
			'".$user_idst."',
			'".$type."' )";
		return $this->_executeQuery($query_notify);
	}

	/**
	 * Erase a register notify
	 * @param string	$type 	specify the type of the notify
	 * @param int		$item_id  		specify the id of the resource
	 * @param int		$user_idst 		the user idst
	 *
	 * @return	bool	true if success false otherwise
	 */
	function unsetNotify($type, $item_id, $user_idst) {
		$query_notify = "
		DELETE FROM ".$this->_getMonitoringTable()."
		WHERE item_id = '".$item_id."' AND
			item_type = '".$type."' ";
		$query_notify .= " AND user_idst = '".$user_idst."'";
		return $this->_executeQuery($query_notify);
	}

	/**
	 * Return if a user as set a notify for a resource
	 * @param string	$type 	specify the type of the notify
	 * @param int		$item_id  		specify the id of the resource
	 * @param int		$user_idst 		the user idst
	 *
	 * @return	bool	true if exists false otherwise
	 */
	function issetNotify($type, $item_id, $user_idst) {
		$query_notify = "
		SELECT item_id
		FROM ".$this->_getMonitoringTable()."
		WHERE item_id = '".$item_id."' AND
			user_idst = '".$user_idst."' AND
			item_type = '".$type."'";
		$re = $this->_executeQuery($query_notify);
		return ( mysql_num_rows($re) == 0 ? false : true );
	}

	/**
	 * Return all the users registered notify
	 * @param int		$user_idst 		the user
	 * @param string	$user_idst 	specify the type of the notify
	 *
	 * @return	array	[type]=>(  [id] => id, ...)
	 */
	function getAllNotify($user_idst, $type) {
		$notify = array();
		$query_notify = "
		SELECT item_id, item_type
		FROM ".$this->_getMonitoringTable()."
		WHERE user_idst = '".$user_idst."'";
		$query_notify .= " AND item_type = '".$type."'";
		$re = $this->_executeQuery($query_notify);
		while(list($id_n, $n_is_a) = mysql_fetch_row($re)) {
			$notify[$n_is_a][$id_n] = $id_n;
		}
		return $notify;
	}

	function launchNotify($type, $item_id, $description, &$msg_composer) {

		require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');

		$recipients = array();
		$query_notify = "
		SELECT user_idst
		FROM ".$this->_getMonitoringTable()."
		WHERE item_id = '".$item_id."' AND
			item_type = '".$type."' AND
			user_idst <> '".getLogUserId()."'";
		$re = $this->_executeQuery($query_notify);
		while(list($id_user) = mysql_fetch_row($re)) {
			$recipients[] = $id_user;
		}

		switch($type) {
			case "bug": {
					$event_class="BugChanged";
			} break;
		}

		if(!empty($recipients)) {

			createNewAlert($event_class,
								'bugtracker',
								$type,
								1,
								$description,
								$recipients,
								$msg_composer);
		}
		return;
	}


	function sendBugChangeNotify($app_id, $bug_id, $description) {

		// launch notify
		require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');

		$um =& UrlManager::getInstance();
		$platform=$GLOBALS["platform"];

		$url=$um->getUrl("op=bugdetails&appid=".$app_id."&bugid=".$bug_id);

		$msg_composer = new EventMessageComposer("bugtracker", "framework");

		$replace_arr=array('[id]' => $bug_id, '[url]' => $GLOBALS[$platform]['url'].$url);

		$msg_composer->setSubjectLangText('email', '_BUG_CHANGED_NOTIFY_SUB_EMAIL', $replace_arr);
		$msg_composer->setBodyLangText('email', '_BUG_CHANGED_NOTIFY_TXT_EMAIL', $replace_arr);

		$msg_composer->setSubjectLangText('sms', '_BUG_CHANGED_NOTIFY_SUB_SMS', $replace_arr);
		$msg_composer->setBodyLangText('sms', '_BUG_CHANGED_NOTIFY_TXT_SMS', $replace_arr);


		$this->launchNotify("bug", $bug_id, $description, $msg_composer);
	}


	function sendAssignedNotify($app_id, $bug_id, $idst_arr, & $lang) {

		$um =& UrlManager::getInstance();
		$platform=$GLOBALS["platform"];

		$acl_manager=$GLOBALS["current_user"]->getAclManager();
		$user=$acl_manager->getUser($GLOBALS["current_user"]->getIdSt(), FALSE);
		$fromemail=$user[ACL_INFO_EMAIL];

		$url=$um->getUrl("op=bugdetails&appid=".$app_id."&bugid=".$bug_id);
		$full_url=$GLOBALS[$platform]['url'].$url;

		$charset=$GLOBALS['globLangManager']->getLanguageCharset(getLanguage());
		$headers ='MIME-Version: 1.0'."\r\n";
		//$headers.='Content-type: text/html; charset='.$charset."\r\n";
		$headers.="From: ".$fromemail."\r\n";
		$headers.="Reply-To: ".$fromemail."\r\n";
		$headers.="X-Mailer: DoceboFramework";

		$sub=$lang->def("_EMAIL_USER_ASSIGNED_SUBJECT");

		$msg=$lang->def("_EMAIL_USER_ASSIGNED_BODY");
		$msg=str_replace("[url]", $full_url, $msg);

		$user_info=$acl_manager->getUsers($idst_arr);

		foreach ($user_info as $info) {
			$email=$info[ACL_INFO_EMAIL];

			mail($email, $sub, $msg, $headers);
		}

	}

}

?>
