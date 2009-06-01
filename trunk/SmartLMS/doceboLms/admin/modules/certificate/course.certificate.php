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

require_once($GLOBALS['where_lms'].'/lib/lib.certificate.php');
	
function courseCertifications() {
	checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.sessionsave.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.certificate.php');
	
	$id_course = importVar('id_course', false, 0);
	$url = "index.php?modname=course&amp;op=upd_certifications&amp;id_course=".$id_course;
	
	$lang =& DoceboLanguage::CreateInstance('admin_course_managment', 'lms');
	$form = new Form();
	$cert = new Certificate();
	
	$all_languages 	= $GLOBALS['globLangManager']->getAllLanguages();
	$languages = array();
	foreach($all_languages as $k => $v) { $languages[$v[0]] = $v[1]; }
	
	$query_course = "SELECT code, name FROM ".$GLOBALS['prefix_lms']."_course WHERE idCourse = '".$id_course."'";
	$course = mysql_fetch_array(mysql_query($query_course));

	$title_area = 
	
	$tb	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_CERTIFICATE_TO_COURSE_CAPTION'), $lang->def('_CERTIFICATE_TO_COURSE_SUMMARY'));
	$certificate_list 	= $cert->getCertificateList();
	$course_cert 		= $cert->getCourseCertificate($id_course);
	$released 			= $cert->numOfcertificateReleasedForCourse($id_course);
	
	$possible_status = array(
		AVS_NOT_ASSIGNED 					=> $lang->def('_NOT_ASSIGNED'),
		AVS_ASSIGN_FOR_ALL_STATUS 			=> $lang->def('_ASSIGN_FOR_ALL_STATUS'),
		AVS_ASSIGN_FOR_STATUS_INCOURSE 		=> $lang->def('_ASSIGN_FOR_STATUS_INCOURSE'),
		AVS_ASSIGN_FOR_STATUS_COMPLETED 	=> $lang->def('_ASSIGN_FOR_STATUS_COMPLETED')
	);
	
	$type_h = array('nowrap', 'nowrap', '', 'image');
	$cont_h	= array(
		$lang->def('_TITLE'),
		$lang->def('_CERTIFICATE_LANGUAGE'),
		$lang->def('_CERTIFICATE_ASSIGN_STATUS'),
		$lang->def('_CERTIFICATE_RELEASED')
	);
	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
		
	while(list($id_cert, $cert) = each($certificate_list)) {
		
		$cont = array();
		$cont[] = '<label for="certificate_assign_'.$id_cert.'">'.$cert[CERT_NAME].'</label>';
		$cont[] = $languages[$cert[CERT_LANG]];
		$cont[] = $form->getInputDropdown(	'dropdown_nowh',
											'certificate_assign_'.$id_cert, 
											'certificate_assign['.$id_cert.']',
											$possible_status,
											( isset($course_cert[$id_cert]) ? $course_cert[$id_cert] : 0 ),
											'' );
		$cont[] = ( isset($released[$id_cert]) ? $released[$id_cert] : '0' );
		$tb->addBody($cont);
	}	
	// print table ===========================================================
	$GLOBALS['page']->add(
		getTitleArea(array($url => $lang->def('_MANAGE_CERTIFICATIONS'), $course['name']), 'certificate')
		.'<div class="std_block">'
		
		.$form->getHidden('id_course', 'id_course', $id_course)
		.$form->openForm("main_form", $url)
		
		.$tb->getTable()
		
		.$form->openButtonSpace()
		.$form->getButton('save', 'save', $lang->def('_SAVE'))
		.$form->getButton('course_undo', 'course_undo', $lang->def('_UNDO'))
		.$form->closeButtonSpace()
		
		.$form->closeForm()
		.'</div>'
	, 'content');
}

function updateCertifications() {
	checkPerm('mod');
	
	$id_course = importVar('id_course', false, 0);
	
	if(isset($_POST['certificate_assign'])) {
		
		require_once($GLOBALS['where_lms'].'/lib/lib.certificate.php');
		$cert = new Certificate();
		if(!$cert->updateCertificateCourseAssign($id_course, $_POST['certificate_assign'])) {
		
			jumpTo('index.php?modname=course&op=course_list&result=err');
		}
	}
	jumpTo('index.php?modname=course&op=course_list&result=ok');
}

?>