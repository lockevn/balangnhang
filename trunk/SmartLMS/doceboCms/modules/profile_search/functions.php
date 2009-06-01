<?php
/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");

require_once($GLOBALS["where_cms"]."/modules/profile_search/class.cms_profile_search.php");


function &createProfileSearchObj($teacher_mode=FALSE) {

	$res =new CmsProfileSearch();

	if (($teacher_mode) || (searchTeacherMode())) {
		$res->init("mn=profile_search&pi=".getPI()."&teacher=1&op=main");
		$res->setObjectId("teacher");
	}
	else {
		$res->init("mn=profile_search&pi=".getPI()."&op=main");
	}

	$opt =loadBlockOption($GLOBALS["pb"]);
	if ((isset($opt["custom_field"])) && (!empty($opt["custom_field"]))) {
		$sel_custom_fields =explode(",", $opt["custom_field"]);
		$res->setCustomFieldsFilter($sel_custom_fields);
	}
	else {
		$res->setCustomFieldsFilter(array());
	}

	$avatar_size =(isset($opt["avatar_size"]) ? $opt["avatar_size"] : "small");
	$res->setAvatarSize($avatar_size);

	$res->setItemsPerPage($GLOBALS["cms"]["visuItem"]);

	return $res;
}


function psClearSearchFilter() {

	$psearch=& createProfileSearchObj();
	$psearch->clearSearchFilter();

}


function profile_searchMain($teacher_mode=FALSE) {

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$psearch=& createProfileSearchObj($teacher_mode);


	// TODO: check if lms is active in searchTeacherMode() and block.profile_search_teacher.php
	// temp code --------------------------------------------------------
	if (($teacher_mode) || (searchTeacherMode())) {
		require_once($GLOBALS["where_lms"]."/lib/lib.course.php");
		$course_user_man=new Man_CourseUser();

		$limit_levels=$course_user_man->getUserWithLevelFilter(6);
		$psearch->setSearchLimit($limit_levels);
	}
	// ------------------------------------------------------------------

	require_once($GLOBALS["where_cms"]."/admin/modules/block_profile_search/functions.php");

	$filter=loadBlockFilter($GLOBALS["pb"], "profile_search");
	if (!isset($filter["group"]))
		$filter["group"]=array();
	if (!isset($filter["level"]))
		$filter["level"]=array();


	if (!empty($filter["group"])) {
		$acl_manager=$GLOBALS["current_user"]->getAclManager();
		$group_users=$acl_manager->getGroupListMembers($filter["group"]);
	}
	else {
		$group_users=FALSE;
	}


	if (!empty($filter["level"])) {
		require_once($GLOBALS["where_lms"]."/lib/lib.course.php");
		$course_user_man=new Man_CourseUser();
		$level_users=$course_user_man->getUserWithLevelFilter($filter["level"]);
	}
	else {
		$level_users=FALSE;
	}


	$filter_by_group=((is_array($group_users)) && (!empty($group_users)) ? TRUE : FALSE);
	$filter_by_level=((is_array($level_users)) && (!empty($level_users)) ? TRUE : FALSE);

	if (($filter_by_group) && ($filter_by_level)) {
		$limit_arr=array_unique(array_intersect($group_users, $level_users));
	}
	else if ($filter_by_group) {
		$limit_arr=$group_users;
	}
	else if ($filter_by_level) {
		$limit_arr=$level_users;
	}
	else {
		$limit_arr=array(0);
	}
	$psearch->setSearchLimit($limit_arr);

	// ------------------------------------------------------------------


  $out->add($psearch->getTitleArea("_PROFILE_SEARCH", "profile_search"));
  $out->add($psearch->getHead());

  $out->add($psearch->showMain());

  $out->add($psearch->getFooter());
}


function psShowProfile() {

	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	$psearch=& createProfileSearchObj();

  $out->add($psearch->getTitleArea("_PROFILE_SEARCH", "profile_search"));
  $out->add($psearch->getHead());
  $back_ui=$psearch->backUi();
  $out->add($back_ui);

	if ((isset($_GET["user_id"])) && (!empty($_GET["user_id"]))) {

		require_once($GLOBALS["where_cms"]."/modules/profile/class.cms_user_profile.php");

		$std_query=$psearch->getUrlManagerStdQuery();

		$user_id=$_GET["user_id"];
		$profile=new CmsUserProfile($user_id);
		$profile->init('profile', 'cms', $std_query, 'ap');

  	$out->add($profile->getProfile());

	}

	$out->add($back_ui);
  $out->add($psearch->getFooter());
}


