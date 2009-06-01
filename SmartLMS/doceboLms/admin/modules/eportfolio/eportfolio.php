<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system									*/
/* ============================================							*/
/*																			*/
/* Copyright (c) 2005														*/
/* http://www.docebo.com													*/
/*																			*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

/**
 * @package doceboLms-admin
 * @subpackage e-portfolio 
 * @author	 Fabio Pirovano <fabio [at] docebo-com>
 * @version  $Id: eportfolio.php 635 2006-09-15 07:28:40Z fabio $
 * @since 3.1.0
 */

require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');

// success code
define("_SUCCESS_1", "_EPORTFOLIO_SAVED_CORRECTLY");
define("_SUCCESS_2", "_EPORTFOLIO_DELETED_CORRECTLY");
define("_SUCCESS_3", "_EPORTFOLIO_MEMBERS_ASSIGNED_CORRECTLY");
define("_SUCCESS_4", "_EPORTFOLIO_PDP_SAVE_CORRECT");
define("_SUCCESS_5", "_EPORTFOLIO_PDP_DELETE_CORRECT");
define("_SUCCESS_6", "_EPORTFOLIO_ADMIN_ASSIGNED_CORRECTLY");
define("_SUCCESS_7", "_EPORTFOLIO_COMPETENCE_SAVE_CORRECT");
define("_SUCCESS_8", "_EPORTFOLIO_COMPETENCE_DELETE_CORRECT");

// fail code
define("_FAIL_1", "_EPORTFOLIO_SAVE_UNSUCCESSFUL");
define("_FAIL_2", "_EPORTFOLIO_DELETED_UNSUCCESSFUL");
define("_FAIL_3", "_EPORTFOLIO_MEMBERS_ASSIGNED_UNSUCCESSFUL");
define("_FAIL_4", "_EPORTFOLIO_PDP_SAVE_UNSUCCES");
define("_FAIL_5", "_EPORTFOLIO_PDP_DELETE_UNSUCCES");
define("_FAIL_6", "_EPORTFOLIO_ADMIN_ASSIGNED_UNSUCCESSFUL");
define("_FAIL_7", "_EPORTFOLIO_COMPETENCE_SAVE_UNSUCCES");
define("_FAIL_8", "_EPORTFOLIO_COMPETENCE_DELETE_UNSUCCES");

function eportfolio() {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$mod_perm = checkPerm('mod', true);
	
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	$out 	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$man_epf = new Man_Eportfolio();
	
	$tb = new TypeOne(0, $lang->def('_EPORTFOLIO_CAPTION'), $lang->def('_EPORTFOLIO_SUMMARY'));
	$nav_bar = new NavBar('ini', $GLOBALS['lms']['visuItem'], 0, 'button');
	$ini = $nav_bar->getSelectedElement();
	//search query
	
	//do query
	$re_eportfolii = $man_epf->getQueryResultEportfolio('', $ini, $GLOBALS['lms']['visuItem']);
	$tot_pages = $man_epf->getTotalOfEportfolio();
	
	$nav_bar->setElementTotal($tot_pages);
	
	//-Table---------------------------------------------------------
	$cont_h = array(	$lang->def('_TITLE'), 
						$lang->def('_DESCRIPTION'), 
						'<img src="'.getPathImage().'eportfolio/pdp.gif" alt="'.$lang->def('_TITLE_MOD_PDP').'" title="'.$lang->def('_TITLE_MOD_PDP').'" />',
						'<img src="'.getPathImage().'eportfolio/competences.gif" alt="'.$lang->def('_TITLE_MOD_COMPETENCES').'" title="'.$lang->def('_TITLE_MOD_COMPETENCES').'" />');
	$type_h = array('', '', 'image', 'image');
	if($mod_perm) {
		
		$cont_h[] = '<img src="'.getPathImage().'standard/moduser.gif" alt="'.$lang->def('_TITLE_MODUSER_EPORTFOLIO').'" title="'.$lang->def('_TITLE_MODUSER_EPORTFOLIO').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/modadmin.gif" alt="'.$lang->def('_TITLE_MODADMIN_EPORTFOLIO').'" title="'.$lang->def('_TITLE_MODADMIN_EPORTFOLIO').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_TITLE_MOD_EPORTFOLIO').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" title="'.$lang->def('_TITLE_DEL_EPORTFOLIO').'" />';
		$type_h[] = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	
	if($re_eportfolii != false)
	while(list($id_portfolio, $title, $description) = mysql_fetch_row($re_eportfolii)) {
		
		$cont = array(
			$title, 
			$description, 
			'<a href="index.php?modname=eportfolio&amp;op=epfpdp&amp;id_portfolio='.$id_portfolio.'" title="'.$lang->def('_TITLE_MOD_PDP').' : '.$title.'">'
				.'<img src="'.getPathImage().'eportfolio/pdp.gif" alt="'.$lang->def('_TITLE_MOD_PDP').' : '.$title.'" /></a>',
			'<a href="index.php?modname=eportfolio&amp;op=epfcompetences&amp;id_portfolio='.$id_portfolio.'" title="'.$lang->def('_TITLE_MOD_COMPETENCES').' : '.$title.'">'
				.'<img src="'.getPathImage().'eportfolio/competences.gif" alt="'.$lang->def('_TITLE_MOD_COMPETENCES').' : '.$title.'" /></a>'
		);
		if($mod_perm) {
			$cont[] = '<a href="index.php?modname=eportfolio&amp;op=modepfuser&amp;id_portfolio='.$id_portfolio.'&amp;load=1" 
							title="'.$lang->def('_TITLE_MODUSER_EPORTFOLIO').' : '.$title.'">'
					.'<img src="'.getPathImage().'standard/moduser.gif" alt="'.$lang->def('_TITLE_MODUSER_EPORTFOLIO').' : '.$title.'" /></a>';
			
			$cont[] = '<a href="index.php?modname=eportfolio&amp;op=modepfadmin&amp;id_portfolio='.$id_portfolio.'&amp;load=1" 
							title="'.$lang->def('_TITLE_MODADMIN_EPORTFOLIO').' : '.$title.'">'
					.'<img src="'.getPathImage().'standard/modadmin.gif" alt="'.$lang->def('_TITLE_MODADMIN_EPORTFOLIO').' : '.$title.'" /></a>';
			
			$cont[] = '<a href="index.php?modname=eportfolio&amp;op=modepf&amp;id_portfolio='.$id_portfolio.'" 
							title="'.$lang->def('_TITLE_MOD_EPORTFOLIO').' : '.$title.'">'
					.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$title.'" /></a>';
			
			$cont[] = '<a href="index.php?modname=eportfolio&amp;op=&amp;id_portfolio='.$id_portfolio.'" 
							title="'.$lang->def('_TITLE_DEL_EPORTFOLIO').' : '.$title.'">'
					.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$title.'" /></a>';
		}
		$tb->addBody($cont);
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delepf]');
	
	if($mod_perm) {
		$tb->addActionAdd('<a class="new_element_link" href="index.php?modname=eportfolio&amp;op=modepf" title="'.$lang->def('_TITLE_ADD_EPORTFOLIO').'">'
			.$lang->def('_ADD_EPORTFOLIO').'</a>');
	}
	//visualize result
	$out->add(
		getTitleArea($lang->def('_TITLE_EPORTFOLIO'), 'eportfolio')
		.'<div class="std_block">' );
	if(isset($_GET['result'])) $out->add(guiResultStatus($lang, $_GET['result']));
	$out->add(
		$tb->getTable()
		.Form::openForm('nav_eportfolio', 'index.php?modname=eportfolio&amp;op=eportfolio')
		.$nav_bar->getNavBar($ini)
		.Form::closeForm()
		.'</div>');
}

