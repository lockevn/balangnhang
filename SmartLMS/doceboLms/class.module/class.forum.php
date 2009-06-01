<?php

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

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

class Module_Forum extends LmsModule {
	
	function useExtraMenu() {
		return false;
	}
	
	function loadExtraMenu() {
		$lang =& DoceboLanguage::createInstance('forum');
		$line = '<div class="legend_line">';
		echo $line.'<img src="'.getPathImage().'standard/add.gif" /> '.$lang->def('_REPLY').'</div>'
			.$line.'<img src="'.getPathImage().'standard/mod.gif" /> '.$lang->def('_MOD').'</div>'
			.$line.'<img src="'.getPathImage().'/forum/free.gif" /> '.$lang->def('_FORUMOPEN').'</div>'
			.$line.'<img src="'.getPathImage().'/forum/locked.gif" /> '.$lang->def('_FORUMCLOSED').'</div>';
		if(checkPerm('mod', true)) {
			$line.'<img src="'.getPathImage().'forum/erase.gif" /> '.$lang->def('_DELETEINSERT').'</div>';
			$line.'<img src="'.getPathImage().'forum/unerase.gif" /> '.$lang->def('_RESTOREINSERT').'</div>';
		}
		if(checkPerm('del', true)) {
			$line.'<img src="'.getPathImage().'standard/rem.gif" /> '.$lang->def('_REMFORUM').'</div>';
		}
	}
	
	function loadBody() {
		
		require_once($GLOBALS['where_lms'].'/modules/'.$this->module_name.'/'.$this->module_name.'.php');
		forumDispatch($GLOBALS['op']);
	}
	
	function getAllToken($op) {
		return array( 
			'view' => array( 	'code' => 'view',
								'name' => '_VIEW',
								'image' => 'standard/view.gif'),
			'write' => array( 	'code' => 'write',
								'name' => '_ALT_WRITE',
								'image' => 'forum/write.gif'),
			'upload' => array(	'code' => 'upload',
								'name' => '_UPPLOAD',
								'image' => 'forum/upload.gif'),
			'add' => array( 	'code' => 'add',
								'name' => '_ADD',
								'image' => 'standard/add.gif'),
			'mod' => array( 	'code' => 'mod',
								'name' => '_MOD',
								'image' => 'standard/mod.gif'),
			'del' => array( 	'code' => 'del',
								'name' => '_DEL',
								'image' => 'standard/rem.gif'),
			'moderate' => array('code' => 'moderate',
								'name' => '_MODERATE',
								'image' => 'forum/moderate.gif')/*,
			'sema' => array(	'code' => 'sema',
								'name' => '_SEMA',
								'image' => 'forum/sema.gif')*/
		);
	}
}

?>
