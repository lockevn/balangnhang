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
 * @version  $Id: questcategory.php 573 2006-08-23 09:38:54Z fabio $
 * @author	 Fabio Pirovano <fabio [at] docebo [dot] com>
 */

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

function questcategory() {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$mod_perm = checkPerm('mod', true);
	
	$lang	=& DoceboLanguage::createInstance('questcategory', 'lms');
	$out 	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$tb = new TypeOne(0, $lang->def('_QUESTCATEGORY_CAPTION'), $lang->def('_QUESTCATEGORY_SUMMARY'));
	$nav_bar = new NavBar('ini', $GLOBALS['lms']['visuItem'], 0, 'button');
	$ini = $nav_bar->getSelectedElement();
	
	//search query
	$query_pages = "
	SELECT idCategory, name, textof 
	FROM ".$GLOBALS['prefix_lms']."_quest_category 
	ORDER BY name 
	LIMIT $ini,".$GLOBALS['lms']['visuItem'];
	
	$num_query_pages = "
	SELECT COUNT(*) 
	FROM ".$GLOBALS['prefix_lms']."_quest_category ";
	
	//do query
	$re_pages = mysql_query($query_pages);
	list($tot_pages) = mysql_fetch_row(mysql_query($num_query_pages));
	$nav_bar->setElementTotal($tot_pages);
	
	//-Table---------------------------------------------------------
	$cont_h = array($lang->def('_TITLE'), $lang->def('_DESCRIPTION'));
	$type_h = array('', '');
	if($mod_perm) {
		
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_TITLE_MOD_QCAT').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" title="'.$lang->def('_TITLE_REM_QCAT').'" />';
		$type_h[] = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	
	$i = 1;
	while(list($id, $title, $textof) = mysql_fetch_row($re_pages)) {
		
		$cont = array(
			$title, 
			$textof );
		
		if($mod_perm) {
			
			$cont[] = '<a href="index.php?modname=questcategory&amp;op=modquestcategory&amp;id_qcat='.$id.'" title="'.$lang->def('_TITLE_MOD_QCAT').' : '.$title.'">'
					.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$title.'" /></a>';
			
			$cont[] = '<a href="index.php?modname=questcategory&amp;op=delquestcategory&amp;id_qcat='.$id.'&confirm=1" title="'.$lang->def('_TITLE_REM_QCAT').' : '.$title.'">'
					.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$title.'" /></a>';
		}
		$tb->addBody($cont);
		++$i;
	}
	if($mod_perm) {
		$tb->addActionAdd('<a href="index.php?modname=questcategory&amp;op=addquestcategory" title="'.$lang->def('_TITLE_ADD_QCAT').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" /> '.$lang->def('_ADD_QCAT').'</a>');
	}
	//visualize result
	$out->add(
		getTitleArea($lang->def('_TITLE_QCAT'), 'questcategory', $lang->def('_TITLE_QCAT'))
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
		.Form::openForm('nav_questcategory', 'index.php?modname=questcategory&amp;op=questcategory')
		.$nav_bar->getNavBar($ini)
		.Form::closeForm()
		.'</div>');
		
	if ($mod_perm) {
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox(
			'a[href*=delquestcategory]',
			$lang->def('_AREYOUSURE'),
			$lang->def('_CONFIRM'),
			$lang->def('_UNDO'),
			'function(o) { return o.title; }'
		);
	}
}

function editquestcategory($load = false) {
	checkPerm('mod');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang	=& DoceboLanguage::createInstance('questcategory', 'lms');
	$out 	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();
	
	$id_qcat = importVar('id_qcat', true, 0);
	
	if($load) {
		
		$query_page = "
		SELECT name, textof
		FROM ".$GLOBALS['prefix_lms']."_quest_category 
		WHERE idCategory = '".$id_qcat."'";
		list($title, $description) = mysql_fetch_row(mysql_query($query_page));
	} else {
		
		$title			= $lang->def('_NOTITLE');
		$description	= '';
		$language		= getLanguage();
		$publish		= 0;
		$in_home		= 0;
	}
	$page_title = array(
		'index.php?modname=questcategory&amp;op=questcategory' => $lang->def('_TITLE_QCAT'), 
		( $load ? $lang->def('_TITLE_MOD_QCAT') : $lang->def('_ADD_QCAT') )
	);
	$out->add(
		getTitleArea($page_title, 'questcategory')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=questcategory&amp;op=questcategory', $lang->def('_BACK'))
		.Form::openForm('nav_questcategory', 'index.php?modname=questcategory&amp;op=savequestcategory')
		.Form::openElementSpace()
	);
	if($load) {
		$out->add(Form::getHidden('load', 'load', 1)
				.Form::getHidden('id_qcat', 'id_qcat', $id_qcat) );
	}
	$out->add(
		Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $title)
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

function savequestcategory() {
	checkPerm('mod');
	
	$lang	=& DoceboLanguage::createInstance('questcategory', 'lms');
	
	$id_qcat = importVar('id_qcat', true, 0);
	
	if($_POST['title'] == '') {
		$_POST['title'] = $lang->def('_NOTITLE');
	}
	if(isset($_POST['load'])) {
		
		$query_insert = "
		UPDATE ".$GLOBALS['prefix_lms']."_quest_category 
		SET name = '".$_POST['title']."',
			textof = '".$_POST['description']."'
		WHERE idCategory = '".$id_qcat."'";
	} else {
		
		$query_insert = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_quest_category 
		( name, textof ) VALUES 
		( 	'".$_POST['title']."',
			'".$_POST['description']."')";
	}
	if(!mysql_query($query_insert))jumpTo( 'index.php?modname=questcategory&op=questcategory&result=err');
	jumpTo( 'index.php?modname=questcategory&op=questcategory&result=ok');
}

function delquestcategory() {
	checkPerm('mod');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang		=& DoceboLanguage::createInstance('questcategory', 'lms');
	$id_qcat 	= importVar('id_qcat', true, 0);
	
	
	list($title) = mysql_fetch_row(mysql_query("
	SELECT name
	FROM ".$GLOBALS['prefix_lms']."_quest_category  
	WHERE idCategory = '".$id_qcat."'"));
	
	list($used_test) = mysql_fetch_row(mysql_query("
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_lms']."_testquest 
	WHERE idCategory = '".$id_qcat."'"));
	
	list($used_poll) = mysql_fetch_row(mysql_query("
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_lms']."_pollquest 
	WHERE id_category = '".$id_qcat."'"));
	
	$page_title = array(
		'index.php?modname=news&amp;op=news' => $lang->def('_TITLE_QCAT'), 
		$lang->def('_TITLE_REM_QCAT')
	);
	if($used_poll > 0 || $used_test > 0) {
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'questcategory')
			.'<div class="std_block">'
			.getBackUi('index.php?modname=questcategory&amp;op=questcategory', $lang->def('_BACK'))
			.$lang->def('_CATEGORY_IN_USE')
			.'</div>', 'content');
		return;
	}
	
	if(get_req('confirm', DOTY_INT, 0)>0) {
		
		$query_delete ="
		DELETE FROM ".$GLOBALS['prefix_lms']."_quest_category 
		WHERE idCategory = '".$id_qcat."'";
		
		if(!mysql_query($query_delete)) jumpTo( 'index.php?modname=questcategory&op=questcategory&result=err');
		jumpTo( 'index.php?modname=questcategory&op=questcategory&result=ok');
	} else {
		
		$form = new Form();
		
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'questcategory')
			.'<div class="std_block">'
			.$form->openForm('del_qcat', 'index.php?modname=questcategory&amp;op=delquestcategory')
			.$form->getHidden('id_qcat', 'id_qcat', $id_qcat)
			.getDeleteUi(	$lang->def('_AREYOUSURE'), 
							'<span>'.$lang->def('_TITLE').' : </span>'.$title, 
							false, 
							'confirm',
							'undo'	)
			.$form->closeForm()
			.'</div>', 'content');
	}
}

function questcategoryDispatch($op) {
	
	if(isset($_POST['undo'])) $op = 'questcategory';
	switch($op) {
		case "questcategory" : {
			questcategory();
		};break;
		case "addquestcategory" : {
			editquestcategory();
		};break;
		case "savequestcategory" : {
			savequestcategory();
		};break;
		
		case "modquestcategory" : {
			editquestcategory(true);
		};break;
		case "delquestcategory" : {
			delquestcategory();
		};break;
	}
}

?>