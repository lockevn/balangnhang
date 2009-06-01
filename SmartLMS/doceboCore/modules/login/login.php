<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

function login() {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
	
	$lang 	=& DoceboLanguage::CreateInstance('login', 'framework');
	$out 	=& $GLOBALS['page'];
	
	$user_manager = new UserManager();
	
	$out->setWorkingZone('content');
	
	$extra = false;
	if(isset($GLOBALS['logout'])) {
		$extra = array( 'style' => 'logout_action', 'content' => $lang->def('_UNLOGGED') );
	}
	if(isset($GLOBALS['access_fail'])) {
		$extra = array( 'style' => 'noaccess', 'content' => $lang->def('_NOACCESS') );
	}
	
	$out->add(
		Form::openForm('admin_box_login', 'index.php?modname=login&amp;op=confirm')
		.$user_manager->getLoginMask('index.php?modname=login&amp;op=login', $extra)
		.Form::closeForm()
	);
}

function loginDispatch($op) {
switch($op) {
	case "login" : {
		login();
	};break;
}
}
?>