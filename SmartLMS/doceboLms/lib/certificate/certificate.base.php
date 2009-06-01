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

class CertificateSubstitution {

	var $id_user;
	
	var $id_course;

	function CertificateSubstitution($id_user, $id_course) {
		
		$this->id_user = $id_user;
		$this->id_course = $id_course;
	}
	
	function getSubstitution() {
		
		return array();
	}
	
	function getSubstitutionTags() {
		
		return array();
	}
	
}

?>