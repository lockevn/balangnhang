<?php

/************************************************************************/
/* DOCEBO CORE - Framework                                              */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2005                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

/**
 * @package admin-core
 * @subpackage bugtracker
 * @version  $Id:  $
 * @author   Giovanni Derks <giovanni[AT]docebo-com>
 */

 require_once(dirname(__FILE__).'/class.definition.php');

class Module_BugTracker extends Module {
	
	function useExtraMenu() {
		return true;
	}
	
	function loadExtraMenu() {
		loadAdminModuleLanguage($this->module_name);
	}

	function loadBody() {
		require_once($GLOBALS['where_framework'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		bugtrackerDispatch( $GLOBALS['op'] );
	}
	
	// Function for permission managment
	function getAllToken($op) {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif')
					);
		$op = $op;
	}

}

?>