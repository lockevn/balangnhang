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

require_once($GLOBALS['where_lms'].'/lib/lib.levels.php');

class Module_Storage extends LmsModule {
	
	//class constructor
	function Module_Storage($module_name = '') {
		//EFFECTS: if a module_name is passed use it else use global reference
		global $modname;
		
		parent::LmsModule();
	}
	
	function loadHeader() {
		//EFFECTS: write in standard output extra header information
		global $op;
		$GLOBALS['page']->setWorkingZone( 'page_head' );
		$GLOBALS['page']->add( '<link href="'.getPathTemplate().'style/style_treeview.css" rel="stylesheet" type="text/css" />' );
		$GLOBALS['page']->add( '<link href="'.getPathTemplate().'style/style_organizations.css" rel="stylesheet" type="text/css" />' );
		$GLOBALS['page']->add( '<link href="'.getPathTemplate().'style/style_storage.css" rel="stylesheet" type="text/css" />' );
		return;
	}
	
	function useExtraMenu() {
		return false;
	}
	
	function loadExtraMenu() {
		/*$lang =& DoceboLanguage::CreateInstance('storage', 'lms');
		$line = '<div class="line"><img src="'.getPathImage();
			echo $line.'treeview/newfolder.gif" /> '.$lang->def('_NEWFOLDER').'</div>';
			echo $line.'standard/add.gif" /> '.$lang->def('_NEWOBJECT').'</div>';
			echo $line.'standard/view.gif" /> '.$lang->def('_PREVIEW').'</div>';
			echo $line.'standard/mod.gif" /> '.$lang->def('_EDITMENU').'</div>';
			echo $line.'standard/editcopy.gif" /> '.$lang->def('_COPYPASTE').'</div>';
			echo $line.'organizations/rules.gif" /> '.$lang->def('_RULES').'</div>';
			echo $line.'standard/up.gif" /> '.$lang->def('_UP').'</div>';
			echo $line.'standard/down.gif" /> '.$lang->def('_DOWN').'</div>';
			echo $line.'organizations/configure.gif" /> '.$lang->def('_PROPERTIES').'</div>';
			echo $line.'treeview/rename.gif" /> '.$lang->def('_RENAME').'</div>';		
			echo $line.'treeview/move.gif" /> '.$lang->def('_ALT_MOVE').'</div>';		
			echo $line.'standard/rem.gif" /> '.$lang->def('_DEL').'</div>';
			*/
	}
	
	function getAllToken($op = '') {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif'),
			'home' => array( 	'code' => 'home',
								'name' => '_HOME',
								'image' => 'storage/home.gif'),
			'lesson' => array( 	'code' => 'lesson',
								'name' => '_LESSON',
								'image' => 'storage/lesson.gif'),
			'public' => array( 	'code' => 'public',
								'name' => '_PUBLIC',
								'image' => 'storage/public.gif')
		);
	}
	
	function getPermissionUi( $form_name, $perm ) {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
		
		$lang =& DoceboLanguage::createInstance('manmenu');
		$lang_perm =& DoceboLanguage::createInstance('permission');
		
		$tokens = $this->getAllToken();
		$levels = CourseLevel::getLevels();
		$tb = new TypeOne(0, $lang->def('_TITLE_PERMISSION'), $lang->def('_SUMMARY_PERMISSION'));
		
		$c_head = array($lang->def('_LEVELS'));
		$t_head = array('');
		foreach($tokens as $k => $token) {
			if($token['code'] != 'view') {
				$c_head[] =  '<img src="'.getPathImage().$token['image'].'" alt="'.$lang_perm->def($token['name']).'"'
							.' title="'.$lang_perm->def($token['name']).'" />';
				$t_head[] = 'image';
			}
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
				if($token['code'] != 'view') {
					$c_body[] =  '<input class="check" type="checkbox" '
								.'id="perm_'.$lv.'_'.$token['code'].'" '
								.'name="perm['.$lv.']['.$token['code'].']" value="1"'
								.( isset($perm[$lv][$token['code']]) ? ' checked="checked"' : '' ).' />'
							.'<label class="access-only" for="perm_'.$lv.'_'.$token['code'].'">'
							.$lang_perm->def($token['name']).'</label>'."\n";
				}
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
			if($token['code'] != 'view') {
				$c_select_all[] = '<img class="handover"'
						.' onclick="checkall_fromback(\''.$form_name.'\', \'['.$token['code'].']\', true); return false;"'
						.' src="'.getPathImage().'standard/checkall.gif" alt="'.$lang->def('_CHECKALL').'" />'
					.'<img class="handover"'
						.' onclick="checkall_fromback(\''.$form_name.'\', \'['.$token['code'].']\', false); return false;"'
						.' src="'.getPathImage().'standard/uncheckall.gif" alt="'.$lang->def('_UNCHECKALL').'" />';
			}
		}
		if(count($tokens) > 1) {
			$c_select_all[] = '';
			$c_select_all[] = '';
		}
		$tb->addBody($c_select_all);
		return $tb->getTable();
	}
	
	function getSelectedPermission() {
		
		$tokens 	= $this->getAllToken();
		$levels 	= CourseLevel::getLevels();
		$perm 		= array();
		
		while(list($lv, $levelname) = each($levels)) {
			$perm[$lv] = array();
			foreach($tokens as $k => $token) {
				
				if(isset($_POST['perm'][$lv][$token['code']])) {
					$perm[$lv]['view'] = 1;
					$perm[$lv][$token['code']] = 1;
				}
			}
		}
		return $perm;
	}
}

?>
