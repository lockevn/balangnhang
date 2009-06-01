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

/**
 * @package  DoceboLms
 * @version  $Id: public_course_admin.php 1002 2007-03-24 11:55:51Z fabio $
 * @category course
 * @author   Pirovano Fabio
 */

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

addCss('style_course_list', 'lms');
require_once($GLOBALS['where_lms'].'/lib/lib.levels.php');
function statusNoEnter($perm, $status) { return ( $perm & (1 << $status) ); }


define("_SUCCESS_course", 		"_COURSE_OPERATION_SUCCESS");
define("_SUCCESS_sub", 			"_SUBSCRIBE_OK");
define("_SUCCESS_unsub", 		"_UNSUBSCRIBE_OK");

define("_FAIL_course", 			"_COURSE_OPERATION_FAIL");
define("_FAIL_courseedition", 	"_COURSE_OPERATION_FAIL");
define("_FAIL_coursemenu", 		"_COURSE_OPERATION_FAIL");
define("_FAIL_selempty", 		"_SUBSCRIBE_SELECTION_EMPTY");
define("_FAIL_sub", 			"_SUBSCRIBE_OPERATION_FAILURE");
	

function manageCourseFile($new_file_id, $old_file, $path, $quota_available, $delete_old, $is_image = false) {

	$arr_new_file = ( isset($_FILES[$new_file_id]) && $_FILES[$new_file_id]['tmp_name'] != '' ? $_FILES[$new_file_id] : false);
	$return = array(	'filename' => $old_file,
						'new_size' => 0,
						'old_size' => 0,
						'error' => false,
						'quota_exceeded' => false);

	if(($delete_old || $arr_new_file !== false) && $old_file != '') {

		// the flag for file delete is checked or a new file was uploaded ---------------------
		$return['old_size'] = getFileSize($GLOBALS['where_files_relative'].$path.$old_file);
		$quota_available -= $return['old_size'];
		sl_unlink($path.$old_file);
		$return['filename'] = '';
	}

	if(!empty($arr_new_file)) {

		// if present load the new file --------------------------------------------------------
		$filename = $new_file_id.'_'.mt_rand(0, 100).'_'.time().'_'.$arr_new_file['name'];
		if($is_image) {

			$re = createImageFromTmp(	$arr_new_file['tmp_name'],
										$path.$filename,
										$arr_new_file['name'],
										150,
										150,
										true );

			if($re < 0) $return['error'] = true;
			else {

				// after resize check size ------------------------------------------------------------
				$size = getFileSize($GLOBALS['where_files_relative'].$path.$filename);
				if($size > $quota_available) {
					$return['quota_exceeded'] = true;
					sl_unlink($path.$filename);
				} else {
					$return['new_size'] = $size;
					$return['filename'] = $filename;
				}
			}
		} else {

			// check if the filesize don't exceed the quota ----------------------------------------
			$size = getFileSize($arr_new_file['tmp_name']);

			if($size > $quota_available) $return['quota_exceeded'] = true;
			else {
				// save file ---------------------------------------------------------------------------
				if(!sl_upload($arr_new_file['tmp_name'], $path.$filename)) $return['error'] = true;
				else {
					$return['new_size'] = $size;
					$return['filename'] = $filename;
				}
			}
		}
	}
	return $return;
}

