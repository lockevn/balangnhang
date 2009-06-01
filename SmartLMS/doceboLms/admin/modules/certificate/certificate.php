<?php

/************************************************************************/
/* DOCEBO LMS - Learning managment system								*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');

define('IS_META', 0);

/**
 * @package  DoceboLms
 * @version  $Id: certificate.php,v 1
 * @author	 Claudio Demarinis <claudiodema [at] docebo [dot] com>
 */

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

function certificate() {
	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');


	$mod_perm	= checkPerm('mod', true);
	// create a language istance for module admin_certificate
	$lang 		=& DoceboLanguage::createInstance('admin_certificate', 'lms');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$tb	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_CERTIFICATE_CAPTION'), $lang->def('_CERTIFICATE_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink("index.php?modname=certificate&amp;op=certificate");
	$ini=$tb->getSelectedElement();
	
	$form = new Form();
	
	if (isset($_POST['toggle_filter']))
	{
		unset($_POST['name_filter']);
		unset($_POST['code_filter']);
	}
	
	//search query of certificates
	$query_certificate = "
	SELECT id_certificate, code, name, description
	FROM ".$GLOBALS['prefix_lms']."_certificate"
	." WHERE meta = 0";
	if (isset($_POST['filter']))
	{
		if ($_POST['name_filter'] !== '' && $_POST['code_filter'] !== '')
			$query_certificate .= " AND name LIKE '%".$_POST['name_filter']."%'" .
									" AND code LIKE '%".$_POST['code_filter']."%'";
		elseif ($_POST['name_filter'] !== '')
			$query_certificate .= " AND name LIKE '%".$_POST['name_filter']."%'";
		elseif ($_POST['code_filter'] !== '')
			$query_certificate .= " AND code LIKE '%".$_POST['code_filter']."%'";
	}
	$query_certificate .= " ORDER BY id_certificate
	LIMIT $ini,".$GLOBALS['lms']['visuItem'];

	$query_certificate_tot = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_lms']."_certificate";

	$re_certificate = mysql_query($query_certificate);
	list($tot_certificate) = mysql_fetch_row(mysql_query($query_certificate_tot));


	$type_h = array('', '', '', 'image');
	$cont_h	= array(
		$lang->def('_CODE'),
		$lang->def('_NAME'),
		$lang->def('_DESCRIPTION'),
		'<img src="'.getPathImage().'standard/view.gif" alt="'.$lang->def( '_PREVIEW' ).'" />'
	);
	if($mod_perm) {
		$cont_h[] = '<img src="'.getPathImage().'standard/modelem.gif" alt="'.$lang->def( '_TITLE_MOD_ELEM' ).'" title="'.$lang->def( '_TITLE_MOD_ELEM' ).'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MOD_CERT').'" '
						.'alt="'.$lang->def('_TITLE_MOD_CERT').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_DEL').'" '
						.'alt="'.$lang->def('_DEL').'"" />';
		$type_h[] = 'image';

	}

	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($id_certificate, $code, $name, $descr) = mysql_fetch_row($re_certificate)) {

		$title = strip_tags($name);
		$cont = array(
			$code,
			$name,
			$descr,
			'<a href="index.php?modname=certificate&amp;op=preview&amp;id_certificate='.$id_certificate.'">'
						.'<img src="'.getPathImage().'standard/view.gif" alt="'.$lang->def('_PREVIEW').' : '.$title.'" /></a>'
		);
		if($mod_perm) {
			$cont[] = '<a href="index.php?modname=certificate&amp;op=elemcertificate&amp;id_certificate='.$id_certificate.'" '
						.'title="'.$lang->def('_TITLE_MOD_ELEM').' : '.$name.'">'
						.'<img src="'.getPathImage().'standard/modelem.gif" alt="'.$lang->def('_MOD').' : '.$title.'" /></a>';
			$cont[] = '<a href="index.php?modname=certificate&amp;op=modcertificate&amp;id_certificate='.$id_certificate.'" '
						.'title="'.$lang->def('_TITLE_MOD_CERT').' : '.$name.'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$title.'" /></a>';

			$cont[] = '<a href="index.php?modname=certificate&amp;op=delcertificate&amp;id_certificate='.$id_certificate.'" '
						.'title="'.$lang->def('_TITLE_DEL_CERTIFICATE').' : '.$name.'">'
						.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$title.'" /></a>';
		}
		$tb->addBody($cont);
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delcertificate]');
	
	if($mod_perm) {
		$tb->addActionAdd(
			'<a class="new_element_link" href="index.php?modname=certificate&amp;op=addcertificate" title="'.$lang->def('_NEW_CERTIFICATE').'">'
				//.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" />'
				.$lang->def('_NEW_CERTIFICATE').'</a>'
		);
	}

	$out->add(getTitleArea($lang->def('_TITLE_CERTIFICATE'), 'certificate')
			.'<div class="std_block">'	);
	
	$out->add(
		$form->openForm('certificate_filter', 'index.php?modname=certificate&amp;op=certificate')
		.$form->openElementSpace()
		.$form->getTextfield($lang->def('_NAME'), 'name_filter', 'name_filter', '255', (isset($_POST['name_filter']) && $_POST['name_filter']!== '' ? $_POST['name_filter'] : ''))
		.$form->getTextfield($lang->def('_CODE'), 'code_filter', 'code_filter', '255', (isset($_POST['code_filter']) && $_POST['code_filter']!== '' ? $_POST['code_filter'] : ''))
		.$form->closeElementSpace()
		.$form->openButtonSpace()
		.$form->getButton('filter', 'filter', $lang->def('_FILTER'))
		.$form->getButton('toggle_filter', 'toggle_filter', $lang->def('_TOGGLE_FILTER'))
		.$form->closeButtonSpace()
		.$form->closeForm());
	
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" 		: $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
			case "err" 		: $out->add(getErrorUi($lang->def('_ERR_OPERATION')));break;
			case "err_del" : $out->add(getErrorUi($lang->def('_ERR_DELETE_OP')));break;
		}
	}

	$out->add($tb->getTable().$tb->getNavBar($ini, $tot_certificate).'</div>');

}


