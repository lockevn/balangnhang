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

if(!$GLOBALS['current_user']->isAnonymous()) {

/**
 * @version  $Id: catalogue.php 573 2006-08-23 09:38:54Z fabio $
 * @category Course managment
 * @author	 Fabio Pirovano <fabio [at] docebo [dot] com>
 */

function catlist() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$lang	=& DoceboLanguage::createInstance('catalogue', 'lms');
	$out 	=& $GLOBALS['page'];
	
	$mod_perm	= checkPerm('mod', true);
	$title_area = array($lang->def('_CATALOGUE') );
	
	// Retriving data
	if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');
		
		$course_man = new AdminCourseManagment();
		$catalogues =& $course_man->getUserCatalogues( getLogUserId() );
		
		if(empty($catalogues)) {
			
			$query_catalogue = "
			SELECT idCatalogue, name, description 
			FROM ".$GLOBALS['prefix_lms']."_catalogue
			WHERE 0
			ORDER BY name";
		} else {
			
			$query_catalogue = "
			SELECT idCatalogue, name, description 
			FROM ".$GLOBALS['prefix_lms']."_catalogue 
			WHERE idCatalogue IN (".implode(',', $catalogues).")
			ORDER BY name";
		}
	} else {
		
		$query_catalogue = "
		SELECT idCatalogue, name, description 
		FROM ".$GLOBALS['prefix_lms']."_catalogue 
		ORDER BY name";
	}
	$re_catalogue = mysql_query($query_catalogue);
	
	// Table
	$tb_catalogue 	= new TypeOne(0, $lang->def('_CATALOGUE_CAPTION'), $lang->def('_CATALOGUE_SUMMARY'));
	
	// Table intestation
	$type_h = array('', '', 'image');
	$cont_h = array(
		$lang->def('_CATALOGUE_NAME'), 
		$lang->def('_DESCRIPTION'), 
		'<img src="'.getPathImage('fw').'standard/modelem.gif" alt="'.$lang->def('_ALT_MODELEM').'" />'
	);
	if($mod_perm) {
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage('fw').'standard/moduser.gif" alt="'.$lang->def('_ALT_ASSOC').'" />';
		
		$type_h[] = 'image';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage('fw').'standard/mod.gif" alt="'.$lang->def('_MOD').'" />';
		$cont_h[] = '<img src="'.getPathImage('fw').'standard/rem.gif" alt="'.$lang->def('_DEL').'" />';
	}
	$tb_catalogue->setColsStyle($type_h);
	$tb_catalogue->addHead($cont_h);
	
	// Table content
	while(list($id, $name, $description) = mysql_fetch_row($re_catalogue)) {
		
		$cont = array(
			$name, 
			$description, 
			'<a href="index.php?modname=catalogue&amp;op=entrylist&amp;id='.$id.'" '
				.'title="'.$lang->def('_MOD_ENTRY_CATALOGUE').' : '.strip_tags($name).'">'
			.'<img src="'.getPathImage('fw').'standard/modelem.gif" alt="'.$lang->def('_ALT_MODELEM').' : '
				.strip_tags($name).'" /></a>'
		);
		if($mod_perm) {
			$cont[] = '<a href="index.php?modname=catalogue&amp;op=modcatalogueassoc&amp;load=1&amp;id_catalogue='.$id.'" '
						.'title="'.$lang->def('_ASSOC_CATALOGUE').' : '.strip_tags($name).'">'
				.'<img src="'.getPathImage('fw').'standard/moduser.gif" alt="'.$lang->def('_ALT_ASSOC').' : '
				.strip_tags($name).'" /></a>';
			
			$cont[] = '<a href="index.php?modname=catalogue&amp;op=modcatalogue&amp;id='.$id.'" '
						.'title="'.$lang->def('_MOD_CATALOGUE').' : '.strip_tags($name).'">'
				.'<img src="'.getPathImage('fw').'standard/mod.gif" alt="'.$lang->def('_MOD').' : '
				.strip_tags($name).'" /></a>';
			$cont[] = '<a href="index.php?modname=catalogue&amp;op=delcatalogue&amp;id='.$id.'" '
						.'title="'.$lang->def('_DEL_CATALOGUE').' : '.strip_tags($name).'">'
				.'<img src="'.getPathImage('fw').'standard/rem.gif" alt="'.$lang->def('_DEL').' : '
				.strip_tags($name).'" /></a>';
		}
		$tb_catalogue->addBody($cont);
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delcatalogue]');
	
	// Action for new catalogue
	if($mod_perm) {
		$tb_catalogue->addActionAdd(
			'<a href="index.php?modname=catalogue&amp;op=newcatalogue" title="'.$lang->def('_NEWCATALOGUE_TITLE').'">'
			.'<img src="'.getPathImage('fw').'standard/add.gif" alt="'.$lang->def('_ADD').'" />'
			.$lang->def('_NEW_CATALOGUE').'</a>');
	}
	$out->add(
		getTitleArea($title_area, 'catalogue' )
		.'<div class="std_block">', 'content');
	if(isset($_POST['result'])) 
	switch($_POST['result']) {
		case "ok" : 	$out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')), 'content');	break;
		case "err" :	$out->add(getErrorUi($lang->def('_OPERATION_ERROR')), 'content');	break;
	}
	$out->add(
		$tb_catalogue->getTable()
		.'</div>', 'content');
}

