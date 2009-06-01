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

/**
 * @package  DoceboLms
 * @version  $Id: classroom.php,v 1
 * @author	 Claudio Demarinis <claudiodema [at] docebo [dot] com>
 */

if($GLOBALS['current_user']->isAnonymous()) die("You can't access");

function classroom() {
	checkPerm('view');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');


	$mod_perm	= checkPerm('mod', true);
	// create a language istance for module admin_classroom
	$lang 		=& DoceboLanguage::createInstance('admin_classroom', 'lms');
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$tb	= new TypeOne($GLOBALS['lms']['visuItem'], $lang->def('_CLASSROOM_CAPTION'), $lang->def('_CLASSROOM_SUMMARY'));
	$tb->initNavBar('ini', 'link');
	$tb->setLink("index.php?modname=classroom&amp;op=classroom");
	$ini=$tb->getSelectedElement();

	//search query of classrooms
	$query_classroom = "
	SELECT idClassroom, name, description
	FROM ".$GLOBALS['prefix_lms']."_classroom
	ORDER BY name
	LIMIT $ini,".$GLOBALS['lms']['visuItem'];

	$query_classroom_tot = "
	SELECT COUNT(*)
	FROM ".$GLOBALS['prefix_lms']."_classroom";

	$re_classroom = mysql_query($query_classroom);
	list($tot_classroom) = mysql_fetch_row(mysql_query($query_classroom_tot));


	$type_h = array('', 'news_short_td', "image", "image");
	$cont_h	= array(
	$lang->def('_NAME'),
	$lang->def('_DESCRIPTION')
	);
	if($mod_perm) {
		$cont_h[] = '<img src="'.getPathImage().'standard/mod.gif" title="'.$lang->def('_TITLE_MOD_CLASSROOM').'" '
						.'alt="'.$lang->def('_MOD').'" />';
		$type_h[] = 'image';
		$cont_h[] = '<img src="'.getPathImage().'standard/rem.gif" title="'.$lang->def('_TITLE_DEL_CLASSROOM').'" '
						.'alt="'.$lang->def('_DEL').'"" />';
		$type_h[] = 'image';

	}

	$tb->setColsStyle($type_h);
	$tb->addHead($cont_h);
	while(list($idClassroom, $name, $descr) = mysql_fetch_row($re_classroom)) {

		$cont = array(
			$name,
			$descr
		);
		if($mod_perm) {
			$cont[] = '<a href="index.php?modname=classroom&amp;op=modclassroom&amp;idClassroom='.$idClassroom.'" '
						.'title="'.$lang->def('_TITLE_MOD_CLASSROOM').' : '.$name.'">'
						.'<img src="'.getPathImage().'standard/mod.gif" alt="'.$lang->def('_MOD').' : '.$name.'" /></a>';

			$cont[] = '<a href="index.php?modname=classroom&amp;op=delclassroom&amp;idClassroom='.$idClassroom.'" '
						.'title="'.$lang->def('_TITLE_DEL_CLASSROOM').' : '.$name.'">'
						.'<img src="'.getPathImage().'standard/rem.gif" alt="'.$lang->def('_DEL').' : '.$name.'" /></a>';
		}
		$tb->addBody($cont);
	}
	
	require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
	setupHrefDialogBox('a[href*=delclassroom]');
	
	if($mod_perm) {
		$tb->addActionAdd(
			'<a href="index.php?modname=classroom&amp;op=addclassroom" title="'.$lang->def('_TITLE_NEW_CLASSROOM').'">'
				.'<img src="'.getPathImage().'standard/add.gif" alt="'.$lang->def('_ADD').'" />'
				.$lang->def('_NEW_CLASSROOM').'</a>'
		);
	}

	$out->add(getTitleArea($lang->def('_TITLE_CLASSROOM'), 'classroom', $lang->def('_ALT_TITLE_CLASSROOM'))
			.'<div class="std_block">'	);
	if(isset($_GET['result'])) {
		switch($_GET['result']) {
			case "ok" 		: $out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));break;
			case "err" 		: $out->add(getErrorUi($lang->def('_ERR_OPERATION')));break;
			case "err_del" : $out->add(getErrorUi($lang->def('_ERR_DELETE_OP')));break;
		}
	}

	$out->add($tb->getTable().$tb->getNavBar($ini, $tot_classroom).'</div>');

}

