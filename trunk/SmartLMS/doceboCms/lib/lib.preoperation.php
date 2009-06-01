<?php

/*************************************************************************/
/* DOCEBO CMS - Content Management System                                */
/* ============================================                          */
/*                                                                       */
/* Copyright (c) 2005 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebocms.org                                              */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

// unset page var from the globals
if(isset($GLOBALS['page']) && is_string($GLOBALS['page'])) unset($GLOBALS['page']);

// here control for sql injection

//save login password from modification
if( ($ldap_used == 'on') && isset($_POST['modname']) && ($_POST['modname'] == 'login') && isset($_POST['passIns'])) {
	$password_login = $_POST['passIns'];
}

chkInput($_GET);
chkInput($_POST);
chkInput($_COOKIE);

if( ($ldap_used == 'on') && isset($_POST['modname']) && ($_POST['modname'] == 'login') && isset($_POST['passIns'])) {
	$_POST['passIns'] = stripslashes($password_login);
}


// ip control
if($GLOBALS['current_user']->isLoggedIn() && ($GLOBALS['current_user']->getLogIp() != $_SERVER['REMOTE_ADDR'])) {
	echo "logip: ".$GLOBALS['current_user']->getLogIp()."\n";
	echo "addr: ".$_SERVER['REMOTE_ADDR']."\n";
	die('Ip incoerent!');
}

if(!internalFirewall())
   die('Your ip address is not allowed on this site !');

// domxml class:
require_once($GLOBALS['where_framework'].'/lib/lib.domxml.php');



// Language:
if ((isset($_GET["newLang"])) && ($_GET["newLang"] != "") && ($_GET["special"] == "changelang")) {
	$_SESSION["custom_lang"]=$_GET["newLang"];
	setIdArea(getMainArea($_GET["newLang"]));
	//jumpTo("index.php");
}


$GLOBALS["action"]=importVar("action");


if ($GLOBALS["action"] == "login")
	cms_do_login();

if ($GLOBALS["action"] == "logout")
	cms_do_logout();


// ----------------------------------------------------------------------------------------------


function cms_do_login() {

	require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
	$manager = new UserManager();
	$login_data = $manager->getLoginInfo();
	$manager->saveUserLoginData();

	$current_lang=getLanguage();


	if($login_data['userid'] != '') {
		$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromLogin($login_data['userid'],
			$login_data['password'],
			'public_area',
			$login_data['lang'] );


		if( $GLOBALS['current_user'] === FALSE ) {
			$GLOBALS['current_user'] 	=& DoceboUser::createDoceboUserFromSession('public_area');
			$GLOBALS['access_fail'] 	= true;
			//$GLOBALS['op'] 				= 'login';
		} else {

			if ($current_lang != getLanguage())
				setIdArea(getMainArea($login_data['lang']));

			session_regenerate_id();


			//loading related ST
			$GLOBALS['current_user']->loadUserSectionST('/cms/');
			$GLOBALS['current_user']->SaveInSession();

			// Do common operations
			$pm=& PlatformManager::createInstance();
			$pm->doCommonOperations("login");

			if($GLOBALS['current_user']->isPasswordElapsed() > 0) {
				$pi =(int)$_POST["from_area"]."_".(int)$_POST["from_block"];
				jumpTo('index.php?mn=login&amp;pi='.$pi.'&amp;op=renewalpwd');
			}
			else {
				// As login succedeed, will jump to the previews page
				cmsJumpBack();
			}
		}
	}

}


function cms_do_logout() {

	$_SESSION = array();
	session_destroy();

	// Recreate Anonymous user
	$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('public_area');
	$GLOBALS['current_user']->loadUserSectionST('/cms/');

	//$GLOBALS['op'] 		= 'login';
	//$GLOBALS['modname'] = 'login';
	$GLOBALS['logout'] 	= true;

	$pm=& PlatformManager::createInstance();
	$pm->doCommonOperations("logout");
}


function cmsJumpBack() {

	if ((isset($_POST["from_area"])) && (checkRoleForItem("page", $_POST["from_area"]))) {

		$referer=(isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "");
		$site_url=$GLOBALS["cms"]["url"];

		// no phishing zone
		if ((!empty($referer)) && (strpos($referer, $site_url) !== FALSE)) {
			$back_url=str_replace($site_url, "", $_SERVER["HTTP_REFERER"]);
		}
		else {
			$back_url="index.php?special=changearea&amp;newArea=".$_POST["from_area"];
		}

		if (strpos($back_url, "logout") === FALSE)
			jumpTo($back_url);
	}
}


?>
