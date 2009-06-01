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

require_once($GLOBALS['where_lms'].'/lib/lib.levels.php');

class LmsModule {
	
	//module name
	var $module_name;
	//module version
	var $version;
	//module authors
	var $authors;
	//module mantainers
	var $mantainers;
	//module short description
	var $descr_short;
	//module long description
	var $descr_long;
	
	//class constructor
	function LmsModule($module_name = '') {
		//EFFECTS: if a module_name is passed use it else use global reference
		global $modname;
		
		if( $module_name == '' ) $this->module_name = $modname;
		else $this->module_name = $module_name;
		
		$this->version = '1.0';
		
		$this->authors = array('Pirovano Fabio (gishell@tiscali.it)', 'Sandri Emanuele (emanuele@sandri.it)');
		$this->mantainers = array('Pirovano Fabio (gishell@tiscali.it)', 'Sandri Emanuele (emanuele@sandri.it)');
		
		$this->descr_short = 'General module '.$modname;
		$this->descr_long = 'General module '.$modname ;
	}
	
	function getName() {
		//EFFECTS: return the name of the module
		return $this->module_name;
	}
	
	function getVersion() {
		//EFFECTS: return the module version
		return $this->version;
	}
	
	function getAuthors() {
		//EFFECTS: return an array with the authors info
		return $this->authors;
	}
	
	function getMantainers() {
		//EFFECTS: return an array with the mantainers info
		return $this->mantainers;
	}
	
	function getDescription($get_long = false) {
		//EFFECTS: if $getLong == true return long description else return short description
		if($get_long) return $this->descr_long;
		return $this->descr_short; 
	}
	
	function beforeLoad() {
		return;
	}
	
	function useStdHeader() {
		//EFFECTS: if return false the file header.php will be not included 
		return true;
	}
	
	function hideLateralMenu() {
		return false;
	}
	
	function useHeaderImage() {
		//EFFECTS: if return false the header images will not be loaded 
		return true;
	}
	
	function getTitle() {
		//EFFECTS: return a string with the title for the current page
		return $GLOBALS['title_page'].' - '.$this->module_name;
	}
	
	function loadHeader() {
		//EFFECTS: write in standard output extra header information
		return;
	}
	
	function loadBody() {
		//EFFECTS: include module language and module main file
		
		include($GLOBALS['where_lms'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
	}
	
	function loadFooter() {
		//EFFECTS: write in standard output extra footer information
		return;
	}
	
	function getVoiceMenu() {
		//EFFECTS : return an array with extra menu voice for this module 
		//			or an empty array(display only if this is the active module) 
		
		return array();
	}
	
	function useExtraMenu() {
		//EFFECTS: return true if this module need an extra menu
		return false;
	}
	
	function legendLine($image, $name, $alt = false) {
		
		if($alt === false) $alt = strip_tags($name);
		return '<div class="legend_line">'
			.'<img src="'.getPathImage().$image.'" alt="'.$alt.'" />'
			.$name
			.'</div>'."\n";
	}
	
	function loadExtraMenu() {
		//REQUIRES: that this function is called in a div block
		//EFFECTS : write in standard output an extra menu
		return;
	}
	
	function getAllToken($op) {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif')
		);
	}
	
	function getPermissionUi( $form_name, $perm, $module_op ) {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
		
		$lang =& DoceboLanguage::createInstance('manmenu', 'framework');
		$lang_perm =& DoceboLanguage::createInstance('permission', 'framework');
		
		$tokens = $this->getAllToken($module_op);
		$levels = CourseLevel::getLevels();
		$tb = new TypeOne(0, $lang->def('_TITLE_PERMISSION'), $lang->def('_SUMMARY_PERMISSION'));
		
		$c_head = array($lang->def('_LEVELS'));
		$t_head = array('');
		foreach($tokens as $k => $token) {
			$c_head[] =  '<img src="'.getPathImage().$token['image'].'" alt="'.$lang_perm->def($token['name']).'"'
						.' title="'.$lang_perm->def($token['name']).'" />';
			$t_head[] = 'image';
		}
		if(count($tokens) > 1) {
			$c_head[] = '<img src="'.getPathImage().'standard/checkall.gif" alt="'.$lang->def('_CHECKALL').'" />';
			$c_head[] = '<img src="'.getPathImage().'standard/uncheckall.gif" alt="'.$lang->def('_UNCHECKALL').'" />';
			$t_head[] = 'image';
			$t_head[] = 'image';
		}
		$tb->setColsStyle($t_head);
		$tb->addHead($c_head);
		loadJsLibraries();
		while(list($lv, $levelname) = each($levels)) {
			
			$c_body = array($levelname);
			
			foreach($tokens as $k => $token) {
				$c_body[] =  '<input class="check" type="checkbox" '
							.'id="perm_'.$lv.'_'.$token['code'].'" '
							.'name="perm['.$lv.']['.$token['code'].']" value="1"'
							.( isset($perm[$lv][$token['code']]) ? ' checked="checked"' : '' ).' />'
						.'<label class="access-only" for="perm_'.$lv.'_'.$token['code'].'">'
						.$lang_perm->def($token['name']).'</label>'."\n";
			}
			if(count($tokens) > 1) {
				
				$c_body[] = '<img class="handover"'
					.' onclick="checkall(\''.$form_name.'\', \'perm['.$lv.']\', true); return false;"'
					.' src="'.getPathImage().'standard/checkall.gif" alt="'.$lang->def('_CHECKALL').'" />';
				$c_body[] = '<img class="handover"'
					.' onclick="checkall(\''.$form_name.'\', \'perm['.$lv.']\', false); return false;"'
					.' src="'.getPathImage().'standard/uncheckall.gif" alt="'.$lang->def('_UNCHECKALL').'" />';
			}
			$tb->addBody($c_body);
		}
		$c_select_all = array(''); 
		foreach($tokens as $k => $token) {
			
			$c_select_all[] = '<img class="handover"'
					.' onclick="checkall_fromback(\''.$form_name.'\', \'['.$token['code'].']\', true); return false;"'
					.' src="'.getPathImage().'standard/checkall.gif" alt="'.$lang->def('_CHECKALL').'" />'
				.'<img class="handover"'
					.' onclick="checkall_fromback(\''.$form_name.'\', \'['.$token['code'].']\', false); return false;"'
					.' src="'.getPathImage().'standard/uncheckall.gif" alt="'.$lang->def('_UNCHECKALL').'" />';
		}
		if(count($tokens) > 1) {
			$c_select_all[] = '';
			$c_select_all[] = '';
		}
		$tb->addBody($c_select_all);
		return $tb->getTable();
	}
	
	function getSelectedPermission($module_op) {
		
		$tokens 	= $this->getAllToken($module_op);
		$levels 	= CourseLevel::getLevels();
		$perm 		= array();
		
		while(list($lv, $levelname) = each($levels)) {
			$perm[$lv] = array();
			foreach($tokens as $k => $token) {
				
				if(isset($_POST['perm'][$lv][$token['code']])) {
					$perm[$lv][$token['code']] = 1;
				}
			}
		}
		return $perm;
	}
}

?>