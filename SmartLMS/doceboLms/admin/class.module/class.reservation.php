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
 * @package doceboLms
 * @subpackage reservation 
 * @author Marco Valloni
 */

require_once(dirname(__FILE__).'/class.definition.php');

class Module_Reservation extends LmsAdminModule {
		
	function loadBody() {
		
		require_once(dirname(__FILE__).'/../modules/reservation/reservation.php');
		reservationDispatch($GLOBALS['op']);
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
		);
	}
}

?>