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
 * @version  $Id: class.subscribe.php 573 2006-08-23 09:38:54Z fabio $
 * @category Course managment
 * @author	 Fabio Pirovano <fabio [at] docebo [dot] com>
 */

require_once(dirname(__FILE__).'/class.definition.php');

class Module_Public_Subscribe_Admin extends LmsModule {
	
	
	function loadBody() {
		
		require_once(dirname(__FILE__).'/../modules/'.$this->module_name.'/'.$this->module_name.'.php');
		publicSubscribeAdminDispatch($GLOBALS['op']);
	}
}

?>
