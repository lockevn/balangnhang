<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system								*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @package  DoceboLms
 * @subpackage course
 * @version  $Id: subscribe.php 1002 2007-03-24 11:55:51Z fabio $
 * @author	 Fabio Pirovano <fabio[at]docebo-com>
 */

require_once($GLOBALS['where_lms'].'/lib/lib.levels.php');

function subscribeadd() {
	checkPerm('subscribe', false, 'public_course_admin');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/class.module/class.public_user_admin.php');

	$id_course = importVar('id_course', true, 0);
	$edition_id = getCourseEditionId();
	$ed_url_param=getEditionUrlParameter($edition_id);

	$lang =& DoceboLanguage::CreateInstance('subscribe', 'lms');
	$out =& $GLOBALS['page'];

	$user_select = new Module_Public_User_Admin();
	$user_select->show_user_selector = TRUE;
	$user_select->show_group_selector = false;
	$user_select->show_orgchart_selector = TRUE;
	$user_select->show_orgchart_simple_selector = TRUE;
	if(isset($_GET['load'])) {
		// ema -- add requested_tab to show user selector
		$user_select->requested_tab = PEOPLEVIEW_TAB;
		$user_alredy_subscribed = getSubscribed($id_course, FALSE, FALSE, FALSE, $edition_id);
		$user_select->resetSelection($user_alredy_subscribed);
	}
	
	$acl_man =& $GLOBALS['current_user']->getAclManager();
	$user_select->setUserFilter('exclude', array($acl_man->getAnonymousId()));
	
	$user_select->loadSelector('index.php?modname=public_subscribe_admin&amp;op=subscribeadd&amp;id_course='.$id_course.$ed_url_param.'&amp;jump=1',
			$lang->def('_SUBSCRIBE'),
			$lang->def('_CHOOSE_SUBSCRIBE'),
			true,
			true );
}

