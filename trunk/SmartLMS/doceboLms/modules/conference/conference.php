<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2006													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package DoceboLms
 * @subpackage conference
 * @category driver with external
 * @author Fabio Pirovano
 * @version $Id:$
 * @since 3.5.0
 * 
 * ( editor = Eclipse 3.2.0[phpeclipse,subclipse,WTP], tabwidth = 4 ) 
 */

if(!defined('IN_DOCEBO')) die('You cannot access this file directly');



require_once($GLOBALS['where_scs'].'/lib/lib.conference.php');

function conference_list(&$url) {
	checkPerm('view');
	//$mod_perm = checkPerm('mod');
	
	$lang =& DoceboLanguage::createInstance('conference', 'lms');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	
	$idCourse=$_SESSION['idCourse'];
	
	$conference = new Conference_Manager();
	$re_room 		= $conference->roomActive($_SESSION['idCourse'], fromDatetimeToTimestamp(date("Y-m-d H:i:s")));
	$room_number 	= $conference->totalRoom($re_room);
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_CONFERENCE'), 'conference')
		.'<div class="std_block">', 'content');
	
	if(checkPerm('mod', true)) {
	
		if ($GLOBALS["scs"]["code_teleskill"] or ($GLOBALS["scs"]["dimdim_server"] and $GLOBALS["scs"]["dimdim_user"] and $GLOBALS["scs"]["dimdim_password"])) {
	
		if ($conference->can_create_user_limit(getLogUserId(),$idCourse,time())) {
			$GLOBALS['page']->add('<p><a href="'.$url->getUrl('op=startnewconf').'">'.$lang->def('_CREATE').'</a></p>'
		, 'content');
		} else {
			$GLOBALS['page']->add('<p>'.$lang->def('_NO_MORE_ROOM').'</p>'
		, 'content');
		}
		
		}
	}
	
	if($room_number == 0) {
		// no rooms
		$GLOBALS['page']->add('<strong id="noroom">'.$lang->def('_NO_ROOM_AVAILABLE').'</strong>', 'content');
	} else {
		
		// list rooms active in this moment
		$tb = new TypeOne(0, $lang->def('_ROOMS_AVAILABLE'), $lang->def('_SUMMARY_ROOM_AVAILABLE'));

		$cont_h = array($lang->def('_ROOM_TITLE'), 
						$lang->def('_START_DATE'),
						$lang->def('_MEETING_HOURS'), 
						$lang->def('_ROOM_LOGIN'));

		$type_h = array('table_main_colum', 'align_center nowrap', 'align_center nowrap', 'align_center');
		
		if(checkPerm('mod', true))
		{
			$cont_h[] = '';
			$type_h[] = 'image';
		}
		
		$tb->setColsStyle($type_h);
		$tb->addHead($cont_h);
		
		$acl_manager =& $GLOBALS['current_user']->getAclManager();
		$display_name = $GLOBALS['current_user']->getUserName();
		$u_info = $acl_manager->getUser(getLogUserId(), false);
		$user_email=$u_info[ACL_INFO_EMAIL];
		
		while($room = $conference->nextRow($re_room)) {

			$room_id = $room["id"];
	
			$cont = array();
			$cont[]=$room["name"]." (".$room["room_type"].")";	
			$start_date=$GLOBALS['regset']->databaseToRegional(date("Y-m-d H:i:s",$room["starttime"]), 'datetime');
			$cont[]=$start_date;
			$cont[]=$room["meetinghours"];		
			
			$now=time();
			/*
			$booking = new RoomBooking();
			
			$user_booked = $booking->userIsBooked(getLogUserId(), $room["id"]);
			$user_valid = $booking->userIsValid(getLogUserId(), $room["id"]);
			$room_full = $booking->roomIsFull($room["id"]);
			*/
			if ($room["endtime"]>=$now and $room["starttime"]<=$now)
				$cont[]=$conference->getUrl($room["id"],$room["room_type"]);			
			else
				$cont[]="";
			
			if(checkPerm('mod', true))
			{
				if (getLogUserId()==$room["idSt"])
					$cont[]='<a href="index.php?modname=conference&amp;op=delconf&id='.$room["id"].'" '
							.'title="'.$lang->def('_DEL').' : '.strip_tags($room["name"]).'"><img src="'.getPathImage().'/standard/rem.gif'.'" /></a>';
				else
					$cont[] = '';
			}
			
			$tb->addBody($cont);
			
			
		} // end while
		
		require_once($GLOBALS['where_framework'].'/lib/lib.dialog.php');
		setupHrefDialogBox('a[href*=delconf]');
		
		$GLOBALS['page']->add($tb->getTable(), 'content');
	}
	
	$GLOBALS['page']->add('</div>'
		, 'content');
}

