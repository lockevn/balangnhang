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
 * @package admin-core
 * @subpackage field
 */
 
require_once(dirname(__FILE__).'/class.definition.php');

class Module_Field_Manager extends Module {
	
	function loadBody() {
		
		require_once($GLOBALS['where_framework'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		
	}
	
	function getAllToken() {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif'),
			
			'add' => array( 	'code' => 'add',
								'name' => '_ADD',
								'image' => 'standard/add.gif'),
			
			'mod' => array( 	'code' => 'mod',
								'name' => '_MOD',
								'image' => 'standard/mod.gif'),
			
			'del' => array( 	'code' => 'del',
								'name' => '_DEL',
								'image' => 'standard/rem.gif')
		);
	}
}

?>
