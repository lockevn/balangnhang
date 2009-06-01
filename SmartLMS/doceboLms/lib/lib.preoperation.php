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

/**
 * @package 	DoceboLMS
 * @category 	utilities
 * @author 		Fabio Pirovano <fabio@docebo.com>
 * @version 	$Id: lib.preoperation.php 1002 2007-03-24 11:55:51Z fabio $
 */

// here control for sql injection

//save login password from modification
if( ($ldap_used == 'on') && isset($_POST['modname']) && ($_POST['modname'] == 'login') && isset($_POST['passIns'])) { 
	$password_login = $_POST['passIns'];
}

chkInput($_GET);
chkInput($_POST);
chkInput($_COOKIE);

if(!internalFirewall()) 
	die('Your ip address is not allowed on this site !');

//require_once($GLOBALS['where_framework'].'/lib/lib.domxml.php');

if( ($ldap_used == 'on') && isset($_POST['modname']) && ($_POST['modname'] == 'login') && isset($_POST['passIns'])) { 
	$_POST['passIns'] = stripslashes($password_login);
}

$GLOBALS['modname'] = importVar('modname');
$GLOBALS['op'] 		= importVar('op');

// redirection
if( !isset($_GET['no_redirect']) && !isset($_POST['no_redirect']) ) {
	if( (!isset($GLOBALS['modname']) || ($GLOBALS['modname'] != 'login')) && $GLOBALS['current_user']->isAnonymous() ) {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');
		$pl_man =& PlatformManager::CreateInstance();
		$pl = $pl_man->getHomePlatform();
		
		if($pl != 'cms') {
			// Added by Claudio Redaelli
			$_SESSION['login_requestedURL'] = "?" . $_SERVER['QUERY_STRING'];
			
			$GLOBALS['op'] 		= 'login';
			$GLOBALS['modname'] = 'login';
			jumpTo('../index.php');
		}
	}
}

if($GLOBALS['modname'] == '' && $GLOBALS['op'] == '' && !$GLOBALS['current_user']->isAnonymous() && !isset($_SESSION['idCourse'])) {
	$_SESSION['current_main_menu'] = '1';
	$_SESSION['sel_module_id'] = '1';
	$GLOBALS['modname'] = 'course';
	$GLOBALS['op'] 		= 'mycourses';
}