function list_element_certificate() {
	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$mod_perm		= checkPerm('mod', true);
	$id_certificate = importVar('id_certificate');
	
	// create a language istance for module admin_certificate
	$lang 		=& DoceboLanguage::createInstance('admin_certificate', 'lms');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');
	$form		= new Form();
	
	$page_title = array(
		'index.php?modname=certificate&amp;op=certificate' => $lang->def('_TITLE_CERTIFICATE'),
		$lang->def('_STRUCTURE_CERTIFICATE')
	);
	
	$out->add(getTitleArea($page_title, 'certificate')
			.'<div class="std_block">'
			.getBackUi( 'index.php?modname=certificate&amp;op=certificate', $lang->def('_BACK') )
	);
	
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" 		: $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
			case "err" 		: $out->add(getErrorUi($lang->def('_ERR_OPERATION')));break;
			case "err_del" : $out->add(getErrorUi($lang->def('_ERR_DELETE_OP')));break;
		}
	}
	
	$query_structure = "
	SELECT cert_structure, orientation, bgimage
	FROM ".$GLOBALS['prefix_lms']."_certificate 
	WHERE id_certificate = ".$id_certificate."";
	
	list($structure, $orientation, $bgimage) = mysql_fetch_row(mysql_query($query_structure));
	
	$out->add('<div class="std_block">'	);
	
	$out->add( getInfoUi($lang->def('_CERTIFICATE_WARNING')) );
	
	$out->add($form->openForm('structure_certificate', 'index.php?modname=certificate&amp;op=savecertificate', false, false, 'multipart/form-data'));
	$out->add($form->openElementSpace()
						
		.$form->getTextarea ($lang->def('_STRUCTURE_CERTIFICATE'), 'structure', 'structure', $structure) 
		.'<p><b>'.$lang->def('_ORIENTATION').'</b></p>'
		.$form->getRadio($lang->def('_PORTRAIT'), 'portrait', 'orientation', 'P', ($orientation == 'P'))
		.$form->getRadio($lang->def('_LANDSCAPE'), 'landscape', 'orientation', 'L', ($orientation == 'L'))
		
		.$form->getExtendedFilefield(	$lang->def('_BACK_IMAGE'), 
										'bgimage', 
										'bgimage', 
										$bgimage)
		
		.$form->closeElementSpace()
		.$form->openButtonSpace()
		.$form->getHidden('id_certificate', 'id_certificate', $_GET['id_certificate'])
		.$form->getButton('structure_certificate', 'structure_certificate', ($lang->def('_SAVE') ) )
		.$form->getButton('undo', 'undo', $lang->def('_UNDO'))
		.$form->closeButtonSpace()
		.$form->closeForm());

	
	$tb = new TypeOne(0, $lang->def('_TAG_LIST_CAPTION'), $lang->def('_TAG_LIST_SUMMARY'));
	
	$tb->setColsStyle(array('', ''));
	$tb->addHead(array($lang->def('_TAG_CODE'), $lang->def('_TAG_DESCRIPTION')));
	
	//search query of certificates tag
	$query_format_tag = "
	SELECT file_name, class_name 
	FROM ".$GLOBALS['prefix_lms']."_certificate_tags ";
	$re_certificate_tags = mysql_query($query_format_tag);
	while(list($file_name, $class_name) = mysql_fetch_row($re_certificate_tags)) {
		
		if(file_exists($GLOBALS['where_lms'].'/lib/certificate/'.$file_name)) {
			
			require_once($GLOBALS['where_lms'].'/lib/certificate/'.$file_name);
			$instance = new $class_name(0, 0);
			$this_subs = $instance->getSubstitutionTags();
			foreach($this_subs as $tag => $description) {
			
				$tb->addBody(array($tag, $description));
			} // end foreach
		} // end if
	}	
	$out->add($tb->getTable());
	
	$out->add('</div>');
}

