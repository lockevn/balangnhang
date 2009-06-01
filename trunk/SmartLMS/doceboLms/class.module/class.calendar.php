<?php

/****************************************************************************/
/* DOCEBO LMS - Learning Managment System										*/
/* ========================================================================	*/
/*																				*/
/* This program is free software. You can redistribute it and/or modify  	*/
/* it under the terms of the GNU General Public License as published by  	*/
/* the Free Software Foundation; either version 2 of the License.        	*/
/****************************************************************************/

class Module_Calendar extends LmsModule {
	
	function loadBody() {
		
		require_once($GLOBALS['where_lms'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		drawCalendar();
	}
	
	function getAllToken($op) {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif'),
			'personal' => array( 'code' => 'personal',
								'name' => '_PERSONAL',
								'image' => 'standard/user.gif'),
			'mod' => array( 	'code' => 'mod',
								'name' => '_MOD',
								'image' => 'standard/mod.gif')
		);
	}
	
}

?>