function getSelCourseInfo($id, $sel_id, $edition=FALSE, $row_info, & $lang) {
	require_once($GLOBALS["where_framework"]."/lib/lib.mimetype.php");

	$name 		= ( isset($row_info["name"]) ? $row_info["name"] : '' );
	$desc 		= ( isset($row_info["desc"]) ? $row_info["desc"] : '' );
	
	if($edition !== false) $teacher =& fromIdstToUser(getSubscribed($row_info['idCourse'], false, 6, true, $id));
	else $teacher 	= ( isset($row_info["teacher"]) ? $row_info["teacher"] : '' );
	
	$auto_sub 	= ( isset($row_info["auto_sub"]) ? $row_info["auto_sub"] : '' );
	$show_rules = ( isset($row_info["show_rules"]) ? $row_info["show_rules"] : '' );
	$end_mode 	= ( isset($row_info["end_mode"]) ? $row_info["end_mode"] : '' );
	$waiting 	= ( isset($row_info["waiting"]) ? $row_info["waiting"] : '' );

	if (!$edition) {

		$prefix 		= "";
		$check_id 		= (int)$id;
		$check_sel_id 	= (int)$sel_id;
	} else {

		$prefix 		= "edition_";
		$check_id 		= $id;
		$check_sel_id 	= $sel_id;
	}

	$course_info['base'] = ( $prefix.$check_id == $check_sel_id ?
			'<input type="image" class="button_image" '
			.'src="'.getPathImage().'standard/less.gif" '
			.'alt="'.$lang->def('_LESS_INFO').'" '
			.'id="sel_course_'.$prefix.'0" '
			.'name="sel_course['.$prefix.'0]"  />' : '' )
		.( $prefix.$check_id != $check_sel_id ?
			'<input type="image" class="button_image" '
			.'src="'.getPathImage().'standard/more.gif" '
			.'alt="'.$lang->def('_MORE_INFO').'" '
			.'id="sel_course_'.$prefix.$check_id.'" '
			.'name="sel_course['.$prefix.$check_id.']" />' : '' )
		.$name;

	$course_info['extra'] = false;

	if( $prefix.$check_id == $check_sel_id) {

		$GLOBALS['page']->add('<a hrf="#more_'.$prefix.$check_id.'">'.$lang->def('_JUMP_TO_EXTRACOURSE_OPENED_INFO').'</a>', 'blind_navigation');
		$GLOBALS['page']->add('
		<script type="text/javascript">
			<!--
			var temp = window.onload;
			if (typeof(window.onload) != "function") {

				window.onload = function() {
					var extra_info = document.getElementById("more_'.$prefix.$check_id.'_a");
					extra_info.focus();
				}
			} else {

				window.onload = function() {
					temp();
					var extra_info = document.getElementById("more_'.$prefix.$check_id.'_a");
					extra_info.focus();
				}
			}
			-->
		</script>
		','page_head');

		$course_info['extra'] = '<div id="more_'.$prefix.$check_id.'" class="course_more_info">'
			.'<span>'.$lang->def('_COURSE_TEACHERS').' : </span> '
				.( count($teacher) ? implode(', ', $teacher) : $lang->def('_NONE') )
			.'<br />';

		if (!$edition) {
			$course_info['extra'].=
					'<span>'.$lang->def('_COURSE_SUBSRIBE').' : </span> '
					.( $auto_sub == 0 ? $lang->def('_COURSE_S_GODADMIN') : '' )
					.( $auto_sub == 1 ? $lang->def('_COURSE_S_MODERATE') : '' )
					.( $auto_sub == 2 ? $lang->def('_COURSE_S_FREE') : '' ).'<br />'

					.'<span>'.$lang->def('_WHERE_SHOW_COURSE').' : </span> '
					.( $show_rules == 0 ? $lang->def('_SC_EVERYWHERE') : '' )
					.( $show_rules == 1 ? $lang->def('_SC_ONLY_IN') : '' )
					.( $show_rules == 2 ? $lang->def('_SC_ONLYINSC_USER') : '' ).'<br />'
					.'<span>'.$lang->def('_COURSE_END_MODE').' : </span> '
					.( $end_mode ?
							$lang->def('_COURSE_EM_TEACHER') :
							$lang->def('_COURSE_EM_LO') ).'<br />';
		}

		$course_info['extra'].=
				'<span>'.$lang->def('_COURSE_WAITING_USER').' : </span>'
					.( isset($waiting[$id]) ? $waiting[$id] : $lang->def('_NONE') )
				.'<br />'
				.'<div class="nofloat"></div>'
				.'<span>'.$lang->def('_DESCRIPTION').' : </span> '
				.'<div class="description">'
				.( isset($_POST['c_filter_descr']) && ($_POST['c_filter_descr'] != '') ?
					eregi_replace($_POST['c_filter_descr'], '<span class="filter_evidence">'.$_POST['c_filter_descr'].'</span>', $desc) :
					$desc )
				.'</div>';

		$attach_list="";
		if ((isset($row_info["img_material"])) && (!empty($row_info["img_material"]))) {
			$filename=$row_info["img_material"];
			$ext=strtolower(end(explode(".", $filename)));
			$img ="<img src=\"".getPathImage('fw').mimeDetect($filename)."\" ";
			$img.="alt=\"".$ext."\" title=\"".$ext."\" />";
			$break_apart = explode('_', $filename);
			$break_apart[0] = $break_apart[1] = $break_apart[2] = '';
			$filename = substr(implode('_', $break_apart), 3);
			$attach_list.='<span>'.$lang->def('_USER_MATERIAL').' : </span>';
			$attach_list.=$img." ".$filename."<br />";
		}
		if ((isset($row_info["img_othermaterial"])) && (!empty($row_info["img_othermaterial"]))) {
			$filename=$row_info["img_othermaterial"];
			$ext=strtolower(end(explode(".", $filename)));
			$img ="<img src=\"".getPathImage('fw').mimeDetect($filename)."\" ";
			$img.="alt=\"".$ext."\" title=\"".$ext."\" />";
			$break_apart = explode('_', $filename);
			$break_apart[0] = $break_apart[1] = $break_apart[2] = '';
			$filename = substr(implode('_', $break_apart), 3);
			$attach_list.='<span>'.$lang->def('_OTHER_USER_MATERIAL').' : </span> ';
			$attach_list.=$img." ".$filename."<br />";
		}
		if ((isset($row_info["imgSponsor"])) && (!empty($row_info["imgSponsor"]))) {
			$filename=$row_info["imgSponsor"];
			$ext=strtolower(end(explode(".", $filename)));
			$img ="<img src=\"".getPathImage('fw').mimeDetect($filename)."\" ";
			$img.="alt=\"".$ext."\" title=\"".$ext."\" />";
			$break_apart = explode('_', $filename);
			$break_apart[0] = $break_apart[1] = $break_apart[2] = '';
			$filename = substr(implode('_', $break_apart), 3);
			$attach_list.='<span>'.$lang->def('_SPONSOR_LOGO').' : </span> ';
			$attach_list.=$img." ".$filename."<br />";
		}
		if ((isset($row_info["img_course"])) && (!empty($row_info["img_course"]))) {
			$filename=$row_info["img_course"];
			$ext=strtolower(end(explode(".", $filename)));
			$img ="<img src=\"".getPathImage('fw').mimeDetect($filename)."\" ";
			$img.="alt=\"".$ext."\" title=\"".$ext."\" />";
			$break_apart = explode('_', $filename);
			$break_apart[0] = $break_apart[1] = $break_apart[2] = '';
			$filename = substr(implode('_', $break_apart), 3);
			$attach_list.='<span>'.$lang->def('_COURSE_LOGO').' : </span> ';
			$attach_list.=$img." ".$filename."<br />";
		}
		if ((isset($row_info["course_demo"])) && (!empty($row_info["course_demo"]))) {
			$filename=$row_info["course_demo"];
			$ext=strtolower(end(explode(".", $filename)));
			$img ="<img src=\"".getPathImage('fw').mimeDetect($filename)."\" ";
			$img.="alt=\"".$ext."\" title=\"".$ext."\" />";
			$break_apart = explode('_', $filename);
			$break_apart[0] = $break_apart[1] = $break_apart[2] = '';
			$filename = substr(implode('_', $break_apart), 3);
			$attach_list.='<span>'.$lang->def('_COURSE_DEMO').' : </span> ';
			$attach_list.=$img." ".$filename."<br />";
		}

		if (!empty($attach_list)) {
			$course_info['extra'].='<div class="nofloat"></div>'.$attach_list;
		}
		
		// Classroom info --------------------------
		if ((hasClassroom($row_info["course_type"])) && (!empty($row_info["classrooms"]))) {
			require_once($GLOBALS["where_lms"]."/lib/lib.classroom.php");
			$cm=new ClassroomManager();

			$where="t1.idClassroom IN (".$row_info["classrooms"].")";

			$rooms=$cm->getClassroomList(FALSE, FALSE, $where);

			foreach($rooms["data_arr"] as $classroom) {
				$course_info['extra'].="<span>".def("_CLASSROOM", "admin_classroom", "lms").": </span>".$classroom["name"]."<br />\n";
				$course_info['extra'].="<span>".def("_LOCATION", "admin_classroom", "lms").": </span>".$classroom["location"]."<br />\n";
			}
		}

		$course_info['extra'].='<a id="more_'.$prefix.$check_id.'_a" name="more_'.$prefix.$check_id.'_a"></a>'
				.'</div>';
	} // end if
	return $course_info;
}

function addEditionRow($even, &$tb, &$edition, &$edition_users, $id_course, $sel_id_course, $can_subscribe, $can_moderate, $can_mod, $can_del, $ini) {

	$lang 	=& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
	
	$rowcnt 		= array();
	$edition_id 	= $edition['idCourseEdition'];
	$course_info 	= getSelCourseInfo($edition_id, $sel_id_course, TRUE, $edition, $lang);
	
	$status_list 	= array(
		CST_PREPARATION => $lang->def('_CST_PREPARATION'), 
		CST_AVAILABLE 	=> $lang->def('_CST_AVAILABLE'), 
		CST_EFFECTIVE 	=> $lang->def('_CST_CONFIRMED'), 
		CST_CONCLUDED 	=> $lang->def('_CST_CONCLUDED'), 
		CST_CANCELLED 	=> $lang->def('_CST_CANCELLED')
	);
	
	$edition_type 	= ( isset($edition["edition_type"]) ? $edition["edition_type"] : '' );
	$has_waiting 	= ( (isset($edition_users[$edition_id]) && $edition_users[$edition_id]["waiting"]) ? true : false );
	
	switch($edition_type) {
		case "classroom" : 	{ $course_type_trans = $lang->def('_COURSE_TYPE_CLASSROOM'); };break;
		case "blended" : 	{ $course_type_trans = $lang->def('_COURSE_TYPE_BLENDED'); };break;
		case "elearning" : 	{ $course_type_trans = $lang->def('_COURSE_TYPE_ELEARNING'); };break;
	}
	$date_begin = $edition["date_begin"];
	$date_end 	= $edition["date_end"];
	
	$edition["teacher"] = '';
	$edition["desc"] 	= $edition['description'];
	
	$users_tot = (isset($edition_users[$edition_id]) 
		? $edition_users[$edition_id]["user_count"] - $edition_users[$edition_id]["waiting"] 
		: "0" );
	
	// rowcnt -----------------------------------------------------------------
	
	$rowcnt[] = $edition["code"];
	$rowcnt[] = '<img src="'.getPathImage().'course/icon_edition.gif" '
					.'alt="'.$lang->def("_EDITION").'" '
					.'title="'.$lang->def("_EDITION").'" />';
	$rowcnt[] = $course_info['base'];
	$rowcnt[] = $status_list[$edition["status"]];
	
	$rowcnt[] = $users_tot.( $edition["max_num_subscribe"] > 0 ? "/".$edition["max_num_subscribe"] : '' );
	
	$rowcnt[] = $course_type_trans;

	$rowcnt[] = ($date_begin == "0000-00-00" ? "&nbsp;" : $GLOBALS["regset"]->databaseToRegional($date_begin, "date") );
	$rowcnt[] = ($date_end == "0000-00-00" ? "&nbsp;" : $GLOBALS["regset"]->databaseToRegional($date_end, "date") );
	
	// classroom to edition if edition is blended or classroom ----------------
	
	if(hasClassroom($edition_type)) {
		
		$rowcnt[] = '<input type="image" class="button_image" '
			.'id="classroom_to_edition_'.$edition_id.'" '
			.'name="classroom_to_edition['.$edition_id.']" '
			.'alt="'.$lang->def('_ALT_CLASSROOM_TO_COURSE').'" '
			.'title="'.$lang->def('_CLASSROOM_TO_COURSE').'" '
			.'src="'.getPathImage().'standard/classroom.gif" />';
	} else {
		
		$rowcnt[] = '' ;
	}
	
	$rowcnt[] = "&nbsp;";

	if(($can_moderate) && $has_waiting) {
		
		$rowcnt[] = '<a href="index.php?modname=public_subscribe_admin&amp;op=waitinguser&amp;id_course='.$id_course.'&edition='.$edition_id.'&amp;ini_hidden='.$ini.'"'
			.' title="'.$lang->def('_USERWAITING').'">'
			.'<strong>'.$edition_users[$edition_id]["waiting"].'</strong>'
			//.'<img src="'.getPathImage('fw').'standard/moderate.gif" alt="'.$lang->def('_ALT_USERWAITING').'" />'
			.'</a>';
	} else {
		$rowcnt[] = '';
	}
	
	if($can_subscribe) {
		require_once($GLOBALS['where_framework'].'/lib/lib.preference.php');
		$pref = new UserPreferences(getLogUserId());
		
		$rowcnt[] = (!(!$pref->getPreference('admin_rules.max_course_subscribe') && $pref->getPreference('admin_rules.limit_course_subscribe') == 'on') ? '<a href="index.php?modname=public_subscribe_admin&amp;load=1&amp;op=subscribeadd&amp;id_course='.$id_course.'&amp;edition='.$edition_id.'&amp;ini_hidden='.$ini.'"'
			.' title="'.$lang->def('_ADD_SUBSCRIBE').'">'
			.'<img src="'.getPathImage().'subscribe/add_subscribe.gif" alt="'.$lang->def('_ALT_ADD_SUSCRIBE').'" />'
			.'</a>' : '');
		
		$rowcnt[] = '<a href="index.php?modname=public_subscribe_admin&amp;op=subscribemod&amp;id_course='.$id_course.'&amp;edition='.$edition_id.'&amp;ini_hidden='.$ini.'"'
			.' title="'.$lang->def('_MOD_SUBSCRIBE').'">'
			.'<img src="'.getPathImage().'subscribe/mod_subscribe.gif" alt="'.$lang->def('_ALT_MOD_SUSCRIBE').'" />'
			.'</a>';
		
		$rowcnt[] = '<a href="index.php?modname=public_subscribe_admin&amp;op=subscribedel&amp;id_course='.$id_course.'&amp;edition='.$edition_id.'&amp;ini_hidden='.$ini.'"'
			.' title="'.$lang->def('_DEL_SUBSCRIBE').'">'
			.'<img src="'.getPathImage().'subscribe/del_subscribe.gif" alt="'.$lang->def('_DEL').'" />'
			.'</a>';
	}

	$rowcnt[] = "&nbsp;";
	$rowcnt[] = "&nbsp;";

	if($can_mod) {
		
		$rowcnt[] = '<input type="image" class="button_image" '
			.'id="mod_course_edition'.$edition['idCourseEdition'].'" '
			.'name="mod_course_edition['.$edition['idCourseEdition'].']" '
			.'alt="'.$lang->def('_ALT_EDITION_MOD').'" '
			.'title="'.$lang->def('_COURSE_EDITION_MODIFY').'" '
			.'src="'.getPathImage().'standard/mod.gif" />';
	}
	if($can_del) {
		
		$rowcnt[] = '<input type="image" class="button_image" '
			.'id="del_course_edition'.$edition['idCourseEdition'].'" '
			.'name="del_course_edition['.$edition['idCourseEdition'].']" '
			.'alt="'.$lang->def('_ALT_EDITION_DEL').'" '
			.'title="'.$lang->def('_COURSE_EDITION_DELETE').'" '
			.'src="'.getPathImage().'standard/rem.gif" />';
	}
	
	if($even%2) $style="edition_line line-".($even%2);
	else $style="edition_line line-1";
	
	$tb->addBody($rowcnt, $style);
	if($course_info['extra'] != false) $tb->addBodyExpanded($course_info['extra'], 'course_more_info');
}

function course() {

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.sessionsave.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_lms'].'/admin/modules/category/category.php');
	require_once($GLOBALS['where_lms'].'/admin/modules/category/tree.category.php');
	
	$lang 	=& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
	$out 	=& $GLOBALS['page'];
	
	$categoryDb = new TreeDb_CatDb($GLOBALS['prefix_lms'].'_category');
	$treeView 	= new TreeView_CatView($categoryDb, 'course_category', $lang->def('_COURSE_CATEGORY'));
	
	if(isset($_SESSION['course_category']['tree_status'])) {
		$arr_state = @unserialize(($_SESSION['course_category']['tree_status']));
		if(is_array($arr_state)) $treeView->setState($arr_state);
		
	}
	$treeView->parsePositionData($_POST, $_POST, $_POST);
	$_SESSION['course_category']['tree_status'] = (serialize($treeView->getState()));
	
	// -------------------------------------------------------------------
	$status_list = array(
		CST_PREPARATION => $lang->def('_CST_PREPARATION'), 
		CST_AVAILABLE 	=> $lang->def('_CST_AVAILABLE'), 
		CST_EFFECTIVE 	=> $lang->def('_CST_CONFIRMED'), 
		CST_CONCLUDED 	=> $lang->def('_CST_CONCLUDED'), 
		CST_CANCELLED 	=> $lang->def('_CST_CANCELLED')
	);
	// -------------------------------------------------------------------
	
	$GLOBALS['page']->setWorkingZone('content');
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_COURSE'), 'course')
		.'<div class="std_block">');
	
	if($treeView->op) {

		//category operation ---------------------------------------------
		$GLOBALS['page']->add(
			Form::openForm('course_category', 'index.php?modname=public_course_admin&amp;op=course_list'));
		
		categoryDispatch($treeView->op, $treeView);
		
		$GLOBALS['page']->add(
			Form::closeForm()
			.'</div>'
		, 'content');
		$_SESSION['course_category']['tree_status'] = serialize($treeView->getState());
		return;
	}
	
	//-------------------------------------------------------------------------
	// standard operation -----------------------------------------------------
	
	// display result ---------------------------------------------------------
	if(isset($_GET['result'])) { $GLOBALS['page']->add( guiResultStatus($lang, $_GET['result']), 'content'); }
	
	//course normal display
	$out->add(
		Form::openForm('course_list', 'index.php?modname=public_course_admin&amp;op=course_list')
		.$treeView->load()
		.$treeView->loadActions()
		.'<br />'
	, 'content');

	$id_categories 		= array();
	$id_category 		= $treeView->getSelectedFolderId();
	$id_categories 		= $categoryDb->getDescendantsId($categoryDb->getFolderById($id_category));
	$id_categories[] 	= $id_category;	//add selected folder

	$can_add 			= checkPerm('add', true);
	$can_mod 			= checkPerm('mod', true);
	$can_del 			= checkPerm('del', true);
	$can_subscribe 		= checkPerm('subscribe', true);
	$can_moderate		= checkPerm('moderate', true);
	
	$pref = new UserPreferences(getLogUserId());
	if(!$pref->getPreference('admin_rules.max_user_insert') && $pref->getPreference('admin_rules.limit_user_insert') == 'on' && $GLOBALS['platform'] == 'lms')
		$can_subscribe = false;
	
	$flat = isset($_POST['c_flatview']);
	$tb = new TypeOne($GLOBALS['lms']['visu_course'], $lang->def('_COURSE_LIST_CAPTION'), $lang->def('_COURSE_LIST_SUMMARY'));
	$tb->initNavBar('ini', 'button');
	
	// ----------------------------------------------------------------------------var_dump($_SESSION['course_category']['ini_status']);
	
	if(!isset($_SESSION['course_category']['last_cat_selected']) 
			|| $_SESSION['course_category']['last_cat_selected'] != $id_category) {
		
		$_SESSION['course_category']['last_cat_selected'] = $id_category;
		$ini = 0;
	} else {
		
		if(isset($_POST['filter']) || isset($_POST['clean_filter'])) {
		
			$ini =  0;
		} else {
			
			$ini = ( isset($_POST['ini_hidden']) && !$tb->asSelected() 
				? $_POST['ini_hidden'] 
				: $tb->getSelectedElement() );
		}
		if(isset($_SESSION['course_category']['ini_status'])) {
			
			$ini = $_SESSION['course_category']['ini_status'];
			unset($_SESSION['course_category']['ini_status']);
		}
	}
	if(isset($_POST['sel_course'])) list($sel_id_course) = each($_POST['sel_course']);
	else $sel_id_course = false;
	
	// Manage filter -------------------------------------------------------------
	
	$c_filter = '';
	$c_expire = '';
	if(isset($_POST['clean_filter'])) {
		
		unset($_SESSION['course_category']['filter_status']);
	} elseif(isset($_POST['c_filter'])) {
		
		$c_filter = importVar('c_filter', false);
		$c_expire = importVar('c_expire', false);
		if(!isset($_SESSION['course_category']['filter_status'])) {
			
			$filt = array();
			$filt['c_filter'] = $c_filter;
			$filt['c_expire'] = $c_expire;
			$_SESSION['course_category']['filter_status'] = (serialize($filt));
		}
	} elseif(isset($_SESSION['course_category']['filter_status'])) {
		
		$filt = unserialize(($_SESSION['course_category']['filter_status']));
		$c_filter = $filt['c_filter'];
		$c_expire = $filt['c_expire'];
	}
	if(isset($_POST['filter'])) {
		
		$filt = array();
		$filt['c_filter'] = $c_filter;
		$filt['c_expire'] = $c_expire;
		$_SESSION['course_category']['filter_status'] = (serialize($filt));
	}
	// Filter--------------------------------------------------------------------
	
	$out->add(
		Form::getOpenFieldset($lang->def('_FILTER'))
		
		.Form::getTextfield($lang->def('_SEARCH'), 'c_filter', 'c_filter', '255', $c_filter)
		.Form::getDropdown($lang->def('_FILTER_EXPIRE'), 'c_expire', 'c_expire', array(	0 => $lang->def('_FE_NONE'),
																						1 => $lang->def('_FE_WITHOUT_EXPIRED'),
																						2 => $lang->def('_FE_NOT_EXPIRED'),
																						3 => $lang->def('_FE_EXPIRED')),
																				( isset($_POST['c_expire']) ? $c_expire : 0 ) )

		.Form::getCheckbox($lang->def('_FILTER_FLATVIEW'), 'c_flatview', 'c_flatview', '1',
			isset($_POST['c_flatview']),
			' onclick="submit();" ' )
		
		.Form::openButtonSpace()
		.Form::getButton('filter', 'filter', $lang->def('_SEARCH'), 'button_nowh')
		.( $c_filter != '' || $c_expire != false
			? '&nbsp;'.Form::getButton('clean_filter', 'clean_filter', $lang->def('_CLEAN_FILTER'), 'button_nowh')
			: '')
		.Form::closeButtonSpace()
		
		.Form::getCloseFieldset()
	, 'content');
	
	// Retriving subscribed user -----------------------------------------------------------
	
	$select = " SELECT c.idCourse, c.code, c.name, c.description, c.status, c.difficult,
		c.subscribe_method, c.permCloseLo, c.show_rules, c.max_num_subscribe, c.course_edition,c.classrooms,c.course_type,
		c.date_begin,c.date_end,c.imgSponsor, c.img_course, c.img_material, c.img_othermaterial, c.course_demo ";
	
	$query_course = " FROM ".$GLOBALS['prefix_lms']."_course AS c
	WHERE c.idCategory IN ( ".( !$flat ? $id_category  : implode(",", $id_categories) )." )
		AND c.course_type <> 'assessment'";

	if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {
		
		// if the usre is a subadmin with only few course assigned
		require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');

		$course_man = new AdminCourseManagment();
		$all_courses =& $course_man->getUserAllCourses( getLogUserId() );

		if(empty($all_courses)) $query_course .= " AND 0 ";
		else $query_course .= " AND c.idCourse IN (".implode(',', $all_courses).") ";
	}
	if($c_filter != '') {
		$query_course .= " AND ( "
				." c.code LIKE '%".$c_filter."%' OR "
				." c.name LIKE '%".$c_filter."%' OR "
				." c.description LIKE '%".$c_filter."%' )";
	}
	if($c_expire != '') {
		switch($c_expire) {
			case 1 : $query_course .= " AND c.date_end = '0000-00-00'";break;
			case 2 : $query_course .= " AND UNIX_TIMESTAMP(c.date_end) >= '".time()."' ";break;
			case 3 : $query_course .= " AND UNIX_TIMESTAMP(c.date_end) <= '".time()."' AND c.date_end <> '0000-00-00'";break;
		}
	}
	list($tot_course) = mysql_fetch_row(mysql_query("SELECT COUNT(*)".$query_course));
	$query_course .= " ORDER BY c.name
						LIMIT ".$ini.",".(int)$GLOBALS['lms']['visu_course'];
	$re_course = mysql_query($select.$query_course);
	
	//show result-------------------------------------------------------------------

	$col_type = array('image','image','name_col','align_center','image','align_center','image nowrap','image nowrap','image');
	$col_content = array(
		$lang->def('_CODE'),
		'',
		str_replace('[more]', '<img src="'.getPathImage().'standard/more.gif" alt="'.$lang->def('_MORE_INFO').'" />', $lang->def('_COURSE_NAME_TABLE')),
		$lang->def('_STATUS'),
		$lang->def('_ENROL_COUNT'),
		$lang->def('_COURSE_TYPE'),
		$lang->def("_DATE_BEGIN"),
		$lang->def("_DATE_END"),
		
		'<img src="'.getPathImage().'standard/classroom.gif" alt="'.$lang->def('_ALT_CLASSROOM_TO_COURSE').'"'
			.' title="'.$lang->def('_CLASSROOM_TO_COURSE').'" />'
	);
	
	if ($can_moderate)
		{
			$col_type[] = 'image';
			$col_content[] = '<img src="'.getPathImage('lms').'course/pdf.gif" alt="'.$lang->def('_MANAGE_CERTIFICATIONS').'"'
				.' title="'.$lang->def('_MANAGE_CERTIFICATIONS').'" />';
			
			$col_type[] = 'image';
			$col_content[] = '<img src="'.getPathImage('fw').'standard/moderate.gif" alt="'.$lang->def('_ALT_USERWAITING').'"'
				.' title="'.$lang->def('_USERWAITING').'" />';
		}
	
	if($can_subscribe) {
		
		$col_type[] = 'image';
		$col_content[] = '<img src="'.getPathImage().'subscribe/add_subscribe.gif" alt="'.$lang->def('_ALT_ADD_SUSCRIBE').'"'
				.' title="'.$lang->def('_ADD_SUBSCRIBE').'" />';
		
		$col_type[] = 'image';
		$col_content[] = '<img src="'.getPathImage().'subscribe/mod_subscribe.gif" alt="'.$lang->def('_ALT_MOD_SUSCRIBE').'"'
				.' title="'.$lang->def('_MOD_SUBSCRIBE').'" />';
		
		$col_type[] = 'image';
		$col_content[] = '<img src="'.getPathImage().'subscribe/del_subscribe.gif" alt="'.$lang->def('_DEL').'"'
				.' title="'.$lang->def('_DEL_SUBSCRIBE').'" />';
	}
	//modify action ----------------------------------------------------
	if($can_mod) {
		
		$col_type[] = 'image';
		$col_content[] = '<img src="'.getPathImage().'course/assign_menu.gif" '
			.'alt="'.$lang->def('_ALT_ASSIGN_MENU').'" '
			.'title="'.$lang->def('_ASSIGN_MENU').'" />';
		
		$col_type[] = 'image';
		$col_content[] = '<img src="'.getPathImage().'standard/move.gif" '
			.'alt="'.$lang->def('_ALT_MOVE', 'standard').'" '
			.'title="'.$lang->def('_COURSE_MOVE').'" />';
		
		$col_type[] = 'image';
		$col_content[] = '<img src="'.getPathImage().'standard/mod.gif" '
			.'alt="'.$lang->def('_MOD', 'standard').'" '
			.'title="'.$lang->def('_COURSE_MODIFY').'" />';
	}
	//delete action ----------------------------------------------------
	if($can_del) {
		
		$col_type[] = 'image';
		$col_content[] = '<img src="'.getPathImage().'standard/rem.gif" '
			.'alt="'.$lang->def('_DEL', 'standard').'" '
			.'title="'.$lang->def('_COURSE_DELETE').'" />';
	}


	// Getting list of courses editions ---------------------------------------
	// ------------------------------------------------------------------------
	$courses_list = array();
	if($GLOBALS['current_user']->getUserLevelId() == ADMIN_GROUP_PUBLICADMIN) {

		if($re_course && mysql_num_rows($re_course) > 0) {
			
			while($row = mysql_fetch_assoc($re_course)) {

				$courses_list[] = $row["idCourse"];
			}
			mysql_data_seek($re_course, 0);
		}
	} else {
		// $all_courses should already been set.
		$courses_list = $all_courses;
	}

	$editions 		= array();
	$query_edition 	= "
	SELECT * 
	FROM ".$GLOBALS["prefix_lms"]."_course_edition 
	WHERE idCourse IN (".implode(',', $courses_list).") 
	ORDER BY date_begin";
	$re_edition = mysql_query($query_edition);
	if($re_edition) while($row = mysql_fetch_assoc($re_edition)) {
		
		$editions[$row["idCourse"]][] = $row;
	}
	
	// users statistics --------------------------------------------------------------
	
	$edition_users 		= array();
	$user_count 		= array();
	$user_subscribed 	= array();
	
	 $query_stats ="
	SELECT idCourse, edition_id, sum(waiting = '1') as waiting, COUNT(*) as user_count 
	FROM ".$GLOBALS["prefix_lms"]."_courseuser 
	WHERE idCourse IN ( ".implode(',', $courses_list)." ) 
	GROUP BY idCourse, edition_id ";
	$re_stats = mysql_query($query_stats);

	if($re_stats) 
	while($row = mysql_fetch_assoc($re_stats)) {
		
		if($row["edition_id"] == 0) {
			
			$waiting[$row['idCourse']] = $row['waiting'];
			$user_subscribed[$row['idCourse']] = $row['user_count'] - $row['waiting'];
		} else {
			
			$edition_users[$row["edition_id"]] = $row;
		}
	}
	// ------------------------------------------------------------------------
	
	$tb->setColsStyle($col_type);
	$tb->addHead($col_content);
	while(list($id_course, $code, $name, $desc, $status, $difficult, $auto_sub, $end_mode, $show_rules, $max_user_sub, $course_edition, 
			$classrooms,$course_type,$date_begin,$date_end,$imgSponsor, $img_course, $img_material, $img_othermaterial, $course_demo) = mysql_fetch_row($re_course)) {

		$tb_content = array();
		
		$row_info["name"] 			= ($c_filter != ''
										? eregi_replace($c_filter, '<strong class="filter_evidence">'.$c_filter.'</strong>', $name)
										: $name );
		$row_info["desc"] 			= ($c_filter != ''
										? eregi_replace($c_filter, '<strong class="filter_evidence">'.$c_filter.'</strong>', $desc)
										: $desc );

		$row_info["teacher"] 		=& fromIdstToUser(getSubscribed($id_course, false, 6, true));;
		$row_info["auto_sub"] 		= $auto_sub;
		$row_info["show_rules"] 	= $show_rules;
		$row_info["end_mode"] 		= $end_mode;
		
		$row_info["img_material"] 	= $img_material;
		$row_info["img_othermaterial"] = $img_othermaterial;
		$row_info["imgSponsor"] 	= $imgSponsor;
		$row_info["img_course"] 	= $img_course;
		
		$row_info["course_demo"] 	= $course_demo;
		$row_info["waiting"] 		= ( isset($waiting[$id_course]) ? $waiting[$id_course] : '' );
		$row_info["course_type"] 	= $course_type;
		$row_info["classrooms"] 	= $classrooms;
		
		$course_info = getSelCourseInfo($id_course, $sel_id_course, FALSE, $row_info, $lang);
	
		switch($course_type) {
			case "classroom" : 	{ $course_type_trans = $lang->def('_COURSE_TYPE_CLASSROOM'); };break;
			case "blended" : 	{ $course_type_trans = $lang->def('_COURSE_TYPE_BLENDED'); };break;
			case "elearning" : 	{ $course_type_trans = $lang->def('_COURSE_TYPE_ELEARNING'); };break;
		}
		
		// Print the row ----------------------------------------------------------------
		$tb_content[] = ($c_filter != ''
							? eregi_replace($c_filter, '<strong class="filter_evidence">'.$c_filter.'</strong>', $code)
							: $code );
		$tb_content[] = '<img src="'.getPathImage().'course/icon_course.gif" alt="'.$lang->def("_COURSE").'" title="'.$lang->def("_COURSE").'" />';
		$tb_content[] = $course_info['base'];
		$tb_content[] = $status_list[$status];
		
		// number of user subscribed ----------------------------------------------------
		
		if ($course_edition) {
			$tb_content[] = "&nbsp;";
		} elseif(isset($user_subscribed[$id_course])) {
			$tb_content[] = ( $max_user_sub > 0 ? numberOfUserViewed($id_course).' ('.($max_user_sub - $user_subscribed[$id_course]).')' : numberOfUserViewed($id_course) );
		} else {
			$tb_content[] = ( $max_user_sub > 0 ? $max_user_sub : '0' );
		}
		$tb_content[] = $course_type_trans;
		$tb_content[] = ($date_begin == "0000-00-00" ? "&nbsp;" : $GLOBALS["regset"]->databaseToRegional($date_begin, "date"));
		$tb_content[] = ($date_begin == "0000-00-00" ? "&nbsp;" : $GLOBALS["regset"]->databaseToRegional($date_end, "date"));
		
		//classroom to course if course is blended or classroom -------------------------
		
		if(hasClassroom($course_type) && ($course_edition != 1)) {
			
			$tb_content[] = '<input type="image"'
				.' class="button_image"'
				.' id="classroom_to_course_'.$id_course.'"'
				.' name="classroom_to_course['.$id_course.']"'
				.' alt="'.$lang->def('_ALT_CLASSROOM_TO_COURSE').'"'
				.' title="'.$lang->def('_CLASSROOM_TO_COURSE').'"'
				.' src="'.getPathImage().'standard/classroom.gif" />';
		} else {
			$tb_content[] = '' ;
		}
		
		// manage certification and user approvation ------------------------------------
		if($can_moderate) {
			
			$tb_content[] = '<a href="index.php?modname=public_course_admin&amp;op=certifications&amp;id_course='.$id_course.'&amp;ini_hidden='.$ini.'">'
				.'<img src="'.getPathImage('lms').'course/pdf.gif" alt="'.$lang->def('_MANAGE_CERTIFICATIONS').'"'
				.' title="'.$lang->def('_MANAGE_CERTIFICATIONS').'" /></a>'."\n";
			
			if(isset($waiting[$id_course])) {
			
				$tb_content[] = '<a class="course_waiting_user" href="index.php?modname=public_subscribe_admin&amp;op=waitinguser&amp;id_course='.$id_course.'&amp;ini_hidden='.$ini.'"'
					.' title="'.$lang->def('_USERWAITING').'">'
					.'<strong>'.$waiting[$id_course].'</strong>'
					.'</a>';
			} else {
				$tb_content[] = '';
			}
		}
	
		// subscribe operatio -----------------------------------------------------------
		if($can_subscribe) {
			require_once($GLOBALS['where_framework'].'/lib/lib.preference.php');
			$pref = new UserPreferences(getLogUserId());
			
			$tb_content[] = ($course_edition==0 || !(!$pref->getPreference('admin_rules.max_course_subscribe') && $pref->getPreference('admin_rules.limit_course_subscribe') == 'on')) ? '
				<a href="index.php?modname=public_subscribe_admin&amp;load=1&amp;op=subscribeadd&amp;id_course='.$id_course.'&amp;ini_hidden='.$ini.'"'
					.' title="'.$lang->def('_ADD_SUBSCRIBE').'">'
				.'<img src="'.getPathImage().'subscribe/add_subscribe.gif" alt="'.$lang->def('_ALT_ADD_SUSCRIBE').'" />'
				.'</a>' : '';
			$tb_content[] = ($course_edition==0) ? '
				<a href="index.php?modname=public_subscribe_admin&amp;op=subscribemod&amp;id_course='.$id_course.'&amp;ini_hidden='.$ini.'"'
					.' title="'.$lang->def('_MOD_SUBSCRIBE').'">'
				.'<img src="'.getPathImage().'subscribe/mod_subscribe.gif" alt="'.$lang->def('_ALT_MOD_SUSCRIBE').'" />'
				.'</a>' : '';
			$tb_content[] = ($course_edition==0) ? '
				<a href="index.php?modname=public_subscribe_admin&amp;op=subscribedel&amp;id_course='.$id_course.'&amp;ini_hidden='.$ini.'"'
					.' title="'.$lang->def('_DEL_SUBSCRIBE').'">'
				.'<img src="'.getPathImage().'subscribe/del_subscribe.gif" alt="'.$lang->def('_DEL').'" />'
				.'</a>' : '';
		}

		// course subscribe management action -------------------------------------------
		if($can_mod) {
			
			$tb_content[] = '<input type="image" class="button_image" '
				.'id="assign_menu_course_'.$id_course.'" '
				.'name="assign_menu_course['.$id_course.']" '
				.'alt="'.$lang->def('_ALT_ASSIGN_MENU').'" '
				.'title="'.$lang->def('_ASSIGN_MENU').'" '
				.'src="'.getPathImage().'course/assign_menu.gif" />';
			
			$tb_content[] = '<input type="image" class="button_image" '
				.'id="move_course_'.$id_course.'" '
				.'name="move_course['.$id_course.']" '
				.'alt="'.$lang->def('_ALT_MOVE').'" '
				.'title="'.$lang->def('_COURSE_MOVE').'" '
				.'src="'.getPathImage().'standard/move.gif" />';
			
			$tb_content[] = '<input type="image" class="button_image" '
				.'id="mod_course_'.$id_course.'" '
				.'name="mod_course['.$id_course.']" '
				.'alt="'.$lang->def('_MOD').'" '
				.'title="'.$lang->def('_COURSE_MODIFY').'" '
				.'src="'.getPathImage().'standard/mod.gif" />';
		}
		
		// delete option ----------------------------------------------------------------
		if($can_del) {
			
			$tb_content[] = '<input type="image" class="button_image" '
				.'id="del_course_'.$id_course.'" '
				.'name="del_course['.$id_course.']" '
				.'alt="'.$lang->def('_DEL').'" '
				.'title="'.$lang->def('_COURSE_DELETE').'" '
				.'src="'.getPathImage().'standard/rem.gif" />';
		}
		$tb->addBody($tb_content);
		
		// extra info if required -------------------------------------------------------
		if($course_info['extra'] != false) $tb->addBodyExpanded($course_info['extra'], 'course_more_info');

		// --------------------------------------------------------------------------------
		// -- Course editions -------------------------------------------------------------
		if(isset($editions[$id_course]) && is_array($editions[$id_course])) {
		
			$even = 0;
			foreach($editions[$id_course] as $edition) {
				
				addEditionRow($even++, $tb, $edition, $edition_users, $id_course, $sel_id_course, $can_subscribe, $can_moderate, $can_mod, $can_del, $ini);
			}
		}
		// --------------------------------------------------------------------------------
	}
	if($can_add) {
		
		$with_edition_arr = getCoursesWithEditionArr($flat, $id_category, $id_categories);
		$add_action = Form::getButton('new_course', 'new_course', $lang->def('_COURSE_NEW'), 'transparent_add_button');
		if (count($with_edition_arr) > 0) {
			
			$add_action .= Form::getButton('new_course_edition', 'new_course_edition', $lang->def('_COURSE_NEW_EDITION'), 'transparent_add_button');
			$add_action .= Form::getInputDropdown("dropdown_nowh", "course_id", "course_id", $with_edition_arr, '', '');
		}
		$tb->addActionAdd($add_action);
	}
	
	$GLOBALS['page']->add(''
		.$tb->getNavBar($ini, $tot_course)
		.$tb->getTable()
		.$tb->getNavBar($ini, $tot_course)
		.Form::getHidden('ini_hidden', 'ini_hidden', $ini)
		.Form::closeForm()
	);
	$GLOBALS['page']->add('</div>');
}

