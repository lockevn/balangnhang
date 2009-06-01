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
 * @version  $Id: news.php 573 2006-08-23 09:38:54Z fabio $
 * @author	 Fabio Pirovano <fabio [at] docebo [dot] com>
 */

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

function news() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.navbar.php');
	
	$mod_perm	= checkPerm('mod', true);
	$lang 		=& DoceboLanguage::createInstance('admin_news', 'lms');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$tb	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_NEWS_CAPTION'), $lang->def('_NEWS_SUMMARY'));
	$nav_bar = new NavBar('ini', $GLOBALS['lms']['visuItem'], 0, 'link');
	
	$ini = $nav_bar->getSelectedElement();
	
	//search query
	$query_news = "
	SELECT idNews, publish_date, title, short_desc, important 
	FROM ".$GLOBALS['prefix_lms']."_news_internal 
	ORDER BY important DESC, publish_date DESC 
	LIMIT $ini,".$GLOBALS['lms']['visuItem'];
	
	$query_news_tot = "
	SELECT COUNT(*) 
	FROM ".$GLOBALS['prefix_lms']."_news_internal ";
	
	$re_news = mysql_query($query_news);
	list($tot_news) = mysql_fetch_row(mysql_query($query_news_tot));
	

	$nav_bar->setElementTotal($tot_news);
	$impo_gif = '<img src="'.getPathImage('lms').'news/important.gif" '
			.'title="'.$lang->def('_TITLE_IMPORTANT').'" '
			.'alt="'.$lang->def('_ALT_IMPORTANT').'" />';
	
	$type_h = array('image', '', '', 'news_short_td');
	$cont_h	= array(
		$impo_gif, 
		$lang->def('_PUBLISH_DATE'), 
		$lang->def('_TITLE'), 
		$lang->def('_SHORTDESC')
	);
	if($mod_perm) {
		
		$cont_h[] = '<img src="'.getPathImage().'standard/moduser.gif" title="'.$lang->def('_RECIPIENTS').'" '
						.'alt="'.$lang->def('_RECIPIENTS').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MOD_NEWS').'" '
						.'alt="'.$lang->def('_MOD').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_TITLE_DEL_NEWS').'" '
						.'alt="'.$lang->def('_DEL').'"" />';
		$type_h[] = 'image';
	}
	
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($id_news, $publish_date, $title, $short_desc, $impo) = mysql_fetch_row($re_news)) {
		
		$cont = array(
			( $impo ? $impo_gif : '' ), 
			$GLOBALS['regset']->databaseToRegional($publish_date), 
			$title, 
			$short_desc
		);
		if($mod_perm) {
			
			$cont[] = '<a href="index.php?modname=internal_news&amp;op=editviewer&amp;load=1&amp;id_news='.$id_news.'" '
						.'title="'.$lang->def('_RECIPIENTS').' : '.$title.'">'
						.'<img src="'.getPathImage().'standard/moduser.gif" alt="'.$lang->def('_RECIPIENTS').' : '.$title.'" /></a>';
			
			$cont[] = '<a href="index.php?modname=internal_news&amp;op=modnews&amp;id_news='.$id_news.'" '
						.'title="'.$lang->def('_TITLE_MOD_NEWS').' : '.$title.'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$title.'" /></a>';
						
			$cont[] = '<a href="index.php?modname=internal_news&amp;op=delnews&amp;id_news='.$id_news.'" '
						.'title="'.$lang->def('_TITLE_DEL_NEWS').' : '.$title.'">'
						.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$title.'" /></a>';
		}
		$tb->addBody($cont);
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delnews]');
	
	if($mod_perm) {
		$tb->addActionAdd(
			'<a href="index.php?modname=internal_news&amp;op=addnews" title="'.$lang->def('_TITLE_NEW_NEWS').'">'
				.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" />'
				.$lang->def('_NEW_NEWS').'</a>'
		);
	}
	
	$out->add(getTitleArea($lang->def('_NEWS'), 'news')
			.'<div class="std_block">'	);
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" 		: $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
			case "err" 		: $out->add(getErrorUi($lang->def('_ERR_OPERATION')));break;
			case "err_del" : $out->add(getErrorUi($lang->def('_ERR_DELETE_OP')));break;
		}
	}
	if($mod_perm) {
		$form = new Form();
		
		if(isset($_POST['save_homepage'])) {
			
			$query_how_news = "
			UPDATE ".$GLOBALS['prefix_lms']."_setting 
			SET param_value = '".abs((int)$_POST['howmuch'])."'
			WHERE param_name = 'visuNewsHomePage'";
			if(mysql_query($query_how_news)) $GLOBALS['lms']['visuNewsHomePage'] = abs((int)$_POST['howmuch']);
		}
	}
	$out->add($tb->getTable()
			.$nav_bar->getNavBar($ini)
			.'</div>');
}

