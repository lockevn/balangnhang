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

if($GLOBALS['current_user']->isAnonymous()) die('You can\'t access');

/**
 * @package doceboLms
 * @subpackage e-portfolio 
 * @author	 Fabio Pirovano <fabio [at] docebo-com>
 * @version  $Id:$
 * @since 3.1.0
 */

function modepfuser() {
	checkPerm('view');
	
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
	$user_select->show_orgchart_simple_selector = TRUE;
	$user_select->multi_choice = TRUE;
	$user_select->nFields = 0;
	
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

function epfpdp() {
	checkPerm('view');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$mod_perm = checkPerm('view', true);
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
		
		$cont_h[] = '<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_ALT_ORD_DESC').'" title="'.$lang->def('_TITLE_ORD_DESC_PDP').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_ALT_ORD_ASC').'" title="'.$lang->def('_TITLE_ORD_ASC_PDP').'" />';
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
							.'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').' : '.$sequence.'" /></a>';
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
	checkPerm('view');
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
	checkPerm('view');
	
	$lang	=& DoceboLanguage::createInstance('eportfolio', 'lms');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	//load eportfolio data
	require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
	$man_epf 		= new Man_Eportfolio();
	$id_pdp 		= importVar('id_pdp', true, 0);
	$data 			= $man_epf->getPdpDetails($id_pdp);
	$id_portfolio 	= $data['id_portfolio'];
	
	if(isset($_POST['undo'])) {
		jumpTo('index.php?modname=eportfolio&op=epfpdp&id_portfolio='.$id_portfolio);
	}
	if(isset($_POST['confirm'])) {
		
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
	
	$mod_perm = checkPerm('view', true);
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
						'<img src="'.getPathImage('lms').'eportfolio/blocked_competence.gif" alt="'.$lang->def('_BLOCKED_COMPETENCE').'" />' );
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
							.'<img src="'.getPathImage().'standard/down.gif" alt="'.$lang->def('_DOWN').' : '.$sequence.'" /></a>';
				} else {
					$cont[] = '';
				}
				if($i != 1) {
					$cont[] = '<a href="index.php?modname=eportfolio&amp;op=upcompetence&amp;id_competence='.$id_competence.'" title="'.$lang->def('_MOVE_UP').' : '.$sequence.'">'
							.'<img src="'.getPathImage().'standard/up.gif" alt="'.$lang->def('_UP').' : '.$sequence.'" /></a>';
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
	checkPerm('view');
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
	checkPerm('view');
	
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

?>