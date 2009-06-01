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
 * @version  $Id: manmenu.php 573 2006-08-23 09:38:54Z fabio $
 * @category Course menu managment
 * @author	 Fabio Pirovano <fabio [at] docebo [dot] com>
 */

if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

require_once($GLOBALS['where_lms'].'/lib/lib.levels.php');
require_once($GLOBALS['where_lms'].'/lib/lib.manmenu.php');

/**
 * User interface functions
 */

function mancustom() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$out 		=& $GLOBALS['page'];
	$lang 		=& DoceboLanguage::createInstance('manmenu');
	
	$mod_perm 	= checkPerm('mod', true);
	
	$query = "
	SELECT idCustom, title, description 
	FROM ".$GLOBALS['prefix_lms']."_menucustom 
	ORDER BY title";
	$re_custom = mysql_query($query);
	
	$out->setWorkingZone('content');
	$out->add(
		getTitleArea($lang->def('_TITLE_MANMENU'), 'manmenu')
		.'<div class="std_block">');
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case 0 : $out->add(getResultUi($lang->def('_OPERATION_FAILURE')));break;
			case 1 : $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
		} 
	}
	$tb = new TypeOne(0, $lang->def('_TB_CM_CAPTION'), $lang->def('_TB_CM_SUMMARY'));
	$content_h 	= array(
		$lang->def('_TITLE'), $lang->def('_DESCRIPTION'), 
		'<img src="'.getPathImage().'standard/modelem.gif" title="'.$lang->def('_MODMENU').'" '
			.'alt="'.$lang->def('_MODMENU').'" />');
	$type_h 	= array('', '', 'image');
	if($mod_perm) {
		$content_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MODCUSTOMINFO').'" '
			.'alt="'.$lang->def('_TITLE_MODCUSTOMINFO').'" />';
		$type_h[] 	 = 'image';
		
		$content_h[] = '<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_TITLE_DELCUSTOM').'" '
			.'alt="'.$lang->def('_TITLE_DELCUSTOM').'" />';
		$type_h[] 	 = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($content_h);
	while(list($id_custom, $title_custom, $text_custom) = mysql_fetch_row($re_custom)) {
		$title_custom = strip_tags($title_custom);
		$content = array(
			'<a href="index.php?modname=manmenu&amp;op=manmenu&amp;id_custom='.$id_custom.'" '
				.' title="'.$lang->def('_MODMENU').' : '.$title_custom.'">'.$title_custom.'</a>', 
			$text_custom, 
			'<a href="index.php?modname=manmenu&amp;op=manmenu&amp;id_custom='.$id_custom.'" '
				.' title="'.$lang->def('_MODMENU').' : '.$title_custom.'">'
				.'<img src="'.getPathImage().'standard/modelem.gif" alt="'.$lang->def('_MODMENU').' : '.$title_custom.'" /></a>');
		if($mod_perm) {
			
			$content[] = '<a href="index.php?modname=manmenu&amp;op=modcustom&amp;id_custom='.$id_custom.'"'
				.'title="'.$lang->def('_TITLE_MODCUSTOMINFO').' : '.$title_custom.'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$title_custom.'" /></a>';
			
			$content[] = '<a href="index.php?modname=manmenu&amp;op=delcustom&amp;id_custom='.$id_custom.'"'
				.' title="'.$lang->def('_TITLE_DELCUSTOM').' : '.$title_custom.'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$title_custom.'" /></a>';
		}
		$tb->addBody($content);
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delcustom]');
	
	if($mod_perm) {
		
		$tb->addActionAdd('<a href="index.php?modname=manmenu&amp;op=addcustom" title="'.$lang->def('_TITLE_ADDCUSTOM').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_TITLE_ADDCUSTOM').'" />'
			.$lang->def('_TITLE_ADDCUSTOM').'</a>');
	}
	$out->add($tb->getTable());
	
	$out->add('</div>');
}