function manageCertificateFile($new_file_id, $old_file, $path, $delete_old, $is_image = false) {
	require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');
	$arr_new_file = ( isset($_FILES[$new_file_id]) && $_FILES[$new_file_id]['tmp_name'] != '' ? $_FILES[$new_file_id] : false );
	$return = array(	'filename' => $old_file,
						'new_size' => 0,
						'old_size' => 0,
						'error' => false,
						'quota_exceeded' => false);
	sl_open_fileoperations();
	if(($delete_old || $arr_new_file !== false) && $old_file != '') {

		// the flag for file delete is checked or a new file was uploaded ---------------------
		sl_unlink($path.$old_file);
	}
	
	if(!empty($arr_new_file)) {
		
		// if present load the new file --------------------------------------------------------
		$filename = $new_file_id.'_'.mt_rand(0, 100).'_'.time().'_'.$arr_new_file['name'];
		
		if(!sl_upload($arr_new_file['tmp_name'], $path.$filename)) {
	
			return false;
		}
		else return $filename;
	}
	sl_close_fileoperations();
	return '';
}

function editcertificate($load = false) {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang 		=& DoceboLanguage::createInstance('admin_certificate', 'lms');
	$form		= new Form();
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$id_certificate = importVar('id_certificate', true, 0);
	$all_languages 	= $GLOBALS['globLangManager']->getAllLanguages();
	$languages = array();
	foreach($all_languages as $k => $v) { $languages[$v[0]] = $v[1]; }
	
	if($load) {
		
		$query_certificate = "
		SELECT code, name, base_language, description
		FROM ".$GLOBALS['prefix_lms']."_certificate
		WHERE id_certificate = '".$id_certificate."'";
		list($code, $name, $base_language, $descr) = mysql_fetch_row(mysql_query($query_certificate));
	} else {

		$code = '';
		$name 	= '';
		$descr 	= '';
		$base_language = getLanguage();
	}

	$page_title = array(
		'index.php?modname=certificate&amp;op=certificate' => $lang->def('_TITLE_CERTIFICATE'),
		( $load ? $lang->def('_MOD_CERTIFICATE') : $lang->def('_NEW_CERTIFICATE') )
	);
	$out->add(getTitleArea($page_title, 'certificate')
			.'<div class="std_block">'
			.getBackUi( 'index.php?modname=certificate&amp;op=certificate', $lang->def('_BACK') )

			.$form->openForm('adviceform', 'index.php?modname=certificate&amp;op=savecertificate')
	);
	if($load) {

		$out->add($form->getHidden('id_certificate', 'id_certificate', $id_certificate)
				.$form->getHidden('load', 'load', 1)	);
	}
	$out->add(
		$form->openElementSpace()
		.$form->getTextfield($lang->def('_CODE'), 'code', 'code', 255, $code)
		.$form->getTextfield($lang->def('_NAME'), 'name', 'name', 255, $name)
		
		.Form::getDropdown( $lang->def('_BASE_LANGUAGE'),
							'base_language',
							'base_language',
							$languages,
							$base_language)
	
		.$form->getTextarea($lang->def('_DESCRIPTION'), 'descr', 'descr', $descr)
		.$form->closeElementSpace()
		.$form->openButtonSpace()
		.$form->getButton('certificate', 'certificate', ( $load ? $lang->def('_SAVE') : $lang->def('_INSERT') ) )
		.$form->getButton('undo', 'undo', $lang->def('_UNDO'))
		.$form->closeButtonSpace()
		.$form->closeForm()
		.'</div>'
	);
}