function mancatalogue($load_id = false) {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang	=& DoceboLanguage::createInstance('catalogue', 'lms');
	$out 	=& $GLOBALS['page'];
	
	$title_area = array(
		'index.php?modname=catalogue&amp;op=catlist' => $lang->def('_CATALOGUE') );
	
	if($load_id === false) {
		
		$title_area[] 	= $lang->def('_NEW_CATALOGUE');
		$name 			= '';
		$description 	= '';
	} else {
		
		$title_area[] = $lang->def('_MOD_CATALOGUE');
		// Retriving data
		$query_catalogue = "
		SELECT name, description 
		FROM ".$GLOBALS['prefix_lms']."_catalogue 
		WHERE idCatalogue = '".(int)$load_id."'";
		list($name, $description) = mysql_fetch_row(mysql_query($query_catalogue));
	}
	
	$out->add(
		getTitleArea($title_area, 'catalogue' )
		.'<div class="std_block">'
		.Form::openForm('mancatalogue', 'index.php?modname=catalogue&amp;op=savecatalogue')
		.( $load_id === false ? '' : Form::getHidden('id_cat', 'id_cat', $load_id) )
		.Form::openElementSpace()
		.Form::getTextfield($lang->def('_CATALOGUE_NAME'), 'name', 'name', 255, $name)
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', $description)
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>', 'content');
}

function savecatalogue() {
	checkPerm('mod');
	
	if(isset($_POST['id_cat'])) {
		
		// Update entry
		$query_catalogue = "
		UPDATE ".$GLOBALS['prefix_lms']."_catalogue 
		SET name = '".$_POST['name']."', 
			description = '".$_POST['description']."'
		WHERE idCatalogue = '".(int)$_POST['id_cat']."'";
		$re = mysql_query($query_catalogue);
	} else {
		
		// Create a new entry
		$query_catalogue = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_catalogue
		( name, description ) VALUES 
		( '".$_POST['name']."', '".$_POST['description']."' )";
		$re = mysql_query($query_catalogue);
		if($GLOBALS['current_user']->getUserLevelId() != ADMIN_GROUP_GODADMIN) {
			
			list($id_cat) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
			$re &= mysql_query("
			INSERT INTO ".$GLOBALS['prefix_fw']."_admin_course 
			( id_entry, type_of_entry, idst_user ) VALUES 
			( '".$id_cat."', 'catalogue', '".getLogUserId()."') ");
		}
	}
	jumpTo('index.php?modname=catalogue&op=catlist&result='.( $re ? 'ok' : 'err' ) );
}

function delcatalogue() {
	checkPerm('mod');
	
	$id_cat = get_req('id', DOTY_INT, 0);
	
	if(get_req('confirm', DOTY_INT, 0) == 1) {
		
		$re = true;
		$re = true;
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_catalogue_member 
		WHERE idCatalogue = '".$id_cat."'"))
			jumpTo('index.php?modname=coursepath&op=pathlist&result=err' );
		
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_catalogue_entry 
		WHERE idCatalogue = '".$id_cat."'"))
			jumpTo('index.php?modname=coursepath&op=pathlist&result=err' );
		
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_catalogue 
		WHERE idCatalogue = '".$id_cat."'"))
			jumpTo('index.php?modname=coursepath&op=pathlist&result=err' );
		
		jumpTo('index.php?modname=catalogue&op=catlist&result='.( $re ? 'ok' : 'err' ));
	}
}


function getCatalogueName($id) {
	$query_catalogue = "
	SELECT name 
	FROM ".$GLOBALS['prefix_lms']."_catalogue 
	WHERE idCatalogue = '".(int)$id."'";
	list($name) = mysql_fetch_row(mysql_query($query_catalogue));
	return $name;
}

function entrylist() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.coursepath.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	
	$lang	=& DoceboLanguage::createInstance('catalogue', 'lms');
	$out 	=& $GLOBALS['page'];
	
	$id_cat = get_req('id', DOTY_INT, 0);
	$cat_name = getCatalogueName($id_cat);
	
	$mod_perm	= checkPerm('mod', true);
	$title_area = array(
		'index.php?modname=catalogue&amp;op=catlist' => $lang->def('_CATALOGUE'),
		$cat_name
	);
	$tb_entry = new TypeOne(0, $lang->def('_ENTRY_CAPTION'), $lang->def('_ENTRY_SUMMARY'));
	
	$query_entry = "
	SELECT idEntry, type_of_entry
	FROM ".$GLOBALS['prefix_lms']."_catalogue_entry 
	WHERE idCatalogue = '".$id_cat."'";
	$re_entry = mysql_query($query_entry);
	
	$courses 	= array();
	$coursepath = array();
	while(list($id, $t_o_entry)= mysql_fetch_row($re_entry)) {
		
		if($t_o_entry == 'course') 	$courses[$id] = $id;
		else $coursepath[$id] = $id;
	}
	$coursepath_man = new CoursePath_Manager();
	$coursespath_name =& $coursepath_man->getNames($coursepath);
	
	$course_name =& getCoursesInfo($courses);
	
	$cont_h = array($lang->def('_CODE'), $lang->def('_NAME'), $lang->def('_TYPE'));
	$type_h = array('', '', '');
	if($mod_perm) {
		
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage('fw').'standard/rem.gif" alt="'.$lang->def('_DEL').'" />';
	}
	$tb_entry->setColsStyle($type_h);
	$tb_entry->addHead($cont_h);
	if(is_array($course_name)) {
		foreach($course_name as $course){
			
			$cont = array($course['code'], $course['name'], $lang->def('_COURSE'));
			if($mod_perm) {
				
				$cont[] = '<a href="index.php?modname=catalogue&amp;op=delentry&amp;id_cat='.$id_cat.'&amp;type=course&amp;id_entry='.$course['id'].'" '
						.'title="'.$lang->def('_REMOVE_ENTRY').' : '.strip_tags($course['name']).'">'
					.'<img src="'.getPathImage('fw').'standard/rem.gif" '
						.'alt="'.$lang->def('_DEL').' : '.strip_tags($course['name']).'" /></a>';
			}
			$tb_entry->addBody($cont);
		}
	}
	if(is_array($coursespath_name)) {
		while(list($id, $coursepath) = each($coursespath_name)) {
			
			$cont = array($coursepath, $lang->def('_COURSEPATH'));
			if($mod_perm) {
				
				$cont[] = '<a href="index.php?modname=catalogue&amp;op=delentry&amp;id_cat='.$id_cat.'&amp;type=coursepath&amp;id_entry='.$id.'" '
						.'title="'.$lang->def('_REMOVE_ENTRY').' : '.strip_tags($coursepath).'">'
					.'<img src="'.getPathImage('fw').'standard/rem.gif" '
						.'alt="'.$lang->def('_DEL').' : '.strip_tags($coursepath).'" /></a>';
			}
			$tb_entry->addBody($cont);
		}
	}
	
	$select_entry = array('course' => $lang->def('_COURSE'), 'coursepath' => $lang->def('_COURSEPATH'));
	$tb_entry->addActionAdd(
		'<a href="index.php?modname=catalogue&amp;op=import&amp;id_cat='.$id_cat.'&amp;load=1" title="'.$lang->def('_IMPORT_NEW_ENTRY').'">'
		.'<img src="'.getPathImage('fw').'standard/import.gif" alt="'.$lang->def('_IMPORT').'" /> '
		.$lang->def('_IMPORT').'</a>'
	);
	$out->add(
		getTitleArea($title_area, 'catalogue')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=catalogue&amp;op=catlist', $lang->def('_BACK'))
		.$tb_entry->getTable()
		.getBackUi('index.php?modname=catalogue&amp;op=catlist', $lang->def('_BACK'))
		.'</div>', 'content');
	
	if($mod_perm)
	{
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delentry]');
	}
}

