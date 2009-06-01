<?php
/*************************************************************************/
/* DOCEBO CRM - Customer Relationship Management                         */
/* =============================================                         */
/*                                                                       */
/* Copyright (c) 2006 by Giovanni Derks <virtualdarkness[AT]gmail-com>   */
/* http://www.docebo.com                                                 */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @version  $Id: lib.istance.php 113 2006-03-08 18:08:42Z ema $
 */
// ----------------------------------------------------------------------------

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
function &createCrmModule($module_name, $class_name = NULL) {

	if(file_exists($GLOBALS['where_crm'].'/class.module/class.'.$module_name.'.php')) {
		include($GLOBALS['where_crm'].'/class.module/class.'.$module_name.'.php');
		if( $class_name === NULL ) $class_name = 'Module_'.ucfirst($module_name);
	}
	else {
		require_once($GLOBALS['where_crm'].'/class.module/class.definition.php');
		$class_name = 'CrmModule';
	}
	$module_cfg = eval( "return new $class_name ();" );
	return $module_cfg;
}

?>
