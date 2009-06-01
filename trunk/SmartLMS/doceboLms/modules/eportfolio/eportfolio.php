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

define("_SUCCESS_answer","_PDP_ANSWER_OK");
define("_SUCCESS_newpres","_PRESENTATION_INSERT_OK");
define("_SUCCESS_pinvite","_PRESENTATION_INVITE_OK");
define("_SUCCESS_presattach","_PRESENTATION_ATTACH_OK");

define("_FAIL_pdpcannot","_PDP_ANSWER_CANNOT_INSERT");
define("_FAIL_pdpcannotmod","_PDP_ANSWER_CANNOT_MOD");
define("_FAIL_insanswer","_PDP_ANSWER_ERR_INSERT");

require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
require_once($GLOBALS['where_lms'].'/modules/eportfolio/admin.eportfolio.php');

function eportfolio() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$acl 		= $GLOBALS['current_user']->getAcl();
	$man_epf 	= new Man_Eportfolio();
	
	$user_groups 		= $acl->getSTGroupsST(getLogUserId());
	$user_groups[] 		= getLogUserId();
	$eportfolio_lists 	= $man_epf->getEportfolioAssignedTo($user_groups);
	$num_user_epf 		= count($eportfolio_lists['user']);
	$num_admin_epf 		= count($eportfolio_lists['admin']);
	
	if($num_admin_epf == 0) {
		switch($num_user_epf) {
			case 0 : {
				
				$GLOBALS['page']->add(
					getTitleArea($lang->def('_TITLE_EPORTFOLIO_SELECTION'), 'eportfolio')
					.'<div class="std_block">'
					.$lang->def('_NO_EPORTFOLIO_ASSIGNED')
					.'</div>', 'content');
					return;
			};break;
			case 1 : {
				
				if(!isset($_GET['s'])) {
					//if s isset, the user come form the eportfolio details
					showeportfolio(array_pop($eportfolio_lists['user']));
					return;
				}
			};break;
		}
	}
	
	// print table with epf
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_TITLE_EPORTFOLIO_SELECTION'), 'eportfolio')
		.'<div class="std_block">', 'content');
	
	// list of eportfolio assigned as admin user
	if($num_admin_epf != 0) {
		
		$re_admin_epf = $man_epf->getEportfolioInfo($eportfolio_lists['admin']);
		$tb_epf_admin = new TypeOne(0, $lang->def('_CAPTION_EPF_ADMIN'), $lang->def('_SUMMARY_EPF_ADMIN'));
		
		$assigned_user = $man_epf->getNumberOfAssociatedMember(false, 'user');
		
		$cont_h = array(
			$lang->def('_TITLE'), 
			$lang->def('_DESCRIPTION'), 
			'<img src="'.getPathImage().'eportfolio/pdp.gif" alt="'.$lang->def('_ALT_MOD_PDP').'" title="'.$lang->def('_TITLE_MOD_PDP').'" />',
			'<img src="'.getPathImage().'eportfolio/competences.gif" alt="'.$lang->def('_ALT_MOD_COMPETENCES').'" title="'.$lang->def('_TITLE_MOD_COMPETENCES').'" />', 
			'<img src="'.getPathImage().'standard/moduser.gif" alt="'.$lang->def('_ALT_MODUSER').'" title="'.$lang->def('_TITLE_MODUSER_EPORTFOLIO').'" />'
		);
		$type_h = array('epf_title', '', 'image', 'image', 'image');
		
		$tb_epf_admin->setColsStyle($type_h);
		$tb_epf_admin->addHead($cont_h);
		while($row = mysql_fetch_row($re_admin_epf)) {
			
			$cont = array(
			( !isset($assigned_user[$row[EPF_ID]]) 
				? '<img src="'.getPathImage().'standard/warning.gif" title="'.$lang->def('_NO_USER_ASSIGNED').'" alt="!!" /> ' 
				: '' )
				.$row[EPF_TITLE], 
			$row[EPF_DESCRIPTION],
			'<a href="index.php?modname=eportfolio&amp;op=epfpdp&amp;id_portfolio='.$row[EPF_ID].'" 
				title="'.$lang->def('_TITLE_MOD_PDP').' : '.$row[EPF_TITLE].'">'
				.'<img src="'.getPathImage().'eportfolio/pdp.gif" alt="'.$lang->def('_ALT_MOD_PDP').' : '.$row[EPF_TITLE].'" /></a>',
			
			'<a href="index.php?modname=eportfolio&amp;op=epfcompetences&amp;id_portfolio='.$row[EPF_ID].'" 
				title="'.$lang->def('_TITLE_MOD_COMPETENCES').' : '.$row[EPF_TITLE].'">'
				.'<img src="'.getPathImage().'eportfolio/competences.gif" alt="'.$lang->def('_ALT_MOD_COMPETENCES').' : '.$row[EPF_TITLE].'" /></a>', 
			
			'<a href="index.php?modname=eportfolio&amp;op=modepfuser&amp;id_portfolio='.$row[EPF_ID].'&amp;load=1" 
				title="'.$lang->def('_TITLE_MODUSER_EPORTFOLIO').' : '.$row[EPF_TITLE].'">'
				.'<img src="'.getPathImage().'standard/moduser.gif" alt="'.$lang->def('_ALT_MODUSER').' : '.$row[EPF_TITLE].'" /></a>'
			);
			$tb_epf_admin->addBody($cont);
		}
		
		$GLOBALS['page']->add($tb_epf_admin->getTable(), 'content');
	}
	/* ========================================================================================== */
	// list of eportfolio assigned as normal user
	if($num_user_epf != 0) {
		
		if($num_admin_epf != 0) $GLOBALS['page']->add('<div class="epf_space_my_portfoli"></div>', 'content');
		
		$re_user_epf = $man_epf->getEportfolioInfo($eportfolio_lists['user']);
		$tb_epf_user = new TypeOne(0, $lang->def('_CAPTION_EPF'), $lang->def('_SUMMARY_EPF'));
		
		$cont_h = array(
			$lang->def('_TITLE'), 
			$lang->def('_DESCRIPTION')
		);
		$type_h = array('epf_title', '');
		
		$tb_epf_user->setColsStyle($type_h);
		$tb_epf_user->addHead($cont_h);
		
		while($row = mysql_fetch_row($re_user_epf)) {
			
			$cont = array(
				'<a href="index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$row[EPF_ID].'" title="'.$lang->def('_ENTER_EPF').'">'
					.$row[EPF_TITLE].'</a>', 
				$row[EPF_DESCRIPTION]
			);
			$tb_epf_user->addBody($cont);
		}
		
		$GLOBALS['page']->add($tb_epf_user->getTable(), 'content');
	}
	
	$GLOBALS['page']->add('</div>', 'content');
}

