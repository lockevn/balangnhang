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

function mycertificate(&$url) {
	checkPerm('view');
		
	$lang =& DoceboLanguage::createInstance('certificate', 'lms');
	
	$html = getTitleArea($lang->def('_MY_CERTIFICATE'), 'mycertificate')
		.'<div class="std_block">';
	
	$cert = new Certificate();
	
	$query =	"SELECT a.idMetaCertificate, a.idCertificate, c.name, c.description, a.on_date, a.cert_file"
				." FROM ".$GLOBALS['prefix_lms']."_certificate_meta_assign as a"
				." JOIN ".$GLOBALS['prefix_lms']."_certificate as c ON a.idCertificate = c.id_certificate"
				." WHERE a.idUser = '".getLogUserId()."'";
	
	$result = mysql_query($query);
	
	$av_meta_cert = mysql_num_rows($result);
	$cert_meta_html = '';
	
	if($av_meta_cert)
	{
		$html .=	'<div class="course_certificate_list">'
					.'<h2>'.$lang->def('_META_CERTIFICATE_TITLE').'</h2>'
					.'<p>'.$lang->def('_META_CERTIFICATE_LIST').'</p>'
					.'<div class="certificate_list">';
	}
	
	while(list($id_meta, $id_certificate, $name, $description, $date, $file) = mysql_fetch_row($result))
	{
		$cert_meta_html .=	'<h3>'.$name.'</h3>'
							.'<div class="certificate_description">'.$description.'</div>'
							.'<li><strong>'.str_replace('[on_date]', $GLOBALS['regset']->databaseToRegional($date, 'date'), $lang->def('_CERT_RELEASED')).'</strong> '
							.'<a href="'.$url->getUrl('op=release_cert&id_certificate='.$id_certificate.'&idmeta='.$id_meta).'">'
							.'<img src="'.getPathImage('lms').'certificate/certificate.gif" alt="'.$lang->def('_ALT_TAKE_A_COPY').' : '.strip_tags($name).'" />'
							.$lang->def('_TAKE_A_COPY')
							.'</a></li>';
	}
	
	if($av_meta_cert)
		$html .= $cert_meta_html.'</div></div>';
	
	$html .= '<br/><br/>';
	
	$available_cert 	= $cert->certificateForCourses(false, false);
	$released 			= $cert->certificateReleased(getLogUserId());
	
	$select_course = ""
	." SELECT c.idCourse, c.code, c.name, u.status AS user_status ";
	$from_course = " FROM ".$GLOBALS['prefix_lms']."_course AS c "
	."	JOIN ".$GLOBALS['prefix_lms']."_courseuser AS u ";
	$where_course = " "
	."	c.idCourse = u.idCourse "
	."	AND u.idUser = '".getLogUserId()."' "
	." 	AND c.course_type <> 'assessment' ";
	$order_course = " ORDER BY c.name ";
	
	$course_list = mysql_query($select_course.$from_course." WHERE ".$where_course.$order_course);
	$av_cert = 0;
	while(list($id_course, $code, $name, $user_status) = mysql_fetch_row($course_list)) {
		
		if(isset($available_cert[$id_course])) {
			
			$course_cert = 0;
			
			$cert_html = '<div class="course_certificate_list">' 
				.'<h2>'
				//.( $code != '' ? '['.$code.'] ' : '' )
				.$name.'</h2>'
				.'<p>'.$lang->def('_COURSE_CERTIFICATE_LIST').'</p>';
			while(list($id_cert, $certificate) = each($available_cert[$id_course])) {
				
				$cert_html .= '<div class="certificate_list">' 
					.'<h3>'.$certificate[CERT_NAME].'</h3>'
					.'<div class="certificate_description">'.$certificate[CERT_DESCR].'</div>';
				
				if(isset($released[$id_course][$id_cert])) {
					
					$course_cert++;
					$av_cert++;
					
					$cert_html .= '<ul class="adjac_link cert_action">';
					$cert_html .= '<li><strong>'.str_replace('[on_date]', $GLOBALS['regset']->databaseToRegional($released[$id_course][$id_cert], 'date'), $lang->def('_CERT_RELEASED')).'</strong> '
							.'<a href="'.$url->getUrl('op=release_cert&id_certificate='.$id_cert.'&id_course='.$id_course).'">'
							.'<img src="'.getPathImage('lms').'certificate/certificate.gif" alt="'.$lang->def('_ALT_TAKE_A_COPY').' : '.strip_tags($certificate[CERT_NAME]).'" />'
							.$lang->def('_TAKE_A_COPY')
							.'</a></li>';
					$cert_html .= '</ul>';
				} elseif($cert->canRelease($certificate[CERT_AV_STATUS], $user_status)) {
					
					$course_cert++;
					$av_cert++;
					
					$cert_html .= '<ul class="adjac_link cert_action">';
					$cert_html .= '<li>'
							.'<a href="'.$url->getUrl('op=preview_cert&id_certificate='.$id_cert.'&id_course='.$id_course).'">'
							.'<img src="'.getPathImage('lms').'certificate/preview.gif" alt="'.$lang->def('_PREVIEW').' : '.strip_tags($certificate[CERT_NAME]).'" />'
							.$lang->def('_PREVIEW')
							.'</a>'
							.'</li> ';
					$cert_html .= '<li>'
							.'<a href="'.$url->getUrl('op=release_cert&id_certificate='.$id_cert.'&id_course='.$id_course).'">'
							.'<img src="'.getPathImage('lms').'certificate/certificate.gif" alt="'.$lang->def('_ALT_TAKE_THE_CERTIFICATE').' : '.strip_tags($certificate[CERT_NAME]).'" />'
							.$lang->def('_TAKE_THE_CERTIFICATE')
							.'</a>'
							.'</li>';
					$cert_html .= '</ul>';
				} else {
					/*
					$html .= '<ul class="adjac_link cert_action">';
					$html .= '<li><strong>'.$lang->def('_NOT_AVAILABLE').'</strong></li>';
					$html .= '</ul>';
					*/
				}
				$cert_html .= '</div>';
			} // end while
			if($course_cert != 0) $html .= $cert_html.'</div>';
		}
		
	} // end while
	
	if($av_cert == 0 && $av_meta_cert == 0)
		$html .= '<p>'.$lang->def('_NO_CERT_AVAILABLE').'</p>';
	
	$html .= '</div>';
	
	$GLOBALS['page']->add($html, 'content');
}

