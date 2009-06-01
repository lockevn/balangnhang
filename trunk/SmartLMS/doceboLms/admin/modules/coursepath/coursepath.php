<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system									*/
/* ============================================							*/
/*																			*/
/* Copyright (c) 2005														*/
/* http://www.docebo.com													*/
/*																			*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @version  $Id: coursepath.php 767 2006-10-31 10:09:25Z fabio $
 * @author	 Fabio Pirovano <fabio [at] docebo [dot] com>
 */

if(!$GLOBALS['current_user']->isAnonymous()) {

function pathlist() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$out 	=& $GLOBALS['page'];
	$lang	=& DoceboLanguage::createInstance('coursepath', 'lms');
	
	$subscribe_perm = checkPerm('subscribe', true);
	$moderate_perm 	= checkPerm('moderate', true);
	$mod_perm 		= checkPerm('mod', true);
	$del_perm 		= checkPerm('mod', true);
	
	$query_pathlist = "
	SELECT id_path, path_code, path_name, path_descr
	FROM ".$GLOBALS['prefix_lms']."_coursepath ";
	
	if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');
		
		$course_man = new AdminCourseManagment();
		
		$all_path =& $course_man->getUserAllCoursePaths( getLogUserId() );
		
		if(empty($all_path)) {
			
			$query_pathlist .= "WHERE 0 ";
		} else {
			
			$query_pathlist .= "WHERE id_path IN (".implode(',', $all_path).") ";
		}
	}
	$query_pathlist .= " ORDER BY path_name ";
	$re_pathlist = mysql_query($query_pathlist);
	
	// find subscriptions
	$subscriptions = array();
	$query_subcription = "
	SELECT id_path, COUNT(idUser), SUM(waiting)
	FROM ".$GLOBALS['prefix_lms']."_coursepath_user 
	GROUP BY id_path";
	$re_subscription = mysql_query($query_subcription);
	while(list($id_path, $users, $waitings) = mysql_fetch_row($re_subscription)) {
		$subscriptions[$id_path]['users'] = $users - $waitings;
		$subscriptions[$id_path]['waiting'] = $waitings;
	}
	
	$out->setWorkingZone('content');
	$out->add(getTitleArea($lang->def('_COURSE_PATH'), 'coursepath')
		.'<div class="std_block">');
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok"  : $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFULL')));	break;
			case "err" : $out->add(getErrorUi($lang->def('_OPERATION_FAILED')));		break;
		}
	}
	$tb_path = new Typeone(0, $lang->def('_COURSE_PATH_CAPTION'), $lang->def('_COURSE_PATH_SUMMARY'));
	
	$cont_h = array($lang->def('_CODE'), 
					$lang->def('_COURSE_PATH_NAME'), 
					$lang->def('_COURSE_PATH_DESC'),
					$lang->def('_SUBSCRIBED_USER'), 
					'<img src="'.getPathImage().'standard/modelem.gif" alt="'.$lang->def('_ALT_MODELEM').'" />');
	$type_h = array('course_code', '', '', 'image', 'image');
	if($moderate_perm) {
		
		$cont_h[] = '<img src="'.getPathImage().'subscribe/waiting.gif" alt="'.$lang->def('_ALT_SUB_WAITING').'" />';
		$type_h[] = 'image';
	}
	if($subscribe_perm) {
		$cont_h[] = '<img src="'.getPathImage().'subscribe/add_subscribe.gif" alt="'.$lang->def('_ALT_ADD_SUB').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'subscribe/del_subscribe.gif" alt="'.$lang->def('_ALT_REM_SUB').'" />';
		$type_h[] = 'image';
	}
	if($mod_perm) {
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" />';
		$type_h[] = 'image';
	}
	if($del_perm) {
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" />';
		$type_h[] = 'image';
	}
	
	$tb_path->setColsStyle($type_h);
	$tb_path->addHead($cont_h);
	while(list($id_path, $path_code, $path_name, $path_descr) = mysql_fetch_row($re_pathlist)) {
		
		$cont = array($path_code, $path_name, $path_descr, 
			( isset($subscriptions[$id_path]['users'] ) ? $subscriptions[$id_path]['users'] : '' ), 
			'<a href="index.php?modname=coursepath&amp;op=pathelem&amp;id_path='.$id_path.'" '
				.'title="'.$lang->def('_MODIFY_COURSE_CONTAINED').' : '.$path_name.'">'
			.'<img src="'.getPathImage().'standard/modelem.gif" alt="'.$lang->def('_ALT_MODELEM').' : '.$path_name.'" /></a>');
		if($moderate_perm) {
			if(isset($subscriptions[$id_path]['waiting']) && $subscriptions[$id_path]['waiting']!= false) {
				
				$cont[] = '<a href="index.php?modname=coursepath&amp;op=waitingsubscription&amp;id_path='.$id_path.'" '
							.'title="'.$lang->def('_ALT_SUB_WAITING').' : '.$path_name.'">'
						.'<img src="'.getPathImage().'subscribe/waiting.gif" alt="'.$lang->def('_ALT_SUB_WAITING').' : '.$path_name.'" /></a>';
			} else {
				$cont[] = '';
			}
		}
		if($subscribe_perm) {
			$cont[] = '<a href="index.php?modname=coursepath&amp;op=addsubscription&amp;id_path='.$id_path.'&amp;load=1" '
						.'title="'.$lang->def('_ALT_ADD_SUB').' : '.$path_name.'">'
					.'<img src="'.getPathImage().'subscribe/add_subscribe.gif" alt="'.$lang->def('_ALT_ADD_SUB').' : '.$path_name.'" /></a>';
			$cont[] = '<a href="index.php?modname=coursepath&amp;op=delsubscription&amp;id_path='.$id_path.'" '
						.'title="'.$lang->def('_ALT_REM_SUB').' : '.$path_name.'">'
					.'<img src="'.getPathImage().'subscribe/del_subscribe.gif" alt="'.$lang->def('_ALT_REM_SUB').' : '.$path_name.'" /></a>';
		}
		if($mod_perm) {
			$cont[] = '<a href="index.php?modname=coursepath&amp;op=modcoursepath&amp;id_path='.$id_path.'" '
						.'title="'.$lang->def('_MOD').' : '.$path_name.'">'
					.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$path_name.'" /></a>';
		}
		if($del_perm) {
			$cont[] = '<a href="index.php?modname=coursepath&amp;op=deletepath&amp;id_path='.$id_path.'" '
						.'title="'.$lang->def('_DEL').' : '.$path_name.'">'
					.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$path_name.'" /></a>';
		}
		$tb_path->addBody($cont);
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=deletepath]');
	
	if($mod_perm) {
		
		$tb_path->addActionAdd(
			'<a href="index.php?modname=coursepath&amp;op=newcoursepath" title="'.$lang->def('_NEW_COURSEPATH_TITLE').'">'
			.'<img src="'.getPathimage().'standard/add.gif" alt="'.$lang->def('_ADD').'" />'
			.$lang->def('_NEW_COURSEPATH')
			.'</a>');
	}
	$out->add($tb_path->getTable());
	
	$out->add('</div>');
}


