<?php

/*************************************************************************/
/* DOCEBO LMS - E-Learning System                                        */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Fabio Pirovano (gishell@tiscali.it)             */
/* http://www.spaghettilearning.com                                      */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if(!$GLOBALS['current_user']->isAnonymous()) {

// XXX: additem
function additem( $object_item ) {
	checkPerm('view', false, 'storage');
	$lang =& DoceboLanguage::createInstance('item');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECTIONNAME_ITEM'), 'item')
		.'<div class="std_block">'
		.getBackUi( ereg_replace('&', '&amp;', $object_item->back_url).'&amp;create_result=0', $lang->def('_BACK') )
		
		.Form::openForm('itemform', 'index.php?modname=item&amp;op=insitem', 'std_form', 'post', 'multipart/form-data')
		.Form::openElementSpace()
		
		.Form::getHidden('back_url', 'back_url', htmlentities(urlencode($object_item->back_url)))
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 100, $lang->def('_TITLE'))
		.Form::getFilefield($lang->def('_FILE'), 'file', 'attach')
		
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $lang->def('_DESCRIPTION'))
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('additem', 'additem', $lang->def('_INSERT'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function insitem() {
	checkPerm('view', false, 'storage');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	
	$back_url = urldecode($_POST['back_url']);
	
	//scanning title
	if(trim($_POST['title']) == "") $_POST['title'] = def('_NOTITLE');
	
	//save file
	if($_FILES['attach']['name'] == '') {
		
		$_SESSION['last_error'] = def('_FILEUNSPECIFIED');
		jumpTo( $back_url.'&create_result=0' );
	} else {
		
		$quota = $GLOBALS['course_descriptor']->getQuotaLimit();
		$used = $GLOBALS['course_descriptor']->getUsedSpace();
		
		if(fileExceedQuota($_FILES['attach']['tmp_name'], $quota, $used)) {
				
			$_SESSION['last_error'] = def('_QUOTA_EXCEDED');
			jumpTo( $back_url.'&create_result=0' );
		}
		$path = '/doceboLms/'.$GLOBALS['lms']['pathlesson'];
		$savefile = $_SESSION['idCourse'].'_'.mt_rand(0,100).'_'.time().'_'.$_FILES['attach']['name'];
		if(!file_exists( $GLOBALS['where_files_relative'].$path.$savefile )) {
			sl_open_fileoperations();
			if(!sl_upload($_FILES['attach']['tmp_name'], $path.$savefile)) {
				sl_close_fileoperations();
				$_SESSION['last_error'] = def('_ERRORUPLOAD');
				jumpTo( $back_url.'&create_result=0' );
			}
			sl_close_fileoperations();
		} else {
			$_SESSION['last_error'] = def('_ERRORUPLOAD');
			jumpTo( $back_url.'&create_result=0' );
		}
	}
	
	$insert_query = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_materials_lesson 
	SET author = '".getLogUserId()."',
		title = '".$_POST['title']."',
		description = '".$_POST['description']."',
		path = '$savefile'";
	
	if(!mysql_query($insert_query)) {
		sl_unlink($GLOBALS['prefix_lms'].$savefile );
		$_SESSION['last_error'] = def('_ERR_SAVE');
		jumpTo( $back_url.'&create_result=0' );
	}
	$GLOBALS['course_descriptor']->addFileToUsedSpace($GLOBALS['where_files_relative'].$path.$savefile);
	list($idLesson) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	jumpTo( $back_url.'&id_lo='.$idLesson.'&create_result=1' );
}

//= XXX: edit=====================================================================

function moditem( $object_item ) {
	checkPerm('view', false, 'storage');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('item');
	
	$back_coded = htmlentities(urlencode( $object_item->back_url ));
	
	list($title, $description) = mysql_fetch_row(mysql_query("
	SELECT title, description 
	FROM ".$GLOBALS['prefix_lms']."_materials_lesson 
	WHERE author = ".getLogUserId()." AND idLesson = '".$object_item->getId()."'"));
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECTIONNAME_ITEM'), 'item')
		.'<div class="std_block">'
		.getBackUi( ereg_replace('&', '&amp;', $object_item->back_url).'&amp;mod_result=0', $lang->def('_BACK') )
		
		
		.Form::openForm('itemform', 'index.php?modname=item&amp;op=upitem', 'std_form', 'post', 'multipart/form-data')
		.Form::openElementSpace()
		
		.Form::getHidden('idItem', 'idItem', $object_item->getId())
		.Form::getHidden('back_url', 'back_url', htmlentities(urlencode($object_item->back_url)))
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 100, $title)
		.Form::getFilefield($lang->def('_FILE_MOD'), 'file', 'attach')
		
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $description)
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('additem', 'additem', $lang->def('_SAVE'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function upitem() {
	checkPerm('view', false, 'storage');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php' );
	
	$back_url = urldecode($_POST['back_url']);
	
	//scanning title
	if(trim($_POST['title']) == "") $_POST['title'] = def('_NOTITLE', 'item', 'lms');
	
	//save file
	if($_FILES['attach']['name'] != '') {
		
		$path = '/doceboLms/'.$GLOBALS['lms']['pathlesson'];
		
		// retrive and delte ld file --------------------------------------------------
		
		list($old_file) = mysql_fetch_row(mysql_query("
		SELECT path 
		FROM ".$GLOBALS['prefix_lms']."_materials_lesson 
		WHERE idLesson = '".(int)$_POST['idItem']."'"));
		
		$size = getFileSize($GLOBALS['where_files_relative'].$path.$old_file);
		if(!sl_unlink( $path.$old_file )) {
			
			sl_close_fileoperations();
			$_SESSION['last_error'] = def('_ERRDELFILE', 'item', 'lms');
			jumpTo($back_url.'&id_lo='.(int)$_POST['idItem'].'&mod_result=0' );
		}
		$GLOBALS['course_descriptor']->subFileToUsedSpace(false, $size);
		
		// control course quota ---------------------------------------------------

		$quota = $GLOBALS['course_descriptor']->getQuotaLimit();
		$used = $GLOBALS['course_descriptor']->getUsedSpace();
		
		if(fileExceedQuota($_FILES['attach']['tmp_name'], $quota, $used)) {
				
			$_SESSION['last_error'] = def('_QUOTA_EXCEDED');
			jumpTo( $back_url.'&create_result=0' );
		}
				
		// save new file ------------------------------------------------------------
		
		sl_open_fileoperations();
		$savefile = $_SESSION['idCourse'].'_'.mt_rand(0,100).'_'.time().'_'.$_FILES['attach']['name'];
		if(!file_exists($GLOBALS['where_files_relative'].$path.$savefile )) {
			if(!sl_upload($_FILES['attach']['tmp_name'], $path.$savefile)) {
				
				sl_close_fileoperations();
				$_SESSION['last_error'] = def('_ERRORUPLOAD', 'item', 'lms');
				jumpTo($back_url.'&id_lo='.(int)$_POST['idItem'].'&mod_result=0' );
			}
			sl_close_fileoperations();
		} else {
			
			$_SESSION['last_error'] = def('_ERRORUPLOAD', 'item', 'lms');
			jumpTo($back_url.'&id_lo='.(int)$_POST['idItem'].'&mod_result=0');
		}
		$new_file = ", path = '".$savefile."'";
	}
	
	$insert_query = "
	UPDATE ".$GLOBALS['prefix_lms']."_materials_lesson 
	SET author = '".getLogUserId()."',
		title = '".$_POST['title']."',
		description = '".$_POST['description']."'
		$new_file
	WHERE idLesson = '".(int)$_POST['idItem']."'";
	
	if(!mysql_query($insert_query)) {
		sl_unlink($path.$savefile);
		$_SESSION['last_error'] = def('_ERR_SAVE', 'item', 'lms');
		jumpTo($back_url.'&id_lo='.(int)$_POST['idItem'].'&mod_result=0');
	}
	$GLOBALS['course_descriptor']->addFileToUsedSpace($GLOBALS['where_files_relative'].$path.$savefile);
	
	require_once($GLOBALS['where_lms'].'/class.module/track.object.php');
	Track_Object::updateObjectTitle($_POST['idItem'], 'item', $_POST['title']);
	
	jumpTo($back_url.'&id_lo='.(int)$_POST['idItem'].'&mod_result=1');
}

//= XXX: switch===================================================================
switch($GLOBALS['op']) {
	
	case "insitem" : {
		insitem();
	};break;
	
	case "upitem" : {
		upitem();
	};break;
}

}

?>