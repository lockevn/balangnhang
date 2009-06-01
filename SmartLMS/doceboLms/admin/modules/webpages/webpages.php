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
 * @version  $Id: webpages.php 793 2006-11-21 15:43:19Z fabio $
 * @author	 Fabio Pirovano <fabio [at] docebo [dot] com>
 */

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

function webpages() {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$mod_perm = checkPerm('mod', true);
	
	$lang	=& DoceboLanguage::createInstance('admin_webpages', 'lms');
	$out 	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$tb = new TypeOne(0, $lang->def('_WEBPAGES_CAPTION'), $lang->def('_WEBPAGES_SUMMARY'));
	$nav_bar = new NavBar('ini', $GLOBALS['lms']['visuItem'], 0, 'button');
	$ini = $nav_bar->getSelectedElement();
	
	//search query
	$query_pages = "
	SELECT idPages, title, publish, in_home, sequence 
	FROM ".$GLOBALS['prefix_lms']."_webpages 
	ORDER BY sequence 
	LIMIT $ini,".$GLOBALS['lms']['visuItem'];
	
	$num_query_pages = "
	SELECT COUNT(*) 
	FROM ".$GLOBALS['prefix_lms']."_webpages ";
	
	//do query
	$re_pages = mysql_query($query_pages);
	list($tot_pages) = mysql_fetch_row(mysql_query($num_query_pages));
	$nav_bar->setElementTotal($tot_pages);
	
	//-Table---------------------------------------------------------
	$cont_h = array(
		$lang->def('_TITLE'), 
		//'<img src="'.getPathImage().'webpages/home.gif" alt="'.$lang->def('_ALT_HOME').'" title="'.$lang->def('_TITLE_HOME').'" />', 
		'<img src="'.getPathImage().'webpages/publish.gif" alt="'.$lang->def('_ALT_PUBLISH').'" title="'.$lang->def('_TITLE_PUBLISH').'" />'	);
	$type_h = array('', 'image', 'image');
	if($mod_perm) {
		
		$cont_h[] = '<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').'" title="'.$lang->def('_MOVE_DOWN').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').'" title="'.$lang->def('_MOVE_UP').'" />';
		$type_h[] = 'image';
		
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_TITLE_MOD_PAGES').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" title="'.$lang->def('_TITLE_REM_PAGES').'" />';
		$type_h[] = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	
	$i = 1;
	while(list($id, $title, $publish, $in_home) = mysql_fetch_row($re_pages)) {
		
		$cont = array(
			$title//, 
			//( $in_home ? '<img src="'.getPathImage().'webpages/home.gif" alt="'.$lang->def('_ALT_HOME').'" title="'.$lang->def('_TITLE_HOME').'" />' : '')
		);
		if($publish) {
			$cont[] = '<a href="index.php?modname=webpages&amp;op=unpublish&amp;id_page='.$id.'" title="'.$lang->def('_TITLE_PUBLISH_TO_UN').' : '.$title.'">'
						.'<img src="'.getPathImage().'webpages/publish.gif" alt="'.$lang->def('_ALT_PUBLISH').' : '.$title.'" /></a>';
		} else {
			$cont[] = '<a href="index.php?modname=webpages&amp;op=publish&amp;id_page='.$id.'" title="'.$lang->def('_TITLE_UNPUBLISH_TO_PU').' : '.$title.'">'
						.'<img src="'.getPathImage().'webpages/unpublish.gif" alt="'.$lang->def('_ALT_UNPUBLISH').' : '.$title.'" /></a>';
		}
		if($mod_perm) {
			if($i != $tot_pages - ($ini * $GLOBALS['lms']['visuItem']) ) {
				$cont[] = '<a href="index.php?modname=webpages&amp;op=movedown&amp;id_page='.$id.'" title="'.$lang->def('_TITLE_DOWN').' : '.$title.'">'
						.'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').' : '.$title.'" /></a>';
			} else {
				$cont[] = '&nbsp;';
			}
			if($i != 1 || $ini != 0) {
				$cont[] = '<a href="index.php?modname=webpages&amp;op=moveup&amp;id_page='.$id.'" title="'.$lang->def('_MOVE_UP').' : '.$title.'">'
						.'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').' : '.$title.'" /></a>';
			} else {
				$cont[] = '&nbsp;';
			}
			
			$cont[] = '<a href="index.php?modname=webpages&amp;op=modpages&amp;id_page='.$id.'" title="'.$lang->def('_TITLE_MOD_PAGES').' : '.$title.'">'
					.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$title.'" /></a>';
			
			$cont[] = '<a href="index.php?modname=webpages&amp;op=delpages&amp;id_page='.$id.'" title="'.$lang->def('_TITLE_REM_PAGES').' : '.$title.'">'
					.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$title.'" /></a>';
		}
		
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delpages]');
		
		$tb->addBody($cont);
		++$i;
	}
	if($mod_perm) {
		$tb->addActionAdd('<a href="index.php?modname=webpages&amp;op=addpages" title="'.$lang->def('_TITLE_ADD_WEBPAGES').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" /> '.$lang->def('_TITLE_ADD_WEBPAGES').'</a>');
	}
	//visualize result
	$out->add(
		getTitleArea($lang->def('_TITLE_WEBPAGES'), 'webpages')
		.'<div class="std_block">'
	);
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" 		: $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
			case "err" 		: $out->add(getErrorUi($lang->def('_ERR_OPERATION')));break;
			case "err_del" : $out->add(getErrorUi($lang->def('_ERR_DELETE_OP')));break;
		}
	}
	$out->add(
		$tb->getTable()
		.Form::openForm('nav_webpages', 'index.php?modname=webpages&amp;op=webpages')
		.$nav_bar->getNavBar($ini)
		.Form::closeForm()
		.'</div>');
}