function editclassroom($load = false) {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	require_once($GLOBALS["where_lms"]."/lib/lib.classlocation.php");

	$lang 		=& DoceboLanguage::createInstance('admin_classroom', 'lms');
	$form		= new Form();
	$out 		=& $GLOBALS['page'];
	$out->setWorkingZone('content');

	$idClassroom = importVar('idClassroom', true, 0);
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();

	if($load) {

		$query_classroom = "
		SELECT name, description , location_id , room , street, city, state , zip_code,
		phone,fax, capacity, disposition, instrument, available_instrument,note,responsable
		FROM ".$GLOBALS['prefix_lms']."_classroom
		WHERE idClassroom = '".$idClassroom."'";
		list($name, $descr,$location_id,$room,$street,$city,$state,
		$zip_code,$phone,$fax,$capacity,$disposition,$instrument,$available_instrument,$note,$responsable) = mysql_fetch_row(mysql_query($query_classroom));
	} else {

		$name =  $lang->def('_NO_NAME');
		$descr = '';
		$impo = 0;
		$lang_sel = getLanguage();
		
		$location_id=FALSE;
		$room="";
		$street="";
		$city="";
		$state="";
		$zip_code="";
		$phone="";
		$fax="";
		$capacity="";
		$disposition="";
		$instrument="";
		$available_instrument="";
		$note="";
		$responsable="";
	}

	$page_title = array(
		'index.php?modname=classroom&amp;op=classroom' => $lang->def('_TITLE_CLASSROOM'),
		( $load ? $lang->def('_MOD_CLASSROOM') : $lang->def('_NEW_CLASSROOM') )
	);
	$out->add(getTitleArea($page_title, 'classroom', $lang->def('_ALT_TITLE_CLASSROOMS'))
			.'<div class="std_block">'
			.getBackUi( 'index.php?modname=classroom&amp;op=classroom', $lang->def('_BACK') )

			.$form->openForm('adviceform', 'index.php?modname=classroom&amp;op=saveclassroom')
	);
	if($load) {

		$out->add($form->getHidden('idClassroom', 'idClassroom', $idClassroom)
				.$form->getHidden('load', 'load', 1)	);
	}
	
	
	$clm=new ClassLocationManager();
	$location_arr=$clm->getClassLocationArray();
	
	$out->add($form->openElementSpace()

			.$form->getTextfield($lang->def('_NAME'), 'name', 'name', 255, $name)
			.$form->getTextarea($lang->def('_DESCRIPTION'), 'descr', 'descr', $descr)
			.$form->getDropdown($lang->def('_LOCATION'), 'location_id', 'location_id', $location_arr, $location_id)
			.$form->getTextfield($lang->def('_BUILDING_ROOM'), 'room', 'room', 255, $room)
			.$form->getTextfield($lang->def('_CAPACITY'), 'capacity', 'capacity',255, $capacity)
			.$form->getTextfield($lang->def('_RESPONSABLE'), 'responsable', 'responsable', 255, $responsable)
			.$form->getTextfield($lang->def('_STREET'), 'street', 'street', 255, $street)
			.$form->getTextfield($lang->def('_CITY'), 'city', 'city', 255, $city)
			.$form->getTextfield($lang->def('_STATE'), 'state', 'state', 255, $state)
			.$form->getTextfield($lang->def('_ZIP_CODE'), 'zip_code', 'zip_code', 255, $zip_code)
			.$form->getTextfield($lang->def('_PHONE'), 'phone', 'phone', 255, $phone)
			.$form->getTextfield($lang->def('_FAX'), 'fax', 'fax', 255, $fax)
			.$form->getTextarea($lang->def('_DISPOSITION'), 'disposition', 'disposition',  $disposition)
			.$form->getTextarea($lang->def('_INSTRUMENT'), 'instrument', 'instrument', $instrument)
			.$form->getTextarea($lang->def('_AVAILABLE_INSTRUMENT'), 'available_instrument', 'available_instrument', $available_instrument)
			.$form->getTextarea($lang->def('_NOTE'), 'note', 'note', $note)
			.$form->closeElementSpace()
			.$form->openButtonSpace()
			.$form->getButton('classroom', 'classroom', ( $load ? $lang->def('_SAVE') : $lang->def('_INSERT') ) )
			.$form->getButton('undo', 'undo', $lang->def('_UNDO'))
			.$form->closeButtonSpace()
			.$form->closeForm()
			.'</div>');

}