function psShowTeacherProfile() {

	// TODO: exit if lms is not active!


	$out=& $GLOBALS["page"];
	$out->setWorkingZone("content");

	addCss("style_teacher_profile");

	$psearch=& createProfileSearchObj();

  $out->add($psearch->getTitleArea("_PROFILE_SEARCH", "profile_search"));
  $out->add($psearch->getHead());
  $back_ui=$psearch->backUi();
  $out->add($back_ui);

	require_once($GLOBALS["where_lms"].'/modules/teacher_profile/class.teacher_profile.php');

	$acl_man 	=& $GLOBALS['current_user']->getAClManager();
	$lang 		=& DoceboLanguage::createInstance('teacher_profile', "lms");

	$id_user 	= importVar('user_id', true, 0);

	$path 		= $GLOBALS['where_files_relative'].'/doceboCore/'.$GLOBALS['framework']['pathphoto'];

	$tp_man 	= new TProfile_Man($id_user);

	$user_info 	= $acl_man->getUser($id_user, false);
	$user_name 	= $acl_man->getConvertedUserName($user_info);


	$html = '<h2>'.$user_name.'</h2>'
		.'<div class="teacher_profile_container">';
	if($user_info[ACL_INFO_PHOTO] != "") {

		$photo = $path.$user_info[ACL_INFO_PHOTO];

		$img_size = @getimagesize($photo);
		$html .= '<img'.( $img_size[0] > 150 || $img_size[1] > 150 ? ' class="image_limit"' : '' )
			.' src="'.$photo.'" alt="'.$lang->def('_PHOTO').'" /><br />';
	} else {
		$html .= '<img src="'.getPathImage().'profile/sagoma.png" alt="'.$lang->def('_NOPHOTO').'" /><br />';
	}
	$html .= '</div>';

	$teacher_list	= $tp_man->getCourseAsTeacher();
	$tutor_list 	= $tp_man->getCourseAsTutor();
	$mentor_list 	= $tp_man->getCourseAsMentor();

	// teacher course list
	if(!empty($teacher_list)) {
		$html .= '<h3 class="tp_user_list">'.$lang->def('_COURSE_AS_TEACHER').'</h3>'
				.'<ul>';
		while(list($id, $data) = each($teacher_list)) {
			$html .= '<li>['.$data['code'].'] '.$data['name'].'</li>';
		}
		$html .= '</ul>';
	}
	// tutor course list
	if(!empty($tutor_list)) {
		$html .= '<h3 class="tp_user_list">'.$lang->def('_COURSE_AS_TUTOR').'</h3>'
				.'<ul>';
		while(list($id, $data) = each($tutor_list)) {
			$html .= '<li>['.$data['code'].'] '.$data['name'].'</li>';
		}
		$html .= '</ul>';
	}
	// menor course list
	if(!empty($mentor_list)) {
		$html .= '<h3 class="tp_user_list">'.$lang->def('_COURSE_AS_MENTOR').'</h3>'
				.'<ul>';
		while(list($id, $data) = each($mentor_list)) {
			$html .= '<li>['.$data['code'].'] '.$data['name'].'</li>';
		}
		$html .= '</ul>';
	}
	$html .= '<div class="no_float"></div>';

	$html .= '<h3 class="tp_user_list">'.$lang->def('_TEACHER_CURRICULUM').'</h3>'
			.'<div class="theacher_curriculum">'
			.$tp_man->getCurriculum()
			.'</div>';

/*
	if($mod_perm && getLogUserId() == $id_user) {
		$html .= '<a href="index.php?modname=tprofile&amp;op=modtprofile">'.$lang->def('_MOD_MY_PROFILE').'</a>';
	}
*/

	$out->add($html);
	$out->add($back_ui);
	$out->add($psearch->getFooter());
}


function searchTeacherMode() {
	$res=TRUE;

	if 	((!isset($_GET["teacher"])) || ($_GET["teacher"] != "1"))
		$res=FALSE;

	return $res;
}



?>
