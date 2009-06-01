<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly!');

/**
 * @package admin-core
 * @subpackage dashboard
 */

require_once($GLOBALS['where_framework'].'/class.module/class.definition.php');

class Module_Dashboard extends Module {
	
	function loadBody() {
		require_once($GLOBALS['where_framework'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		dashboardDispatch( $GLOBALS['op'] );
	}
	
	// Function for permission managment
	function getAllToken($op) {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif')
					);
	}
	
	function getPermissionUi( $module_name, $modname, $op, $form_name, $perm, $all_perm_tokens ) {
		
		$lang =& DoceboLanguage::createInstance('manmenu');
		$lang_perm =& DoceboLanguage::createInstance('permission');
		
		$tokens = $this->getAllToken($op);
		
		$c_body = array($module_name);
		
		foreach($all_perm_tokens as $k => $token) {
			
			if(isset($tokens[$k])) {
				
				$c_body[] = '<input class="check" type="checkbox" '
								.'id="perm_'.$modname.'_'.$op.'_'.$tokens[$k]['code'].'" '
								.'name="perm['.$modname.']['.$op.']['.$tokens[$k]['code'].']" value="1" '
								.' checked="checked" disabled="disabled" />'
						
						.'<label class="access-only" for="perm_'.$modname.'_'.$op.'_'.$tokens[$k]['code'].'">'
						.$lang_perm->def($tokens[$k]['name']).'</label>'."\n";
			} else {
				$c_body[] = '';
			}
		}
		return $c_body;
	}
	
	function getSelectedPermission($source_array, $modname, $op) {
		
		$tokens 	= $this->getAllToken($op);
		$perm 		= array();
		
		foreach($tokens as $k => $token) {
				
			$perm[$token['code']] = 1;
		}
		return $perm;
	}
}

?>