function savecertificate() {
	checkPerm('mod');

	$id_certificate = importVar('id_certificate', true, 0);
	$load 			= importVar('load', true, 0);
	
	$all_languages 	= $GLOBALS['globLangManager']->getAllLangCode();
	$lang 			=& DoceboLanguage::createInstance('admin_certificate', 'lms');

	if($_POST['name'] == '') $_POST['name'] = $lang->def('_NOTITLE');

	if(isset($_POST['structure_certificate'])){
		
		
		$path 	= '/doceboLms/certificate/';
		$path 	= $path.( substr($path, -1) != '/' && substr($path, -1) != '\\' ? '/' : '');
		
		$bgimage = manageCertificateFile('bgimage',
										$_POST["old_bgimage"],
										$path,
										isset($_POST['file_to_del']['bgimage']) );
		if(!$bgimage) $bgimage = '';
		
		$query_insert = "
		UPDATE ".$GLOBALS['prefix_lms']."_certificate
		SET	cert_structure = '".$_POST['structure']."',
			orientation = '".$_POST['orientation']."'
			". ( $bgimage != '' && !isset($_POST['file_to_del']['bgimage']) ? " , bgimage = '".$bgimage."'" : '' )."  
			". ( $bgimage == '' && isset($_POST['file_to_del']['bgimage']) ? " , bgimage = ''" : '' )."
		WHERE id_certificate = '".$id_certificate."'";
		
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=certificate&op=certificate&result=err');
		jumpTo('index.php?modname=certificate&op=certificate&result=ok');
	}
	if($load == 1) {

		$query_insert = "
		UPDATE ".$GLOBALS['prefix_lms']."_certificate
		SET	code = '".$_POST['code']."', 
			name = '".$_POST['name']."',
			base_language = '".$_POST['base_language']."',
			description = '".$_POST['descr']."'
		WHERE id_certificate = '".$id_certificate."'";
		
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=certificate&op=certificate&result=err');
		jumpTo('index.php?modname=certificate&op=certificate&result=ok');
	} else {

		$query_insert = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_certificate
		( code, name, base_language, description ) VALUES
		( 	'".$_POST['code']."' ,
			'".$_POST['name']."' ,
		 	'".$_POST['base_language']."' ,
			'".$_POST['descr']."'
		)";
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=certificate&op=certificate&result=err');
		
		list($id_certificate) = mysql_fetch_row(mysql_query("SELECT LAST_INSERT_ID()"));
		jumpTo('index.php?modname=certificate&op=elemcertificate&id_certificate='.$id_certificate);
	}
}

