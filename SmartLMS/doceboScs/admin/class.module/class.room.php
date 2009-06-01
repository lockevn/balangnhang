<?php

/************************************************************************/
/* DOCEBO SCS - Syncronous Collaborative System							*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @version  $Id: class.room.php 113 2006-03-08 18:08:42Z ema $
 * @category Configuration
 */

require_once(dirname(__FILE__).'/class.definition.php');

class Module_Room extends ScsAdminModule {
	
	function loadBody() {
		
		require_once(dirname(__FILE__).'/../modules/'.$this->module_name.'/'.$this->module_name.'.php');
		roomDispatch($GLOBALS['op']);
	}	
	
	// Function for permission managment
	
	function getAllToken($op) {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif'),
			'mod' => array( 	'code' => 'mod',
								'name' => '_MOD',
								'image' => 'standard/mod.gif')
		);
	}
}

?>