function maskModCourse(&$course, $new = false, $name_category = '') {
	
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	$out =& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$form 	= new Form();
	
	//addAjaxJs();
	addYahooJs();
	
	$lang 	=& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
	
	$levels = CourseLevel::getLevels();
	$array_lang = $GLOBALS['globLangManager']->getAllLangCode();
	
	//status of course -----------------------------------------------------
	$status = array(
		CST_PREPARATION => $lang->def('_CST_PREPARATION'), 
		CST_AVAILABLE 	=> $lang->def('_CST_AVAILABLE'), 
		CST_EFFECTIVE 	=> $lang->def('_CST_CONFIRMED'), 
		CST_CONCLUDED 	=> $lang->def('_CST_CONCLUDED'), 
		CST_CANCELLED 	=> $lang->def('_CST_CANCELLED') );
	//difficult ------------------------------------------------------------
	$difficult_lang = array(
		'veryeasy' 		=> $lang->def('_DIFFICULT_VERYEASY'),
		'easy' 			=> $lang->def('_DIFFICULT_EASY'),
		'medium' 		=> $lang->def('_DIFFICULT_MEDIUM'),
		'difficult' 	=> $lang->def('_DIFFICULT_DIFFICULT'),
		'verydifficult' => $lang->def('_DIFFICULT_VERYDIFFICULT'));
	//type of course -------------------------------------------------------
	$course_type= array (
		'elearning' 	=> $lang->def('_COURSE_TYPE_ELEARNING'),
		'blended' 		=> $lang->def('_COURSE_TYPE_BLENDED'),
		'classroom' 	=> $lang->def('_COURSE_TYPE_CLASSROOM'));
	// points policy -------------------------------------------------------
	$policy_point= array (
		'nopoints' 		=> $lang->def('_POLICY_POINT_NOPOINTS'),
		'sametoall' 	=> $lang->def('_POLICY_POINT_SAMETOALL'),
		'tofield' 		=> $lang->def('_POLICY_POINT_TOFIELD'));
	
	if($new == true) {
		
		// menu availables -----------------------------------------------------
		$menu_custom = getAllCustom();
		list($sel_custom) = current($menu_custom);
		reset($menu_custom);
	}
	
	$out->add(
		$form->openElementSpace()
	);
	
	if($new == true) $out->add($form->getLineBox($lang->def('_CATEGORY_SELECTED'), $name_category));
	
	$out->add(
		$form->getTextfield($lang->def('_CODE'), 		'course_code', 		'course_code', 		'50', 	$course['code'])
		.$form->getTextfield($lang->def('_COURSE_NAME'), 		'course_name', 		'course_name', 		'255', 	$course['name'])
		
		.$form->getDropdown($lang->def('_COURSE_LANG_METHOD'), 	'course_lang', 		'course_lang', 		$array_lang, 		array_search($course['lang_code'], $array_lang) )
		.$form->getDropdown($lang->def('_COURSE_DIFFICULT'), 	'course_difficult', 'course_difficult', $difficult_lang, 	$course['difficult'] )
		.$form->getDropdown($lang->def('_COURSE_TYPE'), 		'course_type', 		'course_type', 		$course_type, 		$course['course_type'] )
		.$form->getDropdown($lang->def('_STATUS'), 		'course_status', 	'course_status', 	$status, 			$course['status'] )
		.$form->getCheckbox($lang->def('_COURSE_EDITION'), 		'course_edition_yes', 'course_edition', 1, $course['course_edition'] == 1 )
		
		.( $new == true 
			? $form->getDropdown($lang->def('_COURSE_MENU_TO_ASSIGN'), 'selected_menu', 'selected_menu', $menu_custom, $sel_custom )
			: '' )
		
		.$form->getTextarea($lang->def('_DESCRIPTION'), 'course_descr', 		'course_descr', 	$course['description'])
		/*
		.( ($course['course_edition'] == 1)
			? $form->getCheckbox($lang->def('_CASCADE_TO_EDITION'), 'cascade_on_edition', 'cascade_on_edition', 0 )
			: '' )
		*/
		.'<div class="align_center">'
			.str_replace('[down]', '<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_OTHER_OPTION').'" />',
				$lang->def('_COURSE_MORE_OPTION'))
		.'</div>'
	
		.( !$new 
			? $form->getCheckbox($lang->def('_CASCADE_MOD_ON_EDITION'), 'cascade_on_ed', 'cascade_on_ed', 1)
			: '' )
	
		.$form->closeElementSpace()
		
		.$form->openElementSpace()
		
		.$form->getOpenFieldset($lang->def('_COURSE_SUBSCRIPTION'))
		
		//-----------------------------------------------------------------
		.$form->getOpenCombo($lang->def('_USER_CAN_SUBSCRIBE'))
		.$form->getRadio($lang->def('_SUBSCRIPTION_CLOSED'), 		'subscription_closed', 	'can_subscribe', '0', $course['can_subscribe'] == 0 )
		.$form->getRadio($lang->def('_SUBSCRIPTION_OPEN'), 			'subscription_open', 	'can_subscribe', '1', $course['can_subscribe'] == 1 )
		.$form->getRadio($lang->def('_SUBSCRIPTION_IN_PERIOD').":", 'subscription_period', 	'can_subscribe', '2', $course['can_subscribe'] == 2 )
		.$form->getCloseCombo()
		
		.$form->getDatefield($lang->def('_SUBSCRIPTION_DATE_BEGIN').":", 	'sub_start_date', 	'sub_start_date', 	$course['sub_start_date'] )
		.$form->getDatefield($lang->def('_SUBSCRIPTION_DATE_END').":", 		'sub_end_date', 	'sub_end_date', 	$course['sub_end_date'] )
		.$form->getCloseFieldset()

		//-display-mode----------------------------------------------------
		
		.$form->getOpenFieldset($lang->def('_COURSE_DISPLAY_MODE'))
				
		//-where-show-course----------------------------------------------
		.$form->getOpenCombo($lang->def('_WHERE_SHOW_COURSE'))
		.$form->getRadio($lang->def('_SC_EVERYWHERE'), 			'course_show_rules_every', 			'course_show_rules', '0', $course['show_rules'] == 0 )
		.$form->getRadio($lang->def('_SC_ONLY_IN'), 			'course_show_rules_only_in', 		'course_show_rules', '1', $course['show_rules'] == 1 )
		.$form->getRadio($lang->def('_SC_ONLYINSC_USER'), 		'course_show_rules_onlyinsc_user', 	'course_show_rules', '2', $course['show_rules'] == 2 )
		.$form->getCloseCombo()

		//-what-show------------------------------------------------------
		.$form->getOpenCombo($lang->def('_WHAT_SHOW'))
		.$form->getCheckbox($lang->def('_SHOW_PROGRESS'), 		'course_progress', 	'course_progress', 	'1', $course['show_progress'] == 1 )
		.$form->getCheckbox($lang->def('_SHOW_TIME'), 			'course_time', 		'course_time', 		'1', $course['show_time'] == 1 )
		.$form->getCheckbox($lang->def('_SHOW_ADVANCED_INFO'), 	'course_advanced', 	'course_advanced', 	'1', $course['show_extra_info'] == 1 )
		.$form->getCloseCombo()

		//-list-of-user---------------------------------------------------
		.$form->getOpenCombo($lang->def('_SHOW_USER_OF_LEVEL')));
	while(list($level, $level_name) = each($levels)) {

		$out->add($form->getCheckbox($level_name, 'course_show_level_'.$level, 'course_show_level['.$level.']', $level, $course['level_show_user'] & (1 << $level) ));
	}
	$out->add(
		$form->getCloseCombo()

		.$form->getCloseFieldset()

		//-user-interaction--------------------------------------------------
		
		.$form->getOpenFieldset($lang->def('_USER_INTERACTION_OPTION'))

		//-subscribe-method-----------------------------------------------
		.$form->getOpenCombo($lang->def('_COURSE_SUBSRIBE'))
		.$form->getRadio($lang->def('_COURSE_S_GODADMIN'), 		'course_subs_godadmin', 'course_subs', 	'0', 	$course['subscribe_method'] == 0 )
		.$form->getRadio($lang->def('_COURSE_S_MODERATE'), 		'course_subs_moderate', 'course_subs', 	'1', 	$course['subscribe_method'] == 1 )
		.$form->getRadio($lang->def('_COURSE_S_FREE'), 			'course_subs_free', 	'course_subs', 	'2', 	$course['subscribe_method'] == 2 )
		.$form->getCloseCombo()
		
		
		.$form->getCheckbox($lang->def('_COURSE_SELL'), 		'course_sell', 			'course_sell', 	'1', 	$course['selling'] == 1 )
		.$form->getTextfield($lang->def('_COURSE_PRIZE'), 		'course_prize', 		'course_prize', '11', 	$course['prize'])
		.$form->getTextfield($lang->def('_COURSE_ADVANCE'), 	'advance', 				'advance', 		'11', 	$course['advance'])
		
		// mode for course end--------------------------------------------
		.$form->getOpenCombo($lang->def('_COURSE_END_MODE'))
		.$form->getRadio($lang->def('_COURSE_EM_TEACHER'), 		'course_em_manual', 	'course_em', 	'1', 	$course['permCloseLO'] == 1 )
		.$form->getRadio($lang->def('_COURSE_EM_LO'), 			'course_em_lo', 		'course_em', 	'0', 	$course['permCloseLO'] == 0 )
		.$form->getCloseCombo()

		//status that can enter------------------------------------------
		.$form->getOpenCombo($lang->def('_COURSE_STATUS_CANNOT_ENTER'))
		.$form->getCheckbox($lang->def('_USER_STATUS_SUBS'), 	'user_status_'._CUS_SUBSCRIBED, 'user_status['._CUS_SUBSCRIBED.']', _CUS_SUBSCRIBED, 
			$course['userStatusOp'] & (1 << _CUS_SUBSCRIBED))
		.$form->getCheckbox($lang->def('_USER_STATUS_BEGIN'), 	'user_status_'._CUS_BEGIN, 		'user_status['._CUS_BEGIN.']', 		_CUS_BEGIN, 
			$course['userStatusOp'] & (1 << _CUS_BEGIN))
		.$form->getCheckbox($lang->def('_USER_STATUS_END'), 	'user_status_'._CUS_END, 		'user_status['._CUS_END.']', 		_CUS_END,
			$course['userStatusOp'] & (1 << _CUS_END))
		.$form->getCheckbox($lang->def('_USER_STATUS_SUSPEND'), 'user_status_'._CUS_SUSPEND, 	'user_status['._CUS_SUSPEND.']',	 _CUS_SUSPEND,
			$course['userStatusOp'] & (1 << _CUS_SUSPEND) )
		.$form->getCloseCombo()

		.$form->getCloseFieldset());

	//-expiration---------------------------------------------------------
	$hours = array('-1' => '- -', '0' =>'00', '01', '02', '03', '04', '05', '06', '07', '08', '09', 
					'10', '11', '12', '13', '14', '15', '16', '17', '18', '19', 
					'20', '21', '22', '23' );
	$quarter = array('-1' => '- -', '00' => '00', '15' => '15', '30' => '30', '45' => '45');
	
	if($course['hour_begin'] != '-1') {
		$hb_sel = (int)substr($course['hour_begin'], 0, 2);
		$qb_sel = substr($course['hour_begin'], 3, 2); 
	} else $hb_sel = $qb_sel = '-1';
	
	if($course['hour_end'] != '-1') {
		$he_sel = (int)substr($course['hour_end'], 0, 2);
		$qe_sel = substr($course['hour_end'], 3, 2); 
	} else $he_sel = $qe_sel = '-1';
	
	$out->add(
		'<script type="text/javascript">'."\n".
		'var $=YAHOO.util.Dom.get;
    alert(cal_course_date_begin);
		cal_course_date_begin.onUpdate = function() {
			var new_date = $("course_date_begin").value;
			$("course_date_end").value = new_date;
		}'."\n".
    '</script>'
	, 'footer');
	
	$out->add(
		$form->getOpenFieldset($lang->def('_COURSE_TIME_OPTION'))
		.$form->getDatefield($lang->def('_DATE_BEGIN'), 		'course_date_begin', 	'course_date_begin', 	$course['date_begin'] )
		.$form->getDatefield($lang->def('_DATE_END'), 			'course_date_end', 		'course_date_end', 		$course['date_end'] )
		
		.$form->getLineBox(
			'<label for="hour_begin_hour">'.$lang->def('_HOUR_BEGIN').'</label>',
			$form->getInputDropdown('dropdown_nw', 'hour_begin_hour', 'hour_begin[hour]', $hours, $hb_sel, '')
			.' : '
			.$form->getInputDropdown('dropdown_nw', 'hour_begin_quarter', 'hour_begin[quarter]', $quarter, $qe_sel, '')
		)
		
		.$form->getLineBox(
			'<label for="hour_end_hour">'.$lang->def('_HOUR_END').'</label>',
			$form->getInputDropdown('dropdown_nw', 'hour_end_hour', 'hour_end[hour]', $hours, $he_sel, '')
			.' : '
			.$form->getInputDropdown('dropdown_nw', 'hour_end_quarter', 'hour_end[quarter]', $quarter, $qe_sel, '')
		)
		
		.$form->getTextfield($lang->def('_DAY_OF_VALIDITY'), 	'course_day_of', 		'course_day_of', 		'10', $course['valid_time'])
		.$form->getTextfield($lang->def('_MEDIUM_TIME'), 		'course_medium_time', 	'course_medium_time', 	'10', $course['mediumTime'])
		.$form->getCloseFieldset());

	//sponsor-and-logo----------------------------------------------------
	$out->add(
		$form->getOpenFieldset($lang->def('_COURSE_SPECIAL_OPTION'))
		
		.$form->getDropdown($lang->def('_POLICY_POINT'), 		'policy_point', 		'policy_point', 		$policy_point, 
			$course['policy_point'] )
		
		.$form->getTextfield($lang->def('_MIN_NUM_SUBSCRIBE'), 	'min_num_subscribe', 	'min_num_subscribe', 	'11', 
			$course['min_num_subscribe'])
		.$form->getTextfield($lang->def('_MAX_NUM_SUBSCRIBE'), 	'max_num_subscribe', 	'max_num_subscribe', 	'11', 
			$course['max_num_subscribe'])
		.$form->getCheckbox($lang->def('_ALLOW_OVERBOOKING'), 	'allow_overbooking', 	'allow_overbooking', 	'1', 
			$course['allow_overbooking'] == 1)
		.$form->getTextfield($lang->def('_COURSE_QUOTA'), 		'course_quota', 		'course_quota', 		'11', 
			($course['course_quota'] != COURSE_QUOTA_INHERIT ? $course['course_quota'] : 0))
		.$form->getCheckbox($lang->def('_INHERIT_QUOTA'), 		'inherit_quota', 		'inherit_quota', 		'1', 
			$course['course_quota'] == COURSE_QUOTA_INHERIT)
		
		.$form->getCloseFieldset()
		
		.$form->getOpenFieldset($lang->def('_DOCUMENT_UPLOAD'))
	);
	
	if($new == true) {
		
		$out->add(
			$form->getFilefield($lang->def('_USER_MATERIAL'), 'course_user_material', 'course_user_material')
			.$form->getFilefield($lang->def('_OTHER_USER_MATERIAL'), 'course_otheruser_material', 'course_otheruser_material')
			
			.$form->getTextfield($lang->def('_SPONSOR_LINK'), 'course_sponsor_link', 'course_sponsor_link', '255', $course['linkSponsor'])
			
			.$form->getFilefield($lang->def('_SPONSOR_LOGO'), 'course_sponsor_logo', 'course_sponsor_logo')
			.$form->getFilefield($lang->def('_COURSE_LOGO'), 'course_logo', 'course_logo')
			.$form->getFilefield($lang->def('_COURSE_DEMO'), 'course_demo', 'course_demo')
		);
	} else { 
		
		$out->add(
			$form->getExtendedFilefield($lang->def('_USER_MATERIAL'), 'course_user_material', 'course_user_material', $course["img_material"])
			.$form->getExtendedFilefield($lang->def('_OTHER_USER_MATERIAL'),'course_otheruser_material', 'course_otheruser_material', $course["img_othermaterial"])
			
			.$form->getTextfield($lang->def('_SPONSOR_LINK'), 'course_sponsor_link', 'course_sponsor_link', '255', $course['linkSponsor'])
			
			.$form->getExtendedFilefield($lang->def('_SPONSOR_LOGO'),'course_sponsor_logo', 'course_sponsor_logo', $course["imgSponsor"])
			.$form->getExtendedFilefield($lang->def('_COURSE_LOGO'),'course_logo', 'course_logo', $course["img_course"])
			.$form->getExtendedFilefield($lang->def('_COURSE_DEMO'),'course_demo', 'course_demo', $course["course_demo"])
		);
	}
	$out->add(
		$form->getCloseFieldset()
		.$form->closeElementSpace()
	);
}

function addCourse() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

	require_once($GLOBALS['where_lms'].'/admin/modules/category/category.php');
	require_once($GLOBALS['where_lms'].'/admin/modules/category/tree.category.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');

	$form 	= new Form();
	$lang 	=& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
	
	// tree for categories ------------------------------------------------ 
	$categoryDb 	= new TreeDb_CatDb($GLOBALS['prefix_lms'].'_category');
	$treeView 		= new TreeView_CatView($categoryDb, 'course_category', $lang->def('_COURSE_CATEGORY'));
	$treeView->parsePositionData($_POST, $_POST, $_POST);
	$id_category 	= $treeView->getSelectedFolderId();
	$name_category 	= $treeView->getFolderPrintName($categoryDb->getFolderById($id_category));
	// -------------------------------------------------------------------
	
	$course = array( 
		'code' 				=> '',
		'name' 				=> '',
		'lang_code' 		=> getLanguage(),
		'difficult' 		=> 'medium',
		'course_type' 		=> 'elearning',
		'status' 			=> CST_AVAILABLE,
		'course_edition' 	=> 0,
		'description' 		=> '',
		'can_subscribe' 	=> 1,
		'sub_start_date' 	=> '',
		'sub_end_date' 		=> '',
		'show_rules' 		=> 0,
		'show_progress' 	=> 1,
		'show_time' 		=> 1,
		'show_extra_info' 	=> 0,
		'level_show_user' 	=> 0,
		'subscribe_method' 	=> 0,
		'selling' 			=> 0,
		'prize' 			=> '',
		'advance' 			=> '',
		'permCloseLO' 		=> 1,
		'userStatusOp' 		=> (1 << _CUS_SUSPEND),
		
		'date_begin' 		=> '',
		'date_end' 			=> '',
		'hour_begin' 		=> '-1',
		'hour_end' 			=> '-1',
		
		'valid_time' 		=> '0',
		'mediumTime' 		=> '0',
		'policy_point' 		=> 'nopoints',
		'min_num_subscribe' => '0',
		'max_num_subscribe' => '0',
		'allow_overbooking' => '',
		'course_quota' 		=> '',
		
		'linkSponsor' 		=> 'http://'
	);
	
	// -------------------------------------------------------------------
	
	$title_area = array(
		'index.php?modname=public_course_admin&amp;op=course_list' => $lang->def('_COURSE'),
		$lang->def('_ADD_COURSE')
	);
	
	$GLOBALS['page']->add(
	
		getTitleArea($title_area, 'course')
		.'<div class="std_block">'
		.getBackUi( 'index.php?modname=public_course_admin&amp;op=course_list', $lang->def('_BACK') )

		.$form->getFormHeader($lang->def('_COURSE_NEW'))
		.$form->openForm('course_creation', 'index.php?modname=public_course_admin&amp;op=add_course', false, false, 'multipart/form-data')
		
		.$form->getHidden('idCategory', 'idCategory', $id_category)

	, 'content');
	
	maskModCourse($course, true, $name_category);
	
	$GLOBALS['page']->add(
		$form->openButtonSpace()
		.$form->getButton('course_create', 'course_create', $lang->def('_CREATE'))
		.$form->getButton('course_undo', 'course_undo', $lang->def('_UNDO'))
		.$form->closeButtonSpace()
		
		.$form->closeForm()
		.'</div>'
	, 'content');
}

