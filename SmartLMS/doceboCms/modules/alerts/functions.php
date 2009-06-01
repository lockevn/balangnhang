<?php

/************************************************************************/
/* DOCEBO CORE - Framework												*/
/* ============================================							*/
/*																		*/
/* Copyright (c) 2005													*/
/* http://www.docebo.com												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/************************************************************************/

/**
 * @package  DoceboCore
 * @version  $Id: functions.php 113 2006-03-08 18:08:42Z ema $
 * @category Event
 * @author   Emanuele Sandri <esandri@docebo.com>
 */
 
if(!defined('IN_DOCEBO')) die('You cannot access this file directly');
 
require_once( $GLOBALS['where_framework'].'/lib/lib.eventmanager.php' );


function event_user_view($op) {

	require_once($GLOBALS['where_framework'].'/lib/lib.newtypeone.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.lang.php');
	require_once($GLOBALS['where_framework'].'/lib/lib.form.php');

	$lang =& DoceboLanguage::createInstance('event_manager', 'framework');
	$out  =& $GLOBALS['page'];
	$form = new Form();

	$out->setWorkingZone('content');
	//$out->add(getTitleArea($lang->def('_EVENT_USER'), 'event_user'));
	$out->add('<div class="std_block">');

	if( $op == 'user_save' ) {
		$rs = mysql_query( "SELECT idEventMgr"
							." FROM ".$GLOBALS['prefix_fw']."_event_manager"
							." ORDER BY idEventMgr" );

		$arr_channel = $_POST['channel'];

		while( list($idEventMgr) = mysql_fetch_row( $rs ) ) {
			$rs_test = mysql_query( "SELECT channel"
							."  FROM ".$GLOBALS['prefix_fw']."_event_user "
							." WHERE idEventMgr = '".$idEventMgr."'"
							."   AND idst = '".$GLOBALS['current_user']->getIdSt()."'");
			$channels = isset($arr_channel[$idEventMgr])?(implode(',',$arr_channel[$idEventMgr])):'';
			if( mysql_num_rows( $rs_test ) == 1 )
				$query = "UPDATE ".$GLOBALS['prefix_fw']."_event_user "
						." SET channel='".$channels."'"
						." WHERE idEventMgr = '".(int)$idEventMgr."'"
						."   AND idst = '".$GLOBALS['current_user']->getIdSt()."'";
			else
				$query = "INSERT INTO ".$GLOBALS['prefix_fw']."_event_user "
					 	." (idEventMgr,idst,channel) VALUES"
						." ('".(int)$idEventMgr."','".$GLOBALS['current_user']->getIdSt()."','".$channels."' )";
			$result = mysql_query( $query );
			mysql_free_result($rs_test);
			if( $result === FALSE )
				break;
		}

		if( $result )
			$out->add(getResultUi($lang->def('_OPERATION_SUCCESSFUL')));
		else
			$out->add(getErrorUi($lang->def('_ERROR_IN_SAVE')));
	}	

	$out->add($form->openForm('event_settings', 'index.php?mn=alerts&amp;pi='.getPI().'&amp;op=user_save'));
	//$out->add($form->openElementSpace());

	//$out->add('<script type="text/javascript">window.setTimeout("document.forms[0].submit()",5000);</script>');

	$ord = importVar('ord', false, 'trans');
	$flip = importVar('flip', true, 0);

	$tb_event_classes = new TypeOne($GLOBALS['visuItem'], $lang->def('_EVENT_SETTINGS'), $lang->def('_EVENT_SETTINGS'));

	$content_h 	= array(
		$lang->def('_EVENT_PLATFORM'),
		$lang->def('_NAME'),
		$lang->def('_DESCRIPTION'),
		$lang->def('_EMAIL'),
		$lang->def('_EVENT_CHANNEL_SMS'),
		);
	$type_h 	= array('', '', '', 'image', 'image');

	$tb_event_classes->setColsStyle($type_h);
	$tb_event_classes->addHead($content_h);

	// Cms:
	$tb_event_classes->setTableStyle("alerts_table");

	$rs = mysql_query( "SELECT ec.idClass, class, platform, description, idEventMgr, permission, channel"
						." FROM ".$GLOBALS['prefix_fw']."_event_class as ec"
						." JOIN ".$GLOBALS['prefix_fw']."_event_manager as em"
						." WHERE ec.idClass = em.idClass AND ec.platform LIKE 'cms%'"
						." ORDER BY idEventMgr" );

	while( list($idClass,$class,$platform,$description,$idEventMgr,$permission,$channel) = mysql_fetch_row($rs) ) {

		$perm_not_used = ($permission == 'not_used');
		$perm_mandatory = ($permission == 'mandatory');
		$perm_user_selectable = ($permission == 'user_selectable');
		$arr_channel = explode(',',$channel);
		$channel_email = in_array('email',$arr_channel);
		$channel_sms = in_array('sms',$arr_channel);

		if( $perm_mandatory || $perm_user_selectable ) {
			$cont = array();
			$cont[] = $lang->def('_EVENT_PLATFORM_'.$platform);
			$cont[] = $lang->def('_EVENT_CLASS_'.$class);
			$cont[] = $lang->def($description);

			if( $perm_mandatory ) {
				$cont[] = '<input type="checkbox" name="Mchannel['.$idEventMgr.'][email]" value="email"'
							.($channel_email?' checked="checked"':'')
							.' disabled="disabled"/>';
				$cont[] = '<input type="checkbox" name="Mchannel['.$idEventMgr.'][sms]" value="sms"'
							.($channel_sms?' checked="checked"':'')
							.' disabled="disabled"/>';
			} else {
				$query = "SELECT channel "
						." FROM ".$GLOBALS['prefix_fw']."_event_user"
						." WHERE idEventMgr='".$idEventMgr."'"
						."   AND idst='".$GLOBALS['current_user']->getIdSt()."'";
				$rs_user = mysql_query( $query );
				if( mysql_num_rows($rs_user) == 1 ) {
					list( $user_channel ) = mysql_fetch_row( $rs_user );
					$arr_user_channel = explode( ',', $user_channel );
					$cont[] = '<input type="checkbox" name="channel['.$idEventMgr.'][email]" value="email"'
								.($channel_email?
											(in_array('email',$arr_user_channel)?' checked="checked"':'')
											:' disabled="disabled"')
								.' />';
					$cont[] = '<input type="checkbox" name="channel['.$idEventMgr.'][sms]" value="sms"'
								.($channel_sms?
											(in_array('sms',$arr_user_channel)?' checked="checked"':'')
											:' disabled="disabled"')
								.' />';
				} else {
					$cont[] = '<input type="checkbox" name="channel['.$idEventMgr.'][email]" value="email"'
								.($channel_email?'':' disabled="disabled"')
								.' />';
					$cont[] = '<input type="checkbox" name="channel['.$idEventMgr.'][sms]" value="sms"'
								.($channel_sms?'':' disabled="disabled"')
								.' />';
				}
				mysql_free_result($rs_user);
			}

			$tb_event_classes->addBody($cont);
		}
	}

	$out->add($tb_event_classes->getTable());
	//$out->add($form->closeElementSpace());
	$out->add($form->openButtonSpace());
	$out->add($form->getButton('save', 'save', $lang->def('_SAVE')));
	$out->add($form->closeButtonSpace());
	$out->add($form->closeForm());
	$out->add('</div>');

}



function eventDispatch($op) {

	if (!$GLOBALS["current_user"]->isAnonymous()) {

		switch($op) {
			case "user_display":
			case "user_save":
				event_user_view($op);
			break;
		}

	}
}

?>