<?php
/************************************************************************/
/* DOCEBO ECOMMERCE - Ecommerce Managment System                        */
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


require_once($GLOBALS['where_ecom'].'/admin/class.module/class.definition.php');


class EcomAdmin_Productfield extends EcomAdminModule {

	function loadBody() {

		require_once($GLOBALS['where_ecom'].'/admin/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		productfieldDispatch($GLOBALS['op']);
	}

	function getAllToken($op) {
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