function showeportfolio($passed_portfolio) {
    checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, $passed_portfolio);
	$man_epf 		= new Man_Eportfolio();
	
	$epf_info 		= $man_epf->getEportfolio($id_portfolio);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			( !empty($epf_info) ? $epf_info['title'] : $lang->def('_NO_EPF_SELECTED_TITLE') ) ), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1', $lang->def('_BACK')), 'content');
	if(empty($epf_info)) {
		$GLOBALS['page']->add($lang->def('_NO_EPF_SELECTED').'</div>', 'content');
		return;
	}
	$GLOBALS['page']->add('<p id="choose_zone">'.$lang->def('_CHOOSE_ZONE').'</p>', 'content');
	
	$curriculum = $man_epf->getCurriculum($id_portfolio, getLogUserId());
	
	$GLOBALS['page']->add(
		
		
		'<h2 id="curriculum_zone">'
			.'<a href="index.php?modname=eportfolio&amp;op=updatecurriculum&amp;id_portfolio='.$id_portfolio.'">'.$lang->def('_CURRICULUM_ZONE').'</a></h2>'
		.'<p class="curriculum_zone_description">'
			.$lang->def('_CURRICULUM_ZONE_DESCRIPTION')
		.'</p>'
		.( $curriculum === false 
			? 	'<p class="curriculum_not_loaded">'
					.'<i>'.$lang->def('_NO_CURRICULUM').'</i>'
				.'</p>'
			:	'<p class="curriculum_loaded">'
					.$lang->def('_CURRICULUM_LOADED').' : <a class="down_curriculum" href="index.php?modname=eportfolio&amp;op=downloadcurriculum&amp;id_portfolio='.$id_portfolio.'">'
					.$lang->def('_DOWNLOAD_CURRICULUM').'</a>'
					.' ( <span>'.$GLOBALS['regset']->databaseToRegional($curriculum[CURRICULUM_DATE], 'date').'</span> )'
				.'</p>'
		)
		
		.'<h2 id="pdp_zone">'
			.'<a href="index.php?modname=eportfolio&amp;op=showpdp&amp;id_portfolio='.$id_portfolio.'" 
				title="'.$lang->def('_GOTO_PDP_ZONE').'">'
			.$lang->def('_PDP_ZONE').'</a></h2>'
		.'<p class="epf_zone_description">'
			.( $epf_info['custom_pdp_descr'] != '' ? $epf_info['custom_pdp_descr'] : $lang->def('_PDP_ZONE_DESCRIPTION') )
		.'</p>'
		
		.'<h2 id="competence_zone">'
			.'<a href="index.php?modname=eportfolio&amp;op=showcompetences&amp;id_portfolio='.$id_portfolio.'" 
				title="'.$lang->def('_GOTO_COMPETENCES_ZONE').'">'
			.$lang->def('_COMPETENCES_ZONE').'</a></h2>'
		.'<p class="epf_zone_description">'
			.( $epf_info['custom_competence_descr'] != '' ? $epf_info['custom_competence_descr'] : $lang->def('_COMPETENCES_ZONE_DESCRIPTION') )
		.'</p>'
		
		.'<h2 id="presentation_zone">'
			.'<a href="index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio.'" 
				title="'.$lang->def('_GOTO_PRESENTATIONS_ZONE').'">'
			.$lang->def('_PRESENTATIONS_ZONE').'</a></h2>'
		.'<p class="epf_zone_description">'
			.$lang->def('_PRESENTATIONS_ZONE_DESCRIPTION')
		.'</p>'
	, 'content');
	
	
	$invite_number = $man_epf->getCompetenceInviteNumber(getLogUserId());
	switch($invite_number) {
		case 0 : {};break;
		case 1 : { 
			$re_invite = $man_epf->getAllCompetenceInvite(getLogUserId());
			$invite_info = mysql_fetch_row($re_invite);
			
			$acl_man =& $GLOBALS ['current_user']->getAclManager();
			$username = $acl_man->getUserName($invite_info[INVITE_SENDER]);	
					
			$GLOBALS['page']->add(
				'<p class="eportfolio_invite_request">'
				.'<a href="index.php?modname=eportfolio&amp;op=evalcompetencescore&amp;id_portfolio='.$id_portfolio.'&amp;on_portfolio='.$invite_info[INVITE_ID_PORTFOLIO]
						.'&amp;sender='.$invite_info[INVITE_SENDER].'" 
						title="'.$lang->def('_EVALUATE_USER').'">'
					.str_replace('[user]', $username, $lang->def('_HAS_INVITE_YOU')).'</a>'
				.'</p>'
			, 'content');
		};break;
		default : {
						
			$GLOBALS['page']->add(
				'<p class="eportfolio_invite_request">'
				.'<a href="index.php?modname=eportfolio&amp;op=listrecivedinviteforcompetence&amp;id_portfolio='.$id_portfolio .'
						title="'.$lang->def('_EVALUATE_USER').'">'
					.str_replace('[invite_num]', $invite_number, $lang->def('_NUMBER_OF_INVITE')).'</a>'
				.'</p>'
			, 'content');
		}
	}
	
	$GLOBALS['page']->add('</div>', 'content');
}

function showpdp() {
    checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$man_epf 		= new Man_Eportfolio();
	$epf_info 		= $man_epf->getEportfolio($id_portfolio);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			$lang->def('_PDP_ZONE')
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK')), 'content');
	if(isset($_GET['result'])) $GLOBALS['page']->add(guiResultStatus($lang, $_GET['result']), 'content');
	
	$re_pdp = $man_epf->getQueryPdpOfEportfolio($id_portfolio);
	
	if(!$re_pdp) $GLOBALS['page']->add(getErrorUi($lang->def('_NO_EPF_SELECTED')), 'content');
	else {
		
		$tb = new TypeOne(0, '', $lang->def('_SUMMARY_PDP_ANSWER'));
		$tb->setTableStyle('epf_answer');
		
		$tb->setColsStyle(array('epf_post_date', '', 'image'));
		$tb->addHead(array($lang->def('_POST_DATE'), $lang->def('_ANSWER'), 
			'<img class="access-only" src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_TITLE_MOD_ANSWER').'" />') );
		
		while($row = mysql_fetch_row($re_pdp)) {
			
			$re_pdp_answer 	= $man_epf->getQueryPdpUserAnswer($row[PDP_ID], getLogUserId());
			$num_answer 	= mysql_num_rows($re_pdp_answer);
			
			$GLOBALS['page']->add('<div class="pdp_question_display">', 'content');
			
			if($num_answer != 0) {
				
				$tb->setCaption($row[PDP_TEXTOF]);
				$tb->emptyBody();
				$end_phrase = '';
				while($answer = mysql_fetch_row($re_pdp_answer)) {
					$cont = array(
						$GLOBALS['regset']->databaseToRegional($answer[PDP_ANSWER_POST_DATE], 'date'), 
						$answer[PDP_ANSWER_TEXTOF]
					);
					switch($row[PDP_ANSWER_MOD_FOR_DAY]) {
						case _MOD_ANSWER_FOREVER : {
							
							$end_phrase = '<p class="forever_answer">'.$lang->def('_CAN_MODIFY_FOREVER').'</p>';
							$cont[] = '<a href="index.php?modname=eportfolio&amp;op=modpdpanswer&amp;id_pdp='.$row[PDP_ID].'&amp;id_answer='.$answer[PDP_ANSWER_ID].'"
											title="'.$lang->def('_TITLE_MOD_ANSWER').' '.$answer[PDP_ANSWER_TEXTOF].'">'
									.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" /></a>';
						};break;
						case _MOD_ANSWER_NEVER : {
							$cont[] = '';
						};break;
						default : {
							
							$limit_date = date("Y-m-d", (fromDatetimeToTimestamp($answer[PDP_ANSWER_POST_DATE]) + $row[PDP_ANSWER_MOD_FOR_DAY]*24*3600) );
							
							if(date("Y-m-d") <= $limit_date) {
								
								$cont[1] .= '<p class="limit_date_answer">'.str_replace(	'[limit_date]', 
												$GLOBALS['regset']->databaseToRegional($limit_date, 'date'), 
												$lang->def('_CAN_MODIFY_FOR')).'</p>';
								
								$cont[] = '<a href="index.php?modname=eportfolio&amp;op=modpdpanswer&amp;id_pdp='.$row[PDP_ID].'&amp;id_answer='.$answer[PDP_ANSWER_ID].'"
												title="'.$lang->def('_TITLE_MOD_ANSWER').' '.$answer[PDP_ANSWER_TEXTOF].'">'
										.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" /></a>';
							} else {
								
								$cont[] = '';
							}
						};break;
					}
					
					$tb->addBody($cont);
				}
				$GLOBALS['page']->add($tb->getTable().$end_phrase, 'content');
			} else {
				
				$GLOBALS['page']->add('<h2>'.$row[PDP_TEXTOF].'</h2>', 'content');
			}
			if($num_answer < $row[PDP_MAX_ANSWER] && $row[PDP_ALLOW_ANSWER] == 'true') {
				
				$GLOBALS['page']->add('<p class="pdp_add_answer">'
					.'<a href="index.php?modname=eportfolio&amp;op=modpdpanswer&amp;id_pdp='.$row[PDP_ID].'" 
						title="'.$lang->def('_ADD_ANSWER_TITLE').' '.$row[PDP_TEXTOF].'">'
						.$lang->def('_ADD_NEW_ANSWER').'</a>'
					.'</p>', 'content');
			} else {
				
				$GLOBALS['page']->add('<p class="pdp_add_answer">'
					.$lang->def('_NOT_OTHER_ANSWER')
					.'</p>', 'content');	
			}
			$GLOBALS['page']->add('</div>', 'content');
		}
	}
	$GLOBALS['page']->add('</div>', 'content');
}