function chooselevel() {
	checkPerm('subscribe', false, 'public_course_admin');

	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_lms'].'/class.module/class.public_user_admin.php');

	$id_course 		= importVar('id_course', true, 0);
	$course_info 	= Man_Course::getCourseInfo($id_course);

	$edition_id=getCourseEditionId();

	if ($edition_id > 0) {
		$edition_info =Man_Course::getEditionInfo($edition_id, $id_course);
		$course_info =$edition_info+$course_info;
	}

	$out 			=& $GLOBALS['page'];
	$acl_man		=& $GLOBALS['current_user']->getAclManager();
	$lang 			=& DoceboLanguage::CreateInstance('subscribe', 'lms');
	$levels 		= CourseLevel::getLevels();
	
	unset($levels[7]);
	unset($levels[6]);
	unset($levels[5]);
	unset($levels[4]);
	unset($levels[1]);
	
	
	// Find limitation
	$can_subscribe = true;
	$max_num_subscribe 	= $course_info['max_num_subscribe'];
	$subscribe_method 	= $course_info['subscribe_method'];
	if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {

		$limited_subscribe = $GLOBALS['current_user']->preference->getPreference('admin_rules.limit_course_subscribe');
		$max_subscribe 		= $GLOBALS['current_user']->preference->getPreference('admin_rules.max_course_subscribe');
		$direct_subscribe 	= $GLOBALS['current_user']->preference->getPreference('admin_rules.direct_course_subscribe');

		if($limited_subscribe == 'on') $limited_subscribe = true;
		else $limited_subscribe = false;
		if($direct_subscribe == 'on') $direct_subscribe = true;
		else $direct_subscribe = false;
	} else {

		$limited_subscribe 	= false;
		$max_subscribe 		= 0 ;
		$direct_subscribe 	= true;
	}

	// Print page
	$page_title = array(
		'index.php?modname=public_course_admin&amp;op=course_list' => $lang->def('_COURSES'),
		$lang->def('_SUBSCRIBE'),
		$course_info['name']
	);
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'subscribe')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=public_course_admin&amp;op=course_list', $lang->def('_BACK'))
	, 'content');
	// User selected
	$user_alredy_subscribed 	= getSubscribed($id_course, FALSE, FALSE, FALSE, $edition_id);
	$num_all_user = count($user_alredy_subscribed);

	if(!isset($_POST['user_level_sel'])) {

		$user_select 	= new Module_Public_User_Admin();
		$entity_selected 	= $user_select->getSelection($_POST);
		// convert to user only

		$user_selected =& $acl_man->getAllUsersFromIdst($entity_selected);

		$user_selected = array_diff($user_selected, $user_alredy_subscribed);
		$num_selected = count($user_selected);
	} else {

		$num_selected = 0;
		$user_selected = array();
		while(list($id_user, $lv) = each($_POST['user_level_sel'])) {

			$user_selected[$id_user] = $id_user;
			if($lv != 0) $num_selected++;
		}
		reset($_POST['user_level_sel']);
	}
	$user_selected_info =& $acl_man->getUsers($user_selected);

	if($num_selected == 0) {
		$GLOBALS['page']->add($lang->def('_SELECTION_EMPTY').'</div>', 'content');
		return;
	}
	// Report error and limitation 
	/*
	$free_space = $max_num_subscribe - $num_all_user;
	if($max_num_subscribe != 0 && $free_space <= 0) {
		$GLOBALS['page']->add(getResultUi($lang->def('_COURSELIMITREACHED').'</div>'), 'content');
		$can_subscribe = false;
		return;
	}
	
	$free_space = $max_subscribe - $num_all_user;
	
	if(($limited_subscribe && $max_subscribe < $free_space)) {

		$GLOBALS['page']->add(getResultUi(str_replace('[user_subscribe_limit]', ''.$max_subscribe, $lang->def('_YOUCANSUBSCRIBE')).'<br />'), 'content');
		if($num_selected > $max_subscribe) $can_subscribe = false;
	} elseif($max_num_subscribe != 0) {

		if($free_space < $num_selected) {

			$GLOBALS['page']->add(getResultUi(str_replace('[max_subscribe]', ''.$free_space, $lang->def('_EMPTYSPACE')).'<br />'), 'content');
			$can_subscribe = false;
		}
	}
	*/
	if($subscribe_method != 3 && !$direct_subscribe)
		$GLOBALS['page']->add(getResultUi($lang->def('_BEFORE_THIS_APPROVE').'<br />'), 'content');

	if(isset($_POST['subscribe']) && $can_subscribe) {
		// do subscription

		//retrive id of group of the course for the varioud level
		$level_idst =& getCourseLevel($id_course);
		if(count($level_idst) == 0) {

			//if the group doesn't exists create it
			$level_idst =& createCourseLevel($id_course);
		}
		// Subscirbing user
		$waiting = 0;
		$user_subscribed = array();
		$user_waiting = array();
		if($subscribe_method != 3 && !$direct_subscribe) $waiting = 1;
		while(list($id_user, $lv_sel) = each($_POST['user_level_sel'])) {
			if(!$limited_subscribe || $max_subscribe)
			{
				if($lv_sel != 0) {
	
					// Add in group for permission
					$acl_man->addToGroup($level_idst[$lv_sel], $id_user);
	
					// Add to edition group
					if ($edition_id > 0) {
	
						$group ='/lms/course_edition/'.$edition_id.'/subscribed';
						$group_idst =$acl_man->getGroupST($group);
						if ($group_idst === FALSE) {
							$group_idst =$acl_man->registerGroup($group, 'all the user of a course edition', true, "course");
						}
	
						$acl_man->addToGroup($group_idst, $id_user);
					}
	
					// Add in table
					$re = mysql_query("
					INSERT INTO ".$GLOBALS['prefix_lms']."_courseuser
					( idUser, idCourse, edition_id, level, waiting, subscribed_by, date_inscr )
					VALUES
					( '".$id_user."', '".$id_course."', '".$edition_id."', '".$lv_sel."', '".$waiting."', '".getLogUserId()."', '".date("Y-m-d H:i:s")."' )	");
					if($re) {
						if($waiting) $user_waiting[] = $id_user;
						else $user_subscribed[] = $id_user;
	
						addUserToTimeTable($id_user, $id_course, $edition_id);
					}
				}
				
				$max_subscribe--;
			}
		}
		$GLOBALS['current_user']->loadUserSectionST('/lms/course/private/');
		$GLOBALS['current_user']->SaveInSession();

		require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');
		$array_subst = array(	'[url]' => $GLOBALS['lms']['url'],
								'[course]' => $course_info['name'] );
		if(!empty($user_subscribed)) {
			// message to user that is waiting
			$msg_composer = new EventMessageComposer('subscribe', 'lms');

			$msg_composer->setSubjectLangText('email', '_NEW_USER_SUBSCRIBED_SUBJECT', false);
			$msg_composer->setBodyLangText('email', '_NEW_USER_SUBSCRIBED_TEXT', $array_subst);

			$msg_composer->setSubjectLangText('sms', '_NEW_USER_SUBSCRIBED_SUBJECT_SMS', false);
			$msg_composer->setBodyLangText('sms', '_NEW_USER_SUBSCRIBED_TEXT_SMS', $array_subst);

			// send message to the user subscribed
			createNewAlert(	'UserCourseInserted', 'subscribe', 'insert', '1', 'User subscribed',
						$user_subscribed, $msg_composer  );

		}
		if(!empty($user_waiting)) {
			// message to user that is waiting
			$msg_composer = new EventMessageComposer('subscribe', 'lms');

			$msg_composer->setSubjectLangText('email', '_NEW_USER_SUBS_WAITING_SUBJECT', false);
			$msg_composer->setBodyLangText('email', '_NEW_USER_SUBS_WAITING_TEXT', $array_subst);

			$msg_composer->setSubjectLangText('sms', '_NEW_USER_SUBS_WAITING_SUBJECT_SMS', false);
			$msg_composer->setBodyLangText('sms', '_NEW_USER_SUBS_WAITING_TEXT_SMS', $array_subst);

			// send message to the user subscribed
			createNewAlert(	'UserCourseInsertModerate', 'subscribe', 'insert', '1', 'User subscribed with moderation',
						$user_waiting, $msg_composer  );
		}

		if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {
			$GLOBALS['current_user']->preference->setPreference('admin_rules.max_course_subscribe', $max_subscribe);
		}
		backcourse('ok_subs');
	}

	$GLOBALS['page']->add(
		Form::openForm('levelselection', 'index.php?modname=public_subscribe_admin&amp;op=chooselevel')
		.Form::getHidden('id_course', 'id_course', $id_course)
		.Form::getHidden('edition_id', 'edition_id', $edition_id)
	, 'content');

	$tb = new TypeOne( 0, $lang->def('_CAPTION_SELECT_LEVELS'), $lang->def('_SUMMARY_SELECT_LEVEL') );
	$type_h = array('image', '', '');
	$img ='<img src="'.getPathImage('fw').'standard/warning_triangle.png" ';
	$img.='alt="'.$lang->def("_USER_IS_BUSY").'" title="'.$lang->def("_USER_IS_BUSY").'" />';
	$content_h = array($img, $lang->def('_USERNAME'), $lang->def('_FIRST_NAME_LAST_NAME'));
	foreach($levels as $lv => $lv_name) {
		$type_h[]	 = 'image';
		$content_h[] = $lv_name;
	}
	$type_h[]	 = 'image';
	$content_h[] = $lang->def('_CANCEL');
	$tb->addHead($content_h, $type_h);

	if ($course_info["course_type"] === "elearning") {
		$busy_users=array();
	}
	else {
		require_once($GLOBALS['where_framework']."/lib/resources/lib.timetable.php");
		$tt=new TimeTable();
		$busy_users=$tt->getResourcesInUse("user", $course_info["date_begin"], $course_info["date_end"], TRUE);
	}

	$num_user_sel = 0;
	$enought_credit = true;
	reset($user_selected_info);
	while( (list($id_user, $user_info) = each($user_selected_info)) && ($enought_credit)) {

		// if the user isn't alredy subscribed to the course
		if(!isset($user_alredy_subscribed[$id_user])) {

			if (in_array($id_user, $busy_users)) {
				
				$img ='<img src="'.getPathImage('fw').'standard/warning_triangle.png" ';
				$img.='alt="'.$lang->def("_USER_IS_BUSY").'" title="'.$lang->def("_USER_IS_BUSY").'" />';
				$msg =$lang->def("_USER_IS_BUSY_MSG");
				
				$is_user_busy=$img;//."</a>";
			}
			else {
				$is_user_busy="&nbsp;";
			}

			$content = array(	$is_user_busy, substr($user_info[ACL_INFO_USERID], 1),
								$user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]);
			foreach($levels as $lv => $lv_name) {

				$content[] = Form::getInputRadio(	'user_level_sel_'.$id_user.'_'.$lv,
													'user_level_sel['.$id_user.']',
													$lv,
													( isset($_POST['user_level_sel']) ? $lv == $_POST['user_level_sel'][$id_user] : $lv == 3 ),
													'' )
							.'<label class="access-only" for="user_level_sel_'.$id_user.'_'.$lv.'">'.$lv_name.'</label>';
			}
			$content[] = Form::getInputRadio(	'user_level_sel_'.$id_user.'_0',
													'user_level_sel['.$id_user.']',
													0,
													( isset($_POST['user_level_sel']) ? 0 == $_POST['user_level_sel'][$id_user] : false ),
													'' )
							.'<label class="access-only" for="user_level_sel_'.$id_user.'_0">'.$lang->def('_CANCEL').'</label>';
			$tb->addBody($content);
			$num_user_sel++;
		}
	}
	$GLOBALS['page']->add($tb->getTable(), 'content');
	$GLOBALS['page']->add(
		Form::openButtonSpace()
		.'<br />'
		.Form::getButton('subscribe', 'subscribe', $lang->def('_SUBSCRIBE'))
		.Form::getButton('cancelselector', 'cancelselector', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
	, 'content');
	$GLOBALS['page']->add('</div>', 'content');
}