function delcertificate() {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$id_certificate 	= get_req('id_certificate', DOTY_INT, 0);
	$lang 		=& DoceboLanguage::createInstance('admin_certificate', 'lms');

	if(get_req('confirm', DOTY_INT, 0) == 1) {

		$query_certificate = "
		DELETE FROM ".$GLOBALS['prefix_lms']."_certificate
		WHERE id_certificate = '".$id_certificate."'";
		if(!mysql_query($query_certificate)) jumpTo('index.php?modname=certificate&op=certificate&result=err_del');
		else jumpTo('index.php?modname=certificate&op=certificate&result=ok');
	} else {

		list($name, $descr) = mysql_fetch_row(mysql_query("
		SELECT name, description
		FROM ".$GLOBALS['prefix_lms']."_certificate
		WHERE id_certificate = '".$id_certificate."'"));

		$form = new Form();
		$page_title = array(
			'index.php?modname=certificate&amp;op=certificate' => $lang->def('_TITLE_CERTIFICATE'),
			$lang->def('_DEL_CERTIFICATE')
		);
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'admin_certificate')
			.'<div class="std_block">'
			.$form->openForm('del_certificate', 'index.php?modname=certificate&amp;op=delcertificate')
			.$form->getHidden('id_certificate', 'id_certificate', $id_certificate)
			.getDeleteUi(	$lang->def('_AREYOUSURE'),
							'<span>'.$lang->def('_NAME').' : </span>'.$name.'<br />'
								.'<span>'.$lang->def('_DESCRIPTION').' : </span>'.$descr,
							false,
							'confirm',
							'undo'	)
			.$form->closeForm()
			.'</div>', 'content');
	}
}