function updateCatalogueEntry(&$new_sel, &$old_sel, $type, $id_cat) {
	
	$re = true;
	$to_add 	= array_diff($new_sel, $old_sel);
	$to_del 	= array_diff($old_sel, $new_sel);
	while(list(,$id) = each($to_add)) {
		
		$re &= mysql_query("
		INSERT INTO ".$GLOBALS['prefix_lms']."_catalogue_entry 
		( idEntry, type_of_entry, idCatalogue ) VALUES 
		( '".$id."', '".$type."', '".$id_cat."') ");
	}
	while(list(,$id) = each($to_del)) {
		
		$re &= mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_catalogue_entry 
		WHERE idEntry = '".$id."' AND type_of_entry = '".$type."' AND idCatalogue = '".$id_cat."'");
	}
	return $re;
}

function import() {
	checkPerm('mod');
	require_once($GLOBALS['where_lms'].'/lib/lib.course_managment.php');
	
	$lang 		=& DoceboLanguage::createInstance('catalogue', 'lms');
	$id_cat 	= importVar('id_cat', true, 0);
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$sel = new Course_Manager();
	$sel->show_catalogue_selector = false;
	$sel->setLink('index.php?modname=catalogue&amp;op=import');
	
	if(isset($_POST['undo'])) {
		jumpTo('index.php?modname=catalogue&amp;op=entrylist&amp;id='.$id_cat);
	}
	if(isset($_GET['load']) || isset($_POST['save_selection'])) {
		
		$course_initial_sel = array();
		$coursepath_initial_sel = array();
		$query = "
		SELECT idEntry, type_of_entry 
		FROM ".$GLOBALS['prefix_lms']."_catalogue_entry 
		WHERE idCatalogue = '".$id_cat."'";
		$re_entry = mysql_query($query);
		while(list($id, $type) = mysql_fetch_row($re_entry)) {
			
			switch($type) {
				case "course" : 		$course_initial_sel[$id] = $id;break;
				case "coursepath" : 	$coursepath_initial_sel[$id] = $id;break;
			}
		}
		if(isset($_GET['load'])) {
			$sel->resetCourseSelection($course_initial_sel);
			$sel->resetCoursePathSelection($coursepath_initial_sel);
		}
	}
	if(isset($_POST['save_selection'])) {
		
		$re = true;
		$course = $sel->getCourseSelection($_POST);
		$re &= updateCatalogueEntry($course, $course_initial_sel, 'course', $id_cat);
		
		$coursepath = $sel->getCoursePathSelection($_POST);
		$re &= updateCatalogueEntry($coursepath, $coursepath_initial_sel, 'coursepath', $id_cat);
		
		jumpTo('index.php?modname=catalogue&amp;op=entrylist&amp;id='.$id_cat.'&amp;result='.( $re ? 'ok' : 'err' ));
	}
	$title_area = array(
		'index.php?modname=catalogue&amp;op=catlist' => $lang->def('_CATALOGUE'),
		'index.php?modname=catalogue&amp;op=entrylist&amp;id='.$id_cat => getCatalogueName($id_cat),
		$lang->def('_IMPORT_NEW_ENTRY').' '.$lang->def('_COURSE') 
	);
	$out->addStart(
		getTitleArea($title_area, 'catalogue')
		.'<div class="std_block">'
		.Form::openForm('mancoursepath', 'index.php?modname=catalogue&amp;op=import')
		.Form::getHidden('id_cat', 'id_cat', $id_cat)
		, 'content' );
	
	$out->addEnd(
		Form::openButtonSpace()
		.Form::getButton('save_selection', 'save_selection', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>'
		, 'content' );
	
	$sel->loadSelector();
}

function delentry() {
	checkPerm('mod');
	
	$id_cat 		= importVar('id_cat', false);
	$type_of_entry 	= importVar('type', false);
	$id_entry 		= importVar('id_entry', true, 0);
	$id_arr 		= array($id_entry);
	
	$query_catalogue = "
	SELECT idCatalogue 
	FROM ".$GLOBALS['prefix_lms']."_catalogue_entry 
	WHERE idCatalogue = '".$id_cat."' AND idEntry = '".$id_entry."' AND type_of_entry = '".$type_of_entry."'";
	list($id_cat) = mysql_fetch_row(mysql_query($query_catalogue));
	
	if(get_req('confirm', DOTY_INT, 0))
	{
		$re = true;
		if(!mysql_query("
		DELETE FROM ".$GLOBALS['prefix_lms']."_catalogue_entry 
		WHERE idCatalogue = '".$id_cat."' AND idEntry = '".$id_entry."' AND type_of_entry = '".$type_of_entry."'"))
			jumpTo('index.php?modname=coursepath&op=pathlist&result=err' );
		
		jumpTo('index.php?modname=catalogue&op=entrylist&id='.$id_cat.'&result='.( $re ? 'ok' : 'err' ));
	}
}

function addToCatologue($memebers, $id_catalogue) {
	
	$re = true;
	reset($memebers);
	while(list(, $id_m) = each($memebers)) {
		
		$query_insert = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_catalogue_member
		( idCatalogue, idst_member ) VALUES 
		( '".$id_catalogue."', '".$id_m."' )";
		$re &= mysql_query($query_insert);
	}
	reset($memebers);
	return $re;
}

function removeFromCatologue($memebers, $id_catalogue) {
	
	$re = true;
	reset($memebers);
	while(list(, $id_m) = each($memebers)) {
		
		$query_delete = "
		DELETE FROM ".$GLOBALS['prefix_lms']."_catalogue_member
		WHERE idCatalogue = '".$id_catalogue."' AND idst_member = '".$id_m."'";
		$re &= mysql_query($query_delete);
	}
	reset($memebers);
	return $re;
}

function modcatalogueassoc() {
	checkPerm('mod');
	
	$lang	=& DoceboLanguage::createInstance('catalogue', 'lms');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	
	$id_catalogue = importVar('id_catalogue', true, 0);
	$out =& $GLOBALS['page'];
	
	$user_select = new Module_Directory();
	$user_select->show_user_selector = FALSE;
	$user_select->show_group_selector = TRUE;
	$user_select->show_orgchart_selector = TRUE;
	$user_select->show_orgchart_simple_selector = TRUE;
	$user_select->multi_choice = TRUE;
	
	if(isset($_POST['okselector'])) {
		
		$old_members = array();
		$re_members = mysql_query("
		SELECT idst_member 
		FROM ".$GLOBALS['prefix_lms']."_catalogue_member 
		WHERE idCatalogue = '".$id_catalogue."'");
		while(list($id_members) = mysql_fetch_row($re_members)) {
			
			$old_members[$id_members] = $id_members;
		}
		$new_members = $user_select->getSelection($_POST);
		$to_add = array_diff($new_members, $old_members);
		$to_del = array_diff($old_members, $new_members);
		
		$re = true;
		$re &= addToCatologue($to_add, $id_catalogue);
		$re &= removeFromCatologue($to_del, $id_catalogue);
		
		jumpTo('index.php?modname=catalogue&op=catlist&result='.( $re ? 'ok' : 'err' ));
	}
	
	if(isset($_GET['load'])) {
		
		$members = array();
		$re_members = mysql_query("
		SELECT idst_member 
		FROM ".$GLOBALS['prefix_lms']."_catalogue_member 
		WHERE idCatalogue = '".$id_catalogue."'");
		
		while(list($id_members) = mysql_fetch_row($re_members)) {
			
			$members[$id_members] = $id_members;
		}
		$user_select->resetSelection($members);
	}
	$title_area = getTitleArea(
		array('index.php?modname=catalogue&amp;op=catlist' => $lang->def('_CATALOGUE'),getCatalogueName($id_catalogue)), 
		'catalogue');
	$user_select->setPageTitle($title_area);
	$user_select->loadSelector('index.php?modname=catalogue&amp;op=modcatalogueassoc&amp;id_catalogue='.$id_catalogue, 
			$lang->def('_CATALOGUE'), 
			$lang->def('_ASSOC_CATALOGUE'), 
			true, 
			true );
}

function catalogueDispatch($op) {
	
	if(isset($_POST['undo'])) $op = 'catlist';
	if(isset($_POST['undoentry'])) $op = 'entrylist';
	if(isset($_POST['cancelselector'])) $op = 'catlist';
	
	switch($op) {
		case "catlist" : {
			catlist();
		};break;
		
		case "newcatalogue" : {
			mancatalogue(false);
		};break;
		case "modcatalogue" : {
			mancatalogue(importVar('id', false, 0));
		};break;
		case "savecatalogue" : {
			savecatalogue();
		};break;
		
		case "delcatalogue" : {
			delcatalogue();
		};break;
		
		case "entrylist" : {
			entrylist();
		};break;
		
		case "import" : {
			import();
		};break;
		
		case "delentry" : {
			delentry();
		};break;
		
		case "modcatalogueassoc" : {
			modcatalogueassoc();
		};break;
		
	}
}

}
?>