function backcourse($result = false) {

	jumpTo('index.php?modname=public_course_admin&op=course_list'.( $result !== false ? '&result='.$result : '' ) );
}

/******************************************************************************/

function subscribemod() {
	checkPerm('subscribe', false, 'public_course_admin');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.publicadminmanager.php');
	$adminManager = new PublicAdminManager();
	
	$p_dr 	= new PeopleDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
	
	if( isset( $adminManager->user_filter['exclude'] ) )
		$p_dr->addNotFilter($adminManager->user_filter['exclude']);

	if( isset( $adminManager->user_filter['user'] ) ) {
			$p_dr->setUserFilter($adminManager->user_filter['user']);
	}

	if( isset( $adminManager->user_filter['group'] ) ) {
		foreach( $adminManager->user_filter['group'] as $idstGroup )
			$p_dr->setGroupFilter($idstGroup);
	} else {
		$userlevelid = $GLOBALS['current_user']->getUserLevelId();
		if( $userlevelid != ADMIN_GROUP_GODADMIN) {
			$p_dr->intersectGroupFilter($adminManager->getAdminTree($GLOBALS['current_user']->getIdSt()));
		}
	}
	
	$re_people = $p_dr->getAllRowsIdst();
	
	$user_selected = array();
	if($re_people)	
		while(list($idst) = mysql_fetch_row($re_people))
		{
			$user_selected[$idst] = $idst; 
		}
	
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$id_course 		= importVar('id_course', true, 0);
	$course_info 	= Man_Course::getCourseInfo($id_course);
	$edition_id 	= getCourseEditionId();

	$out 			=& $GLOBALS['page'];
	$lang 			=& DoceboLanguage::CreateInstance('subscribe', 'lms');
	$acl_man		=& $GLOBALS['current_user']->getAclManager();
	$levels 		= CourseLevel::getLevels();
	
	unset($levels[7]);
	unset($levels[6]);
	unset($levels[5]);
	unset($levels[4]);
	unset($levels[1]);
	
	
	$arr_absent = array(0 => $lang->def('_NO'),
						1 => $lang->def('_JUSTIFIED'),
						2 => $lang->def('_NOT_JUSTIFIED') );
	
	$arr_status = array(_CUS_CONFIRMED 		=> $lang->def('_USER_STATUS_CONFIRMED'),
						
						_CUS_SUBSCRIBED 	=> $lang->def('_USER_STATUS_SUBS'),
						_CUS_BEGIN 			=> $lang->def('_USER_STATUS_BEGIN'),
						_CUS_END 			=> $lang->def('_USER_STATUS_END'),
						_CUS_SUSPEND 		=> $lang->def('_USER_STATUS_SUSPEND'), 
						
						_CUS_CANCELLED		=> $lang->def('_USER_STATUS_CANCELLED') );
	
	// Retrive info about the selected user
	$user_alredy_subscribed 	= getSubscribed($id_course, false, false, true, $edition_id);
	$user_alredy_subscribed = array_intersect($user_alredy_subscribed, $user_selected);
	$user_levels 				= getSubscribedInfo($id_course, false, false, false, false, $edition_id);
	$user_selected_info 		=& $acl_man->getUsers($user_alredy_subscribed);

	$page_title = array(
		'index.php?modname=public_course_admin&op=course_list' => $lang->def('_COURSES'),
		$course_info['name'] ,
		$lang->def('_SUBSCRIBE')
	);
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'subscribe')
		.'<div class="std_block">'
		.getInfoUi(
			str_replace('[status]' ,$lang->def('_USER_STATUS_CONFIRMED') , $lang->def('_PLEASE_BE_CAREFULL') )
		)
		.Form::openForm('levelselection', 'index.php?modname=public_subscribe_admin&amp;op=subscribeupdate')
		.Form::getHidden('id_course', 'id_course', $id_course)
		.Form::getHidden('edition_id', 'edition_id', $edition_id)
		, 'content');

	$tb 	= new TypeOne( 0, $lang->def('_CAPTION_SELECT_LEVELS'), $lang->def('_SUMMARY_SELECT_LEVEL') );

	$type_h = array('', '');
	$content_h = array($lang->def('_USERNAME'), $lang->def('_FIRST_NAME_LAST_NAME'));
	foreach($levels as $lv => $lv_name) {
	
		$type_h[]	 = 'image';
		$content_h[] = $lv_name;
	}
	$type_h[]	 = 'image';
	$content_h[] = $lang->def('_USER_STATUS');
	
	if($course_info['course_type'] != 'elearning') {
		$type_h[]	 = 'image';
		$content_h[] = $lang->def('_ABSENT');
	}
	$tb->addHead($content_h, $type_h);

	$num_user_sel = 0;
	if(is_array($user_selected_info)) {

		reset($user_selected_info);
		while( (list($id_user, $user_info) = each($user_selected_info))) {

			// if the user isn't alredy subscribed to the course
			$content = array(	substr($user_info[ACL_INFO_USERID], 1),
								$user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME]);
			foreach($levels as $lv => $lv_name) {

				$content[] = Form::getInputRadio(	'user_level_sel_'.$id_user.'_'.$lv,
													'user_level_sel['.$id_user.']',
													$lv,
													($lv == $user_levels[$id_user]['level']),
													'' )
							.'<label class="access-only" for="user_level_sel_'.$id_user.'_'.$lv.'">'.$lv_name.'</label>';
			}
			$content[] = Form::getInputDropdown(	'dropdown',
													'user_status_sel_'.$id_user.'',
													'user_status_sel['.$id_user.']',
													$arr_status,  
													$user_levels[$id_user]['status'],
													'')
						.'<label class="access-only" for="user_status_sel_'.$id_user.'">'.$lang->def('_USER_STATUS').'</label>';
						
			if($course_info['course_type'] != 'elearning') {
				
				$content[] = Form::getInputDropdown('dropdown_nowh',
													'user_absent'.$id_user.'',
													'user_absent['.$id_user.']',
													$arr_absent,  
													$user_levels[$id_user]['absent'],
													'')
						.'<label class="access-only" for="user_absent_'.$id_user.'">'.$lang->def('_ABSENT').'</label>';	
			}
						
			$tb->addBody($content);
		}
		$GLOBALS['page']->add($tb->getTable(), 'content');
	}
	$GLOBALS['page']->add(
		Form::openButtonSpace()
		.'<br />'
		.Form::getButton('subscribe', 'subscribe', $lang->def('_MOD_SUBSCRIBE'))
		.Form::getButton('cancelselector', 'cancelselector', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
	, 'content');
	$GLOBALS['page']->add('</div>', 'content');
}

