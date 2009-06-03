<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

require_once($GLOBALS['where_lms'].'/lib/lib.manmenu_course.php');
require_once($GLOBALS['where_lms'].'/lib/lib.levels.php');

function manmenu() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang 		=& DoceboLanguage::createInstance('manmenu', 'framework');
	$mo_lang 	=& DoceboLanguage::createInstance('menu_over', 'lms');
	
	$mod_perm 	= checkPerm('mod', true);
	
	$query_voice = "
	SELECT idMain, name, image, sequence 
	FROM ".$GLOBALS['prefix_lms']."_menucourse_main 
	WHERE idCourse = '".(int)$_SESSION['idCourse']."'
	ORDER BY sequence";
	$re_voice = mysql_query($query_voice);
	$tot_voice = mysql_num_rows($re_voice);
	
	$tb = new TypeOne(0, $lang->def('_TB_MANMENU_CAPTION'), $lang->def('_TB_MANMENU_SUMMARY'));
	$content_h 	= array(
		$lang->def('_ORDER'), 
		'<img src="'.getPathImage().'manmenu/symbol.gif" title="'.$lang->def('_SYMBOL_TITLE').'" alt="'.$lang->def('_SYMBOL').'" />', 
		$lang->def('_TITLE_MENUVOICE'), 
		'<img src="'.getPathImage().'standard/down.gif" title="'.$lang->def('_MOVE_DOWN').'" alt="'.$lang->def('_DOWN').'" />', 
		'<img src="'.getPathImage().'standard/up.gif" title="'.$lang->def('_MOVE_UP').'" alt="'.$lang->def('_UP').'" />', 
		'<img src="'.getPathImage().'standard/modelem.gif" title="'.$lang->def('_MODMODULE').'" alt="'.$lang->def('_ALT_MODMODULE').'" />');
	$type_h 	= array('image', 'image', '', 'image', 'image', 'image');
	if($mod_perm) {
		$content_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MODMENUVOICE').'" alt="'.$lang->def('_MOD').'" />';
		$type_h[] 	 = 'image';
		$content_h[] = '<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_TITLE_DELMENUVOICE').'" alt="'.$lang->def('_DEL').'" />';
		$type_h[] 	 = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($content_h);
	$i = 0;
	while(list($id_m, $name, $image, $sequence) = mysql_fetch_row($re_voice)) {
		
		$strip_name = strip_tags(( $mo_lang->isDef($name) ? $mo_lang->def($name) : $name ));
		$content = array(
			$sequence,
			'<img class="manmenu_symbol" src="'.getPathImage('lms').'menu/'.$image.'" alt="'.$strip_name.'" />',
			'<a href="index.php?modname=manmenu&amp;op=manmodule&amp;id_main='.$id_m.'"'
				.' title="'.$lang->def('_MODMODULE').'">'.( $mo_lang->isDef($name) ? $mo_lang->def($name) : $name ).'</a>');
		// Up and Down action
		$content[] = ( $i != ($tot_voice - 1) ? '<a href="index.php?modname=manmenu&amp;op=mdmenuvoice&amp;id_main='.$id_m.'"'
				.' title="'.$lang->def('_MOVE_DOWN').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').' : '.$strip_name.'" /></a>' : '' );
		$content[] = ( $i != 0 ? '<a href="index.php?modname=manmenu&amp;op=mumenuvoice&amp;id_main='.$id_m.'"'
				.' title="'.$lang->def('_MOVE_UP').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').' : '.$strip_name.'" /></a>' : '' );
		// Modify module
		$content[] = '<a href="index.php?modname=manmenu&amp;op=manmodule&amp;id_main='.$id_m.'"'
				.' title="'.$lang->def('_MODMODULE').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'standard/modelem.gif" alt="'.$lang->def('_ALT_MODMODULE').' : '.$strip_name.'" /></a>';
		if($mod_perm) {
			// Modify voice
			$content[] = '<a href="index.php?modname=manmenu&amp;op=modmenuvoice&amp;id_main='.$id_m.'"'
				.' title="'.$lang->def('_TITLE_MODMENUVOICE').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$strip_name.'" /></a>';
			
			// Delete voice
			$content[] = '<a href="index.php?modname=manmenu&amp;op=delmenuvoice&amp;id_main='.$id_m.'"'
				.' title="'.$lang->def('_TITLE_DELMENUVOICE').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$strip_name.'" /></a>';
		}
		$tb->addBody($content);
		$i++;
	}
	if($mod_perm) {
		
		$tb->addActionAdd('<a href="index.php?modname=manmenu&amp;op=addmenuvoice"'
			.' title="'.$lang->def('_TITLE_ADDMENUVOICE').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" />'.$lang->def('_ADDMENUVOICE').'</a>');
		
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delmenuvoice]');
	}
	
	// print out
	$page_title = array(
		$lang->def('_TITLE_MANMENU')
	);
	
	$out->setWorkingZone('content');
	$out->add(
		getTitleArea($page_title, 'manmenu')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=manmenu&amp;op=mancustom', $lang->def('_BACK')) );
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case 0 : $out->add(getResultUi($lang->def('_OPERATION_FAILURE')));break;
			case 1 : $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
		} 
	}
	$out->add($tb->getTable()
		.'[ <a href="index.php?modname=manmenu&amp;op=fixmenuvoice" '
			.'title="'.$lang->def('_FIXSEQUENCE_MANMENU_TITLE').'">'
			.$lang->def('_FIXSEQUENCE_MANMENU').'</a> ]'
		.'</div>');
}