function editcustom($load = false) {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$out 		=& $GLOBALS['page'];
	$lang 		=& DoceboLanguage::createInstance('manmenu');
	$mod_perm 	= checkPerm('mod', true);
	
	$page_title = array(
		'index.php?modname=manmenu&amp;op=mancustom' => $lang->def('_TITLE_MANMENU')
	);
	
	if($load == false) {
		
		$title = $lang->def('_NOTITLE');
		$text  = $lang->def('_DESCRIPTION');
		
		$page_title[] = $lang->def('_ADDCUSTOM');
	} else {
		
		$id_custom = importVar('id_custom');
		$query_custom = "
		SELECT title, description 
		FROM ".$GLOBALS['prefix_lms']."_menucustom
		WHERE idCustom = '".$id_custom."'";
		list($title, $text) = mysql_fetch_row(mysql_query($query_custom));
		
		$page_title[] = $lang->def('_TITLE_MODCUSTOMINFO');
	}
	$out->add(
		getTitleArea($page_title, 'manmenu')
		.'<div class="std_block">'
		.Form::openForm('addcustom_form', 'index.php?modname=manmenu&amp;op=savecustom')
		.Form::openElementSpace()
		.Form::getTextfield($lang->def('_TITLE'), 'title', 'title', 255, $title)
	);
	if($load != false) {
		
		$out->add(Form::getHidden('id_custom', 'id_custom', $id_custom));
	} else {
		
		$custom = array(0 => $lang->def('_NOTHING'));
		$query = "
		SELECT idCustom, title 
		FROM ".$GLOBALS['prefix_lms']."_menucustom 
		ORDER BY title";
		$re_custom = mysql_query($query);
		while(list($id_c, $title_c) = mysql_fetch_row($re_custom)) {
			$custom[$id_c] = $title_c;
		}
		$out->add(Form::getDropdown($lang->def('_FROM_CUSTOM'), 'from_custom', 'from_custom', $custom, '0'));
	}
	$out->add(
		Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $text)
		
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('addcustom', 'addcustom', ( $load == false ? $lang->def('_INSERT') : $lang->def('_SAVE') ))
		.Form::getButton('undocustom', 'undocustom', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>'
	);
}