function subscribeupdate() {
	checkPerm('subscribe', false, 'public_course_admin');

	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

	$id_course 		= importVar('id_course', true, 0);
	$edition_id 	= getCourseEditionId();
	$course_info 	= Man_Course::getCourseInfo($id_course);
	
	$lang 		=& DoceboLanguage::CreateInstance('subscribe', 'lms');
	$out 		=& $GLOBALS['page'];
	$acl_man	=& $GLOBALS['current_user']->getAclManager();

	if(!isset($_POST['user_level_sel'])) {

		//the user selection is empty, return to course selection
		backcourse('err_selempty');
	}
	
	//retrive id of group of the course for the various level ---------------------------
	$level_idst 		=& getCourseLevel($id_course);
	$actual_user_level 	= getSubscribedLevel($id_course, false, false, $edition_id);
	if(count($level_idst) == 0) {

		//if the group doesn't exists create it
		$level_idst =& createCourseLevel($id_course);
	}

	// Subscirbing user ----------------------------------------------------------------- 
	
	$re = true;
	$user_subs = array();
	while(list($id_user, $lv_sel) = each($_POST['user_level_sel'])) {

		$lv_old = $actual_user_level[$id_user];
		if($lv_sel != $lv_old) {

			// Add in group for permission
			$acl_man->removeFromGroup($level_idst[$lv_old], $id_user);
			$acl_man->addToGroup($level_idst[$lv_sel], $id_user);
		}	
		$new_status = $_POST['user_status_sel'][$id_user];
		
		$upd_query = "
		UPDATE ".$GLOBALS['prefix_lms']."_courseuser
		SET level = '".$lv_sel."',
			status = '".$new_status."',
			absent = '".( isset($_POST['user_absent'][$id_user]) ? $_POST['user_absent'][$id_user] : '0' )."'
		
		".( $new_status == _CUS_RESERVED || $new_status == _CUS_WAITING_LIST || $new_status == _CUS_CONFIRMED
			? ", waiting = '1'" 
			: ""  )."

		".( $_POST['user_status_sel'][$id_user] == _CUS_CANCELLED 
			? ", cancelled_by = '".getLogUserId()."'" 
			: ", cancelled_by = '0'"  )."

		WHERE idUser = '".$id_user."' 
			 AND idCourse = '".$id_course."'
			 AND edition_id='".$edition_id."'";

		// Add in table
		$re_sing = mysql_query($upd_query);
		if($re_sing) {
			$user_subs[] = $id_user;
			addUserToTimeTable($id_user, $id_course, $edition_id);
		}
		$re &= $re_sing;
	}

	$GLOBALS['current_user']->loadUserSectionST('/lms/course/private/');
	$GLOBALS['current_user']->SaveInSession();

	require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');
	$array_subst = array(	'[url]' => $GLOBALS['lms']['url'],
							'[course]' => $course_info['name'] );
	if(!empty($user_subs)) {
		// message to user that is waiting
		$msg_composer = new EventMessageComposer('subscribe', 'lms');

		$msg_composer->setSubjectLangText('email', '_MOD_USER_SUBSCRIPTION_SUBJECT', false);
		$msg_composer->setBodyLangText('email', '_MOD_USER_SUBSCRIPTION_TEXT', $array_subst);

		$msg_composer->setSubjectLangText('sms', '_MOD_USER_SUBSCRIPTION_SUBJECT_SMS', false);
		$msg_composer->setBodyLangText('sms', '_MOD_USER_SUBSCRIPTION_TEXT_SMS', $array_subst);

		// send message to the user subscribed
		createNewAlert(	'UserCourseLevelChanged', 'subscribe', 'modify', '1', 'User subscribed',
					$user_subs, $msg_composer  );

	}
	backcourse( ( $re ? 'ok_subs' : 'err_subs' ) );
}