function editmenuvoice($load = false) {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang 		=& DoceboLanguage::createInstance('manmenu', 'framework');
	$mo_lang 	=& DoceboLanguage::createInstance('menu_over', 'lms');
	
	$out->setWorkingZone('content');
	
	// Find images
	$all_images = array();
	$templ = dir(getPathImage('lms').'menu/');
	while($elem = $templ->read()) {
		
		if(ereg('.gif', $elem)) $all_images[$elem] = $elem;
	}
	closedir($templ->handle);
	
	if($load == false) {
		$page_title = array(
			'index.php?modname=manmenu&amp;op=manmenu' => $lang->def('_TITLE_MANMENU'),
			$lang->def('_ADDMENUVOICE')
		);
		
		$name = '';
		$image = 'blank.gif';
	} else {
		
		$id_main = importVar('id_main', true, 0);
		$query_custom = "
		SELECT name, image 
		FROM ".$GLOBALS['prefix_lms']."_menucourse_main
		WHERE idMain = '".$id_main."'";
		list($name, $image) = mysql_fetch_row(mysql_query($query_custom));
		$page_title = array(
			'index.php?modname=manmenu&amp;op=manmenu' => $lang->def('_TITLE_MANMENU'),
			$lang->def('_TITLE_MODMENUVOICE').' : '.$name
		);
	}
	$out->add(
		getTitleArea($page_title, 'manmenu')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=manmenu&amp;op=manmenu'
			.( $load == false ? '' : '&amp;id_main='.$id_main ), $lang->def('_BACK'))
		.Form::openForm('addmenuvoice_form', 'index.php?modname=manmenu&amp;op=savemenuvoice')
		.Form::openElementSpace()
		.Form::getTextfield($lang->def('_TITLE'), 'name', 'name', 255, $name)
	);
	if($load !== false) {
		
		$out->add(Form::getHidden('id_main', 'id_main', $id_main));
	}
	$out->add(
		Form::getDropdown($lang->def('_SYMBOL_TITLE'), 'image', 'image', $all_images, $image)
	);
	$out->add(Form::getLineBox($lang->def('_PREVIEW'), 
		'<img class="image_preview" id="imgpreview" src="'.getPathImage().'menu/'.$image.'" alt="'.$lang->def('_PREVIEW').'" />'));
	$out->add('<script type="text/javascript">
		<!--
		var imgselect = null;
		var imgpreview = null;
		window.onload = function() {
			if( document.getElementById ) {
				imgselect = document.getElementById("image");
				imgpreview = document.getElementById("imgpreview");
			} else {
				imgselect = document.all["image"];
				imgpreview = document.all["imgpreview"];
			}
			imgselect.onchange = function() {
				imgpreview.src = "'.getPathImage('lms').'menu/" + imgselect.options[imgselect.selectedIndex].value;
			}
		}
		// -->
	 </script>');
	$out->add(
		Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('addmenuvoice', 'addmenuvoice', ( $load == false ? $lang->def('_INSERT') : $lang->def('_SAVE') ))
		.Form::getButton('undomenuvoice', 'undomenuvoice', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>'	);
}

function savemenuvoice() {
	checkPerm('mod');
	
	$re = true;
	if(isset($_POST['undomenuvoice'])) {
		
		if(isset($_POST['id_main'])) {
			jumpTo('index.php?modname=manmenu&op=manmenu&id_main='.$_POST['id_main']);
		} else {
			jumpTo('index.php?modname=manmenu&op=manmenu');
		}
	}
	if(isset($_POST['id_main'])) {
		
		$re = mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_main 
		SET name = '".$_POST['name']."', 
			image = '".$_POST['image']."' 
		WHERE idMain = '".$_POST['id_main']."'");
		
		jumpTo('index.php?modname=manmenu&op=manmenu&id_main='.$_POST['id_main'].'&result='.( $re ? 1 : 0 ));
	} else {
		
		$query_seq = "
		SELECT MAX(sequence)
		FROM ".$GLOBALS['prefix_lms']."_menucourse_main 
		WHERE idCourse = '".(int)$_SESSION['idCourse']."'";
		list($seq) = mysql_fetch_row(mysql_query($query_seq));
		++$seq;
		
		$re = mysql_query("
		INSERT INTO ".$GLOBALS['prefix_lms']."_menucourse_main 
		( idCourse, name, image, sequence ) VALUES 
		( '".$_SESSION['idCourse']."', '".$_POST['name']."', '".$_POST['image']."', '".$seq."' )");
		
		jumpTo('index.php?modname=manmenu&op=manmenu&result='.( $re ? 1 : 0 ));
	}
}

function delmenuvoice() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang 		=& DoceboLanguage::createInstance('manmenu', 'framework');
	$mo_lang 	=& DoceboLanguage::createInstance('menu_over');
	
	$id_main = importVar('id_main', true, 0);
	
	$query_custom = "
	SELECT idCustom, name, image 
	FROM ".$GLOBALS['prefix_lms']."_menucourse_main 
	WHERE idMain = '".$id_main."'";
	list($id_custom, $name_db, $image) = mysql_fetch_row(mysql_query($query_custom));
	
	if(isset($_POST['undo'])) {
		
		jumpTo('index.php?modname=manmenu&op=manmenu&id_main='.$id_main);
	} elseif(isset($_POST['confirm']) || isset($_GET['confirm'])) {
		
		$id_main = get_req('id_main', DOTY_INT, 0);
		
		$re = true;
		$re_modules = mysql_query("
		SELECT idModule 
		FROM ".$GLOBALS['prefix_lms']."_menucourse_under 
		WHERE idMain = '".$id_main."'");
		while(list($id_module) = mysql_fetch_row($re_modules)) {
			
			$re &= removeModule($id_module, $id_main, $id_custom);
		}
		if(!$re) jumpTo('index.php?modname=manmenu&op=manmenu&result=0');
		
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_menucourse_under 
		WHERE idMain = '".$id_main."'"))
			jumpTo('index.php?modname=manmenu&op=manmenu&result=0');
		
		$re = mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_menucourse_main 
		WHERE idMain = '".$id_main."'");
		
		$GLOBALS['current_user']->loadUserSectionST();
		$GLOBALS['current_user']->SaveInSession();
		
		jumpTo('index.php?modname=manmenu&op=manmenu&result='.( $re ? 1 : 0 ));
	} else {
		$name = ( $mo_lang->isDef($name_db) ? $mo_lang->def($name_db) : $name_db );
		$strip_name = strip_tags($name);
		$out->add(
			getTitleArea($lang->def('_TITLE_MANMENU'), 'manmenu')
			.'<div class="std_block">'
			.Form::openForm('delcustom_form', 'index.php?modname=manmenu&amp;op=delmenuvoice')
			.Form::getHidden('id_main', 'id_main', $id_main)
			.getDeleteUi($lang->def('_AREYOUSURE_MENUCUSTOM'), 
						'<img class="manmenu_symbol" src="'.getPathImage('lms').'menu/'.$image.'" alt="'.$strip_name.'" />'
						.'<span class="text_bold">'.$lang->def('_TITLE_MENUVOICE').' : </span>'.$name, 
						false, 
						'confirm', 
						'undo')
			.Form::closeForm()
			.'</div>');
	}
}

function movemenuvoice($direction) {
	checkPerm('mod');
	
	$id_main = importVar('id_main', true, 0);
	
	list($seq) = mysql_fetch_row(mysql_query("
	SELECT sequence 
	FROM ".$GLOBALS['prefix_lms']."_menucourse_main 
	WHERE idMain = '$id_main'"));
	
	if($direction == 'up') {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_main 
		SET sequence = '$seq' 
		WHERE idCourse = '".$_SESSION['idCourse']."' AND sequence = '".($seq - 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_main 
		SET sequence = sequence - 1 
		WHERE idMain = '$id_main'");
		
	}
	if($direction == 'down') {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_main 
		SET sequence = '$seq' 
		WHERE idCourse = '".$_SESSION['idCourse']."' AND sequence = '".($seq + 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_main 
		SET sequence = '".($seq + 1)."' 
		WHERE idMain = '$id_main'");
	}
	jumpTo('index.php?modname=manmenu&op=manmenu&id_main='.$id_main);
}

function fixmenuvoice() {
	checkPerm('mod');
	
	$id_custom = importVar('id_custom', true, 0);
	
	$query = "
	SELECT idMain 
	FROM ".$GLOBALS['prefix_lms']."_menucourse_main 
	WHERE idCourse = '".$_SESSION['idCourse']."' 
	ORDER BY sequence";	
	$reField = mysql_query($query);
	
	$i = 1;
	while(list($id) = mysql_fetch_row($reField)) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_main 
		SET sequence = '".($i++)."' 
		WHERE idMain = '$id'");
	}
	jumpTo('index.php?modname=manmenu&op=manmenu');
}

function manmodule() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$out 		=& $GLOBALS['page'];
	$lang 		=& DoceboLanguage::createInstance('manmenu', 'framework');
	$mo_lang 	=& DoceboLanguage::createInstance('menu_over', 'lms');
	$menu_lang 	=& DoceboLanguage::createInstance('menu_course', 'lms');
	
	$mod_perm 	= checkPerm('mod', true);
	
	// Find main voice info
	$id_main 	= importVar('id_main', true, 0);
	$query_custom = "
	SELECT name 
	FROM ".$GLOBALS['prefix_lms']."_menucourse_main 
	WHERE idMain = '".(int)$id_main."'";
	list($title_main) = mysql_fetch_row(mysql_query($query_custom));
	
	// Find all modules in this voice
	$query_module = "
	SELECT module.idModule, module.default_name, menu.my_name, menu.sequence 
	FROM ".$GLOBALS['prefix_lms']."_menucourse_under AS menu JOIN
		".$GLOBALS['prefix_lms']."_module AS module
	WHERE module.idModule = menu.idModule AND menu.idMain = '".(int)$id_main."' 
	ORDER BY menu.sequence";
	$re_module = mysql_query($query_module);
	$tot_module = mysql_num_rows($re_module);
	
	$used_module = '';
	$query_used_module = "
	SELECT module.idModule 
	FROM ".$GLOBALS['prefix_lms']."_menucourse_under AS menu JOIN 
		".$GLOBALS['prefix_lms']."_module AS module 
	WHERE module.idModule = menu.idModule AND 
		( menu.idCourse = '".$_SESSION['idCourse']."' OR menu.idCourse = 0 )";
	$re_used_module = mysql_query($query_used_module);
	
	while(list($id_mod_used) = mysql_fetch_row($re_used_module)) {
		$used_module .= $id_mod_used.',';
	}
	
	$query_free_module = "
	SELECT idModule, default_name 
	FROM ".$GLOBALS['prefix_lms']."_module AS module 
	WHERE  default_op <> '' AND idModule NOT IN ( ".substr($used_module, 0 , -1)." )";
	$re_free_module = mysql_query($query_free_module);
	
	$tb = new TypeOne(0, $lang->def('_TB_MANMODULE_CAPTION'), $lang->def('_TB_MANMODULE_SUMMARY'));
	
	$content_h 	= array(
		$lang->def('_ORDER'), 
		$lang->def('_TITLE_MODULE'), 
		'<img src="'.getPathImage().'standard/down.gif" title="'.$lang->def('_MOVE_DOWN').'" alt="'.$lang->def('_DOWN').'" />', 
		'<img src="'.getPathImage().'standard/up.gif" title="'.$lang->def('_MOVE_UP').'" alt="'.$lang->def('_UP').'" />');
	$type_h 	= array('image', '', 'image', 'image');
	if($mod_perm) {
		$content_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MODMODULE').'"'
			.' alt="'.$lang->def('_MOD').'" />';
		$type_h[] 	 = 'image';
		
		$content_h[] = '<img src="'.getPathImage().'manmenu/putdown.gif" title="'.$lang->def('_TITLE_CANCMODULE').'"'
			.' alt="'.$lang->def('_ALT_CANC_MODULE').'" />';
		$type_h[] 	 = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($content_h);
	
	$i = 0;
	while(list($id_mod, $name_db, $my_name, $sequence) = mysql_fetch_row($re_module)) {
		$name = ( $my_name != '' ? $my_name : $menu_lang->def($name_db) );
		$strip_name = strip_tags($name);
		$content = array($sequence, $name);
		
		$content[] = ( $i != ($tot_module - 1) ? '<a href="index.php?modname=manmenu&amp;op=mdmodule&amp;id_main='.$id_main.'&amp;id_module='.$id_mod.'"'
				.' title="'.$lang->def('_MOVE_DOWN').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').'" /></a>' : '' );
		$content[] = ( $i != 0 ? '<a href="index.php?modname=manmenu&amp;op=mumodule&amp;id_main='.$id_main.'&amp;id_module='.$id_mod.'"'
				.' title="'.$lang->def('_MOVE_UP').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').'" /></a>' : '' );
		if($mod_perm) {
			
			$content[] = '<a href="index.php?modname=manmenu&amp;op=modmodule&amp;id_main='.$id_main.'&amp;id_module='.$id_mod.'"'
				.' title="'.$lang->def('_TITLE_MODMODULE').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" /></a>';
			
			$content[] = '<a href="index.php?modname=manmenu&amp;op=delmodule&amp;id_main='.$id_main.'&amp;id_module='.$id_mod.'"'
				.' title="'.$lang->def('_TITLE_CANCMODULE').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'manmenu/putdown.gif" alt="'.$lang->def('_ALT_CANC_MODULE').'" /></a>';
		}
		$tb->addBody($content);
		$i++;
	}
	if($mod_perm) {
	
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delmodule]');	
	}
	
	$tb_free = new TypeOne(0, $lang->def('_TB_FREE_MANMODULE_CAPTION'), $lang->def('_TB_FREE_MANMODULE_SUMMARY'));
	$c_free_h 	= array($lang->def('_TITLE_MODULE'));
	$t_free_h 	= array('');
	if($mod_perm) {
		$c_free_h[] = '<img src="'.getPathImage().'manmenu/grab.gif" title="'.$lang->def('_TITLE_GRABMODULE').'"'
			.' alt="'.$lang->def('_ALT_GRAB').'" />';
		$t_free_h[] 	 = 'image';
	}
	$tb_free ->setColsStyle($t_free_h);
	$tb_free ->addHead($c_free_h);
	while(list($id_import_mod, $name_db) = mysql_fetch_row($re_free_module)) {
		$name = $menu_lang->def($name_db);
		$strip_name = strip_tags($name);
		
		$content = array($name);
		if($mod_perm) {
			
			$content[] = '<a href="index.php?modname=manmenu&amp;op=addmodule&amp;id_main='.$id_main.'&amp;id_module='.$id_import_mod.'"'
				.' title="'.$lang->def('_TITLE_GRABMODULE').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'manmenu/grab.gif" alt="'.$lang->def('_ALT_GRAB').'" /></a>';
		}
		$tb_free->addBody($content);
	}
	// print out
	$out->setWorkingZone('content');
	
	$page_title = array(
		'index.php?modname=manmenu&amp;op=manmenu' => $lang->def('_TITLE_MANMENU'), 
		( $mo_lang->isDef($title_main) ? $mo_lang->def($title_main) : $title_main )
	);
	
	$out->add(
		getTitleArea($page_title, 'manmenu')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=manmenu&amp;op=manmenu', $lang->def('_BACK')) );
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case 0 : $out->add(getResultUi($lang->def('_OPERATION_FAILURE')));break;
			case 1 : $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
		} 
	}
	$out->add(
		$tb->getTable()
		.'[ <a href="index.php?modname=manmenu&amp;op=fixmodule&amp;id_main='.$id_main.'" '
			.'title="'.$lang->def('_FIXSEQUENCE_MANMENU_TITLE').'">'
			.$lang->def('_FIXSEQUENCE_MANMENU').'</a> ]'
		.'<br /><br />'
		.( mysql_num_rows($re_free_module) != false ? $tb_free->getTable() : '' ) 
		.'</div>');
}

function editmodule($load = false) {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang 		=& DoceboLanguage::createInstance('manmenu', 'framework');
	$menu_lang =& DoceboLanguage::createInstance('menu_course', 'lms');
	
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$id_main 	= importVar('id_main', true, 0);
	$id_module 	= importVar('id_module', true, 0);
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	$perm		= array();
	
	// Load module info
	$query_module = "
	SELECT module_name, default_name, file_name, class_name 
	FROM ".$GLOBALS['prefix_lms']."_module 
	WHERE idModule = '".$id_module."'";
	list($module_name, $name_db, $file_name, $class_name) = mysql_fetch_row(mysql_query($query_module));
	$module_obj =& createLmsModule($file_name, $class_name);
	
	// Standard name
	$name = ( $menu_lang->isDef($name_db) ? $menu_lang->def($name_db) : $name_db );
	$my_name = '';
	
	$query_module = "
	SELECT default_op 
	FROM ".$GLOBALS['prefix_lms']."_module 
	WHERE idModule = '".$id_module."'";
	list($module_op) = mysql_fetch_row(mysql_query($query_module));
	
	// Load info
	if($load) {
		
		// Find personalized name
		$query_seq = "
		SELECT u.my_name, m.default_op 
		FROM ".$GLOBALS['prefix_lms']."_menucourse_under AS u JOIN
			".$GLOBALS['prefix_lms']."_module AS m 
		WHERE u.idModule = m.idModule AND u.idMain = '".$id_main."' AND u.idModule = '".$id_module."'";
		list($my_name, $def_op) = mysql_fetch_row(mysql_query($query_seq));
		
		// Load actual module permission
		
		$levels = CourseLevel::getLevels();
		$tokens = $module_obj->getAllToken($module_op);
		
		$map_level_idst	 	=& getCourseLevelSt($_SESSION['idCourse']);
		$map_all_role 		=& getModuleRoleSt($module_name, $tokens, TRUE);
		$group_idst_roles 	=& getAllModulesPermissionSt($map_level_idst, $map_all_role);
		$perm				=& fromStToToken($group_idst_roles, $map_all_role);
		
	}
	
	$query_mains = "
	SELECT idMain, name 
	FROM ".$GLOBALS['prefix_lms']."_menucourse_main 
	WHERE idCourse = '".$_SESSION['idCourse']."'
	ORDER BY sequence";
	$re_mains = mysql_query($query_mains);
	while(list($id_db_main, $main_name) = mysql_fetch_row($re_mains)) {
		
		$mains[$id_db_main] = $main_name;
		if($id_db_main == $id_main) $title_main = $main_name;
	}
	
	
	// Form
	$page_title = array(
		'index.php?modname=manmenu&amp;op=manmenu' => $lang->def('_TITLE_MANMENU'), 
		'index.php?modname=manmenu&amp;op=manmodule&amp;id_main='.$id_main => $title_main, 
		( $my_name != '' ? $my_name : $name )
	);
	$out->add(
		getTitleArea($page_title, 'manmenu')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=manmenu&amp;op=manmodule&amp;id_main='.$id_main, $lang->def('_BACK'))
		
		.Form::openForm('module_permission', 
						'index.php?modname=manmenu&amp;op=upmodule&amp;id_main='.$id_main.'&amp;id_module='.$id_module)
		.Form::getHidden('id_main', 'id_main', $id_main)
		.Form::getHidden('id_module', 'id_module', $id_module)
		
		.( $load ? Form::getHidden('load', 'load', '1') : '' )
		
		.Form::getTextfield($lang->def('_MY_NAME'), 'my_name', 'my_name', 255, 
			( $load ? $my_name : '' ) )
		.Form::getDropdown($lang->def('_TITLE_MENUVOICE'), 'new_id_main', 'new_id_main', $mains, $id_main)
		.Form::getBreakRow()
		.$module_obj->getPermissionUi('module_permission', $perm, $module_op)
		.Form::getBreakRow()
		.Form::openButtonSpace()
		.Form::getButton('saveperm', 'saveperm', ( $load ? $lang->def('_SAVE') : $lang->def('_IMPORT') ))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		
		.Form::closeForm()
		.'</div>'
	);
}

function upmodule() {
	checkPerm('mod');
	
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$id_main 	= importVar('id_main', true, 0);
	$new_id_main = importVar('new_id_main', true, 0);
	$id_module 	= importVar('id_module', true, 0);
	
	$lang 		=& DoceboLanguage::createInstance('manmenu', 'framework');
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	
	if(isset($_POST['undo'])) {
		jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$id_main);
	}
	
	// Load module info
	$query_module = "
	SELECT module_name, default_name, file_name, class_name, default_op 
	FROM ".$GLOBALS['prefix_lms']."_module 
	WHERE idModule = '".$id_module."'";
	list($module_name, $name_db, $file_name, $class_name, $def_op) = mysql_fetch_row(mysql_query($query_module));
	$module_obj =& createLmsModule($file_name, $class_name);
	
	//*************************************************************//
	//* Find permission to save or delete *************************//
	//*************************************************************//
	
	$levels 			= CourseLevel::getLevels();
	$all_token 			= $module_obj->getAllToken($def_op);
	$new_token 			= $module_obj->getSelectedPermission($def_op);
	// corresponding of token -> idst role
	$map_idst_token 	=& getModuleRoleSt($module_name, $all_token);
	// corresponding of level -> idst level
	$map_idst_level	 	=& getCourseLevelSt($_SESSION['idCourse']);
	// idst of the selected perm
	$idst_new_perm 		=& fromTokenToSt($new_token, $map_idst_token);
	// old permission of all module
	$idst_old_perm		=& getAllModulesPermissionSt($map_idst_level, array_flip($map_idst_token));
	
	// What to add what to delete
	foreach($levels as $lv => $name_level) {
		
		if(isset($idst_new_perm[$lv])) {
			
			$perm_to_add_idst[$lv] = array_diff_assoc($idst_new_perm[$lv], $idst_old_perm[$lv]);
			
			$perm_to_del_idst[$lv] = array_diff_assoc($idst_old_perm[$lv], $idst_new_perm[$lv]);
		} else {
			
			$perm_to_add_idst[$lv] = array();
			$perm_to_del_idst[$lv] = $idst_old_perm[$lv];
		}
	}
	
	foreach($levels as $lv => $name_level) {
		
		$idlevel = $map_idst_level[$lv];
		foreach($perm_to_add_idst[$lv] as $idrole => $v) {
			
		//Thêm role cho 1 group
			$acl_man->addToRole( $idrole, $idlevel );
		}
		foreach($perm_to_del_idst[$lv] as $idrole => $v) {
			
			$acl_man->removeFromRole( $idrole, $idlevel );
		}
	}
	/*
	echo '<div class="box_evidence" style="float: left;">New<br /><pre>';
	print_r($idst_new_perm);
	echo '</pre></div>'
		.'<div class="box_evidence" style="float: left;">Old<br /><pre>';
	print_r($idst_old_perm);
	echo '</pre></div>';
	
	echo '<div class="box_evidence" style="float: left;">To add<br /><pre>';
	print_r($perm_to_add_idst);
	echo '</pre></div>'
		.'<div class="box_evidence" style="float: left;">To del<br /><pre>';
	print_r($perm_to_del_idst);
	echo '</pre></div>';
	die();*/
	//*************************************************************//
	//* Saving permission setting *********************************//
	//*************************************************************//
	$re = true;
	if(isset($_POST['load'])) {
		
		$re = mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_under
		SET my_name = '".$_POST['my_name']."', 
			idMain = '".$new_id_main."'
		WHERE  	idMain = '".$id_main."' AND  
				idModule = '".$id_module."'");
		
	} else {
		
		$seq = getModuleNextSeq($_POST['id_main']);
		
		if($_POST['my_name'] == $lang->def('_DEFAULT_MY_NAME')) $my_name = '';
		else $my_name = $_POST['my_name'];
		
		// Insert module in the list of this menu custom
		$re = mysql_query("
		INSERT INTO ".$GLOBALS['prefix_lms']."_menucourse_under 
		( idCourse, idMain, idModule, sequence, my_name ) VALUES 
		( '".$_SESSION['idCourse']."', '".$new_id_main."', '".$id_module."', '".$seq."', '".$my_name."' ) ");
	}
	$GLOBALS['current_user']->loadUserSectionST();
	$GLOBALS['current_user']->SaveInSession();
	
	jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$new_id_main.'&result='.( $re ? 1 : 0 ));
}

function removeModule($id_module, $id_main, $id_course) {
	
	$acl_man 		=& $GLOBALS['current_user']->getAclManager();
	
	// Load module info
	$query_module = "
	SELECT module_name, default_name, file_name, class_name, default_op 
	FROM ".$GLOBALS['prefix_lms']."_module 
	WHERE idModule = '".$id_module."'";
	list($module_name, $name_db, $file_name, $class_name, $def_op) = mysql_fetch_row(mysql_query($query_module));
	$module_obj =& createLmsModule($file_name, $class_name);
	
	$levels 			= CourseLevel::getLevels();
	$all_token 			= $module_obj->getAllToken();
	// corresponding of token -> idst role
	$map_idst_token 	=& getModuleRoleSt($module_name, $all_token);
	// corresponding of level -> idst level
	$map_idst_level	 	=& getCourseLevelSt($id_course);
	// old permission of all module
	$actual_perm		=& getAllModulesPermissionSt($map_idst_level, array_flip($map_idst_token));
	
	$re = true;
	foreach($levels as $lv => $name_level) {
		
		$idlevel = $map_idst_level[$lv];
		foreach($actual_perm[$lv] as $idrole => $v) {
			
			$acl_man->removeFromRole( $idrole, $idlevel );
		}
	}
	if($re) {
		$re = mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_menucourse_under 
		WHERE idMain = '".(int)$id_main."' AND idModule = '".(int)$id_module."' AND idCourse = '".$_SESSION['idCourse']."'");
	}
	return $re;
}

function delmodule() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$id_main 	= importVar('id_main', true, 0);
	$id_module 	= importVar('id_module', true, 0);
	
	$lang 		=& DoceboLanguage::createInstance('manmenu', 'framework');
	$menu_lang 	=& DoceboLanguage::createInstance('menu_course', 'lms');
	
	if(isset($_POST['undo'])) {
		
		jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$id_main);
	} 
	
	if(isset($_POST['confirm']) || isset($_GET['confirm'])) {
		
		$re = removeModule($id_module, $id_main, $_SESSION['idCourse']);
		
		$GLOBALS['current_user']->loadUserSectionST();
		$GLOBALS['current_user']->SaveInSession();
		
		jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$id_main.'&result='.( $re ? 1 : 0 ));
	} else {
		
		// Load module info
		$query_module = "
		SELECT default_name 
		FROM ".$GLOBALS['prefix_lms']."_module 
		WHERE idModule = '".$id_module."'";
		list($name_db) = mysql_fetch_row(mysql_query($query_module));
		
		$query_custom = "
		SELECT name 
		FROM ".$GLOBALS['prefix_lms']."_menucourse_main
		WHERE idMain = '".$id_main."'";
		list($main_title) = mysql_fetch_row(mysql_query($query_custom));
		
		$name = ( $menu_lang->isDef($name_db) ? $menu_lang->def($name_db) : $name_db );
		
		$page_title = array( 
			'index.php?modname=manmenu&amp;op=manmenu' => $lang->def('_TITLE_MANMENU'), 
			'index.php?modname=manmenu&amp;op=manmodule&amp;id_main='.$id_main => $main_title, 
			$lang->def('_TITLE_CANCMODULE').' : '.$name
		);
		$strip_name = strip_tags($name);
		$out->add(
			getTitleArea($page_title, 'manmenu')
			.'<div class="std_block">'
			.Form::openForm('delcustom_form', 'index.php?modname=manmenu&amp;op=delmodule')
			.Form::getHidden('id_main', 'id_main', $id_main)
			.Form::getHidden('id_module', 'id_module', $id_module)
			.getDeleteUi( $lang->def('_AREYOUSURE_MODULE'), 
						'<span class="text_bold">'.$lang->def('_TITLE_MODULE').' : </span>'.$name, 
						false, 
						'confirm', 
						'undo' )
			.Form::closeForm()
			.'</div>');
	}
}