function editpages($load = false) {
	checkPerm('mod');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang	=& DoceboLanguage::createInstance('admin_webpages', 'lms');
	$out 	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();
	
	$id_page = importVar('id_page', true, 0);
	
	if($load) {
		
		$query_page = "
		SELECT title, description, language, publish, in_home
		FROM ".$GLOBALS['prefix_lms']."_webpages 
		WHERE idPages = '".$id_page."'";
		list($title, $description, $language, $publish, $in_home) = mysql_fetch_row(mysql_query($query_page));
	} else {
		
		$title			= $lang->def('_NOTITLE');
		$description	= '';
		$language		= getLanguage();
		$publish		= 0;
		$in_home		= 0;
	}
	$page_title = array(
		'index.php?modname=webpages&amp;op=webpages' => $lang->def('_TITLE_WEBPAGES'), 
		( $load ? $lang->def('_TITLE_MOD_PAGES') : $lang->def('_ADD_WEBPAGES') )
	);
	$out->add(
		getTitleArea($page_title, 'webpages')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=webpages&amp;op=webpages', $lang->def('_BACK'))
		.Form::openForm('nav_webpages', 'index.php?modname=webpages&amp;op=savepages')
		.Form::openElementSpace()
	);
	if($load) {
		$out->add(Form::getHidden('load', 'load', 1)
				.Form::getHidden('id_page', 'id_page', $id_page) );
	}
	$out->add(
		Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $title)
		.Form::getDropdown($lang->def('_LANGUAGE'), 'language', 'language', $all_languages, array_search($language, $all_languages))
		
		.Form::getCheckbox($lang->def('_DIRECT_PUBLISH'), 'publish', 'publish', 1, $publish)
		//.Form::getCheckbox($lang->def('_PUT_IN_HOME'), 'in_home', 'in_home', 1, $in_home)
		
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $description)
		.Form::closeElementSpace()
		
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>'
	);
}

