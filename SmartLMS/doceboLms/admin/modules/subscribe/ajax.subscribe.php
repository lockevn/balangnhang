<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

// here all the specific code ==========================================================

$op = importVar('op');
require_once($GLOBALS['where_framework'].'/lib/lib.permission.php');
switch($op) {

	default : {
		
		checkPerm('subscribe', false, 'course');
	
		require_once($GLOBALS['where_framework'].'/lib/lib.field.php');
		$fman 	= new FieldList();
		
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		
		$id_course 	= importVar('id_course', true, 0);
		$id_field 	= importVar('id_field', false, 0); 
		
		$values = array();
		
		switch($id_field) {
			case "name" : {
				require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');
				$acl_man = new DoceboACLManager();
				
				$users = getSubscribed($id_course);
				$allusers_info = $acl_man->getUsers($users);
				
				while(list(, $user_info) = each($allusers_info)) {
					
					$values[$user_info[ACL_INFO_IDST]] = $user_info[ACL_INFO_LASTNAME].' '.$user_info[ACL_INFO_FIRSTNAME];
				}
			};break;
			case "email" : {
				require_once($GLOBALS['where_framework'].'/lib/lib.aclmanager.php');
				$acl_man = new DoceboACLManager();
				
				$users = getSubscribed($id_course);
				$allusers_info = $acl_man->getUsers($users);
				
				while(list(, $user_info) = each($allusers_info)) {
					
					$values[$user_info[ACL_INFO_IDST]] = $user_info[ACL_INFO_EMAIL];
				}
			};break;
			default: {
			
				$users = getSubscribed($id_course);
				$values = $fman->fieldValue((int)$id_field, $users);
			}
		}
		
		if($id_field == 'name') {
			
			
		} else {
		
		}
		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');

		$json = new Services_JSON();
		$output = $json->encode($values);
  		docebo_cout($output);
	};break;
}

?>