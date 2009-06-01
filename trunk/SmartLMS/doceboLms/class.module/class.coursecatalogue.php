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

class Module_Coursecatalogue extends LmsModule {
	
	function loadBody() {
		
		require_once($GLOBALS['where_lms'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		coursecatalogueDispatch($GLOBALS['op']);
	}
}

?>