function report_certificate() {
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.certificate.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.course.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	checkPerm('view');
	
	$out =& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$form = new Form();
	$certificate = new Certificate();
	
	$lang =& DoceboLanguage::createInstance('admin_certificate', 'lms');
	
	if (isset($_GET['id_certificate'])) {
		
		$id_certificate = importVar('id_certificate', true, 0);
		$man_course = new Man_Course();
		
		$id_course = array();
		$id_course = $certificate->getCourseForCertificate($id_certificate);
		
		$course_info = array();
		
		$out->add(
			getTitleArea($lang->def('_CERTIFICATE_REPORT'), 'admin_certificate')
			.'<div class="std_block">'
			.getBackUi('index.php?modname=certificate&amp;op=report_certificate', $lang->def('_BACK'))
		);
		
		$tb	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_CHOOSE_COURSE'), $lang->def('_COURSE_LIST'));

		$type_h = array('', 'align_center', '');
		$cont_h	= array(
			$lang->def('_NAME'),
			$lang->def('_NUMBER_OF_CERTIFICATE'),
			$lang->def('_DESCRIPTION')
		);
		
			$tb->setColsStyle($type_h);
			$tb->addHead($cont_h);
		foreach ($id_course as $course_id) {
			$course_info = $man_course->getCourseInfo($course_id);
			$cont = array(
				'<a href="index.php?modname=certificate&amp;op=view_report_certificate&amp;id_certificate='.$id_certificate.'&amp;id_course='.$course_id.'">'
					.$course_info['name'].'</a>',
				$certificate->getNumberOfCertificateForCourse($id_certificate, $course_info['idCourse']),
				$course_info['description']
			);
			$tb->addBody($cont);
		}
		
		$out->add($tb->getTable().'</div>');
	} else {
		
		$out->add(
			getTitleArea($lang->def('_CERTIFICATE_REPORT'), 'admin_certificate')
			.'<div class="std_block">'
		);
		
		if (isset($_POST['toggle_filter']))
		{
			unset($_POST['name_filter']);
			unset($_POST['code_filter']);
		}
		
		$out->add(
			$form->openForm('certificate_filter', 'index.php?modname=certificate&amp;op=report_certificate')
			.$form->openElementSpace()
			.$form->getTextfield($lang->def('_NAME'), 'name_filter', 'name_filter', '255', (isset($_POST['name_filter']) && $_POST['name_filter']!== '' ? $_POST['name_filter'] : ''))
			.$form->getTextfield($lang->def('_CODE'), 'code_filter', 'code_filter', '255', (isset($_POST['code_filter']) && $_POST['code_filter']!== '' ? $_POST['code_filter'] : ''))
			.$form->closeElementSpace()
			.$form->openButtonSpace()
			.$form->getButton('filter', 'filter', $lang->def('_FILTER'))
			.$form->getButton('toggle_filter', 'toggle_filter', $lang->def('_TOGGLE_FILTER'))
			.$form->closeButtonSpace()
			.$form->closeForm());
		
		if (isset($_POST['filter']))
		{
			if ($_POST['name_filter'] !== '' && $_POST['code_filter'] !== '')
				$certificate_info = $certificate->getCertificateList($_POST['name_filter'], $_POST['code_filter']);
			elseif ($_POST['name_filter'] !== '')
				$certificate_info = $certificate->getCertificateList($_POST['name_filter']);
			elseif ($_POST['code_filter'] !== '')
				$certificate_info = $certificate->getCertificateList(false, $_POST['code_filter']);
			else
				$certificate_info = $certificate->getCertificateList();
		}
		else
			$certificate_info = $certificate->getCertificateList();
		
		$tb	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_CHOOSE_CERTIFICATE'), $lang->def('_CERTIFICATE_LIST'));

		$type_h = array('', '');
		$cont_h	= array(
			$lang->def('_CODE'),
			$lang->def('_NAME'),
			$lang->def('_DESCRIPTION')
		);
		$tb->setColsStyle($type_h);
		$tb->addHead($cont_h);
		foreach($certificate_info as $info_certificate) {
			
			$cont = array(
				$info_certificate[CERT_CODE],
				'<a href="index.php?modname=certificate&amp;op=report_certificate&amp;id_certificate='.$info_certificate[CERT_ID].'">'
					.$info_certificate[CERT_NAME].'</a>',
				$info_certificate[CERT_DESCR]
			);
			$tb->addBody($cont);
		}
		$out->add($tb->getTable().'</div>');
	}
}

