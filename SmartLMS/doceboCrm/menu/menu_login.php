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

	$result = mysql_query( "
	SELECT idPages, title
	FROM ".$GLOBALS['prefix_lms']."_webpages
	WHERE publish = '1' AND in_home = '0' AND language = '".getLanguage()."'
	ORDER BY sequence ");
	$out = '<div class="login_menu_box">'."\n"
		.'<ul class="log_list">'."\n"
		.'<li><a class="voice" href="index.php">'.$lang->def('_HOMEPAGE').'</a></li>';
	while( list($idPages, $title) = mysql_fetch_row($result)) {
		$out .= '<li>'
			.'<a class="voice" href="index.php?modname=login&amp;op=readwebpages&amp;idPages='.$idPages.'">'
			.$title.'</a></li>';
	}
/*	if($GLOBALS['crm']['activeNews'] == 'on') {
		$out .= '<li><a class="voice" href="index.php?modname=login&amp;op=news">'.$lang->def('_NEWS').'</a></li>';
	} */
	$out .= '</ul>'."\n"
		.'</div>'."\n";
	//return $out;
}

// XXX: loadLogin
function loadLogin() {

	require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$user_manager = new UserManager();

	//$user_manager->setRegisterTo('link', 'index.php?modname=login&amp;op=register');
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

// XXX: loadCategory
function loadCategory() {

	$lang = DoceboLanguage::createInstance('login');

	$reCategory = mysql_query( "
	SELECT idCategory, path
	FROM ".$GLOBALS['prefix_lms']."_category
	WHERE lev = '1'
	ORDER BY path");

	$out = '<div class="login_menu_box">'."\n"
		.'<div class="log_title_cat">'.$lang->def('_CATEGORYLIST').'</div>'."\n"
		.'<ul class="log_list">'."\n";
	while( list($idC, $name_c) = mysql_fetch_row($reCategory) ) {
		$out .= '<li><a class="voice" href="index.php?modname=login&amp;op=courselist&amp;idCategory='.$idC.'">'
			.substr($name_c, 6).'</a></li>';
	}
	$out .= '</ul>'
		.'</div>'."\n";
	return $out;
}

// XXX: compose menu
$GLOBALS['page']->add(
	loadMenu()
	.loadLogin()
	/* .( $GLOBALS['crm']['course_block'] == 'on' ? loadCategory() : '') */
	, 'menu');


?>
