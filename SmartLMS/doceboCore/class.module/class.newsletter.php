<?php
/*************************************************************************/
/* DOCEBO LCMS - Learning Content Managment System                       */
/* ======================================================================*/
/* Docebo is the new name of SpaghettiLearning Project                   */
/*                                                                       */
/* Copyright (c) 2005 by Emanuele Sandri (esandri@tiscali.it)            */
/*                                                                       */
/* This program is free software. You can redistribute it and/or modify  */
/* it under the terms of the GNU General Public License as published by  */
/* the Free Software Foundation; either version 2 of the License.        */
/*************************************************************************/

/**
 * @package admin-core
 * @subpackage newsletter
 */
require_once(dirname(__FILE__).'/class.definition.php');

class Module_Newsletter extends Module {

	function useExtraMenu() {
		return true;
	}

	function loadExtraMenu() {
		loadAdminModuleLanguage($this->module_name);

	}

	function loadBody() {
		global $op, $modname, $prefix;
		require_once($GLOBALS['where_framework'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
	}

}

?>
