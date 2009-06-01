<?php
/*************************************************************************/
/* DOCEBO Framework                                                      */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006                                                    */
/* http://www.docebo.org                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');


/**
 * @package admin-core
 * @subpackage user
 */

class ProfileSearch {

	// class var =========================================================

	/**
	 * @var int the idst of the user
	 * @access private
	 */
	var $_id_user;

	/**
	 * @var bool true if the profile must be printed in edit_mode
	 * @access private
	 */
	var $_edit_mode = false;

	/**
	 * @var UrlManager the instance of the url manager
	 * @access private
	 */
	var $_url_man;

	/**
	 * @var UserProfileViewer the instance of the profile viewver
	 * @access private
	 */
	var $_ps_viewer;

	/**
	 * @var UserProfileData the instance of the profile data manager
	 * @access private
	 */
	var $_ps_man;

	/**
	 * @var DoceboLanguage the instance of the language manager
	 * @access private
	 */
	var $_lang;

	// class method =======================================================

	/**
	 * class constructor
	 */
	function ProfileSearch() {


	}

	// initialize functions ===========================================================

	/**
	 * initialize the various class used by this one
	 * @param string 		$std_query 	the std_query for the address
	 * @param string $platform
	 * @param resource_id 	$db_conn 	the id of a db connection if different form the standard
	 */
	function init($std_query, $platform=FALSE, $db_conn = NULL) {

		if ($platform === FALSE)
			$platform=$GLOBALS["platform"];

		$this->initLang($platform);

		$this->initDataManager($db_conn);
		$this->initUrlManager($std_query);
		$this->initViewer();

		$this->_setReference();

		addCss("style_profile_search");
		addCss("style_profile_search");
	}

	/**
	 * instance the viewer class of the profile
	 */
	function initViewer() {

		$this->_ps_viewer = new ProfileSearchViewer($this);
	}

	/**
	 * instance the data manager class of the profile
	 * @param resource_id $db_conn the database connnection
	 */
	function initDataManager($db_conn = NULL) {

		$this->_ps_man = new ProfileSearchManager($db_conn);
	}

	/**
	 * initialize the internal url manager instance
	 * @param string $std_query the std_query for the address
	 */
	function initUrlManager($std_query) {

		require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');

		$this->_url_man =& UrlManager::getInstance();
		$this->_url_man->setStdQuery($std_query);
	}


	function getUrlManagerStdQuery() {
		return $this->_url_man->getStdQuery();
	}


	/**
	 * initialize the internal lang manager
	 * @param string $std_query the std_query for the address
	 */
	function initLang($platform) {

		$this->_lang =& DoceboLanguage::createInstance("profile_search", $platform);
	}

	/**
	 * set the reference trought the urlmanager, viever and data calsses
	 */
	function _setReference() {

		$this->_ps_viewer->setUrlManager($this->_url_man);
		$this->_ps_viewer->setDataManager($this->_ps_man);
	}

	// setter and getter functions ==============================================

	/**
	 * return the id of the user actually used by the instance of the class
	 * @return int the idst of the user
	 */
	function getIdUser() {

		return $this->_id_user;
	}

	/**
	 * set the id of the user used by the instance of the class
	 * @param int $id_user the idst of the user to assign
	 *
	 * @return int the idst of the user assigned
	 */
	function setIdUser($id_user) {

		$this->_ps_viewer->loadUserInfo($id_user);
		return $this->_id_user = $id_user;
	}


	function setCustomFieldsFilter($filter_arr) {
		$this->_ps_viewer->setCustomFieldsFilter($filter_arr);
	}


	function getCustomFieldsFilter() {
		return $this->_ps_viewer->getCustomFieldsFilter();
	}


	function setItemsPerPage($num_items) {
		$this->_ps_viewer->setItemsPerPage($num_items);
	}


	function getItemsPerPage() {
		return $this->_ps_viewer->getItemsPerPage();
	}


	function setAvatarSize($avatar_size) {
		$this->_ps_viewer->setAvatarSize($avatar_size);
	}


	function getAvatarSize() {
		return $this->_ps_viewer->getAvatarSize();
	}


	/**
	 * enable the edit mode for the profile
	 */
	function enableEditMode() {

		$this->_edit_mode = true;
	}

	/**
	 * disable the edit mode for the profile
	 */
	function disableEditMode() {

		$this->_edit_mode = false;
	}

	function &getUrlManager() {

		return $this->_url_man;
	}

	function &getDataManager() {

		return $this->_ps_man;
	}

	function &getLang() {

		return $this->_lang;
	}

	function editMode() {

		return $this->_edit_mode;
	}

	// function for standard image display =================================================