function modepf() {
	checkPerm('mod');
	
	$id_portfolio = importVar('id_portfolio', true, 0);
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	
	$error = false;
	if(isset($_POST['save_epf'])) {
		
		// check data
		if(!isset($_POST['title']) || (isset($_POST['title']) && trim($_POST['title']) == '')) {
			$error = $lang->def('_ERROR_EMPTY_TITLE');
		}
		
		//save data
		if($error === false) {
			
			$man_epf = new Man_Eportfolio();
			if($id_portfolio == 0) $id_portfolio = false;
			$re_op = $man_epf->savePortfolio(	$id_portfolio, 
												array(	'title' => $_POST['title'], 
														'description' => $_POST['description'], 
														'custom_pdp_descr' => $_POST['custom_pdp_descr'], 
														'custom_competence_descr' => $_POST['custom_competence_descr']));
			
			jumpTo('index.php?modname=eportfolio&amp;op=eportfolio&amp;result='.($re_op ? 'ok_1' : 'err_1' ));
		}
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$title_page = array('index.php?modname=eportfolio&amp;op=eportfolio' => $lang->def('_TITLE_EPORTFOLIO'));
	//load eportfolio data
	if($id_portfolio != 0) {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
		$man_epf 	= new Man_Eportfolio();
		$data 		= $man_epf->getEportfolio($id_portfolio);
		
		$epf_title 						= $data['title'];
		$epf_description 				= $data['description'];
		$epf_custom_pdp_descr 			= $data['custom_pdp_descr'];
		$epf_custom_competence_descr 	= $data['custom_competence_descr'];
		$title_page[] = $lang->def('_TITLE_MOD_EPORTFOLIO').' : '.$epf_title;
	} else {
		
		$epf_title 						= '';
		$epf_description 				= '';
		$epf_custom_pdp_descr 			= '';
		$epf_custom_competence_descr 	= '';
		$title_page[] = $lang->def('_ADD_EPORTFOLIO');
	}
	
	// print page
	$GLOBALS['page']->add(
		getTitleArea($title_page, 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=eportfolio', $lang->def('_BACK'))
		
		.( $error !== false ? getErrorUi($error) : '' )
		
		.Form::openForm('form_add_eportfolio', 'index.php?modname=eportfolio&amp;op=modepf')
		//----------------------------------------
		.Form::openElementSpace()
		
		.( $id_portfolio != 0 ? Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio) : '' )
		
		.Form::getTextfield($lang->def('_TITLE_EPORTFOLIO'), 'title', 'title', 255, 
			( isset($_POST['title']) ? stripslashes($_POST['title']) : $epf_title ) )
		
		.Form::getTextarea($lang->def('_DESCRIPTION'), 'description', 'description', 
			( isset($_POST['description']) ? stripslashes($_POST['description']) : $epf_description ) )
		
		
		.Form::getTextarea($lang->def('_CUSTOM_PDP_DESCR'), 'custom_pdp_descr', 'custom_pdp_descr', 
			( isset($_POST['custom_pdp_descr']) ? stripslashes($_POST['custom_pdp_descr']) : $epf_custom_pdp_descr ) )
		
		.Form::getTextarea($lang->def('_CUSTOM_COMPETENCE_DESCR'), 'custom_competence_descr', 'custom_competence_descr', 
			( isset($_POST['custom_competence_descr']) ? stripslashes($_POST['custom_competence_descr']) : $epf_custom_competence_descr ) )
		
		.Form::closeElementSpace()
		//----------------------------------------
		.Form::openButtonSpace()
		.Form::getButton('save_epf', 'save_epf', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>'
	, 'content');
}

function delepf() {
	checkPerm('mod');
	
	$id_portfolio = get_req('id_portfolio', DOTY_INT, 0);
	
	
	if(isset($_POST['undo'])) {
		jumpTo('index.php?modname=eportfolio&amp;op=eportfolio');
	}
	if(get_req('confirm', DOTY_INT, 0) == 1) {
		
		$man_epf = new Man_Eportfolio();
		if($id_portfolio == 0) $id_portfolio = false;
		$re_op = $man_epf->deletePortfolio($id_portfolio);
		
		jumpTo('index.php?modname=eportfolio&amp;op=eportfolio&amp;result='.($re_op ? 'ok_2' : 'err_2' ));
	}
	
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	
	//load eportfolio data
	require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
	$man_epf = new Man_Eportfolio();
	$data = $man_epf->getEportfolio($id_portfolio);
	
	$title_page = array('index.php?modname=eportfolio&amp;op=eportfolio' => $lang->def('_TITLE_EPORTFOLIO'),
		$lang->def('_TITLE_DEL_EPORTFOLIO').': <b>'.$data['title'].'</b>' 
	);
	// print page
	$GLOBALS['page']->add(
		getTitleArea($title_page, 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=eportfolio', $lang->def('_BACK'))
		
		.Form::openForm('form_del_eportfolio', 'index.php?modname=eportfolio&amp;op=delepf')
		.Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio)
		
		.getDeleteUi($lang->def('_ARE_YOU_SURE'), 
			'<b>'.$lang->def('_TITLE').': </b> '.$data['title'].'<br />'
			.'<b>'.$lang->def('_DESCRIPTION').': </b> '.$data['description'],
			false,
			'confirm',
			'undo' )
		.Form::closeForm()
		.'</div>'
	, 'content');
}

function modepfuser() {
	checkPerm('mod');
	
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
	
	$id_portfolio = importVar('id_portfolio', true, 0);
	$out =& $GLOBALS['page'];
	$man_epf = new Man_Eportfolio();
	$data = $man_epf->getEportfolio($id_portfolio);
	
	$user_select = new Module_Directory();
	$user_select->show_user_selector = TRUE;
	$user_select->show_group_selector = TRUE;
	$user_select->show_orgchart_selector = TRUE;
	$user_select->show_orgchart_simple_selector = FALSE;
	$user_select->multi_choice = TRUE;
	
	if(isset($_POST['okselector'])) {
		
		
		$admins =& $man_epf->getAssociatedAdmin($id_portfolio);
		$members =& $man_epf->getAssociatedMember($id_portfolio);
		$selection = $user_select->getSelection($_POST);
		
		$to_add 	= array_diff($selection, $admins, $members);
		$to_change 	= array_intersect($admins, $selection);
		$to_del 	= array_diff($members, $selection);
		
		$re = true;
		$re &= $man_epf->addMembers($id_portfolio, $to_add, false);
		$re &= $man_epf->updateMembers($id_portfolio, $to_change, false);
		$re &= $man_epf->removeMembers($id_portfolio, $to_del, false);
		
		jumpTo('index.php?modname=eportfolio&op=eportfolio&result='.( $re ? 'ok_3' : 'err_3' ));
	}
	
	if(isset($_GET['load'])) {
		
		$members =& $man_epf->getAssociatedMember($id_portfolio);
		$user_select->resetSelection($members);
	}
	
	$title_area = getTitleArea(
		array('index.php?modname=eportfolio&amp;op=eportfolio' => $lang->def('_TITLE_EPORTFOLIO'), $data['title']), 
		'eportfolio');
	$user_select->setPageTitle($title_area);
	$user_select->loadSelector('index.php?modname=eportfolio&amp;op=modepfuser&amp;id_portfolio='.$id_portfolio, 
			$lang->def('_TITLE_EPORTFOLIO'), 
			$lang->def('_ASSOC_EPORTFOLIO_TO_USER'), 
			true, 
			true );
}

function modepfadmin() {
	checkPerm('mod');
	
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
	
	$id_portfolio = importVar('id_portfolio', true, 0);
	$out =& $GLOBALS['page'];
	$man_epf = new Man_Eportfolio();
	$data = $man_epf->getEportfolio($id_portfolio);
	
	$user_select = new Module_Directory();
	$user_select->show_user_selector = TRUE;
	$user_select->show_group_selector = TRUE;
	$user_select->show_orgchart_selector = TRUE;
	$user_select->show_orgchart_simple_selector = FALSE;
	$user_select->multi_choice = TRUE;
	
	if(isset($_POST['okselector'])) {
		
		$admins =& $man_epf->getAssociatedAdmin($id_portfolio);
		$members =& $man_epf->getAssociatedMember($id_portfolio);
		$selection = $user_select->getSelection($_POST);
		
		$to_add 		= array_diff($selection, $admins, $members);
		$to_change 	= array_intersect($members, $selection);
		$to_del 		= array_diff($admins, $selection);
		
		$re = true;
		$re &= $man_epf->addMembers($id_portfolio, $to_add, true);
		$re &= $man_epf->updateMembers($id_portfolio, $to_change, true);
		$re &= $man_epf->removeMembers($id_portfolio, $to_del, true);
		
		jumpTo('index.php?modname=eportfolio&op=eportfolio&result='.( $re ? 'ok_6' : 'err_6' ));
	}
	
	if(isset($_GET['load'])) {
		
		$members =& $man_epf->getAssociatedAdmin($id_portfolio);
		$user_select->resetSelection($members);
	}
	$title_area = getTitleArea(
		array('index.php?modname=eportfolio&amp;op=eportfolio' => $lang->def('_TITLE_EPORTFOLIO'), $data['title']), 
		'eportfolio');
	$user_select->setPageTitle($title_area);
	$user_select->loadSelector('index.php?modname=eportfolio&amp;op=modepfadmin&amp;id_portfolio='.$id_portfolio, 
			$lang->def('_TITLE_EPORTFOLIO'), 
			$lang->def('_ASSOC_EPORTFOLIO_TO_ADMIN'), 
			true, 
			true );
}

function epfpdp() {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$mod_perm = checkPerm('mod', true);
	$id_portfolio = importVar('id_portfolio');
	
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	$out 	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$man_epf = new Man_Eportfolio();
	
	$portfolio_info = $man_epf->getEportfolio($id_portfolio);
	
	$tb = new TypeOne(0, $lang->def('_PDP_CAPTION'), $lang->def('_PDP_SUMMARY'));
	$nav_bar = new NavBar('ini', $GLOBALS['lms']['visuItem'], 0, 'button');
	$ini = $nav_bar->getSelectedElement();
	//search query
	
	//do query
	$re_pdp = $man_epf->getQueryPdpOfEportfolio($id_portfolio, $ini, $GLOBALS['lms']['visuItem']);
	$tot_pages = $man_epf->getTotalOfPdp($id_portfolio);
	
	$nav_bar->setElementTotal($tot_pages);
	
	//-Table---------------------------------------------------------
	$cont_h = array(	$lang->def('_SEQUENCE_NUMBER'), 
						$lang->def('_TEXTOF'), 
						$lang->def('_ALLOW_ANSWER'), 
						$lang->def('_MAX_ANSWER'), 
						$lang->def('_ANSWER_MOD_FOR_DAY') );
	$type_h = array('image', '', 'image', 'image', 'image');
	if($mod_perm) {
		
		$cont_h[] = '<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_TITLE_ORD_DESC_PDP').'" title="'.$lang->def('_TITLE_ORD_DESC_PDP').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_TITLE_ORD_ASC_PDP').'" title="'.$lang->def('_TITLE_ORD_ASC_PDP').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_TITLE_MOD_EPORTFOLIO').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" title="'.$lang->def('_TITLE_DEL_EPORTFOLIO').'" />';
		$type_h[] = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	$i = 1;
	if($re_pdp != false) {
		
		$tot_elem = mysql_num_rows($re_pdp);
		while(list($id_pdp, $texof, $allow_answer, $max_answer, $answer_mod_for_day, $sequence) = mysql_fetch_row($re_pdp)) {
		
			$cont = array(
				$sequence, 
				$texof, 
				( $allow_answer == 'true' ? $lang->def('_YES') : $lang->def('_NO') ), 
				$max_answer
			); 
			switch($answer_mod_for_day) {
				case _MOD_ANSWER_FOREVER : {
					$cont[] = $lang->def('_MOD_ANSWER_FOREVER');
				};break;
				case _MOD_ANSWER_NEVER : {
					$cont[] = $lang->def('_MOD_ANSWER_NEVER');
				};break;
				default : {
					$cont[] = $answer_mod_for_day;
				};break;
			}
			
			if($mod_perm) {
				if($i != $tot_elem) {
					$cont[] = '<a href="index.php?modname=eportfolio&amp;op=downpdp&amp;id_pdp='.$id_pdp.'" title="'.$lang->def('_MOVE_DOWN').' : '.$sequence.'">'
							.'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').' : '.$sequence.'" /></a>';
				} else {
					$cont[] = '';
				}
				if($i != 1) {
					$cont[] = '<a href="index.php?modname=eportfolio&amp;op=uppdp&amp;id_pdp='.$id_pdp.'" title="'.$lang->def('_MOVE_UP').' : '.$sequence.'">'
							.'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_MOVE_UP').' : '.$sequence.'" /></a>';
				} else {
					$cont[] = '';
				}
				$cont[] = '<a href="index.php?modname=eportfolio&amp;op=modpdpquest&amp;id_pdp='.$id_pdp.'" title="'.$lang->def('_TITLE_MOD_PDP').' : '.$sequence.'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$sequence.'" /></a>';
				
				$cont[] = '<a href="index.php?modname=eportfolio&amp;op=delpdpquest&amp;id_pdp='.$id_pdp.'" title="'.$lang->def('_TITLE_DEL_PDP').' : '.$sequence.'">'
						.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$sequence.'" /></a>';
			}
			$tb->addBody($cont);
			$i++;
		}
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delpdpquest]');
	
	if($mod_perm) {
		$tb->addActionAdd('<a class="new_element_link_float" href="index.php?modname=eportfolio&amp;op=modpdpquest&amp;id_portfolio='.$id_portfolio.'" title="'.$lang->def('_TITLE_ADD_PDP').'">'
			.$lang->def('_ADD_PDP').'</a>');
	}
	//visualize result
	$out->add(
		getTitleArea(array('index.php?modname=eportfolio&amp;op=eportfolio' => $lang->def('_TITLE_EPORTFOLIO'), 
			$lang->def('_PDP_QUESTION').' : '.$portfolio_info['title']), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=eportfolio', $lang->def('_BACK')));
	if(isset($_GET['result'])) $out->add(guiResultStatus($lang, $_GET['result']));
	$out->add(
		$tb->getTable()
		.Form::openForm('nav_eportfolio', 'index.php?modname=eportfolio&amp;op=epfpdp')
		.Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio)
		.$nav_bar->getNavBar($ini)
		.Form::closeForm()
		.'</div>');
}