function insCourse() {

	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.multimedia.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');

	$array_lang	 	= $GLOBALS['globLangManager']->getAllLangCode();
	$acl_man 		=& $GLOBALS['current_user']->getAclManager();

	$id_custom = importVar('selected_menu');

	// calc quota limit
	$quota = $_POST['course_quota'];
	if(isset($_POST['inherit_quota'])) {
		$quota = $GLOBALS['lms']['course_quota'];
		$_POST['course_quota'] = COURSE_QUOTA_INHERIT;
	}
	$quota = $quota * 1024 * 1024;

	$path = $GLOBALS['lms']['pathcourse'];
	$path = '/doceboLms/'.$GLOBALS['lms']['pathcourse'].( substr($path, -1) != '/' && substr($path, -1) != '\\' ? '/' : '');

	if($_POST['course_name'] == '') $_POST['course_name'] = def('_NO_NAME', 'admin_course_managment', 'lms');

	// restriction on course status ------------------------------------------
	$user_status = 0;
	if(isset($_POST['user_status'])) {
		while(list($status) = each($_POST['user_status'])) $user_status |= (1 << $status);
	}

	// level that will be showed in the course --------------------------------
	$show_level = 0;
	if(isset($_POST['course_show_level'])) {
		while(list($lv) = each($_POST['course_show_level'])) $show_level |= (1 << $lv);
	}

	// save the file uploaded -------------------------------------------------
	$file_sponsor 		= '';
	$file_logo 			= '';
	$file_material 		= '';
	$file_othermaterial = '';
	$file_demo 			= '';

	$error 				= false;
	$quota_exceeded 	= false;
	$total_file_size 	= 0;

	if(is_array($_FILES) && !empty($_FILES)) sl_open_fileoperations();
	// load user material ---------------------------------------------------------------------------
	$arr_file = manageCourseFile(	'course_user_material',
									'',
									$path,
									($quota != 0 ? $quota - $total_file_size : false),
									false );
	$error 				|= $arr_file['error'];
	$quota_exceeded 	|= $arr_file['quota_exceeded'];
	$file_material		= $arr_file['filename'];
	$total_file_size 	= $total_file_size + $arr_file['new_size'];

	// course otheruser material -------------------------------------------------------------------
	$arr_file = manageCourseFile(	'course_otheruser_material',
									'',
									$path,
									($quota != 0 ? $quota - $total_file_size : false),
									false );
	$error 				|= $arr_file['error'];
	$quota_exceeded 	|= $arr_file['quota_exceeded'];
	$file_othermaterial	= $arr_file['filename'];
	$total_file_size 	= $total_file_size + $arr_file['new_size'];

	// course demo-----------------------------------------------------------------------------------
	$arr_file = manageCourseFile(	'course_demo',
									'',
									$path,
									($quota != 0 ? $quota - $total_file_size : false),
									false );
	$error 				|= $arr_file['error'];
	$quota_exceeded 	|= $arr_file['quota_exceeded'];
	$file_demo			= $arr_file['filename'];
	$total_file_size 	= $total_file_size + $arr_file['new_size'];

	// course sponsor---------------------------------------------------------------------------------
	$arr_file = manageCourseFile(	'course_sponsor_logo',
									'',
									$path,
									($quota != 0 ? $quota - $total_file_size : false),
									false,
									true );
	$error 				|= $arr_file['error'];
	$quota_exceeded 	|= $arr_file['quota_exceeded'];
	$file_sponsor		= $arr_file['filename'];
	$total_file_size 	= $total_file_size + $arr_file['new_size'];

	// course logo-----------------------------------------------------------------------------------
	$arr_file = manageCourseFile(	'course_logo',
									'',
									$path,
									($quota != 0 ? $quota - $total_file_size : false),
									false,
									true );
	$error 				|= $arr_file['error'];
	$quota_exceeded 	|= $arr_file['quota_exceeded'];
	$file_logo			= $arr_file['filename'];
	$total_file_size 	= $total_file_size + $arr_file['new_size'];

	// ----------------------------------------------------------------------------------------------
	sl_close_fileoperations();


	if ($_POST["can_subscribe"] == "2")  {
		$sub_start_date = $GLOBALS["regset"]->regionalToDatabase($_POST["sub_start_date"], "date");
		$sub_end_date 	= $GLOBALS["regset"]->regionalToDatabase($_POST["sub_end_date"], "date");
	}
	
	$date_begin	= $GLOBALS["regset"]->regionalToDatabase($_POST['course_date_begin'], "date");
	$date_end 	= $GLOBALS["regset"]->regionalToDatabase($_POST['course_date_end'], "date");
	
	// insert the course in database -----------------------------------------------------------
	$hour_begin = '-1';
	$hour_end = '-1';
	if($_POST['hour_begin']['hour'] != '-1') {
		
		$hour_begin = ( strlen($_POST['hour_begin']['hour']) == 1 ? '0'.$_POST['hour_begin']['hour'] : $_POST['hour_begin']['hour'] );
		if($_POST['hour_begin']['quarter'] == '-1') $hour_begin .= ':00';
		else $hour_begin .= ':'.$_POST['hour_begin']['quarter'];
	}
	
	if($_POST['hour_end']['hour'] != '-1') {
		
		$hour_end = ( strlen($_POST['hour_end']['hour']) == 1 ? '0'.$_POST['hour_end']['hour'] : $_POST['hour_end']['hour'] );
		if($_POST['hour_end']['quarter'] == '-1') $hour_end .= ':00';
		else $hour_end .= ':'.$_POST['hour_end']['quarter'];
	}
	
	$query_course = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_course
	SET idCategory 			= '".( isset($_POST['idCategory']) ? $_POST['idCategory'] : 0 )."',
		code 				= '".$_POST['course_code']."',
		name 				= '".$_POST['course_name']."',
		description 		= '".$_POST['course_descr']."',
		lang_code 			= '".$array_lang[$_POST['course_lang']]."',
		status 				= '".(int)$_POST['course_status']."',
		level_show_user 	= '".$show_level."',
		subscribe_method 	= '".(int)$_POST['course_subs']."',
		
		create_date			= '".date("Y-m-d H:i:s")."',
		
		linkSponsor 		= '".$_POST['course_sponsor_link']."',
		imgSponsor 			= '".$file_sponsor."',
		img_course 			= '".$file_logo."',
		img_material 		= '".$file_material."',
		img_othermaterial 	= '".$file_othermaterial."',
		course_demo 		= '".$file_demo."',

		mediumTime 			= '".$_POST['course_medium_time']."',
		permCloseLO 		= '".$_POST['course_em']."',
		userStatusOp 		= '".$user_status."',
		difficult 			= '".$_POST['course_difficult']."',

		show_progress 		= '".( isset($_POST['course_progress']) ? 1 : 0 )."',
		show_time 			= '".( isset($_POST['course_time']) ? 1 : 0 )."',
		show_extra_info 	= '".( isset($_POST['course_advanced']) ? 1 : 0 )."',
		show_rules 			= '".(int)$_POST['course_show_rules']."',

		date_begin 			= '".$date_begin."',
		date_end 			= '".$date_end."',
		hour_begin 			= '".$hour_begin."',
		hour_end 			= '".$hour_end."',

		valid_time 			= '".(int)$_POST['course_day_of']."',
		
		min_num_subscribe 	= '".(int)$_POST['min_num_subscribe']."',
		max_num_subscribe 	= '".(int)$_POST['max_num_subscribe']."',
		selling 			= '".( isset($_POST['course_sell']) ? '1' : '0' )."',
		prize 				= '".$_POST['course_prize']."',

		course_type 		= '".$_POST['course_type']."',
		policy_point 		= '".$_POST['policy_point']."',
		course_edition 		= '".( isset($_POST['course_edition']) ? 1 : 0) ."',

		course_quota 		= '".$_POST['course_quota']."',
		used_space			= '".$total_file_size."',
		allow_overbooking 	= '".( isset($_POST["allow_overbooking"]) ? 1 : 0 )."',
		can_subscribe 		= '".(int)$_POST["can_subscribe"]."',
		sub_start_date 		= ".( $_POST["can_subscribe"] == '2' ? "'".$sub_start_date."'" : 'NULL' ).",
		sub_end_date 		= ".( $_POST["can_subscribe"] == '2' ? "'".$sub_end_date."'" : 'NULL' ).",

		advance 			= '".$_POST['advance']."'";

	if(!mysql_query($query_course)) {
		
		// course save failed, delete uploaded file
		
		if($file_sponsor != '') 	sl_unlink($path.$file_sponsor);
		if($file_logo != '') 		sl_unlink($path.$file_logo);
		if($file_material != '') 	sl_unlink($path.$file_material);
		if($file_othermaterial != '') sl_unlink($path.$file_othermaterial);
		if($file_demo != '') 		sl_unlink($path.$file_demo);

		jumpTo('index.php?modname=public_course_admin&op=course_list&result=err_course');
	}

	// recover the id of the course inserted --------------------------------------------
	list($id_course) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));

	// add this corse to the pool of course visible by the user that have create it -----
	if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {

		$re &= mysql_query("
		INSERT INTO ".$GLOBALS['prefix_fw']."_admin_course
		( id_entry, type_of_entry, idst_user ) VALUES
		( '".$id_course."', 'course', '".getLogUserId()."') ");
	}

	//if the scs exist create a room ----------------------------------------------------
	if($GLOBALS['where_scs'] !== false) {

		require_once($GLOBALS['where_scs'].'/lib/lib.room.php');

		$rules = array(
					'room_name' => $_POST['course_name'],
					'room_type' => 'course',
					'id_source' => $id_course );
		$admin_rules = getAdminRules();
		$rules = array_merge($rules, $admin_rules);
		$re = insertRoom($rules);
	}
	$course_idst =& createCourseLevel($id_course);

	// create the course menu -----------------------------------------------------------
	if(!cerateCourseMenuFromCustom($id_custom, $id_course, $course_idst)) {

		jumpTo('index.php?modname=public_course_admin&op=course_list&result=err_coursemenu');
	}
	/*
	// create the first edition if required ---------------------------------------------
	if(isset($_POST['course_edition']) && $_POST['course_edition'] == 1) {

		$query_course_edition = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_course_edition
		SET idCourse 			= '".$id_course."',
			code 				= '".$_POST['course_code']."',
			name 				= '".$_POST['course_name']."',
			description 		= '".$_POST['course_descr']."',
			status 				= '".(int)$_POST['course_status']."',
			edition_type 		= '".$_POST['course_type']."',

			img_material 		= '".$file_material."',
			img_othermaterial 	= '".$file_othermaterial."',

			date_begin 			= '".$date_begin."',
			date_end 			= '".$date_end."',
			hour_begin 			= '".$hour_begin."',
			hour_end 			= '".$hour_end."',

			min_num_subscribe 	= '".(int)$_POST['min_num_subscribe']."',
			max_num_subscribe 	= '".(int)$_POST['max_num_subscribe']."',

			price 				= '".$_POST['course_prize']."',
			advance 			= '".$_POST['advance']."'";
		
		if(!mysql_query($query_course_edition)) jumpTo('index.php?modname=public_course_admin&op=course_list&result=err_courseedition');
		
		$acl_manager =& $GLOBALS["current_user"]->getAclManager();
		$edition_id = mysql_insert_id();
		
		$group = '/lms/course_edition/'.$edition_id.'/subscribed';
		$group_idst = $acl_manager->registerGroup($group, 'all the user of a course edition', true, "course");
	}
	*/
	
	/*
	// send alert -------------------------------------------------------------------------------
	require_once($GLOBALS['where_framework'] . '/lib/lib.eventmanager.php');

	$msg_composer = new EventMessageComposer('admin_course_management', 'lms');

	$msg_composer->setSubjectLangText('email', '_ALERT_SUBJECT', false);
	$msg_composer->setBodyLangText('email', '_ALERT_TEXT', array(	'[url]' => $GLOBALS['lms']['url'],
																	'[course_code]' => $_POST['course_code'],
																	'[course]' => $_POST['course_name'] ) );

	$msg_composer->setSubjectLangText('sms', '_ALERT_SUBJECT_SMS', false);
	$msg_composer->setBodyLangText('sms', '_ALERT_TEXT_SMS', array(	'[url]' => $GLOBALS['lms']['url'],
																	'[course_code]' => $_POST['course_code'],
																	'[course]' => $_POST['course_name'] ) );
	
	require_once($GLOBALS['where_lms'] . '/lib/lib.course.php');
	$course_man = new Man_Course();
	$recipients = $course_man->getIdUserOfLevel($id_course);
	createNewAlert(	'CoursePropModified',
					'course',
					'add',
					'1',
					'Inserted course '.$_POST['course_name'],
					$recipients,
					$msg_composer );
	*/
	jumpTo('index.php?modname=public_course_admin&op=course_list&result='.( $error ? 'err_course' : 'ok_course' ).( $quota_exceeded ? '&limit_reach=1' : '' ));
}

function modCourse() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.tab.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

	require_once($GLOBALS['where_lms'].'/admin/modules/category/category.php');
	require_once($GLOBALS['where_lms'].'/admin/modules/category/tree.category.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');

	$form 	= new Form();
	$lang 	=& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
	$out 	=& $GLOBALS['page'];

	$levels = CourseLevel::getLevels();
	$array_lang = $GLOBALS['globLangManager']->getAllLangCode();
	
	$form 	= new Form();
	$out->setWorkingZone('content');
	if (isset($_POST['mod_course'])) {
		list($id_course) = each($_POST['mod_course']);
	} else {
		list($id_course) = $_GET['mod_course'];
	}
	
	// load from post the setting for the selected tab
	// retrive course info
	$query_course = "
	SELECT code, name, description, lang_code, status, level_show_user, subscribe_method,
		linkSponsor, mediumTime, permCloseLO, userStatusOp, difficult,
		show_progress, show_time, show_extra_info, show_rules, date_begin, date_end, hour_begin, hour_end, sub_start_date, sub_end_date, valid_time,
		min_num_subscribe, max_num_subscribe, max_sms_budget,selling,prize,course_type,policy_point,point_to_all,course_edition,
		imgSponsor, img_course, img_material, img_othermaterial, course_demo, course_quota, allow_overbooking,
		can_subscribe, advance
	FROM ".$GLOBALS['prefix_lms']."_course
	WHERE idCourse = '".$id_course."'";
	
	$course = mysql_fetch_assoc(mysql_query($query_course));
	
	$course['date_begin'] 	= $GLOBALS['regset']->databaseToRegional($course['date_begin'], 'date');
	$course['date_end'] 	= $GLOBALS['regset']->databaseToRegional($course['date_end'], 'date');
	$course['sub_start_date'] = $GLOBALS['regset']->databaseToRegional($course['sub_start_date'], 'date');
	$course['sub_end_date'] = $GLOBALS['regset']->databaseToRegional($course['sub_end_date'], 'date');
	
	$array_lang = $GLOBALS['globLangManager']->getAllLangCode();
	$lang_code = array_search($course['lang_code'], $array_lang);
	
	// create tabs --------------------------------------------------------------------
	
	$tabs = new TabView('modcourse', 'index.php?modname=public_course_admin&amp;op=mod_course');
	$general_tab = new TabElemDefault('general', $lang->def('_MOD_COURSE_GENERAL'));
	$tabs->addTab($general_tab);
	
	if(isset($course['policy_point']) && ($course['policy_point'] != 'nopoints' )){
		$point_tab = new TabElemDefault('point', $lang->def('_MOD_COURSE_POINT'));
		$tabs->addTab($point_tab);
	}
	
	$active_tab = $tabs->getActiveTab();
	if($active_tab == false) $active_tab = 'general';
	$tabs->setActiveTab($active_tab);
	
	
	// set page title ------------------------------------------------------------------
	$title_area = array(
		'index.php?modname=public_course_admin&amp;op=course_list' => $lang->def('_COURSE'),
		$lang->def('_MOD_COURSE').' : '.$course['name']
	);
	switch($active_tab) {
		case "general" : 		$title_area[] = $lang->def("_MOD_COURSE_GENERAL");break;
		case "point" : 			$title_area[] = $lang->def("_SCORE");break;
	}
	
	// print opern form ----------------------------------------------------------------
	$out->add(
		getTitleArea($title_area, 'configuration')
		.'<div class="std_block">'
		.getBackUi( 'index.php?modname=public_course_admin&amp;op=course_list', $lang->def('_BACK') )
		
		.$form->openForm('upd_course', 'index.php?modname=public_course_admin&amp;op=upd_course', false, false, 'multipart/form-data')
		
		.$tabs->printTabView_Begin('',FALSE)
		
		.$form->getHidden('mod_course_'.$id_course, 'mod_course['.$id_course.']', $id_course)
	);
	switch($active_tab) {
		case "general" : {
			
			maskModCourse($course, false);
			
		};break;
		case "point" : {
			
			$out->add(
				// print course name hidden
				$form->getHidden('course_name', 'course_name', $course['name'])
				.$form->getHidden('course_id', 'course_id', $id_course)
				.$form->getHidden('course_code', 'course_code', $course['code'])
				
				.$form->getHidden("old_date_begin", "old_date_begin", $course['date_begin'])
				.$form->getHidden("old_date_end", "old_date_end", $course['date_end'])
				
				// print action for the tab selected and print as hidden the content of the other tab
				.$form->getOpenFieldset($lang->def('_COURSE_POINT_FIELD'))
			);
			if ($_POST['general_policy_point']=='tofield' ) {
				require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
				$fl=new FieldList();
				$all_fields=$fl->getAllFieldValue(39);
				foreach($all_fields as $key=>$val) {
					$point = (getPointCourse ($id_course,$key)==false) ? 0 : getPointCourse ($id_course,$key);

					$out->add($form->getTextfield($val, 'point_to_field', 'point_to_field['.$key.']', '255', $point));
				}
			}
			if ($_POST['general_policy_point']=='sametoall')	{
				$out->add($form->getTextfield($lang->def('_POINT_TO_ALL'), 'point_to_all', 'point_to_all', '5',
				$course['point_to_all']));
			}	$out->add($form->getCloseFieldset());
			
		};break;
	}
	// close tab
	
	$out->add(
		$form->openButtonSpace()
		.$form->getButton('upd_course', 'upd_course', $lang->def('_SAVE'))
		.$form->getButton('course_undo', 'course_undo', $lang->def('_UNDO'))

		.$form->closeButtonSpace()
		.$form->closeForm()
		.$tabs->printTabView_End()
		.'</div>'
	);
}

