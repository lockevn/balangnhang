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

function play( $object_link, $id_param) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	$lang =& DoceboLanguage::createInstance('link');
	
	$idCategory = $object_link->getId();
	$mode = importVar('mode', false, 'link');
	$back_coded = htmlentities(urlencode( $object_link->back_url ));
	
	require_once( $GLOBALS['where_lms'].'/lib/lib.param.php' );
	$idReference = getLOParam($id_param, 'idReference');
	// NOTE: Track only if $idReference is present 
	if( $idReference !== FALSE ) {
		require_once( $GLOBALS['where_lms'].'/class.module/track.link.php' );
		list( $exist, $idTrack) = Track_Link::getIdTrack($idReference, getLogUserId(), $idCategory, TRUE );
		if( $exist ) {
			$ti = new Track_Link( $idTrack );
			$ti->setDate(date('Y-m-d H:i:s'));
			$ti->status = 'completed';
			$ti->update();
		} else {
			$ti = new Track_Link( false );
			$ti->createTrack( $idReference, $idTrack, getLogUserId(), date('Y-m-d H:i:s'), 'completed', 'link' );
		}
	}
	
	list($title) = mysql_fetch_row(mysql_query("
	SELECT title 
	FROM ".$GLOBALS['prefix_lms']."_link_cat 
	WHERE idCategory = '".(int)$idCategory."'"));
	
	$link = 'index.php?modname=link&amp;op=play&amp;idCategory='.$idCategory
		.'&amp;id_param='.$id_param.'&amp;back_url='.$back_coded;
	
	$GLOBALS['page']->add('<div id="top" class="std_block">'
		.'<div class="colum_container">'
		.getBackUi( ereg_replace( '&', '&amp;', $object_link->back_url ), $lang->def('_BACK'))
		.'<div class="align_center">', 'content');
	if( $mode == 'keyw' ) {
		$GLOBALS['page']->add('[ <a href="'.$link.'&amp;mode=list">'.$lang->def('_SWITCH_TO_LIST').'</a> | '.$lang->def('_SWITCH_TO_KEYWORD').' ]', 'content');
	}
	else {
		$GLOBALS['page']->add('[ '.$lang->def('_SWITCH_TO_LIST').' | <a href="'.$link.'&amp;mode=keyw">'.$lang->def('_SWITCH_TO_KEYWORD').'</a> ]', 'content');
	} 
	$GLOBALS['page']->add('</div>'
		.'<div class="title">'.$lang->def('_TITLE').' : '.$title.'</div>'
		.$lang->def('_LINKIUNNEWWINDOW')
		.'</div>'
		.'<br />', 'content');
	if( $mode == 'keyw' ) displayAsKey( $idCategory, $link.'&amp;mode=keyw' );
	else displayAsList( $idCategory );
	$GLOBALS['page']->add('<div class="align_center">'
		.'<a href="#top"><img src="'.getPathImage().'standard/upcheck.gif" title="'.$lang->def('_BACKTOTOP').'" />'.$lang->def('_BACKTOTOP').'</a>'
		.'</div>'
		.getBackUi( ereg_replace( '&', '&amp;', $object_link->back_url ), $lang->def('_BACK'))
		.'</div>', 'content');
}

function displayAsList( $idCategory ) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	$lang =& DoceboLanguage::createInstance('link');
	
	$textQuery = "
	SELECT title, link_address, description 
	FROM ".$GLOBALS['prefix_lms']."_link 
	WHERE idCategory = '".(int)$idCategory."' 
	ORDER BY sequence";
	$result = mysql_query($textQuery);
	
	while(list($title, $link_a, $description) = mysql_fetch_row($result)) {
		$GLOBALS['page']->add('<div class="padding_04">'
			.'<div class="boxinfo_title">'.$title.'</div>'
			.'<div class="boxinfo_container">'
			.'<div class="text_indent"><a href="'.$link_a.'" onclick="window.open(\''.$link_a.'\'); return false;">'.$link_a.'</a></div><br />'.$description
			.'</div>'
			.'</div><br />', 'content');
	}
}

function displayAsKey( $idCategory, $link ) {
	if(!checkPerm('view', true, 'organization') && !checkPerm('view', true, 'storage')) die("You can't access");
	$lang =& DoceboLanguage::createInstance('link');
	
	$textQuery = "
	SELECT keyword 
	FROM ".$GLOBALS['prefix_lms']."_link 
	WHERE idCategory = '".(int)$_GET['idCategory']."'";
	$result = mysql_query($textQuery);
	
	//analyze keyword
	$keyword_help = array();
	while(list($keyword) = mysql_fetch_row($result)) {
		$keyword_split = explode(',', $keyword);
		if(is_array($keyword_split))
		while(list(, $value) = each($keyword_split)) {
			$value = trim($value);
			if($value != '') {
				if(isset($keyword_help[$value])) ++$keyword_help[$value];
				else $keyword_help[$value] = 1;
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
		$GLOBALS['page']->add('<li><a class="href_block" href="'.$link.'&amp;word='.base64_encode($key).'">'
			.$key.' ('.$value.')</a></li>', 'content'); 
	}
	$GLOBALS['page']->add('</ul></div>'
		.'</div>'
		.'<div class="colum_75">', 'content');
	$GLOBALS['page']->add('<div class="padding_04">', 'content');
	if( isset($_GET['word']) ) {
		$reDef = mysql_query("
		SELECT title, link_address, description 
		FROM ".$GLOBALS['prefix_lms']."_link 
		WHERE keyword LIKE '%".base64_decode($_GET['word'])."%' AND idCategory = '".(int)$_GET['idCategory']."'
		ORDER BY title");
		while(list($title, $link_a, $description) = mysql_fetch_row($reDef)) {
			$GLOBALS['page']->add('<div class="boxinfo_title">'.$title.'</div>'
				.'<div class="boxinfo_container">'
				.'<div class="text_indent"><a href="" onclick="window.open(\''.$link_a.'\'); void 0;">'.$link_a.'</a></div><br />'.$description
				.'</div><br />', 'content');
		}
	}
	else $GLOBALS['page']->add($lang->def('_SESLECTTERM'), 'content');
	$GLOBALS['page']->add('</div>', 'content');
	$GLOBALS['page']->add('</div>'
		.'</div>'
		.'<div class="no_float"></div><br />', 'content');
}

}

?>