function saveclassroom() {
	checkPerm('mod');

	$idClassroom 	= importVar('idClassroom', true, 0);
	$load 		= importVar('load', true, 0);
	$all_languages = $GLOBALS['globLangManager']->getAllLangCode();
	$lang 		=& DoceboLanguage::createInstance('admin_classroom', 'lms');

	if($_POST['title'] == '') $_POST['title'] = $lang->def('_NOTITLE');
	$lang_sel = $_POST['language'];
	if($load == 1) {

		$query_insert = "
		UPDATE ".$GLOBALS['prefix_lms']."_classroom
		SET	name = '".$_POST['name']."' ,
			description = '".$_POST['descr']."',
			location_id = '".(int)$_POST['location_id']."',
			room = '".$_POST['room']."',
			street = '".$_POST['street']."',
			city = '".$_POST['city']."',
			state = '".$_POST['state']."' ,
			zip_code = '".$_POST['zip_code']."',
			phone = '".$_POST['phone']."',
			fax = '".$_POST['fax']."',
			capacity = '".$_POST['capacity']."',
			disposition = '".$_POST['disposition']."',
			instrument = '".$_POST['instrument']."',
			available_instrument = '".$_POST['available_instrument']."',
			note = '".$_POST['note']."',
			responsable = '".$_POST['responsable']."'
			WHERE idClassroom = '".$idClassroom."'";
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=classroom&op=classroom&result=err');
		jumpTo('index.php?modname=classroom&op=classroom&result=ok');
		echo $query_insert;
	} else {

		$query_insert = "
		INSERT INTO ".$GLOBALS['prefix_lms']."_classroom
		(name, description , location_id , room , street, city, state , zip_code,
		phone,fax, capacity, disposition, instrument, available_instrument,note,responsable) VALUES
		( 	'".$_POST['name']."' ,
			'".$_POST['descr']."',
			'".(int)$_POST['location_id']."',
			'".$_POST['room']."',
			'".$_POST['street']."',
			'".$_POST['city']."',
			'".$_POST['state']."',
			'".$_POST['zip_code']."',
			'".$_POST['phone']."',
			'".$_POST['fax']."',
			'".$_POST['capacity']."',
			'".$_POST['disposition']."',
			'".$_POST['instrument']."',
			'".$_POST['available_instrument']."',
			'".$_POST['note']."',
			'".$_POST['responsable']."'
			)";
		if(!mysql_query($query_insert)) jumpTo('index.php?modname=classroom&op=classroom&result=err');
		jumpTo('index.php?modname=classroom&op=classroom&result=ok');
		echo $query_insert;
	}
}

function delclassroom() {
	checkPerm('mod');

	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$idClassroom 	= importVar('idClassroom', true, 0);
	$lang 		=& DoceboLanguage::createInstance('admin_classroom', 'lms');

	if(get_req('confirm', DOTY_INT, 0) == 1) {

		$query_classroom = "
		DELETE FROM ".$GLOBALS['prefix_lms']."_classroom
		WHERE idClassroom = '".$idClassroom."'";
		if(!mysql_query($query_classroom)) jumpTo('index.php?modname=classroom&op=classroom&result=err_del');
		else jumpTo('index.php?modname=classroom&op=classroom&result=ok');
	} else {

		list($name, $descr) = mysql_fetch_row(mysql_query("
		SELECT name, description
		FROM ".$GLOBALS['prefix_lms']."_classroom
		WHERE idClassroom = '".$idClassroom."'"));

		$form = new Form();
		$page_title = array(
			'index.php?modname=classroom&amp;op=classroom' => $lang->def('_TITLE_CLASSROOM'),
			$lang->def('_DEL_CLASSROOM')
		);
		$GLOBALS['page']->add(
			getTitleArea($page_title, 'admin_classroom')
			.'<div class="std_block">'
			.$form->openForm('del_classroom', 'index.php?modname=classroom&amp;op=delclassroom')
			.$form->getHidden('idClassroom', 'idClassroom', $idClassroom)
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




function classroomDispatch($op) {

	if(isset($_POST['undo'])) $op = 'classroom';
	switch($op) {
		case "classroom" : {
			classroom();
		};break;
		case "addclassroom" : {
			editclassroom();
		};break;
		case "modclassroom" : {
			editclassroom(true);
		};break;
		case "saveclassroom" : {
			saveclassroom();
		};break;
		case "delclassroom" : {
			delclassroom();
		};break;
	}
}

?>
