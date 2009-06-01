<?php
/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2006                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @package doceboLms
 * @subpackage e-portfolio 
 * @author	 Fabio Pirovano
 * @version  $Id;$
 * @since 3.1.0
 */
	
require_once($GLOBALS['where_lms'].'/lib/lib.eportfolio.php');
	
function extpresentation() {
	
	$lang =& DoceboLanguage::createInstance('eportfolio');
	
	$security_code 		= importVar('code', false, '');
	$id_presentation 	= importVar('id_presentation', true, 0);
	
	$man_epf 		= new Man_Eportfolio();
	$man_pres 		= new EpfShowPresentation($man_epf, $id_presentation);
	
	if(!$man_epf->validateInvite($id_presentation, $security_code)) {
		
		$GLOBALS['page']->add(
			getTitleArea($lang->def('_TITLE_EPORTFOLIO_SELECTION'), 'eportfolio')
			.'<div class="std_block">'.getErrorUi($lang->def('_INVALID_INVITE')).'</div>'
		, 'content');
		return;
	}
	$GLOBALS['page']->add(
		getTitleArea($man_pres->getTitle(), 'eportfolio')
		.'<div class="std_block">'
	, 'content');
	
	$GLOBALS['page']->add(
		'<p class="epf_presentation_owner_comment">'
			.$man_pres->getOwnerComment()
		.'</p>'
		.'<p class="epf_presentation_curriculum">'
			.$man_pres->getCurriculum(true, $security_code)
		.'</p>'
		
		.'<div class="epf_presentation_pdp">'
			.$man_pres->getPdp()
		.'</div>'
		.'<div class="epf_presentation_pdp">'
			.$man_pres->getCompetence()
		.'</div>'
		.'<div class="epf_presentation_pdp">'
			.$man_pres->getAttachedFile(true, $security_code)
		.'</div>'
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
	
	$security_code 		= importVar('code', false, '');
	$id_presentation 	= importVar('id_presentation', true, 0);
	
	if(!$man_epf->validateInvite($id_presentation, $security_code)) {
		
		$GLOBALS['page']->add(
			getTitleArea($lang->def('_TITLE_EPORTFOLIO_SELECTION'), 'eportfolio')
			.'<div class="std_block">'.getErrorUi($lang->def('_INVALID_INVITE')).'</div>'
		, 'content');
		return;
	}
	
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
	
	$security_code 		= importVar('code', false, '');
	$id_presentation 	= importVar('id_presentation', true, 0);
	$id_file 			= importVar('id_file', true, 0);
	
	if(!$man_epf->validateInvite($id_presentation, $security_code)) {
		
		$GLOBALS['page']->add(
			getTitleArea($lang->def('_TITLE_EPORTFOLIO_SELECTION'), 'eportfolio')
			.'<div class="std_block">'.getErrorUi($lang->def('_INVALID_INVITE')).'</div>'
		, 'content');
		return;
	}
	
	$file_info = $files->getFileInfo($id_file);
	
	if(!$file_info) {
		$GLOBALS['page']->add(getErrorUi('Sorry, such file does not exist!'), 'content');
		return;
	}
	
	//recognize mime type
	$extens = array_pop(explode('.', $file_info[MYFILE_FILE_NAME]));
	sendFile($files->getFilePath(), $file_info[MYFILE_FILE_NAME], $extens);
}

function extEportfolioDispatch($op) {
	
	$GLOBALS['page']->add('<link href="'.getPathTemplate().'style/style_eportfolio.css" rel="stylesheet" type="text/css" />'."\n", 'page_head');
	
	switch($op) {
		case "downloadfile" : {
			downloadfile();
		};break;
		case "downloadcurriculum" : {
			downloadcurriculum();
		};break;
		case "extpresentation" :
		default: {
			extpresentation();
		}
	} // end switch
}

?>