	/**
	 * print the title of the page
	 * @param mixed $text the title of the area, or the array with zone path and name
	 * @param string $image the image to load before the title
	 *
	 * @return string the html code for space open
	 */
	function getTitleArea($text, $image = '') {

		return $this->_ps_viewer->getTitleArea($text, $image);
	}

	/**
	 * Print the head of the module space after the getTitle area
	 * @return string the html code for space open
	 */
	function getHead() {
		return $this->_ps_viewer->getHead();
	}

	/**
	 * Print the footer of the module space
	 * @return string the html code for space close
	 */
	function getFooter() {
		return $this->_ps_viewer->getFooter();
	}

	/**
	 * print the back command in the page
	 * @param string $url the url used for back, if not passed will be setted with the one of the urlmanager
	 */
	function backUi($url = false) {
		return $this->_ps_viewer->backUi($url);
	}


	function setObjectId($str) {
		$this->_ps_viewer->setObjectId($str);
	}


	function getObjectId($with_sep=FALSE) {
		return $this->_ps_viewer->getObjectId($with_sep);
	}


	function setSearchLimit($users_arr) {
		$this->_ps_viewer->setSearchLimit($users_arr);
	}


	function appendSearchLimit($users_arr) {
		$this->_ps_viewer->appendSearchLimit($users_arr);
	}


	function getSearchLimit() {
		return $this->_ps_viewer->getSearchLimit();
	}


	function unsetSearchLimit() {
		return $this->_ps_viewer->unsetSearchLimit();
	}


	function clearSearchFilter() {
		require_once($GLOBALS["where_framework"]."/lib/lib.search.php");
		$search=new SearchUI("profile_search".$this->getObjectId(TRUE));

		$search->clearSearchFilter();
	}


	function showMain() {
		return $this->_ps_viewer->showMain();
	}


	function listFound($found_users) {
		return $this->_ps_viewer->listFound($found_users);
	}


}

// ========================================================================================================== //
// ========================================================================================================== //
// ========================================================================================================== //

/**
 * @category library
 * @package user_management
 * @subpackage profile
 *
 * This class will manage the display of the data readed by the
 */
class ProfileSearchViewer {

	/**
	 * @var UserProfile the instance of the profile
	 * @access private
	 */
	var $_profile_search;

	/**
	 * @var UrlManager the instance of the url manager
	 * @access private
	 */
	var $_url_man;

	/**
	 * @var DoceboLanguage the instance of the language manager
	 * @access private
	 */
	var $_lang;

	/**
	 * @var UserProfileData the instance of the profile data manager
	 * @access private
	 */
	var $_ps_man;

	/**
	 * @var array cache for user info
	 * @access private
	 */
	var $user_info = false;

	// Profile search object id
	var $obj_id="";

	// user idst "pre" filter
	var $search_limit=FALSE;

	var $custom_fields_filter =FALSE;
	var $items_per_page =20;
	var $avatar_size ="small";


	/**
	 * class constructor
	 */
	function ProfileSearchViewer(&$profile_search) {

		$this->_profile_search =& $profile_search;

		$this->acl_man = $GLOBALS['current_user']->getAclManager();

		$this->_lang =& $this->_profile_search->getLang();
	}

	/**
	 * set the reference to the UrlManager
	 * @param UrlManager $url_man the url manager instance
	 */
	function setUrlManager(&$url_man) {

		$this->_url_man =& $url_man;
	}

	/**
	 * set the reference to the DataManager
	 * @param UserProfileData $up_data_man the data manager instance
	 */
	function setDataManager(& $ps_man) {

		$this->_ps_man =& $ps_man;
	}


	function setCustomFieldsFilter($filter_arr) {
		$this->custom_fields_filter =(array)$filter_arr;
	}


	function getCustomFieldsFilter() {
		return $this->custom_fields_filter;
	}


	function setItemsPerPage($num_items) {
		$this->items_per_page =(int)$num_items;
	}


	function getItemsPerPage() {
		return $this->items_per_page;
	}


	function setAvatarSize($avatar_size) {
		$this->avatar_size =$avatar_size;
	}


	function getAvatarSize() {
		return $this->avatar_size;
	}


	/**
	 * print the title of the page
	 * @param mixed $text the title of the area, or the array with zone path and name
	 * @param string $image the image to load before the title
	 *
	 * @return string the html code for space open
	 */
	function getTitleArea($text, $image = '') {

		return;
	}

	/**
	 * Print the head of the module space after the getTitle area
	 * @return string the html code for space open
	 */
	function getHead() {

		return '<div class="std_block">'."\n";
	}

	/**
	 * Print the footer of the module space
	 * @return string the html code for space close
	 */
	function getFooter() {

		return '</div>'."\n";
	}

