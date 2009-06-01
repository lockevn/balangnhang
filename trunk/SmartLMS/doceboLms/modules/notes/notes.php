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

if(!$GLOBALS['current_user']->isAnonymous()) {

function notes() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');
	$lang =& DoceboLanguage::createInstance('notes', 'lms');
	
	$nav_bar = new NavBar('ini', $GLOBALS['lms']['visuItem'], 0 );
	
	$ini = $nav_bar->getSelectedElement();
	$ord = importVar( 'ord' );
	$inv = importVar( 'inv' );

	switch($ord) {
		case "tit" : {
			$ord = $order = 'title';
			if( $inv != 'y' ) $a_down = '&amp;inv=y';
			else {
				$order .= ' DESC';
				$a_down = '';
			}
		};break;
		default : {
			$ord = $order = 'data';
			if( $inv == 'y' ) $a_down = '';
			else {
				$order .= ' DESC';
				$a_down = '&amp;inv=y';
			}
		}
	}
	
	$reNotes = mysql_query("
	SELECT idNotes, data, title 
	FROM ".$GLOBALS['prefix_lms']."_notes 
	WHERE owner ='".getLogUserId()."' AND idCourse='".$_SESSION['idCourse']."' 
	ORDER BY $order 
	LIMIT $ini,".$GLOBALS['lms']['visuItem']);
	
	
	list($num_notes) = mysql_fetch_row(mysql_query("SELECT COUNT(*) 
	FROM ".$GLOBALS['prefix_lms']."_notes 
	WHERE owner ='".getLogUserId()."' AND idCourse='".$_SESSION['idCourse']."' "));
	$nav_bar->setElementTotal($num_notes);
	
	$img_up = '<img src="'.getPathImage().'standard/ord_asc.gif" alt="'.$lang->def('_UP').'"/>';
	$img_down = '<img src="'.getPathImage().'standard/ord_desc.gif" alt="'.$lang->def('_DOWN').'"/>';
	$tb = new typeOne(	$GLOBALS['lms']['visuItem'], 
						$lang->def('_NOTES_CAPTION'), 
						$lang->def('_NOTES_SUMMARY') );
	
	$contentH = array(
		( $ord == 'data' ? ( $inv == 'y' ? $img_up : $img_down ) : '' )
			.'<a href="index.php?modname=notes&amp;op=notes'.$a_down.'">'.$lang->def('_WHEN').'</a>',
		( $ord == 'title' ? ( $inv == 'y' ? $img_up : $img_down ) : ''  )
			.'<a href="index.php?modname=notes&amp;op=notes&amp;ord=tit'.$a_down.'">'.$lang->def('_TITLE').'</a>',
		'<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_MODT').'" alt="'.$lang->def('_MOD').'" />', 
		'<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_DEL').'" alt="'.$lang->def('_DEL').'" />'
	);	
	$typeH = array('colum_width_date', '', 'image', 'image');
	$tb->setColsStyle($typeH);
	$tb->addHead($contentH);
	while(list( $idNotes, $data, $title ) = mysql_fetch_row($reNotes)) {
		
		$content = array(
			$GLOBALS['regset']->databaseToRegional($data), 
			'<a href="index.php?modname=notes&amp;op=displaynotes&amp;idNotes='.$idNotes.'" title="'.$lang->def('_MORET').'">'.$title.'</a>',
			'<a href="index.php?modname=notes&amp;op=modnotes&amp;idNotes='.$idNotes.'">
				<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_MODT').'" alt="'.$lang->def('_MOD').'" /></a>', 
			'<a id="delnotes_'.$idNotes.'"'
				.' href="index.php?modname=notes&amp;op=delnotes&amp;idNotes='.$idNotes.'"'
				.' title="'.$lang->def('_TITLE').' : '.strip_tags(str_replace(array('"',"'"),'',$title)).'">
				<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" /></a>' );
		$tb->addBody($content);
	}
	$tb->addActionAdd(
		'<a href="index.php?modname=notes&amp;op=addnotes">'
		.'<img src="'.getPathImage().'standard/add.gif" title="'.$lang->def('_ADDT').'" alt="'.$lang->def('_ADD').'" /> '
		.$lang->def('_ADD_NOTES').'</a>'
	);
	$GLOBALS['page']->add(
		getTitleArea(array($lang->def('_NOTES')), 'notes')
		.'<div class="std_block">', 'content');
	if(isset($_POST['result'])) {
		switch($_POST['result']) {
			case "ok" 	: $GLOBALS['page']->add( getResultUi($lang->def('_OPERATION_SUCCESSFUL')), 'content');
			case "err" 	: $GLOBALS['page']->add( getErrorUi($lang->def('_OPERATION_FAILURE')), 'content');
		}
	}
	$GLOBALS['page']->add(
		$tb->getTable()
		.$nav_bar->getNavBar($ini)
	, 'content');
		
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delnotes]');
	
	$GLOBALS['page']->add('</div>', 'content');
}

function displaynotes() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.typeone.php');
	$lang =& DoceboLanguage::createInstance('notes', 'lms');
	
	list($data, $title, $textof) = mysql_fetch_row(mysql_query("
	SELECT data,title,textof 
	FROM ".$GLOBALS['prefix_lms']."_notes 
	WHERE idNotes='".$_GET['idNotes']."' AND owner ='".getLogUserid()."' and idCourse='".$_SESSION['idCourse']."'"));
	
	$page_title = array(
		'index.php?modname=notes&amp;op=notes' => $lang->def('_NOTES'), 
		$title
	);
	$GLOBALS['page']->add(
		getTitleArea($page_title, 'notes')
		.'<div class="std_block">'
		.getBackUi( 'index.php?modname=notes&amp;op=notes', $lang->def('_BACK') )
		
		.'<div class="boxinfo_title">'.$title.'</div>'
		.'<div class="boxinfo_container">'
		.$GLOBALS['regset']->databaseToRegional($data).'<br /><br />'
		.'<b>'.$lang->def('_TEXTOF').'</b><br />'.$textof.'</div>'
		
		.getBackUi( 'index.php?modname=notes&amp;op=notes', $lang->def('_BACK') )
		.'</div>', 'content');
}

function addnotes() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('notes', 'lms');
	
	$title_page = array(
		'index.php?modname=notes&amp;op=notes' => $lang->def('_NOTES'), 
		$lang->def('_ADD_NOTES')
	);
	$GLOBALS['page']->add(
		getTitleArea($title_page, 'notes')
		.'<div class="std_block">'
		.getBackUi( 'index.php?modname=notes&amp;op=notes', $lang->def('_BACK') )
		
		.Form::openForm('formnotes', 'index.php?modname=notes&amp;op=insnotes')
		.Form::openElementSpace()
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $lang->def('_NOTITLE'))
		.Form::getTextarea($lang->def('_TEXTOF'), 'description', 'description')
		.Form::closeElementSpace()
		
		.Form::openButtonSpace()
		.Form::getButton('insert', 'insert', $lang->def('_INSERT'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function insnotes() {
	checkPerm('view');
	
	$lang =& DoceboLanguage::createInstance('notes', 'lms');
	
	if(isset($_POST['undo'])) jumpTo( 'index.php?modname=notes&op=notes');
	if($_POST['title'] == '') $_POST['title'] = $lang->def('_NOTITLE');
	
	$query_ins = "
	INSERT INTO ".$GLOBALS ['prefix_lms']."_notes 
	SET owner = '".getLogUserId()."',
		idCourse = '".(int)$_SESSION['idCourse']."',
		data = '".date("Y-m-d H:i:s")."',
		title = '".$_POST['title']."',
		textof = '".$_POST['description']."'";
	
	if(!mysql_query($query_ins)) jumpTo( 'index.php?modname=notes&op=notes&amp;result=err');
	jumpTo( 'index.php?modname=notes&op=notes&amp;result=ok');
}

function modnotes() {
	checkPerm('view');
	
	list($title, $textof) = mysql_fetch_row(mysql_query("
	SELECT title, textof 
	FROM ".$GLOBALS['prefix_lms']."_notes 
	WHERE  idNotes = '".$_GET['idNotes']."'  AND owner ='".getLogUserId()."' AND idCourse='".$_SESSION['idCourse']."'"));
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	$lang =& DoceboLanguage::createInstance('notes', 'lms');
	
	$page_title = array(
		'index.php?modname=notes&amp;op=notes' => $lang->def('_NOTES'),
		$lang->def('_MOd_NOTES')
	);
	
	$GLOBALS['page']->add(
		getTitleArea(array(), 'notes')
		.'<div class="std_block">'
		.getBackUi( 'index.php?modname=notes&amp;op=notes', $lang->def('_BACK') )
		
		.Form::openForm('formnotes', 'index.php?modname=notes&amp;op=upnotes')
		.Form::openElementSpace()
		.Form::getHidden('idNotes', 'idNotes', $_GET['idNotes'])
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $title)
		.Form::getTextarea($lang->def('_TEXTOF'), 'description', 'description', $textof)
		.Form::closeElementSpace()
		
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		
		.Form::closeForm()
		.'</div>', 'content' );
}

function upnotes() {
	checkPerm('view');
	$lang =& DoceboLanguage::createInstance('notes', 'lms');
	
	if(isset($_POST['undo'])) jumpTo( 'index.php?modname=notes&op=notes');
	if($_POST['title'] == '') $_POST['title'] = $lang->def('_NOTITLE');
	
	$query_ins = "
	UPDATE ".$GLOBALS['prefix_lms']."_notes 
	SET data = '".date("Y-m-d H:i:s")."',
		title = '".$_POST['title']."',
		textof = '".$_POST['description']."'
	WHERE idNotes = '".(int)$_POST['idNotes']."' AND owner = '".(int)getLogUserId()."'";
	
	if(!mysql_query($query_ins)) jumpTo( 'index.php?modname=notes&op=notes&amp;result=err');
	jumpTo( 'index.php?modname=notes&op=notes&amp;result=ok');
}

function delnotes() {
	checkPerm('view');
	$lang =& DoceboLanguage::createInstance('notes', 'lms');
	
	if( isset($_GET['confirm']) ) {
		
		$query = "
		DELETE FROM ".$GLOBALS['prefix_lms']."_notes
		WHERE idNotes='".$_GET['idNotes']."' AND owner='".getLogUserId()."' AND idCourse='".$_SESSION['idCourse']."'";
		if(!mysql_query($query)) jumpTo( 'index.php?modname=notes&op=notes&amp;result=err');
		jumpTo( 'index.php?modname=notes&op=notes&amp;result=ok');
	}
	else {
		
		list($title) = mysql_fetch_row(mysql_query("
		SELECT title
		FROM ".$GLOBALS['prefix_lms']."_notes 
		WHERE owner = '".getLogUserId()."' AND idNotes = '".(int)$_GET['idNotes']."'"));
		
		$title_page = array(
			'index.php?modname=notes&amp;op=notes' => $lang->def('_NOTES'), 
			$lang->def('_DEL_NOTES')
		);
		$GLOBALS['page']->add( 
			getTitleArea($title_page, 'notes')
			.'<div class="std_block">'
			.getDeleteUi(	$lang->def('_AREYOUSURE'),
							'<span>'.$lang->def('_TITLE').' : </span>'.$title,
							true, 
							'index.php?modname=notes&amp;op=delnotes&amp;idNotes='.$_GET['idNotes'].'&amp;confirm=1',
							'index.php?modname=notes&amp;op=notes' )
			.'</div>', 'content');
	}
}

function notesDispatch($op) {

switch($op) {
	case "notes" : {
		notes();
	};break;
	case "displaynotes" : {
		displaynotes();
	};break;
	
	case "addnotes" : {
		addnotes();
	};break;
	case "insnotes" : {
		insnotes();
	};break;
	
	case "modnotes" : {
		modnotes();
	};break;
	case "upnotes" : {
		upnotes();
	};break;
	
	case "delnotes" :  {
		delnotes();
	};break;
}
}

}

?>