function view_report_certificate()
{
	checkPerm('view');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.certificate.php');
	
	$out =& $GLOBALS['page'];
	$out->setWorkingZone('content');
	
	$lang =& DoceboLanguage::createInstance('admin_certificate', 'lms');
	
	$deletion = importVar('deletion', true, 0);
	
	if ($deletion)
		switch ($deletion)
		{
			case 1:
				$out->add(getResultUi($lang->def('_DELETION_SUCCESSFUL')));
			break;
			
			case 2:
				$out->add(getErrorUi($lang->def('_DELETION_ERROR')));
			break;
			
			case 3:
				$out->add(getErrorUi($lang->def('_DELETION_FILE_ERROR')));
			break;
		}
	
	$certificate = new Certificate();
	
	$id_certificate = importVar('id_certificate', true, 0);
	$id_course = importVar('id_course', true, 0);
	
	$report_info = array();
	$report_info = $certificate->getInfoForCourseCertificate($id_course, $id_certificate);
	
	$out->add(getTitleArea($lang->def('_CERTIFICATE_REPORT'), 'admin_certificate')
			.'<div class="std_block">'
			.getBackUi('index.php?modname=certificate&amp;op=report_certificate&amp;id_certificate='.$id_certificate.'', $lang->def('_BACK')));
	
	if (count($report_info))
	{
		require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
		
		$tb = new TypeOne(10, $lang->def('_CERTIFICATE_VIEW_CAPTION'), $lang->def('_CERTIFICATE_VIEW_SUMMARY'));
		$tb->initNavBar('ini', 'button');
		$ini = $tb->getSelectedElement();
		
		$cont_h = array($lang->def('_USERCOMPLETENAME'),
						$lang->def('_RELASE_DATE'),
						'<img src="'.getPathImage().'course/pdf.gif" title="'.$lang->def('_TITLE_VIEW_CERT').'" alt="'.$lang->def('_TITLE_VIEW_CERT').'"" />',
						'<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_TITLE_DEL_CERT').'" alt="'.$lang->def('_DEL').'"" />');
		
		$type_h = array('', '', 'image', 'image');
		
		$tb->setColsStyle($type_h);
		$tb->addHead($cont_h);
		
		$acl_man =& $GLOBALS['current_user']->getAclManager();
		
		foreach($report_info as $info_report)
		{
			$cont = array();
			
			$user_info = $acl_man->getUser($info_report[ASSIGN_USER_ID], false);
			
			if ($user_info[ACL_INFO_FIRSTNAME] != '' && $user_info[ACL_INFO_LASTNAME] != '')
				$cont[] =  $user_info[ACL_INFO_FIRSTNAME].' '.$user_info[ACL_INFO_LASTNAME];
			elseif ($user_info[ACL_INFO_FIRSTNAME] != '')
				$cont[] =  $user_info[ACL_INFO_FIRSTNAME];
			elseif ($user_info[ACL_INFO_LASTNAME] != '')
				$cont[] =  $user_info[ACL_INFO_LASTNAME];
			else
				$cont[] = $acl_man->relativeId($user_info[ACL_INFO_USERID]);
			
			$cont[] = $info_report[ASSIGN_OD_DATE];
			
			$cont[] = '<a href="index.php?modname=certificate&amp;op=send_certificate&amp;certificate_id='.$id_certificate.'&amp;course_id='.$id_course.'&amp;user_id='.$info_report[ASSIGN_USER_ID].'">'
			
				.'<img src="'.getPathImage().'course/pdf.gif" title="'.$lang->def('_TITLE_VIEW_CERT').'" alt="'.$lang->def('_TITLE_VIEW_CERT').'"" /></a>';
			
			$cont[] = '<a href="index.php?modname=certificate&amp;op=del_report_certificate&amp;certificate_id='.$id_certificate.'&amp;course_id='.$id_course.'&amp;user_id='.$info_report[ASSIGN_USER_ID].'">'
				.'<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_DEL').'" alt="'.$lang->def('_DEL').'"" /></a>';
			
			$tb->addBody($cont);
		}
		
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=del_report_certificate]');
		
		$out->add($tb->getTable()
					.$tb->getNavBar($ini, count($report_info)));
	}
	else
	{
		$out->add($lang->def('_NO_USER_FOR_CERTIFICATE'));
	}
	
	$out->add(getBackUi('index.php?modname=certificate&amp;op=report_certificate&amp;id_certificate='.$id_certificate.'', $lang->def('_BACK'))
				.'</div>');
}

