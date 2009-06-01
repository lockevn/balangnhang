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
 * @package 
 * @subpackage 
 * @category 
 * @author Fabio Pirovano
 * @version $Id:$
 * @since 3.5
 * 
 * ( editor = Eclipse 3.2.0[phpeclipse,subclipse,WTP], tabwidth = 4 ) 
 */

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Module_Conference extends LmsModule {
	
	function loadBody() {
		
		require_once($GLOBALS['where_lms'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		dispatchConference($GLOBALS['op']);
	}
	
	function getAllToken($op) {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif'), 
			'mod' => array( 	'code' => 'mod',
								'name' => '_SCHEDULE',
								'image' => 'standard/mod.gif')
		);
	}
	
}

?>