/************************************************************************************/

function subscribedel() {
	checkPerm('subscribe', false, 'public_course_admin');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.publicadminmanager.php');
	$adminManager = new PublicAdminManager();
	
	$p_dr 	= new PeopleDataRetriever($GLOBALS['dbConn'], $GLOBALS['prefix_fw']);
	
	if( isset( $adminManager->user_filter['exclude'] ) )
		$p_dr->addNotFilter($adminManager->user_filter['exclude']);

	if( isset( $adminManager->user_filter['user'] ) ) {
			$p_dr->setUserFilter($adminManager->user_filter['user']);
	}

	if( isset( $adminManager->user_filter['group'] ) ) {
		foreach( $adminManager->user_filter['group'] as $idstGroup )
			$p_dr->setGroupFilter($idstGroup);
	} else {
		$userlevelid = $GLOBALS['current_user']->getUserLevelId();
		if( $userlevelid != ADMIN_GROUP_GODADMIN) {
			$p_dr->intersectGroupFilter($adminManager->getAdminTree($GLOBALS['current_user']->getIdSt()));
		}
	}
	
	$re_people = $p_dr->getAllRowsIdst();
	
	$user_selected = array();
	if($re_people)	
		while(list($idst) = mysql_fetch_row($re_people))
		{
			$user_selected[$idst] = $idst; 
		}
	
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');

	$id_course 			= importVar('id_course', true, 0);
	$course_to_save 	= Man_Course::saveCourseStatus();

	$edition_id = getCourseEditionId();

	$out 			=& $GLOBALS['page'];
	$lang 			=& DoceboLanguage::CreateInstance('subscribe', 'lms');
	$acl_man		=& $GLOBALS['current_user']->getAclManager();
	$levels 		= CourseLevel::getLevels();

	$user_alredy_subscribed	= getSubscribed($id_course, false, false, true, $edition_id);
	$user_alredy_subscribed = array_intersect($user_alredy_subscribed, $user_selected);
	$user_levels 				= getSubscribedLevel($id_course, false, false, $edition_id);

	$user_selected_info =& $acl_man->getUsers($user_alredy_subscribed);

	$GLOBALS['page']->add(
		getTitleArea($lang->def('_SUBSCRIBE'), 'subscribe')
		.'<div class="std_block">'
		.Form::openForm('levelselection', 'index.php?modname=public_subscribe_admin&amp;op=subscriberemove')
		.Form::getHidden('id_course', 'id_course', $id_course)
		.Form::getHidden('edition_id', 'edition_id', $edition_id)
		, 'content');

	$tb 	= new TypeOne( 0, $lang->def('_CAPTION_SELECT_LEVELS'), $lang->def('_SUMMARY_SELECT_LEVEL') );

	$type_h = array('', '', '', 'image');
	$content_h = array($lang->def('_USERNAME'), $lang->def('_FIRST_NAME_LAST_NAME'), $lang->def('_LEVEL'),
				'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'">');

	$tb->addHead($content_h, $type_h);

	$num_user_sel = 0;
	if(is_array($user_selected_info)) {

		reset($user_selected_info);
		while( (list($id_user, $user_info) = each($user_selected_info))) {

			// if the user isn't alredy subscribed to the course
			$content = array(	substr($user_info[ACL_INFO_USERID], 1),
								$user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME],
								$levels[$user_levels[$id_user]],
								$content[] = Form::getInputCheckbox('user_to_remove'.$id_user,
													'user_to_remove['.$id_user.']',
													$id_user,
													false,
													'' )
							.'<label class="access-only" for="user_to_remove'.$id_user.'">'.$user_info[ACL_INFO_USERID].'</label>');

			$tb->addBody($content);
		}
		$GLOBALS['page']->add($tb->getTable(), 'content');
	}
	$GLOBALS['page']->add(
		Form::openButtonSpace()
		.'<br />'
		.Form::getButton('subscribe', 'subscribe', $lang->def('_DEL_SUBSCRIBE'))
		.Form::getButton('cancelselector', 'cancelselector', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
	, 'content');
	$GLOBALS['page']->add('</div>', 'content');
}

function subscriberemove() {
	checkPerm('subscribe', false, 'public_course_admin');

	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

	$id_course = importVar('id_course', true, 0);
	$course_info 	= Man_Course::getCourseInfo($id_course);

	$edition_id=getCourseEditionId();

	if ($edition_id > 0) {
		$edition_info =Man_Course::getEditionInfo($edition_id, $id_course);
		$course_info =$edition_info+$course_info;
	}

	$lang =& DoceboLanguage::CreateInstance('subscribe', 'lms');
	$out =& $GLOBALS['page'];
	$acl_man	=& $GLOBALS['current_user']->getAclManager();

	if(!isset($_POST['user_to_remove'])) {

		//the user selection is empty, return to course selection
		backcourse('err_selempty');
	}

	$group_levels 	= getCourseLevel($id_course);
	$user_levels 	= getSubscribedLevel($id_course, false, false, $edition_id);
	// Subscirbing user
	$re = true;
	$user_del = array();
	while(list($id_user, $v) = each($_POST['user_to_remove'])) {

		$date_begin =$course_info["date_begin"];
		$date_end =$course_info["date_end"];

		$re_sing = removeSubscription($id_course, $id_user, $group_levels[$user_levels[$id_user]], $edition_id, $date_begin, $date_end);
		if($re_sing) $user_del[] = $id_user;
		$re &= $re_sing;
	}

		require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');
	$array_subst = array(	'[url]' => $GLOBALS['lms']['url'],
							'[course]' => $course_info['name'] );
	if(!empty($user_del)) {
		// message to user that is waiting
		$msg_composer = new EventMessageComposer('subscribe', 'lms');

		$msg_composer->setSubjectLangText('email', '_DEL_USER_SUBSCRIPTION_SUBJECT', false);
		$msg_composer->setBodyLangText('email', '_DEL_USER_SUBSCRIPTION_TEXT', $array_subst);

		$msg_composer->setSubjectLangText('sms', '_DEL_USER_SUBSCRIPTION_SUBJECT_SMS', false);
		$msg_composer->setBodyLangText('sms', '_DEL_USER_SUBSCRIPTION_TEXT_SMS', $array_subst);

		// send message to the user subscribed
		createNewAlert(	'UserCourseRemoved', 'subscribe', 'remove', '1', 'User removed form a course',
					$user_del, $msg_composer  );

	}
	backcourse( ( $re ? 'ok_subs' : 'err_subs' ) );
}

/************************************************************************************/


function waitinguser() {
	checkPerm('moderate', false, 'public_course_admin');

	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.user_profile.php');

	$id_course 		= importVar('id_course', true, 0);
	$man_course		= new Man_Course();
	$course_info 	= $man_course->getCourseInfo($id_course);

	$edition_id 	= getCourseEditionId();
	$ed_url_param 	= getEditionUrlParameter($edition_id);

	$out 			=& $GLOBALS['page'];
	$lang 			=& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
	$lang 			=& DoceboLanguage::CreateInstance('subscribe', 'lms');
	$acl_man		=& $GLOBALS['current_user']->getAclManager();
	$levels 		= CourseLevel::getLevels();

	$waiting_users	=& $man_course->getWaitingSubscribed($id_course, $edition_id);
	$users_name =& $acl_man->getUsers($waiting_users['all_users_id']);

	$arr_status = array(_CUS_RESERVED		=> $lang->def('_USER_STATUS_RESERVED'),
						_CUS_WAITING_LIST	=> $lang->def('_USER_STATUS_WAITING_LIST'),
	
						_CUS_CONFIRMED 		=> $lang->def('_USER_STATUS_CONFIRMED'),
						
						_CUS_SUBSCRIBED 	=> $lang->def('_USER_STATUS_SUBS'),
						_CUS_BEGIN 			=> $lang->def('_USER_STATUS_BEGIN'),
						_CUS_END 			=> $lang->def('_USER_STATUS_END'),
						_CUS_SUSPEND 		=> $lang->def('_SUSPENDED'), 
						
						_CUS_CANCELLED		=> $lang->def('_USER_STATUS_CANCELLED') );

	$page_title = array(
		'index.php?modname=public_course_admin&amp;op=course_list' => $lang->def('_COURSE', 'admin_course_managment', 'lms'),
		$course_info['name'],
		$lang->def('_USERWAITING', 'admin_course_managment', 'lms')
	);
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'subscribe')
		.'<div class="std_block">'
		.Form::openForm('approve users', 'index.php?modname=public_subscribe_admin&amp;op=approveusers')
		.Form::getHidden('id_course', 'id_course', $id_course)
		.Form::getHidden('edition_id', 'edition_id', $edition_id)
	, 'content');

	$tb 	= new TypeOne( 0, $lang->def('_SELECT_WHO_CONFIRM'), $lang->def('_SUMMARY_SELECT_WHO_CONFIRM') );

	$type_h = array('', '', '', '', '', 'image', 'image', 'image');
	$content_h = array($lang->def('_USERNAME'), $lang->def('_FIRST_NAME_LAST_NAME'), $lang->def('_LEVEL'),
		$lang->def('_SUBSCRIBED_BY'),
		$lang->def('_STATUS'),
		$lang->def('_APPROVE'),
		$lang->def('_DENY'),
		$lang->def('_WAIT')
	);
	$tb->addHead($content_h, $type_h);

	if(is_array($waiting_users['users_info'])) {

		reset($waiting_users['users_info']);
		while((list($id_user, $info) = each($waiting_users['users_info']))) {

			$id_sub_by = $info['subscribed_by'];
			$subscribed 	= ( $users_name[$id_sub_by][ACL_INFO_LASTNAME].''.$users_name[$id_sub_by][ACL_INFO_FIRSTNAME] != ''
				? $users_name[$id_sub_by][ACL_INFO_LASTNAME].' '.$users_name[$id_sub_by][ACL_INFO_FIRSTNAME]
				: $acl_man->relativeId($users_name[$id_sub_by][ACL_INFO_USERID]) );
			$more = ( isset($_GET['id_user']) &&  $_GET['id_user'] == $id_user
				? '<a href="index.php?modname=public_subscribe_admin&amp;op=waitinguser&amp;id_course='.$id_course.$ed_url_param.'"><img src="'.getPathImage().'standard/less.gif"></a> '
				: '<a href="index.php?modname=public_subscribe_admin&amp;op=waitinguser&amp;id_course='.$id_course.$ed_url_param.'&amp;id_user='.$id_user.'"><img src="'.getPathImage().'standard/more.gif"></a> ');
			$content = array(
				$more.
				$acl_man->relativeId($users_name[$id_user][ACL_INFO_USERID]),
				$users_name[$id_user][ACL_INFO_LASTNAME].' '.$users_name[$id_user][ACL_INFO_FIRSTNAME],
				$levels[$info['level']],
				$subscribed.' ['.$users_name[$id_user][ACL_INFO_EMAIL].']'
			);
			$content[] = $arr_status[$info['status']];
			$content[] = Form::getInputRadio(
					'waiting_user_0_'.$id_user,
					'waiting_user['.$id_user.']',
					'0',
					false,
					'' ).'<label class="access-only" for="waiting_user_0_'.$id_user.'">'.$users_name[$id_user][ACL_INFO_USERID].'</label>';

			$content[] = Form::getInputRadio(
					'waiting_user_1_'.$id_user,
					'waiting_user['.$id_user.']',
					'1',
					false,
					'' ).'<label class="access-only" for="waiting_user_1_'.$id_user.'">'.$users_name[$id_user][ACL_INFO_USERID].'</label>';
			
			$content[] = Form::getInputRadio(
						'waiting_user_2_'.$id_user,
						'waiting_user['.$id_user.']',
						'2',
						true,
						'' ).'<label class="access-only" for="waiting_user_1_'.$id_user.'">'.$users_name[$id_user][ACL_INFO_USERID].'</label>';
					
			$tb->addBody($content);
			if (isset($_GET['id_user']) &&  $id_user == $_GET['id_user']) {
				$field = new FieldList();
				$info = $field->playFieldsForUser( $id_user, false, true );
				$tb->addBodyExpanded(( $info != '' ? $info : $lang->def('_NO_EXTRAINFO_AVAILABLE') ), 'user_specific_info');
			}
		}
	}

	$GLOBALS['page']->add(
		$tb->getTable()
		.'<br />'
		.Form::openElementSpace()
		.Form::getSimpleTextarea($lang->def('_SUBSCRIBE_ACCEPT'), 'subscribe_accept', 'subscribe_accept')
		.Form::getSimpleTextarea($lang->def('_SUBSCRIBE_REFUSE'), 'subscribe_refuse','subscribe_refuse')
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.'<br />'
		.Form::getButton('subscribe', 'subscribe', $lang->def('_SAVE'))
		.Form::getButton('cancelselector', 'cancelselector', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
	, 'content');
	$GLOBALS['page']->add('</div>', 'content');
}