function savecustom() {
	checkPerm('mod');
	
	$re = true;
	if(isset($_POST['undocustom'])) {
		jumpTo('index.php?modname=manmenu&op=mancustom');
	}
	if(isset($_POST['id_custom'])) {
		
		$re = mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom
		SET title = '".$_POST['title']."', 
			description = '".$_POST['description']."' 
		WHERE idCustom = '".$_POST['id_custom']."'");
	} else {
		
		$re = mysql_query("
		INSERT INTO ".$GLOBALS['prefix_lms']."_menucustom
		( title, description ) VALUES 
		( '".$_POST['title']."', '".$_POST['description']."' )");
		
		list($id_custom) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		
		$acl_man = $GLOBALS['current_user']->getAclManager();
		$levels = CourseLevel::getLevels();
		foreach($levels as $key => $value) {
			$idst = $acl_man->registerGroup( '/lms/custom/'.$id_custom.'/'.$key, 
									'for custom lms menu', 
									true );
			$new_group_idst[$key] = $idst;
		}
		if($_POST['from_custom'] != 0) {
			$id_custom_from = $_POST['from_custom'];
			
			// Copy main areas --------------------------------------
			$re_main = mysql_query("
			SELECT idMain, sequence, name, image 
			FROM ".$GLOBALS['prefix_lms']."_menucustom_main
			WHERE idCustom = '".$id_custom_from."'");
			
			$main_values = array();
			$query_ins_main = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_menucustom_main (idMain, idCustom, sequence, name, image ) VALUES";
			while(list($id_main, $seq, $name, $image) = mysql_fetch_row($re_main)) {
				
				if(!mysql_query($query_ins_main."( '', '".$id_custom."','".$seq."', '".$name."', '".$image."')") ) {
					$map_main_id[$id_main] = false;
				} else {
					list($map_main_id[$id_main]) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
				}
			}
			
			// copy module ------------------------------------------
			$re_module = mysql_query("
			SELECT idModule, idMain, sequence, my_name 
			FROM ".$GLOBALS['prefix_lms']."_menucustom_under
			WHERE idCustom = '".$id_custom_from."'");
			
			$module_values = array();
			$query_ins_module = "
			INSERT INTO ".$GLOBALS['prefix_lms']."_menucustom_under ( idCustom, idModule, idMain, sequence, my_name ) VALUES";
			while(list($id_module, $id_main, $seq, $my_name) = mysql_fetch_row($re_module)) {
				
				if(isset($map_main_id[$id_main]) && ($map_main_id[$id_main] !== false)) {
					
					$module_values[] = "('".$id_custom."', '".$id_module."', '".$map_main_id[$id_main]."', '".$seq."', '".$my_name."')";
				}
			}
			$query_ins_module .= implode(',', $module_values);
			if(!mysql_query($query_ins_module)) {
				
				jumpTo('index.php?modname=manmenu&op=mancustom&result=0');
			}
			
			//copy module permission
			$group_of_from 	=& getCustomLevelSt($id_custom_from);
			$perm_form 		=& getAllModulesPermissionSt($group_of_from);
			$levels  		=  CourseLevel::getLevels();
			foreach($levels as $lv => $name_level) {
				
				foreach($perm_form[$lv] as $idrole => $v) {
					
					$acl_man->addToRole( $idrole, $new_group_idst[$lv] );
				}
			}
			
		}
	}
	jumpTo('index.php?modname=manmenu&op=mancustom&result='.( $re ? 1 : 0 ));
}

function delcustom() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang 		=& DoceboLanguage::createInstance('manmenu');
	$acl_man	=& $GLOBALS['current_user']->getAclManager();
	
	$id_custom = get_req('id_custom', DOTY_INT, 0);
	
	if(get_req('confirm', DOTY_INT, 0) == 1) {
		
		$re = true;
		$re_modules = mysql_query("
		SELECT idModule, idMain 
		FROM ".$GLOBALS['prefix_lms']."_menucustom_under 
		WHERE idCustom = '".$id_custom."'");
		while(list($id_module, $id_main) = mysql_fetch_row($re_modules)) {
			
			$re =& removeModule($id_module, $id_main, $_POST['id_custom']);
		}
		if(!$re) jumpTo('index.php?modname=manmenu&op=manmenu&id_custom='.$_POST['id_custom'].'&result=0');
		
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_menucustom_under 
		WHERE idCustom = '".$id_custom."'") )
			jumpTo('index.php?modname=manmenu&op=manmenu&id_custom='.$_POST['id_custom'].'&result=0');
		
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_menucustom_main 
		WHERE idCustom = '".$id_custom."'"))
			jumpTo('index.php?modname=manmenu&op=manmenu&id_custom='.$_POST['id_custom'].'&result=0');
		
		// Remove group
		$groups =& getCustomLevelSt($_POST['id_custom']);
		foreach($groups as $lv => $idst) {
			$acl_man->deleteGroup($idst);
		}
		$re = mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_menucustom 
		WHERE idCustom = '".$id_custom."'");
		
		jumpTo('index.php?modname=manmenu&op=mancustom&result='.( $re ? 1 : 0 ));
	}
}

function manmenu() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$out 		=& $GLOBALS['page'];
	$lang 		=& DoceboLanguage::createInstance('manmenu');
	$mo_lang 	=& DoceboLanguage::createInstance('menu_over', 'lms');
	
	if(isset($_GET['id_custom'])) {
		
		$id_custom 	= importVar('id_custom', true, 0);
	} elseif(isset($_GET['id_main'])) {
		
		$id_main 	= importVar('id_main', true, 0);
		$id_custom 	= getIdCustomFromMain($id_main);
	}
	$mod_perm 	= checkPerm('mod', true);
	
	$query = "
	SELECT title 
	FROM ".$GLOBALS['prefix_lms']."_menucustom 
	WHERE idCustom = '".(int)$id_custom."'";
	list($title_custom) = mysql_fetch_row(mysql_query($query));
	
	$query_voice = "
	SELECT idMain, name, image 
	FROM ".$GLOBALS['prefix_lms']."_menucustom_main 
	WHERE idCustom = '".(int)$id_custom."'
	ORDER BY sequence";
	$re_voice = mysql_query($query_voice);
	$tot_voice = mysql_num_rows($re_voice);
	
	$tb = new TypeOne(0, $lang->def('_TB_MANMENU_CAPTION'), $lang->def('_TB_MANMENU_SUMMARY'));
	$content_h 	= array(
		'<img src="'.getPathImage().'manmenu/symbol.gif" title="'.$lang->def('_SYMBOL_TITLE').'" alt="'.$lang->def('_SYMBOL_TITLE').'" />', 
		$lang->def('_TITLE_MENUVOICE'), 
		'<img src="'.getPathImage().'standard/down.gif" title="'.$lang->def('_MOVE_DOWN').'" alt="'.$lang->def('_DOWN').'" />', 
		'<img src="'.getPathImage().'standard/up.gif" title="'.$lang->def('_MOVE_UP').'" alt="'.$lang->def('_UP').'" />', 
		'<img src="'.getPathImage().'standard/modelem.gif" title="'.$lang->def('_MODMODULE').'" alt="'.$lang->def('_MODMODULE').'" />');
	$type_h 	= array('image', '', 'image', 'image', 'image');
	if($mod_perm) {
		$content_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MODMENUVOICE').'" alt="'.$lang->def('_TITLE_MODMENUVOICE').'" />';
		$type_h[] 	 = 'image';
	
		$content_h[] = '<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_TITLE_DELMENUVOICE').'" alt="'.$lang->def('_TITLE_DELMENUVOICE').'" />';
		$type_h[] 	 = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($content_h);
	$i = 0;
	while(list($id_m, $name, $image) = mysql_fetch_row($re_voice)) {
		
		$strip_name = strip_tags(( $mo_lang->isDef($name) ? $mo_lang->def($name) : $name ));
		$content = array(
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
				.'<img src="'.getPathImage().'standard/modelem.gif" alt="'.$lang->def('_MODMODULE').' : '.$strip_name.'" /></a>';
		if($mod_perm) {
			// Modify voice
			$content[] = '<a href="index.php?modname=manmenu&amp;op=modmenuvoice&amp;id_main='.$id_m.'"'
				.' title="'.$lang->def('_TITLE_MODMENUVOICE').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_TITLE_MODMENUVOICE').' : '.$strip_name.'" /></a>';
			// Delete voice
			$content[] = '<a href="index.php?modname=manmenu&amp;op=delmenuvoice&amp;id_main='.$id_m.'"'
				.' title="'.$lang->def('_TITLE_DELMENUVOICE').' : '.$strip_name.'">'
				.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_TITLE_DELMENUVOICE').' : '.$strip_name.'" /></a>';
		}
		$tb->addBody($content);
		$i++;
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delmenuvoice]');
	
	if($mod_perm) {
		
		$tb->addActionAdd('<a href="index.php?modname=manmenu&amp;op=addmenuvoice&amp;id_custom='.$id_custom.'"'
			.' title="'.$lang->def('_TITLE_ADDMENUVOICE').'">'
			.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" />'
			.$lang->def('_TITLE_ADDMENUVOICE').'</a>');
	}
	
	// print out
	$out->setWorkingZone('content');
	
	$page_title = array(
		'index.php?modname=manmenu&amp;op=mancustom' => $lang->def('_TITLE_MANMENU'), 
		$title_custom
	);
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
	$out->add(
		$tb->getTable()
		.'[ <a href="index.php?modname=manmenu&amp;op=fixmenuvoice&amp;id_custom='.$id_custom.'" '
			.'title="'.$lang->def('_FIXSEQUENCE_MANMENU_TITLE').'">'
			.$lang->def('_FIXSEQUENCE_MANMENU_TITLE').'</a> ]'
		.'</div>');
}

function editmenuvoice($load = false) {
		checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$out 		=& $GLOBALS['page'];
	$lang 		=& DoceboLanguage::createInstance('manmenu');
	$mo_lang 	=& DoceboLanguage::createInstance('menu_over');
	
	// Find images
	$all_images = array();
	$templ = dir(getPathImage('lms').'menu/');
	while($elem = $templ->read()) {
		if(ereg('.gif', $elem)) $all_images[$elem] = $elem;
	}
	closedir($templ->handle);
	
	if($load == false) {
		
		$id_custom = importVar('id_custom', true, 0);
		$name = $lang->def('_NOMAINVOICE');
		$image = 'blank.gif';
	} else {
		
		$id_main = importVar('id_main', true, 0);
		$query_custom = "
		SELECT idCustom, name, image 
		FROM ".$GLOBALS['prefix_lms']."_menucustom_main
		WHERE idMain = '".$id_main."'";
		list($id_custom, $name, $image) = mysql_fetch_row(mysql_query($query_custom));
	}
	
	
	$query_custom = "
	SELECT title 
	FROM ".$GLOBALS['prefix_lms']."_menucustom 
	WHERE idCustom = '".$id_custom."'";
	list($custom_name) = mysql_fetch_row(mysql_query($query_custom));
	$page_title = array(
		'index.php?modname=manmenu&amp;op=mancustom' => $lang->def('_TITLE_MANMENU'), 
		'index.php?modname=manmenu&amp;op=manmenu&amp;id_custom='.$id_custom => $custom_name, 
		( $load ? $lang->def('_TITLE_MODMENUVOICE') : $lang->def('_TITLE_ADDMENUVOICE') )
	);
	$out->add(
		getTitleArea($page_title, 'manmenu')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=manmenu&amp;op=manmenu'
			.( $load == false ? '&amp;id_custom='.$id_custom : '&amp;id_main='.$id_main ), $lang->def('_BACK'))
		.Form::openForm('addmenuvoice_form', 'index.php?modname=manmenu&amp;op=savemenuvoice')
		.Form::openElementSpace()
		.Form::getTextfield($lang->def('_TITLE'), 'name', 'name', 255, $name)
	);
	if($load == false) {
		
		$out->add(Form::getHidden('id_custom', 'id_custom', $id_custom));
	} else {
		
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
				imgpreview.alt = imgselect.options[imgselect.selectedIndex].value;
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
		.'</div>'
	);
}

function savemenuvoice() {
	$re = true;
	if(isset($_POST['undomenuvoice'])) {
		
		if(isset($_POST['id_main'])) {
			jumpTo('index.php?modname=manmenu&op=manmenu&id_main='.$_POST['id_main']);
		} else {
			jumpTo('index.php?modname=manmenu&op=manmenu&id_custom='.$_POST['id_custom']);
		}
	}
	if(isset($_POST['id_main'])) {
		
		checkPerm('mod');
		
		$re = mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_main 
		SET name = '".$_POST['name']."', 
			image = '".$_POST['image']."' 
		WHERE idMain = '".$_POST['id_main']."'");
		
		jumpTo('index.php?modname=manmenu&op=manmenu&id_main='.$_POST['id_main'].'&result='.( $re ? 1 : 0 ));
	} elseif(isset($_POST['id_custom'])) {
		
		checkPerm('mod');
		
		$query_seq = "
		SELECT MAX(sequence)
		FROM ".$GLOBALS['prefix_lms']."_menucustom_main 
		WHERE idCustom = '".(int)$_POST['id_custom']."'";
		list($seq) = mysql_fetch_row(mysql_query($query_seq));
		++$seq;
		
		$re = mysql_query("
		INSERT INTO ".$GLOBALS['prefix_lms']."_menucustom_main 
		( idCustom, name, image, sequence ) VALUES 
		( '".$_POST['id_custom']."', '".$_POST['name']."', '".$_POST['image']."', '".$seq."' )");
		
		jumpTo('index.php?modname=manmenu&op=manmenu&id_custom='.$_POST['id_custom'].'&result='.( $re ? 1 : 0 ));
	}
}

function delmenuvoice() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$lang 		=& DoceboLanguage::createInstance('manmenu');
	$mo_lang 	=& DoceboLanguage::createInstance('menu_over');
	
	$id_main = get_req('id_main', DOTY_INT, 0);
	
	$query_custom = "
	SELECT idCustom, name, image 
	FROM ".$GLOBALS['prefix_lms']."_menucustom_main 
	WHERE idMain = '".$id_main."'";
	list($id_custom, $name_db, $image) = mysql_fetch_row(mysql_query($query_custom));
	
	if(get_req('confirm', DOTY_INT, 0) == 1) {
		
		$re = true;
		$re_modules = mysql_query("
		SELECT idModule 
		FROM ".$GLOBALS['prefix_lms']."_menucustom_under 
		WHERE idMain = '".$id_main."'");
		while(list($id_module) = mysql_fetch_row($re_modules)) {
			
			$re &= removeModule($id_module, $id_main, $id_custom);
		}
		if(!$re) jumpTo('index.php?modname=manmenu&op=manmenu&id_custom='.$id_custom.'&result=0');
		
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_menucustom_under 
		WHERE idMain = '".$id_main."'"))
			jumpTo('index.php?modname=manmenu&op=manmenu&id_custom='.$id_custom.'&result=0');
		
		$re = mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_menucustom_main 
		WHERE idMain = '".$id_main."'");
		jumpTo('index.php?modname=manmenu&op=manmenu&id_custom='.$id_custom.'&result='.( $re ? 1 : 0 ));
	}
}

function movemenuvoice($direction) {
	checkPerm('mod');
	
	$id_main = importVar('id_main', true, 0);
	
	list($id_custom, $seq) = mysql_fetch_row(mysql_query("
	SELECT idCustom, sequence 
	FROM ".$GLOBALS['prefix_lms']."_menucustom_main 
	WHERE idMain = '$id_main'"));
	
	if($direction == 'up') {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_main 
		SET sequence = '$seq' 
		WHERE idCustom = '".$id_custom."' AND sequence = '".($seq - 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_main 
		SET sequence = sequence - 1 
		WHERE idMain = '$id_main'");
		
	}
	if($direction == 'down') {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_main 
		SET sequence = '$seq' 
		WHERE idCustom = '".$id_custom."' AND sequence = '".($seq + 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_main 
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
	FROM ".$GLOBALS['prefix_lms']."_menucustom_main 
	WHERE idCustom = '$id_custom'
	ORDER BY sequence";	
	$reField = mysql_query($query);
	
	$i = 1;
	while(list($id) = mysql_fetch_row($reField)) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_main 
		SET sequence = '".($i++)."' 
		WHERE idMain = '$id'");
	}
	jumpTo('index.php?modname=manmenu&op=manmenu&id_custom='.$id_custom);
}

function manmodule() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$out 		=& $GLOBALS['page'];
	$lang 		=& DoceboLanguage::createInstance('manmenu');
	$mo_lang 	=& DoceboLanguage::createInstance('menu_over', 'lms');
	$menu_lang 	=& DoceboLanguage::createInstance('menu_course', 'lms');
	
	$mod_perm 	= checkPerm('mod', true);
	
	// Find main voice info
	$id_main 	= importVar('id_main', true, 0);
	$query_custom = "
	SELECT idCustom, name 
	FROM ".$GLOBALS['prefix_lms']."_menucustom_main 
	WHERE idMain = '".(int)$id_main."'";
	list($id_custom, $title_main) = mysql_fetch_row(mysql_query($query_custom));
	
	// Find menu custom info
	$query = "
	SELECT title 
	FROM ".$GLOBALS['prefix_lms']."_menucustom 
	WHERE idCustom = '".(int)$id_custom."'";
	list($title_custom) = mysql_fetch_row(mysql_query($query));
	
	// Find all modules in this voice
	$query_module = "
	SELECT module.idModule, module.default_name, menu.my_name 
	FROM ".$GLOBALS['prefix_lms']."_menucustom_under AS menu JOIN
		".$GLOBALS['prefix_lms']."_module AS module
	WHERE module.idModule = menu.idModule AND menu.idMain = '".(int)$id_main."' 
	ORDER BY menu.sequence";
	$re_module = mysql_query($query_module);
	$tot_module = mysql_num_rows($re_module);
	
	$used_module = '';
	$query_used_module = "
	SELECT module.idModule 
	FROM ".$GLOBALS['prefix_lms']."_menucustom_under AS menu JOIN 
		".$GLOBALS['prefix_lms']."_module AS module 
	WHERE module.idModule = menu.idModule AND 
		( menu.idCustom = '".(int)$id_custom."' OR menu.idCustom = 0 )";
	$re_used_module = mysql_query($query_used_module);
	
	while(list($id_mod_used) = mysql_fetch_row($re_used_module)) {
		$used_module .= $id_mod_used.',';
	}
	
	$query_free_module = "
	SELECT idModule, default_name 
	FROM ".$GLOBALS['prefix_lms']."_module AS module 
	WHERE default_op <> '' "
	." AND module_info = ''";
	if($used_module != '') $query_free_module .= " AND idModule NOT IN ( ".substr($used_module, 0 , -1)." )";
	$re_free_module = mysql_query($query_free_module);
	
	$tb = new TypeOne(0, $lang->def('_TB_MANMODULE_CAPTION'), $lang->def('_TB_MANMODULE_SUMMARY'));
	
	$content_h 	= array(
		$lang->def('_TITLE_MODULE'), 
		'<img src="'.getPathImage().'standard/down.gif" title="'.$lang->def('_MOVE_DOWN').'" alt="'.$lang->def('_DOWN').'" />', 
		'<img src="'.getPathImage().'standard/up.gif" title="'.$lang->def('_MOVE_UP').'" alt="'.$lang->def('_UP').'" />');
	$type_h 	= array('', 'image', 'image');
	if($mod_perm) {
		$content_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MODMODULE').'"�'
			.' alt="'.$lang->def('_TITLE_MODMODULE').'" />';
		$type_h[] 	 = 'image';
		$content_h[] = '<img src="'.getPathImage().'manmenu/putdown.gif" title="'.$lang->def('_TITLE_CANCMODULE').'"'
			.' alt="'.$lang->def('_TITLE_CANCMODULE').'" />';
		$type_h[] 	 = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($content_h);
	
	$i = 0;
	while(list($id_mod, $name_db, $my_name) = mysql_fetch_row($re_module)) {
		$name = ( $my_name != '' ? $my_name : $menu_lang->def($name_db) );
		$strip_name = strip_tags($name);
		$content = array($name);
		
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
				.'<img src="'.getPathImage().'manmenu/putdown.gif" alt="'.$lang->def('_TITLE_CANCMODULE').'" /></a>';
		}
		$tb->addBody($content);
		$i++;
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delmodule]');
	
	$tb_free = new TypeOne(0, $lang->def('_TB_FREE_MANMODULE_CAPTION'), $lang->def('_TB_FREE_MANMODULE_SUMMARY'));
	$c_free_h 	= array($lang->def('_TITLE_MODULE'));
	$t_free_h 	= array('');
	if($mod_perm) {
		$c_free_h[] = '<img src="'.getPathImage().'manmenu/grab.gif" title="'.$lang->def('_TITLE_GRABMODULE').'"'
			.' alt="'.$lang->def('_TITLE_GRABMODULE').'" />';
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
				.'<img src="'.getPathImage().'manmenu/grab.gif" alt="'.$lang->def('_TITLE_GRABMODULE').'" /></a>';
		}
		$tb_free->addBody($content);
	}
	// print out
	
	$page_title = array(
		'index.php?modname=manmenu&amp;op=mancustom' => $lang->def('_TITLE_MANMENU'), 
		'index.php?modname=manmenu&amp;op=manmenu&amp;id_custom='.$id_custom => $title_custom, 
		( $mo_lang->isDef($title_main) ? $mo_lang->def($title_main) : $title_main )
	);
	$out->setWorkingZone('content');
	$out->add(
		getTitleArea($page_title, 'manmenu')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=manmenu&amp;op=manmenu&amp;id_custom='.$id_custom, $lang->def('_BACK')) );
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case 0 : $out->add(getResultUi($lang->def('_OPERATION_FAILURE')));break;
			case 1 : $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
		} 
	}
	$out->add(
		$tb->getTable()
		.'[ <a href="index.php?modname=manmenu&amp;op=fixmodule&amp;id_main='.$id_main.'&amp;id_custom='.$id_custom.'" '
			.'title="'.$lang->def('_FIXSEQUENCE_MANMENU_TITLE').'">'
			.$lang->def('_FIXSEQUENCE_MANMENU_TITLE').'</a> ]'
		.'<br /><br />'
		.( mysql_num_rows($re_free_module) != false ? $tb_free->getTable() : '' ) 
		.'</div>');
}

function editmodule($load = false) {
	if($load) checkPerm('mod');
	else checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('manmenu');
	$menu_lang =& DoceboLanguage::createInstance('menu_course', 'lms');
	
	$out 		=& $GLOBALS['page'];
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
	
	// Load info
	
	$query_module = "
	SELECT default_op 
	FROM ".$GLOBALS['prefix_lms']."_module 
	WHERE idModule = '".$id_module."'";
	list($module_op) = mysql_fetch_row(mysql_query($query_module));
	
	if($load) {
		
		// Find personalized name
		$query_seq = "
		SELECT idCustom, my_name
		FROM ".$GLOBALS['prefix_lms']."_menucustom_under 
		WHERE idMain = '".$id_main."' AND idModule = '".$id_module."'";
		list($id_custom, $my_name) = mysql_fetch_row(mysql_query($query_seq));
		
		// Load actual module permission
		
		$levels = CourseLevel::getLevels();
		$tokens = $module_obj->getAllToken($module_op);
		
		$map_level_idst	 	=& getCustomLevelSt($id_custom);
		$map_all_role 		=& getModuleRoleSt($module_name, $tokens, TRUE);
		$group_idst_roles 	=& getAllModulesPermissionSt($map_level_idst, $map_all_role);
		$perm				=& fromStToToken($group_idst_roles, $map_all_role);
		
	}
	
	// Find personalized name
	$id_custom = getIdCustomFromMain($id_main);
	
	$query_custom = "
	SELECT title 
	FROM ".$GLOBALS['prefix_lms']."_menucustom 
	WHERE idCustom = '".$id_custom."'";
	list($custom_name) = mysql_fetch_row(mysql_query($query_custom));
	
	$query_mains = "
	SELECT idMain, name 
	FROM ".$GLOBALS['prefix_lms']."_menucustom_main 
	WHERE idCustom = '".$id_custom."'
	ORDER BY sequence";
	$re_mains = mysql_query($query_mains);
	while(list($id_db_main, $main_name) = mysql_fetch_row($re_mains)) {
		
		$mains[$id_db_main] = $main_name;
		if($id_db_main == $id_main) $title_main = $main_name;
	}
	
	$page_title = array(
		'index.php?modname=manmenu&amp;op=mancustom' => $lang->def('_TITLE_MANMENU'), 
		'index.php?modname=manmenu&amp;op=manmenu&amp;id_custom='.$id_custom => $custom_name, 
		'index.php?modname=manmenu&amp;op=manmodule&amp;id_main='.$id_main => $title_main, 
		( $load ? $lang->def('_YOURE_WORKING_ON_MODULE') : $lang->def('_YOURE_IMPORTING') ).' : '.$name
	);
	// Form
	$out->add(
		getTitleArea($page_title, 'manmenu')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=manmenu&amp;op=manmodule&amp;id_main='.$id_main, $lang->def('_BACK'))
		
		.'<div class="box_evidence">'
		.'<span class="text_bold">'.( $load ? $lang->def('_YOURE_WORKING_ON_MODULE') : $lang->def('_YOURE_IMPORTING') )
			.' : </span>'.$name
		.'</div>'
		
		.Form::openForm('module_permission', 
						'index.php?modname=manmenu&amp;op=upmodule&amp;id_main='.$id_main.'&amp;id_module='.$id_module)
		.Form::getHidden('id_main', 'id_main', $id_main)
		.Form::getHidden('id_module', 'id_module', $id_module)
		
		.( $load ? Form::getHidden('load', 'load', '1') : '' )
		
		.Form::getTextfield($lang->def('_MY_NAME'), 'my_name', 'my_name', 255, 
			( $load ? $my_name : $lang->def('_DEFAULT_MY_NAME') ) )
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
	
	$out 		=& $GLOBALS['page'];
	$id_main 	= importVar('id_main', true, 0);
	$new_id_main = importVar('new_id_main', true, 0);
	$id_module 	= importVar('id_module', true, 0);
	
	$lang 		=& DoceboLanguage::createInstance('manmenu');
	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	
	$id_custom 	= getIdCustomFromMain($id_main);
	
	if(isset($_POST['undo'])) {
		jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$id_main);
	}
	
	// Load module info
	$query_module = "
	SELECT module_name, default_name, file_name, class_name, default_op 
	FROM ".$GLOBALS['prefix_lms']."_module 
	WHERE idModule = '".$id_module."'";
	list($module_name, $name_db, $file_name, $class_name, $module_op) = mysql_fetch_row(mysql_query($query_module));
	$module_obj =& createLmsModule($file_name, $class_name);
	
	//*************************************************************//
	//* Find permission to save or delete *************************//
	//*************************************************************//
	
	$levels 			= CourseLevel::getLevels();
	$all_token 			= $module_obj->getAllToken($module_op);
	$new_token 			= $module_obj->getSelectedPermission($module_op);
	// corresponding of token -> idst role
	$map_idst_token 	=& getModuleRoleSt($module_name, $all_token);
	// corresponding of level -> idst level
	$map_idst_level	 	=& getCustomLevelSt($id_custom);
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
		checkPerm('mod');
		
		$re = mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_under
		SET my_name = '".$_POST['my_name']."', 
			idMain = '".$new_id_main."'
		WHERE  	idMain = '".$id_main."' AND  
				idModule = '".$id_module."'");
		
	} else {
		checkPerm('mod');
		
		$seq 		= getModuleNextSeq($_POST['id_main']);
		
		if($_POST['my_name'] == $lang->def('_DEFAULT_MY_NAME')) $my_name = '';
		else $my_name = $_POST['my_name'];
		
		// Insert module in the list of this menu custom
		$re = mysql_query("
		INSERT INTO ".$GLOBALS['prefix_lms']."_menucustom_under 
		( idCustom, idMain, idModule, sequence, my_name ) VALUES 
		( '".$id_custom."', '".$new_id_main."', '".$id_module."', '".$seq."', '".$my_name."' ) ");
	}
	jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$new_id_main.'&result='.( $re ? 1 : 0 ));
}

function removeModule($id_module, $id_main, $id_custom) {
	
	
	$acl_man 		=& $GLOBALS['current_user']->getAclManager();
	// Load module info
	$query_module = "
	SELECT module_name, default_name, file_name, class_name 
	FROM ".$GLOBALS['prefix_lms']."_module 
	WHERE idModule = '".$id_module."'";
	list($module_name, $name_db, $file_name, $class_name) = mysql_fetch_row(mysql_query($query_module));
	$module_obj =& createLmsModule($file_name, $class_name);
	
	$levels 			= CourseLevel::getLevels();
	$all_token 			= $module_obj->getAllToken();
	// corresponding of token -> idst role
	$map_idst_token 	=& getModuleRoleSt($module_name, $all_token);
	// corresponding of level -> idst level
	$map_idst_level	 	=& getCustomLevelSt($id_custom);
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
		DELETE FROM ".$GLOBALS['prefix_lms']."_menucustom_under 
		WHERE idMain = '".(int)$id_main."' AND idModule = '".(int)$id_module."'");
	}
	return $re;
}

