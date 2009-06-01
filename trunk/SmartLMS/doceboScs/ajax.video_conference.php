<?php

/*======================================================================*/
/* DOCEBO - The E-Learning Suite										*/
/* ==================================================================== */
/* 																		*/
/* Copyright (c) 2007													*/
/* http://www.docebo.com/												*/
/*																		*/
/* This program is free software. You can redistribute it and/or modify	*/
/* it under the terms of the GNU General Public License as published by	*/
/* the Free Software Foundation; either version 2 of the License.		*/
/*======================================================================*/

define("IN_DOCEBO", true);

ob_start();

session_name("docebo_video_conference");
session_start();

// check for remote file inclusion attempt -------------------------------
$list = array('GLOBALS', '_POST', '_GET', '_COOKIE', '_SESSION'); 
while(list(, $elem) = each($list)) {
		
	if(isset($_REQUEST[$elem])) die('Request overwrite attempt detected');
}

require(dirname(__FILE__).'/config.php');
require($GLOBALS['where_config'].'/config.php');

require_once($GLOBALS['where_scs'].'/lib/lib.php');

adapt_input_data($_GET);
adapt_input_data($_POST);
adapt_input_data($_COOKIE);

// load standard language module and put it global
$glang =& DoceboLanguage::createInstance( 'standard', 'framework');
$glang->setGlobal();


// first db connection
$db 	=& DbConn::getInstance();
$html 	=& DbPurifier::getInstance();

function performRequestedAction($resource, $action_idref, &$action_data) {
	
	$result = false;
	switch($resource) {
		case "user" : {
			
			$user_man 	= new User_VR($_SESSION['id_user']);
			$result 	= $user_man->performAction($action_idref, $action_data);
		};break;
		case "chat" : {
			
			$chat_man 	= new Chat_VR($_SESSION['id_room']);
			$result 	= $chat_man->performAction($action_idref, $action_data);
		};break;
		case "room" : {
			
			$room_man 	= new Room_VR($_SESSION['id_room']);
			$result 	= $room_man->performAction($action_idref, $action_data);
		};break;
	}
	$return_code = array(
		'resource' 		=> $resource,
		'action_idref'	=> $action_idref,
		'result'		=> $result
	);
	return $return_code;
}

// this part identify the type of request
if(isset($_POST['requested_action'])) {
	
	$json = new Services_JSON();
	
	$action_data = get_req('action_data', false);
	
	switch($_POST['requested_action']) {
		case "shot" : {
			
			// instant action, only one resource ask, simple process
			$action_data = $json->decode(rawurldecode($action_data));
			
			$return_code = performRequestedAction($_POST['resource'], $_POST['action_idref'], $action_data);
			echo $json->encode($return_code);
		};break;
		case "ping" : {
			
			// ping action, is a collection of request from 1 to N resources
			$return_code = array();
			$action_data = $json->decode(rawurldecode($action_data));
			while(list(, $r_obj) = each($action_data)) {
				
				$return_code[] = performRequestedAction($r_obj->resource, $r_obj->action_idref, $r_obj->action_data);
			}
			echo $json->encode($return_code);
		};break;
	}
	
}

$db->close();

ob_end_flush();

?>