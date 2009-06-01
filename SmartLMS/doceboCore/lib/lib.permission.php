<?php

/************************************************************************/
/* DOCEBO CORE - Framework                                              */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

/**
 * @package  admin-library
 * @subpackage user
 * @author 		Fabio Pirovano <fabio@docebo.com>
 * @version 	$Id:$
 */

function checkPerm($token, $return_value = false, $use_custom_name = false, $use_custom_platform = false) {
	if($use_custom_name !== false) $mod_name = $use_custom_name;
	else $mod_name = $GLOBALS['modname'];

	if($use_custom_platform !== false) $platform_name = $use_custom_platform;
	else $platform_name = $_SESSION['current_action_platform'];

	switch($token) {
		case "OP" : $suff = 'view';break;
		case "NEW" : $suff = 'add';break;
		case "MOD" : $suff = 'mod';break;
		case "REM" : $suff = 'del';break;
		default:  $suff = $token;
	}

	$role = '/'
			.( $platform_name != '' ? $platform_name.'/' 	: '' )
			.'admin/'
			.( $mod_name != '' 		? $mod_name.'/' 		: '' )
			.$suff;

	// if alredy asked
	if(isset($GLOBALS['role_asked'][$role])) {

		if($GLOBALS['role_asked'][$role]) return true;
		elseif($return_value) return false;
		else die("You can't access");
	}

	if($GLOBALS['current_user']->matchUserRole($role)) {

		$GLOBALS['role_asked'][$role] = true;
		return true;
	} else {

		$GLOBALS['role_asked'][$role] = false;
		if($return_value) return false;
		else die("You can't access");
	}
}

function checkRole($roleid) {

	if($GLOBALS['current_user']->matchUserRole($roleid)) return true;
	return false;
}

?>
