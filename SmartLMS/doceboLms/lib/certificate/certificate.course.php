<?php

/************************************************************************/
/* DOCEBO - Learning Managment System                               	*/
/* ============================================                         */
/*                                                                      */
/* Copyright (c) 2007                                                   */
/* http://www.docebo.com                                                */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

require_once(dirname(__FILE__).'/certificate.base.php');

class CertificateSubs_Course extends CertificateSubstitution {
	
	function getSubstitutionTags() {
		
		$lang =& DoceboLanguage::createInstance('admin_certificate', 'lms');
		
		$subs = array();
		
		if($_GET['modname'] == 'meta_certificate')
		{
			
		}
		else
		{
			$subs['[course_code]'] 			= $lang->def('_CODE');
			$subs['[course_name]'] 			= $lang->def('_COURSE_NAME');
			$subs['[course_description]'] 	= $lang->def('_DESCRIPTION');
			$subs['[date_begin]'] 			= $lang->def('_COURSE_BEGIN');
			$subs['[date_end]'] 			= $lang->def('_COURSE_END');
		}
		
		return $subs;
	}
	
	/**
	 * return the list of substitution
	 */
	function getSubstitution() {
		
		$subs = array();
		
		if($_GET['modname'] == 'meta_certificate')
		{
			
		}
		else
		{
			require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
			
			$man_course = new DoceboCourse($this->id_course);
			
			$subs['[course_code]'] 			= $man_course->getValue('code');
			$subs['[course_name]'] 			= $man_course->getValue('name');
			$subs['[course_description]'] 	= $man_course->getValue('description');
			$subs['[date_begin]'] 			= $GLOBALS['regset']->databaseToRegional($man_course->getValue('date_begin'), 'date');
			$subs['[date_end]'] 			= $GLOBALS['regset']->databaseToRegional($man_course->getValue('date_end'), 'date');
		}
		return $subs;
	}
}

?>