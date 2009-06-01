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

/**
 * @author Fabio Pirovano
 * @version $Id:$
 * @since 3.1.0
 */

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Module_MyFriends extends LmsModule {
	
	function loadBody() {
		
		require_once($GLOBALS['where_lms'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		myfriendsDispatch($GLOBALS['op']);
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