function modpdpanswer() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_answer 	= importVar('id_answer', true, 0);
	$id_pdp 	= importVar('id_pdp', true, 0);
	
	$man_epf 	= new Man_Eportfolio();
	$pdp 		= $man_epf->getPdpDetails($id_pdp);
	$epf_info 	= $man_epf->getEportfolio($pdp[PDP_ID_PORTFOLIO]);
	
	if($id_answer == 0) $answer['textof'] = '';
	else $answer = $man_epf->getPdpAnswer($id_answer, getLogUserId());
	
	if(isset($_POST['save'])) {
		
		$num_answer 	= $man_epf->getCountPdpUserAnswer($id_pdp, getLogUserId());
		
		if(($num_answer >= $pdp[PDP_MAX_ANSWER]) || ($pdp[PDP_ALLOW_ANSWER] == 'false')) {
			
			jumpTo('index.php?modname=eportfolio&amp;op=showpdp&amp;id_portfolio='.$pdp[PDP_ID_PORTFOLIO]
				.'&amp;result=err_pdpcannot');
		}
		if($id_answer != 0) {
			
			switch($pdp[PDP_ANSWER_MOD_FOR_DAY]) {
				case _MOD_ANSWER_NEVER : {
					jumpTo('index.php?modname=eportfolio&amp;op=showpdp&amp;id_portfolio='.$pdp[PDP_ID_PORTFOLIO]
							.'&amp;result=err_pdpcannotmod');
				};break;
				case _MOD_ANSWER_FOREVER : {};break;
				default : {
					
					$limit_date = date("Y-m-d", (fromDatetimeToTimestamp($answer[PDP_ANSWER_POST_DATE]) + $pdp[PDP_ANSWER_MOD_FOR_DAY]*24*3600) );
					if(date("Y-m-d") > $limit_date) {
						
						jumpTo('index.php?modname=eportfolio&amp;op=showpdp&amp;id_portfolio='.$pdp[PDP_ID_PORTFOLIO]
								.'&amp;result=err_pdpcannotmod');
					}
				}
			}
		}
		$re = $man_epf->savePdpAnswer($id_answer, $id_pdp, getLogUserId(), $_POST['pdp_answer']);
		
		jumpTo('index.php?modname=eportfolio&amp;op=showpdp&amp;id_portfolio='.$pdp[PDP_ID_PORTFOLIO]
			.'&amp;result='.( $re !== false ? 'ok_insanswer' : 'err_insanswer' ));
	}
	
	$pdp_answer = '';
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$pdp[PDP_ID_PORTFOLIO] => $epf_info['title'], 
			'index.php?modname=eportfolio&amp;op=showpdp&amp;id_portfolio='.$pdp[PDP_ID_PORTFOLIO] => $lang->def('_PDP_ZONE'), 
			$lang->def('_ANSWER'),
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showpdp&amp;id_portfolio='.$pdp[PDP_ID_PORTFOLIO], $lang->def('_BACK'))
		
		.'<h2 class="give_an_answer">'.$pdp[PDP_TEXTOF].'</h2>'
		.Form::openForm('pdp_answer', 'index.php?modname=eportfolio&amp;op=modpdpanswer')
		
		.Form::openElementSpace()
		.Form::getHidden('id_portfolio', 'id_portfolio', $pdp[PDP_ID_PORTFOLIO])
		.Form::getHidden('id_answer', 'id_answer', $id_answer)
		.Form::getHidden('id_pdp', 'id_pdp', $id_pdp)
		.Form::getTextarea($lang->def('_ANSWER'), 'pdp_answer', 'pdp_answer', $answer['textof'])
		.Form::closeElementSpace()
		
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo_pdp_mod', 'undo_pdp_mod', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		
		.Form::closeForm()
		
		.'</div>', 'content');
}

function showcompetences() {
    checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$man_epf 		= new Man_Eportfolio();
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	
	$epf_info 	= $man_epf->getEportfolio($id_portfolio);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			$lang->def('_COMPETENCES_ZONE')
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK')), 'content');
	if(isset($_GET['result'])) $GLOBALS['page']->add(guiResultStatus($lang, $_GET['result']));
	
	$re_competence = $man_epf->getQueryCompetenceOfEportfolio($id_portfolio);
	
	$user_score = array();
	$self_score = array();
	$users 		= array();
	$re_user_score = $man_epf->getDetailedCompetenceScore($id_portfolio, getLogUserID());
	
	$user = getLogUserID();
	while($s_row = mysql_fetch_row($re_user_score)) {
		
		if($s_row[COMPETENCE_SCORE_STATUS] == C_SCORE_VALID) {
			
			$score = $s_row[COMPETENCE_SCORE_SCORE];
			if($s_row[COMPETENCE_SCORE_FROM_USER] == $user) {
				
				$self_score[$s_row[COMPETENCE_SCORE_ID_COMPETENCE]]['score'] = $score;
				$self_score[$s_row[COMPETENCE_SCORE_ID_COMPETENCE]]['comment'] = $s_row[COMPETENCE_SCORE_COMMENT];
			} else {
				
				$user_score[$s_row[COMPETENCE_SCORE_ID_COMPETENCE]][$s_row[COMPETENCE_SCORE_FROM_USER]] = $score;
				$users[$s_row[COMPETENCE_SCORE_FROM_USER]] = $s_row[COMPETENCE_SCORE_FROM_USER];
			}
		}
	}
	
	$users_info =& $acl_man->getUsers($users);
	//$competences_other_score = $man_epf->getScoreForUser($id_portfolio, getLogUserId());
	
	$tb = new TypeOne(0, $lang->def('_CAPTION_COMPETENCE_SCORE'), $lang->def('_SUMMARY_COMPETENCE_SCORE'));
	$tb->setTableStyle('epf_competence_score');
	
	$type_h = array('', 'competence_score', '');
	$cont_h = array(	$lang->def('_TEXTOF_COMPETENCE'), 
						'<a href="index.php?modname=eportfolio&amp;op=modmyepfcompetencescore&amp;id_portfolio='.$id_portfolio.'" 
							title="'.$lang->def('_MOD_MY_SCORE').'">'.$lang->def('_SELF_EVALUATION').'</a>', 
						$lang->def('_MY_COMMENT') );
	
	$users = array();
	
	if(!empty($users_info))
	while(list($id, $uinfo) = each($users_info)) {
		
		$userid = ( $uinfo[ACL_INFO_LASTNAME].$uinfo[ACL_INFO_FIRSTNAME]
				? $uinfo[ACL_INFO_LASTNAME].' '.$uinfo[ACL_INFO_FIRSTNAME]
				: $acl_man->relativeId($uinfo[ACL_INFO_USERID]) );
		$type_h[] = 'competence_score';
		$cont_h[] = '<a href="index.php?modname=eportfolio&amp;op=showscorecompetence&amp;id_portfolio='.$id_portfolio.'&amp;id_user='.$id.'" 
						title="'.$lang->def('_SHOW_SCORE_OF_USER').': '.$userid.'">'.$userid.'</a>';
			
		$users[$id] = $id;
	}
	$type_h[] = 'competence_score';
	$cont_h[] = $lang->def('_C_SCORE_STATS');
	
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while($competence = mysql_fetch_row($re_competence)) {
		
		$cont = array($competence[COMPETENCE_TEXTOF]);
		if(isset($self_score[$competence[COMPETENCE_ID]])) {
			
			$cont[] = $self_score[$competence[COMPETENCE_ID]]['score'];
			$cont[] = $self_score[$competence[COMPETENCE_ID]]['comment'];
		} else {
			
			$cont[] = '';
			$cont[] = '';
		}
		$competence_divisor = 0;
		if(!empty($users_info))
		foreach($users as $id) {
			
			if(isset($user_score[$competence[COMPETENCE_ID]][$id])) {
				
				$score = $user_score[$competence[COMPETENCE_ID]][$id];
				
				if(isset($stats[$competence[COMPETENCE_ID]])) {
					$stats[$competence[COMPETENCE_ID]] += $score;
				} else {
					$stats[$competence[COMPETENCE_ID]] = $score;
				}
				$cont[] = $score;
				
				$competence_divisor++;
			} else $cont[] = '';
		}
		if(isset($user_score[$competence[COMPETENCE_ID]]) && $competence_divisor > 0) {
			
			if(isset($self_score[$competence[COMPETENCE_ID]])) {
				
				$averange = $stats[$competence[COMPETENCE_ID]] / $competence_divisor;
				$diff = ($averange - $self_score[$competence[COMPETENCE_ID]]['score']);
				$cont[] = $averange.' ( '.( $diff > 0 ? '+'.$diff : ( $diff == 0 ? '0' : $diff ) ).' )';
			} else {
				
				$averange = $stats[$competence[COMPETENCE_ID]] / $competence_divisor;
				$cont[] = $averange.' ( ? )';
			}
		} else $cont[] =''; 
		$tb->addBody($cont);
	}
	
	$GLOBALS['page']->add(
		$tb->getTable()
		
		.'<p class="epfsendinvite">'
		.'<a href="index.php?modname=eportfolio&amp;op=competenceinvite&amp;id_portfolio='.$id_portfolio.'" title="'.$lang->def('_SEND_INVITE_TITLE').'">'
		.$lang->def('_SEND_INVITE').'</a>'
		.'</p>'
		
		.'</div>', 'content');
}

function modmyepfcompetencescore() {
    checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$man_epf 	= new Man_Eportfolio();
	$epf_info 	= $man_epf->getEportfolio($id_portfolio);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			'index.php?modname=eportfolio&amp;op=showcompetences&amp;id_portfolio='.$id_portfolio => $lang->def('_COMPETENCES_ZONE'),
			$lang->def('_SELF_EVALUATION')
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showcompetences&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK')), 'content');
	
	$GLOBALS['page']->add(
		Form::openForm('evaluate_user', 'index.php?modname=eportfolio&amp;op=saveepfcompetencescore')
		.Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio)
		.Form::getHidden('on_portfolio', 'on_portfolio', $id_portfolio)
		.Form::getHidden('extimated_user', 'extimated_user', getLogUserId())
		.Form::getHidden('from_user', 'from_user', getLogUserId())
		
		.maskModEpfCompetence($id_portfolio, getLogUserId(), getLogUserId())
		
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo_compscore', 'undo_compscore', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
	, 'content');
	
	$GLOBALS['page']->add('</div>', 'content');
}

/**
 * @param int	$id_portfolio 	the id of the portfolio
 * @param int	$extimated_user	the id of the user to evaluate
 * @param int 	$from_user 		the user that will insert the score
 * 
 * @return string the html of the mask
 */
