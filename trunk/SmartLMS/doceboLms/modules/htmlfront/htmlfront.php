<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System								*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous()) die('You cannot access as anonymous');

function showhtml() {
	checkPerm('view');
	
	$lang =& DoceboLanguage::createInstance('htmlfront', 'lms');
	
	$query = "
	SELECT textof
	FROM ".$GLOBALS['prefix_lms']."_htmlfront 
	WHERE id_course = '".$_SESSION['idCourse']."'";
	$re_htmlfront = mysql_query($query);
	list($textof) = mysql_fetch_row($re_htmlfront);
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_HTMLFRONT'), 'htmlfront')
		.'<div class="std_block">'
		.( isset($_GET['saveok']) 
			? getResultUi($lang->def('_SAVE_CORRECT'))
			: '' )
		.'<div class="htmlfront_container">'
		.$textof
		.'</div>'
		
		.( checkPerm('mod', true) 
			? '<p class="mod_container">'
				.'<a class="infomod" href="index.php?modname=htmlfront&amp;op=edithtml" title="'.$lang->def('_EDIT_HTML_TITLE').'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MODINFOIMG').'" />&nbsp;'
				.$lang->def('_EDIT_HTML').'</a></p>'
			: '' )
		.'</div>', 'content');
}

function edithtml() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$query = "
	SELECT textof
	FROM ".$GLOBALS['prefix_lms']."_htmlfront 
	WHERE id_course = '".$_SESSION['idCourse']."'";
	$re_htmlfront = mysql_query($query);
	
	$error = false;
	if(isset($_POST['save'])) {
		
		if(mysql_num_rows($re_htmlfront) > 0) {
			
			$upd_query = "
			UPDATE ".$GLOBALS['prefix_lms']."_htmlfront 
			SET textof = '".$_POST['description']."'
			WHERE id_course = '".$_SESSION['idCourse']."'";
			$re = mysql_query($upd_query);
		} else {
			
			$ins_query = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_htmlfront 
			( id_course, textof) VALUES 
			( 	'".$_SESSION['idCourse']."',
				'".$_POST['description']."' )";
			$re = mysql_query($ins_query);
		}
		if($re) jumpTo('index.php?modname=htmlfront&amp;op=showhtml&amp;saveok=1');
		else $error = true;
	}
	
	$lang =& DoceboLanguage::createInstance('htmlfront', 'lms');
	
	list($textof) = mysql_fetch_row($re_htmlfront);
	
	$title_page = array(
		'index.php?modname=htmlfront&amp;op=showhtml' => $lang->def('_HTMLFRONT'), 
		$lang->def('_EDIT_PAGE')
	);
	$GLOBALS['page']->add(
		getTitleArea($title_page, 'htmlfront')
		.'<div class="std_block">'
		.getBackUi( 'index.php?modname=htmlfront&amp;op=showhtml', $lang->def('_BACK') )
		.( $error 
			? getErrorUi($lang->def('_ERROR_IN_SAVE'))
			: '' )
		.Form::openForm('formnotes', 'index.php?modname=htmlfront&amp;op=edithtml')
		.Form::openElementSpace()
		.Form::getTextarea($lang->def('_TEXTOF'), 'description', 'description', 
			importVar('description', false, $textof) )
		.Form::closeElementSpace()
		
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

// dispatch function ================================================== //

function htmlfrontDispatch($op) {
	
	if(isset($_POST['undo'])) $op= 'showhtml';
	
	switch($op) {
		case "showhtml" : showhtml(); break;
		case "edithtml" : edithtml(); break;
	}
}

?>