if($GLOBALS['modname'] == '' && $GLOBALS['op'] == '' && !$GLOBALS['current_user']->isAnonymous() && isset($_SESSION['sel_module_id'])) {
	
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	$mod_info = getModuleFromId($_SESSION['sel_module_id']);
	
	if($mod_info != false) {
		$GLOBALS['modname'] = $mod_info[0];
		$GLOBALS['op'] 		= $mod_info[1];
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

if($GLOBALS['lms']['stop_concurrent_user'] == 'on') {

	if(!$GLOBALS['current_user']->isAnonymous() && isset($_SESSION['idCourse'])) {
		
		//two user logged at the same time
		require_once($GLOBALS['where_lms'].'/lib/lib.track_user.php');
		
		if(!TrackUser::checkSession(getLogUserId())) {
			
			TrackUser::resetUserSession(getLogUserId());
			
			$_SESSION = array();
			session_destroy();
			
			// Recreate Anonymous user
			$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('public_area');
			$GLOBALS['logout'] 	= true;
			
			die('Two user logged at the same time with the same username'
				.'<br /><a href="index.php">Back to login</a>');
		}
	}
}


//operation that is needed before loading grafiphs element, menu and so on
switch($GLOBALS['op']) {
	
	//login control
	case "confirm" : {
		if($GLOBALS['modname'] == 'login') {
			
			require_once($GLOBALS['where_framework'].'/lib/lib.usermanager.php');
			$manager = new UserManager();
			$login_data = $manager->getLoginInfo();
			$manager->saveUserLoginData();
			
			if($login_data['userid'] != '') {
				
				$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromLogin( 	$login_data['userid'], 
																					$login_data['password'], 
																					'public_area', 
																					$login_data['lang'] );
				
				if( $GLOBALS['current_user'] === FALSE ) {
					$GLOBALS['current_user'] 	=& DoceboUser::createDoceboUserFromSession('public_area');
					$GLOBALS['access_fail'] 	= true;
					$GLOBALS['op'] 				= 'login';
					
					jumpTo('../index.php?access_fail=1');
				} else {
					
					//loading related ST
					$GLOBALS['current_user']->loadUserSectionST('/lms/course/public/');
					
					if($GLOBALS['current_user']->isPasswordElapsed() > 0) {
						
						$GLOBALS['modname'] = 'profile';
						$GLOBALS['op'] 		= 'renewalpwd';
					} else {
						
						$_SESSION['current_main_menu'] = '1';
						$_SESSION['sel_module_id'] = '1';
						
						if($GLOBALS['lms']['first_catalogue'] === 'on')
						{
							$GLOBALS['modname'] = 'coursecatalogue';
							$GLOBALS['op'] 		= 'courselist';
						}
						else
						{
							$GLOBALS['modname'] = 'course';
							$GLOBALS['op'] 		= 'mycourses';
						}
					}
					
					// perform other platforms login operation
					require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');
					$pm =& PlatformManager::createInstance();
					$pm->doCommonOperations("login");
					
					if(isset($_SESSION['login_requestedURL']) 
						&& !empty($_SESSION['login_requestedURL']) 
						&& strcmp('id_course', $_SESSION['login_requestedURL'])) {
						
						$url = $_SESSION['login_requestedURL'];
						unset($_SESSION['login_requestedURL']);
						
						$str = parse_url($url);
						parse_str($str['query'], $vars );
						if(isset($vars['id_course'])) {
							
							require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
							if(logIntoCourse($vars['id_course'], false)) jumpTo($url);
						}
					}
					//goto welcome page
					
					$_SESSION['current_main_menu'] = '1';
					$_SESSION['sel_module_id'] = '1';
					
					$GLOBALS['current_user']->SaveInSession();
					
					jumpTo('index.php?modname='.$GLOBALS['modname'].'&op='.$GLOBALS['op']);
					
					// end login
				}
			} else {
				
				jumpTo('../index.php');
			}
		}
	};break;
	case "logout" : {
		require_once($GLOBALS['where_lms'].'/lib/lib.track_user.php');
		
		if(!$GLOBALS['current_user']->isAnonymous() && isset($_SESSION['idCourse'])) {
		
			TrackUser::setActionTrack(getLogUserId(), $_SESSION['idCourse'], '', '');
		}
		if(!$GLOBALS['current_user']->isAnonymous()) {
			TrackUser::logoutSessionCourseTrack();
			$_SESSION = array();
			session_destroy();
			
			// load standard language module and put it global
			$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
			$glang->setGlobal();
			
			// Recreate Anonymous user
			$GLOBALS['current_user'] =& DoceboUser::createDoceboUserFromSession('public_area');
			$GLOBALS['logout'] 	= true;
			
			require_once($GLOBALS['where_framework'].'/lib/lib.platform.php');
			$pm =& PlatformManager::createInstance();
			$pm->doCommonOperations("logout");
		}
		
		$GLOBALS['op'] 		= 'login';
		$GLOBALS['modname'] = 'login';
		
		jumpTo('../index.php?logout=1');
	};break;
	case "aula" : {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		if(!logIntoCourse($_GET['idCourse'], true)) {
			
			$_SESSION['current_main_menu'] = '1';
			$_SESSION['sel_module_id'] = '1';
			$GLOBALS['modname'] = 'course';
			$GLOBALS['op'] 		= 'mycourses';
		}
	};break;
	//registering menu information
	case "unregistercourse" : {
		
		//if a course is selected the selection is deleted
		if (isset($_SESSION['idCourse'])) {
			
			TrackUser::closeSessionCourseTrack();
			
			unset($_SESSION['idCourse']);
			unset($_SESSION['idEdition']);
		}
		if(isset($_SESSION['test_assessment'])) unset($_SESSION['test_assessment']);
		if(isset($_SESSION['direct_play'])) unset($_SESSION['direct_play']); 
		if(isset($_SESSION['cp_assessment_effect'])) unset($_SESSION['cp_assessment_effect']);
		$_SESSION['current_main_menu'] = '1';
		$_SESSION['sel_module_id'] = '1';
		$_SESSION['is_ghost'] = false;
		$GLOBALS['modname'] = 'course';
		$GLOBALS['op'] 		= 'mycourses';
	};break;
	case "selectMain" : {
		$_SESSION['current_main_menu'] = (int)$_GET['idMain'];
		$first_page = firstPage( $_SESSION['current_main_menu'] );
		
		if($first_page['modulename'] != '') 
			jumpTo( 'index.php?modname='.$first_page['modulename'].'&op='.$first_page['op'].'&sel_module='.$first_page['idModule']);
	};break;
	//change language for register user
	case "registerconfirm" : {
		setLanguage($_POST['language']);
	};break;
	case "registerme" : {
		list($language_reg) = mysql_fetch_row(mysql_query("
		SELECT language
		FROM ".$GLOBALS['prefix_lms']."_user_temp 
		WHERE random_code = '".$_GET['random_code']."'"));
		if($language_reg != '') setLanguage($language_reg);
	};break;
	
}
// special operation
$sop = importVar('sop', false, '');
if($sop) {
	if(is_array($sop)) $sop = key($sop); 
	switch($sop) {
		
		case "setcourse" : {
			$id_c = get_req('sop_idc', DOTY_INT, 0);
			
			if (isset($_SESSION['idCourse']) && $_SESSION['idCourse'] != $id_c) {
				
				TrackUser::closeSessionCourseTrack();
				unset($_SESSION['idCourse']);
				unset($_SESSION['idEdition']);
				
				require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
				logIntoCourse($id_c, false);
			} elseif(!isset($_SESSION['idCourse'])) {
				
				require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
				logIntoCourse($id_c, false);
			}
			if(isset($_SESSION['cp_assessment_effect'])) unset($_SESSION['cp_assessment_effect']);
			
		};break;
		case "resetselmodule" : {
			unset($_SESSION['sel_module_id']);
		};break;
		case "unregistercourse" : {
			if (isset($_SESSION['idCourse'])) {
				
				TrackUser::closeSessionCourseTrack();
				unset($_SESSION['idCourse']);
				unset($_SESSION['idEdition']);
			}
			if(isset($_SESSION['cp_assessment_effect'])) unset($_SESSION['cp_assessment_effect']);
		};break;
		case "changelang" : {
			setLanguage(importVar('new_lang'));
			$_SESSION['changed_lang'] = true;
		};break;
	}
}

// istance the course description class
if(isset($_SESSION['idCourse']) && !isset($GLOBALS['course_descriptor'])) {

	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	
	$GLOBALS['course_descriptor'] = new DoceboCourse($_SESSION['idCourse']);
}


?>