function courseUpdate() {

	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.multimedia.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');

	$array_lang	 	= $GLOBALS['globLangManager']->getAllLangCode();
	$acl_man 		=& $GLOBALS['current_user']->getAclManager();

	if (isset($_POST['mod_course'])) {
		list($id_course) = each($_POST['mod_course']);
	} else {
		list($id_course) = $_GET['mod_course'];
	}
	// calc quota limit
	$quota = $_POST['course_quota'];
	if(isset($_POST['inherit_quota'])) {
		$quota = $GLOBALS['lms']['course_quota'];
		$_POST['course_quota'] = COURSE_QUOTA_INHERIT;
	}
	$quota = $quota*1024*1024;

	$course_man = new DoceboCourse($id_course);
	$used = $course_man->getUsedSpace();

	if($_POST['course_name'] == '') $_POST['course_name'] = def('_NO_NAME', 'admin_course_managment', 'lms');

	// restriction on course status ------------------------------------------
	$user_status = 0;
	if(isset($_POST['user_status'])) {
		while(list($status) = each($_POST['user_status'])) $user_status |= (1 << $status);
	}

	// level that will be showed in the course --------------------------------
	$show_level = 0;
	if(isset($_POST['course_show_level'])) {
		while(list($lv) = each($_POST['course_show_level'])) $show_level |= (1 << $lv);
	}

	// save the file uploaded -------------------------------------------------

	$error 			= false;
	$quota_exceeded = false;
	$path 			= '/doceboLms/'.$GLOBALS['lms']['pathcourse'];
	$path 			.= $path.( substr($path, -1) != '/' && substr($path, -1) != '\\' ? '/' : '');
	$old_file_size 	= 0;
	if ((is_array($_FILES) && !empty($_FILES)) || (is_array($_POST["file_to_del"]))) sl_open_fileoperations();

	// load user material ---------------------------------------------------------------------------
	$arr_file = manageCourseFile(	'course_user_material',
									$_POST["old_course_user_material"],
									$path,
									($quota != 0 ? $quota - $used : false),
									isset($_POST['file_to_del']['course_user_material']) );
	$error 				|= $arr_file['error'];
	$quota_exceeded 	|= $arr_file['quota_exceeded'];
	$file_material		= $arr_file['filename'];
	$used 				= $used + ($arr_file['new_size'] - $arr_file['old_size']);
	$old_file_size 		+= $arr_file['old_size'];

	// course otheruser material -------------------------------------------------------------------
	$arr_file = manageCourseFile(	'course_otheruser_material',
									$_POST["old_course_otheruser_material"],
									$path,
									($quota != 0 ? $quota - $used : false),
									isset($_POST['file_to_del']['course_otheruser_material']) );
	$error 				|= $arr_file['error'];
	$quota_exceeded 	|= $arr_file['quota_exceeded'];
	$file_othermaterial	= $arr_file['filename'];
	$used 				= $used + ($arr_file['new_size'] - $arr_file['old_size']);
	$old_file_size 		+= $arr_file['old_size'];

	// course demo-----------------------------------------------------------------------------------
	$arr_file = manageCourseFile(	'course_demo',
									$_POST["old_course_demo"],
									$path,
									($quota != 0 ? $quota - $used : false),
									isset($_POST['file_to_del']['course_demo']) );
	$error 				|= $arr_file['error'];
	$quota_exceeded 	|= $arr_file['quota_exceeded'];
	$file_demo			= $arr_file['filename'];
	$used 				= $used + ($arr_file['new_size'] - $arr_file['old_size']);
	$old_file_size 		+= $arr_file['old_size'];
	// course sponsor---------------------------------------------------------------------------------
	$arr_file = manageCourseFile(	'course_sponsor_logo',
									$_POST["old_course_sponsor_logo"],
									$path,
									($quota != 0 ? $quota - $used : false),
									isset($_POST['file_to_del']['course_sponsor_logo']),
									true );
	$error 				|= $arr_file['error'];
	$quota_exceeded 	|= $arr_file['quota_exceeded'];
	$file_sponsor		= $arr_file['filename'];
	$used 				= $used + ($arr_file['new_size'] - $arr_file['old_size']);
	$old_file_size 		+= $arr_file['old_size'];
	// course logo-----------------------------------------------------------------------------------
	$arr_file = manageCourseFile(	'course_logo',
									$_POST["old_course_logo"],
									$path,
									($quota != 0 ? $quota - $used : false),
									isset($_POST['file_to_del']['course_logo']),
									true );
	$error 				|= $arr_file['error'];
	$quota_exceeded 	|= $arr_file['quota_exceeded'];
	$file_logo			= $arr_file['filename'];
	$used 				= $used + ($arr_file['new_size'] - $arr_file['old_size']);
	$old_file_size 		+= $arr_file['old_size'];
	// ----------------------------------------------------------------------------------------------
	sl_close_fileoperations();

	$date_begin	= $GLOBALS["regset"]->regionalToDatabase($_POST['course_date_begin'], "date");
	$date_end 	= $GLOBALS["regset"]->regionalToDatabase($_POST['course_date_end'], "date");
	
	if ($_POST["can_subscribe"] == "2") {
		$sub_start_date = $GLOBALS["regset"]->regionalToDatabase($_POST["sub_start_date"], "date")."'";
		$sub_end_date 	= $GLOBALS["regset"]->regionalToDatabase($_POST["sub_end_date"], "date")."'";
	}
	
	$hour_begin = '-1';
	$hour_end = '-1';
	if($_POST['hour_begin']['hour'] != '-1') {
		
		$hour_begin = ( strlen($_POST['hour_begin']['hour']) == 1 ? '0'.$_POST['hour_begin']['hour'] : $_POST['hour_begin']['hour'] );
		if($_POST['hour_begin']['quarter'] == '-1') $hour_begin .= ':00';
		else $hour_begin .= ':'.$_POST['hour_begin']['quarter'];
	}
	
	if($_POST['hour_end']['hour'] != '-1') {
		
		$hour_end = ( strlen($_POST['hour_end']['hour']) == 1 ? '0'.$_POST['hour_end']['hour'] : $_POST['hour_end']['hour'] );
		if($_POST['hour_end']['quarter'] == '-1') $hour_end .= ':00';
		else $hour_end .= ':'.$_POST['hour_end']['quarter'];
	}
	
	// update database ----------------------------------------------------
	$query_course = "
	UPDATE ".$GLOBALS['prefix_lms']."_course
	SET code 				= '".$_POST['course_code']."',
		name 				= '".$_POST['course_name']."',
		description 		= '".$_POST['course_descr']."',
		lang_code 			= '".$array_lang[$_POST['course_lang']]."',
		status 				= '".(int)$_POST['course_status']."',
		level_show_user 	= '".$show_level."',
		subscribe_method 	= '".(int)$_POST['can_subscribe']."',

		linkSponsor 		= '".$_POST['course_sponsor_link']."',
		
		imgSponsor 			= '".$file_sponsor."',
		img_course 			= '".$file_logo."',
		img_material 		= '".$file_material."',
		img_othermaterial 	= '".$file_othermaterial."',
		course_demo 		= '".$file_demo."',

		mediumTime 			= '".$_POST['course_medium_time']."',
		permCloseLO 		= '".$_POST['course_em']."',
		userStatusOp 		= '".$user_status."',
		difficult 			= '".$_POST['course_difficult']."',

		show_progress 		= '".( isset($_POST['course_progress']) ? 1 : 0 )."',
		show_time 			= '".( isset($_POST['course_time']) ? 1 : 0 )."',
		show_extra_info 	= '".( isset($_POST['course_advanced']) ? 1 : 0 )."',
		show_rules 			= '".(int)$_POST['course_show_rules']."',

		date_begin 			= '".$date_begin."',
		date_end 			= '".$date_end."',
		hour_begin 			= '".$hour_begin."',
		hour_end 			= '".$hour_end."',

		valid_time 			= '".(int)$_POST['course_day_of']."',

		min_num_subscribe 	= '".(int)$_POST['min_num_subscribe']."',
		max_num_subscribe 	= '".(int)$_POST['max_num_subscribe']."',

		course_type 		= '".$_POST['course_type']."',
		point_to_all 		= '".( isset($_POST['point_to_all']) ? $_POST['point_to_all'] : 0 )."',
		course_edition 		= '".( isset($_POST['course_edition']) ? $_POST['course_edition'] : 0 )."',
		selling 			= '".( isset($_POST['course_sell']) ? 1 : 0 )."',
		prize 				= '".( isset($_POST['course_prize']) ? $_POST['course_prize'] : 0 )."',
		policy_point 		= '".$_POST['policy_point']."',

		course_quota 		= '".$_POST['course_quota']."',

		allow_overbooking 	= '".( isset($_POST["allow_overbooking"]) ? 1 : 0 )."',
		can_subscribe 		= '".(int)$_POST["can_subscribe"]."',
		sub_start_date 		= ".( $_POST["can_subscribe"] == "2" ? "'".$sub_start_date."'" : 'NULL' ).",
		sub_end_date 		= ".( $_POST["can_subscribe"] == "2" ? "'".$sub_end_date."'" : 'NULL' ).",

		advance 			= '".$_POST['advance']."'

	WHERE idCourse = '".$id_course."'";
	
	if(!mysql_query($query_course)) {
		if($file_sponsor != '') 	sl_unlink($path.$file_sponsor);
		if($file_logo != '') 		sl_unlink($path.$file_logo);
		if($file_material != '') 	sl_unlink($path.$file_material);
		if($file_othermaterial != '') sl_unlink($path.$file_othermaterial);
		if($file_demo != '') 		sl_unlink($path.$file_demo);

		$course_man->subFileToUsedSpace(false, $old_file_size);
		jumpTo('index.php?modname=public_course_admin&amp;op=course_list&result=err_course');
	}
	
	// Let's update the classroom occupation schedule if course type is classroom -------
	if (hasClassroom($_POST["general_course_type"])) {
		$old_date_begin=$_POST["old_date_begin"];
		$old_date_end=$_POST["old_date_end"];
		updateCourseTimtable($id_course, FALSE, $date_begin, $date_end, $old_date_begin, $old_date_end);
	}
	
	// cascade modify on all the edition of thi course ---------------------------------
	if(isset($_POST['cascade_on_ed'])) {
		
		$query_editon = "
		UPDATE ".$GLOBALS['prefix_lms']."_course_edition 
		SET code 			= '".$_POST['course_code']."',
			name 			= '".$_POST['course_name']."',
			description 	= '".$_POST['course_descr']."',
			edition_type 	= '".$_POST['course_type']."',
			status 			= '".$_POST['course_status']."'
		WHERE idCourse = '".$id_course."'";
		mysql_query($query_editon);
	}
	jumpTo('index.php?modname=public_course_admin&amp;op=course_list&result=ok_course'.( $quota_exceeded ? '&limit_reach=1' : '' ));
}

function courseDelete() {

	if(isset($_POST['confirm_del_course'])) {

		$is_ok = removeCourse($_POST['id_course']);

		jumpTo('index.php?modname=public_course_admin&op=course_list&course_category_status='.importVar('course_category_status')
			.'&result='.( $is_ok ? 'ok_course' : 'err_course' ));
	} else {
		require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$lang 		=& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
		$out 		=& $GLOBALS['page'];

		list($id_course) = each($_POST['del_course']);
		$query_course = "
		SELECT code, name
		FROM ".$GLOBALS['prefix_lms']."_course
		WHERE idCourse = '".$id_course."'";
		list($code, $name) = mysql_fetch_row(mysql_query($query_course));

		$out->add(
			getTitleArea($lang->def('_COURSE'), 'course')
			.'<div class="std_block">'
			.Form::openForm('course_del', 'index.php?modname=public_course_admin&amp;op=del_course')
			.Form::getHidden('id_course', 'id_course', $id_course)
			.getDeleteUi(	$lang->def('_AREYOUSURE'),
							'<span class="text_bold">'.$lang->def('_CODE').' : </span>'.$code.'<br />'
							.'<span class="text_bold">'.$lang->def('_COURSE_NAME').' : </span>'.$name,
							false,
							'confirm_del_course['.$id_course.']',
							'course_undo')
			.Form::closeForm()
			.'</div>', 'content' );
	}
}

function removeCourse($id_course) {

	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');

	$acl_man	=& $GLOBALS['current_user']->getAclManager();
	$course_man = new Man_Course();

	/*
	//remove advice--------------------------------------------------
	$re_advice = mysql_query("SELECT idAdvice
	FROM ".$GLOBALS['prefix_lms']."_advice
	WHERE idCourse='".$idCourse."'" );
	while( list($id_a) = mysql_fetch_row($re_advice) ) {
		if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_adviceuser WHERE idAdvice='".$id_a."'")) return false;
	}
	mysql_free_result($re_advice);
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_advice WHERE idCourse='".$idCourse."'")) return false;

	//remove forum---------------------------------------------------
	$re_forum = mysql_query("
	SELECT idForum
	FROM ".$GLOBALS['prefix_lms']."_forum
	WHERE idCourse='".$idCourse."'" );
	while( list($id_f) = mysql_fetch_row($re_forum) ) {
		//thread
		if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_forumthread WHERE idForum='".$id_f."'")) return false;
		//access
		if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_forum_access WHERE idForum='".$id_f."'")) return false;
	}
	mysql_free_result($re_forum);
		//message
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_forummessage WHERE idCourse='".$idCourse."'")) return false;
	//sema
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_forum_sema WHERE idc='".$idCourse."'")) return false;
	//last access
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_forum_timing WHERE idCourse='".$idCourse."'")) return false;
	//forum
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_forum WHERE idCourse='".$idCourse."'")) return false;

	//remove groups--------------------------------------------------
	$re_group = mysql_query("
	SELECT idGroup
	FROM ".$GLOBALS['prefix_lms']."_coursegroup
	WHERE idCourse='".$idCourse."'" );
	while( list($id_g) = mysql_fetch_row($re_group) ) {

		if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_coursegroupuser WHERE idGroup='".$id_g."'")) return false;
	}
	mysql_free_result($re_group);
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_coursegroup WHERE idCourse='".$idCourse."'")) return false;

	//delete tree
	require_once( 'modules/organization/orglib.php' );
	$tree_course = new OrgDirDb( $idCourse );
	if( !$tree_course->deleteAllTree() ) return false;

	//remove inscription---------------------------------------------
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_courseuser WHERE idCourse = '$idCourse'")) return false;

	//remove menu----------------------------------------------------
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_menucourseunder_custom WHERE idCourse = '$idCourse'")) return false;
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_menucourse_category WHERE idCourse = '$idCourse'")) return false;

	//remove tracking------------------------------------------------
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_trackingeneral WHERE idCourse = '$idCourse'")) return false;
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_tracksession WHERE idCourse = '$idCourse'")) return false;

	//remove from path-------------------------------------------------
	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_course_path_course WHERE idCourse = '".$idCourse."'")) return false;
	*/

	//remove course subscribed------------------------------------------

	$levels =& $course_man->getCourseIdstGroupLevel($id_course);
	foreach($levels as $lv => $idst) {

		$acl_man->deleteGroup($idst);
	}
	$alluser = getIDGroupAlluser($id_course);
	$acl_man->deleteGroup($alluser);
	$course_man->removeCourseRole($id_course);
	$course_man->removeCourseMenu($id_course);

	if(!mysql_query("
	DELETE FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idCourse = '$id_course'")) return false;

	//remove course-----------------------------------------------------
	$query_course = "
	SELECT imgSponsor, img_course, img_material, img_othermaterial, course_demo
	FROM ".$GLOBALS['prefix_lms']."_course
	WHERE idCourse = '".$id_course."'";
	list($file_sponsor, $file_logo, $file_material, $file_othermaterial, $file_demo) = mysql_fetch_row(mysql_query($query_course));

	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	require_once($GLOBALS['where_lms'].'/setting.php');

	$path = '/doceboLms/'.$GLOBALS['lms']['pathcourse']
		.( substr($GLOBALS['lms']['pathcourse'], -1) != '/' && substr($GLOBALS['lms']['pathcourse'], -1) != '\\' ? '/' : '');
	sl_open_fileoperations();
	if($file_sponsor != '') 	sl_unlink($path.$file_sponsor);
	if($file_logo != '') 		sl_unlink($path.$file_logo);
	if($file_material != '') 	sl_unlink($path.$file_material);
	if($file_othermaterial != '') sl_unlink($path.$file_othermaterial);
	if($file_demo != '') 		sl_unlink($path.$file_demo);
	sl_close_fileoperations();

	//if the scs exist create a room
	if($GLOBALS['where_scs'] !== false) {

		require_once($GLOBALS['where_scs'].'/lib/lib.room.php');
		$re = deleteRoom(false, 'course', $id_course);
	}

	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_course WHERE idCourse = '$id_course'")) return false;

	return true;
}

function newCourseEdition() {
	checkPerm('mod');

	if(isset($_POST["course_id"])) {
		
		newCourseEditionForm($_POST["course_id"]);
	} else {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

		$lang =& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
		$form = new Form();
		
		$with_edition_arr 	= getCoursesWithEditionArr();
		$array_lang 		= $GLOBALS['globLangManager']->getAllLangCode();
		
		$title_area = array(
			'index.php?modname=public_course_admin&amp;op=course_list' => $lang->def('_COURSE'),
			$lang->def('_COURSE_EDITION_FOR_COURSE')." '".$_POST['course_name']."'"
		);
		
		$GLOBALS['page']->add(
			getTitleArea($title_area, 'course_edition')
			.'<div class="std_block">'
		
			.getBackUi( 'index.php?modname=public_course_admin&op=course_list', $lang->def('_BACK') )

			.$form->getFormHeader($lang->def('_COURSE_EDITION_FOR_COURSE')." '".$_POST['course_name']."'")
			
			.$form->openForm('course_edition_creation', 'index.php?modname=public_course_admin&amp;op=add_course_edition', false, false, 'multipart/form-data')
			
			.$form->openElementSpace()
			.$form->getDropdown($lang->def("_COURSE"), "course_id", "course_id", $with_edition_arr)
			.$form->closeElementSpace()
			 
			.$form->openButtonSpace()
			.$form->getButton('course_create', 'course_create', $lang->def('_CREATE'))
			.$form->getButton('course_undo_edition', 'course_undo_edition', $lang->def('_UNDO'))
			.$form->closeButtonSpace()
			
			.$form->closeForm()
			.'</div>'
		, 'content');
	}
}

function newCourseEditionForm($course_id) {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$lang =& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
	$form = new Form();
	
	$array_lang = $GLOBALS['globLangManager']->getAllLangCode();

	// possibile course status
	$course_status = array(
		CST_PREPARATION => $lang->def('_CST_PREPARATION'), 
		CST_AVAILABLE 	=> $lang->def('_CST_AVAILABLE'), 
		CST_EFFECTIVE 	=> $lang->def('_CST_CONFIRMED'), 
		CST_CONCLUDED 	=> $lang->def('_CST_CONCLUDED'), 
		CST_CANCELLED 	=> $lang->def('_CST_CANCELLED')
	);

	//type of edition
	$edition_type= array (
		'elearning' => $lang->def('_COURSE_TYPE_ELEARNING'),
		'blended' => $lang->def('_COURSE_TYPE_BLENDED'),
		'classroom'=> $lang->def('_COURSE_TYPE_CLASSROOM')
	);

	$query_course = "
	SELECT 	code, name, description, lang_code, status, level_show_user, subscribe_method,
		linkSponsor, mediumTime, permCloseLO, userStatusOp, difficult,
		show_progress, show_time, show_extra_info, show_rules, date_begin, date_end, hour_begin, hour_end, valid_time,
		min_num_subscribe, max_num_subscribe, max_sms_budget, selling, prize, advance,
		course_type, policy_point, point_to_all, course_edition	
	FROM ".$GLOBALS['prefix_lms']."_course
	WHERE idCourse = '".$course_id."'";
	$course = mysql_fetch_assoc(mysql_query($query_course));
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_COURSE_EDITION_FOR_COURSE')." '".$course["name"]."'", 'course_edition')
		.'<div class="std_block">'
		
		.getBackUi( 'index.php?modname=public_course_admin&op=course_list', $lang->def('_BACK') )
	
		.$form->getFormHeader($lang->def('_COURSE_EDITION_FOR_COURSE')." '".$course["name"]."'")
		
		.$form->openForm('course_edition_creation', 'index.php?modname=public_course_admin&amp;op=add_course_edition', false, false, 'multipart/form-data')
		
		.$form->openElementSpace()
		.$form->getHidden('course_id', 'course_id', $course_id)
		.$form->getTextfield($lang->def('_CODE'), 'course_edition_code', 'course_edition_code', '50', $course["code"])
		.$form->getTextfield($lang->def('_COURSE_NAME'), 'course_edition_name', 'course_edition_name', '255', $course["name"])
		
		// mode for course end ---------------------------------------------
		.$form->getDropdown($lang->def('_STATUS'), 'course_edition_status', 'course_edition_status', $course_status, $course['status'] )
		.$form->getTextarea($lang->def('_DESCRIPTION'), 'course_edition_descr', 'course_edition_descr', $course['description'])
		.$form->getDropdown($lang->def('_COURSE_TYPE'), 'edition_type', 'edition_type', $edition_type, $course["course_type"] )
	, 'content');

	$GLOBALS['page']->add(
		$form->getOpenFieldset($lang->def('_COURSE_SUBSCRIPTION'))

		//-----------------------------------------------------------------
		.$form->getOpenCombo($lang->def('_USER_CAN_SUBSCRIBE'))
		.$form->getRadio($lang->def('_SUBSCRIPTION_CLOSED'), 'subscription_closed', 'can_subscribe', '0', TRUE )
		.$form->getRadio($lang->def('_SUBSCRIPTION_OPEN'), 'subscription_open', 'can_subscribe', '1', FALSE)
		.$form->getRadio($lang->def('_SUBSCRIPTION_IN_PERIOD').":", 'subscription_period', 'can_subscribe', '2', FALSE)
		.$form->getCloseCombo()
		
		.$form->getDatefield($lang->def('_SUBSCRIPTION_DATE_BEGIN').":", 'sub_start_date', 'sub_start_date', "")
		.$form->getDatefield($lang->def('_SUBSCRIPTION_DATE_END').":", 'sub_end_date', 'sub_end_date', "")
		.$form->getCloseFieldset()
	, 'content');

	$GLOBALS['page']->add(
		$form->getOpenFieldset($lang->def('_COURSE_SPECIAL_OPTION'))
		.$form->getTextfield($lang->def('_COURSE_PRIZE'), 		'edition_price', 		'edition_price', 		11, $course["prize"])
		.$form->getTextfield($lang->def('_COURSE_ADVANCE'), 	'edition_advance', 		'edition_advance', 		11, $course['advance'])
		// max number of user that can be subscribed
		.$form->getTextfield($lang->def('_MAX_NUM_SUBSCRIBE'), 	'min_num_subscribe', 	'min_num_subscribe', 	11, $course["min_num_subscribe"])
		.$form->getTextfield($lang->def('_MAX_NUM_SUBSCRIBE'), 	'max_num_subscribe', 	'max_num_subscribe', 	11, $course["max_num_subscribe"])
		.$form->getCheckbox($lang->def('_ALLOW_OVERBOOKING'), 	'allow_overbooking', 	'allow_overbooking', 	1)
		.$form->getCloseFieldset()
	, 'content');
	
	$hours = array('-1' => '- -', '0' =>'00', '01', '02', '03', '04', '05', '06', '07', '08', '09', 
					'10', '11', '12', '13', '14', '15', '16', '17', '18', '19', 
					'20', '21', '22', '23' );
	$quarter = array('-1' => '- -', '00' => '00', '15' => '15', '30' => '30', '45' => '45');
	
	if($course['hour_begin'] != '-1') {
		$hb_sel = (int)substr($course['hour_begin'], 0, 2);
		$qb_sel = substr($course['hour_begin'], 3, 2); 
	} else $hb_sel = $qb_sel = '-1';
	
	if($course['hour_end'] != '-1') {
		$he_sel = (int)substr($course['hour_end'], 0, 2);
		$qe_sel = substr($course['hour_end'], 3, 2); 
	} else $he_sel = $qe_sel = '-1';
	
	$GLOBALS['page']->add(
		$form->getOpenFieldset($lang->def('_EDITION_PERIOD'))
		.$form->getDatefield($lang->def('_DATE_BEGIN'), 	'course_edition_date_begin', 	'course_edition_date_begin', 
			$GLOBALS['regset']->databaseToRegional($course["date_begin"]))
		.$form->getDatefield($lang->def('_DATE_END'), 		'course_edition_date_end', 		'course_edition_date_end', 
			$GLOBALS['regset']->databaseToRegional($course["date_end"]))
		
		
		.$form->getLineBox(
			'<label for="hour_begin_hour">'.$lang->def('_HOUR_BEGIN').'</label>',
			$form->getInputDropdown('dropdown_nw', 'hour_begin_hour', 'hour_begin[hour]', $hours, $hb_sel, '')
			.' : '
			.$form->getInputDropdown('dropdown_nw', 'hour_begin_quarter', 'hour_begin[quarter]', $quarter, $qe_sel, '')
		)
		
		.$form->getLineBox(
			'<label for="hour_end_hour">'.$lang->def('_HOUR_END').'</label>',
			$form->getInputDropdown('dropdown_nw', 'hour_end_hour', 'hour_end[hour]', $hours, $he_sel, '')
			.' : '
			.$form->getInputDropdown('dropdown_nw', 'hour_end_quarter', 'hour_end[quarter]', $quarter, $qe_sel, '')
		)
		
		.$form->getCloseFieldset()
	, 'content');

	$GLOBALS['page']->add(
		$form->getOpenFieldset($lang->def('_DOCUMENT_UPLOAD'))
		.$form->getFilefield($lang->def('_USER_MATERIAL'), 			'course_edition_user_material', 		'course_edition_user_material')
		.$form->getFilefield($lang->def('_OTHER_USER_MATERIAL'), 	'course_edition_otheruser_material', 	'course_edition_otheruser_material')
		.$form->getCloseFieldset()
		
		.$form->closeElementSpace()
		
		.$form->openButtonSpace()
		
		.$form->getButton('course_create', 'course_create', $lang->def('_CREATE'))
		.$form->getButton('course_undo_edition', 'course_undo_edition', $lang->def('_UNDO'))
		
		.$form->closeButtonSpace()
		
		.$form->closeForm()
		.'</div>'
	, 'content');
}

function insCourseEdition() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.multimedia.php');

	$array_lang	 = $GLOBALS['globLangManager']->getAllLangCode();
	
	$id_course = $_POST['course_id'];
	
	if($_POST['course_edition_name'] == '')
		 $_POST['course_edition_name'] = def('_NO_NAME', 'admin_course_managment', 'lms');

	$path = '/doceboLms/'.$GLOBALS['lms']['pathcourse'];
	if(substr($path, -1) != '/' && substr($path, -1) != '\\') $path = $path.'/';

	$file_sponsor 	= '';
	$file_logo 		= '';
	$file_material 	= '';
	$file_othermaterial = '';
	$error 			= 0;
	$show_level 	= 0;
	$user_status 	= 0;
	
	if(isset($_POST['user_status'])) {
		while(list($status) = each($_POST['user_status'])) $user_status |= (1 << $status);
	}
	if(isset($_POST['course_edition_show_level'])) {
		while(list($lv) = each($_POST['course_edition_show_level'])) $show_level |= (1 << $lv);
	}
	
	sl_open_fileoperations();
	if($_FILES['course_edition_user_material']['tmp_name'] != '') {
		
		$file_material = 'edition_user_material_'.mt_rand(0, 100).'_'.time().'_'.$_FILES['course_edition_user_material']['name'];
		$re = createImageFromTmp(	$_FILES['course_edition_user_material']['tmp_name'],
									$path.$file_material,
									$_FILES['course_edition_user_material']['name'],
									150,
									150,
									true );
		if(!$re) {
			$error = 1;
			$file_material = '';
		}
	}
	if($_FILES['course_edition_otheruser_material']['tmp_name'] != '') {

		$file_othermaterial = 'edition_otheruser_material_'.mt_rand(0, 100).'_'.time().'_'.$_FILES['course_edition_otheruser_material']['name'];
		$re = createImageFromTmp(	$_FILES['course_edition_otheruser_material']['tmp_name'],
									$path.$file_othermaterial,
									$_FILES['course_edition_otheruser_material']['name'],
									150,
									150,
									true );
		if(!$re) {
			$error = 1;
			$file_othermaterial = '';
		}
	}
	if($_FILES['course_edition_sponsor_logo']['tmp_name'] != '') {

		$file_sponsor = 'edition_sponsor_'.mt_rand(0, 100).'_'.time().'_'.$_FILES['course_edition_sponsor_logo']['name'];
		$re = createImageFromTmp(	$_FILES['course_edition_sponsor_logo']['tmp_name'],
									$path.$file_sponsor,
									$_FILES['course_edition_sponsor_logo']['name'],
									150,
									150,
									true );
		if(!$re) {
			$error = 1;
			$file_sponsor = '';
		}
	}
	if($_FILES['course_edition_logo']['tmp_name'] != '') {

		$file_logo = 'edition_logo_'.mt_rand(0, 100).'_'.time().'_'.$_FILES['course_edition_logo']['name'];
		$re = createImageFromTmp(	$_FILES['course_edition_logo']['tmp_name'],
									$path.$file_logo,
									$_FILES['course_edition_logo']['name'],
									150,
									150,
									true );
		if(!$re) {
			$error = 1;
			$file_sponsor = '';
		}
	}
	sl_close_fileoperations();

	// if subsribe gap is defined with the date ------------------------------- 
	if ($_POST["can_subscribe"] != "2") {
		$sub_start_date = "NULL";
		$sub_end_date = "NULL";
	} else {
		$sub_start_date = "'".$GLOBALS["regset"]->regionalToDatabase($_POST["sub_start_date"], "date")."'";
		$sub_end_date = "'".$GLOBALS["regset"]->regionalToDatabase($_POST["sub_end_date"], "date")."'";
	}
	
	// insert the course in database -----------------------------------------------------------
	$hour_begin = '-1';
	$hour_end = '-1';
	if($_POST['hour_begin']['hour'] != '-1') {
		
		$hour_begin = ( strlen($_POST['hour_begin']['hour']) == 1 ? '0'.$_POST['hour_begin']['hour'] : $_POST['hour_begin']['hour'] );
		if($_POST['hour_begin']['quarter'] == '-1') $hour_begin .= ':00';
		else $hour_begin .= ':'.$_POST['hour_begin']['quarter'];
	}
	
	if($_POST['hour_end']['hour'] != '-1') {
		
		$hour_end = ( strlen($_POST['hour_end']['hour']) == 1 ? '0'.$_POST['hour_end']['hour'] : $_POST['hour_end']['hour'] );
		if($_POST['hour_end']['quarter'] == '-1') $hour_end .= ':00';
		else $hour_end .= ':'.$_POST['hour_end']['quarter'];
	}
	
	
	$query_course_edition = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_course_edition
		SET idCourse 			= '".$id_course."',
			code 				= '".$_POST['course_edition_code']."',
			name 				= '".$_POST['course_edition_name']."',
			description 		= '".$_POST['course_edition_descr']."',
			status 				= '".(int)$_POST['course_edition_status']."',
			
			date_begin 			= '".$GLOBALS['regset']->regionalToDatabase($_POST['course_edition_date_begin'],'date')."',
			date_end 			= '".$GLOBALS['regset']->regionalToDatabase($_POST['course_edition_date_end'],'date')."',
			hour_begin 			= '".$hour_begin."',
			hour_end 			= '".$hour_end."',

			img_material 		= '".$file_material."',
			img_othermaterial 	= '".$file_othermaterial."',

			min_num_subscribe 	= '".(int)$_POST["min_num_subscribe"]."',
			max_num_subscribe 	= '".(int)$_POST["max_num_subscribe"]."',
			price 				= '".$_POST["edition_price"]."',
			advance 			= '".$_POST["edition_advance"]."',

			edition_type 		= '".$_POST["edition_type"]."',
			allow_overbooking 	= '".( isset($_POST["allow_overbooking"]) ? 1 : 0 )."',
			can_subscribe 		= '".(int)$_POST["can_subscribe"]."',
			sub_start_date 		= ".$sub_start_date.",
			sub_end_date 		= ".$sub_end_date."";
	
	if(!mysql_query($query_course_edition)) {
		
		$error = 1;
		if($file_sponsor != '') sl_unlink($path.$file_sponsor);
		if($file_logo != '') sl_unlink($path.$file_logo);
		if($file_material != '') sl_unlink($path.$file_material);
		if($file_othermaterial != '') sl_unlink($path.$file_othermaterial);
		jumpTo('index.php?modname=public_course_admin&op=course_list&result=err_course');
	} else {
		
		$edition_id = mysql_insert_id();

		$acl_manager =& $GLOBALS["current_user"]->getAclManager();
		$group = '/lms/course_edition/'.$edition_id.'/subscribed';
		$group_idst =$acl_manager->getGroupST($group);

		if ($group_idst === FALSE) {
			$group_idst =$acl_manager->registerGroup($group, 'all the user of a course edition', true, "course");
		}

		// send alert ---------------------------------------------------------------------------
		require_once($GLOBALS['where_framework'] . '/lib/lib.eventmanager.php');

		$msg_composer = new EventMessageComposer('admin_course_management', 'lms');

		$msg_composer->setSubjectLangText('email', '_ALERT_SUBJECT', false);
		$msg_composer->setBodyLangText('email', '_ALERT_TEXT', array(	'[url]' => $GLOBALS['lms']['url'],
			'[course_code]' => $_POST['course_edition_code'],
			'[course]' => $_POST['course_edition_name'] ) );

		$msg_composer->setSubjectLangText('sms', '_ALERT_SUBJECT_SMS', false);
		$msg_composer->setBodyLangText('sms', '_ALERT_TEXT_SMS', array(	'[url]' => $GLOBALS['lms']['url'],
			'[course_code]' => $_POST['course_edition_code'],
			'[course]' => $_POST['course_edition_name'] ) );

		require_once($GLOBALS['where_lms'] . '/lib/lib.course.php');
		$course_man = new Man_Course();
		$recipients = $course_man->getIdUserOfLevel($id_course);
		createNewAlert(	'CoursePropModified',
			'course',
			'add',
			'1',
			'Inserted course '.$_POST['course_name'],
			$recipients,
			$msg_composer );
		jumpTo('index.php?modname=public_course_admin&op=course_list&result=ok_course');
	}


}

