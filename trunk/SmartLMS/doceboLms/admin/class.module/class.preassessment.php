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
 * @package DoceboLms
 * @subpackage Course menu managment
 * @category 
 * @author Fabio Pirovano
 * @version $Id:$
 * @since 3.5
 * 
 * ( editor = Eclipse 3.2.0[phpeclipse,subclipse,WTP], tabwidth = 4 ) 
 */

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once(dirname(__FILE__).'/class.definition.php');

class Module_PreAssessment extends LmsAdminModule {
	
	
	function loadBody() {
		
		require_once(dirname(__FILE__).'/../modules/'.$this->module_name.'/'.$this->module_name.'.php');
		preAssessmentDispatch($GLOBALS['op']);
	}
	
	// Function for permission managment
	function getAllToken($op) {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif'), 
			'mod' => array( 	'code' => 'mod',
								'name' => '_MOD',
								'image' => 'standard/mod.gif'),
			'subscribe' => array( 'code' => 'subscribe',
								'name' => '_SUBSCRIBE',
								'image' => 'subscribe/add_subscribe.gif')
		);
	}
}

?>