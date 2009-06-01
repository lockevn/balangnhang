<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System						 */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*																		 */
/* Copyright(c) 2004													 */
/* Fabio Pirovano (gishell@tiscali.it)									 */
/*                                                                       */
/* http://www.spaghettilearning.com										 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if(!$GLOBALS['current_user']->isAnonymous()) {

// XXX: addpage
function addpage($object_page) {
	checkPerm('view', false, 'storage');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('htmlpage');
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECT_PAGE'), 'htmlpage')
		.'<div class="std_block">'
		.getBackUi( ereg_replace('&', '&amp;', $object_page->back_url).'&amp;create_result=0', $lang->def('_BACK') )
		
		.Form::openForm('pageform', 'index.php?modname=htmlpage&amp;op=inspage')
		.Form::openElementSpace()
		.Form::getHidden('back_url', 'back_url', htmlentities(urlencode($object_page->back_url)) )
		
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 150, $lang->def('_TITLE') )
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof', $lang->def('_TEXTOF'))
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('addhtmlpage', 'addhtmlpage', $lang->def('_INSERT'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

// XXX:inspage
function inspage() {
	checkPerm('view', false, 'storage');
	
	$back_url = urldecode($_POST['back_url']);
	
	$insert_query = "
	INSERT INTO ".$GLOBALS['prefix_lms']."_htmlpage
	SET title = '".( (trim($_POST['title']) == '') ? def('_NOTITLE', 'htmlpage', 'lms') : $_POST['title'] )."',
		textof = '".$_POST['textof']."',
		author = '".(int)getLogUserId()."'";
	if(!mysql_query($insert_query)) {
		
		$_SESSION['last_error'] = def('_ERR_SAVE', 'htmlpage', 'lms');
		jumpTo( $back_url.'&create_result=0' );
	}
	list($idPage) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
	jumpTo( $back_url.'&id_lo='.$idPage.'&create_result=1' );
}

// XXX: modpage
function modpage( $object_page ) {
	checkPerm('view', false, 'storage');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('htmlpage');
	
	//retriving info
	list($title, $textof) = mysql_fetch_row(mysql_query("
	SELECT title, textof 
	FROM ".$GLOBALS['prefix_lms']."_htmlpage 
	WHERE idPage = '".$object_page->getId()."'"));
	
	$GLOBALS['page']->add(getTitleArea($lang->def('_SECT_PAGE'), 'htmlpage')
		.'<div class="std_block">'
		.getBackUi( ereg_replace('&', '&amp;', $object_page->back_url).'&amp;mod_result=0', $lang->def('_BACK') )
		
		.Form::openForm('pageform', 'index.php?modname=htmlpage&amp;op=uppage')
		.Form::openElementSpace()
		.Form::getHidden('idPage', 'idPage', $object_page->getId())
		.Form::getHidden('back_url', 'back_url', htmlentities(urlencode($object_page->back_url)))
		
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 150, $title)
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof', $textof)
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('addhtmlpage', 'addhtmlpage', $lang->def('_SAVE'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

// XXX:uppage
function uppage() {
	checkPerm('view', false, 'storage');
	
	$back_url = urldecode($_POST['back_url']);
	
	$insert_query = "
	UPDATE ".$GLOBALS['prefix_lms']."_htmlpage
	SET title = '".( (trim($_POST['title']) == '') ? def('_NOTITLE', 'htmlpage', 'lms') : $_POST['title'] )."',
		textof = '".$_POST['textof']."'
	WHERE idPage = '".(int)$_POST['idPage']."'";
	if(!mysql_query($insert_query)) {
		
		$_SESSION['last_error'] = def('_ERR_SAVE', 'htmlpage', 'lms');
		jumpTo( $back_url.'&mod_result=0' );
	}

	require_once($GLOBALS['where_lms'].'/class.module/track.object.php');
	Track_Object::updateObjectTitle($_POST['idPage'], 'htmlpage', $_POST['title']);
	
	jumpTo( $back_url.'&id_lo='.$_POST['idPage'].'&mod_result=1' );
}

// XXX: switch
switch($GLOBALS['op']) {
	case "inspage" : {
		inspage();
	};break;
	case "uppage" : {
		uppage();
	};break;
}

}

?>