function modCourseEdition() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.tab.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');
	
	$lang	=& DoceboLanguage::createInstance('admin_course_managment', 'lms');
	$form 	= new Form();
	$out	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$course_status = array(
		CST_PREPARATION => $lang->def('_CST_PREPARATION'), 
		CST_AVAILABLE 	=> $lang->def('_CST_AVAILABLE'), 
		CST_EFFECTIVE 	=> $lang->def('_CST_CONFIRMED'), 
		CST_CONCLUDED 	=> $lang->def('_CST_CONCLUDED'), 
		CST_CANCELLED 	=> $lang->def('_CST_CANCELLED')
	);
	
	//type of edition
	$edition_type= array (
		'elearning' => $lang->def('_COURSE_TYPE_ELEARNING'),
		'blended' => $lang->def('_COURSE_TYPE_BLENDED'),
		'classroom'=> $lang->def('_COURSE_TYPE_CLASSROOM')
	);
	
	list($id_course_edition) = each($_POST['mod_course_edition']);
	
	$query_course_edition = "
	SELECT *
	FROM ".$GLOBALS['prefix_lms']."_course_edition
	WHERE idCourseEdition = '".$id_course_edition."'";
	$course_edition = mysql_fetch_assoc(mysql_query($query_course_edition));
	
	// set page title
	$title_area 	= array(
		'index.php?modname=public_course_admin&amp;op=course_list' => $lang->def('_COURSE'),
		$lang->def('_COURSE_EDITION_MODIFY').' : '.$course_edition['name']
	);
	$date_begin 	= $GLOBALS['regset']->databaseToRegional($course_edition['date_begin'],'date');
	$date_end 		= $GLOBALS['regset']->databaseToRegional($course_edition['date_end'],'date');
	
	$out->add(
		getTitleArea($title_area, 'configuration')
		.'<div class="std_block">'
		
		.$form->openForm('upd_course', 'index.php?modname=public_course_admin&amp;op=upd_course', false, false, 'multipart/form-data')
		
		//also print the hidden id course
		.$form->getHidden('mod_course_edition'.$id_course_edition, 'mod_course_edition['.$id_course_edition.']', $id_course_edition)
		
		// print course name hidden
		.$form->getHidden("course_id", "course_id", $course_edition["idCourse"])
		.$form->getHidden("old_date_begin", "old_date_begin", $course_edition['date_begin'])
		.$form->getHidden("old_date_end", "old_date_end", $course_edition['date_end'])
	);
	$out->add(
		$form->openElementSpace()
		.$form->getTextfield($lang->def('_CODE'), 	'course_edition_code', 		'course_edition_code', 	'50', 	$course_edition['code'])
		.$form->getTextfield($lang->def('_COURSE_NAME'), 	'course_edition_name', 		'course_edition_name', 	'255', 	$course_edition['name'])
		.$form->getDropdown($lang->def('_STATUS'), 	'course_edition_status', 	'course_edition_status', 		$course_status, 	$course_edition['status'] )
		.$form->getDropdown($lang->def('_COURSE_TYPE'), 	'edition_type', 			'edition_type', 				$edition_type, 		$course_edition['edition_type'] )
		.$form->getTextarea($lang->def('_DESCRIPTION'), 'course_edition_descr', 	'course_edition_descr', 		$course_edition['description'])
	);
	$out->add(
		$form->getOpenFieldset($lang->def('_COURSE_SUBSCRIPTION'))
		
		.$form->getOpenCombo($lang->def('_USER_CAN_SUBSCRIBE'))
		.$form->getRadio($lang->def('_SUBSCRIPTION_CLOSED'), 		'subscription_closed', 	'can_subscribe', '0', ($course_edition['can_subscribe'] == 0) )
		.$form->getRadio($lang->def('_SUBSCRIPTION_OPEN'), 			'subscription_open', 	'can_subscribe', '1', ($course_edition['can_subscribe'] == 1) )
		.$form->getRadio($lang->def('_SUBSCRIPTION_IN_PERIOD').":", 'subscription_period', 	'can_subscribe', '2', ($course_edition['can_subscribe'] == 2) )
		.$form->getCloseCombo()
		.$form->getDatefield($lang->def('_SUBSCRIPTION_DATE_BEGIN').":", 	'sub_start_date', 	'sub_start_date', 
			$GLOBALS["regset"]->databaseToRegional($course_edition['sub_start_date'], "date"))
		.$form->getDatefield($lang->def('_SUBSCRIPTION_DATE_END').":", 		'sub_end_date', 	'sub_end_date', 
			$GLOBALS["regset"]->databaseToRegional($course_edition['sub_end_date'], "date"))
		.$form->getCloseFieldset()
	);
	$out->add(
		$form->getOpenFieldset($lang->def('_COURSE_SPECIAL_OPTION'))
		.$form->getTextfield($lang->def('_COURSE_PRIZE'), 		'edition_price', 		'edition_price', 	11, 	$course_edition["price"])
		.$form->getTextfield($lang->def('_COURSE_ADVANCE'), 	'edition_advance', 		'edition_advance', 	11, 	$course_edition['advance'])
		.$form->getTextfield($lang->def('_MIN_NUM_SUBSCRIBE'), 	'min_num_subscribe', 	'min_num_subscribe', 11, 	$course_edition["min_num_subscribe"])
		.$form->getTextfield($lang->def('_MAX_NUM_SUBSCRIBE'), 	'max_num_subscribe', 	'max_num_subscribe', 11, 	$course_edition["max_num_subscribe"])
		.$form->getCheckbox($lang->def('_ALLOW_OVERBOOKING'), 	'allow_overbooking', 	'allow_overbooking', 1, 	$course_edition["allow_overbooking"])
		.$form->getCloseFieldset()
	);
	
	$hours = array('-1' => '- -', '0' =>'00', '01', '02', '03', '04', '05', '06', '07', '08', '09', 
					'10', '11', '12', '13', '14', '15', '16', '17', '18', '19', 
					'20', '21', '22', '23' );
	$quarter = array('-1' => '- -', '00' => '00', '15' => '15', '30' => '30', '45' => '45');
	
	if($course_edition['hour_begin'] != '-1') {
		$hb_sel = (int)substr($course_edition['hour_begin'], 0, 2);
		$qb_sel = substr($course_edition['hour_begin'], 3, 2); 
	} else $hb_sel = $qb_sel = '-1';
	
	if($course_edition['hour_end'] != '-1') {
		$he_sel = (int)substr($course_edition['hour_end'], 0, 2);
		$qe_sel = substr($course_edition['hour_end'], 3, 2); 
	} else $he_sel = $qe_sel = '-1';
	
	$out->add(
		$form->getOpenFieldset($lang->def('_COURSE_TIME_OPTION'))
		.$form->getDatefield($lang->def('_DATE_BEGIN'), 'course_edition_date_begin', 	'course_edition_date_begin', 	$date_begin)
		.$form->getDatefield($lang->def('_DATE_END'), 	'course_edition_date_end', 		'course_edition_date_end', 		$date_end)
		
		
		.$form->getLineBox(
			'<label for="hour_begin_hour">'.$lang->def('_HOUR_BEGIN').'</label>',
			$form->getInputDropdown('dropdown_nw', 'hour_begin_hour', 'hour_begin[hour]', $hours, $hb_sel, '')
			.' : '
			.$form->getInputDropdown('dropdown_nw', 'hour_begin_quarter', 'hour_begin[quarter]', $quarter, $qb_sel, '')
		)
		
		.$form->getLineBox(
			'<label for="hour_end_hour">'.$lang->def('_HOUR_END').'</label>',
			$form->getInputDropdown('dropdown_nw', 'hour_end_hour', 'hour_end[hour]', $hours, $he_sel, '')
			.' : '
			.$form->getInputDropdown('dropdown_nw', 'hour_end_quarter', 'hour_end[quarter]', $quarter, $qe_sel, '')
		)
		
		
		.$form->getCloseFieldset()
	);
	$out->add(
		$form->getOpenFieldset($lang->def('_DOCUMENT_UPLOAD'))
		.$form->getExtendedFilefield(	$lang->def('_USER_MATERIAL'),
		 								'course_edition_material', 
										'course_edition_material', 
										$course_edition["img_material"] )
		.$form->getExtendedFilefield(	$lang->def('_OTHER_USER_MATERIAL'),
		 								'course_edition_othermaterial', 
										'course_edition_othermaterial', 
										$course_edition["img_othermaterial"] )
		.$form->getCloseFieldset()
		.$form->closeElementSpace()
	);
	$out->add(	
		$form->openButtonSpace()
		.$form->getButton('course_edition_modify', 'course_edition_modify', $lang->def('_SAVE'))
		.$form->getButton('course_undo', 'course_undo', $lang->def('_UNDO'))
		.$form->closeButtonSpace()
		
		.$form->closeForm()
		.'</div>'
	);
}

function confirmModCourseEdition () {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.multimedia.php');

	$array_lang = $GLOBALS['globLangManager']->getAllLangCode();
	list($id_course_edition) =  each($_POST['mod_course_edition']);

	$path = '/doceboLms/'.$GLOBALS['lms']['pathcourse'];
	if(substr($path, -1) != '/' && substr($path, -1) != '\\') { $path = $path.'/'; }

	$error 					= 0;
	$show_level 			= 0;
	$file_edition_material 	= '';
	$file_edition_othermaterial = '';
	
	// manage file  upload -----------------------------------------
	if((is_array($_FILES) && !empty($_FILES)) || (is_array($_POST["file_to_del"]))) sl_open_fileoperations();

	if (is_array($_POST["file_to_del"])) {
		foreach($_POST["file_to_del"] as $field_id => $old_file) {
			
			sl_unlink($path.$old_file);
		}
	}
	
	if(isset($_FILES['course_edition_material']) && $_FILES['course_edition_material']['tmp_name'] != '') {
		
		// delete old file
		if((isset($_POST["old_course_edition_material"])) && (!empty($_POST["old_course_edition_material"]))) {
			
			sl_unlink($path.$_POST["old_course_edition_material"]);
		}
		// upload new file
		$file_edition_material = 'usermaterial_'.mt_rand(0, 100).'_'.time().'_'.$_FILES['course_edition_material']['name'];
		if(!sl_upload($_FILES['course_edition_material']['tmp_name'], $path.$file_edition_material)) {
			
			$error = true;
			$file_edition_material = '';
		}
	} elseif(!isset($_POST["file_to_del"]["course_edition_material"])) {
		
		// new not loaded use old file
		$file_edition_material = (isset($_POST["old_course_edition_material"]) ? $_POST["old_course_edition_material"] : "" );
	}
	
	if(isset($_FILES['course_edition_othermaterial']) && $_FILES['course_edition_othermaterial']['tmp_name'] != '') {
		
		// delete old file
		if((isset($_POST["old_course_edition_othermaterial"])) && (!empty($_POST["old_course_edition_othermaterial"]))) {
			
			sl_unlink($path.$_POST["old_course_edition_othermaterial"]);
		}
		// upload new file
		$file_edition_othermaterial = 'otherusermaterial_'.mt_rand(0, 100).'_'.time().'_'.$_FILES['course_edition_othermaterial']['name'];
		if(!sl_upload($_FILES['course_edition_othermaterial']['tmp_name'], $path.$file_edition_othermaterial)) {
			
			$error = true;
			$file_edition_othermaterial = '';
		}
	} else if(!isset($_POST["file_to_del"]["course_edition_othermaterial"])) {
		
		// new not loaded use old file
		$file_edition_othermaterial=(isset($_POST["old_course_edition_othermaterial"]) ? $_POST["old_course_edition_othermaterial"] : "");
	}
	sl_close_fileoperations();

	// save mod in db ---------------------------------------
	if ($_POST["can_subscribe"] != "2") {
		$sub_start_date = "NULL";
		$sub_end_date 	= "NULL";
	} else {
		$sub_start_date = "'".$GLOBALS["regset"]->regionalToDatabase($_POST["sub_start_date"], "date")."'";
		$sub_end_date 	= "'".$GLOBALS["regset"]->regionalToDatabase($_POST["sub_end_date"], "date")."'";
	}
	
	$date_begin = $GLOBALS['regset']->regionalToDatabase($_POST['course_edition_date_begin'],'date');
	$date_end = $GLOBALS['regset']->regionalToDatabase($_POST['course_edition_date_end'],'date');
	
	$hour_begin = '-1';
	$hour_end = '-1';
	if($_POST['hour_begin']['hour'] != '-1') {
		
		$hour_begin = ( strlen($_POST['hour_begin']['hour']) == 1 ? '0'.$_POST['hour_begin']['hour'] : $_POST['hour_begin']['hour'] );
		if($_POST['hour_begin']['quarter'] == '-1') $hour_begin .= ':00';
		else $hour_begin .= ':'.$_POST['hour_begin']['quarter'];
	}
	
	if($_POST['hour_end']['hour'] != '-1') {
		
		$hour_end = ( strlen($_POST['hour_end']['hour']) == 1 ? '0'.$_POST['hour_end']['hour'] : $_POST['hour_end']['hour'] );
		if($_POST['hour_end']['quarter'] == '-1') $hour_end .= ':00';
		else $hour_end .= ':'.$_POST['hour_end']['quarter'];
	}
	
	
	$query_course_edition = "
	UPDATE ".$GLOBALS['prefix_lms']."_course_edition
	SET code 				= '".$_POST['course_edition_code']."',
		name 				= '".$_POST['course_edition_name']."',
		description 		= '".$_POST['course_edition_descr']."',
		status 				= '".(int)$_POST['course_edition_status']."',

		img_material 		='".$file_edition_material."',
		img_othermaterial 	='".$file_edition_othermaterial."',

		date_begin 			= '".$date_begin."',
		date_end 			= '".$date_end."',
		hour_begin 			= '".$hour_begin."',
		hour_end 			= '".$hour_end."',

		min_num_subscribe 	= '".(int)$_POST["min_num_subscribe"]."',
		max_num_subscribe 	= '".(int)$_POST["max_num_subscribe"]."',
		price 				= '".$_POST["edition_price"]."',
		advance 			= '".$_POST["edition_advance"]."',

		edition_type 		= '".$_POST["edition_type"]."',
		allow_overbooking 	= '".( isset($_POST["allow_overbooking"]) ? 1 : 0 )."',
		can_subscribe 		= '".(int)$_POST["can_subscribe"]."',
		sub_start_date 		= ".$sub_start_date.",
		sub_end_date 		= ".$sub_end_date."

	WHERE idCourseEdition = '".$id_course_edition."'";
	if(!mysql_query($query_course_edition)) {

		$error = 1;
		if($file_edition_material != '') sl_unlink($path.$file_edition_material);
		if($file_edition_othermaterial != '') sl_unlink($path.$file_edition_othermaterial);
	} else {

		$acl_manager =& $GLOBALS["current_user"]->getAclManager();
		$group = '/lms/course_edition/'.$id_course_edition.'/subscribed';
		$group_idst =$acl_manager->getGroupST($group);
		if ($group_idst === FALSE) {
			$group_idst = $acl_manager->registerGroup($group, 'all the user of a course edition', true, "course");
		}
		// -- Let's update the classroom occupation schedule if course type is classroom ----
		if(hasClassroom($_POST["edition_type"])) {
			
			$old_date_begin = $_POST["old_date_begin"];
			$old_date_end 	= $_POST["old_date_end"];
			updateCourseTimtable($_POST["course_id"], $id_course_edition, $date_begin, $date_end, $old_date_begin, $old_date_end);
		}
		// ----------------------------------------------------------------------------------
	}
	jumpTo('index.php?modname=public_course_admin&op=course_list&course_category_status='.importVar('course_category_status').'&result=ok_course');
}