function maskModEpfCompetence($id_portfolio, $extimated_user, $from_user) {
	
	$lang 		=& DoceboLanguage::createInstance('eportfolio');
	$man_epf 	= new Man_Eportfolio();
	$acl_man 	= $GLOBALS['current_user']->getAclManager();
	
	$re_competence = $man_epf->getQueryCompetenceOfEportfolio($id_portfolio);
	
	$user_score = array();
	$self_score = array();
	$users 		= array();
	$re_user_score = $man_epf->getDetailedCompetenceScore($id_portfolio, $extimated_user, $from_user);
	
	while($s_row = mysql_fetch_row($re_user_score)) {
		
		if($s_row[COMPETENCE_SCORE_STATUS] == C_SCORE_VALID) {
			
			$score[$s_row[COMPETENCE_SCORE_ID_COMPETENCE]] = $s_row[COMPETENCE_SCORE_SCORE];
			$comment[$s_row[COMPETENCE_SCORE_ID_COMPETENCE]] = $s_row[COMPETENCE_SCORE_COMMENT];
		}
	}
	$tb = new TypeOne(0, $lang->def('_CAPTION_COMPETENCE_SCORE'), $lang->def('_SUMMARY_COMPETENCE_SCORE'));
	$tb->setTableStyle('epf_competence_score');
	
	$type_h = array('assign_score', 'assign_score_little', 'assign_score_little');
	$cont_h = array(	$lang->def('_TEXTOF_COMPETENCE'), 
						$lang->def('_SCORE'), 
						$lang->def('_COMMENT') );
	
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while($competence = mysql_fetch_row($re_competence)) {
		
		for($i = $competence[COMPETENCE_MIN_SCORE]; $i <= $competence[COMPETENCE_MAX_SCORE]; $i++) {
			$values[$i] = $i;
		}
		
		$cont = array($competence[COMPETENCE_TEXTOF]);
		if($competence[COMPETENCE_BLOCK] == 0) {
				
			$cont[] = Form::getLabel(	'score_'.$competence[COMPETENCE_ID], 
										$lang->def('_SCORE'), 
										'access-only')
					.Form::getInputDropdown('dropdown_nowh', 
											'score_'.$competence[COMPETENCE_ID],
											'score['.$competence[COMPETENCE_ID].']', 
											$values, 
											( isset($score[$competence[COMPETENCE_ID]]) ? 
												$score[$competence[COMPETENCE_ID]] : 
												$competence[COMPETENCE_MIN_SCORE] ),
											''
										);
										
			$cont[] = 	Form::getLabel(	'comment_'.$competence[COMPETENCE_ID], 
										$lang->def('_COMMENT'), 
										'access-only')
						.Form::getInputTextarea('comment_'.$competence[COMPETENCE_ID],
												'comment['.$competence[COMPETENCE_ID].']',
												( isset($comment[$competence[COMPETENCE_ID]]) ? 
													$comment[$competence[COMPETENCE_ID]] : 
													'' ),
												'textarea_nowh',
												5,
												55
											);
		} else {
			$cont[] = '';
			$cont[] = $lang->def('_BLOCKED_COMPETENCE');
		}
		$tb->addBody($cont);
	}
	
	return $tb->getTable();
}

function epfusercard($id_user, $epf_info, $message, $extra = '') {

	$acl_man 	=& $GLOBALS['current_user']->getAclManager();
	$lang 		=& DoceboLanguage::createInstance('eportfolio');
	$user_info 	= $acl_man->getUser($id_user, false);
	
	if($user_info[ACL_INFO_AVATAR] != '') {
		
		$path 		= $GLOBALS['where_files_relative'].'/doceboCore/'.$GLOBALS['framework']['pathphoto'];
		$img_size 	= @getimagesize($path.$user_info[ACL_INFO_AVATAR]);
	}
	$html = '<div class="epf_usercard">'
		.$lang->def('_THEUSER').' : <b>'.$acl_man->getConvertedUserName($user_info).'</b><br />'
		.( $user_info[ACL_INFO_AVATAR] == '' 
			? '<img src="'.getPathImage('fw').'/standard/avatar_unknow.gif" alt="'.$lang->def('_AVATAR').'" />'
			: '<img '.( $img_size[0] > 150 || $img_size[1] > 150 ? 'class="image_limit""' : '' ).' src="'.$user_info[ACL_INFO_AVATAR].'" alt="'.$lang->def('_AVATAR').'" />'
		)
		.'<b>'.$lang->def('_INTRO_MESSAGE_TEXT').'</b><br />'
		.'<div class="user_message">'.( $message != '' ? $message : $lang->def('_NOMESSAGE') ).'</div>'
		.'<b>'.$lang->def('_TITLE').' : </b>'.$epf_info['title'].'<br />'
		.'<b>'.$lang->def('_DESCRIPTION').' : </b>'.$epf_info['description'].'<br />'.'<br />'
		.'<div class="no_float"></div>'
		.$extra
		.'</div>';
		
	return $html;
}

function listrecivedinviteforcompetence() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$man_epf 		= new Man_Eportfolio();
	$epf_info 		= $man_epf->getEportfolio($id_portfolio);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			$lang->def('_INVITE_LIST')
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK')), 'content');
	$re_invite = $man_epf->getAllCompetenceInvite(getLogUserId());
	while($invite_info = mysql_fetch_row($re_invite)) {
		
		$on_epf_info = $man_epf->getEportfolio($invite_info[INVITE_ID_PORTFOLIO]);
		
		$GLOBALS['page']->add(
			epfusercard(	$invite_info[INVITE_SENDER], 
							$on_epf_info, 
							$invite_info[INVITE_MESSAGE_TEXT], 
							'<p class="eportfolio_invite_request">'
							.'<a href="index.php?modname=eportfolio&amp;op=evalcompetencescore&amp;id_portfolio='.$id_portfolio.'&amp;on_portfolio='.$invite_info[INVITE_ID_PORTFOLIO]
									.'&amp;sender='.$invite_info[INVITE_SENDER].'" 
									title="'.$lang->def('_EVALUATE_USER').'">'
								.$lang->def('_EVALUATE_USER').'</a>'
							.'</p>'
							
							.'<p class="eportfolio_invite_refuse">'
							.'<a href="index.php?modname=eportfolio&amp;op=refusecompetenceinvite&amp;id_portfolio='.$id_portfolio.'&amp;on_portfolio='.$invite_info[INVITE_ID_PORTFOLIO]
									.'&amp;sender='.$invite_info[INVITE_SENDER].'&amp;return=invitelist" 
									title="'.$lang->def('_REFUSE_INVITE_TITLE').'">'
								.$lang->def('_REFUSE_INVITE').'</a>'
							.'</p>')
		, 'content');
	}
	$GLOBALS['page']->add('</div>', 'content');
}

function evalcompetencescore() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$on_portfolio 	= importVar('on_portfolio', true, 0);
	$man_epf 		= new Man_Eportfolio();
	$epf_info 		= $man_epf->getEportfolio($id_portfolio);
	$sender 		= importVar('sender', true, 0);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			'index.php?modname=eportfolio&amp;op=listrecivedinviteforcompetence&amp;id_portfolio='.$id_portfolio => $lang->def('_INVITE_LIST'),
			$lang->def('_EVALUATE_USER')
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showcompetences&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK')), 'content');
	
	// user card
	$on_epf_info = $man_epf->getEportfolio($on_portfolio);
	$invite_info = $man_epf->getCompetenceInvite(getLogUserId(), $sender, $on_portfolio);
	$GLOBALS['page']->add(
		epfusercard($sender, $on_epf_info, $invite_info[INVITE_MESSAGE_TEXT],
			'<p class="eportfolio_invite_refuse">'
				.'<a href="index.php?modname=eportfolio&amp;op=refusecompetenceinvite&amp;id_portfolio='.$id_portfolio.'&amp;on_portfolio='.$invite_info[INVITE_ID_PORTFOLIO]
						.'&amp;sender='.$invite_info[INVITE_SENDER].'&amp;return=competence" 
						title="'.$lang->def('_REFUSE_INVITE_TITLE').'">'
					.$lang->def('_REFUSE_INVITE').'</a>'
			.'</p>')
	, 'content');
	
	$GLOBALS['page']->add(
		Form::openForm('evaluate_user', 'index.php?modname=eportfolio&amp;op=saveepfcompetencescore')
		
		.Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio)
		.Form::getHidden('on_portfolio', 'on_portfolio', $on_portfolio)
		.Form::getHidden('extimated_user', 'extimated_user', $sender)
		.Form::getHidden('from_user', 'from_user', getLogUserId())
		
		.Form::getHidden('back_to_function', 'back_to_function', 'listrecivedinviteforcompetence')
		
		.maskModEpfCompetence($on_portfolio, $sender, getLogUserId())
		
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo_compscore', 'undo_compscore', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
	, 'content');
	
	$GLOBALS['page']->add('</div>', 'content');
}

