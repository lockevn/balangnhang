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

class CourseLevel {

	function getLevels() {

		$lang =& DoceboLanguage::createInstance('levels', 'lms');
		return array(
			7 => $lang->def('_LEVEL_7'),		//'Admin'
			6 => $lang->def('_LEVEL_6'),		//'Prof'
			5 => $lang->def('_LEVEL_5'),		//'Mentor'
			4 => $lang->def('_LEVEL_4'),		//'Tutor'
			3 => $lang->def('_LEVEL_3'),		//'Studente'
			2 => $lang->def('_LEVEL_2'),		//'Ghost' (no track)
			1 => $lang->def('_LEVEL_1'),		//'Guest'
		);
	}


	function isTeacher($level) {
		$res=((int)$level === 6 ? TRUE : FALSE);

		return $res;
	}

}

?>