function courseEditionDelete() {
	checkPerm('mod');
	if(isset($_POST['confirm_del_edition_course'])) {

		$is_ok = removeCourseEdition($_POST['id_course_edition']);
		jumpTo('index.php?modname=public_course_admin&op=course_list&course_category_status='.importVar('course_category_status')
			.'&result='.( $is_ok ? 'ok_course' : 'fail_course' ));
	} else {
		require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		
		$lang 		=& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');

		list($id_course_edition) = each($_POST['del_course_edition']);
		
		$query_course = "
		SELECT code, name
		FROM ".$GLOBALS['prefix_lms']."_course_edition
		WHERE idCourseEdition = '".$id_course_edition."'";
		list($code, $name) = mysql_fetch_row(mysql_query($query_course));
		
		$title_area 	= array(
			'index.php?modname=public_course_admin&amp;op=course_list' => $lang->def('_COURSE'),
			$lang->def('_COURSE_EDITION')
		);
		
		$GLOBALS['page']->add(
			getTitleArea($title_area, 'course')
			.'<div class="std_block">'
			.Form::openForm('course_edition_del', 'index.php?modname=public_course_admin&amp;op=del_course')
			.Form::getHidden('id_course_edition', 'id_course_edition', $id_course_edition)
			.getDeleteUi(	$lang->def('_AREYOUSURE_DEL_EDITION'),
							'<span class="text_bold">'.$lang->def('_CODE').' : </span>'.$code.'<br />'
							.'<span class="text_bold">'.$lang->def('_COURSE_NAME').' : </span>'.$name,
							false,
							'confirm_del_edition_course['.$id_course_edition.']',
							'course_undo')
			.Form::closeForm()
			.'</div>'
		, 'content' );
	}
}


function removeCourseEdition($id_course_edition) {

	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	$query_course_edition = "
	SELECT imgSponsor, img_course ,img_material,img_othermaterial
	FROM ".$GLOBALS['prefix_lms']."_course_edition
	WHERE idCourseEdition = '".$id_course_edition."'";
	list($old_sponsor, $old_logo,$old_material,$old_othermaterial) = mysql_fetch_row(mysql_query($query_course_edition));

	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	require_once($GLOBALS['where_lms'].'/setting.php');

	$path = '/doceboLms/'.$GLOBALS['lms']['pathcourse'];
	if(substr($path, -1) != '/' && substr($path, -1) != '\\') {
		$path = $path.'/';
	}

	sl_open_fileoperations();
	if($old_sponsor != '')
	if(!sl_unlink($path.$old_sponsor)) return false;
	if($old_logo != '')
	if(!sl_unlink($path.$old_logo)) return false;
	if($old_material != '')
	if(!sl_unlink($path.$old_material)) return false;
	if($old_othermaterial != '')
	if(!sl_unlink($path.$old_othermaterial)) return false;
	sl_close_fileoperations();

	if(!mysql_query("DELETE FROM ".$GLOBALS['prefix_lms']."_course_edition WHERE idCourseEdition = '$id_course_edition'")) return false;

	$acl_manager =& $GLOBALS["current_user"]->getAclManager();
	$group ='/lms/course_edition/'.$id_course_edition.'/subscribed';
	$group_idst =$acl_manager->getGroupST($group);
	$acl_manager->deleteGroup($group_idst);

	return true;
}

function assignMenu() {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	if(isset($_POST['assign'])) {

		$id_course = importVar('id_course', true, 0);
		$id_custom = importVar('selected_menu', true, 0);

		require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

		$acl_man	=& $GLOBALS['current_user']->getAclManager();
		$course_man = new Man_Course();

		$levels =& $course_man->getCourseIdstGroupLevel($id_course);
		if(empty($levels) || implode('', $levels) == '') $levels =& createCourseLevel($id_course);
		
		$course_man->removeCourseRole($id_course);
		$course_man->removeCourseMenu($id_course);
		$course_idst =& $course_man->getCourseIdstGroupLevel($id_course);

		$result = cerateCourseMenuFromCustom($id_custom, $id_course, $course_idst);

		jumpTo('index.php?modname=public_course_admin&op=course_list&result='.( $result ? 'ok_course' : 'fail_course' ));

	} else {

		$lang =& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');

		list($id_course) = each($_POST['assign_menu_course']);
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

		require_once($GLOBALS['where_lms'].'/admin/modules/category/category.php');
		require_once($GLOBALS['where_lms'].'/admin/modules/category/tree.category.php');
		require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.sessionsave.php');

		$form = new Form();
		$menu_custom = getAllCustom();
		$sel_custom = key($menu_custom);
		reset($menu_custom);

		$title_area 	= array(
			'index.php?modname=public_course_admin&amp;op=course_list' => $lang->def('_COURSE'),
			$lang->def('_ASSIGN_MENU')
		);

		$GLOBALS['page']->setWorkingZone('content');
		$GLOBALS['page']->add(
			getTitleArea($title_area, 'course')
			.'<div class="std_block">'
	
			.$form->openForm('course_creation', 'index.php?modname=public_course_admin&amp;op=assignMenu')
			.$form->openElementSpace()
			.$form->getHidden('id_course', 'id_course', $id_course)
			.$form->getDropdown($lang->def('_COURSE_MENU_TO_ASSIGN'), 'selected_menu', 'selected_menu', $menu_custom, $sel_custom )
	
			.$form->closeElementSpace()
	
			.$form->openButtonSpace()
			.$form->getButton('assign', 'assign', $lang->def('_ASSIGN'))
			.$form->getButton('undo', 'undo', $lang->def('_UNDO'))
			.$form->closeButtonSpace()
			.$form->closeForm()
		.'</div>');
	}
}

function move_course() {
	checkPerm('mod');

	require_once($GLOBALS['where_lms'].'/admin/modules/category/category.php');
	require_once($GLOBALS['where_lms'].'/admin/modules/category/tree.category.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.sessionsave.php');

	$out=& $GLOBALS['page'];
	$lang=& DoceboLanguage::createInstance('admin_course_managment', 'lms');

	$out->add(getTitleArea(array($lang->def('_COURSE'), $lang->def('_MOVECOURSE')), 'course')
	.'<div class="std_block">');

	if( isset($_POST["move_course"]) ) list($id_course) = each($_POST['move_course']);
	else $id_course = importVar('id_course', true, 0);

	$categoryDb = new TreeDb_CatDb($GLOBALS['prefix_lms'].'_category');
	$treeView = new TreeView_CatView($categoryDb, 'course_category', $lang->def('_COURSE_CATEGORY'));

	if(isset($_POST[$treeView->_getCancelId()])) jumpTo('index.php?modname=public_course_admin&op=course_list');

	$treeView->parsePositionData($_POST, $_POST, $_POST);
	$treeView->show_action = false;

	if( isset($_POST[$treeView->_getFolderNameId()]) ) $folderid = $_POST[$treeView->_getFolderNameId()];
	else $folderid = $treeView->getSelectedFolderId();

	$folder = $treeView->tdb->getFolderById( $treeView->getSelectedFolderId() );
	$out->add('<form method="post" action="index.php?modname=public_course_admin&amp;op=move_course">'
		.'<input type="hidden" id="id_course" name="id_course" value="'.$id_course.'" />'
		.'<input type="hidden" id="folderid" name="'.$treeView->_getFolderNameId().'" value="'.$folderid.'" />');
	$out->add('<input type="hidden" name="folder_id" value="'.$treeView->getSelectedFolderId().'" />');
	$out->add('<input type="hidden" name="id_course" value="'.$id_course.'" />');
	$out->add('<div>'.$treeView->getFolderPrintName($folder).'</div>');
	$out->add($treeView->load());
	$out->add(' <img src="'.$treeView->_getMoveImage().'" alt="'.$treeView->_getMoveAlt().'" /> '
	.'<input type="submit" class="TreeViewAction" value="'.$lang->def("_MOVECOURSE").'"'
	.' name="move_course_here" id="move'.$id_course.'" />');
	$out->add(' <img src="'.$treeView->_getCancelImage().'" alt="'.$treeView->_getCancelAlt().'" /> '
	.'<input type="submit" class="TreeViewAction" value="'.$treeView->_getCancelLabel().'"'
	.' name="'.$treeView->_getCancelId().'" id="'.$treeView->_getCancelId().'" />');

	$out->add('</form>'
	.'</div>');
}

function move_course_upd() {
	checkPerm('mod');

	require_once($GLOBALS['where_lms'].'/admin/modules/category/category.php');
	require_once($GLOBALS['where_lms'].'/admin/modules/category/tree.category.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.sessionsave.php');

	$id_course = importVar('id_course', true, 0);

	$categoryDb = new TreeDb_CatDb($GLOBALS['prefix_lms'].'_category');
	$treeView = new TreeView_CatView($categoryDb, 'course_category', '');
	$treeView->parsePositionData($_POST, $_POST, $_POST);

	$error = 0;
	$query_course = "
	UPDATE ".$GLOBALS['prefix_lms']."_course
	SET idCategory = '".$treeView->getSelectedFolderId()."'
	WHERE idCourse = '".$id_course."'";
	if(!mysql_query($query_course)) {

		$error = 1;
	}
	jumpTo('index.php?modname=public_course_admin&op=course_list&course_category_status='.importVar('course_category_status')
		.'&result='.( $error ? 'ok_course' : 'fail_course' ));
}
/*
function courseCertifications() {

	if ((isset($_GET["id_course"])) && (!empty($_GET["id_course"]))) {
		$id_course=(int)$_GET["id_course"];
	}
	else
		return FALSE;

	// print form for certificate content -----------------------------------------------
	// print hidden field for general, point, edition -----------------------------------
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.sessionsave.php');
	$lang =& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
	$out =& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$form = new Form();

	$url="index.php?modname=public_course_admin&amp;op=upd_certifications&amp;id_course=".$id_course;
	$out->add($form->openForm("main_form", $url));
	//$out->add($form->openElementSpace());

	$general=getOtherTab('general');
	$out->add($general);
	$point=getOtherTab('point');
	$out->add($point);
	$tb	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_CERTIFICATE_TO_COURSE_CAPTION'), $lang->def('_CERTIFICATE_TO_COURSE_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink("index.php?modname=public_course_admin&amp;op=certifications&amp;id_course=".$id_course);
	$ini=$tb->getSelectedElement();
	//search query of certificates
	$query_certificate = "
	SELECT id_certificate, name, description
	FROM ".$GLOBALS['prefix_lms']."_certificate
	ORDER BY name
	LIMIT $ini,".$GLOBALS['lms']['visuItem'];
	
	// search certificates assigned -----------------------------------------------------
	$query_certificate_assigned="
	SELECT certificates
	FROM ".$GLOBALS['prefix_lms']."_course
	where idCourse= ".$id_course."";
	list($assigned_certificate) = mysql_fetch_row(mysql_query($query_certificate_assigned));
	
	$assigned_certificate=explode(',',$assigned_certificate);
	$query_certificate_tot = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_lms']."_certificate";
	$re_certificate = mysql_query($query_certificate);
	list($tot_certificate) = mysql_fetch_row(mysql_query($query_certificate_tot));
	$type_h = array('image', 'news_short_td');
	$cont_h	= array(
	$lang->def('_TITLE'),
	$lang->def('_DESCRIPTION')
	);
	$cont_h[] = '';
	$type_h[] = 'image';
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	$certificate_to_course=array();
	while(list($idCert, $name, $descr) = mysql_fetch_row($re_certificate)) {
		$cont = array(
		$name,
		$descr
		);
		$certificate_val=0;
		$certificate_assigned=0;
		foreach($assigned_certificate as $key => $certificate_assigned){
			if ($certificate_assigned==$idCert) {
				$certificate_val=$assigned_certificate;
			}
		}
		$cont[] = $form->getCheckbox('',
		'certificate_to_course',
		'certificate_to_course['.$idCert.']',
		$idCert,
		$certificate_val) ;
		$tb->addBody($cont);
	}
	$out->add(getTitleArea($lang->def('_TITLE_CERTIFICATE_TO_COURSE'), 'certificate', $lang->def('_ALT_TITLE_CERTIFICATE_TO_COURSE'))
	.'<div class="std_block">'	);
	$out->add($tb->getTable().$tb->getNavBar($ini, $tot_certificate).'</div>');


	//$out->add($form->closeElementSpace());

	$out->add($form->openButtonSpace()
	.$form->getButton('save', 'save', $lang->def('_SAVE'))
	.$form->getButton('course_undo', 'course_undo', $lang->def('_UNDO')));
	$out->add(
	$form->closeButtonSpace()
	.$form->closeForm());

}


function updateCertifications() {
	
	$id_course = importVar('id_course', false, 0);

	$certificates='';
	if(is_string($_POST['certificate_to_course']) )
	{
		$certificates=unserialize(($_POST['certificate_to_course']));
		$certificates=implode(',',$certificates);
	} else if(isset($_POST['certificate_to_course'])) $certificates=implode(',',$_POST['certificate_to_course']);


	$qtxt ="UPDATE ".$GLOBALS["prefix_lms"]."_course ";
	$qtxt.="SET certificates='".$certificates."' ";
	$qtxt.="WHERE idCourse='".$id_course."' LIMIT 1";

	$q=mysql_query($qtxt);

	jumpTo('index.php?modname=public_course_admin&op=course_list&result=ok_course');
}
*/
function classroomToCourse() {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.sessionsave.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	
	if(isset($_POST['classroom_to_course'])) list($idCourse) = each($_POST['classroom_to_course']);
	else $idCourse = importVar('idCourse', true, 0);
	
	$of_loc = importVar('of_loc', false, '');
	$of_name = importVar('of_name', false, '');
	
	$query_course_name="
	SELECT name
	FROM ".$GLOBALS['prefix_lms']."_course
	WHERE idCourse = $idCourse ";
	list($course_name)= mysql_fetch_row(mysql_query($query_course_name));
	
	$checked_class = checkAvailableClass($idCourse);
	
	$out =& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$form = new Form();
	
	$lang =& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
	
	$tb	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_CLASSROOMTOCOURSE_CAPTION'), $lang->def('_CLASSROOMTOCOURSE_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink('index.php?modname=public_course_admin&amp;op=classroom_to_course&amp;idCourse='.$idCourse.'&amp;of_loc='.$of_loc.'&amp;of_name='.$of_name);
	
	$ini = $tb->getSelectedElement();
	
	$classroom_order = "l.location, c.name ";
	if($of_loc == 'loc') 	$classroom_order = "l.location, c.name ";
	if($of_loc == 'locd') 	$classroom_order = "l.location DESC, c.name ";
	if($of_name == 'name') 	$classroom_order = "c.name, l.location ";
	if($of_name == 'namec') $classroom_order = "c.name DESC, l.location ";
	if($of_loc == '' && $of_name == '') $of_loc = 'loc';
	
	//search query of classrooms ----------------------------------------------
	$query_classroom = "
	SELECT c.idClassroom, c.name, c.description, l.location
	FROM ".$GLOBALS['prefix_lms']."_classroom AS c 
		JOIN ".$GLOBALS['prefix_lms']."_class_location AS l
	WHERE l.location_id = c.location_id
	ORDER BY ".$classroom_order."
	LIMIT $ini,".$GLOBALS['lms']['visuItem'];
	$re_classroom = mysql_query($query_classroom);
	
	$query_classroom_tot = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_lms']."_classroom";
	list($tot_classroom) = mysql_fetch_row(mysql_query($query_classroom_tot));
	
	// search classrooms assigned ---------------------------------------------
	$query_class_assigned = "
	SELECT classrooms
	FROM ".$GLOBALS['prefix_lms']."_course
	where idCourse= ".$idCourse."";
	list($assigned_classroom) = mysql_fetch_row(mysql_query($query_class_assigned));
	
	// table intestation
	$type_h = array('', '', '', 'image');
	$cont_h	= array(
		'<a href="'."index.php?modname=public_course_admin&amp;op=classroom_to_course&amp;idCourse=$idCourse&amp;of_loc="
			.( $of_loc != 'locd' ? 'loc' : 'locd' ).'">'
		
		
		.( $of_loc == 'loc' 
			? '<img src="'.getPathImage().'/standard/1downarrow.png" alt="'.$lang->def('_DEF_DOWN').'" />'
			: ( $of_loc == 'locd' 
				? '<img src="'.getPathImage().'/standard/1uparrow.png" alt="'.$lang->def('_DEF_UP').'" />' 
				:  '<img src="'.getPathImage().'/standard/sort.png" alt="'.$lang->def('_DEF_SORT').'" />' ) )	
		.$lang->def('_CLASSROOMLOCATION').'</a>', 
			
		'<a href="'."index.php?modname=public_course_admin&amp;op=classroom_to_course&amp;idCourse=$idCourse&amp;of_name="
			.( $of_name != 'named' ? 'name' : 'named' ).'">'
			
		.( $of_name == 'name' 
			? '<img src="'.getPathImage().'/standard/1downarrow.png" alt="'.$lang->def('_DEF_DOWN').'" />'
			: ( $of_name == 'named' 
				? '<img src="'.getPathImage().'/standard/1uparrow.png" alt="'.$lang->def('_DEF_UP').'" />' 
				:  '<img src="'.getPathImage().'/standard/sort.png" alt="'.$lang->def('_DEF_SORT').'" />' ) )	
		.$lang->def('_CLASSROOMNAME').'</a>', 
		
		$lang->def('_STATUS'), 
		$lang->def('_USETHIS')
	);
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	
	$class_room_to_edition=array();
	while(list($idClassroom, $name, $descr, $location) = mysql_fetch_row($re_classroom)) {
		
		$cont = array(
			'<label for="class_room_to_course_'.$idClassroom.'">'.$location.'</label>',
			'<label for="class_room_to_course_'.$idClassroom.'">'.$name.'</label>' 
		);
		
		if(isset($checked_class[$idClassroom])) $cont[] = $lang->def('_CLASSROOM_OCCUPATED_YES');
		else $cont[] = '';
		
		$cont[] = $form->getRadio('',
								'class_room_to_course_'.$idClassroom.'',
								'class_room_to_course',
								$idClassroom,
								$assigned_classroom == $idClassroom ) ;
		$tb->addBody($cont);
	}
	$page_title = array(
		'index.php?modname=public_course_admin&amp;op=course_list' => $lang->def('_TITLE_CLASSROOMTOCOURSE'),
		$lang->def('_TITLE_CLASSROOMTOCOURSE'),
		$course_name
	);
	
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'classroomtocourse', $lang->def('_ALT_TITLE_CLASSROOMTOCOURSE'))
		.'<div class="std_block">'
		
		.($checked_class !== false
			? getResultUi($lang->def('_CLASSROOM_OCCUPATED'))
			: ''
		)
		.getBackUi( 'index.php?modname=public_course_admin&amp;op=course_list', $lang->def('_BACK') )

		.$form->openForm('assignClassroom', 'index.php?modname=public_course_admin&amp;op=assignClassroom', false, false, 'multipart/form-data')
		.$form->getHidden('idCourse', 'idCourse', $idCourse)
		
		.$tb->getTable()
		.$tb->getNavBar($ini, $tot_classroom)
		
		.$form->openButtonSpace()
		.$form->getButton( 'assignClassroom' ,'assignClassroom',$lang->def('_SAVE'))
		.$form->getButton('course_undo', 'course_undo', $lang->def('_UNDO'))
		.$form->closeButtonSpace()
		.$form->closeForm()
		.'</div>'
	, 'content');
}