	/**
	 * print the back command in the page
	 * @param string $url the url used for back, if not passed will be setted with the one of the urlmanager
	 */
	function backUi($url = false) {

		if($url === FALSE && $this->_url_man != false) $url = $this->_url_man->getUrl();
		else $url = '';
		return getBackUi($url, $this->_lang->def( '_BACK' ));
	}


	function setObjectId($str) {
		$this->obj_id=$str;
	}


	function getObjectId($with_sep=FALSE) {

		$res=$this->obj_id;

		if (($with_sep) && (!empty($res)))
			$res="_".$res;

		return $res;
	}


	function setSearchLimit($users_arr) {
		$this->search_limit=$users_arr;
	}


	function appendSearchLimit($users_arr) {
		$this->search_limit =array_merge($this->getSearchLimit(), $users_arr);
	}


	function getSearchLimit() {

		//if ((is_array($this->search_limit)) && (count($this->search_limit) > 0))
		if (is_array($this->search_limit))
			return $this->search_limit;
		else
			return FALSE;
	}


	function unsetSearchLimit() {
		$this->search_limit=FALSE;
	}


	function showMain() {
		$res="";

		require_once($GLOBALS["where_framework"]."/lib/lib.form.php");
		require_once($GLOBALS["where_framework"]."/lib/lib.field.php");
		require_once($GLOBALS["where_framework"]."/lib/lib.search.php");
		require_once($GLOBALS["where_framework"]."/lib/lib.navbar.php");

		$form=new Form();
		$search=new SearchUI("profile_search".$this->getObjectId(TRUE));
		$fl=new FieldList();


		$search_items=array();
		$search_items["search_key"]=(isset($_POST["search_key"]) ? $_POST["search_key"] : "");
		$search_items["online_only"]=(isset($_POST["online_only"]) ? $_POST["online_only"] : 0);
		if ((isset($_POST["field_filter"])) && (is_array($_POST["field_filter"]))) {
			foreach($_POST["field_filter"] as $key=>$val) {
				$search_items["field_filter[".$key."]"]=$val;
			}
		}
		$search->applySearch($search_items);

		$res.=$search->openSearchForm($form, $this->_url_man->getUrl());
		// --------------------------------------------------------------------------

		$search_key =$search->getSearchItem("search_key", "string");
		$online_only =($search->getSearchItem("online_only", "int") == 1 ? TRUE : FALSE);

		$res.=$form->getTextfield($search->lang->def("_SEARCH_KEY"), "search_key", "search_key", 255, $search_key);

		// --| Extra fields | ---------

		$acl_manager=$GLOBALS["current_user"]->getAclManager();
		$user_groups=array($acl_manager->getGroupRegisteredId());

		$field_list=$fl->getFieldsFromIdst($user_groups);

		$custom_fields_filter =$this->getCustomFieldsFilter();
		if ($custom_fields_filter !== FALSE) {
			if (function_exists("array_intersect_key")) {
				$field_list =array_intersect_key($field_list, array_flip($custom_fields_filter));
			}
			else {
				foreach($field_list as $field_id=>$field_info) {
					if (!in_array($field_id, $custom_fields_filter)) {
						unset($field_list[$field_id]);
					}
				}
			}
		}

		$values=array();
		if ((isset($_POST["field_filter"])) && (is_array($_POST["field_filter"]))) {
			foreach($_POST["field_filter"] as $key=>$val) {
				$values[$key]=$search->getSearchItem("field_filter[".$key."]", "string");
			}
		}

		$res.=$fl->playFilters(array_keys($field_list), $values);

		$res.=$form->getCheckbox($search->lang->def("_ONLINE_ONLY"), "online_only", "online_only", 1, $online_only);

		$field_id_arr=array_keys($field_list);
		// ----------------------------

		// --------------------------------------------------------------------------
		$res.=$search->closeSearchForm($form);


// function searchUser($internal_fields, $extra_fields=FALSE, $idst_filter=FALSE, $ini=FALSE, $vis_item=FALSE)

		$internal_fields=array();
		$like_type=FALSE;
		if (!empty($search_key)) {
			$internal_fields[ACL_INFO_USERID]["add_before"]="(";
			$internal_fields[ACL_INFO_USERID]["filter"]=$search_key;
			$internal_fields[ACL_INFO_USERID]["like"]="both";
			$internal_fields[ACL_INFO_USERID]["nextop"]="OR";
			$internal_fields[ACL_INFO_FIRSTNAME]["filter"]=$search_key;
			$internal_fields[ACL_INFO_FIRSTNAME]["like"]="both";
			$internal_fields[ACL_INFO_FIRSTNAME]["nextop"]="OR";
			$internal_fields[ACL_INFO_LASTNAME]["filter"]=$search_key;
			$internal_fields[ACL_INFO_LASTNAME]["like"]="both";
			$internal_fields[ACL_INFO_LASTNAME]["add_after"]=")";
			$internal_fields[ACL_INFO_LASTNAME]["nextop"]="AND";
		}

		if ($online_only) {
			$internal_fields[ACL_INFO_LASTENTER]["comp_op"] =">";
			$internal_fields[ACL_INFO_LASTENTER]["filter"] =date("Y-m-d H:i:s", time() - REFRESH_LAST_ENTER);
		}

		$extra_fields=FALSE;
		if (count($field_id_arr) > 0) {

			$extra_fields["method"]="AND";
			$extra_fields["like"]=array();
			$extra_fields["search"]=array();

			if (count($values) > 0) {
				foreach($values as $id_common=>$val) {

					if (!empty($val)) {
						$extra_fields["fields"][]=$id_common;
						$extra_fields["like"][$id_common]="both";
						$extra_fields["search"][$id_common]=$val;
					}
				}

			}
		}

		if (count($extra_fields["search"]) < 1) {
			$extra_fields=FALSE;
		}


		// Show online only?


		$vis_item =$this->getItemsPerPage();
		$navbar =new NavBar("ini", $vis_item, 0);
		$ini =$navbar->getSelectedElement();
		$found_users =$acl_manager->searchUsers($internal_fields, $extra_fields, $this->getSearchLimit(), $ini, $vis_item);
		$total =$acl_manager->getSearchUsersTot($internal_fields, $extra_fields, $this->getSearchLimit());

		$res.=$this->listFound($found_users, $total);

		////////
		/*
		$fl->setFieldEntryTable($GLOBALS['prefix_fw'].'_field_userentry'); // <- ??!
		echo("<hr /><br /><br />");
		$like=array("1"=>"both", "5"=>"off");
		$search=array("1"=>"bc", "5"=>"0");
		$x1=$fl->quickSearchUsersFromEntry(array(1, 5), "AND", $like, $search);
		print_r($x1);
		echo("<br /><br />");
		$x2=$fl->quickSearchUsersFromEntry(array(1, 5), "OR", $like, $search);
		print_r($x2);
		*/

		return $res;
	}


