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

class Module_Course extends LmsModule {
	
	function beforeLoad() {
		switch($GLOBALS['op']) {
			case "mycourses" : 
			case "unregistercourse" : {
				if (isset($_SESSION['idCourse'])) {
				
					TrackUser::closeSessionCourseTrack();
					unset($_SESSION['idCourse']);
					unset($_SESSION['idEdition']);
				}
				if(isset($_SESSION['cp_assessment_effect'])) unset($_SESSION['cp_assessment_effect']);
			}
		}	
	}
	
	function loadBody() {
		
		switch($GLOBALS['op']) {
			case 'showresults': {
				$id_course = get_req('id_course', DOTY_INT, false);
				$_SESSION['idCourse'] = $id_course;
				jumpTo('index.php?modname=organization&op=showresults&idcourse='.$id_course);
			};break;
			case "mycourses" : 
			case "unregistercourse" : {
				
				require_once($GLOBALS['where_lms'].'/modules/'.$this->module_name.'/course.php');
				
				require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
				$url =& UrlManager::getInstance('course');
				$url->setStdQuery('modname=course&op=mycourses');
				
				mycourses($url);
			};break;
			case "donwloadmaterials":
				downloadMaterials();
			break;
			default: {
				
				require_once($GLOBALS['where_lms'].'/modules/'.$this->module_name.'/infocourse.php');
				infocourseDispatch($GLOBALS['op']);
			};break;
		}
	}
	
	function getAllToken($op) {
		
		switch($op) {
			case "infocourse" : {
				
				return array( 
					'view' => array( 	'code' => 'view_info',
										'name' => '_VIEW',
										'image' => 'standard/view.gif'), 
					'mod' => array( 	'code' => 'mod',
										'name' => '_MOD_FILES',
										'image' => 'standard/mod.gif')
				);
			};break;
			default : {
				
				return array( 
					'view' => array( 	'code' => 'view',
										'name' => '_VIEW',
										'image' => 'standard/view.gif')
				);
			} 
		}
	}
}

?>