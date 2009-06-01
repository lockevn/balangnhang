<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system								*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

/**
 * @package  DoceboLms
 * @version  $Id: class.course.php 1003 2007-03-31 13:59:46Z fabio $
 * @category Category
 */

require_once(dirname(__FILE__).'/class.definition.php');

class Module_Course extends LmsAdminModule {
	
	function loadBody() {
		
		require_once(dirname(__FILE__).'/../modules/course/course.php');
		courseDispatch($GLOBALS['op']);
	}	
	
	// Function for permission managment
	
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
								'image' => 'standard/rem.gif'),
			'subscribe' => array( 'code' => 'subscribe',
								'name' => '_SUBSCRIBE',
								'image' => 'subscribe/add_subscribe.gif'),
			'moderate' => array( 	'code' => 'moderate',
								'name' => '_MODERATE',
								'image' => 'standard/moderate.gif'),
		);
	}
}

?>