function assignClassroom() {
	$err = FALSE;

	$idCourse = $_POST['idCourse'];

	// -- timetable setup ------------------------------------------------
	require_once($GLOBALS["where_framework"]."/lib/resources/lib.timetable.php");
	$tt = new TimeTable();

	$resource 		= "classroom";
	$consumer 		= "course";
	$consumer_id 	= $idCourse;
	// -------------------------------------------------------------------
	
	if(isset($_POST['class_room_to_course'])) {
		
		$saved_room = $_POST['class_room_to_course'];

		// -- Adding info to the timetable -----------------------------------

		$qtxt ="
		SELECT date_begin, date_end 
		FROM ".$GLOBALS["prefix_lms"]."_course 
		WHERE idCourse='".(int)$idCourse."'";
		$q = mysql_query($qtxt);

		if(!$q || !mysql_num_rows($q)) {
			jumpTo('index.php?modname=public_course_admin&amp;op=course_list&result=fail_course');
		}
		
		list($start_date, $end_date) = mysql_fetch_row($q);

		$save_ok=$tt->saveEvent(FALSE, 
							$start_date, 
							$end_date, 
							$start_date, 
							$end_date, 
							$resource, 
							$saved_room, 
							$consumer, 
							$consumer_id );
		
	} else {
		$saved_room = "";
	}


	// -- Removin old info from the timetable ----------------------------

	if($saved_room != '') {
		$exclude_resource_id=$saved_room;
	} else {
		$exclude_resource_id=FALSE;
	}

	$tt->deleteAllConsumerEventsForResource($resource, $consumer, $consumer_id, $exclude_resource_id);
	// -------------------------------------------------------------------
	
	$query=	"
	UPDATE ".$GLOBALS['prefix_lms']."_course
	SET classrooms = '$saved_room'
	WHERE idCourse = $idCourse";
	$err = mysql_query($query);
	
	jumpTo('index.php?modname=public_course_admin&amp;op=course_list&result='.( $err === false ? 'ok_course' : 'err_course' ));
}


function classroomToEdition() {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

	if(isset($_POST['classroom_to_edition'])) list($edition_id) = each($_POST['classroom_to_edition']);
	else $edition_id = importVar('edition_id', true, 0);

	$of_loc = importVar('of_loc', false, '');
	$of_name = importVar('of_name', false, '');
	
	$form = new Form();
	
	$query_course_name = "SELECT idCourse, name
	FROM ".$GLOBALS['prefix_lms']."_course_edition
	WHERE idCourseEdition = '".$edition_id."'";
	list($idCourse, $edition_name) = mysql_fetch_row(mysql_query($query_course_name));
	
	$lang =& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
	
	$tb	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_CLASSROOMTOCOURSE_CAPTION'), $lang->def('_CLASSROOMTOCOURSE_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink("index.php?modname=public_course_admin&amp;op=classroom_to_edition&amp;edition_id=$edition_id".'&amp;of_loc='.$of_loc.'&amp;of_name='.$of_name);
	
	$ini = $tb->getSelectedElement();
	$checked_class = checkAvailableClass($idCourse, $edition_id);
	
	$classroom_order = "l.location, c.name ";
	if($of_loc == 'loc') 	$classroom_order = "l.location, c.name ";
	if($of_loc == 'locd') 	$classroom_order = "l.location DESC, c.name ";
	if($of_name == 'name') 	$classroom_order = "c.name, l.location ";
	if($of_name == 'namec') $classroom_order = "c.name DESC, l.location ";
	if($of_loc == '' && $of_name == '') $of_loc = 'loc';
	
	//search query of classrooms ---------------------------------
	$query_classroom = "
	SELECT c.idClassroom, c.name, c.description, l.location
	FROM ".$GLOBALS['prefix_lms']."_classroom AS c 
		JOIN ".$GLOBALS['prefix_lms']."_class_location AS l
	WHERE l.location_id = c.location_id
	ORDER BY ".$classroom_order."
	LIMIT $ini,".$GLOBALS['lms']['visuItem'];
	$re_classroom = mysql_query($query_classroom);
	
	// search classrooms assigned --------------------------------
	$query_class_assigned="
	SELECT classrooms
	FROM ".$GLOBALS['prefix_lms']."_course_edition
	where idCourseEdition= ".$edition_id."";
	list($assigned_classroom) = mysql_fetch_row(mysql_query($query_class_assigned));
	
	$query_classroom_tot = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_lms']."_classroom ";
	list($tot_classroom) = mysql_fetch_row(mysql_query($query_classroom_tot));
	
	// table intestation
	$type_h = array('', '', '', 'image');
	$cont_h	= array(
		'<a href="'."index.php?modname=public_course_admin&amp;op=classroom_to_edition&amp;edition_id=".$edition_id."&amp;of_loc="
			.( $of_loc == 'loc' ? 'locd' : 'loc' ).'">'
			
		.( $of_loc == 'loc' 
			? '<img src="'.getPathImage().'/standard/1downarrow.png" alt="'.$lang->def('_DEF_DOWN').'" />'
			: ( $of_loc == 'locd' 
				? '<img src="'.getPathImage().'/standard/1uparrow.png" alt="'.$lang->def('_DEF_UP').'" />' 
				:  '<img src="'.getPathImage().'/standard/sort.png" alt="'.$lang->def('_DEF_SORT').'" />' ) )
		.$lang->def('_CLASSROOMLOCATION').'</a>', 
			
		'<a href="'."index.php?modname=public_course_admin&amp;op=classroom_to_edition&amp;edition_id=".$edition_id."&amp;of_name="
			.( $of_name == 'name' ? 'named' : 'name' ).'">'
		
		.( $of_name == 'name' 
			? '<img src="'.getPathImage().'/standard/1downarrow.png" alt="'.$lang->def('_DEF_DOWN').'" />'
			: ( $of_name == 'named' 
				? '<img src="'.getPathImage().'/standard/1uparrow.png" alt="'.$lang->def('_DEF_UP').'" />' 
				:  '<img src="'.getPathImage().'/standard/sort.png" alt="'.$lang->def('_DEF_SORT').'" />' ) )
		.$lang->def('_CLASSROOMNAME').'</a>',
			 
		$lang->def('_STATUS'), 
		$lang->def('_USETHIS')
	);
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	
	$class_room_to_edition = array();
	while(list($idClassroom, $name, $descr, $location) = mysql_fetch_row($re_classroom)) {
		
		$cont = array(
			'<label for="class_room_to_edition_'.$idClassroom.'">'.$location.'</label>',
			'<label for="class_room_to_edition_'.$idClassroom.'">'.$name.'</label>' 
		);
		
		if(isset($checked_class[$idClassroom])) $cont[] = $lang->def('_CLASSROOM_OCCUPATED_YES');
		else $cont[] = '';
		
		$cont[] = $form->getRadio('',
								'class_room_to_edition_'.$idClassroom.'',
								'class_room_to_edition',
								$idClassroom,
								$assigned_classroom == $idClassroom ) ;
		$tb->addBody($cont);
	}
	
	$page_title = array(
		'index.php?modname=public_course_admin&amp;op=course_list' => $lang->def('_TITLE_CLASSROOMTOCOURSE'),
		$lang->def('_TITLE_CLASSROOMTOCOURSE'),
		$edition_name
	);
	
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'classroomtocourse', $lang->def('_ALT_TITLE_CLASSROOMTOCOURSE'))
		.'<div class="std_block">'
		.($checked_class !== false
			? getResultUi($lang->def('_CLASSROOM_OCCUPATED'))
			: ''
		)
		
		.getBackUi( 'index.php?modname=public_course_admin&amp;op=course_list', $lang->def('_BACK') )
		
		.$form->openForm('assignEditionClassroom', 'index.php?modname=public_course_admin&amp;op=assignEditionClassroom', false, false, 'multipart/form-data')
		
		.$form->getHidden('edition_id', 'edition_id', $edition_id)
		.$form->getHidden('idCourse', 'idCourse', $idCourse)

		.$tb->getTable()
		.$tb->getNavBar($ini, $tot_classroom)

		.$form->openButtonSpace()
		.$form->getButton('assignEditionClassroom' ,'assignEditionClassroom', $lang->def('_SAVE'))
		.$form->getButton('course_undo', 'course_undo', $lang->def('_UNDO'))
		.$form->closeButtonSpace()
		.$form->closeForm()
		
		.'</div>'
	, 'content');
}

function assignClassroomToEdition() {
	
	$err 				= FALSE;
	$idCourse 			= importVar('idCourse', true, 0);
	$idCourseEdition 	= importVar('edition_id', true, 0);

	// -- timetable setup ------------------------------------------------
	require_once($GLOBALS["where_framework"]."/lib/resources/lib.timetable.php");
	$tt = new TimeTable();

	$resource 		= "classroom";
	$consumer 		= "course_edition";
	$consumer_id 	= $idCourseEdition;
	// -------------------------------------------------------------------

	if(isset($_POST['class_room_to_edition'])) {
		
		$saved_room = $_POST["class_room_to_edition"];
		
		// -- Adding info to the timetable -----------------------------------
		$qtxt = "SELECT date_begin, date_end "
				."FROM ".$GLOBALS["prefix_lms"]."_course_edition "
				."WHERE idCourseEdition = '".(int)$idCourseEdition."'";
		$q = mysql_query($qtxt);

		if(!$q || !mysql_num_rows($q)) {
			jumpTo('index.php?modname=public_course_admin&amp;op=course_list&result=fail_course');
		}
		
		list($start_date, $end_date) = mysql_fetch_row($q);
		$save_ok = $tt->saveEvent(FALSE, 
						$start_date, 
						$end_date, 
						$start_date, 
						$end_date, 
						$resource, 
						$saved_room, 
						$consumer, 
						$consumer_id);
		
	} else {
		
		$saved_room = '';
	}

	// -- Removin old info from the timetable ----------------------------
	if($saved_room != '') {
		$exclude_resource_id = $saved_room;
	} else {
		$exclude_resource_id = FALSE;
	}

	$tt->deleteAllConsumerEventsForResource($resource, $consumer, $consumer_id, $exclude_resource_id);
	// -------------------------------------------------------------------

	$query = "UPDATE ".$GLOBALS['prefix_lms']."_course_edition
	SET classrooms = '$saved_room'
	WHERE idCourseEdition = $idCourseEdition";
	$err = mysql_query($query);

	jumpTo('index.php?modname=public_course_admin&amp;op=course_list&result='.($err === FALSE ? "ok_course" : "fail_course"));
}

// requires array of class assigned to course
/**
 * This function check if classrooms to be occupated are available or not
 *
 * @param int $idCourse
 * @return array $course_class or false if classrooms are available
 */
function checkAvailableClass ($idCourse, $edition_id=FALSE) {

	require_once($GLOBALS["where_framework"]."/lib/resources/lib.timetable.php");
	$tt=new TimeTable();

	$resource="classroom";

	if ($edition_id === FALSE) {
		$consumer="course";
		$consumer_id=$idCourse;

		$qtxt ="SELECT date_begin, date_end FROM ".$GLOBALS["prefix_lms"]."_course ";
		$qtxt.="WHERE idCourse='".(int)$idCourse."'";
	}
	else {
		$consumer="course_edition";
		$consumer_id=$edition_id;

		$qtxt ="SELECT date_begin, date_end FROM ".$GLOBALS["prefix_lms"]."_course_edition ";
		$qtxt.="WHERE idCourseEdition='".(int)$edition_id."'";
	}

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_assoc($q);

		$start_date=$row["date_begin"];
		$end_date=$row["date_end"];
	}
	else {
		return FALSE;
	}


	// Occupied resources
	$in_use=$tt->getResourcesInUse($resource, $start_date, $end_date);

	// Classroom Resources used by current consumer
	$consumer_resources=$tt->getConsumerResources($consumer, $consumer_id, $start_date, $end_date, $resource);

	foreach($consumer_resources as $val) {

		$tmp_id=$val["resource_id"];
		if (in_array($tmp_id, $in_use)) {
			unset($in_use[$tmp_id]);
		}
	}

	if (empty($in_use))
		$in_use=FALSE;

	return $in_use;
}

function getPointCourse ($idCourse,$idField){
	$query="SELECT point
	FROM ".$GLOBALS['prefix_lms']."_course_point
	WHERE idCourse = $idCourse
	AND idField = $idField ";
	$re_point=mysql_query($query);
	if($re_point) {
		list($point)=mysql_fetch_row($re_point);
		return $point ;
	} else return false;
}

function insertPointCourse ($idCourse,$idField){
	$query="SELECT point
	FROM ".$GLOBALS['prefix_lms']."_course_point
	WHERE idCourse = $idCourse
	AND idField = $idField ";
	$re_point=mysql_query($query);
	if(list($point)=mysql_fetch_row($re_point)) {
		return $point ;
	} else return false;

}


function getCoursesWithEditionArr($flat, $id_category, $id_categories) {

	$qtxt ="SELECT idCourse, name FROM ".$GLOBALS["prefix_lms"]."_course ";
	$qtxt.="WHERE course_edition='1' AND ";
	$qtxt.="idCategory IN ( ".( !$flat ? $id_category  : implode(",", $id_categories) )." ) ";
	$qtxt.="ORDER BY name";

	$q=mysql_query($qtxt);

	$with_edition_arr=array();
	if (($q) && (mysql_num_rows($q) > 0)) {
		while($row=mysql_fetch_assoc($q)) {

			$id=$row["idCourse"];
			$with_edition_arr[$id]=$row["name"];

		}
	}

	return $with_edition_arr;
}

// return string of hidden for others tab
function getOtherTab($prefix) {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$hidden='';
	$len_prefix = strlen($prefix);
	foreach ($_POST as $key => $value ) {
		if(substr($key,0,$len_prefix)== $prefix){
			if (is_array($value)){
				$value=(serialize($value));
			}
			$hidden.=Form::getHidden($key,$key,$value);

		}
	}
	return $hidden;
}


function updateCourseTimtable($course_id, $edition_id, $start_date, $end_date, $old_start_date=FALSE, $old_end_date=FALSE) {

	updateClassroomOccupation($course_id, $edition_id, $start_date, $end_date, $old_start_date, $old_end_date);
	updateUserOccupation($course_id, $edition_id, $start_date, $end_date);

}


function updateClassroomOccupation($course_id, $edition_id, $start_date, $end_date, $old_start_date=FALSE, $old_end_date=FALSE) {

	require_once($GLOBALS["where_framework"]."/lib/resources/lib.timetable.php");
	$tt=new TimeTable();

	$resource="classroom";
	$course_id=(int)$course_id;

	if ($edition_id === FALSE) {
		$consumer="course";
		$consumer_id=$course_id;

		$qtxt ="SELECT classrooms FROM ".$GLOBALS["prefix_lms"]."_course ";
		$qtxt.="WHERE idCourse='".$course_id."'";
	}
	else {
		$consumer="course_edition";
		$consumer_id=(int)$edition_id;

		$qtxt ="SELECT classrooms FROM ".$GLOBALS["prefix_lms"]."_course_edition ";
		$qtxt.="WHERE idCourseEdition='".(int)$edition_id."'";
	}

	$q=mysql_query($qtxt);

	if (($q) && (mysql_num_rows($q) > 0)) {
		$row=mysql_fetch_assoc($q);

		$classrooms=$row["classrooms"];
	}
	else {
		return FALSE;
	}
/*
	$classroom_arr=explode(",", $classrooms);
	$updated=array();

	foreach($classroom_arr as $resource_id) {
*/
		$save_ok=$tt->updateEvent(FALSE, $start_date, $end_date, $old_start_date, $old_end_date, $resource, $classrooms, $consumer, $consumer_id);
/*
		if ($save_ok) {
			$updated[]=$resource_id;
		}
	}


	if (empty($updated))
		$classrooms="";
	else
		$classrooms=implode(",", $updated);

	if ($edition_id === FALSE) {
		$qtxt ="UPDATE ".$GLOBALS['prefix_lms']."_course ";
		$qtxt.="SET classrooms='".$classrooms."' ";
		$qtxt.="WHERE idCourse='".(int)$course_id."'";
	}
	else {
		$qtxt ="UPDATE ".$GLOBALS['prefix_lms']."_course_edition ";
		$qtxt.="SET classrooms='".$classrooms."' ";
		$qtxt.="WHERE idCourseEdition='".(int)$edition_id."'";
	}
*/

//	$q=mysql_query($qtxt);

	return $save_ok;
}


function updateUserOccupation($course_id, $edition_id, $start_date, $end_date) {

	require_once($GLOBALS["where_framework"]."/lib/resources/lib.timetable.php");
	$tt=new TimeTable();

	$consumer="user";
	$course_id=(int)$course_id;

	if ($edition_id > 0) {
		$resource="course_edition";
		$resource_id=(int)$edition_id;
	}
	else {
		$resource="course";
		$resource_id=$course_id;
	}

	$tt->updateEventDateByResource($resource, $resource_id, $start_date, $end_date);
}


function hasClassroom($type) {

	if (($type == "classroom") || ($type == "blended")) {
		$res=TRUE;
	}
	else {
		$res=FALSE;
	}

	return $res;
}

function numberOfUserViewed ($id_course)
{
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.publicadminmanager.php');
	require_once($GLOBALS['where_lms'].'/modules/public_subscribe_admin/public_subscribe_admin.php');
	
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
	
	$edition_id = getCourseEditionId();
	$user_alredy_subscribed	= getSubscribed($id_course, false, false, true, $edition_id);
	
	$number_of_user_viewed = 0;
	
	foreach ($user_alredy_subscribed as $user)
		if (in_array($user, $user_selected))
			$number_of_user_viewed++;
	
	return $number_of_user_viewed;
}

function publicCourseAdminDispatch($op) {
	require_once($GLOBALS['where_framework'].'/lib/lib.preference.php');
	$pref = new UserPreferences(getLogUserId());
	if(!$pref->getPreference('admin_rules.max_course_subscribe') && $pref->getPreference('admin_rules.limit_course_subscribe') == 'on')
	{
		$lang =& DoceboLanguage::createInstance('profile', 'framework');
		$GLOBALS['page']->add('<p class="result_container"><strong>'.$lang->def('_SUBSCRIPTION_USER_LIMIT_REACHED').'</strong></p>', 'content');
	}
	if(isset($_POST['new_course'])) $op = 'new_course';
	if(isset($_POST['mod_course']) || isset($_GET['mod_course'])) $op = 'mod_course';

	if(isset($_POST['del_course'])) $op = 'del_course';
	if(isset($_POST['del_course_edition'])) $op = 'del_course_edition';
	if(isset($_POST['confirm_del_edition_course'])) $op='del_course_edition';
	if(isset($_POST['mod_course_edition'])) $op = 'mod_course_edition';
	if(isset($_POST['course_edition_modify'])) $op='confirm_mod_course_edition';
	if(isset($_POST['course_undo'])) $op = 'course_list';
	if(isset($_POST['course_undo_edition'])) $op = 'course_list';
	if(isset($_POST['assignClassroomToEd'])) $op = 'assignClassroomToEd';
	if(isset($_POST['assignClassroom'])) $op = 'assignClassroom';
	if(isset($_POST['classroom_to_course'])) $op = 'classroom_to_course';
	if(isset($_POST['classroom_to_edition'])) $op = 'classroom_to_edition';
	if(isset($_POST['classroom_to_course_ed'])) $op = 'classroom_to_course_ed';
	if(isset($_POST['checkAvailableClass'])) $op = 'classroom_to_course';
	if(isset($_POST['assign_menu_course'])) $op = 'assignMenu';
	if(isset($_POST['move_course'])) $op = 'move_course';
	if(isset($_POST['move_course_here'])) $op = 'move_course_upd';
	if(isset($_POST['undo'])) $op = 'course_list';
	if(isset($_POST['new_course_edition'])) $op = 'new_course_edition';
	if(isset($_POST['upd_course'])) $op = 'upd_course';
	
	
	if((isset($_GET['ini_hidden']) || isset($_POST['ini_hidden'])) && $op != 'course_list') {
		
		$_SESSION['course_category']['ini_status'] = importVar('ini_hidden', true, 0);
	}
	
	switch($op) {
		default:
		case "course_list" : {
			course();
		};break;
		case "new_course" : {
			addCourse();
		};break;
		case "add_course" : {
			insCourse();
		};break;
		case "del_course" : {
			courseDelete();
		};break;
		case "add_course_edition" : {
			insCourseEdition();
		};break;
		case "mod_course" : {
			modCourse();
		};break;
		case "new_course_edition" : {
			newCourseEdition();
		};break;
		case "mod_course_edition" : {
			modCourseEdition();
		};break;
		case "confirm_mod_course_edition" : {
			confirmModCourseEdition();
		};break;
		case "upd_course" : {
			courseUpdate();
		};break;
		case "move_course" : {
			move_course();
		};break;
		case "move_course_upd" : {
			move_course_upd();
		};break;
		case "del_course_edition" : {
			courseEditionDelete();
		};break;
		case "assignMenu" : {
			assignMenu();
		};break;
		
		
		case "classroom_to_course" : {
			classroomToCourse();
		};break;
		case "classroom_to_edition" : {
			classroomToEdition();
		};break;
		case "assignClassroomToEd" :
		case "assignEditionClassroom" : {
			assignClassroomToEdition();
		};break;
		case "assignClassroom" : {
			assignClassroom();
		};break;
		
		
		case "certifications": {
			require_once($GLOBALS["where_lms"]."/admin/modules/certificate/course.certificate.php");
			courseCertifications();
		} break;
		case "upd_certifications": {
			require_once($GLOBALS["where_lms"]."/admin/modules/certificate/course.certificate.php");
			updateCertifications();
		} break;
	}
}

?>