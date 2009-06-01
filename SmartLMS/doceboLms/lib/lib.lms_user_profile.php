<?php

/************************************************************************/
/* DOCEBO LMS - Learning Managment System                               */
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2004 - 2006                                            */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once($GLOBALS['where_framework'].'/lib/lib.user_profile.php');

/**
 * @category library
 * @package user_management
 * @subpackage profile
 * 
 * This class will manage the action with performed by the profile (data access, view, etc.)
 */
class LmsUserProfile extends UserProfile {
	
	/**
	 * class constructor
	 */
	function LmsUserProfile($id_user, $edit_mode = false) {
		
		parent::UserProfile($id_user, $edit_mode);
	}
	
	// initialize functions ===========================================================
	
	/**
	 * instance the viewer class of the profile
	 */
	function initViewer($varname_action) {
		
		$this->_up_viewer = new LmsUserProfileViewer($this, $varname_action);
	}
	
}

// ========================================================================================================== //
// ========================================================================================================== //
// ========================================================================================================== //

/**
 * @category library
 * @package user_management
 * @subpackage profile
 * 
 * This class will manage the display of the data readed by the 
 */
class LmsUserProfileViewer extends UserProfileViewer {
	
	/**
	 * class constructor
	 */
	function LmsUserProfileViewer(&$user_profile, $varname_action) {
		
		parent::UserProfileViewer($user_profile, $varname_action);
	}
	
	/**
	 * print the title of the page
	 * @param mixed $text the title of the area, or the array with zone path and name
	 * @param string $image the image to load before the title
	 * 
	 * @return string the html code for space open
	 */
	function getTitleArea() {
		
		return '';
	}
	
	/**
	 * Print the head of the module space after the getTitle area
	 * @return string the html code for space open
	 */
	function getHead() {
		
		return '<div class="up_main">'."\n";
	}
	
	/**
	 * Print the footer of the module space
	 * @return string the html code for space close
	 */
	function getFooter() {
		
		return '</div>'."\n";
	}

}


?>