function saveepfcompetencescore() {
	checkPerm('view');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$on_portfolio 	= importVar('on_portfolio', true, $id_portfolio);
	$extimated_user = importVar('extimated_user', true, 0);
	$from_user 		= importVar('from_user', true, 0);
	
	$back_to_function = importVar('back_to_function', false, 'showcompetences');
	
	$man_epf 	= new Man_Eportfolio();
	
	if(!isset($_POST['score']) && !is_array($_POST['score'])) {
		jumpTo('index.php?modname=eportfolio&amp;op='.$back_to_function.'&amp;id_portfolio='.$id_portfolio.'&amp;re=compscore_fail');
	}
	
	$re = $man_epf->saveAllCompetenceScore($on_portfolio, $extimated_user, $from_user, $_POST['score'], $_POST['comment']);
	if($re) {
		$man_epf->deleteCompetenceInvite($on_portfolio, $extimated_user, $from_user);
	}
	
	$re_invite = $man_epf->getAllCompetenceInvite(getLogUserId());
	if(!mysql_num_rows($re_invite)) {
		jumpTo('index.php?modname=eportfolio&amp;op=showcompetences&amp;id_portfolio='.$id_portfolio.( $re ? 'compscore_ok' : 'compscore_fail' ) );
	}
	jumpTo('index.php?modname=eportfolio&amp;op='.$back_to_function.'&amp;id_portfolio='.$id_portfolio.'&amp;re='.( $re ? 'compscore_ok' : 'compscore_fail' ) );
}

function showscorecompetence() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$id_user 		= importVar('id_user', true, 0);
	$man_epf 		= new Man_Eportfolio();
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	
	$epf_info 	= $man_epf->getEportfolio($id_portfolio);
	
	
	$re_competence = $man_epf->getQueryCompetenceOfEportfolio($id_portfolio);
	
	$user_score = array();
	$self_score = array();
	$users 		= array();
	$re_user_score = $man_epf->getDetailedCompetenceScore($id_portfolio, getLogUserId(), $id_user);
	
	$user = getLogUserID();
	while($s_row = mysql_fetch_row($re_user_score)) {
		
		if($s_row[COMPETENCE_SCORE_STATUS] == C_SCORE_VALID) {
			
			$user_score[$s_row[COMPETENCE_SCORE_ID_COMPETENCE]]['score'] = $s_row[COMPETENCE_SCORE_SCORE];
			$user_score[$s_row[COMPETENCE_SCORE_ID_COMPETENCE]]['comment'] = $s_row[COMPETENCE_SCORE_COMMENT];
		}
	}
	$users_info = $acl_man->getUser($id_user, false);
	
	$tb = new TypeOne(0, $lang->def('_CAPTION_COMPETENCE_SCORE'), $lang->def('_SUMMARY_COMPETENCE_SCORE'));
	$tb->setTableStyle('epf_competence_score');
	
	$type_h = array('', 'competence_score', '');
	$cont_h = array($lang->def('_TEXTOF_COMPETENCE'), 
					$lang->def('_SCORE'),
					$lang->def('_COMMENT'));
	
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while($competence = mysql_fetch_row($re_competence)) {
		
		$cont = array($competence[COMPETENCE_TEXTOF]);
		
		if(isset($user_score[$competence[COMPETENCE_ID]]['score'])) {
			$cont[] = $user_score[$competence[COMPETENCE_ID]]['score'];
			$cont[] = $user_score[$competence[COMPETENCE_ID]]['comment'];
		} else {
			$cont[] = '';
			$cont[] = '';
		}
		
		$tb->addBody($cont);
	}
	
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			'index.php?modname=eportfolio&amp;op=showcompetences&amp;id_portfolio='.$id_portfolio => $lang->def('_COMPETENCES_ZONE'),
			$lang->def('_EVALUATION_FROM_USER').' : '.$acl_man->getConvertedUserName($users_info)
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showcompetences&amp;id_portfolio='.$id_portfolio.'&amp;load=1', $lang->def('_BACK')), 'content');
	
	if(isset($_GET['result'])) $GLOBALS['page']->add(guiResultStatus($lang, $_GET['result']));
	
	$GLOBALS['page']->add(
		$tb->getTable()
		.'</div>', 'content');
}

function competenceinvite() {
	checkperm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$man_epf 		= new Man_Eportfolio();
	$epf_info 		= $man_epf->getEportfolio($id_portfolio);
	$acl_man 		= $GLOBALS['current_user']->getAclManager();
	
	$user_select = new Module_Directory();
	$user_select->show_user_selector = TRUE;
	$user_select->show_group_selector = FALSE;
	$user_select->show_orgchart_selector = FALSE;
	$user_select->show_orgchart_simple_selector = FALSE;
	$user_select->multi_choice = TRUE;
	$user_select->nFields = 0;
	
	$members =& $man_epf->getAssociatedMember($id_portfolio);
	$user_members = $acl_man->getUsersFromMixedIdst($members);
	
	$user_select->setUserFilter('user', $user_members);
	$user_select->setUserFilter('group', $members);
	
	// exclude people whit a pendent invite
	$exclude_list 		= array();
	$exclude_list[] 	= getLogUserId();
	$re_invited = $man_epf->getUserInvtedByUser(getLogUserId(), $id_portfolio);
	while(list($invited_user) = mysql_fetch_row($re_invited)) {
		
		$exclude_list[] = $invited_user;
	}
	$user_select->setUserFilter('exclude', $exclude_list);
	
	$title_area = getTitleArea(array(
		'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
		'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
		'index.php?modname=eportfolio&amp;op=showcompetences&amp;id_portfolio='.$id_portfolio => $lang->def('_COMPETENCES_ZONE'), 
		$lang->def('_SEND_INVITE')
	), 'eportfolio');
	
	if(isset($_POST['cancelselector']) || isset($_POST['undo_invite_send'])) {
		
		jumpTo('index.php?modname=eportfolio&amp;op=showcompetences&amp;id_portfolio='.$id_portfolio);
	}
	if(isset($_POST['okselector'])) {
		
		$selection = $user_select->getSelection($_POST);
		$selection_string = urldecode(serialize($selection));
		
		$GLOBALS['page']->add(
			$title_area
			.'<div class="std_block">'
			
			.Form::openForm('send_invite', 'index.php?modname=eportfolio&amp;op=competenceinvite')
			
			.Form::openElementSpace()
			.Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio)
			.Form::getHidden('user_selected', 'user_selected', $selection_string)
			.Form::getTextarea($lang->def('_INTRO_MESSAGE_WRITE'), 'message_from_user', 'message_from_user')
			.Form::closeElementSpace()
			
			.Form::openButtonSpace()
			.Form::getButton('send_invite', 'send_invite', $lang->def('_SEND_INVITE_BUTTON'))
			.Form::getButton('undo_invite_send', 'undo_invite_send', $lang->def('_UNDO'))
			.Form::closeButtonSpace()
			
			.Form::closeForm()
			.'</div>'
		, 'content');
		return;
	}
	if(isset($_POST['send_invite'])) {
		
		$me = getLogUserId();
		$selection = unserialize(urldecode($_POST['user_selected']));
		while(list(, $user) = each($selection)) {
			
			$man_epf->createCompetenceInvite($user, $me, $id_portfolio, $_POST['message_from_user']);
		}
		jumpTo('index.php?modname=eportfolio&amp;op=showcompetences&amp;id_portfolio='.$id_portfolio);
	}
		
	// display selector
	$user_select->setPageTitle($title_area);
	$user_select->loadSelector('index.php?modname=eportfolio&amp;op=competenceinvite&amp;id_portfolio='.$id_portfolio, 
			$lang->def('_TITLE_EPORTFOLIO'), 
			$lang->def('_SELECT_USER_TOINVITE'), 
			true, 
			true );
}

function refusecompetenceinvite() {
	checkperm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/class.module/class.directory.php');
	
	$man_epf 		= new Man_Eportfolio();
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	
	$man_epf->refuseCompetenceInvite(
		getLogUserId(),
		importVar('sender', true, 0),	
		importVar('on_portfolio', true, 0)
	);
	if($_GET['return'] == 'invitelist') jumpTo('index.php?modname=eportfolio&op=listrecivedinviteforcompetence&id_portfolio='.$id_portfolio);
	if($_GET['return'] == 'competence') jumpTo('index.php?modname=eportfolio&amp;op=showcompetences&amp;id_portfolio='.$id_portfolio);
}

// Create a presentation ============================================ //

