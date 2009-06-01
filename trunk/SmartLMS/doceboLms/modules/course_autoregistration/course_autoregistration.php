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

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

function courseAutoregistration()
{
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$out =& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$lang =& DoceboLanguage::CreateInstance('course_autoregistration', 'lms');
	
	$form = new Form();
	
	$out->add(getTitleArea($lang->def('_AUTOREGISTRATION'))
				.'<div class="std_block">');
	
	$out->add($form->openForm('course_autoregistration', 'index.php?modname=course_autoregistration&amp;op=course_autoregistration')
			.$form->openElementSpace()
			.$form->getTextfield($lang->def('_COURSE_AUTOREGISTRATION_CODE'), 'course_autoregistration_code', 'course_autoregistration_code', '255', '')
			.$form->closeElementSpace()
			.$form->openButtonSpace()
			.$form->getButton('subscribe', 'subscribe', $lang->def('_SEND'))
			.$form->closeButtonSpace());
	
	$out->add('</div>');
}

function subscribe()
{
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$form = new Form();
	if (isset($_POST['course_autoregistration_code']))
	{
		require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
		
		$out =& $GLOBALS['page'];
		$out->setWorkingZone('content');
		
		$lang =& DoceboLanguage::CreateInstance('course_autoregistration', 'lms');
		
		$code = $_POST['course_autoregistration_code'];
		$code = strtoupper($code);
		$code = str_replace('-', '', $code);
		
		$course_registration_result = false;
		
		$man_course_user = new Man_CourseUser();
			
		$course_registration_result = $man_course_user->subscribeUserWithCode($code, getLogUserId());
		
		$out->add(getTitleArea($lang->def('_AUTOREGISTRATION'))
				.'<div class="std_block">');
		if($course_registration_result > 0) {
			
			$out->add(str_replace('[course_added]', $course_registration_result, $lang->def('_REGISTRATION_SUCCESSFUL_TO')));
			$out->add('<br/><a href="index.php?modname=course&op=mycourses">'.$lang->def('_BACK_TO_COURSE').'</a>');
		} else {
		
		
			if($course_registration_result < 0) {
			
				$out->add(getErrorUi($lang->def('_CODE_ALREDY_USED')));
			} else {
				
				$out->add(getErrorUi($lang->def('_ERROE_OR_INCORRECT_CODE')));
			}
			$out->add($form->openForm('course_autoregistration', 'index.php?modname=course_autoregistration&amp;op=course_autoregistration')
				.$form->openElementSpace()
				.$form->getTextfield($lang->def('_COURSE_AUTOREGISTRATION_CODE'), 'course_autoregistration_code', 'course_autoregistration_code', '255', '')
				.$form->closeElementSpace()
				.$form->openButtonSpace()
				.$form->getButton('subscribe', 'subscribe', $lang->def('_SEND'))
				.$form->closeButtonSpace());
		}
		
		$out->add('</div>');
	}
}

function courseAutoregistrationDispatch($op)
{
	if (isset($_POST['subscribe']))
		$op = 'subscribe';
	switch ($op)
	{
		case 'course_autoregistration':
			courseAutoregistration();
		break;
		
		case 'subscribe':
			subscribe();
		break;
		
		default:
			courseAutoregistration();
		break;
	}
}
?>
