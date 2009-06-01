<?

/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System						 */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*																		 */
/* Copyright (c) 2002 by Claudio Erba (webmaster@spaghettilearning.com)  */
/* & Fabio Pirovano (gishell@tiscali.it) http://www.spaghettilearning.com*/
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

class Module_User extends Module {
	
	
	function loadHeader() {
		//EFFECTS: write in standard output extra header information
		global $op;
		
		switch($op) {
			case "newuser" :
			case "groupsinscription" :
			case "subscribe" :
			case "modinfousergroup" : {
				echo '<link href="templates/'.getTemplate().'/style/style-calendar.css" rel="stylesheet" type="text/css" />';
					
				echo '<script type="text/javascript" src="addons/calendar/calendar.js"></script>'
					.'<script type="text/javascript" src="addons/calendar/lang/calendar-'.getCmsAdmLang().'.js"></script>'
					.'<script type="text/javascript" src="addons/calendar/calendar-setup.js"></script>';
			}
		}
		
		return;
	}
	
	function loadBody() {
		//EFFECTS: include module language and module main file	
		global $op, $modulename, $prefix;
		
		loadCmsAdmLang($this->module_name);
		
		include('admin/modules/'.$this->module_name.'/'.$this->module_name.'.php');
	}
}

$module_cfg = new Module_User();

?>