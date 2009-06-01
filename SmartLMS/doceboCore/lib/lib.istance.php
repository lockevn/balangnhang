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

/**
 * @package admin-library
 * @subpackage module
 * @author 		Fabio Pirovano <fabio@docebo.com>
 * @version 	$Id: lib.istance.php 831 2006-11-27 21:58:49Z fabio $
 */

/**
 * create a istance of a specified class of a module
 * automaticaly include the file that contains the class of the module
 *
 * @param string	$module_name 	the name og the module to istance
 * @param string 	$class_name 	the name of the class relative to the module, if not passed is
 *									extracted from the $module_name
 *
 * @return mixed 	the class istance
 */
function &createModule($module_name, $class_name = NULL) {

	if(!isset($_SESSION['current_action_platform']))
		$_SESSION['current_action_platform'] = 'framework';
	
	switch($_SESSION['current_action_platform']) {
		case "framework" : {
			if($GLOBALS['where_framework']) $where = $GLOBALS['where_framework'];
			$def_class_name = 'Module';
		};break;
		case "lms" : {
			if($GLOBALS['where_lms']) $where = $GLOBALS['where_lms'].'/admin';
			$def_class_name = 'Module';
		};break;
		case "cms" : {
			if($GLOBALS['where_cms']) $where = $GLOBALS['where_cms'].'/admin';
			$def_class_name = 'Module';
		};break;
		case "scs" : {
			if($GLOBALS['where_scs']) $where = $GLOBALS['where_scs'].'/admin';
			$def_class_name = 'Module';
		};break;
		case "kms" : {
			if($GLOBALS['where_kms']) $where = $GLOBALS['where_kms'].'/admin';
			$def_class_name = 'Module';
		};break;
		case "crm" : {
			if($GLOBALS['where_crm']) $where = $GLOBALS['where_crm'].'/admin';
			$def_class_name = 'Module';
		};break;
		case "ecom" : {
			if($GLOBALS['where_ecom']) $where = $GLOBALS['where_ecom'].'/admin';
			$def_class_name = 'EcomAdmin';
		};break;
	}
	
	if(file_exists($where.'/class.module/class.'.$module_name.'.php')) {

		require_once($where.'/class.module/class.'.$module_name.'.php');
		if( $class_name === NULL ) $class_name = $def_class_name.'_'.ucfirst($module_name);
	}
	elseif(file_exists($GLOBALS['where_framework'].'/class.module/class.'.$module_name.'.php')) {

		require_once($GLOBALS['where_framework'].'/class.module/class.'.$module_name.'.php');
		if( $class_name === NULL ) $class_name = $def_class_name.'_'.ucfirst($module_name);
	} else {

		require_once($where.'/class.module/class.definition.php');
		$class_name = $def_class_name;
	}
	
	$module_cfg = new $class_name();
	
	return $module_cfg;
}


function &createLmsModule($file_name, $class_name) {

	$file_path = $GLOBALS['where_lms'].'/class.module/'.$file_name;

	if(file_exists($file_path) && !is_dir($file_path)) {

		require_once($GLOBALS['where_lms'].'/class.module/class.definition.php');
		require_once($file_path);
	} else {

		require_once($GLOBALS['where_lms'].'/class.module/class.definition.php');
		$class_name = 'LmsModule';
	}
	$module_cfg = eval( "return new $class_name();" );
	return $module_cfg;
}

function &createKmsModule($file_name) {

	$file_path = $GLOBALS['where_kms'].'/class.module/'.$file_name;

	if(file_exists($file_path) && !is_dir($file_path)) {

		require_once($GLOBALS['where_kms'].'/class.module/class.definition.php');
		require_once($file_path);
	} else {

		require_once($GLOBALS['where_kms'].'/class.module/class.definition.php');
		$class_name = 'KmsModule';
	}
	$module_cfg = eval( "return new $class_name();" );
	return $module_cfg;
}

?>
