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
 * @subpackage module utility
 * @category 	function for class istance
 * @author 		Fabio Pirovano <fabio@docebo.com>
 * @version 	$Id: lib.istance.php 573 2006-08-23 09:38:54Z fabio $
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
function createModule($module_name, $class_name = NULL) {
	
	if(file_exists(dirname(__FILE__).'/../class.module/class.'.$module_name.'.php')) {
		include(dirname(__FILE__).'/../class.module/class.'.$module_name.'.php');
		if( $class_name === NULL ) $class_name = 'Module_'.ucfirst($module_name); 
	}
	else {
		// include('class.module/class.standard.php');
		$class_name = 'LmsModule';
	}
	$module_cfg = eval( "return new $class_name ();" );
	return $module_cfg;
}

?>