function showpresentation() {
	checkperm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$man_epf 		= new Man_Eportfolio();
	$epf_info 		= $man_epf->getEportfolio($id_portfolio);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			$lang->def('_PRESENTATIONS_ZONE')
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK')), 'content');
	
	if(isset($_GET['result'])) $GLOBALS['page']->add(guiResultStatus($lang, $_GET['result']), 'content');
	
	$re_pres = $man_epf->getQueryPresentation($id_portfolio, getLogUserId() );
	
	if(!$re_pres) $GLOBALS['page']->add(getErrorUi($lang->def('_PRESENTATION ERROR')), 'content');
	else {
		
		$tb = new TypeOne(0, $lang->def('_CAPTION_PRESENTATION'), $lang->def('_SUMMARY_PRESENTATION'));
		
		$tb->setColsStyle(array('', 'epf_post_date', 'image', 'image', 'image', 'image'));
		$tb->addHead(array($lang->def('_TITLE'), $lang->def('_PUBBLICATION_DATE'), 
			'<img src="'.getPathImage().'standard/view.gif" alt="'.$lang->def('_PREVIEW').'" />', 
			'<img src="'.getPathImage().'standard/attach.gif" alt="'.$lang->def('_ATTACHMENT').'" />', 
			'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" title="'.$lang->def('_TITLE_MOD_PRESENTATION').'" />',
			'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" title="'.$lang->def('_TITLE_REM_PRESENTATION').'" />') );
		
		while($row = mysql_fetch_row($re_pres)) {
			
			$cont = array($row[PRES_TITLE]);
			
			$cont[] = $GLOBALS['regset']->databaseToRegional($row[PRES_PUBBLICATION_DATE], 'date');
			
			$cont[] = '<a href="index.php?modname=eportfolio&amp;op=presentationpreview&amp;id_portfolio='.$id_portfolio.'&amp;id_presentation='.$row[PRES_ID].'"
							title="'.$lang->def('_PRES_PREVIEW').' '.$row[PRES_TITLE].'">'
					.'<img src="'.getPathImage().'standard/view.gif" alt="'.$lang->def('_PREVIEW').'" /></a>';
			
			$cont[] = '<a href="index.php?modname=eportfolio&amp;op=presentationattach&amp;id_portfolio='.$id_portfolio.'&amp;id_presentation='.$row[PRES_ID].'&amp;load=1"
							title="'.$lang->def('_PRES_ATTACH').' '.$row[PRES_TITLE].'">'
					.'<img src="'.getPathImage().'standard/attach.gif" alt="'.$lang->def('_ATTACHMENT').'" /></a>';
				
			$cont[] = '<a href="index.php?modname=eportfolio&amp;op=setuppresentation&amp;id_portfolio='.$id_portfolio.'&amp;id_presentation='.$row[PRES_ID].'"
							title="'.$lang->def('_TITLE_MOD_PRESENTATION').' '.$row[PRES_TITLE].'">'
					.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').'" /></a>';
			
			$cont[] = '<a href="index.php?modname=eportfolio&amp;op=delpresentation&amp;id_portfolio='.$id_portfolio.'&amp;id_presentation='.$row[PRES_ID].'"
							title="'.$lang->def('_TITLE_REM_PRESENTATION').' '.$row[PRES_TITLE].'">'
					.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').'" /></a>';
			$tb->addBody($cont);
		}
		
		$tb->addActionAdd(
			'<a class="new_element_link_float" href="index.php?modname=eportfolio&amp;op=setuppresentation&amp;id_portfolio='.$id_portfolio.'" 
				title="'.$lang->def('_ADD_NEW_PRESENTATION_TITLE').'">'
				.$lang->def('_ADD_NEW_PRESENTATION').'</a>'
			.'</a>');
		
		$GLOBALS['page']->add($tb->getTable(), 'content');
		
		// invite other people to look at your presentation
		if(mysql_num_rows($re_pres)) {
			
			$GLOBALS['page']->add(
				'<p class="epf_pres_invite">'
				.'<a href="index.php?modname=eportfolio&amp;op=sendpresentationinvite&amp;id_portfolio='.$id_portfolio.'">'
				.$lang->def('_SEND_PRESENTATION_INVITE').'</a></p>', 'content');
		}
	}
	
	$GLOBALS['page']->add('</div>', 'content');
}

function setuppresentation() {
	checkperm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$id_presentation = importVar('id_presentation', true, 0);
	$man_epf 		= new Man_Eportfolio();
	$epf_info 		= $man_epf->getEportfolio($id_portfolio);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			'index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio => $lang->def('_PRESENTATIONS_ZONE'), 
			$lang->def('_ADD_NEW_PRESENTATION')
			
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK'))
	, 'content');
	
	if(isset($_POST['attach'])) {
		
	} elseif(isset($_POST['save'])) {
		
		if($id_presentation == 0) {
		
			$re = $man_epf->createPresentation(	$id_portfolio, 
												$_POST['title'], 
												$_POST['textof'],
												getLogUserId(),
												( isset($_POST['show_pdp']) ? '1' : '0' ),
												( isset($_POST['show_competence']) ? '1' : '0' ),
												( isset($_POST['show_curriculum']) ? '1' : '0' ),
												date("Y-m-d H:i:s"),
												'' );
		} else {
		
			$re = $man_epf->updatePresentation(	$id_presentation,
												$id_portfolio, 
												$_POST['title'], 
												$_POST['textof'],
												getLogUserId(),
												( isset($_POST['show_pdp']) ? '1' : '0' ),
												( isset($_POST['show_competence']) ? '1' : '0' ),
												( isset($_POST['show_curriculum']) ? '1' : '0' ),
												date("Y-m-d H:i:s"),
												'' );
		}
		if($re !== false) {
			jumpTo('index.php?modname=eportfolio&op=showpresentation&id_portfolio='.$id_portfolio.'&result=ok_newpres');
		} else {
			$GLOBALS['page']->add(getErrorUi($lang->def('_NEW_PRESENTATION_ERROR')), 'content');
		}
	} 
	
	if($id_presentation && !isset($_POST['title'])) {
		
		$pres = $man_epf->getPresentation($id_presentation);
		$title				= $pres[PRES_TITLE];
		$textof				= $pres[PRES_TEXTOF]; 
		$show_curriculum 	= $pres[PRES_SHOW_CURRICULUM];
		$show_pdp 			= $pres[PRES_SHOW_PDP];
		$show_competence 	= $pres[PRES_SHOW_COMPETENCE];
	} else {
		
		$title 				= stripslashes(importVar('title', false, ''));
		$textof 			= stripslashes(importVar('textof', false, ''));
		$show_curriculum 	= isset($_POST['show_curriculum']);
		$show_pdp 			= isset($_POST['show_pdp']);
		$show_competence 	= isset($_POST['show_competence']);
	}
	//----------------------------------------------------------------
	$GLOBALS['page']->add(
		Form::openForm('new_presentation', 'index.php?modname=eportfolio&amp;op=setuppresentation')
		.Form::openElementSpace()
		
		.Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio)
		.Form::getHidden('id_presentation', 'id_presentation', $id_presentation)
		
		.Form::getTextfield($lang->def('_PRESENTATION_TITLE'), 
							'title',
							'title', 
							255,
							$title )
		.Form::getTextarea(	$lang->def('_PRESENTATION_COMMENT'), 
							'textof', 
							'textof',
							$textof )
		
		.Form::getOpenFieldset($lang->def('_CHOOSE_WHAT_SHOW'))
		.Form::getCheckbox(	$lang->def('_SHOW_MY_CURRICULUM'), 
							'show_curriculum', 
							'show_curriculum', 
							'1', 
							$show_curriculum )
		.Form::getCheckbox(	$lang->def('_SHOW_MY_PDP'), 
							'show_pdp', 
							'show_pdp', 
							'1', 
							$show_pdp )
		.Form::getCheckbox(	$lang->def('_SHOW_MY_COMPETENCE'), 
							'show_competence', 
							'show_competence', 
							'1', 
							$show_competence )
							
		.Form::getCloseFieldset()
		
		.Form::closeElementSpace()
		
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE') )
		.Form::getButton('undo_new_pres', 'undo_new_pres', $lang->def('_UNDO') )
		.Form::closeButtonSpace()
		.Form::closeForm()
	, 'content');
	
	$GLOBALS['page']->add(
		'</div>'
	, 'content');
}

function presentationattach() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.myfiles.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
	
	$lang	=& DoceboLanguage::createInstance('eportfolio');
	
	//load eportfolio data
	
	$id_presentation = importVar('id_presentation', true, 0);
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	
	$man_epf 		= new Man_Eportfolio();
	$epf_info 		= $man_epf->getEportfolio($id_portfolio);
	$pres 			= $man_epf->getPresentation($id_presentation);
	
	$file_sel 		= new MyFileSelector();
	
	if($file_sel->pressedUndo()) {
		jumpTo('index.php?modname=eportfolio&op=showpresentation&id_portfolio='.$id_portfolio);
	}
	if($file_sel->pressedSave()) {
		
		$file_selection = $file_sel->getSelection();
		
		$re = $man_epf->updatePresentationAttach( $id_presentation, getLogUserId(), $file_selection );
		jumpTo('index.php?modname=eportfolio&op=showpresentation&id_portfolio='.$id_portfolio.'&result=ok_presattach');
	}
	if(isset($_GET['load'])) {
		
		$file_selection = $man_epf->getPresentationAttach($id_presentation, getLogUserId());
		$file_sel->setSelection($file_selection);
	}
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			'index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio => $lang->def('_PRESENTATIONS_ZONE'), 
			$lang->def('_ATTACH_FILE_TO_PRES').' : '.$pres[PRES_TITLE]
			
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK'))
	, 'content');
	// print page
	$GLOBALS['page']->add(
		Form::openForm('form_del_pdp', 'index.php?modname=eportfolio&amp;op=presentationattach')
		
		.Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio)
		.Form::getHidden('id_presentation', 'id_presentation', $id_presentation)
	, 'content');
	
	$file_sel->loadSelector();
	$file_sel->loadButton();
	
	$GLOBALS['page']->add(
		Form::closeForm()
		.'</div>'
	, 'content');
}

