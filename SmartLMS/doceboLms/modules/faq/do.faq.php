<?php

/*************************************************************************/
/* DOCEBO LMS - E-Learning System                                        */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Fabio Pirovano (gishell@tiscali.it)             */
/* http://www.spaghettilearning.com                                      */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

if(!$GLOBALS['current_user']->isAnonymous()) {

function play( $object_faq, $id_param) {
	!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.param.php');
	
	$lang =& DoceboLanguage::createInstance('faq');
	
	$idCategory = $object_faq->getId();
	$mode 		= importVar('mode', false, 'faq');
	$back_coded = htmlentities(urlencode( $object_faq->back_url ));
	$search 	= importVar('search');
	if(isset($_POST['empty'])) $search = '';
	
	$idReference = getLOParam($id_param, 'idReference');
	$link = 'index.php?modname=faq&amp;op=play&amp;idCategory='.$idCategory
		.'&amp;id_param='.$id_param.'&amp;back_url='.$back_coded;
	
	// NOTE: Track only if $idReference is present 
	if( $idReference !== FALSE ) {
		require_once($GLOBALS['where_lms'].'/class.module/track.faq.php');
		list( $exist, $idTrack) = Track_Faq::getIdTrack($idReference, getLogUserId(), $idCategory, TRUE );
		if( $exist ) {
			$ti = new Track_Faq( $idTrack );
			$ti->setDate(date('Y-m-d H:i:s'));
			$ti->status = 'completed';
			$ti->update();
		} else {
			$ti = new Track_Faq( false );
			$ti->createTrack( $idReference, $idTrack, getLogUserId(), date('Y-m-d H:i:s'), 'completed', 'faq' );
		}
	}
	
	list($title) = mysql_fetch_row(mysql_query("
	SELECT title 
	FROM ".$GLOBALS['prefix_lms']."_faq_cat 
	WHERE idCategory = '".(int)$idCategory."'"));
	
	$GLOBALS['page']->add('<div id="top" class="std_block">'
		.'<div class="colum_container">'
		.getBackUi( ereg_replace( '&', '&amp;', $object_faq->back_url ), $lang->def('_BACK'))
		.'<div class="align_center">', 'content');
	if( $mode == 'help') {
		$GLOBALS['page']->add('[ <a href="'.$link.'&amp;mode=faq">'.$lang->def('_SWITCH_TO_FAQ').'</a> | '.$lang->def('_SWITCH_TO_HELP').'</a> ]', 'content');
	}
	else {
		$GLOBALS['page']->add('[ '.$lang->def('_SWITCH_TO_FAQ').' | <a href="'.$link.'&amp;mode=help">'.$lang->def('_SWITCH_TO_HELP').'</a> ]', 'content');
	}
	$GLOBALS['page']->add('</div>'
		.'<div class="title">'.$lang->def('_TITLE').' : '.$title.'</div>'
		.'</div>'
		.'<br />', 'content');
	if( $mode == 'help' ) {
		
		$link .= '&amp;mode=help';
		$letter = importVar('letter', true, '');
		$search = urldecode(importVar('search'));
		
		// Display as help
		$textQuery = "
		SELECT keyword 
		FROM ".$GLOBALS['prefix_lms']."_faq 
		WHERE idCategory = '".importVar('idCategory', true)."'";
		if($search != '' && !isset($_POST['empty'])) {
			$textQuery .= " AND ( question LIKE '%".$search."%' OR answer LIKE '%".$search."%' ) ";
		}
		$result = mysql_query($textQuery);
		
		$GLOBALS['page']->add(Form::openForm('glossary_play', 'index.php?modname=faq&amp;op=play')
			
			.Form::getOpenFieldset($lang->def('_FILTER'))
			.Form::getHidden('idCategory', 'idCategory', $idCategory)
			.Form::getHidden('id_param', 'id_param', $id_param)
			.Form::getHidden('back_url', 'back_url', $back_coded)
			.Form::getHidden('mode', 'mode', $mode)
			
			.Form::getTextfield($lang->def('_SEARCH'), 'search', 'search', 255, 
				( $search != '' && !isset($_POST['empty']) ? $search : '' )), 'content');
		$GLOBALS['page']->add('[ ', 'content');
		//letter selection
		for($i = 97; $i < 123; $i++) {
			if($letter == $i) $GLOBALS['page']->add('<span class="text_bold">(', 'content');
			$GLOBALS['page']->add('<a href="'.$link.'&amp;letter='.$i.'">'.chr($i).'</a>', 'content');
			
			if($letter == $i) $GLOBALS['page']->add(')</span>', 'content');
			if($i < 122) $GLOBALS['page']->add('-', 'content');
		}
		$GLOBALS['page']->add('&nbsp;]&nbsp;[&nbsp;', 'content');
		for($i = 48; $i < 58; $i++) {
			if($letter == $i) $GLOBALS['page']->add('<span class="text_bold">(', 'content');
			$GLOBALS['page']->add('<a href="'.$link.'&amp;letter='.$i.'">'.chr($i).'</a>', 'content');
			
			if($letter == $i) $GLOBALS['page']->add(')</span>', 'content');
			if($i < 57) $GLOBALS['page']->add('-', 'content');
		}
		$GLOBALS['page']->add(' ] ', 'content');
		
		$GLOBALS['page']->add(Form::getBreakRow()
			.Form::openButtonSpace()
			.Form::getButton('do_search', 'do_search', $lang->def('_SEARCH'))
			.Form::getButton('empty', 'empty', $lang->def('_UNDOSEL'))
			.Form::closeButtonSpace()
			.Form::getCloseFieldset()
			.Form::closeForm(), 'content');
		
		//analyze keyword
		$keyword_help = array();
		while(list($keyword) = mysql_fetch_row($result)) {
			$keyword_split = explode(',', $keyword);
			if(is_array($keyword_split))
			while(list(, $value) = each($keyword_split)) {
				$value = trim($value);
				if($value != '') {
					if($letter == 0) {
						
						if(isset($keyword_help[$value])) ++$keyword_help[$value];
						else $keyword_help[$value] = 1;
					} elseif(substr($value, 0, 1) == chr($letter)) {
						
						if(isset($keyword_help[$value])) ++$keyword_help[$value];
						else $keyword_help[$value] = 1;
					}
				}
			}
		}
		ksort($keyword_help);
		reset($keyword_help);
		$GLOBALS['page']->add('<div class="colum_container">'
			.'<div class="colum_25">'
			.'<div class="padding_04">'
			.'<div class="boxinfo_title">'.$lang->def('_TERM').'</div>'
			.'<ul class="unformatted_list">', 'content');
		while(list($key, $value) = each($keyword_help)) {
			
			$GLOBALS['page']->add('<li><a class="href_block" href="'.$link.'&amp;letter='.$letter.'&amp;search='
				.urlencode($search).'&amp;word='.base64_encode($key).'">'
				.$key.' ('.$value.')</a></li>', 'content');
		}
		$GLOBALS['page']->add('</ul></div>'
			.'</div>'
			.'<div class="colum_75">'
			.'<div class="padding_04">', 'content');
		if( isset($_GET['word']) ) {
			$reDef = mysql_query("
			SELECT title, answer 
			FROM ".$GLOBALS['prefix_lms']."_faq 
			WHERE keyword LIKE '%".base64_decode($_GET['word'])."%' AND idCategory = '".(int)$_GET['idCategory']."'
			ORDER BY title");
			while(list($title, $answer) = mysql_fetch_row($reDef)) {
				$GLOBALS['page']->add('<div class="boxinfo_title">'.$title.'</div>'
					.'<div class="boxinfo_container">'
					.( $search == '' ? $answer :
					 eregi_replace($search, '<span class="filter_evidence">'.$search.'</span>', $answer) ).'</div><br />', 'content');
			}
		}
		$GLOBALS['page']->add('</div>'
			.'</div>'
			.'<div class="no_float"></div>'
			.'</div>', 'content');
			
		
	} else {
		
		// Display as faq
		$textQuery = "
		SELECT question, answer 
		FROM ".$GLOBALS['prefix_lms']."_faq 
		WHERE idCategory = '".(int)$idCategory."' "
		.( isset($_POST['search']) && !isset($_POST['empty']) ? 
			" AND ( question LIKE '%".$search."%' OR answer LIKE '%".$search."%' ) " : '' )
		."ORDER BY sequence";
		$result = mysql_query($textQuery);
		
		$GLOBALS['page']->add(Form::openForm('glossary_play', 'index.php?modname=faq&amp;op=play')
			
			.Form::getOpenFieldset($lang->def('_FILTER'))
			.Form::getHidden('idCategory', 'idCategory', $idCategory)
			.Form::getHidden('id_param', 'id_param', $id_param)
			.Form::getHidden('back_url', 'back_url', $back_coded)
			
			.Form::getTextfield($lang->def('_SEARCH'), 'search', 'search', 255, 
				( $search != '' && !isset($_POST['empty']) ? $search : '' ))
			
			.Form::getBreakRow()
			.Form::openButtonSpace()
			.Form::getButton('do_search', 'do_search', $lang->def('_SEARCH'))
			.Form::getButton('empty', 'empty', $lang->def('_UNDOSEL'))
			.Form::closeButtonSpace()
			.Form::getCloseFieldset()
			.Form::closeForm(), 'content');
		
		while(list($question, $answer) = mysql_fetch_row($result)) {
			$GLOBALS['page']->add('<div class="boxinfo_title">'
				.( $search == '' ? $question :
				 	eregi_replace($search, '<span class="filter_evidence_2">'.$search.'</span>', $question) )
				.'</div>'
				.'<div class="boxinfo_container">'
				.( $search == '' ? $answer :
				 	eregi_replace($search, '<span class="filter_evidence">'.$search.'</span>', $answer) ).'</div><br />', 'content');
		}
	}
	$GLOBALS['page']->add('<div class="align_center">'
		.'<a href="#top">'
			.'<img src="'.getPathImage().'standard/upcheck.gif" title="'.$lang->def('_BACKTOTOP').'" />'
			.$lang->def('_BACKTOTOP')
		.'</a>'
		.'</div>'
		.getBackUi( ereg_replace( '&', '&amp;', $object_faq->back_url ), $lang->def('_BACK'))
		.'</div>', 'content');
}

}

?>