function conference_startnewconf($url) {
	checkPerm('view');
	$mod_perm = checkPerm('mod');
	
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');
	
	$lang =& DoceboLanguage::createInstance('conference', 'lms');
	
	if(isset($_POST['create_conf'])) {

		$conference = new Conference_Manager();
		
		$start_date = $GLOBALS['regset']->regionalToDatabase($_POST['start_date'], 'date');
		$start_date = substr($start_date, 0, 10);

		$start_time = ( strlen($_POST['start_time']['hour']) == 1 ? '0' : '' ).$_POST['start_time']['hour'].':'
			.( strlen($_POST['start_time']['minute']) == 1 ? '0' : '' ).$_POST['start_time']['minute'].':00';
		
		$start_timestamp = fromDatetimeToTimestamp($start_date.' '.$start_time);
			
		$conference_name=(trim($_POST["conference_name"]))?(trim($_POST["conference_name"])):($lang->def('_ROOM'));
		
		$meetinghours=(int)$_POST["meetinghours"];	
		
		$end_timestamp = $start_timestamp + $meetinghours * 3600;
			
		$maxparticipants=(int)$_POST["maxparticipants"];	
		
		$idCourse=$_SESSION['idCourse'];
		$room_type=$_POST["room_type"];

		if ($conference->can_create_room_limit(getLogUserId(),$idCourse,$room_type,$start_timestamp,$end_timestamp) and $conference->can_create_user_limit(getLogUserId(),$idCourse,$start_timestamp))
		{
			$conference->insert_room($idCourse,getLogUserId(),$conference_name,$room_type,$start_timestamp,$end_timestamp,$meetinghours,$maxparticipants);
			jumpTo('index.php?modname=conference&amp;op=list');
		} else {
			$title_page = array(
			'index.php?modname=conference&amp;op=list' => $lang->def('_CONFERENCE'), 
			$lang->def('_CREATE')
		);
		$GLOBALS['page']->add( 
			getTitleArea($title_page, 'conference', $lang->def('_CONFERENCE'))
			.'<div class="std_block">'
			.'<span><strong>'.$lang->def('_NO_MORE_ROOM').'</strong></span>'
			.'</div>', 'content');
			return false;
		}
		
	}
		
	$start_time['hour'] 	= date('H');
	$start_time['minute'] 	= date('i');
	$start_date = importVar('start_date', false, date("Y-m-d H:i:s"));
	
	$conf_system=array();
	//$conf_system[""]="";
	$default="";
	$default_maxp=30;
	if ($GLOBALS["scs"]["code_teleskill"]) $conf_system["teleskill"]="teleskill";
	if ($GLOBALS["scs"]["dimdim_server"] and $GLOBALS["scs"]["dimdim_user"] and $GLOBALS["scs"]["dimdim_password"]) {
		$conf_system["dimdim"]="dimdim"; 
		$default="dimdim";
		$default_maxp=$GLOBALS["scs"]["dimdim_max_participant"];
	}
	
	
	
	//if ($GLOBALS["scs"]["intelligere_application_code"]) $conf_system[]="intelligere";
	addJs($GLOBALS['where_lms_relative'].'/modules/conference/', 'ajax_conference.js');
		
	
	$GLOBALS['page']->add(
		getTitleArea($lang->def('_CONFERENCE'), 'conference')
		.'<div class="std_block">'
	, 'content');
	
	$GLOBALS['page']->add(
		Form::openForm('create_conference', $url->getUrl('op=startnewconf'))
		.Form::openElementSpace()
		.Form::getTextfield(	$lang->def('_CONFERENCE_NAME'), 
								'conference_name', 
								'conference_name', 
								255, 
								importVar('conference_name') )
		
		
		.Form::getLineBox(
			$lang->def('_CONFERENCE_SYSTEM'),
			Form::getInputDropdown('', 'room_type', 'room_type', $conf_system
				, $default
				, 'onchange="getMaxRoom()"' )
		)
							
		.Form::getDatefield($lang->def('_START_DATE'), 	'start_date', 'start_date',
			$GLOBALS['regset']->databaseToRegional($start_date, 'date') )
			
		.Form::getLineBox(
			$lang->def('_AT_HOUR'),
			Form::getInputDropdown('', 'start_time_hour', 'start_time[hour]', range(0, 23)
				, importVar('start_time_hour', false, date("H"))
				, '' )
			.' : '
			.Form::getInputDropdown('', 'start_time_minute', 'start_time[minute]', range(0, 59)
				, importVar('start_time_hour', false, date("i"))
				, '' )
		)
		
		.Form::getLineBox(
			$lang->def('_MEETING_HOURS'),
			Form::getInputDropdown('', 'meetinghours', 'meetinghours', range(0, 5)
				, importVar('meetinghours', false, 2)
				, '' )
								
		)	
		
		.Form::getTextfield(	$lang->def('_MAX_PARTICIPANTS'),
								'maxparticipants',
								'maxparticipants',
								6,
								importVar('maxparticipants', true, $default_maxp) )
		
		
		.Form::closeElementSpace()
		
		.Form::openButtonSpace()
		.Form::getButton('create_conf', 'create_conf', $lang->def('_CREATE'))
		.Form::getButton('undo', 'undo', $lang->def('_UNDO'))
		.Form::closeButtonSpace()
		.Form::closeForm()
		
		.'</div>'
	, 'content');
}