function mancoursepath($load_id = false) {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$out 	=& $GLOBALS['page'];
	$lang	=& DoceboLanguage::createInstance('coursepath', 'lms');
	
	if($load_id === false) {
		
		$path_code 			= '';
		$path_name 			= '';
		$path_descr 		= '';
		$subscribe_method 	= 0;
	} else {
			
		$query_pathlist = "
		SELECT path_code, path_name, path_descr, subscribe_method 
		FROM ".$GLOBALS['prefix_lms']."_coursepath 
		WHERE id_path = '".(int)$load_id."'
		ORDER BY path_name";
		list($path_code, $path_name, $path_descr, $subscribe_method) = mysql_fetch_row(mysql_query($query_pathlist));
	}
	
	$title_area = array('index.php?modname=coursepath&amp;op=pathlist' => $lang->def('_COURSE_PATH'));
	if($load_id === false) {
		
		$title_area[] = $lang->def('_NEW_COURSEPATH_TITLE');
	} else {
		
		$title_area[] = $path_name;
	}
	$out->add(
		getTitleArea($title_area, 'coursepath')
		.'<div class="std_block">'
		.( $load_id === false ? Form::getFormHeader($lang->def('_NEW_COURSEPATH_TITLE')) : '' )
		.Form::openForm('mancoursepath', 'index.php?modname=coursepath&amp;op=savecoursepath')
		.Form::openElementSpace()
		.( $load_id === false ? '' : Form::getHidden('id_path', 'id_path', $load_id) )
		.Form::getTextfield($lang->def('_CODE'), 'path_code', 'path_code', 255,
			$path_code )
		.Form::getTextfield($lang->def('_COURSE_PATH_NAME'), 'path_name', 'path_name', 255,
			$path_name )
		.Form::getTextarea($lang->def('_COURSE_PATH_DESC'), 'path_descr', 'path_descr',
			$path_descr )
			
		.Form::getOpenCombo($lang->def('_COURSE_PATH_SUBSCRIBE'))
		.Form::getRadio($lang->def('_COURSE_S_GODADMIN'), 'course_subs_godadmin', 'subscribe_method', '0', ($subscribe_method == 0) )
		.Form::getRadio($lang->def('_COURSE_S_MODERATE'), 'course_subs_moderate', 'subscribe_method', '1', ($subscribe_method == 1))
		.Form::getRadio($lang->def('_COURSE_S_FREE'), 'course_subs_free', 'subscribe_method', '2', ($subscribe_method == 2))
		.Form::getCloseCombo()
		
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function savecoursepath() {
	checkPerm('mod');
	
	$re = true;
	if(isset($_POST['id_path'])) {
		
		// Update existing
		$query_update = "
		UPDATE ".$GLOBALS['prefix_lms']."_coursepath 
		SET path_code = '".$_POST['path_code']."',
			path_name = '".$_POST['path_name']."',
			path_descr = '".$_POST['path_descr']."',
			subscribe_method = '".$_POST['subscribe_method']."'
		WHERE id_path = '".(int)$_POST['id_path']."'";
		$re = mysql_query($query_update);
	} else {
		// Create new
		$query_insert = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_coursepath 
		( path_code, path_name, path_descr, subscribe_method ) VALUES 
		( '".$_POST['path_code']."', 
		  '".( $_POST['path_name'] != '' ? $_POST['path_name'] : def('_EMPTY_NAME', 'coursegpath') )."',
		  '".$_POST['path_descr']."',
		  '".$_POST['subscribe_method']."' )";
		$re = mysql_query($query_insert);
		if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {
			
			list($id_path) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
			$re &= mysql_query("
			INSERT INTO ".$GLOBALS['prefix_fw']."_admin_course 
			( id_entry, type_of_entry, idst_user ) VALUES 
			( '".$id_path."', 'coursepath', '".getLogUserId()."') ");
		}
	}
	jumpTo('index.php?modname=coursepath&op=pathlist&result='.( $re ? 'ok' : 'err' ));
}

function deletepath() {
	checkPerm('mod');
	
	$id_path = importVar('id_path', true, 0);
	
	if(get_req('confirm', DOTY_INT, 0) == 1) {
		
		$re = true;
		
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_coursepath_courses 
		WHERE id_path = '".$id_path."'"))
			jumpTo('index.php?modname=coursepath&op=pathlist&result=err' );
		
		// Update existing
		$query_delete = "
		DELETE FROM ".$GLOBALS['prefix_lms']."_coursepath 
		WHERE id_path = '".(int)$id_path."'";
		$re = mysql_query($query_delete);
		jumpTo('index.php?modname=coursepath&op=pathlist&result='.( $re ? 'ok' : 'err' ));
	} else {
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		
		$query_pathlist = "
		SELECT path_name, path_descr
		FROM ".$GLOBALS['prefix_lms']."_coursepath 
		WHERE id_path = '".(int)$id_path."'
		ORDER BY path_name";
		list($path_name, $path_descr) = mysql_fetch_row(mysql_query($query_pathlist));
		
		$lang	=& DoceboLanguage::createInstance('coursepath', 'lms');
		
		$GLOBALS['page']->add(
			getTitleArea($lang->def('_COURSE_PATH'), 'coursepath')
			.'<div class="std_block">'
			.Form::openForm('deletepath', 'index.php?modname=coursepath&amp;op=deletepath')
			.Form::getHidden('id_path', 'id_path', $id_path)
			.getDeleteUi(
				$lang->def('_AREE_YOU_SURE_TO_DELETE_PATH'), 
				'<span class="text_bold">'.$lang->def('_COURSE_PATH_NAME').' : </span>'.$path_name.'<br />'
				.'<span class="text_bold">'.$lang->def('_COURSE_PATH_DESC').' : </span>'.$path_descr, 
				false,
				'confirm', 
				'undo')
			.Form::closeForm()
			.'</div>', 'content');
	}
}

function coursePathSubstPrer($id_string, $names) {
	
	$prereq = '';
	if($id_string == '') return $prereq;
	$all_id = explode(',', $id_string);
	$i = 0;
	while(list(, $id) = each($all_id)) {
		
		$i++;
		$prereq .= $names[$id]['name'].', ';
	}
	return '( '.$i.' ) '.substr($prereq, 0, -2);
}

function pathelem() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	
	$lang	=& DoceboLanguage::createInstance('coursepath', 'lms');
	
	$id_path = importVar('id_path', true, 0);
	$mod_perm = checkPerm('mod', true);
	
	$path_man 	= new CoursePath_Manager();
	$course_man = new Man_Course();
	
	$path = $path_man->getCoursepathInfo($id_path);
	
	// retriving id of the courses in this path
	$slots 		= $path_man->getPathSlot($id_path);
	$courses 	= $path_man->getPathElem($id_path);
	
	// retrive all i need about courses name
	if(isset($courses['course_list'])) $course_info 	= $course_man->getAllCourses(false, false, $courses['course_list']);
	else $course_info = array();
	
	$area_title = array('index.php?modname=coursepath&amp;op=pathlist'=> $lang->def('_COURSE_PATH'), 
		$path['path_name']);
	
	$GLOBALS['page']->add(getTitleArea($area_title, 'coursepath')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=coursepath&amp;op=pathlist', $lang->def('_BACK'))
	,'content');
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok"  : $GLOBALS['page']->add(getResultUi($lang->def('_OPERATION_SUCCESSFULL')), 'content');	break;
			case "err" : $GLOBALS['page']->add(getErrorUi($lang->def('_OPERATION_FAILED')), 'content');			break;
		}
	}
		
	$tb_path = new Typeone(0, $lang->def('_COURSE_PATH_COURSES_CAPTION'), $lang->def('_COURSE_PATH_COURSES_SUMMARY'));
	
	$cont_h = array($lang->def('_CODE'), $lang->def('_COURSE_NAME'), $lang->def('_COURSE_PREREQUISITES'));
	$type_h = array('coursepath_code', 'coursepath_name', '', 'image');
	if($mod_perm) {
		$cont_h[] = '<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'course/prerequisites.gif" alt="'.$lang->def('_MOD').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" />';
		$type_h[] = 'image';
	}
	$tb_path->setColsStyle($type_h);
	$tb_path->addHead($cont_h);
		
	$slot_number = 0;
	foreach($slots as $id_slot => $slot_info) {
		
		if($id_slot != 0) {
			$tb_path->setSummary($lang->def('_COURSE_PATH_COURSES_SLOT_SUMMARY'));
			$tb_path->setCaption(str_replace('[slot_number]', $slot_number, $lang->def('_COURSE_PATH_COURSES_SLOT_CAPTION')));
		}
		$tb_path->emptyBody();
		$tb_path->emptyFoot();
		
		$i = 0;
		if(!isset($courses[$id_slot])) $num_course = 0;
		else {
				
			$num_course = count($courses[$id_slot]);
			while(list($id_item, $prerequisites) = each($courses[$id_slot])) {
				
				$cont = array(	$course_info[$id_item]['code'], 
								$course_info[$id_item]['name'] );
				if($prerequisites != '') $cont[] = coursePathSubstPrer($prerequisites, $course_info );
				else $cont[] = '';
				if($mod_perm) {
					
					if($i != $num_course - 1) {
						$cont[] = '<a href="index.php?modname=coursepath&amp;op=downelem&amp;id_path='.$id_path.'&amp;id_course='.$id_item.'&amp;id_slot='.$id_slot.'" '
									.'title="'.$lang->def('_MOVE_DOWN').' : '.$course_info[$id_item]['name'].'">'
								.'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').'" />'.'</a>';
					} else $cont[] = '';
					
					if($i != 0) {
						$cont[] = '<a href="index.php?modname=coursepath&amp;op=upelem&amp;id_path='.$id_path.'&amp;id_course='.$id_item.'&amp;id_slot='.$id_slot.'" '
									.'title="'.$lang->def('_MOVE_UP').' : '.$course_info[$id_item]['name'].'">'
								.'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').'" />'.'</a>';
					} else $cont[] = '';
					
					$cont[] = '<a href="index.php?modname=coursepath&amp;op=modprerequisites&amp;id_path='.$id_path.'&amp;id_course='.$id_item.'&amp;id_slot='.$id_slot.'" '
								.'title="'.$lang->def('_MOD').' : '.$course_info[$id_item]['name'].'">'
							.'<img src="'.getPathImage().'course/prerequisites.gif" alt="'.$lang->def('_MOD').' : '.$course_info[$id_item]['name'].'" /></a>';
					
					$cont[] = '<a href="index.php?modname=coursepath&amp;op=delcoursepath&amp;id_path='.$id_path.'&amp;id_course='.$id_item.'&amp;id_slot='.$id_slot.'" '
								.'title="'.$lang->def('_DEL').' : '.$course_info[$id_item]['name'].'">'
							.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$course_info[$id_item]['name'].'" /></a>';
				}
				$tb_path->addBody($cont);
				$i++;
			}
		}
		// add link
		if($mod_perm) {
			
			if($id_slot == 0) {
				
				$tb_path->addActionAdd(
					'<a href="index.php?modname=coursepath&amp;op=importcourse&amp;load=1&amp;id_path='.$id_path.'&amp;id_slot='.$id_slot.'" '
						.'title="'.$lang->def('_IMPORT_COURSE').'">'
					.'<img src="'.getPathimage().'standard/import.gif" alt="'.$lang->def('_IMPORT').'" />'
					.$lang->def('_IMPORT_COURSE')
					.'</a>');
			} else {
				$tb_path->addBodyExpanded(
					$lang->def('_MIN_SELECTION').': <strong>'.$slot_info['min_selection'].'</strong><br />'
					.$lang->def('_MAX_SELECTION').': <strong>'.$slot_info['max_selection'].'</strong>');
				$tb_path->addActionAdd(
					'<ul class="adjac_link"><li>'
					.'<a href="index.php?modname=coursepath&amp;op=importcourse&amp;load=1&amp;id_path='.$id_path.'&amp;id_slot='.$id_slot.'" '
						.'title="'.$lang->def('_IMPORT_COURSE_IN_SLOT').'">'
					.'<img src="'.getPathimage().'standard/import.gif" alt="'.$lang->def('_IMPORT').'" />'
					.$lang->def('_IMPORT_COURSE_IN_SLOT')
					.'</a>'
					.'</li><li>'
					.'<a href="index.php?modname=coursepath&amp;op=modslot&amp;id_slot='.$id_slot.'&amp;id_path='.$id_path.'" '
						.'title="'.$lang->def('_MOD_COURSE_SLOT').'">'
					.'<img src="'.getPathimage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" />'
					.$lang->def('_MOD_SLOT')
					.'</a>'
					.'</li><li>'
					.'<a href="index.php?modname=coursepath&amp;op=delslot&amp;id_slot='.$id_slot.'&amp;id_path='.$id_path.'" '
						.'title="'.$lang->def('_DELETE_COURSE_SLOT').'">'
					.'<img src="'.getPathimage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" />'
					.$lang->def('_DEL_SLOT')
					.'</a>'
					.'</li></ul>');
			}
		}
		$GLOBALS['page']->add($tb_path->getTable().'<br />','content');
		$slot_number++;
	}
	/*
	$GLOBALS['page']->add(
		'<a href="index.php?modname=coursepath&amp;op=modslot&amp;id_path='.$id_path.'" '
			.'title="'.$lang->def('_NEW_SLOT_TITLE').'">'
			.'<img src="'.getPathimage().'standard/add.gif" alt="'.$lang->def('_ADD').'" />'
			.$lang->def('_NEW_SLOT')
		.'</a>'
	,'content');
	*/
	$GLOBALS['page']->add(getBackUi('index.php?modname=coursepath&amp;op=pathlist', $lang->def('_BACK') )
		.'</div>'
	,'content');
}