function approveusers() {
	checkPerm('moderate', false, 'public_course_admin');

	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.preference.php');

	$id_course 		= importVar('id_course', true, 0);
	$course_info 	= Man_Course::getCourseInfo($id_course);

	$edition_id 	= getCourseEditionId();
	
	$re= true;
	$approve_user 	= array();
	$deny_user 		= array();
	if(isset($_POST['waiting_user'])) {
		
		$man_course		= new Man_Course();
		$waiting_users	=& $man_course->getWaitingSubscribed($id_course);
		$tot_deny 		= array();
		$group_levels 	= getCourseLevel($id_course);
				
		while(list($id_user, $action) = each($_POST['waiting_user'])) {
			
			if($action == 0) {
				// approved -----------------------------------------------
				
				$text_query = "
				UPDATE ".$GLOBALS['prefix_lms']."_courseuser
				SET waiting = 0, 
					status = '"._CUS_SUBSCRIBED."'
				WHERE idCourse = '".$id_course."' AND idUser = '".$id_user."' ";
				$text_query.= "AND edition_id='".$edition_id."'";
				$result = mysql_query($text_query);
				if($result) $approve_user[] = $id_user;
				$re &= $result;
				
			} elseif($action == 1) {
				// refused --------------------------------------------------
				
				$level 		= $waiting_users['users_info'][$id_user]['level'];
				$sub_by 	= $waiting_users['users_info'][$id_user]['subscribed_by'];
				$result 	= removeSubscription($id_course, $id_user, $group_levels[$level], $edition_id);
				if($sub_by != 0 && ($id_user != $sub_by)) {
	
					if(isset($tot_deny[$sub_by])) $tot_deny[$sub_by]++;
					else $tot_deny[$sub_by] = 1;
				}
				if($result) $deny_user[] = $id_user;
				$re &= $result;
			}
		}
	}
	if(!empty($tot_deny)) {

		while(list($id_user, $inc) = each($tot_deny)) {

			$pref = new UserPreferences($id_user);
			$max_subscribe = $pref->getPreference('admin_rules.max_course_subscribe');
			$pref->setPreference('admin_rules.max_course_subscribe', ($max_subscribe + $inc));
		}
	}
	require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');
	$array_subst = array(	'[url]' => $GLOBALS['lms']['url'],
							'[course]' => $course_info['name'] );
	if(!empty($approve_user)) {

		$msg_composer = new EventMessageComposer('subscribe', 'lms');

		$msg_composer->setSubjectLangText('email', '_APPROVED_SUBSCRIBED_SUBJECT', false);
		$msg_composer->setBodyLangText('email', '_APPROVED_SUBSCRIBED_TEXT', $array_subst);
		$msg_composer->setBodyLangText('email', "\n\n".$_POST['subscribe_accept'], array(), true);

		$msg_composer->setSubjectLangText('sms', '_APPROVED_SUBSCRIBED_SUBJECT_SMS', false);
		$msg_composer->setBodyLangText('sms', '_APPROVED_SUBSCRIBED_TEXT_SMS', $array_subst);

		// send message to the user subscribed
		createNewAlert(	'UserCourseInserted', 'subscribe', 'approve', '1', 'User course approve',
					$approve_user, $msg_composer, true );

	}
	if(!empty($deny_user)) {

		$msg_composer = new EventMessageComposer('subscribe', 'lms');

		$msg_composer->setSubjectLangText('email', '_DENY_SUBSCRIBED_SUBJECT', false);
		$msg_composer->setBodyLangText('email', '_DENY_SUBSCRIBED_TEXT', $array_subst);
		$msg_composer->setBodyLangText('email', "\n\n".$_POST['subscribe_deny'], array(), true);

		$msg_composer->setSubjectLangText('sms', '_DENY_SUBSCRIBED_SUBJECT_SMS', false);
		$msg_composer->setBodyLangText('sms', '_DENY_SUBSCRIBED_TEXT_SMS', $array_subst);

		// send message to the user subscribed
		createNewAlert(	'UserCourseInserted', 'subscribe', 'deny', '1', 'User course deny',
					$deny_user, $msg_composer, true );
	}
	backcourse( ( $re ? 'ok' : 'err' ) );

}