function editnews($load = false) {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang 		=& DoceboLanguage::createInstance('admin_news', 'lms');
	$form		= new Form();
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$id_news = importVar('id_news', true, 0);
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();
	
	if($load) {
		
		$query_news = "
		SELECT title, short_desc, long_desc, important, language 
		FROM ".$GLOBALS['prefix_lms']."_news_internal 
		WHERE idNews = '".$id_news."'";
		list($title, $short_desc, $long_desc, $impo, $lang_sel) = mysql_fetch_row(mysql_query($query_news));
	} else {
		
		$title =  $lang->def('_NOTITLE');
		$short_desc = '';
		$long_desc = '';
		$impo = 0;
		$lang_sel = getLanguage();
	}
	
	$page_title = array(
		'index.php?modname=internal_news&amp;op=news' => $lang->def('_NEWS'), 
		( $load ? $lang->def('_MOD_NEWS') : $lang->def('_NEW_NEWS') )
	);
	$out->add(getTitleArea($page_title, 'news')
			.'<div class="std_block">'
			.getBackUi( 'index.php?modname=internal_news&amp;op=news', $lang->def('_BACK') )
			
			.$form->openForm('adviceform', 'index.php?modname=internal_news&amp;op=savenews')
	);
	if($load) {
		
		$out->add($form->getHidden('id_news', 'id_news', $id_news)
				.$form->getHidden('load', 'load', 1)	);
	}
	$out->add($form->openElementSpace()
			
			.$form->getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $title)
			.$form->getCheckbox($lang->def('_IMPOFLAG'), 'impo', 'impo', 1, $impo)
			.$form->getDropdown($lang->def('_LANGUAGE'), 'language', 'language', $all_languages, array_search($lang_sel, $all_languages))
			
			.$form->getTextarea($lang->def('_SHORTDESC'), 'short_desc', 'short_desc', $short_desc)
			
			.$form->closeElementSpace()
			.$form->openButtonSpace()
			.$form->getButton('news', 'news', ( $load ? $lang->def('_SAVE') : $lang->def('_INSERT') ) )
			.$form->getButton('undo', 'undo', $lang->def('_UNDO'))
			.$form->closeButtonSpace()
			.$form->closeForm()
			.'</div>');
	
}


function editviewer() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang 		=& DoceboLanguage::createInstance('admin_news', 'lms');
	$form		= new Form();
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$id_news = importVar('id_news', true, 0);
		
	$page_title = array(
		'index.php?modname=internal_news&amp;op=news' => $lang->def('_NEWS'), 
		$lang->def('_RECIPIENTS')
	);
	$acl_manager = new DoceboACLManager();
	$user_select = new Module_Directory();
	
	$user_select->show_user_selector = TRUE;
	$user_select->show_group_selector = TRUE;
	$user_select->show_orgchart_selector = TRUE;
	$user_select->show_orgchart_simple_selector = false;
	//$user_select->multi_choice = TRUE;
	
	$query_news = "
	SELECT title, viewer
	FROM ".$GLOBALS['prefix_lms']."_news_internal 
	WHERE idNews = '".$id_news."'";
	list($title, $viewer) = mysql_fetch_row(mysql_query($query_news));
	// try to load previous saved
	if(isset($_GET['load'])) {
		
		$viewer = unserialize($viewer);
		if(is_array($viewer))	$user_select->resetSelection($viewer);
		else $user_select->resetSelection(array());
	}
	if(isset($_POST['cancelselector'])) {
		
		jumpTo('index.php?modname=internal_news&amp;op=news');
	}
	if(isset($_POST['okselector'])) {
		
		$selected = $user_select->getSelection($_POST);
		
		$query_news = "
		UPDATE ".$GLOBALS['prefix_lms']."_news_internal 
		SET viewer = '".serialize($selected)."'
		WHERE idNews = '".$id_news."'";
		$re = mysql_query($query_news);
		
		jumpTo('index.php?modname=internal_news&amp;op=news&amp;result='.($re ? 'ok' : 'err' ));
	}
	
	
	$user_select->setPageTitle(
		getTitleArea($page_title, 'news')
	);
	$user_select->addFormInfo(
		$form->getHidden('id_news', 'id_news', $id_news)
	);
	$user_select->loadSelector('index.php?modname=internal_news&amp;op=editviewer', 
			false, 
			$lang->def('_CHOOSE_WHO_CAN_SEE'), 
			true, 
			true );
	
}

