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

// XXX: loadMenu
function loadMenu() {
	
	$lang = DoceboLanguage::createInstance('login');
	
	$query = "
	SELECT idPages, title 
	FROM ".$GLOBALS['prefix_lms']."_webpages 
	WHERE publish = '1' AND language = '".getLanguage()."'
	";
	if($GLOBALS['lms']['home_course_catalogue'] == 'off') { 
		$query .= "  AND in_home = '0' ";
	}
	$query .= " ORDER BY sequence ";
	$result = mysql_query( $query);
	
	$out = '<div class="login_menu_box">'."\n"
		.'<ul class="log_list">'."\n"
		.'<li class="first_row"><a class="voice" href="index.php">'.$lang->def('_HOMEPAGE').'</a></li>';
	while( list($idPages, $title) = mysql_fetch_row($result)) {
		$out .= '<li>'
			.'<a class="voice" href="index.php?modname=login&amp;op=readwebpages&amp;idPages='.$idPages.'">'
			.$title.'</a></li>';
	}
	if($GLOBALS['lms']['activeNews'] == 'link') {
		$out .= '<li><a class="voice" href="index.php?modname=login&amp;op=news">'.$lang->def('_NEWS').'</a></li>';
	}
	$lang = DoceboLanguage::createInstance('course', 'lms');
	if($GLOBALS['lms']['course_block'] == 'on' && ($GLOBALS['lms']['home_course_catalogue'] == 'off')) {
		
		$out .= '<li><a class="voice" href="index.php?modname=login&amp;op=courselist">'
				.$lang->def('_COURSELIST').'</a></li>';
	}
	$out .= '</ul>'."\n"
		.'</div>'."\n";
	return $out;
}

// XXX: loadLogin
function loadLogin() {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$user_manager = new UserManager();
	
	$user_manager->setRegisterTo('link', 'index.php?modname=login&amp;op=register');
	$user_manager->setLostPwdTo('link', 'index.php?modname=login&amp;op=lostpwd');
	
	$extra = false;
	if(isset($GLOBALS['logout'])) {
		$extra = array( 'style' => 'logout_action', 'content' => def('_UNLOGGED', 'login', 'lms') );
	}
	if(isset($GLOBALS['access_fail'])) {
		$extra = array( 'style' => 'noaccess', 'content' => def('_NOACCESS', 'login', 'lms') );
	}
	return Form::openForm('login_confirm', 'index.php?modname=login&amp;op=confirm')
		.$user_manager->getLoginMask('index.php?modname=login&amp;op=login', $extra)
		.Form::closeForm();
}

function loadNewsBlock() {
		
	$lang = DoceboLanguage::createInstance('login');
	
	$textQuery = "
	SELECT idNews, publish_date, title, short_desc 
	FROM ".$GLOBALS['prefix_lms']."_news 
	WHERE language = '".getLanguage()."'
	ORDER BY important DESC, publish_date DESC";
	
	$result = mysql_query($textQuery);
	$html = '<div class="home_news_block">'
		.'<h1>'.$lang->def('_NEWS').'</h1>';
	while( list($idNews, $publish_date, $title, $short_desc) = mysql_fetch_row($result)) {
		
		$html .= '<h2>'
			.'<a href="index.php?modname=login&amp;op=readnews&amp;idNews='.$idNews.'">'.$title.'</a></h2>'
			.'<p><span class="news_data">'.$lang->def('_PUBDATE').' '.$GLOBALS['regset']->databaseToRegional($publish_date, 'date').': </span>'
			.$short_desc.'</p>';
	}
	if(mysql_num_rows($result) == 0) {
		$html .= $lang->def('_NO_NEWS_TO_LIST');
	}
	$html .= '</div>';
	return $html;
}

// XXX: compose menu
$GLOBALS['page']->add(
	loadMenu()
	.loadLogin()
	.( $GLOBALS['lms']['activeNews'] == 'block' ? loadNewsBlock() : '' )
	, 'menu');


?>
