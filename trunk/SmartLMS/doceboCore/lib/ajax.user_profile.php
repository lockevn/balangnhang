<?php defined("IN_DOCEBO") or die('You can\'t access directly');

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
 * @package admin-core
 * @subpackage user
 * @category ajax server
 * @version $Id:$
 */

// here all the specific code ==========================================================

$op = get_req('op',DOTY_ALPHANUM, '');

switch($op) {
	
	case "get_lang" : {
		
		$module_name 	= get_req('module_name',DOTY_ALPHANUM, '');
		$platform 		= get_req('platform', DOTY_ALPHANUM, '');
		
		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
		$lang =& DoceboLanguage::createInstance( $module_name, $platform);
		
		$value = array(
			'_TITLE_ASK_A_FRIEND' 	=> $lang->def('_TITLE_ASK_A_FRIEND'), 
			'_WRITE_ASK_A_FRIEND' 	=> $lang->def('_WRITE_ASK_A_FRIEND'), 
			'_SEND_MESSAGE' 		=> $lang->def('_SEND'), 
			'_UNDO' 				=> $lang->def('_UNDO'),
			'_ASK_FRIEND_SEND' 		=> $lang->def('_SEND'), 
			'_ASK_FRIEND_FAIL' 		=> $lang->def('failed'), 
			
			'_MESSAGE_SUBJECT' 		=> $lang->def('_MESSAGE_SUBJECT'), 
			'_MESSAGE_TEXT' 		=> $lang->def('_MESSAGE_TEXT'), 
			'_MESSAGE_SEND' 		=> $lang->def('_MESSAGE_SEND'), 
			'_MESSAGE_FAIL' 		=> $lang->def('_MESSAGE_FAIL')
		);
  
		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');
		$json = new Services_JSON();
		$output = $json->encode($value);
  		docebo_cout($output);
	};break;
	case "send_ask_friend" : {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.myfriends.php');
		
		$module_name 	= get_req('module_name',DOTY_ALPHANUM, '');
		$platform 		= get_req('platform', DOTY_ALPHANUM, '');
		
		$id_friend 			= importVar('id_friend');
		$message_request 	= importVar('message_request');
		
		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
		$lang =& DoceboLanguage::createInstance( $module_name, $platform);
		
		$my_fr = new MyFriends(getLogUserId());
		if($my_fr->addFriend($id_friend, MF_WAITING, $message_request)) {
			$value = array('re' => true);
		} else {
			$value = array('re' => false);
		}
		
		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');
		$json = new Services_JSON();
		$output = $json->encode($value);
  		docebo_cout($output);
	};break;
	case "send_message" : {
		
		require_once($GLOBALS['where_framework'].'/lib/lib.message.php');
		
		$module_name 	= importVar('module_name');
		$platform 		= importVar('platform');
		
		$recipient 			= importVar('send_to');
		$message_subject 	= importVar('message_subject');
		$message_text 		= importVar('message_text');
		
		$lang =& DoceboLanguage::createInstance( 'standard', 'framework');
		$lang->setGlobal();
		$lang =& DoceboLanguage::createInstance( $module_name, $platform);
		
		if(MessageModule::quickSendMessage(getLogUserId(), $recipient, $message_subject, $message_text)) {
			$value = array('re' => true);
		} else {
			$value = array('re' => false);
		}
		require_once($GLOBALS['where_framework'].'/lib/lib.json.php');
		$json = new Services_JSON();
		$output = $json->encode($value);
  		docebo_cout($output);
	};break;
}
 
?>