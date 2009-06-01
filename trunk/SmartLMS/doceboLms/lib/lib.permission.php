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

function funAccess($functionname, $mode, $returnValue = false, $custom_mod_name = false) {
	
	return true;
	checkPerm($mode, $returnValue, $custom_mod_name);
}


function checkPerm($mode, $return_value = false, $use_mod_name = false, $is_public = false) {
	
	if($use_mod_name != false) $mod_name = $use_mod_name;
	else $mod_name = $GLOBALS['modname'];
	
	switch($mode) {
		case "OP" :
		case "view" : $suff = 'view';break;
		case "NEW" :
		case "add" : $suff = 'add';break;
		case "MOD" :
		case "mod" : $suff = 'mod';break;
		case "REM" :
		case "del" : $suff = 'del';break;
		default:  $suff = $mode;
	}
	$role = '/'.$GLOBALS['platform'].'/'
		.( isset($_SESSION['idCourse']) && $is_public == false ? 'course/private/'.$_SESSION['idCourse'].'/' : 'course/public/' )
		.$mod_name.'/'.$suff;
	
	if(!$return_value && isset($_SESSION['idCourse'])) {
		
		TrackUser::setActionTrack(getLogUserId(), $_SESSION['idCourse'], $mod_name, $suff);
	}
	
	if($GLOBALS['current_user']->matchUserRole($role)) {
		
		return true;
	} else {
		
		if($return_value) { return false; }
		else { die("You can't access".$role); }
	}
}

function checkPermForCourse($mode, $id_course, $return_value = false, $use_mod_name = false) {
	
	if($use_mod_name != false) $mod_name = $use_mod_name;
	else $mod_name = $GLOBALS['modname'];
	
	switch($mode) {
		case "OP" :
		case "view" : $suff = 'view';break;
		case "NEW" :
		case "add" : $suff = 'add';break;
		case "MOD" :
		case "mod" : $suff = 'mod';break;
		case "REM" :
		case "del" : $suff = 'del';break;
		default:  $suff = $mode;
	}
	
	$role = '/'.$GLOBALS['platform'].'/course/private/'.$id_course.'/'.$mod_name.'/'.$suff;
	
	if(!$return_value && isset($_SESSION['idCourse'])) {
		
		TrackUser::setActionTrack(getLogUserId(), $_SESSION['idCourse'], $mod_name, $suff);
	}
	
	if($GLOBALS['current_user']->matchUserRole($role)) {
		
		return true;
	} else {
		
		if($return_value) return false;
		else die("You can't access");
	}
}

function checkRole($roleid) {
	
	if($GLOBALS['current_user']->matchUserRole($roleid)) return true;
	return false;
}

?>