function delmodule() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$out 		=& $GLOBALS['page'];
	$id_main 	= get_req('id_main', DOTY_INT, 0);
	$id_module 	= get_req('id_module', DOTY_INT, 0);
	
	$lang 		=& DoceboLanguage::createInstance('manmenu');
	$menu_lang 	=& DoceboLanguage::createInstance('menu_course', 'lms');
	
	if(isset($_POST['undo'])) {
		
		jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$id_main);
	}
	
	if(get_req('confirm', DOTY_INT, 0) == 1) {
		
		$re = removeModule($id_module, $id_main, getIdCustomFromMain($id_main));
		jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$id_main.'&result='.( $re ? 1 : 0 ));
	}
}

function movemodule($direction) {
	checkPerm('mod');
	
	$id_main 	= importVar('id_main', true, 0);
	$id_module	= importVar('id_module', true, 0);
	
	list($id_custom, $seq) = mysql_fetch_row(mysql_query("
	SELECT idCustom, sequence 
	FROM ".$GLOBALS['prefix_lms']."_menucustom_under 
	WHERE idMain = '".$id_main."' AND idModule = '".$id_module."'"));
	
	if($direction == 'up') {
		
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_under 
		SET sequence = '$seq' 
		WHERE idMain = '".$id_main."' AND sequence = '".($seq - 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_under 
		SET sequence = sequence - 1 
		WHERE idMain = '".$id_main."' AND idModule = '".$id_module."'");
	}
	if($direction == 'down') {
		
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_under 
		SET sequence = '$seq' 
		WHERE idMain = '".$id_main."' AND sequence = '".($seq + 1)."'");
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_under 
		SET sequence = '".($seq + 1)."' 
		WHERE idMain = '".$id_main."' AND idModule = '".$id_module."'");
	}
	jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$id_main.'&id_custom='.$id_custom);
}

function fixmodule() {
	checkPerm('mod');
	
	$id_main 	= importVar('id_main', true, 0);
	$id_custom 	= importVar('id_custom', true, 0);
	
	$query = "
	SELECT idModule 
	FROM ".$GLOBALS['prefix_lms']."_menucustom_under 
	WHERE idMain = '$id_main'
	ORDER BY sequence";	
	$reField = mysql_query($query);
	
	$i = 1;
	while(list($id) = mysql_fetch_row($reField)) {
		mysql_query("
		UPDATE ".$GLOBALS['prefix_lms']."_menucustom_under 
		SET sequence = '".($i++)."' 
		WHERE idModule = '$id'");
	}
	jumpTo('index.php?modname=manmenu&op=manmodule&id_main='.$id_main.'&id_custom='.$id_custom);
}

function manmenuDispatch($op) {
	
	switch($op) {
		//main custom voice
		case "mancustom" : {
			mancustom();
		};break;
		case "addcustom" : {
			editcustom();
		};break;
		case "modcustom" : {
			editcustom(true);
		};break;
		case "savecustom" : {
			savecustom();
		};break;
		case "delcustom" : {
			delcustom();
		};break;
		
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