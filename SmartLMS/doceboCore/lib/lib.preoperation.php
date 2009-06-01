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

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

//check input
chkInput($_GET);
chkInput($_POST);
chkInput($_COOKIE);

if(!internalFirewall())
	die('Your ip address is not allowed on this site !');

require_once($GLOBALS['where_framework'].'/lib/lib.domxml.php');

$GLOBALS['modname'] = importVar('modname');
$GLOBALS['op'] = importVar('op');

if(isset($_GET['of_platform']) || isset($_POST['of_platform'])) {
	$_SESSION['current_action_platform'] = importVar('of_platform');
}

if( !isset($_GET['no_redirect']) && !isset($_POST['no_redirect']) ) {
	if( ( ($GLOBALS['op'] == '') || ($GLOBALS['op'] == 'logout') || ($GLOBALS['op'] != 'confirm' ) ) && $GLOBALS['current_user']->isAnonymous()) {
		$GLOBALS['op'] 		= 'login';
		$GLOBALS['modname'] = 'login';
	}
}

// ip control
if(isset($_GLOBALS['framework']['session_ip_control']) && $_GLOBALS['framework']['session_ip_control'] == 'on') {

	if($GLOBALS['current_user']->isLoggedIn() && ($GLOBALS['current_user']->getLogIp() != $_SERVER['REMOTE_ADDR'])) {
		echo "logip: ".$GLOBALS['current_user']->getLogIp()."\n";
		echo "addr: ".$_SERVER['REMOTE_ADDR']."\n";
		die('Ip incoerent!');
	}
}
// NOTE: some special function
switch($GLOBALS['op']) {
	case "change_main" : {

		//change form a menu voice to another
		$_SESSION['current_admin_id_menu'] = importVar('new_main');
		$_SESSION['current_action_platform'] = importVar('of_platform');
	};break;
	case "confirm" : {

		if($GLOBALS['modname'] == 'login') {

			require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
			$manager = new UserManager();
			$login_data = $manager->getLoginInfo();
			$manager->saveUserLoginData();

			$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromLogin( 	$login_data['userid'],
																					$login_data['password'],
																					( ($GLOBALS['framework']['common_admin_session'] == 'on') ? "public_area" : "admin_area" ),
																					$login_data['lang'] );

			if( $GLOBALS['current_user'] === FALSE ) {
				$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession(( ($GLOBALS['framework']['common_admin_session'] == 'on') ? "public_area" : "admin_area" ));
				$GLOBALS['access_fail'] = true;
				$GLOBALS['op'] = 'login';
			} else {
				//echo $GLOBALS['current_user']->userid;
				$GLOBALS['modname'] = 'dashboard';
				$GLOBALS['op'] = '';

				//loading related ST
				$GLOBALS['current_user']->loadUserSectionST('/framework/admin/');
				//if($GLOBALS['where_lms'] !== false) $GLOBALS['current_user']->loadUserSectionST('/lms/admin/');
				//if($GLOBALS['where_cms'] !== false) $GLOBALS['current_user']->loadUserSectionST('/cms/admin/');
				$GLOBALS['current_user']->SaveInSession();
				$GLOBALS['just_login'] = true;

				// perform other platforms login operation
				require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');
				$pm =& PlatformManager::createInstance();
				$pm->doCommonOperations("login");

			}
		}
	};break;
	case "logout" : {

		$_SESSION = array();
		session_destroy();

 		// Recreate Anonymous user
 		$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession(( ($GLOBALS['framework']['common_admin_session'] == 'on') ? "public_area" : "admin_area" ));

		$GLOBALS['op'] 		= 'login';
		$GLOBALS['modname'] = 'login';
		$GLOBALS['logout'] 	= true;

		$pm=& PlatformManager::createInstance();
		$pm->doCommonOperations("logout");
	};break;
	case "config" : {
		if(isset($_POST['option']['defaultLanguage'])) {

			//setLanguage($_POST['option']['defaultLanguage']);
		}
		if(isset($_POST['option']['defaultTemplate'])) {

			setTemplate($_POST['option']['defaultTemplate']);
		}
		if(isset($_POST['option']['layout'])) {

			//setLayout($_POST['option']['layout']);
			$GLOBALS['layout'] = $_POST['option']['layout'];
		}
	};break;
	case "platform_sel" : {
		$_SESSION['menu_over']['p_sel'] = $_GET['pl_sel'];
		$_SESSION['menu_over']['main_sel'] = 0;
	};break;
	case "over_main_sel" : {
		$_SESSION['menu_over']['main_sel'] = $_GET['id_sel'];
	};break;
}

if(isset($_GET['close_over'])) {
	$_SESSION['menu_over']['p_sel'] = '';
	$_SESSION['menu_over']['main_sel'] = 0;
}

?>
