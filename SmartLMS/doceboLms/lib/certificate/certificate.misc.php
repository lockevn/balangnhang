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

class CertificateSubs_Misc extends CertificateSubstitution {

	function getSubstitutionTags() {
		
		$lang =& DoceboLanguage::createInstance('admin_certificate', 'lms');
		
		$subs = array();
		$subs['[today]'] 			= $lang->def('_COURSE_TODAY');
		$subs['[year]'] 			= $lang->def('_COURSE_YEAR');
		return $subs;
	}
	
	function getSubstitution() {
		
		$subs = array();
		
		$subs['[today]'] 	= $GLOBALS['regset']->databaseToRegional(date("Y-m-d H:i:s"), 'date');
		$subs['[year]'] 	= date("Y");
		
		return $subs;
	}
}

?>