function modpdpquest() {
	checkPerm('mod');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	$out 	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$man_epf = new Man_Eportfolio();
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$id_pdp 		= importVar('id_pdp', true, 0);
	
	if($id_portfolio == 0) {
		
		//load pdp question
		$load 			= true;
		$data 			= $man_epf->getPdpDetails($id_pdp);
		$id_portfolio 	= $data['id_portfolio'];
	} else {
		
		$load 						= false;
		$data['textof'] 			= '';
		$data['allow_answer'] 		= 'true';
		$data['max_answer']  		= 1;
		$data['answer_mod_for_day'] = _MOD_ANSWER_FOREVER;
	}
	
	$portfolio_info = $man_epf->getEportfolio($id_portfolio);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio' => $lang->def('_TITLE_EPORTFOLIO'), 
			'index.php?modname=eportfolio&amp;op=epfpdp&amp;id_portfolio='.$id_portfolio => $lang->def('_OPERATION_ON_PDP').': '.$portfolio_info['title'], 
			( !$load ? $lang->def('_ADD_PDP') : strip_tags($data['textof']) )
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=epfpdp&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK'))
	);
	if(isset($_POST['undo'])) jumpTo('index.php?modname=eportfolio&op=epfpdp&id_portfolio='.$id_portfolio);
	if(isset($_POST['save_pdp_question'])) {
		
		if(trim($_POST['textof']) == '') {
			
			$GLOBALS['page']->add(getErrorUi($lang->def('_ERR_PDP_QUEST_EMPTY')));
		} else {
			
			if($id_portfolio == 0) {
				$data 			= $man_epf->getPdpDetails($id_pdp);
				$id_portfolio 	= $data['id_portfolio'];
			}
			$re = $man_epf->savePdpQuestion(($id_pdp != 0 ? $id_pdp :false ), $id_portfolio, $_POST);
			jumpTo('index.php?modname=eportfolio&op=epfpdp&id_portfolio='.$id_portfolio.'&result='.( $re ? 'ok_4' : 'err_4' ));
		}
	}
	
	$max_answer_values = array();
	for($i = 1; $i <= $GLOBALS['lms']['max_pdp_answer']; $i++) $max_answer_values[$i] = $i;
	
	$mod_for_day_values = array();
	$mod_for_day_values[_MOD_ANSWER_FOREVER] 	= $lang->def('_MOD_ANSWER_FOREVER');
	$mod_for_day_values[_MOD_ANSWER_NEVER] 		= $lang->def('_MOD_ANSWER_NEVER');
	for($i = 1; $i <= 30; $i++) $mod_for_day_values[$i] = $i;
	
	$GLOBALS['page']->add(
		Form::openForm('form_add_pdp', 'index.php?modname=eportfolio&amp;op=modpdpquest')
		//----------------------------------------
		.Form::openElementSpace()
		
		.( !$load ? 
			Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio) : 
			Form::getHidden('id_pdp', 'id_pdp', $id_pdp) )
		
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof', 
			( isset($_POST['textof']) ? stripslashes($_POST['textof']) : $data['textof'] ) )
			
		.Form::getOpenCombo($lang->def('_ALLOW_ANSWER'))
		.Form::getRadio($lang->def('_YES'), 'allow_answer_true', 'allow_answer', 'true', 
			($data['allow_answer'] == 'true'))
		.Form::getRadio($lang->def('_NO'), 'allow_answer_false', 'allow_answer', 'false', 
			($data['allow_answer'] == 'false'))
		.Form::getCloseCombo()
		
		.Form::getDropdown($lang->def('_MAX_ANSWER'), 'max_answer', 'max_answer', $max_answer_values, 
			$data['max_answer'] )
		
		.Form::getDropdown($lang->def('_ANSWER_MOD_FOR_DAY'), 'answer_mod_for_day', 'answer_mod_for_day', $mod_for_day_values, 
			$data['answer_mod_for_day'] )
			
		.Form::closeElementSpace()
		//----------------------------------------
		.Form::openButtonSpace()
		.Form::getButton('save_pdp_question', 'save_pdp_question', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>'
	, 'content');
}

