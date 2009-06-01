<?php defined("IN_DOCEBO") or die("You can't access this file directly");

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2008													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if($GLOBALS['current_user']->isAnonymous()) die("You must login first.");

function tagslist() {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.tags.php');
	$lang =& DoceboLanguage::createInstance('tags', 'framework');
	
	$id_tag 	= get_req( 'id_tag', DOTY_INT, 0 );
	$tag_name 	= get_req( 'tag', DOTY_STRING, '' );
	$filter 	= get_req( 'filter', DOTY_STRING, '' );
	
	$nav_bar = new NavBar('ini', $GLOBALS['lms']['visuItem'], 0 );
	$nav_bar->setLink('index.php?modname=tags&amp;op=tags&amp;id_tag='.$id_tag);
	$ini = $nav_bar->getSelectedElement();

	$tags = new Tags('*');
	$resources = $tags->getResourceByTags($id_tag, false, false, $ini, $GLOBALS['lms']['visuItem']);

	$GLOBALS['page']->add(
		getTitleArea(array($lang->def('_TAGS')), 'tags')
		.'<div class="std_block">'
		.'<div class="tag_list">'
	, 'content');
	
	while(list(, $res) = each($resources['list'])) {
		
		$link = $res['permalink'];
		$delim = ( strpos($link, '?') === false ? '?' : '&' );
		if( strpos($link, '#') === false) {
			$link = $link . $delim . 'sop=setcourse&sop_idc='.$res['id_course'];
		} else {
			$link = str_replace('#', $delim . 'sop=setcourse&sop_idc='.$res['id_course'].'#', $link);
		}
		
		$GLOBALS['page']->add(''
			.'<h2>'
				.'<a href="'.$link.'">'.$res['title'].'</a>'
			.'</h2>'
			.'<p>'
				.$res['sample_text']
			.'</p>'
			.'<div class="tag_cloud">'
				.'<span>'.$lang->def('_TAGS').' : </span>'
				.'<ul><li>'
					.implode('</li><li>', $res['related_tags'])
				.'</li></ul>'
			.'</div>'
			.'<br />'
		, 'content');
	}
	$GLOBALS['page']->add(
		'</div>'	
		.$nav_bar->getNavBar($ini, $resources['count'])
		.'</div>'
	, 'content');
}

function tags_dispatch($op) {
	switch($op) {
		default: tagslist();
	}
}

?>