function movemodule($direction) {
	checkPerm('mod');
	
	$id_main 	= importVar('id_main', true, 0);
	$id_module	= importVar('id_module', true, 0);
	
	list($seq) = mysql_fetch_row(mysql_query("
	SELECT sequence 
	FROM ".$GLOBALS['prefix_lms']."_menucourse_under 
	WHERE idMain = '".$id_main."' AND idModule = '".$id_module."'"));
	
	if($direction == 'up') {
		
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_under 
		SET sequence = '$seq' 
		WHERE idMain = '".$id_main."' AND sequence = '".($seq - 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_under 
		SET sequence = sequence - 1 
		WHERE idMain = '".$id_main."' AND idModule = '".$id_module."'");
	}
	if($direction == 'down') {
		
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_under 
		SET sequence = '$seq' 
		WHERE idMain = '".$id_main."' AND sequence = '".($seq + 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_under 
		SET sequence = '".($seq + 1)."' 
		WHERE idMain = '".$id_main."' AND idModule = '".$id_module."'");
	}
	jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$id_main);
}

function fixmodule() {
	checkPerm('mod');
	
	$id_main 	= importVar('id_main', true, 0);
	$id_custom 	= importVar('id_custom', true, 0);
	
	$query = "
	SELECT idModule 
	FROM ".$GLOBALS['prefix_lms']."_menucourse_under 
	WHERE idMain = '$id_main'
	ORDER BY sequence";	
	$reField = mysql_query($query);
	
	$i = 1;
	while(list($id) = mysql_fetch_row($reField)) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucourse_under 
		SET sequence = '".($i++)."' 
		WHERE idModule = '$id'");
	}
	jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$id_main.'&id_custom='.$id_custom);
}

function manmenuDispatch($op) {
	
	switch($op) {
		//main menu
		case "manmenu" : {
			manmenu();
		};break;
		case "addmenuvoice" : {
			editmenuvoice();
		};break;
		case "modmenuvoice" : {
			editmenuvoice(true);
		};break;
		case "savemenuvoice" : {
			savemenuvoice();
		};break;
		case "delmenuvoice" : {
			delmenuvoice();
		};break;
		case "mdmenuvoice" : {
			movemenuvoice('down');
		};break;
		case "mumenuvoice" : {
			movemenuvoice('up');
		};break;
		case "fixmenuvoice" : {
			fixmenuvoice();
		};break;
		
		case "manmodule" : {
			manmodule();
		};break;
		case "addmodule" : {
			editmodule();
		};break;
		case "modmodule" : {
			editmodule(true);
		};break;
		case "upmodule" : {
			upmodule();
		};break;
		case "delmodule" : {
			delmodule();
		};break;
		case "mdmodule" : {
			movemodule('down');
		};break;
		case "mumodule" : {
			movemodule('up');
		};break;
		case "fixmodule" : {
			fixmodule();
		};break;
	}
}

?>