function downelem() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	
	$id_path = importVar('id_path', true, 0);
	$id_slot = importVar('id_slot', true, 0);
	$id_course = importVar('id_course', true, 0);
	
	$path_man 	= new CoursePath_Manager();
	$path_man->moveDown($id_path, $id_slot, $id_course);
	jumpTo('index.php?modname=coursepath&op=pathelem&id_path='.$id_path);
}

function upelem() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	
	$id_path = importVar('id_path', true, 0);
	$id_slot = importVar('id_slot', true, 0);
	$id_course = importVar('id_course', true, 0);
	
	$path_man 	= new CoursePath_Manager();
	$path_man->moveUp($id_path, $id_slot, $id_course);
	jumpTo('index.php?modname=coursepath&op=pathelem&id_path='.$id_path);
}

function importcourse() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	
	$out 	=& $GLOBALS['page'];
	$lang	=& DoceboLanguage::createInstance('coursepath', 'lms');
	
	$id_path = importVar('id_path', true, 0);
	$id_slot = importVar('id_slot', true, 0);
	
	$selector = new Selector_Course();
	$selector->parseForState($_POST);
	
	$path_man 	= new CoursePath_Manager();
	
	if(isset($_GET['load'])) {
		
		$initial_selection = $path_man->getSlotElem($id_path, $id_slot);
		
		if(isset($_GET['load'])) $selector->resetSelection($initial_selection);
	}
	if(isset($_POST['import'])) {
		
		$initial_selection 	= $path_man->getSlotElem($id_path, $id_slot);
		$selected_courses 	= $selector->getSelection();
		
		$to_add = array_diff($selected_courses, $initial_selection);
		$to_del = array_diff($initial_selection, $selected_courses);
		
		$re = true;
		$added_courses = array();
		$removed_courses = array();
		while(list(,$id_c) = each($to_add)) {
			
			$re_s = $path_man->addToSlot($id_path, $id_slot, $id_c);
			if($re_s) $added_courses[] = $id_c;
			$re &= $re_s;
		}
		while(list(,$id_c) = each($to_del)) {
			
			$re_s = $path_man->delFromSlot($id_path, $id_slot, $id_c);
			if($re_s) $removed_courses[] = $id_c;
			$re &= $re_s;
		}
		// update users course subscription
		require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.subscribe.php');
		
		$cpath_man 	= new CoursePath_Manager();
		$subs_man 	= new CourseSubscribe_Management();
		$users 		= $cpath_man->getSubscribed($id_path);
		
		if(!empty($added_courses) && !empty($users)) 
			$re &= $subs_man->multipleSubscribe($users , $added_courses, 3);
		
		if(!$re) die('<a href="index.php?modname=coursepath&op=pathelem&id_path='.$id_path.'">waths happen in insert ???</a>');
		if(!empty($removed_courses) && !empty($users)) 
			$re &= $subs_man->multipleUnsubscribe($users , $removed_courses);
		
		$cpath_man->fixSequence($id_path, $id_slot);
		jumpTo('index.php?modname=coursepath&op=pathelem&id_path='.$id_path.'&result='.( $re ? 'ok' : 'err' ));
	}
	
	$query_pathlist = "
	SELECT path_name, path_descr
	FROM ".$GLOBALS['prefix_lms']."_coursepath 
	WHERE id_path = '".(int)$id_path."'
	ORDER BY path_name";
	list($path_name, $path_descr) = mysql_fetch_row(mysql_query($query_pathlist));
	
	$page_title = array(
		'index.php?modname=coursepath&amp;op=pathlist' => $lang->def('_COURSE_PATH'),
		'index.php?modname=coursepath&amp;op=pathelem&amp;id_path='.$id_path => $path_name,
		$lang->def('_IMPORT_COURSE')
	);
	$out->add(
		getTitleArea($page_title, 'coursepath')
		.'<div class="std_block">'
		.Form::openForm('mancoursepath', 'index.php?modname=coursepath&amp;op=importcourse')
		.Form::getHidden('id_path', 'id_path', $id_path)
		.Form::getHidden('id_slot', 'id_slot', $id_slot)
		, 'content'
	);
	$selector->loadCourseSelector();
	$out->add(
		Form::openButtonSpace()
		.Form::getBreakRow()
		.Form::getButton('import', 'import', $lang->def('_IMPORT'))
		.Form::getButton('undoelem', 'undoelem', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function modprerequisites() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$out 	=& $GLOBALS['page'];
	$lang	=& DoceboLanguage::createInstance('coursepath', 'lms');
	
	$id_path 	= importVar('id_path', true, 0);
	$id_course 	= importVar('id_course', true, 0);
	
	$mod_perm = checkPerm('mod', true);
	
	$query_pathlist = "
	SELECT path_name 
	FROM ".$GLOBALS['prefix_lms']."_coursepath 
	WHERE id_path = '".$id_path."'";
	list($path_name) = mysql_fetch_row(mysql_query($query_pathlist));
	
	$query_pathelem = "
	SELECT id_item, prerequisites 
	FROM ".$GLOBALS['prefix_lms']."_coursepath_courses 
	WHERE id_path = '".$id_path."'";
	$repath_elem = mysql_query($query_pathelem);
	while(list($id_c, $prer) = mysql_fetch_row($repath_elem)) {
		
		$courses_in_path[$id_c] = $id_c;
		$courses_prer[$id_c] 	= $prer;
	}
	$course_man = new Man_Course();
	$course_info =& $course_man->getAllCourses();
	
	// prerequisites of this course
	$this_course_prer = array_flip(explode(',', $courses_prer[$id_course]));
	
	$in_this = '<img src="'.getPathImage('lms').'standard/check.gif" alt="'.$lang->def('_IN_THIS_COURSEPATH').'" /> ';
	
	$area_title = array(
		'index.php?modname=coursepath&amp;op=pathlist' => $lang->def('_COURSE_PATH'),
		'index.php?modname=coursepath&amp;op=pathelem&amp;id_path='.$id_path => $path_name,
		$course_info[$id_course]['name'].' - '.$lang->def('_PREREQUISITES')
	);
	$out->setWorkingZone('content');
	$out->add(getTitleArea($area_title, 'coursepath')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=coursepath&amp;op=pathelem&amp;id_path='.$id_path, $lang->def('_BACK') )
		
		.getInfoUi($lang->def('_PREREQUISITES_SELECTION_INSTRUCTION'))
		
		.Form::openForm('prerequisites', 'index.php?modname=coursepath&amp;op=writeprerequisites')
		.Form::getHidden('id_path', 'id_path', $id_path) 
		.Form::getHidden('id_course', 'id_course', $id_course) );
	
	$tb_path = new Typeone(0, $lang->def('_COURSE_PATH_PREREQUISITES'), $lang->def('_COURSE_PATH_PREREQUISITES_SUMMARY'));
	$cont_h = array(
		'<img src="'.getPathImage('fw').'standard/check.gif" alt="'.$lang->def('_ALT_CHECK').'" />', 
		$lang->def('_CODE'), 
		$lang->def('_COURSE_NAME'), 
		$lang->def('_COURSE_DESC') 
	);
	$type_h = array('image', '', '', '');
	
	$tb_path->setColsStyle($type_h);
	$tb_path->addHead($cont_h);
	while(list($id_c, $course) = each($course_info)) {
		
		if($id_c != $id_course) {
			
			if(isset($courses_prer[$id_c]) && ereg($id_course, $courses_prer[$id_c])) {
				
				// this course contain the current working course as a  prerequisites
				$cont = array(
					'<img src="'.getPathImage('lms').'course/locked.gif" alt="'.$lang->def('_LOCKED').'" />', 
					$course['code'],
					( isset($courses_in_path[$id_c]) ? $in_this : '' ).$course['name'], 
					$course['description']);
			} else {
				
				$cont = array(
					Form::getInputCheckbox(	'prerequisites_'.$id_c, 
											'prerequisites['.$id_c.']', 
											$id_c, 
											isset($this_course_prer[$id_c]), 
											'' ), 
					'<label for="prerequisites_'.$id_c.'">'.$course['code'].'</label>',
					'<label for="prerequisites_'.$id_c.'">'.( isset($courses_in_path[$id_c]) ? $in_this : '' ).$course['name'].'</label>', 
					'<label for="prerequisites_'.$id_c.'">'.$course['description'].'</label>' );
			}
			$tb_path->addBody($cont);
		}
	}
	$out->add(
		$tb_path->getTable()
		.Form::openButtonSpace()
		.Form::getButton('accept', 'accept', $lang->def('_SAVE'))
		.Form::getButton('undoelem', 'undoelem', $lang->def('_UNDO'))
		.Form::closeForm()
	);
	
	$out->add(getBackUi('index.php?modname=coursepath&amp;op=pathelem&amp;id_path='.$id_path, $lang->def('_BACK') )
		.'</div>');
}

function writeprerequisites() {
	checkPerm('mod');
	
	$id_course = importVar('id_course', true, 0);
	$id_path = importVar('id_path', true, 0);
	
	$new_prerequisites = '';
	$new_prerequisites = implode(',', $_POST['prerequisites']);
	
	$re = mysql_query("
	UPDATE ".$GLOBALS['prefix_lms']."_coursepath_courses
	SET prerequisites = '".$new_prerequisites."'
	WHERE id_path = '".$id_path."' AND id_item = '".$id_course."'");
	
	jumpTo('index.php?modname=coursepath&op=pathelem&amp;id_path='.$id_path.'&amp;result='.( $re ? 'ok' : 'err' ) );
}

function delcoursepathelem() {
	checkPerm('mod');
	
	$id_course 	= importVar('id_course', true, 0);
	$id_path 	= importVar('id_path', true, 0);
	$id_slot 	= importVar('id_slot', true, 0);
	
	if(isset($_POST['confirm'])) {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
		$cpath_man 	= new CoursePath_Manager();
		
		$re = $cpath_man->delFromSlot($id_path, $id_slot, $id_course);
		if($re) {
			// update users course subscription
			require_once($GLOBALS['where_lms'].'/lib/lib.subscribe.php');
			
			$subs_man 	= new CourseSubscribe_Management();
			
			$users 		= $cpath_man->getSubscribed($id_path);
			if(!empty($users)) $re &= $subs_man->unsubscribeUsers($users , $id_course);
		}
		jumpTo('index.php?modname=coursepath&op=pathelem&amp;id_path='.$id_path.'&amp;result='.( $re ? 'ok' : 'err' ) );
	} else {
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		
		$arr_course = array($id_course => $id_course);
		$course_info =& getCoursesInfo($arr_course);
		$lang	=& DoceboLanguage::createInstance('coursepath', 'lms');
		
		$query_pathlist = "
		SELECT path_name 
		FROM ".$GLOBALS['prefix_lms']."_coursepath 
		WHERE id_path = '".(int)$id_path."'
		ORDER BY path_name";
		list($path_name) = mysql_fetch_row(mysql_query($query_pathlist));
		
		$title_area = array(
			'index.php?modname=coursepath&amp;op=pathlist' => $lang->def('_COURSE_PATH'),
			'index.php?modname=coursepath&amp;op=pathelem&amp;id_path='.$id_path => $path_name,
			$lang->def('_DEL')
		);
		
		$GLOBALS['page']->add(
			getTitleArea($title_area, 'coursepath')
			.'<div class="std_block">'
			.Form::openForm('deletepath', 'index.php?modname=coursepath&amp;op=delcoursepath')
			.Form::getHidden('id_path', 'id_path', $id_path)
			.Form::getHidden('id_course', 'id_course', $id_course)
			.Form::getHidden('id_slot', 'id_slot', $id_slot)
			.getDeleteUi(
				$lang->def('_AREE_YOU_SURE_TO_REMOVE_COURSE_FROM_PATH'), 
				'<span class="text_bold">'.$lang->def('_COURSE_NAME').' : </span>'.$course_info[$id_course]['name'].'<br />'
				.'<span class="text_bold">'.$lang->def('_COURSE_DESC').' : </span>'.$course_info[$id_course]['description'], 
				false,
				'confirm', 
				'undoelem')
			.Form::closeForm()
			.'</div>', 'content');  
	}
}

//-----------------------------------------------------------------

function waitingsubscription() {
	checkPerm('moderate');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.subscribe.php');
	
	$id_path 	= importVar('id_path', true, 0);
	$lang		=& DoceboLanguage::createInstance('coursepath', 'lms');
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	
	if(isset($_POST['accept'])) {
		
		$cpath_man = new CoursePath_Manager();
		$courses = $cpath_man->getAllCourses(array($id_path));
		
		$subs_man = new CourseSubscribe_Management();
		
		$re = true;
		if(isset($_POST['approve_user'])) {
			
			$users_subsc = array();
			while(list($id_user) = each($_POST['approve_user'])) {
				
				$text_query = "
				UPDATE ".$GLOBALS['prefix_lms']."_coursepath_user
				SET waiting = 0
				WHERE id_path = '".$id_path."' AND idUser = '".$id_user."'";
				$re_s = mysql_query($text_query);
				if($re_s == true) $users_subsc[] = $id_user;
				$re &= $re_s;
			}
			// now subscribe user to all the course
			if(!empty($users_subsc)) $re &= $subs_man->multipleSubscribe($users_subsc, $courses, 3);
			
		}
		if(isset($_POST['deny_user'])) {
			
			while(list($id_user) = each($_POST['deny_user'])) {
				
				$text_query = "
				DELETE FROM ".$GLOBALS['prefix_lms']."_coursepath_user 
				WHERE id_path = '".$id_path."' AND idUser = '".$id_user."'";
				$re &= mysql_query($text_query);
			}
		}
		jumpTo('index.php?modname=coursepath&amp;op=pathlist&result='.( $re ? 'ok' : 'err' ));
	}
	
	
	$subscriptions = array();
	$query_subcription = "
	SELECT idUser, subscribed_by 
	FROM ".$GLOBALS['prefix_lms']."_coursepath_user 
	WHERE id_path = '".$id_path."' AND waiting = '1'";
	$re_subscription = mysql_query($query_subcription);
	while(list($id_user, $subscribed_by) = mysql_fetch_row($re_subscription)) {
		
		$subs_by[$id_user] = $subscribed_by;
		$users[$id_user] = $id_user;
		$users[$subscribed_by] = $subscribed_by;
	}
	if(!empty($users)) $users_waiting = $acl_man->getUsers($users);
	
	$tb = new typeOne(0, $lang->def('_WAITING_CAPTION'), $lang->def('_WAITING_SUMMARY'));
	
	$type_h = array('', '', '', 'image', 'image');
	$cont_h = array($lang->def('_USERNAME'), $lang->def('_FIRST_NAME_LAST_NAME'),
		$lang->def('_SUBSCRIBED_BY'),
		$lang->def('_APPROVE'),
		$lang->def('_DENY')
	);
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	
	if(!empty($users))
	while(list($id_user, $user_info) = each($users_waiting)) {
		
		$cont = array( $acl_man->relativeId($user_info[ACL_INFO_USERID]),
						$user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME], 
						$acl_man->getConvertedUserName($users_waiting[$subs_by[$id_user]]) );
						
		$cont[] = Form::getInputCheckbox(
				'approve_user_'.$id_user, 
				'approve_user['.$id_user.']', 
				$id_user, 
				false, 
				'' ).'<label class="access-only" for="approve_user_'.$id_user.'">'.$user_info[ACL_INFO_USERID].'</label>';
		
		$cont[] = Form::getInputCheckbox(
				'deny_user_'.$id_user, 
				'deny_user['.$id_user.']', 
				$id_user, 
				false, 
				'' ).'<label class="access-only" for="deny_user_'.$id_user.'">'.$user_info[ACL_INFO_USERID].'</label>';
		
		$tb->addBody($cont);
	}
	
	$query_pathlist = "
	SELECT path_name 
	FROM ".$GLOBALS['prefix_lms']."_coursepath 
	WHERE id_path = '".$id_path."' 
	ORDER BY path_name ";
	list($path_name) = mysql_fetch_row(mysql_query($query_pathlist));
	
	$GLOBALS['page']->add(
		getTitleArea( array('index.php?modname=coursepath&amp;op=pathlist' => $lang->def('_COURSE_PATH'), $path_name)
			, 'coursepath')
		.'<div class="std_block">'
		.Form::openForm('deletepath', 'index.php?modname=coursepath&amp;op=waitingsubscription')
		.Form::getHidden('id_path', 'id_path', $id_path)
		.$tb->getTable()
		.Form::openButtonSpace()
		.Form::getButton('accept', 'accept', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function addsubscription() {
	checkPerm('subscribe');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.subscribe.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	
	$id_path = importVar('id_path', true, 0);
	$lang =& DoceboLanguage::createInstance('coursepath', 'lms');
	$out =& $GLOBALS['page'];
	
	if(isset($_POST['cancelselector'])) jumpTo('index.php?modname=coursepath&amp;op=pathlist');
		
	$user_select = new Module_Directory();
	$user_select->show_user_selector = TRUE;
	$user_select->show_group_selector = TRUE;
	$user_select->show_orgchart_selector = TRUE;
	
	$query_pathlist = "
	SELECT path_name, subscribe_method 
	FROM ".$GLOBALS['prefix_lms']."_coursepath 
	WHERE id_path = '".$id_path."' 
	ORDER BY path_name ";
	list($path_name, $subscribe_method) = mysql_fetch_row(mysql_query($query_pathlist));
	
	
	if(isset($_GET['load'])) {
		
		$cp_man = new CoursePath_Manager();
		$users = $cp_man->getSubscribed($id_path);
		
		$user_select->resetSelection($users);
	}
	if(isset($_POST['okselector']))
	{
		$acl_manager = new DoceboACLManager();
		
		$user_selected 	= $user_select->getSelection($_POST);
		
		$user_selected =& $acl_manager->getAllUsersFromIdst($user_selected);
		
		$user_selected = array_unique($user_selected);
		
		$cp_man = new CoursePath_Manager();
		$users = $cp_man->getSubscribed($id_path);
		
		$user_selected = array_diff($user_selected, $users);
		
		if(empty($user_selected )) jumpTo('index.php?modname=coursepath&amp;op=pathlist');
		
		$cpath_man = new CoursePath_Manager();
		$subs_man = new CourseSubscribe_Management();
		
		$courses = $cpath_man->getAllCourses(array($id_path));
		$re = true;
		
		if($subscribe_method != 1 && !checkPerm('moderate', true)) $waiting = 1;
		else $waiting = 0;
		$users_subsc =array();
		while(list(,$id_user) = each($user_selected)) {
			
			$text_query = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_coursepath_user 
			( id_path, idUser, waiting, subscribed_by ) VALUES 
			( '".$id_path."', '".$id_user."', '".$waiting."', '".getLogUserId()."' )";
			$re_s = mysql_query($text_query);
			if($re_s == true) $users_subsc[] = $id_user;
			$re &= $re_s;
		}
		// now subscribe user to all the course
		if($waiting == 0) $re &= $subs_man->multipleSubscribe($users_subsc, $courses, 3);
		
		jumpTo('index.php?modname=coursepath&amp;op=pathlist&result='.( $re ? 'ok' : 'err' ));
	}
	
	$user_select->setPageTitle(getTitleArea( array('index.php?modname=coursepath&amp;op=pathlist' => $lang->def('_COURSE_PATH'), $path_name)
			, 'coursepath'));
	$user_select->loadSelector('index.php?modname=coursepath&amp;op=addsubscription&amp;id_path='.$id_path, 
			$lang->def('_SUBSCRIBE'), 
			$lang->def('_CHOOSE_SUBSCRIBE'), 
			true, 
			true );
}

function delsubscription() {
	checkPerm('subscribe');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.subscribe.php');
	
	$id_path 	= importVar('id_path', true, 0);
	$lang		=& DoceboLanguage::createInstance('coursepath', 'lms');
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	
	if(isset($_POST['save'])) {
		
		$cpath_man = new CoursePath_Manager();
		$courses = $cpath_man->getAllCourses(array($id_path));
		
		$subs_man = new CourseSubscribe_Management();
		
		$re = true;
		if(isset($_POST['delete_user'])) {
			
			$deleted = array();
			foreach($_POST['delete_user'] as $i => $id_user) {
				
				$text_query = "
				DELETE FROM ".$GLOBALS['prefix_lms']."_coursepath_user 
				WHERE id_path = '".$id_path."' AND idUser = '".$id_user."' ";
				if($resingle = mysql_query($text_query)) { $deleted[$id_user] = $id_user; }
				$re &= $resingle;
			}
			$re &= $subs_man->multipleUnsubscribe($deleted, $courses);
		}
		jumpTo('index.php?modname=coursepath&amp;op=pathlist&result='.( $re ? 'ok' : 'err' ));
	}
	
	$subscriptions = array();
	$query_subcription = "
	SELECT idUser
	FROM ".$GLOBALS['prefix_lms']."_coursepath_user 
	WHERE id_path = '".$id_path."' AND waiting = '0'";
	$re_subscription = mysql_query($query_subcription);
	while(list($id_user) = mysql_fetch_row($re_subscription)) {
		
		$users[$id_user] = $id_user;
	}
	if(!empty($users)) $users_info = $acl_man->getUsers($users);
	
	$tb = new typeOne(0, $lang->def('_SUBSCRIBED_CAPTION'), $lang->def('_SUBSCRIBED_SUMMARY'));
	
	$type_h = array('', '', 'image');
	$cont_h = array($lang->def('_USERNAME'), $lang->def('_FIRST_NAME_LAST_NAME'),
		'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" />'
	);
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	
	if(!empty($users))
	while(list($id_user, $user_info) = each($users_info)) {
		
		$cont = array( $acl_man->relativeId($user_info[ACL_INFO_USERID]),
						$user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME] );
		$cont[] = Form::getInputCheckbox(
				'delete_user_'.$id_user, 
				'delete_user['.$id_user.']', 
				$id_user, 
				false, 
				'' ).'<label class="access-only" for="delete_user_'.$id_user.'">'.$user_info[ACL_INFO_USERID].'</label>';
		
		$tb->addBody($cont);
	}
	
	$query_pathlist = "
	SELECT path_name 
	FROM ".$GLOBALS['prefix_lms']."_coursepath 
	WHERE id_path = '".$id_path."' 
	ORDER BY path_name ";
	list($path_name) = mysql_fetch_row(mysql_query($query_pathlist));
	
	$GLOBALS['page']->add(
		getTitleArea( array('index.php?modname=coursepath&amp;op=pathlist' => $lang->def('_COURSE_PATH'), $path_name)
			, 'coursepath')
		.'<div class="std_block">'
		.Form::openForm('deletesubscription', 'index.php?modname=coursepath&amp;op=delsubscription')
		.Form::getHidden('id_path', 'id_path', $id_path)
		.$tb->getTable()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function modslot() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	
	$out 	=& $GLOBALS['page'];
	$lang	=& DoceboLanguage::createInstance('coursepath', 'lms');
	
	$id_slot = importVar('id_slot');
	$id_path = importVar('id_path');
	
	$cpath_man = new CoursePath_Manager();
	$path = $cpath_man->getCoursepathInfo($id_path);
	
	if(isset($_POST['save'])) {
		
		if($id_slot == false) {
			
			$re = $cpath_man->createSlot($id_path, $_POST['min_selection'], $_POST['max_selection']);
		} else {
		
			$re = $cpath_man->saveSlot($id_slot, $_POST['min_selection'], $_POST['max_selection']);
		}
		jumpTo('index.php?modname=coursepath&amp;op=pathelem&amp;id_path='.$id_path);
	}
	
	if($id_slot == false) {
		
		$min_selection 			= 1;
		$max_selection 			= 1;
	} else {
		
		$slot = $cpath_man->getSlotInfo($id_slot);
		
		$min_selection = $slot[CP_SLOT_MIN];
		$max_selection = $slot[CP_SLOT_MAX];
	}
	
	$title_area = array('index.php?modname=coursepath&amp;op=pathlist' => $lang->def('_COURSE_PATH'));
	$title_area['index.php?modname=coursepath&amp;op=pathelem&amp;id_path='.$id_path] = $path['path_name'];
	$title_area[] = $lang->def('_MANAGE_SLOT');
	$out->add(
		getTitleArea($title_area, 'coursepath')
		.'<div class="std_block">'
		.Form::openForm('mancoursepath', 'index.php?modname=coursepath&amp;op=modslot')
		.Form::openElementSpace()
		.Form::getHidden('id_path', 'id_path', $id_path)
		.Form::getHidden('id_slot', 'id_slot', $id_slot)
		.Form::getTextfield($lang->def('_MIN_SELECTION'), 'min_selection', 'min_selection', 3,
			$min_selection )
		.Form::getTextfield($lang->def('_MAX_SELECTION'), 'max_selection', 'max_selection', 3,
			$max_selection )
		
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function delslot() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	
	$out 	=& $GLOBALS['page'];
	$lang	=& DoceboLanguage::createInstance('coursepath', 'lms');
	
	$id_slot = importVar('id_slot');
	$id_path = importVar('id_path');
	
	$cpath_man = new CoursePath_Manager();
	$path = $cpath_man->getCoursepathInfo($id_path);
	
	if(isset($_POST['confirm'])) {
		
		if($id_slot != false) {
			
			$re = $cpath_man->deleteSlot($id_slot, $id_path);
		}
		jumpTo('index.php?modname=coursepath&amp;op=pathelem&amp;id_path='.$id_path);
	}
	
	$slot = $cpath_man->getSlotInfo($id_slot);
	
	$title_area = array('index.php?modname=coursepath&amp;op=pathlist' => $lang->def('_COURSE_PATH'));
	$title_area['index.php?modname=coursepath&amp;op=pathelem&amp;id_path='.$id_path] = $path['path_name'];
	$title_area[] = $lang->def('_DEL_SLOT');
	
	$GLOBALS['page']->add(
			getTitleArea($title_area, 'coursepath')
			.'<div class="std_block">'
			.Form::openForm('deletepath', 'index.php?modname=coursepath&amp;op=delslot')
			.Form::getHidden('id_path', 'id_path', $id_path)
			.Form::getHidden('id_slot', 'id_slot', $id_slot)
			.getDeleteUi(
				$lang->def('_AREE_YOU_SURE_TO_DELETE_SLOT'), 
				'<span class="text_bold">'.$lang->def('_MIN_SELECTION').' : </span>'.$slot['min_selection'].'<br />'
				.'<span class="text_bold">'.$lang->def('_MAX_SELECTION').' : </span>'.$slot['max_selection'], 
				false,
				'confirm', 
				'undo')
			.Form::closeForm()
			.'</div>', 'content');
}

//-----------------------------------------------------------------

function coursepathDispatch($op) {
	
	if(isset($_POST['undo'])) $op = 'pathlist';
	if(isset($_POST['undoelem'])) $op = 'pathelem';
	switch($op) {
		case "pathlist" : {
			pathlist();
		};break;
		
		case "newcoursepath" : {
			mancoursepath(false);
		};break;
		case "modcoursepath" : {
			mancoursepath(importVar('id_path', true, 0));
		};break;
		case "savecoursepath" : {
			savecoursepath();
		};break;
		
		case "deletepath" : {
			deletepath();
		};break;
		//----------------------
		case "pathelem" : {
			pathelem();
		};break;
		case "upelem" : {
			upelem();
		};break;
		case "downelem" : {
			downelem();
		};break;
		case "importcourse" : {
			importcourse();
		};break;
		
		case "modprerequisites" : {
			modprerequisites();
		};break;
		case "writeprerequisites" : {
			writeprerequisites();
		};break;
		
		case "delcoursepath" : {
			delcoursepathelem();
		};break;
		
		//---------------------
		case "waitingsubscription" : {
			waitingsubscription();
		};break;
		case "addsubscription" : {
			addsubscription();
		};break;
		case "delsubscription" : {
			delsubscription();
		};break;
		
		case "modslot" : {
			modslot();
		};break;
		case "delslot" : {
			delslot();
		};break;
	}
}

}

?>