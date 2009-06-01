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

class Module_EPortfolio extends LmsModule {
	
	function loadBody() {
		
		if(isset($_GET['type']) && ($_GET['type'] == 'ext')) {
			
			require_once($GLOBALS['where_lms'].'/modules/'.$this->module_name.'/ext.'.$this->module_name.'.php');
			extEportfolioDispatch($GLOBALS['op']);
		} else {
			
			require_once($GLOBALS['where_lms'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
			publicEportfolioDispatch($GLOBALS['op']);
		}
	}
	
	function getAllToken($op) {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif')
		);
	}
	
}

?>