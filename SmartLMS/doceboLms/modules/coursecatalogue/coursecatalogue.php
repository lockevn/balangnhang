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
 * @version  $Id: coursecatalogue.php 1003 2007-03-31 13:59:46Z fabio $
 * @author	 Fabio Pirovano <fabio [at] docebo-com>
 * @package course
 * @subpackage course list
 */

if(!defined("IN_DOCEBO")) die('You can\'t access directly');
if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

define("_SUCCESS_wait","_SUBSCRIPTION_OK_WAIT");
define("_SUCCESS_subs","_SUBSCRIPTION_OK");
define("_SUCCESS_buy","_BUYED_CORRECTLY");

define("_FAIL_subs","_SUBSCRIPTION_ERROR");
define("_FAIL_buy","_BUYED_FAIL");

require_once($GLOBALS['where_lms'].'/lib/lib.levels.php');
require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

function coursecatalogueJsSetup() {

	addYahooJs(array(
		'animation' 		=> 'animation-min.js',
		'dragdrop' 			=> 'dragdrop-min.js',
		'button' 			=> 'button-min.js',
		'container' 		=> 'container-min.js',
		'my_window' 		=> 'windows.js'
	), array(
		'container/assets/skins/sam' => 'container.css',
		'button/assets/skins/sam' => 'button.css'
	));
	
	addCss('style_course_list', 'lms');
	addCss('style_yui_docebo', 'lms');
	addJs($GLOBALS['where_lms_relative'].'/modules/coursecatalogue/', 'ajax.coursecatalogue.js');
}