function savenews() {
	checkPerm('mod');
	
	$id_news 	= importVar('id_news', true, 0);
	$load 		= importVar('load', true, 0);
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();
	$lang 		=& DoceboLanguage::createInstance('admin_news', 'lms');
	
	if($_POST['title'] == '') $_POST['title'] = $lang->def('_NOTITLE');
	$lang_sel = $_POST['language'];
	
	if($load == 1) {
		
		$query_insert = "
		UPDATE ".$GLOBALS['prefix_lms']."_news_internal 
		SET	title = '".$_POST['title']."' ,
			short_desc = '".$_POST['short_desc']."' ,
			important = '".( isset($_POST['impo']) ? 1 : 0 )."' ,
			language = '".$all_languages[$lang_sel]."'
		WHERE idNews = '".$id_news."'";
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=internal_news&op=news&result=err');
		jumpTo('index.php?modname=internal_news&op=news&result=ok');
	} else {
		
		$query_insert = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_news_internal 
		( title, publish_date, short_desc, important, language ) VALUES
		( 	'".$_POST['title']."' ,
			'".date("Y-m-d H:i:s")."', 
			'".$_POST['short_desc']."' ,
			'".( isset($_POST['impo']) ? 1 : 0 )."' ,
			'".$all_languages[$lang_sel]."' )";
			
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=internal_news&op=news&result=err');
		jumpTo('index.php?modname=internal_news&op=news&result=ok');
	}
}

function delnews() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$id_news 	= get_req('id_news', DOTY_INT, 0);
	$lang 		=& DoceboLanguage::createInstance('admin_news', 'lms');
	
	if(get_req('confirm', DOTY_INT, 0) == 1) {
		
		$query_news = "
		DELETE FROM ".$GLOBALS['prefix_lms']."_news_internal 
		WHERE idNews = '".$id_news."'";
		if(!mysql_query($query_news)) jumpTo('index.php?modname=internal_news&op=news&result=err_del');
		else jumpTo('index.php?modname=internal_news&op=news&result=ok');
	} else {
		
		list($title, $short_desc) = mysql_fetch_row(mysql_query("
		SELECT title, short_desc
		FROM ".$GLOBALS['prefix_lms']."_news_internal 
		WHERE idNews = '".$id_news."'"));
		
		$form = new Form();
		$page_title = array(
			'index.php?modname=internal_news&amp;op=news' => $lang->def('_NEWS'), 
			$lang->def('_DEL_NEWS')
		);
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'admin_news')
			.'<div class="std_block">'
			.$form->openForm('del_news', 'index.php?modname=internal_news&amp;op=delnews')
			.$form->getHidden('id_news', 'id_news', $id_news)
			.getDeleteUi(	$lang->def('_AREYOUSURE'), 
							'<span>'.$lang->def('_TITLE').' : </span>'.$title.'<br />'
								.'<span>'.$lang->def('_SHORTDESC').' : </span>'.$short_desc, 
							false, 
							'confirm',
							'undo'	)
			.$form->closeForm()
			.'</div>', 'content');
	}
}

function internal_newsDispatch($op) {
	
	if(isset($_POST['undo'])) $op = 'news';
	switch($op) {
		case "news" : {
			news();
		};break;
		case "addnews" : {
			editnews();
		};break;
		case "editviewer" : {
			editviewer();
		};break;
		case "modnews" : {
			editnews(true);
		};break;
		case "savenews" : {
			savenews();
		};break;
		case "delnews" : {
			delnews();
		};break;
	}
}

?>