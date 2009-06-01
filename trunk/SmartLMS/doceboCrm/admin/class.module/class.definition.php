<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2004													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/


/**
 * @package  DoceboCore
 * @version  $Id: class.definition.php 831 2006-11-27 21:58:49Z fabio $
 * @category Module
 */

class CrmAdmin {

	var $module_name;

	var $version;

	var $authors;

	var $mantainers;

	var $descr_short;

	var $descr_long;


	function CrmAdmin($module_name = '') {

		if( $module_name == '' ) $this->module_name = $GLOBALS['modname'];
		else $this->module_name = $module_name;

		$this->version = '1.0';

		$this->authors = array('Giovanni Derks');
		$this->mantainers = array(	'Giovanni Derks');

		$this->descr_short = 'General module : '.$this->module_name;
		$this->descr_long = 'General module : '.$this->module_name;
	}

	function getName() {

		return $this->module_name;
	}

	function getVersion() {

		return $this->version;
	}

	function getAuthors() {

		return $this->authors;
	}

	function getMantainers() {

		return $this->mantainers;
	}

	function getDescription($get_long = false) {

		return $this->descr_short;
	}

	function useStdHeader() {

		return true;
	}

	function useHeaderImage() {

		return true;
	}

	function getTitle() {

	}

	function loadHeader() {

		return;
	}

	function loadBody() {

		include(dirname(__FILE__).'/../modules/'.$this->module_name.'/'.$this->module_name.'.php');
	}

	function loadFooter() {

		return;
	}

	function getVoiceMenu() {

		return array();
	}

	function useExtraMenu() {

		return false;
	}

	function loadExtraMenu() {

		return;
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
								.'name="perm['.$modname.']['.$op.']['.$tokens[$k]['code'].']" value="1"'
								.( isset($perm[$tokens[$k]['code']]) ? ' checked="checked"' : '' ).' />'

						.'<label class="access-only" for="perm_'.$modname.'_'.$op.'_'.$tokens[$k]['code'].'">'
						.$lang_perm->def($token['name']).'</label>'."\n";
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

			if(isset($source_array['perm'][$modname][$op][$token['code']])) {

				$perm[$token['code']] = 1;
			}
		}

		return $perm;
	}
}

?>