function delpdpquest() {
	checkPerm('mod');
	
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	//load eportfolio data
	require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
	$man_epf 		= new Man_Eportfolio();
	$id_pdp 		= get_req('id_pdp', DOTY_INT, 0);
	$data 			= $man_epf->getPdpDetails($id_pdp);
	$id_portfolio 	= $data['id_portfolio'];
	
	if(isset($_POST['undo'])) {
		jumpTo('index.php?modname=eportfolio&op=epfpdp&id_portfolio='.$id_portfolio);
	}
	if(get_req('confirm', DOTY_INT, 0) == 1) {
		
		$re = $man_epf->deletePdpQuestion($id_pdp);
		jumpTo('index.php?modname=eportfolio&op=epfpdp&id_portfolio='.$id_portfolio.'&result='.( $re ? 'ok_5' : 'err_5' ));
	}
	
	$data_epf = $man_epf->getEportfolio($id_portfolio);
	
	$title_page = array('index.php?modname=eportfolio&amp;op=eportfolio' => $lang->def('_TITLE_EPORTFOLIO'),
		'index.php?modname=eportfolio&amp;op=epfpdp&amp;id_portfolio='.$id_portfolio => $lang->def('_OPERATION_ON_PDP').': '.$data_epf['title'], 
		$lang->def('_TITLE_DEL_PDP').': <b>'.$data['textof'].'</b>'
	);
	// print page
	$GLOBALS['page']->add(
		getTitleArea($title_page, 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=epfpdp&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK'))
		
		.Form::openForm('form_del_pdp', 'index.php?modname=eportfolio&amp;op=delpdpquest')
		.Form::getHidden('id_pdp', 'id_pdp', $id_pdp)
		
		.getDeleteUi($lang->def('_ARE_YOU_SURE_PDP_QUESTION'), 
			'<b>'.$lang->def('_TEXTOF').': </b> '.$data['textof'], 
			false,
			'confirm',
			'undo' )
		.Form::closeForm()
		.'</div>'
	, 'content');
}

function epfcompetences() {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$mod_perm = checkPerm('mod', true);
	$id_portfolio = importVar('id_portfolio');
	
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	$out 	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$man_epf = new Man_Eportfolio();
	
	$portfolio_info = $man_epf->getEportfolio($id_portfolio);
	
	$tb = new TypeOne(0, $lang->def('_COMPETENCES_CAPTION'), $lang->def('_COMPETENCES_SUMMARY'));
	$nav_bar = new NavBar('ini', $GLOBALS['lms']['visuItem'], 0, 'button');
	$ini = $nav_bar->getSelectedElement();
	//search query
	
	//do query
	$re_pdp = $man_epf->getQueryCompetenceOfEportfolio($id_portfolio, $ini, $GLOBALS['lms']['visuItem']);
	$tot_pages = $man_epf->getTotalOfCompetence($id_portfolio);
	
	$nav_bar->setElementTotal($tot_pages);
	
	//-Table---------------------------------------------------------
	$cont_h = array(	$lang->def('_SEQUENCE_NUMBER'), 
						$lang->def('_TEXTOF_COMPETENCE'), 
						$lang->def('_VALUE_SCALE'),
						'<img src="'.getPathImage('lms').'eportfolio/unblocked_competence.gif" alt="'.$lang->def('_BLOCKED_COMPETENCE').'" />' );
	$type_h = array('image', '', 'image', 'image');
	if($mod_perm) {
		
		$cont_h[] = '<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').'" title="'.$lang->def('_MOVE_DOWN').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').'" title="'.$lang->def('_MOVE_UP').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_TITLE_MOD_COMPETENCE').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" title="'.$lang->def('_TITLE_DEL_COMPETENCE').'" />';
		$type_h[] = 'image';
	}
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	$i = 1;
	if($re_pdp != false) {
		
		$tot_elem = mysql_num_rows($re_pdp);
		while(list($id_competence, $texof, $min_score, $max_score, $sequence, $block_competence) = mysql_fetch_row($re_pdp)) {
		
			$cont = array(
				$sequence, 
				$texof, 
				$min_score.' - '.$max_score,
				( $block_competence == 0
					? '<a href="index.php?modname=eportfolio&amp;op=blockcompetence&amp;id_portfolio='.$id_portfolio.'&amp;id_competence='.$id_competence.'" title="'.$lang->def('_BLOCK_COMPETENCE').' : '.$sequence.'">'
							.'<img src="'.getPathImage('lms').'eportfolio/unblocked_competence.gif" alt="'.$lang->def('_UNBLOCKED_COMPETENCE').' : '.$sequence.'" /></a>'
					: '<a href="index.php?modname=eportfolio&amp;op=unblockcompetence&amp;id_portfolio='.$id_portfolio.'&amp;id_competence='.$id_competence.'" title="'.$lang->def('_UNBLOCK_COMPETENCE').' : '.$sequence.'">'
							.'<img src="'.getPathImage('lms').'eportfolio/blocked_competence.gif" alt="'.$lang->def('_BLOCKED_COMPETENCE').' : '.$sequence.'" /></a>' )
			); 
			
			if($mod_perm) {
				if($i != $tot_elem) {
					$cont[] = '<a href="index.php?modname=eportfolio&amp;op=downcompetence&amp;id_competence='.$id_competence.'" title="'.$lang->def('_MOVE_DOWN').' : '.$sequence.'">'
							.'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_MOVE_DOWN').' : '.$sequence.'" /></a>';
				} else {
					$cont[] = '';
				}
				if($i != 1) {
					$cont[] = '<a href="index.php?modname=eportfolio&amp;op=upcompetence&amp;id_competence='.$id_competence.'" title="'.$lang->def('_MOVE_UP').' : '.$sequence.'">'
							.'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_MOVE_UP').' : '.$sequence.'" /></a>';
				} else {
					$cont[] = '';
				}
				$cont[] = '<a href="index.php?modname=eportfolio&amp;op=modepfcompetences&amp;id_competence='.$id_competence.'" title="'.$lang->def('_TITLE_MOD_PDP').' : '.$sequence.'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$sequence.'" /></a>';
				
				$cont[] = '<a href="index.php?modname=eportfolio&amp;op=delepfcompetences&amp;id_competence='.$id_competence.'" title="'.$lang->def('_TITLE_DEL_PDP').' : '.$sequence.'">'
						.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$sequence.'" /></a>';
			}
			$tb->addBody($cont);
			$i++;
		}
	}
	if($mod_perm) {
		$tb->addActionAdd('<a class="new_element_link_float" href="index.php?modname=eportfolio&amp;op=modepfcompetences&amp;id_portfolio='.$id_portfolio.'" title="'.$lang->def('_TITLE_ADD_COMPETENCE').'">'
			.$lang->def('_ADD_COMPETENCE').'</a>');
	}
	
	$clang=&DoceboLanguage::CreateInstance('competences', 'lms');
	//visualize result
	$out->add(
		getTitleArea(array('index.php?modname=eportfolio&amp;op=eportfolio' => $lang->def('_TITLE_EPORTFOLIO'), 
			$clang->def('_COMPETENCES').' : '.$portfolio_info['title']), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=eportfolio', $lang->def('_BACK')) );
	if(isset($_GET['result'])) $out->add(guiResultStatus($lang, $_GET['result']));
	$out->add(
		$tb->getTable()
		.Form::openForm('nav_competence', 'index.php?modname=eportfolio&amp;op=epfcompetences')
		.Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio)
		.$nav_bar->getNavBar($ini)
		.Form::closeForm()
		.'</div>');
}