function savepages() {
	checkPerm('mod');
	
	$lang	=& DoceboLanguage::createInstance('admin_webpages', 'lms');
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();
	
	$id_page = importVar('id_page', true, 0);
	
	if($_POST['title'] == '') {
		$_POST['title'] = $lang->def('_NOTITLE');
	}
	$lang_sel = $_POST['language'];
	if(isset($_POST['in_home'])) {
		
		if(!mysql_query("UPDATE ".$GLOBALS['prefix_lms']."_webpages SET in_home = 0 
			WHERE in_home = 1 
				AND language = '".$all_languages[$lang_sel]."'")) unset($_POST['in_home']);
	}
	if(isset($_POST['load'])) {
		/*
			in_home = '".( isset($_POST['in_home']) ? 1 : 0 )."',*/
		$query_insert = "
		UPDATE ".$GLOBALS['prefix_lms']."_webpages
		SET title = '".$_POST['title']."',
			description = '".$_POST['description']."',
			language = '".$all_languages[$lang_sel]."',
			publish = '".( isset($_POST['publish']) ? 1 : 0 )."'
		WHERE idPages = '".$id_page."'";
	} else {
		/**/
		list($seq) = mysql_fetch_row(mysql_query("
		SELECT MAX(sequence) + 1
		FROM ".$GLOBALS['prefix_lms']."_webpages"));
		
		$query_insert = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_webpages
		( title, description, language, in_home, publish, sequence ) VALUES 
		( 	'".$_POST['title']."',
			'".$_POST['description']."',
			'".$all_languages[$lang_sel]."',
			'".( isset($_POST['in_home']) ? 1 : 0 )."',
			'".( isset($_POST['publish']) ? 1 : 0 )."',
			'".$seq."')";
	}
	if(!mysql_query($query_insert)) jumpTo( 'index.php?modname=webpages&op=webpages&result=err');
	jumpTo( 'index.php?modname=webpages&op=webpages&result=ok');
}

function delpages() {
	checkPerm('mod');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang	=& DoceboLanguage::createInstance('admin_webpages', 'lms');
	$id_page = importVar('id_page', true, 0);
	
	
	list($title, $seq) = mysql_fetch_row(mysql_query("
	SELECT title, sequence
	FROM ".$GLOBALS['prefix_lms']."_webpages 
	WHERE idPages = '".$id_page."'"));
	
	if(get_req('confirm', DOTY_INT, 0) == 1) {
		
		$query_delete ="
		DELETE FROM ".$GLOBALS['prefix_lms']."_webpages 
		WHERE idPages = '".$id_page."'";
		
		if(!mysql_query($query_delete)) jumpTo( 'index.php?modname=webpages&op=webpages&result=err');
		
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_webpages
		SET sequence = sequence -1
		WHERE sequence > '".$seq."'");
		
		jumpTo( 'index.php?modname=webpages&op=webpages&result=ok');
	} else {
		
		$form = new Form();
		$page_title = array(
			'index.php?modname=news&amp;op=news' => $lang->def('_NEWS'), 
			$lang->def('_TITLE_REM_PAGES')
		);
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'admin_webpages')
			.'<div class="std_block">'
			.$form->openForm('del_pages', 'index.php?modname=webpages&amp;op=delpages')
			.$form->getHidden('id_page', 'id_page', $id_page)
			.getDeleteUi(	$lang->def('_AREYOUSURE'), 
							'<span>'.$lang->def('_TITLE').' : </span>'.$title, 
							false, 
							'confirm',
							'undo'	)
			.$form->closeForm()
			.'</div>', 'content');
	}
}

function publish($id_page, $publish) {
	checkPerm('mod');
	
	if($publish) {
		$query_publish = "
		UPDATE ".$GLOBALS['prefix_lms']."_webpages 
		SET publish = 1 
		WHERE idPages = '".$id_page."'";
	} else {
		
		$query_publish = "
		UPDATE ".$GLOBALS['prefix_lms']."_webpages 
		SET publish = 0 
		WHERE idPages = '".$id_page."'";
	}
	if(!mysql_query($query_publish)) jumpTo( 'index.php?modname=webpages&op=webpages&result=err');
	jumpTo( 'index.php?modname=webpages&op=webpages&result=ok');
}

function movepages($direction) {
	checkPerm('mod');
	
	$id_page = importVar('id_page', true, 0);
	
	list($seq) = mysql_fetch_row(mysql_query("
	SELECT sequence
	FROM ".$GLOBALS['prefix_lms']."_webpages 
	WHERE idPages = '".$id_page."'"));
	
	if($direction == 'up') {
		if($seq == 0) return;
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_webpages 
		SET sequence = '$seq' 
		WHERE sequence = '".($seq - 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_webpages 
		SET sequence = sequence - 1 
		WHERE idPages = '".$id_page."'");
		
	}
	if($direction == 'down') {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_webpages 
		SET sequence = '$seq' 
		WHERE sequence = '".($seq + 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_webpages 
		SET sequence = '".($seq + 1)."' 
		WHERE idPages = '".$id_page."'");
	}
	jumpTo( 'index.php?modname=webpages&op=webpages');
}

function webpagesDispatch($op) {
	
	if(isset($_POST['undo'])) $op = 'webpages';
	switch($op) {
		case "webpages" : {
			webpages();
		};break;
		case "addpages" : {
			editpages();
		};break;
		case "savepages" : {
			savepages();
		};break;
		
		case "publish" : {
			publish($_GET['id_page'], true);
		};break;
		case "unpublish" : {
			publish($_GET['id_page'], false);
		};break;
		
		case "movedown" : {
			movepages('down');
		};break;
		case "moveup" : {
			movepages('up');
		};break;
		
		case "modpages" : {
			editpages(true);
		};break;
		case "delpages" : {
			delpages();
		};break;
	}
}

?>