function delpresentation() {
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
	
	$lang	=& DoceboLanguage::createInstance('eportfolio');
	
	//load eportfolio data
	
	$id_presentation = importVar('id_presentation', true, 0);
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	
	$man_epf 		= new Man_Eportfolio();
	$epf_info 		= $man_epf->getEportfolio($id_portfolio);
	$pres 			= $man_epf->getPresentation($id_presentation);
	
	if(isset($_POST['undo'])) {
		jumpTo('index.php?modname=eportfolio&op=showpresentation&id_portfolio='.$id_portfolio);
	}
	if(isset($_POST['confirm'])) {
		
		$re = $man_epf->delPresentation( $id_presentation, getLogUserId() );
		jumpTo('index.php?modname=eportfolio&op=showpresentation&id_portfolio='.$id_portfolio.'&result=ok_del_pres');
	}
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			'index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio => $lang->def('_PRESENTATIONS_ZONE'), 
			$lang->def('_DEL_PRESENTATION').' : '.$pres[PRES_TITLE]
			
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK'))
	, 'content');
	// print page
	$GLOBALS['page']->add(
		Form::openForm('form_del_pdp', 'index.php?modname=eportfolio&amp;op=delpresentation')
		
		.Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio)
		.Form::getHidden('id_presentation', 'id_presentation', $id_presentation)
		
		.getDeleteUi($lang->def('_ARE_YOU_SURE_PRESENTATION'), 
			'<b>'.$lang->def('_PRESENTATION_TITLE').': </b> '.$pres[PRES_TITLE].'<br />'
			.'<b>'.$lang->def('_PRESENTATION_COMMENT').': </b> '.$pres[PRES_TEXTOF], 
			false,
			'confirm',
			'undo' )
		.Form::closeForm()
		.'</div>'
	, 'content');
}

function presentationpreview() {
	checkperm('view');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$id_presentation = importVar('id_presentation', true, 0);
	$man_epf 		= new Man_Eportfolio();
	$epf_info 		= $man_epf->getEportfolio($id_portfolio);
	
	$man_pres 		= new EpfShowPresentation($man_epf, $id_presentation);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			'index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio => $lang->def('_PRESENTATIONS_ZONE'), 
			$lang->def('_PRESENTATION_PREVIEW')
			
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK'))
	, 'content');
	
	$GLOBALS['page']->add(
		'<h2 class="epf_presentation_title">'
			.$man_pres->getTitle()
		.'</h2>'
		.'<p class="epf_presentation_owner_comment">'
			.$man_pres->getOwnerComment()
		.'</p>'
		.'<p class="epf_presentation_curriculum">'
			.$man_pres->getCurriculum()
		.'</p>'
		
		.'<div class="epf_presentation_pdp">'
			.$man_pres->getPdp()
		.'</div>'
		.'<div class="epf_presentation_pdp">'
			.$man_pres->getCompetence()
		.'</div>'
		.'<div class="epf_presentation_pdp">'
			.$man_pres->getAttachedFile()
		.'</div>'
	, 'content');
	
	$GLOBALS['page']->add(
		'</div>'
	, 'content');
}

function sendpresentationinvite() {
	checkperm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$id_portfolio 		= importVar('id_portfolio', true, 0);
	$man_epf 			= new Man_Eportfolio();
	$epf_info 			= $man_epf->getEportfolio($id_portfolio);
	$acl_man 			= $GLOBALS['current_user']->getAclmanager();
	
	$all_presentation 	= array();
	$re_pres = $man_epf->getQueryPresentation($id_portfolio, getLogUserId());
	while($row = mysql_fetch_row($re_pres)) {
			
		$all_presentation[$row[PRES_ID]] = $row[PRES_TITLE];
	}
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			'index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio => $lang->def('_PRESENTATIONS_ZONE'), 
			$lang->def('_PRESENTATION_INVITE')
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK'))
	, 'content');
	
	// -------------------------------------------------------------------------------------------
	if(isset($_POST['send_invite_pres'])) {
		
		// control mail is correct
		if(trim($_POST['recipient_mail'] == '')) {
			
			$GLOBALS['page']->add(getErrorUi($lang->def('_PLEASE_INSERT_VALID_MAIL')), 'content');
		} elseif (!eregi("^([a-z0-9_\-]|\\.[a-z0-9_])+@(([a-z0-9_\-]|\\.-)+\\.)+[a-z]{2,8}$", $_POST['recipient_mail'])) {
			
			$GLOBALS['page']->add(getErrorUi($lang->def('_PLEASE_INSERT_VALID_MAIL')), 'content');
		} elseif(eregi("[\r\n]+", $_POST['recipient_mail'])) {
			
			$GLOBALS['page']->add(getErrorUi($lang->def('_PLEASE_INSERT_VALID_MAIL')), 'content');
		} elseif(trim($_POST['mail_subject']) == '') {
		
			$GLOBALS['page']->add(getErrorUi($lang->def('_PLEASE_INSERT_A_SUBJECT')), 'content');
		} else {
			
			$code = chr(mt_rand(97, 122)).mt_rand(0, 9)
				.chr(mt_rand(97, 122)).mt_rand(0, 9)
				.chr(mt_rand(97, 122)).mt_rand(0, 9)
				.chr(mt_rand(97, 122)).mt_rand(0, 9)
				.chr(mt_rand(97, 122)).mt_rand(0, 9)
				.chr(mt_rand(97, 122)).mt_rand(0, 9)
				.chr(mt_rand(97, 122)).mt_rand(0, 9)
				.chr(mt_rand(97, 122)).mt_rand(0, 9);
			
			if(!$man_epf->savePresentationInvite(	$_POST['presentation'], 
													$_POST['recipient_mail'], 
													$code, 
													date('Y-m-d H:i:s') )) {
													
				$GLOBALS['page']->add(getErrorUi($lang->def('_SEND_INVITE_FAILURE')), 'content');
			} else {
				
				//create email link -----------------------------------------
				$link = $GLOBALS['lms']['url'].'index.php?modname=eportfolio&amp;type=ext&amp;op=extpresentation&amp;id_presentation='.$_POST['presentation']
						.'&amp;code='.$code.'&amp;no_redirect=1';
				
				$link_to_pres = '<br /><br />'
					.'<a href="'.$link.'">'
					.$lang->def('_GOTO_PRESENTATION')
					.'</a><br /><br />'
					.$lang->def('_MAIL_TROUBLE').'<br/>'.str_replace('&amp;', '&', $link);
				
				//compose e-mail --------------------------------------------
				$user_info = $acl_man->getUser(getLogUserId(), false);
				$mail_sender = $user_info[ACL_INFO_EMAIL];
				
				/*$from = "From: ".$mail_sender."\r\n";
				$intestazione  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=".getUnicode()."\r\n";
				$intestazione .= "Return-Path: ".$mail_sender."\r\n";
				$intestazione .= "Reply-To: ".$mail_sender."\r\n";
				$intestazione .= "X-Sender: ".$mail_sender."\r\n";
				$intestazione .= "X-Mailer: PHP/". phpversion()."\r\n";*/
				
				require_once($GLOBALS['where_framework'].'/lib/lib.mailer.php');
				$mailer = DoceboMailer::getInstance();
				
				if (!$mailer->SendMail($mail_sender, $_POST['recipient_mail'], $_POST['mail_subject'], 
					$_POST['mail_body'].$link_to_pres, false, 
					array(MAIL_REPLYTO => $mail_sender, MAIL_SENDER_ACLNAME => false))) {
				
				//if(!@mail($_POST['recipient_mail'], $_POST['mail_subject'], $_POST['mail_body'].$link_to_pres, $from.$intestazione)) {
					
					$GLOBALS['page']->add(getErrorUi($lang->def('_SEND_INVITE_FAILURE')), 'content');
				} else jumpTo('index.php?modname=eportfolio&amp;op=showpresentation&amp;id_portfolio='.$id_portfolio.'&amp;result=ok_pinvite');
			} // end else $man_epf->savePresentationInvite
		} // end else of check
	}
	
	if(isset($_POST['mail_body'])) {
		
		$mail_body = stripslashes(str_replace('\r\n', "\r\n", $_POST['mail_body']));
	} else {
		$mail_body = '';
	}
	$GLOBALS['page']->add(
		Form::openForm('send_invite', 'index.php?modname=eportfolio&amp;op=sendpresentationinvite')
			
		.Form::openElementSpace()
		.Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio)
		
		.Form::getDropdown($lang->def('_SELECT_PRESENTATION'), 
							'presentation',
							'presentation', 
							$all_presentation,
							importVar('presentation', true) )
		.Form::getTextfield($lang->def('_PRES_MAIL'), 
							'recipient_mail',
							'recipient_mail', 
							255,
							importVar('recipient_mail', false, '', true) )
		.Form::getTextfield($lang->def('_PRES_MAIL_SUBJECT'), 
							'mail_subject',
							'mail_subject', 
							255,
							importVar('mail_subject', false, '', true) )
		.Form::getSimpleTextarea(	$lang->def('_PRES_MAIL_BODY'), 
							'mail_body', 
							'mail_body', 
							$mail_body)
		.Form::closeElementSpace()
		
		.Form::openButtonSpace()
		.Form::getButton('send_invite_pres', 'send_invite_pres', $lang->def('_SEND_INVITE_BUTTON'))
		.Form::getButton('undo_invite_pres', 'undo_invite_pres', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		
		.Form::closeForm()
	, 'content');
	
	$GLOBALS['page']->add(
		'</div>'
	, 'content');
}