function modepfcompetences() {
	checkPerm('mod');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	$out 	=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$man_epf = new Man_Eportfolio();
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$id_competence 	= importVar('id_competence', true, 0);
	
	if($id_portfolio == 0) {
		
		//load pdp question
		$load 			= true;
		$data 			= $man_epf->getCompetenceDetails($id_competence);
		$id_portfolio 	= $data['id_portfolio'];
	} else {
		
		$load 				= false;
		$data['textof'] 	= '';
		$data['min_score'] 	= 1;
		$data['max_score']  = 10;
	}
	$portfolio_info = $man_epf->getEportfolio($id_portfolio);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio' => $lang->def('_TITLE_EPORTFOLIO'), 
			'index.php?modname=eportfolio&amp;op=epfcompetences&amp;id_portfolio='.$id_portfolio => $lang->def('_OPERATION_ON_COMPETENCE').': '.$portfolio_info['title'], 
			( !$load ? $lang->def('_ADD_COMPETENCE') : strip_tags($data['textof']) )
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=epfcompetences&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK'))
	);
	if(isset($_POST['undo'])) jumpTo('index.php?modname=eportfolio&op=epfcompetences&id_portfolio='.$id_portfolio);
	if(isset($_POST['save_competence'])) {
		
		if(trim($_POST['textof']) == '') {
			
			$GLOBALS['page']->add(getErrorUi($lang->def('_ERR_COMPETENCE_EMPTY')));
		} else {
			
			if($_POST['min_score'] > $_POST['max_score']) {
				
				$temp = $_POST['min_score'];
				$_POST['min_score'] = $_POST['max_score'];
				$_POST['max_score'] = $temp;
			}
			if($id_portfolio == 0) {
				$data 			= $man_epf->getCompetenceDetails($id_competence);
				$id_portfolio 	= $data['id_portfolio'];
			}
			$re = $man_epf->saveCompeteceQuestion(($id_competence != 0 ? $id_competence :false ), $id_portfolio, $_POST);
			jumpTo('index.php?modname=eportfolio&op=epfcompetences&id_portfolio='.$id_portfolio.'&result='.( $re ? 'ok_7' : 'err_7' ));
		}
	}
	
	$GLOBALS['page']->add(
		Form::openForm('form_add_pdp', 'index.php?modname=eportfolio&amp;op=modepfcompetences')
		//----------------------------------------
		.Form::openElementSpace()
		
		.( !$load ? 
			Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio) : 
			Form::getHidden('id_competence', 'id_competence', $id_competence) )
		
		.Form::getTextarea($lang->def('_TEXTOF'), 'textof', 'textof', 
			( isset($_POST['textof']) ? stripslashes($_POST['textof']) : $data['textof'] ) )
		
		.Form::getTextfield($lang->def('_MIN_SCORE_COMPETENCE'), 'min_score', 'min_score', 5, 
			( isset($_POST['min_score']) ? $_POST['min_score'] : $data['min_score'] ) )
		.Form::getTextfield($lang->def('_MAX_SCORE_COMPETENCE'), 'max_score', 'max_score', 5, 
			( isset($_POST['max_score']) ? $_POST['max_score'] : $data['max_score'] ) )
		
		.Form::closeElementSpace()
		//----------------------------------------
		.Form::openButtonSpace()
		.Form::getButton('save_competence', 'save_competence', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		.'</div>'
	, 'content');
}