function removeSubscription($id_course, $id_user, $lv_group, $edition_id=0, $start_date=FALSE, $end_date=FALSE) {

	require_once($GLOBALS["where_framework"]."/lib/resources/lib.timetable.php");
	$tt=new TimeTable();
	// ----------------------------------------
	$resource="user";
	$resource_id=$id_user;
	if ($edition_id > 0) {
		$consumer="course_edition";
		$consumer_id=$edition_id;
	}
	else {
		$consumer="course";
		$consumer_id=$id_course;
	}
	// ----------------------------------------
	$tt->deleteEvent(FALSE, $resource, $resource_id, $consumer, $consumer_id, $start_date, $end_date);

	$acl_man =& $GLOBALS['current_user']->getAclManager();
	$acl_man->removeFromGroup($lv_group, $id_user);

	if ($edition_id > 0) {
		$group ='/lms/course_edition/'.$edition_id.'/subscribed';
		$group_idst =$acl_man->getGroupST($group);
		$acl_man->removeFromGroup($group_idst, $id_user);
	}

	return mysql_query("
	DELETE FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idUser = '".$id_user."' AND idCourse = '".$id_course."'
	AND edition_id='".(int)$edition_id."'");
}


function getCourseEditionId() {

	if (isset($_POST["edition_id"])) {
		$res=(int)$_POST["edition_id"];
	}
	else if (isset($_GET["edition"])) {
		$res=(int)$_GET["edition"];
	}
	else {
		$res=0;
	}
	
	return $res;
}


function getEditionUrlParameter($edition_id) {

	if ($edition_id > 0) {
		$res="&amp;edition=".$edition_id;
	}
	else {
		$res="";
	}

	return $res;
}


function addUserToTimeTable($user_id, $course_id, $edition_id=0) {

	// -- timetable setup ------------------------------------------------
	require_once($GLOBALS["where_framework"]."/lib/resources/lib.timetable.php");
	$tt=new TimeTable();

	$resource="user";
	$resource_id=$user_id;
	if ($edition_id > 0) {
		$consumer="course_edition";
		$consumer_id=$edition_id;
		$table=$GLOBALS["prefix_lms"]."_course_edition";
		$id_name="idCourseEdition";
	}
	else {
		$consumer="course";
		$consumer_id=$course_id;
		$table=$GLOBALS["prefix_lms"]."_course";
		$id_name="idCourse";
	}
	// -------------------------------------------------------------------


	$qtxt ="SELECT date_begin, date_end FROM ".$table." ";
	$qtxt.="WHERE ".$id_name."='".(int)$consumer_id."'";

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_assoc($q);

		$start_date=$row["date_begin"];
		$end_date=$row["date_end"];
	}
	else {
		return FALSE;
	}


	$res=$tt->saveEvent(FALSE, $start_date, $end_date, $start_date, $end_date, $resource, $resource_id, $consumer, $consumer_id);

	return $res;
}


/****************************************************************************************/

function publicSubscribeAdminDispatch($op) {
	
	if(isset($_GET['ini_hidden']) || isset($_POST['ini_hidden'])) {
		
		$_SESSION['course_category']['ini_status'] = importVar('ini_hidden', true, 0);
	}
	if(isset($_POST['okselector'])) {
		$op = 'chooselevel';
	}
	if(isset($_POST['cancelselector'])) {
		$op = 'backcourse';
	}
	switch($op) {
		case "subscribeadd" : {
			subscribeadd();
		};break;
		case "chooselevel" : {
			chooselevel();
		};break;

		case "subscribemod" : {
			subscribemod();
		};break;
		case "subscribeupdate" : {
			subscribeupdate();
		};break;

		case "subscribedel" : {
			subscribedel();
		};break;
		case "subscriberemove" : {
			subscriberemove();
		};break;

		case "waitinguser" : {
			waitinguser();
		};break;
		case "approveusers" : {
			approveusers();
		};break;

		case "backcourse" : {
			backcourse();
		};break;
	}
}

?>