	function listFound($found_users, $total) {
		$res="";

		addCss("style_navbar");
		require_once($GLOBALS["where_framework"]."/lib/lib.navbar.php");
		require_once($GLOBALS["where_framework"]."/lib/lib.user_profile.php");

		$navbar =new NavBar("ini", $this->getItemsPerPage(), $total, FALSE, "standard");

		$acl_manager=$GLOBALS["current_user"]->getAclManager();
		$user_info=$acl_manager->getUsers($found_users);

		$std_query =$this->_url_man->getStdQuery("array");
		$profile = new UserProfile(0);
		$profile->init("profile", $GLOBALS["platform"], $std_query);
		$profile->setCahceForUsers($found_users);

		if ($user_info === FALSE)
			$user_info=array();

		$navbar->setLink($this->_url_man->getUrl());
		$res.=$navbar->getNavBar();

		foreach($user_info as $idst=>$user) {

			$url=$this->_url_man->getUrl("op=profile&user_id=".$idst);
			$profile->setIdUser($idst);
			$res.=$profile->minimalUserInfo(FALSE, $this->getAvatarSize(), $url);
		}

		$res.=$navbar->getNavBar();

		return $res;
	}


}

// ========================================================================================================== //
// ========================================================================================================== //
// ========================================================================================================== //

/**
 * @category library
 * @package user_management
 * @subpackage profile
 *
 * This class will manage the display of the data readed by the
 */
class ProfileSearchManager {

	var $_db_conn = NULL;


	/**
	 * class constructor
	 */
	function ProfileSearchManager($db_conn = NULL) {

		$this->_db_conn = $db_conn;

	}

	function _query($query) {

		if($this->_db_conn === NULL)
			$re =  mysql_query($query);
		else
			$re =  mysql_query($query, $this->_db_conn);

		doDebug($query.( $re ? '' : ' :: error :'.mysql_error() ));
		return $re;
	}

	function getUserData($id_user) {

		return $this->acl_man->getUser($id_user, false);
	}

	function getUserField($id_user) {

		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');

		$field_man 		= new FieldList();

		$user_groups 	= $this->acl->getUserGroupsST($id_user);
		$field_list 	= $field_man->getFieldsFromIdst($user_groups);

		$field_id_arr 	= array_keys($field_list);

		$user_field_arr = $field_man->showFieldForUserArr(array($id_user), $field_id_arr);

		if (is_array($user_field_arr[$id_user]))
	 		$field_val = $user_field_arr[$id_user];
		else
			$field_val = array();

		foreach($field_val as $field_id => $value) {

			$field_founded[] = array(	'name' => $field_list[$field_id][FIELD_INFO_TRANSLATION],
										'value' => $value );
		}
		return $field_founded;
	}


}


?>