function conference_delconf() {
	checkPerm('mod');
	
	$id=importVar('id');
	$conference = new Conference_Manager();
	
	$room=$conference->roomInfo($id);
	
	$lang =& DoceboLanguage::createInstance('conference', 'lms');
	if( get_req('confirm', DOTY_INT, 0))
	{
		$conference->deleteRoom($id);
		
		jumpTo('index.php?modname=conference&amp;op=list');
	} else {
		$title_page = array(
			'index.php?modname=conference&amp;op=list' => $lang->def('_CONFERENCE'), 
			$lang->def('_DEL')
		);
		$GLOBALS['page']->add( 
			getTitleArea($title_page, 'conference', $lang->def('_CONFERENCE'))
			.'<div class="std_block">'
			.getDeleteUi(	$lang->def('_AREYOUSURE'),
							'<span>'.$lang->def('_CONFERENCE_NAME').' : </span>'.$room["name"],
							true, 
							'index.php?modname=conference&amp;op=delconf&amp;id='.$id.'&amp;confirm=1',
							'index.php?modname=conference&amp;op=list' )
			.'</div>', 'content');
	
	}
}

// =================================================================== //
// conference dispatch
// =================================================================== //

function dispatchConference($op) {
	
	require_once($GLOBALS['where_framework'].'/lib/lib.urlmanager.php');
	$url =& UrlManager::getInstance();
	$url->setStdQuery('modname=conference&op=list');
	
	if(isset($_POST['undo'])) $op = 'list';
	
	switch($op) {
		case "list" : {
			conference_list($url);
		};break;
		case "startnewconf" : {
			conference_startnewconf($url);
		};break;

		case "delconf" : {
			conference_delconf();
		};break;
		
		default : {
			conference_list($url);
		}
	}
}

?>