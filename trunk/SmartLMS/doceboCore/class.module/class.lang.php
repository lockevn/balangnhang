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
 * @subpackage language
 */

require_once(dirname(__FILE__).'/class.definition.php');

class Module_Lang extends Module {
	
	function useExtraMenu() {
		return true;
	}
	
	function loadExtraMenu() {
		loadAdminModuleLanguage($this->module_name);
		
	}

	function loadBody() {
		global $op, $modname, $prefix;
		require_once($GLOBALS['where_framework'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		langDispatch( $op );
	}
	
	// Function for permission managment
	function getAllToken($op) {
		
		switch($op) {
			case "lang" : {
				return array( 
					'view' => array( 	'code' => 'view',
										'name' => '_VIEW',
										'image' => 'standard/view.gif')
				);
			};break;
			case "importexport" : {
				return array( 
					'view' => array( 	'code' => 'view',
										'name' => '_VIEW',
										'image' => 'standard/view.gif')
				);
			};break;
		}
	}
}

?>
