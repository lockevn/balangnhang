<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2004 by Giovanni Derks                                  */
/* http://www.docebocms.com                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

// ---------------------------------------------------------------------------
if (!defined("IN_DOCEBO")) die ("You can't access this file directly...");
// ---------------------------------------------------------------------------



function load_lostpwd() {
	$pb=$GLOBALS["pb"];

	require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');

	$lang = DoceboLanguage::createInstance('login');
	$user_manager = new UserManager();

	$GLOBALS['page']->add('<div class="std_block">', 'content');
	if($user_manager->haveToLostpwdConfirm()) {

		$GLOBALS['page']->add($user_manager->performLostpwdConfirm(), 'content');
	}
	if($user_manager->haveToLostpwdAction()) {

		$GLOBALS['page']->add($user_manager->performLostpwdAction('index.php?mn=login&amp;pi='.getPI().'&amp;op=lostpwd'), 'content');
	}
	if($user_manager->haveToLostpwdMask()) {

		$GLOBALS['page']->add($user_manager->getLostpwdMask('index.php?mn=login&amp;pi='.getPI().'&amp;op=lostpwd'), 'content');
	}
	$GLOBALS['page']->add( '</div>', 'content');
}


function load_register() {
	$pb=$GLOBALS["pb"];

	require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$user_manager = new UserManager();

	$link = 'http://'.$_SERVER['HTTP_HOST']
    		.( strlen(dirname($_SERVER['PHP_SELF'])) != 1 ? dirname($_SERVER['PHP_SELF']) : '' )
			.'/index.php?mn=login&amp;pi='.getPI().'&amp;op=register_opt';

	$GLOBALS['page']->add(
		//getTitleArea(def('_REGISTER', 'register', 'lms'), 'register')
		'<div class="std_block">'
		.Form::openForm('login_confirm_form', 'index.php?mn=login&amp;pi='.getPI().'&amp;op=register', false, false, 'multipart/form-data')
		.$user_manager->getRegister($link)
		.Form::closeForm()
		.'</div>', 'content');

}


function register_confirm() {
	require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$user_manager = new UserManager();

	$GLOBALS['page']->add(
		'<div class="std_block">'
		.$user_manager->confirmRegister()
		.'</div>', 'content');
}


function renewalpwd() {

	require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
	$user_manager = new UserManager();
	$lang 		=& DoceboLanguage::createInstance('profile', 'framework');


	if($user_manager->clickSaveElapsed()) {

		$error = $user_manager->saveElapsedPassword();
		if($error['error'] == true) {

			$GLOBALS['page']->add(
				getCmsTitleArea($lang->def('_TITLE_CHANGE'), 'profile')
				.'<div class="std_block">'
				.$error['msg']
				.$user_manager->getElapsedPassword('index.php?mn=login&amp;pi='.getPI().'&amp;op=renewalpwd')
				.'</div>', 'content');
		} else {
			jumpTo('index.php');
		}

	} else {

		$GLOBALS['page']->add(
			getCmsTitleArea($lang->def('_TITLE_CHANGE'), 'profile')
			.'<div class="std_block">'
			.$user_manager->getElapsedPassword('index.php?mn=login&amp;pi='.getPI().'&amp;op=renewalpwd')
			.'</div>', 'content');

	}
}


?>
