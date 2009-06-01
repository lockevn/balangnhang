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

class Module_Message extends LmsModule {
	
	function loadBody() {
		
		require_once($GLOBALS['where_lms'].'/modules/message/message.php');
		messageDispatch($GLOBALS['op']);
	}
	
	function getAllToken($op) {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif'),
			/*'send_upper' => array( 	'code' => 'send_upper',
								'name' => '_SEND_UPPER',
								'image' => 'message/send_upper.gif'), */
			'send_all' => array( 	'code' => 'send_all',
								'name' => '_SEND_ALL',
								'image' => 'message/send.gif')
		);
	}
}

?>