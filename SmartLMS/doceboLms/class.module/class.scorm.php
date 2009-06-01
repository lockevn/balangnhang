<?php

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System                       */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*                                                                       */
/* Copyright (c) 2002 by Emanuele Sandri (esandri@tiscali.it)            */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Module_Scorm extends LmsModule {

	//class constructor
	function Module_Scorm($module_name = '') {
		//EFFECTS: if a module_name is passed use it else use global reference
		global $modname;
		
		parent::LmsModule();
	}
	
	function loadHeader() {
		//EFFECTS: write in standard output extra header information
		global $op;
		//echo '<link href="'.getPathTemplate().'style/style_treeview.css" rel="stylesheet" type="text/css" />'."\n";
		switch($op) {
			case "category" : {
				//echo '<link href="'.getPathTemplate().'style/style_treeview.css" rel="stylesheet" type="text/css" />'."\n";
			};break;
		}
		return;
	}
	function loadBody() {
		//EFFECTS: include module language and module main file

		//if( version_compare(phpversion(), "5.0.0") == -1 )
			include($GLOBALS['where_lms'].'/modules/scorm/'.$this->module_name.'.php');
		//else
		//	include($GLOBALS['where_lms'].'/modules/scorm5/'.$this->module_name.'.php');
	}
}



?>