function preview_cert(&$url) {
	checkPerm('view');
	
	$id_certificate = importVar('id_certificate', true, 0);
	$id_course = importVar('id_course', true, 0);
	
	$cert = new Certificate();
	$subs = $cert->getSubstitutionArray(getLogUserId(), $id_course);
	$cert->send_facsimile_certificate($id_certificate, getLogUserId(), $id_course, $subs);
}

function release_cert(&$url) {
	checkPerm('view');
	
	$id_certificate = importVar('id_certificate', true, 0);
	$id_course = importVar('id_course', true, 0);
	
	$cert = new Certificate();
	$subs = $cert->getSubstitutionArray(getLogUserId(), $id_course);
	$cert->send_certificate($id_certificate, getLogUserId(), $id_course, $subs);
}

// ================================================================================

function mycertificateDispatch($op) {
	
	require_once($GLOBALS['where_lms'].'/lib/lib.certificate.php');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
	$url =& UrlManager::getInstance('mycertificate');
	$url->setStdQuery('modname=mycertificate&op=mycertificate');
	
	switch($op) {
		case "preview_cert" : {
			preview_cert($url);
		};break;
		case "release_cert" : {
			release_cert($url);
		};break;
		
		case "mycertificate" :
		default : {
			mycertificate($url);
		}
	}
	
}

?>