function delepfcompetences() {
	checkPerm('mod');
	
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	//load eportfolio data
	require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
	$man_epf 		= new Man_Eportfolio();
	$id_competence 	= importVar('id_competence', true, 0);
	$data 			= $man_epf->getCompetenceDetails($id_competence);
	$id_portfolio 	= $data['id_portfolio'];
	
	if(isset($_POST['undo'])) {
		jumpTo('index.php?modname=eportfolio&op=epfcompetences&id_portfolio='.$id_portfolio);
	}
	if(isset($_POST['confirm'])) {
		
		$re = $man_epf->deleteCompetence($id_competence);
		jumpTo('index.php?modname=eportfolio&op=epfcompetences&id_portfolio='.$id_portfolio.'&result='.( $re ? 'ok_8' : 'err_8' ));
	}
	
	$data_epf = $man_epf->getEportfolio($id_portfolio);
	
	$title_page = array('index.php?modname=eportfolio&amp;op=eportfolio' => $lang->def('_TITLE_EPORTFOLIO'),
		'index.php?modname=eportfolio&amp;op=epfcompetences&amp;id_portfolio='.$id_portfolio => $lang->def('_OPERATION_ON_COMPETENCE').': '.$data_epf['title'], 
		$lang->def('_TITLE_DEL_COMPETENCE').': <b>'.$data['textof'].'</b>'
	);
	// print page
	$GLOBALS['page']->add(
		getTitleArea($title_page, 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=epfcompetences&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK'))
		
		.Form::openForm('form_del_competence', 'index.php?modname=eportfolio&amp;op=delepfcompetences')
		.Form::getHidden('id_competence', 'id_competence', $id_competence)
		
		.getDeleteUi($lang->def('_ARE_YOU_SURE_COMPETENCE'), 
			'<b>'.$lang->def('_TEXTOF_COMPETENCE').': </b> '.$data['textof'], 
			false,
			'confirm',
			'undo' )
		.Form::closeForm()
		.'</div>'
	, 'content');
}

//==================================================================

function eportfolioDispatch($op) {
	
	if(isset($_POST['cancelselector'])) $op = 'eportfolio';
	switch($op) {
		case "eportfolio" : {
			eportfolio();
		};break;
		case "modepf" : {
			modepf();
		};break;
		case "delepf" : {
			delepf();
		};break;
		
		case "modepfuser" : {
			modepfuser();
		};break;
		case "modepfadmin" : {
			modepfadmin();
		};break;
		
		case "epfpdp" : {
			epfpdp();
		};break;
		
		case "downpdp" : {
			
			$man_epf = new Man_Eportfolio();
			$id_pdp 	= importVar('id_pdp', true, 0);
			$data 		= $man_epf->getPdpDetails($id_pdp);
			$man_epf->movePdp('down', $id_pdp, $data['id_portfolio']);
			jumpTo('index.php?modname=eportfolio&op=epfpdp&id_portfolio='.$data['id_portfolio']);
		};break;
		case "uppdp" : {
			$man_epf = new Man_Eportfolio();
			$id_pdp 	= importVar('id_pdp', true, 0);
			$data 		= $man_epf->getPdpDetails($id_pdp);
			$man_epf->movePdp('up', $id_pdp, $data['id_portfolio']);
			jumpTo('index.php?modname=eportfolio&op=epfpdp&id_portfolio='.$data['id_portfolio']);
		};break;
		
		case "modpdpquest" : {
			modpdpquest();
		};break;
		case "delpdpquest" : {
			delpdpquest();
		};break;
		
		case "epfcompetences" : {
			epfcompetences();
		};break;
		
		case "downcompetence" : {
			
			$man_epf = new Man_Eportfolio();
			$id_competence 	= importVar('id_competence', true, 0);
			$data 			= $man_epf->getCompetenceDetails($id_competence);
			$man_epf->moveCompetence('down', $id_competence, $data['id_portfolio']);
			jumpTo('index.php?modname=eportfolio&op=epfcompetences&id_portfolio='.$data['id_portfolio']);
		};break;
		case "upcompetence" : {
			$man_epf = new Man_Eportfolio();
			$id_competence 	= importVar('id_competence', true, 0);
			$data 			= $man_epf->getCompetenceDetails($id_competence);
			$man_epf->moveCompetence('up', $id_competence, $data['id_portfolio']);
			jumpTo('index.php?modname=eportfolio&op=epfcompetences&id_portfolio='.$data['id_portfolio']);
		};break;
		
		case "modepfcompetences" : {
			modepfcompetences();
		};break;
		case "delepfcompetences" : {
			delepfcompetences();
		};break;
		
		case "blockcompetence" : {
			require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
			
			$man_epf = new Man_Eportfolio();
			$man_epf->modBlockCompeteceQuestion(importVar('id_competence', true, 0), 1);
			epfcompetences();
		};break;
		case "unblockcompetence" : {
			require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
			
			$man_epf = new Man_Eportfolio();
			$man_epf->modBlockCompeteceQuestion(importVar('id_competence', true, 0), 0);
			epfcompetences();
		};break;
	}
}
?>