function courselist(&$url) {
	checkPerm('view');

	require_once(dirname(__FILE__).'/lib.coursecatalogue.php');

	addCss('style_tab', 'lms');
	coursecatalogueJsSetup();
	loadEcomItems();

	$GLOBALS['page']->add(
	'<!--[if lt IE 7.]>
		<script type="text/javascript" src="'.$GLOBALS['where_framework_relative'].'/lib/lib.pngfix.js"></script>
	<![endif]-->', 'page_head');

	$lang 	=& DoceboLanguage::createInstance('coursecatalogue');
	$lang_c =& DoceboLanguage::createInstance('course');

	// searching for courses -------------------------------------------------------------
	if(isset($_GET['tab']) || isset($_POST['tab'])) $selected_tab = $_SESSION['cc_tab'] = importVar('tab', false, 'category');
	elseif(isset($_SESSION['cc_tab'])) $selected_tab = $_SESSION['cc_tab'];
	elseif(isset($GLOBALS['lms']['first_coursecatalogue_tab'])) $selected_tab = $GLOBALS['lms']['first_coursecatalogue_tab'];
	else $selected_tab = 'category';
	
	$tab_config = unserialize(urldecode($GLOBALS['lms']['tablist_coursecatalogue']));
	
	$tab_list = array();
	
	foreach($tab_config as $tab => $is_selected)
	{
		switch($tab)
		{
			case 'category':
				if($is_selected)
					$tab_list['category'] = $lang->def('_TAB_VIEW_CATEGORY');
			break;
			
			case 'all':
				if($is_selected)
					$tab_list['all'] = $lang->def('_TAB_VIEW_ALL');
			break;
			
			case 'pathcourse':
				if($GLOBALS['lms']['use_coursepath'] == '1' && $is_selected)
					$tab_list['pathcourse'] = $lang->def('_TAB_VIEW_PATHCOURSE');
			break;
			
			case 'mostscore':
				if($GLOBALS['lms']['use_social_courselist'] == 'on' && $is_selected)
					$tab_list['mostscore'] = $lang->def('_TAB_VIEW_MOSTSCORE');
			break;
			
			case 'popular':
				if($GLOBALS['lms']['use_social_courselist'] == 'on' && $is_selected)
					$tab_list['popular'] = $lang->def('_TAB_VIEW_MOSTPOPULAR');
			break;
			
			case 'recent':
				if($GLOBALS['lms']['use_social_courselist'] == 'on' && $is_selected)
					$tab_list['recent'] = $lang->def('_TAB_VIEW_RECENT');
			break;
		}
	}

	// show courses --------------------------------------------------------------------
	/*$GLOBALS['page']->add(
		getTitleArea($lang->def('_TITLE_COURSECATALOGUE'), 'coursecatalogue'), 'content');
	*/
	// print tabs -----------------------------------------------------------------------

	$GLOBALS['page']->add(
		'<div id="coursecatalogue_tab_container">'
		.'<ul class="flat_tab">', 'content');
	foreach($tab_list as $key => $tab_name) {

		$GLOBALS['page']->add('<li'.( $selected_tab == $key ? ' class="now_selected"' : '').'>'
			.'<a href="'.$url->getUrl('tab='.$key).'"><span>'.$tab_name.'</span></a></li>', 'content');
	}
	$GLOBALS['page']->add('</ul>'
		.'</div>'
		.'<div class="std_block yui-skin-sam" id="coursecatalogue">', 'content');

	if($selected_tab == 'pathcourse') {

		displayCoursePathList($url, $selected_tab);
	} else {

		displayCourseList($url, $selected_tab);
	}

	$GLOBALS['page']->add('</div>', 'content');

	// end of function ----------------------------------------------------------------
	unsetEcomItems();
}

function showdemo(&$url) {

	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.multimedia.php');
	$lang = DoceboLanguage::createInstance('course', 'lms');

	$id_course = importVar('id_course', true, 0);

	$man_course = new Man_Course();
	$course = $man_course->getCourseInfo($id_course);

	$back = importVar('back', false, '');
	if($back == 'details') {

		$page_title = array('index.php?modname=coursecatalogue&amp;op=courselist' => $lang->def('_COURSELIST'),
							$lang->def('_SHOW_DEMO') );
	} else {

		$page_title = array('index.php?modname=coursecatalogue&amp;op=courselist' => $lang->def('_COURSELIST'),
							'index.php?modname=coursecatalogue&amp;op=coursedetails&amp;id_course='.$id_course => $course['name'],
							$lang->def('_SHOW_DEMO') );
	}
	$GLOBALS['page']->add( getTitleArea($page_title, 'course')
		.'<div class="std_block">'
		.'<div class="align_center">'
	, 'content');

	$ext = end(explode('.', $course['course_demo']));
	$GLOBALS['page']->add(
		getEmbedPlay('/doceboLms/'.$GLOBALS['lms']['pathcourse'], $course['course_demo'], $ext, '450', '450', true, $lang->def('_SHOW_DEMO') )
	, 'content');

	$GLOBALS['page']->add(
		'</div>'
		.'<h2><span class="code_course">'.$course['code'].' - </span> '.$course['name'].'</h2>'
		.'<p>'.$course['description'].'</p>'
		.'</div>', 'content');
}

function donwloadmaterials(&$url) {

	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.multimedia.php');
	$lang = DoceboLanguage::createInstance('course', 'lms');

	$id_course = importVar('id_course', true, 0);
	$edition_id = importVar('edition_id', true, 0);

	if($id_course != 0) {

		$man_course = new DoceboCourse($id_course);
		$file = $man_course->getValue('img_material');
	}
	if($edition_id != 0) {
		$select_edition = " SELECT img_material ";
		$from_edition 	= " FROM ".$GLOBALS["prefix_lms"]."_course_edition";
		$where_edition 	= " WHERE idCourseEdition = '".$edition_id."' ";

		list($file) = mysql_fetch_row(mysql_query($select_edition.$from_edition.$where_edition));
	}
	require_once($GLOBALS['where_framework'].'/lib/lib.download.php');
	$ext = end(explode('.', $file));
	sendFile('/doceboLms/'.$GLOBALS['lms']['pathcourse'], $file, $ext);
}

function showprofile(&$url) {

	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.lms_user_profile.php');

	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$lang =& DoceboLanguage::createInstance('course');

	$id_user 	= importVar('id_user');
	$id_course 	= importVar('id_course');
	$man_course = new Man_Course();
	$course = $man_course->getCourseInfo($id_course);

	$profile = new LmsUserProfile( $id_user );
	$profile->init('profile', 'lms', 'modname=coursecatalogue&op=showprofile&id_course'.$id_course.'&id_user='.$id_user, 'ap');


	$GLOBALS['page']->add(
		getTitleArea($lang->def('_TITLE_COURSECATALOGUE', 'coursecatalogue'), 'coursecatalogue')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=coursecatalogue&amp;op=courselist&amp;id_parent='.$course['idCategory'], $lang->def('_BACK')), 'content');

	$GLOBALS['page']->add(
		'<p class="category_path">'
			.'<b>'.$lang->def('_CATEGORY_PATH').' :</b> '
			.$man_course->getCategoryPath(	$course['idCategory'],
											$lang->def('_MAIN_CATEGORY'),
											$lang->def('_TITLE_CATEGORY_JUMP'),
											'index.php?modname=coursecatalogue&amp;op=courselist',
											'id_parent' )
			.' &gt; '.$course['name']
		.'</p>'
		.$profile->getProfile( getLogUserId() )
		.'</div>'
	, 'content');
}

function &getUserCourse($id_user, $no_wait = FALSE) {

	$course = array();
	$query_course = "
	SELECT idCourse
	FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idUser = '".$id_user."' AND edition_id='0'";
	if ($no_wait)
		$query_course .= " AND waiting = '0'";
	$re_course = mysql_query($query_course);
	while(list($id) = mysql_fetch_row($re_course)) {

		$course[$id] = $id;
	}
	return $course;
}

function &getUserCourseWait($id_user) {

	$course = array();
	$query_course = "
	SELECT idCourse
	FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idUser = '".$id_user."' AND waiting = '1' AND edition_id='0'";
	$re_course = mysql_query($query_course);
	while(list($id) = mysql_fetch_row($re_course)) {

		$course[$id] = $id;
	}
	return $course;
}


function &getUserEdition($id_user, $no_wait = FALSE) {

	$course = array();
	$query_course = "
	SELECT edition_id
	FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idUser = '".$id_user."'";
	if ($no_wait)
		$query_course .= " AND waiting = '0'";
	$re_course = mysql_query($query_course);
	while(list($edition_id) = mysql_fetch_row($re_course)) {

		$course[$edition_id] = $edition_id;
	}
	return $course;
}

function &getUserEditionWait($id_user) {

	$course = array();
	$query_course = "
	SELECT edition_id
	FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idUser = '".$id_user."' AND waiting = '1'";
	$re_course = mysql_query($query_course);
	while(list($edition_id) = mysql_fetch_row($re_course)) {

		$course[$edition_id] = $edition_id;
	}
	return $course;
}


function &getUserCoursepath($id_user, $no_wait = FALSE) {

	$course = array();
	$query_course = "
	SELECT idPath
	FROM ".$GLOBALS['prefix_lms']."_coursepath_user
	WHERE idUser = '".$id_user."' ";
	if ($no_wait) $query_course .= "AND waiting = 0";
	$re_course = mysql_query($query_course);
	while(list($id) = mysql_fetch_row($re_course)) {

		$course[$id] = $id;
	}
	return $course;
}

function &getUserCoursepathWait($id_user) {

	$course = array();
	$query_course = "
	SELECT idPath
	FROM ".$GLOBALS['prefix_lms']."_coursepath_user
	WHERE idUser = '".$id_user."' AND waiting = 1";
	$re_course = mysql_query($query_course);
	while(list($id) = mysql_fetch_row($re_course)) {

		$course[$id] = $id;
	}
	return $course;
}

function printCoursePanel($id_course, &$course, $has_edition=FALSE) {

	$lang =& DoceboLanguage::createInstance('course');

	//status of course -----------------------------------------------------
	$status_lang = array(
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

	$subs_lang = array(
		0 => $lang->def('_COURSE_S_GODADMIN'),
		1 => $lang->def('_COURSE_S_MODERATE'),
		2 => $lang->def('_COURSE_S_FREE'),
		3 => $lang->def('_COURSE_S_SECURITY_CODE') );

	$GLOBALS['page']->add(
		'<table class="cd_course_info">'
			.'<caption class="cd_name">'.$course['name'].'</caption>'
			.'<tr>'
				.'<th scope="row">'.$lang->def('_CODE').'</th><td>'.$course['code'].'</td>'
			.'</tr>'
			.'<tr>'
				.'<th scope="row">'.$lang->def('_COURSE').'</th><td>'.$course['name'].'</td>'
			.'</tr>'
			.'<tr>'
				.'<th scope="row">'.$lang->def('_DIFFICULT_NAME').'</th><td>'.$difficult_lang[$course['difficult']].'</td>'
			.'</tr>', 'content');

	if (!$has_edition) {
		$GLOBALS['page']->add(
				'<tr>'
					.'<th scope="row">'.$lang->def('_STATUS').'</th><td>'.$status_lang[$course['status']].'</td>'
				.'</tr>', 'content');
	}
	$GLOBALS['page']->add(
			'<tr>'
				.'<th scope="row">'.$lang->def('_SUBSCRIBE_METHOD').'</th><td>'.$subs_lang[$course['subscribe_method']].'</td>'
			.'</tr>', 'content');
	if (!$has_edition) {
		$GLOBALS['page']->add(
				'<tr>'
					.'<th scope="row">'.$lang->def('_ENROL_COUNT').'</th><td>'.$course['enrolled'].'</td>'
				.'</tr>'
				.'<tr>'
					.'<th scope="row">'.$lang->def('_CREATION_DATE').'</th><td>'.createDateDistance($course['create_date']).'</td>'
				.'</tr>', 'content');
	}

	$GLOBALS['page']->add(
		'<tr>'
			.'<th scope="row">'.$lang->def('_DESCRIPTION').'</th><td>'.$course['description'].'</td>'
		.'</tr>', 'content');

	if (!$has_edition) {
		$GLOBALS['page']->add(
				'<tr class="cd_course_file">'
					.'<th scope="col" colspan="2">'.$lang->def('_COURSE_FILES').'</th>'
				.'</tr>'
				.'<tr>'
					.'<th scope="row">'.$lang->def('_DEMO_COURSE').'</th><td>'
					.( $course['course_demo'] == ''
						? ''
						: '<a href="index.php?modname=coursecatalogue&amp;op=showdemo&amp;id_course='.$id_course.'&amp;back=details" title="'.$lang->def('_SHOW_COURSE_DEMO').'">'
							.$lang->def('_SHOW_DEMO')
					).'</td>'
				.'</tr>', 'content');
	}
	$GLOBALS['page']->add(
		'</table>'
	, 'content');
}


function printCourseEditionPanel($id_course, &$course, &$edition) {

	$lang =& DoceboLanguage::createInstance('course');

	//status of course -----------------------------------------------------
	$status_lang = array(
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

	$subs_lang = array(
		0 => $lang->def('_COURSE_S_GODADMIN'),
		1 => $lang->def('_COURSE_S_MODERATE'),
		2 => $lang->def('_COURSE_S_FREE'),
		3 => $lang->def('_COURSE_S_SECURITY_CODE') );

	$GLOBALS['page']->add(
		'<table class="cd_course_info">'
			.'<caption class="cd_name">'.$edition['name'].'</caption>'
			.'<tr>'
				.'<th scope="row">'.$lang->def('_CODE').'</th><td>'.$edition['code'].'</td>'
			.'</tr>'
			.'<tr>'
				.'<th scope="row">'.$lang->def('_COURSE').'</th><td>'.$edition['name'].'</td>'
			.'</tr>'
			.'<tr>'
				.'<th scope="row">'.$lang->def('_DIFFICULT_NAME').'</th><td>'.$difficult_lang[$course['difficult']].'</td>'
			.'</tr>', 'content');


	$GLOBALS['page']->add(
			'<tr>'
				.'<th scope="row">'.$lang->def('_STATUS').'</th><td>'.$status_lang[$edition['status']].'</td>'
			.'</tr>', 'content');

	$GLOBALS['page']->add(
			'<tr>'
				.'<th scope="row">'.$lang->def('_SUBSCRIBE_METHOD').'</th><td>'.$subs_lang[$course['subscribe_method']].'</td>'
			.'</tr>', 'content');

	$GLOBALS['page']->add(
			/*'<tr>'
				.'<th scope="row">'.$lang->def('_ENROL_COUNT').'</th><td>'.$course['enrolled'].'</td>'
			.'</tr>'
			.*/'<tr>'
				.'<th scope="row">'.$lang->def('_CREATION_DATE').'</th><td>'.createDateDistance($course['create_date']).'</td>'
			.'</tr>', 'content');

	$GLOBALS['page']->add(
		'<tr>'
			.'<th scope="row">'.$lang->def('_DESCRIPTION').'</th><td>'.$edition['description'].'</td>'
		.'</tr>', 'content');

	$GLOBALS['page']->add(
			'<tr class="cd_course_file">'
				.'<th scope="col" colspan="2">'.$lang->def('_COURSE_FILES').'</th>'
			.'</tr>'
			.'<tr>'
				.'<th scope="row">'.$lang->def('_DEMO_COURSE').'</th><td>'
				.( $course['course_demo'] == ''
					? ''
					: '<a href="index.php?modname=coursecatalogue&amp;op=showdemo&amp;id_course='.$id_course.'&amp;back=details" title="'.$lang->def('_SHOW_COURSE_DEMO').'">'
						.$lang->def('_SHOW_DEMO')
				).'</td>'
			.'</tr>', 'content');

	$GLOBALS['page']->add(
		'</table>'
	, 'content');
}


function coursedetails(&$url) {
	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.user_profile.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.catalogue.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_lms'].'/modules/coursecatalogue/lib.coursecatalogue.php');

	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$lang =& DoceboLanguage::createInstance('course');

	$id_course = importVar('id_course', true, 0);
	$man_course = new Man_Course();
	$course = $man_course->getCourseInfo($id_course);

	$teacher = getSubscribed($id_course, false, 6, true);
	$has_edition=($course["course_edition"] == 1 ? TRUE : FALSE);

	$query_enrolled = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_lms']."_courseuser
	WHERE idCourse = '".$id_course."'";
	list($course['enrolled']) = mysql_fetch_row(mysql_query($query_enrolled));

	$GLOBALS['page']->add(
		getTitleArea($lang->def('_TITLE_COURSECATALOGUE', 'coursecatalogue'), 'coursecatalogue')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=coursecatalogue&amp;op=courselist&amp;id_parent='.$course['idCategory'], $lang->def('_BACK')), 'content');

	$GLOBALS['page']->add(
		'<p class="category_path">'
			.'<b>'.$lang->def('_CATEGORY_PATH').' :</b> '
			.$man_course->getCategoryPath(	$course['idCategory'],
											$lang->def('_MAIN_CATEGORY'),
											$lang->def('_TITLE_CATEGORY_JUMP'),
											'index.php?modname=coursecatalogue&amp;op=courselist',
											'id_parent' )
			.' &gt; '.$course['name']
		.'</p>'
	, 'content');


		$GLOBALS['page']->add(
			'<div class="cd_action">'
			.getSubscribeActionLink($id_course, $course, $lang)
			.'</div>', 'content');



	printCoursePanel($id_course, $course, $has_edition);


	if (hasClassroom($course["course_type"])) {
		$GLOBALS['page']->add('<h2 class="cd_course_teacher">'.$lang->def('_COURSE_CLASSROOMS').'</h2>'
			.'<div class="cd_course_teacher_container">', 'content');

		$GLOBALS['page']->add(getClassroomPanel($course["classrooms"]), 'content');

		$GLOBALS['page']->add('</div>', 'content');
	}


	if (!$has_edition) {
		$GLOBALS['page']->add('<h2 class="cd_course_teacher">'.$lang->def('_TEACHERS').'</h2>'
			.'<div class="cd_course_teacher_container">', 'content');
		if(is_array($teacher) && !empty($teacher))
		while(list(, $id_teach) = each($teacher)) {

			$profile = new UserProfile( $id_teach );
			$profile->init('profile', 'lms', 'modname=coursecatalogue&op=searchuser', 'ap');

			$GLOBALS['page']->add($profile->userPanel(false, false, 'index.php?modname=coursecatalogue&amp;op=showprofile&amp;id_course='.$id_course.'&amp;id_user='.$id_teach), 'content');

		}
		$GLOBALS['page']->add('<div class="no_float"></div>'
			.'</div>', 'content');
	}


	/*
	require_once($GLOBALS["where_lms"]."/modules/coursecatalogue/lib.coursecatalogue.php");
	$editions_table=getCourseEditionTable($id_course);
	if ($editions_table !== FALSE) {

		$GLOBALS['page']->add('<h2 class="cd_edition">'.$lang->def('_EDITIONS').'</h2>'
			.'<div class="cd_edition_container">', 'content');

		$GLOBALS['page']->add($editions_table, 'content');

		$GLOBALS['page']->add('</div>', 'content'); // cd_edition_container
	}
	*/


	$GLOBALS['page']->add('</div>', 'content');
}


function editionDetails(&$url) {

	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.user_profile.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.catalogue.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_lms'].'/modules/coursecatalogue/lib.coursecatalogue.php');

	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$lang =& DoceboLanguage::createInstance('course');

	$course_id = importVar('course_id', true, 0);
	$edition_id = importVar('edition_id', true, 0);
	$man_course = new Man_Course();
	$course = $man_course->getCourseInfo($course_id);
	$edition = $man_course->getEditionInfo($edition_id);

	$teacher = getSubscribed($course_id, false, 6, true);

	$GLOBALS['page']->add(
		getTitleArea($lang->def('_TITLE_COURSECATALOGUE', 'coursecatalogue'), 'coursecatalogue')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=coursecatalogue&amp;op=courselist&amp;id_parent='.$course['idCategory'], $lang->def('_BACK')), 'content');


	$GLOBALS['page']->add(
		'<p class="category_path">'
			.'<b>'.$lang->def('_CATEGORY_PATH').' :</b> '
			.$man_course->getCategoryPath(	$course['idCategory'],
											$lang->def('_MAIN_CATEGORY'),
											$lang->def('_TITLE_CATEGORY_JUMP'),
											'index.php?modname=coursecatalogue&amp;op=courselist',
											'id_parent' )
			.' &gt; '.$course['name']
		.'</p>'
	, 'content');


/*
	if(isUserCourseSubcribed(getLogUserId(), $course_id, $edition_id)) {

		$GLOBALS['page']->add(
			'<div class="cd_action">'
			.$lang->def('_SUBSCRIBED_T')
			.'</div>', 'content');
	} elseif($course['subscribe_method'] == 1 || $course['subscribe_method'] == 2 || $course['subscribe_method'] == 3) {

		$GLOBALS['page']->add('<div class="cd_action">', 'content');

		$subscr_img = '<img src="'.getPathImage().'course/go_subscribe.gif" alt="'.$lang->def('_SUBSCRIBE_YOU', 'coursecatalogue').'" />';
		$selling_img = '<img src="'.getPathImage().'course/add_cart.gif" alt="'.$lang->def('_GO_SELLING', 'coursecatalogue').'" />';

		if($course['selling'] == 1) {

			$GLOBALS['page']->add('<a href="index.php?modname=coursecatalogue&amp;op=addToCart&amp;course_edition='.$edition_id.'" '
				.'title="'.$lang->def('_BUY_COURSE_T', 'coursecatalogue').'">'.$selling_img.' '.$lang->def('_BUY_COURSE', 'coursecatalogue').' ('.$course['prize'].')'.'</a>', 'content');

		} elseif (($course['selling'] == 1)) {

			$GLOBALS['page']->add('<a href="index.php?modname=coursecatalogue&amp;op=reserve&amp;course_edition='.$edition_id.'" '
				.'title="'.$lang->def('_RESERVE_COURSE', 'coursecatalogue').'">'.$selling_img.' '.$lang->def('_RESERVE_COURSE').' ('.$course['prize'].')'.'</a>', 'content');
		} else {

			$GLOBALS['page']->add('<a href="index.php?modname=coursecatalogue&amp;op=subscribecourse&amp;id='.$course_id.'&amp;edition_id='.$edition_id.'" '
				.'title="'.$lang->def('_SUBSCRIBE_COURSE_T', 'coursecatalogue').'">'.$subscr_img.' '.$lang->def('_SUBSCRIBE_YOU').'</a>', 'content');
		}
		$GLOBALS['page']->add('</div>', 'content');
	}
	*/

	$GLOBALS['page']->add(
		'<div class="cd_action">'
		.getSubscribeActionLink($course_id, $course, $lang, $edition_id)
		.'</div>', 'content');



	printCourseEditionPanel($course_id, $course, $edition);


	if (hasClassroom($course["course_type"])) {
		$GLOBALS['page']->add('<h2 class="cd_course_teacher">'.$lang->def('_COURSE_CLASSROOMS').'</h2>'
			.'<div class="cd_course_teacher_container">', 'content');

		$GLOBALS['page']->add(getClassroomPanel($edition["classrooms"]), 'content');

		$GLOBALS['page']->add('</div>', 'content');
	}


	$GLOBALS['page']->add('</div>', 'content');

	require_once($GLOBALS["where_lms"]."/lib/lib.coursesubscribe.php");
	$cs=& CourseSubscribe::getInstance();

	$info=$cs->getSubscribeInfo(FALSE, $edition_id);

}


function getClassroomPanel($classrooms) {

	require_once($GLOBALS["where_lms"]."/lib/lib.classroom.php");
	$cm=new ClassroomManager();


	if (!empty($classrooms)) {
		$where="t1.idClassroom IN (".$classrooms.")";
	}
	else {
		$where="0";
	}

	$rooms=$cm->getClassroomList(FALSE, FALSE, $where);
	$res="";

	$res.="<ul>";
	foreach($rooms["data_arr"] as $classroom) {
		$res.="<li>".$classroom["name"]." (".$classroom["location"].")</li>\n";
	}
	$res.="</ul>\n";

	if (count($rooms["data_arr"]) < 1) {
		$res="";
	}

	return $res;
}


function subscribecourse(&$url) {
	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.catalogue.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.adminmanager.php');

	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$lang =& DoceboLanguage::createInstance('course');

	if ((isset($_GET["edition_id"])) && ($_GET["edition_id"] > 0)) {
		$edition_id=$_GET["edition_id"];
	}
	else {
		$edition_id=FALSE;
	}

	$sel_course = importVar('id', true, 0);
	$man_course = new Man_Course();
	$course = $man_course->getCourseInfo($sel_course);
	if ($edition_id !== FALSE)
		$edition = $man_course->getEditionInfo($edition_id);

	$GLOBALS['page']->add(
		getTitleArea($lang->def('_TITLE_COURSECATALOGUE', 'coursecatalogue'), 'coursecatalogue')
		.'<div class="std_block">', 'content');

	if(isset($_GET['re']))
		$GLOBALS['page']->add(guiResultStatus($lang, $_GET['re']), 'content');

	$GLOBALS['page']->add(
		'<p class="category_path">'
			.'<b>'.$lang->def('_CATEGORY_PATH').' :</b> '
			.$man_course->getCategoryPath(	$course['idCategory'],
											$lang->def('_MAIN_CATEGORY'),
											$lang->def('_TITLE_CATEGORY_JUMP'),
											'index.php?modname=coursecatalogue&amp;op=courselist',
											'id_parent' )
			.' &gt; '.$course['name']
		.'</p>'
	, 'content');


	$cat_man 		= new Catalogue_Manager();
	$catalogues 	=& $cat_man->getUserAllCatalogueId( getLogUserId() );

	if ($edition_id === FALSE) {
		$user_course 			=& getUserCourse( getLogUserId() );
		$user_course_wait 		=& getUserCourseWait( getLogUserId() );
	}
	else {
		$user_edition=getUserEdition( getLogUserId() );
		$user_edition_wait=getUserEditionWait( getLogUserId() );
	}

	if(!empty($catalogues)) {

		// at least one catalogue is assigned to this user
		$cat_courses =& $cat_man->getAllCourseOfUser( getLogUserId() );
		if(!isset($cat_courses[$sel_course])) {

			$GLOBALS['page']->add(
				getErrorUi($lang->def('_THIS_ISNT_IN_POOL')).'</div>',
			'content');
			return;
		}
	}

	if ($edition_id === FALSE) {
		$user_arr=& $user_course;
		$user_wait_arr=& $user_course_wait;
		$user_index=& $sel_course;
		$selling=$course['selling'];
		$subscribe_method=$course['subscribe_method'];

		$edition_field="";
		$edition_val="";
		$edition_param="";
	}
	else {
		$user_arr=& $user_edition;
		$user_wait_arr=& $user_edition_wait;
		$user_index=& $edition_id;
		$selling=$course['selling'];
		$subscribe_method=$course['subscribe_method'];

		$edition_field=" edition_id,";
		$edition_val=" '".$edition_id."',";
		$edition_param="&amp;edition_id=".$edition_id;
	}

	$can_subscribe = false;
	if(isset($user_arr[$user_index])) {

		if(isset($user_wait_arr[$user_index])) {
			$GLOBALS['page']->add(
				getErrorUi($lang->def('_YOU_ARE_WAITING')).'</div>',
			'content');
			return;
		} else {
			$GLOBALS['page']->add(
				getErrorUi($lang->def('_YOU_ARE_ALREDY_SUBSCRIBED')).'</div>',
			'content');
			return;
		}
	} elseif($selling != 1) {

		$waiting = 1;
		switch($subscribe_method) {
			case "0" : {
				$GLOBALS['page']->add(
					getErrorUi($lang->def('_CANNOT_SUBSCRIBE')).'</div>',
				'content');
				return;
			};break;
			case "1" : {
				$write = $lang->def('_MUST_WAIT_FOR_MODERATION', 'course');
				$can_subscribe = true;
			};break;
			case "2" : {
				$write = $lang->def('_DIRECT_SUBSCRIPTION', 'course');
				$can_subscribe = true;
				$waiting = 0;
			};break;
		}
	}
	if(isset($write)) $GLOBALS['page']->add(getResultUi($write), 'content');

	if(isset($_GET['confirm']) || isset($_POST['confirm']) ) {


		if($subscribe_method ==  3) {
			// check if security code is correct
			if($_POST['securtiy_code_insert'] != $course['security_code']) {

				$GLOBALS['page']->add(getErrorUi($lang->def('_SECURITY_CODE_DIDNT_MATCH')), 'content');
				$can_subscribe = false;
			} else {

				$waiting = 0;
				$can_subscribe = true;
			}
		}
		if($selling == 1) {

			//retrive id of group of the course for the varioud level
			$level_idst =& getCourseLevel($sel_course);

			// Add in group for permission
			$acl_man =& $GLOBALS['current_user']->getAclManager();
			$acl_man->addToGroup($level_idst[3], getLogUserId());

			// Add in table
			$re = mysql_query("
			INSERT INTO ".$GLOBALS['prefix_lms']."_courseuser
			( idUser, idCourse,".$edition_field." level, waiting, subscribed_by, date_inscr )
			VALUES
			( '".getLogUserId()."', '".$sel_course."',".$edition_val." '3', '1', '".getLogUserId()."', '".date("Y-m-d H:i:s")."' )	");

			$array_subst = array(	'[url]' => $GLOBALS['lms']['url'],
								'[course]' => $course['name'],
								'[price]' => $course['prize'] );

			require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');
			if($re) {

				// message to user that is waiting
				$msg_composer = new EventMessageComposer('subscribe', 'framework');

				$msg_composer->setSubjectLangText('email', '_NEW_USER_SUBS_BUY_SUBJECT', false);
				$msg_composer->setBodyLangText('email', '_NEW_USER_SUBS_BUY_TEXT', $array_subst);

				$msg_composer->setSubjectLangText('sms', '_NEW_USER_SUBS_BUY_SUBJECT_SMS', false);
				$msg_composer->setBodyLangText('sms', '_NEW_USER_SUBS_BUY_TEXT_SMS', $array_subst);

				// send message to the user subscribed
				createNewAlert(	'UserCourseBuy', 'subscribe', 'insert', '1', 'User buy a course',
							array(getLogUserId()), $msg_composer, true );
			}
			jumpTo('index.php?modname=coursecatalogue&amp;op=courselist&amp;id_course='.$sel_course.'&amp;re='.( $re ? 'ok_buy' : 'err_buy' ));

		} elseif($can_subscribe) {

			//retrive id of group of the course for the varioud level
			$level_idst =& getCourseLevel($sel_course);

			// Add in group for permission
			$acl_man =& $GLOBALS['current_user']->getAclManager();
			$acl_man->addToGroup($level_idst[3], getLogUserId());

			// Add in table
			$re = mysql_query("
			INSERT INTO ".$GLOBALS['prefix_lms']."_courseuser
			( idUser, idCourse,".$edition_field." level, waiting, subscribed_by, date_inscr )
			VALUES
			( '".getLogUserId()."', '".$sel_course."',".$edition_val." '3', '".$waiting."', '".getLogUserId()."', '".date("Y-m-d H:i:s")."' )	");

			$acl_man =& $GLOBALS['current_user']->getAclManager();
			$userinfo = $acl_man->getUser(getLogUserId(), false);

			$array_subst = array(	'[url]' => $GLOBALS['lms']['url'],
									'[course]' => $course['name'],
									'[firstname]' => $userinfo[ACL_INFO_FIRSTNAME] ,
									'[lastname]' => $userinfo[ACL_INFO_LASTNAME]  );

			require_once($GLOBALS['where_framework'].'/lib/lib.eventmanager.php');
			if($re) {

				if($waiting) {

					// message to user that is waiting
					$msg_composer = new EventMessageComposer('subscribe', 'lms');

					$msg_composer->setSubjectLangText('email', '_NEW_USER_SUBS_WAITING_SUBJECT', false);
					$msg_composer->setBodyLangText('email', '_NEW_USER_SUBS_WAITING_TEXT', $array_subst);

					$msg_composer->setSubjectLangText('sms', '_NEW_USER_SUBS_WAITING_SUBJECT_SMS', false);
					$msg_composer->setBodyLangText('sms', '_NEW_USER_SUBS_WAITING_TEXT_SMS', $array_subst);

					// send message to the user subscribed
					/*createNewAlert(	'UserCourseInsertModerate', 'subscribe', 'insert', '1', 'User subscribed with moderation',
								array(getLogUserId()), $msg_composer  );*/
								
					$acl =& $GLOBALS['current_user']->getAcl();
                    $acl_man =& $GLOBALS['current_user']->getAclManager();
					
					$recipients = array();
					
					$idst_group_god_admin = $acl->getGroupST(ADMIN_GROUP_GODADMIN);
					
					$recipients = $acl_man->getGroupMembers($idst_group_god_admin);
					
					$idst_group_admin = $acl->getGroupST(ADMIN_GROUP_ADMIN);
					
					$idst_admin = $acl_man->getGroupMembers($idst_group_admin);
					
					foreach($idst_admin as $id_user)
					{
						$adminManager = new AdminManager();
						$acl_manager = new DoceboACLManager();
						
						$idst_associated = $adminManager->getAdminTree($id_user);
						
						$array_user =& $acl_manager->getAllUsersFromIdst($idst_associated);
								
						$array_user = array_unique($array_user);
						
						$array_user[] = $array_user[0];
						unset($array_user[0]);
						
						$control_user = array_search(getLogUserId(), $array_user);
						
						$query =	"SELECT COUNT(*)"
									." FROM ".$GLOBALS['prefix_fw']."_admin_course"
									." WHERE idst_user = '".$id_user."'"
									." AND type_of_entry = 'course'"
									." AND id_entry = '".$sel_course."'";
						
						list($control_course) = mysql_fetch_row(mysql_query($query));
						
						/*if($control)
							$recipients[] = $id_user;*/
						
						$query =	"SELECT COUNT(*)"
									." FROM ".$GLOBALS['prefix_fw']."_admin_course"
									." WHERE idst_user = '".$id_user."'"
									." AND type_of_entry = 'coursepath'"
									." AND id_entry IN"
									." ("
									." SELECT id_path"
									." FROM ".$GLOBALS['prefix_lms']."_coursepath_courses"
									." WHERE id_item = '".$sel_course."'"
									." )";
						
						list($control_coursepath) = mysql_fetch_row(mysql_query($query));
						
						/*if($control)
							$recipients[] = $id_user;*/
						
						$query =	"SELECT COUNT(*)"
									." FROM ".$GLOBALS['prefix_fw']."_admin_course"
									." WHERE idst_user = '".$id_user."'"
									." AND type_of_entry = 'catalogue'"
									." AND id_entry IN"
									." ("
									." SELECT idCatalogue"
									." FROM ".$GLOBALS['prefix_lms']."_catalogue_entry"
									." WHERE idEntry = '".$sel_course."'"
									." )";
						
						list($control_catalogue) = mysql_fetch_row(mysql_query($query));
						
						if($control_user && ($control_course || $control_coursepath || $control_catalogue))
							$recipients[] = $id_user;
					}
					
					$recipients = array_unique($recipients);
					
					createNewAlert(	'UserCourseInsertModerate', 'subscribe', 'insert', '1', 'User subscribed with moderation',
								$recipients, $msg_composer  );
					
				} else {

					// message to user that is subscribed
					$msg_composer = new EventMessageComposer('subscribe', 'lms');

					$msg_composer->setSubjectLangText('email', '_NEW_USER_SUBSCRIBED_SUBJECT', false);
					$msg_composer->setBodyLangText('email', '_NEW_USER_SUBSCRIBED_TEXT', $array_subst);

					$msg_composer->setSubjectLangText('sms', '_NEW_USER_SUBSCRIBED_SUBJECT_SMS', false);
					$msg_composer->setBodyLangText('sms', '_NEW_USER_SUBSCRIBED_TEXT_SMS', $array_subst);

					// send message to the user subscribed
					createNewAlert(	'UserCourseInserted', 'subscribe', 'insert', '1', 'User subscribed',
								array(getLogUserId()), $msg_composer  );
				}
			}
			// reload permission -----------------------------------------------------------------------
			
			$GLOBALS['current_user']->loadUserSectionST('/lms/course/private/');
			$GLOBALS['current_user']->SaveInSession();
			
			jumpTo('index.php?modname=coursecatalogue&amp;op=courselist&amp;id_course='.$sel_course.'&amp;re='.( $re ? ( $waiting ? 'ok_wait' : 'ok_subs' ) : 'err_subs' ));
		}
	}
	if($course['selling'] == 1) {

		// sure to buy
		$GLOBALS['page']->add(
			'<div class="boxinfo_title">'
			.$lang->def('_ARESURE_BY_COURSE')
			.'</div>'
			.'<div class="boxinfo_container">'
			.'<b>'.$lang->def('_CODE', 'course').' : </b>'.$course['code'].'<br />'
			.'<b>'.$lang->def('_COURSE', 'course').' : </b>'.$course['name'].'<br />'
			.'<b>'.$lang->def('_PRICE_COURSE', 'course').' : </b>'.$course['prize'].'<br />'
			.'<b>'.$lang->def('_DESCRIPTION', 'course').' : </b>'.$course['description']
			.'</div>'
			.'<div class="confirm_container">'
				.'<a href="index.php?modname=coursecatalogue&amp;op=subscribecourse&amp;id='.$sel_course.'&amp;confirm=1" title="'.$lang->def('_CONFIRM').'">'
					.'<img src="'.getPathImage().'standard/confirm.gif" alt="'.$lang->def('_CONFIRM').'" />'.$lang->def('_CONFIRM').'</a>'
				.'<a href="index.php?modname=coursecatalogue&amp;op=courselist" title="'.$lang->def('_UNDO').'">'
					.'<img src="'.getPathImage().'standard/undo.gif" alt="'.$lang->def('_UNDO').'" />'.$lang->def('_UNDO').'</a>'
			.'</div>'
		, 'content');

	} else {

		// sure to subscribe
		// printCoursePanel($course);

		$GLOBALS['page']->add('<div class="boxinfo_title">'.$lang->def('_ARESURE_SUBSCRIBE', 'coursecatalogue').$course['name'].'</div>'
			.'<div class="confirm_container">', 'content');

		if($course['subscribe_method'] == 3) {

			require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

			$GLOBALS['page']->add(
				Form::openForm('cart','index.php?modname=coursecatalogue&amp;op=subscribecourse&amp;id='.$sel_course.$edition_param)
				.Form::getTextfield($lang->def('_COURSE_REQUIRE_SECURITY_CODE'), 'securtiy_code_insert', 'securtiy_code_insert', 255)
				.Form::openButtonSpace()
				.Form::getButton('confirm', 'confirm', $lang->def('_CONFIRM'))
				.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
				.Form::closeButtonSpace()
				.Form::closeForm()
			, 'content');
		} else {
			$GLOBALS['page']->add(
					'<a href="index.php?modname=coursecatalogue&amp;op=subscribecourse&amp;id='.$sel_course.$edition_param.'&amp;confirm=1" title="'.$lang->def('_CONFIRM').'">'
						.'<img src="'.getPathImage().'standard/confirm.gif" alt="'.$lang->def('_CONFIRM').'" />'.$lang->def('_CONFIRM').'</a>'
					.'<a href="index.php?modname=coursecatalogue&amp;op=courselist&amp;id_parent='.$course['idCategory'].'" title="'.$lang->def('_UNDO').'">'
						.'<img src="'.getPathImage().'standard/undo.gif" alt="'.$lang->def('_UNDO').'" />'.$lang->def('_UNDO').'</a>'
			, 'content');
		}
		$GLOBALS['page']->add('</div>', 'content');
	}
	$GLOBALS['page']->add('</div>', 'content');
}


function subscribecoursepath(&$url) {
	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.catalogue.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');

	$lang =& DoceboLanguage::createInstance('course');
	$lang =& DoceboLanguage::createInstance('coursecatalogue');

	$sel_coursepath = importVar('id', true, 0);
	$man_coursepath = new CoursePath_Manager();
	$coursepath = $man_coursepath->getCoursepathInfo($sel_coursepath);

	$page_title = array(
		'index.php?modname=coursecatalogue&amp;op=courselist' => $lang->def('_TITLE_COURSECATALOGUE'),
		$lang->def('_SUBSCRIBE_YOU', 'course').' : '.$coursepath['path_name'] );
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'coursecatalogue' )
		.'<div class="std_block">', 'content');

	$cat_man 		= new Catalogue_Manager();
	$catalogues 	=& $cat_man->getUserAllCatalogueId( getLogUserId() );

	$user_coursepath 		=& getUserCoursepath( getLogUserId() );
	$user_coursepath_wait 	=& getUserCoursepathWait( getLogUserId() );

	if(!empty($catalogues)) {

		// at least one catalogue is assigned to this user
		$cat_courses 		=& $cat_man->getAllCoursepathOfUser( getLogUserId() );
		if(!isset($cat_courses[$sel_coursepath])) {

			$GLOBALS['page']->add(
				getErrorUi($lang->def('_THIS_ISNT_IN_POOL')).'</div>',
			'content');
			return;
		}
	}

	$can_subscribe = false;
	if(isset($user_coursepath[$sel_coursepath])) {

		if(isset($user_coursepath_wait[$sel_coursepath])) {
			$GLOBALS['page']->add(
				getErrorUi($lang->def('_YOU_ARE_WAITING')).'</div>',
			'content');
			return;
		} else {
			$GLOBALS['page']->add(
				getErrorUi($lang->def('_YOU_ARE_ALREDY_SUBSCRIBED')).'</div>',
			'content');
			return;
		}
	} else {

		$waiting = 1;
		switch($coursepath['subscribe_method']) {
			case "0" : {
				$GLOBALS['page']->add(
					getErrorUi($lang->def('_CANNOT_SUBSCRIBE')).'</div>',
				'content');
				return;
			};break;
			case "1" : {
				$write = $lang->def('_MUST_WAIT_FOR_MODERATION_PC', 'course');
				$can_subscribe = true;
			};break;
			case "2" : {
				$write = $lang->def('_DIRECT_SUBSCRIPTION', 'course');
				$can_subscribe = true;
				$waiting = 0;
			};break;
		}
	}
	$GLOBALS['page']->add(getResultUi($write), 'content');

	if(isset($_GET['confirm'])) {
		if($can_subscribe) {

			// Add in table
			$re = mysql_query("
			INSERT INTO ".$GLOBALS['prefix_lms']."_coursepath_user
			( idUser, idPath, waiting, subscribed_by )
			VALUES
			( '".getLogUserId()."', '".$sel_coursepath."', '".$waiting."', '".getLogUserId()."' )	");

			jumpTo('index.php?modname=coursecatalogue&amp;op=courselist&amp;re='.( $re ? ( $waiting ? 'ok_wait' : 'ok' ) : 'err' ));
		}
	} else {

		$GLOBALS['page']->add(
			'<div class="boxinfo_title">'
			.$lang->def('_ARESURE_SUBSCRIBE')
			.'</div>'
			.'<div class="boxinfo_container">'
			.'<span>'.$lang->def('_CODE', 'course').' : </span>'.$coursepath['path_code'].'<br />'
			.'<span>'.$lang->def('_PATHCOURSE', 'course').' : </span>'.$coursepath['path_name'].'<br />'
			.'<span>'.$lang->def('_DESCRIPTION', 'course').' : </span>'.$coursepath['path_descr']
			.'</div>'
			.'<div class="confirm_container">'
				.'<a href="index.php?modname=coursecatalogue&amp;op=subscribecoursepath&amp;id='.$sel_coursepath.'&amp;confirm=1" title="'.$lang->def('_CONFIRM').'">'
					.'<img src="'.getPathImage().'standard/confirm.gif" alt="'.$lang->def('_CONFIRM').'" />'.$lang->def('_CONFIRM').'</a>'
				.'<a href="index.php?modname=coursecatalogue&amp;op=courselist" title="'.$lang->def('_UNDO').'">'
					.'<img src="'.getPathImage().'standard/undo.gif" alt="'.$lang->def('_UNDO').'" />'.$lang->def('_UNDO').'</a>'
			.'</div>'
		, 'content');
	}
	$GLOBALS['page']->add('</div>', 'content');
}

/* =========================================================================== */
/* ==                     ==================================================== */
/* == E-Commerce function ==================================================== */
/* ==                     ==================================================== */
/* =========================================================================== */

function addToCart (&$url) {

	require_once($GLOBALS['where_ecom'].'/lib/lib.cart.php');
	$cart =& Cart::createInstance();

	if(isset($_GET['course_edition'])) {
		$product_code='course_edition';
		$product_type=$product_code;
		$id_item = $_GET['course_edition'];
		$query_sell = "SELECT lce.name,lc.prize,lce.price
		FROM ".$GLOBALS['prefix_lms']."_course as lc
		LEFT JOIN  ".$GLOBALS['prefix_lms']."_course_edition as lce
		ON (lc.idCourse=lce.idCourse)
		WHERE idCourseEdition = '".$_GET['course_edition']."'";

		list($name,$course_price, $edition_price)=mysql_fetch_row(mysql_query($query_sell));

		$price=(empty($edition_price) ? $course_price : $edition_price);
	} else {
		$product_code='course';
		$product_type=$product_code;
		$id_item = $_GET['id'];
		$query_sell = "SELECT name,prize
		FROM ".$GLOBALS['prefix_lms']."_course
		WHERE idCourse = '".$_GET['id']."'";
		list($name,$price)=mysql_fetch_row(mysql_query($query_sell));
	}

	if (!isset($cart->array_item[$product_code.'_'.$id_item])) {
		$cart->addItemToCart($product_code,$id_item, $name, $price,1,$product_type);
	}
	else {
		//$cart->updateQuantityItem($product_code.'_'.$id_item,$cart->array_item[$product_code.'_'.$id_item]['quantity']+1);
	}
	$cart->saveCart();

	jumpTo("index.php?modname=coursecatalogue");
	//courselist();
}

function update_cart(&$url){

	require_once($GLOBALS['where_ecom'].'/lib/lib.cart.php');
	$cart =& Cart::createInstance();

	$go_to_cart=FALSE;

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
	if(($go_to_cart) || (isset($_POST['update_go_cart']))){
		go_cart($url);
	}
	else courselist($url);
}


function go_cart(&$url){
	require_once($GLOBALS['where_ecom'].'/lib/lib.cart.php');

	addCss('style_course_list');

	$cart =& Cart::createInstance();
	$tax_zone_arr=$cart->getTaxZoneDropdownArr();

	$tax_zone=FALSE;
	$sel_tax_zone=FALSE;
	if (isset($_POST["tax_zone"])) {
		$tax_zone=$_POST["tax_zone"];
		$_SESSION["cart_tax_zone"]=$tax_zone;
	}
	else 	if (isset($_SESSION["cart_tax_zone"])) {
		$sel_tax_zone=$_SESSION["cart_tax_zone"];
	}

	if (($tax_zone !== FALSE) || count($tax_zone_arr) == 1) {

		if ($tax_zone === FALSE)
			list($tax_zone)=$tax_zone_arr;

		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		require_once($GLOBALS['where_framework'].'/lib/lib.company.php');
		$form = new Form();
		$company= new CoreCompanyManager();
		$lang =& DoceboLanguage::createInstance('coursecatalogue');
		$out 		=& $GLOBALS['page'];
		$out->setWorkingZone('content');
		$out->add(getTitleArea($lang->def('_TITLE_CART'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');
		$out->add(
		Form::openForm('cart','index.php?modname=coursecatalogue&op=invoice_info'));
		//Form::openForm('cart','index.php?modname=coursecatalogue&op=payment_method_select'));

		$out->add($cart->getCart(FALSE, $tax_zone),'content');


		$company_for_user=$company->getUserCompanies(getLogUserID());
		if ((is_array($company_for_user)) && (count($company_for_user) > 0)){
			$out->add($form->getRadio("<b>".$lang->def('_USE_DATE_ASSOCIATED_COMPANY')."</b>:", 'invoice_associated_company', 'invoice_mode', 'company', true ));
			if ((count($company_for_user))>1){

				foreach ($company_for_user as $key => $value) {
					$company_info=$company->getCompanyInfo($value);
					$id_company=$company_info['company_id'];
					$companies[$id_company]=$company_info['name'];

				}
				$out->add($form->getDropdown($lang->def('_COMPANY_NAME'),'company_to_associate','company_to_associate',$companies));
			} else {
				$company_for_user=$company->getCompanyInfo($company_for_user[0]);
				$out->add('<div class="form_line_l">'.$lang->def('_COMPANY_NAME').$form->getHidden('company_to_associate','company_to_associate',$company_for_user['company_id']));
				$out->add($company_for_user['name'].'</div>');
			}

		}


		$ecom_type = getPLSetting("ecom", "ecom_type", "none");
		if ($ecom_type == "with_buyer") {
			$out->add($form->getRadio("<b>".$lang->def('_USE_CODE_TO_ASSOCIATE')."</b>:", 'invoice_use_code_to_associate', 'invoice_mode', 'company_code')
			.$form->getTextfield($company->getCompanyIdrefCodeName(),'company_code','company_code','255','','','',''));
		}

		$out->add($form->getRadio("<b>".$lang->def('_INSERT_NEW_COMPANY')."</b>", 'new_company', 'invoice_mode', 'new_company')
		.$form->getHidden('tax_zone', 'tax_zone', $_SESSION["cart_tax_zone"]));


		$out->add(Form::getHidden('tax_zone', 'tax_zone', $tax_zone));

		$out->add(Form::openButtonSpace()
		.$form->getButton('company_billing_info', 'company_billing_info', $lang->def('_GO_CART_THREE'))
		.Form::closeButtonSpace()
		.Form::closeForm().'</div>');
	}
	else {
		selectTaxZone($tax_zone_arr, $sel_tax_zone);
	}
}


function selectTaxZone($tax_zone_arr, $sel_tax_zone=FALSE) {

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$out->add(getTitleArea($lang->def('_TITLE_CART'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');
	$out->add(
	Form::openForm('cart','index.php?modname=coursecatalogue&op=invoice_info'));

	$out->add(Form::getDropdown($lang->def('_TAX_ZONE'), 'tax_zone', 'tax_zone', $tax_zone_arr, $sel_tax_zone));

	$out->add(Form::openButtonSpace()
	.Form::getButton('go_cart', 'go_cart', $lang->def('_BUY_COURSE'))
	.Form::closeButtonSpace()
	.Form::closeForm().'</div>');

}


function invoice_info (&$url) {
	/*
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.company.php');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$company= new CoreCompanyManager();
	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$form = new Form();
	$out->add(getTitleArea($lang->def('_TITLE_INVOICE_INFO'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');
	$out->add(
	$form->openForm('invoice_info','index.php?modname=coursecatalogue&op=company_billing_info'));
	$company_for_user=$company->getUserCompanies(getLogUserID());
	if ((is_array($company_for_user)) && (count($company_for_user) > 0)){
		$out->add($form->getRadio($lang->def('_USE_DATE_ASSOCIATED_COMPANY'), 'invoice_associated_company', 'invoice_mode', 'company', true ));
		if ((count($company_for_user))>1){

			foreach ($company_for_user as $key => $value) {
				$company_info=$company->getCompanyInfo($value);
				$id_company=$company_info['company_id'];
				$companies[$id_company]=$company_info['name'];

			}
			$out->add($form->getDropdown($lang->def('_COMPANY_NAME'),'company_to_associate','company_to_associate',$companies));
		} else {
			$company_for_user=$company->getCompanyInfo($company_for_user[0]);
			$out->add('<div class="form_line_l">'.$lang->def('_COMPANY_NAME').$form->getHidden('company_to_associate','company_to_associate',$company_for_user['company_id']));
			$out->add($company_for_user['name'].'</div>');
		}

	}



	$out->add($form->getRadio($lang->def('_USE_CODE_TO_ASSOCIATE'), 'invoice_use_code_to_associate', 'invoice_mode', 'company_code')
	.$form->getTextfield($company->getCompanyIdrefCodeName(),'company_code','company_code','255','','','','')
	.$form->getRadio($lang->def('_INSERT_NEW_COMPANY'), 'new_company', 'invoice_mode', 'new_company')
	.$form->getHidden('tax_zone', 'tax_zone', $_SESSION["cart_tax_zone"])
	.$form->openButtonSpace()
	.$form->getButton('go_cart', 'go_cart', $lang->def('_BACK'))
	.$form->getButton('company_billing_info', 'company_billing_info', $lang->def('_GO_CART_THREE'))
	.$form->closeButtonSpace()
	.$form->closeForm()
	.'</div>'); */
}

function company_billing_info (&$url) {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.company.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_ecom'].'/modules/payment/class.payment.php');

	if (isset($_POST["billing_info_data"])) {
		$billing_info_data =unserialize(urldecode($_POST["billing_info_data"]));
		$_POST =$_POST+$billing_info_data;
	}

	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$company= new CoreCompanyManager();
	$payment= new Payment();
	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$form = new Form();
	$tb	= new TypeOne('', $lang->def('_INVOICE_INFO_CAPTION'), $lang->def('_INVOICE_INFO_SUMMARY'));
	$out->add(getTitleArea($lang->def('_TITLE_INVOICE_INFO'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');
	//$out->add(getBackUi( 'index.php?modname=coursecatalogue&op=invoice_info', $lang->def('_BACK')));
	$out->add($form->openForm('invoice_info','index.php?modname=coursecatalogue&op=invoice_info'));
	//$out->add($form->openForm('invoice_info','index.php?modname=coursecatalogue&op=go_cart'));
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
			jumpTo("index.php?modname=coursecatalogue&op=new_company");
			die();
		}break;
	}
	if ((isset($billing_info)) && ($billing_info !== FALSE)) {
		$type_h = array('','');
		$cont_h	= array('','');
		$tb->setColsStyle($type_h);
		$tb->addHead($cont_h);
		foreach ($billing_info as $key => $value) {

			if(in_array($key,$billing_valid_info)==TRUE ){
				$tb_billing_info=array();
				
				$temp='';
				switch (strtoupper($key)) {
          case 'EMAIL': $temp='_'.strtoupper($key); break;
          case 'ADDRESS': $temp='_'.strtoupper($key); break;
          default: $temp="_COMPANY_INFO_".strtoupper($key); break;        
        }
				$tb_billing_info[]=$lang->def($temp);
				$tb_billing_info[]=$value;
				$out->add($tb->addBody($tb_billing_info));
			}
		}
		$_SESSION["cart_billing_info"]=$billing_info;
	} else  $out->add(getErrorUi($lang->def('_NO_INVOICE_INFO')));


	$out->add($tb->getTable());
	$form = new Form();
	$out->add($form->getHidden('tax_zone', 'tax_zone', $_SESSION["cart_tax_zone"]));
	$out->add($form->getHidden('billing_info_data', 'billing_info_data', urlencode(serialize($_POST))));

	$out->add("<h3>".$lang->def("_SEL_PAY_METHOD").":</h3>");
	$sel =(isset($_POST["paymod"]) ? $_POST["paymod"] : FALSE);
	$out->add($out->add($payment->getFormSelection($sel)),'content');
	$out->add(Form::openButtonSpace()
		.Form::getButton('go_cart', 'go_cart', $lang->def('_BACK'))
		.Form::getButton('confirm_buy', 'confirm_buy', $lang->def('_GO_CART')));

	/* $out->add($form->openButtonSpace()
	.$form->getButton('go_cart', 'go_cart', $lang->def('_BACK'))
	.$form->getButton('payment_method_select', 'payment_method_select', $lang->def('_GO_CART_THREE'))
	// .$form->getButton('go_cart', 'go_cart', $lang->def('_GO_CART')) */
	$out->add($form->closeButtonSpace()
	.$form->closeForm()
	.'</div>');

}

function payment_method_select(&$url) {
	/*require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_ecom'].'/modules/payment/class.payment.php');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$payment= new Payment();
	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$out->add(getTitleArea($lang->def('_TITLE_CART'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');
	$out->add(getBackUi( 'index.php?modname=coursecatalogue&op=invoice_info', $lang->def('_BACK')));
	$out->add("<h3>".$lang->def("_SEL_PAY_METHOD").":</h3>");
	$out->add(
	Form::openForm('payment','index.php?modname=coursecatalogue&op=confirm_buy'));
	$out->add($out->add($payment->getFormSelection()),'content');
	$out->add(Form::openButtonSpace()
	.Form::getButton('invoice_info', 'invoice_info', $lang->def('_BACK'))
	// .Form::getButton('payment_selected', 'payment_selected', $lang->def('_GO_CART'))
	.Form::getButton('confirm_buy', 'confirm_buy', $lang->def('_GO_CART'))
	.Form::closeButtonSpace()
	.Form::closeForm().'</div>');*/
}


function transactionSummary($tax_zone, $paymod, $processed=FALSE, $payment_text="", $order_id=0) {
	$res ="";
	require_once($GLOBALS["where_ecom"]."/lib/lib.cart.php");

	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$cart=& Cart::createInstance();
	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$lang =& DoceboLanguage::createInstance('admin_payaccount', 'ecom');

	$billing_valid_info=array('name','address','tel','email','vat_number');
	$billing_info =$_SESSION["cart_billing_info"];

	$customer_info ="";
	foreach ($billing_info as $key => $value) {
		if(in_array($key,$billing_valid_info)==TRUE ){
			$customer_info.=$value."<br />";
		}
	}

	$seller_info = nl2br(getPLSetting("ecom", "company_details", "none"));

	$pay_method_desc =$lang->def('_ADMIN_PAYACCOUNT_'.$paymod,'admin_payaccount','ecom');

	if ($order_id > 0) {
		$res.='<h2>'.$lang->def("_ORDER_ID").": ".$order_id."</h2>\n";
	}

	$res.='<table class="cart_table_grey" summary="'.$lang->def("_TAB_TRINFO_SUMMARY").'" cellspacing="0">
		<caption>'.$lang->def("_TAB_TRINFO_SUMMARY").'</caption>
		<tbody>
		<tr class="line-col">
		<td class="box_left">'.$seller_info.'</td>
		<td class="box_right">'.$customer_info.'</td>
		</tr>
		<tr class="line-col">
		<td colspan="2" class="box_left"><b>'.$lang->def("_PAYMENT_METHOD")."</b>: ".$pay_method_desc.
		(!empty($payment_text) ? "<br />\n".$payment_text : "").'</td>
		</tr>
		<tr class="line-col">
		<td colspan="2" class="box_left">'.($processed ? $lang->def("_ORDER_PROCESSED") : $lang->def("_ORDER_NOT_PROCESSED")).'</td>
		</tr>
		</tbody>
		</table>';

	$code=$cart->getCart(FALSE, $tax_zone, FALSE, "cart_table_grey");
	$res.=$code;

	return $res;
}


function confirmBuy(&$url) {

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$out->add(getTitleArea($lang->def('_TITLE_CART'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');
	//$out->add(getBackUi( 'index.php?modname=coursecatalogue&op=company_billing_info', $lang->def('_BACK')));
	$out->add("<h3>".$lang->def("_CONFIRM_BUY").":</h3>");

	$tax_zone =$_SESSION["cart_tax_zone"];
	$paymod =$_POST["paymod"];

	$pay_info =getPaymentInfo($paymod);
	$payment_text =$pay_info["payment_text"];

	$out->add(transactionSummary($tax_zone, $paymod, FALSE, $payment_text));

	$out->add(
	Form::openForm('payment','index.php?modname=coursecatalogue&op=payment_selected'));
	$out->add(Form::getHidden('billing_info_data', 'billing_info_data', $_POST["billing_info_data"]));
	$out->add(Form::getHidden('paymod', 'paymod', $paymod));
	$out->add(Form::getHidden('tax_zone', 'tax_zone', $_SESSION["cart_tax_zone"]));
	$out->add(Form::openButtonSpace()
	.Form::getButton('company_billing_info', 'company_billing_info', $lang->def('_BACK'))
	.Form::getButton('back_to_list', 'back_to_list', $lang->def('_CANCEL_BUY'))
	.Form::getButton('payment_selected', 'payment_selected', $lang->def('_CONFIRM_BUY'))
	.Form::closeButtonSpace()
	.Form::closeForm().'</div>');

}


function getPaymentInfo($paymod) {
	require_once($GLOBALS['where_ecom'].'/lib/lib.cart.php');
	require_once($GLOBALS['where_ecom'].'/modules/payment/class.payment.php');
	$cart =& Cart::createInstance();
	$payment= new Payment();
	$lang =& DoceboLanguage::createInstance('payaccount_'.$_POST['paymod'],'ecom');

	$valid_paymod=$payment->getActivePayment();
	if (in_array($_POST['paymod'], $valid_paymod)) {
		require_once($GLOBALS['where_ecom'].'/modules/payment/'.$_POST['paymod'].'.php');
	}

	$payment_text ="";

	$default_payment_status=$payment->getDefaultStatus("payment");
	$default_order_status=$payment->getDefaultStatus("order");

	switch($paymod){

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

	foreach($payment_info as $key => $value ) {
		$payment_text.=$lang->def($key).": ".$value."<br />"; // Form::getLineBox($lang->def($key),$value);
	}

	$res["payment_info"] =$payment_info;
	$res["payment_status"] =$payment_status;
	$res["order_status"] =$order_status;
	$res["payment_text"] =$payment_text;

	return $res;
}


function paymentSelected (&$url) {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_ecom'].'/lib/lib.cart.php');
	require_once($GLOBALS['where_ecom'].'/modules/payment/class.payment.php');
	$payment= new Payment();
	$cart =& Cart::createInstance();
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang =& DoceboLanguage::createInstance('payaccount_'.$_POST['paymod'],'ecom');
	$out->add(getTitleArea($lang->def('_TITLE_CART'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');


	if ($cart->isEmpty()) {
		jumpTo($url->getUrl());
		die();
	}

	$tax_zone =$_SESSION["cart_tax_zone"];
	$paymod =$_POST["paymod"];

	$summary ="";
	$pay_info =getPaymentInfo($paymod);
	$payment_text =$pay_info["payment_text"];
	$total_amount=$cart->getTotalAmount();

	$company_id=$_SESSION["cart_billing_info"]["company_id"];
	$transaction_id =$payment->saveTransaction($company_id, $cart->getTotalAmount(),$pay_info["order_status"],$pay_info["payment_status"],$paymod, $cart->array_item);

	$user_id =getLogUserID();
	foreach ( $cart->array_item as $id_prod => $product_detail) {
		$pt =$product_detail['type'];
		if (($pt == "course") || ($pt == "course_edition")) {
			$product_id =(int)substr($id_prod, strlen($pt)+1);
			saveTransCourse($user_id, $pt, $product_id);
		}
	}

	$summary.='<div class="no_float"></div><hr /><br />';

	$summary.=transactionSummary($tax_zone, $paymod, TRUE, $payment_text, $transaction_id);
	$out->add($summary);
	sendOrderEmail($summary);

	$print_code ='<form method="post" action="'.$url->getUrl().'">';
	$print_code.='<input type="button" value="'.$lang->def("_PRINT").'" onclick="window.print();" />';
	$print_code.='</form>';
	$out->add($print_code);

	$cart->emptyCart();
	$out->add('</div>');
}


function createNewCompany(&$url) {
	checkPerm('view');
	require_once($GLOBALS["where_framework"]."/lib/lib.company.php");
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");

	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$out->add(getTitleArea($lang->def('_TITLE_INVOICE_INFO'), 'cart_summary', $lang->def('_ALT_CART'))	.'<div class="std_block">');

	$cm=new CompanyManager();
	$cca=new CoreCompanyAdmin();

	require_once($GLOBALS["where_framework"]."/lib/lib.urlmanager.php");
	$um =& UrlManager::getInstance();
	$um->setStdQuery("modname=coursecatalogue");

	$out->add($cca->getAddEditForm(0, $cm, array(), "savecompany"));

	$out->add('</div>');
}


function saveCompany(&$url) {
	require_once($GLOBALS["where_crm"]."/modules/company/lib.company.php");
	require_once($GLOBALS['where_framework'].'/lib/lib.company.php');

	require_once($GLOBALS["where_framework"]."/lib/lib.urlmanager.php");
	$um =& UrlManager::getInstance();
	$um->setStdQuery("modname=coursecatalogue");

	$cm=new CompanyManager();
	$ccm=new CoreCompanyManager();

	$company_id=$cm->saveData($_POST);
	$ccm->addToCompanyUsers($company_id, $GLOBALS["current_user"]->getIdSt());

	jumpTo($um->getUrl("op=company_billing_info&invoice_mode=company&company_id=".$company_id));
}

function reserveSelCompany(&$url) {
	checkPerm("view");

	require_once($GLOBALS['where_framework'].'/lib/lib.company.php');

	$course_edition = importVar('course_edition', false, 0);
	$id_c 			= importVar('id', true, 0);
	if($id_c == 0) $id_c = false;

	$res = "";
	$ccm = new CoreCompanyManager();
	$user_companies = $ccm->getUserCompanies($GLOBALS["current_user"]->getIdSt());

	$company_code = "";
	$error = "";
	// perform the action required ---------------------------------------------------
	if(isset($_POST["action"])) {

		switch ($_POST["action"]) {
			case "company_code": {
				$company_code=$_POST["company_code"];
				$company_info=$ccm->getCompanyInfoFromCode($company_code);

				if (($company_info !== FALSE) && (in_array($company_info["company_id"], $user_companies))) {
					$user_companies=array($company_info["company_id"]);
				}
				else {
					$error="_INVALID_COMPANY_CODE";
				}

			} break;
			case "select_company": {
				$company_id=(isset($_POST["company_id"]) ? $_POST["company_id"] : 0);
				if (($company_id > 0) && (in_array($company_id, $user_companies))) {
					$user_companies=array($company_id);
				}
			} break;
		}
	}

	// list the companies ------------------------------------------------------------
	$companies_count = count($user_companies);

	if (($companies_count == 0) || ($companies_count > 1)) {
		require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
		$out 		=& $GLOBALS['page'];
		$out->setWorkingZone('content');
		$lang =& DoceboLanguage::createInstance('coursecatalogue');
		$form=new Form();
		$out->add(getTitleArea($lang->def('_TITLE_INVOICE_INFO'), 'cart_summary', $lang->def('_ALT_CART'))	);
		$out->add('<div class="std_block">');

		if (!empty($error)) {
			$out->add(getErrorUi($lang->def($error)));
		}

		$url ="index.php?modname=coursecatalogue&amp;op=reserve&amp;id=".$id_c;
		$url.=($course_edition > 0 ? "&amp;course_edition=".$course_edition : "");
		$res =$form->openForm("main_form", $url);
		$res.=$form->openElementSpace();
	}


	if ($companies_count == 0) {

		$res.=$form->getFormHeader($lang->def("_PROVIDE_COMPANY_CODE"));
		$res.=$form->getTextfield($lang->def("_COMPANY_CODE"), "company_code", "company_code", 255, $company_code);
		$res.=$form->getHidden("action", "action", "company_code");

	}
	else if ($companies_count == 1) {

		$url ="index.php?modname=coursecatalogue&amp;op=savereservation&amp;id=".$id_c;
		$url.=($course_edition > 0 ? "&amp;course_edition=".$course_edition : "");
		$url.="&amp;company_id=".$user_companies[0];

		jumpTo($url);
	}
	else if ($companies_count > 1) {

		$res.=$form->getFormHeader($lang->def("_SELECT_COMPANY"));

		foreach($user_companies as $company_id) {
			$company_info=$ccm->getCompanyInfo($company_id);
			$res.=$form->getRadio($company_info["name"], "company_id_".$company_id, "company_id", $company_id);
		}
		$res.=$form->getHidden("action", "action", "select_company");
	}


	if (($companies_count == 0) || ($companies_count > 1)) {
		$res.=$form->closeElementSpace();
		$res.=$form->openButtonSpace();
		$res.=$form->getButton('save', 'save', $lang->def("_SAVE"));
		$res.=$form->getButton('undo', 'undo', $lang->def('_UNDO'));
		$res.=$form->closeButtonSpace();
		$res.=$form->closeForm();
		$out->add($res);
		$out->add('</div>');
	}
}


function saveReservation(&$url) {
	checkPerm("view");
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	if ((isset($_GET["company_id"])) && ($_GET["company_id"] > 0))
		$company_id=$_GET["company_id"];
	else
		return FALSE;

	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang =& DoceboLanguage::createInstance('coursecatalogue');
	$form=new Form();
	$out->add(getTitleArea($lang->def('_TITLE_RESERVATION_SAVED'), 'cart_summary', $lang->def('_ALT_CART'))	);
	$out->add('<div class="std_block">');

	$user_id=$GLOBALS["current_user"]->getIdSt();
	$saved=doSaveReservation($company_id, $user_id);

	$out->add($lang->def("_RESERVATION_SAVED"));

	$url="index.php?modname=coursecatalogue&amp;op=back_to_list";
	$res =$form->openForm("main_form", $url);

	$res.=$form->openButtonSpace();
	$res.=$form->getButton('save', 'save', $lang->def("_GO_ON"));
	$res.=$form->closeButtonSpace();
	$res.=$form->closeForm();

	$out->add($res);
	$out->add('</div>');
}



function doSaveReservation($company_id, $user_id) {

	if(isset($_GET['course_edition'])) {
		$product_code='course_edition';
		$type="course_edition";
		$id_item = (int)$_GET['course_edition'];
		$query_sell = "SELECT lce.name,price
		FROM ".$GLOBALS['prefix_lms']."_course as lc
		LEFT JOIN  ".$GLOBALS['prefix_lms']."_course_edition as lce
		ON (lc.idCourse=lce.idCourse)
		WHERE idCourseEdition = '".$_GET['course_edition']."'";

	} else {
		$product_code='course';
		$type="course";
		$id_item = (int)$_GET['id'];
		$query_sell = "SELECT name,prize
		FROM ".$GLOBALS['prefix_lms']."_course
		WHERE idCourse = '".$_GET['id']."'";
	}
	list($name,$price)=mysql_fetch_row(mysql_query($query_sell));

	$product_code.="_".$id_item;

	$table=$GLOBALS["prefix_ecom"]."_reservation";

	$qtxt ="INSERT INTO ".$table." (product_code, company_id, user_id, name, type, price, reservation_date) ";
	$qtxt.="VALUES ('".$product_code."', '".(int)$company_id."', '".(int)$user_id."', ";
	$qtxt.="'".addslashes($name)."', '".$type."', '".$price."', NOW())";

	$q = mysql_query($qtxt);

	// subscription
	if($q) {

		saveTransCourse($user_id, $type, $id_item);
	}

	return $q;
}

function saveTransCourse($user_id, $type, $id_item) {
	// status ??? (overbooking) -------------------------------------------

	require_once($GLOBALS['where_lms'].'/lib/lib.subscribe.php');
	$s_man = new CourseSubscribe_Management();

	if($type == 'course_edition') {

		$id_edition = $id_item;

		$query_course = "
		SELECT idCourse, max_num_subscribe, allow_overbooking
		FROM ".$GLOBALS['prefix_lms']."_course_edition
		WHERE idCourseEdition = '".$id_edition."'";
		list($id_course, $max_num_subscribe, $allow_overbooking) = mysql_fetch_row(mysql_query($query_course));

		$q = $s_man->subscribeEditionUsers(array($user_id), $id_edition, '3', $id_course);

	} else {

		$id_edition = 0;
		$id_course = $id_item;

		$query_course = "
		SELECT max_num_subscribe, allow_overbooking
		FROM ".$GLOBALS['prefix_lms']."_course
		WHERE idCourse = '".$id_course."'";
		list($max_num_subscribe, $allow_overbooking) = mysql_fetch_row(mysql_query($query_course));

		$q = $s_man->subscribeUser($user_id, $id_course, '3');
	}

	list($enrolled) = mysql_fetch_row(mysql_query(""
		." SELECT COUNT(*)"
		." FROM ".$GLOBALS['prefix_lms']."_courseuser AS u"
		." WHERE u.idCourse = '".$id_course."' "
		."		AND u.edition_id = '".$id_edition."' "
		." AND u.level = '3'"
		." AND u.status IN ('"._CUS_CONFIRMED."', '"._CUS_SUBSCRIBED."', '"._CUS_BEGIN."', '"._CUS_END."', '"._CUS_SUSPEND."', '"._CUS_WAITING_LIST."')"
		." AND u.absent = '0' "));

	if($max_num_subscribe != 0 && $enrolled >= $max_num_subscribe) {
		$prenote_status = _CUS_WAITING_LIST;
	} else {
		$prenote_status = _CUS_CONFIRMED;
	}

	// change status --------------------------------------------------
	$q_status = "
	UPDATE ".$GLOBALS['prefix_lms']."_courseuser
	SET status = '".$prenote_status."', waiting = 1
	WHERE idUser = '".$user_id."'
		AND idCourse = '".$id_course."'
		AND edition_id = '".$id_edition."' ";
	return mysql_query($q_status);
}


function sendOrderEmail($summary) {
	$lang =& DoceboLanguage::createInstance('cart', 'ecom');

	$acl_man =$GLOBALS["current_user"]->getAclManager();
	$user_info =$acl_man->getUser(getLogUserID(), FALSE);

	//$nl ="\n";
	$customer_email =$user_info[ACL_INFO_EMAIL];
	$send_order_email = nl2br(getPLSetting("ecom", "send_order_email", "none"));

	// To send HTML mail, the Content-type header must be set
	/*$headers  = 'MIME-Version: 1.0' . $nl;
	$headers .= 'Content-type: text/html; charset=utf-8'. $nl;*/

	$filename = getPathTemplate('ecom')."style/style_ecom.css";
	$handle = fopen($filename, "r");
	$css = fread($handle, filesize($filename));
	fclose($handle);
	$msg ="<style>".$css."</style>";
	$msg.=$summary;

	// Additional headers
	//$headers .= 'From: ' . $send_order_email . $nl;

	require_once($GLOBALS['where_framework'].'/lib/lib.mailer.php');
	$mailer = DoceboMailer::getInstance();
	
	// Mail to seller
	//mail($send_order_email, $lang->def("_EMAIL_SUBJECT_NEW_ORDER"), $msg, $headers);
	$mailer->SendMail($send_order_email, $send_order_email, 
				$lang->def("_EMAIL_SUBJECT_NEW_ORDER"), $msg, false, 
				array(MAIL_REPLYTO => $send_order_email, MAIL_SENDER_ACLNAME => false));
	
	// Mail to customer
	//mail($customer_email, $lang->def("_EMAIL_SUBJECT_ORDER_CONF"), $msg, $headers);
	$mailer->SendMail($send_order_email, $customer_email, 
				$lang->def("_EMAIL_SUBJECT_ORDER_CONF"), $msg, false, 
				array(MAIL_REPLYTO => $send_order_email, MAIL_SENDER_ACLNAME => false));
}



// Course catalogue function dispatcher -----------------------------------------------------

function coursecatalogueDispatch($op) {

	require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
	$url =& UrlManager::getInstance('coursecatalogue');
	$url->setStdQuery('modname=coursecatalogue&op=coursecatalogue');

	addCss("style_ecom", "ecom");
	addCss("style_course_list");

	if(isset($_POST['go_cart'])) $op = 'go_cart';
	if(isset($_POST['update_go_cart'])) $op = 'update_go_cart';
	if(isset($_POST['company_billing_info'])) $op = 'company_billing_info';
	if(isset($_POST['invoice_info'])) $op = 'invoice_info';
	if(isset($_POST['back_to_list'])) $op = 'back_to_list';
	if(isset($_POST['del_item'])) $op = 'update_cart';
	if(isset($_POST['confirm_buy'])) $op = 'confirm_buy';
	if(isset($_POST['empty_cart'])) $op = 'empty_cart';

	switch($op) {
		case "coursedetails" : {
			coursedetails($url);
		};break;
		case "subscribecourse" : {
			subscribecourse($url);
		};break;
		case "subscribecoursepath" : {
			subscribecoursepath($url);
		};break;
		case "showdemo" : {
			showdemo($url);
		};break;
		case "donwloadmaterials" : {
			donwloadmaterials($url);
		};break;
		case "showprofile" : {
			showprofile($url);
		};break;

		case "addToCart" : {
			addToCart($url);
		};break;
		case "update_cart" ; {
			update_cart($url);
		};break;
		case "go_cart" ; {
			go_cart($url);
		};break;
		case "update_go_cart" ; {
			update_cart($url);
		};break;
		case "invoice_info" ; {
			invoice_info($url);
		};break;
		case "company_billing_info" ; {
			company_billing_info($url);
		};break;
		case "payment_method_select" ; {
			payment_method_select($url);
		};break;
		case "payment_selected" ; {
			paymentSelected($url);
		};break;
		case "confirm_buy" : {
			confirmBuy($url);
		};break;
		case "new_company": {
			createNewCompany($url);
		} break;
		case "savecompany": {
			saveCompany($url);
		} break;
		case "back_to_list": {
			courselist($url);
		} break;
		case "reserve": {
			reserveSelCompany($url);
		} break;
		case "savereservation": {
			saveReservation($url);
		} break;
		case "editiondetails" : {
			editionDetails($url);
		};break;
		case "empty_cart": {
			require_once($GLOBALS['where_ecom'].'/lib/lib.cart.php');
			$cart =& Cart::createInstance();
			$cart->emptyCart();
			jumpTo($url->getUrl());
		} break;
		default : {
			courselist($url);
		}
	}
}

?>