// Curriculum =======================================================

function updatecurriculum() {
	checkperm('view');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$man_epf 		= new Man_Eportfolio();
	
	if(isset($_POST['undo'])) jumpTo('index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio);
	if(isset($_POST['save'])) {
		
		$re = $man_epf->saveCurriculum($id_portfolio, getLogUserId(), $_FILES['curriculum_file']);
		jumpTo('index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio
			.'&amp;result='.( $re ? 'ok_curr' : 'err_curr' ));
	}
	
	
	$epf_info 		= $man_epf->getEportfolio($id_portfolio);
	
	$GLOBALS['page']->add(
		getTitleArea(array(
			'index.php?modname=eportfolio&amp;op=eportfolio&amp;s=1' => $lang->def('_TITLE_EPORTFOLIO_SELECTION'), 
			'index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio => $epf_info['title'], 
			$lang->def('_LOAD_CURRICULUM')
		), 'eportfolio')
		.'<div class="std_block">'
		.getBackUi('index.php?modname=eportfolio&amp;op=showeportfolio&amp;id_portfolio='.$id_portfolio, $lang->def('_BACK'))
	, 'content');
	
	$GLOBALS['page']->add(
		getInfoUi($lang->def('_CURRICULUM_ZONE_DESCRIPTION'))
		.Form::openForm('update_curriculum', 'index.php?modname=eportfolio&amp;op=updatecurriculum', false, false, 'multipart/form-data')
		.Form::openElementSpace()
		.Form::getHidden('id_portfolio', 'id_portfolio', $id_portfolio)
		.Form::getFilefield(	$lang->def('_CURRICULUM_FILE'), 
								'curriculum_file', 
								'curriculum_file' )
		.Form::closeElementSpace()
		.Form::openButtonSpace()
		.Form::getButton('save', 'save', $lang->def('_SAVE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
	, 'content');
	
	$GLOBALS['page']->add(
		'</div>'
	, 'content');
}

function downloadcurriculum() {

	require_once($GLOBALS['where_framework'].'/lib/lib.download.php');
	
	//find selected file
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$id_user 		= importVar('id_user', true, getLogUserId());
	$man_epf 		= new Man_Eportfolio();
	
	$curriculum = $man_epf->getCurriculum($id_portfolio, $id_user);
	
	if(!$curriculum) {
		$GLOBALS['page']->add(getErrorUi('Sorry, such file does not exist!'), 'content');
		return;
	}
	//recognize mime type
	$extens = array_pop(explode('.', $curriculum[CURRICULUM_FILE]));
	sendFile($man_epf->getCurriculumPath(), $curriculum[CURRICULUM_FILE], $extens);
}

function downloadfile() {

	require_once($GLOBALS['where_framework'].'/lib/lib.download.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.myfiles.php');
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	//find selected file
	
	$id_portfolio 	= importVar('id_portfolio', true, 0);
	$id_user 		= importVar('id_user', true, getLogUserId());
	$man_epf 		= new Man_Eportfolio();
	$files 			= new MyFile($id_user);
	
	$id_presentation 	= importVar('id_presentation', true, 0);
	$id_file 			= importVar('id_file', true, 0);
	
	$file_info = $files->getFileInfo($id_file);
	
	if(!$file_info) {
		$GLOBALS['page']->add(getErrorUi('Sorry, such file does not exist!'), 'content');
		return;
	}
	
	//recognize mime type
	$extens = array_pop(explode('.', $file_info[MYFILE_FILE_NAME]));
	sendFile($files->getFilePath(), $file_info[MYFILE_FILE_NAME], $extens);
}

// Function dispatch ================================================ //

function publicEportfolioDispatch($op) {
	
	$GLOBALS['page']->add('<link href="'.getPathTemplate().'style/style_eportfolio.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');
	
	if(isset($_POST['undo_pdp_mod'])) 			$op = 'showpdp';
	if(isset($_POST['undo_competence_mod'])) 	$op = 'showcompetences';
	if(isset($_POST['undo_compscore'])) 		$op = importVar('back_to_function', false, 'showcompetences');
	if(isset($_POST['undo_new_pres'])) 			$op = 'showpresentation';
	if(isset($_POST['undo_invite_pres'])) 		$op = 'showpresentation';
	
	switch($op) {
		
		case "eportfolio" : {
			eportfolio();
		};break;
		case "showeportfolio" : {
			showeportfolio(0);
		};break;
		
		case "showpdp" : {
			showpdp();
		};break;
		case "modpdpanswer" : {
			modpdpanswer();
		};break;
		
		case "showcompetences" : {
			showcompetences();
		};break;
		case "modcompetencescore" : {
			modcompetencescore();
		};break;
		
		case "competenceinvite" : {
			competenceinvite();
		};break;
		
		case "refusecompetenceinvite" : {
			refusecompetenceinvite();
		};break;
		
		// manage competence as normal user ------------------------------------- //
		
		case "modmyepfcompetencescore" : {
			modmyepfcompetencescore();
		};break;
		case "saveepfcompetencescore" : {
			saveepfcompetencescore();
		};break;
		
		case "listrecivedinviteforcompetence" : {
			listrecivedinviteforcompetence();
		};break;
		case "evalcompetencescore" : {
			evalcompetencescore();
		};break;
		case "showscorecompetence" : {
			showscorecompetence();
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
		
		// ------------------------------------------------------------------- //
		// presentation ------------------------------------------------------ //
		// ------------------------------------------------------------------- //
		case "showpresentation" : {
			showpresentation();
		};break;
		case "setuppresentation" : {
			setuppresentation();
		};break;
		case "presentationattach" : {
			presentationattach();
		};break;
		case "delpresentation" : {
			delpresentation();
		};break;
		case "presentationpreview" : {
			presentationpreview();
		};break;
		case "sendpresentationinvite" : {
			sendpresentationinvite();
		};break;
		
		case "sendpresentationinvite" : {
			sendpresentationinvite();
		};break;
		
		// ------------------------------------------------------------------- //
		// curriculum -------------------------------------------------------- //
		// ------------------------------------------------------------------- //
		case "updatecurriculum" : {
			updatecurriculum();
		};break;
		case "downloadcurriculum" : {
			downloadcurriculum();
		};break;
		case "downloadfile" : {
			downloadfile();
		};break;
		
		
		// ------------------------------------------------------------------- //
		// admin option ------------------------------------------------------ //
		// ------------------------------------------------------------------- //
		case "modepfuser" : {
			modepfuser();
		};break;
		
		case "epfpdp" : {
			epfpdp();
		};break;
		
		case "downpdp" : {
			
			$man_epf = new Man_Eportfolio();
			$id_pdp 	= importVar('id_pdp', true, 0);
			$data 		= $man_epf->getPdpDetails($id_pdp);
			$man_epf->movePdp('down', $id_pdp, $data[PDP_ID_PORTFOLIO]);
			jumpTo('index.php?modname=eportfolio&amp;op=epfpdp&amp;id_portfolio='.$data['id_portfolio']);
		};break;
		case "uppdp" : {
			$man_epf = new Man_Eportfolio();
			$id_pdp 	= importVar('id_pdp', true, 0);
			$data 		= $man_epf->getPdpDetails($id_pdp);
			$man_epf->movePdp('up', $id_pdp, $data[PDP_ID_PORTFOLIO]);
			jumpTo('index.php?modname=eportfolio&amp;op=epfpdp&amp;id_portfolio='.$data['id_portfolio']);
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
			jumpTo('index.php?modname=eportfolio&amp;op=epfcompetences&amp;id_portfolio='.$data['id_portfolio']);
		};break;
		case "upcompetence" : {
			$man_epf = new Man_Eportfolio();
			$id_competence 	= importVar('id_competence', true, 0);
			$data 			= $man_epf->getCompetenceDetails($id_competence);
			$man_epf->moveCompetence('up', $id_competence, $data['id_portfolio']);
			jumpTo('index.php?modname=eportfolio&amp;op=epfcompetences&amp;id_portfolio='.$data['id_portfolio']);
		};break;
		
		case "modepfcompetencescore" : {
			modepfcompetencescore();
		};break;
		case "delepfcompetences" : {
			delepfcompetences();
		};break;
		
		
	} // end switch
}

?>