function del_report_certificate()
{
	checkPerm('view');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_lms'].'/lib/lib.certificate.php');
	
	$certificate = new Certificate();
	$form = new Form();
	
	$lang =& DoceboLanguage::createInstance('admin_certificate', 'lms');
	
	$id_certificate = importVar('certificate_id', true, 0);
	$id_course = importVar('course_id', true, 0);
	$id_user = importVar('user_id', true, 0);
	
	$certificate_info = array();
	$certificate_info = $certificate->getCertificateInfo($id_certificate);
	
	$c_infos = $certificate->getInfoForCourseCertificate($id_course, $id_certificate, $id_user);
	$certificate_info = current($c_infos);
	if (get_req('confirm_del_report_certificate', DOTY_INT, 0) == 1)
	{
		require_once($GLOBALS['where_framework'].'/lib/lib.upload.php');

		$path = '/doceboLms/certificate/';
		if($certificate_info[CERT_NAME] != '')
			$deletion_result = sl_unlink($path.$certificate_info[ASSIGN_CERT_FILE]);
		
		if ($deletion_result)
		{
			$deletion_result = $certificate->delCertificateForUserInCourse($id_certificate, $id_user, $id_course);
			if ($deletion_result)
				jumpTo('index.php?modname=certificate&amp;op=view_report_certificate&id_certificate='.$id_certificate.'&id_course='.$id_course.'&deletion=1');
			else
				jumpTo('index.php?modname=certificate&amp;op=view_report_certificate&id_certificate='.$id_certificate.'&id_course='.$id_course.'&deletion=2');
		}
		else
			jumpTo('index.php?modname=certificate&amp;op=view_report_certificate&id_certificate='.$id_certificate.'&id_course='.$id_course.'&deletion=3');
	}
	elseif (isset($_POST['undo_del_report_certificate']))
		jumpTo('index.php?modname=certificate&amp;op=view_report_certificate&id_certificate='.$id_certificate.'&id_course='.$id_course);
	else
	{
		$GLOBALS['page']->add(
			getTitleArea($lang->def('_VIEW_REPORT_DELETION'), 'admin_certificate')
			.'<div class="std_block">'
			.$form->openForm('del_certificate', 'index.php?modname=certificate&amp;op=del_report_certificate&amp;certificate_id='.$id_certificate.'&amp;course_id='.$id_course.'&amp;user_id='.$id_user)
			.$form->getHidden('id_certificate', 'id_certificate', $id_certificate)
			.getDeleteUi(	$lang->def('_AREYOUSURE'),
							'<span>'.$lang->def('_NAME').' : </span>'.$certificate_info[$id_certificate][CERT_NAME].'<br />'
								.'<span>'.$lang->def('_DESCRIPTION').' : </span>'.$certificate_info[$id_certificate][CERT_DESCR],
							false,
							'confirm_del_report_certificate',
							'undo_del_report_certificate'	)
			.$form->closeForm()
			.'</div>', 'content');
	}
}

function send_certificate() {
	
	require_once($GLOBALS['where_lms'].'/lib/lib.certificate.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.download.php');
	
	$id_certificate = importVar('certificate_id', true, 0);
	$id_course 		= importVar('course_id', true, 0);
	$id_user 		= importVar('user_id', true, 0);
	
	
	$certificate = new Certificate();
	
	$report_info = array();
	$report_info = $certificate->getInfoForCourseCertificate($id_course, $id_certificate, $id_user);
	$info_report = current($report_info);
	
	$file = $info_report[ASSIGN_CERT_FILE];
	
	//recognize mime type
	$expFileName = explode('.', $file);
	$totPart = count($expFileName) - 1;
	
	//send file
	sendFile('/doceboLms/certificate/', $file, $expFileName[$totPart]);
}

function preview() {
	checkPerm('view');
	
	require_once($GLOBALS['where_lms'].'/lib/lib.certificate.php');
	
	$id_certificate = importVar('id_certificate', true, 0);
	
	$cert = new Certificate();
	$cert->send_preview_certificate($id_certificate, array());
}

function certificateDispatch($op) {

	if(isset($_POST['undo'])) $op = 'certificate';
	if(isset($_POST['undo_report'])) $op = 'report_certificate';
	if(isset($_POST['certificate_course_selection'])) $op = 'view_report_certificate';
	if(isset($_POST['certificate_course_selection_back'])) $op = 'report_certificate';
	switch($op) {
		case "certificate" : {
			certificate();
		};break;
		case "addcertificate" : {
			editcertificate();
		};break;
		case "modcertificate" : {
			editcertificate(true);
		};break;
		case "savecertificate" : {
			savecertificate();
		};break;
		case "delcertificate" : {
			delcertificate();
		};break;
		case "elemcertificate" : {
			list_element_certificate();
		};break;
		case "report_certificate" : {
			report_certificate();
		};break;
		case "view_report_certificate" : {
			view_report_certificate();
		};break;
		case "del_report_certificate" : {
			del_report_certificate();
		};break;
		case "send_certificate" : {
			send_certificate();
		};break;
		case "preview" : {
			preview();
		};break;
		
	}
}

?>